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
if ($id<=0 and $id<>-1) {
  debug_mail(false,'the id is not set','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί το').' ID.'));
  echo json_encode($return); die();}

$my_page_title=gks_lang('Αποθήκευση Συνταγής').' id: '.$id;
db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_bom',($id==-1 ? 'add':'edit'),$id);
if ($perm_ret['success']==false) {$return = array('success' => false, 'message' => base64_encode($perm_ret['message']));echo json_encode($return); die();}



$is_new_rec=false;
if ($id==-1) {
  $is_new_rec=true;
} else {
  $sql ="SELECT * FROM gks_production_bom where id_production_bom = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('record not found'));
    echo json_encode($return); die(); }
  if ($result->num_rows!=1) {
    debug_mail(false,'error sql',gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα')));
    echo json_encode($return); die();  }
  $row = $result->fetch_assoc();
}

$from_calc_pliroteo=false; if (isset($_POST['from_calc_pliroteo'])) $from_calc_pliroteo=(intval($_POST['from_calc_pliroteo'])==1);
$bom_descr=''; if (isset($_POST['bom_descr'])) $bom_descr=trim_gks(base64_decode($_POST['bom_descr']));
$reference=''; if (isset($_POST['reference'])) $reference=trim_gks(base64_decode($_POST['reference']));
$bom_product_id=0; if (isset($_POST['bom_product_id'])) $bom_product_id=intval($_POST['bom_product_id']);
$bom_quantity=0; if (isset($_POST['bom_quantity'])) $bom_quantity=floatval(str_replace(',','.', $_POST['bom_quantity']));
$bom_monada_id=0; if (isset($_POST['bom_monada_id'])) $bom_monada_id=intval($_POST['bom_monada_id']);
$bom_disable=0; if (isset($_POST['bom_disable'])) $bom_disable=intval($_POST['bom_disable']);
if ($bom_disable!=0) $bom_disable=1;
$bom_note=''; if (isset($_POST['bom_note'])) $bom_note=trim_gks(base64_decode($_POST['bom_note']));


$company_id_sub_id=''; if (isset($_POST['company_id_sub_id'])) $company_id_sub_id=trim_gks(base64_decode($_POST['company_id_sub_id']));
$company_id=0;
$company_sub_id=0;
$user_companys=gks_get_companys_list();
if ($company_id_sub_id!='') {
  $parts=explode('|',$company_id_sub_id);
  if (count($parts)==2) {
    $company_id=intval($parts[0]);
    $company_sub_id=intval($parts[1]);
    $found=false;
    foreach ($user_companys as $value) {
      if ($value['id_company'] == $company_id and $value['id_company_sub'] == $company_sub_id) {
        $found=true;
        break;
      }
    }
    if ($found==false) {$company_id=0;$company_sub_id=0;}
  }
}



  



$eidi_array_str = trim_gks(base64_decode($_POST['eidi_array_str']));

