<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Ημερολόγιο','part2');
$nav_active_array=array('crm','crm_calendar');


db_open();
stat_record();
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_calendar','view',0);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}


$perm_company_subs_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_calendar','view',0);
$perm_company_subs_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_calendar','edit',0);
$perm_company_subs_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_calendar','add',0);
$perm_company_subs_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_calendar','delete',0);

$perm_calendar_other_users_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_calendar_other_users','view',0);
$perm_calendar_other_users_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_calendar_other_users','edit',0);
$perm_calendar_other_users_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_calendar_other_users','add',0);
$perm_calendar_other_users_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_calendar_other_users','delete',0);



//$gks_user_settings= gks_get_user_settings($my_wp_user_id);
//print '<pre>';print_r($gks_user_settings);die();
 


$initialdate=showDate(time(), 'Y-m-d',1);
$id=0; if (isset($_GET['id'])) $id=intval($_GET['id']);
if ($id>0) {
	$sql="select * from gks_calendar where id_calendar=".$id;
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
	
	$initialdate=showDate(strtotime($row['calendar_start']), 'Y-m-d',1);
}


include_once('_my_header_admin.php');

?>

<div class="container-fluid gksitemheader" id="gks_main_session1">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h3><?php echo $my_page_title;?></h3>
    </div>
  </div>
</div>


<div style="clear: both;"></div>

<i class="fas fa-bars" id="cal_submenu_fixed" <?php if ($gks_user_settings['calendar']['leftpanel']=='1') echo 'style="display:none;"';?>></i>
<div id="gks_main_session2">

  <div id="main_panel_left" <?php if ($gks_user_settings['calendar']['leftpanel']!='1') echo 'style="display:none;"';?>>
    <div style="text-align:center;padding-bottom:24px;">
      <i class="fas fa-bars" id="cal_submenu_static" <?php if ($gks_user_settings['calendar']['leftpanel']!='1') echo 'style="display:none;"';?>></i>
    </div>
    <div>
      <input id="c_cal_small" type="text" class="form-control form-control-sm myneedsave" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px;">
    </div>
    <div id="gks_calendar_users_list">

      <?php
      $calendar_user_color='#3788d8';
      if (isset($gks_user_settings['calendar']['user_color'])) $calendar_user_color=$gks_user_settings['calendar']['user_color'];
      $calendar_visible_cal=1;
      if (isset($gks_user_settings['calendar']['visible_cal'])) $calendar_visible_cal=$gks_user_settings['calendar']['visible_cal'];
      if ($id>0) $calendar_visible_cal=1;
      
      $other_users_rows=[];
      if ($perm_calendar_other_users_view) {
        $sql="SELECT gks_calendar_other_users.other_user_id, gks_calendar_other_users.other_user_color,other_visible,".GKS_WP_TABLE_PREFIX."users.gks_nickname
        FROM gks_calendar_other_users LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_calendar_other_users.other_user_id = ".GKS_WP_TABLE_PREFIX."users.ID
        WHERE gks_calendar_other_users.this_user_id=".$my_wp_user_id." and ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null
        and gks_calendar_other_users.other_myobj='cal'
        ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname";
        $result = $db_link->query($sql);        
        if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
        while ($row = $result->fetch_assoc()) {
          $other_users_rows[]=$row;
        }
      }
      $cal_allusers_toggle_checked=true;
      if ($calendar_visible_cal==0) {
        $cal_allusers_toggle_checked=false;
      } else {
        foreach ($other_users_rows as $row) {
          if ($row['other_visible']==0) {
            $cal_allusers_toggle_checked=false; 
          }
        }
      }      
      ?>
      <div>
        <input type="checkbox" name="cal_allusers_toggle" id="cal_allusers_toggle" <?php if ($cal_allusers_toggle_checked) echo 'checked';?>> 
        <?php echo gks_lang('Ημερολόγια','part2');?>:
      </div>
      <div class="cal_user_row" data-id="0">
        <input type="checkbox" name="cal_user" data-id="0" id="cal_user_0" <?php if ($calendar_visible_cal==1) echo 'checked';?>> 
        <input type="text" class="cal_user_color" data-id="0" value="<?php echo $calendar_user_color;?>">
        <label for="cal_user_0" class="cal_user_label"><?php echo gks_lang('Δικό μου');?></label>
      </div>
