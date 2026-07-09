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


$lastercs=300;
if (isset($_GET['c'])) {$lastercs=intval($_GET['c']);}

$my_page_title=gks_lang('Στατιστικά').': '.$lastercs.' '.gks_lang('Τελευταίες Σελίδες');
$nav_active_array=array('stat','stat_lasthits');

stat_record();


$query = "SELECT gks_stat_stat.*, 
gks_stat_ips.dns_name, gks_stat_ips.country_initials, 
gks_country.country_name
FROM (gks_stat_stat 
LEFT JOIN gks_stat_ips ON gks_stat_stat.ip = gks_stat_ips.ip) 
LEFT JOIN gks_country ON gks_stat_ips.country_initials = gks_country.country_initials
where 1=1";
if (isset($_gks_session['gks']['stat']['admin_stats_bots_enable']) and $_gks_session['gks']['stat']['admin_stats_bots_enable']==false) {
	$query.=" and (gks_stat_ips.isbot =0 or gks_stat_ips.isbot is null)";
}
$query.= " ORDER BY gks_stat_stat.id DESC ";
if ($lastercs>0) {
	$query .= " limit 0,".$lastercs;
}
//echo $query;die(); 
$mybackcolor=' style="background-color: #C0C0C0"';


$result = $db_link->query($query);        
if (!$result) debug_mail(false,'error stat_sql',$query);

if (!$result) die('dddddd');
$mybackcolor=' style="background-color: #C0C0C0"';

include_once('_my_header_admin.php');
?>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
      <div>
        <a class="btn btn-primary btn-sm" href="cron_ips.php?stat=1&redirect=<?php echo rawurlencode($_SERVER['SCRIPT_NAME']).rawurlencode('?'.$_SERVER['QUERY_STRING']) ?>"><?php echo gks_lang('Ανανέωση');?></a>
        <?php
        $sql_remain="select count(*) as cc from gks_stat_queue";
        $result_remain = $db_link->query($sql_remain);        
        if (!$result_remain) debug_mail(false,'error stat_sql',$sql_remain);
        $row_remain = $result_remain->fetch_assoc();
        echo number_format($row_remain['cc'],0,',','.').' records to add';
        ?>
      </div>
    </div>
  </div>
</div>



<p align="center">Last
	<span <?php if ($lastercs==300) echo $mybackcolor;?> ><a href="?c=300">300</a></span>
	<span <?php if ($lastercs==1000) echo $mybackcolor;?> ><a href="?c=1000">1000</a></span>
	<span <?php if ($lastercs==2000) echo $mybackcolor;?> ><a href="?c=2000">2.000</a></span>
	<span <?php if ($lastercs==5000) echo $mybackcolor;?> ><a href="?c=5000">5.000</a></span>
	<span <?php if ($lastercs==10000) echo $mybackcolor;?> ><a href="?c=10000">10.000</a></span>
	<span <?php if ($lastercs==-1) echo $mybackcolor;?> ><a href="?c=-1">All</a></span>
Hits</p>
<table class="table table-sm table-responsive1 table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
  <tr>
    <th class="table-dark" scope="col" width="0%" nowrap>#</td>
    <th class="table-dark" scope="col" width="0%" nowrap ><?php echo gks_lang('Πότε');?></td>
    <th class="table-dark" scope="col" width="0%" nowrap><?php echo gks_lang('Χώρα');?></td>
    <th class="table-dark" scope="col" width="0%" nowrap><?php echo gks_lang('IP');?></td>
    <th class="table-dark" scope="col" width="0%" nowrap ><?php echo gks_lang('Hostname');?></td>
    <th class="table-dark" scope="col" width="0%" nowrap align="left" ><?php echo gks_lang('Χρήστης');?></td>
    <th class="table-dark" scope="col" width="20%" align="left" ><?php echo gks_lang('Σελίδα');?></td>
    <th class="table-dark" scope="col" width="25%" align="left" ><?php echo gks_lang('Διεύθυνση');?></td>
    <th class="table-dark" scope="col" width="25%" align="left" ><?php echo gks_lang('Από','part2');?></td>
    <th class="table-dark" scope="col" width="30%" align="left" ><?php echo gks_lang('User Agent');?></td>
  </tr>
</thead>  
<tbody> 
<?php 
$i=0;
while ($line = $result->fetch_assoc()) {

	$i++;
?>
  <tr>
    <th scope="row" class="mytdcm"><?php echo $i;?></th>
    <td nowrap class="mytdcm"><?php 
    $ltime=$line['timevisit'];

    
    print showDate(strtotime($ltime), 'd/m/Y H:i:s', 1);

    
    ?>&nbsp;&nbsp;
  	</td>
  	<td nowrap class="mytdcm">
  	  
  	  <?php 
  	  if (isset($line['dns_name'])==false or $line['dns_name']=='') $line['dns_name']=$line['ip'];
  	  echo gks_stat_country_icon($line); 
  	  ?>
  	  
    	
  	</td>
  	<td nowrap class="mytdcm">
	    <a href="admin-stat-ip.php?ip=<?php print $line['ip']?>"><?php print $line['ip']?></a>
  	</td>
  	<td nowrap class="mytdcm">
	    <a target="_blank" href="https://apps.db.ripe.net/db-web-ui/query?bflag=false&dflag=false&rflag=true&searchtext=<?php print $line['ip']?>&source=RIPE"><?php print $line['dns_name']?></a>
	  </td>
	  <td nowrap class="mytdcml"><a href="admin-stat-user.php?username=<?php echo urlencode($line['username'])?>"><?php echo $line['username']?></a> &nbsp;</td>
	  <td nowrap class="mytdcml"><?php echo htmlspecialchars_gks($line['pagetitle'])?> &nbsp;</td>
	  <td class="mytdcml"><a href="<?php 
    	if ($line['host']!='') echo '//';
    	
    	$pp =$line['host'].$line['pageurl'];
    	if (isset($line['query_string']) && $line['query_string']!='') {
    	 $pp.='?'.$line['query_string']; 
    	}
    	echo $pp;
    	?>">
    	<?php
    	$pp =$line['pageurl'];
    	if (isset($line['query_string']) && $line['query_string']!='') {
    	 $pp.='?'.$line['query_string']; 
    	}
    	//echo $pp;
    	    	
    	print substr ($pp,0,70);
    	if (strlen($pp)>70) echo '...';
    	?></a>
	</td>
	
	<td class="mytdcml">
		<?php
		$pp =$line['referer'];
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

