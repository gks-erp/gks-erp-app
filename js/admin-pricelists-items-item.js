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
  function mysubmit() {
    
    datasend='';

    datasend+='&pricelist_item_descr='  + encodeURIComponent($.base64.encode($("#mypostform #pricelist_item_descr").val().trim()));
    datasend+='&pricelist_id='  + encodeURIComponent($("#mypostform #pricelist_id").val().trim());
    datasend+='&pricelist_item_coupon='  + encodeURIComponent($.base64.encode($("#mypostform #pricelist_item_coupon").val().trim()));
    datasend+='&pricelist_item_sequence='  + encodeURIComponent($("#mypostform #pricelist_item_sequence").val().trim());
    datasend+='&pricelist_item_price_epi='  + encodeURIComponent($("#mypostform #pricelist_item_price_epi").val().trim());
    datasend+='&pricelist_item_price_plus='  + encodeURIComponent($("#mypostform #pricelist_item_price_plus").val().trim());
    datasend+='&pricelist_item_price_eval='  + encodeURIComponent($.base64.encode($("#mypostform #pricelist_item_price_eval").val()).trim());
    datasend+='&pricelist_item_date_from='  + encodeURIComponent($("#mypostform #pricelist_item_date_from").val().trim());
    datasend+='&pricelist_item_date_to='  + encodeURIComponent($("#mypostform #pricelist_item_date_to").val().trim());
    datasend+='&pricelist_item_min_posotita='  + encodeURIComponent($("#mypostform #pricelist_item_min_posotita").val().trim());
    datasend+='&pricelist_item_min_price='  + encodeURIComponent($("#mypostform #pricelist_item_min_price").val().trim());
    datasend+='&pricelist_item_max_price='  + encodeURIComponent($("#mypostform #pricelist_item_max_price").val().trim());
    datasend+='&pricelist_item_individual_use=' + (($('#pricelist_item_individual_use').is(':checked')) ? '1':'0');
    datasend+='&pricelist_item_exclude_sale_items=' + (($('#pricelist_item_exclude_sale_items').is(':checked')) ? '1':'0');
    datasend+='&pricelist_item_users_emails='  + encodeURIComponent($.base64.encode($("#mypostform #pricelist_item_users_emails").val().trim()));
    datasend+='&pricelist_item_usage_limit='  + encodeURIComponent($("#mypostform #pricelist_item_usage_limit").val().trim());
    datasend+='&pricelist_item_limit_usage_to_x_items='  + encodeURIComponent($("#mypostform #pricelist_item_limit_usage_to_x_items").val().trim());
    datasend+='&pricelist_item_usage_limit_per_user='  + encodeURIComponent($("#mypostform #pricelist_item_usage_limit_per_user").val().trim());
    datasend+='&pricelist_item_disable=' + (($('#pricelist_item_disable').is(':checked')) ? '0':'1');
    
    datasend+=gks_custom_datasend();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-pricelists-items-item-exec.php?id=' + from_php_id,
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
  
  $('#pricelist_item_date_from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,}));
  $('#pricelist_item_date_to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,}));


  $('#pricelist_item_min_posotita, #pricelist_item_price_epi, #pricelist_item_price_plus').on('change keyup paste', function(event) {  
    myexample();
  });

  
  function myexample() {
    var epi=parseFloat($('#pricelist_item_price_epi').val().replace(',','.'));
    var plus=parseFloat($('#pricelist_item_price_plus').val().replace(',','.'));
    
    $('#price_epi').html(epi);
    $('#price_plus').html(plus);
    var res=100*(1+epi)+plus;
    $('#example').html(res.formatMoney(2,',','.'));
  }
  myexample();
  


  $('#product').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        //onlydescr:1,
        and_variable:1,
        mode:'simple',
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
    select: function( event, ui ) {
      //$("#product").val(ui.item.descr);
      $("#product_id").val(ui.item.id);
      //datasend='&product_id='  + encodeURI(ui.item.id.trim());
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#product").val("");
          $("#product_id").val("");
        }
    },
//    create: function () {
//      $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
//        return $('<li>')
//          .append('<a class="gks_autocomplete_id">' + item.value + '</a>' + '<span class="gks_autocomplete_text">' + item.descr + '</span>')
//          .appendTo(ul);
//      };
//    },
//    open: function(event, ui) {
//      var mymaxui_id=0;
//      $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_id').each(function() {
//        temp=$(this).outerWidth();
//        if (temp>mymaxui_id) mymaxui_id=temp;
//      });
//      var mymaxui_text=0;
//      $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_text').each(function() {
//        temp=$(this).outerWidth();
//        if (temp>mymaxui_text) mymaxui_text=temp;
//      });
//      mymaxui_id+=4;
//      $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_id').each(function() {
//        $(this).css({'min-width':mymaxui_id + 'px','display' : 'inline-block'});
//      }); 
//      mymaxui_text+=mymaxui_id + 4;
//      $(this).data('ui-autocomplete').menu.element.css('width',mymaxui_text+'px');
//    },    
  });
 
  $('#add_product').click(function(event) {  
    if (from_php_id<=0) {myalert('error:' + gks_lang('Αποθηκεύστε πρώτα την εγγραφή')); return;}	
    datasend='';
    datasend+='id=' + from_php_id;    
    datasend+='&from=pricelistitem&product_id='  + encodeURIComponent($("#product_id").val().trim());    
    datasend+='&is_include=' + encodeURIComponent($('#product_include').val().trim());
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-pricelists-items-item-product-add.php',
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
            row_html=$.base64.decode(data.row_html);
            //console.log(row_html);
            
            tr_first=$('#product_table tbody tr:first');
            if (tr_first.length>=1) {
              tr_first.before(row_html);
            } else {
              $('#product_table tbody').html(row_html);
            }
            
            $('.product_tr_new .deleterow').click(deleterow_click); 
  
  
            $('.product_tr_new').each(function() {
              $(this).removeClass('product_tr_new').addClass('product_tr_exist');
            });
            var product_aa=0;
            $('#product_table .product_aa').each(function () {
              product_aa++;
              $(this).html(product_aa);  
            });
            
  
            $("body").removeClass("myloading");  
            
            $('#product').val('');
            $('#product_id').val('');
          

					} else {
					  myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  });    

  window.gks_fnc_product_delete_after = function (myargs) {
    $("body").removeClass("myloading");
    $('.product_tr_exist[data-id=' + myargs[0] + ']').hide('fade', {}, 500,function() { 
      $(this).remove(); 
      var product_aa=0;
      $('#product_table .product_aa').each(function () {
        product_aa++;
        $(this).html(product_aa);  
      });    
    });
  }
  
  
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
    if (from_php_id<=0) {myalert('error:' + gks_lang('Αποθηκεύστε πρώτα την εγγραφή')); return;}	  
    datasend='';
    datasend+='id=' + from_php_id;    
    datasend+='&from=pricelistitem&category_id='  + encodeURI($("#cateidos_id").val().trim());    
    datasend+='&is_include=' + encodeURIComponent($('#cateidos_include').val().trim());
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: 'admin-pricelists-items-item-category-add.php',
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
    if (from_php_id<=0) {myalert('error:' + gks_lang('Αποθηκεύστε πρώτα την εγγραφή')); return;}	  
    datasend='';
    datasend+='id=' + from_php_id;    
    datasend+='&from=pricelistitem&product_brand_id='  + encodeURI($("#brand_eidos_id").val().trim());    
    datasend+='&is_include=' + encodeURIComponent($('#brand_eidos_include').val().trim());
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: 'admin-pricelists-items-item-brand-add.php',
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

  var elems_switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  elems_switchery1_this.forEach(function(html) {
    var switchery1_this = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });
  
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
