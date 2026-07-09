<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();

$my_page_title=gks_lang('Έλεγχος Τρόπων Αποστολής-Πληρωμής');

$nav_active_array=array('manage','manage_d','manage_p');


db_open();
stat_record();

$perm_delivery_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_delivery_methods','view',0);
$perm_delivery_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_delivery_methods','edit',0);
$perm_delivery_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_delivery_methods','add',0);
$perm_delivery_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_delivery_methods','delete',0);

$perm_payment_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_payment_acquirers','view',0);
$perm_payment_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_payment_acquirers','edit',0);
$perm_payment_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_payment_acquirers','add',0);
$perm_payment_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_payment_acquirers','delete',0);

if ($perm_delivery_view==false and $perm_payment_view==false) {header('Location: /my/admin-deny.php?message='.rawurlencode(gks_lang('Δεν επιτρέπεται η πρόσβαση στους τρόπους πληρωμής-αποστολής'))); die();}






$country_id=91;
if (isset($_GET['country_id']) and $_GET['country_id']!='' ) {
  $country_id = intval($_GET['country_id']);
}
$ajia=0;
if (isset($_GET['ajia']) and $_GET['ajia']!='' ) {
  $ajia = floatval(str_replace(',', '.', $_GET['ajia']));
}
$varos=0;
if (isset($_GET['varos']) and $_GET['varos']!='' ) {
  $varos = floatval(str_replace(',', '.', $_GET['varos']));
}
$ogos=0;
if (isset($_GET['ogos']) and $_GET['ogos']!='' ) {
  $ogos = floatval(str_replace(',', '.', $_GET['ogos']));
}

if (!isset($_gks_session['gks']['basket']['destination_data']['country_id'])) {
  $_gks_session['gks']['basket']['destination_data']['country_id']=91;
}
$tropos_apostolis = $_gks_session['gks']['basket']['tropos_apostolis'];
$tropos_pliromis = $_gks_session['gks']['basket']['tropos_pliromis'];
$kostos_apostolis = $_gks_session['gks']['basket']['kostos_apostolis'];
$kostos_pliromis = $_gks_session['gks']['basket']['kostos_pliromis'];
$data_country_id=$_gks_session['gks']['basket']['destination_data']['country_id'];
$products_total=$_gks_session['gks']['basket']['products_total'];
$products_varos=$_gks_session['gks']['basket']['products_varos'];
$products_ogos=$_gks_session['gks']['basket']['products_ogos'];
$products_need_apostoli = $_gks_session['gks']['basket']['products_need_apostoli'];
$products_need_pliromi = $_gks_session['gks']['basket']['products_need_pliromi'];



$_gks_session['gks']['basket']['tropos_apostolis'] = 0;
$_gks_session['gks']['basket']['tropos_pliromis']  = 0;
$_gks_session['gks']['basket']['kostos_apostolis'] = 0;
$_gks_session['gks']['basket']['kostos_pliromis'] = 0;
$_gks_session['gks']['basket']['destination_data']['country_id'] = $country_id;
$_gks_session['gks']['basket']['products_total'] = $ajia;
$_gks_session['gks']['basket']['products_varos'] = $varos;
$_gks_session['gks']['basket']['products_ogos']  = $ogos;
$_gks_session['gks']['basket']['products_need_apostoli'] = $_gks_session['gks']['basket']['products_varos'] > 0;
$_gks_session['gks']['basket']['products_need_pliromi']  = $_gks_session['gks']['basket']['products_total'] >0;

$kostos_apostolis = gks_calculate_kostos_apostolis($_gks_session['gks']['basket'], -2);
$kostos_pliromis  = gks_calculate_kostos_pliromis ($_gks_session['gks']['basket'], -2);

$_gks_session['gks']['basket']['tropos_apostolis']= $tropos_apostolis;
$_gks_session['gks']['basket']['tropos_pliromis']= $tropos_pliromis;
$_gks_session['gks']['basket']['kostos_pliromis']= $kostos_pliromis;
$_gks_session['gks']['basket']['tropos_pliromis']= $tropos_pliromis;
$_gks_session['gks']['basket']['destination_data']['country_id']= $data_country_id;
$_gks_session['gks']['basket']['products_total']= $products_total;
$_gks_session['gks']['basket']['products_varos']= $products_varos;
$_gks_session['gks']['basket']['products_ogos']= $products_ogos;
$_gks_session['gks']['basket']['products_need_apostoli']= $products_need_apostoli;
$_gks_session['gks']['basket']['products_need_pliromi']= $products_need_pliromi;


