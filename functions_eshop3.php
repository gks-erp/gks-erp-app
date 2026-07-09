<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function gks_products_get(&$mybasketarray,$product_posotita,$mydate='',$coupons=array(), $fix_prices=array(), $fields_change=array(), $gp_params=array()) {
  
  //print '<pre>44444444';print_r($fields_change); die();
  
//$product_posotita =array(0=>11,1=>22, 3=>33, 4=>44);
//$product_posotita =array(2=>11);
//$coupons=array('COUpon1');
//$myproducts = products_get(0,0,$product_posotita,'',$coupons);
//$myproducts = products_get(0,0,array(),'',array());
  
  //print '<pre>';print_r($product_posotita);  die();
  //print '<pre>qqqqqqq ';print_r($mybasketarray);  die();
  
  global $db_link;
  global $my_wp_user_id;
  global $my_is_global_admin;
  global $my_wp_user_info;  
  
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $GKS_BASKET_ROUND_DIAFORA_001;
  global $GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_LIANIKI;
  global $GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_XONDRIKI;
  global $GKS_BASKET_CALC_ITEM_DECIMAL;
  
  $mybasketarray['before_seconds_1']=0;
  $mybasketarray['before_seconds_2']=0;
  $mybasketarray['start_seconds_1']=0;
  $mybasketarray['start_seconds_2']=0;
  $mybasketarray['pick_up_time_real_1']=0;
  $mybasketarray['pick_up_time_real_2']=0;


  if (isset($mybasketarray['inv_date'])) $mydate=$mybasketarray['inv_date'];
  else if (isset($mybasketarray['order_date'])) $mydate=$mybasketarray['order_date'];
  else if (isset($mybasketarray['pay_date'])) $mydate=$mybasketarray['pay_date'];
  else if (isset($mybasketarray['transfer_reservation_date'])) $mydate=$mybasketarray['transfer_reservation_date'];
  if ($mydate=='') $mydate=date('Y-m-d H:i:s');
  //print '<pre>111 ';print_r($mybasketarray); die();
  //print '<pre>';print_r($mydate); die();

  
  $myproducts=array();

  

  $sql="select * from gks_aade_katigoria_parakratoumemenon_foron";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  $katigoria_parakratoumemenon_foron=array();
  while ($row = $result->fetch_assoc()) {
    $katigoria_parakratoumemenon_foron[$row['id_aade_katigoria_parakratoumemenon_foron']]=$row;
  }
  
  $sql="select * from gks_aade_katigoria_loipon_foron where aade_disable=0";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  $katigoria_loipon_foron=array();
  while ($row = $result->fetch_assoc()) {
    $katigoria_loipon_foron[$row['id_aade_katigoria_loipon_foron']]=$row;
  }  
  
  $sql="select * from gks_aade_katigoria_xartosimou";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  $katigoria_xartosimou=array();
  while ($row = $result->fetch_assoc()) {
    $katigoria_xartosimou[$row['id_aade_katigoria_xartosimou']]=$row;
  }  

  $sql="select * from gks_aade_katigoria_telon";
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die(); }  
  $katigoria_telon=array();
  while ($row = $result->fetch_assoc()) {
    $katigoria_telon[$row['id_aade_katigoria_telon']]=$row;
  }
  
  
  
  
  
  
  
  
  $mybasketarray['price_is_xondriki']=0;
  $mybasketarray['pricelist_descr']='';
  $sql="select pricelist_descr,price_is_xondriki from gks_eshop_pricelist where id_pricelist=".$mybasketarray['pricelist_id'];
  $result = $db_link->query($sql);
  if (!$result) {
    debug_mail(false,'products_get error sql',$sql);
    die('sql error');
  }    
  if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $mybasketarray['price_is_xondriki']=intval($row['price_is_xondriki']);
    $mybasketarray['pricelist_descr']=trim_gks($row['pricelist_descr']);
  }
  
  
  
  $id_fpa_base_in=array();
  $id_fpa_aade_in=array();
  foreach ($product_posotita as $index => $product_item) {
    //print '<pre>wsdrwretwertwert ';print_r($product_item);die();
    
    $sql = "SELECT gks_eshop_products.*, 0 as product_category_id, 
    '' as product_category_descr, 
    0 as product_category_parent_id, ";
    if ($mybasketarray['from']=='reservation' or $product_item['is_hotel_room_type']) $sql.="gks_hotel_room_type.product_id as room_type_product_id, ";
    if ($product_item['is_transfer_oxima_type']) $sql.="gks_transfer_oxima_type.product_id as oxima_type_product_id, ";
    
    $sql.="
    if (product_price_retail_sale=0,product_price_retail,
      if (product_price_retail_sale_from is null and product_price_retail_sale_to is null , product_price_retail_sale,
        if (product_price_retail_sale_from is not null and product_price_retail_sale_from<='".$mydate."' and
            product_price_retail_sale_to   is not null and product_price_retail_sale_to>='".$mydate."', product_price_retail_sale,
          if (product_price_retail_sale_from is null and
              product_price_retail_sale_to   is not null and product_price_retail_sale_to>='".$mydate."', product_price_retail_sale,
            if (product_price_retail_sale_from is not null and product_price_retail_sale_from<='".$mydate."' and
                product_price_retail_sale_to   is null, product_price_retail_sale,
              product_price_retail
            )
          )
        )
      )
    ) as product_price_retail_calc,
    
    if (product_price_yperx_sale=0,product_price_yperx,
      if (product_price_yperx_sale_from is null and product_price_yperx_sale_to is null , product_price_yperx_sale,
        if (product_price_yperx_sale_from is not null and product_price_yperx_sale_from<='".$mydate."' and
            product_price_yperx_sale_to   is not null and product_price_yperx_sale_to>='".$mydate."', product_price_yperx_sale,
          if (product_price_yperx_sale_from is null and
              product_price_yperx_sale_to   is not null and product_price_yperx_sale_to>='".$mydate."', product_price_yperx_sale,
            if (product_price_yperx_sale_from is not null and product_price_yperx_sale_from<='".$mydate."' and
                product_price_yperx_sale_to   is null, product_price_yperx_sale,
              product_price_yperx
            )
          )
        )
      )
    ) as product_price_yperx_calc,    

    if (product_price_sale=0,product_price,
      if (product_price_sale_from is null and product_price_sale_to is null , product_price_sale,
        if (product_price_sale_from is not null and product_price_sale_from<='".$mydate."' and
            product_price_sale_to   is not null and product_price_sale_to>='".$mydate."', product_price_sale,
          if (product_price_sale_from is null and
              product_price_sale_to   is not null and product_price_sale_to>='".$mydate."', product_price_sale,
            if (product_price_sale_from is not null and product_price_sale_from<='".$mydate."' and
                product_price_sale_to   is null, product_price_sale,
              product_price
            )
          )
        )
      )
    ) as product_price_calc    
    
    FROM gks_eshop_products ";

    if ($mybasketarray['from']=='reservation' or $product_item['is_hotel_room_type']) $sql.=" LEFT JOIN gks_hotel_room_type ON gks_eshop_products.id_product = gks_hotel_room_type.product_id ";
    if ($product_item['is_transfer_oxima_type']) $sql.=" LEFT JOIN gks_transfer_oxima_type ON gks_eshop_products.id_product = gks_transfer_oxima_type.product_id ";

    $sql.="    
    where gks_eshop_products.id_product=".intval($product_item['id_product']);
    if ($mybasketarray['from']=='reservation' or $product_item['is_hotel_room_type']) $sql.=' and gks_hotel_room_type.product_id>0';
    if ($product_item['is_transfer_oxima_type']) $sql.=' and gks_transfer_oxima_type.product_id>0';
    //print '<pre>';print $sql;die();

    //print '<pre>555555555 '.$sql.' ';print_r($mybasketarray);die();

    $res = $db_link->query($sql);
    if (!$res) {
      debug_mail(false,'products_get error sql',$sql);
      die('sql error');
    }
    if ($res->num_rows >= 1){
      
      
      $row = $res->fetch_assoc();
      
       
      //print '<pre>llllll ';
      //print_r($product_item);
      //die();
      
      
      $product_quantity=    $product_item['posotita'];
  		$product_set=         $product_item['set'];
      $product_sheets=      $product_item['sheets'];
      $product_monada_id=   $product_item['monada_id'];
      $product_fpa_base_id= $product_item['fpa_base_id'];
      $product_fpa_aade_id= $product_item['fpa_aade_id'];
      
      //echo '<pre>'.$product_fpa_base_id.'|'.$product_fpa_aade_id;die();
      
      //die('<pre>||'.$product_fpa_base_id.'|');
      //echo '<pre>';print_r($product_item);die();
      
      
      
      $monada_convert=array();
      gks_monada_convert($row['product_monada_id'], $product_monada_id, $monada_convert, array());
      $row['monada_convert']=$monada_convert;
      
      $row['product_price_plist_calc']=0;
      $row['product_price_plist']=0;
      $row['product_price_plist_sale']=0;
      $row['product_price_plist_sale_from']='';
      $row['product_price_plist_sale_to']='';
      $row['product_price_plist_sheets_formula']='';
      $row['product_price_plist_quantity_formula']='';
      $row['product_price_plist_include_vat']=0;
      $row['quantitycheck_price_plist']=0;
      if ($mybasketarray['pricelist_id']>=10001) {
        $sql_plist="select gks_eshop_products_prices.*,
        if (product_price_plist_sale=0,product_price_plist,
          if (product_price_plist_sale_from is null and product_price_plist_sale_to is null , product_price_plist_sale,
            if (product_price_plist_sale_from is not null and product_price_plist_sale_from<='".$mydate."' and
                product_price_plist_sale_to   is not null and product_price_plist_sale_to>='".$mydate."', product_price_plist_sale,
              if (product_price_plist_sale_from is null and
                  product_price_plist_sale_to   is not null and product_price_plist_sale_to>='".$mydate."', product_price_plist_sale,
                if (product_price_plist_sale_from is not null and product_price_plist_sale_from<='".$mydate."' and
                    product_price_plist_sale_to   is null, product_price_plist_sale,
                  product_price_plist
                )
              )
            )
          )
        ) as product_price_plist_calc
        from gks_eshop_products_prices
        where pricelist_id=".$mybasketarray['pricelist_id']."
        and product_id=".intval($product_item['id_product']);
        
        $res_plist = $db_link->query($sql_plist);
        if (!$res_plist) {
          debug_mail(false,'products_get error sql',$sql_plist);
          die('sql error');
        }
        if ($res_plist->num_rows >= 1){
          $row_plist = $res_plist->fetch_assoc(); 
          $row['product_price_plist_calc']=floatval($row_plist['product_price_plist_calc']);

          $row['product_price_plist']=floatval($row_plist['product_price_plist']);
          $row['product_price_plist_sale']=floatval($row_plist['product_price_plist_sale']);
          $row['product_price_plist_sale_from']=$row_plist['product_price_plist_sale_from'];
          $row['product_price_plist_sale_to']=$row_plist['product_price_plist_sale_to'];
          $row['product_price_plist_sheets_formula']=trim_gks($row_plist['product_price_plist_sheets_formula']);
          $row['product_price_plist_quantity_formula']=trim_gks($row_plist['product_price_plist_quantity_formula']);
          $row['product_price_plist_include_vat']=intval($row_plist['product_price_plist_include_vat']);
          $row['quantitycheck_price_plist']=floatval($row_plist['quantitycheck_price_plist']);
        }
      }
      
      //print '<pre>';print_r($row);die();
      $price_calc_array=array();

      gks_price_formula_calc($row ,$product_quantity, $product_monada_id, $product_sheets, $price_calc_array, false, $mybasketarray['acc_eidos_parastatikou_id']);
  
      //echo '<pre>';print_r($row);die();
      
      $row['calc_pricelist_item_descr']=gks_lang('Λιανική');
      $product_price_start_peritem_db = $price_calc_array['quantitycheck_price_item_retail'];
      if ($product_quantity!=0) $product_price_start_peritem_db = $price_calc_array['quantitycheck_price_retail']/$product_quantity;
      $product_price_include_vat = $row['product_price_retail_include_vat'];
      if ($mybasketarray['price_is_xondriki']==1) {
        $row['calc_pricelist_item_descr']=gks_lang('Χονδρική');
        $product_price_start_peritem_db = $price_calc_array['quantitycheck_price_item'];
        if ($product_quantity!=0) $product_price_start_peritem_db = $price_calc_array['quantitycheck_price']/$product_quantity;
        $product_price_include_vat = $row['product_price_include_vat'];
      } else if ($mybasketarray['price_is_xondriki']==2) {
        $row['calc_pricelist_item_descr']=gks_lang('ΥπερΧονδρικής');
        $product_price_start_peritem_db = $price_calc_array['quantitycheck_price_item_yperx'];
        if ($product_quantity!=0) $product_price_start_peritem_db = $price_calc_array['quantitycheck_price_yperx']/$product_quantity;
        $product_price_include_vat = $row['product_price_yperx_include_vat'];
      } else if ($mybasketarray['price_is_xondriki']==3) {
        $row['calc_pricelist_item_descr']=gks_lang('Αγοράς');
        $product_price_start_peritem_db = $price_calc_array['quantitycheck_price_item_kostos'];
        if ($product_quantity!=0) $product_price_start_peritem_db = $price_calc_array['quantitycheck_price_item_kostos']/$product_quantity;
        $product_price_include_vat = 0;
      }
      
      if ($mybasketarray['pricelist_id']>=10001 and $price_calc_array['quantitycheck_price_item_plist']>0) {
        $product_price_start_peritem_db = $price_calc_array['quantitycheck_price_item_plist'];
        if ($product_quantity!=0) $product_price_start_peritem_db = $price_calc_array['quantitycheck_price_plist']/$product_quantity;
        $product_price_include_vat = $row['product_price_plist_include_vat'];
        //print '<pre>';print_r($price_calc_array);die();
        $row['calc_pricelist_item_descr']=$mybasketarray['pricelist_descr']; //"\r\n".gks_lang('pList');
      } else {
      
        global $gks_get_pricelist_items_data;
        gks_get_pricelist_items($mybasketarray['pricelist_id'],$mydate);
        $key=$mybasketarray['pricelist_id'].'|'.$mydate;
        
        if (isset($gks_get_pricelist_items_data[$key])) {
          
          foreach ($gks_get_pricelist_items_data[$key]['items'] as $plist_item) {
            //echo '<pre>sss '; print_r($plist_item); echo '</pre>';die();
            $is_valid=true;
            if ($is_valid and $product_quantity < $plist_item['pricelist_item_min_posotita']) $is_valid=false;
  
            
            if ($is_valid and count($plist_item['list_products'])>0) {
  
              if (isset($plist_item['list_products'][$product_item['id_product']])) {
                if ($plist_item['list_products'][$product_item['id_product']]== -1) {
                  $is_valid=false;
                }
              } else {
                $is_all_ejerasi=true;
                foreach ($plist_item['list_products'] as $is_include) {
                  if ($is_include== 1) {//kapoio pou na apaitite
                    $is_all_ejerasi=false;
                    break;
                  }
                }
                if ($is_all_ejerasi==false) {
                  $is_valid=false;
                }
              }
            }
            
            $row['calc_product_categories']=[];
            if ($is_valid and count($plist_item['list_categories'])>0) {
              
              $product_class=$row['product_class'];
              $id_pcat=0;
              if ($product_class=='simple' or $product_class=='variable') {
                $id_pcat=$product_item['id_product'];
              } else if ($product_class=='variable_item' and $row['product_parent_id']>0) {
                $id_pcat=$row['product_parent_id'];
              }
              if ($id_pcat>0) {
                $sql_pcat='select product_category_id
                from gks_eshop_products_categories_products
                where product_id='.$id_pcat." order by product_category_id";
                $res_pcat = $db_link->query($sql_pcat);
                if (!$res_pcat) {debug_mail(false,'products_get error sql',$sql_pcat);die('sql error');}
                while ($row_pcat = $res_pcat->fetch_assoc()) {
                  $row['calc_product_categories'][]=$row_pcat['product_category_id'];
                }
                if (count($row['calc_product_categories'])>0) {
                  $cat_is_valid='';
                  foreach ($row['calc_product_categories'] as $pcat) {
                    if (isset($plist_item['list_categories'][$pcat])) {
                      if ($plist_item['list_categories'][$pcat]== -1) {
                        $cat_is_valid='no';
                        break;
                      } else {
                        $cat_is_valid='yes';
                      }
                    }
                  }
                  if ($cat_is_valid=='') {
                    $is_all_ejerasi=true;
                    foreach ($plist_item['list_categories'] as $is_include) {
                      if ($is_include== 1) {//kapoio pou na apaitite
                        $is_all_ejerasi=false;
                        break;
                      }
                    }
                    if ($is_all_ejerasi) {
                      $cat_is_valid='yes';
                    }
                  }
                  //echo '<pre>'.$cat_is_valid.'|'.$is_valid;die();
                  if ($cat_is_valid!='yes') $is_valid=false;
                
                } else {
                  $is_all_ejerasi=true;
                  foreach ($plist_item['list_categories'] as $is_include) {
                    if ($is_include== 1) {//kapoio pou na apaitite
                      $is_all_ejerasi=false;
                      break;
                    }
                  }
                  if ($is_all_ejerasi==false) {                
                    $is_valid=false;
                  }
                }
                //echo '<pre>'.$sql_pcat."\r\n";print_r($row['calc_product_categories']);print_r($plist_item['list_categories']);die();
              }
            }
            
            $row['calc_product_brands']=[];
            if ($is_valid and count($plist_item['list_brands'])>0) {
              $product_class=$row['product_class'];
              $id_pbra=0;
              if ($product_class=='simple' or $product_class=='variable') {
                $id_pbra=$product_item['id_product'];
              } else if ($product_class=='variable_item' and $row['product_parent_id']>0) {
                $id_pbra=$row['product_parent_id'];
              }
              if ($id_pbra>0) {
                $sql_pbra='select product_brand_id
                from gks_eshop_products_brands_products
                where product_id='.$id_pbra." order by product_brand_id";
                $res_pbra = $db_link->query($sql_pbra);
                if (!$res_pbra) {debug_mail(false,'products_get error sql',$sql_pbra);die('sql error');}
                while ($row_pbra = $res_pbra->fetch_assoc()) {
                  $row['calc_product_brands'][]=$row_pbra['product_brand_id'];
                }
                if (count($row['calc_product_brands'])>0) {
                  $bra_is_valid='';
                  foreach ($row['calc_product_brands'] as $pbra) {
                    if (isset($plist_item['list_brands'][$pbra])) {
                      if ($plist_item['list_brands'][$pbra]== -1) {
                        $bra_is_valid='no';
                        break;
                      } else {
                        $bra_is_valid='yes';
                      }
                    }
                  }
                  if ($bra_is_valid=='') {
                    $is_all_ejerasi=true;
                    foreach ($plist_item['list_brands'] as $is_include) {
                      if ($is_include== 1) {//kapoio pou na apaitite
                        $is_all_ejerasi=false;
                        break;
                      }
                    }
                    if ($is_all_ejerasi) {
                      $bra_is_valid='yes';
                    }
                  }
                  //echo '<pre>'.$bra_is_valid.'|'.$is_valid;die();
                  if ($bra_is_valid!='yes') $is_valid=false;
                
                } else {
                  $is_all_ejerasi=true;
                  foreach ($plist_item['list_brands'] as $is_include) {
                    if ($is_include== 1) {//kapoio pou na apaitite
                      $is_all_ejerasi=false;
                      break;
                    }
                  }
                  if ($is_all_ejerasi==false) {                
                    $is_valid=false;
                  }
                }
                //echo '<pre>'.$sql_pbra."\r\n";print_r($row['calc_product_brands']);print_r($plist_item['list_categories']);die();
              }
            }
              
            //echo '<pre>sss1 '.$is_valid.'|'.$plist_item['pricelist_item_descr'].'|'.$product_price_start_peritem_db.'</pre>';
            //echo '<pre>';print_r($plist_item);echo '</pre>';
            
            if ($is_valid) {
              $row['calc_pricelist_item_descr'].="\r\n".$plist_item['pricelist_item_descr'];
              $pricelist_item_price_eval=trim_gks($plist_item['pricelist_item_price_eval']); 
              if ($pricelist_item_price_eval!='') {
      					$eval_string= $pricelist_item_price_eval;
      					$eval_string=substr($eval_string, 1, strlen($eval_string)-1);
      					$eval_string= str_replace('[[posotita]]', number_format($product_quantity,10,'.',''), $eval_string);
      					$eval_string= str_replace('[[price]]', number_format($product_price_start_peritem_db,10,'.',''), $eval_string);
      					$product_price_start_peritem_db = round(eval('return '.$eval_string.';'),$GKS_BASKET_CALC_ITEM_DECIMAL);
              } else {
                $pricelist_item_price_epi=floatval($plist_item['pricelist_item_price_epi']);
                $pricelist_item_price_plus=floatval($plist_item['pricelist_item_price_plus']);
    	          $product_price_start_peritem_db=round($product_price_start_peritem_db * (1+$pricelist_item_price_epi) + $pricelist_item_price_plus,$GKS_BASKET_CALC_ITEM_DECIMAL);
              }
              //echo '<pre>sss2 '.$product_price_start_peritem_db.'</pre>';
              break; 
            }
            
          }
          
        }
      }
      //echo '<pre>sss '.$product_price_start_peritem_db;die();
      //echo '<pre>sss ';print_r($price_calc_array);die();
      //product_price_retail
      
      $product_normal=$row['product_normal'];
      $room_ajia_table=array();
      $oxima_ajia_table=array();
      
//      echo '<pre>';
//      echo $row['room_type_product_id'];
//      die();
      
      
      
      if (isset($row['room_type_product_id']) and $row['room_type_product_id']>0) {
        $product_price_include_vat=1;
        $product_normal=1;
        //echo 'gggg';
        
        if ($mybasketarray['from']=='reservation') {
        //if (1==1 or $mybasketarray['from']=='reservation') {
          //echo '<pre>sssssssssdffffffffff';print_r($row);die();
          //print_r($product_item);
          //print_r($mybasketarray['products'][$index]);
          //die();
          
          //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/mybasketarray.txt',print_r($mybasketarray,true));
          
          $roomid=$mybasketarray['products'][$index]['user_room_id'];
          //echo '<pre>roomid';print_r($row);die();
          $get_availability_rooms_input=array(
            'id_hotel' => $mybasketarray['products'][$index]['id_hotel'],
            'date_from' => $mybasketarray['products'][$index]['user_check_in'],
            'date_to' => $mybasketarray['products'][$index]['user_check_out'],
            'alldata' => true,
            'id_hotel_room' => $roomid,
            'id_hotel_room_type' => 0,
            'not_id_hotel_reservation' => 0,
            'not_id_hotel_folio' => 0,
            'not_id_hotel_room' => array(),
            'rnum_adults' => $mybasketarray['products'][$index]['user_rnum_adults'],
            'rnum_childs' => $mybasketarray['products'][$index]['user_rnum_childs'],
            'rchilds_ages_list' => json_decode($mybasketarray['products'][$index]['user_rchilds_ages_list'], true),
            'rnum_child_kounies' => $mybasketarray['products'][$index]['user_rnum_child_kounies'],
            'rnum_extra_beds' => $mybasketarray['products'][$index]['user_rnum_extra_beds'],
          );
          
          $roomaf=get_availability_rooms($get_availability_rooms_input);
          $product_price_start_peritem_db=0;
          if (isset($roomaf['rooms'][$roomid]['room_ajia_table'])) {
            $product_price_start_peritem_db = $roomaf['rooms'][$roomid]['room_ajia_table']['ajia_total_out']/$product_quantity;
            $room_ajia_table = $roomaf['rooms'][$roomid]['room_ajia_table'];
          }
          //echo '<pre>roomaf '.$roomid.' ';print_r($roomaf);die();
          //echo '<pre>';
          //die();
        }
      }
      
      if (isset($row['oxima_type_product_id']) and $row['oxima_type_product_id']>0) {
        $product_price_include_vat=1;
        $product_normal=1;
        
        if ($product_item['is_transfer_oxima_type']) {
        //if (1==1 or $mybasketarray['from']=='reservation') {
          //print '<pre>dddddddd';
          //print_r($product_item);
          //print_r($mybasketarray['products'][$index]);
          //die();
          
          //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/mybasketarray.txt',print_r($mybasketarray,true));
          //echo '<pre>prin oximaaf ';print_r($mybasketarray);die();
          //echo '<pre>prin oximaaf ';print_r($mybasketarray['products'][$index]);die();
          //echo 1/0;die();
          $oximaid=$mybasketarray['products'][$index]['user_asset_id'];

          
          $get_availability_oximata_input=array(
            'from' => 'transfer',
            'id_transfer' => $mybasketarray['products'][$index]['id_transfer'],
            'transfer_start' => $mybasketarray['products'][$index]['user_transfer_start'],
            'transfer_end' => $mybasketarray['products'][$index]['user_transfer_end'],
            'alldata' => true,
            'id_asset' => $oximaid,
            'id_transfer_oxima_type' => $mybasketarray['products'][$index]['user_oxima_type_id'], // i miden efoson den exo
            'not_id_transfer_reservation' => 0,
            'not_id_asset_rental' => 0,
            'not_id_asset' => array(),
            'rnum_adults' => $mybasketarray['products'][$index]['user_rnum_adults'],
            'rnum_childs' => $mybasketarray['products'][$index]['user_rnum_childs'],
            'rnum_babys' => $mybasketarray['products'][$index]['user_rnum_babys'],
            
            'poi_id_from' => $mybasketarray['products'][$index]['user_poi_id_from'],
            'poi_from_place_id' => $mybasketarray['products'][$index]['user_poi_from_place_id'],
            'poi_from_place_formatted_address' => $mybasketarray['products'][$index]['user_poi_from_place_formatted_address'],
            'poi_from_place_lat' => $mybasketarray['products'][$index]['user_poi_from_place_lat'],
            'poi_from_place_lng' => $mybasketarray['products'][$index]['user_poi_from_place_lng'],
            
            
            'poi_id_to' => $mybasketarray['products'][$index]['user_poi_id_to'],
            'poi_to_place_id' => $mybasketarray['products'][$index]['user_poi_to_place_id'],
            'poi_to_place_formatted_address' => $mybasketarray['products'][$index]['user_poi_to_place_formatted_address'],
            'poi_to_place_lat' => $mybasketarray['products'][$index]['user_poi_to_place_lat'],
            'poi_to_place_lng' => $mybasketarray['products'][$index]['user_poi_to_place_lng'],
            
            'apostasi' => $mybasketarray['products'][$index]['user_apostasi'],
            'diarkeia' => $mybasketarray['products'][$index]['user_diarkeia'],
            
            'group_type' => $mybasketarray['products'][$index]['user_group_type'],
            'rsrv_oxima_num_booster' => $mybasketarray['products'][$index]['user_rsrv_oxima_num_booster'],
            'rsrv_oxima_num_kareklakia' => $mybasketarray['products'][$index]['user_rsrv_oxima_num_kareklakia'],
            'rsrv_oxima_num_amajidia' => $mybasketarray['products'][$index]['user_rsrv_oxima_num_amajidia'],
            'rsrv_oxima_num_golfbag' => $mybasketarray['products'][$index]['user_rsrv_oxima_num_golfbag'],
            'rsrv_oxima_num_skis' => $mybasketarray['products'][$index]['user_rsrv_oxima_num_skis'],
            'rsrv_oxima_num_5minstop' => $mybasketarray['products'][$index]['user_rsrv_oxima_num_5minstop'],

            
            
          );
          //echo '<pre>oximaaf ';print_r($get_availability_oximata_input);die();
          //print '<pre>sssssssssssss1111'; print_r($gp_params);die();

          
          $ao_params=$gp_params;
          $oximaaf=get_availability_oximata($get_availability_oximata_input,$ao_params);
          $product_price_start_peritem_db=0;
          
          $mybasketarray['before_seconds_1']=$oximaaf['before_seconds_1'];
          $mybasketarray['before_seconds_2']=$oximaaf['before_seconds_2'];
          $mybasketarray['start_seconds_1']=$oximaaf['start_seconds_1'];
          $mybasketarray['start_seconds_2']=$oximaaf['start_seconds_2'];
          $mybasketarray['pick_up_time_real_1']=$oximaaf['pick_up_time_real_1'];
          $mybasketarray['pick_up_time_real_2']=$oximaaf['pick_up_time_real_2'];
            
//          print '<pre>sssssssss 11112 ';print_r($oximaaf);die();
//          if ($oximaid==0) {
//            //echo '<pre>oximaaf '.$oximaid.' ';print_r($oximaaf);die();
//            foreach ($oximaaf['oximata'] as $key_oxima => $value) { //na vro to proto, opoio einai.
//              $oximaid=$key_oxima;
//              break;
//            }
//          }
          
          if (isset($oximaaf['oximata'][$oximaid]['oxima_ajia_table'])) {
            $product_price_start_peritem_db = $oximaaf['oximata'][$oximaid]['oxima_ajia_table']['ajia_total_out']/$product_quantity;
            $oxima_ajia_table = $oximaaf['oximata'][$oximaid]['oxima_ajia_table'];
          }
          //$product_price_start_peritem_db = 200;
          
          if ($oximaaf['error_msg']!='') {
            $return = array('success' => false, 'message' => base64_encode($oximaaf['error_msg']));
            echo json_encode($return); die();    
          }
          
          //echo '<pre>oximaaf '.$oximaid.' ';print_r($oximaaf);die();
          //echo '<pre>oximaaf '.$oximaid.' ';print_r($oxima_ajia_table);die();
          //echo '<pre>mybasketarray '.$oximaid.' ';print_r($mybasketarray);die();
          //echo '<pre>';
          //die();
        }
      }
      
      //print '<pre>ssssssss ';print_r($row);die();
      
      if ($product_fpa_base_id<=0 and $product_fpa_aade_id<=0) {
        $product_fpa_base_id=$row['product_fpa_base_id'];
      }
      
      //die('<pre>|'.$product_fpa_base_id.'|');
      $thisitem=array('id_product' => $row['id_product'],
                      'product_set' => $product_set,
                      'product_descr' => $row['product_descr'],
                      'product_descr_small' => $row['product_descr_small'],
                      'product_descr_big' => $row['product_descr_big'],
                      'product_type' => $row['product_type'],
                      'product_object_name' => $row['product_object_name'],
                      'product_is_digital' => $row['product_is_digital'],
                      'product_is_simple_download' => $row['product_is_simple_download'],
                      'product_need_apostoli' => $row['product_need_apostoli'],
                      'product_fpa_base_id_org' => $row['product_fpa_base_id'],
                      'product_fpa_base_id' => $product_fpa_base_id,
                      'product_fpa_aade_id' => $product_fpa_aade_id,
                      'product_fpa_id' => $product_fpa_base_id,
                      'product_fpa_id_array' => array(),
                      'product_normal' => $product_normal,
                      'product_need_multi_files' => $row['product_need_multi_files'],
                      'product_need_multi_files_min' => $row['product_need_multi_files_min'],
                      'product_need_multi_files_max' => $row['product_need_multi_files_max'],
                      'product_varos' => floatval($row['product_varos']),
                      'product_ogos_x' => floatval($row['product_ogos_x']),
                      'product_ogos_y' => floatval($row['product_ogos_y']),
                      'product_ogos_z' => floatval($row['product_ogos_z']),
                      'product_sortorder' => $row['product_sortorder'],
                      'product_disable' => $row['product_disable'],
                      'product_show_on_dialog' => $row['product_show_on_dialog'],
                      'product_min_pixels_x' => $row['product_min_pixels_x'],
                      'product_min_pixels_y' => $row['product_min_pixels_y'],
                      'product_min_pixels_can_rotate' => $row['product_min_pixels_can_rotate'],
                      
                      
                      
                      'product_category_id' => $row['product_category_id'],
                      'product_category_descr' => $row['product_category_descr'],
                      'product_category_parent_id' => $row['product_category_parent_id'],
                      'product_sheets' => $product_sheets,
                      'product_monada_id_org' => $row['product_monada_id'],
                      'product_monada_id' => $product_monada_id,
                      'monada_convert' => $row['monada_convert'],
                      'product_quantity' => $product_quantity,
                      
                      //'product_price_start_peritem_db' => $row['product_price'],
                      //'product_price_include_vat' => $row['product_price_include_vat'],
                      
                      'product_price_start_peritem_db' => $product_price_start_peritem_db,
                      'product_price_include_vat' => $product_price_include_vat,
                      
                      'product_price_start_peritem_net' => 0,
                      'product_price_start_peritem_fpa' => 0,
                      'product_price_start_peritem_total' => 0,
                      'product_price_start_all_net' => 0,
                      'product_price_start_all_fpa' => 0,
                      'product_price_start_all_total' => 0,
  
                      'product_price_final_peritem_db' => 0,
                      'product_price_final_peritem_net' => 0,
                      'product_price_final_peritem_fpa' => 0,
                      'product_price_final_peritem_total' => 0,
                      'product_price_final_all_net' => 0,
                      'product_price_final_all_fpa' => 0,
                      'product_price_final_all_total' => 0,
                      
  
                      'product_pricelist_item_id' => 0,
                      'product_pricelist_item_descr' => '',
                      'product_pricelist_item_percent' => 0,
                      'product_price_coupon_use' => '',
                      'product_price_coupon_use_disabled' => '',
                      
                      'price_calc_array' => $price_calc_array,
                      'product_price_sheets_formula' => trim_gks($row['product_price_sheets_formula']),
                      'product_price_quantity_formula' => trim_gks($row['product_price_quantity_formula']),
                      'product_price_retail_sheets_formula' => trim_gks($row['product_price_retail_sheets_formula']),
                      'product_price_retail_quantity_formula' => trim_gks($row['product_price_retail_quantity_formula']),
                      
                      'room_ajia_table' => $room_ajia_table,
                      'oxima_ajia_table' => $oxima_ajia_table,
                      
                      'calc_pricelist_item_descr'=>$row['calc_pricelist_item_descr'],
                    );
      
      //die('<pre>[['.$thisitem['product_fpa_id'].'|');
      if (isset($thisitem['product_fpa_base_id']) && $thisitem['product_fpa_base_id']>0) {
        if (!in_array($thisitem['product_fpa_base_id'],$id_fpa_base_in)) $id_fpa_base_in[]=$thisitem['product_fpa_base_id'];
      } else if (isset($thisitem['product_fpa_aade_id']) && $thisitem['product_fpa_aade_id']>0) {
        if (!in_array($thisitem['product_fpa_aade_id'],$id_fpa_aade_in)) $id_fpa_aade_in[]=$thisitem['product_fpa_aade_id'];
      } else {
        if (!in_array(1,$id_fpa_base_in)) $id_fpa_base_in[]=1;
      }
      

      $thisitem['input'] = $product_item;
      $myproducts[$index] = $thisitem;      
      
    }

  } 
  //echo '<pre>fffffffffffffff';print_r($id_fpa_base_in);print_r($id_fpa_aade_in);die();
  
  //print '<pre>vvvvvvvvvvaaaa ';print_r($myproducts); die();
  
