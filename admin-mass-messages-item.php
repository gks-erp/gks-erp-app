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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_mass_messages',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}







if ($id<=0) {
  if (isset($_POST['id'])) $id=intval($_POST['id']);
}
if ($id< 1) {header('Location: /my'); die(); }







$sql = "SELECT gks_mass_messages.*, 
wp_users_add.gks_nickname as gks_nickname_add,
wp_users_edit.gks_nickname as gks_nickname_edit,
gks_email_template.email_template_descr
FROM ((gks_mass_messages 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as wp_users_add  ON gks_mass_messages.user_id_add = wp_users_add.ID)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as wp_users_edit ON gks_mass_messages.user_id_edit = wp_users_edit.ID)
LEFT JOIN gks_email_template ON gks_mass_messages.email_template_id = gks_email_template.id_email_template
where id_mass_message=".$id;
$result = $db_link->query($sql);        
if (!$result) debug_mail(false,'error sql',$sql);
if (!$result) die('sql error');
if ($result->num_rows!=1) die('record not found'); 
$row = $result->fetch_assoc();
$my_page_title=gks_lang('Μαζική Αποστολή SMS-Viber-email').': '.$id;


$mylist=json_decode($row['mylist'],true);
$myresult=json_decode($row['myresult'],true);
$mybuttons=json_decode($row['mybuttons'],true);

if (is_array($mylist)==false) $mylist=[];
if (is_array($myresult)==false) $myresult=[];
if (is_array($mybuttons)==false) $mybuttons=[];

foreach ($mybuttons as &$mybutton_item) {
  $mybutton_item['res_count']=0;
}
unset($mybutton_item);


//echo '<pre>';print_r($mylist);die();
//echo '<pre>';print_r($myresult);die();
//echo '<pre>';print_r($mybuttons);die();
/*
        (
            [id] => 1
            [email] => goutoudis@gmail.com
            [mobile] => 6971881406
            [v_se] => sKKkyjZhCQyvvSAm+gZQJQ==
            [v_su] => 1
            [with] => viber
            [res] => 1
            vmt viber message_token
        )
*/

foreach ($myresult as &$value) {
  $value['gks_nickname']='';
}
unset($value);


if (count($mylist)>0) {
  $sql_d="select ID, gks_nickname
  from ".GKS_WP_TABLE_PREFIX."users
  where ID in (".implode(',',$mylist).")";
  $result_d = $db_link->query($sql_d);        
  if (!$result_d) debug_mail(false,'error sql',$sql_d);
  if (!$result_d) die('sql error');
  while ($row_d = $result_d->fetch_assoc()) {
    foreach ($myresult as &$value) {
      if ($value['id']==$row_d['ID']) {
        $value['gks_nickname']=$row_d['gks_nickname'];
        break; 
      }
    }
    unset($value);    
  }
}

if ($row['cc_viber']>0) {
  $vmt=[];
  foreach ($myresult as $value) {
    if ($value['with']=='viber' and isset($value['vmt'])) {
      $vmt[]="'".$db_link->escape_string($value['vmt'])."'";
    }
  }
  //echo '<pre>';print_r($vmt);die();
  if (count($vmt)>0) {
    $sql_d="SELECT id_viber_msgs as rec_id,mydate,message_token,delivered,seen
    FROM gks_viber_msgs
    WHERE message_token in (".implode(',',$vmt).")";
    $result_d = $db_link->query($sql_d);        
    if (!$result_d) debug_mail(false,'error sql',$sql_d);
    if (!$result_d) die('sql error');
    while ($row_d = $result_d->fetch_assoc()) {
      foreach ($myresult as &$value) {
        if ($value['with']=='viber' and $value['vmt']==$row_d['message_token']) {
          $value['d_viber']=$row_d;
          break; 
        }
      }
      unset($value);    
    }
    
    //viber_id sender_id receiver_id
    $sql_d="SELECT mydate,message,sender_id,user_id,action_cmd_part3
    FROM gks_viber_msgs
    WHERE action_cmd_part1='massmessage'
    and action_cmd_part2='".$id."'";
    $result_d = $db_link->query($sql_d);        
    if (!$result_d) debug_mail(false,'error sql',$sql_d);
    if (!$result_d) die('sql error');
    while ($row_d = $result_d->fetch_assoc()) {
      foreach ($myresult as &$value) {
        if ($value['with']=='viber' and $value['v_se']==$row_d['sender_id']) {
          $value['d_viber_response']=$row_d;
          break; 
        }
      }
      unset($value);    
    }
    //print '<pre>';print_r($myresult);die();
    
  }
}

