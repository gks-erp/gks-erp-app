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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_bank_accounts',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

$gks_custom_prepare = gks_custom_table_item_prepare('gks_bank_accounts',['from'=>'item']);




if ($id<=0) {
  if (isset($_POST['id'])) $id=intval($_POST['id']);
}

if ($id==-1) {
  $row=array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_bank_account']=-1;
  $row['user_id']=0;
  $row['gks_nickname']='';
  $row['IBAN']='';
  $row['account_number']='';
  $row['bank_id']=0;
  $row['bank_descr']='';
  $row['account_type']='';
  $row['account_descr']='';
  $row['show_eshop']=0;
  $row['account_dikaiouxos']='';
  $row['deleted_from_user']=0;
  $row['bank_account_disable']=0;

  if (isset($_GET['user_id'])) {
    $ttt=intval($_GET['user_id']);  
    if ($ttt>0) {
      $sql_du="select ID, gks_nickname from ".GKS_WP_TABLE_PREFIX."users where ID=".$ttt;
      $result_du = $db_link->query($sql_du);        
      if (!$result_du) debug_mail(false,'error sql',$sql_du);
      if (!$result_du) die('sql error');
      if ($result_du->num_rows==1) {
        $row_du = $result_du->fetch_assoc();
        $row['user_id']=$row_du['ID'];
        $row['gks_nickname']=$row_du['gks_nickname'];
      }
    }
  }

  $my_page_title=gks_lang('Νέος Τραπεζικός λογαριασμός');



} else {
 $sql ="SELECT gks_bank_accounts.*,
  ".GKS_WP_TABLE_PREFIX."users.gks_nickname, gks_banks.bank_descr,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit
  FROM (((gks_bank_accounts
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_bank_accounts.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_bank_accounts.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_bank_accounts.user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
  LEFT JOIN gks_banks ON gks_bank_accounts.bank_id = gks_banks.id_bank
  
  
  where id_bank_account = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Τραπεζικός λογαριασμός').': '.$row['account_number'];
  $object_title=$row['account_number'];
}

$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);



stat_record();
$nav_active_array=array('manage','manage_bank_accounts');



include_once('_my_header_admin.php');
?>
<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <?php if ($id > 0) {?>
        <h3><?php echo gks_lang('Τραπεζικός λογαριασμός');?>: <span class="gks_object_badge_secondary">#<?php echo $id;?></span> <?php echo $object_title;?></h3>
      <?php } else { ?>
        <h3><?php echo gks_lang('Τραπεζικός λογαριασμός');?>: <span class="gks_object_badge_secondary">#<?php echo gks_lang('Νέος');?></span></h3>
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
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="account_descr"><?php echo gks_lang('Περιγραφή');?>:</label>
            <div class="col-sm-8">
              <input type="text" class="form-control form-control-sm myneedsave" id="account_descr"  value="<?php echo htmlspecialchars_gks($row['account_descr']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="IBAN"><?php echo gks_lang('IBAN');?>:</label>
            <div class="col-sm-8">
              <?php
                $iban_print=$row['IBAN'];
                $iban = iban_to_machine_format($iban_print);
                $iban_is_verify=false;
                if(verify_iban($iban)) {
                  $iban_is_verify=true;
                  $iban_print=iban_to_human_format($iban);
                }
              ?>
              <input type="text" class="form-control form-control-sm myneedsave" id="IBAN"  value="<?php echo htmlspecialchars_gks($iban_print);?>">
              <small id="iban_check" style="display:<?php echo (($iban_is_verify or $iban=='') ? 'none;' : '');?>">
                <?php echo gks_lang('Tο ΙΒΑΝ είναι');?> <span style="color:red;font-weight: bold;"><?php echo gks_lang('λάθος');?></span>
              </small>
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="account_number"><?php echo gks_lang('Αριθμός');?>:</label>
            <div class="col-sm-8">
              <input type="text" class="form-control form-control-sm myneedsave" id="account_number"  value="<?php echo htmlspecialchars_gks($row['account_number']);?>">
            </div>
          </div>

          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="bank_id"><?php echo gks_lang('Τράπεζα');?>:</label>
            <div class="col-sm-8">
              <input type="text" class="form-control form-control-sm myneedsave" id="bank_id" data-id="<?php echo $row['bank_id'];?>" value="<?php echo htmlspecialchars_gks($row['bank_descr']);?>" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
            </div>
          </div>  
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="account_type"><?php echo gks_lang('Τύπος');?>:</label>
            <div class="col-sm-8">
              <input type="text" class="form-control form-control-sm myneedsave" id="account_type"  value="<?php echo htmlspecialchars_gks($row['account_type']);?>" placeholder="<?php echo gks_lang('π.χ. Όψεως, μισθοδοσίας, προσωπικός');?>">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="account_dikaiouxos"><?php echo gks_lang('Δικαιούχος');?>:</label>
            <div class="col-sm-8">
              <input type="text" class="form-control form-control-sm myneedsave" id="account_dikaiouxos"  value="<?php echo htmlspecialchars_gks($row['account_dikaiouxos']);?>">
            </div>
          </div>
          <div class="form-group row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right" for="user_id"><?php echo gks_lang('Επαφή');?>:</label>
            <div class="col-sm-8">
              <input type="text" class="form-control form-control-sm myneedsave" id="user_id" data-id="<?php echo $row['user_id'];?>" value="<?php echo htmlspecialchars_gks($row['gks_nickname']);?>" placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
            </div>
          </div>          
          <div class="form-group row">
            <label for="show_eshop" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Προβολή στο eshop');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="show_eshop" value="1" <?php if ($row['show_eshop']==1) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>
          <div class="form-group row">
            <label for="deleted_from_user" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Διαγραφή από χρήστη');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="deleted_from_user" value="1" <?php if ($row['deleted_from_user']==1) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>          
          <div class="form-group row">
            <label for="bank_account_disable" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ενεργός');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="bank_account_disable" value="1" <?php if ($row['bank_account_disable']==0) echo ' checked '; ?> class="switchery1_this">
            </div>
          </div>                    

        </div>
      </div>

    </div>

    <div class="col-md-6">
      <?php gks_erp_app_purchase_ads_fix_item_card();?>
      
<?php
echo $gks_custom_row['html'];
//echo '<pre>';print_r($gks_custom_row);print '</pre>';
?>


    </div>
  </div>
</div>
      
<div id="gks_rsrv_f_pos"></div>
<div class="container-fluid" id="gks_rsrv_f">
  <div class="form-group1 row">
    <div class="col-md-12 text-center mt-2">

      <button type="button" class="btn btn-primary" id="submit_button_ok_custom"><?php echo gks_lang('Αποθήκευση');?></button>
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_bank_account'];?>" data-model="gks_bank_accounts" data-backurl="admin-bank_accounts.php" <?php if ($id<=0) echo 'disabled';?>><?php echo gks_lang('Διαγραφή');?></button>

    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>


    




<div class="container-fluid">
  <div class="row">
      
    <div class="col-md-6">

      <?php 
      echo getObjectRels('gks_bank_accounts',$id);
      echo getActivityObjectTable('gks_bank_accounts',$id);
      $obj_fileslist= gks_FilesObjectList(array('objname'=>'gks_bank_accounts','id'=>$id));
      echo $obj_fileslist['html'];
      ?>

      

      
    </div>

    <div class="col-md-6">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('kat');?>> 
          
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_bank_account']>0) echo $row['id_bank_account'];?></span></div>
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



var from_php_dialog_object_rel_curr='gks_bank_accounts';
var from_php_activity_model='gks_bank_accounts';
var from_php_activity_model_id=<?php echo $id;?>;
var from_php_activity_def_user_id=<?php echo $my_wp_user_id;?>;
var from_php_activity_def_user_name=$.base64.decode('<?php echo base64_encode($my_wp_user_info->gks_nickname);?>');


var from_php_id=<?php echo $id;?>;


var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_bank_accounts','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_bank_accounts','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_bank_accounts','delete',$id);?>;


jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  
  var control_enter_active=false;
  $(document).on('keypress', function(event) {
    if (event.which == 10 && event.ctrlKey) {
      control_enter_active=true;
      event.preventDefault();
      event.stopPropagation();
      elem=$('#submit_button_ok_custom');
      if (elem.is(":visible")) {
        elem.click();  
      }
      setTimeout(function(){control_enter_active=false; }, 300);
    }  
  });    
  
 
  $('#submit_button_ok_custom').click(function(event) {mysubmit(''); return false;});  
  function mysubmit() {
    
    datasend='';

    datasend+='&account_descr='  + encodeURIComponent($.base64.encode($("#mypostform #account_descr").val().trim()));
    datasend+='&IBAN='  + encodeURIComponent($.base64.encode($("#mypostform #IBAN").val().trim()));
    datasend+='&account_number='  + encodeURIComponent($.base64.encode($("#mypostform #account_number").val().trim()));
    datasend+='&bank_id='  + $("#mypostform #bank_id").attr('data-id');
    datasend+='&account_type='  + encodeURIComponent($.base64.encode($("#mypostform #account_type").val().trim()));
    datasend+='&account_dikaiouxos='  + encodeURIComponent($.base64.encode($("#mypostform #account_dikaiouxos").val().trim()));
    datasend+='&user_id='  + $("#mypostform #user_id").attr('data-id');
    datasend+='&show_eshop=' + (($('#mypostform #show_eshop').is(':checked')) ? '1':'0');
    datasend+='&deleted_from_user=' + (($('#mypostform #deleted_from_user').is(':checked')) ? '1':'0');
    datasend+='&bank_account_disable=' + (($('#mypostform #bank_account_disable').is(':checked')) ? '0':'1');
    
    datasend+=gks_custom_datasend();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-bank_accounts-item-exec.php?id=' + <?php echo $id;?>,
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
  
  



  var elems_switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  elems_switchery1_this.forEach(function(html) {
    var switchery1_this = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
  });
  

  $('#bank_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
      };
      $.ajax({
        url: 'admin-autocomplete-bank.php',
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
      $("#bank_id").attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        need_save=true;
        $("#bank_id").val('').attr('data-id','0');
      }
    }
  });


  $('#user_id').autocomplete({
    source: function(request, response) {
      mydata={
        term: request.term,
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
      $("#user_id").attr('data-id',ui.item.id);
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        need_save=true;
        $("#user_id").val('').attr('data-id','0');
      }
    }
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

 
 
</script>


<?php
echo $obj_fileslist['vars'];
echo $obj_fileslist['fileupload_files'];
echo $obj_fileslist['js_files'];

include_once('_my_footer_admin.php');


