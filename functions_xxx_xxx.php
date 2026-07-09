<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_other_entity_get_data($doc_table,$id_xxx_xxx_other_entity,$entity_user_id=-1,$address_extra=-1) {
  global $db_link;
  $d=array();
  $d['afm']='';
  $d['name']='';
  $d['sub_name']='';
  $d['branch']='';
  $d['odos']='';
  $d['arithmos']='';
  $d['orofos']='';
  $d['perioxi']='';
  $d['poli']='';
  $d['tk']='';
  $d['country_id']=0;
  $d['country_initials']='';
  $d['country_name']='';
  $d['nomos_id']=0;
  $d['nomos_descr']='';
  $d['address']='';
  
  $xxx_xxx='';
  if ($doc_table=='gks_acc_inv') {
    $xxx_xxx='acc_inv';
  } else if ($doc_table=='gks_whi_mov') {
    $xxx_xxx='whi_mov';
  } else {
    debug_mail(false,'doc_table not set',$doc_table);
    $return = array('success' => false, 'message' => base64_encode('doc_table not set'));
    echo json_encode($return); die();
  }
  
  if ($id_xxx_xxx_other_entity > 0) {
    $sql="SELECT gks_".$xxx_xxx."_other_entity.*, 
    gks_country.country_initials, gks_country.country_name, 
    gks_nomoi.nomos_descr
    FROM (gks_".$xxx_xxx."_other_entity 
    LEFT JOIN gks_country ON gks_".$xxx_xxx."_other_entity.entity_country_id = gks_country.id_country) 
    LEFT JOIN gks_nomoi ON gks_".$xxx_xxx."_other_entity.entity_nomos_id = gks_nomoi.id_nomos
    WHERE gks_".$xxx_xxx."_other_entity.id_".$xxx_xxx."_other_entity=".$id_xxx_xxx_other_entity;
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }    
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $d['afm']=trim_gks($row['entity_afm']);
      $d['name']=trim_gks($row['entity_name']);
      $d['sub_name']=trim_gks($row['entity_sub_name']);
      $d['branch']=trim_gks($row['entity_branch']);
      $d['odos']=trim_gks($row['entity_odos']);
      $d['arithmos']=trim_gks($row['entity_arithmos']);
      $d['orofos']=trim_gks($row['entity_orofos']);
      $d['perioxi']=trim_gks($row['entity_perioxi']);
      $d['poli']=trim_gks($row['entity_poli']);
      $d['tk']=trim_gks($row['entity_tk']);
      $d['country_id']=intval($row['entity_country_id']);
      $d['country_initials']=trim_gks($row['country_initials']);
      $d['country_name']=trim_gks($row['country_name']);
      $d['nomos_id']=intval($row['entity_nomos_id']);
      $d['nomos_descr']=trim_gks($row['nomos_descr']);
      
    }    

    
  } else if ($entity_user_id>0) {
    $sql="SELECT gks_users.*, 
    gks_country.country_initials, gks_country.country_name, 
    gks_nomoi.nomos_descr
    FROM (gks_users LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country) LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos
    WHERE gks_users.user_id=".$entity_user_id;
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }    
    if ($result->num_rows>0) {
      $row = $result->fetch_assoc();
      $d['afm']=trim_gks($row['afm']);
      $d['name']=trim_gks($row['eponimia']);
      $d['sub_name']=gks_lang('Κεντρικό');
      $d['branch']=trim_gks($row['ma_branch']);
      $d['odos']=trim_gks($row['ma_odos']);
      $d['arithmos']=trim_gks($row['ma_arithmos']);
      $d['orofos']=trim_gks($row['ma_orofos']);
      $d['perioxi']=trim_gks($row['ma_perioxi']);
      $d['poli']=trim_gks($row['ma_poli']);
      $d['tk']=trim_gks($row['ma_tk']);
      $d['country_id']=intval($row['ma_country_id']);
      $d['country_initials']=trim_gks($row['country_initials']);
      $d['country_name']=trim_gks($row['country_name']);
      $d['nomos_id']=intval($row['ma_nomos_id']);
      $d['nomos_descr']=trim_gks($row['nomos_descr']);
      
    }
    if ($address_extra>0) {
      $sql="SELECT gks_users_extra_address.*,
      gks_country.country_initials, gks_country.country_name,
      gks_nomoi.nomos_descr
      FROM (gks_users_extra_address 
      LEFT JOIN gks_country ON gks_users_extra_address.ea_country_id = gks_country.id_country) 
      LEFT JOIN gks_nomoi ON gks_users_extra_address.ea_nomos_id = gks_nomoi.id_nomos
      where id_users_extra_address=".$address_extra."
      and gks_users_extra_address.user_id=".$entity_user_id;
      $result = $db_link->query($sql);
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die(); }    
      if ($result->num_rows>0) {
        $row = $result->fetch_assoc();
        //$d['afm']=trim_gks($row['afm']);
        //$d['name']=trim_gks($row['eponimia']);
        $d['sub_name']=trim_gks($row['ea_name']);
        $d['branch']=trim_gks($row['ea_branch']);
        $d['odos']=trim_gks($row['ea_odos']);
        $d['arithmos']=trim_gks($row['ea_arithmos']);
        $d['orofos']=trim_gks($row['ea_orofos']);
        $d['perioxi']=trim_gks($row['ea_perioxi']);
        $d['poli']=trim_gks($row['ea_poli']);
        $d['tk']=trim_gks($row['ea_tk']);
        $d['country_id']=intval($row['ea_country_id']);
        $d['country_initials']=trim_gks($row['country_initials']);
        $d['country_name']=trim_gks($row['country_name']);
        $d['nomos_id']=intval($row['ea_nomos_id']);
        $d['nomos_descr']=trim_gks($row['nomos_descr']);
        
      }      
      
    }
    
  }
  
  $d['address']='';
  $temp=[];
  if ($d['odos']!='') $temp[]=trim_gks($d['odos'].' '.$d['arithmos']);
  if ($d['orofos']!='') $temp[]=$d['orofos'];
  if ($d['perioxi']!='') $temp[]=$d['perioxi'];
  if ($d['poli']!='') $temp[]=$d['poli'];
  if ($d['tk']!='') $temp[]=$d['tk'];
  if ($d['nomos_descr']!='') $temp[]=$d['nomos_descr'];
  if ($d['country_name']!='') $temp[]=$d['country_name'];
  $d['address']=implode(' ',$temp);
  
  $html=[];
  $html[]=gks_lang('ΑΦΜ').': '.$d['afm'];
  $html[]=gks_lang('Επωνυμία').': '.$d['name'];
  $html[]=gks_lang('Υποκατάστημα').': '.$d['sub_name'];
  $html[]=gks_lang('Αριθμός Εγκατάστασης').': '.$d['branch'];
  $html[]=gks_lang('Διεύθυνση').': '.$d['address'];

  $ret=[];
  $ret['data']=$d;
  $ret['html']=implode(', ',$html);
  return $ret;
  
}

