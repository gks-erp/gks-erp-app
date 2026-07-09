<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();

$warehouse_id=0;
if (isset($_POST['warehouse_id'])) $warehouse_id=intval($_POST['warehouse_id']);
if ($warehouse_id<=0) {
  debug_mail(false,'the warehouse_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η αποθήκη')));
  echo json_encode($return); die();
}

$product_id=0;
if (isset($_POST['product_id'])) $product_id=intval($_POST['product_id']);
if ($product_id<=0) {
  debug_mail(false,'the product_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το είδος')));
  echo json_encode($return); die();
}

$lot_product_id=0;
if (isset($_POST['lot_product_id'])) $lot_product_id=intval($_POST['lot_product_id']);
if ($lot_product_id<=0) {
  debug_mail(false,'the lot_product_id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί η παρτίδα/serial number')));
  echo json_encode($return); die();
}

$posotitaonhand=0;
if (isset($_POST['posotitaonhand'])) $posotitaonhand=floatval($_POST['posotitaonhand']);

if ($posotitaonhand<0) {
  debug_mail(false,'posotitaonhand is negative','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν μπορεί η ποσότητα να είναι αρνητική')));
  echo json_encode($return); die(); }
  


$my_page_title=gks_lang('Αποθήκευση κίνησης απογραφής παρτίδας').' '.$warehouse_id.' '.$product_id.' '.$posotitaonhand;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_whi_mov_balance_lots_serials_apografi','add',0);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



$sql="select * from gks_warehouses where id_warehouse=".$warehouse_id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows<=0) {
  debug_mail(false,'warehouse not found ID: '.$warehouse_id,$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η αποθήκη με ID').': '.$warehouse_id));
  echo json_encode($return); die();}
$row_warehouse = $result->fetch_assoc();

$sql="select * from gks_eshop_products where id_product=".$product_id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows<=0) {
  debug_mail(false,'product not found: '.$product_id,$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε το είδος με ID').': '.$product_id));
  echo json_encode($return); die();}
$row_product = $result->fetch_assoc();

$sql="select * from gks_eshop_product_lots where id_lot_product=".$lot_product_id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows<=0) {
  debug_mail(false,'lot_product_id not found ID: '.$lot_product_id,$sql);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η παρτίδα/serial number με ID').': '.$lot_product_id));
  echo json_encode($return); die();}
$row_product = $result->fetch_assoc();






$all_products_for_balance=array($product_id);
gks_whi_mov_balance_calc($all_products_for_balance);
//gks_whi_mov_lots_serials_balance_calc($all_products_for_balance);


$curr_balance=0;
$sql="select balance from gks_warehouse_balance_lots_serials
where lot_product_id=".$lot_product_id." and warehouse_id=".$warehouse_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $curr_balance=floatval($row['balance']);
}

$diafora = $posotitaonhand - $curr_balance;
//if ($diafora==0) {
//  debug_mail(false,'diafora is zero',$sql);
//  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει νόημα αυτή η καταχώρηση, διότι ήδη έχετε αυτό το υπόλοιπο')));
//  echo json_encode($return); die();    
//}



$company_id=intval($row_warehouse['company_id']);
$company_sub_id=intval($row_warehouse['company_sub_id']);

