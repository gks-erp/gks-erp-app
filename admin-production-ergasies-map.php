<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Χάρτης Εργασιών');
$nav_active_array=array('production','production_ergasies_map');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_ergasies_map','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}







include_once('_my_header_admin.php');
?>


<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
  </div>
</div>


<?php
$sql="SELECT id_production_ergasia as id, production_ergasia_descr as descr FROM gks_production_ergasies order by production_ergasia_sortorder,production_ergasia_descr";
$result = $db_link->query($sql); 
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');
$ergasies=array();
while ($row = $result->fetch_assoc()) {
  $row['class']='';
  $ergasies[$row['id']]=$row;
}


$sql="SELECT gks_production_ergasies_mustdone.ergasia_id, gks_production_ergasies_mustdone.ergasia_mustdone_id
FROM (gks_production_ergasies_mustdone 
LEFT JOIN gks_production_ergasies AS gks_production_ergasies_n ON gks_production_ergasies_mustdone.ergasia_id = gks_production_ergasies_n.id_production_ergasia) 
LEFT JOIN gks_production_ergasies AS gks_production_ergasies_m ON gks_production_ergasies_mustdone.ergasia_mustdone_id = gks_production_ergasies_m.id_production_ergasia
WHERE (((gks_production_ergasies_n.id_production_ergasia) Is Not Null) AND ((gks_production_ergasies_m.id_production_ergasia) Is Not Null))
order by id_production_ergasia_mustdone";
$result = $db_link->query($sql); 
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');
$mustodone=array();
while ($row = $result->fetch_assoc()) {
  $mustodone[]=array('n'=> $row['ergasia_id'], 'm' => $row['ergasia_mustdone_id']);
}


foreach ($ergasies as &$erg) {
  $is_start=true;
  $is_end=true;
  foreach ($mustodone as $must) {
    if ($must['n'] == $erg['id']) $is_start=false; 
    if ($must['m'] == $erg['id']) $is_end=false; 
  }
  if ($is_start) {
    $erg['class']='starterg';
  } else if ($is_end) {
    $erg['class']='enderg';
  }
}
unset($erg);


//https://mermaidjs.github.io/#/mermaidAPI
//https://cdnjs.cloudflare.com/ajax/libs/mermaid/8.3.1/mermaid.min.js
//https://medium.com/better-programming/mermaid-create-charts-and-diagrams-with-markdown-88a9e639ab14

?>
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="mermaid" style="text-align: center;">
<?php if (count($ergasies)==0) {
        echo '<p>'.gks_lang('Δεν βρέθηκαν εργασίες').'</p>';
 } else {?>          
        graph LR
<?php
        foreach ($ergasies as $erg) {
          echo 'erg'.$erg['id'].'('.$erg['descr'].')'."\r\n";
          if ($erg['class']!='') {
            echo 'class erg'.$erg['id'].' '.$erg['class']."\r\n";
          } else {
            echo 'class erg'.$erg['id'].' myergasia'."\r\n";
          }
        } 
        foreach ($mustodone as $myconn) {
          echo 'erg'.$myconn['m'].'-->erg'.$myconn['n']."\r\n";
        }
}
?>
      </div>
    </div>
  </div>
</div>
<style>
.starterg {cursor:pointer;}
.enderg {cursor:pointer;}
.myergasia {cursor:pointer;}

.starterg > rect {
  fill:#337AB7 !important;
  stroke:#245580 !important;
  stroke-width:2px !important;   
}  
.starterg > .label {
  color:white !important;
} 

.enderg > rect {
  fill:#47a447 !important;
  stroke:#2e6b2e !important;
  stroke-width:2px !important;   
}  
.enderg > .label {
  color:white !important;
} 
</style>

<?php if (count($ergasies)>0) {?>  

<script  src="js/mermaid-8.3.1/mermaid.min.js"></script>
<script>

  function ergas_click() {
    ergasid=$(this).attr('id');
    //console.log(ergasid);
    if (ergasid.length<=3) return;
    ergasid=ergasid.substring(3);
    //console.log(ergasid);
    window.location.href='admin-production-ergasies-item.php?id=' + ergasid;    
  }

  var gks_callback = function(){
    jQuery('.myergasia').click(ergas_click);
    jQuery('.starterg').click(ergas_click);
    jQuery('.enderg').click(ergas_click);   
  }
  
  var config = {
    startOnLoad:true,
    theme: 'default', //default forest dark neutral
    flowchart:{
      useMaxWidth:true,
      htmlLabels:true,
      curve:'basis', //basis linear cardinal
      //width: '100px',
    },
    mermaid: {
      callback:gks_callback,
    },
    securityLevel:'loose',
  };
  
  mermaid.initialize(config);  
  
</script>
<?php } ?>  

<?php

//print '<pre>';
//print_r($data);
//print_r($ergasies);
//print_r($mustodone);
//print '</pre>';
?>
<style>
  
.div_ergas_child {
 padding-left: 20px; 
}  
.overitem_hover {
  background-color:yellow;  
}
  
</style>



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>


  
  
});
</script>


<?php
//db_close();
include_once('_my_footer_admin.php');




