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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_crm_tasks',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_gks_crm_tasks_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_crm_tasks','edit',0);
$perm_mono_dika_mou=gks_permission_user_int_cond($my_wp_user_id,'gks_crm_tasks','01');

$gks_voip_params=gks_voip_user_params();

$perm_id_print_forms=gks_permission_user_condition($my_wp_user_id,'gks_print_forms','01');


$user_companys=gks_get_companys_list();






gks_get_tasks_status($tasks_status,$tasks_status_styles);
//echo '<pre>';print_r($tasks_status);print_r($tasks_status_styles);die();
//echo date('d/m/Y H:i:s',strtotime('10:11:12')); die();




if ($id==-1) {
  //$nav_active_array=array('crm','crm_new_task'); 
  $nav_active_array=array('crm','crm_tasks');
} else {
  $nav_active_array=array('crm','crm_tasks');
}


$gks_custom_prepare = gks_custom_table_item_prepare('gks_crm_tasks',['from'=>'item']);
//print '<pre>';print_r($gks_custom_prepare);die();

if ($id==-1) {
  $row = array();
  $row['id_crm_task']=-1;
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['task_date']=date('Y-m-d H:i:s');
  $row['task_status_id']=1;
  $row['task_color']='';
  
  
  $row['user_id']=0;
  $row['gks_nickname']='';
  $row['form_id']=0;
  $row['post_id']=0;
  
  $row['first_name']='';
  $row['last_name']='';
  $row['email']='';
  $row['mobile']='';
  $row['phone']='';
  $row['web']='';
  $row['address_extra']=-1;
  $row['odos']='';
  $row['arithmos']='';
  $row['orofos']='';
  $row['perioxi']='';
  $row['poli']='';
  $row['tk']='';
  $row['nomos_id']=0;
  $row['country_id']=91;
  $row['map_latitude']='';
  $row['map_longitude']='';
  $row['subject']='';
  $row['message']='';
  //$row['birthday']='';
  $row['esoda']=0;
  $row['task_planned_date_from']=date('Y-m-d H:i:s');

  $def_duration_minutes=60;
  if (isset($gks_user_settings['gks_crm_tasks']['def_duration_minutes'])) {
    $def_duration_minutes=intval($gks_user_settings['gks_crm_tasks']['def_duration_minutes']);
  }
  $row['task_planned_date_to']=date('Y-m-d H:i:s',time() + $def_duration_minutes*60); //sin 1 ora
  
  $row['internal_note']='';
  $row['user_lang']='el-GR';
  $row['pelati_sxolio']='';
  $row['order_sxolio']='';

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

  $row['eponimia']='';
  $row['title']='';
  $row['afm']='';
  $row['doy']='';
  $row['epaggelma']='';
  $row['fiscal_position_id']=1;
  $row['pricelist_id']=1;
  $row['assigned_id']=0;
  $row['gks_nickname_assigned']='';
  $row['crm_channel_id']=0;
  $row['crm_channel_sale_descr']='';
  $row['crm_channel_contact_id']=0;
  $row['crm_channel_contact_gks_nickname']='';
  $row['crm_channel_campain_id']=0;
  $row['ads_campain_name']='';
  $row['crm_channel_url']='';
  $row['crm_channel_code']=''; 
  $row['crm_channel_text']='';

  $row['print_date']='';
  $row['print_file_name']='';
  $row['print_file_url']='';
  $row['print_user_id']=0; 
  $row['print_crm_task_status']='';
  $row['print_crm_task_status_id']=0; 

  $my_page_title=gks_lang('Νέα Εργασία');

  if (isset($_GET['crm_task_machine_id'])) {
    $sql_new="SELECT gks_crm_machine.*,
    ".GKS_WP_TABLE_PREFIX."users.user_email, ".GKS_WP_TABLE_PREFIX."users.user_url,
    ".GKS_WP_TABLE_PREFIX."users.gks_nickname, ".GKS_WP_TABLE_PREFIX."users.gks_mobile,
    ".GKS_WP_TABLE_PREFIX."users.fiscal_position_id,".GKS_WP_TABLE_PREFIX."users.pricelist_id, 
    ".GKS_WP_TABLE_PREFIX."users.gks_lang,
    gks_users.eponimia, gks_users.title, gks_users.afm, gks_users.doy, gks_users.epaggelma, 
    gks_users.ma_odos, gks_users.ma_arithmos, gks_users.ma_orofos, gks_users.ma_perioxi, gks_users.ma_poli, gks_users.ma_tk, gks_users.ma_country_id, gks_users.ma_nomos_id, gks_users.ma_latitude, gks_users.ma_longitude,
    gks_users.phone_home,gks_users.genisi_date,gks_users.pelati_sxolio,gks_users.order_sxolio,
    table_last_name.mylast_name as user_last_name, table_first_name.myfirst_name as user_first_name
    
    FROM (((gks_crm_machine 
    LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_machine.crm_machine_user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
    LEFT JOIN gks_users ON gks_crm_machine.crm_machine_user_id = gks_users.user_id)
    LEFT JOIN (
      SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS myfirst_name
      FROM ".GKS_WP_TABLE_PREFIX."usermeta
      WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='first_name'))
    )  AS table_first_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_first_name.user_id) 
    LEFT JOIN (
      SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mylast_name
      FROM ".GKS_WP_TABLE_PREFIX."usermeta
      WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='last_name'))
    ) AS table_last_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_last_name.user_id

    where gks_crm_machine.id_crm_machine=".intval($_GET['crm_task_machine_id'])." and id_crm_machine>0";
    
    $result_new = $db_link->query($sql_new);        
    if (!$result_new) {
      debug_mail(false,'error sql',$sql_new);
      die('sql error');
    }
    if ($result_new->num_rows==1) {
      $row_new = $result_new->fetch_assoc();

      $row['user_id']=$row_new['crm_machine_user_id'];
      $row['gks_nickname']=$row_new['gks_nickname'];
      $row['first_name']=$row_new['user_first_name'];
      $row['last_name']=$row_new['user_last_name'];
      $row['email']=$row_new['user_email'];
      $row['mobile']=$row_new['gks_mobile'];
      $row['phone']=$row_new['phone_home'];
      $row['web']=$row_new['user_url'];
      if (trim_gks($row_new['genisi_date'])<>'') $row['birthday']=$row_new['genisi_date'];
      $row['address_extra']=$row_new['users_extra_address_id'];
      if ($row_new['users_extra_address_id']<=0) {
        $row['odos']=$row_new['ma_odos'];
        $row['arithmos']=$row_new['ma_arithmos'];
        $row['orofos']=$row_new['ma_orofos'];
        $row['perioxi']=$row_new['ma_perioxi'];
        $row['poli']=$row_new['ma_poli'];
        $row['tk']=$row_new['ma_tk'];
        $row['nomos_id']=$row_new['ma_nomos_id'];
        $row['country_id']=$row_new['ma_country_id'];
        $row['map_latitude']=$row_new['ma_latitude'];
        $row['map_longitude']=$row_new['ma_longitude'];
      } else {
        $sql_ea="SELECT gks_users_extra_address.*, country_name,nomos_descr
        FROM (gks_users_extra_address 
        LEFT JOIN gks_country ON gks_users_extra_address.ea_country_id = gks_country.id_country) 
        LEFT JOIN gks_nomoi ON gks_users_extra_address.ea_nomos_id = gks_nomoi.id_nomos
        WHERE id_users_extra_address=".$row_new['users_extra_address_id']." and gks_users_extra_address.user_id=".$row['user_id'];
        $result_ea = $db_link->query($sql_ea);        
        if (!$result_ea) {
          debug_mail(false,'error sql',$sql_ea);
          die('sql error');
        }
        if ($result_ea->num_rows==1) {
          $row_ea = $result_ea->fetch_assoc();
          $row['odos']=$row_ea['ea_odos'];
          $row['arithmos']=$row_ea['ea_arithmos'];
          $row['orofos']=$row_ea['ea_orofos'];
          $row['perioxi']=$row_ea['ea_perioxi'];
          $row['poli']=$row_ea['ea_poli'];
          $row['tk']=$row_ea['ea_tk'];
          $row['nomos_id']=$row_ea['ea_nomos_id'];
          $row['country_id']=$row_ea['ea_country_id'];
          $row['map_latitude']=$row_ea['ea_latitude'];
          $row['map_longitude']=$row_ea['ea_longitude'];
    
        }
      }
      $row['eponimia']=$row_new['eponimia'];
      $row['title']=$row_new['title'];
      $row['afm']=$row_new['afm'];
      $row['doy']=$row_new['doy'];
      $row['epaggelma']=$row_new['epaggelma'];
      $row['fiscal_position_id']=$row_new['fiscal_position_id'];
      $row['pricelist_id']=$row_new['pricelist_id'];
      
      $row['user_lang']=$row_new['gks_lang'];
      $row['pelati_sxolio']=$row_new['pelati_sxolio'];
      $row['order_sxolio']=$row_new['order_sxolio'];
            
      
    }

    
  }
  

} else {
  $sql=select_gks_crm_tasks()." where id_crm_task = ".$id;
  if ($perm_mono_dika_mou==1) {
    $sql.=" and gks_crm_tasks.id_crm_task in (
      select crm_task_id from gks_crm_tasks_employee where crm_task_employee_id=".$my_wp_user_id." group by crm_task_id
    )";  
  }
  
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
  

  
  
  $my_page_title=gks_lang('Εργασία').': '.$row['subject'];
  $object_title=$row['subject'];
}


