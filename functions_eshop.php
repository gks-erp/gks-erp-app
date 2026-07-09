<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function guid_for_order() {
  global $db_link;
  while (true) {
    mt_srand(intval((double)microtime()*10000));//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = ''; //chr(45);// "-"
    $guid = substr($charid, 0, 8)
        .substr($charid, 8, 4)
        .substr($charid,12, 4)
        .substr($charid,16, 4)
        .substr($charid,20,12);
    $guid = strtolower($guid);
    $sql = "SELECT order_guid from gks_orders where order_guid='".$db_link->escape_string($guid)."'";
    $result = $db_link->query($sql);
    
    if ($result->num_rows == 0) {
      return $guid; 
    }
  }
}
function guid_for_acc_inv() {
  global $db_link;
  while (true) {
    mt_srand(intval((double)microtime()*10000));//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = ''; //chr(45);// "-"
    $guid = substr($charid, 0, 8)
        .substr($charid, 8, 4)
        .substr($charid,12, 4)
        .substr($charid,16, 4)
        .substr($charid,20,12);
    $guid = strtolower($guid);
    $sql = "SELECT inv_guid from gks_acc_inv where inv_guid='".$db_link->escape_string($guid)."'";
    $result = $db_link->query($sql);
    
    if ($result->num_rows == 0) {
      return $guid; 
    }
  }
}

function guid_for_acc_pay() {
  global $db_link;
  while (true) {
    mt_srand(intval((double)microtime()*10000));//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = ''; //chr(45);// "-"
    $guid = substr($charid, 0, 8)
        .substr($charid, 8, 4)
        .substr($charid,12, 4)
        .substr($charid,16, 4)
        .substr($charid,20,12);
    $guid = strtolower($guid);
    $sql = "SELECT pay_guid from gks_acc_pay where pay_guid='".$db_link->escape_string($guid)."'";
    $result = $db_link->query($sql);
    
    if ($result->num_rows == 0) {
      return $guid; 
    }
  }
}

function guid_for_async_queue() {
  global $db_link;
  while (true) {
    mt_srand(intval((double)microtime()*10000));//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = ''; //chr(45);// "-"
    $guid = substr($charid, 0, 8)
        .substr($charid, 8, 4)
        .substr($charid,12, 4)
        .substr($charid,16, 4)
        .substr($charid,20,12);
    $guid = strtolower($guid);
    $sql = "SELECT guid from gks_async_queue where guid='".$db_link->escape_string($guid)."'";
    $result = $db_link->query($sql);
    
    if ($result->num_rows == 0) {
      return $guid; 
    }
  }
}


function guid_for_pos() {
  global $db_link;
  while (true) {
    mt_srand(intval((double)microtime()*10000));//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = ''; //chr(45);// "-"
    $guid = substr($charid, 0, 8)
        .substr($charid, 8, 4)
        .substr($charid,12, 4)
        .substr($charid,16, 4)
        .substr($charid,20,12);
    $guid = strtolower($guid);
    $sql = "SELECT pos_guid from gks_pos where pos_guid='".$db_link->escape_string($guid)."'";
    $result = $db_link->query($sql);
    
    if ($result->num_rows == 0) {
      return $guid; 
    }
  }
}

function guid_for_gps() {
  global $db_link;
  while (true) {
    mt_srand(intval((double)microtime()*10000));//optional for php 4.2.0 and up.
    $charid = strtoupper(md5(uniqid(rand(), true)));
    $hyphen = ''; //chr(45);// "-"
    $guid = substr($charid, 0, 8)
        .substr($charid, 8, 4)
        .substr($charid,12, 4)
        .substr($charid,16, 4)
        .substr($charid,20,12);
    $guid = strtolower($guid);
    $sql = "SELECT mydiadromi from gks_gps where mydiadromi='".$db_link->escape_string($guid)."'";
    $result = $db_link->query($sql);
    
    if ($result->num_rows == 0) {
      return $guid; 
    }
  }
}


function order_execute_download($id) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  
  $myreturn=array('need_refresh' => false, 'code' => 'error', 
  'text' => gks_lang('Προέκυψε κάποιο σφάλμα κατά την εκτέλεση της παραγγελίας').'<br>'.
  gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
  
  $sql="SELECT id_order_product, gks_orders_products.product_id, product_need_apostoli,
  gks_orders_products.product_is_simple_download,
  gks_orders_products.product_is_digital,
  gks_orders.user_id,
  gks_orders.user_email
  FROM gks_orders_products 
  LEFT JOIN gks_orders ON gks_orders_products.order_id = gks_orders.id_order
  WHERE order_id=".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'order_execute_download error sql',$sql);
    return $myreturn;
  }
  
  $has_more_than_download=false;
  $has_execute=false;
  $has_one_change=false;
  while ($row = $result->fetch_assoc()) {
    
    if ($row['product_id']== -10001) {
      $has_execute=true;
      $has_one_change=true;
      $user_email=trim_gks($row['user_email']);
      
      if ($user_email!='') {
        $sql_esf="SELECT Sum(product_quantity) AS mysum
        FROM gks_orders LEFT JOIN gks_orders_products ON gks_orders.id_order = gks_orders_products.order_id
        WHERE gks_orders.user_email like '".$db_link->escape_string($user_email)."'
        AND gks_orders.order_state In ('095execute','100completed','110payment')
        AND gks_orders_products.product_id=10001;";
        $result_esf = $db_link->query($sql_esf);        
        if (!$result_esf) {
          debug_mail(false,'order_execute_download error sql',$sql_esf);
          return $myreturn;
        }
        if ($result_esf->num_rows>0) {
          $row_esf = $result_esf->fetch_assoc();
          $quantity=intval($row_esf['mysum']);
          
          $sql_esf="select * from gks_efs_license where email like '".$db_link->escape_string($user_email)."'";
          $result_esf = $db_link->query($sql_esf);        
          if (!$result_esf) {
            debug_mail(false,'order_execute_download error sql',$sql_esf);
            return $myreturn;
          }
          if ($result_esf->num_rows>0) {
            $sql_esf="update gks_efs_license set quantity=".$quantity.",mydate_edit=now(),user_id_edit=".$my_wp_user_id.",myip='".$db_link->escape_string($gkIP)."' where email like '".$db_link->escape_string($user_email)."'";
            $result_esf = $db_link->query($sql_esf);        
            if (!$result_esf) {
              debug_mail(false,'order_execute_download error sql',$sql_esf);
              return $myreturn;
            }
            
          } else {
            $sql_esf="insert into gks_efs_license (
            email,quantity,mydate_add,mydate_edit,user_id_add,user_id_edit,myip
            ) values (
             '".$db_link->escape_string($user_email)."',
             ".$quantity.",
             now(),
             now(),
             ".$my_wp_user_id.",
             ".$my_wp_user_id.",
             '".$db_link->escape_string($gkIP)."'
            )";
            $result_esf = $db_link->query($sql_esf);        
            if (!$result_esf) {
              debug_mail(false,'order_execute_download error sql',$sql_esf);
              return $myreturn;
            }
            
          } 
          
        }
      }
      
    } else if ($row['product_need_apostoli'] ==0 and $row['product_is_digital'] ==1 and $row['product_is_simple_download'] ==1) {
      $has_execute=true;
      $sql="SELECT gks_orders_products_objects.order_product_id, gks_orders_products_objects_files.guid, gks_orders_products_objects_files.dbid
      FROM gks_orders_products_objects 
      LEFT JOIN gks_orders_products_objects_files ON gks_orders_products_objects.id_order_product_object = gks_orders_products_objects_files.order_product_object_id
      WHERE (((gks_orders_products_objects.order_product_id)=".$row['id_order_product']."));";
      $result_file = $db_link->query($sql);        
      if (!$result_file) {
        debug_mail(false,'order_execute_download error sql',$sql);
        return $myreturn;
      }
      if ($result_file->num_rows==0) {
        $has_one_change=true;
        // tha kano kati gia auto ???????????
        
      } else {      
        while ($row_file = $result_file->fetch_assoc()) {
          $sql="SELECT gks_file_perm.id_file_perm, gks_file_perm.file_id, gks_file_perm.user_id, gks_file_perm.order_id
          FROM gks_file_perm
          WHERE gks_file_perm.file_id=".$row_file['dbid']." AND gks_file_perm.user_id=".$row['user_id'];//." AND gks_file_perm.order_id=".$id;
          $result_check = $db_link->query($sql);        
          if (!$result_check) {
            debug_mail(false,'order_execute_download error sql',$sql);
            return $myreturn;
          } 
          if ($result_check->num_rows==0) {
            $has_one_change=true;
            $sql="insert into gks_file_perm (file_id,user_id, upload_from_user,can_download,order_id) values (
            ".$row_file['dbid'].",
            ".$row['user_id'].",
            0,
            1,
            ".$id.")";
            $run = $db_link->query($sql);        
            if (!$run) {
              debug_mail(false,'order_execute_download error sql',$sql);
              return $myreturn;
            }           
            
            
          } else {
            //iparxei idi, den prepei na kano kati 
          }
          
        }
        
      }
      

    } else {
      $has_more_than_download = true;     
    }
  }
  
  
  if ($has_execute == false) {
    $myreturn['code']='nothing';
    $myreturn['text']=gks_lang('Δεν υπάρχουν προϊόντα κατάλληλα για εκτέλεση');
  } else if ($has_one_change==false) {
    $myreturn['code']='no_change';
    $myreturn['text']=gks_lang('Δεν έγινε κάποια αλλαγή');
  } else if ($has_execute and $has_more_than_download) {
    $myreturn['code']='has_more_than_download';
    $myreturn['text']=gks_lang('Η παραγγελία έχει εκτελεστεί επιτυχώς για κάποια προϊόντα').'<br>'.
    gks_lang('Υπάρχουν και αλλά προϊόντα τα οποία δεν μπορούν να εκτελεστούν');
  } else {
    $myreturn['code']='OK';
    $myreturn['text']=gks_lang('Η παραγγελία έχει εκτελεστεί επιτυχώς για όλα τα προϊόντα');
  }
  
  if ($myreturn['code']=='OK') {
    $sql = "UPDATE gks_orders SET mdate_execute=Now() WHERE mdate_execute Is Null AND id_order=".$id;
    $run = $db_link->query($sql);        
    if (!$run) {
      debug_mail(false,'order_execute_download error sql',$sql);
      $myreturn=array('code' => 'error', 
      'text' => gks_lang('Προέκυψε κάποιο σφάλμα κατά την εκτέλεση της παραγγελίας').'<br>'.
      gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
      return $myreturn;
    } 
    if ($db_link->affected_rows > 0) {
      $myreturn['need_refresh'] = true;
    }
  }
  
  debug_mail(false,'order_execute_download result',print_r($myreturn,true));
   
  
  return $myreturn; 
}

