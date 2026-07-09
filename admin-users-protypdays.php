<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();
die('to do');


$mydaydif=0;
if (isset($_GET['day'])) $mydaydif=intval($_GET['day']);


$isergastirio=0;
if (isset($_GET['ergastirio'])) $isergastirio=intval($_GET['ergastirio']);

$sergastirio=0;
$sergastirio_array=array();
if (isset($_gks_session['gks']['sergastirio']) and !isset($_GET['sergastirio'])) {
  $tempa=explode(',',$_gks_session['gks']['sergastirio']);
  foreach ($tempa as $value) {
    $sergastirio_array[] = intval($value);
  }
  $sergastirio = implode(',', $sergastirio_array);
}
if (isset($_GET['sergastirio'])) {
  $tempa=explode(',',$_GET['sergastirio']);
  foreach ($tempa as $value) {
    $sergastirio_array[] = intval($value);
  }
  $sergastirio = implode(',', $sergastirio_array);
}
$_gks_session['gks']['sergastirio'] = $sergastirio;
gks_erp_cookie_save();

$smagazi=0;
$smagazi_array=array();
if (isset($_GET['smagazi'])) {
  $tempa=explode(',',$_GET['smagazi']);
  foreach ($tempa as $value) {
    $smagazi_array[] = intval($value);
  }
  $smagazi = implode(',', $smagazi_array);
}
if (count($smagazi_array) == 1 and $smagazi_array[0]==0) {
  $smagazi=0;
  $smagazi_array=array();  
}
$sortorder=0;
if (isset($_GET['sortorder'])) $sortorder=intval($_GET['sortorder']);

$ffilter1=0;
if (isset($_GET['ffilter1'])) $ffilter1=intval($_GET['ffilter1']);






$my_page_title=gks_lang('Αναφορά Πρότυπων Ημερών Ασφάλισης');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'wp_users','edit',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}




$filter_ergastiria='';
if (ur_ad()==false) {
  $filter_ergastiria='-1,';
  $sql="SELECT gks_ergastiria_ipethinosperioxis.ergastirio_id
  FROM gks_ergastiria_ipethinosperioxis 
  LEFT JOIN gks_ergastiria ON gks_ergastiria_ipethinosperioxis.ergastirio_id = gks_ergastiria.id_ergastirio
  WHERE gks_ergastiria_ipethinosperioxis.user_id=".$my_wp_user_id." AND gks_ergastiria.ergastirio_disable=0";
  $result_filter_ergastiria = $db_link->query($sql);    
  if (!$result_filter_ergastiria) {
    debug_mail(false,'sql error',$sql);
    die('sql error');
  }
  while ($row_filter_ergastiria = $result_filter_ergastiria->fetch_assoc()) {
    $filter_ergastiria.=$row_filter_ergastiria['ergastirio_id'].',';
  }
  $filter_ergastiria=substr($filter_ergastiria, 0, strlen($filter_ergastiria)-1);
}
if (ur_lo()) $filter_ergastiria='';

$mytimenow=time() + $mydaydif*24*60*60; // + 0*24*60*60;

$time_vardia=_time_user($mytimenow, 1);
$time_vardia-= GKS_ERP_START_VARDIA*60*60;
$today_vardia = date('Y-m-d',$time_vardia);

//echo  date('Y-m-d H:i:s',$time_vardia);
//echo '<br>';
//echo $today_vardia;
//echo '<br>';
//die();

$today_vardia = strtotime($today_vardia) + GKS_ERP_START_VARDIA*60*60;
$today_vardia = _time_user($today_vardia, -1);
$today_vardia_time = $today_vardia;
$today_vardia = date('Y-m-d H:i:s', $today_vardia);

$today_day = date('w', $today_vardia_time); //0 (for Sunday) through 6 (for Saturday)

$diff_startvardia = 12-GKS_ERP_START_VARDIA+5;










