/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

var gks_run_until='';

var gks_pos_settings={};
var gks_session_pos='';
var gks_eftpos_step='';
var gks_eftpos_payment_ok=false;

var need_save=false;
//var mychange = 'input keyup paste';
var mychange = 'change paste keyup';
//var mychange = 'keyup';
//var mychange = 'propertychange input change keyup paste'; 

//var mychange = 'change';
var gks_page_loading=true;

var gks_erp_app_mobile_local_printers=[];
var gks_device_type='';
var last_action_time = new Date();
var my_gps_location_lat=0;
var my_gps_location_lng=0;
var my_gps_location_res=gks_lang('Σφάλμα');

jQuery.fn.scrollTo = function(elem, speed) { 
    $(this).animate({
        scrollTop:  $(this).scrollTop() - $(this).offset().top + $(elem).offset().top 
    }, speed == undefined ? 1000 : speed); 
    return this; 
};

var audio_num = '/my/audio/num2.mp3?v=' + from_php_gks_cache_version;
var audio_added = '/my/audio/added2.mp3?v=' + from_php_gks_cache_version;
var audio_exit = '/my/audio/exit2.mp3?v=' + from_php_gks_cache_version;
var audio_reset = '/my/audio/reset2.mp3?v=' + from_php_gks_cache_version;
var audio_delete = '/my/audio/delete2.mp3?v=' + from_php_gks_cache_version;

