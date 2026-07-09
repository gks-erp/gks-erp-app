<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


$nav_active_array=array('manage','manage_users_groups');

db_open();

$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_users_groups',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}


$gks_custom_prepare = gks_custom_table_item_prepare('gks_users_groups',['from'=>'item']);

if ($id ==-1) {
  
  $row=array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_users_group']=-1;
  $row['group_title']='';
  //$row['group_date_add']='';
  $row['group_comments']='';
  $row['group_old_code']='';
  $row['group_disable']=0;
  $row['group_parent_id']=0;
  
  $my_page_title=gks_lang('Νέα Ομάδα Επαφών');
  
} else {
  $sql ="SELECT gks_users_groups.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit
  FROM gks_users_groups
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_users_groups.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_users_groups.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where id_users_group = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) {
    debug_mail(false,'error sql',$sql);
    die('sql error');
  }
  if ($result->num_rows!=1) {
    debug_mail(false,'record not found sql',$sql); 
    die('no record found');
  }
  $row = $result->fetch_assoc();
  $object_title=$row['group_title'];
  $my_page_title=gks_lang('Ομάδα Επαφών').': '.$row['group_title'];

}

$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);
//print '<pre>';print_r($gks_custom_row);die();



stat_record();


include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Ομάδα Επαφών');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Ομάδα Επαφών');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
      <?php }?>
    </div>
  </div>
</div>



