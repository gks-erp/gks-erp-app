/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
var autocomplete_gks_disable='off';
var gks_card_expand_icon_count=0;

var debug_gks_map_load_date = new Date();
debug_gks_map_load_date=debug_gks_map_load_date.getMinutes()*60*1000 + debug_gks_map_load_date.getSeconds() * 1000 + debug_gks_map_load_date.getMilliseconds();
var gks_map_js_load_done=false;
var gks_address_autocomplete_to_load=[];

let newestRequestId = 0;



//if (window.SharedWorker) {
//  const worker = new SharedWorker('/my/js/_notification-worker.js?v=1');
//  
//  worker.port.onmessage = function(e) {
//    const data = e.data;
//    if (data.count > 0) {
//      console.log('updateNotificationBadge');
//      //updateNotificationBadge(data.count);
//    }
//  };
//  
//  worker.onerror = function(e) {
//    console.error('[SharedWorker] Error:', e.message, e.filename, e.lineno);
//  };
//
//  worker.port.start();
//  console.log('worker start');
//} else {
//  // Fallback for browsers without SharedWorker
//  console.log('startLegacyPolling');
//  //startLegacyPolling();
//}

function gks_lang(mykey,part='',item='') {
  if (mykey=='' || mykey==null) return '';
  if (part=='') {
    if (typeof(gks_lang_data_js)=='undefined') return mykey;
    if (typeof(gks_lang_data_js[mykey])=='undefined') {
      console.log('gks_lang',mykey);
      return mykey;
    }
    return gks_lang_data_js[mykey];
  }
  if (part=='part2') {
    if (typeof(gks_lang_data_js_part2)=='undefined') return mykey;
    if (typeof(gks_lang_data_js_part2[mykey])=='undefined') {
      console.log('gks_lang',mykey);
      return mykey;
    }
    return gks_lang_data_js_part2[mykey];
  }
  if (part=='part3') {
    if (typeof(gks_lang_data_js_part3)=='undefined') return mykey;
    if (typeof(gks_lang_data_js_part3[mykey])=='undefined') {
      console.log('gks_lang',mykey);
      return mykey;
    }
    return gks_lang_data_js_part3[mykey];
  }
  if (part=='part4') {
    if (typeof(gks_lang_data_js_part4)=='undefined') return mykey;
    if (typeof(gks_lang_data_js_part4[item])=='undefined') {
      console.log('gks_lang',mykey);
      return mykey;
    }
    if (typeof(gks_lang_data_js_part4[item][mykey])=='undefined') {
      console.log('gks_lang',mykey,item);
      return mykey;
    }
    return gks_lang_data_js_part4[item][mykey];
  }
  
  return mykey;
}
function gks_n_h(i) {
  if (i<=12) {
    return gks_lang(i+'η','part3');
  } else {
    return i+gks_lang('η','part3');
  }
}
function gks_n_ho(i) {
  if (i<=12) {
    return gks_lang(i+'ου','part3');
  } else {
    return i+gks_lang('ου','part3');
  }
}


function myisint(a) {
  if (typeof a === 'undefined' || a === null) return false;
  a=a.trim();
  if (a=='') return false;
  ia=parseInt(a);
  if (isNaN(ia)) return false;
  //if (ia <= 0) return false;
  return true;
} 
function myparseInt(a) {
  if (typeof a === 'undefined' || a === null) return 0;
  a=a.trim();
  if (a=='') return 0;
  ia=parseInt(a);
  if (isNaN(ia)) return 0;
  return ia;
}
function myparseFloat(a) {
  if (typeof a === 'undefined' || a === null) return 0;
  a=a.trim();
  if (a=='') return 0;
  ia=parseFloat(a);
  if (isNaN(ia)) return 0;
  return ia;
}
function nl2br (str, is_xhtml) {
    if (typeof str === 'undefined' || str === null) {
      return '';
    }
    breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}
function br2nl(str) {
    return str.replace(/<br\s*\/?>/mg,"\n");
}
function br2null(str) {
    return str.replace(/<br\s*\/?>/mg,"");
}
function escapeRegExp(str) {
  return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
}

String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.replace(new RegExp(escapeRegExp(search), 'g'), replacement);
};

function isEmail(email) {
  regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  return regex.test(email);
}
function isMobile(phone) {
  if (phone.length != 10) {
    return false;
  }
  regex = /^69+([01345789]{1})+([0-9]{7})+$/;
  return regex.test(phone);
}

function _renderExtendedProgress (data) {
            return this._formatBitrate(data.bitrate) + ' | ' +
                this._formatTime(
                    (data.total - data.loaded) * 8 / data.bitrate
                ) + ' | ' +
                this._formatPercentage(
                    data.loaded / data.total
                ) + ' | ' +
                this._formatFileSize(data.loaded) + ' / ' +
                this._formatFileSize(data.total);
};
function _formatBitrate (bits) {
            if (typeof bits !== 'number') {
                return '';
            }
            if (bits >= 1000000000) {
                return (bits / 1000000000).toFixed(2) + ' Gbit/s';
            }
            if (bits >= 1000000) {
                return (bits / 1000000).toFixed(2) + ' Mbit/s';
            }
            if (bits >= 1000) {
                return (bits / 1000).toFixed(2) + ' kbit/s';
            }
            return bits.toFixed(2) + ' bit/s';
        };
function _formatPercentage (floatValue) {
            return (floatValue * 100).toFixed(2) + ' %';
        };
                
function _formatFileSize (bytes) {
            if (typeof bytes !== 'number') {
                return '';
            }
            if (bytes >= 1000000000) {
                return (bytes / 1000000000).toFixed(2) + ' GB';
            }
            if (bytes >= 1000000) {
                return (bytes / 1000000).toFixed(2) + ' MB';
            }
            return (bytes / 1000).toFixed(2) + ' KB';
        };
function _formatTime (seconds) {
            date = new Date(seconds * 1000),
                days = Math.floor(seconds / 86400);
            days = days ? days + 'd ' : '';
            return days +
                ('0' + date.getUTCHours()).slice(-2) + ':' +
                ('0' + date.getUTCMinutes()).slice(-2) + ':' +
                ('0' + date.getUTCSeconds()).slice(-2);
        };    
        
//function selectrecsperpage(obj,newurl) {
//	//alert(newurl);
//	v = obj.value;
//	//alert(v);
//	window.location = 'set-recsperpage.php?num=' + v + '&url=' + newurl;
//}

function gks_formatMoney(n, c, d, t){
  
  c = isNaN(c = Math.abs(c)) ? 2 : c; 
  d = d == undefined ? "." : d; 
  t = t == undefined ? "," : t; 
  s = n < 0 ? "-" : ""; 
  iii = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c)));
  jjj = (jjj = iii.length) > 3 ? jjj % 3 : 0;
  return s + (jjj ? iii.substr(0, jjj) + t : "") + iii.substr(jjj).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - iii).toFixed(c).slice(2) : "");
}

Number.prototype.formatMoney = function(c, d, t){
    n = this;
    c = isNaN(c = Math.abs(c)) ? 2 : c; 
    d = d == undefined ? "." : d; 
    t = t == undefined ? "," : t; 
    s = n < 0 ? "-" : ""; 
    iii = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))); 
    jjj = (jjj = iii.length) > 3 ? jjj % 3 : 0;
   return s + (jjj ? iii.substr(0, jjj) + t : "") + iii.substr(jjj).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - iii).toFixed(c).slice(2) : "");
};

Number.prototype.mymoney = function(andsymbol=true) {
  myn = this; 
  myret= myn.formatMoney(from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, from_php_GKS_NUMBER_FORMAT_DECIMAL, from_php_GKS_NUMBER_FORMAT_THOUSAND);
  
  if (andsymbol) {
    if (from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW == 'after') 
      myret+=''+ from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL;
    else
      myret=from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL+''+myret;
  }
  
  return myret;
};

Number.prototype.myNumberFormatNo0Local = function(zero_to_empty=false) {
  myn = this; 
  ret= myn.formatMoney(10, from_php_GKS_NUMBER_FORMAT_DECIMAL, from_php_GKS_NUMBER_FORMAT_THOUSAND);
  for (i=1;i<=11;i++) {
    if (ret.endsWith(from_php_GKS_NUMBER_FORMAT_DECIMAL)) {
      ret=ret.substr(0, ret.length-1);
      break;
    } 
    if (ret.endsWith('0')) ret=ret.substr(0,ret.length-1);
    else break;
  }
  if (zero_to_empty && ret=='0') ret='';
  return ret;  
}


Number.prototype.myround = function(scale) {
  num=this;
  if(!("" + num).includes("e")) {
    return +(Math.round(num + "e+" + scale)  + "e-" + scale);
  } else {
    arr = ("" + num).split("e");
    sig = ""
    if(+arr[1] + scale > 0) {
      sig = "+";
    }
    return +(Math.round(+arr[0] + "e" + sig + (+arr[1] + scale)) + "e-" + scale);
  }
};

function calc_age_getMonthLength(month,year,julianFlag) {
  ml=0;
  if(month==1 || month==3 || month==5 || month==7 || month==8 || month==10||month==12) {
    ml = 31;
  } else {
    if(month==2) {
      ml = 28;
      if (!(year%4) && (julianFlag==1 || year%100 || !(year%400))) ml++;
    } else {
      ml = 30;
    }
  }
  return ml;
}

function calc_age(myDateobj) {
  mynow = new Date();
  yd = mynow.getFullYear();
  md = mynow.getMonth() + 1;
  dd = mynow.getDate();

  yb = myDateobj.getFullYear();
  mb = myDateobj.getMonth() + 1;
  db = myDateobj.getDate();
  mLength = 0 ;

  // 0 if Gregorian, 1 is Julian
  isJulian =0;
  ma=0;
  ya=0;

  da = dd-db;
  // This is the all-important day borrowing code.
  if(da<0) {
    md--;
    // Borrow months from the year if necesssary.
    if(md<1) {
      yd--;
      // Determine no. of months in year
      if(mLength) {
        md=md+parseInt(365/mLength);
      } else {
        md=md+12;
      }
    }
    if(mLength==0) // Use real month length if no fixed
    {              // length is indicated - note that we add a leap day if necessary.
      ml=calc_age_getMonthLength(md,yd,isJulian);
      da=da+ml;
    } else {
      // For this case, everything works like it did in elementary school.
      da+=mLength;
    } // Use fixed month length
  }

  ma = md - mb;
  // Month borrowing code - borrows months from years.
  if(ma<0) {
    yd--;
    if(mLength!=0) {
      ma=ma+parseInt(365/mLength);
    } else {
      ma=ma+12;
    }
  }

  ya = yd - yb;
  ret = ya+' '+gks_lang('Ετών')+' '+ma+' '+gks_lang('Μηνών')+' '+da+' '+gks_lang('Ημερών');
  return ret;
}
function cleartonous(a) {
  a=a.toLowerCase();
  a=a.replace(/ά/g,'α');
  a=a.replace(/έ/g,'ε');
  a=a.replace(/ή/g,'η');
  a=a.replace(/ί/g,'ι');
  a=a.replace(/ώ/g,'ω');
  a=a.replace(/ύ/g,'υ');
  a=a.replace(/ό/g,'ο');
  a=a.replace(/ϊ/g,'ι');
  a=a.replace(/ΐ/g,'ι');
  a=a.replace(/ϋ/g,'υ');
  a=a.replace(/ΰ/g,'υ');
  a=a.replace(/ς/g,'σ');
 
  return a;
  
}
function greeklish(a) {
  a=a.toLowerCase();
  gr=['α', 'β', 'γ', 'δ', 'ε', 'ζ', 'η', 'θ',  'ι', 'κ', 'λ', 'μ', 'ν', 'ξ',  'ο', 'π', 'ρ', 'σ', 'τ', 'υ', 'φ', 'χ', 'ψ',  'ω', 'ς', 'ά', 'έ', 'ί', 'ό', 'ώ', 'ύ', 'ή', 'ϊ', 'ϋ', 'ΐ', 'ΰ'];
  en=['a', 'b', 'g', 'd', 'e', 'z', 'i', 'th', 'i', 'k', 'l', 'm', 'n', 'ks', 'o', 'p', 'r', 's', 't', 'y', 'f', 'x', 'ps', 'w', 's', 'a', 'e', 'i', 'o', 'o', 'y', 'i', 'i', 'i', 'i', 'i'];
  for (i=0; i<gr.length; i++) {
    a=a.replaceAll(gr[i],en[i]);
  }
  return a;
}
function greekkeybord(a) {
  a=a.toLowerCase();
  gr =['α', 'β', 'γ', 'δ', 'ε', 'ζ', 'η', 'θ', 'ι', 'κ', 'λ', 'μ', 'ν', 'ξ', 'ο', 'π', 'ρ', 'σ', 'τ', 'υ', 'φ', 'χ', 'ψ', 'ω', 'ς', 'ά', 'έ', 'ί', 'ό', 'ώ', 'ύ', 'ή', 'ϊ', 'ϋ', 'ΐ', 'ΰ'];
  en =['a', 'b', 'g', 'd', 'e', 'z', 'h', 'u', 'i', 'k', 'l', 'm', 'n', 'j', 'o', 'p', 'r', 's', 't', 'y', 'f', 'x', 'c', 'v', 'w', 'a', 'e', 'i', 'o', 'o', 'u', 'h', 'i', 'i', 'i', 'i'];
  for (i=0; i<gr.length; i++) {
    a=a.replaceAll(gr[i],en[i]);
  }
  return a;
  
}

(function() {
  /**
   * Decimal adjustment of a number.
   *
   * @param {String}  type  The type of adjustment.
   * @param {Number}  value The number.
   * @param {Integer} exp   The exponent (the 10 logarithm of the adjustment base).
   * @returns {Number} The adjusted value.
   */
  function decimalAdjust(type, value, exp) {
    // If the exp is undefined or zero...
    if (typeof exp === 'undefined' || +exp === 0) {
      return Math[type](value);
    }
    value = +value;
    exp = +exp;
    // If the value is not a number or the exp is not an integer...
    if (isNaN(value) || !(typeof exp === 'number' && exp % 1 === 0)) {
      return NaN;
    }
    // If the value is negative...
    if (value < 0) {
      return -decimalAdjust(type, -value, exp);
    }
    // Shift
    value = value.toString().split('e');
    value = Math[type](+(value[0] + 'e' + (value[1] ? (+value[1] - exp) : -exp)));
    // Shift back
    value = value.toString().split('e');
    return +(value[0] + 'e' + (value[1] ? (+value[1] + exp) : exp));
  }

  // Decimal round
  if (!Math.round10) {
    Math.round10 = function(value, exp) {
      return decimalAdjust('round', value, exp);
    };
  }
  // Decimal floor
  if (!Math.floor10) {
    Math.floor10 = function(value, exp) {
      return decimalAdjust('floor', value, exp);
    };
  }
  // Decimal ceil
  if (!Math.ceil10) {
    Math.ceil10 = function(value, exp) {
      return decimalAdjust('ceil', value, exp);
    };
  }
})();

function getWeekDayName(i) {
  //0 (for Sunday) through 6 (for Saturday)
  if (i>=7) i=i-7;
  if (i>=7) i=i-7;
  if (i>=7) i=i-7;
  
  out='';
  switch (i) {   
    case 0: out = gks_lang('Κυριακή','part3');break;  
    case 1: out = gks_lang('Δευτέρα','part3');break;  
    case 2: out = gks_lang('Τρίτη','part3');break;  
    case 3: out = gks_lang('Τετάρτη','part3');break;  
    case 4: out = gks_lang('Πέμπτη','part3');break;  
    case 5: out = gks_lang('Παρασκευή','part3');break;  
    case 6: out = gks_lang('Σάββατο','part3');break;  
  
  } 
  return out;
}

function getMonthName(i) {
  if (i>12) i=i-12;
  if (i>12) i=i-12;
  if (i>12) i=i-12;
  
  out='';
  switch (i) {   
    case 1: out = gks_lang('Ιανουάριος','part3');break;  
    case 2: out = gks_lang('Φεβρουάριος','part3');break;  
    case 3: out = gks_lang('Μάρτιος','part3');break;  
    case 4: out = gks_lang('Απρίλιος','part3');break;  
    case 5: out = gks_lang('Μάιος','part3');break;  
    case 6: out = gks_lang('Ιούνιος','part3');break;  
    case 7: out = gks_lang('Ιούλιος','part3');break;  
    case 8: out = gks_lang('Αύγουστος','part3');break;  
    case 9: out = gks_lang('Σεπτέμβριος','part3');break;  
    case 10: out = gks_lang('Οκτώβριος','part3');break;  
    case 11: out = gks_lang('Νοέμβριος','part3');break;  
    case 12: out = gks_lang('Δεκέμβριος','part3');break;  
  
  } 
  return out;
}

function pad(n, width, z) {
  z = z || '0';
  n = n + '';
  return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}

function escapeHtml(text) {
  map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };

  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}



function gks_pad(num, size) {
  s = num+"";
  while (s.length < size) s = "0" + s;
  return s;
}

function gks_get_utc(d) {
  if (d===null) return null;
  //return new Date(Date.UTC(d.getUTCFullYear(), d.getUTCMonth(), d.getUTCDate(),d.getUTCHours(), d.getUTCMinutes(), d.getUTCSeconds()));
  return new Date(d.getTime() + d.getTimezoneOffset() * 60000);
  
}


function gks_random_string(length, chars) {
  chars='0123456789abcdefghijklmnopqrstuvwxyz';
  var result = '';
  for (var i = length; i > 0; --i) result += chars[Math.floor(Math.random() * chars.length)];
  return result;
}

// Add an initial request body.
const gks_googlemaps_request = {
    input: '',
    //locationRestriction: { west: -122.44, north: 37.8, east: -122.39, south: 37.78 },
    //origin: { lat: 37.7893, lng: -122.4039 },
    //includedPrimaryTypes: ['restaurant'],
    language: 'el-GR',
    //region: 'us',
};

window.gks_map_js_load_initialize_default = function() {
  //console.log('Google Maps Version: ' + google.maps.version);
  //console.log('gks_map_js_load_initialize_default');
  //dddd = new Date();
  //dddd=dddd.getMinutes()*60*1000 + dddd.getSeconds() * 1000 + dddd.getMilliseconds();
  //console.log(dddd-debug_gks_map_load_date);

  gks_map_js_load_done=true;
  //console.log('gks_address_autocomplete_to_load');
  //console.log(gks_address_autocomplete_to_load);
  
  for (i = 0; i < gks_address_autocomplete_to_load.length; i++) {
    gks_address_autocomplete_item_map(gks_address_autocomplete_to_load[i]);
  }
  
  if (typeof gks_map_js_load_initialize_default_localjs === "function") { 
    gks_map_js_load_initialize_default_localjs();
  } else {
    //console.log('gks_map_js_load_initialize_default_localjs not found');
    
  }
    
  gks_googlemaps_refreshToken(gks_googlemaps_request);
}


function gks_address_autocomplete_item_map(item_to_load) {
  //console.log(item_to_load.runfrom);
  //console.log(item_to_load);
  //console.log('gks_address_autocomplete_item_map',document.readyState);
  if (document.readyState == "complete") {
    gks_window_load_autocomplete(item_to_load);
  } else {
    window.addEventListener('load', function() {
      gks_window_load_autocomplete(item_to_load);
    });    
  }
}  