jQuery(document).ready(function($) {

  var numpad_text='';
  
  if ($('#pos_multi_copies').length==0) {
    $('.row_gks_multi_copies_enable').hide();
  }
  
  gks_pos_settings.merchant_ref=true;  
  gks_pos_settings.merchant_ref_def_value='';  
  gks_pos_settings.multi_copies=false;
  gks_pos_settings.customer=false;  
  gks_pos_settings.msg_ok_show=true;
  gks_pos_settings.min_clicks=true;
  gks_pos_settings.edit_price=false;
  gks_pos_settings.show_fpa=false;
  gks_pos_settings.edit_quantity=false;
  gks_pos_settings.delete_item=true;
  gks_pos_settings.zoom_item=200;
  gks_pos_settings.check_exist_elem='addline'; //plusposotita
  gks_pos_settings.pay_mode='twosteps'; //fast
  gks_pos_settings.audio=true;
  gks_pos_settings.viva_id_asset=0;
  gks_pos_settings.viva_asset_title='';
  gks_pos_settings.worldline_id_asset=0;
  gks_pos_settings.worldline_asset_title='';
    
    
  gks_pos_settings.layout='numpad'; //normal
  gks_pos_settings.layout_normal={};
  gks_pos_settings.layout_normal.landscape={};
  gks_pos_settings.layout_normal.landscape.products=50;
  gks_pos_settings.layout_normal.landscape.bill=50;
  gks_pos_settings.layout_normal.portrait={};
  gks_pos_settings.layout_normal.portrait.products=100;
  gks_pos_settings.layout_normal.portrait.bill=100;
 
  gks_pos_settings.layout_numpad={};
  gks_pos_settings.layout_numpad.landscape={};
  gks_pos_settings.layout_numpad.landscape.numpad=30;
  gks_pos_settings.layout_numpad.landscape.products=30;
  gks_pos_settings.layout_numpad.landscape.bill=40;
  gks_pos_settings.layout_numpad.portrait={};
  gks_pos_settings.layout_numpad.portrait.numpad=50;
  gks_pos_settings.layout_numpad.portrait.products=50;
  gks_pos_settings.layout_numpad.portrait.bill=100;
 
  
  if (gks_is_mobile()) {
    $('body').addClass('gks_erp_app_mobile');
  }
  if (!(typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1)) {
    $('#gks_pos_menu_screen').show();
  }
  
  
//  gks_pos_settings.layout_normal.landscape.products=50; //edit
//  
//  gks_pos_settings.layout_numpad.landscape.numpad=10; //edit
//  gks_pos_settings.layout_numpad.landscape.products=45; //edit
//  
//  gks_pos_settings.layout_numpad.portrait.numpad=50; //edit
  
  
  cvalue=gks_getCookie('gks_pos_run_settings');
  if (cvalue!=null) {
    cvalue=JSON.parse(cvalue);
    //console.log(cvalue);
    
    if (cvalue.merchant_ref !== undefined || cvalue.merchant_ref != null) gks_pos_settings.merchant_ref=cvalue.merchant_ref;
    if (cvalue.merchant_ref_def_value !== undefined || cvalue.merchant_ref_def_value != null) gks_pos_settings.merchant_ref_def_value=cvalue.merchant_ref_def_value;
    if (cvalue.multi_copies !== undefined || cvalue.multi_copies != null) gks_pos_settings.multi_copies=cvalue.multi_copies;
    if (cvalue.customer !== undefined || cvalue.customer != null) gks_pos_settings.customer=cvalue.customer;
    if (cvalue.msg_ok_show !== undefined || cvalue.msg_ok_show != null) gks_pos_settings.msg_ok_show=cvalue.msg_ok_show;
    if (cvalue.min_clicks !== undefined || cvalue.min_clicks != null) gks_pos_settings.min_clicks=cvalue.min_clicks;
    if (cvalue.edit_price !== undefined || cvalue.edit_price != null) gks_pos_settings.edit_price=cvalue.edit_price;
    if (cvalue.show_fpa !== undefined || cvalue.show_fpa != null) gks_pos_settings.show_fpa=cvalue.show_fpa;
    if (cvalue.edit_quantity !== undefined || cvalue.edit_quantity != null) gks_pos_settings.edit_quantity=cvalue.edit_quantity;
    if (cvalue.delete_item !== undefined || cvalue.delete_item != null) gks_pos_settings.delete_item=cvalue.delete_item;
    if (cvalue.zoom_item !== undefined || cvalue.zoom_item != null) gks_pos_settings.zoom_item=cvalue.zoom_item;
    if (cvalue.check_exist_elem !== undefined || cvalue.check_exist_elem != null) gks_pos_settings.check_exist_elem=cvalue.check_exist_elem;
    if (cvalue.pay_mode !== undefined || cvalue.pay_mode != null) gks_pos_settings.pay_mode=cvalue.pay_mode;
    if (cvalue.audio !== undefined || cvalue.audio != null) gks_pos_settings.audio=cvalue.audio;
    if (cvalue.viva_id_asset !== undefined || cvalue.viva_id_asset != null) gks_pos_settings.viva_id_asset=cvalue.viva_id_asset;
    if (cvalue.viva_asset_title !== undefined || cvalue.viva_asset_title != null) gks_pos_settings.viva_asset_title=cvalue.viva_asset_title;
    if (cvalue.worldline_id_asset !== undefined || cvalue.worldline_id_asset != null) gks_pos_settings.worldline_id_asset=cvalue.worldline_id_asset;
    if (cvalue.worldline_asset_title !== undefined || cvalue.worldline_asset_title != null) gks_pos_settings.worldline_asset_title=cvalue.worldline_asset_title;
    if (cvalue.layout !== undefined || cvalue.layout != null) gks_pos_settings.layout=cvalue.layout;
    if (cvalue.layout_normal !== undefined || cvalue.layout_normal != null) gks_pos_settings.layout_normal=cvalue.layout_normal;
    if (cvalue.layout_numpad !== undefined || cvalue.layout_numpad != null) gks_pos_settings.layout_numpad=cvalue.layout_numpad;

    if ($('.div_payment_one_terminal_start').length>0) {
      gks_pos_settings.pay_mode='twosteps';   
    }
  }
  
  function gks_session_pos_set() {
    gks_session_pos=from_php_id_pos + '_' + from_php_user_id + '_' + (new Date().getTime()) + '_' + gks_random_string(32);
  }
  gks_session_pos_set();
  
  function myresize() {
    mywidth=$(window).width();
    myheight=$(window).height();
    if (mywidth>myheight) {
      $('#gks_pos_panel_products').removeClass('gks_pos_panel_products_portrait');
      $('#gks_pos_panel_bill').removeClass('gks_pos_panel_bill_portrait');
      $('#gks_pos_panel_numpad').removeClass('gks_pos_panel_numpad_portrait');
      $('#gks_pos_panel_products').removeClass('gks_pos_panel_products_numpad_portrait');
      
    } else {
      $('#gks_pos_panel_products').addClass('gks_pos_panel_products_portrait');
      $('#gks_pos_panel_bill').addClass('gks_pos_panel_bill_portrait');
      $('#gks_pos_panel_numpad').addClass('gks_pos_panel_numpad_portrait');
      if (gks_pos_settings.layout=='numpad') {
        $('#gks_pos_panel_products').addClass('gks_pos_panel_products_numpad_portrait');
      } else {
        $('#gks_pos_panel_products').removeClass('gks_pos_panel_products_numpad_portrait');
      }
    }
    
    if (gks_pos_settings.layout=='numpad') {
      $('#gks_pos_panel_products').addClass('gks_pos_panel_products_numpad');
      $('#gks_pos_panel_numpad').show();
      
      mybth=$('#gks_pos_panel_numpad_header').height();
      mybth=Math.round(0.5* mybth);
  
      mybtw=$('.gks_pos_panel_numpad_btn:first').width();
      mybtw=Math.round(0.5* mybtw);
      //console.log(mybth,mybtw);
      if (mybtw<mybth) mybth=mybtw;
      
      $('#gks_pos_panel_numpad_header2').css('font-size',mybth + 'px');
      $('#gks_pos_panel_numpad').css('font-size',mybth + 'px');
      
    } else {
      $('#gks_pos_panel_numpad').hide();
      $('#gks_pos_panel_products').removeClass('gks_pos_panel_products_numpad');
    }
    
    
    myw=$('#gks_pos_panel_products').width()-1;
    //console.log(myw);
    num_items=Math.round(myw/gks_pos_settings.zoom_item);
    
    
    var itemw=Math.floor(myw/num_items);
    var itemh=itemw; //Math.floor(itemw*3/3) + 50;
    if (mywidth<myheight) itemh=Math.floor(itemw*1/3) + 40;
    $('.myproduct').each(function() {
      $(this).css('width',(itemw-12)+ 'px').css('height',(itemh-12) + 'px');
    });
    
    mycstyle='';
    
    if (gks_pos_settings.edit_price==false) {
      mycstyle+='.gks_pos_item_edit_price{display:none;} ';
    }
    if (gks_pos_settings.show_fpa==false) {
      mycstyle+='#gks_pos_panel_bill_total_tax, .gks_pos_item_fpa_in_descr, .gks_pos_item_fpa_in_price {display:none;}';
    }
    if (gks_pos_settings.edit_quantity==false) {
      mycstyle+='.gks_pos_item_header_quantity, .gks_pos_item_quantity {display:none;} .gks_pos_item_product {width: 60%;} .gks_pos_item_price {width: 40%;} ';
    } else {
      mycstyle+='.gks_pos_item_quantity_in_descr {display:none;} ';
    }
    if (gks_pos_settings.delete_item==false) {
      mycstyle+='.gks_pos_item_delete {display:none;} ';
    }
    
    if (gks_pos_settings.pay_mode=='twosteps') {
      mycstyle+='#gks_pos_panel_pway {display:none;} ';
      mycstyle+='#gks_pos_panel_bill_total {width: 100%;} ';
      //mycstyle+='#gks_pos_panel_bill_total {width: 100%;text-align:center;} ';
      //mycstyle+='#gks_pos_panel_bill_total > div {width:50%;display: inline;} ';
      
      
    } else {
      
    }
    if (gks_pos_settings.merchant_ref==false) {
      mycstyle+='#gks_merchant_ref_trns_text {display:none;} ';
    }
    if (gks_pos_settings.multi_copies==false) {
      mycstyle+='#pos_multi_copies_div {display:none;} ';
    }
    
    
    
    if (gks_pos_settings.customer) {
      mycstyle+='#gks_pos_panel_bill_customer {display:block;} #gks_pos_panel_bill_list {top:40px;height: calc(100% - 140px);} ';
    }
    
    if (gks_pos_settings.layout=='numpad') {
      if (mywidth>myheight) {
        gks_pos_settings.layout_numpad.landscape.bill=100-gks_pos_settings.layout_numpad.landscape.numpad-gks_pos_settings.layout_numpad.landscape.products;
        
        mycstyle+='#gks_pos_panel_numpad {width: ' + gks_pos_settings.layout_numpad.landscape.numpad + '%;} ';
        mycstyle+='#gks_pos_panel_products {width: ' + gks_pos_settings.layout_numpad.landscape.products + '% !important; left: ' + gks_pos_settings.layout_numpad.landscape.numpad + '% !important;} ';
        mycstyle+='#gks_pos_panel_bill {width: ' + gks_pos_settings.layout_numpad.landscape.bill + '%;} ';
        
      } else {
        gks_pos_settings.layout_numpad.portrait.products=100-gks_pos_settings.layout_numpad.portrait.numpad;
        mycstyle+='#gks_pos_panel_numpad {width: ' + gks_pos_settings.layout_numpad.portrait.numpad + '% !important;} ';
        mycstyle+='#gks_pos_panel_products {width: ' + gks_pos_settings.layout_numpad.portrait.products + '% !important; left: ' + gks_pos_settings.layout_numpad.portrait.numpad + '% !important;} ';
        
      }
    } else { //normal
      if (mywidth>myheight) {
        gks_pos_settings.layout_normal.landscape.bill=100-gks_pos_settings.layout_normal.landscape.products;
        mycstyle+='#gks_pos_panel_products {width: ' + gks_pos_settings.layout_normal.landscape.products + '%;} ';
        mycstyle+='#gks_pos_panel_bill {width: ' + gks_pos_settings.layout_normal.landscape.bill + '%;} ';
      } else {
        //den allazei kati
      }
    }
    
    
    
    $('#gks_pos_run_cstyle').html(mycstyle);
    
    
  }

  //$('#gks_pos_menu_exit').click(function() {
  //  window.location.href='/my/admin-pos-run-select.php';
  //});
  
  $(window).resize(myresize);
  myresize();


  var products_page=0;
  var get_products_xhr;
  var myterm_key_old='';
  function get_products(has_enter_at_end = false) {
    last_action_time = new Date();
    term=$('#gks_pos_products_search').val().trim();
    //if (term==myterm_key_old) return;
    //myterm_key_old=term.trim();
    
    
    if(get_products_xhr && get_products_xhr.readyState != 4){
      get_products_xhr.abort();
    }    
    
    if (products_page==0) {
      $('#gks_pos_products').html(gks_lang('Φόρτωση προϊόντων')+' ...');
    }
    $('.products_loadmore').html('<img src="img/wait.gif">');
    
    //console.log('products_page',products_page);
    datasend='';
    datasend+='&id_pos=' + from_php_id_pos;
    datasend+='&page=' + products_page;
    datasend+='&term=' + encodeURIComponent($.base64.encode($('#gks_pos_products_search').val()));  

    get_products_xhr = $.ajax({
      timeout: 30000, // sets timeout to 3 seconds
			url: '/my/admin-pos-run-get-products.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_page: products_page,
			gks_has_enter_at_end:has_enter_at_end,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  if (textStatus != 'abort') {
				  if (typeof jqXHR.responseText !== 'undefined' && jqXHR.responseText.trim()!=='') {
  			    myerror_show=jqXHR.responseText
  			  } else {
  			    myerror_show=gks_lang('Υπάρχει σύνδεση με το internet ;');
  			  }
  				myalert('error:' + myerror_show);
			  }
			},				
			success: function(data) {
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
            //console.log(data);

            $('.products_loadmore').remove();
            
            out='';
            if (data.list.length>1) {
              out+='<div id="products_num_records">'+gks_lang('Βρέθηκαν [1] προϊόντα').replaceAll('[1]',data.total_records)+'</div>';
            } else if (data.list.length==1) {
              out+='<div id="products_num_records">'+gks_lang('Βρέθηκε το προϊόν')+'</div>';
            } else if (data.list.length==0) {
              out+='<div id="products_num_records">'+gks_lang('Δεν βρέθηκαν προϊόντα')+'</div>';
            }
            for (i = 0; i < data.list.length; i++) {
              if (data.list[i].myimgurl=='') myimgurl='/my/img/product.png'; else myimgurl=data.list[i].myimgurl;
              
              out+='<div class="myproduct" data-page="' + this.gks_page + '" data-id="' + data.list[i].id+ '" data-code="' + data.list[i].code + '" data-priceperitem="' + data.list[i].price + '" data-vatperitem="' + data.list[i].vat + '" data-ovatperitem="' + data.list[i].ovat + '" data-vatpososto="' + data.list[i].vp + '">' +
              '<img src="' + myimgurl + '" class="myproduct_img" border="0">' + 
              '<div class="myproduct_descr">' + data.list[i].descr + '</div>' +
              '<div class="myproduct_price">' +
                '<span class="myproduct_price_inner">' + data.list[i].price.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, from_php_GKS_NUMBER_FORMAT_DECIMAL, from_php_GKS_NUMBER_FORMAT_THOUSAND) + '</span>' + 
                '<span class="gks_pos_item_fpa_in_price">' + (data.list[i].vp*100) + '%</span>' +
              '</div>' +
              
              '</div>';
            }
            
            out+='<div class="products_br_new" style="clear: both;"></div>';
            
            
            if (data.list.length==50) {
              out+='<div class="products_loadmore"><button id="products_loadmore_button" class="btn btn-primary">'+gks_lang('Περισσότερα')+'</button></div>';
            }
            
            
            if ($('.products_br').length==0) {
              $('#gks_pos_products').html(out);
            } else {
              $('.products_br').before(out);
            }
            $('.products_br').remove();
            $('.products_br_new').removeClass('products_br_new').addClass('products_br');
            
            $('#products_loadmore_button').click(products_loadmore_button_click);
            
            myresize();

            if (this.gks_page>0) {
              $('#gks_pos_panel_products').animate({
                  scrollTop:  $('#gks_pos_panel_products').scrollTop() - $('#gks_pos_panel_products').offset().top + $('.myproduct[data-id=' + data.list[0].id + ']').offset().top - 43
              }, 500 ); 
            }
            
            $('.myproduct[data-page=' + this.gks_page + ']').click(myproduct_click);
            
            if (this.gks_has_enter_at_end && $('.myproduct').length==1) {
              $('.myproduct').click();
              myterm_key_old='';
              $('#gks_pos_products_search').val('');
            }
            
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});
  }
  get_products();
  
  
  var timer_gks_pos_products_search=null;
  
  $('#gks_pos_products_search').on('change keyup paste', function() {
    last_action_time = new Date();
    //console.log('gks_pos_products_search',event.which);
    has_enter_at_end=false;
    if (typeof event.which !=='undefined' && event.which==13) has_enter_at_end=true;
    
    
    myterm=$('#gks_pos_products_search').val().trim();
    if (myterm.length >= 1 && myterm.length <= 2) return;
    
    if (myterm==myterm_key_old) {
      if (has_enter_at_end && $('.myproduct').length==1 && myterm!='') {
        $('.myproduct').click();
        $('#gks_pos_products_search').val('');
      }
      return;
    }
    myterm_key_old=myterm;
    
    if (myterm=='') {
      //console.log('sssss');
      products_page=0;get_products();
      return;
    }
    setTimeout(function() {
      //myterm_key_old='';
    },10000);
    
    if (timer_gks_pos_products_search!=null) {
      clearTimeout(timer_gks_pos_products_search);  
    }
    timer_gks_pos_products_search=setTimeout(function() {
      gks_pos_products_search_run(myterm,has_enter_at_end);
    },200);
    
  });
  
  var db_result=[];
  var db_result_from='';
  function gks_pos_products_search_run(myterm,has_enter_at_end) {
    //console.log('gks_pos_products_search_run');
    //myterm=$('#gks_pos_products_search').val().trim();
    //if (myterm.length >= 1 && myterm.length <= 2) return;
    //console.log(myterm);
    if (from_php_pos_indexeddb) {
      db_result=[];
      gks_pos_products_search_run_sku(myterm,has_enter_at_end);
    } else {
      products_page=0;get_products(has_enter_at_end);
    }
    
  }

  function gks_pos_products_search_run_sku(myterm,has_enter_at_end) {
    var transaction = gks_db.transaction(['products'], 'readonly');
    transaction.oncomplete = function(event) {
      if (db_result.length>0) {
        db_result_from='sku';
        gks_pos_products_search_render(0,has_enter_at_end);
      } else {
        gks_pos_products_search_run_gtin(myterm,has_enter_at_end);
      }
    };
    var objectStore = transaction.objectStore('products');
    var index = objectStore.index('search_sku');
    index.get(greeklish(myterm).toLowerCase()).onsuccess = (event) => {
      if (typeof event.target.result !='undefined') db_result.push(event.target.result);
    };
  }

  function gks_pos_products_search_run_gtin(myterm,has_enter_at_end) {
    var transaction = gks_db.transaction(['products'], 'readonly');
    transaction.oncomplete = function(event) {
      if (db_result.length>0) {
        db_result_from='gtin';
        gks_pos_products_search_render(0,has_enter_at_end);
      } else {
        gks_pos_products_search_run_upc(myterm,has_enter_at_end);
      }
    };
    var objectStore = transaction.objectStore('products');
    var index = objectStore.index('search_gtin');
    index.get(myterm).onsuccess = (event) => {
      if (typeof event.target.result !='undefined') db_result.push(event.target.result);
    };
  }
  function gks_pos_products_search_run_upc(myterm,has_enter_at_end) {
    var transaction = gks_db.transaction(['products'], 'readonly');
    transaction.oncomplete = function(event) {
      if (db_result.length>0) {
        db_result_from='upc';
        gks_pos_products_search_render(0,has_enter_at_end);
      } else {
        gks_pos_products_search_run_ean(myterm,has_enter_at_end);
      }
    };
    var objectStore = transaction.objectStore('products');
    var index = objectStore.index('search_upc');
    index.get(myterm).onsuccess = (event) => {
      if (typeof event.target.result !='undefined') db_result.push(event.target.result);
    };
  }
  function gks_pos_products_search_run_ean(myterm,has_enter_at_end) {
    var transaction = gks_db.transaction(['products'], 'readonly');
    transaction.oncomplete = function(event) {
      if (db_result.length>0) {
        db_result_from='ean';
        gks_pos_products_search_render(0,has_enter_at_end);
      } else {
        gks_pos_products_search_run_isbn(myterm,has_enter_at_end);
      }
    };
    var objectStore = transaction.objectStore('products');
    var index = objectStore.index('search_ean');
    index.get(myterm).onsuccess = (event) => {
      if (typeof event.target.result !='undefined') db_result.push(event.target.result);
    };
  }
  function gks_pos_products_search_run_isbn(myterm,has_enter_at_end) {
    var transaction = gks_db.transaction(['products'], 'readonly');
    transaction.oncomplete = function(event) {
      if (db_result.length>0) {
        db_result_from='isbn';
        gks_pos_products_search_render(0,has_enter_at_end);
      } else {
        gks_pos_products_search_run_code(myterm,has_enter_at_end);
      }
    };
    var objectStore = transaction.objectStore('products');
    var index = objectStore.index('search_isbn');
    index.get(myterm).onsuccess = (event) => {
      if (typeof event.target.result !='undefined') db_result.push(event.target.result);
    };
  }
  function gks_pos_products_search_run_code(myterm,has_enter_at_end) {
    var transaction = gks_db.transaction(['products'], 'readonly');
    transaction.oncomplete = function(event) {
      if (db_result.length>0) {
        db_result_from='code';
        gks_pos_products_search_render(0,has_enter_at_end);
      } else {
        gks_pos_products_search_run_descr(myterm,has_enter_at_end);
      }
    };
    
    var objectStore = transaction.objectStore('products');
    var range = IDBKeyRange.bound(greeklish(myterm).toLowerCase(), greeklish(myterm).toLowerCase() + '\uffff');
    var index = objectStore.index('search_code');
    index.openCursor(range).onsuccess = function(event) {
      var cursor = event.target.result;
      if(cursor) {
        db_result.push(cursor.value);
        cursor.continue();
      }
    };
        
//    var objectStore = transaction.objectStore('products');
//    var index = objectStore.index('search_code');
//    index.get(myterm).onsuccess = (event) => {
//      if (typeof event.target.result !='undefined') db_result.push(event.target.result);
//    };
  }
  function gks_pos_products_search_run_descr(myterm,has_enter_at_end) {
    myterm=cleartonous(myterm);
    var transaction = gks_db.transaction(['products'], 'readonly');
    transaction.oncomplete = function(event) {
      if (db_result.length>0) {
        db_result_from='descr';
        gks_pos_products_search_render(0,has_enter_at_end);
      } else {
        products_page=0;get_products(has_enter_at_end);
      }
    };
    
    var objectStore = transaction.objectStore('products');
    var range = IDBKeyRange.bound(greeklish(myterm).toLowerCase(), greeklish(myterm).toLowerCase() + '\uffff');
    var index = objectStore.index('search_descr');
    index.openCursor(range).onsuccess = function(event) {
      var cursor = event.target.result;
      if(cursor) {
        db_result.push(cursor.value);
        cursor.continue();
      }
    };

  }  

  
  function gks_pos_products_search_render(r_page,has_enter_at_end) {
    //console.log(db_result);
    if (r_page==0) {
      $('#gks_pos_products').html(gks_lang('Φόρτωση προϊόντων')+' ...');
    }
    //$('.products_loadmore').html('<img src="img/wait.gif">');
    $('.products_loadmore').remove();
    
    out='';cc=0;
    if (r_page==0) {
      if (db_result.length>1) {
        out+='<div id="products_num_records">'+gks_lang('Βρέθηκαν [1] προϊόντα').replaceAll('[1]',db_result.length)+'</div>';
      } else if (db_result.length==1) {
        out+='<div id="products_num_records">'+gks_lang('Βρέθηκε το προϊόν')+'</div>';
      } else if (db_result.length==0) {
        out+='<div id="products_num_records">'+gks_lang('Δεν βρέθηκαν προϊόντα')+'</div>';
      }
    }
    var id_to_scroll=0;
    for (i = (r_page*50); i < db_result.length; i++) {
      cc++;
      
      if (db_result[i].myimgurl=='') myimgurl='/my/img/product.png'; else myimgurl=db_result[i].myimgurl;
      
      out+='<div class="myproduct" data-page="' + r_page + '" data-id="' + db_result[i].id+ '" data-code="' + db_result[i].code + '" data-priceperitem="' + db_result[i].price + '" data-vatperitem="' + db_result[i].vat + '" data-ovatperitem="' + db_result[i].ovat + '" data-vatpososto="' + db_result[i].vp + '" data-dbcc="' + i + '">' +
      '<img src="' + myimgurl + '" class="myproduct_img" border="0">' + 
      '<div class="myproduct_descr">' + db_result[i].descr + '</div>' +
      '<div class="myproduct_price">' +
        '<span class="myproduct_price_inner">' + db_result[i].price.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, from_php_GKS_NUMBER_FORMAT_DECIMAL, from_php_GKS_NUMBER_FORMAT_THOUSAND) + '</span>' + 
        '<span class="gks_pos_item_fpa_in_price">' + (db_result[i].vp*100) + '%</span>' +
      '</div>' +
      
      '</div>';
      
      if (cc>=50) break;
    }
    
    out+='<div class="products_br_new" style="clear: both;"></div>'; 
    
    if (((r_page+1)*50) < db_result.length) {
      out+='<div class="products_loadmore"><button id="products_loadmore_button_fromdb" data-page-to-show="' + (r_page+1) + '" class="btn btn-primary">'+gks_lang('Περισσότερα')+'</button></div>';
    }
    
    if ($('.products_br').length==0) {
      $('#gks_pos_products').html(out);
    } else {
      $('.products_br').before(out);
    }
    $('.products_br').remove();

    $('.products_br_new').removeClass('products_br_new').addClass('products_br');
    
    $('#products_loadmore_button_fromdb').click(products_loadmore_button_fromdb_click);
    
    myresize();
    
    if (r_page==0) {
      $('#gks_pos_panel_products').animate({
          scrollTop:  0
      }, 500 ); 
    } else {
      $('#gks_pos_panel_products').animate({
          scrollTop:  $('#gks_pos_panel_products').scrollTop() - $('#gks_pos_panel_products').offset().top + $('.myproduct[data-id=' + db_result[r_page*50].id + ']').offset().top - 43
      }, 500 ); 
      
    }
    
    $('.myproduct[data-page=' + r_page + ']').click(myproduct_click);                
            
    if (db_result.length==1 && ['code','sku','gtin','upc','ean','isbn'].includes(db_result_from)) {
      $('.myproduct').click();
      myterm_key_old='';
      $('#gks_pos_products_search').val('');
    }
    
    //if (db_result.length==1 && r_page==0 && has_enter_at_end) {
    //  $('#gks_pos_products_search').val('');
    //}
  }
  
  
  function products_loadmore_button_click() {
    //console.log('products_loadmore_button_click');
    products_page++;
    myterm_key_old='-';
    get_products();
  }
  
  function products_loadmore_button_fromdb_click() {
    r_page=parseInt($(this).attr('data-page-to-show'));
    if (isNaN(r_page)) return;
    if (r_page<=0) return;
    gks_pos_products_search_render(r_page,false);
  }
  
  function gks_pos_item_quantity_minus_click() {
    base_elem=$(this).parent().parent().parent();
    aa=parseInt(base_elem.attr('data-aa')); if (isNaN(aa)) aa=-1;
    //console.log(aa);
    if (aa<0) return;
    quantity=parseFloat(base_elem.attr('data-quantity')); if (isNaN(quantity)) quantity=0;
    quantity--;
    if (quantity<=0) return;
    base_elem.attr('data-quantity',quantity);
    $(this).parent().find('.gks_pos_item_quantity_val').html(quantity);
    $(this).parent().parent().find('.gks_pos_item_quantity_in_descr').html('x' + quantity);
    calc_pliroteo();
  }
  function gks_pos_item_quantity_plus_click() {
    base_elem=$(this).parent().parent().parent();
    aa=parseInt(base_elem.attr('data-aa')); if (isNaN(aa)) aa=-1;
    //console.log(aa);
    if (aa<0) return;
    quantity=parseFloat(base_elem.attr('data-quantity')); if (isNaN(quantity)) quantity=0;
    quantity++;
    //if (quantity<=0) return;
    base_elem.attr('data-quantity',quantity);
    $(this).parent().find('.gks_pos_item_quantity_val').html(quantity);
    $(this).parent().parent().find('.gks_pos_item_quantity_in_descr').html('x' + quantity);

    calc_pliroteo();
  }
  
    
  
  function gks_pos_item_delete_click() {
    aa=parseInt($(this).parent().parent().parent().attr('data-aa')); if (isNaN(aa)) aa=-1;
    if (aa<0) return;
    //console.log(aa);
    $('.gks_pos_item[data-aa='+aa+']').remove();
    if (gks_pos_settings.audio) new Audio(audio_delete).play();
    calc_pliroteo();    
  }
  
  var sel_aa=0;
  var sel_quantity=0;
  var sel_priceperitem=0;
  var sel_vatpososto=0;
  
  function gks_pos_item_edit_price_click() {
    base_elem=$(this).parent().parent().parent();
    aa=parseInt(base_elem.attr('data-aa')); if (isNaN(aa)) aa=-1;
    if (aa<0) return;
    quantity=parseFloat(base_elem.attr('data-quantity')); if (isNaN(quantity)) quantity=0;
    priceperitem=parseFloat(base_elem.attr('data-priceperitem')); if (isNaN(priceperitem)) priceperitem=0;
    vatpososto=parseFloat(base_elem.attr('data-vatpososto')); if (isNaN(vatpososto)) vatpososto=0;
    
    if (quantity<=0) quantity=1;
    
    sel_aa=aa;
    sel_quantity=quantity;
    sel_priceperitem=priceperitem;
    sel_vatpososto=vatpososto;    
    
    //console.log(aa,quantity,priceperitem,vatpososto);
    $('#dialog_price_change_item').val(priceperitem);
    $('#dialog_price_change_label_quantity').html(quantity);
    $('#dialog_price_change_total').val(quantity*priceperitem);
    
    www=$('#dialog_price_change').width();
    hhh=$('#dialog_price_change').height();
    mywidth=$(window).width();
    myheight=$(window).height();
    mleft=((mywidth-www)/2).myround(0);
    if (mywidth>600 && gks_is_mobile_or_tablet()==false) {
      mtop=((myheight-hhh)/2).myround(0);
    } else {
      mtop=60;
    }
    $('#dialog_price_change').css({left: mleft + 'px',top :mtop + 'px'});
    $('#dialog_price_change_back').show(); 
    $('#dialog_price_change_item').select();
  }

  $('.gks_pos_item_quantity_minus').click(gks_pos_item_quantity_minus_click);
  $('.gks_pos_item_quantity_plus').click(gks_pos_item_quantity_plus_click);
  $('.gks_pos_item_delete').click(gks_pos_item_delete_click);
  $('.gks_pos_item_edit_price').click(gks_pos_item_edit_price_click);
  
  $('#dialog_price_change_ok').click(function() {
    
    base_elem=$('.gks_pos_item[data-aa=' + sel_aa + ']');
    if (base_elem.length!=1) return;
    
    old_ovatperitem=parseFloat(base_elem.attr('data-ovatperitem'));
    if (isNaN(old_ovatperitem)) old_ovatperitem=0;

    totalprice=parseFloat($('#dialog_price_change_total').val());if (isNaN(totalprice)) totalprice=0;
    //itemprice=parseFloat($('#dialog_price_change_item').val());if (isNaN(itemprice)) itemprice=0;
    itemprice=totalprice/sel_quantity;
    vatperitem=((sel_vatpososto*(itemprice-old_ovatperitem))/(1+sel_vatpososto));//.myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);


    new_ovatperitem=old_ovatperitem;
    old_priceperitem=parseFloat(base_elem.attr('data-priceperitem'));
    if (isNaN(old_priceperitem)) old_priceperitem=0;
    if (old_ovatperitem!=0 && old_priceperitem!=0) {
      new_ovatperitem=itemprice*old_ovatperitem/old_priceperitem;
    }
    
    
    
    base_elem.attr('data-vatperitem',vatperitem).attr('data-priceperitem',itemprice).attr('data-totalprice',totalprice).attr('data-ovatperitem',new_ovatperitem);
    base_elem.find('.gks_pos_item_price_val').html(totalprice.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, from_php_GKS_NUMBER_FORMAT_DECIMAL, from_php_GKS_NUMBER_FORMAT_THOUSAND));
    $('#dialog_price_change_back').hide();
    calc_pliroteo();
    
  });
  
  $('#dialog_price_change_item').on('input change keyup paste', function() {
    need_save=true;
    val=parseFloat($(this).val()); if (isNaN(val)) val=0;
    val=(val*quantity).myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
    $('#dialog_price_change_total').val(val);
  });
  $('#dialog_price_change_total').on('input change keyup paste', function() {
    need_save=true;
    val=parseFloat($(this).val()); if (isNaN(val)) val=0;
    val=(val/quantity).myround(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);;
    $('#dialog_price_change_item').val(val);
  });
  
  $('#dialog_price_change_item').keyup(function(event) {
    if (event != undefined && event.which != undefined && event.which == 13) {
      $('#dialog_price_change_total').select();
    }    
  });
  $('#dialog_price_change_total').keyup(function(event) {
    if (event != undefined && event.which != undefined && event.which == 13) {
      $('#dialog_price_change_ok').click();
    }    
  });
  
  
  function calc_pliroteo() {
    need_save=true;
    var mytotal=0;
    var myvattotal=0;
    var myovattotal=0;
    $('.gks_pos_item').each(function() {
      quantity=parseFloat($(this).attr('data-quantity')); if (isNaN(quantity)) quantity=0;
      priceperitem=parseFloat($(this).attr('data-priceperitem')); if (isNaN(priceperitem)) priceperitem=0;
      itemprice=quantity*priceperitem;
      $(this).attr('data-totalprice',itemprice);
      $(this).find('.gks_pos_item_price_val').html(itemprice.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, from_php_GKS_NUMBER_FORMAT_DECIMAL, from_php_GKS_NUMBER_FORMAT_THOUSAND));
      mytotal+=itemprice; 
      vatperitem=parseFloat($(this).attr('data-vatperitem')); if (isNaN(vatperitem)) vatperitem=0;
      myvattotal+=(quantity*vatperitem);
      ovatperitem=parseFloat($(this).attr('data-ovatperitem')); if (isNaN(ovatperitem)) ovatperitem=0;
      myovattotal+=(quantity*ovatperitem);
    });
    
    $('#gks_pos_panel_bill_total_total_value').html(mytotal.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, from_php_GKS_NUMBER_FORMAT_DECIMAL, from_php_GKS_NUMBER_FORMAT_THOUSAND));
    $('#gks_pos_panel_bill_total_tax_value').html(myvattotal.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, from_php_GKS_NUMBER_FORMAT_DECIMAL, from_php_GKS_NUMBER_FORMAT_THOUSAND));
    $('#gks_pos_panel_bill_total_otax_value').html(myovattotal.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, from_php_GKS_NUMBER_FORMAT_DECIMAL, from_php_GKS_NUMBER_FORMAT_THOUSAND));

    if (myovattotal==0) {
      $('#gks_pos_panel_bill_total_otax_value').html('').hide(); $('#gks_pos_panel_bill_total_otax_label').hide();
    } else {
      $('#gks_pos_panel_bill_total_otax_value').show(); $('#gks_pos_panel_bill_total_otax_label').show();
    }
    
    
  }
  
  var list_aa=0;
  function myproduct_click() {
    last_action_time = new Date();
    //console.log('myproduct_click');
    product_id=parseInt($(this).attr('data-id')); if (isNaN(product_id)) product_id=0;
    product_code=$(this).attr('data-code').trim();
    product_descr=$(this).find('.myproduct_descr').html();
    priceperitem=parseFloat($(this).attr('data-priceperitem')); if (isNaN(priceperitem)) priceperitem=0;
    vatperitem=parseFloat($(this).attr('data-vatperitem')); if (isNaN(vatperitem)) vatperitem=0;
    ovatperitem=parseFloat($(this).attr('data-ovatperitem')); if (isNaN(ovatperitem)) ovatperitem=0;
    vatpososto=parseFloat($(this).attr('data-vatpososto')); if (isNaN(vatpososto)) vatpososto=0;
    vatpososto_html=(vatpososto*100) + '%';
    
    posotita=1;
    totalprice=priceperitem;
    
    
    if (gks_pos_settings.layout=='numpad' && numpad_text!='') {
      val_p=0;val_q=1;
      parts=numpad_text.split(' x ');
      if (parts.length>=1) {
        parts[0]=parts[0].replaceAll(',','.');
        if (parts[0]=='') {
          val_p=priceperitem; // einai tou stil xoris ajia ' x 2'
        } else {
          val_p=parseFloat(parts[0]);
          if (isNaN(val_p)) val_p=priceperitem;
        }
        if (val_p==0) val_p=priceperitem;
      } 
      if (parts.length==2) {
        parts[1]=parts[1].replaceAll(',','.');
        val_q=parseFloat(parts[1]);
        
      }
      if (val_p>0 && val_q>0) {
        old_priceperitem=priceperitem;
        
        
        totalprice=val_p*val_q;
        priceperitem=val_p;
        //vatperitem=priceperitem*vatpososto;
        
        if (ovatperitem!=0 && old_priceperitem!=0) {
          new_ovatperitem=(priceperitem*ovatperitem)/old_priceperitem;
          ovatperitem=new_ovatperitem;
        }        
        
        vatperitem=((vatpososto*(priceperitem-ovatperitem))/(1+vatpososto));
        posotita=val_q;
        

        
      }
      numpad_text='';
      $('#gks_pos_panel_numpad_header2').html('');
      //console.log(numpad_text,val_p,val_q);
    }
    
    use_exist_line=false;
    if (gks_pos_settings.check_exist_elem=='plusposotita') {
      exist_elem=null;
      
      $('.gks_pos_item[data-id=' + product_id + ']').each(function() {
        curr_priceperitem=parseFloat($(this).attr('data-priceperitem'));
        if (isNaN(curr_priceperitem)) curr_priceperitem=0;
        if (curr_priceperitem>0 && priceperitem>0 && curr_priceperitem==priceperitem) {
          exist_elem=$(this);
          use_exist_line=true;
          return;
        }
      });
    }
    
    if (use_exist_line) {
      exist_elem.find('.gks_pos_item_quantity_plus').click();
      
      $('#gks_pos_panel_bill_list').animate({
          scrollTop:  $('#gks_pos_panel_bill_list').scrollTop() - $('#gks_pos_panel_bill_list').offset().top + exist_elem.offset().top 
      }, 300 ); 
      
    } else {
    
      list_aa++;
      html=
      '<div class="gks_pos_item" data-aa="' + list_aa + '" data-id="' + product_id + '" data-priceperitem="' + priceperitem + '" data-totalprice="' + totalprice + '" data-vatperitem="' + vatperitem + '" data-ovatperitem="' + ovatperitem + '" data-vatpososto="' + vatpososto + '" data-quantity="' + posotita + '">' +
        '<div class="gks_pos_item_div">' +
          '<div class="gks_pos_item_product">' +
            '<span class="gks_pos_item_product_descr">' + product_descr + '</span>' +
            '<span class="gks_pos_item_fpa_in_descr">' + vatpososto_html + '</span>' +
            '<span class="gks_pos_item_quantity_in_descr">x' + posotita + '</span>' +
            
          '</div>' +
          '<div class="gks_pos_item_quantity">' +
            '<i class="fas fa-minus-circle gks_pos_item_quantity_minus"></i>' +
            ' <span class="gks_pos_item_quantity_val">' + posotita + '</span> ' +
            '<i class="fas fa-plus-circle  gks_pos_item_quantity_plus"></i>' +
          '</div>' +
          '<div class="gks_pos_item_price">' +
            (from_php_pos_user_can_change_prices ? '<i class="fas fa-pencil-alt gks_pos_item_edit_price"></i>' : '') +
            '<span class="gks_pos_item_price_val">' + totalprice.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, from_php_GKS_NUMBER_FORMAT_DECIMAL, from_php_GKS_NUMBER_FORMAT_THOUSAND) + '</span>' +
            ' <i class="fas fa-trash-alt gks_pos_item_delete"></i>' +
          '</div>' +
          '<div style="clear: both;"></div>' +
        '</div>' +
      '</div>';
      
      $('#gks_pos_panel_bill_list').append(html);
      
      var elem_insert=$('.gks_pos_item[data-aa=' + list_aa + ']');
      elem_insert.find('.gks_pos_item_quantity_minus').click(gks_pos_item_quantity_minus_click);
      elem_insert.find('.gks_pos_item_quantity_plus').click(gks_pos_item_quantity_plus_click);
      elem_insert.find('.gks_pos_item_delete').click(gks_pos_item_delete_click);
      elem_insert.find('.gks_pos_item_edit_price').click(gks_pos_item_edit_price_click);
      
      
      scrollheight=$('#gks_pos_panel_bill_list')[0].scrollHeight;
      divheight=$('#gks_pos_panel_bill_list').height() + 20;
      //console.log(scrollheight,divheight);
      
      if (scrollheight > divheight) {
        //console.log('scroll');
        $('#gks_pos_panel_bill_list').animate({
            scrollTop: scrollheight - divheight
        }, 300 ); 
      }
    }
    
    //$('#gks_pos_panel_bill_list').animate({
    //    scrollTop:  $('#gks_pos_panel_bill_list').scrollTop() - $('#gks_pos_panel_bill_list').offset().top + elem_insert.offset().top 
    //}, 1000 ); 
    
                
    if (gks_pos_settings.audio) new Audio(audio_added).play();
    calc_pliroteo();
    
  }
  
  
  function gks_pos_menu_reset_click() {
    last_action_time = new Date();
    
    $('.gks_pos_item').remove();
    $('#gks_pos_panel_bill_total_total_value').html('');
    $('#gks_pos_panel_bill_total_tax_value').html('');
    $('#gks_pos_panel_bill_total_otax_value').html('').hide(); $('#gks_pos_panel_bill_total_otax_label').hide();
    list_aa=0;
    
    $('#gks_pos_products_search').val('');
    $('#gks_merchant_ref_trns_text').val(gks_pos_settings.merchant_ref ? gks_pos_settings.merchant_ref_def_value : '');
    
    $('#gks_pos_panel_bill_customer_icon').attr('data-sel_user_id','0').attr('data-sel_user_nickname','');
    
    $('#gks_pos_panel_bill_customer_inner').html($.base64.decode($('#gks_pos_panel_bill_customer_icon').attr('data-def_user_nickname')));
    customer_has_open=false;

    
    if ($('#pway' + from_php_pway_id).length>0) {
      $('#pway' + from_php_pway_id).click();
      $('#gks_pos_panel_pway').animate({
          scrollTop:  $('#gks_pos_panel_pway').scrollTop() - $('#gks_pos_panel_pway').offset().top + $('#pway' + from_php_pway_id).offset().top  - $('#gks_pos_panel_pway').height()/2 +4
      }, 300 );     
    }
    
    
//    products_page=0;
//    if (myterm_key_old!='') {
//      myterm_key_old='--';  
//      get_products(); 
//    }
    myresize();
    from_php_id=-1;
    from_php_pos_step='';
    need_save=false;   
    gks_eftpos_payment_ok=false;
    gks_session_pos_set();
    if (gks_pos_panel_pay_data_html!='') {
      $('#gks_pos_panel_pay_data').html(gks_pos_panel_pay_data_html);
      $('.gks_pos_panel_pay_btn').click(gks_pos_panel_pay_btn_click);
      $('#gks_pos_panel_pay_cancel').click(function() {
        $('#gks_pos_panel_pay').hide();
        fnc_gks_erp_app_mobile_pos_dialog_pay_open(false);
      });
      
      gks_pos_menu_reset_click_otherjs();
      set_def_xxx_terminal();
      
    }
    
    myterm_key_old='-';
    products_page=0;get_products();
    
  }
  $('#gks_pos_menu_reset').click(function() {
    if (gks_pos_settings.audio) new Audio(audio_reset).play();
    gks_pos_menu_reset_click();
  });
  
  
  window.gks_pos_run_submit = function(is_force) {
    last_action_time = new Date();
    var mytotal=0;
    var mysumitems=0;
    var eidi_array=[];
    var zero_ajia=[];
    var aa_cc=0;
    $('.gks_pos_item').each(function() {
      aa_cc++;
      aa=parseInt($(this).attr('data-aa')); if (isNaN(aa)) aa=0;
      product_id=parseInt($(this).attr('data-id')); if (isNaN(product_id)) product_id=0;
      quantity=parseFloat($(this).attr('data-quantity')); if (isNaN(quantity)) quantity=0;
      priceperitem=parseFloat($(this).attr('data-priceperitem')); if (isNaN(priceperitem)) priceperitem=0;
      totalprice=parseFloat($(this).attr('data-totalprice')); if (isNaN(totalprice)) totalprice=0;
      //totalprice=quantity*priceperitem;
      if (totalprice<0.01) zero_ajia.push(aa_cc);
      mytotal+=totalprice; 
      mysumitems+=quantity;
      
      if (quantity>0) {
        item={};
        item.aa=aa;
        item.product_id=product_id;
        item.product_quantity=quantity;
        item.product_priceperitem=priceperitem;
        item.product_totalprice=totalprice;
        eidi_array.push(item);
      }
    });
    
    if (eidi_array.length==0) {
      myalert('error:'+gks_lang('Προσθέστε είδη'));
      return;
    }
    if (zero_ajia.length>0) {
      myalert('error:'+gks_lang('Στην γραμμή [1] το είδος δεν έχει αξία').replaceAll('[1]',zero_ajia[0])); 
      return;
    }
    //console.log(eidi_array);
    //return;
    eidi_array_str = encodeURIComponent($.base64.encode(JSON.stringify(eidi_array)));
    
    
    if ($('input[name=pway]').length>=2) {
      pway=$('input[name=pway]:checked').val();
      if (pway === undefined) {
        myalert('error:'+gks_lang('Επιλέξτε τρόπο πληρωμής'));
        return;      
      }
    } else {
      pway=from_php_pway_id;
    }
    
    
    goto_next_copies=false;
    if ($('#pos_multi_copies').length>=1) {
      pos_multi_copies=parseInt($('#pos_multi_copies').val());if (isNaN(pos_multi_copies)) pos_multi_copies=0;
      if (pos_multi_copies>1) {
        has_error=false;
        if (eidi_array.length>1) {
          has_error=true;
        } else {
          for (i=0;i<eidi_array.length;i++) {
            if (eidi_array[i].product_quantity!=1) {
              has_error=true;
              break;
            }
          }
        }
        if (has_error) {
          myalert('error:'+gks_lang('Για Πολλαπλά αντίγραφα θα πρέπει να έχετε ένα μόνο είδος στο καλάθος με ποσότητα 1'));
          return; 
        }
      }
    }
    
    
    datasend='';
    
    datasend+='&gks_session_pos=' + encodeURIComponent($.base64.encode(gks_session_pos));
    datasend+='&gks_erp_cookie_id=' + encodeURIComponent($.base64.encode(gks_getCookie('gks_erp_cookie_id')));
    datasend+='&gks_run_until=' + encodeURIComponent(gks_run_until);
    
    
    datasend+='&id_pos=' + from_php_id_pos;
    datasend+='&id=' + from_php_id;
    datasend+='&pos_step=' + from_php_pos_step;
    datasend+='&mytotal=' + mytotal;
    datasend+='&eidi_array_str=' + eidi_array_str; 
    datasend+='&js_pos_user_can_change_prices=' + (from_php_pos_user_can_change_prices ? '1' : '0');
    datasend+='&is_force=' + (is_force ? '1' : '0'); 
    datasend+='&gks_pos_client_send_fileto_url=' + encodeURIComponent($.base64.encode(from_php_gks_pos_client_send_fileto_url));
    datasend+='&pway=' + pway; 
    datasend+='&merchant_ref_trns=' + encodeURIComponent($.base64.encode($('#gks_merchant_ref_trns_text').val()));
    
    //console.log(mytotal);
    //console.log(eidi_array);
    
    datasend+='&customer_has_open=' + (customer_has_open ? '1' : '0');
    if (customer_has_open) {
      user_id=parseInt($('#gks_pos_panel_bill_customer_icon').attr('data-sel_user_id'));
      if (isNaN(user_id)) user_id=0;
      
      datasend+='&user_id=' + $('#gks_pos_panel_bill_customer_icon').attr('data-sel_user_id');
      datasend+='&dr_user_first_name='  + encodeURIComponent($.base64.encode($("#dr_user_first_name").val().trim()));
      datasend+='&dr_user_last_name='  + encodeURIComponent($.base64.encode($("#dr_user_last_name").val().trim()));
      datasend+='&dr_user_email='  + encodeURIComponent($.base64.encode($("#dr_user_email").val().trim()));
      datasend+='&dr_user_mobile='  + encodeURIComponent($.base64.encode($("#dr_user_mobile").val().trim()));
      datasend+='&dr_user_lang='  + encodeURIComponent($.base64.encode($("#dr_user_lang").val().trim()));
      datasend+='&dr_user_ma_odos='  + encodeURIComponent($.base64.encode($("#dr_user_ma_odos").val().trim()));
      datasend+='&dr_user_ma_arithmos='  + encodeURIComponent($.base64.encode($("#dr_user_ma_arithmos").val().trim()));
      datasend+='&dr_user_ma_orofos='  + encodeURIComponent($.base64.encode($("#dr_user_ma_orofos").val().trim()));
      datasend+='&dr_user_ma_perioxi='  + encodeURIComponent($.base64.encode($("#dr_user_ma_perioxi").val().trim()));
      datasend+='&dr_user_ma_poli='  + encodeURIComponent($.base64.encode($("#dr_user_ma_poli").val().trim()));
      datasend+='&dr_user_ma_tk='  + encodeURIComponent($.base64.encode($("#dr_user_ma_tk").val().trim()));
      datasend+='&dr_user_ma_country_id='  + encodeURIComponent($("#dr_user_ma_country_id").val().trim());
      datasend+='&dr_user_ma_nomos_id='  + encodeURIComponent($("#dr_user_ma_nomos_id").val().trim());
      
      
      
    } else {
      user_id=parseInt($('#gks_pos_panel_bill_customer_icon').attr('data-def_user_id'));
      if (isNaN(user_id)) user_id=0;
      
    }
    if (user_id<1) {
      myalert('error:'+gks_lang('Επιλέξτε έναν πελάτη'));
      return;       
    }
    
    if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
      datasend+='&gks_erp_app_mobile=1';
      datasend+='&gks_erp_app_mobile_local_printers_length=' + gks_erp_app_mobile_local_printers.length;
    } else {
      if ($('.div_payment_one_terminal_terminal[data-pawid="1"]').length==1) {
        gks_pos_settings.viva_id_asset=$('.div_payment_one_terminal_terminal[data-pawid="1"]').attr('data-asset_id');
        gks_pos_settings.viva_asset_title=$('.div_payment_one_terminal_terminal[data-pawid="1"]').val();
        gks_setCookie('gks_pos_run_settings',JSON.stringify(gks_pos_settings),100*24*60*60);
      }
      if ($('.div_payment_one_terminal_terminal[data-pawid="6"]').length==1) {
        gks_pos_settings.worldline_id_asset=$('.div_payment_one_terminal_terminal[data-pawid="6"]').attr('data-asset_id');
        gks_pos_settings.worldline_asset_title=$('.div_payment_one_terminal_terminal[data-pawid="6"]').val();
        gks_setCookie('gks_pos_run_settings',JSON.stringify(gks_pos_settings),100*24*60*60);
      }      
      
      
    }
    datasend+='&iderpappmobile=' + from_php_iderpappmobile;
    datasend+='&screen_width=' + $(window).width();
    datasend+='&screen_height=' + $(window).height();
    
    datasend+='&device_type=' + encodeURIComponent($.base64.encode(gks_device_type));


    if (my_gps_location_res=='ok') {
      datasend+='&my_gps_location_lat=' + encodeURIComponent($.base64.encode(my_gps_location_lat + ''));
      datasend+='&my_gps_location_lng=' + encodeURIComponent($.base64.encode(my_gps_location_lng + ''));
    }
    
    //console.log('admin-pos-run-exec.php');
    //console.log('gks_run_until',gks_run_until);
    //console.log('from_php_pos_step',from_php_pos_step);
    //console.log(datasend);
    
    
    $('body').addClass('myloading');
    
    $.ajax({
			url: '/my/admin-pos-run-exec.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('body').removeClass('myloading');
			  if (typeof jqXHR.responseText !== 'undefined' && jqXHR.responseText.trim()!=='') {
			    myerror_show=jqXHR.responseText
			  } else {
			    myerror_show=gks_lang('Υπάρχει σύνδεση με το internet ;');
			  }
				myalert('error:' + myerror_show);
			},				
			success: function(data) {
				$('body').removeClass('myloading');
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
          if (data.insertid !== undefined) from_php_id=data.insertid;
          if (data.pos_step !== undefined) from_php_pos_step=data.pos_step;
				  //console.log(data);
				  
					if (data.success == true) {
					  
  					//myalert('ok:' + 'OK');
  					need_save=false;
            
            if (data.thermal_url_file!== undefined && data.thermal_url_file != '') {
              fnc_gks_erp_app_mobile_thermal_url_file_run(data.thermal_url_file);
            }
            
            
            goto_next_copies=false;
            if ($('#pos_multi_copies').length>=1) {
              pos_multi_copies=parseInt($('#pos_multi_copies').val());if (isNaN(pos_multi_copies)) pos_multi_copies=0;
              if (pos_multi_copies>1) {
                pos_multi_copies--;
                $('#pos_multi_copies').val(pos_multi_copies);
                if ($.base64.decode(data.save_but_message).includes('alert-danger')==false) {
                  goto_next_copies=true;
                }
              }
            }            
					  if (gks_run_until!='') goto_next_copies=false;
					  
  					if (data.save_but_message!='') {
  					  if ($.base64.decode(data.message)=='ok') {
  					    //myalert('ok:' + $.base64.decode(data.save_but_message), $.base64.decode(data.doc_item_url),true);
  					    if (goto_next_copies) {
  					      
  					      setTimeout(function() {
  					        $('body').addClass('myloading');
                    from_php_id=-1;
                    from_php_pos_step='';
					          gks_session_pos_set();
  					        gks_pos_run_submit(true);
  					      }, 100);
  					      
  					    } else {

    					    if (gks_run_until=='') {
    					      gks_pos_menu_reset_click();
    					    } else {
    					      gks_run_until='';
    					      $('#gks_pos_panel_pay_close').hide();
    					      $('.gks_pos_panel_pay_footer').remove();  
    					      if (gks_pos_settings.min_clicks==false) {
                      $('.div_payment_one_terminal_start').prop('disabled',false);
                      $('.div_payment_one_terminal_terminal').prop('disabled',false);
                    } else {
                      //$('.div_payment_one_terminal_start').prop('disabled',true);
                      //$('.div_payment_one_terminal_terminal').prop('disabled',true);
                    }
    					      need_save=true;
    					    }
    					    
  					      mymessageshow=$.base64.decode(data.save_but_message);
  					      if (mymessageshow.includes('alert-danger')) {
  					        myalert('ok:' + mymessageshow);
  					        document.querySelector('#eft-pos-done').play();
  					      } else {
  					        parts=gks_eftpos_step.split('|');
  					        gks_eftpos_step='';
  					        if (gks_pos_settings.min_clicks==true && parts.length==2 && parts[0]=='div_payment_one_terminal_start') {
  					          $('.div_payment_one_terminal[data-one_pway=' + parts[1] + ']').find('.div_payment_one_terminal_start').click();
  					        } else {
      					      if (gks_pos_settings.msg_ok_show) {
    					          myalert('ok:' + mymessageshow);
    					          
    					          
    					          document.querySelector('#eft-pos-done').play();
    					          $('#acc_inv_pos_run8_sms_run').click(acc_inv_pos_run8_sms_run_click);
    					          $('.acc_inv_pos_run7 img.accinvposimgqrcode').click(accinvposimgqrcode_click);
    					          
    					        }
    					      }
  					        
  					      }
    					    
    					    

    					        					    
    					    
    					    
    					  }
  					  } else {
  					    //myalert('error:' + $.base64.decode(data.save_but_message), $.base64.decode(data.doc_item_url),true);
  					    myalert('error:' + $.base64.decode(data.save_but_message));
  					  }
  					} else {
  					  gks_pos_menu_reset_click();
    				}
      			
      			            
					} else {
					  if (data.for_force !== undefined) {
					    myconfirm($.base64.decode(data.message),'gks_pos_run_submit_force');
					  } else if (data.for_retry !== undefined) {
					    if (data.save_but_message !== undefined && data.save_but_message!='') {
					      message_out=$.base64.decode(data.save_but_message) + '<br><br>' + gks_lang('<b>ΟΚ</b>: Νέα προσπάθεια<br><b>Άκυρο</b>: Ακύρωση προσπάθειας και δημιουργία νέου');
					                //mymessage, function_ok              ,delete_model,delete_id,delete_backurl,param1,param2,param3,fnc_deleteafter,function_cancel
					      myconfirm(message_out,'gks_pos_run_submit_retry',''          ,''       ,''            ,''    ,''    ,''    ,''             ,'gks_pos_run_submit_retry_cancel');
					    } else {
					      myconfirm($.base64.decode(data.message),'gks_pos_run_submit_retry');
					    }
            } else {
						  myalert('error:' + $.base64.decode(data.message));
						}
					}
        }
      }
    });
  }
  
  $('#gks_pos_save').click(function () {
    last_action_time = new Date();
    num_items=0;
    $('.gks_pos_item').each(function() {
      quantity=parseFloat($(this).attr('data-quantity')); if (isNaN(quantity)) quantity=0;
      if (quantity>0) num_items++;
    });
    if (num_items<=0) {
      myalert('error:'+gks_lang('Προσθέστε είδη'));
      return;
    }
        
    if (gks_pos_settings.pay_mode=='twosteps') {
      if (gks_pos_settings.min_clicks) {
        $('.div_payment_one_terminal_start').prop('disabled',false);
        $('.div_payment_one_terminal_terminal').prop('disabled',false);
      } else {
        $('.div_payment_one_terminal_start').prop('disabled',true);
        $('.div_payment_one_terminal_terminal').prop('disabled',true);
      }
      
      $('#gks_pos_panel_pay_close').show();
      $('#gks_pos_panel_pay').show();
      fnc_gks_erp_app_mobile_pos_dialog_pay_open(true);
      
    } else {
      gks_pos_run_submit(false);
    }
  });
  
  window.gks_pos_run_submit_force_run = function() {
    gks_pos_run_submit(true);
  }
  window.gks_pos_run_submit_retry_run = function() {
    gks_pos_run_submit(true);
  }

  window.gks_pos_run_submit_retry_cancel_run= function() {
    //console.log('gks_pos_run_submit_retry_cancel_run');
    gks_pos_menu_reset_click();
  }
  
  
  $('#gks_pos_menu_bars').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
  $('#gks_pos_menu_title').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
  $('#gks_pos_menu_screen').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
  $('#gks_pos_menu_print_x').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
  $('#gks_pos_menu_reprint').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
  $('#gks_pos_menu_reset').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
  $('#gks_pos_menu_exit').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
  $('.row_gks_width_active_icon').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
  
  $('.gks_pos_panel_numpad_btn > div[data-key=C]').parent().tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
  $('.gks_pos_panel_numpad_btn > div[data-key=X]').parent().tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
  $('.gks_pos_panel_numpad_btn > div[data-key=B]').parent().tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
  $('.gks_pos_panel_numpad_btn > div[data-key="."]').parent().tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
 
  var elem_document_screen = document.documentElement;
  $('#gks_pos_menu_screen').click(function() {
    val=$(this).attr('data-val');
    if (val=='0') {
      $(this).attr('data-val','1').attr('class','fas fa-compress');
      if (elem_document_screen.requestFullscreen) {
        elem_document_screen.requestFullscreen();
      } else if (elem.webkitRequestFullscreen) { /* Safari */
        elem_document_screen.webkitRequestFullscreen();
      } else if (elem.msRequestFullscreen) { /* IE11 */
        elem_document_screen.msRequestFullscreen();
      }      
    } else {
      $(this).attr('data-val','0').attr('class','fas fa-expand');
      if (document.exitFullscreen) {
        document.exitFullscreen();
      } else if (document.webkitExitFullscreen) { /* Safari */
        document.webkitExitFullscreen();
      } else if (document.msExitFullscreen) { /* IE11 */
        document.msExitFullscreen();
      }      
    }  
  });

  if (document.addEventListener)   {
   document.addEventListener('fullscreenchange', exitFullScreenHandler);
   document.addEventListener('mozfullscreenchange', exitFullScreenHandler, false);
   document.addEventListener('MSFullscreenChange', exitFullScreenHandler, false);
   document.addEventListener('webkitfullscreenchange', exitFullScreenHandler, false);
  }
  
  function exitFullScreenHandler() {
    if (!document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
      $('#gks_pos_menu_screen').attr('data-val','0').attr('class','fas fa-expand');
      //console.log('aaaa');
    } else {
      $('#gks_pos_menu_screen').attr('data-val','1').attr('class','fas fa-compress');
      //console.log('bbbb');
    }
  }  
  
