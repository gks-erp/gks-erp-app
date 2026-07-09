<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$nav_active_array=array('manage','manage_users');

db_open();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_users_extra_address',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$gks_voip_params=gks_voip_user_params();


if ($id==-1) {
//  $sql="insert into gks_users_groups (group_title,group_date_add,group_disable,
//  user_id_add,user_id_edit,mydate_add,mydate_edit,myip
//  ) values ('-draft ".time()."',NOW(),1,
//  ".$my_wp_user_id.",".$my_wp_user_id.",now(),now(),'".$db_link->escape_string($gkIP)."')";
//  $result = $db_link->query($sql);        
//  if (!$result) {
//    debug_mail(false,'error sql',$sql);
//    die('sql error');
//  }  
//  $id = $db_link->insert_id;
//  header('Location: ?id='.$id);
//  die();

  $row = array();
  $row['id_users_extra_address']=-1;
  $row['user_id'] =0;
  $row['gks_nickname'] ='';
  $row['ea_name'] ='';
  $row['ea_phone'] ='';
  $row['ea_branch'] ='';
  $row['ea_odos'] ='';
  $row['ea_arithmos'] ='';
  $row['ea_orofos'] ='';
  $row['ea_perioxi'] ='';
  $row['ea_poli'] ='';
  $row['ea_tk'] ='';
  $row['ea_country_id'] =91;
  $row['ea_nomos_id'] =0;
  $row['ea_latitude']='';
  $row['ea_longitude']='';

  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  
  $my_page_title=gks_lang('Νέα Διεύθυνση');
  
  $user_id=0; if (isset($_GET['user_id'])) $user_id=intval($_GET['user_id']);
  if ($user_id>0) {
    $sql="SELECT ".GKS_WP_TABLE_PREFIX."users.gks_nickname, gks_users.ma_country_id, gks_users.ma_nomos_id
    FROM ".GKS_WP_TABLE_PREFIX."users LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id
    WHERE ".GKS_WP_TABLE_PREFIX."users.ID=".$user_id;
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
    if ($result->num_rows==1) {
      $row_user = $result->fetch_assoc();  
      $row['user_id'] =$user_id;
      $row['gks_nickname']=trim_gks($row_user['gks_nickname']);
      $row['ea_country_id']=$row_user['ma_country_id'];
      $row['ea_nomos_id']=$row_user['ma_nomos_id'];
      
      $my_page_title=gks_lang('Νέα Διεύθυνση της επαφής').': '.$row['gks_nickname'];
    }    
    
  }
  
  
} else {
  $sql ="SELECT gks_users_extra_address.*, 
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit,
  ".GKS_WP_TABLE_PREFIX."users.gks_nickname, gks_country.country_name, gks_nomoi.nomos_descr
  FROM ((((gks_users_extra_address 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_users_extra_address.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_users_extra_address.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN gks_country ON gks_users_extra_address.ea_country_id = gks_country.id_country) 
  LEFT JOIN gks_nomoi ON gks_users_extra_address.ea_nomos_id = gks_nomoi.id_nomos) 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_users_extra_address.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE gks_users_extra_address.id_users_extra_address=".$id;
  
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
  $my_page_title=gks_lang('Διεύθυνση της επαφής').': '.$row['gks_nickname'].' '.$row['ea_name'];
}







stat_record();


include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h3><?php echo $my_page_title;?></h3>
    </div>
  </div>
</div>



<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">
      

        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Διεύθυνση');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('addr');?>>        
  
          <div class="form-group row">
            <label for="user" class="col-md-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επαφή');?>:</label>
            <div class="col-md-8">
              <input id="user" type="text" class="form-control form-control-sm myneedsave" 
              value="<?php echo htmlspecialchars_gks($row['gks_nickname']);?>" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
              <input id="user_id" type="hidden" value="<?php echo $row['user_id'];?>" class="myneedsave">
            </div>
  
          </div>
          <div class="form-group row">
            <label for="ea_name" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όνομα');?>:</label>
            <div class="col-md-8">
              <input id="ea_name" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ea_name']);?>" placeholder="<?php echo gks_lang('π.χ. Εξοχικό');?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="ea_phone" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τηλέφωνο');?>:</label>
            <div class="col-md-8">
              <input id="ea_phone" type="text" class="form-control form-control-sm myneedsave <?php echo $gks_voip_params['class_input'];?>" value="<?php echo htmlspecialchars_gks($row['ea_phone']);?>">
              <?php echo $gks_voip_params['html_after_input'];?>
            </div>
          </div> 
          <div class="form-group row">
            <label for="ea_branch" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός Εγκατάστασης');?>:</label>
            <div class="col-md-8">
              <input id="ea_branch" type="number" class="form-control form-control-sm myneedsave" value="<?php echo $row['ea_branch'];?>" min=0>
            </div>
          </div> 
          <div class="form-group row">
            <label for="ea_odos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Οδός');?>:</label>
            <div class="col-md-8">
              <input id="ea_odos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ea_odos']);?>">
              <small class="form-text text-muted auto_googlemaps" id="ea_odos_auto_googlemaps"></small>
            </div>
          </div> 
          <div class="form-group row">
            <label for="ea_arithmos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αριθμός');?>:</label>
            <div class="col-md-8">
              <input id="ea_arithmos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ea_arithmos']);?>">
            </div>
          </div> 
          
          <div class="form-group row">
            <label for="ea_orofos" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Όροφος');?>:</label>
            <div class="col-md-8">
              <input id="ea_orofos" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ea_orofos']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="ea_perioxi" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Περιοχή');?>:</label>
            <div class="col-md-8">
              <input id="ea_perioxi" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ea_perioxi']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="ea_poli" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Πόλη');?>:</label>
            <div class="col-md-8">
              <input id="ea_poli" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ea_poli']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="ea_tk" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('TK');?>:</label>
            <div class="col-md-8">
              <input id="ea_tk" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ea_tk']);?>">
            </div>
          </div> 
          <div class="form-group row">
            <label for="ea_nomos_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Νομός');?>:</label>
            <div class="col-md-8">
              <select id="ea_nomos_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $lang_prepare_gks_nomos=gks_lang_data_obj_prepare('gks_nomoi','default');
                gks_lang_data_obj_sql_prepare($lang_prepare_gks_nomos, array('nomos_descr'));
                $sql="select id_nomos,".gks_lang_sql_field('nomos_descr',$lang_prepare_gks_nomos)." 
                FROM ".$lang_prepare_gks_nomos['sql']['from1']." gks_nomoi 
                ".$lang_prepare_gks_nomos['sql']['from2']."
                where country_id=".$row['ea_country_id']." ORDER BY nomos_descr";
                
                $result_select = $db_link->query($sql);        
                if (!$result_select) {
                  debug_mail(false,'admin-users-item.php error sql',$sql);
                  die('sql error');
                }
                while ($row_select = $result_select->fetch_assoc()) {
                  echo '<option value="'.$row_select['id_nomos'].'" ';
                  if ($row_select['id_nomos']==$row['ea_nomos_id']) echo ' selected ';
                  echo '>'.$row_select['nomos_descr'].'</option>';
                }?>
              </select>    
            </div>
          </div>          
          <div class="form-group row">
            <label for="ea_country_id" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Χώρα');?>:</label>
            <div class="col-md-8">
              <select id="ea_country_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
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
                  echo '<option value="'.$row_select['id_country'].'" data-ci="'.$row_select['country_initials'].'" ';
                  if ($row_select['id_country']==$row['ea_country_id']) echo ' selected ';
                  echo '>'.$row_select['country_name'].'</option>';
                }?>
              </select>    
            </div>
          </div> 
          <div class="form-group row">
            <label for="ea_latitude" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γεωγραφικό Πλάτος');?>:</label>
            <div class="col-md-8">
              <input id="ea_latitude" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ea_latitude']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="-90" max="90" step="0.00001">
            </div>
          </div> 
          <div class="form-group row">
            <label for="ea_longitude" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Γεωγραφικό Μήκος');?>:</label>
            <div class="col-md-8">
              <input id="ea_longitude" type="number" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['ea_longitude']);?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" min="-180" max="180" step="0.00001">
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

    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>>      
  
  
  
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><input id="id_users_extra_address" type="text" readonly class="form-control-plaintext form-control-sm" value="<?php if ($row['id_users_extra_address']>0) echo $row['id_users_extra_address'];?>"></div>
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