if ($row['cc_sms']>0) {
  $sql_d="SELECT id as rec_id,myfrom,myto,Message,date_add,donedate,donedate_date,
  sms_result,myret,status,status_name,
  model,model_id
  FROM gks_sms
  WHERE model='mass' 
  AND model_id=".$id;
  //echo '<pre>'.$sql_d;die();
  
  $result_d = $db_link->query($sql_d);        
  if (!$result_d) debug_mail(false,'error sql',$sql_d);
  if (!$result_d) die('sql error');
  while ($row_d = $result_d->fetch_assoc()) {
    foreach ($myresult as &$value) {
      if ($value['with']=='sms' and endwith($row_d['myto'],$value['mobile'])) {
        $value['d_sms']=$row_d;
        break; 
      }
    }
    unset($value);    
  }
}
//echo '<pre>dddd';die();
if ($row['cc_email']>0) {
  $sql_d="SELECT id as rec_id, myfrom, myto, date_add,date_view,views_ips, views_count, myret
  FROM gks_email
  WHERE model='mass' 
  AND model_id=".$id;
  $result_d = $db_link->query($sql_d);        
  if (!$result_d) debug_mail(false,'error sql',$sql_d);
  if (!$result_d) die('sql error');
  while ($row_d = $result_d->fetch_assoc()) {
    foreach ($myresult as &$value) {
      if ($value['with']=='email' and $value['email']==$row_d['myto']) {
        $value['d_email']=$row_d;
        break; 
      }
    }
    unset($value);    
  }
}


foreach ($myresult as $val) {
  if (isset($val['d_viber_response']['action_cmd_part3'])) {
    $this_desc=$val['d_viber_response']['action_cmd_part3'];
    if (count($mybuttons)>0) {
      foreach ($mybuttons as &$mybutton_item) {
        if ($mybutton_item['desc']==$this_desc) {
          $mybutton_item['res_count']++;
          break;  
        }
      }
      unset($mybutton_item);
    }
  }  
}
//echo '<pre>';print_r($mybuttons);die();

//echo '<pre>';print_r($myresult);die();


$send_stats=array(
  'viber'=>array(),
  'sms'=>array(
    'all' => 0,
    'send' =>0,
    'delivered'=>0,
    'status' => array(),
  ),
  'email'=>array(),
  'none'=>0,
);
foreach ($myresult as $val) {
  if ($val['with']=='sms') {
    $send_stats['sms']['all']++;
    if (isset($val['d_sms']['status'])) $send_stats['sms']['send']++;
    if (isset($val['d_sms']['status']) and in_array($val['d_sms']['status'],[404])) {
      $send_stats['sms']['delivered']++;
    } else if (isset($val['d_sms']['status'])) {
      $kkk=$val['d_sms']['status'].'-'.$val['d_sms']['status_name'];
      if (isset($send_stats['sms']['status'][$kkk])==false) {
        $send_stats['sms']['status'][$kkk]=array(
          'status' => $val['d_sms']['status'],
          'status_name' => $val['d_sms']['status_name'],
          'count' => 0, 
        );
      }
      $send_stats['sms']['status'][$kkk]['count']++;
    }
  } 
} 


stat_record();

$nav_active_array=array('crm','manage_sms','manage_mass_messages');


include_once('_my_header_admin.php');
?>
<style>
.mass_message_rows .form-group  {
  margin-bottom: 0px;
}
#table_results {
  width:100%;  
}