<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">
      


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Βασικά στοιχεία');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('bas');?>>        


          <div class="form-group row">
            <label for="group_title" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όνομα');?>:</label>
            <div class="col-md-8">
              <input id="group_title" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['group_title']);?>">
              
            </div>
          </div>
          <div class="form-group row">
            <label for="group_parent_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γονική Ομάδα');?>:</label>
            <div class="col-md-8">
              <select name="group_parent_id" id="group_parent_id"  class="form-control form-control-sm myneedsave">
              <option value="0"></option>
              <?php
              $sql="SELECT gks_users_groups.id_users_group, 
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
                              gks_users_groups.group_title) as fullpath
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

              where gks_users_groups.id_users_group <>".$id."
              ORDER BY fullpath";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die('sql error');
              }
              while ($row_select = $result_select->fetch_assoc()) {
                echo '<option value="'.$row_select['id_users_group'].'" ';
                if ($row_select['id_users_group']==$row['group_parent_id']) echo ' selected ';
                echo '>'.$row_select['fullpath'].'</option>';
              }?></select>
              <small class="form-text text-muted"><?php echo gks_lang('Έως 10 επίπεδα');?></small>
            </div>
          </div>
          <div class="form-group row">
            <label for="group_comments" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο');?>:</label>
            <div class="col-md-8">
              <textarea id="group_comments" type="text" class="form-control form-control-sm myneedsave" style="min-height:100px;"><?php echo htmlspecialchars_gks($row['group_comments']); ?></textarea>
            </div>
          </div>        
          <div class="form-group row">
            <label for="group_disable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" name="group_disable"  id="group_disable" value="1" <?php if ($row['group_disable']==0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>          
          

          
          
        </div>
      </div>
    </div>

    <div class="col-md-6">

      <?php gks_erp_app_purchase_ads_fix_item_card();?>

<?php
echo $gks_custom_row['html'];
//echo '<pre>';print_r($gks_custom_row['fields']);print '</pre>';
?>              


        

      
      
    </div>
  </div>
</div>




<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_ok"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_users_group'];?>" data-model="gks_users_groups" data-backurl="admin-usersgroups.php"><?php echo gks_lang('Διαγραφή');?></button>
    </div>
  </div>
</div>


<?php gks_erp_app_purchase_ads_fix_970x90('item');?>


<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
      

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ομαδάρχες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('omadar');?>>        
          <?php
          $query = "SELECT gks_users_groups_users.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, ".GKS_WP_TABLE_PREFIX."users.gks_wp_capabilities,".GKS_WP_TABLE_PREFIX."users.gks_wsl_current_user_image
          FROM gks_users_groups_users LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users_groups_users.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE gks_users_groups_users.group_id=".$id." and gks_users_groups_users.is_omadarxis=1
          order by ".GKS_WP_TABLE_PREFIX."users.gks_nickname";
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Επαφή');?></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><?php echo gks_lang('Ρόλοι');?></th>        
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  ><?php echo gks_lang('Ημερομηνία');?></th>        

            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" id="tr_<?php echo $row_list['id_users_groups_users'];?>">
              <th scope="row" nowrap align="right"><?php echo ($i);?></td>       
              <td nowrap align="center">
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-id="<?php echo $row_list['id_users_groups_users'];?>" data-model="gks_users_groups_users">            
              </td>
              <td><?php echo getUserPhoto($row_list['user_id'],$row_list['gks_wsl_current_user_image'],32);?></td>
              <td nowrap><?php echo '<a href="admin-users-item.php?id='.$row_list['user_id'].'">'.$row_list['gks_nickname'].'</a>';?>
              <?php
              if (!(strpos($row_list['gks_wp_capabilities'], '"omadarxis"') !== false)) {
                echo '<br><span style="color:#ff0000">'.gks_lang('Προσοχή! Δεν έχει δικαιώματα ομαδάρχη').'</span>';
              }?> 
              </td>  
              <td nowrap><?php echo getUserRoleDescr($row_list['user_id']);?></td>  
              <td nowrap><?php echo showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
            </tr>
          <?php } ?>


            <tr class="" id="tr_new">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <input type="text"   name="omadarxis"    id="omadarxis"   class="form-control" style="width:98%;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                <input type="hidden" name="omadarxis_id" id="omadarxis_id">
              </td>  
            </tr>
            <tr class="" id="tr_new_button">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center"></td>
              <td nowrap colspan="5">
                <button style="justify-content: center!important;" type="button" class="btn btn-sm btn-primary" id="add_omadarxis"><?php echo gks_lang('Προσθήκη');?></button>
              </td>  
            </tr>      
          </tbody>
          </table>

        </div>
      </div>        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Επαφές');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('epafes');?>>        

          <?php
          //and gks_users_groups_users.is_omadarxis=0
          $query = "SELECT gks_users_groups_users.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, ".GKS_WP_TABLE_PREFIX."users.gks_wp_capabilities,gks_wsl_current_user_image
          FROM gks_users_groups_users LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users_groups_users.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE gks_users_groups_users.group_id=".$id." 
          order by ".GKS_WP_TABLE_PREFIX."users.gks_nickname";
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Επαφή');?></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><?php echo gks_lang('Ρόλοι');?></th>        
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  ><?php echo gks_lang('Ημερομηνία');?></th>        

            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" id="tr_<?php echo $row_list['id_users_groups_users'];?>">
              <th scope="row" nowrap align="right"><?php echo ($i);?></td>       
              <td nowrap align="center">
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-id="<?php echo $row_list['id_users_groups_users'];?>" data-model="gks_users_groups_users">            
              </td>
              <td><?php echo getUserPhoto($row_list['user_id'],$row_list['gks_wsl_current_user_image'],32);?></td>
              <td nowrap><?php echo '<a href="admin-users-item.php?id='.$row_list['user_id'].'">'.$row_list['gks_nickname'].'</a>';?></td>  
              <td nowrap><?php echo getUserRoleDescr($row_list['user_id']);?></td>  
              <td nowrap><?php echo showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
            </tr>
          <?php } ?>


            <tr class="" id="tr_new">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <input type="text"   name="user"    id="user"   class="form-control" style="width:98%;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                <input type="hidden" name="user_id" id="user_id">
              </td>  
            </tr>
            <tr class="" id="tr_new_button">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center"></td>
              <td nowrap colspan="5">
                <button style="justify-content: center!important;" type="button" class="btn btn-sm btn-primary" id="add_user"><?php echo gks_lang('Προσθήκη');?></button>
              </td>  
            </tr>      
          </tbody>
          </table>      

        </div>
      </div>
      
      <?php 
      echo getObjectRels('gks_users_groups',$id);
      echo getActivityObjectTable('gks_users_groups',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_users_groups','id'=>$id));
      echo $obj_fileslist['html'];
      ?>
    </div>
      
    <div class="col-md-6">

        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Όλες οι Επαφές, και από τις υποομάδες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('allepaf');?>>        

          <?php


          $sql="SELECT 
          ug1.id_users_group AS gid1, 
          ug2.id_users_group AS gid2, 
          ug3.id_users_group AS gid3, 
          ug4.id_users_group AS gid4, 
          ug5.id_users_group AS gid5,
          ug6.id_users_group AS gid6,
          ug7.id_users_group AS gid7,
          ug8.id_users_group AS gid8,
          ug9.id_users_group AS gid9,
          ug10.id_users_group AS gid10
          FROM ((((((((gks_users_groups AS ug1 
          LEFT JOIN gks_users_groups AS ug2 ON ug1.id_users_group = ug2.group_parent_id) 
          LEFT JOIN gks_users_groups AS ug3 ON ug2.id_users_group = ug3.group_parent_id) 
          LEFT JOIN gks_users_groups AS ug4 ON ug3.id_users_group = ug4.group_parent_id) 
          LEFT JOIN gks_users_groups AS ug5 ON ug4.id_users_group = ug5.group_parent_id)
          LEFT JOIN gks_users_groups AS ug6 ON ug5.id_users_group = ug6.group_parent_id)
          LEFT JOIN gks_users_groups AS ug7 ON ug6.id_users_group = ug7.group_parent_id)
          LEFT JOIN gks_users_groups AS ug8 ON ug7.id_users_group = ug8.group_parent_id)
          LEFT JOIN gks_users_groups AS ug9 ON ug8.id_users_group = ug9.group_parent_id)
          LEFT JOIN gks_users_groups AS ug10 ON ug9.id_users_group = ug10.group_parent_id
          
          where ug1.id_users_group=".$row['id_users_group'];
          //echo $sql;
          $result_gu = $db_link->query($sql);        
          if (!$result_gu) {
            debug_mail(false,'error sql',$sql);
            die('sql error');
          }
          $gu_in='';
          
          while ($row_gu = $result_gu->fetch_assoc()) {
            if (isset($row_gu['gid1'])) $gu_in.=$row_gu['gid1'].',';
            if (isset($row_gu['gid2'])) $gu_in.=$row_gu['gid2'].',';
            if (isset($row_gu['gid3'])) $gu_in.=$row_gu['gid3'].',';
            if (isset($row_gu['gid4'])) $gu_in.=$row_gu['gid4'].',';
            if (isset($row_gu['gid5'])) $gu_in.=$row_gu['gid5'].',';
            if (isset($row_gu['gid6'])) $gu_in.=$row_gu['gid6'].',';
            if (isset($row_gu['gid7'])) $gu_in.=$row_gu['gid7'].',';
            if (isset($row_gu['gid8'])) $gu_in.=$row_gu['gid8'].',';
            if (isset($row_gu['gid9'])) $gu_in.=$row_gu['gid9'].',';
            if (isset($row_gu['gid10'])) $gu_in.=$row_gu['gid10'].',';
          }
          if (strlen($gu_in)>0) $gu_in=substr($gu_in, 0, strlen($gu_in)-1);
          if (strlen($gu_in)==0) $gu_in='-1'; //gia na exei kati
            
          $sql="SELECT DISTINCT gks_users_groups_users.user_id, ".GKS_WP_TABLE_PREFIX."users.gks_nickname,".GKS_WP_TABLE_PREFIX."users.gks_wp_capabilities, ".GKS_WP_TABLE_PREFIX."users.gks_wsl_current_user_image
          FROM gks_users_groups_users LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users_groups_users.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE gks_users_groups_users.group_id In (".$gu_in.")
          ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname;";
          
          //echo $sql;
          
          $result_list = $db_link->query($sql); 
          if (!$result_list) debug_mail(false,'error sql',$sql);
          if (!$result_list) die('sql error');
          ?>                
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Επαφή');?></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><?php echo gks_lang('Ρόλοι');?></th>        
            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
              <th scope="row" nowrap align="right"><?php echo ($i);?></td>       
              <td><?php echo getUserPhoto($row_list['user_id'],$row_list['gks_wsl_current_user_image'],32);?></td>
              <td nowrap><?php echo '<a href="admin-users-item.php?id='.$row_list['user_id'].'">'.$row_list['gks_nickname'].'</a>';?></td>  
              <td nowrap><?php echo getUserRoleDescr($row_list['user_id']);?></td>  
            </tr>
          <?php } ?>



          </tbody>
          </table>  
        </div>
      </div>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>      



          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-md-8"><input id="id_users_group" type="text" readonly class="form-control-plaintext form-control-sm" value="<?php echo $row['id_users_group'];?>"></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_add'])) echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επεξεργασία στις');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_edit'])) echo showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('IP');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><a href="admin-stat-ip.php?ip=<?php echo $row['myip'];?>"><?php echo $row['myip'];?></a></span></div>
          </div>
        </div>
      </div>      
    </div> 
    
  </div>