$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);
//print '<pre>';print_r($gks_custom_row);die();

$pelati_sxolio=nl2br_gks($row['pelati_sxolio']);
$order_sxolio=nl2br_gks($row['order_sxolio']);




unset($mybasketarray);
gks_mybasketarray_create($mybasketarray);
$mybasketarray['from']='crm_task';
$mybasketarray['id_object'] = $id;
$mybasketarray['company_id']= $row['company_id'];
$mybasketarray['company_sub_id']= $row['company_sub_id'];
$mybasketarray['user']['user_id']=$row['user_id'];
$mybasketarray['user']['afm']=$row['afm'];
$mybasketarray['user']['ma_country_id']=$row['country_id'];
$mybasketarray['parastatiko']=1; //parastatiko

gks_CheckAFM_Live($mybasketarray);
$check_vies=$mybasketarray['check_vies'];

//print '<pre>';print_r($check_vies);print '</pre>';//die();

stat_record();



include_once('_my_header_admin.php');
?>
<link href="css/admin-crm-task-item.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">


<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Εργασία');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Εργασία');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
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
        <div class="card-body" <?php echo gks_card_body('bas');?> > 


          <div class="form-group row">
            <label for="task_date" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερομηνία');?>:</label>
            <div class="col-md-8">
              <input id="task_date" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['task_date'])) echo  showDate(strtotime($row['task_date']), 'd/m/Y H:i', 1);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px">
            </div>
          </div>
          
          <div class="form-group row">
            <label for="task_status_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κατάσταση');?>:</label>
            <div class="col-md-8" style="padding-top: 3px;">
              <?php
              foreach ($tasks_status as $row_select) {
                if ($row_select['task_status_disabled']==0) {
                  echo '<span data-id="'.$row_select['id_crm_task_status'].'" '.
                  'class="task_status_this task_status_'.$row_select['id_crm_task_status'].
                  ($row_select['id_crm_task_status']==$row['task_status_id'] ? ' task_status_selected' : '').
                  '">'.$row_select['task_status_descr'].
                  '</span>';
                }
              }
              
              ?>
              
              
              
                 
            </div>
          </div>
          <div class="form-group row">
            <label for="subject" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Εργασία');?>:</label>
            <div class="col-md-8">
              <input id="subject" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['subject']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="message" class="col-md-12 col-form-label form-control-sm text-md-right1"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-md-12">
              <textarea id="message" type="text" class="gks_tinymce form-control form-control-sm myneedsave" style="min-height:100px;height:100px;" ><?php echo htmlspecialchars_gks($row['message']);?></textarea>
            </div>
          </div>          

          <div class="form-group row">
            <label for="task_planned_date_from" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προγραμματισμός');?>:</label>
            <div class="col-md-8">
              <?php
              $duration='';
              if (isset($row['task_planned_date_from']) and isset($row['task_planned_date_to'])) {
                $temp=strtotime($row['task_planned_date_to'])-strtotime($row['task_planned_date_from']);
                if ($temp < 24*60*60) { //kat apo mia 1 imera
                  $duration=date('H:i',$temp);    
                }
              }
              ?>
              <span style="font-size: 0.875rem;"><?php echo gks_lang('Από');?>: </span>
                <input id="task_planned_date_from" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['task_planned_date_from'])) echo  showDate(strtotime($row['task_planned_date_from']), 'd/m/Y H:i', 1);?>" style="max-width:200px;display: inline-block;margin-bottom: 6px;" placeholder="" >
              <br>
              <span style="font-size: 0.875rem;"><?php echo gks_lang('Διάρκεια');?>: </span>
                <input id="task_planned_date_duration" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $duration;?>" autocomplete="off" style="max-width:169px;display: inline-block;margin-bottom: 6px;" placeholder=""">
              <br>
              <span style="font-size: 0.875rem;"><?php echo gks_lang('Έως');?>: </span>
                <input id="task_planned_date_to" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['task_planned_date_to'])) echo  showDate(strtotime($row['task_planned_date_to']), 'd/m/Y H:i', 1);?>" style="max-width:200px;display: inline-block;" placeholder="">
              
              
            </div>
          </div> 

                    
          <div class="form-group row">
            <label for="esoda" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αναμενόμενα έσοδα');?>:</label>
            <div class="col-md-8">
              <input id="esoda" type="number" class="form-control form-control-sm myneedsave" value="<?php echo number_format($row['esoda'],$GKS_NUMBER_FORMAT_CURRENCY_DECIMAL,'.','');?>" style="max-width:100px;display: inline-block;" placeholder="" min="0" step="50">
            </div>
          </div> 
                               
          <div class="form-group row">
            <label for="task_color" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χρώμα');?>:</label>
            <div class="col-md-8">
              <input id="task_color" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['task_color']);?>" style="max-width:200px;">
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
            <label for="crm_channel_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κανάλι πωλήσεων');?>:</label>
            <div class="col-md-8">
              <select id="crm_channel_id" class="form-control form-control-sm myneedsave" >
                <option value="0" data-contact="0" data-contact_filter="" data-campain="0" data-url="0" data-code="0" data-text="0"></option>
                <?php
                $sql_channel_sale="SELECT *
                FROM gks_crm_channel_sale
                WHERE crm_channel_sale_disabled=0
                ORDER BY crm_channel_sale_sortorder";
                $result_channel_sale = $db_link->query($sql_channel_sale);        
                if (!$result_channel_sale) {
                  debug_mail(false,'error sql',$sql_channel_sale);
                  die('sql error');
                }
                $row_channel_sale_selected=array(
                  'crm_channel_has_contact'=>0,
                  'crm_channel_has_contact_filter'=>'',
                  'crm_channel_has_campain'=>0,
                  'crm_channel_has_url'=>0,
                  'crm_channel_has_code'=>0,
                  'crm_channel_has_text'=>0,
                );
                
                while ($row_channel_sale = $result_channel_sale->fetch_assoc()) {
                  echo '<option value="'.$row_channel_sale['id_crm_channel_sale'].'" '.
                  'data-contact="'.intval($row_channel_sale['crm_channel_has_contact']).'" '.
                  'data-contact_filter="'.base64_encode(trim_gks($row_channel_sale['crm_channel_has_contact_filter'])).'" '.
                  'data-campain="'.intval($row_channel_sale['crm_channel_has_campain']).'" '.
                  'data-url="'.intval($row_channel_sale['crm_channel_has_url']).'" '.
                  'data-code="'.intval($row_channel_sale['crm_channel_has_code']).'" '.
                  'data-text="'.intval($row_channel_sale['crm_channel_has_text']).'" ';
                  if ($row_channel_sale['id_crm_channel_sale']==$row['crm_channel_id']) {
                    echo ' selected ';
                    $row_channel_sale_selected=$row_channel_sale;
                  }
                  echo '>'.$row_channel_sale['crm_channel_sale_descr'].'</option>';
                }?>
              </select>
            </div>
          </div>

          <div class="form-group row" id="crm_channel_contact_id_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_contact']==0) echo 'display:none;';?>">
            <label for="crm_channel_contact_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επαφή Πωλήσεων');?>:</label>
            <div class="col-md-8">
              <input id="crm_channel_contact_id" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['crm_channel_contact_gks_nickname']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['crm_channel_contact_id'];?>">
            </div>
          </div>


          <div class="form-group row" id="crm_channel_campain_id_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_campain']==0) echo 'display:none;';?>">
            <label for="crm_channel_campain_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Καμπάνια');?>:</label>
            <div class="col-md-8">
              <input id="crm_channel_campain_id" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['ads_campain_name']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" data-id="<?php echo $row['crm_channel_campain_id'];?>">
            </div>
          </div>

          <div class="form-group row" id="crm_channel_url_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_url']==0) echo 'display:none;';?>">
            <label for="crm_channel_url" class="col-md-4 col-form-label form-control-sm text-md-right">URL:</label>
            <div class="col-md-8">
              <input id="crm_channel_url" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['crm_channel_url']);?>">
            </div>
          </div>
          <div class="form-group row" id="crm_channel_code_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_code']==0) echo 'display:none;';?>">
            <label for="crm_channel_code" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κωδικός');?>:</label>
            <div class="col-md-8">
              <input id="crm_channel_code" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['crm_channel_code']);?>">
            </div>
          </div>          
          <div class="form-group row" id="crm_channel_text_div" style="<?php if ($row_channel_sale_selected['crm_channel_has_text']==0) echo 'display:none;';?>">
            <label for="crm_channel_text" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο');?>:</label>
            <div class="col-md-8">
              <textarea id="crm_channel_text" type="text" class="form-control form-control-sm myneedsave" style="min-height:100px;height:100px;"><?php echo htmlspecialchars_gks($row['crm_channel_text']);?></textarea>
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
      
