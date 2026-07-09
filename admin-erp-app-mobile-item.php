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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_erp_app_mobile',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}



//echo GKS_PROXY['HTTP_PREFIX']; die();



if ($id<=0) {
  if (isset($_POST['id'])) $id=intval($_POST['id']);
}
if ($id == 0 or $id < -1) {header('Location: /my'); die(); }



$gks_custom_prepare = gks_custom_table_item_prepare('gks_erp_app_mobile',['from'=>'item']);


if ($id==-1) {



  $row=array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_erp_app_mobile']=-1;
  $row['erp_app_mobile_name']='';
  $row['erp_app_mobile_country']='';
  $row['erp_app_mobile_phonenumber']='';
  $row['erp_app_mobile_cost_per_sms']=0;
  $row['erp_app_mobile_descr']='';
  $row['erp_app_mobile_token']='';
  $row['erp_app_mobile_secret']='';
  $row['erp_app_mobile_user_id']=0;
  $row['erp_app_mobile_user_token']='';
  $row['erp_app_mobile_user_gks_nickname']='';
  
  $row['erp_app_mobile_url']='frp';
  $row['erp_app_mobile_port']=55555;
  $row['erp_app_mobile_sortorder']=1000;
  $row['erp_app_mobile_disabled']=0;
  
  $row['erp_app_mobile_url2ip']='';
  $row['erp_app_mobile_lan_ip']='';
  $row['erp_app_mobile_wan_ip']='';
  $row['appver']='';
  $row['ostime']='';
  $row['personname']='';
  $row['phonenumber']='';
  $row['osver']='';
  $row['hdwd']=0;
  $row['screw']=0;
  $row['screh']=0;
  $row['mac']='';

  $row['erp_app_mobile_can_capture']=0;
  $row['erp_app_mobile_can_sms']=0;
  $row['erp_app_mobile_can_gps']=0;
  $row['erp_app_mobile_gps_dt']=30;
  $row['erp_app_mobile_gps_ds']=50;
  $row['erp_app_mobile_gps_chunk']=10;
  $row['erp_app_mobile_gps_timegap']=900;
  
  
  $row['erp_app_mobile_can_pos']=0;
  $row['erp_app_mobile_pos_list']='';
  $row['erp_app_mobile_local_printers']='';
  $row['erp_app_mobile_can_transfer']=0;
  
  $row['firebase_token']='';
  
  $my_page_title=gks_lang('Νέο gks ERP App Mobile');
} else {
  $sql ="SELECT gks_erp_app_mobile.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit,
  gks_erp_app_mobile_ping.ostime, gks_erp_app_mobile_ping.personname, gks_erp_app_mobile_ping.phonenumber, gks_erp_app_mobile_ping.osver, gks_erp_app_mobile_ping.appver, 
  gks_erp_app_mobile_ping.hdwd, gks_erp_app_mobile_ping.screw, gks_erp_app_mobile_ping.screh, gks_erp_app_mobile_ping.mac,
  erp_app_mobile_user_table.gks_nickname as erp_app_mobile_user_gks_nickname
  
  FROM (((gks_erp_app_mobile 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_erp_app_mobile.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_erp_app_mobile.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN gks_erp_app_mobile_ping ON gks_erp_app_mobile.mobile_last_ping_id = gks_erp_app_mobile_ping.id)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS erp_app_mobile_user_table ON gks_erp_app_mobile.erp_app_mobile_user_id = erp_app_mobile_user_table.ID


  where id_erp_app_mobile = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('gks ERP App Mobile').': '.$row['erp_app_mobile_name'];
  $object_title=$row['erp_app_mobile_name'];
}
$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);


stat_record();

$nav_active_array=array('manage','manage_erp_app_mobile');


include_once('_my_header_admin.php');
?>
<style>
span.form-control-plaintext {
  height:unset;    
}
.run_command {  
  font-size: 150%;
  line-height: 1.2;
  color:green;
  cursor:pointer
}
  
#erp_app_mobile_can_gps_div label, #erp_app_mobile_can_gps_div select {
  font-size:80%;
}  
#table_gps td.td_point {
  padding:0px;  
}
#table_gps i.fas {
  font-size:24px;  
}
#run_command_frpclog_result {
  display: block;
  width: 100%;
  max-height: 300px;
  overflow: auto;
  
}
#run_command_frpclog_result > pre {
  background-color: #eeeeee;  
}

#span_firebase_token {
  height:unset !important;
  white-space: break-spaces;
  font-size:80%;
}

