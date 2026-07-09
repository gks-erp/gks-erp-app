<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();




$my_page_title=gks_lang('Χάρτης Λογιστικής');
$nav_active_array=array('accounting','accounting_map');


db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_pay_map','view',0);
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





$company=array();
$company_subs=array();
$sql="select id_company, company_title,company_color from gks_company order by company_sortorder,company_title";
$result = $db_link->query($sql); 
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');
while ($row = $result->fetch_assoc()) {
  $company[]=array('id'=> $row['id_company'], 'descr' => $row['company_title'], 'color' => $row['company_color']);
  $company_subs[]=array('id'=> 'kentriko'.$row['id_company'], 'descr' => gks_lang('Κεντρικό'), 'cid' => $row['id_company'], 'color' => $row['company_color']);
}

$sql="SELECT gks_company_subs.id_company_sub, gks_company_subs.company_id, gks_company_subs.company_sub_title,gks_company_subs.company_sub_color
FROM gks_company_subs LEFT JOIN gks_company ON gks_company_subs.company_id = gks_company.id_company
WHERE (((gks_company.id_company) Is Not Null))
order by gks_company_subs.company_sub_sortorder, gks_company_subs.company_sub_title";
$result = $db_link->query($sql); 
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');
while ($row = $result->fetch_assoc()) {
  $company_subs[]=array('id'=> $row['id_company_sub'], 'descr' => $row['company_sub_title'], 'cid' => $row['company_id'], 'color' => $row['company_sub_color']);
}


$journal=array();
$sql="SELECT gks_acc_journal.id_acc_journal, gks_acc_journal.acc_journal_descr,
gks_acc_journal.company_id,gks_acc_journal.company_sub_id
FROM gks_acc_journal LEFT JOIN gks_company ON gks_acc_journal.company_id = gks_company.id_company
WHERE (((gks_company.id_company) Is Not Null))
ORDER BY gks_acc_journal.acc_journal_descr;";
$result = $db_link->query($sql); 
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');
while ($row = $result->fetch_assoc()) {
  $journal[]=array('id'=> $row['id_acc_journal'], 'descr' => $row['acc_journal_descr'], 'cid' => $row['company_id'], 'csid' => $row['company_sub_id']);
}

$seires=array();
$sql="SELECT gks_acc_seires.id_acc_seira, gks_acc_seires.seira_code, gks_acc_seires.seira_descr, gks_acc_seires.acc_journal_id
FROM gks_acc_seires LEFT JOIN gks_acc_journal ON gks_acc_seires.acc_journal_id = gks_acc_journal.id_acc_journal
WHERE (((gks_acc_journal.id_acc_journal) Is Not Null))
ORDER BY gks_acc_seires.seira_descr";
$result = $db_link->query($sql); 
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');
while ($row = $result->fetch_assoc()) {
  $seires[]=array('id'=> $row['id_acc_seira'], 'descr' => $row['seira_descr'].' ('.$row['seira_code'].')', 'jid' => $row['acc_journal_id']);
}





//print '<pre>';
//echo color_inverse('#000000');
//echo color_inverse('#bcdf0c');
//print '</pre>';
//die();

//https://mermaidjs.github.io/#/mermaidAPI
//https://cdnjs.cloudflare.com/ajax/libs/mermaid/8.3.1/mermaid.min.js
//https://medium.com/better-programming/mermaid-create-charts-and-diagrams-with-markdown-88a9e639ab14