$eidi_array = json_decode($eidi_array_str, true);
if ($eidi_array === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['eidi_array_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').' (1)<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}
  

$not_del_id_production_bom_product=array();
$gks_production_bom_product=array();
foreach ($eidi_array as $eidi_array_item) {
  $id_production_bom_product=intval($eidi_array_item['id_production_bom_product']);
  $pbom_aa=intval($eidi_array_item['pbom_aa']);
  $pbom_product_id=intval($eidi_array_item['pbom_product_id']);
  $pbom_variant_product_id=intval($eidi_array_item['pbom_variant_product_id']);
  $pbom_quantity=floatval($eidi_array_item['pbom_quantity']);  
  $pbom_monada_id=intval($eidi_array_item['pbom_monada_id']);
  $pbom_kostos_type=intval($eidi_array_item['pbom_kostos_type']);
  if ($pbom_kostos_type!=1) $pbom_kostos_type=0;
  $pbom_kostos_value=floatval($eidi_array_item['pbom_kostos_value']);  
  if ($pbom_kostos_type==0) $pbom_kostos_value=0;
  $pbom_note=trim_gks($eidi_array_item['pbom_note']);

  if ($from_calc_pliroteo or ($pbom_product_id>0 or $pbom_monada_id>0)) {
  //if (1==1) {
    if ($id_production_bom_product>0) $not_del_id_production_bom_product[] = $id_production_bom_product;
    $gks_production_bom_product[]=array(
      'id_production_bom_product'=>$id_production_bom_product,
      'pbom_aa'=>$pbom_aa,
      'pbom_product_id'=>$pbom_product_id,
      'pbom_variant_product_id'=>$pbom_variant_product_id,
      'pbom_quantity'=>$pbom_quantity,
      'pbom_monada_id'=>$pbom_monada_id,
      'pbom_kostos_type'=>$pbom_kostos_type,
      'pbom_kostos_value'=>$pbom_kostos_value,
      'pbom_note'=>$pbom_note,
      //for calc_pliroteo
      'bom_monada_convert' =>'',
      'product_kostos_org' =>0,
      
    );
  }
}

$cost_array_str = trim_gks(base64_decode($_POST['cost_array_str']));

$cost_array = json_decode($cost_array_str, true);
if ($cost_array === null && json_last_error() !== JSON_ERROR_NONE) {
  debug_mail(false,'json_decode error',$_POST['cost_array_str']);
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').' (2)<br>'.gks_lang('Ξαναδοκιμάστε')));
  echo json_encode($return); die();}
  

$not_del_id_production_bom_cost=array();
$gks_production_bom_cost=array();
foreach ($cost_array as $cost_array_item) {
  $id_production_bom_cost=intval($cost_array_item['id_production_bom_cost']);
  $cbom_aa=intval($cost_array_item['cbom_aa']);
  $cbom_cost=trim_gks($cost_array_item['cbom_cost']);  
  $cbom_note=trim_gks($cost_array_item['cbom_note']);
  $cbom_kostos_value=floatval($cost_array_item['cbom_kostos_value']);  
  $cbom_variant_product_id=intval($cost_array_item['cbom_variant_product_id']);
  
  if ($from_calc_pliroteo or ($cbom_cost!='' or $cbom_kostos_value<>0)) {
    if ($id_production_bom_cost>0) $not_del_id_production_bom_cost[] = $id_production_bom_cost;
    $gks_production_bom_cost[]=array(
      'id_production_bom_cost'=>$id_production_bom_cost,
      'cbom_aa'=>$cbom_aa,
      'cbom_cost'=>$cbom_cost,
      'cbom_note'=>$cbom_note,
      'cbom_kostos_value'=>$cbom_kostos_value,
      'cbom_variant_product_id'=>$cbom_variant_product_id,
    );
  }
}





$monada_convert_base=array();  
$out_data=array();
$out_data['div_monada_conv_display']='none';
$out_data['span_monada_conv_html']='';
$out_data['div_monada_conv2_display']='none';
$out_data['span_monada_conv2_html']='';

if ($bom_product_id>0) {
  $sql="select product_monada_id,
  gks_monades_metrisis.monada_descr as monada_descr_org, gks_monades_metrisis.monada_symbol as monada_symbol_org
  FROM gks_eshop_products 
  LEFT JOIN gks_monades_metrisis ON gks_eshop_products.product_monada_id = gks_monades_metrisis.id_monada
  where id_product=".$bom_product_id;
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}    
  if ($result->num_rows<=0) {die('product not found');}
  $row = $result->fetch_assoc();
  $product_monada_id_org=intval($row['product_monada_id']);
  $monada_descr_org=trim_gks($row['monada_descr_org']);
  $monada_symbol_org=trim_gks($row['monada_symbol_org']);
  
  gks_monada_convert($bom_monada_id, $product_monada_id_org, $monada_convert_base,array());
  if ($bom_monada_id==$product_monada_id_org) {
    $out_data['div_monada_conv_display']='none';
    $out_data['div_monada_conv2_display']='none';
  } else {
    $out_data['div_monada_conv_display']='';
    $out_data['div_monada_conv2_display']='';
    
    $out_data['span_monada_conv_html']=gks_lang('Μονάδα μέτρησης του είδους').': '.$monada_descr_org.($monada_symbol_org=='' ? '' : ' ('.$monada_symbol_org.')').'<br>'.
    gks_lang('Μετατροπή').': ';
    
    
    if ($monada_convert_base['ok'] and $monada_convert_base['epi']!=0) {
      //$quantity_mm=$quantity / $monada_convert_base['epi'];
      //echo '<pre>';print_r($monada_convert_base);print '</pre>';
      $out_epi= myNumberFormatNo0Local($monada_convert_base['epi'],true);
      $out_data['span_monada_conv_html'].= '<b>1</b> '.$monada_convert_base['from_descr'].' = <b>'.$out_epi.'</b> '.$monada_convert_base['to_descr'];
    } else {
      $out_data['span_monada_conv_html'].='<span style="color:red;">'.gks_lang('Δεν μπορεί να γίνει η μετατροπή').'</span>';  
    }      
    if ($monada_convert_base['ok'] and $monada_convert_base['epi']!=0) {
      $out_data['span_monada_conv2_html']= '<b>'.myNumberFormatNo0Local($bom_quantity,true).'</b> '.$monada_convert_base['from_descr'].
      ' = <b>'.myNumberFormatNo0Local($monada_convert_base['epi']*floatval($bom_quantity),true).'</b> '.$monada_convert_base['to_descr'];
    }      
  }
}

