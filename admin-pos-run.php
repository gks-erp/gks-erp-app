<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
//gks_permission_user_must_login_post();

// from=gks_erp_app_mobile
//https://test.easyfilesselection.com/my/admin-pos-run.php?id=10007&from=gks_erp_app_mobile&iderpappmobile=10001&_cache=0
//https://test.easyfilesselection.com/my/admin-pos-run.php?id=10006&from=gks_erp_app_mobile&iderpappmobile=10001&_cache=0
//https://demo.gks.gr/my/admin-pos-run.php?id=10002&from=gks_erp_app_mobile&iderpappmobile=10002&_cache=0

if (isset($_GET['id']) and isset($_GET['from']) and isset($_GET['ltype']) and (isset($_GET['token']) or isset($_GET['uname'])) and isset($_GET['mydate']) and isset($_GET['send1']) and isset($_GET['send2'])) {
  
  if (intval($_GET['id'])>0 and $_GET['from']=='gks_erp_app_mobile' and (strlen($_GET['token'])>=9 or strlen($_GET['uname'])>=4)  and strlen($_GET['mydate'])==19 and strlen($_GET['send1'])>=32 and strlen($_GET['send2'])>=32) {
    db_open();
    
    if ($_GET['ltype']!='token' and $_GET['ltype']!='user') die('dddddddddddddd');
    
    $pos_id=intval($_GET['id']);
    if ($_GET['ltype']=='token') {
      $sql="SELECT id_pos,pos_disable,app_mobile_userlogin_id from gks_pos 
      where id_pos=".$pos_id;
    } else if ($_GET['ltype']=='user') {
      $sql="select * from ".GKS_WP_TABLE_PREFIX."users
      where user_login='".$db_link->escape_string($_GET['uname'])."'";
      $result = $db_link->query($sql);  
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
      if ($result->num_rows==0) {debug_mail(false,'user pos not found (2)',$sql);die('user pos not found (2)');}
      $row = $result->fetch_assoc();
      $erp_app_mobile_user_id=intval($row['ID']);
      if ($erp_app_mobile_user_id<=0) {debug_mail(false,'user pos not found (3)',$sql);die('user pos not found (3)');}
      //echo 'fffff1';die();
      
      $sql="SELECT id_pos,pos_disable,app_mobile_userlogin_id from gks_pos 
      where id_pos=".$pos_id; //. " and app_mobile_userlogin_id=".$erp_app_mobile_user_id;
      //edo prepi na kano kapoion parapano elegxo, apo ta dikaomata toy xristi
      //
    } else {
      $sql="SELECT id_pos,pos_disable,app_mobile_userlogin_id from gks_pos 
      where id_pos=-1 and 1=2";
    }
    
    //debug_mail(false,'sql aaaaaaaa',$sql);
    
    $result = $db_link->query($sql);        
    if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
    if ($result->num_rows==0) {
      echo gks_lang('Δεν βρέθηκε το σημείο Εντατικής Λιανικής');die();
    }
    
    if ($result->num_rows==1) {
      $row_rpos=$result->fetch_assoc(); 
      $pos_id=intval($row_rpos['id_pos']);
      $app_mobile_userlogin_id=intval($row_rpos['app_mobile_userlogin_id']);
      if ($_GET['ltype']=='user') $app_mobile_userlogin_id=$erp_app_mobile_user_id;
      
      if (intval($row_rpos['pos_disable'])!=0) {
        echo gks_lang('Αυτό το σημείο Εντατικής Λιανικής έχει απενεργοποιηθεί');die();
      }
      
      if ($app_mobile_userlogin_id<=0) {
        echo gks_lang('Δεν έχει ορισθεί ο χρήστης για to gks ERP App Mobile σε αυτό το σημείο Εντατικής Λιανικής');die();
      }
      
      if ($_GET['ltype']=='token') {
        $erp_app_mobile_token=trim($_GET['token']);
        $sql="SELECT * from gks_erp_app_mobile 
        where erp_app_mobile_disabled=0 and erp_app_mobile_token='".$db_link->escape_string($erp_app_mobile_token)."'";
      }
      if ($_GET['ltype']=='user') {
        $sql="SELECT * from gks_erp_app_mobile 
        where erp_app_mobile_disabled=0 and erp_app_mobile_user_id=".$erp_app_mobile_user_id;
      }
      
      $result = $db_link->query($sql);        
      if (!$result) {debug_mail(false,'error sql',$sql);die('sql error');}
      if ($result->num_rows==1) {
        $row_app=$result->fetch_assoc(); 
        $erp_app_mobile_secret=$row_app['erp_app_mobile_secret'];
        $erp_app_mobile_user_token=$row_app['erp_app_mobile_user_token'];
        
        $id_erp_app_mobile=intval($row_app['id_erp_app_mobile']);
        
        $send1=trim($_GET['send1']);
        $send2=trim($_GET['send2']);
        $mydate=trim($_GET['mydate']);
        if ($_GET['ltype']=='token') {
          $calc2= md5($send1 . $pos_id . $mydate . $erp_app_mobile_secret . $send1 . $erp_app_mobile_token .  GKS_ERP_HASHMD5KEY01 . $send1);
        } else if ($_GET['ltype']=='user') {
          $calc2= md5($send1 . $pos_id . $mydate . $_GET['uname'] . $send1 . $erp_app_mobile_user_token .  GKS_ERP_HASHMD5KEY01 . $send1);
        } else {
          $calc2='ssssssssssssssssss';
        }
        //die('ssss|'.$calc2.'|'.$erp_app_mobile_secret.'|'.$erp_app_mobile_user_token);
        
        $diafora=strtotime($mydate) - _time_user(time(),1);
        //echo $diafora;die();
        if (abs($diafora)>60) {
          echo gks_lang('Σφάλμα 12343. Έχει η συσκευή σωστή ημερομηνία/ώρα ;');die();
        }
        
        if ($calc2==$send2) {
          if ($my_wp_user_id!=$app_mobile_userlogin_id) {
            wp_set_current_user($app_mobile_userlogin_id);
            wp_set_auth_cookie($app_mobile_userlogin_id);
          }
          
          $gourl='/my/admin-pos-run.php?id='.$pos_id.'&from=gks_erp_app_mobile&iderpappmobile='.$id_erp_app_mobile.'&_cache='.time();
          //echo $gourl;die();
          header('Location: '.$gourl);die();
        } else {
          echo gks_lang('Σφάλμα').' 12345';die();
        }

        //echo '<pre>ddddddddddddddd'."\n".$erp_app_mobile_secret."\n".$calc2."\n".$send2."\nuser:".$app_mobile_userlogin_id;die();
        
        
      } else {
        echo gks_lang('Σφάλμα').' 12346';die();
      }
    } else {
      echo gks_lang('Σφάλμα').' 12347';die();
    }
  } else {
    echo gks_lang('Σφάλμα').' 12348';die();
  }
}

$from_gks_erp_app_mobile=false;
if (isset($_GET['from']) and $_GET['from']=='gks_erp_app_mobile') $from_gks_erp_app_mobile=true;

$iderpappmobile=0; if (isset($_GET['iderpappmobile'])) $iderpappmobile=intval($_GET['iderpappmobile']);

$mydaydif=0;
$mytimenow=time() + $mydaydif*24*60*60; // + 0*24*60*60;
//$mytimenow=strtotime('2019-02-22 03:30:00');

$time_vardia=_time_user($mytimenow, 1);
$time_vardia-= GKS_ERP_START_VARDIA*60*60;
$today_vardia = date('Y-m-d',$time_vardia);
$today_vardia = strtotime($today_vardia);
//$today_vardia = _time_user($today_vardia, -1);
//$today_vardia = $today_vardia + GKS_ERP_START_VARDIA*60*60;
$today_vardia_time = $today_vardia;
$today_vardia = date('Y-m-d H:i:s', $today_vardia);

gks_permission_user_must_login_page();

//if ($from_gks_erp_app_mobile)

db_open();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'gks_pos_run','add',$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}
$perm_id_pos_run_ids=gks_permission_user_condition($my_wp_user_id,'gks_pos_run','01');
$print_x_days_back=gks_permission_user_int_cond($my_wp_user_id,'gks_pos_run','02');
//echo '<pre>';print_r($print_x_days_back);die();

$perm_id_company_ids=gks_permission_user_condition($my_wp_user_id,'gks_company','01');
$perm_id_company_sub_ids=gks_permission_user_condition($my_wp_user_id,'gks_company_subs','01');
$perm_id_acc_journal_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_journal','01');
$perm_id_acc_seira_ids=gks_permission_user_condition($my_wp_user_id,'gks_acc_seires','01');

