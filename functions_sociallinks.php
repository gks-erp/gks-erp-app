<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

function gks_sociallinks_item($object_name,$object_id) {
  global $db_link;
  
  $sql="SELECT gks_sociallinks.*, 
  gks_sociallinks_type.sociallinks_type_descr, gks_sociallinks_type.sociallinks_type_icon
  FROM gks_sociallinks 
  LEFT JOIN gks_sociallinks_type ON gks_sociallinks.sociallinks_type_id = gks_sociallinks_type.id_sociallinks_type
  WHERE gks_sociallinks.object_name='".$db_link->escape_string($object_name)."'
  AND gks_sociallinks.object_id=".$object_id."
  ORDER BY gks_sociallinks_type.sociallinks_type_sortorder, gks_sociallinks.id_sociallinks;";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  
  $items='';$aa = 0;
  while ($row = $result->fetch_assoc()) {
    $aa++;

    $items.='
    <tr class="sociallinks_tr" data-aa="'.$aa.'">
      <th scope="row" nowrap class="mytdcm sociallinks_aa">'.$aa.'</td>      
      <td nowrap class="mytdcm"><i class="fas fa-trash-alt sociallinks_remove" data-aa="'.$aa.'"></i></td>
      
      <td nowrap class="mytdcm">
        <a href="'.$row['url'].'" title="'.$row['sociallinks_type_descr'].'" target="_blank">'
        .$row['sociallinks_type_icon'].
        '</a>
      </td> 
      <td nowrap class="mytdcm">
        <input type="text" class="form-control form-control-sm myneedsave sociallinks_url" data-aa="'.$aa.'" 
        value="'.$row['url'].'" 
        data-type_id="'.$row['sociallinks_type_id'].'">
      </td>
    </tr>
    ';
    
  };
    
  $html='<div class="sociallinks_div">
<table id="sociallinks_table" class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
    <tr >	
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%">#</th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="100%">Url</th>        
    </tr>
</thead>
<tbody>';

  $html.=$items;

  $html.='
  <tr class="" id="sociallinks_tr_new">
    <th scope="row" colspan="2" class="mytdcm"><i class="fas fa-plus-circle gks_gen_add"></i></td>      
    <td nowrap colspan="5">
      <button style="justify-content: center!important;" type="button" class="btn btn-sm btn-primary" id="sociallinks_add">'.gks_lang('Προσθήκη').'</button>';
  
  $select='';    
  $sql="SELECT id_sociallinks_type, sociallinks_type_descr, sociallinks_type_icon
  FROM gks_sociallinks_type
  WHERE sociallinks_type_disable=0 ORDER BY sociallinks_type_sortorder;";
  $result = $db_link->query($sql);  
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => base64_encode('sql error'));
    echo json_encode($return); die();}
  while ($row = $result->fetch_assoc()) {
    $select.='<option value="'.$row['id_sociallinks_type'].'" data-icon="'.base64_encode($row['sociallinks_type_icon']).'">'.$row['sociallinks_type_descr'].'</option>';
    
  }
  $html.='<select id="sociallinks_select" class="form-control form-control-sm myneedsave"><option value="0"></option>'.$select.'</select>';      
      
  $html.='
    </td>  
  </tr>   
  
</tbody>
</table>';
  
  
  $html.='</div>';
  
  return $html;
  
}

function gks_sociallinks_item_save($post,$table_name,$id) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;  
  
  if (isset($post['sociallinks_array_str'])==false or $table_name=='' or $id<1) {
    return array('success' => true, 'message' => 'Nothing to do');}
  
  $sociallinks_array_str = trim_gks(base64_decode($post['sociallinks_array_str']));
  
  
  $sociallinks_array = json_decode($sociallinks_array_str, true);
  if ($sociallinks_array === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'json_decode error sociallinks_array',$_POST['sociallinks_array_str']);
    return array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').' (1)'.gks_lang('Ξαναδοκιμάστε')));}
    
  //echo '<pre>';print_r($sociallinks_array);
  
  $id_sociallinks_array=[];
  foreach ($sociallinks_array as $item) {
    $item['url']=trim_gks($item['url']);
    
    if (startwith(strtolower($item['url']),'http://')==false and startwith(strtolower($item['url']),'https://')==false) {
      $item['url']='https://'.$item['url'];
    }
    if (strlen($item['url'])>=11) { //example min http://a.bc
      $sql="select id_sociallinks from gks_sociallinks 
      where object_name='".$db_link->escape_string($table_name)."'
      and object_id=".$id."
      and sociallinks_type_id=".intval($item['id']);
      $result = $db_link->query($sql);  
      if (!$result) {debug_mail(false,'error sql',$sql);return array('success' => false, 'message' => 'sql error');}
      if ($result->num_rows==0) {
        $sql="insert into gks_sociallinks (
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
        object_name,object_id,sociallinks_type_id,url
        ) values (
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
        '".$db_link->escape_string($table_name)."',
        ".$id.",
        ".intval($item['id']).",
        '".$db_link->escape_string($item['url'])."'
        )";      
        ///echo '<pre>'.$sql;die(); 
        $result = $db_link->query($sql); 
        if (!$result) {debug_mail(false,'error sql',$sql);return array('success' => false, 'message' => 'sql error');}
        $id_sociallinks = $db_link->insert_id;
      } else {
        $row = $result->fetch_assoc();
        $id_sociallinks=$row['id_sociallinks'];
        $sql="update gks_sociallinks set
        object_name='".$db_link->escape_string($table_name)."',
        object_id=".$id.",
        sociallinks_type_id=".intval($item['id']).",
        url='".$db_link->escape_string($item['url'])."',
        mydate_edit=now(),
        user_id_edit=".$my_wp_user_id.",
        myip='".$db_link->escape_string($gkIP)."'
        where id_sociallinks=".$id_sociallinks;
        ///echo '<pre>'.$sql;die(); 
        $result = $db_link->query($sql); 
        if (!$result) {debug_mail(false,'error sql',$sql);return array('success' => false, 'message' => 'sql error');}
      }
      $id_sociallinks_array[]=$id_sociallinks;
    }
  } 
  
  $sql="delete from gks_sociallinks
  where object_name='".$db_link->escape_string($table_name)."'
  and object_id=".$id;
  if (count($id_sociallinks_array)>0) $sql.=" and id_sociallinks not in (".implode(',',$id_sociallinks_array).")";
  $result = $db_link->query($sql); 
  if (!$result) {debug_mail(false,'error sql',$sql);return array('success' => false, 'message' => 'sql error');}
  //echo '<pre>'.$sql;die();
  
  return array('success' => true, 'message' => 'OK');
}
  
  