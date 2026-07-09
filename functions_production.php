<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function getProductionLineStateDescr($mystate) {
  switch ($mystate) {
    case '010draft':      return gks_lang('Πρόχειρο','part4','getProductionLineStateDescr'); break; 
    case '020cancelled':  return gks_lang('Ακυρωμένο','part4','getProductionLineStateDescr'); break; 
    case '030pending':    return gks_lang('Αναμονή','part4','getProductionLineStateDescr'); break;  
    case '040ready':      return gks_lang('Προς Επεξεργασία','part4','getProductionLineStateDescr'); break; 
    case '050processing': return gks_lang('Σε Επεξεργασία','part4','getProductionLineStateDescr'); break; 
    case '060pause':      return gks_lang('Σε Παύση','part4','getProductionLineStateDescr'); break; 
    case '070failed':     return gks_lang('Απέτυχε','part4','getProductionLineStateDescr'); break; 
    case '100completed':  return gks_lang('Ολοκληρωμένο','part4','getProductionLineStateDescr'); break; 

    default: return $mystate; break; 
  } 
}

function gks_production_order_ergasies_error($message, $body) {
    debug_mail(false,str_replace('<br>',' ',$message),$body);  
    $return = array('success' => false, 'message' => base64_encode($message));  
    echo json_encode($return); 
    die();  
}

function gks_production_order_ergasies($id) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  
  $id=intval($id);
  if ($id<=0) gks_production_order_ergasies_error(gks_lang('Δεν έχει ορισθεί το').' ID',$id);
  
  $sql="select id_order,order_state,ddate from gks_orders where id_order=".$id;
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  if ($result->num_rows!=1) gks_production_order_ergasies_error(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Ανανεώστε την σελίδα'),$sql);
  $orders_array = $result->fetch_assoc();
  if ($orders_array['order_state']!='070inproduction') return;
  
  $sql="SELECT gks_orders_products.id_order_product, gks_orders_products.product_set, 
  gks_eshop_products.id_product, gks_eshop_products.product_parent_id, gks_eshop_products.product_class,
  gks_eshop_products.product_code, gks_eshop_products.product_descr, 
  gks_orders_products.product_sheets, gks_orders_products.product_quantity, gks_eshop_products.use_only_mine_ergasies
  FROM gks_orders_products 
  LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product
  WHERE gks_eshop_products.id_product Is Not Null and gks_orders_products.order_id=".$id." 
  AND gks_orders_products.product_is_optional in (0,2)
  order by id_order_product";
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  $orders_products_array=array();
  while ($row = $result->fetch_assoc()) {
    $product_set=trim_gks($row['product_set']);
    if (strpos($product_set, ',') !== false) {
      $parts=explode(',', $product_set);
      $product_set=array();
      foreach ($parts as $value) {
        $value=trim_gks($value);
        if ($value!='') $product_set[] = $value;
      } 
    } else {
      $product_set=array($product_set);
    }
    foreach ($product_set as $value) {
      if ($value=='') $value=gks_lang('κενό');
      $row['product_set'] = $value;
      $row['ergasies']=array();
      $orders_products_array[$row['id_order_product'].'|'.$value] = $row;
    }
  }
  
  //print '<pre>';print_r($orders_products_array);die();
  
  
  foreach ($orders_products_array as &$product) {
    
    if ($product['use_only_mine_ergasies']==0) {
      
      $sql="SELECT product_category_id
      FROM gks_eshop_products_categories_products
      WHERE product_id=".$product['id_product'];
      if ($product['product_parent_id']>0) $sql.=" or product_id=".$product['product_parent_id'];
      
      
      $result = $db_link->query($sql);        
      if (!$result) gks_production_order_ergasies_error('sql error',$sql);
      $product_category_id=array();
      while ($row = $result->fetch_assoc()) {
        $product_category_id[] = $row['product_category_id'];
      }

      $gids=array();
      if (count($product_category_id)>0) {
        $sql="SELECT 
        ug1.id_product_category as gid1, 
        ug2.id_product_category as gid2, 
        ug3.id_product_category as gid3,
        ug4.id_product_category as gid4,
        ug5.id_product_category as gid5,
        ug6.id_product_category as gid6,
        ug7.id_product_category as gid7,
        ug8.id_product_category as gid8,
        ug9.id_product_category as gid9,
        ug10.id_product_category as gid10
        FROM ((((((((gks_eshop_products_categories AS ug1 
        LEFT JOIN gks_eshop_products_categories AS ug2 ON ug1.product_category_parent_id = ug2.id_product_category) 
        LEFT JOIN gks_eshop_products_categories AS ug3 ON ug2.product_category_parent_id = ug3.id_product_category)
        LEFT JOIN gks_eshop_products_categories AS ug4 ON ug3.product_category_parent_id = ug4.id_product_category)
        LEFT JOIN gks_eshop_products_categories AS ug5 ON ug4.product_category_parent_id = ug5.id_product_category)
        LEFT JOIN gks_eshop_products_categories AS ug6 ON ug5.product_category_parent_id = ug6.id_product_category)
        LEFT JOIN gks_eshop_products_categories AS ug7 ON ug6.product_category_parent_id = ug7.id_product_category)
        LEFT JOIN gks_eshop_products_categories AS ug8 ON ug7.product_category_parent_id = ug8.id_product_category)
        LEFT JOIN gks_eshop_products_categories AS ug9 ON ug8.product_category_parent_id = ug9.id_product_category)
        LEFT JOIN gks_eshop_products_categories AS ug10 ON ug9.product_category_parent_id = ug10.id_product_category
        WHERE ug1.id_product_category in (".implode(',',$product_category_id).")";
        
        $result = $db_link->query($sql);        
        if (!$result) gks_production_order_ergasies_error('sql error',$sql);
        while ($row = $result->fetch_assoc()) {
          if (!empty($row['gid1']))  if (in_array(intval($row['gid1']), $gids)==false) $gids[] = intval($row['gid1']);
          if (!empty($row['gid2']))  if (in_array(intval($row['gid2']), $gids)==false) $gids[] = intval($row['gid2']);
          if (!empty($row['gid3']))  if (in_array(intval($row['gid3']), $gids)==false) $gids[] = intval($row['gid3']);
          if (!empty($row['gid4']))  if (in_array(intval($row['gid4']), $gids)==false) $gids[] = intval($row['gid4']);
          if (!empty($row['gid5']))  if (in_array(intval($row['gid5']), $gids)==false) $gids[] = intval($row['gid5']);
          if (!empty($row['gid6']))  if (in_array(intval($row['gid6']), $gids)==false) $gids[] = intval($row['gid6']);
          if (!empty($row['gid7']))  if (in_array(intval($row['gid7']), $gids)==false) $gids[] = intval($row['gid7']);
          if (!empty($row['gid8']))  if (in_array(intval($row['gid8']), $gids)==false) $gids[] = intval($row['gid8']);
          if (!empty($row['gid9']))  if (in_array(intval($row['gid9']), $gids)==false) $gids[] = intval($row['gid9']);
          if (!empty($row['gid10'])) if (in_array(intval($row['gid10']),$gids)==false) $gids[] = intval($row['gid10']);
        }
      }

      if (count($gids)>0) {     
        $sql="SELECT gks_production_ergasies.id_production_ergasia, gks_production_ergasies.production_ergasia_descr
        FROM gks_production_ergasies_eidoscat 
        LEFT JOIN gks_production_ergasies ON gks_production_ergasies_eidoscat.production_ergasia_id = gks_production_ergasies.id_production_ergasia
        WHERE gks_production_ergasies.id_production_ergasia Is Not Null 
        AND gks_production_ergasies_eidoscat.cateidos_id In (".implode(",",$gids).")
        ORDER BY gks_production_ergasies.production_ergasia_sortorder, gks_production_ergasies.id_production_ergasia";

        $result = $db_link->query($sql);        
        if (!$result) gks_production_order_ergasies_error('sql error',$sql);
        while ($row = $result->fetch_assoc()) {
          $row['id_production_line']=0;
          $row['pl_state']='';
          $product['ergasies'][$row['id_production_ergasia']] = $row;
        }
      }
    }
    
    
    
    $sql="SELECT gks_production_ergasies.id_production_ergasia, gks_production_ergasies.production_ergasia_descr
    FROM gks_production_ergasies_eidos 
    LEFT JOIN gks_production_ergasies ON gks_production_ergasies_eidos.production_ergasia_id = gks_production_ergasies.id_production_ergasia
    WHERE gks_production_ergasies.id_production_ergasia Is Not Null
    and (gks_production_ergasies_eidos.eidos_id=".$product['id_product'];
    if ($product['product_parent_id']>0) $sql.=" or gks_production_ergasies_eidos.eidos_id=".$product['product_parent_id'];
    $sql.=")";
    
    
    $result = $db_link->query($sql);        
    if (!$result) gks_production_order_ergasies_error('sql error',$sql);
    while ($row = $result->fetch_assoc()) {
      if (isset($product['ergasies'][$row['id_production_ergasia']])==false) {
        $row['id_production_line']=0;
        $row['pl_state']='';
        $product['ergasies'][$row['id_production_ergasia']] = $row;
      }
    }
  } 
  unset($product);
  
  //print '<pre>';print_r($orders_products_array);die();


  $production_line_pid=array();
  
  foreach ($orders_products_array as &$product) {
    foreach ($product['ergasies'] as &$ergasia) {
      $sql="select * from gks_production_line 
      where order_id=".$id."
      and set_id='".$db_link->escape_string($product['product_set'])."'
      and ergasia_id=".$ergasia['id_production_ergasia'];
      
      //and order_product_id=".$product['id_order_product']."
      //and product_id=".$product['id_product']."
      //order_product_id,product_id,
      //  ".$product['id_order_product'].",
      //  ".$product['id_product'].",

      $result = $db_link->query($sql);        
      if (!$result) gks_production_order_ergasies_error('sql error',$sql);
      if ($result->num_rows==0) {
        $sql="insert into gks_production_line (
        order_id,set_id,ergasia_id,pl_state,
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip
        ) values (
        ".$id.",
        '".$db_link->escape_string($product['product_set'])."',
        ".$ergasia['id_production_ergasia'].",
        '010draft',
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."'
        )";
        $result_insert = $db_link->query($sql);        
        if (!$result_insert) gks_production_order_ergasies_error('sql error',$sql);
        $ergasia['id_production_line'] = $db_link->insert_id;
        $ergasia['pl_state'] ='010draft';
      } else {
        $row = $result->fetch_assoc();
        $ergasia['id_production_line'] = $row['id_production_line'];
        $ergasia['pl_state'] = $row['pl_state'];
      }
      
      if (isset($production_line_pid[$ergasia['id_production_line']]) == false) {
        $production_line_pid[$ergasia['id_production_line']] = array();
      }
      $production_line_pid[$ergasia['id_production_line']][] = $product['id_order_product'];
    }
    unset($ergasia);
  }
  unset($product);
  
  //print '<pre>';print_r($orders_products_array);die();
  
  $not_delete=array();
  foreach ($orders_products_array as $product) {
    foreach ($product['ergasies'] as $ergasia) {
      $not_delete[]= $ergasia['id_production_line'];
    }
  }
  $sql="delete from gks_production_line
  where order_id=".$id."
  and pl_state in ('010draft','030pending')";
  if (count($not_delete)>=1) {
    $sql.=" and id_production_line not in (".implode(',',$not_delete).")";
  }
  $result_delete = $db_link->query($sql);        
  if (!$result_delete) gks_production_order_ergasies_error('sql error',$sql);
  
  $not_delete_pid=array();
  foreach ($production_line_pid as $key => $order_pid) {
    foreach ($order_pid as $order_pid2) {

      $sql="select id_production_line_pid from gks_production_line_pid where order_id=".$id." and production_line_id=".$key." and order_product_id=".$order_pid2;
      $result = $db_link->query($sql);        
      if (!$result) gks_production_order_ergasies_error('sql error',$sql);
      if ($result->num_rows==0) {
        $sql="insert into gks_production_line_pid (
        order_id,production_line_id,order_product_id,
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip
        ) values (
        ".$id.",
        ".$key.",
        ".$order_pid2.",
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."'
        )";
        $result_insert = $db_link->query($sql);        
        if (!$result_insert) gks_production_order_ergasies_error('sql error',$sql);
        $not_delete_pid[] = $db_link->insert_id;
  
        
      } else {
        $row = $result->fetch_assoc();
        $not_delete_pid[] = $row['id_production_line_pid'];
      }
    }
  } 

  $sql="delete from gks_production_line_pid
  where order_id=".$id;
  if (count($not_delete_pid)>=1) {
    $sql.=" and id_production_line_pid not in (".implode(',',$not_delete_pid).")";
  }
  $result_delete = $db_link->query($sql);        
  if (!$result_delete) gks_production_order_ergasies_error('sql error',$sql);
    
