/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


jQuery(document).ready(function($) {

  var elems_switchery_gks_custom = Array.prototype.slice.call(document.querySelectorAll('.switchery_gks_custom'));
  elems_switchery_gks_custom.forEach(function(html) {
    
    var switchery_one = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });
    

  function gks_custom_field_textarea_change() {gks_resize_textarea($(this));}
  $('.gks_custom_field_textarea').on('change keyup paste', gks_custom_field_textarea_change);
  $('.gks_custom_field_textarea').each(function() {
    gks_resize_textarea($(this));
  });
    

  $('.gks_custom_field_datetime').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));
    
  $('.gks_custom_field_date').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,onChangeDateTime:
    function(ct,$i){
      need_save=true;
    }
  }));
  $('.gks_custom_field_time').TimePickerAlone({dragAndDrop:true,mouseWheel:true,twelveHoursFormat:false,seconds:true,ampm:false,saveOnChange:true,defaultTime:'',onChange:
    function(ct,$i){
      need_save=true;
    }
  });
  
  var gks_custom_field_multiselect_lists=[];
  $('.gks_custom_field_multiselect').each(function() {
    list_index=gks_custom_field_multiselect_lists.length+1;
    
    gks_tags=$(this).attr('data-tags');
    gks_multi_tags=$(this).attr('data-multi-tags');
    if (typeof(gks_tags) !== 'undefined') {
      
      if (gks_tags=='') { //simple
        gks_custom_field_multiselect_lists[list_index]=[];
      } else {
        gks_custom_field_multiselect_lists[list_index]=JSON.parse($.base64.decode(gks_tags));
        //console.log(gks_custom_field_multiselect_lists[list_index]);
      }
      
      $(this).tagit({
        allowSpaces: true, 
        singleFieldDelimiter:']][[',
        availableTags: gks_custom_field_multiselect_lists[list_index],
        showAutocompleteOnFocus : true,
        onTagAdded:function() {need_save=true;},
        onTagRemoved:function() {need_save=true;},
      });
    } else if (typeof(gks_multi_tags) !== 'undefined') { //multi $(this).attr('data-multi-tags')
      gks_custom_field_multiselect_lists[list_index]=[];
      
      $(this).tagit({
        gks_this: $(this),
        allowDuplicates:false,
        allowSpaces: true, 
        singleFieldDelimiter:']][[',
        autocomplete: {
          gks_this: $(this),
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
        },
        showAutocompleteOnFocus : false,
        onTagAdded:function() {need_save=true;},
        onTagRemoved:function() {need_save=true;},
      });
            
    }
  
  });

  $('.gks_custom_field_autocomplete').each(function() {

    $(this).autocomplete({
      source:  function(request, response) {
        mydata={ 
          term: request.term,
          //eml:1,
          //notme:1,
          //test:1,
        };
        data_url=$(this)[0].element.attr('data-url');
        if (data_url=='admin-autocomplete-product.php') {
          mydata.mode='simple';
          mydata.and_variable=1;
        }
        
        $.ajax({
          url: data_url,
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
      select: function( event,ui) {
        need_save=true;
        $(this).attr('data-id',ui.item.id.trim());
        gks_cf_id=$(this).attr('id');
        data_url_a=$(this).attr('data-url-a');
        data_url_a=data_url_a.replaceAll('[[]]',ui.item.id.trim());
        //console.log(data_url_a);
        $('#' + gks_cf_id + '_autocomplete').attr('href', data_url_a).show();
      },
      change: function (event, ui) {
        need_save=true;
        if(!ui.item) {
          $(this).attr('data-id','0').val('');    
          gks_cf_id=$(this).attr('id');
          $('#' + gks_cf_id + '_autocomplete').hide();
        }
      }
    });    
  });
  
   
  
  window.gks_custom_datasend = function () {
    main_elem=$('.gks_custom_fileds_data');
    if (main_elem.length==0) return '';
    var fdata=[];
    $('.gks_custom_field_class').each(function() {
      data_cf_id=$(this).attr('data-cf-id');
      data_cf_val='';
      if ($(this).hasClass('switchery_gks_custom')) {
        if ($(this).is(':checked')) data_cf_val=1; else data_cf_val=0;
      } else if ($(this).hasClass('gks_custom_field_int')) {
        data_cf_val=$(this).val();
      } else if ($(this).hasClass('gks_custom_field_double')) {
        data_cf_val=$(this).val();
      } else if ($(this).hasClass('gks_custom_field_text')) {
        data_cf_val=$(this).val();
      } else if ($(this).hasClass('gks_custom_field_textarea')) {
        data_cf_val=$(this).val();
      } else if ($(this).hasClass('gks_tinymce')) {
        data_cf_val=tinyMCE.get($(this).attr('id')).getContent();
      } else if ($(this).hasClass('gks_custom_field_date')) {
        data_cf_val=$(this).val();
      } else if ($(this).hasClass('gks_custom_field_time')) {
        data_cf_val=$(this).val();
      } else if ($(this).hasClass('gks_custom_field_datetime')) {
        data_cf_val=$(this).val();
      } else if ($(this).hasClass('gks_custom_field_select')) {
        data_cf_val=$(this).val();
      } else if ($(this).hasClass('gks_custom_field_multiselect')) {
        data_cf_val=$(this).val();
      } else if ($(this).hasClass('gks_custom_field_autocomplete')) {
        data_cf_val=$(this).attr('data-id');
      } else {
      
        //data_cf_val=$(this).val();
      }
        
      
      fdata.push({f:data_cf_id,v:data_cf_val});
    });
    //console.log(fdata);
    
    
    cf_datasend_str = encodeURIComponent($.base64.encode(JSON.stringify(fdata)));
    cf_datasend='&cf_datasend_str=' + cf_datasend_str;
    //console.log(cf_datasend);
    
    return cf_datasend;
    
  }
  
  
    
});