$perm_gks_acc_inv_view  =gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','view',0);
$perm_gks_acc_inv_edit  =gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','edit',0);
$perm_gks_acc_inv_add   =gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','add',0);
$perm_gks_acc_inv_delete=gks_permission_user_can_action_php($my_wp_user_id,'gks_acc_inv','delete',0);

$gks_voip_params=gks_voip_user_params();
$perm_id_print_forms=gks_permission_user_condition($my_wp_user_id,'gks_print_forms','01');

$gks_pos_client_send_fileto_url='';
if (isset($_GET['gks_pos_client_send_fileto_url'])) $gks_pos_client_send_fileto_url=base64_decode(rawurldecode($_GET['gks_pos_client_send_fileto_url']));

//https://www.gks.gr/my/api_erp_pos_client_send_fileto_url.php?lab=3';

//echo time();
//die();

//print '<pre>';
//$out=array();
//gks_monada_convert(6, 8, $out,array());
//print_r($out);
//die();










if ($id <= 0) {header('Location: /my'); die(); }
$nav_active_array=array('accounting','accounting_inv_pos');


$user_companys=gks_get_companys_list();
if (count($user_companys)==0) {
  debug_mail(false,'company not found','');
  echo 'company not found';
  die(); 
}




$template_id=$id;



  
  
$sql="SELECT gks_pos.*, 
".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
gks_company.company_afm,gks_company.company_title,gks_company_subs.company_sub_title,
gks_payment_acquirers.payment_acquirer_name, gks_delivery_methods.delivery_method_name, 
gks_eshop_pricelist.pricelist_descr, gks_eshop_fiscal_position.fiscal_position_descr,
gks_users.order_sxolio,gks_users.pelati_sxolio,
gks_users.eponimia, gks_users.title, gks_users.afm, gks_users.doy, gks_users.epaggelma, 
gks_users.ma_odos, gks_users.ma_arithmos, gks_users.ma_orofos, gks_users.ma_perioxi, 
gks_users.ma_poli, gks_users.ma_tk, 
gks_users.ma_country_id,gks_users.ma_nomos_id,
".GKS_WP_TABLE_PREFIX."users.generic_ekprosi,
".GKS_WP_TABLE_PREFIX."users.user_email,
".GKS_WP_TABLE_PREFIX."users.gks_mobile,

gks_acc_journal.acc_journal_descr, gks_acc_seires.seira_code, gks_acc_seires.seira_descr,gks_acc_seires.is_xeirografi,gks_acc_seires.send_mydata,
gks_acc_journal.acc_eidos_parastatikou_id,
gks_acc_eidi_parastatikon.eidos_parastatikou_type_id, antisimvalomenos_label, gks_acc_eidi_parastatikon.eidos_parastatikou_need_prev, gks_acc_eidi_parastatikon.eidos_parastatikou_has_fpa,gks_acc_eidi_parastatikon.eidos_parastatikou_has_othertaxes, 
gks_acc_eidi_parastatikon.eidos_parastatikou_has_esoda, gks_acc_eidi_parastatikon.eidos_parastatikou_has_eksoda,gks_acc_eidi_parastatikon.eidos_parastatikou_need_afm,gks_acc_eidi_parastatikon.eidos_parastatikou_balance_pros,
whi_gks_acc_eidi_parastatikon.eidos_parastatikou_stock_pros as whi_eidos_parastatikou_stock_pros,
whi_gks_acc_eidi_parastatikon.eidos_parastatikou_type_id as whi_eidos_parastatikou_type_id,
gks_lang.lang_name,gks_country.country_name,gks_nomoi.nomos_descr,
myfirst_name,mylast_name,
gks_aade_skopos_diakinisis.aade_skopos_diakinisis_descr,
".GKS_WP_TABLE_PREFIX."users_assigned.gks_nickname AS gks_nickname_assigned, 
gks_crm_channel_sale.crm_channel_sale_descr, 
".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.gks_nickname as crm_channel_contact_gks_nickname,
gks_ads_campain.ads_campain_name,
gks_warehouses_from.warehouse_name AS warehouse_name_from, gks_warehouses_to.warehouse_name AS warehouse_name_to