<?php
      foreach ($other_users_rows as $row) {
        echo '<div class="cal_user_row" data-id="'.$row['other_user_id'].'">'.
        '<input type="checkbox" name="cal_user" data-id="'.$row['other_user_id'].'" id="cal_user_'.$row['other_user_id'].'" '.($row['other_visible']==0?'':'checked').'>'. 
        //' <div class="cal_user_color_con"><div class="cal_user_color_wra" data-id="'.$row['other_user_id'].'" style="background-color: '.$row['other_user_color'].';">'.
        ' <input type="text" class="cal_user_color" data-id="'.$row['other_user_id'].'" value="'.$row['other_user_color'].'" '.($perm_company_subs_edit ? '' : 'disabled').'>'.
        //'</div></div>'.
        ' <label for="cal_user_'.$row['other_user_id'].'" class="cal_user_label">'.$row['gks_nickname'].'</label>'.
        ($perm_company_subs_edit ? ' <i class="fas fa-trash-alt cal_user_remove" data-aa="'.$row['other_user_id'].'" data-myobj="cal"></i>' : '').
        '</div>';
      }
      
?>
      <?php if ($perm_calendar_other_users_add) { ?>
      <div class="cal_user_row" id="div_cal_user_add" >
        <i class="fas fa-plus-circle" id="cal_user_add"></i> 
        <input id="cal_user_add_user" type="text" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="display:none;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
      </div>
      <?php } ?>
<?php

      $calendar_user_color_task='#bf9000';
      if (isset($gks_user_settings['calendar']['user_color_task'])) $calendar_user_color_task=$gks_user_settings['calendar']['user_color_task'];
      $calendar_visible_task=1;
      if (isset($gks_user_settings['calendar']['visible_task'])) $calendar_visible_task=$gks_user_settings['calendar']['visible_task'];

      $other_users_task_rows=[];
      if ($perm_calendar_other_users_view) {
        $sql="SELECT gks_calendar_other_users.other_user_id, gks_calendar_other_users.other_user_color,other_visible,".GKS_WP_TABLE_PREFIX."users.gks_nickname
        FROM gks_calendar_other_users LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_calendar_other_users.other_user_id = ".GKS_WP_TABLE_PREFIX."users.ID
        WHERE gks_calendar_other_users.this_user_id=".$my_wp_user_id." and ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null
        and gks_calendar_other_users.other_myobj='task'
        ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname";
        $result = $db_link->query($sql);        
        if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
        
        
        while ($row = $result->fetch_assoc()) {
          $other_users_task_rows[]=$row;
        }
      }
      
      $cal_alltasks_toggle_checked=true;
      if ($calendar_visible_task==0) {
        $cal_alltasks_toggle_checked=false;
      } else {
        foreach ($other_users_task_rows as $row) {
          if ($row['other_visible']==0) {
            $cal_alltasks_toggle_checked=false; 
          }
        }
      }  
?>
            
      <div style="margin-top: 10px;">
        <input type="checkbox" name="cal_alltasks_toggle" id="cal_alltasks_toggle" <?php if ($cal_alltasks_toggle_checked) echo 'checked';?>>
        <?php echo gks_lang('Εργασίες');?>:
      </div>
      <div class="cal_user_row_task" data-id="0">
        <input type="checkbox" name="cal_user_task" data-id="0" id="cal_user_task_0" <?php if ($calendar_visible_task==1) echo 'checked';?>> 
        <input type="text" class="cal_user_color_task" data-id="0" value="<?php echo $calendar_user_color_task;?>">
        <label for="cal_user_task_0" class="cal_user_label_task"><?php echo gks_lang('Δικό μου');?></label>
      </div>