//basket_recalc();

include_once('_my_header_admin.php');
?>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h3><?php echo $my_page_title;?></h3>
    </div>
  </div>
</div>


<table id="filters" class="filters-table" border="0" width="96%" cellspacing="0" cellpadding="5" align="center">  
  <tbody>
    <tr>
      <td>
        <form method="GET" action="" id=form2 name=form2>
          <table width="100%" cellspacing="0" cellpadding="2" border="0"><tbody>
            <tr>
              <td width="0%" style="padding-bottom: 4px;">
                <div class="divfilter">
                  <table width="100%" cellspacing="0" cellpadding="2" border="0"><tbody>
                    <tr><td width="100%"><?php echo gks_lang('Μεταβλητές');?>:</td></tr>
                    <tr><td width="100%"><img src="img/filter.png" border="0"></td></tr>
                  </tbody></table>
                </div>            

                <div class="divfilter">
                  <table width="100%" cellspacing="0" cellpadding="2" border="0"><tbody>
                    <tr>
                      <td width="0%" nowrap="nowrap"><?php echo gks_lang('Χώρα');?></td>
                    </tr>
                    <tr>
                      <td width="0%" nowrap="nowrap">
                        <select name="country_id" class="form-control form-control-sm myneedsave">
                          <?php $sql="SELECT id_country,country_name,country_initials FROM gks_country ORDER BY country_name";
                        $res = $db_link->query($sql);
                        if (!$res) {
                          debug_mail(false,'delivery error sql',$sql);
                          die('sql error');
                        }
                        while ($row = $res->fetch_assoc()) {  
                          echo '<option value="'.$row['id_country'].'" data-ci="'.$row['country_initials'].'" ';
                          if ($row['id_country'] == $country_id) echo ' selected ';
                          echo '>'.$row['country_name'].'</option>';
                          
                        }?>      
                        </select>
                                    
                      <input type="hidden" name="fuser_id" id="fuser_id" value="-1">
                      <select "="" id="fuser_id_ms" class="filterselectbox " style="display: none;" multiple="multiple">
                        <option value="20447" selected="selected">Kostas Goutoudis Pelatis Test1</option>
                        
                      </select>
                      </td>
                    </tr>
                  </tbody></table>
                </div>

                <div class="divfilter">
                  <table width="100%" cellspacing="0" cellpadding="2" border="0"><tbody>
                    <tr>
                      <td width="0%" nowrap="nowrap"><?php echo gks_lang('Βάρος gr');?></td>
                    </tr>
                    <tr>
                      <td width="0%" nowrap="nowrap">
                        <input type="number" style="line-height: 1;width:200px;" id="varos" name="varos" value="<?php echo $varos;?>" placeholder="100.00" class="form-control form-control-sm" min="0" />
                      </td>
                    </tr>
                  </tbody></table>
                </div>
                <div class="divfilter">
                  <table width="100%" cellspacing="0" cellpadding="2" border="0"><tbody>
                    <tr>
                      <td width="0%" nowrap="nowrap"><?php echo gks_lang('Αξία');?></td>
                    </tr>
                    <tr>
                      <td width="0%" nowrap="nowrap">
                        <input type="number" style="line-height: 1;width:200px;" id="ajia" name="ajia" value="<?php echo $ajia;?>" placeholder="100.00" class="form-control form-control-sm" min="0" />
                      </td>
                    </tr>
                  </tbody></table>
                </div>
                <div class="divfilter">
                  <table width="100%" cellspacing="0" cellpadding="2" border="0"><tbody>
                    <tr>
                      <td width="0%" nowrap="nowrap"><?php echo gks_lang('Όγκος cm³');?></td>
                    </tr>
                    <tr>
                      <td width="0%" nowrap="nowrap">
                        <input type="number" style="line-height: 1;width:200px;" id="ogos" name="ogos" value="<?php echo $ogos;?>" placeholder="100.00" class="form-control form-control-sm" min="0" />
                      </td>
                    </tr>
                  </tbody></table>
                </div>

                <div class="divfilter" style="vertical-align: top;">
                  <table width="100%" cellspacing="0" cellpadding="2" border="0"><tbody>
                    <tr><td width="100%">&nbsp;</td></tr>
                    <tr>
                      <td width="100%">
                        <input class="btn btn-primary btn-sm submit_button" type="submit" value="<?php echo gks_lang('Εφαρμογή');?>">
                      </td>
                    </tr>
                  </tbody></table>
                </div>                

             
                            
              </td>
            </tr>
          </table>
          
          
        </form>

      </td>
    </tr>
  </tbody>
