<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();


$nav_active_array=array('crm','manage_email','manage_email_templates');
db_open();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_email_template',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}





$gks_custom_prepare = gks_custom_table_item_prepare('gks_email_template',['from'=>'item']);


if ($id==-1) {
  
  $copy_id=0;
  if (isset($_GET['copy'])) $copy_id=intval($_GET['copy']);
  if ($copy_id > 0) {
    //echo time();
    //die();
    //product_photo
      
    $sql="INSERT INTO gks_email_template (
    email_template_descr,
    email_body,email_subject,email_message,
    is_disable,sortorder,gks_lang,need_attachments,other_fields,attachments,edit_mode,localization_set_id,
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip)
    
    SELECT CONCAT(email_template_descr,' draft ".rand(1000,9999)."') as email_template_descr_new,
    email_body,email_subject,email_message,
    1 as is_disable,sortorder,gks_lang,need_attachments,other_fields,attachments,edit_mode,0 as localization_set_id,
    
    now() as mydate_add, now() as mydate_edit, ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit,'".$db_link->escape_string($gkIP)."' as myip
    from gks_email_template
    WHERE id_email_template=".$copy_id;
    
    //print '<pre>';
    //print $sql;
    //die();
    
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
    $id = $db_link->insert_id;

    
    
    $sql="insert into gks_email_template_object_forms (
      mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
      email_template_id,email_template_object_id) 
    SELECT now() as mydate_add,now() as mydate_edit,".$my_wp_user_id." as user_id_add,".$my_wp_user_id." as user_id_edit,'".$db_link->escape_string($gkIP)."' as myip,
    ".$id." as email_template_id, email_template_object_id
    FROM gks_email_template_object_forms 
    WHERE email_template_id=".$copy_id;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
    
    //var_dump($id);
    //die();    
    if ($id > 0) {
      header('Location: ?id='.$id);
      die();
    }
  }
    
  $row = array();
  $row['id_email_template']=-1;
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['email_template_descr']='';
  $row['is_disable']=1;
  $row['sortorder']=1000;
  $row['gks_lang']='el-GR';
  $row['email_body']='';
  $row['email_subject']='';
  $row['email_message']='';
  $row['need_attachments']=0;
  $row['other_fields']='';
  $row['attachments']='';
  $row['localization_set_id']=0;
  $row['edit_mode']='html';
  
  $my_page_title=gks_lang('Νέο Πρότυπο email');
} else {
  $sql ="SELECT gks_email_template.*, 
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
  gks_lang.lang_name
  FROM ((gks_email_template 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_email_template.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_email_template.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN gks_lang ON gks_email_template.gks_lang = gks_lang.id_lang
  where id_email_template = ".$id;
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
  $my_page_title=gks_lang('Πρότυπο email').': '.$row['email_template_descr'];
  $object_title=$row['email_template_descr'];
}

$edit_mode=$row['edit_mode'];

$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);



$localization_set_id=$row['localization_set_id'];



$sql_fobjects="SELECT gks_email_template_object.object_descr
FROM gks_email_template_object_forms 
LEFT JOIN gks_email_template_object ON gks_email_template_object_forms.email_template_object_id = gks_email_template_object.id_email_template_object
WHERE gks_email_template_object_forms.email_template_id=".$id."
ORDER BY gks_email_template_object.object_descr";
$result_fobjects = $db_link->query($sql_fobjects);        
if (!$result_fobjects) {debug_mail(false,'error sql',$sql_fobjects);die('sql error');}
$fobjects=array();
while ($row_fobjects = $result_fobjects->fetch_assoc()) {
  $fobjects[]=$row_fobjects['object_descr'];
}
$fobjects_text=implode(']][[',$fobjects);


$gks_fobjects_tags=array();
$sql_fobjects="select object_descr from gks_email_template_object order by object_descr";
$result_fobjects = $db_link->query($sql_fobjects);        
if (!$result_fobjects) {debug_mail(false,'error sql',$sql_fobjects);die('sql error');}
$gks_fobjects_tags=array();
while ($row_fobjects = $result_fobjects->fetch_assoc()) {
  $gks_fobjects_tags[]=$row_fobjects['object_descr'];
}

stat_record();



function this_html_add_code($html_in) {
  global $edit_mode;
  if ($edit_mode=='raw') return $html_in;
  
  return $html_in;
  
  //$pattern='~\{((?:[^\{\}]++|(?R))*)\}~'; // { 
  //$pattern="~\[((?:[^\[\]]++|(?R))*)\]~"; // [
  //preg_match_all( $pattern, $html_in, $matches);
  
  $html_out=$html_in;
//  foreach ($matches[0] as $value) {
//    //$html_out=str_replace($value, htmlspecialchars_gks($value), $html_out);
//    $html_out=str_replace($value, htmlentities($value), $html_out);
//  } 
  //print_r($html_out);
  //die();
  
  return '<!DOCTYPE html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>gks</title>
  <base href="'.GKS_SITE_URL.'">
  <link href="'.GKS_SITE_URL.'my/css/fontawesome-all.min.css" rel="stylesheet">
  <style type="text/css">

    thead { display: table-header-group; }
    tfoot { display: table-row-group; }
    tr { page-break-inside: avoid; }  
    th { page-break-inside: avoid; }  
  </style>
</head>
<body>

'.
$html_out.  
  
'

</body>
</html>';    
}



include_once('_my_header_admin.php');
?>

<link rel="stylesheet" href="/my/css/admin-email-templates-item.css?v=<?php echo $gks_cache_version;?>" type="text/css">    


<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Πρότυπο email');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Πρότυπο email');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέο');?></span></h3>
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
            <label for="email_template_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-md-8">
              <input id="email_template_descr" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['email_template_descr']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="email_subject" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Θέμα');?>:</label>
            <div class="col-md-8">
              <input id="email_subject" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['email_subject']);?>">
            </div>
          </div> 
                    
          <div class="form-group row">
            <label for="gks_lang" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γλώσσα');?>:</label>
            <div class="col-md-8">
              <select id="gks_lang" class="form-control form-control-sm myneedsave">
                <option value=""></option>
                <?php
                $local_set_lang_current='';
                $langs_array=array();
                
                $lang_prepare_gks_lang=gks_lang_data_obj_prepare('gks_lang','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_lang, array('lang_name'));
                $sql="select id_lang,lang_ico,".gks_lang_sql_field('lang_name',$lang_prepare_gks_lang)." 
                FROM ".$lang_prepare_gks_lang['sql']['from1']." gks_lang 
                ".$lang_prepare_gks_lang['sql']['from2']."
                ORDER BY lang_sortorder,lang_name";                  
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_lang'].'" ';
                  if ($row_select['id_lang']==$row['gks_lang']) {
                    echo ' selected ';
                    $local_set_lang_current=$row_select['lang_name'];
                  }
                  echo '>'.$row_select['lang_name'].'</option>';
                  $langs_array[]=array('id'=>$row_select['id_lang'], 'descr' => $row_select['lang_name']);
                }?>
              </select>    
            </div>
          </div>           
          
                    




          <div class="form-group row">
            <label for="need_attachments" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Απαιτείται συνημμένο');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="need_attachments" value="1" <?php if ($row['need_attachments']==1) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>          
          
          
          
          <div class="form-group row">
            <label for="is_disable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="is_disable" value="1" <?php if ($row['is_disable']==0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          <div class="form-group row">
            <label for="sortorder" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-sm-8">
              <input id="sortorder" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['sortorder']);?>">
            </div>
          </div>          
        </div>
      </div>


    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Περιορισμοί');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('apply');?>>      
          <div class="form-group row">
            <label for="gks_fobjects" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αντικείμενα');?>:</label>
            <div class="col-md-8" id="field_gks_fobjects">
              <input id="gks_fobjects" type="text" class="form-control form-control-sm myneedsave" value="<?php echo $fobjects_text;?>">
            </div>
          </div>

        </div>
      </div>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σε άλλες γλώσσες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('langs');?>> 