$sql="select id_acc_journal from gks_acc_journal
where company_id=".$company_id." and company_sub_id=".$company_sub_id." and acc_eidos_parastatikou_id=999 and is_disable=0
order by id_acc_journal";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $id_acc_journal=$row['id_acc_journal'];
} else {
  $cc=0;
  $acc_journal_code=gks_lang('ΑΠΟΓ');
  $acc_journal_descr=gks_lang('Απογραφή');
  do {
    $sql="select id_acc_journal from gks_acc_journal 
    where acc_journal_code like '".$db_link->escape_string($acc_journal_code)."' 
    and acc_journal_descr like '".$db_link->escape_string($acc_journal_descr)."'
    and company_id=".$company_id." 
    and company_sub_id=".$company_sub_id;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    if ($result->num_rows==0) break; 
    $cc++;
    $acc_journal_code=gks_lang('ΑΠΟΓ').$cc;
    $acc_journal_descr=gks_lang('Απογραφή').' '.$cc;
  } while(true);
  
  $sql="select max(sortorder) as cc from gks_acc_journal 
  where company_id=".$company_id." 
  and company_sub_id=".$company_sub_id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  $sortorder=1000;
  if ($result->num_rows>0) {
    $row = $result->fetch_assoc();
    if (isset($row['cc']) and intval($row['cc'])>0) $sortorder=intval($row['cc']) + 1;
  }
  
  $sql="insert into gks_acc_journal (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    acc_journal_code,acc_journal_descr,
    company_id,company_sub_id,
    acc_eidos_parastatikou_id,
    is_disable,sortorder
  ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    '".$db_link->escape_string($acc_journal_code)."','".$db_link->escape_string($acc_journal_descr)."',
    ".$company_id.",".$company_sub_id.",
    999,
    0,".$sortorder."
  )";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  $id_acc_journal=$db_link->insert_id;
}

$sql="select id_acc_seira from gks_acc_seires where acc_journal_id=".$id_acc_journal." and is_disable=0";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $id_acc_seira=$row['id_acc_seira'];
} else {
  $cc=0;
  $seira_code=gks_lang('ΑΠΟΓ');
  $seira_descr=gks_lang('Απογραφή');
  do {
    $sql="select id_acc_seira from gks_acc_seires 
    where seira_code like '".$db_link->escape_string($seira_code)."' 
    and seira_descr like '".$db_link->escape_string($seira_descr)."'
    and acc_journal_id=".$id_acc_journal;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    if ($result->num_rows==0) break; 
    $cc++;
    $seira_code=gks_lang('ΑΠΟΓ').$cc;
    $seira_descr=gks_lang('Απογραφή').' '.$cc;
  } while(true);
  
  $sql="select max(sortorder) as cc from gks_acc_seires 
  where acc_journal_id=".$id_acc_journal;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  $sortorder=1000;
  if ($result->num_rows>0) {
    $row = $result->fetch_assoc();
    if (isset($row['cc']) and intval($row['cc'])>0) $sortorder=intval($row['cc']) + 1;
  }
  
  $sql="insert into gks_acc_seires (
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
    acc_journal_id,is_disable,
    seira_code,seira_descr,
    prefix,suffix,
    number_size,number_step,next_number,
    sortorder,is_xeirografi
  ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$id_acc_journal.",0,
    '".$db_link->escape_string($seira_code)."','".$db_link->escape_string($seira_descr)."',
    '','',
    6,1,1,
    ".$sortorder.",0
  )";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  $id_acc_seira=$db_link->insert_id;
}




