/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/



function gks_tinymce_init(gks_selector) {
  
  
  

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
        
    selector: gks_selector,
    init_instance_callback: function(editor) {
      editor.on('Change', function(e) {
        need_save=true;
      });
    },
    readonly : (from_php_perm_ret_edit ? 0 : 1),
  });
  //console.log('gks_tinymce_init',gks_selector);
}
gks_tinymce_init('.gks_tinymce');

if ($('#product_descr_small').length>0) gks_tinymce_init('.variable_product_descr_small');


var need_save=false;
var mychange = 'change keyup paste';
var gks_page_loading=true;
var myvariables=[];
var var_column_order=[];
var pricelists=[];

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
    
  $('#submit_button_ok_custom').click(function(event) {mysubmit(''); return false;});

  

  $('#product_price_yperx_sale_from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));
  $('#product_price_yperx_sale_to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));    
  $('#product_price_sale_from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));
  $('#product_price_sale_to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));  
  $('#product_price_retail_sale_from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));
  $('#product_price_retail_sale_to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));    

  $('.gks_product_price_plist_item').each(function() {
    id_pricelist=parseInt($(this).attr('data-id_pricelist'));
    if (isNaN(id_pricelist)) id_pricelist=0;
    if (id_pricelist>0) {
      pricelist_descr=$(this).find('label[for="product_price_plist_' + id_pricelist + '"]').text();
      pricelist_descr=pricelist_descr.substring(0,pricelist_descr.length-1);
      pricelists.push({id:id_pricelist,descr:pricelist_descr});
    }
  });
  //console.log('pricelists',pricelists);
  
  for(plid=0;plid<pricelists.length;plid++) {
    plist_id='_'+pricelists[plid].id;
    $('#product_price_plist_sale_from'+plist_id).datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
      function(ct,$i){
        need_save=true;
      }
    }));
    $('#product_price_plist_sale_to'+plist_id).datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
      function(ct,$i){
        need_save=true;
      }
    }));     
  }
    
  function mysubmit() {
    
    datasend='';

    
    datasend+='&def_column_show=' + encodeURIComponent($.base64.encode(JSON.stringify(def_column_show)));
    datasend+='&def_column_width=' + encodeURIComponent($.base64.encode(JSON.stringify(def_column_width)));
    
    datasend+='&product_class='  + encodeURIComponent($.base64.encode($("#mypostform #product_class").val().trim()));
    datasend+='&product_code='  + encodeURIComponent($.base64.encode($("#mypostform #product_code").val().trim()));
    datasend+='&product_descr='  + encodeURIComponent($.base64.encode($("#mypostform #product_descr").val().trim()));
    datasend+='&product_def_comments='  + encodeURIComponent($.base64.encode($("#mypostform #product_def_comments").val().trim()));
    if ($('#product_descr_small').length>0) datasend+='&product_descr_small='  + encodeURIComponent($.base64.encode(tinyMCE.get('product_descr_small').getContent()));
    if ($('#product_descr_big').length>0) datasend+='&product_descr_big='  + encodeURIComponent($.base64.encode(tinyMCE.get('product_descr_big').getContent()));
    datasend+='&product_fpa_base_id='  + encodeURIComponent($("#mypostform #product_fpa_base_id").val().trim());
    datasend+='&product_fpa_ejeresi_id='  + encodeURIComponent($("#mypostform #product_fpa_ejeresi_id").val().trim());
    
    datasend+='&product_monada_id='  + encodeURIComponent($("#mypostform #product_monada_id").val().trim());
    datasend+='&product_need_multi_files_min='  + encodeURIComponent($("#mypostform #product_need_multi_files_min").val().trim());
    datasend+='&product_need_multi_files_max='  + encodeURIComponent($("#mypostform #product_need_multi_files_max").val().trim());
    datasend+='&product_object_name='  + encodeURIComponent($.base64.encode($("#mypostform #product_object_name").val().trim()));
    datasend+='&product_varos='  + encodeURIComponent($("#mypostform #product_varos").val().trim());
    datasend+='&product_ogos_x='  + encodeURIComponent($("#mypostform #product_ogos_x").val().trim());
    datasend+='&product_ogos_y='  + encodeURIComponent($("#mypostform #product_ogos_y").val().trim());
    datasend+='&product_ogos_z='  + encodeURIComponent($("#mypostform #product_ogos_z").val().trim());
    //datasend+='&product_sortorder='  + encodeURIComponent($("#mypostform #product_sortorder").val().trim());
    datasend+='&product_min_pixels_x='  + encodeURIComponent($("#mypostform #product_min_pixels_x").val().trim());
    datasend+='&product_min_pixels_y='  + encodeURIComponent($("#mypostform #product_min_pixels_y").val().trim());
    
    datasend+='&product_can_sell=' + (($('#product_can_sell').is(':checked')) ? '1':'0');
    datasend+='&product_can_buy=' + (($('#product_can_buy').is(':checked')) ? '1':'0');
    
    datasend+='&product_is_digital=' + (($('#product_is_digital').is(':checked')) ? '1':'0');
    datasend+='&product_is_simple_download=' + (($('#product_is_simple_download').is(':checked')) ? '1':'0');
    datasend+='&product_base_type=' + $('input[name=product_base_type]:checked').val();
    datasend+='&product_sku='  + encodeURIComponent($.base64.encode($("#mypostform #product_sku").val().trim()));
    datasend+='&product_gtin='  + encodeURIComponent($.base64.encode($("#mypostform #product_gtin").val().trim()));
    datasend+='&product_upc='  + encodeURIComponent($.base64.encode($("#mypostform #product_upc").val().trim()));
    datasend+='&product_ean='  + encodeURIComponent($.base64.encode($("#mypostform #product_ean").val().trim()));
    datasend+='&product_isbn='  + encodeURIComponent($.base64.encode($("#mypostform #product_isbn").val().trim()));
    datasend+='&product_taric='  + encodeURIComponent($.base64.encode($("#mypostform #product_taric").val().trim()));
    
    //GKS_PRODUCT_LOTS_SERIALS
    if (from_php_GKS_PRODUCT_LOTS_SERIALS) datasend+='&product_lot_serial=' + encodeURIComponent($.base64.encode($('input[name=product_lot_serial]:checked').val()));
    
    datasend+='&product_need_apostoli=' + (($('#product_need_apostoli').is(':checked')) ? '1':'0');
    datasend+='&product_need_multi_files=' + (($('#product_need_multi_files').is(':checked')) ? '1':'0');
    datasend+='&product_min_pixels_can_rotate=' + (($('#product_min_pixels_can_rotate').is(':checked')) ? '1':'0');
    datasend+='&product_disable=' + (($('#product_disable').is(':checked')) ? '0':'1');

    datasend+='&form_product_photo='  + encodeURI($("#form_product_photo").val().trim());

    datasend+='&product_price_yperx='  + encodeURIComponent($("#mypostform #product_price_yperx").val().trim());
    datasend+='&product_price_yperx_include_vat=' + (($('#product_price_yperx_include_vat').is(':checked')) ? '1':'0');
    datasend+='&product_price_yperx_sale='  + encodeURIComponent($("#mypostform #product_price_yperx_sale").val().trim());
    datasend+='&product_price_yperx_sale_dates=' + (($('#product_price_yperx_sale_dates').is(':checked')) ? '1':'0');
    datasend+='&product_price_yperx_sale_from='  + encodeURIComponent($("#mypostform #product_price_yperx_sale_from").val().trim());
    datasend+='&product_price_yperx_sale_to='  + encodeURIComponent($("#mypostform #product_price_yperx_sale_to").val().trim());
    datasend+='&product_price_yperx_sheets_formula='  + encodeURIComponent($.base64.encode($("#mypostform #product_price_yperx_sheets_formula").val().trim()));
    datasend+='&product_price_yperx_quantity_formula='  + encodeURIComponent($.base64.encode($("#mypostform #product_price_yperx_quantity_formula").val().trim()));

    datasend+='&product_price='  + encodeURIComponent($("#mypostform #product_price").val().trim());
    datasend+='&product_price_include_vat=' + (($('#product_price_include_vat').is(':checked')) ? '1':'0');
    datasend+='&product_price_sale='  + encodeURIComponent($("#mypostform #product_price_sale").val().trim());
    datasend+='&product_price_sale_dates=' + (($('#product_price_sale_dates').is(':checked')) ? '1':'0');
    datasend+='&product_price_sale_from='  + encodeURIComponent($("#mypostform #product_price_sale_from").val().trim());
    datasend+='&product_price_sale_to='  + encodeURIComponent($("#mypostform #product_price_sale_to").val().trim());
    datasend+='&product_price_sheets_formula='  + encodeURIComponent($.base64.encode($("#mypostform #product_price_sheets_formula").val().trim()));
    datasend+='&product_price_quantity_formula='  + encodeURIComponent($.base64.encode($("#mypostform #product_price_quantity_formula").val().trim()));

    datasend+='&product_price_retail='  + encodeURIComponent($("#mypostform #product_price_retail").val().trim());
    datasend+='&product_price_retail_include_vat=' + (($('#product_price_retail_include_vat').is(':checked')) ? '1':'0');
    datasend+='&product_price_retail_sale='  + encodeURIComponent($("#mypostform #product_price_retail_sale").val().trim());
    datasend+='&product_price_retail_sale_dates=' + (($('#product_price_retail_sale_dates').is(':checked')) ? '1':'0');
    datasend+='&product_price_retail_sale_from='  + encodeURIComponent($("#mypostform #product_price_retail_sale_from").val().trim());
    datasend+='&product_price_retail_sale_to='  + encodeURIComponent($("#mypostform #product_price_retail_sale_to").val().trim());
    datasend+='&product_price_retail_sheets_formula='  + encodeURIComponent($.base64.encode($("#mypostform #product_price_retail_sheets_formula").val().trim()));
    datasend+='&product_price_retail_quantity_formula='  + encodeURIComponent($.base64.encode($("#mypostform #product_price_retail_quantity_formula").val().trim()));

    datasend+='&product_kostos='  + encodeURIComponent($("#mypostform #product_kostos").val().trim());

    product_price_plist=[];
    for(plid=0;plid<pricelists.length;plid++) {
      plist_id='_'+pricelists[plid].id;
      plist_item={};

      plist_item.id_pricelist=pricelists[plid].id;
      plist_item.product_price_plist=$('#product_price_plist'+plist_id).val();
      plist_item.product_price_plist_sale=$('#product_price_plist_sale'+plist_id).val();
      plist_item.product_price_plist_sale_dates=($('#product_price_plist_sale_dates'+plist_id).is(':checked')?1:0);
      plist_item.product_price_plist_sale_from=$('#product_price_plist_sale_from'+plist_id).val();
      plist_item.product_price_plist_sale_to=$('#product_price_plist_sale_to'+plist_id).val();
      plist_item.product_price_plist_sheets_formula=$.base64.encode($('#product_price_plist_sheets_formula'+plist_id).val());
      plist_item.product_price_plist_quantity_formula=$.base64.encode($('#product_price_plist_quantity_formula'+plist_id).val());
      plist_item.product_price_plist_include_vat=($('#product_price_plist_include_vat'+plist_id).is(':checked')?1:0);
     
      product_price_plist.push(plist_item);
    }
    //console.log('product_price_plist',product_price_plist);
    //return;
    datasend+='&product_price_plist=' + encodeURIComponent($.base64.encode(JSON.stringify(product_price_plist)));
    
    datasend+='&use_only_mine_ergasies=' + (($('#use_only_mine_ergasies').is(':checked')) ? '1':'0');

    datasend+='&product_withheldPercentCategory='  + encodeURIComponent($("#mypostform #product_withheldPercentCategory").val().trim());
    datasend+='&product_otherTaxesPercentCategory='  + encodeURIComponent($("#mypostform #product_otherTaxesPercentCategory").val().trim());
    datasend+='&product_stampDutyPercentCategory='  + encodeURIComponent($("#mypostform #product_stampDutyPercentCategory").val().trim());
    datasend+='&product_feesPercentCategory='  + encodeURIComponent($("#mypostform #product_feesPercentCategory").val().trim());


    datasend+='&internal_note='  + encodeURIComponent($.base64.encode($("#mypostform #internal_note").val().trim()));
    datasend+='&min_quantity_alert='  + encodeURIComponent($("#mypostform #min_quantity_alert").val().trim());
    datasend+='&def_supplier='  + encodeURIComponent($("#mypostform #def_supplier").attr('data-id').trim());


    var has_error_this=false;
    var line_cc=0;
    xarakt_esoda=[];
    $('.gks_xarakt_esoda_cat_id').each(function() {
      line_cc++;
      xx=parseInt($(this).attr('data-xx'));
      if (isNaN(xx)) xx=0;          
      if (xx>0) {
        cat_id =  parseInt($(this).val());if (isNaN(cat_id)) cat_id=0;
        ep_id =  parseInt($('.gks_xarakt_esoda_eidos_parastatikou_id[data-xx=' + xx + ']').val());if (isNaN(ep_id)) ep_id=0;
        typos_id =  parseInt($('.gks_xarakt_esoda_typos_id[data-xx=' + xx + ']').val());if (isNaN(typos_id)) typos_id=0;
        if (cat_id==0) {
          myalert('error:' + gks_lang('Ορίστε την κατηγορία στην [n] γραμμών στο Χαρακτηρισμός Εσόδων').replace('[n]',gks_n_h(line_cc)));
          has_error_this=true; return;
        }
        if (typos_id==0) {
          myalert('error:' + gks_lang('Ορίστε τον τύπο στην [n] γραμμών στο Χαρακτηρισμός Εσόδων').replace('[n]',gks_n_h(line_cc)));
          has_error_this=true; return;
        }
        if (cat_id!=0 || typos_id!=0) {
          ammount=parseFloat($('.gks_xarakt_esoda_ammount[data-xx=' + xx + ']').val()); if (isNaN(ammount)) ammount=0;
          if (ammount>0 && ammount<=100) {
            xarakt_item={};
            xarakt_item.xx=xx;
            xarakt_item.ep_id=ep_id;
            xarakt_item.cat_id=cat_id;
            xarakt_item.typos_id=typos_id;
            xarakt_item.ammount=ammount;
            xarakt_esoda.push(xarakt_item);
          } else {
            myalert('error:'+ gks_lang('Το ποσοστό στην [n] γραμμών στο Χαρακτηρισμός Εσόδων πρέπει να είναι μεγαλύτερο από μηδέν και μικρότερο ή ίσο από 100').replace('[n]',gks_n_h(line_cc)));
            has_error_this=true; return;
          }
        }
      }
    });
    if (has_error_this) return;
    
    var has_error_this=false;
    var line_cc=0;
    xarakt_eksoda=[];
    $('.gks_xarakt_eksoda_cat_id').each(function() {
      line_cc++;
      xx=parseInt($(this).attr('data-xx'));
      if (isNaN(xx)) xx=0;          
      if (xx>0) {
        cat_id =  parseInt($(this).val());if (isNaN(cat_id)) cat_id=0;
        ep_id =  parseInt($('.gks_xarakt_eksoda_eidos_parastatikou_id[data-xx=' + xx + ']').val());if (isNaN(ep_id)) ep_id=0;
        typos_id =  parseInt($('.gks_xarakt_eksoda_typos_id[data-xx=' + xx + ']').val());if (isNaN(typos_id)) typos_id=0;
        if (cat_id==0) {
          myalert('error:' + gks_lang('Ορίστε την κατηγορία στην [n] γραμμών στο Χαρακτηρισμός Εξόδων').replace('[n]',gks_n_h(line_cc)));
          has_error_this=true; return;
        }
        if (typos_id==0) {
          myalert('error:' + gks_lang('Ορίστε τον τύπο στην [n] γραμμών στο Χαρακτηρισμός Εξόδων').replace('[n]',gks_n_h(line_cc)));
          has_error_this=true; return;
        }
        if (cat_id!=0 || typos_id!=0) {
          ammount=parseFloat($('.gks_xarakt_eksoda_ammount[data-xx=' + xx + ']').val()); if (isNaN(ammount)) ammount=0;
          if (ammount>0 && ammount<=100) {
            xarakt_item={};
            xarakt_item.xx=xx;
            xarakt_item.ep_id=ep_id;
            xarakt_item.cat_id=cat_id;
            xarakt_item.typos_id=typos_id;
            xarakt_item.ammount=ammount;
            xarakt_eksoda.push(xarakt_item);
          } else {
            myalert('error:' + gks_lang('Το ποσοστό στην [n] γραμμών στο Χαρακτηρισμός Εξόδων πρέπει να είναι μεγαλύτερο από μηδέν και μικρότερο ή ίσο από 100').replace('[n]',gks_n_h(line_cc)));
            has_error_this=true; return;
          }
        }
      }
    });
    if (has_error_this) return;

    datasend+='&xarakt_esoda=' + encodeURIComponent($.base64.encode(JSON.stringify(xarakt_esoda)));
    datasend+='&xarakt_eksoda=' + encodeURIComponent($.base64.encode(JSON.stringify(xarakt_eksoda)));
    
    var idiotites=[];
    $('.gks_idiotita').each(function() {
      id_product_idiotita=parseInt($(this).attr('data-id'));
      if (isNaN(id_product_idiotita)) id_product_idiotita=0;
      if (id_product_idiotita>0) {
        terms=$(this).tagit('assignedTags');
        isv=($('.gks_idiotita_isv[data-id=' + id_product_idiotita + ']').is(':checked') ? '1' : '0')
        idiotites.push({id: id_product_idiotita, terms:terms,isv:isv});
      }
    });
    datasend+='&idiotites=' + encodeURIComponent($.base64.encode(JSON.stringify(idiotites)));
    //console.log(idiotites);return;
    
    var variable_products=[];
    $('.variable_product').each(function() {
      paa=parseInt($(this).attr('data-paa'));
      if (isNaN(paa)) paa=0;
      if (paa>0) {
        pid=parseInt($(this).attr('data-pid'));
        if (isNaN(pid)) pid=0;
        var pidiotites=[];
        $(this).find('.variables_combo').each(function() {
          iid=parseInt($(this).attr('data-iid'));
          if (isNaN(iid)) iid=0;
          if (iid>0) {
            pidiotites.push({iid:iid,val:$(this).val()}); 
          }
        });
        
        
        item={};
        
        item.product_photo=$(this).find('.variable_product_photo').val();
        item.product_code=$(this).find('.variable_product_code').val();
        item.product_descr=$(this).find('.variable_product_descr').val();
        item.product_def_comments=$(this).find('.variable_product_def_comments').val();
        if ($('#product_descr_small').length>0) {
          product_descr_small_id=$(this).find('.variable_product_descr_small').attr('id');
          item.product_descr_small=tinyMCE.get(product_descr_small_id).getContent();
        } 
        item.product_sku=$(this).find('.variable_product_sku').val();
        item.product_gtin=$(this).find('.variable_product_gtin').val();
        item.product_upc=$(this).find('.variable_product_upc').val();
        item.product_ean=$(this).find('.variable_product_ean').val();
        item.product_isbn=$(this).find('.variable_product_isbn').val();
        item.product_taric=$(this).find('.variable_product_taric').val();
        
        item.product_price_yperx=$(this).find('.variable_product_price_yperx').val();
        item.product_price_yperx_include_vat=$(this).find('.variable_product_price_yperx_include_vat').is(':checked') ? '1' : '0';
        item.product_price_yperx_sale=$(this).find('.variable_product_price_yperx_sale').val();
        item.product_price_yperx_sale_dates=$(this).find('.variable_product_price_yperx_sale_dates').is(':checked') ? '1' : '0';
        item.product_price_yperx_sale_from=$(this).find('.variable_product_price_yperx_sale_from').val();
        item.product_price_yperx_sale_to=$(this).find('.variable_product_price_yperx_sale_to').val();
        item.product_price_yperx_sheets_formula=$(this).find('.variable_product_price_yperx_sheets_formula').val();
        item.product_price_yperx_quantity_formula=$(this).find('.variable_product_price_yperx_quantity_formula').val();
        
        item.product_price=$(this).find('.variable_product_price').val();
        item.product_price_include_vat=$(this).find('.variable_product_price_include_vat').is(':checked') ? '1' : '0';
        item.product_price_sale=$(this).find('.variable_product_price_sale').val();
        item.product_price_sale_dates=$(this).find('.variable_product_price_sale_dates').is(':checked') ? '1' : '0';
        item.product_price_sale_from=$(this).find('.variable_product_price_sale_from').val();
        item.product_price_sale_to=$(this).find('.variable_product_price_sale_to').val();
        item.product_price_sheets_formula=$(this).find('.variable_product_price_sheets_formula').val();
        item.product_price_quantity_formula=$(this).find('.variable_product_price_quantity_formula').val();
        
        item.product_price_retail=$(this).find('.variable_product_price_retail').val();
        item.product_price_retail_include_vat=$(this).find('.variable_product_price_retail_include_vat').is(':checked') ? '1' : '0';
        item.product_price_retail_sale=$(this).find('.variable_product_price_retail_sale').val();
        item.product_price_retail_sale_dates=$(this).find('.variable_product_price_retail_sale_dates').is(':checked') ? '1' : '0';
        item.product_price_retail_sale_from=$(this).find('.variable_product_price_retail_sale_from').val();
        item.product_price_retail_sale_to=$(this).find('.variable_product_price_retail_sale_to').val();
        item.product_price_retail_sheets_formula=$(this).find('.variable_product_price_retail_sheets_formula').val();
        item.product_price_retail_quantity_formula=$(this).find('.variable_product_price_retail_quantity_formula').val();
        
        
        item.product_price_plist=[];
        for(plid=0;plid<pricelists.length;plid++) {
          plist_id='_'+pricelists[plid].id+'_'+paa;
          plist_item={};
          //id="variable_product_price_plist_10005_16"
          plist_item.id_pricelist=pricelists[plid].id;
          plist_item.product_price_plist=$('#variable_product_price_plist'+plist_id).val();
          plist_item.product_price_plist_sale=$('#variable_product_price_plist_sale'+plist_id).val();
          plist_item.product_price_plist_sale_dates=($('#variable_product_price_plist_sale_dates'+plist_id).is(':checked')?1:0);
          plist_item.product_price_plist_sale_from=$('#variable_product_price_plist_sale_from'+plist_id).val();
          plist_item.product_price_plist_sale_to=$('#variable_product_price_plist_sale_to'+plist_id).val();
          plist_item.product_price_plist_sheets_formula=$.base64.encode($('#variable_product_price_plist_sheets_formula'+plist_id).val());
          plist_item.product_price_plist_quantity_formula=$.base64.encode($('#variable_product_price_plist_quantity_formula'+plist_id).val());
          plist_item.product_price_plist_include_vat=($('#variable_product_price_plist_include_vat'+plist_id).is(':checked')?1:0);
         
          item.product_price_plist.push(plist_item);
        }        
        //console.log(item.product_price_plist);
        
        item.product_kostos=$(this).find('.variable_product_kostos').val();
        item.min_quantity_alert=$(this).find('.variable_min_quantity_alert').val();
        
        item.product_varos=$(this).find('.variable_product_varos').val();
        item.product_ogos_x=$(this).find('.variable_product_ogos_x').val();
        item.product_ogos_y=$(this).find('.variable_product_ogos_y').val();
        item.product_ogos_z=$(this).find('.variable_product_ogos_z').val();

        item.product_fpa_base_id=$(this).find('.variable_product_fpa_base_id').val();
        
        
        //console.log(item);

        var data_lang=[];
        $(this).find('.gks_lang_data_obj_input_variable, .gks_lang_data_obj_input_textarea_variable').each(function() {
          elem_id=$(this).attr('id');
          elem_id_split=elem_id.split('laang');
          if (elem_id_split.length==2) {
            elem_value=$(this).val();
            lang_item={};lang_item.id=elem_id_split[0];lang_item.value=elem_value;
            data_lang.push(lang_item);
          }
          //datasend_ret+='&' + elem_id + '='  +  encodeURIComponent($.base64.encode(elem_value.trim()));
          
        });
        $(this).find('.gks_lang_data_obj_input_tinymce_variable').each(function() {
          elem_id=$(this).attr('id');
          elem_id_split=elem_id.split('laang');
          if (elem_id_split.length==2) {
            elem_value=tinyMCE.get(elem_id).getContent().trim();
            lang_item={};lang_item.id=elem_id_split[0];lang_item.value=elem_value;
            data_lang.push(lang_item);
          }
          //datasend_ret+='&' + elem_id + '='  +  encodeURIComponent($.base64.encode(elem_value.trim()));
        });
        
        item.data_lang=data_lang;
        
        
        variable_products.push({paa:paa,pid:pid, pidiotites:pidiotites,item:item});
      }
    });
    datasend+='&variable_products=' + encodeURIComponent($.base64.encode(JSON.stringify(variable_products)));
    //console.log(variable_products);return;
    
    datasend+=gks_custom_datasend();
    datasend+=gks_lang_data_obj_input_collect();
    //console.log(datasend);
    //console.log(gks_lang_data_obj_input_collect());
    //return ;
    

    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-products-item-exec.php?id=' + from_php_id,
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
    
  var file_cc=0;
    
  jqXHR = $('#myphoto_upload').fileupload({
      dropZone:$('#f_button_add_files_photo'),
      dataType: 'json',
      limitConcurrentUploads: 1,
      add: function (e, data) {
        
          var uploadErrors = [];
          var re = /(?:\.([^.]+))?$/;
          var ext = re.exec(data.originalFiles[0]['name']);
          ext=ext[0].toLowerCase();
          
          if (from_php_id<=0) {
             uploadErrors.push(gks_lang('Αποθηκεύστε πρώτα το είδος'));
          }
          
          var acceptFileTypes = gks_image_extension; //['.gif','.jpg','.jpeg','.png'];
          if(acceptFileTypes.indexOf(ext)<0) {
              uploadErrors.push(gks_lang('Αρχείο') + ': ' + data.originalFiles[0]['name'] + "\n" + gks_lang('Μη αποδεκτός τύπος αρχείου') + ': ' + ext);
          }
          if(data.originalFiles[0]['size'] > from_php_gks_get_max_upload_file_size) {
              uploadErrors.push(gks_lang('Αρχείο') + ': ' + data.originalFiles[0]['name'] + "\n" + gks_lang('Πολύ μεγάλο μέγεθος αρχείου') + ': ' + data.originalFiles[0]['size']);
          }
          
          if(uploadErrors.length > 0) {
              myalert('error:' + uploadErrors.join("<br>"));
          } else {
        
            file_cc++;
            data.mycc=file_cc;

            data.submit();
            $('#progress-bar_photo').show();
            $('#progress-extended_photo').show();
          }
      },
      done: function (e, data) {
          
          $.each(data.result.files, function (index, file) {
            if (typeof file.error == 'undefined') {
              
              
              myhtmlimg='';
              myhtmlimg+='<div id="item_upload_photo_' + file.insert_id + '" style="float: left;width:100px;height:130px;border: 0px solid #ddd;padding:2px;margin:2px;text-align: center;overflow: hidden;">';
              myhtmlimg+='  <a class="lightgalleryitem_user" href="' + file.url + '" data-download-url="' + file.url + '">';
              myhtmlimg+='    <img style="position: relative; top: 5px; left: 0px;max-width:96px;max-height:96px;" id="myimg" src="' + file.url_thumb + '">';
              myhtmlimg+='  </a>';
              myhtmlimg+='  <br>';
              myhtmlimg+='  <div style="padding-top:4px">';
              myhtmlimg+='      <a href="" class="set_profile_photo"   data-url="' + file.url_thumb + '" title="' + gks_lang('Ορισμός ως προεπιλεγμένη φωτογραφία') + '"><img src="/my/img/icons/photo.png" border="0" width="16"></a>';
              myhtmlimg+='      <a href="" class="delete_upload_photo" data-url="' + file.url_thumb + '" data-id="' + file.insert_id + '" title="' + gks_lang('Διαγραφή') + '"><img src="/my/img/0.png" border="0" width="16"></a>';
              myhtmlimg+='  </div>';
              myhtmlimg+='</div>';


              $('#imagelist_photo').append(myhtmlimg);
              $('#item_upload_photo_' + file.insert_id + ' .delete_upload_photo').click(delete_upload_click_photo);
              $('#item_upload_photo_' + file.insert_id + ' .set_profile_photo').click(set_profile_photo);
              
             
            
              $("#lightgallery_user").data('lightGallery').destroy(true);
              $("#lightgallery_user").lightGallery({
              	selector: '.lightgalleryitem_user',
              	thumbnail:true,
              	hideBarsDelay:1000,
              }); 
              
              if ($('#form_product_photo').val() == '') {
                $('#form_product_photo').val(file.url_thumb);
                $('#form_product_photo_img').attr("src",file.url_thumb);  
                $('#reset_profile_photo').show(); 
                need_save=true;         
              }
            }
          });
      },
      progressall: function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progress-bar_photo .bar_photo').css(
            'width',
            progress + '%'
        );
        $('#progress-extended_photo').html(_renderExtendedProgress(data));
      },
      fail: function (e, data) {
        myalert('error:' + gks_lang('Παρακαλώ ανανεώστε την σελίδα και ξαναδοκιμάστε') +'<br>' + data.jqXHR.responseText);
      },
      progress: function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progressfile_photo' + data.mycc + ' .bar_photo').css(
            'width',
            progress + '%'
        );
      },
      stop: function (e) {
        $('#progress-bar_photo').hide();
        $('#progress-extended_photo').hide();
      },
      
  });
      
	delete_upload_click_photo = function(event){	
    var uid=$(event.target.parentNode).attr('data-id');
    var data_url=$(event.target.parentNode).attr('data-url');
    
    
    $.ajax({
			url: '/my/admin-products-item-photo-delete.php?id=' + uid,
			myuid: uid,
			type: 'POST',
			cache: false,
			dataType: 'json',
			mydata_url:data_url,
			data: '',
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
  					$('#item_upload_photo_' + this.myuid).remove();
  					$('#myfileid_photo_' + this.myuid).remove();
  					
  					if (this.mydata_url == $('#form_product_photo').val()) {
    					need_save=true;
    					if ($(".set_profile_photo").length == 0) {
    					  
                $('#form_product_photo').val('');
                $('#form_product_photo_img').attr("src",'/my/img/product.png');
                $('#reset_profile_photo').hide();
              } else {
                
                $(".set_profile_photo").each(function( index ) {
                  var data_url=$(this).attr('data-url');
                  $('#form_product_photo').val(data_url);
                  $('#form_product_photo_img').attr("src",data_url);
                  $('#reset_profile_photo').show();
                  return;
                });  					
      				}
            }
            
            $("#lightgallery_user").data('lightGallery').destroy(true);
            $("#lightgallery_user").lightGallery({
            	selector: '.lightgalleryitem_user',
            	thumbnail:true,
            	hideBarsDelay:1000,
            }); 
					  
            
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  }

  $('.delete_upload_photo').click(delete_upload_click_photo);

	set_profile_photo = function(event){	
    if (from_php_id<=0) {myalert('error:' + gks_lang('Αποθηκεύστε πρώτα το είδος')); return;}	  
    need_save=true;      
    var data_url=$(event.target.parentNode).attr('data-url');
    $('#form_product_photo').val(data_url);
    $('#form_product_photo_img').attr("src",data_url);
    $('#reset_profile_photo').show();
    return false;
  }

  $('.set_profile_photo').click(set_profile_photo);

  $('#reset_profile_photo').click(function() {
    if (from_php_id<=0) {myalert('error:' + gks_lang('Αποθηκεύστε πρώτα το είδος')); return;}	  
    need_save=true;
    $('#form_product_photo').val('');
    $('#form_product_photo_img').attr("src",'/my/img/product.png');   
    $('#reset_profile_photo').hide(); 
    return false;
  });
  
  
  $("#lightgallery_user").lightGallery({
  	selector: '.lightgalleryitem_user',
  	thumbnail:true,
  	hideBarsDelay:1000,
  });        
  
  $('#ergasia').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        eml: 1,
      };
      $.ajax({
        url: 'admin-autocomplete-ergasies.php',
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
      $("#ergasia_id").val(ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#ergasia").val("");
          $("#ergasia_id").val("");
        }
    }
  });

  $('#add_ergasia').click(function(event) {  
    if (from_php_id<=0) {myalert('error:' + gks_lang('Αποθηκεύστε πρώτα το είδος')); return;}	  

    datasend='';
    datasend+='eidos_id=' + from_php_id;    
    datasend+='&from=product&ergasia_id='  + encodeURI($("#ergasia_id").val().trim());    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-products-item-ergasia.php',
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
            row_html=$.base64.decode(data.row_html);
            //console.log(row_html);
            
            tr_first=$('#ergasies_table tbody tr:first');
            if (tr_first.length>=1) {
              tr_first.before(row_html);
            } else {
              $('#ergasies_table tbody').html(row_html);
            }
            
            $('.ergasies_tr_new .deleterow').click(deleterow_click); 
  
  
            $('.ergasies_tr_new').each(function() {
              $(this).removeClass('ergasies_tr_new').addClass('ergasies_tr_exist');
            });
            var ergasies_aa=0;
            $('#ergasies_table .ergasies_aa').each(function () {
              ergasies_aa++;
              $(this).html(ergasies_aa);  
            });
            
  
            $("body").removeClass("myloading");  
            
            $('#ergasia').val('');
            $('#ergasia_id').val('');		
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  });      
  
  
  $('#cateidos').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-cateidos.php',
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
      $("#cateidos_id").val(ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#cateidos").val("");
          $("#cateidos_id").val("");
        }
    }
  });

  $('#add_cateidos').click(function(event) {  
    if (from_php_id<=0) {myalert('error:' + gks_lang('Αποθηκεύστε πρώτα το είδος')); return;}	  
    datasend='';
    datasend+='product_id=' + from_php_id;    
    datasend+='&from=product&id='  + encodeURI($("#cateidos_id").val().trim());    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: 'admin-product-categories-item-product-add.php',
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
            row_html=$.base64.decode(data.row_html);
            //console.log(row_html);
            
            tr_first=$('#categories_table tbody tr:first');
            if (tr_first.length>=1) {
              tr_first.before(row_html);
            } else {
              $('#categories_table tbody').html(row_html);
            }
            
            $('.categories_tr_new .deleterow').click(deleterow_click); 
  
  
            $('.categories_tr_new').each(function() {
              $(this).removeClass('categories_tr_new').addClass('categories_tr_exist');
            });
            var categories_aa=0;
            $('#categories_table .categories_aa').each(function () {
              categories_aa++;
              $(this).html(categories_aa);  
            });
            
  
            $("body").removeClass("myloading");  
            
            $('#cateidos').val('');
            $('#cateidos_id').val('');					  
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  });   
  
  $('#brand_eidos').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-brands.php',
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
      $("#brand_eidos_id").val(ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#brand_eidos").val("");
          $("#brand_eidos_id").val("");
        }
    }
  });

  $('#add_brand_eidos').click(function(event) {  
    if (from_php_id<=0) {myalert('error:' + gks_lang('Αποθηκεύστε πρώτα το είδος')); return;}	  
    datasend='';
    datasend+='product_id=' + from_php_id;    
    datasend+='&from=product&id='  + encodeURI($("#brand_eidos_id").val().trim());    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: 'admin-product-brands-item-product-add.php',
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
            row_html=$.base64.decode(data.row_html);
            //console.log(row_html);
            
            tr_first=$('#brands_table tbody tr:first');
            if (tr_first.length>=1) {
              tr_first.before(row_html);
            } else {
              $('#brands_table tbody').html(row_html);
            }
            
            $('.brands_tr_new .deleterow').click(deleterow_click); 
  
  
            $('.brands_tr_new').each(function() {
              $(this).removeClass('brands_tr_new').addClass('brands_tr_exist');
            });
            var brands_aa=0;
            $('#brands_table .brands_aa').each(function () {
              brands_aa++;
              $(this).html(brands_aa);  
            });
            
  
            $("body").removeClass("myloading");  
            
            $('#brand_eidos').val('');
            $('#brand_eidos_id').val('');					  
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  });   
  
  $('#product_need_apostoli').change(function() {
    if ($(this).is(':checked')) {
      $('.div_product_need_apostoli').each(function() {$(this).show();});
      if ($('#product_is_digital').is(':checked')) $('#product_is_digital').click(); 
    } else {
      $('.div_product_need_apostoli').each(function() {$(this).hide();});
    }
  });
  $('#product_need_multi_files').change(function() {
    if ($(this).is(':checked')) {
      $('.div_product_need_multi_files').each(function() {$(this).show();});
    } else {
      $('.div_product_need_multi_files').each(function() {$(this).hide();});
    }
  });

  $('input[name=product_base_type]').change(function() {
    val=$('input[name=product_base_type]:checked').val();
    if (isNaN(val)) val=-1;
    if (val==-1) return;
    
    if (val!=0) $('.gks_base_type0').not('.gks_base_type' + val).slideUp();
    if (val!=1) $('.gks_base_type1').not('.gks_base_type' + val).slideUp();
    if (val!=2) $('.gks_base_type2').not('.gks_base_type' + val).slideUp();
      
    $('.gks_base_type1_pt12').css('padding-top',(val==1 ? '12px' : '0px'));
    
    $('.gks_base_type' + val).slideDown();     
  });

  $('#product_is_digital').change(function() {
    if ($('#product_is_digital').is(':checked')) {
      $('#div_product_is_simple_download').slideDown();
      if ($('#product_need_apostoli').is(':checked')) $('#product_need_apostoli').click(); 
    } else { 
      $('#div_product_is_simple_download').slideUp();
    }
  });















  eidi_parastatikon_str=$.base64.decode(eidi_parastatikon_str);
  
  for(i=0;i<katigoria_xarakt_esodon.length;i++) {
    katigoria_xarakt_esodon[i].descr=$.base64.decode(katigoria_xarakt_esodon[i].descr);
  }
  for(i=0;i<typos_xarakt_esodon.length;i++) {
    typos_xarakt_esodon[i].descr=$.base64.decode(typos_xarakt_esodon[i].descr);
  }
  for(i=0;i<katigoria_xarakt_eksodon.length;i++) {
    katigoria_xarakt_eksodon[i].descr=$.base64.decode(katigoria_xarakt_eksodon[i].descr);
  }
  for(i=0;i<typos_xarakt_eksodon.length;i++) {
    typos_xarakt_eksodon[i].descr=$.base64.decode(typos_xarakt_eksodon[i].descr);
  }






  function gks_add_xarakt_esoda_click() {
    need_save=true;
    xx=parseInt($('.div_gks_xarakt_esoda:last').attr('data-xx'));
    if (isNaN(xx)) xx=0;
    xx++;
    
//    var temp=0;
//    $('.gks_xarakt_esoda_ammount').each(function() {
//      val=parseFloat($(this).val());
//      if (isNaN(val)) val=0;
//      temp+=val;
//    });
//    rest_pososto=100-temp; if (rest_pososto<0) rest_pososto=0;
    rest_pososto=100;
    
    div_xarakt=
    '<div class="form-group row div_gks_xarakt_esoda" style="margin: 0px 0px 10px 0px; border-bottom: 1px solid lightblue;padding-bottom: 10px;" data-xx="' + xx + '">' +
      '<div class="col-12 col-md-12 col-lg-6 col-xl-6 gks_items_col" >' +
        '<select class="gks_xarakt_esoda_eidos_parastatikou_id form-control form-control-sm" data-xx="' + xx + '" style="width:100%;">' +
        '</select>' +
      '</div>' +
      '<div class="col-12 col-md-12 col-lg-6 col-xl-6 gks_items_col" >' +
        '<select class="gks_xarakt_esoda_cat_id form-control form-control-sm" data-xx="' + xx + '" style="width:100%;">' +
        '</select>' +
      '</div>' +
      '<div class="col-12 col-md-12 col-lg-6 col-xl-6 gks_items_col" >' +
        '<select class="gks_xarakt_esoda_typos_id form-control form-control-sm" data-xx="' + xx + '" style="width:100%;">' +
        '</select>' +
      '</div>' +
      '<div class="col-6 col-md-6 col-lg-3 col-xl-3 gks_items_col" >' +
        '<input type="number" class="gks_xarakt_esoda_ammount form-control form-control-sm" data-xx="' + xx + '" ' +
        'value="' + rest_pososto + '" placeholder="%"' + 
        'style="text-align:right;" min=0 step="1">' +
      '</div>' +
      '<div class="col-6 col-md-6 col-lg-3 col-xl-3 gks_items_col text-center offset-md-0 offset-lg-0 offset-xl-0">' +
        '<i class="fas fa-clone gks_clone_eidos_xarakt_esoda" data-xx="' + xx + '" style=""></i> ' +
        '<i class="fas fa-trash-alt gks_delete_eidos_xarakt_esoda" data-xx="' + xx + '" style=""></i> ' +
        '<i class="fas fa-plus-circle gks_add_xarakt_esoda"  style=""></i>' +
      '</div>' +
    '</div>';   
    if (xx==1) {//einai to proto
      $('.div_add_xarakt_esoda').after(div_xarakt);
    } else {
      $('.div_gks_xarakt_esoda:last').after(div_xarakt);
    }
    
    $('.div_gks_xarakt_esoda[data-xx=' + xx + '] .gks_xarakt_esoda_eidos_parastatikou_id').each(gks_xarakt_esoda_eidos_parastatikou_id_set_options);
    $('.div_gks_xarakt_esoda[data-xx=' + xx + '] .gks_xarakt_esoda_eidos_parastatikou_id').change(gks_xarakt_esoda_eidos_parastatikou_id_change);

    $('.div_gks_xarakt_esoda[data-xx=' + xx + '] .gks_xarakt_esoda_cat_id').each(gks_xarakt_esoda_cat_id_set_options);
    $('.div_gks_xarakt_esoda[data-xx=' + xx + '] .gks_xarakt_esoda_cat_id').change(gks_xarakt_esoda_cat_id_change);
    $('.div_gks_xarakt_esoda[data-xx=' + xx + '] .gks_xarakt_esoda_typos_id').each(gks_xarakt_esoda_typos_id_set_options);
    
    
    $('.div_gks_xarakt_esoda[data-xx=' + xx + '] .gks_clone_eidos_xarakt_esoda').click(gks_clone_eidos_xarakt_esoda_click);
    $('.div_gks_xarakt_esoda[data-xx=' + xx + '] .gks_delete_eidos_xarakt_esoda').click(gks_delete_eidos_xarakt_esoda_click);
    $('.div_gks_xarakt_esoda[data-xx=' + xx + '] .gks_add_xarakt_esoda').click(gks_add_xarakt_esoda_click);
    $('.div_gks_xarakt_esoda[data-xx=' + xx + '] .gks_xarakt_esoda_ammount').on(mychange, gks_xarakt_esoda_ammount_change);
    
    gks_add_xarakt_esoda_visible();
    span_sum_xarakt_esoda_calc();
    gks_myscroll();
  }
  $('.gks_add_xarakt_esoda').click(gks_add_xarakt_esoda_click);


  function gks_xarakt_esoda_eidos_parastatikou_id_change() {
    need_save=true;
    xx=parseInt($(this).attr('data-xx'));
    if (isNaN(xx)) xx=0;
    elem=$('.gks_xarakt_esoda_cat_id[data-xx=' + xx + ']');
    gks_xarakt_esoda_cat_id_filter(elem);   
  }
  $('.gks_xarakt_esoda_eidos_parastatikou_id').change(gks_xarakt_esoda_eidos_parastatikou_id_change);

  

  function gks_xarakt_esoda_eidos_parastatikou_id_set_options() {
    dbval=parseInt($(this).attr('data-dbval'));
    if (isNaN(dbval)) dbval=0;
    $(this).append('<option value="0"></option>');
    $(this).append(eidi_parastatikon_str);
    $(this).val(dbval); $(this).removeAttr('data-dbval');
  }
  $('.gks_xarakt_esoda_eidos_parastatikou_id').each(gks_xarakt_esoda_eidos_parastatikou_id_set_options);


  function gks_xarakt_esoda_cat_id_set_options() {
    dbval=parseInt($(this).attr('data-dbval'));
    if (isNaN(dbval)) dbval=0;
    $(this).append('<option value="0"></option>');
    for(i=0;i<katigoria_xarakt_esodon.length;i++) {
      $(this).append('<option value="' + katigoria_xarakt_esodon[i].id + '">' + katigoria_xarakt_esodon[i].descr + '</option>');
    }   
    $(this).val(dbval); $(this).removeAttr('data-dbval');
    gks_xarakt_esoda_cat_id_filter($(this));
  }
  $('.gks_xarakt_esoda_cat_id').each(gks_xarakt_esoda_cat_id_set_options);

  function gks_xarakt_esoda_cat_id_filter(elem) {
    xx=parseInt(elem.attr('data-xx'));
    if (isNaN(xx)) xx=0;
    
    var from_php_acc_eidos_parastatikou_id=parseInt($('.gks_xarakt_esoda_eidos_parastatikou_id[data-xx=' + xx + ']').val());
    if (isNaN(from_php_acc_eidos_parastatikou_id)) from_php_acc_eidos_parastatikou_id=0;
     
    elem.find('option').each(function() {
      val = $(this).val();  
      if (isNaN(val)) val=0;
      if (val>0) {
        found=false;
        for (i=0;i<xarakt_sindiasmoi_esodon1.length;i++) {
          if ((from_php_acc_eidos_parastatikou_id==0 || xarakt_sindiasmoi_esodon1[i].p==from_php_acc_eidos_parastatikou_id) && xarakt_sindiasmoi_esodon1[i].c == val) {
            found=true;
            break; 
          }
        }
        if (found) $(this).show(); else  $(this).hide();
      }
    });
    if (gks_page_loading==false && elem.find('option:selected').css('display') == 'none') {
      elem.val(0);
      $('.gks_xarakt_esoda_typos_id[data-xx=' + xx + ']').val(0);
    }

    elem=$('.gks_xarakt_esoda_typos_id[data-xx=' + xx + ']');
    gks_xarakt_esoda_typos_id_filter(elem);     
  }  

  function gks_xarakt_esoda_cat_id_change() {
    need_save=true;
    xx=parseInt($(this).attr('data-xx'));
    if (isNaN(xx)) xx=0;
    elem=$('.gks_xarakt_esoda_typos_id[data-xx=' + xx + ']');
    gks_xarakt_esoda_typos_id_filter(elem);
  }
  $('.gks_xarakt_esoda_cat_id').change(gks_xarakt_esoda_cat_id_change);

  function gks_xarakt_esoda_typos_id_set_options() {
    dbval=parseInt($(this).attr('data-dbval'));
    if (isNaN(dbval)) dbval=0;
    $(this).append('<option value="0"></option>');
    for(i=0;i<typos_xarakt_esodon.length;i++) {
      $(this).append('<option value="' + typos_xarakt_esodon[i].id + '">' + typos_xarakt_esodon[i].descr + '</option>');
    }   
    $(this).val(dbval); $(this).removeAttr('data-dbval');
    gks_xarakt_esoda_typos_id_filter($(this));
  }
  $('.gks_xarakt_esoda_typos_id').each(gks_xarakt_esoda_typos_id_set_options);

  function gks_xarakt_esoda_typos_id_filter(elem) {
    if (elem.length==0) return;

    xx=parseInt(elem.attr('data-xx'));
    if (isNaN(xx)) xx=0;
    var cat_id=parseInt($('.gks_xarakt_esoda_cat_id[data-xx=' + xx + ']').val());
    if (isNaN(cat_id)) cat_id=0;
    
    var from_php_acc_eidos_parastatikou_id=parseInt($('.gks_xarakt_esoda_eidos_parastatikou_id[data-xx=' + xx + ']').val());
    if (isNaN(from_php_acc_eidos_parastatikou_id)) from_php_acc_eidos_parastatikou_id=0;
    
    elem.find('option').each(function() {
      val = $(this).val();  
      if (isNaN(val)) val=0;
      if (val>0) {
        found=false;
        for (i=0;i<xarakt_sindiasmoi_esodon2.length;i++) {
          if ((from_php_acc_eidos_parastatikou_id==0 || xarakt_sindiasmoi_esodon2[i].p==from_php_acc_eidos_parastatikou_id) && xarakt_sindiasmoi_esodon2[i].c == cat_id && xarakt_sindiasmoi_esodon2[i].t == val) {
            found=true;
            break; 
          }
        }
        if (found) $(this).show(); else  $(this).hide();
      }
    });
    if (gks_page_loading==false && elem.find('option:selected').css('display') == 'none') {
      elem.val(0);
    }
  }  

  function gks_clone_eidos_xarakt_esoda_click() {
    need_save=true;
    xx=parseInt($(this).attr('data-xx'));
    if (isNaN(xx)) xx=0; if (xx<=0) return; 
    var temp=0;
    $('.gks_xarakt_esoda_ammount').each(function() {
      xx2=parseInt($(this).attr('data-xx'));
      if (xx2!=xx) {
        val=parseFloat($(this).val());
        if (isNaN(val)) val=0;
        temp+=val;
      }
    });
    val=100;
    if (isNaN(val)) val=0;
    rest=val-temp;
    if (rest<0) rest=0;
    $('.gks_xarakt_esoda_ammount[data-xx=' + xx + ']').val(rest);
    
    span_sum_xarakt_esoda_calc();
    gks_myscroll();    
  }
  $('.gks_clone_eidos_xarakt_esoda').click(gks_clone_eidos_xarakt_esoda_click);


  function gks_delete_eidos_xarakt_esoda_click() {
    need_save=true;
    xx=parseInt($(this).attr('data-xx'));
    if (isNaN(xx)) xx=0;
    if (xx<=0) return;
    $('.div_gks_xarakt_esoda[data-xx=' + xx + ']').remove();
    gks_add_xarakt_esoda_visible();
    span_sum_xarakt_esoda_calc();
  }
  $('.gks_delete_eidos_xarakt_esoda').click(gks_delete_eidos_xarakt_esoda_click);

  function gks_xarakt_esoda_ammount_change(event) {
    event.preventDefault();  
    need_save=true;
    span_sum_xarakt_esoda_calc();

    gks_myscroll();
  }
  $('.gks_xarakt_esoda_ammount').on(mychange, gks_xarakt_esoda_ammount_change);

  function gks_add_xarakt_esoda_visible() {
    $('.div_gks_xarakt_esoda .gks_add_xarakt_esoda').each(function() {
      $(this).hide();
    });
    $('.div_gks_xarakt_esoda .gks_add_xarakt_esoda:last').show();
    if ($('.div_gks_xarakt_esoda').length==0) {
      $('.div_add_xarakt_esoda .gks_add_xarakt_esoda').show();
      $('.div_sum_xarakt_esoda').hide();
    } else {
      $('.div_add_xarakt_esoda .gks_add_xarakt_esoda').hide();
      $('.div_sum_xarakt_esoda').show();
    }
  }
  function span_sum_xarakt_esoda_calc() {
    var temp=0;
    $('.gks_xarakt_esoda_ammount').each(function() {
      val=parseFloat($(this).val());
      if (isNaN(val)) val=0;
      temp+=val;
    });
    $('.span_sum_xarakt_esoda').html(temp.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, from_php_GKS_NUMBER_FORMAT_DECIMAL, from_php_GKS_NUMBER_FORMAT_THOUSAND));
  }



















  function gks_add_xarakt_eksoda_click() {
      
    xx=parseInt($('.div_gks_xarakt_eksoda:last').attr('data-xx'));
    if (isNaN(xx)) xx=0;
    xx++;
    
//    var temp=0;
//    $('.gks_xarakt_eksoda_ammount').each(function() {
//      val=parseFloat($(this).val());
//      if (isNaN(val)) val=0;
//      temp+=val;
//    });
//    rest_pososto=100-temp; if (rest_pososto<0) rest_pososto=0;
    rest_pososto=100;
    
    div_xarakt=
    '<div class="form-group row div_gks_xarakt_eksoda" style="margin: 0px 0px 10px 0px; border-bottom: 1px solid lightblue;padding-bottom: 10px;" data-xx="' + xx + '">' +
      '<div class="col-12 col-md-12 col-lg-6 col-xl-6 gks_items_col" >' +
        '<select class="gks_xarakt_eksoda_eidos_parastatikou_id form-control form-control-sm" data-xx="' + xx + '" style="width:100%;">' +
        '</select>' +
      '</div>' +
      '<div class="col-12 col-md-12 col-lg-6 col-xl-6 gks_items_col" >' +
        '<select class="gks_xarakt_eksoda_cat_id form-control form-control-sm" data-xx="' + xx + '" style="width:100%;">' +
        '</select>' +
      '</div>' +
      '<div class="col-12 col-md-12 col-lg-6 col-xl-6 gks_items_col" >' +
        '<select class="gks_xarakt_eksoda_typos_id form-control form-control-sm" data-xx="' + xx + '" style="width:100%;">' +
        '</select>' +
      '</div>' +
      '<div class="col-6 col-md-6 col-lg-3 col-xl-3 gks_items_col" >' +
        '<input type="number" class="gks_xarakt_eksoda_ammount form-control form-control-sm" data-xx="' + xx + '" ' +
        'value="' + rest_pososto + '" placeholder="%"' + 
        'style="text-align:right;" min=0 step="1">' +
      '</div>' +
      '<div class="col-6 col-md-6 col-lg-3 col-xl-3 gks_items_col text-center offset-md-0 offset-lg-0 offset-xl-0">' +
        '<i class="fas fa-clone gks_clone_eidos_xarakt_eksoda" data-xx="' + xx + '" style=""></i> ' +
        '<i class="fas fa-trash-alt gks_delete_eidos_xarakt_eksoda" data-xx="' + xx + '" style=""></i> ' +
        '<i class="fas fa-plus-circle gks_add_xarakt_eksoda"  style=""></i>' +
      '</div>' +
    '</div>';   
    if (xx==1) {//einai to proto
      $('.div_add_xarakt_eksoda').after(div_xarakt);
    } else {
      $('.div_gks_xarakt_eksoda:last').after(div_xarakt);
    }
    
    $('.div_gks_xarakt_eksoda[data-xx=' + xx + '] .gks_xarakt_eksoda_eidos_parastatikou_id').each(gks_xarakt_eksoda_eidos_parastatikou_id_set_options);
    $('.div_gks_xarakt_eksoda[data-xx=' + xx + '] .gks_xarakt_eksoda_eidos_parastatikou_id').change(gks_xarakt_eksoda_eidos_parastatikou_id_change);
    
    $('.div_gks_xarakt_eksoda[data-xx=' + xx + '] .gks_xarakt_eksoda_cat_id').each(gks_xarakt_eksoda_cat_id_set_options);
    $('.div_gks_xarakt_eksoda[data-xx=' + xx + '] .gks_xarakt_eksoda_cat_id').change(gks_xarakt_eksoda_cat_id_change);
    $('.div_gks_xarakt_eksoda[data-xx=' + xx + '] .gks_xarakt_eksoda_typos_id').each(gks_xarakt_eksoda_typos_id_set_options);
    
    
    $('.div_gks_xarakt_eksoda[data-xx=' + xx + '] .gks_clone_eidos_xarakt_eksoda').click(gks_clone_eidos_xarakt_eksoda_click);
    $('.div_gks_xarakt_eksoda[data-xx=' + xx + '] .gks_delete_eidos_xarakt_eksoda').click(gks_delete_eidos_xarakt_eksoda_click);
    $('.div_gks_xarakt_eksoda[data-xx=' + xx + '] .gks_add_xarakt_eksoda').click(gks_add_xarakt_eksoda_click);
    $('.div_gks_xarakt_eksoda[data-xx=' + xx + '] .gks_xarakt_eksoda_ammount').on(mychange, gks_xarakt_eksoda_ammount_change);
    
    gks_add_xarakt_eksoda_visible();
    span_sum_xarakt_eksoda_calc();
    gks_myscroll();
  }
  $('.gks_add_xarakt_eksoda').click(gks_add_xarakt_eksoda_click);

  function gks_xarakt_eksoda_eidos_parastatikou_id_change() {
    need_save=true;
    xx=parseInt($(this).attr('data-xx'));
    if (isNaN(xx)) xx=0;
    elem=$('.gks_xarakt_eksoda_cat_id[data-xx=' + xx + ']');
    gks_xarakt_eksoda_cat_id_filter(elem);   
  }
  $('.gks_xarakt_eksoda_eidos_parastatikou_id').change(gks_xarakt_eksoda_eidos_parastatikou_id_change);


  function gks_xarakt_eksoda_eidos_parastatikou_id_set_options() {
    dbval=parseInt($(this).attr('data-dbval'));
    if (isNaN(dbval)) dbval=0;
    $(this).append('<option value="0"></option>');
    $(this).append(eidi_parastatikon_str);
    $(this).val(dbval); $(this).removeAttr('data-dbval');
  }
  $('.gks_xarakt_eksoda_eidos_parastatikou_id').each(gks_xarakt_eksoda_eidos_parastatikou_id_set_options);



  function gks_xarakt_eksoda_cat_id_set_options() {
    dbval=parseInt($(this).attr('data-dbval'));
    if (isNaN(dbval)) dbval=0;
    $(this).append('<option value="0"></option>');
    for(i=0;i<katigoria_xarakt_eksodon.length;i++) {
      $(this).append('<option value="' + katigoria_xarakt_eksodon[i].id + '">' + katigoria_xarakt_eksodon[i].descr + '</option>');
    }   
    $(this).val(dbval); $(this).removeAttr('data-dbval');
    gks_xarakt_eksoda_cat_id_filter($(this));
  }
  $('.gks_xarakt_eksoda_cat_id').each(gks_xarakt_eksoda_cat_id_set_options);

  function gks_xarakt_eksoda_cat_id_filter(elem) {
    xx=parseInt(elem.attr('data-xx'));
    if (isNaN(xx)) xx=0;
    
    var from_php_acc_eidos_parastatikou_id=parseInt($('.gks_xarakt_eksoda_eidos_parastatikou_id[data-xx=' + xx + ']').val());
    if (isNaN(from_php_acc_eidos_parastatikou_id)) from_php_acc_eidos_parastatikou_id=0;
    
    elem.find('option').each(function() {
      val = $(this).val();  
      if (isNaN(val)) val=0;
      if (val>0) {
        found=false;
        for (i=0;i<xarakt_sindiasmoi_eksodon1.length;i++) {
          if ((from_php_acc_eidos_parastatikou_id==0 || xarakt_sindiasmoi_eksodon1[i].p==from_php_acc_eidos_parastatikou_id) && xarakt_sindiasmoi_eksodon1[i].c == val) {
            found=true;
            break; 
          }
        }
        if (found) $(this).show(); else  $(this).hide();
      }
    });
    if (gks_page_loading==false && elem.find('option:selected').css('display') == 'none') {
      elem.val(0);
      $('.gks_xarakt_eksoda_typos_id[data-xx=' + xx + ']').val(0);
    }

    elem=$('.gks_xarakt_eksoda_typos_id[data-xx=' + xx + ']');
    gks_xarakt_eksoda_typos_id_filter(elem);    
  }  

  function gks_xarakt_eksoda_cat_id_change() {

    xx=parseInt($(this).attr('data-xx'));
    if (isNaN(xx)) xx=0;
    elem=$('.gks_xarakt_eksoda_typos_id[data-xx=' + xx + ']');
    gks_xarakt_eksoda_typos_id_filter(elem);
  }
  $('.gks_xarakt_eksoda_cat_id').change(gks_xarakt_eksoda_cat_id_change);

  function gks_xarakt_eksoda_typos_id_set_options() {
    dbval=parseInt($(this).attr('data-dbval'));
    if (isNaN(dbval)) dbval=0;
    $(this).append('<option value="0"></option>');
    for(i=0;i<typos_xarakt_eksodon.length;i++) {
      $(this).append('<option value="' + typos_xarakt_eksodon[i].id + '">' + typos_xarakt_eksodon[i].descr + '</option>');
    }   
    $(this).val(dbval); $(this).removeAttr('data-dbval');
    gks_xarakt_eksoda_typos_id_filter($(this));
  }
  $('.gks_xarakt_eksoda_typos_id').each(gks_xarakt_eksoda_typos_id_set_options);

  function gks_xarakt_eksoda_typos_id_filter(elem) {
    if (elem.length==0) return;

    xx=parseInt(elem.attr('data-xx'));
    if (isNaN(xx)) xx=0;
    var cat_id=parseInt($('.gks_xarakt_eksoda_cat_id[data-xx=' + xx + ']').val());
    if (isNaN(cat_id)) cat_id=0;
    
    var from_php_acc_eidos_parastatikou_id=parseInt($('.gks_xarakt_eksoda_eidos_parastatikou_id[data-xx=' + xx + ']').val());
    if (isNaN(from_php_acc_eidos_parastatikou_id)) from_php_acc_eidos_parastatikou_id=0;
    
    elem.find('option').each(function() {
      val = $(this).val();  
      if (isNaN(val)) val=0;
      if (val>0) {
        found=false;
        for (i=0;i<xarakt_sindiasmoi_eksodon2.length;i++) {
          if ((from_php_acc_eidos_parastatikou_id==0 || xarakt_sindiasmoi_eksodon2[i].p==from_php_acc_eidos_parastatikou_id) && xarakt_sindiasmoi_eksodon2[i].c == cat_id && xarakt_sindiasmoi_eksodon2[i].t == val) {
            found=true;
            break; 
          }
        }
        if (found) $(this).show(); else  $(this).hide();
      }
    });
    if (gks_page_loading==false && elem.find('option:selected').css('display') == 'none') {
      elem.val(0);
    }
  }  

  function gks_clone_eidos_xarakt_eksoda_click() {
    xx=parseInt($(this).attr('data-xx'));
    if (isNaN(xx)) xx=0; if (xx<=0) return; 
    var temp=0;
    $('.gks_xarakt_eksoda_ammount').each(function() {
      xx2=parseInt($(this).attr('data-xx'));
      if (xx2!=xx) {
        val=parseFloat($(this).val());
        if (isNaN(val)) val=0;
        temp+=val;
      }
    });
    val=100;
    if (isNaN(val)) val=0;
    rest=val-temp;
    if (rest<0) rest=0;
    $('.gks_xarakt_eksoda_ammount[data-xx=' + xx + ']').val(rest);
    
    span_sum_xarakt_eksoda_calc();
    gks_myscroll();    
  }
  $('.gks_clone_eidos_xarakt_eksoda').click(gks_clone_eidos_xarakt_eksoda_click);


  function gks_delete_eidos_xarakt_eksoda_click() {

    xx=parseInt($(this).attr('data-xx'));
    if (isNaN(xx)) xx=0;
    if (xx<=0) return;
    $('.div_gks_xarakt_eksoda[data-xx=' + xx + ']').remove();
    gks_add_xarakt_eksoda_visible();
    span_sum_xarakt_eksoda_calc();
  }
  $('.gks_delete_eidos_xarakt_eksoda').click(gks_delete_eidos_xarakt_eksoda_click);

  function gks_xarakt_eksoda_ammount_change(event) {
    event.preventDefault();  
  
    span_sum_xarakt_eksoda_calc();

    gks_myscroll();
  }
  $('.gks_xarakt_eksoda_ammount').on(mychange, gks_xarakt_eksoda_ammount_change);

  function gks_add_xarakt_eksoda_visible() {
    $('.div_gks_xarakt_eksoda .gks_add_xarakt_eksoda').each(function() {
      $(this).hide();
    });
    $('.div_gks_xarakt_eksoda .gks_add_xarakt_eksoda:last').show();
    if ($('.div_gks_xarakt_eksoda').length==0) {
      $('.div_add_xarakt_eksoda .gks_add_xarakt_eksoda').show();
      $('.div_sum_xarakt_eksoda').hide();
    } else {
      $('.div_add_xarakt_eksoda .gks_add_xarakt_eksoda').hide();
      $('.div_sum_xarakt_eksoda').show();
    }
  }
  function span_sum_xarakt_eksoda_calc() {
    var temp=0;
    $('.gks_xarakt_eksoda_ammount').each(function() {
      val=parseFloat($(this).val());
      if (isNaN(val)) val=0;
      temp+=val;
    });
    $('.span_sum_xarakt_eksoda').html(temp.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, from_php_GKS_NUMBER_FORMAT_DECIMAL, from_php_GKS_NUMBER_FORMAT_THOUSAND));
  }
  
  $('#product_fpa_base_id').change(function() {
    if ($(this).val() =='1004') {
      $('#div_product_fpa_ejeresi_id').show();  
    } else {
      $('#div_product_fpa_ejeresi_id').hide();
    }
  });

  var switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  switchery1_this.forEach(function(html) {
    var switchery3 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });
    

  $('#product_price_yperx_sale_dates').change(function() {
    if ($(this).is(':checked')) {
      $('#div_product_price_yperx_sale_dates').show();
    } else {
      $('#div_product_price_yperx_sale_dates').hide();
    }  
  });
  $('#product_price_sale_dates').change(function() {
    if ($(this).is(':checked')) {
      $('#div_product_price_sale_dates').show();
    } else {
      $('#div_product_price_sale_dates').hide();
    }  
  });
  $('#product_price_retail_sale_dates').change(function() {
    if ($(this).is(':checked')) {
      $('#div_product_price_retail_sale_dates').show();
    } else {
      $('#div_product_price_retail_sale_dates').hide();
    }  
  });

  for(plid=0;plid<pricelists.length;plid++) {
    plist_id='_'+pricelists[plid].id;
    $('#product_price_plist_sale_dates'+plist_id).change(function() {
      plist_id=$(this).attr('data-plist_id');
      if ($(this).is(':checked')) {
        $('#div_product_price_plist_sale_dates'+plist_id).show();
      } else {
        $('#div_product_price_plist_sale_dates'+plist_id).hide();
      }  
    });
  }

  function eshop_sync_click() {
    if (need_save) {
      myalert('error:' + gks_lang('Αποθηκεύστε πρώτα το είδος'));
      return;
    }
    if ($(this).hasClass('fa-sync-alt')==false) return;
    
    id_woo_product=parseInt($(this).attr('data-id_woo_product'));
    if (isNaN(id_woo_product)) id_woo_product=0;
    if (id_woo_product<=0) return;
    
    $(this).html('<img src="img/wait.gif" style="height: 22px;">').removeClass('fa-sync-alt');
    //console.log(eshop_id);
    
    datasend='product_id=' + from_php_id + '&id_woo_product=' + id_woo_product;
    $.ajax({
			url: '/my/admin-products-item-eshop-sync.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_id_woo_product:id_woo_product,
			error : function(jqXHR ,textStatus,  errorThrown) {
				myalert('error:' + jqXHR.responseText);
				$('.eshop_sync[data-id_woo_product=' + this.gks_id_woo_product +']').html('').addClass('fa-sync-alt');
			},				
			success: function(data) {
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
					$('.eshop_sync[data-id_woo_product=' + this.gks_id_woo_product +']').html('').addClass('fa-sync-alt');
				} else {
				  
					if (data.success == true) {
  					if (data.save_but_message!='') {
  					  if ($.base64.decode(data.message)=='ok') {
  					    myalert('ok:' + $.base64.decode(data.save_but_message), '',true);
  					  } else {
  					    myalert('error:' + $.base64.decode(data.save_but_message), '',true);
  					  }
  					} else {
  					  $('body').addClass("myloading");
    					window.location.reload();
    				}
					} else {
						myalert('error:' + $.base64.decode(data.message));
						$('.eshop_sync[data-id_woo_product=' + this.gks_id_woo_product +']').html('').addClass('fa-sync-alt');
					}
				}
			}
			
		});    
  }
  $('.eshop_sync').click(eshop_sync_click);


  var dialog_eshoplink;
  dialog_eshoplink = $('#dialog_eshoplink').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_eshoplink_link",
        text: gks_lang('Σύνδεση','part2'),
        icon: "ui-icon-print",  
        click: function() {
          eshop_id=parseInt($('#dialog_eshoplink_eshop').val());
          if (isNaN(eshop_id)) eshop_id=0;
          if (eshop_id<=0) {
            myalert('error:' + gks_lang('Επιλέξτε ένα eshop'));
            return;
          }
          remote_product_id=parseInt($('#dialog_eshoplink_list').val());
          if (isNaN(remote_product_id)) remote_product_id=0;
          if (remote_product_id<=0) {
            myalert('error:' + gks_lang('Επιλέξτε ένα προϊόν'));
            return;
          }
          remote_lang=$('#dialog_eshoplink_list option[value=' + remote_product_id + ']').attr('data-lang');
          datasend='product_id=' + from_php_id + 
          '&eshop_id=' + eshop_id + 
          '&remote_product_id=' + remote_product_id + 
          
          '&remote_lang=' +  encodeURIComponent($.base64.encode(remote_lang));
          //console.log(datasend);
          
          $('body').addClass("myloading");
          $.ajax({
      			url: '/my/admin-products-item-eshop-link-remote-product.php',
      			type: 'POST',
      			cache: false,
      			dataType: 'json',
      			data: datasend,
      			error : function(jqXHR ,textStatus,  errorThrown) {
      				$('body').removeClass("myloading");
      				myalert('error:' + jqXHR.responseText);
      			},				
      			success: function(data) {
      			  $('body').removeClass("myloading");
      				if (!data) {
      					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
      				} else {
      					if (data.success == true) {

                  
                  row_html=$.base64.decode(data.row_html);
                  //console.log(row_html);
                  
                  tr_first=$('#eshoplink_table tbody tr:first');
                  if (tr_first.length>=1) {
                    tr_first.before(row_html);
                  } else {
                    $('#eshoplink_table tbody').html(row_html);
                  }
                  
                  $('.eshoplink_tr_new .deleterow').click(deleterow_click); 
                  $('.eshoplink_tr_new .eshop_sync').click(eshop_sync_click); 
                  $('.eshoplink_tr_new .tooltipster').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true});

                  $('.eshoplink_tr_new').each(function() {
                    $(this).removeClass('eshoplink_tr_new').addClass('eshoplink_tr_exist');
                  });
                  var eshoplink_aa=0;
                  $('#eshoplink_table .eshoplink_aa').each(function () {
                    eshoplink_aa++;
                    $(this).html(eshoplink_aa);  
                  });
                  
                  dialog_eshoplink.dialog( "close" );
                  $("body").removeClass("myloading");                  
                  
                  			  
      					} else {
      						myalert('error:' + $.base64.decode(data.message));
      					}
      				}
      			}
      			
      		});            
          
        }
      },
      {
        id: "dialog_eshoplink_cancel",
        text: gks_lang('Άκυρο'),
        icon: "ui-icon-cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
      },
    ],
  });
  
  $('#eshoplink_add').click(function(event)   {
    event.stopPropagation();
    //$('#dialog_eshoplink_eshop').val('0');
    //$('#dialog_eshoplink_search').val('');
    //$('#dialog_eshoplink_list').val('0');
    
    
    
    dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 600) dwidth=600;
	  if (dheight> 520) dheight=520;
	  dialog_eshoplink.dialog('option', 'width', dwidth);
	  dialog_eshoplink.dialog('option', 'height', dheight);
	  $('#dialog_eshoplink').parent().css({position:'fixed'});      
    dialog_eshoplink.dialog('open');
    
  });

  $('#dialog_eshoplink_eshop').change(function() {
    eshop_id=parseInt($(this).val());
    if (isNaN(eshop_id)) eshop_id=0;

    $('#dialog_eshoplink_list option').each(function() { 
      if ($(this).attr('value') >0 ) $(this).remove();
    });
    if (eshop_id<=0) return;
    
    $('body').addClass("myloading");
    datasend='eshop_id=' + eshop_id;
    $.ajax({
			url: '/my/admin-products-item-eshop-get-products.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_eshop_id:eshop_id,
			error : function(jqXHR ,textStatus,  errorThrown) {
				$('body').removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
			  $('body').removeClass("myloading");
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
            
            elem=$('#dialog_eshoplink_list');
				    for (i = 0; i < data.plist.length; i++) {
				      elem.append('<option value="' + data.plist[i].i + '" data-lang="' + data.plist[i].l + '">' + data.plist[i].d + ' (' + data.plist[i].i + (data.plist[i].l=='' ? '' : (','+data.plist[i].l)) + ')</option>');
				    }	
				    $('#dialog_eshoplink_search').val('');
				    $('#dialog_eshoplink_search').focus();				  
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});    
    
  });


  $('#dialog_eshoplink_search').on(mychange, function() {
    sval=$(this).val().trim();
    //console.log(sval);
    
    $('#dialog_eshoplink_list option').each(function() { 
      if (sval=='' || $(this).text().search(sval)>=0) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });    
  });

  window.gks_fnc_eshoplink_delete_after = function (myargs) {
    $("body").removeClass("myloading");
    $('.eshoplink_tr_exist[data-id=' + myargs[0] + ']').hide('fade', {}, 500,function() { 
      $(this).remove(); 
      var eshoplink_aa=0;
      $('#eshoplink_table .eshoplink_aa').each(function () {
        eshoplink_aa++;
        $(this).html(eshoplink_aa);  
      });    
    });
  }


  window.gks_fnc_ergasies_delete_after = function (myargs) {
    $("body").removeClass("myloading");
    $('.ergasies_tr_exist[data-id=' + myargs[0] + ']').hide('fade', {}, 500,function() { 
      $(this).remove(); 
      var ergasies_aa=0;
      $('#ergasies_table .ergasies_aa').each(function () {
        ergasies_aa++;
        $(this).html(ergasies_aa);  
      });    
    });
  }
  window.gks_fnc_categories_delete_after = function (myargs) {
    $("body").removeClass("myloading");
    $('.categories_tr_exist[data-id=' + myargs[0] + ']').hide('fade', {}, 500,function() { 
      $(this).remove(); 
      var categories_aa=0;
      $('#categories_table .categories_aa').each(function () {
        categories_aa++;
        $(this).html(categories_aa);  
      });    
    });
  }
  window.gks_fnc_brands_delete_after = function (myargs) {
    $("body").removeClass("myloading");
    $('.brands_tr_exist[data-id=' + myargs[0] + ']').hide('fade', {}, 500,function() { 
      $(this).remove(); 
      var brands_aa=0;
      $('#brands_table .brands_aa').each(function () {
        brands_aa++;
        $(this).html(brands_aa);  
      });    
    });
  }
  
  
  $('.gks_idiotita').each(function() {
    id_product_idiotita=parseInt($(this).attr('data-id'));
    if (isNaN(id_product_idiotita)) id_product_idiotita=0;
    if (id_product_idiotita>0) {
      
      if (typeof gks_product_idiotites[id_product_idiotita] ==='undefined') {
        $(this).tagit({
          singleFieldDelimiter: ']][[',
          allowSpaces: true, 
          availableTags: [],
          showAutocompleteOnFocus : true,
          afterTagAdded:function() {need_save=true;},
          afterTagRemoved:function() {need_save=true;},
        });        
      } else {
        $(this).tagit({
          singleFieldDelimiter: ']][[',
          allowSpaces: true, 
          availableTags: gks_product_idiotites[id_product_idiotita].terms,
          showAutocompleteOnFocus : true,
          afterTagAdded:function() {need_save=true;gks_get_variables_from_idiotites();},
          afterTagRemoved:function() {need_save=true;gks_get_variables_from_idiotites();},
        });        
      }
    }
  });
  
  function idiotites_add_exec(id_product_idiotita) {
    //console.log(id_product_idiotita);
    
    html='<div class="form-group row div_gks_idiotita" data-id="' + id_product_idiotita + '">' +
              '<label for="" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_product_idiotites[id_product_idiotita].name + '</label>' +
              '<div class="col-md-7">' +
                '<input class="gks_idiotita form-control form-control-sm myneedsave" data-id="' + id_product_idiotita + '" type="text" value="">' +
                '<small class="small_gks_idiotita_isv" style="' + 
                (from_php_product_class=='variable' ? '' : 'display:none;') +
                '"><input class="gks_idiotita_isv" data-id="' + id_product_idiotita + '" type="checkbox" value="1">' +
                  ' ' + gks_lang('Χρησιμοποιείται στις παραλλαγές') + '</small>' +
              '</div>' +
              '<div class="col-md-1">' +
              '<i class="fas fa-trash-alt gks_idiotita_delete" data-id="' + id_product_idiotita + '" style=""></i>' +
              '</div>' +
            '</div>';
    
    $('#div_idiotites').append(html);
    $('.gks_idiotita_delete[data-id=' + id_product_idiotita + ']').click(gks_idiotita_delete_click);
    $('.gks_idiotita_isv[data-id=' + id_product_idiotita + ']').change(gks_idiotita_isv_change);
    $('.gks_idiotita[data-id=' + id_product_idiotita + ']').tagit({
          singleFieldDelimiter: ']][[',
          allowSpaces: true, 
          availableTags: gks_product_idiotites[id_product_idiotita].terms,
          showAutocompleteOnFocus : true,
          afterTagAdded:function() {need_save=true;gks_get_variables_from_idiotites();},
          afterTagRemoved:function() {need_save=true;gks_get_variables_from_idiotites();},
        });
    $('.div_gks_idiotita[data-id=' + id_product_idiotita + '] ul.tagit input').focus();
    gks_get_variables_from_idiotites();
  }
  

  var idiotites_add_contextMenu={
		//event: 'click',
    items: function(e) {
      var arr = [];
      gks_product_idiotites.forEach(function(item, i) {
        arr.push({type: 'item', text: gks_product_idiotites[i].name, icon1: '', 
          disabled: $('.gks_idiotita[data-id=' + gks_product_idiotites[i].id + ']').length>0,
          click: function(e){	
    		  e.preventDefault(); idiotites_add_exec(gks_product_idiotites[i].id);}});        
      });
      return arr;
    }
	};

  $('#idiotites_add').contextMenu(idiotites_add_contextMenu);
	
	$('#idiotites_add').click(function(e) {
	  event.stopPropagation();
	  $('#idiotites_add').contextMenu('show',e);  
  });
	
	

  function gks_idiotita_delete_click() {
    id_product_idiotita=parseInt($(this).attr('data-id'));
    if (isNaN(id_product_idiotita)) id_product_idiotita=0;
    if (id_product_idiotita<=0) return;
    $('.div_gks_idiotita[data-id=' + id_product_idiotita + ']').remove();
    gks_get_variables_from_idiotites();
  }
  $('.gks_idiotita_delete').click(gks_idiotita_delete_click);
  
  
  function gks_idiotita_isv_change() {
    gks_get_variables_from_idiotites();
  }
  $('.gks_idiotita_isv').change(gks_idiotita_isv_change);
  
  
  //var myvariables=[];
  function gks_get_variables_from_idiotites() {
    if (gks_page_loading) return;
    if (from_php_product_class!='variable') return;
    //console.log('gks_get_variables_from_idiotites');
    
    myvariables=[];
    $('.gks_idiotita').each(function() {
      id_product_idiotita=parseInt($(this).attr('data-id'));
      if (isNaN(id_product_idiotita)) id_product_idiotita=0;
      if (id_product_idiotita>0) {
        terms=$(this).tagit('assignedTags');
        isv=($('.gks_idiotita_isv[data-id=' + id_product_idiotita + ']').is(':checked') ? '1' : '0')
        if (isv=='1' && terms.length>0) {
          myvariables.push({id: id_product_idiotita, terms:terms,isv:isv});
        }
      }
    });
    
    
    
    $('.variable_product').each(function() {
      paa=parseInt($(this).attr('data-paa'));
      if (isNaN(paa)) paa=0;
      if (paa>0) {
        spanelem=$(this).find('.variables_list_combos');
        
        
        spanelem.find('.variables_combo').each(function() {
          iid=parseInt($(this).attr('data-iid'));
          if (isNaN(iid)) iid=0;
          if (iid>0) {
            found=false;
            for(i=0; i < myvariables.length; i++) {
              if (myvariables[i].id==iid) {
                found=true;
                break;
              }
            }
            if (found==false) {
              $(this).remove();
            }
            
          }
        });
        
        
        for(i=0; i < myvariables.length; i++) {
          combo=spanelem.find('.variables_combo[data-iid=' + myvariables[i].id + ']');
          if (combo.length==0) {
            html='<select class="variables_combo form-control form-control-sm myneedsave gks_stoppropagation" data-iid="' + myvariables[i].id + '">' +
            '<option value="0">' + gks_lang('Οποιοδήποτε') + '</option>';
            for(j=0; j < myvariables[i].terms.length; j++) {
              valueid=0;
              if (typeof gks_product_idiotites[myvariables[i].id] !='undefined') {
                for (k=0; k<gks_product_idiotites[myvariables[i].id].termsf.length; k++) {
                  if (gks_product_idiotites[myvariables[i].id].termsf[k].name == myvariables[i].terms[j]) {
                    valueid=gks_product_idiotites[myvariables[i].id].termsf[k].id;
                    break;
                  }
                }
              }
              if (valueid>0) {
                html+='<option value="' + valueid + '">' + myvariables[i].terms[j] + '</option>';
              }
            }            
            html+='</select>';
            
            if (i==0) {
              spanelem.prepend(html);
            } else {
              spanelem.find('.variables_combo[data-iid=' + myvariables[i-1].id + ']').after(html);
            }
            spanelem.find('.variables_combo[data-iid=' + myvariables[i].id + ']').click(function() {
              event.stopPropagation();
            });
            spanelem.find('.variables_combo[data-iid=' + myvariables[i].id + ']').change(variables_combo_change);
              
            need_save=true; 
          } else {
            
            for(j=0; j < myvariables[i].terms.length; j++) {
              if (typeof gks_product_idiotites[myvariables[i].id] !='undefined') {
                for (k=0; k<gks_product_idiotites[myvariables[i].id].termsf.length; k++) {
                  if (gks_product_idiotites[myvariables[i].id].termsf[k].name == myvariables[i].terms[j]) {
                    valueid=gks_product_idiotites[myvariables[i].id].termsf[k].id;
                    myoption=combo.find('option[value=' + valueid + ']');
                    if (myoption.length==0) {
                      html='<option value="' + valueid + '">' + myvariables[i].terms[j] + '</option>';
                      combo.append(html);
                    }
                  }
                }
              }
            }
            
            combo.find('option').each(function() {
              mytext=$(this).text();
              val=parseInt($(this).attr('value'));
              if (isNaN(val)) val=0;
              if (val>0) {
                //console.log(mytext,val);
                if (myvariables[i].terms.includes(mytext)==false) {
                  $(this).remove();
                  if (val==combo.val()) combo.val('0');
                }
              }
            });
          }
        }
      }
    });
    
    //console.log('myvariables');
    //console.log(myvariables);
    
    
    gks_varibles_summary();
  }
  

  

  $('#product_class').change(function() {
    from_php_product_class=$('#product_class').val();
    //console.log(from_php_product_class);
    
    if (from_php_product_class=='simple') {
      $('#div_variables').hide();
      $('.small_gks_idiotita_isv').hide();
    } else if (from_php_product_class=='variable') {
      $('#div_variables').show();
      $('.small_gks_idiotita_isv').show();
      gks_get_variables_from_idiotites();
    } else {
      $('#div_variables').hide();
      $('.small_gks_idiotita_isv').hide();
    }
  })


  var sindoiasmoi=[];
  function gks_get_variables_sindiasmoi_recursive(i) {
    ret=[];
    for (j=0; j < myvariables[i].terms.length; j++) {
      newitem={id:myvariables[i].id, term:myvariables[i].terms[j]}
      for (k=0; k < sindoiasmoi.length; k++) {
        temp=[];
        for(m=0; m < sindoiasmoi[k].length; m++) {
          temp.push(sindoiasmoi[k][m]);
        }
        temp.push(newitem);
        ret.push(temp);
      }
    }
    sindoiasmoi=ret;
  }
  
  function gks_get_variables_sindiasmoi() {
    sindoiasmoi=[];
    if (myvariables.length==0) return;
    for (j=0; j< myvariables[0].terms.length; j++) {
      temp=[];
      temp.push({id:myvariables[0].id, term:myvariables[0].terms[j]});
      sindoiasmoi.push(temp);
    }
    for (i=1; i< myvariables.length; i++) {
      gks_get_variables_sindiasmoi_recursive(i);
    }
    //console.log('sindoiasmoi');
    //console.log(sindoiasmoi);
  }

  function variable_add_run(mtype) {
    gks_get_variables_from_idiotites();
    if ($('.div_gks_idiotita').length==0) {
      myalert('error:' + gks_lang('Εισάγετε πρώτα κάποια ιδιότητες'));
      return;  
    }
    if (myvariables.length==0) {
      myalert('error:' + gks_lang('Ενεργοποιήστε την επιλογή<br><b>Χρησιμοποιείται στις παραλλαγές</b><br>στις ιδιότητες που θέλετε να συμμετέχουν στις παραλλαγές'));
      return;  
    }
    
    if (mtype=='all') {
      var exist_sindiasmos=[];
      $('.variable_product').each(function() {
        var temp='';
        $(this).find('.variables_combo').each(function() {
          temp+=$(this).find('option:selected').text() + '|';
        });
        exist_sindiasmos.push(temp);
      });
      //console.log(exist_sindiasmos);
      
      gks_get_variables_sindiasmoi();
      sindiasmoi_add=0;
      for (ij=0; ij < sindoiasmoi.length; ij++) {
        temp='';
        for (ijt=0; ijt < sindoiasmoi[ij].length; ijt++) {
          temp+=sindoiasmoi[ij][ijt].term + '|';
        }
        //console.log(temp);
        if (exist_sindiasmos.includes(temp)==false) {
          variable_add_item(sindoiasmoi[ij]);
          sindiasmoi_add++;
        }
      }
      
      if (sindiasmoi_add==0) {
        myalert('error:' + gks_lang('Δεν προστέθηκαν παραλλαγές'));
      } else {
        myalert('ok:' + gks_lang('Προστέθηκαν <b>[1]</b> παραλλαγές').replace('[1]',sindiasmoi_add));
      }
      
    } else if (mtype=='select') {
      var exist_sindiasmos=[];
      $('.variable_product').each(function() {
        var temp='';
        $(this).find('.variables_combo').each(function() {
          temp+=$(this).find('option:selected').text() + '|';
        });
        exist_sindiasmos.push(temp);
      });

      gks_get_variables_sindiasmoi();
      
      html='<table class="table table-sm table-responsive1 table-striped table-bordered gkstable100" border="0" ' +
        'cellspacing="0" cellpadding="5" align="center" id="dialog_variable_sindiasmoi_table">' +
        '<thead>' +
        '<tr>' +
        '<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">#</th>';
      mywidth=100/myvariables.length;
      
      for (i=0; i< myvariables.length; i++) {
        mytext='';
        if (typeof gks_product_idiotites[myvariables[i].id] !='undefined') mytext = gks_product_idiotites[myvariables[i].id].name;
        html+='<th class="table-dark" scope="col" style="text-align: center !important;" width="' + mywidth + '%">' + mytext + '</th>';
      }       
      html+='<th class="table-dark" scope="col" style="text-align: center !important;" width="0%">' + gks_lang('Επιλογή') + '</th>';                
      html+='</tr></thead><tbody>' ;

      
      sindiasmoi_add=1;
      for (ij=0; ij < sindoiasmoi.length; ij++) {
        temp='';
        mytr='<tr><th scope="row" nowrap style="text-align:center">' + sindiasmoi_add + '</th>';
        for (ijt=0; ijt < sindoiasmoi[ij].length; ijt++) {
          temp+=sindoiasmoi[ij][ijt].term + '|';
          mytr+='<td style="text-align:center">' + sindoiasmoi[ij][ijt].term + '</td>';
        }
        mytr+='<td style="text-align:center"><input type="checkbox" class="dialog_variable_sindiasmoi_checkbox" value="' + ij + '"></td>';
        mytr+='</tr>';
        
        //console.log(temp);
        if (exist_sindiasmos.includes(temp)==false) {
          html+=mytr;
          sindiasmoi_add++;

        }
      }
      html+='</tbody></table>';
      
      if (sindiasmoi_add<=1) {
        myalert('error:' + gks_lang('Δεν βρέθηκαν άλλοι συνδυασμοί'));
        return;  
      }
      
      $('#dialog_variable_sindiasmoi_div_table').html(html);
      
      dwidth=$(window).width() * 0.96;
      dheight=$(window).height() * 0.96;
  	  if (dwidth> 600) dwidth=600;
  	  if (dheight> 520) dheight=520;
  	  dialog_variable_sindiasmoi.dialog('option', 'width', dwidth);
  	  dialog_variable_sindiasmoi.dialog('option', 'height', dheight);
  	  $('#dialog_variable_sindiasmoi').parent().css({position:'fixed'});      
      dialog_variable_sindiasmoi.dialog('open');      

    } else {
      variable_add_item(null);
    }
    
    gks_varibles_summary();    
  }
  function variable_add_item(sindiasmos) {
    var maxpaa=0;
    $('.variable_product').each(function() {
      paa=parseInt($(this).attr('data-paa'));
      if (isNaN(paa)) paa=0;
      if (paa>maxpaa) maxpaa=paa;
    });
    maxpaa++;
    

    //return;
    gks_base_type_val=$('input[name=product_base_type]:checked').val();
    
    html=
    '<div class="variable_product card gks_card_expand" data-pid="0" data-paa="' + maxpaa + '">' +
      '<div class="card-header" style="text-align:left;padding-right: 60px;">' +
        '<span class="variables_id">#' + gks_lang('Νέο') + '</span> ' +
        '<span class="variables_list_combos"> ';
        for(i=0; i < myvariables.length; i++) {
          html+='<select class="variables_combo form-control form-control-sm myneedsave gks_stoppropagation" data-iid="' + myvariables[i].id + '">';
          html+='<option value="0">' + gks_lang('Οποιοδήποτε') + '</option>';
          
          
          for(j=0; j < myvariables[i].terms.length; j++) {
            //console.log(myvariables[i].terms[j]);  
            valueid=0;
            if (typeof gks_product_idiotites[myvariables[i].id] !='undefined') {
              for (k=0; k<gks_product_idiotites[myvariables[i].id].termsf.length; k++) {
                if (gks_product_idiotites[myvariables[i].id].termsf[k].name == myvariables[i].terms[j]) {
                  valueid=gks_product_idiotites[myvariables[i].id].termsf[k].id;
                  break;
                }
              }
            }
            if (valueid>0) {
              html+='<option value="' + valueid + '">' + myvariables[i].terms[j] + '</option>';
            }            
          }

          //console.log(valueid);
          
  //          foreach ($value1['terms'] as $value2) {
  //            echo '<option value="'.$value2['id_product_idiotita_term'].'"';
  //            foreach ($variable_item['products_variables'] as $value3) {
  //              if ($value3['product_idiotita_term_id'] == $value2['id_product_idiotita_term']) {
  //                echo ' selected';
  //                //break;
  //              }
  //            } 
  //            echo '>'.$value2['idiotita_term_name'].'</option>';
  //          } 
          html+='</select>';
        } 
        
        html+='</span>' +
        '<i class="variable_product_delete fas fa-trash-alt gks_stoppropagation"></i>' +
        '<i class="fas fa-arrows-alt-v sortorder_handle"></i>' +
      '</div>' +
      '<div class="card-body">' +
      

              '<div class="col-md-12" style="text-align:center;margin-bottom: 10px;">' +
                '<img src="/my/img/product.png" border="0" style="" data-paa="' + maxpaa + '" class="variable_product_photo_img"/><br>' +
                '<img src="/my/img/0.png" data-paa="' + maxpaa + '" class="variable_product_photo_reset" title="' + gks_lang('Αφαίρεση') + '" style="display:none;">' +
                '<input type="hidden" data-paa="' + maxpaa + '" class="variable_product_photo" value="" />' +
              '</div>' +
                    
              '<div class="form-group row">' +
                '<label for="variable_product_code_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Κωδικός') + ':</label>' +
                '<div class="col-md-8">' +
                  '<input id="variable_product_code_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_code form-control form-control-sm myneedsave" value="">' +
                '</div>' +
              '</div>' +
              '<div class="form-group row">' +
                '<label for="variable_product_descr_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Περιγραφή') + ':</label>' +
                '<div class="col-md-8">' +
                  '<input id="variable_product_descr_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_descr form-control form-control-sm myneedsave" value="">' +
                '</div>' +
              '</div>';
              for (ilangdiv=0; ilangdiv<from_php_GKS_LANG_DATA_ARRAY.length; ilangdiv++) {
                html+=  
                '<div class="form-group row">' +
                  '<label for="product_descr_' + from_php_GKS_LANG_DATA_ARRAY[ilangdiv].id_lang + 'laang' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Περιγραφή') + ' (' + from_php_GKS_LANG_DATA_ARRAY[ilangdiv].lang_name + '):</label>' +
                  '<div class="col-md-8">' +
                    '<input id="product_descr_' + from_php_GKS_LANG_DATA_ARRAY[ilangdiv].id_lang + 'laang' + maxpaa + '" type="text" class="form-control form-control-sm myneedsave gks_lang_data_obj_input_variable" value="">' +
                  '</div>' +
                '</div>';                
              }
              html+=
              '<div class="form-group row">' +
                '<label for="variable_product_def_comments_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Σχόλιο για παραγγελία, παραστατικό, δελτίο') + ':</label>' +
                '<div class="col-md-8">' +
                  '<textarea id="variable_product_def_comments_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_def_comments form-control form-control-sm myneedsave" style="min-height: 100px;height:100px;"></textarea>' +
                '</div>' +
              '</div>';              
              for (ilangdiv=0; ilangdiv<from_php_GKS_LANG_DATA_ARRAY.length; ilangdiv++) {
                html+=  
                '<div class="form-group row">' +
                  '<label for="product_def_comments_' + from_php_GKS_LANG_DATA_ARRAY[ilangdiv].id_lang + 'laang' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Σχόλιο για παραγγελία, παραστατικό, δελτίο') + ' (' + from_php_GKS_LANG_DATA_ARRAY[ilangdiv].lang_name + '):</label>' +
                  '<div class="col-md-8">' +
                    '<textarea id="product_def_comments_' + from_php_GKS_LANG_DATA_ARRAY[ilangdiv].id_lang + 'laang' + maxpaa + '" type="text" class="form-control form-control-sm myneedsave gks_lang_data_obj_input_textarea_variable" style="min-height: 100px;height:100px;"></textarea>' +
                  '</div>' +
                '</div>';                
              }              
                            
              if ($('#product_descr_small').length>0) {
                 html+=
                '<div class="form-group row">' +
                  '<label for="variable_product_descr_small_' + maxpaa + '" class="col-md-12 col-form-label form-control-sm text-md-right">' + gks_lang('Μικρή Περιγραφή') + ':</label>' +
                  '<div class="col-md-12">' +
                    '<textarea id="variable_product_descr_small_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_descr_small form-control form-control-sm myneedsave"></textarea>' +
                  '</div>' +
                '</div>';
                
                for (ilangdiv=0; ilangdiv<from_php_GKS_LANG_DATA_ARRAY.length; ilangdiv++) {
                 html+=
                  '<div class="form-group row">' +
                    '<label for="product_descr_small_' + from_php_GKS_LANG_DATA_ARRAY[ilangdiv].id_lang + 'laang' + maxpaa + '" class="col-md-12 col-form-label form-control-sm text-md-right">' + gks_lang('Μικρή Περιγραφή') + ' (' + from_php_GKS_LANG_DATA_ARRAY[ilangdiv].lang_name + '):</label>' +
                    '<div class="col-md-12">' +
                      '<textarea id="product_descr_small_' + from_php_GKS_LANG_DATA_ARRAY[ilangdiv].id_lang + 'laang' + maxpaa + '" type="text" class="form-control form-control-sm myneedsave gks_lang_data_obj_input_tinymce_variable"></textarea>' +
                    '</div>' +
                  '</div>';                  
                }
              }
              
              html+=
              '<div class="form-group row">' +
                '<label for="variable_product_sku_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right" title="Stock Keeping Unit">' + gks_lang('SKU') + ':</label>' +
                '<div class="col-md-8">' +
                  '<input id="variable_product_sku_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_sku form-control form-control-sm myneedsave" value="">' +
                '</div>' +
              '</div>' +
              '<div class="form-group row">' +
                '<label for="variable_product_gtin_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right" title="Global Trade Item Number">' + gks_lang('GTIN') + ':</label>' +
                '<div class="col-md-8">' +
                  '<input id="variable_product_gtin_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_gtin form-control form-control-sm myneedsave" value="">' +
                '</div>' +
              '</div>' +
              '<div class="form-group row">' +
                '<label for="variable_product_upc_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right" title="Universal Product Code">' + gks_lang('UPC') + ':</label>' +
                '<div class="col-md-8">' +
                  '<input id="variable_product_upc_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_upc form-control form-control-sm myneedsave" value="">' +
                '</div>' +
              '</div>' +
              '<div class="form-group row">' +
                '<label for="variable_product_ean_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right" title="European Article Number">' + gks_lang('EAN') + ':</label>' +
                '<div class="col-md-8">' +
                  '<input id="variable_product_ean_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_ean form-control form-control-sm myneedsave" value="">' +
                '</div>' +
              '</div>' +
              '<div class="form-group row">' +
                '<label for="variable_product_isbn_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right" title="International Standard Book Number">' + gks_lang('ISBN') + ':</label>' +
                '<div class="col-md-8">' +
                  '<input id="variable_product_isbn_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_isbn form-control form-control-sm myneedsave" value="">' +
                '</div>' +
              '</div>' +
              
              
              
              
              '<div class="form-group row">' +
                '<label for="variable_product_taric_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Taric No') + ':</label>' +
                '<div class="col-md-8">' +
                  '<input id="variable_product_taric_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_taric form-control form-control-sm myneedsave" value="" placeholder="'+gks_lang('π.χ.')+' 0710 80 70">' +
                  ' <i class="product_taric_get_descr fas fa-search-plus" title="' + gks_lang('Αναζήτηση περιγραφής') + '"></i>' +
                '</div>' +
                '<div class="col-md-12 product_taric_descr"></div>' + 
              '</div>' +
              '<div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>' +

