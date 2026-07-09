<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$is_multi=0;if (isset($_POST['is_multi'])) $is_multi=intval($_POST['is_multi']);

$id=0;
if ($is_multi==0) {
  if (isset($_GET['id'])) $id=intval($_GET['id']);
  if (isset($_POST['id'])) $id=intval($_POST['id']);
  
  if ($id<=0) {
    debug_mail(false,'the id is not set','');
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
    echo json_encode($return); die();
  } 
  $ids_array=array();
  $ids_array[]=$id;
} else {

  $ids_str=''; if (isset($_POST['ids_str'])) $ids_str=trim_gks(base64_decode($_POST['ids_str']));
  $ids_array = json_decode($ids_str, true);
  if ($ids_array === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'json_decode error',$_POST['acc_inv_payment_str']);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').' (acc_inv_payment_str)<br>'.gks_lang('Ξαναδοκιμάστε')));
    echo json_encode($return); die();}
  
  //echo '<pre>';print_r($ids_array);die();
  
  
}

$my_page_title=gks_lang('Αποθήκευση κατάστασης εργασίας από παραγγελία');
db_open();
stat_record();

$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_posta_run_time',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}





$newstate='';
if (isset($_POST['newstate'])) $newstate=trim_gks($_POST['newstate']);
if ($newstate!='010draft' and $newstate!='020cancelled' and $newstate!='030pending' and $newstate!='040ready' and $newstate!='050processing' and $newstate!='060pause' and $newstate!='070failed' and $newstate!='100completed') {
  debug_mail(false,'production line state',$newstate);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η νέα κατάσταση')));
  echo json_encode($return); die();    
}

$posto_id=0;
if (isset($_POST['posto_id'])) $posto_id=intval($_POST['posto_id']);
if ($posto_id>0) {
  $sql="select id_production_posto from gks_production_posta where id_production_posto=".$posto_id." limit 1";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'production_posta not found',$sql);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το πόστο').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
    echo json_encode($return); die();  
  }  
}