$sql="select id_whi_mov from gks_whi_mov 
where company_id=".$company_id."
and company_sub_id=".$company_sub_id."
and mov_whi_journal_id=".$id_acc_journal."
and mov_whi_seira_id=".$id_acc_seira."
and cancel_for_whi_mov_id=0
and credit_memo_for_whi_mov_id=0
and mov_date>='".$today."' and gks_whi_mov.mov_date < DATE_ADD('".$today."', INTERVAL 1 DAY)
and warehouses_id_from=0
and warehouses_id_to=".$warehouse_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $id_whi_mov=$row['id_whi_mov'];
} else {
  $sqlF=''; $sqlV='';
  $sqlF.='cancel_for_whi_mov_id,'; $sqlV.="0,";
  $sqlF.='credit_memo_for_whi_mov_id,'; $sqlV.="0,";
  //$sqlF.='import_mov_whi_seira_code,'; $sqlV.="'',";
  //$sqlF.='import_mov_whi_number_str,'; $sqlV.="'',";
  //$sqlF.='import_eidos_parastatikou_aade_code,'; $sqlV.="'',";
  $sqlF.='mov_guid,'; $sqlV.="'".$db_link->escape_string(guid_for_whi_mov())."',";
  $sqlF.='mov_date,'; $sqlV.="now(),";
  $sqlF.='mydate_add,'; $sqlV.="now(),";
  $sqlF.='mydate_edit,'; $sqlV.="now(),";
  $sqlF.='user_id_add,'; $sqlV.=$my_wp_user_id.",";
  $sqlF.='user_id_edit,'; $sqlV.=$my_wp_user_id.",";
  $sqlF.='myip,'; $sqlV.="'".$db_link->escape_string($gkIP)."',";
  $sqlF.='company_id,'; $sqlV.=$company_id.",";
  $sqlF.='company_sub_id,'; $sqlV.=$company_sub_id.",";
  $sqlF.='mov_whi_journal_id,'; $sqlV.=$id_acc_journal.",";
  $sqlF.='mov_whi_seira_id,'; $sqlV.=$id_acc_seira.",";
  //$sqlF.='mov_whi_seira_code,'; $sqlV.=",";
  //$sqlF.='mov_whi_number_int,'; $sqlV.=",";
  //$sqlF.='mov_whi_number_str,'; $sqlV.=",";
  $sqlF.='mov_whi_ekdosi_date,'; $sqlV.="now(),";
  $sqlF.='mov_state,'; $sqlV.="'090ekdosi',";
  $sqlF.='user_id,'; $sqlV.="0,";
  //$sqlF.='user_email,'; $sqlV.=",";
  //$sqlF.='user_first_name,'; $sqlV.=",";
  //$sqlF.='user_last_name,'; $sqlV.=",";
  //$sqlF.='user_mobile,'; $sqlV.=",";
  //$sqlF.='user_lang,'; $sqlV.=",";
  //$sqlF.='whi_calendar_id,'; $sqlV.=",";
  //$sqlF.='whi_seira_id,'; $sqlV.=",";
  //$sqlF.='eponimia,'; $sqlV.=",";
  //$sqlF.='title,'; $sqlV.=",";
  //$sqlF.='afm,'; $sqlV.=",";
  //$sqlF.='doy,'; $sqlV.=",";
  //$sqlF.='epaggelma,'; $sqlV.=",";
  //$sqlF.='ma_odos,'; $sqlV.=",";
  //$sqlF.='ma_perioxi,'; $sqlV.=",";
  //$sqlF.='ma_poli,'; $sqlV.=",";
  //$sqlF.='ma_tk,'; $sqlV.=",";
  //$sqlF.='ma_country_id,'; $sqlV.=",";
  //$sqlF.='ma_nomos_id,'; $sqlV.=",";
  //$sqlF.='ma_apostoli_number,'; $sqlV.=",";
  //$sqlF.='address_extra,'; $sqlV.=",";
  //$sqlF.='destination_data_name,'; $sqlV.=",";
  //$sqlF.='destination_data_phone,'; $sqlV.=",";
  //$sqlF.='destination_data_odos,'; $sqlV.=",";
  //$sqlF.='destination_data_perioxi,'; $sqlV.=",";
  //$sqlF.='destination_data_poli,'; $sqlV.=",";
  //$sqlF.='destination_data_tk,'; $sqlV.=",";
  //$sqlF.='destination_data_country_id,'; $sqlV.=",";
  //$sqlF.='destination_data_nomos_id,'; $sqlV.=",";
  //$sqlF.='destination_data_apostoli_number,'; $sqlV.=",";
  //$sqlF.='user_notes,'; $sqlV.=",";
  //$sqlF.='idiotites,'; $sqlV.=",";
  //$sqlF.='note_logistirio,'; $sqlV.=",";
  //$sqlF.='note_doc,'; $sqlV.=",";
  $sqlF.='fiscal_position_id,'; $sqlV.="0,";
  $sqlF.='pricelist_id,'; $sqlV.="0,";
  //$sqlF.='is_other,'; $sqlV.=",";
  //$sqlF.='other_first_name,'; $sqlV.=",";
  //$sqlF.='other_last_name,'; $sqlV.=",";
  //$sqlF.='other_email,'; $sqlV.=",";
  //$sqlF.='other_mobile,'; $sqlV.=",";
  //$sqlF.='other_lang,'; $sqlV.=",";
  //$sqlF.='other_ma_odos,'; $sqlV.=",";
  //$sqlF.='other_ma_perioxi,'; $sqlV.=",";
  //$sqlF.='other_ma_poli,'; $sqlV.=",";
  //$sqlF.='other_ma_tk,'; $sqlV.=",";
  //$sqlF.='other_ma_country_id,'; $sqlV.=",";
  //$sqlF.='other_ma_nomos_id,'; $sqlV.=",";
  //$sqlF.='products_posotita,'; $sqlV.=",";
  //$sqlF.='products_varos,'; $sqlV.=",";
  //$sqlF.='products_ogos,'; $sqlV.=",";
  //$sqlF.='products_ogos_max_x,'; $sqlV.=",";
  //$sqlF.='products_ogos_max_y,'; $sqlV.=",";
  //$sqlF.='products_ogos_max_z,'; $sqlV.=",";
  //$sqlF.='products_need_apostoli,'; $sqlV.=",";
  $sqlF.='kostos_apostolis,'; $sqlV.="0,";
  $sqlF.='tropos_apostolis,'; $sqlV.="1,";
  //$sqlF.='tropos_apostolis_json,'; $sqlV.=",";
  $sqlF.='session_id,'; $sqlV.="'".$db_link->escape_string($_gks_id_session)."',";
  //$sqlF.='session_basket,'; $sqlV.=",";
  //$sqlF.='bank_deposit_9digit,'; $sqlV.=",";
  //$sqlF.='delivery_id_8,'; $sqlV.=",";
  //$sqlF.='delivery_number,'; $sqlV.=",";
  //$sqlF.='def_ekptosi,'; $sqlV.=",";
  //$sqlF.='dispatch_date,'; $sqlV.=",";
  //$sqlF.='vehicle_number,'; $sqlV.=",";
  $sqlF.='aade_skopos_diakinisis_id,'; $sqlV.="0,";
  //$sqlF.='print_date,'; $sqlV.=",";
  //$sqlF.='print_file_name,'; $sqlV.=",";
  //$sqlF.='print_file_url,'; $sqlV.=",";
  //$sqlF.='print_user_id,'; $sqlV.=",";
  //$sqlF.='print_mov_state,'; $sqlV.=",";
  //$sqlF.='assigned_id,'; $sqlV.=",";
  //$sqlF.='crm_channel_id,'; $sqlV.=",";
  //$sqlF.='crm_channel_contact_id,'; $sqlV.=",";
  //$sqlF.='crm_channel_campain_id,'; $sqlV.=",";
  //$sqlF.='crm_channel_url,'; $sqlV.=",";
  //$sqlF.='crm_channel_text,'; $sqlV.=",";
  $sqlF.='warehouses_id_from,'; $sqlV.="0,";
  $sqlF.='warehouses_id_to,'; $sqlV.=$warehouse_id.",";

  $sqlF=substr($sqlF, 0, strlen($sqlF)-1);
  $sqlV=substr($sqlV, 0, strlen($sqlV)-1);
  $sql="insert into gks_whi_mov (".$sqlF.") values (".$sqlV.")";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  $id_whi_mov=$db_link->insert_id;

  $id=$id_whi_mov;
  $mov_whi_number_int_old=0;
  $mov_whi_seira_id=$id_acc_seira;
  gks_whi_mov_get_ekdosi_numbers();
  
  $sql="update gks_whi_mov set 
  mov_whi_seira_code='".$db_link->escape_string($mov_whi_seira_code_new)."',
  mov_whi_number_int=".$mov_whi_number_int_new.",
  mov_whi_number_str='".$db_link->escape_string($mov_whi_number_str_new)."',
  mov_whi_ekdosi_date=now()
  
  where id_whi_mov=".$id_whi_mov." limit 1";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
    
  $sxolio=gks_lang('Προσθήκη από backend - Υπόλοιπα Ειδών'); 
  $sql="insert into gks_whi_mov_log (whi_mov_id, add_date,user_id,sxolio) values (
  ".$id_whi_mov.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
    
}




