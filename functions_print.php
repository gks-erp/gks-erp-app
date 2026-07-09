<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/



function erp_app_dest_printer_method_descr($id) {
  $id=intval($id);
  if ($id==1) return 'PDFium (pdf)';
  if ($id==0) return 'Adobe Acrobat Reader (pdf)';
  if ($id==2) return 'LPR (pdf)';
  if ($id==3) return 'Internet Explorer (html)';
  return '--';
  
}


function gks_print_form($object_name,$id,$form_id,$options,$custom_row_form=false) {
  global $db_link;
  global $my_wp_user_id;
  
  $ret=array('success' => false, 'message' => 'generic error');

  if ($object_name=='') {$ret['message']=gks_lang('Δεν έχει ορισθεί το αντικείμενο εκτύπωσης');debug_mail(false,$ret['message'],$object_name.'|'.$id.'|'.$form_id); return $ret;}
  
  if ($object_name=='gks_eshop_products') {
    if (isset($options['sql'])==false or $options['sql']=='') {
      global $_gks_session;
      $_gks_session['gks']['rows_per_page']=1000000;
      
      global $sql;
      include_once('admin-products_filters.php');
      
      $ppp=strpos($sql, ' ORDER BY ');
      if ($ppp !== false) {
        $sql=substr($sql, 0, $ppp);
        $sql.=' and gks_eshop_products.id_product='.$id;  
        $options['sql']=$sql;
        //print '<pre>';print $id.' '.$sql;die();
        unset($sql);
      }
    }
    if (isset($options['sql'])==false or $options['sql']=='') {
      $ret['message']=gks_lang('Δεν έχει ορισθεί το ερώτημα επιλογής εγγραφών');
      debug_mail(false,$ret['message'],$object_name.'|'.$id.'|'.$form_id,'|'.$options['sql']); return $ret;}
  } else {
    if ($id<=0) {
      $ret['message']=gks_lang('Δεν έχει ορισθεί το ID του αντικειμένου εκτύπωσης');
      debug_mail(false,$ret['message'],$object_name.'|'.$id.'|'.$form_id); return $ret;}
  }
  
  if (($form_id<=0 and $custom_row_form===false) or ($form_id==-1 and is_array($custom_row_form)==false))      
    {$ret['message']=gks_lang('Δεν έχει ορισθεί το ID της φόρμας εκτύπωσης');
      debug_mail(false,$ret['message'],$object_name.'|'.$id.'|'.$form_id); return $ret;}

  $options['object_name']=$object_name;
  $options['form_id']=$form_id;
  
  if (isset($options) and is_array($options)) {
    if (isset($options['fileserver']) and $options['fileserver']!='') {
      if (file_exists($options['fileserver']) == false) {
        $ret['message']=gks_lang('Δεν βρέθηκε ο βασικός φάκελος αποθήκευσης (FileServer) του συστήματος εκτυπώσεων');
        debug_mail(false,$ret['message'],'object_name: '.$object_name."\n".'id: '.$id."\n".'form_id: '.$form_id."\n".'options: '.print_r($options,true)); 
        return $ret;
      }
    }
    if (isset($options['folder']) and $options['folder']!='') {
      if (file_exists($options['fileserver'].$options['folder']) == false) {
        if (@mkdir($options['fileserver'].$options['folder'] , 0777, true) == false ) {
          $ret['message']=gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος <b>[1]</b> στον βασικό φάκελο αποθήκευσης (FileServer) του συστήματος εκτυπώσεων');
          $ret['message']=str_replace('[1]',$options['folder'],$ret['message']);
          debug_mail(false,$ret['message'],'object_name: '.$object_name."\n".'id: '.$id."\n".'form_id: '.$form_id."\n".'options: '.print_r($options,true)); 
          return $ret;
        }
      }
    }
  }
  
  
  
  
  //echo '<pre>',$options['fileserver'].$options['folder']; die();
  
  
  if ($form_id==-1) {
    $row_form=$custom_row_form;
  } else {
    $sql="select * from gks_print_forms where is_disable=0 and id_print_form=".$form_id;
    
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
    if ($result->num_rows < 1) {
      $ret['message']=gks_lang('Δεν βρέθηκε η φόρμα εκτύπωσης με ID').' <b>'.$form_id.'</b>';
      debug_mail(false,$ret['message'],$object_name.'|'.$id.'|'.$form_id.'|'.$sql); return $ret;}
    $row_form = $result->fetch_assoc();
  }
//  print '<pre>';
//  print_r($form_id);
//  print_r($row_form);
//  die();
  
  
  if ($row_form['width_cm'] > $row_form['height_cm']) { // panta to width < height, diladi to default na einai panta Portrait
    $temp=$row_form['height_cm'];
    $row_form['height_cm']=$row_form['width_cm'];
    $row_form['width_cm']=$temp;
  }
  //echo '<pre>'; print_r($row_form); die();
  
  
  if (isset($options) and is_array($options)) {
    if (isset($options['override'])) {
      if (isset($options['override']['gks_lang'])     and $options['override']['gks_lang']!='')     $row_form['gks_lang']=    $options['override']['gks_lang'];
      if (isset($options['override']['file_type'])    and $options['override']['file_type']!='')    $row_form['file_type']=   $options['override']['file_type'];
      if (isset($options['override']['grayscale'])    and $options['override']['grayscale']!=-1)    $row_form['grayscale']=   intval($options['override']['grayscale']);
      if (isset($options['override']['zoom'])         and $options['override']['zoom']!=-1)         $row_form['zoom']=        floatval($options['override']['zoom']);
      if (isset($options['override']['is_landscape']) and $options['override']['is_landscape']!=-1) $row_form['is_landscape']=intval($options['override']['is_landscape']);
    }
  }
  //print '<pre>';print $row_form['gks_lang']; die();
  
  
  
  
  switch ($object_name) {   
    case 'gks_orders':       
      $ret= gks_print_form_gks_orders($id,$row_form,$options);
      break;
    case 'gks_acc_inv':       
      $ret= gks_print_form_gks_acc_inv($id,$row_form,$options);
      break;
    case 'gks_acc_pay':       
      $ret= gks_print_form_gks_acc_pay($id,$row_form,$options);
      break;
    case 'gks_whi_mov':       
      $ret= gks_print_form_gks_whi_mov($id,$row_form,$options);
      break;
    case 'gks_hotel_reservation':       
      $ret= gks_print_form_gks_hotel_reservation($id,$row_form,$options);
      break;
    case 'gks_transfer_reservation':       
      $ret= gks_print_form_gks_transfer_reservation($id,$row_form,$options);
      break;
    case 'gks_crm_tasks':       
      $ret= gks_print_form_gks_crm_tasks($id,$row_form,$options);
      break;
      
    case 'gks_eshop_products':
      $ret= gks_print_form_gks_eshop_products($id,$row_form,$options);
      break;

    case 'gks_customt':
      $ret= gks_print_form_gks_customt($id,$row_form,$options);
      break;
      

        
    default: 
      $ret['message']=gks_lang('Δεν υποστηρίζεται το αντικείμενο εκτύπωσης');debug_mail(false,$ret['message'],$object_name.'|'.$id.'|'.$form_id); return $ret;
      break;
  }
  if ($ret['success']) {
    //print '<pre>fff';print_r($ret);print_r($row_form);print_r($options); die();
    
    
    
    
    
    
    if (isset($options['gks_pos_client_send_fileto_url']) and $options['gks_pos_client_send_fileto_url']<>'') {

      $copy_file_to=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/'.$ret['save_basename'];
      if (!copy($ret['path_file'],$copy_file_to)) {
        $ret['gks_pos_client_send_fileto_url_message']='<div class="alert alert-danger" role="alert">'.gks_lang('Προέκυψε σφάλμα κατά την αντιγραφή του αρχείου').'</div>';// . $ret['path_file'].'|'.$copy_file_to;
      } else {
        $postdata = http_build_query(array(
          'file_url'=>GKS_SITE_URL.'my/temp/'.$ret['save_basename'],
          //'folder' => '', //$options['gks_erp_app']['erp_app_dest_folder'],
        ));
        //print '<pre>';print_r($options);print "\n".$postdata; die();      
    
  
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$options['gks_pos_client_send_fileto_url']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$postdata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $remote_result_raw = curl_exec($ch);
        $gks_curl_errno=curl_errno($ch);
        $gks_curl_info = curl_getinfo($ch);
      
      
        $gks_curl_http_code=(isset($gks_curl_info['http_code']) ? intval($gks_curl_info['http_code']) : 0);
        $extra_error_message='';
        if ($gks_curl_http_code==0) { //HTTP Host not found
          $extra_error_message='HTTP Host not found';
          $file='';
        } else if ($gks_curl_http_code==404) { //HTTP 404 REQUEST not found
          $extra_error_message='HTTP 404 REQUEST not found';
          $file='';
        } else if ($gks_curl_http_code==400) { //HTTP 400 BAD_REQUEST
          $extra_error_message='HTTP 400 BAD_REQUEST';
          $file='';
        } else if ($gks_curl_http_code==401) { //HTTP 401 UNAUTHORIZED
          $extra_error_message='HTTP 401 UNAUTHORIZED';
          $file='';
        } else if ($gks_curl_http_code!=200) { //not ok, HTTP 200 OK
          $extra_error_message='Unkown HTTP Code: '.$gks_curl_http_code;
          $file='';
        } 
        
        if ($remote_result_raw == '') {
          debug_mail(false,'gks_pos_client_send_fileto_url error1', print_r($options,true));
          $ret['gks_pos_client_send_fileto_url_message']='<div class="alert alert-danger" role="alert">'.gks_lang('Προέκυψε σφάλμα κατά την αποστολή δεδομένων στο URL').'<br>'.$extra_error_message.'</div>';
        } else {
          $remote_result=json_decode($remote_result_raw, true);
          if (is_array($remote_result) and isset($remote_result['success']) and isset($remote_result['message']) and $remote_result['success']) {
            $ret['gks_pos_client_send_fileto_url_message']='<div class="alert alert-success" role="alert">'.gks_lang('Επιτυχής αποστολή στο URL').'</div>';
           } else {
            debug_mail(false,'gks_pos_client_send_fileto_url error2', print_r($options,true).'<br>'.$remote_result_raw);
            if (isset($remote_result['message'])) {
              $ret['gks_pos_client_send_fileto_url_message']='<div class="alert alert-danger" role="alert">'.gks_lang('Προέκυψε σφάλμα κατά την αποστολή δεδομένων στο URL').'<br>'.base64_decode($remote_result['message']).'</div>';
            } else {
              $ret['gks_pos_client_send_fileto_url_message']='<div class="alert alert-danger" role="alert">'.gks_lang('Προέκυψε σφάλμα κατά την αποστολή δεδομένων στο URL').'<br>'.$remote_result_raw.'</div>';
            }
          }
        }
        //echo '<pre>';echo $options['gks_pos_client_send_fileto_url'];echo "\r\n"; echo $remote_result;die();
      }
            
    } else if (isset($options['gks_erp_app']) and isset($options['gks_erp_app']['id_erp_app']) and $options['gks_erp_app']['id_erp_app']>0) {
      //print '<pre>fff';print_r($ret);print_r($options); die();
      
      if (file_exists($ret['path_file'])==false) {
        $ret['gks_erp_message']='<div class="alert alert-danger" role="alert">'.gks_lang('Προέκυψε σφάλμα κατά την αποστολή του αρχείου στην εφαρμογή gks ERP App Desktop').' (3):<br>'.gks_lang('Δεν βρέθηκε το αρχείο εκτύπωσης').'</div>';
      } else {
        $copy_file_to=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/'.$ret['save_basename'];
        if (!copy($ret['path_file'],$copy_file_to)) {
          $ret['gks_erp_message']='<div class="alert alert-danger" role="alert">'.gks_lang('Προέκυψε σφάλμα κατά την αποστολή του αρχείου στην εφαρμογή gks ERP App Desktop').' (4):<br>'.gks_lang('Σφάλμα κατά την αντιγραφή του αρχείου').'</div>';// . $ret['path_file'].'|'.$copy_file_to;
        } else {
        
          $erp_app_params=array(
            'id' => $options['gks_erp_app']['id_erp_app'],
          );
          
          $erp_app_params['postdata']=array();
          $erp_app_params['postdata']['file_url']=GKS_SITE_URL.'my/temp/'.$ret['save_basename'];
          
          
          
          
          if ($options['gks_erp_app']['erp_app_dest']=='printer') {
            $erp_app_params['cmd']='run_command_print_file';
            $erp_app_params['postdata']['printer']=$options['gks_erp_app']['erp_app_dest_printer'];
            $erp_app_params['postdata']['print_method']=$options['gks_erp_app']['erp_app_dest_printer_method'];
            $erp_app_params['postdata']['lpr_ip']=$options['gks_erp_app']['erp_app_dest_printer_lpr_ip'];
            $erp_app_params['postdata']['copies']=$options['gks_erp_app']['erp_app_dest_printer_copies'];
            
          } else if ($options['gks_erp_app']['erp_app_dest']=='folder') {
            $erp_app_params['cmd']='run_command_save_file';
            $erp_app_params['postdata']['folder']=$options['gks_erp_app']['erp_app_dest_folder'];
          }
          
          $gks_erp_run_result=gks_erp_app_run_command($erp_app_params);
          
          if ($gks_erp_run_result['success']==false) {
            $ret['gks_erp_message']='<div class="alert alert-danger" role="alert">'.gks_lang('Προέκυψε σφάλμα κατά την αποστολή του αρχείου στην εφαρμογή gks ERP App Desktop').' (1):<br>'.($gks_erp_run_result['message']).'</div>';
            //$return = array('success' => false, 'message' => $gks_erp_run_result['message'],'html' => '');
            //echo json_encode($return); die(); } 
            
          } else if (isset($gks_erp_run_result['data'])) {
            $gks_erp_app_data=json_decode($gks_erp_run_result['data'],true);
            if (isset($gks_erp_app_data['success']) and $gks_erp_app_data['success']==false and isset($gks_erp_app_data['message'])) {
              $ret['gks_erp_message']='<div class="alert alert-danger" role="alert">'.gks_lang('Προέκυψε σφάλμα κατά την αποστολή του αρχείου στην εφαρμογή gks ERP App Desktop').' (2):<br>'.base64_decode($gks_erp_app_data['message']).'</div>';
            } else {
              $ret['gks_erp_message']='<div class="alert alert-success" role="alert">'.gks_lang('Επιτυχής αποστολή στην εφαρμογή gks ERP App Desktop').'</div>';
            }
            
          } else {
            $ret['gks_erp_message']='<div class="alert alert-success" role="alert">'.gks_lang('Επιτυχής αποστολή στην εφαρμογή gks ERP App Desktop').'</div>';
          }
        }
      
      
        //print '<pre>';print_r($ret);print_r($options);print_r($erp_app_params);print_r($gks_erp_run_result);die();
      }
      
      
      
    }
    
    if (isset($options['paroxos_send_pdf']) and $options['paroxos_send_pdf'] and 
       ($object_name=='gks_acc_inv' or $object_name=='gks_acc_pay')) {
      
      //echo '<pre>paroxos_send_pdf'."\n".$id;die(); 
      //echo '<pre>paroxos_send_pdf'."\n".$id."\n";print_r($ret);die(); 
      //echo '<pre>'.$object_name;print_r($row_form);die(); 
      
/* $ret Array
(
    [success] => 1
    [message] => ok
    [save_basename] => INV_11545_ekdosi_2024-02-26_18.11.49.439.pdf
    [path_file] => /var/www/php/test.easyfilesselection.com/FileServerShare/acc/inv/11545/print/INV_11545_ekdosi_2024-02-26_18.11.49.439.pdf
    [path_relative] => acc/inv/11545/print/INV_11545_ekdosi_2024-02-26_18.11.49.439.pdf
    [url_file] => /my/admin-get-file.php?fs=fileservers&file=acc%2Finv%2F11545%2Fprint%2FINV_11545_ekdosi_2024-02-26_18.11.49.439.pdf
)*/      
      $xxx='';
      if ($object_name=='gks_acc_inv') {
        $xxx='inv';
      } else if ($object_name=='gks_acc_pay') {
        $xxx='pay';
      }
      
      $sql_acc_xxx="select ".$xxx."_state from gks_acc_".$xxx." where id_acc_".$xxx."=".$id;
    
      $result_acc_xxx = $db_link->query($sql_acc_xxx);        
      if (!$result_acc_xxx) {
        debug_mail(false,'error sql',$sql_acc_xxx);$ret['message']='sql error'; return $ret;}
      if ($result_acc_xxx->num_rows < 1) {
        $ret['message']=gks_lang('Δεν βρέθηκε το παραστατικό με ID').' <b>'.$id.'</b>';debug_mail(false,$ret['message'],$object_name.'|'.$id.'|'.$form_id.'|'.$sql_acc_xxx); return $ret;}
      $row_acc_xxx = $result_acc_xxx->fetch_assoc();
      
      $print_xxx_state=$row_acc_xxx[$xxx.'_state'];
      if ($print_xxx_state!='090ekdosi' and $print_xxx_state!='100payment') {
        $ret['message']=gks_lang('Το παραστατικό δεν είναι σε κατάσταση 090ekdosi ή 100payment').' <b>'.$id.'</b>';debug_mail(false,$ret['message'],$object_name.'|'.$id.'|'.$form_id.'|'.$sql_acc_xxx); return $ret;}
      

      $from_print=array(
        'fromprint' => true,
        'print_date' => date('Y-m-d H:i:s'),
        'print_file_name' => $ret['save_basename'],
        'print_file_url' => $ret['url_file'],
        'print_user_id' => $my_wp_user_id,
        'print_'.$xxx.'_state' => $print_xxx_state,
      );
      //echo '<pre>paroxos_send_pdf'."\n".$id."\n";print_r($from_print); die(); 
      
      $ret['gks_paroxos_send_pdf']=gks_paroxos_invoice_xml_send_pdf($object_name,[$id],[],$from_print);
      if ($ret['gks_paroxos_send_pdf']['success']) {
        $ret['gks_paroxos_send_pdf_message']='<div class="alert alert-success" role="alert">'.gks_lang('Το αρχείο pdf έχει αποσταλεί επιτυχώς στον πάροχο').'</div>';
      } else {
        $ret['gks_paroxos_send_pdf_message']='<div class="alert alert-danger" role="alert">'.gks_lang('Το αρχείο pdf <b>ΔΕΝ</b> έχει αποσταλεί στον πάροχο').'<br>'.gks_lang('Θα πρέπει να το στείλετε εσείς από το σχετικό εικονίδιο').'<br>'.gks_lang('Σφάλμα').': '.$ret['gks_paroxos_send_pdf']['message'].'</div>';
      }
      //echo '<pre>paroxos_send_pdf'."\n".$id."\n";print_r($ret['gks_paroxos_send_pdf']); die(); 
      
      
    }
    
    if ($ret['success']) {
      gks_plugins_functions_run('functions_print_after',array(
        'object_name'=>&$object_name,
        'id'=>&$id,
        'form_id'=>&$form_id,
        'options'=>&$options,
        'custom_row_form'=>&$custom_row_form,
        'ret'=>&$ret,
        

      ));  
    }    
    
    
    
  }
 
  return $ret;
  
}

function gks_print_form_gks_orders($id,$row_form,$options) {
  global $db_link;
  global $GKS_PRODUCT_LOTS_SERIALS;
  
  $ret=array('success' => false, 'message' => 'gks_orders generic error');
 
  $sql="SELECT gks_orders.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, ".GKS_WP_TABLE_PREFIX."users.user_url,
  gks_company.company_afm,gks_company.company_title,gks_company.company_tagline,gks_company_subs.company_sub_title,gks_company_subs.company_sub_tagline,
  gks_payment_acquirers.payment_acquirer_name, gks_delivery_methods.delivery_method_name,
  gks_eshop_pricelist.pricelist_descr, gks_eshop_fiscal_position.fiscal_position_descr,
  gks_users.order_sxolio,gks_users.pelati_sxolio,gks_users.phone_home,

  gks_lang.lang_name,
  gks_country.country_name,gks_country.country_initials,gks_country.country_initials3,gks_country.country_ee,
  gks_nomoi.nomos_descr,
  gks_country_dest.country_name as country_name_dest, 
  gks_nomoi_dest.nomos_descr as nomos_descr_dest, 
  gks_orders_occasion.title as occasion_title,gks_occasion_types.occasion_type_descr, gks_orders_occasion.mydate_add as occasion_mydate_add
  ";
  

  $ret_plugin_sql='';
  gks_plugins_functions_run('functions_print_gks_print_form_gks_orders_select',array(
    'ret_plugin_sql'=>&$ret_plugin_sql,
  ));
  $sql.=$ret_plugin_sql;
  //echo '<pre>'.$sql;die();
  
  $sql.=" FROM ";
  
  $ret_plugin_sql='';
  gks_plugins_functions_run('functions_print_gks_print_form_gks_orders_from1',array(
    'ret_plugin_sql'=>&$ret_plugin_sql,
  ));
  $sql.=$ret_plugin_sql;
  //echo '<pre>'.$sql;die();
  
  $sql.=" ((((((((((((((((gks_orders ";
  
  $ret_plugin_sql='';
  gks_plugins_functions_run('functions_print_gks_print_form_gks_orders_from2',array(
    'ret_plugin_sql'=>&$ret_plugin_sql,
  ));
  $sql.=" ".$ret_plugin_sql;
  //echo '<pre>'.$sql;die();
    
 

  $sql.="
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_orders.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_orders.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_orders.user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
  LEFT JOIN gks_company on gks_orders.company_id = gks_company.id_company)
  LEFT JOIN gks_company_subs on gks_orders.company_sub_id = gks_company_subs.id_company_sub)
  LEFT JOIN gks_payment_acquirers ON gks_orders.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer)
  LEFT JOIN gks_delivery_methods ON gks_orders.tropos_apostolis = gks_delivery_methods.id_delivery_method)
  LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id)
  LEFT JOIN gks_eshop_fiscal_position ON gks_orders.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position)
  LEFT JOIN gks_eshop_pricelist ON gks_orders.pricelist_id = gks_eshop_pricelist.id_pricelist)
  LEFT JOIN gks_country ON gks_orders.ma_country_id = gks_country.id_country)
  LEFT JOIN gks_nomoi ON gks_orders.ma_nomos_id = gks_nomoi.id_nomos)
  LEFT JOIN gks_lang ON gks_orders.user_lang = gks_lang.id_lang)
  LEFT JOIN gks_country AS gks_country_dest ON gks_orders.destination_data_country_id = gks_country_dest.id_country)
  LEFT JOIN gks_nomoi AS gks_nomoi_dest ON gks_orders.destination_data_nomos_id = gks_nomoi_dest.id_nomos)
  LEFT JOIN gks_orders_occasion on gks_orders.order_occasion_id = gks_orders_occasion.id_order_occasion)
  LEFT JOIN gks_occasion_types ON gks_orders_occasion.occasion_id = gks_occasion_types.id_occasion_type

  


  where gks_orders.id_order = ".$id;
  //print '<pre>'; print_r($sql);die();
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
  if ($result->num_rows!=1) {$ret['message']=str_replace('[1]',$id,gks_lang('Δεν βρέθηκε η παραγγελία με ID <b>[1]</b> για εκτύπωση'));debug_mail(false,$ret['message'],$sql); return $ret;}
  $row = $result->fetch_assoc();

  $row['payment_acquirer_name']=gks_lang_pft($row_form['gks_lang'],'gks_payment_acquirers','payment_acquirer_name',$row['tropos_pliromis'],$row['payment_acquirer_name']);
  $row['delivery_method_name']=gks_lang_pft($row_form['gks_lang'],'gks_delivery_methods','delivery_method_name',$row['tropos_apostolis'],$row['delivery_method_name']);

  $row['country_name']=gks_lang_pft($row_form['gks_lang'],'gks_country','country_name',$row['ma_country_id'],$row['country_name']);
  $row['country_name_dest']=gks_lang_pft($row_form['gks_lang'],'gks_country','country_name',$row['destination_data_country_id'],$row['country_name_dest']);
  
  $row['nomos_descr']=gks_lang_pft($row_form['gks_lang'],'gks_nomoi','nomos_descr',$row['ma_nomos_id'],$row['nomos_descr']);
  $row['nomos_descr_dest']=gks_lang_pft($row_form['gks_lang'],'gks_nomoi','nomos_descr',$row['destination_data_nomos_id'],$row['nomos_descr_dest']);

  $row['country_name_en_US']='';$row['country_name_en_US_dest']='';
  gks_lang_data_obj_insert_to_row($row,'gks_country',array(
    array('id' => $row['ma_country_id'],               'filename'=>'country_name', 'new_field' => 'country_name_%s'),
    array('id' => $row['destination_data_country_id'], 'filename'=>'country_name', 'new_field' => 'country_name_%s_dest'),
  ));
  $row['nomos_descr_en_US']='';$row['nomos_descr_en_US_dest']='';
  gks_lang_data_obj_insert_to_row($row,'gks_nomoi',array(
    array('id' => $row['ma_nomos_id'],               'filename'=>'nomos_descr', 'new_field' => 'nomos_descr_%s'),
    array('id' => $row['destination_data_nomos_id'], 'filename'=>'nomos_descr', 'new_field' => 'nomos_descr_%s_dest'),
  ));
  
  //print '<pre>';print_r($row);die();
  
  $company_id=intval($row['company_id']);
  $company_sub_id=intval($row['company_sub_id']);

  $row_person=array();
  $row_person['id']=$row['user_id'];
  $row_person['nickname_add']=trim_gks($row['gks_nickname_add']);
  $row_person['nickname_edit']=trim_gks($row['gks_nickname_edit']);
  $row_person['nickname']=trim_gks($row['gks_nickname']);
  $row_person['email']=$row['user_email'];
  $row_person['url']=trim_gks($row['user_url']);
  $row_person['first_name']=$row['user_first_name'];
  $row_person['last_name']=$row['user_last_name'];
  $row_person['phone']=$row['phone_home'];
  $row_person['mobile']=$row['user_mobile'];
  $row_person['user_lang']=$row['user_lang'];
  $row_person['eponimia']=$row['eponimia'];
  $row_person['title']=$row['title'];
  $row_person['afm']=$row['afm'];
  $row_person['doy']=$row['doy'];
  $row_person['epaggelma']=$row['epaggelma'];
  $row_person['odos']=$row['ma_odos'];
  $row_person['arithmos']=$row['ma_arithmos'];
  $row_person['orofos']=$row['ma_orofos'];
  $row_person['perioxi']=$row['ma_perioxi'];
  $row_person['poli']=$row['ma_poli'];
  $row_person['tk']=$row['ma_tk'];
  $row_person['country_id']=$row['ma_country_id'];
  $row_person['country_name']=$row['country_name'];
  $row_person['country_name_en']=$row_person['country_name']; //$row['country_name_en_US'];
  $row_person['country_initials']=$row['country_initials'];
  $row_person['country_initials3']=$row['country_initials3'];
  $row_person['country_ee']=$row['country_ee'];
  $row_person['nomos_id']=$row['ma_nomos_id'];
  $row_person['nomos_descr']=$row['nomos_descr'];
  $row_person['nomos_descr_en']=$row_person['nomos_descr']; //$row['nomos_descr_en_US'];
  $row_person['pricelist_id']=$row['pricelist_id'];
  $row_person['pricelist']=$row['pricelist_descr'];
  $row_person['fiscal_position_id']=$row['fiscal_position_id'];
  $row_person['fiscal_position']=$row['fiscal_position_descr'];
  $row_person['antisimvalomenos_label']=gks_lang('Πελάτης');
  $row_person['antisimvalomenos_label_en']='Customer';
  
  
  
  
  $row_person['address_text']=gks_print_address_text($row);
  $row_person['dest_name']=$row['destination_data_name'];
  $row_person['dest_phone']=$row['destination_data_phone'];
  $row_person['dest_odos']=$row['destination_data_odos'];
  $row_person['dest_arithmos']=$row['destination_data_arithmos'];
  $row_person['dest_orofos']=$row['destination_data_orofos'];
  $row_person['dest_perioxi']=$row['destination_data_perioxi'];
  $row_person['dest_poli']=$row['destination_data_poli'];
  $row_person['dest_tk']=$row['destination_data_tk'];
  $row_person['dest_country_id']=$row['destination_data_country_id'];
  $row_person['dest_country_name']=$row['country_name_dest'];
  $row_person['dest_country_name_en']=$row_person['dest_country_name']; //$row['country_name_en_US_dest'];
  $row_person['dest_nomos_id']=$row['destination_data_nomos_id'];
  $row_person['dest_nomos_descr']=$row['nomos_descr_dest'];
  $row_person['dest_nomos_descr_en']=$row_person['dest_nomos_descr']; //$row['nomos_descr_en_US_dest'];

  if ($row['address_extra'] < 1) {
    $row_person['dest_name']='';
    $row_person['dest_phone']='';
    $row_person['dest_odos']='';
    $row_person['dest_arithmos']='';
    $row_person['dest_orofos']='';
    $row_person['dest_perioxi']='';
    $row_person['dest_poli']='';
    $row_person['dest_tk']='';
    $row_person['dest_country_id']=0;
    $row_person['dest_country_name']='';
    $row_person['dest_country_name_en']='';
    $row_person['dest_nomos_id']=0;
    $row_person['dest_nomos_descr']='';
    $row_person['dest_nomos_descr_en']='';    
  }
  
  
  $row_doc=array();
  $row_doc['title']=gks_lang('Παραγγελία');
  $row_doc['title_pre']='';
  
  if ($row_form['gks_lang']=='el-GR') {
    $row_doc['title_pre'] = getOrderStateDescr($row['order_state']);
  } else {
    $row_doc['title_pre'] = getOrderStateDescr($row['order_state']);
  }
  $row_doc['company']=$row['company_title'];
  if (trim_gks($row['company_sub_title'])!='') $row_doc['company'].=' \ '.$row['company_sub_title'];
  
  $row_doc['company_title']=$row['company_title'];
  $row_doc['company_sub_title']=trim_gks($row['company_sub_title']);
  if ($row_doc['company_sub_title']=='') $row_doc['company_sub_title']=gks_lang('Κεντρικό');
  
  $row_doc['date']=myDateTimeFormat(_time_user(strtotime($row['order_date']),1));
  $row_doc['date_d']=myDateFormat(_time_user(strtotime($row['order_date']),1));
  $row_doc['date_dw']=myDateFormatw(_time_user(strtotime($row['order_date']),1),$row_form['gks_lang']);
  $row_doc['date_dt']=myDateTimeFormat(_time_user(strtotime($row['order_date']),1));
  $row_doc['date_dtw']=myDateTimeFormatw(_time_user(strtotime($row['order_date']),1),$row_form['gks_lang']);
  $row_doc['date_dtt']=myDateTimeFormatText(_time_user(strtotime($row['order_date']),1),$row_form['gks_lang']);
  $row_doc['datefull']=showDate(strtotime($row['order_date']),'d/m/Y H:i:s',1);
  
  $row_doc['mydate_add']=showDate(strtotime($row['mydate_add']),'d/m/Y H:i',1);
  $row_doc['mydate_edit']=showDate(strtotime($row['mydate_edit']),'d/m/Y H:i',1);
  $row_doc['seira']='';
  $row_doc['seira_descr']='';
  $row_doc['number']=trim_gks($row['id_order']);
  $row_doc['number_str']=trim_gks($row['id_order']);
  $row_doc['mark']='';
  $row_doc['aade_qrurl']='';
  $row_doc['aade_paroxos_qrurl']='';
  $row_doc['paroxos_tf1_url']='';
  $row_doc['aade_invoiceuid']='';
  $row_doc['paroxos_authenticationCode']='';
  $row_doc['products_posotita']=trim_gks($row['products_posotita']);
  $row_doc['gks_price_original_net']=trim_gks($row['gks_price_original_net']);
  $row_doc['gks_price_net']=trim_gks($row['gks_price_net']);
  $row_doc['gks_price_fpa']=trim_gks($row['gks_price_fpa']);
  $row_doc['gks_price_netfpa']=trim_gks($row['gks_price_netfpa']);
  $row_doc['gks_price_total']=trim_gks($row['gks_price_total']);
  $row_doc['totalWithheldAmount']=trim_gks($row['totalWithheldAmount']);
  $row_doc['totalOtherTaxesAmount']=trim_gks($row['totalOtherTaxesAmount']);
  $row_doc['totalStampDutyamount']=trim_gks($row['totalStampDutyamount']);
  $row_doc['totalFeesAmount']=trim_gks($row['totalFeesAmount']);
  $row_doc['totalDeductionsAmount']='';

  $row_person['person_balance_before']=gks_balance_calc(['id' => $row['user_id'], 'except_id_order' => $row['id_order']]);
  $row_person['person_balance_after']=gks_balance_calc(['id' => $row['user_id']]);


  $enarji_apostolis='';
  if (isset($row['dispatch_date'])) $enarji_apostolis=showDate(strtotime($row['dispatch_date']), 'd/m/Y', 0);
  if (isset($row['dispatch_time'])) $enarji_apostolis.=' '.showDate(strtotime($row['dispatch_time']), 'H:i', 0);
  $row_doc['enarji_apostolis']=     $enarji_apostolis;
  $row_doc['enarji_apostolis_date']=(isset($row['dispatch_date']) ? showDate(strtotime($row['dispatch_date']), 'd/m/Y', 0) : '');
  $row_doc['enarji_apostolis_time']=(isset($row['dispatch_time']) ? showDate(strtotime($row['dispatch_time']), 'H:i', 0) : '');

  $row_doc['arithmos_oximatos']=trim_gks($row['vehicle_number']);
  
  $row_doc['isdeliverynote']=0;
  $row_doc['isdeliverynote_display']='none';
  
  $row_doc['load_branch']='';
  $row_doc['load_odos']='';
  $row_doc['load_arithmos']='';
  $row_doc['load_orofos']='';
  $row_doc['load_perioxi']='';
  $row_doc['load_poli']='';
  $row_doc['load_tk']='';
  $row_doc['country_name_load']='';
  $row_doc['nomos_descr_load']='';

  $row_doc['deli_branch']='';
  $row_doc['deli_odos']='';
  $row_doc['deli_arithmos']='';
  $row_doc['deli_orofos']='';
  $row_doc['deli_perioxi']='';
  $row_doc['deli_poli']='';
  $row_doc['deli_tk']='';
  $row_doc['country_name_deli']='';
  $row_doc['nomos_descr_deli']='';
  
  
  
  $row_doc['skopos_diakinisis']='';
  $row_doc['tropos_pliromis']=trim_gks($row['payment_acquirer_name']);
  $row_doc['tropos_apostolis']=trim_gks($row['delivery_method_name']);
  
  $row_doc['arithmos_aposolis']=trim_gks($row['delivery_number']);
  $row_doc['note_doc']=trim_gks($row['note_doc']);
  $row_doc['note_production']=trim_gks($row['note_production']);
  $row_doc['note_logistirio']=trim_gks($row['note_logistirio']);
  
  $row_doc['ddate']= (isset($row['ddate']) ? showDate(strtotime($row['ddate']),'d/m/Y',1) : '');
  $row_doc['occasion_title']=trim_gks($row['occasion_title']);
  $row_doc['occasion_type_descr']=trim_gks($row['occasion_type_descr']);
  $row_doc['occasion_mydate_add']= (isset($row['occasion_mydate_add']) ? showDate(strtotime($row['occasion_mydate_add']),'d/m/Y H:i',1) : '');

  $row_doc['idiotites_text']=gks_print_idiotites_text($row);
  $row_doc['photos']=gks_print_doc_photos_orders($row);
  $row_doc['links']=gks_print_doc_links_orders($row);
  
  gks_plugins_functions_run('functions_print_gks_print_form_gks_orders_row_doc',array(
    'id'=>&$id,
    'row'=>&$row,
    'row_doc'=>&$row_doc,
  ));
  
 
   
  $row_canceled_doc=array();
  $row_canceled_doc['display']='none';
  $row_canceled_doc['title']='';
  $row_canceled_doc['title_pre']='';
  $row_canceled_doc['company']='';
  $row_canceled_doc['date']='';
  $row_canceled_doc['datefull']='';
  $row_canceled_doc['seira']='';
  $row_canceled_doc['seira_descr']='';
  $row_canceled_doc['number']=0;
  $row_canceled_doc['number_str']='';
  $row_canceled_doc['mark']='';
  $row_canceled_doc['aade_qrurl']='';
  $row_canceled_doc['aade_paroxos_qrurl']='';
  $row_canceled_doc['paroxos_tf1_url']='';
  $row_canceled_doc['aade_invoiceuid']='';
  $row_canceled_doc['paroxos_authenticationCode']='';

  
  
  //print'<pre>';print_r($row);die();
  $row_credit_doc=array();

  $row_credit_doc['display']='none';
  $row_credit_doc['title']='';
  $row_credit_doc['title_pre']='';
  $row_credit_doc['company']='';
  $row_credit_doc['date']='';
  $row_credit_doc['datefull']='';
  $row_credit_doc['seira']='';
  $row_credit_doc['seira_descr']='';
  $row_credit_doc['number']=0;
  $row_credit_doc['number_str']='';
  $row_credit_doc['mark']='';
  $row_credit_doc['aade_qrurl']='';
  $row_credit_doc['aade_paroxos_qrurl']='';
  $row_credit_doc['paroxos_tf1_url']='';
  $row_credit_doc['aade_invoiceuid']='';
  $row_credit_doc['paroxos_authenticationCode']='';
  
  
  
  
  $sql="SELECT
  CASE
    WHEN gks_eshop_products.product_class='variable_item' THEN
      CASE
        WHEN gks_eshop_products.product_photo<>'' THEN
          gks_eshop_products.product_photo
        ELSE
          gks_eshop_products_parent.product_photo
      END
    ELSE gks_eshop_products.product_photo

  END as product_photo_p,
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
  END as product_descr_p,  
  CASE
    WHEN gks_eshop_products.product_class='variable_item' THEN
      CASE
        WHEN gks_eshop_products.product_descr_small<>'' THEN
          gks_eshop_products.product_descr_small
        ELSE
          CASE
            WHEN gks_eshop_products.product_descr_variable<>'' THEN
              CONCAT_WS(' ', gks_eshop_products_parent.product_descr_small, gks_eshop_products.product_descr_variable)
            ELSE
              gks_eshop_products_parent.product_descr_small
          END
      END
    ELSE gks_eshop_products.product_descr_small
  END as product_descr_small_p,  
  gks_orders_products.*, 
      
  gks_eshop_products.product_code, 
  gks_eshop_products.product_sku, 
  gks_eshop_products.product_gtin,
  gks_eshop_products.product_upc,
  gks_eshop_products.product_ean,
  gks_eshop_products.product_isbn,
  gks_eshop_products.product_taric,
  gks_eshop_products.product_photo, 
  gks_eshop_products.product_descr_big, 
  gks_monades_metrisis.monada_descr, 
  gks_monades_metrisis.monada_symbol,
  gks_eshop_fpa.fpa_descr_print, gks_eshop_fpa.fpa_pososto,
  gks_eshop_pricelist.pricelist_descr,
  gks_aade_katigoria_parakratoumemenon_foron.aade_katigoria_parakratoumemenon_foron_type, 
  gks_aade_katigoria_parakratoumemenon_foron.aade_katigoria_parakratoumemenon_foron_descr,
  gks_aade_katigoria_loipon_foron.aade_katigoria_loipon_foron_type,
  gks_aade_katigoria_loipon_foron.aade_katigoria_loipon_foron_descr,
  gks_aade_katigoria_xartosimou.aade_katigoria_xartosimou_descr,
  
  gks_aade_katigoria_telon.aade_katigoria_telon_type,
  gks_aade_katigoria_telon.aade_katigoria_telon_descr

  FROM ((((((((gks_orders_products
  LEFT JOIN gks_eshop_products ON gks_orders_products.product_id = gks_eshop_products.id_product)
  LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product)
  LEFT JOIN gks_monades_metrisis ON gks_orders_products.product_monada_id = gks_monades_metrisis.id_monada)
  LEFT JOIN gks_eshop_fpa ON gks_orders_products.product_fpa_id = gks_eshop_fpa.id_fpa)
  LEFT JOIN gks_eshop_pricelist ON gks_orders_products.product_pricelist_item_id = gks_eshop_pricelist.id_pricelist)
  LEFT JOIN gks_aade_katigoria_parakratoumemenon_foron ON gks_orders_products.product_withheldPercentCategory = gks_aade_katigoria_parakratoumemenon_foron.id_aade_katigoria_parakratoumemenon_foron)
  LEFT JOIN gks_aade_katigoria_loipon_foron ON gks_orders_products.product_otherTaxesPercentCategory = gks_aade_katigoria_loipon_foron.id_aade_katigoria_loipon_foron)
  LEFT JOIN gks_aade_katigoria_xartosimou ON gks_orders_products.product_stampDutyPercentCategory = gks_aade_katigoria_xartosimou.id_aade_katigoria_xartosimou)
  LEFT JOIN gks_aade_katigoria_telon ON gks_orders_products.product_feesPercentCategory = gks_aade_katigoria_telon.id_aade_katigoria_telon

  
  WHERE gks_orders_products.order_id=".$id."
  ORDER BY gks_orders_products.product_aa;";
  
  //AND gks_orders_products.product_is_optional in (0,2) 

   
  
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
  //if ($result->num_rows==0) {$ret['message']=str_replace('[1]',$id,gks_lang('Δεν βρέθηκαν είδη για την παραγγελία με ID <b>[1]</b> για εκτύπωση'));debug_mail(false,$ret['message'],$sql); return $ret;}
  
  $row_eidoi=array();
  $id_product_array_ids=array();
  $row_eidoi_optional=array();
  while ($eidos = $result->fetch_assoc()) {
    if ($eidos['product_id']==2) $eidos['product_id']=0;
    
    $product_photo=trim_gks($eidos['product_photo']);
    $eidos['product_photo']='';
    if ($product_photo!='') {
      $full_product_photo=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.$product_photo;
      if (file_exists($full_product_photo)) {
        $eidos['product_photo']=GKS_SITE_URL.substr($product_photo, 1);
      }
    }
    
    $eidos['monada_descr']=gks_lang_pft($row_form['gks_lang'],'gks_monades_metrisis','monada_descr',$eidos['product_monada_id'],$eidos['monada_descr']);
    $eidos['monada_symbol']=gks_lang_pft($row_form['gks_lang'],'gks_monades_metrisis','monada_symbol',$eidos['product_monada_id'],$eidos['monada_symbol']);

    if (in_array(intval($eidos['product_is_optional']),[0,2])) {
      $id_product_array_ids[]=$eidos['id_order_product'];
      $row_eidoi[]=$eidos;
    } else {
      $row_eidoi_optional[]=$eidos;
    }
  }
  
  
  //print '<pre>';print_r($row_eidoi);die();

  //$timemmm=time();
  $gks_custom_prepare = gks_custom_table_item_prepare('gks_eshop_products',['from'=>'print']);
  if ($gks_custom_prepare['success']==true) {
    foreach ($row_eidoi as $pkey => $eidos) {
      $custom_row['id_product']=$eidos['product_id'];
      $gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$custom_row);
      if ($gks_custom_row['success']) {
        //print '<pre>';print_r($gks_custom_row);die();
        
        foreach ($gks_custom_row['fields'] as $key => $cf_item) {
          $row_eidoi[$pkey]['custom_'.$key]=array(
            'type'  => $cf_item['field_type_id'],
            'value' => $cf_item['print'],
          );
        } 
      }
    }
    foreach ($row_eidoi_optional as $pkey => $eidos) {
      $custom_row['id_product']=$eidos['product_id'];
      $gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$custom_row);
      if ($gks_custom_row['success']) {
        //print '<pre>';print_r($gks_custom_row);die();
        
        foreach ($gks_custom_row['fields'] as $key => $cf_item) {
          $row_eidoi[$pkey]['custom_'.$key]=array(
            'type'  => $cf_item['field_type_id'],
            'value' => $cf_item['print'],
          );
        } 
      }
    }
    
    //echo '<pre>'.(time()-$timemmm);die();
    //echo '<pre>';print_r($row_doc);die();
    //print_r($gks_custom_row);
  }
  //print '<pre>';print_r($row_eidoi);die();
  //print '<pre>';print_r($row_eidoi_optional);die();
  
  
  $products_lots_serials=array();
  if (count($id_product_array_ids)>0) {
    if ($GKS_PRODUCT_LOTS_SERIALS) {
      $sql_lots_serials="SELECT 
      gks_orders_products_lots.lot_product_id,
      order_product_id as id, 
      lot_product_quantity,
      gks_eshop_product_lots.lot_name, 
      gks_eshop_product_lots.lot_descr, 
      gks_eshop_product_lots.lot_date_production, 
      gks_eshop_product_lots.lot_date_expire, 
      gks_eshop_product_lots.lot_disabled
      FROM gks_orders_products_lots
      LEFT JOIN gks_eshop_product_lots ON gks_orders_products_lots.lot_product_id = gks_eshop_product_lots.id_lot_product
      WHERE gks_orders_products_lots.order_product_id In (".implode(',',$id_product_array_ids).")
      ORDER BY gks_orders_products_lots.id_order_product_lots";
      $result_lots_serials = $db_link->query($sql_lots_serials);        
      if (!$result_lots_serials) {debug_mail(false,'error sql',$sql_lots_serials); die('sql error');}
      while ($row_lots_serials = $result_lots_serials->fetch_assoc()) {
        $products_lots_serials[$row_lots_serials['id']][]=$row_lots_serials;
      }
      
      //echo '<pre>';print_r($products_lots_serials);die();
      
    }    
  }
  
  $row_fpa=array();
  foreach ($row_eidoi as $eidos) {
    if ($eidos['product_fpa_pososto']!=0) {
      if (isset($row_fpa[$eidos['product_fpa_pososto']])==false) {
        $row_fpa[$eidos['product_fpa_pososto']]=array('aa' => 1, 'pososto' => $eidos['product_fpa_pososto'], 'net'=> 0, 'fpa'=>0);
      }
      $row_fpa[$eidos['product_fpa_pososto']]['net']+=$eidos['product_price_final_all_net'];
      $row_fpa[$eidos['product_fpa_pososto']]['fpa']+=$eidos['product_price_final_all_fpa'];
    }
  } 
  
  //print '<pre>'; print_r($row_fpa); die();
  
  $row_foroi=array();
  
  $row_foroi[1]=array(
    'descr'=>gks_lang('Παρακρατούμενος Φόρος'),
    'net' => 0,
    'foros' => 0,
  );
  $row_foroi[2]=array(
    'descr'=>gks_lang('Λοιποί Φόροι'),
    'net' => 0,
    'foros' => 0,
  );
  $row_foroi[3]=array(
    'descr'=>gks_lang('Ψηφιακό Τέλος συναλλαγής'),
    'net' => 0,
    'foros' => 0,
  );
  $row_foroi[4]=array(
    'descr'=>gks_lang('Τέλη'),
    'net' => 0,
    'foros' => 0,
  );
  $row_foroi[5]=array(
    'descr'=>gks_lang('Κρατήσεις'),
    'net' => 0,
    'foros' => 0,
  );
  
  foreach ($row_eidoi as $eidos) {
    if ($eidos['product_withheldAmount']!=0) {
      $row_foroi[1]['net']+=$eidos['product_price_final_all_net'];
      $row_foroi[1]['foros']+=$eidos['product_withheldAmount'];
    }
    if ($eidos['product_otherTaxesAmount']!=0) {
      $row_foroi[2]['net']+=$eidos['product_price_final_all_net'];
      $row_foroi[2]['foros']+=$eidos['product_otherTaxesAmount'];
    }
    if ($eidos['product_stampDutyAmount']!=0) {
      $row_foroi[3]['net']+=$eidos['product_price_final_all_net'];
      $row_foroi[3]['foros']+=$eidos['product_stampDutyAmount'];
    }
    if ($eidos['product_feesAmount']!=0) {
      $row_foroi[4]['net']+=$eidos['product_price_final_all_net'];
      $row_foroi[4]['foros']+=$eidos['product_feesAmount'];
    }
//    if ($eidos['product_deductionsAmount']!=0) {
//      $row_foroi[5]['net']+=$eidos['product_price_final_all_net'];
//      $row_foroi[5]['foros']+=$eidos['product_deductionsAmount'];
//    }
  }  
  
  if ($row_foroi[1]['foros']==0) unset($row_foroi[1]);
  if ($row_foroi[2]['foros']==0) unset($row_foroi[2]);
  if ($row_foroi[3]['foros']==0) unset($row_foroi[3]);
  if ($row_foroi[4]['foros']==0) unset($row_foroi[4]);
  if ($row_foroi[5]['foros']==0) unset($row_foroi[5]);
  

  $gks_custom_prepare = gks_custom_table_item_prepare('gks_orders',['from'=>'print']);
  if ($gks_custom_prepare['success']==true) {
    $custom_row['id_order']=$id;
    $gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$custom_row);
    if ($gks_custom_row['success']) {
      foreach ($gks_custom_row['fields'] as $key => $cf_item) {
        $row_doc['custom_'.$key]=array(
          'type'  => $cf_item['field_type_id'],
          'value' => $cf_item['print'],
        );
      } 
    }
    //echo '<pre>';print_r($row_doc);die();
    //print_r($gks_custom_row);
  }

  $gks_custom_prepare = gks_custom_table_item_prepare('wp_users',['from'=>'print']);
  if ($gks_custom_prepare['success']==true) {
    $custom_row['ID']=$row_person['id'];
    $gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$custom_row);
    if ($gks_custom_row['success']) {
      foreach ($gks_custom_row['fields'] as $key => $cf_item) {
        $row_person['custom_'.$key]=array(
          'type'  => $cf_item['field_type_id'],
          'value' => $cf_item['print'],
        );
      } 
    }
    //echo '<pre>';print_r($row_person);die();
    //print_r($gks_custom_row);
  }  
    
  $company_ret=gks_print_form_company_data($company_id,$company_sub_id,$options,$row_form);
  if ($company_ret['success']==false) {$ret['message']=$company_ret['message'];return $ret;}
  
  return gks_print_form_make_print($row_form,$row,$row_doc,$row_canceled_doc,$row_credit_doc,$row_eidoi,$row_fpa,$row_foroi,$company_ret['data'],$row_person,$options,$products_lots_serials,$row_eidoi_optional);
}

function gks_print_form_gks_acc_inv($id,$row_form,$options) {
  global $db_link;
  global $GKS_PRODUCT_LOTS_SERIALS;
  
  $ret=array('success' => false, 'message' => 'gks_acc_inv generic error');
 
  $sql="SELECT gks_acc_inv.*, 
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, ".GKS_WP_TABLE_PREFIX."users.user_url,
  gks_company.company_afm,gks_company.company_title,gks_company.company_tagline,gks_company_subs.company_sub_title,gks_company_subs.company_sub_tagline,
  gks_payment_acquirers.payment_acquirer_name, gks_delivery_methods.delivery_method_name, 
  gks_eshop_pricelist.pricelist_descr, gks_eshop_fiscal_position.fiscal_position_descr,
  gks_users.order_sxolio,gks_users.pelati_sxolio,gks_users.phone_home,
  gks_acc_journal.acc_journal_descr, gks_acc_seires.seira_code, gks_acc_seires.seira_descr,gks_acc_seires.is_xeirografi,
  gks_acc_journal.acc_eidos_parastatikou_id,
  eidos_parastatikou_type_id, antisimvalomenos_label, eidos_parastatikou_type_id, antisimvalomenos_label_en, 
  eidos_parastatikou_need_prev, eidos_parastatikou_has_fpa,eidos_parastatikou_has_othertaxes, 
  eidos_parastatikou_has_esoda, eidos_parastatikou_has_eksoda,eidos_parastatikou_need_afm,
  gks_lang.lang_name,
  gks_country.country_name,gks_country.country_initials,gks_country.country_initials3,gks_country.country_ee,
  gks_nomoi.nomos_descr,
  
  gks_country_dest.country_name as country_name_dest,  
  gks_nomoi_dest.nomos_descr as nomos_descr_dest, 
  gks_aade_skopos_diakinisis.aade_skopos_diakinisis_code, 
  gks_aade_skopos_diakinisis.aade_skopos_diakinisis_descr,

  gks_acc_eidi_parastatikon.rbs_code_a,
  
  gks_acc_seires.seira_isdeliverynote,
  gks_nomoi_load.nomos_descr as nomos_descr_load,
  gks_country_load.country_name as country_name_load,
  gks_nomoi_deli.nomos_descr as nomos_descr_deli,
  gks_country_deli.country_name as country_name_deli  
  
  FROM (((((((((((((((((((((((gks_acc_inv
  
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_acc_inv.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_acc_inv.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_inv.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
  LEFT JOIN gks_company on gks_acc_inv.company_id = gks_company.id_company)
  LEFT JOIN gks_company_subs on gks_acc_inv.company_sub_id = gks_company_subs.id_company_sub)
  LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
  LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
  LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira)
  LEFT JOIN gks_payment_acquirers ON gks_acc_inv.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer) 
  LEFT JOIN gks_delivery_methods ON gks_acc_inv.tropos_apostolis = gks_delivery_methods.id_delivery_method) 
  LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
  LEFT JOIN gks_eshop_fiscal_position ON gks_acc_inv.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
  LEFT JOIN gks_eshop_pricelist ON gks_acc_inv.pricelist_id = gks_eshop_pricelist.id_pricelist)
  LEFT JOIN gks_country ON gks_acc_inv.ma_country_id = gks_country.id_country)
  LEFT JOIN gks_nomoi ON gks_acc_inv.ma_nomos_id = gks_nomoi.id_nomos)
  LEFT JOIN gks_lang ON gks_acc_inv.user_lang = gks_lang.id_lang)
  LEFT JOIN gks_country AS gks_country_dest ON gks_acc_inv.destination_data_country_id = gks_country_dest.id_country) 
  LEFT JOIN gks_nomoi AS gks_nomoi_dest ON gks_acc_inv.destination_data_nomos_id = gks_nomoi_dest.id_nomos)
  LEFT JOIN gks_aade_skopos_diakinisis ON gks_acc_inv.aade_skopos_diakinisis_id = gks_aade_skopos_diakinisis.id_aade_skopos_diakinisis)

  LEFT JOIN gks_country as gks_country_load ON gks_acc_inv.load_country_id = gks_country_load.id_country)
  LEFT JOIN gks_nomoi as gks_nomoi_load ON gks_acc_inv.load_nomos_id = gks_nomoi_load.id_nomos)
  LEFT JOIN gks_country as gks_country_deli ON gks_acc_inv.deli_country_id = gks_country_deli.id_country)
  LEFT JOIN gks_nomoi as gks_nomoi_deli ON gks_acc_inv.deli_nomos_id = gks_nomoi_deli.id_nomos

  where gks_acc_inv.id_acc_inv = ".$id;
  //print '<pre>'; print_r($sql);die();
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
  if ($result->num_rows!=1) {$ret['message']=str_replace('[1]',$id,gks_lang('Δεν βρέθηκε το παραστατικό με ID <b>[1]</b> για εκτύπωση'));debug_mail(false,$ret['message'],$sql); return $ret;}
  $row = $result->fetch_assoc();
  
  //echo 'hhhhh '.$row['company_tagline']; die();
  
  //$row['company_tagline']='gggggggggggggggggg';
  
  $row['acc_journal_descr']=gks_lang_pft($row_form['gks_lang'],'gks_acc_journal','acc_journal_descr',$row['inv_acc_journal_id'],$row['acc_journal_descr']);
  $row['payment_acquirer_name']=gks_lang_pft($row_form['gks_lang'],'gks_payment_acquirers','payment_acquirer_name',$row['tropos_pliromis'],$row['payment_acquirer_name']);
  $row['delivery_method_name']=gks_lang_pft($row_form['gks_lang'],'gks_delivery_methods','delivery_method_name',$row['tropos_apostolis'],$row['delivery_method_name']);
  $row['country_name']=gks_lang_pft($row_form['gks_lang'],'gks_country','country_name',$row['ma_country_id'],$row['country_name']);
  $row['country_name_dest']=gks_lang_pft($row_form['gks_lang'],'gks_country','country_name',$row['destination_data_country_id'],$row['country_name_dest']);
  $row['nomos_descr']=gks_lang_pft($row_form['gks_lang'],'gks_nomoi','nomos_descr',$row['ma_nomos_id'],$row['nomos_descr']);
  $row['nomos_descr_dest']=gks_lang_pft($row_form['gks_lang'],'gks_nomoi','nomos_descr',$row['destination_data_nomos_id'],$row['nomos_descr_dest']);
  $row['aade_skopos_diakinisis_descr']=gks_lang_pft($row_form['gks_lang'],'gks_aade_skopos_diakinisis','aade_skopos_diakinisis_descr',$row['aade_skopos_diakinisis_id'],$row['aade_skopos_diakinisis_descr']);
  
  
  $row['country_name_en_US']='';$row['country_name_en_US_dest']='';
  gks_lang_data_obj_insert_to_row($row,'gks_country',array(
    array('id' => $row['ma_country_id'],               'filename'=>'country_name', 'new_field' => 'country_name_%s'),
    array('id' => $row['destination_data_country_id'], 'filename'=>'country_name', 'new_field' => 'country_name_%s_dest'),
  ));
  $row['nomos_descr_en_US']='';$row['nomos_descr_en_US_dest']='';
  gks_lang_data_obj_insert_to_row($row,'gks_nomoi',array(
    array('id' => $row['ma_nomos_id'],               'filename'=>'nomos_descr', 'new_field' => 'nomos_descr_%s'),
    array('id' => $row['destination_data_nomos_id'], 'filename'=>'nomos_descr', 'new_field' => 'nomos_descr_%s_dest'),
  ));
  //print '<pre>';print_r($row);die();
    

  $company_id=intval($row['company_id']);
  $company_sub_id=intval($row['company_sub_id']);
  $warehouses_id_from=intval($row['warehouses_id_from']);
  $warehouses_id_to=intval($row['warehouses_id_to']);
  
  $row_person=array();
  $row_person['id']=$row['user_id'];
  $row_person['nickname_add']=trim_gks($row['gks_nickname_add']);
  $row_person['nickname_edit']=trim_gks($row['gks_nickname_edit']);
  $row_person['nickname']=trim_gks($row['gks_nickname']);
  $row_person['email']=$row['user_email'];
  $row_person['url']=trim_gks($row['user_url']);
  $row_person['first_name']=$row['user_first_name'];
  $row_person['last_name']=$row['user_last_name'];
  $row_person['phone']=$row['phone_home'];
  $row_person['mobile']=$row['user_mobile'];
  $row_person['user_lang']=$row['user_lang'];
  $row_person['eponimia']=$row['eponimia'];
  $row_person['title']=$row['title'];
  $row_person['afm']=$row['afm'];
  $row_person['doy']=$row['doy'];
  $row_person['epaggelma']=$row['epaggelma'];
  $row_person['odos']=$row['ma_odos'];
  $row_person['arithmos']=$row['ma_arithmos'];
  $row_person['orofos']=$row['ma_orofos'];
  $row_person['perioxi']=$row['ma_perioxi'];
  $row_person['poli']=$row['ma_poli'];
  $row_person['tk']=$row['ma_tk'];
  $row_person['country_id']=$row['ma_country_id'];
  $row_person['country_name']=$row['country_name'];
  $row_person['country_name_en']=$row['country_name_en_US'];
  $row_person['country_initials']=$row['country_initials'];
  $row_person['country_initials3']=$row['country_initials3'];
  $row_person['country_ee']=$row['country_ee'];
  $row_person['nomos_id']=$row['ma_nomos_id'];
  $row_person['nomos_descr']=$row['nomos_descr'];
  $row_person['nomos_descr_en']=$row['nomos_descr_en_US'];
  $row_person['pricelist_id']=$row['pricelist_id'];
  $row_person['pricelist']=$row['pricelist_descr'];
  $row_person['fiscal_position_id']=$row['fiscal_position_id'];
  $row_person['fiscal_position']=$row['fiscal_position_descr'];
  $row_person['antisimvalomenos_label']=$row['antisimvalomenos_label'];
  $row_person['antisimvalomenos_label_en']=$row['antisimvalomenos_label_en'];
  

  $row_person['address_text']=gks_print_address_text($row);
  $row_person['dest_name']=$row['destination_data_name'];
  $row_person['dest_phone']=$row['destination_data_phone'];
  $row_person['dest_odos']=$row['destination_data_odos'];
  $row_person['dest_arithmos']=$row['destination_data_arithmos'];
  $row_person['dest_orofos']=$row['destination_data_orofos'];
  $row_person['dest_perioxi']=$row['destination_data_perioxi'];
  $row_person['dest_poli']=$row['destination_data_poli'];
  $row_person['dest_tk']=$row['destination_data_tk'];
  $row_person['dest_country_id']=$row['destination_data_country_id'];
  $row_person['dest_country_name']=$row['country_name_dest'];
  $row_person['dest_country_name_en']=$row['country_name_en_US_dest'];
  $row_person['dest_nomos_id']=$row['destination_data_nomos_id'];
  $row_person['dest_nomos_descr']=$row['nomos_descr_dest'];
  $row_person['dest_nomos_descr_en']=$row['nomos_descr_en_US_dest'];

  //print '<pre>';print $row['nomos_descr_en_US'];die();
  if ($row['address_extra'] < 1) {
    $row_person['dest_name']='';
    $row_person['dest_phone']='';
    $row_person['dest_odos']='';
    $row_person['dest_arithmos']='';
    $row_person['dest_orofos']='';
    $row_person['dest_perioxi']='';
    $row_person['dest_poli']='';
    $row_person['dest_tk']='';
    $row_person['dest_country_id']=0;
    $row_person['dest_country_name']='';
    $row_person['dest_country_name_en']='';
    $row_person['dest_nomos_id']=0;
    $row_person['dest_nomos_descr']='';
    $row_person['dest_nomos_descr_en']='';    
  }

  $row_doc=array();
  $row_doc['title']=$row['acc_journal_descr'];
  $row_doc['title_pre']='';
  
  if ($row_form['gks_lang']=='el-GR') {
    if ($row['inv_state']=='010draft') $row_doc['title_pre']=gks_lang('Πρόχειρο','part4','getAccInvStateDescr_title_pre');
    else if ($row['inv_state']=='040cancelled') $row_doc['title_pre']=gks_lang('Ακυρωμένο','part4','getAccInvStateDescr_title_pre');
    else if ($row['inv_state']=='050proinvoice') $row_doc['title_pre']=gks_lang('Προτιμολόγιο','part4','getAccInvStateDescr_title_pre');
    else if ($row['inv_state']=='080listing') $row_doc['title_pre']=gks_lang('Καταχωρημένο','part4','getAccInvStateDescr_title_pre');
  } else {
    if ($row['inv_state']=='010draft') $row_doc['title_pre']='Draft';
    else if ($row['inv_state']=='040cancelled') $row_doc['title_pre']='Cancelled';
    else if ($row['inv_state']=='050proinvoice') $row_doc['title_pre']='Proinvoice';
    else if ($row['inv_state']=='080listing') $row_doc['title_pre']='Listing';
  }
  $row_doc['company']=$row['company_title'];
  if (trim_gks($row['company_sub_title'])!='') $row_doc['company'].=' \ '.$row['company_sub_title'];
  
  $row_doc['company_title']=$row['company_title'];
  $row_doc['company_sub_title']=trim_gks($row['company_sub_title']);
  if ($row_doc['company_sub_title']=='') $row_doc['company_sub_title']=gks_lang('Κεντρικό');
  
  $row_doc['date']=myDateTimeFormat(_time_user(strtotime($row['inv_date']),1));
  $row_doc['date_d']=myDateFormat(_time_user(strtotime($row['inv_date']),1));
  $row_doc['date_dw']=myDateFormatw(_time_user(strtotime($row['inv_date']),1),$row_form['gks_lang']);
  $row_doc['date_dt']=myDateTimeFormat(_time_user(strtotime($row['inv_date']),1));
  $row_doc['date_dtw']=myDateTimeFormatw(_time_user(strtotime($row['inv_date']),1),$row_form['gks_lang']);
  $row_doc['date_dtt']=myDateTimeFormatText(_time_user(strtotime($row['inv_date']),1),$row_form['gks_lang']);
  $row_doc['datefull']=showDate(strtotime($row['inv_date']),'d/m/Y H:i:s',1);
  
  $row_doc['mydate_add']=showDate(strtotime($row['mydate_add']),'d/m/Y H:i',1);
  $row_doc['mydate_edit']=showDate(strtotime($row['mydate_edit']),'d/m/Y H:i',1);
  $row_doc['seira']=trim_gks($row['seira_code']);
  $row_doc['seira_descr']=trim_gks($row['seira_descr']);
  $row_doc['number']=trim_gks($row['inv_acc_number_int']);
  $row_doc['number_str']=trim_gks($row['inv_acc_number_str']);
  $row_doc['mark']=trim_gks($row['aade_invoicemark']);
  $row_doc['aade_qrurl']=trim_gks($row['aade_qrurl']);
  $row_doc['aade_paroxos_qrurl']=trim_gks($row['aade_paroxos_qrurl']);
  $row_doc['paroxos_tf1_url']=trim_gks($row['paroxos_tf1_url']);
  $row_doc['aade_invoiceuid']=trim_gks($row['aade_invoiceuid']);
  $row_doc['paroxos_authenticationCode']=trim_gks($row['paroxos_authenticationCode']);
  $row_doc['products_posotita']=trim_gks($row['products_posotita']);
  $row_doc['gks_price_original_net']=trim_gks($row['gks_price_original_net']);
  $row_doc['gks_price_net']=trim_gks($row['gks_price_net']);
  $row_doc['gks_price_fpa']=trim_gks($row['gks_price_fpa']);
  $row_doc['gks_price_netfpa']=trim_gks($row['gks_price_netfpa']);
  $row_doc['gks_price_total']=trim_gks($row['gks_price_total']);
  $row_doc['totalWithheldAmount']=trim_gks($row['totalWithheldAmount']);
  $row_doc['totalOtherTaxesAmount']=trim_gks($row['totalOtherTaxesAmount']);
  $row_doc['totalStampDutyamount']=trim_gks($row['totalStampDutyamount']);
  $row_doc['totalFeesAmount']=trim_gks($row['totalFeesAmount']);
  $row_doc['totalDeductionsAmount']=trim_gks($row['totalDeductionsAmount']);

  $row_person['person_balance_before']=gks_balance_calc(['id' => $row['user_id'], 'except_id_acc_inv' => $row['id_acc_inv']]);
  $row_person['person_balance_after']=gks_balance_calc(['id' => $row['user_id']]);
  
  $enarji_apostolis='';
  if (isset($row['dispatch_date'])) $enarji_apostolis=showDate(strtotime($row['dispatch_date']), 'd/m/Y', 0);
  if (isset($row['dispatch_time'])) $enarji_apostolis.=' '.showDate(strtotime($row['dispatch_time']), 'H:i', 0);
  $row_doc['enarji_apostolis']=     $enarji_apostolis;
  $row_doc['enarji_apostolis_date']=(isset($row['dispatch_date']) ? showDate(strtotime($row['dispatch_date']), 'd/m/Y', 0) : '');
  $row_doc['enarji_apostolis_time']=(isset($row['dispatch_time']) ? showDate(strtotime($row['dispatch_time']), 'H:i', 0) : '');
  $row_doc['arithmos_oximatos']=trim_gks($row['vehicle_number']);
  
  $row_doc['isdeliverynote']=$row['seira_isdeliverynote'];
  $row_doc['isdeliverynote_display']=(intval($row['seira_isdeliverynote'])==1 ? '' : 'none');
  
  $row_doc['load_branch']=trim_gks($row['load_branch']);
  $row_doc['load_odos']=trim_gks($row['load_odos']);
  $row_doc['load_arithmos']=trim_gks($row['load_arithmos']);
  $row_doc['load_orofos']=trim_gks($row['load_orofos']);
  $row_doc['load_perioxi']=trim_gks($row['load_perioxi']);
  $row_doc['load_poli']=trim_gks($row['load_poli']);
  $row_doc['load_tk']=trim_gks($row['load_tk']);
  $row_doc['country_name_load']=trim_gks($row['country_name_load']);
  $row_doc['nomos_descr_load']=trim_gks($row['nomos_descr_load']);

  $row_doc['deli_branch']=trim_gks($row['deli_branch']);
  $row_doc['deli_odos']=trim_gks($row['deli_odos']);
  $row_doc['deli_arithmos']=trim_gks($row['deli_arithmos']);
  $row_doc['deli_orofos']=trim_gks($row['deli_orofos']);
  $row_doc['deli_perioxi']=trim_gks($row['deli_perioxi']);
  $row_doc['deli_poli']=trim_gks($row['deli_poli']);
  $row_doc['deli_tk']=trim_gks($row['deli_tk']);
  $row_doc['country_name_deli']=trim_gks($row['country_name_deli']);
  $row_doc['nomos_descr_deli']=trim_gks($row['nomos_descr_deli']);

  
  
  $row_doc['skopos_diakinisis']=trim_gks($row['aade_skopos_diakinisis_descr']);
  if ($row['aade_skopos_diakinisis_code']==19 and trim_gks($row['aade_skopos_19_descr'])!='') {
    $row_doc['skopos_diakinisis']=trim_gks($row['aade_skopos_19_descr']);
  }  
  
  
  $row_doc['tropos_pliromis']=trim_gks($row['payment_acquirer_name']);
  $row_doc['tropos_pliromis_via']=trim_gks($row['payment_acquirer_name']);
  if (trim_gks($row['tropos_pliromis_via'])!='') {
    $row_doc['tropos_pliromis_via']=trim_gks($row['tropos_pliromis_via']).' via '.trim_gks($row['payment_acquirer_name']);
  }
  
  $row_doc['tropos_apostolis']=trim_gks($row['delivery_method_name']);
  
  $row_doc['arithmos_aposolis']=trim_gks($row['delivery_number']);
  $row_doc['note_doc']=trim_gks($row['note_doc']);
  $row_doc['note_production']='';
  $row_doc['note_logistirio']=trim_gks($row['note_logistirio']);
  $row_doc['ddate']='';
  $row_doc['occasion_title']='';
  $row_doc['occasion_type_descr']='';
  $row_doc['occasion_mydate_add']= '';


  $row_doc['idiotites_text']=gks_print_idiotites_text($row);
  $row_doc['photos']='';
  $row_doc['links']='';
  
  $sql_terminal_ids="SELECT gks_eftpos_transaction.terminalId
  FROM gks_acc_inv_payment 
  LEFT JOIN gks_eftpos_transaction ON gks_acc_inv_payment.transaction_id = gks_eftpos_transaction.id_eftpos_transaction
  WHERE gks_acc_inv_payment.acc_inv_id=".$id."
  AND gks_eftpos_transaction.terminalId<>''
  order by gks_eftpos_transaction.terminalId";
  $result_terminal_ids = $db_link->query($sql_terminal_ids);        
  if (!$result_terminal_ids) {debug_mail(false,'error sql',$sql_terminal_ids);die('sql error');}
  $temp=[];
  while ($row_terminal_ids = $result_terminal_ids->fetch_assoc()) {
    $temp[]=$row_terminal_ids['terminalId'];
  }
  $row_doc['terminal_ids']=implode(', ',$temp);
    
  $rbs_code_a=intval($row['rbs_code_a']);
  //print '<pre>';print $rbs_code_a;die();
  if ($rbs_code_a>0 and $row['inv_acc_number_int']>0) {
    $afm_pelati=trim_gks($row['afm']);
    if ($rbs_code_a==173 or $rbs_code_a==215) $afm_pelati=gks_lang('Λιανικής');
    
    $row_doc['rbs_stream']=array(
         
      'afm_ekdosi'=>trim_gks($row['company_afm']),  //AFM ekdoti                                                                                                                           
      'afm_pelati'=>$afm_pelati,                    //AFM pelati (An den yparchei prosthetoume kati alfarithmitiko p. ch. A1)                                                              
      'karta' => '',                                //arithmos kartas syllogis apodeixeon (Proairetiko pedio) – Stin periptosi tou eidikou akyrotikou ischyei to AKYROTIKO_PARAKRATISI. pdf
      'rbs_code_a'=>trim_gks($rbs_code_a),          //kodikos parastatikou (Pinakas D1 (POL1220 & 1221). pdf)                                                                              
      'seira'=>trim_gks($row['seira_code']),        //seira parastatikou (An den yfistatai, anaferoume to "ANEY")                                                                          
      'aa'=>trim_gks($row['inv_acc_number_int']),   //AA seiras parastatikou                                                                                                               
      'net_a'=>0,                                   //katharo A (6%)                                                                                                                       
      'net_b'=>0,                                   //katharo B (13%)                                                                                                                      
      'net_c'=>0,                                   //katharo C (24%)                                                                                                                      
      'net_d'=>0,                                   //katharo D (36%)                                                                                                                      
      'net_e'=>0,                                   //katharo E (0%)                                                                                                                       
      'fpa_a'=>0,                                   //FPA A (6%)                                                                                                                           
      'fpa_b'=>0,                                   //FPA B (13%)                                                                                                                          
      'fpa_c'=>0,                                   //FPA C (24%)                                                                                                                          
      'fpa_d'=>0,                                   //FPA D (36%)                                                                                                                          
      'total'=>0,                                   //geniko synolo parastatikou                                                                                                           
      'currency'=> '0',                             //kodikos nomismatos (Gia to Evro einai: 0)                                                                                            
    );
    
    
    if ($rbs_code_a==215) {
      
      $sql_rbs_cancel="SELECT gks_acc_inv.id_acc_inv,  gks_acc_inv.inv_acc_seira_code, gks_acc_inv.inv_acc_number_int, gks_acc_eidi_parastatikon.rbs_code_a
      FROM ((gks_acc_inv 
      LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira) 
      LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
      LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou
      WHERE id_acc_inv>0 and id_acc_inv=".$row['cancel_for_acc_inv_id'];
      $result_rbs_cancel = $db_link->query($sql_rbs_cancel);        
      if (!$result_rbs_cancel) {debug_mail(false,'error sql',$sql_rbs_cancel);$ret['message']='sql error'; return $ret;}
      if ($result_rbs_cancel->num_rows!=1) {$ret['message']=gks_lang('Δεν βρέθηκε το παραστατικό που θα ακυρωθεί με ID').' <b>'.$row['cancel_for_acc_inv_id'].'</b>';debug_mail(false,$ret['message'],$sql); return $ret;}
      $row_rbs_cancel = $result_rbs_cancel->fetch_assoc();
      $row_doc['rbs_stream']['cancel_doc']=array(
        'id_acc_inv' => intval($row_rbs_cancel['id_acc_inv']),
        'inv_acc_seira_code' =>trim_gks($row_rbs_cancel['inv_acc_seira_code']),
        'inv_acc_number_int' =>trim_gks($row_rbs_cancel['inv_acc_number_int']),
        'rbs_code_a'=>trim_gks($row_rbs_cancel['rbs_code_a']),
      );
    }
    //print '<pre>';print_r($row_doc['rbs_stream']);die();
    
  }
  
  

  

  
  
  $cancel_for_acc_inv_id=intval($row['cancel_for_acc_inv_id']);  
  $row_canceled_doc=array();
  
  if ($cancel_for_acc_inv_id>0) {
    $sql_canceled="SELECT gks_acc_inv.inv_acc_journal_id, gks_acc_journal.acc_journal_descr, gks_acc_journal.acc_eidos_parastatikou_id, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, 
    gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
    gks_acc_inv.inv_acc_number_int, gks_acc_inv.inv_acc_number_str, gks_acc_inv.inv_acc_ekdosi_date, gks_acc_inv.inv_date,gks_acc_inv.inv_state,
    
    company_title,company_sub_title,
    aade_invoicemark,aade_qrurl,aade_paroxos_qrurl,paroxos_tf1_url,
    aade_invoiceuid,paroxos_authenticationCode
    
    FROM (((((gks_acc_inv 
    LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
    LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira)
    LEFT JOIN gks_company on gks_acc_inv.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_acc_inv.company_sub_id = gks_company_subs.id_company_sub
    where gks_acc_inv.id_acc_inv=".$cancel_for_acc_inv_id;
    $result_canceled = $db_link->query($sql_canceled);        
    if (!$result_canceled) {debug_mail(false,'error sql',$sql_canceled);die('sql error');}
    if ($result_canceled->num_rows!=1) {
      debug_mail(false,'record parent not found sql',$sql_canceled); 
      die('no record found');
    }
    $row_canceled = $result_canceled->fetch_assoc();
    $row_canceled['acc_journal_descr']=gks_lang_pft($row_form['gks_lang'],'gks_acc_journal','acc_journal_descr',$row_canceled['inv_acc_journal_id'],$row_canceled['acc_journal_descr']);
    $row_canceled_doc['display']=''; //diladi, orato
    $row_canceled_doc['title']=$row_canceled['acc_journal_descr'];
    $row_canceled_doc['title_pre']='';
    
    if ($row_form['gks_lang']=='el-GR') {
      if ($row_canceled['inv_state']=='010draft') $row_canceled_doc['title_pre']=gks_lang('Πρόχειρο','part4','getAccInvStateDescr_title_pre');
      else if ($row_canceled['inv_state']=='040cancelled') $row_canceled_doc['title_pre']=gks_lang('Ακυρωμένο','part4','getAccInvStateDescr_title_pre');
      else if ($row_canceled['inv_state']=='050proinvoice') $row_canceled_doc['title_pre']=gks_lang('Προτιμολόγιο','part4','getAccInvStateDescr_title_pre');
      else if ($row_canceled['inv_state']=='080listing') $row_canceled_doc['title_pre']=gks_lang('Καταχωρημένο','part4','getAccInvStateDescr_title_pre');
    } else {
      if ($row_canceled['inv_state']=='010draft') $row_canceled_doc['title_pre']='Draft';
      else if ($row_canceled['inv_state']=='040cancelled') $row_canceled_doc['title_pre']='Cancelled';
      else if ($row_canceled['inv_state']=='050proinvoice') $row_canceled_doc['title_pre']='Proinvoice';
      else if ($row_canceled['inv_state']=='080listing') $row_canceled_doc['title_pre']='Listing';
    }
    $row_canceled_doc['company']=$row_canceled['company_title'];
    if (trim_gks($row_canceled['company_sub_title'])!='') $row_canceled_doc['company'].=' \ '.$row_canceled['company_sub_title'];
    
    $row_canceled_doc['date']=showDate(strtotime($row_canceled['inv_date']),'d/m/Y',1);
    $row_canceled_doc['datefull']=showDate(strtotime($row_canceled['inv_date']),'d/m/Y H:i:s',1);
    $row_canceled_doc['seira']=trim_gks($row_canceled['seira_code']);
    $row_canceled_doc['seira_descr']=trim_gks($row_canceled['seira_descr']);
    $row_canceled_doc['number']=intval($row_canceled['inv_acc_number_int']);
    $row_canceled_doc['number_str']=trim_gks($row_canceled['inv_acc_number_str']);
    $row_canceled_doc['mark']=trim_gks($row_canceled['aade_invoicemark']);
    $row_canceled_doc['aade_qrurl']=trim_gks($row_canceled['aade_qrurl']);
    $row_canceled_doc['aade_paroxos_qrurl']=trim_gks($row_canceled['aade_paroxos_qrurl']);
    $row_canceled_doc['paroxos_tf1_url']=trim_gks($row_canceled['paroxos_tf1_url']);
    $row_canceled_doc['aade_invoiceuid']=trim_gks($row_canceled['aade_invoiceuid']);
    $row_canceled_doc['paroxos_authenticationCode']=trim_gks($row_canceled['paroxos_authenticationCode']);
  } else {
    $row_canceled_doc['display']='none';
    $row_canceled_doc['title']='';
    $row_canceled_doc['title_pre']='';
    $row_canceled_doc['company']='';
    $row_canceled_doc['date']='';
    $row_canceled_doc['datefull']='';
    $row_canceled_doc['seira']='';
    $row_canceled_doc['seira_descr']='';
    $row_canceled_doc['number']=0;
    $row_canceled_doc['number_str']='';
    $row_canceled_doc['mark']='';
    $row_canceled_doc['aade_qrurl']='';
    $row_canceled_doc['aade_paroxos_qrurl']='';
    $row_canceled_doc['paroxos_tf1_url']='';
    $row_canceled_doc['aade_invoiceuid']='';
    $row_canceled_doc['paroxos_authenticationCode']='';
  }
  
  
  //print'<pre>';print_r($row);die();
  
  $credit_memo_for_acc_inv_id=intval($row['credit_memo_for_acc_inv_id']);  
  $row_credit_doc=array();
  $row_credit_doc['display']='none';
  if ($credit_memo_for_acc_inv_id>0) {
    $sql_credit="SELECT gks_acc_inv.inv_acc_journal_id, gks_acc_journal.acc_journal_descr, gks_acc_journal.acc_eidos_parastatikou_id, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, 
    gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
    gks_acc_inv.inv_acc_number_int, gks_acc_inv.inv_acc_number_str, gks_acc_inv.inv_acc_ekdosi_date, gks_acc_inv.inv_date,gks_acc_inv.inv_state,
    
    company_title,company_sub_title,
    aade_invoicemark,aade_qrurl,aade_paroxos_qrurl,paroxos_tf1_url,
    aade_invoiceuid,paroxos_authenticationCode
    
    FROM (((((gks_acc_inv 
    LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
    LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira)
    LEFT JOIN gks_company on gks_acc_inv.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_acc_inv.company_sub_id = gks_company_subs.id_company_sub
    where gks_acc_inv.id_acc_inv=".$credit_memo_for_acc_inv_id;
    $result_credit = $db_link->query($sql_credit);        
    if (!$result_credit) {debug_mail(false,'error sql',$sql_credit);die('sql error');}
    if ($result_credit->num_rows!=1) {
      debug_mail(false,'record parent not found sql',$sql_credit); 
      die('no record found');
    }
    $row_credit = $result_credit->fetch_assoc();
    $row_credit['acc_journal_descr']=gks_lang_pft($row_form['gks_lang'],'gks_acc_journal','acc_journal_descr',$row_credit['inv_acc_journal_id'],$row_credit['acc_journal_descr']);
    $row_credit_doc['display']=''; //diladi, orato
    $row_credit_doc['title']=$row_credit['acc_journal_descr'];
    $row_credit_doc['title_pre']='';
    
    if ($row_form['gks_lang']=='el-GR') {
      if ($row_credit['inv_state']=='010draft') $row_credit_doc['title_pre']=gks_lang('Πρόχειρο','part4','getAccInvStateDescr_title_pre');
      else if ($row_credit['inv_state']=='040cancelled') $row_credit_doc['title_pre']=gks_lang('Ακυρωμένο','part4','getAccInvStateDescr_title_pre');
      else if ($row_credit['inv_state']=='050proinvoice') $row_credit_doc['title_pre']=gks_lang('Προτιμολόγιο','part4','getAccInvStateDescr_title_pre');
      else if ($row_credit['inv_state']=='080listing') $row_credit_doc['title_pre']=gks_lang('Καταχωρημένο','part4','getAccInvStateDescr_title_pre');
    } else {
      if ($row_credit['inv_state']=='010draft') $row_credit_doc['title_pre']='Draft';
      else if ($row_credit['inv_state']=='040cancelled') $row_credit_doc['title_pre']='Cancelled';
      else if ($row_credit['inv_state']=='050proinvoice') $row_credit_doc['title_pre']='Proinvoice';
      else if ($row_credit['inv_state']=='080listing') $row_credit_doc['title_pre']='Listing';
    }
    $row_credit_doc['company']=$row_credit['company_title'];
    if (trim_gks($row_credit['company_sub_title'])!='') $row_credit_doc['company'].=' \ '.$row_credit['company_sub_title'];
    
    $row_credit_doc['date']=showDate(strtotime($row_credit['inv_date']),'d/m/Y',1);
    $row_credit_doc['datefull']=showDate(strtotime($row_credit['inv_date']),'d/m/Y H:i:s',1);
    $row_credit_doc['seira']=trim_gks($row_credit['seira_code']);
    $row_credit_doc['seira_descr']=trim_gks($row_credit['seira_descr']);
    $row_credit_doc['number']=intval($row_credit['inv_acc_number_int']);
    $row_credit_doc['number_str']=trim_gks($row_credit['inv_acc_number_str']);
    $row_credit_doc['mark']=trim_gks($row_credit['aade_invoicemark']);
    $row_credit_doc['aade_qrurl']=trim_gks($row_credit['aade_qrurl']);
    $row_credit_doc['aade_paroxos_qrurl']=trim_gks($row_credit['aade_paroxos_qrurl']);
    $row_credit_doc['paroxos_tf1_url']=trim_gks($row_credit['paroxos_tf1_url']);
    $row_credit_doc['aade_invoiceuid']=trim_gks($row_credit['aade_invoiceuid']);
    $row_credit_doc['paroxos_authenticationCode']=trim_gks($row_credit['paroxos_authenticationCode']);
    
  } else {
    $row_credit_doc['display']='none';
    $row_credit_doc['title']='';
    $row_credit_doc['title_pre']='';
    $row_credit_doc['company']='';
    $row_credit_doc['date']='';
    $row_credit_doc['datefull']='';
    $row_credit_doc['seira']='';
    $row_credit_doc['seira_descr']='';
    $row_credit_doc['number']=0;
    $row_credit_doc['number_str']='';
    $row_credit_doc['mark']='';
    $row_credit_doc['aade_qrurl']='';
    $row_credit_doc['aade_paroxos_qrurl']='';
    $row_credit_doc['paroxos_tf1_url']='';
    $row_credit_doc['aade_invoiceuid']='';
    $row_credit_doc['paroxos_authenticationCode']='';
  }  
  
  //warehouse_from
  $row_doc['warehouse_from_hide_if_is_other']='';
  $row_doc['warehouse_from_name']='';
  $row_doc['warehouse_from_phone']='';
  $row_doc['warehouse_from_odos']='';
  $row_doc['warehouse_from_arithmos']='';
  $row_doc['warehouse_from_orofos']='';
  $row_doc['warehouse_from_perioxi']='';
  $row_doc['warehouse_from_tk']='';
  $row_doc['warehouse_from_poli']='';
  $row_doc['warehouse_from_nomos_descr']='';
  $row_doc['warehouse_from_country_name']='';
  
  if ($warehouses_id_from>0) {
    $sql="SELECT gks_warehouses.*, gks_nomoi.nomos_descr, gks_country.country_name
    FROM (gks_warehouses 
    LEFT JOIN gks_nomoi ON gks_warehouses.warehouse_nomos_id = gks_nomoi.id_nomos) 
    LEFT JOIN gks_country ON gks_warehouses.warehouse_country_id = gks_country.id_country
    WHERE gks_warehouses.is_virtual=0 and gks_warehouses.id_warehouse=".$warehouses_id_from;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
    if ($result->num_rows==1) {
      $row = $result->fetch_assoc();
      if (!($company_id!=$row['company_id'] or $company_sub_id!=$row['company_sub_id'])) {
        $row_doc['warehouse_from_hide_if_is_other']='display:none;';
      }
      $row_doc['warehouse_from_name']=$row['warehouse_name'];
      $row_doc['warehouse_from_topos_fortosis']=$row['warehouse_topos_fortosis'];
      $row_doc['warehouse_from_phone']=$row['warehouse_phone'];
      $row_doc['warehouse_from_odos']=$row['warehouse_odos'];
      $row_doc['warehouse_from_arithmos']=$row['warehouse_arithmos'];
      $row_doc['warehouse_from_orofos']=$row['warehouse_orofos'];
      $row_doc['warehouse_from_perioxi']=$row['warehouse_perioxi'];
      $row_doc['warehouse_from_tk']=$row['warehouse_tk'];
      $row_doc['warehouse_from_poli']=$row['warehouse_poli'];
      $row_doc['warehouse_from_nomos_descr']=$row['nomos_descr'];
      $row_doc['warehouse_from_country_name']=$row['country_name'];
      //echo '<pre>';print_r($row_doc);die();
      
    }
  }
  //echo '<pre>www|'.$warehouses_id_from.'|'.$row_doc['warehouse_from_name'];die();
  //warehouse_to
  $row_doc['warehouse_to_hide_if_is_other']='';
  $row_doc['warehouse_to_name']='';
  $row_doc['warehouse_to_phone']='';
  $row_doc['warehouse_to_odos']='';
  $row_doc['warehouse_to_arithmos']='';
  $row_doc['warehouse_to_orofos']='';
  $row_doc['warehouse_to_perioxi']='';
  $row_doc['warehouse_to_tk']='';
  $row_doc['warehouse_to_poli']='';
  $row_doc['warehouse_to_nomos_descr']='';
  $row_doc['warehouse_to_country_name']='';
  
  if ($warehouses_id_to>0) {
    $sql="SELECT gks_warehouses.*, gks_nomoi.nomos_descr, gks_country.country_name
    FROM (gks_warehouses 
    LEFT JOIN gks_nomoi ON gks_warehouses.warehouse_nomos_id = gks_nomoi.id_nomos) 
    LEFT JOIN gks_country ON gks_warehouses.warehouse_country_id = gks_country.id_country
    WHERE gks_warehouses.is_virtual=0 and gks_warehouses.id_warehouse=".$warehouses_id_to;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
    if ($result->num_rows==1) {
      $row = $result->fetch_assoc();
      
      if (!($company_id!=$row['company_id'] or $company_sub_id!=$row['company_sub_id'])) {
        $row_doc['warehouse_to_hide_if_is_other']='display:none;';
      }
      $row_doc['warehouse_to_name']=$row['warehouse_name'];
      $row_doc['warehouse_to_topos_fortosis']=$row['warehouse_topos_fortosis'];
      $row_doc['warehouse_to_phone']=$row['warehouse_phone'];
      $row_doc['warehouse_to_odos']=$row['warehouse_odos'];
      $row_doc['warehouse_to_arithmos']=$row['warehouse_arithmos'];
      $row_doc['warehouse_to_orofos']=$row['warehouse_orofos'];
      $row_doc['warehouse_to_perioxi']=$row['warehouse_perioxi'];
      $row_doc['warehouse_to_tk']=$row['warehouse_tk'];
      $row_doc['warehouse_to_poli']=$row['warehouse_poli'];
      $row_doc['warehouse_to_nomos_descr']=$row['nomos_descr'];
      $row_doc['warehouse_to_country_name']=$row['country_name'];
      //echo '<pre>';print_r($row_doc);die();
    }
  }    
  
  $sql="SELECT 
  CASE
    WHEN gks_eshop_products.product_class='variable_item' THEN
      CASE
        WHEN gks_eshop_products.product_photo<>'' THEN
          gks_eshop_products.product_photo
        ELSE
          gks_eshop_products_parent.product_photo
      END
    ELSE gks_eshop_products.product_photo

  END as product_photo_p,
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
  END as product_descr_p,  
  CASE
    WHEN gks_eshop_products.product_class='variable_item' THEN
      CASE
        WHEN gks_eshop_products.product_descr_small<>'' THEN
          gks_eshop_products.product_descr_small
        ELSE
          CASE
            WHEN gks_eshop_products.product_descr_variable<>'' THEN
              CONCAT_WS(' ', gks_eshop_products_parent.product_descr_small, gks_eshop_products.product_descr_variable)
            ELSE
              gks_eshop_products_parent.product_descr_small
          END
      END
    ELSE gks_eshop_products.product_descr_small
  END as product_descr_small_p,  
  gks_acc_inv_products.*, 
      
  gks_eshop_products.product_code, 
  gks_eshop_products.product_sku,
  gks_eshop_products.product_gtin,
  gks_eshop_products.product_upc,
  gks_eshop_products.product_ean,
  gks_eshop_products.product_isbn,
  gks_eshop_products.product_taric,  
  gks_eshop_products.product_photo, 
  gks_eshop_products.product_descr_big, 
  gks_monades_metrisis.monada_descr, 
  gks_monades_metrisis.monada_symbol,
  gks_eshop_fpa.fpa_descr_print, gks_eshop_fpa.fpa_pososto,
  gks_eshop_pricelist.pricelist_descr,
  gks_aade_katigoria_parakratoumemenon_foron.aade_katigoria_parakratoumemenon_foron_type, 
  gks_aade_katigoria_parakratoumemenon_foron.aade_katigoria_parakratoumemenon_foron_descr,
  gks_aade_katigoria_loipon_foron.aade_katigoria_loipon_foron_type,
  gks_aade_katigoria_loipon_foron.aade_katigoria_loipon_foron_descr,
  gks_aade_katigoria_xartosimou.aade_katigoria_xartosimou_type,
  gks_aade_katigoria_xartosimou.aade_katigoria_xartosimou_descr,
  
  gks_aade_katigoria_telon.aade_katigoria_telon_type, 
  gks_aade_katigoria_telon.aade_katigoria_telon_descr,
  gks_aade_katigoria_fpa_ejeresi.aade_katigoria_fpa_ejeresi_descr
  FROM (((((((((gks_acc_inv_products 
  LEFT JOIN gks_eshop_products ON gks_acc_inv_products.product_id = gks_eshop_products.id_product) 
  LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product)
  LEFT JOIN gks_monades_metrisis ON gks_acc_inv_products.product_monada_id = gks_monades_metrisis.id_monada) 
  LEFT JOIN gks_eshop_fpa ON gks_acc_inv_products.product_fpa_id = gks_eshop_fpa.id_fpa) 
  LEFT JOIN gks_eshop_pricelist ON gks_acc_inv_products.product_pricelist_item_id = gks_eshop_pricelist.id_pricelist)
  LEFT JOIN gks_aade_katigoria_parakratoumemenon_foron ON gks_acc_inv_products.product_withheldPercentCategory = gks_aade_katigoria_parakratoumemenon_foron.id_aade_katigoria_parakratoumemenon_foron) 
  LEFT JOIN gks_aade_katigoria_loipon_foron ON gks_acc_inv_products.product_otherTaxesPercentCategory = gks_aade_katigoria_loipon_foron.id_aade_katigoria_loipon_foron)
  LEFT JOIN gks_aade_katigoria_xartosimou ON gks_acc_inv_products.product_stampDutyPercentCategory = gks_aade_katigoria_xartosimou.id_aade_katigoria_xartosimou)
  LEFT JOIN gks_aade_katigoria_telon ON gks_acc_inv_products.product_feesPercentCategory = gks_aade_katigoria_telon.id_aade_katigoria_telon) 
  LEFT JOIN gks_aade_katigoria_fpa_ejeresi ON gks_acc_inv_products.product_fpa_ejeresi_id = gks_aade_katigoria_fpa_ejeresi.id_aade_katigoria_fpa_ejeresi
  
  WHERE gks_acc_inv_products.acc_inv_id=".$id."
  ORDER BY gks_acc_inv_products.product_aa;";

   
  
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
  if ($result->num_rows==0) {$ret['message']=gks_lang('Δεν βρέθηκαν είδη για το παραστατικό με ID').' <b>'.$id.'</b>';debug_mail(false,$ret['message'],$sql); return $ret;}
  
  $row_eidoi=array();
  $id_product_array_ids=array();
  while ($eidos = $result->fetch_assoc()) {
    if ($eidos['product_id']==2) $eidos['product_id']=0;
    
    $product_photo=trim_gks($eidos['product_photo']);
    $eidos['product_photo']='';
    if ($product_photo!='') {
      $full_product_photo=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.$product_photo;
      if (file_exists($full_product_photo)) {
        $eidos['product_photo']=GKS_SITE_URL.substr($product_photo, 1);
      }
    }
    $eidos['id_order_product'] = $eidos['id_acc_inv_product'];

    $eidos['monada_descr']=gks_lang_pft($row_form['gks_lang'],'gks_monades_metrisis','monada_descr',$eidos['product_monada_id'],$eidos['monada_descr']);
    $eidos['monada_symbol']=gks_lang_pft($row_form['gks_lang'],'gks_monades_metrisis','monada_symbol',$eidos['product_monada_id'],$eidos['monada_symbol']);
    
    $id_product_array_ids[]=$eidos['id_order_product'];
    $row_eidoi[]=$eidos;
  }
  //print '<pre>';print_r($row_eidoi);die();

  //$timemmm=time();
  $gks_custom_prepare = gks_custom_table_item_prepare('gks_eshop_products',['from'=>'print']);
  if ($gks_custom_prepare['success']==true) {
    foreach ($row_eidoi as $pkey => $eidos) {
      $custom_row['id_product']=$eidos['product_id'];
      $gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$custom_row);
      if ($gks_custom_row['success']) {
        //print '<pre>';print_r($gks_custom_row);die();
        
        foreach ($gks_custom_row['fields'] as $key => $cf_item) {
          $row_eidoi[$pkey]['custom_'.$key]=array(
            'type'  => $cf_item['field_type_id'],
            'value' => $cf_item['print'],
          );
        } 
      }
    }
    //echo '<pre>'.(time()-$timemmm);die();
    //echo '<pre>';print_r($row_doc);die();
    //print_r($gks_custom_row);
  }
  //print '<pre>';print_r($row_eidoi);die();
    
  $products_lots_serials=array();
  if (count($id_product_array_ids)>0) {
    if ($GKS_PRODUCT_LOTS_SERIALS) {
      $sql_lots_serials="SELECT 
      gks_acc_inv_products_lots.lot_product_id,
      acc_inv_product_id as id, 
      lot_product_quantity,
      gks_eshop_product_lots.lot_name, 
      gks_eshop_product_lots.lot_descr, 
      gks_eshop_product_lots.lot_date_production, 
      gks_eshop_product_lots.lot_date_expire, 
      gks_eshop_product_lots.lot_disabled
      FROM gks_acc_inv_products_lots
      LEFT JOIN gks_eshop_product_lots ON gks_acc_inv_products_lots.lot_product_id = gks_eshop_product_lots.id_lot_product
      WHERE gks_acc_inv_products_lots.acc_inv_product_id In (".implode(',',$id_product_array_ids).")
      ORDER BY gks_acc_inv_products_lots.id_acc_inv_product_lots";
      $result_lots_serials = $db_link->query($sql_lots_serials);        
      if (!$result_lots_serials) {debug_mail(false,'error sql',$sql_lots_serials); die('sql error');}
      while ($row_lots_serials = $result_lots_serials->fetch_assoc()) {
        $products_lots_serials[$row_lots_serials['id']][]=$row_lots_serials;
      }
      
      //echo '<pre>';print_r($products_lots_serials);die();
      
    }    
  }  
  
  //print '<pre>';print_r($row_eidoi);die();
  
  
  $row_fpa=array();
  foreach ($row_eidoi as $eidos) {
    if ($eidos['product_fpa_pososto']!=0) {
      if (isset($row_fpa[$eidos['product_fpa_pososto']])==false) {
        $row_fpa[$eidos['product_fpa_pososto']]=array('aa' => 1, 'pososto' => $eidos['product_fpa_pososto'], 'net'=> 0, 'fpa'=>0);
      }
      $row_fpa[$eidos['product_fpa_pososto']]['net']+=$eidos['product_price_final_all_net'];
      $row_fpa[$eidos['product_fpa_pososto']]['fpa']+=$eidos['product_price_final_all_fpa'];
    }
  } 
  //print '<pre>'; print_r($row_fpa); die();
  
//  if (isset($row_doc['rbs_stream'])) {
//    $row_doc['rbs_stream']['total']=0;
//    foreach ($row_fpa as $fpa_anas) {
//      $row_doc['rbs_stream']['total']+=$fpa_anas['net']+$fpa_anas['fpa'];
//      $pososto=$fpa_anas['pososto'];
//      switch ($pososto) {
//        case 0.06:
//          $row_doc['rbs_stream']['net_a']+=$fpa_anas['net'];                               //katharo A (6%)
//          $row_doc['rbs_stream']['fpa_a']+=$fpa_anas['fpa'];                               //FPA A (6%)
//          break;
//        case 0.13:
//          $row_doc['rbs_stream']['net_b']+=$fpa_anas['net'];                               //katharo B (13%)
//          $row_doc['rbs_stream']['fpa_b']+=$fpa_anas['fpa'];                               //FPA Β (13%)
//          break;
//        case 0.24:
//          $row_doc['rbs_stream']['net_c']+=$fpa_anas['net'];                               //katharo C (24%)
//          $row_doc['rbs_stream']['fpa_c']+=$fpa_anas['fpa'];                               //FPA C (24%)
//          break;
//        case 0.36:
//          $row_doc['rbs_stream']['net_d']+=$fpa_anas['net'];                               //katharo D (36%)
//          $row_doc['rbs_stream']['fpa_d']+=$fpa_anas['fpa'];                               //FPA D (36%)
//          break;
//        case 0:
//          $row_doc['rbs_stream']['net_e']+=$fpa_anas['net'];                               //katharo E (0%)
//          break;
//        default:
//          $ret['message']=gks_lang('Άγνωστο ΦΠΑ').' '.$pososto.' '.gks_lang('για το RBS Stream').':<pre>'.print_r($row_doc['rbs_stream'],true).'</pre>';
//          debug_mail(false,$ret['message'],print_r($row_doc,true)); 
//          return $ret;
//          break;
//      }
//      
//        
//    }
//    //print '<pre>';print_r($row_doc['rbs_stream']);die();
//  }
  
  $row_foroi=array();
  
  $row_foroi[1]=array(
    'descr'=>gks_lang('Παρακρατούμενος Φόρος'),
    'net' => 0,
    'foros' => 0,
  );
  $row_foroi[2]=array(
    'descr'=>gks_lang('Λοιποί Φόροι'),
    'net' => 0,
    'foros' => 0,
  );
  $row_foroi[3]=array(
    'descr'=>gks_lang('Ψηφιακό Τέλος συναλλαγής'),
    'net' => 0,
    'foros' => 0,
  );
  $row_foroi[4]=array(
    'descr'=>gks_lang('Τέλη'),
    'net' => 0,
    'foros' => 0,
  );
  $row_foroi[5]=array(
    'descr'=>gks_lang('Κρατήσεις'),
    'net' => 0,
    'foros' => 0,
  );
  
  foreach ($row_eidoi as $eidos) {
    if ($eidos['product_withheldAmount']!=0) {
      $row_foroi[1]['net']+=$eidos['product_price_final_all_net'];
      $row_foroi[1]['foros']+=$eidos['product_withheldAmount'];
    }
    if ($eidos['product_otherTaxesAmount']!=0) {
      $row_foroi[2]['net']+=$eidos['product_price_final_all_net'];
      $row_foroi[2]['foros']+=$eidos['product_otherTaxesAmount'];
    }
    if ($eidos['product_stampDutyAmount']!=0) {
      $row_foroi[3]['net']+=$eidos['product_price_final_all_net'];
      $row_foroi[3]['foros']+=$eidos['product_stampDutyAmount'];
    }
    if ($eidos['product_feesAmount']!=0) {
      $row_foroi[4]['net']+=$eidos['product_price_final_all_net'];
      $row_foroi[4]['foros']+=$eidos['product_feesAmount'];
    }
    if ($eidos['product_deductionsAmount']!=0) {
      $row_foroi[5]['net']+=$eidos['product_price_final_all_net'];
      $row_foroi[5]['foros']+=$eidos['product_deductionsAmount'];
    }
  }  
  
  if ($row_foroi[1]['foros']==0) unset($row_foroi[1]);
  if ($row_foroi[2]['foros']==0) unset($row_foroi[2]);
  if ($row_foroi[3]['foros']==0) unset($row_foroi[3]);
  if ($row_foroi[4]['foros']==0) unset($row_foroi[4]);
  if ($row_foroi[5]['foros']==0) unset($row_foroi[5]);
  
  $gks_custom_prepare = gks_custom_table_item_prepare('gks_acc_inv',['from'=>'print']);
  if ($gks_custom_prepare['success']==true) {
    $custom_row['id_acc_inv']=$id;
    $gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$custom_row);
    if ($gks_custom_row['success']) {
      foreach ($gks_custom_row['fields'] as $key => $cf_item) {
        $row_doc['custom_'.$key]=array(
          'type'  => $cf_item['field_type_id'],
          'value' => $cf_item['print'],
        );
      } 
    }
    //echo '<pre>';print_r($row_doc);die();
    //print_r($gks_custom_row);
  }
  

  $gks_custom_prepare = gks_custom_table_item_prepare('wp_users',['from'=>'print']);
  if ($gks_custom_prepare['success']==true) {
    $custom_row['ID']=$row_person['id'];
    $gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$custom_row);
    if ($gks_custom_row['success']) {
      foreach ($gks_custom_row['fields'] as $key => $cf_item) {
        $row_person['custom_'.$key]=array(
          'type'  => $cf_item['field_type_id'],
          'value' => $cf_item['print'],
        );
      } 
    }
    //echo '<pre>';print_r($row_person);die();
    //print_r($gks_custom_row);
  }
    
    
  $company_ret=gks_print_form_company_data($company_id,$company_sub_id,$options,$row_form);
  if ($company_ret['success']==false) {$ret['message']=$company_ret['message'];return $ret;}
  
  return gks_print_form_make_print($row_form,$row,$row_doc,$row_canceled_doc,$row_credit_doc,$row_eidoi,$row_fpa,$row_foroi,$company_ret['data'],$row_person,$options,$products_lots_serials,[]);
}

function gks_print_form_gks_acc_pay($id,$row_form,$options) {
  global $db_link;
  
  $ret=array('success' => false, 'message' => 'gks_acc_pay generic error');
 
   
  $sql="SELECT gks_acc_pay.*, 
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
  ".GKS_WP_TABLE_PREFIX."users_print.gks_nickname AS gks_nickname_print,
  gks_company.company_afm,gks_company.company_title,gks_company_subs.company_sub_title,
  gks_users.order_sxolio,gks_users.pelati_sxolio,
  gks_acc_journal.acc_journal_descr, gks_acc_seires.seira_code, gks_acc_seires.seira_descr,gks_acc_seires.is_xeirografi,
  gks_acc_journal.acc_eidos_parastatikou_id,
  eidos_parastatikou_type_id, antisimvalomenos_label, eidos_parastatikou_need_prev, eidos_parastatikou_has_fpa,eidos_parastatikou_has_othertaxes, 
  eidos_parastatikou_has_esoda, eidos_parastatikou_has_eksoda,eidos_parastatikou_need_afm,eidos_parastatikou_balance_pros,
  gks_users.eponimia,gks_users.title,gks_users.afm,gks_users.doy,gks_users.epaggelma,
  gks_users.ma_odos,gks_users.ma_arithmos,
  gks_users.ma_orofos,gks_users.ma_perioxi,gks_users.ma_poli,gks_users.ma_tk,
  gks_users.ma_country_id,gks_users.ma_nomos_id,
  gks_country.country_name,gks_nomoi.nomos_descr,
  table_last_name.mylast_name as user_last_name, table_first_name.myfirst_name as user_first_name,
  ".GKS_WP_TABLE_PREFIX."users.user_email, ".GKS_WP_TABLE_PREFIX."users.gks_mobile as user_mobile,
  gks_lang.lang_name, ".GKS_WP_TABLE_PREFIX."users.gks_lang as user_lang,
  
  
  gks_country.country_initials,gks_country.country_initials3,gks_country.country_ee,
  antisimvalomenos_label_en,".GKS_WP_TABLE_PREFIX."users.user_url,gks_users.phone_home
    
  FROM (((((((((((((((gks_acc_pay
  
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_acc_pay.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_acc_pay.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_acc_pay.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_print ON gks_acc_pay.print_user_id = ".GKS_WP_TABLE_PREFIX."users_print.ID) 
  LEFT JOIN gks_company on gks_acc_pay.company_id = gks_company.id_company)
  LEFT JOIN gks_company_subs on gks_acc_pay.company_sub_id = gks_company_subs.id_company_sub)
  LEFT JOIN gks_acc_journal ON gks_acc_pay.pay_acc_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
  LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
  LEFT JOIN gks_acc_seires ON gks_acc_pay.pay_acc_seira_id = gks_acc_seires.id_acc_seira)
  LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
  LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country)
  LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos)
  LEFT JOIN (
    SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS myfirst_name
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='first_name'))
  )  AS table_first_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_first_name.user_id) 
  LEFT JOIN (
    SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mylast_name
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='last_name'))
  )  AS table_last_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_last_name.user_id) 
  LEFT JOIN gks_lang ON ".GKS_WP_TABLE_PREFIX."users.gks_lang = gks_lang.id_lang
  
  where gks_acc_pay.id_acc_pay = ".$id;
  
  //print '<pre>'; print_r($sql);die();
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
  if ($result->num_rows!=1) {$ret['message']=gks_lang('Δεν βρέθηκε το παραστατικό με ID').' <b>'.$id.'</b>';debug_mail(false,$ret['message'],$sql); return $ret;}
  $row = $result->fetch_assoc();
  
  $row['acc_journal_descr']=gks_lang_pft($row_form['gks_lang'],'gks_acc_journal','acc_journal_descr',$row['pay_acc_journal_id'],$row['acc_journal_descr']);
  $row['country_name']=gks_lang_pft($row_form['gks_lang'],'gks_country','country_name',$row['ma_country_id'],$row['country_name']);
  $row['nomos_descr']=gks_lang_pft($row_form['gks_lang'],'gks_nomoi','nomos_descr',$row['ma_nomos_id'],$row['nomos_descr']);

  $row['country_name_en_US']='';
  gks_lang_data_obj_insert_to_row($row,'gks_country',array(
    array('id' => $row['ma_country_id'], 'filename'=>'country_name', 'new_field' => 'country_name_%s'),
  ));
  $row['nomos_descr_en_US']='';
  gks_lang_data_obj_insert_to_row($row,'gks_nomoi',array(
    array('id' => $row['ma_nomos_id'],               'filename'=>'nomos_descr', 'new_field' => 'nomos_descr_%s'),
  ));  
  //print '<pre>';print_r($row);die();

  $company_id=intval($row['company_id']);
  $company_sub_id=intval($row['company_sub_id']);

  $row_person=array();
  $row_person['id']=$row['user_id'];
  $row_person['nickname_add']=trim_gks($row['gks_nickname_add']);
  $row_person['nickname_edit']=trim_gks($row['gks_nickname_edit']);
  $row_person['nickname']=trim_gks($row['gks_nickname']);
  $row_person['email']=$row['user_email'];
  $row_person['url']=trim_gks($row['user_url']);
  $row_person['first_name']=$row['user_first_name'];
  $row_person['last_name']=$row['user_last_name'];
  $row_person['phone']=$row['phone_home'];
  $row_person['mobile']=$row['user_mobile'];
  $row_person['user_lang']=$row['user_lang'];
  $row_person['eponimia']=$row['eponimia'];
  $row_person['title']=$row['title'];
  $row_person['afm']=$row['afm'];
  $row_person['doy']=$row['doy'];
  $row_person['epaggelma']=$row['epaggelma'];
  $row_person['odos']=$row['ma_odos'];
  $row_person['arithmos']=$row['ma_arithmos'];
  $row_person['orofos']=$row['ma_orofos'];
  $row_person['perioxi']=$row['ma_perioxi'];
  $row_person['poli']=$row['ma_poli'];
  $row_person['tk']=$row['ma_tk'];
  $row_person['country_id']=$row['ma_country_id'];
  $row_person['country_name']=$row['country_name'];
  $row_person['country_name_en']=$row['country_name_en_US'];
  $row_person['country_initials']=$row['country_initials'];
  $row_person['country_initials3']=$row['country_initials3'];
  $row_person['country_ee']=$row['country_ee'];
  $row_person['nomos_id']=$row['ma_nomos_id'];
  $row_person['nomos_descr']=$row['nomos_descr'];
  $row_person['nomos_descr_en']=$row['nomos_descr_en_US'];
  $row_person['antisimvalomenos_label']=$row['antisimvalomenos_label'];
  $row_person['antisimvalomenos_label_en']=$row['antisimvalomenos_label_en'];
  
  $row_person['address_text']=gks_print_address_text($row);


  $row_doc=array();
  $row_doc['title']=$row['acc_journal_descr'];
  $row_doc['title_pre']='';
  
  if ($row_form['gks_lang']=='el-GR') {
    if ($row['pay_state']=='010draft') $row_doc['η']=gks_lang('Πρόχειρη','part4','getAccPayStateDescr_title_pre');
    else if ($row['pay_state']=='040cancelled') $row_doc['title_pre']=gks_lang('Ακυρωμένη','part4','getAccPayStateDescr_title_pre');
    else if ($row['pay_state']=='080listing') $row_doc['title_pre']=gks_lang('Καταχωρημένη','part4','getAccPayStateDescr_title_pre');
  } else {
    if ($row['pay_state']=='010draft') $row_doc['title_pre']='Draft';
    else if ($row['pay_state']=='040cancelled') $row_doc['title_pre']='Cancelled';
    else if ($row['pay_state']=='080listing') $row_doc['title_pre']='Listing';
  }
  $row_doc['company']=$row['company_title'];
  if (trim_gks($row['company_sub_title'])!='') $row_doc['company'].=' \ '.$row['company_sub_title'];
  
  $row_doc['company_title']=$row['company_title'];
  $row_doc['company_sub_title']=trim_gks($row['company_sub_title']);
  if ($row_doc['company_sub_title']=='') $row_doc['company_sub_title']=gks_lang('Κεντρικό');
  
  $row_doc['date']=myDateTimeFormat(_time_user(strtotime($row['pay_date']),1));
  $row_doc['date_d']=myDateFormat(_time_user(strtotime($row['pay_date']),1));
  $row_doc['date_dw']=myDateFormatw(_time_user(strtotime($row['pay_date']),1),$row_form['gks_lang']);
  $row_doc['date_dt']=myDateTimeFormat(_time_user(strtotime($row['pay_date']),1));
  $row_doc['date_dtw']=myDateTimeFormatw(_time_user(strtotime($row['pay_date']),1),$row_form['gks_lang']);
  $row_doc['date_dtt']=myDateTimeFormatText(_time_user(strtotime($row['pay_date']),1),$row_form['gks_lang']);
  $row_doc['datefull']=showDate(strtotime($row['pay_date']),'d/m/Y H:i:s',1);
  
  $row_doc['mydate_add']=showDate(strtotime($row['mydate_add']),'d/m/Y H:i',1);
  $row_doc['mydate_edit']=showDate(strtotime($row['mydate_edit']),'d/m/Y H:i',1);
  $row_doc['seira']=trim_gks($row['seira_code']);
  $row_doc['seira_descr']=trim_gks($row['seira_descr']);
  $row_doc['number']=trim_gks($row['pay_acc_number_int']);
  $row_doc['number_str']=trim_gks($row['pay_acc_number_str']);
  $row_doc['mark']=trim_gks($row['aade_invoicemark']);
  $row_doc['aade_qrurl']=trim_gks($row['aade_qrurl']);
  $row_doc['aade_paroxos_qrurl']=trim_gks($row['aade_paroxos_qrurl']);
  $row_doc['paroxos_tf1_url']=trim_gks($row['paroxos_tf1_url']);
  $row_doc['aade_invoiceuid']=trim_gks($row['aade_invoiceuid']);
  $row_doc['paroxos_authenticationCode']=trim_gks($row['paroxos_authenticationCode']);
  $row_doc['gks_price_total']=trim_gks($row['gks_price_total']);
  $row_doc['note_doc']=trim_gks($row['note_doc']);
  $row_doc['note_production']='';
  $row_doc['note_logistirio']=trim_gks($row['note_logistirio']);
  $row_doc['ddate']='';
  $row_doc['occasion_title']='';
  $row_doc['occasion_type_descr']='';
  $row_doc['occasion_mydate_add']= '';

  $row_doc['idiotites_text']=gks_print_idiotites_text($row);
  $row_doc['photos']='';
  $row_doc['links']='';

  $row_person['person_balance_before']=gks_balance_calc(['id' => $row['user_id'], 'except_id_acc_pay' => $row['id_acc_pay']]);
  $row_person['person_balance_after']=gks_balance_calc(['id' => $row['user_id']]);

  
  //print'<pre>';print_r($row);die();
  
  $row_canceled_doc=array();
  $row_canceled_doc['display']='none';
  
  $credit_memo_for_acc_pay_id=intval($row['credit_memo_for_acc_pay_id']);  
  $row_credit_doc=array();
  $row_credit_doc['display']='none';
  if ($credit_memo_for_acc_pay_id>0) {
    $sql_credit="SELECT gks_acc_pay.pay_acc_journal_id, gks_acc_journal.acc_journal_descr, gks_acc_journal.acc_eidos_parastatikou_id, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, 
    gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
    gks_acc_pay.pay_acc_number_int, gks_acc_pay.pay_acc_number_str, gks_acc_pay.pay_acc_ekdosi_date, gks_acc_pay.pay_date,gks_acc_pay.pay_state,
    
    company_title,company_sub_title
    
    FROM (((((gks_acc_pay 
    LEFT JOIN gks_acc_journal ON gks_acc_pay.pay_acc_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
    LEFT JOIN gks_acc_seires ON gks_acc_pay.pay_acc_seira_id = gks_acc_seires.id_acc_seira)
    LEFT JOIN gks_company on gks_acc_pay.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_acc_pay.company_sub_id = gks_company_subs.id_company_sub
    where gks_acc_pay.id_acc_pay=".$credit_memo_for_acc_pay_id;
    $result_credit = $db_link->query($sql_credit);        
    if (!$result_credit) {debug_mail(false,'error sql',$sql_credit);die('sql error');}
    if ($result_credit->num_rows!=1) {
      debug_mail(false,'record parent not found sql',$sql_credit); 
      die('no record found');
    }
    $row_credit = $result_credit->fetch_assoc();
    $row_credit['acc_journal_descr']=gks_lang_pft($row_form['gks_lang'],'gks_acc_journal','acc_journal_descr',$row_credit['pay_acc_journal_id'],$row_credit['acc_journal_descr']);
    $row_credit_doc['display']=''; //diladi, orato
    $row_credit_doc['title']=$row_credit['acc_journal_descr'];
    $row_credit_doc['title_pre']='';
    
    if ($row_form['gks_lang']=='el-GR') {
      if ($row_credit['pay_state']=='010draft') $row_credit_doc['title_pre']=gks_lang('Πρόχειρη','part4','getAccPayStateDescr_title_pre');
      else if ($row_credit['pay_state']=='040cancelled') $row_credit_doc['title_pre']=gks_lang('Ακυρωμένη','part4','getAccPayStateDescr_title_pre');
      else if ($row_credit['pay_state']=='080listing') $row_credit_doc['title_pre']=gks_lang('Καταχωρημένη','part4','getAccPayStateDescr_title_pre');
    } else {
      if ($row_credit['pay_state']=='010draft') $row_credit_doc['title_pre']='Draft';
      else if ($row_credit['pay_state']=='040cancelled') $row_credit_doc['title_pre']='Cancelled';
      else if ($row_credit['pay_state']=='080listing') $row_credit_doc['title_pre']='Listing';
    }
    $row_credit_doc['company']=$row_credit['company_title'];
    if (trim_gks($row_credit['company_sub_title'])!='') $row_credit_doc['company'].=' \ '.$row_credit['company_sub_title'];
    
    $row_credit_doc['date']=showDate(strtotime($row_credit['pay_date']),'d/m/Y',1);
    $row_credit_doc['datefull']=showDate(strtotime($row_credit['pay_date']),'d/m/Y H:i:s',1);
    $row_credit_doc['seira']=trim_gks($row_credit['seira_code']);
    $row_credit_doc['seira_descr']=trim_gks($row_credit['seira_descr']);
    $row_credit_doc['number']=intval($row_credit['pay_acc_number_int']);
    $row_credit_doc['number_str']=trim_gks($row_credit['v_acc_number_str']);
    
    
  } else {
    $row_credit_doc['display']='none';
    $row_credit_doc['title']='';
    $row_credit_doc['title_pre']='';
    $row_credit_doc['company']='';
    $row_credit_doc['date']='';
    $row_credit_doc['datefull']='';
    $row_credit_doc['seira']='';
    $row_credit_doc['seira_descr']='';
    $row_credit_doc['number']=0;
    $row_credit_doc['number_str']='';
  }  
  
  
  //

  $sql="SELECT gks_acc_pay_method.*, gks_payment_acquirers.payment_acquirer_name
  FROM gks_acc_pay_method 
  LEFT JOIN gks_payment_acquirers ON gks_acc_pay_method.paymethod_id = gks_payment_acquirers.id_payment_acquirer
  WHERE gks_acc_pay_method.acc_pay_id=".$id."
  ORDER BY gks_acc_pay_method.paymethod_aa;";   
  
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
  if ($result->num_rows==0) {$ret['message']=gks_lang('Δεν βρέθηκαν κινήσεις για την πληρωμή με ID').' <b>'.$id.'</b>';debug_mail(false,$ret['message'],$sql); return $ret;}
  
  $row_eidoi=array();
  //$id_product_array_ids=array();
  while ($eidos = $result->fetch_assoc()) {
    $eidos['product_code']=$eidos['payment_acquirer_name'];
    $eidos['product_descr']=$eidos['paymethod_descr'];
    $eidos['product_comments']=$eidos['paymethod_comments'];
    $eidos['product_price_final_all_total']=$eidos['paymethod_total'];
    
    $eidos['id_order_product'] = $eidos['id_acc_pay_method'];
    //$id_product_array_ids[]=$eidos['id_order_product'];

    $eidos['product_code']=gks_lang_pft($row_form['gks_lang'],'gks_payment_acquirers','payment_acquirer_name',$eidos['paymethod_id'],$eidos['product_code']);

    
    $sql_terminal_ids="SELECT gks_eftpos_transaction.terminalId
    FROM gks_acc_pay_payment 
    LEFT JOIN gks_eftpos_transaction ON gks_acc_pay_payment.transaction_id = gks_eftpos_transaction.id_eftpos_transaction
    WHERE gks_acc_pay_payment.acc_pay_method_id=".$id."
    AND gks_eftpos_transaction.terminalId<>''
    ORDER BY gks_eftpos_transaction.terminalId";
    $result_terminal_ids = $db_link->query($sql_terminal_ids);        
    if (!$result_terminal_ids) {debug_mail(false,'error sql',$sql_terminal_ids);die('sql error');}
    $temp=[];
    while ($row_terminal_ids = $result_terminal_ids->fetch_assoc()) {
      $temp[]=$row_terminal_ids['terminalId'];
    }
    $eidos['terminal_ids']=implode(', ',$temp);


    $row_eidoi[]=$eidos;
  }
  
  
  $products_lots_serials=array();
  
  //print '<pre>';print_r($row_eidoi);die();
  
  $row_fpa=array();
  $row_foroi=array();  

  
  //print '<pre>'; print_r($row_fpa); die();
  

  $gks_custom_prepare = gks_custom_table_item_prepare('gks_acc_pay',['from'=>'print']);
  if ($gks_custom_prepare['success']==true) {
    $custom_row['id_acc_pay']=$id;
    $gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$custom_row);
    if ($gks_custom_row['success']) {
      foreach ($gks_custom_row['fields'] as $key => $cf_item) {
        $row_doc['custom_'.$key]=array(
          'type'  => $cf_item['field_type_id'],
          'value' => $cf_item['print'],
        );
      } 
    }
    //echo '<pre>';print_r($row_doc);die();
    //print_r($gks_custom_row);
  }  
 
  $gks_custom_prepare = gks_custom_table_item_prepare('wp_users',['from'=>'print']);
  if ($gks_custom_prepare['success']==true) {
    $custom_row['ID']=$row_person['id'];
    $gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$custom_row);
    if ($gks_custom_row['success']) {
      foreach ($gks_custom_row['fields'] as $key => $cf_item) {
        $row_person['custom_'.$key]=array(
          'type'  => $cf_item['field_type_id'],
          'value' => $cf_item['print'],
        );
      } 
    }
    //echo '<pre>';print_r($row_person);die();
    //print_r($gks_custom_row);
  }  
  
  $company_ret=gks_print_form_company_data($company_id,$company_sub_id,$options,$row_form);
  if ($company_ret['success']==false) {$ret['message']=$company_ret['message'];return $ret;}
  
  
  return gks_print_form_make_print($row_form,$row,$row_doc,$row_canceled_doc,$row_credit_doc,$row_eidoi,$row_fpa,$row_foroi,$company_ret['data'],$row_person,$options,$products_lots_serials,[]);
}

function gks_print_form_gks_whi_mov($id,$row_form,$options) {
  global $db_link;
  global $GKS_PRODUCT_LOTS_SERIALS;
  
  $ret=array('success' => false, 'message' => 'gks_whi_mov generic error');
 
  $sql="SELECT gks_whi_mov.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, ".GKS_WP_TABLE_PREFIX."users.user_url,
  gks_company.company_afm,gks_company.company_title,gks_company.company_tagline,gks_company_subs.company_sub_title,gks_company_subs.company_sub_tagline,
  gks_delivery_methods.delivery_method_name,
  gks_eshop_pricelist.pricelist_descr, gks_eshop_fiscal_position.fiscal_position_descr,
  gks_users.order_sxolio,gks_users.pelati_sxolio,gks_users.phone_home,
  gks_acc_journal.acc_journal_descr, gks_acc_seires.seira_code, gks_acc_seires.seira_descr,gks_acc_seires.is_xeirografi,
  gks_acc_journal.acc_eidos_parastatikou_id,
  eidos_parastatikou_type_id, antisimvalomenos_label, eidos_parastatikou_type_id, antisimvalomenos_label_en, 
  eidos_parastatikou_need_prev, eidos_parastatikou_has_fpa,eidos_parastatikou_has_othertaxes, 
  eidos_parastatikou_has_esoda, eidos_parastatikou_has_eksoda,eidos_parastatikou_need_afm,
  gks_lang.lang_name,
  gks_country.country_name,gks_country.country_initials,gks_country.country_initials3,gks_country.country_ee,
  gks_nomoi.nomos_descr,
  
  gks_country_dest.country_name as country_name_dest, 
  gks_nomoi_dest.nomos_descr as nomos_descr_dest, 
  gks_aade_skopos_diakinisis.aade_skopos_diakinisis_code, 
  gks_aade_skopos_diakinisis.aade_skopos_diakinisis_descr,

  gks_acc_seires.seira_isdeliverynote,
  gks_nomoi_load.nomos_descr as nomos_descr_load,
  gks_country_load.country_name as country_name_load,
  gks_nomoi_deli.nomos_descr as nomos_descr_deli,
  gks_country_deli.country_name as country_name_deli 
  
  FROM ((((((((((((((((((((((gks_whi_mov
  
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_whi_mov.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_whi_mov.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_whi_mov.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
  LEFT JOIN gks_company on gks_whi_mov.company_id = gks_company.id_company)
  LEFT JOIN gks_company_subs on gks_whi_mov.company_sub_id = gks_company_subs.id_company_sub)
  LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal)
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
  LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
  LEFT JOIN gks_acc_seires ON gks_whi_mov.mov_whi_seira_id = gks_acc_seires.id_acc_seira)
  LEFT JOIN gks_delivery_methods ON gks_whi_mov.tropos_apostolis = gks_delivery_methods.id_delivery_method)
  LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id)
  LEFT JOIN gks_eshop_fiscal_position ON gks_whi_mov.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
  LEFT JOIN gks_eshop_pricelist ON gks_whi_mov.pricelist_id = gks_eshop_pricelist.id_pricelist)
  LEFT JOIN gks_country ON gks_whi_mov.ma_country_id = gks_country.id_country)
  LEFT JOIN gks_nomoi ON gks_whi_mov.ma_nomos_id = gks_nomoi.id_nomos)
  LEFT JOIN gks_lang ON gks_whi_mov.user_lang = gks_lang.id_lang)
  LEFT JOIN gks_country AS gks_country_dest ON gks_whi_mov.destination_data_country_id = gks_country_dest.id_country) 
  LEFT JOIN gks_nomoi AS gks_nomoi_dest ON gks_whi_mov.destination_data_nomos_id = gks_nomoi_dest.id_nomos)
  LEFT JOIN gks_aade_skopos_diakinisis ON gks_whi_mov.aade_skopos_diakinisis_id = gks_aade_skopos_diakinisis.id_aade_skopos_diakinisis)

  LEFT JOIN gks_country as gks_country_load ON gks_whi_mov.load_country_id = gks_country_load.id_country)
  LEFT JOIN gks_nomoi as gks_nomoi_load ON gks_whi_mov.load_nomos_id = gks_nomoi_load.id_nomos)
  LEFT JOIN gks_country as gks_country_deli ON gks_whi_mov.deli_country_id = gks_country_deli.id_country)
  LEFT JOIN gks_nomoi as gks_nomoi_deli ON gks_whi_mov.deli_nomos_id = gks_nomoi_deli.id_nomos


  where gks_whi_mov.id_whi_mov = ".$id;
  //print '<pre>'; print_r($sql);die();
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
  if ($result->num_rows!=1) {$ret['message']=gks_lang('Δεν βρέθηκε το δελτίο αποστολής με ID').' <b>'.$id.'</b>';debug_mail(false,$ret['message'],$sql); return $ret;}
  $row = $result->fetch_assoc();

  $row['acc_journal_descr']=gks_lang_pft($row_form['gks_lang'],'gks_acc_journal','acc_journal_descr',$row['mov_whi_journal_id'],$row['acc_journal_descr']);
  $row['delivery_method_name']=gks_lang_pft($row_form['gks_lang'],'gks_delivery_methods','delivery_method_name',$row['tropos_apostolis'],$row['delivery_method_name']);
  $row['country_name']=gks_lang_pft($row_form['gks_lang'],'gks_country','country_name',$row['ma_country_id'],$row['country_name']);
  $row['country_name_dest']=gks_lang_pft($row_form['gks_lang'],'gks_country','country_name',$row['destination_data_country_id'],$row['country_name_dest']);
  $row['nomos_descr']=gks_lang_pft($row_form['gks_lang'],'gks_nomoi','nomos_descr',$row['ma_nomos_id'],$row['nomos_descr']);
  $row['nomos_descr_dest']=gks_lang_pft($row_form['gks_lang'],'gks_nomoi','nomos_descr',$row['destination_data_nomos_id'],$row['nomos_descr_dest']);
  $row['aade_skopos_diakinisis_descr']=gks_lang_pft($row_form['gks_lang'],'gks_aade_skopos_diakinisis','aade_skopos_diakinisis_descr',$row['aade_skopos_diakinisis_id'],$row['aade_skopos_diakinisis_descr']);

  $row['country_name_en_US']='';$row['country_name_en_US_dest']='';
  gks_lang_data_obj_insert_to_row($row,'gks_country',array(
    array('id' => $row['ma_country_id'],               'filename'=>'country_name', 'new_field' => 'country_name_%s'),
    array('id' => $row['destination_data_country_id'], 'filename'=>'country_name', 'new_field' => 'country_name_%s_dest'),
  ));
  $row['nomos_descr_en_US']='';$row['nomos_descr_en_US_dest']='';
  gks_lang_data_obj_insert_to_row($row,'gks_nomoi',array(
    array('id' => $row['ma_nomos_id'],               'filename'=>'nomos_descr', 'new_field' => 'nomos_descr_%s'),
    array('id' => $row['destination_data_nomos_id'], 'filename'=>'nomos_descr', 'new_field' => 'nomos_descr_%s_dest'),
  ));
    
  //print '<pre>';print_r($row);die();
  
  $company_id=intval($row['company_id']);
  $company_sub_id=intval($row['company_sub_id']);
  $warehouses_id_from=intval($row['warehouses_id_from']);
  $warehouses_id_to=intval($row['warehouses_id_to']);
  
  $row_person=array();
  $row_person['id']=$row['user_id'];
  $row_person['nickname_add']=trim_gks($row['gks_nickname_add']);
  $row_person['nickname_edit']=trim_gks($row['gks_nickname_edit']);
  $row_person['nickname']=trim_gks($row['gks_nickname']);
  $row_person['email']=$row['user_email'];
  $row_person['url']=trim_gks($row['user_url']);
  $row_person['first_name']=$row['user_first_name'];
  $row_person['last_name']=$row['user_last_name'];
  $row_person['phone']=$row['phone_home'];
  $row_person['mobile']=$row['user_mobile'];
  $row_person['user_lang']=$row['user_lang'];
  $row_person['eponimia']=$row['eponimia'];
  $row_person['title']=$row['title'];
  $row_person['afm']=$row['afm'];
  $row_person['doy']=$row['doy'];
  $row_person['epaggelma']=$row['epaggelma'];
  $row_person['odos']=$row['ma_odos'];
  $row_person['arithmos']=$row['ma_arithmos'];
  $row_person['orofos']=$row['ma_orofos'];
  $row_person['perioxi']=$row['ma_perioxi'];
  $row_person['poli']=$row['ma_poli'];
  $row_person['tk']=$row['ma_tk'];
  $row_person['country_id']=$row['ma_country_id'];
  $row_person['country_name']=$row['country_name'];
  $row_person['country_name_en']=$row['country_name_en_US'];
  $row_person['country_initials']=$row['country_initials'];
  $row_person['country_initials3']=$row['country_initials3'];
  $row_person['country_ee']=$row['country_ee'];
  $row_person['nomos_id']=$row['ma_nomos_id'];
  $row_person['nomos_descr']=$row['nomos_descr'];
  $row_person['nomos_descr_en']=$row['nomos_descr_en_US'];
  $row_person['pricelist_id']=$row['pricelist_id'];
  $row_person['pricelist']=$row['pricelist_descr'];
  $row_person['fiscal_position_id']=$row['fiscal_position_id'];
  $row_person['fiscal_position']=$row['fiscal_position_descr'];
  $row_person['antisimvalomenos_label']=$row['antisimvalomenos_label'];
  $row_person['antisimvalomenos_label_en']=$row['antisimvalomenos_label_en'];
  

  $row_person['address_text']=gks_print_address_text($row);
  $row_person['dest_name']=$row['destination_data_name'];
  $row_person['dest_phone']=$row['destination_data_phone'];
  $row_person['dest_odos']=$row['destination_data_odos'];
  $row_person['dest_arithmos']=$row['destination_data_arithmos'];
  $row_person['dest_orofos']=$row['destination_data_orofos'];
  $row_person['dest_perioxi']=$row['destination_data_perioxi'];
  $row_person['dest_poli']=$row['destination_data_poli'];
  $row_person['dest_tk']=$row['destination_data_tk'];
  $row_person['dest_country_id']=$row['destination_data_country_id'];
  $row_person['dest_country_name']=$row['country_name_dest'];
  $row_person['dest_country_name_en']=$row['country_name_en_US_dest'];
  $row_person['dest_nomos_id']=$row['destination_data_nomos_id'];
  $row_person['dest_nomos_descr']=$row['nomos_descr_dest'];
  $row_person['dest_nomos_descr_en']=$row['nomos_descr_en_US_dest'];

  if ($row['address_extra'] < 1) {
    $row_person['dest_name']='';
    $row_person['dest_phone']='';
    $row_person['dest_odos']='';
    $row_person['dest_arithmos']='';
    $row_person['dest_orofos']='';
    $row_person['dest_perioxi']='';
    $row_person['dest_poli']='';
    $row_person['dest_tk']='';
    $row_person['dest_country_id']=0;
    $row_person['dest_country_name']='';
    $row_person['dest_country_name_en']='';
    $row_person['dest_nomos_id']=0;
    $row_person['dest_nomos_descr']='';
    $row_person['dest_nomos_descr_en']='';    
  }

  $row_doc=array();
  $row_doc['title']=$row['acc_journal_descr'];
  $row_doc['title_pre']='';
  
  if ($row_form['gks_lang']=='el-GR') {
    if ($row['mov_state']=='010draft') $row_doc['title_pre']=gks_lang('Πρόχειρο','part4','getWhiMovStateDescr_title_pre');
    else if ($row['mov_state']=='040cancelled') $row_doc['title_pre']=gks_lang('Ακυρωμένο','part4','getWhiMovStateDescr_title_pre');
    else if ($row['mov_state']=='080listing') $row_doc['title_pre']=gks_lang('Καταχωρημένο','part4','getWhiMovStateDescr_title_pre');
  } else {
    if ($row['mov_state']=='010draft') $row_doc['title_pre']='Draft';
    else if ($row['mov_state']=='040cancelled') $row_doc['title_pre']='Cancelled';
    else if ($row['mov_state']=='080listing') $row_doc['title_pre']='Listing';
  }
  $row_doc['company']=$row['company_title'];
  if (trim_gks($row['company_sub_title'])!='') $row_doc['company'].=' \ '.$row['company_sub_title'];
  
  $row_doc['company_title']=$row['company_title'];
  $row_doc['company_sub_title']=trim_gks($row['company_sub_title']);
  if ($row_doc['company_sub_title']=='') $row_doc['company_sub_title']=gks_lang('Κεντρικό');
  
  
  $row_doc['date']=myDateTimeFormat(_time_user(strtotime($row['mov_date']),1));
  $row_doc['date_d']=myDateFormat(_time_user(strtotime($row['mov_date']),1));
  $row_doc['date_dw']=myDateFormatw(_time_user(strtotime($row['mov_date']),1),$row_form['gks_lang']);
  $row_doc['date_dt']=myDateTimeFormat(_time_user(strtotime($row['mov_date']),1));
  $row_doc['date_dtw']=myDateTimeFormatw(_time_user(strtotime($row['mov_date']),1),$row_form['gks_lang']);
  $row_doc['date_dtt']=myDateTimeFormatText(_time_user(strtotime($row['mov_date']),1),$row_form['gks_lang']);
  $row_doc['datefull']=showDate(strtotime($row['mov_date']),'d/m/Y H:i:s',1);
  
  $row_doc['mydate_add']=showDate(strtotime($row['mydate_add']),'d/m/Y H:i',1);
  $row_doc['mydate_edit']=showDate(strtotime($row['mydate_edit']),'d/m/Y H:i',1);
  $row_doc['seira']=trim_gks($row['seira_code']);
  $row_doc['seira_descr']=trim_gks($row['seira_descr']);
  $row_doc['number']=trim_gks($row['mov_whi_number_int']);
  $row_doc['number_str']=trim_gks($row['mov_whi_number_str']);
  $row_doc['mark']=trim_gks($row['aade_invoicemark']);
  $row_doc['aade_qrurl']=trim_gks($row['aade_qrurl']);
  $row_doc['aade_paroxos_qrurl']=trim_gks($row['aade_paroxos_qrurl']);
  $row_doc['paroxos_tf1_url']=trim_gks($row['paroxos_tf1_url']);
  $row_doc['aade_invoiceuid']=trim_gks($row['aade_invoiceuid']);
  $row_doc['paroxos_authenticationCode']=trim_gks($row['paroxos_authenticationCode']);

  $row_doc['products_posotita']=trim_gks($row['products_posotita']);

  $enarji_apostolis='';
  if (isset($row['dispatch_date'])) $enarji_apostolis=showDate(strtotime($row['dispatch_date']), 'd/m/Y', 0);
  if (isset($row['dispatch_time'])) $enarji_apostolis.=' '.showDate(strtotime($row['dispatch_time']), 'H:i', 0);
  $row_doc['enarji_apostolis']=     $enarji_apostolis;
  $row_doc['enarji_apostolis_date']=(isset($row['dispatch_date']) ? showDate(strtotime($row['dispatch_date']), 'd/m/Y', 0) : '');
  $row_doc['enarji_apostolis_time']=(isset($row['dispatch_time']) ? showDate(strtotime($row['dispatch_time']), 'H:i', 0) : '');

  $row_doc['arithmos_oximatos']=trim_gks($row['vehicle_number']);
  
  $row_doc['isdeliverynote']=$row['seira_isdeliverynote'];
  $row_doc['isdeliverynote_display']=(intval($row['seira_isdeliverynote'])==1 ? '' : 'none');
  
  $row_doc['load_branch']=trim_gks($row['load_branch']);
  $row_doc['load_odos']=trim_gks($row['load_odos']);
  $row_doc['load_arithmos']=trim_gks($row['load_arithmos']);
  $row_doc['load_orofos']=trim_gks($row['load_orofos']);
  $row_doc['load_perioxi']=trim_gks($row['load_perioxi']);
  $row_doc['load_poli']=trim_gks($row['load_poli']);
  $row_doc['load_tk']=trim_gks($row['load_tk']);
  $row_doc['country_name_load']=trim_gks($row['country_name_load']);
  $row_doc['nomos_descr_load']=trim_gks($row['nomos_descr_load']);

  $row_doc['deli_branch']=trim_gks($row['deli_branch']);
  $row_doc['deli_odos']=trim_gks($row['deli_odos']);
  $row_doc['deli_arithmos']=trim_gks($row['deli_arithmos']);
  $row_doc['deli_orofos']=trim_gks($row['deli_orofos']);
  $row_doc['deli_perioxi']=trim_gks($row['deli_perioxi']);
  $row_doc['deli_poli']=trim_gks($row['deli_poli']);
  $row_doc['deli_tk']=trim_gks($row['deli_tk']);
  $row_doc['country_name_deli']=trim_gks($row['country_name_deli']);
  $row_doc['nomos_descr_deli']=trim_gks($row['nomos_descr_deli']);

    
  
  
  $row_doc['skopos_diakinisis']=trim_gks($row['aade_skopos_diakinisis_descr']);
  if ($row['aade_skopos_diakinisis_code']==19 and trim_gks($row['aade_skopos_19_descr'])!='') {
    $row_doc['skopos_diakinisis']=trim_gks($row['aade_skopos_19_descr']);
  }
  
  $row_doc['tropos_apostolis']=trim_gks($row['delivery_method_name']);
  
  $row_doc['arithmos_aposolis']=trim_gks($row['delivery_number']);
  $row_doc['note_doc']=trim_gks($row['note_doc']);
  $row_doc['note_production']='';
  $row_doc['note_logistirio']=trim_gks($row['note_logistirio']);
  $row_doc['ddate']='';
  $row_doc['occasion_title']='';
  $row_doc['occasion_type_descr']='';
  $row_doc['occasion_mydate_add']= '';


  $row_doc['idiotites_text']=gks_print_idiotites_text($row);
  $row_doc['photos']='';
  $row_doc['links']='';


  $row_person['person_balance_before']=gks_balance_calc(['id' => $row['user_id'], 'except_id_whi_mov' => $row['id_whi_mov']]);
  $row_person['person_balance_after']=gks_balance_calc(['id' => $row['user_id']]);


    
  $cancel_for_whi_mov_id=intval($row['cancel_for_whi_mov_id']);  
  $row_canceled_doc=array();
  
  if ($cancel_for_whi_mov_id>0) {
    $sql_canceled="SELECT gks_whi_mov.mov_whi_journal_id,gks_acc_journal.acc_journal_descr, gks_acc_journal.acc_eidos_parastatikou_id, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, 
    gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
    gks_whi_mov.mov_whi_number_int, gks_whi_mov.mov_whi_number_str, gks_whi_mov.mov_whi_ekdosi_date, gks_whi_mov.mov_date,gks_whi_mov.mov_state,
    
    company_title,company_sub_title,
    aade_invoicemark,aade_qrurl,aade_paroxos_qrurl,paroxos_tf1_url,
    aade_invoiceuid,paroxos_authenticationCode
    
    FROM (((((gks_whi_mov 
    LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
    LEFT JOIN gks_acc_seires ON gks_whi_mov.mov_whi_seira_id = gks_acc_seires.id_acc_seira)
    LEFT JOIN gks_company on gks_whi_mov.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_whi_mov.company_sub_id = gks_company_subs.id_company_sub
    where gks_whi_mov.id_whi_mov=".$cancel_for_whi_mov_id;
    $result_canceled = $db_link->query($sql_canceled);        
    if (!$result_canceled) {debug_mail(false,'error sql',$sql_canceled);die('sql error');}
    if ($result_canceled->num_rows!=1) {
      debug_mail(false,'record parent not found sql',$sql_canceled); 
      die('no record found');
    }
    $row_canceled = $result_canceled->fetch_assoc();
    $row_canceled['acc_journal_descr']=gks_lang_pft($row_form['gks_lang'],'gks_acc_journal','acc_journal_descr',$row_canceled['mov_whi_journal_id'],$row_canceled['acc_journal_descr']);
    $row_canceled_doc['display']=''; //diladi, orato
    $row_canceled_doc['title']=$row_canceled['acc_journal_descr'];
    $row_canceled_doc['title_pre']='';
    
    if ($row_form['gks_lang']=='el-GR') {
      if ($row_canceled['mov_state']=='010draft') $row_canceled_doc['title_pre']=gks_lang('Πρόχειρο','part4','getWhiMovStateDescr_title_pre');
      else if ($row_canceled['mov_state']=='040cancelled') $row_canceled_doc['title_pre']=gks_lang('Ακυρωμένο','part4','getWhiMovStateDescr_title_pre');
      else if ($row_canceled['mov_state']=='080listing') $row_canceled_doc['title_pre']=gks_lang('Καταχωρημένο','part4','getWhiMovStateDescr_title_pre');
    } else {
      if ($row_canceled['mov_state']=='010draft') $row_canceled_doc['title_pre']='Draft';
      else if ($row_canceled['mov_state']=='040cancelled') $row_canceled_doc['title_pre']='Cancelled';
      else if ($row_canceled['mov_state']=='080listing') $row_canceled_doc['title_pre']='Listing';
    }
    $row_canceled_doc['company']=$row_canceled['company_title'];
    if (trim_gks($row_canceled['company_sub_title'])!='') $row_canceled_doc['company'].=' \ '.$row_canceled['company_sub_title'];
    
    $row_canceled_doc['date']=showDate(strtotime($row_canceled['mov_date']),'d/m/Y',1);
    $row_canceled_doc['datefull']=showDate(strtotime($row_canceled['mov_date']),'d/m/Y H:i:s',1);
    $row_canceled_doc['seira']=trim_gks($row_canceled['seira_code']);
    $row_canceled_doc['seira_descr']=trim_gks($row_canceled['seira_descr']);
    $row_canceled_doc['number']=intval($row_canceled['mov_whi_number_int']);
    $row_canceled_doc['number_str']=trim_gks($row_canceled['mov_whi_number_str']);
    $row_canceled_doc['mark']=trim_gks($row_canceled['aade_invoicemark']);
    $row_canceled_doc['aade_qrurl']=trim_gks($row_canceled['aade_qrurl']);
    $row_canceled_doc['aade_paroxos_qrurl']=trim_gks($row_canceled['aade_paroxos_qrurl']);
    $row_canceled_doc['paroxos_tf1_url']=trim_gks($row_canceled['paroxos_tf1_url']);
    $row_canceled_doc['aade_invoiceuid']=trim_gks($row_canceled['aade_invoiceuid']);
    $row_canceled_doc['paroxos_authenticationCode']=trim_gks($row_canceled['paroxos_authenticationCode']);
    
  } else {
    $row_canceled_doc['display']='none';
    $row_canceled_doc['title']='';
    $row_canceled_doc['title_pre']='';
    $row_canceled_doc['company']='';
    $row_canceled_doc['date']='';
    $row_canceled_doc['datefull']='';
    $row_canceled_doc['seira']='';
    $row_canceled_doc['seira_descr']='';
    $row_canceled_doc['number']=0;
    $row_canceled_doc['number_str']='';
    $row_canceled_doc['mark']='';
    $row_canceled_doc['aade_qrurl']='';
    $row_canceled_doc['aade_paroxos_qrurl']='';
    $row_canceled_doc['paroxos_tf1_url']='';
    $row_canceled_doc['aade_invoiceuid']='';
    $row_canceled_doc['paroxos_authenticationCode']='';
  }
  
  
  //print'<pre>';print_r($row);die();
  
  $credit_memo_for_whi_mov_id=intval($row['credit_memo_for_whi_mov_id']);  
  $row_credit_doc=array();
  $row_credit_doc['display']='none';
  if ($credit_memo_for_whi_mov_id>0) {
    $sql_credit="SELECT gks_whi_mov.mov_whi_journal_id, gks_acc_journal.acc_journal_descr, gks_acc_journal.acc_eidos_parastatikou_id, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, 
    gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, 
    gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
    gks_whi_mov.mov_whi_number_int, gks_whi_mov.mov_whi_number_str, gks_whi_mov.mov_whi_ekdosi_date, gks_whi_mov.mov_date,gks_whi_mov.mov_state,
    
    company_title,company_sub_title
    
    FROM (((((gks_whi_mov 
    LEFT JOIN gks_acc_journal ON gks_whi_mov.mov_whi_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
    LEFT JOIN gks_acc_seires ON gks_whi_mov.mov_whi_seira_id = gks_acc_seires.id_acc_seira)
    LEFT JOIN gks_company on gks_whi_mov.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_whi_mov.company_sub_id = gks_company_subs.id_company_sub
    where gks_whi_mov.id_whi_mov=".$credit_memo_for_whi_mov_id;
    $result_credit = $db_link->query($sql_credit);        
    if (!$result_credit) {debug_mail(false,'error sql',$sql_credit);die('sql error');}
    if ($result_credit->num_rows!=1) {
      debug_mail(false,'record parent not found sql',$sql_credit); 
      die('no record found');
    }
    $row_credit = $result_credit->fetch_assoc();
    $row_credit['acc_journal_descr']=gks_lang_pft($row_form['gks_lang'],'gks_acc_journal','acc_journal_descr',$row_credit['mov_whi_journal_id'],$row_credit['acc_journal_descr']);
    $row_credit_doc['display']=''; //diladi, orato
    $row_credit_doc['title']=$row_credit['acc_journal_descr'];
    $row_credit_doc['title_pre']='';
    
    if ($row_form['gks_lang']=='el-GR') {
      if ($row_credit['mov_state']=='010draft') $row_credit_doc['title_pre']=gks_lang('Πρόχειρο','part4','getWhiMovStateDescr_title_pre');
      else if ($row_credit['mov_state']=='040cancelled') $row_credit_doc['title_pre']=gks_lang('Ακυρωμένο','part4','getWhiMovStateDescr_title_pre');
      else if ($row_credit['mov_state']=='080listing') $row_credit_doc['title_pre']=gks_lang('Καταχωρημένο','part4','getWhiMovStateDescr_title_pre');
    } else {
      if ($row_credit['mov_state']=='010draft') $row_credit_doc['title_pre']='Draft';
      else if ($row_credit['mov_state']=='040cancelled') $row_credit_doc['title_pre']='Cancelled';
      else if ($row_credit['mov_state']=='080listing') $row_credit_doc['title_pre']='Listing';
    }
    $row_credit_doc['company']=$row_credit['company_title'];
    if (trim_gks($row_credit['company_sub_title'])!='') $row_credit_doc['company'].=' \ '.$row_credit['company_sub_title'];
    
    $row_credit_doc['date']=showDate(strtotime($row_credit['mov_date']),'d/m/Y',1);
    $row_credit_doc['datefull']=showDate(strtotime($row_credit['mov_date']),'d/m/Y H:i:s',1);
    $row_credit_doc['seira']=trim_gks($row_credit['seira_code']);
    $row_credit_doc['seira_descr']=trim_gks($row_credit['seira_descr']);
    $row_credit_doc['number']=intval($row_credit['mov_whi_number_int']);
    $row_credit_doc['number_str']=trim_gks($row_credit['mov_whi_number_str']);
    
  } else {
    $row_credit_doc['display']='none';
    $row_credit_doc['title']='';
    $row_credit_doc['title_pre']='';
    $row_credit_doc['company']='';
    $row_credit_doc['date']='';
    $row_credit_doc['datefull']='';
    $row_credit_doc['seira']='';
    $row_credit_doc['seira_descr']='';
    $row_credit_doc['number']=0;
    $row_credit_doc['number_str']='';
  }  
  
  
  //warehouse_from
  $row_doc['warehouse_from_hide_if_is_other']='';
  $row_doc['warehouse_from_name']='';
  $row_doc['warehouse_from_phone']='';
  $row_doc['warehouse_from_odos']='';
  $row_doc['warehouse_from_arithmos']='';
  $row_doc['warehouse_from_orofos']='';
  $row_doc['warehouse_from_perioxi']='';
  $row_doc['warehouse_from_tk']='';
  $row_doc['warehouse_from_poli']='';
  $row_doc['warehouse_from_nomos_descr']='';
  $row_doc['warehouse_from_country_name']='';
  
  if ($warehouses_id_from>0) {
    $sql="SELECT gks_warehouses.*, gks_nomoi.nomos_descr, gks_country.country_name
    FROM (gks_warehouses 
    LEFT JOIN gks_nomoi ON gks_warehouses.warehouse_nomos_id = gks_nomoi.id_nomos) 
    LEFT JOIN gks_country ON gks_warehouses.warehouse_country_id = gks_country.id_country
    WHERE gks_warehouses.is_virtual=0 and gks_warehouses.id_warehouse=".$warehouses_id_from;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
    if ($result->num_rows==1) {
      $row = $result->fetch_assoc();
      if (!($company_id!=$row['company_id'] or $company_sub_id!=$row['company_sub_id'])) {
        $row_doc['warehouse_from_hide_if_is_other']='display:none;';
      }      
      $row_doc['warehouse_from_name']=$row['warehouse_name'];
      $row_doc['warehouse_from_topos_fortosis']=$row['warehouse_topos_fortosis'];
      $row_doc['warehouse_from_phone']=$row['warehouse_phone'];
      $row_doc['warehouse_from_odos']=$row['warehouse_odos'];
      $row_doc['warehouse_from_arithmos']=$row['warehouse_arithmos'];
      $row_doc['warehouse_from_orofos']=$row['warehouse_orofos'];
      $row_doc['warehouse_from_perioxi']=$row['warehouse_perioxi'];
      $row_doc['warehouse_from_tk']=$row['warehouse_tk'];
      $row_doc['warehouse_from_poli']=$row['warehouse_poli'];
      $row_doc['warehouse_from_nomos_descr']=$row['nomos_descr'];
      $row_doc['warehouse_from_country_name']=$row['country_name'];
      //echo '<pre>';print_r($row_doc);die();
    }
  }

  //warehouse_to
  $row_doc['warehouse_to_hide_if_is_other']='';
  $row_doc['warehouse_to_name']='';
  $row_doc['warehouse_to_phone']='';
  $row_doc['warehouse_to_odos']='';
  $row_doc['warehouse_to_arithmos']='';
  $row_doc['warehouse_to_orofos']='';
  $row_doc['warehouse_to_perioxi']='';
  $row_doc['warehouse_to_tk']='';
  $row_doc['warehouse_to_poli']='';
  $row_doc['warehouse_to_nomos_descr']='';
  $row_doc['warehouse_to_country_name']='';
  
  if ($warehouses_id_to>0) {
    $sql="SELECT gks_warehouses.*, gks_nomoi.nomos_descr, gks_country.country_name
    FROM (gks_warehouses 
    LEFT JOIN gks_nomoi ON gks_warehouses.warehouse_nomos_id = gks_nomoi.id_nomos) 
    LEFT JOIN gks_country ON gks_warehouses.warehouse_country_id = gks_country.id_country
    WHERE gks_warehouses.is_virtual=0 and gks_warehouses.id_warehouse=".$warehouses_id_to;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
    if ($result->num_rows==1) {
      $row = $result->fetch_assoc();
      if (!($company_id!=$row['company_id'] or $company_sub_id!=$row['company_sub_id'])) {
        $row_doc['warehouse_to_hide_if_is_other']='display:none;';
      }
      $row_doc['warehouse_to_name']=$row['warehouse_name'];
      $row_doc['warehouse_to_topos_fortosis']=$row['warehouse_topos_fortosis'];
      $row_doc['warehouse_to_phone']=$row['warehouse_phone'];
      $row_doc['warehouse_to_odos']=$row['warehouse_odos'];
      $row_doc['warehouse_to_arithmos']=$row['warehouse_arithmos'];
      $row_doc['warehouse_to_orofos']=$row['warehouse_orofos'];
      $row_doc['warehouse_to_perioxi']=$row['warehouse_perioxi'];
      $row_doc['warehouse_to_tk']=$row['warehouse_tk'];
      $row_doc['warehouse_to_poli']=$row['warehouse_poli'];
      $row_doc['warehouse_to_nomos_descr']=$row['nomos_descr'];
      $row_doc['warehouse_to_country_name']=$row['country_name'];
      //echo '<pre>';print_r($row_doc);die();
    }
  }  
  
  
  
  
  $sql="SELECT 
  CASE
    WHEN gks_eshop_products.product_class='variable_item' THEN
      CASE
        WHEN gks_eshop_products.product_photo<>'' THEN
          gks_eshop_products.product_photo
        ELSE
          gks_eshop_products_parent.product_photo
      END
    ELSE gks_eshop_products.product_photo

  END as product_photo_p,
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
  END as product_descr_p,
  CASE
    WHEN gks_eshop_products.product_class='variable_item' THEN
      CASE
        WHEN gks_eshop_products.product_descr_small<>'' THEN
          gks_eshop_products.product_descr_small
        ELSE
          CASE
            WHEN gks_eshop_products.product_descr_variable<>'' THEN
              CONCAT_WS(' ', gks_eshop_products_parent.product_descr_small, gks_eshop_products.product_descr_variable)
            ELSE
              gks_eshop_products_parent.product_descr_small
          END
      END
    ELSE gks_eshop_products.product_descr_small
  END as product_descr_small_p,  
  gks_whi_mov_products.*, 
      
  gks_eshop_products.product_code, 
  gks_eshop_products.product_sku,
  gks_eshop_products.product_gtin,
  gks_eshop_products.product_upc,
  gks_eshop_products.product_ean,
  gks_eshop_products.product_isbn,
  gks_eshop_products.product_taric,  
  gks_eshop_products.product_photo, 

  gks_eshop_products.product_descr_big, 
  gks_monades_metrisis.monada_descr, 
  gks_monades_metrisis.monada_symbol
  FROM ((gks_whi_mov_products 
  LEFT JOIN gks_eshop_products ON gks_whi_mov_products.product_id = gks_eshop_products.id_product) 
  LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product)
  LEFT JOIN gks_monades_metrisis ON gks_whi_mov_products.product_monada_id = gks_monades_metrisis.id_monada  
  WHERE gks_whi_mov_products.whi_mov_id=".$id."
  ORDER BY gks_whi_mov_products.product_aa;";

   
  
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
  if ($result->num_rows==0) {$ret['message']=gks_lang('Δεν βρέθηκαν είδη για τo δελτίο αποστολής με ID').' <b>'.$id.'</b>';debug_mail(false,$ret['message'],$sql); return $ret;}
  
  $row_eidoi=array();
  $id_product_array_ids=array();
  while ($eidos = $result->fetch_assoc()) {
    if ($eidos['product_id']==2) $eidos['product_id']=0;
    
    $product_photo=trim_gks($eidos['product_photo']);
    $eidos['product_photo']='';
    if ($product_photo!='') {
      $full_product_photo=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.$product_photo;
      if (file_exists($full_product_photo)) {
        $eidos['product_photo']=GKS_SITE_URL.substr($product_photo, 1);
      }
    }
    $eidos['id_order_product'] = $eidos['id_whi_mov_product'];

    $eidos['monada_descr']=gks_lang_pft($row_form['gks_lang'],'gks_monades_metrisis','monada_descr',$eidos['product_monada_id'],$eidos['monada_descr']);
    $eidos['monada_symbol']=gks_lang_pft($row_form['gks_lang'],'gks_monades_metrisis','monada_symbol',$eidos['product_monada_id'],$eidos['monada_symbol']);
    
    $id_product_array_ids[]=$eidos['id_order_product'];
    $row_eidoi[]=$eidos;
  }
  //print '<pre>';print_r($row_eidoi);die();


  //$timemmm=time();
  $gks_custom_prepare = gks_custom_table_item_prepare('gks_eshop_products',['from'=>'print']);
  if ($gks_custom_prepare['success']==true) {
    foreach ($row_eidoi as $pkey => $eidos) {
      $custom_row['id_product']=$eidos['product_id'];
      $gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$custom_row);
      if ($gks_custom_row['success']) {
        //print '<pre>';print_r($gks_custom_row);die();
        
        foreach ($gks_custom_row['fields'] as $key => $cf_item) {
          $row_eidoi[$pkey]['custom_'.$key]=array(
            'type'  => $cf_item['field_type_id'],
            'value' => $cf_item['print'],
          );
        } 
      }
    }
    //echo '<pre>'.(time()-$timemmm);die();
    //echo '<pre>';print_r($row_doc);die();
    //print_r($gks_custom_row);
  }
  //print '<pre>';print_r($row_eidoi);die();
    
  $products_lots_serials=array();
  if (count($id_product_array_ids)>0) {
    if ($GKS_PRODUCT_LOTS_SERIALS) {
      $sql_lots_serials="SELECT 
      gks_whi_mov_products_lots.lot_product_id,
      whi_mov_product_id as id, 
      lot_product_quantity,
      apografi_lot_posotitaonhand,
      gks_eshop_product_lots.lot_name, 
      gks_eshop_product_lots.lot_descr, 
      gks_eshop_product_lots.lot_date_production, 
      gks_eshop_product_lots.lot_date_expire, 
      gks_eshop_product_lots.lot_disabled
      FROM gks_whi_mov_products_lots
      LEFT JOIN gks_eshop_product_lots ON gks_whi_mov_products_lots.lot_product_id = gks_eshop_product_lots.id_lot_product
      WHERE gks_whi_mov_products_lots.whi_mov_product_id In (".implode(',',$id_product_array_ids).")
      ORDER BY gks_whi_mov_products_lots.id_whi_mov_product_lots";
      $result_lots_serials = $db_link->query($sql_lots_serials);        
      if (!$result_lots_serials) {debug_mail(false,'error sql',$sql_lots_serials); die('sql error');}
      while ($row_lots_serials = $result_lots_serials->fetch_assoc()) {
        $products_lots_serials[$row_lots_serials['id']][]=$row_lots_serials;
      }
      
      //echo '<pre>';print_r($products_lots_serials);die();
      
    }    
  }  
  
  $row_fpa=array();
  $row_foroi=array(); 
  
  $gks_custom_prepare = gks_custom_table_item_prepare('gks_whi_mov',['from'=>'print']);
  if ($gks_custom_prepare['success']==true) {
    $custom_row['id_whi_mov']=$id;
    $gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$custom_row);
    if ($gks_custom_row['success']) {
      foreach ($gks_custom_row['fields'] as $key => $cf_item) {
        $row_doc['custom_'.$key]=array(
          'type'  => $cf_item['field_type_id'],
          'value' => $cf_item['print'],
        );
      } 
    }
    //echo '<pre>';print_r($row_doc);die();
    //print_r($gks_custom_row);
  }

  $gks_custom_prepare = gks_custom_table_item_prepare('wp_users',['from'=>'print']);
  if ($gks_custom_prepare['success']==true) {
    $custom_row['ID']=$row_person['id'];
    $gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$custom_row);
    if ($gks_custom_row['success']) {
      foreach ($gks_custom_row['fields'] as $key => $cf_item) {
        $row_person['custom_'.$key]=array(
          'type'  => $cf_item['field_type_id'],
          'value' => $cf_item['print'],
        );
      } 
    }
    //echo '<pre>';print_r($row_person);die();
    //print_r($gks_custom_row);
  }
      
  $company_ret=gks_print_form_company_data($company_id,$company_sub_id,$options,$row_form);
  if ($company_ret['success']==false) {$ret['message']=$company_ret['message'];return $ret;}
  
  return gks_print_form_make_print($row_form,$row,$row_doc,$row_canceled_doc,$row_credit_doc,$row_eidoi,$row_fpa,$row_foroi,$company_ret['data'],$row_person,$options,$products_lots_serials,[]);
}

function gks_print_form_gks_hotel_reservation($id,$row_form,$options) {
  global $db_link;
  global $GKS_PRODUCT_LOTS_SERIALS;
  
  $ret=array('success' => false, 'message' => 'gks_hotel_reservation generic error');
 
  $sql="SELECT gks_hotel_reservation.*, 
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, ".GKS_WP_TABLE_PREFIX."users.user_url,
  gks_hotel.company_id,gks_hotel.company_sub_id,
  gks_company.company_afm,gks_company.company_title,gks_company.company_tagline,gks_company_subs.company_sub_title,gks_company_subs.company_sub_tagline,
  gks_payment_acquirers.payment_acquirer_name, gks_delivery_methods.delivery_method_name, 
  gks_eshop_pricelist.pricelist_descr, gks_eshop_fiscal_position.fiscal_position_descr,
  gks_users.order_sxolio,gks_users.pelati_sxolio,gks_users.phone_home,
  gks_acc_journal.acc_journal_descr, gks_acc_seires.seira_code, gks_acc_seires.seira_descr,gks_acc_seires.is_xeirografi,
  gks_acc_journal.acc_eidos_parastatikou_id,
  eidos_parastatikou_type_id, antisimvalomenos_label, eidos_parastatikou_type_id, antisimvalomenos_label_en, 
  eidos_parastatikou_need_prev, eidos_parastatikou_has_fpa,eidos_parastatikou_has_othertaxes, 
  eidos_parastatikou_has_esoda, eidos_parastatikou_has_eksoda,eidos_parastatikou_need_afm,
  gks_lang.lang_name,
  gks_country.country_name,gks_country.country_initials,gks_country.country_initials3,gks_country.country_ee,
  gks_nomoi.nomos_descr,

  gks_acc_eidi_parastatikon.rbs_code_a

  
  FROM (((((((((((((((((gks_hotel_reservation
  
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_hotel_reservation.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_hotel_reservation.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hotel_reservation.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
  LEFT JOIN gks_hotel ON gks_hotel_reservation.hotel_id = gks_hotel.id_hotel)

  LEFT JOIN gks_company on gks_hotel.company_id = gks_company.id_company)
  LEFT JOIN gks_company_subs on gks_hotel.company_sub_id = gks_company_subs.id_company_sub)
  LEFT JOIN gks_acc_journal ON gks_hotel_reservation.reservation_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
  LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
  LEFT JOIN gks_acc_seires ON gks_hotel_reservation.reservation_seira_id = gks_acc_seires.id_acc_seira)
  LEFT JOIN gks_payment_acquirers ON gks_hotel_reservation.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer) 
  LEFT JOIN gks_delivery_methods ON gks_hotel_reservation.tropos_apostolis = gks_delivery_methods.id_delivery_method) 
  LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
  LEFT JOIN gks_eshop_fiscal_position ON gks_hotel_reservation.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
  LEFT JOIN gks_eshop_pricelist ON gks_hotel_reservation.pricelist_id = gks_eshop_pricelist.id_pricelist)
  LEFT JOIN gks_country ON gks_hotel_reservation.ma_country_id = gks_country.id_country)
  LEFT JOIN gks_nomoi ON gks_hotel_reservation.ma_nomos_id = gks_nomoi.id_nomos)
  LEFT JOIN gks_lang ON gks_hotel_reservation.user_lang = gks_lang.id_lang

  where gks_hotel_reservation.id_hotel_reservation = ".$id;
  //print '<pre>'; print_r($sql);die();
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
  if ($result->num_rows!=1) {$ret['message']=gks_lang('Δεν βρέθηκε η κράτηση με ID').' <b>'.$id.'</b>';debug_mail(false,$ret['message'],$sql); return $ret;}
  $row = $result->fetch_assoc();
  
  $row['acc_journal_descr']=gks_lang_pft($row_form['gks_lang'],'gks_acc_journal','acc_journal_descr',$row['reservation_journal_id'],$row['acc_journal_descr']);
  $row['payment_acquirer_name']=gks_lang_pft($row_form['gks_lang'],'gks_payment_acquirers','payment_acquirer_name',$row['tropos_pliromis'],$row['payment_acquirer_name']);
  $row['delivery_method_name']=gks_lang_pft($row_form['gks_lang'],'gks_delivery_methods','delivery_method_name',$row['tropos_apostolis'],$row['delivery_method_name']);
  $row['country_name']=gks_lang_pft($row_form['gks_lang'],'gks_country','country_name',$row['ma_country_id'],$row['country_name']);
  $row['nomos_descr']=gks_lang_pft($row_form['gks_lang'],'gks_nomoi','nomos_descr',$row['ma_nomos_id'],$row['nomos_descr']);

  $row['country_name_en_US']='';
  gks_lang_data_obj_insert_to_row($row,'gks_country',array(
    array('id' => $row['ma_country_id'], 'filename'=>'country_name', 'new_field' => 'country_name_%s'),
  ));
  $row['nomos_descr_en_US']='';
  gks_lang_data_obj_insert_to_row($row,'gks_nomoi',array(
    array('id' => $row['ma_nomos_id'], 'filename'=>'nomos_descr', 'new_field' => 'nomos_descr_%s'),
  ));
  //print '<pre>';print_r($row);die();
   
  $company_id=intval($row['company_id']);
  $company_sub_id=intval($row['company_sub_id']);

  $row_person=array();
  $row_person['id']=$row['user_id'];
  $row_person['nickname_add']=trim_gks($row['gks_nickname_add']);
  $row_person['nickname_edit']=trim_gks($row['gks_nickname_edit']);
  $row_person['nickname']=trim_gks($row['gks_nickname']);
  $row_person['email']=$row['user_email'];
  $row_person['url']=trim_gks($row['user_url']);
  $row_person['first_name']=$row['user_first_name'];
  $row_person['last_name']=$row['user_last_name'];
  $row_person['phone']=$row['phone_home'];
  $row_person['mobile']=$row['user_mobile'];
  $row_person['user_lang']=$row['user_lang'];
  $row_person['eponimia']=$row['eponimia'];
  $row_person['title']=$row['title'];
  $row_person['afm']=$row['afm'];
  $row_person['doy']=$row['doy'];
  $row_person['epaggelma']=$row['epaggelma'];
  $row_person['odos']=$row['ma_odos'];
  $row_person['arithmos']=$row['ma_arithmos'];
  $row_person['orofos']=$row['ma_orofos'];
  $row_person['perioxi']=$row['ma_perioxi'];
  $row_person['poli']=$row['ma_poli'];
  $row_person['tk']=$row['ma_tk'];
  $row_person['country_id']=$row['ma_country_id'];
  $row_person['country_name']=$row['country_name'];
  $row_person['country_name_en']=$row['country_name_en_US'];
  $row_person['country_initials']=$row['country_initials'];
  $row_person['country_initials3']=$row['country_initials3'];
  $row_person['country_ee']=$row['country_ee'];
  $row_person['nomos_id']=$row['ma_nomos_id'];
  $row_person['nomos_descr']=$row['nomos_descr'];
  $row_person['nomos_descr_en']=$row['nomos_descr_en_US'];
  $row_person['pricelist_id']=$row['pricelist_id'];
  $row_person['pricelist']=$row['pricelist_descr'];
  $row_person['fiscal_position_id']=$row['fiscal_position_id'];
  $row_person['fiscal_position']=$row['fiscal_position_descr'];
  $row_person['antisimvalomenos_label']=$row['antisimvalomenos_label'];
  $row_person['antisimvalomenos_label_en']=$row['antisimvalomenos_label_en'];
  

  $row_person['address_text']=gks_print_address_text($row);
//  $row_person['dest_name']=$row['destination_data_name'];
//  $row_person['dest_phone']=$row['destination_data_phone'];
//  $row_person['dest_odos']=$row['destination_data_odos'];
//  $row_person['dest_perioxi']=$row['destination_data_perioxi'];
//  $row_person['dest_poli']=$row['destination_data_poli'];
//  $row_person['dest_tk']=$row['destination_data_tk'];
//  $row_person['dest_country_id']=$row['destination_data_country_id'];
//  $row_person['dest_country_name']=$row['country_name_dest'];
//  $row_person['dest_country_name_en']=$row['country_name_en_US_dest'];
//  $row_person['dest_nomos_id']=$row['destination_data_nomos_id'];
//  $row_person['dest_nomos_descr']=$row['nomos_descr_dest'];
//  $row_person['dest_nomos_descr_en']=$row['nomos_descr_en_US_dest'];

  //if ($row['address_extra'] < 1) {
  if (true) {
    $row_person['dest_name']='';
    $row_person['dest_phone']='';
    $row_person['dest_odos']='';
    $row_person['dest_arithmos']='';
    $row_person['dest_orofos']='';
    $row_person['dest_perioxi']='';
    $row_person['dest_poli']='';
    $row_person['dest_tk']='';
    $row_person['dest_country_id']=0;
    $row_person['dest_country_name']='';
    $row_person['dest_country_name_en']='';
    $row_person['dest_nomos_id']=0;
    $row_person['dest_nomos_descr']='';
    $row_person['dest_nomos_descr_en']='';    
  }

  $row_doc=array();
  $row_doc['title']=$row['acc_journal_descr'];
  $row_doc['title_pre']='';
  
  if ($row_form['gks_lang']=='el-GR') {
    $row_doc['title_pre']=getHotelReservationStatusDescr($row['reservation_status']);
  } else {
    $row_doc['title_pre']=getHotelReservationStatusDescr_en_US($row['reservation_status']);
  }
  $row_doc['company']=$row['company_title'];
  if (trim_gks($row['company_sub_title'])!='') $row_doc['company'].=' \ '.$row['company_sub_title'];
  
  $row_doc['company_title']=$row['company_title'];
  $row_doc['company_sub_title']=trim_gks($row['company_sub_title']);
  if ($row_doc['company_sub_title']=='') $row_doc['company_sub_title']=gks_lang('Κεντρικό');
  

  $row_doc['date']=myDateTimeFormat(_time_user(strtotime($row['reservation_date']),1));
  $row_doc['date_d']=myDateFormat(_time_user(strtotime($row['reservation_date']),1));
  $row_doc['date_dw']=myDateFormatw(_time_user(strtotime($row['reservation_date']),1),$row_form['gks_lang']);
  $row_doc['date_dt']=myDateTimeFormat(_time_user(strtotime($row['reservation_date']),1));
  $row_doc['date_dtw']=myDateTimeFormatw(_time_user(strtotime($row['reservation_date']),1),$row_form['gks_lang']);
  $row_doc['date_dtt']=myDateTimeFormatText(_time_user(strtotime($row['reservation_date']),1),$row_form['gks_lang']);
  $row_doc['datefull']=showDate(strtotime($row['reservation_date']),'d/m/Y H:i:s',1);
  
  $row_doc['mydate_add']=showDate(strtotime($row['mydate_add']),'d/m/Y H:i',1);
  $row_doc['mydate_edit']=showDate(strtotime($row['mydate_edit']),'d/m/Y H:i',1);
  $row_doc['seira']=trim_gks($row['seira_code']);
  $row_doc['seira_descr']=trim_gks($row['seira_descr']);
  $row_doc['number']=trim_gks($row['reservation_number_int']);
  $row_doc['number_str']=trim_gks($row['reservation_number_str']);
  $row_doc['mark']='';
  $row_doc['aade_qrurl']='';
  $row_doc['aade_paroxos_qrurl']='';
  $row_doc['paroxos_tf1_url']='';
  $row_doc['aade_invoiceuid']='';
  $row_doc['paroxos_authenticationCode']='';
  $row_doc['products_posotita']=trim_gks($row['products_posotita']);
  $row_doc['gks_price_original_net']=trim_gks($row['gks_price_original_net']);
  $row_doc['gks_price_net']=trim_gks($row['gks_price_net']);
  $row_doc['gks_price_fpa']=trim_gks($row['gks_price_fpa']);
  $row_doc['gks_price_netfpa']=trim_gks($row['gks_price_netfpa']);
  $row_doc['gks_price_total']=trim_gks($row['gks_price_total']);
  $row_doc['totalWithheldAmount']=trim_gks($row['totalWithheldAmount']);
  $row_doc['totalOtherTaxesAmount']=trim_gks($row['totalOtherTaxesAmount']);
  $row_doc['totalStampDutyamount']=trim_gks($row['totalStampDutyamount']);
  $row_doc['totalFeesAmount']=trim_gks($row['totalFeesAmount']);
  $row_doc['totalDeductionsAmount']=0; //trim_gks($row['totalDeductionsAmount']);

  $row_doc['check_in']=myDateTimeFormat(strtotime($row['check_in']));
  $row_doc['check_in_d']=myDateFormat(strtotime($row['check_in']));
  $row_doc['check_in_dw']=myDateFormatw(strtotime($row['check_in']),$row_form['gks_lang']);
  $row_doc['check_in_dt']=myDateTimeFormat(strtotime($row['check_in']));
  $row_doc['check_in_dtw']=myDateTimeFormatw(strtotime($row['check_in']),$row_form['gks_lang']);
  $row_doc['check_in_dtt']=myDateTimeFormatText(strtotime($row['check_in']),$row_form['gks_lang']);

  $row_doc['check_out']=myDateTimeFormat(strtotime($row['check_out']));
  $row_doc['check_out_d']=myDateFormat(strtotime($row['check_out']));
  $row_doc['check_out_dw']=myDateFormatw(strtotime($row['check_out']),$row_form['gks_lang']);
  $row_doc['check_out_dt']=myDateTimeFormat(strtotime($row['check_out']));
  $row_doc['check_out_dtw']=myDateTimeFormatw(strtotime($row['check_out']),$row_form['gks_lang']);
  $row_doc['check_out_dtt']=myDateTimeFormatText(strtotime($row['check_out']),$row_form['gks_lang']);

  $row_doc['rooms_plithos']=intval($row['rooms_plithos']);
  $row_doc['num_days']=intval($row['num_days']);
  $row_doc['num_adults']=intval($row['num_adults']);
  $row_doc['num_childs']=intval($row['num_childs']);
  $row_doc['num_visitors']=intval($row['num_adults'])+intval($row['num_childs']);
  $row_doc['num_child_kounies']=intval($row['num_child_kounies']);
  $row_doc['num_extra_beds']=intval($row['num_extra_beds']);






  $row_person['person_balance_before']=gks_balance_calc(['id' => $row['user_id'], 'except_id_hotel_reservation' => $row['id_hotel_reservation']]);
  $row_person['person_balance_after']=gks_balance_calc(['id' => $row['user_id']]);
  

  $row_doc['enarji_apostolis']=     ''; //(isset($row['dispatch_date']) ? showDate(strtotime($row['dispatch_date']), 'd/m/Y H:i', 1) : '');
  $row_doc['enarji_apostolis_date']=''; //(isset($row['dispatch_date']) ? showDate(strtotime($row['dispatch_date']), 'd/m/Y', 1) : '');
  $row_doc['enarji_apostolis_time']=''; //(isset($row['dispatch_date']) ? showDate(strtotime($row['dispatch_date']), 'H:i', 1) : '');
  $row_doc['arithmos_oximatos']=''; //trim_gks($row['vehicle_number']);
  $row_doc['skopos_diakinisis']=''; //trim_gks($row['aade_skopos_diakinisis_descr']);
  $row_doc['tropos_pliromis']=trim_gks($row['payment_acquirer_name']);
  $row_doc['tropos_apostolis']=trim_gks($row['delivery_method_name']);
  
  $row_doc['arithmos_aposolis']=trim_gks($row['delivery_number']);
  $row_doc['user_notes']=trim_gks($row['user_notes']);
  $row_doc['note_doc']=trim_gks($row['sxolio']);
  $row_doc['note_production']='';
  $row_doc['note_logistirio']=trim_gks($row['note_logistirio']);
  $row_doc['ddate']='';
  $row_doc['occasion_title']='';
  $row_doc['occasion_type_descr']='';
  $row_doc['occasion_mydate_add']= '';


  $row_doc['idiotites_text']=gks_print_idiotites_text($row);
  $row_doc['photos']='';
  $row_doc['links']='';
  

  
  
  
  
//  $cancel_for_acc_inv_id=0; //intval($row['cancel_for_acc_inv_id']);  
  $row_canceled_doc=array();
  $row_canceled_doc['display']='none';
//  if ($cancel_for_acc_inv_id>0) {
//    $sql_canceled="SELECT gks_acc_journal.acc_journal_descr, gks_acc_journal.acc_eidos_parastatikou_id, 
//    gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
//    gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa, 
//    gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, 
//    gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, 
//    gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
//    gks_acc_inv.inv_acc_number_int, gks_acc_inv.inv_acc_number_str, gks_acc_inv.inv_acc_ekdosi_date, gks_acc_inv.inv_date,gks_acc_inv.inv_state,
//    
//    company_title,company_sub_title,aade_invoicemark
//    
//    FROM (((((gks_acc_inv 
//    LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
//    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
//    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
//    LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira)
//    LEFT JOIN gks_company on gks_acc_inv.company_id = gks_company.id_company)
//    LEFT JOIN gks_company_subs on gks_acc_inv.company_sub_id = gks_company_subs.id_company_sub
//    where gks_acc_inv.id_acc_inv=".$cancel_for_acc_inv_id;
//    $result_canceled = $db_link->query($sql_canceled);        
//    if (!$result_canceled) {debug_mail(false,'error sql',$sql);die('sql error');}
//    if ($result_canceled->num_rows!=1) {
//      debug_mail(false,'record parent not found sql',$sql_canceled); 
//      die('no record found');
//    }
//    $row_canceled = $result_canceled->fetch_assoc();
//    $row_canceled_doc['display']=''; //diladi, orato
//    $row_canceled_doc['title']=$row_canceled['acc_journal_descr'];
//    $row_canceled_doc['title_pre']='';
//    
//    if ($row_form['gks_lang']=='el-GR') {
//      if ($row_canceled['inv_state']=='010draft') $row_canceled_doc['title_pre']='Draft';
//      else if ($row_canceled['inv_state']=='040cancelled') $row_canceled_doc['title_pre']='Cancelled';
//      else if ($row_canceled['inv_state']=='050proinvoice') $row_canceled_doc['title_pre']='Proinvoice';
//      else if ($row_canceled['inv_state']=='080listing') $row_canceled_doc['title_pre']='Listing';
//    } else {
//      if ($row_canceled['inv_state']=='010draft') $row_canceled_doc['title_pre']='Draft';
//      else if ($row_canceled['inv_state']=='040cancelled') $row_canceled_doc['title_pre']='Cancelled';
//      else if ($row_canceled['inv_state']=='050proinvoice') $row_canceled_doc['title_pre']='Proinvoice';
//      else if ($row_canceled['inv_state']=='080listing') $row_canceled_doc['title_pre']='Listing';
//    }
//    $row_canceled_doc['company']=$row_canceled['company_title'];
//    if (trim_gks($row_canceled['company_sub_title'])!='') $row_canceled_doc['company'].=' \ '.$row_canceled['company_sub_title'];
//    
//    $row_canceled_doc['date']=showDate(strtotime($row_canceled['inv_date']),'d/m/Y',1);
//    $row_canceled_doc['datefull']=showDate(strtotime($row_canceled['inv_date']),'d/m/Y H:i:s',1);
//    $row_canceled_doc['seira']=trim_gks($row_canceled['seira_code']);
//    $row_canceled_doc['seira_descr']=trim_gks($row_canceled['seira_descr']);
//    $row_canceled_doc['number']=trim_gks($row_canceled['inv_acc_number_int']);
//    $row_canceled_doc['number_str']=trim_gks($row_canceled['inv_acc_number_str']);
//    $row_canceled_doc['mark']=trim_gks($row_canceled['aade_invoicemark']);
//    
//  } else {
    $row_canceled_doc['display']='none';
    $row_canceled_doc['title']='';
    $row_canceled_doc['title_pre']='';
    $row_canceled_doc['company']='';
    $row_canceled_doc['date']='';
    $row_canceled_doc['datefull']='';
    $row_canceled_doc['seira']='';
    $row_canceled_doc['seira_descr']='';
    $row_canceled_doc['number']=0;
    $row_canceled_doc['number_str']='';
    $row_canceled_doc['mark']='';
    $row_canceled_doc['aade_qrurl']='';
    $row_canceled_doc['aade_paroxos_qrurl']='';
    $row_canceled_doc['paroxos_tf1_url']='';
    $row_canceled_doc['aade_invoiceuid']='';
    $row_canceled_doc['paroxos_authenticationCode']='';
  //}
  
  
  //print'<pre>';print_r($row);die();
  
  //$credit_memo_for_acc_inv_id=intval($row['credit_memo_for_acc_inv_id']);  
  $row_credit_doc=array();
//  if ($credit_memo_for_acc_inv_id>0) {
//    $sql_credit="SELECT gks_acc_journal.acc_journal_descr, gks_acc_journal.acc_eidos_parastatikou_id, 
//    gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, gks_acc_eidi_parastatikon_types.antisimvalomenos_label, 
//    gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa, 
//    gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, 
//    gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda, gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm, 
//    gks_acc_seires.seira_code, gks_acc_seires.seira_descr, 
//    gks_acc_inv.inv_acc_number_int, gks_acc_inv.inv_acc_number_str, gks_acc_inv.inv_acc_ekdosi_date, gks_acc_inv.inv_date,gks_acc_inv.inv_state,
//    
//    company_title,company_sub_title,aade_invoicemark
//    
//    FROM (((((gks_acc_inv 
//    LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
//    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou) 
//    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type) 
//    LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira)
//    LEFT JOIN gks_company on gks_acc_inv.company_id = gks_company.id_company)
//    LEFT JOIN gks_company_subs on gks_acc_inv.company_sub_id = gks_company_subs.id_company_sub
//    where gks_acc_inv.id_acc_inv=".$credit_memo_for_acc_inv_id;
//    $result_credit = $db_link->query($sql_credit);        
//    if (!$result_credit) {debug_mail(false,'error sql',$sql);die('sql error');}
//    if ($result_credit->num_rows!=1) {
//      debug_mail(false,'record parent not found sql',$sql_credit); 
//      die('no record found');
//    }
//    $row_credit = $result_credit->fetch_assoc();
//    $row_credit_doc['display']=''; //diladi, orato
//    $row_credit_doc['title']=$row_credit['acc_journal_descr'];
//    $row_credit_doc['title_pre']='';
//    
//    if ($row_form['gks_lang']=='el-GR') {
//      if ($row_credit['inv_state']=='010draft') $row_credit_doc['title_pre']='Draft';
//      else if ($row_credit['inv_state']=='040cancelled') $row_credit_doc['title_pre']='Cancelled';
//      else if ($row_credit['inv_state']=='050proinvoice') $row_credit_doc['title_pre']='Proinvoice';
//      else if ($row_credit['inv_state']=='080listing') $row_credit_doc['title_pre']='Listing';
//    } else {
//      if ($row_credit['inv_state']=='010draft') $row_credit_doc['title_pre']='Draft';
//      else if ($row_credit['inv_state']=='040cancelled') $row_credit_doc['title_pre']='Cancelled';
//      else if ($row_credit['inv_state']=='050proinvoice') $row_credit_doc['title_pre']='Proinvoice';
//      else if ($row_credit['inv_state']=='080listing') $row_credit_doc['title_pre']='Listing';
//    }
//    $row_credit_doc['company']=$row_credit['company_title'];
//    if (trim_gks($row_credit['company_sub_title'])!='') $row_credit_doc['company'].=' \ '.$row_credit['company_sub_title'];
//    
//    $row_credit_doc['date']=showDate(strtotime($row_credit['inv_date']),'d/m/Y',1);
//    $row_credit_doc['datefull']=showDate(strtotime($row_credit['inv_date']),'d/m/Y H:i:s',1);
//    $row_credit_doc['seira']=trim_gks($row_credit['seira_code']);
//    $row_credit_doc['seira_descr']=trim_gks($row_credit['seira_descr']);
//    $row_credit_doc['number']=trim_gks($row_credit['inv_acc_number_int']);
//    $row_credit_doc['number_str']=trim_gks($row_credit['inv_acc_number_str']);
//    $row_credit_doc['mark']=trim_gks($row_credit['aade_invoicemark']);
//    
//  } else {
    $row_credit_doc['display']='none';
    $row_credit_doc['title']='';
    $row_credit_doc['title_pre']='';
    $row_credit_doc['company']='';
    $row_credit_doc['date']='';
    $row_credit_doc['datefull']='';
    $row_credit_doc['seira']='';
    $row_credit_doc['seira_descr']='';
    $row_credit_doc['number']=0;
    $row_credit_doc['number_str']='';
    $row_credit_doc['mark']='';
    $row_credit_doc['aade_qrurl']='';
    $row_credit_doc['aade_paroxos_qrurl']='';
    $row_credit_doc['paroxos_tf1_url']='';
    $row_credit_doc['aade_invoiceuid']='';
    $row_credit_doc['paroxos_authenticationCode']='';
  //}  
  
  
  
  $sql="SELECT 
  CASE
    WHEN gks_eshop_products.product_class='variable_item' THEN
      CASE
        WHEN gks_eshop_products.product_photo<>'' THEN
          gks_eshop_products.product_photo
        ELSE
          gks_eshop_products_parent.product_photo
      END
    ELSE gks_eshop_products.product_photo

  END as product_photo_p,
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
  END as product_descr_p,  
  CASE
    WHEN gks_eshop_products.product_class='variable_item' THEN
      CASE
        WHEN gks_eshop_products.product_descr_small<>'' THEN
          gks_eshop_products.product_descr_small
        ELSE
          CASE
            WHEN gks_eshop_products.product_descr_variable<>'' THEN
              CONCAT_WS(' ', gks_eshop_products_parent.product_descr_small, gks_eshop_products.product_descr_variable)
            ELSE
              gks_eshop_products_parent.product_descr_small
          END
      END
    ELSE gks_eshop_products.product_descr_small
  END as product_descr_small_p,  
  
  gks_hotel_reservation_room.*, 
  gks_hotel_room.room_descr, gks_hotel_room_en_US.room_descr_en_US, gks_hotel_room.room_photo, 
  gks_hotel_room.hotel_room_type_id, gks_hotel_room_type.room_type_descr, gks_hotel_room_type_en_US.room_type_descr_en_US, gks_hotel_room_type.room_type_photo, 
  gks_eshop_products.product_code, 
  gks_eshop_products.product_sku, 
  gks_eshop_products.product_gtin,
  gks_eshop_products.product_upc,
  gks_eshop_products.product_ean,
  gks_eshop_products.product_isbn,
  gks_eshop_products.product_taric,  
  gks_eshop_products.product_photo, 
  gks_eshop_products.product_descr_big, 
  gks_eshop_fpa.fpa_descr_print, gks_eshop_fpa.fpa_pososto, 
  gks_eshop_pricelist.pricelist_descr, 
  gks_aade_katigoria_parakratoumemenon_foron.aade_katigoria_parakratoumemenon_foron_type, 
  gks_aade_katigoria_parakratoumemenon_foron.aade_katigoria_parakratoumemenon_foron_descr, 
  gks_aade_katigoria_loipon_foron.aade_katigoria_loipon_foron_type, 
  gks_aade_katigoria_loipon_foron.aade_katigoria_loipon_foron_descr, 
  gks_aade_katigoria_xartosimou.aade_katigoria_xartosimou_type, 
  gks_aade_katigoria_xartosimou.aade_katigoria_xartosimou_descr, 
  gks_aade_katigoria_telon.aade_katigoria_telon_type, gks_aade_katigoria_telon.aade_katigoria_telon_descr
  FROM (((((((((((gks_hotel_reservation_room 
  LEFT JOIN gks_eshop_products ON gks_hotel_reservation_room.product_id = gks_eshop_products.id_product) 
  LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product) 
  LEFT JOIN gks_eshop_fpa ON gks_hotel_reservation_room.product_fpa_id = gks_eshop_fpa.id_fpa) 
  LEFT JOIN gks_eshop_pricelist ON gks_hotel_reservation_room.product_pricelist_item_id = gks_eshop_pricelist.id_pricelist) 
  LEFT JOIN gks_aade_katigoria_parakratoumemenon_foron ON gks_hotel_reservation_room.product_withheldPercentCategory = gks_aade_katigoria_parakratoumemenon_foron.id_aade_katigoria_parakratoumemenon_foron) 
  LEFT JOIN gks_aade_katigoria_loipon_foron ON gks_hotel_reservation_room.product_otherTaxesPercentCategory = gks_aade_katigoria_loipon_foron.id_aade_katigoria_loipon_foron) 
  LEFT JOIN gks_aade_katigoria_xartosimou ON gks_hotel_reservation_room.product_stampDutyPercentCategory = gks_aade_katigoria_xartosimou.id_aade_katigoria_xartosimou) 
  LEFT JOIN gks_aade_katigoria_telon ON gks_hotel_reservation_room.product_feesPercentCategory = gks_aade_katigoria_telon.id_aade_katigoria_telon) 
  LEFT JOIN gks_hotel_room ON gks_hotel_reservation_room.hotel_room_id = gks_hotel_room.id_hotel_room)
  LEFT JOIN (
    SELECT hotel_room_id, room_descr as room_descr_en_US FROM gks_hotel_room_lang WHERE lang_code='en-US'
  ) AS gks_hotel_room_en_US ON gks_hotel_room.id_hotel_room = gks_hotel_room_en_US.hotel_room_id)
  LEFT JOIN gks_hotel_room_type ON gks_hotel_room.hotel_room_type_id = gks_hotel_room_type.id_hotel_room_type)
  LEFT JOIN (
    SELECT hotel_room_type_id, room_type_descr as room_type_descr_en_US FROM gks_hotel_room_type_lang WHERE lang_code='en-US'
  ) AS gks_hotel_room_type_en_US ON gks_hotel_room_type.id_hotel_room_type = gks_hotel_room_type_en_US.hotel_room_type_id  
  
  WHERE gks_hotel_reservation_room.hotel_reservation_id=".$id."
  ORDER BY gks_hotel_reservation_room.id_hotel_reservation_room;";
  
     
  
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
  if ($result->num_rows==0) {$ret['message']=gks_lang('Δεν βρέθηκαν δωμάτια για την κράτηση με ID').' <b>'.$id.'</b>';debug_mail(false,$ret['message'],$sql); return $ret;}
  
  $row_eidoi=array();
  $id_product_array_ids=array();
  while ($eidos = $result->fetch_assoc()) {
    if ($eidos['product_id']==2) $eidos['product_id']=0;
    
    $product_photo=trim_gks($eidos['product_photo']);
    $eidos['product_photo']='';
    if ($product_photo!='') {
      $full_product_photo=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.$product_photo;
      if (file_exists($full_product_photo)) {
        $eidos['product_photo']=GKS_SITE_URL.substr($product_photo, 1);
      }
    }
    $eidos['id_order_product'] = $eidos['id_hotel_reservation_room'];
    $eidos['rnum_visitors']=$eidos['rnum_adults']+$eidos['rnum_childs'];
    
    $eidos['room_descr']=gks_lang_pft($row_form['gks_lang'],'gks_hotel_room','room_descr',$eidos['hotel_room_id'],$eidos['room_descr']);
    $eidos['room_type_descr']=gks_lang_pft($row_form['gks_lang'],'gks_hotel_room_type','room_type_descr',$eidos['hotel_room_type_id'],$eidos['room_type_descr']);
   
    
    $id_product_array_ids[]=$eidos['id_order_product'];
    $row_eidoi[]=$eidos;
  }
  //print '<pre>';print_r($row_eidoi);die();


  
  $products_lots_serials=array();
//  if (count($id_product_array_ids)>0) {
//    if ($GKS_PRODUCT_LOTS_SERIALS) {
//      $sql_lots_serials="SELECT 
//      gks_acc_inv_products_lots.lot_product_id,
//      acc_inv_product_id as id, 
//      lot_product_quantity,
//      gks_eshop_product_lots.lot_name, 
//      gks_eshop_product_lots.lot_descr, 
//      gks_eshop_product_lots.lot_date_production, 
//      gks_eshop_product_lots.lot_date_expire, 
//      gks_eshop_product_lots.lot_disabled
//      FROM gks_acc_inv_products_lots
//      LEFT JOIN gks_eshop_product_lots ON gks_acc_inv_products_lots.lot_product_id = gks_eshop_product_lots.id_lot_product
//      WHERE gks_acc_inv_products_lots.acc_inv_product_id In (".implode(',',$id_product_array_ids).")
//      ORDER BY gks_acc_inv_products_lots.id_acc_inv_product_lots";
//      $result_lots_serials = $db_link->query($sql_lots_serials);        
//      if (!$result_lots_serials) {debug_mail(false,'error sql',$sql_lots_serials); die('sql error');}
//      while ($row_lots_serials = $result_lots_serials->fetch_assoc()) {
//        $products_lots_serials[$row_lots_serials['id']][]=$row_lots_serials;
//      }
//      
//      //echo '<pre>';print_r($products_lots_serials);die();
//      
//    }    
//  }  
  
  //print '<pre>';print_r($row_eidoi);die();
  
  
  $row_fpa=array();
  foreach ($row_eidoi as $eidos) {
    if ($eidos['product_fpa_pososto']!=0) {
      if (isset($row_fpa[$eidos['product_fpa_pososto']])==false) {
        $row_fpa[$eidos['product_fpa_pososto']]=array('aa' => 1, 'pososto' => $eidos['product_fpa_pososto'], 'net'=> 0, 'fpa'=>0);
      }
      $row_fpa[$eidos['product_fpa_pososto']]['net']+=$eidos['product_price_final_all_net'];
      $row_fpa[$eidos['product_fpa_pososto']]['fpa']+=$eidos['product_price_final_all_fpa'];
    }
  } 
  //print '<pre>'; print_r($row_fpa); die();

  $row_foroi=array();
  
  $row_foroi[1]=array(
    'descr'=>gks_lang('Παρακρατούμενος Φόρος'),
    'net' => 0,
    'foros' => 0,
  );
  $row_foroi[2]=array(
    'descr'=>gks_lang('Λοιποί Φόροι'),
    'net' => 0,
    'foros' => 0,
  );
  $row_foroi[3]=array(
    'descr'=>gks_lang('Ψηφιακό Τέλος συναλλαγής'),
    'net' => 0,
    'foros' => 0,
  );
  $row_foroi[4]=array(
    'descr'=>gks_lang('Τέλη'),
    'net' => 0,
    'foros' => 0,
  );
  $row_foroi[5]=array(
    'descr'=>gks_lang('Κρατήσεις'),
    'net' => 0,
    'foros' => 0,
  );
  
  foreach ($row_eidoi as $eidos) {
    if ($eidos['product_withheldAmount']!=0) {
      $row_foroi[1]['net']+=$eidos['product_price_final_all_net'];
      $row_foroi[1]['foros']+=$eidos['product_withheldAmount'];
    }
    if ($eidos['product_otherTaxesAmount']!=0) {
      $row_foroi[2]['net']+=$eidos['product_price_final_all_net'];
      $row_foroi[2]['foros']+=$eidos['product_otherTaxesAmount'];
    }
    if ($eidos['product_stampDutyAmount']!=0) {
      $row_foroi[3]['net']+=$eidos['product_price_final_all_net'];
      $row_foroi[3]['foros']+=$eidos['product_stampDutyAmount'];
    }
    if ($eidos['product_feesAmount']!=0) {
      $row_foroi[4]['net']+=$eidos['product_price_final_all_net'];
      $row_foroi[4]['foros']+=$eidos['product_feesAmount'];
    }
//    if ($eidos['product_deductionsAmount']!=0) {
//      $row_foroi[5]['net']+=$eidos['product_price_final_all_net'];
//      $row_foroi[5]['foros']+=$eidos['product_deductionsAmount'];
//    }
  }  
  
  if ($row_foroi[1]['foros']==0) unset($row_foroi[1]);
  if ($row_foroi[2]['foros']==0) unset($row_foroi[2]);
  if ($row_foroi[3]['foros']==0) unset($row_foroi[3]);
  if ($row_foroi[4]['foros']==0) unset($row_foroi[4]);
  if ($row_foroi[5]['foros']==0) unset($row_foroi[5]);
  
  $gks_custom_prepare = gks_custom_table_item_prepare('gks_hotel_reservation',['from'=>'print']);
  if ($gks_custom_prepare['success']==true) {
    $custom_row['id_hotel_reservation']=$id;
    $gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$custom_row);
    if ($gks_custom_row['success']) {
      foreach ($gks_custom_row['fields'] as $key => $cf_item) {
        $row_doc['custom_'.$key]=array(
          'type'  => $cf_item['field_type_id'],
          'value' => $cf_item['print'],
        );
      } 
    }
    //echo '<pre>';print_r($row_doc);die();
    //print_r($gks_custom_row);
  }
    
  $gks_custom_prepare = gks_custom_table_item_prepare('wp_users',['from'=>'print']);
  if ($gks_custom_prepare['success']==true) {
    $custom_row['ID']=$row_person['id'];
    $gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$custom_row);
    if ($gks_custom_row['success']) {
      foreach ($gks_custom_row['fields'] as $key => $cf_item) {
        $row_person['custom_'.$key]=array(
          'type'  => $cf_item['field_type_id'],
          'value' => $cf_item['print'],
        );
      } 
    }
    //echo '<pre>';print_r($row_person);die();
    //print_r($gks_custom_row);
  }
      
  $company_ret=gks_print_form_company_data($company_id,$company_sub_id,$options,$row_form);
  if ($company_ret['success']==false) {$ret['message']=$company_ret['message'];return $ret;}
  
  return gks_print_form_make_print($row_form,$row,$row_doc,$row_canceled_doc,$row_credit_doc,$row_eidoi,$row_fpa,$row_foroi,$company_ret['data'],$row_person,$options,$products_lots_serials,[]);
}



function gks_print_form_gks_transfer_reservation($id,$row_form,$options) {
  global $db_link;

  
  $ret=array('success' => false, 'message' => 'gks_transfer_reservation generic error');
 
  
  $sql ="SELECT gks_transfer_reservation.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
  ".GKS_WP_TABLE_PREFIX."users.gks_nickname, ".GKS_WP_TABLE_PREFIX."users_print.gks_nickname AS gks_nickname_print,
  ".GKS_WP_TABLE_PREFIX."users.user_url,
  gks_country.country_name, gks_nomoi.nomos_descr, 
  gks_eshop_fiscal_position.fiscal_position_descr, gks_eshop_pricelist.pricelist_descr,
  gks_country.country_initials,gks_country.country_initials3,gks_country.country_ee,gks_country.country_name,
  gks_lang.lang_ico, gks_lang.lang_name,
  gks_users.pelati_sxolio, gks_users.order_sxolio,gks_users.phone_home,
  gks_transfer.transfer_title,
  gks_transfer.company_id, gks_transfer.company_sub_id,
  ".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned, 
  gks_crm_channel_sale.crm_channel_sale_descr, 
  ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.gks_nickname as crm_channel_contact_gks_nickname,
  gks_ads_campain.ads_campain_name,
  gks_company.company_afm,gks_company.company_title,gks_company_subs.company_sub_title,
  
  antisimvalomenos_label, antisimvalomenos_label_en,

  gks_acc_journal.acc_journal_descr, gks_acc_seires.seira_code, gks_acc_seires.seira_descr,gks_acc_seires.is_xeirografi,gks_acc_seires.send_mydata,
  gks_acc_journal.acc_eidos_parastatikou_id,
  gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, antisimvalomenos_label, gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa,gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, 
  gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda,gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm,gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
  whi_gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros as whi_eidos_parastatikou_stock_pros,
  whi_gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as whi_eidos_parastatikou_type_id,
  gks_payment_acquirers.payment_acquirer_name,
  
  gks_poi_from.poi_descr AS poi_descr_from, gks_poi_to.poi_descr AS poi_descr_to,
  gks_poi_from.poi_map_latitude as poi_map_latitude_from, gks_poi_from.poi_map_longitude as poi_map_longitude_from,
  gks_poi_to.poi_map_latitude as poi_map_latitude_to, gks_poi_to.poi_map_longitude as poi_map_longitude_to,
  gks_poi_from.poi_type_id as poi_type_id_from,
  gks_poi_to.poi_type_id as poi_type_id_to
  
  
  FROM ((((((((((((((((((((((((gks_transfer_reservation 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_transfer_reservation.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_transfer_reservation.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_transfer_reservation.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_print ON gks_transfer_reservation.print_user_id = ".GKS_WP_TABLE_PREFIX."users_print.ID) 
  LEFT JOIN gks_country ON gks_transfer_reservation.ma_country_id = gks_country.id_country)
  LEFT JOIN gks_nomoi ON gks_transfer_reservation.ma_nomos_id = gks_nomoi.id_nomos)
  LEFT JOIN gks_eshop_fiscal_position ON gks_transfer_reservation.fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position)
  LEFT JOIN gks_eshop_pricelist ON gks_transfer_reservation.pricelist_id = gks_eshop_pricelist.id_pricelist)
  LEFT JOIN gks_lang ON gks_transfer_reservation.user_lang = gks_lang.id_lang)
  LEFT JOIN gks_users on gks_transfer_reservation.user_id = gks_users.user_id)
  LEFT JOIN gks_transfer ON gks_transfer_reservation.transfer_id = gks_transfer.id_transfer)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_transfer_reservation.assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID) 
  LEFT JOIN gks_crm_channel_sale ON gks_transfer_reservation.crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact ON gks_transfer_reservation.crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.ID)
  LEFT JOIN gks_ads_campain ON gks_transfer_reservation.crm_channel_campain_id = gks_ads_campain.id_ads_campain)
  LEFT JOIN gks_company on gks_transfer.company_id = gks_company.id_company)
  LEFT JOIN gks_company_subs on gks_transfer.company_sub_id = gks_company_subs.id_company_sub)
  LEFT JOIN gks_acc_journal ON gks_transfer_reservation.transfer_reservation_journal_id = gks_acc_journal.id_acc_journal) 
  LEFT JOIN gks_acc_eidi_parastatikon AS whi_gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = whi_gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
  LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
  LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
  LEFT JOIN gks_acc_seires ON gks_transfer_reservation.transfer_reservation_seira_id = gks_acc_seires.id_acc_seira)
  LEFT JOIN gks_payment_acquirers ON gks_transfer_reservation.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer)
  
  LEFT JOIN gks_poi AS gks_poi_from ON gks_transfer_reservation.poi_id_from = gks_poi_from.id_poi) 
  LEFT JOIN gks_poi AS gks_poi_to ON gks_transfer_reservation.poi_id_to = gks_poi_to.id_poi
  where gks_transfer_reservation.id_transfer_reservation = ".$id;

//  //print '<pre>'; print_r($sql);die();
  
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
  if ($result->num_rows!=1) {$ret['message']=gks_lang('Δεν βρέθηκε η κράτηση με ID').' <b>'.$id.'</b>';debug_mail(false,$ret['message'],$sql); return $ret;}
  $row = $result->fetch_assoc();
  
  $row['acc_journal_descr']=gks_lang_pft($row_form['gks_lang'],'gks_acc_journal','acc_journal_descr',$row['transfer_reservation_journal_id'],$row['acc_journal_descr']);
  $row['payment_acquirer_name']=gks_lang_pft($row_form['gks_lang'],'gks_payment_acquirers','payment_acquirer_name',$row['tropos_pliromis'],$row['payment_acquirer_name']);
  //$row['delivery_method_name']=gks_lang_pft($row_form['gks_lang'],'gks_delivery_methods','delivery_method_name',$row['tropos_apostolis'],$row['delivery_method_name']);
  $row['country_name']=gks_lang_pft($row_form['gks_lang'],'gks_country','country_name',$row['ma_country_id'],$row['country_name']);
  $row['nomos_descr']=gks_lang_pft($row_form['gks_lang'],'gks_nomoi','nomos_descr',$row['ma_nomos_id'],$row['nomos_descr']);

  $row['country_name_en_US']='';
  gks_lang_data_obj_insert_to_row($row,'gks_country',array(
    array('id' => $row['ma_country_id'], 'filename'=>'country_name', 'new_field' => 'country_name_%s'),
  ));
  $row['nomos_descr_en_US']='';
  gks_lang_data_obj_insert_to_row($row,'gks_nomoi',array(
    array('id' => $row['ma_nomos_id'], 'filename'=>'nomos_descr', 'new_field' => 'nomos_descr_%s'),
  ));
  //print '<pre>';print_r($row);die();
   
  $company_id=intval($row['company_id']);
  $company_sub_id=intval($row['company_sub_id']);

  $row_person=array();
  $row_person['id']=$row['user_id'];
  $row_person['nickname_add']=trim_gks($row['gks_nickname_add']);
  $row_person['nickname_edit']=trim_gks($row['gks_nickname_edit']);
  $row_person['nickname']=trim_gks($row['gks_nickname']);
  $row_person['email']=$row['user_email'];
  $row_person['url']=trim_gks($row['user_url']);
  $row_person['first_name']=$row['user_first_name'];
  $row_person['last_name']=$row['user_last_name'];
  $row_person['phone']=$row['phone_home'];
  $row_person['mobile']=$row['user_mobile'];
  $row_person['user_lang']=$row['user_lang'];
  $row_person['eponimia']=$row['eponimia'];
  $row_person['title']=$row['title'];
  $row_person['afm']=$row['afm'];
  $row_person['doy']=$row['doy'];
  $row_person['epaggelma']=$row['epaggelma'];
  $row_person['odos']=$row['ma_odos'];
  $row_person['arithmos']=$row['ma_arithmos'];
  $row_person['orofos']=$row['ma_orofos'];
  $row_person['perioxi']=$row['ma_perioxi'];
  $row_person['poli']=$row['ma_poli'];
  $row_person['tk']=$row['ma_tk'];
  $row_person['country_id']=$row['ma_country_id'];
  $row_person['country_name']=$row['country_name'];
  $row_person['country_name_en']=$row['country_name_en_US'];
  $row_person['country_initials']=$row['country_initials'];
  $row_person['country_initials3']=$row['country_initials3'];
  $row_person['country_ee']=$row['country_ee'];
  $row_person['nomos_id']=$row['ma_nomos_id'];
  $row_person['nomos_descr']=$row['nomos_descr'];
  $row_person['nomos_descr_en']=$row['nomos_descr_en_US'];
  $row_person['pricelist_id']=$row['pricelist_id'];
  $row_person['pricelist']=$row['pricelist_descr'];
  $row_person['fiscal_position_id']=$row['fiscal_position_id'];
  $row_person['fiscal_position']=$row['fiscal_position_descr'];
  $row_person['antisimvalomenos_label']=$row['antisimvalomenos_label'];
  $row_person['antisimvalomenos_label_en']=$row['antisimvalomenos_label_en'];
  

  $row_person['address_text']=gks_print_address_text($row);
//  $row_person['dest_name']=$row['destination_data_name'];
//  $row_person['dest_phone']=$row['destination_data_phone'];
//  $row_person['dest_odos']=$row['destination_data_odos'];
//  $row_person['dest_perioxi']=$row['destination_data_perioxi'];
//  $row_person['dest_poli']=$row['destination_data_poli'];
//  $row_person['dest_tk']=$row['destination_data_tk'];
//  $row_person['dest_country_id']=$row['destination_data_country_id'];
//  $row_person['dest_country_name']=$row['country_name_dest'];
//  $row_person['dest_country_name_en']=$row['country_name_en_US_dest'];
//  $row_person['dest_nomos_id']=$row['destination_data_nomos_id'];
//  $row_person['dest_nomos_descr']=$row['nomos_descr_dest'];
//  $row_person['dest_nomos_descr_en']=$row['nomos_descr_en_US_dest'];

  //if ($row['address_extra'] < 1) {
  if (true) {
    $row_person['dest_name']='';
    $row_person['dest_phone']='';
    $row_person['dest_odos']='';
    $row_person['dest_arithmos']='';
    $row_person['dest_orofos']='';
    $row_person['dest_perioxi']='';
    $row_person['dest_poli']='';
    $row_person['dest_tk']='';
    $row_person['dest_country_id']=0;
    $row_person['dest_country_name']='';
    $row_person['dest_country_name_en']='';
    $row_person['dest_nomos_id']=0;
    $row_person['dest_nomos_descr']='';
    $row_person['dest_nomos_descr_en']='';    
  }

  $row_doc=array();
  $row_doc['title']=$row['acc_journal_descr'];
  $row_doc['title_pre']='';
  
  if ($row_form['gks_lang']=='el-GR') {
    $row_doc['title_pre']=getTransferReservationStatusDescr($row['transfer_reservation_status']);
  } else {
    $row_doc['title_pre']=getTransferReservationStatusDescr_en_US($row['transfer_reservation_status']);
  }
  $row_doc['company']=$row['company_title'];
  if (trim_gks($row['company_sub_title'])!='') $row_doc['company'].=' \ '.$row['company_sub_title'];
  
  $row_doc['company_title']=$row['company_title'];
  $row_doc['company_sub_title']=trim_gks($row['company_sub_title']);
  if ($row_doc['company_sub_title']=='') $row_doc['company_sub_title']=gks_lang('Κεντρικό');
  

  $row_doc['date']=myDateTimeFormat(_time_user(strtotime($row['transfer_reservation_date']),1));
  $row_doc['date_d']=myDateFormat(_time_user(strtotime($row['transfer_reservation_date']),1));
  $row_doc['date_dw']=myDateFormatw(_time_user(strtotime($row['transfer_reservation_date']),1),$row_form['gks_lang']);
  $row_doc['date_dt']=myDateTimeFormat(_time_user(strtotime($row['transfer_reservation_date']),1));
  $row_doc['date_dtw']=myDateTimeFormatw(_time_user(strtotime($row['transfer_reservation_date']),1),$row_form['gks_lang']);
  $row_doc['date_dtt']=myDateTimeFormatText(_time_user(strtotime($row['transfer_reservation_date']),1),$row_form['gks_lang']);
  $row_doc['datefull']=showDate(strtotime($row['transfer_reservation_date']),'d/m/Y H:i:s',1);

  $row_doc['mydate_add']=showDate(strtotime($row['mydate_add']),'d/m/Y H:i',1);
  $row_doc['mydate_edit']=showDate(strtotime($row['mydate_edit']),'d/m/Y H:i',1);
  $row_doc['seira']=trim_gks($row['seira_code']);
  $row_doc['seira_descr']=trim_gks($row['seira_descr']);
  $row_doc['number']=trim_gks($row['transfer_reservation_number_int']);
  $row_doc['number_str']=trim_gks($row['transfer_reservation_number_str']);
  $row_doc['mark']='';
  $row_doc['aade_qrurl']='';
  $row_doc['aade_paroxos_qrurl']='';
  $row_doc['paroxos_tf1_url']='';
  $row_doc['aade_invoiceuid']='';
  $row_doc['paroxos_authenticationCode']='';
  $row_doc['products_posotita']=trim_gks($row['products_posotita']);
  $row_doc['gks_price_original_net']=trim_gks($row['gks_price_original_net']);
  $row_doc['gks_price_net']=trim_gks($row['gks_price_net']);
  $row_doc['gks_price_fpa']=trim_gks($row['gks_price_fpa']);
  $row_doc['gks_price_netfpa']=trim_gks($row['gks_price_netfpa']);
  $row_doc['gks_price_total']=trim_gks($row['gks_price_total']);
  $row_doc['totalWithheldAmount']=trim_gks($row['totalWithheldAmount']);
  $row_doc['totalOtherTaxesAmount']=trim_gks($row['totalOtherTaxesAmount']);
  $row_doc['totalStampDutyamount']=trim_gks($row['totalStampDutyamount']);
  $row_doc['totalFeesAmount']=trim_gks($row['totalFeesAmount']);
  $row_doc['totalDeductionsAmount']=0; //trim_gks($row['totalDeductionsAmount']);

  $row_doc['transfer_booking_number']=trim_gks($row['transfer_booking_number']);
  $row_doc['transfer_start']=myDateTimeFormat(strtotime($row['transfer_start']));
  $row_doc['transfer_start_d']=myDateFormat(strtotime($row['transfer_start']));
  $row_doc['transfer_start_dw']=myDateFormatw(strtotime($row['transfer_start']),$row_form['gks_lang']);
  $row_doc['transfer_start_dt']=myDateTimeFormat(strtotime($row['transfer_start']));
  $row_doc['transfer_start_dtw']=myDateTimeFormatw(strtotime($row['transfer_start']),$row_form['gks_lang']);
  $row_doc['transfer_start_dtt']=myDateTimeFormatText(strtotime($row['transfer_start']),$row_form['gks_lang']);

  $row_doc['transfer_end']=myDateTimeFormat(strtotime($row['transfer_end']));
  $row_doc['transfer_end_d']=myDateFormat(strtotime($row['transfer_end']));
  $row_doc['transfer_end_dw']=myDateFormatw(strtotime($row['transfer_end']),$row_form['gks_lang']);
  $row_doc['transfer_end_dt']=myDateTimeFormat(strtotime($row['transfer_end']));
  $row_doc['transfer_end_dtw']=myDateTimeFormatw(strtotime($row['transfer_end']),$row_form['gks_lang']);
  $row_doc['transfer_end_dtt']=myDateTimeFormatText(strtotime($row['transfer_end']),$row_form['gks_lang']);

  $row_doc['oximata_plithos']=0; //intval($row['rooms_plithos']);
  $row_doc['duration_secs']=intval($row['duration_secs']);
  
  $temp1=date('j:G:i',$row_doc['duration_secs']);
  $temp1=explode(':',$temp1);
  $temp1[0]=intval($temp1[0]); //day
  $temp1[1]=intval($temp1[1]); //hour
  $temp1[2]=intval($temp1[2]); //minute
  if ($temp1[0]>1) $temp1[1]+=24*($temp1[0]-1);
  $temp2=($temp1[1]>=10 ? $temp1[1] : '0'.$temp1[1]).':'.($temp1[2]>=10 ? $temp1[2] : '0'.$temp1[2]);

  $row_doc['duration_minutes_secs']=$temp2;
  
      
  
  $row_doc['num_adults']=intval($row['num_adults']);
  $row_doc['num_childs']=intval($row['num_childs']);
  $row_doc['num_babys']=intval($row['num_babys']);
  $row_doc['num_epivates']=intval($row['num_adults'])+intval($row['num_childs'])+intval($row['num_babys']);

  
  $OUTWARD_id_transfer_reservation=$id;
  $RETURN_id_transfer_reservation=0;
  $row_outward=[];$row_outward['transfer_reservation_status']='010draft';$row_outward['poi_descr_from']='';$row_outward['poi_descr_to']='';
  $row_return=[]; $row_return['transfer_reservation_status'] ='010draft';$row_return['poi_descr_from']='';$row_return['poi_descr_to']='';

  if (intval($row['is_return_transfer_for_id'])==0) {
    $row_outward=$row;
    
    $sql_tt=select_gks_transfer_reservation()." where is_return_transfer_for_id=".$id;
    $result_tt = $db_link->query($sql_tt);   
    if (!$result_tt) {debug_mail(false,'error sql',$sql_tt);$ret['message']='sql error'; return $ret;}
    if ($result_tt->num_rows>0) {
      $row_return = $result_tt->fetch_assoc();
      $RETURN_id_transfer_reservation=$row_return['id_transfer_reservation'];
    }
  } else {
    $sql_tt=select_gks_transfer_reservation()." where id_transfer_reservation=".intval($row['is_return_transfer_for_id']);
    $result_tt = $db_link->query($sql_tt);        
    if (!$result_tt) {debug_mail(false,'error sql',$sql_tt);$ret['message']='sql error'; return $ret;}
    if ($result_tt->num_rows>=1) {
      $row_outward = $result_tt->fetch_assoc();
      $row_return=$row;
      
      $OUTWARD_id_transfer_reservation=$row_outward['id_transfer_reservation'];
      $RETURN_id_transfer_reservation=$id;
      
    } else {
      $row_outward=$row;
    }  
  }

  $poi_type1=$row['poi_type_id_from'];
  $poi_type2=$row['poi_type_id_to'];
  
  $rsrv_direction='';
  
  if ($row['poi_type_id_from']==2 or $row['poi_type_id_from']==3 or $row['poi_type_id_from']==4) {
    $rsrv_direction='tori';
  } else if ($row['poi_type_id_to']==2 or $row['poi_type_id_to']==3 or $row['poi_type_id_to']==4) {
    $rsrv_direction='tole';
  }
    
  //echo $id.'|'.$OUTWARD_id_transfer_reservation.'|'.$RETURN_id_transfer_reservation.'|'.$rsrv_direction;die();
  if ($RETURN_id_transfer_reservation==$id) {//eimaste sto return
    if ($rsrv_direction=='tori')    $rsrv_direction='tole';
    else if ($rsrv_direction=='tole')  $rsrv_direction='tori';
  }

  if ($OUTWARD_id_transfer_reservation==$id)     $type_of_transfer='is_normal';
  else if ($RETURN_id_transfer_reservation==$id) $type_of_transfer='is_return';

  
  $params_vf=array(
    'poi_type1' => $poi_type1,
    'poi_type2' => $poi_type2,
    'direction' => $rsrv_direction, //'tori', //$remote_return['data']['data']['val_direction'], //tori tole
    'date2_time' => ($RETURN_id_transfer_reservation==0 ? 0 : 1),  //$remote_return['data']['data']['val_date2_time'],
    'type_of_transfer' => $type_of_transfer,
  );
  $my_visible_fields= gks_popsicle_transfer_fields_backend($params_vf);
  
  //echo '<pre>';print $OUTWARD_id_transfer_reservation.'|'.$id;die();
  
  if ($OUTWARD_id_transfer_reservation==$id) {
    $row_doc['outward_transfer_start']=null;
    $row_doc['outward_transfer_start_d']=null;
    $row_doc['outward_transfer_start_dw']=null;
    $row_doc['outward_transfer_start_dt']=null;
    $row_doc['outward_transfer_start_dtw']=null;
    $row_doc['outward_transfer_start_dtt']=null;
    
    $row_doc['outward_transfer_end']=null;
    $row_doc['outward_transfer_end_d']=null;
    $row_doc['outward_transfer_end_dw']=null;
    $row_doc['outward_transfer_end_dt']=null;
    $row_doc['outward_transfer_end_dtw']=null;
    $row_doc['outward_transfer_end_dtt']=null;
  } else {
    $row_doc['outward_transfer_start']=myDateTimeFormat(strtotime($row_outward['transfer_start']));
    $row_doc['outward_transfer_start_d']=myDateFormat(strtotime($row_outward['transfer_start']));
    $row_doc['outward_transfer_start_dw']=myDateFormatw(strtotime($row_outward['transfer_start']),$row_form['gks_lang']);
    $row_doc['outward_transfer_start_dt']=myDateTimeFormat(strtotime($row_outward['transfer_start']));
    $row_doc['outward_transfer_start_dtw']=myDateTimeFormatw(strtotime($row_outward['transfer_start']),$row_form['gks_lang']);
    $row_doc['outward_transfer_start_dtt']=myDateTimeFormatText(strtotime($row_outward['transfer_start']),$row_form['gks_lang']);

    $row_doc['outward_transfer_end']=myDateTimeFormat(strtotime($row_outward['transfer_end']));
    $row_doc['outward_transfer_end_d']=myDateFormat(strtotime($row_outward['transfer_end']));
    $row_doc['outward_transfer_end_dw']=myDateFormatw(strtotime($row_outward['transfer_end']),$row_form['gks_lang']);
    $row_doc['outward_transfer_end_dt']=myDateTimeFormat(strtotime($row_outward['transfer_end']));
    $row_doc['outward_transfer_end_dtw']=myDateTimeFormatw(strtotime($row_outward['transfer_end']),$row_form['gks_lang']);
    $row_doc['outward_transfer_end_dtt']=myDateTimeFormatText(strtotime($row_outward['transfer_end']),$row_form['gks_lang']);
  }
  

  $row_doc['outward_poi_descr_from']=trim_gks($row_outward['poi_descr_from']);
  $row_doc['outward_poi_descr_from_place']=trim_gks($row_outward['poi_from_place_formatted_address']);
  $row_doc['outward_from_pick_up_point']=trim_gks($row['outward_from_pick_up_point']);

  if (empty($row['outward_from_pick_up_time'])) {
    $row_doc['outward_from_pick_up_time']='';
    $row_doc['outward_from_pick_up_time_d']='';
    $row_doc['outward_from_pick_up_time_dw']='';
    $row_doc['outward_from_pick_up_time_dt']='';
    $row_doc['outward_from_pick_up_time_dtw']='';
    $row_doc['outward_from_pick_up_time_dtt']='';
  } else {
    $row_doc['outward_from_pick_up_time']=myDateTimeFormat(strtotime($row['outward_from_pick_up_time']));
    $row_doc['outward_from_pick_up_time_d']=myDateFormat(strtotime($row['outward_from_pick_up_time']));
    $row_doc['outward_from_pick_up_time_dw']=myDateFormatw(strtotime($row['outward_from_pick_up_time']),$row_form['gks_lang']);
    $row_doc['outward_from_pick_up_time_dt']=myDateTimeFormat(strtotime($row['outward_from_pick_up_time']));
    $row_doc['outward_from_pick_up_time_dtw']=myDateTimeFormatw(strtotime($row['outward_from_pick_up_time']),$row_form['gks_lang']);
    $row_doc['outward_from_pick_up_time_dtt']=myDateTimeFormatText(strtotime($row['outward_from_pick_up_time']),$row_form['gks_lang']);
  }

  if (empty($row['outward_from_pick_up_time_max'])) {
    $row_doc['outward_from_pick_up_time_max']='';
    $row_doc['outward_from_pick_up_time_max_d']='';
    $row_doc['outward_from_pick_up_time_max_dw']='';
    $row_doc['outward_from_pick_up_time_max_dt']='';
    $row_doc['outward_from_pick_up_time_max_dtw']='';
    $row_doc['outward_from_pick_up_time_max_dtt']='';
  } else {
    $row_doc['outward_from_pick_up_time_max']=myDateTimeFormat(strtotime($row['outward_from_pick_up_time_max']));
    $row_doc['outward_from_pick_up_time_max_d']=myDateFormat(strtotime($row['outward_from_pick_up_time_max']));
    $row_doc['outward_from_pick_up_time_max_dw']=myDateFormatw(strtotime($row['outward_from_pick_up_time_max']),$row_form['gks_lang']);
    $row_doc['outward_from_pick_up_time_max_dt']=myDateTimeFormat(strtotime($row['outward_from_pick_up_time_max']));
    $row_doc['outward_from_pick_up_time_max_dtw']=myDateTimeFormatw(strtotime($row['outward_from_pick_up_time_max']),$row_form['gks_lang']);
    $row_doc['outward_from_pick_up_time_max_dtt']=myDateTimeFormatText(strtotime($row['outward_from_pick_up_time_max']),$row_form['gks_lang']);
  }  
  
  $row_doc['outward_from_airline']=trim_gks($row['outward_from_airline']);
  $row_doc['outward_from_flight_number']=trim_gks($row['outward_from_flight_number']);
  $row_doc['outward_from_originating_airport']=trim_gks($row['outward_from_originating_airport']);

  
  if (empty($row['outward_from_flight_arrival_time'])) {
    $row_doc['outward_from_flight_arrival_time']='';
    $row_doc['outward_from_flight_arrival_time_d']='';
    $row_doc['outward_from_flight_arrival_time_dw']='';
    $row_doc['outward_from_flight_arrival_time_dt']='';
    $row_doc['outward_from_flight_arrival_time_dtw']='';
    $row_doc['outward_from_flight_arrival_time_dtt']='';
  } else {
    $row_doc['outward_from_flight_arrival_time']=myDateTimeFormat(strtotime($row['outward_from_flight_arrival_time']));
    $row_doc['outward_from_flight_arrival_time_d']=myDateFormat(strtotime($row['outward_from_flight_arrival_time']));
    $row_doc['outward_from_flight_arrival_time_dw']=myDateFormatw(strtotime($row['outward_from_flight_arrival_time']),$row_form['gks_lang']);
    $row_doc['outward_from_flight_arrival_time_dt']=myDateTimeFormat(strtotime($row['outward_from_flight_arrival_time']));
    $row_doc['outward_from_flight_arrival_time_dtw']=myDateTimeFormatw(strtotime($row['outward_from_flight_arrival_time']),$row_form['gks_lang']);
    $row_doc['outward_from_flight_arrival_time_dtt']=myDateTimeFormatText(strtotime($row['outward_from_flight_arrival_time']),$row_form['gks_lang']);
  }  
  

  $row_doc['outward_poi_descr_to']=trim_gks($row_outward['poi_descr_to']);
  $row_doc['outward_poi_descr_to_place']=trim_gks($row_outward['poi_to_place_formatted_address']);
  $row_doc['outward_to_drop_off_point']=trim_gks($row['outward_to_drop_off_point']);
  $row_doc['outward_to_departure_airline']=trim_gks($row['outward_to_departure_airline']);
  $row_doc['outward_to_flight_number']=trim_gks($row['outward_to_flight_number']);

  if (empty($row_doc['outward_to_flight_departure_time'])) {
    $row_doc['outward_to_flight_departure_time']='';
    $row_doc['outward_to_flight_departure_time_d']='';
    $row_doc['outward_to_flight_departure_time_dw']='';
    $row_doc['outward_to_flight_departure_time_dt']='';
    $row_doc['outward_to_flight_departure_time_dtw']='';
    $row_doc['outward_to_flight_departure_time_dtt']='';
  } else {
    $row_doc['outward_to_flight_departure_time']=myDateTimeFormat(strtotime($row['outward_to_flight_departure_time']));
    $row_doc['outward_to_flight_departure_time_d']=myDateFormat(strtotime($row['outward_to_flight_departure_time']));
    $row_doc['outward_to_flight_departure_time_dw']=myDateFormatw(strtotime($row['outward_to_flight_departure_time']),$row_form['gks_lang']);
    $row_doc['outward_to_flight_departure_time_dt']=myDateTimeFormat(strtotime($row['outward_to_flight_departure_time']));
    $row_doc['outward_to_flight_departure_time_dtw']=myDateTimeFormatw(strtotime($row['outward_to_flight_departure_time']),$row_form['gks_lang']);
    $row_doc['outward_to_flight_departure_time_dtt']=myDateTimeFormatText(strtotime($row['outward_to_flight_departure_time']),$row_form['gks_lang']);
  }
  $row_doc['outward_display']=';';
  $row_doc['return_display']=';';
  if ($RETURN_id_transfer_reservation==0) {
    $row_doc['return_display']='none;';
  }
  
  $row_doc['return_transfer_start']=null;
  $row_doc['return_transfer_start_d']=null;
  $row_doc['return_transfer_start_dw']=null;
  $row_doc['return_transfer_start_dt']=null;
  $row_doc['return_transfer_start_dtw']=null;
  $row_doc['return_transfer_start_dtt']=null;
  
  $row_doc['return_transfer_end']=null;
  $row_doc['return_transfer_end_d']=null;
  $row_doc['return_transfer_end_dw']=null;
  $row_doc['return_transfer_end_dt']=null;
  $row_doc['return_transfer_end_dtw']=null;
  $row_doc['return_transfer_end_dtt']=null;
  
  if ($RETURN_id_transfer_reservation!=$id) {
    
    $row_doc['return_transfer_start']=myDateTimeFormat(strtotime($row['transfer_start']));
    $row_doc['return_transfer_start_d']=myDateFormat(strtotime($row['transfer_start']));
    $row_doc['return_transfer_start_dw']=myDateFormatw(strtotime($row['transfer_start']),$row_form['gks_lang']);
    $row_doc['return_transfer_start_dt']=myDateTimeFormat(strtotime($row['transfer_start']));
    $row_doc['return_transfer_start_dtw']=myDateTimeFormatw(strtotime($row['transfer_start']),$row_form['gks_lang']);
    $row_doc['return_transfer_start_dtt']=myDateTimeFormatText(strtotime($row['transfer_start']),$row_form['gks_lang']);

    $row_doc['return_transfer_end']=myDateTimeFormat(strtotime($row['transfer_end']));
    $row_doc['return_transfer_end_d']=myDateFormat(strtotime($row['transfer_end']));
    $row_doc['return_transfer_end_dw']=myDateFormatw(strtotime($row['transfer_end']),$row_form['gks_lang']);
    $row_doc['return_transfer_end_dt']=myDateTimeFormat(strtotime($row['transfer_end']));
    $row_doc['return_transfer_end_dtw']=myDateTimeFormatw(strtotime($row['transfer_end']),$row_form['gks_lang']);
    $row_doc['return_transfer_end_dtt']=myDateTimeFormatText(strtotime($row['transfer_end']),$row_form['gks_lang']);

  }



  $row_doc['return_poi_descr_from']=trim_gks($row_return['poi_descr_from']);
  $row_doc['return_poi_descr_from_place']=trim_gks($row_return['poi_from_place_formatted_address']);
  $row_doc['return_from_address_different']=intval($row['return_from_address_different'])==0 ? false : true;
  $row_doc['return_from_pick_up_point']=trim_gks($row['return_from_pick_up_point']);               
  
  if (empty($row['return_from_pick_up_time'])) {
    $row_doc['return_from_pick_up_time']='';
    $row_doc['return_from_pick_up_time_d']='';
    $row_doc['return_from_pick_up_time_dw']='';
    $row_doc['return_from_pick_up_time_dt']='';
    $row_doc['return_from_pick_up_time_dtw']='';
    $row_doc['return_from_pick_up_time_dtt']='';
  } else {
    $row_doc['return_from_pick_up_time']=myDateTimeFormat(strtotime($row['return_from_pick_up_time']));
    $row_doc['return_from_pick_up_time_d']=myDateFormat(strtotime($row['return_from_pick_up_time']));
    $row_doc['return_from_pick_up_time_dw']=myDateFormatw(strtotime($row['return_from_pick_up_time']),$row_form['gks_lang']);
    $row_doc['return_from_pick_up_time_dt']=myDateTimeFormat(strtotime($row['return_from_pick_up_time']));
    $row_doc['return_from_pick_up_time_dtw']=myDateTimeFormatw(strtotime($row['return_from_pick_up_time']),$row_form['gks_lang']);
    $row_doc['return_from_pick_up_time_dtt']=myDateTimeFormatText(strtotime($row['return_from_pick_up_time']),$row_form['gks_lang']);
  }
  
  if (empty($row['return_from_pick_up_time_max'])) {
    $row_doc['return_from_pick_up_time_max']='';
    $row_doc['return_from_pick_up_time_max_d']='';
    $row_doc['return_from_pick_up_time_max_dw']='';
    $row_doc['return_from_pick_up_time_max_dt']='';
    $row_doc['return_from_pick_up_time_max_dtw']='';
    $row_doc['return_from_pick_up_time_max_dtt']='';
  } else {
    $row_doc['return_from_pick_up_time_max']=myDateTimeFormat(strtotime($row['return_from_pick_up_time_max']));
    $row_doc['return_from_pick_up_time_max_d']=myDateFormat(strtotime($row['return_from_pick_up_time_max']));
    $row_doc['return_from_pick_up_time_max_dw']=myDateFormatw(strtotime($row['return_from_pick_up_time_max']),$row_form['gks_lang']);
    $row_doc['return_from_pick_up_time_max_dt']=myDateTimeFormat(strtotime($row['return_from_pick_up_time_max']));
    $row_doc['return_from_pick_up_time_max_dtw']=myDateTimeFormatw(strtotime($row['return_from_pick_up_time_max']),$row_form['gks_lang']);
    $row_doc['return_from_pick_up_time_max_dtt']=myDateTimeFormatText(strtotime($row['return_from_pick_up_time_max']),$row_form['gks_lang']);
  }


  $row_doc['return_from_airline']=trim_gks($row['return_from_airline']);
  $row_doc['return_from_flight_number']=trim_gks($row['return_from_flight_number']);
  $row_doc['return_from_originating_airport']=trim_gks($row['return_from_originating_airport']);

  if (empty($row['return_from_flight_arrival_time'])) {
    $row_doc['return_from_flight_arrival_time']='';
    $row_doc['return_from_flight_arrival_time_d']='';
    $row_doc['return_from_flight_arrival_time_dw']='';
    $row_doc['return_from_flight_arrival_time_dt']='';
    $row_doc['return_from_flight_arrival_time_dtw']='';
    $row_doc['return_from_flight_arrival_time_dtt']='';
  } else {
    $row_doc['return_from_flight_arrival_time']=myDateTimeFormat(strtotime($row['return_from_flight_arrival_time']));
    $row_doc['return_from_flight_arrival_time_d']=myDateFormat(strtotime($row['return_from_flight_arrival_time']));
    $row_doc['return_from_flight_arrival_time_dw']=myDateFormatw(strtotime($row['return_from_flight_arrival_time']),$row_form['gks_lang']);
    $row_doc['return_from_flight_arrival_time_dt']=myDateTimeFormat(strtotime($row['return_from_flight_arrival_time']));
    $row_doc['return_from_flight_arrival_time_dtw']=myDateTimeFormatw(strtotime($row['return_from_flight_arrival_time']),$row_form['gks_lang']);
    $row_doc['return_from_flight_arrival_time_dtt']=myDateTimeFormatText(strtotime($row['return_from_flight_arrival_time']),$row_form['gks_lang']);
  }


  $row_doc['return_poi_descr_to']=trim_gks($row_return['poi_descr_to']);
  $row_doc['return_poi_descr_to_place']=trim_gks($row_return['poi_to_place_formatted_address']);
  $row_doc['return_to_airline']=trim_gks($row['return_to_airline']);
  $row_doc['return_to_flight_number']=trim_gks($row['return_to_flight_number']);
  
  if (empty($row_doc['return_to_flight_departure_time'])) {
    $row_doc['return_to_flight_departure_time']='';
    $row_doc['return_to_flight_departure_time_d']='';
    $row_doc['return_to_flight_departure_time_dw']='';
    $row_doc['return_to_flight_departure_time_dt']='';
    $row_doc['return_to_flight_departure_time_dtw']='';
    $row_doc['return_to_flight_departure_time_dtt']='';
  } else {
    $row_doc['return_to_flight_departure_time']=myDateTimeFormat(strtotime($row['return_to_flight_departure_time']));
    $row_doc['return_to_flight_departure_time_d']=myDateFormat(strtotime($row['return_to_flight_departure_time']));
    $row_doc['return_to_flight_departure_time_dw']=myDateFormatw(strtotime($row['return_to_flight_departure_time']),$row_form['gks_lang']);
    $row_doc['return_to_flight_departure_time_dt']=myDateTimeFormat(strtotime($row['return_to_flight_departure_time']));
    $row_doc['return_to_flight_departure_time_dtw']=myDateTimeFormatw(strtotime($row['return_to_flight_departure_time']),$row_form['gks_lang']);
    $row_doc['return_to_flight_departure_time_dtt']=myDateTimeFormatText(strtotime($row['return_to_flight_departure_time']),$row_form['gks_lang']);
  }  
  
  $row_doc['return_to_address_different']=intval($row['return_to_address_different'])==0 ? false : true;             
  $row_doc['return_to_drop_off_point']=trim_gks($row['return_to_drop_off_point']);


  if ($row_doc['return_from_address_different']==false) $row_doc['return_from_pick_up_point']=null;
  if ($row_doc['return_to_address_different']==false) $row_doc['return_to_drop_off_point']=null;
  


  foreach ($my_visible_fields['visible'] as $vkey => $is_visible) {
    if (isset($row_doc[$vkey])) {
      if ($is_visible==false) $row_doc[$vkey]=null; 
    }
  }
  
  //print '<pre>';print_r($my_visible_fields);die();
  
  $row_doc['text_act1ofal']=$my_visible_fields['texts']['text_act1ofal'];
  $row_doc['text_act1otdal']=$my_visible_fields['texts']['text_act1otdal'];
  $row_doc['text_act1rfal']=$my_visible_fields['texts']['text_act1rfal'];
  $row_doc['text_act1rtal']=$my_visible_fields['texts']['text_act1rtal'];
  $row_doc['text_act2offn']=$my_visible_fields['texts']['text_act2offn'];
  $row_doc['text_act2otfn']=$my_visible_fields['texts']['text_act2otfn'];
  $row_doc['text_act2rffn']=$my_visible_fields['texts']['text_act2rffn'];
  $row_doc['text_act2rtfn']=$my_visible_fields['texts']['text_act2rtfn'];
  $row_doc['text_act3ofoap']=$my_visible_fields['texts']['text_act3ofoap'];
  $row_doc['text_act3rfoap']=$my_visible_fields['texts']['text_act3rfoap'];
  $row_doc['text_act4offat']=$my_visible_fields['texts']['text_act4offat'];
  $row_doc['text_act4rffat']=$my_visible_fields['texts']['text_act4rffat'];
  $row_doc['text_act5otfdt']=$my_visible_fields['texts']['text_act5otfdt'];
  $row_doc['text_act5rtfdt']=$my_visible_fields['texts']['text_act5rtfdt'];
  $row_doc['text_act6ofal']=$my_visible_fields['texts']['text_act6ofal'];
  $row_doc['text_act6otdal']=$my_visible_fields['texts']['text_act6otdal'];
  $row_doc['text_act6rfal']=$my_visible_fields['texts']['text_act6rfal'];
  $row_doc['text_act6rtal']=$my_visible_fields['texts']['text_act6rtal'];
  $row_doc['text_act7ofoap']=$my_visible_fields['texts']['text_act7ofoap'];
  $row_doc['text_act8rfoap']=$my_visible_fields['texts']['text_act8rfoap'];

 
  //$row_doc['duration_minutes_secs']='<pre>'.print_r($my_visible_fields['texts'],true).'</pre>';

  $row_person['person_balance_before']=gks_balance_calc(['id' => $row['user_id'], 'except_id_transfer_reservation' => $row['id_transfer_reservation']]);
  $row_person['person_balance_after']=gks_balance_calc(['id' => $row['user_id']]);
  

  $row_doc['enarji_apostolis']=     ''; //(isset($row['dispatch_date']) ? showDate(strtotime($row['dispatch_date']), 'd/m/Y H:i', 1) : '');
  $row_doc['enarji_apostolis_date']=''; //(isset($row['dispatch_date']) ? showDate(strtotime($row['dispatch_date']), 'd/m/Y', 1) : '');
  $row_doc['enarji_apostolis_time']=''; //(isset($row['dispatch_date']) ? showDate(strtotime($row['dispatch_date']), 'H:i', 1) : '');
  $row_doc['arithmos_oximatos']=''; //trim_gks($row['vehicle_number']);
  $row_doc['skopos_diakinisis']=''; //trim_gks($row['aade_skopos_diakinisis_descr']);
  $row_doc['tropos_pliromis']=trim_gks($row['payment_acquirer_name']);
  //$row_doc['tropos_apostolis']=trim_gks($row['delivery_method_name']);
  
  $row_doc['arithmos_aposolis']=trim_gks($row['delivery_number']);
  $row_doc['user_notes']=trim_gks($row['user_notes']);
  $row_doc['note_doc']=trim_gks($row['sxolio']);
  $row_doc['note_production']='';
  $row_doc['note_logistirio']=trim_gks($row['note_logistirio']);
  $row_doc['ddate']='';
  $row_doc['occasion_title']='';
  $row_doc['occasion_type_descr']='';
  $row_doc['occasion_mydate_add']= '';


  $row_doc['idiotites_text']=gks_print_idiotites_text($row);
  $row_doc['photos']='';
  $row_doc['links']='';
  

  
  
  
  $row_canceled_doc=array();
  
  $row_canceled_doc['display']='none';
  $row_canceled_doc['title']='';
  $row_canceled_doc['title_pre']='';
  $row_canceled_doc['company']='';
  $row_canceled_doc['date']='';
  $row_canceled_doc['datefull']='';
  $row_canceled_doc['seira']='';
  $row_canceled_doc['seira_descr']='';
  $row_canceled_doc['number']=0;
  $row_canceled_doc['number_str']='';
  $row_canceled_doc['mark']='';
  $row_canceled_doc['aade_qrurl']='';
  $row_canceled_doc['aade_paroxos_qrurl']='';
  $row_canceled_doc['paroxos_tf1_url']='';
  $row_canceled_doc['aade_invoiceuid']='';
  $row_canceled_doc['paroxos_authenticationCode']='';

  $row_credit_doc=array();

  $row_credit_doc['display']='none';
  $row_credit_doc['title']='';
  $row_credit_doc['title_pre']='';
  $row_credit_doc['company']='';
  $row_credit_doc['date']='';
  $row_credit_doc['datefull']='';
  $row_credit_doc['seira']='';
  $row_credit_doc['seira_descr']='';
  $row_credit_doc['number']=0;
  $row_credit_doc['number_str']='';
  $row_credit_doc['mark']='';
  $row_credit_doc['aade_qrurl']='';
  $row_credit_doc['aade_paroxos_qrurl']='';
  $row_credit_doc['paroxos_tf1_url']='';
  $row_credit_doc['aade_invoiceuid']='';
  $row_credit_doc['paroxos_authenticationCode']='';
  
  
  
  $sql="SELECT gks_transfer_reservation_oximata.*, 
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit, 
  gks_assets.asset_title, 
  gks_assets.asset_photo,
  ".GKS_WP_TABLE_PREFIX."users.gks_nickname, gks_lang.lang_name, gks_lang.lang_ico, 
  gks_country.country_initials, gks_country.country_initials3, gks_country.country_ee, gks_country.country_name, 
  gks_nomoi.nomos_descr,
  gks_transfer_oxima_type.transfer_oxima_type_site_text,
                          transfer_oxima_type_site_text_en_US,
  gks_transfer_oxima_type.transfer_oxima_type_photo,
  gks_transfer_oxima_type.transfer_oxima_type_descr,
  gks_transfer_oxima_type.transfer_oxima_type_max_epivates,
  gks_transfer_oxima_type.transfer_oxima_type_max_suitcases,
  
  gks_transfer_oxima_type.transfer_oxima_type_max_booster,
  gks_transfer_oxima_type.transfer_oxima_type_max_kareklakia,
  gks_transfer_oxima_type.transfer_oxima_type_max_amajidia,
  gks_transfer_oxima_type.transfer_oxima_type_max_golfbag,
  gks_transfer_oxima_type.transfer_oxima_type_max_skis,
  gks_transfer_oxima_type.transfer_oxima_type_max_5minstop,
  gks_transfer_oxima_type.transfer_oxima_type_comments,
  
  gks_transfer_oxima_type.transfer_oxima_type_price_booster,
  gks_transfer_oxima_type.transfer_oxima_type_price_kareklakia,
  gks_transfer_oxima_type.transfer_oxima_type_price_amajidia,
  gks_transfer_oxima_type.transfer_oxima_type_price_golfbag,
  gks_transfer_oxima_type.transfer_oxima_type_price_skis,
  gks_transfer_oxima_type.transfer_oxima_type_price_5minstop,
  
  gks_transfer_oxima_type.transfer_oxima_type_service_door_to_door,
  gks_transfer_oxima_type.transfer_oxima_type_service_porter,
  gks_transfer_oxima_type.transfer_oxima_type_service_treat_yourself,
  gks_transfer_oxima_type.transfer_oxima_type_service_free_wifi,
  gks_transfer_oxima_type.transfer_oxima_type_service_bottled_water,
  
  
  gks_eshop_fpa.fpa_descr_print, gks_eshop_fpa.fpa_pososto,
  
  gks_aade_katigoria_parakratoumemenon_foron.aade_katigoria_parakratoumemenon_foron_descr, 
  gks_aade_katigoria_xartosimou.aade_katigoria_xartosimou_descr, 
  gks_aade_katigoria_telon.aade_katigoria_telon_descr, 
  gks_aade_katigoria_loipon_foron.aade_katigoria_loipon_foron_descr,
  
  gks_monades_metrisis.monada_descr, gks_eshop_fpa_base.fpa_base_descr,
  gks_eshop_fiscal_position.fiscal_position_descr, gks_eshop_pricelist.pricelist_descr
  
  FROM ((((((((((((((((((gks_transfer_reservation_oximata 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_transfer_reservation_oximata.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_transfer_reservation_oximata.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID) 
  LEFT JOIN gks_assets ON gks_transfer_reservation_oximata.transfer_oxima_asset_id = gks_assets.id_asset) 
  LEFT JOIN gks_transfer_oxima_type ON gks_transfer_reservation_oximata.transfer_oxima_type_id = gks_transfer_oxima_type.id_transfer_oxima_type) 
  LEFT JOIN (
    SELECT transfer_oxima_type_id, 
    transfer_oxima_type_descr as transfer_oxima_type_descr_en_US, 
    transfer_oxima_type_site_text as transfer_oxima_type_site_text_en_US 
    FROM gks_transfer_oxima_type_lang WHERE lang_code='en-US'
  ) AS gks_transfer_oxima_type_en_US ON gks_transfer_oxima_type.id_transfer_oxima_type = gks_transfer_oxima_type_en_US.transfer_oxima_type_id)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_transfer_reservation_oximata.ruser_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
  LEFT JOIN gks_lang ON gks_transfer_reservation_oximata.ruser_lang = gks_lang.id_lang) 
  LEFT JOIN gks_country ON gks_transfer_reservation_oximata.ruser_ma_country_id = gks_country.id_country) 
  LEFT JOIN gks_nomoi ON gks_transfer_reservation_oximata.ruser_ma_nomos_id = gks_nomoi.id_nomos)
  LEFT JOIN gks_eshop_fpa ON gks_transfer_reservation_oximata.product_fpa_id = gks_eshop_fpa.id_fpa)
  
  LEFT JOIN gks_aade_katigoria_parakratoumemenon_foron ON gks_transfer_reservation_oximata.product_withheldPercentCategory = gks_aade_katigoria_parakratoumemenon_foron.id_aade_katigoria_parakratoumemenon_foron) 
  LEFT JOIN gks_aade_katigoria_xartosimou ON gks_transfer_reservation_oximata.product_stampDutyPercentCategory = gks_aade_katigoria_xartosimou.id_aade_katigoria_xartosimou) 
  LEFT JOIN gks_aade_katigoria_telon ON gks_transfer_reservation_oximata.product_feesPercentCategory = gks_aade_katigoria_telon.id_aade_katigoria_telon) 
  LEFT JOIN gks_aade_katigoria_loipon_foron ON gks_transfer_reservation_oximata.product_otherTaxesPercentCategory = gks_aade_katigoria_loipon_foron.id_aade_katigoria_loipon_foron)
  
  LEFT JOIN gks_eshop_products ON gks_transfer_reservation_oximata.product_id = gks_eshop_products.id_product)
  LEFT JOIN gks_monades_metrisis ON gks_eshop_products.product_monada_id = gks_monades_metrisis.id_monada)
  LEFT JOIN gks_eshop_fpa_base ON gks_transfer_reservation_oximata.product_fpa_base_id = gks_eshop_fpa_base.id_fpa_base)
  LEFT JOIN gks_eshop_fiscal_position ON gks_transfer_reservation_oximata.ruser_fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position)
  LEFT JOIN gks_eshop_pricelist ON gks_transfer_reservation_oximata.ruser_pricelist_id = gks_eshop_pricelist.id_pricelist

  WHERE transfer_reservation_id=".$id."
  order by oximata_aa,id_transfer_reservation_oximata";
  
      
  
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
  if ($result->num_rows==0) {$ret['message']=gks_lang('Δεν βρέθηκαν δωμάτια για την κράτηση με ID').' <b>'.$id.'</b>';debug_mail(false,$ret['message'],$sql); return $ret;}
  
  $row_eidoi=array();
  $id_product_array_ids=array();
  while ($eidos = $result->fetch_assoc()) {
    $row_doc['oximata_plithos']++;
    if ($eidos['product_id']==2) $eidos['product_id']=0;
    
    $transfer_oxima_type_photo=trim_gks($eidos['transfer_oxima_type_photo']);
    $eidos['transfer_oxima_type_photo']='';
    if ($transfer_oxima_type_photo!='') {
      $full_transfer_oxima_type_photo=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.$transfer_oxima_type_photo;
      if (file_exists($full_transfer_oxima_type_photo)) {
        $eidos['transfer_oxima_type_photo']=GKS_SITE_URL.substr($transfer_oxima_type_photo, 1);
      }
    }
    $eidos['id_order_product'] = $eidos['id_transfer_reservation_oximata'];
    $eidos['rnum_epivates']=$eidos['rnum_adults']+$eidos['rnum_childs']+$eidos['rnum_babys'];
    
    //$eidos['room_descr']=gks_lang_pft($row_form['gks_lang'],'gks_hotel_room','room_descr',$eidos['hotel_room_id'],$eidos['room_descr']);
    //$eidos['room_type_descr']=gks_lang_pft($row_form['gks_lang'],'gks_hotel_room_type','room_type_descr',$eidos['hotel_room_type_id'],$eidos['room_type_descr']);
   
    
    $id_product_array_ids[]=$eidos['id_order_product'];
    $row_eidoi[]=$eidos;
  }
  //print '<pre>';print_r($row_eidoi);die();
  
  $products_lots_serials=array();

  
  //print '<pre>';print_r($row_eidoi);die();
  
  
  $row_fpa=array();
  foreach ($row_eidoi as $eidos) {
    if ($eidos['product_fpa_pososto']!=0) {
      if (isset($row_fpa[$eidos['product_fpa_pososto']])==false) {
        $row_fpa[$eidos['product_fpa_pososto']]=array('aa' => 1, 'pososto' => $eidos['product_fpa_pososto'], 'net'=> 0, 'fpa'=>0);
      }
      $row_fpa[$eidos['product_fpa_pososto']]['net']+=$eidos['product_price_final_all_net'];
      $row_fpa[$eidos['product_fpa_pososto']]['fpa']+=$eidos['product_price_final_all_fpa'];
    }
  } 
  //print '<pre>'; print_r($row_fpa); die();

  
  $row_foroi=array();
  
  $row_foroi[1]=array(
    'descr'=>gks_lang('Παρακρατούμενος Φόρος'),
    'net' => 0,
    'foros' => 0,
  );
  $row_foroi[2]=array(
    'descr'=>gks_lang('Λοιποί Φόροι'),
    'net' => 0,
    'foros' => 0,
  );
  $row_foroi[3]=array(
    'descr'=>gks_lang('Ψηφιακό Τέλος συναλλαγής'),
    'net' => 0,
    'foros' => 0,
  );
  $row_foroi[4]=array(
    'descr'=>gks_lang('Τέλη'),
    'net' => 0,
    'foros' => 0,
  );
  $row_foroi[5]=array(
    'descr'=>gks_lang('Κρατήσεις'),
    'net' => 0,
    'foros' => 0,
  );
  
  foreach ($row_eidoi as $eidos) {
    if ($eidos['product_withheldAmount']!=0) {
      $row_foroi[1]['net']+=$eidos['product_price_final_all_net'];
      $row_foroi[1]['foros']+=$eidos['product_withheldAmount'];
    }
    if ($eidos['product_otherTaxesAmount']!=0) {
      $row_foroi[2]['net']+=$eidos['product_price_final_all_net'];
      $row_foroi[2]['foros']+=$eidos['product_otherTaxesAmount'];
    }
    if ($eidos['product_stampDutyAmount']!=0) {
      $row_foroi[3]['net']+=$eidos['product_price_final_all_net'];
      $row_foroi[3]['foros']+=$eidos['product_stampDutyAmount'];
    }
    if ($eidos['product_feesAmount']!=0) {
      $row_foroi[4]['net']+=$eidos['product_price_final_all_net'];
      $row_foroi[4]['foros']+=$eidos['product_feesAmount'];
    }
//    if ($eidos['product_deductionsAmount']!=0) {
//      $row_foroi[5]['net']+=$eidos['product_price_final_all_net'];
//      $row_foroi[5]['foros']+=$eidos['product_deductionsAmount'];
//    }
  }  
  
  if ($row_foroi[1]['foros']==0) unset($row_foroi[1]);
  if ($row_foroi[2]['foros']==0) unset($row_foroi[2]);
  if ($row_foroi[3]['foros']==0) unset($row_foroi[3]);
  if ($row_foroi[4]['foros']==0) unset($row_foroi[4]);
  if ($row_foroi[5]['foros']==0) unset($row_foroi[5]);


  $gks_custom_prepare = gks_custom_table_item_prepare('gks_transfer_reservation',['from'=>'print']);
  if ($gks_custom_prepare['success']==true) {
    $custom_row['id_transfer_reservation']=$id;
    $gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$custom_row);
    if ($gks_custom_row['success']) {
      foreach ($gks_custom_row['fields'] as $key => $cf_item) {
        $row_doc['custom_'.$key]=array(
          'type'  => $cf_item['field_type_id'],
          'value' => $cf_item['print'],
        );
      } 
    }
    //echo '<pre>';print_r($row_doc);die();
    //print_r($gks_custom_row);
  }
    
  $gks_custom_prepare = gks_custom_table_item_prepare('wp_users',['from'=>'print']);
  if ($gks_custom_prepare['success']==true) {
    $custom_row['ID']=$row_person['id'];
    $gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$custom_row);
    if ($gks_custom_row['success']) {
      foreach ($gks_custom_row['fields'] as $key => $cf_item) {
        $row_person['custom_'.$key]=array(
          'type'  => $cf_item['field_type_id'],
          'value' => $cf_item['print'],
        );
      } 
    }
    //echo '<pre>';print_r($row_person);die();
    //print_r($gks_custom_row);
  }
    
  $company_ret=gks_print_form_company_data($company_id,$company_sub_id,$options,$row_form);
  if ($company_ret['success']==false) {$ret['message']=$company_ret['message'];return $ret;}
  
  return gks_print_form_make_print($row_form,$row,$row_doc,$row_canceled_doc,$row_credit_doc,$row_eidoi,$row_fpa,$row_foroi,$company_ret['data'],$row_person,$options,$products_lots_serials,[]);
}

function gks_print_form_gks_crm_tasks($id,$row_form,$options) {
  global $db_link;

  
  $ret=array('success' => false, 'message' => 'gks_crm_tasks generic error');
 
  $sql=select_gks_crm_tasks()." where id_crm_task = ".$id;;
  

  //print '<pre>'; print_r($sql);die();
  
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
  if ($result->num_rows!=1) {$ret['message']=gks_lang('Δεν βρέθηκε η κράτηση με ID').' <b>'.$id.'</b>';debug_mail(false,$ret['message'],$sql); return $ret;}
  $row = $result->fetch_assoc();
  
  //$row['acc_journal_descr']='';
  //$row['payment_acquirer_name']='';
  //$row['delivery_method_name']=gks_lang_pft($row_form['gks_lang'],'gks_delivery_methods','delivery_method_name',$row['tropos_apostolis'],$row['delivery_method_name']);
  $row['country_name']=gks_lang_pft($row_form['gks_lang'],'gks_country','country_name',$row['country_id'],$row['country_name']);
  $row['nomos_descr']=gks_lang_pft($row_form['gks_lang'],'gks_nomoi','nomos_descr',$row['nomos_id'],$row['nomos_descr']);

  $row['country_name_en_US']='';
  gks_lang_data_obj_insert_to_row($row,'gks_country',array(
    array('id' => $row['country_id'], 'filename'=>'country_name', 'new_field' => 'country_name_%s'),
  ));
  $row['nomos_descr_en_US']='';
  gks_lang_data_obj_insert_to_row($row,'gks_nomoi',array(
    array('id' => $row['nomos_id'], 'filename'=>'nomos_descr', 'new_field' => 'nomos_descr_%s'),
  ));
  //print '<pre>';print_r($row);die();
   
  $company_id=intval($row['company_id']);
  $company_sub_id=intval($row['company_sub_id']);

  $row_person=array();
  $row_person['id']=$row['user_id'];
  $row_person['nickname_add']=trim_gks($row['gks_nickname_add']);
  $row_person['nickname_edit']=trim_gks($row['gks_nickname_edit']);
  $row_person['nickname']=trim_gks($row['gks_nickname']);
  $row_person['email']=$row['email'];
  $row_person['url']=trim_gks($row['web']);
  $row_person['first_name']=$row['first_name'];
  $row_person['last_name']=$row['last_name'];
  $row_person['phone']=$row['phone'];
  $row_person['mobile']=$row['mobile'];
  $row_person['user_lang']=$row['user_lang'];
  $row_person['eponimia']=$row['eponimia'];
  $row_person['title']=$row['title'];
  $row_person['afm']=$row['afm'];
  $row_person['doy']=$row['doy'];
  $row_person['epaggelma']=$row['epaggelma'];
  $row_person['odos']=$row['odos'];
  $row_person['arithmos']=$row['arithmos'];
  $row_person['orofos']=$row['orofos'];
  $row_person['perioxi']=$row['perioxi'];
  $row_person['poli']=$row['poli'];
  $row_person['tk']=$row['tk'];
  $row_person['country_id']=$row['country_id'];
  $row_person['country_name']=$row['country_name'];
  $row_person['country_name_en']=$row['country_name_en_US'];
  $row_person['country_initials']=$row['country_initials'];
  $row_person['country_initials3']=$row['country_initials3'];
  $row_person['country_ee']=$row['country_ee'];
  $row_person['nomos_id']=$row['nomos_id'];
  $row_person['nomos_descr']=$row['nomos_descr'];
  $row_person['nomos_descr_en']=$row['nomos_descr_en_US'];
  $row_person['pricelist_id']=$row['pricelist_id'];
  $row_person['pricelist']=$row['pricelist_descr'];
  $row_person['fiscal_position_id']=$row['fiscal_position_id'];
  $row_person['fiscal_position']=$row['fiscal_position_descr'];
  $row_person['antisimvalomenos_label']=gks_lang('Πελάτης');//$row['antisimvalomenos_label'];
  $row_person['antisimvalomenos_label_en']='Customer';//$row['antisimvalomenos_label_en'];
  
  $row_temp=[];
  $row_temp['ma_odos']=$row_person['odos'];
  $row_temp['ma_arithmos']=$row_person['arithmos'];
  $row_temp['ma_orofos']=$row_person['orofos'];
  $row_temp['ma_perioxi']=$row_person['perioxi'];
  $row_temp['ma_poli']=$row_person['poli'];
  $row_temp['nomos_descr']=$row_person['nomos_descr'];
  $row_temp['ma_tk']=$row_person['tk'];
  $row_temp['country_name']=$row_person['country_name'];
  

  
  $row_person['address_text']=gks_print_address_text($row_temp);
//  echo $row_person['address_text'];die();
//  $row_person['dest_name']=$row['destination_data_name'];
//  $row_person['dest_phone']=$row['destination_data_phone'];
//  $row_person['dest_odos']=$row['destination_data_odos'];
//  $row_person['dest_perioxi']=$row['destination_data_perioxi'];
//  $row_person['dest_poli']=$row['destination_data_poli'];
//  $row_person['dest_tk']=$row['destination_data_tk'];
//  $row_person['dest_country_id']=$row['destination_data_country_id'];
//  $row_person['dest_country_name']=$row['country_name_dest'];
//  $row_person['dest_country_name_en']=$row['country_name_en_US_dest'];
//  $row_person['dest_nomos_id']=$row['destination_data_nomos_id'];
//  $row_person['dest_nomos_descr']=$row['nomos_descr_dest'];
//  $row_person['dest_nomos_descr_en']=$row['nomos_descr_en_US_dest'];

  //if ($row['address_extra'] < 1) {
  if (true) {
    $row_person['dest_name']='';
    $row_person['dest_phone']='';
    $row_person['dest_odos']='';
    $row_person['dest_arithmos']='';
    $row_person['dest_orofos']='';
    $row_person['dest_perioxi']='';
    $row_person['dest_poli']='';
    $row_person['dest_tk']='';
    $row_person['dest_country_id']=0;
    $row_person['dest_country_name']='';
    $row_person['dest_country_name_en']='';
    $row_person['dest_nomos_id']=0;
    $row_person['dest_nomos_descr']='';
    $row_person['dest_nomos_descr_en']='';    
  }

  $row_doc=array();
  $row_doc['title']=gks_lang('Εργασία'); //$row['acc_journal_descr'];
  $row_doc['title_pre']='';
  
  gks_get_tasks_status($tasks_status,$tasks_status_styles);
  $task_status='empty';
  $task_status_id=$row['task_status_id'];
  if (isset($tasks_status[$task_status_id])) $task_status= $tasks_status[$task_status_id]['task_status_descr'];


  if ($row_form['gks_lang']=='el-GR') {
    $row_doc['title_pre']=$task_status;//getTransferReservationStatusDescr($row['transfer_reservation_status']);
  } else {
    $row_doc['title_pre']=$task_status; //getTransferReservationStatusDescr_en_US($row['transfer_reservation_status']);
  }
  $row_doc['company']=$row['company_title'];
  if (trim_gks($row['company_sub_title'])!='') $row_doc['company'].=' \ '.$row['company_sub_title'];
  
  $row_doc['company_title']=$row['company_title'];
  $row_doc['company_sub_title']=trim_gks($row['company_sub_title']);
  if ($row_doc['company_sub_title']=='') $row_doc['company_sub_title']=gks_lang('Κεντρικό');
  

  $row_doc['date']=myDateTimeFormat(_time_user(strtotime($row['task_date']),1));
  $row_doc['date_d']=myDateFormat(_time_user(strtotime($row['task_date']),1));
  $row_doc['date_dw']=myDateFormatw(_time_user(strtotime($row['task_date']),1),$row_form['gks_lang']);
  $row_doc['date_dt']=myDateTimeFormat(_time_user(strtotime($row['task_date']),1));
  $row_doc['date_dtw']=myDateTimeFormatw(_time_user(strtotime($row['task_date']),1),$row_form['gks_lang']);
  $row_doc['date_dtt']=myDateTimeFormatText(_time_user(strtotime($row['task_date']),1),$row_form['gks_lang']);
  $row_doc['datefull']=showDate(strtotime($row['task_date']),'d/m/Y H:i:s',1);

  $row_doc['mydate_add']=showDate(strtotime($row['mydate_add']),'d/m/Y H:i',1);
  $row_doc['mydate_edit']=showDate(strtotime($row['mydate_edit']),'d/m/Y H:i',1);
  //$row_doc['seira']=trim_gks($row['seira_code']);
  //$row_doc['seira_descr']=trim_gks($row['seira_descr']);
  $row_doc['number']=trim_gks($row['id_crm_task']);
  $row_doc['number_str']=trim_gks($row['id_crm_task']);
  $row_doc['mark']='';
  $row_doc['aade_qrurl']='';
  $row_doc['aade_paroxos_qrurl']='';
  $row_doc['paroxos_tf1_url']='';
  $row_doc['aade_invoiceuid']='';
  $row_doc['paroxos_authenticationCode']='';
  //$row_doc['products_posotita']=trim_gks($row['products_posotita']);
  //$row_doc['gks_price_original_net']=trim_gks($row['gks_price_original_net']);
  //$row_doc['gks_price_net']=trim_gks($row['gks_price_net']);
  //$row_doc['gks_price_fpa']=trim_gks($row['gks_price_fpa']);
  //$row_doc['gks_price_netfpa']=trim_gks($row['gks_price_netfpa']);
  $row_doc['gks_price_total']=trim_gks($row['esoda']);
  //$row_doc['totalWithheldAmount']=trim_gks($row['totalWithheldAmount']);
  //$row_doc['totalOtherTaxesAmount']=trim_gks($row['totalOtherTaxesAmount']);
  //$row_doc['totalStampDutyamount']=trim_gks($row['totalStampDutyamount']);
  //$row_doc['totalFeesAmount']=trim_gks($row['totalFeesAmount']);
  //$row_doc['totalDeductionsAmount']=0; //trim_gks($row['totalDeductionsAmount']);

  //$row_doc['transfer_booking_number']=trim_gks($row['transfer_booking_number']);
  $row_doc['task_planned_date_from']=myDateTimeFormat(_time_user(strtotime($row['task_planned_date_from']),1));
  $row_doc['task_planned_date_from_d']=myDateFormat(_time_user(strtotime($row['task_planned_date_from']),1));
  $row_doc['task_planned_date_from_dw']=myDateFormatw(_time_user(strtotime($row['task_planned_date_from']),1),$row_form['gks_lang']);
  $row_doc['task_planned_date_from_dt']=myDateTimeFormat(_time_user(strtotime($row['task_planned_date_from']),1));
  $row_doc['task_planned_date_from_dtw']=myDateTimeFormatw(_time_user(strtotime($row['task_planned_date_from']),1),$row_form['gks_lang']);
  $row_doc['task_planned_date_from_dtt']=myDateTimeFormatText(_time_user(strtotime($row['task_planned_date_from']),1),$row_form['gks_lang']);

  $row_doc['task_planned_date_to']=myDateTimeFormat(_time_user(strtotime($row['task_planned_date_to']),1));
  $row_doc['task_planned_date_to_d']=myDateFormat(_time_user(strtotime($row['task_planned_date_to']),1));
  $row_doc['task_planned_date_to_dw']=myDateFormatw(_time_user(strtotime($row['task_planned_date_to']),1),$row_form['gks_lang']);
  $row_doc['task_planned_date_to_dt']=myDateTimeFormat(_time_user(strtotime($row['task_planned_date_to']),1));
  $row_doc['task_planned_date_to_dtw']=myDateTimeFormatw(_time_user(strtotime($row['task_planned_date_to']),1),$row_form['gks_lang']);
  $row_doc['task_planned_date_to_dtt']=myDateTimeFormatText(_time_user(strtotime($row['task_planned_date_to']),1),$row_form['gks_lang']);

  //$row_doc['oximata_plithos']=0; //intval($row['rooms_plithos']);
  $row_doc['duration_secs']=strtotime($row['task_planned_date_to'])-strtotime($row['task_planned_date_from']);
  
  $temp1=date('j:G:i',$row_doc['duration_secs']);
  $temp1=explode(':',$temp1);
  $temp1[0]=intval($temp1[0]); //day
  $temp1[1]=intval($temp1[1]); //hour
  $temp1[2]=intval($temp1[2]); //minute
  if ($temp1[0]>1) $temp1[1]+=24*($temp1[0]-1);
  $temp2=($temp1[1]>=10 ? $temp1[1] : '0'.$temp1[1]).':'.($temp1[2]>=10 ? $temp1[2] : '0'.$temp1[2]);

  $row_doc['duration_minutes_secs']=$temp2;
  
      
  
  
  $row_person['person_balance_before']=gks_balance_calc(['id' => $row['user_id']]);
  $row_person['person_balance_after']=$row_person['person_balance_before'] + $row['esoda'];
  

  $row_doc['enarji_apostolis']=     ''; //(isset($row['dispatch_date']) ? showDate(strtotime($row['dispatch_date']), 'd/m/Y H:i', 1) : '');
  $row_doc['enarji_apostolis_date']=''; //(isset($row['dispatch_date']) ? showDate(strtotime($row['dispatch_date']), 'd/m/Y', 1) : '');
  $row_doc['enarji_apostolis_time']=''; //(isset($row['dispatch_date']) ? showDate(strtotime($row['dispatch_date']), 'H:i', 1) : '');
  $row_doc['arithmos_oximatos']=''; //trim_gks($row['vehicle_number']);
  $row_doc['skopos_diakinisis']=''; //trim_gks($row['aade_skopos_diakinisis_descr']);
  //$row_doc['tropos_pliromis']=trim_gks($row['payment_acquirer_name']);
  //$row_doc['tropos_apostolis']=trim_gks($row['delivery_method_name']);
  
  //$row_doc['arithmos_aposolis']=trim_gks($row['delivery_number']);
  $row_doc['subject']=trim_gks($row['subject']);
  $row_doc['message']=trim_gks($row['message']);
  $row_doc['internal_note']=trim_gks($row['internal_note']);
  //$row_doc['note_production']='';
  //$row_doc['note_logistirio']=trim_gks($row['note_logistirio']);
  $row_doc['ddate']='';
  $row_doc['occasion_title']='';
  $row_doc['occasion_type_descr']='';
  $row_doc['occasion_mydate_add']= '';


  //$row_doc['idiotites_text']=gks_print_idiotites_text($row);
  $row_doc['photos']='';
  $row_doc['links']='';
  

  $sql_machine="SELECT ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
  gks_crm_tasks_machine.*, 
  gks_crm_machine.id_crm_machine,
  gks_crm_machine.crm_machine_name, gks_crm_machine.crm_machine_serial_number
  FROM (gks_crm_tasks_machine 
  LEFT JOIN gks_crm_machine ON gks_crm_tasks_machine.crm_task_machine_id = gks_crm_machine.id_crm_machine) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_crm_tasks_machine.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  WHERE gks_crm_tasks_machine.crm_task_id=".$id."
  ORDER BY gks_crm_tasks_machine.id_crm_task_machine;";
  
  $result_machine = $db_link->query($sql_machine);        
  if (!$result_machine) {debug_mail(false,'error sql',$sql_machine);$ret['message']='sql error'; return $ret;}
  $machine=[];
  while ($row_machine = $result_machine->fetch_assoc()) {
    $item=$row_machine['crm_machine_name'];
    if (!empty($row_machine['crm_machine_serial_number'])) {
      $item.=' - '.$row_machine['crm_machine_serial_number'];
    }
    $machine[]=$item;
  }
  $row_doc['machine']=implode('<br>',$machine);
  
  
  $sql_employee="SELECT ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
  gks_crm_tasks_employee.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
  ".GKS_WP_TABLE_PREFIX."users.gks_wsl_current_user_image
  FROM (gks_crm_tasks_employee 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_tasks_employee.crm_task_employee_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_crm_tasks_employee.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  WHERE gks_crm_tasks_employee.crm_task_id=".$id."
  ORDER BY gks_crm_tasks_employee.id_crm_task_employee;";

  $result_employee = $db_link->query($sql_employee);        
  if (!$result_employee) {debug_mail(false,'error sql',$sql_employee);$ret['message']='sql error'; return $ret;}
  $employee=[];
  while ($row_employee = $result_employee->fetch_assoc()) {
    $item=$row_employee['gks_nickname'];
    $employee[]=$item;
  }
  $row_doc['employee']=implode('<br>',$employee);  
  
  
  
  
  
  $row_canceled_doc=array();
  
  $row_canceled_doc['display']='none';
  $row_canceled_doc['title']='';
  $row_canceled_doc['title_pre']='';
  $row_canceled_doc['company']='';
  $row_canceled_doc['date']='';
  $row_canceled_doc['datefull']='';
  $row_canceled_doc['seira']='';
  $row_canceled_doc['seira_descr']='';
  $row_canceled_doc['number']=0;
  $row_canceled_doc['number_str']='';
  $row_canceled_doc['mark']='';
  $row_canceled_doc['aade_qrurl']='';
  $row_canceled_doc['aade_paroxos_qrurl']='';
  $row_canceled_doc['paroxos_tf1_url']='';
  $row_canceled_doc['aade_invoiceuid']='';
  $row_canceled_doc['paroxos_authenticationCode']='';

  $row_credit_doc=array();

  $row_credit_doc['display']='none';
  $row_credit_doc['title']='';
  $row_credit_doc['title_pre']='';
  $row_credit_doc['company']='';
  $row_credit_doc['date']='';
  $row_credit_doc['datefull']='';
  $row_credit_doc['seira']='';
  $row_credit_doc['seira_descr']='';
  $row_credit_doc['number']=0;
  $row_credit_doc['number_str']='';
  $row_credit_doc['mark']='';
  $row_credit_doc['aade_qrurl']='';
  $row_credit_doc['aade_paroxos_qrurl']='';
  $row_credit_doc['paroxos_tf1_url']='';
  $row_credit_doc['aade_invoiceuid']='';
  $row_credit_doc['paroxos_authenticationCode']='';
  
  
  
  
  
  
  $row_eidoi=array();
  $id_product_array_ids=array();
 
  $products_lots_serials=array();

  
  //print '<pre>';print_r($row_eidoi);die();
  
  
  $row_fpa=array();
  

  $row_foroi=array();
  
    
  $gks_custom_prepare = gks_custom_table_item_prepare('gks_crm_tasks',['from'=>'print']);
  if ($gks_custom_prepare['success']==true) {
    $custom_row['id_crm_task']=$id;
    $gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$custom_row);
    if ($gks_custom_row['success']) {
      foreach ($gks_custom_row['fields'] as $key => $cf_item) {
        $row_doc['custom_'.$key]=array(
          'type'  => $cf_item['field_type_id'],
          'value' => $cf_item['print'],
        );
      } 
    }
    //echo '<pre>';print_r($row_doc);die();
    //print_r($gks_custom_row);
  }

  $gks_custom_prepare = gks_custom_table_item_prepare('wp_users',['from'=>'print']);
  if ($gks_custom_prepare['success']==true) {
    $custom_row['ID']=$row_person['id'];
    $gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$custom_row);
    if ($gks_custom_row['success']) {
      foreach ($gks_custom_row['fields'] as $key => $cf_item) {
        $row_person['custom_'.$key]=array(
          'type'  => $cf_item['field_type_id'],
          'value' => $cf_item['print'],
        );
      } 
    }
    //echo '<pre>';print_r($row_person);die();
    //print_r($gks_custom_row);
  }  
  
  $company_ret=gks_print_form_company_data($company_id,$company_sub_id,$options,$row_form);
  if ($company_ret['success']==false) {$ret['message']=$company_ret['message'];return $ret;}
  
  return gks_print_form_make_print($row_form,$row,$row_doc,$row_canceled_doc,$row_credit_doc,$row_eidoi,$row_fpa,$row_foroi,$company_ret['data'],$row_person,$options,$products_lots_serials,[]);
}

function gks_print_form_company_data($id_company,$id_company_sub,$options,$row_form) {
  global $db_link;

  $ret=array('success' => false, 'message' => 'gks_print_form_company_data generic error', $data=array());

  //$ret=array('success' => false, 'message' => 'error|'.$id_company.'|'.$id_company_sub, $data=array());return $ret;

  if ($id_company_sub==0) { //kentriko
    $sql="SELECT 
    id_company as id,
    '' as main_title,
    company_title as title,
    '' as main_tagline,
    company_tagline as tagline,
    company_eponimia as eponimia,
    '' as main_eponimia,
    company_afm as afm,
    company_doy as doy,
    company_epaggelma as epaggelma,
    company_phone as phone,
    company_email as email,
    company_url as url,
    company_odos as odos,
    company_arithmos as arithmos,
    company_orofos as orofos,
    company_perioxi as perioxi,
    company_poli as poli,
    company_tk as tk,
    company_nomos_id as nomos_id,nomos_descr,
    company_country_id as country_id,country_name,country_initials, country_initials3, country_ee,
    company_map_latitude as map_latitude,
    company_map_longitude as map_longitude,
    company_disable as disable,
    company_color as color,
    default_eshop_company as default_eshop
    FROM (gks_company 
    LEFT JOIN gks_nomoi ON gks_company.company_nomos_id = gks_nomoi.id_nomos) 
    LEFT JOIN gks_country ON gks_company.company_country_id = gks_country.id_country
    
    where id_company=".$id_company;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
    if ($result->num_rows!=1) {$ret['message']=gks_lang('Δεν βρέθηκε η εταιρεία με ID').' <b>'.$id_company.'</b>';debug_mail(false,$ret['message'],$sql); return $ret;}
    $ret['data'] = $result->fetch_assoc();
    
    $ret['data']['title']=gks_lang_pft($row_form['gks_lang'],'gks_company','company_title',$id_company,$ret['data']['title']);
    $ret['data']['tagline']=gks_lang_pft($row_form['gks_lang'],'gks_company','company_tagline',$id_company,$ret['data']['tagline']);
    $ret['data']['eponimia']=gks_lang_pft($row_form['gks_lang'],'gks_company','company_eponimia',$id_company,$ret['data']['eponimia']);
    $ret['data']['doy']=gks_lang_pft($row_form['gks_lang'],'gks_company','company_doy',$id_company,$ret['data']['doy']);
    $ret['data']['epaggelma']=gks_lang_pft($row_form['gks_lang'],'gks_company','company_epaggelma',$id_company,$ret['data']['epaggelma']);
    $ret['data']['phone']=gks_lang_pft($row_form['gks_lang'],'gks_company','company_phone',$id_company,$ret['data']['phone']);
    $ret['data']['odos']=gks_lang_pft($row_form['gks_lang'],'gks_company','company_odos',$id_company,$ret['data']['odos']);
    $ret['data']['arithmos']=gks_lang_pft($row_form['gks_lang'],'gks_company','company_arithmos',$id_company,$ret['data']['arithmos']);
    $ret['data']['orofos']=gks_lang_pft($row_form['gks_lang'],'gks_company','company_orofos',$id_company,$ret['data']['orofos']);
    $ret['data']['perioxi']=gks_lang_pft($row_form['gks_lang'],'gks_company','company_perioxi',$id_company,$ret['data']['perioxi']);
    $ret['data']['poli']=gks_lang_pft($row_form['gks_lang'],'gks_company','company_poli',$id_company,$ret['data']['poli']);
    
    $ret['data']['country_name']=gks_lang_pft($row_form['gks_lang'],'gks_country','country_name',$ret['data']['country_id'],$ret['data']['country_name']);
    $ret['data']['nomos_descr']=gks_lang_pft($row_form['gks_lang'],'gks_nomoi','nomos_descr',$ret['data']['nomos_id'],$ret['data']['nomos_descr']);
    
    
    
      
    $row['country_name_en_US']='';
    gks_lang_data_obj_insert_to_row($ret['data'],'gks_country',array(
      array('id' => $ret['data']['country_id'],               'filename'=>'country_name', 'new_field' => 'country_name_%s'),
    ));
    $row['nomos_descr_en_US']='';
    gks_lang_data_obj_insert_to_row($ret['data'],'gks_nomoi',array(
      array('id' => $ret['data']['nomos_id'],               'filename'=>'nomos_descr', 'new_field' => 'nomos_descr_%s'),
    ));
    //print '<pre>'; print_r($ret['data']); die();
    
  } else {
    $sql="SELECT 
    id_company_sub as id,
    gks_company_subs.company_id,
    company_title as main_title,
    company_sub_title as title,
    company_tagline as main_tagline,
    company_sub_tagline as tagline,
    company_eponimia as main_eponimia,
    company_sub_eponimia as eponimia,
    company_afm as afm,
    company_doy as doy,
    company_epaggelma as epaggelma,
    company_sub_phone as phone,
    company_sub_email as email,
    company_sub_url as url,
    company_sub_odos as odos,
    company_sub_arithmos as arithmos,
    company_sub_orofos as orofos,
    company_sub_perioxi as perioxi,
    company_sub_poli as poli,
    company_sub_tk as tk,
    company_sub_nomos_id as nomos_id,nomos_descr,
    company_sub_country_id as country_id,country_name,country_initials, country_initials3, country_ee,
    company_sub_map_latitude as map_latitude,
    company_sub_map_longitude as map_longitude,
    company_sub_disable as disable,
    company_sub_color as color,
    gks_company_subs.default_eshop_company as default_eshop 
    FROM ((gks_company_subs 
    LEFT JOIN gks_company ON gks_company_subs.company_id = gks_company.id_company) 
    LEFT JOIN gks_nomoi ON gks_company_subs.company_sub_nomos_id = gks_nomoi.id_nomos) 
    LEFT JOIN gks_country ON gks_company_subs.company_sub_country_id = gks_country.id_country
    
    where id_company_sub=".$id_company_sub;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
    if ($result->num_rows!=1) {$ret['message']=gks_lang('Δεν βρέθηκε η εταιρεία με ID').' <b>'.$id_company_sub.'</b>';debug_mail(false,$ret['message'],$sql); return $ret;}
    $ret['data'] = $result->fetch_assoc();
    
    $id_company=$ret['data']['company_id'];
    
    $ret['data']['main_title']=gks_lang_pft($row_form['gks_lang'],'gks_company','company_title',$id_company,$ret['data']['main_title']);
    $ret['data']['main_tagline']=gks_lang_pft($row_form['gks_lang'],'gks_company','company_tagline',$id_company,$ret['data']['main_tagline']);
    $ret['data']['main_eponimia']=gks_lang_pft($row_form['gks_lang'],'gks_company','company_eponimia',$id_company,$ret['data']['main_eponimia']);
    $ret['data']['doy']=gks_lang_pft($row_form['gks_lang'],'gks_company','company_doy',$id_company,$ret['data']['doy']);
    $ret['data']['epaggelma']=gks_lang_pft($row_form['gks_lang'],'gks_company','company_epaggelma',$id_company,$ret['data']['epaggelma']);

    
    $ret['data']['title']=gks_lang_pft($row_form['gks_lang'],'gks_company_subs','company_sub_title',$id_company_sub,$ret['data']['title']);
    $ret['data']['tagline']=gks_lang_pft($row_form['gks_lang'],'gks_company_subs','company_sub_tagline',$id_company_sub,$ret['data']['tagline']);
    $ret['data']['eponimia']=gks_lang_pft($row_form['gks_lang'],'gks_company_subs','company_sub_eponimia',$id_company_sub,$ret['data']['eponimia']);
    $ret['data']['phone']=gks_lang_pft($row_form['gks_lang'],'gks_company_subs','company_sub_phone',$id_company_sub,$ret['data']['phone']);
    $ret['data']['odos']=gks_lang_pft($row_form['gks_lang'],'gks_company_subs','company_sub_odos',$id_company_sub,$ret['data']['odos']);
    $ret['data']['arithmos']=gks_lang_pft($row_form['gks_lang'],'gks_company_subs','company_sub_arithmos',$id_company_sub,$ret['data']['arithmos']);
    $ret['data']['orofos']=gks_lang_pft($row_form['gks_lang'],'gks_company_subs','company_sub_orofos',$id_company_sub,$ret['data']['orofos']);
    $ret['data']['perioxi']=gks_lang_pft($row_form['gks_lang'],'gks_company_subs','company_sub_perioxi',$id_company_sub,$ret['data']['perioxi']);
    $ret['data']['poli']=gks_lang_pft($row_form['gks_lang'],'gks_company_subs','company_sub_poli',$id_company_sub,$ret['data']['poli']);
    
    $ret['data']['country_name']=gks_lang_pft($row_form['gks_lang'],'gks_country','country_name',$ret['data']['country_id'],$ret['data']['country_name']);
    $ret['data']['nomos_descr']=gks_lang_pft($row_form['gks_lang'],'gks_nomoi','nomos_descr',$ret['data']['nomos_id'],$ret['data']['nomos_descr']);
    
    
    
    $row['country_name_en_US']='';
    gks_lang_data_obj_insert_to_row($ret['data'],'gks_country',array(
      array('id' => $ret['data']['country_id'], 'filename'=>'country_name', 'new_field' => 'country_name_%s'),
    ));
    $row['nomos_descr_en_US']='';
    gks_lang_data_obj_insert_to_row($ret['data'],'gks_nomoi',array(
      array('id' => $ret['data']['nomos_id'],               'filename'=>'nomos_descr', 'new_field' => 'nomos_descr_%s'),
    ));
    //print '<pre>'; print_r($ret['data']); die();    
  }
   
  $ret['success']=true;
  return $ret;
}

function gks_mymatches_sort($a, $b) {
  if ($a['nl'] != $b['nl']) {
    if ($a['nl'] < $b['nl']) return 1;
    return -1;
  }
  $c = new Collator('el_GR');
  return $c->compare($a['val'], $b['val']);
}

function gks_print_form_mc($html_in) {
  //$mc=array();

  $mymatches=array();
  $pattern='~\{((?:[^\{\}]++|(?R))*)\}~';
  //print '<pre>';var_dump($html_in);die();
  
  preg_match_all( $pattern, $html_in, $matches );
  //print '<pre>';print_r($matches);die();
  
  
  if (is_array($matches) and isset($matches[0])) {
    foreach ($matches[0] as $value) {
      $mymatches[]=array('nl'=> 1,'val'=>$value);
    }
    //print '<pre>';print_r($mymatches);print_r($matches);die();
    if (isset($matches[1])) {
      foreach ($matches[1] as $value) {
        $pattern='~\{((?:[^\{\}]++|(?R))*)\}~';
        preg_match_all( $pattern, $value, $matches );
        if (is_array($matches) and isset($matches[0])) {
          foreach ($matches[0] as $value) {
            $mymatches[]=array('nl'=> 2,'val'=>$value);
          }
  
          if (isset($matches[1])) {
            foreach ($matches[1] as $value) {
              $pattern='~\{((?:[^\{\}]++|(?R))*)\}~';
              preg_match_all( $pattern, $value, $matches );
              if (is_array($matches) and isset($matches[0])) {
                foreach ($matches[0] as $value) {
                  $mymatches[]=array('nl'=> 3,'val'=>$value);
                }
                if (isset($matches[1])) {
                  foreach ($matches[1] as $value) {
                    $pattern='~\{((?:[^\{\}]++|(?R))*)\}~';
                    preg_match_all( $pattern, $value, $matches );
                    if (is_array($matches) and isset($matches[0])) {
                      foreach ($matches[0] as $value) {
                        $mymatches[]=array('nl'=> 4,'val'=>$value);
                      }
                      
                      
                    }
                  }    
                }               
                
              }
            }    
          }        
        }
      }    
    }
  }  
  
  usort($mymatches, "gks_mymatches_sort");
  
  //print '<pre>1111';print_r($mymatches);die();
  
  //preg_match_all("/\{[^\}]*\}/", $html_in, $matches);
  //if (is_array($matches) and isset($matches[0])) {
  $mc=array();
  foreach ($mymatches as $value) {
    if (startwith($value['val'],'{') and endwith($value['val'],'}')) {
      if (strlen($value['val'])>=3) {
        $name=substr($value['val'],1, strlen($value['val'])-2);
        $dest='%%';
        $hide=array('empty');
        $format='';
        $parts=explode(' ', $name,2);
        if (count($parts)==2) {
          $name=$parts[0];
          //preg_match_all("/\[[^\]]*\]/", $parts[1], $params);
          preg_match_all("~\[((?:[^\[\]]++|(?R))*)\]~", $parts[1], $params);
          //print '<pre>';print_r();
          if (is_array($params) and isset($params[0]) and is_array($params[0]) and count($params[0])>=1) {
            //if (startwith($params[0][0],'[') and endwith($params[0][0],']') and strlen($params[0][0])>=3) {
             
            //}
            for ($j=0; $j < count($params[0]); $j++) {
              if (startwith($params[0][$j],'[hide:')) {
                $hide=explode('|',substr($params[0][$j], 6, strlen($params[0][$j])-7));
              } else if (startwith($params[0][$j],'[format:')) {
                $format=substr($params[0][$j], 8, strlen($params[0][$j])-9);
              } else if (startwith($params[0][$j],'[') and endwith($params[0][$j],']') and strlen($params[0][$j])>=3) {
                $dest=substr($params[0][$j], 1, strlen($params[0][$j])-2) ;
              }
            }
          }
          //echo '<pre>';print_r($parts);print_r($params);print_r($hide);die();
        }
        if (isset($mc[$name])==false) {
          $mc[$name]=array();
        }
        $mc[$name][]=array('src' => $value['val'], 'dest' => $dest, 'hide' => $hide, 'format' => $format, 'nl' => $value['nl']);
        //echo '<pre>';print_r($mc[$name]);die();
      }
    }
  } 
    
  //echo '<pre>';print_r($mc);die();
  //}
  return $mc;  
}

function gks_print_form_replace_person($mc,$row_person, $html_in,$options,$row_form) {
  //s -> string
  //h -> html
  //n -> number int
  //nl -> number myNumberFormatNo0Local
  //c -> Currency
    //cs -> Currency + symbol
  
  $in_array=array();
  if (isset($row_person['id'])) {
    $in_array['person_id']=array('value' => $row_person['id'],'type' => 'n');
    
    $in_array['person_nickname_add']=array('value' => trim_gks($row_person['nickname_add']),'type' => 's');
    $in_array['person_nickname_edit']=array('value' => trim_gks($row_person['nickname_edit']),'type' => 's');
    $in_array['person_nickname']=array('value' => trim_gks($row_person['nickname']),'type' => 's');
    
    $in_array['person_email']=array('value' => trim_gks($row_person['email']),'type' => 's');
    $in_array['person_url']=array('value' => (trim_gks($row_person['url'])=='' ? '' : (startwith(trim_gks($row_person['url']),'http') ? trim_gks($row_person['url']) : 'http://'.trim_gks($row_person['url']))),'type' => 's');
    $in_array['person_first_name']=array('value' => trim_gks($row_person['first_name']),'type' => 's');
    $in_array['person_last_name']=array('value' => trim_gks($row_person['last_name']),'type' => 's');
    $in_array['person_phone']=array('value' => trim_gks($row_person['phone']),'type' => 's');
    $in_array['person_mobile']=array('value' => trim_gks($row_person['mobile']),'type' => 's');
    $in_array['person_user_lang']=array('value' => trim_gks($row_person['user_lang']),'type' => 's');
    $in_array['person_eponimia']=array('value' => trim_gks($row_person['eponimia']),'type' => 's');
    $in_array['person_title']=array('value' => trim_gks($row_person['title']),'type' => 's');
    $in_array['person_afm']=array('value' => trim_gks($row_person['afm']),'type' => 's');
    $in_array['person_doy']=array('value' => trim_gks($row_person['doy']),'type' => 's');
    $in_array['person_epaggelma']=array('value' => trim_gks($row_person['epaggelma']),'type' => 's');
    $in_array['person_odos']=array('value' => trim_gks($row_person['odos']),'type' => 's');
    $in_array['person_arithmos']=array('value' => trim_gks($row_person['arithmos']),'type' => 's');
    $in_array['person_orofos']=array('value' => trim_gks($row_person['orofos']),'type' => 's');
    $in_array['person_perioxi']=array('value' => trim_gks($row_person['perioxi']),'type' => 's');
    $in_array['person_poli']=array('value' => trim_gks($row_person['poli']),'type' => 's');
    $in_array['person_tk']=array('value' => trim_gks($row_person['tk']),'type' => 's');
    $in_array['person_country_id']=array('value' => ($row_person['country_id']),'type' => 'n');
    $in_array['person_country_name']=array('value' => trim_gks($row_person['country_name']),'type' => 's');
    $in_array['person_country_name_en']=array('value' => trim_gks($row_person['country_name_en']),'type' => 's');
    $in_array['person_country_initials']=array('value' => trim_gks($row_person['country_initials']),'type' => 's');
    $in_array['person_country_initials3']=array('value' => trim_gks($row_person['country_initials3']),'type' => 's');
    $in_array['person_country_ee']=array('value' => trim_gks($row_person['country_ee']),'type' => 's');
    $in_array['person_nomos_id']=array('value' => ($row_person['nomos_id']),'type' => 'n');
    $in_array['person_nomos_descr']=array('value' => trim_gks($row_person['nomos_descr']),'type' => 's');
    $in_array['person_nomos_descr_en']=array('value' => trim_gks($row_person['nomos_descr_en']),'type' => 's');
    $in_array['person_pricelist_id']=array('value' => gks_print_isset_n($row_person['pricelist_id']),'type' => 'n');
    $in_array['person_pricelist']=array('value' => gks_print_isset_s($row_person['pricelist']),'type' => 's');
    $in_array['person_fiscal_position_id']=array('value' => gks_print_isset_n($row_person['fiscal_position_id']),'type' => 'n');
    $in_array['person_fiscal_position']=array('value' => gks_print_isset_s($row_person['fiscal_position']),'type' => 's');
  
    //
    if ($row_form['gks_lang']=='el-GR') {
      $in_array['person_label']=array('value' => trim_gks($row_person['antisimvalomenos_label']),'type' => 's');
    } else {
      $in_array['person_label']=array('value' => trim_gks($row_person['antisimvalomenos_label_en']),'type' => 's');
    }
    $in_array['person_address_text']=array('value' => gks_print_isset_h($row_person['address_text']),'type' => 'h');
    
    
    
  
    $in_array['dest_name']=array('value' => gks_print_isset_s($row_person['dest_name']),'type' => 's');
    $in_array['dest_phone']=array('value' => gks_print_isset_s($row_person['dest_phone']),'type' => 's');
    $in_array['dest_odos']=array('value' => gks_print_isset_s($row_person['dest_odos']),'type' => 's');
    $in_array['dest_arithmos']=array('value' => gks_print_isset_s($row_person['dest_arithmos']),'type' => 's');
    $in_array['dest_orofos']=array('value' => gks_print_isset_s($row_person['dest_orofos']),'type' => 's');
    $in_array['dest_perioxi']=array('value' => gks_print_isset_s($row_person['dest_perioxi']),'type' => 's');
    $in_array['dest_poli']=array('value' => gks_print_isset_s($row_person['dest_poli']),'type' => 's');
    $in_array['dest_tk']=array('value' => gks_print_isset_s($row_person['dest_tk']),'type' => 's');
    $in_array['dest_country_id']=array('value' => gks_print_isset_n($row_person['dest_country_id']),'type' => 'n');
    $in_array['dest_country_name']=array('value' => gks_print_isset_s($row_person['dest_country_name']),'type' => 's');
    $in_array['dest_country_name_en']=array('value' => gks_print_isset_s($row_person['dest_country_name_en']),'type' => 's');
    $in_array['dest_nomos_id']=array('value' => gks_print_isset_n($row_person['dest_nomos_id']),'type' => 'n');
    $in_array['dest_nomos_descr']=array('value' => gks_print_isset_s($row_person['dest_nomos_descr']),'type' => 's');
    $in_array['dest_nomos_descr_en']=array('value' => gks_print_isset_s($row_person['dest_nomos_descr_en']),'type' => 's');
  
    
    $in_array['person_balance_before']=array('value' => gks_print_isset_n($row_person['person_balance_before']),'type' => 'c');
    $in_array['person_balance_after']=array('value' => gks_print_isset_n($row_person['person_balance_after']),'type' => 'c');
  
    foreach ($row_person as $key => $value) {
      if (substr($key,0,7)=='custom_') {
        if (in_array($value['type'],[9])) {
          $in_array[$key]=array('value' => gks_print_isset_h($value['value']),'type' => 'h');
        } else {
          $in_array[$key]=array('value' => gks_print_isset_s($value['value']),'type' => 's');
        }
      }
    }  
  
  }
    
  $tr_m= array('html' => $html_in, 'mc' => $mc, 'tr_hide'=> false);
  $html_out=gks_print_form_replace_field($tr_m,$in_array);
  
  return $html_out;
}

function gks_print_form_replace_company($mc,$row_company, $html_in,$options,$row_form) {
  //s -> string
  //n -> number int
  //nl -> number myNumberFormatNo0Local
  //c -> Currency
    //cs -> Currency + symbol  
  
  $in_array=array();
  
  if (isset($row_company['id'])) {
  
    $in_array['company_id']=array('value' => $row_company['id'],'type' => 'n');
    $in_array['company_main_title']=array('value' => trim_gks($row_company['main_title']),'type' => 's');
    $in_array['company_title']=array('value' => trim_gks($row_company['title']),'type' => 's');
    $in_array['company_main_tagline']=array('value' => trim_gks($row_company['main_tagline']),'type' => 's');
    $in_array['company_tagline']=array('value' => trim_gks($row_company['tagline']),'type' => 's');
    $in_array['company_main_eponimia']=array('value' => trim_gks($row_company['main_eponimia']),'type' => 's');
    $in_array['company_eponimia']=array('value' => trim_gks($row_company['eponimia']),'type' => 's');
    $in_array['company_afm']=array('value' => trim_gks($row_company['afm']),'type' => 's');
    $in_array['company_doy']=array('value' => trim_gks($row_company['doy']),'type' => 's');
    $in_array['company_epaggelma']=array('value' => trim_gks($row_company['epaggelma']),'type' => 's');
    $in_array['company_phone']=array('value' => trim_gks($row_company['phone']),'type' => 's');
    $in_array['company_email']=array('value' => trim_gks($row_company['email']),'type' => 's');
    $in_array['company_url']=array('value' =>(trim_gks($row_company['url'])=='' ? '' : (startwith(trim_gks($row_company['url']),'http') ? trim_gks($row_company['url']) : 'http://'.trim_gks($row_company['url']))),'type' => 's');
    $in_array['company_odos']=array('value' => trim_gks($row_company['odos']),'type' => 's');
    $in_array['company_arithmos']=array('value' => trim_gks($row_company['arithmos']),'type' => 's');
    $in_array['company_orofos']=array('value' => trim_gks($row_company['orofos']),'type' => 's');
    $in_array['company_perioxi']=array('value' => trim_gks($row_company['perioxi']),'type' => 's');
    $in_array['company_poli']=array('value' => trim_gks($row_company['poli']),'type' => 's');
    $in_array['company_tk']=array('value' => trim_gks($row_company['tk']),'type' => 's');
    $in_array['company_nomos_id']=array('value' => ($row_company['nomos_id']),'type' => 'n');
    $in_array['company_nomos_descr']=array('value' => trim_gks($row_company['nomos_descr']),'type' => 's');
    $in_array['company_nomos_descr_en']=$in_array['company_nomos_descr']; //array('value' => trim_gks($row_company['nomos_descr_en_US']),'type' => 's');
    $in_array['company_country_id']=array('value' => ($row_company['country_id']),'type' => 'n');
    $in_array['company_country_name']=array('value' => trim_gks($row_company['country_name']),'type' => 's');
    $in_array['company_country_name_en']=$in_array['company_country_name']; //array('value' => trim_gks($row_company['country_name_en_US']),'type' => 's');
    $in_array['company_country_initials']=array('value' => trim_gks($row_company['country_initials']),'type' => 's');
    $in_array['company_country_initials3']=array('value' => trim_gks($row_company['country_initials3']),'type' => 's');
    $in_array['company_country_ee']=array('value' => trim_gks($row_company['country_ee']),'type' => 's');
    $in_array['company_map_latitude']=array('value' => ($row_company['map_latitude']),'type' => 'nl');
    $in_array['company_map_longitude']=array('value' => ($row_company['map_longitude']),'type' => 'nl');
    $in_array['company_disable']=array('value' => ($row_company['disable']),'type' => 'n');
    $in_array['company_color']=array('value' => trim_gks($row_company['color']),'type' => 's');
  }

  $tr_m= array('html' => $html_in, 'mc' => $mc, 'tr_hide'=> false);
  $html_out=gks_print_form_replace_field($tr_m,$in_array);
  
  return $html_out;
}

function gks_print_form_replace_generic($mc,$row_form, $html_in,$options) {
  global $my_wp_user_info;
  global $GKS_SITE_HUMAN_NAME;
  global $GKS_OFFICIAL_SITE_URL;
  global $GKS_SITE_NAME;
  global $GKS_SITE_EMAIL;
  
  //s -> string
  //n -> number int
  //nl -> number myNumberFormatNo0Local
  //c -> Currency
    //cs -> Currency + symbol  
    
  $in_array=array();
  $in_array['logo_url']=array('value' => (trim_gks($row_form['logo_url'])=='' ? '' :(startwith(trim_gks($row_form['logo_url']),'http') ? trim_gks($row_form['logo_url']) : 'http://'.trim_gks($row_form['logo_url']))),'type' => 's');
  $in_array['page']=array('value' => trim_gks('<span class="page"></span>'),'type' => 's');
  $in_array['pages']=array('value' => trim_gks('<span class="topage"></span>'),'type' => 's');
  $in_array['site_hname']=array('value' => trim_gks($GKS_SITE_HUMAN_NAME),'type' => 's');
  $in_array['site_official_url']=array('value' => trim_gks($GKS_OFFICIAL_SITE_URL),'type' => 's');
  $in_array['site_name']=array('value' => trim_gks($GKS_SITE_NAME),'type' => 's');
  $in_array['site_url']=array('value' => (trim_gks(GKS_SITE_URL)=='' ? '' : (startwith(trim_gks(GKS_SITE_URL),'http') ? trim_gks(GKS_SITE_URL) : 'http://'.trim_gks($GKS_SITE_URL))),'type' => 's');
  $in_array['site_email']=array('value' => trim_gks($GKS_SITE_EMAIL),'type' => 's');
  $in_array['time']=array('value' => trim_gks(showDate(time(), 'H:i',1)),'type' => 's');
  $in_array['date']=array('value' => trim_gks(showDate(time(), 'd/m/Y',1)),'type' => 's');
  $in_array['now']=array('value' => trim_gks(showDate(time(), 'd/m/Y H:i:s',1)),'type' => 's');
  //var_dump($my_wp_user_info);die();  
  $user_fullname='';
  if (isset($my_wp_user_info) and isset($my_wp_user_info->display_name) and ur_ad() or ur_lo()) $user_fullname=$my_wp_user_info->display_name;
  $in_array['user_fullname']=array('value' => trim_gks($user_fullname),'type' => 's');
  
  
  //$in_array['qr_code_url']=array('value' => '','type' => 's');
  //$in_array['aade_qrurl']=array('value' => '','type' => 's');
  
  
  
  $tr_m= array('html' => $html_in, 'mc' => $mc, 'tr_hide'=> false);
  $html_out=gks_print_form_replace_field($tr_m,$in_array);

  return $html_out;
}

function gks_print_form_replace_doc($mc,$row_doc,$html_in,$options,$row_form) {
  //s -> string
  //n -> number int
  //nl -> number myNumberFormatNo0Local
  //c -> Currency
    //cs -> Currency + symbol  
    
  $in_array=array();
  $in_array['doc_title']=array('value' => gks_print_isset_s($row_doc['title']),'type' => 's');
  $in_array['doc_title_pre']=array('value' => gks_print_isset_s($row_doc['title_pre']),'type' => 's');

  if (isset($row_doc['company'])) {
    $in_array['doc_company']=array('value' => gks_print_isset_s($row_doc['company']),'type' => 's');
    $in_array['doc_company_title']=array('value' => gks_print_isset_s($row_doc['company_title']),'type' => 's');
    $in_array['doc_company_sub_title']=array('value' => gks_print_isset_s($row_doc['company_sub_title']),'type' => 's');
    
    $in_array['doc_date']=array('value' => gks_print_isset_s($row_doc['date']),'type' => 's');
    $in_array['doc_date_d']=array('value' => gks_print_isset_s($row_doc['date_d']),'type' => 's');
    $in_array['doc_date_dw']=array('value' => gks_print_isset_s($row_doc['date_dw']),'type' => 's');
    $in_array['doc_date_dt']=array('value' => gks_print_isset_s($row_doc['date_dt']),'type' => 's');
    $in_array['doc_date_dtw']=array('value' => gks_print_isset_s($row_doc['date_dtw']),'type' => 's');
    $in_array['doc_date_dtt']=array('value' => gks_print_isset_s($row_doc['date_dtt']),'type' => 's');
    $in_array['doc_datefull']=array('value' => gks_print_isset_s($row_doc['datefull']),'type' => 's');
  
    $in_array['doc_date_add']=array('value' => gks_print_isset_s($row_doc['mydate_add']),'type' => 's');
    $in_array['doc_date_edit']=array('value' => gks_print_isset_s($row_doc['mydate_edit']),'type' => 's');
  
    $in_array['doc_seira']=array('value' => gks_print_isset_s($row_doc['seira']),'type' => 's');
    $in_array['doc_seira_descr']=array('value' => gks_print_isset_s($row_doc['seira_descr']),'type' => 's');
    $in_array['doc_number']=array('value' => gks_print_isset_n($row_doc['number']),'type' => 'n');
    $in_array['doc_number_str']=array('value' => gks_print_isset_s($row_doc['number_str']),'type' => 's');
    $in_array['doc_mark']=array('value' => gks_print_isset_s($row_doc['mark']),'type' => 's');
    
    $qr_code_url_aade='';
    if (gks_print_isset_s($row_doc['aade_qrurl'])!='') {
      $qr_code_url_aade=gks_qr_code_generate($row_doc['aade_qrurl']);
    }
    $qr_code_url_paroxos='';
    if (gks_print_isset_s($row_doc['aade_paroxos_qrurl'])!='') {
      $qr_code_url_paroxos=gks_qr_code_generate($row_doc['aade_paroxos_qrurl']);
    }
    $qr_code_url_paroxos_tf1='';
    if (gks_print_isset_s($row_doc['paroxos_tf1_url'])!='') {
      $qr_code_url_paroxos_tf1=gks_qr_code_generate($row_doc['paroxos_tf1_url']);
    }
    
    
    $doc_aade_qrurl='#';
    if ($qr_code_url_paroxos!='') {
      $qr_code_url=$qr_code_url_paroxos;
      $doc_aade_qrurl=$row_doc['aade_paroxos_qrurl'];
    } else {
      $qr_code_url=$qr_code_url_aade;
      $doc_aade_qrurl=$row_doc['aade_qrurl'];
    }
    if ($qr_code_url=='') {
      $qr_code_url=$qr_code_url_paroxos_tf1;
      $doc_aade_qrurl=$row_doc['paroxos_tf1_url'];
    }
    //echo '<pre>aaaaaaaaaaa '.$qr_code_url_paroxos_tf1; die();
    
    $in_array['doc_qr_code_url']=array('value' => $qr_code_url,'type' => 's');
    $in_array['doc_qr_code_url_aade']=array('value' => $qr_code_url_aade,'type' => 's');
    $in_array['doc_qr_code_url_paroxos']=array('value' => $qr_code_url_paroxos,'type' => 's');
    $in_array['doc_qr_code_url_paroxos_tf1']=array('value' => $qr_code_url_paroxos_tf1,'type' => 's');
    
    $in_array['doc_aade_qrurl']=array('value' => gks_print_isset_s($doc_aade_qrurl),'type' => 's');
    $in_array['doc_mydata_qrurl']=array('value' => gks_print_isset_s($row_doc['aade_qrurl']),'type' => 's');
    $in_array['doc_paroxos_qrurl']=array('value' => gks_print_isset_s($row_doc['aade_paroxos_qrurl']),'type' => 's');
    $in_array['doc_paroxos_qrurl_tf1']=array('value' => gks_print_isset_s($row_doc['paroxos_tf1_url']),'type' => 's');
    

    
    $in_array['doc_aade_invoiceuid']=array('value' => gks_print_isset_s($row_doc['aade_invoiceuid']),'type' => 's');
    $in_array['doc_paroxos_authenticationCode']=array('value' => gks_print_isset_s($row_doc['paroxos_authenticationCode']),'type' => 's');
  
  
  
    $in_array['doc_check_in']=array('value' => gks_print_isset_s($row_doc['check_in']),'type' => 's');
    $in_array['doc_check_in_d']=array('value' => gks_print_isset_s($row_doc['check_in_d']),'type' => 's');
    $in_array['doc_check_in_dw']=array('value' => gks_print_isset_s($row_doc['check_in_dw']),'type' => 's');
    $in_array['doc_check_in_dt']=array('value' => gks_print_isset_s($row_doc['check_in_dt']),'type' => 's');
    $in_array['doc_check_in_dtw']=array('value' => gks_print_isset_s($row_doc['check_in_dtw']),'type' => 's');
    $in_array['doc_check_in_dtt']=array('value' => gks_print_isset_s($row_doc['check_in_dtt']),'type' => 's');
  
    $in_array['doc_check_out']=array('value' => gks_print_isset_s($row_doc['check_out']),'type' => 's');
    $in_array['doc_check_out_d']=array('value' => gks_print_isset_s($row_doc['check_out_d']),'type' => 's');
    $in_array['doc_check_out_dw']=array('value' => gks_print_isset_s($row_doc['check_out_dw']),'type' => 's');
    $in_array['doc_check_out_dt']=array('value' => gks_print_isset_s($row_doc['check_out_dt']),'type' => 's');
    $in_array['doc_check_out_dtw']=array('value' => gks_print_isset_s($row_doc['check_out_dtw']),'type' => 's');
    $in_array['doc_check_out_dtt']=array('value' => gks_print_isset_s($row_doc['check_out_dtt']),'type' => 's');
  
    $in_array['doc_rooms']=array('value' => gks_print_isset_n($row_doc['rooms_plithos']),'type' => 'nl');
    $in_array['doc_days']=array('value' => gks_print_isset_n($row_doc['num_days']),'type' => 'nl');
    $in_array['doc_adults']=array('value' => gks_print_isset_n($row_doc['num_adults']),'type' => 'nl');
    $in_array['doc_childs']=array('value' => gks_print_isset_n($row_doc['num_childs']),'type' => 'nl');
    $in_array['doc_babys']=array('value' => gks_print_isset_n($row_doc['num_babys']),'type' => 'nl');
    $in_array['doc_visitors']=array('value' => gks_print_isset_n($row_doc['num_visitors']),'type' => 'nl');
    $in_array['doc_child_kounies']=array('value' => gks_print_isset_n($row_doc['num_child_kounies']),'type' => 'nl');
    $in_array['doc_extra_beds']=array('value' => gks_print_isset_n($row_doc['num_extra_beds']),'type' => 'nl');
  
  
    $in_array['doc_transfer_booking_number']=array('value' => gks_print_isset_s($row_doc['transfer_booking_number']),'type' => 's');
  
    $in_array['doc_transfer_start']=array('value' => gks_print_isset_s($row_doc['transfer_start']),'type' => 's');
    $in_array['doc_transfer_start_d']=array('value' => gks_print_isset_s($row_doc['transfer_start_d']),'type' => 's');
    $in_array['doc_transfer_start_dw']=array('value' => gks_print_isset_s($row_doc['transfer_start_dw']),'type' => 's');
    $in_array['doc_transfer_start_dt']=array('value' => gks_print_isset_s($row_doc['transfer_start_dt']),'type' => 's');
    $in_array['doc_transfer_start_dtw']=array('value' => gks_print_isset_s($row_doc['transfer_start_dtw']),'type' => 's');
    $in_array['doc_transfer_start_dtt']=array('value' => gks_print_isset_s($row_doc['transfer_start_dtt']),'type' => 's');
  
    $in_array['doc_transfer_end']=array('value' => gks_print_isset_s($row_doc['transfer_end']),'type' => 's');
    $in_array['doc_transfer_end_d']=array('value' => gks_print_isset_s($row_doc['transfer_end_d']),'type' => 's');
    $in_array['doc_transfer_end_dw']=array('value' => gks_print_isset_s($row_doc['transfer_end_dw']),'type' => 's');
    $in_array['doc_transfer_end_dt']=array('value' => gks_print_isset_s($row_doc['transfer_end_dt']),'type' => 's');
    $in_array['doc_transfer_end_dtw']=array('value' => gks_print_isset_s($row_doc['transfer_end_dtw']),'type' => 's');
    $in_array['doc_transfer_end_dtt']=array('value' => gks_print_isset_s($row_doc['transfer_end_dtt']),'type' => 's');
  
  
    $in_array['doc_oximata_plithos']=array('value' => gks_print_isset_n($row_doc['oximata_plithos']),'type' => 'nl');
    $in_array['doc_epivates']=array('value' => gks_print_isset_n($row_doc['num_epivates']),'type' => 'nl');
    $in_array['doc_duration_secs']=array('value' => gks_print_isset_n($row_doc['duration_secs']),'type' => 'nl');
    $in_array['doc_duration_minutes_secs']=array('value' => gks_print_isset_s($row_doc['duration_minutes_secs']),'type' => 's');
  
    $in_array['doc_task_planned_date_from']=array('value' => gks_print_isset_s($row_doc['task_planned_date_from']),'type' => 's');
    $in_array['doc_task_planned_date_from_d']=array('value' => gks_print_isset_s($row_doc['task_planned_date_from_d']),'type' => 's');
    $in_array['doc_task_planned_date_from_dw']=array('value' => gks_print_isset_s($row_doc['task_planned_date_from_dw']),'type' => 's');
    $in_array['doc_task_planned_date_from_dt']=array('value' => gks_print_isset_s($row_doc['task_planned_date_from_dt']),'type' => 's');
    $in_array['doc_task_planned_date_from_dtw']=array('value' => gks_print_isset_s($row_doc['task_planned_date_from_dtw']),'type' => 's');
    $in_array['doc_task_planned_date_from_dtt']=array('value' => gks_print_isset_s($row_doc['task_planned_date_from_dtt']),'type' => 's');
  
    $in_array['doc_task_planned_date_to']=array('value' => gks_print_isset_s($row_doc['task_planned_date_to']),'type' => 's');
    $in_array['doc_task_planned_date_to_d']=array('value' => gks_print_isset_s($row_doc['task_planned_date_to_d']),'type' => 's');
    $in_array['doc_task_planned_date_to_dw']=array('value' => gks_print_isset_s($row_doc['task_planned_date_to_dw']),'type' => 's');
    $in_array['doc_task_planned_date_to_dt']=array('value' => gks_print_isset_s($row_doc['task_planned_date_to_dt']),'type' => 's');
    $in_array['doc_task_planned_date_to_dtw']=array('value' => gks_print_isset_s($row_doc['task_planned_date_to_dtw']),'type' => 's');
    $in_array['doc_task_planned_date_to_dtt']=array('value' => gks_print_isset_s($row_doc['task_planned_date_to_dtt']),'type' => 's');
  
    $in_array['doc_subject']=array('value' => gks_print_isset_s($row_doc['subject']),'type' => 's');
    $in_array['doc_message']=array('value' => gks_print_isset_h($row_doc['message']),'type' => 's');
    $in_array['doc_internal_note']=array('value' => gks_print_isset_s($row_doc['internal_note']),'type' => 's');
    $in_array['doc_machine']=array('value' => gks_print_isset_h($row_doc['machine']),'type' => 'h');
    $in_array['doc_employee']=array('value' => gks_print_isset_h($row_doc['employee']),'type' => 'h');
  
  
    //echo '<pre>';print_r($in_array['doc_outward_transfer_start']);die();
    $in_array['doc_outward_transfer_start']=array('value' => gks_print_isset_s($row_doc['outward_transfer_start']),'type' => 's');
    $in_array['doc_outward_transfer_start_d']=array('value' => gks_print_isset_s($row_doc['outward_transfer_start_d']),'type' => 's');
    $in_array['doc_outward_transfer_start_dw']=array('value' => gks_print_isset_s($row_doc['outward_transfer_start_dw']),'type' => 's');
    $in_array['doc_outward_transfer_start_dt']=array('value' => gks_print_isset_s($row_doc['outward_transfer_start_dt']),'type' => 's');
    $in_array['doc_outward_transfer_start_dtw']=array('value' => gks_print_isset_s($row_doc['outward_transfer_start_dtw']),'type' => 's');
    $in_array['doc_outward_transfer_start_dtt']=array('value' => gks_print_isset_s($row_doc['outward_transfer_start_dtt']),'type' => 's');
    
    $in_array['doc_outward_transfer_end']=array('value' => gks_print_isset_s($row_doc['outward_transfer_end']),'type' => 's');
    $in_array['doc_outward_transfer_end_d']=array('value' => gks_print_isset_s($row_doc['outward_transfer_end_d']),'type' => 's');
    $in_array['doc_outward_transfer_end_dw']=array('value' => gks_print_isset_s($row_doc['outward_transfer_end_dw']),'type' => 's');
    $in_array['doc_outward_transfer_end_dt']=array('value' => gks_print_isset_s($row_doc['outward_transfer_end_dt']),'type' => 's');
    $in_array['doc_outward_transfer_end_dtw']=array('value' => gks_print_isset_s($row_doc['outward_transfer_end_dtw']),'type' => 's');
    $in_array['doc_outward_transfer_end_dtt']=array('value' => gks_print_isset_s($row_doc['outward_transfer_end_dtt']),'type' => 's');
  
    $in_array['doc_outward_poi_descr_from']=array('value' => gks_print_isset_s($row_doc['outward_poi_descr_from']),'type' => 's');
    $in_array['doc_outward_poi_descr_from_place']=array('value' => gks_print_isset_s($row_doc['outward_poi_descr_from_place']),'type' => 's');
    $in_array['doc_outward_from_pick_up_point']=array('value' => gks_print_isset_s($row_doc['outward_from_pick_up_point']),'type' => 's');
  
    $in_array['doc_outward_from_pick_up_time']=array('value' => gks_print_isset_s($row_doc['outward_from_pick_up_time']),'type' => 's');
    $in_array['doc_outward_from_pick_up_time_d']=array('value' => gks_print_isset_s($row_doc['outward_from_pick_up_time_d']),'type' => 's');
    $in_array['doc_outward_from_pick_up_time_dw']=array('value' => gks_print_isset_s($row_doc['outward_from_pick_up_time_dw']),'type' => 's');
    $in_array['doc_outward_from_pick_up_time_dt']=array('value' => gks_print_isset_s($row_doc['outward_from_pick_up_time_dt']),'type' => 's');
    $in_array['doc_outward_from_pick_up_time_dtw']=array('value' => gks_print_isset_s($row_doc['outward_from_pick_up_time_dtw']),'type' => 's');
    $in_array['doc_outward_from_pick_up_time_dtt']=array('value' => gks_print_isset_s($row_doc['outward_from_pick_up_time_dtt']),'type' => 's');
  
    $in_array['doc_outward_from_pick_up_time_max']=array('value' => gks_print_isset_s($row_doc['outward_from_pick_up_time_max']),'type' => 's');
    $in_array['doc_outward_from_pick_up_time_max_d']=array('value' => gks_print_isset_s($row_doc['outward_from_pick_up_time_max_d']),'type' => 's');
    $in_array['doc_outward_from_pick_up_time_max_dw']=array('value' => gks_print_isset_s($row_doc['outward_from_pick_up_time_max_dw']),'type' => 's');
    $in_array['doc_outward_from_pick_up_time_max_dt']=array('value' => gks_print_isset_s($row_doc['outward_from_pick_up_time_max_dt']),'type' => 's');
    $in_array['doc_outward_from_pick_up_time_max_dtw']=array('value' => gks_print_isset_s($row_doc['outward_from_pick_up_time_max_dtw']),'type' => 's');
    $in_array['doc_outward_from_pick_up_time_max_dtt']=array('value' => gks_print_isset_s($row_doc['outward_from_pick_up_time_max_dtt']),'type' => 's');
  
    $in_array['doc_outward_from_airline']=array('value' => gks_print_isset_s($row_doc['outward_from_airline']),'type' => 's');
    $in_array['doc_outward_from_flight_number']=array('value' => gks_print_isset_s($row_doc['outward_from_flight_number']),'type' => 's');
    $in_array['doc_outward_from_originating_airport']=array('value' => gks_print_isset_s($row_doc['outward_from_originating_airport']),'type' => 's');
  
    $in_array['doc_outward_from_flight_arrival_time']=array('value' => gks_print_isset_s($row_doc['outward_from_flight_arrival_time']),'type' => 's');
    $in_array['doc_outward_from_flight_arrival_time_d']=array('value' => gks_print_isset_s($row_doc['outward_from_flight_arrival_time_d']),'type' => 's');
    $in_array['doc_outward_from_flight_arrival_time_dw']=array('value' => gks_print_isset_s($row_doc['outward_from_flight_arrival_time_dw']),'type' => 's');
    $in_array['doc_outward_from_flight_arrival_time_dt']=array('value' => gks_print_isset_s($row_doc['outward_from_flight_arrival_time_dt']),'type' => 's');
    $in_array['doc_outward_from_flight_arrival_time_dtw']=array('value' => gks_print_isset_s($row_doc['outward_from_flight_arrival_time_dtw']),'type' => 's');
    $in_array['doc_outward_from_flight_arrival_time_dtt']=array('value' => gks_print_isset_s($row_doc['outward_from_flight_arrival_time_dtt']),'type' => 's');
  
    $in_array['doc_outward_poi_descr_to']=array('value' => gks_print_isset_s($row_doc['outward_poi_descr_to']),'type' => 's');
    $in_array['doc_outward_poi_descr_to_place']=array('value' => gks_print_isset_s($row_doc['outward_poi_descr_to_place']),'type' => 's');
    $in_array['doc_outward_to_drop_off_point']=array('value' => gks_print_isset_s($row_doc['outward_to_drop_off_point']),'type' => 's');
    $in_array['doc_outward_to_departure_airline']=array('value' => gks_print_isset_s($row_doc['outward_to_departure_airline']),'type' => 's');
    $in_array['doc_outward_to_flight_number']=array('value' => gks_print_isset_s($row_doc['outward_to_flight_number']),'type' => 's');
  
    
    $in_array['doc_outward_to_flight_departure_time']=array('value' => gks_print_isset_s($row_doc['outward_to_flight_departure_time']),'type' => 's');
    $in_array['doc_outward_to_flight_departure_time_d']=array('value' => gks_print_isset_s($row_doc['outward_to_flight_departure_time_d']),'type' => 's');
    $in_array['doc_outward_to_flight_departure_time_dw']=array('value' => gks_print_isset_s($row_doc['outward_to_flight_departure_time_dw']),'type' => 's');
    $in_array['doc_outward_to_flight_departure_time_dt']=array('value' => gks_print_isset_s($row_doc['outward_to_flight_departure_time_dt']),'type' => 's');
    $in_array['doc_outward_to_flight_departure_time_dtw']=array('value' => gks_print_isset_s($row_doc['outward_to_flight_departure_time_dtw']),'type' => 's');
    $in_array['doc_outward_to_flight_departure_time_dtt']=array('value' => gks_print_isset_s($row_doc['outward_to_flight_departure_time_dtt']),'type' => 's');
  
    $in_array['doc_outward_display']=array('value' => gks_print_isset_s($row_doc['outward_display']),'type' => 's');
    $in_array['doc_return_display']=array('value' => gks_print_isset_s($row_doc['return_display']),'type' => 's');
    
    $in_array['doc_return_transfer_start']=array('value' => gks_print_isset_s($row_doc['return_transfer_start']),'type' => 's');
    $in_array['doc_return_transfer_start_d']=array('value' => gks_print_isset_s($row_doc['return_transfer_start_d']),'type' => 's');
    $in_array['doc_return_transfer_start_dw']=array('value' => gks_print_isset_s($row_doc['return_transfer_start_dw']),'type' => 's');
    $in_array['doc_return_transfer_start_dt']=array('value' => gks_print_isset_s($row_doc['return_transfer_start_dt']),'type' => 's');
    $in_array['doc_return_transfer_start_dtw']=array('value' => gks_print_isset_s($row_doc['return_transfer_start_dtw']),'type' => 's');
    $in_array['doc_return_transfer_start_dtt']=array('value' => gks_print_isset_s($row_doc['return_transfer_start_dtt']),'type' => 's');
  
  
    $in_array['doc_return_transfer_end']=array('value' => gks_print_isset_s($row_doc['return_transfer_end']),'type' => 's');
    $in_array['doc_return_transfer_end_d']=array('value' => gks_print_isset_s($row_doc['return_transfer_end_d']),'type' => 's');
    $in_array['doc_return_transfer_end_dw']=array('value' => gks_print_isset_s($row_doc['return_transfer_end_dw']),'type' => 's');
    $in_array['doc_return_transfer_end_dt']=array('value' => gks_print_isset_s($row_doc['return_transfer_end_dt']),'type' => 's');
    $in_array['doc_return_transfer_end_dtw']=array('value' => gks_print_isset_s($row_doc['return_transfer_end_dtw']),'type' => 's');
    $in_array['doc_return_transfer_end_dtt']=array('value' => gks_print_isset_s($row_doc['return_transfer_end_dtt']),'type' => 's');
  
    $in_array['doc_return_poi_descr_from']=array('value' => gks_print_isset_s($row_doc['return_poi_descr_from']),'type' => 's');
    $in_array['doc_return_poi_descr_from_place']=array('value' => gks_print_isset_s($row_doc['return_poi_descr_from_place']),'type' => 's');
    $in_array['doc_return_from_pick_up_point']=array('value' => gks_print_isset_s($row_doc['return_from_pick_up_point']),'type' => 's');
  
    $in_array['doc_return_from_pick_up_time']=array('value' => gks_print_isset_s($row_doc['return_from_pick_up_time']),'type' => 's');
    $in_array['doc_return_from_pick_up_time_d']=array('value' => gks_print_isset_s($row_doc['return_from_pick_up_time_d']),'type' => 's');
    $in_array['doc_return_from_pick_up_time_dw']=array('value' => gks_print_isset_s($row_doc['return_from_pick_up_time_dw']),'type' => 's');
    $in_array['doc_return_from_pick_up_time_dt']=array('value' => gks_print_isset_s($row_doc['return_from_pick_up_time_dt']),'type' => 's');
    $in_array['doc_return_from_pick_up_time_dtw']=array('value' => gks_print_isset_s($row_doc['return_from_pick_up_time_dtw']),'type' => 's');
    $in_array['doc_return_from_pick_up_time_dtt']=array('value' => gks_print_isset_s($row_doc['return_from_pick_up_time_dtt']),'type' => 's');
  
    $in_array['doc_return_from_pick_up_time_max']=array('value' => gks_print_isset_s($row_doc['return_from_pick_up_time_max']),'type' => 's');
    $in_array['doc_return_from_pick_up_time_max_d']=array('value' => gks_print_isset_s($row_doc['return_from_pick_up_time_max_d']),'type' => 's');
    $in_array['doc_return_from_pick_up_time_max_dw']=array('value' => gks_print_isset_s($row_doc['return_from_pick_up_time_max_dw']),'type' => 's');
    $in_array['doc_return_from_pick_up_time_max_d']=array('value' => gks_print_isset_s($row_doc['return_from_pick_up_time_max_d']),'type' => 's');
    $in_array['doc_return_from_pick_up_time_max_dtw']=array('value' => gks_print_isset_s($row_doc['return_from_pick_up_time_max_dtw']),'type' => 's');
    $in_array['doc_return_from_pick_up_time_max_dtt']=array('value' => gks_print_isset_s($row_doc['return_from_pick_up_time_max_dtt']),'type' => 's');
  
  
  
  
    $in_array['doc_return_from_airline']=array('value' => gks_print_isset_s($row_doc['return_from_airline']),'type' => 's');
    $in_array['doc_return_from_flight_number']=array('value' => gks_print_isset_s($row_doc['return_from_flight_number']),'type' => 's');
    $in_array['doc_return_from_originating_airport']=array('value' => gks_print_isset_s($row_doc['return_from_originating_airport']),'type' => 's');
    
    $in_array['doc_return_from_flight_arrival_time']=array('value' => gks_print_isset_s($row_doc['return_from_flight_arrival_time']),'type' => 's');
    $in_array['doc_return_from_flight_arrival_time_d']=array('value' => gks_print_isset_s($row_doc['return_from_flight_arrival_time_d']),'type' => 's');
    $in_array['doc_return_from_flight_arrival_time_dw']=array('value' => gks_print_isset_s($row_doc['return_from_flight_arrival_time_dw']),'type' => 's');
    $in_array['doc_return_from_flight_arrival_time_dt']=array('value' => gks_print_isset_s($row_doc['return_from_flight_arrival_time_dt']),'type' => 's');
    $in_array['doc_return_from_flight_arrival_time_dtw']=array('value' => gks_print_isset_s($row_doc['return_from_flight_arrival_time_dtw']),'type' => 's');
    $in_array['doc_return_from_flight_arrival_time_dtt']=array('value' => gks_print_isset_s($row_doc['return_from_flight_arrival_time_dtt']),'type' => 's');
  
    $in_array['doc_return_poi_descr_to']=array('value' => gks_print_isset_s($row_doc['return_poi_descr_to']),'type' => 's');
    $in_array['doc_return_poi_descr_to_place']=array('value' => gks_print_isset_s($row_doc['return_poi_descr_to_place']),'type' => 's');
    $in_array['doc_return_to_airline']=array('value' => gks_print_isset_s($row_doc['return_to_airline']),'type' => 's');
    $in_array['doc_return_to_flight_number']=array('value' => gks_print_isset_s($row_doc['return_to_flight_number']),'type' => 's');
  
    $in_array['doc_return_to_flight_departure_time']=array('value' => gks_print_isset_s($row_doc['return_to_flight_departure_time']),'type' => 's');
    $in_array['doc_return_to_flight_departure_time_d']=array('value' => gks_print_isset_s($row_doc['return_to_flight_departure_time_d']),'type' => 's');
    $in_array['doc_return_to_flight_departure_time_dw']=array('value' => gks_print_isset_s($row_doc['return_to_flight_departure_time_dw']),'type' => 's');
    $in_array['doc_return_to_flight_departure_time_dt']=array('value' => gks_print_isset_s($row_doc['return_to_flight_departure_time_dt']),'type' => 's');
    $in_array['doc_return_to_flight_departure_time_dtw']=array('value' => gks_print_isset_s($row_doc['return_to_flight_departure_time_dtw']),'type' => 's');
    $in_array['doc_return_to_flight_departure_time_dtt']=array('value' => gks_print_isset_s($row_doc['return_to_flight_departure_time_dtt']),'type' => 's');
  
    $in_array['doc_return_to_drop_off_point']=array('value' => gks_print_isset_s($row_doc['return_to_drop_off_point']),'type' => 's');
  
  
    $in_array['doc_text_act1ofal']=array('value' => gks_print_isset_h($row_doc['text_act1ofal']),'type' => 'h');
    $in_array['doc_text_act1otdal']=array('value' => gks_print_isset_h($row_doc['text_act1otdal']),'type' => 'h');
    $in_array['doc_text_act1rfal']=array('value' => gks_print_isset_h($row_doc['text_act1rfal']),'type' => 'h');
    $in_array['doc_text_act1rtal']=array('value' => gks_print_isset_h($row_doc['text_act1rtal']),'type' => 'h');
    $in_array['doc_text_act2offn']=array('value' => gks_print_isset_h($row_doc['text_act2offn']),'type' => 'h');
    $in_array['doc_text_act2otfn']=array('value' => gks_print_isset_h($row_doc['text_act2otfn']),'type' => 'h');
    $in_array['doc_text_act2rffn']=array('value' => gks_print_isset_h($row_doc['text_act2rffn']),'type' => 'h');
    $in_array['doc_text_act2rtfn']=array('value' => gks_print_isset_h($row_doc['text_act2rtfn']),'type' => 'h');
    $in_array['doc_text_act3ofoap']=array('value' => gks_print_isset_h($row_doc['text_act3ofoap']),'type' => 'h');
    $in_array['doc_text_act3rfoap']=array('value' => gks_print_isset_h($row_doc['text_act3rfoap']),'type' => 'h');
    $in_array['doc_text_act4offat']=array('value' => gks_print_isset_h($row_doc['text_act4offat']),'type' => 'h');
    $in_array['doc_text_act4rffat']=array('value' => gks_print_isset_h($row_doc['text_act4rffat']),'type' => 'h');
    $in_array['doc_text_act5otfdt']=array('value' => gks_print_isset_h($row_doc['text_act5otfdt']),'type' => 'h');
    $in_array['doc_text_act5rtfdt']=array('value' => gks_print_isset_h($row_doc['text_act5rtfdt']),'type' => 'h');
    $in_array['doc_text_act6ofal']=array('value' => gks_print_isset_h($row_doc['text_act6ofal']),'type' => 'h');
    $in_array['doc_text_act6otdal']=array('value' => gks_print_isset_h($row_doc['text_act6otdal']),'type' => 'h');
    $in_array['doc_text_act6rfal']=array('value' => gks_print_isset_h($row_doc['text_act6rfal']),'type' => 'h');
    $in_array['doc_text_act6rtal']=array('value' => gks_print_isset_h($row_doc['text_act6rtal']),'type' => 'h');
    $in_array['doc_text_act7ofoap']=array('value' => gks_print_isset_h($row_doc['text_act7ofoap']),'type' => 'h');
    $in_array['doc_text_act8rfoap']=array('value' => gks_print_isset_h($row_doc['text_act8rfoap']),'type' => 'h');
  
  
  
    if (isset($row_doc['gks_price_original_net'])==false) $row_doc['gks_price_original_net']=0;
    $in_array['doc_priceall_start']=array('value' => gks_print_isset_n($row_doc['gks_price_original_net']),'type' => 'c');
    $doc_priceall_ekptosi=0;
    if (isset($row_doc['gks_price_original_net']) and isset($row_doc['gks_price_net'])) {
      $doc_priceall_ekptosi=$row_doc['gks_price_original_net']-$row_doc['gks_price_net'];
    }
    $in_array['doc_priceall_ekptosi']=array('value' => gks_print_isset_n($doc_priceall_ekptosi),'type' => 'c');
  
    $in_array['doc_posotita']=array('value' => gks_print_isset_n($row_doc['products_posotita']),'type' => 'nl');
    $in_array['doc_priceall']=array('value' => gks_print_isset_n($row_doc['gks_price_net']),'type' => 'c');
    $in_array['doc_fpa_amount_total']=array('value' => gks_print_isset_n($row_doc['gks_price_fpa']),'type' => 'c');
    $in_array['doc_netfpa_amount_total']=array('value' => gks_print_isset_n($row_doc['gks_price_netfpa']),'type' => 'c');
    $in_array['doc_priceall_total']=array('value' => gks_print_isset_n($row_doc['gks_price_total']),'type' => 'c');
    $in_array['doc_priceall_total_olografos_gr']=array('value' => gks_print_isset_s(gks_olografos_numberToGreekWords($row_doc['gks_price_total'],true)),'type' => 's');
    $in_array['doc_priceall_total_olografos_en']=array('value' => gks_print_isset_s(gks_olografos_numberToEnglishWords($row_doc['gks_price_total'],true)),'type' => 's');
    
    
    $in_array['doc_withheld']=array('value' => gks_print_isset_n($row_doc['totalWithheldAmount']),'type' => 'c');
    $in_array['doc_othertaxes']=array('value' => gks_print_isset_n($row_doc['totalOtherTaxesAmount']),'type' => 'c');
    $in_array['doc_stampduty']=array('value' => gks_print_isset_n($row_doc['totalStampDutyamount']),'type' => 'c');
    $in_array['doc_fees']=array('value' => gks_print_isset_n($row_doc['totalFeesAmount']),'type' => 'c');
    $in_array['doc_deductions']=array('value' => gks_print_isset_n($row_doc['totalDeductionsAmount']),'type' => 'c');
    
    
  
    $in_array['doc_enarji_apostolis']=array('value' => gks_print_isset_s($row_doc['enarji_apostolis']),'type' => 's');
    $in_array['doc_enarji_apostolis_date']=array('value' => gks_print_isset_s($row_doc['enarji_apostolis_date']),'type' => 's');
    $in_array['doc_enarji_apostolis_time']=array('value' => gks_print_isset_s($row_doc['enarji_apostolis_time']),'type' => 's');
    $in_array['doc_arithmos_oximatos']=array('value' => gks_print_isset_s($row_doc['arithmos_oximatos']),'type' => 's');
    
    $in_array['doc_isdeliverynote_display']=array('value' => gks_print_isset_s($row_doc['isdeliverynote_display']),'type' => 's');
    
    $in_array['doc_load_branch']=array('value' => gks_print_isset_s($row_doc['load_branch']),'type' => 's');
    $in_array['doc_load_odos']=array('value' => gks_print_isset_s($row_doc['load_odos']),'type' => 's');
    $in_array['doc_load_arithmos']=array('value' => gks_print_isset_s($row_doc['load_arithmos']),'type' => 's');
    $in_array['doc_load_orofos']=array('value' => gks_print_isset_s($row_doc['load_orofos']),'type' => 's');
    $in_array['doc_load_perioxi']=array('value' => gks_print_isset_s($row_doc['load_perioxi']),'type' => 's');
    $in_array['doc_load_poli']=array('value' => gks_print_isset_s($row_doc['load_poli']),'type' => 's');
    $in_array['doc_load_tk']=array('value' => gks_print_isset_s($row_doc['load_tk']),'type' => 's');
    $in_array['doc_load_country_name']=array('value' => gks_print_isset_s($row_doc['country_name_load']),'type' => 's');
    $in_array['doc_load_nomos_descr']=array('value' => gks_print_isset_s($row_doc['nomos_descr_load']),'type' => 's');
    
    $in_array['doc_deli_branch']=array('value' => gks_print_isset_s($row_doc['deli_branch']),'type' => 's');
    $in_array['doc_deli_odos']=array('value' => gks_print_isset_s($row_doc['deli_odos']),'type' => 's');
    $in_array['doc_deli_arithmos']=array('value' => gks_print_isset_s($row_doc['deli_arithmos']),'type' => 's');
    $in_array['doc_deli_orofos']=array('value' => gks_print_isset_s($row_doc['deli_orofos']),'type' => 's');
    $in_array['doc_deli_perioxi']=array('value' => gks_print_isset_s($row_doc['deli_perioxi']),'type' => 's');
    $in_array['doc_deli_poli']=array('value' => gks_print_isset_s($row_doc['deli_poli']),'type' => 's');
    $in_array['doc_deli_tk']=array('value' => gks_print_isset_s($row_doc['deli_tk']),'type' => 's');
    $in_array['doc_deli_country_name']=array('value' => gks_print_isset_s($row_doc['country_name_deli']),'type' => 's');
    $in_array['doc_deli_nomos_descr']=array('value' => gks_print_isset_s($row_doc['nomos_descr_deli']),'type' => 's');
    
    $in_array['doc_skopos_diakinisis']=array('value' => gks_print_isset_s($row_doc['skopos_diakinisis']),'type' => 's');
    $in_array['doc_tropos_pliromis']=array('value' => gks_print_isset_s($row_doc['tropos_pliromis']),'type' => 's');
    $in_array['doc_tropos_pliromis_via']=array('value' => gks_print_isset_s($row_doc['tropos_pliromis_via']),'type' => 's');
    
    $in_array['doc_tropos_apostolis']=array('value' => gks_print_isset_s($row_doc['tropos_apostolis']),'type' => 's');
    $in_array['doc_arithmos_aposolis']=array('value' => gks_print_isset_s($row_doc['arithmos_aposolis']),'type' => 's');
    $in_array['doc_user_notes']=array('value' => nl2br_gks(gks_print_isset_s($row_doc['user_notes'])),'type' => 's');
    //$in_array['doc_sxolio']=array('value' => nl2br_gks(gks_print_isset_s($row_doc['sxolio'])),'type' => 's');
    $in_array['doc_note_doc']=array('value' => nl2br_gks(gks_print_isset_s($row_doc['note_doc'])),'type' => 's');
    $in_array['doc_note_production']=array('value' => nl2br_gks(gks_print_isset_s($row_doc['note_production'])),'type' => 's');
    $in_array['doc_note_logistirio']=array('value' => nl2br_gks(gks_print_isset_s($row_doc['note_logistirio'])),'type' => 's');
  
    $in_array['doc_ddate']=array('value' => gks_print_isset_s($row_doc['ddate']),'type' => 's');
    $in_array['doc_occasion_title']=array('value' => gks_print_isset_s($row_doc['occasion_title']),'type' => 's');
    $in_array['doc_occasion_type_descr']=array('value' => gks_print_isset_s($row_doc['occasion_type_descr']),'type' => 's');
    $in_array['doc_occasion_mydate_add']=array('value' => gks_print_isset_s($row_doc['occasion_mydate_add']),'type' => 's');
   
    $in_array['doc_idiotites_text']=array('value' => gks_print_isset_h($row_doc['idiotites_text']),'type' => 'h');
   
    $in_array['doc_terminal_ids']=array('value' => gks_print_isset_s($row_doc['terminal_ids']),'type' => 's');
   
   
    if (isset($row_doc['rbs_stream'])) {
      // [<]997981320;064202089;;169;aa;12345;0.00;0.00;0.00;0.00;0.00;0.00;0.00;0.00;0.00;0.00;0[>]
      //; Ta pedia diachorizontai me ton charaktira tou Ellinikou erotimatikou (;).
      //; Den yparchei symvolo diachorismou chiliadon.
      //; Ta posa einai me dyo dekadika psifia kai to symvolo tis ypodiastolis einai i teleia.
      //; Ta arnitika posa paristanontai gia paradeigma -100. 00
      //; To mideniko poso paristanetai os 0. 00
      // 
      //epipleon sto pedio tou Kodikou parastatikou (4o pedio) kai gia synkekrimeno typo parastatikon,
      //mporoun na prostethoun ta pedia tou Arithmou apallagis kai tou posou parakratisis diachorismena me# kai $ antistoicha.      // 
      //[<]997981320;064202089;1234567890123456789;169#24$52.00;aa;12345;0.00;0.00;0.00;0.00;0.00;0.00;0.00;0.00;0.00;0.00;0[>]
  
      if ($row_doc['rbs_stream']['rbs_code_a']==215) { //akyrotiko
        $rbs_stream='';
        $rbs_stream.='[<]';
        $rbs_stream.=$row_doc['rbs_stream']['afm_ekdosi'].';';
        $rbs_stream.=$row_doc['rbs_stream']['afm_pelati'].';';
        $rbs_stream.=$row_doc['rbs_stream']['karta'].';';
        $rbs_stream.=$row_doc['rbs_stream']['rbs_code_a'].';';
        
        $rbs_stream.=$row_doc['rbs_stream']['seira'].'#';
        $rbs_stream.=$row_doc['rbs_stream']['cancel_doc']['rbs_code_a'].'#';
        $rbs_stream.=$row_doc['rbs_stream']['cancel_doc']['inv_acc_number_int'].'#';
        $rbs_stream.=$row_doc['rbs_stream']['cancel_doc']['inv_acc_seira_code'].';';
        
        
        $rbs_stream.=$row_doc['rbs_stream']['aa'].';';
        $rbs_stream.=number_format(-$row_doc['rbs_stream']['net_a'],2,'.','').';';
        $rbs_stream.=number_format(-$row_doc['rbs_stream']['net_b'],2,'.','').';';
        $rbs_stream.=number_format(-$row_doc['rbs_stream']['net_c'],2,'.','').';';
        $rbs_stream.=number_format(-$row_doc['rbs_stream']['net_d'],2,'.','').';';
        $rbs_stream.=number_format(-$row_doc['rbs_stream']['net_e'],2,'.','').';';
        $rbs_stream.=number_format(-$row_doc['rbs_stream']['fpa_a'],2,'.','').';';
        $rbs_stream.=number_format(-$row_doc['rbs_stream']['fpa_b'],2,'.','').';';
        $rbs_stream.=number_format(-$row_doc['rbs_stream']['fpa_c'],2,'.','').';';
        $rbs_stream.=number_format(-$row_doc['rbs_stream']['fpa_d'],2,'.','').';';
        $rbs_stream.=number_format(-$row_doc['rbs_stream']['total'],2,'.','').';';
        $rbs_stream.=$row_doc['rbs_stream']['currency'];
        $rbs_stream.='[>]';
              
      } else {
        $rbs_stream='';
        $rbs_stream.='[<]';
        $rbs_stream.=$row_doc['rbs_stream']['afm_ekdosi'].';';
        $rbs_stream.=$row_doc['rbs_stream']['afm_pelati'].';';
        $rbs_stream.=$row_doc['rbs_stream']['karta'].';';
        $rbs_stream.=$row_doc['rbs_stream']['rbs_code_a'].';';
        $rbs_stream.=$row_doc['rbs_stream']['seira'].';';
        $rbs_stream.=$row_doc['rbs_stream']['aa'].';';
        $rbs_stream.=number_format($row_doc['rbs_stream']['net_a'],2,'.','').';';
        $rbs_stream.=number_format($row_doc['rbs_stream']['net_b'],2,'.','').';';
        $rbs_stream.=number_format($row_doc['rbs_stream']['net_c'],2,'.','').';';
        $rbs_stream.=number_format($row_doc['rbs_stream']['net_d'],2,'.','').';';
        $rbs_stream.=number_format($row_doc['rbs_stream']['net_e'],2,'.','').';';
        $rbs_stream.=number_format($row_doc['rbs_stream']['fpa_a'],2,'.','').';';
        $rbs_stream.=number_format($row_doc['rbs_stream']['fpa_b'],2,'.','').';';
        $rbs_stream.=number_format($row_doc['rbs_stream']['fpa_c'],2,'.','').';';
        $rbs_stream.=number_format($row_doc['rbs_stream']['fpa_d'],2,'.','').';';
        $rbs_stream.=number_format($row_doc['rbs_stream']['total'],2,'.','').';';
        $rbs_stream.=$row_doc['rbs_stream']['currency'];
        $rbs_stream.='[>]';
      }
      
      $rbs_stream=htmlspecialchars_gks($rbs_stream);
      
      $in_array['gks_rbs_stream']=array(
        'value'=>$rbs_stream,
        'type' => 's',
      );
      
      
      
    }
  
    $in_array['warehouse_from_hide_if_is_other']=array('value' => gks_print_isset_s($row_doc['warehouse_from_hide_if_is_other']),'type' => 's');
    $in_array['warehouse_from_name']=array('value' => gks_print_isset_s($row_doc['warehouse_from_name']),'type' => 's');
    $in_array['warehouse_from_topos_fortosis']=array('value' => gks_print_isset_s($row_doc['warehouse_from_topos_fortosis']),'type' => 's');
    $in_array['warehouse_from_phone']=array('value' => gks_print_isset_s($row_doc['warehouse_from_phone']),'type' => 's');
    $in_array['warehouse_from_odos']=array('value' => gks_print_isset_s($row_doc['warehouse_from_odos']),'type' => 's');
    $in_array['warehouse_from_arithmos']=array('value' => gks_print_isset_s($row_doc['warehouse_from_arithmos']),'type' => 's');
    $in_array['warehouse_from_orofos']=array('value' => gks_print_isset_s($row_doc['warehouse_from_orofos']),'type' => 's');
    $in_array['warehouse_from_perioxi']=array('value' => gks_print_isset_s($row_doc['warehouse_from_perioxi']),'type' => 's');
    $in_array['warehouse_from_tk']=array('value' => gks_print_isset_s($row_doc['warehouse_from_tk']),'type' => 's');
    $in_array['warehouse_from_poli']=array('value' => gks_print_isset_s($row_doc['warehouse_from_poli']),'type' => 's');
    $in_array['warehouse_from_nomos_descr']=array('value' => gks_print_isset_s($row_doc['warehouse_from_nomos_descr']),'type' => 's');
    $in_array['warehouse_from_country_name']=array('value' => gks_print_isset_s($row_doc['warehouse_from_country_name']),'type' => 's');
  
    $in_array['warehouse_to_hide_if_is_other']=array('value' => gks_print_isset_s($row_doc['warehouse_to_hide_if_is_other']),'type' => 's');
    $in_array['warehouse_to_name']=array('value' => gks_print_isset_s($row_doc['warehouse_to_name']),'type' => 's');
    $in_array['warehouse_to_topos_fortosis']=array('value' => gks_print_isset_s($row_doc['warehouse_to_topos_fortosis']),'type' => 's');
    $in_array['warehouse_to_phone']=array('value' => gks_print_isset_s($row_doc['warehouse_to_phone']),'type' => 's');
    $in_array['warehouse_to_odos']=array('value' => gks_print_isset_s($row_doc['warehouse_to_odos']),'type' => 's');
    $in_array['warehouse_to_arithmos']=array('value' => gks_print_isset_s($row_doc['warehouse_to_arithmos']),'type' => 's');
    $in_array['warehouse_to_orofos']=array('value' => gks_print_isset_s($row_doc['warehouse_to_orofos']),'type' => 's');
    $in_array['warehouse_to_perioxi']=array('value' => gks_print_isset_s($row_doc['warehouse_to_perioxi']),'type' => 's');
    $in_array['warehouse_to_tk']=array('value' => gks_print_isset_s($row_doc['warehouse_to_tk']),'type' => 's');
    $in_array['warehouse_to_poli']=array('value' => gks_print_isset_s($row_doc['warehouse_to_poli']),'type' => 's');
    $in_array['warehouse_to_nomos_descr']=array('value' => gks_print_isset_s($row_doc['warehouse_to_nomos_descr']),'type' => 's');
    $in_array['warehouse_to_country_name']=array('value' => gks_print_isset_s($row_doc['warehouse_to_country_name']),'type' => 's');

  }

  $in_array['doc_photos']=array('value' => gks_print_isset_h($row_doc['photos']),'type' => 'h');
  $in_array['doc_links']=array('value' => gks_print_isset_h($row_doc['links']),'type' => 'h');


  foreach ($row_doc as $key => $value) {
    if (substr($key,0,7)=='custom_') {
      if (in_array($value['type'],[9])) {
        $in_array[$key]=array('value' => gks_print_isset_h($value['value']),'type' => 'h');
      } else {
        $in_array[$key]=array('value' => gks_print_isset_s($value['value']),'type' => 's');
      }
    }
  }
  

  gks_plugins_functions_run('functions_print_gks_print_form_replace_doc_in_array',array(
    'row_doc'=>&$row_doc,
    'in_array'=>&$in_array,
  ));
  

  
  //echo '<pre>';print_r($in_array);die();  
   
  $tr_m= array('html' => $html_in, 'mc' => $mc, 'tr_hide'=> false);
  $html_out=gks_print_form_replace_field($tr_m,$in_array);

  return $html_out;
}

function gks_print_form_replace_doc_canceled($mc,$row_canceled_doc,$html_in,$options,$row_form) {
  //s -> string
  //n -> number int
  //nl -> number myNumberFormatNo0Local
  //c -> Currency
    //cs -> Currency + symbol  
    
  $in_array=array();

  $in_array['doc_canceled_display']=array('value' => gks_print_isset_s($row_canceled_doc['display']),'type' => 's');
  $in_array['doc_canceled_title']=array('value' => gks_print_isset_s($row_canceled_doc['title']),'type' => 's');
  $in_array['doc_canceled_title_pre']=array('value' => gks_print_isset_s($row_canceled_doc['title_pre']),'type' => 's');
  $in_array['doc_canceled_company']=array('value' => gks_print_isset_s($row_canceled_doc['company']),'type' => 's');
  $in_array['doc_canceled_date']=array('value' => gks_print_isset_s($row_canceled_doc['date']),'type' => 's');
  $in_array['doc_canceled_datefull']=array('value' => gks_print_isset_s($row_canceled_doc['datefull']),'type' => 's');
  $in_array['doc_canceled_seira']=array('value' => gks_print_isset_s($row_canceled_doc['seira']),'type' => 's');
  $in_array['doc_canceled_seira_descr']=array('value' => gks_print_isset_s($row_canceled_doc['seira_descr']),'type' => 's');
  $in_array['doc_canceled_number']=array('value' => gks_print_isset_n($row_canceled_doc['number']),'type' => 'n');
  $in_array['doc_canceled_number_str']=array('value' => gks_print_isset_s($row_canceled_doc['number_str']),'type' => 'n');
  $in_array['doc_canceled_mark']=array('value' => gks_print_isset_s($row_canceled_doc['mark']),'type' => 's');
  $in_array['doc_canceled_aade_qrurl']=array('value' => gks_print_isset_s($row_canceled_doc['aade_qrurl']),'type' => 's');
  $in_array['doc_canceled_paroxos_qrurl']=array('value' => gks_print_isset_s($row_canceled_doc['aade_paroxos_qrurl']),'type' => 's');
  $in_array['doc_canceled_paroxos_qrurl_tf1']=array('value' => gks_print_isset_s($row_canceled_doc['paroxos_tf1_url']),'type' => 's');
  $in_array['doc_canceled_aade_invoiceuid']=array('value' => gks_print_isset_s($row_canceled_doc['aade_invoiceuid']),'type' => 's');
  $in_array['doc_canceled_paroxos_authenticationCode']=array('value' => gks_print_isset_s($row_canceled_doc['paroxos_authenticationCode']),'type' => 's');

  //print '<pre>[';print_r($row_canceled_doc['number']);print ']';die();
    
  $tr_m= array('html' => $html_in, 'mc' => $mc, 'tr_hide'=> false);
  $html_out=gks_print_form_replace_field($tr_m,$in_array);

  return $html_out;  
}

function gks_print_form_replace_doc_credit($mc,$row_credit_doc,$html_in,$options,$row_form) {
  //s -> string
  //n -> number int
  //nl -> number myNumberFormatNo0Local
  //c -> Currency
    //cs -> Currency + symbol  
    
  $in_array=array();

  $in_array['doc_credit_display']=array('value' => gks_print_isset_s($row_credit_doc['display']),'type' => 's');
  $in_array['doc_credit_title']=array('value' => gks_print_isset_s($row_credit_doc['title']),'type' => 's');
  $in_array['doc_credit_title_pre']=array('value' => gks_print_isset_s($row_credit_doc['title_pre']),'type' => 's');
  $in_array['doc_credit_company']=array('value' => gks_print_isset_s($row_credit_doc['company']),'type' => 's');
  $in_array['doc_credit_date']=array('value' => gks_print_isset_s($row_credit_doc['date']),'type' => 's');
  $in_array['doc_credit_datefull']=array('value' => gks_print_isset_s($row_credit_doc['datefull']),'type' => 's');
  $in_array['doc_credit_seira']=array('value' => gks_print_isset_s($row_credit_doc['seira']),'type' => 's');
  $in_array['doc_credit_seira_descr']=array('value' => gks_print_isset_s($row_credit_doc['seira_descr']),'type' => 's');
  $in_array['doc_credit_number']=array('value' => gks_print_isset_n($row_credit_doc['number']),'type' => 'n');
  $in_array['doc_credit_number_str']=array('value' => gks_print_isset_s($row_credit_doc['number_str']),'type' => 'n');
  $in_array['doc_credit_mark']=array('value' => gks_print_isset_s($row_credit_doc['mark']),'type' => 's');
  $in_array['doc_credit_aade_qrurl']=array('value' => gks_print_isset_s($row_credit_doc['aade_qrurl']),'type' => 's');
  $in_array['doc_credit_paroxos_qrurl']=array('value' => gks_print_isset_s($row_credit_doc['aade_paroxos_qrurl']),'type' => 's');
  $in_array['doc_credit_paroxos_qrurl_tf1']=array('value' => gks_print_isset_s($row_credit_doc['paroxos_tf1_url']),'type' => 's');
  $in_array['doc_credit_aade_invoiceuid']=array('value' => gks_print_isset_s($row_credit_doc['aade_invoiceuid']),'type' => 's');
  $in_array['doc_credit_paroxos_authenticationCode']=array('value' => gks_print_isset_s($row_credit_doc['paroxos_authenticationCode']),'type' => 's');

  //print '<pre>[';print_r($row_credit_doc['number']);print ']';die();
  
  $tr_m= array('html' => $html_in, 'mc' => $mc, 'tr_hide'=> false);
  $html_out=gks_print_form_replace_field($tr_m,$in_array);

  return $html_out;  
}

function gks_print_form_details_body($html_in,$row_eidoi,$lots_and_serials_analysis_html,$products_lots_serials) {
  global $db_link;
  $html_in=trim_gks($html_in);
  if ($html_in == '') return '';
  
  $dom = new DomDocument();
  if (@$dom->loadHTML('<?xml encoding="utf-8" data="details_body"?>'.$html_in) === false) {
    return $html_in;
  }
  $tables = $dom->getElementsByTagName('table');  
  if ($tables->length < 1) return $html_in;
  $mytable=$tables[0];
  $mytrs = $mytable->getElementsByTagName('tr');  
  
  //san to:
  //<tr style="border-width: 0px;"><td colspan="6" style="text-align:center;font-size: 14pt;font-weight: bold;padding-top: 30pt;border-width: 0px;">{eidos_set}</td></tr>
  //alla encode se base64
  
  $data_gks_breakset=trim_gks((string)$mytable->getAttribute('data-gks-breakset'));
  $data_gks_breakset=base64_decode($data_gks_breakset);
  
  //echo '<pre>';echo time();echo $data_gks_breakset;die();
  
  $thead_index=-1;
  $tfoot_index=-1;
  $found_eidos=false;
  for ($i = 0; $i < $mytrs->length; $i++) {
    
    $text=$mytrs[$i]->textContent;
    $nodeName=$mytrs[$i]->parentNode->nodeName;
    if (is_string($nodeName)==false) $nodeName='agnosto';
    
    if (strpos($text, '{eidos_') !== false or $nodeName=='tbody') {
      $found_eidos=true;
    } else {
      if ($found_eidos==false or $nodeName=='thead') $thead_index=$i;
    }
    if (strpos($text, '{doc_') !== false or $nodeName=='tfoot') {
      if ($tfoot_index==-1) $tfoot_index=$i;
    }
    
  }

  if ($thead_index == -1) return $html_in; //den vrethike kefalida
  if ($found_eidos == false) return $html_in; //den vrethike tr me eidos
  
  
  $tfoot_cut=array();
  if ($tfoot_index>=0) {
    $nodeName=$mytrs[$tfoot_index]->parentNode->nodeName;
    if (is_string($nodeName)==false) $nodeName='agnosto';
    if ($nodeName!='tfoot') {

      for ($i = 0; $i < $mytrs->length; $i++) {
        if ($i >= $tfoot_index) {
          $tfoot_cut[] = $dom->saveHTML($mytrs[$i]);
        }
      }
      for ($i = $mytrs->length - 1; $i >=0; $i--) {
        if ($i >= $tfoot_index) {
          $temp = $mytrs[$i];
          $temp->parentNode->removeChild($temp);
        }
      } 
      $tfoot_index = -1;
    }
  }
  
  $tr_htmls=array();
  for ($i = 0; $i < $mytrs->length; $i++) {
    if ($i > $thead_index and ($i < $tfoot_index or $tfoot_index==-1)) {
      $tr_htmls[] = $dom->saveHTML($mytrs[$i]);
    }
  }
  //print '<pre>';print_r($tr_htmls);die();

  for ($i = $mytrs->length - 1; $i >=0; $i--) {
    if ($i > $thead_index and ($i < $tfoot_index or $tfoot_index==-1)) {
      $temp = $mytrs[$i];
      $temp->parentNode->removeChild($temp);
    }
  }  



  $tbody=$mytable->getElementsByTagName('tbody')[0];  
  if ($tbody === null) $tbody=$mytable;
  
  $tr_ma=array();
  foreach ($tr_htmls as $tr_html) {
    $mc=gks_print_form_mc($tr_html);
    $tr_hide=false;
    if (strpos($tr_html, '{hide}') !== false) {
      $tr_html=str_replace('{hide}', '', $tr_html);
      $tr_hide=true;
    }
    $tr_ma[]= array('html' => $tr_html, 'mc' => $mc, 'tr_hide'=> $tr_hide);
  }
  
  //print '<pre>';print_r($tr_ma);die();
  
  $attribute_ids=[];$attributes_data=[];
  foreach ($tr_ma as $tr_ma_val) {
    foreach ($tr_ma_val['mc'] as $mc_key => $mc_val) {
      if (substr($mc_key,0,16)=='eidos_attribute_') {
        $attribute_ids[]=substr($mc_key,16);
      }
    }
  } 
  //print '<pre>';print_r($attribute_ids);die();
  if (count($attribute_ids)>0) {
    $pids=[];
    foreach ($row_eidoi as $eidos) {
      if (isset($eidos['product_id'])) {
        $pids[]=$eidos['product_id']; 
      }
    }
    if (count($pids)>0) {
      $sql_attr="SELECT 
      gks_eshop_products_variables.product_id, 
      gks_eshop_products_variables.product_idiotita_term_id, 
      gks_product_idiotites_terms.idiotita_term_name, 
      gks_product_idiotites_terms.idiotita_id
      FROM gks_eshop_products_variables 
      LEFT JOIN gks_product_idiotites_terms ON gks_eshop_products_variables.product_idiotita_term_id = gks_product_idiotites_terms.id_product_idiotita_term
      WHERE gks_eshop_products_variables.product_id in (".implode(',',$pids).")
      AND gks_product_idiotites_terms.idiotita_id In (".implode(',',$attribute_ids).")";
      $result_attr = $db_link->query($sql_attr);        
      if (!$result_attr) {debug_mail(false,'error sql',$sql_attr);$ret['message']='sql error'; return $ret;}

      while ($rowattr = $result_attr->fetch_assoc()) {
        if (isset($attributes_data[$rowattr['product_id']])==false) {
          $attributes_data[$rowattr['product_id']]=array(
            'idiotites' => array(),
          );
        }
        if (isset($attributes_data[$rowattr['product_id']]['idiotites'][$rowattr['idiotita_id']])==false) {
          $attributes_data[$rowattr['product_id']]['idiotites'][$rowattr['idiotita_id']]=array(
            'terms'=>[],
          );
        }
        $attributes_data[$rowattr['product_id']]['idiotites'][$rowattr['idiotita_id']]['terms'][]=$rowattr['idiotita_term_name'];
      }
      //print '<pre>';print_r($pids);die();
      //print '<pre>';print_r($attributes_data);die();
    }
  }
  
  
  $old_set='';
  $aa=0;
  foreach ($row_eidoi as $eidos) {
    $aa++;
    foreach ($tr_ma as $tr_m) {
      
      if ($data_gks_breakset!='') {
        $curr_set=trim_gks($eidos['product_set']);
        if ($aa==1) $old_set=$curr_set;
        if ($old_set!=$curr_set) {
        
          //echo '<pre>';var_dump($old_set);var_dump($curr_set);var_dump($eidos);die();  
          
          gks_appendHTML($tbody, str_replace('{eidos_set}',$curr_set,$data_gks_breakset));
          $old_set=$curr_set;
        }
      }
      
      //s -> string
      //n -> number int
      //nl -> number myNumberFormatNo0Local
      //c -> Currency
        //cs -> Currency + symbol
      
      $in_array=array();

      $in_array['eidos_aa']=array('value' => gks_print_isset_n($aa),'type' => 'n');
      $in_array['eidos_code']=array('value' => gks_print_isset_s($eidos['product_code']),'type' => 's');
      $in_array['eidos_sku']=array('value' => gks_print_isset_s($eidos['product_sku']),'type' => 's');
      $in_array['eidos_gtin']=array('value' => gks_print_isset_s($eidos['product_gtin']),'type' => 's');
      $in_array['eidos_upc']=array('value' => gks_print_isset_s($eidos['product_upc']),'type' => 's');
      $in_array['eidos_ean']=array('value' => gks_print_isset_s($eidos['product_ean']),'type' => 's');
      $in_array['eidos_isbn']=array('value' => gks_print_isset_s($eidos['product_isbn']),'type' => 's');
      $in_array['eidos_taric']=array('value' => gks_print_isset_s($eidos['product_taric']),'type' => 's');
      
      $in_array['eidos_photo_url']=array('value' => gks_print_isset_s($eidos['product_photo_p']),'type' => 's');
      $in_array['eidos_descr']=array('value' => nl2br_gks(gks_print_isset_s($eidos['product_descr'])),'type' => 's');
      $in_array['eidos_comments']=array('value' => nl2br_gks(gks_print_isset_s($eidos['product_comments'])),'type' => 's');
      $in_array['eidos_mm']=array('value' => gks_print_isset_s($eidos['monada_symbol']),'type' => 's');
      $in_array['eidos_set']=array('value' => gks_print_isset_s($eidos['product_set']),'type' => 's');
      $in_array['eidos_sheets']=array('value' => gks_print_isset_n($eidos['product_sheets']),'type' => 'nl');
      $in_array['eidos_quantity']=array('value' => gks_print_isset_n($eidos['product_quantity']),'type' => 'nl');
      $in_array['eidos_priceitem']=array('value' => gks_print_isset_n($eidos['product_price_final_peritem_net']),'type' => 'c');
      $in_array['eidos_priceitem_start']=array('value' => gks_print_isset_n($eidos['product_price_start_peritem_net']),'type' => 'c');
      
      
      $in_array['eidos_ekptosi_net']=array('value' => gks_print_isset_n($eidos['product_price_ekptosi_net']),'type' => 'c');
      $in_array['eidos_ekptosi_pososto']=array('value' => gks_print_isset_n($eidos['product_price_ekptosi_pososto']),'type' => 'nl');
      
      
      
      
      
      $in_array['eidos_fpa_rate']=array('value' => gks_print_isset_n($eidos['product_fpa_pososto'])*100,'type' => 'nl');
      $in_array['eidos_fpa_amount']=array('value' => gks_print_isset_n($eidos['product_price_final_peritem_fpa']),'type' => 'c');
      $in_array['eidos_priceitem_total']=array('value' => gks_print_isset_n($eidos['product_price_final_peritem_total']),'type' => 'c');
      $in_array['eidos_priceall_start']=array('value' => gks_print_isset_n($eidos['product_price_start_all_net']),'type' => 'c');
      $in_array['eidos_priceall']=array('value' => gks_print_isset_n($eidos['product_price_final_all_net']),'type' => 'c');
      $in_array['eidos_fpa_amount_total']=array('value' => gks_print_isset_n($eidos['product_price_final_all_fpa']),'type' => 'c');
      $in_array['eidos_priceall_total']=array('value' => gks_print_isset_n($eidos['product_price_final_all_total']),'type' => 'c');


      $in_array['eidos_ejeresi_fpa']=array('value' => gks_print_isset_s($eidos['aade_katigoria_fpa_ejeresi_descr']),'type' => 's');
      $in_array['eidos_parakratisi_descr']=array('value' => gks_print_isset_s($eidos['aade_katigoria_parakratoumemenon_foron_descr']),'type' => 's');
      $in_array['eidos_parakratisi_poso']=array('value' => gks_print_isset_n($eidos['product_withheldAmount']),'type' => 'c');
      $in_array['eidos_loipoi_foroi_descr']=array('value' => gks_print_isset_s($eidos['aade_katigoria_loipon_foron_descr']),'type' => 's');
      $in_array['eidos_loipoi_foroi_poso']=array('value' => gks_print_isset_n($eidos['product_otherTaxesAmount']),'type' => 'c');
      $in_array['eidos_xartosimo_descr']=array('value' => gks_print_isset_s($eidos['aade_katigoria_xartosimou_descr']),'type' => 's');
      $in_array['eidos_xartosimo_poso']=array('value' => gks_print_isset_n($eidos['product_stampDutyAmount']),'type' => 'c');
      $in_array['eidos_teloi_descr']=array('value' => gks_print_isset_s($eidos['aade_katigoria_telon_descr']),'type' => 's');
      $in_array['eidos_teloi_poso']=array('value' => gks_print_isset_n($eidos['product_feesAmount']),'type' => 'c');
      $in_array['eidos_kratiseis_poso']=array('value' => gks_print_isset_n($eidos['product_deductionsAmount']),'type' => 'c');
      
      //eidika gia product list
      $in_array['eidos_product_photo_p']=array('value' => gks_print_isset_s($eidos['product_photo_p']),'type' => 's');
      $in_array['eidos_product_descr_p']=array('value' => gks_print_isset_s($eidos['product_descr_p']),'type' => 's');
      
      $in_array['eidos_product_price']=array('value' => gks_print_isset_n($eidos['product_price']),'type' => 'c');
      $in_array['eidos_product_price_sale']=array('value' => gks_print_isset_n($eidos['product_price_sale']),'type' => 'c');
      $in_array['eidos_product_price_retail']=array('value' => gks_print_isset_n($eidos['product_price_retail']),'type' => 'c');
      $in_array['eidos_product_price_retail_sale']=array('value' => gks_print_isset_n($eidos['product_price_retail_sale']),'type' => 'c');
      
      $in_array['eidos_quantitycheck_price']=array('value' => gks_print_isset_n($eidos['quantitycheck_price']),'type' => 'c');
      $in_array['eidos_product_price_calc']=array('value' => gks_print_isset_n($eidos['product_price_calc']),'type' => 'c');
      $in_array['eidos_product_price_include_vat']=array('value' => gks_print_isset_n($eidos['product_price_include_vat']),'type' => 'nl');
      $in_array['eidos_quantitycheck_price_retail']=array('value' => gks_print_isset_n($eidos['quantitycheck_price_retail']),'type' => 'c');
      $in_array['eidos_product_price_retail_calc']=array('value' => gks_print_isset_n($eidos['product_price_retail_calc']),'type' => 'c');
      $in_array['eidos_product_price_retail_include_vat']=array('value' => gks_print_isset_n($eidos['product_price_retail_include_vat']),'type' => 'nl');
      $in_array['eidos_fpa_base_descr']=array('value' => gks_print_isset_s($eidos['fpa_base_descr']),'type' => 's');
      $in_array['eidos_product_can_sell']=array('value' => gks_print_isset_n($eidos['product_can_sell']),'type' => 'nl');
      $in_array['eidos_product_can_buy']=array('value' => gks_print_isset_n($eidos['product_can_buy']),'type' => 'nl');
      

      if (isset($eidos['product_base_type'])) {
        $in_array['eidos_product_base_type_descr']=array('value' => gks_print_isset_s(gks_product_base_type_descr($eidos['product_base_type'])),'type' => 's');
      }
      
      $in_array['eidos_product_need_apostoli']=array('value' => gks_print_isset_n($eidos['product_need_apostoli']),'type' => 'nl');
      $in_array['eidos_product_varos']=array('value' => gks_print_isset_n($eidos['product_varos']),'type' => 'c');
      $in_array['eidos_product_ogos_x']=array('value' => gks_print_isset_n($eidos['product_ogos_x']),'type' => 'c');
      $in_array['eidos_product_ogos_y']=array('value' => gks_print_isset_n($eidos['product_ogos_y']),'type' => 'c');
      $in_array['eidos_product_ogos_z']=array('value' => gks_print_isset_n($eidos['product_ogos_z']),'type' => 'c');
      $in_array['eidos_product_is_digital']=array('value' => gks_print_isset_n($eidos['product_is_digital']),'type' => 'nl');
      $in_array['eidos_product_is_simple_download']=array('value' => gks_print_isset_n($eidos['product_is_simple_download']),'type' => 'nl');
      $in_array['eidos_product_need_multi_files']=array('value' => gks_print_isset_n($eidos['product_need_multi_files']),'type' => 'nl');
      $in_array['eidos_product_need_multi_files_min']=array('value' => gks_print_isset_n($eidos['product_need_multi_files_min']),'type' => 'nl');
      $in_array['eidos_product_need_multi_files_max']=array('value' => gks_print_isset_n($eidos['product_need_multi_files_max']),'type' => 'nl');
      $in_array['eidos_count_var']=array('value' => gks_print_isset_n($eidos['count_var']),'type' => 'nl');
      
      $in_array['eidos_terminal_ids']=array('value' => gks_print_isset_s($eidos['terminal_ids']),'type' => 's');
      
      
      
      //custom_eidos
      foreach ($eidos as $key => $value) {
        if (substr($key,0,7)=='custom_') {
          if (in_array($value['type'],[9])) {
            $in_array['eidos_'.$key]=array('value' => gks_print_isset_h($value['value']),'type' => 'h');
          } else {
            $in_array['eidos_'.$key]=array('value' => gks_print_isset_s($value['value']),'type' => 's');
          }
        }
      }
      //print '<pre>';print_r($in_array);die();
          
          
      //attribute
      if (isset($eidos['product_id'])) {
        foreach ($attribute_ids as $aid) {
          $aidv='';
          if (isset($attributes_data[$eidos['product_id']])) {
            if (isset($attributes_data[$eidos['product_id']]['idiotites'][$aid])) {
              $aidv=implode(',',$attributes_data[$eidos['product_id']]['idiotites'][$aid]['terms']);
            }
          }
          $in_array['eidos_attribute_'.$aid]=array('value' => gks_print_isset_s($aidv),'type' => 's');
        }
      }
      
      $lots_and_serials_analysis='';
      //if (isset($eidos['id_order_product'])==false) {
        //print '<pre>';print_r($row_eidoi);die();
      //}
      
      if (isset($products_lots_serials[$eidos['id_order_product']])) {
        $lots_and_serials_analysis=gks_print_form_lots_and_serials_analysis($lots_and_serials_analysis_html,$products_lots_serials[$eidos['id_order_product']]);
        
        //echo '<pre>g gggggggggg gggggggggg g';print $lots_and_serials_analysis;die();
      }
      
      $in_array['eidos_lots_and_serials_analysis']=array('value' => $lots_and_serials_analysis,'type' => 's');
     
     
      //reservation
      $in_array['room_descr']=array('value' => gks_print_isset_s($eidos['room_descr']),'type' => 's');
      $in_array['room_descr_en_US']=array('value' => gks_print_isset_s($eidos['room_descr_en_US']),'type' => 's');
      $in_array['room_photo']=array('value' => gks_print_isset_s($eidos['room_photo']),'type' => 's');
      $in_array['room_type_descr']=array('value' => gks_print_isset_s($eidos['room_type_descr']),'type' => 's');
      $in_array['room_type_descr_en_US']=array('value' => gks_print_isset_s($eidos['room_type_descr_en_US']),'type' => 's');
      $in_array['room_type_photo']=array('value' => gks_print_isset_s($eidos['room_type_photo']),'type' => 's');
      
      $in_array['room_adults']=array('value' => gks_print_isset_s($eidos['rnum_adults']),'type' => 'nl');
      $in_array['room_childs']=array('value' => gks_print_isset_s($eidos['rnum_childs']),'type' => 'nl');
      $in_array['room_visitors']=array('value' => gks_print_isset_s($eidos['rnum_visitors']),'type' => 'nl');
      $in_array['room_child_kounies']=array('value' => gks_print_isset_s($eidos['rnum_child_kounies']),'type' => 'nl');
      $in_array['room_extra_beds']=array('value' => gks_print_isset_s($eidos['rnum_extra_beds']),'type' => 'nl');
      
      
      //transfer 
       
      $in_array['oxima_type_photo']=array('value' => gks_print_isset_s($eidos['transfer_oxima_type_photo']),'type' => 's');
      $in_array['oxima_type_descr']=array('value' => gks_print_isset_s($eidos['transfer_oxima_type_descr']),'type' => 's');
      $in_array['oxima_photo']=array('value' => gks_print_isset_s($eidos['asset_photo']),'type' => 's');
      $in_array['oxima_descr']=array('value' => gks_print_isset_s($eidos['asset_title']),'type' => 's');
      $in_array['oxima_adults']=array('value' => gks_print_isset_s($eidos['rnum_adults']),'type' => 'nl');
      $in_array['oxima_childs']=array('value' => gks_print_isset_s($eidos['rnum_childs']),'type' => 'nl');
      $in_array['oxima_babys']=array('value' => gks_print_isset_s($eidos['rnum_babys']),'type' => 'nl');
      $in_array['oxima_epivates']=array('value' => gks_print_isset_s($eidos['rnum_epivates']),'type' => 'nl');
      
      
      $in_array['allfields_eidos_print_r']=array('value' => gks_print_isset_s(print_r($eidos,true)),'type' => 's');


      $temp=gks_print_form_replace_field($tr_m,$in_array);
      
      if ($temp!='') {
        gks_appendHTML($tbody, $temp);
      }
    }
  } 
  
  
  foreach ($tfoot_cut as $tfoot_tr) {
    gks_appendHTML($tbody, $tfoot_tr);
  }
  
  //echo '<pre>';print_r($products_lots_serials);print_r($eidos);die();

  

  $html_out= $dom->saveHTML($mytable);

  
  return $html_out;
}



function gks_print_form_lots_and_serials_analysis($html_in,$row_lots) {
  $html_in=trim_gks($html_in);
  if ($html_in == '') return '';
  if (count($row_lots)==0) return '';
  
  //echo '<pre>';print_r($row_lots);die();
  
  $dom = new DomDocument();
  if (@$dom->loadHTML('<?xml encoding="utf-8" data="fpa_analysis"?>'.$html_in) === false) {
    return 'ddddd'.$html_in;
  }
  $tables = $dom->getElementsByTagName('table');  
  if ($tables->length < 1) return $html_in;
  $mytable=$tables[0];
  $mytrs = $mytable->getElementsByTagName('tr');  
  
  
  $thead_index=-1;
  $tfoot_index=-1;
  $found_eidos=false;
  for ($i = 0; $i < $mytrs->length; $i++) {
    
    $text=$mytrs[$i]->textContent;
    $nodeName=$mytrs[$i]->parentNode->nodeName;
    if (is_string($nodeName)==false) $nodeName='agnosto';
    
    if (strpos($text, '{eidos_') !== false or $nodeName=='tbody') {
      $found_eidos=true;
    } else {
      if ($found_eidos==false or $nodeName=='thead') $thead_index=$i;
    }
    if (strpos($text, '{doc_') !== false or $nodeName=='tfoot') {
      if ($tfoot_index==-1) $tfoot_index=$i;
    }
    
  }

  if ($thead_index == -1) return $html_in; //den vrethike kefalida
  if ($found_eidos == false) return $html_in; //den vrethike tr me eidos
  
  
  $tfoot_cut=array();
  if ($tfoot_index>=0) {
    $nodeName=$mytrs[$tfoot_index]->parentNode->nodeName;
    if (is_string($nodeName)==false) $nodeName='agnosto';
    if ($nodeName!='tfoot') {

      for ($i = 0; $i < $mytrs->length; $i++) {
        if ($i >= $tfoot_index) {
          $tfoot_cut[] = $dom->saveHTML($mytrs[$i]);
        }
      }
      for ($i = $mytrs->length - 1; $i >=0; $i--) {
        if ($i >= $tfoot_index) {
          $temp = $mytrs[$i];
          $temp->parentNode->removeChild($temp);
        }
      } 
      $tfoot_index = -1;
    }
  }
  
  $tr_htmls=array();
  for ($i = 0; $i < $mytrs->length; $i++) {
    if ($i > $thead_index and ($i < $tfoot_index or $tfoot_index==-1)) {
      $tr_htmls[] = $dom->saveHTML($mytrs[$i]);
    }
  }
  //print '<pre>';print_r($tr_htmls);die();

  for ($i = $mytrs->length - 1; $i >=0; $i--) {
    if ($i > $thead_index and ($i < $tfoot_index or $tfoot_index==-1)) {
      $temp = $mytrs[$i];
      $temp->parentNode->removeChild($temp);
    }
  }  



  $tbody=$mytable->getElementsByTagName('tbody')[0];  
  if ($tbody === null) $tbody=$mytable;
  
  $tr_ma=array();
  foreach ($tr_htmls as $tr_html) {
    $mc=gks_print_form_mc($tr_html);
    $tr_hide=false;
    if (strpos($tr_html, '{hide}') !== false) {
      $tr_html=str_replace('{hide}', '', $tr_html);
      $tr_hide=true;
    }
    $tr_ma[]= array('html' => $tr_html, 'mc' => $mc, 'tr_hide'=> $tr_hide);
  }
  
  $aa=0;
  foreach ($row_lots as $lot_item) {
    $aa++;
    foreach ($tr_ma as $tr_m) {
      
      //s -> string
      //n -> number int
      //nl -> number myNumberFormatNo0Local
      //c -> Currency
        //cs -> Currency + symbol
      
      if (trim_gks($lot_item['lot_date_production'])!='') $lot_item['lot_date_production']=showDate(strtotime($lot_item['lot_date_production']),'d/m/Y',1);
      if (trim_gks($lot_item['lot_date_expire'])!='') $lot_item['lot_date_expire']=showDate(strtotime($lot_item['lot_date_expire']),'d/m/Y',1);
      
      
      $in_array=array();
      $in_array['lotserial_aa']=array('value' => $aa,'type' => 'n');
      $in_array['lotserial_name']=array('value' => $lot_item['lot_name'],'type' => 's');
      $in_array['lotserial_quantity']=array('value' => $lot_item['lot_product_quantity'],'type' => 'nl');
      $in_array['lotserial_production']=array('value' => $lot_item['lot_date_production'],'type' => 's');
      $in_array['lotserial_expire']=array('value' => $lot_item['lot_date_expire'],'type' => 's');

      
      $temp=gks_print_form_replace_field($tr_m,$in_array);
      
      if ($temp!='') {
        gks_appendHTML($tbody, $temp);
      }
    }
  } 
  
  
  foreach ($tfoot_cut as $tfoot_tr) {
    gks_appendHTML($tbody, $tfoot_tr);
  }
  
  if (count($row_lots)==1) {
    $tfoot=$mytable->getElementsByTagName('tfoot')[0];  
    if ($tfoot!=null) {
      $mytable->removeChild($tfoot);
    }
  }
  $html_out= $dom->saveHTML($mytable);
  
  //print '<pre>';
  //print $dom->saveHTML($mytable);

  $quantity_sum=0;
  
  foreach ($row_lots as $lot_item) {
    $quantity_sum+=$lot_item['lot_product_quantity'];
  }
  
  
  $in_array=array();
  $in_array['quantity_sum']=array('value' => nl2br_gks($quantity_sum),'type' => 'nl');
  
  
  
  $mc=gks_print_form_mc($html_out);
  $tr_m= array('html' => $html_out, 'mc' => $mc, 'tr_hide'=> false);
  //print_r($tr_m); die();
  $html_out=gks_print_form_replace_field($tr_m,$in_array);
    
  return $html_out;
}


function gks_print_form_fpa_analysis($html_in,$row_fpa) {
  $html_in=trim_gks($html_in);
  if ($html_in == '') return '';
  if (count($row_fpa)==0) return '';
  
  $dom = new DomDocument();
  if (@$dom->loadHTML('<?xml encoding="utf-8" data="fpa_analysis"?>'.$html_in) === false) {
    return 'ddddd'.$html_in;
  }
  $tables = $dom->getElementsByTagName('table');  
  if ($tables->length < 1) return $html_in;
  $mytable=$tables[0];
  $mytrs = $mytable->getElementsByTagName('tr');  
  
  
  $thead_index=-1;
  $tfoot_index=-1;
  $found_eidos=false;
  for ($i = 0; $i < $mytrs->length; $i++) {
    
    $text=$mytrs[$i]->textContent;
    $nodeName=$mytrs[$i]->parentNode->nodeName;
    if (is_string($nodeName)==false) $nodeName='agnosto';
    
    if (strpos($text, '{eidos_') !== false or $nodeName=='tbody') {
      $found_eidos=true;
    } else {
      if ($found_eidos==false or $nodeName=='thead') $thead_index=$i;
    }
    if (strpos($text, '{doc_') !== false or $nodeName=='tfoot') {
      if ($tfoot_index==-1) $tfoot_index=$i;
    }
    
  }

  if ($thead_index == -1) return $html_in; //den vrethike kefalida
  if ($found_eidos == false) return $html_in; //den vrethike tr me eidos
  
  
  $tfoot_cut=array();
  if ($tfoot_index>=0) {
    $nodeName=$mytrs[$tfoot_index]->parentNode->nodeName;
    if (is_string($nodeName)==false) $nodeName='agnosto';
    if ($nodeName!='tfoot') {

      for ($i = 0; $i < $mytrs->length; $i++) {
        if ($i >= $tfoot_index) {
          $tfoot_cut[] = $dom->saveHTML($mytrs[$i]);
        }
      }
      for ($i = $mytrs->length - 1; $i >=0; $i--) {
        if ($i >= $tfoot_index) {
          $temp = $mytrs[$i];
          $temp->parentNode->removeChild($temp);
        }
      } 
      $tfoot_index = -1;
    }
  }
  
  $tr_htmls=array();
  for ($i = 0; $i < $mytrs->length; $i++) {
    if ($i > $thead_index and ($i < $tfoot_index or $tfoot_index==-1)) {
      $tr_htmls[] = $dom->saveHTML($mytrs[$i]);
    }
  }
  //print '<pre>';print_r($tr_htmls);die();

  for ($i = $mytrs->length - 1; $i >=0; $i--) {
    if ($i > $thead_index and ($i < $tfoot_index or $tfoot_index==-1)) {
      $temp = $mytrs[$i];
      $temp->parentNode->removeChild($temp);
    }
  }  



  $tbody=$mytable->getElementsByTagName('tbody')[0];  
  if ($tbody === null) $tbody=$mytable;
  
  $tr_ma=array();
  foreach ($tr_htmls as $tr_html) {
    $mc=gks_print_form_mc($tr_html);
    $tr_hide=false;
    if (strpos($tr_html, '{hide}') !== false) {
      $tr_html=str_replace('{hide}', '', $tr_html);
      $tr_hide=true;
    }
    $tr_ma[]= array('html' => $tr_html, 'mc' => $mc, 'tr_hide'=> $tr_hide);
  }
  
  $aa=0;
  foreach ($row_fpa as $fpa_anas) {
    $aa++;
    foreach ($tr_ma as $tr_m) {
      
      //s -> string
      //n -> number int
      //nl -> number myNumberFormatNo0Local
      //c -> Currency
        //cs -> Currency + symbol
      
      $in_array=array();
      $in_array['fpa_aa']=array('value' => $aa,'type' => 'n');
      $in_array['fpa_pososto']=array('value' => $fpa_anas['pososto']*100,'type' => 'nl');
      $in_array['fpa_net']=array('value' => nl2br_gks($fpa_anas['net']),'type' => 'c');
      $in_array['fpa_fpa']=array('value' => nl2br_gks($fpa_anas['fpa']),'type' => 'c');
      $in_array['fpa_total']=array('value' => nl2br_gks($fpa_anas['net'] + $fpa_anas['fpa']),'type' => 'c');

      
      $temp=gks_print_form_replace_field($tr_m,$in_array);
      
      if ($temp!='') {
        gks_appendHTML($tbody, $temp);
      }
    }
  } 
  
  
  foreach ($tfoot_cut as $tfoot_tr) {
    gks_appendHTML($tbody, $tfoot_tr);
  }
  
  if (count($row_fpa)==1) {
    $tfoot=$mytable->getElementsByTagName('tfoot')[0];  
    if ($tfoot!=null) {
      $mytable->removeChild($tfoot);
    }
  }
  $html_out= $dom->saveHTML($mytable);
  
  //print '<pre>';
  //print $dom->saveHTML($mytable);

  $fpa_sum_net=0;
  $fpa_sum_fpa=0;
  $fpa_sum_total=0;
  
  foreach ($row_fpa as $fpa_anas) {
    $fpa_sum_net+=$fpa_anas['net'];
    $fpa_sum_fpa+=$fpa_anas['fpa'];
    $fpa_sum_total+=$fpa_anas['net']+$fpa_anas['fpa'];
  }
  
  
  $in_array=array();
  $in_array['fpa_sum_net']=array('value' => nl2br_gks($fpa_sum_net),'type' => 'c');
  $in_array['fpa_sum_fpa']=array('value' => nl2br_gks($fpa_sum_fpa),'type' => 'c');
  $in_array['fpa_sum_total']=array('value' => nl2br_gks($fpa_sum_total),'type' => 'c');
  
  $mc=gks_print_form_mc($html_out);
  $tr_m= array('html' => $html_out, 'mc' => $mc, 'tr_hide'=> false);
  //print_r($tr_m); die();
  $html_out=gks_print_form_replace_field($tr_m,$in_array);
    
  return $html_out;
}

function gks_print_form_foroi_analysis($html_in,$row_foroi) {
  $html_in=trim_gks($html_in);
  if ($html_in == '') return '';
  if (count($row_foroi)==0) return '';
  
  $dom = new DomDocument();
  if (@$dom->loadHTML('<?xml encoding="utf-8" data="foroi_analysis"?>'.$html_in) === false) {
    return 'ddddd'.$html_in;
  }
  $tables = $dom->getElementsByTagName('table');  
  if ($tables->length < 1) return $html_in;
  $mytable=$tables[0];
  $mytrs = $mytable->getElementsByTagName('tr');  
  
  
  $thead_index=-1;
  $tfoot_index=-1;
  $found_eidos=false;
  for ($i = 0; $i < $mytrs->length; $i++) {
    
    $text=$mytrs[$i]->textContent;
    $nodeName=$mytrs[$i]->parentNode->nodeName;
    if (is_string($nodeName)==false) $nodeName='agnosto';
    
    if (strpos($text, '{eidos_') !== false or $nodeName=='tbody') {
      $found_eidos=true;
    } else {
      if ($found_eidos==false or $nodeName=='thead') $thead_index=$i;
    }
    if (strpos($text, '{doc_') !== false or $nodeName=='tfoot') {
      if ($tfoot_index==-1) $tfoot_index=$i;
    }
    
  }

  if ($thead_index == -1) return $html_in; //den vrethike kefalida
  if ($found_eidos == false) return $html_in; //den vrethike tr me eidos
  
  
  $tfoot_cut=array();
  if ($tfoot_index>=0) {
    $nodeName=$mytrs[$tfoot_index]->parentNode->nodeName;
    if (is_string($nodeName)==false) $nodeName='agnosto';
    if ($nodeName!='tfoot') {

      for ($i = 0; $i < $mytrs->length; $i++) {
        if ($i >= $tfoot_index) {
          $tfoot_cut[] = $dom->saveHTML($mytrs[$i]);
        }
      }
      for ($i = $mytrs->length - 1; $i >=0; $i--) {
        if ($i >= $tfoot_index) {
          $temp = $mytrs[$i];
          $temp->parentNode->removeChild($temp);
        }
      } 
      $tfoot_index = -1;
    }
  }
  
  $tr_htmls=array();
  for ($i = 0; $i < $mytrs->length; $i++) {
    if ($i > $thead_index and ($i < $tfoot_index or $tfoot_index==-1)) {
      $tr_htmls[] = $dom->saveHTML($mytrs[$i]);
    }
  }
  //print '<pre>';print_r($tr_htmls);die();

  for ($i = $mytrs->length - 1; $i >=0; $i--) {
    if ($i > $thead_index and ($i < $tfoot_index or $tfoot_index==-1)) {
      $temp = $mytrs[$i];
      $temp->parentNode->removeChild($temp);
    }
  }  



  $tbody=$mytable->getElementsByTagName('tbody')[0];  
  if ($tbody === null) $tbody=$mytable;
  
  $tr_ma=array();
  foreach ($tr_htmls as $tr_html) {
    $mc=gks_print_form_mc($tr_html);
    $tr_hide=false;
    if (strpos($tr_html, '{hide}') !== false) {
      $tr_html=str_replace('{hide}', '', $tr_html);
      $tr_hide=true;
    }
    $tr_ma[]= array('html' => $tr_html, 'mc' => $mc, 'tr_hide'=> $tr_hide);
  }
  
  $aa=0;
  foreach ($row_foroi as $foroi_anas) {
    $aa++;
    foreach ($tr_ma as $tr_m) {
      
      //s -> string
      //n -> number int
      //nl -> number myNumberFormatNo0Local
      //c -> Currency
        //cs -> Currency + symbol
      
      $in_array=array();
      $in_array['foroi_aa']=array('value' => $aa,'type' => 'n');
      $in_array['foroi_descr']=array('value' => $foroi_anas['descr'],'type' => 's');
      $in_array['foroi_net']=array('value' => nl2br_gks($foroi_anas['net']),'type' => 'c');
      $in_array['foroi_foros']=array('value' => nl2br_gks($foroi_anas['foros']),'type' => 'c');
      $in_array['foroi_total']=array('value' => nl2br_gks($foroi_anas['net'] + $foroi_anas['foros']),'type' => 'c');

      
      $temp=gks_print_form_replace_field($tr_m,$in_array);
      
      if ($temp!='') {
        gks_appendHTML($tbody, $temp);
      }
    }
  } 
  

  
  foreach ($tfoot_cut as $tfoot_tr) {
    gks_appendHTML($tbody, $tfoot_tr);
  } 
  $html_out= $dom->saveHTML($mytable);
  
  


  $foroi_sum_net=0;
  $foroi_sum_foros=0;
  $foroi_sum_total=0;
  
  foreach ($row_foroi as $foroi_anas) {
    $foroi_sum_net+=$foroi_anas['net'];
    $foroi_sum_foros+=$foroi_anas['foros'];
    $foroi_sum_total+=$foroi_anas['net']+$foroi_anas['foros'];
  }
  
  
  $in_array=array();
  $in_array['foroi_sum_net']=array('value' => nl2br_gks($foroi_sum_net),'type' => 'c');
  $in_array['foroi_sum_foros']=array('value' => nl2br_gks($foroi_sum_foros),'type' => 'c');
  $in_array['foroi_sum_total']=array('value' => nl2br_gks($foroi_sum_total),'type' => 'c');
  
  $mc=gks_print_form_mc($html_out);
  $tr_m= array('html' => $html_out, 'mc' => $mc, 'tr_hide'=> false);
  //print_r($tr_m); die();
  $html_out=gks_print_form_replace_field($tr_m,$in_array);
    
  return $html_out;
}

function gks_print_form_replace_field($tr_m,$in_array) {
  $html_out_normal=$tr_m['html'];
  $html_out_empty =$tr_m['html'];
  
  //s -> string
  //n -> number int
  //nl -> number myNumberFormatNo0Local
  //c -> Currency
    //cs -> Currency + symbol
  //print '<pre>';print_r($in_array);
  
//  foreach ($in_array as $name => $value) {
//    if (isset($tr_m['mc'][$name])) {
//      for($index=0;$index < count($tr_m['mc'][$name]); $index++) {
        
  //print '<pre>';
  
  //print '<pre>';print_r();die();
  
  foreach ($tr_m['mc'] as $name => $mymc) {
    //print 'name: '.$name."\n";
    for($index=0;$index < count($tr_m['mc'][$name]); $index++) {
      
      //if (isset($in_array[$name])==false) {
      //  $in_array[$name]=array('value' => '','type' => 's');
      //}
      
      
      
      if (isset($in_array[$name])) {
        //print 'name: '.$name."\n";
        //print 'index: '.$index."\n";
        
//        print 'in_array';
//        print_r($in_array[$name]);
//        print 'in_array';
//        print_r($tr_m['mc'][$name][$index]);
        
        
        $value=$in_array[$name];
        
        
        $hide=false;
        if (     $value['type']=='s'  and in_array('empty', $tr_m['mc'][$name][$index]['hide']) and $value['value']=='') $hide=true;
        else if ($value['type']=='n'  and in_array('zero',  $tr_m['mc'][$name][$index]['hide']) and $value['value']==0)  $hide=true;
        else if ($value['type']=='nl' and in_array('zero',  $tr_m['mc'][$name][$index]['hide']) and $value['value']==0)  $hide=true;
        else if ($value['type']=='c'  and in_array('zero',  $tr_m['mc'][$name][$index]['hide']) and $value['value']==0)  $hide=true;
        
        if ($hide) {
          $new_value='';
        } else {
          $temp=$value['value'];
          
          if ($value['type']=='s') {
            if ($tr_m['mc'][$name][$index]['format']=='qrcode') {
              $temp=gks_qr_code_generate($value['value']);
            } else if (substr($tr_m['mc'][$name][$index]['format'],0,8)=='barcode,') {
              $tempbr=gks_barcode_generate(substr($tr_m['mc'][$name][$index]['format'], 8),$value['value']);
              if ($tempbr['url']!='') $temp=$tempbr['url'];
            } else {
              $temp=$value['value'];
            }
          } else if ($value['type']=='n') {
            $temp=$value['value'].'';
          } else if ($value['type']=='nl') {
            $temp=myNumberFormatNo0Local($value['value']);
          } else if ($value['type']=='c') {
            //var_dump($value['value']);
            if ($tr_m['mc'][$name][$index]['format'] == 'cs') {
              $temp=myCurrencyFormat($value['value'],true);
            } else {
              $temp=myCurrencyFormat($value['value'],false);
            }
          }
          
          //$episymbol=substr('%%%%%%%%%%%%%%%%', 0, $tr_m['mc'][$name][$index]['nl'] + 1);
          $episymbol='%%';
          //if (strpos($tr_m['mc'][$name][$index]['dest'], 'company_tk') !== false) {
            
            //print '<pre>';print_r($value);die();
            //print '<pre>'.$name."\n".$index."\n".$episymbol."\n"; print_r($tr_m['mc'][$name][$index]);die();
          //}
          
          $new_value=str_replace($episymbol,$temp, $tr_m['mc'][$name][$index]['dest']);
        }
        
        //print 'dest: '.$tr_m['mc'][$name][$index]['dest']."\n";
        
        
//        if ($name=='company_tk' or $name=='company_odos') {
//          print $name. ' 1 :';print_r($tr_m['mc'][$name][$index]); 
//        }
        $tr_m['mc'][$name][$index]['dest']= $new_value;
//        if ($name=='company_tk' or $name=='company_odos') {
//          print $name. ' 2 :';print_r($tr_m['mc'][$name][$index]); 
//        }
        
        
        //$tr_m['mc'][$name][$index]['dest']=str_replace($tr_m['mc'][$name][$index]['dest'], $new_value, $tr_m['mc'][$name][$index]['dest']);
        
        
        if ($tr_m['mc'][$name][$index]['nl']>1) { 
          //ean einai nested, na ginei allagi se ola ta ypoloipa
          foreach ($tr_m['mc'] as $name_nest => $mymc_nest) {
            //print 'name: '.$name."\n";
            for($index_nest=0;$index_nest < count($tr_m['mc'][$name_nest]); $index_nest++) {
              $tr_m['mc'][$name_nest][$index_nest]['dest']=str_replace($tr_m['mc'][$name][$index]['src'], $tr_m['mc'][$name][$index]['dest'], $tr_m['mc'][$name_nest][$index_nest]['dest']);
            }
          }
        }
        

        
        if ($tr_m['mc'][$name][$index]['nl']==1) {  
          //$html_out_normal=str_replace($tr_m['mc'][$name][$index]['src'], $new_value, $html_out_normal);
          $has_data='[[data]]';
          if ($tr_m['mc'][$name][$index]['dest']=='') $has_data='';
          
          $html_out_normal=str_replace($tr_m['mc'][$name][$index]['src'], $tr_m['mc'][$name][$index]['dest'].$has_data, $html_out_normal);
          $html_out_empty= str_replace($tr_m['mc'][$name][$index]['src'], '',         $html_out_empty);
        }
        
        //if ($name=='company_odos') {
          //print '<pre>'.$name."\n".$index."\n".$episymbol."\n"; print_r($tr_m['mc'][$name][$index]);die();
        //}
      }
    }
  }
  
  //print '<pre>'; print_r($tr_m['mc']);die();
  
  if ($tr_m['tr_hide'] and $html_out_normal == $html_out_empty) return '';
  
  return $html_out_normal;
}




function gks_appendHTML(DOMNode $parent, $source) {
  
    //echo '<pre>'.$source;die();
    libxml_use_internal_errors(true);
    libxml_clear_errors();
    $tmpDoc = new DOMDocument();
    $res = $tmpDoc->loadHTML('<?xml encoding="utf-8" data="append"?>'.$source);
    //echo '<pre>';var_dump($tmpDoc);echo '</pre>';
    $errors='';
    foreach (libxml_get_errors() as $error_item) {
      $errors.=print_r($error_item,true).'<br>';
    }
    libxml_clear_errors();
    if ($res===false or $errors!='') {
      debug_mail(false,'loadHTML error',$errors.'<br><br>'.htmlspecialchars($source, ENT_NOQUOTES)); 
      echo '<pre>loadHTML error: '."\n".$errors."\n".htmlspecialchars($source, ENT_NOQUOTES);die();
    }
    foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
        $node = $parent->ownerDocument->importNode($node, true);
        $parent->appendChild($node);
    }
}

function gks_print_form_trhide($html_in) {
  $html_in=trim_gks($html_in);
  if ($html_in == '') return '';

  $dom = new DomDocument();
  if (@$dom->loadHTML('<?xml encoding="utf-8" data="trhide"?>'.$html_in) === false) {
    return $html_in;
  }
  
  $mytrs = $dom->getElementsByTagName('tr');  
  for ($i = 0; $i < $mytrs->length; $i++) {
    $text=$mytrs[$i]->textContent;
    if (strpos($text, '{hide}') !== false) {    //exei trhide
      if (!(strpos($text, '[[data]]') !== false)) { //DEN exei data
        $mytrs[$i]->setAttribute('style', 'display:none;'); 

      }
    }
  }
  
  $mydivs = $dom->getElementsByTagName('div');  
  for ($i = 0; $i < $mydivs->length; $i++) {
    $text=$mydivs[$i]->textContent;
    if (strpos($text, '{hide}') !== false) {    //exei trhide
      if (!(strpos($text, '[[data]]') !== false)) { //DEN exei data
        $mydivs[$i]->setAttribute('style', 'display:none;'); 

      }
    }
  }  
  
  $temp=$dom->saveHTML($dom->getElementsByTagName('body')[0]);
  if (startwith($temp,'<body>') and endwith($temp,'</body>')) { // exei kai to body, na vgei
    $temp=substr($temp, 6, strlen($temp) - 13);
  }
  //echo $temp; die();
  
  $temp=str_replace('[[data]]', '', $temp);
  $temp=str_replace('{hide}', '', $temp);
  
  

  return $temp;
}

function gks_print_form_make_print($row_form,$row,$row_doc,$row_canceled_doc,$row_credit_doc,$row_eidoi,$row_fpa,$row_foroi,$row_company,$row_person,$options,$products_lots_serials,$row_eidoi_optional) {
  global $db_link;

  $ret=array('success' => false, 'message' => 'gks_print_form_make_print generic error');
  
  
  
  //echo '<pre>';echo $lots_and_serials_analysis; die();
  
  $form_header=$row_form['form_header'];
  $mc=gks_print_form_mc($form_header);
  $form_header=gks_print_form_replace_generic       ($mc,$row_form,         $form_header,$options);
  $form_header=gks_print_form_replace_company       ($mc,$row_company,      $form_header,$options,$row_form);
  $form_header=gks_print_form_replace_person        ($mc,$row_person,       $form_header,$options,$row_form);
  $form_header=gks_print_form_replace_doc           ($mc,$row_doc,          $form_header,$options,$row_form);
  $form_header=gks_print_form_replace_doc_canceled  ($mc,$row_canceled_doc, $form_header,$options,$row_form);
  $form_header=gks_print_form_replace_doc_credit    ($mc,$row_credit_doc,   $form_header,$options,$row_form);



  $form_footer=$row_form['form_footer'];
  $mc=gks_print_form_mc($form_footer);
  $form_footer=gks_print_form_replace_generic       ($mc,$row_form,         $form_footer,$options);
  $form_footer=gks_print_form_replace_company       ($mc,$row_company,      $form_footer,$options,$row_form);
  $form_footer=gks_print_form_replace_person        ($mc,$row_person,       $form_footer,$options,$row_form);
  $form_footer=gks_print_form_replace_doc           ($mc,$row_doc,          $form_footer,$options,$row_form);
  $form_footer=gks_print_form_replace_doc_canceled  ($mc,$row_canceled_doc, $form_footer,$options,$row_form);
  $form_footer=gks_print_form_replace_doc_credit    ($mc,$row_credit_doc,   $form_footer,$options,$row_form);

  $details_header=$row_form['details_header'];
  $mc=gks_print_form_mc($details_header);
  $details_header=gks_print_form_replace_generic      ($mc,$row_form,        $details_header,$options);
  $details_header=gks_print_form_replace_company      ($mc,$row_company,     $details_header,$options,$row_form);
  $details_header=gks_print_form_replace_person       ($mc,$row_person,      $details_header,$options,$row_form);
  $details_header=gks_print_form_replace_doc          ($mc,$row_doc,         $details_header,$options,$row_form);
  $details_header=gks_print_form_replace_doc_canceled ($mc,$row_canceled_doc,$details_header,$options,$row_form);
  $details_header=gks_print_form_replace_doc_credit   ($mc,$row_credit_doc,  $details_header,$options,$row_form);

  
    
  


  $details_body=$row_form['details_body'];
  //print '<pre>';print_r($row_form);die();
  $lots_and_serials_analysis=$row_form['lots_and_serials_analysis'];
  $details_body=gks_print_form_details_body($details_body,$row_eidoi,$lots_and_serials_analysis,$products_lots_serials);

  //echo '<pre>';var_dump($details_body);die();
  $eidoi_optional=$row_form['eidoi_optional'];
  $eidoi_optional=gks_print_form_details_body($eidoi_optional,$row_eidoi_optional,'',[]);
  if (count($row_eidoi_optional)==0) $eidoi_optional='';
  //echo '<pre>';var_dump($eidoi_optional);die();
  
  
  $mc=gks_print_form_mc($details_body);
  $details_body=gks_print_form_replace_generic      ($mc,$row_form,        $details_body,$options);
  $details_body=gks_print_form_replace_company      ($mc,$row_company,     $details_body,$options,$row_form);
  $details_body=gks_print_form_replace_person       ($mc,$row_person,      $details_body,$options,$row_form);
  $details_body=gks_print_form_replace_doc          ($mc,$row_doc,         $details_body,$options,$row_form);
  $details_body=gks_print_form_replace_doc_canceled ($mc,$row_canceled_doc,$details_body,$options,$row_form);
  $details_body=gks_print_form_replace_doc_credit   ($mc,$row_credit_doc,  $details_body,$options,$row_form);
  //echo '<pre>';var_dump($details_body);die();

  $mc=gks_print_form_mc($eidoi_optional);
  $eidoi_optional=gks_print_form_replace_generic      ($mc,$row_form,        $eidoi_optional,$options);
  $eidoi_optional=gks_print_form_replace_company      ($mc,$row_company,     $eidoi_optional,$options,$row_form);
  $eidoi_optional=gks_print_form_replace_person       ($mc,$row_person,      $eidoi_optional,$options,$row_form);
  $eidoi_optional=gks_print_form_replace_doc          ($mc,$row_doc,         $eidoi_optional,$options,$row_form);
  $eidoi_optional=gks_print_form_replace_doc_canceled ($mc,$row_canceled_doc,$eidoi_optional,$options,$row_form);
  $eidoi_optional=gks_print_form_replace_doc_credit   ($mc,$row_credit_doc,  $eidoi_optional,$options,$row_form);
  //echo '<pre>';var_dump($eidoi_optional);die();
  
  
  $details_footer=$row_form['details_footer'];
  $mc=gks_print_form_mc($details_footer);
  $details_footer=gks_print_form_replace_generic      ($mc,$row_form,        $details_footer,$options);
  $details_footer=gks_print_form_replace_company      ($mc,$row_company,     $details_footer,$options,$row_form);
  $details_footer=gks_print_form_replace_person       ($mc,$row_person,      $details_footer,$options,$row_form);
  $details_footer=gks_print_form_replace_doc          ($mc,$row_doc,         $details_footer,$options,$row_form);
  $details_footer=gks_print_form_replace_doc_canceled ($mc,$row_canceled_doc,$details_footer,$options,$row_form);
  $details_footer=gks_print_form_replace_doc_credit   ($mc,$row_credit_doc,  $details_footer,$options,$row_form);



  $fpa_analysis=$row_form['fpa_analysis'];
  $fpa_analysis=gks_print_form_fpa_analysis($fpa_analysis,$row_fpa);
  
  //$mc=gks_print_form_mc($fpa_analysis); den xreiazetai, ginetai mesa stin gks_print_form_fpa_analysis
  $fpa_analysis=gks_print_form_replace_generic      ($mc,$row_form,        $fpa_analysis,$options);
  $fpa_analysis=gks_print_form_replace_company      ($mc,$row_company,     $fpa_analysis,$options,$row_form);
  $fpa_analysis=gks_print_form_replace_person       ($mc,$row_person,      $fpa_analysis,$options,$row_form);
  $fpa_analysis=gks_print_form_replace_doc          ($mc,$row_doc,         $fpa_analysis,$options,$row_form);
  $fpa_analysis=gks_print_form_replace_doc_canceled ($mc,$row_canceled_doc,$fpa_analysis,$options,$row_form);
  $fpa_analysis=gks_print_form_replace_doc_credit   ($mc,$row_credit_doc,  $fpa_analysis,$options,$row_form);
  
  
  $form_header=str_replace('{fpa_analysis}', $fpa_analysis, $form_header);
  $details_header=str_replace('{fpa_analysis}', $fpa_analysis, $details_header);
  $details_body=str_replace('{fpa_analysis}', $fpa_analysis, $details_body);
  $details_footer=str_replace('{fpa_analysis}', $fpa_analysis, $details_footer);
  $form_footer=str_replace('{fpa_analysis}', $fpa_analysis, $form_footer);
  
  
  
  
  $foroi_analysis=$row_form['foroi_analysis'];
  $foroi_analysis=gks_print_form_foroi_analysis($foroi_analysis,$row_foroi);

  $foroi_analysis=gks_print_form_replace_generic      ($mc,$row_form,        $foroi_analysis,$options);
  $foroi_analysis=gks_print_form_replace_company      ($mc,$row_company,     $foroi_analysis,$options,$row_form);
  $foroi_analysis=gks_print_form_replace_person       ($mc,$row_person,      $foroi_analysis,$options,$row_form);
  $foroi_analysis=gks_print_form_replace_doc          ($mc,$row_doc,         $foroi_analysis,$options,$row_form);
  $foroi_analysis=gks_print_form_replace_doc_canceled ($mc,$row_canceled_doc,$foroi_analysis,$options,$row_form);
  $foroi_analysis=gks_print_form_replace_doc_credit   ($mc,$row_credit_doc,  $foroi_analysis,$options,$row_form);

  $form_header=str_replace('{foroi_analysis}', $foroi_analysis, $form_header);
  $details_header=str_replace('{foroi_analysis}', $foroi_analysis, $details_header);
  $details_body=str_replace('{foroi_analysis}', $foroi_analysis, $details_body);
  $details_footer=str_replace('{foroi_analysis}', $foroi_analysis, $details_footer);
  $form_footer=str_replace('{foroi_analysis}', $foroi_analysis, $form_footer);

  $form_header=str_replace('{eidoi_optional}', $eidoi_optional, $form_header);
  $details_header=str_replace('{eidoi_optional}', $eidoi_optional, $details_header);
  $details_body=str_replace('{eidoi_optional}', $eidoi_optional, $details_body);
  $details_footer=str_replace('{eidoi_optional}', $eidoi_optional, $details_footer);
  $form_footer=str_replace('{eidoi_optional}', $eidoi_optional, $form_footer);

  

  
  $myhtml=        gks_print_form_trhide($form_header.
                                        $details_header.
                                        $details_body.
                                        $details_footer.
                                        $form_footer);
  

  $custom_css=$row_form['custom_css'];
  $custom_javascript=$row_form['custom_javascript'];
  //echo '<pre>';print_r($custom_javascript);die();
  
  
  $script_subst_header='
    <script>
    function subst() {
        var vars = {};
        var query_strings_from_url = document.location.search.substring(1).split(\'&\');
        
        for (var query_string in query_strings_from_url) {
            if (query_strings_from_url.hasOwnProperty(query_string)) {
                var temp_var = query_strings_from_url[query_string].split(\'=\', 2);
                vars[temp_var[0]] = decodeURI(temp_var[1]);
            }
        }
        var css_selector_classes = [\'page\', \'frompage\', \'topage\', \'webpage\', \'section\', \'subsection\', \'date\', \'isodate\', \'time\', \'title\', \'doctitle\', \'sitepage\', \'sitepages\'];
        for (var css_class in css_selector_classes) {
            if (css_selector_classes.hasOwnProperty(css_class)) {
                var element = document.getElementsByClassName(css_selector_classes[css_class]);
                for (var j = 0; j < element.length; ++j) {
                    element[j].textContent = vars[css_selector_classes[css_class]];
                }
            }
        }

//        mybody = document.getElementsByTagName("body")[0];
//        myimg = document.createElement("img");
//        myimg.src = "https://test.easyfilesselection.com/my/img/print_backrounds/1.png";
//        myimg.style.opacity = 0.8;
//        //myimg.style.marginTop = "0%";
//        myimg.style.position="absolute";
//        myimg.style.top="0px";
//        myimg.style.width="100%";
//        myimg.style.zIndex ="-1";
//        mybody.appendChild(myimg); 
        
         
    }
    </script>
  ';
  

  $script_subst_footer='
    <script>
    function subst() {
        var vars = {};
        var query_strings_from_url = document.location.search.substring(1).split(\'&\');
        
        for (var query_string in query_strings_from_url) {
            if (query_strings_from_url.hasOwnProperty(query_string)) {
                var temp_var = query_strings_from_url[query_string].split(\'=\', 2);
                vars[temp_var[0]] = decodeURI(temp_var[1]);
            }
        }
        var css_selector_classes = [\'page\', \'frompage\', \'topage\', \'webpage\', \'section\', \'subsection\', \'date\', \'isodate\', \'time\', \'title\', \'doctitle\', \'sitepage\', \'sitepages\'];
        for (var css_class in css_selector_classes) {
            if (css_selector_classes.hasOwnProperty(css_class)) {
                var element = document.getElementsByClassName(css_selector_classes[css_class]);
                for (var j = 0; j < element.length; ++j) {
                    element[j].textContent = vars[css_selector_classes[css_class]];
                }
            }
        }

        
               
    }
    </script>
  ';

  
  $myout='<!DOCTYPE html>
  <head>
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
  	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  	<title>gks</title>
    <base href="'.GKS_SITE_URL.'">
    <link href="'.GKS_SITE_URL.'my/css/fontawesome-all.min.css" rel="stylesheet">
    <style type="text/css">
    '.$custom_css.'
    </style>
    <style type="text/css">
    ';
  //size: A4 portrait;

  if ($row_form['file_type']=='html' or $row_form['file_type']=='jpg') {  
    $myout.='
      
      body {
        margin-left: '. intval($row_form['margin_cm_left']*10).'mm;
        margin-right:'. intval($row_form['margin_cm_right']*10).'mm;
        margin-top: '.  intval($row_form['margin_cm_top']*10).'mm;
        margin-bottom:'.intval($row_form['margin_cm_bottom']*10).'mm;
        ';
        
//    if ($row_form['zoom'] !=1) {
//    $myout.='
//        transform: scale('.$row_form['zoom'].');
//      ';
//      
//    }        
        
    $myout.='
      }
      
      @media print {
        body {
          margin-left:  0mm;
          margin-right: 0mm;
          margin-top:   0mm;
          margin-bottom:0mm;
        }
      
        @page {
          margin-left: '. intval($row_form['margin_cm_left']*10).'mm;
          margin-right:'. intval($row_form['margin_cm_right']*10).'mm;
          margin-top: '.  intval($row_form['margin_cm_top']*10).'mm;
          margin-bottom:'.intval($row_form['margin_cm_bottom']*10).'mm;
    ';
    
    //https://developer.mozilla.org/en-US/docs/Web/CSS/@page/size
    $page_name='';
    switch ($row_form['size_name']) {
      case 'A5':       
      case 'A4':       
      case 'A3':       
      case 'B5':       
      case 'B4':       
      case 'JIS-B5':       
      case 'JIS-B4':       
      case 'letter':       
      case 'legal':       
      case 'ledger':       
        $page_name=$row_form['size_name'];
        break;  
      default: 
        break;
    }
      
  
    

    if ($row_form['is_landscape']==0) $myout.='      size: '.$page_name.' portrait;';
    else $myout.='      size: '.$page_name.' landscape;';
        
    $myout.='
        }
      }
    ';
    if ($row_form['grayscale']!=0) {
    $myout.='
      html {
          -webkit-filter: grayscale(100%);
          -moz-filter: grayscale(100%);
          filter: grayscale(100%);
      }
      
      html .class-you-wanna-exlude {
         -webkit-filter: grayscale(0%);
         -moz-filter: grayscale(0%);
         filter: grayscale(0%);
      }    
      ';
    }
  }
  
  
    
    

    
//  $myout.='
//      body {
//
//  ';
//  if (1==2 and trim_gks($row_form['page_background_url'])!='') {
//  $myout.='
//      background-image: url(\''.$row_form['page_background_url'].'\');
//    	background-repeat: repeat-y; 
//    	background-position: center top;
//    	background-attachment: fixed;
//    	background-size: 100% ;    
//      fill-opacity: 0.26;
//  ';
//  }
//  
//   
//  $myout.='
//      }
//  ';
  if (trim_gks($row_form['page_background_url'])!='') {
    if ($row_form['page_background_opacity']>=1) {
      
  $myout.='
      body {
        background-image: url(\''.$row_form['page_background_url'].'\');
      	background-repeat: no-repeat; 
      	background-position: center top;
      	background-attachment: fixed;
      	background-size: 100% ;    
        opacity: '.number_format(floatval($row_form['page_background_opacity']),1,'.','').';
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        position: absolute;
        z-index: -1;  
      }
    ';
      
    } else {
  
  $myout.='
      body::after {
        content: "";
        background-image: url(\''.$row_form['page_background_url'].'\');
      	background-repeat: no-repeat; 
      	background-position: center top;
      	background-attachment: fixed;
      	background-size: 100% ;    
        opacity: '.number_format(floatval($row_form['page_background_opacity']),1,'.','').';
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        position: absolute;
        z-index: -1;  
      }
    ';

    }


  }

  
  //print '<pre>';
  //print $myout;
  //die();

  
  //table {page-break-inside: avoid;}
  $myout.='
      
      thead { display: table-header-group; }
      tfoot { display: table-row-group; }
      tr { page-break-inside: avoid; }  
      th { page-break-inside: avoid; }  
      
    </style>
  </head>
  <body style="margin:0px;padding:0px">'.
  '[[[[header]]]]';
    
//  if (trim_gks($row_form['page_background_url'])!='') {
//  $myout.='
//    <div id="gks_page_background_url" style="
//      
//      background: url(\''.$row_form['page_background_url'].'\');
//    	background-repeat: repeat-y; 
//    	background-position: center top;
//    	background-attachment: fixed;
//    	background-size: 100% 100%;    
//      opacity: '.number_format(floatval($row_form['page_background_opacity']),1,'.','').';
//      left: 0;
//      right: 0;
//      top: 0;
//      height:100%;
//      position: absolute;
//      z-index: -1;   
//    "></div>';
//  }  
  
  $myout.=$myhtml;
  $myout.='[[[[footer]]]]';
  
  if ($custom_javascript!='') $myout.='<script>'.$custom_javascript.'</script>';

  
  $myout.='</body></html>';
  
  
//  $fpdf = new FPDF();
//  $fpdf->AddPage();
//  $fpdf->Image('background-image.png', 0, 0, $fpdf->w, $fpdf->h);
//  $fpdf->Output();
    

  

  //echo $myout;die();
  
  $page_header=trim_gks($row_form['page_header']);
  if ($page_header!='') {
    $mc=gks_print_form_mc($page_header);
    $page_header=gks_print_form_replace_doc     ($mc,$row_doc,    $page_header,$options,$row_form);
    $page_header=gks_print_form_replace_generic ($mc,$row_form,   $page_header,$options);
    $page_header=gks_print_form_replace_company ($mc,$row_company,$page_header,$options,$row_form);
    $page_header=gks_print_form_replace_person  ($mc,$row_person, $page_header,$options,$row_form);
    
    $page_header=str_replace('{fpa_analysis}', $fpa_analysis, $page_header);
    $page_header=str_replace('{foroi_analysis}', $foroi_analysis, $page_header);
    $page_header=gks_print_form_trhide($page_header);
        
    if ($row_form['file_type']=='pdf') {
      $page_header= '<!DOCTYPE html><head>'.
                    '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">'.
                    '<meta name="viewport" content="width=device-width, initial-scale=1.0">'.
                    '<base href="'.GKS_SITE_URL.'">'.
                    '<link href="/my/css/fontawesome-all.min.css" rel="stylesheet">'.
                    '<style type="text/css">'.$custom_css.'</style>'.
                    $script_subst_header.
                    '</head>'.
                    '<body style="margin:0px;padding:0px" onload="subst()">'.
                    $page_header.
                    ($custom_javascript!='' ? '<script>'.$custom_javascript.'</script>' : '').
                    '</body></html>';
      //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/page_header_'.time().'.html',$page_header);
    }
  }
  //echo '<pre>'.htmlspecialchars($page_header);die();
  
  $page_footer=trim_gks($row_form['page_footer']);
  if ($page_footer!='') {
    $mc=gks_print_form_mc($page_footer);
    $page_footer=gks_print_form_replace_doc     ($mc,$row_doc,    $page_footer,$options,$row_form);
    $page_footer=gks_print_form_replace_generic ($mc,$row_form,   $page_footer,$options);
    $page_footer=gks_print_form_replace_company ($mc,$row_company,$page_footer,$options,$row_form);
    $page_footer=gks_print_form_replace_person  ($mc,$row_person, $page_footer,$options,$row_form);

    
    $page_footer=str_replace('{fpa_analysis}', $fpa_analysis, $page_footer);
    $page_footer=str_replace('{foroi_analysis}', $foroi_analysis, $page_footer);
    $page_footer=gks_print_form_trhide($page_footer);
    
    if ($row_form['file_type']=='pdf') {
      $page_footer= '<!DOCTYPE html><head>'.
                    '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">'.
                    '<meta name="viewport" content="width=device-width, initial-scale=1.0">'.
                    '<base href="'.GKS_SITE_URL.'">'.
                    '<link href="/my/css/fontawesome-all.min.css" rel="stylesheet">'.
                    '<style type="text/css">'.$custom_css.'</style>'.
                    $script_subst_footer.
                    '</head>'.
                    '<body style="margin:0px;padding:0px" onload="subst()">'.
                    $page_footer.
                    ($custom_javascript!='' ? '<script>'.$custom_javascript.'</script>' : '').
                    '</body></html>';
                    
      //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/page_footer_'.time().'.html',$page_footer);
    }
                  
  }

  //echo '<pre>';print_r($row_form);
  //echo $page_header;die();



  
  
  if (isset($options['fileserver']) and isset($options['folder']) and isset($options['filename']) and file_exists($options['fileserver'].$options['folder'])==false) {
    $ret['message']=str_replace('[1]',$options['folder'],gks_lang('Δεν βρέθηκε ο φάκελος <b>[1]</b> στον βασικό φάκελο αποθήκευσης (FileServer) του συστήματος εκτυπώσεων'));
    debug_mail(false,$ret['message'],'options: '.print_r($options,true)); 
    return $ret;}
  
  
  //echo $page_header;
  if ($row_form['file_type']=='html' or $row_form['file_type']=='jpg') {
    
    $page_header=str_replace('<span class="page"></span>','1',$page_header);
    $page_header=str_replace('<span class="topage"></span>','1',$page_header);
    
    $page_footer=str_replace('<span class="page"></span>','1',$page_footer);
    $page_footer=str_replace('<span class="topage"></span>','1',$page_footer);

    $mytop=0;
    $mytop=$row_form['margin_cm_top']*10*1.5; //to 1.5 einai aythereto
    $mybottom=0;
    $mybottom=$row_form['margin_cm_bottom']*10*1.5; //to 1.5 einai aythereto
    
    $mywidth=100;
    //if ($row_form['width_cm']!=0) {
    //  $mywidth=100*($row_form['width_cm']-$row_form['margin_cm_left']-$row_form['margin_cm_right'])/$row_form['width_cm'];
    //}
//    $out_html='';
//    $out_html='<table cellspacing=0 cellpadding=0 style="'.
//    'width:'.number_format($mywidth,2,'.','').'%;'.
//    //'margin-top:'.number_format($mytop,2,'.','').'px;'.
//    //'margin-bottom:'.number_format($mybottom,2,'.','').'px;'.
//    'border-collapse:collapse;" border="0" align="center">'.
//      '<tr><td>'.
//      $page_header.$myout.$page_footer.
//      '</td></tr>'.
//      '</table>';
    
    $myout=str_replace('[[[[header]]]]', $page_header, $myout);
    //echo $myout; die();

    $myout=str_replace('[[[[footer]]]]', $page_footer, $myout);
    

    $save_basename='gks_doc.html';
    $path_file='';
    
    if (isset($options['fileserver']) and isset($options['folder']) and isset($options['filename'])) {
      $save_basename=$options['filename'].'.html';
      do {
        $path_file=$options['fileserver'].$options['folder'].$save_basename;
        if (file_exists($path_file) == false) break;
        $save_basename=substr($options['filename'], 0, strlen($options['filename'])-3).rand(100,999).'.html';
      } while(true);
      
      $write_bytes=file_put_contents($path_file,$myout);
      if ($write_bytes === false) {
        $ret['message']=str_replace('[1]',$save_basename,gks_lang('Δεν είναι δυνατή η δημιουργία του αρχείου [1] στο σύστημα εκτυπώσεων'));
        debug_mail(false,$ret['message'],'options: '.print_r($options,true)); 
        return $ret;}
      
      if ($row_form['file_type']=='html') {
        
        $ret['success']=true;
        $ret['message']='ok';
        $ret['save_basename']=$save_basename;
        $ret['path_file']=$path_file;
        $ret['path_relative']=$options['folder'].$save_basename;
        $ret['url_file']='/my/admin-get-file.php?fs=fileservers&file='.rawurlencode($ret['path_relative']);
        if (isset($options['override']['is_preview']) and $options['override']['is_preview']!=0) {
          $ret['url_file']='/my/admin-get-file.php?fs=tmp&file='.rawurlencode($ret['path_relative']);
        }        
        return $ret;
      }
      
      if ($row_form['file_type']=='jpg') {
        $save_basename_jpg=substr($save_basename, 0, strlen($save_basename)-5).'.jpg';
        $path_file_jpg=substr($path_file, 0, strlen($path_file)-5).'.jpg';
        
/*
      --crop-h <int>                  Set height for cropping
      --crop-w <int>                  Set width for cropping
      --crop-x <int>                  Set x coordinate for cropping
      --crop-y <int>                  Set y coordinate for cropping
  -H, --extended-help                 Display more extensive help, detailing
                                      less common command switches
  -f, --format <format>               Output file format
      --height <int>                  Set screen height (default is calculated
                                      from page content) (default 0)
  -h, --help                          Display help
      --license                       Output license information and exit
      --log-level <level>             Set log level to: none, error, warn or
                                      info (default info)
      --quality <int>                 Output image quality (between 0 and 100)
                                      (default 94)
  -q, --quiet                         Be less verbose, maintained for backwards
                                      compatibility; Same as using --log-level
                                      none
  -V, --version                       Output version information and exit
      --width <int>                   Set screen width, note that this is used
                                      only as a guide line. Use
                                      --disable-smart-width to make it strict.
                                      (default 1024)


*/        
        $options_image=array();
        $options_image['zoom']=1;
        if ($row_form['zoom'] >= 0.1 and $row_form['zoom'] <= 2) $options_image['zoom']=$row_form['zoom'];
        //$options_image['dpi']=$row_form['dpi'];
        $options_image['zoom']=3*$options_image['zoom'];
        $options_image['width']=($row_form['is_landscape']==0 ? intval(3*1024*$row_form['width_cm']/21) : intval(3*1448*$row_form['width_cm']/29.7)); //apla noumera othonis
        //$options_image['width']=intval(($row_form['width_cm']/2.54)*$row_form['dpi']);
        //$options_image[]='disable-smart-width';
        $options_image['load-error-handling']='ignore';       
        $options_image['format']='jpg';       
        $options_image['log-level'] ='info';                 //Set log level to: none, error, warn or info (default info)
    
    
        if (GKS_PDF_GENERATOR!='') {

          $senddata=array(
            'options'=> $options_image,
            'myout' => '',
            'file_type' => 'jpg',
            'html' => file_get_contents($path_file),
          );
          
          // to read the ginei me 'php://input'
          $data_string = json_encode($senddata);
          
          if (GKS_DEBUG) $data_string=str_replace('test.easyfilesselection.com', 'www.gks.gr', $data_string);
           
          
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, GKS_PDF_GENERATOR);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
          if (defined('CURLOPT_SSL_VERIFYSTATUS')) curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);
          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); //seconds 
          curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
          curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
          //curl_setopt($ch, CURLOPT_POST,1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
          //curl_setopt($ch, CURLOPT_HEADER, true);
          curl_setopt($ch, CURLOPT_HTTPHEADER,
              array(
                  'accept: application/json',
                  'Content-Type: application/json',
                  //'Content-Type: application/x-www-form-urlencoded',
                  'Content-Length: ' . strlen($data_string)
              )
          ); 
          //curl_setopt($ch, CURLOPT_TIMEOUT_MS, 100); //HERE MAGIC (We wait only 1ms on connection) Script waiting but (processing of send package to $curl is continue up to successful) so after 1ms we continue scripting and in background php continue already package to destiny. This is like apple on tree, we cut and go, but apple still fallow to destiny but we don't care what happened when fall down :) 
          //curl_setopt($ch, CURLOPT_NOSIGNAL, 1); // i'dont know just it works together read manual ;)
          
          
          $remote_result=curl_exec($ch);
          $gks_curl_errno=curl_errno($ch);
          $gks_curl_info = curl_getinfo($ch);
          curl_close ($ch); 
                
          $remote_result=json_decode($remote_result, true);
          if (is_array($remote_result)==false or 
              isset($remote_result['success'])==false or 
              isset($remote_result['message'])==false or 
              isset($remote_result['url'])==false or 
              $remote_result['success']==false) {
                
            $ret['success']=false;
            if (isset($remote_result['message'])) $ret['message']=base64_decode($remote_result['message']);
            $ret['save_basename']='';
            $ret['path_file']='';
            $ret['path_relative']='';
            $ret['url_file']='/my/admin-get-file.php?fs=fileservers&file=';
            return $ret;
          }
                          
          file_put_contents($path_file_jpg,file_get_contents($remote_result['url']));
          
        } else {
          //echo '<pre>';print_r( $options_image);die();
          $html_image = new mikehaertl\wkhtmlto\Image($path_file);
          //$html_image->type='jpg';
          $html_image->setOptions($options_image);
          $run_image= $html_image->saveAs($path_file_jpg);
          $mycmd_image=$html_image->getCommand();
          $errors_image=$html_image->getError();
          
          if ($run_image == false) {
            $ret['message']=gks_lang('Σφάλμα κατά την δημιουργία του αρχείου jpg').'<br>'.$errors_image;
            debug_mail(false,$ret['message'],$mycmd_image."\n".$errors_image."\n".print_r($options,true)); 
            return $ret;}   
        
        }
              
        if ($row_form['grayscale']!=0) {
          $im = imagecreatefromjpeg($path_file_jpg);
          if (!($im && imagefilter($im, IMG_FILTER_GRAYSCALE))) {
            imagedestroy($im);
            $ret['message']=gks_lang('Σφάλμα κατά την μετατροπή της φωτογραφίας σε αποχρώσεις του γκρι').'<br>'.$errors_image;
            debug_mail(false,$ret['message'],$mycmd_image."\n".$errors_image."\n".print_r($options,true)); 
            return $ret;}  
                       
          imagejpeg($im,$path_file_jpg);
          imagedestroy($im);
        }
          
        $ret['success']=true;
        $ret['message']='ok';
        $ret['save_basename']=$save_basename_jpg;
        $ret['path_file']=$path_file_jpg;
        $ret['path_relative']=$options['folder'].$save_basename_jpg;
        $ret['url_file']='/my/admin-get-file.php?fs=fileservers&file='.rawurlencode($ret['path_relative']);
        if (isset($options['override']['is_preview']) and $options['override']['is_preview']!=0) {
          $ret['url_file']='/my/admin-get-file.php?fs=tmp&file='.rawurlencode($ret['path_relative']);
        }
        return $ret;
      }
      echo 'print_error'; die();
    }
    echo $myout; die();
    //echo '<pre>'; print_r($ret); die();
  }

  //print '<pre>'; print_r($options); die();

  if ($row_form['file_type']=='pdf') {
  
    $myout=str_replace('[[[[header]]]]', '', $myout);
    $myout=str_replace('[[[[footer]]]]', '', $myout);
      
    //http://wkhtmltopdf.org/usage/wkhtmltopdf.txt
    $options_pdf = array();
    $options_pdf['zoom']=1;
    if ($row_form['zoom'] >= 0.1 and $row_form['zoom'] <= 2) $options_pdf['zoom']=$row_form['zoom'];
    //$options_pdf[]                = 'print-media-type';
    //$options_pdf[]                = 'enable-local-file-access'; // Allowed conversion of a local file to read in other local files.
    //$options_pdf['allow'] ='.';                         //Allow the file or files from the specified folder to be loaded (repeatable)
    if ($row_form['grayscale']!=0) $options_pdf[] = 'grayscale'; //PDF will be generated in grayscale
    $options_pdf['log-level'] ='info';                 //Set log level to: none, error, warn or info (default info)
    $options_pdf['load-error-handling']='ignore';       //Specify how to handle pages that fail to load: abort, ignore or skip (default abort)
    $options_pdf['load-media-error-handling']='ignore'; //Specify how to handle media files that fail to load: abort, ignore or skip (default ignore)
    $options_pdf['encoding']='UTF-8';
    $options_pdf['page-width']    = intval($row_form['width_cm']*10);   // in mm, panta to width < height, diladi to default na einai panta Portrait
    $options_pdf['page-height']   = intval($row_form['height_cm']*10);   // in mm
    $options_pdf['dpi']           = intval($row_form['dpi']);
    $options_pdf[]                = 'no-outline';    // Make Chrome not complain
    $options_pdf['margin-left']   = intval($row_form['margin_cm_left']*10);   // in mm
    $options_pdf['margin-right']  = intval($row_form['margin_cm_right']*10); // in mm
    //$options_pdf['margin-top']    = intval($row_form['margin_cm_top']*10*($page_header=='' ? 1 : $options_pdf['zoom']));   // in mm
    //$options_pdf['margin-bottom'] = intval($row_form['margin_cm_bottom']*10*($page_footer=='' ? 1 : $options_pdf['zoom'])); // in mm
    $options_pdf['margin-top']    = intval($row_form['margin_cm_top']*10);   // in mm
    $options_pdf['margin-bottom'] = intval($row_form['margin_cm_bottom']*10); // in mm
    $options_pdf['orientation']   = ($row_form['is_landscape']==0 ? 'Portrait' : 'Landscape');  //Landscape or Portrait
  
    $options_pdf['title']='gks pdf document | www.gks.gr';
    if ($page_header!='') $options_pdf['header-html']=$page_header;
    if ($page_footer!='') $options_pdf['footer-html']=$page_footer;
    //$options_pdf[]                = 'header-line';
    //$options_pdf[]                = 'footer-line';
    //print '<pre>';print_r($options_pdf);die();
  
    //$options_pdf['javascript-delay']=3000;
  
  //  $options_pdf = array(
  //      
  //      'header-line',
  //      'header-right' => '[page]/[toPage]',
  //      //'header-left' => showDate(time(), 'd/m/Y H:i:s', 1),
  //      //'header-font-name ' => 'Arial',
  //      //'header-font-size' => '7',
  //      'header-center' => $GKS_SITE_HUMAN_NAME,
  //      
  //      'footer-line',
  //      'footer-font-size' => '20',
  //      'footer-font-name ' => 'Arial',
  //      'footer-right' => $GKS_SITE_HUMAN_NAME,
  //      'footer-html' =>'<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body style="font-size:8pt;font-family:Arial;"><div style="color:#888888;">ddddddd</div></body></html>',
  //
  //      'encoding' => 'UTF-8',  // option with argument
  //      'page-size' => 'A4',
  //      'page-width'    => intval($row_form['width_cm']*10),   // in mm, panta to width < height, diladi to default na einai panta Portrait
  //      'page-height'   => intval($row_form['height_cm']*10),   // in mm
  //      'dpi'           => intval($row_form['dpi']),
  //      'no-outline',         // Make Chrome not complain
  //      'margin-left'   => intval($row_form['margin_cm_left']*10),   // in mm
  //      'margin-right'  => intval($row_form['margin_cm_right']*10), // in mm
  //      'margin-top'    => intval($row_form['margin_cm_top']*10),   // in mm
  //      'margin-bottom' => intval($row_form['margin_cm_bottom']*10), // in mm
  //      'orientation'   => ($row_form['is_landscape']==0 ? 'Portrait' : 'Landscape'),  //Landscape or Portrait
  //  //
  //  //    // Default page options
  //  //    'disable-smart-shrinking',
  //  //    'user-style-sheet' => '/path/to/pdf.css', 
  //  
  //  //    'binary' => '/obscure/path/to/wkhtmltopdf',
  //  //    'ignoreWarnings' => true,
  //  //    'commandOptions' => array(
  //  //        'useExec' => true,      // Can help if generation fails without a useful error message
  //  //        'procEnv' => array(
  //  //            // Check the output of 'locale' on your system to find supported languages
  //  //            'LANG' => 'en_US.utf-8',
  //  //        ),
  //  //    ),
  //  //
  //  //    // Option with 2 arguments
  //  //    'cookie' => array('name'=>'value'),
  //  //
  //  //    // Repeatable options with single argument
  //  //    'run-script' => array(
  //  //        '/path/to/local1.js',
  //  //        '/path/to/local2.js',
  //  //    ),
  //  //
  //  //    // Repeatable options with 2 arguments
  //  //    'replace' => array(
  //  //        '{page}' => $page++,
  //  //        '{title}' => $pageTitle,
  //  //    ),
  //  
  //  );  
  //  
  //  print '<pre>';print_r($options_pdf);die();
  
    
    $save_basename='gks_doc.pdf';
    $path_file='';
    
    if (isset($options['fileserver']) and isset($options['folder']) and isset($options['filename'])) {
      $save_basename=$options['filename'].'.pdf';
      do {
        $path_file=$options['fileserver'].$options['folder'].$save_basename;
        if (file_exists($path_file) == false) break;
        $save_basename=substr($options['filename'], 0, strlen($options['filename'])-3).rand(100,999).'.pdf';
      } while(true);
    }

    //debug_mail(false,'print',htmlspecialchars(print_r($myout,true)));
    //debug_mail(false,'print options_pdf',htmlspecialchars(print_r($options_pdf,true)));
    
    //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/print_options_pdf_'.time().'.html',print_r($options_pdf,true));
    //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/print_myout_'.time().'.html',print_r($myout,true));
    
    
    if (GKS_PDF_GENERATOR!='') { 
      //remote p.x. https://tools.gks.gr/remote_pdf_generator/create.php
      
      $senddata=array(
        'options'=> $options_pdf,
        'myout' => $myout,
        'file_type' => 'pdf',
      );
      
      // to read the ginei me 'php://input'
      $data_string = json_encode($senddata);
      
      if (GKS_DEBUG) $data_string=str_replace('test.easyfilesselection.com', 'www.gks.gr', $data_string);
       
      
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, GKS_PDF_GENERATOR);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      if (defined('CURLOPT_SSL_VERIFYSTATUS')) curl_setopt($ch, CURLOPT_SSL_VERIFYSTATUS, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); //seconds 
      curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      //curl_setopt($ch, CURLOPT_POST,1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
      //curl_setopt($ch, CURLOPT_HEADER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER,
          array(
              'accept: application/json',
              'Content-Type: application/json',
              //'Content-Type: application/x-www-form-urlencoded',
              'Content-Length: ' . strlen($data_string)
          )
      ); 
      //curl_setopt($ch, CURLOPT_TIMEOUT_MS, 100); //HERE MAGIC (We wait only 1ms on connection) Script waiting but (processing of send package to $curl is continue up to successful) so after 1ms we continue scripting and in background php continue already package to destiny. This is like apple on tree, we cut and go, but apple still fallow to destiny but we don't care what happened when fall down :) 
      //curl_setopt($ch, CURLOPT_NOSIGNAL, 1); // i'dont know just it works together read manual ;)
      
      
      $remote_result=curl_exec($ch);
      $gks_curl_errno=curl_errno($ch);
      $gks_curl_info = curl_getinfo($ch);
      curl_close ($ch); 
       
      $ret['success']=false;
      $ret['message']=$remote_result;
      
      
      $remote_result=json_decode($remote_result, true);
      if (is_array($remote_result)==false or 
          isset($remote_result['success'])==false or 
          isset($remote_result['message'])==false or 
          isset($remote_result['url'])==false or 
          $remote_result['success']==false) {
            
        $ret['success']=false;
        if (isset($remote_result['message'])) $ret['message']=base64_decode($remote_result['message']);
        $ret['save_basename']='';
        $ret['path_file']='';
        $ret['path_relative']='';
        $ret['url_file']='/my/admin-get-file.php?fs=fileservers&file=';
        return $ret;
      }
      
      if (isset($options['fileserver']) and isset($options['folder']) and isset($options['filename'])) {
        file_put_contents($path_file,file_get_contents($remote_result['url']));
      } else {
        $ret['success']=false; 
        $ret['message']='poli perirgi katastasi';
        return $ret;
      }
      //echo '<pre>hhhhhhhhhhhhhhhhhhhhhhhh';die();
          
      
    } else {
      
      
      
    	ini_set('display_errors', 'on');
    	ini_set('display_startup_errors', 'on');
    	ini_set('log_errors',1);
    		
      $pdf = new mikehaertl\wkhtmlto\Pdf;
      
      
      $pdf->setOptions($options_pdf);
      $pdf->addPage($myout);
     
      $save_basename='gks_doc.pdf';
      $path_file='';


      
      if (isset($options['fileserver']) and isset($options['folder']) and isset($options['filename'])) {

        $save_basename=$options['filename'].'.pdf';
        do {
          $path_file=$options['fileserver'].$options['folder'].$save_basename;
          if (file_exists($path_file) == false) break;
          $save_basename=substr($options['filename'], 0, strlen($options['filename'])-3).rand(100,999).'.pdf';
        } while(true);
                
        $run_pdf = $pdf->saveAs($path_file);
      } else {
        $run_pdf = $pdf->send($save_basename,true);
      }
    
    
      $mycmd=$pdf->getCommand();
      $errors=$pdf->getError();
      if ($run_pdf == false) {
        $ret['message']=gks_lang('Σφάλμα κατά την δημιουργία του αρχείου PDF').'<br>'.$errors;
        debug_mail(false,$ret['message'],$mycmd."\n".$errors."\n".print_r($options,true)); 
        return $ret;}
    }
    
    
    if ($path_file!='' and file_exists($path_file)) {
      @chmod($myoutfile,0666);
    }


   
    if (isset($options['override']) and 
        isset($options['override']['is_preview']) and 
        $options['override']['is_preview']==1 and 
        isset($options['override']['createthump']) and 
        $options['override']['createthump']==1) {
      
//      print '<pre>'; print $path_file; die();
      
      //print '<pre>';print_r($options);die();
      $imagick = new imagick();
      $imagick->setResolution(300, 300);
      $imagick->readImage($path_file);
      
      
      $imagick->setImageBackgroundColor('white');
      $imagick->setImageAlphaChannel(11); // Imagick::ALPHACHANNEL_REMOVE
      $imagick->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
      
      
      $imagick->thumbnailImage(500,500,true);
      
      $imagick->setImageFormat("jpg");
      $imagick->setImageCompression(imagick::COMPRESSION_JPEG);
      $imagick->setImageCompressionQuality(100);
      
      
      $folder_thump=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/uploads/print_forms_preview/';
      
      if (file_exists($folder_thump) == false) {
        if (@mkdir($folder_thump , 0777, true) == false ) {
          $ret['message']=gks_lang('Δεν μπορεί να δημιουργηθεί ο φάκελος <b>print_forms_preview/</b> στον φάκελο uploads');
          debug_mail(false,$ret['message'],'object_name: '.$object_name."\n".'id: '.$id."\n".'form_id: '.$form_id."\n".'options: '.print_r($options,true)); 
          return $ret;
        }
      }
      
      $save_basename_org=basename($path_file);
      $save_basename_org=substr($save_basename_org, 0, strlen($save_basename_org)-4).'.jpg';
      
      do {
        $path_file_thump=$folder_thump.$save_basename_org;
        if (file_exists($path_file_thump) == false) break;
        $save_basename_org=substr($save_basename_org, 0, strlen($save_basename_org)-3).rand(100,999).'.jpg';
      } while(true);
      
      //print '<pre>';print $path_file_thump;die();
      $imagick->writeImages($path_file_thump, false);   
         
      $ret['file_thump']=$save_basename_org;
      $ret['file_thump_url']='/my/uploads/print_forms_preview/'.$save_basename_org;
      
    }
    


    $ret['success']=true;
    $ret['message']='ok';
    $ret['save_basename']=$save_basename;
    $ret['path_file']=$path_file;
    $ret['path_relative']=$options['folder'].$save_basename;
    $ret['url_file']='/my/admin-get-file.php?fs=fileservers&file='.rawurlencode($ret['path_relative']);
    if (isset($options['override']['is_preview']) and $options['override']['is_preview']!=0) {
      $ret['url_file']='/my/admin-get-file.php?fs=tmp&file='.rawurlencode($ret['path_relative']);
    }
    return $ret;
    
  }
  
  if ($row_form['file_type']=='raw') {
   
    
    
    $myout=
    '[[[[header]]]]'.
    $myhtml.
    '[[[[footer]]]]';
    
    $myout=str_replace('[[[[header]]]]', $page_header, $myout);
    //echo $myout; die();

    $myout=str_replace('[[[[footer]]]]', $page_footer, $myout);
    

    $myout=str_replace('<table>',"\n",$myout);
    $myout=str_replace('</table>',"\n",$myout);
    $myout=str_replace('<thead>',"\n",$myout);
    $myout=str_replace('</thead>',"\n",$myout);
    $myout=str_replace('<tbody>',"\n",$myout);
    $myout=str_replace('</tbody>',"\n",$myout);
    $myout=str_replace('<tfoot>',"\n",$myout);
    $myout=str_replace('</tfoot>',"\n",$myout);
    $myout=str_replace('<tr>',"\n",$myout);
    $myout=str_replace('</tr>',"\n",$myout);
    $myout=str_replace('<td>',"\n",$myout);
    $myout=str_replace('</td>',"\n",$myout);
    $myout=str_replace('<p>',"\n",$myout);
    $myout=str_replace('</p>',"\n",$myout);
    
    for ($dddd=0;$dddd<=30;$dddd++) {
      $myout=str_replace("\n\n","\n",$myout);
    }
        
    $myout=str_replace("[br]","\n",$myout);
    
    $save_basename='gks_doc.txt';
    $path_file='';
    
    if (isset($options['fileserver']) and isset($options['folder']) and isset($options['filename'])) {
      $save_basename=$options['filename'].'.txt';
      do {
        $path_file=$options['fileserver'].$options['folder'].$save_basename;
        if (file_exists($path_file) == false) break;
        $save_basename=substr($options['filename'], 0, strlen($options['filename'])-3).rand(100,999).'.txt';
      } while(true);
      
      $write_bytes=file_put_contents($path_file,$myout);
      if ($write_bytes === false) {
        $ret['message']=str_replace('[1]',$save_basename,gks_lang('Δεν είναι δυνατή η δημιουργία του αρχείου [1] στο σύστημα εκτυπώσεων'));
        debug_mail(false,$ret['message'],'options: '.print_r($options,true)); 
        return $ret;}
      
      
        
      $ret['success']=true;
      $ret['message']='ok';
      $ret['save_basename']=$save_basename;
      $ret['path_file']=$path_file;
      $ret['path_relative']=$options['folder'].$save_basename;
      $ret['url_file']='/my/admin-get-file.php?fs=fileservers&file='.rawurlencode($ret['path_relative']);
      if (isset($options['override']['is_preview']) and $options['override']['is_preview']!=0) {
        $ret['url_file']='/my/admin-get-file.php?fs=tmp&file='.rawurlencode($ret['path_relative']);
      }        
      return $ret;

    }
    echo $myout; die();
    //echo '<pre>'; print_r($ret); die();
  }
  
  
  $ret['message']=str_replace('[1]',$row_form['file_type'],gks_lang('Ο τύπος αρχείου <b>[1]</b> δεν υποστηρίζεται'));
  debug_mail(false,$ret['message'],print_r($options,true)); 
  return $ret;
      
      
//  print '<pre>'; print_r($options); die();
//  
//  echo $myout;
//  die();
//  print '<pre>';
//  print_r($row_form);
//  print_r($row_company);
//  die();
//  
//  
//  return $ret;
}


function gks_paper_sizes() {
  $ret=array();
  $ret[]=array('name' => 'A5',     'width_mm' => 148,'height_mm' => 210);
  $ret[]=array('name' => 'A4',     'width_mm' => 210,'height_mm' => 297);
  $ret[]=array('name' => 'A3',     'width_mm' => 297,'height_mm' => 420);
  $ret[]=array('name' => 'B5',     'width_mm' => 176,'height_mm' => 250);
  $ret[]=array('name' => 'B4',     'width_mm' => 250,'height_mm' => 353);
  $ret[]=array('name' => 'JIS-B5', 'width_mm' => 182,'height_mm' => 257);
  $ret[]=array('name' => 'JIS-B4', 'width_mm' => 257,'height_mm' => 364);
  $ret[]=array('name' => 'Letter', 'width_mm' => 216,'height_mm' => 279);  //8.5in x 11in.
  $ret[]=array('name' => 'Legal',  'width_mm' => 216,'height_mm' => 356);  //8.5in x 14in.
  $ret[]=array('name' => 'Ledger', 'width_mm' => 279,'height_mm' => 432);  //11in x 17in.
    
  //$ret[]=array('name' => gks_lang('Προσαρμοσμένο'), 'width_mm' => 0,'height_mm' => 0);
  return $ret;
}

function gks_print_form_get_maxids(&$gks_fobjects_tags,&$max_ids) {
  global $db_link;
 
  $max_ids=array();
  $sql_select="select * FROM gks_print_objects ORDER BY id_print_object ";
  $result_select = $db_link->query($sql_select);        
  if (!$result_select) {debug_mail(false,'error sql',$sql_select);die('sql error');}
  $gks_fobjects_tags=array();
  while ($row_select = $result_select->fetch_assoc()) {
    $gks_fobjects_tags[]=$row_select['object_descr'];
    $sql_maxid='';
    $ctid=0;
    switch ($row_select['object_name']) {   
      case 'gks_orders':   
        $sql_maxid="select max(id_order) as mymaxid from gks_orders having max(id_order)>0";
        break;  
      case 'gks_acc_inv':   
        $sql_maxid="select max(id_acc_inv) as mymaxid from gks_acc_inv having max(id_acc_inv)>0";
        break;  
      case 'gks_acc_pay':   
        $sql_maxid="select max(id_acc_pay) as mymaxid from gks_acc_pay having max(id_acc_pay)>0";
        break;  
      case 'gks_whi_mov':   
        $sql_maxid="select max(id_whi_mov) as mymaxid from gks_whi_mov having max(id_whi_mov)>0";
        break;  
      case 'gks_hotel_reservation':   
        $sql_maxid="select max(id_hotel_reservation) as mymaxid from gks_hotel_reservation having max(id_hotel_reservation)>0";
        break;  
      case 'gks_transfer_reservation':   
        $sql_maxid="select max(id_transfer_reservation) as mymaxid from gks_transfer_reservation having max(id_transfer_reservation)>0";
        break;  
      case 'gks_crm_tasks':   
        $sql_maxid="select max(id_crm_task) as mymaxid from gks_crm_tasks having max(id_crm_task)>0";
        break;  
      case 'gks_eshop_products':   
        $sql_maxid="select max(id_product) as mymaxid from gks_eshop_products having max(id_product)>0";
        break;  
     default:
        if (substr($row_select['object_name'],0,7)=='gks_ct_') {
          $ctid=$row_select['id_print_object'];
          $sql_maxid="select max(id_gks_customt_gks_ct_".$ctid.") as mymaxid
          from gks_customt_gks_ct_".$ctid."
          having max(id_gks_customt_gks_ct_".$ctid.")>0";
        }
    }
    $curr_maxid=0;
    if ($sql_maxid!='') {
      $result_maxid = $db_link->query($sql_maxid);        
      if (!$result_maxid) {debug_mail(false,'error sql',$sql_maxid);die('sql error');}
      if ($result_maxid->num_rows>=1) {
        $row_maxid = $result_maxid->fetch_assoc();
        $curr_maxid=$row_maxid['mymaxid'];
      } 
    } 
    $max_ids[]=array(
      'id'=>intval($row_select['id_print_object']),
      'name'=>$row_select['object_name'],
      'ctid'=>$ctid,
      'maxid'=>$curr_maxid,
      'descr'=>$row_select['object_descr'],
    );
  }

  return true;
  
}

function gks_print_isset_n(&$a) {
  if (isset($a)==false) return 0;
  return $a;
}

function gks_print_isset_s(&$a) {
  if (isset($a)==false) return '';
  return htmlspecialchars_gks(trim_gks($a));
}
function gks_print_isset_h(&$a) {
  if (isset($a)==false) return '';
  return $a;
}


function gks_print_address_text($row) {
  $ttta=[];
  $tttt=trim_gks(trim_gks($row['ma_odos']).' '.trim_gks($row['ma_arithmos']));
  if ($tttt!='') $ttta[]=$tttt;
  if (trim_gks($row['ma_orofos'])!='') $ttta[]=trim_gks($row['ma_orofos']);
  if (trim_gks($row['ma_perioxi'])!='') $ttta[]=trim_gks($row['ma_perioxi']);
  $addressL1=implode(', ',$ttta);
  
  $ttta=[];
  if (trim_gks($row['ma_poli'])!='') $ttta[]=trim_gks($row['ma_poli']);
  if (trim_gks($row['nomos_descr'])!='') $ttta[]=trim_gks($row['nomos_descr']);
  if (trim_gks($row['ma_tk'])!='') $ttta[]=trim_gks($row['ma_tk']);
  
  $addressL2=implode(', ',$ttta);;
  $addressL3=trim_gks($row['country_name']);
  
  $ttta=[];
  if ($addressL1!='') $ttta[]=$addressL1;
  if ($addressL2!='') $ttta[]=$addressL2;
  if ($addressL3!='') $ttta[]=$addressL3;
  $address=implode('<br>',$ttta);
  return $address;
}

function gks_print_idiotites_text($row) {
  $idiotites = '';
  $temp=trim_gks($row['idiotites']);
  if ($temp!='') {
    $myarray = json_decode($temp, true);
    $temp='';
    foreach ($myarray as $value) {
      $temp.=$value[0].': <b>'.$value[1].'</b><br>';
    } 
    if ($temp!='') $temp=substr($temp, 0, strlen($temp)-4);
    $idiotites=$temp;
  }
  return $idiotites;
}

function gks_print_doc_photos_orders($row) {
  global $db_link;
  $temp_out='';
  
  $base_photo_url=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/my/admin-get-file.php?fs=fileservers&file='; //.rawurlencode('order/'.$id.'/');
  
  $sql_photos="select * from gks_orders_photo where order_id=".$row['id_order']." and show_print=1 order by id_orders_photo";
  $result_photos = $db_link->query($sql_photos);
  if (!$result_photos) {debug_mail(false,'error sql',$sql_photos); die('sql error');}
  $row_photos_array=array();
  while ($row_photos = $result_photos->fetch_assoc()) {
    $row_photos_array[]=$row_photos;
  }
  
  if (file_exists(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/')==false) {
    if (@mkdir(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/' , 0777, true) == false ) {
      debug_mail(false,'gks_print_doc_photos_orders can not create dir: ',GKS_SITE_PATH.'my/temp/');
      echo 'error No 221097501203857: can not create temp dir';
      die();
    } 
  }
  
  foreach ($row_photos_array as $row_photos) {
    $full_local_path=GKS_FileServerShare.$row_photos['photo_url'];
    if (file_exists($full_local_path)) {
      // 	$row_photos['photo_url'] is like that: order/32681/4965.jpg
      $thumbfile='';
      do {
        $tttt_rand=rand(10000,99999).'.jpg';
        $tttt=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/'.$tttt_rand;
        if (file_exists($tttt)==false) {
          $thumbfile=$tttt;
          break;
        }
      } while (true);
      makeThumbnails_normal($full_local_path, $thumbfile, 800,600, false);
      
      //echo $thumbfile;die();
      //$this_src=$base_photo_url.urlencode($row_photos['photo_url']);
      //$temp_out.='<img src="'.$base_photo_url.urlencode($row_photos['photo_url']).'" style="max-height:235px;max-width:24%;float:left;padding:0px 1% 1% 0px;">';
      $this_src=GKS_SITE_URL.'my/temp/'.$tttt_rand;
      $temp_out.='<img src="'.$this_src.'" style="max-height:235px;max-width:24%;float:left;padding:0px 1% 1% 0px;">';
    }
    
  }
  return '<div style="clear: both;"></div><div>'.$temp_out.'</div><div style="clear: both;"></div>';
}

function gks_print_doc_links_orders($row) {
  global $db_link;
  $temp_out='';


  $extra_order_id_links=0;
  gks_plugins_functions_run('functions_print_gks_print_doc_links_orders_extra_id',array(
    'id'=>&$row['id_order'],
    'row'=>&$row,
    'extra_order_id_links' => &$extra_order_id_links,
  ));
            


  $query = "SELECT gks_orders_links.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
  FROM gks_orders_links LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_orders_links.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE gks_orders_links.order_id in (".$row['id_order'].($extra_order_id_links>0 ? ','.$extra_order_id_links : '').")
  ORDER BY gks_orders_links.mydate, gks_orders_links.id_order_links;";
  $result_list = $db_link->query($query); 
  if (!$result_list) debug_mail(false,'error sql',$query);
  if (!$result_list) die('sql error');
  $i = 0;
  while ($row_list = $result_list->fetch_assoc()) {
    $temp=trim_gks($row_list['url']);
    $temp_out.=$temp. ' ('.$row_list['gks_nickname'].')<br>';    
  }
  return $temp_out;
}







function gks_print_form_gks_eshop_products($id,$row_form,$options) {
  global $db_link;
  
  $ret=array('success' => false, 'message' => 'gks_orders generic error');
  
  $row=array();
  $row_person=array();
  $row_doc=array();
  $row_canceled_doc=array();
  $row_credit_doc=array();

 
  $sql=str_replace('SELECT SQL_CALC_FOUND_ROWS','SELECT ',$options['sql']);

  //echo '<pre>'.$sql;die();
  
  gks_plugins_functions_run('functions_print_gks_print_form_gks_eshop_products_select',array(
    'sql'=>&$sql,
  ));

    
  

  
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
  if ($result->num_rows==0) {$ret['message']=gks_lang('Δεν βρέθηκαν είδη για εκτύπωση');debug_mail(false,$ret['message'],$sql); return $ret;}

  //echo '<pre>'.$sql;die();
  
  $row_eidoi=array();
  $id_product_array_ids=array();
  while ($eidos = $result->fetch_assoc()) {
    
    $eidos['product_id']=$eidos['id_product'];
    if ($eidos['product_id']==2) $eidos['product_id']=0;
    
    $product_photo=trim_gks($eidos['product_photo']);
    $eidos['product_photo']='';
    if ($product_photo!='') {
      $full_product_photo=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.$product_photo;
      if (file_exists($full_product_photo)) {
        $eidos['product_photo']=GKS_SITE_URL.substr($product_photo, 1);
      }
    }
    
    $eidos['monada_descr']=gks_lang_pft($row_form['gks_lang'],'gks_monades_metrisis','monada_descr',$eidos['product_monada_id'],$eidos['monada_descr']);
    $eidos['monada_symbol']=gks_lang_pft($row_form['gks_lang'],'gks_monades_metrisis','monada_symbol',$eidos['product_monada_id'],$eidos['monada_symbol']);

    $eidos['id_order_product']=0;
    
    $row_eidoi[]=$eidos;
  }
  //print '<pre>';print_r($row_eidoi);die();
  
  //$timemmm=time();
  $gks_custom_prepare = gks_custom_table_item_prepare('gks_eshop_products',['from'=>'print']);
  if ($gks_custom_prepare['success']==true) {
    foreach ($row_eidoi as $pkey => $eidos) {
      $custom_row['id_product']=$eidos['id_product'];
      $gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$custom_row);
      if ($gks_custom_row['success']) {
        //print '<pre>';print_r($gks_custom_row);die();
        
        foreach ($gks_custom_row['fields'] as $key => $cf_item) {
          $row_eidoi[$pkey]['custom_'.$key]=array(
            'type'  => $cf_item['field_type_id'],
            'value' => $cf_item['print'],
          );
        } 
      }
    }
    //echo '<pre>'.(time()-$timemmm);die();
    //echo '<pre>';print_r($row_doc);die();
    //print_r($gks_custom_row);
  }
  //print '<pre>';print_r($row_eidoi);die();

  $products_lots_serials=array();
  $row_fpa=array();
  $row_foroi=array();
    
  $company_id=0;$company_sub_id=0;
  $company_ret=[];
  $company_ret['data']=[];
  
  
  return gks_print_form_make_print($row_form,$row,$row_doc,$row_canceled_doc,$row_credit_doc,$row_eidoi,$row_fpa,$row_foroi,$company_ret['data'],$row_person,$options,$products_lots_serials,[]);
}

require_once 'functions_print2.php';
