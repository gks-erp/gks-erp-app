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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_erp_app',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}







if ($id<=0) {
  if (isset($_POST['id'])) $id=intval($_POST['id']);
}
if ($id == 0 or $id < -1) {header('Location: /my'); die(); }



$gks_custom_prepare = gks_custom_table_item_prepare('gks_erp_app',['from'=>'item']);


if ($id==-1) {



  $row=array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_erp_app']=-1;
  $row['erp_app_name']='';
  $row['erp_app_descr']='';
  $row['erp_app_token']='';
  $row['erp_app_secret']='';
  $row['erp_app_url']='frp';
  $row['erp_app_port']=55555;
  $row['erp_app_sortorder']=1000;
  $row['erp_app_disabled']=0;
  
  $row['erp_app_url2ip']='';
  $row['erp_app_lan_ip']='';
  $row['erp_app_wan_ip']='';
  $row['appver']='';
  $row['pctime']='';
  $row['pcusername']='';
  $row['pcname']='';
  $row['winver']='';
  $row['hdwd']=0;
  $row['screw']=0;
  $row['screh']=0;
  $row['mac']='';
  $row['erp_app_local_printers']='';
  $row['voip_localdb']='';
  $row['voip_ip']='';
  $row['voip_AIM_port']=0;
  $row['voip_AIM_username']='';
  $row['voip_AIM_password']='';
  $row['voip_call_originate']=0;
  $row['voip_call_monitoring']=0;
  
  $my_page_title=gks_lang('Νέο gks ERP App Desktop');
} else {
  $sql ="SELECT gks_erp_app.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit,
  gks_erp_app_ping.pctime, gks_erp_app_ping.pcusername, gks_erp_app_ping.pcname, gks_erp_app_ping.winver, gks_erp_app_ping.appver, 
  gks_erp_app_ping.hdwd, gks_erp_app_ping.screw, gks_erp_app_ping.screh, gks_erp_app_ping.mac
  FROM ((gks_erp_app 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_erp_app.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_erp_app.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN gks_erp_app_ping ON gks_erp_app.last_ping_id = gks_erp_app_ping.id


  where id_erp_app = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('gks ERP App Desktop').': '.$row['erp_app_name'];
  $object_title=$row['erp_app_name'];
}
$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);


stat_record();

$nav_active_array=array('manage','manage_erp_app');


include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('gks ERP App Desktop');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('gks ERP App Desktop');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
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
            <label for="erp_app_name" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα');?>:</label>
            <div class="col-sm-8">
              <input id="erp_app_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['erp_app_name']);?>">
            </div>
          </div>

          <div class="form-group row">
            <label for="erp_app_descr" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-sm-8">
              <textarea id="erp_app_descr" type="text" class="form-control form-control-sm myneedsave" style="min-height:100px;height:100px;" ><?php echo htmlspecialchars_gks($row['erp_app_descr']);?></textarea>
            </div>
          </div>
          <div class="form-group row">
            <label for="erp_app_token" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Δημόσιο Κλειδί');?>:</label>
            <div class="col-sm-8">
              <input id="erp_app_token" type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars_gks($row['erp_app_token']);?>" readonly>
              <?php if ($id>0){?>
              <small>
                <input type="checkbox" id="erp_app_token_new" val="1" style="vertical-align: middle;"> 
                <label for="erp_app_token_new" style="margin: 0px;"><?php echo gks_lang('Δημιουργία νέου κλειδιού');?></label>
              </small>
              <?php } ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="erp_app_secret" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ιδιωτικό Κλειδί');?>:</label>
            <div class="col-sm-8">
              <input id="erp_app_secret" type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars_gks($row['erp_app_secret']);?>">
              
              <small>
                <?php echo gks_lang('Θα πρέπει να έχει μήκος τουλάχιστον 32 χαρακτήρες');?><br>
                <span id="erp_app_secret_new" class="btn btn-primary btn-sm"><?php echo gks_lang('Δημιουργία τυχαίου');?></span>
              </small>
              
            </div>
          </div> 
                    
          <div class="form-group row">
            <label for="erp_app_url" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Url');?>:</label>
            <div class="col-sm-8">
              <input id="erp_app_url" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['erp_app_url']);?>" placeholder="<?php echo gks_lang('π.χ.');?> frp">
              <small><i><?php echo gks_lang('frp (μέσω proxy gks) ή IP/Hostname');?></i></small>
            </div>
          </div>
          <div class="form-group row">
            <label for="erp_app_port" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πόρτα');?>:</label>
            <div class="col-sm-8">
              <input id="erp_app_port" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['erp_app_port']);?>" min="10000" max="65000">
              <small><i><?php echo gks_lang('από 10000 έως 65000');?></i></small>
            </div>
          </div>
          <div class="form-group row">
            <label for="erp_app_sortorder" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-sm-8">
              <input id="erp_app_sortorder" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['erp_app_sortorder']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="erp_app_disabled" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργή');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="erp_app_disabled" value="1" <?php if ($row['erp_app_disabled']==0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>

          

        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Τηλεφωνικό κέντρο');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('voip');?>>  
          
          <div class="form-group row">
            <label for="voip_localdb" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('MySQL Connection');?>:</label>
            <div class="col-sm-8">
              <input id="voip_localdb" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['voip_localdb']);?>" placeholder="<?php echo gks_lang('π.χ.');?> server=192.168.1.251; database=gks_voip;port=3306; user=myusername;password=mypassword;charset=utf8;">
              <small><i>
                <?php echo gks_lang('Το κείμενο για την σύνδεση της τοπικής MySQL για τον συγχρονισμό των τηλεφωνικών αριθμών από το gks ERP.');?>
                <?php echo gks_lang('Από αυτήν την βάση θα μπορεί να διαβάζει το τηλεφωνικό κέντρο τις επαφές για την αναγνώριση του καλούντος.');?>
                <?php echo gks_lang('Αυτή η βάση είναι καλό να βρίσκεται στο ίδιο τοπικό δίκτυο με το τηλεφωνικό κέντρο.');?>
                <br>
                <?php echo gks_lang('π.χ.');?>:
               </i></small>

              <pre class="gks_precode">server=192.168.1.251; database=gks_voip;port=3306; user=myusername;password=mypassword;charset=utf8;</pre>
            </div>
          </div>
          <div class="form-group row">
            <label for="voip_ip" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('VoIP Local IP');?>:</label>
            <div class="col-sm-8">
              <input id="voip_ip" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['voip_ip']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 192.168.1.251">
            </div>
          </div>          
          <div class="form-group row">
            <label for="voip_AIM_port" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('VoIP AIM Port');?>:</label>
            <div class="col-sm-8">
              <input id="voip_AIM_port" type="number" class="form-control form-control-sm myneedsave" value="<?php if (intval($row['voip_AIM_port'])!=0) echo $row['voip_AIM_port'];?>" placeholder="<?php echo gks_lang('π.χ.');?> 7777">
            </div>
          </div>          
          <div class="form-group row">
            <label for="voip_AIM_username" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('VoIP AIM Username');?>:</label>
            <div class="col-sm-8">
              <input id="voip_AIM_username" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['voip_AIM_username']);?>" placeholder="<?php echo gks_lang('π.χ.');?> amitest123">
            </div>
          </div>          
          <div class="form-group row">
            <label for="voip_AIM_password" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('VoIP AIM Password');?>:</label>
            <div class="col-sm-8">
              <input id="voip_AIM_password" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['voip_AIM_password']);?>" placeholder="<?php echo gks_lang('π.χ.');?> amitest123pass">
            </div>
          </div>          
          <div class="form-group row">
            <label for="voip_call_originate" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έναρξη κλήσης');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="voip_call_originate" value="1" <?php if ($row['voip_call_originate']==1) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>               
          <div class="form-group row">
            <label for="voip_call_monitoring" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Καταγραφή εισερχόμενης κλήσης');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="voip_call_monitoring" value="1" <?php if ($row['voip_call_monitoring']==1) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>               
               
                    
        </div>
      </div>

    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Δεδομένα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('data');?>>         

          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Public URL');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php 
            if ($id>0) {
              $public_url='';
              if ($row['erp_app_url']=='frp') { 
                if (trim_gks($row['erp_app_token'])!='') {
                  $public_url='http://'.$row['erp_app_token'].GKS_PROXY['DOMAIN_BASE_NAME'].':'.GKS_PROXY['VPORT'];
                }
              } else {
                if (trim_gks($row['erp_app_url'])!='' and $row['erp_app_port']>0) {
                  $public_url='http://'.$row['erp_app_url'].':'.$row['erp_app_port'];
                }
              }
              if ($public_url!='') echo '<a href="'.$public_url.'" target="_blank">'.$public_url.'</a>';
            }
            ?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('LAN IP');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo $row['erp_app_lan_ip'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('WAN IP');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo $row['erp_app_wan_ip'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('URL to IP');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo $row['erp_app_url2ip'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τελευταία σύνδεση');?>:</label>
            <div class="col-sm-8">
              <?php if (isset($row['erp_app_last_ping'])) {
              echo '<span class="form-control-plaintext form-control-sm">';
                echo showDate(strtotime($row['erp_app_last_ping']), 'd/m/Y H:i:s', 1);
              echo '</span>';
              //echo '<br>';
              echo '<span class="form-control-plaintext form-control-sm ' ;
              if (strtotime($row['erp_app_last_ping']) > time()-15*60) echo 'gks_erp_app_alive'; else echo 'gks_erp_app_not_alive';
              echo '">';
                echo secondsago(strtotime($row['erp_app_last_ping']));
              echo '</span>';
              }?>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Έκδοση gks ERP App Desktop');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset"><?php echo $row['appver'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ώρα Η/Υ');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset"><?php echo $row['pctime'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα χρήστη');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset"><?php echo $row['pcusername'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα Η/Υ');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset"><?php echo $row['pcname'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Έκδοση windows');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset"><?php echo $row['winver'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ελεύθερος χώρος στον δίσκο');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset"><?php if ($row['hdwd']>0) echo number_format($row['hdwd']/1024,2,$GKS_NUMBER_FORMAT_DECIMAL,$GKS_NUMBER_FORMAT_THOUSAND).' GB';?> </span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Οθόνη');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset"><?php if ($row['screw']>0) echo $row['screw'].'x'.$row['screh'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Mac Address');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset"><?php 
              if (trim_gks($row['mac'])!='') {
                $parts=explode('|',$row['mac']);
                echo implode('<br>', $parts); 
                
              }
            ?></span></div>
          </div>


          
        </div>
      </div>
          
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Εντολές');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('commands');?>>         

          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Λειτουργεί;');?></label>
            <div class="col-sm-2">
              <i id="run_command_alive" class="run_command fa fa-arrow-circle-right" style="font-size: 150%;line-height: 1.2;color:green;cursor:pointer;"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset" id="run_command_alive_result"></span></div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Ρυθμίσεις');?>:</label>
            <div class="col-sm-2">
              <i id="run_command_settings" class="run_command fa fa-arrow-circle-right" style="font-size: 150%;line-height: 1.2;color:green;cursor:pointer;"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset" id="run_command_settings_result"></span></div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Στατιστικά');?>:</label>
            <div class="col-sm-2">
              <i id="run_command_stats" class="run_command fa fa-arrow-circle-right" style="font-size: 150%;line-height: 1.2;color:green;cursor:pointer;"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset" id="run_command_stats_result"></span></div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Λήψη Δεδομένων');?>:</label>
            <div class="col-sm-2">
              <i id="run_command_getdata" class="run_command fa fa-arrow-circle-right" style="font-size: 150%;line-height: 1.2;color:green;cursor:pointer;"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset" id="run_command_getdata_result"></span></div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Εκτυπωτές');?>:</label>
            <div class="col-sm-2">
              <i id="run_command_local_printers" class="run_command fa fa-arrow-circle-right" style="font-size: 150%;line-height: 1.2;color:green;cursor:pointer;"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset" id="run_command_local_printers_result">
            <?php
            $erp_app_local_printers=trim_gks($row['erp_app_local_printers']);
            if ($erp_app_local_printers!='') {
              $temp=unserialize($erp_app_local_printers);  
              if (is_array($temp) and count($temp)>0) {
                echo '<ol>';
                foreach ($temp as $value) {
                  echo '<li>'.$value.'</li>';
                } 
                echo '</ol>';
              }
            }
            
            ?>
              
            </span></div>
          </div>
          
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('VoIP Local DB Phonebook');?>:</label>
            <div class="col-sm-2">
              <i id="run_command_voiplocaldbphonebook" class="run_command fa fa-arrow-circle-right" style="font-size: 150%;line-height: 1.2;color:green;cursor:pointer;"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset" id="run_command_voiplocaldbphonebook_result"></span></div>
          </div>

          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('VoIP AIM test');?>:</label>
            <div class="col-sm-2">
              <i id="run_command_voipaimtest" class="run_command fa fa-arrow-circle-right" style="font-size: 150%;line-height: 1.2;color:green;cursor:pointer;"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height:unset" id="run_command_voipaimtest_result"></span></div>
          </div>

          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('VoIP Έναρξη κλήσης');?>:</label>
            <div class="col-sm-2">
              <i id="run_command_voipaimoriginatecall" class="run_command fa fa-arrow-circle-right" style="font-size: 150%;line-height: 1.2;color:green;cursor:pointer;"></i>
            </div>
            <div class="col-sm-8">
              <div class="form-group row">
                <label for="voipaimoriginatecall_extension" class="col-sm-4 col-form-label form-control-sm text-sm-right1"><?php echo gks_lang('Εσωτερικό');?>:</label>
                <div class="col-sm-8">
                  <input id="voipaimoriginatecall_extension" type="text" class="form-control form-control-sm" style="" placeholder="<?php echo gks_lang('π.χ.');?> 111" />
                </div>
              </div>
              <div class="form-group row">
                <label for="voipaimoriginatecall_phone" class="col-sm-4 col-form-label form-control-sm text-sm-right1"><?php echo gks_lang('Τηλέφωνο');?>:</label>
                <div class="col-sm-8">
                  <input id="voipaimoriginatecall_phone" type="text" class="form-control form-control-sm" style="" placeholder="<?php echo gks_lang('π.χ.');?> 6971881406" />
                </div>
              </div>
              <div>
                <span class="form-control-plaintext form-control-sm" style="height:unset" id="run_command_voipaimoriginatecall_result"></span>
              </div>
            </div>
          </div>
                              
        </div>
      </div>
          
          
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
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_erp_app'];?>" data-model="gks_erp_app" data-backurl="admin-erp-app.php"><?php echo gks_lang('Διαγραφή');?></button>
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>




<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-sm-12">


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Τα 50 τελευταία pings');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('pings');?>>         

          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr >	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"  ><?php echo gks_lang('Ημερομηνία');?></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><?php echo gks_lang('Lan IP');?></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><?php echo gks_lang('Wan IP');?></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><span class="tooltipster" title="<?php echo gks_lang('Έκδοση gks ERP App Desktop');?>"><?php echo gks_lang('Ver');?></span></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><span class="tooltipster" title="<?php echo gks_lang('Ώρα Η/Υ');?>"><?php echo gks_lang('Ώρα');?></span></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><span class="tooltipster" title="<?php echo gks_lang('Όνομα χρήστη');?>"><?php echo gks_lang('Χρήστης');?></span></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><span class="tooltipster" title="<?php echo gks_lang('Όνομα Η/Υ');?>"><?php echo gks_lang('Η/Υ');?></span></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><span class="tooltipster" title="<?php echo gks_lang('Έκδοση windows');?>"><?php echo gks_lang('Win');?></span></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><span class="tooltipster" title="<?php echo gks_lang('Ελεύθερος χώρος στον δίσκο');?>"><?php echo gks_lang('Δίσκος');?></span></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><?php echo gks_lang('Οθόνη');?></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><span class="tooltipster" title="<?php echo gks_lang('Mac Address');?>"><?php echo gks_lang('Mac');?></span></th>
            </tr>
          </thead>
          <tbody>
          <?php
          $sql_list = "SELECT gks_erp_app_ping.*
          FROM gks_erp_app_ping 
          where erp_app_id=".$id."
          ORDER BY id desc limit 50";
          $result_list = $db_link->query($sql_list); 
          if (!$result_list) debug_mail(false,'error sql',$sql_list);
          if (!$result_list) die('sql error');
          
          
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
            $i++;
          ?>
            <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
              <th scope="row" nowrap class="mytdcm"><?php echo ($i);?></td>      
                
              <td class="mytdcm" nowrap><?php echo showDate(strtotime($row_list['mydate']), 'd/m/Y H:i:s', 1);?></td>   
              
              <td class="mytdcm" nowrap><?php echo $row_list['lanips'];?></a></td>
              <td class="mytdcm" nowrap><a href="admin-stat-ip.php?ip=<?php echo $row_list['myip'];?>"><?php echo $row_list['myip'];?></a></td>
              <td class="mytdcm" nowrap><?php echo $row_list['appver'].' '.$row_list['arc'].'bit';?></td>
              <td class="mytdcm" nowrap><?php echo $row_list['pctime'];?></td>
              <td class="mytdcm" nowrap><?php echo $row_list['pcusername'];?></td>
              <td class="mytdcm" nowrap><?php echo $row_list['pcname'];?></td>
              <td class="mytdcm" nowrap><?php echo $row_list['winver'];?></td>
              <td class="mytdcm" nowrap><?php if ($row_list['hdwd']>0) echo number_format($row_list['hdwd']/1024,2,$GKS_NUMBER_FORMAT_DECIMAL,$GKS_NUMBER_FORMAT_THOUSAND).' GB';?></td>
              <td class="mytdcm" nowrap><?php if ($row_list['screw']>0) echo $row_list['screw'].'x'.$row_list['screh'];;?></td>
              <td class="mytdcm" nowrap><?php 
                if (trim_gks($row_list['mac'])!='') {
                  $parts=explode('|',$row_list['mac']);
                  echo implode('<br>', $parts);} 
              ?></td>


            </tr>
          
          <?php } ?>
          </tbody>
          </table>

        </div>
      </div>
      
    </div>
  </div>