$sql="SELECT 
gks_eshop_products.*,
CASE
  WHEN gks_eshop_products.product_class='variable_item' THEN
    CASE
      WHEN gks_eshop_products.product_descr<>'' THEN
        gks_eshop_products.product_descr
      ELSE
        CASE
          WHEN gks_eshop_products.product_descr_variable<>'' THEN
            CONCAT_WS(' ', gks_eshop_products_parent.product_descr, gks_eshop_products.product_descr_variable)
          ELSE
            gks_eshop_products_parent.product_descr
        END
    END
  ELSE gks_eshop_products.product_descr
END as product_descr_p
FROM gks_eshop_products
LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product
where gks_eshop_products.id_product=".$product_id;
$result = $db_link->query($sql);
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
$product_descr='';
$product_monada_id=0;
$monada_convert_json='';



if ($result->num_rows>0) {
  $row = $result->fetch_assoc();
  $product_descr=trim_gks($row['product_descr_p']);
  $product_monada_id=$row['product_monada_id'];
  $monada_convert=array();
  gks_monada_convert(intval($row['product_monada_id']), intval($row['product_monada_id']), $monada_convert, array());
  $monada_convert_json=json_encode($monada_convert);




}


$sql="select max(product_aa) as cc from gks_whi_mov_products where whi_mov_id=".$id_whi_mov;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
$product_aa=1;
if ($result->num_rows>0) {
  $row = $result->fetch_assoc();
  if (isset($row['cc']) and intval($row['cc'])>0) $product_aa=intval($row['cc']) + 1;
}