//  print '<pre>';
//  print_r($orders_array);
//  print_r($orders_products_array);
//  print_r($production_line_pid);
//  print '</pre>';
//  die();  
  
  
}


function gks_production_order_calc_ergasies_tree($id) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  
  global $mustodone;
  global $ergasies;
  
  $id=intval($id);
  if ($id<=0) gks_production_order_ergasies_error(gks_lang('Δεν έχει ορισθεί το').' ID',$id);
  
  $sql="select id_order,order_state,ddate from gks_orders where id_order=".$id;
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  if ($result->num_rows!=1) gks_production_order_ergasies_error(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Ανανεώστε την σελίδα'),$sql);
  $orders_array = $result->fetch_assoc();
  //if ($orders_array['order_state']!='070inproduction') return;
  
  
  $sql="SELECT gks_production_line.id_production_line, gks_production_line.pl_state,
  gks_production_line.set_id,
  gks_production_line.ergasia_id, gks_production_ergasies.production_ergasia_descr
  FROM gks_production_line 
  LEFT JOIN gks_production_ergasies ON gks_production_line.ergasia_id = gks_production_ergasies.id_production_ergasia
  WHERE gks_production_line.order_id=".$id."
  ORDER BY gks_production_line.ergasia_id, gks_production_ergasies.production_ergasia_descr, gks_production_line.id_production_line";
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  $sets_array=array();
  while ($row = $result->fetch_assoc()) {
    $key = trim_gks($row['set_id']);
    if ($key=='') $key=gks_lang('κενό');
    if (isset($sets_array[$key]) == false) {
      $sets_array[$key]=array();
    }
    $sets_array[$key]['ergasies'][$row['ergasia_id']]=array(
      'set_id' => $key,
      'id' => $row['ergasia_id'],
      'descr' => $row['production_ergasia_descr'],
      'id_production_line' => $row['id_production_line'],
      'pl_state' => $row['pl_state'],
    );

  }
  
  
//  print '<pre>';
//  print_r($sets_array);
//  print '</pre>';
//  die(); 
  
  
  
  $sql="SELECT gks_production_ergasies_mustdone.ergasia_id, gks_production_ergasies_mustdone.ergasia_mustdone_id
  FROM (gks_production_ergasies_mustdone 
  LEFT JOIN gks_production_ergasies AS gks_production_ergasies_n ON gks_production_ergasies_mustdone.ergasia_id = gks_production_ergasies_n.id_production_ergasia) 
  LEFT JOIN gks_production_ergasies AS gks_production_ergasies_m ON gks_production_ergasies_mustdone.ergasia_mustdone_id = gks_production_ergasies_m.id_production_ergasia
  WHERE (((gks_production_ergasies_n.id_production_ergasia) Is Not Null) AND ((gks_production_ergasies_m.id_production_ergasia) Is Not Null))
  ORDER BY gks_production_ergasies_n.production_ergasia_descr, gks_production_ergasies_m.production_ergasia_descr;";
  $result = $db_link->query($sql); 
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  $mustodone_all=array();
  while ($row = $result->fetch_assoc()) {
    $mustodone_all[]=array('n'=> $row['ergasia_id'], 'm' => $row['ergasia_mustdone_id']);
  }

//  print '<pre>';
//  print_r($mustodone_all);
//  print '</pre>';
//  die(); 
    
  foreach ($sets_array as &$myset) {
    $ergasies_in=array();
    foreach ($myset['ergasies'] as $ergasia) {
      $ergasies_in[]=$ergasia['id'];
    }
    $myset['ergasies_in']=$ergasies_in;
    
    $mustodone=array();
    foreach ($mustodone_all as $item) {
      if (in_array($item['n'], $ergasies_in) and in_array($item['m'], $ergasies_in)) {
        $mustodone[]=$item;
      }
    } 
    $myset['mustodone']=$mustodone;
    
    
    $ergasies=$myset['ergasies'];
    $mustodone=$myset['mustodone'];
    
    $data=array();
    foreach ($ergasies as $erg) {
      $is_root=true;
      foreach ($mustodone as $must) {
        if ($must['n'] == $erg['id']) {
          $is_root=false; break;
        }
      }
      if ($is_root) {
        $data[$erg['id']]=$erg;
      }
    }
    
    
    foreach ($data as &$item) {
      gks_production_order_calc_ergasies_tree_map($item);
    }
    unset($item);
    $myset['data']=$data;
  }
  unset($myset);
  
  

  

  foreach ($sets_array as &$myset) {
    $myset['html']='graph LR'."\r\n";
    
//    foreach ($myset['ergasies'] as &$erg) {
//      $erg['class']='';
//      $is_start=true;
//      $is_end=true;
//      foreach ($myset['mustodone'] as $must) {
//        if ($must['n'] == $erg['id']) $is_start=false; 
//        if ($must['m'] == $erg['id']) $is_end=false; 
//      }
//      if ($is_start) {
//        $erg['class']='starterg';
//      } else if ($is_end) {
//        $erg['class']='enderg';
//      }
//    }
//    unset($erg);

    foreach ($myset['ergasies'] as $erg) {
      $myset['html'].= 'erg'.$erg['id'].'('.$erg['descr'].')'."\r\n";
      //if ($erg['class']!='') {
      //  $myset['html'].= 'class erg'.$erg['id'].' '.$erg['class'].''."\r\n";
      //} else {
        $set_id=trim_gks($erg['set_id']);
        if ($set_id=='') $set_id=gks_lang('κενό');
      
        $myset['html'].= 'class erg'.$erg['id'].' svg_'.$erg['id'].'_'. $set_id.'_'.$erg['id_production_line'].'_'.$erg['pl_state']."\r\n";
      //}
    } 
    foreach ($myset['mustodone'] as $myconn) {
      $myset['html'].= 'erg'.$myconn['m'].'-->erg'.$myconn['n']."\r\n";
    }        
    
  }
  unset($myset);  

//  print '<pre>';
//  print_r($sets_array);
//  print '</pre>';  
//  die();

  
    
//  foreach ($sets_array as &$myset) {
//    $myset['html']='';
//    $aa=0;
//    foreach ($myset['data'] as &$item) {
//      $aa++;
//      $myset['html'].= gks_production_order_calc_ergasies_tree_html($item,trim_gks($aa));
//    }
//    unset($item);
//  }
//  unset($myset);
  
//  print '<pre>';
//  print_r($sets_array);
//  print '</pre>';  
//  die();  
  
  $not_delete_setid=array();
  foreach ($sets_array as $set_id => $myset) {
    $sql="select id_orders_products_sets 
    from gks_orders_products_sets 
    where order_id=".$id." and set_id='".$db_link->escape_string($set_id)."'";
    $result = $db_link->query($sql);        
    if (!$result) gks_production_order_ergasies_error('sql error',$sql);
    if ($result->num_rows==0) {
      $sql="insert into gks_orders_products_sets (
      order_id,set_id,ergasies_tree
      ) values (
      ".$id.",'".$db_link->escape_string($set_id)."','".$db_link->escape_string($myset['html'])."'
      )";
      $result = $db_link->query($sql);        
      if (!$result) gks_production_order_ergasies_error('sql error',$sql);
      $not_delete_setid[] = $db_link->insert_id;
      
    } else {
      $row = $result->fetch_assoc();
      $not_delete_setid[]=$row['id_orders_products_sets'];
      $sql="update gks_orders_products_sets set ergasies_tree='".$db_link->escape_string($myset['html'])."' where id_orders_products_sets=".$row['id_orders_products_sets'];
      $result = $db_link->query($sql);        
      if (!$result) gks_production_order_ergasies_error('sql error',$sql);
    }    

  }
  
  $sql="delete from gks_orders_products_sets
  where order_id=".$id;
  if (count($not_delete_setid)>=1) {
    $sql.=" and id_orders_products_sets not in (".implode(',',$not_delete_setid).")";
  }
  $result_delete = $db_link->query($sql);        
  if (!$result_delete) gks_production_order_ergasies_error('sql error',$sql);
  
  
