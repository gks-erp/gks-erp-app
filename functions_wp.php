<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_mybasketarray_create(&$mybasketarray) {
  
  if (isset($mybasketarray) and is_array($mybasketarray)) return;

  $mybasketarray=array();
  
  $mybasketarray['from'] = 'basket';
  $mybasketarray['id_object']=0;
  $mybasketarray['user'] = array();
  $mybasketarray['user']['user_id'] = 0;
  $mybasketarray['user']['first_name'] = '';
  $mybasketarray['user']['last_name'] = '';
  $mybasketarray['user']['email'] = '';
  $mybasketarray['user']['mobile'] = '';
  $mybasketarray['user']['lang'] = '';
  $mybasketarray['user']['ma_odos'] = '';
  $mybasketarray['user']['ma_arithmos'] = '';
  $mybasketarray['user']['ma_orofos'] = '';
  $mybasketarray['user']['ma_perioxi'] = '';
  $mybasketarray['user']['ma_poli'] = '';
  $mybasketarray['user']['ma_tk'] = '';
  $mybasketarray['user']['ma_country_id'] = 0;
  $mybasketarray['user']['ma_nomos_id'] = 0;
  
  $mybasketarray['user']['eponimia'] = '';
  $mybasketarray['user']['title'] = '';
  $mybasketarray['user']['afm'] = '';
  $mybasketarray['user']['doy'] = '';
  $mybasketarray['user']['epaggelma'] = '';
  
  $mybasketarray['user']['manual_version'] = '';
  $mybasketarray['user']['manual_version_download'] = '';
  
  
  
  $mybasketarray['user_other'] = array();
  $mybasketarray['user_other']['first_name'] = '';
  $mybasketarray['user_other']['last_name'] = '';
  $mybasketarray['user_other']['email'] = '';
  $mybasketarray['user_other']['mobile'] = '';
  $mybasketarray['user_other']['lang'] = '';
  $mybasketarray['user_other']['ma_odos'] = '';
  $mybasketarray['user_other']['ma_arithmos'] = '';
  $mybasketarray['user_other']['ma_orofos'] = '';
  $mybasketarray['user_other']['ma_perioxi'] = '';
  $mybasketarray['user_other']['ma_poli'] = '';
  $mybasketarray['user_other']['ma_tk'] = '';
  $mybasketarray['user_other']['ma_country_id'] = 0;
  $mybasketarray['user_other']['ma_nomos_id'] = 0;
  
  
  
  $mybasketarray['products']=array();
  $mybasketarray['tropoi_apostolis_all']=array();
  $mybasketarray['tropoi_pliromis_all']=array();
  $mybasketarray['products_posotita']=0;
  $mybasketarray['products_varos']=0;                  //se grammaria
  $mybasketarray['products_ogos']=0;                   //se cm^3  
  $mybasketarray['products_ogos_max_x']=0;             //se cm 
  $mybasketarray['products_ogos_max_y']=0;             //se cm 
  $mybasketarray['products_ogos_max_z']=0;             //se cm
  $mybasketarray['products_original_netvalue']=0;
  $mybasketarray['products_netvalue']=0;
  $mybasketarray['products_fpa']=0;
  $mybasketarray['products_total']=0;
  $mybasketarray['products_need_apostoli']=true;
  $mybasketarray['products_need_pliromi']=true;
  $mybasketarray['kostos_apostolis']=0;
  $mybasketarray['tropos_apostolis']=0;
  $mybasketarray['kostos_pliromis']=0;
  $mybasketarray['tropos_pliromis']=0;
  $mybasketarray['coupons']=array();
  $mybasketarray['address_extra']=-1;
//  $mybasketarray['address_extra_data']=array();
//  $mybasketarray['address_extra_data']['ea_name'] = '';
//  $mybasketarray['address_extra_data']['ea_phone'] = '';
//  $mybasketarray['address_extra_data']['ea_odos'] = '';
//  $mybasketarray['address_extra_data']['ea_orofos'] = '';
//  $mybasketarray['address_extra_data']['ea_perioxi'] = '';
//  $mybasketarray['address_extra_data']['ea_poli'] = '';
//  $mybasketarray['address_extra_data']['ea_tk'] = '';
//  $mybasketarray['address_extra_data']['ea_country_id'] =  0;
//  $mybasketarray['address_extra_data']['ea_nomos_id'] =  0;
  
  
  
  
  $mybasketarray['destination_data']=array();
  $mybasketarray['destination_data']['name'] = '';
  $mybasketarray['destination_data']['phone'] = '';
  $mybasketarray['destination_data']['odos'] = '';
  $mybasketarray['destination_data']['arithmos'] = '';
  $mybasketarray['destination_data']['orofos'] = '';
  $mybasketarray['destination_data']['perioxi'] = '';
  $mybasketarray['destination_data']['poli'] = '';
  $mybasketarray['destination_data']['tk'] = '';
  $mybasketarray['destination_data']['country_id'] = 0;
  $mybasketarray['destination_data']['nomos_id'] = 0;

  
  
  $mybasketarray['parastatiko']=0;
  $mybasketarray['fiscal_position']=1;
  $mybasketarray['pricelist_id']=1;
  $mybasketarray['price_is_xondriki']=0;
  
  $mybasketarray['payment']=array();
  $mybasketarray['payment']['id_payment']=0;
  $mybasketarray['payment']['table_name']='';
  $mybasketarray['payment']['table_id']=0;

  $mybasketarray['hotel']['reservation'] = array();
  $mybasketarray['hotel']['reservation']['show_customer_more']=0;
  $mybasketarray['hotel']['reservation']['basket']=array();
  $mybasketarray['hotel']['reservation']['basket']['parastatiko']=0;
  $mybasketarray['hotel']['reservation']['basket']['reservation_other']=0;
  $mybasketarray['hotel']['reservation']['basket']['reservations']=array();
  $mybasketarray['hotel']['reservation']['basket']['extra']=array();

  $mybasketarray['transfer']['reservation'] = array();
  $mybasketarray['transfer']['reservation']['show_customer_more']=0;
  $mybasketarray['transfer']['reservation']['basket']=array();
  $mybasketarray['transfer']['reservation']['basket']['parastatiko']=0;
  $mybasketarray['transfer']['reservation']['basket']['reservation_other']=0;
  $mybasketarray['transfer']['reservation']['basket']['reservations']=array();
  $mybasketarray['transfer']['reservation']['basket']['extra']=array();


  $mybasketarray['check_vies'] = array();
  $mybasketarray['check_vies']['run'] = false;
  $mybasketarray['check_vies']['valid'] = 0;
  $mybasketarray['check_vies']['error'] = '';
  $mybasketarray['check_vies']['function'] = '';
   

}


function myCurrencyFormat($value, $show_currency_symbol = true, $and_decimal = true) {
  global $GKS_NUMBER_FORMAT_DECIMAL;
  global $GKS_NUMBER_FORMAT_THOUSAND;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL;
  global $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW;
  if ($and_decimal) {
    $ret = number_format(floatval($value), $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL, $GKS_NUMBER_FORMAT_DECIMAL, $GKS_NUMBER_FORMAT_THOUSAND);
  } else {
    $ret = number_format(floatval($value), 0, $GKS_NUMBER_FORMAT_DECIMAL, $GKS_NUMBER_FORMAT_THOUSAND);
  }
  if ($show_currency_symbol) {
    if ($GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW == 'after') 
      $ret.=''.$GKS_NUMBER_FORMAT_CURRENCY_SYMBOL;
    else
      $ret=$GKS_NUMBER_FORMAT_CURRENCY_SYMBOL.''.$ret;
  } 
  return $ret;
}
function myNumberFormat($value,$decimal=0) {
  global $GKS_NUMBER_FORMAT_DECIMAL;
  global $GKS_NUMBER_FORMAT_THOUSAND;
  $ret = number_format(floatval($value), $decimal, $GKS_NUMBER_FORMAT_DECIMAL, $GKS_NUMBER_FORMAT_THOUSAND);
  return $ret;
}

//decimal always . for input number type
function myNumberFormatNo0($value, $zero_to_empty=false) { 
  $ret = number_format(floatval($value), 10, '.', '');
  for ($i=1;$i<=11;$i++) {
    if (endwith($ret,'.')) {
      $ret=substr($ret, 0, strlen($ret)-1);
      break;
    } 
    if (endwith($ret,'0')) $ret=substr($ret, 0, strlen($ret)-1);
    else break;
  }
  if ($zero_to_empty and $ret=='0') $ret='';
  return $ret;
}
function myNumberFormatNo0Local($value, $zero_to_empty=false) { 
  global $GKS_NUMBER_FORMAT_DECIMAL;
  global $GKS_NUMBER_FORMAT_THOUSAND;
  
  $ret = number_format(floatval($value), 10, $GKS_NUMBER_FORMAT_DECIMAL, $GKS_NUMBER_FORMAT_THOUSAND);
  for ($i=1;$i<=11;$i++) {
    if (endwith($ret,$GKS_NUMBER_FORMAT_DECIMAL)) {
      $ret=substr($ret, 0, strlen($ret)-1);
      break;
    } 
    if (endwith($ret,'0')) $ret=substr($ret, 0, strlen($ret)-1);
    else break;
  }
  if ($zero_to_empty and $ret=='0') $ret='';
  return $ret;
}


function myParseCurrency($value) {
  global $GKS_NUMBER_FORMAT_DECIMAL;
  global $GKS_NUMBER_FORMAT_THOUSAND;
  global $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL;
  if ($value=='')  return 0;
  $value = str_replace($GKS_NUMBER_FORMAT_CURRENCY_SYMBOL, '', $value);
  $value = str_replace($GKS_NUMBER_FORMAT_THOUSAND, '', $value);
  $value = str_replace($GKS_NUMBER_FORMAT_DECIMAL, '.', $value);
  $value = floatval($value);
  return $value;
}
function myParseNumber($value) {
  global $GKS_NUMBER_FORMAT_DECIMAL;
  global $GKS_NUMBER_FORMAT_THOUSAND;
  if ($value=='')  return 0;
  $value = str_replace($GKS_NUMBER_FORMAT_THOUSAND, '', $value);
  $value = str_replace($GKS_NUMBER_FORMAT_DECIMAL, '.', $value);
  $value = floatval($value);
  return $value;
}

function myDateFormat($value) {
  global $GKS_NUMBER_FORMAT_DATE;
  return date($GKS_NUMBER_FORMAT_DATE, $value);
}
function myDateTimeFormat($value) {
  global $GKS_NUMBER_FORMAT_DATE;
  global $GKS_NUMBER_FORMAT_TIME;
  return date($GKS_NUMBER_FORMAT_DATE.' '.$GKS_NUMBER_FORMAT_TIME, $value);
}
function myDateFormatw($value,$load_lang='') {
  global $GKS_NUMBER_FORMAT_DATE;
  return mb_substr(getWeekDayName(date('w',$value),$load_lang),0,2) . ' '. date($GKS_NUMBER_FORMAT_DATE, $value);
}
function myDateTimeFormatw($value,$load_lang='') {
  global $GKS_NUMBER_FORMAT_DATE;
  global $GKS_NUMBER_FORMAT_TIME;
  if (empty($value)) return '';
  if (intval($value)==0) return '';
  return mb_substr(getWeekDayName(date('w',$value),$load_lang),0,2) . ' '. date($GKS_NUMBER_FORMAT_DATE.' '.$GKS_NUMBER_FORMAT_TIME, $value);
}

function myDateTimeFormatText($value,$load_lang='') {
  global $GKS_NUMBER_FORMAT_DATE;
  global $GKS_NUMBER_FORMAT_TIME;
  return date($GKS_NUMBER_FORMAT_TIME, $value).', '.getWeekDayName(date('w',$value),$load_lang) . ', '. date('d', $value).' '.getMonthName(date('m',$value),$load_lang).' '.date('Y',$value);
}




function is_global_admin() {
  global $my_wp_user_info;
  if (isset($my_wp_user_info->roles)) {
    foreach ($my_wp_user_info->roles as $role) {
      if (trim_gks(strtolower($role)) == 'administrator') {
        return true;
      }
      if (trim_gks(strtolower($role)) == 'adminmy') {
        return true;
      }
    }
  }
  return false;
}


function _time_user($mytime, $mode) {
  $mytimezone='Europe/Athens';
  $mytime_string = date('Y-m-d H:i:s', $mytime);

  if ($mode > 0) {
    // GMT -> user
    $dateTime = new DateTime($mytime_string);
    $dateTime->setTimezone(new DateTimeZone($mytimezone));
    return strtotime($dateTime->format('Y-m-d H:i:s'));
  } else if ($mode < 0) {
    //user -> GMT
    $dateTime = new DateTime($mytime_string, new DateTimeZone($mytimezone));
    $dateTime->setTimezone(new DateTimeZone('UTC'));
    return strtotime($dateTime->format('Y-m-d H:i:s'));
  } else {
    return $mytime;
  }
}

function _date_user($mytime, $mode) {
	return strtotime(date('Y-m-d', _time_user($mytime, $mode)));
}

//echo showDate(strtotime('2016-05-10'),'Y-m-d H:i:s',-1);
function showDate($time, $format, $mode) {
	if (!isset($time)) {
	    return '';
	}
	$time = _time_user($time, $mode);
	return date($format, $time);
}

function showDateDT($time, $format, $mode) {
	if (!isset($time)) {
	    return '';
	}
	//$time = _time_user($time, $mode);
  $SECOND = 1;
  $MINUTE = 60 * $SECOND;
  $HOUR = 60 * $MINUTE;
  $DAY = 24 * $HOUR;
  $MONTH = 30 * $DAY;

	
  $delta = time() - $time;
  
  $out='';
  if ($delta>0) {
    if ($delta < 1 * $MINUTE) {
        if ($delta<=1) return gks_lang('Ένα δευτ. πριν');
        $out= str_replace('[1]',$delta,gks_lang('Πριν από [1] δευτ.'));
    }
    if ($delta < 2 * $MINUTE)
        $out= gks_lang('Πριν ένα λεπτό');
  
    if ($delta < 60 * $MINUTE)
        $out= str_replace('[1]',intval($delta/60),gks_lang('Πριν από [1] λεπτά'));
  
    if ($delta < 2*60 * $MINUTE)
        $out= str_replace('[1]',date('H:i', time() - $thistime),gks_lang('Πριν από [1]'));
  
    if ($delta < 24 * $HOUR)
        $out= str_replace('[1]',showDate($thistime, $format, 1),gks_lang('Στις [1]'));
        

  } else {
    $delta=abs($delta);
    if ($delta < 1 * $MINUTE) {
        if ($delta<=1) $out= gks_lang('Σε ένα δευτ.');
        else $out= str_replace('[1]',$delta,gks_lang('Σε [1] δευτ.'));
    }
    if ($delta < 2 * $MINUTE)
        $out= gks_lang('Σε ένα λεπτό');
  
    if ($delta < 60 * $MINUTE)
        $out=  str_replace('[1]',intval($delta/60),gks_lang('Σε [1] λεπτά'));
  
    if ($delta < 2*60 * $MINUTE)
        $out= str_replace('[1]',date('H:i', time() - $thistime),gks_lang('Μετά από [1]'));
  
    if ($delta < 24 * $HOUR)
        $out= str_replace('[1]',showDate($thistime, $format, 1),gks_lang('Στις [1]'));
        
      
    
  }	
	
	return date($format, $time);
}

function gks_myFormatDate($date) {
  $v1=explode('/',$date);
  if (count($v1)!=3) return '';
  $v1=$v1[2].'-'.$v1[1].'-'.$v1[0];
  $v1=strtotime($v1);
  return $v1;
}
function gks_myFormatTime($time) {
  $v1=explode(':',$time);
  if (count($v1)==2) {
    $v1=$v1[0].':'.$v1[1].':00';
  } else if (count($v1)==3) {
    $v1=$v1[0].':'.$v1[1].':'.$v1[2];
  } else {
    return '';  
  }
  $v1=strtotime($v1);
  return $v1;
}
function gks_myFormatDurationTime($seconds) {
  $temp1=date('j:G:i',$seconds);
  $temp1=explode(':',$temp1);
  $temp1[0]=intval($temp1[0]); //day
  $temp1[1]=intval($temp1[1]); //hour
  $temp1[2]=intval($temp1[2]); //minute
  if ($temp1[0]>1) $temp1[1]+=24*($temp1[0]-1);
  $temp2=($temp1[1]>=10 ? $temp1[1] : '0'.$temp1[1]).':'.($temp1[2]>=10 ? $temp1[2] : '0'.$temp1[2]);

  return $temp2;
}


function gks_parse_date($mystring, &$mytime=0) {
  $mystring=trim_gks($mystring); $mytime=0;
  if ($mystring=='') return '';
  
  $parts=explode(' ',$mystring);
  if (count($parts)==2) {
    $parts_date=explode('/',$parts[0]);
    $parts_time=explode(':',$parts[1]);
    if (count($parts_date)==3 and count($parts_time)==2) {
      $temp=$parts_date[2].'-'.$parts_date[1].'-'.$parts_date[0].' '.$parts_time[0].':'.$parts_time[1].':00';
      
      $mytime=_time_user(strtotime($temp),-1);
      
      return date('Y-m-d H:i:s',$mytime);
    }
  }  
  return '';
}
function mystrtodb($string, $mymode =-1) {
  //'31/12/2016 11:00' -> 1483174800 -> 31/12/2016 09:00
  $date = date_create_from_format('d/m/Y H:i',$string );
  $date = date_timestamp_get($date);
  if ($mymode !=0) {
  	$date = date('Y-m-d H:i:s', _time_user($date,$mymode));
	} else {
		$date = date('Y-m-d H:i:s', $date);
	}
  return $date;
}
function mystrtodb_s($string) {
  $date = date_create_from_format('d/m/Y H:i:s',$string );
  $date = date_timestamp_get($date);
  $date = date('Y-m-d', $date);
  return $date;
}
function mystrtodb_st($string) {
  $date = date_create_from_format('d/m/Y H:i:s',$string );
  $date = date_timestamp_get($date);
  $date = date('Y-m-d H:i:s', $date);
  return $date;
}

function user_server_curdate() {
    $today = date('Y-m-d', _time_user(time(), 1));
    $today = strtotime($today);
    $today = _time_user($today, -1);
    $today = date('Y-m-d H:i:s', $today);
    return $today;
}

function user_server_curdate_plus($add_days=1) {
    $today = date('Y-m-d', _time_user(time()+$add_days*24*60*60, 1));
    $today = strtotime($today);
    $today = _time_user($today, -1);
    $today = date('Y-m-d H:i:s', $today);
    return $today;
}

function get_file_type($file_ext) {
  if ($file_ext == 'jpg' || $file_ext == 'png' || $file_ext == 'tif' || $file_ext == 'tiff') {
    return 1;
  } else if ($file_ext == 'mp4' || $file_ext == 'avi') {
    return 2;
  } else if ($file_ext == 'mp3' || $file_ext == 'wav') {
    return 3;
  } else if ($file_ext == 'eps' || $file_ext == 'pdf') {
    return 4;
  } else {
    return 0; 
  }
}




function doOutputList_ul($TreeArray, $deep=0)
{
    $padding = str_repeat('  ', $deep*3);
    $myret='';
    $myret.= $padding . "<ul>\n";
    foreach($TreeArray as $key => $arr)
    {
        $myret.= $padding . "  <li>\n";
        if(is_array($arr)) 
        {
                $myret.=doOutputList_ul($arr, $deep+1);
        }
        else
        {
                $myret.= $padding .'    '.$key.': '. $arr;
        }
        $myret.= $padding . "  </li>\n";
    }
    $myret.= $padding . "</ul>\n";
  
    return $myret;
}
function doOutputList($TreeArray)
{
    $myret='';
    $myret.= '<ul>';
    foreach($TreeArray as $key => $arr)
    {
        $myret.= '<li>';
        if(is_array($arr)) 
        {
                $myret.=doOutputList($arr);
        }
        else
        {
                $myret.= $key.': '.$arr;
        }
        $myret.= '</li>';
    }
    $myret.= '</ul>';
    return $myret;
}
function doOutputList_text($TreeArray, $deep=0)
{
  $padding = str_repeat('  ', $deep*3);
  $myret='';
  //$myret.= $padding . "<span>";
  foreach($TreeArray as $key => $arr)
  {
    //$myret.= $padding . "  <span>";
    if(is_array($arr)) 
    {
      $myret.='<span style="margin-left:'.(5 + $deep*15).'px;">'.$key.':</span><br>';
      $myret.=doOutputList_text($arr, $deep+1);
    }
    else
    {
      $myret.='<span style="margin-left:'.(5 + $deep*15).'px;">'.$key.': '. $arr.'</span><br>';
    }
    //$myret.= $padding . "  </span><br>";
  }
  //$myret.= $padding . "</span><br>";

  return $myret;
}


function stat_record() {
  global $db_link;
  global $my_wp_user_id;
  global $my_wp_user_info;
  global $my_page_title;
  global $_gks_id_session;
  
  $my_page_title_db=$my_page_title.'';
  

  $mystat_host = $db_link->escape_string($_SERVER["HTTP_HOST"]);
  $qry_string = $db_link->escape_string(urldecode($_SERVER['QUERY_STRING']));
  
  
  $mystat_sn = $db_link->escape_string(urldecode($_SERVER['SCRIPT_NAME']));
  $gkIP = $db_link->escape_string($_SERVER['REMOTE_ADDR']);
//  $uvisitor = $db_link->escape_string($_SERVER['REMOTE_ADDR']);
//  $utime = time();
//  $exptime = $utime - 3600; // (1 hour in seconds)
  
  
//  $sql = "delete from gks_stat_online where timevisit < ".$exptime;
//  $res = $db_link->query($sql);
//  if (!$res) debug_mail(false,'stat online error sql',$sql);
  
  $msessionid = $_gks_id_session;
  
  
  $uumyusername = '';
  if (isset($my_wp_user_info->data->display_name)) $uumyusername= $db_link->escape_string($my_wp_user_info->data->display_name);
  
  
  
//  $sql = "select id from gks_stat_online where visitor='".$uvisitor."' and session='".$msessionid."'";
//  $res = $db_link->query($sql);
//  if (!$res) debug_mail(false,'stat online error sql',$sql);
//  $uexists = $res->num_rows;
//  
//  
//  if ($uexists > 0) {
//    $sql="update gks_stat_online set pagetitle='".$db_link->escape_string($my_page_title_db)."', timevisit='$utime' , username='" . $uumyusername . "', lasturl='" . $mystat_sn . "', query_string='" . $qry_string . "',host='" . $mystat_host . "' where visitor='$uvisitor' and session='$msessionid'";
//    $res = $db_link->query($sql);
//    if (!$res) debug_mail(false,'stat online error sql',$sql);
//  } else {
//    $sql="insert into gks_stat_online (pagetitle,visitor,session,timevisit,username,lasturl,`query_string`,host) values ('".$db_link->escape_string($my_page_title_db)."','$uvisitor','$msessionid','$utime','" . $uumyusername . "','" . $mystat_sn . "','" . $qry_string . "','" . $mystat_host . "')";
//    $res = $db_link->query($sql);
//    if (!$res) debug_mail(false,'stat online error sql',$sql);
//  }
      
  
  $msessionid = $_gks_id_session;
  //$stat_sql = "insert into gks_stat_stat (pagetitle,userid,sessionid,username,ip,timevisit,pageurl,query_string,host, userAgent,referer) values (
  $stat_sql="('".$db_link->escape_string($my_page_title_db)."',";
  
  
  if (isset($my_wp_user_id)) {
      $stat_sql.=$my_wp_user_id . ",";
  } else {
      $stat_sql.="0,";
  }
  $stat_sql.="'" . $msessionid . "',";
  if (isset($uumyusername)) {
      $stat_sql.="'" . $db_link->escape_string($uumyusername) . "',";
  } else {
      $stat_sql.="'',";
  }
  
  $stat_sql.="'" . $gkIP . "',";
  $stat_sql.="'".date('Y-m-d H:i:s')."',";
  $stat_sql.="'" . $mystat_sn . "',";
  $stat_sql.="'" . $qry_string . "',";
  $stat_sql.="'" . $mystat_host . "', ";
  
  $user_agent = '';
  if (isset($_SERVER['HTTP_USER_AGENT'])) {
      $user_agent = $_SERVER['HTTP_USER_AGENT'];
      $user_agent = mb_substr($user_agent,0,255);
  }
  $stat_sql.="'" . $db_link->escape_string(urldecode($user_agent)) . "',";
  
  $myreferer = '';
  if (isset($_SERVER['HTTP_REFERER'])) {
      $myreferer = trim_gks(rawurldecode($_SERVER['HTTP_REFERER']));
  }
  $stat_sql.="'" . $db_link->escape_string(urldecode($myreferer)) . "')";
  
  $stat_sql_in="insert into gks_stat_queue (myvalues) values ('".$db_link->escape_string($stat_sql)."')";
  
  $res = $db_link->query($stat_sql_in);        
  if (!$res) debug_mail(false,'stat online error stat_sql',$stat_sql_in);

}
    
function nslookup($ip) {
  global $db_link;
  //$host = split('Name:',`nslookup $ip`);
  //return ( trim (isset($host[1]) ? str_replace ("\n".'Address:  '.$ip, '', $host[1]) : $ip));
  //return gethostbyaddr($ip);
  $ret=array(
    'ip'=>'',
    'isbot'=>0,
    'dns_name'=>'',
    'country_initials' =>'',
    'country_name' => '',
  );
  
  $query = "SELECT gks_stat_ips.ip, gks_stat_ips.isbot, gks_stat_ips.dns_name, gks_stat_ips.country_initials, gks_country.country_name
  FROM gks_stat_ips LEFT JOIN gks_country ON gks_stat_ips.country_initials = gks_country.country_initials
  where gks_stat_ips.ip='" . $ip . "'";
  
  $result = $db_link->query($query);        
  if (!$result) {debug_mail(false,'stat nslookup error stat_sql',$query); die('sql error');}

  if ($result->num_rows > 0) {
    $line = $result->fetch_assoc();
    if (isset($line['dns_name'])==false or $line['dns_name']=='') $line['dns_name']=$line['ip'];
    return $line;
  } 
  return $ret;
}    
   
function gks_stat_country_icon($a) {
  $country_initials=trim_gks($a['country_initials']);
  $country_name=trim_gks($a['country_name']);
  
  if ($country_initials=='') {
    $flag_file='/my/img/flags/flags_iso/16/0.png';
  } else if ($country_initials=='-') {
    $flag_file='/my/img/county_-.png';
  } else if ($country_initials=='--') {
    $flag_file='/my/img/county_--.png';
  } else {
    $flag_file='/my/img/flags/flags_iso/16/'.strtolower($country_initials).'.png';
  }
  
  return '<img src="'.$flag_file.'" border="0" title="'.$country_name.'">';
  
  
}