//echo '<pre>'.
//'warehouse_id:'.$warehouse_id."\n".
//'product_id:'.$product_id."\n".
//'lot_product_id:'.$lot_product_id."\n".
//'posotitaonhand:'.$posotitaonhand."\n".
//'curr_balance:'.$curr_balance."\n".
//'diafora:'.$diafora."\n".
//'company_id:'.$company_id."\n".
//'company_sub_id:'.$company_sub_id."\n".
//'id_acc_journal:'.$id_acc_journal."\n".
//'id_acc_seira:'.$id_acc_seira."\n".
//'';
//die();


$sql="select id_whi_mov_product,product_quantity from gks_whi_mov_products where whi_mov_id=".$id_whi_mov." and product_id=".$product_id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $id_whi_mov_product=$row['id_whi_mov_product'];
  $exist_quantity=floatval($row['product_quantity']);
  $new_quantity=$exist_quantity+$diafora;
  
  $sql="update gks_whi_mov_products set 
  mydate_edit=now(),
  user_id_edit=".$my_wp_user_id.",
  myip='".$db_link->escape_string($gkIP)."',
  product_descr='".$db_link->escape_string($product_descr)."',
  product_monada_id_org=".$product_monada_id.",
  product_monada_id=".$product_monada_id.",
  monada_convert_json='".$db_link->escape_string($monada_convert_json)."',
  product_quantity=".number_format($new_quantity,8,'.','').",
  apografi_posotitaonhand=".number_format($posotitaonhand,8,'.','').",
  p_warehouses_id_from=0,
  p_warehouses_id_to=".$warehouse_id.",
  p_mov_state='090ekdosi'
  where id_whi_mov_product=".$id_whi_mov_product;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  
  $sxolio=gks_lang('Ενημέρωση είδους <b>[1]</b> σε ποσότητα: <b>[2]</b>'); 
  $sxolio=str_replace('[1]',$product_descr,$sxolio);
  $sxolio=str_replace('[2]',$new_quantity,$sxolio);  
  
  $sql="insert into gks_whi_mov_log (whi_mov_id, add_date,user_id,sxolio) values (
  ".$id_whi_mov.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 

  
} else {
  
  $sqlF=''; $sqlV='';
  $sqlF.='mydate_add,'; $sqlV.="now(),";
  $sqlF.='mydate_edit,'; $sqlV.="now(),";
  $sqlF.='user_id_add,'; $sqlV.=$my_wp_user_id.",";
  $sqlF.='user_id_edit,'; $sqlV.=$my_wp_user_id.",";
  $sqlF.='myip,'; $sqlV.="'".$db_link->escape_string($gkIP)."',";
  $sqlF.='whi_mov_id,'; $sqlV.=$id_whi_mov.",";
  $sqlF.='product_aa,'; $sqlV.=$product_aa.",";
  $sqlF.='product_id,'; $sqlV.=$product_id.",";
  $sqlF.='product_descr,'; $sqlV.="'".$db_link->escape_string($product_descr)."',";
  $sqlF.='product_monada_id_org,'; $sqlV.=$product_monada_id.",";
  $sqlF.='product_monada_id,'; $sqlV.=$product_monada_id.",";
  $sqlF.='monada_convert_json,'; $sqlV.="'".$db_link->escape_string($monada_convert_json)."',";
  $sqlF.='product_quantity,'; $sqlV.=number_format($diafora,8,'.','').",";
  $sqlF.='apografi_posotitaonhand,'; $sqlV.=number_format($posotitaonhand,8,'.','').",";
  $sqlF.='p_warehouses_id_from,'; $sqlV.="0,";
  $sqlF.='p_warehouses_id_to,'; $sqlV.=$warehouse_id.",";
  $sqlF.='p_mov_state,'; $sqlV.="'090ekdosi',";





  $sqlF=substr($sqlF, 0, strlen($sqlF)-1);
  $sqlV=substr($sqlV, 0, strlen($sqlV)-1);
  $sql="insert into gks_whi_mov_products (".$sqlF.") values (".$sqlV.")";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  $id_whi_mov_product=$db_link->insert_id;
  
  $sxolio=gks_lang('Προσθήκη είδους').' '.$product_descr.' '.gks_lang('με ποσότητα').': '.$diafora; 
  $sql="insert into gks_whi_mov_log (whi_mov_id, add_date,user_id,sxolio) values (
  ".$id_whi_mov.",now(),".$my_wp_user_id.",'".$db_link->escape_string($sxolio)."')";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  


}

