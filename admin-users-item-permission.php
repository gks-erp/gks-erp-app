<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


db_open();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);

$userrole='';
if (isset($my_wp_user_info->roles)) {
  if (in_array('adminmy',$my_wp_user_info->roles))  $userrole='adminmy';
  if (in_array('administrator',$my_wp_user_info->roles))  $userrole='administrator';
}
if ($userrole=='') {header('Location: /my/admin-deny.php'); die(); }


//echo '<pre>'; echo gks_notification_userperm_internal_users();die();

$sql="select gks_nickname from ".GKS_WP_TABLE_PREFIX."users where ID=".$id;
$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'error sql',$sql);  die('sql error');}
if ($result->num_rows!=1) {
  debug_mail(false,'record not found sql',$sql); 
  die('no record found');  
}
$row = $result->fetch_assoc();
$gks_nickname=$row['gks_nickname'];

$mynot='';

if ($GKS_HOTEL_BACKEND==false) {
  $mynot.=" and card_title <>'Ξενοδοχείο'";
}
if (GKS_TRANSFER==false) {
  $mynot.=" and card_title <>'Transfer'";
}

if ($GKS_CRM_ENABLE==false) {
  $mynot.=" and card_title <>'CRM'";
} else {
  if ($GKS_CRM_LEADS_ENABLE==false) {
    $mynot.=" and table_name not in ('gks_crm_leads','gks_crm_leads_status')";
  }
  if ($GKS_CRM_TASKS_ENABLE==false) {
    $mynot.=" and table_name not in ('gks_crm_tasks','gks_crm_tasks_status')";
  }
  if ($GKS_CRM_MACHINE_ENABLE==false) {
    $mynot.=" and table_name not in ('gks_crm_machine')";
  }
}
if ($GKS_WARE_HOUSE_ENABLE==false) {
  $mynot.=" and card_title <>'Αποθήκη'";
} else {
  
}


if ($GKS_ORDERS_ENABLE==false) {
  $mynot.=" and card_title <>'Πωλήσεις'";
} else {

  if ($GKS_ORDERS_OCCASION==false) {
    $mynot.=" and table_name not in ('gks_orders_occasion')";
  }
  
}

if ($GKS_ORDERS_PRODUCTION==false) {
  $mynot.=" and card_title <>'Παραγωγή'";
} else {
  
}

if ($GKS_ACC_ENABLE==false) {
  $mynot.=" and card_title <>'Λογιστική'";
} else {
  
}
if ($GKS_ASSETS_ENABLE==false) {
  $mynot.=" and card_title <>'Πάγια'";
} else {
  
}


if ($mynot!='') $mynot=' where '. substr($mynot, 4);


$sql="SELECT id_permission_object, card_title, parent_id, table_name, object_name, user_perm.*
FROM gks_permission_object 
LEFT JOIN (
  SELECT * FROM gks_permission_user WHERE user_id=".$id."
) AS user_perm ON gks_permission_object.id_permission_object = user_perm.permission_object_id
".$mynot."
ORDER BY gks_permission_object.sortorder;";
$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'error sql',$sql);  die('sql error');}

$perms=array();  
while ($row = $result->fetch_assoc()) {
  if (isset($perms[$row['card_title']])==false) {
    $perms[$row['card_title']]=array(
      'card_title'=>$row['card_title'],
      'objects' => array(),
    );
  }
  $perms[$row['card_title']]['objects'][]=$row;
}

//print '<pre>';print_r($perms);die();

$sql="select meta_value from ".GKS_WP_TABLE_PREFIX."usermeta where user_id=".$id." and meta_key='".GKS_WP_TABLE_PREFIX."capabilities'";
$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'error sql',$sql);  die('sql error');}

$user_roles=array();
if ($result->num_rows==1) {
  $row = $result->fetch_assoc();
  $temp=trim_gks($row['meta_value']);
  $temp=unserialize($temp);
  if (is_array($temp) and count($temp)>0) $user_roles=$temp;
}


$my_page_title=gks_lang('Δικαιώματα χρήστη').': '.$gks_nickname;


$nav_active_array=array('manage','manage_users'); 
if ($my_wp_user_id==$id) {
  $nav_active_array=array('my_permission'); 
  $my_page_title=gks_lang('Το Δικαιώματά μου');
}



stat_record();

include_once('_my_header_admin.php');
?>

<style>
.gks_flock {
  height: unset !important;
}
.gks_eidos_label {
  font-size:0.8rem;
  /* font-weight: bold; */
  padding: 5px 0px 5px 0px;
  border-radius: 10px;
  text-align: center;
  margin-top: 2px;
}
.gks_int_cond {
  position: relative;
  top: 2px;  
}

</style>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-6" style="text-align:center">
      <h3><?php echo $my_page_title;?></h3>
    </div>
    <div class="col-md-6" style="text-align:center">
      <a class="btn btn-primary" href="admin-users-item.php?id=<?php echo $id;?>#user_roles_div"><?php echo gks_lang('Επιστροφή');?></a>
    </div>    
  </div>
</div>

<div class="container-fluid">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <h5><?php echo gks_lang('Ρόλοι του χρήστη');?>:</h5>
      <p><?php 
      
      $gks_wp_system_roles = gks_wp_system_roles_func();
      $temp='';
      foreach ($gks_wp_system_roles as $role_item) {
        if (isset($user_roles[$role_item['id']]) and $user_roles[$role_item['id']]==1)  {
          $temp.= $role_item['name'].' | ';
        }
      }
      if ($temp!='') $temp=substr($temp, 0, strlen($temp)-3);
      echo $temp;
      ?></p>
    </div>
  </div>
</div>


<div class="container-fluid" style="margin-top:20px;margin-bottom:10px;">
  <div class="row">
    <div class="col-md-12 text-center">
      <button type="button" class="btn btn-lg btn-primary" id="perm_global_all"  ><?php echo gks_lang('Όλα');?></button>
      <button type="button" class="btn btn-lg btn-primary" id="perm_global_none" ><?php echo gks_lang('Κανένα');?></button>
    </div>
  </div>
</div>            
            