</style>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3>gks ERP App Mobile: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3>gks ERP App Mobile: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
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
            <label for="erp_app_mobile_name" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα');?>:</label>
            <div class="col-sm-8">
              <input id="erp_app_mobile_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['erp_app_mobile_name']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="erp_app_mobile_name" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αριθμός Κινητού');?>:</label>
            <div class="col-sm-8">
              <div style="display:flex;gap: 6px;flex-direction: row;flex-wrap: nowrap;justify-content: space-between;"> 
              <input id="erp_app_mobile_country" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['erp_app_mobile_country']);?>" placeholder="<?php echo gks_lang('π.χ.');?> +30" style="width:100%;max-width: 70px;">
              <input id="erp_app_mobile_phonenumber" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['erp_app_mobile_phonenumber']);?>" placeholder="<?php echo gks_lang('π.χ.');?> 6971881406" style="width:100%;">
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label for="erp_app_mobile_cost_per_sms" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κόστος ανά SMS');?>:</label>
            <div class="col-sm-8">
              <input id="erp_app_mobile_cost_per_sms" type="number" class="form-control form-control-sm myneedsave" value="<?php if ($row['erp_app_mobile_cost_per_sms']>0) echo $row['erp_app_mobile_cost_per_sms'];?>" placeholder="<?php echo gks_lang('π.χ.');?> 0,04" min="0" max="0.10" step="0.001">
            </div>
          </div>

          <div class="form-group row">
            <label for="erp_app_mobile_descr" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-sm-8">
              <textarea id="erp_app_mobile_descr" type="text" class="form-control form-control-sm myneedsave" style="min-height:100px;height:100px;" ><?php echo htmlspecialchars_gks($row['erp_app_mobile_descr']);?></textarea>
            </div>
          </div>

          <div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>
          
          <div class="form-group row">
            <label for="erp_app_mobile_token" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Δημόσιο Κλειδί');?>:</label>
            <div class="col-sm-8">
              <input id="erp_app_mobile_token" type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars_gks($row['erp_app_mobile_token']);?>" readonly>
              <?php if ($id>0){?>
              <small class="form-text text-muted">
                <input type="checkbox" id="erp_app_mobile_token_new" val="1" style="vertical-align: middle;"> 
                <label for="erp_app_mobile_token_new" style="margin: 0px;"><?php echo gks_lang('Δημιουργία νέου κλειδιού');?></label>
              </small>
              <?php } ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="erp_app_mobile_secret" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ιδιωτικό Κλειδί');?>:</label>
            <div class="col-sm-8">
              <input id="erp_app_mobile_secret" type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars_gks($row['erp_app_mobile_secret']);?>">
              
              <small class="form-text text-muted">
                <?php echo gks_lang('Θα πρέπει να έχει μήκος τουλάχιστον 32 χαρακτήρες');?><br>
                <span id="erp_app_mobile_secret_new" class="btn btn-primary btn-sm"><?php echo gks_lang('Δημιουργία τυχαίου');?></span>
              </small>
              
            </div>
          </div>          

          <div class="form-group row">
            <label for="erp_app_mobile_url" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Url');?>:</label>
            <div class="col-sm-8">
              <input id="erp_app_mobile_url" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['erp_app_mobile_url']);?>" placeholder="<?php echo gks_lang('π.χ.');?> frp">
              <small class="form-text text-muted"><i><?php echo gks_lang('frp (μέσω proxy gks) ή IP/Hostname');?></i></small>
            </div>
          </div>
          <div class="form-group row">
            <label for="erp_app_mobile_port" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πόρτα');?>:</label>
            <div class="col-sm-8">
              <input id="erp_app_mobile_port" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['erp_app_mobile_port']);?>" min="10000" max="65000">
              <small class="form-text text-muted"><i><?php echo gks_lang('από 10000 έως 65000');?></i></small>
            </div>
          </div>          
          <div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>

          <div class="form-group row">
            <label for="erp_app_mobile_user_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χρήστης');?>:</label>
            <div class="col-md-8">
              <input id="erp_app_mobile_user_id" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['erp_app_mobile_user_gks_nickname']);?>" 
              style="width:calc(98% - 22px);display:inline;" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" 
              data-user_id="<?php echo $row['erp_app_mobile_user_id'];?>"
              >
              <a id="autocomplete_erp_app_mobile_user_id" tabindex="-1" href="admin-users-item.php?id=<?php echo $row['erp_app_mobile_user_id'];?>" style="<?php if ($row['erp_app_mobile_user_id']==0) echo 'display:none';?>"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή επαφής');?>"></i></a>
            </div>
          </div>
          <div class="form-group row">
            <label for="erp_app_mobile_user_token" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Token Χρήστη');?>:</label>
            <div class="col-sm-8">
              <input id="erp_app_mobile_user_token" type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars_gks($row['erp_app_mobile_user_token']);?>" disabled>
            </div>
          </div>          
                    


          <div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>
          

          <div class="form-group row">
            <label for="erp_app_mobile_sortorder" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-sm-8">
              <input id="erp_app_mobile_sortorder" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['erp_app_mobile_sortorder']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="erp_app_mobile_disabled" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργή');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="erp_app_mobile_disabled" value="1" <?php if ($row['erp_app_mobile_disabled']==0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>

          

        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Δυνατότητες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('can');?>>         
          <div class="form-group row">
            <label for="erp_app_mobile_can_pos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εντατική Λιανική');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="erp_app_mobile_can_pos" value="1" <?php if ($row['erp_app_mobile_can_pos']!=0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>
          <div class="form-group row" id="erp_app_mobile_pos_list_div" style="<?php if ($row['erp_app_mobile_can_pos']==0) echo 'display:none;';?>">
            <label for="erp_app_mobile_pos_list" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μόνο τα');?>:</label>
            <div class="col-md-8">
              <?php
              $value='';
              $rdata=trim_gks($row['erp_app_mobile_pos_list']);
              if ($rdata!='') {
                //echo '<pre>'.$rdata;die();
                $rdata=unserialize($rdata);
                
                
                if (is_array($rdata) and count($rdata)>0) {
                  $sqltags="select id_pos as myid,pos_name as mytag from gks_pos where id_pos in (".implode(',',$rdata).") order by pos_name";
                  $resulttags = $db_link->query($sqltags);        
                  if (!$resulttags) {debug_mail(false,'error sql',$sqltags);echo 'sql error';die();}
                  $rdata=array();
                  while ($rowtag = $resulttags->fetch_assoc()) $rdata[]=$rowtag['mytag'].' (#'.$rowtag['myid'].')';  
                  if (count($rdata)>0) $value=implode(']][[',$rdata);
                }
              }
              echo '<input id="erp_app_mobile_pos_list" value="'.htmlspecialchars_gks($value).'" class="form-control form-control-sm myneedsave" type="text">';

              ?>
              
              
              <small class="form-text text-muted"><?php echo gks_lang('Εάν δεν επιλεγεί κάποιο θα είναι όλα διαθέσιμα');?></small>
            </div>
          </div>
          
          <div class="form-group row">
            <label for="erp_app_mobile_can_capture" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σάρωση');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="erp_app_mobile_can_capture" value="1" <?php if ($row['erp_app_mobile_can_capture']!=0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>
          <div class="form-group row">
            <label for="erp_app_mobile_can_sms" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('SMS');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="erp_app_mobile_can_sms" value="1" <?php if ($row['erp_app_mobile_can_sms']!=0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>
          <div class="form-group row">
            <label for="erp_app_mobile_can_gps" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('GPS');?>:</label>
            <div class="col-md-8">
              <div>
                <input type="checkbox" id="erp_app_mobile_can_gps" value="1" <?php if ($row['erp_app_mobile_can_gps']!=0) echo ' checked '; ?> class="switchery1_sel">
              </div>
              <div id="erp_app_mobile_can_gps_div" style="<?php if ($row['erp_app_mobile_can_gps']==0) echo 'display:none;';?>">
                <div class="form-group row">
                  <label for="erp_app_mobile_gps_dt" class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ακρίβεια χρόνου σε δευτερόλεπτα');?>:</label>
                  <div class="col-md-6">
                    <select id="erp_app_mobile_gps_dt" class="form-control form-control-sm myneedsave">
                      <?php 
                      $mylist=[1,2,3,4,5,6,7,8,9,10,15,20,30,45,60,90,120,180,240,300,360,420,480,540,600,900,1200,1800,3600];
                      foreach ($mylist as $mi) {
                        if ($mi<90) $sv=$mi.' secs'; else $sv=($mi/60).' mins'; 
                        echo '<option value="'.$mi.'"'.
                        ($mi==$row['erp_app_mobile_gps_dt'] ? ' selected' : '').
                        '>'.$sv.'</option>';
                      } ?>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label for="erp_app_mobile_gps_ds" class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ακρίβεια απόστασης σε μέτρα');?>:</label>
                  <div class="col-md-6">
                    <select id="erp_app_mobile_gps_ds" class="form-control form-control-sm myneedsave">
                      <?php 
                      $mylist=[1,2,3,4,5,10,20,30,40,50,60,70,80,90,100,150,200,250,300,400,500,600,700,800,900,1000];
                      foreach ($mylist as $mi) {
                        $sv=$mi.' m'; 
                        echo '<option value="'.$mi.'"'.
                        ($mi==$row['erp_app_mobile_gps_ds'] ? ' selected' : '').
                        '>'.$sv.'</option>';
                      } ?>
                    </select>
                  </div>
                </div>                
                <small class="form-text text-muted" style="margin-bottom: 1rem;">
                  <?php echo gks_lang('Όσο μικρότερη η ακρίβεια σε χρόνο και απόσταση, τόσο περισσότερα θα είναι τα δεδομένα');?>
                  <!--<br><?php echo gks_lang('Οι προεπιλογές είναι 30 secs, 50 m, 10 σημεία');?>-->
                </small>
                              
                <div class="form-group row">
                  <label for="erp_app_mobile_gps_chunk" class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πλήθος σημείων αποστολής ανά κλήση');?>:</label>
                  <div class="col-md-6">
                    <select id="erp_app_mobile_gps_chunk" class="form-control form-control-sm myneedsave">
                      <?php 
                      $mylist=[1,2,3,4,5,6,7,8,9,10];
                      foreach ($mylist as $mi) {
                        $sv=$mi.' '.($mi>=2 ? gks_lang('σημεία') : gks_lang('σημείο')); 
                        echo '<option value="'.$mi.'"'.
                        ($mi==$row['erp_app_mobile_gps_chunk'] ? ' selected' : '').
                        '>'.$sv.'</option>';
                      } ?>
                    </select>
                  </div>
                </div> 
                <small class="form-text text-muted" style="margin-bottom: 1rem;">
                  <?php echo gks_lang('Όσο μεγαλύτερο το πλήθος σημείων αποστολής ανά κλήση τόσο λιγότερα δεδομένα από το κινητό προς το gks ERP App');?>
                  <!--<br><?php echo gks_lang('Οι προεπιλογές είναι 30 secs, 50 m, 10 σημεία');?>-->
                </small>
                
                <div class="form-group row">
                  <label for="erp_app_mobile_gps_timegap" class="col-md-6 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χρονικό κενό για νέα διαδρομή');?>:</label>
                  <div class="col-md-6">
                    <select id="erp_app_mobile_gps_timegap" class="form-control form-control-sm myneedsave">
                      <?php 
                      $mylist=[60,120,180,240,300,360,420,480,540,600,900,1200,1500,1800,2100,2400,2700,3000,3300,3600,3900,4200,4500,4800,5100,5400,5700,6000,6300,6600,6900,7200,7800,8400,9000,9600,10200,10800,14400,18000,21600];
                      foreach ($mylist as $mi) {
                        $sv=($mi/60).' '.($mi<=60 ? gks_lang('λεπτό') : gks_lang('λεπτά')); 
                        switch ($mi) {   
                          case 3600: $sv=gks_lang('1 ώρα'); break;
                          case 7200: $sv=gks_lang('2 ώρες'); break;
                          case 10800: $sv=gks_lang('3 ώρες'); break;
                          case 14400: $sv=gks_lang('4 ώρες'); break;
                          case 18000: $sv=gks_lang('5 ώρες'); break;
                          case 21600: $sv=gks_lang('6 ώρες'); break;
                        }
                        echo '<option value="'.$mi.'"'.
                        ($mi==$row['erp_app_mobile_gps_timegap'] ? ' selected' : '').
                        '>'.$sv.'</option>';
                      } ?>
                    </select>
                  </div>
                </div>                

                               
              </div>              
            </div>
            
            
          </div>          

<?php if (GKS_TRANSFER) {?>
          <div class="form-group row">
            <label for="erp_app_mobile_can_transfer" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Transfer');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="erp_app_mobile_can_transfer" value="1" <?php if ($row['erp_app_mobile_can_transfer']!=0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>                
<?php } ?>
          



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
              if ($row['erp_app_mobile_url']=='frp') { 
                if (trim_gks($row['erp_app_mobile_token'])!='') {
                  $public_url='http://'.$row['erp_app_mobile_token'].GKS_PROXY['DOMAIN_BASE_NAME'].':'.GKS_PROXY['VPORT'];
                }
              } else {
                if (trim_gks($row['erp_app_mobile_url'])!='' and $row['erp_app_mobile_port']>0) {
                  $public_url='http://'.$row['erp_app_mobile_url'].':'.$row['erp_app_mobile_port'];
                }
              }
              if ($public_url!='') echo '<a href="'.$public_url.'" target="_blank">'.$public_url.'</a>';
            }
            ?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('LAN IP');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo $row['erp_app_mobile_lan_ip'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('WAN IP');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo $row['erp_app_mobile_wan_ip'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('URL to IP');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo $row['erp_app_mobile_url2ip'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τελευταία σύνδεση');?>:</label>
            <div class="col-sm-8">
              <?php if (isset($row['erp_app_mobile_last_ping'])) {
              echo '<span class="form-control-plaintext form-control-sm">';
                echo showDate(strtotime($row['erp_app_mobile_last_ping']), 'd/m/Y H:i:s', 1);
              echo '</span>';
              //echo '<br>';
              echo '<span class="form-control-plaintext form-control-sm ' ;
              if (strtotime($row['erp_app_mobile_last_ping']) > time()-60*60) //mia ora, to elaxisto einai 15 lepta
                echo 'gks_erp_app_alive'; else echo 'gks_erp_app_not_alive';
              echo '">';
                echo secondsago(strtotime($row['erp_app_mobile_last_ping']));
              echo '</span>';
              }?>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Έκδοση gks ERP App Mobile');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo $row['appver'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ώρα κινητού');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo $row['ostime'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα χρήστη');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo $row['personname'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αριθμός από κινητό');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo $row['phonenumber'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Έκδοση λειτουργικού');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo $row['osver'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ελεύθερος χώρος');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['hdwd']>0) echo number_format($row['hdwd']/1024,2,$GKS_NUMBER_FORMAT_DECIMAL,$GKS_NUMBER_FORMAT_THOUSAND).' GB';?> </span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Οθόνη');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['screw']>0) echo $row['screw'].'x'.$row['screh'];?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Mac Address');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php 
              if (trim_gks($row['mac'])!='') {
                $parts=explode('|',$row['mac']);
                echo implode('<br>', $parts); 
                
              }
            ?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Firebase Token');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" id="span_firebase_token"><?php echo $row['firebase_token'];?></span></div>
          </div>

          
        </div>
      </div>
          
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Εντολές');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('commands');?>>         
          <div style="text-align:center;font-weight:bold;">Web</div>
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Λειτουργεί');?>;</label>
            <div class="col-sm-2">
              <i id="run_command_alive" class="run_command fa fa-arrow-circle-right"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" id="run_command_alive_result"></span></div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Ρυθμίσεις');?>:</label>
            <div class="col-sm-2">
              <i id="run_command_settings" class="run_command fa fa-arrow-circle-right"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" id="run_command_settings_result"></span></div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Στατιστικά');?>:</label>
            <div class="col-sm-2">
              <i id="run_command_stats" class="run_command fa fa-arrow-circle-right"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" id="run_command_stats_result"></span></div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Λήψη Δεδομένων');?>:</label>
            <div class="col-sm-2">
              <i id="run_command_getdata" class="run_command fa fa-arrow-circle-right"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" id="run_command_getdata_result"></span></div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left">frpc.log:</label>
            <div class="col-sm-2">
              <i id="run_command_frpclog" class="run_command fa fa-arrow-circle-right"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" id="run_command_frpclog_result"></span></div>
          </div>          
          
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Εκτυπωτές');?>:</label>
            <div class="col-sm-2">
              <i id="run_command_local_printers" class="run_command fa fa-arrow-circle-right"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" id="run_command_local_printers_result">
            <?php
            $erp_app_mobile_local_printers=trim_gks($row['erp_app_mobile_local_printers']);
            if ($erp_app_mobile_local_printers!='') {
              $temp=unserialize($erp_app_mobile_local_printers);  
              if (is_array($temp) and count($temp)>0) {
                echo '<ol>';
                foreach ($temp as $value) {
                  echo '<li><b>'.$value['Name'].'</b> '.gks_lang('Κατάσταση').': '.$value['Status'].' ('.$value['Init_id'].'|'.$value['Status_id'].')</li>';
                }
                echo '</ol>';
              }
            }
            
            ?>
              
            </span></div>
          </div>
                    
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left" style="height: unset;"><?php echo gks_lang('Αποστολή δοκιμαστικού SMS');?>:</label>
            <div class="col-sm-2">
              <i id="run_command_testsms" class="run_command fa fa-arrow-circle-right"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" id="run_command_testsms_result"></span></div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left" style="height: unset;"><?php echo gks_lang('Αποστολή όλων των SMS στο gks ERP App');?>:</label>
            <div class="col-sm-2">
              <i id="run_command_readallsms" class="run_command fa fa-arrow-circle-right"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" id="run_command_readallsms_result"></span></div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left" style="height: unset;"><?php echo gks_lang('Στίγμα');?>:</label>
            <div class="col-sm-2">
              <i id="run_command_gps_curr" class="run_command fa fa-arrow-circle-right"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" id="run_command_gps_curr_result"></span></div>
          </div>
          
          <div style="height: 1px;width: 100%;background-color: lightgray;margin-top: 16px;margin-bottom: 16px;"></div>

          <div style="text-align:center;font-weight:bold;">Push Notification</div>

          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Αποστολή <em>Hello!</em>');?></label>
            <div class="col-sm-2">
              <i id="run_command_push_notifyinfo" class="run_command fa fa-arrow-circle-right"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" id="run_command_push_notifyinfo_result"></span></div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Επανεκκίνηση service');?>:</em></label>
            <div class="col-sm-2">
              <i id="run_command_push_restartservice" class="run_command fa fa-arrow-circle-right"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" id="run_command_push_restartservice_result"></span></div>
          </div>
          <div class="row">
            <label class="col-sm-2 col-form-label form-control-sm text-sm-left"><?php echo gks_lang('Λήψη Δεδομένων');?>:</em></label>
            <div class="col-sm-2">
              <i id="run_command_push_getdata" class="run_command fa fa-arrow-circle-right"></i>
            </div>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" id="run_command_push_getdata_result"></span></div>
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
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_erp_app_mobile'];?>" data-model="gks_erp_app_mobile" data-backurl="admin-erp-app-mobile.php"><?php echo gks_lang('Διαγραφή');?></button>
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

          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="table_pings">
          <thead>
            <tr >	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'   >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"   ><?php echo gks_lang('Ημερομηνία');?></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"   ><?php echo gks_lang('Lan IP');?></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"   ><?php echo gks_lang('Wan IP');?></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"   ><span class="tooltipster" title="<?php echo gks_lang('Έκδοση gks ERP App Mobile');?>"><?php echo gks_lang('Ver');?></span></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><span class="tooltipster" title="<?php echo gks_lang('Ώρα Η/Υ');?>"><?php echo gks_lang('Ώρα');?></span></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"   ><span class="tooltipster" title="<?php echo gks_lang('Όνομα χρήστη');?>"><?php echo gks_lang('Χρήστης');?></span></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"   ><span class="tooltipster" title="<?php echo gks_lang('Αριθμός από κινητό');?>"><?php echo gks_lang('Αριθμός');?></span></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"   ><span class="tooltipster" title="<?php echo gks_lang('Έκδοση λειτουργικού');?>"><?php echo gks_lang('OS');?></span></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%"   ><span class="tooltipster" title="<?php echo gks_lang('Ελεύθερος χώρος');?>"><?php echo gks_lang('Δίσκος');?></span></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><?php echo gks_lang('Οθόνη');?></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="10%" ><span class="tooltipster" title="<?php echo gks_lang('Mac Address');?>"><?php echo gks_lang('Mac');?></span></th>
            </tr>
          </thead>
          <tbody>
          <?php
          $sql_list = "SELECT gks_erp_app_mobile_ping.*
          FROM gks_erp_app_mobile_ping 
          where erp_app_mobile_id=".$id."
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
              <td class="mytdcm" nowrap><?php echo $row_list['appver'];?></td>
              <td class="mytdcm" nowrap><?php echo $row_list['ostime'];?></td>
              <td class="mytdcm" nowrap><?php echo $row_list['personname'];?></td>
              <td class="mytdcm" nowrap><?php echo $row_list['phonenumber'];?></td>
              <td class="mytdcm" nowrap><?php echo $row_list['osver'];?></td>
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
          <?php echo gks_lang('Τα 50 τελευταία τεχνικά μηνύματα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('logs');?>>         

          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="table_logs">
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
          $sql_list = "SELECT gks_erp_app_mobile_log.*
          FROM gks_erp_app_mobile_log 
          where erp_app_mobile_id=".$id."
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
              <td class="mytdcml" nowrap><?php echo $row_list['mygroup'];?></td>
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

<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Τα 50 τελευταία σημεία GPS');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('gps');?>>         

          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="table_gps">
          <thead>
            <tr >	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%' >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="20%"><?php echo gks_lang('Ημερομηνία');?></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('ΑΑ');?></th>
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo gks_lang('Latitude');?></th>
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="20%"><?php echo gks_lang('Longitude');?></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Σημείο');?></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Provider');?></th>
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="40%"><?php echo gks_lang('Διαδρομή');?></th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo gks_lang('IP');?></th>
            </tr>
          </thead>
          <tbody>
          <?php
          $sql_list = "SELECT gks_gps.*
          FROM gks_gps 
          where erp_app_mobile_id=".$id."
          ORDER BY id_gps desc limit 50";
          $result_list = $db_link->query($sql_list); 
          if (!$result_list) debug_mail(false,'error sql',$sql_list);
          if (!$result_list) die('sql error');
          
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
            $i++;
          ?>
            <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
              <th scope="row" nowrap class="mytdcm"><?php echo ($i);?></td>      
              <td class="mytdcm" nowrap><?php echo showDate(strtotime($row_list['mytime']), 'd/m/Y H:i:s', 1);?></td>   
              <td class="mytdcm" nowrap><?php echo $row_list['myaa'];?></td>
              <td class="mytdcml" nowrap><?php echo $row_list['mylat'];?></td>
              <td class="mytdcml" nowrap><?php echo $row_list['mylng'];?></td>
              <td class="mytdcm td_point" nowrap><a href="http://maps.google.com/maps?q=loc:<?php echo $row_list['mylat'];?>,<?php echo $row_list['mylng'];?>" target="_blank"><i class="fas fa-map-marker-alt"></i></a></td>
              <td class="mytdcm" nowrap><?php echo $row_list['myprovider'];?></td>
              <td class="mytdcml" nowrap><?php echo $row_list['mydiadromi'];?></td>
              
              
              <td class="mytdcm" nowrap><a href="admin-stat-ip.php?ip=<?php echo $row_list['myip'];?>"><?php echo $row_list['myip'];?></a></td>
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
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_erp_app_mobile']>0) echo $row['id_erp_app_mobile'];?></span></div>
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

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_erp_app_mobile','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_erp_app_mobile','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_erp_app_mobile','delete',$id);?>;

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


    datasend+='&erp_app_mobile_name='  + encodeURIComponent($.base64.encode($("#mypostform #erp_app_mobile_name").val().trim()));
    datasend+='&erp_app_mobile_country='  + encodeURIComponent($.base64.encode($("#mypostform #erp_app_mobile_country").val().trim()));
    datasend+='&erp_app_mobile_phonenumber='  + encodeURIComponent($.base64.encode($("#mypostform #erp_app_mobile_phonenumber").val().trim()));
    datasend+='&erp_app_mobile_cost_per_sms='  + encodeURIComponent($("#mypostform #erp_app_mobile_cost_per_sms").val().trim());
    datasend+='&erp_app_mobile_descr='  + encodeURIComponent($.base64.encode($("#mypostform #erp_app_mobile_descr").val().trim()));
    datasend+='&erp_app_mobile_url='  + encodeURIComponent($.base64.encode($("#mypostform #erp_app_mobile_url").val().trim()));
    datasend+='&erp_app_mobile_port='  + encodeURIComponent(($("#mypostform #erp_app_mobile_port").val().trim()));
    datasend+='&erp_app_mobile_sortorder='  + encodeURIComponent(($("#mypostform #erp_app_mobile_sortorder").val().trim()));
    datasend+='&erp_app_mobile_disabled=' + (($('#erp_app_mobile_disabled').is(':checked')) ? '0':'1');
    datasend+='&erp_app_mobile_token_new=' + (($('#erp_app_mobile_token_new').is(':checked')) ? '1':'0');
    datasend+='&erp_app_mobile_secret='  + encodeURIComponent($.base64.encode($("#mypostform #erp_app_mobile_secret").val().trim()));
    datasend+='&erp_app_mobile_user_id=' + encodeURIComponent($('#erp_app_mobile_user_id').attr('data-user_id'));    
    datasend+='&erp_app_mobile_can_capture=' + (($('#erp_app_mobile_can_capture').is(':checked')) ? '1':'0');
    datasend+='&erp_app_mobile_can_sms=' + (($('#erp_app_mobile_can_sms').is(':checked')) ? '1':'0');
    datasend+='&erp_app_mobile_can_gps=' + (($('#erp_app_mobile_can_gps').is(':checked')) ? '1':'0');
    datasend+='&erp_app_mobile_gps_dt='  + encodeURIComponent(($("#mypostform #erp_app_mobile_gps_dt").val().trim()));
    datasend+='&erp_app_mobile_gps_ds='  + encodeURIComponent(($("#mypostform #erp_app_mobile_gps_ds").val().trim()));
    datasend+='&erp_app_mobile_gps_chunk='  + encodeURIComponent(($("#mypostform #erp_app_mobile_gps_chunk").val().trim()));
    datasend+='&erp_app_mobile_gps_timegap='  + encodeURIComponent(($("#mypostform #erp_app_mobile_gps_timegap").val().trim()));
    datasend+='&erp_app_mobile_can_pos=' + (($('#erp_app_mobile_can_pos').is(':checked')) ? '1':'0');
    datasend+='&erp_app_mobile_pos_list='  + encodeURIComponent($.base64.encode($("#mypostform #erp_app_mobile_pos_list").val().trim()));
    if ($('#erp_app_mobile_can_transfer').length==1) {
      datasend+='&erp_app_mobile_can_transfer=' + (($('#erp_app_mobile_can_transfer').is(':checked')) ? '1':'0');
    }

    
    datasend+=gks_custom_datasend();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-erp-app-mobile-item-exec.php?id=' + <?php echo $id;?>,
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
  
  function erp_app_mobile_descr_change() {gks_resize_textarea($(this));}
  $('#erp_app_mobile_descr').on('change keyup paste', erp_app_mobile_descr_change);
  gks_resize_textarea($('#erp_app_mobile_descr'));
  
  
  $('.run_command').click(function() {
    var item_id=$(this).attr('id');  
    //console.log(item_id);
    $(this).removeClass('fa-arrow-circle-right').addClass('fa-hourglass').css('color','gray');
    $('#' + item_id + '_result').html('');
    
    datasend='id=' + <?php echo $id;?>;
    datasend+='&cmd=' + encodeURIComponent($.base64.encode(item_id)); 
    
    $.ajax({
			url: '/my/admin-erp-app-mobile-item-run-command.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			gks_item_id:item_id,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#' + this.gks_item_id).removeClass('fa-hourglass').addClass('fa-arrow-circle-right').css('color','green');
				myalert('error:' + jqXHR.responseText);
			},				
			success: function(data) {
			  $('#' + this.gks_item_id).removeClass('fa-hourglass').addClass('fa-arrow-circle-right').css('color','green');
				if (!data) {
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
					  //console.log(data);
					  if (this.gks_item_id=='run_command_gps_curr') {
					    ddd=$.base64.decode(data.html);
					    ddd=JSON.parse(ddd);
					    if (ddd.success) {
					      if (ddd.lat==0 && ddd.lng==0) {
					        hhh=gks_lang('Σφάλμα');
					      } else {
					        //hhh=ddd.message + '<br>';
					        hhh=gks_lang('Latitude')+': ' + ddd.lat + '<br>';
  					      hhh+=gks_lang('Longitude')+': ' + ddd.lng + '<br>';
  					      hhh+=gks_lang('Provider')+': ' + ddd.p + '<br>';
  					      hhh+=gks_lang('Time')+': ' + ddd.t + '<br>';
  					      hhh+='<a href="http://maps.google.com/maps?q=loc:' + ddd.lat + ',' + ddd.lng + ' (point)" target="_blank">Google map</a>';
					      }
					      $('#' + this.gks_item_id + '_result').html(hhh);
					    } else {
					      $('#' + this.gks_item_id + '_result').html(ddd.message);
					    } 
					    //console.log(ddd);
					  } else if (this.gks_item_id=='run_command_local_printers') {
					    local_printers=$.base64.decode(data.html);
					    dataremote=JSON.parse(local_printers);
					    if (dataremote.success==true) {
					      local_printers=dataremote.local_printers;
					      html= '<ol>';
                for(ii=0; ii < local_printers.length; ii++) {
                  html+= '<li><b>' + local_printers[ii].Name + '</b> '+gks_lang('Κατάσταση')+': ' + local_printers[ii].Status + ' (' + local_printers[ii].Init_id + '|' + local_printers[ii].Status_id + ')</li>';
                }
                html+= '</ol>';
                if (local_printers.length==0)  {
                   html= gks_lang('Δεν βρέθηκαν εκτυπωτές');
                } 
                $('#' + this.gks_item_id + '_result').html(html);
					    } else {
					      myalert('error:' + $.base64.decode(dataremote.message));
					    }
					    //console.log(data);
					    //console.log(local_printers);
					    
					  } else {
  				    $('#' + this.gks_item_id + '_result').html($.base64.decode(data.html));
  				  }
  					
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});
    
          
                    
                        
  });
  
  
  $('#erp_app_mobile_secret_new').click(function() {
    
    myk='';
    for (i=1;i<=100;i++) {
      myk+= (Math.floor(Math.random()*1000)) + '';
      if (myk.length>32) break;
    }
    $('#erp_app_mobile_secret').val(myk);
    return false;
  });


  var gks_pos_tags = [];
  <?php 
  $sqltags="select id_pos as myid, pos_name as mytag from gks_pos where pos_name<>'' order by pos_name";
  $resulttags = $db_link->query($sqltags);
  if (!$resulttags) {debug_mail(false,'error sql',$sqltags);  die('sql error');}
  while ($rowtag = $resulttags->fetch_assoc())   echo "gks_pos_tags.push($.base64.decode('".base64_encode(trim_gks($rowtag['mytag']).' (#'.$rowtag['myid'].')')."'));";
  ?>
  $('#erp_app_mobile_pos_list').tagit({allowSpaces:true,singleFieldDelimiter: ']][[',availableTags: gks_pos_tags,showAutocompleteOnFocus : true,onTagAdded:function() {need_save=true;},onTagRemoved:function() {need_save=true;},});



  $('#erp_app_mobile_can_gps').on('change', function() {
    if ($('#erp_app_mobile_can_gps').is(':checked')) {
      $('#erp_app_mobile_can_gps_div').slideDown();
    } else {
      $('#erp_app_mobile_can_gps_div').slideUp();
    }  
  });
     
  $('#erp_app_mobile_can_pos').on('change', function() {
    if ($('#erp_app_mobile_can_pos').is(':checked')) {
      $('#erp_app_mobile_pos_list_div').slideDown();
    } else {
      $('#erp_app_mobile_pos_list_div').slideUp();
    }  
  });
  
  $('#erp_app_mobile_user_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        eml:1,
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
    autoFocus: true,
    delay: 300, //default
    select: function( event, ui ) {
      need_save=true;
      $("#erp_app_mobile_user_id").attr('data-user_id',ui.item.id);
      $('#autocomplete_erp_app_mobile_user_id').attr('href', 'admin-users-item.php?id=' + ui.item.id.trim()).show();
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $("#erp_app_mobile_user_id").val('').attr('data-user_id','0');
        $('#autocomplete_erp_app_mobile_user_id').hide(); 
      }
    }
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