//  print '<pre>';
//  print_r($sets_array);
//  print '</pre>';  
//  die();
  
}


function gks_production_order_calc_ergasies_tree_map(&$item) {
  global $mustodone;
  global $ergasies;
  
  foreach ($mustodone as $must) {
    if ($must['m'] == $item['id']) {
      if (isset($item['childs']) == false) $item['childs']=array();
      $item_add=array(
        'id' => $must['n'], 
        'descr'=> $ergasies[$must['n']]['descr'],
        'id_production_line'=> $ergasies[$must['n']]['id_production_line'],
        'pl_state'=> $ergasies[$must['n']]['pl_state'],
        'set_id'=> $ergasies[$must['n']]['set_id'],
        
      );
      gks_production_order_calc_ergasies_tree_map($item_add);
      $item['childs'][]=$item_add;
    }
  } 
}    

function gks_production_order_calc_ergasies_tree_html(&$item, $aa) {
  $child_html='';
  if (isset($item['childs'])) {
    $bb=0;
    foreach ($item['childs'] as &$child) {
      $bb++;
      $child_html.=gks_production_order_calc_ergasies_tree_html($child,$aa.'.'.trim_gks($bb));
    }   
    unset($child);
  }
  $set_id=trim_gks($item['set_id']);
  if ($set_id=='') $set_id=gks_lang('κενό');
  
  $myhtml='<div>
<div data-id="'.$item['id'].'" ><b>'.$aa.'.</b> <span class="line_state production_line_state_'.$item['pl_state'].'" '.
'data-id="'.$item['id'].'" '.
'data-setid="'.$set_id.'" '.
'title="'.getProductionLineStateDescr($item['pl_state']).'" '.
'data-recid="'.$item['id_production_line'].'" '.
'data-oldstate="'.$item['pl_state'].'" '.
'>'.$item['descr'].'</span></div>
<div class="div_ergas_child">'.$child_html.'</div>
</div>
';
  
//  print '<pre>';
//  print_r($item);
//  die();
  return $myhtml;
}



function gks_production_order_calc_ergasies_setready($id) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  
  //print '<pre>';
  
  $sql="SELECT id_production_line, pl_state, set_id, ergasia_id
  FROM gks_production_line
  WHERE order_id=".$id;
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  $cc=array(
    'all' => 0,
    '010draft' => 0,
    '020cancelled' => 0,
    '030pending' => 0,
    '040ready' => 0,
    '050processing' => 0,
    '060pause' => 0,
    '070failed' => 0,
    '100completed' => 0,
  );
  
  $cc_set_id=array();
  while ($row = $result->fetch_assoc()) {
    $cc['all']++;
    $key = trim_gks($row['set_id']);
    if ($key=='') $key=gks_lang('κενό');    
    
    if (isset($cc_set_id[$key]) == false) {
      $cc_set_id[$key]=array(
        'all' => 0,
        '010draft' => 0,
        '020cancelled' => 0,
        '030pending' => 0,
        '040ready' => 0,
        '050processing' => 0,
        '060pause' => 0,
        '070failed' => 0,
        '100completed' => 0,
      );
    }
    $cc_set_id[$key]['all']++;
    
    if ($row['pl_state'] == '010draft') {
      //print_r($row);
      
      $sql="select ergasia_mustdone_id from gks_production_ergasies_mustdone where ergasia_id=".$row['ergasia_id'];
      $result_mustdone = $db_link->query($sql);        
      if (!$result_mustdone) gks_production_order_ergasies_error('sql error',$sql);
      
      $parent_isok=true;
      if ($result_mustdone->num_rows==0) {
        $parent_isok=true;
      } else {
        while ($row_mustdone = $result_mustdone->fetch_assoc()) {
          //print_r($row_mustdone);
          $sql="select pl_state from gks_production_line where order_id=".$id." and set_id='".$db_link->escape_string($row['set_id'])."' and ergasia_id=".$row_mustdone['ergasia_mustdone_id'];
          $result_pergas = $db_link->query($sql);        
          if (!$result_pergas) gks_production_order_ergasies_error('sql error',$sql);
          if ($result_pergas->num_rows > 0) {
            $row_pergas = $result_pergas->fetch_assoc();
            if ($row_pergas['pl_state']!='100completed' and $row_pergas['pl_state']!='020cancelled') {
              $parent_isok=false;
              break; 
            }
          }
          
        }
      
      }
      if ($parent_isok) {
        $sql="update gks_production_line set pl_state='040ready',
        mydate_edit=now(),user_id_edit=".$my_wp_user_id.", myip='".$db_link->escape_string($gkIP)."'
        where id_production_line=".$row['id_production_line'];
        $result_update = $db_link->query($sql); 
        if (!$result_update) gks_production_order_ergasies_error('sql error',$sql);   
        $row['pl_state'] == '040ready';
      }
    
    }
    $cc_set_id[$key][$row['pl_state']]++;
    $cc[$row['pl_state']]++;
  }
  
  
  $done=$cc['020cancelled'] + $cc['100completed'];
  $pososto=0;
  if ($cc['all']!=0) $pososto= (100*$done) / $cc['all'];
  
  $sql="update gks_orders set production_pososto=".number_format($pososto, 2, '.', '').", production_ergasies_done=".$done.", production_ergasies_total=".$cc['all']." where id_order=".$id;
  $result_update = $db_link->query($sql); 
  if (!$result_update) gks_production_order_ergasies_error('sql error',$sql); 