function gks_window_load_autocomplete(item_to_load) {
  //console.log(item_to_load.ma_odos);
  mydiv1=document.createElement('div');
  mydiv1.setAttribute('id', item_to_load.ma_odos + '_gmac1');
  mydiv1.setAttribute('class', 'gks_google_autocomplete_div1');

  mydiv1.setAttribute('data-simple_input', (item_to_load.simple_input ? '1' : '0'));
  //mydiv1.setAttribute('data-simple_input', item_to_load.ma_odos ? '1' : '0');
  mydiv1.setAttribute('data-ma_odos', item_to_load.ma_odos);
  mydiv1.setAttribute('data-ma_arithmos', item_to_load.ma_arithmos);
  mydiv1.setAttribute('data-ma_orofos', item_to_load.ma_orofos);
  mydiv1.setAttribute('data-ma_perioxi', item_to_load.ma_perioxi);
  mydiv1.setAttribute('data-ma_poli', item_to_load.ma_poli);
  mydiv1.setAttribute('data-ma_tk', item_to_load.ma_tk);
  mydiv1.setAttribute('data-ma_country_id', item_to_load.ma_country_id);
  mydiv1.setAttribute('data-ma_nomos_id', item_to_load.ma_nomos_id);
  mydiv1.setAttribute('data-ma_latitude', item_to_load.ma_latitude);
  mydiv1.setAttribute('data-ma_longitude', item_to_load.ma_longitude);
  
  mydiv2=document.createElement('div');
  mydiv2.setAttribute('id', item_to_load.ma_odos + '_gmac2');
  mydiv2.setAttribute('class', 'gks_google_autocomplete_div2');
  mydiv1.appendChild(mydiv2);

  inputElement =document.getElementById(item_to_load.ma_odos);
  inputElement.addEventListener('input', gks_gmac_makeAutocompleteRequest);
  inputElement.after(mydiv1);
  inputElement.setAttribute('placeholder', gks_lang('Αναζήτηση'));
  return;
  
  /*
  //console.log('gks_window_load_autocomplete fire',item_to_load);
  var autocomplete_googlemaps = new google.maps.places.Autocomplete(document.getElementById(item_to_load.ma_odos));
  google.maps.event.addListener(autocomplete_googlemaps, 'place_changed', function () {
    var myplace = autocomplete_googlemaps.getPlace();
    //console.log(myplace);
    my_formatted_address=myplace.formatted_address;
    if (myplace.name !== undefined) my_formatted_address+='<br>'+gks_lang('Όνομα')+': ' + myplace.name;
    if (myplace.international_phone_number !== undefined) my_formatted_address+='<br>'+gks_lang('Τηλέφωνο')+': ' + myplace.international_phone_number;
    if (myplace.website !== undefined) my_formatted_address+='<br>'+gks_lang('Ιστότοπος')+': <a href="' + myplace.website + '" target="_blank">' + myplace.website + '</a>';
    if (myplace.url !== undefined) my_formatted_address+='<br>Google Map: <a href="' + myplace.url + '" target="_blank">' + myplace.url.substring(0, 30) + (myplace.url.length>30 ? '...' : '') + '</a>';
   
   

    $('#' + item_to_load.ma_odos + '_auto_googlemaps').html(my_formatted_address).show();
    
    if (item_to_load.simple_input) return;
    
    //gks 57013
    temp_odos='';
    temp_number='';
    temp_tk='';
    temp_premise='';
    temp_neighborhood='';
    temp_locality='';
    temp_sublevel_1='';
    temp_level_3='';
    temp_level_2='';
    temp_level_1='';
    temp_country='';
    temp_country_short_name='';
    
    for (pi=0; pi < myplace.address_components.length;pi++) {
      
      if (myplace.address_components[pi].types.length>0 && myplace.address_components[pi].types[0]=='street_number') {
        temp_number=myplace.address_components[pi].short_name.trim();
      }  
      if (myplace.address_components[pi].types.length>0 && myplace.address_components[pi].types[0]=='route') {
        temp_odos=myplace.address_components[pi].long_name.trim();
      }  
      if (myplace.address_components[pi].types.length>0 && myplace.address_components[pi].types[0]=='postal_code') {
        temp_tk=myplace.address_components[pi].short_name.replaceAll(' ','').trim();
      }  
      
      if (myplace.address_components[pi].types.length>0 && myplace.address_components[pi].types[0]=='premise') {
        temp_premise=myplace.address_components[pi].long_name.trim();
      }
      if (myplace.address_components[pi].types.length>0 && myplace.address_components[pi].types[0]=='neighborhood') {
        temp_neighborhood=myplace.address_components[pi].long_name.trim();
      }
      if (myplace.address_components[pi].types.length>0 && myplace.address_components[pi].types[0]=='locality') {
        temp_locality=myplace.address_components[pi].long_name.trim();
      }
      
      if (myplace.address_components[pi].types.length>0 && myplace.address_components[pi].types[0]=='sublocality_level_1') {
        temp_sublevel_1=myplace.address_components[pi].long_name.trim();
      }
      if (myplace.address_components[pi].types.length>0 && myplace.address_components[pi].types[0]=='administrative_area_level_3') {
        temp_level_3=myplace.address_components[pi].long_name.trim();
      }
      if (myplace.address_components[pi].types.length>0 && myplace.address_components[pi].types[0]=='administrative_area_level_2') {
        temp_level_2=myplace.address_components[pi].long_name.trim();
      }
      if (myplace.address_components[pi].types.length>0 && myplace.address_components[pi].types[0]=='administrative_area_level_1') {
        temp_level_1=myplace.address_components[pi].long_name.trim();
      }
      if (myplace.address_components[pi].types.length>0 && myplace.address_components[pi].types[0]=='country') {
        temp_country=myplace.address_components[pi].long_name.trim();
        temp_country_short_name=myplace.address_components[pi].short_name.trim();
        
      }
      
      
    }
    //console.log(temp_odos,'|',temp_number,'|',temp_premise,'|',temp_tk,'|',temp_neighborhood,'|',temp_locality,'|',temp_level_3,'|',temp_level_2,'|',temp_level_1,'|',temp_country);
    
    if (item_to_load.ma_odos!='') {
      temp_odos=temp_odos.trim();
      if (temp_odos=='') temp_odos=temp_premise;
      if (temp_odos=='' && myplace.formatted_address!='') {
        pp=myplace.formatted_address.split(',');
        temp_odos=pp[0].trim();
      }
      
      $('#' + item_to_load.ma_odos).val(temp_odos);
      $('#' + item_to_load.ma_arithmos).val(temp_number);
    }
    
    if (item_to_load.ma_perioxi!='') {
      if (temp_neighborhood!='') {
        $('#' + item_to_load.ma_perioxi).val(temp_neighborhood);
      } else {
        $('#' + item_to_load.ma_perioxi).val('');
      }
    }
    
    if (item_to_load.ma_poli!='') {
      if (temp_locality!='') {
        $('#' + item_to_load.ma_poli).val(temp_locality);
      } else if (temp_sublevel_1!='') {
        $('#' + item_to_load.ma_poli).val(temp_sublevel_1);
      } else {
        $('#' + item_to_load.ma_poli).val('');
      }
    }
    
    if (item_to_load.ma_tk!='') {
      $('#' + item_to_load.ma_tk).val(temp_tk);
      
    }
    
    var country_new_val=0;
    if (item_to_load.ma_country_id!='') { 
      if (temp_country_short_name!='') {
        $('#' + item_to_load.ma_country_id + ' option').each(function() {
          tval=$(this).attr('data-ci');
          if (temp_country_short_name == tval) {
            tval2=$(this).attr('value');
            if ($('#' + item_to_load.ma_country_id).val!=tval2) {
              $('#' + item_to_load.ma_country_id).val(tval2);
              country_new_val=parseInt(tval2);if (isNaN(country_new_val)) country_new_val=0;
              //console.log(country_new_val);
              search_obj={};
              if (temp_odos!='') search_obj.odos=temp_odos;
              if (temp_tk!='') search_obj.tk=temp_tk;
              if (temp_premise!='') search_obj.premise=temp_premise;
              if (temp_neighborhood!='') search_obj.neighborhood=temp_neighborhood;
              if (temp_locality!='') search_obj.locality=temp_locality;
              if (temp_sublevel_1!='') search_obj.sublevel_1=temp_sublevel_1;
              if (temp_level_1!='') search_obj.level_1=temp_level_1;
              if (temp_level_2!='') search_obj.level_2=temp_level_2;
              if (temp_level_3!='') search_obj.level_3=temp_level_3;

              search_string=JSON.stringify(search_obj);
              //console.log(search_string);
              nomos_fill(item_to_load.ma_nomos_id,country_new_val,0,search_string);
            }
          }
        });  
      }
    }
    

    if (item_to_load.ma_latitude!='' && item_to_load.ma_longitude!='') {
      $('#' + item_to_load.ma_latitude).val(myplace.geometry.location.lat());
      $('#' + item_to_load.ma_longitude).val(myplace.geometry.location.lng());
    }
    
    if (typeof gks_this_map_set_pos != 'undefined') {
      gks_this_map_set_pos(myplace.geometry.location.lat(),myplace.geometry.location.lng());
    }
  
  });    
  */    
  
}

async function gks_gmac_makeAutocompleteRequest(inputEvent) {
  // Reset elements and exit if an empty string is received.
  //console.log(inputEvent.target.id);
  
  document.getElementById(inputEvent.target.id + '_gmac1').style.display = 'block';
  
  
  resultsContainerElement = document.getElementById(inputEvent.target.id + '_gmac2');
  if (inputEvent.target.value == '' || inputEvent.target.value.length<3) {
      //titleElement.innerText = '';
      resultsContainerElement.replaceChildren();
      return;
  }
  // Add the latest char sequence to the request.
  gks_googlemaps_request.input = inputEvent.target.value;
  // To avoid race conditions, store the request ID and compare after the request.
  const requestId = ++newestRequestId;
  // Fetch autocomplete suggestions and show them in a list.
  // @ts-ignore
  const { suggestions } = await google.maps.places.AutocompleteSuggestion.fetchAutocompleteSuggestions(gks_googlemaps_request);
  // If the request has been superseded by a newer request, do not render the output.
  if (requestId !== newestRequestId)
      return;
  //titleElement.innerText = `Query predictions for "${request.input}"`;
  // Clear the list first.
  resultsContainerElement.replaceChildren();
  items_fount=0;
  hhhh=0;
  for (const suggestion of suggestions) {
    items_fount++;
    hhhh+=52;
    const placePrediction = suggestion.placePrediction;
    // Create a link for the place, add an event handler to fetch the place.
    mydivitem = document.createElement('div');
    mydivitem.setAttribute('class', 'gks_gmac_item');
    mydivitem.setAttribute('data-gmac-item', inputEvent.target.id);
    
    mydivitem.addEventListener('click', () => {
        gks_gmac_onPlaceSelected(placePrediction.toPlace(),inputEvent.target.id);
    });
    mmtext1='';
    if (placePrediction.mainText!=null) mmtext1=placePrediction.mainText.toString();
    mmtext2='';
    if (placePrediction.secondaryText!=null) mmtext2=placePrediction.secondaryText.toString();
    
    mydivitem.innerHTML = 
    '<div class="gks_gmac_item_icon"><i class="fas fa-map-marker-alt"></i></div>' +
    '<div class="gks_gmac_item_row">' + 
      '<div class="gks_gmac_item_title">' + mmtext1 + '</div>' +
      '<div class="gks_gmac_item_address">' + mmtext2 + '</div>' +
    '</div>';
    resultsContainerElement.appendChild(mydivitem);
  }
  
  if (items_fount==0) {
    hhhh+=52;
    mydivitem = document.createElement('div');
    mydivitem.setAttribute('class', 'gks_gmac_noresults');
    mydivitem.innerHTML =gks_lang('Δεν βρέθηκαν αποτελέσματα')+'<br>'+gks_lang('Δοκιμάστε μία άλλη αναζήτηση');
    resultsContainerElement.appendChild(mydivitem);
  }
  mydivimg = document.createElement('div');
  mydivimg.setAttribute('class', 'gks_gmac_img');
  mydivimg.innerHTML = '<span>powered by</span> <img src="/my/img/google_on_white.png"/>';
  hhhh+=30;
  
  $('#' + inputEvent.target.id + '_gmac2').css({'max-height':hhhh+'px'});
  
  resultsContainerElement.appendChild(mydivimg);
  
    
}

// Event handler for clicking on a suggested place.
async function gks_gmac_onPlaceSelected(myplace,target_id) {
  basediv=document.getElementById(target_id + '_gmac1'); 
  basediv.style.display = 'none';
  //console.log(target_id);
  
  await myplace.fetchFields({
      fields: ['displayName', 'formattedAddress','location','addressComponents','websiteURI','internationalPhoneNumber'],
  });
  
  gks_googlemaps_refreshToken(gks_googlemaps_request);
  
  myplaceid=myplace.id;

  data_transfer = basediv.getAttribute('data-transfer'); 
  if (data_transfer==null) data_transfer='0';
  
  data_admincrmmap = basediv.getAttribute('data-admincrmmap'); 
  if (data_admincrmmap==null) data_admincrmmap='0';
  
  simple_input = basediv.getAttribute('data-simple_input'); 
  if (simple_input==null) simple_input='1';

  
  my_formatted_address=myplace.formattedAddress;
  if (myplace.displayName !== undefined) my_formatted_address+='<br>'+gks_lang('Όνομα')+': ' + myplace.displayName;
  if (myplace.internationalPhoneNumber !== null) my_formatted_address+='<br>'+gks_lang('Τηλέφωνο')+': ' + myplace.internationalPhoneNumber;
  if (myplace.websiteURI !== null) my_formatted_address+='<br>'+gks_lang('Ιστότοπος')+': <a href="' + myplace.website + '" target="_blank">' + myplace.websiteURI + '</a>';
  my_formatted_address+='<br>Google Map: <a href="https://www.google.com/maps/place/?q=place_id:' + myplaceid + '" target="_blank">' + myplaceid + '</a>';
 
  if (data_admincrmmap=='1') { 
    $('#' + target_id + '_auto_googlemaps small').html(my_formatted_address);
    $('#' + target_id + '_auto_googlemaps').slideDown();    
  } else {
    auto_googlemaps=document.getElementById(target_id + '_auto_googlemaps');
    auto_googlemaps.innerHTML=my_formatted_address;
    auto_googlemaps.style.display='block';
  }
  
  ma_odos=target_id;
  ma_arithmos=basediv.getAttribute('data-ma_arithmos'); 
  ma_orofos=basediv.getAttribute('data-ma_orofos'); 
  ma_perioxi=basediv.getAttribute('data-ma_perioxi'); 
  ma_poli=basediv.getAttribute('data-ma_poli'); 
  ma_tk=basediv.getAttribute('data-ma_tk'); 
  ma_country_id=basediv.getAttribute('data-ma_country_id'); 
  ma_nomos_id=basediv.getAttribute('data-ma_nomos_id'); 
  ma_latitude=basediv.getAttribute('data-ma_latitude'); 
  ma_longitude=basediv.getAttribute('data-ma_longitude'); 
  

  if (data_admincrmmap=='1') {
    my_formatted_address=myplace.formattedAddress
    if (myplace.displayName !== undefined) my_formatted_address=myplace.displayName + ', ' + my_formatted_address;
    $('#' + target_id).val(my_formatted_address);
    
    if (ma_latitude!='' && ma_longitude!='') {
      $('#' + ma_latitude).val(myplace.location.lat());
      $('#' + ma_longitude).val(myplace.location.lng());
    }    

    place_map_latitude=myplace.location.lat();
    place_map_longitude=myplace.location.lng();
    
    myLatLng = {lat: place_map_latitude, lng: place_map_longitude};
    if (typeof markerpos != 'undefined') markerpos.position=myLatLng;
    if (typeof markerpos != 'undefined') map.setOptions({center: myLatLng});
    map.setOptions({zoom: 17});
      
    return;  
  }
  
  if (data_transfer=='1') {
    my_formatted_address=myplace.formattedAddress
    if (myplace.displayName !== undefined) my_formatted_address=myplace.displayName + ', ' + my_formatted_address;
    $('#' + target_id).val(my_formatted_address);
    
    $('#' + target_id).attr('data-place_id',myplaceid);
    
    if (ma_latitude!='' && ma_longitude!='') {
      $('#' + ma_latitude).val(myplace.location.lat());
      $('#' + ma_longitude).val(myplace.location.lng());
    }    

    if (target_id=='poi_from_place_formatted_address') {
      poi_from_place_lat = myplace.location.lat();
			poi_from_place_lng = myplace.location.lng();
			$('#poi_id_from').val('').attr('data-id','0');
			$('#autocomplete_poi_id_from').attr('href', '#');
      $('#autocomplete_poi_id_from').hide();
      get_poi_id_after_latlng('from',poi_from_place_lat,poi_from_place_lng);
      
    } else if (target_id=='poi_to_place_formatted_address') {
      poi_to_place_lat = myplace.location.lat();
			poi_to_place_lng = myplace.location.lng();
			$('#poi_id_to').val('').attr('data-id','0');
			$('#autocomplete_poi_id_to').attr('href', '#');
      $('#autocomplete_poi_id_to').hide();
      get_poi_id_after_latlng('to',poi_to_place_lat,poi_to_place_lng);
    }

    $('#poi_diadromes_id_to').hide();
    $('#poi_diadromes_id_to span').html('0'); 

	  if (map_is_open) {
	    gks_map_update_markers(true);
	    gks_map_update_directions(); 
	  }
	          
    return;
  }
  
  if (simple_input=='1') {
    my_formatted_address=myplace.formattedAddress
    if (myplace.displayName !== undefined) my_formatted_address=myplace.displayName + ', ' + my_formatted_address;
    $('#' + target_id).val(my_formatted_address);
    return;
  }
    
  
  temp_odos='';
  temp_number='';
  temp_number2='';
  temp_tk='';
  temp_premise='';
  temp_neighborhood='';
  temp_locality='';
  temp_sublevel_1='';
  temp_level_3='';
  temp_level_2='';
  temp_level_1='';
  temp_country='';
  temp_country_short_name='';
  
      
  for (pi=0; pi < myplace.addressComponents.length;pi++) {
    //console.log(myplace.addressComponents[pi]);
    
    if (myplace.addressComponents[pi].types.length>0 && myplace.addressComponents[pi].types[0]=='subpremise') {
      temp_number2=myplace.addressComponents[pi].shortText.trim();
    }
    if (myplace.addressComponents[pi].types.length>0 && myplace.addressComponents[pi].types[0]=='street_number') {
      temp_number=myplace.addressComponents[pi].shortText.trim();
    }

    if (myplace.addressComponents[pi].types.length>0 && myplace.addressComponents[pi].types[0]=='route') {
      temp_odos=myplace.addressComponents[pi].longText.trim();
    }  
    if (myplace.addressComponents[pi].types.length>0 && myplace.addressComponents[pi].types[0]=='postal_code') {
      temp_tk=myplace.addressComponents[pi].shortText.replaceAll(' ','').trim();
    }  
    
    if (myplace.addressComponents[pi].types.length>0 && myplace.addressComponents[pi].types[0]=='premise') {
      temp_premise=myplace.addressComponents[pi].longText.trim();
    }
    if (myplace.addressComponents[pi].types.length>0 && myplace.addressComponents[pi].types[0]=='neighborhood') {
      temp_neighborhood=myplace.addressComponents[pi].longText.trim();
    }
    if (myplace.addressComponents[pi].types.length>0 && myplace.addressComponents[pi].types[0]=='locality') {
      temp_locality=myplace.addressComponents[pi].longText.trim();
    }
    
    if (myplace.addressComponents[pi].types.length>0 && myplace.addressComponents[pi].types[0]=='sublocality_level_1') {
      temp_sublevel_1=myplace.addressComponents[pi].longText.trim();
    }
    if (myplace.addressComponents[pi].types.length>0 && myplace.addressComponents[pi].types[0]=='administrative_area_level_3') {
      temp_level_3=myplace.addressComponents[pi].longText.trim();
    }
    if (myplace.addressComponents[pi].types.length>0 && myplace.addressComponents[pi].types[0]=='administrative_area_level_2') {
      temp_level_2=myplace.addressComponents[pi].longText.trim();
    }
    if (myplace.addressComponents[pi].types.length>0 && myplace.addressComponents[pi].types[0]=='administrative_area_level_1') {
      temp_level_1=myplace.addressComponents[pi].longText.trim();
    }
    if (myplace.addressComponents[pi].types.length>0 && myplace.addressComponents[pi].types[0]=='country') {
      temp_country=myplace.addressComponents[pi].longText.trim();
      temp_country_short_name=myplace.addressComponents[pi].shortText.trim();
      
    }
        
  }
  
  if (temp_number2!='') temp_number+=' ' + temp_number2;
  

  if (ma_odos!='') {
    temp_odos=temp_odos.trim();
    if (temp_odos=='') temp_odos=temp_premise;
    if (temp_odos=='' && myplace.formattedAddress!='') {
      pp=myplace.formattedAddress.split(',');
      temp_odos=pp[0].trim();
    }
    
    $('#' + ma_odos).val(temp_odos);
    $('#' + ma_arithmos).val(temp_number);
  }
      
  if (ma_orofos!='') {
    if (temp_neighborhood!='') {
      $('#' + ma_orofos).val(temp_neighborhood);
    } else {
      $('#' + ma_orofos).val('');
    }
  }
  if (ma_perioxi!='') {
    if (temp_neighborhood!='') {
      $('#' + ma_perioxi).val(temp_neighborhood);
    } else {
      $('#' + ma_perioxi).val('');
    }
  }
  
  if (ma_poli!='') {
    if (temp_locality!='') {
      $('#' + ma_poli).val(temp_locality);
    } else if (temp_sublevel_1!='') {
      $('#' + ma_poli).val(temp_sublevel_1);
    } else {
      $('#' + ma_poli).val('');
    }
  }
  
  if (ma_tk!='') {
    $('#' + ma_tk).val(temp_tk);
    
  }
  
  var country_new_val=0;
  if (ma_country_id!='') { 
    if (temp_country_short_name!='') {
      $('#' + ma_country_id + ' option').each(function() {
        tval=$(this).attr('data-ci');
        if (temp_country_short_name == tval) {
          tval2=$(this).attr('value');
          if ($('#' + ma_country_id).val!=tval2) {
            $('#' + ma_country_id).val(tval2);
            country_new_val=parseInt(tval2);if (isNaN(country_new_val)) country_new_val=0;
            //console.log(country_new_val);
            search_obj={};
            if (temp_odos!='') search_obj.odos=temp_odos;
            if (temp_tk!='') search_obj.tk=temp_tk;
            if (temp_premise!='') search_obj.premise=temp_premise;
            if (temp_neighborhood!='') search_obj.neighborhood=temp_neighborhood;
            if (temp_locality!='') search_obj.locality=temp_locality;
            if (temp_sublevel_1!='') search_obj.sublevel_1=temp_sublevel_1;
            if (temp_level_1!='') search_obj.level_1=temp_level_1;
            if (temp_level_2!='') search_obj.level_2=temp_level_2;
            if (temp_level_3!='') search_obj.level_3=temp_level_3;

            search_string=JSON.stringify(search_obj);
            //console.log(search_string);
            nomos_fill(ma_nomos_id,country_new_val,0,search_string);
          }
        }
      });  
    }
  }
  

  if (ma_latitude!='' && ma_longitude!='') {
    $('#' + ma_latitude).val(myplace.location.lat());
    $('#' + ma_longitude).val(myplace.location.lng());
  }
  
  if (typeof gks_this_map_set_pos != 'undefined') {
    gks_this_map_set_pos(myplace.location.lat(),myplace.location.lng());
  }    
    
    
}
// Helper function to refresh the session token.
function gks_googlemaps_refreshToken(request) {
  // Create a new session token and add it to the request.
  request.sessionToken = new google.maps.places.AutocompleteSessionToken();
}