function makeFilters($filters, &$filter, $applied, $show_filter_button = true, $show_search_string = true, $search_string_value='') {
  global $db_link;
  global $gkIP;
  global $autocomplete_gks_disable;
  
  $filter = array('html' => '', 'sql' => '', 'url' => '', 'script' => '');

  //$titles = '<table width="100%" cellspacing="0" cellpadding="2" border="0" class="divfiltertablemain"><tr><td width="0%" style="padding-bottom: 4px;">';
  
  $ffff=gks_filters_body();
  if ($ffff===false or $ffff===1) {
    $ffff_class='divfiltertablemain_expand';
    $ffff_class2='divfiltertablemain_button_rotate';
  } else {
    $ffff_class='';
    $ffff_class2='';
  }
  //echo '<pre>'.var_dump($ffff);die();
  
  $titles = '<div class="divfiltertablemain '.$ffff_class.'"><div class="divfiltertablemain_button_div"><i class="fas fa-angle-double-down divfiltertablemain_button '.$ffff_class2.'"></i></div><div class="divfiltertablemain_content">';
  
  $button = '<img src="img/filter.png" border="0">';
  $titles .= '<div class="divfilter divfilterimg"><table width="100%" cellspacing="0" cellpadding="2" border="0" class="divfiltertableimg"><tr><td width="100%">'.gks_lang('Φίλτρα').':</td></tr><tr><td width="100%">' . $button . '</td>
  </tr></table></div>';


  //$boxes = '<tr>';

  $extraFields = array();
  foreach ($filters as $f) {
    $class = isset($f['has_custom_date']) ? 'has-custom-date' : '';

    $titles .= '<div class="divfilter"><table width="100%" cellspacing="0" cellpadding="2" border="0" class="divfiltertable"><tr><td width="0%" nowrap="nowrap" class="divfiltertd1">' . $f['title'] . '</td></tr><tr><td width="0%" nowrap="nowrap" class="divfiltertd2">';

    $styleifall=false;
    $is_custom_date_html='';

    $custom_default = false;
    if (isset($f['has_custom_default'])) {
      $custom_default = true;
      if (!isset($applied[$f['name']])) {
        $applied[$f['name']] = $f['has_custom_default'];
      }
    }
    
		$html='';
		$is_multiselect=false;
		$is_multiselect_vals=array();
		if (isset($f['multiselect']) and $f['multiselect'] == true) {
			
			$is_multiselect = true;
		  $is_multiselect_vals=explode(',', $applied[$f['name']]);
		  

      //if (!isset($applied[$f['name'].'_inhi'])) {
      //  $applied[$f['name']] = $f['has_custom_default'];
      //} else {
      //	$applied[$f['name']] =$applied[$f['name'].'_inhi'];
      //}
					
			
			$html.= "<input type='hidden' name='".$f['name']."' id='".$f['name']."' value='".$applied[$f['name']]."'/>";
			$filter['script'].= "$('#".$f['name']."_ms').multiselect({
		    height:'auto',
		    selectedList: 1,
		    needsubmit:false,
		    minWidth:150,
		    position: {
		      my: 'left top',
		      at: 'left bottom'
		    },
				checkAll: function(){
					$('#".$f['name']."_ms').multiselect('option', 'needsubmit' ,true);
					
				},
				uncheckAll: function(){
					$('#".$f['name']."_ms').multiselect('option', 'needsubmit', true);
				},		
				click: function(event, ui) {
					$('#".$f['name']."_ms').multiselect('option', 'needsubmit', true);
				},    
		    close: function(){
		    	myneedsubmit = $('#".$f['name']."_ms').multiselect('option', 'needsubmit');
		      if (myneedsubmit==false) return;
		      var myselval=$('#".$f['name']."_ms').val();
		      if (myselval == null || myselval.length==0) {
		        myalert('error:".gks_lang('Κάντε τουλάχιστον μία επιλογή')."');
		        return;
		      }
		      if (typeof myselval == 'undefined') return;
		      if ($('#".$f['name']."_ms')[0].options.length == myselval.length) {
		        $('#".$f['name']."').val('-1');
		      } else {
		        $('#".$f['name']."').val(myselval.join(','));
		      }
		      
		      $('#filter-form').submit();
		    }    
		  }).multiselectfilter();
		  ";
		}

		
    $html.= '<select '. ($is_multiselect ? '' : ' name="' . $f['name']) . '" id="' . $f['name'] .($is_multiselect ? '_ms' : '') . '" class="gks_filter_css_filter ' . $f['class'] . ' ' . $class . ' [[classifall]]" style="' . $f['style'] . ' " '. ($is_multiselect ? 'multiple="multiple"' : '' ) .'>';
                                                                                                                                                                                                                        //[[styleifall]]          
    //print '<pre>';
    //print_r($_GET);
    //die();
    //echo $html ;
    //die();

    $filter_sql='';
    $filter_sql_mywherepos = array();
    
    
    if (isset($f['vals'])) {
      foreach ($f['vals'] as $v) {

      	if ($is_multiselect) {
         	$selected = (count($is_multiselect_vals) > 0 && in_array($v['value'], $is_multiselect_vals)) ? 'selected="selected"' : '';
					$selected_html=$selected;
					if (count($is_multiselect_vals) == 1 and $is_multiselect_vals[0]==-1) {
						$selected_html = 'selected="selected"';
						$styleifall=true;
					}
       	} else {
        	$selected = (isset($applied[$f['name']]) && (int) $applied[$f['name']] == $v['value']) ? 'selected="selected"' : '';
					$selected_html=$selected;
        }
        

        if (!empty($selected) and $v['sql'] == '1=1') {
          $styleifall=true;
        }
          
        if (isset($v['is_custom_date']) && $v['is_custom_date']) {
          
          
          $dateFrom = isset($applied[$f['name'] . '-from']) ? trim_gks($applied[$f['name'] . '-from']) : '';
          $dateTo = isset($applied[$f['name'] . '-to']) ? trim_gks($applied[$f['name'] . '-to']) : '';
          if ($dateFrom == '__/__/____')$dateFrom='';
          if ($dateTo == '__/__/____')$dateTo='';
          

          
          $dateFrom = gks_myFormatDate($dateFrom);
          $dateTo = gks_myFormatDate($dateTo);


          $continue_to_vals=true;
          if ($dateFrom == false) $continue_to_vals=false;
          if ($dateTo == false) $continue_to_vals=false;
          if ($dateFrom > $dateTo) $continue_to_vals=false;
          if ($applied[$f['name']] != -2) $continue_to_vals=false;

          $is_custom_date_html_display='none';
          
          
          $vardiacustomdate=0;
          $time_to_calc= time();
          if (isset($v['vardiacustomdate']) && $v['vardiacustomdate']) {
            $vardiacustomdate = GKS_ERP_START_VARDIA*60*60;
            $time_to_calc= time() - GKS_ERP_START_VARDIA*60*60;
          }
          

          
          
          //$vv_from=showDate(time()-10*86400, 'd/m/Y', 1);
          $vv_from=showDate($time_to_calc, 'd/m/Y', 1);
          $vv_to  =showDate($time_to_calc, 'd/m/Y', 1);
          

          

          
          if ($continue_to_vals) {
            
            $time_updown=-1;
            if (isset($v['local_time'])) {
              if ($v['local_time']===true) {
                $time_updown=0;
              } else if ($v['local_time']===false) {
                $time_updown=-1;
              }
            } 
                        
            //echo date('Y-m-d H:i:s',$dateFrom);die();
            $dateFromdb = showDate($dateFrom + $vardiacustomdate, 'Y-m-d H:i:s', $time_updown);
            $dateTodb = showDate($dateTo+86400-1 + $vardiacustomdate, 'Y-m-d H:i:s', $time_updown);
            $v['sql'] .= $f['field'] . " BETWEEN '" . $dateFromdb . "' AND '" . $dateTodb . "'";
            //echo $v['sql'];
            //die();
 

            $vv_from = date('d/m/Y', $dateFrom);
            $vv_to   = date('d/m/Y', $dateTo);

            if (!empty($filter['url'])) {
              $filter['url'] .= '&';
            }
            $filter['url'] .= $f['name'] . '-from=' . $vv_from;

            
            if (!empty($filter['url'])) {
              $filter['url'] .= '&';
            }
            $filter['url'] .= $f['name'] . '-to=' .  $vv_to; 

            //$extraFields[] = array('name' => $f['name'] . '-from', 'value' => $vv_from);
            //$extraFields[] = array('name' => $f['name'] . '-to',   'value' => $vv_to);
            $is_custom_date_html_display='inline-block';

            
            //echo $filter['url'] ;
            //die();

          }
          

                    
          $is_custom_date_html= '<div id ="filterdate-'. $f['name'].'" style="display:'.$is_custom_date_html_display.';"><table width="100%" cellspacing="0" cellpadding="2" border="0" ><tr><td width="0%" nowrap="nowrap">' . gks_lang('Επιλογή ημερομηνιών') . '</td></tr><tr><td width="0%" nowrap="nowrap">';
          $is_custom_date_html.='<input autocomplete="'.$autocomplete_gks_disable.'" type="text" class="filterselectboxcustomdate ui-state-default ui-corner-all" id="'.$f['name'].'-from"';
          if ($applied[$f['name']] == -2) {
            $is_custom_date_html.=' name="'.$f['name'].'-from"';
          }
          $is_custom_date_html.=' value="'.$vv_from.'"/> ';
          $is_custom_date_html.='<input autocomplete="'.$autocomplete_gks_disable.'" type="text" class="filterselectboxcustomdate ui-state-default ui-corner-all" id="'.$f['name'].'-to"';
          if ($applied[$f['name']] == -2) {
            $is_custom_date_html.=' name="'.$f['name'].'-to"';
          }
          $is_custom_date_html.=' value="'.$vv_to.'"/>';
          $is_custom_date_html.= '</td><td></td></tr></table></div>';
          
        }

        $html .= '<option value="' . $v['value'] . '" ' . $selected_html . '>' . $v['text'] . '</option>';


				
        if (!empty($selected)) {
        	
        	
          if (isset($f['mywherepos'])) {
           
            if (!isset($filter_sql_mywherepos['sql' . $f['mywherepos']])) {
              $filter_sql_mywherepos['sql' . $f['mywherepos']] = '';
            }
            $filter_sql_mywherepos['sql' . $f['mywherepos']] .= $v['sql']. ' or ';
            
            
          } else {
            //if (!empty($filter['sql'])) {
            //  $filter['sql'] .= ' AND ';
            //}
            //$filter['sql'] .= $v['sql'];
            $filter_sql.= $v['sql'].' or ';
          }

          if (stripos($filter['url'], $f['name'] . '=') === false) {
            if (!empty($filter['url'])) {
              $filter['url'] .= '&';
            }
            $filter['url'] .= $f['name'] . '=' . $v['value'];

          } else {
            $thisfilter = $f['name'] . '=' . $v['value'].'%2C';
            $filter['url'] = str_replace($f['name'] . '=', $thisfilter, $filter['url']); 
          }
        } else if (isset($applied[$f['name']]) && (int) $applied[$f['name']] == -1 && stripos($filter['url'], $f['name'] . '=') === false) {
          if (!empty($filter['url'])) {
            $filter['url'] .= '&';
          }
          $filter['url'] .= $f['name'] . '=-1';
        }
      }
    }
    if (isset($f['sql'])) {
      $res = $db_link->query($f['sql']);
      if (!$res) {
        debug_mail(false,'error sql',$f['sql']);
        return false;
      }
			
			
      while ($row = $res->fetch_assoc()) {
        
      	if ($is_multiselect) {
         	$selected = (count($is_multiselect_vals) > 0 && in_array($row['id'], $is_multiselect_vals)) ? 'selected="selected"' : '';
					$selected_html=$selected;
					if (count($is_multiselect_vals) == 1 and $is_multiselect_vals[0]==-1) {
						$selected_html = 'selected="selected"';
						$styleifall=true;
					}
       	} else {
        	$selected = (isset($applied[$f['name']]) && $applied[$f['name']] == $row['id']) ? 'selected="selected"' : '';
					$selected_html=$selected;
        }

        if (isset($f['lang']) && $f['lang']) {
          $d = isset($row['descr']) ? $row['descr'] : $row['descr'];
        } else {
          if (trim_gks($row['descr']) == "") {
            $d = "id : " . $row['id'];
          } else {
            $d = $row['descr'];
          }
        }

				
        $html .= '<option value="' . $row['id'] . '" ' . $selected_html . '>' . $d . '</option>';
        if (!empty($selected)) {
          if (isset($f['mywherepos'])) {
            if (!isset($filter_sql_mywherepos['sql' . $f['mywherepos']])) {
              $filter_sql_mywherepos['sql' . $f['mywherepos']] = '';
            }
            $filter_sql_mywherepos['sql' . $f['mywherepos']] .= str_replace('%V%', $row['id'], $f['field']). ' or ';
            
            
          } else {
            //if (!empty($filter['sql'])) {
            //  $filter['sql'] .= ' AND ';
            //}
            //$filter['sql'] .= str_replace('%V%', $row['id'], $f['field']);

            $filter_sql .= str_replace('%V%', $row['id'], $f['field']). ' or ';
          }
          
          if ($is_multiselect) {
            
            if (stripos($filter['url'], $f['name'] . '=') === false) {
              if (!empty($filter['url'])) {
                $filter['url'] .= '&';
              }
              $filter['url'] .= $f['name'] . '=' . $row['id'];
//              if ($gkIP == '94.67.151.220') {
//                echo   $filter['url'].'||'.$f['name'].'||'.$row['id'].'<br>';
//              }  
            } else {
              
              $thisfilter = $f['name'] . '=' . $row['id'].'%2C';
              $filter['url'] = str_replace($f['name'] . '=', $thisfilter, $filter['url']);
//              
//              if ($gkIP == '94.67.151.220') {
//                echo   $filter['url'].'||'.$f['name'].'<br>';
//              }                
            }          
            
            
          } else {
          
          
            if (stripos($filter['url'], $f['name'] . '=') === false) {
              if (!empty($filter['url'])) {
                $filter['url'] .= '&';
              }
              $filter['url'] .= $f['name'] . '=' . $row['id'];
            }          
          }
        }
      }
    }
    $html .= '</select>';
    
    if ($filter_sql != '') {
    	$filter_sql=substr($filter_sql, 0, strlen($filter_sql) - 4);
    	
	    if (!empty($filter['sql'])) {
	      $filter['sql'] .= ' AND ';
	    } 
	    $filter['sql'].= ' ('. $filter_sql .') ';
	  }

            	  
	  foreach ($filter_sql_mywherepos as $key => $value) {
      if (!empty($value)) {
        $value = substr($value, 0, strlen($value) - 4);  
  	    $value = ' ('. $value .') ';  
  	    if (isset($filter[$key]))  {
  	      $filter[$key].= ' AND '.$value;
  	    } else {
  	      $filter[$key] = $value;  
  	    }
	    } 
    } 
	  
	  

                
    //if ( $f['name'] == 'fergastirio_id') {
	  //  echo $filter['sql'];
	  //  die();
	  //}
	  
	  
    if ($styleifall == false) {
      //$html= str_replace('[[styleifall]]',';background-color: yellow;background-image: none;',$html);
      $html= str_replace('[[classifall]]','gks_filtercss_select_some',$html);
      
      
      
    	if ($is_multiselect) {
    		$filter['script'].= "
    		  $('#".$f['name']."_ms_ms').addClass('gks_filtercss_select_some_ms_ms');
    		";
//      		$('#".$f['name']."_ms_ms').css('color','#654b24');
//      		$('#".$f['name']."_ms_ms').css('background-image','unset');
//      		$('#".$f['name']."_ms_ms').css('background-color','blue');
//    		  $('#".$f['name']."_ms_ms').css('height','31px');
    		
    	}
    	
    } else {
      //$html= str_replace('[[styleifall]]','',$html);
      $html= str_replace('[[classifall]]','gks_filtercss_select_all',$html);
      if ($is_multiselect) {
        $filter['script'].= "
          $('#".$f['name']."_ms_ms').addClass('gks_filtercss_select_all_ms_ms');
        ";
      }
    }

    $titles .= $html . '</td></tr></table></div>';
    //$titles .= $html . '</td><td></td></tr></table></div>';

    $titles .= $is_custom_date_html;


  }




  if ($show_search_string) {

    $inputbox='<input class="form-control form-control-sm" name="search_string" id="search_string" type="text" value="'.(isset($_GET['search_string']) ? $_GET['search_string'] : '').'"/>';
    $titles .= '<div class="divfilter" style="vertical-align: top;"><table width="100%" cellspacing="0" cellpadding="2" border="0"><tr><td width="100%"><label for="search_string" style="margin-bottom:0px">'.gks_lang('Κείμενο αναζήτησης').':</label></td></tr><tr><td width="100%">' . $inputbox. '</td>
    </tr></table></div>';
  }
  if ($show_filter_button) {
    $button = '<input class="btn btn-primary btn-sm" type="submit" value="'.gks_lang('Εφαρμογή').'">  ';
    $titles .= '<div  class="divfilter" style="vertical-align: top;"><table width="100%" cellspacing="0" cellpadding="2" border="0"><tr><td width="100%">&nbsp;</td></tr><td width="100%">' . $button . '</td>
    </tr></table></div>';
  }

  //$titles .= '</td></tr></table>';
  $titles .= '</div></div>';



  //$table = '<table width="100%" cellspacing="0" cellpadding="2" border="0">';
  $table = $titles;
  //$table .= $boxes;
  //$table .= '</table>';

  //print_r($extraFields);
//  $extraFields[$i]
//  die();
  
  for ($i = 0; $i < count($extraFields); $i++) {
    $table .= '<input type="hidden1" name="' . $extraFields[$i]['name'] . '" value="' . $extraFields[$i]['value'] . '" />';
  }

  $filter['html'] = $table;

  return true;
}



function make_search_where($search_string,$search_fields) {
  global $db_link;
  if (count($search_fields)==0) return '';
  
  $terms =$search_string;
  $terms =trim_gks($terms);
  $terms = str_replace('  ',' ',$terms);
  $terms = str_replace('  ',' ',$terms);
  $terms = str_replace('  ',' ',$terms);
  
  $terms = explode(' ', $terms);
  $search_where = '';
  
  for ($i = 0; $i < count($terms); $i++) {
    if (mb_strlen(trim_gks($terms[$i]), 'utf8') >= 1) {
      $swtemp='';
      foreach($search_fields as $field_temp) {
        $swtemp.=$field_temp." like '%" . $db_link->escape_string(trim_gks($terms[$i])) . "%' OR ";
      }
      $swtemp = substr($swtemp,0,strlen($swtemp)-4);
      $swtemp = ' ('. $swtemp .') ';
      if ($search_where == '') {
        $search_where .= $swtemp;
      } else {
        $search_where .= " AND ".$swtemp;
      }
    } else {
      $search_where=' 1=1 ';
    }
  }  
  return trim_gks($search_where);
}



function pagination($pages, $page, $total_records, $url, &$paging, $ajax = false, $filter = '') {
    
    $paging['records'] = $total_records;
    $paging['total'] = ($pages + 1);
    $paging['pages'] = '';

    $b = '';

    if (!empty($filter)) {
        //$filter = '/?' . $filter;
    }


    $onclick = ($ajax) ? "onclick='pageClick(this); return false;'" : '';

    


    if ($pages > 0) {
        $pagesshow = 3;
        $i1 = $page - $pagesshow;
        $i2 = $page + $pagesshow;
        if ($i1 <= 0) {
            $i1 = 0;
        }

        if (($i2 - $i1) < $pagesshow * 2) {
            $i2 = $i1 + 2 * $pagesshow;
        }
        if ($i2 >= $pages) {
            $i2 = $pages;
        }

        if (($i2 - $i1) < $pagesshow * 2) {
            $i1 = $i2 - 2 * $pagesshow;
        }

        if ($i1 <= 0) {
            $i1 = 0;
        }
        if ($i2 >= $pages) {
            $i2 = $pages;
        }


        $a = '';

        if ($i1 != 0) {
            $a .= "<a href='" . $url . $filter . "' class='page-link' rel='1' " . $onclick . ">1</a>";
            
            $a .='<li class="page-item disabled"><a class="page-link" href="#" >-</a></li>'; 
        }

        for ($i = $i1; $i <= $i2; $i++) {
            $j = $i + 1;
            if ($i == $page) {
                $a .= '<li class="page-item active"><a class="page-link" href="' . $url . '&page=' . $i . $filter . '" rel="'.$j.'" '.$onclick. '">'.$j.'</a></li>';
            } else {
                $a .= '<li class="page-item"><a class="page-link" href="' . $url . '&page=' . $i . $filter . '" rel="'.$j.'" '.$onclick. '">'.$j.'</a></li>';
                
                 //<a href='" . $url . '&page=' . $i . $filter . "' class='page-link' rel='" . $j . "' " . $onclick . ">" . $j . "</a>|";
            }
        }
        //$a = substr($a, 0, strlen($a) - 1);
        $j = $pages + 1;
        if ($i2 != $pages) {
          $a .='<li class="page-item disabled"><a class="page-link" href="#" >-</a></li>'; 
          $a .= '<li class="page-item"><a class="page-link" href="' . $url . '&page=' . $pages . $filter . '" rel="'.$j.'" '.$onclick. '">'.$j.'</a></li>';    

            
            
        }

        $b .= $a;

        $paging['pages'] = '<div style="float:left;position: relative;top: 5px;padding-right: 6px;">'.gks_lang('Σελίδες').': </div><div style="float:left"><nav aria-label="Page navigation"><ul class="pagination pagination-sm" style="margin-bottom:0px">'.$b.'</ul></nav></div>';
    }
    $paging['recsperpage'] = recsperpage($url . $filter, $ajax);
}

function mytablepages($paging, $total_records) {
  if (!empty($paging['pages']) or !empty($paging['recsperpage'])) {
?>  
<div class="container-fluid" style="width:96%;padding:4px 0px 4px 0px;">
  <div class="row align-items-center no-gutters">
    <div class="col-sm-5" style="text-align:left">
      <?php echo $paging['pages']; ?>
    </div>
    <div class="col-sm-2" style="text-align:center">
      <?php echo gks_lang('Εγγραφές');?>: <?php echo myNumberFormat($total_records,0); ?>
    </div>
    <div class="col-sm-5" style="text-align:right">
      <?php echo $paging['recsperpage']; ?>
    </div>
  </div>
</div>  
<?php 
  }
}

function recsperpage($url, $ajax = false) {
  global $_gks_session;
  //echo $url;
  //die();
  $a = '<select class="recsperpageselectbox form-control form-control-sm" data-url="'.urlencode($url).'" data-ajax="'.($ajax ? '1' : '0').'">';
  //if ($ajax == false) {
      //$a.=" onchange=\"selectrecsperpage(this,'" . urlencode($url) . "'); return false;\" ";
  //} else {
      //$a.=" onchange=\"recsperpageClick(this); return false;\" ";
  //}
  //$a.=">";

  $a.='<option value="2"';
  if ($_gks_session['gks']['rows_per_page'] == 2)
      $a.= ' selected ';
  $a.='>2</option>';

  $a.='<option value="10"';
  if ($_gks_session['gks']['rows_per_page'] == 10)
      $a.= ' selected ';
  $a.='>10</option>';


  $a.='<option value="20"';
  if ($_gks_session['gks']['rows_per_page'] == 20)
      $a.= ' selected ';
  $a.='>20</option>';

  $a.='<option value="30"';
  if ($_gks_session['gks']['rows_per_page'] == 30)
      $a.= ' selected ';
  $a.='>30</option>';

  $a.='<option value="40"';
  if ($_gks_session['gks']['rows_per_page'] == 40)
      $a.= ' selected ';
  $a.='>40</option>';

  $a.='<option value="50"';
  if ($_gks_session['gks']['rows_per_page'] == 50)
      $a.= ' selected ';
  $a.='>50</option>';

  $a.='<option value="100"';
  if ($_gks_session['gks']['rows_per_page'] == 100)
      $a.= ' selected ';
  $a.='>100</option>';

  $a.='<option value="200"';
  if ($_gks_session['gks']['rows_per_page'] == 200)
      $a.= ' selected ';
  $a.='>200</option>';

  $a.='<option value="500"';
  if ($_gks_session['gks']['rows_per_page'] == 500)
      $a.= ' selected ';
  $a.='>500</option>';

  $a.='<option value="1000"';
  if ($_gks_session['gks']['rows_per_page'] == 1000)
      $a.= ' selected ';
  $a.='>1000</option>';

  $a.='</select>';

  return $a;
}


function makeSortLink($sortable, $prefix_url, $myURL, $field, $text, $extra = '') {
    global $url_prefix;
    $link = '<a href="%URL%">' . $text . '</a>';
    $url = $url_prefix . $prefix_url;

    $order = '';
    $sort = sortURL($sortable, $field, $myURL, $order);
   // if (substr($url, -1) != '?') {
        $url .= '&';
    //}
    $url .= $sort;

    if ($order == 'desc') {
        $link .= ' <img src="img/asc.png" Title="order asc" />';
    } else if ($order == 'asc') {
        $link .= ' <img src="img/desc.png" Title="order desc" />';
    }
    return str_replace('%URL%', $url . $extra, $link);
}

function sortURL($sortable, $sort, $myURL, &$order) {
    foreach ($sortable as $item) {
        $a = $item['name'];
        if (strtolower($sort) == strtolower($a)) {
            $return = $a . '=asc';
            ;
            $order = '';
            if (isset($myURL[$a])) {
                $order = (strtolower($myURL[$a]) == 'desc') ? 'asc' : 'desc';
                $return = $a . '=' . $order;
            }
            return $return;
        }
    }
    return '';
}

function makeSortable($sortable, &$sort, $myURL) {
    $sort = array('sql' => '', 'url' => '');

    foreach ($sortable as $item) {
        $a = $item['name'];
        if (isset($myURL[$a])) {
            
            
            if (strtolower($myURL[$a]) == 'desc') {
              $order ='desc';
            } else {
              $order ='asc';
            }
            
            $sort['sql'] = ''; //$item['field'];
            $vals=explode(',',$item['field']);
            foreach ($vals as $value) {
              $sort['sql'].=$value.' ' . $order.', ';
            } 
            
            if (strlen($sort['sql'])>2 and  substr($sort['sql'],strlen($sort['sql'])-2,2)==', ') {
              $sort['sql']=substr($sort['sql'],0,strlen($sort['sql'])-2);
            }
            
            $sort['url'] = $a . '=' . $order;
            break;
        }
    }
    //echo $sort['sql'];
    //die();
    return true;
}

function gks_clear_dspaces($a) {
  do {
    if (strpos($a, '  ') === false) break;
    $a=str_replace('  ',' ',$a);  
  } while (true);
  return trim_gks($a);
}


function gks_CheckAFM_Live(&$mybasketarray) {
  global $db_link;
  $check_vies_run=false;
  $check_vies_valid=0;
  $check_vies_error='';
  $check_vies_function='';
  
  $afm=trim_gks($mybasketarray['user']['afm']);
  if ($mybasketarray['from']=='order' or 
      $mybasketarray['from']=='acc_inv' or 
      $mybasketarray['from']=='acc_pay' or 
      $mybasketarray['from']=='whi_mov' or 
      $mybasketarray['from']=='crm_lead' or 
      $mybasketarray['from']=='crm_task' or
      $mybasketarray['from']=='crm_machine' or 
      $mybasketarray['from']=='reservation' or 
      $mybasketarray['from']=='transfer_reservation') {
    if ($mybasketarray['user']['ma_country_id'] == 91) { //greece
      if ($mybasketarray['parastatiko']!=0) {//timologio 
        if ($afm!='') {
          $res_vies = CheckAFM_GSIS($afm,$mybasketarray['company_id']);
          $check_vies_function='CheckAFM_GSIS';
          $check_vies_run=true;
          $check_vies_error=$res_vies['error'];
          $check_vies_valid=$res_vies['valid'];
 
        }        
      }
    } else if ($mybasketarray['user']['ma_country_id'] > 0) { //alli xora
      $country_ee='';
      $sql="select country_ee from gks_country where country_ee<>'' and id_country=".$mybasketarray['user']['ma_country_id'];
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'sql error',$sql);
      } else {
        if ($result->num_rows == 1) {
          $row = $result->fetch_assoc();
          $country_ee=$row['country_ee'];
        }
      }
      if ($country_ee!='') { //ee
        if ($mybasketarray['parastatiko']!=0) {//timologio 
          if ($afm!='') {
            $res_vies = CheckAFM_VIES($country_ee,$afm);
            $check_vies_function='CheckAFM_VIES';
            $check_vies_run=true;
            $check_vies_error=$res_vies['error'];
            $check_vies_valid=$res_vies['valid'];
          }
        }
      }
    }
  }
  
  $mybasketarray['check_vies']=array();
  $mybasketarray['check_vies']['run'] = $check_vies_run;
  $mybasketarray['check_vies']['valid'] = $check_vies_valid;
  $mybasketarray['check_vies']['error'] = $check_vies_error;
  $mybasketarray['check_vies']['function'] = $check_vies_function;  
  
  $views_run_img='';
  if ($mybasketarray['check_vies']['run']) {
    if ($mybasketarray['check_vies']['valid']==1) { //true
    	$views_run_img='<img src="/my/img/1.png" style="width:24px;" title="'.
    	($mybasketarray['check_vies']['function']=='CheckAFM_VIES' ? gks_lang('EU VIES Έλεγχος: Το VAT number είναι έγκυρο') : gks_lang('Έλεγχος ΑΦΜ μέσω του gsis.gr: Είναι έκγυρο')).
    	'" class="tooltipster">';
    } else if ($mybasketarray['check_vies']['valid']==2) { //wait
    	$views_run_img='<img src="/my/img/wait.gif" style="width:24px;" title="'.
    	($mybasketarray['check_vies']['function']=='CheckAFM_VIES' ? gks_lang('Αναμονή EU VIES Έλεγχος ...') : gks_lang('Αναμονή gsis.gr ...')).
    	'" class="tooltipster">';
    } else {
      if ($mybasketarray['check_vies']['error']=='') $views_run_img='<img src="/my/img/0.png" style="width:24px;" title="'.
        ($mybasketarray['check_vies']['function']=='CheckAFM_VIES' ? gks_lang('EU VIES Έλεγχος: Το VAT δεν είναι έγκυρο') : gks_lang('Έλεγχος ΑΦΜ μέσω του gsis.gr: Δεν είναι έγκυρο')).
        '" class="tooltipster">';
      else $views_run_img='<img src="/my/img/warning.gif" style="width:24px;" title="'.
        ($mybasketarray['check_vies']['function']=='CheckAFM_VIES' ? gks_lang('EU VIES Έλεγχος σφάλμα: ') . $mybasketarray['check_vies']['error'] : gks_lang('Σφάλμα κατά τον έλεγχο του ΑΦΜ μέσω του gsis.gr: ').$mybasketarray['check_vies']['error']).
        '" class="tooltipster">';
    }
  }  
  $mybasketarray['check_vies']['views_run_img']=$views_run_img;
}

function CheckAFM_GSIS($afm,$company_id, $force=false) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;

  //echo '<pre>ssssssssssssssss '.$afm.'|'.$company_id.'|'.$force;die();
  
  $out=array('valid'=>0, 'error'=>'');
  $afm=trim_gks($afm);
  
  if ($afm=='' or $afm=='000000000') {
    $out['error']=gks_lang('Δεν βρέθηκαν δεδομένα');
    return $out;
  }
  if ($force==false) {
    $mydate=date('Y-m-d H', time() - 6*24*60*60).':00:00'; // 6 imeres piso
    $sql="select * from gks_gsis_check
    where afm like '".$db_link->escape_string($afm)."' 
    and mydate>='".$mydate."' 
    order by connection_ok desc, valid desc, id_gsis_check desc
    limit 1";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $out['error']='SQL Error';
      return $out;
    } 
    if ($result->num_rows==1) {
      //echo 'dbbbbbbbbb ';
      
      $row = $result->fetch_assoc();  
      $out['valid'] = intval($row['valid']);
      $out['error']=(trim_gks($row['error_text'])=='' ? '' : '<br>'.trim_gks($row['error_text']));
      $out['error_text']=trim_gks($row['error_text']);
      
      $out['call_seq_id'] = trim_gks($row['response_call_seq_id']);
      $out['error_rec'] = unserialize(trim_gks($row['response_error_rec']));
      $out['afm_called_by_rec'] = unserialize(trim_gks($row['response_afm_called_by_rec']));
      
      $out['basic_rec'] = array();
      $out['basic_rec']['afm'] = trim_gks($row['response_afm']);
      $out['basic_rec']['doy'] = trim_gks($row['response_doy']);
      $out['basic_rec']['doy_descr'] = trim_gks($row['response_doy_descr']);
      $out['basic_rec']['i_ni_flag_descr'] = trim_gks($row['response_i_ni_flag_descr']);
      $out['basic_rec']['deactivation_flag'] = trim_gks($row['response_deactivation_flag']);
      $out['basic_rec']['deactivation_flag_descr'] = trim_gks($row['response_deactivation_flag_descr']);
      $out['basic_rec']['firm_flag_descr'] = trim_gks($row['response_firm_flag_descr']);
      $out['basic_rec']['onomasia'] = trim_gks($row['response_onomasia']);
      $out['basic_rec']['commer_title'] = trim_gks($row['response_commer_title']);
      $out['basic_rec']['legal_status_descr'] = trim_gks($row['response_legal_status_descr']);
      $out['basic_rec']['postal_address'] = trim_gks($row['response_postal_address']);
      $out['basic_rec']['postal_address_no'] = trim_gks($row['response_postal_address_no']);
      $out['basic_rec']['postal_zip_code'] = trim_gks($row['response_postal_zip_code']);
      $out['basic_rec']['postal_area_description'] = trim_gks($row['response_postal_area_description']);
      $out['basic_rec']['regist_date'] = trim_gks($row['response_regist_date']);
      $out['basic_rec']['stop_date'] = trim_gks($row['response_stop_date']);
      $out['basic_rec']['normal_vat_system_flag'] = trim_gks($row['response_normal_vat_system_flag']);
      
      $out['firm_act_tab'] = unserialize(trim_gks($row['response_firm_act_tab']));
      
      
      //echo '<pre>ggggggggggggggg sync CheckAFM_GSIS force:'.$force;print_r($out);die();
      
      return $out;
    
    } 
  } 
  

  
  //echo '<pre>ggggggggggggggg live '; 
  
    //echo 'liveeeeeeeeeee ';

  $gsis_afm_check_username='';
  $gsis_afm_check_password='';
  
  $company_id=intval($company_id);
  if ($company_id>0) {
    $sql="select * from gks_company where company_disable=0 and id_company=".$company_id;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$out['error']='SQL Error';return $out;} 
    if ($result->num_rows==0) {debug_mail(false,'company not found',$sql);$out['error']=gks_lang('Δεν βρέθηκε η εταιρεία');return $out;} 
    $row = $result->fetch_assoc();   
    $gsis_afm_check_username=trim_gks($row['gsis_afm_check_username']);
    $gsis_afm_check_password=trim_gks($row['gsis_afm_check_password']);
    if ($gsis_afm_check_username=='' or $gsis_afm_check_password=='') {
      //debug_mail(false,'gsis_afm_check_username and/or gsis_afm_check_password found',$sql);
      //to keimeno na min exei ", mono ' dioti mpainei to tooltip
      $temp=gks_lang('<br>Στην εταιρεία <a href="admin-company-item.php?id=[1]" target="_blank"><b>[2]</b></a> δεν έχουν ορισθεί τα<br><b>Κωδικός εισόδου</b> και <b>Συνθηματικό</b> στην ενότητα</b><br><b>ΑΑΔΕ - Αναζήτηση Βασικών Στοιχείων Μητρώου Επιχειρήσεων</b>');
      $temp=str_replace('[1]',$row['id_company'],$temp);
      $temp=str_replace('[2]',$row['company_eponimia'],$temp);
      $out['error']=$temp;

      if ($force) debug_mail(false,'CheckAFM_GSIS',$sql.'<br>'.$out['error']);
    }
  }
  
  if ($gsis_afm_check_username=='' or $gsis_afm_check_password=='') {
    $sql="SELECT *
    FROM gks_company
    where gsis_afm_check_username<>'' and gsis_afm_check_password<>'' and company_disable=0
    order by RAND()";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$out['error']='SQL Error';return $out;} 
    if ($result->num_rows==0) {
      //if ($force) debug_mail(false,'company_id=0','');
      
      $out['error']=gks_lang('Δεν βρέθηκε εταιρεία που να έχει<br><b>Κωδικός εισόδου</b> και <b>Συνθηματικό χρήστη</b><br>για την <b>Αναζήτηση Βασικών Στοιχείων Μητρώου Επιχειρήσεων</b>');
      if ($force) debug_mail(false,'CheckAFM_GSIS (2)',$sql.'<br>'.$out['error']);
      return $out;
    } 
    $row = $result->fetch_assoc();  
    $company_id=$row['id_company'];
    $gsis_afm_check_username=trim_gks($row['gsis_afm_check_username']);
    $gsis_afm_check_password=trim_gks($row['gsis_afm_check_password']);
    //debug_mail(false,'company_id=0','');    
  }
  
  if ($gsis_afm_check_username=='' or $gsis_afm_check_password=='') {
    $out['error']=gks_lang('Δεν έχουν ορισθεί τα<br><b>Κωδικός εισόδου</b> και <b>Συνθηματικό</b> στην ενότητα</b><br><b>ΑΑΔΕ - Αναζήτηση Βασικών Στοιχείων Μητρώου Επιχειρήσεων</b><br>σε καμία εταιρεία');
    
    if ($force) debug_mail(false,'CheckAFM_GSIS (3)',$out['error']);
    return $out;
  }

  
  if ($force == false) { 
    $out['valid'] = 2; // wait
    $out['error']=gks_lang('Αναμονή').' (1) ...';
    $out['error_text']=gks_lang('Αναμονή').' (2) ...';
    
    $out['call_seq_id'] = '';
    $out['error_rec'] = array();
    $out['afm_called_by_rec'] = array();
    $out['firm_act_tab'] = array();
		
		CheckAFM_GSIS_VIES_async($afm,$company_id,'','gsis');
		//echo '<pre>ggggggggggggggg async CheckAFM_GSIS force:'.$force;print_r($out);die();
		return $out;
	}
	      

  //echo '<pre>ggggggggggggggg CheckAFM_GSIS force:'.$force;die();

  //echo '<pre>'.$force.'|'.$gsis_afm_check_username.'|'.$gsis_afm_check_password;die();
    
  $xml_post_string = '<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" xmlns:ns1="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" xmlns:ns2="http://rgwspublic2/RgWsPublic2Service" xmlns:ns3="http://rgwspublic2/RgWsPublic2">
   <env:Header>
      <ns1:Security>
         <ns1:UsernameToken>
            <ns1:Username>'.htmlspecialchars($gsis_afm_check_username, ENT_XML1, 'UTF-8').'</ns1:Username>
            <ns1:Password>'.htmlspecialchars($gsis_afm_check_password, ENT_XML1, 'UTF-8').'</ns1:Password>
         </ns1:UsernameToken>
      </ns1:Security>
   </env:Header>
   <env:Body>
      <ns2:rgWsPublic2AfmMethod>
         <ns2:INPUT_REC>
            <ns3:afm_called_by/>
            <ns3:afm_called_for>'.$afm.'</ns3:afm_called_for>
         </ns2:INPUT_REC>
      </ns2:rgWsPublic2AfmMethod>
   </env:Body>