//  print '<pre>';
//  print_r($cc_set_id);
//  print '</pre>';
  foreach ($cc_set_id as $set_key => $item) {
    $done_item=0;
    if (isset($item['020cancelled'])) $done_item+=$item['020cancelled'];
    if (isset($item['100completed'])) $done_item+=$item['100completed'];
    
    $pososto=0;
    if ($item['all']!=0) $pososto= (100*$done_item) / $item['all'];

    $sql="update gks_orders_products_sets set production_set_pososto=".number_format($pososto, 2, '.', '')." where order_id=".$id." and set_id='".$db_link->escape_string($set_key)."'";
    //echo $sql;
    $result_update = $db_link->query($sql); 
    if (!$result_update) gks_production_order_ergasies_error('sql error',$sql); 

    
  } 
  
  
  //echo 'gggggggggggg'.$cc['all'].'|'.$done;
  
  if ($cc['all'] == $done) {
    $sql="SELECT gks_orders.order_state, gks_orders.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
    FROM gks_orders LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_orders.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE gks_orders.id_order=".$id;
    $result = $db_link->query($sql); 
    if (!$result) gks_production_order_ergasies_error('sql error',$sql);
    if ($result->num_rows!=1) gks_production_order_ergasies_error(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Ανανεώστε την σελίδα'),$sql);
    $row = $result->fetch_assoc();
    if ($row['order_state']=='070inproduction') {
      $user_id=$row['user_id'];
      $gks_nickname=$row['gks_nickname'];
      
      $sql="update gks_orders set order_state='090indelivery' where id_order=".$id;
      $result = $db_link->query($sql); 
      if (!$result) gks_production_order_ergasies_error('sql error',$sql);
      
      $message=gks_lang('Η παραγγελία με αριθμό <a href="/my/admin-orders-item.php?id=[1]" target="_blank">#[1]</a> του πελάτη <a href="/my/admin-users-item.php?id=[2]" target="_blank">[3]</a> έχει ολοκληρωθεί');
      $message=str_replace('[1]',$id,$message);
      $message=str_replace('[2]',$user_id,$message);      
      $message=str_replace('[3]',$gks_nickname,$message);      
      $sql="insert into gks_notification (
      message,for_user_id,`date_add`,for_date,has_ok,model,model_id
      )
      select
      '".$db_link->escape_string($message)."' as message,
      user_id as for_user_id,
      now() as `date_add`,
      now() as `for_date`,
      0 as has_ok,
      'orders' as model,
      ".$id." as model_id
      from gks_notification_userperm where notification_type_id=510 and from_admin=1 and from_user=1".gks_notification_userperm_internal_users();
      //from ".GKS_WP_TABLE_PREFIX."users where gks_wp_capabilities like '%ordermanager%' or gks_wp_capabilities like '%adminmy%';";
      $result = $db_link->query($sql); 
      
      
      $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.viber_id
      FROM gks_notification_userperm 
      LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification_userperm.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
      WHERE ".GKS_WP_TABLE_PREFIX."users.viber_id<>''
      AND ".GKS_WP_TABLE_PREFIX."users.viber_subscribed<>0
      AND gks_notification_userperm.notification_type_id=510 AND gks_notification_userperm.from_admin=1 AND gks_notification_userperm.to_viber=1".gks_notification_userperm_internal_users();
      //debug_mail(false,'sql',$sql);
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('status'  => 'error','info' => '<br>'.gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
        echo json_encode($return); die(); }  
      $send_viber=array();
      while ($row = $result->fetch_assoc()) {
        $send_viber[]=$row['viber_id'];
      }
      foreach ($send_viber as $value) {
        $message=gks_lang('Η παραγγελία με αριθμό [1] του πελάτη [2] έχει ολοκληρωθεί');
        $message=str_replace('[1]',$id,$message);
        $message=str_replace('[3]',$gks_nickname,$message);
        $message.="\n".GKS_SITE_URL.'my/admin-orders-item.php?id='.$id;
        gks_viber_send('order', $id ,$value,$message);
      } 
      
      $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.user_email
      FROM gks_notification_userperm 
      LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_notification_userperm.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
      WHERE ".GKS_WP_TABLE_PREFIX."users.user_email<>''
      AND gks_notification_userperm.notification_type_id=510 AND gks_notification_userperm.from_admin=1 AND gks_notification_userperm.to_email=1".gks_notification_userperm_internal_users();
      //debug_mail(false,'sql',$sql);
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('status'  => 'error','info' => '<br>'.gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Ανανεώστε την σελίδα και δοκιμάστε ξανά'));
        echo json_encode($return); die(); }  
      $send_viber=array();
      while ($row = $result->fetch_assoc()) {
        $mysubject=gks_lang('Η παραγγελία με αριθμό [1] του πελάτη [2] έχει ολοκληρωθεί');
        $mysubject=str_replace('[1]',$id,$mysubject);
        $mysubject=str_replace('[2]',$gks_nickname,$mysubject);        
        $message=gks_lang('Η παραγγελία με αριθμό <a href="[4]my/admin-orders-item.php?id=[1]" target="_blank">#[1]</a> του πελάτη <a href="[4]my/admin-users-item.php?id=[2]" target="_blank">[3]</a> έχει ολοκληρωθεί');
        $message=str_replace('[1]',$id,$message);
        $message=str_replace('[2]',$user_id,$message);
        $message=str_replace('[3]',$gks_nickname,$message);
        $message=str_replace('[4]',GKS_SITE_URL,$message);
        
        $replaces=array();
        $replaces[] = array('[[message]]', $message);
        $params=array(
          'model'=>'order',
          'model_id'=>$id,
          'to'=>$row['user_email'],
          'subject'=>$mysubject,
          'template'=>3, //'empty.html',
          'replaces'=>$replaces,
        );
        $send_email_res = gks_mymail_template($params);
      }

      gks_plugins_functions_run('functions_production_gks_production_order_calc_ergasies_setready',array(
        'id'=>&$id,
      ));
      
      
    }
      
    
  }

//  print '<pre>';
//  print_r($cc);
//  print '</pre>';  
//  die();  
}


function gks_production_order_calc_ergasies_time($id) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  
  //print '<pre>';
  $sql="update gks_orders set production_sum_time=0 WHERE id_order=".$id;
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  
  $sql="update gks_orders_products set product_sum_time=0 WHERE order_id=".$id;
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  
  $sql="SELECT set_id FROM gks_production_line WHERE order_id=".$id." group by set_id";
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  
  $order_sets=array();

  while ($row = $result->fetch_assoc()) {
    $order_sets[$row['set_id']] = array(
      'set_id' => $row['set_id'],
      'sum' => 0,
      'gks_production_line' => array(),
    );
  }
  if (count($order_sets)<=0) return;
  

  
  $sql="select id_production_line,set_id from gks_production_line where order_id=".$id;
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  $id_production_line=array();
  while ($row = $result->fetch_assoc()) {
    if (isset($order_sets[$row['set_id']])) {
      $order_sets[$row['set_id']]['gks_production_line'][$row['id_production_line']] = array(
        'id_production_line' => $row['id_production_line'],
        'sum'=>0,
        'gks_production_line_time' => array(),        
      );
      $id_production_line[]=$row['id_production_line'];
    }
  }
  if (count($id_production_line)<=0) return;
  
//  print '<pre>';
//  print_r($order_sets);
//  die();
    
  $sql="update gks_production_line set prod_sum_time=0 where id_production_line in (".implode(',',$id_production_line).")";
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  
  
  $sql="SELECT gks_production_line.set_id,gks_production_line_time.production_line_id, Sum(gks_production_line_time.duration_secs) AS sumd
  FROM (gks_production_line_time 
  LEFT JOIN gks_production_line ON gks_production_line_time.production_line_id = gks_production_line.id_production_line)
  LEFT JOIN gks_production_posta ON gks_production_line_time.posto_id = gks_production_posta.id_production_posto
  WHERE gks_production_line_time.production_line_id In (".implode(',',$id_production_line).")
  and gks_production_line_time.time_end is not null
  and gks_production_posta.bypass_time=0
  GROUP BY gks_production_line.set_id, gks_production_line_time.production_line_id;";
//  echo '<pre>';
//  echo $sql;
//  die();
 
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  $production_sum_time=0;
  while ($row = $result->fetch_assoc()) {
    
    if (isset($order_sets[$row['set_id']])) {
      $production_sum_time+=$row['sumd'];
      $order_sets[$row['set_id']]['sum']+=$row['sumd'];
      if (isset($order_sets[$row['set_id']]['gks_production_line'][$row['production_line_id']])) {
        $order_sets[$row['set_id']]['gks_production_line'][$row['production_line_id']]['sum']+=$row['sumd'];
      }
    }
  }
//  print '<pre>';
//  print_r($order_sets);
//  die();
    
  $sql="SELECT gks_production_line.set_id, gks_production_line_time.production_line_id, Sum(TIME_TO_SEC(TIMEDIFF(now(),time_start))) AS sumd
  FROM (gks_production_line_time 
  LEFT JOIN gks_production_line ON gks_production_line_time.production_line_id = gks_production_line.id_production_line)
  LEFT JOIN gks_production_posta ON gks_production_line_time.posto_id = gks_production_posta.id_production_posto
  WHERE gks_production_line_time.production_line_id In (".implode(',',$id_production_line).")
  and gks_production_line_time.time_end is null
  and gks_production_posta.bypass_time=0
  GROUP BY gks_production_line.set_id, gks_production_line_time.production_line_id;";
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  //$production_sum_time=0;
  while ($row = $result->fetch_assoc()) {
    if (isset($order_sets[$row['set_id']])) {
      $production_sum_time+=$row['sumd'];
      $order_sets[$row['set_id']]['sum']+=$row['sumd'];
      if (isset($order_sets[$row['set_id']]['gks_production_line'][$row['production_line_id']])) {
        $order_sets[$row['set_id']]['gks_production_line'][$row['production_line_id']]['sum']+=$row['sumd'];
      }
    }
  }  

//  print '<pre>';
//  print_r($order_sets);
//  die();
  
  $sql="update gks_orders set production_sum_time=".$production_sum_time." WHERE id_order=".$id;
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);

  $sql="update gks_orders_products_sets set set_sum_time=0 WHERE order_id=".$id;
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);

  foreach ($order_sets as $key_set => $myset) {
    $sql="update gks_orders_products_sets set set_sum_time=".$myset['sum']." where order_id=".$id." and set_id='".$db_link->escape_string($key_set)."'";
    $result = $db_link->query($sql);        
    if (!$result) gks_production_order_ergasies_error('sql error',$sql);
    
    foreach ($myset['gks_production_line'] as $line) {
      $sql="update gks_production_line set prod_sum_time=".$line['sum']." where id_production_line=".$line['id_production_line'];
      $result = $db_link->query($sql);        
      if (!$result) gks_production_order_ergasies_error('sql error',$sql);
    } 
    
  } 