//  $('.gks_pos_panel_numpad_btn > div').hover(
//      function() {
//        $(this).stop().animate({backgroundColor:'#bbbbbb'}, 300);
//      },
//      function () {
//        $(this).stop().animate({backgroundColor:'#eeeeee'}, 100);
//      }
//  );
    
  $('.gks_pos_panel_numpad_btn > div').click(function() {
    last_action_time = new Date();
    mykey=$(this).attr('data-key');
    if (mykey=='') return;
    //console.log(mykey);
    $(this).css('background-color', 'var(--gks_pos_panel_numpad_btn_color1)');
    $(this).stop().animate({backgroundColor:$(":root").css('--gks_pos_panel_numpad_btn_color2')}, 300);
    
      //}, function () {
      //$(this).stop().animate({backgroundColor:'#eeeeee'}, 100);
    if (['0','1','2','3','4','5','6','7','8','9'].includes(mykey)) {
      numpad_text+=mykey;
    } else if (mykey=='X') {
      //if (numpad_text.length>0 && numpad_text.includes('x')==false) {
      if (numpad_text.includes('x')==false) {
        numpad_text+=' x ';
      }
    } else if (mykey=='.') {
      if (numpad_text=='') {
        numpad_text='0,';
      } else {
        parts=numpad_text.split(' x ');
        if (parts.length==1) {
          if (numpad_text.includes(',')) return;
        } else {
          if (parts[1].includes(',')) return;
        }
        
        if (numpad_text.length>=4 && numpad_text.substring(numpad_text.length-3,numpad_text.length)==' x ') {
          numpad_text+='0,';
        } else {
          numpad_text+=',';
        }
      }
    } else if (mykey=='B') {
      if (numpad_text.length>0) {
        if (numpad_text.length>=3 && numpad_text.substring(numpad_text.length-4,numpad_text.length-1)==' x ') {
          numpad_text=numpad_text.substring(0, numpad_text.length-4);
        } else {
          numpad_text=numpad_text.substring(0, numpad_text.length-1);
        } 
      }
    } else if (mykey=='C') {
      numpad_text='';
    } 
    //console.log('|' + numpad_text + '|');
    $('#gks_pos_panel_numpad_header2').html(numpad_text);
    if (gks_pos_settings.audio) new Audio(audio_num).play();
  });
  


    

  $('#gks_pos_menu_bars').click(function() {
    last_action_time = new Date();
    if (gks_pos_settings.layout=='numpad') {
      $('#gks_layout_numpad').prop('checked',true);
    } else {
      $('#gks_layout_normal').prop('checked',true);
    }
    if (gks_pos_settings.check_exist_elem=='addline') {
      $('#gks_check_exist_elem_addline').prop('checked',true);
    } else {
      $('#gks_check_exist_elem_plusposotita').prop('checked',true);
    }
    if (gks_pos_settings.pay_mode=='twosteps') {
      $('#gks_pay_mode_twosteps').prop('checked',true);
    } else {
      $('#gks_pay_mode_fast').prop('checked',true);
    }
    //console.log(gks_pos_settings);
    
    

    if (gks_pos_settings.merchant_ref != $('#gks_merchant_ref_enable').is(':checked')) $('#gks_merchant_ref_enable').click();
    $('#gks_merchant_ref_def_value').val(gks_pos_settings.merchant_ref_def_value);
    
    if (gks_pos_settings.multi_copies != $('#gks_multi_copies_enable').is(':checked')) $('#gks_multi_copies_enable').click();
    if (gks_pos_settings.customer != $('#gks_customer').is(':checked')) $('#gks_customer').click();
    if (gks_pos_settings.msg_ok_show != $('#gks_msg_ok_show').is(':checked')) $('#gks_msg_ok_show').click();
    if (gks_pos_settings.min_clicks != $('#gks_min_clicks').is(':checked')) $('#gks_min_clicks').click();
    if (gks_pos_settings.edit_price != $('#gks_edit_price').is(':checked')) $('#gks_edit_price').click();
    if (gks_pos_settings.show_fpa != $('#gks_show_fpa').is(':checked')) $('#gks_show_fpa').click();
    if (gks_pos_settings.edit_quantity != $('#gks_edit_quantity').is(':checked')) $('#gks_edit_quantity').click();
    if (gks_pos_settings.delete_item != $('#gks_delete_item').is(':checked')) $('#gks_delete_item').click();
    if (gks_pos_settings.audio != $('#gks_audio').is(':checked')) $('#gks_audio').click();
    
    $('#zoom_item_slider').slider('option', 'value', gks_pos_settings.zoom_item);
    zoom_item_slider_handle.text($('#zoom_item_slider').slider('value') + 'px');
    
    if ($(window).width()>$(window).height()) land_port='land'; else land_port='port';
    $('.row_gks_width_active').removeClass('row_gks_width_active');
    
    $('#width_normal_landscape_products_slider').slider('option', 'value', gks_pos_settings.layout_normal.landscape.products);
    width_normal_landscape_products_slider_handle.text($('#width_normal_landscape_products_slider').slider('value') + '%');
    if (gks_pos_settings.layout=='normal' && land_port=='land') $('.row_gks_width_normal_landscape_products .row_gks_width_active_icon').addClass('row_gks_width_active');
    
    $('#width_numpad_landscape_numpad_slider').slider('option', 'value', gks_pos_settings.layout_numpad.landscape.numpad);
    width_numpad_landscape_numpad_slider_handle.text($('#width_numpad_landscape_numpad_slider').slider('value') + '%');
    if (gks_pos_settings.layout=='numpad' && land_port=='land') $('.row_gks_width_numpad_landscape_numpad .row_gks_width_active_icon').addClass('row_gks_width_active');
    
    $('#width_numpad_landscape_products_slider').slider('option', 'value', gks_pos_settings.layout_numpad.landscape.products);
    width_numpad_landscape_products_slider_handle.text($('#width_numpad_landscape_products_slider').slider('value') + '%');
    if (gks_pos_settings.layout=='numpad' && land_port=='land') $('.row_gks_width_numpad_landscape_products .row_gks_width_active_icon').addClass('row_gks_width_active');
    
    $('#width_numpad_portrait_numpad_slider').slider('option', 'value', gks_pos_settings.layout_numpad.portrait.numpad);
    width_numpad_portrait_numpad_slider_handle.text($('#width_numpad_portrait_numpad_slider').slider('value') + '%');
    if (gks_pos_settings.layout=='numpad' && land_port=='port') $('.row_gks_width_numpad_portrait_numpad .row_gks_width_active_icon').addClass('row_gks_width_active');

    if ($('.div_payment_one_terminal_start').length>0) {
      $('#gks_pay_mode_fast').prop('disabled',true);
    } else {
      $('#gks_pay_mode_fast').prop('disabled',false);  
    }

    
    $('#gks_pos_panel_settings').show();

  });
  
  $('#gks_pos_panel_settings_close').click(function() {$('#gks_pos_panel_settings').hide();});
  $('#gks_pos_panel_settings_cancel').click(function() {$('#gks_pos_panel_settings').hide();});  
  
  $('#gks_pos_panel_settings_save').click(function() {
    if ($('#gks_layout_numpad').prop('checked')) gks_pos_settings.layout='numpad';
    else if ($('#gks_layout_normal').prop('checked')) gks_pos_settings.layout='normal';
    
    if ($('#gks_check_exist_elem_addline').prop('checked')) gks_pos_settings.check_exist_elem='addline';
    else if ($('#gks_check_exist_elem_plusposotita').prop('checked')) gks_pos_settings.check_exist_elem='plusposotita';
    
    if ($('#gks_pay_mode_twosteps').prop('checked')) gks_pos_settings.pay_mode='twosteps';
    else if ($('#gks_pay_mode_fast').prop('checked')) gks_pos_settings.pay_mode='fast';
    if ($('.div_payment_one_terminal_start').length>0) {
      gks_pos_settings.pay_mode='twosteps';   
    }
    
    gks_pos_settings.merchant_ref=$('#gks_merchant_ref_enable').is(':checked');
    gks_pos_settings.merchant_ref_def_value=$('#gks_merchant_ref_def_value').val();
    if (gks_pos_settings.merchant_ref==false) gks_pos_settings.merchant_ref_def_value='';
    $('#gks_merchant_ref_trns_text').val(gks_pos_settings.merchant_ref ? gks_pos_settings.merchant_ref_def_value : '');


    gks_pos_settings.multi_copies=$('#gks_multi_copies_enable').is(':checked');
    gks_pos_settings.customer=$('#gks_customer').is(':checked');
    gks_pos_settings.msg_ok_show=$('#gks_msg_ok_show').is(':checked');
    gks_pos_settings.min_clicks=$('#gks_min_clicks').is(':checked');
    gks_pos_settings.edit_price=$('#gks_edit_price').is(':checked');
    gks_pos_settings.show_fpa=$('#gks_show_fpa').is(':checked');
    gks_pos_settings.edit_quantity=$('#gks_edit_quantity').is(':checked');
    gks_pos_settings.delete_item=$('#gks_delete_item').is(':checked');
    gks_pos_settings.audio=$('#gks_audio').is(':checked');
    //console.log(gks_pos_settings.layout);
    
    gks_pos_settings.zoom_item=parseInt($('#zoom_item_slider').slider('value'));
    if (isNaN(gks_pos_settings.zoom_item)) gks_pos_settings.zoom_item=200;

    gks_pos_settings.layout_normal.landscape.products=parseInt($('#width_normal_landscape_products_slider').slider('value'));
    if (isNaN(gks_pos_settings.layout_normal.landscape.products)) gks_pos_settings.layout_normal.landscape.products=50;
        
    gks_pos_settings.layout_numpad.landscape.numpad=parseInt($('#width_numpad_landscape_numpad_slider').slider('value'));
    if (isNaN(gks_pos_settings.layout_numpad.landscape.numpad)) gks_pos_settings.layout_numpad.landscape.numpad=30;
        
    gks_pos_settings.layout_numpad.landscape.products=parseInt($('#width_numpad_landscape_products_slider').slider('value'));
    if (isNaN(gks_pos_settings.layout_numpad.landscape.products)) gks_pos_settings.layout_numpad.landscape.products=30;
        
    gks_pos_settings.layout_numpad.portrait.numpad=parseInt($('#width_numpad_portrait_numpad_slider').slider('value'));
    if (isNaN(gks_pos_settings.layout_numpad.portrait.numpad)) gks_pos_settings.layout_numpad.portrait.numpad=50;
        
    
    
        
    $('#gks_pos_panel_settings').hide();
    myresize();
    myresize();
    gks_setCookie('gks_pos_run_settings',JSON.stringify(gks_pos_settings),100*24*60*60);
    

  });  
  
  
  var zoom_item_slider_handle = $('#zoom_item_slider_handle');
  $('#zoom_item_slider').slider({
    min: 50,
    max: 400,
    value: 100,
    create: function() {
      zoom_item_slider_handle.text( $( this ).slider('value') + 'px');
    },
    slide: function( event, ui ) {
      zoom_item_slider_handle.text( ui.value + 'px' );
    }
  });  
  
  var width_normal_landscape_products_slider_handle = $('#width_normal_landscape_products_slider_handle');
  $('#width_normal_landscape_products_slider').slider({
    min: 10,
    max: 90,
    value: 50,
    create: function() {
      width_normal_landscape_products_slider_handle.text( $( this ).slider('value') + '%');
    },
    slide: function( event, ui ) {
      width_normal_landscape_products_slider_handle.text( ui.value + '%' );
    }
  });   
  
  var width_numpad_landscape_numpad_slider_handle = $('#width_numpad_landscape_numpad_slider_handle');
  $('#width_numpad_landscape_numpad_slider').slider({
    min: 10,
    max: 80,
    value: 50,
    create: function() {
      width_numpad_landscape_numpad_slider_handle.text( $( this ).slider('value') + '%');
    },
    slide: function( event, ui ) {
      width_numpad_landscape_numpad_slider_handle.text( ui.value + '%' );
      max_other = 100-ui.value-10;
      other_value=$('#width_numpad_landscape_products_slider').slider('value');
      //console.log(max_other,other_value);
      if (other_value > max_other) {
        $('#width_numpad_landscape_products_slider').slider('option', 'value', max_other);
        width_numpad_landscape_products_slider_handle.text(max_other + '%');
      }
    }
  });  
  var width_numpad_landscape_products_slider_handle = $('#width_numpad_landscape_products_slider_handle');
  $('#width_numpad_landscape_products_slider').slider({
    min: 10,
    max: 80,
    value: 50,
    create: function() {
      width_numpad_landscape_products_slider_handle.text( $( this ).slider('value') + '%');
    },
    slide: function( event, ui ) {
      width_numpad_landscape_products_slider_handle.text( ui.value + '%' );
      max_other = 100-ui.value-10; 
      other_value=$('#width_numpad_landscape_numpad_slider').slider('value');
      //console.log(max_other,other_value);
      if (other_value > max_other) {
        $('#width_numpad_landscape_numpad_slider').slider('option', 'value', max_other);
        width_numpad_landscape_numpad_slider_handle.text(max_other + '%');
      }
    }
  });  
  
  var width_numpad_portrait_numpad_slider_handle = $('#width_numpad_portrait_numpad_slider_handle');
  $('#width_numpad_portrait_numpad_slider').slider({
    min: 10,
    max: 90,
    value: 50,
    create: function() {
      width_numpad_portrait_numpad_slider_handle.text( $( this ).slider('value') + '%');
    },
    slide: function( event, ui ) {
      width_numpad_portrait_numpad_slider_handle.text( ui.value + '%' );
    }
  });   




  
  $('#gks_pos_panel_pay_close').click(function() {
    $('#gks_pos_panel_pay').hide();
    fnc_gks_erp_app_mobile_pos_dialog_pay_open(false);
  });
  $('#gks_pos_panel_pay_cancel').click(function() {
    $('#gks_pos_panel_pay').hide();
    fnc_gks_erp_app_mobile_pos_dialog_pay_open(false);
  });
  
  
  
    
  function gks_pos_panel_pay_btn_click() {
    last_action_time = new Date();
    pval=parseInt($(this).attr('data-id')); if (isNaN(pval)) pval=0;
    pawid=$(this).attr('data-pawid'); if (isNaN(pawid)) pawid=0;
    if (pval<=0) return;
    //console.log(pval,pawid);
    $('#pway' + pval).prop('checked', true);
    
    if (pawid==0) {
      $('#gks_pos_panel_pay').hide();
      fnc_gks_erp_app_mobile_pos_dialog_pay_open(false);
      gks_run_until='';
    } else {
      if (from_php_id==-1 || gks_eftpos_payment_ok==false) {
        gks_run_until='070ypoekdosi';
      } else {
        $('#gks_pos_panel_pay').hide();
        fnc_gks_erp_app_mobile_pos_dialog_pay_open(false);
      }
      if ($('#pos_multi_copies').length>=1) {
        $('#pos_multi_copies').val('1');  
      }
    }
    
    
    gks_pos_run_submit(false);
  }
  $('.gks_pos_panel_pay_btn').click(gks_pos_panel_pay_btn_click);
  
  

  

  
  
  
  
  
  
  
  
  
  
  
  
  
  
  $('#dr_user_ma_country_id').each(function() {
    dbval=parseInt($(this).attr('data-dbval'));  
    if (isNaN(dbval)) dbval=0;
    $(this).append('<option value="0">'+gks_lang('Χώρα')+'...</option>');
    for(i=0;i<gks_country.length;i++) {
      $(this).append('<option value="' + gks_country[i].id_country + '" data-ci="' + gks_country[i].country_initials +'" data-ee="'+gks_country[i].country_ee+'">' + gks_country[i].country_name + '</option>');
    }   
    $(this).val(dbval); $(this).removeAttr('data-dbval');
  });   
  
  $('#dr_user_ma_country_id').change(function() {
    v=parseInt($('#dr_user_ma_country_id').val());
    if (isNaN(v)) v=0;
    //console.log(v);
    nomos_fill('dr_user_ma_nomos_id',v,0);
  });  


  gks_address_autocomplete('dr_user_ma_odos','dr_user_ma_arithmos','dr_user_ma_orofos','dr_user_ma_perioxi','dr_user_ma_poli','dr_user_ma_tk','dr_user_ma_nomos_id','dr_user_ma_country_id','','',true);


  $('#user').autocomplete({
    source: function(request, response) {
      last_action_time = new Date();
      mydata={
        term: request.term,
      };
      $.ajax({
        timeout: 3000, // sets timeout to 3 seconds
        url: 'admin-autocomplete-user.php',
        dataType: "json",
        cache: false,
        data: mydata,
        error : function(jqXHR ,textStatus,  errorThrown) {
  			  if (typeof jqXHR.responseText !== 'undefined' && jqXHR.responseText.trim()!=='') {
  			    myerror_show=jqXHR.responseText
  			  } else {
  			    myerror_show=gks_lang('Υπάρχει σύνδεση με το internet ;');
  			  }
  				myalert('error:' + myerror_show);
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
      $('#user_save').hide();
      
      
      
      gks_admin_get_user_data(ui.item.id);
    
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        gks_admin_get_user_null();
             
                     
      }
    }
  });

  function gks_admin_get_user_null() {
    $("#user").val('');
    $("#user_id").val('');

    $('#autocomplete_user_id').hide(); 
    $('#user_save').show();
    $('#div_pelati_sxolio').hide('fade', 'slow');
    $('#text_pelati_sxolio').html('');
                    
    $('#div_order_sxolio').hide('fade', 'slow');
    $('#text_order_sxolio').html('');   
       
    $('#dr_user_first_name').val('');
    $('#dr_user_last_name').val('');
    $('#dr_user_email').val('');
    $('#dr_user_mobile').val('');
    $('#dr_user_lang').val('el-GR');
    $('#dr_user_ma_odos').val('');
    $('#dr_user_ma_arithmos').val('');
    $('#dr_user_ma_orofos').val('');
    $('#dr_user_ma_perioxi').val('');
    $('#dr_user_ma_poli').val('');
    $('#dr_user_ma_tk').val('');
        
    $('#dr_user_ma_country_id').val('91');
    nomos_fill('dr_user_ma_nomos_id','91',0);
            
  }

  function gks_admin_get_user_data(user_id) {
    last_action_time = new Date();
      
    datasend='cmd=get&id=' + user_id + '&acc_inv_id=-1';
    $.ajax({
      timeout: 30000, // sets timeout to 3 seconds
			url: 'admin-get-user-data.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  if (typeof jqXHR.responseText !== 'undefined' && jqXHR.responseText.trim()!=='') {
			    myerror_show=jqXHR.responseText
			  } else {
			    myerror_show=gks_lang('Υπάρχει σύνδεση με το internet ;');
			  }
				myalert('error:' + myerror_show);
			},				
			success: function(data) {
			  need_save=true;
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {

					  $("#user").val(data.gks_nickname);
					  $('#user_id').val(data.id);
            $('#autocomplete_user_id').attr('href', 'admin-users-item.php?id=' + data.id);
            $('#autocomplete_user_id').show();
            $('#user_save').hide();
      					  
					  
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
            }
            
            $('#dr_user_first_name').val(data.first_name);
            $('#dr_user_last_name').val(data.last_name);
            $('#dr_user_email').val(data.email);
            $('#dr_user_mobile').val((data.def_phone!='' ? data.def_phone : (data.phone_home!='' ? data.phone_home : data.mobile)));
            $('#dr_user_lang').val(data.lang);
            

            //console.log('gks_dialog_gsis_result false');
            $('#dr_user_ma_odos').val(data.ma_odos);
            $('#dr_user_ma_arithmos').val(data.ma_arithmos);
            $('#dr_user_ma_orofos').val(data.ma_orofos);
            $('#dr_user_ma_perioxi').val(data.ma_perioxi);
            $('#dr_user_ma_poli').val(data.ma_poli);
            $('#dr_user_ma_tk').val(data.ma_tk);
            $('#dr_user_ma_country_id').val(data.ma_country_id);
            nomos_fill('dr_user_ma_nomos_id',data.ma_country_id,data.ma_nomos_id);
              
           
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		});     
    
  }

  var customer_has_open=false;
  $('#gks_pos_panel_bill_customer_icon').click(function() {
    last_action_time = new Date();
    $('#gks_pos_panel_customer').show(); 
    if (customer_has_open==false) {
      user_id=$(this).attr('data-def_user_id');
      if (isNaN(user_id)) user_id=0;
      if (user_id>=1) {
        gks_admin_get_user_data(user_id);
      } else {
        gks_admin_get_user_null();
      }
    }
    
  });
  
  $('#gks_pos_panel_customer_close').click(function() {
    last_action_time = new Date();
    $('#gks_pos_panel_customer').hide();
  });
  $('#gks_pos_panel_customer_cancel').click(function() {
    last_action_time = new Date();
    $('#gks_pos_panel_customer').hide();
  });
  $('#gks_pos_panel_customer_ok').click(function() {
    last_action_time = new Date();
    //console.log('gks_pos_panel_customer_ok');
    user_id=parseInt($('#user_id').val()); if (isNaN(user_id)) user_id=-1;
    user_nickname=$('#user').val().trim();
    $('#gks_pos_panel_bill_customer_icon').attr('data-sel_user_id',user_id).attr('data-sel_user_nickname',$.base64.encode(user_nickname));
    $('#gks_pos_panel_bill_customer_inner').html(user_nickname);
    $('#gks_pos_panel_customer').hide();
    customer_has_open=true;
      
  });

  var dialog_user_save;
  dialog_user_save = $( "#dialog_user_save" ).dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_user_save_ok",
        html: '<i class="fa fa-pen-square"></i> '+gks_lang('Προσθήκη ή επιλογή χρήστη'),
        //icon: "ui-icon-circle-plus",
        click: function() {
          last_action_time = new Date();
          sel_elem=$('input[name=dialog_user_save_radio]:checked');
          if (sel_elem.length==0) {
            myalert('error:'+gks_lang('Επιλέξτε ή την προσθήκη νέου χρήστη ή έναν υπάρχον χρήστη'));
            return;  
          }
          select_user_id=sel_elem.val();
          //console.log(select_user_id);
          datasend=dialog_user_save.datasend;
          datasend+='&force=1&select_user_id=' + select_user_id;
          //console.log(datasend);
          
          $('body').addClass("myloading");
          $.ajax({
            timeout: 30000, // sets timeout to 3 seconds
      			url: '/my/admin-users-add-exec.php',
      			type: 'POST',
      			cache: false,
      			dataType: 'json',
      			data: datasend,
      			error : function(jqXHR ,textStatus,  errorThrown) {
      			  $("body").removeClass("myloading");
      			  if (typeof jqXHR.responseText !== 'undefined' && jqXHR.responseText.trim()!=='') {
      			    myerror_show=jqXHR.responseText
      			  } else {
      			    myerror_show=gks_lang('Υπάρχει σύνδεση με το internet ;');
      			  }
      				myalert('error:' + myerror_show);
      			},
      			success: function(data) {
      				$("body").removeClass("myloading");
      				if (!data) {
      					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
      				} else {
      					if (data.success == true) {
                  $('#user_id').val(data.user_id);
                  $('#user').val(data.gks_nickname);
                  $('#autocomplete_user_id').show().attr('href', 'admin-users-item.php?id=' + data.user_id);

                  $('#user_save').hide();
                  dialog_user_save.dialog( "close" );     					  
      					} else {
      					  myalert('error:' + $.base64.decode(data.message));
      					}
      				}
      			}
      		});
      					            
          
          //$(this).dialog( "close" );
        }
        //showText: false
      },
      {
        id: "dialog_user_save_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Άκυρο'),
        //icon: "ui-icon-cancel",
        click: function() {
          $( this ).dialog( "close" );
        }
        //showText: false
      },      
    ]
        

  });
  
  
  $('#user_save').click(function() {
    last_action_time = new Date();
    datasend='';    
    datasend+='&dr_user_first_name='  + encodeURIComponent($.base64.encode($("#dr_user_first_name").val().trim()));
    datasend+='&dr_user_last_name='  + encodeURIComponent($.base64.encode($("#dr_user_last_name").val().trim()));
    datasend+='&dr_user_email='  + encodeURIComponent($.base64.encode($("#dr_user_email").val().trim()));
    datasend+='&dr_user_mobile='  + encodeURIComponent($.base64.encode($("#dr_user_mobile").val().trim()));
    datasend+='&dr_user_lang='  + encodeURIComponent($.base64.encode($("#dr_user_lang").val().trim()));
    datasend+='&dr_user_ma_odos='  + encodeURIComponent($.base64.encode($("#dr_user_ma_odos").val().trim()));
    datasend+='&dr_user_ma_arithmos='  + encodeURIComponent($.base64.encode($("#dr_user_ma_arithmos").val().trim()));
    datasend+='&dr_user_ma_orofos='  + encodeURIComponent($.base64.encode($("#dr_user_ma_orofos").val().trim()));
    datasend+='&dr_user_ma_perioxi='  + encodeURIComponent($.base64.encode($("#dr_user_ma_perioxi").val().trim()));
    datasend+='&dr_user_ma_poli='  + encodeURIComponent($.base64.encode($("#dr_user_ma_poli").val().trim()));
    datasend+='&dr_user_ma_tk='  + encodeURIComponent($.base64.encode($("#dr_user_ma_tk").val().trim()));
    datasend+='&dr_user_ma_country_id='  + encodeURIComponent($("#dr_user_ma_country_id").val().trim());
    datasend+='&dr_user_ma_nomos_id='  + encodeURIComponent($("#dr_user_ma_nomos_id").val().trim());
    datasend+='&dr_user_eponimia=';
    datasend+='&dr_user_title=';
    datasend+='&dr_user_afm=';
    datasend+='&dr_user_doy=';
    datasend+='&dr_user_epaggelma=';

    datasend+='&fiscal_position_id=1' ;
    datasend+='&pricelist_id=1' ;
    datasend+='&def_ekptosi=0';
    
    datasend+='&acc_inv_id=-1';
    datasend+='&journal_id=' + $('#inv_acc_journal_id').val();
    datasend+='&seira_id=' + $('#inv_acc_seira_id').val();
        
    //console.log('user_save');
    //console.log(datasend);
    
    dialog_user_save.datasend=datasend;
    
    $('body').addClass("myloading");
    $.ajax({
      timeout: 30000, // sets timeout to 3 seconds
			url: '/my/admin-users-add-exec.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $("body").removeClass("myloading");
			  if (typeof jqXHR.responseText !== 'undefined' && jqXHR.responseText.trim()!=='') {
			    myerror_show=jqXHR.responseText
			  } else {
			    myerror_show=gks_lang('Υπάρχει σύνδεση με το internet ;');
			  }
				myalert('error:' + myerror_show);
			},
			success: function(data) {
				$("body").removeClass("myloading");
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
				  
				    if (data.ask_user) {
				      //console.log(data.exist_rows);
    				      
    				  outhtml='';
    					outhtml+='<table class="table table-sm table-responsive1 table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">';
    					outhtml+='<thead><tr>';
    					outhtml+='<th nowrap class="table-dark" scope="col" style="text-align: center !important;width:0%">'+gks_lang('Α/Α')+'</th>';
    					outhtml+='<th nowrap class="table-dark" scope="col" style="text-align: center !important;width:5%">'+gks_lang('Επιλογή')+'</th>';
    					outhtml+='<th nowrap class="table-dark" scope="col" style="text-align: left !important;width:30%">'+gks_lang('Υποκοριστικό')+'</th>';
    					outhtml+='<th nowrap class="table-dark" scope="col" style="text-align: left !important;width:60%">'+gks_lang('Αναζήτηση')+'</th>';
    					outhtml+='<th nowrap class="table-dark" scope="col" style="text-align: center !important;width:5%">'+gks_lang('Προβολή')+'</th>';
    					outhtml+='</tr></thead><tbody>';
  
    					for (i=0;i < data.exist_rows.length; i++) {
    					  outhtml+='<tr>' + 
    					  '<td scope="row" style="text-align: center !important;" nowrap>' + (i + 1) + '</td>' +
    					  '<td style="text-align: center !important;">' + '<input type="radio" name="dialog_user_save_radio" id="dialog_user_save_radio_' + data.exist_rows[i].ID + '" value="' + data.exist_rows[i].ID + '"  title="'+gks_lang('Επιλογή χρήστη')+'"></td>' +
    					  '<td><label class="gks_label" for="dialog_user_save_radio_' + data.exist_rows[i].ID + '" style="vertical-align: middle;">' + data.exist_rows[i].gks_nickname + '</label></td>' + 
    					  '<td>' + data.exist_rows[i].descrs.join('<br>') + '</td>' +
    					  '<td style="text-align: center !important;"><a href="admin-users-item.php?id=' + data.exist_rows[i].ID + '" target="_blank"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="'+gks_lang('Προβολή χρήστη')+'"></i></a></td>' + 
    					  '</tr>';
    					}
    					outhtml+='</tbody></table>';
  					  
  					  $('#dialog_user_save_html').html(outhtml);
          	  dwidth=$(window).width() * 0.96;
              dheight=$(window).height() * 0.96;
          	  if (dwidth> 850) dwidth=850;
          	  //if (dheight> 600) dheight=600;
          	  if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
          	    dwidth=$(window).width();dheight=$(window).height();
          	  } else if ($('body').hasClass('gks_erp_app_mobile')) {
          	    dwidth=$(window).width();dheight=$(window).height();
          	  }         	  
          	  $('#dialog_user_save_radio_new').prop('disabled', false);
          	  dialog_user_save.dialog('option', 'width', dwidth);
          	  dialog_user_save.dialog('option', 'height', dheight);
          	  $('#dialog_user_save').parent().css({position:'fixed'});      
              dialog_user_save.dialog('open');    
  					  			      
				    } else {
              $('#user_id').val(data.user_id);
              $('#user').val(data.gks_nickname);
              $('#autocomplete_user_id').show().attr('href', 'admin-users-item.php?id=' + data.user_id);
              $('#user_save').hide();				      
				    }
				    
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}				
	  });    
    
  });
  
  
  $('#gks_pos_menu_title').click(function() {
    last_action_time = new Date();
    phtml='';
    for(i=0; i < gks_erp_app_mobile_local_printers.length;i++) {
      phtml+=gks_erp_app_mobile_local_printers[i].Name + '<br>' + 
      gks_erp_app_mobile_local_printers[i].Status + '<br>' +
      gks_erp_app_mobile_local_printers[i].Init_id + ', ' + gks_erp_app_mobile_local_printers[i].Status_id + '<br>';
    }
    if (gks_erp_app_mobile_local_printers.length==0) phtml=gks_lang('Δεν βρέθηκε εκτυπωτής');
    $('#gks_erp_app_mobile_local_printers_div').html(phtml);
    
    shtml=$(window).width() + 'x' + $(window).height();
    $('#gks_erp_app_mobile_screen_span').html(shtml);


    
    $('#gks_pos_panel_info').show() ;//.show('slide',{},500); 
  });
  $('#gks_pos_panel_info_close').click(function() {$('#gks_pos_panel_info').hide();});
  $('#gks_pos_panel_info_ok').click(function() {$('#gks_pos_panel_info').hide();});
  
  
  
  
  $('#gks_pos_menu_exit').click(function() {
    
    
    if (gks_pos_settings.audio) new Audio(audio_exit).play();
    if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
      gks_pos_menu_reset_click();
      fnc_gks_erp_app_mobile_exit_pos();
    } else {
      window.location.href='/my/admin-pos-run-select.php';
    }
    
  });
  
  function acc_inv_pos_run8_sms_run_click() {
    last_action_time = new Date();
    //console.log('acc_inv_pos_run8_sms_run_click');  
    $('#acc_inv_pos_run9_result').hide();
    pos_sms_erp_app_mobile_id_code=($(this).attr('data-pos_sms_erp_app_mobile_id_code')).trim();
    if (pos_sms_erp_app_mobile_id_code=='') {
      $('#acc_inv_pos_run9_result').html('<div class="alert alert-danger" role="alert">'+gks_lang('Δεν έχει ορισθεί το κινητό αποστολής')+' (internel error 784510740)</div>').slideDown();
      return;
    }
    data_url=$(this).attr('data-url');
    if (data_url=='') {
      $('#acc_inv_pos_run9_result').html('<div class="alert alert-danger" role="alert">'+gks_lang('Δεν έχει ορισθεί o σύνδεσμος αποστολής')+' (internel error 784510741)</div>').slideDown();
      return;
    }
    
    data_objid=$(this).attr('data-objid');
    if (isNaN(data_objid)) data_objid=0;
    if (data_objid<=0) {
      $('#acc_inv_pos_run9_result').html('<div class="alert alert-danger" role="alert">'+gks_lang('Δεν έχει ορισθεί το ID εγγραφής')+' (internel error 784510743)</div>').slideDown();
      return;
    }
    
    
    acc_inv_pos_run8_sms=$('#acc_inv_pos_run8_sms').val().trim();
    if (acc_inv_pos_run8_sms=='') {
      $('#acc_inv_pos_run9_result').html('<div class="alert alert-danger" role="alert">'+gks_lang('Πληκτρολογήστε το κινητό του πελάτη για να του αποσταλεί ο σύνδεσμος')+'</div>').slideDown();
      return;
    }
    
    datasend='';
    datasend+='&gks_session_pos=' + encodeURIComponent($.base64.encode(gks_session_pos));
    datasend+='&gks_erp_cookie_id=' + encodeURIComponent($.base64.encode(gks_getCookie('gks_erp_cookie_id')));
    datasend+='&gks_run_until=' + encodeURIComponent(gks_run_until);
    
    datasend+='&id_pos=' + from_php_id_pos;
    datasend+='&id=' + from_php_id;
    
    datasend+='&acc_inv_pos_run8_sms_run=1';
    datasend+='&pos_sms_erp_app_mobile_id_code=' + pos_sms_erp_app_mobile_id_code;
    datasend+='&acc_inv_pos_run8_sms='  + encodeURIComponent($.base64.encode(acc_inv_pos_run8_sms));
    datasend+='&data_url='  + encodeURIComponent(data_url);
    datasend+='&data_objid='  + encodeURIComponent(data_objid);
    
    
    $('#acc_inv_pos_run9_result').html('<img src="img/wait.gif"/>').slideDown();
    $('#acc_inv_pos_run8_sms').prop('disabled',true);
    $('#acc_inv_pos_run8_sms_run').prop('disabled',true);
    
    $.ajax({
			url: '/my/admin-pos-run-exec.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  if (typeof jqXHR.responseText !== 'undefined' && jqXHR.responseText.trim()!=='') {
			    myerror_show=jqXHR.responseText
			  } else {
			    myerror_show=gks_lang('Υπάρχει σύνδεση με το internet ;');
			  }
							  
        $('#acc_inv_pos_run9_result').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': ' + myerror_show + '</div>').slideDown();
        $('#acc_inv_pos_run8_sms').prop('disabled',false);
        $('#acc_inv_pos_run8_sms_run').prop('disabled',false);
			},				
			success: function(data) {
        //console.log(data);
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          $('#acc_inv_pos_run9_result').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': '+gks_lang('Παρακαλώ δοκιμάστε αργότερα')+'</div>').slideDown();
          $('#acc_inv_pos_run8_sms').prop('disabled',false);
          $('#acc_inv_pos_run8_sms_run').prop('disabled',false);
				} else {
					if (data.success == true) {
            $('#acc_inv_pos_run9_result').html('<div class="alert alert-success" role="alert">' + $.base64.decode(data.message) + '</div>').slideDown();
            $('#acc_inv_pos_run8_sms').prop('disabled',false);
            $('#acc_inv_pos_run8_sms_run').prop('disabled',false);
					} else {
            $('#acc_inv_pos_run9_result').html('<div class="alert alert-danger" role="alert">'+gks_lang('Σφάλμα')+': ' + $.base64.decode(data.message) + '</div>').slideDown();
            $('#acc_inv_pos_run8_sms').prop('disabled',false);
            $('#acc_inv_pos_run8_sms_run').prop('disabled',false);
					}
				}
			}
			
		});					        
    
  }
  
  
  // pre end
   
  
  if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
    temp=fnc_gks_erp_app_mobile_get_local_printers().trim();
    if (temp=='') return;
    //myalert('error:' + temp);
    temp=JSON.parse(temp);
    if (Array.isArray(temp)) {
      gks_erp_app_mobile_local_printers=temp;
      //myalert('error:' + gks_erp_app_mobile_local_printers.length + ' ' + gks_erp_app_mobile_local_printers[0].Name);
    }
  }
  
  gks_pos_menu_reset_click();
  
  
  //database 
  if (from_php_pos_indexeddb) {
    window.indexedDB = window.indexedDB || window.webkitIndexedDB || window.mozIndexedDB;
    var IDBTransaction = window.IDBTransaction || window.webkitIDBTransaction;
    var IDBKeyRange = window.IDBKeyRange || window.webkitIDBKeyRange;
    var gks_db;
  }
  

  
  function openDb() {
    //console.log("openDb ...");
    if (from_php_pos_indexeddb==false) return;
    var req = indexedDB.open('gks_erp_pos', 3);
    req.onsuccess = function (evt) {
      gks_db = this.result;
      //console.log("openDb DONE");
      get_products_for_Db();
    };
    req.onerror = function (evt) {
      console.error("openDb:", evt.target.errorCode);
    };

    req.onupgradeneeded = function (evt) {
      //console.log("openDb.onupgradeneeded");
      var store = evt.currentTarget.result.createObjectStore('products', { keyPath: 'id', autoIncrement: true });

      store.createIndex('search_code',  'search_code', { unique: false });
      store.createIndex('search_descr', 'search_descr', { unique: false });
      store.createIndex('search_sku',   'search_sku', { unique: false });
      store.createIndex('search_gtin',  'search_gtin', { unique: false });
      store.createIndex('search_upc',   'search_upc', { unique: false });
      store.createIndex('search_ean',   'search_ean', { unique: false });
      store.createIndex('search_isbn',  'search_isbn', { unique: false });
    };
  }
  //openDb();
  
  function deleteDb() {
    //console.log("deleteDb ...");
    var req = indexedDB.deleteDatabase('gks_erp_pos');
    req.onsuccess = function (evt) {
      openDb();
    };
    req.onerror = function (evt) {
      openDb();
    };    
    
  }
  
  
  deleteDb();
  
  
  
  var products_page_for_Db=0;
  var products_db_curr=0;
  var products_db_total=0;
  
  function get_products_for_Db() {
    last_action_time = new Date();
    datasend='';
    datasend+='&id_pos=' + from_php_id_pos;
    datasend+='&page=' + products_page_for_Db;
    datasend+='&term=';  
    datasend+='&fordb=1';
    //console.log(datasend);
    
    $.ajax({
			url: '/my/admin-pos-run-get-products.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_page: products_page_for_Db,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  if (textStatus != 'abort') {
				  if (typeof jqXHR.responseText !== 'undefined' && jqXHR.responseText.trim()!=='') {
  			    myerror_show=jqXHR.responseText
  			  } else {
  			    myerror_show=gks_lang('Υπάρχει σύνδεση με το internet ;');
  			  }
  				myalert('error:' + myerror_show);
			  }
			},				
			success: function(data) {
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
            //console.log(data);
            
            products_db_curr+=data.list.length;
            products_db_total=data.total_records;
            products_db_per100=products_db_curr*100/products_db_total;
            $('#gks_pos_products_loading .progress-bar').html(products_db_curr+'/'+products_db_total).css({width:products_db_per100 + '%'});


            gks_db.transaction(["products"], "readonly").objectStore("products").count().onsuccess = function(e) {
              //var count = e.target.result;
              //if (count == 0) {
                var done = 0;
                var product_rec = gks_db.transaction(["products"], "readwrite").objectStore("products");
                
                for (i = 0; i < data.list.length; i++) {
                  var p_rec = data.list[i];
                  if (p_rec.descr!='') p_rec.search_descr = p_rec.descr_conv;
                  if (p_rec.code!='') p_rec.search_code = p_rec.code_conv;
                  if (p_rec.sku!='') p_rec.search_sku = p_rec.sku;
                  if (p_rec.gtin!='') p_rec.search_gtin = p_rec.gtin;
                  if (p_rec.upc!='') p_rec.search_upc = p_rec.upc;
                  if (p_rec.ean!='') p_rec.search_ean = p_rec.ean;
                  if (p_rec.isbn!='') p_rec.search_isbn = p_rec.isbn;
                  
                  
                  resp = product_rec.add(p_rec);
                  resp.onsuccess = function(e) {
                    done++;
                    if (done == data.list.length) {

                      if (data.list.length==1000) {
                        $('#gks_pos_products_loading').slideDown();
                        setTimeout(function() {
                          //console.log('next page');
                          products_page_for_Db++;
                          get_products_for_Db()
                        }, 300);
                      } else {
                        setTimeout(function() {
                          $('#gks_pos_products_loading').slideUp();  
                        },3000);
                      }
                                            
                    }
                  }
                }
              //}
            };
            
            
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});
  }


  function set_def_xxx_terminal() {
    last_action_time = new Date();
    if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
      if ($('.div_payment_one_terminal_terminal[data-pawid="1"]').length>0) {
        temp=fnc_gks_erp_app_mobile_get_viva_terminal_data();
        if (temp!=''){
          //myalert('error:' + temp);
          tempo=JSON.parse(temp);
          if (typeof tempo =='object' && typeof tempo.viva_pos_terminal_id !== 'undefined') {
            $('.div_payment_one_terminal_terminal[data-pawid="1"]').attr('data-asset_id',tempo.id_asset).val(tempo.asset_title);
            $('.payxxx_company_eponimia[data-cpawid="1"]').html(gks_lang('Εταιρεία')+': ' + tempo.company_eponimia);
          } else {
            myalert('error:'+gks_lang('Σφάλμα λήψης Viva Terminal ID')+':<br>' + temp);
          }
        }
      }
      if ($('.div_payment_one_terminal_terminal[data-pawid="6"]').length>0) {
        temp=fnc_gks_erp_app_mobile_get_worldline_terminal_data();
        if (temp!='') {
          //myalert('error:' + temp);
          tempo=JSON.parse(temp);
          if (typeof tempo =='object' && typeof tempo.worldline_pos_terminal_id !== 'undefined') {
            $('.div_payment_one_terminal_terminal[data-pawid="6"]').attr('data-asset_id',tempo.id_asset).val(tempo.asset_title);
            $('.payxxx_company_eponimia[data-cpawid="6"]').html(gks_lang('Εταιρεία')+': ' + tempo.company_eponimia);
          } else {
            myalert('error:'+gks_lang('Σφάλμα λήψης Worldline Terminal ID')+':<br>' + temp);
          }
        }
      }      
    } else {
      if (gks_pos_settings.viva_id_asset>0 && gks_pos_settings.viva_asset_title!='') {
        $('.div_payment_one_terminal_terminal[data-pawid="1"]').attr('data-asset_id',gks_pos_settings.viva_id_asset).val(gks_pos_settings.viva_asset_title);
        $('.payxxx_company_eponimia[data-cpawid="1"]').html('');
      }
      if (gks_pos_settings.worldline_id_asset>0 && gks_pos_settings.worldline_asset_title!='') {
        $('.div_payment_one_terminal_terminal[data-pawid="6"]').attr('data-asset_id',gks_pos_settings.worldline_id_asset).val(gks_pos_settings.worldline_asset_title);
        $('.payxxx_company_eponimia[data-cpawid="6"]').html('');
      }      
      
    }
  }
  set_def_xxx_terminal();



  function gks_app_mobile_is_mobile() {
    let check = false;
    (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
    return check;
  };
  function gks_app_mobile_is_mobile_and_tablet() {
    let check = false;
    (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
    return check;
  };  
    
    
  window.from_main_back_button_pressed_inside = function() {
    if (gks_and_app_pos_dialog_pay_open==true && gks_and_app_pos_dialog_card_open==false) {
      if ($('#gks_pos_panel_pay_cancel').length==0) {
        fnc_gks_erp_app_mobile_toast(gks_lang('Στείλτε την απόδειξη με μετρητά ή κάρτα'));
      } else {
        $('#gks_pos_panel_pay_cancel').click();
      }
      return;  
    }
    if (gks_and_app_pos_dialog_card_open==true) {
      if ($('#dialog_payment_with_cancel').css('display')=='none') {
        fnc_gks_erp_app_mobile_toast(gks_lang('Μετρητά ή άλλη κάρτα'));
      } else { 
        $('#dialog_payment_with_cancel').click();
      }
      return;
    }
    
    //document.getElementById("gks_pos_panel_products").style.backgroundColor='red';
    
  }
  
  function gks_pos_panel_reprint_header_reload_click() {
    last_action_time = new Date();
    $('#gks_pos_panel_reprint_header_reload').prop('disabled',true);
    $('.gks_pos_panel_reprint_data2').html('<div class="gks_pos_panel_reprint_data2_wait"><img src="img/wait.gif"/></div>').slideDown();
    $('#gks_pos_panel_reprint').show() ;
    datasend='';
    datasend+='&id_pos=' + from_php_id_pos;

    datasend+='&device_type=' + encodeURIComponent($.base64.encode(gks_device_type));

    
    $.ajax({
			url: '/my/admin-pos-run-reprint.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  if (typeof jqXHR.responseText !== 'undefined' && jqXHR.responseText.trim()!=='') {
			    myerror_show=jqXHR.responseText
			  } else {
			    myerror_show=gks_lang('Υπάρχει σύνδεση με το internet ;');
			  }
				$('.gks_pos_panel_reprint_data2').html('<div class="alert alert-danger">error:' + myerror_show + '</div>');
				$('#gks_pos_panel_reprint_header_reload').prop('disabled',false);
			},				
			success: function(data) {
				$('#gks_pos_panel_reprint_header_reload').prop('disabled',false);
				if (!data) {
					$('.gks_pos_panel_reprint_data2').html('<div class="alert alert-danger">'+gks_lang('Σφάλμα')+': '+gks_lang('Παρακαλώ δοκιμάστε αργότερα')+'</div>');
				} else {
					if (data.success == true) {
            $('.gks_pos_panel_reprint_data2').html($.base64.decode(data.html));
            $('.mytooltipsterdate').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
            $('.mytooltipsterfixcard').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
					} else {
            $('.gks_pos_panel_reprint_data2').html('<div class="alert alert-danger">' + $.base64.decode(data.message) + '</div>');
					}
        }
      }
    });    
    
  }
  
  $('#gks_pos_menu_reprint, #gks_pos_panel_reprint_header_reload').click(function() {
    last_action_time = new Date();
    gks_pos_panel_reprint_header_reload_click();
  });
  $('#gks_pos_panel_reprint_close, #gks_pos_panel_reprint_ok').click(function() {$('#gks_pos_panel_reprint').hide();});
  

  function gks_pos_panel_print_x_header_reload_click() {
    last_action_time = new Date();
    $('#gks_pos_panel_print_x_header_reload').prop('disabled',true);
    $('.gks_pos_panel_print_x_data2').html('<div class="gks_pos_panel_print_x_data2_wait"><img src="img/wait.gif"/></div>').slideDown();
    $('#gks_pos_panel_print_x_data2_printbtn').hide();
    $('#gks_pos_panel_print_x_to_local').attr('data-printfile','');
    $('#gks_pos_panel_print_x_data2_qrcode').html('');
    $('#gks_pos_panel_print_x').show() ;
    datasend='';
    datasend+='&id_pos=' + from_php_id_pos;
    datasend+='&mydaydif=' + filter_days_back;
    
    datasend+='&device_type=' + encodeURIComponent($.base64.encode(gks_device_type));

    
    $.ajax({
			url: '/my/admin-pos-run-print-x.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  if (typeof jqXHR.responseText !== 'undefined' && jqXHR.responseText.trim()!=='') {
			    myerror_show=jqXHR.responseText
			  } else {
			    myerror_show=gks_lang('Υπάρχει σύνδεση με το internet ;');
			  }
				$('.gks_pos_panel_print_x_data2').html('<div class="alert alert-danger">error:' + myerror_show + '</div>');
				$('#gks_pos_panel_print_x_header_reload').prop('disabled',false);
			},				
			success: function(data) {
				$('#gks_pos_panel_print_x_header_reload').prop('disabled',false);
				if (!data) {
					$('.gks_pos_panel_print_x_data2').html('<div class="alert alert-danger">'+gks_lang('Σφάλμα')+': '+gks_lang('Παρακαλώ δοκιμάστε αργότερα')+'</div>');
				} else {
					if (data.success == true) {
            $('.gks_pos_panel_print_x_data2').html($.base64.decode(data.html));
            
            $('#gks_pos_panel_print_x_data2_printbtn').show();
            $('#gks_pos_panel_print_x_to_local').attr('data-printfile',data.url_txt);
            
            if (data.url_qrcode!='') {
              $('#gks_pos_panel_print_x_data2_qrcode').html('<a href="' + $.base64.decode(data.url_txt) + '" target="_blank"><img src="' + $.base64.decode(data.url_qrcode) + '"/></a>');
            }
            
					} else {
            $('.gks_pos_panel_print_x_data2').html('<div class="alert alert-danger">' + $.base64.decode(data.message) + '</div>');
					}
        }
      }
    });    
    
  }
  
  $('#gks_pos_panel_print_x_to_local').click(function() {
    url_txt=$('#gks_pos_panel_print_x_to_local').attr('data-printfile');
    if (url_txt=='') return ;
    url_txt=$.base64.decode(url_txt);
    
    if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
      temp=fnc_gks_erp_app_mobile_get_local_printers().trim();
      if (temp!='') {
        fnc_gks_erp_app_mobile_thermal_url_file_run(url_txt);
        return;
      }
    }

    var newWindow=window.open(url_txt);
    newWindow.focus();
    newWindow.print();    
    //newWindow.close();    
    
  });
  
  
  $('#gks_pos_menu_print_x, #gks_pos_panel_print_x_header_reload').click(function() {
    last_action_time = new Date();
    gks_pos_panel_print_x_header_reload_click();
  });
  $('#gks_pos_panel_print_x_close, #gks_pos_panel_print_x_ok').click(function() {$('#gks_pos_panel_print_x').hide();});
  


  function gks_get_gps_location() {
    if (typeof gks_erp_app_mobile !== 'undefined') {
      fnc_gks_erp_app_mobile_get_location();
    } else {
      //if (gks_device_type=='desktop' && my_gps_location_lat!=0 && my_gps_location_lng!=0) return;
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            my_gps_location_lat = position.coords.latitude;
            my_gps_location_lng = position.coords.longitude;
            my_gps_location_res='ok';
            geoUri = 'http://maps.google.com/maps?q=loc:' + my_gps_location_lat + ',' + my_gps_location_lng + ' (point)';
            $('#gks_get_gps_location_report').html('<a href="' + geoUri +'" target="_blank">' + my_gps_location_lat + ' , ' +my_gps_location_lng + '</a>');
            
        }, function() {
          my_gps_location_res='permission error';
          $('#gks_get_gps_location_report').html(my_gps_location_res);
        });
      } else {
        my_gps_location_res='browser error';
      }
    }
  }
  
  setInterval(function () {
    mynow = new Date();
    secondsdiff = (mynow.getTime() - last_action_time.getTime()) / 1000;
    //console.log(secondsdiff);
    if (secondsdiff>3600) { // 1 hour
      if (gks_device_type=='desktop') {
        window.location.reload();
      } else {
        $('#gks_pos_menu_reset').click();
        $('#gks_pos_menu_exit').click();
      }
    }
  }, 5000);  
    
  
  gks_device_type='desktop';
  if (gks_app_mobile_is_mobile()) gks_device_type='mobile';
  else if (gks_app_mobile_is_mobile_and_tablet()) gks_device_type='tablet';
  $('#gks_erp_app_mobile_device_type_span').html(gks_device_type);
  gks_get_gps_location();
  setInterval(gks_get_gps_location,60000);
    
  function accinvposimgqrcode_click() {
    //console.log('accinvposimgqrcode_click');
    myqrimg=$(this).attr('src');
    myclass='';
    if ($(window).width() > $(window).height()) {
      myclass='gks_qrcode_fullscreen_landspace';
    } else {
      myclass='gks_qrcode_fullscreen_portrait'; 
    }
    html_qrcode=
    '<div id="gks_qrcode_fullscreen" class="' + myclass + '">' + 
     '<img src="' + myqrimg + '">' +
    '</div>';
    $(html_qrcode).appendTo('body');
    
    $('#gks_qrcode_fullscreen').click(function() {
      $(this).remove();
    });
  }  
  
  var filter_days_back=0;
  $('#gks_pos_panel_print_x_data_filter_date').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,
    {mask:'39/19/9999',
    format:'d/m/Y',
    timepicker:false,
    dayOfWeekStart:1,
    onChangeDateTime:function(ct,$i){
      var mynow = new Date(from_php_mynow_Y,from_php_mynow_m,from_php_mynow_d,0,0,0,0 );
      var mydiff = ct - mynow; //in milliseconds
      mydiff = mydiff/1000/86400;
      mydiff=Math.round(mydiff);
      filter_days_back=mydiff;
      //console.log(filter_days_back);
      gks_pos_panel_print_x_header_reload_click();
    },
    minDate:from_php_print_x_days_min,
    maxDate:from_php_print_x_days_max,
  }));
    
      
  //generic
  gks_page_loading=false;
  $('.myneedsave').on('input change keyup paste', function() {
    need_save=true; 
  });

  window.onbeforeunload = function() {
    //if (from_php_perm_ret_edit==false) return;
    
    if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
    
    } else {
      if (need_save==false) return;
      return gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');
    }
  };

  need_save=false;
  
  //console.log('load end');

	  
});



