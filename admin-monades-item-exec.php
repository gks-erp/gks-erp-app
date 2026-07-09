<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$id=0;
if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id<=0 and $id!= -1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}


$my_page_title=gks_lang('Αποθήκευση Μονάδας Μέτρησης').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_monades_metrisis',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}


if ($id>0) {
  $sql ="SELECT * FROM gks_monades_metrisis where id_monada = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  }
  $row = $result->fetch_assoc();
}

$monada_descr=''; if (isset($_POST['monada_descr'])) $monada_descr=trim_gks(base64_decode($_POST['monada_descr']));
$monada_symbol=''; if (isset($_POST['monada_symbol'])) $monada_symbol=trim_gks(base64_decode($_POST['monada_symbol']));
$monada_parent_id=0; if (isset($_POST['monada_parent_id'])) $monada_parent_id=intval($_POST['monada_parent_id']);
$monada_parent_epi=0; if (isset($_POST['monada_parent_epi'])) $monada_parent_epi=floatval(str_replace(',','.', $_POST['monada_parent_epi']));
$monada_sortorder=0; if (isset($_POST['monada_sortorder'])) $monada_sortorder=intval($_POST['monada_sortorder']);
$aade_eidos_posotitas_id=0; if (isset($_POST['aade_eidos_posotitas_id'])) $aade_eidos_posotitas_id=intval($_POST['aade_eidos_posotitas_id']);
$monada_peppol_code=''; if (isset($_POST['monada_peppol_code'])) $monada_peppol_code=trim_gks(base64_decode($_POST['monada_peppol_code']));


if ($monada_parent_id<=0) $monada_parent_epi=0;


if ($monada_descr=='') {debug_mail(false,'emptyl',               gks_lang('Η περιγραφή δεν μπορεί να είναι κενή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η περιγραφή δεν μπορεί να είναι κενή')));
  echo json_encode($return); die(); }

if ($monada_symbol=='') {debug_mail(false,'emptyl',              gks_lang('Το σύμβολο δεν μπορεί να είναι κενό'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Το σύμβολο δεν μπορεί να είναι κενό')));
  echo json_encode($return); die(); }

if ($monada_parent_id>0 and $monada_parent_epi<=0) {debug_mail(false,'emptyl', gks_lang('Ορίστε τον συντελεστή μετατροπής'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε τον συντελεστή μετατροπής')));
  echo json_encode($return); die(); }



$sql="select * from gks_monades_metrisis where monada_descr like '".$db_link->escape_string($monada_descr)."' and id_monada<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Η περιγραφή <b>[1]</b> υπάρχει ήδη:<br><a href="admin-monades-item.php?id=[2]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$monada_descr,$message);
  $message=str_replace('[2]',$row['id_monada'],$message);

  debug_mail(false,'monada metrisis exist symbol',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}

$sql="select * from gks_monades_metrisis where monada_symbol like '".$db_link->escape_string($monada_symbol)."' and id_monada<>".$id;
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $message=gks_lang('Το σύμβολο <b>[1]</b> υπάρχει ήδη στην μονάδα μέτρησης:<br><b>[2]</b><br><a href="admin-monades-item.php?id=[3]" class="gks_link">Προβολή</a>');
  $message=str_replace('[1]',$monada_symbol,$message);
  $message=str_replace('[2]',$row['monada_descr'],$message);
  $message=str_replace('[3]',$row['id_monada'],$message);
  
  
  debug_mail(false,'monada metrisis exist symbol',$message);
  $return = array('success' => false, 'message' => base64_encode($message));
  echo json_encode($return); die();
}

if ($monada_parent_id>0) {
  $out=array();
  $rec_edit=array('id' => $id, 'parent' => $monada_parent_id, 'epi' => $monada_parent_epi, 'epi_rev' => ($monada_parent_epi!=0 ? 1/$monada_parent_epi : 0), 'descr' => $monada_descr, 'symbol' => $monada_symbol);
  gks_monada_convert($id, $monada_parent_id, $out, $rec_edit);
  if ($out['from_circular'] or $out['to_circular']) {
    $mon_steps=array();
    $mon_steps_html='';
    if ($out['from_circular']) {
      foreach ($out['from_array'] as $val) {
        if (in_array($val['descr'], $mon_steps) == false) {
          $mon_steps[]=$val['descr'];
          $mon_steps_html.='<a href="admin-monades-item.php?id='.$val['id'].'" style="" class="gks_link">'.$val['descr'].'</a><br>';
        }
      }
    }
    if ($out['to_circular']) {
      foreach ($out['to_array'] as $val) {
        if (in_array($val['descr'], $mon_steps) == false) {
          $mon_steps[]=$val['descr'];
          $mon_steps_html.='<a href="admin-monades-item.php?id='.$val['id'].'" style="" class="gks_link">'.$val['descr'].'</a><br>';
        }
      }
    }
    
    debug_mail(false,'circularl',print_r($out,true));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Κυκλική αναφορά<br>Όλες οι σχετικές μονάδες μέτρησης').':<br>'.$mon_steps_html));
    echo json_encode($return); die(); 
  }
}

$redirect='';
if ($id==-1) {
  $sql="insert into gks_monades_metrisis (mydate_add,user_id_add,myip) values (now(),".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."');";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  
  $id = $db_link->insert_id; 
  $redirect=base64_encode('admin-monades-item.php?id='.$id); 
}

$sql="update gks_monades_metrisis set 
monada_descr='".$db_link->escape_string($monada_descr)."',
monada_symbol='".$db_link->escape_string($monada_symbol)."',
monada_parent_epi=".$monada_parent_epi.",
monada_parent_id=".$monada_parent_id.",
monada_sortorder=".$monada_sortorder.",
aade_eidos_posotitas_id=".$aade_eidos_posotitas_id.",
monada_peppol_code='".$db_link->escape_string($monada_peppol_code)."',
user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_monada = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  

gks_lang_data_obj_save_exec_php('gks_monades_metrisis',$id);

$filters=array('id_monada'=>$id);
gks_monada_recs_convert($filters);  

$return = array('success' => true, 'message' => base64_encode('OK'), 'redirect'=> $redirect);
echo json_encode($return); die();