foreach ($ids_array as $id) {
   
   
  $sql="select * from gks_production_line where id_production_line=".$id." limit 1";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  
  }
  $row_line = $result->fetch_assoc();
  $pl_state=$row_line['pl_state'];
  $order_id=$row_line['order_id'];
  $set_id=trim_gks($row_line['set_id']);
  if ($set_id=='') $set_id=gks_lang('κενό');
  
  if ($pl_state == $newstate) {
    debug_mail(false,'error pl_state is idio',$pl_state.'->'.$newstate);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Η εργασία είναι ήδη σε αυτήν την κατάσταση')));
    echo json_encode($return); die();   
  }
  
  if ($newstate=='010draft') {
    if ($pl_state=='050processing') {
      debug_mail(false,'error pl_state',$pl_state.'->'.$newstate);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Η προηγούμενη κατάσταση δεν μπορεί να είναι είναι').'<br><b>'.getProductionLineStateDescr('050processing').'</b><br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();    
    }
  
    $sql="insert into gks_production_line_time (
      prev_state,curr_state,production_line_id,user_id,time_start,time_end,duration_secs,posto_id,
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip
    ) values (
      '".$db_link->escape_string($pl_state)."',
      '010draft',
      ".$id.",
      ".$my_wp_user_id.",
      now(),
      now(),
      0,
      ".$posto_id.",
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."'
    )";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }  
    
    $sql="update gks_production_line set pl_state='010draft',last_user_id_production=".$my_wp_user_id.",last_posto_id=".$posto_id.",
    mydate_edit=now(),user_id_edit=".$my_wp_user_id.", myip='".$db_link->escape_string($gkIP)."'
    where id_production_line=".$id;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }     
    
  //  $return = array('success' => true, 'message' => base64_encode('OK'));
  //  echo json_encode($return); die();    
  
  }
  else if ($newstate=='020cancelled') {
    if ($pl_state=='100completed') {
      debug_mail(false,'error pl_state',$pl_state.'->'.$newstate);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Η εργασία έχει ολοκληρωθεί').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();    
    }
    if ($pl_state=='050processing') {
      debug_mail(false,'error pl_state',$pl_state.'->'.$newstate);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Η εργασία είναι σε Επεξεργασία').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();    
    }
  
    
    $sql="select * from gks_production_line_time where production_line_id=".$id." and time_end is null order by id_production_line_time desc limit 1";
    $result = $db_link->query($sql);    
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }
    if ($result->num_rows==0) {
      $sql="insert into gks_production_line_time (
        prev_state,curr_state,production_line_id,user_id,time_start,time_end,duration_secs,posto_id,
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip
      ) values (
        '".$db_link->escape_string($pl_state)."',
        '020cancelled',
        ".$id.",
        ".$my_wp_user_id.",
        now(),
        now(),
        0,
        ".$posto_id.",
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."'
      )";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();
      }  
      
      $sql="update gks_production_line set pl_state='020cancelled',last_user_id_production=".$my_wp_user_id.",last_posto_id=".$posto_id.",
      mydate_edit=now(),user_id_edit=".$my_wp_user_id.", myip='".$db_link->escape_string($gkIP)."'
      where id_production_line=".$id;
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();
      }     
      gks_production_order_calc_ergasies_setready($order_id);
  //    $return = array('success' => true, 'message' => base64_encode('OK'));
  //    echo json_encode($return); die();     
      
    } else {
      
      $row_time = $result->fetch_assoc();
      $id_production_line_time=$row_time['id_production_line_time'];
      $duration_secs=time() - strtotime($row_time['time_start']);
      
      $sql="update gks_production_line_time set curr_state='020cancelled', time_end=now(),duration_secs=".$duration_secs.",posto_id=".$posto_id.",
      mydate_edit=now(),user_id_edit=".$my_wp_user_id.",myip='".$db_link->escape_string($gkIP)."'
      where id_production_line_time=".$id_production_line_time;
      $result = $db_link->query($sql);    
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();
      }
      
      $sql="update gks_production_line set pl_state='020cancelled',last_user_id_production=".$my_wp_user_id.",last_posto_id=".$posto_id.",
      mydate_edit=now(),user_id_edit=".$my_wp_user_id.", myip='".$db_link->escape_string($gkIP)."'
      where id_production_line=".$id;
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();
      }     
      gks_production_order_calc_ergasies_setready($order_id);
  //    $return = array('success' => true, 'message' => base64_encode('OK'));
  //    echo json_encode($return); die();       
          
    }  
    
  }
  else if ($newstate=='030pending') {
    if ($pl_state=='050processing') {
      debug_mail(false,'error pl_state',$pl_state.'->'.$newstate);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Η προηγούμενη κατάσταση δεν μπορεί να είναι είναι').'<br><b>'.getProductionLineStateDescr('050processing').'</b><br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();    
    }
  
    $sql="insert into gks_production_line_time (
      prev_state,curr_state,production_line_id,user_id,time_start,time_end,duration_secs,posto_id,
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip
    ) values (
      '".$db_link->escape_string($pl_state)."',
      '030pending',
      ".$id.",
      ".$my_wp_user_id.",
      now(),
      now(),
      0,
      ".$posto_id.",
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."'
    )";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }  
    
    $sql="update gks_production_line set pl_state='030pending',last_user_id_production=".$my_wp_user_id.",last_posto_id=".$posto_id.",
    mydate_edit=now(),user_id_edit=".$my_wp_user_id.", myip='".$db_link->escape_string($gkIP)."'
    where id_production_line=".$id;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }     
    
  //  $return = array('success' => true, 'message' => base64_encode('OK'));
  //  echo json_encode($return); die();    
  
  }
  else if ($newstate=='040ready') {
    if ($pl_state=='050processing') {
      debug_mail(false,'error pl_state',$pl_state.'->'.$newstate);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Η προηγούμενη κατάσταση δεν μπορεί να είναι είναι').'<br><b>'.getProductionLineStateDescr('050processing').'</b><br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();    
    }
  
    $sql="insert into gks_production_line_time (
      prev_state,curr_state,production_line_id,user_id,time_start,time_end,duration_secs,posto_id,
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip
    ) values (
      '".$db_link->escape_string($pl_state)."',
      '040ready',
      ".$id.",
      ".$my_wp_user_id.",
      now(),
      now(),
      0,
      ".$posto_id.",
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."'
    )";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }  
    
    $sql="update gks_production_line set pl_state='040ready',last_user_id_production=".$my_wp_user_id.",last_posto_id=".$posto_id.",
    mydate_edit=now(),user_id_edit=".$my_wp_user_id.", myip='".$db_link->escape_string($gkIP)."'
    where id_production_line=".$id;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }     
    
  //  $return = array('success' => true, 'message' => base64_encode('OK'));
  //  echo json_encode($return); die();    
  
  }
  else if ($newstate=='050processing') {
    if ($pl_state!='040ready' and $pl_state!='060pause') {
      debug_mail(false,'error pl_state',$pl_state.'->'.$newstate);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Η προηγούμενη κατάσταση δεν είναι').'<br><b>'.getProductionLineStateDescr('040ready').'</b><br>'.gks_lang('ή').'<br><b>'.getProductionLineStateDescr('060pause').'</b><br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();    
    }
  
    $sql="insert into gks_production_line_time (
      prev_state,curr_state,production_line_id,user_id,time_start,time_end,duration_secs,posto_id,
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip
    ) values (
      '".$db_link->escape_string($pl_state)."',
      '050processing',
      ".$id.",
      ".$my_wp_user_id.",
      now(),
      null,
      0,
      ".$posto_id.",
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."'
    )";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }  
    
    $sql="update gks_production_line set pl_state='050processing',last_user_id_production=".$my_wp_user_id.",last_posto_id=".$posto_id.",
    mydate_edit=now(),user_id_edit=".$my_wp_user_id.", myip='".$db_link->escape_string($gkIP)."'
    where id_production_line=".$id;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }     
    
  //  $return = array('success' => true, 'message' => base64_encode('OK'));
  //  echo json_encode($return); die();    
  
  }
  else if ($newstate=='060pause') {
    if ($pl_state!='050processing') {
      debug_mail(false,'error pl_state',$pl_state.'->'.$newstate);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Η προηγούμενη κατάσταση δεν είναι').'<br><b>'.getProductionLineStateDescr('050processing').'</b><br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();    
    }
    
    $sql="select * from gks_production_line_time where production_line_id=".$id." and curr_state in ('050processing') order by id_production_line_time desc limit 1";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }
    if ($result->num_rows==0) {
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η προηγούμενη κατάσταση').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();    
    } else {
      $row_time = $result->fetch_assoc();
      $id_production_line_time=$row_time['id_production_line_time'];
      $duration_secs=time() - strtotime($row_time['time_start']);
      
      $sql="update gks_production_line_time set curr_state='060pause', time_end=now(),duration_secs=".$duration_secs.",posto_id=".$posto_id.",
      mydate_edit=now(),user_id_edit=".$my_wp_user_id.",myip='".$db_link->escape_string($gkIP)."'
      where id_production_line_time=".$id_production_line_time;
      $result = $db_link->query($sql);    
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();
      }
      
      $sql="update gks_production_line set pl_state='060pause',last_user_id_production=".$my_wp_user_id.",last_posto_id=".$posto_id.",
      mydate_edit=now(),user_id_edit=".$my_wp_user_id.", myip='".$db_link->escape_string($gkIP)."'
      where id_production_line=".$id;
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();
      }     
      
  //    $return = array('success' => true, 'message' => base64_encode('OK'));
  //    echo json_encode($return); die();       
    }  
  }
  else if ($newstate=='070failed') {
    if ($pl_state=='100completed') {
      debug_mail(false,'error pl_state',$pl_state.'->'.$newstate);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Η εργασία έχει ολοκληρωθεί').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();    
    }
    if ($pl_state!='050processing') {
      debug_mail(false,'error pl_state',$pl_state.'->'.$newstate);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Η εργασία δεν είναι σε Επεξεργασία').'<br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();    
    }
    
    $sql="select * from gks_production_line_time where production_line_id=".$id." and time_end is null order by id_production_line_time desc limit 1";
    $result = $db_link->query($sql);    
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }
    if ($result->num_rows==0) {
      $sql="insert into gks_production_line_time (
        prev_state,curr_state,production_line_id,user_id,time_start,time_end,duration_secs,posto_id,
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip
      ) values (
        '".$db_link->escape_string($pl_state)."',
        '070failed',
        ".$id.",
        ".$my_wp_user_id.",
        now(),
        now(),
        0,
        ".$posto_id.",
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."'
      )";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();
      }  
      
      $sql="update gks_production_line set pl_state='070failed',last_user_id_production=".$my_wp_user_id.",last_posto_id=".$posto_id.",
      mydate_edit=now(),user_id_edit=".$my_wp_user_id.", myip='".$db_link->escape_string($gkIP)."'
      where id_production_line=".$id;
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();
      }     
      gks_production_order_calc_ergasies_setready($order_id);
  //    $return = array('success' => true, 'message' => base64_encode('OK'));
  //    echo json_encode($return); die();     
      
    } else {
      
      $row_time = $result->fetch_assoc();
      $id_production_line_time=$row_time['id_production_line_time'];
      $duration_secs=time() - strtotime($row_time['time_start']);
      
      $sql="update gks_production_line_time set curr_state='070failed', time_end=now(),duration_secs=".$duration_secs.",posto_id=".$posto_id.",
      mydate_edit=now(),user_id_edit=".$my_wp_user_id.",myip='".$db_link->escape_string($gkIP)."'
      where id_production_line_time=".$id_production_line_time;
      $result = $db_link->query($sql);    
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();
      }
      
      $sql="update gks_production_line set pl_state='070failed',last_user_id_production=".$my_wp_user_id.",last_posto_id=".$posto_id.",
      mydate_edit=now(),user_id_edit=".$my_wp_user_id.", myip='".$db_link->escape_string($gkIP)."'
      where id_production_line=".$id;
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();
      }     
      gks_production_order_calc_ergasies_setready($order_id);
  //    $return = array('success' => true, 'message' => base64_encode('OK'));
  //    echo json_encode($return); die();       
          
    }  
    
  }
  else if ($newstate=='100completed') {
    //if ($pl_state!='040ready' and $pl_state!='050processing' and $pl_state!='060pause') {
    if ($pl_state!='010draft' and $pl_state!='040ready' and $pl_state!='060pause' and $pl_state!='050processing') {
      debug_mail(false,'error pl_state',$pl_state.'->'.$newstate);
      $return = array('success' => false, 'message' => base64_encode(gks_lang('Η προηγούμενη κατάσταση δεν είναι').'<br><b>'.getProductionLineStateDescr('050processing').'</b><br>'.gks_lang('Ανανεώστε την σελίδα')));
      echo json_encode($return); die();    
    }
    $sql="select * from gks_production_line_time where production_line_id=".$id." and time_end is null order by id_production_line_time desc limit 1";
    $result = $db_link->query($sql);    
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();
    }
    if ($result->num_rows==0) {
      $sql="insert into gks_production_line_time (
        prev_state,curr_state,production_line_id,user_id,time_start,time_end,duration_secs,posto_id,
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip
      ) values (
        '".$db_link->escape_string($pl_state)."',
        '100completed',
        ".$id.",
        ".$my_wp_user_id.",
        now(),
        now(),
        0,
        ".$posto_id.",
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."'
      )";
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();
      }  
      
      $sql="update gks_production_line set pl_state='100completed',last_user_id_production=".$my_wp_user_id.",last_posto_id=".$posto_id.",
      mydate_edit=now(),user_id_edit=".$my_wp_user_id.", myip='".$db_link->escape_string($gkIP)."'
      where id_production_line=".$id;
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();
      }     
      gks_production_order_calc_ergasies_setready($order_id);
  //    $return = array('success' => true, 'message' => base64_encode('OK'));
  //    echo json_encode($return); die();     
      
    } else {
      
      $row_time = $result->fetch_assoc();
      $id_production_line_time=$row_time['id_production_line_time'];
      $duration_secs=time() - strtotime($row_time['time_start']);
      
      $sql="update gks_production_line_time set curr_state='100completed', time_end=now(),duration_secs=".$duration_secs.",posto_id=".$posto_id.",
      mydate_edit=now(),user_id_edit=".$my_wp_user_id.",myip='".$db_link->escape_string($gkIP)."'
      where id_production_line_time=".$id_production_line_time;
      $result = $db_link->query($sql);    
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();
      }
      
      $sql="update gks_production_line set pl_state='100completed',last_user_id_production=".$my_wp_user_id.",last_posto_id=".$posto_id.",
      mydate_edit=now(),user_id_edit=".$my_wp_user_id.", myip='".$db_link->escape_string($gkIP)."'
      where id_production_line=".$id;
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();
      }     
      gks_production_order_calc_ergasies_setready($order_id);
  //    $return = array('success' => true, 'message' => base64_encode('OK'));
  //    echo json_encode($return); die();       
          
    }
  }
  
  $sql="update gks_orders_products_sets set last_user_id_set=".$my_wp_user_id." where order_id=".$order_id." and set_id='".$db_link->escape_string($set_id)."'";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();
  }    
  
  //$return = array('success' => false, 'message' => base64_encode($sql));
  //echo json_encode($return); die();
}


$return = array('success' => true, 'message' => base64_encode('OK'));
echo json_encode($return); die();
