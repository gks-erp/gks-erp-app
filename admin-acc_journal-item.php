<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$nav_active_array=array('manage','accounting_journal');
db_open();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_acc_journal',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_seires_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_seires','view',0);
$perm_seires_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_seires','edit',0);
$perm_seires_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_seires','add',0);
$perm_seires_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_seires','delete',0);






$sql_parast="SELECT gks_acc_eidi_parastatikon.*, gks_acc_eidi_parastatikon_types.acc_eidi_parastatikon_type_descr, 
gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
gks_acc_eidi_parastatikon_credit.eidos_parastatikou_descr AS credit_descr,
gks_acc_eidi_parastatikon_whi.eidos_parastatikou_descr AS whi_descr
FROM ((gks_acc_eidi_parastatikon 
LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
LEFT JOIN gks_acc_eidi_parastatikon AS gks_acc_eidi_parastatikon_credit ON gks_acc_eidi_parastatikon.credit_acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon_credit.id_acc_eidos_parastatikou) 
LEFT JOIN gks_acc_eidi_parastatikon AS gks_acc_eidi_parastatikon_whi ON gks_acc_eidi_parastatikon.eidos_parastatikou_whi_type_id = gks_acc_eidi_parastatikon_whi.id_acc_eidos_parastatikou
ORDER BY gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
";
$result_parast = $db_link->query($sql_parast);        
if (!$result_parast) {
  debug_mail(false,'error sql',$sql_parast);
  die('sql error');
}
$parasts=array();
$parasts[0]=array(
  'id'=>0,
  'type_descr' => '',
  'label' => '',
  'prev' => '',
  'fpa' => '',
  'posotita' => '',
  'othertaxes' => '',
  'esoda' => '',
  'eksoda' => '',
  'aade' => '',
  'balance' => '',
  'credit_descr' => '',
  'eidos_parastatikou_whi_type_id' =>0,
  'whi_descr' => '',
  'eidos_parastatikou_other_entity' =>0,
  'other_entity_descr' =>'',
  'eidos_parastatikou_correlated_invoices' => 0,
  'correlated_invoices_descr' => '',
  
  'eidos_parastatikou_multiple_connected_marks'=>0,
  'multiple_connected_marks_descr' => '',
  
  'eidos_parastatikou_packings_declarations'=>0,
  'packings_declarations_descr' => '',
);

while ($row_parast = $result_parast->fetch_assoc()) {
  
  $balance='--'; 
  if ($row_parast['eidos_parastatikou_balance_pros']==1) $balance=gks_lang('Θετικό');
  else if ($row_parast['eidos_parastatikou_balance_pros']==-1) $balance=gks_lang('Αρνητικό');
  
  $othertaxes='';
  $temp=trim_gks($row_parast['eidos_parastatikou_has_othertaxes']);
  if ($temp!='') {
    $temp=explode(',',$temp);
    $found=array();
    foreach ($temp as $val) { //wh,ot,sd,fe,dd
      if ($val=='wh') $found[]=gks_lang('Φόροι Παρακρατούμενοι');
      else if ($val=='ot') $found[]=gks_lang('Λοιποί Φόροι');
      else if ($val=='sd') $found[]=gks_lang('Ψηφιακό Τέλος συναλλαγής');
      else if ($val=='fe') $found[]=gks_lang('Τέλη');
      else if ($val=='dd') $found[]=gks_lang('Κρατήσεις','part2');
    } 
    $othertaxes=implode(', ',$found);
  }

  
  
  $parasts[$row_parast['id_acc_eidos_parastatikou']]=array(
    'id'=>$row_parast['id_acc_eidos_parastatikou'],
    'type_descr' => $row_parast['acc_eidi_parastatikon_type_descr'],
    'label' => $row_parast['antisimvalomenos_label'],
    'prev' => ($row_parast['eidos_parastatikou_need_prev']==0 ? gks_lang('Όχι'): gks_lang('Ναι')),
    'fpa' => (($row_parast['eidos_parastatikou_has_fpa']==0 or $row_parast['eidos_parastatikou_has_fpa']==2) ? gks_lang('Όχι'): gks_lang('Ναι')),
    'posotita' => ($row_parast['eidos_parastatikou_has_posotita']==0 ? gks_lang('Όχι'): gks_lang('Ναι')),
    'othertaxes' => $othertaxes,
    'esoda' => ($row_parast['eidos_parastatikou_has_esoda']==0 ? gks_lang('Όχι'): gks_lang('Ναι')),
    'eksoda' => ($row_parast['eidos_parastatikou_has_eksoda']==0 ? gks_lang('Όχι'): gks_lang('Ναι')),
    'aade' => (!empty($row_parast['eidos_parastatikou_aade_code']) ? gks_lang('Ναι, με κωδικό').': '.$row_parast['eidos_parastatikou_aade_code'] : gks_lang('Όχι')),
    'balance' => $balance,
    'credit_descr' => $row_parast['credit_descr'],
    'eidos_parastatikou_whi_type_id' => intval($row_parast['eidos_parastatikou_whi_type_id']),
    'whi_descr' => $row_parast['whi_descr'],
    
    'eidos_parastatikou_other_entity' => intval($row_parast['eidos_parastatikou_other_entity']),
    'other_entity_descr' =>($row_parast['eidos_parastatikou_other_entity']==0 ? gks_lang('Όχι'): gks_lang('Ναι')),
  
    'eidos_parastatikou_correlated_invoices' => intval($row_parast['eidos_parastatikou_correlated_invoices']),
    'correlated_invoices_descr'=>($row_parast['eidos_parastatikou_correlated_invoices']==0 ? gks_lang('Όχι'): gks_lang('Ναι')),
    
    'eidos_parastatikou_multiple_connected_marks'=>intval($row_parast['eidos_parastatikou_multiple_connected_marks']),
    'multiple_connected_marks_descr' => ($row_parast['eidos_parastatikou_multiple_connected_marks']==0 ? gks_lang('Όχι'): gks_lang('Ναι')),
    
    'eidos_parastatikou_packings_declarations'=>intval($row_parast['eidos_parastatikou_packings_declarations']),
    'packings_declarations_descr' => ($row_parast['eidos_parastatikou_packings_declarations']==0 ? gks_lang('Όχι'): gks_lang('Ναι')),
  );

}

//print '<pre>';print_r($parasts); print '</pre>';//die();

$gks_custom_prepare = gks_custom_table_item_prepare('gks_acc_journal',['from'=>'item']);



if ($id==-1) {
  $row = array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_acc_journal']=-1;
  $row['company_id']=0;
  $row['company_title']='';
  $row['company_sub_id']=-1;
  $row['company_sub_title']='';
  $row['acc_journal_code']='';
  $row['acc_journal_descr']='';
  $row['acc_eidos_parastatikou_id']=0;
  $row['acc_eidos_parastatikou_whi_id']=0;
  $row['acc_eidos_parastatikou_other_entity']=0;
  $row['journal_has_correlated_invoices']=0;
  $row['journal_has_multiple_connected_marks']=0;
  $row['journal_has_packings_declarations']=0;
  $row['is_disable']=0;
  $row['sortorder']=1000;
  
  
  $my_page_title=gks_lang('Νέο Ημερολόγιο');

  $company_sub_id=0; if (isset($_GET['company_sub_id'])) $company_sub_id=intval($_GET['company_sub_id']);
  if ($company_sub_id>0) {
    $sql="SELECT company_sub_title, company_id, company_title
    FROM gks_company_subs LEFT JOIN gks_company ON gks_company_subs.company_id = gks_company.id_company
    WHERE id_company_sub=".$company_sub_id;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
    if ($result->num_rows==1) {
      $row_user = $result->fetch_assoc();  
      $row['company_sub_id'] =$company_sub_id;
      $row['company_sub_title']=$row_user['company_sub_title'];
      $row['company_id'] =$row_user['company_id'];
      $row['company_title']=$row_user['company_title'];
    }    
       
    
  } else {
    $company_id=0; if (isset($_GET['company_id'])) $company_id=intval($_GET['company_id']);
    if ($company_id>0) {
      $sql="SELECT company_title FROM gks_company WHERE id_company=".$company_id;
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
      if ($result->num_rows==1) {
        $row_user = $result->fetch_assoc();  
        $row['company_id'] =$company_id;
        $row['company_title']=$row_user['company_title'];
      }
    }
  }
  

} else {
  $sql ="SELECT gks_acc_journal.*, ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
  gks_company.company_title, gks_company_subs.company_sub_title
  FROM (((gks_acc_journal 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_acc_journal.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_acc_journal.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
  LEFT JOIN gks_company ON gks_acc_journal.company_id = gks_company.id_company) 
  LEFT JOIN gks_company_subs ON gks_acc_journal.company_sub_id = gks_company_subs.id_company_sub

  where id_acc_journal = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'record not found sql',$sql); 
    die('no record found');
  }
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Ημερολόγιο').': '.$row['acc_journal_descr'];
  $object_title=$row['acc_journal_descr'];
}

$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);