</env:Envelope>
';  
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/gsis_send_'.time().'.txt',$xml_post_string);
  //echo '<pre>'.$xml_post_string;die();

  $headers = array(
    "POST /wsaade/RgWsPublic2/RgWsPublic2 HTTP/1.1",
    "Host: www1.gsis.gr",
    "Content-Type: application/soap+xml; charset=utf-8",
    "Content-Length: ".strlen($xml_post_string)
  ); 
  
  $soapUrl = "https://www1.gsis.gr/wsaade/RgWsPublic2/RgWsPublic2?WSDL";

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $soapUrl);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 5);
  
  $response = curl_exec($ch); 

  if($ch === false) {
    debug_mail(false,'error CheckAFM_GSIS cURL error',$soapUrl."\n".print_r($headers,true)."\n".$xml_post_string);
    $out['error']=gks_lang('Σφάλμα σύνδεσης με το https://www1.gsis.gr (Soap Client Connection error (3))');
    $sql="insert into gks_gsis_check (mydate,myip,user_id,afm,connection_ok,valid,error_text) values (
    now(),'".$db_link->escape_string($gkIP)."',".$my_wp_user_id.",'".$db_link->escape_string($afm)."',0,0,'".$db_link->escape_string($out['error'])."')";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $out['error']='SQL Error';
      return $out;
    }      
    return $out;
  }
  curl_close($ch);
  
  if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/gsis_response_'.time().'.txt',$response);

  //echo $response;die();
  
  if ($response=='' or strpos($response, 'Error 404') !== false) {
    debug_mail(false,'error CheckAFM_GSIS cURL error',$soapUrl."\n".print_r($headers,true)."\n".$xml_post_string);
    $out['error']=gks_lang('Σφάλμα σύνδεσης με το https://www1.gsis.gr (Soap Client Connection error (4))');
    $sql="insert into gks_gsis_check (mydate,myip,user_id,afm,connection_ok,valid,error_text) values (
    now(),'".$db_link->escape_string($gkIP)."',".$my_wp_user_id.",'".$db_link->escape_string($afm)."',0,0,'".$db_link->escape_string($out['error'])."')";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $out['error']='SQL Error';
      return $out;
    }       
    return $out;
  }
  
  //debug_mail(false,'error CheckAFM_GSIS Result', htmlspecialchars_gks($response));
  //file_put_contents(GKS_SITE_PATH.'logs/gsis.'.time().'.xml',$response);
  //echo $response;//die();
  try {
    $xml = new SimpleXMLElement($response,  LIBXML_NOERROR);
  } catch(Exception $e) { 
    //var_dump($e);die();
    
    debug_mail(false,'error CheckAFM_GSIS cURL error',$soapUrl."\n".print_r($headers,true)."\n".$xml_post_string."\n".$e->getMessage() ."\n".htmlspecialchars_gks($response) );
    $out['error']=gks_lang('Σφάλμα κατά την αναγνώριση ως XML τα δεδομένα που επέστρεψε ο διακομιστής της ΑΑΔΕ (xml parse error)').'<br>'.htmlspecialchars_gks($response).'<br>'.htmlspecialchars_gks($e->getMessage());
    $sql="insert into gks_gsis_check (mydate,myip,user_id,afm,connection_ok,valid,error_text) values (
    now(),'".$db_link->escape_string($gkIP)."',".$my_wp_user_id.",'".$db_link->escape_string($afm)."',0,0,'".$db_link->escape_string($out['error'])."')";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $out['error']='SQL Error';
      return $out;
    }       
          
    return $out; 
  } catch(\Exception $e) { 
    //var_dump($e);die();
    
    debug_mail(false,'error CheckAFM_GSIS cURL error',$soapUrl."\n".print_r($headers,true)."\n".htmlspecialchars_gks($xml_post_string)."\n".htmlspecialchars_gks($response) ."\n".htmlspecialchars_gks($e->getMessage())  );
    $out['error']=gks_lang('Σφάλμα κατά την αναγνώριση ως XML τα δεδομένα που επέστρεψε ο διακομιστής της ΑΑΔΕ (xml parse error)').'<br>'.htmlspecialchars_gks($response).'<br>'.htmlspecialchars_gks($e->getMessage());
    $sql="insert into gks_gsis_check (mydate,myip,user_id,afm,connection_ok,valid,error_text) values (
    now(),'".$db_link->escape_string($gkIP)."',".$my_wp_user_id.",'".$db_link->escape_string($afm)."',0,0,'".$db_link->escape_string($out['error'])."')";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $out['error']='SQL Error';
      return $out;
    }       
    return $out; 
  }
  
  $namespaces = $xml->getNameSpaces(true);
  //echo '<pre>';var_dump($namespaces);die();
  //echo 'gggg';die();
  $xs = $xml->children($namespaces['env']);
  
  $xml->registerXPathNamespace('test', 'http://rgwspublic2/RgWsPublic2Service'); 
  // Retrieving xpaths 
  $result = $xml->xpath('//env:Envelope/env:Body/test:rgWsPublic2AfmMethodResponse/test:result'); 
  
  
  
  $out['call_seq_id'] = gks_clear_dspaces(trim_gks((string)$result[0]->rg_ws_public2_result_rtType->call_seq_id));
  
  
  $error_rec = $result[0]->rg_ws_public2_result_rtType->error_rec;
  $out['error_rec'] = array();
  $out['error_rec']['error_code'] = gks_clear_dspaces(trim_gks((string)$error_rec->error_code));
  $out['error_rec']['error_descr'] = gks_clear_dspaces(trim_gks((string)$error_rec->error_descr));
  
  $afm_called_by_rec = $result[0]->rg_ws_public2_result_rtType->afm_called_by_rec;
  $out['afm_called_by_rec'] = array();
  $out['afm_called_by_rec']['token_username'] = gks_clear_dspaces(trim_gks((string)$afm_called_by_rec->token_username));
  $out['afm_called_by_rec']['token_afm'] = gks_clear_dspaces(trim_gks((string)$afm_called_by_rec->token_afm));
  $out['afm_called_by_rec']['token_afm_fullname'] = gks_clear_dspaces(trim_gks((string)$afm_called_by_rec->token_afm_fullname));
  $out['afm_called_by_rec']['afm_called_by'] = gks_clear_dspaces(trim_gks((string)$afm_called_by_rec->afm_called_by));
  $out['afm_called_by_rec']['afm_called_by_fullname'] = gks_clear_dspaces(trim_gks((string)$afm_called_by_rec->afm_called_by_fullname));
  $out['afm_called_by_rec']['as_on_date'] = gks_clear_dspaces(trim_gks((string)$afm_called_by_rec->as_on_date));
  
  $basic_rec = $result[0]->rg_ws_public2_result_rtType->basic_rec;
  $out['basic_rec'] = array();
  $out['basic_rec']['afm'] = gks_clear_dspaces(trim_gks((string)$basic_rec->afm));
  $out['basic_rec']['doy'] = gks_clear_dspaces(trim_gks((string)$basic_rec->doy));
  $out['basic_rec']['doy_descr'] = gks_clear_dspaces(trim_gks((string)$basic_rec->doy_descr));
  $out['basic_rec']['i_ni_flag_descr'] = gks_clear_dspaces(trim_gks((string)$basic_rec->i_ni_flag_descr));
  $out['basic_rec']['deactivation_flag'] = gks_clear_dspaces(trim_gks((string)$basic_rec->deactivation_flag));
  $out['basic_rec']['deactivation_flag_descr'] = gks_clear_dspaces(trim_gks((string)$basic_rec->deactivation_flag_descr));
  $out['basic_rec']['firm_flag_descr'] = gks_clear_dspaces(trim_gks((string)$basic_rec->firm_flag_descr));
  $out['basic_rec']['onomasia'] = gks_clear_dspaces(trim_gks((string)$basic_rec->onomasia));
  $out['basic_rec']['commer_title'] = gks_clear_dspaces(trim_gks((string)$basic_rec->commer_title));
  $out['basic_rec']['legal_status_descr'] = gks_clear_dspaces(trim_gks((string)$basic_rec->legal_status_descr ));
  $out['basic_rec']['postal_address'] = gks_clear_dspaces(trim_gks((string)$basic_rec->postal_address));
  $out['basic_rec']['postal_address_no'] = gks_clear_dspaces(trim_gks((string)$basic_rec->postal_address_no));
  $out['basic_rec']['postal_zip_code'] = gks_clear_dspaces(trim_gks((string)$basic_rec->postal_zip_code));
  $out['basic_rec']['postal_area_description'] = gks_clear_dspaces(trim_gks((string)$basic_rec->postal_area_description));
  $out['basic_rec']['regist_date'] = gks_clear_dspaces(trim_gks((string)$basic_rec->regist_date));
  $out['basic_rec']['stop_date'] = gks_clear_dspaces(trim_gks((string)$basic_rec->stop_date));
  $out['basic_rec']['normal_vat_system_flag'] = gks_clear_dspaces(trim_gks((string)$basic_rec->normal_vat_system_flag));
  
  
  $out['firm_act_tab']=array();
  $firm_act_tab = $result[0]->rg_ws_public2_result_rtType->firm_act_tab;
  if ($firm_act_tab->item !=null) {
    foreach ($firm_act_tab->item as $firm_act) {
      $firm_act_item=array();
      $firm_act_item['code'] = gks_clear_dspaces(trim_gks((string) $firm_act->firm_act_code));
      $firm_act_item['cdescr'] = gks_clear_dspaces(trim_gks((string) $firm_act->firm_act_descr));
      $firm_act_item['kind'] = gks_clear_dspaces(trim_gks((string) $firm_act->firm_act_kind));
      $firm_act_item['kdescr'] = gks_clear_dspaces(trim_gks((string) $firm_act->firm_act_kind_descr));
      $out['firm_act_tab'][] = $firm_act_item;
    }
  }
  
  //$out['valid'] = ($out['error_rec']['error_code']=='' and $out['basic_rec']['normal_vat_system_flag']=='Y' ? true : false);
  //$out['valid'] = ($out['error_rec']['error_code']=='' and $out['basic_rec']['deactivation_flag']=='1' ? true : false);
  

  //EPITIDEYMATIAS
  //PROIN EPITIDEYMATIAS
  $out['valid']=0;
  $out['error_text']='';
  
  if ($out['error_rec']['error_code']!='RG_WS_PUBLIC_TOKEN_USERNAME_NOT_AUTHENTICATED' and 
      $out['error_rec']['error_code']!='RG_WS_PUBLIC_AFM_CALLED_BY_NOT_ALLOWED') {
    
    $error_text='';if (isset($out['error_rec']['error_descr'])) $error_text=$out['error_rec']['error_descr'];

    
    if ($out['error_rec']['error_code']=='') {
      if ($out['basic_rec']['firm_flag_descr']=='ΕΠΙΤΗΔΕΥΜΑΤΙΑΣ') {
        $out['valid']=1;  
      } else if ($out['basic_rec']['firm_flag_descr']=='ΠΡΩΗΝ ΕΠΙΤΗΔΕΥΜΑΤΙΑΣ') {
        $error_text=gks_lang('ΠΡΩΗΝ ΕΠΙΤΗΔΕΥΜΑΤΙΑΣ').' '.gks_lang('Διακοπή στις').' '.$out['basic_rec']['stop_date'].' '.$error_text;
        $out['valid']=0;  
      } else if ($out['basic_rec']['firm_flag_descr']=='ΜΗ ΕΠΙΤΗΔΕΥΜΑΤΙΑΣ') {
        $error_text=gks_lang('ΜΗ ΕΠΙΤΗΔΕΥΜΑΤΙΑΣ').' '.gks_lang('Διακοπή στις').' '.$out['basic_rec']['stop_date'].' '.$error_text;
        $out['valid']=0;  
      } else if ($out['basic_rec']['firm_flag_descr']!='') {
        $error_text=gks_lang('Μη ενεργό').' '.$out['basic_rec']['firm_flag_descr'].' '.$error_text;
        $out['valid']=0;  
      }
      
    }
    $out['error_text']=$error_text;
    $out['error']=$out['error_text'];
      
    $sql="insert into gks_gsis_check (
    mydate,myip,user_id,afm,connection_ok,valid,error_text,
    response_call_seq_id,
    response_error_rec,
    response_afm_called_by_rec,
    response_afm,response_doy,response_doy_descr,
    response_i_ni_flag_descr,response_deactivation_flag,response_deactivation_flag_descr,response_firm_flag_descr,
    response_onomasia,response_commer_title,response_legal_status_descr,
    response_postal_address,response_postal_address_no,
    response_postal_zip_code,response_postal_area_description,
    response_regist_date,response_stop_date,
    response_normal_vat_system_flag,response_firm_act_tab
    
    ) values (
      now(),
      '".$db_link->escape_string($gkIP)."',
      ".$my_wp_user_id.",
      '".$db_link->escape_string($afm)."',
      1,
      ".$out['valid'].",
      '".$db_link->escape_string($error_text)."',
      '".$db_link->escape_string($out['call_seq_id'])."',
      '".$db_link->escape_string(serialize($out['error_rec']))."',
      '".$db_link->escape_string(serialize($out['afm_called_by_rec']))."',
      '".$db_link->escape_string($out['basic_rec']['afm'])."',
      '".$db_link->escape_string($out['basic_rec']['doy'])."',
      '".$db_link->escape_string($out['basic_rec']['doy_descr'])."',
      '".$db_link->escape_string($out['basic_rec']['i_ni_flag_descr'])."',
      '".$db_link->escape_string($out['basic_rec']['deactivation_flag'])."',
      '".$db_link->escape_string($out['basic_rec']['deactivation_flag_descr'])."',
      '".$db_link->escape_string($out['basic_rec']['firm_flag_descr'])."',
      '".$db_link->escape_string($out['basic_rec']['onomasia'])."',
      '".$db_link->escape_string($out['basic_rec']['commer_title'])."',
      '".$db_link->escape_string($out['basic_rec']['legal_status_descr'])."',
      '".$db_link->escape_string($out['basic_rec']['postal_address'])."',
      '".$db_link->escape_string($out['basic_rec']['postal_address_no'])."',
      '".$db_link->escape_string($out['basic_rec']['postal_zip_code'])."',
      '".$db_link->escape_string($out['basic_rec']['postal_area_description'])."',
      '".$db_link->escape_string($out['basic_rec']['regist_date'])."',
      '".$db_link->escape_string($out['basic_rec']['stop_date'])."',
      '".$db_link->escape_string($out['basic_rec']['normal_vat_system_flag'])."',
      '".$db_link->escape_string(serialize($out['firm_act_tab']))."'
    )";
    $result = $db_link->query($sql);  
    //echo '<pre>'.$sql;die();
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $out['error']='SQL Error';
      return $out;
    }      
  } else {
    debug_mail(false,'error CheckAFM_GSIS cURL error',$soapUrl."\n".print_r($headers,true)."\n".$xml_post_string."\n".print_r($out,true));
    $out['error']=gks_lang('ΑΑΔΕ - Αναζήτηση Βασικών Στοιχείων Μητρώου Επιχειρήσεων').' '.$out['error_rec']['error_descr'];
    $sql="insert into gks_gsis_check (mydate,myip,user_id,afm,connection_ok,valid,error_text) values (
    now(),'".$db_link->escape_string($gkIP)."',".$my_wp_user_id.",'".$db_link->escape_string($afm)."',1,0,'".$db_link->escape_string($out['error'])."')";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $out['error']='SQL Error';
      return $out;
    }       

		
  }
    
  //echo '<pre>ggggggggggggggg ';print_r($out);die();
  
  return $out;
}

function CheckAFM_VIES($country_ee,$afm,$force=false) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  
  
  //echo '<pre>ssssssssssssssss '.$country_ee.'|'.$afm.'|'.$force;die();
  
  $out=array('valid'=>0, 'countryCode'=>'', 'vatNumber'=>'', 'requestDate'=>'', 'traderName'=>'', 'traderCompanyType'=>'','traderAddress'=>'','requestIdentifier'=>'', 'error'=>'');
  $country_ee=trim_gks($country_ee);
  $afm=trim_gks($afm);
  
  if ($country_ee=='' or $afm=='') {
    $out['error']='Data is empty';
    return $out;
  }
  
  if ($force==false) {
    $mydate=date('Y-m-d H', time() - 24*60*60).':00:00';
    $sql="select * from gks_vies_check 
    where country_ee like '".$db_link->escape_string($country_ee)."' 
    and afm like '".$db_link->escape_string($afm)."' 
    and mydate>='".$mydate."' 
    order by connection_ok desc, response_valid desc, id_vies_check desc limit 1";
    //debug_mail(false,'error sql',$sql);
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $out['error']='SQL Error';
      return $out;
    } 
    if ($result->num_rows==1) {
      $row = $result->fetch_assoc();
      $out['valid'] = intval($row['response_valid']);
      $out['countryCode'] = trim_gks($row['response_countryCode']);
      $out['vatNumber'] = trim_gks($row['response_vatNumber']);
      $out['requestDate'] = trim_gks($row['response_requestDate']);
      $out['traderName'] = trim_gks($row['response_traderName']);
      $out['traderCompanyType'] = trim_gks($row['response_traderCompanyType']);
      $out['traderAddress'] = trim_gks($row['response_traderAddress']);
      $out['requestIdentifier'] = trim_gks($row['response_requestIdentifier']);
      $out['error']=trim_gks($row['error_text']);
      return $out;
    }
  }
  
  if ($force == false) {
  	$out['valid']=2;
  	$out['error']=gks_lang('Αναμονή').' (3) ...';
  	
  	CheckAFM_GSIS_VIES_async($afm,0,$country_ee,'vies');
  	//echo '<pre>gggggggggggggg ';
  	return $out;
  }


  $opts = array(
    'socket' => array(
      'bindto' => '0:0' //use IPv4
    ),
    'http' => array(
      'user_agent' => 'PHPSoapClient'
    ),
    'ssl' => array(
      'verify_peer'       => false,
      'verify_peer_name'  => false,
      'allow_self_signed' => true        
    ),
  );
  $context = stream_context_create($opts);
  $soapClientOptions = array(
    'stream_context' => $context,
    'cache_wsdl' => WSDL_CACHE_NONE,
    'trace' => 1,
  );
  try {
    $client = new SoapClient("https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl", $soapClientOptions );
    if (!$client) {
      debug_mail(false,'error CheckAFM_VIES new SoapClient error',$country_ee.' '.$afm);
      $out['error']='Soap Client error';
      $raw='';
      $sql="insert into gks_vies_check (mydate,myip,user_id,country_ee,afm,response_valid,error_text,response_raw,connection_ok) values (
        now(),'".$db_link->escape_string($gkIP)."',".$my_wp_user_id.",'".$db_link->escape_string($country_ee)."',
        '".$db_link->escape_string($afm)."',0,'".$db_link->escape_string($out['error'])."','".$db_link->escape_string($raw)."',0)";
      $result = $db_link->query($sql);  
      if (!$result) {debug_mail(false,'error sql',$sql);$out['error']='SQL Error';return $out;}             
      return $out;
    }
  } catch(SoapFault $e) {
    debug_mail(false,'error CheckAFM_VIES SoapClient error',$e->faultstring);
    $out['error']='Soap Client Connection error (5) '.$e->faultstring;
    $raw='';
    $sql="insert into gks_vies_check (mydate,myip,user_id,country_ee,afm,response_valid,error_text,response_raw,connection_ok) values (
      now(),'".$db_link->escape_string($gkIP)."',".$my_wp_user_id.",'".$db_link->escape_string($country_ee)."',
      '".$db_link->escape_string($afm)."',0,'".$db_link->escape_string($out['error'])."','".$db_link->escape_string($raw)."',0)";
    $result = $db_link->query($sql);  
    if (!$result) {debug_mail(false,'error sql',$sql);$out['error']='SQL Error';return $out;}             
    
    return $out;
    //echo 'Error, see message: '.$e->faultstring;
  }
  
  //$country_ee='EL';
  $params = array('countryCode' => $country_ee, 'vatNumber' => $afm,);
  try{
    $r = $client->checkVatApprox($params); // or checkVat
    $raw= $client->__getLastResponse();
    //var_dump($raw);
    //https://ec.europa.eu/taxation_customs/vies/checkVatTestService.wsdl
    
    if($r->valid == true) $out['valid']= 1;
    if (isset($r->countryCode)) $out['countryCode'] = trim_gks($r->countryCode);
    if (isset($r->vatNumber)) $out['vatNumber'] = trim_gks($r->vatNumber);
    if (isset($r->requestDate)) $out['requestDate'] = trim_gks($r->requestDate);
    if (isset($r->traderName)) $out['traderName'] = trim_gks($r->traderName);
    if (isset($r->traderCompanyType)) $out['traderCompanyType'] = trim_gks($r->traderCompanyType);
    if (isset($r->traderAddress)) $out['traderAddress'] = trim_gks($r->traderAddress);
    if (isset($r->traderStreet)) $out['traderStreet'] = trim_gks($r->traderStreet);
    if (isset($r->traderPostcode)) $out['traderPostcode'] = trim_gks($r->traderPostcode);
    if (isset($r->traderCity)) $out['traderCity'] = trim_gks($r->traderCity);
    if (isset($r->traderNameMatch)) $out['traderNameMatch'] = trim_gks($r->traderNameMatch);
    if (isset($r->traderCompanyTypeMatch)) $out['traderCompanyTypeMatch'] = trim_gks($r->traderCompanyTypeMatch);
    if (isset($r->traderStreetMatch)) $out['traderStreetMatch'] = trim_gks($r->traderStreetMatch);
    if (isset($r->traderPostcodeMatch)) $out['traderPostcodeMatch'] = trim_gks($r->traderPostcodeMatch);
    if (isset($r->traderCityMatch)) $out['traderCityMatch'] = trim_gks($r->traderCityMatch);
    
    if (isset($r->requestIdentifier)) $out['requestIdentifier'] = trim_gks($r->requestIdentifier);
    
    
    if ($out['traderName']=='---') $out['traderName']='';
    if ($out['traderCompanyType']=='---') $out['traderCompanyType']='';
    if ($out['traderAddress']=='---') $out['traderAddress']='';
    
    if ($out['vatNumber']!=$afm) {
      $out['error']='Error response';
    }
    
    $sql="insert into gks_vies_check (
      mydate,myip,user_id,country_ee,afm,
      response_valid,response_countryCode,response_vatNumber,
      response_requestDate,response_traderName,response_traderCompanyType,
      response_traderAddress,response_requestIdentifier,
      error_text,response_raw,connection_ok
    ) values (
      now(),
      '".$db_link->escape_string($gkIP)."',
      ".$my_wp_user_id.",
      '".$db_link->escape_string($country_ee)."',
      '".$db_link->escape_string($afm)."',
      ".$out['valid'].",
      '".$db_link->escape_string($out['countryCode'])."',
      '".$db_link->escape_string($out['vatNumber'])."',
      '".$db_link->escape_string($out['requestDate'])."',
      '".$db_link->escape_string($out['traderName'])."',
      '".$db_link->escape_string($out['traderCompanyType'])."',
      '".$db_link->escape_string($out['traderAddress'])."',
      '".$db_link->escape_string($out['requestIdentifier'])."',
      '',
      '".$db_link->escape_string($raw)."',
      1
    )";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $out['error']='SQL Error';
      return $out;
    }             
  } catch(SoapFault $e) {
    debug_mail(false,'error CheckAFM_VIES SoapClient error',$e->faultstring);
    $out['error']='Soap Client Connection error (2) '.$e->faultstring;
    $raw= $client->__getLastResponse();
    $sql="insert into gks_vies_check (mydate,myip,user_id,country_ee,afm,response_valid,error_text,response_raw,connection_ok) values (
      now(),'".$db_link->escape_string($gkIP)."',".$my_wp_user_id.",'".$db_link->escape_string($country_ee)."',
      '".$db_link->escape_string($afm)."',0,'".$db_link->escape_string($out['error'])."','".$db_link->escape_string($raw)."',0)";
    $result = $db_link->query($sql);  
    if (!$result) {debug_mail(false,'error sql',$sql);$out['error']='SQL Error';return $out;}             
    
    return $out;
    //echo 'Error, see message: '.$e->faultstring;
  }    
  


  return $out;
} 

function CheckAFM_GSIS_VIES_async($afm,$company_id,$country_ee,$poio) {
	
	//return; //na kanei exit otan thelo na kano debug
	
	$params=array(
		'afm' => $afm,
		'company_id' => $company_id,
		'country_ee' => $country_ee,
		'poio' => $poio,
	);

	$url=GKS_SITE_URL.'my/async_get_gsis_vies.php?cache='.time().rand(1000,9999).rand(1000,9999).rand(1000,9999);
	//echo $url;die();
  $data_string = json_encode($params);
  
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  if (defined('CURLOPT_SSL_VERIFYSTATUS')) curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  //curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER,
      array(
          'accept: application/json',
          'Content-Type: application/json',
          //'Content-Type: application/x-www-form-urlencoded',
          'Content-Length: ' . strlen($data_string)
      )
  ); 
  curl_setopt($ch, CURLOPT_TIMEOUT_MS, 100); //HERE MAGIC (We wait only 1ms on connection) Script waiting but (processing of send package to $curl is continue up to successful) so after 1ms we continue scripting and in background php continue already package to destiny. This is like apple on tree, we cut and go, but apple still fallow to destiny but we don't care what happened when fall down :) 
  curl_setopt($ch, CURLOPT_NOSIGNAL, 1); // i'dont know just it works together read manual ;)
  $result=curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);
  $gks_curl_info = curl_getinfo($ch);
  curl_close ($ch);
  
	//echo $result;

	
}

  
function CheckAFM($afm) {
    if (!preg_match('/^(EL){0,1}[0-9]{9}$/i', $afm))
        return false;
    if (strlen($afm) > 9)
        $afm = substr($afm, 2);

    $remainder = 0;
    $sum = 0;

    for ($nn = 2, $k = 7, $sum = 0; $k >= 0; $k--, $nn += $nn)
        $sum += $nn * ($afm[$k]);
    $remainder = $sum % 11;

    return ($remainder == 10) ? $afm[8] == '0'
                              : $afm[8] == $remainder;
}
function CheckAMKA($a) {
  $a=trim_gks($a);
  if ($a=='' or strlen($a) !=11 or $a=='00000000000') return false;
  if (ctype_digit($a) == false) return false;
  
  $iSum = 0;
  for ($i = 1; $i <= strlen($a); $i++) {
    $iDigit = intval($a[$i - 1]);
    if ($i % 2 === 0) {
      $iDigit *= 2;
      if ($iDigit > 9) {
        $iDigit -= 9;
      }
    }
  $iSum += $iDigit;
  }
  return ($iSum % 10 == 0);  
}

function gks_CheckPhone($a) {
  $a=trim_gks($a);
  if ($a=='') return false;
  $validchars1=array('0','1','2','3','4','5','6','7','8','9');
  $validchars2=array('*','#', '+','-', ',','.', '(',')', '/','N',';',' ');
  $num_count=0;
  for ($i = 0; $i < strlen($a); $i++) {
    $mychar = $a[$i];
    if (in_array($mychar,$validchars1)) {
      $num_count++;    
    }
    if (in_array($mychar,$validchars1)==false and in_array($mychar,$validchars2)==false) {
      return false; 
    }
  }
  if ($num_count<=3) return false;
  return true;
}



