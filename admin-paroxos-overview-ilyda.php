<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$curr_paroxos_id=8;
//admin-paroxos-overview-ilyda.php

/**

admin-acc-inv.php?fdate_add=-1&faade_send_date=103&faade_mark=-100&fparoxos=8
admin-acc-pay.php?fdate_add=-1&faade_send_date=103&faade_mark=-100&fparoxos=8
admin-whi-mov.php?fdate_add=-1&faade_send_date=103&faade_mark=-100&fparoxos=8


**/

$my_page_title=gks_lang('Επισκόπηση Παρόχου ΙΛΥΔΑ');
$nav_active_array=array('accounting','accounting_paroxos_overview','accounting_paroxos_overview_ilyda');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks__paroxos_overview_ilyda','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}


$template_data=array(
  'enable'=>false,
  
);


$data=[];
$sql = "SELECT id_company, company_title, company_eponimia
FROM gks_company
WHERE company_disable=0
ORDER BY company_sortorder, company_title, company_eponimia";
$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');
while ($row = $result->fetch_assoc()) {
  $data[$row['id_company']]=array(
    'id'=>$row['id_company'],
    'title'=>$row['company_title'],
    'eponimia'=>$row['company_eponimia'],
    'enable'=>false,
    'subs'=>[],
  );
  $data[$row['id_company']]['subs'][0]=array(
    'id_sub'=>0,
    'title_sub'=>gks_lang('Κεντρικό'),
    'eponimia_sub'=>gks_lang('Κεντρικό'),
    'd'=>$template_data,
  );
}

$sql="SELECT id_company_sub, company_id, company_sub_title, company_sub_eponimia
FROM gks_company_subs
WHERE company_sub_disable=0
ORDER BY company_sub_sortorder, company_sub_title, company_sub_eponimia;";
$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');
while ($row = $result->fetch_assoc()) {
  if (isset($data[$row['company_id']])) {
    $data[$row['company_id']]['subs'][$row['id_company_sub']]=array(
      'id_sub'=>$row['id_company_sub'],
      'title_sub'=>$row['company_sub_title'],
      'eponimia_sub'=>$row['company_sub_eponimia'],
      'd'=>$template_data,
    );
  }
}

$sql="SELECT gks_company_paroxos.*, gks_company_subs.company_id AS company_id_from_sub
FROM gks_company_paroxos 
LEFT JOIN gks_company_subs ON gks_company_paroxos.company_sub_id = gks_company_subs.id_company_sub
WHERE gks_company_paroxos.aade_paroxos_id=".$curr_paroxos_id;
$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');
while ($row = $result->fetch_assoc()) {
  $cid=-1; $sid=-1;
  if ($row['company_id']>0) {
    $cid=$row['company_id'];
    $sid=0; 
  } else {
    $cid=$row['company_id_from_sub'];
    $sid=$row['company_sub_id'];       
  }
  if (isset($data[$cid]['subs'][$sid])) {
    $data[$cid]['subs'][$sid]['d']['enable']=true;    
    $data[$cid]['subs'][$sid]['d']['branch']=$row['paroxos_branch'];    
    $data[$cid]['subs'][$sid]['d']['username']=$row['pc_username'];    
    $data[$cid]['subs'][$sid]['d']['send']=$row['paroxos_send'];    
    $data[$cid]['subs'][$sid]['d']['live']=$row['paroxos_mydata_live'];    
  }
}