$id_product_ids=array();
$out_eidi=array();
foreach ($gks_production_bom_product as $value) {
  $id_product_ids[]=$value['pbom_product_id'];
} 

if (count($id_product_ids)>0) {
  $sql="select id_product,product_monada_id as product_monada_id_org,
  gks_monades_metrisis.monada_descr as monada_descr_org, gks_monades_metrisis.monada_symbol as monada_symbol_org,
  product_kostos as product_kostos_org
  FROM gks_eshop_products 
  LEFT JOIN gks_monades_metrisis ON gks_eshop_products.product_monada_id = gks_monades_metrisis.id_monada
  where id_product in (".implode(',',$id_product_ids).")";
  $result = $db_link->query($sql);
  if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}    
  while ($row = $result->fetch_assoc()) {
    foreach ($gks_production_bom_product as &$value) {
      if ($value['pbom_product_id']==$row['id_product']) {
        $value['product_kostos_org']=$row['product_kostos_org'];
        
        $pbom_monada_id=intval($value['pbom_monada_id']);
        $bom_product_monada_id_org=intval($row['product_monada_id_org']);
      
        $bom_monada_convert=array();
        gks_monada_convert($pbom_monada_id, $bom_product_monada_id_org, $bom_monada_convert,array());
        //print '<pre>';print_r($bom_monada_convert);die();
        if ($pbom_monada_id==$bom_product_monada_id_org) {
          $value['bom_monada_convert']='';
        } else if ($bom_monada_convert['ok'] and $bom_monada_convert['epi']!=0) {
          $value['bom_monada_convert']= '<b>'.myNumberFormatNo0Local($value['pbom_quantity'],true).'</b> '.$bom_monada_convert['from_descr'].
            ' = <b>'.myNumberFormatNo0Local($bom_monada_convert['epi']*floatval($value['pbom_quantity']),true).'</b> '.$bom_monada_convert['to_descr'];
            
          $value['product_kostos_org']=$value['product_kostos_org']*$bom_monada_convert['epi'];
        } else {
          $value['bom_monada_convert']='<span style="color:red;">'.gks_lang('Δεν μπορεί να γίνει η μετατροπή').'</span>';  
        }
        
      }
    }
    unset($value);
  }
    
  $out_eidi=array();
  foreach ($gks_production_bom_product as $value) {
    $out_eidi[]=array(
      'aa' => $value['pbom_aa'],
      'gks_bom_monada_convert_html'=>$value['bom_monada_convert'],
      'gks_pbom_kostos_value_val'=>$value['product_kostos_org'],
      'gks_pbom_kostos_value_data_kostos_org'=>$value['product_kostos_org'],
    );
  }
  //print '<pre>';print_r($gks_production_bom_product);die();
  
}