stat_record();

$lang_data_obj=gks_lang_data_obj_prepare('gks_acc_journal','default');
if ($lang_data_obj['success']==false) die($lang_data_obj['message']);


include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Ημερολόγιο');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Ημερολόγιο');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέο');?></span></h3>
      <?php }?>
    </div>
  </div>
</div>

<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">
      

        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ημερολόγιο');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('bas');?>> 
          <div class="form-group row">
            <label for="company" class="col-md-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-md-8">
              <input id="company" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['company_title']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
              <input id="company_id" type="hidden" value="<?php echo $row['company_id'];?>" class="myneedsave">
            </div>
          </div>
          <div class="form-group row">
            <label for="company_sub_title" class="col-md-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Υποκατάστημα');?>:</label>
            <div class="col-md-8">
              <input id="company_sub_title" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php if ($row['company_sub_id']==0) echo gks_lang('Κεντρικό'); else echo htmlspecialchars_gks($row['company_sub_title']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
              <input id="company_sub_id" type="hidden" value="<?php echo $row['company_sub_id'];?>" class="myneedsave">
            </div>
          </div>
          
          <div class="form-group row">
            <label for="acc_eidos_parastatikou_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος Παραστατικού');?>:</label>
            <div class="col-md-8">
              <select id="acc_eidos_parastatikou_id" class="form-control form-control-sm myneedsave gks_select2">
                <option value="0"></option>
                <?php
//                $sql="select * FROM gks_acc_eidi_parastatikon where is_selectable=1 ORDER BY sortorder ";
//                $result_select = $db_link->query($sql);        
//                if (!$result_select) {
//                  debug_mail(false,'admin-users-item.php error sql',$sql);
//                  die('sql error');
//                }
//                while ($row_select = $result_select->fetch_assoc()) {
//                  echo '<option value="'.$row_select['id_acc_eidos_parastatikou'].'" ';
//                  if ($row_select['id_acc_eidos_parastatikou']==$row['acc_eidos_parastatikou_id']) echo ' selected ';
//                  echo '>'.$row_select['eidos_parastatikou_descr'].'</option>';
//                }
                
                
                $sql="SELECT gks_acc_eidi_parastatikon.*,
ug2.eidos_parastatikou_descr AS gt2,
ug3.eidos_parastatikou_descr AS gt3, 
ug4.eidos_parastatikou_descr AS gt4, 
ug5.eidos_parastatikou_descr AS gt5, 
ug6.eidos_parastatikou_descr AS gt6, 
ug7.eidos_parastatikou_descr AS gt7, 
ug8.eidos_parastatikou_descr AS gt8, 
ug9.eidos_parastatikou_descr AS gt9, 
ug10.eidos_parastatikou_descr AS gt10,


ug2.id_acc_eidos_parastatikou AS id2, 
ug3.id_acc_eidos_parastatikou AS id3, 
ug4.id_acc_eidos_parastatikou AS id4, 
ug5.id_acc_eidos_parastatikou AS id5,
ug6.id_acc_eidos_parastatikou AS id6,
ug7.id_acc_eidos_parastatikou AS id7,
ug8.id_acc_eidos_parastatikou AS id8,
ug9.id_acc_eidos_parastatikou AS id9,
ug10.id_acc_eidos_parastatikou AS id10,

CONCAT_WS('\\\\',
                 ug10.eidos_parastatikou_descr,
                 ug9.eidos_parastatikou_descr,
                 ug8.eidos_parastatikou_descr,
                 ug7.eidos_parastatikou_descr,
                 ug6.eidos_parastatikou_descr,
                 ug5.eidos_parastatikou_descr,
                 ug4.eidos_parastatikou_descr,
                 ug3.eidos_parastatikou_descr,
                 ug2.eidos_parastatikou_descr,
                 gks_acc_eidi_parastatikon.eidos_parastatikou_descr) as fullpath,
CONCAT_WS('\\\\',
                 ug10.eidos_parastatikou_descr,
                 ug9.eidos_parastatikou_descr,
                 ug8.eidos_parastatikou_descr,
                 ug7.eidos_parastatikou_descr,
                 ug6.eidos_parastatikou_descr,
                 ug5.eidos_parastatikou_descr,
                 ug4.eidos_parastatikou_descr,
                 ug3.eidos_parastatikou_descr,
                 ug2.eidos_parastatikou_descr) as dirpath
FROM ((((((((gks_acc_eidi_parastatikon

LEFT JOIN gks_acc_eidi_parastatikon AS ug2 ON gks_acc_eidi_parastatikon.parent_id = ug2.id_acc_eidos_parastatikou) 
LEFT JOIN gks_acc_eidi_parastatikon AS ug3 ON ug2.parent_id = ug3.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon AS ug4 ON ug3.parent_id = ug4.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon AS ug5 ON ug4.parent_id = ug5.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon AS ug6 ON ug5.parent_id = ug6.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon AS ug7 ON ug6.parent_id = ug7.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon AS ug8 ON ug7.parent_id = ug8.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon AS ug9 ON ug8.parent_id = ug9.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon AS ug10 ON ug9.parent_id = ug10.id_acc_eidos_parastatikou

where 1=1 
ORDER BY gks_acc_eidi_parastatikon.sortorder,fullpath";

                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                $isgroup_open=false;
                while ($row_select = $result_select->fetch_assoc()) {
                  $mypad=''; 
                  if (!empty($row_select['gt2'])) $mypad='&nbsp;&nbsp;&nbsp;';
                  if (!empty($row_select['gt3'])) $mypad='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                  
                  $aade_code='';
//                  $aade_code=trim_gks($row_select['eidos_parastatikou_aade_code']);
//                  if ($aade_code!='') {
//                    $aade_code=$aade_code.' ';
//                  }
                  
                  if ($row_select['is_selectable']==0) {
                    if ($isgroup_open) echo '</optgroup>'."\n";
                    $isgroup_open=true;
                    echo '<optgroup label="'.$mypad.$aade_code.$row_select['eidos_parastatikou_descr'].'">'."\n";
                  } else {
                    
                    echo '<option value="'.$row_select['id_acc_eidos_parastatikou'].'" ';
                    if ($row_select['id_acc_eidos_parastatikou']==$row['acc_eidos_parastatikou_id']) echo ' selected ';
                    echo '>'.$mypad.$aade_code.$row_select['eidos_parastatikou_descr'].'</option>'."\n";
                  }
                }
                if ($isgroup_open) echo '</optgroup>';                
                ?>
              </select>   
              <div style="font-size:80%;margin-top:6px;">
                <table class="table table-sm table-responsive1 table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
                  <thead>
                    <tr>
                      <th class="table-dark" scope="col" style="width:50%;" width="50%"><?php echo gks_lang('Ιδιότητα');?></th>  
                      <th class="table-dark" scope="col" style="width:50%;" width="50%"><?php echo gks_lang('Τιμή');?></th>  
                    </tr>  
                  </thead>
                  <tbody>
                    <tr>
                      <td><?php echo gks_lang('Τύπος');?>:</td>  
                      <td id="eidos_type_descr"><?php echo $parasts[$row['acc_eidos_parastatikou_id']]['type_descr'];?></td>  
                    </tr>  
                    <tr>
                      <td><?php echo gks_lang('Επαφή ως');?>:</td>  
                      <td id="eidos_label"><?php echo $parasts[$row['acc_eidos_parastatikou_id']]['label'];?></td>  
                    </tr>  
                    <tr>
                      <td><?php echo gks_lang('Απαιτεί άλλο παραστατικό');?>:</td>  
                      <td id="eidos_prev"><?php echo $parasts[$row['acc_eidos_parastatikou_id']]['prev'];?></td>  
                    </tr>  
                    <tr>
                      <td><?php echo gks_lang('Έχει ΦΠΑ');?>:</td>  
                      <td id="eidos_fpa"><?php echo $parasts[$row['acc_eidos_parastatikou_id']]['fpa'];?></td>  
                    </tr>  
                    <tr>
                      <td><?php echo gks_lang('Έχει Ποσότητες');?>:</td>  
                      <td id="eidos_posotita"><?php echo $parasts[$row['acc_eidos_parastatikou_id']]['posotita'];?></td>  
                    </tr>  
                    <tr>
                      <td><?php echo gks_lang('Άλλοι φόροι');?>:</td>  
                      <td id="eidos_othertaxes"><?php echo $parasts[$row['acc_eidos_parastatikou_id']]['othertaxes'];?></td>  
                    </tr>  
                    <tr>
                      <td><?php echo gks_lang('Έχει έσοδα');?>:</td>  
                      <td id="eidos_esoda"><?php echo $parasts[$row['acc_eidos_parastatikou_id']]['esoda'];?></td>  
                    </tr>  
                    <tr>
                      <td><?php echo gks_lang('Έχει έξοδα');?>:</td>  
                      <td id="eidos_eksoda"><?php echo $parasts[$row['acc_eidos_parastatikou_id']]['eksoda'];?></td>  
                    </tr>  
                    <tr>
                      <td><?php echo gks_lang('Αποστολή σε ΑΑΔΕ');?>:</td>  
                      <td id="eidos_aade"><?php echo $parasts[$row['acc_eidos_parastatikou_id']]['aade'];?></td>    
                    </tr>  
                    <tr>
                      <td><?php echo gks_lang('Πρόσημο υπολοίπου επαφής');?>:</td>  
                      <td id="eidos_balance"><?php echo $parasts[$row['acc_eidos_parastatikou_id']]['balance'];?></td>  
                    </tr>  
                    <tr>
                      <td><?php echo gks_lang('Το πιστωτικό του είναι');?>:</td>  
                      <td id="eidos_credit_descr"><?php echo $parasts[$row['acc_eidos_parastatikou_id']]['credit_descr'];?></td>  
                    </tr>  
                    <tr>
                      <td><?php echo gks_lang('Το δελτίο του είναι');?>:</td>  
                      <td id="eidos_whi_descr"><?php echo $parasts[$row['acc_eidos_parastatikou_id']]['whi_descr'];?></td>  
                    </tr>  
                    <tr>
                      <td><?php echo gks_lang('Λοιπoί Συσχετιζόμενοι ΑΦΜ');?>:</td>  
                      <td id="eidos_other_entity_descr"><?php echo $parasts[$row['acc_eidos_parastatikou_id']]['other_entity_descr'];?></td>  
                    </tr>
                    <tr>
                      <td><?php echo gks_lang('Συσχετιζόμενα Παραστατικά');?>:</td>  
                      <td id="eidos_correlated_invoices_descr"><?php echo $parasts[$row['acc_eidos_parastatikou_id']]['correlated_invoices_descr'];?></td>  
                    </tr>
                    <tr>
                      <td><?php echo gks_lang('Πολλαπλά Συνδεόμενα ΜΑΡΚ');?>:</td>  
                      <td id="eidos_multiple_connected_marks_descr"><?php echo $parasts[$row['acc_eidos_parastatikou_id']]['multiple_connected_marks_descr'];?></td>  
                    </tr>
                    <tr>
                      <td><?php echo gks_lang('Πληροφορίες Συσκευασίας Διακίνησης');?>:</td>  
                      <td id="eidos_packings_declarations_descr"><?php echo $parasts[$row['acc_eidos_parastatikou_id']]['packings_declarations_descr'];?></td>  
                    </tr>
                  </tbody>
                </table>

              
              </div> 
            </div>
          </div> 
          <div class="form-group row">
            <label for="acc_journal_code" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κωδικός');?>:</label>
            <div class="col-md-8">
              <input id="acc_journal_code" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['acc_journal_code']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="acc_journal_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-md-8">
              <input id="acc_journal_descr" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['acc_journal_descr']);?>">
            </div>
          </div> 
          <?php 
          echo gks_lang_data_obj_render_html($lang_data_obj,$row,array('acc_journal_descr'));
          ?>
          <div class="form-group row">
            <label for="sortorder" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-md-8">
              <input id="sortorder" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['sortorder'];?>" min="0" step="1">
            </div>
          </div> 

          <div class="form-group row" id="div_whi_id" style="display:<?php 
            if ($parasts[$row['acc_eidos_parastatikou_id']]['eidos_parastatikou_whi_type_id']==0) echo 'none';
            ?>"> 
            <label for="whi_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Δελτίο Αποστολής/Παραλαβής');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="whi_id" value="1" <?php if ($row['acc_eidos_parastatikou_whi_id']!=0) echo ' checked '; ?> class="switchery1_this">
              <br>
              <small class="form-text text-muted"><?php echo gks_lang('Εάν θα επηρεάσει την αποθήκη, ομοίως σαν το');?><br><i><span id="eidos_whi_descr2"><?php echo $parasts[$row['acc_eidos_parastatikou_id']]['whi_descr'];?></span></i></small>
            </div>
          </div>
          <div class="form-group row" id="div_other_entity" style="display:<?php 
            if ($parasts[$row['acc_eidos_parastatikou_id']]['eidos_parastatikou_other_entity']==0) echo 'none';
            ?>"> 
            <label for="other_entity" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Λοιπoί Συσχετιζόμενοι ΑΦΜ');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="other_entity" value="1" <?php if ($row['acc_eidos_parastatikou_other_entity']!=0) echo ' checked '; ?> class="switchery1_this">
              <br>
              <small class="form-text text-muted"><?php echo gks_lang('Εάν θα υπάρχει η δυνατότητα να προστεθούν');?> <i><?php echo gks_lang('Λοιπoί Συσχετιζόμενοι ΑΦΜ');?></i></small>
            </div>
          </div> 
          <div class="form-group row" id="div_correlated_invoices" style="display:<?php 
            if ($parasts[$row['acc_eidos_parastatikou_id']]['eidos_parastatikou_correlated_invoices']==0) echo 'none';
            ?>"> 
            <label for="correlated_invoices" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Συσχετιζόμενα Παραστατικά');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="correlated_invoices" value="1" <?php if ($row['journal_has_correlated_invoices']!=0) echo ' checked '; ?> class="switchery1_this">
              <br>
              <small class="form-text text-muted"><?php echo gks_lang('Εάν θα υπάρχει η δυνατότητα να προστεθούν');?> <i><?php echo gks_lang('Συσχετιζόμενα Παραστατικά');?></i></small>
            </div>
          </div>         
          
          <div class="form-group row" id="div_multiple_connected_marks" style="display:<?php 
            if ($parasts[$row['acc_eidos_parastatikou_id']]['eidos_parastatikou_multiple_connected_marks']==0) echo 'none';
            ?>"> 
            <label for="multiple_connected_marks" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πολλαπλά Συνδεόμενα ΜΑΡΚ');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="multiple_connected_marks" value="1" <?php if ($row['journal_has_multiple_connected_marks']!=0) echo ' checked '; ?> class="switchery1_this">
              <br>
              <small class="form-text text-muted"><?php echo gks_lang('Εάν θα υπάρχει η δυνατότητα να προστεθούν');?> <i><?php echo gks_lang('Πολλαπλά Συνδεόμενα ΜΑΡΚ');?></i></small>
            </div>
          </div>
          <div class="form-group row" id="div_packings_declarations" style="display:<?php 
            if ($parasts[$row['acc_eidos_parastatikou_id']]['eidos_parastatikou_packings_declarations']==0) echo 'none';
            ?>"> 
            <label for="packings_declarations" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πληροφορίες Συσκευασίας Διακίνησης');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="packings_declarations" value="1" <?php if ($row['journal_has_packings_declarations']!=0) echo ' checked '; ?> class="switchery1_this">
              <br>
              <small class="form-text text-muted"><?php echo gks_lang('Εάν θα υπάρχει η δυνατότητα να προστεθούν');?> <i><?php echo gks_lang('Πληροφορίες Συσκευασίας Διακίνησης');?></i></small>
            </div>
          </div>

          
                                                                    

          
          
          <div class="form-group row">
            <label for="is_disable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="is_disable" value="1" <?php if ($row['is_disable']==0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          

        </div>
      </div>
    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>

<?php
echo $gks_custom_row['html'];
//echo '<pre>';print_r($gks_custom_row['fields']);print '</pre>';
?>
      
      
      
      
      
      
    </div>
  </div>
</div>
          

<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_acc_journal'];?>" data-model="gks_acc_journal" data-backurl="admin-acc_journal.php" <?php if ($id<=0) echo 'disabled';?>><?php echo gks_lang('Διαγραφή');?></button>

    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">

      <?php 
      echo getObjectRels('gks_acc_journal',$id); 
      echo getActivityObjectTable('gks_acc_journal',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_acc_journal','id'=>$id));
      echo $obj_fileslist['html'];
      ?>

    
    </div>
    <div class="col-md-6">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>      
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_acc_journal']>0) echo $row['id_acc_journal'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['user_id_add']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_add']))echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['user_id_edit']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_edit'])) echo showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('IP');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><a href="admin-stat-ip.php?ip=<?php echo $row['myip'];?>"><?php echo $row['myip'];?></a></span></div>
          </div>
        </div>
      </div>    
    </div>
    
  </div>
</div>  
<?php if ($perm_seires_view) {?>
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          
          <span style="vertical-align: middle;"><?php echo gks_lang('Σειρές');?></span>
          <?php if ($perm_seires_add) {?>
          <a class="btn btn-sm btn-primary gks_stoppropagation" style="margin-left:10px;" href="admin-acc_seires-item.php?id=-1&acc_journal_id=<?php echo $id;?>">
            <?php echo gks_lang('Προσθήκη');?>
          </a>
          <?php } ?>
                    
        </div>
        <div class="card-body" <?php echo gks_card_body('jseir');?>> 
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="activity_table">
            <thead>
              <tr>
                <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  >#</th>
                <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  colspan="<?php 
                  if ($perm_seires_delete) echo '3'; else echo '2';
                ?>">ID</th> 
                <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  nowrap="nowrap"><span title="<?php echo gks_lang('Κωδικός');?>"><?php echo gks_lang('Κωδ');?></span></th>        
                <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%" nowrap="nowrap"><?php echo gks_lang('Περιγραφή');?></th>        
        
                <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><span class="tooltipster" title="<?php echo gks_lang('Πρόθεμα');?>"><?php echo gks_lang('Προ');?></span></th>        
                <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><span class="tooltipster" title="<?php echo gks_lang('Επίθεμα');?>"><?php echo gks_lang('Επι');?></span></th>        
                <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><span class="tooltipster" title="<?php echo gks_lang('Πλήθος Ψηφίων');?>"><?php echo gks_lang('ΠΨ');?></span></th>        
                <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><span class="tooltipster" title="<?php echo gks_lang('Βήμα αριθμών');?>"><?php echo gks_lang('Βήμα');?></span></th>        
                <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><span class="tooltipster" title="<?php echo gks_lang('Επόμενος Αριθμός');?>"><?php echo gks_lang('ΕΑ');?></span></th>        
                <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><span class="tooltipster" title="<?php echo gks_lang('Αποστολή myData');?>"><?php echo gks_lang('myData');?></span></th>        
                <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><span class="tooltipster" title="<?php echo gks_lang('Χειρόγραφη Σειρά');?>"><?php echo gks_lang('Χειρ');?></span></th>        
        
                       
                <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><span class="tooltipster" title="<?php echo gks_lang('Πλήθος Παραστατικών');?>"><?php echo gks_lang('Π.Παρ.');?></span></th>        
                <?php if ($perm_seires_edit) {?>
                <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" nowrap="nowrap"><span class="tooltipster" title="<?php echo gks_lang('Σειρά Ταξινόμησης');?>"><?php echo gks_lang('ΣειράΤ');?></span></th>
                <?php } ?>
                <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%" nowrap="nowrap"><?php echo gks_lang('Ενεργή');?></th>   



              </tr>
            </thead>  
            <tbody>
<?php
            $sql = "SELECT gks_acc_seires.*, 
            ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
            gks_acc_journal.acc_journal_descr, 
            gks_acc_journal.company_id, gks_company.company_title, 
            gks_acc_journal.company_sub_id, gks_company_subs.company_sub_title, 
            gks_acc_journal.acc_eidos_parastatikou_id, gks_acc_eidi_parastatikon.eidos_parastatikou_descr,
            tbl_inv.cc_inv
            FROM ((((((gks_acc_seires 
            LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_acc_seires.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
            LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_acc_seires.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
            LEFT JOIN gks_acc_journal ON gks_acc_seires.acc_journal_id = gks_acc_journal.id_acc_journal) 
            LEFT JOIN gks_company ON gks_acc_journal.company_id = gks_company.id_company) 
            LEFT JOIN gks_company_subs ON gks_acc_journal.company_sub_id = gks_company_subs.id_company_sub) 
            LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
            LEFT JOIN (
              SELECT inv_acc_seira_id, Count(id_acc_inv) AS cc_inv FROM gks_acc_inv GROUP BY inv_acc_seira_id
            ) as tbl_inv on gks_acc_seires.id_acc_seira =tbl_inv.inv_acc_seira_id
            
            where acc_journal_id=".$id."
            ORDER BY gks_acc_seires.sortorder,gks_acc_seires.seira_descr";
            
            
            //echo $query;
            //die();
            	
            $result = $db_link->query($sql);        
            if (!$result) debug_mail(false,'error sql',$sql);
            if (!$result) die('sql error');

            $i = 0;
            while ($row = $result->fetch_assoc()) {
        
        	$i++;
        ?>
          <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" data-id="<?php echo $row['id_acc_seira'];?>">
            <th scope="row" nowrap align="right"><?php echo ($i);?></th>
            <td nowrap class="mytdcm p-0"><a href="admin-acc_seires-item.php?id=<?php echo $row['id_acc_seira'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
            <td nowrap class="mytdcm p-0"><?php echo $row['id_acc_seira'];?></td>
            <?php if ($perm_seires_delete) {?>
            <td nowrap class="mytdcm p-0"><i class="fas fa-trash-alt deleterow" data-id="<?php echo $row['id_acc_seira'];?>" data-model="gks_acc_seires"></i></td>
            <?php } ?>
            <td nowrap class="mytdcm"><?php echo $row['seira_code'];?></td>
            <td        class="mytdcml"><?php echo $row['seira_descr'];?></td>
            
            
            <td nowrap class="mytdcm"><?php echo $row['prefix'];?></td>
            <td nowrap class="mytdcm"><?php echo $row['suffix'];?></td>
            <td nowrap class="mytdcm"><?php echo $row['number_size'];?></td>
            <td nowrap class="mytdcm"><?php echo $row['number_step'];?></td>
            <td nowrap class="mytdcm"><?php echo $row['next_number'];?></td>
            <td nowrap class="mytdcm"><?php if ($row['send_mydata']!=0) {?><i class="fas fa-database tooltipster" title="<?php echo gks_lang('Αποστολή myData');?>" style="color:#0094da;"></i> <?php } ?></td>
            <td nowrap class="mytdcm"><?php if ($row['is_xeirografi']!=0) {?><i class="fas fa-edit tooltipster" title="<?php echo gks_lang('Χειρόγραφη');?>"></i> <?php } ?></td>
        
        
            <td nowrap class="mytdcm"><?php echo $row['cc_inv'];?></td>
            <?php if ($perm_seires_edit) {?>
            <td nowrap class="mytdcm sortorder_handle" title="<?php echo $row['sortorder'];?>">
              <i class="fas fa-arrows-alt-v"></i>
              <span><?php echo $row['sortorder'];?></span>
            </td>
            <?php }?>

            <td class="mytdcm"><?php echo myimg010r($row['is_disable']);?></td>
          </tr>
        <?php    
            }
?>

              
              
            </tbody>   
          </table>
                  
        </div>
      </div>
    </div>
  </div>
</div>
<?php } ?>



<?php include_once('_dialogs.php'); ?>
<script src='/my/js/tinymce/tinymce.min.js'></script>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>
  
var need_save=false;
var mychange = 'change keyup paste';
var gks_page_loading=true;

var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;
var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 





var from_php_dialog_object_rel_curr='gks_acc_journal';
var from_php_activity_model='gks_acc_journal';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;

var from_php_parasts=JSON.parse('<?php echo json_encode($parasts);?>');

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_acc_journal','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_acc_journal','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_acc_journal','delete',$id);?>;



tinymce.init({
  language: from_php_gks_tinymce_locale,
  entity_encoding : 'raw',
  forced_root_block:false, 
  remove_trailing_brs: false,
  theme: 'silver', 
  browser_spellcheck: true,
  plugins: 'autoresize print preview  searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists  wordcount imagetools textpattern help code',
  toolbar: 'undo redo formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | code',
  menubar:true,
  statusbar: true,
  contextmenu: '', //gia na gine disable to default
  templates: [],
  content_css: [],
  content_style: '.mce-content-body {font-size:12px;font-family:"Open Sans",sans-serif;}',
  relative_urls : true,
  convert_urls: true,
  document_base_url : (window.location.origin + '/'),
  min_height: 200,
    
  selector: '.gks_tinymce',
  init_instance_callback: function(editor) {
    editor.on('Change', function(e) {
      need_save=true;
    });
  },
  readonly : (from_php_perm_ret_edit ? 0 : 1),
    
});

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  

  var control_enter_active=false;
  $(document).on('keypress', function(event) {
    if (event.which == 10 && event.ctrlKey) {
      control_enter_active=true;
      event.preventDefault();
      event.stopPropagation();
      elem=$('#submit_button_ok_custom');
      if (elem.is(":visible")) {
        elem.click();  
      }
      setTimeout(function(){control_enter_active=false; }, 300);
    }  
  });  

  $('#submit_button_ok_custom').click(function(event) {mysubmit(''); return false;});
  function mysubmit() {
    
    datasend='';

    datasend+='&company_id='  + encodeURIComponent(($("#mypostform #company_id").val().trim()));
    datasend+='&company_sub_id='  + encodeURIComponent(($("#mypostform #company_sub_id").val().trim()));
    datasend+='&acc_journal_code='  + encodeURIComponent($.base64.encode($("#mypostform #acc_journal_code").val().trim()));
    datasend+='&acc_journal_descr='  + encodeURIComponent($.base64.encode($("#mypostform #acc_journal_descr").val().trim()));
    datasend+='&acc_eidos_parastatikou_id='  + encodeURIComponent($("#mypostform #acc_eidos_parastatikou_id").val().trim());
    datasend+='&sortorder='  + encodeURIComponent(($("#mypostform #sortorder").val().trim()));
    datasend+='&whi_id=' + (($('#whi_id').is(':checked')) ? '1':'0');
    datasend+='&other_entity=' + (($('#other_entity').is(':checked')) ? '1':'0');
    datasend+='&correlated_invoices=' + (($('#correlated_invoices').is(':checked')) ? '1':'0');
    datasend+='&multiple_connected_marks=' + (($('#multiple_connected_marks').is(':checked')) ? '1':'0');
    datasend+='&packings_declarations=' + (($('#packings_declarations').is(':checked')) ? '1':'0');
    
    datasend+='&is_disable=' + (($('#is_disable').is(':checked')) ? '0':'1');
    
    
    
    datasend+=gks_custom_datasend();
    datasend+=gks_lang_data_obj_input_collect();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-acc_journal-item-exec.php?id=' + <?php echo $id;?>,
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				$("body").removeClass("myloading");
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
				  
					if (data.success == true) {
					  need_save=false;
            if (data.redirect=='') {
  					  window.location.reload();
  					} else {
  					  window.location.href = $.base64.decode(data.redirect);
  					}
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     

    return false;
  }


  $('#company').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-company.php',
        dataType: "json",
        cache: false,
        data: mydata,
        error : function(jqXHR ,textStatus,  errorThrown) {
  				myalert('error:' + jqXHR.responseText);
  			},
        success: function( data ) {
          if (data.success == true) {
            response( data.list);
          } else {
            myalert('error:' + $.base64.decode(data.message));
          }
        }
      });
    },
    minLength: 3,
    delay: 300, //default
    select: function( event, ui ) {
      $('#company_id').val(ui.item.id);
      $('#company_sub_title').val(gks_lang('Κεντρικό'));
      $('#company_sub_id').val('0'); 
      
      
      //console.log(ui.item);     
    },
    change: function (event, ui) {
        if(!ui.item){
          $('#company').val('');
          $('#company_id').val('');
          $('#company_sub_title').val('');
          $('#company_sub_id').val('');
        }
    }
  });  
  
  $('#company_sub_title').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        company_id: $('#company_id').val(),
        and_kentriko:1,        
      };
      $.ajax({
        url: 'admin-autocomplete-company-sub.php',
        dataType: "json",
        cache: false,
        data: mydata,
        error : function(jqXHR ,textStatus,  errorThrown) {
  				myalert('error:' + jqXHR.responseText);
  			},
        success: function( data ) {
          if (data.success == true) {
            response( data.list);
          } else {
            myalert('error:' + $.base64.decode(data.message));
          }
        }
      });
    },    
    minLength: 3,
    delay: 300, //default
    select: function( event, ui ) {
      $('#company_sub_id').val(ui.item.id);
            
    },
    change: function (event, ui) {
        if(!ui.item){
          $('#company_sub_title').val('');
          $('#company_sub_id').val('');
        }
    }
  });      


  var acc_journal_descr_change=false;
  $('#acc_eidos_parastatikou_id').change(function() {
    eidos_id=parseInt($(this).val());
    if (isNaN(eidos_id)) eidos_id=0;
    if (acc_journal_descr_change==false || $('#acc_journal_descr').val()=='') {
      val=$('#acc_eidos_parastatikou_id option:selected').html().replaceAll('&nbsp;',' ').trim();
      $('#acc_journal_descr').val(val);  
    }
    //console.log(from_php_parasts[eidos_id]);
    $('#eidos_type_descr').html(from_php_parasts[eidos_id].type_descr);
    $('#eidos_label').html(from_php_parasts[eidos_id].label);
    $('#eidos_prev').html(from_php_parasts[eidos_id].prev);
    $('#eidos_fpa').html(from_php_parasts[eidos_id].fpa);
    $('#eidos_posotita').html(from_php_parasts[eidos_id].posotita);
    $('#eidos_othertaxes').html(from_php_parasts[eidos_id].othertaxes);
    $('#eidos_esoda').html(from_php_parasts[eidos_id].esoda);
    $('#eidos_eksoda').html(from_php_parasts[eidos_id].eksoda);
    $('#eidos_aade').html(from_php_parasts[eidos_id].aade);
    $('#eidos_balance').html(from_php_parasts[eidos_id].balance);
    $('#eidos_credit_descr').html(from_php_parasts[eidos_id].credit_descr);
    $('#eidos_whi_descr').html(from_php_parasts[eidos_id].whi_descr);
    $('#eidos_whi_descr2').html(from_php_parasts[eidos_id].whi_descr);
    $('#eidos_other_entity_descr').html(from_php_parasts[eidos_id].other_entity_descr);
    $('#eidos_correlated_invoices_descr').html(from_php_parasts[eidos_id].correlated_invoices_descr);
    $('#eidos_multiple_connected_marks_descr').html(from_php_parasts[eidos_id].multiple_connected_marks_descr);
    $('#eidos_packings_declarations_descr').html(from_php_parasts[eidos_id].packings_declarations_descr);
    
    
    
    if (from_php_parasts[eidos_id].eidos_parastatikou_whi_type_id==0) {
      $('#div_whi_id').hide();
    } else {
      $('#div_whi_id').show();
    }
    if (from_php_parasts[eidos_id].eidos_parastatikou_other_entity==0) {
      $('#div_other_entity').hide();
    } else {
      $('#div_other_entity').show();
    }
    
    if (from_php_parasts[eidos_id].eidos_parastatikou_correlated_invoices==0) {
      $('#div_correlated_invoices').hide();
    } else {
      $('#div_correlated_invoices').show();
    }
    if (from_php_parasts[eidos_id].eidos_parastatikou_multiple_connected_marks==0) {
      $('#div_multiple_connected_marks').hide();
    } else {
      $('#div_multiple_connected_marks').show();
    }
    if (from_php_parasts[eidos_id].eidos_parastatikou_packings_declarations==0) {
      $('#div_packings_declarations').hide();
    } else {
      $('#div_packings_declarations').show();
    }


    
  });
  
  $('#acc_journal_descr').on('change keyup paste', function() {
    acc_journal_descr_change=true;
  });
  
  $('#activity_table > tbody').sortable({
    handle: '.sortorder_handle',
    update: function( event, ui ) {
      mylist = $(this).sortable('toArray', {attribute: 'data-id'});
      gks_sortorder_obj('gks_acc_seires',mylist,'#activity_table > tbody');
    }
  });



  var switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  switchery1_this.forEach(function(html) {
    var switchery3 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });
  
  
  
    
  //generic
  gks_page_loading=false;
  
  if (from_php_scrollto!='') {
    if ($('#' + from_php_scrollto).length>0) {
      $([document.documentElement, document.body]).animate({
          scrollTop: $('#' + from_php_scrollto).offset().top
      }, 500);
    }
    if (window.location.href.endsWith('&scrollto=' + from_php_scrollto)) {
      newurl=window.location.href;
      newurl=newurl.substring(0,newurl.length-('&scrollto=' + from_php_scrollto).length);
      
      window.history.pushState({}, window.document.title, newurl);
    }
  } else if (from_php_temp_mypropertiesheight!=0) {
    $("html").scrollTop(from_php_temp_mypropertiesheight);
  }



  $('.myneedsave').on('input keyup paste', function() {
    need_save=true; 
  });

  window.onbeforeunload = function() {
    if (need_save==false) return;
    return gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');
  };

  need_save=false;  
    
});



 
</script>




<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