$sql="select * from gks_whi_mov_products_lots where whi_mov_product_id=".$id_whi_mov_product." and lot_product_id=".$lot_product_id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $id_whi_mov_product_lots=$row['id_whi_mov_product_lots'];
  
  $exist_quantity=floatval($row['lot_product_quantity']);
  $new_quantity=$exist_quantity+$diafora;
  
  $sql="update gks_whi_mov_products_lots set
  lot_product_quantity=".number_format($new_quantity,8,'.','').",
  apografi_lot_posotitaonhand=".number_format($posotitaonhand,8,'.','')."
  where id_whi_mov_product_lots=".$id_whi_mov_product_lots;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}

  
} else {
  
  $sql="insert into gks_whi_mov_products_lots (
  whi_mov_product_id,lot_product_id,lot_product_quantity,apografi_lot_posotitaonhand
  ) values (
  ".$id_whi_mov_product.",".$lot_product_id.",".number_format($diafora,8,'.','').",".number_format($posotitaonhand,8,'.','')."
  )";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  
}



$all_products_for_balance=array($product_id);
gks_whi_mov_balance_calc($all_products_for_balance);


$curr_balance=0;
$sql="select balance from gks_warehouse_balance_lots_serials 
where lot_product_id=".$lot_product_id." and warehouse_id=".$warehouse_id;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $curr_balance=floatval($row['balance']);
}

$total_balance=0;
$sql="select balance from gks_warehouse_balance_lots_serials 
where lot_product_id=".$lot_product_id." and warehouse_id=0";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}
if ($result->num_rows>=1) {
  $row = $result->fetch_assoc();
  $total_balance=floatval($row['balance']);
}



$myarray_new=array();
$myarray_line_new=array();
$idiotites_new=get_whi_mov_details_txt($id_whi_mov, $myarray_new, $myarray_line_new); 

$sql="update gks_whi_mov set idiotites='".$db_link->escape_string(json_encode($myarray_new))."' where id_whi_mov = ".$id_whi_mov." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); } 