<?php
$localization_sets=array();
if ($localization_set_id>0) {
  $sql_localization="SELECT gks_email_template.id_email_template, gks_email_template.email_template_descr, gks_email_template.gks_lang, gks_lang.lang_name
  FROM gks_email_template LEFT JOIN gks_lang ON gks_email_template.gks_lang = gks_lang.id_lang
  WHERE gks_email_template.localization_set_id=".$localization_set_id."
  and gks_email_template.id_email_template<>".$id."
  ORDER BY gks_lang.lang_name";
  $result_localization = $db_link->query($sql_localization);        
  if (!$result_localization) {debug_mail(false,'error sql',$sql_localization);die('sql error');}
  while ($row_localization = $result_localization->fetch_assoc()) {
    $localization_sets[]=$row_localization;
  }
  //print '<pre>'; print_r($localization_sets);die();
}
?>
<table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
    <tr >	
        <th class="table-dark" scope="col" style="text-align: center !important;" width='0%' >#</th>
        <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="50%"><?php echo gks_lang('Γλώσσα');?></th> 
        <th class="table-dark" scope="col" style="text-align: center !important;" width="50%"><?php echo gks_lang('Πρότυπο');?></th>        
    </tr>
</thead>
<tbody>
<tr class="" data-id="0">
  <th scope="row" nowrap align="right">1</td>      
  <td nowrap align="center"></td>
  <td nowrap>
    <span class="form-control-sm" id="local_set_lang_current"><?php echo $local_set_lang_current;?></span>
  </td>
  <td nowrap>
    <span class="form-control-sm"><?php echo gks_lang('Τρέχον πρότυπο');?></span>
  </td>