function fnc_gks_erp_app_mobile_toast(toast) {
  gks_erp_app_mobile.showToast(toast);
}
function fnc_gks_erp_app_mobile_exit_pos() {
  gks_erp_app_mobile.exit_pos();
}
function fnc_gks_erp_app_mobile_viva_foreground() {
  gks_erp_app_mobile.viva_foreground();
}
function fnc_gks_erp_app_mobile_worldline_foreground() {
  gks_erp_app_mobile.worldline_foreground();
}

function fnc_gks_erp_app_mobile_thermal_url_file_run(urlfile) {
  gks_erp_app_mobile.thermal_url_file_run(urlfile);
}
function fnc_gks_erp_app_mobile_get_local_printers() {
  if (typeof gks_erp_app_mobile === 'undefined') return '';
  return gks_erp_app_mobile.get_local_printers();
}
function fnc_gks_erp_app_mobile_get_viva_terminal_data() {
  if (typeof gks_erp_app_mobile === 'undefined') return '';
  return gks_erp_app_mobile.get_viva_terminal_data();
}
function fnc_gks_erp_app_mobile_get_worldline_terminal_data() {
  if (typeof gks_erp_app_mobile === 'undefined') return '';
  return gks_erp_app_mobile.get_worldline_terminal_data();
}

