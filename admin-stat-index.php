<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Αρχική στατιστικών');
$nav_active_array=array('stat','stat_index');

db_open();
stat_record();
$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
}
if ($userrole=='') {header('Location: /my/admin-deny.php'); die(); }





$date_from=showDate(time()-1*86400, 'd/m/Y', 1);
$date_to  =showDate(time(),   'd/m/Y', 1);

if (isset($_GET['date_from']) and $_GET['date_from']!='' ) {
  $date_from=$_GET['date_from'];
}
if (isset($_GET['date_to']) and $_GET['date_to']!='' ) {
  $date_to=$_GET['date_to'];
}


$date_from_time=gks_myFormatDate($date_from);
$date_from=date('d/m/Y', $date_from_time);

$date_to_time=gks_myFormatDate($date_to);
$date_to=date('d/m/Y', $date_to_time);



$p1 = date('Y-m-d H:i:s', _time_user($date_from_time,-1));
$p2 = date('Y-m-d H:i:s', _time_user($date_to_time+86400,-1));
//echo $date_from_sql.' ';
//echo $date_to_sql;
//die();
 



include_once('_my_header_admin.php');
?>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
      <div><a class="btn btn-primary btn-sm" href="cron_ips.php?stat=1&redirect=<?php echo rawurlencode($_SERVER['SCRIPT_NAME']).rawurlencode('?'.$_SERVER['QUERY_STRING']) ?>"><?php echo gks_lang('Ανανέωση');?></a></div>
      
    </div>
  </div>
</div>



<form method="GET" action="" id=form2 name=form2>
  <p align="center">
  <?php echo gks_lang('Από');?>: <input  type="text" style="width:100px;display:inline-block;" id="date_from" name="date_from" value="<?php echo $date_from;?>" class="form-control form-control-sm"/>
  <?php echo gks_lang('Έως');?>: <input  type="text" style="width:100px;display:inline-block;" id="date_to"   name="date_to"   value="<?php echo $date_to;?>"   class="form-control form-control-sm"/>
  <input type="submit" value="<?php echo gks_lang('Εύρεση');?>" name="B1" class="btn btn-primary btn-sm" style="vertical-align: baseline;"> 
  
</form>

<hr style="margin:50px"/>
<?php gks_erp_app_purchase_ads_fix_970x90('page');?>





<?php
// Performing SQL query

$query = "SELECT userid,username, Count(gks_stat_stat.ip) AS rrr, max(timevisit) as lastmytime 
FROM gks_stat_stat 
left join gks_stat_ips on gks_stat_ips.ip = gks_stat_stat.ip
WHERE timevisit>='".$p1."' and timevisit<='".$p2."' ";
if (isset($_gks_session['gks']['stat']['admin_stats_bots_enable']) and $_gks_session['gks']['stat']['admin_stats_bots_enable']==false) {
	$query.=" and  (gks_stat_ips.isbot =0 or gks_stat_ips.isbot is null)";
}
$query.= " GROUP BY userid,username 
ORDER BY lastmytime desc,rrr DESC;";


$result = $db_link->query($query);        
if (!$result) debug_mail(false,'stat index error sql',$query);
$sum2=0;
$ddd=[];
while ($line = $result->fetch_assoc()) {  
  $sum2=$sum2+$line["rrr"];
  $ddd[]=$line;
}
?>

<div class="container-fluid">
  <div class="row align-items-center">
    <div class="col-sm-12">
      <div class="alert alert-primary" role="alert" style="width:98%;margin:auto">
        <?php
        print '<div align=center><b><big>'.gks_lang('Σελίδες').': '.myNumberFormat($sum2,0).'</big></b></div>';
        ?>
      </div>
    </div>
  </div>
</div>

<hr style="margin:50px"/>