</tr>  
<?php
$local_set_index = 1;
foreach ($localization_sets as $row_list) {
  

  $local_set_index++;
  ?>
  <tr class="local_set_tr" data-id="<?php echo $local_set_index;?>">
    <th scope="row" nowrap class="mytdcm local_set_aa"><?php echo $local_set_index;?></td>      
    <td nowrap class="mytdcm"><i class="fas fa-trash-alt local_set_index_remove" data-id="<?php echo $local_set_index;?>"></i></td>
    
    <td nowrap class="mytdcm">
      <select class="form-control form-control-sm myneedsave local_set_lang" style="width:100%;" data-id="<?php echo $local_set_index;?>">
        <option value=""></option>
        <?php
        foreach ($langs_array as $lang_rec) {
          echo '<option value="'.$lang_rec['id'].'" ';
          if ($lang_rec['id']==$row_list['gks_lang']) echo ' selected ';
          echo '>'.$lang_rec['descr'].'</option>';
        }?>
      </select>
      
      
          
    </td>
    <td nowrap class="mytdcm">
      <input type="text" class="form-control form-control-sm myneedsave local_set_form" value="<?php echo $row_list['email_template_descr'];?>"
      data-id="<?php echo $local_set_index;?>"
      style="width:calc(100% - 22px);display:inline;" 
      placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>"
      data-form_id="<?php echo $row_list['id_email_template'];?>">
      <a class="local_set_form_link" data-id="<?php echo $local_set_index;?>" href="admin-email-templates-item.php?id=<?php echo $row_list['id_email_template'];?>" tabindex="-1">
        <i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή Φόρμας');?>"></i>
      </a>
    </td>
  </tr>
<?php } ?>


  <tr class="" id="tr_new_button">
    <th scope="row" colspan="2" class="mytdcm"><i class="fas fa-plus-circle gks_gen_add"></i></td>      
    <td nowrap colspan="5">
      <button style="justify-content: center!important;" type="button" class="btn btn-sm btn-primary" id="add_local_set"><?php echo gks_lang('Προσθήκη');?></button>
    </td>  
  </tr>   
  
</tbody>
</table>
        </div>
      </div>
      