function gks_price_formula_calc($row ,$quantity, $product_monada_id, $sheets, &$out, $reset_buffer,$id_acc_eidos_parastatikou) { //$reset_buffer=false
  //return 'sssss';
  
  //echo '<pre>';print_r($row);echo '</pre>';
  
  //die('<pre>gks_price_formula_calc '.time()); 
  //if (isset($row['product_price_quantity_formula'])==false) die('<pre>gks_price_formula_calc '.time()."\n".print_r($row,true)); 
  //file_put_contents(GKS_SITE_PATH.'tmp/gks_price_formula_calc_'.time().rand(1000,9999).'.txt',$quantity."\n".$sheets."\n".print_r($row,true));
  $errors='';
  $out=array();

  $out['quantitycheck_price_kostos']=0;
  $out['quantitycheck_price_item_kostos']=0;
  $row['product_price_kostos_sheets_formula']='';
  $row['product_price_kostos_quantity_formula']='';
  
  $out['quantitycheck_price_yperx']=0;
  $out['quantitycheck_price_item_yperx']=0;
  $row['product_price_yperx_sheets_formula']=trim_gks($row['product_price_yperx_sheets_formula']);
  $row['product_price_yperx_quantity_formula']=trim_gks($row['product_price_yperx_quantity_formula']);

  $out['quantitycheck_price']=0;
  $out['quantitycheck_price_item']=0;
  $row['product_price_sheets_formula']=trim_gks($row['product_price_sheets_formula']);
  $row['product_price_quantity_formula']=trim_gks($row['product_price_quantity_formula']);

  $out['quantitycheck_price_retail']=0;
  $out['quantitycheck_price_item_retail']=0;
  $row['product_price_retail_sheets_formula']=trim_gks($row['product_price_retail_sheets_formula']);
  $row['product_price_retail_quantity_formula']=trim_gks($row['product_price_retail_quantity_formula']);


  //delete me
  //if (isset($row['product_price_plist_quantity_formula'])==false) {
  //  echo 1/0;die();
  //}

  $out['quantitycheck_price_plist']=0;
  $out['quantitycheck_price_item_plist']=0;
  $row['product_price_plist_sheets_formula']=trim_gks($row['product_price_plist_sheets_formula']);
  $row['product_price_plist_quantity_formula']=trim_gks($row['product_price_plist_quantity_formula']);
  
  
  $product_monada_id_org=$row['product_monada_id'];
  
  
  $quantity_mm=$quantity;
  if (isset($row['monada_convert'])) {
    $monada_convert=$row['monada_convert'];
    //echo 'ggggggggggggg';
  } else {
    $monada_convert=array();
    gks_monada_convert($product_monada_id_org, $product_monada_id, $monada_convert, array());
    $row['monada_convert']=$monada_convert;
  }
  if ($monada_convert['ok'] and $monada_convert['epi']!=0) {
    $quantity_mm=$quantity / $monada_convert['epi'];
  }
  
  
  
  if ($reset_buffer) {
    ob_start();
    ob_get_clean();
  }
  
  /*
141 Timologio / Endokoinotikes Apoktiseis
142 Timologio / Apoktiseis Triton Choron
143 Timologio / Endokoinotiki Lipsi Ypiresion
144 Timologio / Lipsi Ypiresion Triton Choron
203 Prosfores apo promitheftes
205 Parangelies se promitheftes
502 Timologio Agoras Eidon imedapis (apo Timologio Polisis)
503 Pistotiko Timologio Agoras Eidon imedapis (apo Pistotiko Timologio)
504 Timologio Agoras Ypiresion imedapis (apo Timologio Parochis)
505 Pistotiko Timologio Agoras Ypiresion imedapis (apo Pistotiko Timologio)
551 Katachorisi ALP os exodo (apo ALP)
552 Katachorisi APY os exodo (apo APY)
  */
  if (in_array($id_acc_eidos_parastatikou,[141,142,143,144,203,205,502,503,504,505,551,552]) and floatval($row['product_kostos'])>0) {
    $row['product_price_kostos_sheets_formula']='';
    $row['product_price_kostos_quantity_formula']='';
    $row['product_price_kostos_calc']=floatval($row['product_kostos']);

    $row['product_price_yperx_sheets_formula']='';
    $row['product_price_yperx_quantity_formula']='';
    $row['product_price_yperx_calc']=floatval($row['product_kostos']);

    $row['product_price_sheets_formula']='';
    $row['product_price_quantity_formula']='';
    $row['product_price_calc']=floatval($row['product_kostos']);

    $row['product_price_retail_sheets_formula']='';
    $row['product_price_retail_quantity_formula']='';
    $row['product_price_retail_calc']=floatval($row['product_kostos']);
    
    $row['product_price_plist_sheets_formula']='';
    $row['product_price_plist_quantity_formula']='';
    $row['product_price_plist_calc']=floatval($row['product_kostos']);
    
    
  }
  ///print '<pre>fffffff ';print_r($row);die(); 
  //
      
  
  //if ($row['id_product']==12498) {print'<pre>';print_r($row);die();}
  //print'<pre>';print_r($row);die();
  
  //kostos
  //if ($row['product_price_kostos_sheets_formula'] == '') {
    $out['quantitycheck_price_item_kostos'] = $row['product_kostos'];
    //if ($out['ok'] and $out['epi']!=0 and $out['epi']!=1) 
  //} else {
  //}
  
  //yperxondriki
  if ($row['product_price_yperx_sheets_formula'] == '') {
    $out['quantitycheck_price_item_yperx'] = $row['product_price_yperx_calc'];
    //if ($out['ok'] and $out['epi']!=0 and $out['epi']!=1) 
  } else {
    $row['product_price_yperx_sheets_formula']=str_replace('[sheets]', $sheets, $row['product_price_yperx_sheets_formula']);
    $row['product_price_yperx_sheets_formula']=str_replace('[itemprice]', $row['product_price_yperx_calc'], $row['product_price_yperx_sheets_formula']);
    //echo '<pre>';print_r($row);die();
    
    $eval_result=false;
    $eval_formula='$eval_calc='.$row['product_price_yperx_sheets_formula'].'; $eval_result=true;';
    

    try {
      $res=eval($eval_formula);
      $out['quantitycheck_price_item_yperx'] = $eval_calc;
      //if ($row['id_product']==12498) {echo '<pre>';print_r($out);die();}
    } catch (ParseError  $e) {
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου υπερχονδρικής').' (1):<br><b>'.$row['product_price_yperx_sheets_formula'].'</b><br>'.$e->getMessage();
    } catch(ArithmeticError $e){
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου υπερχονδρικής').' (2):<br><b>'.$row['product_price_yperx_sheets_formula'].'</b><br>'.$e->getMessage();
    } catch(DivisionByZeroError $e){
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου υπερχονδρικής').' (3):<br><b>'.$row['product_price_yperx_sheets_formula'].'</b><br>'.$e->getMessage();
    } catch(TypeError $e){
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου υπερχονδρικής').' (4):<br><b>'.$row['product_price_yperx_sheets_formula'].'</b><br>'.$e->getMessage();
    } catch(AssertionError $e){
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου υπερχονδρικής').' (5):<br><b>'.$row['product_price_yperx_sheets_formula'].'</b><br>'.$e->getMessage();
    } catch(Exception  $e){
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου υπερχονδρικής').' (6):<br><b>'.$row['product_price_yperx_sheets_formula'].'</b><br>'.$e->getMessage();
    }
    
  }
  //echo 'ffffff||'.$eval_result.'||'.$errors.'||'.ob_get_contents();
  //echo $quantity_mm.' x '.$out['quantitycheck_price_item_yperx'];
  //die();
  
  
  if ($row['product_price_yperx_quantity_formula'] == '') {
    $out['quantitycheck_price_yperx'] = $quantity_mm*$out['quantitycheck_price_item_yperx'];
  } else {
    $row['product_price_yperx_quantity_formula']=str_replace('[quantity]', $quantity_mm, $row['product_price_yperx_quantity_formula']);
    $row['product_price_yperx_quantity_formula']=str_replace('[itemprice]', $out['quantitycheck_price_item_yperx'], $row['product_price_yperx_quantity_formula']);
    
    $eval_result=false;
    $eval_formula='$eval_calc='.$row['product_price_yperx_quantity_formula'].'; $eval_result=true;';
    //echo 'gggggggg';
    //if ($row['id_product']==12498) {echo '<pre>';print_r($out);die();}

    try {
      $res=eval($eval_formula);
      $out['quantitycheck_price_yperx'] = $eval_calc;
      //if ($row['id_product']==12498) {echo '<pre>sss ';print_r($out);die();}
      
    } catch (ParseError  $e) {
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου υπερχονδρικής').' (1):<br><b>'.$row['product_price_yperx_quantity_formula'].'</b><br>'.$e->getMessage();
    } catch(ArithmeticError $e){
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου υπερχονδρικής').' (2):<br><b>'.$row['product_price_yperx_quantity_formula'].'</b><br>'.$e->getMessage();
    } catch(DivisionByZeroError $e){
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου υπερχονδρικής').' (3):<br><b>'.$row['product_price_yperx_quantity_formula'].'</b><br>'.$e->getMessage();
    } catch(TypeError $e){
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου υπερχονδρικής').' (4):<br><b>'.$row['product_price_yperx_quantity_formula'].'</b><br>'.$e->getMessage();
    } catch(AssertionError $e){
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου υπερχονδρικής').' (5):<br><b>'.$row['product_price_yperx_quantity_formula'].'</b><br>'.$e->getMessage();
    } catch(Exception  $e){
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου υπερχονδρικής').' (6):<br><b>'.$row['product_price_yperx_quantity_formula'].'</b><br>'.$e->getMessage();
    }

  }    
    
  //print'<pre>';print_r($row);
  //xondriki
  if ($row['product_price_sheets_formula'] == '') {
    $out['quantitycheck_price_item'] = $row['product_price_calc'];
    //if ($out['ok'] and $out['epi']!=0 and $out['epi']!=1) 
  } else {
    $row['product_price_sheets_formula']=str_replace('[sheets]', $sheets, $row['product_price_sheets_formula']);
    $row['product_price_sheets_formula']=str_replace('[itemprice]', $row['product_price_calc'], $row['product_price_sheets_formula']);
    //echo '<pre>';print_r($row);die();
    
    $eval_result=false;
    $eval_formula='$eval_calc='.$row['product_price_sheets_formula'].'; $eval_result=true;';
    

    try {
      $res=eval($eval_formula);
      $out['quantitycheck_price_item'] = $eval_calc;
    } catch (ParseError  $e) {
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου χονδρικής').' (1):<br><b>'.$row['product_price_sheets_formula'].'</b><br>'.$e->getMessage();
    } catch(ArithmeticError $e){
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου χονδρικής').' (2):<br><b>'.$row['product_price_sheets_formula'].'</b><br>'.$e->getMessage();
    } catch(DivisionByZeroError $e){
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου χονδρικής').' (3):<br><b>'.$row['product_price_sheets_formula'].'</b><br>'.$e->getMessage();
    } catch(TypeError $e){
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου χονδρικής').' (4):<br><b>'.$row['product_price_sheets_formula'].'</b><br>'.$e->getMessage();
    } catch(AssertionError $e){
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου χονδρικής').' (5):<br><b>'.$row['product_price_sheets_formula'].'</b><br>'.$e->getMessage();
    } catch(Exception  $e){
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου χονδρικής').' (6):<br><b>'.$row['product_price_sheets_formula'].'</b><br>'.$e->getMessage();
    }
    
  }
  //echo 'ffffff||'.$eval_result.'||'.$errors.'||'.ob_get_contents();
  //die();
  
  if ($row['product_price_quantity_formula'] == '') {
    $out['quantitycheck_price'] = $quantity_mm*$out['quantitycheck_price_item'];
  } else {
    $row['product_price_quantity_formula']=str_replace('[quantity]', $quantity_mm, $row['product_price_quantity_formula']);
    $row['product_price_quantity_formula']=str_replace('[itemprice]', $out['quantitycheck_price_item'], $row['product_price_quantity_formula']);
    
    $eval_result=false;
    $eval_formula='$eval_calc='.$row['product_price_quantity_formula'].'; $eval_result=true;';
    //echo 'gggggggg';

    try {
      $res=eval($eval_formula);
      $out['quantitycheck_price'] = $eval_calc;
    } catch (ParseError  $e) {
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου χονδρικής').' (1):<br><b>'.$row['product_price_quantity_formula'].'</b><br>'.$e->getMessage();
    } catch(ArithmeticError $e){
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου χονδρικής').' (2):<br><b>'.$row['product_price_quantity_formula'].'</b><br>'.$e->getMessage();
    } catch(DivisionByZeroError $e){
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου χονδρικής').' (3):<br><b>'.$row['product_price_quantity_formula'].'</b><br>'.$e->getMessage();
    } catch(TypeError $e){
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου χονδρικής').' (4):<br><b>'.$row['product_price_quantity_formula'].'</b><br>'.$e->getMessage();
    } catch(AssertionError $e){
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου χονδρικής').' (5):<br><b>'.$row['product_price_quantity_formula'].'</b><br>'.$e->getMessage();
    } catch(Exception  $e){
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου χονδρικής').' (6):<br><b>'.$row['product_price_quantity_formula'].'</b><br>'.$e->getMessage();
    }

  }
  

  
  //retail
  if ($row['product_price_retail_sheets_formula'] == '') {
    $out['quantitycheck_price_item_retail'] = $row['product_price_retail_calc'];
  } else {
    $row['product_price_retail_sheets_formula']=str_replace('[sheets]', $sheets, $row['product_price_retail_sheets_formula']);
    $row['product_price_retail_sheets_formula']=str_replace('[itemprice]', $row['product_price_retail_calc'], $row['product_price_retail_sheets_formula']);
    $row['product_price_retail_sheets_formula']=str_replace('[price]', $out['quantitycheck_price_item'], $row['product_price_retail_sheets_formula']);
    

    $eval_result=false;
    $eval_formula='$eval_calc='.$row['product_price_retail_sheets_formula'].'; $eval_result=true;';

    try {
      $res=eval($eval_formula);
      $out['quantitycheck_price_item_retail'] = $eval_calc;
    } catch (ParseError  $e) {
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου λιανικής').' (1):<br><b>'.$row['product_price_retail_sheets_formula'].'</b><br>'.$e->getMessage();
    } catch(ArithmeticError $e){
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου λιανικής').' (2):<br><b>'.$row['product_price_retail_sheets_formula'].'</b><br>'.$e->getMessage();
    } catch(DivisionByZeroError $e){
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου λιανικής').' (3):<br><b>'.$row['product_price_retail_sheets_formula'].'</b><br>'.$e->getMessage();
    } catch(TypeError $e){
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου λιανικής').' (4):<br><b>'.$row['product_price_retail_sheets_formula'].'</b><br>'.$e->getMessage();
    } catch(AssertionError $e){
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου λιανικής').' (5):<br><b>'.$row['product_price_retail_sheets_formula'].'</b><br>'.$e->getMessage();
    } catch(Exception  $e){
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου λιανικής').' (6):<br><b>'.$row['product_price_retail_sheets_formula'].'</b><br>'.$e->getMessage();
    }

  }
  
  //print '<pre>aaa ';print $errors;die();
  
  
  if ($row['product_price_retail_quantity_formula'] == '') {
    $out['quantitycheck_price_retail'] = $quantity_mm*$out['quantitycheck_price_item_retail'];
  } else {
    
    $row['product_price_retail_quantity_formula']=str_replace('[quantity]', $quantity_mm, $row['product_price_retail_quantity_formula']);
    $row['product_price_retail_quantity_formula']=str_replace('[itemprice]', $out['quantitycheck_price_item_retail'], $row['product_price_retail_quantity_formula']);
    $row['product_price_retail_quantity_formula']=str_replace('[price]', $out['quantitycheck_price'], $row['product_price_retail_quantity_formula']);
    
    $eval_result=false;
    $eval_formula='$eval_calc='.$row['product_price_retail_quantity_formula'].'; $eval_result=true;';

    try {
      $res=eval($eval_formula);
      $out['quantitycheck_price_retail'] = $eval_calc;
    } catch (ParseError  $e) {
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου λιανικής').' (1):<br><b>'.$row['product_price_retail_quantity_formula'].'</b><br>'.$e->getMessage();
    } catch(ArithmeticError $e){
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου λιανικής').' (2):<br><b>'.$row['product_price_retail_quantity_formula'].'</b><br>'.$e->getMessage();
    } catch(DivisionByZeroError $e){
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου λιανικής').' (3):<br><b>'.$row['product_price_retail_quantity_formula'].'</b><br>'.$e->getMessage();
    } catch(TypeError $e){
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου λιανικής').' (4):<br><b>'.$row['product_price_retail_quantity_formula'].'</b><br>'.$e->getMessage();
    } catch(AssertionError $e){
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου λιανικής').' (5):<br><b>'.$row['product_price_retail_quantity_formula'].'</b><br>'.$e->getMessage();
    } catch(Exception  $e){
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου λιανικής').' (6):<br><b>'.$row['product_price_retail_quantity_formula'].'</b><br>'.$e->getMessage();
    }

  }


  //plist
  if ($row['product_price_plist_sheets_formula'] == '') {
    $out['quantitycheck_price_item_plist'] = $row['product_price_plist_calc'];
    //if ($out['ok'] and $out['epi']!=0 and $out['epi']!=1) 
  } else {
    $row['product_price_plist_sheets_formula']=str_replace('[sheets]', $sheets, $row['product_price_plist_sheets_formula']);
    $row['product_price_plist_sheets_formula']=str_replace('[itemprice]', $row['product_price_plist_calc'], $row['product_price_plist_sheets_formula']);
    //echo '<pre>';print_r($row);die();
    
    $eval_result=false;
    $eval_formula='$eval_calc='.$row['product_price_plist_sheets_formula'].'; $eval_result=true;';
    

    try {
      $res=eval($eval_formula);
      $out['quantitycheck_price_item_plist'] = $eval_calc;
      //if ($row['id_product']==12498) {echo '<pre>';print_r($out);die();}
    } catch (ParseError  $e) {
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου τιμοκαταλόγου').' (1):<br><b>'.$row['product_price_plist_sheets_formula'].'</b><br>'.$e->getMessage();
    } catch(ArithmeticError $e){
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου τιμοκαταλόγου').' (2):<br><b>'.$row['product_price_plist_sheets_formula'].'</b><br>'.$e->getMessage();
    } catch(DivisionByZeroError $e){
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου τιμοκαταλόγου').' (3):<br><b>'.$row['product_price_plist_sheets_formula'].'</b><br>'.$e->getMessage();
    } catch(TypeError $e){
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου τιμοκαταλόγου').' (4):<br><b>'.$row['product_price_plist_sheets_formula'].'</b><br>'.$e->getMessage();
    } catch(AssertionError $e){
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου τιμοκαταλόγου').' (5):<br><b>'.$row['product_price_plist_sheets_formula'].'</b><br>'.$e->getMessage();
    } catch(Exception  $e){
      $errors.=gks_lang('Τύπος υπολογισμού τεμαχίου τιμοκαταλόγου').' (6):<br><b>'.$row['product_price_plist_sheets_formula'].'</b><br>'.$e->getMessage();
    }
    
  }
  //echo 'ffffff||'.$eval_result.'||'.$errors.'||'.ob_get_contents();
  //echo $quantity_mm.' x '.$out['quantitycheck_price_item_plist'];
  //die();
  
  
  if ($row['product_price_plist_quantity_formula'] == '') {
    $out['quantitycheck_price_plist'] = $quantity_mm*$out['quantitycheck_price_item_plist'];
  } else {
    $row['product_price_plist_quantity_formula']=str_replace('[quantity]', $quantity_mm, $row['product_price_plist_quantity_formula']);
    $row['product_price_plist_quantity_formula']=str_replace('[itemprice]', $out['quantitycheck_price_item_plist'], $row['product_price_plist_quantity_formula']);
    
    $eval_result=false;
    $eval_formula='$eval_calc='.$row['product_price_plist_quantity_formula'].'; $eval_result=true;';
    //echo 'gggggggg';
    //if ($row['id_product']==12498) {echo '<pre>';print_r($out);die();}

    try {
      $res=eval($eval_formula);
      $out['quantitycheck_price_plist'] = $eval_calc;
      //if ($row['id_product']==12498) {echo '<pre>sss ';print_r($out);die();}
      
    } catch (ParseError  $e) {
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου τιμοκαταλόγου').' (1):<br><b>'.$row['product_price_plist_quantity_formula'].'</b><br>'.$e->getMessage();
    } catch(ArithmeticError $e){
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου τιμοκαταλόγου').' (2):<br><b>'.$row['product_price_plist_quantity_formula'].'</b><br>'.$e->getMessage();
    } catch(DivisionByZeroError $e){
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου τιμοκαταλόγου').' (3):<br><b>'.$row['product_price_plist_quantity_formula'].'</b><br>'.$e->getMessage();
    } catch(TypeError $e){
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου τιμοκαταλόγου').' (4):<br><b>'.$row['product_price_plist_quantity_formula'].'</b><br>'.$e->getMessage();
    } catch(AssertionError $e){
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου τιμοκαταλόγου').' (5):<br><b>'.$row['product_price_plist_quantity_formula'].'</b><br>'.$e->getMessage();
    } catch(Exception  $e){
      $errors.=gks_lang('Τύπος υπολογισμού συνόλου τιμοκαταλόγου').' (6):<br><b>'.$row['product_price_plist_quantity_formula'].'</b><br>'.$e->getMessage();
    }

  }
  
//  if ($row['id_product'] == 12498) { 
//    print '<pre>ddd';
//    print_r($row);
//    print_r($out);
//    die();
// }
    
  if ($reset_buffer) {
    $my_buffer_contents=ob_get_clean();
    if ($my_buffer_contents!='') {
      $errors.=gks_lang('Σφάλμα').':<br><br>'.$my_buffer_contents;
    }
  }
      
  if ($errors!='') {
    //echo $errors;
    //die();
    debug_mail(false,'gks_price_formula_calc id: '.$row['id_product'],$errors);      
    return $errors;  
  }
  return '';
}