//  print'<pre>';
//  print_r($id_order_product);
//  print_r($id_production_line);
//  print_r($order_sets);
//  
//  print'</pre>';
//  die();
}

function calc_gks_production_bom_per_product($id_product,$bom_quantity,$monada_convert_base,$eidi_array=array(),$cost_array=array()) {
  global $db_link;
  $ret=array();
  $ret['base']['id']=$id_product;
  $ret['base']['product_class']='';
  $ret['base']['product_descr']=0;
  $ret['base']['ylika']=0;
  $ret['base']['ylika_str']='';
  $ret['base']['other_cost']=0;
  $ret['base']['other_cost_str']='';
  $ret['base']['total']=0;
  $ret['base']['total_str']='';
  $ret['monada_convert_base']=$monada_convert_base;
  $ret['product_variants']=array();
  $ret['report']='';
  
  $sql="select gks_eshop_products.id_product, gks_eshop_products.product_descr, gks_eshop_products.product_class, gks_eshop_products.product_parent_id,
  gks_eshop_products_parent.product_descr as product_descr_parent, gks_eshop_products.product_descr_variable
  FROM gks_eshop_products 
  LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product
  where gks_eshop_products.id_product=".$id_product;
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); } 
  if ($result->num_rows<=0) return $ret;
  $row=$result->fetch_assoc();
  
  $ret['base']['product_class']=trim_gks($row['product_class']);  
  $ret['base']['product_descr']=trim_gks($row['product_descr']); 
  if ($row['product_parent_id']!=0) {
    $ret['base']['product_descr']=trim_gks(trim_gks($row['product_descr_parent']).' '.trim_gks($row['product_descr_variable'])); 
  }
  
  $ret['product_variants']=array();
  if ($ret['base']['product_class']=='variable') {
    $sql_variants="SELECT id_product, product_descr_variable
    FROM gks_eshop_products
    WHERE product_parent_id=".$id_product." AND product_disable=0
    order by product_descr_variable";
    $result_variants = $db_link->query($sql_variants);        
    if (!$result_variants) {debug_mail(false,'error sql',$sql_variants); die('sql error');}
    while ($row_variants= $result_variants->fetch_assoc()) {
      $ret['product_variants'][$row_variants['id_product']]=array(
        'id'=> intval($row_variants['id_product']),
        'descr'=> trim_gks($row_variants['product_descr_variable']),
        'ylika'=>0,
        'other_cost'=>0,
        'total'=>0,
      );
    }
  }
  
  
  
  foreach ($eidi_array as $value) {
    if ($ret['base']['product_class']!='variable' or ($ret['base']['product_class']=='variable' and $value['pbom_variant_product_id']==0)) {
      $ret['base']['ylika']+=
      $value['pbom_quantity']*
      ($value['pbom_kostos_type']==0 ? 
      $value['product_kostos_org'] : 
      $value['pbom_kostos_value']);
    }
  } 
  foreach ($cost_array as $value) {
    if ($ret['base']['product_class']!='variable' or ($ret['base']['product_class']=='variable' and $value['cbom_variant_product_id']==0)) {
      $ret['base']['other_cost']+=$value['cbom_kostos_value'];
    }
  }
  $ret['base']['total']=$ret['base']['ylika']+$ret['base']['other_cost'];

  $periptosi=1;
  $col1='col-3';
  $col2='col-3';
  $col3='col-3';
  $col4='col-3';
  
  if ($bom_quantity==1) {
    if ($monada_convert_base['from']==$monada_convert_base['to'] or $bom_quantity==0) {
      $periptosi=1;
      $col1='col-8';
      $col2='col-4';
    } else {
      $periptosi=2;
      $col1='col-6';
      $col2='col-3';
      $col3='col-3';
      
    }
    
  } else {
    if ($monada_convert_base['from']==$monada_convert_base['to']) {
      $periptosi=3;
      $col1='col-6';
      $col2='col-3';
      $col3='col-3';
      
    } else {
      $periptosi=4;
      $col1='col-4';
      $col2='col-2';
      $col3='col-2';
      $col4='col-2';
      $col5='col-2';
    }
  }

  $ret['base']['ylika_str']=myCurrencyFormat($ret['base']['ylika']);
  $ret['base']['other_cost_str']=myCurrencyFormat($ret['base']['other_cost']);
  $ret['base']['total_str']=myCurrencyFormat($ret['base']['total']);
  $ret['base']['per_monades']=array();
  $ret['base']['per_monades'][$monada_convert_base['to']]=($bom_quantity==0 ? 0 : $monada_convert_base['epi_rev']*$ret['base']['total']/$bom_quantity);
  $ret['base']['per_monades'][$monada_convert_base['from']]=$ret['base']['total'];
  

  $ret['report'].= '<div class="gks_div_rows row">'.
                    '<div class="'.$col1.' text-left gks_eidos_label" style="font-weight: bold;padding-left: 10px;">'.
                      gks_lang('Είδος').
                    '</div>'.
                    '<div class="'.$col2.' text-right gks_eidos_label" style="font-weight: bold;">'.
                      $bom_quantity.' '.$monada_convert_base['from_descr'].
                    '</div>';
  if ($periptosi==2) {
    $ret['report'].='<div class="'.$col3.' text-right gks_eidos_label" style="font-weight: bold;">'.
                      '1 '.$monada_convert_base['to_descr'].
                    '</div>';
  }                    
  if ($periptosi==3 or $periptosi==4) {
    $ret['report'].='<div class="'.$col3.' text-right gks_eidos_label" style="font-weight: bold;">'.
                      '1 '.$monada_convert_base['from_descr'].
                    '</div>';
  }
  if ($periptosi==4) {
    $ret['report'].='<div class="'.$col4.' text-right gks_eidos_label" style="font-weight: bold;">'.
                      myNumberFormatNo0Local($monada_convert_base['epi']*$bom_quantity).' '.$monada_convert_base['to_descr'].
                    '</div>';
    $ret['report'].='<div class="'.$col5.' text-right gks_eidos_label" style="font-weight: bold;">'.
                      '1 '.$monada_convert_base['to_descr'].
                    '</div>';
  }
  $ret['report'].='</div>';


  
  $ret['report'].= '<div class="gks_div_rows row">'.
                    '<div class="'.$col1.' text-left gks_eidos_label" style="padding-left: 10px;">'.
                      $ret['base']['product_descr'].
                    '</div>'.
                    '<div class="'.$col2.' text-right gks_eidos_label">'.
                      myCurrencyFormat($ret['base']['total']).
                    '</div>';
  if ($periptosi==2) {
    $ret['report'].='<div class="'.$col3.' text-right gks_eidos_label">'.
                      ($bom_quantity==0 ? '' : 
                      myCurrencyFormat(($monada_convert_base['epi_rev'] * $ret['base']['total'])/$bom_quantity) ).
                    '</div>';
  }                    
  if ($periptosi==3 or $periptosi==4) {
    $ret['report'].='<div class="'.$col3.' text-right gks_eidos_label">'.
                      myCurrencyFormat($ret['base']['total']/$bom_quantity).
                    '</div>';
  }
  if ($periptosi==4) {
    $ret['report'].='<div class="'.$col4.' text-right gks_eidos_label">'.
                      myCurrencyFormat($ret['base']['total']).
                    '</div>';
    $ret['report'].='<div class="'.$col5.' text-right gks_eidos_label">'.
                      ($bom_quantity==0 ? '' : 
                      myCurrencyFormat($monada_convert_base['epi_rev']*$ret['base']['total']/$bom_quantity) ).
                    '</div>';
  }
  $ret['report'].='</div>';
          

  //print '<pre>';print_r($ret);die();
  
  if ($ret['base']['product_class']=='variable') {
    foreach ($ret['product_variants'] as &$variant) {
      foreach ($eidi_array as $value) {
        if ($value['pbom_variant_product_id']==0 or $value['pbom_variant_product_id']==$variant['id']) {
          $variant['ylika']+=
          $value['pbom_quantity']*
          ($value['pbom_kostos_type']==0 ? 
          $value['product_kostos_org'] : 
          $value['pbom_kostos_value']);
        }
      } 
      foreach ($cost_array as $value) {
        if ($value['cbom_variant_product_id']==0 or $value['cbom_variant_product_id']==$variant['id']) {
          $variant['other_cost']+=$value['cbom_kostos_value'];
        }
      }
      $variant['total']=$variant['ylika']+$variant['other_cost'];
      $variant['per_monades']=array();
      $variant['per_monades'][$monada_convert_base['to']]=($bom_quantity==0 ? 0 : $monada_convert_base['epi_rev']*$variant['total']/$bom_quantity);
      $variant['per_monades'][$monada_convert_base['from']]=$variant['total'];
      
      
    }
    unset($variant);
    
    foreach ($ret['product_variants'] as $variant) {
      $ret['report'].= '<div class="gks_div_rows row">'.
                        '<div class="'.$col1.' text-left gks_eidos_label" style="padding-left: 30px;">'.
                          $variant['descr'].
                        '</div>'.
                        '<div class="'.$col2.' text-right gks_eidos_label">'.
                          myCurrencyFormat($variant['total']).
                        '</div>';
      if ($periptosi==2) {
        $ret['report'].='<div class="'.$col3.' text-right gks_eidos_label">'.
                          myCurrencyFormat(($monada_convert_base['epi_rev'] * $variant['total'])/$bom_quantity).
                        '</div>';
      }                    
      if ($periptosi==3 or $periptosi==4) {
        $ret['report'].= 
                        '<div class="'.$col3.' text-right gks_eidos_label">'.
                          myCurrencyFormat($variant['total']/$bom_quantity).
                        '</div>';
      }                    
      if ($periptosi==4) {
        $ret['report'].='<div class="'.$col4.' text-right gks_eidos_label">'.
                          myCurrencyFormat($variant['total']).
                        '</div>';

        $ret['report'].='<div class="'.$col5.' text-right gks_eidos_label">'.
                          myCurrencyFormat($monada_convert_base['epi_rev']*$variant['total']/$bom_quantity).
                        '</div>';
      }
               
      $ret['report'].= '</div>';      
    }
    
    
  }
  
  
  
  
  
