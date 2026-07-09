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
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_orders_occasion',($id==-1 ? 'add' : 'view'),$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}







if ($id<=0) {
  if (isset($_POST['id'])) $id=intval($_POST['id']);
}
if ($id == 0 or $id < -1) {header('Location: /my'); die(); }



$gks_custom_prepare = gks_custom_table_item_prepare('gks_orders_occasion',['from'=>'item']);


if ($id==-1) {



  $row=array();
  $row['user_id_add'] =0;
  $row['user_id_edit'] =0;
  $row['gks_nickname_add'] ='';
  $row['gks_nickname_edit'] ='';
  $row['myip'] ='';
  $row['id_order_occasion']=-1;
  $row['user_id']=0;
  $row['gks_nickname'] ='';
  $row['title']='';
  $row['occasion_id']=0;
  $row['notes']='';
  $row['pay_method_id']=0;
  $row['notes']='';
  
  
  if (isset($_GET['user_id']) and intval($_GET['user_id'])) {
    $sql_new ="select ID,gks_nickname from ".GKS_WP_TABLE_PREFIX."users where ID=".intval($_GET['user_id']);
    $result_new = $db_link->query($sql_new);        
    if (!$result_new) debug_mail(false,'error sql',$sql_new);
    if (!$result_new) die('sql error');
    if ($result_new->num_rows>=1) {
      $row_new = $result_new->fetch_assoc();
      $row['gks_nickname']=$row_new['gks_nickname'];
      $row['user_id']=$row_new['ID'];
      
    }
    
    
    
  }
  
  $my_page_title=gks_lang('Νέα Περίσταση');
} else {
  $sql ="SELECT gks_orders_occasion.*,
  ".GKS_WP_TABLE_PREFIX."users_add.gks_nickname as gks_nickname_add, ".GKS_WP_TABLE_PREFIX."users_edit.gks_nickname as gks_nickname_edit,
  ".GKS_WP_TABLE_PREFIX."users.gks_nickname
  FROM ((gks_orders_occasion 
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_add on gks_orders_occasion.user_id_add = ".GKS_WP_TABLE_PREFIX."users_add.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users as ".GKS_WP_TABLE_PREFIX."users_edit on gks_orders_occasion.user_id_edit = ".GKS_WP_TABLE_PREFIX."users_edit.ID)
  LEFT JOIN ".GKS_WP_TABLE_PREFIX."users on gks_orders_occasion.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  
  
  
  where id_order_occasion = ".$id;
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  if ($result->num_rows!=1) die('record not found'); 
  $row = $result->fetch_assoc();
  $my_page_title=gks_lang('Περίσταση').': '.$row['title'];
}
$gks_custom_row = gks_custom_table_item_view($gks_custom_prepare,$row);


stat_record();

$nav_active_array=array('manage','manage_d');


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
          <?php echo gks_lang('Βασικά στοιχεία');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('bas');?>>         
          
          
          <div class="form-group row">
            <label for="user_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επαφή');?>:</label>
            <div class="col-sm-8">
              <input id="user_id" type="text" class="form-control form-control-sm myneedsave" 
              data-id="<?php echo $row['user_id'];?>" value="<?php echo htmlspecialchars_gks($row['gks_nickname']);?>"
              style="width:calc(98% - 22px);display:inline;" 
              placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>"
              >
              <a id="autocomplete_user_id" tabindex="-1" href="admin-users-item.php?id=<?php echo $row['user_id'];?>" style="<?php if ($row['user_id']<=0) echo 'display:none';?>"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή επαφής');?>"></i></a>
              
            </div>
          </div>
          
          
          <div class="form-group row">
            <label for="title" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τίτλος Περίστασης');?>:</label>
            <div class="col-sm-8">
              <input id="title" type="text" class="form-control form-control-sm myneedsave" value="<?php echo htmlspecialchars_gks($row['title']);?>">
            </div>
          </div>
          
          

          
          
          <div class="form-group row">
            <label for="occasion_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τύπος');?>:</label>
            <div class="col-sm-8">
              <select id="occasion_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $div_warehouses_id_from_hide=true;
                $sql_list="SELECT * FROM gks_occasion_types order by id_occasion_type";
                $result_list = $db_link->query($sql_list);  
                if (!$result_list) {debug_mail(false,'error sql',$sql_list.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}  
                while ($row_list= $result_list->fetch_assoc()) { 
                  echo '<option '.($row_list['id_occasion_type']==$row['occasion_id'] ? 'selected' : '').' value="'.$row_list['id_occasion_type'].'">'.$row_list['occasion_type_descr'].'</option>';
                }?>
              </select>
            </div>
          </div>          


          
          <div class="form-group row">
            <label for="notes" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Σχόλιο');?>:</label>
            <div class="col-sm-8">
              <textarea id="notes" type="text" class="form-control form-control-sm myneedsave" style="min-height:100px;height:100px;" ><?php echo htmlspecialchars_gks($row['notes']);?></textarea>
            </div>
          </div>

          <div class="form-group row">
            <label for="pay_method_id" class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Tρόπος Πληρωμής');?>:</label>
            <div class="col-sm-8">
              <select id="pay_method_id" class="form-control form-control-sm myneedsave">
                <option value="0"></option>
                <?php
                $div_warehouses_id_from_hide=true;
                $sql_list="SELECT * FROM gks_payment_acquirers order by mysortorder";
                $result_list = $db_link->query($sql_list);  
                if (!$result_list) {debug_mail(false,'error sql',$sql_list.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}  
                while ($row_list= $result_list->fetch_assoc()) { 
                  echo '<option '.($row_list['id_payment_acquirer']==$row['pay_method_id'] ? 'selected' : '').' value="'.$row_list['id_payment_acquirer'].'">'.$row_list['payment_acquirer_name'].'</option>';
                }?>
              </select>
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
      <button type="button" class="btn btn-danger deleterowbtn" data-id="<?php echo $row['id_order_occasion'];?>" data-model="gks_orders_occasion" data-backurl="admin-orders-occasion.php"><?php echo gks_lang('Διαγραφή');?></button>
    </div>
  </div>
</div>

<?php gks_erp_app_purchase_ads_fix_970x90('item');?>

<div class="container-fluid" id="mypostform">
  <div class="row">
    <div class="col-md-6">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Καταγραφή');?>
        </div>
                
        <div class="card-body" <?php echo gks_card_body('kat');?>>      
  
          <div class="row">
            <label class="col-sm-4 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ID');?>:</label>
            <div class="col-sm-8"><span class="form-control-plaintext form-control-sm"><?php if ($row['id_order_occasion']>0) echo $row['id_order_occasion'];?></span></div>
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

var from_php_perm_ret_edit  =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_orders_occasion','edit',  $id);?>;
var from_php_perm_ret_add   =<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_orders_occasion','add',   $id);?>;
var from_php_perm_ret_delete=<?php echo gks_permission_user_can_action_javascript($my_wp_user_id, 'gks_orders_occasion','delete',$id);?>;

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


    datasend+='&user_id='  + encodeURIComponent($("#mypostform #user_id").attr('data-id').trim());
    datasend+='&title='  + encodeURIComponent($.base64.encode($("#mypostform #title").val().trim()));
    datasend+='&occasion_id='  + encodeURIComponent($("#mypostform #occasion_id").val().trim());
    datasend+='&notes='  + encodeURIComponent($.base64.encode($("#mypostform #notes").val().trim()));
    datasend+='&pay_method_id='  + encodeURIComponent($("#mypostform #pay_method_id").val().trim());
    
 
    datasend+=gks_custom_datasend();
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-orders-occasion-item-exec.php?id=' + <?php echo $id;?>,
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
  
  function notes_change() {gks_resize_textarea($(this));}
  $('#notes').on('change keyup paste', notes_change);
  gks_resize_textarea($('#notes'));
  
  

  var switchery1_this = Array.prototype.slice.call(document.querySelectorAll('.switchery1_this'));
  switchery1_this.forEach(function(html) {
    var switchery3 = new Switchery(html,gks_switchery_defaults());
    html.onchange = function() {need_save=true;};
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
      $('#autocomplete_user_id').attr('href', 'admin-users-item.php?id=' + ui.item.id.trim());
      $('#autocomplete_user_id').show();
   
    },
    change: function (event, ui) {
      need_save=true;
      if(!ui.item){
        $("#user_id").val('').attr('data-id','0');
        $('#autocomplete_user_id').hide(); 
      }
    }
  })
      

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