FROM ((((((((((((((((((((((((gks_pos
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON gks_pos.def_user_id = ".GKS_WP_TABLE_PREFIX."users.ID) 
LEFT JOIN gks_company on gks_pos.pos_company_id = gks_company.id_company)
LEFT JOIN gks_company_subs on gks_pos.pos_company_sub_id = gks_company_subs.id_company_sub)
LEFT JOIN gks_acc_journal ON gks_pos.pos_journal_id = gks_acc_journal.id_acc_journal) 
LEFT JOIN gks_acc_eidi_parastatikon AS whi_gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_whi_id = whi_gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
LEFT JOIN gks_acc_seires ON gks_pos.pos_seira_id = gks_acc_seires.id_acc_seira)
LEFT JOIN gks_payment_acquirers ON gks_pos.def_tropos_pliromis = gks_payment_acquirers.id_payment_acquirer) 
LEFT JOIN gks_delivery_methods ON gks_pos.def_tropos_apostolis = gks_delivery_methods.id_delivery_method) 
LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id) 
LEFT JOIN gks_eshop_fiscal_position ON gks_pos.def_fiscal_position_id = gks_eshop_fiscal_position.id_fiscal_position) 
LEFT JOIN gks_eshop_pricelist ON gks_pos.def_pricelist_id = gks_eshop_pricelist.id_pricelist)
LEFT JOIN gks_country ON gks_users.ma_country_id = gks_country.id_country)
LEFT JOIN gks_nomoi ON gks_users.ma_nomos_id = gks_nomoi.id_nomos)
LEFT JOIN gks_lang ON gks_pos.def_user_lang = gks_lang.id_lang)
LEFT JOIN gks_aade_skopos_diakinisis ON gks_pos.def_aade_skopos_diakinisis_id = gks_aade_skopos_diakinisis.id_aade_skopos_diakinisis)
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_assigned ON gks_pos.def_assigned_id = ".GKS_WP_TABLE_PREFIX."users_assigned.ID) 
LEFT JOIN gks_crm_channel_sale ON gks_pos.def_crm_channel_id = gks_crm_channel_sale.id_crm_channel_sale) 
LEFT JOIN ".GKS_WP_TABLE_PREFIX."users AS ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact ON gks_pos.def_crm_channel_contact_id = ".GKS_WP_TABLE_PREFIX."users_crm_channel_contact.ID)
LEFT JOIN gks_ads_campain ON gks_pos.def_crm_channel_campain_id = gks_ads_campain.id_ads_campain)
LEFT JOIN gks_warehouses AS gks_warehouses_from ON gks_pos.pos_warehouses_id_from = gks_warehouses_from.id_warehouse) 
LEFT JOIN gks_warehouses AS gks_warehouses_to ON gks_pos.pos_warehouses_id_to = gks_warehouses_to.id_warehouse)
LEFT JOIN (
  SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS myfirst_name
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='first_name'))
)  AS table_first_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_first_name.user_id) 
LEFT JOIN (
  SELECT ".GKS_WP_TABLE_PREFIX."usermeta.user_id, ".GKS_WP_TABLE_PREFIX."usermeta.meta_value AS mylast_name
  FROM ".GKS_WP_TABLE_PREFIX."usermeta
  WHERE (((".GKS_WP_TABLE_PREFIX."usermeta.meta_key)='last_name'))
)  AS table_last_name ON ".GKS_WP_TABLE_PREFIX."users.ID = table_last_name.user_id


where pos_disable=0 and id_pos=".$id;

if (count($perm_id_pos_run_ids)>0) $sql.=" and gks_pos.id_pos in (".implode(',',$perm_id_pos_run_ids).")";


if (count($perm_id_company_ids)>0) $sql.=" and gks_pos.pos_company_id in (".implode(',',$perm_id_company_ids).")";
if (count($perm_id_company_sub_ids)>0) $sql.=" and gks_pos.pos_company_sub_id in (".implode(',',$perm_id_company_sub_ids).")";
if (count($perm_id_acc_journal_ids)>0) $sql.=" and gks_pos.pos_journal_id in (".implode(',',$perm_id_acc_journal_ids).")";
if (count($perm_id_acc_seira_ids)>0) $sql.=" and gks_pos.pos_seira_id in (".implode(',',$perm_id_acc_seira_ids).")";

$result = $db_link->query($sql);        
if (!$result) {
  debug_mail(false,'error sql',$sql);die('sql error');
}
  
if ($result->num_rows!=1) {
  debug_mail(false,'record not found sql tempate',$sql); 
  die('no record found (tempate)');
}
$row_pos=$result->fetch_assoc();
  
$row['id_acc_inv']=-1;
$row['cancel_for_acc_inv_id']=0;
$row['credit_memo_for_acc_inv_id']=0;
$row['dimotikos_foros_for_acc_inv_id']=0;
$row['from_aade_import']='';
$row['import_inv_acc_seira_code']='';
$row['import_inv_acc_number_str']='';
$row['import_eidos_parastatikou_aade_code']='';
$row['inv_guid']='';
$row['aade_skopos_diakinisis_id']=$row_pos['def_aade_skopos_diakinisis_id'];
$row['inv_date']=date('Y-m-d H:i:s');
$row['mydate_add']=null;
$row['mydate_edit']=null;
$row['user_id_add']=0;
$row['user_id_edit']=0;
$row['gks_nickname_add']='';
$row['gks_nickname_edit']='';
$row['myip']='';

$row['inv_state']='010draft';
$row['user_id']=$row_pos['def_user_id'];
$row['gks_nickname']=trim_gks($row_pos['gks_nickname']);
$row['user_first_name']=trim_gks($row_pos['myfirst_name']);
$row['user_last_name']=trim_gks($row_pos['mylast_name']);
$row['user_email']=trim_gks($row_pos['user_email']);
$row['user_mobile']=trim_gks($row_pos['gks_mobile']);
$row['user_lang']=trim_gks($row_pos['def_user_lang']);
$row['lang_name']=trim_gks($row_pos['lang_name']);

$row['eponimia']=trim_gks($row_pos['eponimia']);
$row['title']=trim_gks($row_pos['title']);
$row['afm']=trim_gks($row_pos['afm']);
$row['doy']=trim_gks($row_pos['doy']);
$row['epaggelma']=trim_gks($row_pos['epaggelma']);
$row['ma_odos']=trim_gks($row_pos['ma_odos']);
$row['ma_arithmos']=trim_gks($row_pos['ma_arithmos']);
$row['ma_orofos']=trim_gks($row_pos['ma_orofos']);
$row['ma_perioxi']=trim_gks($row_pos['ma_perioxi']);
$row['ma_poli']=trim_gks($row_pos['ma_poli']);
$row['ma_tk']=trim_gks($row_pos['ma_tk']);
$row['ma_country_id']=$row_pos['ma_country_id'];
$row['ma_nomos_id']=$row_pos['ma_nomos_id'];
$row['address_extra']=-1;

  

$row['note_doc']='';
$row['note_logistirio']='';



$row['gks_price_net']=0;
$row['gks_price_fpa']=0;
$row['gks_price_netfpa']=0;
$row['gks_price_total']=0;
$row['totalWithheldAmount']=0;
$row['totalOtherTaxesAmount']=0;
$row['totalStampDutyamount']=0;
$row['totalFeesAmount']=0;
$row['totalDeductionsAmount']=0;



$row['products_posotita']=0;
$row['products_varos']=0;
$row['products_ogos']=0;
$row['products_ogos_max_x']=0;
$row['products_ogos_max_y']=0;
$row['products_ogos_max_z']=0;
$row['products_need_apostoli']=0;
$row['products_need_pliromi']=0;

$row['tropos_apostolis']=$row_pos['def_tropos_apostolis'];
$row['tropos_pliromis']=$row_pos['def_tropos_pliromis'];
$row['kostos_apostolis']=0;
$row['kostos_pliromis']=0;
$row['destination_data_name']='';
$row['destination_data_phone']='';
$row['destination_data_odos']='';
$row['destination_data_arithmos']='';
$row['destination_data_orofos']='';
$row['destination_data_perioxi']='';
$row['destination_data_poli']='';
$row['destination_data_tk']='';
$row['destination_data_country_id']=0;
$row['destination_data_nomos_id']=0;

$row['fiscal_position_id']=$row_pos['def_fiscal_position_id'];
$row['pricelist_id']=$row_pos['def_pricelist_id'];

$row['pelati_sxolio']=trim_gks($row_pos['pelati_sxolio']);
$row['order_sxolio']=trim_gks($row_pos['order_sxolio']);

$row['delivery_id_8']=$row_pos['def_delivery_id_8'];
$row['delivery_number']='';
$row['vehicle_number']='';
$row['dispatch_date']='';
$row['coupons']='';
$row['def_ekptosi']=$row_pos['generic_ekprosi'];


$row['company_afm']=trim_gks($row_pos['company_afm']);
$row['company_id']=$row_pos['pos_company_id'];
$row['company_sub_id']=$row_pos['pos_company_sub_id'];

$row['inv_acc_journal_id']=$row_pos['pos_journal_id'];
$row['inv_acc_seira_id']=$row_pos['pos_seira_id'];
$row['inv_acc_seira_code']=$row_pos['seira_code'];
$row['inv_acc_number_int']=0;
$row['inv_acc_number_str']='';
$row['send_mydata']=0;
$row['is_xeirografi']=0;

$row['acc_eidos_parastatikou_id']=$row_pos['acc_eidos_parastatikou_id'];
$row['eidos_parastatikou_type_id']=$row_pos['eidos_parastatikou_type_id'];
$row['antisimvalomenos_label']=trim_gks($row_pos['antisimvalomenos_label']);
$row['eidos_parastatikou_need_prev']=$row_pos['eidos_parastatikou_need_prev'];
$row['eidos_parastatikou_has_fpa']=$row_pos['eidos_parastatikou_has_fpa'];
$row['eidos_parastatikou_has_othertaxes']=$row_pos['eidos_parastatikou_has_othertaxes'];
$row['eidos_parastatikou_has_esoda']=$row_pos['eidos_parastatikou_has_esoda'];
$row['eidos_parastatikou_has_eksoda']=$row_pos['eidos_parastatikou_has_eksoda'];
$row['eidos_parastatikou_need_afm']=$row_pos['eidos_parastatikou_need_afm'];
$row['eidos_parastatikou_balance_pros']=$row_pos['eidos_parastatikou_balance_pros'];
$row['whi_eidos_parastatikou_stock_pros']=$row_pos['eidos_parastatikou_need_afm'];
$row['whi_eidos_parastatikou_type_id']=$row_pos['whi_eidos_parastatikou_type_id'];

$row['aade_statuscode']='';
$row['aade_invoiceuid']='';
$row['aade_invoicemark']='';
$row['aade_send_date']='';
$row['aade_errors']='';

$row['print_date']='';
$row['print_file_name']='';
$row['print_file_url']='';
$row['print_user_id']='';
$row['print_inv_state']='';

$row['affect_balance']=$row_pos['def_affect_balance'];
$row['affect_balance_all_poso']=$row_pos['def_affect_balance_all_poso'];
$row['affect_balance_all_poso_type']=$row_pos['def_affect_balance_all_poso_type'];
$row['affect_balance_poso']=$row_pos['def_affect_balance_pros'];
 
$row['assigned_id']=$row_pos['def_assigned_id'];
$row['gks_nickname_assigned']=trim_gks($row_pos['gks_nickname_assigned']);
$row['crm_channel_id']=$row_pos['def_crm_channel_id'];
$row['crm_channel_sale_descr']=trim_gks($row_pos['crm_channel_sale_descr']);
$row['crm_channel_contact_id']=$row_pos['def_crm_channel_contact_id'];
$row['crm_channel_contact_gks_nickname']=trim_gks($row_pos['crm_channel_contact_gks_nickname']);
$row['crm_channel_campain_id']=$row_pos['def_crm_channel_campain_id'];
$row['ads_campain_name']=$row_pos['ads_campain_name'];
$row['crm_channel_url']=$row_pos['def_crm_channel_url'];
$row['crm_channel_code']=$row_pos['def_crm_channel_code'];
$row['crm_channel_text']=$row_pos['def_crm_channel_text'];

$row['warehouses_id_from']=$row_pos['pos_warehouses_id_from'];
$row['warehouse_name_from']=trim_gks($row_pos['warehouse_name_from']);
$row['warehouses_id_to']=$row_pos['pos_warehouses_id_to'];
$row['warehouse_name_to']=trim_gks($row_pos['warehouse_name_to']);
$row['bank_deposit_9digit']='';


$row['from_aade_import_json']='';

//print '<pre>'; print_r($row);die();

$my_page_title=gks_lang('Σημείο Εντατικής Λιανικής').': '.$row_pos['pos_name'];



$id_payment_acquirer_ids=[];$asset_ids=[];

$def_tropos_pliromis_array=array();
$temp=trim_gks($row_pos['def_tropos_pliromis_array']);
if ($temp!='') {
  $def_tropos_pliromis_array=json_decode($temp,true);
  foreach ($def_tropos_pliromis_array as &$value) {
    $value['asset_title']='';
    $id_payment_acquirer_ids[]=intval($value['id']);
    $asset_ids[]=intval($value['asset_id']);
  }
  unset($value);
}
//echo print_r($id_payment_acquirer_ids);die();

if (count($asset_ids)>0) {
  $sql="select id_asset,asset_title 
  from gks_assets 
  where id_asset in (".implode(',',$asset_ids).")
  and asset_disable=0";
  $result_select = $db_link->query($sql);        
  if (!$result_select) {
    debug_mail(false,'error sql',$sql);
    die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));}
  while ($row_select = $result_select->fetch_assoc()) {                
    foreach ($def_tropos_pliromis_array as &$def_value) {
      if ($def_value['asset_id']==$row_select['id_asset']) {
        $def_value['asset_title']=$row_select['asset_title'];
      }
    }
    unset($def_value);
  }
}
//echo '<pre>';print_r($def_tropos_pliromis_array);die();

