/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

var gks_page_loading=true;

var dialog_posotitaonhand;

jQuery(document).ready(function($) {

  $('.filterselectbox').on('change', function() {
      
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      
      if (v==-2) { //is_custom_date
//        if (gks_custom_filters_date_elems.includes(sname)) {
//          $('#filterdate-' + sname).css('display','inline-block'); 
//          $('#' + sname + '-from').attr('name',sname + '-from');
//          $('#' + sname + '-to').attr('name',sname + '-to');
//        }
        
      } else {
//        if (gks_custom_filters_date_elems.includes(sname)) {
//          $('#filterdate-' + sname).css('display','none'); 
//          $('#' + sname + '-from').attr('name','');
//          $('#' + sname + '-to').attr('name','');
//        }
        
        $('#filter-form').submit();
      }
  }); 
  
  
  $("#lightgallery_user").lightGallery({
  	selector: '.lightgalleryitem_user',
  	thumbnail:true,
  	hideBarsDelay:1000,
  });  

  var warehouse_id =0;
  var product_id=0;
  var lot_product_id=0;
   
  $('.mytdypoloipo').mousedown(function(event) {
    $('#search_string').blur();
    
    warehouse_id=parseInt($(this).attr('data-warehouse'));
    if (isNaN(warehouse_id)) warehouse_id=0;
    warehouse_title=$('#warehouse_title_'+ warehouse_id).html();
    //console.log(warehouse_title);
    
    product_id=parseInt($(this).attr('data-product'));
    if (isNaN(product_id)) product_id=0;
    product_title=$('#product_title_'+ product_id).html();
    
    lot_product_id=parseInt($(this).attr('data-lot'));
    if (isNaN(lot_product_id)) lot_product_id=0;
    lot_name=$('#lot_title_'+ lot_product_id).html();
    
    //console.log(product_title);
    
    if (from_php_perm_ret_apografi_add==false) {
      gks_open_product_history(event);
      return;
    }
    
    val=parseFloat($(this).attr('data-val'));
    if (isNaN(val)) val=0;

    $('#dialog_posotitaonhand_posostita').val(val);
    $('#dialog_posotitaonhand_lot').html(lot_name);
    $('#dialog_posotitaonhand_product').html(product_title);
    $('#dialog_posotitaonhand_warehouse').html(warehouse_title);
    
	  dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 450) dwidth=450;
	  if (dheight> 400) dheight=400;
	  dialog_posotitaonhand.dialog('option', 'width', dwidth);
	  dialog_posotitaonhand.dialog('option', 'height', dheight);
	  $('#dialog_posotitaonhand').parent().css({position:'fixed'});      
    dialog_posotitaonhand.dialog('open');
    $('#dialog_posotitaonhand_posostita').select();
      
  });


  $('#dialog_posotitaonhand_history').mousedown(function(event) {
    gks_open_product_history(event);
  });
  
  function gks_open_product_history(event) {
    
    myhref='admin-whi-mov-balance-lots-serials-history.php?fwarehouse_id=' + warehouse_id + '&fproduct_id=-1&flot_id=' + lot_product_id;
    //console.log(myhref);
    dialog_posotitaonhand.dialog('close');
    if (event.which ==2) { //middle Click
      vvv = window.open(myhref, '_blank');
    } else if (event.which ==1) {
      if (event.ctrlKey) {
        vvv = window.open(myhref, '_blank');
      } else {
        window.location.href=myhref;
      }      
    }    
  }
  
  
  dialog_posotitaonhand = $( "#dialog_posotitaonhand" ).dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_posotitaonhand_ok",
        html: '<i class="fa fa-pen-square"></i> '+gks_lang('Ενημέρωση'),
        //icon: "ui-icon-circle-plus",
        click: function() {
          posotitaonhand=$("#dialog_posotitaonhand_posostita").val();
          if (posotitaonhand=='') {
            myalert('error:'+gks_lang('Ορίστε την ποσότητα'));
            return;
          }
          
          datasend='';
      
          datasend+='&warehouse_id='  + encodeURI(warehouse_id);
          datasend+='&product_id='  + encodeURI(product_id);
          datasend+='&lot_product_id='  + encodeURI(lot_product_id);
          datasend+='&posotitaonhand='  + encodeURI($("#dialog_posotitaonhand_posostita").val().trim());
          //console.log(datasend);
      
          $('body').addClass("myloading");
          
          $.ajax({
      			url: '/my/admin-whi-mov-balance-lots-serials-apografi-exec.php',
      			type: 'POST',
      			cache: false,
      			dataType: 'json',
      			data: datasend,
      			gks_warehouse_id:warehouse_id,
      			gks_product_id:product_id,
      			gks_lot_product_id:lot_product_id,
      			
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
        					//window.location.reload();
        					$('.mytdypoloipo[data-warehouse=' + this.gks_warehouse_id + '][data-product=' + this.gks_product_id + '][data-lot=' + this.gks_lot_product_id + ']').html(data.balance).attr('data-val',data.balance_val);
        					$('.mytdtotal[data-product=' + this.gks_product_id + '][data-lot=' + this.gks_lot_product_id + ']').html(data.total_balance);
        					
        					dialog_posotitaonhand.dialog('close');
      					} else {
      						myalert('error:' + $.base64.decode(data.message));
      					}
      				}
      			}
      			
      		});			
		    }	
      },
      {
        id: "dialog_posotitaonhand_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Άκυρο'),
        //icon: "ui-icon-cancel",
        click: function() {
          $( this ).dialog( "close" );
        }			
      },      
    ]
        

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
  

	  
});