<table class="table table-sm table-responsive1 table-striped table-bordered" border="0" style="width:96%;font-size:0.8rem;" cellspacing="0" cellpadding="5"  align=center>
<thead>
  <tr>
    <th class="table-dark" scope="col" width="0%" nowrap>#</th>
    <th class="table-dark" scope="col" width="0%" nowrap><?php echo gks_lang('Πότε');?></th>
    <th class="table-dark" scope="col" width="0%" nowrap></th>
    <th class="table-dark" scope="col" width="20%" nowrap><?php echo gks_lang('Χρήστης');?></th>
    <th class="table-dark" scope="col" width="0%" style="text-align: right;" ><?php echo gks_lang('Σελίδες');?></td>
    <th class="table-dark" scope="col" width="20%" nowrap><?php echo gks_lang('Τρέχουσα Σελίδα');?></th>
    <th class="table-dark" scope="col" width="30%" nowrap><?php echo gks_lang('Διεύθυνση');?></th>
    <th class="table-dark" scope="col" width="30%" nowrap><?php echo gks_lang('User Agent');?></th>
  </tr>
</thead>
<tbody>
<?php 
$sum1=0;
$sum2=0;
$i = 0;

foreach ($ddd as $line) {

	$i++;
	$sum1=$sum1+1;
	$sum2=$sum2+$line["rrr"];
?>
  <tr>
    <th scope="row" class="mytdcm"><?php echo $i;?></th>
    <td nowrap class="mytdcm"><?php print showDate(strtotime($line["lastmytime"]), 'd/m/Y H:i:s', 1);?>
    </td>
	  <td class="mytdcml">
	    <a href="admin-users-item.php?id=<?php echo $line["userid"]?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
	  </td>
	  <td class="mytdcml">
	    <a href="admin-stat-user.php?username=<?php echo urlencode($line["username"])?>"><?php echo $line["username"]?></a> &nbsp;</td>
    <td class="mytdcm"><?php print myNumberFormat($line["rrr"],0);?></td>
    <td class="mytdcml">
      <?php 
      if  (isset($line["userid"]) and $line["userid"]>0) {
        $query= "SELECT * FROM gks_stat_stat WHERE userid=".$line["userid"]." order by id desc limit 1";
        $resultuser = $db_link->query($query);        
        if (!$resultuser) debug_mail(false,'stat index error sql',$query);
        if ($resultuser->num_rows >= 1) {
          $lineuser = $resultuser->fetch_assoc();
          echo htmlspecialchars_gks($lineuser['pagetitle']);
        }     
      } ?>      
    </td>
    <td class="mytdcml">
      <?php if (isset($line["userid"]) and $line["userid"]>0 and $resultuser->num_rows >= 1) { ?>
      <a  href="//<?php 
    	$pp =$lineuser["host"].$lineuser["pageurl"];
    	if (isset($lineuser["query_string"]) && $lineuser["query_string"]!='') {
    	 $pp.='?'.$lineuser["query_string"]; 
    	}
    	echo $pp;
    	?>">
    	<?php
    	print substr ($pp,0,70);
    	if (strlen($pp)>70) echo '...';
    	?></a>
    <?php } ?>
   </td>
   <td class="mytdcml"><?php echo $lineuser['userAgent'];?></td>	
    
    
  </tr>
<?php 
}

?>
<tr class="table-warning">
  <td class="bottomsums" colspan="4"><b><?php echo gks_lang('Σύνολα');?></b></td>
  <td class="bottomsums mytdcm"><b><?php echo myNumberFormat($sum2,0);?></b></td>
  <td class="bottomsums" colspan="3"></td>
</tr>
</tbody>
</table>



<hr style="margin:50px"/>
<?php gks_erp_app_purchase_ads_fix_970x90('page');?>



<?php
// Performing SQL query

$query = "SELECT gks_stat_ips.country_initials, gks_country.country_name, Count(gks_stat_stat.ip) AS rrr, max(gks_stat_stat.timevisit) AS lastmytime
FROM (gks_stat_stat 
LEFT JOIN gks_stat_ips ON gks_stat_stat.ip = gks_stat_ips.ip) 
LEFT JOIN gks_country ON gks_stat_ips.country_initials = gks_country.country_initials
WHERE timevisit>='".$p1."' and timevisit<='".$p2."' ";
if (isset($_gks_session['gks']['stat']['admin_stats_bots_enable']) and $_gks_session['gks']['stat']['admin_stats_bots_enable']==false) {
	$query.=" and  (gks_stat_ips.isbot =0 or gks_stat_ips.isbot is null)";
}
$query.= " GROUP BY gks_stat_ips.country_initials, gks_country.country_name
ORDER BY Count(gks_stat_stat.ip) DESC, lastmytime";


