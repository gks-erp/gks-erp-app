<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$my_page_title=gks_lang('Διαθεσιμότητα και Τιμές');
$nav_active_array=array('hotel','hotel_availability_prices');

db_open();
stat_record();
$perm_a_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_availability','view',0);
$perm_p_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_hotel_price','view',0);
if ($perm_a_ret['success']==false and $perm_p_ret['success']==false) {
  if ($perm_a_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_a_ret['message'])); die();}
  if ($perm_p_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_p_ret['message'])); die();}
}

$perm_id_hotel_ids=gks_permission_user_condition($my_wp_user_id,'gks_hotel','01');

$user_hotels=gks_get_hotels_list();










include_once('_my_header_admin.php');
?>
<style>
  
.myimgselect {background-color: yellow;} 
.myimgselect_notwd {background-color: rgba(127, 127, 0, 0.7); !important;} 

.disable-select {
    -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none;   /* Chrome/Safari/Opera */
    -khtml-user-select: none;    /* Konqueror */
    -moz-user-select: none;      /* Firefox */
    -ms-user-select: none;       /* Internet Explorer/Edge */
    user-select: none;           /* Non-prefixed version, currently supported by any browser but < IE9 */
}
  
</style>
  

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
  </div>
  <div class="form-group">
    <div class="row align-items-center">
      <label for="hotel_id" class="col-md-6 col-form-label form-control-sm text-md-right text-center" style="font-size1: 120%;  font-weight1: bold;"><?php echo gks_lang('Ξενοδοχείο');?>:</label>
      <div class="col-md-6 text-md-left text-center">
        <select id="hotel_id" class="form-control form-control-sm myneedsave" style="width:unset;display: inline-block;">
          <option value="0"></option>
          <?php
          foreach ($user_hotels as $row_select) {
            echo '<option value="'.$row_select['id'].'" ';
            //if ($row_select['id']==$row['hotel_id']) echo ' selected ';
            echo '>'.$row_select['descr'].'</option>';
          }?>
        </select>    
      </div>
     
      
    </div>
    <div class="row align-items-center">
      <div class="col-sm-12" style="text-align:center">
        <div>
        <table cellpadding="5" cellspacing="0" border="0" align="center" style="width:1%;margin-top:24px;margin-bottom:24px;font-size1:120%;font-weight1:bold;border-collapse: separate;border:1px solid rgb(206, 212, 218);border-radius: 12px;">
          <tr>
            <td nowrap style="border-right:1px solid rgb(206, 212, 218);padding: 10px 24px;">
              <label class="form-check-label" for="selecttype2"><?php echo gks_lang('Τύπος Δωματίου');?></label>
              &nbsp;
              <input class="form-check-input111" type="radio" name="selecttype" id="selecttype2" value="1">
            </td>
            <td nowrap style="padding: 10px 24px;">
              <input class="form-check-input111" type="radio" name="selecttype" id="selecttype1" value="0">
              &nbsp;
              <label class="form-check-label" for="selecttype1"><?php echo gks_lang('Δωμάτιο');?></label>
            </td>
          </tr>  
        </table>
        </div>
      </div>
    </div>
    <div class="row" id="selecttypediv2" style="display:none;">
      <label for="hotel_room_type_id" class="col-md-3 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τύπος Δωματίου');?>:</label>
      <div class="col-md-6">
        <select name="hotel_room_type_id" id="hotel_room_type_id"  class="form-control form-control-sm">
        <option value="0"><?php echo gks_lang('Κάντε μια επιλογή');?></option>
        <?php
        $sql="select * FROM gks_hotel_room_type ".
        (count($perm_id_hotel_ids)>0 ? ' where hotel_id in ('.implode(',',$perm_id_hotel_ids).')' : '').
        " ORDER BY room_type_descr";
        $result_select = $db_link->query($sql);        
        if (!$result_select) {
          debug_mail(false,'error sql',$sql);
          die('sql error');
        }
        while ($row_select = $result_select->fetch_assoc()) {
          echo '<option value="'.$row_select['id_hotel_room_type'].'" '.
          'data-hotel_id="'.$row_select['hotel_id'].'" ';
          echo '>'.$row_select['room_type_descr'];
          if ($row_select['room_type_status'] == 'disable') echo ' ('.getHotelRoomTypeStatusDescr('disable').')';
          else if ($row_select['room_type_status'] == 'renovation') echo ' ('.getHotelRoomTypeStatusDescr('renovation').')';
          echo '</option>';
        }?></select>
      </div>
    </div>
      
    <div class="row" id="selecttypediv1" style="display:none1;">
      <label for="hotel_room_id" class="col-md-3 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Δωμάτιο');?>:</label>
      <div class="col-md-6">
        <select name="hotel_room_id" id="hotel_room_id"  class="form-control form-control-sm">
        <option value="0"><?php echo gks_lang('Κάντε μια επιλογή');?></option>
        <?php
        $sql="select * FROM gks_hotel_room ".
        (count($perm_id_hotel_ids)>0 ? ' where hotel_id in ('.implode(',',$perm_id_hotel_ids).')' : '').
        "ORDER BY room_descr";
        $result_select = $db_link->query($sql);        
        if (!$result_select) {
          debug_mail(false,'error sql',$sql);
          die('sql error');
        }
        while ($row_select = $result_select->fetch_assoc()) {
          echo '<option value="'.$row_select['id_hotel_room'].'" '.
          'data-hotel_id="'.$row_select['hotel_id'].'" ';
          echo '>'.$row_select['room_descr'];
          if ($row_select['room_status'] == 'disable') echo ' ('.getHotelRoomTypeStatusDescr('disable').')';
          else if ($row_select['room_status'] == 'renovation') echo ' ('.getHotelRoomTypeStatusDescr('renovation').')';
          echo '</option>';
        }?></select>
      </div>
    </div>    
  
    <div class="row mt-md-2" id="selecttypediv1">
      <label for="fetos" class="col-md-3 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έτος');?>:</label>
      <div class="col-md-2">
        <select name="fetos" id="fetos"  class="form-control form-control-sm">
          <?php 
          for ($i=date('Y',_time_user(time(), 1))-5;$i<=date('Y',_time_user(time(), 1))+5;$i++) {
            echo '<option value="'.$i.'">'.$i.'</option>'; 
          }?>
        </select>     
      </div>
      
      <label for="fminas" class="col-md-1 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Μήνας');?>:</label>
      <div class="col-md-3">
        <select name="fminas" id="fminas"  class="form-control form-control-sm">
          <?php 
          for ($i=1;$i<=12;$i++) {
            echo '<option value="'.$i.'">'.($i<=9 ? '&nbsp;':'').$i.' '.getMonthName($i).'</option>'; 
          }?>      
        </select>     
      </div>
    </div>    
  </div>    
      