<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">
      <button type="button" class="btn btn-primary" id="submit_button_ok"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_users_extra_address'];?>" data-model="gks_users_extra_address" data-backurl="admin-nomoi.php"><?php echo gks_lang('Διαγραφή');?></button>
      
    </div> 
  </div> 
</div> 

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var need_save=false;
var mychange = 'change keyup paste';
var gks_page_loading=true;

var from_php_temp_mypropertiesheight=<?php if (isset($_gks_session['temp_mypropertiesheight']) and $_gks_session['temp_mypropertiesheight']>0) {
    echo $_gks_session['temp_mypropertiesheight'];
    //echo '$("html").scrollTop('.$_gks_session['temp_mypropertiesheight'].');';
    unset($_gks_session['temp_mypropertiesheight']); gks_erp_cookie_save();
  } else { echo '0';}
  ?>;
var from_php_scrollto='<?php if (isset($_GET['scrollto'])) echo $_GET['scrollto'];?>'; 

  
var from_php_user_settings_autocomplete_address='<?php echo $gks_user_settings['autocomplete']['address'];?>';
  
var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_users_extra_address','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_users_extra_address','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_users_extra_address','delete',$id);?>;

var from_php_map_latitude=<?php echo floatval($row['ea_latitude']);?>;
var from_php_map_longitude=<?php echo floatval($row['ea_longitude']);?>;
  
jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>

  var control_enter_active=false;
  $(document).on('keypress', function(event) {
    if (event.which == 10 && event.ctrlKey) {
      control_enter_active=true;
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

    datasend+='&user_id='  + encodeURIComponent(($("#mypostform #user_id").val().trim()));
    datasend+='&ea_name='  + encodeURIComponent($.base64.encode($("#mypostform #ea_name").val().trim()));
    datasend+='&ea_phone='  + encodeURIComponent($.base64.encode($("#mypostform #ea_phone").val().trim()));
    datasend+='&ea_branch='  + encodeURIComponent($.base64.encode($("#mypostform #ea_branch").val().trim()));
    datasend+='&ea_odos='  + encodeURIComponent($.base64.encode($("#mypostform #ea_odos").val().trim()));
    datasend+='&ea_arithmos='  + encodeURIComponent($.base64.encode($("#mypostform #ea_arithmos").val().trim()));
    datasend+='&ea_orofos='  + encodeURIComponent($.base64.encode($("#mypostform #ea_orofos").val().trim()));
    datasend+='&ea_perioxi='  + encodeURIComponent($.base64.encode($("#mypostform #ea_perioxi").val().trim()));
    datasend+='&ea_poli='  + encodeURIComponent($.base64.encode($("#mypostform #ea_poli").val().trim()));
    datasend+='&ea_tk='  + encodeURIComponent($.base64.encode($("#mypostform #ea_tk").val().trim()));
    datasend+='&ea_country_id='  + encodeURIComponent(($("#mypostform #ea_country_id").val().trim()));
    datasend+='&ea_nomos_id='  + encodeURIComponent(($("#mypostform #ea_nomos_id").val().trim()));
    datasend+='&ea_latitude='  + encodeURIComponent($("#mypostform #ea_latitude").val().trim());
    datasend+='&ea_longitude='  + encodeURIComponent($("#mypostform #ea_longitude").val().trim());
    
    //console.log(datasend);
    //return;
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-users-extra_address-item-exec.php?id=' + <?php echo $id;?>,
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
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     

    return false;
  }
  

  
 

  $('#user').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
        all:1,
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
      $("#user_id").val(ui.item.id);
    },
    change: function (event, ui) {
        if(!ui.item){
          $("#user").val("");
          $("#user_id").val("");
        }
    }
  });
  

  $('#ea_country_id').change(function() {
    var v=$(this).val();
    nomos_fill('ea_nomos_id',v,0);
  });  
  