function gks_multiple_connected_marks_get_data($mark,$acc_inv_id,$acc_pay_id,$whi_mov_id) {
  return gks_correlated_invoices_get_data($mark,$acc_inv_id,$acc_pay_id,$whi_mov_id);
}

function gks_correlated_invoices_get_data($mark,$acc_inv_id,$acc_pay_id,$whi_mov_id) {
  global $db_link;
  
  $d=[];
  $html=[];
  
  $mark=trim_gks($mark);
  $acc_inv_id=intval($acc_inv_id);
  $acc_pay_id=intval($acc_pay_id);
  $whi_mov_id=intval($whi_mov_id);
  
  if ($acc_inv_id>0 or $mark!='') {
    $sql_coi="select gks_acc_inv.id_acc_inv,
    gks_acc_inv.aade_invoicemark,
    gks_acc_inv.inv_date, 
    gks_acc_inv.inv_acc_seira_code, 
    gks_acc_inv.inv_acc_number_int, 
    gks_acc_inv.inv_state, 
    gks_acc_inv.user_id, 
    gks_acc_inv.afm, 
    ".GKS_WP_TABLE_PREFIX."users.gks_nickname,
    gks_acc_inv.products_posotita,
    gks_acc_inv.gks_price_net, gks_acc_inv.gks_price_total,
    gks_acc_journal.acc_journal_descr
    FROM (gks_acc_inv
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_inv.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
    LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal
    where ";
    $mywhere=[];
    if ($acc_inv_id>0) {
      $mywhere[]="id_acc_inv=".$acc_inv_id;
    }
    if ($mark!='')  {
      $mywhere[]="aade_invoicemark='".$db_link->escape_string($mark)."'";
    }
    $sql_coi.=implode(' or ',$mywhere);
    $sql_coi.=" order by gks_acc_inv.id_acc_inv desc limit 1";
    
    $result_coi = $db_link->query($sql_coi);        
    if (!$result_coi) {debug_mail(false,'error sql',$sql_coi); die('sql error');}
    if ($result_coi->num_rows>=1) {
      $row_coi = $result_coi->fetch_assoc();
        
      $d['doc_table']='gks_acc_inv';
      $d['id']=$row_coi['id_acc_inv'];
      $d['aade_invoicemark']=$row_coi['aade_invoicemark'];
      $d['date']=$row_coi['inv_date'];
      $d['acc_journal_descr']=$row_coi['acc_journal_descr'];
      $d['seira_code']=$row_coi['inv_acc_seira_code'];
      $d['number_int']=$row_coi['inv_acc_number_int']; 
      $d['state']=$row_coi['inv_state'];
      $d['user_id']=$row_coi['user_id'];
      $d['afm']=$row_coi['afm'];
      $d['gks_nickname']=$row_coi['gks_nickname'];
      $d['products_posotita']=$row_coi['products_posotita'];
      $d['gks_price_net']=$row_coi['gks_price_net'];
      $d['gks_price_total']=$row_coi['gks_price_total'];
      
      $mark=$d['aade_invoicemark'];
    }
  }
  
  if ($acc_pay_id>0 or $mark!='') {
    $sql_coi="select gks_acc_pay.id_acc_pay,
    gks_acc_pay.aade_invoicemark,
    gks_acc_pay.pay_date, 
    gks_acc_pay.pay_acc_seira_code, 
    gks_acc_pay.pay_acc_number_int, 
    gks_acc_pay.pay_state, 
    gks_acc_pay.user_id, 
    gks_users.afm, 
    ".GKS_WP_TABLE_PREFIX."users.gks_nickname,
    0 as products_posotita,
    0 as gks_price_net, 
    gks_acc_pay.gks_price_total,
    gks_acc_journal.acc_journal_descr
    FROM ((gks_acc_pay
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_pay.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
    LEFT JOIN gks_users ON gks_acc_pay.user_id = gks_users.user_id)
    LEFT JOIN gks_acc_journal ON gks_acc_pay.pay_acc_journal_id = gks_acc_journal.id_acc_journal

    where ";
    $mywhere=[];
    if ($acc_pay_id>0) {
      $mywhere[]="id_acc_pay=".$acc_pay_id;
    }
    if ($mark!='')  {
      $mywhere[]="aade_invoicemark='".$db_link->escape_string($mark)."'";
    }
    $sql_coi.=implode(' or ',$mywhere);
    $sql_coi.=" order by gks_acc_pay.id_acc_pay desc limit 1";
    
    $result_coi = $db_link->query($sql_coi);        
    if (!$result_coi) {debug_mail(false,'error sql',$sql_coi); die('sql error');}
    if ($result_coi->num_rows>=1) {
      $row_coi = $result_coi->fetch_assoc();
        
      $d['doc_table']='gks_acc_pay';
      $d['id']=$row_coi['id_acc_pay'];
      $d['aade_invoicemark']=$row_coi['aade_invoicemark'];
      $d['date']=$row_coi['pay_date'];
      $d['acc_journal_descr']=$row_coi['acc_journal_descr'];
      $d['seira_code']=$row_coi['pay_acc_seira_code'];
      $d['number_int']=$row_coi['pay_acc_number_int']; 
      $d['state']=$row_coi['pay_state'];
      $d['user_id']=$row_coi['user_id'];
      $d['afm']=$row_coi['afm'];
      $d['gks_nickname']=$row_coi['gks_nickname'];
      $d['products_posotita']=$row_coi['products_posotita'];
      $d['gks_price_net']=null;
      $d['gks_price_total']=$row_coi['gks_price_total'];
      
      $mark=$d['aade_invoicemark'];
    }
  }  
  
  
  if (count($d)==0 and ($whi_mov_id>0 or $mark!='')) {
    $sql_coi="select gks_whi_mov.id_whi_mov,
    gks_whi_mov.aade_invoicemark,
    gks_whi_mov.mov_date, 
    gks_whi_mov.mov_whi_seira_code, 
    gks_whi_mov.mov_whi_number_int, 
    gks_whi_mov.mov_state, 
    gks_whi_mov.user_id, 
    gks_whi_mov.afm, 
    ".GKS_WP_TABLE_PREFIX."users.gks_nickname,
    gks_whi_mov.products_posotita,
    gks_acc_journal.acc_journal_descr
    FROM (gks_whi_mov
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_whi_mov.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
    LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal

    where ";
    $mywhere=[];
    if ($whi_mov_id>0) {
      $mywhere[]="id_whi_mov=".$whi_mov_id;
    }
    if ($mark!='')  {
      $mywhere[]="aade_invoicemark='".$db_link->escape_string($mark)."'";
    }
    $sql_coi.=implode(' or ',$mywhere);
    $sql_coi.=" order by gks_whi_mov.id_whi_mov desc limit 1";
    
    $result_coi = $db_link->query($sql_coi);        
    if (!$result_coi) {debug_mail(false,'error sql',$sql_coi); die('sql error');}
    if ($result_coi->num_rows>=1) {
      $row_coi = $result_coi->fetch_assoc();
        
      $d['doc_table']='gks_whi_mov';
      $d['id']=$row_coi['id_whi_mov'];
      $d['aade_invoicemark']=$row_coi['aade_invoicemark'];
      $d['date']=$row_coi['mov_date'];
      $d['acc_journal_descr']=$row_coi['acc_journal_descr'];
      $d['seira_code']=$row_coi['mov_whi_seira_code'];
      $d['number_int']=$row_coi['mov_whi_number_int']; 
      $d['state']=$row_coi['mov_state'];
      $d['user_id']=$row_coi['user_id'];
      $d['afm']=$row_coi['afm'];
      $d['gks_nickname']=$row_coi['gks_nickname'];
      $d['products_posotita']=$row_coi['products_posotita'];
      $d['gks_price_net']=null;
      $d['gks_price_total']=null;
      
      $mark=$d['aade_invoicemark'];
    }
  }
  
  if ($mark!='') $html[]=gks_lang('ΜΑΡΚ').': <b>'.$mark.'</b>';
  if (isset($d['doc_table'])) {
    if ($d['doc_table']=='gks_acc_inv') {

      if (isset($d['id'])) $html[]='ID: <a href="admin-acc-inv-item.php?id='.$d['id'].'">#'.$d['id'].'</a>';
    } else if ($d['doc_table']=='gks_whi_mov') {
      if (isset($d['id'])) $html[]='ID: <a href="admin-whi-mov-item.php?id='.$d['id'].'">#'.$d['id'].'</a>';
    }
  }
  if (isset($d['date'])) $html[]=gks_lang('Ημερομηνία').': '.showDate(strtotime($d['date']),'d/m/Y H:i',1);
  if (isset($d['acc_journal_descr'])) $html[]=gks_lang('Ημερολόγιο').': '.$d['acc_journal_descr'];
  if (isset($d['seira_code'])) $html[]=gks_lang('Σειρά').': '.$d['seira_code'];
  if (isset($d['number_int'])) $html[]=gks_lang('Αριθμός').': '.$d['number_int'];
  if (isset($d['state'])) {
    if ($d['doc_table']=='gks_acc_inv') {
      $html[]=gks_lang('Κατάσταση').': <span class="acc_inv_state_'.$d['state'].'">'.getAccInvStateDescr($d['state']).'</span>';
    } else if ($d['doc_table']=='gks_whi_mov') {
      $html[]=gks_lang('Κατάσταση').': <span class="whi_mov_state_'.$d['state'].'">'.getWhiMovStateDescr($d['state']).'</span>';
    }
  }
  
  if (isset($d['user_id']) and isset($d['gks_nickname'])) {
    $html[]=gks_lang('Επαφή').': <a href="admin-users-item.php?id='.$d['user_id'].'">'.$d['gks_nickname'].'</a>';
  }
  if (isset($d['afm'])) $html[]=gks_lang('ΑΦΜ').': '.$d['afm'];
  
  if (isset($d['products_posotita'])) $html[]=gks_lang('Ποσότητα').': '.myNumberFormatNo0Local($d['products_posotita']);
  if (isset($d['gks_price_net'])) $html[]=gks_lang('Τιμή').': <b>'.myCurrencyFormat($d['gks_price_net']).'</b>';
  if (isset($d['gks_price_total'])) $html[]=gks_lang('Μικτό').': '.myCurrencyFormat($d['gks_price_total']);
  
  
  //$html[]='data: <pre>'.print_r($d,true).'</pre>';
  
  $ret=[];
  $ret['data']=$d;
  $ret['html']=implode(', ',$html);
  return $ret;

}
