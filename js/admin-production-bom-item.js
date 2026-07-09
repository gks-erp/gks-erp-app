/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


var need_save=false;
var mychange = 'change keyup paste';
var gks_page_loading=true;

var calc_pliroteo_xhr=null;  
var calc_pliroteo_timer=null;

jQuery(document).ready(function($) {


  var control_enter_active=false;
  
  $(document).on('keypress', function(event) {
    if (event.which == 10 && event.ctrlKey) {
      control_enter_active=true;
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

  function mysubmit(from_calc_pliroteo) {
    
    datasend='';
    datasend+='&from_calc_pliroteo=' + (from_calc_pliroteo ? '1' : '0');
    datasend+='&bom_descr='  + encodeURIComponent($.base64.encode($("#mypostform #bom_descr").val().trim()));
    datasend+='&reference='  + encodeURIComponent($.base64.encode($("#mypostform #reference").val().trim()));
    datasend+='&bom_product_id=' + encodeURIComponent($("#mypostform #bom_product_id").attr('data-id').trim());
    if ($("#company_id_sub_id").length > 0) datasend+='&company_id_sub_id=' + encodeURIComponent($.base64.encode($("#company_id_sub_id").val().trim()));
    datasend+='&bom_quantity='  + encodeURIComponent($("#bom_quantity").val().trim());
    datasend+='&bom_monada_id='  + encodeURIComponent($("#bom_monada_id").val().trim());
    datasend+='&bom_note='  + encodeURIComponent($.base64.encode($("#mypostform #bom_note").val().trim()));
    datasend+='&bom_disable=' + (($('#mypostform #bom_disable').is(':checked')) ? '0':'1');

    var eidi_array=[];
    $('.gks_code').each(function() {
      aa=parseInt($(this).attr('data-aa'));
      if (isNaN(aa)) aa=0;        
      if (aa>0) {
        id_production_bom_product = parseInt($('.gks_eidos[data-aa=' + aa + ']').attr('data-recid'));
        if (isNaN(id_production_bom_product)) id_production_bom_product=0;
        pbom_product_id = parseInt($('.gks_code[data-aa=' + aa + ']').attr('data-id'));
        if (isNaN(pbom_product_id)) pbom_product_id=0; 
        pbom_variant_product_id=parseInt($('.gks_pbom_variant_product_id[data-aa=' + aa + ']').val());
        if (isNaN(pbom_variant_product_id)) pbom_variant_product_id=0; 
        
        pbom_quantity=parseFloat($('.gks_pbom_quantity[data-aa=' + aa + ']').val());
        if (isNaN(pbom_quantity)) pbom_quantity=0; 
        
        pbom_monada_id=parseInt($('.gks_pbom_monada_id[data-aa=' + aa + ']').val());
        if (isNaN(pbom_monada_id)) pbom_monada_id=0; 
        
        pbom_kostos_type=parseInt($('.gks_pbom_kostos_type[data-aa=' + aa + ']').val());
        if (isNaN(pbom_kostos_type)) pbom_kostos_type=0; 
        
        pbom_kostos_value=parseFloat($('.gks_pbom_kostos_value[data-aa=' + aa + ']').val());
        if (isNaN(pbom_kostos_value)) pbom_kostos_value=0; 
        
        pbom_note=$('.gks_comments[data-aa=' + aa + ']').val();
        
        item={};
        item.id_production_bom_product=id_production_bom_product;
        item.pbom_aa=aa;
        item.pbom_product_id=pbom_product_id;
        item.pbom_variant_product_id=pbom_variant_product_id;
        item.pbom_quantity=pbom_quantity;
        item.pbom_monada_id=pbom_monada_id;
        item.pbom_kostos_type=pbom_kostos_type;
        item.pbom_kostos_value=pbom_kostos_value;
        item.pbom_note=pbom_note;
        
        eidi_array.push(item);
      }
      
      
    });
    
    eidi_array_str = encodeURIComponent($.base64.encode(JSON.stringify(eidi_array)));
    datasend+='&eidi_array_str=' + eidi_array_str;    
    
    
    var cost_array=[];
    $('.gks_cbom_cost').each(function() {
      bb=parseInt($(this).attr('data-bb'));
      if (isNaN(bb)) bb=0;        
      if (bb>0) {
        id_production_bom_cost=parseInt($('.gks_cost_line[data-bb=' + bb + ']').attr('data-recid'));
        if (isNaN(id_production_bom_cost)) id_production_bom_cost=0;
        cbom_cost=$('.gks_cbom_cost[data-bb=' + bb + ']').val();
        cbom_note=$('.gks_cbom_note[data-bb=' + bb + ']').val();
        cbom_kostos_value=parseFloat($('.gks_cbom_kostos_value[data-bb=' + bb + ']').val());
        if (isNaN(cbom_kostos_value)) cbom_kostos_value=0; 
        cbom_variant_product_id=parseInt($('.gks_cbom_variant_product_id[data-bb=' + bb + ']').val());
        if (isNaN(cbom_variant_product_id)) cbom_variant_product_id=0; 
        
        item={};
        item.id_production_bom_cost=id_production_bom_cost;
        item.cbom_aa=bb;
        item.cbom_cost=cbom_cost;
        item.cbom_note=cbom_note;
        item.cbom_kostos_value=cbom_kostos_value;
        item.cbom_variant_product_id=cbom_variant_product_id;
        
        cost_array.push(item);
      }
    });

    cost_array_str = encodeURIComponent($.base64.encode(JSON.stringify(cost_array)));
    datasend+='&cost_array_str=' + cost_array_str;    
    
    //console.log(cost_array);
    //console.log(datasend);
    //return;
    
    datasend+=gks_custom_datasend();
    
    if (from_calc_pliroteo==false) $('body').addClass("myloading");
    
    
    var myajax = $.ajax({
			url: '/my/admin-production-bom-item-exec.php?id=' + from_php_id,
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_from_calc_pliroteo: from_calc_pliroteo,
			error : function(jqXHR ,textStatus,  errorThrown) {
				if (this.gks_from_calc_pliroteo) {
    			$('#calc_hourglass').hide();
    			console.log('error:' + jqXHR.responseText);
				} else {
	  		  $("body").removeClass("myloading");
  				myalert('error:' + jqXHR.responseText);
				}
			},				
			success: function(data) {
				if (this.gks_from_calc_pliroteo) {
    			$('#calc_hourglass').hide();
    			//console.log(data);
				} else {
				  $("body").removeClass("myloading");
				}
				if (!data) {
					if (this.gks_from_calc_pliroteo==false) myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
	        if (this.gks_from_calc_pliroteo) {
	          //console.log(data);
	          $('#div_monada_conv').css('display',data.out_data.div_monada_conv_display);
	          $('#span_monada_conv').html(data.out_data.span_monada_conv_html);
	          $('#div_monada_conv2').css('display',data.out_data.div_monada_conv_display);
	          $('#span_monada_conv2').html(data.out_data.span_monada_conv2_html);
	          
	          for(i=0;i<data.out_data.out_eidi.length;i++) {
	            $('.gks_bom_monada_convert[data-aa=' + data.out_data.out_eidi[i].aa + ']').html(data.out_data.out_eidi[i].gks_bom_monada_convert_html);
	            $('.gks_pbom_kostos_value[data-aa=' + data.out_data.out_eidi[i].aa + ']').attr('data-kostos_org',data.out_data.out_eidi[i].gks_pbom_kostos_value_data_kostos_org);
	            if ($('.gks_pbom_kostos_value[data-aa=' + data.out_data.out_eidi[i].aa + ']').prop('disabled')) {
  	            $('.gks_pbom_kostos_value[data-aa=' + data.out_data.out_eidi[i].aa + ']').val(data.out_data.out_eidi[i].gks_pbom_kostos_value_val);
	            }
	          }
	          
	          $('#gsk_base_ylika').html(data.calc_res.base.ylika_str);
	          $('#gsk_base_other_cost').html(data.calc_res.base.other_cost_str);
	          $('#gsk_base_total').html(data.calc_res.base.total_str);
	          
	          $('#gks_report').html(data.calc_res.report);
	          
	          
	          return;
				  }
					if (data.success == true) {
  					//myalert('ok:' + 'OK');
				    need_save=false;
            if (data.redirect=='') {
  					  window.location.reload();
  					} else {
  					  window.location.href = $.base64.decode(data.redirect);
  					}				    
					} else {
						if (this.gks_from_calc_pliroteo==false) myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    if (from_calc_pliroteo) 
      calc_pliroteo_xhr=myajax;
    else
      calc_pliroteo_xhr=null;
      
    return false;
  }  
  
  var bom_descr_user_change=false;
  $('#bom_descr').on(mychange, function() {
    bom_descr_user_change=true;
    if ($(this).val()=='') bom_descr_user_change=false;
  });
  
  
  
  $('#bom_product_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        mode:'simple',
        and_variable:1,
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
    delay: 300, //default
    autoFocus: true,
    select: function( event, ui ) {
      $("#bom_product_id").attr('data-id',ui.item.id);
      $('#autocomplete_bom_product_id').attr('href', 'admin-products-item.php?id=' + ui.item.id.trim());
      $('#autocomplete_bom_product_id').show();
      //$('#bom_monada_id').val(ui.item.monada_id);
      if (bom_descr_user_change==false) {
        $('#bom_descr').val(ui.item.value);
      }
      get_product_data(-1);
      need_save=true;
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $("#bom_product_id").val('').attr('data-id','0');
        $('#autocomplete_bom_product_id').hide(); 
        $('#img_product_photo').attr('src', '/my/img/product.png').parent().attr('href','#');
        $('#div_product_photo').hide();
        $('#span_product_variants').html('');
        $('#div_product_variants').hide();
        
        $('#div_monada_conv').hide();
        $('#div_monada_conv2').hide();

        
        $('#bom_monada_id').val('0');
        from_php_product_class='';
        from_php_gproduct_variants=[];
        $('.gks_pbom_variant_product_id').each(function() {
          $(this).val('0');
          $(this).find('option').each(function() {
            if ($(this).attr('value') >0 ) $(this).remove();
          });
        });
        $('.gks_pbom_variant_product_id').hide();
        $('.gks_cbom_variant_product_id').each(function() {
          $(this).val('0');
          $(this).find('option').each(function() {
            if ($(this).attr('value') >0 ) $(this).remove();
          });
        });
        $('.gks_cbom_variant_product_id').hide();
        
        
        calc_pliroteo();
      }
    },
    create: function () {
//      $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
//        return $('<li>')
//          //.append('<a>' + item.value + '</a>' + '<span class="gks_autocomplete_text">' + item.descr + '</span>')
//          .append('<a>' + item.descr + '</a>')
//          .appendTo(ul);
//      };
    }    
  });
  
  
  function get_product_data(aa) {
    
    id_product=0;
    if (aa==-1) { //einai to basiko
      id_product=parseInt($('#bom_product_id').attr('data-id'));
    } else {
      id_product=parseInt($('.gks_code[data-aa=' + aa + ']').attr('data-id'));
    }
    if (isNaN(id_product)) id_product=0;
    if (id_product<=0) return;
      
    
    datasend='cmd=get&id=' + id_product + '&aa=1&sheets=0&quantity=1';
            
            
    $.ajax({
			url: 'admin-get-product-data.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_aa:aa,
			error : function(jqXHR ,textStatus,  errorThrown) {
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
			  need_save=true;
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
					  //console.log(data);
					  if (this.gks_aa==-1) { //basiko eidos
  					  $('#bom_monada_id').val(data.product_monada_id);
  					  
  					  if (data.product_photo=='') {
  					    $('#div_product_photo').hide();
  					  } else {
  					    $('#div_product_photo').show();
  					    $('#img_product_photo').attr('src', data.product_photo).parent().attr('href',data.photo_url);
  					    mylgbase_restart();
  					  }
  					  
              from_php_product_class=data.product_class;
              if (from_php_product_class=='variable') {
                from_php_gproduct_variants=data.product_variants;
                if (from_php_gproduct_variants.length>0) {
                  temp='';
                  for (i=0;i<from_php_gproduct_variants.length;i++) {
                    temp+='<a href="admin-products-item.php?id=' + from_php_gproduct_variants[i].id + '">' + from_php_gproduct_variants[i].descr + '</a><br>';
                  }
                  temp=temp.substring(0,temp.length-4);
                  $('#span_product_variants').html(temp);
                  $('#div_product_variants').show();
                } else {
                  $('#div_product_variants').hide();
                }                
              } else {
                from_php_gproduct_variants=[];
                $('#div_product_variants').hide();
              }
              
              $('.gks_pbom_variant_product_id').each(function() {
                $(this).val('0');
                $(this).find('option').each(function() {
                  if ($(this).attr('value') >0 ) $(this).remove();
                });
                if (from_php_product_class=='variable') {
                  if (from_php_gproduct_variants.length>0) {
                    for (i=0;i<from_php_gproduct_variants.length;i++) {
                      $(this).append('<option value="' + from_php_gproduct_variants[i].id + '">' + from_php_gproduct_variants[i].descr + '</option>');
                    }
                    $(this).show();
                  } else {
                    $(this).hide();
                  }
                } else {
                  $(this).hide();
                }
              });
              $('.gks_cbom_variant_product_id').each(function() {
                $(this).val('0');
                $(this).find('option').each(function() {
                  if ($(this).attr('value') >0 ) $(this).remove();
                });
                if (from_php_product_class=='variable') {
                  if (from_php_gproduct_variants.length>0) {
                    for (i=0;i<from_php_gproduct_variants.length;i++) {
                      $(this).append('<option value="' + from_php_gproduct_variants[i].id + '">' + from_php_gproduct_variants[i].descr + '</option>');
                    }
                    $(this).show();
                  } else {
                    $(this).hide();
                  }
                } else {
                  $(this).hide();
                }
              });
              
              
            } else { //eidos apo lista
              aa=this.gks_aa;
              if (data.product_photo=='') {
                $('.gks_img[data-aa=' + aa + ']').hide().removeAttr('src').parent().removeAttr('href').removeAttr('data-sub-html').removeClass('lightgalleryitem_bom');
              } else {
                $('.gks_img[data-aa=' + aa + ']').show().attr('src',data.product_photo).parent().attr('href',data.photo_url).attr('data-sub-html',data.product_code).addClass('lightgalleryitem_bom');
              }
              $('.gks_descr[data-aa=' + aa + ']').html(data.product_descr);
              $('.gks_product_zoom[data-aa=' + aa + ']').show().attr('data-id_product',data.id).parent().attr('href','admin-products-item.php?id=' + data.id);
              $('.gks_pbom_monada_id[data-aa=' + aa + ']').val(data.product_monada_id);
   					  mybigdescr=$('.gks_info_descr[data-aa=' + aa + ']');
   					  if (mybigdescr.hasClass('tooltipster')) mybigdescr.tooltipster('destroy');
 					    if (data.product_descr_small=='') {
   					    mybigdescr.hide().attr('title' ,'').removeClass('tooltipster');
 					    } else {
   					    mybigdescr.show().attr('title' ,data.product_descr_small).addClass('tooltipster');
   					    mybigdescr.tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
   					  }
   					  mylgdef_restart();
            }

					  if (from_url) {
              $('#bom_product_id').val(data.product_descr);
              $('#bom_descr').val(data.product_descr);
					    $('#bom_quantity').focus();
					    from_url=false;
					  }

            
            calc_pliroteo();
             
					} else {
						myalert('error:' + $.base64.decode(data.message));
						if (from_url) {
						  $('#bom_product_id').attr('data-id','0');
						  from_url=false;
						}
					}
				}
			}
		});     
            
                
  }
  
  
  $('#bom_monada_id').change(function() {
    calc_pliroteo();
  });

  $('#bom_quantity').on(mychange, function() {
    calc_pliroteo();
  });
  
  function bom_note_change() {gks_resize_textarea($(this));}
  $('#bom_note').on(mychange, bom_note_change);
  gks_resize_textarea($('#bom_note'));

  



  



  var switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  switchery1_this.forEach(function(html) {
    var switchery3 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });  

  var mylgbase = $("#div_photo");
  function mylgbase_restart() {
    if (!(mylgbase.data('lightGallery') === undefined)) {
      mylgbase.data('lightGallery').destroy(true);
    }
    mylgbase.lightGallery({selector: '.class_a_product_photo',thumbnail:true,hideBarsDelay:1000,});
  }
  mylgbase_restart();
  
  var mylgdef = $("#eidi_table");
  function mylgdef_restart() {
    if (!(mylgdef.data('lightGallery') === undefined)) {
      mylgdef.data('lightGallery').destroy(true);
    }
    mylgdef.lightGallery({selector: '.lightgalleryitem_bom',thumbnail:true,hideBarsDelay:1000,});
  }
  mylgdef_restart();
  

  function gks_code_autocomplete(myelem) {
    myelem.autocomplete({
      source: function(request, response) {
        mydata={
          term: request.term,
          onlycode1:1,
          and_variable:1,
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
        $(this).attr('data-id',ui.item.id);
        aa=parseInt($(this).attr('data-aa'));
        if (isNaN(aa)) aa=0;
        if (aa<=0) return;
        get_product_data(aa);
        
      },
      change: function (event, ui) {
        need_save=true;
        if(!ui.item){
          $(this).val('').attr('data-id','0');
          aa=$(this).attr('data-aa');
          $('.gks_img[data-aa=' + aa + ']').hide().removeAttr('src').parent().removeAttr('href').removeAttr('data-sub-html').removeClass('lightgalleryitem_bom');
          $('.gks_descr[data-aa=' + aa + ']').html('');
          $('.gks_product_zoom[data-aa=' + aa + ']').hide().attr('data-id_product','0').parent().removeAttr('href');
          $('.gks_pbom_monada_id[data-aa=' + aa + ']').val(0);
          $('.gks_info_descr[data-aa=' + aa + ']').hide();
          mylgdef_restart();
          calc_pliroteo();
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
        $(this).data('ui-autocomplete').menu.element.css('width',mymaxui_text+'px');
      },      
    });
//    .autocomplete( "instance" )._renderItem = function( ul, item ) {
//      return $( "<li>" )
//        .append( "<div>" + item.label + "<br>" + item.desc + "</div>" )
//        .appendTo( ul );
//    };    
  }
  
  
  $('.gks_code').each(function() {
    gks_code_autocomplete($(this));
  });
  
  
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
  function gks_code_keyup (event) {
    need_save=true;
    if (event != undefined && event.which != undefined && event.which == 13) {
      if (control_enter_active==false) {
        event.preventDefault();  
        aa=parseInt($(this).attr('data-aa'));
        if (isNaN(aa)) aa=0;
        if (aa<=0) return;
        next_enter_field_fnc(aa,'gks_code','gks_comments');
        return;
      }
    }
  }
  $('.gks_code').keyup(gks_code_keyup);  
  
  function gks_comments_keyup (event) {
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
          next_enter_field_fnc(aa,'gks_comments','gks_pbom_quantity');
          return;
        }
      }
    }
  }
  $('.gks_comments').keyup(gks_comments_keyup);
  
  function gks_comments_change() {gks_resize_textarea($(this));}
  $('.gks_comments').on(mychange, gks_comments_change);
  $('.gks_comments').each(function() {gks_resize_textarea($(this));});
    
  function gks_pbom_quantity_change (event) {
    need_save=true;
    event.preventDefault();  
    aa_start=parseInt($(this).attr('data-aa'));
    if (isNaN(aa_start)) aa_start=0;
    if (aa_start<=0) return;
    aa=aa_start;
    if (event != undefined && event.which != undefined && event.which == 13) {
      next_enter_field_fnc(aa,'gks_pbom_quantity','gks_pbom_monada_id');
      return;
    }
    prev_value=parseFloat($(this).attr('data-prev-value'));
    if (isNaN(prev_value)) prev_value=0;
    curr_value=parseFloat($(this).val());
    if (isNaN(curr_value)) curr_value=0;
    if (curr_value!=prev_value) {
      //get_product_data(aa, 0);
      $(this).attr('data-prev-value',curr_value);
    }
    
    calc_pliroteo('gks_pbom_quantity',aa_start);
  }
  $('.gks_pbom_quantity').on(mychange, gks_pbom_quantity_change);
  
  var gks_pbom_monada_id_keyup_aa=0;
  function gks_pbom_monada_id_keydown (event) {
    need_save=true;
    gks_pbom_monada_id_keyup_aa=0;
    if (event != undefined && event.which != undefined && event.which == 13) {
      event.preventDefault();
      aa=parseInt($(this).attr('data-aa'));
      if (isNaN(aa)) aa=0;
      if (aa<=0) return;
      gks_pbom_monada_id_keyup_aa=aa;
    }      
  }
  function gks_pbom_monada_id_keyup() {
    if (gks_pbom_monada_id_keyup_aa>0) {
      next_enter_field_fnc(aa,'gks_pbom_monada_id','gks_pbom_kostos_type');
      gks_pbom_monada_id_keyup_aa=0;
    }
  }
  function gks_pbom_monada_id_change() {
    calc_pliroteo();
  }
  $('.gks_pbom_monada_id').keydown(gks_pbom_monada_id_keydown).keyup(gks_pbom_monada_id_keyup).change(gks_pbom_monada_id_change);
  

  
  
  function gks_pbom_kostos_type_change() {
    need_save=true;
    event.preventDefault();  
    aa=parseInt($(this).attr('data-aa'));
    if (isNaN(aa)) aa=0;
    if (aa<=0) return;

    sel_val=parseInt($(this).val());
    if (isNaN(sel_val)) sel_val=0;
    
    if (sel_val==0) {
      $('.gks_pbom_kostos_value[data-aa=' + aa + ']').prop('disabled', true);
      kostos_org=$('.gks_pbom_kostos_value[data-aa=' + aa + ']').attr('data-kostos_org');
      $('.gks_pbom_kostos_value[data-aa=' + aa + ']').val(kostos_org);
    } else {
      $('.gks_pbom_kostos_value[data-aa=' + aa + ']').prop('disabled', false);
    }
    calc_pliroteo();
  }

    
  var gks_pbom_kostos_type_keyup_aa=0;
  function gks_pbom_kostos_type_keydown (event) {
    need_save=true;
    gks_pbom_kostos_type_keyup_aa=0;
    if (event != undefined && event.which != undefined && event.which == 13) {
      event.preventDefault();
      aa=parseInt($(this).attr('data-aa'));
      if (isNaN(aa)) aa=0;
      if (aa<=0) return;
      gks_pbom_kostos_type_keyup_aa=aa;
    }      
  }
  function gks_pbom_kostos_type_keyup() {
    if (gks_pbom_kostos_type_keyup_aa>0) {
      if ($('.gks_pbom_kostos_value[data-aa=' + gks_pbom_kostos_type_keyup_aa + ']').prop('disabled')) {
        next_enter_field_fnc(aa,'pida_sto','new_row');
      } else {
        next_enter_field_fnc(aa,'gks_pbom_kostos_type','gks_pbom_kostos_value');
      }
      gks_pbom_kostos_type_keyup_aa=0;
    }
  }
  
  $('.gks_pbom_kostos_type').keydown(gks_pbom_kostos_type_keydown).keyup(gks_pbom_kostos_type_keyup).change(gks_pbom_kostos_type_change);
  
  
  
  
  
  var gks_pbom_kostos_value_keyup_aa=0;
  function gks_pbom_kostos_value_keydown (event) {
    need_save=true;
    gks_pbom_kostos_value_keyup_aa=0;
    if (event != undefined && event.which != undefined && event.which == 13) {
      event.preventDefault();
      aa=parseInt($(this).attr('data-aa'));
      if (isNaN(aa)) aa=0;
      if (aa<=0) return;
      gks_pbom_kostos_value_keyup_aa=aa;
    } 
  }
  function gks_pbom_kostos_value_keyup() {
    if (gks_pbom_kostos_value_keyup_aa>0) {
      next_enter_field_fnc(aa,'gks_pbom_kostos_value','new_row');
      gks_pbom_kostos_value_keyup_aa=0;
    }
  }
  function gks_pbom_kostos_value_change() {
    calc_pliroteo();
  }
  $('.gks_pbom_kostos_value').keydown(gks_pbom_kostos_value_keydown).keyup(gks_pbom_kostos_value_keyup).on(mychange,gks_pbom_kostos_value_change);
  
  
  
  function gks_pbom_variant_product_id_change() {
    calc_pliroteo();
  }
  $('.gks_pbom_variant_product_id').change(gks_pbom_variant_product_id_change);
  
  
  
  function gks_delete_eidos_click() {
    need_save=true;
    aa=parseInt( $(this).attr('data-aa'));
    if (isNaN(aa)) aa=0;
    if (aa<=0) return;
    $('.gks_eidos[data-aa=' + aa +']').remove(); 
    
    
    if ($('.gks_eidos').length ==0) {
      eidoi_add(false,0);  
    }
    calc_pliroteo();
    gks_myscroll();
  }
  $('.gks_delete_eidos').click(gks_delete_eidos_click);   


  function eidoi_add(fromloading,click_aa) {
    //console.log('click_aa',click_aa);
    need_save=true;
    last_aa++;
    
    row_html=
    '<div class="form-group row gks_eidos" data-recid="0" data-aa="' + last_aa + '">' +
      '<div class="' + from_php_gkscols1 + '">'+
        '<input type="text" class="form-control form-control-sm gks_code" data-id="0" data-aa="' + last_aa + '" ' +
        'style="width:100%;" ' +
        'value="" ' +
        'placeholder="'+gks_lang('Κωδικός')+'"' +
        '>' +
      '</div>' +
      '<div class="' + from_php_gkscols2 + '">' +
        '<div class="text-left">' +
          '<a class="gks_photo_link" data-aa="' + last_aa + '" tabIndex="-1" href="" ><img class="gks_img" data-aa="' + last_aa + '" src="/my/img/product.png" style="display:none;"></a>' +
          '<a href=""><i class="gks_product_zoom enterrow fas fa-pen" data-id_product="0" data-aa="' + last_aa + '" title="'+gks_lang('Προβολή Είδους')+'"></i></a>' +
          '<i class="fas fa-info-circle gks_info_descr" data-aa="' + last_aa + '" title="" style="display:none;"></i>' +
          '<div class="gks_flock form-control-sm gks_descr" data-aa="' + last_aa + '"></div>' +
        '</div>' +
      '</div>' +
      '<div class="' + from_php_gkscols3 + '">' +
        '<textarea class="gks_comments form-control form-control-sm" rows="1" data-aa="' + last_aa + '" placeholder="'+gks_lang('Σχόλιο')+'"></textarea>' +
      '</div>' +
                  
      '<div class="' + from_php_gkscols4 + '">' +
        '<input style="text-align:right;" type="number" class="form-control form-control-sm gks_pbom_quantity" data-aa="' + last_aa + '" value="1" min=0 step="<?php echo $GKS_INPUT_STEP_POSOTITA;?>" placeholder="'+gks_lang('Ποσότητα')+'">' +
      '</div>' +
      '<div class="' + from_php_gkscols5 + '">' +
        '<select class="form-control form-control-sm myneedsave gks_select2 gks_pbom_monada_id" data-aa="' + last_aa + '">' +
        '<option value="0"></option>';
        for (i=0; i < from_php_monades.length; i++) {
          row_html+='<option value="' + from_php_monades[i].id + '">' + from_php_monades[i].descr + ' (' + from_php_monades[i].symbol +')</option>';
        }
    row_html+=
        '</select>' +
        '<small style="line-height: 12px;display: block;font-size:11px;" class="gks_bom_monada_convert" data-aa="' + last_aa + '">' +
        '</small>' +
        
      '</div>' +
      '<div class="' + from_php_gkscols6 + '">' +
        '<select class="form-control form-control-sm myneedsave gks_select2 gks_pbom_kostos_type" data-aa="' + last_aa + '" >' +
          '<option value="0" selected>'+gks_lang('A Το κόστος του είδους')+' </option>' +
          '<option value="1"         >'+gks_lang('B Ορισμός')+' </option>' +
        '</select> ' +
        '<input style="text-align:right;" type="number" class="form-control form-control-sm gks_pbom_kostos_value" data-aa="' + last_aa + '" value="" min=0 step="' + from_php_GKS_INPUT_STEP_AJIA + '" placeholder="'+gks_lang('Κόστος')+'" disabled data-kostos_org="">' +
      '</div>' +
      '<div class="' + from_php_gkscols7 + '">' +
        '<select class="form-control form-control-sm myneedsave gks_select2 gks_pbom_variant_product_id" data-aa="' + last_aa + '" style="' + (from_php_product_class!='variable' ? 'display:none;' : '') + '">' +
        '<option value="0"></option>';
        for (i=0; i < from_php_gproduct_variants.length; i++) {
          row_html+='<option value="' + from_php_gproduct_variants[i].id + '">' + from_php_gproduct_variants[i].descr + '</option>';
        }
        
        
    row_html+=
        '</select>' +
      '</div>' +
      
      '<div class="' + from_php_gkscols8 + '">' +
        '<div class="text-center gks_icons">' +
          '<div style="width:33%;float:left;">' +
            '<i class="fas fa-trash-alt gks_delete_eidos" data-aa="' + last_aa + '"></i>' +
          '</div>' +
          '<div style="width:33%;float:left;">' +
            '<i class="fas fa-arrows-alt-v sortorder_handle"></i>' +
          '</div>' +
          '<div style="width:33%;float:left;">' +
            '<i class="fas fa-plus-circle gks_add_eidos"  data-aa="' + last_aa + '"></i>' +
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
    

    gks_code_autocomplete($('.gks_code[data-aa=' + last_aa + ']'));
    $('.gks_code[data-aa=' + last_aa + ']').keyup(gks_code_keyup);
    $('.gks_comments[data-aa=' + last_aa + ']').keyup(gks_comments_keyup);
    $('.gks_comments[data-aa=' + last_aa + ']').on(mychange, gks_comments_change);
    $('.gks_pbom_quantity[data-aa=' + last_aa + ']').on(mychange, gks_pbom_quantity_change);
    $('.gks_pbom_monada_id[data-aa=' + last_aa + ']').keydown(gks_pbom_monada_id_keydown).keyup(gks_pbom_monada_id_keyup).change(gks_pbom_monada_id_change);
    $('.gks_pbom_kostos_type[data-aa=' + last_aa + ']').keydown(gks_pbom_kostos_type_keydown).keyup(gks_pbom_kostos_type_keyup).change(gks_pbom_kostos_type_change);
    $('.gks_pbom_kostos_value[data-aa=' + last_aa + ']').keydown(gks_pbom_kostos_value_keydown).keyup(gks_pbom_kostos_value_keyup).on(mychange,gks_pbom_kostos_value_change);
    $('.gks_pbom_variant_product_id[data-aa=' + last_aa + ']').change(gks_pbom_variant_product_id_change);

    
    $('.gks_add_eidos[data-aa=' + last_aa + ']').click(function() {gks_add_eidos_click(false,$(this));});
    $('.gks_delete_eidos[data-aa=' + last_aa + ']').click(gks_delete_eidos_click); //.hide();
    

    if (fromloading==false) {
      if (from_php_enter_order.length>0) {
        $('.' + from_php_enter_order[0] + '[data-aa=' + last_aa + ']').focus().select();
      } else {
        $('.gks_code[data-aa=' + last_aa + ']').focus().select();
      }
    }
    
    if (click_aa>0) {
      var mylist=[];
      $('.gks_eidos').each(function() {
        mylist.push($(this).attr('data-aa'));
      });
      eidi_table_sortable_after(mylist);
    }
    calc_pliroteo();
    gks_myscroll();
  }
  
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
  


  //cost
  function next_enter_cost_field_fnc(bb,fieldfrom,faultback) {
    //console.log('next_enter_field_fnc',bb,fieldfrom,faultback);
    
    if (control_enter_active) return;
    for (i=0; i<from_php_enter_cost_order.length; i++) {
      if (from_php_enter_cost_order[i]==fieldfrom) {
        if (i < (from_php_enter_cost_order.length - 1)) {
          if (from_php_enter_cost_order[i+1]=='new_row') {
            elemnext=$('.gks_cbom_cost[data-bb=' + (bb+1) + ']');
            if (elemnext.length>0) {
              elem=$('.' + from_php_enter_cost_order[0] + '[data-bb=' + (bb+1) + ']');
              if (elem.prop('nodeName')=='TEXTAREA') elem.focus();
              else elem.focus().select();
            } else {
              $('.gks_add_cost[data-bb=' + bb + ']').click();
            }
          } else {
            elem=$('.' + from_php_enter_cost_order[i+1] + '[data-bb=' + bb + ']');
            if (elem.prop('nodeName')=='TEXTAREA') elem.focus();
            else elem.focus().select();
          }
          return;
        }
      }
    }
    if (faultback=='new_row') {
      elemnext=$('.gks_cbom_cost[data-bb=' + (bb+1) + ']');
      if (elemnext.length>0) {
        if (from_php_enter_cost_order.length>0) {
          elemnextuo=$('.' + from_php_enter_cost_order[0] + '[data-bb=' + (bb+1) + ']');
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
        $('.gks_add_cost[data-bb=' + bb + ']').click();
      }
    } else if (faultback!='') {
      elem=$('.' + faultback + '[data-bb=' + bb + ']');
      if (elem.prop('nodeName')=='TEXTAREA') elem.focus();
      else elem.focus().select();
    }
  }  
  function gks_cbom_cost_keyup (event) {
    need_save=true;
    if (event != undefined && event.which != undefined && event.which == 13) {
      if (control_enter_active==false) {
        event.preventDefault();  
        bb=parseInt($(this).attr('data-bb'));
        if (isNaN(bb)) bb=0;
        if (bb<=0) return;
        next_enter_cost_field_fnc(bb,'gks_cbom_cost','gks_cbom_note');
        return;
      }
    }
  }
  $('.gks_cbom_cost').keyup(gks_cbom_cost_keyup);    
  
  function gks_cbom_note_keyup (event) {
    need_save=true;
    if (event != undefined && event.which != undefined && event.which == 13) {
      if (control_enter_active==false) {
        val=$(this).val();
        //console.log('val',val);
        if (val == '\n' || val.endsWith('\n\n')) {
          val=val.replace(/\n+$/, '');
          $(this).val(val);
          event.preventDefault();  
          bb=parseInt($(this).attr('data-bb'));
          if (isNaN(bb)) bb=0;
          if (bb<=0) return;
          next_enter_cost_field_fnc(bb,'gks_cbom_note','gks_cbom_kostos_value');
          return;
        }
      }
    }
  }
  $('.gks_cbom_note').keyup(gks_cbom_note_keyup);
  
  function gks_cbom_note_change() {gks_resize_textarea($(this));}
  $('.gks_cbom_note').on(mychange, gks_cbom_note_change);
  $('.gks_cbom_note').each(function() {gks_resize_textarea($(this));});


  var gks_cbom_kostos_value_keyup_bb=0;
  function gks_cbom_kostos_value_keydown (event) {
    need_save=true;
    gks_cbom_kostos_value_keyup_bb=0;
    if (event != undefined && event.which != undefined && event.which == 13) {
      event.preventDefault();
      bb=parseInt($(this).attr('data-bb'));
      if (isNaN(bb)) bb=0;
      if (bb<=0) return;
      gks_cbom_kostos_value_keyup_bb=bb;
    } 
  }
  function gks_cbom_kostos_value_keyup() {
    if (gks_cbom_kostos_value_keyup_bb>0) {
      next_enter_cost_field_fnc(bb,'gks_cbom_kostos_value','new_row');
      gks_cbom_kostos_value_keyup_bb=0;
    }
  }
  function gks_cbom_kostos_value_change() {
    calc_pliroteo();
  }
  $('.gks_cbom_kostos_value').keydown(gks_cbom_kostos_value_keydown).keyup(gks_cbom_kostos_value_keyup).on(mychange,gks_cbom_kostos_value_change);
  

  function gks_cbom_variant_product_id_change() {
    calc_pliroteo();
  }
  $('.gks_cbom_variant_product_id').change(gks_cbom_variant_product_id_change);


  function gks_delete_cost_click() {
    need_save=true;
    bb=parseInt( $(this).attr('data-bb'));
    if (isNaN(bb)) bb=0;
    if (bb<=0) return;
    $('.gks_cost_line[data-bb=' + bb +']').remove(); 
    
    if ($('.gks_cost_line').length ==0) {
      cost_add(false,0);  
    }
    calc_pliroteo();
    gks_myscroll();
  }
  $('.gks_delete_cost').click(gks_delete_cost_click);   


  function cost_add(fromloading,click_bb) {
    //console.log('click_bb',click_bb);
    need_save=true;
    last_bb++;
    
    row_html=
    
    '<div class="form-group row gks_cost_line" data-recid="0" data-bb="' + last_bb + '">' +
      '<div class="' + from_php_gkscols_cost1 + '">' +
        '<input type="text" class="form-control form-control-sm gks_cbom_cost" data-bb="' + last_bb + '" ' +
        'value="" ' +
        'placeholder="'+gks_lang('Περιγραφή')+'" ' +
        '>' +
      '</div>' +
      
      '<div class="' + from_php_gkscols_cost2 + '">' +
        '<textarea class="gks_cbom_note form-control form-control-sm" rows="1" data-bb="' + last_bb + '" placeholder="'+gks_lang('Σχόλιο')+'"></textarea>' +
      '</div>' +
                  

      '<div class="' + from_php_gkscols_cost3 + '">' +
        '<input style="text-align:right;" type="number" class="form-control form-control-sm gks_cbom_kostos_value" data-bb="' + last_bb + '" value="" min=0 step="' + from_php_GKS_INPUT_STEP_AJIA + '"' +
        'placeholder="'+gks_lang('Κόστος')+'">' +
      '</div>' + 
      '<div class="' + from_php_gkscols_cost4 + '">' +
        '<select class="form-control form-control-sm myneedsave gks_select2 gks_cbom_variant_product_id" data-bb="' + last_bb + '" style="' + (from_php_product_class!='variable' ? 'display:none;' : '') + '">'+
        '<option value="0"></option>';
        for (i=0; i < from_php_gproduct_variants.length; i++) {
          row_html+='<option value="' + from_php_gproduct_variants[i].id + '">' + from_php_gproduct_variants[i].descr + '</option>';
        }
        
    row_html+=

        '</select>' +
      '</div>' +        
      '<div class="' + from_php_gkscols_cost5 + '">' +
        '<div class="text-center gks_cost_icons">' +
          '<div style="width:33%;float:left;">' +
            '<i class="fas fa-trash-alt gks_delete_cost" data-bb="' + last_bb + '"></i>' +
          '</div>' +
          '<div style="width:33%;float:left;">' +
            '<i class="fas fa-arrows-alt-v sortorder_cost_handle"></i>' +
          '</div>' +
          '<div style="width:33%;float:left;">' +
            '<i class="fas fa-plus-circle gks_add_cost"  data-bb="' + last_bb + '"></i>' +
          '</div>' +
          
          
        '</div>' +
      '</div>' +
      
    '</div>';
    
    
    if (click_bb<=0) {
      $('#cost_footer1').before(row_html);
    } else {
      $('.gks_cost_line[data-bb=' + click_bb + ']').after(row_html);
    }
    
    $('.gks_add_cost').show();  
    $('.gks_delete_cost').show();  
    

    
    $('.gks_cbom_cost[data-bb=' + last_bb + ']').keyup(gks_cbom_cost_keyup);
    
    $('.gks_cbom_note[data-bb=' + last_bb + ']').keyup(gks_cbom_note_keyup);
    $('.gks_cbom_note[data-bb=' + last_bb + ']').on(mychange, gks_cbom_note_change);
    $('.gks_cbom_kostos_value[data-bb=' + last_bb + ']').keydown(gks_cbom_kostos_value_keydown).keyup(gks_cbom_kostos_value_keyup).on(mychange,gks_cbom_kostos_value_change);
    $('.gks_cbom_variant_product_id[data-bb=' + last_bb + ']').change(gks_cbom_variant_product_id_change);

    
    $('.gks_add_cost[data-bb=' + last_bb + ']').click(function() {gks_add_cost_click(false,$(this));});
    $('.gks_delete_cost[data-bb=' + last_bb + ']').click(gks_delete_cost_click); //.hide();
    

    if (fromloading==false) {
      if (from_php_enter_cost_order.length>0) {
        $('.' + from_php_enter_cost_order[0] + '[data-bb=' + last_bb + ']').focus().select();
      } else {
        $('.gks_cbom_cost[data-bb=' + last_bb + ']').focus().select();
      }
    }
    
    if (click_bb>0) {
      var mylist=[];
      $('.gks_cost_line').each(function() {
        mylist.push($(this).attr('data-bb'));
      });
      cost_table_sortable_after(mylist);
    }
    calc_pliroteo();
    gks_myscroll();
  }
  
  function gks_add_cost_click(fromloading,elem) {
    bb=elem.attr('data-bb');
    cost_add(fromloading,bb);
  }
  
  $('.gks_add_cost').click(function() {gks_add_cost_click(false,$(this));});

  $('#cost_table').sortable({
    items: '.gks_cost_line',
    handle: '.sortorder_cost_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-bb'});
      cost_table_sortable_after(mylist);
    }
  });
  
  function cost_table_sortable_after(mylist) {
      //console.log(mylist);
      $('#cost_table > .gks_cost_line').each(function() {
        aa=$(this).attr('data-bb');
        $(this).attr('data-bb_temp',aa);
      });
      $('#cost_table > .gks_cost_line').each(function() {
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
  
  //end cost


  function calc_pliroteo(field_name='', field_aa=-1, mycmd='', myfile='') {
    if (gks_page_loading) return;
    need_save=true;
    //console.log('calc_pliroteo ' + field_name + ' ' + field_aa + ' ' + mycmd + ' ' + myfile);
     
    
    if(calc_pliroteo_xhr!=null && calc_pliroteo_xhr.readyState != 4){
      calc_pliroteo_xhr.abort();
    }
    if (calc_pliroteo_timer!=null) clearTimeout(calc_pliroteo_timer);
    calc_pliroteo_timer=setTimeout(calc_pliroteo_run,400,field_name, field_aa, mycmd, myfile);
  }
  function calc_pliroteo_run(field_name='', field_aa=-1, mycmd='', myfile='') {
    $('#calc_hourglass').show();
  
    mysubmit(true);
  }
  
  var from_url=false;
  queryString = window.location.search;
  if (queryString!='') {
    urlParams = new URLSearchParams(queryString);
    product_id = urlParams.get('product_id');
    if (product_id != null) {
      from_url=true;
      //console.log(product_id);
      $('#bom_product_id').attr('data-id',product_id);
      get_product_data(-1);
    }
  }
  
  

  // last of all 
  if (from_php_id==-1) {
    eidoi_add(true,0);
    cost_add(true,0);
    if (from_url==false) {
      $('#bom_product_id').focus().select(); //to eidos for select amesa
    }
  } else {
    if (last_aa==0) eidoi_add(true,0);
    if (last_bb==0) cost_add(true,0);
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