$gks_get_pricelist_items_data=[];
function gks_get_pricelist_items($pricelist_id,$mydate) {
  global $db_link;
  global $gks_get_pricelist_items_data;
  if ($pricelist_id<=0) return;
  if ($mydate=='') return;
  $key=$pricelist_id.'|'.$mydate;
  if (isset($gks_get_pricelist_items_data[$key])) return;
  
  global $gks_get_products_categories_tree_data;
  gks_get_products_categories_tree();

  global $gks_get_products_brands_tree_data;
  gks_get_products_brands_tree();

  $sql="SELECT *
  from gks_eshop_pricelist_items
  where pricelist_id=".$pricelist_id."
  and pricelist_item_disable=0
  and (pricelist_item_coupon='' or pricelist_item_coupon is null)
  and (
    (pricelist_item_date_from is null        and pricelist_item_date_to is null) or 
    (pricelist_item_date_from<='".$mydate."' and pricelist_item_date_to is null) or
    (pricelist_item_date_from is null        and pricelist_item_date_to>='".$mydate."') or
    (pricelist_item_date_from<='".$mydate."' and pricelist_item_date_to>='".$mydate."') 
  )
  order by pricelist_item_sequence";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
  $gks_get_pricelist_items_data[$key]=array(
    'pricelist_id'=>$pricelist_id,
    'mydate'=>$mydate,
    'items'=>[],
  );
  $ids=[];
  while ($row = $result->fetch_assoc()) { 
    $ids[]=$row['id_pricelist_item'];
    $row['list_products']=[];
    $row['list_categories']=[];
    $row['list_brands']=[];
    $gks_get_pricelist_items_data[$key]['items'][$row['id_pricelist_item']]=$row;
  }
  
  if (count($ids)>0) {
    $sql="SELECT gks_eshop_pricelist_items_products.pricelist_item_id, 
    gks_eshop_pricelist_items_products.product_id, 
    gks_eshop_pricelist_items_products.is_include, 
    gks_eshop_products.product_class
    FROM gks_eshop_pricelist_items_products 
    LEFT JOIN gks_eshop_products ON gks_eshop_pricelist_items_products.product_id = gks_eshop_products.id_product
    WHERE gks_eshop_pricelist_items_products.pricelist_item_id In (".implode(',',$ids).")
    order by id_pricelist_item_product";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}

    while ($row = $result->fetch_assoc()) {
      $gks_get_pricelist_items_data[$key]['items'][$row['pricelist_item_id']]['list_products'][$row['product_id']]=intval($row['is_include']);
      if ($row['product_class']=='variable') {
        $sqlp="SELECT id_product
        FROM gks_eshop_products
        where product_parent_id=".$row['product_id']."
        and product_class='variable_item'
        and id_product<>".$row['product_id'];
        $resultp = $db_link->query($sqlp);        
        if (!$resultp) {debug_mail(false,'error sql',$sqlp);die('sql error');}
        while ($rowp = $resultp->fetch_assoc()) {
          $gks_get_pricelist_items_data[$key]['items'][$row['pricelist_item_id']]['list_products'][$rowp['id_product']]=intval($row['is_include']);
        }
      }
    }
    
    //print '<pre>';print_r($gks_get_pricelist_items_data);die();
    
    
    $sql="select pricelist_item_id,product_category_id,is_include
    from gks_eshop_pricelist_items_categories
    where pricelist_item_id in (".implode(',',$ids).")";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
    while ($row = $result->fetch_assoc()) {
      $is_include=intval($row['is_include']);
      $gks_get_pricelist_items_data[$key]['items'][$row['pricelist_item_id']]['list_categories'][$row['product_category_id']]=$is_include;
      if (isset($gks_get_products_categories_tree_data[$row['product_category_id']])) {
        foreach ($gks_get_products_categories_tree_data[$row['product_category_id']]['childs'] as $vvvv) {
          $gks_get_pricelist_items_data[$key]['items'][$row['pricelist_item_id']]['list_categories'][$vvvv]=$is_include;
        }
      }
    }
    
    $sql="select pricelist_item_id,product_brand_id,is_include
    from gks_eshop_pricelist_items_brands
    where pricelist_item_id in (".implode(',',$ids).")";
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
    while ($row = $result->fetch_assoc()) {
      $is_include=intval($row['is_include']);
      $gks_get_pricelist_items_data[$key]['items'][$row['pricelist_item_id']]['list_brands'][$row['product_brand_id']]=$is_include;
      if (isset($gks_get_products_brands_tree_data[$row['product_brand_id']])) {
        foreach ($gks_get_products_brands_tree_data[$row['product_brand_id']]['childs'] as $vvvv) {
          $gks_get_pricelist_items_data[$key]['items'][$row['pricelist_item_id']]['list_brands'][$vvvv]=$is_include;
        }
      }
    }
  }
  //echo '<pre>'.$key.'</pre>';
  //echo '<pre>';print_r($gks_get_pricelist_items_data);die();
}