var gks_and_app_pos_dialog_pay_open=false;
var gks_and_app_pos_dialog_card_open=false;

function fnc_gks_erp_app_mobile_pos_dialog_pay_open(myval) {
  gks_and_app_pos_dialog_pay_open=myval;
  if (typeof gks_erp_app_mobile === 'undefined') return '';
  return gks_erp_app_mobile.pos_dialog_pay_open(myval);
}
function from_main_back_button_pressed() {
  from_main_back_button_pressed_inside();
}
function fnc_gks_erp_app_mobile_get_location() {
  if (typeof gks_erp_app_mobile === 'undefined') return;
  temp=gks_erp_app_mobile.get_location();
  if (temp=='') return;
  //myalert('error:' + temp);
  temp=JSON.parse(temp);

  if (typeof temp === 'undefined') return;
  if (typeof temp !== 'object') return;
  if (typeof temp.lat !== 'number') return;
  if (typeof temp.lng !== 'number') return;
  
  var my_gps_location_lat=temp.lat;
  var my_gps_location_lng=temp.lng;
  var my_gps_location_res='ok';

  geoUri = 'http://maps.google.com/maps?q=loc:' + my_gps_location_lat + ',' + my_gps_location_lng + ' (point)';
  $('#gks_get_gps_location_report').html('<a href="' + geoUri +'" target="_blank">' + my_gps_location_lat + ' , ' +my_gps_location_lng + '</a>');
}

function fnc_gks_erp_app_mobile_set_worldline_terminal_token(token) {
  if (typeof gks_erp_app_mobile === 'undefined') return gks_lang('Σφάλμα')+' 54238325483 gks ERP App';
  return gks_erp_app_mobile.set_worldline_terminal_token(token);
}


    