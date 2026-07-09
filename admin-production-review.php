<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Επισκόπηση Παραγωγής');
$nav_active_array=array('production','production_review');

db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_review','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}



$show_money=false;
if (ur_ad()) $show_money=true;



//$date_from=showDate(time()-31*86400, 'd/m/Y', 1);
$date_from=_time_user(strtotime('-12 month'),1);
$date_from='1/'.date('m/Y',$date_from);

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


$mydata1=array();
//$i=0;
$cy=date('Y',$date_from_time);
$cm=date('m',$date_from_time);
$cye=date('Y',$date_to_time);
$cme=date('m',$date_to_time);
do {
  $key=$cy.'_'.$cm;
  $mydata1[$key]=array(
    'year'=>$cy,
    'month'=>$cm,
    'label' => $cm.'/'.$cy,
    '005prodraft'=>0,
    '010draft'=>0,
    '020pending'=>0,
    '025offer'=>0,
    '030forcancellation'=>0,
    '040cancelled'=>0,
    '050rejected'=>0,
    '055wait_payment'=>0,
    '060registered'=>0,
    '070inproduction'=>0,
    '080failed'=>0,
    '090indelivery'=>0,
    '095execute'=>0,
    '100completed'=>0,
    '110payment'=>0,
    'all'=>0,
    'ajia'=>0,
  );
  
  $cm++;
  if ($cm>=13) {
    $cy++;
    $cm=1;  
  }
  if ($cy>$cye or ($cy==$cye and $cm>$cme)) break;
  //$i++;
  //if ($i>100) break;
} while(true);


$sql_field_ajia='gks_orders.gks_price_net';
gks_plugins_functions_run('admin_production_review_field_ajia',array(
  'sql_field_ajia' => &$sql_field_ajia,
));


$sql="select year(DATE(CONVERT_TZ(order_date, 'GMT','Europe/Athens'))) as myyear,
month(DATE(CONVERT_TZ(order_date, 'GMT','Europe/Athens'))) as mymonth,
order_state,
count(id_order) as ccc,
sum(".$sql_field_ajia.") as ajia
from gks_orders
WHERE order_date >= '".$p1."' and order_date < '".$p2."'
group by myyear,mymonth,order_state
order by myyear,mymonth";

$result = $db_link->query($sql);        
if (!$result) {debug_mail(false,'stat index error sql',$sql);die('sql error');}
while ($row = $result->fetch_assoc()) {  
  $key=$row['myyear'].'_'.$row['mymonth'];
  if (isset($mydata1[$key])) {
    if (trim_gks($row['order_state'])!='') {
      $mydata1[$key][$row['order_state']]+=$row['ccc'];
      $mydata1[$key]['all']+=$row['ccc'];
      $mydata1[$key]['ajia']+=$row['ajia'];
    }
  }
}



function aak($mya, $myk) {
  $myr='';
  foreach ($mya as $val) {
    $myr.='"'. $val[$myk] .'",';
  }  
  if (strlen($myr)>0) $myr=substr($myr, 0, strlen($myr)-1);
  return $myr;
}
function aaks($mya, $myk) {
  $myr=0;
  foreach ($mya as $val) {
    $myr+=$val[$myk];
  }  
  return $myr;
}

include_once('_my_header_admin.php');

//print '<pre>';
//print $p1;
//print "\r\n";
//print $p2;
//
//print "\r\n";
//
//print_r($mydata1);
//
//
//print '</pre>';
?>



<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
  </div>
</div>




<form method="GET" action="" id=form2 name=form2 style="margin-top:30px;">
  <div style="margin-bottom: 16px;text-align:center;vertical-align: middle;">
  <?php echo gks_lang('Από');?>: <input class="form-control form-control-sm" type="text" style="width:120px;display: inline-block;vertical-align: middle;" id="date_from" name="date_from" value="<?php echo $date_from;?>"/>
  <?php echo gks_lang('Έως');?>: <input class="form-control form-control-sm" type="text" style="width:120px;display: inline-block;vertical-align: middle;" id="date_to"   name="date_to"   value="<?php echo $date_to;?>"/>
  <input type="submit" value="<?php echo gks_lang('Εύρεση');?>" name="B1" class="btn btn-primary btn-sm" style="vertical-align: middle;"> 
  </div>
</form>

<div class="container-fluid">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <div class="chart-container" style="position: relative; width:96%;height:500px;text-align: center;margin: auto;border: 1px solid #b7b7b7;">
          <canvas id="canvas1"></canvas>
      </div>
    </div>
  </div>
</div>


<script  src="js/chartjs-2.9.2/dist/Chart.bundle.min.js"></script>
<link rel="stylesheet" href="js/chartjs-2.9.2/dist/Chart.min.css" type="text/css">