<?php if ($GKS_CRM_MACHINE_ENABLE) {?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Συσκευές');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('machine');?>>        

          <?php
          
          if ($id==-1 and isset($_GET['crm_task_machine_id']) and intval($_GET['crm_task_machine_id'])>0) {
            $query="SELECT -1 as id_crm_task_machine, id_crm_machine as crm_task_machine_id, 
            crm_machine_name,crm_machine_serial_number
            from gks_crm_machine
            where id_crm_machine=".intval($_GET['crm_task_machine_id']);
            //echo $query;die();
          } else {
            $query = "SELECT ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
            gks_crm_tasks_machine.*, 
            gks_crm_machine.id_crm_machine,
            gks_crm_machine.crm_machine_name, gks_crm_machine.crm_machine_serial_number
            FROM (gks_crm_tasks_machine 
            LEFT JOIN gks_crm_machine ON gks_crm_tasks_machine.crm_task_machine_id = gks_crm_machine.id_crm_machine) 
            LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_crm_tasks_machine.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
            WHERE gks_crm_tasks_machine.crm_task_id=".$id."
            ORDER BY gks_crm_tasks_machine.id_crm_task_machine;";
          }
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table id="crm_tasks_machine_table" class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="50%"><?php echo gks_lang('Συσκευή');?></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="25%"><?php echo gks_lang('Ημερομηνία');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="25%"><?php echo gks_lang('Από');?></th>        
            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="machine_tr" data-id="<?php echo $row_list['crm_task_machine_id'];?>">
              <th scope="row" nowrap align="right" class="mytdcm machine_aa"><?php echo ($i);?></td>       
              <td nowrap class="mytdcm">
                <i class="fas fa-trash-alt machine_tr_delete" data-id="<?php echo $row_list['crm_task_machine_id'];?>" style="font-size:120%;color:#dc3545;cursor:pointer;"></i>
              </td>
              <td><?php 
                //echo getUserPhoto($row_list['crm_task_employee_id'],$row_list['gks_wsl_current_user_image'],32);
              ?></td>
              <td class="mytdcml"><?php 
                echo '<a href="admin-crm-machine-item.php?id='.$row_list['crm_task_machine_id'].'">'.$row_list['crm_machine_name'].
                (trim_gks($row_list['crm_machine_serial_number'])!='' ? ' ('.trim_gks($row_list['crm_machine_serial_number']).')' : '').
                '</a>';
                ?></td>  
              <td class="mytdcm" nowrap><?php if (isset($row_list['mydate_add'])) echo showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1);?></td>
              <td class="mytdcml"><?php if (isset($row_list['user_id_edit'])) echo '<a href="admin-users-item.php?id='.$row_list['user_id_edit'].'">'.$row_list['gks_nickname_edit'].'</a>';?></td>  
            </tr>
          <?php } ?>



     
          </tbody>
          <tfoot>
            <tr class="">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <input type="text" name="machine" id="machine" data-id="0" class="form-control form-control-sm" style="width:calc(100% - 110px);display: inline-block;margin-right: 10px;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                <button style="justify-content: center!important;vertical-align: baseline;" type="button" class="btn btn-sm btn-primary" id="add_machine"
                  ><?php echo gks_lang('Προσθήκη');?></button>
              </td>  
            </tr>            
          </tfoot>
          </table>      

        </div>
      </div>        
