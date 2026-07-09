<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Online');
$nav_active_array=array('stat','stat_online');

db_open();
stat_record();
$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
}
if ($userrole=='') {header('Location: /my/admin-deny.php'); die(); }


//where lasturl not like '%/xmlrpc.php' and lasturl not like '%/wp-cron.php'
$query="SELECT gks_stat_online.*, 
gks_stat_ips.dns_name, gks_stat_ips.country_initials, 
gks_country.country_name
FROM (gks_stat_online 
LEFT JOIN gks_stat_ips ON gks_stat_online.visitor = gks_stat_ips.ip) 
LEFT JOIN gks_country ON gks_stat_ips.country_initials = gks_country.country_initials

";
if (isset($_gks_session['gks']['stat']['admin_stats_bots_enable']) and $_gks_session['gks']['stat']['admin_stats_bots_enable']==false) {
	$query.=" and (gks_stat_ips.isbot =0 or gks_stat_ips.isbot is null)";
}
$query.=" order by timevisit desc";



$mybackcolor=' style="background-color: #C0C0C0"';

$result = $db_link->query($query);
if (!$result) debug_mail(false,'stat online error sql',$query);
	






include_once('_my_header_admin.php');
?>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
      <div><a class="btn btn-primary btn-sm" href="cron_ips.php?stat=1&redirect=<?php echo rawurlencode($_SERVER['SCRIPT_NAME']).'?'.rawurlencode($_SERVER['QUERY_STRING']) ?>"><?php echo gks_lang('Ανανέωση');?></a></div>
    </div>
  </div>
</div>




<table class="table table-sm table-responsive1 table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
  <tr>
    <th class="table-dark" scope="col" width="0%" align="center" nowrap>#</td>
    <th class="table-dark" scope="col" width="0%" align="center" nowrap><?php echo gks_lang('Πρίν από');?></td>
    <th class="table-dark" scope="col" width="0%" nowrap><?php echo gks_lang('Χρήστης');?></td>
    <th class="table-dark" scope="col" width="0%" nowrap><?php echo gks_lang('Χώρα');?></td>
    <th class="table-dark" scope="col" width="0%" nowrap ><?php echo gks_lang('IP');?></td>
    <th class="table-dark" scope="col" width="0%" nowrap ><?php echo gks_lang('Hostname');?></td>
    <th class="table-dark" scope="col" width="30%" nowrap><?php echo gks_lang('Σελίδα');?></td>
    <th class="table-dark" scope="col" width="30%" nowrap><?php echo gks_lang('Διεύθυνση');?></td>
    <th class="table-dark" scope="col" width="40%" align="left" nowrap><?php echo gks_lang('User Agent');?></td>
  </tr>
</thead>
<tbody>    
<?php 

$mytime=time() ;


$i=0;
while ($line = $result->fetch_assoc()) {  
$i++;


?>
  <tr>
    <th scope="row" class="mytdcm"><?php echo $i;?></th>
    <td class="mytdcml" nowrap><?php  print date('H:i:s',$mytime-strtotime($line['timevisit']));?></td>
    <td class="mytdcml" nowrap> <a href="admin-stat-user.php?username=<?php echo urlencode($line['username'])?>"><?php echo $line['username']?></a></td>

  	<td nowrap class="mytdcm">
  	  
  	  <?php 
  	  
  	  if (isset($line['dns_name'])==false or $line['dns_name']=='') $line['dns_name']=$line['visitor'];
  	  echo gks_stat_country_icon($line); 
  	  
  	  
  	  ?>
  	  
    	
  	</td> 
  	<td nowrap class="mytdcml">
	    <a href="admin-stat-ip.php?ip=<?php print $line['visitor']?>"><?php print $line['visitor']?></a>
	  </td>
  	<td nowrap class="mytdcml">
	    <a target="_blank" href="https://apps.db.ripe.net/db-web-ui/query?bflag=false&dflag=false&rflag=true&searchtext=<?php print $line['visitor']?>&source=RIPE"><?php print $line['dns_name']?></a>
	  </td>
    <td class="mytdcml"><?php echo htmlspecialchars_gks($line['pagetitle'])?></td>
    <td class="mytdcml"><a href="//<?php 
    	$pp =$line['host'].$line['lasturl'];
    	if (isset($line['query_string']) && $line['query_string']!='') {
    	 $pp.='?'.$line['query_string']; 
    	}
		
    	echo $pp;
    	?>">
    	<?php 
    	print substr ($pp,0,70);
    	if (strlen($pp)>70) echo '...';
    	?></a></td>
   <td class="mytdcml"><?php echo $line['userAgent'];?></td>	
  </tr>
<?php 
}
?>
</tbody>
</table>



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>;

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
});
  
</script>

<?php

include_once('_my_footer_admin.php');

