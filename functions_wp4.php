<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/





function getObjectRels($objname, $id) {
  global $db_link;
  global $leads_status;
  global $tasks_status;
  
  
  $sql_rel="select id_object_rel,object_name1,object_id1,object_name2,object_id2 
  from gks_object_rel
  where (object_name1='".$objname."' and object_id1=".$id.") or (object_name2='".$objname."' and object_id2=".$id.")
  order by id_object_rel desc";
  $result_rel = $db_link->query($sql_rel);        
  if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
  $object_rel=array();
  while ($row_rel = $result_rel->fetch_assoc()) {
    if ($row_rel['object_name1']==$objname and $row_rel['object_id1']==$id) {
      $object_rel[]=array('id_object_rel'=>$row_rel['id_object_rel'],'name' => $row_rel['object_name2'],'id' => $row_rel['object_id2']);
    } else {
      $object_rel[]=array('id_object_rel'=>$row_rel['id_object_rel'],'name' => $row_rel['object_name1'],'id' => $row_rel['object_id1']);
    }
  }
  //print '<pre>';print_r($object_rel);die();

  //i seira na einai opos gks_crm_activity_objects me order to crm_activity_object_sortorder




  //gks_assets
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_assets') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT *        
    FROM gks_assets
    WHERE gks_assets.id_asset in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_assets' and $objv['id']==$row_rel['id_asset']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Πάγιο'),
            'link' => '<a href="admin-assets-item.php?id='.$row_rel['id_asset'].'">#'.$row_rel['id_asset'].'</a>',
            'oname' => trim_gks($row_rel['asset_title']),
            'state' => '<img src="img/'.($row_rel['asset_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }
  
  //gks_assets_type
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_assets_type') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT *     
    FROM gks_assets_type
    WHERE id_asset_type in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_assets_type' and $objv['id']==$row_rel['id_asset_type']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Τύπος Παγίου'),
            'link' => '<a href="admin-assets-type-item.php?id='.$row_rel['id_asset_type'].'">#'.$row_rel['id_asset_type'].'</a>',
            'oname' => trim_gks($row_rel['asset_type_descr']),
            'state' => '<img src="img/'.($row_rel['asset_type_disabled']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }

  //gks_assets_service
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_assets_service') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT gks_assets_service.*, asset_title       
    FROM gks_assets_service
    LEFT JOIN gks_assets ON gks_assets_service.asset_id = gks_assets.id_asset
    WHERE gks_assets_service.id_assets_service in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_assets_service' and $objv['id']==$row_rel['id_assets_service']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Service Παγίου'),
            'link' => '<a href="admin-assets-service-item.php?id='.$row_rel['id_assets_service'].'">#'.$row_rel['id_assets_service'].'</a>',
            'oname' => trim_gks($row_rel['asset_title']),
            'state' => '<img src="img/'.($row_rel['isconfirm']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => ($row_rel['ajia']==0 ? '' : myCurrencyFormat($row_rel['ajia'])),
            'date' => showDate(strtotime($row_rel['mydate_send']), 'd/m/Y\<\b\r\>H:i:s', 1),
            'balance' => '',
          );
          break;
        }
      }
    }
  }
  
  //gks_assets_service_reasons
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_assets_service_reasons') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT *    
    FROM gks_assets_service_reasons
    WHERE id_assets_service_reasons in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_assets_service_reasons' and $objv['id']==$row_rel['id_assets_service_reasons']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Αιτία Service Παγίου'),
            'link' => '<a href="admin-assets-service-reasons-item.php?id='.$row_rel['id_assets_service_reasons'].'">#'.$row_rel['id_assets_service_reasons'].'</a>',
            'oname' => trim_gks($row_rel['reasons_descr']),
            'state' => '<img src="img/'.($row_rel['assets_service_reason_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }


  //gks_assets_whi_mov
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_assets_whi_mov') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT *    
    FROM gks_assets_whi_mov
    WHERE id_assets_whi_mov in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_assets_whi_mov' and $objv['id']==$row_rel['id_assets_whi_mov']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Απογραφή Παγίων'),
            'link' => '<a href="admin-assets-whi-mov-item.php?id='.$row_rel['id_assets_whi_mov'].'">#'.$row_rel['id_assets_whi_mov'].'</a>',
            'oname' => '',
            'state' => get_assets_whi_mov_descr($row_rel['assets_whi_mov_status']),
            'price' => '',
            'date' => showDate(strtotime($row_rel['mydate']), 'd/m/Y\<\b\r\>H:i:s', 1),
            'balance' => '',
          );
          break;
        }
      }
    }
  }
      
  //gks_acc_inv
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_acc_inv') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT gks_acc_inv.id_acc_inv, gks_acc_inv.inv_state, gks_acc_inv.gks_price_net,
    gks_acc_inv.inv_date,
    gks_acc_journal.acc_journal_code, gks_acc_journal.acc_journal_descr, 
    gks_acc_seires.seira_code, gks_acc_seires.seira_descr, gks_acc_inv.inv_acc_number_int,
    CASE
      WHEN (inv_state='080listing' or inv_state='090ekdosi' or inv_state='100payment') and affect_balance=1
        THEN affect_balance_pros * affect_balance_poso
      ELSE 0
    END as affect_balance_calc          
    FROM (gks_acc_inv 
    LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira
    WHERE gks_acc_inv.id_acc_inv in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_acc_inv' and $objv['id']==$row_rel['id_acc_inv']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Παραστατικό'),
            'link' => '<a href="admin-acc-inv-item.php?id='.$row_rel['id_acc_inv'].'">#'.$row_rel['id_acc_inv'].'</a>',
            'oname' => $row_rel['acc_journal_code'].'/'.$row_rel['seira_code'].'/'.($row_rel['inv_acc_number_int']!=0 ? $row_rel['inv_acc_number_int'] : ''),
            'state' => '<span class="acc_inv_state_'.$row_rel['inv_state'].'">'.getAccInvStateDescr($row_rel['inv_state']).'</span>',
            'price' => ($row_rel['gks_price_net']==0 ? '' : myCurrencyFormat($row_rel['gks_price_net'])),
            'date' => showDate(strtotime($row_rel['inv_date']), 'd/m/Y\<\b\r\>H:i:s', 1),
            'balance' => ($row_rel['affect_balance_calc']==0 ? '' : myCurrencyFormat($row_rel['affect_balance_calc'])),
          );
          break;
        }
      }
    }
  }

  //gks_acc_journal
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_acc_journal') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_acc_journal,acc_journal_descr,is_disable from gks_acc_journal WHERE id_acc_journal in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_acc_journal' and $objv['id']==$row_rel['id_acc_journal']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Ημερολόγιο'),
            'link' => '<a href="admin-acc_journal-item.php?id='.$row_rel['id_acc_journal'].'">#'.$row_rel['id_acc_journal'].'</a>',
            'oname' => trim_gks($row_rel['acc_journal_descr']),
            'oname_bg'=> '',
            'state' => '<img src="img/'.($row_rel['is_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }
  
  //gks_acc_pay
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_acc_pay') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT gks_acc_pay.id_acc_pay, gks_acc_pay.pay_state, gks_acc_pay.gks_price_total,
    gks_acc_pay.pay_date,
    gks_acc_journal.acc_journal_code, gks_acc_journal.acc_journal_descr, 
    gks_acc_seires.seira_code, gks_acc_seires.seira_descr, gks_acc_pay.pay_acc_number_int,
    CASE
      WHEN (pay_state='080listing' or pay_state='090ekdosi' or pay_state='100payment') and affect_balance=1
        THEN affect_balance_pros * affect_balance_poso
      ELSE 0
    END as affect_balance_calc          
    FROM (gks_acc_pay 
    LEFT JOIN gks_acc_journal ON gks_acc_pay.pay_acc_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_seires ON gks_acc_pay.pay_acc_seira_id = gks_acc_seires.id_acc_seira
    WHERE gks_acc_pay.id_acc_pay in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_acc_pay' and $objv['id']==$row_rel['id_acc_pay']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Πληρωμή'),
            'link' => '<a href="admin-acc-pay-item.php?id='.$row_rel['id_acc_pay'].'">#'.$row_rel['id_acc_pay'].'</a>',
            'oname' => $row_rel['acc_journal_code'].'/'.$row_rel['seira_code'].'/'.($row_rel['pay_acc_number_int']!=0 ? $row_rel['pay_acc_number_int'] : ''),
            'state' => '<span class="acc_pay_state_'.$row_rel['pay_state'].'">'.getAccPayStateDescr($row_rel['pay_state']).'</span>',
            'price' => ($row_rel['gks_price_total']==0 ? '' : myCurrencyFormat($row_rel['gks_price_total'])),
            'date' => showDate(strtotime($row_rel['pay_date']), 'd/m/Y\<\b\r\>H:i:s', 1),
            'balance' => ($row_rel['affect_balance_calc']==0 ? '' : myCurrencyFormat($row_rel['affect_balance_calc'])),
          );
          break;
        }
      }
    }
  }  
  
  //gks_acc_seires
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_acc_seires') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_acc_seira,seira_descr,is_disable from gks_acc_seires WHERE id_acc_seira in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_acc_seires' and $objv['id']==$row_rel['id_acc_seira']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Σειρά'),
            'link' => '<a href="admin-acc_seires-item.php?id='.$row_rel['id_acc_seira'].'">#'.$row_rel['id_acc_seira'].'</a>',
            'oname' => trim_gks($row_rel['seira_descr']),
            'oname_bg'=> '',
            'state' => '<img src="img/'.($row_rel['is_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  } 

  //gks_bank_accounts
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_bank_accounts') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT * from gks_bank_accounts WHERE id_bank_account in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_bank_accounts' and $objv['id']==$row_rel['id_bank_account']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Τραπεζικός λογαριασμός'),
            'link' => '<a href="admin-bank_accounts-item.php?id='.$row_rel['id_bank_account'].'">#'.$row_rel['id_bank_account'].'</a>',
            'oname' => trim_gks($row_rel['account_descr']),
            'oname_bg'=> '',
            'state' => '<img src="img/'.($row_rel['bank_account_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  } 
    
  //gks_company
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_company') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_company,company_title,company_color,company_disable from gks_company WHERE id_company in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_company' and $objv['id']==$row_rel['id_company']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Εταιρεία'),
            'link' => '<a href="admin-company-item.php?id='.$row_rel['id_company'].'">#'.$row_rel['id_company'].'</a>',
            'oname' => trim_gks($row_rel['company_title']),
            'oname_bg'=> trim_gks($row_rel['company_color']),
            'state' => '<img src="img/'.($row_rel['company_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }  
  
  //gks_company_subs
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_company_subs') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_company_sub,company_sub_title,company_sub_color,company_sub_disable from gks_company_subs WHERE id_company_sub in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_company_subs' and $objv['id']==$row_rel['id_company_sub']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Υποκατάστημα'),
            'link' => '<a href="admin-company-sub-item.php?id='.$row_rel['id_company_sub'].'">#'.$row_rel['id_company_sub'].'</a>',
            'oname' => trim_gks($row_rel['company_sub_title']),
            'oname_bg'=> trim_gks($row_rel['company_sub_color']),
            'state' => '<img src="img/'.($row_rel['company_sub_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }  
  
  //gks_crm_leads
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_crm_leads') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT gks_crm_leads.*
    FROM gks_crm_leads
    WHERE gks_crm_leads.id_crm_lead in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_crm_leads' and $objv['id']==$row_rel['id_crm_lead']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Ευκαιρία'),
            'link' => '<a href="admin-crm-lead-item.php?id='.$row_rel['id_crm_lead'].'">#'.$row_rel['id_crm_lead'].'</a>',
            'oname' => trim_gks($row_rel['subject']),
            'oname_bg'=> trim_gks($row_rel['lead_color']),
            'state' => '<span class="lead_status_'.$row_rel['lead_status_id'].'">'.
                       (isset($leads_status[$row_rel['lead_status_id']]) ? $leads_status[$row_rel['lead_status_id']]['lead_status_descr'] : '').'</span>',
            'price' => ($row_rel['esoda']==0 ? '' : myCurrencyFormat($row_rel['esoda'])),
            'date' => showDate(strtotime($row_rel['lead_date']), 'd/m/Y\<\b\r\>H:i:s', 1),
            'balance' => '',

          );
          break;
        }
      }
    }
  }  

  //gks_eshop_product_lots
  
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_eshop_product_lots') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT gks_eshop_product_lots.*, gks_eshop_products.product_descr 
    from gks_eshop_product_lots 
    LEFT JOIN gks_eshop_products ON gks_eshop_product_lots.lotproduct_id = gks_eshop_products.id_product
    WHERE id_lot_product in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_eshop_product_lots' and $objv['id']==$row_rel['id_lot_product']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Παρτίδα-Serial Number'),
            'link' => '<a href="admin-products-lots-item.php?id='.$row_rel['id_lot_product'].'">#'.$row_rel['id_lot_product'].'</a>',
            'oname' => trim_gks($row_rel['lot_name']).'/'.trim_gks($row_rel['product_descr']),
            'oname_bg'=> '',
            'state' => '<img src="img/'.($row_rel['lot_disabled']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => (empty($row_rel['lot_date_expire']) ? '': showDate(strtotime($row_rel['lot_date_expire']), 'd/m/Y', 0)),
            'balance' => '',
          );
          break;
        }
      }
    }
  }  
  
      
  //gks_eshop_products
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_eshop_products') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT gks_eshop_products.id_product,gks_eshop_products.product_disable,
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
    from gks_eshop_products
    LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product
    WHERE gks_eshop_products.id_product in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_eshop_products' and $objv['id']==$row_rel['id_product']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Είδος'),
            'link' => '<a href="admin-products-item.php?id='.$row_rel['id_product'].'">#'.$row_rel['id_product'].'</a>',
            'oname' => trim_gks($row_rel['product_descr_p']),
            'oname_bg'=> '',
            'state' => '<img src="img/'.($row_rel['product_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }  
  
  //gks_eshop_products_categories
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_eshop_products_categories') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT gks_eshop_products_categories.id_product_category,gks_eshop_products_categories.category_disable,
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
    
    FROM ((((((((gks_eshop_products_categories
    LEFT JOIN gks_eshop_products_categories AS ug2  ON gks_eshop_products_categories.product_category_parent_id = ug2.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug3  ON ug2.product_category_parent_id = ug3.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug4  ON ug3.product_category_parent_id = ug4.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug5  ON ug4.product_category_parent_id = ug5.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug6  ON ug5.product_category_parent_id = ug6.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug7  ON ug6.product_category_parent_id = ug7.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug8  ON ug7.product_category_parent_id = ug8.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug9  ON ug8.product_category_parent_id = ug9.id_product_category)
    LEFT JOIN gks_eshop_products_categories AS ug10 ON ug9.product_category_parent_id = ug10.id_product_category
    WHERE gks_eshop_products_categories.id_product_category in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_eshop_products_categories' and $objv['id']==$row_rel['id_product_category']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Κατηγορία Είδους'),
            'link' => '<a href="admin-product-categorys-item.php?id='.$row_rel['id_product_category'].'">#'.$row_rel['id_product_category'].'</a>',
            'oname' => trim_gks($row_rel['fullpath']),
            'oname_bg'=> '',
            'state' => '<img src="img/'.($row_rel['category_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }
  
  //gks_hotel
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_hotel') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_hotel,hotel_title,hotel_color,hotel_disable from gks_hotel WHERE id_hotel in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_hotel' and $objv['id']==$row_rel['id_hotel']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Ξενοδοχείο'),
            'link' => '<a href="admin-hotel-item.php?id='.$row_rel['id_hotel'].'">#'.$row_rel['id_hotel'].'</a>',
            'oname' => trim_gks($row_rel['hotel_title']),
            'oname_bg'=> trim_gks($row_rel['hotel_color']),
            'state' => '<img src="img/'.($row_rel['hotel_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }
  
  
  //gks_hotel_availability
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_hotel_availability') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_hotel_availability,availability_descr,availability_status  from gks_hotel_availability WHERE id_hotel_availability in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_hotel_availability' and $objv['id']==$row_rel['id_hotel_availability']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Διαθεσιμότητα'),
            'link' => '<a href="admin-hotel-availability-item.php?id='.$row_rel['id_hotel_availability'].'">#'.$row_rel['id_hotel_availability'].'</a>',
            'oname' => trim_gks($row_rel['availability_descr']),
            'oname_bg'=> '',
            'state' => '<span class="hotel_availability_'.$row_rel['availability_status'].'">'.getHotelAvailabilityDescr($row_rel['availability_status']).'</span>',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }
    
  //gks_hotel_floor
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_hotel_floor') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_hotel_floor,floor_descr from gks_hotel_floor WHERE id_hotel_floor in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_hotel_floor' and $objv['id']==$row_rel['id_hotel_floor']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Όροφος'),
            'link' => '<a href="admin-hotel-floor-item.php?id='.$row_rel['id_hotel_floor'].'">#'.$row_rel['id_hotel_floor'].'</a>',
            'oname' => trim_gks($row_rel['floor_descr']),
            'oname_bg'=> '',
            'state' => '',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }


  //gks_hotel_price
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_hotel_price') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_hotel_price,price_descr,price from gks_hotel_price WHERE id_hotel_price in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_hotel_price' and $objv['id']==$row_rel['id_hotel_price']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Τιμή δωματίου'),
            'link' => '<a href="admin-hotel-price-item.php?id='.$row_rel['id_hotel_price'].'">#'.$row_rel['id_hotel_price'].'</a>',
            'oname' => trim_gks($row_rel['price_descr']),
            'oname_bg'=> '',
            'state' => '',
            'price' => myCurrencyFormat($row_rel['price']),
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }
  
  
  //gks_hotel_reservation
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_hotel_reservation') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT gks_hotel_reservation.*,".GKS_WP_TABLE_PREFIX."users.gks_nickname
    FROM gks_hotel_reservation
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hotel_reservation.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE gks_hotel_reservation.id_hotel_reservation in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_hotel_reservation' and $objv['id']==$row_rel['id_hotel_reservation']) {
          $oname='';
          if ($row_rel['user_id']>0) {
            if (!empty($row_rel['gks_nickname'])) $oname=$row_rel['gks_nickname'];
          } else {
            if (!empty($row_rel['user_last_name']) or !empty($row_rel['user_first_name'])) $oname=$row_rel['user_last_name'].' '.$row_rel['user_first_name'];
          }
          
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Κράτηση'),
            'link' => '<a href="admin-hotel-reservation-item.php?id='.$row_rel['id_hotel_reservation'].'">#'.$row_rel['id_hotel_reservation'].'</a>',
            'oname' => trim_gks($oname),
            'state' => '<span class="reservation_status_'.$row_rel['reservation_status'].'">'.getHotelReservationStatusDescr($row_rel['reservation_status']).'</span>',
            'price' => ($row_rel['gks_price_total']==0 ? '' : myCurrencyFormat($row_rel['gks_price_total'])),
            'date' => showDate(strtotime($row_rel['check_in']), 'd/m/Y\<\b\r\>H:i:s', 0),
            'balance' => '',

          );
          break;
        }
      }
    }
  }   
  //gks_hotel_room
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_hotel_room') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_hotel_room,room_descr,room_status from gks_hotel_room WHERE id_hotel_room in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_hotel_room' and $objv['id']==$row_rel['id_hotel_room']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Δωμάτιο'),
            'link' => '<a href="admin-hotel-room-item.php?id='.$row_rel['id_hotel_room'].'">#'.$row_rel['id_hotel_room'].'</a>',
            'oname' => trim_gks($row_rel['room_descr']),
            'oname_bg'=> '',
            'state' => '<span class="room_status_'.$row_rel['room_status'].'">'.getHotelRoomTypeStatusDescr($row_rel['room_status']).'</span>',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }  
  
  //gks_hotel_room_type
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_hotel_room_type') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_hotel_room_type,room_type_descr,room_type_status,room_type_price from gks_hotel_room_type WHERE id_hotel_room_type in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_hotel_room_type' and $objv['id']==$row_rel['id_hotel_room_type']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Τύπος δωματίου'),
            'link' => '<a href="admin-hotel-room-type-item.php?id='.$row_rel['id_hotel_room_type'].'">#'.$row_rel['id_hotel_room_type'].'</a>',
            'oname' => trim_gks($row_rel['room_type_descr']),
            'oname_bg'=> '',
            'state' => '<span class="room_type_status_'.$row_rel['room_type_status'].'">'.getHotelRoomTypeStatusDescr($row_rel['room_type_status']).'</span>',
            'price' => myCurrencyFormat($row_rel['room_type_price']),
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }  
  
  //gks_template_html
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_template_html') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_template_html,template_html_descr,is_disable from gks_template_html WHERE id_template_html in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_template_html' and $objv['id']==$row_rel['id_template_html']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Πρότυπο HTML'),
            'link' => '<a href="admin-template_html-item.php?id='.$row_rel['id_template_html'].'">#'.$row_rel['id_template_html'].'</a>',
            'oname' => trim_gks($row_rel['template_html_descr']),
            'oname_bg'=> '',
            'state' => '<img src="img/'.($row_rel['is_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }  
    
  //gks_transfer
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_transfer') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_transfer,transfer_title,transfer_color,transfer_disable from gks_transfer WHERE id_transfer in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_transfer' and $objv['id']==$row_rel['id_transfer']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Κανάλι Transfer'),
            'link' => '<a href="admin-transfer-item.php?id='.$row_rel['id_transfer'].'">#'.$row_rel['id_transfer'].'</a>',
            'oname' => trim_gks($row_rel['transfer_title']),
            'oname_bg'=> trim_gks($row_rel['transfer_color']),
            'state' => '<img src="img/'.($row_rel['transfer_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }  
  
  //gks_transfer_reservation
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_transfer_reservation') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT gks_transfer_reservation.*,".GKS_WP_TABLE_PREFIX."users.gks_nickname
    FROM gks_transfer_reservation
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_transfer_reservation.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
    WHERE gks_transfer_reservation.id_transfer_reservation in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_transfer_reservation' and $objv['id']==$row_rel['id_transfer_reservation']) {
          $oname='';
          if ($row_rel['user_id']>0) {
            if (!empty($row_rel['gks_nickname'])) $oname=$row_rel['gks_nickname'];
          } else {
            if (!empty($row_rel['user_last_name']) or !empty($row_rel['user_first_name'])) $oname=$row_rel['user_last_name'].' '.$row_rel['user_first_name'];
          }
          
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Κράτηση'),
            'link' => '<a href="admin-transfer-reservation-item.php?id='.$row_rel['id_transfer_reservation'].'">#'.$row_rel['id_transfer_reservation'].'</a>',
            'oname' => trim_gks($oname),
            'state' => '<span class="transfer_reservation_status_'.$row_rel['transfer_reservation_status'].'">'.getTransferReservationStatusDescr($row_rel['transfer_reservation_status']).'</span>',
            'price' => ($row_rel['gks_price_total']==0 ? '' : myCurrencyFormat($row_rel['gks_price_total'])),
            'date' => showDate(strtotime($row_rel['transfer_start']), 'd/m/Y\<\b\r\>H:i:s', 0),
            'balance' => '',

          );
          break;
        }
      }
    }
  }  
    
  //gks_transfer_area
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_transfer_area') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_transfer_area,transfer_area_descr,transfer_area_disable from gks_transfer_area WHERE id_transfer_area in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_transfer_area' and $objv['id']==$row_rel['id_transfer_area']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Περιοχές'),
            'link' => '<a href="admin-transfer-area-item.php?id='.$row_rel['id_transfer_area'].'">#'.$row_rel['id_transfer_area'].'</a>',
            'oname' => trim_gks($row_rel['transfer_area_descr']),
            'oname_bg'=> '',
            'state' => '<img src="img/'.($row_rel['transfer_area_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }  
  
  //gks_transfer_oxima_type
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_transfer_oxima_type') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_transfer_oxima_type,transfer_oxima_type_descr,transfer_oxima_type_disable from gks_transfer_oxima_type WHERE id_transfer_oxima_type in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_transfer_oxima_type' and $objv['id']==$row_rel['id_transfer_oxima_type']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Τύπος Οχήματος'),
            'link' => '<a href="admin-transfer-oxima-type-item.php?id='.$row_rel['id_transfer_oxima_type'].'">#'.$row_rel['id_transfer_oxima_type'].'</a>',
            'oname' => trim_gks($row_rel['transfer_oxima_type_descr']),
            'oname_bg'=> '',
            'state' => '<img src="img/'.($row_rel['transfer_oxima_type_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }

  //gks_transfer_pricelist
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_transfer_pricelist') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_transfer_pricelist,transfer_pricelist_disable,
  gks_poi_from.poi_descr AS poi_descr_from, gks_poi_to.poi_descr AS poi_descr_to,
  gks_transfer_oxima_type.transfer_oxima_type_photo,gks_transfer_oxima_type.transfer_oxima_type_descr  
  from ((gks_transfer_pricelist 
  LEFT JOIN gks_poi AS gks_poi_from ON gks_transfer_pricelist.poi_id_from = gks_poi_from.id_poi) 
  LEFT JOIN gks_poi AS gks_poi_to ON gks_transfer_pricelist.poi_id_to = gks_poi_to.id_poi)
  LEFT JOIN gks_transfer_oxima_type ON gks_transfer_pricelist.transfer_oxima_type_id = gks_transfer_oxima_type.id_transfer_oxima_type
  WHERE id_transfer_pricelist in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_transfer_pricelist' and $objv['id']==$row_rel['id_transfer_pricelist']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Καταχώρηση Τιμοκαταλόγου'),
            'link' => '<a href="admin-transfer-pricelist-item.php?id='.$row_rel['id_transfer_pricelist'].'">#'.$row_rel['id_transfer_pricelist'].'</a>',
            'oname' => gks_lang('Προς').' '.$row_rel['poi_descr_to'],
            'oname_bg'=> '',
            'state' => '<img src="img/'.($row_rel['transfer_pricelist_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }  
   
  //gks_poi
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_poi') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_poi,poi_descr,poi_color,poi_disable from gks_poi WHERE id_poi in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_poi' and $objv['id']==$row_rel['id_poi']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Σημείο Ενδιαφέροντος'),
            'link' => '<a href="admin-poi-item.php?id='.$row_rel['id_poi'].'">#'.$row_rel['id_poi'].'</a>',
            'oname' => trim_gks($row_rel['poi_descr']),
            'oname_bg'=> trim_gks($row_rel['poi_color']),
            'state' => '<img src="img/'.($row_rel['poi_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }  
  
  
  //gks_poi_type
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_poi_type') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_poi_type,poi_type_descr,poi_type_disable from gks_poi_type WHERE id_poi_type in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_poi_type' and $objv['id']==$row_rel['id_poi_type']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Τύπος Σημείων Ενδιαφέροντος'),
            'link' => '<a href="admin-poi-type-item.php?id='.$row_rel['id_poi_type'].'">#'.$row_rel['id_poi_type'].'</a>',
            'oname' => trim_gks($row_rel['poi_type_descr']),
            'oname_bg'=> '',
            'state' => '<img src="img/'.($row_rel['poi_type_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }   
  
  //gks_poi_diadromes
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_poi_diadromes') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_poi_diadromes,
    gks_poi_from.poi_descr AS poi_descr_from, gks_poi_to.poi_descr AS poi_descr_to,
    poi_diadromes_disable 
    from (gks_poi_diadromes 
    LEFT JOIN gks_poi AS gks_poi_from ON gks_poi_diadromes.poi_id_from = gks_poi_from.id_poi) 
    LEFT JOIN gks_poi AS gks_poi_to ON gks_poi_diadromes.poi_id_to = gks_poi_to.id_poi
    WHERE id_poi_diadromes in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_poi_diadromes' and $objv['id']==$row_rel['id_poi_diadromes']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Τύπος Σημείων Ενδιαφέροντος'),
            'link' => '<a href="admin-poi-diadromes-item.php?id='.$row_rel['id_poi_diadromes'].'">#'.$row_rel['id_poi_diadromes'].'</a>',
            'oname' => $row_rel['poi_descr_from'].' <i class="fas fa-chevron-circle-right"></i> '.$row_rel['poi_descr_to'],
            'oname_bg'=> '',
            'state' => '<img src="img/'.($row_rel['poi_diadromes_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }   
    
  //gks_orders
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_orders') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT gks_orders.id_order, gks_orders.order_state, gks_orders.gks_price_net,
    gks_orders.order_date,
    CASE
      WHEN (order_state='060registered' or order_state='070inproduction' or 
           order_state='090indelivery' or order_state='095execute' or order_state='100completed' or order_state='110payment') and affect_balance=1
        THEN affect_balance_poso
      ELSE 0
    END as affect_balance_calc         
    FROM gks_orders
    WHERE gks_orders.id_order in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_orders' and $objv['id']==$row_rel['id_order']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Παραγγελία'),
            'link' => '<a href="admin-orders-item.php?id='.$row_rel['id_order'].'">#'.$row_rel['id_order'].'</a>',
            'oname' => '',
            'state' => '<span class="order_state_'.$row_rel['order_state'].'">'.getOrderStateDescr($row_rel['order_state']).'</span>',
            'price' => ($row_rel['gks_price_net']==0 ? '' : myCurrencyFormat($row_rel['gks_price_net'])),
            'date' => showDate(strtotime($row_rel['order_date']), 'd/m/Y\<\b\r\>H:i:s', 1),
            'balance' => ($row_rel['affect_balance_calc']==0 ? '' : myCurrencyFormat($row_rel['affect_balance_calc'])),
          );
          break;
        }
      }
    }
  }
    
  //gks_print_forms
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_print_forms') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_print_form,print_form_descr,is_disable from gks_print_forms WHERE id_print_form in (".implode(',',$oids).")
    order by gks_print_forms.sortorder,gks_print_forms.print_form_descr";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_print_forms' and $objv['id']==$row_rel['id_print_form']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Φόρμα Εκτύπωσης'),
            'link' => '<a href="admin-print_forms-item.php?id='.$row_rel['id_print_form'].'">#'.$row_rel['id_print_form'].'</a>',
            'oname' => trim_gks($row_rel['print_form_descr']),
            'oname_bg'=> '',
            'state' => '<img src="img/'.($row_rel['is_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }  
  
  //gks_production_ergasies
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_production_ergasies') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_production_ergasia,production_ergasia_descr from gks_production_ergasies WHERE id_production_ergasia in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_production_ergasies' and $objv['id']==$row_rel['id_production_ergasia']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Εργασία παραγωγής'),
            'link' => '<a href="admin-production-ergasies-item.php?id='.$row_rel['id_production_ergasia'].'">#'.$row_rel['id_production_ergasia'].'</a>',
            'oname' => trim_gks($row_rel['production_ergasia_descr']),
            'oname_bg'=> '',
            'state' => '',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }
    
  //gks_production_posta
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_production_posta') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_production_posto,production_posto_descr from gks_production_posta WHERE id_production_posto in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_production_posta' and $objv['id']==$row_rel['id_production_posto']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Πόστο'),
            'link' => '<a href="admin-production-posta-item.php?id='.$row_rel['id_production_posto'].'">#'.$row_rel['id_production_posto'].'</a>',
            'oname' => trim_gks($row_rel['production_posto_descr']),
            'oname_bg'=> '',
            'state' => '',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }

  //gks_production_bom
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_production_bom') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_production_bom,bom_descr,bom_disable from gks_production_bom WHERE id_production_bom in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_production_bom' and $objv['id']==$row_rel['id_production_bom']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Συνταγή'),
            'link' => '<a href="admin-production-bom-item.php?id='.$row_rel['id_production_bom'].'">#'.$row_rel['id_production_bom'].'</a>',
            'oname' => trim_gks($row_rel['bom_descr']),
            'oname_bg'=> '',
            'state' => '<img src="img/'.($row_rel['bom_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }


  //gks_users_groups
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_users_groups') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="select gks_users_groups.id_users_group,gks_users_groups.group_disable,
    CONCAT_WS('\\\\',
                    ug10.group_title,
                    ug9.group_title,
                    ug8.group_title,
                    ug7.group_title,
                    ug6.group_title,
                    ug5.group_title,
                    ug4.group_title,
                    ug3.group_title,
                    ug2.group_title,
                    gks_users_groups.group_title) as descr
    FROM ((((((((gks_users_groups
    LEFT JOIN gks_users_groups AS ug2 ON gks_users_groups.group_parent_id = ug2.id_users_group)
    LEFT JOIN gks_users_groups AS ug3 ON ug2.group_parent_id = ug3.id_users_group)
    LEFT JOIN gks_users_groups AS ug4 ON ug3.group_parent_id = ug4.id_users_group)
    LEFT JOIN gks_users_groups AS ug5 ON ug4.group_parent_id = ug5.id_users_group)
    LEFT JOIN gks_users_groups AS ug6 ON ug5.group_parent_id = ug6.id_users_group)
    LEFT JOIN gks_users_groups AS ug7 ON ug6.group_parent_id = ug7.id_users_group)
    LEFT JOIN gks_users_groups AS ug8 ON ug7.group_parent_id = ug8.id_users_group)
    LEFT JOIN gks_users_groups AS ug9 ON ug8.group_parent_id = ug9.id_users_group)
    LEFT JOIN gks_users_groups AS ug10 ON ug9.group_parent_id = ug10.id_users_group
    WHERE gks_users_groups.id_users_group in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_users_groups' and $objv['id']==$row_rel['id_users_group']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Ομάδα Επαφών'),
            'link' => '<a href="admin-usersgroups-item.php?id='.$row_rel['id_users_group'].'">#'.$row_rel['id_users_group'].'</a>',
            'oname' => trim_gks($row_rel['descr']),
            'oname_bg'=> '',
            'state' => '<img src="img/'.($row_rel['group_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }
    
  //gks_warehouses
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_warehouses') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_warehouse,warehouse_name,warehouse_color,warehouse_disable from gks_warehouses WHERE id_warehouse in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_warehouses' and $objv['id']==$row_rel['id_warehouse']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Αποθήκη'),
            'link' => '<a href="admin-warehouses-item.php?id='.$row_rel['id_warehouse'].'">#'.$row_rel['id_warehouse'].'</a>',
            'oname' => trim_gks($row_rel['warehouse_name']),
            'oname_bg'=> trim_gks($row_rel['warehouse_color']),
            'state' => '<img src="img/'.($row_rel['warehouse_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }
  
  //".GKS_WP_TABLE_PREFIX."users
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='wp_users') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT ID,gks_nickname,gks_balance
    FROM ".GKS_WP_TABLE_PREFIX."users
    WHERE ".GKS_WP_TABLE_PREFIX."users.ID in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='wp_users' and $objv['id']==$row_rel['ID']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Επαφή'),
            'link' => '<a href="admin-users-item.php?id='.$row_rel['ID'].'">#'.$row_rel['ID'].'</a>',
            'oname' => $row_rel['gks_nickname'],
            'state' => '',
            'price' => '',
            'date' => '',
            'balance' => ($row_rel['gks_balance']==0 ? '' : myCurrencyFormat($row_rel['gks_balance'])),
          );
          break;
        }
      }
    }
  }  
  //gks_eshops
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_eshops') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_eshop,eshop_name,eshop_disable from gks_eshops WHERE id_eshop in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_eshops' and $objv['id']==$row_rel['id_eshop']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('eshop'),
            'link' => '<a href="admin-eshop-item.php?id='.$row_rel['id_eshop'].'">#'.$row_rel['id_eshop'].'</a>',
            'oname' => trim_gks($row_rel['eshop_name']),
            'oname_bg'=> '',
            'state' => '<img src="img/'.($row_rel['eshop_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }  
  
  //gks_eshop_products_brands
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_eshop_products_brands') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT gks_eshop_products_brands.id_product_brand,gks_eshop_products_brands.brand_disable,
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
    
    FROM ((((((((gks_eshop_products_brands
    LEFT JOIN gks_eshop_products_brands AS ug2  ON gks_eshop_products_brands.product_brand_parent_id = ug2.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug3  ON ug2.product_brand_parent_id = ug3.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug4  ON ug3.product_brand_parent_id = ug4.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug5  ON ug4.product_brand_parent_id = ug5.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug6  ON ug5.product_brand_parent_id = ug6.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug7  ON ug6.product_brand_parent_id = ug7.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug8  ON ug7.product_brand_parent_id = ug8.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug9  ON ug8.product_brand_parent_id = ug9.id_product_brand)
    LEFT JOIN gks_eshop_products_brands AS ug10 ON ug9.product_brand_parent_id = ug10.id_product_brand
    WHERE gks_eshop_products_brands.id_product_brand in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_eshop_products_brands' and $objv['id']==$row_rel['id_product_brand']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Μάρκα'),
            'link' => '<a href="admin-product-brands-item.php?id='.$row_rel['id_product_brand'].'">#'.$row_rel['id_product_brand'].'</a>',
            'oname' => trim_gks($row_rel['fullpath']),
            'oname_bg'=> '',
            'state' => '<img src="img/'.($row_rel['brand_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  } 
  
  //gks_crm_tasks
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_crm_tasks') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT gks_crm_tasks.*
    FROM gks_crm_tasks
    WHERE gks_crm_tasks.id_crm_task in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_crm_tasks' and $objv['id']==$row_rel['id_crm_task']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Εργασία'),
            'link' => '<a href="admin-crm-task-item.php?id='.$row_rel['id_crm_task'].'">#'.$row_rel['id_crm_task'].'</a>',
            'oname' => trim_gks($row_rel['subject']),
            'oname_bg'=> trim_gks($row_rel['task_color']),
            'state' => '<span class="task_status_'.$row_rel['task_status_id'].'">'.
                       (isset($tasks_status[$row_rel['task_status_id']]) ? $tasks_status[$row_rel['task_status_id']]['task_status_descr'] : '').'</span>',
            'price' => ($row_rel['esoda']==0 ? '' : myCurrencyFormat($row_rel['esoda'])),
            'date' => showDate(strtotime($row_rel['task_date']), 'd/m/Y\<\b\r\>H:i:s', 1),
            'balance' => '',

          );
          break;
        }
      }
    }
  }  
 
  //gks_crm_machine
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_crm_machine') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT gks_crm_machine.*
    FROM gks_crm_machine
    WHERE gks_crm_machine.id_crm_machine in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_crm_machine' and $objv['id']==$row_rel['id_crm_machine']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Συσκευή'),
            'link' => '<a href="admin-crm-machine-item.php?id='.$row_rel['id_crm_machine'].'">#'.$row_rel['id_crm_machine'].'</a>',
            'oname' => trim_gks($row_rel['crm_machine_name']),
            'state' => '',
            'price' => '',
            'date' => '',
            'balance' => '',

          );
          break;
        }
      }
    }
  }
  
  //gks_orders_occasion
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_orders_occasion') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT gks_orders_occasion.id_order_occasion, gks_orders_occasion.title, gks_occasion_types.occasion_type_descr
    FROM gks_orders_occasion 
    LEFT JOIN gks_occasion_types ON gks_orders_occasion.occasion_id = gks_occasion_types.id_occasion_type
    WHERE gks_orders_occasion.id_order_occasion in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_orders_occasion' and $objv['id']==$row_rel['id_order_occasion']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Περίσταση'),
            'link' => '<a href="admin-orders-occasion-item.php?id='.$row_rel['id_order_occasion'].'">#'.$row_rel['id_order_occasion'].'</a>',
            'oname' => trim_gks($row_rel['title']).' - '.trim_gks($row_rel['occasion_type_descr']),
            'state' => '',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }  
  
  //gks_crm_channel_sale
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_crm_channel_sale') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_crm_channel_sale,crm_channel_sale_descr,crm_channel_sale_disabled FROM gks_crm_channel_sale WHERE id_crm_channel_sale in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_crm_channel_sale' and $objv['id']==$row_rel['id_crm_channel_sale']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Κανάλι πωλήσεων'),
            'link' => '<a href="admin-crm-channel-sale-item.php?id='.$row_rel['id_crm_channel_sale'].'">#'.$row_rel['id_crm_channel_sale'].'</a>',
            'oname' => $row_rel['crm_channel_sale_descr'],
            'state' => '<img src="img/'.($row_rel['crm_channel_sale_disabled']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }
  
  //gks_crm_leads_status
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_crm_leads_status') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_crm_lead_status,lead_status_descr,lead_status_color,lead_status_disabled FROM gks_crm_leads_status WHERE id_crm_lead_status in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_crm_leads_status' and $objv['id']==$row_rel['id_crm_lead_status']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Κατάσταση Ευκαιριών'),
            'link' => '<a href="admin-crm-leads-status-item.php?id='.$row_rel['id_crm_lead_status'].'">#'.$row_rel['id_crm_lead_status'].'</a>',
            'oname' => $row_rel['lead_status_descr'],
            'oname_bg'=> trim_gks($row_rel['lead_status_color']),
            'state' => '<img src="img/'.($row_rel['lead_status_disabled']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }
    
  //gks_crm_tasks_status
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_crm_tasks_status') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_crm_task_status,task_status_descr,task_status_color,task_status_disabled FROM gks_crm_tasks_status WHERE id_crm_task_status in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_crm_tasks_status' and $objv['id']==$row_rel['id_crm_task_status']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Κατάσταση Εργασιών'),
            'link' => '<a href="admin-crm-tasks-status-item.php?id='.$row_rel['id_crm_task_status'].'">#'.$row_rel['id_crm_task_status'].'</a>',
            'oname' => $row_rel['task_status_descr'],
            'oname_bg'=> trim_gks($row_rel['task_status_color']),
            'state' => '<img src="img/'.($row_rel['task_status_disabled']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }  
  
  //gks_custom_table
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_custom_table') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT id_custom_table,custom_table_descr,custom_table_disabled FROM gks_custom_table WHERE id_custom_table in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_custom_table' and $objv['id']==$row_rel['id_custom_table']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Προσαρμογή'),
            'link' => '<a href="admin-custom-item.php?id='.$row_rel['id_custom_table'].'">#'.$row_rel['id_custom_table'].'</a>',
            'oname' => $row_rel['custom_table_descr'],
            'oname_bg'=> '',
            'state' => '<img src="img/'.($row_rel['custom_table_disabled']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }  

  //gks_sociallinks_type
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_sociallinks_type') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT * FROM gks_sociallinks_type WHERE id_sociallinks_type in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_sociallinks_type' and $objv['id']==$row_rel['id_sociallinks_type']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Τύπος Συνδέσμων Κοινωνικών Δικτύων'),
            'link' => '<a href="admin-sociallinks-type-item.php?id='.$row_rel['id_sociallinks_type'].'">#'.$row_rel['sociallinks_type_descr'].'</a>',
            'oname' => $row_rel['sociallinks_type_descr'],
            'oname_bg'=> '',
            'state' => '<img src="img/'.($row_rel['sociallinks_type_disable']==0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }

  //gks_lang
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_lang') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT * FROM gks_lang WHERE idd_lang in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_lang' and $objv['id']==$row_rel['idd_lang']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Γλώσσα'),
            'link' => '<a href="admin-lang-item.php?id='.$row_rel['idd_lang'].'">#'.$row_rel['idd_lang'].'</a>',
            'oname' => $row_rel['lang_name'],
            'oname_bg'=> '',
            'state' => '<img src="img/'.($row_rel['lang_on_backend']!=0 ? '1' :'0').'.png" border="0" width="16">',
            'price' => '',
            'date' => '',
            'balance' => '',
          );
          break;
        }
      }
    }
  }
  
  // startwith($name1,'gks_ct_'
  $oids_per_ct=array();
  foreach ($object_rel as $obji => $objv) {
    if (startwith($objv['name'],'gks_ct_')) {
      if (isset($oids_per_ct[$objv['name']])==false) $oids_per_ct[$objv['name']]=array();
      $oids_per_ct[$objv['name']][]=$objv['id'];
    }
  }
  foreach ($oids_per_ct as $ct_key => $oids_per_ct_item) {
    $ctid=trim_gks(str_replace('gks_ct_','',$ct_key)); //echo '<pre>|'.$ctid.'|';die();
    $ctid=intval($ctid);
    if ($ctid > 10000) {
      $oids=$oids_per_ct_item;
      //echo '<pre>'.$ctid; print_r($oids);die();
      $field_id='id_gks_customt_gks_ct_'.$ctid;
      $custom_table_descr=gks_lang('Αντικείμενο');
      $sql_rel="select custom_table_descr from gks_custom_table where id_custom_table=".$ctid;
      $result_rel = $db_link->query($sql_rel);        
      if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
      if ($result_rel->num_rows>=1) {
        $row_rel = $result_rel->fetch_assoc();
        $custom_table_descr=$row_rel['custom_table_descr'];
      }
      
      
      if (count($oids)>0) {
        $sql_rel="SELECT ".$field_id." as id,cf_mydate_add FROM gks_customt_gks_ct_".$ctid." WHERE ".$field_id." in (".implode(',',$oids).")";
        $result_rel = $db_link->query($sql_rel);        
        if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
        while ($row_rel = $result_rel->fetch_assoc()) {
          foreach ($object_rel as $obji => $objv) {
            if ($objv['name']==$ct_key and $objv['id']==$row_rel['id']) {
              $object_rel[$obji]['data']=array(
                'objname' => $custom_table_descr,
                'link' => '<a href="admin-ct-item.php?ctid='.$ctid.'&id='.$row_rel['id'].'">#'.$row_rel['id'].'</a>',
                'oname' => '#'.$row_rel['id'],
                'oname_bg'=> '',
                'state' => '',
                'price' => '',
                'date' => showDate(strtotime($row_rel['cf_mydate_add']), 'd/m/Y\<\b\r\>H:i:s', 1),
                'balance' => '',
              );
              break;
            }
          }
        }
      }       
      
    }
  } 
  //print '<pre>';print_r($oids_per_ct);die();
  
  
 


  //den iparxei ston pinaka gks_crm_activity_objects
  //gks_calendar
  $oids=array(); foreach ($object_rel as $obji => $objv) if ($objv['name']=='gks_calendar') $oids[]=$objv['id'];
  if (count($oids)>0) {
    $sql_rel="SELECT gks_calendar.*
    FROM gks_calendar
    WHERE gks_calendar.id_calendar in (".implode(',',$oids).")";
    $result_rel = $db_link->query($sql_rel);        
    if (!$result_rel) {debug_mail(false,'error sql',$sql_rel);return 'sql error';}
    
    while ($row_rel = $result_rel->fetch_assoc()) {
      foreach ($object_rel as $obji => $objv) {
        if ($objv['name']=='gks_calendar' and $objv['id']==$row_rel['id_calendar']) {
          $object_rel[$obji]['data']=array(
            'objname' => gks_lang('Ημερολόγιο'), 
            'link' => '<a href="admin-crm-calendar.php?id='.$row_rel['id_calendar'].'">#'.$row_rel['id_calendar'].'</a>',
            'oname' => trim_gks($row_rel['calendar_title']),
            'oname_bg'=> trim_gks($row_rel['calendar_color']),
            'state' => '',
            'price' => '',
            'date' => showDate(strtotime($row_rel['calendar_start']), 'd/m/Y\<\b\r\>H:i:s', 1),
            'balance' => '',
          );
          break;
        }
      }
    }
  }






  //print '<pre>';print_r($object_rel);print_r($oids);die();
  $ret='';
  $ret.=
  '<div class="card gks_card_expand">
    <div class="card-header" style="text-align:center">
      <span style="vertical-align: middle;">'.gks_lang('Σχετικά αντικείμενα').'</span>
      <button type="button" class="btn btn-sm btn-primary" id="dialog_object_rel_add">'.gks_lang('Προσθήκη').'</button>
    </div>
    <div class="card-body" '.gks_card_body('relobjs').'>'.  
       '<div id="div_c_objects"></div>';
      
      //$objname=='gks_acc_inv' || 
      if ($objname=='gks_orders' || $objname=='gks_hotel_reservation' || $objname=='gks_transfer_reservation') { 
        $ret.='<div class="form-group row">
        <div class="col-md-12 text-center">';
        
        if ($objname=='gks_orders') {   
          $ret.='<button type="button" class="btn btn-sm btn-primary tooltipster" id="submit_button_create_acc_pay" title="'.gks_lang('Δημιουργία πληρωμής').'" '.
            ($id<=0 ? ' disabled' : '').
            '>'.gks_lang('Δημιουργία πληρωμής').'</button> ';
          $ret.='<button type="button" class="btn btn-sm btn-primary tooltipster" id="submit_button_create_acc_inv" title="'.gks_lang('Δημιουργία παραστατικού').'" '.
            ($id<=0 ? ' disabled' : '').
            '>'.gks_lang('Δημιουργία παραστατικού').'</button> ';
        } else if ($objname=='gks_hotel_reservation') {
          $ret.='<button type="button" class="btn btn-sm btn-primary tooltipster" id="submit_button_create_acc_pay" title="'.gks_lang('Δημιουργία πληρωμής').'" '.
            ($id<=0 ? ' disabled' : '').
            '>'.gks_lang('Δημιουργία πληρωμής').'</button> ';
          
          $ret.='<button type="button" class="btn btn-sm btn-primary tooltipster" id="submit_button_create_acc_inv" title="'.gks_lang('Δημιουργία 2 παραστατικών').'" '.
            ($id<=0 ? ' disabled' : '').
            '>'.gks_lang('Δημιουργία παραστατικών').'</button> ';
        } else if ($objname=='gks_transfer_reservation') {
          $ret.='<button type="button" class="btn btn-sm btn-primary tooltipster" id="submit_button_create_acc_pay" title="'.gks_lang('Δημιουργία πληρωμής').'" '.
            ($id<=0 ? ' disabled' : '').
            '>'.gks_lang('Δημιουργία πληρωμής').'</button> ';
          
          $ret.='<button type="button" class="btn btn-sm btn-primary tooltipster" id="submit_button_create_acc_inv" title="'.gks_lang('Δημιουργία παραστατικού').'" '.
            ($id<=0 ? ' disabled' : '').
            '>'.gks_lang('Δημιουργία παραστατικών').'</button> ';
          
        }
        
        $ret.='</div>
        </div>';
      } 
      
      
      //if (count($object_rel)>0) {
      
      $ret.=
      '
            <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="gks_object_rel_table">
            <thead>
                <tr>
                    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" >#</th>
                    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
                    <th class="table-dark" scope="col" style="text-align: center !important;" width="30%">'.gks_lang('Αντικείμενο').'</th> 
                    <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" >'.gks_lang('ID').'</th> 
                    <th class="table-dark" scope="col" style="text-align: left   !important;" width="30%">'.gks_lang('Όνομα').'</th>        
                    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%">'.gks_lang('Κατάσταση').'</th>        
                    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"><span title="'.gks_lang('Καθαρή αξία').'">'.gks_lang('Αξία').'</span></th>        
                    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%">'.gks_lang('Ημερομηνία').'</th>        
                    <th class="table-dark" scope="col" style="text-align: center !important;" width="10%">'.gks_lang('Τιμή για<br>υπόλοιπο').'</th>        
                </tr>
            </thead>
            <tbody>';                
           
            $i=0;
            foreach ($object_rel as $obji => $objv) {
              $i++;
              $ret.=
              '<tr class="object_rel_tr" data-id="'.$objv['id_object_rel'].'">
                <th scope="row" nowrap class="mytdcm gks_object_rel_aa">'.$i.'</td>      
                <td nowrap class="mytdcm">
                  <i class="fas fa-unlink unlink_object_rel" data-deleteafter="gks_fnc_object_rel_delete_after|'.$objv['id_object_rel'].'" '.
                  'data-id="'.$objv['id_object_rel'].'" data-model="gks_object_rel" title="'.gks_lang('Αποσύνδεση','part2').'"></i>
                </td>';
                if (isset($objv['data'])) {
                $ret.= 
                '<td class="mytdcml">'.$objv['data']['objname'].'</td>  
                <td class="mytdcm">'.$objv['data']['link'].'</td>  
                <td class="mytdcml" '.
                (isset($objv['data']['oname_bg']) ? ' style="background-color:'.$objv['data']['oname_bg'].'"' : '').
                '.>'.$objv['data']['oname'].'</td>
                <td nowrap class="mytdcm">'.$objv['data']['state'].'</td>
                <td nowrap class="mytdcm">'.$objv['data']['price'].'</td>
                <td nowrap class="mytdcm">'.$objv['data']['date'].'</td>
                <td nowrap class="mytdcm">'.$objv['data']['balance'].'</td>';
                
                } else { 
                  $ret.='<td nowrap class="mytdcm" colspan="7">'.gks_lang('Σφάλμα').'</td>';
                } 
              $ret.='</tr>';
            }
    
            $ret.='</tbody>
            </table>                   
          ';
 

 

    $ret.='</div>
  </div>';
  
  return $ret;
  
}





