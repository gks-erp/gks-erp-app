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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_production_posta',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}





$gks_custom_prepare = gks_custom_table_item_prepare('gks_production_posta',['from'=>'item']);



if ($id == 0 or $id < -1) {header('Location: /my'); die(); }

if ($id==-1) {
  $my_page_title=gks_lang('Νέο Πόστο');
  $row=array();
  
  $row['user_id_add']=0;
  $row['gks_nickname_add']='';
  $row['mydate_add']=null;
  $row['user_id_edit']=0;
  $row['gks_nickname_edit']='';
  $row['mydate_edit']=null;
  $row['myip']='';

  $row['id_production_posto']=-1;
  $row['production_posto_descr']='';
  $row['bypass_time']=0;
  $row['all_users']=0;
  $row['production_posto_sortorder']=1000;


} else {
    
  $sql ="SELECT gks_production_posta.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit
  FROM gks_production_posta
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_production_posta.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_production_posta.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
  where id_production_posto = ".$id;
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

  $my_page_title=gks_lang('Πόστο').': '.$row['production_posto_descr'];
  $object_title=$row['production_posto_descr'];
}

$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);

stat_record();
$nav_active_array=array('production','production_posta');


include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Πόστο');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Πόστο');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέο');?></span></h3>
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
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="production_posto_descr"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-sm-8">
              <input type="text" class="form-control form-control-sm" id="production_posto_descr"  value="<?php echo htmlspecialchars_gks($row['production_posto_descr']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label for="bypass_time" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μέτρηση χρόνου');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" name="bypass_time"  id="bypass_time" value="1" <?php if ($row['bypass_time']==0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div> 
          <div class="form-group row">
            <label for="all_users" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ανάληψη από άλλον χρήστη');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" name="all_users"  id="all_users" value="1" <?php if ($row['all_users']!=0) echo ' checked '; ?> class="switchery1_sel">
            </div>
          </div>                                     
          <div class="form-group row">
            <label for="production_posto_sortorder" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σειρά Ταξινόμησης');?>:</label>
            <div class="col-md-8">
              <input id="production_posto_sortorder" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['production_posto_sortorder'];?>" min="0" step="1">
            </div>
          </div> 


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
              
<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_ok"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_production_posto'];?>" data-model="gks_production_posta" data-backurl="admin-production-posta.php"><?php echo gks_lang('Διαγραφή');?></button>
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-6">
              
      

      <?php 
      echo getObjectRels('gks_production_posta',$id);
      echo getActivityObjectTable('gks_production_posta',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_production_posta','id'=>$id));
      echo $obj_fileslist['html'];
      ?>
                    


              
    </div>

    <div class="col-md-6">
      
        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Εργασίες');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('erg');?>>        

          <?php
          $query = "SELECT gks_production_posta_ergasies.*, gks_production_ergasies.production_ergasia_descr
          FROM gks_production_posta_ergasies 
          LEFT JOIN gks_production_ergasies ON gks_production_posta_ergasies.production_ergasia_id = gks_production_ergasies.id_production_ergasia
          WHERE gks_production_posta_ergasies.production_posto_id=".$id."
          ORDER BY gks_production_ergasies.production_ergasia_sortorder, gks_production_ergasies.production_ergasia_descr";
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Εργασία');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  ><?php echo gks_lang('Ημερομηνία');?></th>        

            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" id="tr_<?php echo $row_list['id_production_posta_ergasies'];?>">
              <th scope="row" nowrap align="right"><?php echo ($i);?></td>       
              <td nowrap align="center">
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-id="<?php echo $row_list['id_production_posta_ergasies'];?>" data-model="gks_production_posta_ergasies">            
              </td>
              <td nowrap align="center"><a href="admin-production-ergasies-item.php?id=<?php echo $row_list['production_ergasia_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a></td>
              <td nowrap><?php echo $row_list['production_ergasia_descr'].'</a>';?></td>  
              <td nowrap><?php echo showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
            </tr>
          <?php } ?>


            <tr class="" id="tr_new">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <input type="text"   name="ergasia"    id="ergasia"   class="form-control" style="width:98%;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                <input type="hidden" name="ergasia_id" id="ergasia_id">
              </td>  
            </tr>
            <tr class="" id="tr_new_button">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center"></td>
              <td nowrap colspan="5">
                <button style="justify-content: center!important;" type="button" class="btn btn-sm btn-primary" id="add_ergasia"><?php echo gks_lang('Προσθήκη');?></button>
              </td>  
            </tr>      
          </tbody>
          </table>      

        </div>
      </div>                
                
     
      
      
        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Υπάλληλοι');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('ypal');?>>        

          <?php
          $query = "SELECT gks_production_posta_users.*, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, ".GKS_WP_TABLE_PREFIX."users.gks_wp_capabilities,".GKS_WP_TABLE_PREFIX."users.gks_wsl_current_user_image,
          ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname AS gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname AS gks_nickname_edit
          FROM ((gks_production_posta_users 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_production_posta_users.production_user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_add ON gks_production_posta_users.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID) 
          LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_edit ON gks_production_posta_users.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID
          WHERE production_posto_id=".$id."
          ORDER BY ".GKS_WP_TABLE_PREFIX."users.gks_nickname;";
          $result_list = $db_link->query($query); 
          if (!$result_list) debug_mail(false,'error sql',$query);
          if (!$result_list) die('sql error');
          ?>                  
          <table class="table table-sm table-responsive table-striped table-bordered gkstable100" border="0" cellspacing="0" cellpadding="5" align="center">
          <thead>
            <tr>	
              <th class="table-dark" scope="col" style="text-align: center !important;" width='0%'  >#</th>
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="0%" ></th> 
              <th class="table-dark" scope="col" style="text-align: center !important;" width="100%"><?php echo gks_lang('Υπάλληλος');?></th> 
              <th class="table-dark" scope="col" style="text-align: left   !important;" width="0%"  ><?php echo gks_lang('Ημερομηνία');?></th>        

            </tr>
          </thead>
          <tbody>
          <?php
          $i = 0;
          while ($row_list = $result_list->fetch_assoc()) {
          
            $i++;
            ?>
            <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>" id="tr_<?php echo $row_list['id_production_posto_user'];?>">
              <th scope="row" nowrap align="right"><?php echo ($i);?></td>       
              <td nowrap align="center">
                <img src="img/delete.png" border="0" width="16" class="deleterow" data-id="<?php echo $row_list['id_production_posto_user'];?>" data-model="gks_production_posta_users">            
              </td>
              <td><?php echo getUserPhoto($row_list['production_user_id'],$row_list['gks_wsl_current_user_image'],32);?></td>
              <td nowrap><?php echo '<a href="admin-users-item.php?id='.$row_list['production_user_id'].'">'.$row_list['gks_nickname'].'</a>';?>
              <?php
              if (!(strpos($row_list['gks_wp_capabilities'], '"employee"') !== false)) {
                echo '<br><span style="color:#ff0000">'.gks_lang('Προσοχή! Δεν έχει δικαιώματα υπαλλήλου').'</span>';
              }?>
              </td>  
              
              <td nowrap><?php echo showDate(strtotime($row_list['mydate_add']), 'd/m/Y H:i:s', 1);?></td>   
            </tr>
          <?php } ?>


            <tr class="" id="tr_new">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center" style="vertical-align: middle;">
                <i class="fas fa-plus-circle" style="color: #35dc35;font-size: 150%;"></i>
              </td>
              <td nowrap colspan="5">
                <input type="text"   name="production_user"    id="production_user"   class="form-control" style="width:98%;" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
                <input type="hidden" name="production_user_id" id="production_user_id">
              </td>  
            </tr>
            <tr class="" id="tr_new_button">
              <th scope="row" nowrap align="right"></td>      
              <td nowrap align="center"></td>
              <td nowrap colspan="5">
                <button style="justify-content: center!important;" type="button" class="btn btn-sm btn-primary" id="add_production_user"><?php echo gks_lang('Προσθήκη');?></button>
              </td>  
            </tr>      
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
            <div class="col-sm-8"><input id="id_nomos" type="text" readonly class="form-control-plaintext form-control-sm" value="<?php if ($row['id_production_posto']>0) echo $row['id_production_posto'];?>"></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-users-item.php?id='.$row['user_id_add'].'">'.$row['gks_nickname_add'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Προσθήκη στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_add'])) echo showDate(strtotime($row['mydate_add']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία από');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-users-item.php?id='.$row['user_id_edit'].'">'.$row['gks_nickname_edit'].'</a>';?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επεξεργασία στις');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if (isset($row['mydate_edit'])) echo showDate(strtotime($row['mydate_edit']), 'd/m/Y H:i:s', 1);?></span></div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('IP');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php echo '<a href="admin-stat-ip.php?ip='.$row['myip'].'">'.$row['myip'].'</a>';?></span></div>
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


var from_php_dialog_object_rel_curr='gks_production_posta';
var from_php_activity_model='gks_production_posta';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');

var from_php_id=<?php echo $id;?>;

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_production_posta','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_production_posta','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_production_posta','delete',$id);?>;


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
  
  


 
    
  function mysubmit() {
    
    datasend='';


    datasend+='&production_posto_descr='  + encodeURI($("#mypostform #production_posto_descr").val().trim());
    datasend+='&bypass_time=' + (($('#mypostform #bypass_time').is(':checked')) ? '0':'1');
    datasend+='&all_users=' + (($('#mypostform #all_users').is(':checked')) ? '1':'0');
    datasend+='&production_posto_sortorder='  + $("#mypostform #production_posto_sortorder").val().trim();
    
    datasend+=gks_custom_datasend();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-production-posta-item-exec.php?id=' + <?php echo $id;?>,
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
            if (data.redirect=='') {
  					  window.location.reload();
  					} else {
  					  window.location.href = $.base64.decode(data.redirect);
  					}
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     

    return false;
  }  
  


  $('#production_user').autocomplete({
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
    delay: 300, //default
    select: function( event, ui ) {
      $("#production_user_id").val(ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#production_user").val("");
          $("#production_user_id").val("");
        }
    }
  });  
  
  $('#add_production_user').click(function(event) {  
    
    datasend='';
    datasend+='id= <?php echo $id;?>';    
    datasend+='&user_id='  + encodeURI($("#production_user_id").val().trim());    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-production-posta-item-user_add.php',
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
  					//myalert('ok:' + 'OK');
  					window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  });    


  $('#ergasia').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        eml: 1,
      };
      $.ajax({
        url: 'admin-autocomplete-ergasies.php',
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
    delay: 300, //default
    select: function( event, ui ) {
      $("#ergasia_id").val(ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#ergasia").val("");
          $("#ergasia_id").val("");
        }
    }
  }); 


  $('#add_ergasia').click(function(event) {  
    
    datasend='';
    datasend+='posto_id= <?php echo $id;?>';    
    datasend+='&ergasia_id='  + encodeURI($("#ergasia_id").val().trim());    
    //console.log(datasend);
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-production-ergasies-item-posto_add.php',
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
  					//myalert('ok:' + 'OK');
  					window.location.reload();
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     
    
    return false;
  });       
});
</script>


<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


