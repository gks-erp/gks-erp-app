<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Μαζική Αποστολή SMS-Viber-email');
$nav_active_array=array('crm','manage_sms','manage_mass_messages');
db_open();
stat_record();
$perm_email_add  =gks_permission_user_can_action_php($my_wp_user_id,'gks_mass_messages','add',0);
if ($perm_email_add==false) {header('Location: /my/admin-deny.php?message='.rawurlencode(gks_lang('Δεν επιτρέπεται η πρόσβαση στην αποστολή email'))); die();}

$e=[];
$template_id=0;if (isset($_GET['template_id'])) $template_id=intval($_GET['template_id']);
if ($template_id>0) {
  $sql="select * from gks_mass_messages where id_mass_message=".$template_id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows==1) {
    $e = $result->fetch_assoc();
    
  }
}
$list_ids=[];
if ($template_id==0 and isset($_GET['list'])) {
  $temp=trim_gks($_GET['list']);
  $filepath=GKS_SITE_PATH.'tmp/mass_message_'.$temp.'.json';
  if (file_exists($filepath)) {
    $temp=@file_get_contents($filepath);
    if (is_string($temp) and $temp!='') {
      $list_ids = json_decode($temp, true);
      if ($list_ids === null && json_last_error() !== JSON_ERROR_NONE) {
        debug_mail(false,'json_decode error list',$_GET['list']);
        echo '<pre>Error  json decode data. Please retry.</pre>';die();
      }
      //echo '<pre>';print_r($list_ids);die();
    }
    
  }
  
}
//print '<pre>';print_r($exr);die();

include_once('_my_header_admin.php');
?>
<link href="css/admin-mass-messages-new.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">


