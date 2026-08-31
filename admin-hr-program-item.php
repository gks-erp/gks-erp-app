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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hr_program',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_gks_hr_program_add=gks_permission_user_can_action_php($my_wp_user_id,'gks_delivery_methods','add',0);


$user_companys=gks_get_companys_list();

gks_get_hr_program_status($hr_program_status,$hr_program_status_styles);
//print '<pre>';print_r($hr_program_status);die();

gks_get_hr_program_vardia($hr_program_vardia,$hr_program_vardia_styles);
//print '<pre>';print_r($hr_program_vardia);die();


$gks_custom_prepare = gks_custom_table_item_prepare('gks_hr_program',['from'=>'item']);

$base_sql="SELECT gks_hr_program.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit,
  ".GKS_WP_TABLE_PREFIX."users.gks_nickname,".GKS_WP_TABLE_PREFIX."users.user_email, ".GKS_WP_TABLE_PREFIX."users.gks_mobile as user_mobile,
  table_last_name.mylast_name as user_last_name, table_first_name.myfirst_name as user_first_name,
  gks_users.eponimia,gks_users.title,gks_users.afm,gks_users.doy,gks_users.epaggelma,
  gks_users.order_sxolio,gks_users.pelati_sxolio,
  gks_lang.lang_name, ".GKS_WP_TABLE_PREFIX."users.gks_lang as user_lang,
  gks_users.ma_odos,gks_users.ma_arithmos,gks_users.ma_orofos,gks_users.ma_perioxi,gks_users.ma_poli,gks_users.ma_tk,
  gks_users.ma_country_id,gks_country.country_name,
  gks_users.ma_nomos_id,gks_nomoi.nomos_descr,
  gks_company.company_title, gks_company_subs.company_sub_title,
  ".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned,
  gks_production_posta.production_posto_descr
  FROM ((((((((((((gks_hr_program
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_hr_program.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_hr_program.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN gks_company ON gks_hr_program.company_id = gks_company.id_company) 
  LEFT JOIN gks_company_subs ON gks_hr_program.company_sub_id = gks_company_subs.id_company_sub) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hr_program.hr_program_user_id = ".GKS_WP_TABLE_PREFIX."users.ID)
  LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id)
  LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country)
  LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos)
  LEFT JOIN (
    SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS myfirst_name
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='first_name'))
  )  AS table_first_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_first_name.user_id) 
  LEFT JOIN (
    SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mylast_name
    FROM ".GKS_WP_TABLE_PREFIX."usermeta
    WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='last_name'))
  )  AS table_last_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_last_name.user_id) 
  LEFT JOIN gks_lang ON ".GKS_WP_TABLE_PREFIX."users.gks_lang = gks_lang.id_lang)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_hr_program.assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID)
  LEFT JOIN gks_production_posta ON gks_hr_program.hr_program_posto_id = gks_production_posta.id_production_posto
";