<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('#date_from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#date_to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));


  var ctx = document.getElementById('canvas1');
  var myChart = new Chart(ctx, {
      type: 'bar',
      data: {
          labels: [<?php echo aak($mydata1,'label');?>],
          datasets: [
<?php if ($show_money) {?>          
            {
              type: 'line',
              label: '<?php echo gks_lang('Αξία');?>',
              data: [<?php echo aak($mydata1,'ajia');?>],
              fill: false,
              backgroundColor: '#0032c3',
              borderColor: '#002aa1',
              borderWidth: 1,
              yAxisID: 'y-ajia',
            },
<?php } ?>            
            {
              label: '<?php echo getOrderStateDescr('005prodraft');?>',
              data: [<?php echo aak($mydata1,'005prodraft');?>],
              backgroundColor: '#777777',
              borderColor: '#777777',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
            {
              label: '<?php echo getOrderStateDescr('010draft');?>',
              data: [<?php echo aak($mydata1,'010draft');?>],
              backgroundColor: '#aaaaaa',
              borderColor: '#787878',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
            {
              label: '<?php echo getOrderStateDescr('020pending');?>',
              data: [<?php echo aak($mydata1,'020pending');?>],
              backgroundColor: '#5bc0de',
              borderColor: '#4896ad',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
            {
              label: '<?php echo getOrderStateDescr('025offer');?>',
              data: [<?php echo aak($mydata1,'025offer');?>],
              backgroundColor: '#5bc0de',
              borderColor: '#4896ad',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
            {
              label: '<?php echo getOrderStateDescr('030forcancellation');?>',
              data: [<?php echo aak($mydata1,'030forcancellation');?>],
              backgroundColor: '#ed9c28',
              borderColor: '#b3761e',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
            {
              label: '<?php echo getOrderStateDescr('040cancelled');?>',
              data: [<?php echo aak($mydata1,'040cancelled');?>],
              backgroundColor: '#ff0000',
              borderColor: '#c30000',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
            {
              label: '<?php echo getOrderStateDescr('050rejected');?>',
              data: [<?php echo aak($mydata1,'050rejected');?>],
              backgroundColor: '#d2322d',
              borderColor: '#962420',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
            {
              label: '<?php echo getOrderStateDescr('055wait_payment');?>',
              data: [<?php echo aak($mydata1,'055wait_payment');?>],
              backgroundColor: '#518df1',
              borderColor: '#518df1',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
            {
              label: '<?php echo getOrderStateDescr('060registered');?>',
              data: [<?php echo aak($mydata1,'060registered');?>],
              backgroundColor: '#337AB7',
              borderColor: '#245580',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
            {
              label: '<?php echo getOrderStateDescr('070inproduction');?>',
              data: [<?php echo aak($mydata1,'070inproduction');?>],
              backgroundColor: '#8261a7',
              borderColor: '#584272',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
            {
              label: '<?php echo getOrderStateDescr('080failed');?>',
              data: [<?php echo aak($mydata1,'080failed');?>],
              backgroundColor: '#ff3a00',
              borderColor: '#c92e00',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
            {
              label: '<?php echo getOrderStateDescr('090indelivery');?>',
              data: [<?php echo aak($mydata1,'090indelivery');?>],
              backgroundColor: '#71e399',
              borderColor: '#54aa72',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
            {
              label: '<?php echo getOrderStateDescr('095execute');?>',
              data: [<?php echo aak($mydata1,'095execute');?>],
              backgroundColor: '#71e399',
              borderColor: '#71e399',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
            {
              label: '<?php echo getOrderStateDescr('100completed');?>',
              data: [<?php echo aak($mydata1,'100completed');?>],
              backgroundColor: '#47a447',
              borderColor: '#2e6b2e',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
            {
              label: '<?php echo getOrderStateDescr('110payment');?>',
              data: [<?php echo aak($mydata1,'110payment');?>],
              backgroundColor: '#47a447',
              borderColor: '#2e6b2e',
              borderWidth: 1,
              yAxisID: 'y-posotita',
            },
            
          ]
      },
      options: {
        responsive: true,
        maintainAspectRatio:false,
        title: {
				  display: true,
					text: '<?php echo gks_lang('Παραγγελίες');?>'
				},
        tooltips: {
          //mode: 'index', //label
          //position: 'nearest',
          //intersect: false,
        },				
        hover: {
          //mode: 'index', //dataset
        },				
        scales: {
          xAxes: [{
            stacked: true,
            ticks: {
              beginAtZero: true
            }
          }],
          yAxes: [
            {
              stacked: true,
              ticks: {
                beginAtZero: true
              },
              position: 'left',
              id: 'y-posotita',
              scaleLabel: {
								display: true,
								labelString: '<?php echo gks_lang('Πλήθος Παραγγελιών');?>'
							},              
            },

<?php if ($show_money) {?>            
            {
              stacked: true,
              ticks: {
                beginAtZero: true
              },
              position: 'right',
              id: 'y-ajia',
              gridLines : {
                //display: true,
					      //drawBorder: true,
					      //drawOnChartArea: false,
					      color: 'rgba(0,42,161,0.3)',
					      borderWidth: 1,
      				},
              scaleLabel: {
								display: true,
								labelString: '<?php echo gks_lang('Αξία Παραγγελιών');?>'
							},       				
            },
<?php } ?>            
          ],
        }
      }
  });
 



  
    
});
</script>



<?php 
include_once('_my_footer_admin.php');  