<?php
      foreach ($other_users_task_rows as $row) {
        echo '<div class="cal_user_row_task" data-id="'.$row['other_user_id'].'">'.
        '<input type="checkbox" name="cal_user_task" data-id="'.$row['other_user_id'].'" id="cal_user_task_'.$row['other_user_id'].'" '.($row['other_visible']==0?'':'checked').'>'. 
        //' <div class="cal_user_color_con"><div class="cal_user_color_wra" data-id="'.$row['other_user_id'].'" style="background-color: '.$row['other_user_color'].';">'.
        ' <input type="text" class="cal_user_color_task" data-id="'.$row['other_user_id'].'" value="'.$row['other_user_color'].'" '.($perm_company_subs_edit ? '' : 'disabled').'>'.
        //'</div></div>'.
        ' <label for="cal_user_task_'.$row['other_user_id'].'" class="cal_user_label_task">'.$row['gks_nickname'].'</label>'.
        ($perm_company_subs_edit ? ' <i class="fas fa-trash-alt cal_user_remove_task" data-aa="'.$row['other_user_id'].'" data-myobj="task"></i>' : '').
        '</div>';
      }
?>
      <?php if ($perm_calendar_other_users_add) { ?>
      <div class="cal_user_row_task" id="div_cal_user_add_task" >
        <i class="fas fa-plus-circle" id="cal_user_add_task"></i> 
        <input id="cal_user_add_user_task" type="text" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="display:none;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
      </div>
      <?php } ?>












<?php

      $calendar_user_color_activ='#9000bf';
      if (isset($gks_user_settings['calendar']['user_color_activ'])) $calendar_user_color_activ=$gks_user_settings['calendar']['user_color_activ'];
      $calendar_visible_activ=1;
      if (isset($gks_user_settings['calendar']['visible_activ'])) $calendar_visible_activ=$gks_user_settings['calendar']['visible_activ'];

      $other_users_activ_rows=[];
      if ($perm_calendar_other_users_view) {
        $sql="SELECT gks_calendar_other_users.other_user_id, gks_calendar_other_users.other_user_color,other_visible,".GKS_WP_TABLE_PREFIX."users.gks_nickname
        FROM gks_calendar_other_users LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_calendar_other_users.other_user_id = ".GKS_WP_TABLE_PREFIX."users.ID
        WHERE gks_calendar_other_users.this_user_id=".$my_wp_user_id." and ".GKS_WP_TABLE_PREFIX."users.ID Is Not Null
        and gks_calendar_other_users.other_myobj='activ'
        ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname";
        $result = $db_link->query($sql);        
        if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
        
        
        while ($row = $result->fetch_assoc()) {
          $other_users_activ_rows[]=$row;
        }
      }
      
      $cal_allactivs_toggle_checked=true;
      if ($calendar_visible_activ==0) {
        $cal_allactivs_toggle_checked=false;
      } else {
        foreach ($other_users_activ_rows as $row) {
          if ($row['other_visible']==0) {
            $cal_allactivs_toggle_checked=false; 
          }
        }
      }  
?>
            
      <div style="margin-top: 10px;">
        <input type="checkbox" name="cal_allactivs_toggle" id="cal_allactivs_toggle" <?php if ($cal_allactivs_toggle_checked) echo 'checked';?>>
        <?php echo gks_lang('Δραστηριότητες');?>:
      </div>
      <div class="cal_user_row_activ" data-id="0">
        <input type="checkbox" name="cal_user_activ" data-id="0" id="cal_user_activ_0" <?php if ($calendar_visible_activ==1) echo 'checked';?>> 
        <input type="text" class="cal_user_color_activ" data-id="0" value="<?php echo $calendar_user_color_activ;?>">
        <label for="cal_user_activ_0" class="cal_user_label_activ"><?php echo gks_lang('Δικό μου');?></label>
      </div>