<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-md-12" style="text-align:center">
      <h3><?php echo $my_page_title;?></h3>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row">
    <div class="col-lg-6">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Κριτήρια αναζήτησης');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('filters');?>>         

          <div class="form-group row">
            <label for="recommendation_user" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Απλή αναζήτηση');?>:</label>
            <div class="col-sm-8">
              <input type="text"   name="recommendation_user"    id="recommendation_user"   class="form-control form-control-sm" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">              
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ρόλοι χρηστών');?>:</label>
            <div class="col-sm-8" style="padding: 0.25rem 15px;">
            <?php
            $gks_wp_system_roles = gks_wp_system_roles_func();
            foreach ($gks_wp_system_roles as $role_item) {
              echo '<div class="divrole"><input class="rolecheckbox" type="checkbox" name="role_'.$role_item['id'].'" id="role_'.$role_item['id'].'" value="'.$role_item['id'].'"> <label for="role_'.$role_item['id'].'">'.$role_item['name'].'</label></div>';
            }
            ?>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ομάδες χρηστών');?>:</label>
            <div class="col-sm-8" style="padding: 0.25rem 15px;">
            
            <?php
            $sql="select gks_users_groups.id_users_group as id, 
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
            ORDER BY descr";
    
            $result_select = $db_link->query($sql);        
            if (!$result_select) {
              debug_mail(false,'error sql',$sql);
              die('sql error');
            }
            while ($row_select = $result_select->fetch_assoc()) {
              echo '<div class="divgroup"><input class="groupcheckbox" type="checkbox" name="group_'.$row_select['id'].'" id="group_'.$row_select['id'].'" data-id="group_'.$row_select['id'].'" value="'.$row_select['id'].'" > <label for="group_'.$row_select['id'].'">'.$row_select['descr'].'</label></div>';
            }
            ?>           
            </div>
          </div>

          <div class="form-group row">
            <div class="col-sm-12">
              <div style="text-align: center;margin-top: 40px;">
                <input id="make_search" class="btn btn-primary" value="<?php echo gks_lang('Αναζήτηση');?>">
              </div>
            </div>
          </div>
            
          <div class="form-group row">
            <div class="col-sm-12">
              <div id="search_results" style="margin-top: 40px;padding: 20px;">
              </div>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-sm-12">
              <div id="search_span" style="padding: 20px;">
                <?php echo gks_lang('Εδώ θα εμφανιστούν τα αποτελέσματα');?>
              </div>
            </div>
          </div>

          <div class="form-group row">
            <div class="col-sm-12">
              <div id="search_span2" style="padding: 20px;display:none;">
                --
              </div>
            </div>
          </div>
            
          <div class="form-group row">
            <div class="col-sm-12">
              <div id="results_button" style="display:none;text-align: center;margin-top: 40px;">
                <input id="results_button_all" class="btn btn-primary" type="submit" value="<?php echo gks_lang('Προσθήκη όλων');?>">
              </div>
            </div>
          </div>


        </div>
      </div>
    </div>
    


    
    <div class="col-lg-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Αποδέκτες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('sentto');?>>  
          
          <div class="form-group row">
            <div class="col-sm-12">
              <div id="users_list" style="padding: 20px;">
              <?php
              $mylist=[];
              if (!empty($e['mylist'])) {
                $mylist=json_decode($e['mylist'],true);
              } else if (count($list_ids)>0) {
                $mylist=$list_ids;
              }
              //$myresult=json_decode($e['myresult'],true);
              //print '<pre>';print_r($mylist);print '</pre>';
              //print '<pre>';print_r($myresult);print '</pre>';

              if (count($mylist)>=1) {
                $sql_list="SELECT ID as i, 
                gks_nickname as n, 
                user_email as e1,
                gks_mobile as m1, 
                viber_id as v1, 
                viber_subscribed as v2
                FROM ".GKS_WP_TABLE_PREFIX."users
                where ID in (".implode(',',$mylist).")
                ORDER BY gks_nickname";
                $result_list = $db_link->query($sql_list);  
                if (!$result_list) {
                  debug_mail(false,'error sql',$sql_list);
                  $return = array('success' => false, 'message' => base64_encode('sql error'));
                  echo json_encode($return); die(); }
                while ($rr = $result_list->fetch_assoc()) {
                  $rr['i']=intval($rr['i']);
                  $rr['v2']=intval($rr['v2']);
                  if ($rr['v2']!=0 and $rr['v1']!='') {
                    $rr['v']=1;
                  } else {
                    $rr['v']=0;  
                  }
                  if (trim($rr['m1'])!='') {
                    $rr['m']=1;
                  } else {
                    $rr['m']=0;
                  }
                  if (trim($rr['e1'])!='') {
                    $rr['e']=1;
                  } else {
                    $rr['e']=0;
                  }
                  if ($rr['v']==1 or $rr['m']==1 or $rr['e']==1) {
                    echo
        				    '<div class="user_result" data-id="'.$rr['i'].'"' .
        				    ' data-m="'.$rr['m'].'"' .
        				    ' data-v="'.$rr['v'].'"' .
        				    ' data-e="'.$rr['e'].'"' .
        				    '>' .
        				      '<div class="user_result1">'.$rr['n'].'</div>' .
        				      '<div class="user_result2">' . 
        				        ($rr['v']==0 ? '' : '<img class="imgviber" src="img/viber.png">') . 
        				        ' ' . 
        				        ($rr['m']==0 ? '' : '<img class="imgsms" src="img/sms.png">') . 
        				        ' ' . 
        				        ($rr['e']==0 ? '' : '<img class="imgemail" src="img/email2.png">') . 
        				      '</div>' .
        				      
        				      '<div class="user_result3">' .
        				        '<img class="list_add" src="img/add.png">' .
        				        '<img class="list_del" src="img/delete.png">' .
        				      '</div>' .
        				    '</div>';
        				  }
                }
                

              }
                
              
              ?>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-sm-12">
              <div id="users_span" style="padding: 20px;">
                <?php echo gks_lang('Δεν έχετε προσθέσει ακόμα αποδέκτες');?>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <div class="col-sm-12">
              <div id="users_span2" style="padding: 20px;display:none;">
                --
              </div>              
            </div>
          </div>
          <div class="form-group row">
            <div class="col-sm-12">
              <div id="list_button" style="display:none;text-align: center;margin-top: 40px;">
                <input id="list_button_all" class="btn btn-primary" type="submit" value="<?php echo gks_lang('Αφαίρεση όλων');?>">
              </div>              
            </div>
          </div>
          
                        
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row">
    <div class="col-lg-3">
      
    </div>
    <div class="col-lg-6">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Μήνυμα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('run');?>>

          <div class="form-group row">
            <label for="" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αποστολή με');?>:</label>
            <div class="col-sm-8 sent_with_type" style="padding: 0.25rem 15px;">
              <div>
                <input class="sendby_checkbox myneedsave" type="checkbox" id="send_with_viber" 
                <?php if (isset($e['send_with_viber'])==false or $e['send_with_viber']==1) echo 'checked';?>> 
                  <label for="send_with_viber"><?php echo gks_lang('Viber');?></label> ή
              </div> 
              <div>
                <input class="sendby_checkbox myneedsave" type="checkbox" id="send_with_sms" 
                <?php if (isset($e['send_with_sms'])==false or $e['send_with_sms']==1) echo 'checked';?>> 
                  <label for="send_with_sms"><?php echo gks_lang('SMS');?></label> ή
              </div>
              <div>
                <input class="sendby_checkbox myneedsave" type="checkbox" id="send_with_email"
                <?php if (isset($e['send_with_email'])==false or $e['send_with_email']==1) echo 'checked';?>> 
                  <label for="send_with_email"><?php echo gks_lang('email');?></label>
              </div> 
            </div>
          </div>
          
          <div class="form-group row sendby_sms" >
            <label for="from" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Από SMS');?>:</label>
            <div class="col-sm-8">
              <select name="from" id="from" class="form-control form-control-sm myneedsave">
                
              <?php 
              $exv='';if (!empty($e['sender_sms_provider']) and !empty($e['sender_sms_sender'])) $exv=trim_gks($e['sender_sms_provider']).':'.trim_gks($e['sender_sms_sender']);
              
              $sql="SELECT gks_erp_app_mobile.id_erp_app_mobile, gks_erp_app_mobile.erp_app_mobile_name, 
              gks_erp_app_mobile.erp_app_mobile_phonenumber, gks_erp_app_mobile_ping.mydate
              FROM gks_erp_app_mobile 
              LEFT JOIN gks_erp_app_mobile_ping ON gks_erp_app_mobile.mobile_last_ping_id = gks_erp_app_mobile_ping.id
              WHERE gks_erp_app_mobile.erp_app_mobile_disabled=0
              and   gks_erp_app_mobile.erp_app_mobile_can_sms=1
              ORDER BY gks_erp_app_mobile.erp_app_mobile_sortorder;";
              $result_select = $db_link->query($sql);        
              if (!$result_select) {
                debug_mail(false,'error sql',$sql);
                die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
              }
              while ($row_select = $result_select->fetch_assoc()) {
                $ecv='gks_erp_app_mobile:'.$row_select['id_erp_app_mobile'];
                echo '<option value="'.$ecv.'" '.
                'data-provider="gks_erp_app_mobile" '.
                'data-sender="'.$row_select['id_erp_app_mobile'].'" ';
                $is_offline='';$is_selected='';
                if (empty($row_select['mydate'])==false and strtotime($row_select['mydate']) >= (time() - 60*60)) { //mia ora, to elaxisto einai 15 lepta
                  $is_offline='';$is_selected=($exv==$ecv ? 'selected ' : '');
                } else {
                  $is_offline='disabled';
                }            
                echo $is_offline.$is_selected.'>App: '.$row_select['erp_app_mobile_name'].' '.$row_select['erp_app_mobile_phonenumber'];
                if ($is_offline!='') echo ' - '.gks_lang('ανενεργό');
                echo '</option>';
              }
              $parts=explode(',',$GKS_SMS_SENDER);
              foreach ($parts as $value) {
                $value=trim_gks($value);
                if ($value!='') {
                  $ecv='smsapi:'.$value;
                  echo '<option value="'.$ecv.'" '.
                  ($exv==$ecv ? 'selected ' : '').
                  'data-provider="smsapi" '.
                  'data-sender="'.$value.'" '.
                  '>smsapi: '.$value.'</option>';
                }
              }
              ?>                
                

              </select>
            </div>
          </div>
          <div class="form-group row sendby_viber">
            <label for="from_viber" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Από viber');?>:</label>
            <div class="col-sm-8">
              <span class="gks_flock form-control-plaintext form-control-sm">
                <?php echo $GKS_VIBER_URI;?>
              </span>
            </div>
          </div>
          <div class="form-group row sendby_email">
            <label for="send_with_email_from" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Από email');?>:</label>
            <div class="col-sm-8">
              <input id="send_with_email_from" type="text" value="<?php 
              if (!empty($e['email_from'])) echo $e['email_from']; 
              else echo $GKS_SITE_EMAIL;
              ?>" class="form-control form-control-sm myneedsave" autocomplete="<?php echo $autocomplete_gks_disable;?>">   
            </div>
          </div>
          
          <div class="form-group row sendby_email">
            <label for="send_with_email_subject" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Θέμα email');?>:</label>
            <div class="col-sm-8">
              <input id="send_with_email_subject" type="text" value="<?php
              if (!empty($e['email_subject'])) echo $e['email_subject']; 
              ?>" class="form-control form-control-sm myneedsave">   
            </div>
          </div>
          <div class="form-group row sendby_email">
            <label for="send_with_email_template" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πρότυπο email');?>:</label>
            <div class="col-sm-8">
              <select id="send_with_email_template" class="form-control form-control-sm myneedsave">
              <?php
              $mydef='default';
              if (!empty($e['email_template'])) $mydef=trim_gks($e['email_template']);
              $sql_emailt="select id_email_template as id,email_template_descr as descr from gks_email_template where is_disable=0 order by sortorder";
              $result_emailt = $db_link->query($sql_emailt); 
              if (!$result_emailt) {debug_mail(false,'error sql',$sql_emailt);die('sql error');} 
              $mytemplates=array();
              while ($row_emailt = $result_emailt->fetch_assoc()) {
                $mytemplates[]=$row_emailt;
              }
        
              
              
              foreach ($mytemplates as $onlyname) { 
                echo '<option value="'.$onlyname['id'].'" '.
                ($onlyname['descr']==$mydef ? 'selected': '' ).
                '>'.$onlyname['descr'].'</option>';
              } ?>
              </select>
            </div>
          </div>                    
          <div class="form-group row">
            <label for="message" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κείμενο');?>:</label>
            <div class="col-sm-8">
              <textarea name="message" id="message" class="form-control form-control-sm myneedsave" style="min-height: 100px;"><?php
                if (!empty($e['mymessage'])) echo $e['mymessage'];
                ?></textarea>
              <small class="form-text text-muted" id="chars"></small>
            </div>
          </div>
          <div class="form-group row sendby_viber">
            <div class="col-sm-4 col-form-label form-control-sm text-sm-right">
              <label for="viberbuttons" style="display: block;">
              <?php echo gks_lang('Κουμπιά viber');?>:</label>
              <select id="viberbuttons_samples" style="width:64px;display: inline-block;" class="form-control form-control-sm myneedsave">
                <option value="0"><?php echo gks_lang('π.χ.');?></option>
                <option value="1"><?php echo gks_lang('Ναι/Όχι');?></option>
                <option value="2"><?php echo gks_lang('Ναι/Όχι/Ίσως');?></option>
                <option value="3"><?php echo gks_lang('Με ενδιαφέρει/Δεν με ενδιαφέρει');?></option>
                <option value="4"><?php echo gks_lang('Με ενδιαφέρει/Δεν με ενδιαφέρει/Δεν ξέρω');?></option>
                <option value="5"><?php echo gks_lang('1 ώρα/1 ημέρα/1 εβδομάδα');?></option>
                <option value="6"><?php echo gks_lang('με χρώμα');?></option>
              </select>  
              
            
            </div>
            <div class="col-sm-8">
              <textarea id="viberbuttons" class="form-control form-control-sm myneedsave" style="min-height: 100px;"><?php
                if (!empty($e['mybuttons'])) {
                  $mybuttons=json_decode($e['mybuttons'],true);
                  $ecv=[];
                  foreach ($mybuttons as $value) {
                     $parts=[];
                     if (isset($value['desc'])) $parts[]=$value['desc'];
                     if (isset($value['colorb'])) $parts[]=$value['colorb'];
                     if (isset($value['colorf'])) $parts[]=$value['colorf'];
                     if (count($parts)>=1) $ecv[]=implode('|',$parts);
                  } 
                  if (count($ecv)>=1) echo implode("\r\n",$ecv);
                  //print_r($mybuttons);
                }
                ?></textarea>
              <small class="form-text text-muted">
                <?php echo gks_lang('Ένα κουμπί ανά γραμμή');?><br>
                <?php echo gks_lang('Η κάθε γραμμή μπορεί να έχει από 1 έως 3 παραμέτρους που διαχωρίζονται από το σύμβολο');?> <b style="background-color:#eeeeee;padding:4px;">|</b><br>
                <?php echo gks_lang('Η 1η είναι το κείμενο');?>,<br>
                <?php echo gks_lang('η 2η είναι το χρώμα φόντου');?>,<br>
                <?php echo gks_lang('η 3η είναι το χρώμα κειμένου');?><br>
                <?php echo gks_lang('π.χ. <b>Ναι|#006744|#ffffff</b>');?><br>
                <a href="https://www.w3schools.com/colors/colors_picker.asp" target="_blank">HTML Color Picker</a>            
              </small>
            </div>
          </div>  

          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Δοκιμαστική αποστολή');?>:</label>
            <div class="col-sm-8">
              <?php
              $sql_user="select ID, gks_nickname,user_email,gks_mobile,viber_id,viber_subscribed 
              from ".GKS_WP_TABLE_PREFIX."users 
              where ID=".$my_wp_user_id;
              $result_user = $db_link->query($sql_user);        
              if (!$result_user) {debug_mail(false,'error sql',$sql_user);die('sql error');}
              $tag_sms='';
              $tag_viber='';
              $tag_email='';
              if ($result_user->num_rows==1) {
                $row_user = $result_user->fetch_assoc();
                $gks_nickname=trim_gks($row_user['gks_nickname']);
                if ($gks_nickname=='') $gks_nickname='id'.$my_wp_user_id;
                $user_email=trim_gks($row_user['user_email']);
                $gks_mobile=trim_gks($row_user['gks_mobile']);
                $viber_id=trim_gks($row_user['viber_id']);
                $viber_subscribed=intval($row_user['viber_subscribed']);
                if ($gks_mobile!='') $tag_sms=$gks_mobile.' | '.$gks_nickname.' | (#'.$my_wp_user_id.')';
                if ($user_email!='') $tag_email=$user_email.' | '.$gks_nickname.' | (#'.$my_wp_user_id.')';
                if ($viber_id!='' and $viber_subscribed!=0) $tag_viber=$gks_nickname.' | (#'.$my_wp_user_id.')';
              }
              
              ?>
              <div id="test_send">
                <div><?php echo gks_lang('Επιλέξτε τους αποδέκτες δοκιμαστικής αποστολής');?></div>
                <div class="my2side sendby_sms">
                  <label for="test_send_sms"  ><?php echo gks_lang('SMS σε');?></label>
                  <div>
                    <input id="test_send_sms"   type="text" value="<?php echo $tag_sms;?>" class="form-control form-control-sm myneedsave tooltipster" title="<?php echo gks_lang('Εισάγετε πολλούς αποδέκτες με κόμμα ανάμεσά τους π.χ. 6911111111,6922222222');?>">
                  </div>
                </div>
                <div class="my2side sendby_viber">
                  <label for="test_send_viber"><?php echo gks_lang('Viber σε');?></label>
                  <div>
                    <input id="test_send_viber" type="text" value="<?php echo $tag_viber;?>" class="form-control form-control-sm myneedsave tooltipster" title="<?php echo gks_lang('Ένας αποδέκτης');?>">
                  </div>
                </div>
                <div class="my2side sendby_email">
                  <label for="test_send_email"><?php echo gks_lang('email σε');?></label>
                  <div>
                    <input id="test_send_email" type="text" value="<?php echo $tag_email;?>" class="form-control form-control-sm myneedsave tooltipster" title="<?php echo gks_lang('Εισάγετε πολλούς αποδέκτες με κόμμα ανάμεσά τους π.χ. kostas@gks.gr,goutoudis@gmail.com');?>">
                  </div>
                </div>
                
              </div>
              <button type="button" class="btn btn-primary" id="mybutton_test"><?php echo gks_lang('Δοκιμαστική Αποστολή');?></button>
              
              
            </div>
          </div>
          
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Παραγματική αποστολή');?>:</label>
            <div class="col-sm-8">
              <button type="button" class="btn btn-primary" id="mybutton" disabled><?php echo gks_lang('Παραγματική Αποστολή');?></button>
              <small class="form-text text-muted" id="chars"><?php echo gks_lang('Θα πρέπει να γίνει τουλάχιστον μία δοκιμαστική αποστολή');?></small>
            </div>
          </div>
                            
          

        </div>
      </div>
    </div>
  </div>
</div>


<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-lg-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Αποτελέσματα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('results');?>>
          <div id="send_exec_result_table">
            <div id="send_exec_result">
              <?php echo gks_lang('Εδώ θα εμφανιστούν τα αποτελέσματα');?>...
            </div>
          </div>


        </div>
      </div>
    </div>
  </div>
</div>

<?php include_once('_dialogs.php');?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var timestamp = new Date().getTime();


jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>


});    
  
</script>
  
<script src='/my/js/tinymce/tinymce.min.js'></script>
  
<script src="js/admin-mass-messages-new.js?v=<?php echo $gks_cache_version;?>"></script>


<?php
//db_close();
include_once('_my_footer_admin.php');