function get_user_ypoloipo_prokatavolis($fotografos_id, $exlude=0) {
  global $db_link;
  
  $sql_banktra="SELECT Sum(gks_bank_transactions.apodoxes) AS sumapodoxes
  FROM gks_bank_transactions
  WHERE gks_bank_transactions.btrastate In ('complete') 
  AND not_in_prokatavoli=0
  AND gks_bank_transactions.mydate_exec>='2018-01-01'
  AND gks_bank_transactions.user_id=".$fotografos_id;
  $result_banktra = $db_link->query($sql_banktra);  
  if (!$result_banktra) {
    debug_mail(false,'error sql',$sql_banktra);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  $sumapodoxes=0;
  if ($result_banktra->num_rows > 0) {
     $row_banktra = $result_banktra->fetch_assoc();
     $sumapodoxes = $row_banktra['sumapodoxes'];
  }
  
  
  
  
  

  $sumkratiseis_pliromi_bank = 0;
  $sumepliromi_kratiseis = 0;
  
  $ypoloipo_prokatavolis= $sumapodoxes - $sumkratiseis_pliromi_bank - $sumepliromi_kratiseis;
  //if ($ypoloipo_prokatavolis < 0) $ypoloipo_prokatavolis=0;
  return $ypoloipo_prokatavolis;
}

function gks_n_h($i) {
  if ($i<=12) {
    return gks_lang($i.'η','part3');
  } else {
    return $i.gks_lang('η','part3');
  }
}
function gks_n_ho($i) {
  if ($i<=12) {
    return gks_lang($i.'ου','part3');
  } else {
    return $i.gks_lang('ου','part3');
  }
}

function get_user_protypdays_descr($id,&$myarray,$everyweek='') {
  global $db_link;
  $myarray=array();
  $sql="SELECT gks_users_protypdays.*, gks_company.id_company, gks_company.company_title, gks_company.company_color
  FROM gks_users_protypdays LEFT JOIN gks_company ON gks_users_protypdays.company_id = gks_company.id_company
  WHERE user_id In (".$id.")
  ORDER BY user_id, ord_mwday, IF(ord_day=1,8,ord_day), ord_day, company_title";
  
  $result_protypdays = $db_link->query($sql);        
  if (!$result_protypdays) debug_mail(false,'error sql',$sql);
  if (!$result_protypdays) die('sql error');
  
  $res='';
  $prevweek=-1;
  while ($row_protypdays = $result_protypdays->fetch_assoc()) {
    $myarray[] = array(
      intval($row_protypdays['ord_mwday']),
      intval($row_protypdays['ord_day']), 
      intval($row_protypdays['company_id']), 
      $row_protypdays['company_title'].'', 
      $row_protypdays['company_color'].''
    );
    if ($prevweek==-1) $prevweek = $row_protypdays['ord_mwday'];
    if ($row_protypdays['ord_mwday'] != $prevweek) {
      if (endwith($res,', ')) $res=substr($res, 0, strlen($res)-2);
      $res.=$everyweek;
      $prevweek= $row_protypdays['ord_mwday'];
    }
    $res.= '<span class="spanprotypdays" style="background-color:'.$row_protypdays['company_color'].'" data-toggle="tooltip" data-html="true" title="'.$row_protypdays['company_title'].'">'.
    gks_n_h($row_protypdays['ord_mwday']). mb_substr(getWeekDayName($row_protypdays['ord_day'] - 1),0,2). '</span> ';
  }  
  //if (endwith($res,', ')) $res=substr($res, 0, strlen($res)-2);
  
  return $res;  
  
}


function search_get_data(&$outdata_this, $from, $to, &$project_fields) {
  global $db_link;
  global $gkIP;
  
  $project_fields = array();
  
  $count=count($outdata_this);
  $row_subdata_in='';
  for ($i = $from; $i <= $to and $i< $count; $i++) {
    $row_subdata_in.=$outdata_this[$i]['id_task'].',';
  }
  if (strlen($row_subdata_in)) $row_subdata_in=substr($row_subdata_in, 0, strlen($row_subdata_in)-1);
  
  if ($row_subdata_in != '') {
    $sql_sub="SELECT gks_tasks_authors.task_id, gks_persons.id_person, gks_persons.person_name
    FROM gks_tasks_authors INNER JOIN gks_persons ON gks_tasks_authors.author_id = gks_persons.id_person
    WHERE (((gks_tasks_authors.task_id) In (".$row_subdata_in.")) AND ((gks_persons.id_person)>0))
    ORDER BY gks_persons.person_name";
    $result_sub = $db_link->query($sql_sub);        
    if (!$result_sub) debug_mail(false,'error sql',$sql_sub);
    if (!$result_sub) die('sql error');
    
    while ($row_sub = $result_sub->fetch_assoc()) {
      for ($i = $from; $i <= $to and $i< $count; $i++) {
        if ($outdata_this[$i]['id_task'] == $row_sub['task_id']) {
          if (isset($outdata_this[$i]['authors']) == false) $outdata_this[$i]['authors'] = array();
          $outdata_this[$i]['authors'][] = array('id' => $row_sub['id_person'], 'name' => $row_sub['person_name']);
          break;  
        }
      }
    }
  
    $sql_sub="SELECT gks_tasks_publishers.task_id, gks_persons.id_person, gks_persons.person_name
    FROM gks_tasks_publishers LEFT JOIN gks_persons ON gks_tasks_publishers.publisher_id = gks_persons.id_person
    WHERE (((gks_tasks_publishers.task_id) In (".$row_subdata_in.")) AND ((gks_persons.id_person)>0))
    ORDER BY gks_persons.person_name";
    $result_sub = $db_link->query($sql_sub);        
    if (!$result_sub) debug_mail(false,'error sql',$sql_sub);
    if (!$result_sub) die('sql error');
    
    while ($row_sub = $result_sub->fetch_assoc()) {
      for ($i = $from; $i <= $to and $i< $count; $i++) {
        if ($outdata_this[$i]['id_task'] == $row_sub['task_id']) {
          if (isset($outdata_this[$i]['publishers']) == false) $outdata_this[$i]['publishers'] = array();
          $outdata_this[$i]['publishers'][] = array('id' => $row_sub['id_person'], 'name' => $row_sub['person_name']);
          break;  
        }
      }
    }
  
    $sql_sub="SELECT gks_tasks_owners.task_id, gks_persons.id_person, gks_persons.person_name
    FROM gks_tasks_owners LEFT JOIN gks_persons ON gks_tasks_owners.owner_id = gks_persons.id_person
    WHERE (((gks_tasks_owners.task_id) In (".$row_subdata_in.")) AND ((gks_persons.id_person)>0))
    ORDER BY gks_persons.person_name";
    $result_sub = $db_link->query($sql_sub);        
    if (!$result_sub) debug_mail(false,'error sql',$sql_sub);
    if (!$result_sub) die('sql error');
    
    while ($row_sub = $result_sub->fetch_assoc()) {
      for ($i = $from; $i <= $to and $i< $count; $i++) {
        if ($outdata_this[$i]['id_task'] == $row_sub['task_id']) {
          if (isset($outdata_this[$i]['owners']) == false) $outdata_this[$i]['owners'] = array();
          $outdata_this[$i]['owners'][] = array('id' => $row_sub['id_person'], 'name' => $row_sub['person_name']);
          break;  
        }
      }
    }
  
    $sql_sub="SELECT gks_tasks_languages.task_id, gks_languages.id_language, gks_languages.language_name
    FROM gks_tasks_languages LEFT JOIN gks_languages ON gks_tasks_languages.language_id = gks_languages.id_language
    WHERE (((gks_tasks_languages.task_id) In (".$row_subdata_in.")) AND ((gks_languages.id_language)>0))
    ORDER BY gks_languages.language_name;";
    $result_sub = $db_link->query($sql_sub);        
    if (!$result_sub) debug_mail(false,'error sql',$sql_sub);
    if (!$result_sub) die('sql error');
    
    while ($row_sub = $result_sub->fetch_assoc()) {
      for ($i = $from; $i <= $to and $i< $count; $i++) {
        if ($outdata_this[$i]['id_task'] == $row_sub['task_id']) {
          if (isset($outdata_this[$i]['languages']) == false) $outdata_this[$i]['languages'] = array();
          $outdata_this[$i]['languages'][] = array('id' => $row_sub['id_language'], 'name' => $row_sub['language_name']);
          break;  
        }
      }
    }
  
    $sql_sub="SELECT gks_tasks_series.task_id, gks_series.id_series, gks_series.series_name
    FROM gks_tasks_series LEFT JOIN gks_series ON gks_tasks_series.series_id = gks_series.id_series
    WHERE (((gks_tasks_series.task_id) In (".$row_subdata_in.")) AND ((gks_series.id_series)>0))
    ORDER BY gks_series.series_name";
    $result_sub = $db_link->query($sql_sub);        
    if (!$result_sub) debug_mail(false,'error sql',$sql_sub);
    if (!$result_sub) die('sql error');
    
    while ($row_sub = $result_sub->fetch_assoc()) {
      for ($i = $from; $i <= $to and $i< $count; $i++) {
        if ($outdata_this[$i]['id_task'] == $row_sub['task_id']) {
          if (isset($outdata_this[$i]['series']) == false) $outdata_this[$i]['series'] = array();
          $outdata_this[$i]['series'][] = array('id' => $row_sub['id_series'], 'name' => $row_sub['series_name']);
          break;  
        }
      }
    }
  
    $sql_sub="SELECT gks_tasks_tags.task_id, gks_tags.id_tag, gks_tags.tag_name
    FROM gks_tasks_tags LEFT JOIN gks_tags ON gks_tasks_tags.tag_id = gks_tags.id_tag
    WHERE (((gks_tasks_tags.task_id) In (".$row_subdata_in.")) AND ((gks_tags.id_tag)>0))
    ORDER BY gks_tags.tag_name;";
    $result_sub = $db_link->query($sql_sub);        
    if (!$result_sub) debug_mail(false,'error sql',$sql_sub);
    if (!$result_sub) die('sql error');
    
    while ($row_sub = $result_sub->fetch_assoc()) {
      for ($i = $from; $i <= $to and $i< $count; $i++) {
        if ($outdata_this[$i]['id_task'] == $row_sub['task_id']) {
          if (isset($outdata_this[$i]['tags']) == false) $outdata_this[$i]['tags'] = array();
          $outdata_this[$i]['tags'][] = array('id' => $row_sub['id_tag'], 'name' => $row_sub['tag_name']);
          break;  
        }
      }
    }
    
    $sql_sub="SELECT gks_tasks_files.task_id, Count(gks_tasks_files.id_tasks_file) AS cc
    FROM gks_tasks_files
    WHERE (((gks_tasks_files.task_id) In (".$row_subdata_in.")) AND ((gks_tasks_files.Deleted)=0) AND ((gks_tasks_files.IsTest)=0))
    GROUP BY gks_tasks_files.task_id";
    $result_sub = $db_link->query($sql_sub);        
    if (!$result_sub) debug_mail(false,'error sql',$sql_sub);
    if (!$result_sub) die('sql error');
    
    while ($row_sub = $result_sub->fetch_assoc()) {
      for ($i = $from; $i <= $to and $i< $count; $i++) {
        if ($outdata_this[$i]['id_task'] == $row_sub['task_id']) {
          $outdata_this[$i]['pages'] = $row_sub['cc'];
          break;  
        }
      }
    }



    // projects fields
    $sql_sub="SELECT gks_projects_fields.project_id, gks_projects_fields.fieldname, gks_projects_fields.fieldcaption
    FROM gks_tasks LEFT JOIN gks_projects_fields ON gks_tasks.project_id = gks_projects_fields.project_id
    WHERE gks_tasks.id_task in (".$row_subdata_in.") AND gks_projects_fields.id_project_field Is Not Null
    GROUP BY gks_projects_fields.project_id, gks_projects_fields.fieldname, gks_projects_fields.fieldcaption
    ORDER BY gks_projects_fields.project_id, gks_projects_fields.fieldname;";
    $result_sub = $db_link->query($sql_sub);        
    if (!$result_sub) debug_mail(false,'error sql',$sql_sub);
    if (!$result_sub) die('sql error');
    
    while ($row_sub = $result_sub->fetch_assoc()) {
      $project_fields[$row_sub['project_id']][$row_sub['fieldname']] = $row_sub['fieldcaption'];
    }    



    

    for ($i = $from; $i <= $to and $i< $count; $i++) {
      $downloadUrls=array();
      get_item_urls($outdata_this[$i]['Title'], $outdata_this[$i]['FullDir'], $downloadUrls);
      $outdata_this[$i]['downloadUrls'] = $downloadUrls;
    }
  }
}

function utf8_urldecode($str) {
  $str = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($str));
  return html_entity_decode($str,null,'UTF-8');;
}

function get_search_terms($term) {
  //$term=str_replace(',', '', $term);
  //$term=str_replace('.', '', $term);

  $myterms=array();
  do {
    if (preg_match('/"([^"]+)"/', $term, $m)) {
      $myterms[]=trim_gks($m[1]);
      $term=str_replace($m[0], ' ', $term);
    } else {
      break;
    }
    
  } while (true);
  for ($i=1;$i<=10;$i++){
    $term=$term=str_replace('  ', ' ', $term);
  }
  $term=explode(' ', $term);
  foreach ($term as $value) {
    $value=trim_gks($value);
    if ($value!='') $myterms[] = trim_gks($value);
  }
  return $myterms;
}




function rrmdir($dir) {
  if (is_dir($dir)) {
    $objects = scandir($dir);
    foreach ($objects as $object) {
      if ($object != "." && $object != "..") {
        if (filetype($dir."/".$object) == "dir") 
           rrmdir($dir."/".$object); 
        else unlink   ($dir."/".$object);
      }
    }
    reset($objects);
    rmdir($dir);
  }
}
 
function nav_active($data) {
  global $nav_active_array;
  if (!is_array($nav_active_array)) return '';
  if ($data == '') return '';
  if (in_array($data, $nav_active_array)) return ' active ';
  return '';
} 



function gks_get_list_bank_accounts() {
  global $db_link;
  global $my_wp_user_info;        
  
  $sql="SELECT gks_bank_accounts.*, gks_banks.bank_descr
  FROM gks_bank_accounts 
  LEFT JOIN gks_banks ON gks_bank_accounts.bank_id = gks_banks.id_bank
  WHERE gks_bank_accounts.show_eshop<>0 
  and gks_bank_accounts.IBAN <>'' 
  and gks_banks.id_bank is not null
  and user_id=0
  ORDER BY gks_bank_accounts.account_descr;";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return '';
  }
  $mnyret='';
  while ($row = $result->fetch_assoc()) {
    $iban = iban_to_machine_format($row['IBAN']);
    
    if(verify_iban($iban)) {
      $mnyret.= iban_to_human_format($iban);
    } else {
      $mnyret.= $row['IBAN'];
    }    
    
    $mnyret.=' '.gks_lang('Δικαιούχος').': '.$row['account_dikaiouxos']. ', '. $row['bank_descr'].'<br>';
  }
  if ($mnyret<>'') {
    $mnyret=substr($mnyret,0,strlen($mnyret)-4); 
  }
  return $mnyret;
}

function myimg01($val) {
  if (empty($val)) return '';
  if ($val==0) return '';
  //return '<img src="img/1.png" border="0" width="16">';
  return '<i class="gks_myimg01_1 fas fa-check-circle"></i>';
}
function myimg01r($val) {
  if (empty($val)) $val=0;
  if ($val!=0) return '';
  //return '<img src="img/1.png" border="0" width="16">';
  return '<i class="gks_myimg01_1 fas fa-check-circle"></i>';
}
function myimg010($val) {
  if (empty($val)) $val=0;
  if ($val!=0) {
    //return '<img src="img/1.png" border="0" width="16">';
    return '<i class="gks_myimg01_1 fas fa-check-circle"></i>';
  }
  //return '<img src="img/0.png" border="0" width="16">';
  return '<i class="gks_myimg01_0 fas fa-check-circle"></i>';
}
function myimg010r($val) {
  if (empty($val)) $val=0;
  if ($val==0) {
    //return '<img src="img/1.png" border="0" width="16">';
    return '<i class="gks_myimg01_1 fas fa-check-circle"></i>';
  }
  //return '<img src="img/0.png" border="0" width="16">';
  return '<i class="gks_myimg01_0 fas fa-check-circle"></i>';
}



function order_item_sort_dirs_files($a, $b) {
  if ($a['is_dir'] != $b['is_dir']) {
    if ($a['is_dir'] > $b['is_dir']) return 1;
    return -1;
  }
  $c = new Collator('el_GR');
  return $c->compare($a['val'], $b['val']);
  
}
                        
       

function time_duration_format($myval_in_secs, $fromtime=0) {
  
  if ($myval_in_secs==0) return '';
  if ($fromtime==0) $fromtime = time();
  $date = new \DateTime();
  $date->setTimestamp($fromtime - $myval_in_secs);
  $ffdate= new \DateTime();
  $ffdate->setTimestamp($fromtime);
  $interval = $date->diff($ffdate);
  //https://www.php.net/manual/en/dateinterval.format.php
  $myd_year = intval($interval->format('%y'));
  $myd_mont = intval($interval->format('%m'));
  $myd_days = intval($interval->format('%d'));
  $myd_hour = $interval->format('%H');
  $myd_mins = $interval->format('%I');
  $myd_secs = $interval->format('%S');
  $myecho ='';
  if ($myd_year>0)      $myecho=$myd_year.'-'.$myd_mont.'-'.$myd_days.'.'.$myd_hour.':'.$myd_mins.':'.$myd_secs;
  else if ($myd_mont>0) $myecho=              $myd_mont.'-'.$myd_days.'.'.$myd_hour.':'.$myd_mins.':'.$myd_secs;
  else if ($myd_days>0) $myecho=                            $myd_days.'.'.$myd_hour.':'.$myd_mins.':'.$myd_secs;
  else if ($myd_hour>0) $myecho=                                          $myd_hour.':'.$myd_mins.':'.$myd_secs;
  else                  $myecho=                                                        $myd_mins.':'.$myd_secs;

  return $myecho;  
  
}

function file_upload_max_size() {
  static $max_size = -1;

  if ($max_size < 0) {
    // Start with post_max_size.
    $post_max_size = parse_size(ini_get('post_max_size'));
    if ($post_max_size > 0) {
      $max_size = $post_max_size;
    }

    // If upload_max_size is less, then reduce. Except if upload_max_size is
    // zero, which indicates no limit.
    $upload_max = parse_size(ini_get('upload_max_filesize'));
    if ($upload_max > 0 && $upload_max < $max_size) {
      $max_size = $upload_max;
    }
  }
  return $max_size;
}

function parse_size($size) {
  $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
  $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
  if ($unit) {
    // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
    return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
  }
  else {
    return round($size);
  }
}

function db_open_only() {
  global $db_link;
  if ($db_link!==null) return;  
  $db_link = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  if ($db_link->connect_error) {
      debug_mail(true,"System error",$db_link->connect_errno . '-'.$db_link->connect_error);
  }
  $db_link->set_charset('utf8mb4');
  $db_link->query("SET time_zone = '+00:00';");  
}
 
function db_open() {
  global $db_link;
  if ($db_link!==null) return;
  $db_link = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  global $my_wp_user_id;
  global $gks_user_settings;
  global $gks_menu_version;
  global $gks_cache_version;
  global $GKS_CACHE_DB_VER;
  global $gks_user_cache_version_prefix;
  
  if ($db_link->connect_error) {
      debug_mail(true,"System error",$db_link->connect_errno . '-'.$db_link->connect_error);
  }
  $db_link->set_charset('utf8mb4');
  $db_link->query("SET time_zone = '+00:00';");
  
  
  $cache_dir=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/cache/';
  if (file_exists($cache_dir)==false) {
    if (@mkdir($cache_dir, 0755, true) == false ) {
      debug_mail(false,'can not create dir cache',$cache_dir);
      die('error can not create dir cache');
    }
  }
  load_settings();
  $gks_user_settings = gks_get_user_settings($my_wp_user_id);
  
  $gks_menu_version=0;
  $sql_menu_version="select gks_menu_version from ".GKS_WP_TABLE_PREFIX."users where ID=".$my_wp_user_id;
  $result_menu_version = $db_link->query($sql_menu_version);      
  if (!$result_menu_version) {
    debug_mail(false,'error sql',$sql_menu_version);
    die();
  }
  while ($row_menu_version = $result_menu_version->fetch_assoc()) {
    $gks_menu_version=$row_menu_version['gks_menu_version'];
  }

  $gks_user_cache_version_prefix = $GKS_CACHE_DB_VER.'_'.$gks_cache_version.'_'.$my_wp_user_id.'_'.$gks_menu_version.'_';
  
  gks_cache_common_js();
  gks_cache_country();
  gks_cache_lang();
  gks_cache_lang_data();
  
  gks_plugins_load();
}
function db_close() {
  global $db_link;
  $db_link->close();
  $db_link = null;
}

function load_settings() {
  global $db_link;
  
  global $GKS_USERS_ACCESS_ROLES;
  global $GKS_SITE_HUMAN_NAME;
  global $GKS_OFFICIAL_SITE_URL;
  global $GKS_SITE_NAME;
  global $GKS_SITE_EMAIL;
  global $GKS_EMAIL_BCC1;
  global $GKS_EMAIL_BCC2;
  global $GKS_EMAIL_BCC3;
  global $GKS_EMAIL_HOST;
  global $GKS_EMAIL_PORT;
  global $GKS_EMAIL_SMTPAUTH;
  global $GKS_EMAIL_USERNAME;
  global $GKS_EMAIL_PASSWORD;

  global $GKS_ORDERS_AWS;
  global $GKS_ORDERS_SETS;
  global $GKS_ORDERS_SETS_VALS;
  global $GKS_ORDERS_SHEETS;
  global $GKS_ORDERS_COL_ITEMPRICE;
  global $GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA;
  global $GKS_ORDERS_COL_FPA;

  
  global $GKS_ACC_INV_COL_ITEMPRICE;
  global $GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA;
  global $GKS_ACC_INV_COL_FPA;
  global $GKS_ACC_INV_EXTRA_OPEN;
  

  global $GKS_PRODUCT_DESCR_SMALL;
  global $GKS_PRODUCT_DESCR_BIG;
    

  global $GKS_ORDERS_ENABLE;
  global $GKS_ORDERS_OCCASION;
  global $GKS_ORDERS_PRODUCTION;
  global $GKS_CRM_ENABLE;
  global $GKS_CRM_LEADS_ENABLE;
  global $GKS_CRM_TASKS_ENABLE;
  global $GKS_CRM_MACHINE_ENABLE;
  global $GKS_ACC_ENABLE;  
  
  global $GKS_WARE_HOUSE_ENABLE;
  global $GKS_PRODUCT_LOTS_SERIALS;
  
  global $GKS_ASSETS_ENABLE;
  global $GKS_BASKET_CALC_ITEM_DECIMAL;
  global $GKS_BASKET_CALC_EKPTOSI_DECIMAL;
  
  
  global $GKS_NUMBER_FORMAT_DECIMAL;
  global $GKS_NUMBER_FORMAT_THOUSAND;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL;
  global $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW;
  global $GKS_NUMBER_FORMAT_DATE;
  global $GKS_NUMBER_FORMAT_TIME;
  
  global $GKS_HOTEL_DAYS_FUTURE;

  global $GKS_PAYPAL_REAL_USERNAME;
  global $GKS_PAYPAL_REAL_PASSWORD;
  global $GKS_PAYPAL_REAL_SIGNATURE;
  global $GKS_PAYPAL_REAL_CLIENT_ID;
  global $GKS_PAYPAL_REAL_SECRET;
  global $GKS_PAYPAL_SANDBOX;
  global $GKS_PAYPAL_SAND_USERNAME;
  global $GKS_PAYPAL_SAND_PASSWORD;
  global $GKS_PAYPAL_SAND_SIGNATURE;
  global $GKS_PAYPAL_SAND_CLIENT_ID;
  global $GKS_PAYPAL_SAND_SECRET;
  
  global $GKS_ALPHABANK_REAL_MID;
  global $GKS_ALPHABANK_REAL_KEY;
  global $GKS_ALPHABANK_REAL_URL;
  global $GKS_ALPHABANK_SAND_MID;
  global $GKS_ALPHABANK_SAND_KEY;
  global $GKS_ALPHABANK_SAND_URL;



  global $GKS_PIRAEUSBANK_SAND_AcquirerID;
  global $GKS_PIRAEUSBANK_SAND_MerchantID;
  global $GKS_PIRAEUSBANK_SAND_PosID;
  global $GKS_PIRAEUSBANK_SAND_UserName;
  global $GKS_PIRAEUSBANK_SAND_Password;
  global $GKS_PIRAEUSBANK_SANDBOX;
  global $GKS_PIRAEUSBANK_REAL_AcquirerID;
  global $GKS_PIRAEUSBANK_REAL_MerchantID;
  global $GKS_PIRAEUSBANK_REAL_PosID;
  global $GKS_PIRAEUSBANK_REAL_UserName;
  global $GKS_PIRAEUSBANK_REAL_Password;

  global $GKS_AWS_BUCKET;
  global $GKS_AWS_KEY;
  global $GKS_AWS_SECRET;
  global $GKS_AWS_FOLDER;
  
  global $GKS_SEND_ANYWHERE_API_KEY;

  global $GKS_ORDER_DEFAULT_DELIVERY;
  global $GKS_ORDER_DEFAULT_PAYMENT;
  global $GKS_ORDER_DEFAULT_PAYMENT_HOTEL;
  global $GKS_ORDER_DEFAULT_PAYMENT_TRANSFER;
  global $GKS_GOOGLE_MAPS_API_KEY;
  global $GKS_GOOGLE_MAPS_API_KEY_SERVER;
  
  
  global $GKS_CACHE_DB_VER;
  global $GKS_IDIOTITES_CACHE_VER;
  
  global $GKS_NOT_CHANGE_DELIVERY_COST_AT_CLICK;
  global $GKS_NOT_CHANGE_PAYMENT_COST_AT_CLICK;
  global $GKS_BASKET_ROUND_DIAFORA_001;
  global $GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_LIANIKI;
  global $GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_XONDRIKI;
  global $GKS_INPUT_STEP_AJIA;
  global $GKS_INPUT_STEP_POSOTITA;  
  global $GKS_INPUT_STEP_POSOSTO;  
  
  global $GKS_AADE_MYDATA_SANDBOX_AFM;  
  global $GKS_AADE_MYDATA_SANDBOX_BRANCE;  
  global $GKS_AADE_MYDATA_SANDBOX_USER_ID;  
  global $GKS_AADE_MYDATA_SANDBOX_SUBSCRIPTION_KEY;  

  global $GKS_HOTEL_BACKEND;
  global $GKS_HOTEL_RESERVATIONS_ONLINE;


  global $GKS_SMS_SENDER;
  global $GKS_SMS_TOKEN;
  
  global $GKS_VIBER_URI;
  global $GKS_VIBER_TOKEN;
  global $GKS_LANG_DEFAULT;
  global $GKS_LANG_DEFAULT_DB;
  global $GKS_LANG_DATA_ENABLED;
  global $GKS_PLUGINS_ENABLED;
  global $GKS_ERP_CRON_LAST_RUN;
  global $GKS_ERP_APP_DEF_TIMEZONE;
  global $GKS_ERP_APP_PURCHASE_DATA;
  
  
  $sql="SELECT * FROM gks_settings where mykey<>'' and myvalue<>''";
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  $GKS_USERS_ACCESS_ROLES_str='';
  $GKS_PLUGINS_ENABLED_str='';
  while ($row = $result->fetch_assoc()) {
    switch ($row['mykey']) {   
      
      case 'GKS_USERS_ACCESS_ROLES': $GKS_USERS_ACCESS_ROLES_str=trim_gks($row['myvalue']); break;
      case 'GKS_SITE_HUMAN_NAME': $GKS_SITE_HUMAN_NAME=trim_gks($row['myvalue']); break;
      case 'GKS_OFFICIAL_SITE_URL': $GKS_OFFICIAL_SITE_URL=trim_gks($row['myvalue']); break;
      case 'GKS_SITE_NAME': $GKS_SITE_NAME=trim_gks($row['myvalue']); break;
      case 'GKS_SITE_EMAIL': $GKS_SITE_EMAIL=trim_gks($row['myvalue']); break;
      case 'GKS_EMAIL_BCC1': $GKS_EMAIL_BCC1=trim_gks($row['myvalue']); break;
      case 'GKS_EMAIL_BCC2': $GKS_EMAIL_BCC2=trim_gks($row['myvalue']); break;
      case 'GKS_EMAIL_BCC3': $GKS_EMAIL_BCC3=trim_gks($row['myvalue']); break;
      case 'GKS_EMAIL_HOST': $GKS_EMAIL_HOST=trim_gks($row['myvalue']); break;
      case 'GKS_EMAIL_PORT': $GKS_EMAIL_PORT=intval($row['myvalue']); break;
      case 'GKS_EMAIL_SMTPAUTH': $GKS_EMAIL_SMTPAUTH=(trim_gks($row['myvalue']) == 'false' ? false : true); break;
      case 'GKS_EMAIL_USERNAME': $GKS_EMAIL_USERNAME=trim_gks($row['myvalue']); break;
      case 'GKS_EMAIL_PASSWORD': $GKS_EMAIL_PASSWORD=trim_gks($row['myvalue']); break;


      case 'GKS_ORDERS_SETS': $GKS_ORDERS_SETS=(trim_gks($row['myvalue']) == 'false' ? false : true); break;
      case 'GKS_ORDERS_SETS_VALS': $GKS_ORDERS_SETS_VALS=trim_gks($row['myvalue']); break;
      case 'GKS_ORDERS_SHEETS': $GKS_ORDERS_SHEETS=(trim_gks($row['myvalue']) == 'false' ? false : true); break;
      case 'GKS_ORDERS_COL_ITEMPRICE': $GKS_ORDERS_COL_ITEMPRICE=(trim_gks($row['myvalue']) == 'false' ? false : true); break;
      case 'GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA': $GKS_ORDERS_COL_ITEMPRICE_CHECK_FPA=(trim_gks($row['myvalue']) == 'false' ? false : true); break;
      case 'GKS_ORDERS_COL_FPA': $GKS_ORDERS_COL_FPA=(trim_gks($row['myvalue']) == 'false' ? false : true); break;

      
      case 'GKS_ACC_INV_COL_ITEMPRICE': $GKS_ACC_INV_COL_ITEMPRICE=(trim_gks($row['myvalue']) == 'false' ? false : true); break;
      case 'GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA': $GKS_ACC_INV_COL_ITEMPRICE_CHECK_FPA=(trim_gks($row['myvalue']) == 'false' ? false : true); break;
      case 'GKS_ACC_INV_COL_FPA': $GKS_ACC_INV_COL_FPA=(trim_gks($row['myvalue']) == 'false' ? false : true); break;
      case 'GKS_ACC_INV_EXTRA_OPEN': $GKS_ACC_INV_EXTRA_OPEN=(trim_gks($row['myvalue']) == 'false' ? false : true); break;


      case 'GKS_PRODUCT_DESCR_SMALL': $GKS_PRODUCT_DESCR_SMALL=(trim_gks($row['myvalue']) == 'false' ? false : true); break;
      case 'GKS_PRODUCT_DESCR_BIG': $GKS_PRODUCT_DESCR_BIG=(trim_gks($row['myvalue']) == 'false' ? false : true); break;

        
      case 'GKS_ORDERS_ENABLE': $GKS_ORDERS_ENABLE=(trim_gks($row['myvalue']) == 'false' ? false : true); break;
      case 'GKS_ORDERS_OCCASION': $GKS_ORDERS_OCCASION=(trim_gks($row['myvalue']) == 'false' ? false : true); break;
      case 'GKS_ORDERS_PRODUCTION': $GKS_ORDERS_PRODUCTION=(trim_gks($row['myvalue']) == 'false' ? false : true); break;
      case 'GKS_CRM_ENABLE': $GKS_CRM_ENABLE=(trim_gks($row['myvalue']) == 'false' ? false : true); break;
      case 'GKS_CRM_LEADS_ENABLE': $GKS_CRM_LEADS_ENABLE=(trim_gks($row['myvalue']) == 'false' ? false : true); break;
      case 'GKS_CRM_TASKS_ENABLE': $GKS_CRM_TASKS_ENABLE=(trim_gks($row['myvalue']) == 'false' ? false : true); break;
      case 'GKS_CRM_MACHINE_ENABLE': $GKS_CRM_MACHINE_ENABLE=(trim_gks($row['myvalue']) == 'false' ? false : true); break;
      
      
      case 'GKS_ACC_ENABLE': $GKS_ACC_ENABLE=(trim_gks($row['myvalue']) == 'false' ? false : true); break;

      case 'GKS_WARE_HOUSE_ENABLE': $GKS_WARE_HOUSE_ENABLE=(trim_gks($row['myvalue']) == 'false' ? false : true); break;
      case 'GKS_PRODUCT_LOTS_SERIALS': $GKS_PRODUCT_LOTS_SERIALS=(trim_gks($row['myvalue']) == 'false' ? false : true); break;

      case 'GKS_ASSETS_ENABLE': $GKS_ASSETS_ENABLE=(trim_gks($row['myvalue']) == 'false' ? false : true); break;

  

      
      case 'GKS_BASKET_CALC_ITEM_DECIMAL': $GKS_BASKET_CALC_ITEM_DECIMAL=intval($row['myvalue']); break;  
      case 'GKS_BASKET_CALC_EKPTOSI_DECIMAL': $GKS_BASKET_CALC_EKPTOSI_DECIMAL=intval($row['myvalue']); break;  
      
      case 'GKS_NUMBER_FORMAT_DECIMAL': $GKS_NUMBER_FORMAT_DECIMAL=$row['myvalue']; break;  
      case 'GKS_NUMBER_FORMAT_THOUSAND': $GKS_NUMBER_FORMAT_THOUSAND=$row['myvalue']; break;  
      case 'GKS_NUMBER_FORMAT_CURRENCY_DECIMAL': $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL=intval($row['myvalue']); break;  
      case 'GKS_NUMBER_FORMAT_CURRENCY_SYMBOL': $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL=$row['myvalue']; break;  
      case 'GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW': $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW=$row['myvalue']; break;  
      case 'GKS_NUMBER_FORMAT_DATE': $GKS_NUMBER_FORMAT_DATE=$row['myvalue']; break;  
      case 'GKS_NUMBER_FORMAT_TIME': $GKS_NUMBER_FORMAT_TIME=$row['myvalue']; break;  
      
      case 'GKS_HOTEL_DAYS_FUTURE': $GKS_HOTEL_DAYS_FUTURE=intval($row['myvalue']); break;  

      case 'GKS_PAYPAL_REAL_USERNAME':  $GKS_PAYPAL_REAL_USERNAME=trim_gks($row['myvalue']); break;  
      case 'GKS_PAYPAL_REAL_PASSWORD':  $GKS_PAYPAL_REAL_PASSWORD=trim_gks($row['myvalue']); break;  
      case 'GKS_PAYPAL_REAL_SIGNATURE': $GKS_PAYPAL_REAL_SIGNATURE=trim_gks($row['myvalue']); break;  
      case 'GKS_PAYPAL_REAL_CLIENT_ID': $GKS_PAYPAL_REAL_CLIENT_ID=trim_gks($row['myvalue']); break;  
      case 'GKS_PAYPAL_REAL_SECRET':    $GKS_PAYPAL_REAL_SECRET=trim_gks($row['myvalue']); break;  
      case 'GKS_PAYPAL_SANDBOX':        $GKS_PAYPAL_SANDBOX=(trim_gks($row['myvalue']) == 'false' ? false : true); break;  
      case 'GKS_PAYPAL_SAND_USERNAME':  $GKS_PAYPAL_SAND_USERNAME=trim_gks($row['myvalue']); break;  
      case 'GKS_PAYPAL_SAND_PASSWORD':  $GKS_PAYPAL_SAND_PASSWORD=trim_gks($row['myvalue']); break;  
      case 'GKS_PAYPAL_SAND_SIGNATURE': $GKS_PAYPAL_SAND_SIGNATURE=trim_gks($row['myvalue']); break;  
      case 'GKS_PAYPAL_SAND_CLIENT_ID': $GKS_PAYPAL_SAND_CLIENT_ID=trim_gks($row['myvalue']); break;  
      case 'GKS_PAYPAL_SAND_SECRET':    $GKS_PAYPAL_SAND_SECRET=trim_gks($row['myvalue']); break;  

      case 'GKS_ALPHABANK_REAL_MID': $GKS_ALPHABANK_REAL_MID=trim_gks($row['myvalue']); break;  
      case 'GKS_ALPHABANK_REAL_KEY': $GKS_ALPHABANK_REAL_KEY=trim_gks($row['myvalue']); break;  
      case 'GKS_ALPHABANK_REAL_URL': $GKS_ALPHABANK_REAL_URL=trim_gks($row['myvalue']); break;  
      case 'GKS_ALPHABANK_SAND_MID': $GKS_ALPHABANK_SAND_MID=trim_gks($row['myvalue']); break;  
      case 'GKS_ALPHABANK_SAND_KEY': $GKS_ALPHABANK_SAND_KEY=trim_gks($row['myvalue']); break;  
      case 'GKS_ALPHABANK_SAND_URL': $GKS_ALPHABANK_SAND_URL=trim_gks($row['myvalue']); break;  

   
      case 'GKS_PIRAEUSBANK_SAND_AcquirerID': $GKS_PIRAEUSBANK_SAND_AcquirerID=trim_gks($row['myvalue']); break;  
      case 'GKS_PIRAEUSBANK_SAND_MerchantID': $GKS_PIRAEUSBANK_SAND_MerchantID=trim_gks($row['myvalue']); break;  
      case 'GKS_PIRAEUSBANK_SAND_PosID':      $GKS_PIRAEUSBANK_SAND_PosID=trim_gks($row['myvalue']); break;  
      case 'GKS_PIRAEUSBANK_SAND_UserName':   $GKS_PIRAEUSBANK_SAND_UserName=trim_gks($row['myvalue']); break;  
      case 'GKS_PIRAEUSBANK_SAND_Password':   $GKS_PIRAEUSBANK_SAND_Password=trim_gks($row['myvalue']); break;  
      case 'GKS_PIRAEUSBANK_SANDBOX':         $GKS_PIRAEUSBANK_SANDBOX=(trim_gks($row['myvalue']) == 'false' ? false : true); break;  
      case 'GKS_PIRAEUSBANK_REAL_AcquirerID': $GKS_PIRAEUSBANK_REAL_AcquirerID=trim_gks($row['myvalue']); break;  
      case 'GKS_PIRAEUSBANK_REAL_MerchantID': $GKS_PIRAEUSBANK_REAL_MerchantID=trim_gks($row['myvalue']); break;  
      case 'GKS_PIRAEUSBANK_REAL_PosID':      $GKS_PIRAEUSBANK_REAL_PosID=trim_gks($row['myvalue']); break;  
      case 'GKS_PIRAEUSBANK_REAL_UserName':   $GKS_PIRAEUSBANK_REAL_UserName=trim_gks($row['myvalue']); break;  
      case 'GKS_PIRAEUSBANK_REAL_Password':   $GKS_PIRAEUSBANK_REAL_Password=trim_gks($row['myvalue']); break;  

      case 'GKS_AWS_BUCKET':                  $GKS_AWS_BUCKET=trim_gks($row['myvalue']); break;  
      case 'GKS_AWS_KEY':                     $GKS_AWS_KEY=trim_gks($row['myvalue']); break;  
      case 'GKS_AWS_SECRET':                  $GKS_AWS_SECRET=trim_gks($row['myvalue']); break;  
      case 'GKS_AWS_FOLDER':                  $GKS_AWS_FOLDER=trim_gks($row['myvalue']); break;  

      case 'GKS_SEND_ANYWHERE_API_KEY':       $GKS_SEND_ANYWHERE_API_KEY=trim_gks($row['myvalue']); break;  

      case 'GKS_ORDER_DEFAULT_DELIVERY':      $GKS_ORDER_DEFAULT_DELIVERY=intval($row['myvalue']); break;  
      case 'GKS_ORDER_DEFAULT_PAYMENT':       $GKS_ORDER_DEFAULT_PAYMENT=intval($row['myvalue']); break;  
      case 'GKS_ORDER_DEFAULT_PAYMENT_HOTEL': $GKS_ORDER_DEFAULT_PAYMENT_HOTEL=intval($row['myvalue']); break;  
      case 'GKS_ORDER_DEFAULT_PAYMENT_TRANSFER': $GKS_ORDER_DEFAULT_PAYMENT_TRANSFER=intval($row['myvalue']); break;  
      case 'GKS_GOOGLE_MAPS_API_KEY':         $GKS_GOOGLE_MAPS_API_KEY=trim_gks($row['myvalue']); break;  
      case 'GKS_GOOGLE_MAPS_API_KEY_SERVER':  $GKS_GOOGLE_MAPS_API_KEY_SERVER=trim_gks($row['myvalue']); break;  

      case 'GKS_CACHE_DB_VER':                $GKS_CACHE_DB_VER=intval($row['myvalue']); break;  
      case 'GKS_IDIOTITES_CACHE_VER':         $GKS_IDIOTITES_CACHE_VER=intval($row['myvalue']); break;  

      case 'GKS_NOT_CHANGE_DELIVERY_COST_AT_CLICK':    $GKS_NOT_CHANGE_DELIVERY_COST_AT_CLICK=(trim_gks($row['myvalue']) == 'false' ? false : true); break;  
      case 'GKS_NOT_CHANGE_PAYMENT_COST_AT_CLICK':    $GKS_NOT_CHANGE_PAYMENT_COST_AT_CLICK=(trim_gks($row['myvalue']) == 'false' ? false : true); break;  
      case 'GKS_BASKET_ROUND_DIAFORA_001':    $GKS_BASKET_ROUND_DIAFORA_001=(trim_gks($row['myvalue']) == 'false' ? false : true); break;  
      case 'GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_LIANIKI':  $GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_LIANIKI= (trim_gks($row['myvalue']) == 'false' ? false : true); break;  
      case 'GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_XONDRIKI': $GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_XONDRIKI=(trim_gks($row['myvalue']) == 'false' ? false : true); break;  

      case 'GKS_INPUT_STEP_AJIA':             $GKS_INPUT_STEP_AJIA=trim_gks($row['myvalue']); break;  
      case 'GKS_INPUT_STEP_POSOTITA':         $GKS_INPUT_STEP_POSOTITA=trim_gks($row['myvalue']); break;  
      case 'GKS_INPUT_STEP_POSOSTO':          $GKS_INPUT_STEP_POSOSTO=trim_gks($row['myvalue']); break;  

      case 'GKS_AADE_MYDATA_SANDBOX_AFM':              $GKS_AADE_MYDATA_SANDBOX_AFM=trim_gks($row['myvalue']); break;  
      case 'GKS_AADE_MYDATA_SANDBOX_BRANCE':           $GKS_AADE_MYDATA_SANDBOX_BRANCE=intval($row['myvalue']); break;  
      case 'GKS_AADE_MYDATA_SANDBOX_USER_ID':          $GKS_AADE_MYDATA_SANDBOX_USER_ID=trim_gks($row['myvalue']); break;  
      case 'GKS_AADE_MYDATA_SANDBOX_SUBSCRIPTION_KEY': $GKS_AADE_MYDATA_SANDBOX_SUBSCRIPTION_KEY=trim_gks($row['myvalue']); break;  

      case 'GKS_HOTEL_BACKEND':             $GKS_HOTEL_BACKEND=(trim_gks($row['myvalue']) == 'false' ? false : true); break;
      case 'GKS_HOTEL_RESERVATIONS_ONLINE': $GKS_HOTEL_RESERVATIONS_ONLINE=(trim_gks($row['myvalue']) == 'false' ? false : true); break;


      case 'GKS_SMS_SENDER':                  $GKS_SMS_SENDER=trim_gks($row['myvalue']); break;  
      case 'GKS_SMS_TOKEN':                   $GKS_SMS_TOKEN=trim_gks($row['myvalue']); break;  

      case 'GKS_VIBER_URI':                   $GKS_VIBER_URI=trim_gks($row['myvalue']); break;  
      case 'GKS_VIBER_TOKEN':                 $GKS_VIBER_TOKEN=trim_gks($row['myvalue']); break;  

      case 'GKS_LANG_DEFAULT':                $GKS_LANG_DEFAULT=trim_gks($row['myvalue']); break;  
      case 'GKS_LANG_DATA_ENABLED':           $GKS_LANG_DATA_ENABLED=unserialize(trim_gks($row['myvalue'])); break;  
      case 'GKS_PLUGINS_ENABLED':             $GKS_PLUGINS_ENABLED_str=trim_gks($row['myvalue']); break;  
      case 'GKS_ERP_CRON_LAST_RUN':           $GKS_ERP_CRON_LAST_RUN=intval($row['myvalue']); break;
      case 'GKS_ERP_APP_DEF_TIMEZONE':        $GKS_ERP_APP_DEF_TIMEZONE=trim_gks($row['myvalue']); break;  

      case 'GKS_ERP_APP_PURCHASE_DATA':       $GKS_ERP_APP_PURCHASE_DATA=trim_gks($row['myvalue']); break;  

      default:      
    }
  }
  
  $GKS_LANG_DEFAULT_DB=str_replace('-','_',$GKS_LANG_DEFAULT);
  
  if ($GKS_ERP_APP_DEF_TIMEZONE=='') {
    $GKS_ERP_APP_DEF_TIMEZONE='Europe/Athens';  
  }
  
  if ($GKS_USERS_ACCESS_ROLES_str!='') {
    $GKS_USERS_ACCESS_ROLES=unserialize($GKS_USERS_ACCESS_ROLES_str);  
  }
  
  if ($GKS_PLUGINS_ENABLED_str!='') {
    $GKS_PLUGINS_ENABLED=json_decode($GKS_PLUGINS_ENABLED_str, true);
  }
  //echo '<pre>'; print_r($GKS_PLUGINS_ENABLED);die();
  
  //if ($GKS_ORDERS_ENABLE==false) {
  //  $GKS_ORDERS_OCCASION=false;
  //}
  
  if ($GKS_CRM_ENABLE==false) {
    $GKS_CRM_LEADS_ENABLE=false;
    $GKS_CRM_TASKS_ENABLE=false;
    $GKS_CRM_MACHINE_ENABLE=false;
  }
      
  
  if ($GKS_BASKET_CALC_ITEM_DECIMAL<0) $GKS_BASKET_CALC_ITEM_DECIMAL=0;
  if ($GKS_BASKET_CALC_EKPTOSI_DECIMAL<0) $GKS_BASKET_CALC_EKPTOSI_DECIMAL=0;
  if ($GKS_NUMBER_FORMAT_CURRENCY_DECIMAL<0) $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL=0;
  
  
  if ($GKS_AWS_BUCKET!='' and $GKS_AWS_KEY!='' and $GKS_AWS_SECRET!='') $GKS_ORDERS_AWS=true;
  
  gks_erp_app_purchase_func();

}
function gks_erp_app_purchase_func() {
  global $GKS_ERP_APP_PURCHASE_DATA;
  global $GKS_ERP_APP_PURCHASE_CODE;
  
  //$GKS_ERP_APP_PURCHASE_DATA=''; //deleteme
  if ($GKS_ERP_APP_PURCHASE_DATA!='') {
    $GKS_ERP_APP_PURCHASE_CODE=json_decode($GKS_ERP_APP_PURCHASE_DATA,true);
    //echo '<pre>ddd';die();
  }
  //print '<pre>';print_r($GKS_ERP_APP_PURCHASE_CODE);die();
  

/*
$GKS_ERP_APP_PURCHASE_CODE['url']=$_SERVER['HTTP_HOST'];
$GKS_ERP_APP_PURCHASE_CODE['register_email']='goutoudis@gmail.com';
$GKS_ERP_APP_PURCHASE_CODE['register_mobile']='+306971881406';
$GKS_ERP_APP_PURCHASE_CODE['purchase_codes']=[];
$GKS_ERP_APP_PURCHASE_CODE['purchase_codes']['ads']=array(
  'type'=>'ads',
  'code'=> '123234534987349-923845-293485',
  'valid' => true,
  'register_date'=>'2025-07-22 11:00:00',
  'expire_date'=>'2026-07-22 11:00:00',
);

print '<pre>';print json_encode($GKS_ERP_APP_PURCHASE_CODE);die();


Array
(
    [url] => test.easyfilesselection.com
    [register_email] => goutoudis@gmail.com
    [register_mobile] => +306971881406
    [purchase_codes] => Array
        (
            [ads] => Array
                (
                    [type] => ads
                    [code] => 123234534987349-923845-293485
                    [valid] => 1
                    [register_date] => 2025-07-22 11:00:00
                    [expire_date] => 2026-07-22 11:00:00
                )

        )

)
*/
  
  
  
}


