$ergast_in='';
$ergast = array();
$sql="SELECT gks_ergastiria.*
FROM gks_ergastiria
WHERE gks_ergastiria.ergastirio_disable=0";
if ($isergastirio>0) {
  $sql.=" and id_ergastirio=".$isergastirio;
}
if ($sergastirio>0) {
  $sql.=" and (id_ergastirio in (".$sergastirio.") or ergastirio_parent_id in (".$sergastirio."))";
}
if ($my_wp_user_id != 1) {
  $sql.=" and id_ergastirio <>3";
}
if ($filter_ergastiria!='') $sql.=" and id_ergastirio in (".$filter_ergastiria.")";
$sql.=" ORDER BY gks_ergastiria.ergastirio_title";
$result_ergast = $db_link->query($sql);        
if (!$result_ergast) {
  debug_mail(false,'sql error',$sql);
  die('sql error');
}
$isergastirio_descr='';
while ($row_ergast = $result_ergast->fetch_assoc()) {
  $isergastirio_descr=$row_ergast['ergastirio_title'];
  
  
  $row_ergast['programma'] = array();
  $row_ergast['users_ergast'] = array();
  $ergast[$row_ergast['id_ergastirio']] = $row_ergast;
  $ergast_in.=$row_ergast['id_ergastirio'].',';
}

//header('Content-Type: text/html; charset=utf-8');
//print '<pre>';
//print count($ergast);
//print_r($ergast);
//print $ergast_in;
//die();  

$thisdatehuman = getWeekDayName($today_day).' '. showDate($today_vardia_time, 'd/m/Y', 1);
$thisdate=$today_vardia_time ;


$mytoday_year = showDate($today_vardia_time, 'Y', 1);
$mytoday_month = showDate($today_vardia_time, 'm', 1);
$mytoday_day = showDate($today_vardia_time, 'd', 1);
$mytoday_weekday = showDate($today_vardia_time, 'w', 1);
//0 (for Sunday) through 6 (for Saturday)

$myweekscount=0;
for ($dd = 1 ; $dd <= $mytoday_day; $dd++) {
  $tweekday=date('w', strtotime($mytoday_year.'-'.$mytoday_month.'-'.$dd));
  if ($tweekday == $mytoday_weekday) {
    $myweekscount++;
  }
}
$nextmonth = strtotime('+1 month', strtotime($mytoday_year.'-'.$mytoday_month.'-01'));
$lastdayofmonth = date('Y-n-d H:i',$nextmonth - 24*60*60);

$allweekscount=0;
for ($dd = 1 ; $dd <= $lastdayofmonth; $dd++) {
  $tweekday=date('w', strtotime($mytoday_year.'-'.$mytoday_month.'-'.$dd));
  if ($tweekday == $mytoday_weekday) {
    $allweekscount++;
  }
}

//echo $mytoday_day.'--'.$mytoday_month.'--'.$mytoday_year.'---'.$mytoday_weekday.'---'.$myweekscount;
//echo '---'.$nextmonth;
//echo '---'.date('Y-n-d H:i',$nextmonth - 24*60*60);
//echo '---'.$lastdayofmonth;
//echo '---'.$allweekscount;


$ord_day= $mytoday_weekday+1;
$ord_mwday=$myweekscount;

$ord_mwday_int1=$ord_mwday;
$ord_mwday_int2=-1;
if ($myweekscount==4 and $allweekscount == 4) {
  $ord_mwday.=',5';
  $ord_mwday_int2=5;
}


if (strlen($ergast_in)>0) $ergast_in=substr($ergast_in, 0, strlen($ergast_in)-1);

