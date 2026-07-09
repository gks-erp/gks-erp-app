<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


// https://test.easyfilesselection.com/my/admin-ct-item.php?ctid=1&id=-1
// https://test.easyfilesselection.com/my/admin-ct-item.php?ctid=10035&id=-1
// https://test.easyfilesselection.com/my/admin-ct-item.php?ctid=10035&id=10001

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

db_open();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$ctid=0;if (isset($_GET['ctid'])) $ctid=intval($_GET['ctid']); 
if ($ctid < 10000) {
  $message=gks_lang('Δεν έχει ορισθεί το').' ctid. ('.$ctid.')';
  debug_mail(false,$message,$message);die($message); 
}
$sql_ct="select * 
from gks_custom_table 
where custom_table_disabled=0
and id_custom_table=".$ctid;
$result_ct = $db_link->query($sql_ct);        
if (!$result_ct) {debug_mail(false,'error sql',$sql_ct);die('sql error');}
if ($result_ct->num_rows!=1) {debug_mail(false,'record not found',$sql_ct);die('custom table not found ('.$ctid.')'); }
$row_ct = $result_ct->fetch_assoc();
$custom_table_descr=$row_ct['custom_table_descr'];
$custom_table_name=$row_ct['custom_table_name'];
$custom_table_name_real='gks_customt_'.$row_ct['custom_table_name'];
$field_name_id_parent=$row_ct['field_name_id_parent'];
$field_name_id_current=$row_ct['field_name_id_current'];
$field_id='id_gks_customt_gks_ct_'.$ctid;