$template_id=0; if (isset($_GET['template_id'])) $template_id=intval($_GET['template_id']);
if ($id==-1) {
  if ($template_id>0) {
    $sql=$base_sql."where gks_hr_program.id_hr_program = ".$template_id;
    $result = $db_link->query($sql);        
    if (!$result) debug_mail(false,'error sql',$sql);
    if (!$result) die('sql error');
    if ($result->num_rows!=1) {
      debug_mail(false,'record not found sql tempate',$sql); 
      die('no record found (tempate)');
    } 
    $row = $result->fetch_assoc();
    //$row['id_hr_program']=-1; //gia na doulecei to custom
    $row['mydate_add']=null;
    $row['mydate_edit']=null;
    $row['user_id_add']=0;
    $row['user_id_edit']=0; 
    $row['myip']='';

    
    $my_page_title=gks_lang('Νέο πρόγραμμα υπαλλήλων από το πρότυπο').' #'.$template_id;   
  }
  if ($template_id==0) {  
    $row=array();
    $row['user_id_add'] =0;
    $row['user_id_edit'] =0;
    $row['gks_nickname_add'] ='';
    $row['gks_nickname_edit'] ='';
    $row['myip'] ='';
    $row['id_hr_program']=-1;
    $row['hr_program_name'] ='';
    $row['hr_program_descr'] ='';
    $row['hr_program_user_id']=0;
    $row['gks_nickname']='';
    $row['user_email']='';
    $row['user_mobile']='';
    $row['user_last_name']='';
    $row['user_first_name']='';
    $row['eponimia']='';
    $row['title']='';
    $row['afm']='';
    $row['doy']='';
    $row['epaggelma']='';
    $row['order_sxolio']='';
    $row['pelati_sxolio']='';
    $row['lang_name']='';
    $row['user_lang']='';
    $row['ma_odos']='';
    $row['ma_arithmos']='';
    $row['ma_orofos']='';
    $row['ma_perioxi']='';
    $row['ma_poli']='';
    $row['ma_tk']='';
    $row['ma_country_id']=0;
    $row['country_name']='';
    $row['ma_nomos_id']=0;
    $row['nomos_descr']='';


    $row['company_id']=0;
    $row['company_sub_id']=0;

    $row['company_title']='';
    $row['company_sub_title']='';
    
    if (count($user_companys)>=1) {
      foreach ($user_companys as $value) {
        $row['company_id']=$value['id_company'];
        $row['company_sub_id']=$value['id_company_sub'];
        $row['company_title']=$value['company_title'];
        $row['company_sub_title']=$value['company_sub_title'];
        break;
      } 
    }
    $row['hr_program_date']=date('Y-m-d H:i:s');
    $row['hr_program_date_from']=date('Y-m-d H:i:s');

    $def_duration_minutes=60;
    if (isset($gks_user_settings['gks_hr_program']['def_duration_minutes'])) {
      $def_duration_minutes=intval($gks_user_settings['gks_hr_program']['def_duration_minutes']);
    }
    $row['hr_program_date_to']=date('Y-m-d H:i:s',time() + $def_duration_minutes*60); //sin 1 ora
    $row['hr_program_status_id']=0;
    foreach ($hr_program_status as $value) {
      if ($value['hr_program_status_disabled']==0) {
        $row['hr_program_status_id']=$value['id_hr_program_status'];
        break;
      }
    }
    $row['hr_program_vardia_id']=0;
    $row['hr_program_vardia_is_ergasia']=0;
    $row['hr_program_is_ergasia']=0;
    foreach ($hr_program_vardia as $value) {
      if ($value['hr_program_vardia_disabled']==0) {
        $row['hr_program_vardia_id']=$value['id_hr_program_vardia'];
        $row['hr_program_vardia_is_ergasia']=$value['hr_program_vardia_is_ergasia'];
        $row['hr_program_is_ergasia']=$value['hr_program_vardia_is_ergasia'];    
        break;
      }
    }
    $row['hr_program_posto_id']=0;
    $row['production_posto_descr']='';
    $row['hr_program_color']='';
    $row['internal_note']='';
    $row['assigned_id']=0;
    $row['gks_nickname_assigned']='';

    $my_page_title=gks_lang('Νέο πρόγραμμα υπαλλήλων');
  }

} else {
  $sql=$base_sql."where gks_hr_program.id_hr_program = ".$id;
  //die('<pre>'.$sql);
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Πρόγραμμα Υπαλλήλων').': '.$row['hr_program_name'];
  $object_title=$row['hr_program_name'];
}

$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);
//print '<pre>';print_r($gks_custom_row);die();


stat_record();
$nav_active_array=array('hr','hr_program');





$user_comms=gks_get_user_communications($row['hr_program_user_id']);
//print '<pre>'; print_r($user_comms);die();