</style>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      
      <h3><?php echo gks_lang('Μαζική Αποστολή SMS-Viber-email');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span></h3>
      
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Βασικά στοιχεία');?>
        </div>
        <div class="card-body mass_message_rows" <?php echo gks_card_body('bas');?>> 
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right">
              <?php echo gks_lang('Ενεργό');?> <img src="img/viber.png" style="width:24px;vertical-align: middle;">:
            </label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm">
              <img src="img/<?php echo $row['send_with_viber'];?>.png" border="0" width="16">
            </span></div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right">
              <?php echo gks_lang('Ενεργό');?> <img src="img/sms.png" style="width:24px;vertical-align: middle;">:
            </label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm">
              <img src="img/<?php echo $row['send_with_sms'];?>.png" border="0" width="16">
            </span></div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right">
              <?php echo gks_lang('Ενεργό');?> <img src="img/email2.png" style="width:24px;vertical-align: middle;">:
            </label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm">
              <img src="img/<?php echo $row['send_with_email'];?>.png" border="0" width="16">
            </span></div>
          </div>
          
          
                    
          
          
                  
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Από SMS');?>:</label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm">
              <?php 
              if ($row['sender_sms_provider']=='gks_erp_app_mobile') {
                echo 'gks ERP App Mobile';
                $sql_app_mobile = "SELECT id_erp_app_mobile,erp_app_mobile_name from gks_erp_app_mobile where id_erp_app_mobile=".intval($row['sender_sms_sender']);
                $result_app_mobile = $db_link->query($sql_app_mobile);        
                if (!$result_app_mobile) {debug_mail(false,'error sql',$sql_app_mobile);die('sql error');}
                if ($result_app_mobile->num_rows==1) {
                  $row_app_mobile = $result_app_mobile->fetch_assoc();                
                  echo '<br><a href="admin-erp-app-mobile-item.php?id='.$row_app_mobile['id_erp_app_mobile'].'">'.$row_app_mobile['erp_app_mobile_name'].'</a>';
                }
              } else if ($row['sender_sms_provider']=='smsapi') {
                echo 'SMSAPI<br>'.$row['sender_sms_sender'];
              } else {
                echo $row['sender_sms_provider'].' '.$row['sender_sms_sender'];
              }
              

              
              ?>
            </span></div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Από Viber');?>:</label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm">
              <?php echo $row['viber_from'];?>
            </span></div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Από email');?>:</label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm">
              <?php echo $row['email_from'];?>
            </span></div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right">
              <?php echo gks_lang('Θέμα email');?>:
            </label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm">
              <?php echo $row['email_subject'];?>
            </span></div>
          </div>           
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right">
              <?php echo gks_lang('Πρότυπο email');?>:
            </label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm">
              <?php echo $row['email_template_descr'];?>
            </span></div>
          </div>
          
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right">
              <?php echo gks_lang('Μήνυμα');?>:
            </label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm">
              <?php echo nl2br($row['mymessage']);?>
            </span></div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right">
              <?php echo gks_lang('Κουμπιά viber');?> <img src="img/viber.png" style="width:24px;vertical-align: middle;">:
            </label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm">
            <?php
            $tttt=[];
            foreach ($mybuttons as $value) {
               $tttt[]= '<div style="margin:10px 4px;"><span style="padding:6px 10px;border-radius: 6px;display: inline-block;'.
              ($value['colorb']=='' ? '' : 'background-color:'.$value['colorb'].';').
              ($value['colorf']=='' ? '' : 'color:'.$value['colorf'].';').
              '">'.
              $value['desc'].
              '</span> ('.$value['res_count'].')</div>';
            } 
            echo implode('',$tttt);
            ?>
            </span></div>
          </div>
         
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right">
              <?php echo gks_lang('Όλοι οι Αποδέκτες');?>:
            </label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm">
              <?php echo $row['cc_all'];?>
            </span></div>
          </div> 
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right">
              <?php echo gks_lang('Αποδέκτες');?> <img src="img/viber.png" style="width:24px;vertical-align: middle;">:
            </label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm">
              <?php echo $row['cc_viber'];?>
            </span></div>
          </div>         
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right">
              <?php echo gks_lang('Αποδέκτες');?> <img src="img/sms.png" style="width:24px;vertical-align: middle;">:
            </label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm">
              <?php echo $row['cc_sms'];?>
            </span></div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right">
              <?php echo gks_lang('Αποδέκτες');?> <img src="img/email2.png" style="width:24px;vertical-align: middle;">:
            </label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm">
              <?php echo $row['cc_email'];?>
            </span></div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right">
              <?php echo gks_lang('Αποδέκτες τίποτα');?>:
            </label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm">
              <?php echo $row['cc_none'];?>
            </span></div>
          </div>

          <div style="text-align:center;">
            <a class="btn btn-primary gks_add_new_record" href="admin-mass-messages-new.php?template_id=<?php echo $id;?>"><?php echo gks_lang('Δημιουργία αντιγράφου');?></a>  
          </div>
 
          
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kata_log');?>>      
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm"><?php if ($row['id_mass_message']>0) echo $row['id_mass_message'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm"><?php if ($row['user_id_add']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm"><?php if (isset($row['mydate_add']))echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm"><?php if ($row['user_id_edit']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία στις');?>:</label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm"><?php if (isset($row['mydate_edit'])) echo showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αποστολή από');?>:</label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm"><?php if ($row['user_id_add']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αποστολή στις');?>:</label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm"><?php if (isset($row['date_send_start']))  echo showDate(strtotime($row['date_send_start']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τέλος Αποστολής');?>:</label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm"><?php if (isset($row['date_send_end']))  echo showDate(strtotime($row['date_send_end']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('IP');?>:</label>
            <div class="col-sm-8"><span class="gks_flock form-control-plaintext form-control-sm"><a href="admin-stat-ip.php?ip=<?php echo $row['myip'];?>"><?php echo $row['myip'];?></a></span></div>
          </div>
        </div>
      </div>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Στατιστικά');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('sentstats');?>>      
          <div class="row">
            <div class="col-sm-12 text-sm-center"><img src="img/sms.png" style="width:24px;vertical-align: middle;"></div>
          </div>            
          <div class="row">
            <div class="col-sm-6 col-form-label form-control-sm text-sm-right gks_flock"><?php echo gks_lang('Όλα');?></div>
            <div class="col-sm-6 col-form-label form-control-sm text-sm-left  gks_flock"><?php echo $send_stats['sms']['all'];?></span></div>
          </div>
          <div class="row">
            <div class="col-sm-6 col-form-label form-control-sm text-sm-right gks_flock"><?php echo gks_lang('Στάλθηκαν');?></div>
            <div class="col-sm-6 col-form-label form-control-sm text-sm-left  gks_flock"><?php echo $send_stats['sms']['send'];?></span></div>
          </div>
          <div class="row">
            <div class="col-sm-6 col-form-label form-control-sm text-sm-right gks_flock"><?php echo gks_lang('Παραδόθηκαν');?></div>
            <div class="col-sm-6 col-form-label form-control-sm text-sm-left  gks_flock"><?php echo $send_stats['sms']['delivered'];?></span></div>
          </div>
          <?php foreach ($send_stats['sms']['status'] as $cc_status) {?>
          <div class="row">
            <div class="col-sm-6 col-form-label form-control-sm text-sm-right gks_flock"><span class="sms_status sms_status_<?php echo $cc_status['status'];?>"><?php echo $cc_status['status_name'];?></span></div>
            <div class="col-sm-6 col-form-label form-control-sm text-sm-left  gks_flock"><?php echo $cc_status['count'];?></span></div>
          </div>                 
          <?php } ?>

          
          
          <?php 
          //echo '<pre>';print_r($send_stats['sms']);echo '</pre>';
          ?>
             

        </div>
      </div>
          
          
      
    </div>
  </div>
</div>

<?php
$tablehtml='
<table class="table table-sm table-responsive1 table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center" id="table_results">
<thead>
  <tr>
    <th nowrap class="table-dark" scope="col" style="text-align:center !important;width:0%">'.gks_lang('Α/Α').'</th>     
    <th class="table-dark" scope="col" style="text-align:left   !important;width:30%">'.gks_lang('Χρήστες').'</th>     
    <th class="table-dark" scope="col" style="text-align:center !important;width:10%">'.gks_lang('Τρόπος').'</th>     
    <th class="table-dark" scope="col" style="text-align:left   !important;width:10%">'.gks_lang('Κινητό').'</th>     
    <th class="table-dark" scope="col" style="text-align:left   !important;width:10%">'.gks_lang('email').'</th>     
    <th class="table-dark" scope="col" style="text-align:center !important;width:10%">'.gks_lang('Στάλθηκε').'</th>     
    <th class="table-dark" scope="col" style="text-align:center !important;width:10%">'.gks_lang('Παραδόθηκε').'</th>     
    <th class="table-dark" scope="col" style="text-align:center !important;width:10%">'.gks_lang('Προβλήθηκε').'</th>     
    <th class="table-dark" scope="col" style="text-align:center !important;width:10%">'.gks_lang('Απάντηση').'</th>     
    <th class="table-dark" scope="col" style="text-align:center !important;width:10%">'.gks_lang('Λεπτομέρειες').'</th>     
    <th class="table-dark" scope="col" style="text-align:center !important;width:0%">'.gks_lang('Ενέργεια').'</th>     
  <tr>
<thead>
<tbody>';




$i=0;
foreach ($myresult as $val) {
  $i++;
  
  $tablehtml.='  
  <tr class="'.($i % 2 == 0 ? 'even' : 'odd').'">
    <th scope="row" nowrap="" class="mytdcm aa">'.$i.'</td>     
    <td nowrap class="mytdcml"><a href="admin-users-item.php?id='.$val['id'].'">'.$val['gks_nickname'].'</a></td>     
    <td nowrap class="mytdcm">';
    if ($val['with']=='viber') $tablehtml.='<img src="img/viber.png" style="width:24px;">';
    else if ($val['with']=='sms') $tablehtml.='<img src="img/sms.png" style="width:24px;">';
    else if ($val['with']=='email') $tablehtml.='<img src="img/email2.png" style="width:24px;">';
    
  $tablehtml.='  
    </td>
    <td nowrap class="mytdcml">';
    if ($val['with']=='sms' and isset($val['d_sms']['rec_id'])) {
  $tablehtml.=  
      '<i class="fas fa-sms gks_sms_view" data-id="'.$val['d_sms']['rec_id'].'"></i> ';
    }    
  $tablehtml.=  
    $val['mobile'].'</td>     
    <td nowrap class="mytdcml">';
    //$tablehtml.= '<pre>'.print_r($val,true).'</pre>';
    
    if ($val['with']=='email' and isset($val['d_email']['rec_id'])) {
  $tablehtml.=  
      '<i class="fas fa-envelope gks_email_view" data-id="'.$val['d_email']['rec_id'].'"></i> ';
    }
    
  $tablehtml.=  
    $val['email'].'</td>     
    <td nowrap class="mytdcm">';
    
    if (isset($val['d_viber'])) {
      $imgtitle=showDate(strtotime($val['d_viber']['mydate']),'d/m/Y H:i:s',1);
      $tablehtml.='<img title="'.$imgtitle.'"src="img/'.$val['res'].'.png" border="0" width="16">';
    }
    if (isset($val['d_sms']['status'])) {
      $imgtitle=showDate(strtotime($val['d_sms']['date_add']),'d/m/Y H:i:s',1);
      $tablehtml.='<img title="'.$imgtitle.'"src="img/1.png" border="0" width="16">';
    }
    if (isset($val['d_email'])) {
      $imgtitle=showDate(strtotime($val['d_email']['date_add']),'d/m/Y H:i:s',1);
      $tablehtml.='<img title="'.$imgtitle.'"src="img/'.$val['d_email']['myret'].'.png" border="0" width="16">';
    }
    
  $tablehtml.='
    </td>
    <td nowrap class="mytdcm">';
    if (isset($val['d_viber']['delivered'])) {
      $imgtitle=showDate(strtotime($val['d_viber']['delivered']),'d/m/Y H:i:s',1);
      $tablehtml.='<img title="'.$imgtitle.'"src="img/1.png" border="0" width="16">';
    }
    if (isset($val['d_sms']['status']) and in_array($val['d_sms']['status'],[404])) {
      $imgtitle=showDate(strtotime($val['d_sms']['donedate_date']),'d/m/Y H:i:s',1);
      $tablehtml.='<img title="'.$imgtitle.'"src="img/1.png" border="0" width="16">';
    }        

  $tablehtml.='
    </td>
    <td nowrap class="mytdcm">';
    if (isset($val['d_viber']['seen'])) {
      $imgtitle=showDate(strtotime($val['d_viber']['seen']),'d/m/Y H:i:s',1);
      $tablehtml.='<img title="'.$imgtitle.'" src="img/1.png" border="0" width="16">';
    }

    
    if (isset($val['d_email'])) {
      if ($val['d_email']['views_count']>0) {
        $imgtitle=showDate(strtotime($val['d_email']['date_view']),'d/m/Y H:i:s',1);
        $tablehtml.='<img title="'.$imgtitle.'" src="img/1.png" border="0" width="16">';
        if ($val['d_email']['views_count']>1) {
          $tablehtml.='x'.$val['d_email']['views_count'];
        }
      }
    }
    

    


  $tablehtml.='
    </td>    
    <td nowrap class="mytdcm">';
    if (isset($val['d_viber_response']['action_cmd_part3'])) {
      $this_desc=$val['d_viber_response']['action_cmd_part3'];
      
      $colorb='';$colorf='';
      if (count($mybuttons)>0) {
        foreach ($mybuttons as $value) {
          if ($value['desc']==$this_desc) {
            $colorb=$value['colorb'];
            $colorf=$value['colorf'];
            break;  
          }
        }
      }
      
      $tablehtml.='<span style="padding:6px 10px;border-radius: 6px;'.
      ($colorb=='' ? '' : 'background-color:'.$colorb.';').
      ($colorf=='' ? '' : 'color:'.$colorf.';').
      '">'.
      $val['d_viber_response']['action_cmd_part3'].
      '</span>';
      
    }
  $tablehtml.='
    </td>
    <td nowrap class="mytdcm">';
    if (isset($val['d_email']['views_ips'])) {
      $tablehtml.=nl2br($val['d_email']['views_ips']);
    }
    if (isset($val['d_sms']) and in_array($val['d_sms']['status'],[403,404])==false) {
      $tablehtml.='<span class="sms_status sms_status_'.$val['d_sms']['status'].'">'.$val['d_sms']['status_name'].'</span>';
    }
  $tablehtml.='
    </td>
    <td nowrap class="mytdcm">';
  if (isset($val['d_sms']) and gks_sms_can_resend_status($val['d_sms']['status'],$val['d_sms']['model'])) {
    $tablehtml.= '<i class="gks_sms_command_resend fas fa-sync-alt tooltipster" title="'.gks_lang('Επαναποστολή').'" data-id="'.$val['d_sms']['rec_id'].'"></i>';  
  }
  $tablehtml.='
    </td>
  <tr> ';



} 
$tablehtml.='</tbody></table>';

//echo '<pre>';print_r($myresult);echo '</pre>';
?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Αποδέκτες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('results');?>>   
          <?php echo $tablehtml;?>
        </div>
      </div>
    </div>
  </div>
</div>




<?php include_once('_dialogs.php'); ?>
<script src='/my/js/tinymce/tinymce.min.js'></script>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>


jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
  $('.gks_sms_command_resend').click(gks_sms_command_resend_click);  
    
});
</script>


<?php
//db_close();
include_once('_my_footer_admin.php');