<?php } ?>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Υπάλληλοι');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('employee');?>>        

          <?php
          $query = "SELECT ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
          gks_crm_tasks_employee.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
          ".GKS_WP_TABLE_PREFIX."users.gks_wsl_current_user_image
          FROM (gks_crm_tasks_employee 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_tasks_employee.crm_task_employee_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_crm_tasks_employee.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
          WHERE gks_crm_tasks_employee.crm_task_id=".$id."
          ORDER BY gks_crm_tasks_employee.id_crm_task_employee;";

          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table id="crm_tasks_employee_table" class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="50%"><?php echo gks_lang('Υπάλληλος');?></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="25%"><?php echo gks_lang('Ημερομηνία');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="25%"><?php echo gks_lang('Από');?></th>        
            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="employee_tr" data-id="<?php echo $row_list['crm_task_employee_id'];?>">
              <th scope="row" nowrap align="right" class="mytdcm employee_aa"><?php echo ($i);?></td>       
              <td nowrap class="mytdcm">
                <i class="fas fa-trash-alt employee_tr_delete" data-id="<?php echo $row_list['crm_task_employee_id'];?>" style="font-size:120%;color:#dc3545;cursor:pointer;"></i>
              </td>
              <td class="mytdcm p-0"><?php echo getUserPhoto($row_list['crm_task_employee_id'],$row_list['gks_wsl_current_user_image'],32);?></td>
              <td class="mytdcml"><?php 
                echo '<a href="admin-users-item.php?id='.$row_list['crm_task_employee_id'].'">'.$row_list['gks_nickname'].'</a>';?></td>  
              <td class="mytdcm" nowrap><?php echo showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1);?></td>
              <td class="mytdcml"><?php echo '<a href="admin-users-item.php?id='.$row_list['user_id_edit'].'">'.$row_list['gks_nickname_edit'].'</a>';?></td>  
            </tr>
          <?php } ?>
            </tbody>
            <tfoot>
            <tr class="">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <input type="text" name="employee" id="employee" data-id="0" data-photo="" class="form-control form-control-sm" style="width:calc(100% - 110px);display: inline-block;margin-right: 10px;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                <button style="justify-content: center!important;vertical-align: baseline;" type="button" class="btn btn-sm btn-primary" id="add_employee"
                  ><?php echo gks_lang('Προσθήκη');?></button>
              </td>  
            </tr>
            </tfoot>
          </tbody>
          </table>      

        </div>
      </div>        

              
              
    
      
    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Επαφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('pel');?>>      

          <div class="form-group row">
            <label for="" class="col-md-4 col-form-label form-control-sm text-sm-right"><a class="tooltipster" title="<?php echo gks_lang('Αναζήτηση Βασικών Στοιχείων Μητρώου Επιχειρήσεων');?>" href="https://www.aade.gr/epiheiriseis/forologikes-ypiresies/mitroo/anazitisi-basikon-stoiheion-mitrooy-epiheiriseon" target="_blank">aade.gr</a>:</label>
            <div class="col-md-8">
              <button style="" id="btn_gsis_get" class="btn btn-sm btn-primary"><?php echo gks_lang('Αναζήτηση με το ΑΦΜ');?></button>
            </div>
          </div>
                    
          <div class="form-group row">
            <label for="user" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επαφή');?>:</label>
            <div class="col-md-8">
              <input id="user" type="text" class="form-control form-control-sm myneedsave email_contact_name" 
              value="<?php echo htmlspecialchars_gks($row['gks_nickname']);?>" 
              style="width:calc(98% - 22px);display:inline;" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
              <input id="user_id" type="hidden" value="<?php echo $row['user_id'];?>" class="myneedsave">
              <a id="autocomplete_user_id" tabindex="-1" href="admin-users-item.php?id=<?php echo $row['user_id'];?>" style="<?php if ($row['user_id']==0) echo 'display:none';?>"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή επαφής');?>"></i></a>
              <i id="user_save" class="fas fa-save" style="<?php if ($row['user_id']>0) echo 'display:none';?>;color: #35dc35;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Δημιουργία επαφής');?>"></i>
                  
            </div>
          </div>          

          <div class="form-group row" style="margin-bottom: 0px;">
            <div class="col-sm-6">
              <div class="form-group1 row" id="div_pelati_sxolio" style="<?php echo (trim_gks($row['pelati_sxolio'])=='' ? 'display:none;' : '');?>;margin-bottom: 1rem;padding-right: 15px;padding-left: 30px;">
                <div class="col-sm-12 alert alert-danger" role="alert" id="text_pelati_sxolio" style="margin-bottom: 0px;"><?php echo nl2br_gks($row['pelati_sxolio']);?></div>
                <div style="text-align:right;width:100%;margin-bottom: 10px;">
                  <i id="copy_text_pelati_sxolio_to_logistirio" class="far fa-copy tooltipster" style="font-size:120%;vertical-align:middle;color:blue;cursor:pointer;" title="<?php echo gks_lang('Αντιγραφή του κειμένου στο πεδίο <b>Εσωτερική σημείωση</b>');?>"></i>
                </div>
              </div>
                            
            </div>
            
            <div class="col-sm-6">
              <div class="form-group1 row" id="div_order_sxolio" style="<?php echo (trim_gks($row['order_sxolio'])=='' ? 'display:none;' : '');?>;margin-bottom: 1rem;padding-right: 15px;padding-left: 30px;">
                <div class="col-sm-12 alert alert-danger" role="alert" id="text_order_sxolio" style="margin-bottom: 0px;"><?php echo nl2br_gks($row['order_sxolio']);?></div>
                <div style="text-align:right;width:100%;margin-bottom: 10px;">
                  <i id="copy_text_order_sxolio_to_logistirio" class="far fa-copy tooltipster" style="font-size:120%;vertical-align:middle;color:blue;cursor:pointer;" title="<?php echo gks_lang('Αντιγραφή του κειμένου στο πεδίο <b>Εσωτερική σημείωση</b>');?>"></i>
                </div>
              </div>               
            </div>
          </div>
          
                    
          <div class="form-group row">
            <label for="first_name" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όνομα');?>:</label>
            <div class="col-md-8">
              <input id="first_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['first_name']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="last_name" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επώνυμο');?>:</label>
            <div class="col-md-8">
              <input id="last_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['last_name']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="email" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ηλ. διεύθυνση');?>:</label>
            <div class="col-md-8">
              <input id="email" type="email" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['email']);?>">
            </div>
          </div>           
          <div class="form-group row">
            <label for="mobile" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Κινητό');?>:</label>
            <div class="col-md-8">
              <input id="mobile" type="tel" class="form-control form-control-sm myneedsave <?php echo $gks_voip_params['class_input'];?>" value="<?php echo htmlspecialchars_gks($row['mobile']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              <?php echo $gks_voip_params['html_after_input'];?>
            </div>
          </div> 
          <div class="form-group row">
            <label for="phone" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σταθερό Τηλέφωνο');?>:</label>
            <div class="col-md-8">
              <input id="phone" type="tel" class="form-control form-control-sm myneedsave <?php echo $gks_voip_params['class_input'];?>" value="<?php echo htmlspecialchars_gks($row['phone']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>">
              <?php echo $gks_voip_params['html_after_input'];?>
            </div>
          </div> 
          <div class="form-group row">
            <label for="web" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ιστότοπος');?>:</label>
            <div class="col-md-8">
              <input id="web" type="url" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['web']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="user_lang" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γλώσσα');?>:</label>
            <div class="col-md-8">
              <select id="user_lang" class="form-control form-control-sm myneedsave">
                <option value=""></option>
                <?php
                $lang_prepare_gks_lang=gks_lang_data_obj_prepare('gks_lang','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_lang, array('lang_name'));
                $sql="select id_lang,lang_ico,".gks_lang_sql_field('lang_name',$lang_prepare_gks_lang)." 
                FROM ".$lang_prepare_gks_lang['sql']['from1']." gks_lang 
                ".$lang_prepare_gks_lang['sql']['from2']."
                ORDER BY lang_sortorder,lang_name";                
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_lang'].'" ';
                  if ($row['user_lang'] == $row_select['id_lang']) echo ' selected ';
                  echo '>'.$row_select['lang_name'].'</option>';
                }
                ?>
              </select>                  
            </div>
          </div>

          <div class="form-group row">
            <label for="birthday" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημερ. Γέννησης');?>:</label>
            <div class="col-md-8">
              <input id="birthday" type="text" class="form-control form-control-sm myneedsave" value="<?php if (isset($row['birthday'])) echo  date('d/m/Y',strtotime($row['birthday']));?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" style="max-width:200px">
            </div>
          </div>
        </div>
      </div>


      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Διεύθυνση');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('addr');?>>      

          <div class="form-group row">
            <label for="form_select_apostoli" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τόπος');?>:</label>
            <div class="col-md-8">
                <select id="form_select_apostoli" class="form-control form-control-sm myneedsave">
                  <option value="-1" <?php echo ($row['address_extra']==-1 ? ' selected ' : '');?>><?php echo gks_lang('Βασική διεύθυνση');?></option>
                  <?php
                  $sql="SELECT gks_users_extra_address.*, country_name,nomos_descr
                  FROM (gks_users_extra_address 
                  LEFT JOIN gks_country ON gks_users_extra_address.ea_country_id = gks_country.id_country) 
                  LEFT JOIN gks_nomoi ON gks_users_extra_address.ea_nomos_id = gks_nomoi.id_nomos
                  WHERE (gks_users_extra_address.user_id=".$row['user_id']." and gks_users_extra_address.user_id>0)
                  or (gks_users_extra_address.crm_task_id=".$id.")
                  
                  ORDER BY gks_users_extra_address.id_users_extra_address";
                  $result_select = $db_link->query($sql);        
                  if (!$result_select) {
                    debug_mail(false,'error sql',$sql);
                    die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
                  }
                  $selected_ea=array();
                  $selected_ea['ea_name']='';
                  $selected_ea['ea_phone']='';
                  $selected_ea['ea_odos']='';
                  $selected_ea['ea_arithmos']='';
                  $selected_ea['ea_orofos']='';
                  $selected_ea['ea_perioxi']='';
                  $selected_ea['ea_poli']='';
                  $selected_ea['ea_tk']='';
                  $selected_ea['ea_country_id']=0;
                  $selected_ea['ea_nomos_id']=0;
                  
                  
                  while ($row_select = $result_select->fetch_assoc()) {
                    $row_select['country_name']=gks_lang_data_trans($row_select['country_name'],$row_select['ea_country_id'],'gks_country','country_name');
                    $row_select['nomos_descr']=gks_lang_data_trans($row_select['nomos_descr'],$row_select['ea_nomos_id'],'gks_nomoi','nomos_descr');

                    $address_name=$row_select['ea_name'].', '.trim_gks($row_select['ea_odos'].' '.$row_select['ea_arithmos']).', '.$row_select['ea_orofos'].', '.$row_select['ea_perioxi'].', '.$row_select['ea_poli'].', '.$row_select['ea_tk'].', '.$row_select['country_name'].', '.$row_select['nomos_descr'];
                  
                    $address_name=str_replace(', , ', ', ', $address_name);
                    $address_name=str_replace(', , ', ', ', $address_name);
                    $address_name=str_replace(', , ', ', ', $address_name);
                    $address_name=str_replace(', , ', ', ', $address_name);
                    
                    if (substr($address_name, 0,2)==', ') $address_name=substr($address_name,2);
                    if (substr($address_name, 0,2)==', ') $address_name=substr($address_name,2);
                    if (substr($address_name, 0,2)==', ') $address_name=substr($address_name,2);
                  
                    if (substr($address_name, strlen($address_name) -2 ,2)==', ') $address_name=substr($address_name,0, strlen($address_name) - 2);
                    if (substr($address_name, strlen($address_name) -2 ,2)==', ') $address_name=substr($address_name,0, strlen($address_name) - 2);
                    if (substr($address_name, strlen($address_name) -2 ,2)==', ') $address_name=substr($address_name,0, strlen($address_name) - 2);
                    
                    
                    echo '<option value="'.$row_select['id_users_extra_address'].'" ';
                    if ($row['address_extra'] == $row_select['id_users_extra_address']) {
                      echo ' selected ';
                      $selected_ea=$row_select;
                    }
                    echo '>'.$address_name.'</option>';
                  }
                  if ($row['user_id']>0) {
                    echo '<option value="0" '.($row['address_extra']==0 ? ' selected ' : '').'>-- '.gks_lang('Δημιουργία νέας διεύθυνσης').' --</option>';
                  }?>
                  
                </select>              
              
            </div>
          </div>
                   
          <div class="form-group row" id="form_ea_name_div" style="<?php if ($row['address_extra']<=0) echo 'display:none;';?>">
            <label for="form_ea_name" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όνομα');?>:</label>
            <div class="col-md-8">
              <input id="form_ea_name" type="text" class="form-control form-control-sm myneedsave" value="<?php if ($row['address_extra']>0) echo htmlspecialchars_gks($selected_ea['ea_name']);?>">
            </div>
          </div> 
          <div class="form-group row" id="form_ea_phone_div" style="<?php if ($row['address_extra']<=0) echo 'display:none;';?>">
            <label for="form_ea_phone" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τηλέφωνο');?>:</label>
            <div class="col-md-8">
              <input id="form_ea_phone" type="text" class="form-control form-control-sm myneedsave" value="<?php if ($row['address_extra']>0) echo htmlspecialchars_gks($selected_ea['ea_phone']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="odos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Οδός');?>:</label>
            <div class="col-md-8">
              <input id="odos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['odos']);?>">
              <small class="form-text text-muted auto_googlemaps" id="odos_auto_googlemaps"></small>
            </div>
          </div> 
          <div class="form-group row">
            <label for="arithmos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός');?>:</label>
            <div class="col-md-8">
              <input id="arithmos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['arithmos']);?>">
            </div>
          </div> 
          
          <div class="form-group row">
            <label for="orofos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όροφος');?>:</label>
            <div class="col-md-8">
              <input id="orofos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['orofos']);?>">
            </div>
          </div>           
          <div class="form-group row">
            <label for="perioxi" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιοχή');?>:</label>
            <div class="col-md-8">
              <input id="perioxi" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['perioxi']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="poli" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πόλη');?>:</label>
            <div class="col-md-8">
              <input id="poli" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['poli']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="tk" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΤΚ');?>:</label>
            <div class="col-md-8">
              <input id="tk" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['tk']);?>">
            </div>
          </div>

          <div class="form-group row">
            <label for="country_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χώρα');?>:</label>
            <div class="col-md-8">
              <select id="country_id" class="form-control form-control-sm myneedsave">
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
                if ($row_select['id_country']==$row['country_id']) {echo ' selected '; $ee_initials=trim_gks($row_select['country_ee']);}
                  echo '>'.$row_select['country_name'].'</option>';
                }?>
              </select>    
            </div>
          </div> 
          <div class="form-group row">
            <label for="nomos_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Νομός');?>:</label>
            <div class="col-md-8">
              <select id="nomos_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $lang_prepare_gks_nomos=gks_lang_data_obj_prepare('gks_nomoi','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomos, array('nomos_descr'));
                $sql="select id_nomos,".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomos)." 
                FROM ".$lang_prepare_gks_nomos['sql']['from1']." gks_nomoi 
                ".$lang_prepare_gks_nomos['sql']['from2']."
                where country_id=".$row['country_id']." ORDER BY nomos_descr";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'admin-users-item.php error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_nomos'].'" ';
                  if ($row_select['id_nomos']==$row['nomos_id']) echo ' selected '; 
                  echo '>'.$row_select['nomos_descr'].'</option>';
                }?>
              </select>    
            </div>
          </div>    
          

          <div class="form-group row">
            <label for="map_latitude" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γεωγραφικό Πλάτος');?>:</label>
            <div class="col-md-8">
              <input id="map_latitude" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['map_latitude']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="-90" max="90" step="0.00001">
            </div>
          </div> 
          <div class="form-group row">
            <label for="map_longitude" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γεωγραφικό Μήκος');?>:</label>
            <div class="col-md-8">
              <input id="map_longitude" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['map_longitude']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="-180" max="180" step="0.00001">
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
          <?php echo gks_lang('Φορολογικά στοιχεία');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('for');?>>      

          <div class="form-group row">
            <label for="eponimia" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επωνυμία');?>:</label>
            <div class="col-md-8">
              <input id="eponimia" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['eponimia']);?>" >
            </div>
          </div>
          <div class="form-group row">
            <label for="title" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τίτλος');?>:</label>
            <div class="col-md-8">
              <input id="title" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['title']);?>" >
            </div>
          </div>

          <div class="form-group row">
            <label for="afm" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΑΦΜ');?>:</label>
            <div class="col-md-8">

              <span id="dr_user_afm_ee_initials" style="<?php echo ($ee_initials!='' ? '' : 'display:none;');?>"><?php echo $ee_initials;?></span><input 
                style="display: inline-block;max-width:100%;text-align:left;vertical-align: middle;<?php echo ($ee_initials=='' ? 'width:100%;' : 'width:calc(100% - 75px);');?>"
                id="afm" type="text" class="form-control form-control-sm myneedsave <?php echo ($ee_initials=='' ? '':'dr_user_afm_views');?>" value="<?php echo htmlspecialchars_gks($row['afm']);?>" ><span 
                id="dr_user_afm_views_run" style="<?php echo ($check_vies['run'] ? '' : 'display:none;');?>"><?php echo $check_vies['views_run_img'];?></span>


            </div>
          </div>
          <div class="form-group row">
            <label for="doy" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ΔΟΥ');?>:</label>
            <div class="col-md-8">
              <input id="doy" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['doy']);?>" >
            </div>
          </div>
          
          <div class="form-group row">
            <label for="epaggelma" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επάγγελμα');?>:</label>
            <div class="col-md-8">
              <input id="epaggelma" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['epaggelma']);?>" >
            </div>
          </div>          
          
          

          <div class="form-group row">
            <label for="fiscal_position_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Φορολογική Θέση');?>:</label>
            <div class="col-md-8">
              <select id="fiscal_position_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $lang_prepare_gks_eshop_fiscal_position=gks_lang_data_obj_prepare('gks_eshop_fiscal_position','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_eshop_fiscal_position, array('fiscal_position_descr'));
                $sql="select id_fiscal_position,".gks_lang_sql_field('fiscal_position_descr',$lang_prepare_gks_eshop_fiscal_position)." 
                FROM ".$lang_prepare_gks_eshop_fiscal_position['sql']['from1']." gks_eshop_fiscal_position 
                ".$lang_prepare_gks_eshop_fiscal_position['sql']['from2']."
                where fiscal_position_disable=0 
                order by fiscal_position_sortorder,fiscal_position_descr";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'admin-users-item.php error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_fiscal_position'].'" ';
                  if ($row_select['id_fiscal_position']==$row['fiscal_position_id']) echo ' selected ';
                  echo '>'.$row_select['fiscal_position_descr'].'</option>';
                }?>
              </select>    
            </div>
          </div> 
          <div class="form-group row">
            <label for="pricelist_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τιμοκατάλογος');?>:</label>
            <div class="col-md-8">
              <select id="pricelist_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $lang_prepare_gks_eshop_pricelist=gks_lang_data_obj_prepare('gks_eshop_pricelist','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_eshop_pricelist, array('pricelist_descr'));
                $sql="select id_pricelist,".gks_lang_sql_field('pricelist_descr',$lang_prepare_gks_eshop_pricelist)." 
                FROM ".$lang_prepare_gks_eshop_pricelist['sql']['from1']." gks_eshop_pricelist 
                ".$lang_prepare_gks_eshop_pricelist['sql']['from2']."
                where pricelist_disable=0 
                order by sortorder,pricelist_descr";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'admin-users-item.php error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_pricelist'].'" ';
                  if ($row_select['id_pricelist']==$row['pricelist_id']) echo ' selected ';
                  echo '>'.$row_select['pricelist_descr'].'</option>';
                }?>
              </select>    
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
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_crm_task'];?>" data-model="gks_crm_tasks" data-backurl="admin-crm-task.php" <?php if ($id<=0) echo 'disabled';?>><?php echo gks_lang('Διαγραφή');?></button>
      <button type="button" class="btn btn-dark" id="submit_button_print" <?php if ($id<0) echo 'disabled';?>><?php echo gks_lang('Εκτύπωση');?> <i class="fas fa-print" style="color: #35dc35;font-size: 120%;"></i></button>
      <div style="display:inline-block;width:38px;height:38px;vertical-align:top;">
        <div style="border:1px solid gray;padding: 7px 0px 5px 0px;;border-radius:4px;background-color:#343a40;display:none;" id="calc_hourglass">
          <i class="fas fa-hourglass-half" style="color:coral;font-size:120%;"></i>
        </div> 
      </div>
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-xl-6">

      <?php echo getObjectRels('gks_crm_tasks',$id); ?>
      
      <?php echo getActivityObjectTable('gks_crm_tasks',$id); ?>

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
            $sql_msg="SELECT gks_crm_tasks_messages.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
            FROM gks_crm_tasks_messages LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_tasks_messages.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
            WHERE gks_crm_tasks_messages.crm_tasks_id=".$id."
            ORDER BY gks_crm_tasks_messages.mydate_add DESC, gks_crm_tasks_messages.id_crm_tasks_message DESC;";
            $result_msg = $db_link->query($sql_msg);        
            if (!$result_msg) debug_mail(false,'error sql',$sql_msg);
            if (!$result_msg) die('sql error');
            
            $j = 0;
            while ($row_msg = $result_msg->fetch_assoc()) {
              $j++; ?>
          
            
            <tr id="tr_messages_<?php echo $row_msg['id_crm_tasks_message'];?>">
              <th scope="row" class="mytdcm message_aa"><?php echo $j;?></th>
              <td class="mytdcml"><?php echo showDate(strtotime($row_msg['mydate_add']), 'd/m/Y H:i', 1);?></td>  
              <td class="mytdcml"><?php echo $row_msg['gks_nickname'];?></td>  
              <td class="mytdcml"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
                echo str_replace('[[-r]]', '<i class="fas fa-arrow-alt-circle-right gksvm" style=""></i>', $row_msg['crm_tasks_message']);
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
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σύνδεσμοι');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('links');?>><?php

          
          
          $query = "SELECT gks_crm_tasks_links.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
          FROM gks_crm_tasks_links LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_tasks_links.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
          WHERE gks_crm_tasks_links.crm_task_id in (".$id.")
          ORDER BY gks_crm_tasks_links.mydate, gks_crm_tasks_links.id_crm_tasks_links;";
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center" id="links_table">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo gks_lang('Χρήστης');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"><?php echo gks_lang('Προσθήκη');?></th>        
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="100%"><?php echo gks_lang('Σύνδεσμος');?></th>        
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><?php echo gks_lang('Μέγεθος');?></th>        
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%"><i class="fas fa-exclamation-circle" style="font-size:120%;vertical-align:middle;"></i></th>        

            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          $need_download_timer=0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr id="tr_links_url_<?php echo $row_list['id_crm_tasks_links'];?>">
              <th scope="row" nowrap align="right" class="links_aa"><?php echo ($i);?></td>       
              <td nowrap align="center">
                <i class="fas fa-trash-alt deleterow" data-id="<?php echo $row_list['id_crm_tasks_links'];?>" data-deleteafter="gks_fnc_links_delete_after|<?php echo $row_list['id_crm_tasks_links'];?>" data-model="gks_crm_tasks_links" style="font-size:120%;color:#dc3545;cursor:pointer;"></i>

              </td>
              <td nowrap><?php echo '<a href="admin-users-item.php?id='.$row_list['user_id'].'">'.$row_list['gks_nickname'].'</a>';?></td>  
              <td nowrap><?php echo showDate(strtotime($row_list['mydate']), 'd/m/Y H:i', 1);?></td>   
              <td       style="word-break: break-all;">
                <div><?php 
                $temp=trim_gks($row_list['url']);
                if ($temp!='' and startwith($temp,'http')) {
                  $temp='<a href="'.$temp.'" target="_blank">'.(strlen($temp)>80 ? substr($temp, 0,50).'...' : $temp).'</a>';
                  echo $temp;
                } else {
                  echo (strlen($temp)>80 ? substr($temp, 0,50).'...' : $temp);
                }
                ?></div>
                <div class="progress download-perc" data-id="<?php echo $row_list['id_crm_tasks_links'];?>" 
                  style="<?php echo ($row_list['download_status']==1 ? '' : 'display:none;');?>">
                  <div class="download-perc-bar progress-bar progress-bar-striped" 
                    data-id="<?php echo $row_list['id_crm_tasks_links'];?>" role="progressbar" 
                    style="width:0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>    
                <div class="download-message" 
                  data-id="<?php echo $row_list['id_crm_tasks_links'];?>" 
                  style="<?php echo ($row_list['download_status']==3 ? '' : 'display:none;');?>"
                  ><?php echo $row_list['download_message'];?></div>
                
              </td>
              <td nowrap class="download_size_until_now" data-id="<?php echo $row_list['id_crm_tasks_links'];?>" style="text-align:right;vertical-align:middle;"><?php if ($row_list['download_size_until_now']>0) echo number_format($row_list['download_size_until_now']/1024/1024,2,$GKS_NUMBER_FORMAT_DECIMAL, $GKS_NUMBER_FORMAT_THOUSAND).'MB';?></td>  
              <td nowrap class="download_file_td" data-id="<?php echo $row_list['id_crm_tasks_links'];?>" style="text-align:center;vertical-align: middle;"><?php
              
              
              // 0 notdownload
              // 1 downloding
              // 2 complete
              // 3 abort
              
              if ($row_list['download_status']==0) { //notdownload
                echo '<i class="fas fa-file-download download_action_start" data-id="'.$row_list['id_crm_tasks_links'].'" style="font-size:200%;vertical-align:middle;color:blue;cursor:pointer;"></i>';
              } else if ($row_list['download_status']==1) { //downloding
                $need_download_timer=1;
                echo '<i class="fas fa-stop-circle download_action_stop" data-id="'.$row_list['id_crm_tasks_links'].'" style="font-size:200%;vertical-align:middle;color:red;cursor:pointer;"></i>';
              } else if ($row_list['download_status']==2) { //complete
                echo '<i class="fas fa-check-circle download_action_complete" data-id="'.$row_list['id_crm_tasks_links'].'" style="font-size:200%;vertical-align:middle;color:green;cursor:pointer;"></i>';
              } else if ($row_list['download_status']==3) { //abort
                echo '<i class="fas fa-undo download_action_reset" data-id="'.$row_list['id_crm_tasks_links'].'" style="font-size:200%;vertical-align:middle;color:black;cursor:pointer;"></i>';
              } 
                
              ?></td>  
            </tr>
          <?php } ?>


            <tr class="" id="tr_new_links_url">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <input type="text"   name="links_url"    id="links_url"   class="form-control" style="width:98%;" placeholder="<?php echo gks_lang('π.χ.');?> https://we.tl/...">
              </td>  
            </tr>
            <tr class="" id="tr_new_links_url_button">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center"></td>
              <td nowrap colspan="5">
                <button style="justify-content: center!important;" type="button" class="btn btn-sm btn-primary" id="add_links_url"><?php echo gks_lang('Προσθήκη');?></button>
              </td>  
            </tr>      
          </tbody>
          </table>                       
        </div>
      </div>
              



			<?php
			$obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_crm_tasks','id'=>$id));
      echo $obj_fileslist['html'];
      ?>
        
      

    </div>
    <div class="col-xl-6">
      <?php 
      
      if (trim_gks($row['print_date'])!='' or 
          trim_gks($row['print_file_name']) != '' or 
          trim_gks($row['print_file_url']) != '' or 
          $row['print_user_id']>0 or 
          trim_gks($row['print_crm_task_status']) != '') {?>
      
      <div class="card gks_card_expand gks_card_name_print" id="gks_print">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Εκτύπωση');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('print');?>>       
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Ημερομηνία');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['print_date'])) echo showDate(strtotime($row['print_date']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Εκτύπωση από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-users-item.php?id='.$row['print_user_id'].'">'.$row['gks_nickname_print'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Κατάσταση όταν έγινε η εκτύπωση');?>:</label>
            <div class="col-sm-8"><span class="task_status_<?php echo $row['print_crm_task_status_id'];?>"><?php echo $row['print_crm_task_status'];?></span></div>
          </div>

          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αρχείο');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm" style="height: auto;"><?php 
              if (trim_gks($row['print_file_name'])!='') {
                $local_file=GKS_FileServerShare.'crm/task/'.$id.'/print/'.$row['print_file_name'];
                if (file_exists($local_file)) {
                  //print_file_url
                  $url_file='admin-get-file.php?fs=fileservers&file=crm%2Ftask%2F'.$id.'%2Fprint%2F'.urlencode($row['print_file_name']);
                  echo '<a href="'.$url_file.'" target="_blank" id="last_print_file">'.$row['print_file_name'].'</a> ';
                  echo '<a href="'.$url_file.'&download=1" target="_blank"><i class="fas fa-download" style="color:blue;"></i></a>';
                }
              }
              ?></span></div>
          </div>

        </div>      
      </div>              
      <?php } ?>
      
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
            $sql_log="SELECT gks_crm_tasks_log.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
            FROM gks_crm_tasks_log LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_crm_tasks_log.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
            WHERE gks_crm_tasks_log.crm_task_id=".$id."
            ORDER BY gks_crm_tasks_log.id_gks_crm_tasks_log DESC;";
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
              <td align="left"><?php echo str_replace('[[-r]]', '<i class="fas fa-arrow-alt-circle-right gksvm" style=""></i>', $row_log['sxolio']);?></td>    
            </tr>
            <?php } ?>                      
            </tbody>   
          </table>



        </div>
      </div>
      
      
      <?php if ($row['form_id']!=0 or 
                empty($row['form_name'])==false or 
                $row['post_id']!=0 or
                empty($row['post_name'])==false or
                empty($row['source_url'])==false or 
                empty($row['user_agent'])==false
      ) { ?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          Metadata
        </div>
        <div class="card-body" <?php echo gks_card_body('met');?>>   
             

          <?php if ($row['form_id']!=0) { ?>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right">form_id:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm" style="height:unset;"><?php echo '<a target="_blank" href="/wp-admin/post.php?post='.$row['form_id'].'&action=edit">'.$row['form_id'].'</a>';?></span></div>
          </div>
          <?php } ?>
          <?php if (!empty($row['form_name'])) { ?>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right">form_name:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm" style="height:unset;"><?php echo $row['form_name'];?></span></div>
          </div>
          <?php } ?>
          <?php if ($row['post_id']!=0) { ?>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right">post_id:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm" style="height:unset;"><?php echo '<a target="_blank" href="/wp-admin/post.php?post='.$row['post_id'].'&action=edit">'.$row['post_id'].'</a>';?></span></div>
          </div>
          <?php } ?>
          <?php if (!empty($row['post_name'])) { ?>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right">post_name:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm" style="height:unset;"><?php echo $row['post_name'];?></span></div>
          </div>
          <?php } ?>
          <?php if (!empty($row['source_url'])) { ?>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right">source_url:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm" style="height:unset;"><a target="_blank" href="<?php echo $row['source_url'];?>"><?php echo $row['source_url'];?></a></span></div>
          </div>
          <?php } ?>
          <?php if (!empty($row['user_agent'])) { ?>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right">user_agent:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm" style="height:unset;"><?php echo $row['user_agent'];?></span></div>
          </div>
          <?php } ?>
           
          
          
          
          
          
        </div>
      </div>
      <?php } ?>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>      
  
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_crm_task']>0) echo $row['id_crm_task'];?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['user_id_add']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_add']))echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
            <div class="col-md-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['user_id_edit']>0) echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
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