function makeThumbnails_normal($original, $thumb, $my_thumbnail_width, $my_thumbnail_height, $watermark = false)
{
  $width = $my_thumbnail_width;
  $height = $my_thumbnail_height;
  $width_def = $width;
  $height_def = $height;


  
  // Get new dimensions
  list($width_orig, $height_orig) = getimagesize($original);
  
  $ratio_orig = $width_orig/$height_orig;
  if ($width_orig > $width or $height_orig > $height) { // need resize
	  if ($width/$height > $ratio_orig) {
	     $width = $height*$ratio_orig;
	  } else {
	     $height = $width/$ratio_orig;
	  }
	  
  } else {
  	$width = $width_orig;
  	$height = $height_orig;
	}
	
  // Resample
  $image_p = imagecreatetruecolor($width, $height);
  $image = imagecreatefromjpeg($original);
  imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
  
  if ($watermark && $my_thumbnail_width == 300) {
    if ($width > $height) {
      $mywatermark = imagecreatefrompng(GKS_DATA . 'watermark/watermark300x200.png');
      imagecopy ($image_p, $mywatermark,($width-300)/2, ($height-200)/2, 0, 0, 300, 200);
    } else {
      $mywatermark = imagecreatefrompng(GKS_DATA . 'watermark/watermark200x300.png');
      imagecopy ($image_p, $mywatermark,($width-200)/2, ($height-300)/2, 0, 0, 200, 300);
    }
  }
  if ($watermark && $my_thumbnail_width == 1500) {
    if ($width > $height) {
      $mywatermark = imagecreatefrompng(GKS_DATA . 'watermark/watermark1500x1000.png');
      //imagecopy ($image_p, $mywatermark,($width-$width_def)/2, ($height-$height_def)/2, 0, 0, $width_def, $height_def);
      imagecopy ($image_p, $mywatermark,($width-1500)/2, ($height-1000)/2, 0, 0, 1500, 1000);
      
      //debug_mail(false,'3',$width.'-'.$width_def.'-'.$height.'-'.$height_def);
    } else {
      $mywatermark = imagecreatefrompng(GKS_DATA . 'watermark/watermark1000x1500.png');
      //imagecopy ($image_p, $mywatermark,($height-$height_def)/2, ($width-$width_def)/2, 0, 0, $height_def, $width_def);
      imagecopy ($image_p, $mywatermark,($width-1000)/2, ($height-1500)/2, 0, 0, 1000, 1500);
      //debug_mail(false,'4',$width.'-'.$width_def.'-'.$height.'-'.$height_def);
    }
  }
  
  // Output
  imagejpeg($image_p, $thumb, 85);
  imagedestroy( $image_p );
}


function makeThumbnails_square( $srcFile, $thumbFile, $thumbSize, $watermark = false)
{

  $image = new Imagick($srcFile);
  
  $orientation = $image->getImageOrientation();
  switch($orientation) {
      case imagick::ORIENTATION_BOTTOMRIGHT:
          $image->rotateimage("#000", 180); // rotate 180 degrees
      break;

      case imagick::ORIENTATION_RIGHTTOP:
          $image->rotateimage("#000", 90); // rotate 90 degrees CW
      break;

      case imagick::ORIENTATION_LEFTBOTTOM:
          $image->rotateimage("#000", -90); // rotate 90 degrees CCW
      break;
  }
    
  //crop and resize the image
  $image->cropThumbnailImage($thumbSize,$thumbSize);
  
  //remove the canvas
  $image->setImagePage(0, 0, 0, 0);

  $image->writeImage($thumbFile);
  $image->destroy();
  return;
  
  
  /* Determine the File Type */
  $type = substr( $srcFile , strrpos( $srcFile , '.' )+1 );
  /* Create the Source Image */
  switch( $type ){
    case 'jpg' : case 'jpeg' :
      $src = imagecreatefromjpeg( $srcFile ); break;
    case 'png' :
      $src = imagecreatefrompng( $srcFile ); break;
    case 'gif' :
      $src = imagecreatefromgif( $srcFile ); break;
  }
  /* Determine the Image Dimensions */
  $oldW = imagesx( $src );
  $oldH = imagesy( $src );
  
   /* Calculate the New Image Dimensions */
   $limiting_dim = 0;
    if( $oldH > $oldW ){
     /* Portrait */
      $limiting_dim = $oldW;
    }else{
     /* Landscape */
      $limiting_dim = $oldH;
    }
   /* Create the New Image */
    $new = imagecreatetruecolor( $thumbSize , $thumbSize );
   /* Transcribe the Source Image into the New (Square) Image */
    imagecopyresampled( $new , $src , 0 , 0 , ($oldW-$limiting_dim )/2 , ( $oldH-$limiting_dim )/2 , $thumbSize , $thumbSize , $limiting_dim , $limiting_dim );  
  if ($watermark && $thumbSize == 300) {
    $mywatermark = imagecreatefrompng(GKS_DATA . 'watermark/watermark300x300.png');
    imagecopy ($new, $mywatermark, ($thumbSize-300)/2, ($thumbSize-300)/2,0, 0, 300, 300);
  }
  if ($watermark && $thumbSize == 1500) {
    $mywatermark = imagecreatefrompng(GKS_DATA . 'watermark/watermark1500x1500.png');
    imagecopy ($new, $mywatermark, ($thumbSize-1500)/2, ($thumbSize-1500)/2,0, 0, 1500, 1500);
  }
  
  switch( $type ){
    case 'jpg' : case 'jpeg' :
      $src = imagejpeg( $new , $thumbFile, 85 ); break;
    case 'png' :
      $src = imagepng( $new , $thumbFile ); break;
    case 'gif' :
      $src = imagegif( $new , $thumbFile ); break;
  }
  imagedestroy( $new );

}

function image_fix_orientation($filename) {
    $exif = @exif_read_data($filename);
    if (!empty($exif['Orientation'])) {
        $image = imagecreatefromjpeg($filename);
        switch ($exif['Orientation']) {
            case 3:
                $image = imagerotate($image, 180, 0);
                break;

            case 6:
                $image = imagerotate($image, -90, 0);
                break;

            case 8:
                $image = imagerotate($image, 90, 0);
                break;
        }

        imagejpeg($image, $filename, 90);
    }
}


function startwith($string, $test) {
  return substr_compare($string, $test, 0, strlen($test)) === 0;
}
function endwith($string, $test) {
    $strlen = strlen($string);
    $testlen = strlen($test);
    if ($testlen > $strlen) return false;
    return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
}
//function startwith($haystack, $needle)
//{
//     $length = strlen($needle);
//     return (substr($haystack, 0, $length) === $needle);
//}
//
//function endwith($haystack, $needle)
//{
//    $length = strlen($needle);
//    if ($length == 0) {
//        return true;
//    }
//
//    return (substr($haystack, -$length) === $needle);
//}





function getProgramStateDescr($mystate) {
  switch ($mystate) {
    case 'draft': return gks_lang('Πρόχειρο'); break; 
    case 'valid': return gks_lang('Επιβεβαιωμένο'); break; 
    case 'processing': return gks_lang('Σε εξέλιξη'); break; 
    case 'pause': return gks_lang('Σε παύση'); break; 
    case 'complete': return gks_lang('Ολοκληρωμένο'); break; 
    case 'cancel': return gks_lang('Ακυρωμένο'); break; 
    default: return $mystate; break; 
  } 
}

function get_assets_whi_mov_descr($mystate) {
  switch ($mystate) {
    case '00draft': return gks_lang('Πρόχειρη'); break; 
    case '99complete': return gks_lang('Ολοκληρωμένη'); break; 
    default: return $mystate; break; 
  } 
}



function getUserPhoto($id, $myimgurl,$wh=64) {
  $myout='';  
  $myimgurl=trim_gks($myimgurl.'');
  if ($myimgurl == '') {
    $myimgurl="/my/img/avatar.png";
  }
  $myout='<a href="/my/admin-users-item.php?id='.$id.'"><img src="'.$myimgurl.'" border="0" style="max-width:'.$wh.'px;max-height:'.$wh.'px;"/></a>';
  
  return $myout;  
}

function getProductPhoto($id, $myimgurl,$wh=64) {
  $myout='';  
  $myimgurl=trim_gks($myimgurl.'');
  if ($myimgurl == '') {
    $myimgurl="/my/img/product.png";
  }
  $myout='<a href="/my/admin-products-item.php?id='.$id.'"><img src="'.$myimgurl.'" border="0" style="max-width:'.$wh.'px;max-height:'.$wh.'px;"/></a>';
  
  return $myout;  
}

function getCategoryPhoto($id, $myimgurl,$wh=64) {
  $myout='';  
  $myimgurl=trim_gks($myimgurl.'');
  if ($myimgurl == '') {
    $myimgurl="/my/img/product.png";
  }
  $myout='<a href="/my/admin-product-categories-item.php?id='.$id.'"><img src="'.$myimgurl.'" border="0" style="max-width:'.$wh.'px;max-height:'.$wh.'px;"/></a>';
  
  return $myout;  
}
function getBrandPhoto($id, $myimgurl,$wh=64) {
  $myout='';  
  $myimgurl=trim_gks($myimgurl.'');
  if ($myimgurl == '') {
    $myimgurl="/my/img/product.png";
  }
  $myout='<a href="/my/admin-product-brands-item.php?id='.$id.'"><img src="'.$myimgurl.'" border="0" style="max-width:'.$wh.'px;max-height:'.$wh.'px;"/></a>';
  
  return $myout;  
}









function getUserRoleDescr($id) {
  $myout='';
  $gks_wp_system_roles = gks_wp_system_roles_func();
  $user_object = new WP_User($id);
  $user_roles=(array)$user_object->roles;
  foreach ($gks_wp_system_roles as $role_item) {
    if (in_array($role_item['id'],$user_roles)) {
      $myout.= $role_item['name'].'<br>';
    }
  }
  
  
  
  if (strlen($myout)>0) $myout=substr($myout, 0, strlen($myout)-4);
  return $myout;
}



function getUserRolesArray($table_field) {
  global $my_wp_user_info;
  global $db_link;
  
  $gks_wp_system_roles = gks_wp_system_roles_func();
  $min_hierarchy_login_user=9999;
  foreach ($my_wp_user_info->roles as $value) {
    if (isset($gks_wp_system_roles[$value]) and $gks_wp_system_roles[$value]['hierarchy'] < $min_hierarchy_login_user) 
      $min_hierarchy_login_user = $gks_wp_system_roles[$value]['hierarchy'];
  }
  if ($min_hierarchy_login_user==1) $min_hierarchy_login_user=0; //ean einai admin, na mporei na kanei admin
  
  $myret=array();
  $index=1;
  $myret[] = array('value' => $index,'text' => gks_lang('Μη Συνδρομητής'),       'sql' => $table_field." not like '".$db_link->escape_string('a:1:{s:10:"subscriber";b:1;}')."'");
  foreach ($gks_wp_system_roles as $role_item) {
    $index++;
    if ($role_item['hierarchy'] > $min_hierarchy_login_user) {
      
      $myret[] = array('value' => $index,'text' => $role_item['name'],       'sql' => $table_field." like '%".$db_link->escape_string('"'.$role_item['id'].'"')."%'");
      
    }
  }
  return $myret;
  

  
}

function getUserGroups($id) {
	global $db_link;
	$myout='';
	$sql = "SELECT gks_users_groups_users.*, gks_users_groups.group_title,gks_users_groups.group_disable,
  CONCAT_WS('\\\\',
                  ug10.group_title,
                  ug9.group_title,
                  ug8.group_title,
                  ug7.group_title,
                  ug6.group_title,
                  ug5.group_title,
                  ug4.group_title,
                  ug3.group_title,
                  ug2.group_title,
                  gks_users_groups.group_title) as group_descr
  FROM (((((((((gks_users_groups_users
  LEFT JOIN gks_users_groups ON gks_users_groups_users.group_id = gks_users_groups.id_users_group)
  LEFT JOIN gks_users_groups AS ug2 ON gks_users_groups.group_parent_id = ug2.id_users_group)
  LEFT JOIN gks_users_groups AS ug3 ON ug2.group_parent_id = ug3.id_users_group)
  LEFT JOIN gks_users_groups AS ug4 ON ug3.group_parent_id = ug4.id_users_group)
  LEFT JOIN gks_users_groups AS ug5 ON ug4.group_parent_id = ug5.id_users_group)
  LEFT JOIN gks_users_groups AS ug6 ON ug5.group_parent_id = ug6.id_users_group)
  LEFT JOIN gks_users_groups AS ug7 ON ug6.group_parent_id = ug7.id_users_group)
  LEFT JOIN gks_users_groups AS ug8 ON ug7.group_parent_id = ug8.id_users_group)
  LEFT JOIN gks_users_groups AS ug9 ON ug8.group_parent_id = ug9.id_users_group)
  LEFT JOIN gks_users_groups AS ug10 ON ug9.group_parent_id = ug10.id_users_group
  
  WHERE gks_users_groups_users.user_id=".$id." 
  ORDER BY group_descr;";

  $result_list = $db_link->query($sql); 
  if (!$result_list) {debug_mail(false,'error sql',$sql); return '';}

  while ($row_list = $result_list->fetch_assoc()) {
  	$myout.= $row_list['group_descr'].'<br>';
  }	
  if (strlen($myout)>0) $myout=substr($myout, 0, strlen($myout)-4);
  return $myout;	
}

function ur_ad() {
  global $my_wp_user_info;
  if (isset($my_wp_user_info->roles)) {
    if (in_array('administrator',$my_wp_user_info->roles))  return true;  
    if (in_array('adminmy',$my_wp_user_info->roles))  return true;  
  }
  //print '<pre>';var_dump($my_wp_user_info->roles);die();
  return false;
}


function ur_lo() {
  global $my_wp_user_info;
  if (isset($my_wp_user_info->roles)) {
    if (in_array('logistis',$my_wp_user_info->roles))  return true;  
  }
  return false;
}

function ur_hr() {
  global $my_wp_user_info;
  if (isset($my_wp_user_info->roles)) {
    if (in_array('hrmanager',$my_wp_user_info->roles))  return true;  
  }
  return false;
}

function ur_em() {
  global $my_wp_user_info;
  if (isset($my_wp_user_info->roles)) {
    if (in_array('employee',$my_wp_user_info->roles))  return true;  
  }
  return false;
}






function mynl2br2($a) {
  $a = str_replace("\r\n", " \\ ", $a);
  $a = str_replace("\n\r", " \\ ", $a);
  $a = str_replace("\r", " \\ ", $a);
  $a = str_replace("\n", " \\ ", $a);
  return $a;
}







function cleartonous_php($a) {
  
  $a=mb_strtolower($a);
  $a=str_replace('ά','α', $a);
  $a=str_replace('Ά','Α', $a);
  $a=str_replace('έ','ε', $a);
  $a=str_replace('Έ','Ε', $a);
  $a=str_replace('ή','η', $a);
  $a=str_replace('Ή','Η', $a);
  $a=str_replace('ί','ι', $a);
  $a=str_replace('Ί','Ι', $a);
  $a=str_replace('ώ','ω', $a);
  $a=str_replace('Ώ','Ω', $a);
  $a=str_replace('ύ','υ', $a);
  $a=str_replace('Ύ','Υ', $a);
  $a=str_replace('ό','ο', $a);
  $a=str_replace('Ό','Ο', $a);
  $a=str_replace('ϊ','ι', $a);
  $a=str_replace('Ϊ','Ι', $a);
  $a=str_replace('ΐ','ι', $a);
  $a=str_replace('ϋ','υ', $a);
  $a=str_replace('Ϋ','Υ', $a);
  $a=str_replace('ΰ','υ', $a);
  $a=str_replace('ς','σ', $a);
 
  return $a;
  
}

function secondsago($thistime,$onlytime = false) {
  $SECOND = 1;
  $MINUTE = 60 * $SECOND;
  $HOUR = 60 * $MINUTE;
  $DAY = 24 * $HOUR;
  $MONTH = 30 * $DAY;


  $delta = time() - $thistime;
  //echo $delta;
  if ($delta>0) {
    if ($delta < 1 * $MINUTE) {
        if ($delta<=1) return gks_lang('Ένα δευτ. πριν');
        return str_replace('[1]',$delta,gks_lang('Πριν από [1] δευτ.'));
    }
    if ($delta < 2 * $MINUTE)
        return gks_lang('Πριν ένα λεπτό');
  
    if ($delta < 60 * $MINUTE)
        return str_replace('[1]',intval($delta/60),gks_lang('Πριν από [1] λεπτά'));
  
    if ($delta < 2*60 * $MINUTE)
        return str_replace('[1]',date('H:i', time() - $thistime),gks_lang('Πριν από [1]'));
  
    if ($delta < 24 * $HOUR)
        return str_replace('[1]',showDate($thistime, 'H:i', 1),gks_lang('Στις [1]'));
        
    return showDate($thistime, 'd/m/Y H:i', 1);
  } else {
    $delta=abs($delta);
    if ($delta < 1 * $MINUTE) {
        if ($delta<=1) return gks_lang('Σε ένα δευτ.');
        return str_replace('[1]',$delta,gks_lang('Σε [1] δευτ.'));
    }
    if ($delta < 2 * $MINUTE)
        return gks_lang('Σε ένα λεπτό');
  
    if ($delta < 60 * $MINUTE)
        return str_replace('[1]',intval($delta/60),gks_lang('Σε [1] λεπτά'));
  
    if ($delta < 2*60 * $MINUTE)
        return str_replace('[1]',date('H:i', $thistime-time()),gks_lang('Μετά από [1]'));
  
    if ($delta < 24 * $HOUR)
        return str_replace('[1]',showDate($thistime, 'H:i', 1),gks_lang('Στις [1]'));
        
    return showDate($thistime, 'd/m/Y H:i', 1);
    
    
    
  }
  return '';
}



function gks_seconds2hoursminsecs($myseconds) {
  $days=0;
  $hours=0;
  $minutes=0;
  $seconds=0;
  
  $rest=$myseconds;
  $days=floor($rest/(24*60*60));
  $rest=$rest-$days*(24*60*60);
  $hours=floor($rest/(60*60));
  $rest=$rest-$hours*(60*60);
  $minutes=floor($rest/(60));
  $rest=$rest-$minutes*(60);
  $seconds=$rest;
  
  $ret=[];
  if ($days>0) $ret[]=$days.' '.gks_lang('ημέρες');
  if ($hours>0) $ret[]=$hours.' '.gks_lang('ώρες');
  if ($minutes>0) $ret[]=$minutes.' '.gks_lang('λεπτά');
  if ($seconds>0) $ret[]=$seconds.' '.gks_lang('δευτερόλεπτα');
  
  return implode(', ',$ret); 
}