//  return $myproducts;
  

  
//print '<pre>';
//print_r($myproducts);
//die();
    
//  print '<pre>aa';
//  print_r($mybasketarray['fiscal_position']);
//  print_r($id_fpa_base_in);
//  die();
  
  
  
  
  if (isset($mybasketarray['eidos_parastatikou_has_fpa'])==false) $mybasketarray['eidos_parastatikou_has_fpa']=1;
  
  $myfpa_base = array();
  if (count($id_fpa_base_in)>0) {
    $fzero=false;
    if ($mybasketarray['eidos_parastatikou_has_fpa']==0 or $mybasketarray['eidos_parastatikou_has_fpa']==2) $fzero=true;



  
    
    $corsc_id=0;//company or sub company id
    $corsc_f='';
    $corsc_fs='';
    if (isset($mybasketarray['company_sub_id']) and $mybasketarray['company_sub_id']>0) {
      $corsc_id=$mybasketarray['company_sub_id'];
      $corsc_f='_sub';$corsc_fs='_subs';
    } else if (isset($mybasketarray['company_id']) and $mybasketarray['company_id']>0) {
      $corsc_id=$mybasketarray['company_id'];
    }
    
    
    $sql="SELECT fpa_base_id, id_fpa, fpa_descr, fpa_descr_print, fpa_pososto
    FROM gks_company".$corsc_fs."_basefpa 
    LEFT JOIN gks_eshop_fpa ON gks_company".$corsc_fs."_basefpa.fpa_id = gks_eshop_fpa.id_fpa
    WHERE fpa_base_id>0 AND fpa_id>0 and id_fpa>0
    and company".$corsc_f."_id=".$corsc_id;
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'products_get error sql',$sql);die('sql error');}
    
    while ($row = $result->fetch_assoc()) {
      $myfpa_base[$row['fpa_base_id']] = array(
        'id_fpa_to' => ($fzero ? 0 : $row['id_fpa']),
        'id_fpa_fr' => $row['id_fpa'],
        'fpa_descr' => ($fzero ? gks_lang('Χωρίς ΦΠΑ') : $row['fpa_descr']),
        'fpa_descr_fr' => $row['fpa_descr'],
        'fpa_descr_print' => ($fzero ? '0%' : $row['fpa_descr_print']),
        'fpa_descr_print_fr' => $row['fpa_descr_print'],
        'fpa_pososto' => ($fzero ? 0 : $row['fpa_pososto']),
        'fpa_pososto_fr' => $row['fpa_pososto'],
       ); 
    }
    //print '<pre>';print_r($myfpa_base);die();
    
    
    //eidika gia kathe fiscal ean iparxoun na ginoun overide
    $sql="SELECT gks_company".$corsc_fs."_fpa.fpa_base_id as fpa_base_id_from, 
    gks_eshop_fpa_fr.id_fpa AS id_fpa_fr, gks_eshop_fpa_fr.fpa_descr AS fpa_descr_fr, gks_eshop_fpa_fr.fpa_descr_print AS fpa_descr_print_fr, gks_eshop_fpa_fr.fpa_pososto AS fpa_pososto_fr, 
    gks_eshop_fpa_to.id_fpa AS id_fpa_to, gks_eshop_fpa_to.fpa_descr AS fpa_descr_to, gks_eshop_fpa_to.fpa_descr_print AS fpa_descr_print_to, gks_eshop_fpa_to.fpa_pososto AS fpa_pososto_to
    FROM (((gks_company".$corsc_fs."_fpa 
    LEFT JOIN gks_eshop_fiscal_position ON gks_company".$corsc_fs."_fpa.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
    LEFT JOIN (
      SELECT fpa_base_id, fpa_id FROM gks_company".$corsc_fs."_basefpa WHERE company".$corsc_f."_id=".$corsc_id."
    ) AS efb ON gks_company".$corsc_fs."_fpa.fpa_base_id = efb.fpa_base_id) 
    LEFT JOIN gks_eshop_fpa AS gks_eshop_fpa_fr ON efb.fpa_id = gks_eshop_fpa_fr.id_fpa) 
    LEFT JOIN gks_eshop_fpa AS gks_eshop_fpa_to ON gks_company".$corsc_fs."_fpa.fpa_id = gks_eshop_fpa_to.id_fpa
    WHERE gks_company".$corsc_fs."_fpa.fpa_base_id In (".implode(',', $id_fpa_base_in).")
    AND gks_eshop_fiscal_position.id_fiscal_position=".$mybasketarray['fiscal_position']."
    AND gks_company".$corsc_fs."_fpa.company".$corsc_f."_id=".$corsc_id;
    

    //print '<pre>'.$sql; die(); 
    //print '<pre>bbbbbbbbb ';print_r($mybasketarray); die(); 
    
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'products_get error sql',$sql);die('sql error');}
    
    while ($row = $result->fetch_assoc()) {
      $myfpa_base[$row['fpa_base_id_from']] = array(
        'id_fpa_to' => ($fzero ? 0 : $row['id_fpa_to']),
        'id_fpa_fr' => $row['id_fpa_fr'],
        'fpa_descr' => ($fzero ? gks_lang('Χωρίς ΦΠΑ') : $row['fpa_descr_to']),
        'fpa_descr_fr' => $row['fpa_descr_fr'],
        'fpa_descr_print' => ($fzero ? '0%' : $row['fpa_descr_print_to']),
        'fpa_descr_print_fr' => $row['fpa_descr_print_fr'],
        'fpa_pososto' => ($fzero ? 0 : $row['fpa_pososto_to']),
        'fpa_pososto_fr' => $row['fpa_pososto_fr'],
       ); 
    }
    //print '<pre>';print_r($myfpa_base);die();
  }
  
  $myfpa_aade = array();
  if (count($id_fpa_aade_in)>0) {
    $fzero=false;
    if ($mybasketarray['eidos_parastatikou_has_fpa']==0 or $mybasketarray['eidos_parastatikou_has_fpa']==2) $fzero=true;

    
    $sql="SELECT gks_aade_katigoria_fpa.id_aade_katigoria_fpa, 
    gks_eshop_fpa.id_fpa, 
    gks_eshop_fpa.fpa_descr, 
    gks_eshop_fpa.fpa_descr_print, 
    gks_eshop_fpa.fpa_pososto
    FROM gks_aade_katigoria_fpa 
    LEFT JOIN gks_eshop_fpa ON gks_aade_katigoria_fpa.direct_fpa_id = gks_eshop_fpa.id_fpa
    WHERE gks_aade_katigoria_fpa.id_aade_katigoria_fpa In (".implode(',', $id_fpa_aade_in).") 
    AND gks_eshop_fpa.fpa_disable=0";

    //print '<pre>'.$sql; die(); 
    
    $result = $db_link->query($sql);
    if (!$result) {debug_mail(false,'products_get error sql',$sql);die('sql error');}
    
    while ($row = $result->fetch_assoc()) {
      $myfpa_aade[$row['id_aade_katigoria_fpa']] = array(
        'id_fpa_to' => ($fzero ? 0 : $row['id_fpa']),
        'id_fpa_fr' => $row['id_fpa'],
        'fpa_descr' => ($fzero ? gks_lang('Χωρίς ΦΠΑ') : $row['fpa_descr']),
        'fpa_descr_fr' => $row['fpa_descr'],
        'fpa_descr_print' => ($fzero ? '0%' : $row['fpa_descr_print']),
        'fpa_descr_print_fr' => $row['fpa_descr_print'],
        'fpa_pososto' => ($fzero ? 0 : $row['fpa_pososto']),
        'fpa_pososto_fr' => $row['fpa_pososto'],
       ); 
    }
  }  
  
  //print '<pre>ddddddddddddd fpa';print_r($myfpa_aade);die();
    
  foreach ($myproducts as &$value) {
    if (isset($value['product_fpa_base_id']) and $value['product_fpa_base_id'] >0 and isset($myfpa_base[$value['product_fpa_base_id']])) {
      $value['product_fpa_id_array'] = $myfpa_base[$value['product_fpa_base_id']];
    } else if (isset($value['product_fpa_aade_id']) and $value['product_fpa_aade_id'] >0 and isset($myfpa_aade[$value['product_fpa_aade_id']])) {
      $value['product_fpa_id_array'] = $myfpa_aade[$value['product_fpa_aade_id']];
    } else { // 
      //print '<pre>'; print_r($value); die();        
      $value['product_fpa_id_array'] = array( //as valo kati na exei , kalou-kakou
        'id_fpa_to' => 0,
        'id_fpa_fr' => 0,
        'fpa_descr' => '--',
        'fpa_descr_fr' => '--',
        'fpa_descr_print' => '--',
        'fpa_descr_print_fr' => '--',
        'fpa_pososto' => 0,
        'fpa_pososto_fr' => 0,
      );         
      if (isset($value['product_fpa_id']) and $value['product_fpa_id']>0) {
        $sql="select * from gks_eshop_fpa where id_fpa=".$value['product_fpa_id'];
        $result = $db_link->query($sql);
        if (!$result) {debug_mail(false,'products_get error sql',$sql);die('sql error');}
        if ($result->num_rows == 1) {
          $row = $result->fetch_assoc();
          $value['product_fpa_id_array'] = array(
            'id_fpa_to' => ($fzero ? 0 : $row['id_fpa']),
            'id_fpa_fr' => $row['id_fpa'],
            'fpa_descr' => ($fzero ? gks_lang('Χωρίς ΦΠΑ') : $row['fpa_descr']),
            'fpa_descr_fr' => $row['fpa_descr'],
            'fpa_descr_print' => ($fzero ? '0%' : $row['fpa_descr_print']),
            'fpa_descr_print_fr' => $row['fpa_descr_print'],
            'fpa_pososto' => ($fzero ? 0 : $row['fpa_pososto']),
            'fpa_pososto_fr' => $row['fpa_pososto'],
          );             
        }  
      } 
      
    }
  }
  unset($value);
  
  
  
  
  //print '<pre>';print_r($id_fpa_base_in);print_r($id_fpa_aade_in);print_r($myfpa_base);print_r($myfpa_aade);print_r($myproducts);die(); 
    