<div id="dialog_user_save" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid " style="" >
    <div class="form-group row">  
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Προσθήκη ή επιλογή επαφής');?></div>
    </div>
    <div class="form-group row">  
      <div style="font-size: 100%;text-align:center;width: 100%;">
        <?php echo gks_lang('Βρέθηκαν οι παρακάτω επαφές στο σύστημα');?>
        <?php echo gks_lang('Η αναζήτηση έγινε με βάση το σχετικό πεδίο που αναφέρεται στην στήλη <b>Αναζήτηση</b>.');?>
        <?php echo gks_lang('Μήπως η επαφή που θέλετε να προσθέσετε είναι μία από τις παρακάτω;');?>
        <?php echo gks_lang('Εάν <b>ναι</b>, τότε επιλέξτε την.');?>
        <?php echo gks_lang('Εάν <b>όχι</b>, τότε μπορείτε να προσθέσετε την νέα επαφή επιλέγοντας την επιλογή <b>Προσθήκη νέας επαφής</b>');?>
      </div>
    </div>
    <div class="form-group row">  
      <div class="col-sm-12" style="text-align: center !important;">
        <input type="radio" name="dialog_user_save_radio" id="dialog_user_save_radio_new" value="-1">  <label class="gks_label" for="dialog_user_save_radio_new"><?php echo gks_lang('Προσθήκη νέας επαφής');?>:</label>
      </div>
    </div>
    <div class="form-group row">  
      <div class="col-sm-12" id="dialog_user_save_html">
        
      </div>
    </div>
  
  </div>