function greeklish($str) {
  $str = mb_strtolower($str, 'UTF-8');
  $gr = array('α', 'β', 'γ', 'δ', 'ε', 'ζ', 'η', 'θ',  'ι', 'κ', 'λ', 'μ', 'ν', 'ξ',  'ο', 'π', 'ρ', 'σ', 'τ', 'υ', 'φ', 'χ', 'ψ',  'ω', 'ς', 'ά', 'έ', 'ί', 'ό', 'ώ', 'ύ', 'ή', 'ϊ', 'ϋ', 'ΐ', 'ΰ');
  $en = array('a', 'b', 'g', 'd', 'e', 'z', 'i', 'th', 'i', 'k', 'l', 'm', 'n', 'ks', 'o', 'p', 'r', 's', 't', 'y', 'f', 'x', 'ps', 'w', 's', 'a', 'e', 'i', 'o', 'o', 'y', 'i', 'i', 'i', 'i', 'i');
  $str = str_replace($gr, $en, $str);
  return $str;
}
function greekkeybord($str) {
  $str = mb_strtolower($str, 'UTF-8');
  $gr = array('α', 'β', 'γ', 'δ', 'ε', 'ζ', 'η', 'θ',  'ι', 'κ', 'λ', 'μ', 'ν', 'ξ',  'ο', 'π', 'ρ', 'σ', 'τ', 'υ', 'φ', 'χ', 'ψ',  'ω', 'ς', 'ά', 'έ', 'ί', 'ό', 'ώ', 'ύ', 'ή', 'ϊ', 'ϋ', 'ΐ', 'ΰ');
  $en = array('a', 'b', 'g', 'd', 'e', 'z', 'h', 'u',  'i', 'k', 'l', 'm', 'n', 'j',  'o', 'p', 'r', 's', 't', 'y', 'f', 'x', 'c',  'v', 'w', 'a', 'e', 'i', 'o', 'o', 'u', 'h', 'i', 'i', 'i', 'i');
  $str = str_replace($gr, $en, $str);
  return $str;
}




function getProjectStateDescr($mystate) {
  switch ($mystate) {
    case 'draft': return gks_lang('Πρόχειρο','part4','getProjectStateDescr'); break; 
    case 'processing': return gks_lang('Σε εξέλιξη','part4','getProjectStateDescr'); break; 
    case 'complete': return gks_lang('Ολοκληρωμένο','part4','getProjectStateDescr'); break; 
    case 'cancel': return gks_lang('Ακυρωμένο','part4','getProjectStateDescr'); break; 
    default: return $mystate; break; 
  } 
}


function getGenericStateDescr($mystate) {
  switch ($mystate) {
    case 'draft': return gks_lang('Πρόχειρο','part4','getGenericStateDescr'); break; 
    case 'complete': return gks_lang('Ολοκληρωμένο','part4','getGenericStateDescr'); break; 
    case 'cancel': return gks_lang('Ακυρωμένο','part4','getGenericStateDescr'); break; 
    default: return $mystate; break; 
  } 
}


function vardia_name($today_vardia, $daysdiff) {
  $mystotime = _time_user(strtotime($today_vardia) + $daysdiff*24*60*60, 1);
  return getWeekDayName(date('w', $mystotime)) . ' '. date('d', $mystotime) . '/'. date('m', $mystotime);
}
function getTaskStateDescr($mystate) {
  switch ($mystate) {
    case 'draft': return gks_lang('Πρόχειρο','part4','getTaskStateDescr'); break; 
    case 'scanning': return gks_lang('Σάρωση','part4','getTaskStateDescr'); break; 
    case 'check1': return gks_lang('Έλεγχος 1','part4','getTaskStateDescr'); break; 
    case 'edit': return gks_lang('Επεξεργασία','part4','getTaskStateDescr'); break; 
    case 'ocr': return gks_lang('OCR-PDF','part4','getTaskStateDescr'); break; 
    case 'check2': return gks_lang('Έλεγχος 2','part4','getTaskStateDescr'); break; 
    case 'complete': return gks_lang('Ολοκληρωμένο','part4','getTaskStateDescr'); break; 
    case 'public': return gks_lang('Δημόσιο','part4','getTaskStateDescr'); break; 
    case 'cancel': return gks_lang('Ακυρωμένο','part4','getTaskStateDescr'); break; 
    default: return $mystate; break; 
  } 
}



function Index2String($Section, $Index) {
  
  if ($Section == 0)
  {
      return "Cover " . $Index;
  }
  else if ($Section == 1000000)
  {
      return "Back Cover " . (11 - $Index);
  }
  else
  {
      return $Section . '-' . $Index;
  }  
  
}


function calc_profilepososto($id, $is_new_rec) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  
  $myret = array('user' => 0, 'job' =>0);
  $count_user = 0;
  $count_job = 0;

  gks_wp_merge_address_phone_email($id);
  gks_user_update_comm_search($id);
  gks_user_update_dav($id,$is_new_rec);
  gks_voip_remote_localdb_update($id);
  
  $sql = "SELECT ".GKS_WP_TABLE_PREFIX."users.*, gks_eshop_fiscal_position.fiscal_position_descr, gks_eshop_pricelist.pricelist_descr,
  gks_users.eponimia, gks_users.title, gks_users.afm, gks_users.doy, gks_users.epaggelma, 
  gks_users.ma_odos, gks_users.ma_arithmos, gks_users.ma_orofos, gks_users.ma_perioxi,
  gks_users.ma_poli, gks_users.ma_tk, 
  gks_users.ma_country_id, gks_users.ma_nomos_id, 
  gks_users.phone_home, gks_users.genisi_date, gks_users.ethnikotita, 
  gks_users.alli_apasxolisi,gks_users.cv_proipiresia, gks_users.cv_spoydes, gks_users.cv_seminaria, gks_users.cv_mitriki_glossa, gks_users.cv_jenes_glosses,
  gks_users.cv_sxesi_me_photografia, gks_users.cv_metaforiko_meso, gks_users.profilepososto_user, gks_users.profilepososto_job,
  gks_country.country_name, gks_nomoi.nomos_descr, 
  table_last_name.mylast_name, table_first_name.myfirst_name, table_mobile.mymoobile, table_roles.mywp_capabilities,
  gks_users.amka ,gks_users.ama_eam ,gks_users.arithmos_tautoitas ,gks_users.arxi_ekdosis, gks_users.onoma_patera, gks_users.onoma_miteras,
  gks_users_oikogeniaki_katastasti.oikogeniaki_katastasti_descr,gks_users.oikogeniaki_katastasti_id
  FROM (((((((((".GKS_WP_TABLE_PREFIX."users 
  LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
  LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country) 
  LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos)
  LEFT JOIN gks_eshop_fiscal_position ON ".GKS_WP_TABLE_PREFIX."users.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
  LEFT JOIN gks_eshop_pricelist ON ".GKS_WP_TABLE_PREFIX."users.pricelist_id = gks_eshop_pricelist.id_pricelist) 
  LEFT JOIN gks_users_oikogeniaki_katastasti ON gks_users_oikogeniaki_katastasti.id_oikogeniaki_katastasti = gks_users.oikogeniaki_katastasti_id) 
  LEFT JOIN (
    SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS myfirst_name
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='first_name'))
  )  AS table_first_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_first_name.user_id) 
  LEFT JOIN (
    SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mylast_name
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='last_name'))
  )  AS table_last_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_last_name.user_id) 
  LEFT JOIN (
    SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mymoobile
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='mobile'))
  )  AS table_mobile ON ".GKS_WP_TABLE_PREFIX."users.ID = table_mobile.user_id) 
  LEFT JOIN (
    SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mywp_capabilities
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='".GKS_WP_TABLE_PREFIX."capabilities'))
  )  AS table_roles ON ".GKS_WP_TABLE_PREFIX."users.ID = table_roles.user_id
  where ".GKS_WP_TABLE_PREFIX."users.id = ".$id;
  	
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'calc_profilepososto error sql',$sql);
    return $myret;
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'calc_profilepososto record not found sql',$sql); 
    return $myret;
  }
  $row = $result->fetch_assoc();  
  
  // exei user +1                           
  {$count_user++; $count_job++;}   //1
  // exei password + 1
  {$count_user++; $count_job++;}   //2
  
  $feu=array();
  $fej=array();
  
  
  if ($row['myfirst_name'] !='')            {$count_user++; $count_job++; } else { $feu[]=gks_lang('Το Όνομά μου');$fej[]=gks_lang('Το Όνομά μου');} //3
  if ($row['mylast_name'] !='')             {$count_user++; $count_job++; } else { $feu[]=gks_lang('Το Επώνυμό μου');$fej[]=gks_lang('Το Επώνυμό μου');} 
  //if ($row['user_nicename'] !='')           {$count_user++; $count_job++; } else { $feu[]=gks_lang('Υποκοριστικό');$fej[]=gks_lang('Υποκοριστικό');} 
  if ($row['display_name'] !='')            {$count_user++; $count_job++; } else { $feu[]=gks_lang('Προβολή δημοσίως ως');$fej[]=gks_lang('Προβολή δημοσίως ως');} 
  if ($row['onoma_patera'] !='')            {$count_job++; } else { $fej[]=gks_lang('Πατρώνυμο');} 
  if ($row['onoma_miteras'] !='')           {$count_job++; } else { $fej[]=gks_lang('Μητρώνυμο');} 
  if ($row['gks_sex'] !=0)                  {$count_user++; $count_job++; } else { $feu[]=gks_lang('Φύλο');$fej[]=gks_lang('Φύλο');} 
  if ($row['gks_lang'].'' !='')             {$count_user++; $count_job++; } else { $feu[]=gks_lang('Γλώσσα');$fej[]=gks_lang('Γλώσσα');} 
  if ($row['oikogeniaki_katastasti_id'] !=0){$count_job++; } else { $fej[]=gks_lang('Οικογενειακή Κατάσταση');} 
  if (get_user_meta($id, 'wsl_current_user_image', true) !='') {$count_user++; $count_job++; } else { $feu[]=gks_lang('H φωτογραφία του προφίλ μου');$fej[]=gks_lang('H φωτογραφία του προφίλ μου');} //7
  if ($row['user_email'] !='')              {$count_user++; $count_job++; } else { $feu[]=gks_lang('Email');$fej[]=gks_lang('Email');}
  if ($row['mymoobile'] !='')               {$count_user++; $count_job++; } else { $feu[]=gks_lang('Κινητό Τηλέφωνο');$fej[]=gks_lang('Κινητό Τηλέφωνο');}
  //if ($row['phone_home'] !='')              {$count_user++; $count_job++; } else { $feu[]=gks_lang('Σταθερό Τηλέφωνο');$fej[]=gks_lang('Σταθερό Τηλέφωνο');}
  if ($row['ma_odos'] !='')                 {$count_user++; $count_job++; } else { $feu[]=gks_lang('Οδός');$fej[]=gks_lang('Οδός');}
  if ($row['ma_poli'] !='')                 {$count_user++; $count_job++; } else { $feu[]=gks_lang('Πόλη');$fej[]=gks_lang('Πόλη');}
  if ($row['ma_tk'] !='')                   {$count_user++; $count_job++; } else { $feu[]=gks_lang('Ταχυδρομικός Κώδικας');$fej[]=gks_lang('Ταχυδρομικός Κώδικας');}
  if ($row['ma_country_id'] >0)             {$count_user++; $count_job++; } else { $feu[]=gks_lang('Χώρα');$fej[]=gks_lang('Χώρα');}
  if ($row['ma_nomos_id'] >0)               {$count_user++; $count_job++; } else { $feu[]=gks_lang('Νομός');$fej[]=gks_lang('Νομός');}  //15
  
  
  
  if ($row['arithmos_tautoitas']!= '')      {$count_job++; } else { $fej[]=gks_lang('Αριθμός Ταυτότητας');} //16
  if ($row['arxi_ekdosis']!= '')            {$count_job++; } else { $fej[]=gks_lang('Αρχή Έκδοσης');} //17
  if ($row['amka']!= '')                    {$count_job++; } else { $fej[]=gks_lang('ΑΜΚΑ');} //18
  if ($row['ama_eam']!= '')                 {$count_job++; } else { $fej[]=gks_lang('ΑΜΑ - ΕΑΜ');} //19
  
  
  if ($row['afm']!= '')                     {$count_job++; } else { $fej[]=gks_lang('ΑΦΜ');} //20
  if ($row['doy']!= '')                     {$count_job++; } else { $fej[]=gks_lang('ΔΟΥ');} //21
  if ($row['genisi_date']!= '')             {$count_job++; } else { $fej[]=gks_lang('Ημερομηνία Γέννησης');} //22
  if (get_user_meta($id, 'description', true) !='') { $count_job++; } else { $fej[]=gks_lang('Σύντομο βιογραφικό');} //23
  if ($row['ethnikotita']!= '')             {$count_job++; } else { $fej[]=gks_lang('Εθνικότητα');} //24
  if ($row['alli_apasxolisi']!= '')         {$count_job++; } else { $fej[]=gks_lang('Άλλη Απασχόληση');} //25
  if ($row['cv_proipiresia']!= '')          {$count_job++; } else { $fej[]=gks_lang('Προϋπηρεσία');} //26
  if ($row['cv_spoydes']!= '')              {$count_job++; } else { $fej[]=gks_lang('Σπουδές');} //27
  if ($row['cv_seminaria']!= '')            {$count_job++; } else { $fej[]=gks_lang('Σεμινάρια');} //28
  if ($row['cv_mitriki_glossa']!= '')       {$count_job++; } else { $fej[]=gks_lang('Μητρική Γλώσσα');} //29
  if ($row['cv_jenes_glosses']!= '')        {$count_job++; } else { $fej[]=gks_lang('Ξένες Γλώσσες');} //30
  //if ($row['cv_sxesi_me_photografia']!= '') {$count_job++; } else { $fej[]=gks_lang('Σχέση με την Φωτογραφία');} //31
  if ($row['cv_metaforiko_meso']!= '')      {$count_job++; } else { $fej[]=gks_lang('Μεταφορικό Μέσο');}  //32

//  $sql="select id_user_cv from gks_users_cv where user_id=".$id;
//  $result = $db_link->query($sql);        
//  if (!$result) {
//    debug_mail(false,'error sql',$sql);
//    return $myret;}
//  if ($result->num_rows > 0) {  
//    $count_job++;  //33
//  } else { 
//    $fej[]=gks_lang('Πλήρες βιογραφικό');
//  }  
  
  $sql="select id_bank_account from gks_bank_accounts where deleted_from_user=0 and user_id=".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    return $myret;}
  if ($result->num_rows > 0) {  
    $count_job++;  //34
  } else { 
    $fej[]=gks_lang('Τραπεζικός Λογαριασμός');
  }
  

  
  
  
  
  //echo '-'.$row['user_nicename'].'-'.$count_user;
  //if ($row['user_nicename'] !='') echo 'fffffffffffff';
  //die();
  
  foreach ($feu as &$feu_value) {
     $feu_value = base64_encode($feu_value);
  } 
  foreach ($fej as &$fej_value) {
     $fej_value = base64_encode($fej_value);
  } 
  
  
  $myret['user_rf'] = $feu;
  $myret['job_rf'] = $fej;
  
  
  $myret['user'] = intval(100 * $count_user/15);
  $myret['job'] = intval(100 * $count_job/35); //35 -1 (cv_sxesi_me_photografia)  =34
  
  if ($myret['user'] >100) $myret['user'] = 100;
  if ($myret['job'] >100) $myret['job'] = 100;
  
  
//  $sql="select user_id from gks_users where user_id=".$id;
//  $result = $db_link->query($sql);        
//  if (!$result) {
//    debug_mail(false,'error sql',$sql);
//    return $myret;}
//  if ($result->num_rows==0) {
//    $sql="insert into gks_users (user_id,mydate_add,user_id_add,myip) values (".$id.",now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."')";
//    $result_gks_users = $db_link->query($sql);    
//  }
  //echo '-'.$row['user_nicename'].'-'.$count_user;
  
  $sql="update gks_users set 
  profilepososto_user =".$myret['user'].",
  gks_users.profilepososto_job=".$myret['job']."  
  where user_id=".$id." limit 1";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'calc_profilepososto error sql',$sql);
    return $myret;
  }
  //echo $sql;
  //die();
  
  return $myret;
}
 









function getIdiotitaTypeDescr($mystate) {
  switch ($mystate) {
    case '10button': return gks_lang('Κουμπί'); break; 
    case '20color': return gks_lang('Χρώμα'); break; 
    case '30image': return gks_lang('Φωτογραφία'); break; 

    default: return $mystate; break; 
  } 
}
function getProductClassDescr($mystate) {
  switch ($mystate) {
    case 'simple': return gks_lang('Απλό'); break; 
    case 'variable': return gks_lang('Μεταβλητό'); break; 
    default: return $mystate; break; 
  } 
}


if (!function_exists("mb_basename"))
{
  function mb_basename($path)
  {
    $separator = " qq ";
    $path = preg_replace("/[^ ]/u", $separator."\$0".$separator, $path);
    $base = basename($path);
    $base = str_replace($separator, "", $base);
    return $base;
  }
}

if (!function_exists("mb_pathinfo"))
{
  function mb_pathinfo($path, $opt = "")
  {
    $separator = " qq ";
    $path = preg_replace("/[^ ]/u", $separator."\$0".$separator, $path);
    if ($opt == "") $pathinfo = pathinfo($path);
    else $pathinfo = pathinfo($path, $opt);

    if (is_array($pathinfo))
    {
      $pathinfo2 = $pathinfo;
      foreach($pathinfo2 as $key => $val)
      {
        $pathinfo[$key] = str_replace($separator, "", $val);
      }
    }
    else if (is_string($pathinfo)) $pathinfo = str_replace($separator, "", $pathinfo);
    return $pathinfo;
  }
}

function gks_wp_system_roles_sort($a, $b) {
  if ($b['hierarchy'] > $a['hierarchy']) return -1;
  if ($b['hierarchy'] < $a['hierarchy']) return 1;
  
  if ($b['cal_count'] > $a['cal_count']) return 1;
  if ($b['cal_count'] < $a['cal_count']) return -1;
  
  $collator = new Collator('el_GR');
  return $collator -> compare ($a['name'],$b['name']);
}
function getRoleDescrConv($a) {
  if ($a=='Subscriber') return gks_lang('Επισκέπτης/Επαφή','part4','userroles');
  if ($a=='Administrator') return gks_lang('Διαχειριστής','part4','userroles');
  if ($a=='Editor') return gks_lang('Συντάκτης','part4','userroles');
  if ($a=='Author') return gks_lang('Συγγραφέας','part4','userroles');
  if ($a=='Contributor') return gks_lang('Συνεισφέρων','part4','userroles');

  return $a;
}
function gks_wp_system_roles_func() {
  $gks_wp_system_roles=array();
  foreach (wp_roles()->roles as $key => $value) {
    $hierarchy=999;
    if (isset(GKS_ROLES_HIERARCHY[$key])) $hierarchy=GKS_ROLES_HIERARCHY[$key];
    
    $value['name']=getRoleDescrConv($value['name']);
    $value['name']=gks_lang($value['name'].'','part4','userroles');
    $gks_wp_system_roles[$key]=array('id'=> $key, 'hierarchy'=>$hierarchy, 'name'=>$value['name'],'cal_count'=>count($value['capabilities']));
  } 
  
  //print '<pre>';print_r($gks_wp_system_roles);die();
  
  usort($gks_wp_system_roles, "gks_wp_system_roles_sort");  
  
  $gks_wp_system_roles_new=array();
  foreach($gks_wp_system_roles as $value) {
    $gks_wp_system_roles_new[$value['id']] = $value;
    
  }
  
  return $gks_wp_system_roles_new;
}
function encodeURI($uri)
{
    return preg_replace_callback("{[^0-9a-z_.!~*'();,/?:@&=+$#-]}i", function ($m) {
        return sprintf('%%%02X', ord($m[0]));
    }, $uri);
}