include_once('_my_header_admin.php');
?>
<link rel="stylesheet" href="css/admin-hr-program-item.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Πρόγραμμα Υπαλλήλων');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Πρόγραμμα Υπαλλήλων');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
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
            <label for="hr_program_date" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Καταχώρηση');?>:</label>
            <div class="col-md-8">
              <input id="hr_program_date" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['hr_program_date'])) echo  showDate(strtotime($row['hr_program_date']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px">
            </div>
          </div>
          <div class="form-group row">
            <label for="hr_program_status_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κατάσταση');?>:</label>
            <div class="col-md-8" style="padding-top: 3px;">
              <?php
              foreach ($hr_program_status as $row_select) {
                if ($row_select['hr_program_status_disabled']==0) {
                  echo '<span data-id="'.$row_select['id_hr_program_status'].'" '.
                  'class="hr_program_status_this hr_program_status_'.$row_select['id_hr_program_status'].
                  ($row_select['id_hr_program_status']==$row['hr_program_status_id'] ? ' hr_program_status_selected' : '').
                  '">'.$row_select['hr_program_status_descr'].
                  '</span>';
                }
              }
              ?>
            </div>
          </div>
          <div class="form-group row">
            <label for="hr_program_vardia_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Βάρδια');?>:</label>
            <div class="col-md-8" style="padding-top: 3px;">
              <?php

              foreach ($hr_program_vardia as $row_select) {
                if ($row_select['hr_program_vardia_disabled']==0) {
                  echo '<span '.
                  'data-id="'.$row_select['id_hr_program_vardia'].'" '.
                  'data-is_ergasia="'.$row_select['hr_program_vardia_is_ergasia'].'" '.
                  'data-time_start="'.$row_select['hr_program_vardia_time_start'].'" '.
                  'data-time_end="'.$row_select['hr_program_vardia_time_end'].'" '.
                  'data-weekday="'.
                    $row_select['hr_program_vardia_weekday1'].'|'.
                    $row_select['hr_program_vardia_weekday2'].'|'.
                    $row_select['hr_program_vardia_weekday3'].'|'.
                    $row_select['hr_program_vardia_weekday4'].'|'.
                    $row_select['hr_program_vardia_weekday5'].'|'.
                    $row_select['hr_program_vardia_weekday6'].'|'.
                    $row_select['hr_program_vardia_weekday0'].'" '.
                  'class="hr_program_vardia_this hr_program_vardia_'.$row_select['id_hr_program_vardia'].
                  ($row_select['id_hr_program_vardia']==$row['hr_program_vardia_id'] ? ' hr_program_vardia_selected' : '').
                  '">'.$row_select['hr_program_vardia_descr'].
                  '</span>';
                }
              }
              ?>
            </div>
          </div>          
          <div class="form-group row">
            <label for="hr_program_user" class="col-md-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Υπάλληλος');?>:</label>
            <div class="col-md-8">

              <input id="hr_program_user" type="text" class="form-control form-control-sm myneedsave email_contact_name"  
              value="<?php echo htmlspecialchars_gks($row['gks_nickname']);?>" 
              style="width:calc(98% - 22px);display:inline;" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
              <input id="hr_program_user_id" type="hidden" value="<?php echo $row['hr_program_user_id'];?>" class="myneedsave">
              <a id="autocomplete_hr_program_user_id" tabindex="-1" href="admin-users-item.php?id=<?php echo $row['hr_program_user_id'];?>" style="<?php if ($row['hr_program_user_id']==0) echo 'display:none';?>"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή επαφής');?>"></i></a>
            </div>
          </div>

          <div class="form-group row" id="hr_program_posto_div" style="<?php echo ($row['hr_program_is_ergasia']==0?'display:none;':'');?>">
            <label for="hr_program_posto" class="col-md-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πόστο');?>:</label>
            <div class="col-md-8">

              <input id="hr_program_posto" type="text" class="form-control form-control-sm myneedsave email_contact_name"  
              value="<?php echo htmlspecialchars_gks($row['production_posto_descr']);?>" 
              style="width:calc(98% - 22px);display:inline;" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
              <input id="hr_program_posto_id" type="hidden" value="<?php echo $row['hr_program_posto_id'];?>" class="myneedsave">
              <a id="autocomplete_hr_program_posto_id" tabindex="-1" href="admin-production-posta-item.php?id=<?php echo $row['hr_program_posto_id'];?>" style="<?php if ($row['hr_program_posto_id']==0) echo 'display:none';?>"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή πόστου');?>"></i></a>
            </div>
          </div>


          
          <div class="form-group row">
            <label for="hr_program_date_from" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερομηνία');?>:</label>
            <div class="col-md-8">
              <?php
              $duration='';$duration_days=0;
              if (isset($row['hr_program_date_from']) and isset($row['hr_program_date_to'])) {
                $temp=strtotime($row['hr_program_date_to'])-strtotime($row['hr_program_date_from']);
                $duration_days=floor($temp/(24*60*60));
                $temp=$temp-$duration_days*(24*60*60);
                //if ($temp < 24*60*60) { //kat apo mia 1 imera
                $duration=date('H:i',$temp); 
              }
              ?>
              <span style="font-size: 0.875rem;"><?php echo gks_lang('Από');?>: </span>
                <input id="hr_program_date_from" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['hr_program_date_from'])) echo  showDate(strtotime($row['hr_program_date_from']), 'd/m/Y H:i', 1);?>">
              <br>
              <span style="font-size: 0.875rem;"><?php echo gks_lang('Διάρκεια');?>: </span>
                <input id="hr_program_date_duration_days" type="number" class="form-control form-control-sm myneedsave tooltipster" value="<?php echo $duration_days;?>" autocomplete="off" title="<?php echo gks_lang('Ημέρες');?>" min="0">
                <input id="hr_program_date_duration" type="text" class="form-control form-control-sm myneedsave tooltipster" value="<?php echo $duration;?>" autocomplete="off" title="<?php echo gks_lang('Ώρες:Λεπτά');?>">
              <br>
              <span style="font-size: 0.875rem;"><?php echo gks_lang('Έως');?>: </span>
                <input id="hr_program_date_to" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['hr_program_date_to'])) echo  showDate(strtotime($row['hr_program_date_to']), 'd/m/Y H:i', 1);?>">
              
              
            </div>
          </div>
          

          <div class="form-group row">
            <label for="hr_program_name" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-sm-8">
              <input id="hr_program_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['hr_program_name']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="hr_program_descr" class="col-sm-12 col-form-label form-control-sm text-sm-right1" ><?php echo gks_lang('Σχόλιο');?>:</label>
            <div class="col-sm-12">
              <textarea id="hr_program_descr" type="text" class="gks_tinymce form-control form-control-sm myneedsave" style="min-height:100px;height:100px;" ><?php echo htmlspecialchars_gks($row['hr_program_descr']);?></textarea>
            </div>
          </div>          


          <div class="form-group row">
            <label for="hr_program_color" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χρώμα');?>:</label>
            <div class="col-md-8">
              <input id="hr_program_color" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['hr_program_color']);?>" style="max-width:200px;">
            </div>
          </div> 
          
          <div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>

          
          <div class="form-group row">
            <label for="internal_note" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εσωτερική Σημείωση');?>:</label>
            <div class="col-md-8">
              <textarea id="internal_note" type="text" class="form-control form-control-sm myneedsave" style="min-height:100px;height:100px;" ><?php echo htmlspecialchars_gks($row['internal_note']);?></textarea>
            </div>
          </div>    
          <div class="form-group row">
            <label for="assigned_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ανάθεση σε');?>:</label>
            <div class="col-md-8">
              <input id="assigned_id" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['gks_nickname_assigned']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['assigned_id'];?>">
            </div>
          </div>
          <div style="height: 1px;width: 100%;background-color: lightgray;margin-bottom: 16px;"></div>
          
          <div class="form-group row">
            <label for="company" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εταιρεία');?>:</label>
            <div class="col-md-8">
              <input id="company" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['company_title']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
              <input id="company_id" type="hidden" value="<?php echo $row['company_id'];?>" class="myneedsave">
            </div>
          </div>
          <div class="form-group row">
            <label for="company_sub_title" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Υποκατάστημα');?>:</label>
            <div class="col-md-8">
              <input id="company_sub_title" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php if ($row['company_sub_id']==0) echo gks_lang('Κεντρικό'); else echo htmlspecialchars_gks($row['company_sub_title']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
              <input id="company_sub_id" type="hidden" value="<?php echo $row['company_sub_id'];?>" class="myneedsave">
            </div>
          </div>
          



          
          
          
          
        </div>
      </div>


    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Στοιχεία Υπαλλήλου');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('ypallilos');?>>  



          <div class="form-group row" style="margin-bottom: 0px;">
            <div class="col-sm-6">
              <div class="form-group1 row" id="div_pelati_sxolio" style="<?php echo (trim_gks($row['pelati_sxolio'])=='' ? 'display:none;' : '');?>;margin-bottom: 1rem;padding-right: 15px;padding-left: 30px;">
                <div class="offset-md-4 col-sm-8 alert alert-danger" role="alert" id="text_pelati_sxolio" style="margin-bottom: 0px;"><?php echo nl2br_gks($row['pelati_sxolio']);?></div>
              </div>
                            
            </div>
            
            <div class="col-sm-6">
              <div class="form-group1 row" id="div_order_sxolio" style="<?php echo (trim_gks($row['order_sxolio'])=='' ? 'display:none;' : '');?>;margin-bottom: 1rem;padding-right: 15px;padding-left: 30px;">
                <div class="offset-md-4 col-sm-8 alert alert-danger" role="alert" id="text_order_sxolio" style="margin-bottom: 0px;"><?php echo nl2br_gks($row['order_sxolio']);?></div>
              </div>               
            </div>
          </div>


          <div class="form-group row">
            <label for="dr_user_first_name" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_first_name">
                <?php echo $row['user_first_name'];?>
              </div>
            </div>
            <label for="dr_user_last_name" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επώνυμο');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_last_name">
                <?php echo $row['user_last_name'];?>
              </div>
            </div>
          </div>
          <div class="form-group row">
            <label for="dr_user_email" class="col-sm-2 col-form-label form-control-sm text-sm-right">email:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_email">
                <?php 
                if (isset($user_comms['email'])) {
                  $temp=array();
                  foreach ($user_comms['email'] as $value) $temp[]=$value['html'];
                  echo implode('<br>', $temp);
                }?>
              </div>
            </div>
            <label for="dr_user_mobile" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τηλέφωνο');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_mobile">
                <?php 
                if (isset($user_comms['phone'])) {
                  $temp=array();
                  foreach ($user_comms['phone'] as $value) $temp[]=$value['html'];
                  echo implode('<br>', $temp);
                }?>
              </div>                
            </div>
          </div>
              
          <div class="form-group row">
            <label for="dr_user_lang" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Γλώσσα');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_lang" data-val="<?php echo $row['user_lang'];?>">
                <?php echo $row['lang_name'];?>
              </div>
              
            </div>
          </div>
          
          <div class="form-group row">
            <label for="dr_user_eponimia" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επωνυμία');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_eponimia">
                <?php echo $row['eponimia'];?>
              </div>
            </div>
            <label for="dr_user_title" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τίτλος');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_title">
                <?php echo $row['title'];?>
              </div>
            </div>
          </div>
          <?php
          $ee_initials='';
          $sql="select id_country,country_ee,country_name,country_initials 
          FROM gks_country where id_country=".intval($row['ma_country_id'])." ORDER BY country_name";
          $result_select = $db_link->query($sql);        
          if (!$result_select) {
            debug_mail(false,'error sql',$sql);
            die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
          }
          if ($result_select->num_rows==1) {
            $row_select = $result_select->fetch_assoc();
            $ee_initials=trim_gks($row_select['country_ee']);
          }
          $this_select='';
          ?>
          
          
          <div class="form-group row">
            <label for="dr_user_afm" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΑΦΜ');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height">
                <span id="dr_user_afm_ee_initial_static" style="<?php echo ($ee_initials!='' ? '' : 'display:none;');?>"><?php echo $ee_initials;?></span><span 
                  style="display: inline-block;text-align:left;vertical-align: middle;"
                  id="dr_user_afm" class=" <?php echo ($ee_initials=='' ? '':'dr_user_afm_views');?>"><?php echo htmlspecialchars_gks($row['afm']);?></span>
            
              </div>
            </div>
            <label for="dr_user_doy" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΔΟΥ');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_doy">
                <?php echo $row['doy'];?>
              </div>
            </div>
          </div>


          <div class="form-group row">
            <label for="dr_user_epaggelma" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επάγγελμα');?>:</label>
            <div class="col-sm-10">
              <div class="form-control-sm gks_unset_height" id="dr_user_epaggelma">
                <?php echo $row['epaggelma'];?>
              </div>
            </div>
          </div>  

           

          


          <div class="form-group row">
            <label for="dr_user_ma_odos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Οδός');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_ma_odos">
                <?php echo $row['ma_odos'];?>
              </div>
            </div>
            <label for="dr_user_ma_arithmos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αριθμός');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_ma_arithmos">
                <?php echo $row['ma_arithmos'];?>
              </div>
            </div>
            
          </div>
          <div class="form-group row">
            <label for="dr_user_ma_orofos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όροφος');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_ma_orofos">
                <?php echo $row['ma_orofos'];?>
              </div>
            </div>
            <label for="dr_user_ma_perioxi" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Περιοχή');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_ma_perioxi">
                <?php echo $row['ma_perioxi'];?>
              </div>
            </div>
          </div>

          <div class="form-group row">
            <label for="dr_user_ma_poli" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πόλη');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_ma_poli">
                <?php echo $row['ma_poli'];?>
              </div>
            </div>
            <label for="dr_user_ma_tk" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΤΚ');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_ma_tk">
                <?php echo $row['ma_tk'];?>
              </div>
            </div>
          </div>

          <div class="form-group row">
            <label for="dr_user_ma_country_id" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Χώρα');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_ma_country_id" data-id="<?php echo $row['ma_country_id'];?>">
                <?php 
                echo gks_lang_data_trans($row['country_name'],$row['ma_country_id'],'gks_country','country_name');
                ?>
              </div>
            </div>
            <label for="dr_user_ma_nomos_id" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Νομός');?>:</label>
            <div class="col-sm-4">
              <div class="form-control-sm gks_unset_height" id="dr_user_ma_nomos_id">
                <?php 
                echo gks_lang_data_trans($row['nomos_descr'],$row['ma_nomos_id'],'gks_nomoi','nomos_descr');
                ?>
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
      <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
      <?php if ($id>0) {?>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_hr_program'];?>" data-model="gks_hr_program" data-backurl="admin-hr-program.php" <?php if ($id<=0) echo 'disabled';?>><?php echo gks_lang('Διαγραφή');?></button>
      <?php } ?>
      <?php 
      if ($id>0 and $perm_gks_hr_program_add) {
        echo '<a href="admin-hr-program-item.php?id=-1&template_id='.$id.'" style="margin-bottom:0px;" '.
          'class="btn btn-primary tooltipster" '.
          'id="submit_button_template" '.
          'title="<div style=\'text-align: center;\'>'.gks_lang('Δημιουργία αντιγράφου').'</div>">'.
          '<i class="fas fa-copy" style="font-size: 120%;"></i>'.
        '</a> ';
      }?>   
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90();?>



<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
      
      <?php echo getObjectRels('gks_hr_program',$id); ?>
      <?php echo getActivityObjectTable('gks_hr_program',$id); ?>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <span style="vertical-align: middle;"><?php echo gks_lang('Μηνύματα');?></span>
          <button type="button" class="btn btn-sm btn-primary" id="message_item_add"><?php echo gks_lang('Προσθήκη');?></button>
        </div>
        <div class="card-body" <?php echo gks_card_body('message');?>>
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
            <thead>
              <tr>
                <th class="table-dark" scope="col" width="0%" nowrap style="text-align: center;">#</th>
                <th class="table-dark" scope="col" width="20%" nowrap><?php echo gks_lang('Πότε');?></th>
                <th class="table-dark" scope="col" width="20%" nowrap align="left"><?php echo gks_lang('Ποιος');?></th>                
                <th class="table-dark" scope="col" width="60%" nowrap align="left"><?php echo gks_lang('Μήνυμα');?></th>
                <th class="table-dark" scope="col" width="0%" nowrap style="text-align: center;"><i class="fas fa-envelope" style="color: #35dc35;font-size: 120%;"></i></th>
              </tr>
            </thead>  
            <tbody id="item_messages_body"> 
              
            <?php
            $sql_msg="SELECT gks_hr_program_messages.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
            FROM gks_hr_program_messages LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hr_program_messages.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
            WHERE gks_hr_program_messages.hr_program_id=".$id."
            ORDER BY gks_hr_program_messages.mydate_add DESC, gks_hr_program_messages.id_hr_program_message DESC;";
            $result_msg = $db_link->query($sql_msg);        
            if (!$result_msg) debug_mail(false,'error sql',$sql_msg);
            if (!$result_msg) die('sql error');
            
            $j = 0;
            while ($row_msg = $result_msg->fetch_assoc()) {
              $j++; ?>
          
            
            <tr id="tr_messages_<?php echo $row_msg['id_hr_program_message'];?>">
              <th scope="row" class="mytdcm message_aa"><?php echo $j;?></th>
              <td class="mytdcml"><?php echo showDate(strtotime($row_msg['mydate_add']), 'd/m/Y H:i', 1);?></td>  
              <td class="mytdcml"><?php echo $row_msg['gks_nickname'];?></td>  
              <td class="mytdcml"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
                echo str_replace('[[-r]]', '<i class="fas fa-arrow-alt-circle-right gksvm"></i>', $row_msg['hr_program_message']);
                ?></div></div></td>    
              <td class="mytdcm"><?php 
                if ($row_msg['email_id']!=0) {
                  echo '<i class="fas fa-envelope gks_email_view" data-id="'.$row_msg['email_id'].'"></i>';
                }
                if ($row_msg['sms_id']!=0) {
                  echo '<i class="fas fa-sms gks_sms_view" data-id="'.$row_msg['sms_id'].'"></i>';
                }                
                ?></td>
            </tr>
            <?php } ?>                      
            </tbody>   
          </table>                
        </div>
      </div>
            
			<?php
			$obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_hr_program','id'=>$id));
      echo $obj_fileslist['html'];
      ?>
      

       
    </div>
    <div class="col-md-6">


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Ιστορικό');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('his');?>>      

          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
            <thead>
              <tr>
                <th class="table-dark" scope="col" width="0%" nowrap>#</th>
                <th class="table-dark" scope="col" width="20%" nowrap><?php echo gks_lang('Πότε');?></th>
                <th class="table-dark" scope="col" width="20%" nowrap align="left"><?php echo gks_lang('Ποιος');?></th>                
                <th class="table-dark" scope="col" width="60%" nowrap align="left"><?php echo gks_lang('Τι');?></th>
              </tr>
            </thead>  
            <tbody> 
              
            <?php
            $sql_log="SELECT gks_hr_program_log.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
            FROM gks_hr_program_log LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_hr_program_log.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
            WHERE gks_hr_program_log.hr_program_id=".$id."
            ORDER BY gks_hr_program_log.id_gks_hr_program_log DESC;";
            $result_log = $db_link->query($sql_log);        
            if (!$result_log) debug_mail(false,'error sql',$sql_log);
            if (!$result_log) die('sql error');
            
            $j = 0;
            while ($row_log = $result_log->fetch_assoc()) {
              $j++; ?>
          
            <tr>
              <th scope="row" align="center"><?php echo $j;?></th>
              <td align="left"><?php echo showDate(strtotime($row_log['add_date']), 'd/m/Y H:i:s', 1);?></td>  
              <td align="left"><?php echo $row_log['gks_nickname'];?></td>  
              <td align="left"><?php echo str_replace('[[-r]]', '<i class="fas fa-arrow-alt-circle-right gksvm"></i>', $row_log['sxolio']);?></td>    
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
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_hr_program']>0) echo $row['id_hr_program'];?></span></div>
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


<?php include_once 'admin-obj-send-message.php'; ?>


<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>



var from_php_dialog_object_rel_curr='gks_hr_program';
var from_php_activity_model='gks_hr_program';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;


var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;
var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>';



var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hr_program','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hr_program','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_hr_program','delete',$id);?>;


var from_php_dialog_item_message_email_from_array=[];
<?php 
echo 'from_php_dialog_item_message_email_from_array.push($.base64.decode(\'' . base64_encode($GKS_SITE_EMAIL) . '\'));'."\n"; 
?>









jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  



  
  

    
});
</script>


<script src='/my/js/tinymce/tinymce.min.js'></script>
<script src="js/admin-hr-program-item.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="js/admin-obj-send-message.js?v=<?php echo $gks_cache_version;?>"></script>



<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