'<div class="form-group row">' +
  '<label for="variable_product_kostos_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Κόστος') + ':</label>' +
  '<div class="col-md-4">' +
    '<input id="variable_product_kostos_' + maxpaa + '" type="number" min="0" step="' + from_php_GKS_INPUT_STEP_AJIA + '" data-paa="' + maxpaa + '" class="variable_product_kostos form-control form-control-sm myneedsave" value="' + $('#product_kostos').val() + '">' +
    '<small>' + gks_lang('Χωρίς ΦΠΑ','part4','fpa_base_descr') + '</small>' + 
  '</div>' +
'</div>' +


'<div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;' + ($('#product_need_apostoli').is(':checked') ? '' : 'display:none;') + '" class="div_product_need_apostoli"></div>' +

'<div class="form-group row">' +
  '<label for="variable_product_price_yperx_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Τιμή ΥπερΧονδρικής') + ':</label>' +
  '<div class="col-md-4">' +
    '<input id="variable_product_price_yperx_' + maxpaa + '" type="number" min="0" step="' + from_php_GKS_INPUT_STEP_AJIA + '" data-paa="' + maxpaa + '" class="variable_product_price_yperx form-control form-control-sm myneedsave" value="' + $('#product_price_yperx').val() + '">' +
  '</div>' +
  '<label for="variable_product_price_yperx_include_vat_' + maxpaa + '" class="col-md-2 col-form-label form-control-sm text-md-right">' + gks_lang('Περιέχει ΦΠΑ') + ':</label>' +
  '<div class="col-md-2">' +
    '<input type="checkbox" id="variable_product_price_yperx_include_vat_' + maxpaa + '" value="1" ' + ($('#product_price_yperx_include_vat').is(':checked') ? 'checked' : '') + ' data-paa="' + maxpaa + '" class="variable_product_price_yperx_include_vat switchery1_this">' +
  '</div>' +