<?php
echo $gks_custom_row['html'];
//echo '<pre>';print_r($gks_custom_row['fields']);print '</pre>';
?>

    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12" style="margin-top: 24px;margin-bottom: 24px;">
      <div id="edit_mode_div">
        <div><?php echo gks_lang('Επεξεργασία');?>:</div>
        <div>
          <input type="radio" name="edit_mode" id="edit_mode_html" value="html" class="myneedsave" style="cursor: pointer;" <?php if ($row['edit_mode']=='html') echo 'checked';?>>
          <label class="form-control-sm" for="edit_mode_html" style="display:inline;padding-right:18px;cursor: pointer;">html
            <i class="fas fa-file-code tooltipster" title="html" style="color:#4e4e4e;font-size:150%"></i>
          </label>
        </div>
        <div>
          <input type="radio" name="edit_mode" id="edit_mode_raw" value="raw" class="myneedsave" style="cursor: pointer;" <?php if ($row['edit_mode']=='raw') echo 'checked';?>>
          <label class="form-control-sm" for="edit_mode_raw" style="display:inline;padding-right:18px;cursor: pointer;">raw
            <i class="fas fa-code tooltipster" title="raw" style="color:#4e4e4e;font-size:150%"></i>
          </label>        
        </div>
      </div>
    </div>
  </div>
</div>