$sql="SELECT usersword.user_id, usersasfalia.user_asfalia, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, ".GKS_WP_TABLE_PREFIX."users.gks_fullname, gks_users.afm
FROM (((select user_id from (
  SELECT gks_programma_fotografos.fotografos_id as user_id
  FROM gks_programma_fotografos LEFT JOIN gks_programma ON gks_programma_fotografos.programma_id = gks_programma.id_programma
  WHERE gks_programma_fotografos.fmystate<>'cancel' 
  AND gks_programma.date_start >= '".date('Y-m-d H:i:s', $today_vardia_time + (0 * 24*60*60))."'
  AND gks_programma.date_start < '".date('Y-m-d H:i:s', $today_vardia_time + ((1) * 24*60*60))."'
  AND gks_programma.ergastirio_id>0
  ".(strlen($ergast_in) > 0 ? " AND gks_programma.ergastirio_id In (".$ergast_in.") " : "");
  if (count($smagazi_array) > 0) {
    $sql.=" and gks_programma.magazi_id in (".implode(',',$smagazi_array).")";    
  }
  
$sql.=" GROUP BY gks_programma_fotografos.fotografos_id
  union
  SELECT gks_programma_ergastirio.user_id
  FROM gks_programma_ergastirio 
  LEFT JOIN gks_programma ON gks_programma_ergastirio.programma_id = gks_programma.id_programma
  WHERE gks_programma_ergastirio.date_start >= '".date('Y-m-d H:i:s', $today_vardia_time + (0 * 24*60*60))."'
  AND gks_programma_ergastirio.date_start < '".date('Y-m-d H:i:s', $today_vardia_time + ((1) * 24*60*60))."'
  AND gks_programma_ergastirio.emystate<>'cancel'
  ".(strlen($ergast_in) > 0 ? " AND gks_programma_ergastirio.ergastirio_id In (".$ergast_in.") " : "");

  if (count($smagazi_array) > 0) {
    if (in_array(-1,$smagazi_array)) {
      if (count($smagazi_array)>1) {
        $sql.=" and (gks_programma.magazi_id is null or gks_programma.magazi_id in (".implode(',',$smagazi_array)."))";
      } 
    } else {
      $sql.=" and gks_programma.magazi_id in (".implode(',',$smagazi_array).")";
    }
  }
  
$sql.=" GROUP BY gks_programma_ergastirio.user_id
) as usersg
group by user_id
)  AS usersword LEFT JOIN (
  SELECT gks_users_protypdays.user_id as user_asfalia
  FROM gks_users_protypdays
  WHERE gks_users_protypdays.ord_day=".$ord_day." 
  AND gks_users_protypdays.ord_mwday in (".$ord_mwday.")
  GROUP BY gks_users_protypdays.user_id
)  AS usersasfalia ON usersword.user_id = usersasfalia.user_asfalia) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON usersword.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
LEFT JOIN gks_users on gks_users.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
";

if ($ffilter1==1) {
  $sql.=" where usersasfalia.user_asfalia is not null";
} else if ($ffilter1==2) {
  $sql.=" where usersasfalia.user_asfalia is null";
}

if ($sortorder == 0) {
  $sql.=" order by gks_nickname";
} else if ($sortorder == 1) { 
  $sql.=" order by gks_fullname";
} else { 
  $sql.=" order by user_asfalia,gks_nickname";
}


//echo $sql;
//die();

$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');

$row_array=array();
$users_in='';
$sum_user_asfalia=0;
$sum_user_asfalia_all=0;

while ($row = $result->fetch_assoc()) {
  $row_array[] = $row;
  $users_in.=$row['user_id'].',';
  
  $sum_user_asfalia_all++;
  if (isset($row['user_asfalia'])) $sum_user_asfalia++;

  
}
if (strlen($users_in)>0) $users_in=substr($users_in, 0, strlen($users_in)-1);

$protypdays=array();
$company_array=array();
if (strlen($users_in)>0) {
  $sql="SELECT gks_users_protypdays.*, gks_company.id_company, gks_company.company_title, gks_company.company_color
  FROM gks_users_protypdays LEFT JOIN gks_company ON gks_users_protypdays.company_id = gks_company.id_company
  WHERE user_id In (".$users_in.")
  ORDER BY user_id, ord_mwday, IF(ord_day=1,8,ord_day), ord_day, company_title";
  
  $result_protypdays = $db_link->query($sql);        
  if (!$result_protypdays) debug_mail(false,'error sql',$sql);
  if (!$result_protypdays) die('sql error');
  
  while ($row_protypdays = $result_protypdays->fetch_assoc()) {
    if (isset($protypdays[$row_protypdays['user_id']]) == false) {
      $protypdays[$row_protypdays['user_id']] = '';  
    }
    $isbold = ($ord_day == $row_protypdays['ord_day'] and ($ord_mwday_int1 == $row_protypdays['ord_mwday'] or $ord_mwday_int2 == $row_protypdays['ord_mwday']));
    //$isbold = ($ord_day == $row_protypdays['ord_day'] and ($ord_mwday_int1 == $row_protypdays['ord_mwday']));
    //$protypdays[$row_protypdays['user_id']].= $ord_day.'--'.$row_protypdays['ord_day'].'--'.$ord_mwday_int1.'--'.$row_protypdays['ord_mwday'].'<br>';
    
    $protypdays[$row_protypdays['user_id']].= 
    '<span class="spanprotypdays" style="background-color:'.$row_protypdays['company_color'].'" title="'.$row_protypdays['company_title'].'">'.
    ($isbold ? '<b>[': '').
    gks_n_h($row_protypdays['ord_mwday']).
    mb_substr(getWeekDayName($row_protypdays['ord_day'] - 1),0,2). 
    ($isbold ? ']</b>': '').
    '</span> ';
  }
  
  $sql="SELECT gks_company_users.user_id, gks_company_users.company_id, gks_company.company_title, gks_company.company_color
  FROM gks_company_users LEFT JOIN gks_company ON gks_company_users.company_id = gks_company.id_company
  WHERE user_id In (".$users_in.")
  ORDER BY gks_company.company_sortorder, gks_company.company_title";
  
  $result_company = $db_link->query($sql);        
  if (!$result_company) debug_mail(false,'error sql',$sql);
  if (!$result_company) die('sql error');
  
  while ($row_company = $result_company->fetch_assoc()) {
    if (isset($company_array[$row_company['user_id']]) == false) {
      $company_array[$row_company['user_id']] = '';  
    }
    $company_array[$row_company['user_id']].= '<span class="spancompanyprotypdays" style="background-color:'.$row_company['company_color'].'">'.$row_company['company_title'].'</span><br>';
  }
}
//foreach ($protypdays as &$value) {
//  $value=substr($value, 0, strlen($value)-2);
//} 
foreach ($company_array as &$value) {
  $value=substr($value, 0, strlen($value)-4);
} 



//print '<pre>';
//print_r($protypdays);



//WHERE gks_programma.date_start >= '".date('Y-m-d H:i:s', $today_vardia_time + (0 * 24*60*60))."'
//AND gks_programma.date_start< '".date('Y-m-d H:i:s', $today_vardia_time + ((1) * 24*60*60))."'





include_once('_my_header_admin.php');


?>

<br>
<table align="center" width="96%" border="0" cellspacing=0 cellpadding=0>
  <tr>
    <td align="center">
    <div class="headerdivpart" style="width:33%;float:left;border:0px solid green;text-align:left;">
        <button style=font-size:12pt;font-weight:bold;" type="button" class="submit_button" id="dayminus" onclick="window.location.href='?day=<?php
          echo ($mydaydif-1);?>&sergastirio=<?php 
          echo $sergastirio;?>&sortorder=<?php echo $sortorder;?>&ffilter1=<?php echo $ffilter1;?>'">&lt; <?php 
          echo getWeekDayName(date('w', $today_vardia_time+ (-1 * 24*60*60))).' '. showDate($today_vardia_time + (-1 * 24*60*60), 'd/m/Y', 1) ?></button>      
    </div>
    <div class="headerdivpart" style="width:33%;float:left;border:0px solid red;text-align:center;font-size:12pt;font-weight:bold">
      <?php echo $my_page_title;?>
      <input type="text" style="line-height: 1;width:100px;" id="mydatejump" name="mydatejump" value="<?php echo showDate($today_vardia_time, 'd/m/Y', 1); ?>"> 
      
    </div>
    <div class="headerdivpart" style="width:33%;float:left;border:0px solid green;text-align:right">
      <button style=font-size:12pt;font-weight:bold;" type="button" class="submit_button" id="dayplus" onclick="window.location.href='?day=<?php 
        echo ($mydaydif+1);?>&sergastirio=<?php 
        echo $sergastirio;?>&sortorder=<?php echo $sortorder;?>&ffilter1=<?php echo $ffilter1;?>'"><?php 
        echo getWeekDayName(date('w', $today_vardia_time+ (1 * 24*60*60))).' '. showDate($today_vardia_time + (1 * 24*60*60), 'd/m/Y', 1) ?> &gt;</button>      
    </div>
    </td>
  </tr>
</table>  
  


<form method="get" action="?" id="sform">
<input type="hidden" name="day" id="sform_day" value="<? echo $mydaydif;?>"/>
<input type="hidden" name="sergastirio" id="sergastirio" value="<? echo $sergastirio;?>"/>
<input type="hidden" name="smagazi" id="smagazi" value="<? echo $smagazi;?>"/>


<table align="center" width="96%">
  <tr>
    <td width="100%" align="center">

    <select name="sortorder" id="sortorder">
      <option value="0" <?php if ($sortorder == 0) echo ' selected ';?>><?php echo gks_lang('Ταξινόμηση κατά Υποκοριστικό');?></option>
      <option value="1" <?php if ($sortorder == 1) echo ' selected ';?>><?php echo gks_lang('Ταξινόμηση κατά Ονοματεπώνυμο');?></option>
      <option value="2" <?php if ($sortorder == 2) echo ' selected ';?>><?php echo gks_lang('Ταξινόμηση κατά Ασφάλεια');?></option>
    </select>
    <select name="ffilter1" id="ffilter1">
       <option value="0"><?php echo gks_lang('Ασφάλεια Όλα');?></option>  
       <option value="1" <?php if ($ffilter1 == 1) echo ' selected ';?>><?php echo gks_lang('Ασφάλεια Ναι');?></option>  
       <option value="2" <?php if ($ffilter1 == 2) echo ' selected ';?>><?php echo gks_lang('Ασφάλεια Όχι');?></option>  
    </select>   
    <select id="sergastirio_ms" multiple="multiple" style="width:300px">
      
      <?php 
      $sql="SELECT id_ergastirio, ergastirio_title FROM gks_ergastiria WHERE ergastirio_disable=0 and ergastirio_show_programma<>0 ";
      if ($filter_ergastiria!='') $sql.=" and id_ergastirio in (".$filter_ergastiria.")";
      $sql.=" ORDER BY ergastirio_title";
      $result_se = $db_link->query($sql);        
      if (!$result_se) {
        debug_mail(false,'sql error',$sql);
        die('sql error');
      }
      while ($row_se = $result_se->fetch_assoc()) {
        echo '<option value="'.$row_se['id_ergastirio'].'"';
        if ($sergastirio == '0' or in_array($row_se['id_ergastirio'], $sergastirio_array)) echo ' selected ';
        echo '>'.$row_se['ergastirio_title'].'</option>';
      }      
      ?>
    </select>
    
    <select id="smagazi_ms" multiple="multiple" style="width:300px">
        <option value="-1" 
          <?php if ($smagazi == '0' or in_array(-1, $smagazi_array)) echo ' selected ';?>
          >--<?php echo gks_lang('Υποστήριξη');?>--</option>  
      <?php 
      $sql="SELECT gks_programma.magazi_id, gks_magazia.magazi_title
      FROM (gks_programma_fotografos 
      LEFT JOIN gks_programma ON gks_programma_fotografos.programma_id = gks_programma.id_programma) 
      LEFT JOIN gks_magazia ON gks_programma.magazi_id = gks_magazia.id_magazi
			WHERE gks_programma_fotografos.fmystate<>'cancel' 
		  AND gks_programma.date_start >= '".date('Y-m-d H:i:s', $today_vardia_time + (0 * 24*60*60))."'
		  AND gks_programma.date_start < '".date('Y-m-d H:i:s', $today_vardia_time + ((1) * 24*60*60))."'";
      if ($filter_ergastiria!='') $sql.=" and gks_programma.ergastirio_id in (".$filter_ergastiria.")";
		  

		  
			$sql.=" GROUP BY gks_programma.magazi_id
			order by magazi_title";
      $result_se = $db_link->query($sql);        
      if (!$result_se) {
        debug_mail(false,'sql error',$sql);
        die('sql error');
      }
      while ($row_se = $result_se->fetch_assoc()) {
        echo '<option value="'.$row_se['magazi_id'].'"';
        if ($smagazi == '0' or in_array($row_se['magazi_id'], $smagazi_array)) echo ' selected ';
        echo '>'.$row_se['magazi_title'].'</option>';
      }      
      ?>
    </select>    
    
    
			
			
    <?php 

    if ($mydaydif !=0) { ?>
      <a href="?day=0&sergastirio=<?php echo $sergastirio;?>&sortorder=<?php echo $sortorder;?>&ffilter1=<?php echo $ffilter1;?>"><?php echo gks_lang('Σήμερα');?></a>
    <?php } else { 
      echo gks_lang('Σήμερα');
    } 

    ?>
    </td>
  </tr>
</table>
</form>



<?php

echo '<h1 align="center">'.$thisdatehuman.'</h1>';

echo '<p style="text-align:center">'.$sum_user_asfalia.'/'.$sum_user_asfalia_all.'</p>';
//print_r($has_asfalia);
?>

<table class="generic-table" border="0" width="96%" cellspacing="0" cellpadding="5"  align=center>
  
    <tr>	
        <th style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?"><?php echo gks_lang('A/A');?></a></th>
        <th style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo gks_lang('Υποκοριστικό');?></th>        
        <th style="text-align: left   !important;" width="0%"  nowrap="nowrap"><?php echo gks_lang('Ονοματεπώνυμο');?></th>        
        <th style="text-align: center !important;" width="0%"  nowrap="nowrap"><?php echo gks_lang('ΑΦΜ');?></th>        
        <th style="text-align: center !important;" width="0%"  nowrap="nowrap"><?php echo gks_lang('Ασφάλεια');?></th>        
        <th style="text-align: left   !important;" width="50%"  nowrap="nowrap"><?php echo gks_lang('Πρότυπες Ημέρες Ασφάλισης');?></th>        
        <th style="text-align: left   !important;" width="20%"  nowrap="nowrap"><?php echo gks_lang('Εταιρεία');?></th>        
        <th style="text-align: left   !important;" width="15%"  nowrap="nowrap"><?php echo gks_lang('Αποθήκη');?></th>        
        <th style="text-align: left   !important;" width="15%"  nowrap="nowrap"><?php echo gks_lang('Μαγαζί');?></th>        
       
               
    </tr>
    
<?php
$i = 0;
foreach ($row_array as $row) {

  $i++;
  //usersword.user_id, usersasfalia.user_asfalia, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, ".GKS_WP_TABLE_PREFIX."users.gks_fullname
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <td nowrap align="center"><?php echo $i;?></td>      
    <td nowrap ><a href="admin-users-item.php?id=<?php echo $row['user_id'];?>"><?php echo $row['gks_nickname'];?></a></td>
    <td nowrap ><?php echo $row['gks_fullname'];?></td>
    <td nowrap align="center" ><?php echo $row['afm'];?></td>
    <td nowrap align="center"><img src="img/<?php echo (isset($row['user_asfalia']) ? "1" :"0");  ?>.png" border="0" width="16"></td>
    <td        ><?php
      if (isset($protypdays[$row['user_id']])) {
        echo $protypdays[$row['user_id']];
      } 
    ?></td>
    <td        ><?php
      if (isset($company_array[$row['user_id']])) {
        echo $company_array[$row['user_id']];
      } 
    ?></td>
    <td><?php
    	$sql_ergas="SELECT gks_programma.ergastirio_id, gks_ergastiria.ergastirio_title, gks_magazia.magazi_title
      FROM ((gks_programma_fotografos 
      LEFT JOIN gks_programma ON gks_programma_fotografos.programma_id = gks_programma.id_programma) 
      LEFT JOIN gks_ergastiria ON gks_programma.ergastirio_id = gks_ergastiria.id_ergastirio) 
      LEFT JOIN gks_magazia ON gks_programma.magazi_id = gks_magazia.id_magazi
			WHERE gks_programma_fotografos.fotografos_id=".$row['user_id']."
			AND gks_programma_fotografos.fmystate<>'cancel' 
		  AND gks_programma.date_start >= '".date('Y-m-d H:i:s', $today_vardia_time + (0 * 24*60*60))."'
		  AND gks_programma.date_start < '".date('Y-m-d H:i:s', $today_vardia_time + ((1) * 24*60*60))."'
			GROUP BY gks_programma.ergastirio_id, gks_programma.magazi_id
			union
			SELECT gks_programma_ergastirio.ergastirio_id, gks_ergastiria.ergastirio_title, gks_magazia.magazi_title
      FROM ((gks_programma_ergastirio 
      LEFT JOIN gks_ergastiria ON gks_programma_ergastirio.ergastirio_id = gks_ergastiria.id_ergastirio) 
      LEFT JOIN gks_programma ON gks_programma_ergastirio.programma_id = gks_programma.id_programma) 
      LEFT JOIN gks_magazia ON gks_programma.magazi_id = gks_magazia.id_magazi
			WHERE  gks_programma_ergastirio.user_id=".$row['user_id']."
			and gks_programma_ergastirio.date_start >= '".date('Y-m-d H:i:s', $today_vardia_time + (0 * 24*60*60))."'
		  AND gks_programma_ergastirio.date_start < '".date('Y-m-d H:i:s', $today_vardia_time + ((1) * 24*60*60))."'
		  AND gks_programma_ergastirio.emystate<>'cancel'
			GROUP BY ergastirio_id, ergastirio_title
			order by ergastirio_title,magazi_title";
			//echo $sql_ergas;
			$result_ergas = $db_link->query($sql_ergas);        
			if (!$result_ergas) debug_mail(false,'error sql',$sql_ergas);
			if (!$result_ergas) die('sql error');	
			$myout='';		
			$myout_magazi='';		
			while ($row_ergas = $result_ergas->fetch_assoc()) {
			  $myout.= $row_ergas['ergastirio_title'].'<br>';
			  if ($row_ergas['magazi_title'].''!='') {
			    $myout_magazi.= $row_ergas['magazi_title'].'<br>';
        }
			}			
			if ($myout!='') {
				$myout=substr($myout, 0, strlen($myout)-4);
				echo $myout;
			}
    	
    ?></td>
    <td><?php
    if ($myout_magazi!='') {
			$myout_magazi=substr($myout_magazi, 0, strlen($myout_magazi)-4);
			echo $myout_magazi;
		}  
    ?></td>
    
  </tr>
<?php } ?>
</table>

<div id="dialog_message" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <table style="width:100%">
    <tr>
      <td style="width:1%;vertical-align:top">
        <i id="dialog_message_ok"    class="myicons" style = "color: #00e220;font-size: 500%;">&#xf14a;</i>
        <i id="dialog_message_error" class="myicons" style = "color: #cb0000;font-size: 500%;">&#xf071;</i>
      </td>
      <td style="width:99%;vertical-align:top;padding: 20px 0px 0px 0px;">
        <span id="dialog_message_message" style="font-size: 120%;"></span>
      </td>
    </tr> 
  </table>
</div>







<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>;
  
var dialog_message;
var dialog_confirm;

jQuery(document).ready(function($) {
  
  $('#mydatejump').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y',timepicker:false,dayOfWeekStart:1,onChangeDateTime:function(ct,$i){
      var mynow = new Date(<?php echo date('Y',_time_user(time(),1)-GKS_ERP_START_VARDIA*60*60)?> ,<?php echo (date('m',_time_user(time(),1)-GKS_ERP_START_VARDIA*60*60) - 1)?> ,<?php echo date('d',_time_user(time(),1)-GKS_ERP_START_VARDIA*60*60)?>,0,0,0,0 );
      var mydiff = ct - mynow; //in milliseconds
      mydiff = mydiff/1000/86400;
      mydiff=Math.round(mydiff);
      $('#sform_day').val(mydiff);
      $('#sform').submit();
    }
  }));
  

  
   
  $('#sortorder').change(function() {
    $('#sform').submit();
  });
  $('#ffilter1').change(function() {
    $('#sform').submit();
  });       
  $('#sergastirio_ms').multiselect({
    height:'auto',
    selectedList: 4, // 0-based index
    position: {
      my: 'left top',
      at: 'left bottom'
    },
    close: function(){
      var myselval=$("#sergastirio_ms").val();
      if (myselval == null || myselval.length==0) {
        myalert('error:'+gks_lang('Κάντε τουλάχιστον μία επιλογή'));
        return;
      }
      if (typeof myselval == 'undefined') return;
      if ($("#sergastirio_ms")[0].options.length == myselval.length) {
        $("#sergastirio").val('0');
      } else {
        $("#sergastirio").val(myselval.join(','));
      }
      $('#sform').submit();
    },    
  });
  $('#smagazi_ms').multiselect({
    height:'auto',
    selectedList: 4, // 0-based index
    position: {
      my: 'left top',
      at: 'left bottom'
    },
    close: function(){
      var myselval=$("#smagazi_ms").val();
      if (myselval == null) {
        myalert('error:'+gks_lang('Κάντε τουλάχιστον μία επιλογή'));
        return;
      }
      if (typeof myselval == 'undefined') return;
      if ($("#smagazi_ms")[0].options.length == myselval.length) {
        $("#smagazi").val('0');
      } else {
        $("#smagazi").val(myselval.join(','));
      }
      $('#sform').submit();
    },    
  });  

    
  dialog_message = $( "#dialog_message" ).dialog({
    autoOpen: false,
    width: 400,
    height: 300,
    modal: true,
    buttons: {
      "OK" : function() {
        $(this).dialog( "close" );
      }
    }
  });
  
  function myalert(mymessage) {
    $('.ui-dialog-buttonpane button:contains("'+gks_lang('OK')+'")').button().show();
    $('.ui-dialog-buttonpane button:contains("'+gks_lang('Άκυρο')+'")').button().show();
          
    $("#dialog_message_ok").hide();
    $("#dialog_message_error").hide();
    if (mymessage.substring(0, 6) == 'error:') {
       $("#dialog_message_error").show();
       mymessage=mymessage.substring(6);
    }
    if (mymessage.substring(0, 3) == 'ok:') {
       $("#dialog_message_ok").show();
       mymessage=mymessage.substring(3);
    }
    
    $("#dialog_message_message").html(mymessage);
	  dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 450) dwidth=450;
	  if (dheight> 330) dheight=330;
	  dialog_message.dialog('option', 'width', dwidth);
	  dialog_message.dialog('option', 'height', dheight);
	  $('#dialog_message').parent().css({position:'fixed'});      
    dialog_message.dialog('open');
  };     
    
  dialog_confirm = $( "#dialog_confirm" ).dialog({
    autoOpen: false,
    width: 500,
    height: 500,
    modal: true,
    buttons: [
      {
        id: "dialog_confirm_ok",
        html: '<i class="fa fa-pen-square"></i> '+gks_lang('Ενημέρωση Σύστασης'),
        //icon: "ui-icon-circle-plus",
        click: function() {
          $(this).dialog('close');
          
          switch (dialog_confirm.function_ok) {
  //          case 'deleterow':
  //            mydeleterow(dialog_confirm.param1,dialog_confirm.param2,dialog_confirm.param3);
  //            break;
  
            default:
              myalert('error: dialog_confirm function_ok');
              break;
          }			
		    }	
      },
      {
        id: "dialog_confirm_cancel",
        html: '<i class="fa fa-window-close"></i> '+gks_lang('Άκυρο'),
        //icon: "ui-icon-cancel",
        click: function() {
  				if (dialog_confirm.param1 == 'gks_programma_fotografos' || dialog_confirm.param1 == 'gks_programma_ergastirio') {
  				  dialog_confirm.param3.draggable.animate({top:0,left:0},"slow");
  				}	
  				        
          $(this).dialog('close');
        }			
      },      
    ]    

  });
      
  function myconfirm(mymessage, function_ok,param1,param2,param3) {
    
    $('.ui-dialog-buttonpane button:contains("'+gks_lang('OK')+'")').button().show();
    $('.ui-dialog-buttonpane button:contains("'+gks_lang('Άκυρο')+'")').button().show();
          
    $("#dialog_confirm_message").html(mymessage);
    dialog_confirm.function_ok = function_ok;
    dialog_confirm.param1 = param1;
    dialog_confirm.param2 = param2;
    dialog_confirm.param3 = param3;
    
	  dwidth=$(window).width() * 0.96;
    dheight=$(window).height() * 0.96;
	  if (dwidth> 500) dwidth=500;
	  if (dheight> 500) dheight=500;
	  dialog_confirm.dialog('option', 'width', dwidth);
	  dialog_confirm.dialog('option', 'height', dheight);
	  $('#dialog_confirm').parent().css({position:'fixed'});      
    dialog_confirm.dialog('open');
  };     
    
 


 
  
  function myresize() {
    var mywinwidth=$(window).width() * 0.96;
    mywidth= Math.floor(mywinwidth/3);
    if (mywidth<250) {
      $('.headerdivpart').each(function( index ) {
        $(this).width(mywinwidth -3);
      });      
    } else {
      $('.headerdivpart').each(function( index ) {
        $(this).width(mywidth -3);
      });        
    }
  }
  myresize();
  $( window ).resize(function() {
    myresize();
  });  
  





  $(".spanprotypdays").tooltipster({theme: 'tooltipster-noir',contentAsHTML: true});

  

  




  
});

</script>

<?php  
include_once('_my_footer_admin.php');