<?php
      foreach ($other_users_activ_rows as $row) {
        echo '<div class="cal_user_row_activ" data-id="'.$row['other_user_id'].'">'.
        '<input type="checkbox" name="cal_user_activ" data-id="'.$row['other_user_id'].'" id="cal_user_activ_'.$row['other_user_id'].'" '.($row['other_visible']==0?'':'checked').'>'. 
        //' <div class="cal_user_color_con"><div class="cal_user_color_wra" data-id="'.$row['other_user_id'].'" style="background-color: '.$row['other_user_color'].';">'.
        ' <input type="text" class="cal_user_color_activ" data-id="'.$row['other_user_id'].'" value="'.$row['other_user_color'].'" '.($perm_company_subs_edit ? '' : 'disabled').'>'.
        //'</div></div>'.
        ' <label for="cal_user_activ_'.$row['other_user_id'].'" class="cal_user_label_activ">'.$row['gks_nickname'].'</label>'.
        ($perm_company_subs_edit ? ' <i class="fas fa-trash-alt cal_user_remove_activ" data-aa="'.$row['other_user_id'].'" data-myobj="activ"></i>' : '').
        '</div>';
      }
?>
      <?php if ($perm_calendar_other_users_add) { ?>
      <div class="cal_user_row_activ" id="div_cal_user_add_activ" >
        <i class="fas fa-plus-circle" id="cal_user_add_activ"></i> 
        <input id="cal_user_add_user_activ" type="text" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="display:none;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
      </div>
      <?php } ?>
      
      
      


      
                  
    </div>
    
  </div>
  <div id="main_panel_right" <?php if ($gks_user_settings['calendar']['leftpanel']=='0') echo 'style="width:100%;"';?>>
    <div id="calendar_div" style="z-index11:100"></div>
  </div>
</div>


<div style="clear:both;"></div>