<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Σώμα μηνύματος');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('body');?>>
          <textarea class="gks_curr_tinymce" type="text" style="width:100%;height:400px;" id="email_body" name="email_body"><?php echo this_html_add_code($row['email_body']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div>  
      

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Προεπιλεγμένο κείμενο');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('message');?>>
          <textarea class="gks_curr_tinymce" type="text" style="width:100%;height:400px;" id="email_message" name="email_message"><?php echo this_html_add_code($row['email_message']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div> 

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Παράμετροι');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('params');?>>
          <?php
          
//1 label id
//2 type
//3 px
//4 icon
//5 value
//6 jquery_selector
//7 buttons

          
          $gkscols_parameter1 ='col-12 col-sm-6  col-md-4  col-lg-1 gks_items_col';
          $gkscols_parameter2 ='col-12 col-sm-6  col-md-4  col-lg-2 gks_items_col';
          $gkscols_parameter3 ='col-12 col-sm-6  col-md-4  col-lg-2 gks_items_col';
          $gkscols_parameter4 ='col-12 col-sm-6  col-md-3  col-lg-2 gks_items_col';
          $gkscols_parameter5 ='col-12 col-sm-6  col-md-3  col-lg-2 gks_items_col';
          $gkscols_parameter6 ='col-12 col-sm-6  col-md-3  col-lg-2 gks_items_col';
          $gkscols_parameter7 ='col-12 col-sm-12 col-md-3  col-lg-1 gks_items_col';
          
          
          ?>
          <div class="form-group row gks_parameter_label">
            <div class="<?php echo $gkscols_parameter1;?>">
              <div class="table-dark gks_parameter_label"><?php echo gks_lang('Παράμετρος');?></div>
            </div>
            <div class="<?php echo $gkscols_parameter2;?>">
              <div class="table-dark gks_parameter_label"><?php echo gks_lang('Τύπος');?></div>
            </div>

            <div class="<?php echo $gkscols_parameter3;?>">
              <div class="table-dark gks_parameter_label"><?php echo gks_lang('Παράδειγμα');?></div>
            </div>
            <div class="<?php echo $gkscols_parameter4;?>">
              <div class="table-dark gks_parameter_label"><?php echo gks_lang('Εικονίδιο');?></div>
            </div>
            <div class="<?php echo $gkscols_parameter5;?>">
              <div class="table-dark gks_parameter_label"><?php echo gks_lang('Τιμή');?></div>
            </div>
            <div class="<?php echo $gkscols_parameter6;?>">
              <div class="table-dark gks_parameter_label">jQuery Selector</div>
            </div>
            
            <div class="<?php echo $gkscols_parameter7;?>">
              <div class="table-dark gks_parameter_label"><i class="fas fa-exclamation-circle" style="font-size:120%;vertical-align:middle;"></i></div>
            </div> 

          </div>          
          <div id="parameters_table">

          <?php
          $params=[];
          $row['other_fields']=trim_gks($row['other_fields']);
          if ($row['other_fields']!='') {
            $params=json_decode($row['other_fields'],true);
          }
          $bb = 0;
          foreach ($params as $vparam) {
                      
          
          
            $bb++;
            ?>
          <div class="form-group row gks_parameter_line" data-bb="<?php echo $bb;?>">
            <div class="<?php echo $gkscols_parameter1;?>">
              <input type="text" class="form-control form-control-sm gks_fparam_label" data-bb="<?php echo $bb;?>" 
              value="<?php if (isset($vparam['label'])) echo $vparam['label']?>"/>
            </div>
            
            <div class="<?php echo $gkscols_parameter2;?>">
              <select class="form-control form-control-sm myneedsave gks_select2 gks_fparam_type" data-bb="<?php echo $bb;?>">
              <option <?php if ($vparam['type']=='text') echo 'selected';?> value="text"><?php echo gks_lang('Κείμενο');?></option>
              <option <?php if ($vparam['type']=='textarea') echo 'selected';?> value="textarea"><?php echo gks_lang('Μεγάλο κείμενο');?></option>
              </select>
            </div>
            <div class="<?php echo $gkscols_parameter3;?>">
              <input type="text" class="form-control form-control-sm gks_fparam_px" data-bb="<?php echo $bb;?>" 
              value="<?php if (isset($vparam['px'])) echo $vparam['px']?>"/>
            </div>
            <div class="<?php echo $gkscols_parameter4;?>">
              <input type="text" class="form-control form-control-sm gks_fparam_icon" data-bb="<?php echo $bb;?>" 
              value="<?php if (isset($vparam['icon'])) echo $vparam['icon']?>"/>
            </div>
            <div class="<?php echo $gkscols_parameter5;?>">
              <input type="text" class="form-control form-control-sm gks_fparam_value" data-bb="<?php echo $bb;?>" 
              value="<?php if (isset($vparam['value'])) echo $vparam['value']?>"/>
            </div>            
            <div class="<?php echo $gkscols_parameter6;?>">
              <input type="text" class="form-control form-control-sm gks_fparam_jquery_selector" data-bb="<?php echo $bb;?>" 
              value="<?php if (isset($vparam['jquery_selector'])) echo $vparam['jquery_selector']?>"/>
            </div> 
                          
            <div class="<?php echo $gkscols_parameter7;?>">
              <div class="text-center gks_parameter_icons">
                <div style="width:33%;float:left;">
                  <i class="fas fa-trash-alt gks_delete_parameterline" data-bb="<?php echo $bb;?>"></i>
                </div>
                <div style="width:33%;float:left;">
                  <i class="fas fa-arrows-alt-v sortorder_parameterline_handle"></i>
                </div>
                <div style="width:33%;float:left;">
                  <i class="fas fa-plus-circle gks_add_parameterline"  data-bb="<?php echo $bb;?>"></i>
                </div>
                
                
              </div>
            </div>
            
          </div>
          <?php 
          }
          ?>        
        
        
          <div class="row" id="gks_parameter_footer1"></div>
          </div>            
            

                    
        </div>
      </div>
    </div>
  </div>
</div>           


<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Συνημμένα');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('attach');?>>
          <?php

/*$ddd[]=array(
'basefolder'=>'erpfi',
'relative_path'=>'base/users/31413/NIPT TRF Request Consent Form_ ENG FINAL.pdf',
'name_for_email'=>''
'def_check'=>true,
);
*/


          
          $gkscols_attach_param1 ='col-12 col-sm-6  col-md-4  col-lg-2 gks_items_col';
          $gkscols_attach_param2 ='col-12 col-sm-6  col-md-8  col-lg-5 gks_items_col';
          $gkscols_attach_param3 ='col-12 col-sm-6  col-md-6  col-lg-3 gks_items_col';
          $gkscols_attach_param4 ='col-12 col-sm-6  col-md-3  col-lg-1 gks_items_col';
          $gkscols_attach_param5 ='col-12 col-sm-12 col-md-3  col-lg-1 gks_items_col';

          
          
          ?>
          <div class="form-group row gks_attach_param_label">
            <div class="<?php echo $gkscols_attach_param1;?>">
              <div class="table-dark gks_attach_param_label"><?php echo gks_lang('Θέση');?></div>
            </div>
            <div class="<?php echo $gkscols_attach_param2;?>">
              <div class="table-dark gks_attach_param_label"><?php echo gks_lang('Σχετική διαδρομή');?></div>
            </div>
            <div class="<?php echo $gkscols_attach_param3;?>">
              <div class="table-dark gks_attach_param_label"><?php echo gks_lang('Σχόλιο');?></div>
            </div>
            <div class="<?php echo $gkscols_attach_param4;?>" >
              <div class="table-dark gks_attach_param_label"><?php echo gks_lang('Προεπιλεγμένο');?></div>
            </div>
            <div class="<?php echo $gkscols_attach_param5;?>">
              <div class="table-dark gks_attach_param_label"><i class="fas fa-exclamation-circle" style="font-size:120%;vertical-align:middle;"></i></div>
            </div> 
          </div>          
          <div id="attach_params_table">

          <?php
          $attach_param=[];
          $row['attachments']=trim_gks($row['attachments']);
          if ($row['attachments']!='') {
            $attach_param=json_decode($row['attachments'],true);
          }
          $ap = 0;
          foreach ($attach_param as $vparam) {
                      
          
          
            $ap++;
            ?>
          <div class="form-group row gks_attach_param_line" data-ap="<?php echo $ap;?>">
            <div class="<?php echo $gkscols_attach_param1;?>">
              <?php
              $basefolder=''; if (isset($vparam['basefolder'])) $basefolder=$vparam['basefolder'];
              ?>
              <select class="form-control form-control-sm myneedsave gks_select2 gks_attach_param_basefolder" data-ap="<?php echo $ap;?>">
                <option <?php if ($basefolder=='erplo') echo 'selected';?> value="erplo"><?php echo gks_lang('ERP Λογότυπα');?></option>
                <option <?php if ($basefolder=='erpfi') echo 'selected';?> value="erpfi"><?php echo gks_lang('ERP Αρχεία');?></option>
                <option <?php if ($basefolder=='erpul') echo 'selected';?> value="erpul"><?php echo gks_lang('ERP Μεταφορτώσεις');?></option>
                <option <?php if ($basefolder=='erpdl') echo 'selected';?> value="erpdl"><?php echo gks_lang('ERP Λήψεις');?></option>
                <option <?php if ($basefolder=='wodpr') echo 'selected';?> value="wodpr"><?php echo gks_lang('Wordpress');?></option>
              </select>
            </div>
            
            <div class="<?php echo $gkscols_attach_param2;?> gks_attach_param_relative_path_div">
              <input type="text" class="form-control form-control-sm myneedsave gks_attach_param_relative_path" data-ap="<?php echo $ap;?>" 
              value="<?php if (isset($vparam['relative_path'])) echo $vparam['relative_path']?>"/>
              <div title="<?php echo gks_lang('Εξερεύνηση αρχείων');?>" class="btn btn-primary btn-sm gks_attach_param_relative_path_btn tooltipster" data-ap="<?php echo $ap;?>">
                <i class="fa-solid fa-file-lines"></i>
              </div>
            </div>
            <div class="<?php echo $gkscols_attach_param3;?>">
              <input type="text" class="form-control form-control-sm myneedsave gks_attach_param_name_for_email" data-ap="<?php echo $ap;?>" 
              value="<?php if (isset($vparam['name_for_email'])) echo $vparam['name_for_email']?>"/>

            </div>
            <div class="<?php echo $gkscols_attach_param4;?> text-center">
              <input type="checkbox" class="gks_attach_param_def_check switchery1_this" data-ap="<?php echo $ap;?>" 
              <?php if ($vparam['def_check']) echo 'checked';?> value="1"/>
            </div>
            <div class="<?php echo $gkscols_attach_param5;?>">
              <div class="text-center gks_attach_param_icons">
                <div style="width:33%;float:left;">
                  <i class="fas fa-trash-alt gks_delete_attach_paramline" data-ap="<?php echo $ap;?>"></i>
                </div>
                <div style="width:33%;float:left;">
                  <i class="fas fa-arrows-alt-v sortorder_attach_paramline_handle"></i>
                </div>
                <div style="width:33%;float:left;">
                  <i class="fas fa-plus-circle gks_add_attach_paramline"  data-ap="<?php echo $ap;?>"></i>
                </div>
              </div>
            </div>
          </div>
          <?php 
          }
          ?>        
        
        
          <div class="row" id="gks_attach_param_footer1"></div>
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
      
      <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_email_template'];?>" data-model="gks_email_template" data-backurl="admin-email-templates.php"><?php echo gks_lang('Διαγραφή');?></button>
      <?php if ($id>0) {?>
      <button type="button" class="btn btn-primary" id="submit_button_copy" onclick="window.location.href='admin-email-templates-item.php?id=-1&copy=<?php echo $id;?>'"><?php echo gks_lang('Δημιουργία αντιγράφου');?></button>
      <?php } ?>

    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-xl-6">
      
      <?php 
      echo getObjectRels('gks_email_template',$id);
      echo getActivityObjectTable('gks_email_template',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_email_template','id'=>$id));
      echo $obj_fileslist['html'];
      ?>
      
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>      
  
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_email_template']>0) echo $row['id_email_template'];?></span></div>
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
    
    <div class="col-xl-6">

      
    </div>
    
  </div>
</div>



<?php include_once('_dialogs.php'); ?>
<script src='/my/js/tinymce/tinymce.min.js'></script>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var from_php_dialog_object_rel_curr='gks_email_template';
var from_php_activity_model='gks_email_template';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>; 



var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_email_template','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_email_template','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_email_template','delete',$id);?>;



var from_php_edit_mode='<?php echo $edit_mode;?>';



var last_bb=<?php echo $bb;?>;
var last_ap=<?php echo $ap;?>;
var from_php_gkscols_parameter1='<?php echo $gkscols_parameter1;?>';
var from_php_gkscols_parameter2='<?php echo $gkscols_parameter2;?>';
var from_php_gkscols_parameter3='<?php echo $gkscols_parameter3;?>';
var from_php_gkscols_parameter4='<?php echo $gkscols_parameter4;?>';
var from_php_gkscols_parameter5='<?php echo $gkscols_parameter5;?>';
var from_php_gkscols_parameter6='<?php echo $gkscols_parameter6;?>';
var from_php_gkscols_parameter7='<?php echo $gkscols_parameter7;?>';

var from_php_gkscols_attach_param1='<?php echo $gkscols_attach_param1;?>';
var from_php_gkscols_attach_param2='<?php echo $gkscols_attach_param2;?>';
var from_php_gkscols_attach_param3='<?php echo $gkscols_attach_param3;?>';
var from_php_gkscols_attach_param4='<?php echo $gkscols_attach_param4;?>';
var from_php_gkscols_attach_param5='<?php echo $gkscols_attach_param5;?>';


var gks_fobjects_tags = [];
<?php 
  foreach ($gks_fobjects_tags as $value) {
     echo "  gks_fobjects_tags.push('".$value."');"."\n";
  } 
?> 

var from_php_enter_parameter_order=[];
<?php
$enter_parameter_order=array(
  'gks_fparam_label',
  'gks_fparam_type',
  'gks_fparam_px',
  'gks_fparam_icon',
  'gks_fparam_value',
  'gks_fparam_jquery_selector',
  'new_row',
);
foreach ($enter_parameter_order as $value) {
  echo 'from_php_enter_parameter_order.push(\''.$value.'\');'."\n";
}
?>
var from_php_enter_attach_param_order=[];
<?php
$enter_attach_param_order=array(
  'gks_attach_param_basefolder',
  'gks_attach_param_relative_path',
  'gks_attach_param_name_for_email',
  'new_row',
);
foreach ($enter_attach_param_order as $value) {
  echo 'from_php_enter_attach_param_order.push(\''.$value.'\');'."\n";
}
?>
  
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
});
</script>

<script src="js/admin-email-templates-item.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