<div id="mypostform">
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">

      
    <?php 
    $cardaa=0;
    foreach ($perms as $card) { 
      $cardaa++;
      
      ?>

    
        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo $card['card_title']?>
        </div>
        <div class="card-body" <?php echo gks_card_body('card'.greeklish($card['card_title']));?>>        
 
          <div class="form-group row">
            <div class="col-md-12 text-center">
              <button type="button" class="btn btn-primary perm_card_all"  ><?php echo gks_lang('Όλα');?></button>
              <button type="button" class="btn btn-primary perm_card_none" ><?php echo gks_lang('Κανένα');?></button>
            </div>
          </div>
  
          <div class="form-group row">
            <div class="col-12 col-md-3">
              <div class="table-dark gks_eidos_label">
                <?php echo gks_lang('Αντικείμενο');?>
              </div>
            </div>
            <div class="col-2 col-md-1 text-md-center offset-1 offset-md-0">
              <div class="table-dark gks_eidos_label">
                <?php echo gks_lang('Προβολή');?>
              </div>
            </div>
            <div class="col-2 col-md-1 text-md-center"">
              <div class="table-dark gks_eidos_label">
                <?php echo gks_lang('Επεξεργασία');?>
              </div>
            </div>
            <div class="col-2 col-md-1 v">
              <div class="table-dark gks_eidos_label">
                <?php echo gks_lang('Προσθήκη');?>
              </div>
            </div>
            <div class="col-2 col-md-1 text-md-center"">
              <div class="table-dark gks_eidos_label">
                <?php echo gks_lang('Διαγραφή');?>
              </div>
            </div>
            <div class="col-2 col-md-1 text-md-center">
              <div class="table-dark gks_eidos_label">
                <?php echo gks_lang('Επιλογή');?>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="table-dark gks_eidos_label">
                <?php echo gks_lang('Φίλτρα');?>
              </div>
            </div>
          </div>
          
          <div class="form-group row">
            <div class="col-12 col-md-3">
              
            </div>
            <div class="col-2 col-md-1 text-md-center offset-1 offset-md-0">
              <button type="button" class="btn btn-sm btn-primary perm_view_all"  style="margin:1px;padding:2px"><?php echo gks_lang('Όλα');?></button>
              <button type="button" class="btn btn-sm btn-primary perm_view_none" style="margin:1px;padding:2px"><?php echo gks_lang('Κανένα');?></button>
            </div>
            <div class="col-2 col-md-1 text-md-center"">
              <button type="button" class="btn btn-sm btn-primary perm_edit_all"  style="margin:1px;padding:2px"><?php echo gks_lang('Όλα');?></button>
              <button type="button" class="btn btn-sm btn-primary perm_edit_none" style="margin:1px;padding:2px"><?php echo gks_lang('Κανένα');?></button>
            </div>
            <div class="col-2 col-md-1 v">
              <button type="button" class="btn btn-sm btn-primary perm_add_all"  style="margin:1px;padding:2px"><?php echo gks_lang('Όλα');?></button>
              <button type="button" class="btn btn-sm btn-primary perm_add_none" style="margin:1px;padding:2px"><?php echo gks_lang('Κανένα');?></button>
            </div>
            <div class="col-2 col-md-1 text-md-center"">
              <button type="button" class="btn btn-sm btn-primary perm_delete_all"  style="margin:1px;padding:2px"><?php echo gks_lang('Όλα');?></button>
              <button type="button" class="btn btn-sm btn-primary perm_delete_none" style="margin:1px;padding:2px"><?php echo gks_lang('Κανένα');?></button>
            </div>
            <div class="col-2 col-md-1 text-md-center">
              <button type="button" class="btn btn-sm btn-primary perm_autocomplete_all"  style="margin:1px;padding:2px"><?php echo gks_lang('Όλα');?></button>
              <button type="button" class="btn btn-sm btn-primary perm_autocomplete_none" style="margin:1px;padding:2px"><?php echo gks_lang('Κανένα');?></button>
            </div>
            <div class="col-12 col-md-4">
              
            </div>
          </div>
 
 
          <?php foreach ($card['objects'] as $obj) { ?>
          
            
          
          <div class="form-group row">
            <label class="col-12 col-md-3 col-form-label form-control-sm text-md-center1 gks_flock row_perm" data-oid="<?php echo $obj['id_permission_object'];?>"><?php echo $obj['object_name'];?></label>
            <div class="col-2 col-md-1 text-md-center offset-1 offset-md-0">
              <input type="checkbox" value="1" <?php if (isset($obj['perm_view']) and $obj['perm_view']==1) echo ' checked '; ?> class="switchery1_this perm_view" data-oid="<?php echo $obj['id_permission_object'];?>">
            </div>
            <div class="col-2 col-md-1 text-md-center">
              <input type="checkbox" value="1" <?php if (isset($obj['perm_edit']) and $obj['perm_edit']==1) echo ' checked '; ?> class="switchery1_this perm_edit" data-oid="<?php echo $obj['id_permission_object'];?>">
            </div>
            <div class="col-2 col-md-1 text-md-center">
              <input type="checkbox" value="1" <?php if (isset($obj['perm_add']) and $obj['perm_add']==1) echo ' checked '; ?> class="switchery1_this perm_add" data-oid="<?php echo $obj['id_permission_object'];?>">
            </div>
            <div class="col-2 col-md-1 text-md-center">
              <input type="checkbox" value="1" <?php if (isset($obj['perm_delete']) and $obj['perm_delete']==1) echo ' checked '; ?> class="switchery1_this perm_delete" data-oid="<?php echo $obj['id_permission_object'];?>">
            </div>
            <div class="col-2 col-md-1 text-md-center">
              <input type="checkbox" value="1" <?php if (isset($obj['perm_autocomplete']) and $obj['perm_autocomplete']==1) echo ' checked '; ?> class="switchery1_this perm_autocomplete" data-oid="<?php echo $obj['id_permission_object'];?>">
            </div>
            <div class="col-12 col-md-4">
              <?php
              
              switch ($obj['table_name']) {   
                case 'gks_company':
                  $value='';
                  $rdata=trim_gks($obj['perm_condition01']);
                  if ($rdata!='') {
                    $rdata=unserialize($rdata);
                    if (count($rdata)>0) {
                      $sqltags="select id_company as myid,company_title as mytag from gks_company where id_company in (".implode(',',$rdata).") order by company_sortorder";
                      $resulttags = $db_link->query($sqltags);        
                      if (!$resulttags) {debug_mail(false,'error sql',$sqltags);echo 'sql error';die();}
                      $rdata=array();
                      while ($rowtag = $resulttags->fetch_assoc()) $rdata[]=$rowtag['mytag'].' (#'.$rowtag['myid'].')';  
                      if (count($rdata)>0) $value=implode(']][[',$rdata);
                    }
                  }
                  echo '<input id="perm_condition01_gks_company" value="'.htmlspecialchars_gks($value).'" class="perm_condition01 form-control form-control-sm myneedsave" type="text" data-oid="'.$obj['id_permission_object'].'">';
                  break;  
                case 'gks_company_subs':
                  $value='';
                  $rdata=trim_gks($obj['perm_condition01']);
                  if ($rdata!='') {
                    $rdata=unserialize($rdata);
                    if (count($rdata)>0) {
                      $sqltags="select id_company_sub as myid,company_sub_title as mytag from gks_company_subs where id_company_sub in (".implode(',',$rdata).") order by company_sub_sortorder";
                      $resulttags = $db_link->query($sqltags);        
                      if (!$resulttags) {debug_mail(false,'error sql',$sqltags);echo 'sql error';die();}
                      if (in_array(0,$rdata)) $rdata=array(gks_lang('Κεντρικό').' (#0)'); else $rdata=array();
                      while ($rowtag = $resulttags->fetch_assoc()) $rdata[]=$rowtag['mytag'].' (#'.$rowtag['myid'].')'; 
                      if (count($rdata)>0) $value=implode(']][[',$rdata);
                    }
                  }
                  echo '<input id="perm_condition01_gks_company_subs" value="'.htmlspecialchars_gks($value).'" class="perm_condition01 form-control form-control-sm myneedsave" type="text" data-oid="'.$obj['id_permission_object'].'">';
                  break;  
                case 'gks_acc_journal':
                  $value='';
                  $rdata=trim_gks($obj['perm_condition01']);
                  if ($rdata!='') {
                    $rdata=unserialize($rdata);
                    if (count($rdata)>0) {
                      $sqltags="select id_acc_journal as myid,acc_journal_descr as mytag from gks_acc_journal where id_acc_journal in (".implode(',',$rdata).") order by sortorder";
                      $resulttags = $db_link->query($sqltags);        
                      if (!$resulttags) {debug_mail(false,'error sql',$sqltags);echo 'sql error';die();}
                      $rdata=array();
                      while ($rowtag = $resulttags->fetch_assoc()) $rdata[]=$rowtag['mytag'].' (#'.$rowtag['myid'].')';  
                      if (count($rdata)>0) $value=implode(']][[',$rdata);
                    }
                  }
                  echo '<input id="perm_condition01_gks_acc_journal" value="'.htmlspecialchars_gks($value).'" class="perm_condition01 form-control form-control-sm myneedsave" type="text" data-oid="'.$obj['id_permission_object'].'">';
                  break;  
                case 'gks_acc_seires':
                  $value='';
                  $rdata=trim_gks($obj['perm_condition01']);
                  if ($rdata!='') {
                    $rdata=unserialize($rdata);
                    if (count($rdata)>0) {
                      $sqltags="select id_acc_seira as myid,seira_descr as mytag from gks_acc_seires where id_acc_seira in (".implode(',',$rdata).") order by sortorder";
                      $resulttags = $db_link->query($sqltags);        
                      if (!$resulttags) {debug_mail(false,'error sql',$sqltags);echo 'sql error';die();}
                      $rdata=array();
                      while ($rowtag = $resulttags->fetch_assoc()) $rdata[]=$rowtag['mytag'].' (#'.$rowtag['myid'].')';  
                      if (count($rdata)>0) $value=implode(']][[',$rdata);
                    }
                  }
                  echo '<input id="perm_condition01_gks_acc_seires" value="'.htmlspecialchars_gks($value).'" class="perm_condition01 form-control form-control-sm myneedsave" type="text" data-oid="'.$obj['id_permission_object'].'">';
                  break;  
                case 'gks_warehouses':
                  $value='';
                  $rdata=trim_gks($obj['perm_condition01']);
                  if ($rdata!='') {
                    $rdata=unserialize($rdata);
                    if (count($rdata)>0) {
                      $sqltags="select id_warehouse as myid,warehouse_name as mytag from gks_warehouses where id_warehouse in (".implode(',',$rdata).") order by warehouse_sortorder";
                      $resulttags = $db_link->query($sqltags);        
                      if (!$resulttags) {debug_mail(false,'error sql',$sqltags);echo 'sql error';die();}
                      $rdata=array();
                      while ($rowtag = $resulttags->fetch_assoc()) $rdata[]=$rowtag['mytag'].' (#'.$rowtag['myid'].')';  
                      if (count($rdata)>0) $value=implode(']][[',$rdata);
                    }
                  }
                  echo '<input id="perm_condition01_gks_warehouses" value="'.htmlspecialchars_gks($value).'" class="perm_condition01 form-control form-control-sm myneedsave" type="text" data-oid="'.$obj['id_permission_object'].'">';
                  break;  
                case 'gks_print_forms':
                  $value='';
                  $rdata=trim_gks($obj['perm_condition01']);
                  if ($rdata!='') {
                    $rdata=unserialize($rdata);
                    if (count($rdata)>0) {
                      $sqltags="select id_print_form as myid,print_form_descr as mytag from gks_print_forms where id_print_form in (".implode(',',$rdata).") order by sortorder";
                      $resulttags = $db_link->query($sqltags);        
                      if (!$resulttags) {debug_mail(false,'error sql',$sqltags);echo 'sql error';die();}
                      $rdata=array();
                      while ($rowtag = $resulttags->fetch_assoc()) $rdata[]=$rowtag['mytag'].' (#'.$rowtag['myid'].')';  
                      if (count($rdata)>0) $value=implode(']][[',$rdata);
                    }
                  }
                  echo '<input id="perm_condition01_gks_print_forms" value="'.htmlspecialchars_gks($value).'" class="perm_condition01 form-control form-control-sm myneedsave" type="text" data-oid="'.$obj['id_permission_object'].'">';
                  break;  
                case 'gks_eshops':
                  $value='';
                  $rdata=trim_gks($obj['perm_condition01']);
                  if ($rdata!='') {
                    $rdata=unserialize($rdata);
                    if (count($rdata)>0) {
                      $sqltags="select id_eshop as myid,eshop_name as mytag from gks_eshops where id_eshop in (".implode(',',$rdata).") order by eshop_sortorder";
                      $resulttags = $db_link->query($sqltags);        
                      if (!$resulttags) {debug_mail(false,'error sql',$sqltags);echo 'sql error';die();}
                      $rdata=array();
                      while ($rowtag = $resulttags->fetch_assoc()) $rdata[]=$rowtag['mytag'].' (#'.$rowtag['myid'].')';  
                      if (count($rdata)>0) $value=implode(']][[',$rdata);
                    }
                  }
                  echo '<input id="perm_condition01_gks_eshops" value="'.htmlspecialchars_gks($value).'" class="perm_condition01 form-control form-control-sm myneedsave" type="text" data-oid="'.$obj['id_permission_object'].'">';
                  break;  
                
                case 'gks_hotel':
                  $value='';
                  $rdata=trim_gks($obj['perm_condition01']);
                  if ($rdata!='') {
                    $rdata=unserialize($rdata);
                    if (count($rdata)>0) {
                      $sqltags="select id_hotel as myid,hotel_title as mytag from gks_hotel where id_hotel in (".implode(',',$rdata).") order by hotel_sortorder";
                      $resulttags = $db_link->query($sqltags);        
                      if (!$resulttags) {debug_mail(false,'error sql',$sqltags);echo 'sql error';die();}
                      $rdata=array();
                      while ($rowtag = $resulttags->fetch_assoc()) $rdata[]=$rowtag['mytag'].' (#'.$rowtag['myid'].')';  
                      if (count($rdata)>0) $value=implode(']][[',$rdata);
                    }
                  }
                  echo '<input id="perm_condition01_gks_hotel" value="'.htmlspecialchars_gks($value).'" class="perm_condition01 form-control form-control-sm myneedsave" type="text" data-oid="'.$obj['id_permission_object'].'">';
                  break;
                  
                case 'gks_transfer':
                  $value='';
                  $rdata=trim_gks($obj['perm_condition01']);
                  if ($rdata!='') {
                    $rdata=unserialize($rdata);
                    if (count($rdata)>0) {
                      $sqltags="select id_transfer as myid,transfer_title as mytag from gks_transfer where id_transfer in (".implode(',',$rdata).") order by transfer_sortorder";
                      $resulttags = $db_link->query($sqltags);        
                      if (!$resulttags) {debug_mail(false,'error sql',$sqltags);echo 'sql error';die();}
                      $rdata=array();
                      while ($rowtag = $resulttags->fetch_assoc()) $rdata[]=$rowtag['mytag'].' (#'.$rowtag['myid'].')';  
                      if (count($rdata)>0) $value=implode(']][[',$rdata);
                    }
                  }
                  echo '<input id="perm_condition01_gks_transfer" value="'.htmlspecialchars_gks($value).'" class="perm_condition01 form-control form-control-sm myneedsave" type="text" data-oid="'.$obj['id_permission_object'].'">';
                  break;
                  
                case 'gks_transfer_area':
                  $value='';
                  $rdata=trim_gks($obj['perm_condition01']);
                  if ($rdata!='') {
                    $rdata=unserialize($rdata);
                    if (count($rdata)>0) {
                      $sqltags="select id_transfer_area as myid,transfer_area_descr as mytag from gks_transfer_area where id_transfer_area in (".implode(',',$rdata).") order by sort_order";
                      $resulttags = $db_link->query($sqltags);        
                      if (!$resulttags) {debug_mail(false,'error sql',$sqltags);echo 'sql error';die();}
                      $rdata=array();
                      while ($rowtag = $resulttags->fetch_assoc()) $rdata[]=$rowtag['mytag'].' (#'.$rowtag['myid'].')';  
                      if (count($rdata)>0) $value=implode(']][[',$rdata);
                    }
                  }
                  echo '<input id="perm_condition01_gks_transfer_area" value="'.htmlspecialchars_gks($value).'" class="perm_condition01 form-control form-control-sm myneedsave" type="text" data-oid="'.$obj['id_permission_object'].'">';
                  break;                  
                    
                case 'gks_pos':
                  $value='';
                  $rdata=trim_gks($obj['perm_condition01']);
                  if ($rdata!='') {
                    $rdata=unserialize($rdata);
                    if (count($rdata)>0) {
                      $sqltags="select id_pos as myid,pos_name as mytag from gks_pos where id_pos in (".implode(',',$rdata).") order by pos_name";
                      $resulttags = $db_link->query($sqltags);        
                      if (!$resulttags) {debug_mail(false,'error sql',$sqltags);echo 'sql error';die();}
                      $rdata=array();
                      while ($rowtag = $resulttags->fetch_assoc()) $rdata[]=$rowtag['mytag'].' (#'.$rowtag['myid'].')';  
                      if (count($rdata)>0) $value=implode(']][[',$rdata);
                    }
                  }
                  echo '<input id="perm_condition01_gks_pos" value="'.htmlspecialchars_gks($value).'" class="perm_condition01 form-control form-control-sm myneedsave" type="text" data-oid="'.$obj['id_permission_object'].'">';
                  break;  
                case 'gks_pos_run':
                  $value='';
                  $rdata=trim_gks($obj['perm_condition01']);
                  if ($rdata!='') {
                    $rdata=unserialize($rdata);
                    if (count($rdata)>0) {
                      $sqltags="select id_pos as myid,pos_name as mytag from gks_pos where id_pos in (".implode(',',$rdata).") order by pos_name";
                      $resulttags = $db_link->query($sqltags);        
                      if (!$resulttags) {debug_mail(false,'error sql',$sqltags);echo 'sql error';die();}
                      $rdata=array();
                      while ($rowtag = $resulttags->fetch_assoc()) $rdata[]=$rowtag['mytag'].' (#'.$rowtag['myid'].')';  
                      if (count($rdata)>0) $value=implode(']][[',$rdata);
                    }
                  }
                  echo '<input id="perm_condition01_gks_pos_run" value="'.htmlspecialchars_gks($value).'" class="perm_condition01 form-control form-control-sm myneedsave" type="text" data-oid="'.$obj['id_permission_object'].'">';

                  echo '<div style="margin-top:4px;">'.gks_lang('Μπορεί να ορίσει την Εκτύπωση Χ έως').' ';
                  echo '<input id="perm_int_cond02_gks_pos_run" type="number" min="0" max="30" value="';
                  $temp=intval($obj['perm_int_cond02']);
                  if ($temp>0) echo $temp;
                  echo '" class="perm_int_cond02 form-control form-control-sm myneedsave" style="max-width:60px;display:inline-block;" data-oid="'.$obj['id_permission_object'].'">';
                  echo ' '.gks_lang('ημέρες στο παρελθόν').'</div>';
                  
                  break;  
                case 'gks_crm_tasks':
                  echo '<label class="col-form-label form-control-sm gks_int_cond" for="perm_int_cond01_gks_crm_tasks">'.gks_lang('Μόνο τα δικά του').'</label> ';
                  echo '<input id="perm_int_cond01_gks_crm_tasks" type="checkbox" value="1" '.(intval($obj['perm_int_cond01'])==1 ? ' checked ' : '').' class="perm_int_cond01 switchery_blue_this myneedsave" data-oid="'.$obj['id_permission_object'].'">';
                  break;  
                case 'gks_crm_tasks_pivot1':
                  echo '<label class="col-form-label form-control-sm gks_int_cond" for="perm_int_cond01_gks_crm_tasks_pivot1">'.gks_lang('Μόνο τα δικά του').'</label> ';
                  echo '<input id="perm_int_cond01_gks_crm_tasks_pivot1" type="checkbox" value="1" '.(intval($obj['perm_int_cond01'])==1 ? ' checked ' : '').' class="perm_int_cond01 switchery_blue_this myneedsave" data-oid="'.$obj['id_permission_object'].'">';
                  break;  
                case 'gks_transfer_reservation':
                  echo '<label class="col-form-label form-control-sm gks_int_cond" for="perm_int_cond01_gks_transfer_reservation">'.gks_lang('Μόνο τα δικά του').'</label> ';
                  echo '<input id="perm_int_cond01_gks_transfer_reservation" type="checkbox" value="1" '.(intval($obj['perm_int_cond01'])==1 ? ' checked ' : '').' class="perm_int_cond01 switchery_blue_this myneedsave" data-oid="'.$obj['id_permission_object'].'">';
                  break;  
                
                
                case 'dav_card':
                  //echo '<input id="perm_condition01_dav_card" value="'.htmlspecialchars_gks($obj['perm_condition01']).'" class="perm_condition01 form-control form-control-sm myneedsave" type="text" data-oid="'.$obj['id_permission_object'].'">';
                  //echo '<small class="form-text text-muted">'.gks_lang('Ειδικό ερώτημα sql για φίλτρο π.χ. wp_users.ID<1000').'</small>';
                  break;
                
              }
              
              
              
              ?>
            </div>
          </div>
          <?php }?>
        </div>    
      </div>  
    <?php } ?> 
    
    
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ειδοποιήσεις');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('cardnotf');?>>

          <div class="form-group row">
            <div class="col-md-12 text-center">
              <button type="button" class="btn btn-primary notif_all"><?php echo gks_lang('Όλες');?></button>
              <button type="button" class="btn btn-primary notif_none"><?php echo gks_lang('Καμία');?></button>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-12 col-md-3">
              <div class="table-dark gks_eidos_label">
                <?php echo gks_lang('Αντικείμενο');?>
              </div>
            </div>
            <div class="col-2 col-md-1 text-md-center offset-1 offset-md-0">
              <div class="table-dark gks_eidos_label">
                <?php echo gks_lang('Εκχώρηση');?>
              </div>
            </div>
            <div class="col-2 col-md-1 text-md-center" "="">
              <div class="table-dark gks_eidos_label">
                <?php echo gks_lang('Ειδοποίηση');?>
              </div>
            </div>
            <div class="col-2 col-md-1 text-md-center" "="">
              <div class="table-dark gks_eidos_label">
                <?php echo gks_lang('email');?>
              </div>
            </div>
            <div class="col-2 col-md-1 text-md-center" "="">
              <div class="table-dark gks_eidos_label">
                <?php echo gks_lang('Viber');?>
              </div>
            </div>
            
          </div>
          
          <div class="form-group row">
            <div class="col-12 col-md-3">
              
            </div>
            <div class="col-2 col-md-1 text-md-center offset-1 offset-md-0">
              <button type="button" class="btn btn-sm btn-primary notif_admin_all" style="margin:1px;padding:2px"><?php echo gks_lang('Όλες');?></button>
              <button type="button" class="btn btn-sm btn-primary notif_admin_none" style="margin:1px;padding:2px"><?php echo gks_lang('Καμία');?></button>
            </div>
            <div class="col-2 col-md-1 text-md-center">
              <button type="button" class="btn btn-sm btn-primary notif_user_all" style="margin:1px;padding:2px"><?php echo gks_lang('Όλες');?></button>
              <button type="button" class="btn btn-sm btn-primary notif_user_none" style="margin:1px;padding:2px"><?php echo gks_lang('Καμία');?></button>
            </div>
            <div class="col-2 col-md-1 text-md-center">
              <button type="button" class="btn btn-sm btn-primary notif_to_email_all" style="margin:1px;padding:2px"><?php echo gks_lang('Όλες');?></button>
              <button type="button" class="btn btn-sm btn-primary notif_to_email_none" style="margin:1px;padding:2px"><?php echo gks_lang('Καμία');?></button>
            </div>
            <div class="col-2 col-md-1 text-md-center">
              <button type="button" class="btn btn-sm btn-primary notif_to_viber_all" style="margin:1px;padding:2px"><?php echo gks_lang('Όλες');?></button>
              <button type="button" class="btn btn-sm btn-primary notif_to_viber_none" style="margin:1px;padding:2px"><?php echo gks_lang('Καμία');?></button>
            </div>
          </div>

          <?php
          $sql_notif_type="SELECT id_notification_type,notification_type_descr  FROM gks_notification_type WHERE notification_type_disabled=0";
          if ($GKS_HOTEL_BACKEND==false and GKS_TRANSFER==false) $sql_notif_type.=" and id_notification_type not in (1010)";
          
          if ($GKS_ORDERS_PRODUCTION==false) $sql_notif_type.=" and id_notification_type not in (510)";
          if ($GKS_CRM_ENABLE==false) $sql_notif_type.=" and id_notification_type not in (50)";
          $sql_notif_type.=" ORDER BY notification_type_sortorder;";
          $result_notif_type = $db_link->query($sql_notif_type);
          if (!$result_notif_type) {debug_mail(false,'error sql',$sql_notif_type);  die('sql error');}
          $notifs_array=array();
          while ($row_notif_type = $result_notif_type->fetch_assoc()) {
            $row_notif_type['admin']=false;
            $row_notif_type['user']=false;
            $row_notif_type['email']=false;
            $row_notif_type['viber']=false;
            $notifs_array[$row_notif_type['id_notification_type']]=$row_notif_type;
          }
               
          $sql_notif_type="select * from gks_notification_userperm where user_id=".$id;
          $result_notif_type = $db_link->query($sql_notif_type);
          if (!$result_notif_type) {debug_mail(false,'error sql',$sql_notif_type);  die('sql error');}
          while ($row_notif_type = $result_notif_type->fetch_assoc()) {
            if (isset($notifs_array[$row_notif_type['notification_type_id']])) {
              $notifs_array[$row_notif_type['notification_type_id']]['admin']=intval($row_notif_type['from_admin'])!=0;
              $notifs_array[$row_notif_type['notification_type_id']]['user']=intval($row_notif_type['from_user'])!=0;
              $notifs_array[$row_notif_type['notification_type_id']]['email']=intval($row_notif_type['to_email'])!=0;
              $notifs_array[$row_notif_type['notification_type_id']]['viber']=intval($row_notif_type['to_viber'])!=0;
              if ($notifs_array[$row_notif_type['notification_type_id']]['admin']==false) {
                $notifs_array[$row_notif_type['notification_type_id']]['user']=false;
                $notifs_array[$row_notif_type['notification_type_id']]['email']=false;
                $notifs_array[$row_notif_type['notification_type_id']]['viber']=false;
              }
            }
          }
          
          foreach ($notifs_array as $nid => $notif) {?>                    
          <div class="form-group row">
            <label class="col-12 col-md-3 col-form-label form-control-sm text-md-center1 gks_flock row_notif" data-nid="<?php echo $nid;?>"><?php echo $notif['notification_type_descr'];?></label>
            
            <div class="col-2 col-md-1 text-md-center offset-1 offset-md-0">
              <input type="checkbox" value="1" <?php if ($notif['admin']) echo ' checked '; ?> class="switchery1_this notif_admin_item" data-nid="<?php echo $nid;?>">
            </div>
            <div class="col-2 col-md-1 text-md-center offset-1 offset-md-0">
              <input type="checkbox" value="1" <?php if ($notif['user'])  echo ' checked '; ?> <?php if ($notif['admin']==false) echo ' disabled '; ?> class="notif_user_item" data-nid="<?php echo $nid;?>">
            </div>
            <div class="col-2 col-md-1 text-md-center offset-1 offset-md-0">
              <input type="checkbox" value="1" <?php if ($notif['email']) echo ' checked '; ?> <?php if ($notif['admin']==false) echo ' disabled '; ?> class="notif_to_email_item" data-nid="<?php echo $nid;?>">
            </div>
            <div class="col-2 col-md-1 text-md-center offset-1 offset-md-0">
              <input type="checkbox" value="1" <?php if ($notif['viber']) echo ' checked '; ?> <?php if ($notif['admin']==false) echo ' disabled '; ?> class="notif_to_viber_item" data-nid="<?php echo $nid;?>">
            </div>

          </div>
                    
          <?php } ?>
          
        </div>    
      </div>    

       
    </div>    
  </div>    