</div>

<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Τα 50 τελευταία μηνύματα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('logs');?>>         

          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr >	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%' >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Ημερομηνία');?></th>
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo gks_lang('Ομάδα');?></th>
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Μήνυμα');?></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo gks_lang('IP');?></th>
            </tr>
          </thead>
          <tbody>
          <?php
          $sql_list = "SELECT gks_erp_app_log.*
          FROM gks_erp_app_log 
          where erp_app_id=".$id."
          ORDER BY id desc limit 50";
          $result_list = $db_link->query($sql_list); 
          if (!$result_list) debug_mail(false,'error sql',$sql_list);
          if (!$result_list) die('sql error');
          
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
            $i++;
          ?>
            <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
              <th scope="row" nowrap class="mytdcm"><?php echo ($i);?></td>      
              <td class="mytdcm" nowrap><?php echo showDate(strtotime($row_list['mydate']), 'd/m/Y H:i:s', 1);?></td>   
              <td class="mytdcml" nowrap><?php echo $row_list['mygroup'];?></a></td>
              <td class="mytdcml" ><?php echo $row_list['message'];?></td>
              <td class="mytdcm" nowrap><a href="admin-stat-ip.php?ip=<?php echo $row_list['ip'];?>"><?php echo $row_list['ip'];?></a></td>
            </tr>
          <?php } ?>
          </tbody>
          </table>

        </div>
      </div>
      
    </div>
  </div>