$pway=array();
 if (count($id_payment_acquirer_ids)>0) {
  $sql_pway="SELECT id_payment_acquirer, payment_acquirer_name,
  aade_tropos_pliromis_id,payment_acquirer_with_id
  FROM gks_payment_acquirers
  where id_payment_acquirer in (".implode(',',$id_payment_acquirer_ids).")
  and payment_acquirer_disabled=0
  order by mysortorder,payment_acquirer_name";
  $result_pway = $db_link->query($sql_pway);        
  if (!$result_pway) {
    debug_mail(false,'error sql',$sql_pway);die('sql error');
  }
  while ($row_pway=$result_pway->fetch_assoc()) {
    //if ($row_pway['id_payment_acquirer']==$row_pos['def_tropos_pliromis'] or $row_pway['payment_acquirer_disabled']==0) {
      $row_pway['id_payment_acquirer']=intval($row_pway['id_payment_acquirer']);
      $row_pway['payment_acquirer_name']=trim_gks($row_pway['payment_acquirer_name']);
      $row_pway['aade_tropos_pliromis_id']=intval($row_pway['aade_tropos_pliromis_id']);
      $row_pway['payment_acquirer_with_id']=intval($row_pway['payment_acquirer_with_id']);
      $row_pway['asset_id']=0;
      $row_pway['asset_title']='';
      
      foreach ($def_tropos_pliromis_array as $def_value) {
        if ($row_pway['id_payment_acquirer']==$def_value['id']) {
          $row_pway['asset_id']=$def_value['asset_id'];
          $row_pway['asset_title']=$def_value['asset_title'];
        }
      }
      $pway[$row_pway['id_payment_acquirer']]=$row_pway;
    //}
  }
}
//echo '<pre>';print_r($pway);die();


stat_record();
$gks_header_footer_layout='empty';

include_once('_my_header_admin.php');
//print '<pre>';
//print_r($row);
//print '</pre>';

?>
<style>
<?php if ($row_pos['pos_can_search_products']!=1) {?>
#gks_pos_products_search_panel {
  display:none;  
}
<?php }?>   
</style>
<link href="css/admin-pos-run.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">
<link href="css/admin-eftpos-transaction-dialog.css?v=<?php echo $gks_cache_version;?>" rel="stylesheet">
<div style="display:none;" id="gks_hidden_data">
  <input type="text" id="inv_acc_journal_id" value="<?php echo $row_pos['pos_journal_id'];?>">
  <input type="text" id="inv_acc_seira_id"   value="<?php echo $row_pos['pos_seira_id'];?>">
</div>

<div class="gks_pos_header disable-select">
  <i class="fas fa-cog" id="gks_pos_menu_bars" title="<?php echo gks_lang('Ρυθμίσεις');?>"></i>
  <i class="fas fa-expand" id="gks_pos_menu_screen" title="<?php echo gks_lang('Πλήρης οθόνη');?>" data-val="0"></i>
  <div id="gks_pos_menu_print_x" title="<?php echo gks_lang('Εκτύπωση Χ');?>">X</div>
  <i class="fas fa-print" id="gks_pos_menu_reprint" title="<?php echo gks_lang('Επανεκτύπωση');?>"></i>
  <i class="fas fa-undo" id="gks_pos_menu_reset" title="<?php echo gks_lang('Μηδενισμός');?>"></i>
  <i class="fas fa-sign-out-alt" id="gks_pos_menu_exit" title="<?php echo gks_lang('Έξοδος');?>"></i>
  <div id="gks_pos_menu_title" title="<?php echo gks_lang('Πληροφορίες για αυτό το σημείο Εντατικής Λιανικής');?>"><i class="fas fa-info-circle gks_pos_menu_title_icon "></i><span><?php echo $row_pos['pos_name'];?></span></div>
</div>

<div id="gks_pos_panel_numpad" class="disable-select">
  
  <div class="gks_pos_panel_numpad_row">
    <div class="gks_pos_panel_numpad_row2">
      <div id="gks_pos_panel_numpad_header">
        <div id="gks_pos_panel_numpad_header2"></div>
      </div>
    </div>
  </div>
  <div class="gks_pos_panel_numpad_row">
    <div class="gks_pos_panel_numpad_row3">
      <div class="gks_pos_panel_numpad_btn" title="<?php echo gks_lang('Καθαρισμός');?>"><div data-key="C"><div><span>C</span></div></div></div>
      <div class="gks_pos_panel_numpad_btn" title="<?php echo gks_lang('Αξία');?> <b style='color:green'>x</b> Ποσότητα"><div data-key="X"><div><i class="fas fa-times"></i></div></div></div>
      <div class="gks_pos_panel_numpad_btn" title="<?php echo gks_lang('Διαγραφή τελευταίου ψηφίου');?>"><div data-key="B"><div><i class="fas fa-backspace"></i></div></div></div>
    </div>
  </div>
  <div class="gks_pos_panel_numpad_row">
    <div class="gks_pos_panel_numpad_row3">
      <div class="gks_pos_panel_numpad_btn"><div data-key="7"><div>7</div></div></div>
      <div class="gks_pos_panel_numpad_btn"><div data-key="8"><div>8</div></div></div>
      <div class="gks_pos_panel_numpad_btn"><div data-key="9"><div>9</div></div></div>
    </div>
  </div>
  <div class="gks_pos_panel_numpad_row">
    <div class="gks_pos_panel_numpad_row3">
      <div class="gks_pos_panel_numpad_btn"><div data-key="4"><div>4</div></div></div>
      <div class="gks_pos_panel_numpad_btn"><div data-key="5"><div>5</div></div></div>
      <div class="gks_pos_panel_numpad_btn"><div data-key="6"><div>6</div></div></div>
    </div>
  </div>
  <div class="gks_pos_panel_numpad_row">
    <div class="gks_pos_panel_numpad_row3">
      <div class="gks_pos_panel_numpad_btn"><div data-key="1"><div>1</div></div></div>
      <div class="gks_pos_panel_numpad_btn"><div data-key="2"><div>2</div></div></div>
      <div class="gks_pos_panel_numpad_btn"><div data-key="3"><div>3</div></div></div>
    </div>
  </div>
  <div class="gks_pos_panel_numpad_row">
    <div class="gks_pos_panel_numpad_row3">
      <div class="gks_pos_panel_numpad_btn"><div data-key=""><div> </div></div></div>
      <div class="gks_pos_panel_numpad_btn"><div data-key="0"><div>0</div></div></div>
      <div class="gks_pos_panel_numpad_btn" title="<?php echo gks_lang('Υποδιαστολή');?>"><div data-key="."><div>,</div></div></div>
    </div>
  </div>
  
  

  
  
  
</div>
<div id="gks_pos_panel_products" class="disable-select">
  <div id="gks_pos_products_search_panel">
    <i class="fas fa-search" id="gks_pos_products_search_button"></i>
    <input type="search" id="gks_pos_products_search" class="form-control form-control-sm" value="" placeholder="<?php echo gks_lang('Αναζήτηση ειδών');?>" autocomplete="<?php echo $autocomplete_gks_disable;?>"/>
  </div>
  <div id="gks_pos_products_loading" style="display:none;">
    <div class="progress" role="progressbar" aria-label="<?php echo gks_lang('Φόρτωση προϊόντων');?>" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
      <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
    </div>
  </div>
  <div id="gks_pos_products">
    <?php echo gks_lang('Φόρτωση προϊόντων');?> ...
  </div>