'</div>' +

'<div class="form-group row">' +
  '<label for="variable_product_price_yperx_sale_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Προσφορά ΥπερΧονδρικής') + ':</label>' +
  '<div class="col-md-4">' +
    '<input id="variable_product_price_yperx_sale_' + maxpaa + '" type="number" min="0" step="' + from_php_GKS_INPUT_STEP_AJIA + '" data-paa="' + maxpaa + '" class="variable_product_price_yperx_sale form-control form-control-sm myneedsave" value="' + $('#product_price_yperx_sale').val() + '">' +
  '</div>' +
  '<label for="variable_product_price_yperx_sale_dates_' + maxpaa + '" class="col-md-2 col-form-label form-control-sm text-md-right">' + gks_lang('Ημερομηνίες') + ':</label>' +
  '<div class="col-md-2">' +
    '<input type="checkbox" id="variable_product_price_yperx_sale_dates_' + maxpaa + '" value="1" ' + ($('#product_price_yperx_sale_dates').is(':checked') ? 'checked' : '') + ' data-paa="' + maxpaa + '" class="variable_product_price_yperx_sale_dates switchery1_this">' +
  '</div>' +
'</div>' +


'<div class="variable_div_product_price_yperx_sale_dates form-group row" data-paa="' + maxpaa + '" style="' + ($('#div_product_price_yperx_sale_dates').is(":hidden") ? 'display:none;' : '') + '">' +
  '<label for="variable_product_price_yperx_sale_from_' + maxpaa + '" class="col-md-2 col-form-label form-control-sm text-md-right">' + gks_lang('Από') + ':</label>' +
  '<div class="col-md-4">' +
    '<input id="variable_product_price_yperx_sale_from_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_price_yperx_sale_from form-control form-control-sm myneedsave" value="' + $('#product_price_yperx_sale_from').val() + '" autocomplete="' + autocomplete_gks_disable + '" style="max-width:150px">' +
  '</div>' +
  '<label for="variable_product_price_yperx_sale_to_' + maxpaa + '" class="col-md-2 col-form-label form-control-sm text-md-right">' + gks_lang('Έως') + ':</label>' +
  '<div class="col-md-4">' +
    '<input id="variable_product_price_yperx_sale_to_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_price_yperx_sale_to form-control form-control-sm myneedsave" value="' + $('#product_price_yperx_sale_to').val() + '" autocomplete="' + autocomplete_gks_disable + '" style="max-width:150px">' +
  '</div>' +
