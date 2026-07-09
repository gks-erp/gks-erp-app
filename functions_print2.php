<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function gks_print_form_gks_customt($id,$row_form,$options) {
  global $db_link;
  $ret=array('success' => false, 'message' => 'gks_crm_tasks generic error');

  $ctid=intval($options['ctid']);
  if ($ctid < 10000) {$ret['message']=gks_lang('Δεν έχει ορισθεί το').' ctid. ('.$ctid.')';debug_mail(false,$ret['message'],$sql); return $ret;}

  $sql_ct="select * 
  from gks_custom_table 
  where custom_table_disabled=0
  and id_custom_table=".$ctid;
  $result_ct = $db_link->query($sql_ct);        
  if (!$result_ct) {debug_mail(false,'error sql',$sql_ct);die('sql error');}
  if ($result_ct->num_rows!=1) {debug_mail(false,'record not found',$sql_ct);die('custom table not found ('.$ctid.')'); }
  $row_ct = $result_ct->fetch_assoc();
  $custom_table_descr=$row_ct['custom_table_descr'];
  $custom_table_name=$row_ct['custom_table_name'];
  $custom_table_name_real='gks_customt_'.$row_ct['custom_table_name'];
  $field_name_id_parent=$row_ct['field_name_id_parent'];
  $field_name_id_current=$row_ct['field_name_id_current'];
  $field_id='id_gks_customt_gks_ct_'.$ctid;  
  
  $sql ="SELECT ".$field_id.",cf_mydate_add,cf_mydate_edit,cf_user_id_add,cf_user_id_edit,cf_myip,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit
  FROM (".$custom_table_name_real." 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on ".$custom_table_name_real.".cf_user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on ".$custom_table_name_real.".cf_user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where ".$field_id." = ".$id;
    

  //print '<pre>'; print_r($sql);die();
  
  $result = $db_link->query($sql);        
  if (!$result) {debug_mail(false,'error sql',$sql);$ret['message']='sql error'; return $ret;}
  if ($result->num_rows!=1) {$ret['message']=gks_lang('Δεν βρέθηκε η κράτηση με ID').' <b>'.$id.'</b>';debug_mail(false,$ret['message'],$sql); return $ret;}
  $row = $result->fetch_assoc();

  //print '<pre>'; print_r($id);die();
  //print '<pre>'; print_r($row);die();
  

  


  $row_doc=array();
  $row_doc['title']=$row_ct['custom_table_descr']; //$row['acc_journal_descr'];

  $row_doc['mydate_add']=showDate(strtotime($row['cf_mydate_add']),'d/m/Y H:i',1);
  $row_doc['mydate_edit']=showDate(strtotime($row['cf_mydate_edit']),'d/m/Y H:i',1);
  $row_doc['gks_nickname_add']=trim_gks($row['gks_nickname_add']);
  $row_doc['gks_nickname_edit']=trim_gks($row['gks_nickname_edit']);
  $row_doc['number']=trim_gks($id);
  $row_doc['number_str']=trim_gks($id);
  $row_doc['photos']=gks_print_doc_photos_customt($row,$id,$ctid,$custom_table_name_real,$field_name_id_parent,$field_name_id_current);
  $row_doc['links']='';//gks_print_doc_links_orders($row);

  //print '<pre>'; print_r($row_doc);die();

  

  
  //print '<pre>';print_r($row_eidoi);die();
  
  
    
  $gks_custom_prepare = gks_custom_table_item_prepare($custom_table_name,['from'=>'print']);
  if ($gks_custom_prepare['success']==true) {
    $gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);
    if ($gks_custom_row['success']) {
      foreach ($gks_custom_row['fields'] as $key => $cf_item) {
        $row_doc['custom_'.$key]=array(
          'type'  => $cf_item['field_type_id'],
          'value' => $cf_item['print'],
        );
      } 
    }
    //echo '<pre>';print_r($row_doc);die();
    //echo '<pre>';print_r($gks_custom_row);die();
  }

//  $gks_custom_prepare = gks_custom_table_item_prepare('wp_users',['from'=>'print']);
//  if ($gks_custom_prepare['success']==true) {
//    $custom_row['ID']=$row_person['id'];
//    $gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$custom_row);
//    if ($gks_custom_row['success']) {
//      foreach ($gks_custom_row['fields'] as $key => $cf_item) {
//        $row_person['custom_'.$key]=array(
//          'type'  => $cf_item['field_type_id'],
//          'value' => $cf_item['print'],
//        );
//      } 
//    }
//    //echo '<pre>';print_r($row_person);die();
//    //print_r($gks_custom_row);
//  }  


  $company_ret=[];$company_ret['data']=[];
  $company_id=0;
  $company_sub_id=0;

  $sql_def_company="SELECT id_custom_field FROM gks_custom_field
  WHERE custom_table_id=".$ctid." AND field_type_id=1004";//company field id
  $result_def_company = $db_link->query($sql_def_company);        
  if (!$result_def_company) {debug_mail(false,'error sql',$sql_def_company);$ret['message']='sql error'; return $ret;}
  if ($result_def_company->num_rows==1) {
    $row_def_company = $result_def_company->fetch_assoc();
    $id_custom_field=intval($row_def_company['id_custom_field']);
    $temp_id=0;
    if (isset($gks_custom_row['fields']['cf'.$id_custom_field]['value_from_db'])) {
      $temp_id=intval($gks_custom_row['fields']['cf'.$id_custom_field]['value_from_db']);
      if ($temp_id>0) {
        //echo '<pre>'.$temp_id;die();
        $company_id=$temp_id;
        $company_sub_id=0;
        $company_ret=gks_print_form_company_data($company_id,$company_sub_id,$options,$row_form);
        if ($company_ret['success']==false) {$ret['message']=$company_ret['message'];return $ret;}
      }
    }
    //echo '<pre>';print_r($gks_custom_row['fields']);die();
    //die('cccc '.$id_custom_field);
  }
  //echo '<pre>';print_r($company_ret);die();
  
  $sql_def_company="SELECT id_custom_field FROM gks_custom_field
  WHERE custom_table_id=".$ctid." AND field_type_id=1005";//subcompany field id
  $result_def_company = $db_link->query($sql_def_company);        
  if (!$result_def_company) {debug_mail(false,'error sql',$sql_def_company);$ret['message']='sql error'; return $ret;}
  if ($result_def_company->num_rows==1) {
    $row_def_company = $result_def_company->fetch_assoc();
    $id_custom_field=intval($row_def_company['id_custom_field']);
    $temp_id=0;
    if (isset($gks_custom_row['fields']['cf'.$id_custom_field]['value_from_db'])) {
      $temp_id=intval($gks_custom_row['fields']['cf'.$id_custom_field]['value_from_db']);
      if ($temp_id>0) {
        //echo '<pre>'.$temp_id;die();
        $company_id=0;
        $company_sub_id=$temp_id;
        $company_ret=gks_print_form_company_data($company_id,$company_sub_id,$options,$row_form);
        if ($company_ret['success']==false) {$ret['message']=$company_ret['message'];return $ret;}
      }
    }
    //echo '<pre>';print_r($gks_custom_row['fields']);die();
    //die('cccc '.$id_custom_field);
  }
    
  //echo '<pre>';print_r($company_ret);die();
  //die('gggg');
    
  $row_canceled_doc=array();
  $row_credit_doc=array();
  $row_eidoi=array();
  $row_fpa=array();
  $row_foroi=array();
  $row_person=[];
  $products_lots_serials=array();
  
  return gks_print_form_make_print($row_form,$row,$row_doc,
  $row_canceled_doc,
  $row_credit_doc,
  $row_eidoi,
  $row_fpa,
  $row_foroi,
  $company_ret['data'],
  $row_person,
  $options,
  $products_lots_serials,[]);
}

function gks_print_doc_photos_customt($row,$id,$ctid,$custom_table_name_real,$field_name_id_parent,$field_name_id_current) {
  global $db_link;
  $temp_out='';
  //echo '<pre>';print_r($row);die();
  
  $base_photo_url=$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/my/admin-get-file.php?fs=fileservers&file='; //.rawurlencode('order/'.$id.'/');
  
  $sql_photos="select * from ".$custom_table_name_real."_photo where gks_customt_gks_".$field_name_id_current."=".$id." and show_print=1 order by ".$field_name_id_parent."_photo";
  //echo '<pre>'.$sql_photos;die();
  $result_photos = $db_link->query($sql_photos);
  if (!$result_photos) {debug_mail(false,'error sql',$sql_photos); die('sql error');}
  $row_photos_array=array();
  while ($row_photos = $result_photos->fetch_assoc()) {
    $row_photos_array[]=$row_photos;
  }
  
  if (file_exists(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/')==false) {
    if (@mkdir(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/temp/' , 0777, true) == false ) {
      debug_mail(false,'gks_print_doc_photos_customt can not create dir: ',GKS_SITE_PATH.'my/temp/');
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
