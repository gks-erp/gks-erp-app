<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_page();



$my_page_title=gks_lang('Επισκόπηση Επαφής');
$nav_active_array=array('manage','manage_users');


db_open();
stat_record();
$id=0;if (isset($_GET['id'])) $id=intval($_GET['id']);
$perm_ret=gks_permission_user_can_action($my_wp_user_id, 'wp_users','view',$id);
if ($perm_ret['success']==false) {header('Location: /my/admin-deny.php?message='.rawurlencode($perm_ret['message'])); die();}

if ($id>0) {
  $only_numbers=array('0','1','2','3','4','5','6','7','8','9');
  $users_communication=array(
    'email' => array(),
    'phone' => array(),
    'url' => array(),
  );
  $sql_comm="select comm_type,comm_value
  from gks_users_communication
  where user_id=".$id." and comm_value<>'' and comm_type<>''";
  $result_comm = $db_link->query($sql_comm);        
  if (!$result_comm) debug_mail(false,'error sql',$sql_comm);
  if (!$result_comm) die('sql error');
  while ($row_comm = $result_comm->fetch_assoc()) {
    switch ($row_comm['comm_type']) {   
      case 'email':  
        $users_communication['email'][]="'".$db_link->escape_string($row_comm['comm_value'])."'";     
        break;
      case 'phone':
        $value_clean='';
        $value=$row_comm['comm_value'];
        for($i=0; $i < strlen($value); $i++) {
          if (in_array($value[$i],$only_numbers))  $value_clean.=$value[$i];
        }
        if ($value_clean!='') {
          $users_communication['phone'][]=
          "(myto like '%".$db_link->escape_string($value_clean)."' or 
            myfrom like '%".$db_link->escape_string($value_clean)."')";  
        }
        break;
      case 'url':
        $users_communication['url'][]="'".$db_link->escape_string($row_comm['comm_value'])."'"; 
        break;
      
      default:
    }
  }
  //print '<pre>';print_r($users_communication);die();
  
  
  
  $user_companys=gks_get_companys_list();
  
  $filters = array();
  
  $filters[] = array(
    'name' => 'fobj',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Αντικείμενο'),
    'has_custom_default' => '-1',
    'multiselect' => true,
    'field'  => "['%V%']",
    'mywherepos'=>1,
    'vals' => array(
      array('value' => 1, 'text' => gks_lang('Επαφή'),                'sql' => 'wp_users'),
      array('value' => 2, 'text' => gks_lang('Ομάδα επαφών'),         'sql' => 'users_groups'),
      array('value' => 3, 'text' => gks_lang('email'),                'sql' => 'email'),
      array('value' => 4, 'text' => gks_lang('SMS'),                  'sql' => 'sms'),
      array('value' => 5, 'text' => gks_lang('Viber'),                'sql' => 'viber'),
      array('value' => 6, 'text' => gks_lang('Εταιρεία'),             'sql' => 'company_users'),
      array('value' => 7, 'text' => gks_lang('Κράτηση ξενοδοχείου'),  'sql' => 'reservation'),
      array('value' => 8, 'text' => gks_lang('Κράτηση transfer'),     'sql' => 'transfer_reservation'),
      array('value' => 9, 'text' => gks_lang('Ευκαιρία'),             'sql' => 'crm_leads'),
      array('value' => 10,'text' => gks_lang('Εργασία'),              'sql' => 'crm_tasks'),
      array('value' => 11,'text' => gks_lang('Συσκευή'),              'sql' => 'crm_machine'),
      array('value' => 12,'text' => gks_lang('Παρραγγελία'),          'sql' => 'order'),
      array('value' => 13,'text' => gks_lang('Παραστατικό'),          'sql' => 'acc_inv'),
      array('value' => 14,'text' => gks_lang('Πληρωμή'),              'sql' => 'acc_pay'),
      array('value' => 15,'text' => gks_lang('Πάγιο'),                'sql' => 'assets_moves'),
     

      
      
      
    ),
  );  
  
  
  $filters[] = array(
    'name' => 'fstate',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Κατάσταση'),
    'has_custom_default' => '-1',
    'multiselect' => true,
    'field'  => "doc_state = '%V%'",
    'vals' => array(
      array('value' => 1111, 'text' => getOrderStateDescr('005prodraft'),           'sql' => "doc_state='005prodraft'"),
      array('value' => 111, 'text' => getAccInvStateDescr('010draft'),              'sql' => "doc_state='010draft'"),
      array('value' => 1101, 'text' => getOrderStateDescr('020pending'),            'sql' => "doc_state='020pending'"),
      array('value' => 1114, 'text' => getOrderStateDescr('025offer'),              'sql' => "doc_state='025offer'"),
      array('value' => 1102, 'text' => getOrderStateDescr('030forcancellation'),    'sql' => "doc_state='030forcancellation'"),
      array('value' => 100, 'text' => getAccInvStateDescr('040cancelled'),          'sql' => "doc_state='040cancelled'"),
      array('value' => 101, 'text' => getAccInvStateDescr('050proinvoice'),         'sql' => "doc_state='050proinvoice'"),
      array('value' => 1104, 'text' => getOrderStateDescr('050rejected'),           'sql' => "doc_state='050rejected'"),
      array('value' => 1112, 'text' => getOrderStateDescr('055wait_payment'),       'sql' => "doc_state='055wait_payment'"),
      array('value' => 1113, 'text' => getHotelReservationStatusDescr('070wait_payment') .gks_lang(' (Κρατήσεις)'),       'sql' => "doc_state='070wait_payment'"),
      array('value' => 1105, 'text' => getOrderStateDescr('060registered'),         'sql' => "doc_state='060registered'"),
      array('value' => 1106, 'text' => getOrderStateDescr('070inproduction'),       'sql' => "doc_state='070inproduction'"),
      array('value' => 1107, 'text' => getOrderStateDescr('080failed'),             'sql' => "doc_state='080failed'"),
      array('value' => 102, 'text' => getAccInvStateDescr('080listing'),            'sql' => "doc_state='080listing' or doc_state='080confirm'"),
      array('value' => 103, 'text' => getAccInvStateDescr('090ekdosi'),             'sql' => "doc_state='090ekdosi'"),
      array('value' => 1108, 'text' => getOrderStateDescr('090indelivery'),         'sql' => "doc_state='090indelivery'"),
      array('value' => 1110, 'text' => getOrderStateDescr('095execute'),            'sql' => "doc_state='095execute'"),
      array('value' => 109, 'text' => getAccInvStateDescr('100payment'),            'sql' => "(doc_state='100payment' or doc_state='110payment')"),
      array('value' => 1109, 'text' => getOrderStateDescr('100completed'),          'sql' => "doc_state='100completed'"),
      
      
      
    ),
  );
  
  
  
  $filters[] = array(
    'name' => 'fdoc_date',
    'class' => 'filterselectbox ui-state-default ui-corner-all',
    'style' => '',
    'title' => gks_lang('Ημερομηνία'),
    'has_custom_date' => true,
    'field' => 'doc_date', 
    'has_custom_default' => 1,
    //		'mywherepos'=>1,
    'vals' => gks_filter_date_vals(['field'=>'doc_date','future'=>false,'today'=>$today, 'today_vardia'=>$today_vardia]),
  );
  
  if (count($user_companys)>1) {
    $vals=array();
    foreach ($user_companys as $key=>$value) {
      $vals[]=array('value' => $key, 'text' => $value['descr'],      'sql' => "company_id=".$value['id_company']." and company_sub_id=".$value['id_company_sub']);
    } 
    $filters[] = array(
      'name' => 'fcompany_id',
      'class' => 'filterselectbox',
      'style' => '',
      'title' => gks_lang('Εταιρεία'),
      'has_custom_default' => -1,
      'multiselect' => true,
      'field'  => "1=1",
      'vals' => $vals,
    );  
  }
  
  $filters[] = array(
    'name' => 'fjournal',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Ημερολόγιο'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "doc_journal_id = %V%",
    'vals' => array(
        //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_acc_inv.user_id=0"),
        //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_acc_inv.user_id<>0"),
    ),
    'sql' => "SELECT id_acc_journal AS id, acc_journal_descr AS descr
    FROM gks_acc_journal 
    ORDER BY sortorder,acc_journal_descr;",    
  );
  
  
  $filters[] = array(
    'name' => 'fseira',
    'class' => 'filterselectbox',
    'style' => '',
    'title' => gks_lang('Σειρά'),
    'has_custom_default' => -1,
    'multiselect' => true,
    'field'  => "doc_seira_id = %V%",
    'vals' => array(
        //array('value' => -100, 'text' => gks_lang('Δεν έχει ορισθεί'),      'sql' => "gks_acc_inv.user_id=0"),
        //array('value' => -101, 'text' => gks_lang('Έχει ορισθεί'),          'sql' => "gks_acc_inv.user_id<>0"),
    ),
    'sql' => "SELECT id_acc_seira AS id, seira_descr AS descr
  FROM gks_acc_seires 
  ORDER BY sortorder,seira_descr;",    
  );
  
  
  $sortable = array(
    array('name' => 'soid', 'field' => 'doc_id'),
    array('name' => 'soobj_type', 'field' => 'obj_type'),
    array('name' => 'sostate', 'field' => 'doc_state'),
    array('name' => 'sood', 'field' => 'doc_date'),
    array('name' => 'socompany', 'field' => 'company_title, company_sub_title'),
    array('name' => 'sojournal', 'field' => 'acc_journal_descr'),
    array('name' => 'soseira', 'field' => 'seira_code'),
    array('name' => 'sonumber', 'field' => 'doc_number'),
    array('name' => 'soprice', 'field' => 'gks_price_netfpa'),
    array('name' => 'sopa', 'field' => 'payment_acquirer_name'),
    
  );
  
  $has_sort=false;
  foreach ($_GET as $key1 => $value1) {
    foreach ($sortable as $value2) {
      if ($key1==$value2['name']) {
        $has_sort=true;
        break;
      }
    }
    if ($has_sort) break;
  }
  if ($has_sort==false) $_GET['sood']='desc';
  //echo '<pre>';print_r($_GET);die();
  
  $search_fields = array(
  'company_title',
  'company_sub_title',
  'acc_journal_descr',
  'seira_code',
  'payment_acquirer_name',
  );
  
  
  
  $filter = array('html' => '', 'sql' => '', 'url' => '');
  $search_string_value = (isset($_GET['search_string']) ? $_GET['search_string'] : '');
  makeFilters($filters, $filter, $_GET,true,true,$search_string_value);
  
  
  
  
  $search_where = make_search_where($search_string_value,$search_fields);
  $search_where = !empty($search_where) ? ' AND '.$search_where : '';
  //echo $search_where;
  //die();
  
  //$where = !empty($filter['sql']) ? ' AND '.$filter['sql'] : '';
  //$where1 = isset($filter['sql1']) ? ' AND '.$filter['sql1'] : '';
  
  $where = !empty($filter['sql']) ? ' AND '.$filter['sql'] : '';
  $where1 = isset($filter['sql1']) ? ' AND '.$filter['sql1'] : '';
  
  $where1=str_replace(' AND  (', '', $where1);
  $where1=str_replace(') ', '', $where1);
  $where1=trim($where1);
  $fobj_array=array();
  if ($where1!='') $fobj_array=explode(' or ',$where1);
  
  
  $sorted = array('sql' => '', 'url' => '');
  
  makeSortable($sortable, $sorted, $_GET);
  											
  
  
  $rows_per_page = $_gks_session['gks']['rows_per_page'];
  $page = isset($_GET['page']) ? (int) $_GET['page'] : 0;
  
  $showFrom = $page * $rows_per_page;
  $showTo = $showFrom + $rows_per_page;
  
  
  
  
  $sql = "SELECT SQL_CALC_FOUND_ROWS alltables.* 
  from (
  ";
  
  $sql_parts=array();
  
  if (count($fobj_array) == 0 or in_array('wp_users',$fobj_array)) {
    $sql_parts[]="
    select
    'wp_users' as obj_type,
    '' as obj_folder,
    ID as doc_id,
    '' as doc_state,
    user_registered as doc_date, 
    '' as company_title,
    '' as company_sub_title,
    '' as acc_journal_descr,
    '' as seira_code,
    0 as doc_number,
    0 as gks_price_net,
    0 as gks_price_netfpa,
    0 as totalWithheldAmount,    
    0 as affect_balance_calc,
    '' as payment_acquirer_name,
    0 as company_id,
    0 as company_sub_id,
    0 as doc_journal_id,
    0 as doc_seira_id,
    '' as mytext1,
    '' as mytext2,
    '' as mytext3,
    '' as mytext4,
    0 as myint1,
    0 as myint2,
    0 as myint3
    from  ".GKS_WP_TABLE_PREFIX."users
    where ".GKS_WP_TABLE_PREFIX."users.ID=".$id;
  }
    
 
  if (count($fobj_array) == 0 or in_array('users_groups',$fobj_array)) {
    $sql_parts[]="
    select
    'users_groups' as obj_type,
    '' as obj_folder,
    group_id as doc_id,
    '' as doc_state,
    action_date as doc_date, 
    '' as company_title,
    '' as company_sub_title,
    '' as acc_journal_descr,
    '' as seira_code,
    0 as doc_number,
    0 as gks_price_net,
    0 as gks_price_netfpa,
    0 as totalWithheldAmount,    
    0 as affect_balance_calc,
    '' as payment_acquirer_name,
    0 as company_id,
    0 as company_sub_id,
    0 as doc_journal_id,
    0 as doc_seira_id,
    action_type as mytext1,
    group_title as mytext2,
    '' as mytext3,
    '' as mytext4,
    is_omadarxis as myint1,
    0 as myint2,
    0 as myint3
    
    from  gks_log_users_groups_users
    LEFT JOIN gks_users_groups ON gks_log_users_groups_users.group_id = gks_users_groups.id_users_group
    where gks_log_users_groups_users.user_id=".$id."
    ";
  }
    
  if ((count($fobj_array) == 0 or in_array('email',$fobj_array)) and count($users_communication['email'])>0) {
    $sql_parts[]="     
    select 
    'email' as obj_type,
    '' as obj_folder,
    id as doc_id,
    myret as doc_state,
    date_add as doc_date, 
    '' as company_title,
    '' as company_sub_title,
    '' as acc_journal_descr,
    '' as seira_code,
    0 as doc_number,
    0 as gks_price_net,
    0 as gks_price_netfpa,
    0 as totalWithheldAmount,    
    0 as affect_balance_calc,
    '' as payment_acquirer_name,
    0 as company_id,
    0 as company_sub_id,
    0 as doc_journal_id,
    0 as doc_seira_id,
    subject as mytext1,
    myto as mytext2,
    '' as mytext3,
    '' as mytext4,
    myret as myint1,
    0 as myint2,
    0 as myint3  
    from gks_email
    where myto in (".implode(',',$users_communication['email']).")";
  }
  
  if ((count($fobj_array) == 0 or in_array('sms',$fobj_array)) and count($users_communication['phone'])>0) {
    $sql_parts[]="
    select 
    'sms' as obj_type,
    sms_folder as obj_folder,
    id as doc_id,
    myret as doc_state,
    date_add as doc_date, 
    '' as company_title,
    '' as company_sub_title,
    '' as acc_journal_descr,
    '' as seira_code,
    0 as doc_number,
    0 as gks_price_net,
    0 as gks_price_netfpa,
    0 as totalWithheldAmount,    
    0 as affect_balance_calc,
    '' as payment_acquirer_name,
    0 as company_id,
    0 as company_sub_id,
    0 as doc_journal_id,
    0 as doc_seira_id,
    Message_post as mytext1,
    myto as mytext2,
    status_name as mytext3,
    myfrom as mytext4,
    myret as myint1,
    status as myint2,
    0 as myint3 
    from gks_sms
    where (".implode(' or ',$users_communication['phone']).")";
  }  
  //echo '<pre>';echo $sql;die();
  
  if (count($fobj_array) == 0 or in_array('viber',$fobj_array)) {
    $sql_parts[]="
    select 
    'viber' as obj_type,
    '' as obj_folder,
    id_viber_msgs as doc_id,
    '' as doc_state,
    mydate as doc_date, 
    '' as company_title,
    '' as company_sub_title,
    '' as acc_journal_descr,
    '' as seira_code,
    0 as doc_number,
    0 as gks_price_net,
    0 as gks_price_netfpa,
    0 as totalWithheldAmount,    
    0 as affect_balance_calc,
    '' as payment_acquirer_name,
    0 as company_id,
    0 as company_sub_id,
    0 as doc_journal_id,
    0 as doc_seira_id,
    sender_id as mytext1,
    receiver_id as mytext2,
    message as mytext3,
    '' as mytext4,
    case WHEN delivered is null then 0 else 1 END as myint1,
    case WHEN seen is null then 0 else 1 END as myint2,
    0 as myint3  
    from gks_viber_msgs
    LEFT JOIN gks_viber_cmds ON gks_viber_msgs.action_cmd_part1 = gks_viber_cmds.viber_cmd
    where user_id=".$id;
    
  }   
  //echo '<pre>';echo $sql;die();
  
  if (count($fobj_array) == 0 or in_array('company_users',$fobj_array)) {
    $sql_parts[]="
    select 
    'company_users' as obj_type,
    '' as obj_folder,
    gks_log_company_users.company_id as doc_id,
    '' as doc_state,
    action_date as doc_date, 
    company_title,
    company_sub_title,
    '' as acc_journal_descr,
    '' as seira_code,
    0 as doc_number,
    0 as gks_price_net,
    0 as gks_price_netfpa,
    0 as totalWithheldAmount,    
    0 as affect_balance_calc,
    '' as payment_acquirer_name,
    gks_log_company_users.company_id,
    gks_log_company_users.company_sub_id,
    0 as doc_journal_id,
    0 as doc_seira_id,
    action_type as mytext1,
    '' as mytext2,
    '' as mytext3,
    '' as mytext4,
    0 as myint1,
    0 as myint2,
    0 as myint3  
    from (gks_log_company_users
    LEFT JOIN gks_company ON gks_log_company_users.company_id = gks_company.id_company) 
    LEFT JOIN gks_company_subs ON gks_log_company_users.company_sub_id = gks_company_subs.id_company_sub
    where gks_log_company_users.user_id=".$id;
    
  }
  //echo '<pre>';echo $sql;die();

    
  if (count($fobj_array) == 0 or in_array('acc_inv',$fobj_array)) {
    $sql_parts[]="
    SELECT
    'acc_inv' as obj_type,
    '' as obj_folder, 
    gks_acc_inv.id_acc_inv as doc_id,
    gks_acc_inv.inv_state as doc_state,
    gks_acc_inv.inv_date as doc_date,
    gks_company.company_title,
    gks_company_subs.company_sub_title,
    gks_acc_journal.acc_journal_descr,
    gks_acc_seires.seira_code,
    gks_acc_inv.inv_acc_number_int as doc_number,
    gks_acc_inv.gks_price_net,
    gks_acc_inv.gks_price_netfpa,
    gks_acc_inv.totalWithheldAmount,
    CASE
      WHEN (inv_state='080listing' or inv_state='090ekdosi' or inv_state='100payment') and affect_balance=1
        THEN affect_balance_pros * affect_balance_poso
      ELSE 0
    END as affect_balance_calc,
    gks_payment_acquirers.payment_acquirer_name,
    gks_acc_inv.company_id,
    gks_acc_inv.company_sub_id,
    gks_acc_inv.inv_acc_journal_id as doc_journal_id,
    gks_acc_inv.inv_acc_seira_id as doc_seira_id,
    '' as mytext1,
    '' as mytext2,
    '' as mytext3,
    '' as mytext4,
    0 as myint1,
    0 as myint2,
    0 as myint3
    FROM ((((((gks_acc_inv
    LEFT JOIN gks_company on gks_acc_inv.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_acc_inv.company_sub_id = gks_company_subs.id_company_sub)
    LEFT JOIN gks_acc_journal ON gks_acc_inv.inv_acc_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
    LEFT JOIN gks_acc_seires ON gks_acc_inv.inv_acc_seira_id = gks_acc_seires.id_acc_seira)
    LEFT JOIN gks_payment_acquirers ON gks_acc_inv.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer
    where gks_acc_inv.user_id=".$id;
    
  }
    
  if (count($fobj_array) == 0 or in_array('acc_pay',$fobj_array)) {
    $sql_parts[]="
    select
    'acc_pay' as obj_type, 
    '' as obj_folder,
    gks_acc_pay.id_acc_pay as doc_id,
    gks_acc_pay.pay_state as doc_state,
    gks_acc_pay.pay_date as doc_date,
    gks_company.company_title,
    gks_company_subs.company_sub_title,
    gks_acc_journal.acc_journal_descr,
    gks_acc_seires.seira_code,
    gks_acc_pay.pay_acc_number_int as doc_number,
    gks_price_total as gks_price_net,
    gks_price_total as gks_price_netfpa,
    0 as totalWithheldAmount,
    CASE
      WHEN (pay_state='080listing' or pay_state='090ekdosi' or pay_state='100payment') and affect_balance=1
        THEN affect_balance_pros * affect_balance_poso
      ELSE 0
    END as affect_balance_calc,
    '' as payment_acquirer_name,
    gks_acc_pay.company_id,
    gks_acc_pay.company_sub_id,
    gks_acc_pay.pay_acc_journal_id as doc_journal_id,
    gks_acc_pay.pay_acc_seira_id as doc_seira_id,
    '' as mytext1,
    '' as mytext2,
    '' as mytext3,
    '' as mytext4,
    0 as myint1,
    0 as myint2,
    0 as myint3
    FROM (((((gks_acc_pay
    LEFT JOIN gks_company on gks_acc_pay.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_acc_pay.company_sub_id = gks_company_subs.id_company_sub)
    LEFT JOIN gks_acc_journal ON gks_acc_pay.pay_acc_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
    LEFT JOIN gks_acc_seires ON gks_acc_pay.pay_acc_seira_id = gks_acc_seires.id_acc_seira
    where gks_acc_pay.user_id=".$id;
  }
  
  if (count($fobj_array) == 0 or in_array('order',$fobj_array)) {
    $sql_parts[]="
    SELECT
    'order' as obj_type, 
    '' as obj_folder,
    gks_orders.id_order as doc_id,
    gks_orders.order_state as doc_state,
    gks_orders.order_date as doc_date,
    gks_company.company_title,
    gks_company_subs.company_sub_title,
    gks_acc_journal.acc_journal_descr,
    gks_acc_seires.seira_code,
    gks_orders.order_number_int as doc_number,
    gks_orders.gks_price_net,
    gks_orders.gks_price_netfpa,
    0 as totalWithheldAmount,
  
    CASE
      WHEN (order_state='070wait_payment' or order_state='080confirm' or 
           order_state='100completed' or order_state='110payment') and affect_balance=1
        THEN affect_balance_pros * affect_balance_poso
      ELSE 0
    END as affect_balance_calc,  
    gks_payment_acquirers.payment_acquirer_name,
    gks_orders.company_id,
    gks_orders.company_sub_id,
    gks_orders.order_journal_id as doc_journal_id,
    gks_orders.order_seira_id as doc_seira_id,
    '' as mytext1,
    '' as mytext2,
    '' as mytext3,
    '' as mytext4,
    0 as myint1,
    0 as myint2,
    0 as myint3
    FROM ((((((gks_orders
    LEFT JOIN gks_company on gks_orders.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_orders.company_sub_id = gks_company_subs.id_company_sub)
    LEFT JOIN gks_acc_journal ON gks_orders.order_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
    LEFT JOIN gks_acc_seires ON gks_orders.order_seira_id = gks_acc_seires.id_acc_seira)
    LEFT JOIN gks_payment_acquirers ON gks_orders.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer
    where gks_orders.user_id=".$id;
  }
    
    
  if (count($fobj_array) == 0 or in_array('reservation',$fobj_array)) {
    $sql_parts[]="
    SELECT
    'reservation' as obj_type, 
    '' as obj_folder,
    gks_hotel_reservation.id_hotel_reservation as doc_id,
    gks_hotel_reservation.reservation_status as doc_state,
    gks_hotel_reservation.reservation_date as doc_date,
    gks_company.company_title,
    gks_company_subs.company_sub_title,
    gks_acc_journal.acc_journal_descr,
    gks_acc_seires.seira_code,
    gks_hotel_reservation.reservation_number_int as doc_number,
    gks_hotel_reservation.gks_price_net,
    gks_hotel_reservation.gks_price_netfpa,
    0 as totalWithheldAmount,
  
    CASE
      WHEN (reservation_status='060registered' or reservation_status='070inproduction' or 
           reservation_status='090indelivery' or reservation_status='095execute' or reservation_status='100completed' or reservation_status='110payment') and affect_balance=1
        THEN affect_balance_pros * affect_balance_poso
      ELSE 0
    END as affect_balance_calc,  
    gks_payment_acquirers.payment_acquirer_name,
    gks_hotel.company_id,
    gks_hotel.company_sub_id,
    gks_hotel_reservation.reservation_journal_id as doc_journal_id,
    gks_hotel_reservation.reservation_seira_id as doc_seira_id,
    '' as mytext1,
    '' as mytext2,
    '' as mytext3,
    '' as mytext4,
    0 as myint1,
    0 as myint2,
    0 as myint3
    FROM (((((((gks_hotel_reservation
    LEFT JOIN gks_hotel ON gks_hotel_reservation.hotel_id = gks_hotel.id_hotel)
    LEFT JOIN gks_company on gks_hotel.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_hotel.company_sub_id = gks_company_subs.id_company_sub)
    LEFT JOIN gks_acc_journal ON gks_hotel_reservation.reservation_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
    LEFT JOIN gks_acc_seires ON gks_hotel_reservation.reservation_seira_id = gks_acc_seires.id_acc_seira)
    LEFT JOIN gks_payment_acquirers ON gks_hotel_reservation.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer
    where gks_hotel_reservation.user_id=".$id;
  }


  if (count($fobj_array) == 0 or in_array('transfer_reservation',$fobj_array)) {
    $sql_parts[]="  
    SELECT
    'transfer_reservation' as obj_type, 
    '' as obj_folder,
    gks_transfer_reservation.id_transfer_reservation as doc_id,
    gks_transfer_reservation.transfer_reservation_status as doc_state,
    gks_transfer_reservation.transfer_reservation_date as doc_date,
    gks_company.company_title,
    gks_company_subs.company_sub_title,
    gks_acc_journal.acc_journal_descr,
    gks_acc_seires.seira_code,
    gks_transfer_reservation.transfer_reservation_number_int as doc_number,
    gks_transfer_reservation.gks_price_net,
    gks_transfer_reservation.gks_price_netfpa,
    0 as totalWithheldAmount,
  
    CASE
      WHEN (transfer_reservation_status='060registered' or transfer_reservation_status='070inproduction' or 
           transfer_reservation_status='090indelivery' or transfer_reservation_status='095execute' or transfer_reservation_status='100completed' or transfer_reservation_status='110payment') and affect_balance=1
        THEN affect_balance_pros * affect_balance_poso
      ELSE 0
    END as affect_balance_calc,  
    gks_payment_acquirers.payment_acquirer_name,
    gks_transfer.company_id,
    gks_transfer.company_sub_id,
    gks_transfer_reservation.transfer_reservation_journal_id as doc_journal_id,
    gks_transfer_reservation.transfer_reservation_seira_id as doc_seira_id,
    '' as mytext1,
    '' as mytext2,
    '' as mytext3,
    '' as mytext4,
    0 as myint1,
    0 as myint2,
    0 as myint3
    FROM (((((((gks_transfer_reservation
    LEFT JOIN gks_transfer ON gks_transfer_reservation.transfer_id = gks_transfer.id_transfer)
    LEFT JOIN gks_company on gks_transfer.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_transfer.company_sub_id = gks_company_subs.id_company_sub)
    LEFT JOIN gks_acc_journal ON gks_transfer_reservation.transfer_reservation_journal_id = gks_acc_journal.id_acc_journal) 
    LEFT JOIN gks_acc_eidi_parastatikon ON gks_acc_journal.acc_eidos_parastatikou_id = gks_acc_eidi_parastatikon.id_acc_eidos_parastatikou)
    LEFT JOIN gks_acc_eidi_parastatikon_types ON gks_acc_eidi_parastatikon.eidos_parastatikou_type_id = gks_acc_eidi_parastatikon_types.id_acc_eidi_parastatikon_type)
    LEFT JOIN gks_acc_seires ON gks_transfer_reservation.transfer_reservation_seira_id = gks_acc_seires.id_acc_seira)
    LEFT JOIN gks_payment_acquirers ON gks_transfer_reservation.tropos_pliromis = gks_payment_acquirers.id_payment_acquirer
    where gks_transfer_reservation.user_id=".$id;
  }
        
        
  if (count($fobj_array) == 0 or in_array('crm_leads',$fobj_array)) {
    $sql_parts[]="  
    SELECT
    'crm_leads' as obj_type, 
    '' as obj_folder,
    id_crm_lead as doc_id,
    lead_status_descr as doc_state,
    lead_date as doc_date,
    gks_company.company_title,
    gks_company_subs.company_sub_title,
    '' as acc_journal_descr,
    '' as seira_code,
    0  as doc_number,
    0 as gks_price_net,
    esoda as gks_price_netfpa,
    0 as totalWithheldAmount,
  
    esoda as affect_balance_calc,  
    '' as payment_acquirer_name,
    gks_crm_leads.company_id,
    gks_crm_leads.company_sub_id,
    0 as doc_journal_id,
    0 as doc_seira_id,
    subject as mytext1,
    '' as mytext2,
    '' as mytext3,
    '' as mytext4,
    lead_status_id as myint1,
    0 as myint2,
    0 as myint3
    FROM ((gks_crm_leads
    LEFT JOIN gks_crm_leads_status ON gks_crm_leads.lead_status_id = gks_crm_leads_status.id_crm_lead_status)
    LEFT JOIN gks_company on gks_crm_leads.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_crm_leads.company_sub_id = gks_company_subs.id_company_sub
    where gks_crm_leads.user_id=".$id;
  }        
        
  if (count($fobj_array) == 0 or in_array('crm_tasks',$fobj_array)) {
    $sql_parts[]="  
    SELECT
    'crm_tasks' as obj_type, 
    '' as obj_folder,
    id_crm_task as doc_id,
    task_status_descr as doc_state,
    task_planned_date_from as doc_date,
    gks_company.company_title,
    gks_company_subs.company_sub_title,
    '' as acc_journal_descr,
    '' as seira_code,
    0  as doc_number,
    0 as gks_price_net,
    esoda as gks_price_netfpa,
    0 as totalWithheldAmount,
  
    esoda as affect_balance_calc,  
    '' as payment_acquirer_name,
    gks_crm_tasks.company_id,
    gks_crm_tasks.company_sub_id,
    0 as doc_journal_id,
    0 as doc_seira_id,
    subject as mytext1,
    '' as mytext2,
    '' as mytext3,
    '' as mytext4,
    task_status_id as myint1,
    0 as myint2,
    0 as myint3
    FROM ((gks_crm_tasks
    LEFT JOIN gks_crm_tasks_status ON gks_crm_tasks.task_status_id = gks_crm_tasks_status.id_crm_task_status)
    LEFT JOIN gks_company on gks_crm_tasks.company_id = gks_company.id_company)
    LEFT JOIN gks_company_subs on gks_crm_tasks.company_sub_id = gks_company_subs.id_company_sub
    where gks_crm_tasks.user_id=".$id;
  }        
        
  if (count($fobj_array) == 0 or in_array('crm_machine',$fobj_array)) {
    $sql_parts[]="  
    SELECT
    'crm_machine' as obj_type, 
    '' as obj_folder,
    id_crm_machine as doc_id,
    '' as doc_state,
    mydate_add as doc_date,
    '' as company_title,
    '' as company_sub_title,
    '' as acc_journal_descr,
    '' as seira_code,
    0  as doc_number,
    0 as gks_price_net,
    0 as gks_price_netfpa,
    0 as totalWithheldAmount,
  
    0 as affect_balance_calc,  
    '' as payment_acquirer_name,
    0 as company_id,
    0 as company_sub_id,
    0 as doc_journal_id,
    0 as doc_seira_id,
    crm_machine_name as mytext1,
    '' as mytext2,
    '' as mytext3,
    '' as mytext4,
    0 as myint1,
    0 as myint2,
    0 as myint3
    FROM gks_crm_machine
    where gks_crm_machine.crm_machine_user_id=".$id;
  }         
        

  if (count($fobj_array) == 0 or in_array('assets_moves',$fobj_array)) {
    $sql_parts[]="  
    SELECT
    'assets_moves' as obj_type, 
    '' as obj_folder,
    asset_id as doc_id,
    '' as doc_state,
    gks_assets_moves.mydate as doc_date,
    gks_company.company_title,
    '' as company_sub_title,
    '' as acc_journal_descr,
    '' as seira_code,
    0  as doc_number,
    0 as gks_price_net,
    0 as gks_price_netfpa,
    0 as totalWithheldAmount,
  
    0 as affect_balance_calc,  
    '' as payment_acquirer_name,
    gks_assets_moves.company_id,
    0 as company_sub_id,
    0 as doc_journal_id,
    0 as doc_seira_id,
    asset_title as mytext1,
    warehouse_name as mytext2,
    '' as mytext3,
    '' as mytext4,
    id_asset as myint1,
    warehouse_id as myint2,
    asset_disable as myint3
    FROM ((gks_assets_moves
    LEFT JOIN gks_assets ON gks_assets_moves.asset_id = gks_assets.id_asset) 
    LEFT JOIN gks_warehouses ON gks_assets_moves.warehouse_id = gks_warehouses.id_warehouse) 
    LEFT JOIN gks_company ON gks_assets_moves.company_id = gks_company.id_company
    where gks_assets_moves.user_id=".$id;
  }
  
          
  $sql.=implode(' 
  union
  ',
  $sql_parts);
  
  
  $sql.="    
  ) as alltables 
  
  
  where 1=1 ".$where . $search_where;
  
  
  if (empty($sorted['sql'])) {
  	$sql .= " ORDER BY doc_date desc, doc_id desc";
  } else {
  	$sql .= " ORDER BY " . $sorted['sql'];
  }
  $sql .= " LIMIT ". $showFrom .", " . $rows_per_page;
  
  
  //echo '<pre>';print_r($fobj_array);echo "\n";echo $sql; die();   
  $result = $db_link->query($sql);        
  if (!$result) debug_mail(false,'error sql',$sql);
  if (!$result) die('sql error');
  
  $sql_numrows = "SELECT FOUND_ROWS() AS `found_rows`;";
  $res_numrows = $db_link->query($sql_numrows);
  $row_numrows = $res_numrows->fetch_assoc();
  $total_records = $row_numrows['found_rows'];
  
  $pages = ceil($total_records / $rows_per_page) - 1;
  
  $paging = array('records' => '', 'total' => '', 'pages' => '');
  $url = $_SERVER['SCRIPT_NAME'].'?id='.$id;
  $params='';
  if (isset($filter['url']) && $filter['url']!='') $params.='&'.$filter['url'];
  if (isset($sorted['url']) && $sorted['url']!='') $params.='&'.$sorted['url'];
  if (isset($_GET['search_string']) && $_GET['search_string']!='') $params.='&search_string='.urlencode($_GET['search_string']);
  
  
  
  
  pagination($pages, $page, $total_records, $url, $paging, false, $params);
      
  $sortable_url='?id='.$id;
  if (isset($filter['url']) && $filter['url']!='') $sortable_url.='&'.$filter['url'];
  if (isset($page) && $page>0) $sortable_url.='&page='.$page;
  if (isset($_GET['search_string']) && $_GET['search_string']!='') $sortable_url.='&search_string='.urlencode($_GET['search_string']);
  
  
  
  $sortfields = explode("=", $sorted['url']);
  if (count($sortfields) < 2) {
      $sortfields[0] = '';
      $sortfields[1] = '';
  }
}


include_once('_my_header_admin.php');
?>
<style>
.splittd {
  border-left: 2px solid gray !important;
}

@media print {
  .gks_card_expand_icon {display: none;} 
  .gks_card_expand {border-width: 0px;}

}


</style>

<div class="container-fluid gksitemheader">
  <div class="row align-items-center">
    <div class="col-sm-12" style="text-align:center">
      <h1><?php echo $my_page_title;?></h1>
    </div>
  </div>
</div>






<?php if ($id>0) {
  $sql_one="SELECT ".GKS_WP_TABLE_PREFIX."users.ID, ".GKS_WP_TABLE_PREFIX."users.gks_nickname, 
  ".GKS_WP_TABLE_PREFIX."users.gks_balance, ".GKS_WP_TABLE_PREFIX."users.gks_wsl_current_user_image, 
  gks_users.eponimia, gks_users.title, gks_users.afm, gks_users.doy,
  myfirst_name,mylast_name
  FROM ((".GKS_WP_TABLE_PREFIX."users LEFT JOIN gks_users ON ".GKS_WP_TABLE_PREFIX."users.ID = gks_users.user_id)
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
  
  where ".GKS_WP_TABLE_PREFIX."users.ID=".$id;
  $result_one = $db_link->query($sql_one);        
  if (!$result_one) debug_mail(false,'error sql',$sql_one);
  if (!$result_one) die('sql error');
  $gks_nickname='';
  $curr_balance=false;
  $eponimia='';
  $title='';
  $afm='';
  $doy='';
  $myfirst_name='';
  $mylast_name='';
  $photo_html='';
  if ($result_one->num_rows==1) {
    $row_one = $result_one->fetch_assoc();
    $gks_nickname=trim_gks($row_one['gks_nickname']);
    $curr_balance= floatval($row_one['gks_balance']);
    $eponimia=trim_gks($row_one['eponimia']);
    $title=trim_gks($row_one['title']);
    $afm=trim_gks($row_one['afm']);
    $doy=trim_gks($row_one['doy']);
    $myfirst_name=trim_gks($row_one['myfirst_name']);
    $mylast_name=trim_gks($row_one['mylast_name']);
    
    $myimgurl=trim_gks($row_one['gks_wsl_current_user_image'].'');
    if ($myimgurl != '') {
      $mydir = dirname($myimgurl);
      if (endwith($mydir,'/thumbnail')) {
        $photo_url=substr($mydir,0, strlen($mydir)-9).mb_basename($myimgurl);
      } else {
        $photo_url=$myimgurl;
      }
      $photo_html='<a class="lightgalleryitem_user gks_photo_link" tabIndex="-1" href="'.$photo_url.'" data-sub-html="'.$gks_nickname.'"><img style="max-height: 64px;" src="'.$myimgurl.'"></a>';
    }
  }
?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="card gks_card_expand">
        <div class="card-header hide_on_print" style="text-align:center">
          <?php echo gks_lang('Επαφή');?>
        </div>
        <div class="card-body">
          <div class="row">
          
            <div class="col-sm">
              
              <?php if ($title=='' and $eponimia=='' and ($myfirst_name!='' or $mylast_name!='')) { ?>
              <div class="form-group row">
                <div class="col-sm-6 col-print-4 text-sm-right"><?php echo gks_lang('Ονοματεπώνυμο');?>:</div>
                <div class="col-sm-6 col-print-8 text-sm-left"><?php echo $mylast_name.' '.$myfirst_name;?></div>
              </div>
              
              
              <?php } ?>
              <?php if ($title!='') { ?>
              <div class="form-group row">
                <div class="col-sm-6 col-print-4 text-sm-right"><?php echo gks_lang('Τίτλος');?>:</div>
                <div class="col-sm-6 col-print-8 text-sm-left"><?php echo $title;?></div>
              </div>
              <?php } ?>
              <?php if ($eponimia!='') { ?>
              <div class="form-group row">
                <div class="col-sm-6 col-print-4 text-sm-right"><?php echo gks_lang('Επωνυμία');?>:</div>
                <div class="col-sm-6 col-print-8 text-sm-left"><?php echo $eponimia;?></div>
              </div>
              <?php } ?>
              <?php if ($afm!='') { ?>
              <div class="form-group row">
                <div class="col-sm-6 col-print-4 text-sm-right"><?php echo gks_lang('ΑΦΜ');?>:</div>
                <div class="col-sm-6 col-print-8 text-sm-left"><?php echo $afm;?></div>
              </div>
              <?php } ?>
              <?php if ($doy!='') { ?>
              <div class="form-group row">
                <div class="col-sm-6 col-print-4 text-sm-right"><?php echo gks_lang('ΔΟΥ');?>:</div>
                <div class="col-sm-6 col-print-8 text-sm-left"><?php echo $doy;?></div>
              </div>
              <?php } ?>
              
              <div class="form-group row">
                <div class="col-sm-6 col-print-4 text-sm-right"><?php echo gks_lang('Τρέχον υπόλοιπο');?>:</div>
                <div class="col-sm-6 col-print-8 text-sm-left"><?php 
                  if (is_float($curr_balance)) {
                    echo '<span style="font-weight: bold;">'.myCurrencyFormat($curr_balance).'</span>';
    
                  }
                 ?></div>
              </div>
              
            </div>

            <div class="col-sm hide_on_print">
              
              <div class="form-group row">
                <div class="col-sm-6 text-sm-right"><?php echo gks_lang('Υποκοριστικό');?>:</div>
                <div class="col-sm-6 text-sm-left" style="font-weight: bold;"><a href="admin-users-item.php?id=<?php echo $id;?>"><?php echo $gks_nickname;?></a></div>
              </div>
              <?php if ($photo_html!='') {?>
              <div class="form-group row">
                <div class="col-sm-6 text-sm-right"><span style="line-height: 64px;"><?php echo gks_lang('Φωτό');?>:</span></div>
                <div class="col-sm-6 text-sm-left" style="font-weight: bold;"><?php echo $photo_html;?></div>
              </div>
              <?php } ?>
    
            </div>

          </div>
            
                      
        </div>
      </div>
    </div>
  </div>
</div>
<?php } else {?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="alert alert-warning" role="alert" style="text-align:center">
        <?php echo gks_lang('Επιλέξτε μια επαφή');?>:
      </div>
      
      <div class="text-center">
        <input id="user" type="text" class="form-control form-control-sm myneedsave"  
        value="" 
        style="max-width:300px;display:inline;" 
        placeholder="<?php echo gks_lang('Πληκτρολογήστε τουλάχιστον 3 χαρακτήρες');?>" autocomplete="<?php echo $autocomplete_gks_disable;?>" />
        
      </div>
    </div>
  </div>
</div>

<?php } ?>

<?php if ($id>0) {?>
<table id="filters" class="filters-table" border="0" width="96%" cellspacing="0" cellpadding="5"  align="center">  
  <tr><td>
    <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>?page=<?php echo $page; ?>&<?php echo $filter['url']; ?>" method="get" name="filter-form" id="filter-form">
      <input style="display:none;" type="text" name="<?php echo $sortfields[0]; ?>" id="<?php echo $sortfields[0]; ?>" value="<?php echo $sortfields[1]; ?>" />
      <input type="hidden" name="id" value="<?php echo $id;?>"/>
      <?php echo $filter['html']; ?>
    </form>
  </td></tr>    
</table>

<?php
//echo '<pre>'.$where1.print_r($fobj_array,true).'</pre>';
?>
<?php gks_erp_app_purchase_ads_fix_970x90('afterfilters');?>
<div class="hide_on_print">
<?php mytablepages($paging, $total_records); ?>
</div>
<table class="table table-sm table-responsive11 table-striped table-bordered gkstable" border="0" cellspacing="0" cellpadding="5" align="center">
<thead>
    <tr >	
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" nowrap='nowrap' width='0%'><a href="?id=<?php echo $id;?>">#</a></th>
        <th nowrap class="table-dark hide_on_print" scope="col" style="text-align: center !important;" width="0%"><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soid', gks_lang('ID')); ?></th> 
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sood', gks_lang('Ημερομηνία')); ?></th>
        <th nowrap class="table-dark hide_on_print" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soobj_type', gks_lang('Αντικείμενο')); ?></th>        
        <th nowrap class="table-dark hide_on_print" scope="col" style="text-align: center !important;" width="40%" ><?php echo gks_lang('Επεξήγηση');?></th>        
        <th nowrap class="table-dark hide_on_print" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sostate', gks_lang('Κατάσταση')); ?></th>        
        <th nowrap class="table-dark hide_on_print" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soprice', gks_lang('Αξία')); ?></th>        

                
        <th nowrap class="table-dark" scope="col" style="text-align: left !important;" width="10%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'socompany', gks_lang('Εταιρεία')); ?></th>        
        <th nowrap class="table-dark" scope="col" style="text-align: left !important;" width="20%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sojournal', gks_lang('Ημερολόγιο')); ?></th>        
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="20%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'soseira', gks_lang('Σειρά')); ?></th>        
        <th nowrap class="table-dark" scope="col" style="text-align: center !important;" width="0%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sonumber', gks_lang('Αριθμός')); ?></th>        
        <th nowrap class="table-dark hide_on_print" scope="col" style="text-align: left !important;" width="10%" ><?php echo makeSortLink($sortable, $sortable_url, $_GET, 'sopa', gks_lang('Τρόπος<br>Πληρωμής')); ?></th>  
    </tr>
</thead>
<tbody>
  
    <?php
    $curr_balance=0;
    $i = 0;
    while ($row = $result->fetch_assoc()) {

	$i++;
?>
  <tr class="<?php echo ($i % 2 == 0) ? 'even' : 'odd'; ?>">
    <th scope="row" nowrap class="mytdcm aa"><?php echo ($i + $showFrom);?></th>
    <td nowrap class="mytdcm hide_on_print">
      
      <?php if ($row['obj_type']=='wp_users') { ?>
        <a href="admin-users-item.php?id=<?php echo $row['doc_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
      <?php } else if ($row['obj_type']=='users_groups') { ?>
        <a href="admin-usersgroups-item.php?id=<?php echo $row['doc_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
      <?php } else if ($row['obj_type']=='company_users') { ?>
        <a href="admin-company-item.php?id=<?php echo $row['doc_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
      <?php } else if ($row['obj_type']=='acc_inv') { ?>
        <a href="admin-acc-inv-item.php?id=<?php echo $row['doc_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
      <?php } else if ($row['obj_type']=='acc_pay') { ?>
        <a href="admin-acc-pay-item.php?id=<?php echo $row['doc_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
      <?php } else if ($row['obj_type']=='order') { ?>
        <a href="admin-orders-item.php?id=<?php echo $row['doc_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
      <?php } else if ($row['obj_type']=='reservation') { ?>
        <a href="admin-hotel-reservation-item.php?id=<?php echo $row['doc_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
      <?php } else if ($row['obj_type']=='transfer_reservation') { ?>
        <a href="admin-transfer-reservation-item.php?id=<?php echo $row['doc_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
      <?php } else if ($row['obj_type']=='crm_leads') { ?>
        <a href="admin-crm-lead-item.php?id=<?php echo $row['doc_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
      <?php } else if ($row['obj_type']=='crm_tasks') { ?>
        <a href="admin-crm-task-item.php?id=<?php echo $row['doc_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
      <?php } else if ($row['obj_type']=='crm_machine') { ?>
        <a href="admin-crm-machine-item.php?id=<?php echo $row['doc_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
      <?php } else if ($row['obj_type']=='assets_moves') { ?>
        <a href="admin-assets-item.php?id=<?php echo $row['doc_id'];?>"><i class="enterrow fas fa-pen" title="<?php echo gks_lang('Προβολή');?>"></i></a>
      <?php } ?>
      <?php echo $row['doc_id'];?>
    </td>
    <td nowrap class="mytdcm"><?php echo showDate(strtotime($row['doc_date']), 'd/m/Y H:i', 1);?><br> </td>  
    <td nowrap class="mytdcm hide_on_print">
      <?php if ($row['obj_type']=='wp_users') { 
        echo gks_lang('Επαφή');
      } else if ($row['obj_type']=='users_groups') {
        echo gks_lang('Ομάδα επαφών');
      } else if ($row['obj_type']=='email') {
        echo gks_lang('email');
      } else if ($row['obj_type']=='sms') {
        echo gks_lang('SMS');
      } else if ($row['obj_type']=='viber') {
        echo gks_lang('Viber');
      } else if ($row['obj_type']=='company_users') {
        echo gks_lang('Εταιρεία');
      } else if ($row['obj_type']=='acc_inv') {
        echo gks_lang('Παραστατικό');
      } else if ($row['obj_type']=='acc_pay') { 
        echo gks_lang('Πληρωμή');
      } else if ($row['obj_type']=='order') {
        echo gks_lang('Παρραγγελία');
      } else if ($row['obj_type']=='reservation') {
        echo gks_lang('Κράτηση ξενοδοχείου');
      } else if ($row['obj_type']=='transfer_reservation') {
        echo gks_lang('Κράτηση transfer');
      } else if ($row['obj_type']=='crm_leads') {
        echo gks_lang('Ευκαιρία');
      } else if ($row['obj_type']=='crm_tasks') {
        echo gks_lang('Εργασία');
      } else if ($row['obj_type']=='crm_machine') {
        echo gks_lang('Συσκευή');
      } else if ($row['obj_type']=='assets_moves') {
        echo gks_lang('Πάγιο');
      } ?>
    
    </td>
    <td class="gks_td08"><div class="gks_dive1"><div class="gks_dive2 mydivexpand">
      <?php if ($row['obj_type']=='wp_users') { 
        echo gks_lang('Δημιουργία');
      } else if ($row['obj_type']=='users_groups') {
        if ($row['mytext1']=='add') echo gks_lang('Προσθήκη σε').' ';
        else if ($row['mytext1']=='delete') echo gks_lang('Αφαίρεση από').' ';
        echo $row['mytext2'];
        if ($row['myint1']) echo ' '.gks_lang('Ομαδάρχης');
      } else if ($row['obj_type']=='email') {
        echo '<i class="fas fa-envelope gks_email_view" data-id="'.$row['doc_id'].'"></i> ';
        echo $row['mytext1'].' <i class="fas fa-arrow-circle-right"></i> '.$row['mytext2'];
      } else if ($row['obj_type']=='sms') {
        echo '<i class="fas fa-sms gks_sms_view" data-id="'.$row['doc_id'].'"></i> ';
        if ($row['obj_folder']=='inbox') {
          echo nl2br_gks($row['mytext1']).' <i class="fas fa-arrow-circle-left"></i> '.$row['mytext4'];
        } else if ($row['obj_folder']=='sent') {
          echo nl2br_gks($row['mytext1']).' <i class="fas fa-arrow-circle-right"></i> '.$row['mytext2'];
        } else {
          echo nl2br_gks($row['mytext1']).' | '.$row['mytext2'];
        }
      } else if ($row['obj_type']=='viber') {
        if (trim_gks($row['mytext1'])!='') 
          echo '<i title='.gks_lang('Από επαφή').' class="fas fa-sign-in-alt gks_viber_fa-sign-in-alt"></i>'; 
        else if (trim_gks($row['mytext2'])!='') 
          echo '<i title="'.gks_lang('Προς επαφή').' class="fas fa-sign-out-alt gks_viber_fa-sign-out-alt"></i>';
        echo nl2br_gks($row['mytext3']);
      } else if ($row['obj_type']=='company_users') {
        if ($row['mytext1']=='add') echo gks_lang('Προσθήκη σε').' ';
        else if ($row['mytext1']=='delete') echo gks_lang('Αφαίρεση από').' ';
        

        
      } else if ($row['obj_type']=='acc_inv') {
        echo gks_lang('Παραστατικό');
      } else if ($row['obj_type']=='acc_pay') { 
        echo gks_lang('Πληρωμή');
      } else if ($row['obj_type']=='order') {
        echo gks_lang('Παρραγγελία');
      } else if ($row['obj_type']=='reservation') {
        echo gks_lang('Κράτηση ξενοδοχείου');
      } else if ($row['obj_type']=='transfer_reservation') {
        echo gks_lang('Κράτηση transfer');
      } else if ($row['obj_type']=='crm_leads') {
        echo $row['mytext1'];
      } else if ($row['obj_type']=='crm_tasks') {
        echo $row['mytext1'];
      } else if ($row['obj_type']=='crm_machine') {
        echo $row['mytext1'];
      } else if ($row['obj_type']=='assets_moves') {
        echo $row['mytext1'].' '.$row['mytext2'];
      } ?>
    
    </div></div></td>
    
    
    
    <td nowrap class="mytdcm hide_on_print">
      
      <?php if ($row['obj_type']=='wp_users') { ?>
        
        
      <?php } else if ($row['obj_type']=='email') { ?>
        <img src="img/<?php echo $row['myint1'];?>.png" border="0" width="16">
      <?php } else if ($row['obj_type']=='sms') { ?>
        <img src="img/<?php echo $row['myint1'];?>.png" border="0" width="16">
        <span class="sms_status sms_status_<?php echo $row['myint2'];?>"><?php echo $row['mytext3'];?></span>
      <?php } else if ($row['obj_type']=='viber') { ?>
        <img src="img/<?php echo $row['myint1'];?>.png" border="0" width="16" title="<?php echo gks_lang('Παραδόθηκε');?>">
        <img src="img/<?php echo $row['myint2'];?>.png" border="0" width="16" title="<?php echo gks_lang('Προβλήθηκε');?>">
      <?php } else if ($row['obj_type']=='acc_inv') { ?>
        <span class="acc_inv_state_<?php echo $row['doc_state'];?>"><?php echo getAccInvStateDescr($row['doc_state']);?></span>
      <?php } else if ($row['obj_type']=='acc_pay') { ?>
        <span class="acc_pay_state_<?php echo $row['doc_state'];?>"><?php echo getAccPayStateDescr($row['doc_state']);?></span>
      <?php } else if ($row['obj_type']=='order') { ?>
        <span class="order_state_<?php echo $row['doc_state'];?>"><?php echo getOrderStateDescr($row['doc_state']);?></span>
      <?php } else if ($row['obj_type']=='reservation') { ?>
        <span class="reservation_status_<?php echo $row['doc_state'];?>"><?php echo getHotelReservationStatusDescr($row['doc_state']);?></span>
      <?php } else if ($row['obj_type']=='transfer_reservation') { ?>
        <span class="transfer_reservation_status_<?php echo $row['doc_state'];?>"><?php echo getTransferReservationStatusDescr($row['doc_state']);?></span>
      <?php } else if ($row['obj_type']=='crm_leads') { ?>
        <span class="lead_status_<?php echo $row['myint1'];?>"><?php if (isset($leads_status[$row['myint1']])) echo $leads_status[$row['myint1']]['lead_status_descr'];?></span>        
      <?php } else if ($row['obj_type']=='crm_tasks') { ?>
        <span class="task_status_<?php echo $row['myint1'];?>"><?php if (isset($tasks_status[$row['myint1']])) echo $tasks_status[$row['myint1']]['task_status_descr'];?></span>
      <?php } else if ($row['obj_type']=='assets_moves') { ?>
        <img src="img/<?php echo $row['myint3']==0 ? "1" :"0";  ?>.png" border="0" width="16">
      
      <?php } ?>
    </td>  
    <?php
    
   
    
    $gks_price_netfpa=floatval($row['gks_price_netfpa']);
    ?>
 
    <td nowrap class="mytdcm" style="font-weight:bold;"><?php if ($gks_price_netfpa!=0) echo myCurrencyFormat($gks_price_netfpa);?></td>  

      
      
       
    <td        class="mytdcml"><?php echo $row['company_title']; if (isset($row['company_sub_title'])) echo '<br>'.$row['company_sub_title'];?></td> 
    <td        class="mytdcml"><?php echo $row['acc_journal_descr'];?></td>
    <td nowrap class="mytdcm"><?php echo $row['seira_code'];?></td>
    <td nowrap class="mytdcm"><?php if ($row['doc_number']<>0) echo $row['doc_number'];?></td>
    <td        class="mytdcml hide_on_print"><?php echo $row['payment_acquirer_name'];?></a></td>

  </tr>
<?php    
    }
?>

</tbody>
</table>
<div class="hide_on_print"><?php mytablepages($paging, $total_records); ?></div>

<?php } ?>



<?php include_once('_dialogs.php'); ?>
<script type="text/javascript">
<?php echo from_php_global_vars_echo();?>

jQuery(document).ready(function($) {
  <?php include_once('_dialogs.js.php'); ?>



  $('#fdoc_date-from').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));
  $('#fdoc_date-to').datetimepicker(jQuery.extend({},gks_datetimepicker_defaults,{mask:'39/19/9999',format:'d/m/Y', timepicker:false,dayOfWeekStart:1,}));

  $('.filterselectbox').on('change', function() {
      var v=$(this).val();
      var sname=$(this).attr('name')
      var multiple=$(this).attr('multiple');
      if (!(typeof multiple == 'undefined')) return;
      if (v==-2) { //is_custom_date
        if (sname == 'fdoc_date') {
          $('#filterdate-' + sname).css('display','inline-block'); 
          $('#' + sname + '-from').attr('name',sname + '-from');
          $('#' + sname + '-to').attr('name',sname + '-to');
        }
      } else {
        if (sname == 'fdoc_date') {
          $('#filterdate-' + sname).css('display','none'); 
          $('#' + sname + '-from').attr('name','');
          $('#' + sname + '-to').attr('name','');
        }
        $('#filter-form').submit();
      }
  });

  $('#user').autocomplete({
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
    create: function(event, ui){
      //$(this).attr('autocomplete',autocomplete_gks_disable);
    },    
    minLength: 3,
    autoFocus: true,
    delay: 300, //default
    select: function( event, ui ) {
      window.location.href='?id=' + ui.item.id;
    },

  });
});
</script>


<?php
//db_close();
include_once('_my_footer_admin.php');