'</div>' +


'<div class="form-group row">' +
  '<label for="variable_product_price_yperx_sheets_formula_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Τύπος υπολογισμού τεμαχίου') + ':</label>' +
  '<div class="col-md-7">' +
    '<input id="variable_product_price_yperx_sheets_formula_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_price_yperx_sheets_formula form-control form-control-sm myneedsave" value="' + $('#product_price_yperx_sheets_formula').val() + '">' +
  '</div>' +
  
  '<div class="col-md-1 text-md-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" ' +
    'title="[sheets] : ' + gks_lang('Σελίδες') +
    '<br>[itemprice] : ' + gks_lang('η τιμή υπερχονδρικής') +
    '<br>' + gks_lang('π.χ.') +
    '<br>[sheets]*[itemprice]' +
    '<br>([sheets]<10 ? 10*[itemprice] + 35 : [sheets]*[itemprice] + 35)"' +
      '></i></div>' +
'</div>' +
'<div class="form-group row">' +
  '<label for="variable_product_price_yperx_quantity_formula_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Τύπος υπολογισμού συνόλου') + ':</label>' +
  '<div class="col-md-7">' +
    '<input id="variable_product_price_yperx_quantity_formula_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_price_yperx_quantity_formula form-control form-control-sm myneedsave" value="' + $('#product_price_yperx_quantity_formula').val() + '">' +
  '</div>' +
  '<div class="col-md-1 text-md-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" ' +
    'title="[quantity] : ' + gks_lang('Ποσότητα') +
    '<br>[itemprice] : ' + gks_lang('η τιμή υπερχονδρικής ή το αποτέλεσμα του τύπου υπολογισμού τεμαχίου') +
    '<br>' + gks_lang('π.χ.') +
    '<br>[quantity]*[itemprice]' +
    '<br>([quantity]<10 ? [quantity]*[itemprice] : [quantity]*[itemprice] * 0.9)"' +
      '></i></div>' +
'</div>' +


'<div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>' +

              
    
'<div class="form-group row">' +
  '<label for="variable_product_price_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Τιμή Χονδρικής') + ':</label>' +
  '<div class="col-md-4">' +
    '<input id="variable_product_price_' + maxpaa + '" type="number" min="0" step="' + from_php_GKS_INPUT_STEP_AJIA + '" data-paa="' + maxpaa + '" class="variable_product_price form-control form-control-sm myneedsave" value="' + $('#product_price').val() + '">' +
  '</div>' +
  '<label for="variable_product_price_include_vat_' + maxpaa + '" class="col-md-2 col-form-label form-control-sm text-md-right">' + gks_lang('Περιέχει ΦΠΑ') + ':</label>' +
  '<div class="col-md-2">' +
    '<input type="checkbox" id="variable_product_price_include_vat_' + maxpaa + '" value="1" ' + ($('#product_price_include_vat').is(':checked') ? 'checked' : '') + ' data-paa="' + maxpaa + '" class="variable_product_price_include_vat switchery1_this">' +
  '</div>' +
'</div>' +


'<div class="form-group row">' +
  '<label for="variable_product_price_sale_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Προσφορά Χονδρικής') + ':</label>' +
  '<div class="col-md-4">' +
    '<input id="variable_product_price_sale_' + maxpaa + '" type="number" min="0" step="' + from_php_GKS_INPUT_STEP_AJIA + '" data-paa="' + maxpaa + '" class="variable_product_price_sale form-control form-control-sm myneedsave" value="' + $('#product_price_sale').val() + '">' +
  '</div>' +
  '<label for="variable_product_price_sale_dates_' + maxpaa + '" class="col-md-2 col-form-label form-control-sm text-md-right">' + gks_lang('Ημερομηνίες') + ':</label>' +
  '<div class="col-md-2">' +
    '<input type="checkbox" id="variable_product_price_sale_dates_' + maxpaa + '" value="1" ' + ($('#product_price_sale_dates').is(':checked') ? 'checked' : '') + ' data-paa="' + maxpaa + '" class="variable_product_price_sale_dates switchery1_this">' +
  '</div>' +
'</div>' +

'<div class="variable_div_product_price_sale_dates form-group row" data-paa="' + maxpaa + '" style="' + ($('#div_product_price_sale_dates').is(":hidden") ? 'display:none;' : '') + '">' +
  '<label for="variable_product_price_sale_from_' + maxpaa + '" class="col-md-2 col-form-label form-control-sm text-md-right">' + gks_lang('Από') + ':</label>' +
  '<div class="col-md-4">' +
    '<input id="variable_product_price_sale_from_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_price_sale_from form-control form-control-sm myneedsave" value="' + $('#product_price_sale_from').val() + '" autocomplete="' + autocomplete_gks_disable + '" style="max-width:150px">' +
  '</div>' +
  '<label for="variable_product_price_sale_to_' + maxpaa + '" class="col-md-2 col-form-label form-control-sm text-md-right">' + gks_lang('Έως') + ':</label>' +
  '<div class="col-md-4">' +
    '<input id="variable_product_price_sale_to_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_price_sale_to form-control form-control-sm myneedsave" value="' + $('#product_price_sale_to').val() + '" autocomplete="' + autocomplete_gks_disable + '" style="max-width:150px">' +
  '</div>' +
'</div>' +


'<div class="form-group row">' +
  '<label for="variable_product_price_sheets_formula_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Τύπος υπολογισμού τεμαχίου') + ':</label>' +
  '<div class="col-md-7">' +
    '<input id="variable_product_price_sheets_formula_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_price_sheets_formula form-control form-control-sm myneedsave" value="' + $('#product_price_sheets_formula').val() + '">' +
  '</div>' +
  '<div class="col-md-1 text-md-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" ' +
    'title="[sheets] : ' + gks_lang('Σελίδες') +
    '<br>[itemprice] : ' + gks_lang('η τιμή χονδρικής') +
    '<br>' + gks_lang('π.χ.') +
    '<br>[sheets]*[itemprice]' +
    '<br>([sheets]<10 ? 10*[itemprice] + 35 : [sheets]*[itemprice] + 35)"' +
      '></i></div>' +
'</div>' +
'<div class="form-group row">' +
  '<label for="variable_product_price_quantity_formula_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Τύπος υπολογισμού συνόλου') + ':</label>' +
  '<div class="col-md-7">' +
    '<input id="variable_product_price_quantity_formula_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_price_quantity_formula form-control form-control-sm myneedsave" value="' + $('#product_price_quantity_formula').val() + '">' +
  '</div>' +
  '<div class="col-md-1 text-md-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" ' +
    'title="[quantity] : ' + gks_lang('Ποσότητα') +
    '<br>[itemprice] : ' + gks_lang('η τιμή χονδρικής ή το αποτέλεσμα του τύπου υπολογισμού τεμαχίου') +
    '<br>' + gks_lang('π.χ.') +
    '<br>[quantity]*[itemprice]' +
    '<br>([quantity]<10 ? [quantity]*[itemprice] : [quantity]*[itemprice] * 0.9)"' +
      '></i></div>' +
'</div>' +

'<div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>' +



'<div class="form-group row">' +
  '<label for="variable_product_price_retail_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Τιμή Λιανικής') + ':</label>' +
  '<div class="col-md-4">' +
    '<input id="variable_product_price_retail_' + maxpaa + '" type="number" min="0" step="' + from_php_GKS_INPUT_STEP_AJIA + '" data-paa="' + maxpaa + '" class="variable_product_price_retail form-control form-control-sm myneedsave" value="' + $('#product_price_retail').val() + '">' +
  '</div>' +
  '<label for="variable_product_price_retail_include_vat_' + maxpaa + '" class="col-md-2 col-form-label form-control-sm text-md-right">' + gks_lang('Περιέχει ΦΠΑ') + ':</label>' +
  '<div class="col-md-2">' +
    '<input type="checkbox" id="variable_product_price_retail_include_vat_' + maxpaa + '" value="1" ' + ($('#product_price_retail_include_vat').is(':checked') ? 'checked' : '') + ' data-paa="' + maxpaa + '" class="variable_product_price_retail_include_vat switchery1_this">' +
  '</div>' +
'</div>' +

'<div class="form-group row">' +
  '<label for="variable_product_price_retail_sale_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Προσφορά Λιανικής') + ':</label>' +
  '<div class="col-md-4">' +
    '<input id="variable_product_price_retail_sale_' + maxpaa + '" type="number" min="0" step="' + from_php_GKS_INPUT_STEP_AJIA + '" data-paa="' + maxpaa + '" class="variable_product_price_retail_sale form-control form-control-sm myneedsave" value="' + $('#product_price_retail_sale').val() + '">' +
  '</div>' +
  '<label for="variable_product_price_retail_sale_dates_' + maxpaa + '" class="col-md-2 col-form-label form-control-sm text-md-right">' + gks_lang('Ημερομηνίες') + ':</label>' +
  '<div class="col-md-2">' +
    '<input type="checkbox" id="variable_product_price_retail_sale_dates_' + maxpaa + '" value="1" ' + ($('#product_price_retail_sale_dates').is(':checked') ? 'checked' : '') + ' data-paa="' + maxpaa + '" class="variable_product_price_retail_sale_dates switchery1_this">' +
  '</div>' +
'</div>' +


'<div class="variable_div_product_price_retail_sale_dates form-group row" data-paa="' + maxpaa + '" style="' + ($('#div_product_price_retail_sale_dates').is(":hidden") ? 'display:none;' : '') + '">' +
  '<label for="variable_product_price_retail_sale_from_' + maxpaa + '" class="col-md-2 col-form-label form-control-sm text-md-right">' + gks_lang('Από') + ':</label>' +
  '<div class="col-md-4">' +
    '<input id="variable_product_price_retail_sale_from_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_price_retail_sale_from form-control form-control-sm myneedsave" value="' + $('#product_price_retail_sale_from').val() + '" autocomplete="' + autocomplete_gks_disable + '" style="max-width:150px">' +
  '</div>' +
  '<label for="variable_product_price_retail_sale_to_' + maxpaa + '" class="col-md-2 col-form-label form-control-sm text-md-right">' + gks_lang('Έως') + ':</label>' +
  '<div class="col-md-4">' +
    '<input id="variable_product_price_retail_sale_to_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_price_retail_sale_to form-control form-control-sm myneedsave" value="' + $('#product_price_retail_sale_to').val() + '" autocomplete="' + autocomplete_gks_disable + '" style="max-width:150px">' +
  '</div>' +
'</div>' +


'<div class="form-group row">' +
  '<label for="variable_product_price_retail_sheets_formula_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Τύπος υπολογισμού τεμαχίου') + ':</label>' +
  '<div class="col-md-7">' +
    '<input id="variable_product_price_retail_sheets_formula_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_price_retail_sheets_formula form-control form-control-sm myneedsave" value="' + $('#product_price_retail_sheets_formula').val() + '">' +
  '</div>' +
  
  '<div class="col-md-1 text-md-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" ' +
    'title="[sheets] : ' + gks_lang('Σελίδες') +
    '<br>[itemprice] : ' + gks_lang('η τιμή λιανικής') +
    '<br>[price] : ' + gks_lang('το αποτέλεσμα υπολογισμού της τιμής χονδρικής') +
    '<br>' + gks_lang('π.χ.') +
    '<br>[price]*1.5' +
    '<br>[sheets]*[itemprice]' +
    '<br>([sheets]<10 ? 10*[itemprice] + 35 : [sheets]*[itemprice] + 35)"' +
      '></i></div>' +
'</div>' +
'<div class="form-group row">' +
  '<label for="variable_product_price_retail_quantity_formula_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Τύπος υπολογισμού συνόλου') + ':</label>' +
  '<div class="col-md-7">' +
    '<input id="variable_product_price_retail_quantity_formula_' + maxpaa + '" type="text" data-paa="' + maxpaa + '" class="variable_product_price_retail_quantity_formula form-control form-control-sm myneedsave" value="' + $('#product_price_retail_quantity_formula').val() + '">' +
  '</div>' +
  '<div class="col-md-1 text-md-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" ' +
    'title="[quantity] : ' + gks_lang('Ποσότητα') +
    '<br>[itemprice] : ' + gks_lang('η τιμή λιανικής ή το αποτέλεσμα του τύπου υπολογισμού τεμαχίου') +
    '<br>[price] : ' + gks_lang('το αποτέλεσμα υπολογισμού της τιμής χονδρικής') +
    '<br>'+gks_lang('π.χ.') +
    '<br>[quantity]*[price]*0.9' +
    '<br>[quantity]*[itemprice]' +
    '<br>([quantity]<10 ? [quantity]*[itemprice] : [quantity]*[itemprice] * 0.9)"' +
      '></i></div>' +
'</div>';