function gks_get_product_price_plist($product_id) {
  global $db_link;
  $ret=[];
  if ($product_id<=0) return [];
  $sql="SELECT id_pricelist, pricelist_descr, price_is_xondriki
  from gks_eshop_pricelist
  where id_pricelist>=10001
  AND pricelist_disable=0
  ORDER BY sortorder,id_pricelist";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }
  while ($row = $result->fetch_assoc()) {
    $row['id_pricelist']=intval($row['id_pricelist']);
    $ret[$row['id_pricelist']]=array(
      'id_pricelist'=>intval($row['id_pricelist']),
      'pricelist_descr'=>trim_gks($row['pricelist_descr']),
      'price_is_xondriki' =>intval($row['price_is_xondriki']),
      'products'=>[],
    );
    
  }
  //print '<pre>';print_r($ret);die();
  
  $sql="SELECT pricelist_id,product_id,
  product_price_plist, 
  product_price_plist_sale, 
  product_price_plist_sale_from, 
  product_price_plist_sale_to, 
  product_price_plist_sheets_formula, 
  product_price_plist_quantity_formula, 
  product_price_plist_include_vat, 
  quantitycheck_price_plist
  from gks_eshop_products_prices 
  where product_id=".$product_id."
  or product_id in (
    select id_product from gks_eshop_products where product_parent_id=".$product_id."
  )";
  //echo '<pre>'.$sql;die();
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }
  while ($row = $result->fetch_assoc()) {
    if (isset($ret[$row['pricelist_id']])) {
      if (isset($ret[$row['pricelist_id']]['products'][$row['product_id']])==false) {
        $ret[$row['pricelist_id']]['products'][$row['product_id']]=[];
      }
      $ret[$row['pricelist_id']]['products'][$row['product_id']]=array(
        'product_price_plist'=>floatval($row['product_price_plist']),
        'product_price_plist_sale'=>floatval($row['product_price_plist_sale']),
        'product_price_plist_sale_from'=>trim_gks($row['product_price_plist_sale_from']),
        'product_price_plist_sale_to'=>trim_gks($row['product_price_plist_sale_to']),
        'product_price_plist_sheets_formula'=>trim_gks($row['product_price_plist_sheets_formula']),
        'product_price_plist_quantity_formula'=>trim_gks($row['product_price_plist_quantity_formula']),
        'product_price_plist_include_vat'=>intval($row['product_price_plist_include_vat']),
        'quantitycheck_price_plist'=>floatval($row['quantitycheck_price_plist']),
      );
    }
  }
  
  //print '<pre>';print_r($ret);die();
  return $ret;
  
}