</div>


<div id="dialog_gsis" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid " style="" >
    <div class="form-group row">  
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Αναζήτηση Βασικών Στοιχείων Μητρώου Επιχειρήσεων');?></div>
    </div>
    
    <div class="form-group row">  
      <label for="dialog_gsis_afm" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΑΦΜ');?>:</label>
      <div class="col-sm-4">
         <input id="dialog_gsis_afm" type="text" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
      </div>
      <div class="col-sm-4">
         <button style="" id="dialog_gsis_run" class="btn btn-sm btn-primary"><?php echo gks_lang('Αναζήτηση');?></button>
      </div>
    </div>
    <div class="form-group row">  
      <div class="col-sm-12" id="dialog_gsis_html">
        
      </div>
    </div>
    
  </div>
</div>

<?php include_once 'admin-obj-send-message.php'; ?>

<div id="dialog_print" title="<?php echo $GKS_SITE_HUMAN_NAME;?>" style="display: none;">
  <div class="container-fluid " style="" >
    <div class="form-group1 row">  
      <div style="font-size: 120%;font-weight: bold;text-align:center;width: 100%;"><?php echo gks_lang('Ρυθμίσεις Εκτύπωσης');?></div>
    </div>
        
    <div class="row">  
      <label class="col-sm-4 col-form-label form-control-sm text-sm-right" style="font-size: 0.8rem;"><?php echo gks_lang('Τύπος');?>:</label>
      <div class="col-sm-8 form-control-sm text-sm-left" style="font-size: 0.8rem;">
        <input type="radio" name="dialog_print_file_type" id="dialog_print_file_type_pdf"  value="1" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_file_type_pdf" style="display:inline;padding-right:18px;cursor: pointer;">pdf
          <i class="fas fa-file-pdf tooltipster" title="pdf" style="color:#fa0f00;font-size:120%"></i>
          </label>
        <input type="radio" name="dialog_print_file_type" id="dialog_print_file_type_html" value="2" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_file_type_html" style="display:inline;padding-right:18px;cursor: pointer;">html
          <i class="fas fa-file-code tooltipster" title="html" style="color:#4e4e4e;font-size:120%"></i>
          </label>
        <input type="radio" name="dialog_print_file_type" id="dialog_print_file_type_jpg" value="3" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_file_type_jpg" style="display:inline;padding-right:18px;cursor: pointer;">jpg
          <img src="img/jpg21.png" class="tooltipster" title="jpg" style="height:15px;vertical-align: top;"></i>
          </label>
      </div>
    </div>
    <div class="row">  
      <label class="col-sm-4 col-form-label form-control-sm text-sm-right" style="font-size: 0.8rem;"><?php echo gks_lang('Προσανατολισμός');?>:</label>
      <div class="col-sm-8 form-control-sm text-sm-left" style="font-size: 0.8rem;">
        <input type="radio" name="dialog_print_landscape" id="dialog_print_landscape_off" value="1" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_landscape_off" style="display:inline;padding-right:18px;cursor: pointer;"><?php echo gks_lang('Κατακόρυφος');?>
          <i class="fas fa-portrait tooltipster" title="Portrait" style="color:#4e4e4e;font-size:120%"></i>
          </label>
        <input type="radio" name="dialog_print_landscape" id="dialog_print_landscape_on"  value="2" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_landscape_on" style="display:inline;cursor: pointer;"><?php echo gks_lang('Οριζόντιος');?>
          <i class="fas fa-image tooltipster" title="Landscape" style="color:#4e4e4e;font-size:120%"></i>
          </label>
      </div>
    </div>
    <div class="row">  
      <label class="col-sm-4 col-form-label form-control-sm text-sm-right" style="font-size: 0.8rem;"><?php echo gks_lang('Χρώμα ή Γκρι');?>:</label>
      <div class="col-sm-8 form-control-sm text-sm-left" style="font-size: 0.8rem;">
        <input type="radio" name="dialog_print_grayscale" id="dialog_print_grayscale_off" value="1" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_grayscale_off" style="display:inline;padding-right:18px;cursor: pointer;"><?php echo gks_lang('Με χρώμα');?>
          <img src="img/palette-color.png" border="0" width="15" style="vertical-align: top;">
          </label>
        <input type="radio" name="dialog_print_grayscale" id="dialog_print_grayscale_on"  value="2" style="cursor: pointer;">  
          <label class="gks_label" for="dialog_print_grayscale_on" style="display:inline;cursor: pointer;"><?php echo gks_lang('Γκρι');?>
          <img src="img/palette-gray.png" border="0" width="15" style="vertical-align: top;">
          </label>
      </div>
    </div>    

    <div class="row">  
      <label class="col-sm-4 col-form-label form-control-sm text-sm-right" style="font-size: 0.8rem;"><?php echo gks_lang('Μεγέθυνση');?>:</label>
      <div class="col-sm-8 form-control-sm text-sm-left">
        <div id="dialog_print_zoom_slider" style="padding-top: 18px;width: calc(100% - 50px);    margin-left: 25px;">
          <div id="dialog_print_zoom_slider_handle" class="ui-slider-handle"></div>
        </div>
      </div>
    </div>

    
    <div class="row" >
      <div class="gks_print_thump_container">