$result = $db_link->query($query);        
if (!$result) debug_mail(false,'stat index error sql',$query);



// Printing results in HTML
?>
<table class="table table-sm table-responsive1 table-striped table-bordered" border="0" style="width:96%;font-size:0.8rem;" cellspacing="0" cellpadding="5"  align=center>
<thead>
  <tr>
    <th class="table-dark" scope="col" width="0%" nowrap>#</th>
    <th class="table-dark" scope="col" width="0%" nowrap>Last Time</th>
    <th class="table-dark" scope="col" width="0%" nowrap ></td>
    <th class="table-dark" scope="col" width="100%" nowrap><?php echo gks_lang('Χώρα');?></td>
    <th class="table-dark" scope="col" width="0%" style="text-align: right;" ><?php echo gks_lang('Πλήθος');?></td>
  </tr>
</thead>
<tbody>  
<?php 
$sum1=0;
$sum2=0;
$i = 0;

while ($line = $result->fetch_assoc()) {  
	$i++;
	$sum1=$sum1+1;
	$sum2=$sum2+$line["rrr"];
?>
  <tr>
<?php 
#    href="http://www.samspade.org/t/ipwhois?a=1.1.1.1
?>
    <th class="mytdcm" scope="row"><?php echo $i;?></th>
    <td class="mytdcm" nowrap><?php print showDate(strtotime($line["lastmytime"]), 'd/m/Y H:i:s', 1);?></td>
  	<td class="mytdcm" nowrap><?php echo gks_stat_country_icon($line); ?></td>  
  	<td class="mytdcml"><?php echo $line['country_name'];?></td>  
    <td class="mytdcm"><?php print myNumberFormat($line["rrr"],0);?></td>
  </tr>
<?php 
}

?>
<tr class="table-warning">
<td class="bottomsums" colspan="4"><b><?php echo gks_lang('Σύνολα');?></b></td>
<td class="bottomsums mytdcm"><b><?php echo myNumberFormat($sum2,0);?></b></td>
</tr>
</tbody>
</table>





<hr style="margin:50px"/>
<?php gks_erp_app_purchase_ads_fix_970x90('page');?>

<?php
// Performing SQL query
$query = "SELECT gks_stat_stat.ip, Count( pageurl ) AS rrr, max(timevisit) as lastmytime , 
gks_stat_ips.country_initials, gks_stat_ips.dns_name,
gks_country.country_name
FROM (gks_stat_stat 
left join gks_stat_ips on gks_stat_ips.ip = gks_stat_stat.ip)
LEFT JOIN gks_country ON gks_stat_ips.country_initials = gks_country.country_initials
WHERE gks_stat_stat.timevisit>'".$p1."' and gks_stat_stat.timevisit<='".$p2."' ";
if (isset($_gks_session['gks']['stat']['admin_stats_bots_enable']) and $_gks_session['gks']['stat']['admin_stats_bots_enable']==false) {
	$query.=" and  (gks_stat_ips.isbot =0 or gks_stat_ips.isbot is null)";
}
$query.=" GROUP BY gks_stat_stat.ip, gks_stat_ips.country_initials, gks_stat_ips.dns_name,gks_country.country_name
ORDER BY rrr DESC";  

$result = $db_link->query($query);        
if (!$result) debug_mail(false,'stat index error sql',$query);



// Printing results in HTML
?>