</table>  
        
<style>
  .delivery_payment_price {
    font-size: 12px;
  }  
</style>
<div class="container-fluid">
  <div class="row align-items-center1">
    <div class="col-md-6">

      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Τρόποι Αποστολής');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('delivery');?>>  
          
          <?php if ($perm_delivery_view) { ?>
          <table class="table table-sm table-responsive table-striped table-bordered gkstable" style="width:100%;" border="0" width="100%" cellspacing="0" cellpadding="5"  align=center>
            <tr>
              <th class="table-dark" scope="col" width="0%"  nowrap style="text-align: center !important;"><a href="?"><?php echo gks_lang('A/A');?></a></td>
              <th class="table-dark" scope="col" width="10%" nowrap style="text-align: center !important;"><?php echo gks_lang('ID');?></td>
              <th class="table-dark" scope="col" width="10%" nowrap style="text-align: center !important;"><span title="Εάν θα είναι ορατό ανάλογα με τις τιμές"><?php echo gks_lang('Ορατό');?></span></td>
              <th class="table-dark" scope="col" width="10%" nowrap style="text-align: center !important;"><?php echo gks_lang('Επιλογή');?></td>
              <th class="table-dark" scope="col" width="60%" nowrap style="text-align: left   !important;"><?php echo gks_lang('Περιγραφή');?></td>
              <th class="table-dark" scope="col" width="10%" nowrap style="text-align: center !important;"><?php echo gks_lang('Κόστος');?></td>
            </tr>
          
          
          <?php
            $i=0;
            foreach ($_gks_session['gks']['basket']['tropoi_apostolis_all'] as $row) {
              $i++;  
              $rowclass=($i % 2 == 0) ? 'even' : 'odd';
              if ($row['myisok']=='0')  $rowclass.=' mydisabledrow';
              
          ?>
            <tr class="<?php echo $rowclass;?>">
              <td class="mytdcm" nowrap><?php echo $i;?></td>
              <td class="mytdcm" nowrap><?php echo $row['id_delivery_method'];?>
                <a href="admin-delivery-methods-item.php?id=<?php echo $row['id_delivery_method'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
              </td>
              <td class="mytdcm" nowrap><img src="img/<?php echo $row['myisok'];?>.png" border="0" width="16"></td> 
              <td class="mytdcm" nowrap><?php if ($row['myisok'] =='1') { ?>
                <input type="radio" name="radio_delivery_way" value="<?php echo $row['id_delivery_method'];?>" id="radio_delivery_way_<?php echo $row['id_delivery_method'];?>"  
                  data-type="<?php echo $row['delivery_method_type'];?>" data-type-o="<?php echo $row['delivery_method_type_pa'];?>" >
              <?php } ?>
              </td>
          
              <td class="mytdcml"><label for="radio_delivery_way_<?php echo $row['id_delivery_method'];?>" style="cursor: pointer;" class="delivery_payment_label">
                <?php echo $row['delivery_method_name'];?></label>
              </td>
              <td class="mytdcm"><span class="delivery_payment_price" id="price_delivery_way_<?php echo $row['id_delivery_method'];?>" ><b><?php echo myCurrencyFormat($row['dm_calc_kostos'],true,true);?></b></span></td>
          
            </tr>               
          <?php }
          ?>
          
          
          </table>        
          <?php } ?>
          
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card gks_card_expand">
        <div class="card-header" style="text-align:center">
          <?php echo gks_lang('Τρόποι Πληρωμής');?>
        </div>
        <div class="card-body" <?php echo gks_card_body('payment');?>>  
          <?php if ($perm_payment_view) { ?>

          <table class="table table-sm table-responsive table-striped table-bordered gkstable" style="width:100%;" border="0" width="100%" cellspacing="0" cellpadding="5"  align=center>
            <tr>
              <th class="table-dark" scope="col" width="0%"  nowrap style="text-align: center !important;"><a href="?"><?php echo gks_lang('A/A');?></a></td>
              <th class="table-dark" scope="col" width="10%" nowrap style="text-align: center !important;"><?php echo gks_lang('ID');?></td>
              <th class="table-dark" scope="col" width="10%" nowrap style="text-align: center !important;"><span title="<?php echo gks_lang('Εάν θα είναι ορατό ανάλογα με τις τιμές');?>"><?php echo gks_lang('Ορατό');?></span></td>
              <th class="table-dark" scope="col" width="10%" nowrap style="text-align: center !important;"><?php echo gks_lang('Επιλογή');?></td>
              <th class="table-dark" scope="col" width="60%" nowrap style="text-align: left   !important;"><?php echo gks_lang('Περιγραφή');?></td>
              <th class="table-dark" scope="col" width="10%" nowrap style="text-align: center !important;"><?php echo gks_lang('Κόστος');?></td>
          
          
            </tr>
          
          
          <?php
            $i=0;
            foreach ($_gks_session['gks']['basket']['tropoi_pliromis_all'] as $row) {
              $i++;  
              
              $rowclass=($i % 2 == 0) ? 'even' : 'odd';
              if ($row['myisok']=='0')  $rowclass.=' mydisabledrow';
              
          ?>
            <tr class="<?php echo $rowclass;?>">    
              <td class="mytdcm" nowrap><?php echo $i;?></td>
              <td class="mytdcm" nowrap><?php echo $row['id_payment_acquirer'];?> 
                <a href="admin-payment-acquirers-item.php?id=<?php echo $row['id_payment_acquirer'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
              </td>    
              <td class="mytdcm" nowrap><img src="img/<?php echo $row['myisok'];?>.png" border="0" width="16"></td> 
              <td class="mytdcm" nowrap><?php if ($row['myisok'] =='1') { ?>
                <input type="radio" name="radio_payment_way" value="<?php echo $row['id_payment_acquirer'];?>" id="radio_payment_way_<?php echo $row['id_payment_acquirer'];?>" 
                  data-type="<?php echo $row['payment_acquirer_type'];?>" data-type-o="<?php echo $row['payment_acquirer_type_dm'];?>" >         
              <?php } ?>
              </td>
          
              <td class="mytdcml"><label for="radio_payment_way_<?php echo $row['id_payment_acquirer'];?>" style="cursor: pointer;" class="delivery_payment_label">
                <?php echo $row['payment_acquirer_name'];?></span></td>
              <td class="mytdcm"><span class="delivery_payment_price" id="price_payment_way_<?php echo $row['id_payment_acquirer'];?>" ><b><?php echo myCurrencyFormat($row['pa_calc_kostos'],true,true);?></b></span></td>
          
            </tr>               
          <?php } ?>
          </table>

          <?php } ?>
        </div>
      </div>

    </div>

  </div>