function gks_switchery_defaults() {
  return {
    color:              getComputedStyle(document.documentElement).getPropertyValue('--gks_switchery_color'),  
    secondaryColor:     getComputedStyle(document.documentElement).getPropertyValue('--gks_switchery_secondaryColor'),  
    jackColor:          getComputedStyle(document.documentElement).getPropertyValue('--gks_switchery_jackColor'),  
    jackSecondaryColor: getComputedStyle(document.documentElement).getPropertyValue('--gks_switchery_jackSecondaryColor'),  
    disabledOpacity:    getComputedStyle(document.documentElement).getPropertyValue('--gks_switchery_disabledOpacity'),  
    speed:              getComputedStyle(document.documentElement).getPropertyValue('--gks_switchery_speed'),  
    size:               getComputedStyle(document.documentElement).getPropertyValue('--gks_switchery_size') 
  };  
}




function gks_isTextSelected(input) {
  if (typeof input.selectionStart == "number") {
    return input.selectionStart == 0 && input.selectionEnd == input.value.length;
  } else if (typeof document.selection != "undefined") {
    input.focus();
    return document.selection.createRange().text == input.value;
  }
}


/* View in fullscreen */
function gks_openFullscreen() {
  var elem = document.documentElement;
  if (elem.requestFullscreen) {
    elem.requestFullscreen();
  } else if (elem.webkitRequestFullscreen) { /* Safari */
    elem.webkitRequestFullscreen();
  } else if (elem.msRequestFullscreen) { /* IE11 */
    elem.msRequestFullscreen();
  }
}

/* Close fullscreen */
function gks_closeFullscreen() {
  if (document.exitFullscreen) {
    document.exitFullscreen();
  } else if (document.webkitExitFullscreen) { /* Safari */
    document.webkitExitFullscreen();
  } else if (document.msExitFullscreen) { /* IE11 */
    document.msExitFullscreen();
  }
}

function handleLocationError(browserHasGeolocation, infoWindow_userpos, pos) {
  infoWindow_userpos.setPosition(pos);
  infoWindow_userpos.setContent(browserHasGeolocation ?
                        gks_lang('Σφάλμα: Η υπηρεσία εντοπισμού γεωγραφικής τοποθεσίας απέτυχε') :
                        gks_lang('Σφάλμα: Το πρόγραμμα περιήγησής σας δεν υποστηρίζει τον εντοπισμό της γεωγραφικής θέσης'));
  infoWindow_userpos.open({map:map});
}

function gks_setCookie(name,value,myseconds,mypath='/') {
  var expires = "";
  if (myseconds) {
    var date = new Date();
    date.setTime(date.getTime() + (myseconds*1000));
    expires = "; expires=" + date.toUTCString();
  }
  document.cookie = name + "=" + (value || "")  + expires + "; path=" + mypath;
}
function gks_getCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0)==' ') c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
}
function gks_eraseCookie(name) {   
  document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}