$calc_res=calc_gks_production_bom_per_product($bom_product_id,$bom_quantity,$monada_convert_base,$gks_production_bom_product,$gks_production_bom_cost);
//print '<pre>';print_r($calc_res);die();

if ($from_calc_pliroteo) {
  

  
  
  $out_data['out_eidi']=$out_eidi;
  
  $return = array('success' => true, 'message' => base64_encode('from_calc_pliroteo'),'out_data'=>$out_data, 'calc_res'=>$calc_res);
  echo json_encode($return); die();
}





if ($bom_product_id<=0) {debug_mail(false,'emptyl',              gks_lang('Επιλέξτε κάποιο είδος'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Επιλέξτε κάποιο είδος')));
  echo json_encode($return); die(); }

if ($bom_quantity<=0) {debug_mail(false,'emptyl',                gks_lang('Ορίστε την ποσότητα'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την ποσότητα')));
  echo json_encode($return); die(); }

if ($bom_monada_id<=0) {debug_mail(false,'emptyl',               gks_lang('Ορίστε την μονάδα μέτρησης'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Ορίστε την μονάδα μέτρησης')));
  echo json_encode($return); die(); }

if ($out_data['span_monada_conv_html']!='' and strpos($out_data['span_monada_conv_html'],'color:red')) {
  $return = array('success' => false, 'message' => base64_encode($out_data['span_monada_conv_html']));
  echo json_encode($return); die(); }

if ($bom_descr=='') {debug_mail(false,'emptyl',                  gks_lang('Η περιγραφή δεν μπορεί να είναι κενή'));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Η περιγραφή δεν μπορεί να είναι κενή')));
  echo json_encode($return); die(); }


  

  

if (count($gks_production_bom_product)==0 and count($gks_production_bom_cost)==0) {
  debug_mail(false,'gks_production_bom_product zero',print_r($eidi_array,true).'<br>'.print_r($cost_array,true));
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχετε προσθέσει Υλικά που θα χρησιμοποιηθούν ή Άλλα κόστη')));
  echo json_encode($return); die();}

//print '<pre>';print_r($gks_production_bom_product);die();
$aa=0;
foreach ($gks_production_bom_product as $value) {
  $aa++;
  if ($value['pbom_product_id']<=0) {
    debug_mail(false,'pbom_product_id zero',print_r($gks_production_bom_product,true).'<br>'.print_r($cost_array,true));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$aa,gks_lang('Ορίστε το είδος στην γραμμή [1] στα Υλικά που θα χρησιμοποιηθούν'))));
    echo json_encode($return); die();}
  if ($value['pbom_quantity']<=0) {
    debug_mail(false,'pbom_quantity zero '.$aa,print_r($gks_production_bom_product,true).'<br>'.print_r($cost_array,true));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$aa,gks_lang('Ορίστε την ποσότητα στην γραμμή [1] στα Υλικά που θα χρησιμοποιηθούν'))));
    echo json_encode($return); die();}   
  if ($value['pbom_monada_id']<=0) {
    debug_mail(false,'pbom_monada_id zero '.$aa,print_r($gks_production_bom_product,true).'<br>'.print_r($cost_array,true));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$aa,gks_lang('Ορίστε την μονάδα μέτρησης στην γραμμή [1] στα Υλικά που θα χρησιμοποιηθούν'))));
    echo json_encode($return); die();}   
  if ($value['bom_monada_convert']!='' and strpos($value['bom_monada_convert'],'color:red')) {
    debug_mail(false,'bom line '.$aa.' '.$value['bom_monada_convert'],print_r($gks_production_bom_product,true).'<br>'.print_r($cost_array,true));
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Στην γραμμή').' '.$aa.' '.$value['bom_monada_convert']));
    echo json_encode($return); die();}   
} 