function gks_curl_post_async($url, $params) {
  /*
  paradeigma
  $data=array(
    'ggg' => '1a',
    'ttt' => 'ggggggggg',
  );
  gks_curl_post_async('https://test.easyfilesselection.com/my/_test/async_point.php?ggg=1&ggg=2',$data);
  */
  //echo 'hhhhhhhhhhhhh';
  // to read the ginei me 'php://input'
  
  //if (GKS_DEBUG) file_put_contents('/var/www/php/test.easyfilesselection.com/tmp/gg1.txt',$url);
  
  $mycache='cache='.time().rand(1000,9999).rand(1000,9999).rand(1000,9999);
  if (strpos($url, '?') === false) {
    $mycache='?'.$mycache;
  } else {
    $mycache='&'.$mycache;
  }
  
  $data_string = json_encode($params);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url.$mycache);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  if (defined('CURLOPT_SSL_VERIFYSTATUS'))curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  //curl_setopt($ch, CURLOPT_POST,1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
  curl_setopt($ch, CURLOPT_HEADER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER,
      array(
          'accept: application/json',
          'Content-Type: application/json',
          //'Content-Type: application/x-www-form-urlencoded',
          'Content-Length: ' . strlen($data_string)
      )
  ); 
  curl_setopt($ch, CURLOPT_TIMEOUT_MS, 100); //HERE MAGIC (We wait only 1ms on connection) Script waiting but (processing of send package to $curl is continue up to successful) so after 1ms we continue scripting and in background php continue already package to destiny. This is like apple on tree, we cut and go, but apple still fallow to destiny but we don't care what happened when fall down :) 
  curl_setopt($ch, CURLOPT_NOSIGNAL, 1); // i'dont know just it works together read manual ;)
  $result=curl_exec($ch);
  $gks_curl_errno=curl_errno($ch);
  $gks_curl_info = curl_getinfo($ch);
  curl_close ($ch); 
  //echo $result;
  //if (GKS_DEBUG) file_put_contents('/var/www/php/test.easyfilesselection.com/tmp/gg2.txt',print_r($gks_curl_info,true));
  
  
}