//  unset($ret['base']['ylika']);
//  unset($ret['base']['other_cost']);
//  unset($ret['base']['total']);
  
  //print '<pre>';print_r($eidi_array);print_r($cost_array);print_r($ret);die();
  return $ret;
}




function gks_production_order_sintagi($id) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  
  $id=intval($id);
  if ($id<=0) gks_production_order_ergasies_error(gks_lang('Δεν έχει ορισθεί το').' ID',$id);
  
  
  
  $sql="select id_order,order_state,ddate,company_id,company_sub_id
  from gks_orders where id_order=".$id;
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  if ($result->num_rows!=1) gks_production_order_ergasies_error(gks_lang('Δεν βρέθηκε η εγγραφή').'<br>'.gks_lang('Ανανεώστε την σελίδα'),$sql);
  $orders_array = $result->fetch_assoc();
  
  $order_state=$orders_array['order_state'];
  $company_id=$orders_array['company_id'];
  $company_sub_id=$orders_array['company_sub_id'];
  //echo '<pre>';print_r($orders_array);die();
  
  
  
  if ($order_state!='070inproduction') {
    gks_production_sintagi_update_order_state($id, $order_state);
    return;
  }
  //echo '<pre>';print_r($orders_array);die();
  
  //gks_production_order_ergasies_error('testttttt',$id);
  
  $sql="SELECT gks_orders_products.id_order_product, gks_orders_products.product_set, 
  gks_eshop_products.id_product, gks_eshop_products.product_parent_id, gks_eshop_products.product_class,
  gks_eshop_products.product_code, gks_eshop_products.product_descr, 
  gks_orders_products.product_sheets, gks_orders_products.product_quantity, gks_orders_products.product_monada_id,
  gks_eshop_products.product_monada_id as product_monada_id_org
  FROM gks_orders_products 
  LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product
  WHERE gks_eshop_products.id_product Is Not Null and gks_orders_products.order_id=".$id."
  AND gks_orders_products.product_is_optional in (0,2) 
  order by id_order_product";
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  $orders_products_array=array();
  while ($row = $result->fetch_assoc()) {
    $product_set=trim_gks($row['product_set']);
    if (strpos($product_set, ',') !== false) {
      $parts=explode(',', $product_set);
      $product_set=array();
      foreach ($parts as $value) {
        $value=trim_gks($value);
        if ($value!='') $product_set[] = $value;
      } 
    } else {
      $product_set=array($product_set);
    }
    foreach ($product_set as $value) {
      if ($value=='') $value=gks_lang('κενό');
      $row['product_set'] = $value;
      $row['sintages']=array();
      $orders_products_array[$row['id_order_product'].'|'.$value] = $row;
    }
  }
  
  //print '<pre>';print_r($orders_products_array);die();
  
  foreach ($orders_products_array as &$product) {
    $sql="select id_production_bom,bom_descr,bom_product_id,company_id,company_sub_id,bom_quantity,bom_monada_id
    from gks_production_bom 
    where bom_disable=0 
    and (bom_product_id=".$product['id_product'];
    if ($product['product_parent_id']>0) $sql.=" or bom_product_id=".$product['product_parent_id'];
    $sql.=")
    and (
     (company_id=0 and company_sub_id=0) or 
     (company_id=".$company_id." and company_sub_id=".$company_sub_id.")
    )";
    //echo '<pre>';echo $sql;die();
    
    $result = $db_link->query($sql); 
    if (!$result) gks_production_order_ergasies_error('sql error',$sql);
    while ($row = $result->fetch_assoc()) {
      $row['products']=array();
      $row['costs']=array();
      $product['sintages'][$row['id_production_bom']] = $row;
    }
  }
  unset($product);
  
  //print '<pre>';print_r($orders_products_array);die();
  
  foreach ($orders_products_array as &$product) {
    foreach ($product['sintages'] as &$sintagi) {
      
      $sql="SELECT gks_production_bom_product.id_production_bom_product, gks_production_bom_product.pbom_aa, 
      gks_production_bom_product.pbom_product_id, gks_production_bom_product.pbom_quantity, gks_production_bom_product.pbom_monada_id, 
      gks_production_bom_product.pbom_note, gks_production_bom_product.pbom_kostos_type, gks_production_bom_product.pbom_kostos_value, 
      gks_eshop_products.product_kostos,
      gks_eshop_products.product_monada_id as product_monada_id_org
      FROM gks_production_bom_product 
      LEFT JOIN gks_eshop_products ON gks_production_bom_product.pbom_product_id = gks_eshop_products.id_product
      where production_bom_id=".$sintagi['id_production_bom']."
      and (pbom_variant_product_id=0 or pbom_variant_product_id=".$product['id_product'].")";
      $result = $db_link->query($sql); 
      if (!$result) gks_production_order_ergasies_error('sql error',$sql);
      while ($row = $result->fetch_assoc()) {
        $sintagi['products'][]=$row;
      }    
      
      $sql="select id_production_bom_cost,cbom_aa,cbom_cost,cbom_note,cbom_kostos_value
      from gks_production_bom_cost 
      where production_bom_id=".$sintagi['id_production_bom']."
      and (cbom_variant_product_id=0 or cbom_variant_product_id=".$product['id_product'].")";
      $result = $db_link->query($sql); 
      if (!$result) gks_production_order_ergasies_error('sql error',$sql);
      while ($row = $result->fetch_assoc()) {
        $row['cbom_kostos_value_db']=$row['cbom_kostos_value'];;
        $sintagi['costs'][]=$row;
      }    
      
    }
    unset($sintagi);
  }
  unset($product);
  
  //print '<pre>';print_r($orders_products_array);die();
  $production_kostos=0;
  $production_sintagi_total=0;
  foreach ($orders_products_array as &$product) {
    foreach ($product['sintages'] as &$sintagi) {
      $bom_monada_convert=array();
      gks_monada_convert($product['product_monada_id'], $sintagi['bom_monada_id'], $bom_monada_convert,array());
      //print '<pre>';print_r($bom_monada_convert);die();

      foreach ($sintagi['products'] as &$pitem) {
        
        $pbom_monada_convert=array();
        gks_monada_convert($pitem['product_monada_id_org'], $pitem['pbom_monada_id'], $pbom_monada_convert,array());
        $monada_convert_json=json_encode($pbom_monada_convert);
        $pitem['pbom_monada_convert']=$pbom_monada_convert;
        
        $pitem['spbom_quantity']=$product['product_quantity'] * $pitem['pbom_quantity']*$bom_monada_convert['epi']/$sintagi['bom_quantity'];
        
        if ($pitem['pbom_kostos_type']==0) //cost from product
          $pitem['pbom_kostos_value_use']=floatval($pitem['product_kostos']) * $pitem['pbom_monada_convert']['epi_rev'];
        else //cost from current sintagi
          $pitem['pbom_kostos_value_use']=floatval($pitem['pbom_kostos_value']);
        
        
        $pitem['pbom_kostos_value']=$pitem['pbom_kostos_value_use'] * $product['product_quantity'] * $pitem['pbom_quantity']*$bom_monada_convert['epi']/$sintagi['bom_quantity'];
        
        $production_kostos+=$pitem['pbom_kostos_value'];
        $production_sintagi_total++;
      }
      unset($pitem); 
      
      foreach ($sintagi['costs'] as &$citem) {
        $one_cost=$citem['cbom_kostos_value_db']/$sintagi['bom_quantity'];
        $p_cost=$one_cost*$product['product_quantity'];
        $calc_coct=$p_cost*$bom_monada_convert['epi'];
        //echo $one_cost.'|'.$p_cost.'|'.$calc_coct;die();
        $citem['cbom_kostos_value']=$calc_coct;
        $production_kostos+=$calc_coct;
        $production_sintagi_total++;
      }
      unset($citem);
    }
    unset($sintagi);
  }
  unset($product); 

  //print '<pre>';print_r($orders_products_array);die();
  
  
  foreach ($orders_products_array as &$product) {
    foreach ($product['sintages'] as &$sintagi) {
      
      
      
      $sql="select id_production_sintagi from gks_production_sintagi 
      where order_id=".$id."
      and order_product_id=".$product['id_order_product']." 
      and production_bom_id=".$sintagi['id_production_bom'];
      $result = $db_link->query($sql);        
      if (!$result) gks_production_order_ergasies_error('sql error',$sql);
      if ($result->num_rows==0) {
        $sql="insert into gks_production_sintagi (
          mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
          order_id,order_product_id,production_bom_id
        ) values (
          now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
          ".$id.",
          ".$product['id_order_product'].",
          ".$sintagi['id_production_bom']."
        )";
        $result = $db_link->query($sql);        
        if (!$result) gks_production_order_ergasies_error('sql error',$sql);
        $sintagi['id_production_sintagi'] = $db_link->insert_id;

      } else {
        $row = $result->fetch_assoc();
        $sintagi['id_production_sintagi'] = $row['id_production_sintagi'];
      }
      
      
      foreach ($sintagi['products'] as &$pitem) {
        
//        $pbom_monada_convert=array();
//        gks_monada_convert($pitem['product_monada_id_org'], $pitem['pbom_monada_id'], $pbom_monada_convert,array());
//        $pitem['pbom_monada_convert'];
        
        $monada_convert_json=json_encode($pitem['pbom_monada_convert']);
      
        if (isset($pitem['pbom_monada_convert']) and 
            isset($pitem['pbom_monada_convert']['ok']) and 
            isset($pitem['pbom_monada_convert']['epi']) and 
            $pitem['pbom_monada_convert']['ok'] and 
            $pitem['pbom_monada_convert']['epi'] !=0 and
            $pitem['pbom_monada_convert']['epi'] != 1) {
          $monada_convert_epi=$pitem['pbom_monada_convert']['epi'];
          $monada_convert_epi_rev=$pitem['pbom_monada_convert']['epi_rev'];
        } else {
          $monada_convert_epi=1;
          $monada_convert_epi_rev=1;
        }
           
        

        
        $sql="select id_production_sintagi_product from gks_production_sintagi_product
        where production_sintagi_id=".$sintagi['id_production_sintagi']."
        and production_bom_product_id=".$pitem['id_production_bom_product'];
        $result = $db_link->query($sql);        
        if (!$result) gks_production_order_ergasies_error('sql error',$sql);
        if ($result->num_rows==0) {
          $sql="insert into gks_production_sintagi_product (
            mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
            order_id,sp_order_state,
            production_sintagi_id,production_bom_product_id,spbom_aa,spbom_product_id,spbom_quantity,spbom_monada_id,spbom_note,spbom_kostos_value,
            monada_convert_json,monada_convert_epi,monada_convert_epi_rev,
            spbom_monada_id_org
          ) values (
            now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
            ".$id.",
            '".$db_link->escape_string($order_state)."',
            ".$sintagi['id_production_sintagi'].",
            ".$pitem['id_production_bom_product'].",
            ".$pitem['pbom_aa'].",
            ".$pitem['pbom_product_id'].",
            ".number_format($pitem['spbom_quantity'],8,'.','').",
            ".$pitem['pbom_monada_id'].",
            '".$db_link->escape_string($pitem['pbom_note'])."',
            ".number_format($pitem['pbom_kostos_value'],8,'.','').",
            '".$db_link->escape_string($monada_convert_json)."',
            ".number_format($monada_convert_epi,8,'.','').",
            ".number_format($monada_convert_epi_rev,8,'.','').",
            ".$pitem['product_monada_id_org']."
          )";
          $result = $db_link->query($sql);        
          if (!$result) gks_production_order_ergasies_error('sql error',$sql);
          $pitem['id_production_sintagi_product'] = $db_link->insert_id;
          
        } else {
          $row = $result->fetch_assoc();
          $pitem['id_production_sintagi_product']=$row['id_production_sintagi_product'];
          $sql="update gks_production_sintagi_product set
          order_id=".$id.",
          sp_order_state='".$db_link->escape_string($order_state )."',
          spbom_aa=".$pitem['pbom_aa'].",
          spbom_product_id=".$pitem['pbom_product_id'].",
          spbom_quantity=".number_format($pitem['spbom_quantity'],8,'.','').",
          spbom_monada_id=".$pitem['pbom_monada_id'].",
          spbom_note='".$db_link->escape_string($pitem['pbom_note'])."',
          spbom_kostos_value=".number_format($pitem['pbom_kostos_value'],8,'.','').",
          monada_convert_json='".$db_link->escape_string($monada_convert_json)."',
          monada_convert_epi=".number_format($monada_convert_epi,8,'.','').",
          monada_convert_epi_rev=".number_format($monada_convert_epi_rev,8,'.','').",
          spbom_monada_id_org=".$pitem['product_monada_id_org'].",
          mydate_edit=now(),
          user_id_edit=".$my_wp_user_id.",
          myip='".$db_link->escape_string($gkIP)."'
          where id_production_sintagi_product=".$pitem['id_production_sintagi_product'];
          $result = $db_link->query($sql);        
          if (!$result) gks_production_order_ergasies_error('sql error',$sql);
        }
      }
      unset($pitem);
      
      
      foreach ($sintagi['costs'] as &$citem) {
        $sql="select id_production_sintagi_cost from gks_production_sintagi_cost
        where production_sintagi_id=".$sintagi['id_production_sintagi']."
        and production_bom_cost_id=".$citem['id_production_bom_cost'];
        $result = $db_link->query($sql);        
        if (!$result) gks_production_order_ergasies_error('sql error',$sql);
        if ($result->num_rows==0) {
          $sql="insert into gks_production_sintagi_cost (
            mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
            order_id,sc_order_state,
            production_sintagi_id,production_bom_cost_id,scbom_aa,scbom_cost,scbom_note,scbom_kostos_value
          ) values (
            now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
            ".$id.",
            '".$db_link->escape_string($order_state )."',
            ".$sintagi['id_production_sintagi'].",
            ".$citem['id_production_bom_cost'].",
            ".$citem['cbom_aa'].",
            '".$db_link->escape_string($citem['cbom_cost'])."',
            '".$db_link->escape_string($citem['cbom_note'])."',
            ".number_format($citem['cbom_kostos_value'],8,'.','')."
          )";
          $result = $db_link->query($sql);        
          if (!$result) gks_production_order_ergasies_error('sql error',$sql);
          $citem['id_production_sintagi_cost'] = $db_link->insert_id;
          
        } else {
          $row = $result->fetch_assoc();
          $citem['id_production_sintagi_cost']=$row['id_production_sintagi_cost'];
          $sql="update gks_production_sintagi_cost set
          order_id=".$id.",
          sc_order_state='".$db_link->escape_string($order_state )."',
          scbom_aa=".$citem['cbom_aa'].",
          scbom_cost='".$db_link->escape_string($citem['cbom_cost'])."',
          scbom_note='".$db_link->escape_string($citem['cbom_note'])."',
          scbom_kostos_value=".number_format($citem['cbom_kostos_value'],8,'.','').",
          mydate_edit=now(),
          user_id_edit=".$my_wp_user_id.",
          myip='".$db_link->escape_string($gkIP)."'
          where id_production_sintagi_cost=".$citem['id_production_sintagi_cost'];
          $result = $db_link->query($sql);        
          if (!$result) gks_production_order_ergasies_error('sql error',$sql);
        }
      }
      unset($citem);      
      
    }
    unset($sintagi);
  }  
  unset($product);

  
  //print '<pre>';print_r($orders_products_array);die();

  //delete skoupidia
  
  //$id_order_product=array();
  $recs_del_no=array();
  $recs_del_nop=array();
  $recs_del_noc=array();
  
  foreach ($orders_products_array as $product) {
    //$id_order_product[]=$product['id_order_product'];
    foreach ($product['sintages'] as $sintagi) {
      $recs_del_no[]=$sintagi['id_production_sintagi'];
      foreach ($sintagi['products'] as $pitem) {
        $recs_del_nop[]=$pitem['id_production_sintagi_product'];
      } 
      foreach ($sintagi['costs'] as $citem) {
        $recs_del_noc[]=$citem['id_production_sintagi_cost'];
      } 
    }
  }
  
  
  $sql="select id_production_sintagi from gks_production_sintagi where order_id=".$id;
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  $recs_all=array();
  while ($row = $result->fetch_assoc()) {
    $recs_all[]=$row['id_production_sintagi'];
  }
  $recs_del_yes=array_diff($recs_all,$recs_del_no);
  
  $sql="delete from gks_production_sintagi 
  where order_id=".$id;
  if (count($recs_del_no)>0) $sql.=" and id_production_sintagi not in (".implode(',',$recs_del_no).")";
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  
  if  (count($recs_all)>0) {
    $sql="delete from gks_production_sintagi_product 
    where production_sintagi_id in (".implode(',',$recs_all).")";
    if (count($recs_del_nop)>0) $sql.=" and id_production_sintagi_product not in (".implode(',',$recs_del_nop).")";
    $result = $db_link->query($sql);        
    if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  
    $sql="delete from gks_production_sintagi_cost 
    where production_sintagi_id in (".implode(',',$recs_all).")";
    if (count($recs_del_noc)>0) $sql.=" and id_production_sintagi_cost not in (".implode(',',$recs_del_noc).")";
    $result = $db_link->query($sql);        
    if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  }
  

  //return;