</div> 
</div>







   


<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" style="margin-bottom:10px;" class="btn btn-primary" id="submit_button_ok"><?php echo gks_lang('Αποθήκευση');?></button>
    </div> 
  </div> 
</div> 

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>



var need_save=false;

var gks_page_loading=true;
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>


  var switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  switchery1_this.forEach(function(html) {
    var switchery3 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });
    
  var switchery_blue_this = Array.prototype.slice.call(document.querySelectorAll('.switchery_blue_this'));
  switchery_blue_this.forEach(function(html) {
    //var switchery_blue = new Switchery(html, { color: '#4949bd', secondaryColor: '#cfcfeb', jackColor: '#ffffff', jackSecondaryColor: '#ffffff' });
    var switchery_blue = new Switchery(html,gks_switchery_defaults());
    
    html.onchange = function() {need_save=true;};
  });
  
  notif_user_item_array=[];
  var notif_user_item = Array.prototype.slice.call(document.querySelectorAll('.notif_user_item'));
  notif_user_item.forEach(function(html) {
    var switchery_item = new Switchery(html,gks_switchery_defaults());
    nid=$(html).attr('data-nid');
    notif_user_item_array[nid]=switchery_item;
    html.onchange = function() {need_save=true;};
  });
  //console.log(notif_user_item_array);
  notif_to_email_item_array=[];
  var notif_to_email_item = Array.prototype.slice.call(document.querySelectorAll('.notif_to_email_item'));
  notif_to_email_item.forEach(function(html) {
    var switchery_item = new Switchery(html,gks_switchery_defaults());
    nid=$(html).attr('data-nid');
    notif_to_email_item_array[nid]=switchery_item;
    html.onchange = function() {need_save=true;};
  });
  //console.log(notif_to_email_item_array);
  notif_to_viber_item_array=[];
  var notif_to_viber_item = Array.prototype.slice.call(document.querySelectorAll('.notif_to_viber_item'));
  notif_to_viber_item.forEach(function(html) {
    var switchery_item = new Switchery(html,gks_switchery_defaults());
    nid=$(html).attr('data-nid');
    notif_to_viber_item_array[nid]=switchery_item;
    html.onchange = function() {need_save=true;};
  });
  //console.log(notif_to_viber_item_array);
  
  
  
    
  function mysubmit() {
    
    mydata=[];
    $('.row_perm').each(function() {
      oid=$(this).attr('data-oid');
      item={};
      item.oid=oid;
      
      item.view=($('.perm_view[data-oid=' + oid + ']').is(':checked') ? '1':'0');
      item.edit=($('.perm_edit[data-oid=' + oid + ']').is(':checked') ? '1':'0');
      item.add=($('.perm_add[data-oid=' + oid + ']').is(':checked') ? '1':'0');
      item.delete=($('.perm_delete[data-oid=' + oid + ']').is(':checked') ? '1':'0');
      item.autocomplete=($('.perm_autocomplete[data-oid=' + oid + ']').is(':checked') ? '1':'0');
      
      elem=$('.perm_condition01[data-oid=' + oid + ']');
      if (elem.length>0) item.perm_condition01=elem.val();
      //console.log(item);

      elem=$('.perm_condition02[data-oid=' + oid + ']');
      if (elem.length>0) item.perm_condition02=elem.val();
      
      elem=$('.perm_int_cond01[data-oid=' + oid + ']');
      if (elem.length>0) item.perm_int_cond01=(elem.is(':checked') ? '1':'0');
      //console.log(item);
      
      elem=$('.perm_int_cond02[data-oid=' + oid + ']');
      if (elem.length>0) item.perm_int_cond02=elem.val();
      //console.log(item);
      
      
      
      mydata.push(item);
    });
    
    notif=[];
    $('.notif_admin_item').each(function() {
      nid=$(this).attr('data-nid');
      item={};
      item.nid=nid;
      item.admin=($(this).is(':checked') ? '1':'0');
      item.user=($('.notif_user_item[data-nid=' + nid + ']').is(':checked') ? '1':'0');
      item.email=($('.notif_to_email_item[data-nid=' + nid + ']').is(':checked') ? '1':'0');
      item.viber=($('.notif_to_viber_item[data-nid=' + nid + ']').is(':checked') ? '1':'0');
      
      notif.push(item);
    });
    
    
    datasend='';
    datasend+='&mydata=' + encodeURIComponent($.base64.encode(JSON.stringify(mydata)));
    datasend+='&notif=' + encodeURIComponent($.base64.encode(JSON.stringify(notif)));
    
    //console.log(mydata);
    //console.log(notif);
    //console.log(datasend);
    //return;
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-users-item-permission-exec.php?id=' + <?php echo $id;?>,
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $("body").removeClass("myloading");
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
				$("body").removeClass("myloading");
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
					  need_save=false;
            if (data.redirect=='') {
  					  window.location.reload();
  					} else {
  					  window.location.href = $.base64.decode(data.redirect);
  					}
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});    
    
  }

  $('#perm_global_all').click(function() {
    $('.perm_view, .perm_edit, .perm_add, .perm_delete, .perm_autocomplete, .notif_admin_item, .notif_user_item, .notif_to_email_item, .notif_to_viber_item').each(function() {
      if ($(this).is(':checked')) {
        
      } else {
        $(this).click();
      }
    });    
  });
  $('#perm_global_none').click(function() {
    $('.perm_view, .perm_edit, .perm_add, .perm_delete, .perm_autocomplete, .notif_admin_item, .notif_user_item, .notif_to_email_item, .notif_to_viber_item').each(function() {
      if ($(this).is(':checked')) {
        $(this).click();
      } else {
        
      }
    });    
  });
  
  
  function perm_mass_set(elem,myclass,state) {
    var mystate=state;
    //console.log(myclass,state);
    mycard=elem; //.parent().parent().parent();
    mycard.find('.perm_' + myclass).each(function() {
      if ($(this).is(':checked')) {
        if (mystate=='none') $(this).click();
      } else {
        if (mystate=='all') $(this).click();
      }
    });
    
  }
  
  $('.perm_view_all').click(function()          {perm_mass_set($(this).parent().parent().parent(),'view','all');});
  $('.perm_view_none').click(function()         {perm_mass_set($(this).parent().parent().parent(),'view','none');});
  $('.perm_edit_all').click(function()          {perm_mass_set($(this).parent().parent().parent(),'edit','all');});
  $('.perm_edit_none').click(function()         {perm_mass_set($(this).parent().parent().parent(),'edit','none');});
  $('.perm_add_all').click(function()           {perm_mass_set($(this).parent().parent().parent(),'add','all');});
  $('.perm_add_none').click(function()          {perm_mass_set($(this).parent().parent().parent(),'add','none');});
  $('.perm_delete_all').click(function()        {perm_mass_set($(this).parent().parent().parent(),'delete','all');});
  $('.perm_delete_none').click(function()       {perm_mass_set($(this).parent().parent().parent(),'delete','none');});
  $('.perm_autocomplete_all').click(function()  {perm_mass_set($(this).parent().parent().parent(),'autocomplete','all');});
  $('.perm_autocomplete_none').click(function() {perm_mass_set($(this).parent().parent().parent(),'autocomplete','none');});
  

  $('.perm_card_all').click(function() {
    perm_mass_set($(this).parent().parent().parent(),'view','all');
    perm_mass_set($(this).parent().parent().parent(),'edit','all');
    perm_mass_set($(this).parent().parent().parent(),'add','all');
    perm_mass_set($(this).parent().parent().parent(),'delete','all');
    perm_mass_set($(this).parent().parent().parent(),'autocomplete','all');
  });
  $('.perm_card_none').click(function() {
    perm_mass_set($(this).parent().parent().parent(),'view','none');
    perm_mass_set($(this).parent().parent().parent(),'edit','none');
    perm_mass_set($(this).parent().parent().parent(),'add','none');
    perm_mass_set($(this).parent().parent().parent(),'delete','none');
    perm_mass_set($(this).parent().parent().parent(),'autocomplete','none');
  });
  
  
  $('.notif_all').click(function() {
    $('.notif_admin_item, .notif_user_item, .notif_to_email_item, .notif_to_viber_item').each(function() {
      if ($(this).is(':checked')) {
        
      } else {
        $(this).click();
      }
    });    
  });
  $('.notif_none').click(function() {
    $('.notif_admin_item, .notif_user_item, .notif_to_email_item, .notif_to_viber_item').each(function() {
      if ($(this).is(':checked')) {
        $(this).click();
      } else {
        
      }
    });    
  });
  $('.notif_admin_all').click(function() {
    $('.notif_admin_item').each(function() {
      if ($(this).is(':checked')) {
        
      } else {
        $(this).click();
      }
    });    
  });
  $('.notif_admin_none').click(function() {
    $('.notif_admin_item').each(function() {
      if ($(this).is(':checked')) {
        $(this).click();
      } else {
        
      }
    });    
  });

  $('.notif_user_all').click(function() {
    $('.notif_user_item').each(function() {
      if ($(this).is(':checked')) {
        
      } else {
        $(this).click();
      }
    });    
  });
  $('.notif_user_none').click(function() {
    $('.notif_user_item').each(function() {
      if ($(this).is(':checked')) {
        $(this).click();
      } else {
        
      }
    });    
  });  
  
  $('.notif_to_email_all').click(function() {
    $('.notif_to_email_item').each(function() {
      if ($(this).is(':checked')) {
        
      } else {
        $(this).click();
      }
    });    
  });
  $('.notif_to_email_none').click(function() {
    $('.notif_to_email_item').each(function() {
      if ($(this).is(':checked')) {
        $(this).click();
      } else {
        
      }
    });    
  });  
  
  $('.notif_to_viber_all').click(function() {
    $('.notif_to_viber_item').each(function() {
      if ($(this).is(':checked')) {
        
      } else {
        $(this).click();
      }
    });    
  });
  $('.notif_to_viber_none').click(function() {
    $('.notif_to_viber_item').each(function() {
      if ($(this).is(':checked')) {
        $(this).click();
      } else {
        
      }
    });    
  });  
  
  
  
  function notif_user_enabled() {
    
    $('.notif_admin_item').each(function() {
      nid=$(this).attr('data-nid');
      
      if ($(this).is(':checked')) {
        notif_user_item_array[nid].enable();
        notif_to_email_item_array[nid].enable();
        notif_to_viber_item_array[nid].enable();
        //$('.notif_user_item[data-nid=' + nid + ']').click();
      } else {
        if ($('.notif_user_item[data-nid=' + nid + ']').is(':checked')) $('.notif_user_item[data-nid=' + nid + ']').click();  
        if ($('.notif_to_email_item[data-nid=' + nid + ']').is(':checked')) $('.notif_to_email_item[data-nid=' + nid + ']').click();  
        if ($('.notif_to_viber_item[data-nid=' + nid + ']').is(':checked')) $('.notif_to_viber_item[data-nid=' + nid + ']').click();  
        notif_user_item_array[nid].disable();
        notif_to_email_item_array[nid].disable();
        notif_to_viber_item_array[nid].disable();
      }
    });    
    
  }
  
  $('.notif_admin_item').change(notif_user_enabled);
  
  
  var gks_company_tags = [];
  <?php 
  $sqltags="select id_company as myid, company_title as mytag from gks_company where company_title<>'' order by company_sortorder";
  $resulttags = $db_link->query($sqltags);
  if (!$resulttags) {debug_mail(false,'error sql',$sqltags);  die('sql error');}
  while ($rowtag = $resulttags->fetch_assoc())   echo "gks_company_tags.push($.base64.decode('".base64_encode(trim_gks($rowtag['mytag']).' (#'.$rowtag['myid'].')')."'));";
  ?>
  $('#perm_condition01_gks_company').tagit({allowSpaces:true,singleFieldDelimiter: ']][[',availableTags: gks_company_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});

  var gks_company_subs_tags = [];
  gks_company_subs_tags.push(gks_lang('Κεντρικό')+' (#0)');
  <?php 
  $sqltags="select id_company_sub as myid, company_sub_title as mytag from gks_company_subs where company_sub_title<>'' order by company_sub_sortorder";
  $resulttags = $db_link->query($sqltags);
  if (!$resulttags) {debug_mail(false,'error sql',$sqltags);  die('sql error');}
  while ($rowtag = $resulttags->fetch_assoc())   echo "gks_company_subs_tags.push($.base64.decode('".base64_encode(trim_gks($rowtag['mytag']).' (#'.$rowtag['myid'].')')."'));";
  ?>
  $('#perm_condition01_gks_company_subs').tagit({allowSpaces:true,singleFieldDelimiter: ']][[',availableTags: gks_company_subs_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});

  var gks_acc_journal_tags = [];
  <?php 
  $sqltags="select id_acc_journal as myid, acc_journal_descr as mytag from gks_acc_journal where acc_journal_descr<>'' order by sortorder";
  $resulttags = $db_link->query($sqltags);
  if (!$resulttags) {debug_mail(false,'error sql',$sqltags);  die('sql error');}
  while ($rowtag = $resulttags->fetch_assoc())   echo "gks_acc_journal_tags.push($.base64.decode('".base64_encode(trim_gks($rowtag['mytag']).' (#'.$rowtag['myid'].')')."'));";
  ?>
  $('#perm_condition01_gks_acc_journal').tagit({allowSpaces:true,singleFieldDelimiter: ']][[',availableTags: gks_acc_journal_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});

  var gks_acc_seires_tags = [];
  <?php 
  $sqltags="select id_acc_seira as myid, seira_descr as mytag from gks_acc_seires where seira_descr<>'' order by sortorder";
  $resulttags = $db_link->query($sqltags);
  if (!$resulttags) {debug_mail(false,'error sql',$sqltags);  die('sql error');}
  while ($rowtag = $resulttags->fetch_assoc())   echo "gks_acc_seires_tags.push($.base64.decode('".base64_encode(trim_gks($rowtag['mytag']).' (#'.$rowtag['myid'].')')."'));";
  ?>
  $('#perm_condition01_gks_acc_seires').tagit({allowSpaces:true,singleFieldDelimiter: ']][[',availableTags: gks_acc_seires_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});

  var gks_warehouses_tags = [];
  <?php 
  $sqltags="select id_warehouse as myid, warehouse_name as mytag from gks_warehouses where warehouse_name<>'' order by warehouse_sortorder";
  $resulttags = $db_link->query($sqltags);
  if (!$resulttags) {debug_mail(false,'error sql',$sqltags);  die('sql error');}
  while ($rowtag = $resulttags->fetch_assoc())   echo "gks_warehouses_tags.push($.base64.decode('".base64_encode(trim_gks($rowtag['mytag']).' (#'.$rowtag['myid'].')')."'));";
  ?>
  $('#perm_condition01_gks_warehouses').tagit({allowSpaces:true,singleFieldDelimiter: ']][[',availableTags: gks_warehouses_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});

  var gks_print_forms_tags = [];
  <?php 
  $sqltags="select id_print_form as myid, print_form_descr as mytag from gks_print_forms where print_form_descr<>'' order by sortorder";
  $resulttags = $db_link->query($sqltags);
  if (!$resulttags) {debug_mail(false,'error sql',$sqltags);  die('sql error');}
  while ($rowtag = $resulttags->fetch_assoc())   echo "gks_print_forms_tags.push($.base64.decode('".base64_encode(trim_gks($rowtag['mytag']).' (#'.$rowtag['myid'].')')."'));";
  ?>
  $('#perm_condition01_gks_print_forms').tagit({allowSpaces:true,singleFieldDelimiter: ']][[',availableTags: gks_print_forms_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});

  var gks_eshops_tags = [];
  <?php 
  $sqltags="select id_eshop as myid, eshop_name as mytag from gks_eshops where eshop_name<>'' order by eshop_sortorder";
  $resulttags = $db_link->query($sqltags);
  if (!$resulttags) {debug_mail(false,'error sql',$sqltags);  die('sql error');}
  while ($rowtag = $resulttags->fetch_assoc())   echo "gks_eshops_tags.push($.base64.decode('".base64_encode(trim_gks($rowtag['mytag']).' (#'.$rowtag['myid'].')')."'));";
  ?>
  $('#perm_condition01_gks_eshops').tagit({allowSpaces:true,singleFieldDelimiter: ']][[',availableTags: gks_eshops_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});

  var gks_hotel_tags = [];
  <?php 
  $sqltags="select id_hotel as myid, hotel_title as mytag from gks_hotel where hotel_title<>'' order by hotel_sortorder";
  $resulttags = $db_link->query($sqltags);
  if (!$resulttags) {debug_mail(false,'error sql',$sqltags);  die('sql error');}
  while ($rowtag = $resulttags->fetch_assoc())   echo "gks_hotel_tags.push($.base64.decode('".base64_encode(trim_gks($rowtag['mytag']).' (#'.$rowtag['myid'].')')."'));";
  ?>
  $('#perm_condition01_gks_hotel').tagit({allowSpaces:true,singleFieldDelimiter: ']][[',availableTags: gks_hotel_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});

  var gks_transfer_tags = [];
  <?php 
  $sqltags="select id_transfer as myid, transfer_title as mytag from gks_transfer where transfer_title<>'' order by transfer_sortorder";
  $resulttags = $db_link->query($sqltags);
  if (!$resulttags) {debug_mail(false,'error sql',$sqltags);  die('sql error');}
  while ($rowtag = $resulttags->fetch_assoc())   echo "gks_transfer_tags.push($.base64.decode('".base64_encode(trim_gks($rowtag['mytag']).' (#'.$rowtag['myid'].')')."'));";
  ?>
  $('#perm_condition01_gks_transfer').tagit({allowSpaces:true,singleFieldDelimiter: ']][[',availableTags: gks_transfer_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});

  var gks_transfer_area_tags = [];
  <?php 
  $sqltags="select id_transfer_area as myid, transfer_area_descr as mytag from gks_transfer_area where transfer_area_descr<>'' order by sort_order";
  $resulttags = $db_link->query($sqltags);
  if (!$resulttags) {debug_mail(false,'error sql',$sqltags);  die('sql error');}
  while ($rowtag = $resulttags->fetch_assoc())   echo "gks_transfer_area_tags.push($.base64.decode('".base64_encode(trim_gks($rowtag['mytag']).' (#'.$rowtag['myid'].')')."'));";
  ?>
  $('#perm_condition01_gks_transfer_area').tagit({allowSpaces:true,singleFieldDelimiter: ']][[',availableTags: gks_transfer_area_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});



  var gks_pos_tags = [];
  <?php 
  $sqltags="select id_pos as myid, pos_name as mytag from gks_pos where pos_name<>'' order by pos_name";
  $resulttags = $db_link->query($sqltags);
  if (!$resulttags) {debug_mail(false,'error sql',$sqltags);  die('sql error');}
  while ($rowtag = $resulttags->fetch_assoc())   echo "gks_pos_tags.push($.base64.decode('".base64_encode(trim_gks($rowtag['mytag']).' (#'.$rowtag['myid'].')')."'));";
  ?>
  $('#perm_condition01_gks_pos').tagit({allowSpaces:true,singleFieldDelimiter: ']][[',availableTags: gks_pos_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});
  $('#perm_condition01_gks_pos_run').tagit({allowSpaces:true,singleFieldDelimiter: ']][[',availableTags: gks_pos_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});




  //generic
  gks_page_loading=false;
    

  $('.myneedsave').on('input change keyup paste', function() {
    need_save=true; 
  });

  window.onbeforeunload = function() {
    if (need_save==false) return;
    return gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');
  };

  need_save=false;
  
});

 
 
</script>



<?php

include_once('_my_footer_admin.php');



