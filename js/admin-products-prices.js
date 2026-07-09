/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


jQuery(document).ready(function($) {
  
  $('.filterselectbox').on('change', function() {
      
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      
      if (v==-2) { //is_custom_date
        if (gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
        
      } else {
        if (gks_custom_filters_date_elems.includes(sname)) {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        
        $('#filter-form').submit();
      }
  }); 
  
  $('input[name=datecheck]').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1,})); 
  
  $("#lightgallery_user").lightGallery({
  	selector: '.lightgalleryitem_user',
  	thumbnail:true,
  	hideBarsDelay:1000,
  });  


  var timers_text = new Array();
  var timers_text = new Array();
  
  function input_text_change() {
    var fname=''; 
    if ($(this).hasClass('gks_input_kostos')) fname='product_kostos';
    else if ($(this).hasClass('gks_input_price_yperx')) fname='product_price_yperx';
    else if ($(this).hasClass('gks_input_price_yperx_sale')) fname='product_price_yperx_sale';
    else if ($(this).hasClass('gks_input_price')) fname='product_price';
    else if ($(this).hasClass('gks_input_price_sale')) fname='product_price_sale';
    else if ($(this).hasClass('gks_input_price_retail')) fname='product_price_retail';
    else if ($(this).hasClass('gks_input_price_retail_sale')) fname='product_price_retail_sale';
    var pid=parseInt($(this).attr('data-id'));if (isNaN(pid)) pid=0;
	  if (fname=='' || pid<2) return;
	  thisid=fname+'_'+pid;
	  var myvalue=parseFloat($(this).val());if (isNaN(myvalue)) myvalue=0;
	  
	  //console.log(thisid);
	  if (timers_text[thisid] != undefined) {
      clearTimeout(timers_text[thisid]);
    }
    timers_text[thisid] = setTimeout(function() {
        saveToDB(thisid,fname,0,pid,myvalue,'input');
    }, 500);
  }
  function input_plist_text_change() {
    var fname=''; 
    if ($(this).hasClass('gks_input_price_plist')) fname='product_price_plist';
    else if ($(this).hasClass('gks_input_price_plist_sale')) fname='product_price_plist_sale';
    var plid=parseInt($(this).attr('data-plid'));if (isNaN(plid)) plid=0;
    var pid=parseInt($(this).attr('data-id'));if (isNaN(pid)) pid=0;
	  if (fname=='' || plid<10000 || pid<2) return;
	  thisid=fname+'_'+plid+'_'+pid;
	  var myvalue=parseFloat($(this).val());if (isNaN(myvalue)) myvalue=0;
	  
	  //console.log(thisid);
	  if (timers_text[thisid] != undefined) {
      clearTimeout(timers_text[thisid]);
    }
    timers_text[thisid] = setTimeout(function() {
        saveToDB(thisid,fname,plid,pid,myvalue,'input');
    }, 500);
  }  
  function input_checkbox_change() {
    var fname=''; 
    if ($(this).hasClass('gks_input_price_yperx_include_vat')) fname='product_price_yperx_include_vat';
    else if ($(this).hasClass('gks_input_price_include_vat')) fname='product_price_include_vat';
    else if ($(this).hasClass('gks_input_price_retail_include_vat')) fname='product_price_retail_include_vat';
    var pid=parseInt($(this).attr('data-id'));if (isNaN(pid)) pid=0;
	  if (fname=='' || pid<2) return;
	  thisid=fname+'_'+pid;
	  var myvalue=($(this).is(':checked') ? 1 : 0);
	  
	  //console.log(thisid);
	  if (timers_text[thisid] != undefined) {
      clearTimeout(timers_text[thisid]);
    }
    timers_text[thisid] = setTimeout(function() {
        saveToDB(thisid,fname,0,pid,myvalue,'checkbox');
    }, 500);
  }

  function input_plist_checkbox_change() {
    var fname=''; 
    if ($(this).hasClass('gks_input_price_plist_include_vat')) fname='product_price_plist_include_vat';
    var plid=parseInt($(this).attr('data-plid'));if (isNaN(plid)) plid=0;
    var pid=parseInt($(this).attr('data-id'));if (isNaN(pid)) pid=0;
	  if (fname=='' || plid<10000 || pid<2) return;
	  thisid=fname+'_'+plid+'_'+pid;
	  var myvalue=($(this).is(':checked') ? 1 : 0);
	  
	  //console.log(thisid);
	  if (timers_text[thisid] != undefined) {
      clearTimeout(timers_text[thisid]);
    }
    timers_text[thisid] = setTimeout(function() {
        saveToDB(thisid,fname,plid,pid,myvalue,'checkbox');
    }, 500);
  }
      
  function saveToDB(thisid,fname,plid,pid,myvalue,myfrom) {
    //console.log(thisid,fname,plid,pid,myvalue);
    datasend='';
    datasend+='&fname='+ encodeURIComponent($.base64.encode(fname));;
    datasend+='&plid=' + encodeURI(plid);
    datasend+='&pid='  + encodeURI(pid);
    datasend+='&myvalue='  + encodeURI(myvalue);
    //console.log(datasend);
    
    mysel='.gks_input_'+ fname.substring(8)+'[data-id="'+pid+'"]';
    if (plid>10000) {
      mysel+='[data-plid="'+plid+'"]';
    }
    //console.log(mysel);
    if (myfrom=='input') {
      $(mysel).css('background-color','greenyellow');
    } else if (myfrom=='checkbox') {
      $(mysel).parent().css('background-color','greenyellow'); 
    }
    
    $.ajax({
			url: 'admin-products-prices-exec.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_sel:mysel,
			gks_from:myfrom,
			//gks_plid:plid,
			error : function(jqXHR ,textStatus,  errorThrown) {
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
					  if (this.gks_from=='input') {
  					  $(this.gks_sel).animate({'background-color': 'white'}, 1000);
  					} else if (this.gks_from=='checkbox') {
  					  $(this.gks_sel).parent().animate({'background-color': 'white'}, 1000);
  					}
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});
		    
  }
    
  $('.gks_input_kostos, '+
    '.gks_input_price_yperx, .gks_input_price_yperx_sale, '+
    '.gks_input_price, .gks_input_price_sale, '+
    '.gks_input_price_retail, .gks_input_price_retail_sale').change(input_text_change);
  
  $('.gks_input_price_plist, .gks_input_price_plist_sale').change(input_plist_text_change);
  

  $('.gks_input_price_yperx_include_vat, '+
    '.gks_input_price_include_vat, '+
    '.gks_input_price_retail_include_vat').change(input_checkbox_change);

  $('.gks_input_price_plist_include_vat').change(input_plist_checkbox_change);

  $('.gks_input_kostos, '+
  '.gks_input_price_yperx, .gks_input_price_yperx_sale, '+
  '.gks_input_price, .gks_input_price_sale, '+
  '.gks_input_price_retail, .gks_input_price_retail_sale, '+
  '.gks_input_price_plist, .gks_input_price_plist_sale, '+
  '.gks_input_price_yperx_include_vat, '+
  '.gks_input_price_include_vat, '+
  '.gks_input_price_retail_include_vat, '+
  '.gks_input_price_plist_include_vat'
  ).on('keydown', function(event) {
    //down 40, up 38, right 39, left 37, 13 enter
    //console.log(event.which);
    if (event != undefined && event.which != undefined) {
      if (event.which == 40 || event.which == 38 || event.which == 13) { //down
        event.preventDefault();
        event.stopPropagation(); 
        data_aa=parseInt($(this).attr('data-aa')); if (isNaN(data_aa)) data_aa=0;
        if (data_aa>0) {
          elemclass=$(this).attr('class').split(' ');
          elem=$('.' + elemclass[0] + '[data-aa=' + (data_aa + (event.which == 38 ? -1 : 1)) + ']');
          if (elem.length>=1) {
            elem.focus().select();
          } else if (data_aa>1 && (event.which == 40 || event.which == 13)) {
            $('.' + elemclass[0] + '[data-aa=1]').focus().select();
          } else if (data_aa==1 && event.which == 38) {
            $('.' + elemclass[0] + ':last').focus().select();  
          } 
          //console.log(elemclass[0]);
        }
               
      } else if (event.which == 37 || event.which == 39) { 
        data_aa=parseInt($(this).attr('data-aa')); if (isNaN(data_aa)) data_aa=0;
        if (data_aa<=0) return;
        elem_tr=$('tr[data-aa="'+data_aa+'"]');
        if (elem_tr.length==0) return;
        data_cid=parseInt($(this).attr('data-cid')); if (isNaN(data_cid)) data_cid=0;
        if (data_cid<=0) return;
        
        dom_elem=$(this)[0];
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
              if (event.which == 37) gks_goto='left';
              else if (event.which == 39) gks_goto='right';
              break;
            default:

          }
          //console.log('gks_goto', gks_goto);          
          //console.log('data_cid', data_cid);          
        
          
          event.preventDefault();
          event.stopPropagation();
          if (gks_goto=='right') {
            for (ccc=data_cid+1; ccc<=from_php_max_cid;ccc++) {
              elem=elem_tr.find('input[data-cid="'+ccc+'"][data-aa="'+data_aa+'"]');
              if (elem.length==1 && elem.css('display')!='none' && elem.parent().css('display')!='none' && elem.parent().parent().css('display')!='none') {
                elem.focus().select();
                break;
              }
            }
          } else {
            for (ccc=data_cid-1; ccc>0;ccc--) {
              elem=elem_tr.find('input[data-cid="'+ccc+'"][data-aa="'+data_aa+'"]');
              if (elem.length==1 && elem.css('display')!='none' && elem.parent().css('display')!='none' && elem.parent().parent().css('display')!='none') {
                elem.focus().select();
                break;
              }
            }
          }
          
                 
        }
      }
      
    }  
      
  });

  
  
});