function gks_basket_warnings(&$mybasketarray) {
  $allwarnings=array();
  //return $allwarnings;
  
  foreach ($mybasketarray['products'] as $keyp => &$product) {
    foreach ($product['objects'] as $keyo => &$object) {
      $object['warnings'] = array();

  
      if ($product['product_id']['product_need_multi_files']!=0) {
        $mycc=0;
        foreach ($object['files'] as $filetemp) {
          $mycc+=$filetemp['copies'];  
        }
        if ($mycc < $product['product_id']['product_need_multi_files_min']) {
          if ($product['product_id']['product_need_multi_files_min'] == $product['product_id']['product_need_multi_files_max']) {
            $tmpmsg=gks_lang('Θα πρέπει να εισάγετε [1] αρχεία για αυτό το προϊόν');
            $tmpmsg=str_replace('[1]',$product['product_id']['product_need_multi_files_min'],$tmpmsg);            
            $mywarning = array(
            'id' => 'warning_span_'.$keyp.'_'.$keyo ,
            'html' => '<img src="/my/img/warning.gif" style="padding:2px">', 
            'tp' => $tmpmsg);
          } else {
            $tmpmsg=gks_lang('Θα πρέπει να εισάγετε τουλάχιστον [1] αρχεία για αυτό το προϊόν');
            $tmpmsg=str_replace('[1]',$product['product_id']['product_need_multi_files_min'],$tmpmsg);            
            $mywarning = array(
            'id' => 'warning_span_'.$keyp.'_'.$keyo ,
            'html' => '<img src="/my/img/warning.gif" style="padding:2px">', 
            'tp' => $tmpmsg);
          }
          $object['warnings'][] = $mywarning;
        }
      }
      foreach ($object['files'] as &$file) {
        $file['warnings'] = array();
        
        if ($product['product_id']['product_need_multi_files']!=0) {
          if ($file['copies']>1) {
            $tmpmsg=gks_lang('Σίγουρα θέλετε το συγκεκριμένο αρχείο να υπάρχει [1] φορές σε αυτό το προϊόν;');
            $tmpmsg=str_replace('[1]',$file['copies'],$tmpmsg);            
            $mywarning = array(
            'id' => 'warning_span_file_'.$keyp.'_'.$keyo.'_'.$file['id'] ,
            'html' => '<img src="/my/img/warning.gif" style="padding:0px" width="20px">', 
            'tp' => $tmpmsg);
            $file['warnings'][] = $mywarning;
          }
        }
        if ($product['product_id']['product_min_pixels_x'] > 0 and $product['product_id']['product_min_pixels_y'] > 0) {
            
          if ($product['product_id']['product_min_pixels_can_rotate'] == 0) { //den mporei na peristrafi to proion, p.x. koupa
            if ($product['product_id']['product_min_pixels_x'] > $file['width'] or $product['product_id']['product_min_pixels_y'] > $file['height']) {
              $tmpmsg=gks_lang('Το αρχείο έχει πολύ μικρή ανάλυση. Θα πρέπει να είναι τουλάχιστον [1]x[2] pixels.');
              $tmpmsg=str_replace('[1]',$product['product_id']['product_min_pixels_x'],$tmpmsg);
              $tmpmsg=str_replace('[2]',$product['product_id']['product_min_pixels_y'],$tmpmsg);
              $mywarning = array(
              'id' => 'warning_span_file_'.$keyp.'_'.$keyo.'_'.$file['id'] ,
              'html' => '<img src="/my/img/warning.gif" style="padding:0px" width="20px">', 
              'tp' => $tmpmsg);
              $file['warnings'][] = $mywarning;                
            }
            
          } else { //mporei na peristrafi to proion, p.x. ekt;yposi 10x15               
            //photo
            $imx = $file['width'];
            $imy = $file['height'];
            if ($imy > $imx) { // panta x > y
              $tt = $imy;
              $imy = $imx;
              $imx = $tt;
            }
            //plaisio
            $frx=$product['product_id']['product_min_pixels_x'];
            $fry=$product['product_id']['product_min_pixels_y'];
            if ($fry > $frx) { // panta x > y
              $tt = $fry;
              $fry = $frx;
              $frx = $tt;
            }
            
            
            if ($imx / $imy > $frx / $fry) { //i photo einai pio makroyli apo to plaisio, ara kratao to y kai kobo to x
              //imy=imy
              $imx = $imy * ($frx / $fry);
              
            } else {// ara kratao to x kai kobo to y
              //imx=imx
              $imy = $imx * ($fry / $frx);              
            }

            if ($frx > $imx or $fry > $imy) {
              $tmpmsg=gks_lang('Το αρχείο έχει πολύ μικρή ανάλυση. Θα πρέπει να είναι τουλάχιστον [1]x[2] pixels.');
              $tmpmsg=str_replace('[1]',$frx,$tmpmsg);
              $tmpmsg=str_replace('[2]',$fry,$tmpmsg);
              $mywarning = array(
              'id' => 'warning_span_file_'.$keyp.'_'.$keyo.'_'.$file['id'] ,
              'html' => '<img src="/my/img/warning.gif" style="padding:0px" width="20px">', 
              'tp' => $tmpmsg);
              $file['warnings'][] = $mywarning;                
            }            
            
          }
            
        }
        
      }
      unset($file);
      
      
    
    }
    unset($object);
  }
  unset($product);

  unset($mywarning);
  

  foreach ($mybasketarray['products'] as $product) {
    foreach ($product['objects'] as $object) {
      foreach ($object['warnings'] as $mywarning) {
        $allwarnings[] = $mywarning;
      }
      foreach ($object['files'] as $file) {
        foreach ($file['warnings'] as $mywarning) {
          $allwarnings[] = $mywarning;
        }
      }
    }
  }
  
  return $allwarnings;
  
}
function gks_send_email_order_receive($id, $force=false) {
  global $db_link;
  
  $return = array('success' => false, 'message' => 'start...');
 
  $sql="SELECT gks_orders.*, gks_payment_acquirers.payment_acquirer_type
  FROM gks_orders 
  LEFT JOIN gks_payment_acquirers ON gks_orders.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer
  WHERE gks_orders.id_order=".$id;
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'warning on mymail error sql',$sql);
    $return = array('success' => false, 'message' => 'sql error');
    return $return;
  }
  if ($result->num_rows == 0) {
    $return = array('success' => false, 'message' => 'order not found');
    return $return;
  }  
  $row = $result->fetch_assoc();
  $user_email=trim_gks($row['user_email']);
  if ($user_email=='') {
    $return = array('success' => false, 'message' => 'email is empty');
    return $return;
  }
  $bank_deposit_9digit = trim_gks($row['bank_deposit_9digit']);
  $pliroteo=$row['gks_price_total'] + $row['kostos_apostolis'] + $row['kostos_pliromis'];
  $payment_acquirer_type=trim_gks($row['payment_acquirer_type']);
  
  $sql="SELECT id FROM gks_email WHERE model='order' AND model_id=".$id." AND template='order_receive.html' and date_add>'".date('Y-m-d H:i:s', time() - 24*60*60)."'";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'warning on mymail error sql',$sql);
    $return = array('success' => false, 'message' => 'sql error');
    return $return;
    
  }
  if ($force==false && $result->num_rows > 0) {
    $return = array('success' => false, 'message' => 'Already send');
    return $return;    
  }
  $mymessage='';
  $mymessage.='<p style="margin-top: 40px;">Hello,</p>';
  $mymessage.='<p>Your order has been successfully received.</p>';
  $mymessage.='<p style="font-size:16pt;font-weight: bold;">Order number: '.$id.'</p>';
  
  if ($payment_acquirer_type == 'bank') {
    $mymessage.='<p>To advance your order you will need to deposit the amount: ';
    $mymessage.='<b>'.myCurrencyFormat($pliroteo).'</b> ';
    $mymessage.='in one of the following bank accounts:<br><b>';
    $mymessage.=gks_get_list_bank_accounts();
    $mymessage.='</b><br>';
    $mymessage.='When submitting a deposit, specify the number in the Reason <b>'.$bank_deposit_9digit.'</b> ';
    $mymessage.='so that we can identify the deposit with that particular order.';
    $mymessage.='</p>';
    
    $mymessage.='<p>If there are any costs involved in transferring the money, you will have to bear it.<br>';
    $mymessage.='Your order will not be executed if the final amount does not match.<br>';
    $mymessage.='Email us your proof of deposit <a href="info@easyfilesselection.com">info@easyfilesselection.com</a></p>';
  }           
                
  $mymessage.='<p>You will be notified by email about the order progress.</p>';
  $mymessage.='<p>If you have any questions, please feel free to contact us.</p>';
  
  $replaces=array();
  $replaces[] = array('[[id_order]]',$id);
  $replaces[] = array('[[message]]',$mymessage);

  $params=array(
    'model'=>'order',
    'model_id'=>$id,
    'to'=>$user_email,
    'subject'=>'Order '.$id,
    'template'=> 15, //'order_receive.html',
    'replaces'=>$replaces,
  );
    
  $send_email_res = gks_mymail_template($params);
    
  $return = array('success' => true, 'message' => 'OK', 'email', $user_email);
  return $return;    
  

}