</div>
<div id="gks_pos_panel_bill" class="disable-select">
  <div id="gks_pos_panel_bill_customer">
    <i class="fas fa-user" id="gks_pos_panel_bill_customer_icon"
    data-def_user_id="<?php echo intval($row_pos['def_user_id']);?>"  
    data-def_user_nickname="<?php echo base64_encode(trim_gks($row_pos['gks_nickname']));?>"  
    
    data-sel_user_id="0"
    data-sel_user_nickname=""
    ></i>
    <span id="gks_pos_panel_bill_customer_inner">
    <?php echo trim_gks($row_pos['gks_nickname']);?>
    </span>
  </div>
  
  <div id="gks_pos_panel_bill_list">
    <div class="gks_pos_item_header">
      <div class="gks_pos_item_header_div">
        <div class="gks_pos_item_header_product"><?php echo gks_lang('Είδος');?></div>
        <div class="gks_pos_item_header_quantity"><?php echo gks_lang('Ποσότητα');?></div>
        <div class="gks_pos_item_header_price"><?php echo gks_lang('Τιμή');?></div>
      </div>        
    </div>
    <div style="clear: both;"></div>
    
    <?php for($i=0;$i<0;$i++) {?>
    <div class="gks_pos_item" data-aa="<?php echo $i;?>" data-priceperitem="5" data-vatperitem="0.1" data-quantity="<?php echo $i;?>">
      <div class="gks_pos_item_div">
        <div class="gks_pos_item_product">
          <span class="gks_pos_item_product_descr">test</span>
          
        </div>
        <div class="gks_pos_item_quantity">
          <i class="fas fa-minus-circle gks_pos_item_quantity_minus"></i>
          <span class="gks_pos_item_quantity_val"><?php echo $i;?></span>  
          <i class="fas fa-plus-circle  gks_pos_item_quantity_plus"></i>   
        </div>
        <div class="gks_pos_item_price">
          <span class="gks_pos_item_price_val">10,000,00</span>
          <i class="fas fa-trash-alt gks_pos_item_delete"></i>
        </div>
        <div style="clear: both;"></div> 
      </div>
    </div>
     
    <?php } ?>
  
  
  </div>  
  <div id="gks_pos_panel_bill_total">
    <div id="gks_pos_panel_bill_total_total">
      <span id="gks_pos_panel_bill_total_total_label"><?php echo gks_lang('Σύνολο');?>: </span><span id="gks_pos_panel_bill_total_total_value"></span>
    </div>
    <div id="gks_pos_panel_bill_total_tax">
       <span id="gks_pos_panel_bill_total_tax_label"><?php echo gks_lang('ΦΠΑ');?>: </span><span id="gks_pos_panel_bill_total_tax_value"></span>
       <span id="gks_pos_panel_bill_total_otax_label"><?php echo gks_lang('Άλλοι φόροι');?>: </span><span id="gks_pos_panel_bill_total_otax_value"></span>
    </div>
    
  
  </div> 
  <div id="gks_pos_panel_pway">
  <?php
    foreach ($pway as $pid => $pway_value) {
      echo '<div class="gks_pos_panel_pway_div"><input type="radio" name="pway" value="'.$pid.'" id="pway'.$pid.'" '.
      '><label for="pway'.$pid.'">'.$pway_value['payment_acquirer_name'].'</label></div>';
    }   
  ?>
  </div>

  <div id="gks_pos_panel_bill_pay">
    
    <input id="gks_merchant_ref_trns_text" type="number" class="form-control form-control-sm" placeholder="<?php echo gks_lang('Ένα μικρό σχόλιο');?>">
    
    <button class="btn btn-primary <?php echo($row_pos['pos_multi_copies']>0 ? 'wcopies' : '');?>" id="gks_pos_save"><?php echo gks_lang('Πληρωμή');?></button>
    
    <?php
    if ($row_pos['pos_multi_copies']>0) { 
     echo '<div id="pos_multi_copies_div">';
     echo '<span class="pos_multi_copies_span">x</span>';
     echo '<select id="pos_multi_copies" >';
     for($copies=1;$copies<=$row_pos['pos_multi_copies'];$copies++) {
      echo '<option value="'.$copies.'"'.'>'.$copies.'</option>';
      }
     
     echo '</select></div>';
    }?>
  </div>  

</div>
<div id="dialog_price_change_back" style="display:none;">
  <div id="dialog_price_change">
    <input id="dialog_price_change_item"  type="number" class="form-control form-control-sm" value=""  min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>">
    <i id="dialog_price_change_label1" class="fas fa-times"></i>
    <span id="dialog_price_change_label_quantity">99999999</span>
    <i id="dialog_price_change_label3" class="fas fa-equals"></i>
    <input id="dialog_price_change_total" type="number" class="form-control form-control-sm" value=""  min="0" step="<?php echo $GKS_INPUT_STEP_AJIA;?>">
    <i id="dialog_price_change_ok" class="fas fa-caret-square-right"></i>
  </div>
</div>