//  print '<pre>';
//  //print 'id_order_product ';
//  //print_r($id_order_product);
//  print 'recs_all ';
//  print_r($recs_all);
//  print 'recs_del_no ';
//  print_r($recs_del_no);
//  print 'recs_del_yes ';
//  print_r($recs_del_yes);
//  print 'recs_del_nop ';
//  print_r($recs_del_nop);
//  print 'recs_del_noc ';
//  print_r($recs_del_noc);
//  
//  print_r($orders_products_array);
//  die();
  
  
  gks_production_sintagi_update_order_state($id, $order_state);
  

  
  $sql="update gks_orders set 
  production_kostos=".number_format($production_kostos,8,'.','').",
  production_sintagi_total=".$production_sintagi_total."
  where id_order=".$id;
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  
}

function gks_production_sintagi_update_order_state($id,$order_state) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  
  $sql="update gks_production_sintagi set s_order_state='".$db_link->escape_string($order_state)."' where order_id=".$id;
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  
  $sql="update gks_production_sintagi_product set sp_order_state='".$db_link->escape_string($order_state)."' where order_id=".$id;
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  
  $sql="update gks_production_sintagi_cost set sc_order_state='".$db_link->escape_string($order_state)."' where order_id=".$id;
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  
  $sql="UPDATE gks_orders 
  LEFT JOIN gks_production_sintagi_product ON gks_orders.id_order = gks_production_sintagi_product.order_id SET 
  gks_production_sintagi_product.sp_warehouses_id_from = prod_warehouses_id_from
  WHERE gks_orders.id_order=".$id;
  $result = $db_link->query($sql);
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  

  gks_production_sintagi_after_balance_for_order($id);
  
}