function gks_warehouse_address_update($inputdata = array()) {
  global $db_link;
  global $GKS_LANG_DATA_ARRAY;
  gks_build_GKS_LANG_DATA_ARRAY();
  
  
  if (isset($inputdata['id_company_sub'])) {
    $sql="UPDATE gks_warehouses 
    LEFT JOIN gks_company_subs ON gks_warehouses.company_sub_id = gks_company_subs.id_company_sub 
    SET 
    gks_warehouses.warehouse_phone = gks_company_subs.company_sub_phone,
    gks_warehouses.warehouse_email = gks_company_subs.company_sub_email,
    gks_warehouses.warehouse_website = gks_company_subs.company_sub_url,
    gks_warehouses.warehouse_branch = gks_company_subs.aade_branch_sub,
    gks_warehouses.warehouse_odos = gks_company_subs.company_sub_odos,
    gks_warehouses.warehouse_arithmos = gks_company_subs.company_sub_arithmos,
    gks_warehouses.warehouse_orofos = gks_company_subs.company_sub_orofos,
    gks_warehouses.warehouse_perioxi = gks_company_subs.company_sub_perioxi,
    gks_warehouses.warehouse_poli = gks_company_subs.company_sub_poli,
    gks_warehouses.warehouse_tk = gks_company_subs.company_sub_tk,
    gks_warehouses.warehouse_nomos_id = gks_company_subs.company_sub_nomos_id,
    gks_warehouses.warehouse_country_id = gks_company_subs.company_sub_country_id,
    gks_warehouses.warehouse_map_latitude = gks_company_subs.company_sub_map_latitude,
    gks_warehouses.warehouse_map_longitude = gks_company_subs.company_sub_map_longitude
    
    WHERE gks_warehouses.company_sub_id=".$inputdata['id_company_sub']."
    AND gks_warehouses.warehouse_is_company_place=1";
    //echo 'fffffffff '.$sql;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    
    
    foreach ($GKS_LANG_DATA_ARRAY as $lang_item) {
      $sql="UPDATE (gks_warehouses_lang 
      LEFT JOIN gks_warehouses ON gks_warehouses_lang.warehouse_id = gks_warehouses.id_warehouse) 
      LEFT JOIN (
        SELECT company_sub_id, company_sub_phone, 
        company_sub_odos, company_sub_arithmos, 
        company_sub_orofos, company_sub_perioxi, company_sub_poli
        FROM gks_company_subs_lang
        WHERE lang_code='".$lang_item['id_lang']."'
        and company_sub_id=".$inputdata['id_company_sub']."
      ) AS langtable ON gks_warehouses.company_sub_id = langtable.company_sub_id 
      SET 
      gks_warehouses_lang.warehouse_phone = company_sub_phone, 
      gks_warehouses_lang.warehouse_odos = company_sub_odos, 
      gks_warehouses_lang.warehouse_arithmos = company_sub_arithmos, 
      gks_warehouses_lang.warehouse_orofos = company_sub_orofos, 
      gks_warehouses_lang.warehouse_perioxi = company_sub_perioxi, 
      gks_warehouses_lang.warehouse_poli = company_sub_poli
      WHERE gks_warehouses.company_sub_id=".$inputdata['id_company_sub']."
      AND gks_warehouses.warehouse_is_company_place=1
      and gks_warehouses_lang.lang_code='".$lang_item['id_lang']."'";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}
    }
        
  } else if (isset($inputdata['id_company'])) {
    $sql="UPDATE gks_warehouses 
    LEFT JOIN gks_company ON gks_warehouses.company_id = gks_company.id_company 
    SET 
    gks_warehouses.warehouse_phone = gks_company.company_phone,
    gks_warehouses.warehouse_email = gks_company.company_email,
    gks_warehouses.warehouse_website = gks_company.company_url,
    gks_warehouses.warehouse_branch = gks_company.aade_branch,
    gks_warehouses.warehouse_odos = gks_company.company_odos,
    gks_warehouses.warehouse_arithmos = gks_company.company_arithmos,
    gks_warehouses.warehouse_orofos = gks_company.company_orofos,
    gks_warehouses.warehouse_perioxi = gks_company.company_perioxi,
    gks_warehouses.warehouse_poli = gks_company.company_poli,
    gks_warehouses.warehouse_tk = gks_company.company_tk,
    gks_warehouses.warehouse_nomos_id = gks_company.company_nomos_id,
    gks_warehouses.warehouse_country_id = gks_company.company_country_id,
    gks_warehouses.warehouse_map_latitude = gks_company.company_map_latitude,
    gks_warehouses.warehouse_map_longitude = gks_company.company_map_longitude
    
    WHERE gks_warehouses.company_id=".$inputdata['id_company']." and company_sub_id=0
    AND gks_warehouses.warehouse_is_company_place=1";
    //echo 'fffffffff '.$sql;
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    
    foreach ($GKS_LANG_DATA_ARRAY as $lang_item) {
      $sql="UPDATE (gks_warehouses_lang 
      LEFT JOIN gks_warehouses ON gks_warehouses_lang.warehouse_id = gks_warehouses.id_warehouse) 
      LEFT JOIN (
        SELECT company_id, company_phone, 
        company_odos, company_arithmos,
        company_orofos, company_perioxi, company_poli
        FROM gks_company_lang
        WHERE lang_code='".$lang_item['id_lang']."'
        and company_id=".$inputdata['id_company']."
      ) AS langtable ON gks_warehouses.company_id = langtable.company_id 
      SET 
      gks_warehouses_lang.warehouse_phone = company_phone, 
      gks_warehouses_lang.warehouse_odos = company_odos, 
      gks_warehouses_lang.warehouse_arithmos = company_arithmos, 
      gks_warehouses_lang.warehouse_orofos = company_orofos, 
      gks_warehouses_lang.warehouse_perioxi = company_perioxi, 
      gks_warehouses_lang.warehouse_poli = company_poli
      WHERE gks_warehouses.company_id=".$inputdata['id_company']." and company_sub_id=0
      AND gks_warehouses.warehouse_is_company_place=1
      and gks_warehouses_lang.lang_code='".$lang_item['id_lang']."'";
      $result = $db_link->query($sql);  
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}
    }

    
  }
  
}
function gks_get_companys_list($user_id=0) {
  global $my_wp_user_id;
  global $db_link;
  
  if ($user_id==0) $user_id=$my_wp_user_id;

  $perm_id_company_ids=gks_permission_user_condition($user_id,'gks_company','01');
  $perm_id_company_sub_ids=gks_permission_user_condition($user_id,'gks_company_subs','01');

  
  $user_company=array();
  
  if (1==2) {
    //$user_company[1]=array(0);
    $user_company[1]=array(10007);
    //$user_company[10002]=array(0,10003);
    //$user_company[10005]=array(10006);
  }
  
  $in1=array();
  foreach ($user_company as $id_company=>$subs) {
    $in1[]=$id_company;
  } 
  
  $sql="SELECT gks_company.id_company, gks_company.company_afm, gks_company.company_title, csubs.id_company_sub, csubs.company_sub_title
  FROM gks_company 
  LEFT JOIN (
    SELECT id_company_sub, company_id, company_sub_title, company_sub_sortorder
    FROM gks_company_subs
    WHERE company_sub_disable=0 ";
    if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_company_subs.id_company_sub in (".implode(',',$perm_id_company_sub_ids).")";
  $sql.=") as csubs ON gks_company.id_company = csubs.company_id
  where company_disable=0";
  if (count($perm_id_company_ids)>0) $sql.=" and gks_company.id_company in (".implode(',',$perm_id_company_ids).")";
  if (count($in1)>0) $sql.=" and id_company in (".implode(',',$in1).")";
  
  $sql.=" ORDER BY gks_company.company_sortorder, gks_company.company_title, csubs.company_sub_sortorder, csubs.company_sub_title";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  
  $ret=array();
  while ($row = $result->fetch_assoc()) {
    if (isset($row['id_company_sub']) == false) {
      if (count($perm_id_company_sub_ids)==0 or (in_array(0,$perm_id_company_sub_ids))) {
        if (count($in1)==0 or (isset($user_company[$row['id_company']]) and in_array(0,$user_company[$row['id_company']]))) {
          $id=$row['id_company'].'|0';
          $row['company_afm']=trim_gks($row['company_afm']);
          $row['company_title']=trim_gks($row['company_title']);
          
          if (isset($ret[$id])==false) {
              $ret[$id]=array(
              'id' => $id,
              'id_company' => $row['id_company'],
              'id_company_sub' => 0,
              'company_afm' => $row['company_afm'],
              'company_title' => $row['company_title'],
              'company_sub_title' => gks_lang('Κεντρικό'),
              'descr' => $row['company_title'].' \\ '.gks_lang('Κεντρικό'),
            ); 
          }
        }
      }
    } else {
      if (count($perm_id_company_sub_ids)==0 or (in_array(0,$perm_id_company_sub_ids))) {
        if (count($in1)==0 or (isset($user_company[$row['id_company']]) and in_array(0,$user_company[$row['id_company']]))) {
          $id=$row['id_company'].'|0';
          if (isset($ret[$id])==false) {
            $ret[$id]=array(
              'id' => $id,
              'id_company' => $row['id_company'],
              'id_company_sub' => 0,
              'company_afm' => $row['company_afm'],
              'company_title' => $row['company_title'],
              'company_sub_title' => gks_lang('Κεντρικό'),
              'descr' => $row['company_title'].' \\ '.gks_lang('Κεντρικό'),
            );      
          }
        }
      }
      
      if (count($in1)==0 or (isset($user_company[$row['id_company']]) and in_array($row['id_company_sub'],$user_company[$row['id_company']]))) {
        $id=$row['id_company'].'|'.$row['id_company_sub'];
        $ret[$id]=array(
          'id' => $id,
          'id_company' => $row['id_company'],
          'id_company_sub' => $row['id_company_sub'],
          'company_afm' => $row['company_afm'],
          'company_title' => $row['company_title'],
          'company_sub_title' => $row['company_sub_title'],
          'descr' => $row['company_title'].' \\ '.$row['company_sub_title'],
        );     
      }       
      
    }
  }
  return $ret;
}






function gks_get_user_settings($user_id, $myobject='', $mysubobject='') {
  global $db_link;
  global $GKS_ORDER_DEFAULT_DELIVERY;
  global $GKS_ORDER_DEFAULT_PAYMENT;
  global $GKS_GOOGLE_MAPS_API_KEY;
  
  if ($user_id<=0) return array();
   
  $sql="select * from gks_settings_users where user_id=".$user_id." and myobject<>'css'";
  if ($myobject!='') $sql.=" and myobject like '".$db_link->escape_string($myobject)."'";
  if ($mysubobject!='') $sql.=" and mysubobject like '".$db_link->escape_string($mysubobject)."'";
  
  if ($myobject=='' and $mysubobject=='') {
    //na min fortonei tzampa ta gks_customtableview 
    //tha ta fortono otan prepei 
    $sql.=" and myobject <> 'gks_customtableview'";
  }
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
  
  $ret=array();
  while ($row = $result->fetch_assoc()) {
    $temp1=trim_gks($row['myobject']);
    $temp2=trim_gks($row['mysubobject']);
    $temp3=trim_gks($row['myvalue']);
    
    $ret[$temp1][$temp2]=$temp3;
  }

  $ret['print']['forms_products']=array();
  if (isset($ret['print']['form_id_products']) and intval($ret['print']['form_id_products'])>0) {
    $sql="select localization_set_id from gks_print_forms where localization_set_id>0 and id_print_form=".intval($ret['print']['form_id_products']);
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $localization_set_id=$row['localization_set_id'];
       
      $sql="select id_print_form,gks_lang from gks_print_forms where is_disable=0 and localization_set_id=".$localization_set_id;
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
      while ($row = $result->fetch_assoc()) {
        $ret['print']['forms_products'][$row['gks_lang']]=intval($row['id_print_form']);
      }
    }
  }
  
  $ret['print']['forms_order']=array();
  if (isset($ret['print']['form_id_order']) and intval($ret['print']['form_id_order'])>0) {
    $sql="select localization_set_id from gks_print_forms where localization_set_id>0 and id_print_form=".intval($ret['print']['form_id_order']);
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $localization_set_id=$row['localization_set_id'];
       
      $sql="select id_print_form,gks_lang from gks_print_forms where is_disable=0 and localization_set_id=".$localization_set_id;
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
      while ($row = $result->fetch_assoc()) {
        $ret['print']['forms_order'][$row['gks_lang']]=intval($row['id_print_form']);
      }
    }
  }

  $ret['print']['forms_inv']=array();
  if (isset($ret['print']['form_id_inv']) and intval($ret['print']['form_id_inv'])>0) {
    $sql="select localization_set_id from gks_print_forms where localization_set_id>0 and id_print_form=".intval($ret['print']['form_id_inv']);
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $localization_set_id=$row['localization_set_id'];
       
      $sql="select id_print_form,gks_lang from gks_print_forms where is_disable=0 and localization_set_id=".$localization_set_id;
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
      while ($row = $result->fetch_assoc()) {
        $ret['print']['forms_inv'][$row['gks_lang']]=intval($row['id_print_form']);
      }
    }
  }
  $ret['print']['forms_pay']=array();
  if (isset($ret['print']['form_id_pay']) and intval($ret['print']['form_id_pay'])>0) {
    $sql="select localization_set_id from gks_print_forms where localization_set_id>0 and id_print_form=".intval($ret['print']['form_id_pay']);
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $localization_set_id=$row['localization_set_id'];
       
      $sql="select id_print_form,gks_lang from gks_print_forms where is_disable=0 and localization_set_id=".$localization_set_id;
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
      while ($row = $result->fetch_assoc()) {
        $ret['print']['forms_pay'][$row['gks_lang']]=intval($row['id_print_form']);
      }
    }
  }
  $ret['print']['forms_whi']=array();
  if (isset($ret['print']['form_id_whi']) and intval($ret['print']['form_id_whi'])>0) {
    $sql="select localization_set_id from gks_print_forms where localization_set_id>0 and id_print_form=".intval($ret['print']['form_id_whi']);
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $localization_set_id=$row['localization_set_id'];
       
      $sql="select id_print_form,gks_lang from gks_print_forms where is_disable=0 and localization_set_id=".$localization_set_id;
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
      while ($row = $result->fetch_assoc()) {
        $ret['print']['forms_whi'][$row['gks_lang']]=intval($row['id_print_form']);
      }
    }
  }

  
  $ret['print']['forms_reservation']=array();
  if (isset($ret['print']['form_id_reservation']) and intval($ret['print']['form_id_reservation'])>0) {
    $sql="select localization_set_id from gks_print_forms where localization_set_id>0 and id_print_form=".intval($ret['print']['form_id_reservation']);
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $localization_set_id=$row['localization_set_id'];
       
      $sql="select id_print_form,gks_lang from gks_print_forms where is_disable=0 and localization_set_id=".$localization_set_id;
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
      while ($row = $result->fetch_assoc()) {
        $ret['print']['forms_reservation'][$row['gks_lang']]=intval($row['id_print_form']);
      }
    }
  }

  $ret['print']['forms_transfer_reservation']=array();
  if (isset($ret['print']['form_id_transfer_reservation']) and intval($ret['print']['form_id_transfer_reservation'])>0) {
    $sql="select localization_set_id from gks_print_forms where localization_set_id>0 and id_print_form=".intval($ret['print']['form_id_transfer_reservation']);
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $localization_set_id=$row['localization_set_id'];
       
      $sql="select id_print_form,gks_lang from gks_print_forms where is_disable=0 and localization_set_id=".$localization_set_id;
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
      while ($row = $result->fetch_assoc()) {
        $ret['print']['forms_transfer_reservation'][$row['gks_lang']]=intval($row['id_print_form']);
      }
    }
  }  
  
  $ret['print']['forms_crm_task']=array();
  if (isset($ret['print']['form_id_crm_task']) and intval($ret['print']['form_id_crm_task'])>0) {
    $sql="select localization_set_id from gks_print_forms where localization_set_id>0 and id_print_form=".intval($ret['print']['form_id_crm_task']);
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $localization_set_id=$row['localization_set_id'];
       
      $sql="select id_print_form,gks_lang from gks_print_forms where is_disable=0 and localization_set_id=".$localization_set_id;
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
      while ($row = $result->fetch_assoc()) {
        $ret['print']['forms_crm_task'][$row['gks_lang']]=intval($row['id_print_form']);
      }
    }
  } 

  $ret['print']['forms_customt']=array();
  if (isset($ret['print']['form_id_customt']) and intval($ret['print']['form_id_customt'])>0) {
    $sql="select localization_set_id from gks_print_forms where localization_set_id>0 and id_print_form=".intval($ret['print']['form_id_customt']);
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $localization_set_id=$row['localization_set_id'];
       
      $sql="select id_print_form,gks_lang from gks_print_forms where is_disable=0 and localization_set_id=".$localization_set_id;
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
      while ($row = $result->fetch_assoc()) {
        $ret['print']['forms_customt'][$row['gks_lang']]=intval($row['id_print_form']);
      }
    }
  }
      
  if (isset($ret['gks_orders']['enter_order'])==false) {
    $ret['gks_orders']['enter_order']=array();
  } else {
    if ($ret['gks_orders']['enter_order']!='') {
      $ret['gks_orders']['enter_order']=unserialize($ret['gks_orders']['enter_order']);
    } else {
      $ret['gks_orders']['enter_order']=array();
    }
  }
  if (isset($ret['gks_acc_inv']['enter_order'])==false) {
    $ret['gks_acc_inv']['enter_order']=array();
  } else {
    if ($ret['gks_acc_inv']['enter_order']!='') {
      $ret['gks_acc_inv']['enter_order']=unserialize($ret['gks_acc_inv']['enter_order']);
    } else {
      $ret['gks_acc_inv']['enter_order']=array();
    }
  }
  if (isset($ret['gks_whi_mov']['enter_order'])==false) {
    $ret['gks_whi_mov']['enter_order']=array();
  } else {
    if ($ret['gks_whi_mov']['enter_order']!='') {
      $ret['gks_whi_mov']['enter_order']=unserialize($ret['gks_whi_mov']['enter_order']);
    } else {
      $ret['gks_whi_mov']['enter_order']=array();
    }
  }
  if (isset($ret['gks_hotel_reservation']['enter_order'])==false) {
    $ret['gks_hotel_reservation']['enter_order']=array();
  } else {
    if ($ret['gks_hotel_reservation']['enter_order']!='') {
      $ret['gks_hotel_reservation']['enter_order']=unserialize($ret['gks_hotel_reservation']['enter_order']);
    } else {
      $ret['gks_hotel_reservation']['enter_order']=array();
    }
  }
  //gks_hotel_reservation den exei enter_order, diladi poy na paei otan patiete to enter
  
  
  if (isset($ret['print'])==false)              $ret['print']=array();
  if (isset($ret['print']['file_type'])==false) $ret['print']['file_type']='pdf';
  if (isset($ret['print']['grayscale'])==false) $ret['print']['grayscale']='false';
  if (isset($ret['print']['landscape'])==false) $ret['print']['landscape']='false';
  if (isset($ret['print']['zoom'])==false)      $ret['print']['zoom']='100';
  if (isset($ret['print']['form_id'])==false)   $ret['print']['form_id']='0';
  
  if (isset($ret['gks_orders'])==false)                       $ret['gks_orders']=array();
  if (isset($ret['gks_orders']['message_type'])==false) $ret['gks_orders']['message_type']=1;
  if (isset($ret['gks_orders']['email_template'])==false) $ret['gks_orders']['email_template']='default';
  if (isset($ret['gks_orders']['tropos_apostolis'])==false or $ret['gks_orders']['tropos_apostolis']==0) $ret['gks_orders']['tropos_apostolis']=$GKS_ORDER_DEFAULT_DELIVERY;
  if (isset($ret['gks_orders']['tropos_pliromis'])==false  or $ret['gks_orders']['tropos_pliromis']==0)  $ret['gks_orders']['tropos_pliromis']=$GKS_ORDER_DEFAULT_PAYMENT;
  
  if (isset($ret['gks_acc_inv'])==false)                       $ret['gks_acc_inv']=array();
  if (isset($ret['gks_acc_inv']['message_type'])==false) $ret['gks_acc_inv']['message_type']=1;
  if (isset($ret['gks_acc_inv']['email_template'])==false) $ret['gks_acc_inv']['email_template']='default';
  if (isset($ret['gks_acc_inv']['tropos_apostolis'])==false or $ret['gks_acc_inv']['tropos_apostolis']==0) $ret['gks_acc_inv']['tropos_apostolis']=$GKS_ORDER_DEFAULT_DELIVERY;
  if (isset($ret['gks_acc_inv']['tropos_pliromis'])==false or  $ret['gks_acc_inv']['tropos_pliromis']==0)  $ret['gks_acc_inv']['tropos_pliromis']=$GKS_ORDER_DEFAULT_PAYMENT;

  if (isset($ret['gks_acc_inv']['def_company_qrcode'])==false or  $ret['gks_acc_inv']['def_company_qrcode']=='')  $ret['gks_acc_inv']['def_company_qrcode']='0|0';
  
  
  if (isset($ret['gks_acc_pay'])==false)                       $ret['gks_acc_pay']=array();
  if (isset($ret['gks_acc_pay']['message_type'])==false) $ret['gks_acc_pay']['message_type']=1;
  if (isset($ret['gks_acc_pay']['email_template'])==false) $ret['gks_acc_pay']['email_template']='default';
  if (isset($ret['gks_acc_pay']['tropos_pliromis'])==false or  $ret['gks_acc_pay']['tropos_pliromis']==0)  $ret['gks_acc_pay']['tropos_pliromis']=$GKS_ORDER_DEFAULT_PAYMENT;
  
  if (isset($ret['gks_whi_mov'])==false)                       $ret['gks_whi_mov']=array();
  if (isset($ret['gks_whi_mov']['message_type'])==false) $ret['gks_whi_mov']['message_type']=1;
  if (isset($ret['gks_whi_mov']['email_template'])==false) $ret['gks_whi_mov']['email_template']='default';
  if (isset($ret['gks_whi_mov']['tropos_apostolis'])==false or $ret['gks_whi_mov']['tropos_apostolis']==0) $ret['gks_whi_mov']['tropos_apostolis']=$GKS_ORDER_DEFAULT_DELIVERY;
  if (isset($ret['gks_whi_mov']['tropos_pliromis'])==false or  $ret['gks_whi_mov']['tropos_pliromis']==0)  $ret['gks_whi_mov']['tropos_pliromis']=$GKS_ORDER_DEFAULT_PAYMENT;
  
  if (isset($ret['gks_hotel_reservation'])==false)                       $ret['gks_hotel_reservation']=array();
  if (isset($ret['gks_hotel_reservation']['message_type'])==false) $ret['gks_hotel_reservation']['message_type']=1;
  if (isset($ret['gks_hotel_reservation']['email_template'])==false) $ret['gks_hotel_reservation']['email_template']='default';
  if (isset($ret['gks_hotel_reservation']['tropos_apostolis'])==false or $ret['gks_hotel_reservation']['tropos_apostolis']==0) $ret['gks_hotel_reservation']['tropos_apostolis']=$GKS_ORDER_DEFAULT_DELIVERY;
  if (isset($ret['gks_hotel_reservation']['tropos_pliromis'])==false or  $ret['gks_hotel_reservation']['tropos_pliromis']==0)  $ret['gks_hotel_reservation']['tropos_pliromis']=$GKS_ORDER_DEFAULT_PAYMENT;
  
  if (isset($ret['gks_transfer_reservation'])==false)                       $ret['gks_transfer_reservation']=array();
  if (isset($ret['gks_transfer_reservation']['message_type'])==false) $ret['gks_transfer_reservation']['message_type']=1;
  if (isset($ret['gks_transfer_reservation']['email_template'])==false) $ret['gks_transfer_reservation']['email_template']='default';
  if (isset($ret['gks_transfer_reservation']['tropos_apostolis'])==false or $ret['gks_transfer_reservation']['tropos_apostolis']==0) $ret['gks_transfer_reservation']['tropos_apostolis']=$GKS_ORDER_DEFAULT_DELIVERY;
  if (isset($ret['gks_transfer_reservation']['tropos_pliromis'])==false or  $ret['gks_transfer_reservation']['tropos_pliromis']==0)  $ret['gks_transfer_reservation']['tropos_pliromis']=$GKS_ORDER_DEFAULT_PAYMENT;
  
  
  
  if (isset($ret['gks_crm_tasks'])==false)                       $ret['gks_crm_tasks']=array();
  if (isset($ret['gks_crm_tasks']['message_type'])==false) $ret['gks_crm_tasks']['message_type']=1;
  if (isset($ret['gks_crm_tasks']['email_template'])==false) $ret['gks_crm_tasks']['email_template']='default';
  
  if (isset($ret['gks_crm_leads'])==false)                       $ret['gks_crm_leads']=array();
  if (isset($ret['gks_crm_leads']['message_type'])==false) $ret['gks_crm_leads']['message_type']=1;
  if (isset($ret['gks_crm_leads']['email_template'])==false) $ret['gks_crm_leads']['email_template']='default';
  
  
  
  if (isset($ret['dav'])==false)             $ret['dav']=array();
  if (isset($ret['dav']['password'])==false) $ret['dav']['password']='';
  
  if (isset($ret['calendar'])==false) $ret['calendar']=array();
  if (isset($ret['calendar']['view'])== false) $ret['calendar']['view']='timeGridWeek';
  if (isset($ret['calendar']['leftpanel'])== false) $ret['calendar']['leftpanel']='1';
  if (isset($ret['calendar']['full24'])== false) $ret['calendar']['full24']='1';
  if (isset($ret['calendar']['user_color'])== false) $ret['calendar']['user_color']='#3d85c6';
  if (isset($ret['calendar']['user_color_task'])== false) $ret['calendar']['user_color_task']='#bf9000';
  if (isset($ret['calendar']['visible_cal'])== false) $ret['calendar']['visible_cal']=1;
  if (isset($ret['calendar']['visible_task'])== false) $ret['calendar']['visible_task']=1;
  
  
  if (isset($ret['menu'])==false) $ret['menu']=array();
  if (isset($ret['menu']['pos'])== false) $ret['menu']['pos']='';
  if (isset($ret['menu']['sticky-top'])== false) $ret['menu']['sticky-top']='0';
  if (isset($ret['menu']['hover'])== false) $ret['menu']['hover']='0';
  if (isset($ret['menu']['narrow'])== false) $ret['menu']['narrow']='0';


  if (isset($ret['htmlcss'])==false) $ret['htmlcss']=array();
  if (isset($ret['htmlcss']['font_family'])== false) $ret['htmlcss']['font_family']='';
  if (isset($ret['htmlcss']['font_family_group'])== false) $ret['htmlcss']['font_family_group']='';
  if (isset($ret['htmlcss']['font_family_link'])== false) $ret['htmlcss']['font_family_link']='';
  if (isset($ret['htmlcss']['font_size'])== false) $ret['htmlcss']['font_size']='';
 
  //echo '<pre>';print_r($ret['htmlcss']);die();
 
  if (isset($ret['voip'])==false) $ret['voip']=array();
  if (isset($ret['voip']['extensions'])== false) $ret['voip']['extensions']='';
  $temp=explode(',',$ret['voip']['extensions']);$ret['voip']['extensions']=[];
  foreach ($temp as $value) {
    if ($value!='' and ctype_digit($value)) {
      $ret['voip']['extensions'][]=$value;
    }
  } 

 
  if (isset($ret['lang'])==false) $ret['lang']=array();
  if (isset($ret['lang']['backend'])==false) {
    $ret['lang']['backend']='el-GR';
    gks_set_user_settings($user_id, array('lang'=>array('backend'=>'el-GR')));
  }
  
  
  
  //print '<pre>';print_r($ret);die();
  
  if (isset($ret['autocomplete'])==false) $ret['autocomplete']=array();
  //if (isset($ret['autocomplete']['address'])==false) $ret['autocomplete']['address']='from_db';
  if (isset($ret['autocomplete']['address'])==false) $ret['autocomplete']['address']='from_googlemaps';
  
  if ($GKS_GOOGLE_MAPS_API_KEY=='' && $ret['autocomplete']['address']=='from_googlemaps') $ret['autocomplete']['address']='from_db';
  
  //print '<pre>';print_r($ret);die();
  return $ret;
}

function gks_set_user_settings($user_id, $myarray) {
  global $db_link;
  foreach ($myarray as $myobject => $myobjects) {
    foreach ($myobjects as $mysubobject  => $value) {
      $sql="replace into gks_settings_users (
        user_id,myobject,mysubobject,myvalue
      ) values (
        ".$user_id.",'".$db_link->escape_string($myobject)."','".$db_link->escape_string($mysubobject)."','".$db_link->escape_string($value)."'
      )";
    
      $result = $db_link->query($sql);
      if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
    }
  } 
  return true;
}

function gks_from_googlemaps_scripts($libraries='',$callback='gks_map_js_load_initialize_default',$loading_async=true, $andmeasuretool=false) {
  global $gks_user_settings;
  global $GKS_GOOGLE_MAPS_API_KEY;
  $myret='';
  if ($libraries=='') $libraries='places,marker';
  //places,drawing,geometry
  if ($GKS_GOOGLE_MAPS_API_KEY!='' and ($gks_user_settings['autocomplete']['address']=='from_googlemaps' or strpos($libraries,'drawing')!==false)) {
    //$myret='<script '.($loading_async ? 'async' : '').' type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.61&libraries='.$libraries.'&key='.$GKS_GOOGLE_MAPS_API_KEY.($loading_async ? '&loading=async' : '').($callback=='' ? '' : '&callback='.$callback).'"></script>'."\n";
    $myret='<script '.($loading_async ? 'async' : '').' type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.65&libraries='.$libraries.'&key='.$GKS_GOOGLE_MAPS_API_KEY.($loading_async ? '&loading=async' : '').($callback=='' ? '' : '&callback='.$callback).'"></script>'."\n";

  } else {
    $myret='<!-- 
    GKS_GOOGLE_MAPS_API_KEY is empty or 
    autocomplete_address: '.$gks_user_settings['autocomplete']['address'].' !=from_googlemaps
    -->'."\n";
  }
  if ($andmeasuretool) {
    $myret.='<script src="js/measuretool-googlemaps-v3.js"></script>'."\n";
  }
  return $myret;
}

function color_inverse($color){
  
    if ($color===null) return '#000000';
    $color = str_replace('#', '', $color);
    if (strlen($color) != 6){ return '#000000'; }
    //012345
    $r = 255 - hexdec(substr($color,0,2));
    $g = 255 - hexdec(substr($color,2,2));
    $b = 255 - hexdec(substr($color,4,2));
    
    
    if ($r > 100 and $r < 150 and 
        $g > 100 and $g < 150 and 
        $b > 100 and $b < 150 ) {
      return '#ffffff';
    }
        
    if ($r < 100 and 
        $g < 100 and 
        $b < 100 ) {
      return '#000000';
    }
      
    if ($r > 150 and 
        $g > 150 and 
        $b > 150 ) {
      return '#ffffff';
    }
        
    $r = ($r < 0) ? 0 : dechex($r);
    $g = ($g < 0) ? 0 : dechex($g);
    $b = ($b < 0) ? 0 : dechex($b);
    
    
    $rgb = '';
    $rgb .= (strlen($r) < 2) ? '0'.$r : $r;
    $rgb .= (strlen($g) < 2) ? '0'.$g : $g;
    $rgb .= (strlen($b) < 2) ? '0'.$b : $b;
    

    return '#'.$rgb;
}


function gks_get_leads_status(&$array,&$styles) {
  global $db_link;
  $sql="select * from gks_crm_leads_status order by lead_status_sortorder";
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
  
  $styles='';
  $array=array();
  while ($row = $result->fetch_assoc()) {
    $array[$row['id_crm_lead_status']]=$row;
    $styles.='.lead_status_'.$row['id_crm_lead_status'].'        {border-radius: 10px; background: '.$row['lead_status_color'].'; padding:0px 10px 0px 10px; border: 1px solid #000000; color:'.color_inverse($row['lead_status_color']).';white-space: nowrap;}'."\n";
  }
  return true;
}

function gks_get_tasks_status(&$array,&$styles) {
  global $db_link;
  $sql="select * from gks_crm_tasks_status order by task_status_sortorder";
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);die('error sql');}  
  
  $styles='';
  $array=array();
  while ($row = $result->fetch_assoc()) {
    $array[$row['id_crm_task_status']]=$row;
    $styles.='.task_status_'.$row['id_crm_task_status'].'        {border-radius: 10px; background: '.$row['task_status_color'].'; padding:0px 10px 0px 10px; border: 1px solid #000000; color:'.color_inverse($row['task_status_color']).';white-space: nowrap;}'."\n";
  }
  return true;
}

function gks_random_string($length=5) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[random_int(0, $charactersLength - 1)];
  }
  return $randomString;
}
    
function gks_guid_for_user() {
  global $db_link;
  while (true) {
    mt_srand(intval((double)microtime()*10000));//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = ''; //chr(45);// "-"
    $guid = substr($charid, 0, 8).'-'
        .substr($charid, 8, 4).'-'
        .substr($charid,12, 4).'-'
        .substr($charid,16, 4).'-'
        .substr($charid,20,12);
    $guid = strtolower($guid);
    $sql = "SELECT uid from gks_user_carddav where uid='".$db_link->escape_string($guid)."'";
    $result = $db_link->query($sql);
    
    if ($result->num_rows == 0) {
      return $guid; 
    }
  }
}

function gks_user_update_dav($id,$is_new_rec) {
  global $db_link;
  global $gkIP;
  
  
  
  $sql = "SELECT ".GKS_WP_TABLE_PREFIX."users.*, 
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit,
  gks_eshop_fiscal_position.fiscal_position_descr, gks_eshop_pricelist.pricelist_descr,
  gks_users.eponimia, gks_users.title, gks_users.afm, gks_users.doy, gks_users.epaggelma, 
  gks_users.ma_odos, gks_users.ma_arithmos, gks_users.ma_orofos, gks_users.ma_perioxi, 
  gks_users.ma_poli, gks_users.ma_tk, 
  gks_users.ma_country_id, gks_users.ma_nomos_id, 
  gks_users.phone_home, gks_users.genisi_date, gks_users.ethnikotita, 
  gks_users.alli_apasxolisi,gks_users.cv_proipiresia, gks_users.cv_spoydes, gks_users.order_sxolio,gks_users.pelati_sxolio, gks_users.cv_seminaria, gks_users.cv_mitriki_glossa, gks_users.cv_jenes_glosses,
  gks_users.cv_sxesi_me_photografia, gks_users.cv_metaforiko_meso, gks_users.cv_has_bike, gks_users.cv_has_motorcycle, gks_users.cv_has_car,gks_users.cv_has_car_theseis,
  gks_users.profilepososto_user, gks_users.profilepososto_job,
  gks_country.country_name, gks_nomoi.nomos_descr, 
  table_last_name.mylast_name, table_first_name.myfirst_name, table_mobile.mymoobile, table_roles.mywp_capabilities,
  gks_users.user_HumanInitial,
  gks_users.amka ,gks_users.ama_eam ,gks_users.arithmos_tautoitas ,gks_users.arxi_ekdosis, gks_users.onoma_patera, gks_users.onoma_miteras,
  gks_users_oikogeniaki_katastasti.oikogeniaki_katastasti_descr,gks_users.oikogeniaki_katastasti_id, gks_users.oikogeniaki_katastasti_paidia,
  gks_users.sistasi_from,gks_users.days_to_work,
  gks_users.ma_latitude,gks_users.ma_longitude,
  gks_user_carddav.uid
  FROM ((((((((((((".GKS_WP_TABLE_PREFIX."users 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on ".GKS_WP_TABLE_PREFIX."users.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on ".GKS_WP_TABLE_PREFIX."users.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN gks_user_carddav ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_user_carddav.ID) 
  LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
  LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country) 
  LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos)
  LEFT JOIN gks_eshop_fiscal_position ON ".GKS_WP_TABLE_PREFIX."users.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
  LEFT JOIN gks_eshop_pricelist ON ".GKS_WP_TABLE_PREFIX."users.pricelist_id = gks_eshop_pricelist.id_pricelist) 
  LEFT JOIN gks_users_oikogeniaki_katastasti ON gks_users_oikogeniaki_katastasti.id_oikogeniaki_katastasti = gks_users.oikogeniaki_katastasti_id) 
  LEFT JOIN (
    SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS myfirst_name
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='first_name'))
  )  AS table_first_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_first_name.user_id) 
  LEFT JOIN (
    SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mylast_name
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='last_name'))
  )  AS table_last_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_last_name.user_id) 
  LEFT JOIN (
    SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mymoobile
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='mobile'))
  )  AS table_mobile ON ".GKS_WP_TABLE_PREFIX."users.ID = table_mobile.user_id) 
  LEFT JOIN (
    SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mywp_capabilities
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='".GKS_WP_TABLE_PREFIX."capabilities'))
  )  AS table_roles ON ".GKS_WP_TABLE_PREFIX."users.ID = table_roles.user_id
  where ".GKS_WP_TABLE_PREFIX."users.id = ".$id;  

  //echo '<pre>'.$sql;die();
	$result = $db_link->query($sql);  
	if (!$result) {
	  debug_mail(false,'error sql',$sql);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); }  

	if ($result->num_rows<=0) return;
	$row = $result->fetch_assoc();
	$uid=trim_gks($row['uid']);
	if ($uid=='') $uid=gks_guid_for_user();
	

  $sql_comm="select * from gks_users_communication where user_id=".$id." order by comm_primary desc";
  $result_comm = $db_link->query($sql_comm);
  if (!$result_comm) {debug_mail(false,'admin-users-item.php error sql',$sql_comm);die('sql error');}
  $user_communication=array();
  $user_communication['email']=array();
  $user_communication['phone']=array();
  $user_communication['url']=array();
  while ($row_comm = $result_comm->fetch_assoc()) {
    if (isset($user_communication[$row_comm['comm_type']][$row_comm['comm_value']])==false) {
      $user_communication[$row_comm['comm_type']][$row_comm['comm_value']]=array('descr' => $row_comm['comm_descr'],'isp' => $row_comm['comm_primary']);
    } else {
      $user_communication[$row_comm['comm_type']][$row_comm['comm_value']]['descr']=$row_comm['comm_descr'];
      $user_communication[$row_comm['comm_type']][$row_comm['comm_value']]['isp']=$row_comm['comm_primary'];
    }    
  }
  //print '<pre>';print_r($user_communication);die();
  
	
  $vcard = new Sabre\VObject\Component\VCard();
  $vcard->UID=$uid;
  if (trim_gks($row['gks_nickname'])!='') 
    $vcard->add('FN', $row['gks_nickname'], ['CHARSET' => 'utf-8']);

  if (trim_gks($row['mylast_name'])!='' or trim_gks($row['myfirst_name'])!='') 
    $vcard->add('N', [$row['mylast_name'], $row['myfirst_name'],'',''], ['CHARSET' => 'utf-8']);

  
  foreach ($user_communication['phone'] as $val=>$item) {
    switch ($item['descr']) {  
      case 'Εργασία':
      case 'Work':
      case gks_lang('Εργασία'):
        $vcard->add('TEL', $val, ['type' =>['WORK','VOICE'], 'PREF' => $item['isp']]); break;
      case 'Κινητό':
      case 'Mobile':
      case 'Cell':
      case gks_lang('Κινητό'):
        $vcard->add('TEL', $val, ['type' =>'CELL', 'PREF' => $item['isp']]); break;
      case 'Κινητό Εργασίας':
      case 'Work Cell':
      case gks_lang('Κινητό Εργασίας'):
        $vcard->add('TEL', $val, ['type' =>['WORK','CELL'], 'PREF' => $item['isp']]); break;
      case 'Κινητό Προσωπικό':
      case 'Private Cell':
      case gks_lang('Κινητό Προσωπικό'):
        $vcard->add('TEL', $val, ['type' =>'CELL', 'PREF' => $item['isp']]); break;
      case 'Σπίτι':
      case 'House':
      case gks_lang('Σπίτι'):
        $vcard->add('TEL', $val, ['type' =>'HOME', 'PREF' => $item['isp']]); break;
      case 'Φαξ':
      case 'Fax':
      case gks_lang('Φαξ'):
        $vcard->add('TEL', $val, ['type' =>'FAX', 'PREF' => $item['isp']]); break;
      case 'Φαξ Σπιτιού':
      case 'House Fax':
      case gks_lang('Φαξ Σπιτιού'):
        $vcard->add('TEL', $val, ['type' =>['HOME','FAX'], 'PREF' => $item['isp']]); break;
      case 'Φαξ Εργασίας':
      case 'Work Fax':
      case gks_lang('Φαξ Εργασίας'):
        $vcard->add('TEL', $val, ['type' =>['WORK','FAX'], 'PREF' => $item['isp']]); break;
      case 'Σταθερό':
      case 'Landline':
      case gks_lang('Σταθερό'):
        $vcard->add('TEL', $val, ['type' =>['WORK','VOICE'], 'PREF' => $item['isp']]); break;
      case 'Σταθερό Σπιτιού':
      case 'Landline House':
      case gks_lang('Σταθερό Σπιτιού'):
        $vcard->add('TEL', $val, ['type' =>['HOME','VOICE'], 'PREF' => $item['isp']]); break;
      case 'Σταθερό Εργασίας':
      case 'Landline Work':
      case gks_lang('Σταθερό Εργασίας'):
        $vcard->add('TEL', $val, ['type' =>['WORK','VOICE'], 'PREF' => $item['isp']]); break;
      default: 
        $vcard->add('TEL', $val, ['type' => $item['descr'],['CHARSET' => 'utf-8']]); 
          
    }
  }
  
  foreach ($user_communication['email'] as $val=>$item) {
    switch ($item['descr']) {  
      case 'Εργασίας':
      case gks_lang('Εργασίας'):
        $vcard->add('EMAIL', $val, ['type' =>'WORK']); break;
      case 'Προσωπικό':
      case gks_lang('Προσωπικό'):
        $vcard->add('EMAIL', $val, ['type' =>'HOME']); break;
      default: 
        $vcard->add('EMAIL', $val, ['type' => $item['descr'],['CHARSET' => 'utf-8']]); 
    }
  }
  foreach ($user_communication['url'] as $val=>$item) {
    switch ($item['descr']) {  
      case 'Προσωπικό':
      case 'Personal':
      case gks_lang('Προσωπικό'):
        $vcard->add('URL', $val, ['type' =>'HOME']); break;
      case 'Εταιρικό':
      case 'Corporate':
      case gks_lang('Εταιρικό'):
        $vcard->add('URL', $val, ['type' =>'WORK']); break;
      case 'Εταιρικό site':
      case 'Corporate site':
      case gks_lang('Εταιρικό site'):
        $vcard->add('URL', $val, ['type' =>'WORK']); break;
      case 'Προφίλ':
      case 'Profile':
      case gks_lang('Προφίλ'):
        $vcard->add('URL', $val, ['type' =>'HOME']); break;
      case 'Ιστολόγιο':
      case 'Blog':
      case gks_lang('Ιστολόγιο'):
        $vcard->add('URL', $val, ['type' =>'HOME']); break;
      default:
        $vcard->add('URL', $val, ['type' => $item['descr'],['CHARSET' => 'utf-8']]); 
    }
  }
  
  $val=GKS_SITE_URL.'my/admin-users-item-overview.php?id='.$id;
  
  $vcard->add('URL', $val, ['type' => 'gks ERP',['CHARSET' => 'utf-8']]); 
  
  