<div id="mypostform">
<div id="dialog_event" class="container-fluid" style="display:none;margin: 0px;padding:0px;">
  <div class="row" style="margin: 10px 0px 10px 0px;">
    <div class="col-lg-12" style="text-align:center">
      <h3><?php echo gks_lang('Συμβάν');?></h3>
    </div>
  </div>
  <div class="row" style="margin: 0px;">
    <div class="col-lg-6">
    
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Βασικά');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('bas');?>> 
          
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Είναι του');?>:</label>
            <div class="col-md-8">
              <div>
                <span style="white-space: nowrap;"><input type="radio" name="c_user_id" value="0" id="c_user_id0"> <label class="gks_label" for="c_user_id0" style="display:inline;padding-right:18px"><?php echo gks_lang('Δικό μου');?></label></span> 
                <span style="white-space: nowrap;"><input type="radio" name="c_user_id" value="1" id="c_user_id1"> <label class="gks_label" for="c_user_id1" style="display:inline"><?php echo gks_lang('Άλλου');?></label></span>
              </div>
              <div id="div_c_user_id_other">
                <input id="c_user_id_other" type="text" class="form-control form-control-sm myneedsave" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
              </div>
            </div>
          </div>
          
          
          <div class="form-group row">
            <label for="c_title" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Θέμα');?>:</label>
            <div class="col-md-8">
              <input id="c_title" type="text" class="form-control form-control-sm myneedsave" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
                   
          <div class="form-group row">
            <label for="c_allday" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ολοήμερο');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="c_allday" value="1" class="switchery1_sel">
            </div>
          </div>
          <div class="form-group row">
            <label for="c_start" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Από');?>:</label>
            <div class="col-md-8">
              <input id="c_start" type="text" class="form-control form-control-sm myneedsave" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px;">
            </div>
          </div>
          <div class="form-group row">
            <label for="c_end" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έως');?>:</label>
            <div class="col-md-8">
              <input id="c_end" type="text" class="form-control form-control-sm myneedsave" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px;">
            </div>
          </div>
          <div class="form-group row">
            <label for="c_message" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-md-8">
              <textarea id="c_message" class="form-control form-control-sm myneedsave" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="min-height: 100px;"></textarea>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Διαθεσιμότητα');?>:</label>
            <div class="col-md-8">
              <span style="white-space: nowrap;"><input type="radio" name="c_is_exclusive" value="1" id="c_is_exclusive1"> <label class="gks_label" for="c_is_exclusive1" style="display:inline"><?php echo gks_lang('Απασχολημένος');?></label></span>
              <span style="white-space: nowrap;"><input type="radio" name="c_is_exclusive" value="0" id="c_is_exclusive0"> <label class="gks_label" for="c_is_exclusive0" style="display:inline;padding-right:18px"><?php echo gks_lang('Διαθέσιμος');?></label></span> 
            </div>
          </div>
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ορατότητα');?>:</label>
            <div class="col-md-8 ">
              <span style="white-space: nowrap;"><input type="radio" name="c_is_private" value="0" id="c_is_private0"> <label class="gks_label" for="c_is_private0" style="display:inline;padding-right:18px"><?php echo gks_lang('Δημόσιο');?></label></span> 
              <span style="white-space: nowrap;"><input type="radio" name="c_is_private" value="1" id="c_is_private1"> <label class="gks_label" for="c_is_private1" style="display:inline"><?php echo gks_lang('Ιδιωτικό');?></label></span>
            </div>
          </div>
          <div class="form-group row">
            <label for="c_color" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χρώμα');?>:</label>
            <div class="col-md-8">
              <input id="c_color" type="text" class="form-control form-control-sm myneedsave" value="#ffffff" 
              style="max-width:100px;display: inline-block;">
              <span id="set_def_color" class="tooltipster" title="<?php echo gks_lang('Προεπιλεγμένο χρώμα');?>" 
                style="width:30px;height:30px;background-color: #3788d8;display: inline-block;vertical-align: top;
                position: relative;top: 0px;margin-left: 2px;cursor:pointer;"></span>
            </div>
          </div> 

            
        
        </div>

        
      </div>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ειδοποιήσεις');?>
        </div>
        <div class="card-body"  <?php echo gks_card_body('notif');?>> 
					<div id="c_notification">
						
				  </div>
				  <div class="form-group row" id="c_notification_add_alone_div">
				  	<div class="col-md-12" style="text-align: center;">
				  		<i class="fas fa-plus-circle" id="c_notification_add_alone"></i>
				  	</div>	
				  </div>	
	      </div>
	    </div>


    </div>
    <div class="col-lg-6">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Τοποθεσία');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('loca');?>> 

          <div class="form-group row">
            <label for="c_odos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Οδός');?>:</label>
            <div class="col-md-8">
              <input id="c_odos" type="text" class="form-control form-control-sm myneedsave" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              <small class="form-text text-muted auto_googlemaps" id="c_odos_auto_googlemaps"></small>
            </div>
          </div>
          <div class="form-group row">
            <label for="c_arithmos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός');?>:</label>
            <div class="col-md-8">
              <input id="c_arithmos" type="text" class="form-control form-control-sm myneedsave" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          
          
          <div class="form-group row">
            <label for="c_orofos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όροφος');?>:</label>
            <div class="col-md-8">
              <input id="c_orofos" type="text" class="form-control form-control-sm myneedsave" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="c_perioxi" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιοχή');?>:</label>
            <div class="col-md-8">
              <input id="c_perioxi" type="text" class="form-control form-control-sm myneedsave" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="c_poli" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πόλη');?>:</label>
            <div class="col-md-8">
              <input id="c_poli" type="text" class="form-control form-control-sm myneedsave" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="c_tk" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΤΚ');?>:</label>
            <div class="col-md-8">
              <input id="c_tk" type="text" class="form-control form-control-sm myneedsave" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
            </div>
          </div>


          <div class="form-group row">
            <label for="c_country_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χώρα');?>:</label>
            <div class="col-md-8">
              <select id="c_country_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $ee_initials='';
                $lang_prepare_gks_country=gks_lang_data_obj_prepare('gks_country','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_country, array('country_name'));
                $sql="select id_country,country_ee,country_initials,".gks_lang_sql_field('country_name',$lang_prepare_gks_country)." 
                FROM ".$lang_prepare_gks_country['sql']['from1']." gks_country 
                ".$lang_prepare_gks_country['sql']['from2']."
                ORDER BY country_name";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'admin-users-item.php error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_country'].'" data-ci="'.$row_select['country_initials'].'" data-ee="'.trim_gks($row_select['country_ee']).'"';
                  //if ($row_select['id_country']==$row['c_country_id']) {echo ' selected '; $ee_initials=trim_gks($row_select['country_ee']);}
                  echo '>'.$row_select['country_name'].'</option>';
                }?>
              </select>    
            </div>
          </div> 
          <div class="form-group row">
            <label for="c_nomos_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Νομός');?>:</label>
            <div class="col-md-8">
              <select id="c_nomos_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                
              </select>    
            </div>
          </div>
          
          <div class="form-group row">
            <label for="c_map_latitude" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γεωγραφικό Πλάτος');?>:</label>
            <div class="col-md-8">
              <input id="c_map_latitude" type="number" class="form-control form-control-sm myneedsave" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="-90" max="90" step="0.00001">
            </div>
          </div> 
          <div class="form-group row">
            <label for="c_map_longitude" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γεωγραφικό Μήκος');?>:</label>
            <div class="col-md-8">
              <input id="c_map_longitude" type="number" class="form-control form-control-sm myneedsave" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="-180" max="180" step="0.00001">
            </div>
          </div> 
          <div class="form-group row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χάρτης');?>:</label>
            <div class="col-md-8">
              <div style="text-align:left;">
                <button id="showmap" class="btn btn-sm btn-primary" style="cursor:pointer"><?php echo gks_lang('Εμφάνιση χάρτη');?></button>
                <button id="geocode_pos" class="btn btn-sm btn-info" style="cursor:pointer;" disabled><?php echo gks_lang('Στίγμα');?> <span id="geocode_pos_icon"><i class="fas fa-map-marker-alt"></i></span></button>
                <button id="map_pos" class="btn btn-sm btn-info" style="cursor:pointer;" disabled title="<?php echo gks_lang('Εντοπισμός της τρέχουσας θέσης σας');?>"><?php echo gks_lang('Εδώ');?></button>
                
              </div>
            </div>
            <div class="col-md-12" style="height:0px">
              <div id="map" style="width:100%;height:100%"></div>  
            </div>             
          </div>

          
        </div>
      
      
      </div>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Συμμετέχοντες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('partic');?>> 
				  <div class="form-group row" id="c_participant_me_div" style="display:none;">
            <div class="col-md-12 c_participant_item" data-aa="0">
              <input type="text" class="form-control form-control-sm myneedsave c_participant_name" data-aa="0" value="" data-user_id="0" disabled>
              <i class="fas fa-user   c_participant_is_org c_participant_is_org0" data-aa="0"></i>
              <i class="fas fa-user   c_participant_is_opt c_participant_is_opt0" data-aa="0"></i>
              <i class="far fa-circle c_participant_r_type c_participant_r_type0" data-aa="0"></i>
            </div>
				  </div>
					<div id="c_participant">
						
				  </div>
				  <div class="form-group row" id="c_participant_add_alone_div">
				  	<div class="col-md-12" style="text-align: center;">
				  		<i class="fas fa-plus-circle" id="c_participant_add_alone"></i>
				  	</div>	
				  </div>
				  <div class="form-group row" id="c_participant_text_div" style="display:none;">
				  	<div class="col-md-12" style="text-align: left;">
				  		<?php echo gks_lang('Σύμβολα');?>:<br>
				  		<i class="fas fa-user   c_participant_is_org c_participant_is_org0" style="padding-right: 9px;"></i>: <?php echo gks_lang('Συμμετοχή');?><br>
				  		<i class="fas fa-user-cog c_participant_is_org c_participant_is_org1" style="padding-right: 0px;"></i>: <?php echo gks_lang('Διοργανωτής');?><br>
				  		<i class="far fa-user c_participant_is_opt c_participant_is_opt1" style="padding-right: 9px;"></i>: <?php echo gks_lang('Προαιρετική Συμμέτοχη');?><br>
				  		<i class="fas fa-check-circle    c_participant_r_type c_participant_r_typeyes" style="padding-right: 9px;"></i>: <?php echo gks_lang('Θα Συμμετάσχει');?><br>
				  		<i class="fas c_participant_r_type c_participant_r_typeno fa-times-circle" style="padding-right: 9px;"></i>: <?php echo gks_lang('Θα Απουσιάσει');?><br>
				  		<i class="fas c_participant_r_type c_participant_r_typeisos fa-question-circle" style="padding-right: 9px;"></i>: <?php echo gks_lang('Ίσως να συμμετάσχει');?><br>
				  		
				  		
				  	</div>	
				  </div>
				            
        </div>
      </div>    

    </div>
      

            
      
  </div>