<?php if ($id==-1) {?>
  v=parseInt($('#ea_country_id').val());
  if (isNaN()) v=0;
  if (v>0) nomos_fill('ea_nomos_id',v,0);
<?php } ?>  
  
  gks_address_autocomplete('ea_odos','ea_arithmos','ea_orofos','ea_perioxi','ea_poli','ea_tk','ea_nomos_id','ea_country_id','ea_latitude','ea_longitude',true);


  $('#showmap').click(function(event) {  
    if (map_is_open==false) {
    
      $('#map').parent().css('height','500px').css('margin-top','10px');
      showmap_run();
      $('#showmap').html(gks_lang('Απόκρυψη χάρτη'));
      $('#map_pos, #geocode_pos').prop('disabled',false);
    } else {
      if ($('#showmap').html() ==gks_lang('Απόκρυψη χάρτη')) {
        $('#map_pos, #geocode_pos').prop('disabled',true);
        $('#showmap').html(gks_lang('Εμφάνιση χάρτη'));
        $('#map').parent().hide();
      } else {
        $('#map_pos, #geocode_pos').prop('disabled',false);
        $('#showmap').html(gks_lang('Απόκρυψη χάρτη'));
        $('#map').parent().show();
      }
    }
    gks_myscroll();
  });

  

 
  $('#map_pos').click(function(event){
    if (infoWindow_userpos==null) infoWindow_userpos = new google.maps.InfoWindow({map: map});
    
    // Try HTML5 geolocation.
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
        var pos = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
  
        
        infoWindow_userpos.setContent(gks_lang('Η τοποθεσία σας έχει εντοπιστεί'));
        map.setCenter(pos);
        
          
        marker.position=pos;
        place_map_latitude = marker.position.lat;
        place_map_longitude = marker.position.lng;
        infoWindow_userpos.open(map, marker);
        map.setZoom(17);
      
        
          
        $('#ea_latitude').val(place_map_latitude);
        $('#ea_longitude').val(place_map_longitude);
        need_save=true;
        
      }, function() {
        handleLocationError(true, infoWindow_userpos, map.getCenter());
      });
    } else {
      // Browser doesn't support Geolocation
      handleLocationError(false, infoWindow_userpos, map.getCenter());
    }
        
  });
  
  $('#geocode_pos').tooltipster();
  $('#geocode_pos').click(function() {
    
    datasend='';
    datasend+='&odos='  + encodeURIComponent($.base64.encode($("#ea_odos").val().trim()));
    datasend+='&arithmos='  + encodeURIComponent($.base64.encode($("#ea_arithmos").val().trim()));
    datasend+='&orofos='  + encodeURIComponent($.base64.encode($("#ea_orofos").val().trim()));
    datasend+='&perioxi='  + encodeURIComponent($.base64.encode($("#ea_perioxi").val().trim()));
    datasend+='&poli='  + encodeURIComponent($.base64.encode($("#ea_poli").val().trim()));
    datasend+='&tk='  + encodeURIComponent($.base64.encode($("#ea_tk").val().trim()));
    datasend+='&country_id='  + encodeURIComponent($("#ea_country_id").val().trim());
    datasend+='&nomos_id='  + encodeURIComponent($("#ea_nomos_id").val().trim());
    
    $('#geocode_pos').prop('disabled',true);
    $('#geocode_pos_icon').html('<i class="fas fa-hourglass"></i>');
    //console.log(datasend);
    $.ajax({
			url: '/my/admin-get-geocode_pos.php',
			type: 'POST',
			cache: false,
			dataType: 'json',
			data: datasend,
			error : function(jqXHR ,textStatus,  errorThrown) {
			  $('#geocode_pos').prop('disabled',false);
			  $('#geocode_pos_icon').html('<i class="fas fa-exclamation-circle"></i>').parent().tooltipster('destroy').attr('title',gks_lang('Σφάλμα') + ': ' + jqXHR.responseText).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
			},				
			success: function(data) {
			  $('#geocode_pos').prop('disabled',false);
				if (!data) {
				  $('#geocode_pos_icon').html('<i class="fas fa-exclamation-circle"></i>').parent().tooltipster('destroy').attr('title',gks_lang('Σφάλμα') + ': Παρακαλώ δοκιμάστε αργότερα').tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
					myalert('error:' + gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
				} else {
					if (data.success == true) {
					  $('#ea_latitude' ).val(data.pos.lat);
					  $('#ea_longitude').val(data.pos.lng);

            var pos = {lat: data.pos.lat,lng: data.pos.lng};      
            marker.position=pos;
            map.setOptions({center: pos});
            map.setOptions({zoom: 17});
            					  
					  $('#geocode_pos_icon').html('<i class="fas fa-check-circle"></i>').parent().tooltipster('destroy').attr('title','GEO:' + data.pos.lat + ',' + data.pos.lng).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
					} else {
					  $('#geocode_pos_icon').html('<i class="fas fa-exclamation-circle"></i>').parent().tooltipster('destroy').attr('title',gks_lang('Σφάλμα') + ': ' + $.base64.decode(data.message)).tooltipster({theme: 'tooltipster-noir',contentAsHTML: true, interactive:true}).tooltipster('show');
					}
				}
			}
			
		});
  });
  
  
  $('#ea_latitude, #ea_longitude').on(mychange,function() {
    lat=parseFloat($('#ea_latitude').val());
    lng=parseFloat($('#ea_longitude').val());
    gks_this_map_set_pos(lat,lng);
  });


  //generic
  gks_page_loading=false;
  
  if (from_php_scrollto!='') {
    if ($('#' + from_php_scrollto).length>0) {
      $([document.documentElement, document.body]).animate({
          scrollTop: $('#' + from_php_scrollto).offset().top
      }, 500);
    }
    if (window.location.href.endsWith('&scrollto=' + from_php_scrollto)) {
      newurl=window.location.href;
      newurl=newurl.substring(0,newurl.length-('&scrollto=' + from_php_scrollto).length);
      
      window.history.pushState({}, window.document.title, newurl);
    }
  } else if (from_php_temp_mypropertiesheight!=0) {
    $("html").scrollTop(from_php_temp_mypropertiesheight);
  }



  $('.myneedsave').on('input keyup paste', function() {
    need_save=true; 
  });

  window.onbeforeunload = function() {
    if (need_save==false) return;
    return gks_lang('Δεν έχουν αποθηκευτεί οι αλλαγές. Σίγουρα θέλετε να αφήσετε την σελίδα ;');
  };

  need_save=false;  
       
});

