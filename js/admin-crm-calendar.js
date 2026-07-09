/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

var hashchange='';
var hashmydata = {};
var from_hash_analyze=false;

var need_save=false;
var mychange = 'change keyup paste';
var gks_page_loading=true;

var dialog_event;

var obj_calendar;

//import { req } from 'superagent'; // ajax library

jQuery(document).ready(function($) {



    

  var control_enter_active=false;
  $(document).on('keypress', function(event) {
    //var tag = e.target.tagName.toLowerCase();
    if (event.which == 10 && event.ctrlKey) {
      if (gks_event_edit_is_open==false) return;
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
  
  $('#submit_button_ok_custom').click(function(event) {mysubmit(); return false;});

  var gks_tooltipster_isset=false;
  function gks_calendar_buttons(viewname) {
    $('.fc-prevYear-button').addClass('btn-sm');
    $('.fc-prev-button').addClass('btn-sm');
    $('.fc-next-button').addClass('btn-sm');
    $('.fc-nextYear-button').addClass('btn-sm');
    $('.fc-today-button').addClass('btn-sm');
  
    $('.fc-dayGridMonth-button').addClass('btn-sm');
    $('.fc-timeGridWeek-button').addClass('btn-sm');
    $('.fc-timeGridDay-button').addClass('btn-sm');
    $('.fc-listDay-button').addClass('btn-sm');
    $('.fc-listWeek-button').addClass('btn-sm');
    $('.fc-listMonth-button').addClass('btn-sm');
    $('.fc-listYear-button').addClass('btn-sm');
    $('.fc-gks_full24-button').addClass('btn-sm');
    
    if (gks_tooltipster_isset==false) {
      $('.fc-dayGridMonth-button').attr('title',gks_lang('Μήνας')).tooltipster({theme: 'tooltipster-noir'});
      $('.fc-timeGridWeek-button').attr('title',gks_lang('Εβδομάδα')).tooltipster({theme: 'tooltipster-noir'});
      $('.fc-timeGridDay-button').attr('title',gks_lang('Ημέρα')).tooltipster({theme: 'tooltipster-noir'});
      $('.fc-listDay-button').attr('title',gks_lang('Λίστα Ημέρας')).tooltipster({theme: 'tooltipster-noir'});
      $('.fc-listWeek-button').attr('title',gks_lang('Λίστα Εβδομάδας')).tooltipster({theme: 'tooltipster-noir'});
      $('.fc-listMonth-button').attr('title',gks_lang('Λίστα Μήνα')).tooltipster({theme: 'tooltipster-noir'});
      $('.fc-listYear-button').attr('title',gks_lang('Λίστα Έτους')).tooltipster({theme: 'tooltipster-noir'});
      $('.fc-gks_full24-button').attr('title',(from_php_full24 == '1' ? '08:00-18:00' : '00:00-24:00')).tooltipster({theme: 'tooltipster-noir'});
      gks_tooltipster_isset=true;
    }
    
    if (viewname=='timeGridWeek' || viewname=='timeGridDay') {//dayGridMonth timeGridWeek timeGridDay listMonth
      $('.fc-gks_full24-button').prop('disabled', false);
    } else {
      $('.fc-gks_full24-button').prop('disabled', true);
    }
    
    if (gks_page_loading) return;
    if (from_hash_analyze) return;

    datasend='&o=' + encodeURIComponent($.base64.encode('calendar'));
    datasend+='&s=' + encodeURIComponent($.base64.encode('view'));
    datasend+='&v=' + encodeURIComponent($.base64.encode(viewname));
    $.ajax({
			url: '/my/admin-users-settings-item-exec.php',type: 'POST',cache: false,dataType: 'json',	data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {console.log(jqXHR.responseText);},				
			success: function(data) {if (!data) {console.log('error:'+gks_lang('Παρακαλώ δοκιμάστε αργότερα'));} 
			  else {if (data.success == false) {console.log('error:' + $.base64.decode(data.message));}}
			},
		});
    set_hash();  		
     
    
  }

  
  var gks_event_edit_has_run=false;
  
  
  //https://fullcalendar.io/docs#toc
  var calendarEl = document.getElementById('calendar_div');
  obj_calendar = new FullCalendar.Calendar(calendarEl, {
    timeZone: 'local', //none, local, UTC
    themeSystem: 'bootstrap',
    bootstrapFontAwesome: {
      close: 'fa-times',
      prev: 'fa-chevron-left',
      next: 'fa-chevron-right',
      prevYear: 'fa-angle-double-left',
      nextYear: 'fa-angle-double-right',
      dayGridMonth: 'fa-calendar-alt',    
      timeGridWeek: 'fa-calendar-week',  
      timeGridDay: 'fa-calendar-day',
      listDay: 'fa-list',
      listWeek: 'fa-list-ul',
      listMonth: 'fa-bars',
      listYear: 'fa-list-ol',
      
    },
    initialView: from_php_view, //'timeGridDay'
    nowIndicator:true,
    customButtons: {
      gks_full24: {
        //text: 'custom!',
        bootstrapFontAwesome: (from_php_full24 == '1' ? 'fa-compress' : 'fa-expand'),
        click: function() {
          if ($(this).prop('nodeName')=='SPAN') {
            myelem=$(this);
          } else {
            myelem=$(this).find('span');
          }
          
          vvv='0';
          if (myelem.hasClass('fa-expand')) {
            myelem.removeClass('fa-expand').addClass('fa-compress');
            obj_calendar.setOption('slotMinTime','00:00:00');
            obj_calendar.setOption('slotMaxTime','24:00:00');
            $('.fc-gks_full24-button').tooltipster('content', '08:00-18:00');
            vvv='1';
          } else {
            obj_calendar.setOption('slotMinTime','08:00:00');
            obj_calendar.setOption('slotMaxTime','18:00:00');
            myelem.addClass('fa-expand').removeClass('fa-compress');
            $('.fc-gks_full24-button').tooltipster('content', '00:00-24:00');
            vvv='0';
          }
          if (from_hash_analyze) return;
          
          datasend='&o=' + encodeURIComponent($.base64.encode('calendar'));
          datasend+='&s=' + encodeURIComponent($.base64.encode('full24'));
          datasend+='&v=' + encodeURIComponent($.base64.encode(vvv));
          $.ajax({
      			url: '/my/admin-users-settings-item-exec.php',type: 'POST',cache: false,dataType: 'json',	data: datasend,
      			error : function(jqXHR ,textStatus,  errorThrown) {console.log(jqXHR.responseText);},				
      			success: function(data) {if (!data) {console.log('error:'+gks_lang('Παρακαλώ δοκιμάστε αργότερα'));} 
      			  else {if (data.success == false) {console.log('error:' + $.base64.decode(data.message));}}
      			},
      		});            
          set_hash();
        }
      }
    },
    headerToolbar: {
      left: 'prevYear,prev,next,nextYear today',
      center: 'title',
      right: 'listYear listMonth listWeek listDay dayGridMonth timeGridWeek timeGridDay gks_full24' //,listMonth
    },
//    views: {
//      timeGridFourDay: {
//        type: 'timeGrid',
//        //duration: { days: 4 },
//        dayCount: 4,
//        buttonText: '4 day'
//      },
//    },    
    titleFormat: { //{ year: 'numeric', month: 'short or long', day: 'numeric' } // like 'Sep 13 2009', for week views
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    },
    eventTimeFormat: { // like '14:30'
      hour: '2-digit',
      minute: '2-digit',
      //second: '2-digit',
      hour12: false
    },   
    slotLabelFormat: { // like '14:30'
      hour: '2-digit', //'numeric', //'2-digit',
      minute: '2-digit',
      //second: '2-digit',
      //hour12: false,
      hourCycle: 'h23',
      //omitZeroMinute: false,
    },  
    slotDuration:'00:15:00',
    slotEventOverlap:false, //https://fullcalendar.io/docs/slotEventOverlap
    height: 'auto', //1150, // $(window).height() * 0.96 - $('#calendartable').position().top,
    
    //locale: 'el',
    locale: from_php_gks_fullcalendar_locale,
    slotMinTime: (from_php_full24 == '1' ? '00:00:00' : '08:00:00'),
    slotMaxTime: (from_php_full24 == '1' ? '24:00:00' : '18:00:00'),
    businessHours: {
      // days of week. an array of zero-based day of week integers (0=Sunday)
      daysOfWeek: [ 1, 2, 3, 4 , 5], // Monday - Thursday
      startTime: '09:00', // a start time (10am in this example)
      endTime: '17:00', // an end time (6pm in this example)
    },
    businessHours: true, // display business hours
    
    initialDate: from_php_initialdate,
    navLinks: true, // can click day/week names to navigate views
    weekNumbers: true,
    weekNumberCalculation:"ISO",
    weekText:'',
    editable: true,
    selectable: true,
    viewClassNames:(function(view, elem) {
      gks_calendar_buttons(view.view.type); //dayGridMonth timeGridWeek timeGridDay listMonth
    }),
    //Touch Support
    longPressDelay: 500,
    eventLongPressDelay: 500,
    selectLongPressDelay: 500,
    
    eventDidMount: function(info) {
      
      tooltiptext=nl2br(info.event.extendedProps.c_message);
      
      c_objects='';
      for(var list_i7=0; list_i7 < info.event.extendedProps.c_objects.length; list_i7++) {
        c_objects+=info.event.extendedProps.c_objects[list_i7].obj_name;
        if (info.event.extendedProps.c_objects[list_i7].contact_id>0) {
          c_objects+=', <a href="admin-users-item.php?id=' + info.event.extendedProps.c_objects[list_i7].contact_id + '">' + info.event.extendedProps.c_objects[list_i7].contact_name + '</a>';
        } else if (info.event.extendedProps.c_objects[list_i7].contact_name!='') {
          c_objects+=', ' + info.event.extendedProps.c_objects[list_i7].contact_name;
        }
        if (info.event.extendedProps.c_objects[list_i7].esoda!='') {
          c_objects+=', ' + info.event.extendedProps.c_objects[list_i7].esoda;
        }
      }
      if (c_objects!='') {
        if (tooltiptext!='') tooltiptext+='<br>' + c_objects;
        else tooltiptext=c_objects;
      }
      
      if (tooltiptext!=''){
	      var tooltipster_info= $(info.el).tooltipster({
	        theme: 'tooltipster-noir',
	        contentAsHTML: true,
	        interactive:true,
	        content:'<div>' + tooltiptext  + '</div>',
	      });
	      info.event.gks_tooltipster_info=tooltipster_info;
	    }
      
      
      if (gks_event_edit_has_run==false && from_php_start_id==info.event.extendedProps.rec_id && info.event.extendedProps.c_table=='gks_calendar') {
				$('#gks_full24').click();
				
				gks_event_edit(parseInt(info.event.extendedProps.rec_id), (info.event.start), (info.event.end), info.event, info);
				gks_event_edit_has_run=true;
      }
    },
//		events_old: {
//			url: 'admin-crm-calendar-events.php',
//			format: 'json', //'ics',
//			method: 'POST',
////			extraParams: {
////        custom_param1: 'something',
////        custom_param2: 'somethingelse'
////      },
//			failure: function() {
//				myalert('error:'+gks_lang('Σφάλμα κατά την λήψη των συμβάντων'));
//			}
//		},
    events: function(info, successCallback, failureCallback) {
      //datasend+='&end=' +  moment($('#c_end').datetimepicker('getValue')).format("YYYY-MM-DD HH:mm:ss");
      //datasend+='&allday=' + encodeURIComponent(($('#mypostform #c_allday').is(':checked') ? '1' : '0'));
      //console.log(info);
      //start: 2021-01-19T00:00:00+02:00
      datasend='start=' + encodeURIComponent(info.startStr) + '&end=' + encodeURIComponent(info.endStr);
      
      ddd=new Date(info.startStr);
      sss=ddd.getDate()+'/'+(ddd.getMonth()+1)+'/'+ddd.getFullYear();
      $('#c_cal_small').datetimepicker('setOptions', {value: sss});

      
      $('#calc_hourglass').show();
      $.ajax({
        thissuccessCallback: successCallback,
  			url: 'admin-crm-calendar-events.php',
  			type: 'POST',
  			cache: false,
  			dataType: 'json',
  			data: datasend,
  			error : function(jqXHR ,textStatus,  errorThrown) {
  				myalert('error:' + jqXHR.responseText);
  				$('#calc_hourglass').hide();
  			},
  			success: function(data) {
  			  $('#calc_hourglass').hide();
  				if (!data) {
  					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  				} else {

            var cal_user_array=[];
            $('input[name=cal_user]:checked').each(function() {
              uid=parseInt($(this).attr('data-id'));
              if (isNaN(uid)) uid=-1;
              if (uid==0) uid=from_php_my_wp_user_id;
              cal_user_array.push(uid);
            });
            var cal_user_array_task=[];
            $('input[name=cal_user_task]:checked').each(function() {
              uid=parseInt($(this).attr('data-id'));
              if (isNaN(uid)) uid=-1;
              if (uid==0) uid=from_php_my_wp_user_id;
              cal_user_array_task.push(uid);
            });
            var cal_user_array_activ=[];
            $('input[name=cal_user_activ]:checked').each(function() {
              uid=parseInt($(this).attr('data-id'));
              if (isNaN(uid)) uid=-1;
              if (uid==0) uid=from_php_my_wp_user_id;
              cal_user_array_activ.push(uid);
            });            
              				  
  				  for(var list_i8=0; list_i8<data.length; list_i8++) {
  				    //console.log(data[list_i8]);
  				    if (data[list_i8].c_table=='gks_calendar') {
  				      if (cal_user_array.includes(data[list_i8].c_user_id)==false) {
  				        data[list_i8].display='none';
  				      }
  				    } else if (data[list_i8].c_table=='gks_crm_tasks') {
                found_user=false;
                for(uu=0; uu<data[list_i8].c_user_id_multi.length;uu++) {
                  if (cal_user_array_task.includes(data[list_i8].c_user_id_multi[uu])) {
                    found_user=true;break;
                  }
                }  				      
  				      if (found_user==false) {
  				        data[list_i8].display='none';
  				      }
  				    } else if (data[list_i8].c_table=='gks_crm_activity') {
                if (cal_user_array_activ.includes(data[list_i8].c_user_id)==false) {
  				        data[list_i8].display='none';
  				      }
  				    }
  				  }

  				 
  				  this.thissuccessCallback(data);
  				  //console.log(data);

  				}
  			}
  		});
  		
  		
  		set_hash();
  		      
//      req.get('admin-crm-calendar-events.php')
//        .type('json')
//        .query({
//          start: info.start.valueOf(),
//          end: info.end.valueOf()
//        })
//        .end(function(err, res) {
//  
//          if (err) {
//            failureCallback(err);
//          } else {
//  
//            successCallback(
//              Array.prototype.slice.call( // convert to array
//                res.getElementsByTagName('event')
//              ).map(function(eventEl) {
//                return {
//                  title: eventEl.getAttribute('title'),
//                  start: eventEl.getAttribute('start')
//                }
//              })
//            )
//          }
//        })
    },

    eventContent: function(arg) {
      //console.log(arg);
      //console.log(arg.event);
      //console.log(arg.event.extendedProps);
      
      //return arg.event.title + 'ggg';
      mycustomer='';
      if (arg.event.extendedProps.c_customer!='') mycustomer+=arg.event.extendedProps.c_customer;
      
      
      mylocation='';
      if (arg.event.extendedProps.c_odos!='') mylocation+=arg.event.extendedProps.c_odos + ' ';
      if (arg.event.extendedProps.c_arithmos!='') mylocation+=arg.event.extendedProps.c_arithmos;
      mylocation=mylocation.trim();
      if (mylocation!='') mylocation+=', ';
      if (arg.event.extendedProps.c_orofos!='') mylocation+=arg.event.extendedProps.c_orofos + ', ';
      if (arg.event.extendedProps.c_perioxi!='') mylocation+=arg.event.extendedProps.c_perioxi + ', ';
      if (arg.event.extendedProps.c_poli!='') mylocation+=arg.event.extendedProps.c_poli + ', ';
      if (arg.event.extendedProps.c_tk!='') mylocation+=arg.event.extendedProps.c_tk + ', ';
      
      if (mylocation!='') mylocation = mylocation.substring(0, mylocation.length-2);
      
      a='';
      a+='<div class="gks_calendar_time">' + arg.timeText + '</div>';
      a+='<div class="gks_calendar_container">';
      a+='  <div class="gks_calendar_title">' + arg.event.title + '</div>';
      //a+='  <div class="gks_calendar_message">' + arg.event.extendedProps.c_message + '</div>';
      if (mycustomer!='') a+='  <div class="gks_calendar_customer">' + mycustomer + '</div>';
      if (mylocation!='') a+='  <div class="gks_calendar_location">' + mylocation + '</div>';
      a+='</div>';
      
      mydiv = document.createElement('div');
      mydiv.innerHTML = a;
    

      arrayOfDomNodes = [ mydiv ]
      return { domNodes: arrayOfDomNodes }
    },
		select: function(selectionInfo ) {
		  if (from_php_perm_ret_add) gks_event_edit(-1, (selectionInfo.start), (selectionInfo.end), null,selectionInfo);
    },

    eventResize: function(eventResizeInfo ) {
      datasend='cmd=resize&id=' + eventResizeInfo.event.extendedProps.rec_id + "&start=" + moment(eventResizeInfo.event.start).format("YYYY-MM-DD HH:mm:ss");
      if (eventResizeInfo.event.end != null) {
        datasend+= "&end=" + moment(eventResizeInfo.event.end).format("YYYY-MM-DD HH:mm:ss");
      }
      datasend+= "&allday=" + (eventResizeInfo.event.allDay ? '1' : '0');
      
      url_send='/my/admin-crm-calendar-item-exec.php';
      if (eventResizeInfo.event.extendedProps.c_table=='gks_crm_tasks') {
        url_send='/my/admin-crm-task-item-calendar-exec.php';
      }
      if (eventResizeInfo.event.extendedProps.c_table=='gks_crm_activity') {
        console.log('fix me');
        return;
        url_send='/my/admin-crm-task-item-calendar-exec.php';
      }
      
      
      $('#calc_hourglass').show();
      $.ajax({
  			myrevertFunc:eventResizeInfo.revert,
  			
  			url: url_send,
  			type: 'POST',
  			cache: false,
  			dataType: 'json',
  			data: datasend,
  			error : function(jqXHR ,textStatus,  errorThrown) {
  				myalert('error:' + jqXHR.responseText);
  				this.myrevertFunc();
  				$('#calc_hourglass').hide();
  			},				
  			success: function(data) {
  			  $('#calc_hourglass').hide();
  				if (!data) {
  				  this.myrevertFunc();
  					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  				} else {
  					if (data.success == true) {
              
  					} else {
  					  this.myrevertFunc();
  						myalert('error:' + $.base64.decode(data.message));
  					}
  				}
  			}
  		});      
    },    

    defaultTimedEventDuration:'01:00',
    eventDrop: function(eventDropInfo ) {
      if (eventDropInfo.event.allDay) {
        if (eventDropInfo.event.end == null) {
          eventDropInfo.event.setEnd(moment(eventDropInfo.event.start).add(24,'hours').toDate());
        }
      } else {
        if (eventDropInfo.event.end == null) {
          eventDropInfo.event.setEnd(moment(eventDropInfo.event.start).add(1,'hours').toDate()); //check the defaultTimedEventDuration
        }
      }
      
      datasend ='cmd=move&id=' + eventDropInfo.event.extendedProps.rec_id + "&start=" + moment(eventDropInfo.event.start).format("YYYY-MM-DD HH:mm:ss");
      if (eventDropInfo.event.end != null) {
        datasend+= "&end=" + moment(eventDropInfo.event.end).format("YYYY-MM-DD HH:mm:ss");
      }
      datasend+= "&allday=" + (eventDropInfo.event.allDay ? '1' : '0');
     
      url_send='/my/admin-crm-calendar-item-exec.php';
      if (eventDropInfo.event.extendedProps.c_table=='gks_crm_tasks') {
        url_send='/my/admin-crm-task-item-calendar-exec.php';
      }
      if (eventDropInfo.event.extendedProps.c_table=='gks_crm_activity') {
        url_send='/my/admin-crm-activity-item-calendar-exec.php';
      }

      $('#calc_hourglass').show();
      $.ajax({
  			myrevertFunc:eventDropInfo.revert,
  			
  			url: url_send,
  			type: 'POST',
  			gks_thiseventid: parseInt(eventDropInfo.event.extendedProps.rec_id),
  			gks_c_table:eventDropInfo.event.extendedProps.c_table,
  			cache: false,
  			dataType: 'json',
  			data: datasend,
  			error : function(jqXHR ,textStatus,  errorThrown) {
  				myalert('error:' + jqXHR.responseText);
  				this.myrevertFunc();
  				$('#calc_hourglass').hide();
  			},				
  			success: function(data) {
  			  $('#calc_hourglass').hide();
  				if (!data) {
  				  this.myrevertFunc();
  					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  				} else {
  					if (data.success == true) {
//  					  if (this.gks_c_table=='gks_crm_activity') {
//                if (this.gks_thiseventid>0) {
//    				      var rrrrrrr= 'activ' + this.gks_thiseventid;
//                  obj_calendar.getEventById(rrrrrrr).remove();
//    				    }
//    				    var rrr =data.event;
//    				    setTimeout(function() {
//                    myAddEvent(rrr);
//                }, 500);
//              }
          
  					} else {
  					  this.myrevertFunc();
  						myalert('error:' + $.base64.decode(data.message));
  					}
  				}
  			}
  		});      
    },
    eventClick: function(eventClickInfo) { //calEvent, jsEvent, view
      //console.log(eventClickInfo);
      gks_event_edit(parseInt(eventClickInfo.event.extendedProps.rec_id), (eventClickInfo.event.start), (eventClickInfo.event.end), eventClickInfo.event,eventClickInfo);

    },     
//    loading: function(myrunning) { 
//      if (myrunning) $('#calc_hourglass').show();
//      else $('#calc_hourglass').hide();
//    },
    
  });
  
  obj_calendar.render();

  

  
   
  
  function myAddEvent(myEvent)
  {
    obj_calendar.addEvent(myEvent);
    //obj_calendar.getEventById(myid).remove();
    //$('#calendar_div').fullCalendar( 'renderEvent' ,myEvent);
  }    
  

  $('#c_cal_small').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,inline:true,
    onChangeDateTime:function(dp,myinput){
      tempval=$('#c_cal_small').datetimepicker('getValue');
      obj_calendar.gotoDate(dp);
      
    },  
  }));
  
  $('#c_start').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1}));
  $('#c_end').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1}));
  

  $('#c_color').spectrum({
    type: "component",
    locale:'el',
    togglePaletteOnly: true,
    hideAfterPaletteSelect: true,
    showInput: true,
    showInitial: true,
    allowEmpty:true,
    //preferredFormat:'hex',
    chooseText: 'OK',
    cancelText: gks_lang('Άκυρο'),
    togglePaletteMoreText: gks_lang('Περισσότερα'),
    togglePaletteLessText: gks_lang('Παλέτα'),
    clearText : gks_lang('Καθαρισμός'),
    noColorSelectedText: gks_lang('Διάφανο'),
  });
  
  var cal_user_color_settings = {
    type: "color",
    locale:'el',
    togglePaletteOnly: true,
    hideAfterPaletteSelect: true,
    showInput: true,
    showInitial: true,
    allowEmpty:true,
    //preferredFormat:'hex',
    chooseText: 'OK',
    cancelText: gks_lang('Άκυρο'),
    togglePaletteMoreText: gks_lang('Περισσότερα'),
    togglePaletteLessText: gks_lang('Παλέτα'),
    clearText : gks_lang('Καθαρισμός'),
    noColorSelectedText: gks_lang('Διάφανο'),
  };
  
  $('.cal_user_color').spectrum(cal_user_color_settings);
  $('.cal_user_color_task').spectrum(cal_user_color_settings);
  $('.cal_user_color_activ').spectrum(cal_user_color_settings);


	var gks_scrollTop=0;
  var gks_event_edit_is_open=false;
  var gks_event_edit_id=-1;
  var cal_user_color_array=[];
  
  function gks_event_edit(event_id, start, end, myevent, obj_info) {
    if (myevent!= null && myevent.extendedProps !== undefined && myevent.extendedProps.c_table=='gks_crm_tasks') {
      if (obj_info.jsEvent.ctrlKey) {
        window.open('/my/admin-crm-task-item.php?id=' + event_id, '_blank');
      } else {
        window.location.href = '/my/admin-crm-task-item.php?id=' + event_id;
      }
      return;
    }
    if (myevent!= null && myevent.extendedProps !== undefined && myevent.extendedProps.c_table=='gks_crm_activity') {
      if (obj_info.jsEvent.ctrlKey) {
        window.open('/my/admin-crm-activity.php?id=' + event_id, '_blank');
      } else {
        //window.location.href = '/my/admin-crm-activity.php?id=' + event_id;
        activity_add_click(event_id);
        
      }
      return;
    }    
    gks_event_edit_is_open=true;
    
    c_allday=false;
    if (moment(start).format('HH:mm:ss') =='00:00:00' && moment(end).format('HH:mm:ss') =='00:00:00') c_allday=true;
    gks_scrollTop = $(window).scrollTop();
    gks_event_edit_id=event_id;
    
    cal_user_color_array=[];
    $('.cal_user_color').each(function() {
      cal_user_color_array.push({user_id: parseInt($(this).attr('data-id')),color:$(this).val()});
    });

    $('#c_participant_me_div').find('.c_participant_name').val('').attr('data-user_id','0');
    $('#c_participant_me_div').find('.c_participant_is_org').attr('class', 'fas fa-user     c_participant_is_org c_participant_is_org0');
    $('#c_participant_me_div').find('.c_participant_is_opt').attr('class', 'fas fa-user c_participant_is_opt c_participant_is_opt0');
    $('#c_participant_me_div').find('.c_participant_r_type').attr('class', 'far fa-circle          c_participant_r_type c_participant_r_type0');

    
    
    if (event_id==-1) {
      $('#c_title').val('');
      $('#c_message').val('');
      $('#delete_calendar').prop('disabled',true);
      
      
      if (c_allday) {
        if ($('#c_allday').is(':checked') == false) $('#c_allday').click();
      } else {
        if ($('#c_allday').is(':checked') == true)  $('#c_allday').click();
      }
      $('#c_odos').val('');
      $('#c_arithmos').val('');
      $('#c_orofos').val('');
      $('#c_perioxi').val('');
      $('#c_poli').val('');
      $('#c_tk').val('');
      $('#c_country_id').val(91);
      nomos_fill('c_nomos_id',91,0);
      $('#c_map_latitude').val(0);
      $('#c_map_longitude').val(0);
      myLatLng = {lat:0, lng:0};
      $('#c_is_exclusive1').prop('checked',true);
      $('#c_is_private0').prop('checked',true);
      $('#c_color').spectrum('set','#000000');
      $('#set_def_color').css('background-color','#000000').attr('data-mycolor','#000000');
      for(var list_i9=0; list_i9 < cal_user_color_array.length; list_i9++) {
        if (cal_user_color_array[list_i9].user_id==0) {
          
          $('#c_color').spectrum('set',cal_user_color_array[list_i9].color);
          $('#set_def_color').css('background-color',cal_user_color_array[list_i9].color).attr('data-mycolor',cal_user_color_array[list_i9].color);
          break;
        }
      }
            
      $('#div_object_rel').html('');
      $('#div_c_objects').html('');
      
      
      $('#c_event_id').html('');
      $('#c_event_user_id_add').html('');
      $('#c_event_mydate_add').html('');
      $('#c_event_user_id_edit').html('');
      $('#c_event_mydate_edit').html('');
      $('#c_event_myip').html('');
      
      $('#c_user_id0').prop('checked',true);
      $('#div_c_user_id_other').hide();
      $('#c_user_id_other').val('').attr('data-user_id',0);
      
      mydata1={type: 'email', number: 30, unit: 'minute'};
      mydata2={type: 'notif', number: 10, unit: 'minute'};
      c_notification='';
      c_notification+=c_notification_item(1,mydata1);
      c_notification+=c_notification_item(2,mydata2);
      
      mydata1={user_id: from_php_my_wp_user_id, name: from_php_gks_nickname, email: '', mobile: '', is_org:1, is_opt:0, r_type:'yes', r_date:''};
      c_participant='';
      c_participant+=c_participant_item(1,mydata1);
      
      
    } else {
      $('#c_title').val(myevent.title);
      $('#c_message').val(myevent.extendedProps.c_message);
      if (from_php_perm_ret_delete) $('#delete_calendar').prop('disabled',false);
      if (myevent.allDay) {
        if ($('#c_allday').is(':checked') == false) $('#c_allday').click();
      } else {
        if ($('#c_allday').is(':checked') == true)  $('#c_allday').click();
      }
      $('#c_odos').val(myevent.extendedProps.c_odos);
      $('#c_arithmos').val(myevent.extendedProps.c_arithmos);
      $('#c_orofos').val(myevent.extendedProps.c_orofos);
      $('#c_perioxi').val(myevent.extendedProps.c_perioxi);
      $('#c_poli').val(myevent.extendedProps.c_poli);
      $('#c_tk').val(myevent.extendedProps.c_tk);
      $('#c_country_id').val(myevent.extendedProps.c_country_id);
      nomos_fill('c_nomos_id',myevent.extendedProps.c_country_id,myevent.extendedProps.c_nomos_id);
      $('#c_map_latitude').val(myevent.extendedProps.c_map_latitude);
      $('#c_map_longitude').val(myevent.extendedProps.c_map_longitude);
      
      myLatLng = {lat:myevent.extendedProps.c_map_latitude, lng:myevent.extendedProps.c_map_longitude};
      
      if (map_is_visible && myLatLng.lat!=0 && myLatLng.lng!=0) {
        map.setCenter(myLatLng);
        marker.position=myLatLng;
        map.setZoom(17);     
      }
      $('#c_is_exclusive' + myevent.extendedProps.c_is_exclusive).prop('checked', true);
      $('#c_is_private' + myevent.extendedProps.c_is_private).prop('checked', true);
      

      for(var list_i10=0; list_i10 < cal_user_color_array.length; list_i10++) {
        if ((cal_user_color_array[list_i10].user_id==0 && myevent.extendedProps.c_user_id == from_php_my_wp_user_id) || 
            (cal_user_color_array[list_i10].user_id == myevent.extendedProps.c_user_id)) {
          $('#c_color').spectrum('set',cal_user_color_array[list_i10].color);
          $('#set_def_color').css('background-color',cal_user_color_array[list_i10].color).attr('data-mycolor',cal_user_color_array[list_i10].color);
          break;
        }
      }
      if (myevent.extendedProps.c_custom_color == 1) {
        $('#c_color').spectrum('set',myevent.extendedProps.c_color);
      } 

      c_objects='';
      for(var list_i11=0; list_i11 < myevent.extendedProps.c_objects.length; list_i11++) {
        c_objects+=myevent.extendedProps.c_objects[list_i11].obj_name;
        if (myevent.extendedProps.c_objects[list_i11].contact_id>0) {
          c_objects+=', <a href="admin-users-item.php?id=' + myevent.extendedProps.c_objects[list_i11].contact_id + '">' + myevent.extendedProps.c_objects[list_i11].contact_name + '</a>';
        } else if (myevent.extendedProps.c_objects[list_i11].contact_name!='') {
          c_objects+=', ' + myevent.extendedProps.c_objects[list_i11].contact_name;
        }
        if (myevent.extendedProps.c_objects[list_i11].esoda!='') {
          c_objects+=', ' + myevent.extendedProps.c_objects[list_i11].esoda;
        }
         
      }
      

      
      
      $('#div_object_rel').html(myevent.extendedProps.object_rel);
      $('#div_c_objects').html(c_objects);
      
      from_php_id=event_id;
      $('#dialog_object_rel_add').click(dialog_object_rel_add_click);
      $('.unlink_object_rel').click(unlink_object_rel_click);

      
      $('#c_event_id').html(event_id);
      $('#c_event_user_id_add').html(myevent.extendedProps.c_event_user_id_add);
      $('#c_event_mydate_add').html(myevent.extendedProps.c_event_mydate_add);
      $('#c_event_user_id_edit').html(myevent.extendedProps.c_event_user_id_edit);
      $('#c_event_mydate_edit').html(myevent.extendedProps.c_event_mydate_edit);
      $('#c_event_myip').html(myevent.extendedProps.c_event_myip);

      if (from_php_my_wp_user_id == myevent.extendedProps.c_user_id) {
        $('#c_user_id0').prop('checked',true);
        $('#div_c_user_id_other').hide();
        $('#c_user_id_other').val(myevent.extendedProps.c_gks_nickname).attr('data-user_id',from_php_my_wp_user_id);
      } else {
        $('#c_user_id1').prop('checked',true);
        $('#div_c_user_id_other').show();
        $('#c_user_id_other').val(myevent.extendedProps.c_gks_nickname).attr('data-user_id',myevent.extendedProps.c_user_id);
      
      }

      c_notification='';
      for(var list_i12=0; list_i12<myevent.extendedProps.c_notification.length; list_i12++) {
      	c_notification+=c_notification_item(list_i12 + 1,myevent.extendedProps.c_notification[list_i12]);
      }
      
      c_participant='';
      for(var list_i13=0; list_i13<myevent.extendedProps.c_participant.length; list_i13++) {
      	c_participant+=c_participant_item(list_i13 + 1,myevent.extendedProps.c_participant[list_i13]);
      }

    }
    
    $('#c_start').datetimepicker({'value':start});
    c_end_value=end;
    if (c_allday) {
      c_end_value=moment(c_end_value).subtract(24,'hours').toDate();
    }
    
    $('#c_end').datetimepicker({'value':c_end_value});
    set_c_start_format();
    
    $('#c_notification').html(c_notification);
    $('.c_notification_delete').click(c_notification_delete_click);
    $('.c_notification_add').click(c_notification_add_click);
    c_notification_add_hide();
    
    $('#c_participant').html(c_participant);
    $('#c_participant').find('.c_participant_delete').click(c_participant_delete_click);
    $('#c_participant').find('.c_participant_add').click(c_participant_add_click);
    
    $('#c_participant').find('.c_participant_is_org').click(c_participant_is_org_click);
    $('#c_participant').find('.c_participant_is_opt').click(c_participant_is_opt_click);
    $('#c_participant').find('.c_participant_r_type').click(c_participant_r_type_click);

    if (from_php_perm_ret_edit) $('#c_participant').find('.c_participant_name').autocomplete(participant_user_settings);
        
    c_participant_add_hide();
    
    secureid= $('#c_participant_me_div').find('.c_participant_name').attr('data-user_id');
    if (isNaN(secureid)) secureid=0;
    if (secureid==0) {
      val=parseInt($('input[name=c_user_id]:checked').val());
      if (isNaN(val)) val=0;
      if (val==0) {
        $('#c_participant_me_div').find('.c_participant_name').attr('data-user_id',from_php_my_wp_user_id).val(from_php_gks_nickname);
      } else {
        $('#c_participant_me_div').find('.c_participant_name').attr('data-user_id',$('#c_user_id_other').attr('data-user_id')).val($('#c_user_id_other').val());
      }
    }
    
    
    gks_resize_textarea($('#c_message')); 
    
    $('#gks_main_session1').hide();  
    $('#gks_main_session2').hide();  
    $('#gks_nav_session_header').hide();
    $('#gks_nav_session_footer').hide();
    $('#dialog_event_logs').show();
    //$('#dialog_event').show('blind',{},500,function() { $('#gks_rsrv_f').show(); gks_myscroll(); });
    $('#dialog_event').show(); $('#gks_rsrv_f').show(); gks_myscroll();
    
    
    
    $('#calc_refetch').hide();
    $('#c_title').focus();
    
    
  }

  function dialog_event_close() {
    $('#gks_rsrv_f').hide();
    $('#dialog_event').hide();
    $('#dialog_event_logs').hide();
    $('#gks_nav_session_header').show();
    
    //$('#gks_main_session1').show('blind',{},500);  
    //$('#gks_main_session2').show('blind',{},500,function() { $('#gks_nav_session_footer').show(); obj_calendar.setOption('height','auto'); });  
    $('#gks_main_session1').show();  
    $('#gks_main_session2').show(); $('#gks_nav_session_footer').show(); obj_calendar.setOption('height','auto');
    
    $('#calc_refetch').show();
    
    $("html").scrollTop(gks_scrollTop);
    gks_event_edit_is_open=false;
  }
  $('#dialog_event_cancel').click(function() {
    need_save=false;
    dialog_event_close();
  });
    

  $('input[name=c_user_id]').change( function() {
    val=parseInt($('input[name=c_user_id]:checked').val());
    if (isNaN(val)) val=0;
    if (val==0) {
      $('#div_c_user_id_other').hide();
      for(var list_i14=0; list_i14 < cal_user_color_array.length; list_i14++) {
        if (cal_user_color_array[list_i14].user_id==0) {
          $('#c_color').spectrum('set',cal_user_color_array[list_i14].color);
          $('#set_def_color').css('background-color',cal_user_color_array[list_i14].color).attr('data-mycolor',cal_user_color_array[list_i14].color);
          break;
        }
      }
      $('#c_participant_me_div').find('.c_participant_name').attr('data-user_id',from_php_my_wp_user_id).val(from_php_gks_nickname);
    } else {
      $('#div_c_user_id_other').show();
      data_user_id=parseInt($('#c_user_id_other').attr('data-user_id'));
      if (isNaN(data_user_id)) data_user_id=0;
      for(var list_i15=0; list_i15 < cal_user_color_array.length; list_i15++) {
        if (cal_user_color_array[list_i15].user_id==data_user_id) {
          $('#c_color').spectrum('set',cal_user_color_array[list_i15].color);
          $('#set_def_color').css('background-color',cal_user_color_array[list_i15].color).attr('data-mycolor',cal_user_color_array[list_i15].color);
          break;
        }
      }
      $('#c_participant_me_div').find('.c_participant_name').attr('data-user_id',$('#c_user_id_other').attr('data-user_id')).val($('#c_user_id_other').val());
    }
    need_save=true;
    gks_myscroll(); 
  });
  
  $('#c_user_id_other').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        other_calendar: 1
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
      $('#c_user_id_other').attr('data-user_id',ui.item.id);
      data_user_id=parseInt(ui.item.id);
      if (isNaN(data_user_id)) data_user_id=0;
      for(var list_i16=0; list_i16 < cal_user_color_array.length; list_i16++) {
        if (cal_user_color_array[list_i16].user_id==data_user_id) {
          $('#c_color').spectrum('set',cal_user_color_array[list_i16].color);
          $('#set_def_color').css('background-color',cal_user_color_array[list_i16].color).attr('data-mycolor',cal_user_color_array[list_i16].color);
          break;
        }
      }
      setTimeout(function() {
        $('#c_participant_me_div').find('.c_participant_name').attr('data-user_id',$('#c_user_id_other').attr('data-user_id')).val($('#c_user_id_other').val());
      }, 300);
      
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $('#c_user_id_other').val('').attr('data-user_id','0');
        for(var list_i17=0; list_i17 < cal_user_color_array.length; list_i17++) {
          if (cal_user_color_array[list_i17].user_id==0) {
            
            $('#c_color').spectrum('set',cal_user_color_array[list_i17].color);
            $('#set_def_color').css('background-color',cal_user_color_array[list_i17].color).attr('data-mycolor',cal_user_color_array[list_i17].color);
            break;
          }
        }
        $('#c_participant_me_div').find('.c_participant_name').attr('data-user_id','0').val('');
        
      }
    }
  }); 
  
  participant_user_settings = {
    source: function(request, response) {
      mydata={
        term: request.term,
        all: 1
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
      $(this).attr('data-user_id',ui.item.id);
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $(this).val('').attr('data-user_id','0');
      }
    }
  };
   
  

  
  $('#c_allday').change(function() {
    myneedsave=true;
    set_c_start_format();
  });

  function set_c_start_format() {
    if ($('#c_allday').is(':checked')) {
      tempval=$('#c_start').datetimepicker('getValue');
      $('#c_start').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{'mask':'39/19/9999',format:'d/m/Y','timepicker':false}));
      $('#c_start').datetimepicker({'value':tempval});
      
      tempval=$('#c_end').datetimepicker('getValue');
      $('#c_end').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{'mask':'39/19/9999',format:'d/m/Y','timepicker':false}));
      $('#c_end').datetimepicker({'value':tempval});
    } else {
      tempval=$('#c_start').datetimepicker('getValue');
      $('#c_start').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{'mask':'39/19/9999 29:59',format:'d/m/Y H:i','timepicker':true}));
      $('#c_start').datetimepicker({'value':tempval});

      tempval=$('#c_end').datetimepicker('getValue');
      $('#c_end').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{'mask':'39/19/9999 29:59',format:'d/m/Y H:i','timepicker':true}));
      $('#c_end').datetimepicker({'value':tempval});
    }
    
    
    
  }


  function c_notification_item(aa,mydata) {

  	a='';
    a+='<div class="form-group row">';
    a+='  <div class="col-md-12 c_notification_item" data-aa="' + aa + '">';
    a+='		<select class="form-control form-control-sm myneedsave c_notification_type" data-aa="' + aa + '">';
    a+='			<option value="email" ' + (mydata.type=='email' ? 'selected' : '') + '>'+gks_lang('email')+'</option>';
    a+='			<option value="notif" ' + (mydata.type=='notif' ? 'selected' : '') + '>'+gks_lang('Ειδοποίηση')+'</option>';
    a+='		</select>';
    a+='		<input type="number" class="form-control form-control-sm myneedsave c_notification_number" data-aa="' + aa + '" value="' + mydata.number + '">';
    a+='		<select class="form-control form-control-sm myneedsave c_notification_unit" data-aa="' + aa + '">';
    a+='			<option value="minute" ' + (mydata.unit=='minute' ? 'selected' : '') + '>'+gks_lang('Λεπτά')+'</option>';
    a+='			<option value="hour"   ' + (mydata.unit=='hour'   ? 'selected' : '') + '>'+gks_lang('Ώρες')+'</option>';
    a+='			<option value="day"    ' + (mydata.unit=='day'    ? 'selected' : '') + '>'+gks_lang('Ημέρες')+'</option>';
    a+='			<option value="week"   ' + (mydata.unit=='week'   ? 'selected' : '') + '>'+gks_lang('Εβδομάδες')+'</option>';
    a+='		</select>';
    
    a+='		<i class="fas fa-trash-alt c_notification_delete" data-aa="' + aa + '"></i>';
    a+='		<i class="fas fa-plus-circle c_notification_add" data-aa="' + aa + '"></i>';
    a+='	</div>';
    a+='</div>';
    return a;
  }
  
  function c_participant_item(aa,mydata) {
    //console.log(mydata);
    if (mydata.is_org ==0) class1='fas fa-user     c_participant_is_org c_participant_is_org0';
    else                   class1='fas fa-user-cog c_participant_is_org c_participant_is_org1';
    
    if (mydata.is_opt==0) class2='fas fa-user c_participant_is_opt c_participant_is_opt0';
    else                  class2='far fa-user c_participant_is_opt c_participant_is_opt1';
     
    if (mydata.r_type=='')          class3='far fa-circle          c_participant_r_type c_participant_r_type0';
    else if (mydata.r_type=='yes')  class3='fas fa-check-circle    c_participant_r_type c_participant_r_typeyes';
    else if (mydata.r_type=='no')   class3='fas fa-times-circle    c_participant_r_type c_participant_r_typeno ';
    else if (mydata.r_type=='isos') class3='fas fa-question-circle c_participant_r_type c_participant_r_typeisos ';
    
    user_id_form=parseInt($('input[name=c_user_id]:checked').val());
    if (isNaN(user_id_form)) user_id_form=0;
    
    if (user_id_form !=0) {
      user_id_form=parseInt($('#mypostform #c_user_id_other').attr('data-user_id'));
      if (isNaN(user_id_form)) user_id_form=0;
    } else {
      user_id_form = from_php_my_wp_user_id;
    }
    
        
    
    if (mydata.user_id == user_id_form) {
      $('#c_participant_me_div').find('.c_participant_name').val(mydata.name).attr('data-user_id',mydata.user_id);
      $('#c_participant_me_div').find('.c_participant_is_org').attr('class', class1);
      $('#c_participant_me_div').find('.c_participant_is_opt').attr('class', class2);
      $('#c_participant_me_div').find('.c_participant_r_type').attr('class', class3);
      return '';
    } else {
      
    
    	a='';
      a+='<div class="form-group row">';
      a+='  <div class="col-md-12 c_participant_item" data-aa="' + aa + '">';
      a+='		<input type="text" class="form-control form-control-sm myneedsave c_participant_name" data-aa="' + aa + '" value="' + mydata.name + '" data-user_id="' + mydata.user_id + '">';
      a+='		<i class="' + class1 + '" data-aa="' + aa + '"></i>';
      a+='		<i class="' + class2 + '" data-aa="' + aa + '"></i>';
      a+='		<i class="' + class3 + '" data-aa="' + aa + '"></i>';
      a+='		<i class="fas fa-trash-alt c_participant_delete" data-aa="' + aa + '"></i>';
      a+='		<i class="fas fa-plus-circle c_participant_add" data-aa="' + aa + '"></i>';
      a+='	</div>';
      a+='</div>';
      return a;
    }
  }
  
  
  function c_notification_delete_click() {
  	aa=parseInt($(this).attr('data-aa'));
  	if (isNaN(aa)) aa=0;
  	if (aa<=0) return;
  	$('.c_notification_item[data-aa=' + aa + ']').parent().remove();
  	c_notification_add_hide();
  }

  function c_participant_delete_click() {
  	aa=parseInt($(this).attr('data-aa'));
  	if (isNaN(aa)) aa=0;
  	if (aa<=0) return;
  	$('.c_participant_item[data-aa=' + aa + ']').parent().remove();
  	c_participant_add_hide();
  }

  function c_notification_add_click() {
  	aa=parseInt($('.c_notification_item:last').attr('data-aa'));
  	if (isNaN(aa)) aa=0;
  	if (aa<=0) return;
  	aa++;
  	
    mydata={type: 'notif', number: 10, unit: 'minute'};
    c_notification=c_notification_item(aa,mydata);
  	$('.c_notification_item:last').parent().after(c_notification);
  	
    $('.c_notification_item:last').find('.c_notification_delete').click(c_notification_delete_click);
    $('.c_notification_item:last').find('.c_notification_add').click(c_notification_add_click);
  	
  	c_notification_add_hide();
  }
  
  function c_participant_is_org_click() {
    if (from_php_perm_ret_edit==false) return;
    if ($(this).hasClass('c_participant_is_org0')) {
      $(this).removeClass('c_participant_is_org0').addClass('c_participant_is_org1').removeClass('fa-user').addClass('fa-user-cog');
    } else {
      $(this).removeClass('c_participant_is_org1').addClass('c_participant_is_org0').removeClass('fa-user-cog').addClass('fa-user');
    }
  }
  
  function c_participant_is_opt_click() {
    if (from_php_perm_ret_edit==false) return;
    if ($(this).hasClass('c_participant_is_opt0')) {
      $(this).removeClass('c_participant_is_opt0').addClass('c_participant_is_opt1').removeClass('fas').addClass('far');
    } else {
      $(this).removeClass('c_participant_is_opt1').addClass('c_participant_is_opt0').removeClass('far').addClass('fas');
    }
  }
  
  function c_participant_r_type_click() {
    if (from_php_perm_ret_edit==false) return;
    if ($(this).hasClass('c_participant_r_type0')) {
      $(this).removeClass('c_participant_r_type0').addClass('c_participant_r_typeyes').removeClass('far').removeClass('fa-circle').addClass('fas').addClass('fa-check-circle');
    } else if ($(this).hasClass('c_participant_r_typeyes')) {
      $(this).removeClass('c_participant_r_typeyes').addClass('c_participant_r_typeno').removeClass('fa-check-circle').addClass('fa-times-circle');
    
    } else if ($(this).hasClass('c_participant_r_typeno')) {
      $(this).removeClass('c_participant_r_typeno').addClass('c_participant_r_typeisos').removeClass('fa-times-circle').addClass('fa-question-circle');
    
    } else if ($(this).hasClass('c_participant_r_typeisos')) {
      $(this).removeClass('c_participant_r_typeisos').addClass('c_participant_r_type0').removeClass('fa-question-circle').addClass('far').removeClass('fas').addClass('fa-circle');
    
    }
    
  }
  
  
  function c_participant_add_click() {
    if (from_php_perm_ret_edit==false) return;
  	aa=parseInt($('.c_participant_item:last').attr('data-aa'));
  	if (isNaN(aa)) aa=0;
  	if (aa<=0) return;
  	aa++;
  	
    mydata={user_id: 0, name: '', email: '', mobile: '', is_org:0, is_opt:0, r_type:'', r_date:''};
    c_participant=c_participant_item(aa,mydata);
  	$('.c_participant_item:last').parent().after(c_participant);
  	
    $('.c_participant_item:last').find('.c_participant_delete').click(c_participant_delete_click);
    $('.c_participant_item:last').find('.c_participant_add').click(c_participant_add_click);
    
    $('.c_participant_item:last').find('.c_participant_is_org').click(c_participant_is_org_click);
    $('.c_participant_item:last').find('.c_participant_is_opt').click(c_participant_is_opt_click);
    $('.c_participant_item:last').find('.c_participant_r_type').click(c_participant_r_type_click);
  	
    $('.c_participant_item:last').find('.c_participant_name').autocomplete(participant_user_settings);
  	
  	
  	c_participant_add_hide();
  }
  
  function c_notification_add_hide() {
  	$('.c_notification_add').hide();
  	$('.c_notification_add:last').show();
  	if ($('.c_notification_add').length==0) {
  		$('#c_notification_add_alone_div').show();
  	} else {
  		$('#c_notification_add_alone_div').hide();
  	}
  	if (from_php_perm_ret_edit==false) {
  	  $('.c_notification_add').hide();
  	  $('#c_notification_add_alone_div').hide();
  	  $('.c_notification_delete').hide();
  	}
  	
  }
  
  function c_participant_add_hide() {
  	$('.c_participant_add').hide();
  	$('.c_participant_add:last').show();
  	if ($('.c_participant_add').length==0) {
  		$('#c_participant_add_alone_div').show();
  		$('#c_participant_me_div').hide();
  		$('#c_participant_text_div').hide();
  	} else {
  		$('#c_participant_add_alone_div').hide();
  		$('#c_participant_me_div').show();
  		$('#c_participant_text_div').show();
  		
  	}
  	if (from_php_perm_ret_edit==false) {
  	  $('.c_participant_add').hide();
  	  $('#c_participant_add_alone_div').hide();
  	  $('.c_participant_delete').hide();
  	  $('.c_participant_name').prop('disabled', true);
  	  
  	}
  }
  
  
  $('#c_notification_add_alone').click(function() {
    mydata={type: 'notif', number: 10, unit: 'minute'};
    c_notification=c_notification_item(1,mydata);
    $('#c_notification').html(c_notification);
    $('.c_notification_delete').click(c_notification_delete_click);
    $('.c_notification_add').click(c_notification_add_click);
    c_notification_add_hide();
  });
  
  $('#c_participant_add_alone').click(function() {
    mydata={user_id: 0, name: '', email: '', mobile: '', is_org:0, is_opt:0, r_type:'', r_date:''};
    c_participant=c_participant_item(1,mydata);
    $('#c_participant').html(c_participant);
    $('.c_participant_delete').click(c_participant_delete_click);
    $('.c_participant_add').click(c_participant_add_click);
    

    $('.c_participant_item:last').find('.c_participant_is_org').click(c_participant_is_org_click);
    $('.c_participant_item:last').find('.c_participant_is_opt').click(c_participant_is_opt_click);
    $('.c_participant_item:last').find('.c_participant_r_type').click(c_participant_r_type_click);

    $('.c_participant_item:last').find('.c_participant_name').autocomplete(participant_user_settings);
    
    c_participant_add_hide();
  });

  $('#c_participant_me_div').find('.c_participant_is_org').click(c_participant_is_org_click);
  $('#c_participant_me_div').find('.c_participant_is_opt').click(c_participant_is_opt_click);
  $('#c_participant_me_div').find('.c_participant_r_type').click(c_participant_r_type_click);
  
    
  $('#showmap').click(function(event) {  
    initMap();
      
    if (myLatLng.lat==0 && myLatLng.lng==0) {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          myLatLng = {lat: position.coords.latitude,lng: position.coords.longitude};
          //infoWindow_userpos.setContent(gks_lang('Η τοποθεσία σας έχει εντοπιστεί'));
          //infoWindow_userpos.open(map, marker);
          
          marker.position=myLatLng;
          map.setOptions({center: myLatLng});
          map.setOptions({zoom: 17});
                
        }, function() {
          //handleLocationError(true, infoWindow_userpos, map.getCenter());
        });
      } else {
        // Browser doesn't support Geolocation
        //handleLocationError(false, infoWindow_userpos, map.getCenter());
      }
      
    } else {
      marker.position=myLatLng;
      map.setOptions({center: myLatLng});
      map.setOptions({zoom: 17});
    }
    
    if ($('#showmap').html() ==gks_lang('Απόκρυψη χάρτη')) {
      $('#map_pos, #geocode_pos').prop('disabled',true);
      $('#showmap').html(gks_lang('Εμφάνιση χάρτη'));
      $('#map').parent().hide();
      map_is_visible=false;
    } else {
      $('#map_pos, #geocode_pos').prop('disabled',false);
      $('#showmap').html(gks_lang('Απόκρυψη χάρτη'));
      $('#map').parent().show();
      map_is_visible=true;
    }
    
    gks_myscroll();
  
  });

  


 
  $('#map_pos').click(function(event){
    if (infoWindow_userpos==null) infoWindow_userpos = new google.maps.InfoWindow({map: map});
      
    
    
    // Try HTML5 geolocation.
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var myLatLng = {lat: position.coords.latitude,lng: position.coords.longitude};
        
        infoWindow_userpos.setContent(gks_lang('Η τοποθεσία σας έχει εντοπιστεί'));
        infoWindow_userpos.open(map, marker);
        
        map.setCenter(myLatLng);
        marker.position=myLatLng;
        map.setZoom(17);
      
        
         
        $('#c_map_latitude').val(myLatLng.lat);
        $('#c_map_longitude').val(myLatLng.lng);
        need_save=true;
        
      }, function() {
        handleLocationError(true, infoWindow_userpos, map.getCenter());
      });
    } else {
      // Browser doesn't support Geolocation
      handleLocationError(false, infoWindow_userpos, map.getCenter());
    }
        
  });

  $('#geocode_pos').tooltipster();
  $('#geocode_pos').click(function() {
    
    datasend='';
    datasend+='&odos='  + encodeURIComponent($.base64.encode($("#c_odos").val().trim()));
    datasend+='&arithmos='  + encodeURIComponent($.base64.encode($("#c_arithmos").val().trim()));
    datasend+='&orofos='  + encodeURIComponent($.base64.encode($("#c_orofos").val().trim()));
    datasend+='&perioxi='  + encodeURIComponent($.base64.encode($("#c_perioxi").val().trim()));
    datasend+='&poli='  + encodeURIComponent($.base64.encode($("#c_poli").val().trim()));
    datasend+='&tk='  + encodeURIComponent($.base64.encode($("#c_tk").val().trim()));
    datasend+='&country_id='  + encodeURIComponent($("#c_country_id").val().trim());
    datasend+='&nomos_id='  + encodeURIComponent($("#c_nomos_id").val().trim());
    
    $('#geocode_pos').prop('disabled',true);
    $('#geocode_pos_icon').html('<i class="fas fa-hourglass"></i>');
    //console.log(datasend);
    $.ajax({
			url: '/my/admin-get-geocode_pos.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#geocode_pos').prop('disabled',false);
			  $('#geocode_pos_icon').html('<i class="fas fa-exclamation-circle"></i>').parent().tooltipster('destroy').attr('title',gks_lang('Σφάλμα')+': ' + jqXHR.responseText).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
			},				
			success: function(data) {
			  $('#geocode_pos').prop('disabled',false);
				if (!data) {
				  $('#geocode_pos_icon').html('<i class="fas fa-exclamation-circle"></i>').parent().tooltipster('destroy').attr('title',gks_lang('Σφάλμα')+': '+gks_lang('Παρακαλώ δοκιμάστε αργότερα')).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
					  $('#c_map_latitude' ).val(data.pos.lat);
					  $('#c_map_longitude').val(data.pos.lng);

            var pos = {lat: data.pos.lat,lng: data.pos.lng};      
            marker.position=pos;
            map.setOptions({center: pos});
            map.setOptions({zoom: 17});
            					  
					  $('#geocode_pos_icon').html('<i class="fas fa-check-circle"></i>').parent().tooltipster('destroy').attr('title','GEO:' + data.pos.lat + ',' + data.pos.lng).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
					} else {
					  $('#geocode_pos_icon').html('<i class="fas fa-exclamation-circle"></i>').parent().tooltipster('destroy').attr('title',gks_lang('Σφάλμα')+': ' + $.base64.decode(data.message)).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
					}
				}
			}
			
		});
  });
  
  function mysubmit() {
    
    datasend='cmd=edit';
    datasend+='&id=' + gks_event_edit_id;
    
    val=parseInt($('input[name=c_user_id]:checked').val());
    if (isNaN(val)) val=0;
    datasend+='&user_is_other=' + val;
    if (val!=0) {
      val=parseInt($('#mypostform #c_user_id_other').attr('data-user_id'));
      if (isNaN(val)) val=0;
      if (val==0) {
      	myalert('error:'+gks_lang('Εφόσον επιλέξατε ότι το συμβάν είναι άλλου χρήστη, θα πρέπει να επιλέξετε και τον χρήστη'));
      	return;        
      }
    }
    datasend+='&c_user_id_other=' + $('#mypostform #c_user_id_other').attr('data-user_id');
    datasend+='&start=' + moment($('#c_start').datetimepicker('getValue')).format("YYYY-MM-DD HH:mm:ss");
    datasend+='&end=' +  moment($('#c_end').datetimepicker('getValue')).format("YYYY-MM-DD HH:mm:ss");
    datasend+='&allday=' + encodeURIComponent(($('#mypostform #c_allday').is(':checked') ? '1' : '0'));
    if ($("#mypostform #c_title").val().trim()=='') {
    	myalert('error:'+gks_lang('Εισάγετε το Θέμα'));
    	return;
    }
    datasend+='&c_title='  + encodeURIComponent($.base64.encode($("#mypostform #c_title").val().trim()));
    datasend+='&c_message='  + encodeURIComponent($.base64.encode($("#mypostform #c_message").val().trim()));
    datasend+='&c_is_exclusive=' + encodeURIComponent(($('#mypostform #c_is_exclusive0').is(':checked') ? '0' : '1'));
    datasend+='&c_is_private=' + encodeURIComponent(($('#mypostform #c_is_private0').is(':checked') ? '0' : '1'));
    if ($("#mypostform #c_color").val() == $('#set_def_color').attr('data-mycolor')) { //is default color
      datasend+='&c_color=';
    } else {
      datasend+='&c_color='  + encodeURIComponent($.base64.encode($("#mypostform #c_color").val().trim()));
    }
    datasend+='&c_odos='  + encodeURIComponent($.base64.encode($("#mypostform #c_odos").val().trim()));
    datasend+='&c_arithmos='  + encodeURIComponent($.base64.encode($("#mypostform #c_arithmos").val().trim()));
    datasend+='&c_orofos='  + encodeURIComponent($.base64.encode($("#mypostform #c_orofos").val().trim()));
    datasend+='&c_perioxi='  + encodeURIComponent($.base64.encode($("#mypostform #c_perioxi").val().trim()));
    datasend+='&c_poli='  + encodeURIComponent($.base64.encode($("#mypostform #c_poli").val().trim()));
    datasend+='&c_tk='  + encodeURIComponent($.base64.encode($("#mypostform #c_tk").val().trim()));
    datasend+='&c_country_id='  + encodeURIComponent($("#mypostform #c_country_id").val().trim());
    datasend+='&c_nomos_id='  + encodeURIComponent($("#mypostform #c_nomos_id").val().trim());
    datasend+='&c_map_latitude='  + encodeURIComponent($("#mypostform #c_map_latitude").val().trim());
    datasend+='&c_map_longitude='  + encodeURIComponent($("#mypostform #c_map_longitude").val().trim());


		var c_notification=[];
		$('.c_notification_item').each(function() {
	  	aa=parseInt($(this).attr('data-aa'));
	  	if (isNaN(aa)) aa=0;
	  	if (aa>=1) {
	  		c_notification.push({
	  			type  : $('.c_notification_type[data-aa=' + aa + ']').val(),
	  			number: $('.c_notification_number[data-aa=' + aa + ']').val(),
	  			unit: $('.c_notification_unit[data-aa=' + aa + ']').val(),
	  		});
	  	}
		});
		datasend+='&c_notification=' + encodeURIComponent($.base64.encode(JSON.stringify(c_notification)));
		
		var c_participant=[];
		$('.c_participant_item').each(function() {
	  	aa=parseInt($(this).attr('data-aa'));
	  	if (isNaN(aa)) aa=-1;
	  	if (aa>=0) {
	  	  r_type='';
	  	  if ($('.c_participant_r_type[data-aa=' + aa + ']').hasClass('c_participant_r_type0')) r_type='';
	  	  else if ($('.c_participant_r_type[data-aa=' + aa + ']').hasClass('c_participant_r_typeyes')) r_type='yes'; 
	  	  else if ($('.c_participant_r_type[data-aa=' + aa + ']').hasClass('c_participant_r_typeno')) r_type='no'; 
	  	  else if ($('.c_participant_r_type[data-aa=' + aa + ']').hasClass('c_participant_r_typeisos')) r_type='isos'; 
	  	  
	  		c_participant.push({
	  			user_id  : $('.c_participant_name[data-aa=' + aa + ']').attr('data-user_id'),
	  			is_org: ($('.c_participant_is_org[data-aa=' + aa + ']').hasClass('c_participant_is_org1') ? '1' : '0'),
	  			is_opt: ($('.c_participant_is_opt[data-aa=' + aa + ']').hasClass('c_participant_is_opt1') ? '1' : '0'),
	  			r_type: r_type,
	  		});
	  	}
		});
		datasend+='&c_participant=' + encodeURIComponent($.base64.encode(JSON.stringify(c_participant)));
    //console.log(c_participant);
    

    $('#calc_hourglass').show();
    $.ajax({
			url: '/my/admin-crm-calendar-item-exec.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			thiseventid: gks_event_edit_id,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#calc_hourglass').hide();
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				$('#calc_hourglass').hide();
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
            need_save=false;
				    if (this.thiseventid>0) {
				      var rrrrrrr= 'cal' + this.thiseventid;
				      //setTimeout(function() {
                  
              obj_calendar.getEventById(rrrrrrr).remove();
              
              //}, 100)
				    }
				    var rrr =data.event;
				    setTimeout(function() {
                myAddEvent(rrr);
            }, 500);
            
            dialog_event_close();
            
            //if (data.redirect=='') {
  					//  window.location.reload();
  					//} else {
  					//  window.location.href = $.base64.decode(data.redirect);
  					//}
  					
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     

    return false;
  }

  $('#c_country_id').change(function() {
    var v=$(this).val();
    nomos_fill('c_nomos_id',v,0);
  }); 

  $('#cal_submenu_static').click(function() {
    $('#cal_submenu_static').hide();
    $('#main_panel_left').hide();
    $('#cal_submenu_fixed').show();
    $('#main_panel_right').css('width','100%');  
    obj_calendar.setOption('height','auto');
    if (from_hash_analyze) return;
    
    datasend='&o=' + encodeURIComponent($.base64.encode('calendar'));
    datasend+='&s=' + encodeURIComponent($.base64.encode('leftpanel'));
    datasend+='&v=' + encodeURIComponent($.base64.encode('0'));
    $.ajax({
			url: '/my/admin-users-settings-item-exec.php',type: 'POST',cache: false,dataType: 'json',	data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {console.log(jqXHR.responseText);},				
			success: function(data) {if (!data) {console.log('error:'+gks_lang('Παρακαλώ δοκιμάστε αργότερα'));} 
			  else {if (data.success == false) {console.log('error:' + $.base64.decode(data.message));}}
			},
		}); 
		set_hash();   
  });
  $('#cal_submenu_fixed').click(function() {
    $('#cal_submenu_fixed').hide();
    $('#cal_submenu_static').show();
    $('#main_panel_left').show();
    $('#main_panel_right').css('width','');  
    obj_calendar.setOption('height','auto');
    if (from_hash_analyze) return;
    
    datasend='&o=' + encodeURIComponent($.base64.encode('calendar'));
    datasend+='&s=' + encodeURIComponent($.base64.encode('leftpanel'));
    datasend+='&v=' + encodeURIComponent($.base64.encode('1'));
    $.ajax({
			url: '/my/admin-users-settings-item-exec.php',type: 'POST',cache: false,dataType: 'json',	data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {console.log(jqXHR.responseText);},				
			success: function(data) {if (!data) {console.log('error:'+gks_lang('Παρακαλώ δοκιμάστε αργότερα'));} 
			  else {if (data.success == false) {console.log('error:' + $.base64.decode(data.message));}}
			},
		});
		set_hash();    
  });
  
  
  
  
  $('#cal_user_add').click(function() {
    if ($('#cal_user_add_user').css('display')=='none') {
      $('#cal_user_add_user').css('display','inline-block').focus();
      
    } else {
      $('#cal_user_add_user').css('display','none');
    }
  });
  $('#cal_user_add_task').click(function() {
    if ($('#cal_user_add_user_task').css('display')=='none') {
      $('#cal_user_add_user_task').css('display','inline-block').focus();
      
    } else {
      $('#cal_user_add_user_task').css('display','none');
    }
  });
  $('#cal_user_add_activ').click(function() {
    if ($('#cal_user_add_user_activ').css('display')=='none') {
      $('#cal_user_add_user_activ').css('display','inline-block').focus();
      
    } else {
      $('#cal_user_add_user_activ').css('display','none');
    }
  });
    
  $('#cal_user_add_user').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        eml:1,
        notme:1,
        notids: function() {
          var existusers=[];
          $('input[name=cal_user]').each(function() {
            uid=parseInt($(this).attr('data-id'));
            if (isNaN(uid)) uid=-1;
            if (uid==0) uid=from_php_my_wp_user_id;
            existusers.push(uid);
          });
          return encodeURIComponent($.base64.encode(JSON.stringify(existusers)))
        },
        test:1,
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
      $('#calc_hourglass').show();
      datasend='cmd=add&myobj=cal&other_user_id=' + ui.item.id;
      $.ajax({
  			url: '/my/admin-crm-calendar-item-other-exec.php',
  			type: 'POST',
  			cache: false,
  			dataType: 'json',
  			data: datasend,			
  			error : function(jqXHR ,textStatus,  errorThrown) {
  			  $('#calc_hourglass').hide();
  				myalert('error:' + jqXHR.responseText);
  			},				
  			success: function(data) {
  				$('#calc_hourglass').hide();
  				if (!data) {
  					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  				} else {
  					if (data.success == true) {
    					myhtml ='<div class="cal_user_row" data-id="' + data.other_user_id + '">' +
                      '<input type="checkbox" name="cal_user" data-id="' + data.other_user_id + '" id="cal_user_' + data.other_user_id + '" checked>' +
                      //' <div class="cal_user_color_con"><div class="cal_user_color_wra" data-id="' + data.other_user_id + '" style="background-color: #3788d8;">' +
                      ' <input type="text" class="cal_user_color" data-id="' + data.other_user_id + '" value="#3788d8">' +
                      //'</div></div>' +
                      ' <label for="cal_user_' + data.other_user_id + '" class="cal_user_label">' + $.base64.decode(data.gks_nickname) + '</label>' +
                      ' <i class="fas fa-trash-alt cal_user_remove" data-aa="' + data.other_user_id + '"></i>' +
                      '</div>';
    					$('#div_cal_user_add').before(myhtml);
    					$('#cal_user_add_user').hide().val('');
    					$('.cal_user_remove[data-aa=' + data.other_user_id + ']').click(function() {cal_user_remove_click($(this),'cal');});
    					$('#cal_user_' + data.other_user_id).change(function() {cal_user_change($(this));});
    					$('.cal_user_color[data-id=' + data.other_user_id + ']').change(function() {cal_user_color($(this),'cal');});
    					$('.cal_user_color[data-id=' + data.other_user_id + ']').spectrum(cal_user_color_settings);
    					obj_calendar.refetchEvents();
    					set_hash();
  					} else {
  						myalert('error:' + $.base64.decode(data.message));
  					}
  				}
  			}
  		});
  		      
    },
    change: function (event, ui) {
      if(!ui.item){
        $('#cal_user_add_user').val('');
      }
    }
  });  
  
  $('#cal_user_add_user_task').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        eml:1,
        notme:1,
        notids: function() {
          var existusers=[];
          $('input[name=cal_user_task]').each(function() {
            uid=parseInt($(this).attr('data-id'));
            if (isNaN(uid)) uid=-1;
            if (uid==0) uid=from_php_my_wp_user_id;
            existusers.push(uid);
          });
          return encodeURIComponent($.base64.encode(JSON.stringify(existusers)))
        },
        test:1,
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
      $('#calc_hourglass').show();
      datasend='cmd=add&myobj=task&other_user_id=' + ui.item.id;
      $.ajax({
  			url: '/my/admin-crm-calendar-item-other-exec.php',
  			type: 'POST',
  			cache: false,
  			dataType: 'json',
  			data: datasend,			
  			error : function(jqXHR ,textStatus,  errorThrown) {
  			  $('#calc_hourglass').hide();
  				myalert('error:' + jqXHR.responseText);
  			},				
  			success: function(data) {
  				$('#calc_hourglass').hide();
  				if (!data) {
  					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  				} else {
  					if (data.success == true) {
    					myhtml ='<div class="cal_user_row_task" data-id="' + data.other_user_id + '">' +
                      '<input type="checkbox" name="cal_user_task" data-id="' + data.other_user_id + '" id="cal_user_task_' + data.other_user_id + '" checked>' +
                      ' <input type="text" class="cal_user_color_task" data-id="' + data.other_user_id + '" value="#3788d8">' +
                      ' <label for="cal_user_task_' + data.other_user_id + '" class="cal_user_label_task">' + $.base64.decode(data.gks_nickname) + '</label>' +
                      ' <i class="fas fa-trash-alt cal_user_remove_task" data-aa="' + data.other_user_id + '"></i>' +
                      '</div>';
    					$('#div_cal_user_add_task').before(myhtml);
    					$('#cal_user_add_user_task').hide().val('');
    					$('.cal_user_remove_task[data-aa=' + data.other_user_id + ']').click(function() {cal_user_remove_click($(this),'task');});
    					$('#cal_user_task_' + data.other_user_id).change(function() {cal_user_task_change($(this));});
    					$('.cal_user_color_task[data-id=' + data.other_user_id + ']').change(function() {cal_user_color($(this),'task');});
    					$('.cal_user_color_task[data-id=' + data.other_user_id + ']').spectrum(cal_user_color_settings);
    					obj_calendar.refetchEvents();
    					set_hash();
  					} else {
  						myalert('error:' + $.base64.decode(data.message));
  					}
  				}
  			}
  		});
  		      
    },
    change: function (event, ui) {
      if(!ui.item){
        $('#cal_user_add_user_task').val('');
      }
    }
  });
  
  $('#cal_user_add_user_activ').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        eml:1,
        notme:1,
        notids: function() {
          var existusers=[];
          $('input[name=cal_user_activ]').each(function() {
            uid=parseInt($(this).attr('data-id'));
            if (isNaN(uid)) uid=-1;
            if (uid==0) uid=from_php_my_wp_user_id;
            existusers.push(uid);
          });
          return encodeURIComponent($.base64.encode(JSON.stringify(existusers)))
        },
        test:1,
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
      $('#calc_hourglass').show();
      datasend='cmd=add&myobj=activ&other_user_id=' + ui.item.id;
      $.ajax({
  			url: '/my/admin-crm-calendar-item-other-exec.php',
  			type: 'POST',
  			cache: false,
  			dataType: 'json',
  			data: datasend,			
  			error : function(jqXHR ,textStatus,  errorThrown) {
  			  $('#calc_hourglass').hide();
  				myalert('error:' + jqXHR.responseText);
  			},				
  			success: function(data) {
  				$('#calc_hourglass').hide();
  				if (!data) {
  					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  				} else {
  					if (data.success == true) {
    					myhtml ='<div class="cal_user_row_activ" data-id="' + data.other_user_id + '">' +
                      '<input type="checkbox" name="cal_user_activ" data-id="' + data.other_user_id + '" id="cal_user_activ_' + data.other_user_id + '" checked>' +
                      ' <input type="text" class="cal_user_color_activ" data-id="' + data.other_user_id + '" value="#3788d8">' +
                      ' <label for="cal_user_activ_' + data.other_user_id + '" class="cal_user_label_activ">' + $.base64.decode(data.gks_nickname) + '</label>' +
                      ' <i class="fas fa-trash-alt cal_user_remove_activ" data-aa="' + data.other_user_id + '"></i>' +
                      '</div>';
    					$('#div_cal_user_add_activ').before(myhtml);
    					$('#cal_user_add_user_activ').hide().val('');
    					$('.cal_user_remove_activ[data-aa=' + data.other_user_id + ']').click(function() {cal_user_remove_click($(this),'activ');});
    					$('#cal_user_activ_' + data.other_user_id).change(function() {cal_user_activ_change($(this));});
    					$('.cal_user_color_activ[data-id=' + data.other_user_id + ']').change(function() {cal_user_color($(this),'activ');});
    					$('.cal_user_color_activ[data-id=' + data.other_user_id + ']').spectrum(cal_user_color_settings);
    					obj_calendar.refetchEvents();
    					set_hash();
  					} else {
  						myalert('error:' + $.base64.decode(data.message));
  					}
  				}
  			}
  		});
  		      
    },
    change: function (event, ui) {
      if(!ui.item){
        $('#cal_user_add_user_activ').val('');
      }
    }
  });  
  
  var calendar_remove_other_user=0;  
  var cal_user_remove_click_myobj='';
  function cal_user_remove_click(myelem, myobj) {
    cal_user_remove_click_myobj=myobj;
    val=parseInt(myelem.attr('data-aa'));
    if (isNaN(val)) val=0;
    if (val<=0) return;
    calendar_remove_other_user=val;
    myconfirm(gks_lang('Σίγουρα θέλετε να αφαιρέσετε το συγκεκριμένο ημερολόγιο;'),'calendar_remove_other_user');
    
  }
  
  $('.cal_user_remove').click(function() {cal_user_remove_click($(this),'cal');});
  $('.cal_user_remove_task').click(function() {cal_user_remove_click($(this),'task');});
  $('.cal_user_remove_activ').click(function() {cal_user_remove_click($(this),'activ');});
  
  
  
  window.calendar_remove_other_user = function() {
    $('#calc_hourglass').show();
    datasend='cmd=remove&myobj=' + cal_user_remove_click_myobj + '&other_user_id=' + calendar_remove_other_user;
    $.ajax({
			url: '/my/admin-crm-calendar-item-other-exec.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_myobj:cal_user_remove_click_myobj,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#calc_hourglass').hide();
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				$('#calc_hourglass').hide();
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
					  c_table='gks_calendar';extra='';
					  if (this.gks_myobj=='task') {c_table='gks_crm_tasks'; extra='_task';}
					  if (this.gks_myobj=='activ') {c_table='gks_crm_activity'; extra='_activ';}
					  
  					$('.cal_user_row' + extra + '[data-id=' + data.other_user_id + ']').remove();
  					
  					if (extra=='_task') {
              var cal_user_array_task=[];
              $('input[name=cal_user_task]:checked').each(function() {
                uid=parseInt($(this).attr('data-id'));
                if (isNaN(uid)) uid=-1;
                if (uid==0) uid=from_php_my_wp_user_id;
                cal_user_array_task.push(uid);
              });
              //console.log('cal_user_array_task',cal_user_array_task);
            }
  					if (extra=='_activ') {
              var cal_user_array_activ=[];
              $('input[name=cal_user_activ]:checked').each(function() {
                uid=parseInt($(this).attr('data-id'));
                if (isNaN(uid)) uid=-1;
                if (uid==0) uid=from_php_my_wp_user_id;
                cal_user_array_activ.push(uid);
              });
              //console.log('cal_user_array_activ',cal_user_array_activ);
            }
  					
  					mylist=obj_calendar.getEvents();
  					
            for (var list_i18=0; list_i18<mylist.length; list_i18++) {
    					if (mylist[list_i18].extendedProps.c_table=='gks_calendar' && extra=='') {
                if (mylist[list_i18].extendedProps.c_user_id==data.other_user_id) {
                  obj_calendar.getEventById(mylist[list_i18].id).remove();
                }
              } else if (mylist[list_i18].extendedProps.c_table=='gks_crm_tasks' && extra=='_task') {
                if (mylist[list_i18].extendedProps.c_user_id_multi.includes(data.other_user_id)) {
                  
                  //console.log(mylist[list_i18].extendedProps.c_user_id_multi);
                  var filtered =mylist[list_i18].extendedProps.c_user_id_multi.filter(function(value, index, arr){ 
                      return value != data.other_user_id;
                  });
                  mylist[list_i18].setExtendedProp('c_user_id_multi', filtered );                  
                  //console.log(mylist[list_i18].extendedProps.c_user_id_multi);
                  
                  intersection = cal_user_array_task.filter(element => mylist[list_i18].extendedProps.c_user_id_multi.includes(element));
                  //console.log('intersection',intersection);
                  
                  if (intersection.length==0) {
                    obj_calendar.getEventById(mylist[list_i18].id).remove();
                  } 
                }
              } else if (mylist[list_i18].extendedProps.c_table=='gks_crm_activity' && extra=='_activ') {
                if (mylist[list_i18].extendedProps.c_user_id==data.other_user_id) {
                  obj_calendar.getEventById(mylist[list_i18].id).remove();
                }
              }
            }
  					set_hash();

					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		});
    
    return;
  }  
  
  function cal_user_change(checkelem) {
    var cal_user_array=[];
    $('input[name=cal_user]:checked').each(function() {
      uid=parseInt($(this).attr('data-id'));
      if (isNaN(uid)) uid=-1;
      if (uid==0) uid=from_php_my_wp_user_id;
      cal_user_array.push(uid);
    });
    mylist=obj_calendar.getEvents();
    for (var list_i3=0; list_i3<mylist.length; list_i3++) {
      if (mylist[list_i3].extendedProps.c_table=='gks_calendar') {
        if (cal_user_array.includes(mylist[list_i3].extendedProps.c_user_id)) {
          obj_calendar.getEventById(mylist[list_i3].id).setProp('display','auto');
        } else {
          obj_calendar.getEventById(mylist[list_i3].id).setProp('display','none');
        }
      }
    }
    if (checkelem.attr('id')=='cal_allusers_toggle') {
      myuser_id=-1;
    } else {
      myuser_id=parseInt(checkelem.attr('data-id'));if (isNaN(myuser_id)) myuser_id=-1;
    }
    update_cal_user_or_task_change(myuser_id,'cal',(checkelem.is(':checked')?1:0));
  }
  function cal_user_task_change(checkelem) {
    var cal_user_array_task=[];
    $('input[name=cal_user_task]:checked').each(function() {
      uid=parseInt($(this).attr('data-id'));
      if (isNaN(uid)) uid=-1;
      if (uid==0) uid=from_php_my_wp_user_id;
      cal_user_array_task.push(uid);
    });
    mylist=obj_calendar.getEvents();
    for (var list_i1=0; list_i1<mylist.length; list_i1++) {
      //console.log(list_i1);
      if (mylist[list_i1].extendedProps.c_table=='gks_crm_tasks') {
        found_user=false;
        for(uu=0; uu<mylist[list_i1].extendedProps.c_user_id_multi.length;uu++) {
          if (cal_user_array_task.includes(mylist[list_i1].extendedProps.c_user_id_multi[uu])) {
            found_user=true;break;
          }
        }
        if (found_user) {
          obj_calendar.getEventById(mylist[list_i1].id).setProp('display','auto');
        } else {
          obj_calendar.getEventById(mylist[list_i1].id).setProp('display','none');
        }
      }
    }
    if (checkelem.attr('id')=='cal_alltasks_toggle') {
      myuser_id=-1;
    } else {
      myuser_id=parseInt(checkelem.attr('data-id'));if (isNaN(myuser_id)) myuser_id=-1;
    }
    update_cal_user_or_task_change(myuser_id,'task',(checkelem.is(':checked')?1:0));
  }

  function cal_user_activ_change(checkelem) {
    var cal_user_array_activ=[];
    $('input[name=cal_user_activ]:checked').each(function() {
      uid=parseInt($(this).attr('data-id'));
      if (isNaN(uid)) uid=-1;
      if (uid==0) uid=from_php_my_wp_user_id;
      cal_user_array_activ.push(uid);
    });
    mylist=obj_calendar.getEvents();
    for (var list_i1=0; list_i1<mylist.length; list_i1++) {
      //console.log(list_i1);
      if (mylist[list_i1].extendedProps.c_table=='gks_crm_activity') {
        if (cal_user_array_activ.includes(mylist[list_i1].extendedProps.c_user_id)) {
          obj_calendar.getEventById(mylist[list_i1].id).setProp('display','auto');
        } else {
          obj_calendar.getEventById(mylist[list_i1].id).setProp('display','none');
        }
      }
    }
    if (checkelem.attr('id')=='cal_allactivs_toggle') {
      myuser_id=-1;
    } else {
      myuser_id=parseInt(checkelem.attr('data-id'));if (isNaN(myuser_id)) myuser_id=-1;
    }
    update_cal_user_or_task_change(myuser_id,'activ',(checkelem.is(':checked')?1:0));
  }
  
  
  function update_cal_user_or_task_change(myuser_id,myobj,is_visible) {
    if (myuser_id<-1) return;
    if (from_hash_analyze) return;
    $('#calc_hourglass').show();
    datasend='cmd=visible&myobj=' + myobj + '&other_user_id=' + myuser_id + '&visible=' + is_visible;
    //console.log(datasend);
    
    $.ajax({
			url: '/my/admin-crm-calendar-item-other-exec.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,	
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#calc_hourglass').hide();
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				$('#calc_hourglass').hide();
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
            //console.log('OK');
            set_hash();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		});
	}
  
  $('input[name=cal_user]').change(function() {cal_user_change($(this));});
  $('input[name=cal_user_task]').change(function() {cal_user_task_change($(this));});
  $('input[name=cal_user_activ]').change(function() {cal_user_activ_change($(this));});
  
  
  function cal_user_color(myelem,myobj) {
    myuser_id=parseInt(myelem.attr('data-id'));
    if (isNaN(myuser_id)) myuser_id=-1;
    if (myuser_id<0) return;
    //$('.cal_user_color_wra[data-id=' + myuser_id + ']').css('background-color',$(this).val());

    mycolor=myelem.val().trim();
    $('#calc_hourglass').show();
    datasend='cmd=color&myobj=' + myobj + '&other_user_id=' + myuser_id + '&color=' + encodeURIComponent($.base64.encode(mycolor));
    
    
    $.ajax({
			url: '/my/admin-crm-calendar-item-other-exec.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,	
			thismyuser_id:(myuser_id == 0 ? from_php_my_wp_user_id : myuser_id),
			thismycolor:mycolor,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#calc_hourglass').hide();
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				$('#calc_hourglass').hide();
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
            mylist=obj_calendar.getEvents();
            for (var list_i2=0; list_i2<mylist.length; list_i2++) {
              if (mylist[list_i2].extendedProps.c_user_id == this.thismyuser_id && mylist[list_i2].extendedProps.c_custom_color==0) {
                obj_calendar.getEventById(mylist[list_i2].id).setProp('backgroundColor',this.thismycolor);
              }
            }
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
		});
		    
  }
  
  $('.cal_user_color').change(function() {cal_user_color($(this),'cal');});
  $('.cal_user_color_task').change(function() {cal_user_color($(this),'task');});
  $('.cal_user_color_activ').change(function() {cal_user_color($(this),'activ');});
  
  
  $('#set_def_color').click(function() {
    $('#c_color').spectrum('set',$(this).attr('data-mycolor'));
  });
  
  $('#delete_calendar').click(function() {
    myconfirm(gks_lang('Σίγουρα θέλετε να διαγράψετε την εγγραφή;'),'mycalendardelete','',gks_event_edit_id);
  });
  
  window.mycalendardelete = function(myid) {
    //console.log(myid);
    datasend='mymodel=gks_calendar&myid=' + myid;
    //console.log(datasend);
    
    $('body').addClass("myloading");  
    $.ajax({
  		url: '/my/admin-deleterow.php',
  		type: 'POST',
  		cache: false,
  		dataType: 'json',
  		thismyid:myid,
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
  				  obj_calendar.getEventById('cal' + this.thismyid).remove();
            dialog_event_close();
  				} else {
  					myalert('error:' + $.base64.decode(data.message));
  				}
  			}
  		}
  	});
  
      
  }
    
  function c_message_change() {gks_resize_textarea($(this));}
  $('#c_message').on('change keyup paste', c_message_change);
  gks_resize_textarea($('#c_message')); 
  
  $('#calc_refetch').click(function() {
    ff=obj_calendar.getEvents();
    for(var list_i4=0; list_i4<ff.length; list_i4++) {
	    fff=obj_calendar.getEventById(ff[list_i4].id);
	    if (fff !=null) {
	      fff.remove();
	    }      
    }
    obj_calendar.refetchEvents();
  });
  
  myTimer = setInterval(function() {
    if (gks_event_edit_is_open) return;
    obj_calendar.refetchEvents();
  }, 5 * 60 * 1000);
  
  gks_address_autocomplete('c_odos','c_arithmos','c_orofos','c_perioxi','c_poli','c_tk','c_nomos_id','c_country_id','c_map_latitude','c_map_longitude',true);

  $('#c_map_latitude, #c_map_longitude').on(mychange,function() {
    lat=parseFloat($('#c_map_latitude').val());
    lng=parseFloat($('#c_map_longitude').val());
    gks_this_map_set_pos(lat,lng);
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
  } else if (window.location.href.includes('?id=')) {
		if (from_php_start_id>0) {
			newurl=window.location.href.replace('?id=' + from_php_start_id,'');
			window.history.pushState({}, window.document.title, newurl);
		}
  	
  	
  }
  

  

  $('.myneedsave').on('input keyup paste', function() {
    need_save=true; 
  });

  window.onbeforeunload = function() {

    if (need_save==false) return;
    return gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');
  };

  need_save=false;

  //hash
  $(window).hashchange( function(){
    if (hashchange == location.hash) return;
    currsearch=window.location.hash.replace('#', '');
    currsearch=decodeURI(currsearch);
    //console.log('window hashchange');
    //console.log(currsearch);
    
    hash_analyze(currsearch);

  });

  function hash_analyze(currsearch) {
    if (currsearch!='') {
      try {
        hashmydata = JSON.parse(currsearch);
  
      } catch(err) {
        //console.log('error ' + err);  
        return;
      }
    } 
    
    from_hash_analyze=true;
    //console.log(hashmydata);
    if (hashmydata.startdate !== undefined) {
      obj_calendar.gotoDate(hashmydata.startdate);
      ddd=new Date(hashmydata.startdate);
      sss=ddd.getDate()+'/'+(ddd.getMonth()+1)+'/'+ddd.getFullYear();
      $('#c_cal_small').datetimepicker('setOptions', {value: sss});
    }
    if (hashmydata.leftpanel !== undefined) {
      temp=($('#cal_submenu_static').css('display')=='none' ? 0 : 1);
      if (temp!=hashmydata.leftpanel) {
        if (hashmydata.leftpanel==0) $('#cal_submenu_static').click(); else $('#cal_submenu_fixed').click();
      }
    }
    if (hashmydata.full24 !== undefined) {
      temp=($('.fc-gks_full24-button span').hasClass('fa-compress') ? 1 : 0);
      if (temp!=hashmydata.full24) {
        $('.fc-gks_full24-button').click();
      }
    }
    if (hashmydata.view !== undefined) {
      if (obj_calendar.view.type!=hashmydata.view) {
        if (hashmydata.view=='dayGridMonth') $('.fc-dayGridMonth-button').click();
        else if (hashmydata.view=='timeGridWeek') $('.fc-timeGridWeek-button').click();
        else if (hashmydata.view=='timeGridDay') $('.fc-timeGridDay-button').click();
        else if (hashmydata.view=='listDay') $('.fc-listDay-button').click();
        else if (hashmydata.view=='listWeek') $('.fc-listWeek-button').click();
        else if (hashmydata.view=='listMonth') $('.fc-listMonth-button').click();
        else if (hashmydata.view=='listYear') $('.fc-listYear-button').click();
          
      }
    }
    
    if (hashmydata.cal_user !== undefined) {
      //console.log(hashmydata.cal_user); 
      for(var list_i5=0; list_i5< hashmydata.cal_user.length; list_i5++) {
        elem=$('#cal_user_'+hashmydata.cal_user[list_i5][0]);
        if (elem.length==1) {
          if (hashmydata.cal_user[list_i5][1]!==(elem.is(':checked') ? 1 : 0)) elem.click();
        } 
      }
    }
    if (hashmydata.cal_user_task !== undefined) {
      //console.log(hashmydata.cal_user_task); 
      for(var list_i6=0; list_i6< hashmydata.cal_user_task.length; list_i6++) {
        elem=$('#cal_user_task_'+hashmydata.cal_user_task[list_i6][0]);
        if (elem.length==1) {
          if (hashmydata.cal_user_task[list_i6][1]!==(elem.is(':checked') ? 1 : 0)) elem.click();
        } 
      }
    }
    if (hashmydata.cal_user_activ !== undefined) {
      //console.log(hashmydata.cal_user_activ); 
      for(var list_i6=0; list_i6< hashmydata.cal_user_activ.length; list_i6++) {
        elem=$('#cal_user_activ_'+hashmydata.cal_user_activ[list_i6][0]);
        if (elem.length==1) {
          if (hashmydata.cal_user_activ[list_i6][1]!==(elem.is(':checked') ? 1 : 0)) elem.click();
        } 
      }
    }
    
    from_hash_analyze=false;
    
    //console.log('hash_analyze');
    //console.log(hashmydata);
    
  }
  function set_hash() {
    //console.log('set_hash');
    if (obj_calendar === undefined) return; 
    if (gks_page_loading) return;
    if (from_hash_analyze) return;
    
    hashmydata={};
    hashmydata.startdate = obj_calendar.getDate(); //obj_calendar.gotoDate(dp);obj_calendar.gotoDate(dp);
    hashmydata.leftpanel=($("#cal_submenu_static").css('display')=='none' ? 0 : 1);
    hashmydata.full24= ($('.fc-gks_full24-button span').hasClass('fa-compress') ? 1 : 0);
    hashmydata.view= obj_calendar.view.type;
    
    var cal_user_array=[];
    $('input[name=cal_user]').each(function() {
      uid=parseInt($(this).attr('data-id'));
      if (isNaN(uid)) uid=-1;
      if ($(this).is(':checked')) vv=1; else vv=0;
      cal_user_array.push([uid,vv]);
    });
    hashmydata.cal_user=cal_user_array;
    
    var cal_user_array_task=[];
    $('input[name=cal_user_task]').each(function() {
      uid=parseInt($(this).attr('data-id'));
      if (isNaN(uid)) uid=-1;
      if ($(this).is(':checked')) vv=1; else vv=0;
      cal_user_array_task.push([uid,vv]);
    });
    hashmydata.cal_user_task=cal_user_array_task;

    var cal_user_array_activ=[];
    $('input[name=cal_user_activ]').each(function() {
      uid=parseInt($(this).attr('data-id'));
      if (isNaN(uid)) uid=-1;
      if ($(this).is(':checked')) vv=1; else vv=0;
      cal_user_array_activ.push([uid,vv]);
    });
    hashmydata.cal_user_activ=cal_user_array_activ;

    
    //console.log('set_hash');
    //console.log(hashmydata);

    document.location.hash = encodeURI(JSON.stringify(hashmydata));
    hashchange=document.location.hash;
  }

  currsearch=window.location.hash.replace('#', '');
  currsearch=decodeURI(currsearch);
  hash_analyze(currsearch);  
  
    
  $('#cal_allusers_toggle').click(function() {
    if ($(this).is(':checked')) {
      $('input[name="cal_user"]').prop('checked',true);
    } else {
      $('input[name="cal_user"]').prop('checked',false);
    }
    cal_user_change($('#cal_allusers_toggle'));
  });
  $('#cal_alltasks_toggle').click(function() {
    if ($(this).is(':checked')) {
      $('input[name="cal_user_task"]').prop('checked',true);
    } else {
      $('input[name="cal_user_task"]').prop('checked',false);
    }
    cal_user_task_change($('#cal_alltasks_toggle'));    
  });
  $('#cal_allactivs_toggle').click(function() {
    if ($(this).is(':checked')) {
      $('input[name="cal_user_activ"]').prop('checked',true);
    } else {
      $('input[name="cal_user_activ"]').prop('checked',false);
    }
    cal_user_activ_change($('#cal_allactivs_toggle'));    
  });  
});