<?php
  $user_def_form_id=0;
  if (isset($gks_user_settings['print']['form_id_crm_task'])) $user_def_form_id=intval($gks_user_settings['print']['form_id_crm_task']);
  
  $sql_print_forms="SELECT gks_print_forms.*, gks_lang.lang_name
  FROM ((gks_print_objects 
  LEFT JOIN gks_print_objects_forms ON gks_print_objects.id_print_object = gks_print_objects_forms.print_object_id) 
  LEFT JOIN gks_print_forms ON gks_print_objects_forms.print_form_id = gks_print_forms.id_print_form)
  LEFT JOIN gks_lang ON gks_print_forms.gks_lang = gks_lang.id_lang
  WHERE gks_print_forms.id_print_form is not null and gks_print_forms.is_disable=0 AND gks_print_objects.object_name='gks_crm_tasks'
  ".(count($perm_id_print_forms)>0 ? " and gks_print_forms.id_print_form in (".implode(',',$perm_id_print_forms).")" : '')."
  order by gks_print_forms.sortorder,gks_print_forms.print_form_descr";

  $perm_print_forms=array();
  
  $result_print_forms = $db_link->query($sql_print_forms);        
  if (!$result_print_forms) {debug_mail(false,'error sql',$sql_print_forms);die('sql error');}
  while ($row_print_forms = $result_print_forms->fetch_assoc()) {
    //print $row_print_forms['id_print_form'].' '.$row_print_forms['file_thump_url'].'<br>';
    
    $print_form_descr=trim_gks($row_print_forms['print_form_descr']);
    $print_lang_name=trim_gks($row_print_forms['lang_name']);
    $file_thump_url=trim_gks($row_print_forms['file_thump_url']);
    if ($file_thump_url=='') $file_thump_url='img/print_form_empty.png';
    
    $perm_company_ids=trim_gks($row_print_forms['perm_company_ids']);
    $perm_acc_journal_ids=trim_gks($row_print_forms['perm_acc_journal_ids']);
    $perm_acc_seires_ids=trim_gks($row_print_forms['perm_acc_seires_ids']);

    $temp=array('id'=>intval($row_print_forms['id_print_form']));
    if ($perm_company_ids!='') $temp['perm_company_ids']=unserialize($perm_company_ids);
    if ($perm_acc_journal_ids!='') $temp['perm_acc_journal_ids']=unserialize($perm_acc_journal_ids);
    if ($perm_acc_seires_ids!='') $temp['perm_acc_seires_ids']=unserialize($perm_acc_seires_ids);
    $perm_print_forms[]=$temp;
    
    $div_form='<div class="gks_print_thump_div '.
      ($user_def_form_id==$row_print_forms['id_print_form'] ? 'gks_print_thump_div_selected' : '').
      '" data-form_id="'.$row_print_forms['id_print_form'].'" '.
      'data-lang="'.$row_print_forms['gks_lang'].'" '.
      'data-file_type="'.$row_print_forms['file_type'].'" '.
      'data-landscape="'.$row_print_forms['is_landscape'].'" '.
      'data-grayscale="'.$row_print_forms['grayscale'].'" '.
      'data-zoom="'.intval($row_print_forms['zoom']*100).'" '.
      '>';
      $div_form.='<div class="gks_print_thump_title">'.$print_form_descr.'</div>';
      $div_form.='<div class="gks_print_thump_lang">'.$print_lang_name.'</div>';
      $div_form.='<img src="'.$file_thump_url.'" class="gks_print_thump_img" border="0"/>';
      
    
    $div_form.='</div>';
    echo $div_form;
  }
  
  $div_form='<div id="gks_print_thump_more_div">';
    $div_form.='<div id="gks_print_thump_more_text"><i class="fas fa-plus-circle" style="font-size:200%;color:#35dc35;"></i><br>'.gks_lang('Εμφάνιση όλων').'</div>';
  $div_form.='</div>';
  echo $div_form;
  