</div>    
</div>

<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f" style="display:none;">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" class="btn btn-danger" id="delete_calendar" <?php if ($perm_company_subs_delete==false) echo 'disabled';?>><?php echo gks_lang('Διαγραφή');?></button>
      <button type="button" class="btn btn-dark" id="dialog_event_cancel"><?php echo gks_lang('Άκυρο');?></button>


      
    </div>
  </div>
</div>

<div class="container-fluid" style="display:none;" id="dialog_event_logs">
  <div class="row">

    <div class="col-lg-6">
      <div id="div_object_rel"></div>
    </div>
    
    <div class="col-lg-6">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body"  <?php echo gks_card_body('kat');?>>      
  
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" id="c_event_id"></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" id="c_event_user_id_add"></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" id="c_event_mydate_add"></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" id="c_event_user_id_edit"></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" id="c_event_mydate_edit"></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('IP');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" id="c_event_myip"></span></div>
          </div>
        </div>
      </div>
    </div>
    
    
    
  </div>
</div>

<div id="calc_hourglass"
  style="
  display:none;
  border:1px solid gray;
  padding: 8px 0px 0px 12px;
  border-radius:4px;
  background-color:#343a40;
  position: fixed;
  left: 0px;
  bottom: 0px;
  z-index: 1000;
  width: 40px;
  height: 40px;">
  <i class="fas fa-hourglass-half" style="color:coral;font-size:120%;"></i>