</div>












<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var dialog_message;
var dialog_confirm;
var myreload=false;
var timestamp = new Date().getTime();


jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  

  
  
  

  $('input[name=radio_delivery_way]').click( function() {
    mytype=$(this).attr('data-type');
    mytype_o=$(this).attr('data-type-o');

    $('input[name=radio_payment_way]').each(function( index ) {
      myto=$(this).attr('data-type-o');
      if (myto.indexOf('[' + mytype + ']') !== -1) {
        $(this).prop('disabled', false);
        $(this).parent().parent().removeClass('mydisabledrow');
      } else {
        $(this).prop('disabled', true);
        $(this).parent().parent().addClass('mydisabledrow');
        if ($(this).prop('checked')) {
          $(this).prop('checked',false);
        }
      }
    });
    
    $('input[name=radio_delivery_way]').each(function( index ) {
      $(this).prop('disabled', false);
      $(this).parent().parent().removeClass('mydisabledrow');
    });
  });
  $('input[name=radio_payment_way]').click(function() {
    mytype=$(this).attr('data-type');
    mytype_o=$(this).attr('data-type-o');
    

    $('input[name=radio_delivery_way]').each(function( index ) {
      myto=$(this).attr('data-type-o');
      if (myto.indexOf('[' + mytype + ']') !== -1) {
        $(this).prop('disabled', false);
        $(this).parent().parent().removeClass('mydisabledrow');
      } else {
        $(this).prop('disabled', true);
        $(this).parent().parent().addClass('mydisabledrow');
        if ($(this).prop('checked')) {
          $(this).prop('checked',false);
        }
      }
    });

    $('input[name=radio_payment_way]').each(function( index ) {
      $(this).prop('disabled', false);
      $(this).parent().parent().removeClass('mydisabledrow');
    });    
    

  });
  
    
});
  
</script>

<?php
db_close();
include_once('_my_footer_admin.php');