function from_php_global_vars_echo($jQuery3x='$') {
  global $GKS_CRM_ENABLE;
  global $GKS_SITE_HUMAN_NAME;
  global $GKS_OFFICIAL_SITE_URL;
  global $GKS_SITE_EMAIL;
  global $GKS_BASKET_CALC_ITEM_DECIMAL;
  global $GKS_BASKET_CALC_EKPTOSI_DECIMAL;
  global $GKS_NUMBER_FORMAT_CURRENCY_DECIMAL;
  global $GKS_NUMBER_FORMAT_DECIMAL;
  global $GKS_NUMBER_FORMAT_THOUSAND;
  global $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW;
  global $GKS_NUMBER_FORMAT_CURRENCY_SYMBOL;
  global $GKS_INPUT_STEP_AJIA;
  global $GKS_INPUT_STEP_POSOTITA;
  global $GKS_INPUT_STEP_POSOSTO;
  global $GKS_ORDERS_PRODUCTION;
  global $GKS_PRODUCT_LOTS_SERIALS;
  global $GKS_NOT_CHANGE_DELIVERY_COST_AT_CLICK;
  global $GKS_NOT_CHANGE_PAYMENT_COST_AT_CLICK;
  global $GKS_GOOGLE_MAPS_API_KEY;
  global $gks_user_settings;
  global $gks_voip_params;
  if (isset($gks_voip_params)==false) {
    $gks_voip_params=gks_voip_user_params();
  }
  
  return '
'.$jQuery3x.'.base64.utf8encode = true;
'.$jQuery3x.'.base64.utf8decode = true;
var from_php_gks_datetimepicker_locale=\''.gks_datetimepicker_locale($gks_user_settings['lang']['backend']).'\';
'.$jQuery3x.'.datetimepicker.setLocale(from_php_gks_datetimepicker_locale);

var from_php_gks_tinymce_locale=\''.gks_tinymce_locale($gks_user_settings['lang']['backend']).'\';
var from_php_gks_fullcalendar_locale=\''.gks_fullcalendar_locale($gks_user_settings['lang']['backend']).'\';
var from_php_gks_pivottable_locale=\''.gks_pivottable_locale($gks_user_settings['lang']['backend']).'\';

  
var from_php_GKS_SITE_URL='.$jQuery3x.'.base64.decode(\''.base64_encode(GKS_SITE_URL).'\');
var from_php_GKS_CRM_ENABLE='.($GKS_CRM_ENABLE ? 'true' : 'false').';
var from_php_GKS_SITE_HUMAN_NAME='.$jQuery3x.'.base64.decode(\''.base64_encode($GKS_SITE_HUMAN_NAME).'\');
var from_php_GKS_OFFICIAL_SITE_URL='.$jQuery3x.'.base64.decode(\''.base64_encode($GKS_OFFICIAL_SITE_URL).'\');
var from_php_GKS_SITE_EMAIL='.$jQuery3x.'.base64.decode(\''.base64_encode($GKS_SITE_EMAIL).'\');

var from_php_GKS_BASKET_CALC_ITEM_DECIMAL='.$GKS_BASKET_CALC_ITEM_DECIMAL.';
var from_php_GKS_BASKET_CALC_EKPTOSI_DECIMAL='.$GKS_BASKET_CALC_EKPTOSI_DECIMAL.';

var from_php_GKS_NUMBER_FORMAT_CURRENCY_DECIMAL='.$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL.';
var from_php_GKS_NUMBER_FORMAT_DECIMAL=\''.$GKS_NUMBER_FORMAT_DECIMAL.'\';
var from_php_GKS_NUMBER_FORMAT_THOUSAND=\''.$GKS_NUMBER_FORMAT_THOUSAND.'\';
var from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW=\''.$GKS_NUMBER_FORMAT_CURRENCY_SYMBOL_SHOW.'\';
var from_php_GKS_NUMBER_FORMAT_CURRENCY_SYMBOL=\''.$GKS_NUMBER_FORMAT_CURRENCY_SYMBOL.'\';
var from_php_GKS_INPUT_STEP_AJIA=\''.$GKS_INPUT_STEP_AJIA.'\';
var from_php_GKS_INPUT_STEP_POSOTITA=\''.$GKS_INPUT_STEP_POSOTITA.'\';
var from_php_GKS_INPUT_STEP_POSOSTO=\''.$GKS_INPUT_STEP_POSOSTO.'\';
var from_php_GKS_ORDERS_PRODUCTION='.($GKS_ORDERS_PRODUCTION ? 'true' : 'false').';
var from_php_GKS_PRODUCT_LOTS_SERIALS='.($GKS_PRODUCT_LOTS_SERIALS ? 'true' : 'false').';
var from_php_GKS_NOT_CHANGE_DELIVERY_COST_AT_CLICK='.($GKS_NOT_CHANGE_DELIVERY_COST_AT_CLICK ? 'true' : 'false').';
var from_php_GKS_NOT_CHANGE_PAYMENT_COST_AT_CLICK='.($GKS_NOT_CHANGE_PAYMENT_COST_AT_CLICK ? 'true' : 'false').';
var from_php_gks_get_max_upload_file_size='.gks_get_max_upload_file_size(true).';
var from_php_get_list_bank_accounts='.$jQuery3x.'.base64.decode(\''.base64_encode(gks_get_list_bank_accounts()).'\');
var from_php_gks_voip_params=JSON.parse('.$jQuery3x.'.base64.decode(\''.base64_encode(json_encode($gks_voip_params)).'\'));
var gks_google_maps_api_key=\''.$GKS_GOOGLE_MAPS_API_KEY.'\';

';
  
}




function gks_olografos_numberToGreekWords($number, $withCurrency = false) {
  $units = [
    0 => 'μηδέν', 1 => 'ένα', 2 => 'δύο', 3 => 'τρία', 4 => 'τέσσερα',
    5 => 'πέντε', 6 => 'έξι', 7 => 'επτά', 8 => 'οκτώ', 9 => 'εννέα',
    10 => 'δέκα', 11 => 'έντεκα', 12 => 'δώδεκα', 13 => 'δεκατρία',
    14 => 'δεκατέσσερα', 15 => 'δεκαπέντε', 16 => 'δεκαέξι',
    17 => 'δεκαεπτά', 18 => 'δεκαοκτώ', 19 => 'δεκαεννέα'
  ];

  $tens = [
    20 => 'είκοσι', 30 => 'τριάντα', 40 => 'σαράντα', 50 => 'πενήντα',
    60 => 'εξήντα', 70 => 'εβδομήντα', 80 => 'ογδόντα', 90 => 'ενενήντα'
  ];

  $hundreds = [
    100 => 'εκατό', 200 => 'διακόσια', 300 => 'τριακόσια',
    400 => 'τετρακόσια', 500 => 'πεντακόσια', 600 => 'εξακόσια',
    700 => 'επτακόσια', 800 => 'οκτακόσια', 900 => 'εννιακόσια'
  ];

  $scales = [
    1000000000000 => 'τρισεκατομμύρια',
    1000000000    => 'δισεκατομμύρια',
    1000000       => 'εκατομμύρια',
    1000          => 'χιλιάδες'
  ];

  // Negative numbers
  if ($number < 0) {
    return 'μείον ' . gks_olografos_numberToGreekWords(abs($number), $withCurrency);
  }

  // Ean exei dekadiko meros
  if (strpos((string)$number, '.') !== false || strpos((string)$number, ',') !== false) {
    $number = str_replace(',', '.', $number);
    $parts = explode('.', (string)$number);
    $intPart = intval($parts[0]);
    $decPart = str_pad(substr($parts[1], 0, 2), 2, '0'); // 2 dekadika

    $result = gks_olografos_numberToGreekWords($intPart, false);

    if ($withCurrency) {
      $result .= ' ευρώ';
    }

    if (intval($decPart) > 0) {
      $result .= ' και ' . gks_olografos_numberToGreekWords(intval($decPart), false);
      if ($withCurrency) {
          $result .= ' λεπτά';
      }
    }

    return $result;
  }

  // Miden
  if ($number == 0) {
    return $units[0];
  }

  $words = '';

  foreach ($scales as $value => $name) {
    if ($number >= $value) {
      $count = floor($number / $value);
      $number %= $value;

      if ($value == 1000) {
        if ($count == 1) {
            $words .= 'χίλια';
        } else {
            $words .= gks_olografos_numberToGreekWords($count, false) . ' ' . $name;
        }
      } else {
        if ($count == 1) {
            $words .= 'ένα ' . rtrim($name, 'ια').'ιο';
        } else {
            $words .= gks_olografos_numberToGreekWords($count, false) . ' ' . $name;
        }
      }

      if ($number > 0) {
        $words .= ' ';
      }
    }
  }

  if ($number > 0) {
    if ($number < 20) {
      $words .= $units[$number];
    } elseif ($number < 100) {
      $tensPart = intval($number / 10) * 10;
      $remainder = $number % 10;
      $words .= $tens[$tensPart];
      if ($remainder) {
          $words .= ' ' . $units[$remainder];
      }
    } elseif ($number < 1000) {
      $hundredsPart = intval($number / 100) * 100;
      $remainder = $number % 100;
      $words .= $hundreds[$hundredsPart];
      if ($remainder) {
          $words .= ' ' . gks_olografos_numberToGreekWords($remainder, false);
      }
    }
  }

  if ($withCurrency) {
    $words .= ' ευρώ';
  }

  return trim($words);
}

function gks_olografos_numberToEnglishWords($number, $withCurrency = false, $currency = "euro", $centsName = "cents") {
  $units = [
    0 => "zero", 1 => "one", 2 => "two", 3 => "three", 4 => "four",
    5 => "five", 6 => "six", 7 => "seven", 8 => "eight", 9 => "nine",
    10 => "ten", 11 => "eleven", 12 => "twelve", 13 => "thirteen",
    14 => "fourteen", 15 => "fifteen", 16 => "sixteen",
    17 => "seventeen", 18 => "eighteen", 19 => "nineteen"
  ];

  $tens = [
    20 => "twenty", 30 => "thirty", 40 => "forty", 50 => "fifty",
    60 => "sixty", 70 => "seventy", 80 => "eighty", 90 => "ninety"
  ];

  $scales = [
    1000000000000 => "trillion",
    1000000000    => "billion",
    1000000       => "million",
    1000          => "thousand",
    100           => "hundred"
  ];

  // Negative numbers
  if ($number < 0) {
    return "minus " . gks_olografos_numberToEnglishWords(abs($number), $withCurrency, $currency, $centsName);
  }

  // Check if decimal
  if (strpos((string)$number, ".") !== false || strpos((string)$number, ",") !== false) {
    $number = str_replace(",", ".", $number);
    $parts = explode(".", (string)$number);
    $intPart = intval($parts[0]);
    $decPart = str_pad(substr($parts[1], 0, 2), 2, "0"); // 2 decimal digits

    $result = gks_olografos_numberToEnglishWords($intPart, false);

    if ($withCurrency) {
        $result .= " " . $currency;
    }

    if (intval($decPart) > 0) {
        $result .= " and " . gks_olografos_numberToEnglishWords(intval($decPart), false);
        if ($withCurrency) {
            $result .= " " . $centsName;
        }
    }

    return $result;
  }

  // Zero
  if ($number == 0) {
    return $units[0];
  }

  $words = "";

  foreach ($scales as $value => $name) {
    if ($number >= $value) {
      $count = floor($number / $value);
      $number %= $value;

      if ($value >= 100) {
          $words .= gks_olografos_numberToEnglishWords($count, false) . " " . $name;
      }

      if ($number > 0) {
          $words .= " ";
      }
    }
  }

  if ($number > 0) {
    if ($number < 20) {
      $words .= $units[$number];
    } elseif ($number < 100) {
      $tensPart = intval($number / 10) * 10;
      $remainder = $number % 10;
      $words .= $tens[$tensPart];
      if ($remainder) {
          $words .= "-" . $units[$remainder];
      }
    }
  }

  if ($withCurrency && $words !== "") {
    $words .= " " . $currency;
  }

  return trim($words);
}