</div>


      
      
          




<?php include_once('_dialogs.php'); ?>
<script src='/my/js/tinymce/tinymce.min.js'></script>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var need_save=false;
var mychange = 'change keyup paste';
var gks_page_loading=true;

var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;
var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 



var from_php_dialog_object_rel_curr='gks_users_groups';
var from_php_activity_model='gks_users_groups';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_users_groups','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_users_groups','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_users_groups','delete',$id);?>;


tinymce.init({
  language: from_php_gks_tinymce_locale,
  entity_encoding : 'raw',
  forced_root_block:false, 
  remove_trailing_brs: false,
  theme: 'silver', 
  browser_spellcheck: true,
  plugins: 'autoresize print preview  searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists  wordcount imagetools textpattern help code',
  toolbar: 'undo redo formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat | code',
  menubar:true,
  statusbar: true,
  contextmenu: '', //gia na gine disable to default
  templates: [],
  content_css: [],
  content_style: '.mce-content-body {font-size:12px;font-family:"Open Sans",sans-serif;}',
  relative_urls : true,
  convert_urls: true,
  document_base_url : (window.location.origin + '/'),
  min_height: 200,
    
  selector: '.gks_tinymce',
  init_instance_callback: function(editor) {
    editor.on('Change', function(e) {
      need_save=true;
    });
  },
  readonly : (from_php_perm_ret_edit ? 0 : 1),
    
});
  
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  

  function mysubmit() {
    
    datasend='';

    datasend+='&group_title='  + encodeURI($("#mypostform #group_title").val().trim());
    datasend+='&group_comments='  + encodeURI($("#mypostform #group_comments").val().trim());
    datasend+='&group_parent_id='  + encodeURI($("#mypostform #group_parent_id").val().trim());
    datasend+='&group_disable=' + (($('#group_disable').is(':checked')) ? '0':'1');
    
    datasend+=gks_custom_datasend();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-usersgroups-item-exec.php?id=' + <?php echo $id;?>,
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
					myalert('error:<?php echo gks_lang('Παρακαλώ δοκιμάστε αργότερα');?>');
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

    return false;
  }
  
  $('#omadarxis').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-omadarxis.php',
        dataType: "json",
        cache: false,
        data: mydata,
        error : function(jqXHR ,textStatus,  errorThrown) {
  				myalert('error:' + jqXHR.responseText);
  			},
        success: function( data ) {
          if (data.success == true) {
            response( data.list);
          } else {
            myalert('error:' + $.base64.decode(data.message));
          }
        }
      });
    },
    
    minLength: 3,
    delay: 300, //default
    select: function( event, ui ) {
      $("#omadarxis_id").val(ui.item.id);
      datasend='&omadarxis_id='  + encodeURI(ui.item.id.trim());
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#omadarxis").val("");
          $("#omadarxis_id").val("");
        }
    }
  });
  
  
  $('#add_omadarxis').click(function(event) {  
    if (from_php_id <=0) {
      myalert('error:<?php echo gks_lang('Αποθηκεύστε πρώτα την εγγραφή');?>');
      return; 
    }
    datasend='';
    datasend+='id= <?php echo $id;?>';    
    datasend+='&omadarxis_id='  + encodeURI($("#omadarxis_id").val().trim());    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-usersgroups-item-omadarxis_add.php',
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
					myalert('error:<?php echo gks_lang('Παρακαλώ δοκιμάστε αργότερα');?>');
				} else {
				  
					if (data.success == true) {
  					//myalert('ok:' + 'OK');
  					window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  });
      

  $('#user').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        all:1,
      };
      $.ajax({
        url: 'admin-autocomplete-user.php',
        dataType: "json",
        cache: false,
        data: mydata,
        error : function(jqXHR ,textStatus,  errorThrown) {
  				myalert('error:' + jqXHR.responseText);
  			},
        success: function( data ) {
          if (data.success == true) {
            response( data.list);
          } else {
            myalert('error:' + $.base64.decode(data.message));
          }
        }
      });
    },
    minLength: 3,
    delay: 300, //default
    select: function( event, ui ) {
      $("#user_id").val(ui.item.id);
      datasend='&user_id='  + encodeURI(ui.item.id.trim());
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#user").val("");
          $("#user_id").val("");
        }
    }
  });
  
  
  $('#add_user').click(function(event) {  
    if (from_php_id <=0) {
      myalert('error:<?php echo gks_lang('Αποθηκεύστε πρώτα την εγγραφή');?>');
      return; 
    }
    
    datasend='';
    datasend+='id= <?php echo $id;?>';    
    datasend+='&user_id='  + encodeURI($("#user_id").val().trim());    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-usersgroups-item-user_add.php',
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
					myalert('error:<?php echo gks_lang('Παρακαλώ δοκιμάστε αργότερα');?>');
				} else {
				  
					if (data.success == true) {
					  
  					//myalert('ok:' + 'OK');
  					window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  });

  function group_comments_change() {gks_resize_textarea($(this));}
  $('#group_comments').on(mychange, group_comments_change);
  gks_resize_textarea($('#group_comments'));

  var elems_switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  elems_switchery1_this.forEach(function(html) {
    var switchery1_this = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });  
  
  //generic
  gks_page_loading=false;
  
  if (from_php_scrollto!='') {
    if ($('#' + from_php_scrollto).length>0) {
      $([document.documentElement, document.body]).animate({
          scrollTop: $('#' + from_php_scrollto).offset().top
      }, 500);
    }
    if (window.location.href.endsWith('&scrollto=' + from_php_scrollto)) {
      newurl=window.location.href;
      newurl=newurl.substring(0,newurl.length-('&scrollto=' + from_php_scrollto).length);
      
      window.history.pushState({}, window.document.title, newurl);
    }
  } else if (from_php_temp_mypropertiesheight!=0) {
    $("html").scrollTop(from_php_temp_mypropertiesheight);
  }



  $('.myneedsave').on('input keyup paste', function() {
    need_save=true; 
  });

  window.onbeforeunload = function() {
    if (need_save==false) return;
    return '<?php echo gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');?>';
  };

  need_save=false;
    
});
</script>
  



<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