$sql="UPDATE gks_whi_mov_products
LEFT JOIN gks_eshop_products ON gks_whi_mov_products.product_id = gks_eshop_products.id_product
SET
gks_whi_mov_products.product_need_apostoli = gks_eshop_products.product_need_apostoli,
gks_whi_mov_products.product_varos = gks_eshop_products.product_varos,
gks_whi_mov_products.product_ogos_x = gks_eshop_products.product_ogos_x,
gks_whi_mov_products.product_ogos_y = gks_eshop_products.product_ogos_y,
gks_whi_mov_products.product_ogos_z = gks_eshop_products.product_ogos_z,
gks_whi_mov_products.product_normal = gks_eshop_products.product_normal,
gks_whi_mov_products.product_type = gks_eshop_products.product_type,
gks_whi_mov_products.product_need_multi_files = gks_eshop_products.product_need_multi_files,
gks_whi_mov_products.product_need_multi_files_min = gks_eshop_products.product_need_multi_files_min,
gks_whi_mov_products.product_need_multi_files_max = gks_eshop_products.product_need_multi_files_max,
gks_whi_mov_products.product_monada_id_org=gks_eshop_products.product_monada_id

WHERE gks_whi_mov_products.whi_mov_id=".$id_whi_mov;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
} 
$sql="select * from gks_whi_mov_products where whi_mov_id=".$id_whi_mov;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
} 
$products_posotita=0;
$products_varos=0;
$products_ogos=0;
$products_ogos_max_x=0;
$products_ogos_max_y=0;
$products_ogos_max_z=0;
$products_need_apostoli=0;

while ($row = $result->fetch_assoc()) {
  $products_posotita+=$row['product_quantity'];
  $products_varos+=$row['product_quantity'] * $row['product_varos'];
  $products_ogos+=$row['product_quantity'] * ($row['product_ogos_x'] * $row['product_ogos_y'] * $row['product_ogos_z']);
  
  if ($row['product_ogos_x'] > $products_ogos_max_x) $products_ogos_max_x=$row['product_ogos_x'];
  if ($row['product_ogos_y'] > $products_ogos_max_y) $products_ogos_max_y=$row['product_ogos_y'];
  $products_ogos_max_z+=$row['product_quantity'] * $row['product_ogos_z'];  
  
  if ($row['product_need_apostoli']!=0) $products_need_apostoli=1;
}



$sql="update gks_whi_mov set 
products_posotita=".number_format($products_posotita,8,'.','').",
products_need_apostoli=".$products_need_apostoli.",
products_varos=".number_format($products_varos,8,'.','').",
products_ogos=".number_format($products_ogos,8,'.','').",
products_ogos_max_x=".number_format($products_ogos_max_x,8,'.','').",
products_ogos_max_y=".number_format($products_ogos_max_y,8,'.','').",
products_ogos_max_z=".number_format($products_ogos_max_z,8,'.','').",
session_id='".$_gks_id_session."'
where id_whi_mov=".$id_whi_mov;
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();
} 



$sql="select mov_date from gks_whi_mov where id_whi_mov=".$id_whi_mov." and mov_state='090ekdosi' and mov_date is not null";
$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die();}    
if ($result->num_rows==1) {
  $row = $result->fetch_assoc();
  $mybal = gks_whi_mov_balance_calc($all_products_for_balance,$row['mov_date']);
  //echo '<pre>'; print_r($mybal); echo '</pre>';die();
  
  foreach ($mybal as $id_product => $data) {
    $after_balance_warehouses_id_to=0;
    if (isset($data['warehouses'][$warehouse_id])) $after_balance_warehouses_id_to=$data['warehouses'][$warehouse_id]['bal'];
  
    $sql="update gks_whi_mov_products set 
    after_balance_warehouses_id_to=".number_format($after_balance_warehouses_id_to, 8, '.', '')."
    where whi_mov_id=".$id_whi_mov." and product_id=".$id_product;
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}    
  } 
}


$return = array('success' => true, 'message' => base64_encode('OK'),
  'balance'=>myNumberFormatNo0Local($curr_balance),
  'balance_val'=>myNumberFormatNo0($curr_balance),
  'total_balance'=>myNumberFormatNo0Local($total_balance),

);
echo json_encode($return); die();

$return = array('success' => false, 'message' => base64_encode('aaaa '.$warehouse_id.
  ' | '.$product_id.
  ' | '.$posotitaonhand.
  ' | '.$curr_balance.
  ' | '.$company_id.
  ' | '.$company_sub_id.
  ' | '.$id_acc_journal.
  ' | '.$id_acc_seira.
  ' | '.$id_whi_mov.
  ' | '.$id_whi_mov_product

));
echo json_encode($return); die();