function gks_send_email_order_execute($id) {
  global $db_link;
  $return = array('success' => false, 'message' => 'start...');
  
  $sql="SELECT gks_orders.*
  FROM gks_orders 
  WHERE gks_orders.id_order=".$id;
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'warning on mymail error sql',$sql);
    $return = array('success' => false, 'message' => 'sql error');
    return $return;
  }
  if ($result->num_rows == 0) {
    $return = array('success' => false, 'message' => 'order not found');
    return $return;
  }  
  $row = $result->fetch_assoc();
  $user_email=trim_gks($row['user_email']);
  if ($user_email=='') {
    $return = array('success' => false, 'message' => 'email is empty');
    return $return;
  }
    
  //$mymessage='<p style="font-size:22pt;padding:0px 0px 20px 0px;margin:0px;font-weight: bold;">Order number: '.$id.'</p>';
  $mymessage='';

  $replaces=array();
  $replaces[] = array('[[id_order]]',$id);
  $replaces[] = array('[[message]]',$mymessage);
  
  $params=array(
    'model'=>'order',
    'model_id'=>$id,
    'to'=>$user_email,
    'subject'=>gks_lang('Επιβεβαίωση της παραγγελίας σας').' '.$id,
    'template'=> 9, //'order_execute.html',
    'replaces'=>$replaces,
  );
    
  $send_email_res = gks_mymail_template($params);

  $return = array('success' => true, 'message' => 'OK', 'email', $user_email);
  return $return;    

}

function gks_send_email_order_partial($id) {
  global $db_link;
  
  $mymessage='Hello,<br>
  <br>
  Your order with number '.$id.' has been partially executed.<br>
  You will be notified with another message about how the order will progress.<br>';

  $replaces=array();
  $replaces[] = array('[[id_order]]',$id);
  $replaces[] = array('[[message]]',$mymessage);
  
  $params=array(
    'model'=>'order',
    'model_id'=>$id,
    'to'=>$user_email,
    'subject'=>'Partial order fulfillment '.$id,
    'template'=> 11, //'order_execute_partial.html',
    'replaces'=>$replaces,
  );
    
  $send_email_res = gks_mymail_template($params);
}



function gks_monada_convert($from, $to, &$out, $rec_edit) {
  //$rec_edit=array('id' => -1, 'parent' => 0, 'epi' => 1, 'epi_rev' => 1, 'descr' => '', 'symbol' => '');
  global $db_link;
  $from=intval($from);
  $to=intval($to);
  
  $out=array();
  $out['from']=$from;
  $out['from_descr']='';
  $out['from_circular']=false;
  $out['from_array']=array();
  $out['from_last_id']=0;
  $out['epi']=0;
  $out['epi_rev']=0;
  
  $out['to']=$to;
  $out['to_descr']='';
  $out['to_circular']=false;
  $out['to_array']=array();
  $out['to_last_id']=0;
  
  $out['ok']=false;
  

  $lang_prepare_gks_monades_metrisis=gks_lang_data_obj_prepare('gks_monades_metrisis','default');
  gks_lang_data_obj_sql_prepare($lang_prepare_gks_monades_metrisis, array('monada_descr','monada_symbol'));
  
  
  $sql="SELECT gks_monades_metrisis.id_monada as id,
  gks_monades_metrisis.monada_parent_id as parent,
  gks_monades_metrisis.monada_parent_epi as epi,".
  gks_lang_sql_field('monada_descr',$lang_prepare_gks_monades_metrisis,'descr').",".
  gks_lang_sql_field('monada_symbol',$lang_prepare_gks_monades_metrisis,'symbol')."
  FROM ".$lang_prepare_gks_monades_metrisis['sql']['from1']." gks_monades_metrisis
  ".$lang_prepare_gks_monades_metrisis['sql']['from2']."
  order by id_monada";
  //echo '<pre>'.$sql;die();

  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
  $monades=array(); 
  while ($row = $result->fetch_assoc()) {
    if ($row['epi']==0) {
      $row['epi_rev']=0; 
    } else {
      $row['epi_rev']=1/$row['epi'];
    }
    if (isset($rec_edit['id']) and $rec_edit['id'] == $row['id']) {
      $monades[]=$rec_edit;
    } else {
      $monades[]=$row;
    }
  }
  if (isset($rec_edit['id']) and $rec_edit['id'] == -1) {
    $monades[]=$rec_edit;
  }
  
  
  if ($from==$to) {
    foreach ($monades as $monada) {
      if ($out['from']==$monada['id']) {
        $out['from_descr']=$monada['descr'];
      }
      if ($out['to']==$monada['id']) {
        $out['to_descr']=$monada['descr'];
      }
    } 
    $out['epi']=1;
    $out['epi_rev']=1;
    $out['ok']=true;
    return;
  }  
  
//  print '<pre>';
//  print_r($rec_edit);
//  print_r($monades);
//  die();
  
  $from_parent=0;
  
  foreach ($monades as $mon) {
    if ($mon['id']==$from) {
      $out['from_descr']=$mon['descr'];
      $out['from_array'][$mon['id']]=array('id' => $mon['id'], 'parent' => $mon['parent'], 'epi' => $mon['epi'], 'epi_rev' => $mon['epi_rev'], 'descr' => $mon['descr'], 'symbol' => $mon['symbol']);
      $from_parent = $mon['parent'];
      $out['from_last_id']=$mon['id'];
      break;
    }
  } 
  do {
    if ($from_parent==0) break;
    foreach ($monades as $mon) {
      if ($mon['id']==$from_parent) {
        if (isset($out['from_array'][$mon['id']])) {
          $out['from_circular']=true;
          $to_parent=0; break 2;
        }        
        $out['from_array'][$mon['id']]=array('id' => $mon['id'], 'parent' => $mon['parent'], 'epi' => $mon['epi'], 'epi_rev' => $mon['epi_rev'], 'descr' => $mon['descr'], 'symbol' => $mon['symbol']);
        $from_parent = $mon['parent'];
        $out['from_last_id']=$mon['id'];
        break;
      }
    } 
  } while (true);

  $to_parent=0;
  foreach ($monades as $mon) {
    if ($mon['id']==$to) {
      $out['to_descr']=$mon['descr'];
      $out['to_array'][$mon['id']]=array('id' => $mon['id'], 'parent' => $mon['parent'], 'epi' => $mon['epi'], 'epi_rev' => $mon['epi_rev'], 'descr' => $mon['descr'], 'symbol' => $mon['symbol']);
      $to_parent = $mon['parent'];
      $out['to_last_id']=$mon['id'];
      break;
    }
  } 
  do {
    if ($to_parent==0) break;
    foreach ($monades as $mon) {
      if ($mon['id']==$to_parent) {
        if (isset($out['to_array'][$mon['id']])) {
          $out['to_circular']=true;
          $to_parent=0; break 2;
        }
        $out['to_array'][$mon['id']]=array('id' => $mon['id'], 'parent' => $mon['parent'], 'epi' => $mon['epi'], 'epi_rev' => $mon['epi_rev'], 'descr' => $mon['descr'], 'symbol' => $mon['symbol']);
        $to_parent = $mon['parent'];
        $out['to_last_id']=$mon['id'];
        break;
      }
    } 
  } while (true);
  if ($out['from_circular'] or $out['to_circular']) return;
  if (!($out['from_last_id'] > 0 and $out['to_last_id'] > 0 and $out['from_last_id'] == $out['to_last_id'])) return;
  
  $from_epi=1;
  foreach ($out['from_array'] as $val) {
    if ($val['parent']!=0) $from_epi=$from_epi * $val['epi'];
  } 
  $to_epi=1;
  foreach ($out['to_array'] as $val) {
    if ($val['parent']!=0) $to_epi=$to_epi * $val['epi'];
  } 
  
  if ($from_epi!=0 and $to_epi!=0) {
    $out['epi']=$from_epi/$to_epi;
    $out['epi_rev']=$to_epi/$from_epi;
  }
  
  
  if ($out['epi'] != 0) $out['ok']=true;
  
//  print_r($out['from_array']);
//  print_r($out['to_array']);
//  print "\n";
//  print $from_epi;
//  print "\n";
//  print $to_epi;
//  print "\n";
  
  return;
  
}


