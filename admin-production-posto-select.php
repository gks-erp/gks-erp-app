<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Επιλογή Πόστου');
$nav_active_array=array('production','production_posto_select');


db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_posta_select','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}









$sql = "SELECT gks_production_posta.id_production_posto, gks_production_posta.production_posto_descr, ergasiescount.cc, ergasiescount.ccrun
FROM (gks_production_posta_users 
LEFT JOIN gks_production_posta ON gks_production_posta_users.production_posto_id = gks_production_posta.id_production_posto) 
LEFT JOIN (
  SELECT gks_production_posta_ergasies.production_posto_id, Count(gks_production_line.id_production_line) AS cc, sum(if(pl_state='050processing' ,1,0)) as ccrun
  FROM (gks_production_posta_ergasies 
  LEFT JOIN gks_production_line ON gks_production_posta_ergasies.production_ergasia_id = gks_production_line.ergasia_id) 
  LEFT JOIN gks_production_posta ON gks_production_posta_ergasies.production_posto_id = gks_production_posta.id_production_posto
  WHERE 
  gks_production_line.pl_state In ('040ready','060pause') or 
  (gks_production_line.pl_state In ('050processing') and gks_production_line.last_user_id_production=".$my_wp_user_id." and gks_production_posta.all_users=0) or 
  (gks_production_line.pl_state In ('050processing') and gks_production_posta.all_users<>0)

  GROUP BY gks_production_posta_ergasies.production_posto_id
) AS ergasiescount ON gks_production_posta_users.production_posto_id = ergasiescount.production_posto_id
WHERE (((gks_production_posta.id_production_posto) Is Not Null) 
AND ((gks_production_posta_users.production_user_id)=".$my_wp_user_id."))
ORDER BY gks_production_posta.production_posto_sortorder,gks_production_posta.production_posto_descr;";
//echo $sql;
//die();

$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');




include_once('_my_header_admin.php');
?>
<div style="width:calc(100% - 0px);height:10px;background-color:lightblue;margin:0px 0px 0px 0px">
  <div id="psososto_refresh" style="width:100%;height:10px;background-color:darkblue;"></div>
</div>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
  </div>
</div>

<div class="container" style="margin-top: 36px;">
  <div class="row">

<?php
$i = 0;
while ($row = $result->fetch_assoc()) {

	$i++;
?>

    <div class="col-sm" style="text-align: center;margin-bottom: 26px;">
      <a href="admin-production-posto-run.php?id=<?php echo $row['id_production_posto'];?>">
        <button type="button" class="btn btn-primary btn-lg select_posto" data-id="<?php echo $row['id_production_posto'];?>"><?php echo $row['production_posto_descr'];?></button>
      </a>
      <?php 
        if (isset($row['cc']) and $row['cc']>0) {
          echo '<small class="form-text text-muted">'.gks_lang('Εργασίες').': '.$row['cc'].'</small>';
        }
        if (isset($row['ccrun']) and $row['ccrun']>0) {
          echo '<small class="production_line_state_050processing">'.gks_lang('Σε Επεξεργασία').': '.$row['ccrun'].'</small>';
        }
      ?>
    </div>
<?php } ?>

  </div>
</div>

<div style="margin:10px;">
    <div style="height:6px;background-color:#eeeeee;max-width:60%;margin:auto;">
      
    </div>
</div>

<div class="container" style="margin-top: 36px;">
  <div class="row">
    <div class="col-sm" style="text-align: center;margin-bottom: 26px;">
      <a href="admin-production-posto-change-order-state.php">
        <button type="button" class="btn btn-primary btn-lg change-order-state"><?php echo gks_lang('Αλλαγή κατάσταση παραγγελίας');?></button>

      </a>
      <small class="form-text text-muted">
        <?php echo gks_lang('Από');?> 
        <span class="order_state_090indelivery"><?php echo getOrderStateDescr('090indelivery');?></span>
        <?php echo gks_lang('σε');?>
        <span class="order_state_100completed"><?php echo getOrderStateDescr('100completed');?></span>
      </small>
    </div>
    
  </div>
</div>

<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_production_posta_select','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_production_posta_select','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_production_posta_select','delete',0);?>;

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
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
      window.location.reload();
    }
    //console.log(pososto);
    $('#psososto_refresh').css('width',pososto.formatMoney(2,'.','') + '%');
    
  }
  
  $('.select_posto').click(function() {
    //data_id=$(this).attr('data-id');  
    //console.log(data_id);
    //window.location.href='admin-production-posto-run.php?id=' + data_id;
  });
  

});
</script>


<?php
//db_close();
include_once('_my_footer_admin.php');


