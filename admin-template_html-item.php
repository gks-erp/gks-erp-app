<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$nav_active_array=array('manage','manage_template_html');
db_open();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_template_html',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}





$gks_custom_prepare = gks_custom_table_item_prepare('gks_template_html',['from'=>'item']);


if ($id==-1) {
  
  $copy_id=0;
  if (isset($_GET['copy'])) $copy_id=intval($_GET['copy']);
  if ($copy_id > 0) {
    //echo time();
    //die();
    //product_photo
      
    $sql="INSERT INTO gks_template_html (
    template_html_descr,
    template_html_type,orders_online_url,orders_online_sms_sender,
    is_disable,
    gks_lang,edit_mode,
    html_part_1,html_part_2,html_part_3,html_part_4,html_part_5,
    html_part_6,html_part_7,html_part_8,html_part_9,
    custom_css,custom_javascript,
    mydate_add,mydate_edit,user_id_add,user_id_edit,myip)
    
    SELECT CONCAT(template_html_descr,' draft ".rand(1000,9999)."') as template_html_descr_new,
    template_html_type,orders_online_url,orders_online_sms_sender,
    0,
    gks_lang,edit_mode,
    html_part_1,html_part_2,html_part_3,html_part_4,html_part_5,
    html_part_6,html_part_7,html_part_8,html_part_9,
    custom_css,custom_javascript,
    now() as mydate_add, now() as mydate_edit, ".$my_wp_user_id." as user_id_add, ".$my_wp_user_id." as user_id_edit,'".$db_link->escape_string($gkIP)."' as myip
    from gks_template_html
    WHERE id_template_html=".$copy_id;
    
    //print '<pre>';
    //print $sql;
    //die();
    
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}  
    $id = $db_link->insert_id;

    if ($id > 0) {
      header('Location: ?id='.$id);
      die();
    }    
    
  }
    
  $row = array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_template_html']=-1;
  $row['template_html_descr']='';
  $row['template_html_type']=1;
  $row['gks_lang']='el-GR';
  $row['edit_mode']='html';
  $row['is_disable']=0;
  $row['orders_online_url']='';
  $row['orders_online_sms_sender']='';
  
  $row['html_part_1']='';
  $row['html_part_3']='';
  $row['html_part_5']='';
  $row['html_part_6']='';
  $row['html_part_7']='';
  $row['html_part_4']='';
  $row['html_part_2']='';
  $row['html_part_8']='';
  $row['html_part_9']='';
  $row['custom_css']='';
  $row['custom_javascript']='';

  
  
  $row['localization_set_id']=0;
  $row['sortorder']=1000;
  

  
  $my_page_title=gks_lang('Νέο Πρότυπο HTML');

  
  

} else {
  $sql ="SELECT gks_template_html.*,gks_template_html_type.template_html_type_descr,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit,
  gks_lang.lang_name
  FROM (((gks_template_html 
  LEFT JOIN gks_template_html_type ON gks_template_html.template_html_type = gks_template_html_type.id_template_html_type)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_template_html.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_template_html.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN gks_lang ON gks_template_html.gks_lang = gks_lang.id_lang
  where id_template_html = ".$id;
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
  $my_page_title=gks_lang('Πρότυπο HTML').': '.$row['template_html_descr'];
  $object_title=$row['template_html_descr'];
}

$edit_mode=$row['edit_mode'];


$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);



$localization_set_id=$row['localization_set_id'];





stat_record();



function this_html_add_code($html_in) {
  global $edit_mode;
  if ($edit_mode=='raw') return htmlspecialchars($html_in);
  
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
<style>
.gks_curr_tinymce {
  font-family:courier;  
}
.local_set_lang > option:disabled {
  background-color: lightgray;
  color: black;
  
}  
#edit_mode_div {
  display: flex;flex-direction: row;gap: 20px;justify-content: center;
}  
.edit_mode_div_disabled {
  opacity:0.5;  
}
</style>


<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Πρότυπο HTML');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Πρότυπο HTML');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέο');?></span></h3>
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
            <label for="template_html_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-md-8">
              <input id="template_html_descr" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['template_html_descr']);?>">
            </div>
          </div> 
          
          
          <div class="form-group row">
            <label for="template_html_type" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος');?>:</label>
            <div class="col-md-8">
              <select id="template_html_type" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $js_types=[];
                
                $sql="select * FROM gks_template_html_type 
                where type_is_disable=0 
                ORDER BY type_sortorder,template_html_type_descr";
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_template_html_type'].'" ';
                  if ($row_select['id_template_html_type']==$row['template_html_type']) echo ' selected ';
                  echo '>'.$row_select['template_html_type_descr'].'</option>';
                  
                  $js_types[]=array(
                    'id'=>intval($row_select['id_template_html_type']),
                    't'=>array(
                      gks_lang(trim_gks($row_select['html_part_1_title'])),
                      gks_lang(trim_gks($row_select['html_part_2_title'])),
                      gks_lang(trim_gks($row_select['html_part_3_title'])),
                      gks_lang(trim_gks($row_select['html_part_4_title'])),
                      gks_lang(trim_gks($row_select['html_part_5_title'])),
                      gks_lang(trim_gks($row_select['html_part_6_title'])),
                      gks_lang(trim_gks($row_select['html_part_7_title'])),
                      gks_lang(trim_gks($row_select['html_part_8_title'])),
                      gks_lang(trim_gks($row_select['html_part_9_title'])),
                    )
                  );

                }?>                
              </select>    
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
            <label for="orders_online_url" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('OnLine Προσφορά URL');?>:</label>
            <div class="col-md-8">
              <input id="orders_online_url" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['orders_online_url']);?>">
              <small class="form-text text-muted"><?php echo gks_lang('Το URL της σελίδας του Wordpress στην οποία θα υπάρχει το shortcode [gks_erp_sales_order_online]');?></small>
              <small class="form-text text-muted"><?php echo gks_lang('π.χ.').' '.GKS_SITE_URL;?>offers/</small>
            </div>
          </div> 
          
          <div class="form-group row">
            <label for="orders_online_sms_sender" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('OnLine Προσφορά αποστολή SMS από');?>:</label>
            <div class="col-md-8">
              <select id="orders_online_sms_sender" class="form-control form-control-sm myneedsave">
                <option value=""></option>
                <?php 
                
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
                  echo '<option value="gks_erp_app_mobile:'.$row_select['id_erp_app_mobile'].'" '.
                  'data-provider="gks_erp_app_mobile" '.
                  'data-sender="'.$row_select['id_erp_app_mobile'].'" ';
                  if ($row['orders_online_sms_sender'] == 'gks_erp_app_mobile:'.$row_select['id_erp_app_mobile']) echo ' selected ';
                  $is_offline='';
                  if (empty($row_select['mydate'])==false and strtotime($row_select['mydate']) >= (time() - 60*60)) { //mia ora, to elaxisto einai 15 lepta
                    $is_offline='';
                  } else {
                    $is_offline='disabled';
                  }            
                  //echo $is_offline;
                  echo '>App: '.$row_select['erp_app_mobile_name'].' '.$row_select['erp_app_mobile_phonenumber'];
                  if ($is_offline!='') echo ' - '.gks_lang('ανενεργό');
                  echo '</option>';
                }  
                $parts=explode(',',$GKS_SMS_SENDER);
                foreach ($parts as $value) {
                  $value=trim_gks($value);
                  if ($value!='') {
                    echo '<option value=smsapi:'.$value.' '.
                    'data-provider="smsapi" '.
                    'data-sender="'.$value.'" ';
                    if ($row['orders_online_sms_sender'] == 'smsapi:'.$value) echo ' selected ';
                    echo '>smsapi: '.$value.'</option>';
                  }
                }
                ?>
              </select>    
            </div>
          </div> 
          
          <div class="form-group row">
            <label for="sortorder" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-sm-8">
              <input id="sortorder" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['sortorder']);?>">
            </div>
          </div> 
                    
          <div class="form-group row">
            <label for="is_disable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργό');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="is_disable" value="1" <?php if ($row['is_disable']==0) echo ' checked '; ?> class="switchery1_this">
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
  $sql_localization="SELECT gks_template_html.id_template_html, gks_template_html.template_html_descr, gks_template_html.gks_lang, gks_lang.lang_name
  FROM gks_template_html LEFT JOIN gks_lang ON gks_template_html.gks_lang = gks_lang.id_lang
  WHERE gks_template_html.localization_set_id=".$localization_set_id."
  and gks_template_html.id_template_html<>".$id."
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
        <th class="table-dark" scope="col" style="text-align: center !important;" width="50%"><?php echo gks_lang('Πρότυπο HTML');?></th>        
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
      <input type="text" class="form-control form-control-sm myneedsave local_set_form" value="<?php echo $row_list['template_html_descr'];?>"
      data-id="<?php echo $local_set_index;?>"
      style="width:calc(100% - 22px);display:inline;" 
      placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>"
      data-form_id="<?php echo $row_list['id_template_html'];?>">
      <a class="local_set_form_link" data-id="<?php echo $local_set_index;?>" href="admin-template_html-item.php?id=<?php echo $row_list['id_template_html'];?>" tabindex="-1">
        <i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή Πρότυπου HTML');?>"></i>
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

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12" style="margin-top: 24px;margin-bottom: 24px;">
      <div id="edit_mode_div" >
        <div><?php echo gks_lang('Επεξεργασία');?>: </div>
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