function gks_monada_recs_convert($filters=array()) {
  global $db_link;
  
  $id_monada=0;  if (isset($filters['id_monada']))  $id_monada=intval($filters['id_monada']);
  $id_product=array(); if (isset($filters['id_product'])) $id_product[]=intval($filters['id_product']);
  
  
  if (count($id_product)>0) {
    $sql="select id_product from gks_eshop_products where product_parent_id=".$id_product[0];
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    while ($row = $result->fetch_assoc()) {
      $id_product[]=$row['id_product'];
    }  
    
  }
//  $id_order=0;   if (isset($filters['id_order']))   $id_order=intval($filters['id_order']);
//  $id_acc_inv=0; if (isset($filters['id_acc_inv'])) $id_acc_inv=intval($filters['id_acc_inv']);
//  $id_whi_mov=0; if (isset($filters['id_whi_mov'])) $id_whi_mov=intval($filters['id_whi_mov']);
  
  
  
  $all_products_for_balance=array();
  
  $tables=array(
    1 => array('gks_orders_products','id_order_product','product_id','product_monada_id_org','product_monada_id'),
    2 => array('gks_acc_inv_products','id_acc_inv_product','product_id','product_monada_id_org','product_monada_id'),
    3 => array('gks_whi_mov_products','id_whi_mov_product','product_id','product_monada_id_org','product_monada_id'),
    4 => array('gks_production_sintagi_product','id_production_sintagi_product','spbom_product_id','spbom_monada_id_org','spbom_monada_id'),
  );
  
  $recs=array();
  foreach ($tables as $t => $myt) {
    $sql="select ".$myt[1]." as id ,".$myt[2]." as product_id,".$myt[3]." as product_monada_id_org,".$myt[4]." as product_monada_id 
    from ".$myt[0]. " where 1=1 ";
    if ($id_monada>0) $sql.=" and (".$myt[3]."=".$id_monada." or ".$myt[4]."=".$id_monada.")";
    if (count($id_product)>0) $sql.=" and ".$myt[2]." in (".implode(',',$id_product).") ";
    //echo '<pre>'.$sql."\n\n\n</pre>";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    while ($row = $result->fetch_assoc()) {$row['t']=$t;$recs[]=$row; }
  } 
  
  //echo '<pre>';print_r($recs);die();
  
  foreach ($recs as $row) {
    if (in_array($row['product_id'], $all_products_for_balance)==false) $all_products_for_balance[]=$row['product_id'];
    $product_monada_id_org=$row['product_monada_id_org'];
    $product_monada_id=$row['product_monada_id'];

    $monada_convert=array();
    gks_monada_convert($product_monada_id_org, $product_monada_id, $monada_convert, array());

    $sql="update ".$tables[$row['t']][0]." set ";
    if (isset($monada_convert) and 
        isset($monada_convert['ok']) and 
        isset($monada_convert['epi']) and 
        $monada_convert['ok'] and 
        $monada_convert['epi'] !=0 and
        $monada_convert['epi'] != 1) {
      $sql.="monada_convert_epi=".number_format($monada_convert['epi'],16,'.','').",";
      $sql.="monada_convert_epi_rev=".number_format($monada_convert['epi_rev'],16,'.','')."";
    } else {
      $sql.="monada_convert_epi=1,";
      $sql.="monada_convert_epi_rev=1";
    }
    $sql.=" where ".$tables[$row['t']][1]."=".$row['id'];
    //echo '<pre>'.$sql."\n\n\n</pre>";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
  }
  
  
  
  //print '<pre>';print_r($all_products_for_balance);
  
  
  
  gks_whi_mov_balance_calc($all_products_for_balance);
  return true;
  
}

function gks_user_call_withheld_hootel050($value) {
  return $value['product_quantity'] * 0.5;
}
function gks_user_call_withheld_hootel150($value) {
  return $value['product_quantity'] * 1.5;
}
function gks_user_call_withheld_hootel300($value) {
  return $value['product_quantity'] * 3;
}
function gks_user_call_withheld_hootel400($value) {
  return $value['product_quantity'] * 4;
}
function gks_user_call_withheld_roomstolet050($value) {
  return $value['product_quantity'] * 0.5;
}
function gks_user_call_withheld_roomstolet050x2($value) {
  return $value['product_quantity'] * 2.0;
}
function gks_user_call_fees_sakoula($value) {
  return $value['product_quantity'] * 0.07;
}

function gks_user_call_fees_plastic_products($value) {
  return $value['product_quantity'] * 0.04;
}
function gks_user_call_fees_anakiklosi($value) {
  return $value['product_quantity'] * 0.08;
}

function gks_get_bank_deposit_9digit() {
  global $db_link;
  
  do {
    $isOK=false;
    $bank_deposit_9digit=rand(100000000,999999999);
    
    
    $sql = "SELECT bank_deposit_9digit from gks_orders where bank_deposit_9digit='".$db_link->escape_string($bank_deposit_9digit)."' limit 1";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);}
    if ($result->num_rows > 0) goto next_step;
    
    $sql = "SELECT bank_deposit_9digit from gks_acc_inv where bank_deposit_9digit='".$db_link->escape_string($bank_deposit_9digit)."' limit 1";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);}
    if ($result->num_rows > 0) goto next_step;
    
    $sql = "SELECT bank_deposit_9digit from gks_acc_pay where bank_deposit_9digit='".$db_link->escape_string($bank_deposit_9digit)."' limit 1";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);}
    if ($result->num_rows > 0) goto next_step;
    
    $sql = "SELECT bank_deposit_9digit from gks_whi_mov where bank_deposit_9digit='".$db_link->escape_string($bank_deposit_9digit)."' limit 1";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);}
    if ($result->num_rows > 0) goto next_step;
    
    $sql = "SELECT bank_deposit_9digit from gks_hotel_reservation where bank_deposit_9digit='".$db_link->escape_string($bank_deposit_9digit)."' limit 1";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);}
    if ($result->num_rows > 0) goto next_step;
    
    $sql = "SELECT bank_deposit_9digit from gks_transfer_reservation where bank_deposit_9digit='".$db_link->escape_string($bank_deposit_9digit)."' limit 1";
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'error sql',$sql);}
    if ($result->num_rows > 0) goto next_step;
    
    
    break;
    next_step:
    //echo $bank_deposit_9digit.'|';
    
  } while(true); 
  return $bank_deposit_9digit;
  
}

function gks_format_bank_deposit_9digit($value) {
  $value=trim_gks($value);
  if (strlen($value)==9) {
    return substr($value,0,3).' '.substr($value,3,3).' '.substr($value,6,3);
  } else {
    return $value; 
  }
}

function gks_setall_bank_deposit_9digit() {
  global $db_link;
  
  //gks_orders
  $sql="select id_order from gks_orders where bank_deposit_9digit is null or bank_deposit_9digit=''";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  $myarray=array();
  while ($row = $result->fetch_assoc()) {
    $myarray[]=$row['id_order'];
  }
  
  foreach ($myarray as $value) {
    $sql="update gks_orders set bank_deposit_9digit='".gks_get_bank_deposit_9digit()."' where id_order=".$value;
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
  }
  
  //gks_acc_inv
  $sql="select id_acc_inv from gks_acc_inv where bank_deposit_9digit is null or bank_deposit_9digit=''";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  $myarray=array();
  while ($row = $result->fetch_assoc()) {
    $myarray[]=$row['id_acc_inv'];
  }
  
  foreach ($myarray as $value) {
    $sql="update gks_acc_inv set bank_deposit_9digit='".gks_get_bank_deposit_9digit()."' where id_acc_inv=".$value;
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
  }
  
  //gks_acc_pay
  $sql="select id_acc_pay from gks_acc_pay where bank_deposit_9digit is null or bank_deposit_9digit=''";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  $myarray=array();
  while ($row = $result->fetch_assoc()) {
    $myarray[]=$row['id_acc_pay'];
  }
  
  foreach ($myarray as $value) {
    $sql="update gks_acc_pay set bank_deposit_9digit='".gks_get_bank_deposit_9digit()."' where id_acc_pay=".$value;
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
  }
  
  //gks_whi_mov
  $sql="select id_whi_mov from gks_whi_mov where bank_deposit_9digit is null or bank_deposit_9digit=''";
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  $myarray=array();
  while ($row = $result->fetch_assoc()) {
    $myarray[]=$row['id_whi_mov'];
  }
  
  foreach ($myarray as $value) {
    $sql="update gks_whi_mov set bank_deposit_9digit='".gks_get_bank_deposit_9digit()."' where id_whi_mov=".$value;
    $result = $db_link->query($sql);
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
  }
  
   
  
}


function gks_products_update_barcodes($product_ids=[]) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;  
  
  $ret=array('success' => false, 'message' => 'sql error');
  
  if (is_array($product_ids)==false){debug_mail(false,'error product_ids is not array',$product_ids);$ret['product_ids is not array'];return $ret;}
  if (count($product_ids)==0) {$ret['success']=true;$ret['message']='OK';return $ret;}
  
  $sql="select id_product,product_parent_id from gks_eshop_products where id_product in (".implode(',',$product_ids).") and id_product>0";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['sql error'];return $ret;}
  
  $ids=array();
  while ($row = $result->fetch_assoc()) {
    if (in_array($row['id_product'],$ids)==false) 
      $ids[]=$row['id_product'];
    if ($row['product_parent_id']>0) 
      if (in_array($row['product_parent_id'],$ids)==false) 
        $ids[]=$row['product_parent_id'];
  }
  if (count($ids)<=0) {$ret['success']=true;$ret['message']='OK';return $ret;}
  
  $sql="select id_product from gks_eshop_products where product_parent_id in (".implode(',',$ids).") and id_product>0";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['sql error'];return $ret;} 
  while ($row = $result->fetch_assoc()) {
    if (in_array($row['id_product'],$ids)==false) 
      $ids[]=$row['id_product'];
  }
  if (count($ids)<=0) {$ret['success']=true;$ret['message']='OK';return $ret;}
  //print '<pre>';print_r($ids);die();
  
 
  $sql="select id_product,product_code,product_sku,product_gtin,product_upc,product_ean,product_isbn,product_taric
  from gks_eshop_products
  where id_product in (".implode(',',$ids).")";
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['sql error'];return $ret;}  
  $pcodes=[];
  while ($row = $result->fetch_assoc()) {
    $pcodes[]=$row;
  }
  //print '<pre>';print_r($pcodes);die();

  $sql="select id_barcode,barcode,product_id
  from gks_barcodes 
  where product_id in(".implode(',',$ids).") 
  and user_id=0";  
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['sql error'];return $ret;}  
  $exist_recs=[];
  while ($row = $result->fetch_assoc()) {
    $row['_rec_delete']=true;
    $exist_recs[]=$row;
  } 
  
  $check_fields=['product_code','product_sku','product_gtin','product_upc','product_ean','product_isbn','product_taric'];
  foreach ($pcodes as &$p) {
    foreach ($check_fields as $c) {
      $val=trim_gks($p[$c]);
      
      if ($val!='') {
        $_rec_exist=false;
        foreach ($exist_recs as &$e) {
          if ($p['id_product']==$e['product_id'] and $val==$e['barcode']) {
            $e['_rec_delete']=false;
            $_rec_exist=true;
            break;
            //echo '<pre>'.$c.' '.$val.' ';print_r($p);print_r($e);die();
          }
        }
        unset($e);
        if ($_rec_exist==false) {
          //echo '<pre>add new rec';die();
          $barcode=trim_gks($p[$c]);
          $comments=strtoupper(str_replace('product_', '', $c));
          
          $sql="insert into gks_barcodes(
          mydate_add,mydate_edit,user_id_add,user_id_edit,myip,barcode,product_id,comments
          ) values (
          now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
          '".$db_link->escape_string($barcode)."',
          ".$p['id_product'].",
          '".$db_link->escape_string($comments)."'
          )";
          $result = $db_link->query($sql);  
          if (!$result) {debug_mail(false,'error sql',$sql);$ret['sql error'];return $ret;} 
          
          $exist_recs[]=array(
            'id_barcode' => $db_link->insert_id,
            'barcode'=>$barcode,
            'product_id' => $p['id_product'],
            '_rec_delete'=>false,
          );

        }
        
      }
    }
  } 
  unset($p);
  $delete_ids=[];
  foreach ($exist_recs as $e) {
    if ($e['_rec_delete']) $delete_ids[]=$e['id_barcode'];
  }
  if (count($delete_ids)>0) {
    $sql="delete from gks_barcodes where id_barcode in (".implode(',',$delete_ids).")";
    $result = $db_link->query($sql);  
    if (!$result) {debug_mail(false,'error sql',$sql);$ret['sql error'];return $ret;} 
    
  }
  //print '<pre>';print_r($delete_ids);print_r($pcodes);print_r($exist_recs);die();
  //print '<pre>';print_r($pcodes);print_r($exist_recs);die();
  
  $ret['success']=true;
  $ret['message']='OK';
  return $ret;
}