<div id="gks_pos_panel_settings">
  <div id="gks_pos_panel_settings_inner">
    <div id="gks_pos_panel_settings_header">
      <span><?php echo $row_pos['pos_name'];?></span>
      <span><?php echo gks_lang('Ρυθμίσεις');?></span>
    </div>
    <i class="fas fa-window-close" id="gks_pos_panel_settings_close"></i>
    <div id="gks_pos_panel_settings_data">
      <div class="gks_pos_panel_settings_data2 container-fluid">
        <div class="col-md-12">

          <div class="form-group row row_gks_layout">
            <label for="gks_layout" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Διάταξη');?>:</label>
            <div class="col-md-8">
              <input type="radio" name="gks_layout" id="gks_layout_normal" value="normal">  
                <label class="form-control-sm" for="gks_layout_normal"><i class="fas fa-list"></i> <?php echo gks_lang('Κανονικό');?></label>
              <br>
              <input type="radio" name="gks_layout" id="gks_layout_numpad" value="numpad">  
                <label class="form-control-sm" for="gks_layout_numpad"><i class="fas fa-th"></i> <?php echo gks_lang('Με αριθμητικό πληκτρολόγιο');?></label>

            </div>
          </div> 
          <div class="form-group row row_gks_check_exist_elem">
            <label for="gks_check_exist_elem" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Εάν ένα είδος υπάρχει ήδη');?>:</label>
            <div class="col-md-8">
              <input type="radio" name="gks_check_exist_elem" id="gks_check_exist_elem_addline" value="0">  
                <label class="form-control-sm" for="gks_check_exist_elem_addline"><i class="fas fa-grip-lines"></i> <?php echo gks_lang('Προσθήκη νέας γραμμής');?></label>
              <br>
              <input type="radio" name="gks_check_exist_elem" id="gks_check_exist_elem_plusposotita" value="1">  
                <label class="form-control-sm" for="gks_check_exist_elem_plusposotita"><i class="fas fa-plus-circle"></i> <?php echo gks_lang('Αύξηση ποσότητας στο υπάρχον είδος');?></label>

            </div>
          </div>
          <div class="form-group row row_gks_pay_mode">
            <label for="gks_pay_mode" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Πληρωμή');?>:</label>
            <div class="col-md-8">
              <input type="radio" name="gks_pay_mode" id="gks_pay_mode_twosteps" value="0">  
                <label class="form-control-sm" for="gks_pay_mode_twosteps"><i class="fas fa-dice-two"></i> <?php echo gks_lang('Σε δύο βήματα');?></label>
              <br>
              <input type="radio" name="gks_pay_mode" id="gks_pay_mode_fast" value="1">  
                <label class="form-control-sm" for="gks_pay_mode_fast"><i class="fas fa-dice-one"></i> <?php echo gks_lang('Άμεσο');?></label>

            </div>
          </div>   
          <div class="form-group row row_gks_merchant_ref_enable">
            <label for="gks_merchant_ref_enable" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Ένα μικρό σχόλιο');?>:</label>
            <div class="col-md-8">
              <div id="gks_merchant_ref_div">
                <input type="checkbox" id="gks_merchant_ref_enable" value="1" class="switchery1_sel">
                <input type="number" id="gks_merchant_ref_def_value" class="form-control form-control-sm">
              </div>
            </div>            
          </div> 
          <div class="form-group row row_gks_multi_copies_enable">
            <label for="gks_multi_copies_enable" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Πολλαπλά αντίγραφα');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="gks_multi_copies_enable" value="1" class="switchery1_sel">
            </div>
          </div> 
                           
          <div class="form-group row row_gks_customer">
            <label for="gks_customer" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Επιλογή πελάτη');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="gks_customer" value="1" class="switchery1_sel">
            </div>
          </div>          
          <div class="form-group row row_gks_msg_ok_show">
            <label for="gks_msg_ok_show" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Εμφάνιση μηνύματος επιτυχούς έκδοσης παραστατικού');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="gks_msg_ok_show" value="1" class="switchery1_sel">
            </div>
          </div>
          <div class="form-group row row_gks_min_clicks">
            <label for="gks_min_clicks" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Ελάχιστα κλικς');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="gks_min_clicks" value="1" class="switchery1_sel">
            </div>
          </div>                   
          <div class="form-group row row_gks_edit_quantity">
            <label for="gks_edit_quantity" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Επεξεργασία ποσότητας');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="gks_edit_quantity" value="1" class="switchery1_sel">
            </div>
          </div>

                    
          <div class="form-group row row_gks_edit_price">
            <label for="gks_edit_price" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Επεξεργασία αξίας');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="gks_edit_price" value="1" class="switchery1_sel">
            </div>
          </div>
          <div class="form-group row row_gks_show_fpa">
            <label for="gks_show_fpa" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Εμφάνιση ΦΠΑ');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="gks_show_fpa" value="1" class="switchery1_sel">
            </div>
          </div>
          <div class="form-group row row_gks_delete_item">
            <label for="gks_delete_item" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Αφαίρεση είδους');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="gks_delete_item" value="1" class="switchery1_sel">
            </div>
          </div>

          <div class="form-group row row_gks_audio">
            <label for="gks_audio" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Ήχοι');?>:</label>
            <div class="col-md-8">
              <input type="checkbox" id="gks_audio" value="1" class="switchery1_sel">
            </div>
          </div>
                    
          <div class="form-group row row_gks_zoom_item">
            <label for="zoom_item" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Πλάτος είδους');?>:</label>
            <div class="col-md-8">
              <div class="gks_slider_max_width">
                <div id="zoom_item_slider"  class="gks_slider">
                  <div id="zoom_item_slider_handle" class="ui-slider-handle"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group row row_gks_width_normal_landscape_products">
            <label for="width_normal_landscape_products" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Πλάτος στήλης <b>ειδών</b> όταν η διάταξη είναι <b>Κανονική</b> και το <b>πλάτος &gt; ύψος</b>');?>: <i class="fas fa-circle row_gks_width_active_icon" title="Τρέχουσα διάταξη"></i></label>
            <div class="col-md-8">
              <div class="gks_slider_max_width">
                <div id="width_normal_landscape_products_slider" class="gks_slider">
                  <div id="width_normal_landscape_products_slider_handle" class="ui-slider-handle"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group row row_gks_width_numpad_landscape_numpad">
            <label for="width_numpad_landscape_numpad" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Πλάτος στήλης <b>αριθμητικού πληκτρολογίου</b> όταν η διάταξη είναι <b>Με αριθμητικό πληκτρολόγιο</b> και το <b>πλάτος &gt; ύψος</b>');?>: <i class="fas fa-circle row_gks_width_active_icon" title="<?php echo gks_lang('Τρέχουσα διάταξη');?>"></i></label>
            <div class="col-md-8">
              <div class="gks_slider_max_width">
                <div id="width_numpad_landscape_numpad_slider" class="gks_slider">
                  <div id="width_numpad_landscape_numpad_slider_handle" class="ui-slider-handle"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group row row_gks_width_numpad_landscape_products">
            <label for="width_numpad_landscape_products" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Πλάτος στήλης <b>ειδών</b> όταν η διάταξη είναι <b>Με αριθμητικό πληκτρολόγιο</b> και το <b>πλάτος &gt; ύψος</b>');?>: <i class="fas fa-circle row_gks_width_active_icon" title="<?php echo gks_lang('Τρέχουσα διάταξη');?>"></i></label>
            <div class="col-md-8">
              <div class="gks_slider_max_width">
                <div id="width_numpad_landscape_products_slider" class="gks_slider">
                  <div id="width_numpad_landscape_products_slider_handle" class="ui-slider-handle"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="form-group row row_gks_width_numpad_portrait_numpad">
            <label for="width_numpad_portrait_numpad_slider" class="col-md-4 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Πλάτος στήλης <b>αριθμητικού πληκτρολογίου</b> όταν η διάταξη είναι <b>Με αριθμητικό πληκτρολόγιο</b> και το <b>πλάτος &lt; ύψος</b>');?>: <i class="fas fa-circle row_gks_width_active_icon" title="<?php echo gks_lang('Τρέχουσα διάταξη');?>"></i></label>
            <div class="col-md-8">
              <div class="gks_slider_max_width">
                <div id="width_numpad_portrait_numpad_slider" class="gks_slider">
                  <div id="width_numpad_portrait_numpad_slider_handle" class="ui-slider-handle"></div>
                </div>
              </div>
            </div>
          </div>



        </div>
      </div>
    
      <div class="gks_pos_panel_settings_footer">
        
        <button class="btn btn-primary" id="gks_pos_panel_settings_save"><?php echo gks_lang('Εφαρμογή');?></button>
        <button class="btn btn-danger" id="gks_pos_panel_settings_cancel"><?php echo gks_lang('Ακύρωση');?></button>
      </div>
    
    </div>
  </div>
</div>


<div id="gks_pos_panel_pay">
  <div id="gks_pos_panel_pay_inner">
    <div id="gks_pos_panel_pay_header">
      <?php echo gks_lang('Πληρωμή');?>
    </div>
    <i class="fas fa-window-close" id="gks_pos_panel_pay_close"></i>
    <div id="gks_pos_panel_pay_data">
      <div class="gks_pos_panel_pay_data2 container-fluid">
        
        <?php
        foreach ($pway as $pid => $pway_value) {
          echo 
          '<div class="col-md-12 ">'.
            '<div class="form-group1 row row_pay justify-content-center1">'.
            '<button class="btn btn-lg btn-primary gks_pos_panel_pay_btn" 
            data-id="'.$pid.'"
            data-pawid="'.$pway_value['payment_acquirer_with_id'].'"
            >'.$pway_value['payment_acquirer_name'].'</button>';
            
          if ($pway_value['payment_acquirer_with_id']>0) { ?>
            <div class="div_payment_one_terminal" data-one_pway="<?php echo $pway_value['id_payment_acquirer'];?>">
              <button class="div_payment_one_terminal_start div_payment_one_terminal_start_css btn btn-lg btn-warning"><?php echo gks_lang('Πληρωμή με');?>:</button>
              <input data-pp="-1000" data-pawid="<?php echo $pway_value['payment_acquirer_with_id'];?>" class="div_payment_one_terminal_terminal form-control form-control-sm" type="text" placeholder="<?php echo gks_lang('Τερματικό');?>"
              data-asset_id="<?php echo $pway_value['asset_id'];?>"
              value="<?php echo $pway_value['asset_title'];?>">
              <div data-cpawid="<?php echo $pway_value['payment_acquirer_with_id'];?>" class="payxxx_company_eponimia"></div>
            </div>
            
          <?php }          
          echo 
            '</div>'.
          '</div>';
          
        } 
        ?>

      </div>

      <div class="gks_pos_panel_pay_footer">
        <button class="btn btn-lg btn-danger" id="gks_pos_panel_pay_cancel"><?php echo gks_lang('Ακύρωση');?></button>
      </div>
          
    </div>
  </div>
</div>