var map;
var marker;
var myLatLng = {lat: 0,lng: 0};
var map_is_open=false;
var map_is_visible=false;
var infoWindow_userpos=null;
 
function initMap() {
  if (map_is_open) return;
  $('#map').parent().css('height','500px').css('margin-top','10px');
  map = new google.maps.Map(document.getElementById('map'), {
    center: myLatLng,
    zoom: 17,
    mapId: "gks1234567890",
  });
  marker = new google.maps.marker.AdvancedMarkerElement({
    position: myLatLng,
    map: map,
    title: gks_lang('Τοποθεσία'),
    gmpDraggable: true,
  });
    
  marker.addListener('drag', handleEvent_Marker);
  marker.addListener('dragend', handleEvent_Marker);
  map_is_open=true;  	 
}

function handleEvent_Marker(event) {
  document.getElementById('c_map_latitude').value = event.latLng.lat();
  document.getElementById('c_map_longitude').value = event.latLng.lng();
}
window.gks_this_map_set_pos = function(lat,lng) {
  place_map_latitude=lat;
  place_map_longitude=lng;
  
  myLatLng = {lat: lat, lng: lng};
  if (typeof marker != 'undefined') marker.position=myLatLng;
  if (typeof marker != 'undefined') map.setOptions({center: myLatLng});
  //map.setOptions({zoom: 17});
}

 