//  foreach ($myproducts as &$value) {
//    if (isset($product_posotita[$value['id_product']])) {
//      $value['product_quantity'] = $product_posotita[$value['id_product']];
//    } else if (isset($product_posotita[0])) { //defalt gia ola ta proionta otan key=0 kai den exei allo item sto array
//      $value['product_quantity'] = $product_posotita[0];
//    }
//  }
//  unset($value);
  //return $myproducts;
  
  
  $sql="SELECT * from gks_eshop_pricelist_items 
  where pricelist_id=".$mybasketarray['pricelist_id']." 
  and pricelist_item_disable=0 
  and pricelist_item_coupon<>''
  and ('".$mydate."' >= pricelist_item_date_from or pricelist_item_date_from is null)
  and ('".$mydate."' <= pricelist_item_date_to or pricelist_item_date_to is null)
  order by pricelist_item_sequence";
  //echo '<pre>';echo $sql;die();
  
  $res = $db_link->query($sql);
  if (!$res) {
    debug_mail(false,'products_get error sql pricelist',$sql);
    die('sql error');
  }
    
  $pricelist_items = array();
  
  while ($row = $res->fetch_assoc()) {

     
    $pricelist_items[$row['id_pricelist_item']] = array(
      'id_pricelist_item' => $row['id_pricelist_item'],
      'pricelist_item_descr' => $row['pricelist_item_descr'],
      'pricelist_item_sequence' => $row['pricelist_item_sequence'],
      'pricelist_item_coupon' => isset($row['pricelist_item_coupon']) ? $row['pricelist_item_coupon'] : '' ,
      'pricelist_item_date_from' => isset($row['pricelist_item_date_from']) ? $row['pricelist_item_date_from'] : '' ,
      'pricelist_item_date_to' => isset($row['pricelist_item_date_to']) ? $row['pricelist_item_date_to'] : '' ,
      'pricelist_item_product_id' => [], //$row['pricelist_item_product_id'],
      'pricelist_item_category_id' => [], //$row['pricelist_item_category_id'],
      'pricelist_item_brand_id' => [], //$row['pricelist_item_brand_id'],
      'pricelist_item_min_posotita' => $row['pricelist_item_min_posotita'],
      'pricelist_item_price_epi' => $row['pricelist_item_price_epi'],
      'pricelist_item_price_plus' => $row['pricelist_item_price_plus'],
      'pricelist_item_price_eval' => $row['pricelist_item_price_eval'],
    ); 
  }
  

  //print '<pre>';print_r($pricelist_items);die(); 

  
  
  //print '<pre>vvvvvvvvvv';print_r($myproducts); die();


  foreach ($myproducts as $index => &$value) {
    

    
    $item=&$mybasketarray['products'][$index];

    $other_taxes_sum_pososto=0;
    $other_taxes_sum_value=0;
    
    $item['other_taxes']['withheld_edit']='free';
    if (isset($item['other_taxes']['withheldPercentCategory'])) {
      if ($item['other_taxes']['withheldPercentCategory']<=0 or isset($katigoria_parakratoumemenon_foron[$item['other_taxes']['withheldPercentCategory']])==false) {
        $item['other_taxes']['withheldAmount']=0;
        $item['other_taxes']['withheld_edit']='fix';
      } else {
        $tkat1=$katigoria_parakratoumemenon_foron[$item['other_taxes']['withheldPercentCategory']];
        if ($tkat1['aade_katigoria_parakratoumemenon_foron_type']=='pososto') {
          //$other_taxes_sum_pososto+=$tkat1['aade_katigoria_parakratoumemenon_foron_pososto'];
          $item['other_taxes']['withheldAmount']=0;//round(
            //$item['product_id']['product_price_final_all_net']*($tkat1['aade_katigoria_parakratoumemenon_foron_pososto']/100)
            //, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);

          $item['other_taxes']['withheld_edit']='fix';
        } else if ($tkat1['aade_katigoria_parakratoumemenon_foron_type']=='free') {
          $item['other_taxes']['withheld_edit']='free'; //no change 
          $other_taxes_sum_value+=$item['other_taxes']['withheldAmount'];
        } 
        //print '<pre>'; print_r($tkat1);die();
      }
    }
    if (isset($item['other_taxes']['otherTaxesPercentCategory'])) {
      if ($item['other_taxes']['otherTaxesPercentCategory']<=0 or isset($katigoria_loipon_foron[$item['other_taxes']['otherTaxesPercentCategory']])==false) {
        $item['other_taxes']['otherTaxesAmount']=0;
        $item['other_taxes']['othertaxes_edit']='fix';
      } else {
        $tkat2=$katigoria_loipon_foron[$item['other_taxes']['otherTaxesPercentCategory']];
        if ($tkat2['aade_katigoria_loipon_foron_type']=='pososto') {
          $other_taxes_sum_pososto+=$tkat2['aade_katigoria_loipon_foron_pososto'];
          $item['other_taxes']['otherTaxesAmount']=0; //round(
          //  $item['product_id']['product_price_final_all_net']*($tkat2['aade_katigoria_loipon_foron_pososto']/100)
          //  , $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
          $item['other_taxes']['othertaxes_edit']='fix';
        } else if ($tkat2['aade_katigoria_loipon_foron_type']=='free') {
          $item['other_taxes']['othertaxes_edit']='free'; //no change 
          $other_taxes_sum_value+=$item['other_taxes']['otherTaxesAmount'];
        } else if ($tkat2['aade_katigoria_loipon_foron_type']=='function') {
          $item['other_taxes']['othertaxes_edit']='fix';
          $item['other_taxes']['otherTaxesAmount'] = call_user_func_array($tkat2['aade_katigoria_loipon_foron_poso_fn'],array($value));
          $other_taxes_sum_value+=$item['other_taxes']['otherTaxesAmount'];
        }
      }
    }
    if (isset($item['other_taxes']['stampDutyPercentCategory'])) {
      if ($item['other_taxes']['stampDutyPercentCategory']<=0 or isset($katigoria_xartosimou[$item['other_taxes']['stampDutyPercentCategory']])==false) {
        $item['other_taxes']['stampDutyAmount']=0;
        $item['other_taxes']['stampduty_edit']='fix';
      } else {
        $tkat3=$katigoria_xartosimou[$item['other_taxes']['stampDutyPercentCategory']];
        
        //echo '<pre>';print_r($tkat3);die();

        if ($tkat3['aade_katigoria_xartosimou_type']=='pososto') {
          $other_taxes_sum_pososto+=$tkat3['aade_katigoria_xartosimou_pososto'];
          $item['other_taxes']['stampDutyAmount']=0; //round(
          //  $item['product_id']['product_price_final_all_net']*($tkat3['aade_katigoria_xartosimou_pososto']/100)
          //  , $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
          $item['other_taxes']['stampduty_edit']='fix';
        } else if ($tkat3['aade_katigoria_xartosimou_type']=='free') {
          $item['other_taxes']['stampduty_edit']='free'; //no change 
          $other_taxes_sum_value+=$item['other_taxes']['stampDutyAmount'];
        } else if ($tkat3['aade_katigoria_xartosimou_type']=='function') {
          $item['other_taxes']['stampduty_edit']='fix';
          $item['other_taxes']['stampDutyAmount'] = call_user_func_array($tkat3['aade_katigoria_loipon_foron_poso_fn'],array($value));
          $other_taxes_sum_value+=$item['other_taxes']['stampDutyAmount'];
        }
                
        
//        $other_taxes_sum_pososto+=$tkat3['aade_katigoria_xartosimou_pososto'];
//        $item['other_taxes']['stampDutyAmount']=0; //round(
//          //$item['product_id']['product_price_final_all_net']*($tkat3['aade_katigoria_xartosimou_pososto']/100)
//          //, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
//        $item['other_taxes']['stampduty_edit']='fix';
      }
      
    }
    if (isset($item['other_taxes']['feesPercentCategory'])) {
      if ($item['other_taxes']['feesPercentCategory']<=0 or isset($katigoria_telon[$item['other_taxes']['feesPercentCategory']])==false) {
        $item['other_taxes']['feesAmount']=0;
        $item['other_taxes']['feesammount_edit']='fix';
        //if ($item['product_id']['id_product']==10063) {
        //  print '<pre>';print_r($item['other_taxes']);die();
        //}
      } else {
        $tkat4=$katigoria_telon[$item['other_taxes']['feesPercentCategory']];
        if ($tkat4['aade_katigoria_telon_type']=='pososto') {
          $other_taxes_sum_pososto+=$tkat4['aade_katigoria_telon_pososto'];
          $item['other_taxes']['feesAmount']=0; //round(
            //$item['product_id']['product_price_final_all_net']*($tkat4['aade_katigoria_telon_pososto']/100)
            //, $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
          $item['other_taxes']['feesammount_edit']='fix';
        } else if ($tkat4['aade_katigoria_telon_type']=='free') {
          $item['other_taxes']['feesammount_edit']='free'; //no change 
          $other_taxes_sum_value+=$item['other_taxes']['feesAmount'];
        } else if ($tkat4['aade_katigoria_telon_type']=='function') {
          $item['other_taxes']['feesammount_edit']='fix';
          $item['other_taxes']['feesAmount'] = call_user_func_array($tkat4['aade_katigoria_telon_poso_fn'],array($value));
          $other_taxes_sum_value+=$item['other_taxes']['feesAmount'];
        }
      }
    } else {
      //print '<pre>';print $item['other_taxes']['feesAmount'];die();
    }      
    unset($item);

    //if ($mybasketarray['products'][$index]['product_id']['id_product']==10063) {
    // print '<pre>'.$other_taxes_sum_value.' ';print_r($mybasketarray['products'][$index]);die();
    //}

    $fpa_static_total=false;
    if ($mybasketarray['price_is_xondriki']>=1) { //xondriki, yperxondriki
      $fpa_static_total = $GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_XONDRIKI;
    } else { //lianiki
      $fpa_static_total = $GKS_BASKET_PRICE_INCLUDE_FPA_STATIC_TOTAL_LIANIKI;
    }
    //echo '<pre>';var_dump($fpa_static_total);die();

    $user_product_price_check_fpa=false;
    if (isset($mybasketarray['products'][$index]['user_product_price_check_fpa']) and
        $mybasketarray['products'][$index]['user_product_price_check_fpa']==true and 
        isset($mybasketarray['products'][$index]['user_final_total'])) {
      $user_product_price_check_fpa=true;
      //echo '<pre>wwwwwwwwwww '."\n"; print_r($mybasketarray['products'][$index]);die();
    }

    //if ($user_product_price_check_fpa) $fpa_static_total=true;

          
    
    if (isset($fix_prices[$index])) {
      $value['product_price_start_peritem_db'] = $fix_prices[$index];
      //echo '<pre>ssssssssss';print_r($fix_prices);die();
    }
        
    $value['product_price_final_peritem_db']=$value['product_price_start_peritem_db'];

    //print '<pre>vvvvvvvvvv';print_r($value); die();
    //print '<pre>vvvvvvvvvv';print_r($mybasketarray['products'][$index]); die();
    


    
    $price_is_def_pricelist=true;
    //if (isset($fields_change[$index]) and isset($mybasketarray['products'][$index]['user_ekptosi']) and ($fields_change[$index]=='gks_ekptosi' or $fields_change[$index]=='gks_quantity' or $fields_change[$index]=='gks_sheets')) {
    //if (isset($fields_change[$index]) and isset($mybasketarray['products'][$index]['user_ekptosi']) and ($fields_change[$index]=='gks_ekptosi')) {
    //user_final_total
    //print '<pre>';print_r($fields_change); die();
    if ($value['id_product'] == 2) {
      //echo '<pre>ddddddddddddd ';print_r($mybasketarray['products'][$index]);print_r($value);die();
      
      $mybasketarray['products'][$index]['user_field_change']='gks_price';
      //$value['product_price_start_peritem_db']=$user_final_net=$mybasketarray['products'][$index]['user_final_net'];
      if (isset($mybasketarray['products'][$index]['user_final_net'])) {
        $value['product_price_start_all_net']=$mybasketarray['products'][$index]['user_final_net'];
        //echo '<pre>ddddddddddddd '.$value['product_price_start_all_net']."\n";print_r($mybasketarray['products'][$index]);print_r($value);die();
        $value['product_price_start_peritem_db']=$value['product_price_start_all_net'];
        if ($value['product_quantity']<>0) {
          $value['product_price_start_peritem_db']=$mybasketarray['products'][$index]['user_final_net']/$value['product_quantity'];
          
          //echo 'f';
          
        }
        
      } else {
        //echo '<pre>';print_r($mybasketarray['products'][$index]);die();
        
      }
      //echo '<pre>'.$value['product_price_include_vat'];die();
//      echo '<pre>';
//      echo $value['product_quantity']."\n";
//      echo $value['product_price_start_peritem_db']."\n";
//      echo $value['product_price_start_all_net']."\n";
//      die();
      
//      if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/1.txt',
//        'step1'."\n".
//        $value['product_price_include_vat']."\n".
//        $value['product_quantity']."\n".
//        $value['product_price_start_peritem_db']."\n".
//        $value['product_price_start_all_net']."\n".
//        $value['product_price_final_peritem_net']."\n".
//        $value['product_price_final_all_net']."\n".
//        $value['product_pricelist_item_percent']."\n".
//        time()."\n\n"
//      ,FILE_APPEND);
      
//      if (isset($mybasketarray['products'][$index]['user_ekptosi']) and $mybasketarray['products'][$index]['user_ekptosi']!=0 and $mybasketarray['products'][$index]['user_ekptosi']!=100) {
//        
//        //$value['product_price_start_peritem_db'] =$value['product_price_start_peritem_db'] - ($mybasketarray['products'][$index]['user_ekptosi']/100) * $value['product_price_start_peritem_db'];
//        //$value['product_price_start_all_net'] = $mybasketarray['products'][$index]['user_final_net'] / (1 + ($mybasketarray['products'][$index]['user_ekptosi']/100));
//        $value['product_price_start_all_net'] =(100 * $mybasketarray['products'][$index]['user_final_net'])/(100 - $mybasketarray['products'][$index]['user_ekptosi']);
//        $value['product_price_start_peritem_db']=$value['product_price_start_all_net'];
//        if ($value['product_quantity']<>0) {
//          $value['product_price_start_peritem_db']=$value['product_price_start_all_net']/$value['product_quantity'];
//        }
//      }
      
      
      $value['product_price_final_peritem_db']=$value['product_price_start_peritem_db'];
      $value['product_price_final_peritem_net']=$value['product_price_start_peritem_db'];
      $value['product_price_final_all_net']=$value['product_price_start_all_net'];
      
      
      //echo '<pre>ddddddddddddd '.$value['product_price_final_all_net']."\n";print_r($value);die();
      
//      if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/1.txt',
//        'step2'."\n".
//        $value['product_price_include_vat']."\n".
//        $value['product_quantity']."\n".
//        $value['product_price_start_peritem_db']."\n".
//        $value['product_price_start_all_net']."\n".
//        $value['product_price_final_peritem_net']."\n".
//        $value['product_price_final_all_net']."\n".
//        $value['product_pricelist_item_percent']."\n".
//        time()."\n\n"
//      ,FILE_APPEND);
      
      
      $price_is_def_pricelist = false;
    }
    
    if (isset($mybasketarray['products'][$index]['user_change_ekptosi_or_final_net']) and $mybasketarray['products'][$index]['user_change_ekptosi_or_final_net']!='') {

      //echo '<pre>'.$mybasketarray['products'][$index]['user_field_change'];die();
      
      if (($mybasketarray['products'][$index]['user_field_change']=='' and $mybasketarray['products'][$index]['user_change_ekptosi_or_final_net']=='gks_price') or 
          ($mybasketarray['products'][$index]['user_field_change']=='gks_price') or 
          ($mybasketarray['products'][$index]['user_field_change']=='gks_peritem_net')) { 

      //if ($mybasketarray['products'][$index]['user_field_change']=='gks_price') { //'gks_price'

        //print '<pre>';print_r($value); die();
        
        if (isset($mybasketarray['products'][$index]['user_final_net'])) {
          
          $user_final_net=$mybasketarray['products'][$index]['user_final_net'];
          
          
          
          if ($value['product_price_include_vat']==0) { //einai kathari i timi 
  
          } else {      // i timi periexei fpa
            if ($value['product_fpa_id_array']['fpa_pososto_fr'] == $value['product_fpa_id_array']['fpa_pososto']) {  //i proepilegmeni timi me ton proepipelgmeno fpa
              $temp=$user_final_net;
              $user_final_net=($user_final_net) * (1 + $value['product_fpa_id_array']['fpa_pososto'] + ($other_taxes_sum_pososto/100) ) + $other_taxes_sum_value;
//              print '<pre>'; 
//              print $other_taxes_sum_value; 
//              print "\n";
//              print $temp;
//              print "\n";
//              print $user_final_net;
//              print "\n";
//              print $value['product_price_final_peritem_db'];
//              die();              
            } else {    //ean exei allo fpa, diladi alli forologiki thesi 
              $temp=$user_final_net;
              //$user_final_net=($user_final_net) * (1 + $value['product_fpa_id_array']['fpa_pososto_fr'] + ($other_taxes_sum_pososto/100) + $other_taxes_sum_value);
              if ($fpa_static_total) {
                $user_final_net=($user_final_net) * (1 + $value['product_fpa_id_array']['fpa_pososto'] + ($other_taxes_sum_pososto/100) + $other_taxes_sum_value);  
              } else {
                $user_final_net=($user_final_net) * (1 + $value['product_fpa_id_array']['fpa_pososto_fr'] + ($other_taxes_sum_pososto/100) + $other_taxes_sum_value);
              }
              
              //$user_final_net=$user_final_net * (1 + $value['product_fpa_id_array']['fpa_pososto_fr']);
              //$user_final_net=$user_final_net / (1 + $value['product_fpa_id_array']['fpa_pososto']);
              
              
            //print '<pre>';print_r($value['product_fpa_id_array']); die();
              
//              print '<pre>'; 
//              //print $other_taxes_sum_value; 
//              //print "\n";
//              print $temp;
//              print "\n";
//              print $user_final_net;
//              print "\n";
//              print $value['product_price_final_peritem_db'];
//              die();
              
              
            }
          }
          
          
          if ($value['product_quantity']==0) {
            $value['product_price_final_peritem_db']=0;
          } else {
            //$value['product_price_final_peritem_db']=round($user_final_net/$value['product_quantity'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
            $value['product_price_final_peritem_db']=round($user_final_net/$value['product_quantity'],$GKS_BASKET_CALC_ITEM_DECIMAL);
          }
          //echo $value['product_price_final_peritem_db'];
          $price_is_def_pricelist = false;          
          
        }
        
      //} else if ($mybasketarray['products'][$index]['user_field_change']=='gks_price_final') { //price with fpa
      } else if (($mybasketarray['products'][$index]['user_field_change']=='' and $mybasketarray['products'][$index]['user_change_ekptosi_or_final_net']=='gks_price_final') or 
                 ($mybasketarray['products'][$index]['user_field_change']=='gks_price_final')) { 

        //print '<pre>sssssssssssss ';print_r($value); print '</pre>'; //die();

        if (isset($mybasketarray['products'][$index]['user_final_total'])) {
          $user_final_total=$mybasketarray['products'][$index]['user_final_total'];
          
          //print '<pre>sssssssssssss ';die();
          
          if ($value['product_price_include_vat']!=0) { //einai mikti i timi 
            //print '<pre>sssaaaaa '.$user_final_total.' '.$value['product_descr'].'|'.$value['product_price_include_vat'].'|'."\n";print_r($value);die();
            if ($value['product_fpa_id_array']['fpa_pososto_fr'] != $value['product_fpa_id_array']['fpa_pososto']) {  //i proepilegmeni timi me ton proepipelgmeno fpa            
              //if ($user_product_price_check_fpa==false) {   
                //print '<pre>1sssssssssssss '.$user_final_total.'|'; die();
                $user_final_total=($user_final_total) / (1 + $value['product_fpa_id_array']['fpa_pososto'] + ($other_taxes_sum_pososto/100) + $other_taxes_sum_value);
              //} else {
              //  $user_final_total=($user_final_total) / (1 + $value['product_fpa_id_array']['fpa_pososto'] + ($other_taxes_sum_pososto/100) + $other_taxes_sum_value);
              //}
            } 
          } else {      // i timi einai kathari
            if ($value['product_fpa_id_array']['fpa_pososto_fr'] == $value['product_fpa_id_array']['fpa_pososto']) {  //i proepilegmeni timi me ton proepipelgmeno fpa
              
              //print '<pre>sssssssssssssa|'.$user_product_price_check_fpa.'|'.$user_final_total.'|'.$other_taxes_sum_pososto;die();  
              
              $user_final_total=$user_final_total / (1 + $value['product_fpa_id_array']['fpa_pososto'] + ($other_taxes_sum_pososto/100) );
              //print '<pre>sssssssssssssa|'.$user_product_price_check_fpa.'|'.$user_final_total.'|'.$other_taxes_sum_pososto;die();  
              
            } else {    //ean exei allo fpa, diladi alli forologiki thesi 
              //print '<pre>sssssssssssssa|'.$user_product_price_check_fpa.'|'.$user_final_total.'|'.$other_taxes_sum_pososto;die();
              if ($user_product_price_check_fpa) {
                //$user_final_total=$user_final_total; // diladi tipota
                //print '<pre>sssssssssssssa|'.$user_final_total;die();
                //if ($fpa_static_total) {
                  $user_final_total=($user_final_total) / (1 + $value['product_fpa_id_array']['fpa_pososto'] + ($other_taxes_sum_pososto/100) + $other_taxes_sum_value);  
                //} else {
                  //$user_final_total=($user_final_total) / (1 + $value['product_fpa_id_array']['fpa_pososto_fr'] + ($other_taxes_sum_pososto/100) + $other_taxes_sum_value);
                //}                
                //print '<pre>sssssssssssssa|'.$user_final_total.'|'.$other_taxes_sum_pososto;die();
              } else {
                
                $user_final_total=$user_final_total / (1 + $value['product_fpa_id_array']['fpa_pososto_fr'] + ($other_taxes_sum_pososto/100) );
              }
            }
          } 
          

          if ($value['product_quantity']==0) {
            $value['product_price_final_peritem_db']=0;
          } else {
            $value['product_price_final_peritem_db']=round($user_final_total/$value['product_quantity'],$GKS_BASKET_CALC_ITEM_DECIMAL);
          }
          $price_is_def_pricelist = false;  
          //die('dddd');         
          //echo '<pre>bbbbb '.$value['product_price_final_peritem_db'];die();
          
          //echo '<pre>vvvvvvvvv ';print_r($value);die();
          
        }
        
      } else {
        //print '<pre>';print_r($value); die();
        
        if (isset($mybasketarray['products'][$index]['user_ekptosi'])) {
          $value['product_price_final_peritem_db'] =round($value['product_price_start_peritem_db'] - ($mybasketarray['products'][$index]['user_ekptosi']/100) * $value['product_price_start_peritem_db'],$GKS_BASKET_CALC_ITEM_DECIMAL);
          $price_is_def_pricelist = false;
          //print '<pre>sssssssssssss '.$user_final_total.'</pre>';
        }        
        
      }
      //gks_price gks_ekptosi
    }

    //print '<pre>dddddddd '.$value['product_price_start_peritem_db'].' ';print_r($value);die();
    
//    if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/1.txt',
//        'step3'."\n".
//        $value['product_price_include_vat']."\n".
//        $value['product_quantity']."\n".
//        $value['product_price_start_peritem_db']."\n".
//        $value['product_price_start_all_net']."\n".
//        $value['product_price_final_peritem_net']."\n".
//        $value['product_price_final_all_net']."\n".
//        $value['product_pricelist_item_percent']."\n".
//        time()."\n\n"
//    ,FILE_APPEND);    
    
    
    //print '<pre>';print_r($pricelist_items);die();
    if ($price_is_def_pricelist) {
      //print '<pre>';print_r($pricelist_items);die();
      foreach ($pricelist_items as $list) {
        $is_valid=true;
        //if ($list['pricelist_item_product_id'] > 0   and $list['pricelist_item_product_id']   != $value['id_product']) $is_valid=false;
        //if ($list['pricelist_item_category_id'] > 0  and $list['pricelist_item_category_id']  != $value['product_category_id']) $is_valid=false;
        if ($list['pricelist_item_min_posotita'] > 0 and $list['pricelist_item_min_posotita'] > $value['product_quantity']) $is_valid=false;
        //if ($list['pricelist_item_coupon'] !='' and (count($coupons)==0 or in_array($list['pricelist_item_coupon'],$coupons)==false) ) $is_valid=false;
        if ($list['pricelist_item_coupon'] !='' and (count($coupons)==0 or isset($coupons[$list['pricelist_item_coupon']]) == false) ) $is_valid=false;
       
        if ($is_valid) {
  				if (!empty($list['pricelist_item_price_eval']) and $list['pricelist_item_price_eval']!='') {
  					$eval_string= $list['pricelist_item_price_eval'];
  					$eval_string=substr($eval_string, 1, strlen($eval_string)-1);
  					$eval_string= str_replace('[[posotita]]', number_format($value['product_quantity'],10,'.',''), $eval_string);
  					$eval_string= str_replace('[[price]]', number_format($value['product_price_start_peritem_db'],10,'.',''), $eval_string);
  					$value['product_price_final_peritem_db'] = round(eval('return '.$eval_string.';'),$GKS_BASKET_CALC_ITEM_DECIMAL);
  				} else {
  	        $value['product_price_final_peritem_db']=round($value['product_price_start_peritem_db'] * (1+$list['pricelist_item_price_epi']) + $list['pricelist_item_price_plus'],$GKS_BASKET_CALC_ITEM_DECIMAL);
  	      }
          $value['product_pricelist_item_id']=$list['id_pricelist_item'];
          $value['product_pricelist_item_descr']=$list['pricelist_item_descr'];
          $value['product_price_coupon_use'] = $list['pricelist_item_coupon'];
          break;
        }
      }
    } else {
      foreach ($pricelist_items as $list) {
        $is_valid=true;
        //if ($list['pricelist_item_product_id'] > 0   and $list['pricelist_item_product_id']   != $value['id_product']) $is_valid=false;
        //if ($list['pricelist_item_category_id'] > 0  and $list['pricelist_item_category_id']  != $value['product_category_id']) $is_valid=false;
        if ($list['pricelist_item_min_posotita'] > 0 and $list['pricelist_item_min_posotita'] > $value['product_quantity']) $is_valid=false;
        //if ($list['pricelist_item_coupon'] !='' and (count($coupons)==0 or in_array($list['pricelist_item_coupon'],$coupons)==false) ) $is_valid=false;
        if ($list['pricelist_item_coupon'] !='' and (count($coupons)==0 or isset($coupons[$list['pricelist_item_coupon']]) == false) ) $is_valid=false;
       
        if ($is_valid) {
          $value['product_price_coupon_use_disabled'] = $list['pricelist_item_coupon'];
          break;
        }
      }
    }
    
    
    

//      if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/1.txt',
//        'step3.1'."\n".
//        $value['product_price_include_vat']."\n".
//        $value['product_quantity']."\n".
//        $value['product_price_start_peritem_db']."\n".
//        $value['product_price_start_all_net']."\n".
//        $value['product_price_final_peritem_net']."\n".
//        $value['product_price_final_all_net']."\n".
//        $value['product_pricelist_item_percent']."\n".
//        time()."\n\n"
//      ,FILE_APPEND);
    
    //print '<pre>'; print $other_taxes_sum_pososto;die();
    //print '<pre>'; print_r($mybasketarray['products'][$index]['other_taxes']);die();
    //print '<pre>'; print_r($mybasketarray['products'][$index]);die();
    
    //print '<pre>'; print_r($value['product_fpa_id_array']);die();
    
    if ($value['product_price_include_vat']==0) { //einai kathari i timi 
      

      //if (1==2 and $mybasketarray['products'][$index]['product_id']['id_product'] == 2) {
        
        //echo '<pre>ddddddddddddd '.$value['product_price_final_all_net']."\n";print_r($mybasketarray['products'][$index]);die();  
        //echo '<pre>ddddddddddddd '.$value['product_price_final_all_net'];
        
      //} else { 
        
          
        
        $value['product_price_start_peritem_net'] = $value['product_price_start_peritem_db'];
        $value['product_price_start_peritem_fpa'] = round($value['product_price_start_peritem_net'] * $value['product_fpa_id_array']['fpa_pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
    
        //echo $value['product_price_final_peritem_db'];
        $value['product_price_final_peritem_net'] = $value['product_price_final_peritem_db'];
        $value['product_price_final_peritem_fpa'] = round($value['product_price_final_peritem_net'] * $value['product_fpa_id_array']['fpa_pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      
        
      
        $value['product_price_start_peritem_total'] = round($value['product_price_start_peritem_net'] + $value['product_price_start_peritem_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $value['product_price_start_all_net'] = round($value['product_price_start_peritem_net'] * $value['product_quantity'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $value['product_price_start_all_fpa'] =  round($value['product_price_start_all_net'] * $value['product_fpa_id_array']['fpa_pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $value['product_price_start_all_total'] = round($value['product_price_start_all_net'] + $value['product_price_start_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
    
        $value['product_price_final_peritem_total'] = round($value['product_price_final_peritem_net'] +  $value['product_price_final_peritem_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $value['product_price_final_all_net'] = round($value['product_price_final_peritem_net'] * $value['product_quantity'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $value['product_price_final_all_fpa'] = round($value['product_price_final_all_net'] * $value['product_fpa_id_array']['fpa_pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $value['product_price_final_all_total'] = round($value['product_price_final_all_net'] + $value['product_price_final_all_fpa'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        
        //echo '<pre>'; print_r($value);die();
        //echo '<pre>'; print_r($mybasketarray['products'][$index]);die();
          if (isset($mybasketarray['products'][$index]['from_aade_import_user_fpa']) and $mybasketarray['products'][$index]['from_aade_import_user_fpa']==1) {
            if (isset($mybasketarray['products'][$index]['user_final_net'])) {
              $value['product_price_final_all_net']=$mybasketarray['products'][$index]['user_final_net'];
            }
            $value['product_price_final_all_fpa']=round(floatval($mybasketarray['products'][$index]['from_aade_import_user_fpa_value']),$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
            $value['product_price_final_all_total']=$value['product_price_final_all_net']+$value['product_price_final_all_fpa'];
            if ($value['product_quantity']!=0) {
              $value['product_price_final_peritem_fpa']=  $value['product_price_final_all_fpa']/$value['product_quantity'];
              $value['product_price_final_peritem_total']=$value['product_price_final_peritem_net']+$value['product_price_final_peritem_fpa'];
            } else {
              //echo '<pre>'; print_r($value);die();
              
            }
          }
      //}
      


      //print '<pre>';
      //print $value['product_price_final_all_net'];
      //die();
      
//      if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/1.txt',
//        'step3.2'."\n".
//        $value['product_price_include_vat']."\n".
//        $value['product_quantity']."\n".
//        $value['product_price_start_peritem_db']."\n".
//        $value['product_price_start_all_net']."\n".
//        $value['product_price_final_peritem_net']."\n".
//        $value['product_price_final_all_net']."\n".
//        $value['product_pricelist_item_percent']."\n".
//        time()."\n\n"
//      ,FILE_APPEND);
          
    } else {      // i timi periexei fpa
      //echo '<pre>'; print_r($value);die();
      //print '<pre>'; print_r($value['product_fpa_id_array']);die();
      //print '<pre>'; print_r($value);die();
      //print '<pre>'; print_r($mybasketarray);die();
      
      if ($fpa_static_total or $value['product_fpa_id_array']['fpa_pososto'] == $value['product_fpa_id_array']['fpa_pososto_fr']) {  
        //i proepilegmeni timi me ton proepipelgmeno fpa
        
        //print '<pre>'.$fpa_static_total.'</pre>';
        
        //echo '<pre>'; print_r($value);die();
        //if ($index==2) {echo '<pre>';print_r($value);die();}
        
        $value['product_price_start_all_total'] =  round($value['product_quantity'] * $value['product_price_start_peritem_db'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $value['product_price_start_all_net'] = round(($value['product_price_start_all_total']-$other_taxes_sum_value) / ( 1 + $value['product_fpa_id_array']['fpa_pososto'] + ($other_taxes_sum_pososto/100) ),$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $c_other_taxes_start_all=$other_taxes_sum_value + round($value['product_price_start_all_net'] * ($other_taxes_sum_pososto/100),$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $value['product_price_start_all_fpa'] = $value['product_price_start_all_total'] - $c_other_taxes_start_all - $value['product_price_start_all_net'];

//        print '<pre>'; 
//        print $value['product_price_start_all_total'];
//        print "\n";
//        print $value['product_price_start_all_net'];
//        print "\n";
//        print $c_other_taxes_start_all;
//        print "\n";
//        print $value['product_price_start_all_fpa'];
//        die();
        if ($value['product_quantity']!=0) $other_taxes_peritme_value=$other_taxes_sum_value/$value['product_quantity']; else $other_taxes_peritme_value=0;
        $value['product_price_start_peritem_total'] = $value['product_price_start_peritem_db'];
        $value['product_price_start_peritem_net'] = round(($value['product_price_start_peritem_db']-$other_taxes_peritme_value) / ( 1 + $value['product_fpa_id_array']['fpa_pososto'] + ($other_taxes_sum_pososto/100) ),$GKS_BASKET_CALC_ITEM_DECIMAL);
        $c_other_taxes_start_peritem=$other_taxes_peritme_value + round($value['product_price_start_peritem_net'] * ($other_taxes_sum_pososto/100),$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $value['product_price_start_peritem_fpa'] = $value['product_price_start_peritem_total'] - $c_other_taxes_start_peritem - $value['product_price_start_peritem_net'];
//        print '<pre>'; 
//        print $other_taxes_peritme_value;
//        print "\n";
//        print $value['product_price_start_peritem_net'];
//        print "\n";
//        print $c_other_taxes_start_peritem;
//        print "\n";
//        print $value['product_price_start_peritem_fpa'];
//        die();
        $value['product_price_final_all_total'] =  round($value['product_quantity'] * $value['product_price_final_peritem_db'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $value['product_price_final_all_net'] = ($value['product_price_final_all_total']-$other_taxes_sum_value) / ( 1 + $value['product_fpa_id_array']['fpa_pososto'] + ($other_taxes_sum_pososto/100));
        
        $value['product_price_final_all_net']=floor($value['product_price_final_all_net']*pow(10,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL))/pow(10,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        
        //echo '<pre>'.$GKS_BASKET_CALC_ITEM_DECIMAL.'|'.$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL."\n";print_r($value);die(); 
        
        
        $c_other_taxes_final_all=round($other_taxes_sum_value + $value['product_price_final_all_net'] * ($other_taxes_sum_pososto/100),$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $value['product_price_final_all_fpa'] = round($value['product_price_final_all_total'] - $c_other_taxes_final_all - $value['product_price_final_all_net'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        
        
        
//        if (isset($mybasketarray['products'][$index]['from_aade_import_user_fpa']) and $mybasketarray['products'][$index]['from_aade_import_user_fpa']==1) {
//          $value['product_price_final_all_fpa']=round(floatval($mybasketarray['products'][$index]['from_aade_import_user_fpa_value']),$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
//          //$value['product_price_final_all_net']=$value['product_price_final_all_total']-$value['product_price_final_all_fpa'];
//          
//          $value['product_price_final_all_total']=$value['product_price_final_all_net']+$value['product_price_final_all_fpa'];
//        }
        //echo '<pre>'.$value['product_price_final_all_total'].' '.$value['product_quantity'].' '.$value['product_price_final_peritem_db'];die();
        
//        print '<pre>sssssssssssss';
//        print "\n";
//        print $value['product_price_final_peritem_db'];
//        print "\n";
//        print $value['product_price_final_all_total'];
//        print "\n";
//        print $value['product_price_final_all_net'];
//        print "\n";
//        print $c_other_taxes_final_all;
//        print "\n";
//        print $value['product_price_final_all_fpa'];
//        print "\n";
//        print_r($value);
//        print '</pre>'; 
//        die();



        $value['product_price_final_peritem_total'] = round($value['product_price_final_peritem_db'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $value['product_price_final_peritem_net'] = round(($value['product_price_final_peritem_db']-$other_taxes_peritme_value) / ( 1 + $value['product_fpa_id_array']['fpa_pososto'] + ($other_taxes_sum_pososto/100)),$GKS_BASKET_CALC_ITEM_DECIMAL);
        $c_other_taxes_final_peritem=round($other_taxes_peritme_value + $value['product_price_final_peritem_net'] * ($other_taxes_sum_pososto/100),$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $value['product_price_final_peritem_fpa'] = $value['product_price_final_peritem_total'] - $c_other_taxes_final_peritem - $value['product_price_final_peritem_net'];

//        if (isset($mybasketarray['products'][$index]['from_aade_import_user_fpa']) and $mybasketarray['products'][$index]['from_aade_import_user_fpa']==1) {
//          if ($value['product_quantity']!=0) {
//            //echo '<pre>';echo $value['product_quantity'];die();
//            $value['product_price_final_peritem_net']=  $value['product_price_final_all_net']/$value['product_quantity'];
//            $value['product_price_final_peritem_fpa']=  $value['product_price_final_all_fpa']/$value['product_quantity'];
//            $value['product_price_final_peritem_total']=$value['product_price_final_all_total']/$value['product_quantity'];
//            
//            
//          }
//        }


        //echo '<pre>qqqqqqqqqqqqqqqq'; print_r($mybasketarray['products'][$index]);die();
        
        if (isset($mybasketarray['products'][$index]['from_aade_import_user_fpa']) and $mybasketarray['products'][$index]['from_aade_import_user_fpa']==1) {
          if (isset($mybasketarray['products'][$index]['user_final_net'])) {
            $value['product_price_final_all_net']=$mybasketarray['products'][$index]['user_final_net'];
          }
          $value['product_price_final_all_fpa']=round(floatval($mybasketarray['products'][$index]['from_aade_import_user_fpa_value']),$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
          $value['product_price_final_all_total']=$value['product_price_final_all_net']+$value['product_price_final_all_fpa'];
          
          if ($value['product_quantity']!=0) {
            $value['product_price_final_peritem_fpa']=  $value['product_price_final_all_fpa']/$value['product_quantity'];
            $value['product_price_final_peritem_total']=$value['product_price_final_peritem_net']+$value['product_price_final_peritem_fpa'];
            //echo '<pre>'; print_r($value);die();
          } else {
            //echo '<pre>'; print_r($value);die();
          }
        }
      
        //echo '<pre>';print_r($value);die();
        //echo '<pre>'; echo print_r($value);die();

//        print '<pre>sssssssssss'; 
//        print $value['product_price_final_peritem_total'];
//        print "\n";
//        print $value['product_price_final_peritem_net'];
//        print "\n";
//        print $c_other_taxes_final_peritem;
//        print "\n";
//        print $value['product_price_final_peritem_fpa'];
//        print '</pre>';
        
//        die();
        
      } else if ($value['product_fpa_id_array']['fpa_pososto'] != $value['product_fpa_id_array']['fpa_pososto_fr']) {   
        //ean exei allo fpa, diladi alli forologiki thesi 

        if ($user_product_price_check_fpa) {
          $value['product_price_final_peritem_db']=$value['product_price_final_peritem_db']*(1+$value['product_fpa_id_array']['fpa_pososto_fr']);
        }
        
        
        //echo '<pre>vvvvvvvvvvv ';print_r($value);die();
        //echo '<pre>ddddd|'.$fpa_static_total.'|'.$user_product_price_check_fpa.'|'.$value['product_price_final_peritem_db'].'|';die();
        

        if ($value['product_quantity']!=0) $other_taxes_peritme_value=$other_taxes_sum_value/$value['product_quantity']; else $other_taxes_peritme_value=0;
        
        
        //echo '<pre>'.$value['product_price_start_peritem_db'];die();
        $value['product_price_start_peritem_net'] = round(($value['product_price_start_peritem_db']-$other_taxes_peritme_value)/(1 + $value['product_fpa_id_array']['fpa_pososto_fr'] + ($other_taxes_sum_pososto/100) ),$GKS_BASKET_CALC_ITEM_DECIMAL);
        $value['product_price_start_peritem_fpa'] = round($value['product_price_start_peritem_net'] * $value['product_fpa_id_array']['fpa_pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        
        //print '<pre>aaaaaaaaaaaaaaaa ';print_r($value);die();
        //echo '<pre>ccccccccccc '.$value['product_fpa_id_array']['fpa_pososto_fr'];die();
        
        $value['product_price_final_peritem_net'] = round(($value['product_price_final_peritem_db'] -$other_taxes_peritme_value)/(1+$value['product_fpa_id_array']['fpa_pososto_fr'] + ($other_taxes_sum_pososto/100)),$GKS_BASKET_CALC_ITEM_DECIMAL);
        //echo '<pre>bbbbbbbbbbbb '.$value['product_price_final_peritem_net'];die();
        
        
        $value['product_price_final_peritem_fpa'] = round($value['product_price_final_peritem_net'] * $value['product_fpa_id_array']['fpa_pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $c_other_taxes_start_peritem=round($other_taxes_peritme_value + $value['product_price_start_peritem_net'] * ($other_taxes_sum_pososto/100),$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $value['product_price_start_peritem_total'] = round($value['product_price_start_peritem_net'] + $value['product_price_start_peritem_fpa'] + $c_other_taxes_start_peritem,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        
        
        $value['product_price_start_all_net'] = round($value['product_price_start_peritem_net'] * $value['product_quantity'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $value['product_price_start_all_fpa'] =  round($value['product_price_start_all_net'] * $value['product_fpa_id_array']['fpa_pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $c_other_taxes_start_all=round($other_taxes_sum_value + $value['product_price_start_all_net'] * ($other_taxes_sum_pososto/100),$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $value['product_price_start_all_total'] = round($value['product_price_start_all_net'] + $value['product_price_start_all_fpa'] + $c_other_taxes_start_all,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        
        
        $c_other_taxes_final_peritem=round($other_taxes_peritme_value + $value['product_price_final_peritem_net'] * ($other_taxes_sum_pososto/100),$GKS_BASKET_CALC_ITEM_DECIMAL);
        $value['product_price_final_peritem_total'] = round($value['product_price_final_peritem_net'] +  $value['product_price_final_peritem_fpa'] + $c_other_taxes_final_peritem,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        
        $value['product_price_final_all_net'] = $value['product_price_final_peritem_net'] * $value['product_quantity'];
        $value['product_price_final_all_net'] = floor($value['product_price_final_all_net']*pow(10,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL))/pow(10,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);


        
        $value['product_price_final_all_fpa'] = round($value['product_price_final_all_net'] * $value['product_fpa_id_array']['fpa_pososto'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $c_other_taxes_final_all=round($other_taxes_sum_value + $value['product_price_final_all_net'] * ($other_taxes_sum_pososto/100),$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        $value['product_price_final_all_total'] = round($value['product_price_final_all_net'] + $value['product_price_final_all_fpa'] + $c_other_taxes_final_all,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        
        //echo '<pre>'.$value['product_price_final_all_net'].'|'.$value['product_price_final_all_fpa'].'|'.$c_other_taxes_final_all;die();
        //echo '<pre>'; print_r($value);die();
        if (isset($mybasketarray['products'][$index]['from_aade_import_user_fpa']) and $mybasketarray['products'][$index]['from_aade_import_user_fpa']==1) {
          if (isset($mybasketarray['products'][$index]['user_final_net'])) {
            $value['product_price_final_all_net']=$mybasketarray['products'][$index]['user_final_net'];
          }          
          $value['product_price_final_all_fpa']=round(floatval($mybasketarray['products'][$index]['from_aade_import_user_fpa_value']),$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
          $value['product_price_final_all_total']=$value['product_price_final_all_net']+$value['product_price_final_all_fpa'];
          if ($value['product_quantity']!=0) {
            $value['product_price_final_peritem_fpa']=  $value['product_price_final_all_fpa']/$value['product_quantity'];
            $value['product_price_final_peritem_total']=$value['product_price_final_peritem_net']+$value['product_price_final_peritem_fpa'];
          } else {
            //echo '<pre>'; print_r($value);die();
          }
        }
                
//        print '<pre>'; 
//        print $other_taxes_peritme_value;
//        print "\n";
//        print $other_taxes_sum_value;
//        print "\n";
//        print $value['product_price_start_peritem_db'];
//        print "\n";
//        print $value['product_price_start_peritem_net'];
//        print "\n";
//        print $value['product_price_start_peritem_fpa'];
//        print "\n";
//        print $value['product_price_final_peritem_net'];
//        print "\n";
//        print $value['product_price_final_peritem_fpa'];
//        print "\n";
//        print $value['product_price_start_peritem_total'];
//        print "\n";
//        print $value['product_price_start_all_net'];
//        print "\n";
//        print $value['product_price_start_all_fpa'];
//        print "\n";
//        print $value['product_price_start_all_total'];
//        print "\n";
//        print $c_other_taxes_final_peritem;
//        print "\n";
//        print $value['product_price_final_peritem_total'];
//        print "\n";
//        print $value['product_price_final_all_net'];
//        print "\n";
//        print $value['product_price_final_all_fpa'];
//        print "\n";
//        print $value['product_price_final_all_total'];
//        die();


        
      
    
        
        
      }
    }

//    if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/1.txt',
//        'step4'."\n".
//        $value['product_price_include_vat']."\n".
//        $value['product_quantity']."\n".
//        $value['product_price_start_peritem_db']."\n".
//        $value['product_price_start_all_net']."\n".
//        $value['product_price_final_peritem_net']."\n".
//        $value['product_price_final_all_net']."\n".
//        $value['product_pricelist_item_percent']."\n".
//        time()."\n\n"
//    ,FILE_APPEND);
    
    //print '<pre>'; print_r($value);die();


    if ($value['product_price_start_all_net']!=0 and $value['product_price_include_vat']==0) { // einai kathari i timi
      $value['product_pricelist_item_percent'] = ($value['product_price_start_all_net'] - $value['product_price_final_all_net'])*100 /$value['product_price_start_all_net'];
      //print '<pre>'; print $value['product_pricelist_item_percent']; die();
    } else if ($value['product_price_start_all_total']!=0 and $value['product_price_include_vat']!=0) {
      $value['product_pricelist_item_percent'] = ($value['product_price_start_all_total'] - $value['product_price_final_all_total'])*100 /$value['product_price_start_all_total'];
//      print '<pre>dddddddddddd'; 
//      print $value['product_pricelist_item_percent']; 
//      print "\n";
//      print $value['product_price_start_all_total'];
//      print "\n";
//      print $value['product_price_final_all_total'];
//      print '</pre>'; 
//      die();
    } else {
      $value['product_pricelist_item_percent']=0;
    }
    
//    if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/1.txt',
//        'step5'."\n".
//        $value['product_price_include_vat']."\n".
//        $value['product_quantity']."\n".
//        $value['product_price_start_peritem_db']."\n".
//        $value['product_price_start_all_net']."\n".
//        $value['product_price_final_peritem_net']."\n".
//        $value['product_price_final_all_net']."\n".
//        $value['product_pricelist_item_percent']."\n".
//        time()."\n\n"
//    ,FILE_APPEND);    
    
    $item=&$mybasketarray['products'][$index];
    
    if (isset($item['other_taxes'])) {
      $other_taxes_tooltip='';
      if (isset($item['other_taxes']['withheldPercentCategory']) and $item['other_taxes']['withheldPercentCategory']>0 and isset($item['other_taxes']['withheldAmount']) and isset($tkat1['aade_katigoria_parakratoumemenon_foron_descr'])) {
        if ($tkat1['aade_katigoria_parakratoumemenon_foron_type']=='pososto') {
          $item['other_taxes']['withheldAmount']=round(
            $value['product_price_final_all_net']*($tkat1['aade_katigoria_parakratoumemenon_foron_pososto']/100), $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        }
        if ($item['other_taxes']['withheldAmount']!=0) {
          $other_taxes_tooltip.='<tr><td scope="row" nowrap style="text-align:left;">'.gks_lang('Φόροι Παρακρατούμενοι').'</th><td nowrap style="text-align:left;">'.$tkat1['aade_katigoria_parakratoumemenon_foron_descr'].'</td><td nowrap style="text-align:right;">'.myCurrencyFormat($item['other_taxes']['withheldAmount']).'</td></tr>';
        }
      }      
      if (isset($item['other_taxes']['otherTaxesPercentCategory']) and $item['other_taxes']['otherTaxesPercentCategory']>0 and isset($item['other_taxes']['otherTaxesAmount']) and isset($tkat2['aade_katigoria_loipon_foron_descr'])) {
        if ($tkat2['aade_katigoria_loipon_foron_type']=='pososto') {
          $item['other_taxes']['otherTaxesAmount']=round(
            $value['product_price_final_all_net']*($tkat2['aade_katigoria_loipon_foron_pososto']/100), $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        }
        if ($item['other_taxes']['otherTaxesAmount']!=0) {
          $other_taxes_tooltip.='<tr><td scope="row" nowrap style="text-align:left;">'.gks_lang('Λοιποί Φόροι').'</th><td nowrap style="text-align:left;">'.$tkat2['aade_katigoria_loipon_foron_descr'].'</td><td nowrap style="text-align:right;">'.myCurrencyFormat($item['other_taxes']['otherTaxesAmount']).'</td></tr>';
        }
      }
      
      
      //echo '<pre>';print_r($tkat3);die();

      if (isset($item['other_taxes']['stampDutyPercentCategory']) and $item['other_taxes']['stampDutyPercentCategory']>0 and isset($item['other_taxes']['stampDutyAmount']) and isset($tkat3['aade_katigoria_xartosimou_descr'])) {
        if ($tkat3['aade_katigoria_xartosimou_type']=='pososto') {
          $item['other_taxes']['stampDutyAmount']=round(
            $value['product_price_final_all_net']*($tkat3['aade_katigoria_xartosimou_pososto']/100), $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        }
        if ($item['other_taxes']['stampDutyAmount']!=0) {
          $other_taxes_tooltip.='<tr><td scope="row" nowrap style="text-align:left;">'.gks_lang('Ψηφιακό Τέλος συναλλαγής').'</th><td nowrap style="text-align:left;">'.$tkat3['aade_katigoria_xartosimou_descr'].'</td><td nowrap style="text-align:right;">'.myCurrencyFormat($item['other_taxes']['stampDutyAmount']).'</td></tr>';
        }
      }
      
      
//      if (isset($item['other_taxes']['stampDutyPercentCategory']) and $item['other_taxes']['stampDutyPercentCategory']>0 and isset($item['other_taxes']['stampDutyAmount']) and isset($tkat3['aade_katigoria_xartosimou_descr'])) {
//        $item['other_taxes']['stampDutyAmount']=round(
//          $value['product_price_final_all_net']*($tkat3['aade_katigoria_xartosimou_pososto']/100), $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
//        if ($item['other_taxes']['stampDutyAmount']!=0) {
//          $other_taxes_tooltip.='<tr><td scope="row" nowrap style="text-align:left;">'.gks_lang('Ψηφιακό Τέλος συναλλαγής').'</th><td nowrap style="text-align:left;">'.$tkat3['aade_katigoria_xartosimou_descr'].'</td><td nowrap style="text-align:right;">'.myCurrencyFormat($item['other_taxes']['stampDutyAmount']).'</td></tr>';
//        }
//      }
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      
      if (isset($item['other_taxes']['feesPercentCategory']) and $item['other_taxes']['feesPercentCategory']>0 and isset($item['other_taxes']['feesAmount']) and isset($tkat4['aade_katigoria_telon_descr'])) {
        if ($tkat4['aade_katigoria_telon_type']=='pososto') {
          $item['other_taxes']['feesAmount'] = round(
            $value['product_price_final_all_net']*($tkat4['aade_katigoria_telon_pososto']/100), $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        }
        if ($item['other_taxes']['feesAmount']!=0) {
          $other_taxes_tooltip.='<tr><td scope="row" nowrap style="text-align:left;">'.gks_lang('Τέλη').'</th><td nowrap style="text-align:left;">'.$tkat4['aade_katigoria_telon_descr'].'</td><td nowrap style="text-align:right;">'.myCurrencyFormat($item['other_taxes']['feesAmount']).'</td></tr>';
        }
      }
      
      $item['other_taxes']['deductionsAmount']=floatval($item['other_taxes']['deductionsAmount']);
      $item['other_taxes']['deductionsText']='';
      if (isset($item['other_taxes']['deductionsSelection']) and trim_gks($item['other_taxes']['deductionsSelection'])!='') {
        $deductionsText=[];
        $parts_dds=explode(']][[',$item['other_taxes']['deductionsSelection']);
        $sum_ddsa=0;
        foreach ($parts_dds as $item_dds) {
          switch ($item_dds) {   
            case 'MARKETING':
            
              $temp_1=round($value['product_price_final_all_net']*(0.2),2);
              $temp_2=round($value['product_price_final_all_net']*(0.036),2);
              $temp_3=$value['product_price_final_all_net']-$temp_1-$temp_2;
              $temp_4=round($temp_3*0.1333,2);
              $temp_5=round($temp_3*0.0695,2);
              $item_dds_amount=round($temp_2+$temp_4+$temp_5,2);
              $sum_ddsa+=$item_dds_amount;
              
              $deductionsText[]='Κρατήσεις: ΨΗΦΙΑΚΟ ΤΕΛΟΣ ΣΥΝΑΛΛΑΓΗΣ (3.6%)'."\r\n".
                                'Κρατήσεις: ΥΠΕΡ ΕΦΚΑ ΓΙΑ ΚΥΡΙΑ ΣΥΝΤΑΞΗ (13,33%)'."\r\n".
                                'Κρατήσεις: ΥΠΕΡ ΕΦΚΑ ΓΙΑ ΥΓΕΙΟΝΟΜΙΚΗ ΠΕΡΙΘΑΛΨΗ (6,95%)';
              
              break;  
            case 'test':
              //$sum_ddsa+=1;
              break;
          }
        } 
        $item['other_taxes']['deductionsAmount']=$sum_ddsa;
        $item['other_taxes']['deductionsText']=implode("\r\n",$deductionsText);
      }
      
      if ($other_taxes_tooltip!='') {
        $other_taxes_tooltip=
        '<table class="table table-sm table-responsive1 table-striped table-bordered" style="font-size:0.8rem;width:100px;margin-bottom:0px;" border="0" cellspacing="0" cellpadding="5" align="center">
        <tbody>'.
        $other_taxes_tooltip.
        '</tbody></table>';
      }
      $item['other_taxes_tooltip']=$other_taxes_tooltip;
      
//      if ($mybasketarray['products'][$index]['product_id']['id_product']==10063) {
//       print '<pre>'.$other_taxes_sum_value.' ';print_r($mybasketarray['products'][$index]);die();
//      }
      
      //echo 'gggg '.$value['product_price_include_vat'].' '.$user_final_total.' '.$GKS_BASKET_ROUND_DIAFORA_001;die();
      
      //an to teliko sinolo exei diafora 0.01 to na allaksei gia ligo i net timi gia na bgei to sinolo opos exei oristei sto eidos
      
      //strolopoisi fpa

      
//      if (($mybasketarray['products'][$index]['user_field_change']=='' and $mybasketarray['products'][$index]['user_change_ekptosi_or_final_net']=='gks_price_final') or 
//                 ($mybasketarray['products'][$index]['user_field_change']=='gks_price_final')) { 
//        $diafora= $mybasketarray['products'][$index]['user_final_total']-$value['product_price_final_all_total'];         
//        if ($diafora!=0 and abs($diafora)<0.02) {
//        
//          echo '<pre>'.$diafora.'|'.
//          $mybasketarray['products'][$index]['user_final_total'].'|'.
//          $value['product_price_final_all_total'].'|'.
//          $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL."\n"; print_r($mybasketarray['products'][$index]);die();
//        }
//        
//                    
//      }
      
      $value['product_price_final_all_total'] =round($value['product_price_final_all_total'], $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      $value['product_price_final_all_net']   =round($value['product_price_final_all_net'], $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      $value['product_price_final_all_fpa']   =round($value['product_price_final_all_fpa'], $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      $item['other_taxes']['otherTaxesAmount']=round($item['other_taxes']['otherTaxesAmount'],  $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      $item['other_taxes']['stampDutyAmount'] =round($item['other_taxes']['stampDutyAmount'], $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      $item['other_taxes']['feesAmount']      =round($item['other_taxes']['feesAmount'], $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
      
      
      

//      $user_product_price_check_fpa=false;
//      if (isset($mybasketarray['products'][$index]['user_product_price_check_fpa']) and
//          $mybasketarray['products'][$index]['user_product_price_check_fpa']==true and 
//          isset($mybasketarray['products'][$index]['user_final_total'])) {
//        $user_product_price_check_fpa=true;
//        //echo '<pre>wwwwwwwwwww '."\n"; print_r($mybasketarray['products'][$index]);die();
//      }
      
      //
      $check_this=true;
      if ($GKS_BASKET_ROUND_DIAFORA_001==false) {
        //$check_this=false;
      }
      if ($check_this) {
        if ($mybasketarray['products'][$index]['product_id']['id_product']==2) {
          if ($user_product_price_check_fpa==false) {
            $check_this=false;
          }
        } 
      }
      if ($check_this) {
        if (!($value['product_price_include_vat']!=0 or $user_product_price_check_fpa)) {
          $check_this=false;
        }
      }
      
      //print '<pre>'.$check_this.'|';print_r($mybasketarray['products'][$index]);die(); 
            
      if ($check_this) { // i timi periexei fpa
        if ($mybasketarray['products'][$index]['product_id']['id_product']== 2) {
          if ($user_product_price_check_fpa==false) {
            $calc_result=$value['product_price_final_all_net'] + 
                         $value['product_price_final_all_fpa'] +
                         $item['other_taxes']['otherTaxesAmount'] +
                         $item['other_taxes']['stampDutyAmount'] +
                         $item['other_taxes']['feesAmount'];            
          } else {
            $calc_result=$mybasketarray['products'][$index]['user_final_total'];
          }
          
        } else {
          
          if ($value['product_price_include_vat']!=0) {
            $calc_result=$value['product_price_final_all_net'] + 
                         $value['product_price_final_all_fpa'] +
                         $item['other_taxes']['otherTaxesAmount'] +
                         $item['other_taxes']['stampDutyAmount'] +
                         $item['other_taxes']['feesAmount'];
          } else {
            $calc_result=$mybasketarray['products'][$index]['user_final_total'];
          }
        }
        $calc_diafora = round($value['product_price_final_all_total']-$calc_result,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
        
//        print '<pre>'; 
//        print $calc_diafora.''; 
//        print "\n";
//        print $calc_result; 
//        print "\n";
//        print $value['product_price_final_all_total']; 
//        print "\n";
//        print $value['product_price_final_all_net']; 
//        print "\n";
//        print $value['product_price_final_all_fpa']; 
//        die();

        if (abs($calc_diafora)<=(1/(pow(10,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL)))) { // diafora eos 0.02
          
          
          
          //if ($calc_diafora>0 and $value['product_price_final_all_fpa']!=0) {
          if ($value['product_price_include_vat']!=0 and $mybasketarray['products'][$index]['product_id']['id_product']!=2) {
            //i diafora 0.01 na paei sto FPA gia na min yparxei thema me to kratos
            
//            print '<pre>'; 
//            print $calc_diafora.''; 
//            print "\n";
//            print $calc_result; 
//            print "\n";
//            print $value['product_price_final_all_total']; 
//            print "\n";
//            print $value['product_price_final_all_net']; 
//            print "\n";
//            print $value['product_price_final_all_fpa']; 
//            die();           
            
            
            //echo '<pre>'."\n"; print_r($mybasketarray['products'][$index]);die();
            if ($calc_diafora<0 and $value['product_price_final_all_fpa']!=0) {
              $value['product_price_final_all_fpa']=round($value['product_price_final_all_fpa'] + $calc_diafora,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
            } else { // i diafora na paei sto katahria ajia, as xasoume emeis
              $value['product_price_final_all_net']=round($value['product_price_final_all_net'] + $calc_diafora,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
            }            
            
          } else {
            if ($calc_diafora<0 and $value['product_price_final_all_fpa']!=0) {
              $value['product_price_final_all_fpa']=round($value['product_price_final_all_fpa'] - $calc_diafora,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
            } else {
              $value['product_price_final_all_net']=round($value['product_price_final_all_net'] - $calc_diafora,$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL);
            }            
            
          }
           
          

//          print '<pre>'; 
//          print number_format($value['product_price_final_all_total'],20); 
//          print "\n";
//          print $value['product_price_final_all_total']; 
//          print "\n";
//          print number_format($calc_result,20); 
//          print "\n";
//          print $calc_result; 
//          print "\n";
//          print $calc_diafora; 
//          print "\n";
//          print number_format($calc_diafora,20); 
//          die();
        }

//print '<pre>';
//print $value['product_price_final_all_fpa'];
//die();


        
      }
//print '<pre>';
//print $value['product_price_final_all_fpa'];
//die();
      
      //print '<pre>';print_r($item);die();  
    }
    unset($item);
    
  }
  unset($value);
  
  //echo '<pre>';print_r($myproducts);die();
  //die();
      
  
  return $myproducts;
}