<table class="table table-sm table-responsive1 table-striped table-bordered" border="0" style="width:96%;font-size:0.8rem;" cellspacing="0" cellpadding="5"  align=center>
<thead>
  <tr>
    <th class="table-dark" scope="col" width="0%" nowrap>#</th>
    <th class="table-dark" scope="col" width="0%" nowrap><?php echo gks_lang('Ημερομηνία');?></th>
    <th class="table-dark" scope="col" width="0%" nowrap><?php echo gks_lang('Χώρα');?></td>
    <th class="table-dark" scope="col" width="0%" nowrap><?php echo gks_lang('IP');?></th>
    <th class="table-dark" scope="col" width="100%" nowrap><?php echo gks_lang('Hostname');?></th>
    <th class="table-dark" scope="col" width="0%" style="text-align: right;" ><?php echo gks_lang('Πλήθος');?></td>
  </tr>
</thead>
<tbody>  
<?php 
$sum1=0;
$sum2=0;
$i = 0;

while ($line = $result->fetch_assoc()) {  
	$i++;
	$sum1=$sum1+1;
	$sum2=$sum2+$line["rrr"];
?>
  <tr>
<?php 
#    href="http://www.samspade.org/t/ipwhois?a=1.1.1.1
?>
    <th scope="row" class="mytdcm"><?php echo $i;?>&nbsp;&nbsp;</th>
    <td class="mytdcm" nowrap><?php 
    $ltime=$line["lastmytime"];
     print showDate(strtotime($ltime), 'd/m/Y H:i:s', 1);
      
    ?>
    </td>
  	<td class="mytdcm" nowrap>
  	  <?php 
  	  if (isset($line['dns_name'])==false or $line['dns_name']=='') $line['dns_name']=$line['ip'];
  	  echo gks_stat_country_icon($line); 
  	  ?>
  	  
    	
  	</td>      
  	<td class="mytdcml" nowrap>
	    <a href="admin-stat-ip.php?ip=<?php print $line["ip"]?>"><?php print $line['ip']?></a>
  	</td>
  	<td class="mytdcml">
	    <a target="_blank" href="https://apps.db.ripe.net/db-web-ui/query?bflag=false&dflag=false&rflag=true&searchtext=<?php print $line["ip"]?>&source=RIPE"><?php print $line['dns_name']?></a>
	  </td>
    <td class="mytdcm"><?php print myNumberFormat($line["rrr"],0);?></td>
  </tr>
<?php 
}


?>
<tr class="table-warning">
<td class="bottomsums" colspan="5"><b><?php echo gks_lang('Σύνολα');?></b></td>
<td class="bottomsums mytdcm"><b><?php echo myNumberFormat($sum2,0);?></b></td>
</tr>
</tbody>
</table>



<hr style="margin:50px"/>
<?php gks_erp_app_purchase_ads_fix_970x90('page');?>

<?php
// Performing SQL query

$query = "SELECT host,pageurl, Count(gks_stat_stat.ip) AS rrr, max(timevisit) as lastmytime 
FROM gks_stat_stat 
left join gks_stat_ips on gks_stat_ips.ip = gks_stat_stat.ip
WHERE timevisit>='".$p1."' and timevisit<='".$p2."' ";
if (isset($_gks_session['gks']['stat']['admin_stats_bots_enable']) and $_gks_session['gks']['stat']['admin_stats_bots_enable']==false) {
	$query.=" and  (gks_stat_ips.isbot =0 or gks_stat_ips.isbot is null)";
}
$query.= " GROUP BY host,pageurl 
ORDER BY rrr DESC,lastmytime desc,pageurl;";


$result = $db_link->query($query);        
if (!$result) debug_mail(false,'stat index error sql',$query);



// Printing results in HTML
?>
<table class="table table-sm table-responsive1 table-striped table-bordered" border="0" style="width:96%;font-size:0.8rem;" cellspacing="0" cellpadding="5"  align=center>
<thead>
  <tr>
    <th class="table-dark" scope="col" width="0%" nowrap>#</th>
    <th class="table-dark" scope="col" width="0%" nowrap><?php echo gks_lang('Ημερομηνία');?></th>
    <th class="table-dark" scope="col" width="100%" nowrap><?php echo gks_lang('Σελίδα');?></th>
    <th class="table-dark" scope="col" width="0%" style="text-align: right;" ><?php echo gks_lang('Πλήθος');?></td>
  </tr>
</thead>
<tbody>  
<?php 
$sum1=0;
$sum2=0;
$i = 0;