//  if (trim_gks($row['mymoobile'])!='') 
//    $vcard->add('TEL', $row['mymoobile'], ['type' => 'CELL','PREF' => 1]);
//  if (trim_gks($row['phone_home'])!='') 
//    $vcard->add('TEL', $row['phone_home'], ['type' => 'WORK']);
//  if (trim_gks($row['user_url'])!='') 
//    $vcard->add('URL', $row['user_url'], ['type' => 'WORK']);
//  if (trim_gks($row['user_email'])!='') 
//    $vcard->add('EMAIL', $row['user_email'], ['type' => 'WORK']);
  
  if (trim_gks($row['title'])!='') 
    $vcard->add('ORG', $row['title'], ['CHARSET' => 'utf-8']);
  

  if (trim_gks($row['ma_odos'])!='' or 
      trim_gks($row['ma_poli'])!='' or 
      trim_gks($row['nomos_descr'])!='' or 
      trim_gks($row['ma_tk'])!='' or 
      trim_gks($row['country_name'])!='') {
    $vcard->add('ADR', ['', '',
    trim_gks(trim_gks($row['ma_odos']).' '.trim_gks($row['ma_arithmos'])),
    trim_gks($row['ma_poli']),trim_gks($row['nomos_descr']),trim_gks($row['ma_tk']),trim_gks($row['country_name'])], ['type' => 'WORK','PREF' => 1,'CHARSET' => 'utf-8']);
  }
  
  //$vcard->add('ADR', $topothesia, ['type' => 'WORK,PREF','CHARSET' => 'utf-8']);
  
  //ADR;TYPE=WORK,PREF:;;100 Waters Edge;Baytown;LA;30314;United States of America
  
  if (trim_gks($row['pelati_sxolio'])!='') 
    $vcard->add('NOTE', $row['pelati_sxolio'], ['CHARSET' => 'utf-8']);

  if ($row['ma_latitude']!=0 and $row['ma_longitude']!=0) 
    $vcard->add('GEO', [$row['ma_latitude'],$row['ma_longitude']]);
  
  $gks_wsl_current_user_image=trim_gks($row['gks_wsl_current_user_image']);
  if ($gks_wsl_current_user_image!='' and endwith(strtolower($gks_wsl_current_user_image), '.jpg')) {
    
    $local_file='';
    if (startwith($gks_wsl_current_user_image,GKS_SITE_URL))
      $local_file= GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/'.substr($gks_wsl_current_user_image, strlen(GKS_SITE_URL));
    else if (startwith($gks_wsl_current_user_image,'/my/'))
      $local_file= GKS_SITE_PATH.GKS_SITE_HTTPDOCS.$gks_wsl_current_user_image;
      
    if ($local_file!= '' and file_exists($local_file)) {
      $tmp_file=GKS_SITE_PATH.'tmp/'.rand(1000,9999).'.jpg';
      makeThumbnails_square($local_file,$tmp_file,100,false);
      //makeThumbnails_normal($local_file,$tmp_file,100,100,false);
      if (file_exists($tmp_file)) {
        $local_file=base64_encode(file_get_contents($tmp_file));
        //echo $local_file;
        //$vcard->add('PHOTO', $local_file, ['type' => 'JPEG','ENCODING' => 'BASE64']);
        // ok $vcard->add('PHOTO', 'data:image/jpg;base64,'.$local_file, ['ENCODING' => 'b', 'TYPE' => 'image/jpeg']); //'TYPE' => $image->mimeType()]);
        $vcard->add('PHOTO', 'data:image/jpg;base64,'.$local_file);
        //$vcard->add('PHOTO', 'data:image/jpg;base64,'.$local_file, ['ENCODING' => 'b', 'TYPE' => 'image/jpeg']); //'TYPE' => $image->mimeType()]);
      }
    }
    
  }

  
  //$vcard->add('VERSION','2.1');
   

  
  //https://hotexamples.com/examples/sabre.vobject.component/VCard/add/php-vcard-add-method-examples.html
  
  $vcard_str = $vcard->serialize();
  //$vcard_str=str_replace('PHOTO;ENCODING=b,b;TYPE=JPEG:','PHOTO;TYPE=JPEG;ENCODING=b:',$vcard_str);
  //$vcard_str=str_replace('PHOTO;ENCODING=b,b;TYPE=image/jpeg:','PHOTO;ENCODING=b;TYPE=image/jpeg:',$vcard_str);
  //echo  '<pre>'.$vcard_str;die();                      

	
  $etag= md5($vcard_str);
  $size = strlen($vcard_str);
  $uri=$uid.'.vcf';
  
  
  
  $sql_prev="select * from gks_user_carddav where ID=".$id;
	$result_event = $db_link->query($sql_prev);  
	if (!$result_event) {
	  debug_mail(false,'error sql',$sql_prev);
	  $return = array('success' => false, 'message' => base64_encode('sql error'));
	  echo json_encode($return); die(); } 
	$will_update=false;
	if ($result_event->num_rows==0) {  
    $will_update=true;
    $is_new_rec=true;
    //echo '<pre>sss '.$will_update;die();
  } else {
    $row_event = $result_event->fetch_assoc();
    $old_carddata=$row_event['carddata'];
    if ($old_carddata!=$vcard_str) {
      $will_update=true;
    }
  }
  //echo '<pre>ss '.$will_update.'|'.$uri ;die();

  if ($will_update) {
    $sql_event="replace into gks_user_carddav (
    ID,carddata,etag,size,uid,
    myip,mydate_edit
    ) values (
    ".$id.",
    '".$db_link->escape_string($vcard_str)."',
    '".$db_link->escape_string($etag)."',
    ".$size.",
    '".$db_link->escape_string($uid)."',
    '".$db_link->escape_string($gkIP)."',
    now()
    )";
    
  	$result_event = $db_link->query($sql_event);  
  	if (!$result_event) {
  	  debug_mail(false,'error sql',$sql_event);
  	  $return = array('success' => false, 'message' => base64_encode('sql error'));
  	  echo json_encode($return); die(); }  
  
  
  
    $sql_event="select myvalue from gks_settings where mykey='carddav_synctoken'";
  	$result_event = $db_link->query($sql_event);  
  	if (!$result_event) {
  	  debug_mail(false,'error sql',$sql_event);
  	  $return = array('success' => false, 'message' => base64_encode('sql error'));
  	  echo json_encode($return); die(); }  
      
    $carddav_synctoken=0;
    if ($result_event->num_rows>=1) {
      $row_event = $result_event->fetch_assoc();
      $carddav_synctoken=intval($row_event['myvalue']);
    }
    //echo '<pre>ss '.$carddav_synctoken.'|'$sql_event;die();

    $sql_event="update gks_settings set myvalue='".trim_gks($carddav_synctoken + 1)."' where  mykey='carddav_synctoken'";
  	$result_event = $db_link->query($sql_event); 
  	if (!$result_event) {
  	  debug_mail(false,'error sql',$sql_event);
  	  $return = array('success' => false, 'message' => base64_encode('sql error'));
  	  echo json_encode($return); die(); }
    
    $operation=($is_new_rec ? 1 : 2);
    $sql_change="INSERT INTO gks_users_dav_changes (
    uri, synctoken, addressbookid, operation
    ) values (
    '".$db_link->escape_string($uri)."',".$carddav_synctoken.",1,".$operation."
    )";
  	$result_change = $db_link->query($sql_change); 
  	if (!$result_change) {
  	  debug_mail(false,'error sql',$sql_change);
  	  $return = array('success' => false, 'message' => base64_encode('sql error'));
  	  echo json_encode($return); die(); }  
    
  }
  
    
  //echo '<pre>'.$vcard_str;die();
  
  
  //echo '<pre>'.time(); die();
  
  gks_user_fix_phone_numbers($id);
  
}
function gks_user_fix_phone_numbers($id) {
  global $db_link;
  if ($id<=0) return;
  
  $sql_comm="SELECT gks_users_communication.id_user_communication, gks_users_communication.comm_value, 
  gks_users.ma_country_id, gks_country.phone_code
  FROM (gks_users_communication 
  LEFT JOIN gks_users ON gks_users_communication.user_id = gks_users.user_id) 
  LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country
  WHERE gks_users_communication.comm_type='phone'
  AND gks_users_communication.user_id=".$id."
  order by id_user_communication";
  $result_comm = $db_link->query($sql_comm);
  if (!$result_comm) {debug_mail(false,'error sql',$sql_comm);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }

  $data=array();
  while ($row_comm = $result_comm->fetch_assoc()) {
    $data[]=$row_comm;
  }
  if (count($data)==0) return;

  $mynumbers=array('+','0','1','2','3','4','5','6','7','8','9');
  
  $country_phone_code=$data[0]['phone_code'];
  $country_phone_code_clean='';
  if (is_string($country_phone_code)) {
    for($i=0; $i< strlen($country_phone_code);$i++) {
      if (in_array($country_phone_code[$i],$mynumbers)) $country_phone_code_clean.=$country_phone_code[$i];
    }
  }
  if ($country_phone_code_clean=='' and $data[0]['ma_country_id']==0) $country_phone_code_clean='30';
  
  //echo '<pre>'.$country_phone_code_clean;die();
  
  foreach ($data as $value) {
    $phone=$value['comm_value'];
    $phone_clean='';
    for($i=0; $i< strlen($phone);$i++) {
      if (in_array($phone[$i],$mynumbers)) $phone_clean.=$phone[$i];
    }
    
    //if (startwith($phone_clean,'00'.$country_phone_code_clean)) {
    //  $phone_clean=substr($phone_clean, 2);
    //} else if (startwith($phone_clean,$country_phone_code_clean)==false) {
    //  $phone_clean=$country_phone_code_clean.$phone_clean;
    //}
    
    $phone_clean=gks_phone_number_fix($country_phone_code_clean,$phone_clean);
    
    $sql_comm="update gks_users_communication set
    phone_fix='".$phone_clean."'
    where id_user_communication=". $value['id_user_communication'];
    $result_comm = $db_link->query($sql_comm);
    if (!$result_comm) {debug_mail(false,'error sql',$sql_comm);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
    
    //echo '<pre>'.$phone_clean;die();
    
  }
}

function gks_user_update_comm_search($id) {
  global $db_link;
  
  $sql_comm="select * from gks_users_communication where user_id=".$id." order by id_user_communication";
  $result_comm = $db_link->query($sql_comm);
  if (!$result_comm) {debug_mail(false,'error sql',$sql_comm);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
  $comm_search='';
  while ($row_comm = $result_comm->fetch_assoc()) {
    $comm_search.=trim_gks($row_comm['comm_value']).','.trim_gks($row_comm['comm_descr']).',';
  }  
  $comm_search=trim_gks($comm_search);
  $comm_search=str_replace(',,' , ',' , $comm_search);
  
  $sql_comm="update ".GKS_WP_TABLE_PREFIX."users set comm_search='".$db_link->escape_string($comm_search)."' where ID=".$id;
  $result_comm = $db_link->query($sql_comm);
  if (!$result_comm) {debug_mail(false,'error sql',$sql_comm);$return = array('success' => false, 'message' => base64_encode('sql error'));echo json_encode($return); die(); }
  
  
}





function getActivityStatusDescr($mystate,$load_lang='') {
  global $gks_user_settings;
  if ($load_lang=='') {
    $load_lang='el-GR';
    if (isset($gks_user_settings['lang']['backend'])) $load_lang = gks_erp_supperted_lang($gks_user_settings['lang']['backend']);
  }
  
  if ($load_lang=='el-GR') {  
    switch ($mystate) {
      case '050new': return gks_lang('Νέα','part4','getActivityStatusDescr'); break; 
      case '100done': return gks_lang('Έγινε','part4','getActivityStatusDescr'); break; 
      case '200cancel': return gks_lang('Άκυρο','part4','getActivityStatusDescr'); break; 
      default: return $mystate; break; 
    } 
  } else {
    switch ($mystate) {
      case '050new': return 'New'; break; 
      case '100done': return 'Done'; break; 
      case '200cancel': return 'Cancel'; break; 
      default: return $mystate; break; 
    } 
  }
}

function gks_week_date_ranges($use_startvardia) {
  
  //panta ta isxiei i vardia 
  $use_startvardia=true;
  

  
//  $this_startvardia=GKS_ERP_START_VARDIA;
//  if ($use_startvardia==false) $this_startvardia=0;
//  
//  $mydaydif=0;
//  $mytimenow=time() + $mydaydif*24*60*60;
//  $time_vardia=_time_user($mytimenow, 1);
//  $time_vardia-= $this_startvardia*60*60;
//  $today_vardia = date('Y-m-d',$time_vardia);
//  $today_vardia = strtotime($today_vardia) + $this_startvardia*60*60;
//  $today_vardia = _time_user($today_vardia, -1);
//  $today_vardia_time = $today_vardia;
//  $today_vardia_midle=$today_vardia;
  
  $today=showDate(time() + ($use_startvardia ? -GKS_ERP_START_VARDIA*60*60 : 0), 'Y-m-d',1);
  $today_vardia=strtotime($today);
  $today_vardia=_time_user($today_vardia,-1);
  
  $today_vardia_str = date('Y-m-d H:i:s', $today_vardia);  
  //echo $today_vardia_str;die();
  
  $ret=array();
  for ($i=-6; $i<=6; $i++) {
    $from  = $today_vardia +  $i     * 24*60*60 + GKS_ERP_START_VARDIA*60*60;   // >=
    $to  =   $today_vardia + ($i +1) * 24*60*60 + GKS_ERP_START_VARDIA*60*60;   // <
    $from_str=date('Y-m-d H:i:s', $from); 
    $to_str  =date('Y-m-d H:i:s', $to); 
    $descr1='';
    switch ($i) {   
      case -2: $descr1=gks_lang('Προχθές');break;  
      case -1: $descr1=gks_lang('Χθες');break;  
      case 0: $descr1=gks_lang('Σήμερα');break;  
      case 1: $descr1=gks_lang('Αύριο');break;  
      case 2: $descr1=gks_lang('Μεθαύριο');break;  
    }
    if ($descr1=='' and $i>0) {
      $descr1=getWeekDayName(date('w',$from + 12*60*60));
    } else if ($descr1=='' and $i<0) {
      $descr1='Πρ. '.getWeekDayName(date('w',$from + 12*60*60));
    }
    $descr2=myDateFormatw($from + 12*60*60); //to meso tis imerew, gia asfaleia
    
    $ret[$i]=array(
      'from'=>$from,
      'to'=>$to,
      'from_str'=>$from_str,
      'to_str'=>$to_str,
      'descr1'=>$descr1,
      'descr2'=>$descr2,
    );
  }
  return $ret;
}


function getActivityduedateDescr($duedate,$status,$week_date_ranges) {

  //echo '<pre>';print_r($week_date_ranges);die();
  
  $duedate=strtotime($duedate); // - (24 + GKS_ERP_START_VARDIA)*60*60;
  
  $ret='';
  
  $class_status='_'.$status;
  
    

  if ($duedate < $week_date_ranges[-6]['from']) {
    $ret='<span class="activity_duedate_past'.$class_status.'">'.myDateTimeFormatw(_time_user($duedate,1)).'</span>';  
  } else if ($duedate >= $week_date_ranges[6]['to']) {
    $ret='<span class="activity_duedate_future'.$class_status.'">'.myDateTimeFormatw(_time_user($duedate,1)).'</span>';  
  } else {
    for ($i=-6; $i<=6; $i++) {
      if ($duedate >= $week_date_ranges[$i]['from'] && $duedate < $week_date_ranges[$i]['to']) {
        $ret='<span class="';
        if ($i==0) {
          $ret.='activity_duedate_today';
        } else if ($i < 0) {
          $ret.='activity_duedate_past';
        } else if ($i > 0) {
          $ret.='activity_duedate_future';
        }
        $ret.=$class_status.'">'.$week_date_ranges[$i]['descr1'].'</span>';
        break; 
      }
    }
    
  }
  
  

  
  return $ret;
}



function getActivityObjectTable($model='', $model_id=0) {
  global $db_link;
  global $GKS_CRM_ENABLE;
  if ($GKS_CRM_ENABLE==false) return '';
  
  $ret='';
  $ret.=
  '<div class="card gks_card_expand">
    <div class="card-header" style="text-align:center">
      <span style="vertical-align: middle;">'.gks_lang('Δραστηριότητα').'</span>
      <button type="button" class="btn btn-sm btn-primary" id="activity_add">'.gks_lang('Προσθήκη').'</button>
    </div>
    <div class="card-body" '.gks_card_body('act').'> 

      <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" 
        cellpadding="5" align="center" id="activity_table">
        <thead>
          <tr>
            <th class="table-dark" scope="col" width="0%" nowrap>#</th>
            <th class="table-dark" scope="col" width="0%" nowrap></th>
            <th class="table-dark" scope="col" width="0%" nowrap>'.gks_lang('Κατάσταση').'</th>
            <th class="table-dark" scope="col" width="20%" nowrap>'.gks_lang('Ποιος').'</th>
            <th class="table-dark" scope="col" width="20%" nowrap align="left">'.gks_lang('Τι').'</th>
            <th class="table-dark" scope="col" width="20%" nowrap align="left">'.gks_lang('Έως πότε').'</th>
            <th class="table-dark" scope="col" width="20%" nowrap align="left">'.gks_lang('Θέμα').'</th>
            <th class="table-dark" scope="col" width="20%" nowrap align="left">'.gks_lang('Σχόλιο').'</th>
          </tr>
        </thead>  
        <tbody>'; 
          
        
        //$week_date_ranges=gks_week_date_ranges(false);
        
        $sql_activity="SELECT gks_crm_activity.*, ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
        ".GKS_WP_TABLE_PREFIX."users.gks_nickname, gks_crm_activity_types.crm_activity_type_descr,gks_crm_activity_types.crm_activity_type_icon
        FROM (((gks_crm_activity LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_crm_activity.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
        LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_crm_activity.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
        LEFT JOIN gks_crm_activity_types ON gks_crm_activity.activity_type_id = gks_crm_activity_types.id_crm_activity_type) 
        LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_activity.activity_user_id = ".GKS_WP_TABLE_PREFIX."users.ID
        WHERE gks_crm_activity.activity_model='".$db_link->escape_string($model)."'
        AND gks_crm_activity.activity_model_id=".$model_id."
        ORDER BY gks_crm_activity.activity_duedate DESC , id_crm_activity desc";

        $result_activity = $db_link->query($sql_activity);        
        if (!$result_activity) debug_mail(false,'error sql',$sql_activity);
        if (!$result_activity) return 'sql error';
        
        $j = 0;
        while ($row_activity = $result_activity->fetch_assoc()) {
          $j++; 
          $type_icon='';
          if (!empty($row_activity['crm_activity_type_icon'])) {
            $type_icon=$row_activity['crm_activity_type_icon'];
            if (trim_gks($row_activity['activity_color'])!='') {
              $type_icon=str_replace(' class="', ' style="color:'.$row_activity['activity_color'].'" class="', $type_icon);
            }
            $type_icon.=' ';
          }
          
          
        $ret.=
        '<tr class="activity_tr_exist" data-id="'.$row_activity['id_crm_activity'].'">
          <th scope="row" nowrap class="mytdcm activity_aa">'.$j.'</th>
          <td nowrap class="mytdcm">
            <i class="activity_edit enterrow fas fa-pen" data-id="'.$row_activity['id_crm_activity'].'" title="'.gks_lang('Επεξεργασία').'"></i>'.
            $row_activity['id_crm_activity'].
            ' <i class="fas fa-trash-alt deleterow" data-deleteafter="gks_fnc_activity_delete_after|'.$row_activity['id_crm_activity'].'" '.
            'data-id="'.$row_activity['id_crm_activity'].'" data-model="gks_crm_activity"></i>  
          </td>  
          <td nowrap class="mytdcm"><span class="activity_status_'.$row_activity['activity_status'].'">'.
          getActivityStatusDescr($row_activity['activity_status']).'</span></td>  
          <td        class="mytdcml">'.$row_activity['gks_nickname'].'</td>  
          <td        class="mytdcml">'; 
            if ($row_activity['activity_type_id']==4 and $row_activity['calendar_id']>0) { //meeting
              $ret.= $type_icon.'<a href="admin-crm-calendar.php?id='.$row_activity['calendar_id'].'">'.$row_activity['crm_activity_type_descr'].'</a>';
            } else {
              $ret.= $type_icon.$row_activity['crm_activity_type_descr'];
            }
          $ret.='</td>    
          <td class="mytdcml">';
            
            
            //$ret.= getActivityduedateDescr($row_activity['activity_duedate'],$row_activity['activity_status'],$week_date_ranges);
            if ($row_activity['activity_notification']==1) {
              $ret.='<i class="activity_notification_bell fas fa-bell"></i> ';
            }
            $ret.= secondsago(strtotime($row_activity['activity_duedate']));
            if ($row_activity['activity_type_id']==4) { //meeting
              if ($row_activity['calendar_id']>0) {
                $ret.= '<br><a href="admin-crm-calendar.php?id='.$row_activity['calendar_id'].'">'.showDate(strtotime($row_activity['activity_duedate']),'H:i',1).'</a>';
              } else {
                $ret.= '<br>'.showDate(strtotime($row_activity['activity_duedate']),'H:i',1);
              }
            }
            
            //echo '<br>'.$row_activity['activity_duedate'];
          $ret.='</td>  
          <td ';
            if (trim_gks($row_activity['activity_color'])!='') {
              $ret.= ' style="background-color:'.$row_activity['activity_color'].'"';  
            }
            $ret.='>'.$row_activity['activity_subject'].'</td>    
          <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand">'. 
            nl2br_gks($row_activity['activity_message']).
          '</div></div></td> 
        </tr>';
        }                     
        $ret.='</tbody>   
      </table>      
  
    </div>
  </div>';  
  
  return $ret;
  
}

function gks_card_expand() {
  global $db_link;
  global $my_wp_user_id;
  if ($my_wp_user_id<=0) return array();
  if (isset($_SERVER['SCRIPT_FILENAME'])==false) return array();
  $url=trim_gks(basename(trim_gks($_SERVER['SCRIPT_FILENAME'])));
  if ($url=='') return array();
  
  $sql="select item, state from gks_users_card_expand where item<>'' and user_id=".$my_wp_user_id." and url='".$db_link->escape_string($url)."'";
  $result = $db_link->query($sql);
  if (!$result) return array();
  $ret=array();
  while ($row = $result->fetch_assoc()) {
    $ret[$row['item']]=intval($row['state']);
  }
  //print '<pre>'.$sql;print_r($ret);die();
  return $ret;
}

$gks_card_expand_array=null;
function gks_card_body($item,$force_open=false,$def_close=false) {
  global $gks_card_expand_array;
  if ($gks_card_expand_array==null) $gks_card_expand_array=gks_card_expand();

  $ret=' data-item="'.$item.'"';
  if (isset($gks_card_expand_array[$item])) {
    if ($gks_card_expand_array[$item]==0 and $force_open==false) {
      $ret.= ' style="display:none"';
    }
  } else if ($item=='kat' and $force_open==false) { //katagrafi, def = close
    $ret.= ' style="display:none"';
  } else {
    if ($def_close) $ret.= ' style="display:none"';
  }
  return $ret;
}

function gks_filters_expand() {
  global $db_link;
  global $my_wp_user_id;
  if ($my_wp_user_id<=0) return array();
  if (isset($_SERVER['SCRIPT_FILENAME'])==false) return array();
  $url=trim_gks(basename(trim_gks($_SERVER['SCRIPT_FILENAME'])));
  //echo '<pre>'.$url;die();
  if ($url=='') return array();
  
  $sql="select state from gks_users_filters_expand where user_id=".$my_wp_user_id." and url='".$db_link->escape_string($url)."'";
  //echo '<pre>'.$sql;die();
  $result = $db_link->query($sql);
  if (!$result) return array();
  $ret=array();
  while ($row = $result->fetch_assoc()) {
    $ret['page']=intval($row['state']);
  }
  //print '<pre>'.$sql;print_r($ret);die();
  return $ret;
}

$gks_filters_expand_array=null;
function gks_filters_body($force_open=false,$def_close=false) {
  global $gks_filters_expand_array;
  if ($gks_filters_expand_array==null) $gks_filters_expand_array=gks_filters_expand();
  if (isset($gks_filters_expand_array['page'])==false) return false;
  
  //echo '<pre>';print_r($gks_filters_expand_array);die();
  return intval($gks_filters_expand_array['page']);
}


function gks_balance_calc($params) {
  global $db_link;
  global $my_wp_user_id;
  
  $id=0; if (isset($params['id'])) $id=intval($params['id']);
  $except_id_order=0;   if (isset($params['except_id_order']))   $except_id_order  =intval($params['except_id_order']);
  $except_id_acc_inv=0; if (isset($params['except_id_acc_inv'])) $except_id_acc_inv=intval($params['except_id_acc_inv']);
  $except_id_acc_pay=0; if (isset($params['except_id_acc_pay'])) $except_id_acc_pay=intval($params['except_id_acc_pay']);
  $except_id_hotel_reservation=0; if (isset($params['except_id_hotel_reservation'])) $except_id_hotel_reservation=intval($params['except_id_hotel_reservation']);
  $except_id_transfer_reservation=0; if (isset($params['except_id_transfer_reservation'])) $except_id_transfer_reservation=intval($params['except_id_transfer_reservation']);
  $until_date='';if (isset($params['until_date'])) $until_date=trim_gks($params['until_date']);
  
  $balance_final=0;
  $balance_orders=0;
  $balance_acc_inv=0;
  $balance_acc_pay=0;
  $balance_hotel_reservation=0;
  $balance_transfer_reservation=0;
  
  //gks_orders
  $sql="SELECT Sum(affect_balance_pros * affect_balance_poso) AS balance_orders
  FROM gks_orders
  WHERE affect_balance=1 and user_id>0 and user_id=".$id."
  AND order_state In ('060registered','070inproduction','090indelivery','095execute','100completed','110payment')";
  if ($except_id_order!=0) $sql.=" and id_order<>".$except_id_order;
  if ($until_date!='')     $sql.=" and order_date<='".$until_date."'";
  //echo '<pre>'.$id.'|'.$sql;die();
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  
  if ($result->num_rows>=0) {
    $row=$result->fetch_assoc();
    if (!empty($row['balance_orders'])) $balance_orders=$row['balance_orders'];
  }
  
  //gks_acc_inv
  $sql="SELECT Sum(affect_balance_pros * affect_balance_poso) AS balance_acc_inv
  FROM gks_acc_inv
  WHERE affect_balance=1 and user_id>0 and user_id=".$id."
  AND inv_state In ('080listing','090ekdosi','100payment')";
  if ($except_id_acc_inv!=0) $sql.=" and id_acc_inv<>".$except_id_acc_inv;
  if ($until_date!='')       $sql.=" and inv_date<='".$until_date."'";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  
  if ($result->num_rows>=0) {
    $row=$result->fetch_assoc();
    if (!empty($row['balance_acc_inv'])) $balance_acc_inv=$row['balance_acc_inv'];
  }  
  
  //gks_acc_pay
  $sql="SELECT Sum(affect_balance_pros * affect_balance_poso) AS balance_acc_pay
  FROM gks_acc_pay
  WHERE affect_balance=1 and user_id>0 and user_id=".$id."
  AND pay_state In ('080listing','090ekdosi','100payment')";
  if ($except_id_acc_pay!=0) $sql.=" and id_acc_pay<>".$except_id_acc_pay;
  if ($until_date!='')       $sql.=" and pay_date<='".$until_date."'";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  
  if ($result->num_rows>=0) {
    $row=$result->fetch_assoc();
    if (!empty($row['balance_acc_pay'])) $balance_acc_pay=$row['balance_acc_pay'];
  }  
  
  
  //gks_hotel_reservation
  $sql="SELECT Sum(affect_balance_pros * affect_balance_poso) AS balance_acc_pay
  FROM gks_hotel_reservation
  WHERE affect_balance=1 and user_id>0 and user_id=".$id."
  AND reservation_status In ('070wait_payment','080confirm','100completed','110payment')";
  if ($except_id_hotel_reservation!=0) $sql.=" and id_hotel_reservation<>".$except_id_hotel_reservation;
  if ($until_date!='')       $sql.=" and reservation_date<='".$until_date."'";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  
  if ($result->num_rows>=0) {
    $row=$result->fetch_assoc();
    if (!empty($row['balance_acc_pay'])) $balance_hotel_reservation=$row['balance_acc_pay'];
  }  
  
  
  //gks_transfer_reservation
  $sql="SELECT Sum(affect_balance_pros * affect_balance_poso) AS balance_acc_pay
  FROM gks_transfer_reservation
  WHERE affect_balance=1 and user_id>0 and user_id=".$id."
  AND transfer_reservation_status In ('070wait_payment','080confirm','100completed','110payment')";
  if ($except_id_transfer_reservation!=0) $sql.=" and id_transfer_reservation<>".$except_id_transfer_reservation;
  if ($until_date!='')       $sql.=" and transfer_reservation_date<='".$until_date."'";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  
  if ($result->num_rows>=0) {
    $row=$result->fetch_assoc();
    if (!empty($row['balance_acc_pay'])) $balance_transfer_reservation=$row['balance_acc_pay'];
  }  
  
  
  
  
  //final
  $balance_final = $balance_orders + $balance_acc_inv + $balance_acc_pay + $balance_hotel_reservation + $balance_transfer_reservation;
  
  
  if ($id>0 and $except_id_order==0 and $except_id_acc_inv==0 and $except_id_acc_pay==0 and $except_id_hotel_reservation==0 and $except_id_transfer_reservation==0 and $until_date=='') {
    $sql="update ".GKS_WP_TABLE_PREFIX."users set gks_balance=".number_format($balance_final, 10, '.', '')." where ID=".$id;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
  }
  
  //echo '<pre>'; echo time().' '.$balance_final;die();
  
  return floatval($balance_final);
  
}



function gks_product_base_type_descr($typeid,$longtext=false,$load_lang='') {
  global $gks_user_settings;
  if ($load_lang=='') {
    $load_lang='el-GR';
    if (isset($gks_user_settings['lang']['backend'])) $load_lang = gks_erp_supperted_lang($gks_user_settings['lang']['backend']);
  }
  
  if ($load_lang=='el-GR') {  
    if ($longtext) {
      switch ($typeid) {
        case 0: return gks_lang('Εμπόρευμα','part4','gks_product_base_type_descr'); break; 
        case 1: return gks_lang('Προϊόν','part4','gks_product_base_type_descr'); break; 
        case 2: return gks_lang('Υπηρεσία','part4','gks_product_base_type_descr'); break; 
      }   
    } else {
      switch ($typeid) {
        case 0: return gks_lang('Εμ','part4','gks_product_base_type_descr'); break; 
        case 1: return gks_lang('Πρ','part4','gks_product_base_type_descr'); break; 
        case 2: return gks_lang('Υπ','part4','gks_product_base_type_descr'); break; 
      }   
    }
    return $typeid;
  } else {
    if ($longtext) {
      switch ($typeid) {
        case 0: return 'Commodity'; break; 
        case 1: return 'Product'; break; 
        case 2: return 'Service'; break; 
      }   
    } else {
      switch ($typeid) {
        case 0: return 'Co'; break; 
        case 1: return 'Pr'; break; 
        case 2: return 'Se'; break; 
      }   
    }
    return $typeid;
    
    
  }
}


function guid_for_urlshort_hit() {
  global $db_link;
  while (true) {
    mt_srand(intval((double)microtime()*10000));//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = ''; //chr(45);// "-"
    $guid = substr($charid, 0, 8)
        .substr($charid, 8, 4)
        .substr($charid,12, 4)
        .substr($charid,16, 4)
        .substr($charid,20,12);
    $guid = strtolower($guid);
    $sql = "SELECT urlshort_hit_guid from gks_urlshort_hit where urlshort_hit_guid='".$db_link->escape_string($guid)."'";
    $result = $db_link->query($sql);
    
    if ($result->num_rows == 0) {
      return $guid; 
    }
  }
}


function gks_csv_txt($a) {
  $a=trim_gks($a);
  $a=str_replace('"', '_', $a);
  $a=str_replace('<br>', ' ', $a);
  $a=str_replace('<br/>', ' ', $a);
  $a=str_replace('<br />', ' ', $a);
  $a=str_replace("\r", ' ', $a);
  $a=str_replace("\n", ' ', $a);
  $a=str_replace('  ', ' ', $a);
  $a=str_replace('  ', ' ', $a);
  $a=str_replace('  ', ' ', $a);
  
  $a=nl2br_gks($a);
  if ($a=='') $a='--';
  //$a.='11';
  return $a;
}



function gks_notification_userperm_internal_users() {
  global $GKS_USERS_ACCESS_ROLES;
  global $db_link;
  
  
  $out_sql=" and gks_notification_userperm.user_id in (select ID from ".GKS_WP_TABLE_PREFIX."users where 
  gks_wp_capabilities like '%administrator%' or 
  gks_wp_capabilities like '%adminmy%' or ";
  foreach ($GKS_USERS_ACCESS_ROLES as $value) {
    $out_sql.="gks_wp_capabilities like '%".$db_link->escape_string($value)."%' or ";
  } 
  
  $out_sql=substr($out_sql, 0, strlen($out_sql)-4);
  
  
  $out_sql.=") ";
  
  return $out_sql;
}