var map;
var marker;
var place_map_latitude = from_php_map_latitude;
var place_map_longitude = from_php_map_longitude;
var myLatLng;
var infoWindow_userpos=null;

function initMap() {
  map = new google.maps.Map(document.getElementById('map'), {
    center: myLatLng,
    zoom: 17,
    mapId: "gks1234567890",
  });
  marker = new google.maps.marker.AdvancedMarkerElement({
    position: myLatLng,
    map: map,
    title: gks_lang('Τοποθεσία'),
    gmpDraggable: true,
  });
    
}

function handleEvent_Marker(event) {
    document.getElementById('ea_latitude').value = event.latLng.lat();
    document.getElementById('ea_longitude').value = event.latLng.lng();
}
 
var map_is_open=false;
function showmap_run() {
  if (place_map_latitude == 0 && place_map_longitude == 0) {
    //place_map_latitude  = 40.6444460;
    //place_map_longitude = 22.914514;
    
    // Try HTML5 geolocation.
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position) {
          var pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          };      
          place_map_latitude = position.coords.latitude;
          place_map_longitude = position.coords.longitude;
          myLatLng = {lat: place_map_latitude, lng: place_map_longitude};
          marker.position=pos;
          map.setOptions({center: pos});
          map.setOptions({zoom: 17});
          
          jQuery('#ea_latitude').val(place_map_latitude);
          jQuery('#ea_longitude').val(place_map_longitude);
            
          need_save=true;
          
          //console.log('2' + myLatLng);
      }, function() {
        
      });
    } 
  }      

  myLatLng = {lat: place_map_latitude, lng: place_map_longitude};

  initMap();
  marker.addListener('drag', handleEvent_Marker);
  marker.addListener('dragend', handleEvent_Marker);
  map_is_open=true;
  
}
window.gks_this_map_set_pos = function(lat,lng) {
  place_map_latitude=lat;
  place_map_longitude=lng;
  
  myLatLng = {lat: lat, lng: lng};
  if (typeof marker != 'undefined') marker.position=myLatLng;
  if (typeof marker != 'undefined') map.setOptions({center: myLatLng});
  //map.setOptions({zoom: 17});
}
</script>
  



<?php

echo gks_from_googlemaps_scripts();


//db_close();
include_once('_my_footer_admin.php');


