/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

var need_save=false;
//var mychange = 'input keyup paste';
var mychange = 'change keyup paste';
//var mychange = 'propertychange input change keyup paste';

//var mychange = 'change';
var gks_page_loading=true;


jQuery(document).ready(function($) {


  $('td.mytdcm .mybtn_processing').click(function() {    mystate($(this), '050processing',false);  });
  $('td.mytdcm .mybtn_pause').click(function()      {    mystate($(this), '060pause',false);  });
  $('td.mytdcm .mybtn_completed').click(function()  {    mystate($(this), '100completed',false);  });
  $('td.mytdcm .mybtn_failed').click(function()     {    mystate($(this), '070failed',false);  });
  $('td.mytdcm .mybtn_cancelled').click(function()  {    mystate($(this), '020cancelled',false);  });

  $('#gks_multiselect_mybtn .mybtn_processing').click(function() {  
    if ($(this).hasClass('mybtn_processing_disable')) return;  
    mystate($(this), '050processing',true);  });
  $('#gks_multiselect_mybtn .mybtn_pause').click(function()      {  
    if ($(this).hasClass('mybtn_pause_disable')) return;  
    mystate($(this), '060pause',true);  });
  $('#gks_multiselect_mybtn .mybtn_completed').click(function()  {  
    if ($(this).hasClass('mybtn_completed_disable')) return;  
    mystate($(this), '100completed',true);  });
  $('#gks_multiselect_mybtn .mybtn_failed').click(function()     {  
    if ($(this).hasClass('mybtn_failed_disable')) return;  
    mystate($(this), '070failed',true);  });
  $('#gks_multiselect_mybtn .mybtn_cancelled').click(function()  {  
    if ($(this).hasClass('mybtn_cancelled_disable')) return;  
    mystate($(this), '020cancelled',true);  });


  function mystate(elem,newstate,is_multi) {
    data_id=0;
    ids_str='';
    if (is_multi==false) {
      data_id=parseInt(elem.attr('data-id'));
      if (isNaN(data_id)) data_id=0;
      if (data_id<=0) return;
    } else {
      var ids=[];

      $('.order_table_tr').each(function() {
        if ($(this).hasClass('gks_hide_tr')==false) {
          if ($(this).find('.gks_multiselectcheck_selected').length==1) {
            data_id=parseInt($(this).find('.td_buttons').attr('data-id'));
            if (isNaN(data_id)) data_id=0;
            if (data_id>0) {
              ids.push(data_id);
            }
          }
        }
      });
            
      ids_str = encodeURIComponent($.base64.encode(JSON.stringify(ids)));
      
    }
    
    //console.log(data_id);
    //console.log(newstate);
    
    datasend='';
    datasend+='&is_multi='  + (is_multi ? '1' : '0');
    datasend+='&id='  + data_id;
    datasend+='&ids_str=' + ids_str;
    datasend+='&newstate='  + encodeURI(newstate);
    datasend+='&posto_id=' + from_php_posto_id;
    
    //datasend+='&data_id='  + encodeURI($("#mypostform #production_ergasia_descr").val().trim());
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-production-posto-run-exec.php',
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
					  window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     

    return false;    
    
    
  }
  
  var datestart = new Date();
  setInterval(function() {
    datenow = new Date();
    var diff = datenow - datestart;
    
    $('.tdtime').each(function() {
      extra_secs = parseInt($(this).attr('data-secs'));
      if (isNaN(extra_secs)) extra_secs=0;
      var msec = diff + extra_secs*1000;
      
      var yy = Math.floor(msec / 1000 / 60 / 60 /(365*24));
      msec -= yy * 1000 * 60 * 60 * (365*24);
      
      var mn = Math.floor(msec / 1000 / 60 / 60 /(30*24));
      msec -= mn * 1000 * 60 * 60 * (30*24);
      
      var dd = Math.floor(msec / 1000 / 60 / 60 /24);
      msec -= dd * 1000 * 60 * 60 * 24;
      
      var hh = Math.floor(msec / 1000 / 60 / 60);
      msec -= hh * 1000 * 60 * 60;
      var mm = Math.floor(msec / 1000 / 60);
      msec -= mm * 1000 * 60;
      var ss = Math.floor(msec / 1000);
      msec -= ss * 1000;
      
      
      yys=yy; 
      mns=mn;
      dds=dd;
      hhs=hh; if (hh < 10) hhs='0' + hhs;
      mms=mm; if (mm < 10) mms='0' + mms;
      sss=ss; if (ss < 10) sss='0' + sss;
      
      if (yy>0)      myhtml=yys + '-' + mns + '-' + dds + '.' + hhs + ':' + mms + ':' + sss;
      else if (mn>0) myhtml=            mns + '-' + dds + '.' + hhs + ':' + mms + ':' + sss; 
      else if (dd>0) myhtml=                        dds + '.' + hhs + ':' + mms + ':' + sss; 
      else if (hh>0) myhtml=                                    hhs + ':' + mms + ':' + sss; 
      else           myhtml=                                                mms + ':' + sss; 

      $(this).html(myhtml);
      
      
    });
    
  }, 1000);


  var timer_refresh  = setInterval(myTimer, 100);
  var time_start = performance.now();
  var time_end = 2*60*1000; //2 lepta
  function myTimer() {
    var time_now = performance.now();
    diafora = (time_now - time_start);
    //console.log(diafora);
    
    pososto = diafora/time_end;
    pososto=(100-pososto*100);
    
    if (pososto<0) {
      pososto=0;
      window.clearTimeout(timer_refresh);
      //window.location.reload();
      window.location.href = 'admin-production-posto-run.php?id=' + from_php_posto_id;
    }
    //console.log(pososto);
    $('#psososto_refresh').css('width',pososto.formatMoney(2,'.','') + '%');
    
  }

  function call_sxolio_change() {gks_resize_textarea($(this));}
  $('.call_sxolio').on(mychange, call_sxolio_change);
  $('.call_sxolio').each(function() {gks_resize_textarea($(this));});

  var timers_text = new Array();
  $('.call_sxolio').on(mychange, function(event) {
    var thisid=$(this).attr('id');
	  thisid=thisid.substring(7);
	  var mytext = $(this).val();
    if (timers_text[thisid] != undefined) {
      clearTimeout(timers_text[thisid]);
    }
    timers_text[thisid] = setTimeout(function() {
        // Runs 1 second (1000 ms) after the last change    
        saveToDB(thisid,mytext);
    }, 500);
    time_start = performance.now();
    
  });
  
  function saveToDB(thisid,mytext)
  {
    datasend='';
    datasend+='&myid='  + encodeURI(thisid);
    datasend+='&mytext='  + encodeURIComponent($.base64.encode(mytext.trim()));;
    
    //console.log(datasend);
    //return; 
    //$('body').addClass("myloading");
    
    $('#sxolio_'+ thisid).css('background-color','greenyellow');
    
    $.ajax({
			url: 'admin-production-posto-run-sxolio-exec.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  //$("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				//$("body").removeClass("myloading");
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
  					$('#sxolio_'+ data.myid).animate({'background-color': 'white'}, 1000);
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});         
  }  

  $('.order_priority').rateYo({
    fullStar: true,
    numStars: 5,
    maxValue:5,
    starWidth: '14px',
    spacing: '0px',
    readOnly: true,
  });
  

  $('#gks_filter_id').focus();
  $('#gks_filter_id').on('change keyup paste',function() {
    time_start = performance.now();
    var temp_order_id=parseInt($(this).val());
    if (isNaN(temp_order_id)) temp_order_id=0;
    
    $('.order_table_tr').each(function() {
      if (temp_order_id<=0) {
        $(this).removeClass('gks_hide_tr');
      } else {
        data_order_id=$(this).attr('data-order-id');
        if (data_order_id.includes(temp_order_id + '')) 
          $(this).removeClass('gks_hide_tr');
        else
          $(this).addClass('gks_hide_tr');
      }
    });
    gks_multiselectcheck_after_select();
  });
 
 
 
  $('td .gks_multiselectcheck').click(function() {
    time_start = performance.now();
    if ($(this).hasClass('gks_multiselectcheck_selected')) {
      $(this).removeClass('gks_multiselectcheck_selected');
    } else {
      $(this).addClass('gks_multiselectcheck_selected');
    }
    gks_multiselectcheck_after_select();
  });
  $('#gks_multiselect_all').click(function() {
    time_start = performance.now();
    if ($(this).attr('data-all')=='1') {
      $(this).attr('data-all','0').removeClass('gks_multiselectcheck_selected');
      $('.order_table_tr').each(function() {
        if ($(this).hasClass('gks_hide_tr')==false) {
          $(this).find('.gks_multiselectcheck').removeClass('gks_multiselectcheck_selected');
        }
      });
    } else {
      $(this).attr('data-all','1').addClass('gks_multiselectcheck_selected');
      $('.order_table_tr').each(function() {
        if ($(this).hasClass('gks_hide_tr')==false) {
          $(this).find('.gks_multiselectcheck').addClass('gks_multiselectcheck_selected');
        }
      });
    }
    gks_multiselectcheck_after_select();
  });
  
  function gks_multiselectcheck_after_select() {
    var cc_all=0;
    var cc_show=0;
    var cc_selected=0;
    var cc_processing=0;
    var cc_pause=0;
    var cc_completed=0;
    var cc_cancelled=0;
    var cc_failed=0;
    
    $('.order_table_tr').each(function() {
      cc_all++;
      if ($(this).hasClass('gks_hide_tr')==false) {
        cc_show++;
        if ($(this).find('.gks_multiselectcheck_selected').length==1) {
          cc_selected++;
          if ($(this).find('.mybtn_processing').length==1) cc_processing++;
          if ($(this).find('.mybtn_pause').length==1) cc_pause++;
          if ($(this).find('.mybtn_completed').length==1) cc_completed++;
          if ($(this).find('.mybtn_cancelled').length==1) cc_cancelled++;
          if ($(this).find('.mybtn_failed').length==1) cc_failed++;
        }
      }
    });
    
    //console.log(cc_all,cc_show,cc_selected,cc_processing,cc_pause,cc_completed,cc_cancelled,cc_failed);
    
    $('#gks_multiselect_cc span').html(cc_selected);
        
    elem=$('#gks_multiselect_mybtn');
    if (cc_selected==cc_processing) {
       elem.find('.mybtn_processing').removeClass('mybtn_processing_disable');
    } else {
       elem.find('.mybtn_processing').addClass('mybtn_processing_disable');
    }
    if (cc_selected==cc_pause) {
       elem.find('.mybtn_pause').removeClass('mybtn_pause_disable');
    } else {
       elem.find('.mybtn_pause').addClass('mybtn_pause_disable');
    }
    if (cc_selected==cc_completed) {
       elem.find('.mybtn_completed').removeClass('mybtn_completed_disable');
    } else {
       elem.find('.mybtn_completed').addClass('mybtn_completed_disable');
    }
    if (cc_selected==cc_cancelled) {
       elem.find('.mybtn_cancelled').removeClass('mybtn_cancelled_disable');
    } else {
       elem.find('.mybtn_cancelled').addClass('mybtn_cancelled_disable');
    }
    if (cc_selected==cc_failed) {
       elem.find('.mybtn_failed').removeClass('mybtn_failed_disable');
    } else {
       elem.find('.mybtn_failed').addClass('mybtn_failed_disable');
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

    if (need_save==false) return;
    return gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');
  };

  need_save=false;
  
  //console.log('load end');
  
});