$perm_ret=gks_permission_user_can_action($my_wp_user_id, $custom_table_name,($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$perm_id_print_forms=gks_permission_user_condition($my_wp_user_id,'gks_print_forms','01');






if ($id<=0) {
  if (isset($_POST['id'])) $id=intval($_POST['id']);
}
if ($id == 0 or $id < -1) {header('Location: /my'); die(); }



$gks_custom_prepare = gks_custom_table_item_prepare($custom_table_name,['from'=>'item']);


if ($id==-1) {


  $row=array();
  $row['cf_user_id_add'] =0;
  $row['cf_user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['cf_myip'] ='';
  $row[$field_id]=-1;

  
  $my_page_title=gks_lang('Νέα').' '.$custom_table_descr;
} else {
  $sql ="SELECT ".$field_id.",cf_mydate_add,cf_mydate_edit,cf_user_id_add,cf_user_id_edit,cf_myip,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit
  FROM (".$custom_table_name_real." 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on ".$custom_table_name_real.".cf_user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on ".$custom_table_name_real.".cf_user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where ".$field_id." = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $row[$field_name_id_parent]=$id;
  
  $my_page_title=$custom_table_descr.': '.$row[$field_id];
  $object_title=$row[$field_id];
}
$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);


stat_record();


$nav_active_array=array('dikamouobj','dikamouobj_table_'.$ctid);




include_once('_my_header_admin.php');
?>
<link href="css/admin-ct-item.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo $custom_table_descr;?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo $custom_table_descr;?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέα');?></span></h3>
      <?php }?>
    </div>
  </div>
</div>

<div class="container-fluid" id="mypostform" data-table-ctid="<?php echo $ctid;?>" data-table-user-id="<?php echo $my_wp_user_id;;?>">
  <div class="row">


    
      
<?php
echo $gks_custom_row['html'];
//echo '<pre>';print_r($gks_custom_row['fields']);print '</pre>';
?>



  </div>
</div>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
    </div>
  </div>
</div>




<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_ok"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $id;?>" data-model="<?php echo $custom_table_name;?>" data-backurl="admin-ct.php?ctid=<?php echo $ctid;?>"><?php echo gks_lang('Διαγραφή');?></button>
      <button type="button" class="btn btn-dark" id="submit_button_print"><?php echo gks_lang('Εκτύπωση');?> <i class="fas fa-print" style="color: #35dc35;font-size: 120%;"></i></button>
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">
      
      <?php 
      echo getObjectRels($custom_table_name,$id);
      echo getActivityObjectTable($custom_table_name,$id);
      
      ?>
      
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
            $sql_msg="SELECT ".$custom_table_name_real."_messages.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
            FROM ".$custom_table_name_real."_messages
            LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ".$custom_table_name_real."_messages.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
            WHERE ".$custom_table_name_real."_messages.gks_customt_gks_".$field_name_id_current."=".$id."
            ORDER BY ".$custom_table_name_real."_messages.mydate_add DESC, ".$custom_table_name_real."_messages.".$field_name_id_parent."_message DESC;";
            //echo '<pre>'.$sql_msg;die();
            $result_msg = $db_link->query($sql_msg);        
            if (!$result_msg) debug_mail(false,'error sql',$sql_msg);
            if (!$result_msg) die('sql error');
            
            $j = 0;
            while ($row_msg = $result_msg->fetch_assoc()) {
              $j++; ?>
          
            
            <tr id="tr_messages_<?php echo $row_msg[$field_name_id_parent.'_message'];?>">
              <th scope="row" class="mytdcm message_aa"><?php echo $j;?></th>
              <td class="mytdcml"><?php echo showDate(strtotime($row_msg['mydate_add']), 'd/m/Y H:i', 1);?></td>  
              <td class="mytdcml"><?php 
                echo $row_msg['gks_nickname'];?></td>  
              <td class="mytdcml"><div class="gks_dive1"><div class="gks_dive2 mydivexpand"><?php 
                echo str_replace('[[-r]]', '<i class="fas fa-arrow-alt-circle-right gksvm" style=""></i>', $row_msg['customt_message']);
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
      $obj_fileslist= gks_FilesObjectList(array('objname'=>$custom_table_name,'id'=>$id));
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
            $sql_log="SELECT ".$custom_table_name_real."_log.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname
            FROM ".$custom_table_name_real."_log 
            LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ".$custom_table_name_real."_log.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
            WHERE ".$custom_table_name_real."_log.gks_customt_gks_".$field_name_id_current."=".$id."
            ORDER BY ".$custom_table_name_real."_log.".$field_name_id_parent."_log DESC;";
            //echo '<pre>'.$sql_log;die();
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
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
                
        <div class="card-body" <?php echo gks_card_body('kat');?>>      
  
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($id>0) echo $id;?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['cf_user_id_add']>0) echo '<a href="admin-users-item.php?id='.$row['cf_user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['cf_mydate_add']))echo showDate(strtotime($row['cf_mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['cf_user_id_edit']>0) echo '<a href="admin-users-item.php?id='.$row['cf_user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['cf_mydate_edit'])) echo showDate(strtotime($row['cf_mydate_edit']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('IP');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><a href="admin-stat-ip.php?ip=<?php echo $row['cf_myip'];?>"><?php echo $row['cf_myip'];?></a></span></div>
          </div>
        </div>
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
  if (isset($gks_user_settings['print']['form_id_customt'])) $user_def_form_id=intval($gks_user_settings['print']['form_id_customt']);
  
  //print '<pre>';print_r($perm_id_print_forms) ;die(); //
  $sql_print_forms="SELECT gks_print_forms.*, gks_lang.lang_name
  FROM ((gks_print_objects 
  LEFT JOIN gks_print_objects_forms ON gks_print_objects.id_print_object = gks_print_objects_forms.print_object_id) 
  LEFT JOIN gks_print_forms ON gks_print_objects_forms.print_form_id = gks_print_forms.id_print_form)
  LEFT JOIN gks_lang ON gks_print_forms.gks_lang = gks_lang.id_lang
  WHERE gks_print_forms.id_print_form is not null and gks_print_forms.is_disable=0 AND gks_print_objects.object_name='gks_ct_".$ctid."'
  ".(count($perm_id_print_forms)>0 ? " and gks_print_forms.id_print_form in (".implode(',',$perm_id_print_forms).")" : '')."
  order by gks_print_forms.sortorder,gks_print_forms.print_form_descr";
  //print '<pre>';print_r($sql_print_forms);die();

  $perm_print_forms=array();
  
  $result_print_forms = $db_link->query($sql_print_forms);        
  if (!$result_print_forms) {debug_mail(false,'error sql',$sql_print_forms);die('sql error');}
  while ($row_print_forms = $result_print_forms->fetch_assoc()) {
    //print $row_print_forms['id_print_form'].' '.$row_print_forms['file_thump_url'].'<br>';
    
    $print_form_descr=trim_gks($row_print_forms['print_form_descr']);
    $print_lang_name=trim_gks($row_print_forms['lang_name']);
    $file_thump_url=trim_gks($row_print_forms['file_thump_url']);
    if ($file_thump_url=='') $file_thump_url='img/print_form_empty.png';
    




    $temp=array('id'=>intval($row_print_forms['id_print_form']));



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
  $html_erp_app_id='';


  $erp_app_id=0;
  if ($ctid>=10000) {
    $sql_send_erp_app="SELECT erp_app_id, erp_app_dest,  
    erp_app_dest_printer, 
    erp_app_dest_printer_method,
    erp_app_dest_printer_lpr_ip,
    erp_app_dest_printer_copies, 
    erp_app_dest_folder, 
    gks_erp_app.id_erp_app, gks_erp_app.erp_app_name, gks_erp_app.erp_app_last_ping
    FROM gks_custom_table 
    LEFT JOIN gks_erp_app ON gks_custom_table.erp_app_id = gks_erp_app.id_erp_app
    where gks_custom_table.id_custom_table=".$ctid;
    
    $result_send_erp_app = $db_link->query($sql_send_erp_app);        
    if (!$result_send_erp_app) {debug_mail(false,'error sql',$sql_send_erp_app);die('sql error');}
    if ($result_send_erp_app->num_rows==1) {
      $row_send_erp_app = $result_send_erp_app->fetch_assoc();
      $erp_app_id=$row_send_erp_app['erp_app_id'];
      

      $send_erp_app_tooltip='';
      $send_erp_app_tooltip.=gks_lang('gks ERP App Desktop').': '.trim_gks($row_send_erp_app['erp_app_name']).'<br>';
      if ($row_send_erp_app['erp_app_dest']=='printer') {
        $send_erp_app_tooltip.=gks_lang('Προορισμός').': '.gks_lang('Εκτυπωτής').'<br>';
        $send_erp_app_tooltip.=gks_lang('Μέθοδος').': '.erp_app_dest_printer_method_descr($row_send_erp_app['erp_app_dest_printer_method']).'<br>';
        if (in_array($row_send_erp_app['erp_app_dest_printer_method'],[0,1])) $send_erp_app_tooltip.=gks_lang('Εκτυπωτής').': '.trim_gks($row_send_erp_app['erp_app_dest_printer']).'<br>';
        if (in_array($row_send_erp_app['erp_app_dest_printer_method'],[2]))   $send_erp_app_tooltip.=gks_lang('IP εκτυπωτή').': '.trim_gks($row_send_erp_app['erp_app_dest_printer_lpr_ip']).'<br>';
        $send_erp_app_tooltip.=gks_lang('Αντίτυπα').': '.trim_gks($row_send_erp_app['erp_app_dest_printer_copies']);
      } else if ($row_send_erp_app['erp_app_dest']=='folder') {
        $send_erp_app_tooltip.=gks_lang('Προορισμός').': '.gks_lang('Φάκελος').'<br>';
        $send_erp_app_tooltip.=gks_lang('Φάκελος').': '.trim_gks($row_send_erp_app['erp_app_dest_folder']);
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
    $html_erp_app_id=
    '<div class="col-sm-12 form-control-sm text-sm-left">
       <input id="gks_print_send_gks_erp_app" type="checkbox" class="form-control form-control-sm switchery1_sel" value="1" '.($send_erp_app_checkbox_disable ? 'disabled' : 'checked').'>
       <label for="gks_print_send_gks_erp_app" style="margin: 0px;position: relative;top: 2px;font-size: 0.8rem;"> '.gks_lang('Αποστολή στην εφαρμογή gks ERP App Desktop').'</label>
       <i class="fas fa-info-circle tooltipster" title="'.$send_erp_app_tooltip.'" style="font-size: 150%;position: relative;top: 4px;"></i>
     </div>';
  }
  if ($html_erp_app_id!='') {
    echo '<div class="row">';
      echo $html_erp_app_id;
    echo '</div>';
  }

?>    
   

  </div>  
</div>

<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var from_php_ctid=<?php echo $ctid;?>;
var from_php_id=<?php echo $id;?>;
var from_php_dialog_object_rel_curr='<?php echo $custom_table_name;?>';
var from_php_activity_model='<?php echo $custom_table_name;?>';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_print_def_file_type='<?php echo (isset($gks_user_settings['print']['file_type']) ? $gks_user_settings['print']['file_type'] : 'pdf');?>';
var from_php_print_def_grayscale=<?php echo (isset($gks_user_settings['print']['grayscale']) ? $gks_user_settings['print']['grayscale'] : 'false');?>;
var from_php_print_def_landscape=<?php echo (isset($gks_user_settings['print']['landscape']) ? $gks_user_settings['print']['landscape'] : 'false');?>;
var from_php_print_def_zoom=<?php echo (isset($gks_user_settings['print']['zoom']) ? $gks_user_settings['print']['zoom'] : '100');?>;;
var from_php_print_def_form_id=<?php echo (isset($gks_user_settings['print']['form_id_customt']) ? $gks_user_settings['print']['form_id_customt'] : '0');?>;;
var from_php_print_def_forms=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($gks_user_settings['print']['forms_customt']));?>'));

var from_php_dialog_item_message_email_from_array=[];
<?php 
echo 'from_php_dialog_item_message_email_from_array.push($.base64.decode(\'' . base64_encode($GKS_SITE_EMAIL) . '\'));'."\n"; 
?>


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, $custom_table_name,'edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, $custom_table_name,'add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, $custom_table_name,'delete',$id);?>;


jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
    
});
</script>

<script src='/my/js/tinymce/tinymce.min.js'></script>
<script src="js/admin-ct-item.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="js/admin-obj-send-message.js?v=<?php echo $gks_cache_version;?>"></script>


<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