window.gks_is_mobile = function() {
  let check = false;
  (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
  return check;
};

window.gks_is_mobile_or_tablet = function() {
  let check = false;
  (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
  return check;
};

function gks_dateDiffInDays(a, b) {
  const _MS_PER_DAY = 1000 * 60 * 60 * 24;
  // Discard the time and time-zone information.
  const utc1 = Date.UTC(a.getFullYear(), a.getMonth(), a.getDate());
  const utc2 = Date.UTC(b.getFullYear(), b.getMonth(), b.getDate());

  return Math.floor((utc2 - utc1) / _MS_PER_DAY);
}






jQuery(document).ready(function($) {

  window.timestamp = new Date().getTime();
  window.myreload=false;
  
  if (typeof(from_php_page_is_order_item) == 'undefined') from_php_page_is_order_item=false;



//  $(".dropdown").hover(function(){
//    var dropdownMenu = $(this).children(".dropdown-menu");
//    if(dropdownMenu.is(":visible")){
//      dropdownMenu.parent().toggleClass("open");
//    }
//  });
 
  if (typeof(from_php_perm_ret_edit) !== 'undefined') {
    if (from_php_perm_ret_edit==false) {
      //console.log(from_php_perm_ret_edit);  
      mypostform_elem=$('#mypostform');
      if (mypostform_elem.length>0) {
        mypostform_elem.find('input').prop('disabled', true);
        mypostform_elem.find('textarea').prop('disabled', true);
        mypostform_elem.find('select').prop('disabled', true);
        mypostform_elem.find('#btn_gsis_get').prop('disabled', true);
        mypostform_elem.find('#f_button_add_files_photo').hide();
        mypostform_elem.find('#viber_send_def_text').prop('disabled', true);
        mypostform_elem.find('#showmap').prop('disabled', true);
        mypostform_elem.find('#map_pos').prop('disabled', true);

        mypostform_elem.find('.gks_comm_email_add').hide();
        mypostform_elem.find('.gks_comm_email_delete').hide();
        mypostform_elem.find('.gks_comm_phone_add').hide();
        mypostform_elem.find('.gks_comm_phone_delete').hide();
        mypostform_elem.find('.gks_comm_url_add').hide();
        mypostform_elem.find('.gks_comm_url_delete').hide();

        mypostform_elem.find('.gks_comm_email_primary').hide();
        mypostform_elem.find('.gks_comm_email_primary_sel').show();
        mypostform_elem.find('.gks_comm_phone_primary').hide();
        mypostform_elem.find('.gks_comm_phone_primary_sel').show();
        mypostform_elem.find('.gks_comm_url_primary').hide();
        mypostform_elem.find('.gks_comm_url_primary_sel').show();
        
        mypostform_elem.find('#reset_profile_photo').hide();
        mypostform_elem.find('#reset_category_photo').hide();
        mypostform_elem.find('#reset_brand_photo').hide();
        mypostform_elem.find('.set_profile_photo').hide();
        mypostform_elem.find('.set_category_photo').hide();
        mypostform_elem.find('.set_brand_photo').hide();
        mypostform_elem.find('.delete_upload_photo').hide();
        mypostform_elem.find('.delete_category_upload_photo').hide();
        mypostform_elem.find('.delete_brand_upload_photo').hide();
        mypostform_elem.find('.set_bom_kostos').hide();
        
        
        
        mypostform_elem.find('#variable_add').hide();
        mypostform_elem.find('#variable_actions').hide();
        mypostform_elem.find('.sortorder_handle').hide();
        mypostform_elem.find('.variable_product_delete').hide();
        mypostform_elem.find('.gks_idiotita_delete').hide();
        mypostform_elem.find('#idiotites_add').hide();
        
        mypostform_elem.find('.gks_clone_eidos_xarakt_esoda').hide();
        mypostform_elem.find('.gks_delete_eidos_xarakt_esoda').hide();
        mypostform_elem.find('.gks_add_xarakt_esoda').hide();
        mypostform_elem.find('.gks_clone_eidos_xarakt_eksoda').hide();
        mypostform_elem.find('.gks_delete_eidos_xarakt_eksoda').hide();
        mypostform_elem.find('.gks_add_xarakt_eksoda').hide();
        
        mypostform_elem.find('#tr_new_button').hide();
        
        mypostform_elem.find('.gks_div_card_add_card').hide();
        mypostform_elem.find('.gks_div_card_add_field').hide();
        mypostform_elem.find('.gks_div_custom_field_remove_icon').hide();
        mypostform_elem.find('.gks_div_custom_field_add_icon').hide();
        
        mypostform_elem.find('.gks_div_custom_field_handle').hide();
        

        
      }  
      $('#gks_rsrv_f').find('#submit_button_ok').prop('disabled', true);
      $('#gks_rsrv_f').find('#submit_button_ok_custom').prop('disabled', true);
      $('#gks_rsrv_f').find('#submit_button_preview').prop('disabled', true);
      
      
      $('.gks_export_excel').prop('disabled', true);
      $('.gks_export_word').prop('disabled', true);
    }
  }
  if (typeof(from_php_perm_ret_add) !== 'undefined') {
    if (from_php_perm_ret_add==false) {
      //console.log(from_php_perm_ret_add);  
      mypostform_elem=$('#mypostform');
      if (mypostform_elem.length>0) {
      
      }
      $('.gks_add_new_record').addClass('disabled');
      $('#gks_rsrv_f').find('#submit_button_copy').prop('disabled', true);
    }
  }
  if (typeof(from_php_perm_ret_delete) !== 'undefined') {
    if (from_php_perm_ret_delete==false) {
      //console.log(from_php_perm_ret_delete);  
      mypostform_elem=$('#mypostform');
      if (mypostform_elem.length>0) {
      
      }
      $('#gks_rsrv_f').find('.deleterowbtn').prop('disabled', true);
      $('.perm_delete').hide();
    }
  }
  
    
  
  var dialog_message;
  dialog_message = $( "#dialog_message" ).dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: {
      "OK" : function() {
        if (dialog_message.after_redirect=='') {
          if (myreload || dialog_message.after_reload) {
            window.location.reload();
          } else {
            if (dialog_message.after_focus_none==true) {
              //$('input').blur();   
              //$('body').focus();
            }
            $(this).dialog('close');
            if (dialog_message.after_focus_elem!='')  {
              $(dialog_message.after_focus_elem).focus();
            }            
          }
        } else {
          window.location.href = dialog_message.after_redirect;
        }
      }
    }
  });
  
  window.myalert = function (mymessage,after_redirect='',after_reload=false, after_focus_none=false, after_focus_elem='') {
      $("#dialog_message_ok").hide();
      $("#dialog_message_error").hide();
      $("#dialog_message_info").hide();
      $("#dialog_message_warning").hide();
      if (mymessage.substring(0, 6) == 'error:') {
         $("#dialog_message_error").show();
         mymessage=mymessage.substring(6);
      }
      if (mymessage.substring(0, 3) == 'ok:') {
         $("#dialog_message_ok").show();
         mymessage=mymessage.substring(3);
      }
      if (mymessage.substring(0, 5) == 'info:') {
         $("#dialog_message_info").show();
         mymessage=mymessage.substring(5);
      }
      if (mymessage.substring(0, 8) == 'warning:') {
         $("#dialog_message_warning").show();
         mymessage=mymessage.substring(8);
      }
      
      $("#dialog_message_message").html(mymessage);
      
      dwidth=$(window).width() * 0.96;
      dheight=$(window).height() * 0.96;
      //if (dwidth> 450) dwidth=450;
      //if (dheight> 330) dheight=330;
      if (dwidth> 600) dwidth=600;
      
      if (mymessage.includes('accinvposimgqrcode')) {
        
      } else {
        if (dheight> 450) dheight=450;
      }
  	  if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
  	    dwidth=$(window).width();dheight=$(window).height();
  	  } else if ($('body').hasClass('gks_erp_app_mobile')) {
  	    dwidth=$(window).width();dheight=$(window).height();
  	  } 
	        
      dialog_message.dialog('option', 'width', dwidth);
      dialog_message.dialog('option', 'height', dheight);
      $('#dialog_message').parent().css({position:'fixed'});      
      dialog_message.dialog('open');
      dialog_message.after_redirect=after_redirect;
      dialog_message.after_reload=after_reload;
      dialog_message.after_focus_none=after_focus_none;
      dialog_message.after_focus_elem=after_focus_elem;
      $('#gks_header_search_results').hide();
      
      $("#dialog_message").scrollTop(0);
  }; 
  
  var dialog_big_message;
  dialog_big_message = $( "#dialog_big_message" ).dialog({
    autoOpen: false,
    width: 450,
    height: 330,
    modal: true,
    buttons: {
      "OK" : function() {
        if (dialog_big_message.after_redirect=='') {
          $(this).dialog('close');

        } else {
          window.location.href = dialog_message.after_redirect;
        }
      }
    }
  });  
  
  window.mybigalert = function(mymessage,after_redirect='') {
    $("#dialog_big_message_message").html(mymessage);
    dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
    if (dwidth> 1000) dwidth=1000;
    if (dheight> 800) dheight=800;
	  if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
	    dwidth=$(window).width();dheight=$(window).height();
	  }    
    dialog_big_message.dialog('option', 'width', dwidth);
    dialog_big_message.dialog('option', 'height', dheight);
    $('#dialog_big_message').parent().css({position:'fixed'});      
    dialog_big_message.dialog('open');
    dialog_big_message.after_redirect=after_redirect;
    $('#gks_header_search_results').hide();
  };  

  $(".mybigmessage").click(function() {
    mytext = $(this).attr('data-html');
    mytext = $.base64.decode(mytext);
    mybigalert(mytext);
    return false;
  });  
  
  var dialog_email;
  dialog_email = $( "#dialog_email" ).dialog({
    autoOpen: false,
    width: 720,
    height: 550,
    modal: true,
    buttons: {
      "OK" : function() {
        $(this).dialog( "close" );
        $('#myiframe').attr('src','about:blank');
      }
    }
  });            
  
  window.gks_email_view_click = function () {
    myid=parseInt($(this).attr('data-id'));
    if (isNaN(myid)) myid=0;
    if (myid<=0) return;
    
    $('#dialog_email_headers').html('');
    
    dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
    //if (dwidth> 1000) dwidth=1000;
    //if (dheight> 800) dheight=800;
	  if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
	    dwidth=$(window).width();dheight=$(window).height();
	  }    
    dialog_email.dialog('option', 'width', dwidth);
    dialog_email.dialog('option', 'height', dheight);
    $('#dialog_email').parent().css({position:'fixed'});      
    dialog_email.dialog('open');
    
    $('body').addClass('myloading'); 
    
    datasend='';
    $.ajax({
      url: '/my/admin-email-view-exec.php?headers=1&id=' + myid,
      type: 'POST',
      cache: false,
      dataType: 'json',
      data: datasend,
      gks_myid: myid,
      error : function(jqXHR ,textStatus,  errorThrown) {
        $('body').removeClass('myloading');
        myalert('error:' + jqXHR.responseText);
        dialog_email.dialog("close"); 
      },        
      success: function(data) {
        $('body').removeClass('myloading');
        if (!data) {
          myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          dialog_email.dialog("close"); 
        } else {
          if (data.success == true) {
            $('#dialog_email_headers').html($.base64.decode(data.html));
            
            $('#dialog_email_iframe').attr('src','/my/admin-email-view-exec.php?id=' + this.gks_myid).css('width','100%');
            $('#dialog_email_iframe').on('load',  gks_myiframe_load);
            
          } else {
            myalert('error:' + $.base64.decode(data.message));
            dialog_email.dialog("close");            
          }
        }
      },
    });
        
  }
  $('.gks_email_view').click(gks_email_view_click);

  var dialog_sms;
  dialog_sms = $( "#dialog_sms" ).dialog({
    autoOpen: false,
    width: 720,
    height: 550,
    modal: true,
    buttons: {
      "OK" : function() {
        $(this).dialog( "close" );
        
      }
    }
  });  
  window.gks_sms_view_click = function () {
    myid=parseInt($(this).attr('data-id'));
    if (isNaN(myid)) myid=0;
    if (myid<=0) return;
    
    $('#dialog_sms_headers').html('');
    
    dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
    if (dwidth> 600) dwidth=600;
    //if (dheight> 800) dheight=800;
	  if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
	    dwidth=$(window).width();dheight=$(window).height();
	  }    
    dialog_sms.dialog('option', 'width', dwidth);
    dialog_sms.dialog('option', 'height', dheight);
    $('#dialog_sms').parent().css({position:'fixed'});      
    dialog_sms.dialog('open');
    

    $('body').addClass('myloading'); 
    
    datasend='';
    $.ajax({
      url: '/my/admin-sms-view-exec.php?headers=1&id=' + myid,
      type: 'POST',
      cache: false,
      dataType: 'json',
      data: datasend,
      gks_myid: myid,
      error : function(jqXHR ,textStatus,  errorThrown) {
        $('body').removeClass('myloading');
        myalert('error:' + jqXHR.responseText);
        dialog_sms.dialog("close"); 
      },        
      success: function(data) {
        $('body').removeClass('myloading');
        if (!data) {
          myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          dialog_sms.dialog("close"); 
        } else {
          if (data.success == true) {
            $('#dialog_sms_headers').html($.base64.decode(data.html));
            $('#dialog_sms_headers .gks_sms_command_resend').click(gks_sms_command_resend_click);

          } else {
            myalert('error:' + $.base64.decode(data.message));
            dialog_sms.dialog("close");            
          }
        }
      },
    });
        
  }  
  $('.gks_sms_view').click(gks_sms_view_click);
  
  
  window.gks_sms_command_resend_click=function() {
    data_id=parseInt($(this).attr('data-id'));  if (isNaN(data_id)) data_id=0;
    if (data_id<=0) return; 
    //console.log(data_id);

    datasend='id=' + data_id + '&cmd=resend';
    
    $('body').addClass('myloading');
    $.ajax({
      url: 'admin-sms-cmd.php',
      type: 'POST',
      cache: false,
      dataType: "json",
      data: datasend,
      error : function(jqXHR ,textStatus,  errorThrown) {
        $('body').removeClass('myloading');
				myalert('error:' + jqXHR.responseText);
			},
      success: function( data ) {
        $('body').removeClass('myloading');
        if (data.success == true) {
          myalert('ok:' + $.base64.decode(data.message) + '<br>'+gks_lang('Ανανεώστε την σελίδα για να δείτε την νέα εγγραφή που μόλις προστέθηκε'));
        } else {
          myalert('error:' + $.base64.decode(data.message));
        }
      }
    });    
  }
  
  
  function gks_myiframe_load() {
    myscrollHeight=this.contentWindow.document.body.scrollHeight;
    myscrollHeight+=100;
    this.style.height = myscrollHeight + 'px';
    
  }
    
  
  var dialog_confirm;
  dialog_confirm = $( "#dialog_confirm" ).dialog({
    autoOpen: false,
    width: 500,
    height: 500,
    modal: true,
    buttons: [
      {
        id: "dialog_inc_ok",
        html: '<i class="fa fa-pen-square"></i> '+gks_lang('OK'),
        //icon: "ui-icon-circle-plus",
        click: function() {
          $(this).dialog('close');
          
          switch (dialog_confirm.function_ok) {
            case 'deleterow':
              mydeleterow(dialog_confirm.delete_model,dialog_confirm.delete_id,dialog_confirm.delete_backurl,dialog_confirm.fnc_deleteafter);
              break;       
            case 'mycalendardelete':
              mycalendardelete(dialog_confirm.delete_id);
              break;       
            case 'mybankaccountdelete':
              //GR12 0172 2650 0052 6508 6566 119
              mybankaccountdelete(dialog_confirm.param1,dialog_confirm.param2);
              break;       
            case 'deleteitem_this':
              deleteitem_this(dialog_confirm.delete_model);
              break;       
            case 'notification_set_all_as_read':
              notification_set_all_as_read();
              break;
            case 'calendar_remove_other_user':
              calendar_remove_other_user();
              break;
            case 'gks_mysubmit_cancel':
              gks_mysubmit_cancel();
              break;
            case 'gks_mysubmit_credit_memo':
              gks_mysubmit_credit_memo();
              break;
            case 'gks_mysubmit_draft':
              gks_mysubmit_draft();
              break;
            case 'gks_mysubmit_create_acc_inv':
              gks_mysubmit_create_acc_inv();
              break;
            case 'gks_mysubmit_create_acc_pay':
              gks_mysubmit_create_acc_pay();
              break;
            case 'dialog_variable_copy_all_open_run':
              dialog_variable_copy_all_open_run();
              break;
            case 'dialog_variable_removeall_run':
              dialog_variable_removeall_run();
              break;
            case 'gks_pos_run_submit_force':
              gks_pos_run_submit_force_run();
              break;
            case 'gks_pos_run_submit_retry':
              gks_pos_run_submit_retry_run();
              break;
            case 'dialog_pricelist_removeall_run':
              dialog_pricelist_removeall_run();
              break;
            case 'gks_mymass_run':
              gks_mymass_run();
              break;
            case 'gks_fpa_template_apply_run':  
              gks_fpa_template_apply_run();
              break;
            default:
              myalert('error: dialog_confirm function_ok');
              break;
          }			
		    }	
      },
      {
        id: "dialog_inc_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Άκυρο'),
        //icon: "ui-icon-cancel",
        click: function() {
          $(this).dialog('close');
          
          switch (dialog_confirm.function_cancel) {
            case '':
            
              break;
            case 'gks_pos_run_submit_retry_cancel':
              gks_pos_run_submit_retry_cancel_run();
              break;
            default:
              myalert('error: dialog_confirm function_ok');
              break;
            
          }
        }			
      },      
    ]
        

  });
  
  window.myconfirm = function(mymessage, function_ok,delete_model,delete_id,delete_backurl,param1,param2,param3,fnc_deleteafter,function_cancel) {
    $("#dialog_confirm_message").html(mymessage);
    dialog_confirm.function_ok = function_ok;
    dialog_confirm.delete_model = delete_model;
    dialog_confirm.delete_id = delete_id;
    dialog_confirm.delete_backurl = delete_backurl;
    dialog_confirm.param1 = param1;
    dialog_confirm.param2 = param2;
    dialog_confirm.param3 = param3;
    dialog_confirm.fnc_deleteafter = fnc_deleteafter;
    dialog_confirm.function_cancel = (function_cancel === undefined ? '' : function_cancel);
    
    dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
    if (dwidth> 500) dwidth=500;
    if (dheight> 500) dheight=500;
	  if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
	    dwidth=$(window).width();dheight=$(window).height();
	  }    
    dialog_confirm.dialog('option', 'width', dwidth);
    dialog_confirm.dialog('option', 'height', dheight);
    $('#dialog_confirm').parent().css({position:'fixed'});      
    dialog_confirm.dialog('open');
    $('#gks_header_search_results').hide();
  }; 
  
  function mydeleterow(mymodel,myid,backurl,fnc_deleteafter) {
    datasend='mymodel=' + mymodel + '&myid=' + myid;
    //console.log(datasend);
    myid_int=parseInt(myid); if (isNaN(myid_int)) myid_int=-1;
    if (myid_int==-1) {
      if (fnc_deleteafter !== undefined && fnc_deleteafter!='') {
        parts=fnc_deleteafter.split('|');
        if (parts.length>=1 && parts[0].trim()!='') {
          myargs=[];
          for (i=1;i<parts.length;i++) myargs.push(parts[i]);
          myfnc_name=parts[0].trim();
          window[myfnc_name](myargs);
        } else {
          myalert('error:'+gks_lang('Σφάλμα κατά την διαγραφή. Ανανεώστε την σελίδα'));
        }
        
      }      
    } else {
      
      $('body').addClass('myloading');  
      $.ajax({
        url: '/my/admin-deleterow.php',
        type: 'POST',
        mybackurl: backurl,
        myfnc_deleteafter: fnc_deleteafter,
        cache: false,
        dataType: 'json',
        data: datasend,
        error : function(jqXHR ,textStatus,  errorThrown) {
          $('body').removeClass('myloading');
          myalert('error:' + jqXHR.responseText);
        },        
        success: function(data) {
          if (!data) {
            $('body').removeClass('myloading');
            myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          } else {
            if (data.success == true) {
              if (this.myfnc_deleteafter !== undefined && this.myfnc_deleteafter!='') {
                parts=this.myfnc_deleteafter.split('|');
                if (parts.length>=1 && parts[0].trim()!='') {
                  myargs=[];
                  for (i=1;i<parts.length;i++) myargs.push(parts[i]);
                  myfnc_name=parts[0].trim();
                  window[myfnc_name](myargs);
                } else {
                  myalert('error:'+gks_lang('Σφάλμα κατά την διαγραφή. Ανανεώστε την σελίδα'));
                }
                
              } else {
                need_save=false;
                if (this.mybackurl==null || this.mybackurl=='') {
                  window.location.reload();
                } else {
                  window.location.href=this.mybackurl;
                }
              }
            } else {
              $('body').removeClass('myloading');
              myalert('error:' + $.base64.decode(data.message));
            }
          }
        }
      });
    }
  }  
  
  window.gks_fnc_activity_delete_after = function (myargs) {
    $('body').removeClass('myloading');
    $('.activity_tr_exist[data-id=' + myargs[0] + ']').hide('fade', {}, 500,function() { 
      $(this).remove(); 
      var activity_aa=0;
      $('#activity_table .activity_aa').each(function () {
        activity_aa++;
        $(this).html(activity_aa);  
      });    
    });
  }
  
  window.gks_fnc_object_rel_delete_after = function (myargs) {
    $('body').removeClass('myloading');
    $('.object_rel_tr[data-id=' + myargs[0] + ']').hide('fade', {}, 500,function() { 
      $(this).remove(); 
      var gks_object_rel_aa=0;
      $('#gks_object_rel_table .gks_object_rel_aa').each(function () {
        gks_object_rel_aa++;
        $(this).html(gks_object_rel_aa);  
      });    
    });
  }



  window.deleterow_click = function() {
    var delete_id=$(this).attr('data-id');
    var delete_model=$(this).attr('data-model');
    var delete_backurl = $(this).attr('data-backurl');
    var delete_after=$(this).attr('data-deleteafter');
    if (delete_id == undefined || delete_model == undefined) {
      return false; 
    }
    if (delete_backurl == undefined) delete_backurl='';
    if (delete_after ==   undefined) delete_after='';
              //mymessage,                                function_ok,delete_model,delete_id,delete_backurl,param1,param2,param3,fnc_deleteafter
    myconfirm(gks_lang('Σίγουρα θέλετε να διαγράψετε την εγγραφή;'),'deleterow',delete_model,delete_id,delete_backurl,'',    '',    '',    delete_after);
  }
  $('.deleterowbtn, .deleterow').click(deleterow_click); 

  window.unlink_object_rel_click = function() {
    var delete_id=$(this).attr('data-id');
    var delete_model=$(this).attr('data-model');
    var delete_backurl = $(this).attr('data-backurl');
    var delete_after=$(this).attr('data-deleteafter');
    if (delete_id == undefined || delete_model == undefined) {
      return false; 
    }
    if (delete_backurl == undefined) delete_backurl='';
    if (delete_after ==   undefined) delete_after='';
              //mymessage,                                function_ok,delete_model,delete_id,delete_backurl,param1,param2,param3,fnc_deleteafter
    myconfirm(gks_lang('Σίγουρα θέλετε να κάνετε την αποσύνδεση;'),'deleterow',delete_model,delete_id,delete_backurl,'',    '',    '',    delete_after);
  }
  $('.unlink_object_rel').click(unlink_object_rel_click);


  
  $('[data-toggle="tooltip"]').bootstrapTltip();
  $('.tooltipster').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true});
  $('.tooltipsterfast').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:false});

  if (1==2 && $(window).width()>=576) {
    $('.gks_select2').select2({
      placeholder: gks_lang('Κάντε μια επιλογή'),
      //allowClear:true,
      
      language: 'el',
      //theme: 'classic',
      dropdownCssClass: 'gks_select2_dropdown',
      selectionCssClass: 'gks_select2_selection',
    });
  }


  var elems_switchery1_sel = Array.prototype.slice.call(document.querySelectorAll('.switchery1_sel'));
  elems_switchery1_sel.forEach(function(html) {
    var switchery1 = new Switchery(html,gks_switchery_defaults());
  }); 
  

  window.gks_myscroll = function() {
    gks_content_width=$(window).width();
    if (gks_content_width<576) {
      //$('#gks_rsrv_f').css('top',0); //111111111111
      $('#gks_rsrv_f').removeClass('gks_rsrv_fs');
    } else {
      mytoppos = $('#gks_rsrv_f_pos').offset().top;
      window_he= $(window).height();
      myscroll = $(window).scrollTop();
      
      gks_rsrv_f_height=$('#gks_rsrv_f').height();
      
      //console.log(gks_rsrv_f_height);
      gks_rsrv_f_height=-50-gks_rsrv_f_height;
      newtop = - mytoppos + window_he + gks_rsrv_f_height + myscroll;
      extrah=0;
      diafora = mytoppos + newtop;
      if (diafora<500) {
        extrah= 500-diafora
        newtop+=extrah;
      }
      if (newtop>0) {
        newtop=0;
        $('#gks_rsrv_f').removeClass('gks_rsrv_fs');
        $('#gks_rsrv_f_pos').removeClass('gks_rsrv_f_pos_xoros');
      } else {
        $('#gks_rsrv_f').addClass('gks_rsrv_fs');
        $('#gks_rsrv_f_pos').addClass('gks_rsrv_f_pos_xoros');
      }
      ///$('#gks_rsrv_f').css('top',newtop); //111111111111
      
      //console.log(mytoppos + ' ' + window_he + ' ' + myscroll + ' ' + newtop + ' ' + diafora);
    }

  }    
  if ($('#gks_rsrv_f').length>0) {
    $(window).scroll(function() { 
      gks_myscroll();
    });  
    $( window ).resize(function() {
      gks_myscroll();
    });

    gks_myscroll();
    
    function onElementHeightChange(elm, callback){
      var lastHeight = elm.clientHeight, newHeight;
      (function run(){
        newHeight = elm.clientHeight;
        if( lastHeight != newHeight )
          callback(newHeight)
        lastHeight = newHeight
    
            if( elm.onElementHeightChangeTimer )
              clearTimeout(elm.onElementHeightChangeTimer)
    
        elm.onElementHeightChangeTimer = setTimeout(run, 500)
      })()
    }
    
    
    onElementHeightChange(document.body, function(h){
      gks_myscroll();
      //console.log('Body height changed:', h)
    })
    
  }

  window.nomos_fill = function (myelement,country_id,nomos_id,search_string='') {  
    datasend = 'id=' + country_id;
    datasend+='&search_string=' + encodeURIComponent($.base64.encode(search_string));
    
    $('#' + myelement).val('0').attr('data_nomos_id',nomos_id);
    $('#' + myelement + ' option').each(function() { 
      if ($(this).attr('value') >0 ) $(this).remove();
    });
    //console.log('datasend=' + datasend);    
    if (country_id<=0) 
      return;
    $.ajax({
        url: "/my/admin-get-nomoi.php",
        type: 'POST',
        cache: false,
        dataType: "json",
        data:datasend,
        gks_myelement:myelement,
        gks_nomos_id:nomos_id,
        processData: false,
        error : function(jqXHR ,textStatus,  errorThrown) {
          myalert('error:' + jqXHR.responseText);
          //console.log('error:' + jqXHR.responseText);
        },
        success: function(data) {
          //console.log(data);
          if (!data) {
            myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          } else {
            if (data.success == true) {
              //console.log(data);
              if (data.out) {
                for (i = 0; i < data.out.length; i++) {
                  $('#' + this.gks_myelement).append('<option value="' + data.out[i].id + '">' + data.out[i].descr + '</option>');
                  if (this.gks_nomos_id > 0 && data.out[i].id == this.gks_nomos_id) {
                    $('#' + this.gks_myelement).val(this.gks_nomos_id);
                  }                  
                }
                if (data.out.length ==1) $('#' + this.gks_myelement).val(data.out[0].id);
                else if (this.gks_nomos_id == 0) $('#' + this.gks_myelement).val('0'); 
                
                if (this.gks_nomos_id == 0 && data.selected_id > 0) $('#' + this.gks_myelement).val(data.selected_id); 
                $('#' + this.gks_myelement).attr('data_nomos_id','');
              }
            } else {
              myalert('error:' + $.base64.decode(data.message));
            }
          }
        }
    });
  }  
  
  window.inv_acc_journal_id_fill = function (myelement,myelement_seira,company_id,company_sub_id,acc_journal_id) {  
    datasend = 'company_id=' + company_id + '&company_sub_id=' + company_sub_id + '&types=inv';
    $('#' + myelement).val('0');
    $('#' + myelement + ' option').each(function() { 
      if ($(this).attr('value') >0 ) $(this).remove();
    });
    $('#' + myelement_seira).val('0');
    $('#' + myelement_seira + ' option').each(function() { 
      if ($(this).attr('value') >0 ) $(this).remove();
    });
    if (company_id<=0) return;
    $.ajax({
        url: "/my/admin-get-journal.php",
        type: 'POST',
        cache: false,
        dataType: "json",
        data:datasend,
        gks_myelement:myelement,
        gks_myelement_seira:myelement_seira,
        gks_acc_journal_id:acc_journal_id,
        processData: false,
        error : function(jqXHR ,textStatus,  errorThrown) {
          myalert('error:' + jqXHR.responseText);
        },
        success: function(data) {
          if (!data) {
            myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          } else {
            if (data.success == true) {
              if (data.out) {
                for (i = 0; i < data.out.length; i++) {
                  $('#' + this.gks_myelement).append('<option value="' + data.out[i].id + '" ' +
                  'data-eidi_id="' + data.out[i].eidi_id + '" ' +
                  'data-type_id="' + data.out[i].type_id + '" ' +
                  'data-need_prev="' + data.out[i].need_prev + '" ' +
                  'data-fpa="' + data.out[i].fpa + '" ' +
                  'data-othertaxes="' + data.out[i].othertaxes + '" ' +
                  'data-esoda="' + data.out[i].esoda + '" ' +
                  'data-eksoda="' + data.out[i].eksoda + '" ' +
                  'data-need_afm="' + data.out[i].need_afm + '" ' +
                  'data-balance_pros="' + data.out[i].balance_pros + '" ' +
                  'data-whi_stock_pros="' + data.out[i].whi_stock_pros + '" ' +
                  'data-whi_type_id="' + data.out[i].whi_type_id + '" ' +
                  'data-other_entity="' + data.out[i].other_entity + '" ' +
                  'data-correlated_invoices="' + data.out[i].correlated_invoices + '" ' +
                  'data-multiple_connected_marks="' + data.out[i].multiple_connected_marks + '" ' +
                  'data-packings_declarations="' + data.out[i].packings_declarations + '" ' +
                  '>' + data.out[i].descr + '</option>');
                  if (this.gks_acc_journal_id > 0 && data.out[i].id == this.gks_acc_journal_id) {
                    $('#' + this.gks_myelement).val(this.gks_acc_journal_id);
                  }
                }
                if (data.out.length ==1) $('#' + this.gks_myelement).val(data.out[0].id);
                else if (this.gks_acc_journal_id == 0) $('#' + this.gks_myelement).val('0');
                inv_acc_journal_id_change();
              }
            } else {
              myalert('error:' + $.base64.decode(data.message));
            }
          }
        }
    });        
  }
  window.inv_acc_seira_id_fill = function (myelement,acc_journal_id,acc_seira_id) {  
    datasend = 'acc_journal_id=' + acc_journal_id;
    $('#' + myelement + ' option').each(function() { 
      if ($(this).attr('value') >0 ) $(this).remove();
    });    
    if (acc_journal_id<=0) return;
    $.ajax({
        url: "/my/admin-get-seira.php",
        type: 'POST',
        cache: false,
        dataType: "json",
        data:datasend,
        gks_myelement:myelement,
        gks_acc_seira_id:acc_seira_id,
        processData: false,
        error : function(jqXHR ,textStatus,  errorThrown) {
          myalert('error:' + jqXHR.responseText);
        },
        success: function(data) {
          if (!data) {
            myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          } else {
            if (data.success == true) {
              if (data.out) {
                for (i = 0; i < data.out.length; i++) {
                  $('#' + this.gks_myelement).append('<option value="' + data.out[i].id + '" data-is_xeirografi="' + data.out[i].is_xeirografi + '" data-is_deliverynote="' + data.out[i].is_deliverynote + '" data-is_self_pricing="' + data.out[i].is_self_pricing + '" data-is_vat_payment_suspension="' + data.out[i].is_vat_payment_suspension + '">' + data.out[i].descr + '</option>');
                  if (this.gks_acc_seira_id > 0 && data.out[i].id == this.gks_acc_seira_id) {
                    $('#' + this.gks_myelement).val(this.gks_acc_seira_id);
                  }
                }
                if (data.out.length ==1) $('#' + this.gks_myelement).val(data.out[0].id);
                else if (this.gks_acc_seira_id == 0) $('#' + this.gks_myelement).val('0');
                inv_acc_seira_id_change();
              }
            } else {
              myalert('error:' + $.base64.decode(data.message));
            }
          }
        }
    });        
  }
  
  
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
  
  
  
  window.gks_card_expand_run = function(elem) {
    gks_card_expand_icon_count++;
    is_carddiv=false;data_item='';expand_def='';
    if (elem.hasClass('gks_carddiv_expand')) {
      is_carddiv=true;
      data_item=elem.attr('data-item');
      if (elem.css('display')=='none') expand_def='close';
    } else {
      data_item=elem.find('.card-body:first').attr('data-item');
      if (elem.find('.card-body:first').css('display')=='none') expand_def='close';
    }
    myicon='<i class="fas fa-angle-double-down gks_card_expand_icon ' + 
    (is_carddiv ? 'gks_carddiv_expand_icon' : '') +
    '" ' +
    'id="gks_card_expand_icon_' + gks_card_expand_icon_count + '" ' +
    'data-expand="gks_card_expand_icon_' + gks_card_expand_icon_count + '" '  +
    'data-item="' + data_item + '" ' +
    'style="' + (expand_def=='close' ? '' : 'transform: rotate(180deg);') + '" ' +
    '></i>';
    if (is_carddiv) {
      $('gkscarddiv[data-item="' + data_item + '"]').html(myicon);
      elem.attr('data-expand','gks_card_expand_icon_' + gks_card_expand_icon_count);  
    } else {
      elem.find('.card-header:first').after(myicon).attr('data-expand','gks_card_expand_icon_' + gks_card_expand_icon_count);  
      elem.find('.card-body:first').attr('data-expand','gks_card_expand_icon_' + gks_card_expand_icon_count);
      elem.find('.card-footer:first').attr('data-expand','gks_card_expand_icon_' + gks_card_expand_icon_count);
      elem.find('.card-header:first').click(gks_card_expand_icon_click);
      if (expand_def=='close') {
        gksindex='gks_card_expand_icon_' + gks_card_expand_icon_count;
        $('.card-header[data-expand=' + gksindex + ']').css('border-bottom-width','0px');
      }
    }
    $('#gks_card_expand_icon_' + gks_card_expand_icon_count).click(gks_card_expand_icon_click);
    
    //console.log(gks_card_expand_icon_count);    
  }
  
  $('.gks_card_expand, .gks_carddiv_expand').each(function() {gks_card_expand_run($(this));});
  
  
  function gks_card_expand_icon_click() {
    datasend='';
    gksindex=$(this).attr('data-expand');
    is_carddiv=$(this).hasClass('gks_carddiv_expand_icon');
    if (is_carddiv) {
      if ($('.gks_carddiv_expand[data-expand=' + gksindex + ']').is(":visible")) {
        $('.gks_carddiv_expand[data-expand=' + gksindex + ']').hide('blind', {}, 500);
        $('#' + gksindex).animateRotate(180,0,500);
        datasend+='s=0';
      } else {
        $('.gks_carddiv_expand[data-expand=' + gksindex + ']').show('blind', {}, 500);
        $('#' + gksindex).animateRotate(0,180,500);
        datasend+='s=1';
      }
      item=$(this).attr('data-item');
    } else {
      if ($('.card-body[data-expand=' + gksindex + ']').is(":visible")) {
        $('.card-body[data-expand=' + gksindex + ']').hide('blind', {}, 500);
        $('.card-footer[data-expand=' + gksindex + ']').hide('blind', {}, 500);
        $('#' + gksindex).animateRotate(180,0,500);
        $('.card-header[data-expand=' + gksindex + ']').css('border-bottom-width','0px');
        datasend+='s=0';
      } else {
        $('.card-body[data-expand=' + gksindex + ']').show('blind', {}, 500);
        $('.card-footer[data-expand=' + gksindex + ']').show('blind', {}, 500);
        $('#' + gksindex).animateRotate(0,180,500);
        $('.card-header[data-expand=' + gksindex + ']').css('border-bottom-width','');
        datasend+='s=1';
      }    
      item=$('.card-body[data-expand=' + gksindex + ']').attr('data-item');
    }
    if (typeof(item) == 'undefined') return;
    
    datasend+='&i=' + encodeURIComponent($.base64.encode(item));
    datasend+='&u=' + encodeURIComponent($.base64.encode(window.location.href));
    
    //console.log(datasend);
    
    $.ajax({
      url: '/my/admin-users-card-expand.php',
      type: 'POST',
      cache: false,
      dataType: 'json',
      data: datasend,
      error : function(jqXHR ,textStatus,  errorThrown) {
        //console.log('error:' + jqXHR.responseText);
      },        
      success: function(data) {
        if (!data) {
          //console.log('error:'+gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
        } else {
          if (data.success == true) {
            
          } else {
            //console.log('error:' + $.base64.decode(data.message));
          }
        }
      },
    });
          
  }
  
  
  $('.divfiltertablemain_button').click(function() {
    datasend='';
    var ppdiv=$(this).parent().parent();
    if (ppdiv.hasClass('divfiltertablemain_expand')) {
      datasend+='s=0';
      $(this).animateRotate(180,0,500);
      ppdiv.animate({
        height: '68px',
      }, {
        duration: 500,
        easing: 'swing',
        complete: function() {
          ppdiv.removeClass('divfiltertablemain_expand').css('height','');;
        }
      });
    } else {
      datasend+='s=1';
      hh=$('.divfiltertablemain_content').height() + 5;
      $(this).animateRotate(0,180,500);
      ppdiv.animate({
        height: hh + 'px',
      }, {
        duration: 500,
        easing: 'swing',
        complete: function() {
          ppdiv.addClass('divfiltertablemain_expand').css('height','');
        }
      });
    }
    datasend+='&u=' + encodeURIComponent($.base64.encode(window.location.href));
    
    $.ajax({
      url: '/my/admin-users-filters-expand.php',
      type: 'POST',
      cache: false,
      dataType: 'json',
      data: datasend,
      error : function(jqXHR ,textStatus,  errorThrown) {
        //console.log('error:' + jqXHR.responseText);
      },        
      success: function(data) {
        if (!data) {
          //console.log('error:'+gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
        } else {
          if (data.success == true) {
            
          } else {
            //console.log('error:' + $.base64.decode(data.message));
          }
        }
      },
    });    
  });
  
  window.gks_resize_textarea=function(elem) {
    myid='div_resize_' + Math.floor(Math.random() * 10000); 
    mytext=elem.val().replace(/\n/g, '<br/>');
    if (mytext.endsWith('<br/>')) mytext+='&nbsp;';
    mywidth=elem.width();
    //myheight=elem.height();
    mywidth-=10;
    font_family=elem.css('font-family');
    font_size=elem.css('font-size');
    myhtml='<div id="' + myid + '" style="border:0px solid gray;width:' + mywidth + 'px;">' + mytext + '</div>';
    $('body').append(myhtml);
    $('#' + myid).css('font-family',font_family).css('font-size',font_size);
    newheight=$('#' + myid).height();
    temp=parseInt(elem.css('padding-top').replace(/px/g, '')); if (isNaN(temp)) temp=0;
    newheight+=temp;
    temp=parseInt(elem.css('padding-bottom').replace(/px/g, ''));if (isNaN(temp)) temp=0;
    newheight+=temp;
    temp=parseInt(elem.css('border-top-width').replace(/px/g, ''));if (isNaN(temp)) temp=0;
    newheight+=temp;
    temp=parseInt(elem.css('border-bottom-width').replace(/px/g, ''));if (isNaN(temp)) temp=0;
    newheight+=temp;
    elem.css('height',newheight + 'px');
    $('#' + myid).remove();
  }
  
  
  function gks_window_resize_this() {
    w=$(window).width();
    if (w>=1200) console.log('class xl');
    else if (w >= 992) console.log('class lg');
    else if (w >= 768) console.log('class md');
    else if (w >= 576) console.log('class sm');
    else if (w <  576) console.log('class empty');
    
    if (from_php_gks_user_settings_menu_sticky_top=='1') {
      if (w>=992) { //992 768
        gks_nav_session_header=$('#gks_nav_session_header');
        if (gks_nav_session_header.hasClass('sticky-top')==false) gks_nav_session_header.addClass('sticky-top');
      } else {
        gks_nav_session_header=$('#gks_nav_session_header');
        if (gks_nav_session_header.hasClass('sticky-top')) gks_nav_session_header.removeClass('sticky-top');
      }
    }
  }
  $(window).resize(gks_window_resize_this);
  gks_window_resize_this();

  $('.gks_stoppropagation').click(function() {
    //event.preventDefault();
    event.stopPropagation();
    //return false;      
  });  

  window.gks_mydivexpand_click = function () {
    //console.log($(this).css('max-height'));
    if( $(this).hasClass('mydivexpand_on')) {
      $(this).removeClass('mydivexpand_on');
    } else {
      $(this).addClass('mydivexpand_on');
    }    
  }
  $('.mydivexpand').click(gks_mydivexpand_click);
  
  
  
  
  window.gks_lang_data_obj_input_collect = function () {  
    var datasend_ret='';
    $('.gks_lang_data_obj_input, .gks_lang_data_obj_input_textarea').each(function() {
      elem_id=$(this).attr('id');
      elem_value=$(this).val();
      datasend_ret+='&' + elem_id + '='  +  encodeURIComponent($.base64.encode(elem_value.trim()));
      
    });
    $('.gks_lang_data_obj_input_tinymce').each(function() {
      elem_id=$(this).attr('id');
      elem_value=tinyMCE.get(elem_id).getContent();
      datasend_ret+='&' + elem_id + '='  +  encodeURIComponent($.base64.encode(elem_value.trim()));
    });    
    return datasend_ret;
  };
  
  function gks_lang_data_obj_input_textarea_change() {gks_resize_textarea($(this));}
  $('.gks_lang_data_obj_input_textarea').on('change keyup paste', gks_lang_data_obj_input_textarea_change);
  $('.gks_lang_data_obj_input_textarea').each(function() {
    gks_resize_textarea($(this));
  });

  var dialog_activity;
  dialog_activity = $('#dialog_activity').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_activity_ok",
        html: '<i class="fas fa-save"></i> '+gks_lang('Αποθήκευση'),
        click: function() {
          if ($('#dialog_activity_type_id').val()=='0') {
            myalert('error:'+gks_lang('Επιλέξτε τον τύπο'));
            return;  
          }
          
          datasend='';
          datasend+='&cmd=edit';
          datasend+='&page=' + encodeURIComponent($.base64.encode(window.location.pathname));
          datasend+='&id=' + dialog_activity.rec_id;
          datasend+='&model=' + encodeURIComponent($.base64.encode(from_php_activity_model));
          datasend+='&model_id=' + from_php_activity_model_id;
          datasend+='&status=' + encodeURIComponent($.base64.encode($('.activity_status_selected').attr('data-id')));
          datasend+='&user_id=' + encodeURIComponent($('#dialog_activity_user_id').attr('data-id'));
          datasend+='&type_id=' + encodeURIComponent($('#dialog_activity_type_id').val());
          datasend+='&duedate=' + encodeURIComponent($('#dialog_activity_duedate').val().trim());
          datasend+='&notification=' + ($('#dialog_activity_notification').is(':checked') ? '1':'0');
          datasend+='&diarkeia=' + encodeURIComponent($('#dialog_activity_diarkeia').val().trim());
          datasend+='&color='  + encodeURIComponent($.base64.encode($('#dialog_activity_color').val().trim()));
          datasend+='&subject='  + encodeURIComponent($.base64.encode($('#dialog_activity_subject').val().trim()));
          datasend+='&message='  + encodeURIComponent($.base64.encode($('#dialog_activity_message').val().trim()));



          //console.log(datasend);
          $('body').addClass('myloading');
          $.ajax({
            url: '/my/admin-crm-activity-item-exec.php',
            type: 'POST',
            cache: false,
            dataType: 'json',
            data: datasend,
            error : function(jqXHR ,textStatus,  errorThrown) {
              $('body').removeClass('myloading');
              myalert('error:' + jqXHR.responseText);
            },        
            success: function(data) {
              if (!data) {
                $('body').removeClass('myloading');
                myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
              } else {
                
                if (data.success == true) {
                  if (from_php_id== -2 ) {
                    if (window.location.pathname=='/my/admin-crm-calendar.php') {
                      $('#calc_refetch').click();
                      $('body').removeClass('myloading');
                      dialog_activity.dialog('close');
                    } else if (window.location.pathname=='/my/admin-crm-activity.php' && window.location.search.startsWith('?id=')) {
                      window.location.href='/my/admin-crm-activity.php';
                    } else {
                      window.location.reload();
                    }
                  } else {
                      
                    row_html=$.base64.decode(data.row_html);
                    //console.log(row_html);
                    if (data.is_new_rec) {
                      
                      tr_first=$('#activity_table tbody tr:first');
                      if (tr_first.length>=1) {
                        tr_first.before(row_html);
                      } else {
                        $('#activity_table tbody').html(row_html);
                      }
                      
                    } else {
                      $('#activity_table .activity_tr_exist[data-id=' + data.id + ']').replaceWith(row_html);
                    }
                    
                    $('.activity_tr_new .activity_edit').click(activity_edit_click);
                    $('.activity_tr_new .mydivexpand').click(gks_mydivexpand_click);
                    $('.activity_tr_new .deleterow').click(deleterow_click); 
  
                    $('.activity_tr_new').each(function() {
                      $(this).removeClass('activity_tr_new').addClass('activity_tr_exist');
                    });
                    var activity_aa=0;
                    $('#activity_table .activity_aa').each(function () {
                      activity_aa++;
                      $(this).html(activity_aa);  
                    });
                    
                    dialog_activity.dialog( "close" );
                    $('body').removeClass('myloading');

                  }

                } else {
                  $('body').removeClass('myloading');
                  myalert('error:' + $.base64.decode(data.message));
                }
              }
            }
            
          });     
      
          return false;          

        },
      },
      {
        id: "dialog_activity_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Άκυρο'),
        click: function() {
          $( this ).dialog( "close" );
        }
      },      
    ]        
        

    
  });

  var sin_ores_se_lepta=10*60;
  function dialog_activity_type_id_change() {
    //meeeting =4
    val=parseInt($('#dialog_activity_type_id').val());
    if (isNaN(val)) val=0;
    if (val==4) {
      $('#dialog_activity_diarkeia_div').show('blind', {}, 500);
      $('#dialog_activity_duedate_label').html(gks_lang('Ημερομηνία-Ώρα έναρξης'));

      tempval=$('#dialog_activity_duedate').datetimepicker('getValue');
      //tempval=new Date(tempval.getFullYear(),tempval.getMonth(), tempval.getDate());
      //tempval.setTime(tempval.getTime() + (sin_ores_se_lepta*60*1000)); //sin 10 ores
      //console.log(tempval);
      
      //$('#dialog_activity_duedate').datetimepicker({'mask':'39/19/9999 29:59',format:'d/m/Y H:i','timepicker':true});
      //$('#dialog_activity_duedate').datetimepicker({'value':tempval});
      
    } else {
      $('#dialog_activity_diarkeia_div').hide('blind', {}, 500);
      $('#dialog_activity_duedate_label').html(gks_lang('Έως πότε'));
      
      tempval=$('#dialog_activity_duedate').datetimepicker('getValue');
      //tempval=new Date(tempval.getFullYear(),tempval.getMonth(), tempval.getDate());
      //tempval.setTime(tempval.getTime());
      
      //console.log(tempval);
      
      //$('#dialog_activity_duedate').datetimepicker({'mask':'39/19/9999 29:59',format:'d/m/Y H:i','timepicker':true});
      
    }
    $('#dialog_activity_duedate').datetimepicker({'value':tempval});
    
    
  }
  
  $('#dialog_activity_type_id').change(dialog_activity_type_id_change);
  


  
  function activity_edit_click() {
    activity_id=parseInt($(this).attr('data-id'));
    if (isNaN(activity_id)) activity_id=0;
    if (activity_id<=0) return;
    //console.log(activity_id);
    activity_add_click(activity_id);
    
  }
  
  $('.activity_edit').click(activity_edit_click);
  
  $('#activity_add').click(function() {
    event.stopPropagation();
    activity_add_click(-1);
  });
  
  var dialog_activity_notification = new Switchery(document.querySelector('#dialog_activity_notification'),gks_switchery_defaults());
  
  $('#dialog_activity_color').spectrum({
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
  
      
  window.activity_add_click = function(myid) {
  //function activity_add_click(myid) {
    //if (typeof newurl != 'undefined') {
    //  event.stopPropagation();
    //}
    if (from_php_id<=0 && from_php_id!= -2) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την εγγραφή'));
      return;
    }
    if (myid==-1) {
      $('.activity_status_this').each(function() {
        $(this).removeClass('activity_status_selected');
      });
      $('.activity_status_this[data-id=050new]').addClass('activity_status_selected');
      $('#dialog_activity_user_id').val(from_php_activity_def_user_name).attr('data-id',from_php_activity_def_user_id);
      $('#dialog_activity_type_id').val(0);
      
      dialog_activity_type_id_change();
      //duedate=new Date(new Date().getFullYear(),new Date().getMonth() , new Date().getDate());
      //duedate.setTime(duedate.getTime() + (0*24*60*60*1000));
      duedate = new Date();
      $('#dialog_activity_duedate').datetimepicker({'value':duedate});
      //console.log(duedate);
      $('#dialog_activity_diarkeia').val(60);
      
      if ($('#dialog_activity_notification').is(':checked')) {
        $('#dialog_activity_notification').click();
      }
      $('#dialog_activity_color').spectrum('set','');
      $('#dialog_activity_subject').val('');
      $('#dialog_activity_message').val('');
      gks_resize_textarea($('#dialog_activity_message'));
      
      
      dialog_activity.rec_id=-1;
      dwidth=$(window).width() * 0.96;
      dheight=$(window).height() * 0.96;
      if (dwidth> 600) dwidth=600;
      if (dheight> 650) dheight=650;
  	  if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
  	    dwidth=$(window).width();dheight=$(window).height();
  	  }      
      dialog_activity.dialog('option', 'width', dwidth);
      dialog_activity.dialog('option', 'height', dheight);
      $('#dialog_activity').parent().css({position:'fixed'});      
      dialog_activity.dialog('open');
      $('#gks_header_search_results').hide();
      
    } else {
      dialog_activity.rec_id=myid;
      datasend='';    
      datasend+='&cmd=get';
      datasend+='&id=' + dialog_activity.rec_id;
      datasend+='&model=' + encodeURIComponent($.base64.encode(from_php_activity_model));
      datasend+='&model_id=' + from_php_activity_model_id;
      
      $('body').addClass('myloading');
      $.ajax({
        url: '/my/admin-crm-activity-item-exec.php',
        type: 'POST',
        cache: false,
        dataType: 'json',
        data: datasend,
        error : function(jqXHR ,textStatus,  errorThrown) {
          $('body').removeClass('myloading');
          myalert('error:' + jqXHR.responseText);
        },        
        success: function(data) {
          $('body').removeClass('myloading');
          if (!data) {
            myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          } else {
            
            if (data.success == true) {
              $('.activity_status_this').each(function() {
                $(this).removeClass('activity_status_selected');
              });
              $('.activity_status_this[data-id=' + data.row_out.status + ']').addClass('activity_status_selected');
              $('#dialog_activity_user_id').val(data.row_out.user_nickname).attr('data-id', data.row_out.user_id);
              $('#dialog_activity_type_id').val(data.row_out.type_id);
              dialog_activity_type_id_change();
              duedate=new Date();
              duedate.setTime(data.row_out.duedate * 1000); //unix time stamp
              //console.log(duedate);
              
              if (data.row_out.type_id == 4) { //meeting
                sin_ores_se_lepta=duedate.getHours()*60+duedate.getMinutes();
                //console.log(sin_ores_se_lepta);
                $('#dialog_activity_diarkeia').val(data.row_out.diarkeia);
              } else {
                $('#dialog_activity_diarkeia').val(60);  
              }
              
              $('#dialog_activity_duedate').datetimepicker({'value':duedate});
              
              tenpbb=data.row_out.notification==1;
              if ($('#dialog_activity_notification').is(':checked')!=tenpbb) {
                $('#dialog_activity_notification').click();
              }
              $('#dialog_activity_color').spectrum('set',data.row_out.color);
              $('#dialog_activity_subject').val(data.row_out.subject);
              $('#dialog_activity_message').val(data.row_out.message);
              gks_resize_textarea($('#dialog_activity_message'));
              
              
              
              dwidth=$(window).width() * 0.96;
              dheight=$(window).height() * 0.96;
              if (dwidth> 600) dwidth=600;
              if (dheight> 650) dheight=650;
          	  if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
          	    dwidth=$(window).width();dheight=$(window).height();
          	  }              
              dialog_activity.dialog('option', 'width', dwidth);
              dialog_activity.dialog('option', 'height', dheight);
              $('#dialog_activity').parent().css({position:'fixed'});      
              dialog_activity.dialog('open');
              $('#gks_header_search_results').hide();
              

            } else {
              myalert('error:' + $.base64.decode(data.message));
            }
          }
        }
        
      });      
    }
    
  }
  
  
  $('.activity_status_this').click(function() {
    $('.activity_status_this').each(function() {
      $(this).removeClass('activity_status_selected');
    });
    $(this).addClass('activity_status_selected');
  });
    
  $('#dialog_activity_duedate').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999 29:59',format:'d/m/Y H:i', timepicker:true,dayOfWeekStart:1}));  

  function dialog_activity_message_change() {gks_resize_textarea($(this));}
  $('#dialog_activity_message').on('change keyup paste', dialog_activity_message_change);
  if ($('#dialog_activity_message').length>0) gks_resize_textarea($('#dialog_activity_message'));

  $('#dialog_activity_user_id').autocomplete({
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
    autoFocus: true,
    delay: 300, //default
    select: function( event, ui ) {
      $("#dialog_activity_user_id").attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      if(!ui.item) {
        $("#dialog_activity_user_id").val('').attr('data-id','0');
      }
    }
  });
  
  

  var dialog_object_rel;
  dialog_object_rel = $('#dialog_object_rel').dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: [
      {
        id: "dialog_object_rel_ok",
        html: '<i class="fa fa-pen-square"></i> '+gks_lang('Σύνδεση'),
        click: function() {
         
          user_sel_id=parseInt($('#dialog_object_rel_id').val());
          if (isNaN(user_sel_id)) user_sel_id=0;
          
          if (user_sel_id<=0) {
            myalert('error:'+gks_lang('Ορίστε το συνδεδεμένο αντικείμενο'));
            return;  
          }
          
          datasend='';
          datasend+='&name1=' + from_php_dialog_object_rel_curr;
          datasend+='&id1=' + from_php_id;
          datasend+='&name2=' + $('#dialog_object_rel_obj').val();
          datasend+='&id2=' + user_sel_id;
          
          



          //console.log(datasend);
          $('body').addClass('myloading');
          $.ajax({
            url: '/my/admin-object-rel-exec.php',
            type: 'POST',
            cache: false,
            dataType: 'json',
            data: datasend,
            error : function(jqXHR ,textStatus,  errorThrown) {
              $('body').removeClass('myloading');
              myalert('error:' + jqXHR.responseText);
            },        
            success: function(data) {
              if (!data) {
                $('body').removeClass('myloading');
                myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
              } else {
                
                
                if (data.success == true) {

                      
                    row_html=$.base64.decode(data.row_html);

                    
                      
                    tr_first=$('#gks_object_rel_table tbody tr:first');
                    if (tr_first.length>=1) {
                      tr_first.before(row_html);
                    } else {
                      $('#gks_object_rel_table tbody').html(row_html);
                    }
                      

                    $('.gks_object_rel_tr_new .unlink_object_rel').click(unlink_object_rel_click); 
  
                    $('.gks_object_rel_tr_new').each(function() {
                      $(this).removeClass('gks_object_rel_tr_new').addClass('gks_object_rel_tr_exist');
                    });
                    var gks_object_rel_aa=0;
                    $('#gks_object_rel_table .gks_object_rel_aa').each(function () {
                      gks_object_rel_aa++;
                      $(this).html(gks_object_rel_aa);  
                    });
                    
                    dialog_object_rel.dialog( "close" );
                    $('body').removeClass('myloading');

                  

                } else {
                  $('body').removeClass('myloading');
                  myalert('error:' + $.base64.decode(data.message));
                }
              }
            }
            
          });     
      
          return false;          

        },
      },
      {
        id: "dialog_object_rel_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Άκυρο'),
        click: function() {
          $( this ).dialog( "close" );
        }
      },      
    ]        
        

    
  });
  
  window.dialog_object_rel_add_click = function() {
    event.stopPropagation();
    if (from_php_id<=0) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την εγγραφή'));
      return;
    }
    $('#dialog_object_rel_id').val('');

    var dialog_object_rel_obj_option_list=[];
    $('#dialog_object_rel_obj option').each(function() {
      vvv=$(this).attr('value').trim();
      dialog_object_rel_obj_option_list.push(vvv);
    });
    //console.log(dialog_object_rel_obj_option_list);
    
    tabs=window.gks_tabs_registry_obj.getOpenTabs();
    //console.log(tabs);
    
    html='';
    for(i=(tabs.length-1); i>=0; i--) {
      object_rel='';if (typeof(tabs[i].object_rel) != 'undefined') object_rel=tabs[i].object_rel;
      rec_id=parseInt(tabs[i].rec_id);if (isNaN(rec_id)) rec_id=0;
      ctid=parseInt(tabs[i].ctid);if (isNaN(ctid)) ctid=0;
      if (object_rel!='' && dialog_object_rel_obj_option_list.includes(object_rel) && rec_id>0) {
        html+='<div class="gks_tabs_registry_item" '+
        'data-object_rel="'+object_rel+'" '+
        'data-ctid="'+ctid+'" '+
        'data-rec_id="'+rec_id+'" '+
        '><i class="fas fa-arrow-alt-circle-right"></i><span>'+tabs[i].title+'</span></div>';
      }
    }
    $('#dialog_object_rel_list').html(html);
    $('#dialog_object_rel_list .gks_tabs_registry_item').click(gks_tabs_registry_item_click);
    
    dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
    if (dwidth> 600) dwidth=600;
    if (dheight> 450) dheight=450;
	  if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
	    dwidth=$(window).width();dheight=$(window).height();
	  }    
    dialog_object_rel.dialog('option', 'width', dwidth);
    dialog_object_rel.dialog('option', 'height', dheight);
    $('#dialog_object_rel').parent().css({position:'fixed'});      
    dialog_object_rel.dialog('open');      
    $('#gks_header_search_results').hide();
  }
  
  $('#dialog_object_rel_add').click(dialog_object_rel_add_click);
  
  function gks_tabs_registry_item_click() {
    object_rel=$(this).attr('data-object_rel');
    ctid=$(this).attr('data-ctid');
    rec_id=$(this).attr('data-rec_id');
    elem=$('#dialog_object_rel_obj option[value="'+object_rel+'"]');
    if (elem.length==1) {
      $('#dialog_object_rel_obj').val(object_rel);
      $('#dialog_object_rel_id').val(rec_id);
    }
    
  }
  
  
  window.pay_acc_journal_id_fill = function (myelement,myelement_seira,company_id,company_sub_id,acc_journal_id) {  
    datasend = 'company_id=' + company_id + '&company_sub_id=' + company_sub_id + '&types=pay';
    $('#' + myelement).val('0');
    $('#' + myelement + ' option').each(function() { 
      if ($(this).attr('value') >0 ) $(this).remove();
    });
    $('#' + myelement_seira).val('0');
    $('#' + myelement_seira + ' option').each(function() { 
      if ($(this).attr('value') >0 ) $(this).remove();
    });
    if (company_id<=0) return;
    $.ajax({
        url: "/my/admin-get-journal.php",
        type: 'POST',
        cache: false,
        dataType: "json",
        data:datasend,
        gks_myelement:myelement,
        gks_myelement_seira:myelement_seira,
        gks_acc_journal_id:acc_journal_id,
        processData: false,
        error : function(jqXHR ,textStatus,  errorThrown) {
          myalert('error:' + jqXHR.responseText);
        },
        success: function(data) {
          if (!data) {
            myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          } else {
            if (data.success == true) {
              if (data.out) {
                for (i = 0; i < data.out.length; i++) {
                  $('#' + this.gks_myelement).append('<option value="' + data.out[i].id + '" ' +
                  'data-eidi_id="' + data.out[i].eidi_id + '" ' +
                  'data-type_id="' + data.out[i].type_id + '" ' +
                  'data-need_prev="' + data.out[i].need_prev + '" ' +
                  'data-fpa="' + data.out[i].fpa + '" ' +
                  'data-othertaxes="' + data.out[i].othertaxes + '" ' +
                  'data-esoda="' + data.out[i].esoda + '" ' +
                  'data-eksoda="' + data.out[i].eksoda + '" ' +
                  'data-need_afm="' + data.out[i].need_afm + '" ' +
                  'data-balance_pros="' + data.out[i].balance_pros + '" ' +
                  'data-other_entity="' + data.out[i].other_entity + '" ' +
                  'data-correlated_invoices="' + data.out[i].correlated_invoices + '" ' +
                  'data-multiple_connected_marks="' + data.out[i].multiple_connected_marks + '" ' +
                  
                  '>' + data.out[i].descr + '</option>');
                  if (this.gks_acc_journal_id > 0 && data.out[i].id == this.gks_acc_journal_id) {
                    $('#' + this.gks_myelement).val(this.gks_acc_journal_id);
                  }
                }
                if (data.out.length ==1) $('#' + this.gks_myelement).val(data.out[0].id);
                else if (this.gks_acc_journal_id == 0) $('#' + this.gks_myelement).val('0');
                pay_acc_journal_id_change();
              }
            } else {
              myalert('error:' + $.base64.decode(data.message));
            }
          }
        }
    });        
  }
  window.pay_acc_seira_id_fill = function (myelement,acc_journal_id,acc_seira_id) {  
    datasend = 'acc_journal_id=' + acc_journal_id;
    $('#' + myelement + ' option').each(function() { 
      if ($(this).attr('value') >0 ) $(this).remove();
    });    
    if (acc_journal_id<=0) return;
    $.ajax({
        url: "/my/admin-get-seira.php",
        type: 'POST',
        cache: false,
        dataType: "json",
        data:datasend,
        gks_myelement:myelement,
        gks_acc_seira_id:acc_seira_id,
        processData: false,
        error : function(jqXHR ,textStatus,  errorThrown) {
          myalert('error:' + jqXHR.responseText);
        },
        success: function(data) {
          if (!data) {
            myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          } else {
            if (data.success == true) {
              if (data.out) {
                for (i = 0; i < data.out.length; i++) {
                  $('#' + this.gks_myelement).append('<option value="' + data.out[i].id + '" data-is_xeirografi="' + data.out[i].is_xeirografi + '">' + data.out[i].descr + '</option>');
                  if (this.gks_acc_seira_id > 0 && data.out[i].id == this.gks_acc_seira_id) {
                    $('#' + this.gks_myelement).val(this.gks_acc_seira_id);
                  }
                }
                if (data.out.length ==1) $('#' + this.gks_myelement).val(data.out[0].id);
                else if (this.gks_acc_seira_id == 0) $('#' + this.gks_myelement).val('0');
                pay_acc_seira_id_change();
              }
            } else {
              myalert('error:' + $.base64.decode(data.message));
            }
          }
        }
    });        
  }  


  window.mov_whi_journal_id_fill = function (myelement,myelement_seira,company_id,company_sub_id,acc_journal_id) {  
    datasend = 'company_id=' + company_id + '&company_sub_id=' + company_sub_id + '&types=whi';
    $('#' + myelement).val('0');
    $('#' + myelement + ' option').each(function() { 
      if ($(this).attr('value') >0 ) $(this).remove();
    });
    $('#' + myelement_seira).val('0');
    $('#' + myelement_seira + ' option').each(function() { 
      if ($(this).attr('value') >0 ) $(this).remove();
    });
    if (company_id<=0) return;
    $.ajax({
        url: "/my/admin-get-journal.php",
        type: 'POST',
        cache: false,
        dataType: "json",
        data:datasend,
        gks_myelement:myelement,
        gks_myelement_seira:myelement_seira,
        gks_acc_journal_id:acc_journal_id,
        processData: false,
        error : function(jqXHR ,textStatus,  errorThrown) {
          myalert('error:' + jqXHR.responseText);
        },
        success: function(data) {
          if (!data) {
            myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          } else {
            if (data.success == true) {
              if (data.out) {
                for (i = 0; i < data.out.length; i++) {
                  $('#' + this.gks_myelement).append('<option value="' + data.out[i].id + '" ' +
                  'data-eidi_id="' + data.out[i].eidi_id + '" ' +
                  'data-type_id="' + data.out[i].type_id + '" ' +
                  'data-need_prev="' + data.out[i].need_prev + '" ' +
                  'data-need_afm="' + data.out[i].need_afm + '" ' +
                  'data-stock_pros="' + data.out[i].stock_pros + '" ' +
                  'data-other_entity="' + data.out[i].other_entity + '" ' +
                  'data-correlated_invoices="' + data.out[i].correlated_invoices + '" ' +
                  'data-multiple_connected_marks="' + data.out[i].multiple_connected_marks + '" ' +
                  'data-packings_declarations="' + data.out[i].packings_declarations + '" ' +
                  '>' + data.out[i].descr + '</option>');
                  if (this.gks_acc_journal_id > 0 && data.out[i].id == this.gks_acc_journal_id) {
                    $('#' + this.gks_myelement).val(this.gks_acc_journal_id);
                  }
                }
                if (data.out.length ==1) $('#' + this.gks_myelement).val(data.out[0].id);
                else if (this.gks_acc_journal_id == 0) $('#' + this.gks_myelement).val('0');
                mov_whi_journal_id_change();
              }
            } else {
              myalert('error:' + $.base64.decode(data.message));
            }
          }
        }
    });        
  }
  window.mov_whi_seira_id_fill = function (myelement,acc_journal_id,acc_seira_id) {  
    datasend = 'acc_journal_id=' + acc_journal_id;
    $('#' + myelement + ' option').each(function() { 
      if ($(this).attr('value') >0 ) $(this).remove();
    });    
    if (acc_journal_id<=0) return;
    $.ajax({
        url: "/my/admin-get-seira.php",
        type: 'POST',
        cache: false,
        dataType: "json",
        data:datasend,
        gks_myelement:myelement,
        gks_acc_seira_id:acc_seira_id,
        processData: false,
        error : function(jqXHR ,textStatus,  errorThrown) {
          myalert('error:' + jqXHR.responseText);
        },
        success: function(data) {
          if (!data) {
            myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          } else {
            if (data.success == true) {
              if (data.out) {
                for (i = 0; i < data.out.length; i++) {
                  $('#' + this.gks_myelement).append('<option value="' + data.out[i].id + '" data-is_xeirografi="' + data.out[i].is_xeirografi + '" data-is_deliverynote="' + data.out[i].is_deliverynote + '" data-is_reverse_delivery_note="' + data.out[i].is_reverse_delivery_note + '">' + data.out[i].descr + '</option>');
                  if (this.gks_acc_seira_id > 0 && data.out[i].id == this.gks_acc_seira_id) {
                    $('#' + this.gks_myelement).val(this.gks_acc_seira_id);
                  }
                }
                if (data.out.length ==1) $('#' + this.gks_myelement).val(data.out[0].id);
                else if (this.gks_acc_seira_id == 0) $('#' + this.gks_myelement).val('0');
                mov_whi_seira_id_change();
              }
            } else {
              myalert('error:' + $.base64.decode(data.message));
            }
          }
        }
    });        
  } 
  
  
  window.order_journal_id_fill = function (myelement,myelement_seira,company_id,company_sub_id,acc_journal_id) {  
    datasend = 'company_id=' + company_id + '&company_sub_id=' + company_sub_id + '&types=order';
    $('#' + myelement).val('0');
    $('#' + myelement + ' option').each(function() { 
      if ($(this).attr('value') >0 ) $(this).remove();
    });
    $('#' + myelement_seira).val('0');
    $('#' + myelement_seira + ' option').each(function() { 
      if ($(this).attr('value') >0 ) $(this).remove();
    });
    if (company_id<=0) return;
    $.ajax({
        url: "/my/admin-get-journal.php",
        type: 'POST',
        cache: false,
        dataType: "json",
        data:datasend,
        gks_myelement:myelement,
        gks_myelement_seira:myelement_seira,
        gks_acc_journal_id:acc_journal_id,
        processData: false,
        error : function(jqXHR ,textStatus,  errorThrown) {
          myalert('error:' + jqXHR.responseText);
        },
        success: function(data) {
          if (!data) {
            myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          } else {
            if (data.success == true) {
              if (data.out) {
                for (i = 0; i < data.out.length; i++) {
                  $('#' + this.gks_myelement).append('<option value="' + data.out[i].id + '" ' +
                  'data-eidi_id="' + data.out[i].eidi_id + '" ' +
                  'data-type_id="' + data.out[i].type_id + '" ' +
                  'data-need_prev="' + data.out[i].need_prev + '" ' +
                  'data-fpa="' + data.out[i].fpa + '" ' +
                  'data-need_afm="' + data.out[i].need_afm + '" ' +
                  'data-balance_pros="' + data.out[i].balance_pros + '" ' +
                  'data-whi_stock_pros="' + data.out[i].whi_stock_pros + '" ' +
                  'data-whi_type_id="' + data.out[i].whi_type_id + '" ' +
                  '>' + data.out[i].descr + '</option>');
                  if (this.gks_acc_journal_id > 0 && data.out[i].id == this.gks_acc_journal_id) {
                    $('#' + this.gks_myelement).val(this.gks_acc_journal_id);
                  }
                }
                if (data.out.length ==1) $('#' + this.gks_myelement).val(data.out[0].id);
                else if (this.gks_acc_journal_id == 0) $('#' + this.gks_myelement).val('0');
                order_journal_id_change();
              }
            } else {
              myalert('error:' + $.base64.decode(data.message));
            }
          }
        }
    });        
  }
  window.order_seira_id_fill = function (myelement,acc_journal_id,acc_seira_id) {  
    datasend = 'acc_journal_id=' + acc_journal_id;
    $('#' + myelement + ' option').each(function() { 
      if ($(this).attr('value') >0 ) $(this).remove();
    });    
    if (acc_journal_id<=0) return;
    $.ajax({
        url: "/my/admin-get-seira.php",
        type: 'POST',
        cache: false,
        dataType: "json",
        data:datasend,
        gks_myelement:myelement,
        gks_acc_seira_id:acc_seira_id,
        processData: false,
        error : function(jqXHR ,textStatus,  errorThrown) {
          myalert('error:' + jqXHR.responseText);
        },
        success: function(data) {
          if (!data) {
            myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          } else {
            if (data.success == true) {
              if (data.out) {
                for (i = 0; i < data.out.length; i++) {
                  $('#' + this.gks_myelement).append('<option value="' + data.out[i].id + '" data-is_xeirografi="' + data.out[i].is_xeirografi + '">' + data.out[i].descr + '</option>');
                  if (this.gks_acc_seira_id > 0 && data.out[i].id == this.gks_acc_seira_id) {
                    $('#' + this.gks_myelement).val(this.gks_acc_seira_id);
                  }
                }
                if (data.out.length ==1) $('#' + this.gks_myelement).val(data.out[0].id);
                else if (this.gks_acc_seira_id == 0) $('#' + this.gks_myelement).val('0');
                order_seira_id_change();
              }
            } else {
              myalert('error:' + $.base64.decode(data.message));
            }
          }
        }
    });        
  }  
  
  
  window.gks_sortorder_obj = function (obj,list,elem) { 
    
      datasend='obj=' + encodeURIComponent($.base64.encode(obj));
      datasend+='&list_str=' + encodeURIComponent($.base64.encode(JSON.stringify(mylist)));
      $.ajax({
        url: '/my/admin-sortorder-exec.php',
        type: 'POST',
        cache: false,
        dataType: 'json',
        data: datasend,
        gks_elem:elem,
        error : function(jqXHR ,textStatus,  errorThrown) {
          myalert('error:' + jqXHR.responseText);
        },        
        success: function(data) {
          if (!data) {
            myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          } else {
            if (data.success == true) {
              //console.log(data);
              o_elem=$(this.gks_elem);
              for(i=0;i<data.ret.length;i++) {
                tdelem=o_elem.find('tr[data-id=' + data.ret[i].id + ']');
                if (tdelem.length==1) {
                  thdelem=tdelem.find('.sortorder_handle');
                  if (thdelem.length==0) thdelem=tdelem.find('.sortorder_handle_sub');
                  if (thdelem.length==1) {
                    thdelem.attr('title',data.ret[i].so);
                    thdelem.find('span').html(data.ret[i].so);
                  }
                }
              }
            } else {
              myalert('error:' + $.base64.decode(data.message));
            }
          }
        }
      });    
    
  }
  
  var dialog_obj_template_create=false;
  
  window.submit_button_template_create = function (obj_index) { 
    if (need_save) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα τις αλλαγές'));
      return;
    }
    obj_index=parseInt(obj_index); if (isNaN(obj_index)) obj_index=0;
    
    //console.log('submit_button_template_create',obj_index);
    if (dialog_obj_template_create===false) {
      dialog_html=
      '<div id="dialog_obj_template_create" title="' + from_php_GKS_SITE_HUMAN_NAME + '" style="display: none;">' +
        
        '<table style="width:100%" cellpadding="10">' +
        '<tbody><tr>' +
          '<td style="width:1%;vertical-align:top">' +
            '<i class="fas fa-file-alt" style="color: #dca327;font-size: 500%;"></i>' +
            
          '</td>' +
          '<td style="width:99%;vertical-align:top;padding-top:20px;">' +
            '<div style="text-align:left;font-weight: bold;font-size: 120%;">'+gks_lang('Ορισμός του τρέχοντος εγγράφου ως πρότυπο')+'</div>' + 
            '<div style="margin-top: 20px;">' + 
              gks_lang('Πληκτρολογήστε το όνομα για το συγκεκριμένο πρότυπο') + 
            '</div>' +
            '<div style="margin-top: 20px;">' +
              '<input type="text" id="dialog_obj_template_create_message" class="form-control form-control-sm" value="" autocomplete="' + autocomplete_gks_disable + '" >' +
            '</div>' + 
          '</td>' +
        '</tr>' +
      '</tbody></table>' +
  
      
      '<div>';
      $(document.body).append(dialog_html);
      
      dialog_obj_template_create = $('#dialog_obj_template_create').dialog({
        autoOpen: false,
        width: 400,
        height: 300,
        modal: true,
        buttons: [
          {
            id: "dialog_obj_template_create_set",
            html: '<i class="fa fa-plus-circle"></i> '+gks_lang('Ορισμός'),
            click: function() {

              if (from_php_id<=0) {
                myalert('error:'+gks_lang('Δεν βρέθηκε το ID του εγγράφου<br>Δοκιμάστε ξανά αργότερα'));
                return;
              }
              template_name=$('#dialog_obj_template_create_message').val().trim();
              if (template_name=='') {
                myalert('error:'+gks_lang('Πληκτρολογήστε κάποιο όνομα'));
                return;
              }
               
              datasend='';
              datasend+='&template_id=' + from_php_id;
              datasend+='&obj_index=' + obj_index;
              datasend+='&template_name=' + encodeURIComponent($.base64.encode(template_name));

              //console.log(datasend);
              
              $('body').addClass('myloading');
              $.ajax({
                url: '/my/admin-template-doc-add.php?id=' + from_php_id,
                type: 'POST',
                cache: false,
                dataType: 'json',
                data: datasend,
                gks_obj_index:obj_index,
                error : function(jqXHR ,textStatus,  errorThrown) {
                  $('body').removeClass('myloading');
                  myalert('error:' + jqXHR.responseText);
                },        
                success: function(data) {
                  $('body').removeClass('myloading');
                  if (!data) {
                    myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                  } else {
                    if (data.success == true) {
                      temp='ok:'+gks_lang('Επιτυχής ορισμός')+'<br>';
                      if (this.gks_obj_index==1 || this.gks_obj_index==2) {
                        temp+=gks_lang('Έχει προστεθεί στο μενού')+':<br><b>'+gks_lang('Λογιστική/Πρότυπα')+'</b>';
                      } else if (this.gks_obj_index==3) {
                        temp+=gks_lang('Έχει προστεθεί στο μενού')+':<br><b>'+gks_lang('Αποθήκη/Πρότυπα')+'</b>';
                      } else if (this.gks_obj_index==4) {
                        temp+=gks_lang('Έχει προστεθεί στο μενού')+':<br><b>'+gks_lang('Πωλήσεις/Πρότυπα')+'</b>';
                      }
                      myalert(temp,'',true);
                    } else {
                      myalert('error:' + $.base64.decode(data.message));
                    }
                  }
                }
                
              });              
              
            }
          },
          {
            id: "dialog_obj_template_create_cancel",
            html: '<i class="fa fa-window-close"></i> '+gks_lang('Κλείσιμο'),
            click: function() {
              $(this).dialog('close');
            }
          },          

        ],
        
      });      
      //console.log(dialog_html);
    }
    
    dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
    if (dwidth> 600) dwidth=600;
    if (dheight> 400) dheight=400;
	  if (typeof from_php_gks_erp_app_mobile !== 'undefined' && from_php_gks_erp_app_mobile==1) {
	    dwidth=$(window).width();dheight=$(window).height();
	  }
	      
    dialog_obj_template_create.dialog('option', 'width', dwidth);
    dialog_obj_template_create.dialog('option', 'height', dheight);
    $('#dialog_obj_template_create').parent().css({position:'fixed'});      
    dialog_obj_template_create.dialog('open');
    $('#gks_header_search_results').hide();    
    
  }

  

  window.reservation_journal_id_fill = function (myelement,myelement_seira,company_id,company_sub_id,acc_journal_id) {  
    datasend = 'company_id=' + company_id + '&company_sub_id=' + company_sub_id + '&types=reservation';
    $('#' + myelement).val('0');
    $('#' + myelement + ' option').each(function() { 
      if ($(this).attr('value') >0 ) $(this).remove();
    });
    $('#' + myelement_seira).val('0');
    $('#' + myelement_seira + ' option').each(function() { 
      if ($(this).attr('value') >0 ) $(this).remove();
    });
    if (company_id<=0) return;
    $.ajax({
        url: "/my/admin-get-journal.php",
        type: 'POST',
        cache: false,
        dataType: "json",
        data:datasend,
        gks_myelement:myelement,
        gks_myelement_seira:myelement_seira,
        gks_acc_journal_id:acc_journal_id,
        processData: false,
        error : function(jqXHR ,textStatus,  errorThrown) {
          myalert('error:' + jqXHR.responseText);
        },
        success: function(data) {
          if (!data) {
            myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          } else {
            if (data.success == true) {
              if (data.out) {
                for (i = 0; i < data.out.length; i++) {
                  $('#' + this.gks_myelement).append('<option value="' + data.out[i].id + '" ' +
                  'data-eidi_id="' + data.out[i].eidi_id + '" ' +
                  'data-type_id="' + data.out[i].type_id + '" ' +
                  'data-need_prev="' + data.out[i].need_prev + '" ' +
                  'data-fpa="' + data.out[i].fpa + '" ' +
                  'data-need_afm="' + data.out[i].need_afm + '" ' +
                  'data-balance_pros="' + data.out[i].balance_pros + '" ' +
                  'data-whi_stock_pros="' + data.out[i].whi_stock_pros + '" ' +
                  'data-whi_type_id="' + data.out[i].whi_type_id + '" ' +
                  '>' + data.out[i].descr + '</option>');
                  if (this.gks_acc_journal_id > 0 && data.out[i].id == this.gks_acc_journal_id) {
                    $('#' + this.gks_myelement).val(this.gks_acc_journal_id);
                  }
                }
                if (data.out.length ==1) $('#' + this.gks_myelement).val(data.out[0].id);
                else if (this.gks_acc_journal_id == 0) $('#' + this.gks_myelement).val('0');
                reservation_journal_id_change();
              }
            } else {
              myalert('error:' + $.base64.decode(data.message));
            }
          }
        }
    });        
  }

  window.reservation_seira_id_fill = function (myelement,acc_journal_id,acc_seira_id) {  
    datasend = 'acc_journal_id=' + acc_journal_id;
    $('#' + myelement + ' option').each(function() { 
      if ($(this).attr('value') >0 ) $(this).remove();
    });    
    if (acc_journal_id<=0) return;
    $.ajax({
        url: "/my/admin-get-seira.php",
        type: 'POST',
        cache: false,
        dataType: "json",
        data:datasend,
        gks_myelement:myelement,
        gks_acc_seira_id:acc_seira_id,
        processData: false,
        error : function(jqXHR ,textStatus,  errorThrown) {
          myalert('error:' + jqXHR.responseText);
        },
        success: function(data) {
          if (!data) {
            myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          } else {
            if (data.success == true) {
              if (data.out) {
                for (i = 0; i < data.out.length; i++) {
                  $('#' + this.gks_myelement).append('<option value="' + data.out[i].id + '" data-is_xeirografi="' + data.out[i].is_xeirografi + '">' + data.out[i].descr + '</option>');
                  if (this.gks_acc_seira_id > 0 && data.out[i].id == this.gks_acc_seira_id) {
                    $('#' + this.gks_myelement).val(this.gks_acc_seira_id);
                  }
                }
                if (data.out.length ==1) $('#' + this.gks_myelement).val(data.out[0].id);
                else if (this.gks_acc_seira_id == 0) $('#' + this.gks_myelement).val('0');
                reservation_seira_id_change();
              }
            } else {
              myalert('error:' + $.base64.decode(data.message));
            }
          }
        }
    });        
  }
  
  window.transfer_reservation_journal_id_fill = function (myelement,myelement_seira,company_id,company_sub_id,acc_journal_id) {  
    datasend = 'company_id=' + company_id + '&company_sub_id=' + company_sub_id + '&types=transfer';
    $('#' + myelement).val('0');
    $('#' + myelement + ' option').each(function() { 
      if ($(this).attr('value') >0 ) $(this).remove();
    });
    $('#' + myelement_seira).val('0');
    $('#' + myelement_seira + ' option').each(function() { 
      if ($(this).attr('value') >0 ) $(this).remove();
    });
    if (company_id<=0) return;
    $.ajax({
        url: "/my/admin-get-journal.php",
        type: 'POST',
        cache: false,
        dataType: "json",
        data:datasend,
        gks_myelement:myelement,
        gks_myelement_seira:myelement_seira,
        gks_acc_journal_id:acc_journal_id,
        processData: false,
        error : function(jqXHR ,textStatus,  errorThrown) {
          myalert('error:' + jqXHR.responseText);
        },
        success: function(data) {
          if (!data) {
            myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          } else {
            if (data.success == true) {
              if (data.out) {
                for (i = 0; i < data.out.length; i++) {
                  $('#' + this.gks_myelement).append('<option value="' + data.out[i].id + '" ' +
                  'data-eidi_id="' + data.out[i].eidi_id + '" ' +
                  'data-type_id="' + data.out[i].type_id + '" ' +
                  'data-need_prev="' + data.out[i].need_prev + '" ' +
                  'data-fpa="' + data.out[i].fpa + '" ' +
                  'data-need_afm="' + data.out[i].need_afm + '" ' +
                  'data-balance_pros="' + data.out[i].balance_pros + '" ' +
                  'data-whi_stock_pros="' + data.out[i].whi_stock_pros + '" ' +
                  'data-whi_type_id="' + data.out[i].whi_type_id + '" ' +
                  '>' + data.out[i].descr + '</option>');
                  if (this.gks_acc_journal_id > 0 && data.out[i].id == this.gks_acc_journal_id) {
                    $('#' + this.gks_myelement).val(this.gks_acc_journal_id);
                  }
                }
                if (data.out.length ==1) $('#' + this.gks_myelement).val(data.out[0].id);
                else if (this.gks_acc_journal_id == 0) $('#' + this.gks_myelement).val('0');
                transfer_reservation_journal_id_change();
              }
            } else {
              myalert('error:' + $.base64.decode(data.message));
            }
          }
        }
    });        
  }

  window.transfer_reservation_seira_id_fill = function (myelement,acc_journal_id,acc_seira_id) {  
    datasend = 'acc_journal_id=' + acc_journal_id;
    $('#' + myelement + ' option').each(function() { 
      if ($(this).attr('value') >0 ) $(this).remove();
    });    
    if (acc_journal_id<=0) return;
    $.ajax({
        url: "/my/admin-get-seira.php",
        type: 'POST',
        cache: false,
        dataType: "json",
        data:datasend,
        gks_myelement:myelement,
        gks_acc_seira_id:acc_seira_id,
        processData: false,
        error : function(jqXHR ,textStatus,  errorThrown) {
          myalert('error:' + jqXHR.responseText);
        },
        success: function(data) {
          if (!data) {
            myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          } else {
            if (data.success == true) {
              if (data.out) {
                for (i = 0; i < data.out.length; i++) {
                  $('#' + this.gks_myelement).append('<option value="' + data.out[i].id + '" data-is_xeirografi="' + data.out[i].is_xeirografi + '">' + data.out[i].descr + '</option>');
                  if (this.gks_acc_seira_id > 0 && data.out[i].id == this.gks_acc_seira_id) {
                    $('#' + this.gks_myelement).val(this.gks_acc_seira_id);
                  }
                }
                if (data.out.length ==1) $('#' + this.gks_myelement).val(data.out[0].id);
                else if (this.gks_acc_seira_id == 0) $('#' + this.gks_myelement).val('0');
                transfer_reservation_seira_id_change();
              }
            } else {
              myalert('error:' + $.base64.decode(data.message));
            }
          }
        }
    });        
  }  
  
  window.gks_address_autocomplete = function (ma_odos,ma_arithmos,ma_orofos,ma_perioxi,ma_poli,ma_tk,ma_nomos_id,ma_country_id,ma_latitude,ma_longitude,enable_need_save, simple_input=false) {
    //console.log('gks_address_autocomplete');
    //if (from_php_user_settings_autocomplete_address=='' || from_php_user_settings_autocomplete_address=='none') return;
    //console.log('from_php_user_settings_autocomplete_address',from_php_user_settings_autocomplete_address);
    if (from_php_user_settings_autocomplete_address=='from_db') {
      
      $('#' + ma_odos).autocomplete({
        gks_params: [ma_odos,ma_arithmos,ma_orofos,ma_perioxi,ma_poli,ma_tk,ma_nomos_id,ma_country_id,ma_latitude,ma_longitude,enable_need_save,simple_input],
        source: function(request, response) {
          mydata={
            term: request.term,
          };
          $.ajax({
            url: 'admin-autocomplete-tk.php',
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
          
          gks_address_params = $(this).autocomplete('option', 'gks_params');
          //console.log(gks_address_params);
          if (gks_address_params[10]) need_save=true;
          if (gks_address_params[0]!='') $('#' + gks_address_params[0]).val(ui.item.odos);
          if (gks_address_params[1]!='') $('#' + gks_address_params[1]).val(ui.item.arithmos);
          if (gks_address_params[2]!='') $('#' + gks_address_params[2]).val(ui.item.orofos);
          if (gks_address_params[3]!='') $('#' + gks_address_params[3]).val(ui.item.perioxi);
          if (gks_address_params[4]!='') $('#' + gks_address_params[4]).val(ui.item.poli);
          if (gks_address_params[5]!='') $('#' + gks_address_params[5]).val(ui.item.tk);
          if (gks_address_params[7]!='') $('#' + gks_address_params[7]).val(ui.item.country_id);
          if (gks_address_params[6]!='') {
            nomos_fill(gks_address_params[6],ui.item.country_id,ui.item.nomos_id);
          }
          setTimeout(function(val1, val2) {
            $('#' + val2).val(val1);
          },300,ui.item.odos,gks_address_params[0]);
          
        },
        change: function (event, ui) {
          if(!ui.item){
            gks_address_params = $(this).autocomplete('option', 'gks_params');
            //console.log(gks_address_params);          
            if (gks_address_params[10]) need_save=true;
          }
        },
        create: function () {
          $(this).data('ui-autocomplete')._renderItem = function (ul, item) {
            return $('<li>')
              .append('<a class="gks_autocomplete_id">' + item.value + '</a>' + 
              '<span class="gks_autocomplete_text1">' + item.perioxi + '&nbsp;</span>' +
              '<span class="gks_autocomplete_text2">' + item.poli + ' </span>' +
              '<span class="gks_autocomplete_text3">' + item.tk + ' </span>' +
              '<span class="gks_autocomplete_text4">' + item.nomos_descr + ' </span>' +
              '<span class="gks_autocomplete_text5">' + item.country_name + ' </span>'
              
              )
              .appendTo(ul);
          };
        }, 
        open: function(event, ui) {
          var mymaxui_id=0;
          $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_id').each(function() {
            temp=$(this).outerWidth();
            if (temp>mymaxui_id) mymaxui_id=temp;
          });
          var mymaxui_text1=0;
          $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_text1').each(function() {
            temp=$(this).outerWidth();
            if (temp>mymaxui_text1) mymaxui_text1=temp;
          });
          var mymaxui_text2=0;
          $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_text2').each(function() {
            temp=$(this).outerWidth();
            if (temp>mymaxui_text2) mymaxui_text2=temp;
          });
          var mymaxui_text3=0;
          $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_text3').each(function() {
            temp=$(this).outerWidth();
            if (temp>mymaxui_text3) mymaxui_text3=temp;
          });
          var mymaxui_text4=0;
          $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_text4').each(function() {
            temp=$(this).outerWidth();
            if (temp>mymaxui_text4) mymaxui_text4=temp;
          });
          var mymaxui_text5=0;
          $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_text5').each(function() {
            temp=$(this).outerWidth();
            if (temp>mymaxui_text5) mymaxui_text5=temp;
          });
          
          
          mymaxui_id+=0;
          $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_id').each(function() {
            $(this).css({'min-width':mymaxui_id + 'px','display' : 'inline-block'});
          }); 
          mymaxui_text1+=0;
          $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_text1').each(function() {
            $(this).css({'min-width':mymaxui_text1 + 'px','display' : 'inline-block'});
          }); 
          mymaxui_text2+=0;
          $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_text2').each(function() {
            $(this).css({'min-width':mymaxui_text2 + 'px','display' : 'inline-block'});
          }); 
          mymaxui_text3+=0;
          $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_text3').each(function() {
            $(this).css({'min-width':mymaxui_text3 + 'px','display' : 'inline-block'});
          }); 
          mymaxui_text4+=0;
          $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_text4').each(function() {
            $(this).css({'min-width':mymaxui_text4 + 'px','display' : 'inline-block'});
          }); 
          
          mymaxui_text5+=0;
          $(this).data('ui-autocomplete').menu.element.find('li .gks_autocomplete_text5').each(function() {
            $(this).css({'min-width':mymaxui_text5 + 'px','display' : 'inline-block'});
          }); 
          
          mylast=mymaxui_id + mymaxui_text1 + mymaxui_text2 + mymaxui_text3 + mymaxui_text4 + mymaxui_text5 + 100;
          $(this).data('ui-autocomplete').menu.element.css('width',mylast+'px');
          //console.log(mymaxui_text1);
        },
        
      });    
      
      

    } else if (from_php_user_settings_autocomplete_address=='from_googlemaps' && document.getElementById(ma_odos) !== null) {

      item_to_load={
        ma_odos:ma_odos,
        ma_arithmos:ma_arithmos,
        ma_orofos:ma_orofos,
        ma_perioxi:ma_perioxi,
        ma_poli:ma_poli,
        ma_tk:ma_tk,
        ma_nomos_id:ma_nomos_id,
        ma_country_id:ma_country_id,
        ma_latitude:ma_latitude,
        ma_longitude:ma_longitude,
        enable_need_save: enable_need_save,
        simple_input: simple_input,
      };
      
      if (gks_map_js_load_done==false) {
        item_to_load.runfrom='async';
        gks_address_autocomplete_to_load.push(item_to_load);
      } else {
        item_to_load.runfrom='direct';
        gks_address_autocomplete_item_map(item_to_load);
      }
      
    }
   
    if (ma_poli!='') {
      $('#' + ma_poli).autocomplete({
        source: function(request, response) {
          mydata={
            term: request.term,
          };
          $.ajax({
            url: 'admin-autocomplete-tk-poli.php',
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
        minLength: 2,
        delay: 300,
      });        
    }

    if (ma_tk!='') {
      $('#' + ma_tk).autocomplete({
        source: function(request, response) {
          mydata={
            term: request.term,
          };
          $.ajax({
            url: 'admin-autocomplete-tk-zip.php',
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
        minLength: 1,
        delay: 300,
      });        
    }    
  }
  

  
  $('.recsperpageselectbox').change(function() {
    v=parseInt($(this).val());
    if (isNaN(v)) v=0;
    if (v<=0) return;
    newurl=$(this).attr('data-url');
    if (typeof newurl == 'undefined') return;
    
    
    window.location = 'set-recsperpage.php?num=' + v + '&url=' + newurl;

    
    
  });
  


  
  
  function sociallinks_set_aa() {
    var temp=0;
    $('.sociallinks_aa').each(function() {
      temp++;
      $(this).html(temp);
    });  
  } 
  
  function sociallinks_select_disable() {
    var sociallinks_type_id=[];
    $('#sociallinks_table .sociallinks_url').each(function() {
      data_type_id=parseInt($(this).attr('data-type_id')); if (isNaN(data_type_id)) data_type_id=0;
      if (data_type_id>0) sociallinks_type_id.push(data_type_id);
    });
    
    $('#sociallinks_select option').each(function() {
      var this_val=parseInt($(this).val().trim()); if (isNaN(this_val)) this_val=0;
      if (this_val>0) {
        if (sociallinks_type_id.includes(this_val)) {
          $(this).prop('disabled', true);
        } else {
          $(this).prop('disabled', false);
        }
      }
    });
    
    //console.log(sociallinks_type_id);
    var set_value=false;
    $('#sociallinks_select option').each(function() {
      if (set_value) return;
      var this_val=parseInt($(this).val().trim()); if (isNaN(this_val)) this_val=0;
      if (this_val>0) {
        if ($(this).prop('disabled')==false) {
          $('#sociallinks_select').val(this_val);
          set_value=true;
          return;  
        }
      }
    });
    if (set_value==false) $('#sociallinks_select').val('0');
  }
  
  function sociallinks_remove_click() {
    data_aa=parseInt($(this).attr('data-aa')); if (isNaN(data_aa)) data_aa=0;
    if (data_aa<=0) return;
    $('.sociallinks_tr[data-aa=' + data_aa + ']').remove();
    sociallinks_select_disable();
    sociallinks_set_aa();
  }

  function sociallinks_url_change() {
    data_aa=parseInt($(this).attr('data-aa')); if (isNaN(data_aa)) data_aa=0;
    if (data_aa<=0) return;
    myvalue=$(this).val().trim();
    if (myvalue=='') {
      myurl='#';
    } else {
      if (myvalue.toLowerCase().startsWith('http://') || myvalue.toLowerCase().startsWith('https://')) {
        myurl=myvalue;
      } else {
        myurl='https://' + myvalue;
      }
    }
    $('.sociallinks_tr[data-aa=' + data_aa + '] a').attr('href',myurl).attr('target',(myurl=='#' ? '_self' : '_blank'));
  }
  
 
  $('#sociallinks_add').click(function() {
    myvalue=parseInt($('#sociallinks_select').val()); if (isNaN(myvalue)) myvalue=0;
    if (myvalue<=0) {myalert('error:'+gks_lang('Επιλέξτε πρώτα το κοινωνικό δίκτυο')); return;}
    myoption= $('#sociallinks_select option[value=' + myvalue + ']');
    if (myoption.length!=1) return;
    
    myicon=$.base64.decode(myoption.attr('data-icon'));
    mytext=myoption.text();
    
    var aa=1;
    $('.sociallinks_url').each(function() {
      temp_aa=parseInt($(this).attr('data-aa')); if (isNaN(temp_aa)) temp_aa=0;
      if (temp_aa > aa) aa=temp_aa;
    }); 
    
    aa++;
    myhtml='<tr class="sociallinks_tr" data-aa="' + aa + '">' + 
      '<th scope="row" nowrap class="mytdcm sociallinks_aa">' + aa + '</td>' +     
      '<td nowrap class="mytdcm"><i class="fas fa-trash-alt sociallinks_remove" data-aa="' + aa + '"></i></td>' +
      '<td nowrap class="mytdcm">' +
        '<a href="#" title="' + mytext + '" target="_self">' +
        myicon +
        '</a>' +
      '</td>' +
      '<td nowrap class="mytdcm">' +
        '<input type="text" class="form-control form-control-sm myneedsave sociallinks_url" data-aa="' + aa + '" ' +
        'value="" ' +
        'data-type_id="' + myvalue + '">' +
      '</td>' +
    '</tr>';
    $('#sociallinks_tr_new').before(myhtml);
    $('.sociallinks_remove[data-aa=' + aa + ']').click(sociallinks_remove_click);
    $('.sociallinks_url[data-aa=' + aa + ']').on('change keyup paste',sociallinks_url_change);
    //console.log(myvalue,myicon,mytext);
    //console.log(myhtml);
    
    sociallinks_select_disable();
    sociallinks_set_aa();    
    

  });
 
  window.gks_sociallinks_input_collect = function () {  
    var sociallinks_items=[];
    $('.sociallinks_url').each(function() {
      url=$(this).val().trim();
      if (url!='') {
        type_id=parseInt($(this).attr('data-type_id'));if (isNaN(type_id)) type_id=0;
        if (type_id>0) sociallinks_items.push({id: type_id, url:url});
      }  
    });
    //console.log(sociallinks_items);
    return sociallinks_items;
  }
  
  sociallinks_select_disable();
  $('.sociallinks_remove').click(sociallinks_remove_click);
  $('.sociallinks_url').on('change keyup paste',sociallinks_url_change);
 

  var check_vies_valid_wait_timer=null;
  window.check_vies_valid_wait_timer_restart=function () {
    //console.log('check_vies_valid_wait_timer_restart');
    clearTimeout(check_vies_valid_wait_timer);      
    check_vies_valid_wait_timer=null;
    check_vies_valid_wait_timer=setTimeout(check_vies_valid_wait_timer_run, 5000);
  }
  window.check_vies_valid_wait_timer_stop=function () {
    //console.log('check_vies_valid_wait_timer_stop');
    clearTimeout(check_vies_valid_wait_timer);      
    check_vies_valid_wait_timer=null;
  }
  function check_vies_valid_wait_timer_run() {
    //console.log('check_vies_valid_wait_timer_run');
    clearTimeout(check_vies_valid_wait_timer);      
    check_vies_valid_wait_timer=null;
    
    datasend='';
    datasend+='&id=' + from_php_id;



//$mybasketarray['from']=='reservation' or 
//$mybasketarray['from']=='transfer_reservation'
          
    switch (from_php_activity_model) {
      case 'gks_orders':                datasend+='&from=order'; break;
      case 'gks_acc_inv':               datasend+='&from=acc_inv'; break;
      case 'gks_acc_pay':               datasend+='&from=acc_pay'; break;
      case 'gks_whi_mov':               datasend+='&from=whi_mov'; break;
      case 'gks_crm_leads':             datasend+='&from=crm_lead'; break;
      case 'gks_crm_tasks':             datasend+='&from=crm_task'; break;
      case 'gks_crm_machine':           datasend+='&from=crm_machine'; break;
      case 'gks_hotel_reservation':     datasend+='&from=reservation'; break;
      case 'gks_transfer_reservation':  datasend+='&from=transfer_reservation'; break;
      default:
        console.log('from_php_activity_model not set: '+from_php_activity_model);
    } 
    
    company_id=0;company_sub_id=0;
    if ($('#hotel_id').length==1) {
      v=$('#hotel_id option:selected').attr('data-company_id_sub_id');
      if (v === undefined || v === null) v='';
      parts=v.split('|');
      if (parts.length==2) {
        company_id=parseInt(parts[0]); if (isNaN(company_id)) company_id=0; 
        company_sub_id=parseInt(parts[1]); if (isNaN(company_sub_id)) company_sub_id=0;
      }
    } else if ($('#transfer_id').length==1) {
      v=$('#transfer_id option:selected').attr('data-company_id_sub_id');
      if (v === undefined || v === null) v='';
      parts=v.split('|');
      if (parts.length==2) {
        company_id=parseInt(parts[0]); if (isNaN(company_id)) company_id=0; 
        company_sub_id=parseInt(parts[1]); if (isNaN(company_sub_id)) company_sub_id=0;
      }
    } else if ($('#company_id_sub_id').length==1) {
      v=$('#company_id_sub_id').val();
      if (v === undefined || v === null) v='';
      parts=v.split('|');
      if (parts.length==2) {
        company_id=parseInt(parts[0]); if (isNaN(company_id)) company_id=0; 
        company_sub_id=parseInt(parts[1]); if (isNaN(company_sub_id)) company_sub_id=0;
      }
    } else if ($('#company_id').length==1) {
      company_id=$('#company_id').val();
      if ($('#company_sub_id').length==1) company_sub_id=$('#company_sub_id').val();
    }
    
    datasend+='&company_id=' + company_id;
    datasend+='&company_sub_id=' + company_sub_id;
    
    if ($('#user_id').length==1) datasend+='&user_id=' + $('#user_id').val();
    else if ($('#crm_machine_user_id').length==1) datasend+='&user_id=' + $('#crm_machine_user_id').val();
    
    

    if ($('#dr_user_afm').length == 1 && $('#dr_user_afm').prop("tagName")=='INPUT') datasend+='&afm=' + $('#dr_user_afm').val();
    else if ($('#dr_user_afm').length == 1) datasend+='&afm=' + $('#dr_user_afm').text();
    else if ($('#afm').length == 1 && $('#afm').prop("tagName")=='INPUT') datasend+='&afm=' + $('#afm').val();
    
    if ($('#dr_user_ma_country_id').length==1 && $('#dr_user_ma_country_id').prop("tagName")=='INPUT') datasend+='&ma_country_id=' + $('#dr_user_ma_country_id').val();
    else if ($('#dr_user_ma_country_id').length==1 && $('#dr_user_ma_country_id').prop("tagName")=='SELECT') datasend+='&ma_country_id=' + $('#dr_user_ma_country_id').val();
    else if ($('#dr_user_ma_country_id').length==1 && $('#dr_user_ma_country_id').prop("tagName")=="DIV") datasend+='&ma_country_id=' + $('#dr_user_ma_country_id').attr('data-id'); 
    else if ($('#dr_user_ma_country_id_h').length==1) datasend+='&ma_country_id=' + $('#dr_user_ma_country_id_h').val();
    else if ($('#country_id').length==1) datasend+='&ma_country_id=' + $('#country_id').val();

    if ($('#form_parastatiko_timologio').length==1) {
      datasend+='&parastatiko=' + $('input[name=form_parastatiko]:checked').val();
    } else {
      if (typeof(from_php_eidos_parastatikou_need_afm) != 'undefined') {
        datasend+='&parastatiko=' + from_php_eidos_parastatikou_need_afm;
      } else if (from_php_activity_model=='gks_acc_pay' || 
                 from_php_activity_model=='gks_crm_leads' || 
                 from_php_activity_model=='gks_crm_tasks' ||
                 from_php_activity_model=='gks_crm_machine') {
        datasend+='&parastatiko=1';
      } else {
        datasend+='&parastatiko=0';
      }
        
    }
    
    $.ajax({
      url: '/my/admin-gsis-timer-get.php?id=' + from_php_id,
      type: 'POST',
      cache: false,
      dataType: 'json',
      data: datasend,
      error : function(jqXHR ,textStatus,  errorThrown) {
        //console.log('error:' + jqXHR.responseText);
      },        
      success: function(data) {
        if (!data) {
          //console.log('error:'+gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
        } else {
          if (data.success == true) {
            //console.log(data);
            if (data.check_vies.valid==2) {
              check_vies_valid_wait_timer_restart();      
            } else {
              if (data.check_vies.views_run_img!='') {
                $('#dr_user_afm_views_run').html(data.check_vies.views_run_img).show();
                $('#dr_user_afm_views_run .tooltipster').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});
                
                $('#dr_user_afm_views_run_static').html(data.check_vies.views_run_img).css('visibility','visible');
                $('#dr_user_afm_views_run_static .tooltipster').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});

                
                //if (data.check_vies.valid==2) check_vies_valid_wait_timer_restart();
              } else {
                $('#dr_user_afm_views_run').hide();
              }        
            }      
              
            
          } else {
            //console.log('error:' + $.base64.decode(data.message));
          }
        }
      }
    });     
  }
  if (typeof(from_php_check_vies_valid_wait) != 'undefined' && from_php_check_vies_valid_wait) check_vies_valid_wait_timer_restart();
    
  
  
  
   window.messagesms_countchars = function (a) {
    var mystring = a.trim();
    var aa=mystring.length;

    cc1 = (mystring.match(/\^/g) || []).length;
    aa=aa+cc1;
    cc1 = (mystring.match(/\{/g) || []).length;
    aa=aa+cc1;
    cc1 = (mystring.match(/}/g) || []).length;
    aa=aa+cc1;
    cc1 = (mystring.match(/\[/g) || []).length;
    aa=aa+cc1;
    cc1 = (mystring.match(/]/g) || []).length;
    aa=aa+cc1;
    cc1 = (mystring.match(/~/g) || []).length;
    aa=aa+cc1;
    cc1 = (mystring.match(/\\/g) || []).length;
    aa=aa+cc1;
    cc1 = (mystring.match(/\|/g) || []).length;
    aa=aa+cc1;
    cc1 = (mystring.match(/€/g) || []).length;
    aa=aa+cc1; 
    return aa;     
    
    //             ^ { } [ ] ~ \ | €    
  }
  
  
  
  window.messagesms_change = function (elem_text, elem_chars) {
    var aa=messagesms_countchars( $('#' + elem_text).val() );

    if (aa>=0 && aa<=160) {
      smscc=1;
      leftcc=160-aa;
    } else if (aa<=306) {
      smscc=2;
      leftcc=306-aa;
    } else if (aa<=459) {
      smscc=3;
      leftcc=459-aa;
    } else if (aa<=612) {
      smscc=4;
      leftcc=612-aa;
    } else if (aa<=765) {
      smscc=5;
      leftcc=765-aa;
    } else if (aa<=918) {
      smscc=6;
      leftcc=918-aa;
    } else {
      smscc=0;
      leftcc=0;
    }
    aa=smscc + ' SMS, ' + leftcc + ' '+gks_lang('εναπομείναντες χαρακτήρες');
    $('#' + elem_chars).html(aa);    
  }
    
  
  window.gks_field_from_table_save = function(table_name,table_id,field_name,myvalue, showloading=false) {
    //console.log(table_name,table_id,field_name,myvalue);
    //return;
    if (showloading) $('body').addClass('myloading'); 
    
    if (isNaN(table_id)) table_id=0;
    datasend='';
    datasend+='&table_name=' + encodeURIComponent($.base64.encode(table_name));
    datasend+='&table_id=' + table_id;
    datasend+='&field_name=' + encodeURIComponent($.base64.encode(field_name));
    datasend+='&myvalue=' + encodeURIComponent($.base64.encode(myvalue));
    
    $.ajax({
      url: '/my/admin-field-from-table-save.php',
      type: 'POST',
      cache: false,
      dataType: 'json',
      data: datasend,
      gks_showloading:showloading,
      error : function(jqXHR ,textStatus,  errorThrown) {
        if (this.gks_showloading) $('body').removeClass('myloading');
        myalert('error:' + jqXHR.responseText);
      },
      success: function(data) {
        if (this.gks_showloading) $('body').removeClass('myloading');
        if (!data) {
          myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
        } else {
          if (data.success == true) {
            gks_field_from_table_save_after(data);
          } else {
            myalert('error:' + $.base64.decode(data.message));
          }
        }
      },
    });
  }
  
  window.gks_voip_originate_click=function() {
    myclass=$(this).attr('class');
    if (myclass=='gks_voip_originate_after_input') myclass2='gks_voip_originate_input';
    else if (myclass=='gks_voip_originate_after_span') myclass2='gks_voip_originate_span';
    else return;
    elem=$(this).parent().find('.'+myclass2);
    if (elem.length==0) return;
    temp='';
    if (myclass2=='gks_voip_originate_input') temp=elem.val().trim();
    else if (myclass2=='gks_voip_originate_span') temp=elem.text().trim();
    if (temp=='') return;
    myphone='';
    for(i=0; i<temp.length;i++) {
      if (['+','0','1','2','3','4','5','6','7','8','9'].includes(temp[i])) myphone+=temp[i];
      
    }
    if (myphone=='') return;

    voip_extension_def=gks_getCookie('voip_extension_def');
    if (voip_extension_def==null || voip_extension_def=='') {
      voip_extension_def='';
      if (from_php_gks_voip_params.extensions.length==1) voip_extension_def=from_php_gks_voip_params.extensions[0];
    }
    if (voip_extension_def=='') {
      myalert('error:' + 
        gks_lang('Δεν έχετε ορίσει το εσωτερικό τηλέφωνο από το οποίο θα γίνονται οι τηλεφωνικές κλήσεις')+'<br>'+ 
        gks_lang('Μεταβείτε στις ρυθμίσεις σας για το κάνετε')+':<br>'+
        '<a href="admin-user-settings.php" class="gks_link">'+ gks_lang('Οι Ρυθμίσεις μου') + '</a>');
      return;
    }  
    datasend='id=' + from_php_gks_voip_params.id_erp_app;
    datasend+='&cmd=' + encodeURIComponent($.base64.encode('run_command_voipaimoriginatecall')); 
    datasend+='&extension=' + encodeURIComponent($.base64.encode(voip_extension_def)); 
    datasend+='&phone=' + encodeURIComponent($.base64.encode(myphone)); 
    
    //console.log('originate call', myclass, voip_extension_def, myphone);
    
    $.ajax({
			url: '/my/admin-erp-app-item-run-command.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#' + this.gks_item_id).removeClass('fa-hourglass').addClass('fa-arrow-circle-right').css('color','green');
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
			  $('#' + this.gks_item_id).removeClass('fa-hourglass').addClass('fa-arrow-circle-right').css('color','green');
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
  					console.log('run_command_voipaimoriginatecall');
  					console.log($.base64.decode(data.html));
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});
    
  }
  $('.gks_voip_originate_after_input, .gks_voip_originate_after_span').click(gks_voip_originate_click);
  
  // tinymce
  // Prevent jQuery UI dialog from blocking focusin
  $(document).on('focusin', function(e) {
    if ($(e.target).closest(".tox-tinymce, .tox-tinymce-aux, .moxman-window, .tam-assetmanager-root").length) {
      e.stopImmediatePropagation();
    }
  });
  

  if (typeof(from_php_dialog_object_rel_curr)!='undefined' && typeof(from_php_id)!='undefined') {
    if (from_php_id>0) {
      //console.log(from_php_dialog_object_rel_curr,from_php_id);
      temp_ctid=0;
      if (typeof(from_php_ctid)!='undefined') temp_ctid=from_php_ctid;
      window.gks_tabs_registry_obj = new gks_tabs_registry();
      gks_tabs_registry_obj.setEntity(from_php_dialog_object_rel_curr,from_php_id,temp_ctid);
      //gks_tabs_registry_obj.ready((tabs) => {
        //console.log(tabs);
      //});
    }
  }
  
});

   