while ($line = $result->fetch_assoc()) {  
	$i++;
	$sum1=$sum1+1;
	$sum2=$sum2+$line["rrr"];
?>
  <tr>
<?php 
#    href="http://www.samspade.org/t/ipwhois?a=1.1.1.1
?>
    <th scope="row"  class="mytdcm"><?php echo $i;?>&nbsp;&nbsp;</th>
    <td  class="mytdcm" nowrap><?php 
    $ltime=$line["lastmytime"];
     print showDate(strtotime($ltime), 'd/m/Y H:i:s', 1);
      
    ?>
    </td>
	  <td class="mytdcml" ><a href="//<?php 
    	$pp =$line["host"].$line["pageurl"];
		
    	echo $pp;
    	?>">
    	<?php
    	print substr ($pp,0,70);
    	if (strlen($pp)>70) echo '...';
    	?></a>
	  </td>
    <td class="mytdcm"><?php print myNumberFormat($line["rrr"],0);?></td>
  </tr>
<?php 
}


?>
<tr class="table-warning">
<td class="bottomsums" colspan="3"><b><?php echo gks_lang('Σύνολα');?></b></td>
<td class="bottomsums mytdcm" ><b><?php echo myNumberFormat($sum2,0);?></b></td>
</tr>
</tbody>
</table>








<hr style="margin:50px"/>
<?php gks_erp_app_purchase_ads_fix_970x90('page');?>

<?php
// Performing SQL query

$query = "SELECT useragent, Count(gks_stat_stat.ip) AS rrr, max(timevisit) as lastmytime 
FROM gks_stat_stat 
left join gks_stat_ips on gks_stat_ips.ip = gks_stat_stat.ip
WHERE timevisit>='".$p1."' and timevisit<='".$p2."' ";
if (isset($_gks_session['gks']['stat']['admin_stats_bots_enable']) and $_gks_session['gks']['stat']['admin_stats_bots_enable']==false) {
	$query.=" and  (gks_stat_ips.isbot =0 or gks_stat_ips.isbot is null)";
}
$query.= " GROUP BY useragent 
ORDER BY rrr DESC,lastmytime desc;";


$result = $db_link->query($query);        
if (!$result) debug_mail(false,'stat index error sql',$query);



// Printing results in HTML
?>
<table class="table table-sm table-responsive1 table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5"  align=center>
<thead>
  <tr>
    <th class="table-dark" scope="col" width="0%" nowrap>#</th>
    <th class="table-dark" scope="col" width="0%" nowrap><?php echo gks_lang('Ημερομηνία');?></th>
    <th class="table-dark" scope="col" width="100%" nowrap>User Agent</th>
    <th class="table-dark" scope="col" width="0%" style="text-align: right;" ><?php echo gks_lang('Πλήθος');?></td>
  </tr>
</thead>
<tbody>  
<?php 
$sum1=0;
$sum2=0;
$i = 0;

while ($line = $result->fetch_assoc()) {  
	$i++;
	$sum1=$sum1+1;
	$sum2=$sum2+$line["rrr"];
?>
  <tr>
<?php 
#    href="http://www.samspade.org/t/ipwhois?a=1.1.1.1
?>
    <th scope="row" class="mytdcm"><?php echo $i;?>&nbsp;&nbsp;</th>
    <td nowrap class="mytdcm"><?php 
    $ltime=$line["lastmytime"];
     print showDate(strtotime($ltime), 'd/m/Y H:i:s', 1);
      
    ?>
    </td>
	  <td class="mytdcml"><?php echo $line["useragent"]?></td>
  
    <td class="mytdcm"><?php print myNumberFormat($line["rrr"],0);?></td>
  </tr>
<?php 
}


?>
<tr class="table-warning">
<td class="bottomsums" colspan="3"><b><?php echo gks_lang('Σύνολα');?></b></td>
<td class="bottomsums mytdcm"><b><?php echo myNumberFormat($sum2,0);?></b></td>
</tr>
</tbody>
</table>



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>;

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('#date_from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#date_to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  
});
  
</script>

<?php
//db_close();
include_once('_my_footer_admin.php');