<div class="container-fluid container_html_part" data-index="1">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          html_part_1
        </div>
        <div class="card-body" <?php echo gks_card_body('header');?>>
          <textarea class="gks_curr_tinymce form-control form-control-sm" type="text" style="width:100%;height:400px;" id="html_part_1" name="html_part_1"><?php echo this_html_add_code($row['html_part_1']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="container-fluid container_html_part" data-index="2">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          html_part_2
        </div>
        <div class="card-body" <?php echo gks_card_body('page2');?>>
          <textarea class="gks_curr_tinymce form-control form-control-sm" type="text" style="width:100%;height:400px;" id="html_part_2" name="html_part_2"><?php echo this_html_add_code($row['html_part_2']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div>  
<div class="container-fluid container_html_part" data-index="3">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          html_part_3
        </div>
        <div class="card-body" <?php echo gks_card_body('form1');?>>
          <textarea class="gks_curr_tinymce form-control form-control-sm" type="text" style="width:100%;height:400px;" id="html_part_3" name="html_part_3"><?php echo this_html_add_code($row['html_part_3']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="container-fluid container_html_part" data-index="4">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          html_part_4
        </div>
        <div class="card-body" <?php echo gks_card_body('form2');?>>
          <textarea class="gks_curr_tinymce form-control form-control-sm" type="text" style="width:100%;height:400px;" id="html_part_4" name="html_part_4"><?php echo this_html_add_code($row['html_part_4']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div> 
        
<div class="container-fluid container_html_part" data-index="5">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          html_part_5
        </div>
        <div class="card-body" <?php echo gks_card_body('detail1');?>>
          <textarea class="gks_curr_tinymce form-control form-control-sm" type="text" style="width:100%;height:400px;" id="html_part_5" name="html_part_5"><?php echo this_html_add_code($row['html_part_5']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid container_html_part" data-index="6">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          html_part_6
        </div>
        <div class="card-body" <?php echo gks_card_body('detail2');?>>
          <textarea class="gks_curr_tinymce form-control form-control-sm" type="text" style="width:100%;height:400px;" id="html_part_6" name="html_part_6"><?php echo this_html_add_code($row['html_part_6']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div>  
      
<div class="container-fluid container_html_part" data-index="7">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          html_part_7
        </div>
        <div class="card-body" <?php echo gks_card_body('detail3');?>>
          <textarea class="gks_curr_tinymce form-control form-control-sm" type="text" style="width:100%;height:400px;" id="html_part_7" name="html_part_7"><?php echo this_html_add_code($row['html_part_7']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div>        
<div class="container-fluid container_html_part" data-index="8">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          html_part_8
        </div>
        <div class="card-body" <?php echo gks_card_body('fpa');?>>
          <textarea class="gks_curr_tinymce form-control form-control-sm" type="text" style="width:100%;height:400px;" id="html_part_8" name="html_part_8"><?php echo this_html_add_code($row['html_part_8']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div> 

<div class="container-fluid container_html_part" data-index="9">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          html_part_9
        </div>
        <div class="card-body" <?php echo gks_card_body('anafor');?>>
          <textarea class="gks_curr_tinymce form-control form-control-sm" type="text" style="width:100%;height:400px;" id="html_part_9" name="html_part_9"><?php echo this_html_add_code($row['html_part_9']);?></textarea>
        </div>
      </div>
    </div>
  </div>
</div>



 
<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand" id="custom_css_div">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('CSS');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('css');?>>
          <div class="form-group row">
            <div class="col-md-12" style="text-align:left;">
              <textarea id="custom_css" style="border:1px solid black;"><?php echo $row['custom_css'];?></textarea>
            </div>
            <div class="col-md-12" style="text-align:left;">
              <small class="form-text text-muted"><?php echo gks_lang('Για προβολή μεγάλου παραθύρου επεξεργασίας πατήστε το πλήκτρο F11 ενώ είστε μέσα στον επεξεργαστή. Για επαναφορά πατήστε πάλι το F11 ή το esc');?></small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row">
    <div class="col-sm-12">
      <div class="card gks_card_expand" id="custom_javascript_div">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('JavaScript');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('javascript');?>>
          <div class="form-group row">
            <div class="col-md-12" style="text-align:left;">
              <textarea id="custom_javascript" style="border:1px solid black;"><?php echo $row['custom_javascript'];?></textarea>
            </div>
            <div class="col-md-12" style="text-align:left;">
              <small class="form-text text-muted"><?php echo gks_lang('Για προβολή μεγάλου παραθύρου επεξεργασίας πατήστε το πλήκτρο F11 ενώ είστε μέσα στον επεξεργαστή. Για επαναφορά πατήστε πάλι το F11 ή το esc');?></small>
            </div>
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
      <?php if ($id>0) {?>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_template_html'];?>" data-model="gks_template_html" data-backurl="admin-template_html.php"><?php echo gks_lang('Διαγραφή');?></button>
      <button id="submit_button_copy" type="button" class="btn btn-primary tooltipster" onclick="window.location.href='admin-template_html-item.php?id=-1&copy=<?php echo $id;?>'"  title="<?php echo gks_lang('Δημιουργία αντιγράφου');?>"><i class="fas fa-copy" style="font-size: 120%;"></i></button>
      <button id="submit_button_export" type="button" class="btn btn-primary tooltipster"  title="<?php echo gks_lang('Εξαγωγή');?>"><i class="fas fa-download" style="font-size: 120%;"></i></button>
      <?php } ?>
      <button id="submit_button_import" type="button" class="btn btn-primary tooltipster"  title="<?php echo gks_lang('Εισαγωγή');?>"><i class="fas fa-upload" style="font-size: 120%;"></i></button>
      <input id="submit_button_import_file" type="file" accept="application/json" style="display:none;"/>

    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-xl-6">
      
      <?php 
      echo getObjectRels('gks_template_html',$id);
      echo getActivityObjectTable('gks_template_html',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_template_html','id'=>$id));
      echo $obj_fileslist['html'];
      ?>
      

    </div>
    
    <div class="col-xl-6">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>      
  
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_template_html']>0) echo $row['id_template_html'];?></span></div>
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

var from_php_dialog_object_rel_curr='gks_template_html';
var from_php_activity_model='gks_template_html';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>; 



var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_template_html','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_template_html','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_template_html','delete',$id);?>;



var from_php_edit_mode='<?php echo $edit_mode;?>';


var from_php_js_types=JSON.parse($.base64.decode('<?php echo base64_encode(json_encode($js_types));?>'));

    
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
});



</script>


<link rel="stylesheet" href="/my/js/codemirror-5.65.16/lib/codemirror.css">
<link rel="stylesheet" href="/my/js/codemirror-5.65.16/addon/hint/show-hint.css">
<link rel="stylesheet" href="/my/js/codemirror-5.65.16/addon/fold/foldgutter.css" />
<link rel="stylesheet" href="/my/js/codemirror-5.65.16/addon/display/fullscreen.css">
<script src="/my/js/codemirror-5.65.16/lib/codemirror.js"></script>
<script src="/my/js/codemirror-5.65.16/mode/css/css.js"></script>
<script src="/my/js/codemirror-5.65.16/mode/javascript/javascript.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/hint/show-hint.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/hint/css-hint.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/edit/closebrackets.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/selection/active-line.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/fold/foldcode.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/fold/foldgutter.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/fold/brace-fold.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/fold/comment-fold.js"></script>
<script src="/my/js/codemirror-5.65.16/addon/display/fullscreen.js"></script>



<script src="js/admin-template_html-item.js?v=<?php echo $gks_cache_version;?>"></script>

<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