$sql="SELECT gks_acc_inv.company_id, gks_acc_inv.company_sub_id, 
Count(gks_acc_inv.id_acc_inv) AS cc, 
gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, 
gks_acc_eidi_parastatikon.eidos_parastatikou_descr, 
gks_acc_eidi_parastatikon.sortorder
FROM (gks_acc_inv 
LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
WHERE gks_acc_inv.aade_paroxos_id=".$curr_paroxos_id."
AND gks_acc_inv.aade_send_date Is Not Null
GROUP BY gks_acc_inv.company_id, gks_acc_inv.company_sub_id, gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_acc_eidi_parastatikon.eidos_parastatikou_descr, gks_acc_eidi_parastatikon.sortorder
union
SELECT gks_whi_mov.company_id, gks_whi_mov.company_sub_id, 
Count(gks_whi_mov.id_whi_mov) AS cc, 
gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, 
gks_acc_eidi_parastatikon.eidos_parastatikou_descr, 
gks_acc_eidi_parastatikon.sortorder
FROM (gks_whi_mov 
LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
WHERE gks_whi_mov.aade_paroxos_id=".$curr_paroxos_id."
AND gks_whi_mov.aade_send_date Is Not Null
GROUP BY gks_whi_mov.company_id, gks_whi_mov.company_sub_id, gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_acc_eidi_parastatikon.eidos_parastatikou_descr, gks_acc_eidi_parastatikon.sortorder
union
SELECT gks_acc_pay.company_id, gks_acc_pay.company_sub_id, 
Count(gks_acc_pay.id_acc_pay) AS cc, 
gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, 
gks_acc_eidi_parastatikon.eidos_parastatikou_descr, 
gks_acc_eidi_parastatikon.sortorder
FROM (gks_acc_pay 
LEFT JOIN gks_acc_journal ON gks_acc_pay.pay_acc_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
WHERE gks_acc_pay.aade_paroxos_id=".$curr_paroxos_id."
AND gks_acc_pay.aade_send_date Is Not Null
GROUP BY gks_acc_pay.company_id, gks_acc_pay.company_sub_id, gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_acc_eidi_parastatikon.eidos_parastatikou_descr, gks_acc_eidi_parastatikon.sortorder
ORDER BY cc desc,sortorder;";
$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');
$invs=[];$total_invs=[];
while ($row = $result->fetch_assoc()) {
  $invs[]=$row;
  $data[$row['company_id']]['subs'][$row['company_sub_id']]['d']['enable']=true;
  if (isset($total_invs[$row['id_acc_eidos_parastatikou']])==false) {
    $total_invs[$row['id_acc_eidos_parastatikou']]=array(
      'id'=>$row['id_acc_eidos_parastatikou'],
      'descr'=>$row['eidos_parastatikou_descr'],
      'cc'=>0,
    );
  }
  $total_invs[$row['id_acc_eidos_parastatikou']]['cc']+=$row['cc'];
}

function mysort_array($a, $b) {
  if ($a['cc'] > $b['cc']) return -1;
  if ($a['cc'] < $b['cc']) return 1;
  return 0;
}
usort($total_invs, "mysort_array");

$sql="SELECT gks_acc_inv.company_id, gks_acc_inv.company_sub_id, 
Count(gks_acc_inv.id_acc_inv) AS cc, 
gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, 
gks_acc_eidi_parastatikon.eidos_parastatikou_descr, 
gks_acc_eidi_parastatikon.sortorder
FROM (gks_acc_inv 
LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
WHERE gks_acc_inv.aade_paroxos_id=".$curr_paroxos_id."
AND gks_acc_inv.aade_send_date Is Not Null
and (gks_acc_inv.aade_invoicemark Is Null or gks_acc_inv.aade_invoicemark='')
GROUP BY gks_acc_inv.company_id, gks_acc_inv.company_sub_id, gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_acc_eidi_parastatikon.eidos_parastatikou_descr, gks_acc_eidi_parastatikon.sortorder
union
SELECT gks_whi_mov.company_id, gks_whi_mov.company_sub_id, 
Count(gks_whi_mov.id_whi_mov) AS cc, 
gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, 
gks_acc_eidi_parastatikon.eidos_parastatikou_descr, 
gks_acc_eidi_parastatikon.sortorder
FROM (gks_whi_mov 
LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
WHERE gks_whi_mov.aade_paroxos_id=".$curr_paroxos_id."
AND gks_whi_mov.aade_send_date Is Not Null
and (gks_whi_mov.aade_invoicemark Is Null or gks_whi_mov.aade_invoicemark='')
GROUP BY gks_whi_mov.company_id, gks_whi_mov.company_sub_id, gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_acc_eidi_parastatikon.eidos_parastatikou_descr, gks_acc_eidi_parastatikon.sortorder
union
SELECT gks_acc_pay.company_id, gks_acc_pay.company_sub_id, 
Count(gks_acc_pay.id_acc_pay) AS cc, 
gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, 
gks_acc_eidi_parastatikon.eidos_parastatikou_descr, 
gks_acc_eidi_parastatikon.sortorder
FROM (gks_acc_pay 
LEFT JOIN gks_acc_journal ON gks_acc_pay.pay_acc_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
WHERE gks_acc_pay.aade_paroxos_id=".$curr_paroxos_id."
AND gks_acc_pay.aade_send_date Is Not Null
and (gks_acc_pay.aade_invoicemark Is Null or gks_acc_pay.aade_invoicemark='')
GROUP BY gks_acc_pay.company_id, gks_acc_pay.company_sub_id, gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_acc_eidi_parastatikon.eidos_parastatikou_descr, gks_acc_eidi_parastatikon.sortorder

ORDER BY cc desc,sortorder";
$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');
$invs_not_mark=[];$total_invs_not_mark=[];
while ($row = $result->fetch_assoc()) {
  $invs_not_mark[]=$row;
  if (isset($total_invs_not_mark[$row['id_acc_eidos_parastatikou']])==false) {
    $total_invs_not_mark[$row['id_acc_eidos_parastatikou']]=array(
      'id'=>$row['id_acc_eidos_parastatikou'],
      'descr'=>$row['eidos_parastatikou_descr'],
      'cc'=>0,
    );
  }
  $total_invs_not_mark[$row['id_acc_eidos_parastatikou']]['cc']+=$row['cc'];
}
usort($total_invs_not_mark, "mysort_array");



$sql="SELECT signature_status, Count(id_paroxos_signature) AS cc
FROM gks_paroxos_signature
WHERE aade_paroxos_id=".$curr_paroxos_id."
GROUP BY gks_paroxos_signature.signature_status
order by cc desc";
$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');
$signatures=[];
while ($row = $result->fetch_assoc()) {
  $signatures[]=$row;
}

foreach ($data as &$comp) {
  foreach ($comp['subs'] as &$scomp) {
    if ($scomp['d']['enable']) $comp['enable']=true;
  }
  unset($scomp);
}
unset($comp);


$sql="SELECT gks_acc_inv.company_id, gks_acc_inv.company_sub_id, 
Count(gks_acc_inv.id_acc_inv) AS cc, 
gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, 
gks_acc_eidi_parastatikon.eidos_parastatikou_descr, 
gks_acc_eidi_parastatikon.sortorder
FROM (gks_acc_inv 
LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
WHERE gks_acc_inv.aade_paroxos_id=0 and paroxos_tf1_url_has=1
AND gks_acc_inv.aade_send_date Is Null
and (gks_acc_inv.aade_invoicemark Is Null or gks_acc_inv.aade_invoicemark='')
GROUP BY gks_acc_inv.company_id, gks_acc_inv.company_sub_id, gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_acc_eidi_parastatikon.eidos_parastatikou_descr, gks_acc_eidi_parastatikon.sortorder
union
SELECT gks_whi_mov.company_id, gks_whi_mov.company_sub_id, 
Count(gks_whi_mov.id_whi_mov) AS cc, 
gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, 
gks_acc_eidi_parastatikon.eidos_parastatikou_descr, 
gks_acc_eidi_parastatikon.sortorder
FROM (gks_whi_mov 
LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
WHERE gks_whi_mov.aade_paroxos_id=0 and paroxos_tf1_url_has=1
AND gks_whi_mov.aade_send_date Is Null
and (gks_whi_mov.aade_invoicemark Is Null or gks_whi_mov.aade_invoicemark='')
GROUP BY gks_whi_mov.company_id, gks_whi_mov.company_sub_id, gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_acc_eidi_parastatikon.eidos_parastatikou_descr, gks_acc_eidi_parastatikon.sortorder
union
SELECT gks_acc_pay.company_id, gks_acc_pay.company_sub_id, 
Count(gks_acc_pay.id_acc_pay) AS cc, 
gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, 
gks_acc_eidi_parastatikon.eidos_parastatikou_descr, 
gks_acc_eidi_parastatikon.sortorder
FROM (gks_acc_pay 
LEFT JOIN gks_acc_journal ON gks_acc_pay.pay_acc_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
WHERE gks_acc_pay.aade_paroxos_id=0 and paroxos_tf1_url_has=1
AND gks_acc_pay.aade_send_date Is Null
and (gks_acc_pay.aade_invoicemark Is Null or gks_acc_pay.aade_invoicemark='')
GROUP BY gks_acc_pay.company_id, gks_acc_pay.company_sub_id, gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou, gks_acc_eidi_parastatikon.eidos_parastatikou_descr, gks_acc_eidi_parastatikon.sortorder

ORDER BY cc desc,sortorder";
$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');
$invs_tf1=[];$total_invs_tf1=[];
while ($row = $result->fetch_assoc()) {
  $invs_tf1[]=$row;
  if (isset($total_invs_tf1[$row['id_acc_eidos_parastatikou']])==false) {
    $total_invs_tf1[$row['id_acc_eidos_parastatikou']]=array(
      'id'=>$row['id_acc_eidos_parastatikou'],
      'descr'=>$row['eidos_parastatikou_descr'],
      'cc'=>0,
    );
  }
  $total_invs_tf1[$row['id_acc_eidos_parastatikou']]['cc']+=$row['cc'];
}
usort($total_invs_tf1, "mysort_array");

//print '<pre>';print_r($data);die();



include_once('_my_header_admin.php');
?>
<style>
.mytext {
  font-size: 0.875rem;
  padding-top: 4px;  
}    
.gks_col1, .gks_col2 {
  border-right: 1px solid lightgray;
}
#paroxos_get_invoice_pending_results .fa-hourglass,
#paroxos_tf1_get_keys_results .fa-hourglass {
  font-size: 20px;
  position: absolute;
  color: gray;
}

.paroxos_get_docstate {
  margin-top: 6px;
  color: #fff;
  background-color: #007bff;
  border-color: #007bff;
  padding: 1px 6px;
  border-radius: 6px;
}
.tf1_local_ACTIVE {
  padding: 2px 10px;
  border-radius: 15px;
  border-width: 0px;
  background-color: green;
  color: white;
  font-weight: bold;
}
.aade_xml_response_error {
  font-size: 0.875rem;
}
</style>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Συνολικά');?>
        </div>
        <div class="card-body"<?php echo gks_card_body('total');?>> 
          <div class="row">
            <div class="col-md-4 gks_col1">
              <div class="form-group row">
                <div class="col-md-12 text-center"><b><?php echo gks_lang('Υπογραφές για POS');?></b></div>
              </div>
              
<table class="table table-sm table-responsive11 table-striped table-bordered gkstable border="0" cellspacing="0" cellpadding="5" align="center">
  <thead>
    <tr >	
      <th nowrap class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'>#</th>
      <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Κατάσταση');?></th> 
      <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Πλήθος');?></th> 
    </tr>
</thead>
<tbody>
<?php if (count($signatures)==0) echo '<tr><td class="mytdcm" colspan="3">'.gks_lang('Δεν βρέθηκαν καταχωρήσεις').'</td></tr>';?>
<?php $i=0;$mysum=0;foreach ($signatures as $signitem) {$i++;$mysum+=$signitem['cc'];?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" class="mytdcm aa" nowrap><?php echo ($i);?></th>
    <td class="mytdcml"><span class="eftpos_sign_status eftpos_sign_status_<?php echo $signitem['signature_status'];?>"><?php echo gks_paroxos_signature_status_descr($signitem['signature_status']);?></span></td>
    <td class="mytdcm" nowrap ><?php echo myNumberFormat($signitem['cc']);?></td>
  </tr>
<?php } ?>
</tbody> 
<?php if (count($signatures)>1) {?>
<tfoot>
  <tr class="table-warning">
    <td class="bottomsums mytdcml" colspan="2" nowrap><?php echo gks_lang('Σύνολο');?></td>
    <td class="bottomsums mytdcm" colspan="2" nowrap><?php echo myNumberFormat($mysum);?></td>
  </tr>
</tfoot> 
<?php } ?>              
</table>
              
            </div>
            <div class="col-md-4 gks_col2">
              <div class="form-group row">
                <div class="col-md-12 text-center"><b><?php echo gks_lang('Παραστατικά');?></b></div>
              </div>
<table class="table table-sm table-responsive11 table-striped table-bordered gkstable border="0" cellspacing="0" cellpadding="5" align="center">
  <thead>
    <tr >	
      <th nowrap class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'>#</th>
      <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Τύπος');?></th> 
      <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Πλήθος');?></th> 
    </tr>
</thead>
<tbody>
<?php if (count($total_invs)==0) echo '<tr><td class="mytdcm" colspan="3">'.gks_lang('Δεν βρέθηκαν καταχωρήσεις').'</td></tr>';?>
<?php $i=0;$mysum=0;foreach ($total_invs as $inv) {$i++;$mysum+=$inv['cc'];?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" class="mytdcm aa" nowrap><?php echo ($i);?></th>
    <td class="mytdcml"><?php echo $inv['descr'];?></td>
    <td class="mytdcm" nowrap ><?php echo myNumberFormat($inv['cc']);?></td>
  </tr>
<?php } ?>
</tbody> 
<?php if (count($total_invs)>0) {?>
<tfoot>
  <tr class="table-warning">
    <td class="bottomsums mytdcml" colspan="2" nowrap><?php echo gks_lang('Σύνολο');?></td>
    <td class="bottomsums mytdcm" colspan="2" nowrap><?php echo myNumberFormat($mysum);?></td>
  </tr>
</tfoot> 
<?php } ?>              
</table>                

            </div>
            <div class="col-md-4 gks_col3">
              <div class="form-group row">
                <div class="col-md-12 text-center"><b><?php echo gks_lang('Παραστατικά χωρίς ΜΑΡΚ');?></b></div>
                <div class="col-md-12 text-center1 small" id="invoices_no_mark_text">
                </div>
              </div>
            
<table class="table table-sm table-responsive11 table-striped table-bordered gkstable border="0" cellspacing="0" cellpadding="5" align="center">
  <thead>
    <tr >	
      <th nowrap class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'>#</th>
      <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Τύπος');?></th> 
      <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Πλήθος');?></th> 
    </tr>
</thead>
<tbody>
<?php if (count($total_invs_not_mark)==0) echo '<tr><td class="mytdcm" colspan="3">'.gks_lang('Δεν βρέθηκαν καταχωρήσεις').'</td></tr>';?>
<?php $i=0;$mysum=0;foreach ($total_invs_not_mark as $inv) {$i++;$mysum+=$inv['cc'];?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" class="mytdcm aa" nowrap><?php echo ($i);?></th>
    <td class="mytdcml"><?php echo $inv['descr'];?></td>
    <td class="mytdcm" nowrap ><?php echo myNumberFormat($inv['cc']);?></td>
  </tr>
<?php } ?>
</tbody> 
<?php if (count($total_invs_not_mark)>1) {?>
<tfoot>
  <tr class="table-warning">
    <td class="bottomsums mytdcml" colspan="2" nowrap><?php echo gks_lang('Σύνολο');?></td>
    <td class="bottomsums mytdcm" colspan="2" nowrap><?php echo myNumberFormat($mysum);?></td>
  </tr>
</tfoot> 
<?php } ?>              
</table>
              
              <div class="form-group row">
                <div class="col-md-12 text-center"><?php echo gks_lang('Προβολή');?></div>
                <div class="col-md-12 text-center">
                  <a href="admin-acc-inv.php?fdate_add=-1&faade_send_date=103&faade_mark=-100&fparoxos=<?php echo $curr_paroxos_id;?>" class="btn btn-primary btn-sm"><?php echo gks_lang('Παραστατικά');?></a>
                  <a href="admin-acc-pay.php?fdate_add=-1&faade_send_date=103&faade_mark=-100&fparoxos=<?php echo $curr_paroxos_id;?>" class="btn btn-primary btn-sm"><?php echo gks_lang('Πληρωμές');?></a>
                  <a href="admin-whi-mov.php?fdate_add=-1&faade_send_date=103&faade_mark=-100&fparoxos=<?php echo $curr_paroxos_id;?>" class="btn btn-primary btn-sm"><?php echo gks_lang('Δελτία');?></a>
                </div>
              </div>                      
              <div style="height: 1px;width: 50%;background-color: lightgray;margin: 16px auto;"></div>
              <div class="form-group row">
                <div class="col-md-12 text-center"><b><?php echo gks_lang('Σε αναμονή από πάροχο προς ΑΑΔΕ');?></b></div>
              </div>
              <div class="form-group row">
                <label class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πλήθος');?>:</label>
                <div class="col-md-6 mytext">
                  <div id="paroxos_get_invoice_pending_results">--</div>
                </div>
              </div>   
              <div class="form-group row">
                <div class="col-md-12 text-center">
                  <button id="paroxos_get_invoice_pending" class="btn btn-sm btn-primary"><i class="fas fa-sync"></i> <?php echo gks_lang('Λήψη λίστας από πάροχο');?></button>
                </div>
              </div>
              <div class="form-group row" id="paroxos_get_invoice_pending_buttons" style="display:none;">
                <div class="col-md-12 text-center">
                  <a id="paroxos_get_invoice_pending_button1" href="#" class="btn btn-primary btn-sm"><?php echo gks_lang('Προβολή Raw Data');?></a>
                  <a id="paroxos_get_invoice_pending_button2" href="admin-paroxos-overview-ilyda-invoice-pending.php" class="btn btn-primary btn-sm"><?php echo gks_lang('Προβολή Λίστας');?></a>
                </div>
              </div> 
              <div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>
              <div class="form-group row">
                <div class="col-md-12 text-center"><b><?php echo gks_lang('Σφάλμα μετάδοσης παραστατικών');?></b></div>
              </div>
              <div class="form-group row">
                <div class="col-md-12 text-center">
                  <span class="aade_xml_response_error tooltipster"><?php echo gks_lang('Transmission Failure 1 (TF-1)');?></span>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-12 text-center1 small" id="invoices_tf1_text">
                </div>
              </div>              

              
<table class="table table-sm table-responsive11 table-striped table-bordered gkstable border="0" cellspacing="0" cellpadding="5" align="center">
  <thead>
    <tr >	
      <th nowrap class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'>#</th>
      <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Τύπος');?></th> 
      <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Πλήθος');?></th> 
    </tr>
</thead>
<tbody>
<?php if (count($total_invs_tf1)==0) echo '<tr><td class="mytdcm" colspan="3">'.gks_lang('Δεν βρέθηκαν καταχωρήσεις').'</td></tr>';?>
<?php $i=0;$mysum=0;foreach ($total_invs_tf1 as $inv) {$i++;$mysum+=$inv['cc'];?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" class="mytdcm aa" nowrap><?php echo ($i);?></th>
    <td class="mytdcml"><?php echo $inv['descr'];?></td>
    <td class="mytdcm" nowrap ><?php echo myNumberFormat($inv['cc']);?></td>
  </tr>
<?php } ?>
</tbody> 
<?php if (count($total_invs_tf1)>1) {?>
<tfoot>
  <tr class="table-warning">
    <td class="bottomsums mytdcml" colspan="2" nowrap><?php echo gks_lang('Σύνολο');?></td>
    <td class="bottomsums mytdcm" colspan="2" nowrap><?php echo myNumberFormat($mysum);?></td>
  </tr>
</tfoot> 
<?php } ?>              
</table>
               
              <div class="form-group row">
                <div class="col-md-12 text-center"><?php echo gks_lang('Προβολή');?></div>
                <div class="col-md-12 text-center">
                  <a href="admin-acc-inv.php?fdate_add=-1&faade_send_date=102&faade_mark=-100&fparoxos=0&paroxos_tf1_url_has=1" class="btn btn-primary btn-sm"><?php echo gks_lang('Παραστατικά');?></a>
                  <a href="admin-acc-pay.php?fdate_add=-1&faade_send_date=102&faade_mark=-100&fparoxos=0&paroxos_tf1_url_has=1" class="btn btn-primary btn-sm"><?php echo gks_lang('Πληρωμές');?></a>
                  <a href="admin-whi-mov.php?fdate_add=-1&faade_send_date=102&faade_mark=-100&fparoxos=0&paroxos_tf1_url_has=1" class="btn btn-primary btn-sm"><?php echo gks_lang('Δελτία');?></a>
                </div>
              </div>                      
              <div style="height: 1px;width: 50%;background-color: lightgray;margin: 16px auto;"></div>
              <div class="form-group row">
                <div class="col-md-12 text-center"><b><?php echo gks_lang('Διαχείριση κλειδιών');?></b></div>
              </div>              
              
              <div class="form-group row">
                <label class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κλειδιά');?>:</label>
                <div class="col-md-6 mytext">
                  <div id="paroxos_tf1_get_keys_results">--</div>
                </div>
              </div>
              <div class="form-group row">
                <div class="col-md-12 text-center">
                  <div style="margin:10px;">
                    <button id="paroxos_tf1_get_keys" class="btn btn-sm btn-primary"><i class="fas fa-sync"></i> <?php echo gks_lang('Λήψη κλειδιών από πάροχο');?></button>
                  </div>
                  <div style="margin:10px;">
                    <button id="paroxos_tf1_create_keys" class="btn btn-sm btn-primary"><i class="fas fa-plus-square"></i> <?php echo gks_lang('Δημιουργία και λήψη νέων κλειδιών από πάροχο');?></button>
                  </div>
                  <div style="margin:10px;">
                    <a href="admin-paroxos-overview-ilyda-tf1-keys.php" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i> <?php echo gks_lang('Προβολή λίστας κλειδιών');?></a>
                  </div>
                </div>
              </div>              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php foreach ($data as $comp) {
  if ($comp['enable']) {
    ?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo $comp['title']?>
        </div>
        <div class="card-body"<?php echo gks_card_body('c'.$comp['id']);?>> 
<?php foreach ($comp['subs'] as $scomp) {
        if ($scomp['d']['enable']) {?>
          <div class="row">
            <div class="col-md-12">
              <div class="card gks_card_expand">
                <div class="card-header" style="text-align:center">
                  <?php echo $scomp['title_sub']?>
                </div>
                <div class="card-body"<?php echo gks_card_body('s'.$scomp['id_sub']);?>>    
                  <div class="row">
                    <div class="col-md-4 gks_col1">
                      <div class="form-group row">
                        <label class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αποστολή δεδομένων σε πάροχο');?>:</label>
                        <div class="col-md-6 mytext"><?php echo myimg010($scomp['d']['send']);?></div>
                      </div>
                      <div class="form-group row">
                        <label class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός Εγκατάστασης');?>:</label>
                        <div class="col-md-6 mytext"><?php echo $scomp['d']['branch'];?></div>
                      </div>
                      <div class="form-group row">
                        <label class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όνομα Χρήστη');?>:</label>
                        <div class="col-md-6 mytext"><?php echo $scomp['d']['username'];?></div>
                      </div>
                      <div class="form-group row">
                        <label class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πραγματική αποστολή');?>:</label>
                        <div class="col-md-6 mytext"><?php echo myimg010($scomp['d']['live']);?></div>
                      </div>
                    </div>  
                    <div class="col-md-4 gks_col2">
                      <div class="form-group row">
                        <div class="col-md-12 text-center"><b><?php echo gks_lang('Παραστατικά');?></b></div>
                      </div>
<?php
$invs_curr=[];
foreach ($invs as $inv) {
  if ($inv['company_id']==$comp['id'] and $inv['company_sub_id']==$scomp['id_sub']) {
    $invs_curr[]=$inv;
  }
}
usort($invs_curr, "mysort_array");
?>                      
<table class="table table-sm table-responsive11 table-striped table-bordered gkstable border="0" cellspacing="0" cellpadding="5" align="center">
  <thead>
    <tr >	
      <th nowrap class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'>#</th>
      <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Τύπος');?></th> 
      <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Πλήθος');?></th> 
    </tr>
</thead>
<tbody>
<?php if (count($invs_curr)==0) echo '<tr><td class="mytdcm" colspan="3">'.gks_lang('Δεν βρέθηκαν καταχωρήσεις').'</td></tr>';?>
<?php $i=0;$mysum=0;foreach ($invs_curr as $inv) {$i++;$mysum+=$inv['cc'];?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" class="mytdcm aa" nowrap><?php echo ($i);?></th>
    <td class="mytdcml"><?php echo $inv['eidos_parastatikou_descr'];?></td>
    <td class="mytdcm" nowrap ><?php echo myNumberFormat($inv['cc']);?></td>
  </tr>
<?php } ?>
</tbody> 
<?php if (count($invs_curr)>1) {?>
<tfoot>
  <tr class="table-warning">
    <td class="bottomsums mytdcml" colspan="2" nowrap><?php echo gks_lang('Σύνολο');?></td>
    <td class="bottomsums mytdcm" colspan="2" nowrap><?php echo myNumberFormat($mysum);?></td>
  </tr>
</tfoot> 
<?php } ?>              
</table>
                                           
                    </div>  
                    <div class="col-md-4 gks_col3">
                      <div class="form-group row">
                        <div class="col-md-12 text-center"><b><?php echo gks_lang('Παραστατικά χωρίς ΜΑΡΚ');?></b></div>
                      </div>

<?php
$invs_curr=[];
foreach ($invs_not_mark as $inv) {
  if ($inv['company_id']==$comp['id'] and $inv['company_sub_id']==$scomp['id_sub']) {
    $invs_curr[]=$inv;
  }
}
usort($invs_curr, "mysort_array");
?>                      
<table class="table table-sm table-responsive11 table-striped table-bordered gkstable border="0" cellspacing="0" cellpadding="5" align="center">
  <thead>
    <tr >	
      <th nowrap class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'>#</th>
      <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Τύπος');?></th> 
      <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Πλήθος');?></th> 
    </tr>
</thead>
<tbody>
<?php if (count($invs_curr)==0) echo '<tr><td class="mytdcm" colspan="3">'.gks_lang('Δεν βρέθηκαν καταχωρήσεις').'</td></tr>';?>
<?php $i=0;$mysum=0;foreach ($invs_curr as $inv) {$i++;$mysum+=$inv['cc'];?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" class="mytdcm aa" nowrap><?php echo ($i);?></th>
    <td class="mytdcml"><?php echo $inv['eidos_parastatikou_descr'];?></td>
    <td class="mytdcm" nowrap ><?php echo myNumberFormat($inv['cc']);?></td>
  </tr>
<?php } ?>
</tbody> 
<?php if (count($invs_curr)>1) {?>
<tfoot>
  <tr class="table-warning">
    <td class="bottomsums mytdcml" colspan="2" nowrap><?php echo gks_lang('Σύνολο');?></td>
    <td class="bottomsums mytdcm" colspan="2" nowrap><?php echo myNumberFormat($mysum);?></td>
  </tr>
</tfoot> 
<?php } ?>              
</table>
                      
                      <div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>
                      <div class="form-group row">
                        <div class="col-md-12 text-center"><b><?php echo gks_lang('Σφάλμα μετάδοσης παραστατικών');?></b></div>
                      </div>           
<?php
$invs_curr=[];
foreach ($invs_tf1 as $inv) {
  if ($inv['company_id']==$comp['id'] and $inv['company_sub_id']==$scomp['id_sub']) {
    $invs_curr[]=$inv;
  }
}
usort($invs_curr, "mysort_array");
?>                      
<table class="table table-sm table-responsive11 table-striped table-bordered gkstable border="0" cellspacing="0" cellpadding="5" align="center">
  <thead>
    <tr >	
      <th nowrap class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'>#</th>
      <th nowrap class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Τύπος');?></th> 
      <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Πλήθος');?></th> 
    </tr>
</thead>
<tbody>
<?php if (count($invs_curr)==0) echo '<tr><td class="mytdcm" colspan="3">'.gks_lang('Δεν βρέθηκαν καταχωρήσεις').'</td></tr>';?>
<?php $i=0;$mysum=0;foreach ($invs_curr as $inv) {$i++;$mysum+=$inv['cc'];?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" class="mytdcm aa" nowrap><?php echo ($i);?></th>
    <td class="mytdcml"><?php echo $inv['eidos_parastatikou_descr'];?></td>
    <td class="mytdcm" nowrap ><?php echo myNumberFormat($inv['cc']);?></td>
  </tr>
<?php } ?>
</tbody> 
<?php if (count($invs_curr)>1) {?>
<tfoot>
  <tr class="table-warning">
    <td class="bottomsums mytdcml" colspan="2" nowrap><?php echo gks_lang('Σύνολο');?></td>
    <td class="bottomsums mytdcm" colspan="2" nowrap><?php echo myNumberFormat($mysum);?></td>
  </tr>
</tfoot> 
<?php } ?>              
</table>
                      
                    </div>  
                  </div>  
                </div>
              </div>
            </div>
          </div> 
<?php }} ?>         
        </div>
      </div>
    </div>
  </div>
</div>

<?php }} ?>

<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>


jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  $('#paroxos_get_invoice_pending').click(function() {
    $('#paroxos_get_invoice_pending_results').html('<i class="fas fa-hourglass"></i>&nbsp;');
    $('#paroxos_get_invoice_pending').prop('disabled',true);
    datasend='cmd=invoice_pending';
    datasend+='&paroxos=<?php echo $curr_paroxos_id;?>';
    //console.log(datasend);
    $.ajax({
  		url: '/my/admin-paroxos-overview-ilyda-exec.php',
  		type: 'POST',
  		cache: false,
  		dataType: 'json',
  		data: datasend,
  		error : function(jqXHR ,textStatus,  errorThrown) {
  			myalert('error:' + jqXHR.responseText);
  			$('#paroxos_get_invoice_pending_results').html(gks_lang('Σφάλμα'));
  			$('#paroxos_get_invoice_pending').prop('disabled',false);
  		},				
  		success: function(data) {
  		  $('#paroxos_get_invoice_pending').prop('disabled',false);
  			if (!data) {
  				myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  				$('#paroxos_get_invoice_pending_results').html(gks_lang('Σφάλμα'));
  			} else {
  				if (data.success == true) {
  					console.log(data);
  					$('#paroxos_get_invoice_pending_results').html(data.count);
  					if (data.count>0) {
  					  $('#paroxos_get_invoice_pending_button1').attr('href',data.file);
  					  $('#paroxos_get_invoice_pending_buttons').show();
  					} else {
  					  $('#paroxos_get_invoice_pending_buttons').hide();
  					}
  				} else {
  					myalert('error:' + $.base64.decode(data.message));
  					$('#paroxos_get_invoice_pending_results').html(gks_lang('Σφάλμα'));
  				}
  			}
  		}
  	});   
  });
  
  
  function paroxos_tf1_get_create_keys(mycmd) {
    $('#paroxos_tf1_get_keys_results').html('<i class="fas fa-hourglass"></i>&nbsp;');
    $('#paroxos_tf1_get_keys').prop('disabled',true);
    $('#paroxos_tf1_create_keys').prop('disabled',true);
    datasend='cmd=' + mycmd;
    datasend+='&paroxos=<?php echo $curr_paroxos_id;?>';
    //console.log(datasend);
    $.ajax({
  		url: '/my/admin-paroxos-overview-ilyda-exec.php',
  		type: 'POST',
  		cache: false,
  		dataType: 'json',
  		data: datasend,
  		error : function(jqXHR ,textStatus,  errorThrown) {
  			myalert('error:' + jqXHR.responseText);
  			$('#paroxos_tf1_get_keys_results').html(gks_lang('Σφάλμα'));
  			$('#paroxos_tf1_get_keys').prop('disabled',false);
  			$('#paroxos_tf1_create_keys').prop('disabled',false);
  		},				
  		success: function(data) {
  		  $('#paroxos_tf1_get_keys').prop('disabled',false);
  		  $('#paroxos_tf1_create_keys').prop('disabled',false);
  			if (!data) {
  				myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  				$('#paroxos_tf1_get_keys_results').html(gks_lang('Σφάλμα'));
  			} else {
  				if (data.success == true) {
  					$('#paroxos_tf1_get_keys_results').html(data.html);
  				} else {
  					myalert('error:' + $.base64.decode(data.message));
  					$('#paroxos_tf1_get_keys_results').html(gks_lang('Σφάλμα'));
  				}
  			}
  		}
  	});    
  }

  $('#paroxos_tf1_get_keys').click(function() {
    paroxos_tf1_get_create_keys('tf1_get_keys');
  });
  $('#paroxos_tf1_create_keys').click(function() {
    paroxos_tf1_get_create_keys('tf1_create_keys');
  });
  
  
  
  
});


</script>

<?php echo gks_lang_big_texts('admin-paroxos-overview-ilyda');?>


<?php
//db_close();
include_once('_my_footer_admin.php');