</div>

<div id="calc_refetch"
  style="
  display1:none;
  cursor:pointer;
  border:1px solid gray;
  padding: 8px 0px 0px 12px;
  border-radius:4px;
  background-color:#343a40;
  position: fixed;
  left: 0px;
  bottom: 0px;
  z-index: 9999;
  width: 40px;
  height: 40px;">
  <i class="fas fa-sync" style="color:coral;font-size:120%;position: relative;left: -3px;"></i>
</div>




<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>;

var from_php_start_id=<?php echo $id; ?>;
var from_php_user_settings_autocomplete_address='<?php echo $gks_user_settings['autocomplete']['address'];?>';
var from_php_dialog_object_rel_curr='gks_calendar';

var from_php_initialdate='<?php echo $initialdate;?>';
var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;
var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 
var from_php_my_wp_user_id=<?php echo $my_wp_user_id; ?>;
var from_php_gks_nickname=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');
    
var from_php_view='<?php if ($id>0) echo 'timeGridDay'; else echo $gks_user_settings['calendar']['view'];?>'; 
var from_php_full24='<?php if ($id>0) echo 1; else echo $gks_user_settings['calendar']['full24'];?>'; 

var from_php_activity_model='';
var from_php_activity_model_id=0;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=-2;
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_calendar','edit',  0);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_calendar','add',   0);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_calendar','delete',0);?>;



jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>


  
  
});
</script>



<script src="js/admin-crm-calendar.js?v=<?php echo $gks_cache_version;?>"></script>
<script src='js/moment-with-locales.min.js'></script>

<link href='js/fullcalendar/lib/main.css' rel='stylesheet' />
<script src='js/fullcalendar/lib/main.js'></script>
<script src='js/fullcalendar/lib/locales-all.js'></script>

<link href="css/admin-crm-calendar.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">


<?php

echo gks_from_googlemaps_scripts();


//db_close();
include_once('_my_footer_admin.php');




