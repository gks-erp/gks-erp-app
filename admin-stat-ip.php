<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



db_open();
$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
}
if ($userrole=='') {header('Location: /my/admin-deny.php'); die(); }


if (!isset($_GET["ip"]) or trim_gks($_GET["ip"])=='') {
  echo 'The IP is not set';
  die(); 
}


$ssip=trim_gks($_GET["ip"]).'';

$my_page_title=gks_lang('Στατιστικά IP').': '.$ssip;
$nav_active_array=array('stat');
stat_record();



$ssname=nslookup($ssip);

$lastercs=300;
if (isset($_GET['c'])) {$lastercs=intval($_GET['c']);}

$query = "SELECT id,pagetitle, timevisit, ip, host,pageurl,query_string,username,referer,userAgent ";
$query .= " FROM gks_stat_stat";
$query .= " WHERE (((ip)='".$db_link->escape_string($ssip)."'))";
$query .= " ORDER BY id DESC ";
if ($lastercs>0) {
	$query .= " limit 0,".$lastercs;
}

$mybackcolor=' style="background-color: #C0C0C0"';

$result = $db_link->query($query);
if (!$result) debug_mail(false,'stat ip error sql',$query);
	




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


<p align="center">
  <?php echo gks_lang('Όνομα Η/Υ');?>: <?php echo $ssname['dns_name'];?><br>
  <?php echo gks_lang('Είναι Bot');?>: <?php echo $ssname['isbot'];?><br>
  <?php echo gks_lang('Αρχικά χώρας');?>: <?php echo $ssname['country_initials'];?><br>
  <?php echo gks_lang('Χώρα');?>: <?php echo $ssname['country_name'];?><br>
  <?php echo gks_lang('Σημαία');?>: <?php echo gks_stat_country_icon($ssname);?>
</p>

<p align="center">Last
	<span <?php if ($lastercs==300) echo $mybackcolor;?> ><a href="?ip=<?php echo $ssip?>&c=300">300</a></span>
	<span <?php if ($lastercs==1000) echo $mybackcolor;?> ><a href="?ip=<?php echo $ssip?>&c=1000">1000</a></span>
	<span <?php if ($lastercs==2000) echo $mybackcolor;?> ><a href="?ip=<?php echo $ssip?>&c=2000">2.000</a></span>
	<span <?php if ($lastercs==5000) echo $mybackcolor;?> ><a href="?ip=<?php echo $ssip?>&c=5000">5.000</a></span>
	<span <?php if ($lastercs==10000) echo $mybackcolor;?> ><a href="?ip=<?php echo $ssip?>&c=10000">10.000</a></span>
	<span <?php if ($lastercs==-1) echo $mybackcolor;?> ><a href="?ip=<?php echo $ssip?>&c=-1">All</a></span>
Hits</p>

<?php gks_erp_app_purchase_ads_fix_970x90('afterfilters');?>
<table class="table table-sm table-responsive1 table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
  <tr>
    <th class="table-dark" scope="col" width="0%" nowrap>#</td>
    <th class="table-dark" scope="col" width="0%" nowrap><?php echo gks_lang('Πότε');?></td>
    <th class="table-dark" scope="col" width="0%" nowrap align="left" ><?php echo gks_lang('Χρήστης');?></td>
    <th class="table-dark" scope="col" width="20%" align="left" ><?php echo gks_lang('Σελίδα');?></td>
    <th class="table-dark" scope="col" width="25%" align="left" ><?php echo gks_lang('Διεύθυνση');?></td>
    <th class="table-dark" scope="col" width="25%" align="left" ><?php echo gks_lang('Από','part2');?></td> 
    <th class="table-dark" scope="col" width="30%" align="left" ><?php echo gks_lang('User Agent');?></td>    
  </tr>
</thead>  
<tbody>  
<?php 
$sum1=0;
$Oldmytime=0;

$i=0;

while ($line = $result->fetch_assoc()) {  
	$i++;
	$sum1=$sum1+1;
	
	
	
?>
  <tr>
    <th scope="row" class="mytdcm"><?php echo $i;?>&nbsp;&nbsp;</th>
    <td nowrap class="mytdcm"><?php 
    $ltime=$line["timevisit"];

		print showDate(strtotime($ltime), 'd/m/Y H:i:s', 1);
    ?>
    </td>
    <td nowrap class="mytdcml"><a href="admin-stat-user.php?username=<?php echo urlencode($line["username"])?>"><?php echo $line["username"]?></a></td>
    <td nowrap class="mytdcml"><?php echo htmlspecialchars_gks($line["pagetitle"])?> &nbsp;</td>
    <td class="mytdcml" ><a href="//<?php 
    	$pp =$line["host"].$line["pageurl"];
    	if (isset($line["query_string"]) && $line["query_string"]!='') {
    	 $pp.='?'.$line["query_string"]; 
    	}
		
    	echo $pp;
    	?>">
    	<?php 
    	print substr ($pp,0,70);
    	if (strlen($pp)>70) echo '...';
    	?></a></td>
	<td class="mytdcml" >
		<?php
			$pp =$line["referer"];
		if ($pp != null && !empty($pp)) {
			echo '<a href="'.$pp.'">';
			echo substr ($pp,0,70);
			if (strlen($pp)>70) echo '...';
			echo '</a>';
		}
		else{
			echo 'N/A';
		}
    	?>
	</td>   
	<td class="mytdcml"><?php echo $line['userAgent'];?></td>	 	
  </tr>
<?php 
#$Oldmytime=$line["mytime"];
}
?>


<tbody>
</table>

<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>;

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
});
  
</script>

<?php
//db_close();
include_once('_my_footer_admin.php');