$gks_get_products_categories_tree_data=false;
function gks_get_products_categories_tree() {
  global $db_link;
  global $gks_get_products_categories_tree_data;
  if (is_array($gks_get_products_categories_tree_data)) return;
  $sql="SELECT
  gks_eshop_products_categories.id_product_category AS id1,
  ug2.id_product_category AS id2,
  ug3.id_product_category AS id3,
  ug4.id_product_category AS id4,
  ug5.id_product_category AS id5,
  ug6.id_product_category AS id6,
  ug7.id_product_category AS id7,
  ug8.id_product_category AS id8,
  ug9.id_product_category AS id9,
  ug10.id_product_category AS id10,
  CONCAT_WS('\\\\',
                ug10.product_category_descr,
                ug9.product_category_descr,
                ug8.product_category_descr,
                ug7.product_category_descr,
                ug6.product_category_descr,
                ug5.product_category_descr,
                ug4.product_category_descr,
                ug3.product_category_descr,
                ug2.product_category_descr,
                gks_eshop_products_categories.product_category_descr) as fullpath

  FROM  ((((((((gks_eshop_products_categories
  LEFT JOIN gks_eshop_products_categories AS ug2  ON gks_eshop_products_categories.product_category_parent_id = ug2.id_product_category)
  LEFT JOIN gks_eshop_products_categories AS ug3  ON ug2.product_category_parent_id = ug3.id_product_category)
  LEFT JOIN gks_eshop_products_categories AS ug4  ON ug3.product_category_parent_id = ug4.id_product_category)
  LEFT JOIN gks_eshop_products_categories AS ug5  ON ug4.product_category_parent_id = ug5.id_product_category)
  LEFT JOIN gks_eshop_products_categories AS ug6  ON ug5.product_category_parent_id = ug6.id_product_category)
  LEFT JOIN gks_eshop_products_categories AS ug7  ON ug6.product_category_parent_id = ug7.id_product_category)
  LEFT JOIN gks_eshop_products_categories AS ug8  ON ug7.product_category_parent_id = ug8.id_product_category)
  LEFT JOIN gks_eshop_products_categories AS ug9  ON ug8.product_category_parent_id = ug9.id_product_category)
  LEFT JOIN gks_eshop_products_categories AS ug10 ON ug9.product_category_parent_id = ug10.id_product_category
  ORDER BY fullpath";

  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['sql error'];return $ret;}  
  $gks_get_products_categories_tree_data=[];
  while ($row = $result->fetch_assoc()) {
    $row['parents']=[];
    $row['id1'] =intval($row['id1']);
    $row['id2'] =intval($row['id2']);
    $row['id3'] =intval($row['id3']);
    $row['id4'] =intval($row['id4']);
    $row['id5'] =intval($row['id5']);
    $row['id6'] =intval($row['id6']);
    $row['id7'] =intval($row['id7']);
    $row['id8'] =intval($row['id8']);
    $row['id9'] =intval($row['id9']);
    $row['id10']=intval($row['id10']);

    if ($row['id2']>0)  $row['parents'][]=$row['id2'];
    if ($row['id3']>0)  $row['parents'][]=$row['id3'];
    if ($row['id4']>0)  $row['parents'][]=$row['id4'];
    if ($row['id5']>0)  $row['parents'][]=$row['id5'];
    if ($row['id6']>0)  $row['parents'][]=$row['id6'];
    if ($row['id7']>0)  $row['parents'][]=$row['id7'];
    if ($row['id8']>0)  $row['parents'][]=$row['id8'];
    if ($row['id9']>0)  $row['parents'][]=$row['id9'];
    if ($row['id10']>0) $row['parents'][]=$row['id10'];

    $gks_get_products_categories_tree_data[$row['id1']]=$row;
  }
  
  foreach ($gks_get_products_categories_tree_data as &$row) {
    $row['childs']=[]; 
    $id1=$row['id1'];   
    foreach ($gks_get_products_categories_tree_data as $row2) {
      if (in_array($id1,$row2['parents'])) {
        $row['childs'][]=$row2['id1'];
      }
    }
  }
  unset($row);
  //print '<pre>';print_r($gks_get_products_categories_tree_data);die();
}

$gks_get_products_brands_tree_data=false;
function gks_get_products_brands_tree() {
  global $db_link;
  global $gks_get_products_brands_tree_data;
  if (is_array($gks_get_products_brands_tree_data)) return;
  $sql="SELECT
  gks_eshop_products_brands.id_product_brand AS id1,
  ug2.id_product_brand AS id2,
  ug3.id_product_brand AS id3,
  ug4.id_product_brand AS id4,
  ug5.id_product_brand AS id5,
  ug6.id_product_brand AS id6,
  ug7.id_product_brand AS id7,
  ug8.id_product_brand AS id8,
  ug9.id_product_brand AS id9,
  ug10.id_product_brand AS id10,
  CONCAT_WS('\\\\',
                ug10.product_brand_descr,
                ug9.product_brand_descr,
                ug8.product_brand_descr,
                ug7.product_brand_descr,
                ug6.product_brand_descr,
                ug5.product_brand_descr,
                ug4.product_brand_descr,
                ug3.product_brand_descr,
                ug2.product_brand_descr,
                gks_eshop_products_brands.product_brand_descr) as fullpath

  FROM  ((((((((gks_eshop_products_brands
  LEFT JOIN gks_eshop_products_brands AS ug2  ON gks_eshop_products_brands.product_brand_parent_id = ug2.id_product_brand)
  LEFT JOIN gks_eshop_products_brands AS ug3  ON ug2.product_brand_parent_id = ug3.id_product_brand)
  LEFT JOIN gks_eshop_products_brands AS ug4  ON ug3.product_brand_parent_id = ug4.id_product_brand)
  LEFT JOIN gks_eshop_products_brands AS ug5  ON ug4.product_brand_parent_id = ug5.id_product_brand)
  LEFT JOIN gks_eshop_products_brands AS ug6  ON ug5.product_brand_parent_id = ug6.id_product_brand)
  LEFT JOIN gks_eshop_products_brands AS ug7  ON ug6.product_brand_parent_id = ug7.id_product_brand)
  LEFT JOIN gks_eshop_products_brands AS ug8  ON ug7.product_brand_parent_id = ug8.id_product_brand)
  LEFT JOIN gks_eshop_products_brands AS ug9  ON ug8.product_brand_parent_id = ug9.id_product_brand)
  LEFT JOIN gks_eshop_products_brands AS ug10 ON ug9.product_brand_parent_id = ug10.id_product_brand
  ORDER BY fullpath";

  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['sql error'];return $ret;}  
  $gks_get_products_brands_tree_data=[];
  while ($row = $result->fetch_assoc()) {
    $row['parents']=[];
    $row['id1'] =intval($row['id1']);
    $row['id2'] =intval($row['id2']);
    $row['id3'] =intval($row['id3']);
    $row['id4'] =intval($row['id4']);
    $row['id5'] =intval($row['id5']);
    $row['id6'] =intval($row['id6']);
    $row['id7'] =intval($row['id7']);
    $row['id8'] =intval($row['id8']);
    $row['id9'] =intval($row['id9']);
    $row['id10']=intval($row['id10']);

    if ($row['id2']>0)  $row['parents'][]=$row['id2'];
    if ($row['id3']>0)  $row['parents'][]=$row['id3'];
    if ($row['id4']>0)  $row['parents'][]=$row['id4'];
    if ($row['id5']>0)  $row['parents'][]=$row['id5'];
    if ($row['id6']>0)  $row['parents'][]=$row['id6'];
    if ($row['id7']>0)  $row['parents'][]=$row['id7'];
    if ($row['id8']>0)  $row['parents'][]=$row['id8'];
    if ($row['id9']>0)  $row['parents'][]=$row['id9'];
    if ($row['id10']>0) $row['parents'][]=$row['id10'];

    $gks_get_products_brands_tree_data[$row['id1']]=$row;
  }
  
  foreach ($gks_get_products_brands_tree_data as &$row) {
    $row['childs']=[]; 
    $id1=$row['id1'];   
    foreach ($gks_get_products_brands_tree_data as $row2) {
      if (in_array($id1,$row2['parents'])) {
        $row['childs'][]=$row2['id1'];
      }
    }
  }
  unset($row);
  //print '<pre>';print_r($gks_get_products_brands_tree_data);die();
}