</div>

<?php gks_erp_app_purchase_ads_fix_970x90('afterfilters');?>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-4" style="text-align:center">
        
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Αλλαγή');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('change');?>>                       


            <div class="" style="text-align:left;">
      
              <div class="mt-1 form-group row">
                <label for="availability_from" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Από');?>:</label>
                <div class="col-md-8">
                  <input id="availability_from" type="text" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>        
              <div class="form-group row">
                <label for="availability_to" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Έως');?>:</label>
                <div class="col-md-8">
                  <input id="availability_to" type="text" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>        
           
    
              
    
        
              <div class="form-group row">
                <label class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Ημέρες');?>:</label>
                <div class="col-md-8">
                 
                  <div class="form-check">
                  <input class="form-check-input" type="radio" name="availability_seldays" id="availability_seldays1" value="1" checked>
                  <label class="form-check-label" for="availability_seldays1"><?php echo gks_lang('Όλες οι ημέρες');?></label>
                  <br>
                  <input class="form-check-input" type="radio" name="availability_seldays" id="availability_seldays2" value="2" >
                  <label class="form-check-label" for="availability_seldays2"><?php echo gks_lang('Κάποιες ημέρες');?></label>
                  </div>
                  <div class="form-check form-check-inline" id="availability_seldays2div" style="display:none;">
                    <input type="checkbox" name="avail_weekday_de" id="avail_weekday_de" value="1" style="margin-left: 10px;" checked class="avail_weekday">
                    <label class="form-check-label" for="avail_weekday_de"><?php echo gks_lang('Δε','part3');?></label>
                    <input type="checkbox" name="avail_weekday_tr" id="avail_weekday_tr" value="1" style="margin-left: 5px;"  checked class="avail_weekday">
                    <label class="form-check-label" for="avail_weekday_tr"><?php echo gks_lang('Τρ','part3');?></label>
                    <input type="checkbox" name="avail_weekday_te" id="avail_weekday_te" value="1" style="margin-left: 5px;"  checked class="avail_weekday">
                    <label class="form-check-label" for="avail_weekday_te"><?php echo gks_lang('Τε','part3');?></label>
                    <input type="checkbox" name="avail_weekday_pe" id="avail_weekday_pe" value="1" style="margin-left: 5px;"  checked class="avail_weekday">
                    <label class="form-check-label" for="avail_weekday_pe"><?php echo gks_lang('Πε','part3');?></label>
                    <input type="checkbox" name="avail_weekday_pa" id="avail_weekday_pa" value="1" style="margin-left: 5px;"  checked class="avail_weekday">
                    <label class="form-check-label" for="avail_weekday_pa"><?php echo gks_lang('Πα','part3');?></label>
                    <input type="checkbox" name="avail_weekday_sa" id="avail_weekday_sa" value="1" style="margin-left: 5px;"  checked class="avail_weekday">
                    <label class="form-check-label" for="avail_weekday_sa"><?php echo gks_lang('Σα','part3');?></label>
                    <input type="checkbox" name="avail_weekday_ky" id="avail_weekday_ky" value="1" style="margin-left: 5px;"  checked class="avail_weekday">
                    <label class="form-check-label" for="avail_weekday_ky"><?php echo gks_lang('Κυ','part3');?></label>
                    
                  </div>
                  
                  
                </div>
              </div>
      
              <div class="form-group row">
                <label  class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Αλλαγή σε');?>:</label>
                <div class="col-md-8">
                  <input class="form-check-input111" type="radio" name="changetype" id="changetypeav" value="1" checked>
                  <label class="form-check-label" for="changetypeav"><?php echo gks_lang('Διαθεσιμότητα');?></label>
                  <br>
                  <input class="form-check-input111" type="radio" name="changetype" id="changetypepr" value="0">
                  <label class="form-check-label" for="changetypepr"><?php echo gks_lang('Τιμή');?></label>              
                </div>
              </div> 
                
              <div class="form-group row" id="div_changetypeav">
                <label class="col-md-4 col-form-label form-control-sm text-md-right" style="overflow: hidden;text-overflow: ellipsis; "><?php echo gks_lang('Κατάσταση');?>:</label>
                <div class="col-md-8">
                  <input class="form-check-input111" type="radio" name="availability_status" id="availability_status0" value="0">
                  <label class="form-check-label" for="availability_status0"><span class="hotel_availability_0"><?php echo gks_lang('Κλειστό');?></span></label>
                  <br>
                  <input class="form-check-input111" type="radio" name="availability_status" id="availability_status1" value="1" checked>
                  <label class="form-check-label" for="availability_status1"><span class="hotel_availability_1"><?php echo gks_lang('Ανοιχτό');?></span></label>
                </div>
              </div>
              
              <div class="form-group row" id="div_changetypepr" style="display:none">
                <label for="price" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Τιμή');?>:</label>
                <div class="col-md-8">
                  <input id="price" type="text" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>
                          
              <div class="form-group row">
                <label for="availability_descr" class="col-md-4 col-form-label form-control-sm text-md-right"><?php echo gks_lang('Σχόλιο');?>:</label>
                <div class="col-md-8">
                  <input id="availability_descr" type="text" class="form-control form-control-sm" value="" autocomplete="<?php echo $autocomplete_gks_disable;?>">
                </div>
              </div>
    
              <div class="form-group row">
                <div class="offset-sm-4 col-sm-8 mb-2">
                  <button type="button" class="btn btn-primary" id="submit_button_save_change"><?php echo gks_lang('Αποθήκευση');?></button>
                </div>
              </div> 
              
            </div>

            


        </div>
      </div>
      
    </div>
    <div class="col-md-8" style="text-align:center">
      <h1><?php echo gks_lang('Ημερολόγιο','part2');?></h1>

      <table class="table table-sm table-responsive1 table-striped table-bordered" border="0" style="width:100%" cellspacing="0" cellpadding="5" align="center" id="mytable">
        <thead>
          <tr>	
            <th class="table-dark" scope="col" style="text-align: center !important;" width="9%"><?php echo gks_lang('Μήνας');?></th>
            <th class="table-dark" scope="col" style="text-align: center !important;" width="13%"><?php echo gks_lang('Δε','part3');?></th>
            <th class="table-dark" scope="col" style="text-align: center !important;" width="13%"><?php echo gks_lang('Τρ','part3');?></th> 
            <th class="table-dark" scope="col" style="text-align: center !important;" width="13%"><?php echo gks_lang('Τε','part3');?></th>        
            <th class="table-dark" scope="col" style="text-align: center !important;" width="13%"><?php echo gks_lang('Πε','part3');?></th>  
            <th class="table-dark" scope="col" style="text-align: center !important;" width="13%"><?php echo gks_lang('Πα','part3');?></th>        
            <th class="table-dark" scope="col" style="text-align: center !important;" width="13%"><?php echo gks_lang('Σα','part3');?></th>        
            <th class="table-dark" scope="col" style="text-align: center !important;" width="13%"><?php echo gks_lang('Κυ','part3');?></th>        
          </tr>
        </thead>
        <tbody>

        </tbody>    
      </table>
      <p align="center">
        <button type="button" class="btn btn-primary" id="nextmonth"><?php echo gks_lang('Επόμενος Μήνας');?></button>

      </p>    

    
    </div>
    
  </div>