for(plid=0;plid<pricelists.length;plid++) {
    plist_id='_'+pricelists[plid].id;
    
    ' + maxpaa + '

html+=    
'<div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>'+

'<div class="gks_variable_product_price_plist_item" data-id_pricelist="' + pricelists[plid].id + '">'+
  '<div class="form-group row">'+
    '<label for="variable_product_price_plist'+plist_id+'_'+maxpaa+'" class="col-md-4 col-form-label form-control-sm text-md-right">'+pricelists[plid].descr+':</label>'+
    '<div class="col-md-4">'+
      '<input id="variable_product_price_plist'+plist_id+'_'+maxpaa+'" type="number" min="0" step="' + from_php_GKS_INPUT_STEP_AJIA + '" data-paa="' + maxpaa + '" class="variable_product_price_plist form-control form-control-sm myneedsave" value="' + $('#product_price_plist_'+pricelists[plid].id).val()+'">'+
    '</div>'+
    '<label for="variable_product_price_plist_include_vat'+plist_id+'_'+maxpaa+'" class="col-md-2 col-form-label form-control-sm text-md-right">' + gks_lang('Περιέχει ΦΠΑ') + ':</label>'+
    '<div class="col-md-2">'+
      '<input type="checkbox" id="variable_product_price_plist_include_vat'+plist_id+'_'+maxpaa+'" value="1" ' + ($('#product_price_plist_include_vat_'+pricelists[plid].id).is(':checked') ? 'checked' : '') + ' data-paa="' + maxpaa + '" class="variable_product_price_plist_include_vat switchery1_this">'+
    '</div>'+
  '</div>'+
  '<div class="form-group row">'+
    '<label for="variable_product_price_plist_sale'+plist_id+'_'+maxpaa+'" class="col-md-4 col-form-label form-control-sm text-md-right">'+gks_lang('Προσφορά') + ' ' + pricelists[plid].descr + ':</label>'+
    '<div class="col-md-4">'+
      '<input id="variable_product_price_plist_sale'+plist_id+'_'+maxpaa+'" type="number" min="0" step="' + from_php_GKS_INPUT_STEP_AJIA + '" data-paa="' + maxpaa + '" class="variable_product_price_plist_sale form-control form-control-sm myneedsave" value="' + $('#product_price_plist_sale_'+pricelists[plid].id).val()+'">'+
    '</div>'+
    '<label for="variable_product_price_plist_sale_dates'+plist_id+'_'+maxpaa+'" class="col-md-2 col-form-label form-control-sm text-md-right">'+gks_lang('Ημερομηνίες')+':</label>'+
    '<div class="col-md-2">'+
      '<input type="checkbox" data-plist_id="'+plist_id+'" id="variable_product_price_plist_sale_dates'+plist_id+'_'+maxpaa+'" value="1" ' + ($('#product_price_plist_sale_dates_'+pricelists[plid].id).is(':checked') ? 'checked' : '') + ' data-paa="' + maxpaa + '" class="variable_product_price_plist_sale_dates switchery1_this">'+
    '</div>'+
  '</div>'+
  
  '<div class="variable_div_product_price_plist_sale_dates'+plist_id+' form-group row" data-paa="' + maxpaa + '" style="' + ($('#div_product_price_plist_sale_dates_'+pricelists[plid].id).is(":hidden") ? 'display:none;' : '') + ';">'+
    '<label for="variable_product_price_plist_sale_from'+plist_id+'_'+maxpaa+'" class="col-md-2 col-form-label form-control-sm text-md-right">'+gks_lang('Από')+':</label>'+
    '<div class="col-md-4">'+
      '<input id="variable_product_price_plist_sale_from'+plist_id+'_'+maxpaa+'" type="text" data-paa="' + maxpaa + '" class="variable_product_price_plist_sale_from form-control form-control-sm myneedsave" value="' + $('#product_price_plist_sale_from_'+pricelists[plid].id).val() + '" autocomplete="' + autocomplete_gks_disable + '" style="max-width:150px">'+
    '</div>'+
    '<label for="variable_product_price_plist_sale_to'+plist_id+'_'+maxpaa+'" class="col-md-2 col-form-label form-control-sm text-md-right">'+gks_lang('Έως')+':</label>'+
    '<div class="col-md-4">'+
      '<input id="variable_product_price_plist_sale_to'+plist_id+'_'+maxpaa+'" type="text" data-paa="' + maxpaa + '" class="variable_product_price_plist_sale_to form-control form-control-sm myneedsave" value="' + $('#product_price_plist_sale_to_'+pricelists[plid].id).val() + '" autocomplete="' + autocomplete_gks_disable + '" style="max-width:150px">'+
    '</div>'+
  '</div>'+
  
  
  '<div class="form-group row">'+
    '<label for="variable_product_price_plist_sheets_formula'+plist_id+'_'+maxpaa+'" class="col-md-4 col-form-label form-control-sm text-md-right">'+gks_lang('Τύπος υπολογισμού τεμαχίου')+':</label>'+
    '<div class="col-md-7">'+
      '<input id="variable_product_price_plist_sheets_formula'+plist_id+'_'+maxpaa+'" type="text" data-paa="' + maxpaa + '" class="variable_product_price_plist_sheets_formula form-control form-control-sm myneedsave" value="' + $('#product_price_plist_sheets_formula_'+pricelists[plid].id).val() + '">'+
    '</div>'+
    
    '<div class="col-md-1 text-md-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" '+
      'title="[sheets] : '+gks_lang('Σελίδες')+
      '<br>[itemprice] : '+gks_lang('η τιμή')+' '+pricelists[plid].descr+
      '<br>'+gks_lang('π.χ.')+
      '<br>[sheets]*[itemprice]'+
      '<br>([sheets]<10 ? 10*[itemprice] + 35 : [sheets]*[itemprice] + 35)"'+
        '></i></div>'+
  '</div>'+
  '<div class="form-group row">'+
    '<label for="variable_product_price_plist_quantity_formula'+plist_id+'_'+maxpaa+'" class="col-md-4 col-form-label form-control-sm text-md-right">'+gks_lang('Τύπος υπολογισμού συνόλου')+':</label>'+
    '<div class="col-md-7">'+
      '<input id="variable_product_price_plist_quantity_formula'+plist_id+'_'+maxpaa+'" type="text" data-paa="' + maxpaa + '" class="variable_product_price_plist_quantity_formula form-control form-control-sm myneedsave" value="' + $('#product_price_plist_quantity_formula_'+pricelists[plid].id).val() + '">'+
    '</div>'+
    '<div class="col-md-1 text-md-right"><i class="fas fa-info-circle tooltipster" style="vertical-align: middle;" '+
      'title="[quantity] : '+gks_lang('Ποσότητα')+
      '<br>[itemprice] : '+ gks_lang('η τιμή [1] ή το αποτέλεσμα του τύπου υπολογισμού τεμαχίου').replaceAll('[1]',pricelists[plid].descr)+
      '<br>'+gks_lang('π.χ.')+
      '<br>[quantity]*[itemprice]'+
      '<br>([quantity]<10 ? [quantity]*[itemprice] : [quantity]*[itemprice] * 0.9)"'+
        '></i></div>'+
  '</div>'+
'</div>';

}





html+=
'<div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>' +




'<div class="form-group row div_product_need_apostoli" style="' + ($('#product_need_apostoli').is(':checked') ? '' : 'display:none;') + '">' +
  '<label for="variable_product_varos_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Βάρος σε gr') + ':</label>' +
  '<div class="col-md-8">' +
    '<input id="variable_product_varos_' + maxpaa + '" type="number" data-paa="' + maxpaa + '" class="variable_product_varos form-control form-control-sm myneedsave" value="' + $('#product_varos').val() + '" min=0 step="0.01" style="max-width:150px">' +
  '</div>' +
'</div>' +


'<div class="form-group row div_product_need_apostoli" style="' + ($('#product_need_apostoli').is(':checked') ? '' : 'display:none;') + '">' +
  '<label for="variable_product_ogos_x_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Διαστάσεις σε cm') + ':</label>' +
  '<div class="col-md-8">' +
    '<div class="row">' +
      '<div class="col-md-4" style="padding-right:0px;">' +
        '<label for="variable_product_ogos_x_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right" style="display: inline;padding-left:0px;">' + gks_lang('Μήκος') + ':</label>' +
        '<input id="variable_product_ogos_x_' + maxpaa + '" type="number" data-paa="' + maxpaa + '" class="variable_product_ogos_x form-control form-control-sm myneedsave" value="' + $('#product_ogos_x').val() + '" style="display: inline;max-width: 70px;" min=0 step="0.01">' +
      '</div>' +
      '<div class="col-md-4" style="padding-left:0px;padding-right:0px;">' +
        '<label for="variable_product_ogos_y_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right" style="display: inline;">' + gks_lang('Πλάτος') + ':</label>' +
        '<input id="variable_product_ogos_y_' + maxpaa + '" type="number" data-paa="' + maxpaa + '" class="variable_product_ogos_y form-control form-control-sm myneedsave" value="' + $('#product_ogos_y').val() + '" style="display: inline;max-width: 70px;" min=0 step="0.01">' +
      '</div>' +
      '<div class="col-md-4" style="padding-left:0px;">' +
        '<label for="variable_product_ogos_z_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right" style="display: inline;">' + gks_lang('Ύψος') + ':</label>' +
        '<input id="variable_product_ogos_z_' + maxpaa + '" type="number" data-paa="' + maxpaa + '" class="variable_product_ogos_z form-control form-control-sm myneedsave" value="' + $('#product_ogos_z').val() + '" style="display: inline;max-width: 70px;" min=0 step="0.01">' +
      '</div>' +
    '</div>' +
  '</div>' +                   
'</div>' +


'<div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>' +


'<div class="form-group row">' + 
  '<label for="variable_product_fpa_base_id_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('ΦΠΑ') + ':</label>' + 
  '<div class="col-md-8">' + 
    '<select id="variable_product_fpa_base_id_' + maxpaa + '" data-paa="' + maxpaa + '" class="variable_product_fpa_base_id form-control form-control-sm myneedsave" style="max-width:200px">';
    
    var temp='';
    var val=$('#product_fpa_base_id').val();
    $('#product_fpa_base_id option').each(function() {
      mytext=$(this).text();
      myval=$(this).attr('value');
      
      temp+='<option value="' + myval + '" ' + (myval==val ? 'selected' : '') + '>' + mytext + '</option>';
    });
    html+=temp;
    

    
    html+='</select>' + 
  '</div>' + 
'</div>' +     

'<div class="gks_base_type0 gks_base_type1" style="' + ((!(gks_base_type_val==0 || gks_base_type_val==1)) ? 'display:none;' : '') + ';height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>' +

'<div class="form-group row gks_base_type0 gks_base_type1" style="' + ((!(gks_base_type_val==0 || gks_base_type_val==1)) ? 'display:none;' : '') + '">' +
  '<label for="variable_min_quantity_alert_' + maxpaa + '" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Όριο αποθέματος') + ':</label>' +
  '<div class="col-md-4">' +
    '<input id="variable_min_quantity_alert_' + maxpaa + '" data-paa="' + maxpaa + '" type="number" class="variable_min_quantity_alert form-control form-control-sm myneedsave" value="' + $('#min_quantity_alert').val() + '" min=0 step="' + from_php_GKS_INPUT_STEP_POSOTITA + '">' +
  '</div>' +