function gks_production_sintagi_after_balance_for_order($id) {
  global $db_link;
  
  $sql="select order_date, prod_warehouses_id_from from gks_orders where id_order=".$id." and order_date is not null";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  if ($result->num_rows==1) {
    
    $row = $result->fetch_assoc();
    $order_date=$row['order_date'];
    $warehouses_id_from=$row['prod_warehouses_id_from'];

    
    
    $sql="SELECT spbom_product_id FROM gks_production_sintagi_product WHERE order_id=".$id." GROUP BY spbom_product_id ORDER BY spbom_product_id";
    $result = $db_link->query($sql);        
    if (!$result) {
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => base64_encode('sql error'));
      echo json_encode($return); die();}
    $all_products_for_balance=array();
    while ($row = $result->fetch_assoc()) {
      $all_products_for_balance[]=$row['spbom_product_id'];
    }
    //echo '<pre>'; print_r($all_products_for_balance); echo '</pre>';die();
    
    $mybal = gks_whi_mov_balance_calc($all_products_for_balance,$order_date);
    //echo '<pre>'; print_r($mybal); echo '</pre>';die();
    
    foreach ($mybal as $id_product => $data) {
      $after_balance_warehouses_id_from=0;
      if (isset($data['warehouses'][$warehouses_id_from])) $after_balance_warehouses_id_from=$data['warehouses'][$warehouses_id_from]['bal'];
      
      $sql="update gks_production_sintagi_product set 
      after_balance_sp_warehouses_id_from=".number_format($after_balance_warehouses_id_from, 8, '.', '')."
      where order_id=".$id." and spbom_product_id=".$id_product;
      $result = $db_link->query($sql);        
      if (!$result) {
        debug_mail(false,'error sql',$sql);
        $return = array('success' => false, 'message' => base64_encode('sql error'));
        echo json_encode($return); die();}    
    } 
  }
}

function gks_whi_mov_balance_calc_for_production_sintagi($id) {
  global $db_link;
  global $GKS_ORDERS_PRODUCTION;
  
  if ($GKS_ORDERS_PRODUCTION==false) return;
  

  $sql="SELECT spbom_product_id FROM gks_production_sintagi_product WHERE order_id=".$id." GROUP BY spbom_product_id ORDER BY spbom_product_id";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  $all_products_for_balance=array();
  while ($row = $result->fetch_assoc()) {
    $all_products_for_balance[]=$row['spbom_product_id'];
  }  
  
  gks_whi_mov_balance_calc($all_products_for_balance);
  
}


function gks_order_production_warehouses_set($id) {
  global $db_link;
  global $gkIP;
  global $my_wp_user_id;
  
  $sql="select id_order,order_state,company_id,company_sub_id,warehouses_id_from,warehouses_id_to,prod_warehouses_id_from,prod_warehouses_id_to
  from gks_orders
  where id_order=".$id;
  $result = $db_link->query($sql);        
  if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  if ($result->num_rows!=1) return;
  $row_start = $result->fetch_assoc();
  
  $order_state=$row_start['order_state'];
  $company_id=$row_start['company_id'];
  $company_sub_id=$row_start['company_sub_id'];
  $warehouses_id_from=$row_start['warehouses_id_from'];
  $warehouses_id_to=$row_start['warehouses_id_to'];
  $prod_warehouses_id_from=$row_start['prod_warehouses_id_from'];
  $prod_warehouses_id_to=$row_start['prod_warehouses_id_to'];
  
  if ($order_state=='005prodraft' or 
      $order_state=='010draft' or
      $order_state=='020pending' or
      $order_state=='025offer' or
      $order_state=='030forcancellation' or
      $order_state=='040cancelled' or 
      $order_state=='050rejected' or
      $order_state=='055wait_payment' or
      $order_state=='080failed') {
    
    //force to change
    $prod_warehouses_id_from=0;
    $prod_warehouses_id_to=0;
    //echo 'ggg';die();
  } else if (($order_state=='060registered' or 
              $order_state=='070inproduction' or
              $order_state=='090indelivery') and (
              $prod_warehouses_id_from!=0 and $prod_warehouses_id_to!=0)) {
  
    return;              
  } else if ($order_state=='095execute' or 
             $order_state=='100completed' or 
             $order_state=='110payment') {
    return;
  }
  
  
  if ($warehouses_id_from>0) {
    $sql="select id_warehouse FROM gks_warehouses
    WHERE id_warehouse=".$warehouses_id_from." AND warehouse_disable=0 AND is_virtual=0";
    $result = $db_link->query($sql);
    if (!$result) gks_production_order_ergasies_error('sql error',$sql);
    if ($result->num_rows==0) $warehouses_id_from=0;
  }
  if ($warehouses_id_to>0) {
    $sql="select id_warehouse FROM gks_warehouses
    WHERE id_warehouse=".$warehouses_id_to." AND warehouse_disable=0 AND is_virtual=0";
    $result = $db_link->query($sql);
    if (!$result) gks_production_order_ergasies_error('sql error',$sql);
    if ($result->num_rows==0) $warehouses_id_to=0;
  }
  
  
  $need_update=false;
  if ($warehouses_id_from==0 and $warehouses_id_to==0 and $prod_warehouses_id_from==0 and $prod_warehouses_id_to==0) {
    $sql="select id_warehouse from gks_warehouses 
    where is_virtual=0 and company_id=".$company_id." and company_sub_id=".$company_sub_id." and warehouse_disable=0
    order by warehouse_is_company_place desc, id_warehouse limit 1";
    $result = $db_link->query($sql);
    if (!$result) gks_production_order_ergasies_error('sql error',$sql);
    if ($result->num_rows>=1) {
      $row = $result->fetch_assoc();
      $prod_warehouses_id_from=$row['id_warehouse'];
      $prod_warehouses_id_to=$row['id_warehouse'];
      $need_update=true;
    } else {
      $sql="select id_warehouse from gks_warehouses 
      where is_virtual=0 and warehouse_disable=0
      order by warehouse_is_company_place desc, id_warehouse limit 1";
      $result = $db_link->query($sql);
      if (!$result) gks_production_order_ergasies_error('sql error',$sql);
      if ($result->num_rows>=1) {
        $row = $result->fetch_assoc();
        $prod_warehouses_id_from=$row['id_warehouse'];
        $prod_warehouses_id_to=$row['id_warehouse'];
        $need_update=true;
      }
    }
  } else if ($warehouses_id_from>0 and $warehouses_id_to==0 and $prod_warehouses_id_from==0 and $prod_warehouses_id_to==0) {
    $prod_warehouses_id_from=$warehouses_id_from;
    $prod_warehouses_id_to=$warehouses_id_from;
    $need_update=true;
  } else if ($warehouses_id_from==0 and $warehouses_id_to>0 and $prod_warehouses_id_from==0 and $prod_warehouses_id_to==0) {
    $prod_warehouses_id_from=$warehouses_id_to;
    $prod_warehouses_id_to=$warehouses_id_to;
    $need_update=true;
  } 
  
  if ($need_update) {
    $sql="update gks_orders set 
    prod_warehouses_id_from=".$prod_warehouses_id_from.",
    prod_warehouses_id_to=".$prod_warehouses_id_to."
    where id_order=".$id;
    $result = $db_link->query($sql);
    if (!$result) gks_production_order_ergasies_error('sql error',$sql);
  }  
  
}