?>
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="mermaid" style="text-align: center;">
<?php if (count($company)==0) {
        echo '<p>'.gks_lang('Δεν βρέθηκαν δεδομένα').'</p>';
 } else {?>          
        graph LR
<?php
        foreach ($company as $vc) {
          echo 'company'.$vc['id'].'("'.gks_lang('Εταιρεία').'<br>'.$vc['descr'].'")'."\r\n";
          echo 'class company'.$vc['id'].' company'."\r\n";
          echo 'style company'.$vc['id'].' fill:'.$vc['color'].',color:'.color_inverse($vc['color'])."\r\n";
        } 
        foreach ($company_subs as $vcs) {
          if (startwith($vcs['id'],'kentriko')) {
            echo 'companysub'.$vcs['id'].'("'.$vcs['descr'].'")'."\r\n";
            echo 'class companysub'.$vcs['id'].' companysubkentriko'."\r\n";
          } else {
            echo 'companysub'.$vcs['id'].'("'.gks_lang('Υποκατάστημα').'<br>'.$vcs['descr'].'")'."\r\n";
            echo 'class companysub'.$vcs['id'].' companysub'."\r\n";
          }
          echo 'style companysub'.$vcs['id'].' fill:'.$vcs['color'].',color:'.color_inverse($vcs['color'])."\r\n";
          echo 'company'.$vcs['cid'].'-->companysub'.$vcs['id']."\r\n";
        } 
        foreach ($journal as $vj) {
          echo 'journal'.$vj['id'].'("'.gks_lang('Ημερολόγιο').'<br>'.$vj['descr'].'")'."\r\n";
          echo 'class journal'.$vj['id'].' journal'."\r\n";
          if ($vj['csid'] == 0) {
            echo 'companysubkentriko'.$vj['cid'].'-->journal'.$vj['id']."\r\n";
          } else {
            echo 'companysub'.$vj['csid'].'-->journal'.$vj['id']."\r\n";
          }
        } 
        foreach ($seires as $vs) {
          echo 'seira'.$vs['id'].'("'.gks_lang('Σειρά').'<br>'.$vs['descr'].'")'."\r\n";
          echo 'class seira'.$vs['id'].' seira'."\r\n";
          echo 'journal'.$vs['jid'].'-->seira'.$vs['id']."\r\n";
        } 

        
}
?>
      </div>
    </div>
  </div>
</div>
<style>
.company {cursor:pointer;}
.companysubkentriko {cursor:pointer;}
.companysub {cursor:pointer;}
.journal {cursor:pointer;}
.seira {cursor:pointer;}


.journal > rect {
  fill:#337AB7 !important;
  stroke:#245580 !important;
  stroke-width:2px !important;   
}  
.journal > .label {
  color:white !important;
} 

.seira > rect {
  fill:#47a447 !important;
  stroke:#2e6b2e !important;
  stroke-width:2px !important;   
}  
.seira > .label {
  color:white !important;
} 
</style>

<?php if (count($company)>0) {?>  

<script  src="js/mermaid-8.3.1/mermaid.min.js"></script>
<script>
  function company_click() {
    myid=$(this).attr('id');
    //console.log(myid);
    if (myid.length<=3) return;
    myid=myid.substring(7);
    //console.log(myid);
    window.location.href='admin-company-item.php?id=' + myid;    
  }
  function companysubkentriko_click() {
    myid=$(this).attr('id');
    //console.log(myid);
    if (myid.length<=3) return;
    myid=myid.substring(18);
    //console.log(myid);
    window.location.href='admin-company-item.php?id=' + myid;    
  }
  function companysub_click() {
    myid=$(this).attr('id');
    //console.log(myid);
    if (myid.length<=3) return;
    myid=myid.substring(10);
    //console.log(myid);
    window.location.href='admin-company-sub-item.php?id=' + myid;    
  }
  function journal_click() {
    myid=$(this).attr('id');
    //console.log(myid);
    if (myid.length<=3) return;
    myid=myid.substring(7);
    //console.log(myid);
    window.location.href='admin-acc_journal-item.php?id=' + myid;    
  }
  function seira_click() {
    myid=$(this).attr('id');
    //console.log(myid);
    if (myid.length<=3) return;
    myid=myid.substring(5);
    //console.log(myid);
    window.location.href='admin-acc_seires-item.php?id=' + myid;    
  }

  var gks_callback = function(){
    jQuery('.company').click(company_click);
    jQuery('.companysubkentriko').click(companysubkentriko_click);
    jQuery('.companysub').click(companysub_click);   
    jQuery('.journal').click(journal_click);   
    jQuery('.seira').click(seira_click);   
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