'</div>';

    if (from_php_GKS_ORDERS_PRODUCTION) {
    html+=
'<div class="gks_base_type0 gks_base_type1" style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;' + ((!(gks_base_type_val==0 || gks_base_type_val==1)) ? 'display:none;' : '') + '"></div>' +
'<div class="form-group row gks_base_type0 gks_base_type1" style="' + ((!(gks_base_type_val==0 || gks_base_type_val==1)) ? 'display:none;' : '') + '">' +
  '<label for="" class="col-md-4 col-form-label form-control-sm text-md-right">' + gks_lang('Συνταγές') + ':</label>' +
  '<div class="col-md-8">' +

  '</div>' +
'</div>';
      
    }

  
    html+=
          '</div>' +
    '</div>';
    
    //$('#div_variables_list').append(html);
    $('#div_variables_list').prepend(html);
    
    maindiv=$('.gks_card_expand[data-paa=' + maxpaa + ']');
    if (sindiasmos!=null) {
      for (ii=0; ii<sindiasmos.length; ii++) {
        //console.log(sindiasmos[ii]);
        maindiv.find('.variables_combo[data-iid=' + sindiasmos[ii].id + '] option').each(function () {
          ttext=$(this).text();
          if (ttext==sindiasmos[ii].term) {
            val=$(this).attr('value');
            maindiv.find('.variables_combo[data-iid=' + sindiasmos[ii].id + ']').val(val);
            return;
          }
        });
      }
      
    }    
    
    gks_card_expand_run(maindiv);
    
    maindiv.find('.variable_product_def_comments').on(mychange, product_def_comments_change);
    maindiv.find('.gks_lang_data_obj_input_textarea_variable').on(mychange, product_def_comments_change);
    
    maindiv.find('.variables_combo').change(variables_combo_change);
    maindiv.find('.variable_product_delete').click(variable_product_delete_click);
    maindiv.find('.variable_product_photo_reset').click(variable_product_photo_reset_click);
    maindiv.find('.variable_product_photo_img').click(variable_product_photo_img_click);
    maindiv.find('.gks_stoppropagation').click(function() {
      event.stopPropagation();
    });
    maindiv.find('.myneedsave').on('input keyup paste', function() {
      need_save=true; 
    });
    
    maindiv.find('.variable_product_taric').autocomplete(autocomplete_product_taric);
    maindiv.find('.product_taric_get_descr').click(product_taric_get_descr_click);

    maindiv.find('.variable_product_price_yperx_sale_from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
      function(ct,$i){
        need_save=true;
      } 
    }));
    maindiv.find('.variable_product_price_yperx_sale_to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
      function(ct,$i){
        need_save=true;
      }
    }));    
    maindiv.find('.variable_product_price_sale_from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
      function(ct,$i){
        need_save=true;
      }
    }));
    maindiv.find('.variable_product_price_sale_to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
      function(ct,$i){
        need_save=true;
      }
    }));  
    maindiv.find('.variable_product_price_retail_sale_from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
      function(ct,$i){
        need_save=true;
      } 
    }));
    maindiv.find('.variable_product_price_retail_sale_to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
      function(ct,$i){
        need_save=true;
      }
    }));

    maindiv.find('.variable_product_price_plist_sale_from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
      function(ct,$i){
        need_save=true;
      } 
    }));
    maindiv.find('.variable_product_price_plist_sale_to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
      function(ct,$i){
        need_save=true;
      }
    }));
        
    maindiv.find('.switchery1_this').each(function() {
      html=$(this)[0];
      var switchery3 = new Switchery(html,gks_switchery_defaults());
      html.onchange = function() {need_save=true;};
    });
    
    maindiv.find('.variable_product_price_yperx_sale_dates').change(variable_product_price_yperx_sale_dates_change);  
    maindiv.find('.variable_product_price_sale_dates').change(variable_product_price_sale_dates_change);
    maindiv.find('.variable_product_price_retail_sale_dates').change(variable_product_price_retail_sale_dates_change);  
    maindiv.find('.variable_product_price_plist_sale_dates').change(variable_product_price_plist_sale_dates_change);  

    maindiv.find('.tooltipster').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true});

    if ($('#product_descr_small').length>0) {
      gks_tinymce_init('#variable_product_descr_small_' + maxpaa);
      
      maindiv.find('.gks_lang_data_obj_input_tinymce_variable').each(function() {
        varid=$(this).attr('id');
        gks_tinymce_init('#' + varid);
      });
      
    }
    
    need_save=true;
   

    
    gks_myscroll(); 
  }
  
  var variable_add_contextMenu={
		//event: 'click',
    position: {
        my: 'left top+10',
        at: 'left bottom', 
        children: '.context-click-subitem'
    },		
    items: function(e) {
      var arr = [];
      arr.push({type: 'item', text: gks_lang('Απλή Εισαγωγή'), icon1: '', disabled: false,click: function(e){	
  		  e.preventDefault(); variable_add_run('');
  		}});  
      arr.push({type: 'item', text: gks_lang('Εισαγωγή όλων των συνδυασμών'), icon1: '', disabled: false,click: function(e){	
  		  e.preventDefault(); variable_add_run('all');
  		}});
      arr.push({type: 'item', text: gks_lang('Επιλογή από όλους τους συνδυασμούς')+ '...', icon1: '', disabled: false,click: function(e){	
  		  e.preventDefault(); variable_add_run('select');
  		}});

      return arr;
    }
	};

  $('#variable_add').contextMenu(variable_add_contextMenu);
	
	$('#variable_add').click(function(e) {
	  event.stopPropagation();
	  $('#variable_add').contextMenu('show',e);  
  });  
  
  
  var variable_actions_contextMenu={
		//event: 'click',
    position: {
        my: 'left top+10',
        at: 'left bottom', 
        children: '.context-click-subitem'
    },		
    items: function(e) {
      var arr = [];
  		//arr.push({type: 'divider'}); 
      arr.push({type: 'item', text: gks_lang('Αντιγραφή παραμέτρων από το βασικό είδος'), icon1: '', disabled: false,click: function(e){	
  		  e.preventDefault(); dialog_variable_copy_all_open();
  		}});
      arr.push({type: 'item', text: gks_lang('Τιμή ΥπερΧονδρικής')+'...', icon1: '', disabled: false,click: function(e){	
  		  e.preventDefault(); dialog_variable_prices_open('yperxondriki');
  		}});
      arr.push({type: 'item', text: gks_lang('Τιμή Χονδρικής')+'...', icon1: '', disabled: false,click: function(e){	
  		  e.preventDefault(); dialog_variable_prices_open('xondriki');
  		}});
      arr.push({type: 'item', text: gks_lang('Τιμή Λιανικής') + '...', icon1: '', disabled: false,click: function(e){	
  		  e.preventDefault(); dialog_variable_prices_open('lianiki');
  		}});
  		
  		for(plid=0;plid<pricelists.length;plid++) {
        arr.push({id:'gks_contextMenu_plid_'+plid, type: 'item', text: gks_lang('Τιμή') + ' ' + pricelists[plid].descr + '...', icon1: '', disabled: false, click: function(e){	
    		  e.preventDefault(); 
    		  fff=e.currentTarget.getAttribute('id').replaceAll('gks_contextMenu_plid_','');
    		  fff=parseInt(fff);if (isNaN(fff)) fff=-1;
    		  dialog_variable_prices_open('pricelist',fff);
    		}});  		
  		}
  		
      arr.push({type: 'item', text: gks_lang('Αποστολή') + '...', icon1: '', disabled: $('#product_need_apostoli').is(':checked')==false ,click: function(e){	
  		  e.preventDefault(); dialog_variable_apostoli_open();
  		}});
      arr.push({type: 'item', text: gks_lang('ΦΠΑ') + '...', icon1: '', disabled: false,click: function(e){	
  		  e.preventDefault(); dialog_variable_fpa_open();
  		}});
      arr.push({type: 'item', text: gks_lang('Αφαίρεση όλων των παραλλαγών'), icon1: '', disabled: false,click: function(e){	
  		  e.preventDefault(); dialog_variable_removeall();
  		}});

      return arr;
    }
	};

  $('#variable_actions').contextMenu(variable_actions_contextMenu);
	
	$('#variable_actions').click(function(e) {
	  event.stopPropagation();
	  $('#variable_actions').contextMenu('show',e);  
  });  
  
  function variables_combo_change() {
    gks_varibles_summary();
    need_save=true;    
  }
  
  $('.variables_combo').change(variables_combo_change);
  
  
  function variable_product_delete_click() {
    $(this).parent().parent().remove();
    gks_varibles_summary();
    need_save=true;
  }
  $('.variable_product_delete').click(variable_product_delete_click);
  
  function variable_product_photo_reset_click() {
    paa=parseInt($(this).attr('data-paa'));
    if (isNaN(paa)) paa=0;
    if (paa>0) {
      $('.variable_product_photo_img[data-paa=' + paa + ']').attr('src', '/my/img/product.png');
      $('.variable_product_photo[data-paa=' + paa + ']').val('');
      $('.variable_product_photo_reset[data-paa=' + paa + ']').hide();
    }
    //console.log(paa);
  }
  $('.variable_product_photo_reset').click(variable_product_photo_reset_click);
  
  var dialog_variable_photo;
  dialog_variable_photo = $('#dialog_variable_photo').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_variable_photo_cancel",
        text: gks_lang('Άκυρο'),
        icon: "ui-icon-cancel",
        click: function() {
          $( this ).dialog('close');
          $('#dialog_variable_photo_list').html('');
        }
      },
    ],
  });



  
  function variable_product_photo_img_click() {
    paa=parseInt($(this).attr('data-paa'));
    if (isNaN(paa)) paa=0;
    list_paa=parseInt($(this).attr('data-list_paa'));
    if (isNaN(list_paa)) list_paa=0;
    
    
    if (paa>0 || list_paa>0) {
      dialog_variable_photo.gks_paa=paa;
      dialog_variable_photo.gks_list_paa=list_paa;
      
      var html='';
      $('.lightgalleryitem_user').each(function() {
        html+='<img src="' + $(this).find('img').attr('src') + '" class="dialog_variable_photo_img">';
      });
      
      
      
      $('#dialog_variable_photo_list').html(html);
      $('.dialog_variable_photo_img').click(dialog_variable_photo_img_click);
      
      dwidth=$(window).width() * 0.96;
      dheight=$(window).height() * 0.96;
  	  if (dwidth> 800) dwidth=800;
  	  if (dheight> 600) dheight=600;
  	  dialog_variable_photo.dialog('option', 'width', dwidth);
  	  dialog_variable_photo.dialog('option', 'height', dheight);
  	  $('#dialog_variable_photo').parent().css({position:'fixed'});      
      dialog_variable_photo.dialog('open');
          
    }
    //console.log(paa);
  }
  $('.variable_product_photo_img').click(variable_product_photo_img_click);
  
  function dialog_variable_photo_img_click() {
    mysrc=$(this).attr('src');
    if (dialog_variable_photo.gks_paa>0) {
      $('.variable_product_photo_img[data-paa=' + dialog_variable_photo.gks_paa + ']').attr('src', mysrc);
      $('.variable_product_photo[data-paa=' + dialog_variable_photo.gks_paa + ']').val(mysrc);
      $('.variable_product_photo_reset[data-paa=' + dialog_variable_photo.gks_paa + ']').show();
    }
    if (dialog_variable_photo.gks_list_paa>0) {
      $('.variable_product_photo_img_list[data-list_paa=' + dialog_variable_photo.gks_list_paa + ']').attr('src', mysrc);
      //$('.variable_product_photo[data-paa=' + dialog_variable_photo.gks_list_paa + ']').val(mysrc);
      $('.variable_product_photo_reset_list[data-list_paa=' + dialog_variable_photo.gks_list_paa + ']').css('display','');
    }
    
    dialog_variable_photo.dialog('close');
  }
  
  
  var dialog_variable_sindiasmoi;
  dialog_variable_sindiasmoi = $('#dialog_variable_sindiasmoi').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_variable_sindiasmoi_add",
        text: gks_lang('Προσθήκη'),
        icon: "ui-icon-circle-plus",  
        click: function() {
        
          vals=[];
          $('.dialog_variable_sindiasmoi_checkbox:checked').each(function() {
            vals.push(parseInt($(this).val()));
          });
          //console.log(vals);
          if (vals.length<=0) {
            myalert('error:' + gks_lang('Δεν έχετε επιλέξει κάποιον συνδυασμό'));
            return;
          }
          //$('body').addClass("myloading");
          dialog_variable_sindiasmoi.dialog('close');  
          sindiasmoi_add=0;
          for (ij=0; ij < sindoiasmoi.length; ij++) {
            if (vals.includes(ij)) {
              variable_add_item(sindoiasmoi[ij]);
              sindiasmoi_add++;
            }
          }
          
          if (sindiasmoi_add==0) {
            myalert('error:' + gks_lang('Δεν προστέθηκαν παραλλαγές'));
          } else {
            myalert('ok:' + gks_lang('Προστέθηκαν <b>[1]</b> παραλλαγές').replace('[1]',sindiasmoi_add));
          } 
          gks_varibles_summary(); 
          //$('body').removeClass("myloading");       
        }
      },
      {
        id: "dialog_variable_sindiasmoi_cancel",
        text: gks_lang('Άκυρο'),
        icon: "ui-icon-cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
      },
    ],
  });
  
  
  $('#dialog_variable_sindiasmoi_select_all').click(function() {
    $('.dialog_variable_sindiasmoi_checkbox').prop('checked', true);
  });
  $('#dialog_variable_sindiasmoi_select_none').click(function() {
    $('.dialog_variable_sindiasmoi_checkbox').prop('checked', false);
  });
  
  function gks_varibles_summary() {
    
    $('#span_variables_count').html($('.variable_product').length);

    $('.card_header_opoiodipote').removeClass('card_header_opoiodipote');
    
    var count_opoiodipote=0;
    $('.variable_product').each(function() {
      //var has_check_this=false;
      $(this).find('.variables_combo').each(function() {
        //if (has_check_this) return;
        val=$(this).val();
        if (isNaN(val)) val=0;
        if (val==0) {
          count_opoiodipote++;
          $(this).parent().parent().addClass('card_header_opoiodipote');
          return false;
        }
      });
    });
    div_variables_warning='';
    if (count_opoiodipote>0) div_variables_warning=gks_lang('Προσοχή: <b>[1]</b> παραλλαγές έχουν ως τιμή ιδιότητας το <b>Οποιοδήποτε</b>').replace('[1]',count_opoiodipote);
    
    if (div_variables_warning=='') $('#div_variables_warning').hide(); else  $('#div_variables_warning').html(div_variables_warning).show();
    
    
    $('.card_header_mydouble').removeClass('card_header_mydouble');
    
    var mydoubles=[];
    $('.variable_product').each(function() {
      paa=parseInt($(this).attr('data-paa'));
      if (isNaN(paa)) paa=0;
      if (paa>0) {
        var temp='';
        $(this).find('.variables_combo').each(function() {
          temp+=$(this).find('option:selected').text() + '|';
        });
        
        found_double=false;
        for(i=0; i<mydoubles.length; i++) {
          if (mydoubles[i].values == temp) {
            $('.variable_product[data-paa=' + mydoubles[i].paa + '] .card-header').addClass('card_header_mydouble');
            found_double=true;
          }
        }
        if (found_double) {
          $(this).find('.card-header').addClass('card_header_mydouble');
        }
        mydoubles.push({paa:paa,values:temp});
      }
    });
    
    div_variables_danger=$('.card_header_mydouble').length;
    if (div_variables_danger==0) $('#div_variables_danger').hide(); else $('#div_variables_danger').html(gks_lang('Προσοχή: <b>[1]</b> παραλλαγές έχουν τον ίδιο συνδυασμό ιδιοτήτων').replace('[1]',div_variables_danger)).show();
    
    
    //console.log(mydoubles);  
          //card_header_mydouble
  } 
  gks_varibles_summary();
  
  function variable_product_price_yperx_sale_dates_change() {
    paa=parseInt($(this).attr('data-paa'));
    if (isNaN(paa)) paa=0;
    if (paa<=0) return;
    if ($(this).is(':checked')) {
      $('.variable_div_product_price_yperx_sale_dates[data-paa=' + paa + ']').show();
    } else {
      $('.variable_div_product_price_yperx_sale_dates[data-paa=' + paa + ']').hide();
    }
  }
  $('.variable_product_price_yperx_sale_dates').change(variable_product_price_yperx_sale_dates_change);  
  
  function variable_product_price_sale_dates_change() {
    paa=parseInt($(this).attr('data-paa'));
    if (isNaN(paa)) paa=0;
    if (paa<=0) return;
    if ($(this).is(':checked')) {
      $('.variable_div_product_price_sale_dates[data-paa=' + paa + ']').show();
    } else {
      $('.variable_div_product_price_sale_dates[data-paa=' + paa + ']').hide();
    }  
  }
  $('.variable_product_price_sale_dates').change(variable_product_price_sale_dates_change);
  
  function variable_product_price_retail_sale_dates_change() {
    paa=parseInt($(this).attr('data-paa'));
    if (isNaN(paa)) paa=0;
    if (paa<=0) return;
    if ($(this).is(':checked')) {
      $('.variable_div_product_price_retail_sale_dates[data-paa=' + paa + ']').show();
    } else {
      $('.variable_div_product_price_retail_sale_dates[data-paa=' + paa + ']').hide();
    }
  }
  $('.variable_product_price_retail_sale_dates').change(variable_product_price_retail_sale_dates_change);  

  function variable_product_price_plist_sale_dates_change() {
    paa=parseInt($(this).attr('data-paa'));
    if (isNaN(paa)) paa=0;
    if (paa<=0) return;
    plist_id=$(this).attr('data-plist_id');
    if ($(this).is(':checked')) {
      $('.variable_div_product_price_plist_sale_dates'+plist_id+'[data-paa=' + paa + ']').show();
    } else {
      $('.variable_div_product_price_plist_sale_dates'+plist_id+'[data-paa=' + paa + ']').hide();
    }
  }
  $('.variable_product_price_plist_sale_dates').change(variable_product_price_plist_sale_dates_change);  





  $('.variable_product_price_yperx_sale_from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    } 
  }));
  $('.variable_product_price_yperx_sale_to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));
  $('.variable_product_price_sale_from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));
  $('.variable_product_price_sale_to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));  
  $('.variable_product_price_retail_sale_from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    } 
  }));
  $('.variable_product_price_retail_sale_to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));

  $('.variable_product_price_plist_sale_from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    } 
  }));
  $('.variable_product_price_plist_sale_to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));
  
  var dialog_variable_prices;
  dialog_variable_prices = $('#dialog_variable_prices').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_variable_prices_ok",
        text: gks_lang('Ορισμός'),
        icon: "ui-icon-circle-check",
        click: function() {
          
          gsel='';
          if (dialog_variable_prices.gks_mtype=='yperxondriki') {
            gsel='_yperx';
          } else if (dialog_variable_prices.gks_mtype=='xondriki') {
            gsel='';
          } else if (dialog_variable_prices.gks_mtype=='lianiki') {
            gsel='_retail';
          } else if (dialog_variable_prices.gks_mtype=='pricelist') {
            gsel='_plist';
          }
          

          
          if (gsel!='_plist') {
            if ($('#enable_variable_product_price_dialog').is(':checked')) { //timi xondrikhw lianikis
              if ($('#variable_product_price_dialog_type1').is(':checked')) { //timi
                $('.variable_product_price' + gsel).val($('#variable_product_price_dialog').val());
              } else {
                var prosimo=1;
                if ($('#variable_product_price_dialog_type2').is(':checked')) { //afksisi
                  prosimo=1;
                } else if ($('#variable_product_price_dialog_type3').is(':checked')) { //meiosi
                  prosimo=-1;
                }
                var user_val=parseFloat($('#variable_product_price_dialog').val());
                if (isNaN(user_val)) user_val=0;
                if ($('#variable_product_price_dialog_type_poso1').is(':checked')) { //poso
                  $('.variable_product_price' + gsel).each(function() {
                    exist_val=parseFloat($(this).val());
                    if (isNaN(exist_val)) exist_val=0;
                    val=(exist_val + prosimo*user_val).myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
                    if (val<0) val=0;
                    $(this).val(val);
                  });
                } else if ($('#variable_product_price_dialog_type_poso2').is(':checked')) { //pososto
                  user_val=1+prosimo*(user_val/100);
                  $('.variable_product_price' + gsel).each(function() {
                    exist_val=parseFloat($(this).val());
                    if (isNaN(exist_val)) exist_val=0;
                    val=(exist_val*user_val).myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
                    if (val<0) val=0;
                    $(this).val(val);
                  });
                }
              }
            }
              
            if ($('#enable_variable_product_price_sale_dialog').is(':checked')) { //prosofra
              if ($('#variable_product_price_sale_dialog_type1').is(':checked')) { //timi
                $('.variable_product_price' + gsel + '_sale').val($('#variable_product_price_sale_dialog').val());
              } else {
                var prosimo=1;
                if ($('#variable_product_price_sale_dialog_type2').is(':checked')) { //afksisi
                  prosimo=1;
                } else if ($('#variable_product_price_sale_dialog_type3').is(':checked')) { //meiosi
                  prosimo=-1;
                }
                var user_val=parseFloat($('#variable_product_price_sale_dialog').val());
                if (isNaN(user_val)) user_val=0;
                if ($('#variable_product_price_sale_dialog_type_poso1').is(':checked')) { //poso
                  $('.variable_product_price' + gsel + '_sale').each(function() {
                    exist_val=parseFloat($(this).val());
                    if (isNaN(exist_val)) exist_val=0;
                    val=(exist_val + prosimo*user_val).myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
                    if (val<0) val=0;
                    $(this).val(val);
                  });
                } else if ($('#variable_product_price_sale_dialog_type_poso2').is(':checked')) { //pososto
                  user_val=1+prosimo*(user_val/100);
                  $('.variable_product_price' + gsel + '_sale').each(function() {
                    exist_val=parseFloat($(this).val());
                    if (isNaN(exist_val)) exist_val=0;
                    val=(exist_val*user_val).myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
                    if (val<0) val=0;
                    $(this).val(val);
                  });
                }
              }
            }
            
            if ($('#enable_variable_product_price_include_vat_dialog').is(':checked')) {//periexei fpa
              var is_on=$('#variable_product_price_include_vat_dialog').is(':checked');
              $('.variable_product_price' + gsel + '_include_vat').each(function() {
                curr_val=$(this).is(':checked');
                if (is_on!=curr_val) $(this).click();
              });
            }
            
            if ($('#enable_variable_product_price_sale_dates_dialog').is(':checked')) {//.is(':checked')) {//
              if ($('#variable_product_price_sale_dates_dialog').is(':checked')) {
                $('.variable_product_price' + gsel + '_sale_dates').each(function() {
                  curr_val=$(this).is(':checked');
                  if (curr_val==false) $(this).click();  //set to checked              
                });
                
                if ($('#enable_variable_product_price_sale_from_dialog').is(':checked')) { //apo
                  var curr_val=$('#variable_product_price_sale_from_dialog').val();
                  $('.variable_product_price' + gsel + '_sale_from').each(function() {
                    $(this).val(curr_val);
                  });
                }
                if ($('#enable_variable_product_price_sale_to_dialog').is(':checked')) { //apo
                  var curr_val=$('#variable_product_price_sale_to_dialog').val();
                  $('.variable_product_price' + gsel + '_sale_to').each(function() {
                    $(this).val(curr_val);
                  });
                }
                              
              } else {
                $('.variable_product_price' + gsel + '_sale_dates').each(function() {
                  curr_val=$(this).is(':checked');
                  if (curr_val) $(this).click();  //set to unchecked              
                });
              }
            
            }
            
            if ($('#enable_variable_product_price_sheets_formula_dialog').is(':checked')) { //tipos ypologismou temaxiou
              var curr_val=$('#variable_product_price_sheets_formula_dialog').val();
              $('.variable_product_price' + gsel + '_sheets_formula').each(function() {
                $(this).val(curr_val);
              });
            }
            if ($('#enable_variable_product_price_quantity_formula_dialog').is(':checked')) { //tipos ypologismou sinolou
              var curr_val=$('#variable_product_price_quantity_formula_dialog').val();
              $('.variable_product_price' + gsel + '_quantity_formula').each(function() {
                $(this).val(curr_val);
              });
            }
          
          } else if (gsel=='_plist') {
            var paa_list=[];
            $('.variable_product').each(function() {
              paa=parseInt($(this).attr('data-paa'));
              if (isNaN(paa)) paa=0;
              if (paa>0) paa_list.push(paa);
            });             
            plist_id='_'+pricelists[dialog_variable_prices.gks_pricelist_index].id;

            if ($('#enable_variable_product_price_dialog').is(':checked')) { //timi 
              if ($('#variable_product_price_dialog_type1').is(':checked')) { //timi
                for(paaa=0;paaa<paa_list.length;paaa++) {
                  sss=plist_id+'_'+paa_list[paaa];
                  $('#variable_product_price_plist'+sss).val($('#variable_product_price_dialog').val());
                }
              } else {
                var prosimo=1;
                if ($('#variable_product_price_dialog_type2').is(':checked')) { //afksisi
                  prosimo=1;
                } else if ($('#variable_product_price_dialog_type3').is(':checked')) { //meiosi
                  prosimo=-1;
                }
                var user_val=parseFloat($('#variable_product_price_dialog').val());
                if (isNaN(user_val)) user_val=0;
                if ($('#variable_product_price_dialog_type_poso1').is(':checked')) { //poso
                  for(paaa=0;paaa<paa_list.length;paaa++) {
                    sss=plist_id+'_'+paa_list[paaa];                  
                    elem=$('#variable_product_price_plist'+sss);
                    exist_val=parseFloat(elem.val());
                    if (isNaN(exist_val)) exist_val=0;
                    val=(exist_val + prosimo*user_val).myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
                    if (val<0) val=0;
                    elem.val(val);
                  }
                } else if ($('#variable_product_price_dialog_type_poso2').is(':checked')) { //pososto
                  user_val=1+prosimo*(user_val/100);
                  for(paaa=0;paaa<paa_list.length;paaa++) {
                    sss=plist_id+'_'+paa_list[paaa];                  
                    elem=$('#variable_product_price_plist'+sss);
                    exist_val=parseFloat(elem.val());
                    if (isNaN(exist_val)) exist_val=0;
                    val=(exist_val*user_val).myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
                    if (val<0) val=0;
                    elem.val(val);
                  }
                }
              }
            }
              
            if ($('#enable_variable_product_price_sale_dialog').is(':checked')) { //prosofra
              if ($('#variable_product_price_sale_dialog_type1').is(':checked')) { //timi
                for(paaa=0;paaa<paa_list.length;paaa++) {
                  sss=plist_id+'_'+paa_list[paaa];
                  $('#variable_product_price_plist_sale'+sss).val($('#variable_product_price_sale_dialog').val());
                }
              } else {
                var prosimo=1;
                if ($('#variable_product_price_sale_dialog_type2').is(':checked')) { //afksisi
                  prosimo=1;
                } else if ($('#variable_product_price_sale_dialog_type3').is(':checked')) { //meiosi
                  prosimo=-1;
                }
                var user_val=parseFloat($('#variable_product_price_sale_dialog').val());
                if (isNaN(user_val)) user_val=0;
                if ($('#variable_product_price_sale_dialog_type_poso1').is(':checked')) { //poso
                  for(paaa=0;paaa<paa_list.length;paaa++) {
                    sss=plist_id+'_'+paa_list[paaa];                  
                    elem=$('#variable_product_price_plist_sale'+sss);
                    exist_val=parseFloat(elem.val());
                    if (isNaN(exist_val)) exist_val=0;
                    val=(exist_val + prosimo*user_val).myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
                    if (val<0) val=0;
                    elem.val(val);
                  }
                } else if ($('#variable_product_price_sale_dialog_type_poso2').is(':checked')) { //pososto
                  user_val=1+prosimo*(user_val/100);
                  for(paaa=0;paaa<paa_list.length;paaa++) {
                    sss=plist_id+'_'+paa_list[paaa];                  
                    elem=$('#variable_product_price_plist_sale'+sss);
                    exist_val=parseFloat(elem.val());
                    if (isNaN(exist_val)) exist_val=0;
                    val=(exist_val*user_val).myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
                    if (val<0) val=0;
                    elem.val(val);
                  }
                }
              }
            }
            
            if ($('#enable_variable_product_price_include_vat_dialog').is(':checked')) {//periexei fpa
              var is_on=$('#variable_product_price_include_vat_dialog').is(':checked');
              for(paaa=0;paaa<paa_list.length;paaa++) {
                sss=plist_id+'_'+paa_list[paaa];                  
                elem=$('#variable_product_price_plist_include_vat'+sss);
                curr_val=elem.is(':checked');
                if (is_on!=curr_val) elem.click();
              }
            }
            
            if ($('#enable_variable_product_price_sale_dates_dialog').is(':checked')) {//.is(':checked')) {//
              if ($('#variable_product_price_sale_dates_dialog').is(':checked')) {
                for(paaa=0;paaa<paa_list.length;paaa++) {
                  sss=plist_id+'_'+paa_list[paaa];                  
                  elem=$('#variable_product_price_plist_sale_dates'+sss);
                  curr_val=elem.is(':checked');
                  if (curr_val==false) elem.click();  //set to checked              
                }
                
                if ($('#enable_variable_product_price_sale_from_dialog').is(':checked')) { //apo
                  var curr_val=$('#variable_product_price_sale_from_dialog').val();
                  for(paaa=0;paaa<paa_list.length;paaa++) {
                    sss=plist_id+'_'+paa_list[paaa];                  
                    elem=$('#variable_product_price_plist_sale_from'+sss);
                    elem.val(curr_val);
                  }
                }
                if ($('#enable_variable_product_price_sale_to_dialog').is(':checked')) { //eos
                  var curr_val=$('#variable_product_price_sale_to_dialog').val();
                  for(paaa=0;paaa<paa_list.length;paaa++) {
                    sss=plist_id+'_'+paa_list[paaa];                  
                    elem=$('#variable_product_price_plist_sale_to'+sss);
                    elem.val(curr_val);
                  }
                }
              } else {
                for(paaa=0;paaa<paa_list.length;paaa++) {
                  sss=plist_id+'_'+paa_list[paaa];                  
                  elem=$('#variable_product_price_plist_sale_dates'+sss);
                  curr_val=elem.is(':checked');
                  if (curr_val) elem.click();  //set to unchecked              
                }
              }
            
            }
            
            if ($('#enable_variable_product_price_sheets_formula_dialog').is(':checked')) { //tipos ypologismou temaxiou
              var curr_val=$('#variable_product_price_sheets_formula_dialog').val();
              for(paaa=0;paaa<paa_list.length;paaa++) {
                sss=plist_id+'_'+paa_list[paaa];                  
                elem=$('#variable_product_price_plist_sheets_formula'+sss);
                elem.val(curr_val);
              }
            }
            if ($('#enable_variable_product_price_quantity_formula_dialog').is(':checked')) { //tipos ypologismou sinolou
              var curr_val=$('#variable_product_price_quantity_formula_dialog').val();
              for(paaa=0;paaa<paa_list.length;paaa++) {
                sss=plist_id+'_'+paa_list[paaa];         
                elem=$('#variable_product_price_plist_quantity_formula'+sss);         
                elem.val(curr_val);
              }
            }          
          }
          
        
          $(this).dialog('close');
          
        }
      },
      {
        id: "dialog_variable_prices_cancel",
        text: gks_lang('Άκυρο'),
        icon: "ui-icon-cancel",
        click: function() {
          $(this).dialog('close');
        }
      },
    ],
  });

  var dialog_variable_prices_open_startup_hasrun=false;
  
  function dialog_variable_prices_open(mtype,pricelist_index=0) {
    $('#dialog_variable_prices_mtype').html('');
  	if (mtype=='yperxondriki') $('#dialog_variable_prices_mtype').html(gks_lang('ΥπερΧoνδρική'));
  	if (mtype=='xondriki') $('#dialog_variable_prices_mtype').html(gks_lang('Χoνδρική'));
  	if (mtype=='lianiki') $('#dialog_variable_prices_mtype').html(gks_lang('Λιανική'));
  	if (mtype=='pricelist') {
  	  if (pricelist_index<0) return;
  	  $('#dialog_variable_prices_mtype').html(pricelists[pricelist_index].descr);
  	}

    if (dialog_variable_prices_open_startup_hasrun==false) {
      dialog_variable_prices_open_startup_hasrun=true;
      
      $('#enable_variable_product_price_dialog').prop('checked',false);
      $('#variable_product_price_dialog').prop('disabled', true).val('');
      $('#div_variable_product_price_dialog_type').hide();
      $('#variable_product_price_dialog_type1').prop('checked',true);
      $('#div_variable_product_price_dialog_type_poso').hide();
      $('#variable_product_price_dialog_type_poso1').prop('checked',true);
  
      $('#enable_variable_product_price_include_vat_dialog').prop('checked',false);
      if ($('#variable_product_price_include_vat_dialog').is(':checked')) $('#variable_product_price_include_vat_dialog').click();
      variable_product_price_include_vat_dialog_sw.disable();
      
      $('#enable_variable_product_price_sale_dialog').prop('checked',false);
      $('#variable_product_price_sale_dialog').prop('disabled', true).val('');
      $('#div_variable_product_price_sale_dialog_type').hide();
      $('#variable_product_price_sale_dialog_type1').prop('checked',true);
      $('#div_variable_product_price_sale_dialog_type_poso').hide();
      $('#variable_product_price_sale_dialog_type_poso1').prop('checked',true);

      
      $('#enable_variable_product_price_sale_dates_dialog').prop('checked',false);
      if ($('#variable_product_price_sale_dates_dialog').is(':checked')) $('#variable_product_price_sale_dates_dialog').click();
      variable_product_price_sale_dates_dialog_sw.disable();
  
  
  
      $('#variable_div_product_price_sale_dates_dialog').hide();
      $('#enable_variable_product_price_sale_from_dialog').prop('checked',false);
      $('#variable_product_price_sale_from_dialog').prop('disabled', true).val(''); //datetimepicker({'value':null});
      $('#enable_variable_product_price_sale_to_dialog').prop('checked',false);
      $('#variable_product_price_sale_to_dialog').prop('disabled', true).val(''); //
  
  
      $('#enable_variable_product_price_sheets_formula_dialog').prop('checked',false);
      $('#variable_product_price_sheets_formula_dialog').prop('disabled', true).val('');
      $('#enable_variable_product_price_quantity_formula_dialog').prop('checked',false);
      $('#variable_product_price_quantity_formula_dialog').prop('disabled', true).val('');
    }

    dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 800) dwidth=800;
	  if (dheight> 600) dheight=600;
	  dialog_variable_prices.gks_mtype=mtype;
	  dialog_variable_prices.gks_pricelist_index=pricelist_index;
	  dialog_variable_prices.dialog('option', 'width', dwidth);
	  dialog_variable_prices.dialog('option', 'height', dheight);
	  $('#dialog_variable_prices').parent().css({position:'fixed'});      
    dialog_variable_prices.dialog('open');

  }

  var variable_product_price_include_vat_dialog_sw = new Switchery(document.querySelector('#variable_product_price_include_vat_dialog'),gks_switchery_defaults());
  var variable_product_price_sale_dates_dialog_sw = new Switchery(document.querySelector('#variable_product_price_sale_dates_dialog'),gks_switchery_defaults());


  $('#variable_product_price_sale_from_dialog').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,}));
  $('#variable_product_price_sale_to_dialog').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,}));


  $('#enable_variable_product_price_dialog').change(function() {
    if ($(this).is(':checked')) {
      $('#variable_product_price_dialog').prop('disabled', false); 
      $('#div_variable_product_price_dialog_type').show();
      $('#variable_product_price_dialog_type1').prop('checked',true);
      $('#div_variable_product_price_dialog_type_poso').hide();      
    } else {
      $('#variable_product_price_dialog').prop('disabled', true);
      $('#div_variable_product_price_dialog_type').hide();
      $('#div_variable_product_price_dialog_type_poso').hide();
    }
  });
  $('input[name=variable_product_price_dialog_type]').change(function() {
    val=$('input[name=variable_product_price_dialog_type]:checked').val();
    //console.log(val);
    if (val==1) {
      $('#div_variable_product_price_dialog_type_poso').hide();
    } else if (val==2) {
      $('#div_variable_product_price_dialog_type_poso').show();
    } else if (val==3) {
      $('#div_variable_product_price_dialog_type_poso').show();
    }
  });
  $('#enable_variable_product_price_include_vat_dialog').change(function() {
    if ($(this).is(':checked')) variable_product_price_include_vat_dialog_sw.enable(); else variable_product_price_include_vat_dialog_sw.disable();
  });


  $('#enable_variable_product_price_sale_dialog').change(function() {
    if ($(this).is(':checked')) {
      $('#variable_product_price_sale_dialog').prop('disabled', false); 
      $('#div_variable_product_price_sale_dialog_type').show();
      $('#variable_product_price_sale_dialog_type1').prop('checked',true);
      $('#div_variable_product_price_sale_dialog_type_poso').hide();      
    } else {
      $('#variable_product_price_sale_dialog').prop('disabled', true);
      $('#div_variable_product_price_sale_dialog_type').hide();
      $('#div_variable_product_price_sale_dialog_type_poso').hide();
    }
  });
  $('input[name=variable_product_price_sale_dialog_type]').change(function() {
    val=$('input[name=variable_product_price_sale_dialog_type]:checked').val();
    //console.log(val);
    if (val==1) {
      $('#div_variable_product_price_sale_dialog_type_poso').hide();
    } else if (val==2) {
      $('#div_variable_product_price_sale_dialog_type_poso').show();
    } else if (val==3) {
      $('#div_variable_product_price_sale_dialog_type_poso').show();
    }
  });

  $('#enable_variable_product_price_sale_dates_dialog').change(function() {
    if ($(this).is(':checked')) {
      variable_product_price_sale_dates_dialog_sw.enable(); 
      if ($('#variable_product_price_sale_dates_dialog').is(':checked')) {
        $('#variable_div_product_price_sale_dates_dialog').show();
      } else {
        $('#variable_div_product_price_sale_dates_dialog').hide();
      }
    } else {
      variable_product_price_sale_dates_dialog_sw.disable();
      $('#variable_div_product_price_sale_dates_dialog').hide();
    }
  });

  $('#variable_product_price_sale_dates_dialog').change(function() {
    if ($(this).is(':checked')) {
      $('#variable_div_product_price_sale_dates_dialog').show();
    } else {
      $('#variable_div_product_price_sale_dates_dialog').hide();
    }  
  });

  $('#enable_variable_product_price_sale_from_dialog').change(function() {
    if ($(this).is(':checked')) {
      $('#variable_product_price_sale_from_dialog').prop('disabled',false);
    } else {
      $('#variable_product_price_sale_from_dialog').prop('disabled',true);
    }  
  });

  $('#enable_variable_product_price_sale_to_dialog').change(function() {
    if ($(this).is(':checked')) {
      $('#variable_product_price_sale_to_dialog').prop('disabled',false);
    } else {
      $('#variable_product_price_sale_to_dialog').prop('disabled',true);
    }  
  });

  $('#enable_variable_product_price_sheets_formula_dialog').change(function() {
    if ($(this).is(':checked')) {
      $('#variable_product_price_sheets_formula_dialog').prop('disabled',false);
    } else {
      $('#variable_product_price_sheets_formula_dialog').prop('disabled',true);
    }  
  });

  $('#enable_variable_product_price_quantity_formula_dialog').change(function() {
    if ($(this).is(':checked')) {
      $('#variable_product_price_quantity_formula_dialog').prop('disabled',false);
    } else {
      $('#variable_product_price_quantity_formula_dialog').prop('disabled',true);
    }  
  });







  var dialog_variable_apostoli;
  dialog_variable_apostoli = $('#dialog_variable_apostoli').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_variable_prices_ok",
        text: gks_lang('Ορισμός'),
        icon: "ui-icon-circle-check",
        click: function() {
          if ($('#enable_product_varos_dialog').is(':checked')) {
            var user_val=$('#product_varos_dialog').val();
            $('.variable_product_varos').each(function() {
              $(this).val(user_val);
            });
          }
          if ($('#enable_product_ogos_x_dialog').is(':checked')) {
            var user_val=$('#product_ogos_x_dialog').val();
            $('.variable_product_ogos_x').each(function() {
              $(this).val(user_val);
            });
          }
          if ($('#enable_product_ogos_y_dialog').is(':checked')) {
            var user_val=$('#product_ogos_y_dialog').val();
            $('.variable_product_ogos_y').each(function() {
              $(this).val(user_val);
            });
          }
          if ($('#enable_product_ogos_z_dialog').is(':checked')) {
            var user_val=$('#product_ogos_z_dialog').val();
            $('.variable_product_ogos_z').each(function() {
              $(this).val(user_val);
            });
          }
          
          $(this).dialog('close');
        }
      },
      {
        id: "dialog_variable_prices_cancel",
        text: gks_lang('Άκυρο'),
        icon: "ui-icon-cancel",
        click: function() {
          $(this).dialog('close');
        }
      },
    ],
  });

  function dialog_variable_apostoli_open() {

    dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 800) dwidth=800;
	  if (dheight> 400) dheight=400;

	  dialog_variable_apostoli.dialog('option', 'width', dwidth);
	  dialog_variable_apostoli.dialog('option', 'height', dheight);
	  $('#dialog_variable_apostoli').parent().css({position:'fixed'});      
    dialog_variable_apostoli.dialog('open');

  }

  $('#enable_product_varos_dialog').change(function() {
    if ($(this).is(':checked')) {
      $('#product_varos_dialog').prop('disabled',false);
    } else {
      $('#product_varos_dialog').prop('disabled',true);
    }  
  });
  $('#enable_product_ogos_x_dialog').change(function() {
    if ($(this).is(':checked')) {
      $('#product_ogos_x_dialog').prop('disabled',false);
    } else {
      $('#product_ogos_x_dialog').prop('disabled',true);
    }  
  });
  $('#enable_product_ogos_y_dialog').change(function() {
    if ($(this).is(':checked')) {
      $('#product_ogos_y_dialog').prop('disabled',false);
    } else {
      $('#product_ogos_y_dialog').prop('disabled',true);
    }  
  });
  $('#enable_product_ogos_z_dialog').change(function() {
    if ($(this).is(':checked')) {
      $('#product_ogos_z_dialog').prop('disabled',false);
    } else {
      $('#product_ogos_z_dialog').prop('disabled',true);
    }  
  });






  var dialog_variable_fpa;
  dialog_variable_fpa = $('#dialog_variable_fpa').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_variable_prices_ok",
        text: gks_lang('Ορισμός'),
        icon: "ui-icon-circle-check",
        click: function() {
          var user_val=$('#product_fpa_base_id_dialog').val();
          $('.variable_product_fpa_base_id').each(function() {
            $(this).val(user_val);
          });
          $(this).dialog('close');
        }
      },
      {
        id: "dialog_variable_prices_cancel",
        text: gks_lang('Άκυρο'),
        icon: "ui-icon-cancel",
        click: function() {
          $(this).dialog('close');
        }
      },
    ],
  });

  function dialog_variable_fpa_open() {

    dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 350) dwidth=350;
	  if (dheight> 300) dheight=300;

	  dialog_variable_fpa.dialog('option', 'width', dwidth);
	  dialog_variable_fpa.dialog('option', 'height', dheight);
	  $('#dialog_variable_fpa').parent().css({position:'fixed'});      
    dialog_variable_fpa.dialog('open');

  }




  function dialog_variable_copy_all_open() {
    myconfirm(gks_lang('Σίγουρα θέλετε να αντιγράψετε τις παραμέτρους από το βασικό είδος σε όλες τις παραλλαγές'),'dialog_variable_copy_all_open_run');
  }

  window.dialog_variable_copy_all_open_run = function() {
  

    gsel=[];gsel.push('_yperx');gsel.push('');gsel.push('_retail');
    for(i=0;i<=gsel.length-1;i++) {
      $('.variable_product_price' + gsel[i]).val($('#product_price' + gsel[i]).val());
      $('.variable_product_price' + gsel[i] + '_sale').val($('#product_price' + gsel[i] + '_sale').val());

      var is_on=$('#product_price' + gsel[i] + '_include_vat').is(':checked');
      $('.variable_product_price' + gsel[i] + '_include_vat').each(function() {
        curr_val=$(this).is(':checked');
        if (is_on!=curr_val) $(this).click();
      });
      if ($('#product_price' + gsel[i] + '_sale_dates').is(':checked')) {
        $('.variable_product_price' + gsel[i] + '_sale_dates').each(function() {
          curr_val=$(this).is(':checked');
          if (curr_val==false) $(this).click();  //set to checked              
        });
        
        var curr_val=$('#product_price' + gsel[i] + '_sale_from').val();
        $('.variable_product_price' + gsel[i] + '_sale_from').each(function() {
          $(this).val(curr_val);
        });
        var curr_val=$('#product_price' + gsel[i] + '_sale_to').val();
        $('.variable_product_price' + gsel[i] + '_sale_to').each(function() {
          $(this).val(curr_val);
        });
      } else {
        $('.variable_product_price' + gsel[i] + '_sale_dates').each(function() {
          curr_val=$(this).is(':checked');
          if (curr_val) $(this).click();  //set to unchecked              
        });
      }
      $('.variable_product_price' + gsel[i] + '_sheets_formula').val($('#product_price' + gsel[i] + '_sheets_formula').val());
      $('.variable_product_price' + gsel[i] + '_quantity_formula').val($('#product_price' + gsel[i] + '_quantity_formula').val());
      
    }

    if (pricelists.length>0) {
      var paa_list=[];
      $('.variable_product').each(function() {
        paa=parseInt($(this).attr('data-paa'));
        if (isNaN(paa)) paa=0;
        if (paa>0) paa_list.push(paa);
      });
      for(plid=0;plid<pricelists.length;plid++) {
        plist_id='_'+pricelists[plid].id;
        val1=$('#product_price_plist'+plist_id).val();
        val1_sale=$('#product_price_plist_sale'+plist_id).val();
        is_on=$('#product_price_plist_include_vat'+plist_id).is(':checked');
        sale_dates_checked=$('#product_price_plist_sale_dates'+plist_id).is(':checked');
        curr_val_from=$('#product_price_plist_sale_from'+plist_id).val();
        curr_val_to=$('#product_price_plist_sale_to'+plist_id).val();        
        for(paaa=0;paaa<paa_list.length;paaa++) {
          sss=plist_id+'_'+paa_list[paaa];
          
          $('#variable_product_price_plist'+sss).val(val1);
          $('#variable_product_price_plist_sale'+sss).val(val1_sale);
          
          curr_val=$('#variable_product_price_plist_include_vat'+sss).is(':checked');
          if (is_on!=curr_val) $('#variable_product_price_plist_include_vat'+sss).click();

          if (sale_dates_checked) {
            curr_val=$('#variable_product_price_plist_sale_dates'+sss).is(':checked');
            if (curr_val==false) $('#variable_product_price_plist_sale_dates'+sss).click();  //set to checked              
            $('#variable_product_price_plist_sale_from'+sss).val(curr_val_from);
            $('#variable_product_price_plist_sale_to'+sss).val(curr_val_to);
          } else {
            curr_val=$('#variable_product_price_plist_sale_dates'+sss).is(':checked');
            if (curr_val) $('#variable_product_price_plist_sale_dates'+sss).click(); //set to unchecked   
          }
          $('#variable_product_price_plist_sheets_formula'+sss).val($('#product_price_plist_sheets_formula'+plist_id).val());
          $('#variable_product_price_plist_quantity_formula'+sss).val($('#product_price_plist_quantity_formula'+plist_id).val());
        }
      }
    }
         
          
    var user_val=$('#product_varos').val();
    $('.variable_product_varos').each(function() {
      $(this).val(user_val);
    });
    var user_val=$('#product_ogos_x').val();
    $('.variable_product_ogos_x').each(function() {
      $(this).val(user_val);
    });
    var user_val=$('#product_ogos_y').val();
    $('.variable_product_ogos_y').each(function() {
      $(this).val(user_val);
    });
    var user_val=$('#product_ogos_z').val();
    $('.variable_product_ogos_z').each(function() {
      $(this).val(user_val);
    });
    
    var user_val=$('#product_fpa_base_id').val();
    $('.variable_product_fpa_base_id').each(function() {
      $(this).val(user_val);
    });
    
    //console.log('end');
  }
  
  function dialog_variable_removeall() {
    myconfirm(gks_lang('Σίγουρα θέλετε να αφαιρέσετε όλες τις παραλλαγές'),'dialog_variable_removeall_run');
  }
  window.dialog_variable_removeall_run = function() {
    $('.variable_product').remove();
    gks_varibles_summary();
  }  
  
  
  if (from_php_get_variable_item>0) {
    newurl=window.location.href.replace('&variable_item=' + from_php_get_variable_item,'');
		window.history.pushState({}, window.document.title, newurl);
    
    $('.variable_product[data-pid=' + from_php_get_variable_item + ']').css('background-color','rgba(135,206,250,1)');
    //$([document.documentElement, document.body]).animate({
    $([document.documentElement]).animate({
        scrollTop: $('.variable_product[data-pid=' + from_php_get_variable_item + ']').offset().top -100,
    }, 2000, 'swing', function() {
      $('.variable_product[data-pid=' + from_php_get_variable_item + ']').animate({
        backgroundColor:'rgba(135,206,250,0)'
      }, 2000, 'swing', function() {
        $('.variable_product[data-pid=' + from_php_get_variable_item + ']').css('background-color','');
      })
    });
  }
  
  
  function set_bom_kostos_click() {
    val=parseFloat($(this).attr('data-val'));
    if (isNaN(val)) val=0;
    if (val>0) {
      $('#' + $(this).attr('data-to')).val(val);
    }
  }
  
  $('.set_bom_kostos').click(set_bom_kostos_click);
  
  
  $('#div_variables_list').sortable({
    items: '.variable_product',
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-paa'});
      eidi_table_sortable_after(mylist);
      
    },
    start: function(e, ui){
        $(this).find('.variable_product_descr_small').each(function(){
            $(this).val(tinyMCE.get($(this).attr('id')).getContent());
            tinyMCE.execCommand( 'mceRemoveEditor', false, $(this).attr('id') );
        });
    },
    stop: function(e,ui) {
      gks_tinymce_init('.variable_product_descr_small'); 
        //$(this).find('.variable_product_descr_small').each(function(){
        //    tinyMCE.execCommand( 'mceAddControl', true, $(this).attr('id') );
        //    $(this).sortable("refresh");
        //});
    },
  });
  
  function eidi_table_sortable_after(mylist) {
    //console.log(mylist);
    $('#div_variables_list > .variable_product').each(function() {
      paa=$(this).attr('data-paa');
      $(this).attr('data-paa_temp',paa);
    });
    $('#div_variables_list > .variable_product').each(function() {
      paa=$(this).attr('data-paa_temp');
      new_paa=-1;
      for(i=0;i<mylist.length;i++) {
        if (mylist[i]==paa) {
          new_paa=i;break;
        }
      }
      //console.log('new_aa',new_aa);
      if (new_paa>=0) {
        new_paa++
        $(this).attr('data-paa',new_paa);
        $(this).find('*[data-paa=' + paa + ']').attr('data-paa',new_paa);
      }
      
    })      
  }
  
  function product_def_comments_change() {gks_resize_textarea($(this));}
  $('#product_def_comments').on(mychange, product_def_comments_change);
  gks_resize_textarea($('#product_def_comments'));

  $('.variable_product_def_comments').on(mychange, product_def_comments_change);
  $('.variable_product_def_comments').each(function() {
    gks_resize_textarea($(this));
  });
  
  $('.gks_lang_data_obj_input_textarea_variable').on(mychange, product_def_comments_change);
  $('.gks_lang_data_obj_input_textarea_variable').each(function() {
    gks_resize_textarea($(this));
  });
  



  function internal_note_change() {gks_resize_textarea($(this));}
  $('#internal_note').on(mychange, internal_note_change);
  gks_resize_textarea($('#internal_note'));



  $('#def_supplier').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-user.php?pro=1',
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
      $("#def_supplier").attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        need_save=true;
        $("#def_supplier").attr('data-id','0').val('');           
      }
    }
  });    


  var dialog_variable_list;
  dialog_variable_list = $('#dialog_variable_list').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_variable_list_ok",
        text: gks_lang('Ορισμός'),
        icon: "ui-icon-circle-check",
        click: function() {
          $('.def_column_show_check').each(function() {
            i=parseInt($(this).attr('id').replaceAll('def_column_show_','')); if (isNaN(i)) i=0;
            val=$(this).prop('checked');
            def_column_show[i]=val;
          });
          $('.dialog_variable_list_table_th').each(function() {
            data_cid=parseInt($(this).attr('data-cid')); if (isNaN(data_cid)) data_cid=0;
            cid_width=$(this).css('width');cid_width=parseInt(cid_width.replaceAll('px',''));
            def_column_width[data_cid]=cid_width;
          });
          
          
          for (i=2; i<=31; i++) {
            if (def_column_show[i]) {
              switch (i) {
                case 2:
                  $('.variable_product_photo_img_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) {
                      mysrc=$(this).attr('src');
                      if (mysrc=='/my/img/product.png') {
                        $('.variable_product_photo_img[data-paa=' + list_paa + ']').attr('src' ,mysrc);
                        $('.variable_product_photo[data-paa=' + list_paa + ']').val('');
                        $('.variable_product_photo_reset[data-paa=' + list_paa + ']').hide();
                        
                      } else {
                        $('.variable_product_photo_img[data-paa=' + list_paa + ']').attr('src' ,mysrc);
                        $('.variable_product_photo[data-paa=' + list_paa + ']').val(mysrc);
                        $('.variable_product_photo_reset[data-paa=' + list_paa + ']').show();
                      }
                    }
                  });
                  break;
                case 3:
                  $('.variable_product_code_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_code[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                case 4:
                  $('.variable_product_descr_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_descr[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                case 5:
                  $('.variable_product_sku_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_sku[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                case 6:
                  $('.variable_product_gtin_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_gtin[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                case 7:
                  $('.variable_product_upc_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_upc[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                case 8:
                  $('.variable_product_ean_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_ean[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                case 9:
                  $('.variable_product_isbn_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_isbn[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                case 10:
                  $('.variable_product_taric_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_taric[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                  
                case 32:
                  $('.variable_product_price_yperx_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_price_yperx[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                case 33:
                  $('.variable_product_price_yperx_include_vat_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) {
                      elem=$('.variable_product_price_yperx_include_vat[data-paa=' + list_paa + ']');
                      if ($(this).is(':checked')) {
                        if (elem.is(':checked')==false) elem.click(); 
                      } else {
                        if (elem.is(':checked')==true) elem.click(); 
                      }
                    }
                   });
                  break;
                case 34:
                  $('.variable_product_price_yperx_sale_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_price_yperx_sale[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                case 35:
                  $('.variable_product_price_yperx_sale_dates_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) {
                      elem=$('.variable_product_price_yperx_sale_dates[data-paa=' + list_paa + ']');
                      if ($(this).is(':checked')) {
                        if (elem.is(':checked')==false) elem.click(); 
                      } else {
                        if (elem.is(':checked')==true) elem.click(); 
                      }
                    }
                   });
                  break;
                case 36:
                  $('.variable_product_price_yperx_sale_from_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) {
                      val = $(this).val(); if (val=='__/__/____ __:__') val='';
                      $('.variable_product_price_yperx_sale_from[data-paa=' + list_paa + ']').val(val);
                    }
                  });
                  break;
                case 37:
                  $('.variable_product_price_yperx_sale_to_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) {
                      val = $(this).val(); if (val=='__/__/____ __:__') val='';
                      $('.variable_product_price_yperx_sale_to[data-paa=' + list_paa + ']').val(val);
                    }
                  });
                  break;
                case 38:
                  $('.variable_product_price_yperx_sheets_formula_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_price_yperx_sheets_formula[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                case 39:
                  $('.variable_product_price_yperx_quantity_formula_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_price_yperx_quantity_formula[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;

                  
                case 11:
                  $('.variable_product_price_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_price[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                case 12:
                  $('.variable_product_price_include_vat_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) {
                      elem=$('.variable_product_price_include_vat[data-paa=' + list_paa + ']');
                      if ($(this).is(':checked')) {
                        if (elem.is(':checked')==false) elem.click(); 
                      } else {
                        if (elem.is(':checked')==true) elem.click(); 
                      }
                    }
                   });
                  break;
                case 13:
                  $('.variable_product_price_sale_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_price_sale[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                case 14:
                  $('.variable_product_price_sale_dates_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) {
                      elem=$('.variable_product_price_sale_dates[data-paa=' + list_paa + ']');
                      if ($(this).is(':checked')) {
                        if (elem.is(':checked')==false) elem.click(); 
                      } else {
                        if (elem.is(':checked')==true) elem.click(); 
                      }
                    }
                   });
                  break;
                case 15:
                  $('.variable_product_price_sale_from_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) {
                      val = $(this).val(); if (val=='__/__/____ __:__') val='';
                      $('.variable_product_price_sale_from[data-paa=' + list_paa + ']').val(val);
                    }
                  });
                  break;
                case 16:
                  $('.variable_product_price_sale_to_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) {
                      val = $(this).val(); if (val=='__/__/____ __:__') val='';
                      $('.variable_product_price_sale_to[data-paa=' + list_paa + ']').val(val);
                    }
                  });
                  break;
                case 17:
                  $('.variable_product_price_sheets_formula_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_price_sheets_formula[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                case 18:
                  $('.variable_product_price_quantity_formula_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_price_quantity_formula[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                  
                  
                case 19:
                  $('.variable_product_price_retail_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_price_retail[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                case 20:
                  $('.variable_product_price_retail_include_vat_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) {
                      elem=$('.variable_product_price_retail_include_vat[data-paa=' + list_paa + ']');
                      if ($(this).is(':checked')) {
                        if (elem.is(':checked')==false) elem.click(); 
                      } else {
                        if (elem.is(':checked')==true) elem.click(); 
                      }
                    }
                   });
                  break;
                case 21:
                  $('.variable_product_price_retail_sale_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_price_retail_sale[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                case 22:
                  $('.variable_product_price_retail_sale_dates_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) {
                      elem=$('.variable_product_price_retail_sale_dates[data-paa=' + list_paa + ']');
                      if ($(this).is(':checked')) {
                        if (elem.is(':checked')==false) elem.click(); 
                      } else {
                        if (elem.is(':checked')==true) elem.click(); 
                      }
                    }
                   });
                  break;
                case 23:
                  $('.variable_product_price_retail_sale_from_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) {
                      val = $(this).val(); if (val=='__/__/____ __:__') val='';
                      $('.variable_product_price_retail_sale_from[data-paa=' + list_paa + ']').val(val);
                    }
                  });
                  break;
                case 24:
                  $('.variable_product_price_retail_sale_to_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) {
                      val = $(this).val(); if (val=='__/__/____ __:__') val='';
                      $('.variable_product_price_retail_sale_to[data-paa=' + list_paa + ']').val(val);
                    }
                  });
                  break;
                case 25:
                  $('.variable_product_price_retail_sheets_formula_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_price_retail_sheets_formula[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                case 26:
                  $('.variable_product_price_retail_quantity_formula_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_price_retail_quantity_formula[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;                  
                  
                  
                case 27:
                  $('.variable_product_kostos_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_kostos[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                case 28:
                  $('.variable_product_varos_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_varos[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                case 29:
                  $('.variable_product_ogos_x_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_ogos_x[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  $('.variable_product_ogos_y_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_ogos_y[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  $('.variable_product_ogos_z_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_ogos_z[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                case 30:
                  $('.variable_product_fpa_base_id_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_product_fpa_base_id[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                case 31:
                  $('.variable_min_quantity_alert_list').each(function() {
                    list_paa=parseInt($(this).attr('data-list_paa')); if (isNaN(list_paa)) list_paa=0;
                    if (list_paa>0) $('.variable_min_quantity_alert[data-paa=' + list_paa + ']').val($(this).val());
                  });
                  break;
                default:
              }
            }
          }
          
          
          //console.log(def_column_width);
          //console.log(def_column_show);
          need_save=true;
          $('#dialog_variable_list_table').html('');
          $(this).dialog('close');
        }
      },
      {
        id: "dialog_variable_list_cancel",
        text: gks_lang('Άκυρο'),
        icon: "ui-icon-cancel",
        click: function() {
          $('#dialog_variable_list_table').html('');
          $(this).dialog('close');
        }
      },
    ],
  });

  $('#variable_list').click(function() {
    $('body').addClass("myloading");
    //console.log((new Date()).getTime());
    setTimeout(function(){variable_list_run(); }, 100);
    
  });

  var set_datetime_36=false;
  var set_datetime_37=false;
  var set_datetime_15=false;
  var set_datetime_16=false;
  var set_datetime_23=false;
  var set_datetime_24=false;
      
  function variable_list_run() {
    //console.log((new Date()).getTime());
    if (typeof def_column_show[0] == 'undefined')  def_column_show[0]=true;
    if (typeof def_column_show[1] == 'undefined')  def_column_show[1]=true; 
    if (typeof def_column_show[2] == 'undefined')  def_column_show[2]=true;  
    if (typeof def_column_show[3] == 'undefined')  def_column_show[3]=true; 
    if (typeof def_column_show[4] == 'undefined')  def_column_show[4]=true; 
    if (typeof def_column_show[5] == 'undefined')  def_column_show[5]=true; 
    if (typeof def_column_show[6] == 'undefined')  def_column_show[6]=true;  
    if (typeof def_column_show[7] == 'undefined')  def_column_show[7]=true;  
    if (typeof def_column_show[8] == 'undefined')  def_column_show[8]=true;  
    if (typeof def_column_show[9] == 'undefined')  def_column_show[9]=true;  
    if (typeof def_column_show[10] == 'undefined') def_column_show[10]=true;
    if (typeof def_column_show[11] == 'undefined') def_column_show[11]=true;
    if (typeof def_column_show[12] == 'undefined') def_column_show[12]=true;
    if (typeof def_column_show[13] == 'undefined') def_column_show[13]=true;
    if (typeof def_column_show[14] == 'undefined') def_column_show[14]=true; 
    if (typeof def_column_show[15] == 'undefined') def_column_show[15]=true; 
    if (typeof def_column_show[16] == 'undefined') def_column_show[16]=true; 
    if (typeof def_column_show[17] == 'undefined') def_column_show[17]=true; 
    if (typeof def_column_show[18] == 'undefined') def_column_show[18]=true;
    if (typeof def_column_show[19] == 'undefined') def_column_show[19]=true;
    if (typeof def_column_show[20] == 'undefined') def_column_show[20]=true;
    if (typeof def_column_show[21] == 'undefined') def_column_show[21]=true;
    if (typeof def_column_show[22] == 'undefined') def_column_show[22]=true; 
    if (typeof def_column_show[23] == 'undefined') def_column_show[23]=true; 
    if (typeof def_column_show[24] == 'undefined') def_column_show[24]=true;
    if (typeof def_column_show[25] == 'undefined') def_column_show[25]=true;
    if (typeof def_column_show[26] == 'undefined') def_column_show[26]=true; 
    if (typeof def_column_show[27] == 'undefined') def_column_show[27]=true; 
    if (typeof def_column_show[28] == 'undefined') def_column_show[28]=true; 
    if (typeof def_column_show[29] == 'undefined') def_column_show[29]=true; 
    if (typeof def_column_show[30] == 'undefined') def_column_show[30]=true; 
    if (typeof def_column_show[31] == 'undefined') def_column_show[31]=true; 
    if (typeof def_column_show[32] == 'undefined') def_column_show[32]=true; 
    if (typeof def_column_show[33] == 'undefined') def_column_show[33]=true; 
    if (typeof def_column_show[34] == 'undefined') def_column_show[34]=true; 
    if (typeof def_column_show[35] == 'undefined') def_column_show[35]=true; 
    if (typeof def_column_show[36] == 'undefined') def_column_show[36]=true; 
    if (typeof def_column_show[37] == 'undefined') def_column_show[37]=true; 
    if (typeof def_column_show[38] == 'undefined') def_column_show[38]=true; 
    if (typeof def_column_show[39] == 'undefined') def_column_show[39]=true; 

    for (i=0; i<=39;i++) {
      if (def_column_show[i]) $('#def_column_show_' + i).prop('checked',true); else $('#def_column_show_' + i).prop('checked',false);  
    }
    
    
    if (typeof def_column_width[0]  == 'undefined') def_column_width[0]=26;
    if (typeof def_column_width[1]  == 'undefined') def_column_width[1]=100; 
    if (typeof def_column_width[2]  == 'undefined') def_column_width[2]=19;  
    if (typeof def_column_width[3]  == 'undefined') def_column_width[3]=150; 
    if (typeof def_column_width[4]  == 'undefined') def_column_width[4]=250; 
    if (typeof def_column_width[5]  == 'undefined') def_column_width[5]=150; 
    if (typeof def_column_width[6]  == 'undefined') def_column_width[6]=150; 
    if (typeof def_column_width[7]  == 'undefined') def_column_width[7]=150; 
    if (typeof def_column_width[8]  == 'undefined') def_column_width[8]=150; 
    if (typeof def_column_width[9]  == 'undefined') def_column_width[9]=150; 
    if (typeof def_column_width[10] == 'undefined') def_column_width[10]=150; 

    if (typeof def_column_width[32] == 'undefined') def_column_width[32]=110;  
    if (typeof def_column_width[33] == 'undefined') def_column_width[33]=50;  
    if (typeof def_column_width[34] == 'undefined') def_column_width[34]=110;  
    if (typeof def_column_width[35] == 'undefined') def_column_width[35]=80;  
    if (typeof def_column_width[36] == 'undefined') def_column_width[36]=130;
    if (typeof def_column_width[37] == 'undefined') def_column_width[37]=130;
    if (typeof def_column_width[38] == 'undefined') def_column_width[38]=450;
    if (typeof def_column_width[39] == 'undefined') def_column_width[39]=450;

    if (typeof def_column_width[11] == 'undefined') def_column_width[11]=70;  
    if (typeof def_column_width[12] == 'undefined') def_column_width[12]=50;  
    if (typeof def_column_width[13] == 'undefined') def_column_width[13]=70;  
    if (typeof def_column_width[14] == 'undefined') def_column_width[14]=80;  
    if (typeof def_column_width[15] == 'undefined') def_column_width[15]=130;
    if (typeof def_column_width[16] == 'undefined') def_column_width[16]=130;
    if (typeof def_column_width[17] == 'undefined') def_column_width[17]=450;
    if (typeof def_column_width[18] == 'undefined') def_column_width[18]=450;
    if (typeof def_column_width[19] == 'undefined') def_column_width[19]=70; 
    if (typeof def_column_width[20] == 'undefined') def_column_width[20]=50; 
    if (typeof def_column_width[21] == 'undefined') def_column_width[21]=70; 
    if (typeof def_column_width[22] == 'undefined') def_column_width[22]=80; 
    if (typeof def_column_width[23] == 'undefined') def_column_width[23]=130;
    if (typeof def_column_width[24] == 'undefined') def_column_width[24]=130;
    if (typeof def_column_width[25] == 'undefined') def_column_width[25]=450;
    if (typeof def_column_width[26] == 'undefined') def_column_width[26]=450;
    if (typeof def_column_width[27] == 'undefined') def_column_width[27]=70; 
    if (typeof def_column_width[28] == 'undefined') def_column_width[28]=70; 
    if (typeof def_column_width[29] == 'undefined') def_column_width[29]=200;
    if (typeof def_column_width[30] == 'undefined') def_column_width[30]=130;
    if (typeof def_column_width[31] == 'undefined') def_column_width[31]=70; 
    
    
    
    var_column_order[0] = 0;
    var_column_order[1] = 1;
    var_column_order[2] = 2;
    var_column_order[3] = 3;
    var_column_order[4] = 4;
    var_column_order[5] = 5;
    var_column_order[6] = 6;
    var_column_order[7] = 7;
    var_column_order[8] = 8;
    var_column_order[9] = 9;
    var_column_order[10]=10;
    var_column_order[11]=27;
    var_column_order[12]=32;
    var_column_order[13]=33;
    var_column_order[14]=34;
    var_column_order[15]=35;
    var_column_order[16]=36;
    var_column_order[17]=37;
    var_column_order[18]=38;
    var_column_order[19]=39;
    var_column_order[20]=11;
    var_column_order[21]=12;
    var_column_order[22]=13;
    var_column_order[23]=14;
    var_column_order[24]=15;
    var_column_order[25]=16;
    var_column_order[26]=17;
    var_column_order[27]=18;
    var_column_order[28]=19;
    var_column_order[29]=20;
    var_column_order[30]=21;
    var_column_order[31]=22;
    var_column_order[32]=23;
    var_column_order[33]=24;
    var_column_order[34]=25;
    var_column_order[35]=26;
    var_column_order[36]=28;
    var_column_order[37]=29;
    var_column_order[38]=30;
    var_column_order[39]=31;    
    
    $('#dialog_variable_list_table').html('');
    var listhtml='';
    listhtml+='<table id="dialog_variable_list_table_elem" class="table table-sm table-responsive1 table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="0" align="left">';
    listhtml+='<thead>';
    listhtml+='<tr>';
    listhtml+='<th data-cid="0"  class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[0] ==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="0"  style="width:' + def_column_width[0]  + 'px;">#</div></th>';
    listhtml+='<th data-cid="1"  class="table-dark" scope="col" style="text-align: left   !important;vertical-align: middle;' + (def_column_show[1] ==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="1"  style="width:' + def_column_width[1]  + 'px;">' + gks_lang('Παραλλαγή') + '</div></th>';
    listhtml+='<th data-cid="2"  class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[2] ==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="2"  style="width:' + def_column_width[2]  + 'px;">' + gks_lang('Φωτό') + '</div></th>';
    listhtml+='<th data-cid="3"  class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[3] ==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="3"  style="width:' + def_column_width[3]  + 'px;">' + gks_lang('Κωδικός') + '</div></th>';
    listhtml+='<th data-cid="4"  class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[4] ==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="4"  style="width:' + def_column_width[4]  + 'px;">' + gks_lang('Περιγραφή') + '</div></th>';
    listhtml+='<th data-cid="5"  class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[5] ==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="5"  style="width:' + def_column_width[5]  + 'px;">' + gks_lang('SKU') + '</div></th>';
    listhtml+='<th data-cid="6"  class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[6] ==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="6"  style="width:' + def_column_width[6]  + 'px;">' + gks_lang('GTIN') + '</div></th>';
    listhtml+='<th data-cid="7"  class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[7] ==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="7"  style="width:' + def_column_width[7]  + 'px;">' + gks_lang('UPC') + '</div></th>';
    listhtml+='<th data-cid="8"  class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[8] ==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="8"  style="width:' + def_column_width[8]  + 'px;">' + gks_lang('EAN') + '</div></th>';
    listhtml+='<th data-cid="9"  class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[9] ==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="9"  style="width:' + def_column_width[9]  + 'px;">' + gks_lang('ISBN') + '</div></th>';
    listhtml+='<th data-cid="10" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[10]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="10" style="width:' + def_column_width[10] + 'px;">' + gks_lang('Taric No') + '</div></th>';

    listhtml+='<th data-cid="27" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[27]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="27" style="width:' + def_column_width[27] + 'px;">' + gks_lang('Κόστος') + '</div></th>';

    listhtml+='<th data-cid="32" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[32]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="32" style="width:' + def_column_width[32] + 'px;">' + gks_lang('Τιμή ΥπερΧονδρικής') + '</div></th>';
    listhtml+='<th data-cid="33" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[33]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="33" style="width:' + def_column_width[33] + 'px;">' + gks_lang('Περιέχει ΦΠΑ') + '</div></th>';
    listhtml+='<th data-cid="34" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[34]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="34" style="width:' + def_column_width[34] + 'px;">' + gks_lang('Προσφορά ΥπερΧονδρικής') + '</div></th>';
    listhtml+='<th data-cid="35" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[35]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="35" style="width:' + def_column_width[35] + 'px;">' + gks_lang('Ημερομηνίες Προσφοράς ΥπερΧονδρικής') + '</div></th>';
    listhtml+='<th data-cid="36" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[36]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="36" style="width:' + def_column_width[36] + 'px;">' + gks_lang('Από') + '</div></th>';
    listhtml+='<th data-cid="37" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[37]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="37" style="width:' + def_column_width[37] + 'px;">' + gks_lang('Έως') + '</div></th>';
    listhtml+='<th data-cid="38" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[38]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="38" style="width:' + def_column_width[38] + 'px;">' + gks_lang('Τύπος υπολογισμού τεμαχίου ΥπερΧονδρικής') + '</div></th>';
    listhtml+='<th data-cid="39" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[39]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="39" style="width:' + def_column_width[39] + 'px;">' + gks_lang('Τύπος υπολογισμού συνόλου ΥπερΧονδρικής') + '</div></th>';


    listhtml+='<th data-cid="11" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[11]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="11" style="width:' + def_column_width[11] + 'px;">' + gks_lang('Τιμή Χονδρικής') + '</div></th>';
    listhtml+='<th data-cid="12" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[12]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="12" style="width:' + def_column_width[12] + 'px;">' + gks_lang('Περιέχει ΦΠΑ') + '</div></th>';
    listhtml+='<th data-cid="13" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[13]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="13" style="width:' + def_column_width[13] + 'px;">' + gks_lang('Προσφορά Χονδρικής') + '</div></th>';
    listhtml+='<th data-cid="14" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[14]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="14" style="width:' + def_column_width[14] + 'px;">' + gks_lang('Ημερομηνίες Προσφοράς Χονδρικής') + '</div></th>';
    listhtml+='<th data-cid="15" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[15]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="15" style="width:' + def_column_width[15] + 'px;">' + gks_lang('Από') + '</div></th>';
    listhtml+='<th data-cid="16" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[16]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="16" style="width:' + def_column_width[16] + 'px;">' + gks_lang('Έως') + '</div></th>';
    listhtml+='<th data-cid="17" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[17]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="17" style="width:' + def_column_width[17] + 'px;">' + gks_lang('Τύπος υπολογισμού τεμαχίου Χονδρικής') + '</div></th>';
    listhtml+='<th data-cid="18" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[18]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="18" style="width:' + def_column_width[18] + 'px;">' + gks_lang('Τύπος υπολογισμού συνόλου Χονδρικής') + '</div></th>';
    
    listhtml+='<th data-cid="19" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[19]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="19" style="width:' + def_column_width[19] + 'px;">' + gks_lang('Τιμή Λιανικής') + '</div></th>';
    listhtml+='<th data-cid="20" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[20]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="20" style="width:' + def_column_width[20] + 'px;">' + gks_lang('Περιέχει ΦΠΑ') + '</div></th>';
    listhtml+='<th data-cid="21" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[21]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="21" style="width:' + def_column_width[21] + 'px;">' + gks_lang('Προσφορά Λιανικής') + '</div></th>';
    listhtml+='<th data-cid="22" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[22]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="22" style="width:' + def_column_width[22] + 'px;">' + gks_lang('Ημερομηνίες Προσφοράς Λιανικής') + '</div></th>';
    listhtml+='<th data-cid="23" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[23]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="23" style="width:' + def_column_width[23] + 'px;">' + gks_lang('Από') + '</div></th>';
    listhtml+='<th data-cid="24" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[24]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="24" style="width:' + def_column_width[24] + 'px;">' + gks_lang('Έως') + '</div></th>';
    listhtml+='<th data-cid="25" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[25]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="25" style="width:' + def_column_width[25] + 'px;">' + gks_lang('Τύπος υπολογισμού τεμαχίου Λιανικής') + '</div></th>';
    listhtml+='<th data-cid="26" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[26]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="26" style="width:' + def_column_width[26] + 'px;">' + gks_lang('Τύπος υπολογισμού συνόλου Λιανικής') + '</div></th>';
    listhtml+='<th data-cid="28" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[28]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="28" style="width:' + def_column_width[28] + 'px;">' + gks_lang('Βάρος σε gr') + '</div></th>';
    listhtml+='<th data-cid="29" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[29]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="29" style="width:' + def_column_width[29] + 'px;">' + gks_lang('Διαστάσεις σε cm') + '</div></th>';
    listhtml+='<th data-cid="30" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[30]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="30" style="width:' + def_column_width[30] + 'px;">' + gks_lang('ΦΠΑ') + '</div></th>';
    listhtml+='<th data-cid="31" class="table-dark" scope="col" style="text-align: center !important;vertical-align: middle;' + (def_column_show[31]==false ? 'display:none;' : '') + '"  width="0%"><div class="dialog_variable_list_table_th" data-cid="31" style="width:' + def_column_width[31] + 'px;">' + gks_lang('Όριο αποθέματος') + '</div></th>';
    listhtml+='</tr>';
    listhtml+='</thead>';
    listhtml+='<tbody>';
    
    var select_
    var vp_cc=0;
    $('.variable_product').each(function() {
      paa=parseInt($(this).attr('data-paa'));
      if (isNaN(paa)) paa=0;
      if (paa>0) {
        vp_cc++;
        
        listhtml+='<tr class="dialog_variable_list_table_list_item" data-list_paa="' + paa + '">';
        listhtml+='<td data-cid="0" style="' + (def_column_show[0] ==false ? 'display:none;' : '') + '" class="mytdcm" scope="row" nowrap>' + vp_cc + '</td>';
        var paralagi=[];
        $(this).find('.variables_combo option:selected').each(function() {
          paralagi.push($(this).text()); 
        });
        listhtml+='<td data-cid="1"  style="' + (def_column_show[1]  ==false ? 'display:none;' : '') + '" class="mytdcml" nowrap>' + paralagi.join(' ') + '</td>';
        
        src_list=$(this).find('img.variable_product_photo_img').attr('src');
        listhtml+='<td data-cid="2"  style="' + (def_column_show[2]  ==false ? 'display:none;' : '') + 'position: relative;" class="mytdcm td_variable_product_photo_img_list" nowrap>' + 
          '<img data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" class="variable_product_photo_img_list dialog_variable_list_table_photo_img" src="' + $(this).find('img.variable_product_photo_img').attr('src') + '">' +
          '<img data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" src="/my/img/0.png" class="variable_product_photo_reset_list" title="' + gks_lang('Αφαίρεση') + '" style="' + (src_list=='/my/img/product.png' ? 'display:none;' : '')  + '">' +
        '</td>';
        
        listhtml+='<td data-cid="3"  style="' + (def_column_show[3]  ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="text"     value="' + $(this).find('.variable_product_code').val() + '" class="variable_product_code_list form-control form-control-sm dialog_variable_list_table_text"></td>';
        listhtml+='<td data-cid="4"  style="' + (def_column_show[4]  ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="text"     value="' + $(this).find('.variable_product_descr').val() + '" class="variable_product_descr_list form-control form-control-sm dialog_variable_list_table_text"></td>';
        listhtml+='<td data-cid="5"  style="' + (def_column_show[5]  ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="text"     value="' + $(this).find('.variable_product_sku').val() + '" class="variable_product_sku_list form-control form-control-sm dialog_variable_list_table_text"></td>';
        listhtml+='<td data-cid="6"  style="' + (def_column_show[6]  ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="text"     value="' + $(this).find('.variable_product_gtin').val() + '" class="variable_product_gtin_list form-control form-control-sm dialog_variable_list_table_text"></td>';
        listhtml+='<td data-cid="7"  style="' + (def_column_show[7]  ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="text"     value="' + $(this).find('.variable_product_upc').val() + '" class="variable_product_upc_list form-control form-control-sm dialog_variable_list_table_text"></td>';
        listhtml+='<td data-cid="8"  style="' + (def_column_show[8]  ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="text"     value="' + $(this).find('.variable_product_ean').val() + '" class="variable_product_ean_list form-control form-control-sm dialog_variable_list_table_text"></td>';
        listhtml+='<td data-cid="9"  style="' + (def_column_show[9]  ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="text"     value="' + $(this).find('.variable_product_isbn').val() + '" class="variable_product_isbn_list form-control form-control-sm dialog_variable_list_table_text"></td>';
        listhtml+='<td data-cid="10" style="' + (def_column_show[10] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="text"     value="' + $(this).find('.variable_product_taric').val() + '" class="variable_product_taric_list form-control form-control-sm dialog_variable_list_table_text"></td>';
        listhtml+='<td data-cid="27" style="' + (def_column_show[27] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="number"   value="' + $(this).find('.variable_product_kostos').val() + '" class="variable_product_kostos_list form-control form-control-sm dialog_variable_list_table_number" min="0" step="' + from_php_GKS_INPUT_STEP_AJIA + '"></td>';
        
        listhtml+='<td data-cid="32" style="' + (def_column_show[32] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="number"   value="' + $(this).find('.variable_product_price_yperx').val() + '" class="variable_product_price_yperx_list form-control form-control-sm dialog_variable_list_table_number" min="0" step="' + from_php_GKS_INPUT_STEP_AJIA + '"></td>';
        listhtml+='<td data-cid="33" style="' + (def_column_show[33] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="checkbox" value="1" ' + ($(this).find('.variable_product_price_yperx_include_vat').is(':checked') ? 'checked' : '') + ' class="variable_product_price_yperx_include_vat_list dialog_variable_list_table_checkbox"></td>';
        listhtml+='<td data-cid="34" style="' + (def_column_show[34] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="number"   value="' + $(this).find('.variable_product_price_yperx_sale').val() + '" class="variable_product_price_yperx_sale_list form-control form-control-sm dialog_variable_list_table_number" min="0" step="' + from_php_GKS_INPUT_STEP_AJIA + '"></td>';
        has_dates=$(this).find('.variable_product_price_yperx_sale_dates').is(':checked');
        listhtml+='<td data-cid="35" style="' + (def_column_show[35] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="checkbox" value="1" ' + (has_dates ? 'checked' : '') + ' class="variable_product_price_yperx_sale_dates_list dialog_variable_list_table_checkbox"></td>';
        listhtml+='<td data-cid="36" style="' + (def_column_show[36] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="text"     value="' + $(this).find('.variable_product_price_yperx_sale_from').val() + '" class="variable_product_price_yperx_sale_from_list form-control form-control-sm dialog_variable_list_table_text" style="' + (has_dates ? '' : 'display:none;') + '"></td>';
        listhtml+='<td data-cid="37" style="' + (def_column_show[37] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="text"     value="' + $(this).find('.variable_product_price_yperx_sale_to').val() + '" class="variable_product_price_yperx_sale_to_list form-control form-control-sm dialog_variable_list_table_text" style="' + (has_dates ? '' : 'display:none;') + '" ></td>';
        listhtml+='<td data-cid="38" style="' + (def_column_show[38] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="text"     value="' + $(this).find('.variable_product_price_yperx_sheets_formula').val() + '" class="variable_product_price_yperx_sheets_formula_list form-control form-control-sm dialog_variable_list_table_text"></td>';
        listhtml+='<td data-cid="39" style="' + (def_column_show[39] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="text"     value="' + $(this).find('.variable_product_price_yperx_quantity_formula').val() + '" class="variable_product_price_yperx_quantity_formula_list form-control form-control-sm dialog_variable_list_table_text"></td>';

        
        listhtml+='<td data-cid="11" style="' + (def_column_show[11] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="number"   value="' + $(this).find('.variable_product_price').val() + '" class="variable_product_price_list form-control form-control-sm dialog_variable_list_table_number" min="0" step="' + from_php_GKS_INPUT_STEP_AJIA + '"></td>';
        listhtml+='<td data-cid="12" style="' + (def_column_show[12] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="checkbox" value="1" ' + ($(this).find('.variable_product_price_include_vat').is(':checked') ? 'checked' : '') + ' class="variable_product_price_include_vat_list dialog_variable_list_table_checkbox"></td>';
        listhtml+='<td data-cid="13" style="' + (def_column_show[13] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="number"   value="' + $(this).find('.variable_product_price_sale').val() + '" class="variable_product_price_sale_list form-control form-control-sm dialog_variable_list_table_number" min="0" step="' + from_php_GKS_INPUT_STEP_AJIA + '"></td>';
        has_dates=$(this).find('.variable_product_price_sale_dates').is(':checked');
        listhtml+='<td data-cid="14" style="' + (def_column_show[14] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="checkbox" value="1" ' + (has_dates ? 'checked' : '') + ' class="variable_product_price_sale_dates_list dialog_variable_list_table_checkbox"></td>';
        listhtml+='<td data-cid="15" style="' + (def_column_show[15] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="text"     value="' + $(this).find('.variable_product_price_sale_from').val() + '" class="variable_product_price_sale_from_list form-control form-control-sm dialog_variable_list_table_text" style="' + (has_dates ? '' : 'display:none;') + '"></td>';
        listhtml+='<td data-cid="16" style="' + (def_column_show[16] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="text"     value="' + $(this).find('.variable_product_price_sale_to').val() + '" class="variable_product_price_sale_to_list form-control form-control-sm dialog_variable_list_table_text" style="' + (has_dates ? '' : 'display:none;') + '"></td>';
        listhtml+='<td data-cid="17" style="' + (def_column_show[17] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="text"     value="' + $(this).find('.variable_product_price_sheets_formula').val() + '" class="variable_product_price_sheets_formula_list form-control form-control-sm dialog_variable_list_table_text"></td>';
        listhtml+='<td data-cid="18" style="' + (def_column_show[18] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="text"     value="' + $(this).find('.variable_product_price_quantity_formula').val() + '" class="variable_product_price_quantity_formula_list form-control form-control-sm dialog_variable_list_table_text"></td>';
        
        listhtml+='<td data-cid="19" style="' + (def_column_show[19] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="number"   value="' + $(this).find('.variable_product_price_retail').val() + '" class="variable_product_price_retail_list form-control form-control-sm dialog_variable_list_table_number" min="0" step="' + from_php_GKS_INPUT_STEP_AJIA + '"></td>';
        listhtml+='<td data-cid="20" style="' + (def_column_show[20] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="checkbox" value="1" ' + ($(this).find('.variable_product_price_retail_include_vat').is(':checked') ? 'checked' : '') + ' class="variable_product_price_retail_include_vat_list dialog_variable_list_table_checkbox"></td>';
        listhtml+='<td data-cid="21" style="' + (def_column_show[21] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="number"   value="' + $(this).find('.variable_product_price_retail_sale').val() + '" class="variable_product_price_retail_sale_list form-control form-control-sm dialog_variable_list_table_number" min="0" step="' + from_php_GKS_INPUT_STEP_AJIA + '"></td>';
        has_dates=$(this).find('.variable_product_price_retail_sale_dates').is(':checked');
        listhtml+='<td data-cid="22" style="' + (def_column_show[22] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="checkbox" value="1" ' + (has_dates ? 'checked' : '') + ' class="variable_product_price_retail_sale_dates_list dialog_variable_list_table_checkbox"></td>';
        listhtml+='<td data-cid="23" style="' + (def_column_show[23] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="text"     value="' + $(this).find('.variable_product_price_retail_sale_from').val() + '" class="variable_product_price_retail_sale_from_list form-control form-control-sm dialog_variable_list_table_text" style="' + (has_dates ? '' : 'display:none;') + '"></td>';
        listhtml+='<td data-cid="24" style="' + (def_column_show[24] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="text"     value="' + $(this).find('.variable_product_price_retail_sale_to').val() + '" class="variable_product_price_retail_sale_to_list form-control form-control-sm dialog_variable_list_table_text" style="' + (has_dates ? '' : 'display:none;') + '" ></td>';
        listhtml+='<td data-cid="25" style="' + (def_column_show[25] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="text"     value="' + $(this).find('.variable_product_price_retail_sheets_formula').val() + '" class="variable_product_price_retail_sheets_formula_list form-control form-control-sm dialog_variable_list_table_text"></td>';
        listhtml+='<td data-cid="26" style="' + (def_column_show[26] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="text"     value="' + $(this).find('.variable_product_price_retail_quantity_formula').val() + '" class="variable_product_price_retail_quantity_formula_list form-control form-control-sm dialog_variable_list_table_text"></td>';
        
        listhtml+='<td data-cid="28" style="' + (def_column_show[28] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="number"   value="' + $(this).find('.variable_product_varos').val() + '" class="variable_product_varos_list form-control form-control-sm dialog_variable_list_table_number" min=0 step="0.01"></td>';

        listhtml+='<td data-cid="29" style="' + (def_column_show[29] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap>' + 
          '<input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="number" value="' + $(this).find('.variable_product_ogos_x').val() + '" class="variable_product_ogos_x_list form-control form-control-sm dialog_variable_list_table_number3" min=0 step="0.01">' +
          '<input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="number" value="' + $(this).find('.variable_product_ogos_y').val() + '" class="variable_product_ogos_y_list form-control form-control-sm dialog_variable_list_table_number3" min=0 step="0.01">' +
          '<input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="number" value="' + $(this).find('.variable_product_ogos_z').val() + '" class="variable_product_ogos_z_list form-control form-control-sm dialog_variable_list_table_number3" min=0 step="0.01">' +
          '</td>';

        listhtml+='<td data-cid="30" style="' + (def_column_show[30] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap>' +
          '<select data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" class="variable_product_fpa_base_id_list form-control form-control-sm">'
          $(this).find('.variable_product_fpa_base_id option').each(function() {
            listhtml+='<option value="' + $(this).attr('value') + '"' +
            ($(this).prop('selected') ? ' selected ' : '') +
            '>' + $(this).html() + '</option>';
          });
          listhtml+='</select></td>';
          
        listhtml+='<td data-cid="31" style="' + (def_column_show[31] ==false ? 'display:none;' : '') + '" class="mytdcm" nowrap><input data-list_paa="' + paa + '" data-vp_cc="' + vp_cc + '" type="number"   value="' + $(this).find('.variable_min_quantity_alert').val() + '" class="variable_min_quantity_alert_list form-control form-control-sm dialog_variable_list_table_number" min=0 step="' + from_php_GKS_INPUT_STEP_POSOTITA + '"></td>';
        
        
        
        listhtml+='</tr>';
        
        
      }
      
    });
    
    
    listhtml+='</tbody>';
    listhtml+='</table>';
    
    $('#dialog_variable_list_table').html(listhtml);
    
    $('.variable_product_price_yperx_sale_dates_list').change(function() {
      if ($(this).is(':checked')) {
        $(this).parent().parent().find('.variable_product_price_yperx_sale_from_list').show();
        $(this).parent().parent().find('.variable_product_price_yperx_sale_to_list').show();
      } else {
        $(this).parent().parent().find('.variable_product_price_yperx_sale_from_list').hide();
        $(this).parent().parent().find('.variable_product_price_yperx_sale_to_list').hide();
      }
    });
    $('.variable_product_price_sale_dates_list').change(function() {
      if ($(this).is(':checked')) {
        $(this).parent().parent().find('.variable_product_price_sale_from_list').show();
        $(this).parent().parent().find('.variable_product_price_sale_to_list').show();
      } else {
        $(this).parent().parent().find('.variable_product_price_sale_from_list').hide();
        $(this).parent().parent().find('.variable_product_price_sale_to_list').hide();
      }
    });
    $('.variable_product_price_retail_sale_dates_list').change(function() {
      if ($(this).is(':checked')) {
        $(this).parent().parent().find('.variable_product_price_retail_sale_from_list').show();
        $(this).parent().parent().find('.variable_product_price_retail_sale_to_list').show();
      } else {
        $(this).parent().parent().find('.variable_product_price_retail_sale_from_list').hide();
        $(this).parent().parent().find('.variable_product_price_retail_sale_to_list').hide();
      }
    });
    
    
    
    set_datetime_36=false;
    set_datetime_37=false;
    set_datetime_15=false;
    set_datetime_16=false;
    set_datetime_23=false;
    set_datetime_24=false;
    //console.log((new Date()).getTime());
    if (def_column_show[36]==true) {
      set_datetime_36=true;
      $('.variable_product_price_yperx_sale_from_list').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1}));
    }
    if (def_column_show[37]==true) {
      set_datetime_37=true;
      $('.variable_product_price_yperx_sale_to_list').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1}));
    }
    if (def_column_show[15]==true) {
      set_datetime_15=true;
      $('.variable_product_price_sale_from_list').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1})); 
    }
    if (def_column_show[16]==true) {
      set_datetime_16=true;
      $('.variable_product_price_sale_to_list').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1}));
    }
    if (def_column_show[23]==true) {
      set_datetime_23=true;
      $('.variable_product_price_retail_sale_from_list').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1}));
    }
    if (def_column_show[24]==true) {
      set_datetime_24=true;
      $('.variable_product_price_retail_sale_to_list').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1}));
    }

    
    
    $('.variable_product_code_list, .variable_product_descr_list, ' +
      '.variable_product_sku_list, .variable_product_gtin_list, .variable_product_upc_list, .variable_product_ean_list, .variable_product_isbn_list, .variable_product_taric_list, ' +

      '.variable_product_price_yperx_list, .variable_product_price_yperx_include_vat_list, ' +
      '.variable_product_price_yperx_sale_list, .variable_product_price_yperx_sale_dates_list, ' +
      '.variable_product_price_yperx_sale_from_list, .variable_product_price_yperx_sale_to_list, ' +
      '.variable_product_price_yperx_sheets_formula_list, .variable_product_price_yperx_quantity_formula_list, ' +
      
      '.variable_product_price_list, ' + 
      '.variable_product_price_include_vat_list, .variable_product_price_sale_list, .variable_product_price_sale_dates_list, ' + 
      '.variable_product_price_sale_from_list, .variable_product_price_sale_to_list, ' + 
      '.variable_product_price_sheets_formula_list, .variable_product_price_quantity_formula_list, ' + 
      
      '.variable_product_price_retail_list, .variable_product_price_retail_include_vat_list, ' +
      '.variable_product_price_retail_sale_list, .variable_product_price_retail_sale_dates_list, ' +
      '.variable_product_price_retail_sale_from_list, .variable_product_price_retail_sale_to_list, ' +
      '.variable_product_price_retail_sheets_formula_list, .variable_product_price_retail_quantity_formula_list, ' +
      
      '.variable_product_kostos_list, .variable_product_varos_list, ' + 
      '.variable_product_ogos_x_list, .variable_product_ogos_y_list, .variable_product_ogos_z_list, ' +
      '.variable_product_fpa_base_id_list, .variable_min_quantity_alert_list' 
      ).on('keydown', function(event) {
      //down 40, up 38, right 39, left 37, 13 enter
      //console.log(event.which);
      if (event != undefined && event.which != undefined) {
        if (event.which == 40 || event.which == 38 || event.which == 13) { //down
          event.preventDefault();
          event.stopPropagation();
          vp_cc=parseInt($(this).attr('data-vp_cc')); if (isNaN(vp_cc)) vp_cc=0;
          if (vp_cc>0) {
            elemclass=$(this).attr('class').split(' ');
            elem=$('#dialog_variable_list_table_elem .' + elemclass[0] + '[data-vp_cc=' + (vp_cc + (event.which == 38 ? -1 : 1)) + ']');
            if (elem.length>=1) {
              elem.focus().select();
            } else if (vp_cc>1 && (event.which == 40 || event.which == 13)) {
              $('#dialog_variable_list_table_elem .' + elemclass[0] + '[data-vp_cc=1]').focus().select();
            } else if (vp_cc==1 && event.which == 38) {
              $('#dialog_variable_list_table_elem .' + elemclass[0] + ':last').focus().select();  
            } 
            //console.log(elemclass[0]);
          }
        } else if (event.which == 37 || event.which == 39) { 
          dom_elem=$(this)[0];
          //console.log(dom_elem.tagName);
          //console.log($(this).attr('type'));
          if (dom_elem.tagName=='INPUT') { // || dom_elem.tagName=='SELECT'
            gks_goto='';
            switch ($(this).attr('type')) {
              case 'text':
                val = $(this).val();
                if (typeof dom_elem.selectionStart == "number") {
                  if (val=='' &&  event.which == 37) gks_goto='left';
                  else if (val=='' &&  event.which == 39) gks_goto='right';
                  else if (dom_elem.selectionStart == 0 && dom_elem.selectionEnd == val.length && event.which == 37) gks_goto='left';
                  else if (dom_elem.selectionStart == 0 && dom_elem.selectionEnd == val.length && event.which == 39) gks_goto='right';
                  else if (dom_elem.selectionStart == 0 && event.which == 37) gks_goto='left';
                  else if (dom_elem.selectionStart == val.length && event.which == 39) gks_goto='right';
                }
                break;
              case 'number':
                //document.selection.createRange().text
                //dom_elem.focus();
                //console.log(document.selection.createRange());
                if (event.which == 37) gks_goto='left';
                else if (event.which == 39) gks_goto='right';
                break;
              case 'checkbox':
              case 'select':
                if (event.which == 37) gks_goto='left';
                else if (event.which == 39) gks_goto='right';
                break;
              default:
                
            }
            //console.log('gks_goto', gks_goto);
            data_cid=parseInt($(this).parent().attr('data-cid')); if (isNaN(data_cid)) data_cid=0;
            if (data_cid>0) {
              if (gks_goto=='right') {
                event.preventDefault();
                event.stopPropagation();
                data_cindex=0;
                for(i=0;i<var_column_order.length;i++) {
                  if (var_column_order[i]==data_cid) { 
                    data_cindex=i;break;
                  }
                }
                for (i=data_cindex+1 ; i < data_cindex + (var_column_order.length-1) ; i++) {
                  data_index=i; if (i>(var_column_order.length-1)) data_index=i-var_column_order.length-1;
                  elem=$(this).parent().parent().find('td[data-cid=' + var_column_order[data_index] + ']').find('input');
                  //if (elem.length==0) elem=$(this).parent().parent().find('td[data-cid=' + var_column_order[data_index] + ']').find('select');
                  if (elem.length==1 && elem.css('display')!='none' && elem.parent().css('display')!='none') {
                    elem.focus().select();
                    break;
                  }
                }
              } else if (gks_goto=='left') {
                event.preventDefault();
                event.stopPropagation();
                data_cindex=0;
                for(i=0;i<var_column_order.length;i++) {
                  if (var_column_order[i]==data_cid) { 
                    data_cindex=i;break;
                  }
                }                
                for (i=data_cindex-1 ; i > data_cindex - (var_column_order.length-1) ; i--) {
                  data_index=i; if (i<1) data_index=i+(var_column_order.length-1);
                  elem=$(this).parent().parent().find('td[data-cid=' + var_column_order[data_index] + ']').find('input');
                  //if (elem.length==0) elem=$(this).parent().parent().find('td[data-cid=' + var_column_order[data_index] + ']').find('select');
                  if (elem.length==1 && elem.css('display')!='none' && elem.parent().css('display')!='none') {
                    elem.focus().select();
                    break;
                  }
                }
                
              }
            }
            
          }
          
        }
      }
      
    });
    
    
    $('.variable_product_photo_img_list').click(variable_product_photo_img_click);
    $('.variable_product_photo_reset_list').click(function() {
      paa=parseInt($(this).attr('data-list_paa'));
      if (isNaN(paa)) paa=0;
      if (paa>0) {
        $('.variable_product_photo_img_list[data-list_paa=' + paa + ']').attr('src', '/my/img/product.png');
        //$('.variable_product_photo[data-paa=' + paa + ']').val('');
        $('.variable_product_photo_reset_list[data-list_paa=' + paa + ']').hide();
      }
      
    });

    
    //console.log((new Date()).getTime());
    $("body").removeClass("myloading");
    dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  dialog_variable_list.dialog('option', 'width', dwidth);
	  dialog_variable_list.dialog('option', 'height', dheight);
	  $('#dialog_variable_list').parent().css({position:'fixed'});      
    dialog_variable_list.dialog('open');
    //console.log((new Date()).getTime());
  }

  $('.def_column_show_check').change(function() {
    var cid=parseInt($(this).attr('id').replace('def_column_show_','')); if (isNaN(cid)) cid=-1;
    if (cid<0) return;
    //console.log(cid);
    var temp1=$(this).is(':checked');
    //console.log(def_column_show);
    $('#dialog_variable_list_table th[data-cid=' + cid + ']').each(function() {
      if (temp1) $(this).show(); else $(this).hide();
    });
    $('#dialog_variable_list_table td[data-cid=' + cid + ']').each(function() {
      if (temp1) $(this).show(); else $(this).hide();
    });
    if (cid==36 && set_datetime_36==false) {
      $('.variable_product_price_yperx_sale_from_list').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1})); 
      set_datetime_36=true;
    }
    if (cid==37 && set_datetime_37==false) {
      $('.variable_product_price_yperx_sale_to_list').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1})); 
      set_datetime_37=true;
    }

    if (cid==15 && set_datetime_15==false) {
      $('.variable_product_price_sale_from_list').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1})); 
      set_datetime_15=true;
    }
    if (cid==16 && set_datetime_16==false) {
      $('.variable_product_price_sale_to_list').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1})); 
      set_datetime_16=true;
    }
    if (cid==23 && set_datetime_23==false) {
      $('.variable_product_price_retail_sale_from_list').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1})); 
      set_datetime_23=true;
    }
    if (cid==24 && set_datetime_24==false) {
      $('.variable_product_price_retail_sale_to_list').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1})); 
      set_datetime_24=true;
    }
    
  });
  

  autocomplete_product_taric={
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-taric.php',
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

      elem=$(event.target);
      tval=elem.val().trim();
      eid=elem.attr('id');
      elem_descr=elem.parent().parent().find('.product_taric_descr');
      elem_get=elem.parent().find('.product_taric_get_descr');
      //console.log(tval,eid);
      
      if (tval=='') elem_descr.hide();
      else setTimeout(function(myelem) {
        elem_get.click();
      }, 500,elem_get);
      
    },
    change: function (event, ui) {
      need_save=true;

      elem=$(event.target);
      tval=elem.val().trim();
      eid=elem.attr('id');
      elem_descr=elem.parent().parent().find('.product_taric_descr');
      elem_get=elem.parent().find('.product_taric_get_descr');
      //console.log(tval,eid);
      
      if (tval=='') elem_descr.hide();
      else setTimeout(function(myelem) {
        elem_get.click();
      }, 500,elem_get);
      
      
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
      $(this).data('ui-autocomplete').menu.element.css('width',mymaxui_text+'px');
    },
            
  };  
  $('#product_taric, .variable_product_taric').autocomplete(autocomplete_product_taric);

  var product_taric_get_descr_run=false;
  function product_taric_get_descr_click() {
    if (product_taric_get_descr_run) return;
    
    taric_code=$(this).parent().find('input.myneedsave').val().trim();
    elem_product_taric_descr=$(this).parent().parent().find('.product_taric_descr');
    if (taric_code=='') {
      elem_product_taric_descr.html('<div class="alert alert-warning alert-dismissible fade show" role="alert">' +
        gks_lang('Πληκρολογήστε κάποιον κωδικό στο παραπάνω πεδίο') +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
      '</div>').show();
      gks_myscroll();
      return;
    }
    
    product_taric_get_descr_run=true;
    elem_product_taric_descr.html('<div><img src="img/wait.gif"></div>').show();  
    gks_myscroll();    
    datasend='code=' + encodeURIComponent($.base64.encode(taric_code));
    $.ajax({
      url: 'admin-autocomplete-taric-get-descr.php',
      type: 'POST',
      dataType: "json",
      cache: false,
      data: datasend,
      gks_elem_product_taric_descr:elem_product_taric_descr,
      error : function(jqXHR ,textStatus,  errorThrown) {
				product_taric_get_descr_run=false;
				this.gks_elem_product_taric_descr.html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
          gks_lang('Σφάλμα') + ': ' + jqXHR.responseText + 
          '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
        '</div>').show();
        gks_myscroll();
			},
      success: function( data ) {
        product_taric_get_descr_run=false;
        if (data.success == true) {
          //console.log(data);
          tabledata='';
          if (data.data && data.data.length >= 1) {
            tabledata=
            '<table class="table table-sm table-responsive1 table-striped table-bordered gkstable100 product_taric_descr_table" border="0" cellspacing="0" cellpadding="5" align="center">' +
            '<thead>' +
              '<tr>' +
                '<th class="table-dark" scope="col" style="width:0%">#</th>' +
                '<th class="table-dark" scope="col" style="width:20%">' + gks_lang('Κωδικός') + '</th>' +
                '<th class="table-dark" scope="col" style="width:80%">' + gks_lang('Περιγραφή') + '</th>' +
              '</tr>' +
            '</thead>' + 
            '<tbody>';
            for(i=0; i < data.data.length;i++) {
              isbold='';
              if (data.code==data.data[i].c) isbold=' style="font-weight:bold"';
              
              tabledata+='<tr>' +
              '<td class="mytdcm aa">' +  (i+1) + '</td>' +
              '<td class="mytdcml" ' + isbold + '>' +  data.data[i].c + '</td>' +
              '<td class="mytdcml" ' + isbold + '>' +  data.data[i].d + '</td>' +
              '</tr>';
              if (isbold!='' && data.code.length>=10) break;
            }
            tabledata+='</tbody></table>';
          }
          this.gks_elem_product_taric_descr.html('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
            '<div class="product_taric_descr_text">' + $.base64.decode(data.message) + '</div>' + 
            tabledata + 
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
          '</div>').show();
          gks_myscroll();
        } else {
  				this.gks_elem_product_taric_descr.html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
            gks_lang('Σφάλμα') + ': ' + $.base64.decode(data.message) + 
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
          '</div>').show();
          gks_myscroll();
        }
      }
    });
          
  }

  $('.product_taric_get_descr').click(product_taric_get_descr_click);

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
  //console.log('ready');
});