?>      
      </div>
    </div>
<?php
  $erp_app_id=0;
  $settings_user_print_erp_app_id=0;
  if (isset($gks_user_settings['gks_crm_tasks']['print_erp_app_id'])) {
    $settings_user_print_erp_app_id=intval($gks_user_settings['gks_crm_tasks']['print_erp_app_id']);
  }
  //echo '<pre>aaa '.$settings_user_print_erp_app_id." ";print_r($gks_user_settings['gks_crm_tasks']);die();
  if ($settings_user_print_erp_app_id>0) {
    $sql_send_erp_app="SELECT id_erp_app, erp_app_name, erp_app_last_ping
    FROM gks_erp_app
    where erp_app_disabled=0 and id_erp_app=".$settings_user_print_erp_app_id;
    $result_send_erp_app = $db_link->query($sql_send_erp_app);        
    if (!$result_send_erp_app) {debug_mail(false,'error sql',$sql_send_erp_app);die('sql error');}
    if ($result_send_erp_app->num_rows==1) {
      $row_send_erp_app = $result_send_erp_app->fetch_assoc();
      $erp_app_id=$row_send_erp_app['id_erp_app'];
      
      //echo '<pre>'.$erp_app_id;die();
      
      $send_erp_app_tooltip='';
      $send_erp_app_tooltip.='gks ERP App Desktop: '.trim_gks($row_send_erp_app['erp_app_name']).'<br>';
      if ($gks_user_settings['gks_crm_tasks']['print_erp_app_dest']=='printer') {
        $send_erp_app_tooltip.=gks_lang('Προορισμός').': '.gks_lang('Εκτυπωτής').'<br>';
        $send_erp_app_tooltip.=gks_lang('Μέθοδος').': '.erp_app_dest_printer_method_descr($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_method']).'<br>';
        if (in_array($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_method'],[0,1])) $send_erp_app_tooltip.=gks_lang('Εκτυπωτής').': '.trim_gks($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer']).'<br>';
        if (in_array($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_method'],[2]))   $send_erp_app_tooltip.=gks_lang('IP εκτυπωτή').': '.trim_gks($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_lpr_ip']).'<br>';
        $send_erp_app_tooltip.=gks_lang('Αντίτυπα').': '.trim_gks($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_printer_copies']);
        
      } else if ($gks_user_settings['gks_crm_tasks']['print_erp_app_dest']=='folder') {
        $send_erp_app_tooltip.=gks_lang('Προορισμός').': '.gks_lang('Φάκελος').'<br>';
        $send_erp_app_tooltip.=gks_lang('Φάκελος').': '.trim_gks($gks_user_settings['gks_crm_tasks']['print_erp_app_dest_folder']);
      }     
      $send_erp_app_checkbox_disable=true;
      if (isset($row_send_erp_app['erp_app_last_ping'])) {
        if (strtotime($row_send_erp_app['erp_app_last_ping']) > time()-15*60) {
          $send_erp_app_tooltip.= '<br>'.gks_lang('Τελευταία σύνδεση εφαρμογής').':<br><span class=gks_erp_app_alive>'.secondsago(strtotime($row_send_erp_app['erp_app_last_ping'])).'</span>';
          $send_erp_app_checkbox_disable=false;
        } else {
          $send_erp_app_tooltip.= '<br>'.gks_lang('Τελευταία σύνδεση εφαρμογής').':<br><span class=gks_erp_app_not_alive>'.secondsago(strtotime($row_send_erp_app['erp_app_last_ping'])).'</span>';
        }
      }
      
    }
  }
  if ($erp_app_id>0) {
?>    
    <div class="row">  
      <div class="col-sm-12 form-control-sm text-sm-left">
        <input id="gks_print_send_gks_erp_app" type="checkbox" class="form-control form-control-sm switchery1_sel" value="1" <?php if ($send_erp_app_checkbox_disable) echo 'disabled'; else echo 'checked';?>>
        <label for="gks_print_send_gks_erp_app" style="margin: 0px;position: relative;top: 2px;font-size: 0.8rem;"> <?php echo gks_lang('Αποστολή στην εφαρμογή gks ERP App Desktop');?></label>
        <i class="fas fa-info-circle tooltipster" title="<?php echo $send_erp_app_tooltip;?>" style="font-size: 150%;position: relative;top: 4px;"></i>
      </div>
    </div>    
<?php } ?>    

  </div>  
</div>

<?php echo gks_from_googlemaps_scripts();?>


<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>


var from_php_user_settings_autocomplete_address='<?php echo $gks_user_settings['autocomplete']['address'];?>';
var from_php_dialog_object_rel_curr='gks_crm_tasks';
var from_php_activity_model='gks_crm_tasks';
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
var from_php_need_download_timer='<?php echo $need_download_timer;?>';



var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_tasks','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_tasks','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_crm_tasks','delete',$id);?>;


var from_php_dialog_item_message_email_from_array=[];
<?php 
echo 'from_php_dialog_item_message_email_from_array.push($.base64.decode(\'' . base64_encode($GKS_SITE_EMAIL) . '\'));'."\n"; 
?>



var from_php_check_vies_valid_wait=<?php echo ($mybasketarray['check_vies']['valid']==2 ? 'true' : 'false');?>;


var from_php_print_def_file_type='<?php echo (isset($gks_user_settings['print']['file_type']) ? $gks_user_settings['print']['file_type'] : 'pdf');?>';
var from_php_print_def_grayscale=<?php echo (isset($gks_user_settings['print']['grayscale']) ? $gks_user_settings['print']['grayscale'] : 'false');?>;
var from_php_print_def_landscape=<?php echo (isset($gks_user_settings['print']['landscape']) ? $gks_user_settings['print']['landscape'] : 'false');?>;
var from_php_print_def_zoom=<?php echo (isset($gks_user_settings['print']['zoom']) ? $gks_user_settings['print']['zoom'] : '100');?>;;
var from_php_print_def_form_id=<?php echo (isset($gks_user_settings['print']['form_id_crm_task']) ? $gks_user_settings['print']['form_id_crm_task'] : '0');?>;;
var from_php_print_def_forms=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_user_settings['print']['forms_crm_task']));?>'));

var from_php_perm_print_forms=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($perm_print_forms));?>'));


jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  



  
});
 
</script>

<script src='/my/js/tinymce/tinymce.min.js'></script>
<script src="js/admin-crm-task-item.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="js/admin-obj-send-message.js?v=<?php echo $gks_cache_version;?>"></script>


<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