<div id="gks_pos_panel_customer">
  <div id="gks_pos_panel_customer_inner">
    <div id="gks_pos_panel_customer_header">
      <?php echo gks_lang('Πελάτης');?>
    </div>
    <i class="fas fa-window-close" id="gks_pos_panel_customer_close"></i>
    <div id="gks_pos_panel_customer_data">
      <div class="gks_pos_panel_customer_data2 container-fluid">
        
        <div class="form-group row">
          <label for="user" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επαφή');?>:</label>
          <div class="col-sm-4">
            <input id="user" type="text" class="form-control form-control-sm"
            value="" 
            style="width:calc(98% - 22px);display:inline;" 
            placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>">
            <input id="user_id" type="hidden" value="-1">
            
            <a id="autocomplete_user_id" tabindex="-1" href="admin-users-item.php?id=-1" style="display:none"><i class="fas fa-search-plus" style="color: goldenrod;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Προβολή επαφής');?>"></i></a>
            <i id="user_save" class="fas fa-save" style="color: #35dc35;cursor: pointer;vertical-align: middle;" title="<?php echo gks_lang('Δημιουργία επαφής');?>"></i>
          </div>
        </div>

        <div class="form-group row" style="margin-bottom: 0px;">
          <div class="col-sm-6">
            <div class="form-group1 row" id="div_pelati_sxolio" style="display:none;margin-bottom: 1rem;padding-right: 15px;padding-left: 30px;">
              <div class="offset-md-4 col-sm-8 alert alert-danger" role="alert" id="text_pelati_sxolio" style="margin-bottom: 0px;"></div>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="form-group1 row" id="div_order_sxolio" style="display:none;margin-bottom: 1rem;padding-right: 15px;padding-left: 30px;">
              <div class="offset-md-4 col-sm-8 alert alert-danger" role="alert" id="text_order_sxolio" style="margin-bottom: 0px;"></div>
            </div>               
          </div>
        </div>

        <div class="form-group row">
          <label for="dr_user_first_name" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όνομα');?>:</label>
          <div class="col-sm-4">

            <input id="dr_user_first_name" type="text" class="form-control form-control-sm" value="">
          </div>
          <label for="dr_user_last_name" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Επώνυμο');?>:</label>
          <div class="col-sm-4">
             <input id="dr_user_last_name" type="text" class="form-control form-control-sm" value="">
          </div>
        </div>
        <div class="form-group row">
          <label for="dr_user_email" class="col-sm-2 col-form-label form-control-sm text-sm-right">email:</label>
          <div class="col-sm-4">
            <input id="dr_user_email" type="text" class="form-control form-control-sm" value="">
          </div>
          <label for="dr_user_mobile" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Τηλέφωνο');?>:</label>
          <div class="col-sm-4">
            <input id="dr_user_mobile" type="text" class="form-control form-control-sm <?php echo $gks_voip_params['class_input'];?>" value="">
            <?php echo $gks_voip_params['html_after_input'];?>
          </div>
        </div>
            
        <div class="form-group row">
          <label for="dr_user_lang" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Γλώσσα');?>:</label>
          <div class="col-sm-4">

            <select id="dr_user_lang" class="form-control form-control-sm">
              <option value=""></option>
              <?php
              $lang_prepare_gks_lang=gks_lang_data_obj_prepare('gks_lang','default');
              gks_lang_data_obj_sql_prepare($lang_prepare_gks_lang, array('lang_name'));
              $sql_lang="select id_lang,lang_ico,".gks_lang_sql_field('lang_name',$lang_prepare_gks_lang)." 
              FROM ".$lang_prepare_gks_lang['sql']['from1']." gks_lang 
              ".$lang_prepare_gks_lang['sql']['from2']."
              ORDER BY lang_sortorder,lang_name";                  
              $result_select_lang = $db_link->query($sql_lang);        
              if (!$result_select_lang) {
                debug_mail(false,'error sql',$sql_lang);
                die(gks_lang('Σφάλμα SQL').'<br>'.gks_lang('Παρακαλώ δοκιμάστε αργότερα'));
              }
              while ($row_select_lang = $result_select_lang->fetch_assoc()) {
                echo '<option value="'.$row_select_lang['id_lang'].'" ';
                
                echo '>'.$row_select_lang['lang_name'].'</option>';
              }
              ?>
            </select>                  
           </div>
        </div>
   
        <div class="row">  
          <div class="col-md-12">
            <div class="text-sm-center" style="font-weight: bold;"><?php echo gks_lang('Διεύθυνση');?></div>
          </div>
        </div>  
        <div class="form-group row">
          <label for="dr_user_ma_odos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Οδός');?>:</label>
          <div class="col-sm-4">
            <input id="dr_user_ma_odos" type="text" class="form-control form-control-sm" value="">
            <small class="form-text text-muted auto_googlemaps" id="dr_user_ma_odos_auto_googlemaps"></small>
          </div>
          <label for="dr_user_ma_arithmos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Αριθμός');?>:</label>
          <div class="col-sm-4">
            <input id="dr_user_ma_arithmos" type="text" class="form-control form-control-sm" value="">
          </div>
        </div>
        <div class="form-group row">
          <label for="dr_user_ma_orofos" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Όροφος');?>:</label>
          <div class="col-sm-4">
            <input id="dr_user_ma_orofos" type="text" class="form-control form-control-sm" value="" >
          </div>
          <label for="dr_user_ma_perioxi" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Περιοχή');?>:</label>
          <div class="col-sm-4">
            <input id="dr_user_ma_perioxi" type="text" class="form-control form-control-sm" value="" >
          </div>
        </div>

        <div class="form-group row">
          <label for="dr_user_ma_poli" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Πόλη');?>:</label>
          <div class="col-sm-4">
            <input id="dr_user_ma_poli" type="text" class="form-control form-control-sm" value="">
          </div>
          <label for="dr_user_ma_tk" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('ΤΚ');?>:</label>
          <div class="col-sm-4">
            <input id="dr_user_ma_tk" type="text" class="form-control form-control-sm" value="">
          </div>
        </div>

        <div class="form-group row">
          <label for="dr_user_ma_country_id" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Χώρα');?>:</label>
          <div class="col-sm-4">
            <select data-dbval="" id="dr_user_ma_country_id" class="form-control form-control-sm">
            </select> 
          </div>
          <label for="dr_user_ma_nomos_id" class="col-sm-2 col-form-label form-control-sm text-sm-right"><?php echo gks_lang('Νομός');?>:</label>
          <div class="col-sm-4">
            <select id="dr_user_ma_nomos_id" class="form-control form-control-sm">
              <option value="0"><?php echo gks_lang('Νομός');?>...</option>
            </select> 
          </div>
        </div>   

      </div>

      <div class="gks_pos_panel_customer_footer">
        <button class="btn btn-primary" id="gks_pos_panel_customer_ok"><?php echo gks_lang('Επιλογή');?></button>
        <button class="btn btn-danger" id="gks_pos_panel_customer_cancel"><?php echo gks_lang('Ακύρωση');?></button>
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


<div id="gks_pos_panel_info">
  <div id="gks_pos_panel_info_inner">
    <div id="gks_pos_panel_info_header">
      <span><?php echo $row_pos['pos_name'];?></span>
      <span><?php echo gks_lang('Πληροφορίες');?></span>
    </div>
    <i class="fas fa-window-close" id="gks_pos_panel_info_close"></i>
    <div id="gks_pos_panel_info_data">
      <div class="gks_pos_panel_info_data2 container-fluid">

        <div class="form-group row">
          <label class="col-md-6 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Όνομα σημείου Εντατικής Λιανικής');?>:</label>
          <div class="col-md-6">
            <div class="gks_flock form-control-sm">
            <?php echo htmlspecialchars_gks($row_pos['pos_name']);?>
            </div>
          </div>
        </div>
        <?php if (!empty($row_pos['pos_descr'])) {?>
        <div class="form-group row">
          <label class="col-md-6 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Περιγραφή');?>:</label>
          <div class="col-md-6">
            <div class="gks_flock form-control-sm">
            <?php echo nl2br_gks(htmlspecialchars_gks($row_pos['pos_descr']));?>
            </div>
          </div>
        </div>
        <?php } ?>
        <div class="form-group row">
          <label class="col-md-6 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Εταιρεία');?>:</label>
          <div class="col-md-6">
            <div class="gks_flock form-control-sm">
            <?php echo htmlspecialchars_gks($row_pos['company_title']);
            if (!empty($row_pos['company_sub_title'])) echo ' \ '.$row_pos['company_sub_title'];
            ?>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-md-6 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Ημερολόγιο');?>:</label>
          <div class="col-md-6">
            <div class="gks_flock form-control-sm">
            <?php echo htmlspecialchars_gks($row_pos['acc_journal_descr']);?>
            </div>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-md-6 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Σειρά');?>:</label>
          <div class="col-md-6">
            <div class="gks_flock form-control-sm">
            <?php echo htmlspecialchars_gks($row_pos['seira_code']).' - '.htmlspecialchars_gks($row_pos['seira_descr']);?>
            </div>
          </div>
        </div>     
        <div class="form-group row">
          <label class="col-md-6 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Φορολογική Θέση');?>:</label>
          <div class="col-md-6">
            <div class="gks_flock form-control-sm">
            <?php echo htmlspecialchars_gks($row_pos['fiscal_position_descr']);?>
            </div>
          </div>
        </div>     
        <div class="form-group row">
          <label class="col-md-6 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Τιμοκατάλογος');?>:</label>
          <div class="col-md-6">
            <div class="gks_flock form-control-sm">
            <?php echo htmlspecialchars_gks($row_pos['pricelist_descr']);?>
            </div>
          </div>
        </div>     
        <div class="form-group row">
          <label class="col-md-6 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Πελάτης');?>:</label>
          <div class="col-md-6">
            <div class="gks_flock form-control-sm">
            <?php echo htmlspecialchars_gks($row_pos['gks_nickname']);?>
            </div>
          </div>
        </div>         
        
        <div class="form-group row">
          <label class="col-md-6 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Είστε ο/η');?>:</label>
          <div class="col-md-6">
            <div class="gks_flock form-control-sm">
            <strong>
              <?php echo htmlspecialchars_gks($my_wp_user_info->data->display_name);?>
            </strong>
            </div>
          </div>
        </div>        
        
        <div class="form-group row">
          <label class="col-md-6 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Εκτυπωτής');?>:</label>
          <div class="col-md-6">
            <div class="gks_flock form-control-sm" id="gks_erp_app_mobile_local_printers_div"></div>
          </div>
        </div>         
        <div class="form-group row">
          <label class="col-md-6 col-form-label form-control-sm text-md-right gks_unset_height"><?php echo gks_lang('Οθόνη');?>:</label>
          <div class="col-md-6">
            <div class="gks_flock form-control-sm">
              <span id="gks_erp_app_mobile_screen_span"></span>  
              <span id="gks_erp_app_mobile_device_type_span"></span>
            </div>
          </div>
        </div>         

        <div class="form-group row">
          <label class="col-md-6 col-form-label form-control-sm text-md-right gks_unset_height">GPS:</label>
          <div class="col-md-6">
            <div class="gks_flock form-control-sm" id="gks_get_gps_location_report"><?php echo gks_lang('Αναμονή');?>...</div>
          </div>
        </div> 
      </div>
      <div class="gks_pos_panel_info_footer">
        <button class="btn btn-primary" id="gks_pos_panel_info_ok"><?php echo gks_lang('Κλείσιμο');?></button>
      </div>
          
    </div>
  </div>