</div>

<div class="container-fluid" >
  <div class="row">
    <div class="col-md-6">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
                
        <div class="card-body" <?php echo gks_card_body('kat');?>>      
  
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_erp_app']>0) echo $row['id_erp_app'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['user_id_add']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_add']))echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['user_id_edit']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_edit'])) echo showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('IP');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><a href="admin-stat-ip.php?ip=<?php echo $row['myip'];?>"><?php echo $row['myip'];?></a></span></div>
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

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_erp_app','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_erp_app','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_erp_app','delete',$id);?>;

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
  
  var control_enter_active=false;
  
  $(document).on('keypress', function(event) {
    //var tag = e.target.tagName.toLowerCase();
    
    
    if (event.which == 10 && event.ctrlKey) {
      control_enter_active=true;
      //console.log(event.ctrlKey);
      //console.log(event.which);
      event.preventDefault();
      event.stopPropagation();
      
      elem=$('#submit_button_ok');
      if (elem.is(":visible")) {
        elem.click();  
      }
      setTimeout(function(){control_enter_active=false; }, 300);
      
    }  
    
  });
      
    
  function mysubmit() {
    
    datasend='';


    datasend+='&erp_app_name='  + encodeURIComponent($.base64.encode($("#mypostform #erp_app_name").val().trim()));
    datasend+='&erp_app_descr='  + encodeURIComponent($.base64.encode($("#mypostform #erp_app_descr").val().trim()));
    datasend+='&erp_app_url='  + encodeURIComponent($.base64.encode($("#mypostform #erp_app_url").val().trim()));
    datasend+='&erp_app_port='  + encodeURIComponent(($("#mypostform #erp_app_port").val().trim()));
    datasend+='&erp_app_sortorder='  + encodeURIComponent(($("#mypostform #erp_app_sortorder").val().trim()));
    datasend+='&erp_app_disabled=' + (($('#erp_app_disabled').is(':checked')) ? '0':'1');
    datasend+='&erp_app_token_new=' + (($('#erp_app_token_new').is(':checked')) ? '1':'0');
    datasend+='&erp_app_secret='  + encodeURIComponent($.base64.encode($("#mypostform #erp_app_secret").val().trim()));
    datasend+='&voip_localdb='  + encodeURIComponent($.base64.encode($("#mypostform #voip_localdb").val().trim()));
    datasend+='&voip_ip='  + encodeURIComponent($.base64.encode($("#mypostform #voip_ip").val().trim()));
    datasend+='&voip_AIM_port='  + encodeURIComponent($("#mypostform #voip_AIM_port").val().trim());
    datasend+='&voip_AIM_username='  + encodeURIComponent($.base64.encode($("#mypostform #voip_AIM_username").val().trim()));
    datasend+='&voip_AIM_password='  + encodeURIComponent($.base64.encode($("#mypostform #voip_AIM_password").val().trim()));
    datasend+='&voip_call_originate=' + (($('#voip_call_originate').is(':checked')) ? '1':'0');
    datasend+='&voip_call_monitoring=' + (($('#voip_call_monitoring').is(':checked')) ? '1':'0');




    datasend+=gks_custom_datasend();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-erp-app-item-exec.php?id=' + <?php echo $id;?>,
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
					  //console.log($.base64.decode(data.message));
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     

    return false;
  }  
  
  function erp_app_descr_change() {gks_resize_textarea($(this));}
  $('#erp_app_descr').on('change keyup paste', erp_app_descr_change);
  gks_resize_textarea($('#erp_app_descr'));
  
  
  $('.run_command').click(function() {
    if (need_save) {
      myalert('error:'+gks_lang('Αποθηκεύστε πρώτα την εγγραφή'));
      return;
    }
    
    var item_id=$(this).attr('id');  
    if (item_id=='run_command_voipaimoriginatecall') {
      if ($('#voipaimoriginatecall_extension').val().trim()=='') {
        myalert('error:'+gks_lang('Πληκτρολογήστε το εσωτερικό σας αριθμό'));
        return;
      }
      if ($('#voipaimoriginatecall_phone').val().trim()=='') {
        myalert('error:'+gks_lang('Πληκτρολογήστε το τηλέφωνο που θα γίνει η κλήση'));
        return;
      }
    }    
    
    //console.log(item_id);
    $(this).removeClass('fa-arrow-circle-right').addClass('fa-hourglass').css('color','gray');
    $('#' + item_id + '_result').html('');
    gks_myscroll();
    
    datasend='id=' + <?php echo $id;?>;
    datasend+='&cmd=' + encodeURIComponent($.base64.encode(item_id)); 
    if (item_id=='run_command_voipaimoriginatecall') {
      datasend+='&extension=' + encodeURIComponent($.base64.encode($('#voipaimoriginatecall_extension').val().trim())); 
      datasend+='&phone=' + encodeURIComponent($.base64.encode($('#voipaimoriginatecall_phone').val().trim())); 
    }
    
    
    $.ajax({
			url: '/my/admin-erp-app-item-run-command.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_item_id:item_id,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#' + this.gks_item_id).removeClass('fa-hourglass').addClass('fa-arrow-circle-right').css('color','green');
				myalert('error:' + jqXHR.responseText);
				gks_myscroll();
			},				
			success: function(data) {
			  $('#' + this.gks_item_id).removeClass('fa-hourglass').addClass('fa-arrow-circle-right').css('color','green');
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
  				  $('#' + this.gks_item_id + '_result').html($.base64.decode(data.html));
  					
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
				gks_myscroll();
			}
			
		});
    
          
                    
                        
  });
  
  $('#erp_app_secret_new').click(function() {
    
    myk='';
    for (i=1;i<=100;i++) {
      myk+= (Math.floor(Math.random()*1000)) + '';
      if (myk.length>32) break;
    }
    $('#erp_app_secret').val(myk);
    return false;
  });
    

  //generic
  gks_page_loading=false;
  



  $('.myneedsave').on('input keyup paste', function() {
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
//db_close();
include_once('_my_footer_admin.php');