//print '<pre>';print_r($cost_array);die();
$bb=0;
foreach ($gks_production_bom_cost as $value) {
  $bb++;
  if ($value['cbom_cost']=='') {
    debug_mail(false,'cbom_cost empty line '.$bb,print_r($gks_production_bom_product,true).'<br>'.print_r($cost_array,true));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$bb,gks_lang('Ορίστε την περιγραφή στην γραμμή [1] στα Άλλα κόστη'))));
    echo json_encode($return); die();}   
  if ($value['cbom_kostos_value']==0) {
    debug_mail(false,'cbom_kostos_value zero line '.$bb,print_r($gks_production_bom_product,true).'<br>'.print_r($cost_array,true));
    $return = array('success' => false, 'message' => base64_encode(str_replace('[1]',$bb,gks_lang('Ορίστε τo κόστος στην γραμμή [1] στα Άλλα κόστη'))));
    echo json_encode($return); die();}   
}


//save data
  
$sql="delete from gks_production_bom_product where production_bom_id=".$id;
if (count($not_del_id_production_bom_product)>0) {
  $sql.=" and id_production_bom_product not in (".implode(',',$not_del_id_production_bom_product).")";
}
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }  


$sql="delete from gks_production_bom_cost where production_bom_id=".$id;
if (count($not_del_id_production_bom_cost)>0) {
  $sql.=" and id_production_bom_cost not in (".implode(',',$not_del_id_production_bom_cost).")";
}
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }  


//print '<pre>';print_r($gks_production_bom_product);die();
//$return = array('success' => false, 'message' => base64_encode('dddd dddddd'));
//echo json_encode($return); die();


$gks_custom_save_prepare=gks_custom_table_item_save_prepare($_POST,'gks_production_bom');

$bom_kostos_min=0;
$bom_kostos_max=0;
$aa=0;
foreach ($calc_res['product_variants'] as $sitem) {
  $aa++;
  if ($aa==1) {
    $bom_kostos_min=$sitem['total'];
    $bom_kostos_max=$sitem['total'];    
  }
  if ($sitem['total'] < $bom_kostos_min) $bom_kostos_min=$sitem['total'];
  if ($sitem['total'] > $bom_kostos_max) $bom_kostos_max=$sitem['total'];
  
}


$redirect='';
if ($id==-1) {
  $sql="insert into gks_production_bom (
  user_id_add,user_id_edit,mydate_add,mydate_edit,myip
  ) values (
  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."')";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }  
  $id = $db_link->insert_id;
  $redirect=base64_encode('admin-production-bom-item.php?id='.$id);  
}

$sql="update gks_production_bom set 
bom_descr='".$db_link->escape_string($bom_descr)."',
reference='".$db_link->escape_string($reference)."',
bom_product_id=".$bom_product_id.",
company_id=".$company_id.",
company_sub_id=".$company_sub_id.",
bom_quantity=".number_format($bom_quantity, 8, '.', '').", 
bom_monada_id=".$bom_monada_id.",

bom_note='".$db_link->escape_string($bom_note)."',

bom_disable=".$bom_disable.",

bom_kostos=".number_format(floatval($calc_res['base']['total']), 8, '.', '').", 
bom_kostos_min=".number_format(floatval($bom_kostos_min), 8, '.', '').", 
bom_kostos_max=".number_format(floatval($bom_kostos_max), 8, '.', '').", 
bom_json='".$db_link->escape_string(serialize($calc_res))."',

user_id_edit=".$my_wp_user_id.",
mydate_edit=now(),
myip='".$db_link->escape_string($gkIP)."'
where id_production_bom = ".$id." limit 1";
$result = $db_link->query($sql);  
if (!$result) {
  debug_mail(false,'error sql',$sql);
  $return = array('success' => false, 'message' => base64_encode('sql error'));
  echo json_encode($return); die(); }
  
  