</div>




<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var hashchange='';
var mydata = {};
var mytoday= new Date(<?php echo date('Y',_time_user(time(), 1));?>, <?php echo date('n',_time_user(time(), 1));?> -1, <?php echo date('j',_time_user(time(), 1));?>, 0, 0, 0, 0);
var startdate;
var firstdate;
var lastdate;
var nummonths=1;
var nextmonth_firstdate=null;
var nextmonth_lastdate=null;
var tdcount = 0;

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>


  $('#selecttype1').change(function() {
    $('#selecttypediv1').show();
    $('#selecttypediv2').hide();
    get_data();
  });
  $('#selecttype2').change(function() {
    $('#selecttypediv1').hide();
    $('#selecttypediv2').show();
    get_data();
  });

  $('#changetypeav').change(function() {
    $('#div_changetypeav').show();
    $('#div_changetypepr').hide();
  });
  $('#changetypepr').change(function() {
    $('#div_changetypeav').hide();
    $('#div_changetypepr').show();
  });


  

  
  $('#hotel_room_type_id').change(get_data);
  $('#hotel_room_id').change(get_data);
  $('#fetos').change(get_data);
  $('#fminas').change(get_data);
    
  function set_myimgselect_notwd() {
    $('.myimgselect_notwd').each(function( index ) {
      $(this).removeClass('myimgselect_notwd');    
    });
    if ($('#availability_seldays2').is(':checked')) {
      //console.log('availability_seldays2');
      if ($('#avail_weekday_de').is(':checked') == false) $('.myimgselect[data-wd=1]').each(function( index ) {$(this).addClass('myimgselect_notwd');});
      if ($('#avail_weekday_tr').is(':checked') == false) $('.myimgselect[data-wd=2]').each(function( index ) {$(this).addClass('myimgselect_notwd');});
      if ($('#avail_weekday_te').is(':checked') == false) $('.myimgselect[data-wd=3]').each(function( index ) {$(this).addClass('myimgselect_notwd');});
      if ($('#avail_weekday_pe').is(':checked') == false) $('.myimgselect[data-wd=4]').each(function( index ) {$(this).addClass('myimgselect_notwd');});
      if ($('#avail_weekday_pa').is(':checked') == false) $('.myimgselect[data-wd=5]').each(function( index ) {$(this).addClass('myimgselect_notwd');});
      if ($('#avail_weekday_sa').is(':checked') == false) $('.myimgselect[data-wd=6]').each(function( index ) {$(this).addClass('myimgselect_notwd');});
      if ($('#avail_weekday_ky').is(':checked') == false) $('.myimgselect[data-wd=0]').each(function( index ) {$(this).addClass('myimgselect_notwd');});
    }
  }

  $('#availability_from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#availability_to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  $('#availability_seldays1').change(function() {
    $('#availability_seldays2div').hide();
    set_myimgselect_notwd();
  });
  $('#availability_seldays2').change(function() {
    $('#availability_seldays2div').show();
    set_myimgselect_notwd();
  });
  
  
  

  
  $('.avail_weekday').click(function() {
    set_myimgselect_notwd();
  });
  
  function calendartd_mousedown_click(myelem,event) {
    //console.log('calendartd_mousedown_click');
    //console.log(new Date());


    photo_all_ii=parseInt(myelem.attr('data-all_ii'));
    

    if(event.isDefaultPrevented()) return;
    //console.log('calendartd_mousedown_click' + photo_all_ii);
    
    if (event.which !=1) return;
    if (event.ctrlKey) {
//      if (myelem.hasClass('myimgselect')) {
//        myelem.removeClass('myimgselect');    
//      } else {
//        myelem.addClass('myimgselect');
//        first_click_all_ii = photo_all_ii;
//      }
    } else if (event.shiftKey) {
      
      $('.myimgselect').each(function( index ) {
        $(this).removeClass('myimgselect');    
      });
      $('.myimgselect_notwd').each(function( index ) {
        $(this).removeClass('myimgselect_notwd');    
      });
            
      last_click_all_ii = photo_all_ii;
      if (first_click_all_ii < last_click_all_ii) {
        for (callii=first_click_all_ii; callii <= last_click_all_ii; callii++) {
          fff='.calendartd[data-all_ii=' + callii + ']';
          $(fff).addClass('myimgselect');
        } 
        fff='.calendartd[data-all_ii=' + last_click_all_ii + ']';
        id_elem = $(fff).attr('id');
        id_elem=id_elem.replace('date_','').split('-');
        id_elem=id_elem[2] + '/' + id_elem[1] + '/' + id_elem[0];
        //console.log(id_elem);
        $('#availability_to').val(id_elem);
      
      } else {
        for (callii=last_click_all_ii; callii <= first_click_all_ii; callii++) {
          fff='.calendartd[data-all_ii=' + callii + ']';
          $(fff).addClass('myimgselect');
        }
        fff='.calendartd[data-all_ii=' + last_click_all_ii + ']';
        id_elem = $(fff).attr('id');
        id_elem=id_elem.replace('date_','').split('-');
        id_elem=id_elem[2] + '/' + id_elem[1] + '/' + id_elem[0];
        //console.log(id_elem);
        $('#availability_from').val(id_elem);
      }
      
    } else if (event.shiftKey == false) {
      $('.myimgselect').each(function( index ) {
        $(this).removeClass('myimgselect');    
      });
      $('.myimgselect_notwd').each(function( index ) {
        $(this).removeClass('myimgselect_notwd');    
      });      
      myelem.addClass('myimgselect');
      first_click_all_ii = photo_all_ii;
      id_elem = myelem.attr('id');
      id_elem=id_elem.replace('date_','').split('-');
      id_elem=id_elem[2] + '/' + id_elem[1] + '/' + id_elem[0];
      //console.log(id_elem);
      $('#availability_from').val(id_elem);
      $('#availability_to').val(id_elem);
      
    }
    set_myimgselect_notwd();
    
    //$('#group-selected-images').html($('.myimgselect').length);    
  }
  
  calendartd_mousedown = function(event) {
    calendartd_mousedown_click($(this),event);
  };


  $(window).hashchange( function(){
    if (hashchange == location.hash) return;
    currsearch=window.location.hash.replace('#', '');
    currsearch=decodeURI(currsearch);
    //console.log('window hashchange');
    //console.log(currsearch);
    
    hash_analyze(currsearch);
    get_data();
  });
  
 

  //old pos
  
  function hash_analyze(currsearch) {
    if (currsearch!='') {
      try {
        mydata = JSON.parse(currsearch);
  
      } catch(err) {
        //console.log('error ' + err);  
        return;
      }
    } 
    
    
    
    if (mydata.hotel_id === undefined || mydata.hotel_id  === null) mydata.hotel_id=0;
    if (mydata.room_id === undefined || mydata.room_id  === null) mydata.room_id=0;
    if (mydata.room_type_id === undefined || mydata.room_type_id  === null) mydata.room_type_id=0;
    if (mydata.room_id == 0 && mydata.room_type_id  == 0) mydata.room_type_id=0;
    
    if (mydata.hotel_id<=0) {
      //console.log('fffffffff');
      $('#hotel_id option').each(function() {
        temp_val=parseInt($(this).attr('value')); if (isNaN(temp_val)) temp_val=0;
        if (temp_val>0) {
          mydata.hotel_id=temp_val;
          return false; 
        }
        
      });
      
    }
    
      
    
    $('#hotel_id').val(mydata.hotel_id);
    
    
    
    if (mydata.room_id >0) {
      $('#selecttypediv1').show();
      $('#selecttypediv2').hide();
      $('#hotel_room_id').val(mydata.room_id);
      $('#selecttype1').prop('checked', true);
      
    } else if (mydata.room_type_id >=0) {
      $('#selecttypediv1').hide();
      $('#selecttypediv2').show();
      $('#hotel_room_type_id').val(mydata.room_type_id);
      $('#selecttype2').prop('checked', true);
    }
    if (mydata.etos === undefined || mydata.etos  === null) mydata.etos = <?php echo date('Y',_time_user(time(), 1));?>;
    $('#fetos').val(mydata.etos);
    if (mydata.minas === undefined || mydata.minas  === null) mydata.minas = <?php echo date('n',_time_user(time(), 1));?>;
    $('#fminas').val(mydata.minas);
    
    if (mydata.nummonths === undefined || mydata.nummonths  === null) mydata.nummonths = 1;
    nummonths=mydata.nummonths;
    //console.log('hash_analyze');
    //console.log(mydata);
    
  }
  function set_hash() {
    //console.log('set_hash');
    mydata={};
    mydata.hotel_id=parseInt($("#hotel_id").val());
    
    if ($('#selecttype1').is(':checked')) {
      mydata.room_id= parseInt($("#hotel_room_id").val());
      mydata.room_type_id=0;
    } else {
      mydata.room_type_id= parseInt($("#hotel_room_type_id").val());
      mydata.room_id=0;
    }
    mydata.etos=parseInt($("#fetos").val());
    mydata.minas=parseInt($("#fminas").val());
    mydata.nummonths = nummonths;
    
    //console.log('set_hash');
    //console.log(mydata);

    document.location.hash = encodeURI(JSON.stringify(mydata));
    hashchange=document.location.hash;
  }

  
  
  function get_data() {
    set_hash();
    
    tdcount = 0;
    trl= $('#mytable tr').length;
    for (i=2;i <= trl;i++) {
      $('#mytable tr:last').remove();
    }
        
    startdate = new Date(mydata.etos, mydata.minas -1, 1, 0, 0, 0, 0);    
    
    firstdate=null;
    lastdate=null;
    
    weekday = startdate.getDay();
    if (weekday == 0) weekday=7; 
    startday = 1 - weekday;
    myweekcount=-1;
    allhtml='';
    do {
      myweekcount++;
      outhtml='<tr height="110px">';
      if (allhtml=='') outhtml+='<td rowspan="[[myweekcount]]" class="table-dark" style="vertical-align: middle;"><div style="position: relative;"><div class="avprcal_month">' +  getMonthName(mydata.minas) + ' ' + mydata.etos + '</div></div></td>';
      islastweek=false;
      for (i=1;i<=7; i++) {
        thisdate = new Date(mydata.etos, mydata.minas -1, myweekcount*7 + i + startday, 0, 0, 0, 0);
        if (firstdate==null) firstdate=new Date(thisdate);
        lastdate=new Date(thisdate);
        outhtml+=calctd(thisdate,mytoday);
      }
      outhtml+='</tr>';
      allhtml+=outhtml;
      
      checkdate=new Date(thisdate);
      checkdate.setDate(checkdate.getDate() + 1);
      if (checkdate.getMonth()>=mydata.minas || checkdate.getFullYear() > mydata.etos) islastweek=true;
    } while (islastweek == false);
    allhtml = allhtml.replace('[[myweekcount]]', myweekcount+1);
    $('#mytable tr:last').after(allhtml);
    
    for(im=2; im <= mydata.nummonths; im++) {
      //console.log(im);
      nextmonth();
    }
    date_data(firstdate, lastdate);
    $('.calendartd').off('mousedown');
    $('.calendartd').mousedown(calendartd_mousedown);
    
    
    //console.log('firstdate');
    //console.log(firstdate);
    //console.log('lastdate');
    //console.log(lastdate);
    
  }
  
  $('#nextmonth').click(function() {
    if (parseInt($("#hotel_room_type_id").val())<=0 && parseInt($("#hotel_room_id").val())<=0) {
      myalert('error:'+gks_lang('Παρακαλώ επιλέξτε κάποιο δωμάτιο ή τύπο δωματίου'));
      return;
    }
    nummonths++;
    nextmonth();
    set_hash();
    
    //console.log('nextmonth_firstdate');
    //console.log(nextmonth_firstdate);
    //console.log('nextmonth_lastdate');
    //console.log(nextmonth_lastdate);
    date_data(nextmonth_firstdate, nextmonth_lastdate);
    
  });
  
  function nextmonth(){
    ssdate=new Date(lastdate.getFullYear(), lastdate.getMonth(), lastdate.getDate() + 1, 0, 0, 0, 0);
    mydaycount=-1;
    myweekcount=-1;
    allhtml='';
    nextmonth_firstdate = null;
    nextmonth_lastdate = null;
    
    do {
      myweekcount++;
      outhtml='<tr height="110px">';
      if (allhtml=='') outhtml+='<td rowspan="[[myweekcount]]" class="table-dark" style="vertical-align: middle;"><div style="position: relative;"><div class="avprcal_month">' +  getMonthName(ssdate.getMonth() + 1) + ' ' + ssdate.getFullYear() + '</div></div></td>';
      islastweek=false;
      for (i=1;i<=7; i++) {
        mydaycount++;
        thisdate = new Date(ssdate.getFullYear(), ssdate.getMonth(), ssdate.getDate() + mydaycount, 0, 0, 0, 0);
        lastdate=new Date(thisdate);
        if (nextmonth_firstdate==null) nextmonth_firstdate=new Date(thisdate);
        nextmonth_lastdate=new Date(thisdate);
        outhtml+=calctd(thisdate,mytoday);
      }
      outhtml+='</tr>';
      allhtml+=outhtml;
      checkdate=new Date(thisdate);
      checkdate.setDate(checkdate.getDate() + 1);
      if (checkdate.getMonth() > ssdate.getMonth() || checkdate.getFullYear() > ssdate.getFullYear()) islastweek=true;
    } while (islastweek == false);
    allhtml = allhtml.replace('[[myweekcount]]', myweekcount+1);
    $('#mytable tr:last').after(allhtml);  
    //console.log(allhtmlo);
    $('.calendartd').off('mousedown');
    $('.calendartd').mousedown(calendartd_mousedown);
    
    //allhtmlo.mousedown(calendartd_mousedown);   
    //console.log('lastdate ' +lastdate);
    
  }
  
  function calctd(thisdate,mytoday) {
    tdcount++;
    tdclass='';
    if (thisdate < mytoday) {
      tdclass='avprcal_past';
    } else if (thisdate.getMonth() % 2 == 0 && (thisdate.getDay() == 0 || thisdate.getDay() == 6)) {
      tdclass='avprcal_odd_sk';
    } else if (thisdate.getMonth() % 2 != 0 && (thisdate.getDay() == 0 || thisdate.getDay() == 6)) {
      tdclass='avprcal_even_sk';
    } else if (thisdate.getMonth() % 2 == 0) {
      tdclass='avprcal_odd';
    } else if (thisdate.getMonth() % 2 != 0) {
      tdclass='avprcal_even';
    }    
    outhtml='<td class="calendartd disable-select '+ tdclass +'" id="date_'+ thisdate.getFullYear()  + '-' + pad((thisdate.getMonth()+1),2) + '-' + pad(thisdate.getDate(),2) + '" data-wd="' + thisdate.getDay() + '" data-all_ii="' + tdcount + '">' + 
    '<div class="calendartddate">' + thisdate.getDate() + '</div>' +
    '<div class="calendartdstatus"></div>' +
    '<div class="calendartdprice"></div>' +
    '<div class="calendartdreserve"></div>' +
    '</td>';
    return outhtml;
  }
  

  
  function date_data(date_from, date_to) {
    datasend='';
    datasend+='&hotel_id='  + encodeURIComponent(mydata.hotel_id);
    datasend+='&room_id='  + encodeURIComponent(mydata.room_id);
    datasend+='&room_type_id='  + encodeURIComponent(mydata.room_type_id);
    datasend+='&date_from='  + encodeURIComponent(date_from.getFullYear() + '-' + (date_from.getMonth()+1) + '-' + date_from.getDate());
    datasend+='&date_to='  + encodeURIComponent(date_to.getFullYear() + '-' + (date_to.getMonth()+1) + '-' + date_to.getDate()); 
    //console.log(datasend);
    if (mydata.room_id == 0 && mydata.room_type_id == 0) return;
    if (date_from == '' && date_to == '') return;
    
    $('body').addClass("myloading");
    
    $.ajax({
			url: '/my/admin-hotel-availability-prices-calendar-data.php',
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
  					//window.location.reload();
  					if (data.outdata) {
  					  //console.log(data.outdata);
  					  for (x in data.outdata) {
  					    ff='#date_' + x + ' .calendartdstatus';
  					    $(ff).addClass('calendartdstatus' + data.outdata[x].val1); 
  					    fftitle=data.outdata[x].froma;

  					    if (data.outdata[x].descra && data.outdata[x].descra!='') fftitle+= '<br><i>' + data.outdata[x].descra + '</i>';
  					      
  					    $(ff).attr('title',fftitle);
  					    $(ff).bootstrapTltip('dispose');
  					    $(ff).bootstrapTltip({html:true, delay: { "show": 100, "hide": 0 }});
  					    
  					    ffp='#date_' + x + ' .calendartdprice';
  					    $(ffp).html(data.outdata[x].price);

  					    fftitlep=data.outdata[x].fromp;

  					    
  					    if (data.outdata[x].descrp && data.outdata[x].descrp!='') fftitlep+= '<br><i>' + data.outdata[x].descrp + '</i>';
                $(ffp).attr('title',fftitlep);
  					    $(ffp).bootstrapTltip('dispose');
  					    $(ffp).bootstrapTltip({html:true, delay: { "show": 100, "hide": 0 }});
  					    
  					  }
  					}
  					
					} else {
						myalert('error:' + $.base64.decode(data.message));
					}
				}
			}
			
		});     

    return false;
  }  
  

  //$('[data-toggle="tooltip"]').bootstrapTltip();
  function availability_dates_change() {

    $('.myimgselect').each(function( index ) {
      $(this).removeClass('myimgselect');    
    });
    $('.myimgselect_notwd').each(function( index ) {
      $(this).removeClass('myimgselect_notwd');    
    });
    
    ssdate_from=$('#availability_from').val();
    ssdate_to=$('#availability_to').val();
    if (ssdate_from=='__/__/____') ssdate_from=''; 
    if (ssdate_to=='__/__/____') ssdate_to=''; 
    if (ssdate_from == '' && ssdate_to == '') {
      
      return;
    }
    ssdate_from = ssdate_from.split('/');
    ssdate_to = ssdate_to.split('/');
    if (ssdate_from.length == 3) {
      ssy=parseInt(ssdate_from[2]);
      ssm=parseInt(ssdate_from[1]);
      ssd=parseInt(ssdate_from[0]);
      ssdate_from=new Date(ssy, ssm-1, ssd, 0, 0, 0, 0);
    } else {
      ssdate_from = new Date(firstdate);
    }
    if (ssdate_to.length == 3) {
      ssy=parseInt(ssdate_to[2]);
      ssm=parseInt(ssdate_to[1]);
      ssd=parseInt(ssdate_to[0]);
      ssdate_to=new Date(ssy, ssm-1, ssd, 0, 0, 0, 0);
    } else {
      ssdate_to = new Date(lastdate);
    }
    

    if (ssdate_from <= ssdate_to) {
      thisdate = new Date(ssdate_from);
      do {
        //console.log(thisdate);
        fff='date_' + thisdate.getFullYear() + '-' + pad((thisdate.getMonth()+1),2) + '-' + pad(thisdate.getDate(),2);
        $('#' + fff).addClass('myimgselect');
        thisdate.setDate(thisdate.getDate() + 1);
        if (thisdate>ssdate_to) break;
      } while (true);
      set_myimgselect_notwd();
    }
    //console.log('--');
    //console.log(ssdate_from);
    //console.log(ssdate_to);
  }
  
  $('#availability_from').change(function() {
    availability_dates_change();
  });
  $('#availability_to').change(function() {
    availability_dates_change();
  });
  
  
  

  $('#submit_button_save_change').click(function(event) {

    destpage='admin-hotel-availability-item-exec.php';
    if ($('#changetypepr').is(':checked')) {
      destpage='admin-hotel-price-item-exec.php';
    }

    
    if ($('#selecttype1').is(':checked')) {
      if (destpage=='admin-hotel-price-item-exec.php') {
        myalert('error:'+gks_lang('Η τιμή μπορεί να ορισθεί μόνο σε τύπο δωματίου και όχι σε δωμάτιο'));
        return;        
      }
      
      room_type_id=0;
      room_id= parseInt($("#hotel_room_id").val());
      if (room_id<=0) {
        myalert('error:'+gks_lang('Επιλέξτε πρώτα ένα δωμάτιο'));
        return;
      }
    } else {
      room_id=0;
      room_type_id= parseInt($("#hotel_room_type_id").val());
      if (room_type_id<=0) {
        myalert('error:'+gks_lang('Επιλέξτε πρώτα έναν τύπο δωμάτιου'));
        return;
      }
    }
    availability_from=$('#availability_from').val().trim();
    
    if (availability_from=='__/__/____' || availability_from =='') {
      myalert('error:'+gks_lang('Ορίστε την ημερομηνία <b>Από</b>'));
      return;      
    }
    price = $('#price').val().trim();
    if (destpage=='admin-hotel-price-item-exec.php' && (price=='' || price=='0')) {
      myalert('error:'+gks_lang('Ορίστε την <b>Τιμή</b>'));
      return;        
    }
    
    if (destpage=='admin-hotel-availability-item-exec.php') {
      datasend='';
      if ($('#selecttype1').is(':checked')) {
        datasend+='&hotel_room_id=' + room_id;
      } else {
        datasend+='&hotel_room_type_id=' + room_type_id;
      }
     
      datasend+='&availability_from='  + encodeURIComponent($("#availability_from").val().trim());
      datasend+='&availability_to='  + encodeURIComponent($("#availability_to").val().trim());
      datasend+='&availability_descr='  + encodeURIComponent($.base64.encode($("#availability_descr").val().trim()));
      datasend+='&availability_status='  + (($('#availability_status1').is(':checked')) ? '1':'0');
      
      datasend+='&availability_seldays1='  + (($('#availability_seldays1').is(':checked')) ? '1':'0');
      datasend+='&avail_weekday_de='  + (($('#avail_weekday_de').is(':checked')) ? '1':'0');
      datasend+='&avail_weekday_tr='  + (($('#avail_weekday_tr').is(':checked')) ? '1':'0');
      datasend+='&avail_weekday_te='  + (($('#avail_weekday_te').is(':checked')) ? '1':'0');
      datasend+='&avail_weekday_pe='  + (($('#avail_weekday_pe').is(':checked')) ? '1':'0');
      datasend+='&avail_weekday_pa='  + (($('#avail_weekday_pa').is(':checked')) ? '1':'0');
      datasend+='&avail_weekday_sa='  + (($('#avail_weekday_sa').is(':checked')) ? '1':'0');
      datasend+='&avail_weekday_ky='  + (($('#avail_weekday_ky').is(':checked')) ? '1':'0');
      
      //console.log(datasend);

      $('body').addClass("myloading");
      
      $.ajax({
  			url: '/my/admin-hotel-availability-item-exec.php?id=-1',
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
    					get_data();
  					} else {
  						myalert('error:' + $.base64.decode(data.message));
  					}
  				}
  			}
  			
  		});     
  
      return false;
    
      
    } else if (destpage=='admin-hotel-price-item-exec.php') {
    
      if (room_type_id == 0) return;
      datasend='';
      datasend+='&hotel_room_type_id='  + encodeURIComponent($("#hotel_room_type_id").val().trim());
  
     
      datasend+='&price_from='  + encodeURIComponent($("#availability_from").val().trim());
      datasend+='&price_to='  + encodeURIComponent($("#availability_to").val().trim());
      datasend+='&price_descr='  + encodeURIComponent($.base64.encode($("#availability_descr").val().trim()));
      datasend+='&price='  + encodeURIComponent($("#price").val().trim());
      
      datasend+='&price_seldays1='  + (($('#availability_seldays1').is(':checked')) ? '1':'0');
      datasend+='&price_weekday_de='  + (($('#avail_weekday_de').is(':checked')) ? '1':'0');
      datasend+='&price_weekday_tr='  + (($('#avail_weekday_tr').is(':checked')) ? '1':'0');
      datasend+='&price_weekday_te='  + (($('#avail_weekday_te').is(':checked')) ? '1':'0');
      datasend+='&price_weekday_pe='  + (($('#avail_weekday_pe').is(':checked')) ? '1':'0');
      datasend+='&price_weekday_pa='  + (($('#avail_weekday_pa').is(':checked')) ? '1':'0');
      datasend+='&price_weekday_sa='  + (($('#avail_weekday_sa').is(':checked')) ? '1':'0');
      datasend+='&price_weekday_ky='  + (($('#avail_weekday_ky').is(':checked')) ? '1':'0');
      
      //console.log(datasend);
      //return;
      
      $('body').addClass("myloading");
      
      $.ajax({
  			url: '/my/admin-hotel-price-item-exec.php?id=-1',
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
    					get_data();
  					} else {
  						myalert('error:' + $.base64.decode(data.message));
  					}
  				}
  			}
  			
  		}); 
  		return false;          
      
    }
    //console.log(availability_from);
    //    
    //console.log(destpage);
    //console.log(room_type_id);
    //console.log(room_id);
    //console.log('submit_button_save_change');
    
  });


  function hotel_id_change(and_hash) {
    var hotel_id=parseInt($('#hotel_id').val());
    if (isNaN(hotel_id)) hotel_id=0;
    need_get_data=false;
    $('#hotel_room_type_id option').each(function() {
      val=parseInt($(this).val()); if (isNaN(val)) val=0;
      if (val!=0) { 
        val=parseInt($(this).attr('data-hotel_id'));if (isNaN(val)) val=0;
        if (val==hotel_id) $(this).show(); else $(this).hide(); 
      }    
    });
    if ($('#hotel_room_type_id option:selected').css('display') == 'none') {
      $('#hotel_room_type_id').val('0');
      need_get_data=true;
    }
    
    $('#hotel_room_id option').each(function() {
      val=parseInt($(this).val()); if (isNaN(val)) val=0;
      if (val!=0) { 
        val=parseInt($(this).attr('data-hotel_id'));if (isNaN(val)) val=0;
        if (val==hotel_id) $(this).show(); else $(this).hide(); 
      }    
    });
    if ($('#hotel_room_id option:selected').css('display') == 'none') {
      $('#hotel_room_id').val('0');
      need_get_data=true;
    }
    if (need_get_data) get_data();
    
    if (and_hash) set_hash();
  }
  $('#hotel_id').change(function() {hotel_id_change(true)});
  
  
   
  
  currsearch=window.location.hash.replace('#', '');
  currsearch=decodeURI(currsearch);
  hash_analyze(currsearch);
  get_data();
    
  //hotel_id_change(false);
  
    
});
</script>


<?php
//db_close();
include_once('_my_footer_admin.php');


