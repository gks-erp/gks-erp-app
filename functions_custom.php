<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


function gks_custom_table_db_all_create() {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;

  
  $return = array('success' => false, 'message' => 'generic error','run_sqls'=>array());

  $sql="select * from gks_custom_table 
  where ((custom_table_disabled=0 and id_custom_table<10000) or (id_custom_table>=10000))";
  $result = $db_link->query($sql);        
  if (!$result) { 
    debug_mail(false,'error sql',$sql);
     $return['message']='sql error';return $return;}
  
  $mytables=array();
  while ($row = $result->fetch_assoc()) {
    $mytables[]=$row;
  }
  
  foreach ($mytables as $myt) {
    
    $custom_table_name=$myt['custom_table_name'];
    $prefix_custom_table_name=($custom_table_name=='wp_users' ? GKS_WP_TABLE_PREFIX.'users' : $custom_table_name);
    
    $id_custom_table=$myt['id_custom_table'];
    $custom_table_descr=$myt['custom_table_descr'];
    $field_name_id_parent=$myt['field_name_id_parent'];
    $field_name_id_current=$myt['field_name_id_current'];

    $table_name='gks_customt_'.$custom_table_name;
    $sql="show tables like '".$db_link->escape_string($table_name)."'";
    $result = $db_link->query($sql);        
    if (!$result) { 
      debug_mail(false,'error sql',$sql);
      $return['message']='sql error';return $return;}
  
    if ($result->num_rows==0) { //table not exist
      $sql="CREATE TABLE `".$table_name."` (
        `id_".$table_name."` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `cf_odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `cf_mydate_add` datetime DEFAULT NULL,
        `cf_mydate_edit` datetime DEFAULT NULL,
        `cf_user_id_add` int(11) NOT NULL DEFAULT '0',
        `cf_user_id_edit` int(11) NOT NULL DEFAULT '0',
        `cf_myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
        `".$field_name_id_current."` int(11) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id_".$table_name."`) USING BTREE,
        KEY `cf_mydate_edit` (`cf_mydate_edit`),
        KEY `cf_user_id_edit` (`cf_user_id_edit`),
        KEY `".$field_name_id_current."` (`".$field_name_id_current."`)
      ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;";
      
      $result = $db_link->query($sql);        
      if (!$result) { 
        debug_mail(false,'error sql',$sql);
        $return['message']='sql error';return $return;}
      
      $return['run_sqls'][]=$sql;
      
      if ($id_custom_table >= 10000) {
        
        
        $sql="CREATE TABLE IF NOT EXISTS `".$table_name."_photo` (
          `id_".$table_name."_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `".$table_name."_id` int(11) NOT NULL DEFAULT 0,
          `photo_url` varchar(190) NOT NULL,
          `mydate` datetime NOT NULL,
          `mysize` int(11) DEFAULT 0,
          `ip` varchar(48) DEFAULT NULL,
          `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
          `user_add_id` int(11) NOT NULL DEFAULT 0,
          `show_print` tinyint(4) NOT NULL DEFAULT 0,
          `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
          PRIMARY KEY (`id_".$table_name."_photo`),
          KEY `".$table_name."_id` (`".$table_name."_id`),
          KEY `photo_url` (`photo_url`),
          KEY `mydate` (`mydate`),
          KEY `mysize` (`mysize`),
          KEY `ip` (`ip`),
          KEY `show_print` (`show_print`),
          KEY `filesobjectlist` (`filesobjectlist`)
        ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;";
        $result = $db_link->query($sql);        
        if (!$result) { 
          debug_mail(false,'error sql',$sql);
          $return['message']='sql error';return $return;}
        $return['run_sqls'][]=$sql;
        
        $sql="CREATE TABLE IF NOT EXISTS `".$table_name."_log` (
          `id_".$table_name."_log` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `".$table_name."_id` int(11) NOT NULL DEFAULT 0,
          `add_date` datetime NOT NULL,
          `user_id` int(11) DEFAULT 0,
          `sxolio` text NOT NULL,
          PRIMARY KEY (`id_".$table_name."_log`),
          KEY `".$table_name."_id` (`".$table_name."_id`),
          KEY `add_date` (`add_date`),
          KEY `user_id` (`user_id`)
        ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;";
        $result = $db_link->query($sql);        
        if (!$result) { 
          debug_mail(false,'error sql',$sql);
          $return['message']='sql error';return $return;}        
        $return['run_sqls'][]=$sql;


        $sql="CREATE TABLE IF NOT EXISTS `".$table_name."_messages` (
          `id_".$table_name."_message` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `mydate_add` datetime DEFAULT NULL,
          `mydate_edit` datetime DEFAULT NULL,
          `user_id_add` int(11) NOT NULL DEFAULT 0,
          `user_id_edit` int(11) NOT NULL DEFAULT 0,
          `myip` varchar(48) DEFAULT NULL,
          `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
          `".$table_name."_id` int(11) NOT NULL DEFAULT 0,
          `user_id` int(11) NOT NULL DEFAULT 0,
          `customt_message` text DEFAULT NULL,
          `email_id` int(11) NOT NULL DEFAULT 0,
          `sms_id` int(11) NOT NULL DEFAULT 0,
          PRIMARY KEY (`id_".$table_name."_message`),
          KEY `mydate_edit` (`mydate_edit`),
          KEY `user_id_edit` (`user_id_edit`),
          KEY `".$table_name."_id` (`".$table_name."_id`),
          KEY `user_id` (`user_id`),
          KEY `customt_message` (`customt_message`(250)),
          KEY `email_id` (`email_id`),
          KEY `sms_id` (`sms_id`)
        ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;";
        $result = $db_link->query($sql);        
        if (!$result) { 
          debug_mail(false,'error sql',$sql);
          $return['message']='sql error';return $return;}        
        $return['run_sqls'][]=$sql;
                
      }
      
    }
    
    if ($id_custom_table < 10000) {
      
      $sql="insert into ".$table_name." 
      (cf_mydate_add,cf_mydate_edit,cf_user_id_add,cf_user_id_edit,cf_myip,".$field_name_id_current.") 
      SELECT now(),now(),2,2,'127.0.0.1',".$prefix_custom_table_name.".".$field_name_id_parent."
      FROM ".$prefix_custom_table_name." LEFT JOIN ".$table_name." ON ".$prefix_custom_table_name.".".$field_name_id_parent." = ".$table_name.".".$field_name_id_current."
      WHERE ".$table_name.".".$field_name_id_current." Is Null";
      $result = $db_link->query($sql);        
      if (!$result) { 
        debug_mail(false,'error sql',$sql);
        $return['message']='sql error';return $return;}
      $return['run_sqls'][]=$sql;
      
    } else {
      $sql="select * from gks_crm_activity_objects where crm_activity_object_code like 'gks_ct_".$id_custom_table."'";
      $result = $db_link->query($sql);        
      if (!$result) { 
        debug_mail(false,'error sql',$sql);
        $return['message']='sql error';return $return;}
    
      if ($result->num_rows==0) {   
        $sql="insert into gks_crm_activity_objects (
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
        crm_activity_object_code,crm_activity_object_descr,crm_activity_object_sortorder,crm_activity_object_disabled,crm_activity_object_page
        ) values (
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
        'gks_ct_".$id_custom_table."','".$db_link->escape_string($custom_table_descr)."',".$id_custom_table.",0,'admin-ct-item.php?ctid=".$id_custom_table."&id=%s'
        )";
        $result = $db_link->query($sql);        
        if (!$result) { 
          debug_mail(false,'error sql',$sql);
          $return['message']='sql error';return $return;}
        $return['run_sqls'][]=$sql;
      } else {
        $sql="update gks_crm_activity_objects set
        crm_activity_object_descr='".$db_link->escape_string($custom_table_descr)."',
        mydate_edit=now(),
        user_id_edit=".$my_wp_user_id.",
        myip='".$db_link->escape_string($gkIP)."'
        where crm_activity_object_code='gks_ct_".$id_custom_table."' limit 1";
        $result = $db_link->query($sql);        
        if (!$result) { 
          debug_mail(false,'error sql',$sql);
          $return['message']='sql error';return $return;}
        $return['run_sqls'][]=$sql;
      }
      

      $sql="select * from gks_permission_object where table_name='gks_ct_".$id_custom_table."'";
      $result = $db_link->query($sql);        
      if (!$result) { 
        debug_mail(false,'error sql',$sql);
        $return['message']='sql error';return $return;}

      $id_permission_object=0;
      if ($result->num_rows==0) {
      
        $sql="insert into gks_permission_object (
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
        card_title,parent_id,table_name,object_name,sortorder
        ) values (
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
        '".$db_link->escape_string(gks_lang('Προσαρμογή'))."',
        0,'gks_ct_".$id_custom_table."','".$db_link->escape_string($custom_table_descr)."',10".$id_custom_table."
        )";
        $result = $db_link->query($sql);        
        if (!$result) { 
          debug_mail(false,'error sql',$sql);
          $return['message']='sql error';return $return;}

        $id_permission_object = $db_link->insert_id;
        $return['run_sqls'][]=$sql;
      } else {
        $row = $result->fetch_assoc();
        $id_permission_object=$row['id_permission_object'];
        
        $sql="update gks_permission_object set
        object_name='".$db_link->escape_string($custom_table_descr)."',
        mydate_edit=now(),
        user_id_edit=".$my_wp_user_id.",
        myip='".$db_link->escape_string($gkIP)."'
        where id_permission_object=".$id_permission_object." limit 1";
        $result = $db_link->query($sql);        
        if (!$result) { 
          debug_mail(false,'error sql',$sql);
          $return['message']='sql error';return $return;}
        $return['run_sqls'][]=$sql;
        
      }
      
      $sql="select * from gks_permission_user where permission_object_id=".$id_permission_object." and user_id=".$my_wp_user_id;
      $result = $db_link->query($sql);        
      if (!$result) { 
        debug_mail(false,'error sql',$sql);
        $return['message']='sql error';return $return;}

      if ($result->num_rows==0) { 
        $sql="insert into gks_permission_user (
        mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
        permission_object_id,user_id,
        perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete
        ) values (
        now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
        ".$id_permission_object.",".$my_wp_user_id.",
        1,1,1,1,1
        )";
        $result = $db_link->query($sql);        
        if (!$result) { 
          debug_mail(false,'error sql',$sql);
          $return['message']='sql error';return $return;}
        
        $return['run_sqls'][]=$sql;
      }
    }
  }
  
  $sql="update ".GKS_WP_TABLE_PREFIX."users set gks_menu_version=".time()." 
  where ID = ".$my_wp_user_id." 
  or ID in (
    SELECT gks_permission_user.user_id
    FROM (gks_custom_table 
    LEFT JOIN gks_permission_object ON gks_custom_table.custom_table_name = gks_permission_object.table_name) 
    LEFT JOIN gks_permission_user ON gks_permission_object.id_permission_object = gks_permission_user.permission_object_id
    WHERE gks_custom_table.id_custom_table>=10000 
    AND (perm_view=1 or perm_edit=1 or perm_add=1 or perm_delete=1 or perm_autocomplete=1)
    GROUP BY gks_permission_user.user_id
  )";
  $result = $db_link->query($sql);        
  if (!$result) { 
    debug_mail(false,'error sql',$sql);
    $return['message']='sql error';return $return;}
  
  $return['run_sqls'][]=$sql;


  
   
  
  $return['success']=true;
  $return['message']='OK';

  //print '<pre>';print_r($return);die();
  
  return $return; 
}


function gks_custom_table_db_update($custom_table_name) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  
  $return = array('success' => false, 'message' => 'generic error','run_sqls'=>array());
  
  
  $sql="select * from gks_custom_table 
  where ((custom_table_disabled=0 and id_custom_table<10000) or (id_custom_table>=10000))
  and custom_table_name='".$db_link->escape_string($custom_table_name)."'";
  $result = $db_link->query($sql);        
  if (!$result) { 
    debug_mail(false,'error sql',$sql);
     $return['message']='sql error';return $return;}
  if ($result->num_rows==0) { //not devolpment yet
    debug_mail(false,'custom_table_name not devolpment yet',$sql);
    $return['message'] = gks_lang('Δεν έχει υλοποιηθεί αυτήν την στιγμή η συγκεκριμένη λειτουργία για αυτό το αντικείμενο');return $return;}
  
  $row=$result->fetch_assoc();
  $id_custom_table=$row['id_custom_table'];
  $field_name_id_parent=$row['field_name_id_parent'];
  $field_name_id_current=$row['field_name_id_current'];

  
  $sql="SELECT gks_custom_field.id_custom_field, gks_custom_field.field_label, 
  gks_custom_field.field_type_id, gks_custom_field_type.field_type_sql, gks_custom_field_type.field_type_collate, gks_custom_field_type.field_type_index,
  gks_custom_field.field_default_value, gks_custom_field.field_default_value as field_default_value_db,
  gks_custom_field.field_allow_null, gks_custom_field.field_allow_null as field_allow_null_db,
  gks_custom_field.field_attr
  FROM gks_custom_field LEFT JOIN gks_custom_field_type ON gks_custom_field.field_type_id = gks_custom_field_type.id_custom_field_type
  WHERE gks_custom_field.custom_table_id=".$id_custom_table." 
  AND gks_custom_field.field_disabled=0 
  AND gks_custom_field_type.id_custom_field_type Is Not Null";
  $result = $db_link->query($sql);        
  if (!$result) { 
    debug_mail(false,'error sql',$sql);
    $return['message']='sql error';return $return;}
  $fields_new=array();
  while ($row = $result->fetch_assoc()) {
    $row['gks_field_name']='cf'.$row['id_custom_field'];
    $row['gks_found']=false;
    $row['gks_need_update']=false;
    
    if ($row['field_allow_null']==0 and $row['field_default_value']=='' and 
       ($row['field_type_sql']=='int(11)' or $row['field_type_sql']=='tinyint(4)' or $row['field_type_sql']=='double')) {
      $row['field_default_value']='0';   
  
    }
    if ($row['field_type_sql']=='text') {
      $row['field_default_value']='';
      $row['field_allow_null']=1;
    }
    
    if ($row['field_type_sql']=='datetime' or $row['field_type_sql']=='time') {$row['field_allow_null']=1; $row['field_default_value']='';}
    
    $fields_new[]=$row;
  }
  if (count($fields_new)==0) {$return['success']=true; $return['message']='no fields found';return $return;}
  
  

  
  $table_name='gks_customt_'.$custom_table_name;
  $sql="show tables like '".$db_link->escape_string($table_name)."'";
  $result = $db_link->query($sql);        
  if (!$result) { 
    debug_mail(false,'error sql',$sql);
    $return['message']='sql error';return $return;}
  
  if ($result->num_rows==0) { //table not exist
    $sql="CREATE TABLE `".$table_name."` (
      `id_".$table_name."` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `cf_odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      `cf_mydate_add` datetime DEFAULT NULL,
      `cf_mydate_edit` datetime DEFAULT NULL,
      `cf_user_id_add` int(11) NOT NULL DEFAULT '0',
      `cf_user_id_edit` int(11) NOT NULL DEFAULT '0',
      `cf_myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
      `".$field_name_id_current."` int(11) NOT NULL DEFAULT '0',
      PRIMARY KEY (`id_".$table_name."`) USING BTREE,
      KEY `cf_mydate_edit` (`cf_mydate_edit`),
      KEY `cf_user_id_edit` (`cf_user_id_edit`),
      KEY `".$field_name_id_current."` (`".$field_name_id_current."`)
    ) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;";
    
    $result = $db_link->query($sql);        
    if (!$result) { 
      debug_mail(false,'error sql',$sql);
      $return['message']='sql error';return $return;}
    
    $return['run_sqls'][]=$sql;
    
  }
  
  
  
  $sql="SHOW COLUMNS from `".$db_link->escape_string($table_name)."`";
  $result = $db_link->query($sql);        
  if (!$result) { 
    debug_mail(false,'error sql',$sql);
    $return['message']='sql error';return $return;}
  
  $temp=array();
  while ($row = $result->fetch_assoc()) {
    $temp[]=$row;
  }
  $fields_exist=array();
  foreach ($temp as $value) { //clean must have fields
    if ($value['Field']!='id_'.$table_name and $value['Field']!=$field_name_id_current and $value['Field']!='cf_odbc' and $value['Field']!='cf_mydate_add' and $value['Field']!='cf_mydate_edit' and $value['Field']!='cf_user_id_add' and $value['Field']!='cf_user_id_edit' and $value['Field']!='cf_myip') {
      $fields_exist[]=$value;
    }
  }
  
  
  foreach ($fields_new as &$myfn) {
    $found=false;
    foreach ($fields_exist as $myfe) {
      if ($myfe['Field']==$myfn['gks_field_name']) {
        $myfn['gks_found']=true;
        $myfn['exist']=$myfe;
        break;
      }
    }
  }
  unset($myfn);
  
  
  foreach ($fields_new as &$myfn) {
    if ($myfn['gks_found']==false) {
      $sql="ALTER TABLE `".$db_link->escape_string($table_name)."` 
      ADD COLUMN `".$db_link->escape_string($myfn['gks_field_name'])."` ".$myfn['field_type_sql'];
      if ($myfn['field_type_collate']!=0) {
        $sql.=" COLLATE utf8mb4_unicode_520_ci ";
      }
      if ($myfn['field_allow_null']!=0) {
        if ($myfn['field_default_value']=='') {
          $sql.=" DEFAULT NULL";
        } else {
          $sql.=" DEFAULT '".$db_link->escape_string($myfn['field_default_value'])."'";
        }
      } else {
        if ($myfn['field_type_sql']!='text') {
          $sql.=" NOT NULL DEFAULT '".$db_link->escape_string($myfn['field_default_value'])."'";
        }
      }
      if ($myfn['field_label']!='') {
        $sql.=" COMMENT '".$db_link->escape_string($myfn['field_label'])."'";
      }
      if ($myfn['field_type_index']!=0) {
        $sql.=" , ADD INDEX `".$db_link->escape_string($myfn['gks_field_name'])."`(`".$db_link->escape_string($myfn['gks_field_name'])."`".
              ($myfn['field_type_sql']=='text' ? '(250)' : '')   .")";
      }

      
      $return['run_sqls'][]=$sql;
      
      $result = $db_link->query($sql);        
      if (!$result) { 
        debug_mail(false,'error sql',$sql);
        $return['message']='sql error';return $return;}
      
    } else {
      
      if (($myfn['exist']['Type']!=$myfn['field_type_sql']) or               //check if update
          ($myfn['exist']['Null']=='YES' and $myfn['field_allow_null']==0) or 
          ($myfn['exist']['Null']=='NO'  and $myfn['field_allow_null']==1) or
          (trim_gks($myfn['exist']['Default'])!=trim_gks($myfn['field_default_value']))
         ) {
        
        if ($myfn['exist']['Null']=='YES' and $myfn['field_allow_null']==0) {
          $sql="update `".$db_link->escape_string($table_name)."` 
          set `".$db_link->escape_string($myfn['gks_field_name'])."`= ";

          $sql.="'".$db_link->escape_string($myfn['field_default_value'])."'";
          
          $sql.=" where `".$db_link->escape_string($myfn['gks_field_name'])."` is null";
          $return['run_sqls'][]=$sql;
          $result = $db_link->query($sql);        
          if (!$result) { 
            debug_mail(false,'error sql',$sql);
            $return['message']='sql error';return $return;}
          
          
        }
        
        $sql="ALTER TABLE `".$db_link->escape_string($table_name)."` 
        MODIFY COLUMN `".$db_link->escape_string($myfn['gks_field_name'])."` ".$myfn['field_type_sql'];
        if ($myfn['field_type_collate']!=0) {
          $sql.=" COLLATE utf8mb4_unicode_520_ci ";
        }
        if ($myfn['field_allow_null']!=0) {
          if ($myfn['field_default_value']=='') {
            $sql.=" DEFAULT NULL";
          } else {
            $sql.=" DEFAULT '".$db_link->escape_string($myfn['field_default_value'])."'";
          }
        } else {
          if ($myfn['field_type_sql']!='text') {
            $sql.=" NOT NULL DEFAULT '".$db_link->escape_string($myfn['field_default_value'])."'";
          }
        }
        if ($myfn['field_label']!='') {
          $sql.=" COMMENT '".$db_link->escape_string($myfn['field_label'])."'";
        }
        

  
        $return['run_sqls'][]=$sql;
        $result = $db_link->query($sql);        
        if (!$result) { 
          debug_mail(false,'error sql',$sql);
          $return['message']='sql error';return $return;}
        

        //echo 'update '.$myfn['gks_field_name']."\n";  
      } else {
//        $sql="ALTER TABLE `".$db_link->escape_string($table_name)."` 
//        MODIFY COLUMN `".$db_link->escape_string($myfn['gks_field_name'])."` ";
//        if ($myfn['field_label']!='') {
//          $sql.=" COMMENT '".$db_link->escape_string($myfn['field_label'])."'";
//        }    
//        $return['run_sqls'][]=$sql;    
//        $result = $db_link->query($sql);        
//        if (!$result) { 
//          debug_mail(false,'error sql',$sql);
//          $return = array('success' => false, 'message' => 'sql error');return $return;}
        
      }     
    }
  } 
  unset($myfn);
  
//  print '<pre>';
//  print_r($return['run_sqls']);
//  print_r($fields_new);
//  print_r($fields_exist);
//  die();
  
  $return['success']=true;
  $return['message']='OK';
  return $return;
}

function gks_custom_table_item_prepare($custom_table_name,$params=array()) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $today;
  global $today_vardia;
  
  $param_from='item'; // list item pivot print save
  if (isset($params['from'])) {
    $param_from=$params['from'];
  }
  //print '<pre>sssssssss ';print_r($custom_table_name);die();
  $return = array('success' => false, 'message' => 'generic error');

  $prefix_custom_table_name=($custom_table_name=='wp_users' ? GKS_WP_TABLE_PREFIX.'users' : $custom_table_name);
  
  $sql="select * from gks_custom_table where custom_table_name='".$db_link->escape_string($custom_table_name)."'";
  $result = $db_link->query($sql);        
  if (!$result) { 
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => 'sql error');return $return;}
  if ($result->num_rows==0) { //not devolpment yet
    debug_mail(false,'custom_table_name not devolpment yet',$sql);
    $return = array('success' => false, 'message' => gks_lang('Δεν έχει υλοποιηθεί αυτήν την στιγμή η συγκεκριμένη λειτουργία για αυτό το αντικείμενο'));return $return;}
  
  $row=$result->fetch_assoc();
  $table_name='gks_customt_'.$custom_table_name;
  $primary_id='id_gks_customt_'.$custom_table_name;
  $id_custom_table=$row['id_custom_table'];
  $field_name_id_parent=$row['field_name_id_parent'];
  $field_name_id_current=$row['field_name_id_current'];
  $num_columns=$row['num_columns'];

  $card_name_settings=[];
  $temp=trim_gks($row['card_name_settings']);
  if ($temp!='') $card_name_settings=json_decode($temp,true);
  $unique_names=[];
  foreach ($card_name_settings as $value) $unique_names[$value['dbname']]=$value['name'];

  //print '<pre>';print_r($unique_names);print_r($card_name_settings);die();
  
  $sql="SELECT gks_custom_field.id_custom_field, gks_custom_field.field_label, 
  gks_custom_field.field_type_id, gks_custom_field_type.field_type_sql, gks_custom_field_type.field_type_collate, gks_custom_field_type.field_type_index,
  gks_custom_field.field_default_value, gks_custom_field.field_default_value as field_default_value_db,
  gks_custom_field.field_allow_null, gks_custom_field.field_allow_null as field_allow_null_db,
  gks_custom_field.field_attr,
  gks_custom_field.field_card_name,
  gks_custom_field.field_show_on_list
  FROM gks_custom_field LEFT JOIN gks_custom_field_type ON gks_custom_field.field_type_id = gks_custom_field_type.id_custom_field_type
  WHERE gks_custom_field.custom_table_id=".$id_custom_table." 
  AND gks_custom_field.field_disabled=0 
  AND gks_custom_field_type.id_custom_field_type Is Not Null
  AND gks_custom_field_type.field_type_notdevyet=0
  order by gks_custom_field.field_card_name,gks_custom_field.field_sortorder";
  $result = $db_link->query($sql);        
  if (!$result) { 
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => 'sql error');return $return;}
  $fields_new=array();
  while ($row = $result->fetch_assoc()) {
    if (isset($unique_names[$row['field_card_name']])) {
      $row['field_card_name']=$unique_names[$row['field_card_name']];
    } 
      
    $row['gks_field_name']='cf'.$row['id_custom_field'];
    if ($row['field_allow_null']==0 and $row['field_default_value']=='' and 
       ($row['field_type_sql']=='int(11)' or $row['field_type_sql']=='tinyint(4)' or $row['field_type_sql']=='double')) {
      $row['field_default_value']='0';   
    }
    if ($row['field_type_sql']=='text') {
      $row['field_default_value']='';
      $row['field_allow_null']=1;
    }
    
    if ($row['field_type_sql']=='datetime' or $row['field_type_sql']=='time') {$row['field_allow_null']=1; $row['field_default_value']='';}
    
    
    if (trim_gks($row['field_attr'])) {
      $row['field_attr']=unserialize($row['field_attr']);
    } else {
      $row['field_attr']=array();
    }
    
    
    $fields_new[$row['gks_field_name']]=$row;
  }

  //print '<pre>';print_r($fields_new);die();
  
  $sql_all_sele='';
  $sql_all_from='';
  $sql_all_left='';
  $sql_sortable=array();
  $sql_filters=array();
  $sql_filters_date_elems=array();
  $sql_search_fields=array();
  foreach ($fields_new as &$myf) {
    
    $sql_sele='';
    $sql_from='';
    $sql_left='';
    
    //echo '<pre>';print_r($myf);die();
    if ($param_from!='list' or $myf['field_show_on_list']!=0) {
    
      switch ($myf['field_type_id']) {  
        case 1: //	Ναι/Όχι	tinyint(4)
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => $table_name.'.'.$myf['gks_field_name']);
          $sql_filters[] = array(
            'name' => 'f_'.$myf['gks_field_name'],
            'class' => 'filterselectbox',
            'style' => '',
            'title' => $myf['field_label'],
            'has_custom_default' => -1,
            'multiselect' => true,
            'field'  => $table_name.'.'.$myf['gks_field_name']."=%V%",
            'vals' => array(
              array('value' => -100, 'text' => gks_lang('Κενό'),      'sql' => $table_name.'.'.$myf['gks_field_name'].' is null'),
              array('value' => 1, 'text' => gks_lang('Ναι'),          'sql' => $table_name.'.'.$myf['gks_field_name'].'<>0'),
              array('value' => 2, 'text' => gks_lang('Όχι'),          'sql' => $table_name.'.'.$myf['gks_field_name'].'=0'),
            ),
          );
          break;
        case 2: //	Αριθμός ακέραιος	int(11)
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => $table_name.'.'.$myf['gks_field_name']);
          break;
        case 3: //	Αριθμός δεκαδικός	double
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => $table_name.'.'.$myf['gks_field_name']);
          break;
        case 4: //	Κείμενο	varchar(250)
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => $table_name.'.'.$myf['gks_field_name']);
          $sql_search_fields[]=$table_name.'.'.$myf['gks_field_name'];
          break;
        case 5: //	Κείμενο μεγάλο	text
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => $table_name.'.'.$myf['gks_field_name']);
          $sql_search_fields[]=$table_name.'.'.$myf['gks_field_name'];
          break;
        case 6: //	Ημερομηνία	datetime
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => $table_name.'.'.$myf['gks_field_name']);
          $sql_filters_date_elems[]='f_'.$myf['gks_field_name'];
          $sql_filters[] = array(
            'name' => 'f_'.$myf['gks_field_name'],
            'class' => 'filterselectbox ui-state-default ui-corner-all',
            'style' => '',
            'title' => $myf['field_label'],
            'has_custom_date' => true,
            'field' => $table_name.'.'.$myf['gks_field_name'],
            'has_custom_default' => 1,
            //		'mywherepos'=>1,
            'vals' => array_merge(
              array(array('value' => -100, 'text' => gks_lang('Κενό'),      'sql' => $table_name.'.'.$myf['gks_field_name'].' is null')),
              gks_filter_date_vals(['field'=>$table_name.'.'.$myf['gks_field_name'],'future'=>true,'today'=>$today, 'today_vardia'=>$today_vardia])
            ),
          ); 
          break;
        case 7: //	Ώρα	time
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => $table_name.'.'.$myf['gks_field_name']);
          break;
        case 8: //	Ημερομηνία-Ώρα	datetime
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => $table_name.'.'.$myf['gks_field_name']);
          $sql_filters_date_elems[]='f_'.$myf['gks_field_name'];
          $sql_filters[] = array(
            'name' => 'f_'.$myf['gks_field_name'],
            'class' => 'filterselectbox ui-state-default ui-corner-all',
            'style' => '',
            'title' => $myf['field_label'],
            'has_custom_date' => true,
            'field' => $table_name.'.'.$myf['gks_field_name'],
            'has_custom_default' => 1,
            //		'mywherepos'=>1,
            'vals' => array_merge(
              array(array('value' => -100, 'text' => gks_lang('Κενό'),      'sql' => $table_name.'.'.$myf['gks_field_name'].' is null')),
              gks_filter_date_vals(['field'=>$table_name.'.'.$myf['gks_field_name'],'future'=>true,'today'=>$today, 'today_vardia'=>$today_vardia])
            ),          
  			        
            
          );
          break;
        case 9: //	Κείμενο μορφοποιημένο	text
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => $table_name.'.'.$myf['gks_field_name']);
          $sql_search_fields[]=$table_name.'.'.$myf['gks_field_name'];
          break;
        case 201: //	Τηλέφωνο	varchar(250)
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => $table_name.'.'.$myf['gks_field_name']);
          $sql_search_fields[]=$table_name.'.'.$myf['gks_field_name'];
          break;
        case 202: //	email	varchar(250)
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => $table_name.'.'.$myf['gks_field_name']);
          $sql_search_fields[]=$table_name.'.'.$myf['gks_field_name'];
          break;
        case 203: //	url	varchar(250)
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => $table_name.'.'.$myf['gks_field_name']);
          $sql_search_fields[]=$table_name.'.'.$myf['gks_field_name'];
          break;
        case 501: //	Επιλογή ενός από λίστα	int(11)
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => $table_name.'.'.$myf['gks_field_name']);
          $vals=array();
          $vals[]=array('value' => -100, 'text' => gks_lang('Κενό'),      'sql' => '('.$table_name.'.'.$myf['gks_field_name'].' is null or '.$table_name.'.'.$myf['gks_field_name'].' =0)');
          if (isset($myf['field_attr']['options']) and is_array($myf['field_attr']['options'])) {
            foreach ($myf['field_attr']['options'] as $value) {
              if ($value['value']!=0) {
                $vals[]=array('value' => $value['value'], 'text' => $value['text'], 'sql' => $table_name.'.'.$myf['gks_field_name'].'='.$value['value']);
              }
            }
          }
          //print '<pre>';print_r($vals);die();
          if (count($vals)>0) {
            $sql_filters[] = array(
              'name' => 'f_'.$myf['gks_field_name'],
              'class' => 'filterselectbox',
              'style' => '',
              'title' => $myf['field_label'],
              'has_custom_default' => -1,
              'multiselect' => true,
              'field'  => $table_name.'.'.$myf['gks_field_name']."=%V%",
              'vals' => $vals,
            );
          }
          break;
        case 502: //	Επιλογή πολλών από λίστα	varchar(250)
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => $table_name.'.'.$myf['gks_field_name']);
          $sql_search_fields[]=$table_name.'.'.$myf['gks_field_name'];
          //print '<pre>';print_r($myf['field_attr']['options']);die();
          $vals=array();
          $vals[]=array('value' => -100, 'text' => gks_lang('Κενό'),      'sql' => $table_name.'.'.$myf['gks_field_name'].' is null');
          if (isset($myf['field_attr']['options']) and is_array($myf['field_attr']['options'])) {
            foreach ($myf['field_attr']['options'] as $value) {
              if ($value!='') {
                $vals[]=array('value' => $value, 'text' => $value, 'sql' => $table_name.'.'.$myf['gks_field_name'].' like \'%]][['.$db_link->escape_string($value).']][[%\'');
              }
            }
          }
          //print '<pre>';print_r($vals);die();
          if (count($vals)>0) {
            $sql_filters[] = array(
              'name' => 'f_'.$myf['gks_field_name'],
              'class' => 'filterselectbox',
              'style' => '',
              'title' => $myf['field_label'],
              'has_custom_default' => -1,
              'multiselect' => true,
              'field'  => $table_name.'.'.$myf['gks_field_name']." like '%V%'",
              'vals' => $vals,
            );
          }        
          break;
        case 1001: //	Παραστατικό	int(11)
          break;
        case 1002: //	Ημερολόγιο	int(11)
          break;
        case 1003: //	Σειρά	int(11)
          break;
        
        case 1004: //	Εταιρεία	int(11)
          $sql_sele=",table_".$myf['gks_field_name'].".company_title as ".$myf['gks_field_name']."_link ";
          $sql_from="(";
          $sql_left=" LEFT JOIN gks_company as table_".$myf['gks_field_name']." ON ".$table_name.'.'.$myf['gks_field_name']."=table_".$myf['gks_field_name'].".id_company)";
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => 'table_'.$myf['gks_field_name'].'.company_title');
          $sql_search_fields[]="table_".$myf['gks_field_name'].".company_title";
          $sql_filters[] = array(
            'name' => 'f_'.$myf['gks_field_name'],
            'class' => 'filterselectbox',
            'style' => '',
            'title' => $myf['field_label'],
            'has_custom_default' => -1,
            'multiselect' => true,
            'field'  => $table_name.'.'.$myf['gks_field_name']."=%V%",
            'vals' => array(
              array('value' => -100, 'text' => gks_lang('Κενό'),      'sql' => '('.$table_name.'.'.$myf['gks_field_name'].' is null or '.$table_name.'.'.$myf['gks_field_name'].' =0)')
            ),
            'sql'=>  "SELECT gks_company.id_company as id,gks_company.company_title as descr
                      FROM ".$table_name." LEFT JOIN gks_company ON ".$table_name.".".$myf['gks_field_name']." = gks_company.id_company
                      WHERE gks_company.id_company Is Not Null
                      GROUP BY gks_company.id_company
                      ORDER BY gks_company.company_sortorder, gks_company.company_title",
          );
          break;
        case 1005: //	Υποκατάστημα	int(11)
          $sql_sele=",table_".$myf['gks_field_name'].".company_sub_title as ".$myf['gks_field_name']."_link ";
          $sql_from="(";
          $sql_left=" LEFT JOIN gks_company_subs as table_".$myf['gks_field_name']." ON ".$table_name.'.'.$myf['gks_field_name']."=table_".$myf['gks_field_name'].".id_company_sub)";
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => 'table_'.$myf['gks_field_name'].'.company_sub_title');
          $sql_search_fields[]="table_".$myf['gks_field_name'].".company_sub_title";
          $sql_filters[] = array(
            'name' => 'f_'.$myf['gks_field_name'],
            'class' => 'filterselectbox',
            'style' => '',
            'title' => $myf['field_label'],
            'has_custom_default' => -1,
            'multiselect' => true,
            'field'  => $table_name.'.'.$myf['gks_field_name']."=%V%",
            'vals' => array(
              array('value' => -100, 'text' => gks_lang('Κενό'),      'sql' => '('.$table_name.'.'.$myf['gks_field_name'].' is null or '.$table_name.'.'.$myf['gks_field_name'].' =0)'),
              array('value' => 0, 'text' => gks_lang('Κεντρικό'), 'sql' => $table_name.'.'.$myf['gks_field_name'].'=0'),
            ),
            'sql'=>  "SELECT gks_company_subs.id_company_sub as id,gks_company_subs.company_sub_title as descr
                      FROM ".$table_name." LEFT JOIN gks_company_subs ON ".$table_name.".".$myf['gks_field_name']." = gks_company_subs.id_company_sub
                      WHERE gks_company_subs.id_company_sub Is Not Null
                      GROUP BY gks_company_subs.id_company_sub
                      ORDER BY gks_company_subs.company_sub_sortorder, gks_company_subs.company_sub_title",
          );
          break;  
        case 1006: //	Ευκαιρία	int(11)
          $sql_sele=",table_".$myf['gks_field_name'].".subject as ".$myf['gks_field_name']."_link ";
          $sql_from="(";
          $sql_left=" LEFT JOIN gks_crm_leads as table_".$myf['gks_field_name']." ON ".$table_name.'.'.$myf['gks_field_name']."=table_".$myf['gks_field_name'].".id_crm_lead)";
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => 'table_'.$myf['gks_field_name'].'.subject');
          $sql_search_fields[]="table_".$myf['gks_field_name'].".subject";
          $sql_filters[] = array(
            'name' => 'f_'.$myf['gks_field_name'],
            'class' => 'filterselectbox',
            'style' => '',
            'title' => $myf['field_label'],
            'has_custom_default' => -1,
            'multiselect' => true,
            'field'  => $table_name.'.'.$myf['gks_field_name']."=%V%",
            'vals' => array(
              array('value' => -100, 'text' => gks_lang('Κενό'),      'sql' => '('.$table_name.'.'.$myf['gks_field_name'].' is null or '.$table_name.'.'.$myf['gks_field_name'].' =0)'),
            ),
            'sql'=>  "SELECT gks_crm_leads.id_crm_lead as id,gks_crm_leads.subject as descr
                      FROM ".$table_name." LEFT JOIN gks_crm_leads ON ".$table_name.".".$myf['gks_field_name']." = gks_crm_leads.id_crm_lead
                      WHERE gks_crm_leads.id_crm_lead Is Not Null
                      GROUP BY gks_crm_leads.id_crm_lead
                      ORDER BY gks_crm_leads.subject",
          );
          break;
        
        case 1007: //	Είδος	int(11)
          $sql_sele=",CASE
              WHEN table_".$myf['gks_field_name'].".product_class='variable_item' THEN
                CASE
                  WHEN table_".$myf['gks_field_name'].".product_descr<>'' THEN
                    table_".$myf['gks_field_name'].".product_descr
                  ELSE
                    CASE
                      WHEN table_".$myf['gks_field_name'].".product_descr_variable<>'' THEN
                        CONCAT_WS(' ', table_parent_".$myf['gks_field_name'].".product_descr, table_".$myf['gks_field_name'].".product_descr_variable)
                      ELSE
                        table_parent_".$myf['gks_field_name'].".product_descr
                    END
                END
              ELSE table_".$myf['gks_field_name'].".product_descr
            END as ".$myf['gks_field_name']."_link ";
          $sql_from="((";
          $sql_left=" LEFT JOIN gks_eshop_products as table_".$myf['gks_field_name']." ON ".$table_name.'.'.$myf['gks_field_name']."=table_".$myf['gks_field_name'].".id_product)
                      LEFT JOIN gks_eshop_products AS table_parent_".$myf['gks_field_name']." ON table_".$myf['gks_field_name'].".product_parent_id = table_parent_".$myf['gks_field_name'].".id_product)";
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => $myf['gks_field_name'].'_link');
          $sql_search_fields[]="table_".$myf['gks_field_name'].".product_descr";
          $sql_search_fields[]="table_".$myf['gks_field_name'].".product_descr_variable";
          $sql_search_fields[]="table_parent_".$myf['gks_field_name'].".product_descr";
  
          $sql_filters[] = array(
            'name' => 'f_'.$myf['gks_field_name'],
            'class' => 'filterselectbox',
            'style' => '',
            'title' => $myf['field_label'],
            'has_custom_default' => -1,
            'multiselect' => true,
            'field'  => $table_name.'.'.$myf['gks_field_name']."=%V%",
            'vals' => array(
              array('value' => -100, 'text' => gks_lang('Κενό'),      'sql' => '('.$table_name.'.'.$myf['gks_field_name'].' is null or '.$table_name.'.'.$myf['gks_field_name'].' =0)'),
            ),
            'sql'=> "
              SELECT gks_eshop_products.id_product as id,
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
              END as descr
              FROM ".$table_name." 
              LEFT JOIN gks_eshop_products ON ".$table_name.".".$myf['gks_field_name']." = gks_eshop_products.id_product
              LEFT JOIN gks_eshop_products AS gks_eshop_products_parent ON gks_eshop_products.product_parent_id = gks_eshop_products_parent.id_product
              WHERE gks_eshop_products.id_product Is Not Null
              GROUP BY gks_eshop_products.id_product
              ORDER BY descr",
          );
          break;
        case 1008: //	Κατηγορία Είδους	int(11)
          $sql_sele=",table_".$myf['gks_field_name'].".product_category_fullpath as ".$myf['gks_field_name']."_link ";
          $sql_from="(";
          $sql_left=" LEFT JOIN
          (SELECT gks_eshop_products_categories.id_product_category,
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
                            gks_eshop_products_categories.product_category_descr) as product_category_fullpath
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
          ) as table_".$myf['gks_field_name']." ON ".$table_name.'.'.$myf['gks_field_name']."=table_".$myf['gks_field_name'].".id_product_category)";
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => 'table_'.$myf['gks_field_name'].'.product_category_fullpath');
          $sql_search_fields[]="table_".$myf['gks_field_name'].".product_category_fullpath";
          $sql_filters[] = array(
            'name' => 'f_'.$myf['gks_field_name'],
            'class' => 'filterselectbox',
            'style' => '',
            'title' => $myf['field_label'],
            'has_custom_default' => -1,
            'multiselect' => true,
            'field'  => $table_name.'.'.$myf['gks_field_name']."=%V%",
            'vals' => array(
              array('value' => -100, 'text' => gks_lang('Κενό'),      'sql' => '('.$table_name.'.'.$myf['gks_field_name'].' is null or '.$table_name.'.'.$myf['gks_field_name'].' =0)'),
             ),
            'sql'=>  "SELECT id, descr
                      FROM ".$table_name." LEFT JOIN (
                        SELECT gks_eshop_products_categories.id_product_category as id,
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
                                        gks_eshop_products_categories.product_category_descr) as descr
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
                      ) as table_product_category ON ".$table_name.".".$myf['gks_field_name']." = table_product_category.id
                      WHERE table_product_category.id Is Not Null
                      GROUP BY table_product_category.id
                      ORDER BY table_product_category.descr",
          );
          break;      
        case 1009: //	Ξενοδοχείο	int(11)
          break;
        case 1010: //	Διαθεσιμότητα	int(11)
          break;
        case 1011: //	Όροφος	int(11)
          break;
        case 1012: //	Τιμή δωματίου	int(11)
          break;
        case 1013: //	Κράτηση	int(11)
          break;
        case 1014: //	Δωμάτιο	int(11)
          $sql_sele=",table_".$myf['gks_field_name'].".room_descr as ".$myf['gks_field_name']."_link ";
          $sql_from="(";
          $sql_left=" LEFT JOIN gks_hotel_room as table_".$myf['gks_field_name']." ON ".$table_name.'.'.$myf['gks_field_name']."=table_".$myf['gks_field_name'].".id_hotel_room)";
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => 'table_'.$myf['gks_field_name'].'.room_descr');
          $sql_search_fields[]="table_".$myf['gks_field_name'].".room_descr";
          $sql_filters[] = array(
            'name' => 'f_'.$myf['gks_field_name'],
            'class' => 'filterselectbox',
            'style' => '',
            'title' => $myf['field_label'],
            'has_custom_default' => -1,
            'multiselect' => true,
            'field'  => $table_name.'.'.$myf['gks_field_name']."=%V%",
            'vals' => array(
              array('value' => -100, 'text' => gks_lang('Κενό'),      'sql' => '('.$table_name.'.'.$myf['gks_field_name'].' is null or '.$table_name.'.'.$myf['gks_field_name'].' =0)'),
            ),
            'sql'=>  "SELECT gks_hotel_room.id_hotel_room as id, gks_hotel_room.room_descr as descr
                      FROM ".$table_name." LEFT JOIN gks_hotel_room ON ".$table_name.".".$myf['gks_field_name']." = gks_hotel_room.id_hotel_room
                      WHERE gks_hotel_room.id_hotel_room Is Not Null
                      GROUP BY gks_hotel_room.id_hotel_room
                      ORDER BY gks_hotel_room.room_descr",
          );
          break;
        case 1015: //	Τύπος δωματίου	int(11)
          break;
        case 1016: //	Παραγγελία	int(11)
          break;
        case 1017: //	Φόρμα Εκτύπωσης	int(11)
          $sql_sele=",table_".$myf['gks_field_name'].".print_form_descr as ".$myf['gks_field_name']."_link ";
          $sql_from="(";
          $sql_left=" LEFT JOIN gks_print_forms as table_".$myf['gks_field_name']." ON ".$table_name.'.'.$myf['gks_field_name']."=table_".$myf['gks_field_name'].".id_print_form)";
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => 'table_'.$myf['gks_field_name'].'.print_form_descr');
          $sql_search_fields[]="table_".$myf['gks_field_name'].".print_form_descr";
          $sql_filters[] = array(
            'name' => 'f_'.$myf['gks_field_name'],
            'class' => 'filterselectbox',
            'style' => '',
            'title' => $myf['field_label'],
            'has_custom_default' => -1,
            'multiselect' => true,
            'field'  => $table_name.'.'.$myf['gks_field_name']."=%V%",
            'vals' => array(
              array('value' => -100, 'text' => gks_lang('Κενό'),      'sql' => '('.$table_name.'.'.$myf['gks_field_name'].' is null or '.$table_name.'.'.$myf['gks_field_name'].' =0)'),
            ),
            'sql'=>  "SELECT gks_print_forms.id_print_form as id, gks_print_forms.print_form_descr as descr
                      FROM ".$table_name." LEFT JOIN gks_print_forms ON ".$table_name.".".$myf['gks_field_name']." = gks_print_forms.id_print_form
                      WHERE gks_print_forms.id_print_form Is Not Null
                      GROUP BY gks_print_forms.id_print_form
                      order by gks_print_forms.sortorder,gks_print_forms.print_form_descr",
          );
          break;
        case 1018: //	Εργασία παραγωγής	int(11)
          $sql_sele=",table_".$myf['gks_field_name'].".production_ergasia_descr as ".$myf['gks_field_name']."_link ";
          $sql_from="(";
          $sql_left=" LEFT JOIN gks_production_ergasies as table_".$myf['gks_field_name']." ON ".$table_name.'.'.$myf['gks_field_name']."=table_".$myf['gks_field_name'].".id_production_ergasia)";
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => 'table_'.$myf['gks_field_name'].'.production_ergasia_descr');
          $sql_search_fields[]="table_".$myf['gks_field_name'].".production_ergasia_descr";
          $sql_filters[] = array(
            'name' => 'f_'.$myf['gks_field_name'],
            'class' => 'filterselectbox',
            'style' => '',
            'title' => $myf['field_label'],
            'has_custom_default' => -1,
            'multiselect' => true,
            'field'  => $table_name.'.'.$myf['gks_field_name']."=%V%",
            'vals' => array(
              array('value' => -100, 'text' => gks_lang('Κενό'),      'sql' => '('.$table_name.'.'.$myf['gks_field_name'].' is null or '.$table_name.'.'.$myf['gks_field_name'].' =0)'),
            ),
            'sql'=>  "SELECT gks_production_ergasies.id_production_ergasia as id, gks_production_ergasies.production_ergasia_descr as descr
                      FROM ".$table_name." LEFT JOIN gks_production_ergasies ON ".$table_name.".".$myf['gks_field_name']." = gks_production_ergasies.id_production_ergasia
                      WHERE gks_production_ergasies.id_production_ergasia Is Not Null
                      GROUP BY gks_production_ergasies.id_production_ergasia
                      ORDER BY gks_production_ergasies.production_ergasia_sortorder, gks_production_ergasies.production_ergasia_descr",
          );
          break;
        case 1019: //	Πόστο	int(11)
          $sql_sele=",table_".$myf['gks_field_name'].".production_posto_descr as ".$myf['gks_field_name']."_link ";
          $sql_from="(";
          $sql_left=" LEFT JOIN gks_production_posta as table_".$myf['gks_field_name']." ON ".$table_name.'.'.$myf['gks_field_name']."=table_".$myf['gks_field_name'].".id_production_posto)";
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => 'table_'.$myf['gks_field_name'].'.production_posto_descr');
          $sql_search_fields[]="table_".$myf['gks_field_name'].".production_posto_descr";
          $sql_filters[] = array(
            'name' => 'f_'.$myf['gks_field_name'],
            'class' => 'filterselectbox',
            'style' => '',
            'title' => $myf['field_label'],
            'has_custom_default' => -1,
            'multiselect' => true,
            'field'  => $table_name.'.'.$myf['gks_field_name']."=%V%",
            'vals' => array(
              array('value' => -100, 'text' => gks_lang('Κενό'),      'sql' => '('.$table_name.'.'.$myf['gks_field_name'].' is null or '.$table_name.'.'.$myf['gks_field_name'].' =0)'),
            ),
            'sql'=>  "SELECT gks_production_posta.id_production_posto as id, gks_production_posta.production_posto_descr as descr
                      FROM ".$table_name." LEFT JOIN gks_production_posta ON ".$table_name.".".$myf['gks_field_name']." = gks_production_posta.id_production_posto
                      WHERE gks_production_posta.id_production_posto Is Not Null
                      GROUP BY gks_production_posta.id_production_posto
                      ORDER BY gks_production_posta.production_posto_sortorder, gks_production_posta.production_posto_descr",
          );
          break;
        case 1020: //	Ομάδα Επαφών	int(11)
          $sql_sele=",table_".$myf['gks_field_name'].".user_group_fullpath as ".$myf['gks_field_name']."_link ";
          $sql_from="(";
          $sql_left=" LEFT JOIN
          (SELECT gks_users_groups.id_users_group,
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
                            gks_users_groups.group_title) as user_group_fullpath
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
          ) as table_".$myf['gks_field_name']." ON ".$table_name.'.'.$myf['gks_field_name']."=table_".$myf['gks_field_name'].".id_users_group)";
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => 'table_'.$myf['gks_field_name'].'.user_group_fullpath');
          $sql_search_fields[]="table_".$myf['gks_field_name'].".user_group_fullpath";
          $sql_filters[] = array(
            'name' => 'f_'.$myf['gks_field_name'],
            'class' => 'filterselectbox',
            'style' => '',
            'title' => $myf['field_label'],
            'has_custom_default' => -1,
            'multiselect' => true,
            'field'  => $table_name.'.'.$myf['gks_field_name']."=%V%",
            'vals' => array(
              array('value' => -100, 'text' => gks_lang('Κενό'),      'sql' => '('.$table_name.'.'.$myf['gks_field_name'].' is null or '.$table_name.'.'.$myf['gks_field_name'].' =0)'),
            ),
            'sql'=>  "SELECT id, descr
                      FROM ".$table_name." LEFT JOIN (
                        SELECT gks_users_groups.id_users_group as id,
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
                      ) as table_user_group ON ".$table_name.".".$myf['gks_field_name']." = table_user_group.id
                      WHERE table_user_group.id Is Not Null
                      GROUP BY table_user_group.id
                      ORDER BY table_user_group.descr",
          );
          break;
        case 1021: //	Αποθήκη	int(11)
          break;
          
        case 1022: //	Επαφή	int(11)       
          $sql_sele=",table_".$myf['gks_field_name'].".gks_nickname as ".$myf['gks_field_name']."_link ";
          $sql_from="(";
          $sql_left=" LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as table_".$myf['gks_field_name']." ON ".$table_name.'.'.$myf['gks_field_name']."=table_".$myf['gks_field_name'].".ID)";
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => 'table_'.$myf['gks_field_name'].'.gks_nickname');
          $sql_search_fields[]="table_".$myf['gks_field_name'].".gks_nickname";
          $sql_filters[] = array(
            'name' => 'f_'.$myf['gks_field_name'],
            'class' => 'filterselectbox',
            'style' => '',
            'title' => $myf['field_label'],
            'has_custom_default' => -1,
            'multiselect' => true,
            'field'  => $table_name.'.'.$myf['gks_field_name']."=%V%",
            'vals' => array(
              array('value' => -100, 'text' => gks_lang('Κενό'),      'sql' => '('.$table_name.'.'.$myf['gks_field_name'].' is null or '.$table_name.'.'.$myf['gks_field_name'].' =0)'),
            ),
            'sql'=>  "SELECT ".GKS_WP_TABLE_PREFIX."users.ID as id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname as descr
                      FROM ".$table_name." LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ".$table_name.".".$myf['gks_field_name']." = ".GKS_WP_TABLE_PREFIX."users.ID
                      WHERE ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null
                      GROUP BY ".GKS_WP_TABLE_PREFIX."users.ID
                      ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname",
          );
          break;
        case 1023: //	Πληρωμή	int(11)
          break;
        case 1024: //	eshop	int(11)
          break;
        case 1025: //	Μάρκα	int(11)
          $sql_sele=",table_".$myf['gks_field_name'].".brand_fullpath as ".$myf['gks_field_name']."_link ";
          $sql_from="(";
          $sql_left=" LEFT JOIN
          (SELECT gks_eshop_products_brands.id_product_brand,
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
                            gks_eshop_products_brands.product_brand_descr) as brand_fullpath
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
          ) as table_".$myf['gks_field_name']." ON ".$table_name.'.'.$myf['gks_field_name']."=table_".$myf['gks_field_name'].".id_product_brand)";
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => 'table_'.$myf['gks_field_name'].'.brand_fullpath');
          $sql_search_fields[]="table_".$myf['gks_field_name'].".brand_fullpath";
          $sql_filters[] = array(
            'name' => 'f_'.$myf['gks_field_name'],
            'class' => 'filterselectbox',
            'style' => '',
            'title' => $myf['field_label'],
            'has_custom_default' => -1,
            'multiselect' => true,
            'field'  => $table_name.'.'.$myf['gks_field_name']."=%V%",
            'vals' => array(
              array('value' => -100, 'text' => gks_lang('Κενό'),      'sql' => '('.$table_name.'.'.$myf['gks_field_name'].' is null or '.$table_name.'.'.$myf['gks_field_name'].' =0)')
            ),
            'sql'=>  "SELECT id, descr
                      FROM ".$table_name." LEFT JOIN (
                        SELECT gks_eshop_products_brands.id_product_brand as id,
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
                                        gks_eshop_products_brands.product_brand_descr) as descr
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
                      ) as table_brands ON ".$table_name.".".$myf['gks_field_name']." = table_brands.id
                      WHERE table_brands.id Is Not Null
                      GROUP BY table_brands.id
                      ORDER BY table_brands.descr",
          );
          break;
        case 1026: //	Εργασία CRM	int(11)
          $sql_sele=",table_".$myf['gks_field_name'].".subject as ".$myf['gks_field_name']."_link ";
          $sql_from="(";
          $sql_left=" LEFT JOIN gks_crm_tasks as table_".$myf['gks_field_name']." ON ".$table_name.'.'.$myf['gks_field_name']."=table_".$myf['gks_field_name'].".id_crm_task)";
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => 'table_'.$myf['gks_field_name'].'.subject');
          $sql_search_fields[]="table_".$myf['gks_field_name'].".subject";
          $sql_filters[] = array(
            'name' => 'f_'.$myf['gks_field_name'],
            'class' => 'filterselectbox',
            'style' => '',
            'title' => $myf['field_label'],
            'has_custom_default' => -1,
            'multiselect' => true,
            'field'  => $table_name.'.'.$myf['gks_field_name']."=%V%",
            'vals' => array(
              array('value' => -100, 'text' => gks_lang('Κενό'),      'sql' => '('.$table_name.'.'.$myf['gks_field_name'].' is null or '.$table_name.'.'.$myf['gks_field_name'].' =0)'),
            ),
            'sql'=>  "SELECT gks_crm_tasks.id_crm_task as id,gks_crm_tasks.subject as descr
                      FROM ".$table_name." LEFT JOIN gks_crm_tasks ON ".$table_name.".".$myf['gks_field_name']." = gks_crm_tasks.id_crm_task
                      WHERE gks_crm_tasks.id_crm_task Is Not Null
                      GROUP BY gks_crm_tasks.id_crm_task
                      ORDER BY gks_crm_tasks.subject",
          );
          break;
        case 1027: //	Συσκευή	int(11)
          $sql_sele=",case when table_".$myf['gks_field_name'].".crm_machine_serial_number<>'' then
          concat(table_".$myf['gks_field_name'].".crm_machine_name, ' (',table_".$myf['gks_field_name'].".crm_machine_serial_number,')')
          else table_".$myf['gks_field_name'].".crm_machine_name
          end as ".$myf['gks_field_name']."_link ";
          $sql_from="(";
          $sql_left=" LEFT JOIN gks_crm_machine as table_".$myf['gks_field_name']." ON ".$table_name.'.'.$myf['gks_field_name']."=table_".$myf['gks_field_name'].".id_crm_machine)";
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => 'table_'.$myf['gks_field_name'].'.crm_machine_name,table_'.$myf['gks_field_name'].'.crm_machine_serial_number');
          $sql_search_fields[]="table_".$myf['gks_field_name'].".crm_machine_name";
          $sql_search_fields[]="table_".$myf['gks_field_name'].".crm_machine_serial_number";
          $sql_filters[] = array(
            'name' => 'f_'.$myf['gks_field_name'],
            'class' => 'filterselectbox',
            'style' => '',
            'title' => $myf['field_label'],
            'has_custom_default' => -1,
            'multiselect' => true,
            'field'  => $table_name.'.'.$myf['gks_field_name']."=%V%",
            'vals' => array(
              array('value' => -100, 'text' => gks_lang('Κενό'),      'sql' => '('.$table_name.'.'.$myf['gks_field_name'].' is null or '.$table_name.'.'.$myf['gks_field_name'].' =0)'),
            ),
            'sql'=>  "SELECT gks_crm_machine.id_crm_machine as id, 
                      case when crm_machine_serial_number<>'' then
                      concat(crm_machine_name,' (',crm_machine_serial_number,')')
                      else crm_machine_name
                      end as descr
                      FROM ".$table_name." LEFT JOIN gks_crm_machine ON ".$table_name.".".$myf['gks_field_name']." = gks_crm_machine.id_crm_machine
                      WHERE gks_crm_machine.id_crm_machine Is Not Null
                      GROUP BY gks_crm_machine.id_crm_machine
                      ORDER BY gks_crm_machine.crm_machine_name",
          );
          break;
        case 1028: //	Περίσταση	int(11)
          $sql_sele=",table_".$myf['gks_field_name'].".title as ".$myf['gks_field_name']."_link,
          table_types_".$myf['gks_field_name'].".occasion_type_descr as ".$myf['gks_field_name']."_link_type_descr, 
          table_pa_".$myf['gks_field_name'].".payment_acquirer_name as ".$myf['gks_field_name']."_link_an, table_".$myf['gks_field_name'].".mydate_add as ".$myf['gks_field_name']."_link_mydate ";
          $sql_from="(((";
          $sql_left=" LEFT JOIN gks_orders_occasion as table_".$myf['gks_field_name']." ON ".$table_name.'.'.$myf['gks_field_name']."=table_".$myf['gks_field_name'].".id_order_occasion)
          LEFT JOIN gks_occasion_types as table_types_".$myf['gks_field_name']." ON table_".$myf['gks_field_name'].".occasion_id = table_types_".$myf['gks_field_name'].".id_occasion_type) 
          LEFT JOIN gks_payment_acquirers as table_pa_".$myf['gks_field_name']." ON table_".$myf['gks_field_name'].".pay_method_id = table_pa_".$myf['gks_field_name'].".id_payment_acquirer)";
          $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => 'table_'.$myf['gks_field_name'].'.title,table_types_'.$myf['gks_field_name'].'.occasion_type_descr,table_pa_'.$myf['gks_field_name'].'.payment_acquirer_name');
          $sql_search_fields[]="table_".$myf['gks_field_name'].".title";
          $sql_search_fields[]="table_types_".$myf['gks_field_name'].".occasion_type_descr";
          $sql_search_fields[]="table_pa_".$myf['gks_field_name'].".payment_acquirer_name";
          $sql_filters[] = array(
            'name' => 'f_'.$myf['gks_field_name'],
            'class' => 'filterselectbox',
            'style' => '',
            'title' => $myf['field_label'],
            'has_custom_default' => -1,
            'multiselect' => true,
            'field'  => "table_".$myf['gks_field_name'].".occasion_id = %V%", //$table_name.'.'.$myf['gks_field_name']."=%V%",
            'vals' => array(
              array('value' => -100, 'text' => gks_lang('Κενό'),      'sql' => '('.$table_name.'.'.$myf['gks_field_name'].' is null or '.$table_name.'.'.$myf['gks_field_name'].' =0)'),
            ),
            'sql'=>  "SELECT gks_orders_occasion.occasion_id as id, gks_occasion_types.occasion_type_descr as descr
                      FROM (".$table_name." LEFT JOIN gks_orders_occasion ON ".$table_name.".".$myf['gks_field_name']." = gks_orders_occasion.id_order_occasion) 
                      LEFT JOIN gks_occasion_types ON gks_orders_occasion.occasion_id = gks_occasion_types.id_occasion_type
                      WHERE (((gks_occasion_types.id_occasion_type) Is Not Null))
                      GROUP BY gks_orders_occasion.occasion_id, gks_occasion_types.occasion_type_descr
                      ORDER BY gks_occasion_types.occasion_type_descr",
          );
          break;
          
          
        
        case 1222:
  //        echo time();die();
  //        $sql_sortable[]=array('name' => 'so_'.$myf['gks_field_name'], 'field' => $table_name.'.'.$myf['gks_field_name']);
  //        $sql_search_fields[]=$table_name.'.'.$myf['gks_field_name'];
          //print '<pre>';print_r($myf['field_attr']['options']);die();
          
          $vals=array(
            array('value' => -100, 'text' => gks_lang('Κενό'),          'sql' => "(".$table_name.'.'.$myf['gks_field_name']."='' or ".$table_name.'.'.$myf['gks_field_name']." is null)"),
            array('value' => -200, 'text' => gks_lang('Μη κενό'),       'sql' => $table_name.'.'.$myf['gks_field_name']."<>''"),
          );
  
          $sql_multi="select ".$myf['gks_field_name']." as multival from ".$table_name." where ".$myf['gks_field_name']."<>'' group by ".$myf['gks_field_name'];
          $result_multi = $db_link->query($sql_multi);        
          if (!$result_multi) { 
            debug_mail(false,'error sql',$sql_multi);
            $return = array('success' => false, 'message' => 'sql error');return $return;}
          $multi_data=array();
          while ($row_multi = $result_multi->fetch_assoc()) {
            $multi_data[]=trim_gks($row_multi['multival']);
          }
          $temp_ids=array();
          foreach ($multi_data as $temp) {
  
            $temp=explode(']][[',$temp);
            
            foreach ($temp as $value) {
              $value=intval($value);
              if ($value>0 && in_array($value,$temp_ids)==false) {
                $temp_ids[]=$value;
              }
            }
          }
          if (count($temp_ids)>0) {
            $sql_multi="select ID, gks_nickname from ".GKS_WP_TABLE_PREFIX."users where ID in (".implode(',',$temp_ids).") order by gks_nickname";
            
            $result_multi = $db_link->query($sql_multi);        
            if (!$result_multi) { 
              debug_mail(false,'error sql',$sql_multi);
              //$return = array('success' => false, 'message' => 'sql error');return $return;
            }
            if ($result_multi) {
              $temp_multi=array();
              while ($row_multi = $result_multi->fetch_assoc()) {
                
                $vals[]=array('value' => $row_multi['ID'], 'text' => (trim_gks($row_multi['gks_nickname'])!='' ? trim_gks($row_multi['gks_nickname']) : 'ID:'.trim_gks($row_multi['ID'])),'sql' => $table_name.'.'.$myf['gks_field_name']." like '%]][[".$row_multi['ID']."]][[%'");
                
              }
            }
            
            
          }
          
          
  //        if (isset($myf['field_attr']['options']) and is_array($myf['field_attr']['options'])) {
  //          foreach ($myf['field_attr']['options'] as $value) {
  //            if ($value!='') {
  //              $vals[]=array('value' => $value, 'text' => $value, 'sql' => $table_name.'.'.$myf['gks_field_name'].' like \'%]][['.$db_link->escape_string($value).']][[%\'');
  //            }
  //          }
  //        }
  //        print '<pre>';print_r($vals);die();
          if (count($vals)>0) {
            $sql_filters[] = array(
              'name' => 'f_'.$myf['gks_field_name'],
              'class' => 'filterselectbox',
              'style' => '',
              'title' => $myf['field_label'],
              'has_custom_default' => -1,
              'multiselect' => true,
              'field'  => $table_name.'.'.$myf['gks_field_name']." = %V%",
              'vals' => $vals,
            );
          }       
          break;
          
          
        default:
          break;
      }
      if ($sql_sele=='' and $myf['field_type_id']>=1001 and $myf['field_type_id']<1200) {
        debug_mail(false,'field_type_id',print_r($myf,true));
        $return = array('success' => false, 'message' => gks_lang('Δεν έχει υλοποιηθεί αυτήν την στιγμή ο τύπος field_type_id με αριθμό').' '.$myf['field_type_id']);return $return;
      }
      
      $myf['sql_sele']=$sql_sele;
      $myf['sql_from']=$sql_from;
      $myf['sql_left']=$sql_left;
      
      $sql_all_sele.=$sql_sele;
      $sql_all_from.=$sql_from;
      $sql_all_left.=$sql_left;
      
    }
  } 
  unset($myf);
  
  //print '<pre>';print $sql_all_sele."\n";print $sql_all_from."\n";print $sql_all_left."\n"; print_r($fields_new);die();


  $sql_all_list_left="LEFT JOIN ".$table_name." ON ".$prefix_custom_table_name.'.'.$field_name_id_parent."=".$table_name.'.'.$field_name_id_current.")";
  //echo $sql_list;die();
  
  $return = array(
    'success' => true, 
    'message' => 'OK', 
    'fields' => $fields_new,
    'table' => array(
      'custom_table_name' => $custom_table_name,
      'table_name' => $table_name,
      'primary_id' => $primary_id,
      'id_custom_table' => $id_custom_table,
      'field_name_id_parent' => $field_name_id_parent,
      'field_name_id_current' => $field_name_id_current,
      'num_columns' => $num_columns,
      'card_name_settings'=>$card_name_settings,
    ),
    'sql_all_sele'=> (count($fields_new)<=0 ? '' : $sql_all_sele),
    'sql_all_from'=> (count($fields_new)<=0 ? '' : $sql_all_from),
    'sql_all_left'=> (count($fields_new)<=0 ? '' : $sql_all_left),
    'sql_all_list_sele'=> (count($fields_new)<=0 ? '' : ','.$table_name.'.*'.$sql_all_sele),
    'sql_all_list_from'=> (count($fields_new)<=0 ? '' : '( '.$sql_all_from),
    'sql_all_list_left'=> (count($fields_new)<=0 ? '' : $sql_all_list_left."\n".$sql_all_left),
    'sql_sortable' => (count($fields_new)<=0 ? array() : $sql_sortable),
    'sql_filters' => (count($fields_new)<=0 ? array() : $sql_filters),
    'sql_filters_date_elems' => (count($fields_new)<=0 ? array() : $sql_filters_date_elems),
    'sql_search_fields' => (count($fields_new)<=0 ? array() : $sql_search_fields),
  );
  
  //print '<pre>';print_r($fields_new);die();
  //print '<pre>';print_r($return);die();
  
  return $return;

}

function gks_custom_table_item_view($gks_custom_prepare,$row) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $gks_card_expand_array;
  global $autocomplete_gks_disable;
  
  //echo '<pre>sss ';print_r($gks_custom_prepare);die();//kostas
  
  if ($gks_custom_prepare['success']==false) {
    $gks_custom_prepare['html']=$gks_custom_prepare['message'];
    return $gks_custom_prepare;
  }
  //echo time();die();
  $return = array('success' => false, 'message' => 'generic error');
 // print '<pre>';print_r($gks_custom_prepare);die();
  
  
  $custom_table_name=$gks_custom_prepare['table']['custom_table_name'];
  $db_num_columns=$gks_custom_prepare['table']['num_columns'];
  
  //echo $custom_table_name;die();
  $prefix_custom_table_name=($custom_table_name=='wp_users' ? GKS_WP_TABLE_PREFIX.'users' : $custom_table_name);
  
  $table_name=$gks_custom_prepare['table']['table_name'];
  $primary_id=$gks_custom_prepare['table']['primary_id'];
  $id_custom_table=$gks_custom_prepare['table']['id_custom_table'];
  $field_name_id_parent=$gks_custom_prepare['table']['field_name_id_parent'];
  $field_name_id_current=$table_name.'.'.$gks_custom_prepare['table']['field_name_id_current'];
  $card_name_settings=$gks_custom_prepare['table']['card_name_settings'];
  
  $fields=$gks_custom_prepare['fields'];
  $sql_all_sele= $gks_custom_prepare['sql_all_sele'];
  $sql_all_from= $gks_custom_prepare['sql_all_from'];
  $sql_all_left= $gks_custom_prepare['sql_all_left'];
  
  //if (strlen($sql_all_sele)>0) $sql_all_sele=substr($sql_all_sele, 0, strlen($sql_all_sele)-1);
  if (strlen($sql_all_from)>0) $sql_all_from=substr($sql_all_from, 0, strlen($sql_all_from)-1);
  if (strlen($sql_all_left)>0) $sql_all_left=substr($sql_all_left, 0, strlen($sql_all_left)-1);
  
  
  
  $default_vals=true;$idrec=-1;
  //echo $field_name_id_parent;die();
  if (isset($row[$field_name_id_parent]) and $row[$field_name_id_parent]>0) {
    $default_vals=false;
    $idrec=intval($row[$field_name_id_parent]);

  }
  if (isset($row['gks_base_template_id']) and $row['gks_base_template_id']>0) {
    $default_vals=false;
    $idrec=intval($row['gks_base_template_id']);
    //echo 'gggggggggg';die();
  }  
  
  if ($idrec>0) {
    $sql="select ".$table_name.".*". $sql_all_sele. " 
    
    from ".$sql_all_from." ".$table_name." 
    ".$sql_all_left."
    where ".$field_name_id_current."=".$idrec ;
    
    //echo '<pre>';print $sql;die();
    
    $result = $db_link->query($sql);        
    if (!$result) { 
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => 'sql error');return $return;}
    if ($result->num_rows==1) {
      $row_c = $result->fetch_assoc();
    } else {
      $default_vals=true;
    }
  }
  if ($default_vals) {
    $row_c=array();
    foreach ($fields as $myf) {
      if ($myf['field_type_sql']=='int(11)' or $myf['field_type_sql']=='tinyint(4)') {
        $row_c[$myf['gks_field_name']]=intval($myf['field_default_value_db']);
      } else if ($myf['field_type_sql']=='double') {
        $row_c[$myf['gks_field_name']]=floatval($myf['field_default_value_db']);
      } else if ($myf['field_type_sql']=='datetime') {
        if ($myf['field_type_id']==8) { //Ημερομηνία-Ώρα
          if ($myf['field_default_value_db']=='now') $row_c[$myf['gks_field_name']]=date('Y-m-d H:i:s');
          else $row_c[$myf['gks_field_name']]='';
        } else if ($myf['field_type_id']==6) { //Ημερομηνία
          if ($myf['field_default_value_db']=='now') $row_c[$myf['gks_field_name']]=date('Y-m-d').' 00:00:00';
          else $row_c[$myf['gks_field_name']]='';
        } else {
          $row_c[$myf['gks_field_name']]='';
        }
      } else if ($myf['field_type_sql']=='time') {
        if ($myf['field_default_value_db']=='now') $row_c[$myf['gks_field_name']]=date('H:i:s');
        else $row_c[$myf['gks_field_name']]='';
      } else {
        $row_c[$myf['gks_field_name']]=trim_gks($myf['field_default_value_db']);
      }
    }
  }
  
  //echo '<pre>';print_r($row_c); die();
  
  
  
  $htmls=array();
  foreach ($fields as &$myf) {
    $html_key=$myf['field_card_name'];
    if ($html_key=='') $html_key=gks_lang('Προσαρμοσμένα');
    if (isset($htmls[$html_key])==false) $htmls[$html_key]='';
    
    $html_item='';
    $print_item='';
    $fname=$myf['gks_field_name'];
    $elem_id='gks_cf_'.$id_custom_table.'_'.$myf['id_custom_field'];
    $elem_name='gks_cf_'.$id_custom_table.'_'.$myf['id_custom_field'];
    $value_from_db=$row_c[$fname];
    
    
    switch ($myf['field_type_id']) {   
      case 1: //	Ναι/Όχι	tinyint(4)
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input type="checkbox" name="'.$elem_name.'"  id="'.$elem_id.'" value="1" '.
              ($row_c[$fname]!=0 ? ' checked ' : '').
              'data-cf-id="'.$myf['id_custom_field'].'" '.
              'class="switchery_gks_custom gks_custom_field_class">'.
            '</div>'.
          '</div>';
        $print_item=($row_c[$fname]!=0 ? gks_lang('Ναι') : gks_lang('Όχι'));
        break;
      case 2: //	Αριθμός ακέραιος	int(11)
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="number" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_int form-control form-control-sm myneedsave" value="'.myNumberFormatNo0($row_c[$fname],true).'" min=0 step="1" style="max-width:150px">'.
            '</div>'.
          '</div>';
        $print_item=myNumberFormatNo0($row_c[$fname],true);
        break;
      case 3: //	Αριθμός δεκαδικός	double
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="number" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_double form-control form-control-sm myneedsave" value="'.myNumberFormatNo0($row_c[$fname],true).'" min=0 step="0.01" style="max-width:150px">'.
            '</div>'.
          '</div>';
        $print_item=myNumberFormatNo0($row_c[$fname],true);
        break;
      case 4: //	Κείμενο	varchar(250)
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_text form-control form-control-sm myneedsave" value="'.htmlspecialchars_gks($row_c[$fname]).'">'.
            '</div>'.
          '</div>';
        $print_item=htmlspecialchars_gks($row_c[$fname]);
        break;
      case 5: //	Κείμενο μεγάλο	text
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<textarea id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_textarea form-control form-control-sm myneedsave" style="min-height:100px;height:100px;" >'.htmlspecialchars_gks($row_c[$fname]).'</textarea>'.
            '</div>'.
          '</div>';
        $print_item=htmlspecialchars_gks($row_c[$fname]);
        break;
      case 9: //	Κείμενο μορφοποιημένο	text
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-12 col-form-label form-control-sm text-md-right1">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-12">'.
              '<textarea id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_tinymce form-control form-control-sm myneedsave" style="height:200px;" >'.htmlspecialchars_gks($row_c[$fname]).'</textarea>'.
            '</div>'.
          '</div>';
        $print_item=trim_gks($row_c[$fname]);
        break;
      case 6: //	Ημερομηνία	datetime
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_date form-control form-control-sm myneedsave" value="'.
              (empty($row_c[$fname])==false ? showDate(strtotime($row_c[$fname]), 'd/m/Y', 1) : '').
              '" autocomplete="'.$autocomplete_gks_disable.'" style="max-width:150px">'.
            '</div>'.
          '</div>';
        $print_item=(empty($row_c[$fname])==false ? showDate(strtotime($row_c[$fname]), 'd/m/Y', 1) : '');
        break;
      case 7: //	Ώρα	time
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_time form-control form-control-sm myneedsave" value="'.
              (empty($row_c[$fname])==false ? showDate(strtotime($row_c[$fname]), 'H:i:s', 1) : '').
              '" autocomplete="'.$autocomplete_gks_disable.'" style="max-width:150px">'.
            '</div>'.
          '</div>';
        $print_item=(empty($row_c[$fname])==false ? showDate(strtotime($row_c[$fname]), 'H:i:s', 1) : '');
        break;
      case 8: //	Ημερομηνία-Ώρα	datetime
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_datetime form-control form-control-sm myneedsave" value="'.
              (empty($row_c[$fname])==false ? showDate(strtotime($row_c[$fname]), 'd/m/Y H:i', 1) : '').
              '" autocomplete="'.$autocomplete_gks_disable.'" style="max-width:200px">'.
            '</div>'.
          '</div>';
        $print_item=(empty($row_c[$fname])==false ? showDate(strtotime($row_c[$fname]), 'd/m/Y H:i', 1) : '');
        break;
      case 201: //	Τηλέφωνο	varchar(250)
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="tel" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_text form-control form-control-sm myneedsave" value="'.htmlspecialchars_gks($row_c[$fname]).'">'.
            '</div>'.
          '</div>';      
        $print_item=htmlspecialchars_gks($row_c[$fname]);
        break;
      case 202: //	email	varchar(250)
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="email" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_text form-control form-control-sm myneedsave" value="'.htmlspecialchars_gks($row_c[$fname]).'">'.
            '</div>'.
          '</div>';      
        $print_item=htmlspecialchars_gks($row_c[$fname]);
        break;
      case 203: //	url	varchar(250)
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="url" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_text form-control form-control-sm myneedsave" value="'.htmlspecialchars_gks($row_c[$fname]).'">'.
            '</div>'.
          '</div>';      
        $print_item=htmlspecialchars_gks($row_c[$fname]);
        break;
      case 501: //	Επιλογή ενός από λίστα	int(11)
        
        //print '<pre>';print_r($myf['field_attr']);die();
        $select_options='';
        $select_options.='<option value="0" '.
            (0==$row_c[$fname] ? 'selected ' : '').
            '></option>';
        if (isset($myf['field_attr']['options']) and is_array($myf['field_attr']['options'])) {
          foreach ($myf['field_attr']['options'] as $value) {
            if ($value['value']!=0) {
              $select_options.='<option value="'.$value['value'].'" '.
              ($value['value']==$row_c[$fname] ? 'selected ' : '').
              '>'.$value['text'].'</option>';
              
              if ($value['value']==$row_c[$fname]) {
                $print_item=$value['text']; 
              }
            }
          }
        }
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<select id="'.$elem_id.'" name="'.$elem_name.'" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_select form-control form-control-sm myneedsave">'.
                $select_options.
              '</select>'.
            '</div>'.
          '</div>'; 
             
        break;
      case 502: //	Επιλογή πολλών από λίστα	varchar(250)
        
        $tags='';
        if (isset($myf['field_attr']['options']) and is_array($myf['field_attr']['options']) and count($myf['field_attr']['options'])>0) {
          $tags=base64_encode(json_encode($myf['field_attr']['options']));
        }
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_multiselect form-control form-control-sm myneedsave" value="'.htmlspecialchars_gks($row_c[$fname]).'" '.
              'data-tags="'.$tags.'" '.
              '>'.
            '</div>'.
          '</div>';
        
        $temp=$row_c[$fname];
        if (substr($temp, 0,4)==']][[') $temp=substr($temp, 4);
        if (substr($temp, strlen($temp)-4)==']][[') $temp=substr($temp, 0, strlen($temp)-4);
        $print_item=implode(', ',explode(']][[',$temp));
        break;
      case 1001: //	Παραστατικό	int(11)
        break;
      case 1002: //	Ημερολόγιο	int(11)
        break;
      case 1003: //	Σειρά	int(11)
        break;
      case 1004: //	Εταιρεία	int(11)
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_autocomplete form-control form-control-sm myneedsave" '.
              'value="'.(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '').'" '.
              'style="width:calc(98% - 22px);display:inline;" '.
              'placeholder="'.gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες').'" '.
              'data-id="'.$row_c[$fname].'" '.
              'data-url="admin-autocomplete-company.php" '.
              'data-url-a="admin-company-item.php?id=[[]]" '.
              '> '.
              '<a id="'.$elem_id.'_autocomplete" tabindex="-1" href="admin-company-item.php?id='.$row_c[$fname].'" style="'.($row_c[$fname]==0 ? 'display:none' : '').'"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" '.
              'title="'.gks_lang('Προβολή εταιρείας').'"></i></a>'.
            '</div>'.
          '</div>';
        $print_item=(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '');
        break;
      case 1005: //	Υποκατάστημα	int(11)
        $temp=(isset($row_c[$fname.'_link']) ? $row_c[$fname.'_link'] : '');
        if (isset($row_c[$fname]) and $row_c[$fname]==0) $temp=gks_lang('Κεντρικό');
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_autocomplete form-control form-control-sm myneedsave" '.
              'value="'.htmlspecialchars_gks($temp).'" '.
              'style="width:calc(98% - 22px);display:inline;" '.
              'placeholder="'.gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες').'" '.
              'data-id="'.$row_c[$fname].'" '.
              'data-url="admin-autocomplete-company-sub.php?and_kentriko=1" '.
              'data-url-a="admin-company-sub-item.php?id=[[]]" '.
              '> '.
              '<a id="'.$elem_id.'_autocomplete" tabindex="-1" href="admin-company-sub-item.php?id='.$row_c[$fname].'" style="'.($row_c[$fname]==0 ? 'display:none' : '').'"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" '.
              'title="'.gks_lang('Προβολή υποκαταστήματος').'"></i></a>'.
            '</div>'.
          '</div>';
        $print_item=htmlspecialchars_gks($temp);
        break;
      case 1006: //	Ευκαιρία	int(11)
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_autocomplete form-control form-control-sm myneedsave" '.
              'value="'.(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '').'" '.
              'style="width:calc(98% - 22px);display:inline;" '.
              'placeholder="'.gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες').'" '.
              'data-id="'.$row_c[$fname].'" '.
              'data-url="admin-autocomplete-lead.php" '.
              'data-url-a="admin-crm-lead-item.php?id=[[]]" '.
              '> '.
              '<a id="'.$elem_id.'_autocomplete" tabindex="-1" href="admin-crm-lead-item.php?id='.$row_c[$fname].'" style="'.($row_c[$fname]==0 ? 'display:none' : '').'"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" '.
              'title="'.gks_lang('Προβολή εργασίας').'"></i></a>'.
            '</div>'.
          '</div>';
        $print_item=(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '');
        break;
      case 1007: //	Είδος	int(11)
        //echo '<pre>eeeeeeeeee ';print_r($myf);die();//kostas
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_autocomplete form-control form-control-sm myneedsave" '.
              'value="'.(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '').'" '.
              'style="width:calc(98% - 22px);display:inline;" '.
              'placeholder="'.gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες').'" '.
              'data-id="'.$row_c[$fname].'" '.
              'data-url="admin-autocomplete-product.php" '.
              'data-url-a="admin-products-item.php?id=[[]]" '.
              '> '.
              '<a id="'.$elem_id.'_autocomplete" tabindex="-1" href="admin-products-item.php?id='.$row_c[$fname].'" style="'.($row_c[$fname]==0 ? 'display:none' : '').'"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" '.
              'title="'.gks_lang('Προβολή είδους').'"></i></a>'.
            '</div>'.
          '</div>';
        $print_item=(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '');
        break;
      case 1008: //	Κατηγορία Είδους	int(11)
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_autocomplete form-control form-control-sm myneedsave" '.
              'value="'.(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '').'" '.
              'style="width:calc(98% - 22px);display:inline;" '.
              'placeholder="'.gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες').'" '.
              'data-id="'.$row_c[$fname].'" '.
              'data-url="admin-autocomplete-cateidos.php" '.
              'data-url-a="admin-product-categories-item.php?id=[[]]" '.
              '> '.
              '<a id="'.$elem_id.'_autocomplete" tabindex="-1" href="admin-product-categories-item.php?id='.$row_c[$fname].'" style="'.($row_c[$fname]==0 ? 'display:none' : '').'"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" '.
              'title="'.gks_lang('Προβολή κατηγορίας είδους').'"></i></a>'.
            '</div>'.
          '</div>';
        $print_item=(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '');
        break;
      case 1009: //	Ξενοδοχείο	int(11)
        break;
      case 1010: //	Διαθεσιμότητα	int(11)
        break;
      case 1011: //	Όροφος	int(11)
        break;
      case 1012: //	Τιμή	int(11)
        break;
      case 1013: //	Κράτηση	int(11)
        break;
      case 1014: //	Δωμάτιο	int(11)
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_autocomplete form-control form-control-sm myneedsave" '.
              'value="'.(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '').'" '.
              'style="width:calc(98% - 22px);display:inline;" '.
              'placeholder="'.gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες').'" '.
              'data-id="'.$row_c[$fname].'" '.
              'data-url="admin-autocomplete-hotel-room.php" '.
              'data-url-a="admin-hotel-room-item.php?id=[[]]" '.
              '> '.
              '<a id="'.$elem_id.'_autocomplete" tabindex="-1" href="admin-hotel-room-item.php?id='.$row_c[$fname].'" style="'.($row_c[$fname]==0 ? 'display:none' : '').'"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" '.
              'title="'.gks_lang('Προβολή δωματίου').'"></i></a>'.
            '</div>'.
          '</div>';
        $print_item=(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '');
        break;
      case 1015: //	Τύπος δωματίου	int(11)
        break;
      case 1016: //	Παραγγελία	int(11)
        break;
      case 1017: //	Φόρμα Εκτύπωσης	int(11)
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_autocomplete form-control form-control-sm myneedsave" '.
              'value="'.(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '').'" '.
              'style="width:calc(98% - 22px);display:inline;" '.
              'placeholder="'.gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες').'" '.
              'data-id="'.$row_c[$fname].'" '.
              'data-url="admin-autocomplete-print_form.php" '.
              'data-url-a="admin-print_forms-item.php?id=[[]]" '.
              '> '.
              '<a id="'.$elem_id.'_autocomplete" tabindex="-1" href="admin-print_forms-item.php?id='.$row_c[$fname].'" style="'.($row_c[$fname]==0 ? 'display:none' : '').'"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" '.
              'title="'.gks_lang('Προβολή φόρμας εκτύπωσης').'"></i></a>'.
            '</div>'.
          '</div>';
        $print_item=(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '');
        break;
      case 1018: //	Εργασία παραγωγής	int(11)
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_autocomplete form-control form-control-sm myneedsave" '.
              'value="'.(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '').'" '.
              'style="width:calc(98% - 22px);display:inline;" '.
              'placeholder="'.gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες').'" '.
              'data-id="'.$row_c[$fname].'" '.
              'data-url="admin-autocomplete-ergasies.php" '.
              'data-url-a="admin-production-ergasies-item.php?id=[[]]" '.
              '> '.
              '<a id="'.$elem_id.'_autocomplete" tabindex="-1" href="admin-production-ergasies-item.php?id='.$row_c[$fname].'" style="'.($row_c[$fname]==0 ? 'display:none' : '').'"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" '.
              'title="'.gks_lang('Προβολή εργασίας παραγωγής').'"></i></a>'.
            '</div>'.
          '</div>';
        $print_item=(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '');
        break;
      case 1019: //	Πόστο	int(11)
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_autocomplete form-control form-control-sm myneedsave" '.
              'value="'.(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '').'" '.
              'style="width:calc(98% - 22px);display:inline;" '.
              'placeholder="'.gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες').'" '.
              'data-id="'.$row_c[$fname].'" '.
              'data-url="admin-autocomplete-posto.php" '.
              'data-url-a="admin-production-posta-item.php?id=[[]]" '.
              '> '.
              '<a id="'.$elem_id.'_autocomplete" tabindex="-1" href="admin-production-posta-item.php?id='.$row_c[$fname].'" style="'.($row_c[$fname]==0 ? 'display:none' : '').'"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" '.
              'title="'.gks_lang('Προβολή πόστου').'"></i></a>'.
            '</div>'.
          '</div>';
        $print_item=(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '');
        break;
      case 1020: //	Ομάδα Επαφών	int(11)
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_autocomplete form-control form-control-sm myneedsave" '.
              'value="'.(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '').'" '.
              'style="width:calc(98% - 22px);display:inline;" '.
              'placeholder="'.gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες').'" '.
              'data-id="'.$row_c[$fname].'" '.
              'data-url="admin-autocomplete-usersgroups.php" '.
              'data-url-a="admin-usersgroups-item.php?id=[[]]" '.
              '> '.
              '<a id="'.$elem_id.'_autocomplete" tabindex="-1" href="admin-usersgroups-item.php?id='.$row_c[$fname].'" style="'.($row_c[$fname]==0 ? 'display:none' : '').'"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" '.
              'title="'.gks_lang('Προβολή ομάδας επαφών').'"></i></a>'.
            '</div>'.
          '</div>';
        $print_item=(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '');
        break;
      case 1021: //	Αποθήκη	int(11)
        break;
      case 1022: //	Επαφή	int(11)
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_autocomplete form-control form-control-sm myneedsave" '.
              'value="'.(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '').'" '.
              'style="width:calc(98% - 22px);display:inline;" '.
              'placeholder="'.gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες').'" '.
              'data-id="'.$row_c[$fname].'" '.
              'data-url="admin-autocomplete-user.php" '.
              'data-url-a="admin-users-item.php?id=[[]]" '.
              '> '.
              '<a id="'.$elem_id.'_autocomplete" tabindex="-1" href="admin-users-item.php?id='.$row_c[$fname].'" style="'.($row_c[$fname]==0 ? 'display:none' : '').'"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" '.
              'title="'.gks_lang('Προβολή επαφής').'"></i></a>'.
            '</div>'.
          '</div>';
        $print_item=(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '');
        break;
      case 1023: //	Πληρωμή	int(11)
        break;
      case 1024: //	eshop	int(11)
        break;
      case 1025: //	Μάρκα	int(11)
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_autocomplete form-control form-control-sm myneedsave" '.
              'value="'.(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '').'" '.
              'style="width:calc(98% - 22px);display:inline;" '.
              'placeholder="'.gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες').'" '.
              'data-id="'.$row_c[$fname].'" '.
              'data-url="admin-autocomplete-brands.php" '.
              'data-url-a="admin-product-brands-item.php?id=[[]]" '.
              '> '.
              '<a id="'.$elem_id.'_autocomplete" tabindex="-1" href="admin-product-brands-item.php?id='.$row_c[$fname].'" style="'.($row_c[$fname]==0 ? 'display:none' : '').'"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" '.
              'title="'.gks_lang('Προβολή μάρκας').'"></i></a>'.
            '</div>'.
          '</div>';
        $print_item=(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '');
        break;

      case 1026: //	Ergasia	CRM int(11)
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_autocomplete form-control form-control-sm myneedsave" '.
              'value="'.(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '').'" '.
              'style="width:calc(98% - 22px);display:inline;" '.
              'placeholder="'.gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες').'" '.
              'data-id="'.$row_c[$fname].'" '.
              'data-url="admin-autocomplete-task.php" '.
              'data-url-a="admin-crm-task-item.php?id=[[]]" '.
              '> '.
              '<a id="'.$elem_id.'_autocomplete" tabindex="-1" href="admin-crm-task-item.php?id='.$row_c[$fname].'" style="'.($row_c[$fname]==0 ? 'display:none' : '').'"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" '.
              'title="'.gks_lang('Προβολή εργασίας').'"></i></a>'.
            '</div>'.
          '</div>';
        $print_item=(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '');
        break;
      case 1027: //	Συσκευή	int(11)
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_autocomplete form-control form-control-sm myneedsave" '.
              'value="'.(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '').'" '.
              'style="width:calc(98% - 22px);display:inline;" '.
              'placeholder="'.gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες').'" '.
              'data-id="'.$row_c[$fname].'" '.
              'data-url="admin-autocomplete-machine.php" '.
              'data-url-a="admin-crm-machine-item.php?id=[[]]" '.
              '> '.
              '<a id="'.$elem_id.'_autocomplete" tabindex="-1" href="admin-crm-machine-item.php?id='.$row_c[$fname].'" style="'.($row_c[$fname]==0 ? 'display:none' : '').'"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" '.
              'title="'.gks_lang('Προβολή συσκευής').'"></i></a>'.
            '</div>'.
          '</div>';
        $print_item=(isset($row_c[$fname.'_link']) ? htmlspecialchars_gks($row_c[$fname.'_link']) : '');
        break;
      case 1028: //	Περίσταση	int(11)
        $occasion_title = '';
        $temp = (isset($row_c[$fname.'_link_type_descr']) ? trim_gks($row_c[$fname.'_link_type_descr']) : '');         if ($temp!='') $occasion_title.=$temp.' / ';
        $temp = (isset($row_c[$fname.'_link']) ? trim_gks($row_c[$fname.'_link']) : '');      if ($temp!='') $occasion_title.=$temp.' / ';
        $temp = (isset($row_c[$fname.'_link_an']) ? trim_gks($row_c[$fname.'_link_an']) : ''); if ($temp!='') $occasion_title.=$temp.' / ';
        $temp = (isset($row_c[$fname.'_link_mydate']) ? trim_gks($row_c[$fname.'_link_mydate']) : '');   if ($temp!='') $occasion_title.=showDate(strtotime($temp), 'd/m/Y H:i', 1) .' / ';
        if ($occasion_title!='') $occasion_title=substr($occasion_title, 0, strlen($occasion_title) - 3);

      

        
        
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_autocomplete form-control form-control-sm myneedsave" '.
              'value="'.htmlspecialchars_gks($occasion_title).'" '.
              'style="width:calc(98% - 22px);display:inline;" '.
              'placeholder="'.gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες').'" '.
              'data-id="'.$row_c[$fname].'" '.
              'data-url="admin-autocomplete-order-occasion.php?allfields=1" '.
              'data-url-a="admin-orders-occasion-item.php?id=[[]]" '.
              '> '.
              '<a id="'.$elem_id.'_autocomplete" tabindex="-1" href="admin-orders-occasion-item.php?id='.$row_c[$fname].'" style="'.($row_c[$fname]==0 ? 'display:none' : '').'"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" '.
              'title="'.gks_lang('Προβολή περίστασης').'"></i></a>'.
            '</div>'.
          '</div>';
        $print_item=htmlspecialchars_gks($occasion_title);
        break;

      case 1222: //	Επαφές	varchar(250)
//        $multi_tags='';
//        if (isset($myf['field_attr']['options']) and is_array($myf['field_attr']['options']) and count($myf['field_attr']['options'])>0) {
//          $tags=base64_encode(json_encode($myf['field_attr']['options']));
//        }

        $temp_out='';
        $temp=trim_gks($row_c[$fname]);
        if ($temp!='') {
          if (substr($temp, 0,4)==']][[') $temp=substr($temp, 4);
          if (substr($temp, strlen($temp)-4)==']][[') $temp=substr($temp, 0, strlen($temp)-4);
          //echo $temp;die();
          $temp=explode(']][[',$temp);
          $temp_ids=array();
          foreach ($temp as $value) {
            $value=intval($value);
            if ($value>0) {
              $temp_ids[]=$value;
            }
          }
          $temp_out='';
          if (count($temp_ids)>0) {
            $sql_multi="select ID, gks_nickname from ".GKS_WP_TABLE_PREFIX."users where ID in (".implode(',',$temp_ids).") order by gks_nickname";
            
            $result_multi = $db_link->query($sql_multi);        
            if (!$result_multi) { 
              debug_mail(false,'error sql',$sql_multi);
              //$return = array('success' => false, 'message' => 'sql error');return $return;
            }
            if ($result_multi) {
              $temp_multi=array();
              while ($row_multi = $result_multi->fetch_assoc()) {
                if (trim_gks($row_multi['gks_nickname'])!='') {
                  $temp_multi[]=trim_gks($row_multi['gks_nickname']);
                }
              }
              if (count($temp_multi)>0) {
                $temp_out=']][['.implode(']][[',$temp_multi).']][[';
              }
            }
          }
        }
        
        
          
        $html_item=
          '<div class="form-group row">'.
            '<label for="'.$elem_id.'" class="col-md-4 col-form-label form-control-sm text-md-right">'.
            $myf['field_label'].($myf['field_allow_null_db']==0 ? ' (<span style="color:#ff0000">*</span>)' : '').
            '</label>'.
            '<div class="col-md-8">'.
              '<input id="'.$elem_id.'" name="'.$elem_name.'" type="text" data-cf-id="'.$myf['id_custom_field'].'" class="gks_custom_field_class gks_custom_field_multiselect form-control form-control-sm myneedsave" value="'.htmlspecialchars_gks($temp_out).'" '.
              'data-multi-tags="" '.
              '>'.
            '</div>'.
          '</div>';
        $temp=$temp_out;
        if (substr($temp, 0,4)==']][[') $temp=substr($temp, 4);
        if (substr($temp, strlen($temp)-4)==']][[') $temp=substr($temp, 0, strlen($temp)-4);
        $print_item=implode(', ',explode(']][[',$temp));
        break;
              
      default:
      
    }
    
    
    $myf['html']=$html_item;
    
    $htmls[$html_key].=$html_item;
    
    $myf['print']=$print_item;
    $myf['value_from_db']=$value_from_db;
  }
  unset($myf);
  
  $num_columns=1;$num_per_column=count($htmls);$column_class='col-md-6';
  $is_gks_ct=false;
  if (substr($custom_table_name,0,7,)=='gks_ct_') {
    $is_gks_ct=true;
    $num_columns=$db_num_columns;
    
    $num_per_column=ceil(count($htmls)/$num_columns);
    switch ($num_columns) {   
      case 1: $column_class='col-md-12'; break;  
      case 2: $column_class='col-md-6'; break;  
      case 3: $column_class='col-md-4'; break;  
      case 4: $column_class='col-md-3'; break;  
      //case 5: $column_class='col-md-6'; break;  
      case 6: $column_class='col-md-2'; break;  
    }
   
  } 
      
  
  
  
  $html='';
  if (count($htmls)>0) {
    $jj=0;$cur_column=0;
    
    if ($is_gks_ct) {
      $html.='<div class="'.$column_class.'" data-ct="1">';
      //if ($num_columns==1) 
      $html.='<div class="row">';
    }
    
    foreach ($htmls as $key => $value) {
      
      //echo '<pre>sssssss '.$key.' ';print_r($card_name_settings);die();
      $card_width='col-md-12';
      foreach ($card_name_settings as $vset) {
        if ($vset['name']==$key) {
          $card_width='col-md-'.$vset['width'];
          break;
        }
      } 
      
      if ($is_gks_ct and $cur_column >= $num_per_column) {
        $html.='</div></div><div class="'.$column_class.'" data-ct="1"><div class="row">';
        $cur_column=0;
      } else {
        //$card_width='gks_custom_card_typical_obj';
      }
      
      $jj++;
      $cur_column++;
      
      $html.=
      '<!--custom_html_block_'.$jj.' start-->'.
      '<div class="'.$card_width.'">'.
        '<div class="card gks_card_expand gks_custom_fileds_data" data-custom_html_block="'.$jj.'">'.
          '<div class="card-header" style="text-align:center">'.
            $key.
          '</div>'.
          '<div class="card-body" '.gks_card_body('custom'.$jj).'>'.
            $value.
          '</div>'.
        '</div>'.
      '</div>'.
      '<!--custom_html_block_'.$jj.' end-->
      ';
      
      
    }
    
    if ($is_gks_ct) {
      //if ($num_columns==1) 
      $html.='</div>';
      $html.='</div>';
    } else {
      $html='<div class="row">'.$html.'</div>';
      
    }
  }
  
  

  $return = array(
    'success' => true, 
    'message' => 'OK', 
    'fields' => $fields,
    'html'=>$html,
  );
  
  return $return;
    
  
}

function gks_custom_table_item_save_prepare($post,$custom_table_name) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $autocomplete_gks_disable;
  
  $return = array('success'=>false, 'message'=>'generic error');
  
  
  if (isset($post['cf_datasend_str'])==false) {
    $return = array('success'=>true, 'message'=>'OK');
    return $return;
  }
  
  $cf_datasend_str = trim_gks(base64_decode($post['cf_datasend_str']));
  $cf_datasend_array = json_decode($cf_datasend_str, true);
  if ($cf_datasend_array === null && json_last_error() !== JSON_ERROR_NONE) {
    debug_mail(false,'json_decode error',$post['cf_datasend_str']);
    $return = array('success' => false, 'message' => base64_encode(gks_lang('Σφάλμα αποστολής δεδομένων').'<br>'.gks_lang('Ξαναδοκιμάστε')));
    echo json_encode($return); die();}
  
  $fdata=array();
  foreach ($cf_datasend_array as $cf) {
    $fdata['cf'.$cf['f']]=$cf['v'];
  }
  
//  $return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($fdata,true)));
//  echo json_encode($return); die();

  

  
  $gks_custom_prepare=gks_custom_table_item_prepare($custom_table_name,['from'=>'save']);

  $fields=$gks_custom_prepare['fields'];
  $custom_table_name=$gks_custom_prepare['table']['custom_table_name'];
  $prefix_custom_table_name=($custom_table_name=='wp_users' ? GKS_WP_TABLE_PREFIX.'users' : $custom_table_name);


  $table_name=$gks_custom_prepare['table']['table_name'];
  $primary_id=$gks_custom_prepare['table']['primary_id'];
  $id_custom_table=$gks_custom_prepare['table']['id_custom_table'];
  $field_name_id_parent=$gks_custom_prepare['table']['field_name_id_parent'];
  $field_name_id_current=$gks_custom_prepare['table']['field_name_id_current'];

  
  
  foreach ($fdata as $myf => &$myv) {
    $field_type_sql=$fields[$myf]['field_type_sql'];
    switch ($field_type_sql) {
      case 'tinyint(4)':
        $myv=intval($myv);
        if ($myv!=0) $myv=1;
        break;
      case 'int(11)':
        $myv=intval($myv);
        break;
      case 'double':
        $myv=floatval($myv);
        break;
      case 'varchar(250)':
      case 'text':
        if ($fields[$myf]['field_type_id']==502) { //
          if ($myv!='') {
            $myv=']][['.$myv.']][[';
          }
        } else if ($fields[$myf]['field_type_id']==1222) {
          if ($myv!='') {
            $temp_ids=array();
            $parts=explode(']][[',$myv);
            foreach ($parts as $value) {
              $value=trim_gks($value);
              if ($value!='') {
                $sql="select ID from ".GKS_WP_TABLE_PREFIX."users where gks_nickname like '".$db_link->escape_string($value)."'";
                $result = $db_link->query($sql);        
                if (!$result) { 
                  debug_mail(false,'error sql',$sql);
                  $return = array('success' => false, 'message' => base64_encode('error sql'));
                  echo json_encode($return); die(); }
        
                while ($row = $result->fetch_assoc()) {
                  $temp_ids[]=$row['ID'];
                }
              }
            } 
            if (count($temp_ids)>0) {
              $myv=']][['.implode(']][[',$temp_ids).']][[';
            }
            //echo $myv.' - ';print_r($temp_ids);die();
          }
        } else {
          $myv=trim_gks($myv);
        }
        break;
      case 'datetime':
        $myv=trim_gks($myv);
        if ($fields[$myf]['field_type_id']==8) { //Ημερομηνία-Ώρα
          if ($myv=='__/__/____ __:__') $myv='';
          if ($myv!='') {
            $myv = mystrtodb($myv);
          }
        } else if ($fields[$myf]['field_type_id']==6) { //Ημερομηνία 
          if ($myv=='__/__/____') $myv='';
          if ($myv!='') {
            $myv = mystrtodb($myv.' 00:00');
          }
        }
        
        break;
      case 'time':
        $myv=trim_gks($myv);
        if ($myv == '00:00:00') $myv='';
        if (strlen($myv)!=8 or substr($myv,2,1)!=':' or substr($myv,5,1)!=':' or 
            ctype_digit(substr($myv,0,2))==false or 
            ctype_digit(substr($myv,3,2))==false or 
            ctype_digit(substr($myv,6,2))==false) {
          $myv='';
        } else {
          $myv=showDate(strtotime($myv),'H:i:s',-1);
        }
        break;
      
      default:
        //debug_mail(false,'error sql',$myf.' '.$myv.print_r($fdata,true).print_r($gks_custom_prepare,true));
        $return = array('success' => false, 'message' => base64_encode('field_type_sql: '.$field_type_sql));
        echo json_encode($return); die();      
    }
    
  } 
  unset($myf);unset($myv);
  //echo '<pre>';var_dump($fdata);die();
  //$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($fdata,true)));
  //echo json_encode($return); die();
  
  
  $user_warnings=array();
  foreach ($fdata as $myf => $myv) {
    if ($fields[$myf]['field_type_id']==501) { //
      if ($fields[$myf]['field_allow_null_db']==0 and $myv==0) {
        $tmpmsg=gks_lang('Το πεδίο <b>[1]</b> δεν μπορεί να είναι κενό');
        $tmpmsg=str_replace('[1]',$fields[$myf]['field_label'],$tmpmsg);        
        $user_warnings[]=$tmpmsg;
      }
    } else {
      if ($fields[$myf]['field_allow_null_db']==0 and trim_gks($myv)=='') { // apetite
        $tmpmsg=gks_lang('Το πεδίο <b>[1]</b> δεν μπορεί να είναι κενό');
        $tmpmsg=str_replace('[1]',$fields[$myf]['field_label'],$tmpmsg);
        $user_warnings[]=$tmpmsg;
      }
    }
  }
  //print '<pre>';print_r($user_warnings);die();
  
  if (count($user_warnings)>0) {
    debug_mail(false,'user_warnings', print_r($user_warnings,true));
    $return = array('success' => false, 'message' => base64_encode(implode('<br>',$user_warnings)));
    echo json_encode($return); die();}
  
  
  $return = array(
    'success' => true, 
    'message' => 'OK', 
    'gks_custom_prepare' => $gks_custom_prepare,
    'fdata' => $fdata,
  );
  
  return $return;  
  
}
  
function gks_custom_table_item_save_run($gks_custom_prepare,$id) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  
  $return = array('success'=>false, 'message'=>'generic error');
  if (isset($gks_custom_prepare['gks_custom_prepare'])==false) {
    $return = array('success'=>true, 'message'=>'OK');
    return $return;
  }
    
  $fields=$gks_custom_prepare['gks_custom_prepare']['fields'];
  $custom_table_name=$gks_custom_prepare['gks_custom_prepare']['table']['custom_table_name'];
  $prefix_custom_table_name=($custom_table_name=='wp_users' ? GKS_WP_TABLE_PREFIX.'users' : $custom_table_name);
  $table_name=$gks_custom_prepare['gks_custom_prepare']['table']['table_name'];
  $primary_id=$gks_custom_prepare['gks_custom_prepare']['table']['primary_id'];
  $id_custom_table=$gks_custom_prepare['gks_custom_prepare']['table']['id_custom_table'];
  $field_name_id_parent=$gks_custom_prepare['gks_custom_prepare']['table']['field_name_id_parent'];
  $field_name_id_current=$gks_custom_prepare['gks_custom_prepare']['table']['field_name_id_current'];
  
  $fdata=$gks_custom_prepare['fdata'];
  
  //echo '<pre>';print_r($gks_custom_prepare);die();
  
  
  $sql="select * from ".$table_name." where ".$field_name_id_current."=".$id ;
  $result = $db_link->query($sql);        
  if (!$result) { 
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => 'sql error');return $return;}
  
  
  if ($result->num_rows==0) {
    $row_old=array();
    $sql="insert into ".$table_name." (
    cf_mydate_add,cf_mydate_edit,cf_user_id_add,cf_user_id_edit,cf_myip,
    ".$field_name_id_current."
    ) values (
    now(),now(),".$my_wp_user_id.",".$my_wp_user_id.",'".$db_link->escape_string($gkIP)."',
    ".$id."
    )";
    $result = $db_link->query($sql);        
    if (!$result) { 
      debug_mail(false,'error sql',$sql);
      $return = array('success' => false, 'message' => 'sql error');return $return;}
  } else  {
    $row_old=$result->fetch_assoc();
  }
  
  
  
  
  $sql='';
  foreach ($fdata as $myf => &$myv) {
    $field_type_sql=$fields[$myf]['field_type_sql'];


    switch ($field_type_sql) {
      case 'tinyint(4)':
        $sql.=$myf."=".$myv.",";
        break;
      case 'int(11)':
        $sql.=$myf."=".$myv.",";
        break;
      case 'double':
        $sql.=$myf."=".number_format($myv,8,'.','').",";
        break;
      case 'varchar(250)':
      case 'text':
        $sql.=$myf."='".$db_link->escape_string($myv)."',";
        break;
      case 'datetime':
        if ($myv=='') 
          $sql.=$myf."=null,";
        else
          $sql.=$myf."='".$db_link->escape_string($myv)."',";
        break;
      case 'time':
        if ($myv=='') 
          $sql.=$myf."=null,";
        else
          $sql.=$myf."='".$db_link->escape_string($myv)."',";
        
        break;
      default:
        //debug_mail(false,'error sql',$myf.' '.$myv.print_r($fdata,true).print_r($gks_custom_prepare,true));
        $return = array('success' => false, 'message' => base64_encode('field_type_sql: '.$field_type_sql));
        echo json_encode($return); die();      
    }
        
  } 
  
  if ($sql=='') {
    $return = array('success' => true, 'message' => 'no fields to save'); return $return;
  }
  $sql=substr($sql,0,strlen($sql)-1);
  
  $sql="update ".$table_name." set 
  cf_mydate_edit=now(),
  cf_user_id_edit=".$my_wp_user_id.",
  cf_myip='".$db_link->escape_string($gkIP)."',
  
  ".$sql." where ".$field_name_id_current."=".$id;
  $result = $db_link->query($sql);        
  if (!$result) { 
    debug_mail(false,'error sql',$sql);
    $return = array('success' => false, 'message' => 'sql error');return $return;}
  
  
  //$return = array('success' => false, 'message' => base64_encode('<pre>'.$sql));
  //echo json_encode($return); die();
  
  //$return = array('success' => false, 'message' => base64_encode('<pre>'.print_r($gks_custom_prepare['table'],true).print_r($fields,true)));
  //echo json_encode($return); die();
  
  $return = array(
    'success' => true, 
    'message' => 'OK', 

  );
  
  return $return;
    
}

function gks_custom_table_list_header($gks_custom_prepare,$is_sub_list=false,$width_zero=true) {
  global $sortable;
  global $sortable_url;
  
  $html='';
  $cc_fields=0;
  foreach ($gks_custom_prepare['fields'] as $myf) {
    if ($myf['field_show_on_list']!=0) {
      $cc_fields++;
    }
  }
  if ($cc_fields==0) $cc_fields=100; //gia na vgei 1%
  
  foreach ($gks_custom_prepare['fields'] as $myf) {
    if ($myf['field_show_on_list']!=0) {
      $html.= '<th class="table-dark" scope="col" style="text-align: left   !important;" width="';
      if ($width_zero) $html.= '0'; else $html.= intval(100/$cc_fields);
      $html.= '%"  nowrap="nowrap">';
      if ($is_sub_list==false) {
        $html.=makeSortLink($sortable, $sortable_url, $_GET, 'so_'.$myf['gks_field_name'], $myf['field_label']);
      } else {
        $html.=$myf['field_label'];
      }
      $html.='</th>';
    }
  } 
  return $html;
}
function gks_custom_table_list_rows($gks_custom_prepare,$row) {
  global $GKS_NUMBER_FORMAT_THOUSAND;
  global $db_link;
  
  $html='';
  foreach ($gks_custom_prepare['fields'] as $myf) {
    if ($myf['field_show_on_list']!=0) {
    
      $item='';
      switch ($myf['field_type_id']) {   
        case 1: //	Ναι/Όχι	tinyint(4)
          $item='<td class="mytdcm gks_custom_field_list_'.$myf['gks_field_name'].'">'.
          (isset($row[$myf['gks_field_name']]) ? '<img src="img/'.($row[$myf['gks_field_name']]==0 ? '0':'1').'.png" border="0" width="16">' : '').
          '</td>';
          break;
        case 2: //	Αριθμός ακέραιος	int(11)
          $item='<td class="mytdcm gks_custom_field_list_'.$myf['gks_field_name'].'">'.
          ($row[$myf['gks_field_name']]!=0 ? number_format($row[$myf['gks_field_name']],0,'.',$GKS_NUMBER_FORMAT_THOUSAND) : '').
          '</td>';
          break;
        case 3: //	Αριθμός δεκαδικός	double
          $item='<td class="mytdcm gks_custom_field_list_'.$myf['gks_field_name'].'">'.
          myNumberFormatNo0Local($row[$myf['gks_field_name']],true).
          '</td>';
          break;
        case 4: //	Κείμενο	varchar(250)
          $item='<td class="mytdcml gks_custom_field_list_'.$myf['gks_field_name'].'">'.$row[$myf['gks_field_name']].'</td>';
          break;
        case 5: //	Κείμενο μεγάλο	text
          //$item='<td class="mytdcml">'.nl2br_gks($row[$myf['gks_field_name']]).'</td>';
          $item='<td class="gks_td08 gks_custom_field_list_'.$myf['gks_field_name'].'"><div class="gks_dive1"><div class="gks_dive2 mydivexpand">'.nl2br_gks($row[$myf['gks_field_name']]).'</div></div></td>';
          
          break;
        case 6: //	Ημερομηνία	datetime
          $item='<td class="mytdcm gks_custom_field_list_'.$myf['gks_field_name'].'">'.
          (empty($row[$myf['gks_field_name']]) ? '' : showDate(strtotime($row[$myf['gks_field_name']]),'d/m/Y',1)).
          '</td>';
          break;
        case 7: //	Ώρα	time
          $item='<td class="mytdcm gks_custom_field_list_'.$myf['gks_field_name'].'">'.
          (empty($row[$myf['gks_field_name']]) ? '' : showDate(strtotime($row[$myf['gks_field_name']]),'H:i:s',1)).
          '</td>';
          break;
        case 8: //	Ημερομηνία-Ώρα	datetime
          $item='<td class="mytdcm gks_custom_field_list_'.$myf['gks_field_name'].'">'.
          (empty($row[$myf['gks_field_name']]) ? '' : showDate(strtotime($row[$myf['gks_field_name']]),'d/m/Y H:i',1)).
          '</td>';
          break;
        case 9: //	Κείμενο μορφοποιημένο	text
          //$item='<td class="mytdcml">'.$row[$myf['gks_field_name']].'</td>';
          $item='<td class="gks_td08 gks_custom_field_list_'.$myf['gks_field_name'].'"><div class="gks_dive1"><div class="gks_dive2 mydivexpand">'.nl2br_gks($row[$myf['gks_field_name']]).'</div></div></td>';
          break;
        case 201: //	Τηλέφωνο	varchar(250)
          $item='<td class="mytdcm gks_custom_field_list_'.$myf['gks_field_name'].'"><a href="tel:'.$row[$myf['gks_field_name']].'">'.$row[$myf['gks_field_name']].'</a></td>';
          break;
        case 202: //	email	varchar(250)
          $item='<td class="mytdcm gks_custom_field_list_'.$myf['gks_field_name'].'"><a href="mailto:'.$row[$myf['gks_field_name']].'">'.$row[$myf['gks_field_name']].'</a></td>';
          break;
        case 203: //	url	varchar(250)
          $temp=$row[$myf['gks_field_name']];
          if (empty($temp)==false and strlen($temp)>=4 and substr($temp, 0,4)!='http') $temp='http://'.$temp;
          $item='<td class="mytdcm gks_custom_field_list_'.$myf['gks_field_name'].'"><a href="'.$temp.'" target="_blank">'.$row[$myf['gks_field_name']].'</a></td>';
          break;
        case 501: //	Επιλογή ενός από λίστα	int(11)
          $temp='';
          //$item='<td><pre>'.print_r($myf,true).'</pre></td>';
          if (isset($myf['field_attr']['options']) and is_array($myf['field_attr']['options'])) {
            foreach ($myf['field_attr']['options'] as $value) {
              if ($value['value']==$row[$myf['gks_field_name']]) {
                $temp=$value['text'];
              }
            }
          }
          $item='<td class="mytdcml gks_custom_field_list_'.$myf['gks_field_name'].'">'.$temp.'</td>';
          break;
        case 502: //	Επιλογή πολλών από λίστα	varchar(250)
          $temp=trim_gks($row[$myf['gks_field_name']]);
          if ($temp!='') {
            if (substr($temp, 0,4)==']][[') $temp=substr($temp, 4);
            if (substr($temp, strlen($temp)-4)==']][[') $temp=substr($temp, 0, strlen($temp)-4);
            //echo $temp;die();
            $temp=explode(']][[',$temp);
            $temp=implode('<br>',$temp);
          }
          $item='<td class="mytdcml gks_custom_field_list_'.$myf['gks_field_name'].'">'.$temp.'</td>';
          break;
        case 1001: //	Παραστατικό	int(11)
          break;
        case 1002: //	Ημερολόγιο	int(11)
          break;
        case 1003: //	Σειρά	int(11)
          break;
        case 1004: //	Εταιρεία	int(11)
          $item='<td class="mytdcml gks_custom_field_list_'.$myf['gks_field_name'].'"><a href="admin-company-item.php?id='.$row[$myf['gks_field_name']].'">'.$row[$myf['gks_field_name'].'_link'].'</a></td>';
          break;
        case 1005: //	Υποκατάστημα	int(11)
          $item='<td class="mytdcml gks_custom_field_list_'.$myf['gks_field_name'].'">';
          if ($row[$myf['gks_field_name']]==0) {
             //$item.=gks_lang('Κεντρικό');
          } else if ($row[$myf['gks_field_name']]>0) {
             $item.='<a href="admin-company-sub-item.php?id='.$row[$myf['gks_field_name']].'">'.$row[$myf['gks_field_name'].'_link'].'</a>';
          }
          $item.='</td>';
          //echo $item;die();
          break;
        case 1006: //	Ευκαιρία	int(11)
          $item='<td class="mytdcml gks_custom_field_list_'.$myf['gks_field_name'].'"><a href="admin-crm-lead-item.php?id='.$row[$myf['gks_field_name']].'">'.$row[$myf['gks_field_name'].'_link'].'</a></td>';
          break;
        case 1007: //	Είδος	int(11)
          $item='<td class="mytdcml gks_custom_field_list_'.$myf['gks_field_name'].'"><a href="admin-products-item.php?id='.$row[$myf['gks_field_name']].'">'.$row[$myf['gks_field_name'].'_link'].'</a></td>';
          break;
        case 1008: //	Κατηγορία Είδους	int(11)
          $item='<td class="mytdcml gks_custom_field_list_'.$myf['gks_field_name'].'"><a href="admin-product-categories-item.php?id='.$row[$myf['gks_field_name']].'">'.$row[$myf['gks_field_name'].'_link'].'</a></td>';
          break;
        case 1009: //	Ξενοδοχείο	int(11)
          break;
        case 1010: //	Διαθεσιμότητα	int(11)
          break;
        case 1011: //	Όροφος	int(11)
          break;
        case 1012: //	Τιμή δωματίου	int(11)
          break;
        case 1013: //	Κράτηση	int(11)
          break;
        case 1014: //	Δωμάτιο	int(11)
          $item='<td class="mytdcml gks_custom_field_list_'.$myf['gks_field_name'].'"><a href="admin-hotel-room-item.php?id='.$row[$myf['gks_field_name']].'">'.$row[$myf['gks_field_name'].'_link'].'</a></td>';
          break;
        case 1015: //	Τύπος δωματίου	int(11)
          break;
        case 1016: //	Παραγγελία	int(11)
          break;
        case 1017: //	Φόρμα Εκτύπωσης	int(11)
          $item='<td class="mytdcml gks_custom_field_list_'.$myf['gks_field_name'].'"><a href="admin-print_forms-item.php?id='.$row[$myf['gks_field_name']].'">'.$row[$myf['gks_field_name'].'_link'].'</a></td>';
          break;
        case 1018: //	Εργασία παραγωγής	int(11)
          $item='<td class="mytdcml gks_custom_field_list_'.$myf['gks_field_name'].'"><a href="admin-production-ergasies-item.php?id='.$row[$myf['gks_field_name']].'">'.$row[$myf['gks_field_name'].'_link'].'</a></td>';
          break;
        case 1019: //	Πόστο	int(11)
          $item='<td class="mytdcml gks_custom_field_list_'.$myf['gks_field_name'].'"><a href="admin-production-posta-item.php?id='.$row[$myf['gks_field_name']].'">'.$row[$myf['gks_field_name'].'_link'].'</a></td>';
          break;
        case 1020: //	Ομάδα Επαφών	int(11)
          $item='<td class="mytdcml gks_custom_field_list_'.$myf['gks_field_name'].'"><a href="admin-usersgroups-item.php?id='.$row[$myf['gks_field_name']].'">'.$row[$myf['gks_field_name'].'_link'].'</a></td>';
          break;
        case 1021: //	Αποθήκη	int(11)
          break;
        case 1022: //	Επαφή	int(11)
          //echo '<pre>';print_r($row);
          //print '|a '.$myf['gks_field_name'].'|';
          //print '|b '.$myf['gks_field_name'].'_link'.'|';
          //print '|c '.$row[$myf['gks_field_name']].'|';
          //print '|d '.$row[$myf['gks_field_name'].'_link'].'|';
          
          $item='<td class="mytdcml gks_custom_field_list_'.$myf['gks_field_name'].'"><a href="admin-users-item.php?id='.$row[$myf['gks_field_name']].'">'.$row[$myf['gks_field_name'].'_link'].'</a></td>';
          break;
        case 1023: //	Πληρωμή	int(11)
          break;
        case 1024: //	eshop	int(11)
          break;
        case 1025: //	Μάρκα	int(11)
          $item='<td class="mytdcml gks_custom_field_list_'.$myf['gks_field_name'].'"><a href="admin-product-brands-item.php?id='.$row[$myf['gks_field_name']].'">'.$row[$myf['gks_field_name'].'_link'].'</a></td>';
          break;
        case 1026: //	Εργασία CRM	int(11)
          $item='<td class="mytdcml gks_custom_field_list_'.$myf['gks_field_name'].'"><a href="admin-crm-task-item.php?id='.$row[$myf['gks_field_name']].'">'.$row[$myf['gks_field_name'].'_link'].'</a></td>';
          break;
        case 1027: // Συσκευή	int(11)
          $item='<td class="mytdcml gks_custom_field_list_'.$myf['gks_field_name'].'"><a href="admin-crm-machine-item.php?id='.$row[$myf['gks_field_name']].'">'.$row[$myf['gks_field_name'].'_link'].'</a></td>';
          break;
        case 1028: //	Περίσταση	int(11)
          $fname=$myf['gks_field_name'];
          $occasion_title = '';
          $temp = (isset($row[$fname.'_link_type_descr']) ? trim_gks($row[$fname.'_link_type_descr']) : '');         if ($temp!='') $occasion_title.=$temp.' / ';
          $temp = (isset($row[$fname.'_link']) ? trim_gks($row[$fname.'_link']) : '');      if ($temp!='') $occasion_title.=$temp.' / ';
          $temp = (isset($row[$fname.'_link_an']) ? trim_gks($row[$fname.'_link_an']) : ''); if ($temp!='') $occasion_title.=$temp.' / ';
          $temp = (isset($row[$fname.'_link_mydate']) ? trim_gks($row[$fname.'_link_mydate']) : '');   if ($temp!='') $occasion_title.=showDate(strtotime($temp), 'd/m/Y H:i', 1) .' / ';
          if ($occasion_title!='') $occasion_title=substr($occasion_title, 0, strlen($occasion_title) - 3);
  
        
          $item='<td class="mytdcml gks_custom_field_list_'.$myf['gks_field_name'].'"><a href="admin-orders-occasion-item.php?id='.$row[$myf['gks_field_name']].'">'.$occasion_title.'</a></td>';
          break;

        case 1222: //	Επαφές	varchar(250)
          $temp=trim_gks($row[$myf['gks_field_name']]);
          if ($temp!='') {
            if (substr($temp, 0,4)==']][[') $temp=substr($temp, 4);
            if (substr($temp, strlen($temp)-4)==']][[') $temp=substr($temp, 0, strlen($temp)-4);
            //echo $temp;die();
            $temps=explode(']][[',$temp);
            $temp_ids=array();
            foreach ($temps as $value) {
              $value=intval($value);
              if ($value>0) $temp_ids[]=$value;
            } 
            if (count($temp_ids)>0) {
              $sql_case="select gks_nickname from ".GKS_WP_TABLE_PREFIX."users where ID in (".implode(',',$temp_ids).") order by gks_nickname";
              $result_case = $db_link->query($sql_case);        
              if (!$result_case) { 
                debug_mail(false,'error sql',$sql_case);
                //$return = array('success' => false, 'message' => base64_encode('error sql'));
                //echo json_encode($return); die();
              }
              $temp=array();
              if ($result_case) {
                while ($row_case = $result_case->fetch_assoc()) {
                  $temp[]=$row_case['gks_nickname'];
                }
              }
              $temp=implode('<br>',$temp);
            }
            
          }
          $item='<td class="mytdcml gks_custom_field_list_'.$myf['gks_field_name'].'">'.$temp.'</td>';
          break;

        default:      // default actions 
        
          
      }
    
      if ($item=='') {
        $item='<td class="mytdcm" style="background-color: red;">'.$row[$myf['gks_field_name']].'</td>';
      }
    
      //echo '<pre>';print_r($myf);die();
      $html.=$item."\n"; 
    }
  } 
  return $html;
}


function gks_custom_table_pivot_header($gks_custom_prepare,$is_sub_list=false) {
  global $sortable;
  global $sortable_url;
  
  $ret=array();
  foreach ($gks_custom_prepare['fields'] as $myf) {
    if ($myf['field_show_on_list']!=0) {
      //print_r($myf);die();
      switch ($myf['field_type_id']) {
        case 6: //	Ημερομηνία	datetime
          $ret[]=$myf['field_label'].' '.gks_lang('Έτος','part3');
          $ret[]=$myf['field_label'].' '.gks_lang('Μήνας','part3');
          $ret[]=$myf['field_label'].' '.gks_lang('Ημέρα','part3');
          break;
        case 7: //	Ώρα	time
          $ret[]=$myf['field_label'].' '.gks_lang('Ώρα','part3');
          $ret[]=$myf['field_label'].' '.gks_lang('Λεπτά','part3');
          break;
        case 8: //	Ημερομηνία-Ώρα	datetime
          $ret[]=$myf['field_label'].' '.gks_lang('Έτος','part3');
          $ret[]=$myf['field_label'].' '.gks_lang('Μήνας','part3');
          $ret[]=$myf['field_label'].' '.gks_lang('Ημέρα','part3');
          $ret[]=$myf['field_label'].' '.gks_lang('Ώρα','part3');
          $ret[]=$myf['field_label'].' '.gks_lang('Λεπτά','part3');
          break;
          
        default;
          $ret[]=$myf['field_label'];
          break;
      }
    }
  } 
  return $ret;
}


function gks_custom_table_pivot_rows($gks_custom_prepare,$row) {
  global $GKS_NUMBER_FORMAT_THOUSAND;
  global $db_link;
  
  $html='';
  foreach ($gks_custom_prepare['fields'] as $myf) {
    if ($myf['field_show_on_list']!=0) {
    
      $item='';
      switch ($myf['field_type_id']) {
        case 1: //	Ναι/Όχι	tinyint(4)
          $item='"'.(isset($row[$myf['gks_field_name']]) ? ($row[$myf['gks_field_name']]==0 ? gks_lang('Όχι'):gks_lang('Ναι')) : '').'",';
          break;
        case 2: //	Αριθμός ακέραιος	int(11)
        case 3: //	Αριθμός δεκαδικός	double
          $item='"'.$row[$myf['gks_field_name']].'",';
          break;
        case 4: //	Κείμενο	varchar(250)
        case 5: //	Κείμενο μεγάλο	text
        case 9: //	Κείμενο μορφοποιημένο	text
        case 201: //	Τηλέφωνο	varchar(250)
        case 202: //	email	varchar(250)
        case 203: //	url	varchar(250)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name']]).'",';
          break;
        case 6: //	Ημερομηνία	datetime
          if (empty($row[$myf['gks_field_name']])) {
            $item=',,,';
          } else {
            $item=showDate(strtotime($row[$myf['gks_field_name']]), 'Y', 1).','.
                  showDate(strtotime($row[$myf['gks_field_name']]), 'm', 1).','.
                  showDate(strtotime($row[$myf['gks_field_name']]), 'd', 1).',';
          }
          break;
        case 7: //	Ώρα	time
          if (empty($row[$myf['gks_field_name']])) {
            $item=',,';
          } else {
            $item=showDate(strtotime($row[$myf['gks_field_name']]), 'H', 1).','.
                  showDate(strtotime($row[$myf['gks_field_name']]), 'i', 1).',';
          }
          break;
        case 8: //	Ημερομηνία-Ώρα	datetime
          if (empty($row[$myf['gks_field_name']])) {
            $item=',,,,,';
          } else {
            $item=showDate(strtotime($row[$myf['gks_field_name']]), 'Y', 1).','.
                  showDate(strtotime($row[$myf['gks_field_name']]), 'm', 1).','.
                  showDate(strtotime($row[$myf['gks_field_name']]), 'd', 1).','.
                  showDate(strtotime($row[$myf['gks_field_name']]), 'H', 1).','.
                  showDate(strtotime($row[$myf['gks_field_name']]), 'i', 1).',';
          }
          break;
        case 501: //	Επιλογή ενός από λίστα	int(11)
          $temp='';
          //$item='<td><pre>'.print_r($myf,true).'</pre></td>';
          if (isset($myf['field_attr']['options']) and is_array($myf['field_attr']['options'])) {
            foreach ($myf['field_attr']['options'] as $value) {
              if ($value['value']==$row[$myf['gks_field_name']]) {
                $temp=$value['text'];
              }
            }
          }
          if ($temp=='') $temp='--';
          $item='"'.$temp.'",';
          break;
        case 502: //	Επιλογή πολλών από λίστα	varchar(250)
          $temp=trim_gks($row[$myf['gks_field_name']]);
          if ($temp!='') {
            if (substr($temp, 0,4)==']][[') $temp=substr($temp, 4);
            if (substr($temp, strlen($temp)-4)==']][[') $temp=substr($temp, 0, strlen($temp)-4);
            //echo $temp;die();
            $temp=explode(']][[',$temp);
            $temp=implode(', ',$temp);
          }
          if ($temp=='') $temp='--';
          $item='"'.gks_csv_txt($temp).'",';
          break;
        case 1001: //	Παραστατικό	int(11)
          $item='"'.$row[$myf['gks_field_name']].'",';
          break;
        case 1002: //	Ημερολόγιο	int(11)
          $item='"'.$row[$myf['gks_field_name']].'",';
          break;
        case 1003: //	Σειρά	int(11)
          $item='"'.$row[$myf['gks_field_name']].'",';
          break;
        case 1004: //	Εταιρεία	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1005: //	Υποκατάστημα	int(11)
          if ($row[$myf['gks_field_name']]==0) {
             $item='"'.gks_lang('Κεντρικό').'",';
          } else if ($row[$myf['gks_field_name']]>0) {
             $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          }
          break;
        case 1006: //	Ευκαιρία	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1007: //	Είδος	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1008: //	Κατηγορία Είδους	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1009: //	Ξενοδοχείο	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1010: //	Διαθεσιμότητα	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1011: //	Όροφος	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1012: //	Τιμή δωματίου	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1013: //	Κράτηση	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1014: //	Δωμάτιο	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1015: //	Τύπος δωματίου	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1016: //	Παραγγελία	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1017: //	Φόρμα Εκτύπωσης	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1018: //	Εργασία παραγωγής	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1019: //	Πόστο	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1020: //	Ομάδα Επαφών	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1021: //	Αποθήκη	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1022: //	Επαφή	int(11)
          //print_r($myf);print_r($row);die();
          //print $row[$myf['gks_field_name'].'_link'];echo '|';
          //$item='"",';
          //if (isset($row[$myf['gks_field_name'].'_link'])) 
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          //else print_r($row);
          
          break;
        case 1023: //	Πληρωμή	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1024: //	eshop	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1025: //	Μάρκα	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1026: //	Εργασία CRM	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1027: // Συσκευή	int(11)
          $item='"'.gks_csv_txt($row[$myf['gks_field_name'].'_link']).'",';
          break;
        case 1028: //	Περίσταση	int(11)
          $fname=$myf['gks_field_name'];
          $occasion_title = '';
          $temp = (isset($row[$fname.'_link_type_descr']) ? trim_gks($row[$fname.'_link_type_descr']) : '');         if ($temp!='') $occasion_title.=$temp.' / ';
          $temp = (isset($row[$fname.'_link']) ? trim_gks($row[$fname.'_link']) : '');      if ($temp!='') $occasion_title.=$temp.' / ';
          $temp = (isset($row[$fname.'_link_an']) ? trim_gks($row[$fname.'_link_an']) : ''); if ($temp!='') $occasion_title.=$temp.' / ';
          $temp = (isset($row[$fname.'_link_mydate']) ? trim_gks($row[$fname.'_link_mydate']) : '');   if ($temp!='') $occasion_title.=showDate(strtotime($temp), 'd/m/Y H:i', 1) .' / ';
          if ($occasion_title!='') $occasion_title=substr($occasion_title, 0, strlen($occasion_title) - 3);
  
        
          $item='"'.$occasion_title.'",';
          break;

        case 1222: //	Επαφές	varchar(250)
          $temp=trim_gks($row[$myf['gks_field_name']]);
          if ($temp!='') {
            if (substr($temp, 0,4)==']][[') $temp=substr($temp, 4);
            if (substr($temp, strlen($temp)-4)==']][[') $temp=substr($temp, 0, strlen($temp)-4);
            //echo $temp;die();
            $temps=explode(']][[',$temp);
            $temp_ids=array();
            foreach ($temps as $value) {
              $value=intval($value);
              if ($value>0) $temp_ids[]=$value;
            } 
            if (count($temp_ids)>0) {
              $sql_case="select gks_nickname from ".GKS_WP_TABLE_PREFIX."users where ID in (".implode(',',$temp_ids).") order by gks_nickname";
              $result_case = $db_link->query($sql_case);        
              if (!$result_case) { 
                debug_mail(false,'error sql',$sql_case);
                //$return = array('success' => false, 'message' => base64_encode('error sql'));
                //echo json_encode($return); die();
              }
              $temp=array();
              if ($result_case) {
                while ($row_case = $result_case->fetch_assoc()) {
                  $temp[]=$row_case['gks_nickname'];
                }
              }
              $temp=implode(', ',$temp);
            }
            
          }
          $item='"'.$temp.'",';
          break;

        default:      // default actions 
        
          
      }
    
      if ($item=='') {
        $item='"'.gks_csv_txt($row[$myf['gks_field_name']]).'",';
      }
      //echo '<pre>';print_r($myf);die();
      $html.=$item; 
    }
  } 
  return $html;  
}

function gks_shortcode_prefix_for_custom_table() {
  global $db_link;
  while (true) {
    
    do {
      $shortcode_prefix = gks_random_string(3);  
      if ($shortcode_prefix!='xxx' and $shortcode_prefix[0]!='s') break;
    } while (true); 
    $sql = "SELECT shortcode_prefix from gks_custom_table where shortcode_prefix='".$db_link->escape_string($shortcode_prefix)."' limit 1";
    $result = $db_link->query($sql);
    if ($result->num_rows == 0) {
      $sql = "SELECT shorturl from gks_urlshort where shorturl like'".$db_link->escape_string($shortcode_prefix)."%' limit 1";
      $result = $db_link->query($sql);
      if ($result->num_rows == 0) {
        return $shortcode_prefix; 
      }
    }
  }
}

function gks_custom_sxolio_log($crow_old,$crow_new) {
  if (isset($crow_old['fields'])==false) return '';
  if (isset($crow_new['fields'])==false) return '';
  
  //print '<pre>ooooo old ';print_r($crow_old);die();
  //print '<pre>ooooo new ';print_r($crow_new);die();
  $sxolio_log='';
  foreach ($crow_old['fields'] as $key => $vvv) {
    if (isset($crow_new['fields'][$key])) {
      $va='';$vb='';
      if (isset($crow_old['fields'][$key]['print']))
         $va=$crow_old['fields'][$key]['print'];
      if (isset($crow_new['fields'][$key]['print']))
         $vb=$crow_new['fields'][$key]['print'];
         
      if (trim_gks($va) != trim_gks($vb)) 
        $sxolio_log.=$vvv['field_label'].': <b>'.$va.'</b> [[-r]] <b>'.$vb.'</b>'.'<br>';
    }
  }
  
  //print '<pre>bbbb new '.$sxolio_log;die();
  
  return $sxolio_log;
}