$gks_custom_save_prepare=gks_custom_table_item_save_run($gks_custom_save_prepare,$id);


foreach ($gks_production_bom_product as $myrec) {
  $id_production_bom_product=$myrec['id_production_bom_product'];
  if ($myrec['id_production_bom_product']==0) {
    $sql="insert into gks_production_bom_product (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      production_bom_id,
      pbom_aa,
      pbom_product_id,
      pbom_variant_product_id,
      pbom_quantity,
      pbom_monada_id,
      pbom_kostos_type,
      pbom_kostos_value,
      pbom_note
    ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$id.",
      ".$myrec['pbom_aa'].",
      ".$myrec['pbom_product_id'].",
      ".$myrec['pbom_variant_product_id'].",
      ".number_format($myrec['pbom_quantity'],8,'.','').",
      ".$myrec['pbom_monada_id'].",
      ".$myrec['pbom_kostos_type'].",
      ".number_format($myrec['pbom_kostos_value'],8,'.','').",
      '".$db_link->escape_string($myrec['pbom_note'])."'
    );";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }   
      
    $id_production_bom_product=$db_link->insert_id;
        
  } else {
    $sql="update gks_production_bom_product set 
    pbom_aa=".$myrec['pbom_aa'].",
    pbom_product_id=".$myrec['pbom_product_id'].",
    pbom_variant_product_id=".$myrec['pbom_variant_product_id'].",
    pbom_quantity=".number_format($myrec['pbom_quantity'],8,'.','').",
    pbom_monada_id=".$myrec['pbom_monada_id'].",
    pbom_kostos_type=".$myrec['pbom_kostos_type'].",
    pbom_kostos_value=".number_format($myrec['pbom_kostos_value'],8,'.','').",
    pbom_note='".$db_link->escape_string($myrec['pbom_note'])."',
    
    mydate_edit=now(),
    user_id_edit=".$my_wp_user_id.",
    myip='".$db_link->escape_string($gkIP)."'
    where id_production_bom_product=".$myrec['id_production_bom_product']." and production_bom_id=".$id;

    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }      
    
    
    
  }
}


foreach ($gks_production_bom_cost as $myrec) {
  $id_production_bom_cost=$myrec['id_production_bom_cost'];
  if ($myrec['id_production_bom_cost']==0) {
    $sql="insert into gks_production_bom_cost (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      production_bom_id,
      cbom_aa,
      cbom_cost,
      cbom_note,
      cbom_kostos_value,
      cbom_variant_product_id
    ) values (
      now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
      ".$id.",
      ".$myrec['cbom_aa'].",
      '".$db_link->escape_string($myrec['cbom_cost'])."',
      '".$db_link->escape_string($myrec['cbom_note'])."',
      ".number_format($myrec['cbom_kostos_value'],8,'.','').",
      ".$myrec['cbom_variant_product_id']."
    );";
    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }   
      
    $id_production_bom_cost=$db_link->insert_id;
        
  } else {
    $sql="update gks_production_bom_cost set 
    cbom_aa=".$myrec['cbom_aa'].",
    cbom_cost='".$db_link->escape_string($myrec['cbom_cost'])."',
    cbom_note='".$db_link->escape_string($myrec['cbom_note'])."',
    cbom_kostos_value=".number_format($myrec['cbom_kostos_value'],8,'.','').",
    cbom_variant_product_id=".$myrec['cbom_variant_product_id'].",
    
    mydate_edit=now(),
    user_id_edit=".$my_wp_user_id.",
    myip='".$db_link->escape_string($gkIP)."'
    where id_production_bom_cost=".$myrec['id_production_bom_cost']." and production_bom_id=".$id;

    $result = $db_link->query($sql);  
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die(); }      
    
    
    
  }
}    

$return = array('success' => true, 'message' => base64_encode('OK'),'redirect'=> $redirect);
echo json_encode($return); die();