</div>
        
        
<div id="gks_pos_panel_reprint">
  <div id="gks_pos_panel_reprint_inner">
    <div id="gks_pos_panel_reprint_header">
      <span><?php echo $row_pos['pos_name'];?></span>
      <span><?php echo gks_lang('Επανεκτύπωση');?></span>
      <button class="btn btn-primary btn-sm1" id="gks_pos_panel_reprint_header_reload"><i class="fas fa-sync-alt"></i></button>
    </div>
    <i class="fas fa-window-close" id="gks_pos_panel_reprint_close"></i>
    <div id="gks_pos_panel_reprint_data">
      <div class="gks_pos_panel_reprint_data2 container-fluid">
      </div>
      <div class="gks_pos_panel_reprint_footer">
        <button class="btn btn-primary" id="gks_pos_panel_reprint_ok"><?php echo gks_lang('Κλείσιμο');?></button>
      </div>
    </div>
  </div>
</div>

<div id="gks_pos_panel_print_x">
  <div id="gks_pos_panel_print_x_inner">
    <div id="gks_pos_panel_print_x_header">
      <span><?php echo $row_pos['pos_name'];?></span>
      <span><?php echo gks_lang('Εκτύπωση Χ');?></span>
      <button class="btn btn-primary btn-sm1" id="gks_pos_panel_print_x_header_reload"><i class="fas fa-sync-alt"></i></button>
    </div>

    <i class="fas fa-window-close" id="gks_pos_panel_print_x_close"></i>
    <div id="gks_pos_panel_print_x_data">
      <?php if ($print_x_days_back>0) {?>
      <div id="gks_pos_panel_print_x_data_filter">
        <?php echo gks_lang('Ημερομηνία');?> <input type="text" class="form-control form-control-sm" value="<?php echo showDate($today_vardia_time, 'd/m/Y', 1); ?>" id="gks_pos_panel_print_x_data_filter_date">
      </div>      
      <?php } ?>
      
      <div class="gks_pos_panel_print_x_data2 container-fluid">
      </div>
      <div id="gks_pos_panel_print_x_data2_printbtn" class="container-fluid">
        <button class="btn btn-primary" id="gks_pos_panel_print_x_to_local"><?php echo gks_lang('Εκτύπωση');?></button>
      </div>
      <div id="gks_pos_panel_print_x_data2_qrcode" class="container-fluid">
      </div>
      <div class="gks_pos_panel_print_x_footer">
        <button class="btn btn-primary" id="gks_pos_panel_print_x_ok"><?php echo gks_lang('Κλείσιμο');?></button>
      </div>
    </div>
  </div>
</div>
        
        
<style id="gks_pos_run_cstyle">
  
  
</style>

<audio id="eft-pos-done" controls style="position: absolute;left: -1000px;top: -1000px;"><source src="/my/audio/eft-pos-done.mp3" type="audio/mpeg"></audio>
  

<?php include_once('admin-eftpos-transaction-dialog.php');
$sql_ppm="SELECT viva_preferred_payment_methods, mellon_preferred_payment_methods, 
cardlink_preferred_payment_methods, epay_preferred_payment_methods, 
worldline_preferred_payment_methods, nexi_preferred_payment_methods
FROM gks_pos LEFT JOIN gks_company ON gks_pos.pos_company_id = gks_company.id_company
WHERE gks_pos.id_pos=".$id." AND gks_company.id_company Is Not Null";
$result_ppm = $db_link->query($sql_ppm);        
if (!$result_ppm) {debug_mail(false,'error sql',$sql_ppm);die('sql error');}
$preferred_payment_methods=array(); 
while ($row_ppm = $result_ppm->fetch_assoc()) {
  $temp=trim_gks($row_ppm['viva_preferred_payment_methods']);if ($temp=='') $temp='[]';
  $preferred_payment_methods['viva']=json_decode($temp,true);
  $temp=trim_gks($row_ppm['mellon_preferred_payment_methods']);if ($temp=='') $temp='[]';
  $preferred_payment_methods['mellon']=json_decode($temp,true);
  $temp=trim_gks($row_ppm['cardlink_preferred_payment_methods']);if ($temp=='') $temp='[]';
  $preferred_payment_methods['cardlink']=json_decode($temp,true);
  $temp=trim_gks($row_ppm['epay_preferred_payment_methods']);if ($temp=='') $temp='[]';
  $preferred_payment_methods['epay']=json_decode($temp,true);
  $temp=trim_gks($row_ppm['worldline_preferred_payment_methods']);if ($temp=='') $temp='[]';
  $preferred_payment_methods['worldline']=json_decode($temp,true);
  $temp=trim_gks($row_ppm['nexi_preferred_payment_methods']);if ($temp=='') $temp='[]';
  $preferred_payment_methods['nexi']=json_decode($temp,true);
  
  //print '<pre>';print_r($preferred_payment_methods);die();
  break;
}
?>

<?php
//print '<pre>';
//print_r($mybasketarray);
//print '</pre>';

?>

<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

var from_php_user_settings_autocomplete_address='<?php echo $gks_user_settings['autocomplete']['address'];?>';


var from_php_id_pos=<?php echo $id;?>;
var from_php_user_id=<?php echo $my_wp_user_id;?>;
var from_php_id=-1;
var from_php_pos_step='';

var from_php_gks_pos_client_send_fileto_url=$.base64.decode('<?php echo base64_encode($gks_pos_client_send_fileto_url);?>');
var from_php_pway_id=<?php echo intval($row_pos['def_tropos_pliromis']);?>;
var from_php_pos_user_can_change_prices=<?php echo (intval($row_pos['pos_user_can_change_prices'])==1 ? 'true' : 'false');?>;

var from_php_pos_installments=<?php echo intval($row_pos['pos_installments']);?>;
var from_php_pos_tip=<?php echo (intval($row_pos['pos_tip'])==1 ? 'true' : 'false');?>;
var from_php_pos_indexeddb=<?php echo (intval($row_pos['pos_indexeddb'])==1 ? 'true' : 'false');?>;
var from_php_pos_can_search_products=<?php echo (intval($row_pos['pos_can_search_products'])==1 ? 'true' : 'false');?>;
var from_php_pos_auto_click_start_at_paywith=<?php echo (intval($row_pos['pos_auto_click_start_at_paywith'])==1 ? 'true' : 'false');?>;


var from_php_perm_ret_edit=true;

var from_php_gks_erp_app_mobile=<?php echo ($from_gks_erp_app_mobile ? 1 : 0);?>;
var from_php_iderpappmobile=<?php echo $iderpappmobile;?>;
  
var from_php_gks_cache_version=<?php echo $gks_cache_version;?>;

var from_php_preferred_payment_methods = JSON.parse('<?php echo json_encode($preferred_payment_methods);?>');

var from_php_mynow_Y = <?php echo  date('Y',_time_user(time(),1)-GKS_ERP_START_VARDIA*60*60)?>;
var from_php_mynow_m = <?php echo (date('m',_time_user(time(),1)-GKS_ERP_START_VARDIA*60*60) - 1)?>;
var from_php_mynow_d = <?php echo  date('d',_time_user(time(),1)-GKS_ERP_START_VARDIA*60*60)?>;

var from_php_print_x_days_back=<?php echo $print_x_days_back;?>;
var from_php_print_x_days_min='<?php echo showDate($today_vardia_time-$print_x_days_back*24*60*60, 'Y/m/d', 1);?>';
var from_php_print_x_days_max='<?php echo showDate($today_vardia_time, 'Y/m/d', 1);?>';


jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>
  

  
});



</script>  



<script src="cache/<?php echo $gks_user_cache_version_prefix;?>gks_country.js"></script>
<script src="js/admin-pos-run.js?v=<?php echo $gks_cache_version;?>"></script>
<script src="js/admin-eftpos-transaction-dialog.js?v=<?php echo $gks_cache_version;?>"></script>



<?php
echo gks_from_googlemaps_scripts();

include_once('_my_footer_admin.php');


