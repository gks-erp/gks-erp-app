<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/
ini_set('max_execution_time', 600);
set_time_limit(600);
//sleep(2);

//die();

//   /usr/bin/php7.4 /var/www/php/test.easyfilesselection.com/httpdocs/my/cron_async_queue.php aaa bbb
// aaa => guid
// bbb => id_async_queue
// otan to bbb exei kati tote to aaa na einai 'any'
//p.x 
//  /usr/bin/php7.4 /var/www/php/test.easyfilesselection.com/httpdocs/my/cron_async_queue.php 7f41deb1feb87f46ad265bad29c748db
//  /usr/bin/php7.4 /var/www/php/test.easyfilesselection.com/httpdocs/my/cron_async_queue.php any 1

// pending
// https://test.easyfilesselection.com/my/cron_async_queue.php?guid=9938e6794cceb709c80dc8347b717bcb
// https://test.easyfilesselection.com/my/cron_async_queue.php?guid=resume
// https://vetshop.gr/my/cron_async_queue.php?guid=resume

//echo 'guid_async_queue: '.$guid_async_queue; echo "\n";echo 'id_async_queue: '.$id_async_queue;echo "\n";die();



putenv("ENV=PRODUCTION");

define('SECURE', 1);
require_once('_current/_config.php');
require_once(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/functions.php');


$guid_async_queue='';
$id_async_queue=0;


if (!isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
if (isset($HTTP_RAW_POST_DATA)) $HTTP_RAW_POST_DATA = trim_gks($HTTP_RAW_POST_DATA);
$input_data = json_decode($HTTP_RAW_POST_DATA, true);
if (isset($input_data['guid'])) $guid_async_queue=trim_gks($input_data['guid']);
if (isset($input_data['id'])) $id_async_queue=intval($input_data['id']);

if ($guid_async_queue=='' and isset($_GET['guid'])) $guid_async_queue=trim_gks($_GET['guid']);
 
//if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/cron_async_'.time().'_'.rand(1000,9999).'.txt',$guid_async_queue."\n".$id_async_queue);






$my_wp_user_id=2;

db_open();
//debug_mail(false,'cron_async_queue.php','guid_async_queue:'.$guid_async_queue);

if ($guid_async_queue == 'resume') {
  //echo 'resume';

  $sql_resume_guid="SELECT `status`, Count(id_async_queue) AS cc, Max(`end`) AS max_end
  FROM gks_async_queue
  WHERE `status`<>'pending' and `end` is not null
  GROUP BY `status`
  order by Max(`end`)";
  $result_resume_guid = $db_link->query($sql_resume_guid);        
  if (!$result_resume_guid) {
    debug_mail(false,'error sql',$sql_resume_guid);
    die('sql error');}

  $auto_start_guid=true;
  while ($row_resume_guid= $result_resume_guid->fetch_assoc()) {
    if (isset($row_resume_guid['max_end']) and strtotime($row_resume_guid['max_end']) >= (time() - 5*60)) { // 5 lepta prin 
      //echo 'trexei idi kati edo kai 5 lepta'."\n";die();
      $auto_start_guid=false;
      break;
    }
  }
  //'.$run_guid_eidi.'
  //echo $auto_start_guid;
  if ($auto_start_guid) {
    gks_curl_post_async(GKS_SITE_URL.'my/cron_async_queue.php',array());
  } else {
    //echo 'trexei idi kati edo kai 5 lepta, opote min kaneis kati'."\n";die();
  }
  die();  
  
}
//echo 1/0;

if ($id_async_queue>0) {
  //na treksei to sigkekrimeno
  $sql="select * from gks_async_queue where status='pending' and id_async_queue=".$id_async_queue." limit 1";
} else if (strlen($guid_async_queue)==32) {
  //na treksei to proto apo to sigkekrimeno guid
  $sql="select * from gks_async_queue where status='pending' and guid='".$db_link->escape_string($guid_async_queue)."' order by id_async_queue limit 1";
} else {
  //na parei to proto stin lista gia na treksei
  $sql="select * from gks_async_queue where status='pending' order by id_async_queue limit 1";
}
//if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/async.txt','0 '.$sql."\n",FILE_APPEND);

//echo 'sql: '.$sql."\n";die();

$result = $db_link->query($sql);
if (!$result) {debug_mail(false,'sql error',$sql.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}


if ($result->num_rows==0) {
  //debug_mail(false,'no more async_queue and end',$sql);
  die();
}

$row = $result->fetch_assoc();
//print_r($row);echo "\n";die();

$guid_async_queue=$row['guid'];
$id_async_queue=$row['id_async_queue'];
$mytype=trim_gks($row['mytype']);
$cmd=trim_gks($row['cmd']);


//echo $guid_async_queue."\n".$id_async_queue."\n".$mytype."\n".$cmd."\n";die();

$sql_async="update gks_async_queue set 
status='running',
start=now(),
mydate_edit=now(),
user_id_edit=".$my_wp_user_id.",
myip='".$db_link->escape_string($gkIP)."'
where id_async_queue=".$id_async_queue;
//if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/async.txt','1 '.$sql_async."\n",FILE_APPEND);
$result_async = $db_link->query($sql_async);
//if (!$result_async) {debug_mail(false,'sql error',$sql_async.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}

//debug_mail(false,$guid_async_queue.' '.$id_async_queue,'');



switch ($mytype) {   
  case 'woo':
    switch ($cmd) {
      case 'get_product':      
        $eshop_id=intval($row['param1']);
        $id_product=intval($row['param2']);

        //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/async.txt','2 '.$eshop_id.' '.$id_product."\n",FILE_APPEND);

        $ret = gks_woo_get_eshop($eshop_id);
        if ($ret['success']==false) gks_async_queue_end($id_async_queue, $guid_async_queue, false, base64_decode($ret['message']), true);
        
        $eshop=$ret['eshop'];
        $data = array(
        	'cmd'=>'get_product',
        	'pid'=>$id_product,
        	'woosettings' => true,
        );
        $ret=gks_woo_post($eshop, $data);
        if ($ret['success']==false) gks_async_queue_end($id_async_queue, $guid_async_queue, false, base64_decode($ret['message']), true);

        $response_array=$ret['response_array'];
        if (isset($response_array['product'])==false) gks_async_queue_end($id_async_queue, $guid_async_queue, false, 'response_array product is not set', true);

        $woo_settings=$response_array['woo_settings'];
        $ret=gks_woo_product_update_local_from_woo($eshop,$response_array['product'],$woo_settings);
        if ($ret['success']==false) gks_async_queue_end($id_async_queue, $guid_async_queue, false, base64_decode($ret['message']), true);


        gks_async_queue_end($id_async_queue, $guid_async_queue, true, 'OK' ,true);
        
        break;
      case 'get_order':      
        $eshop_id=intval($row['param1']);
        $id_order=intval($row['param2']);
        $force=intval($row['param3']);
        $ret = gks_woo_get_eshop($eshop_id);
        if ($ret['success']==false) gks_async_queue_end($id_async_queue, $guid_async_queue, false, base64_decode($ret['message']), true);
        
        $eshop=$ret['eshop'];
        $data = array(
        	'cmd'=>'get_order',
        	'id_order'=>$id_order,
        	'woosettings' => true,
        	//'force'=>$force,
        );

        $ret=gks_woo_post($eshop, $data);
        //echo '<pre>ggggggggg1';print_r($ret);die();
        
        if ($ret['success']==false) gks_async_queue_end($id_async_queue, $guid_async_queue, false, base64_decode($ret['message']), true);
        
        
        
        $response_array=$ret['response_array'];
        if (isset($response_array['order_data'])==false) gks_async_queue_end($id_async_queue, $guid_async_queue, false, 'response_array order_data is not set', true);

        $woo_settings=$response_array['woo_settings'];
        $ret=gks_woo_order_update_local_from_woo($eshop,$response_array['order_data'],$woo_settings,$force);

        //file_put_contents('/var/www/php/test.easyfilesselection.com/tmp/dd0.txt',print_r($ret,true));
        //print '<pre>';print_r($ret);die();
        
        $data_tosend = array(
        	'cmd'=>'set_order_last_status',
        	'id_order'=>$id_order, 
          'woosettings' => false,
          'data'=>$ret,
        );
        $ret_tosend=gks_woo_post($eshop, $data_tosend);
        //file_put_contents('/var/www/php/test.easyfilesselection.com/tmp/dd2.txt',print_r($ret_tosend,true));
        
        
        
        
        if ($ret['success']==false) {
          gks_async_queue_end($id_async_queue, $guid_async_queue, false, base64_decode($ret['message']), true);
        } else {
          gks_async_queue_end($id_async_queue, $guid_async_queue, true, 'OK' ,true);
        }
        break;
        
      case 'get_comments_order':      
        $eshop_id=intval($row['param1']);
        $id_order=intval($row['param2']);
        $force=intval($row['param3']);

        $ret = gks_woo_get_eshop($eshop_id);
        if ($ret['success']==false) gks_async_queue_end($id_async_queue, $guid_async_queue, false, base64_decode($ret['message']), true);
        
        $eshop=$ret['eshop'];
        $data = array(
        	'cmd'=>'get_comments_order',
        	'id_order'=>$id_order,
        	'woosettings' => false,
        	//'force'=>$force,
        );
        $ret=gks_woo_post($eshop, $data);

        if ($ret['success']==false) gks_async_queue_end($id_async_queue, $guid_async_queue, false, base64_decode($ret['message']), true);


        
        $response_array=$ret['response_array'];
        if (isset($response_array['comments_data'])==false) gks_async_queue_end($id_async_queue, $guid_async_queue, false, 'response_array comments_data is not set', true);

        //print '<pre>';print_r($ret);die();

        $ret=gks_woo_comments_order_update_local_from_woo($eshop,$response_array['comments_data']);

        //file_put_contents('/var/www/php/test.easyfilesselection.com/tmp/dd0.txt',print_r($ret,true));
        //print '<pre>';print_r($ret);die();
        
        $data_tosend = array(
        	'cmd'=>'set_comments_order_last_status',
        	'id_order'=>$id_order, 
          'woosettings' => false,
          'data'=>$ret,
        );
        $ret_tosend=gks_woo_post($eshop, $data_tosend);
        //file_put_contents('/var/www/php/test.easyfilesselection.com/tmp/dd2.txt',print_r($ret_tosend,true));
        
        
        
        
        if ($ret['success']==false) {
          gks_async_queue_end($id_async_queue, $guid_async_queue, false, base64_decode($ret['message']), true);
        } else {
          gks_async_queue_end($id_async_queue, $guid_async_queue, true, 'OK' ,true);
        }
        break;

      case 'get_coupon':      
        $eshop_id=intval($row['param1']);
        $id_coupon=intval($row['param2']);
        $force=intval($row['param3']);
        $ret = gks_woo_get_eshop($eshop_id);
        if ($ret['success']==false) gks_async_queue_end($id_async_queue, $guid_async_queue, false, base64_decode($ret['message']), true);
        
        $eshop=$ret['eshop'];
        $data = array(
        	'cmd'=>'get_coupon',
        	'id_coupon'=>$id_coupon,
        	'woosettings' => true,
        	//'force'=>$force,
        );

        $ret=gks_woo_post($eshop, $data);
        //echo '<pre>ggggggggg1';print_r($ret);die();
        
        if ($ret['success']==false) gks_async_queue_end($id_async_queue, $guid_async_queue, false, base64_decode($ret['message']), true);
        
        
        
        $response_array=$ret['response_array'];
        if (isset($response_array['coupon_data'])==false) gks_async_queue_end($id_async_queue, $guid_async_queue, false, 'response_array coupon_data is not set', true);

        $woo_settings=$response_array['woo_settings'];
        $ret=gks_woo_coupon_update_local_from_woo($eshop,$response_array['coupon_data'],$woo_settings,$force);

        //file_put_contents('/var/www/php/test.easyfilesselection.com/tmp/dd0.txt',print_r($ret,true));
        //print '<pre>';print_r($ret);die();
        
        $data_tosend = array(
        	'cmd'=>'set_coupon_last_status',
        	'id_coupon'=>$id_coupon, 
          'woosettings' => false,
          'data'=>$ret,
        );
        $ret_tosend=gks_woo_post($eshop, $data_tosend);
        //file_put_contents('/var/www/php/test.easyfilesselection.com/tmp/dd2.txt',print_r($ret_tosend,true));
        
        
        
        
        if ($ret['success']==false) {
          gks_async_queue_end($id_async_queue, $guid_async_queue, false, base64_decode($ret['message']), true);
        } else {
          gks_async_queue_end($id_async_queue, $guid_async_queue, true, 'OK' ,true);
        }
        break;      
      
      
        
      default: 
        debug_mail(false,'cron async queue cmd error '.$cmd,$sql);
        gks_async_queue_end($id_async_queue, $guid_async_queue, false, 'unknown cmd' ,true);
        break;
    }
    break;

  case 'mass':
    switch ($cmd) {
      case 'send':  
        $model_id=intval($row['param1']);
        $mylimit=intval($row['param2']);
        
        $sql_sms="select * from gks_sms 
        where model='mass' and model_id=".$model_id." and status=101
        order by id limit ".$mylimit;
        $result_sms = $db_link->query($sql_sms);
        if (!$result_sms) {debug_mail(false,'sql error',$sql_sms.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}
        //echo $sql;die();
        if ($result->num_rows==0) {
          gks_async_queue_end($id_async_queue, $guid_async_queue, true, 'OK' ,true);
        }
        $sms_list=[];
        while ($row_sms=$result_sms->fetch_assoc()) {
          $sms_list[]=$row_sms;
        }
        
        $reas=[];//row erp apps
        
        foreach ($sms_list as $sms_item) {
          $sender_sms_provider=trim_gks($sms_item['sms_provider']);
          
          $erp_app_mobile_id=$sms_item['erp_app_mobile_id'];
          $id_rec=$sms_item['id'];
          $to_f=$sms_item['myto'];
          $szMessageText=$sms_item['Message'];
          $user_id_sender=$sms_item['user_id'];
          $model=$sms_item['model'];
          $model_id=$sms_item['model_id'];
          
          $sms_res_part_ID=$sms_item['message_id'];
          $sms_res_part_Message=$sms_item['Message'];
          $sms_res_part_Length=$sms_item['Length'];
          $sms_res_part_Smscount=$sms_item['Smscount'];
          $sms_res_part_Parts=$sms_item['Parts'];
          $sms_res_part_cost=$sms_item['cost'];
          
          $sms_res_status=409;
          $sms_res_status_name=gks_lang('Σε ουρά');
          
          // copy from functions_sms.php
          
          if ($sender_sms_provider=='gks_erp_app_mobile') {
            

            
            if (isset($reas[$erp_app_mobile_id])==false) {
              $sql="select * from gks_erp_app_mobile where id_erp_app_mobile=".$erp_app_mobile_id." and erp_app_mobile_disabled=0 and erp_app_mobile_can_sms=1";
              $myrun = $db_link->query($sql);
              if (!$myrun) {debug_mail(false,'sql error',$sql.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}
              
              if ($myrun->num_rows==0) {
                gks_async_queue_end($id_async_queue, $guid_async_queue, false, 'gks_erp_app_mobile not found '.$sql, true);  
              }
              $reas[$erp_app_mobile_id]=$myrun->fetch_assoc();
            }
            $erp_app_mobile_cost_per_sms=floatval($reas[$erp_app_mobile_id]['erp_app_mobile_cost_per_sms']);
            $from=$reas[$erp_app_mobile_id]['erp_app_mobile_country'].$reas[$erp_app_mobile_id]['erp_app_mobile_phonenumber'];
            $arow=$reas[$erp_app_mobile_id];
            //print'<pre>';print_r($reas);die();
            
          
            $public_url='';
            if ($arow['erp_app_mobile_url']=='frp') {
              if (trim_gks($arow['erp_app_mobile_token'])!='') {
                $public_url='http://'.$arow['erp_app_mobile_token'].GKS_PROXY['DOMAIN_BASE_NAME'].':'.GKS_PROXY['VPORT'];
              }
            } else {
              if (trim_gks($arow['erp_app_mobile_url'])!='' and $arow['erp_app_mobile_port']>0) {
                $public_url='http://'.$arow['erp_app_mobile_url'].':'.$arow['erp_app_mobile_port'];
              }
            }
            if ($public_url=='') {
              $sms_res_part_OK='url error';
              gks_async_queue_end($id_async_queue, $guid_async_queue, false, 'url error', true);  
            } else {
              $rand1=rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999).rand(1000,9999);
              $semd5=md5($rand1.$arow['erp_app_mobile_secret']);
        
              
              
              
              $public_url.='/sendsms';
              
              //echo $public_url;die();
              
              $params=array(
                'rand1'=>$rand1,
                'semd5'=>$semd5,
                'rec_id' => $id_rec,
                'to' => $to_f,
                'text' => $szMessageText,
                'user_id' => intval($user_id_sender),
                'model' => $model,
                'model_id' => intval($model_id),
              );
              
              $c = curl_init();
              curl_setopt( $c, CURLOPT_URL, $public_url );
              curl_setopt( $c, CURLOPT_POST, true );
              curl_setopt( $c, CURLOPT_POSTFIELDS, $params );
              curl_setopt( $c, CURLOPT_RETURNTRANSFER, true );
          
          
              $content = curl_exec( $c );
              $http_status = curl_getinfo($c, CURLINFO_HTTP_CODE);
              curl_close( $c );
          
              
              if($http_status != 200){
                $sms_res_part_OK= 'http_status error '.$http_status;
                $sms_result='ERROR:http_status error '.$http_status;
                
              } else {
                //die('<pre>'.$public_url."\n".$content);
                $response_data=json_decode($content,true);
                if ($response_data === null && json_last_error() !== JSON_ERROR_NONE) {
                  $sms_res_part_OK='json decode error';
                  $sms_result='ERROR:json decode error';
                } else {
                  if (isset($response_data['success'])==false) {
                    $sms_res_part_OK= 'data response error data';
                    $sms_result='ERROR:data response error data';
                  } else {
                    if ($response_data['success']==false) {
                      $sms_res_part_OK=$response_data['message'];
                      $sms_result='ERROR:'.$response_data['message'];
                    } else {
                      $sms_res_part_OK='OK:';
                      $sms_result='';
                    }
                  }
                }
              }
              //die('<pre>ffff '.$sender_sms_provider.' '.$sql);
            }
            
            
             
                
          } else if ($sender_sms_provider=='smsapi') {
        
        
            
            $token = $GKS_SMS_TOKEN;
            $params = array(
          //    'test' => 1,
              'idx' => 1,
              'details' => 1,
            //  'single' => 1,
              'datacoding' => 'gsm',
          //    'normalize' => '1',
          //    'encoding' => 'iso-8859-7',
              'encoding' => 'utf-8',
              'to' => $to_f,
              'from' => $from,
            //  'message' => $szMessageText_conv ,
              'message' => $szMessageText,
            );
            
            $sms_result = sms_send_api($params,$token);
            debug_mail(false,'sms_result:',$sms_result);
            //echo $sms_result;
            $sms_res_parts=explode("\n",$sms_result);
            //print_r($sms_res_parts);
            
            $sms_res_part_Message='';
            $sms_res_part_Length=0;
            $sms_res_part_Smscount=0;
            $sms_res_part_Parts=0;
            $sms_res_part_OK='';
            $sms_res_part_ID='';
            $sms_res_part_cost=0;
          
            $my_status=409;
            $my_status_name=gks_lang('Σε ουρά');
                    
            if (0 === strpos($sms_result, 'ERROR:')) {
              debug_mail(false,'warning on mysms error:',$sms_result);
            } else {
            
              foreach ($sms_res_parts as $part) {
                if (0 === strpos($part, 'Message: ')) {
                  $sms_res_part_Message=substr($part, strlen('Message: '));
                }
                if (0 === strpos($part, 'Length: ')) {
                  $sms_res_part_Length=intval(substr($part,  strlen('Length: ')));
                }
                if (0 === strpos($part, 'Sms count: ')) {
                  $sms_res_part_Smscount=intval(substr($part, strlen('Sms count: ')));
                }
                if (0 === strpos($part, 'Parts: ')) {
                  $sms_res_part_Parts=intval(substr($part, strlen('Parts: ')));
                }
                if (0 === strpos($part, 'OK:')) {
                  $sms_res_part_OK = $part;
                  $sms_res_part_OK_parts=explode(':',$sms_res_part_OK);
                  $sms_res_part_ID=$sms_res_part_OK_parts[1];
                  $sms_res_part_cost=$sms_res_part_OK_parts[2];
                }
              }
            }
            
          //  echo '<pre>';
          //  echo $sms_res_part_Message."\r\n";
          //  echo $sms_res_part_Length."\r\n";
          //  echo $sms_res_part_Smscount."\r\n";
          //  echo $sms_res_part_Parts."\r\n";
          //  echo $sms_res_part_OK."\r\n";
          //  echo $sms_res_part_ID."\r\n";
          //  echo $sms_res_part_cost."\r\n";
          
         
          }
        
          $sql="update gks_sms set
          sms_result='".$db_link->escape_string($sms_result)."',
          message_id='".$db_link->escape_string($sms_res_part_ID)."',
          Message='".$db_link->escape_string($sms_res_part_Message)."',
          Length=".$sms_res_part_Length.",
          Smscount=".$sms_res_part_Smscount.",
          Parts=".$sms_res_part_Parts.",
          OK='".$db_link->escape_string($sms_res_part_OK)."',
          cost=".$sms_res_part_cost.",
          points=".$sms_res_part_cost.",
          myret=".((0 === strpos($sms_res_part_OK, 'OK:')) ?  '1' : '0').",
          status=".$sms_res_status.",
          status_name='".$db_link->escape_string($sms_res_status_name)."'
          where id=".$id_rec." limit 1";
          
          //echo $sql;die();
          $myrun = $db_link->query($sql);
          if (!$myrun) {
            debug_mail(false,'warning on mysms error sql',$sql);
          }         
           
           
        }
      
        gks_async_queue_end($id_async_queue, $guid_async_queue, true, 'OK. SMS count: '.count($sms_list), true);  
        
        //delete me
        //$db_link->query("update gks_async_queue set status='pending' where id_async_queue=33803");
        //echo '<pre>';print_r($sms_list);die(); 
            
        break;
      case 'fffffffff':
      
        break;
    }
    break;  

    
  default:      
    gks_async_queue_end($id_async_queue, $guid_async_queue, false, 'unknown mytype' ,true);
    debug_mail(false,'cron async queue mytype error '.$mytype,$sql);
    break;
}


//if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/async.txt','100 '.$sql_async."\n",FILE_APPEND);

die();

function gks_async_queue_end($id_async_queue, $guid_async_queue, $success, $message, $gonext) {
  global $db_link;
  global $my_wp_user_id;
  global $gkIP;
  global $mytype;
  global $cmd;

  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/async.txt','10 '.$id_async_queue.' '.$guid_async_queue.' '.$success.' '.$message.' '.$gonext."\n",FILE_APPEND);
  
  $sql_async="update gks_async_queue set 
  status='".($success ? 'finish' : 'errors')."',
  end=now(),
  result=".($success ? '1' : '0').",
  result_message='".$db_link->escape_string($message)."',
  mydate_edit=now(),
  user_id_edit=".$my_wp_user_id.",
  myip='".$db_link->escape_string($gkIP)."'
  where id_async_queue=".$id_async_queue;
  $result_async = $db_link->query($sql_async);
  if (!$result_async) {debug_mail(false,'sql error',$sql_async.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}
  
  //if (GKS_DEBUG) file_put_contents(GKS_SITE_PATH.'tmp/async.txt','11 '.$id_async_queue.' '.$guid_async_queue.' '.$success.' '.$message.' '.$gonext."\n",FILE_APPEND);
  //die();
  
  if ($gonext) {
    if ($mytype=='mass' and $cmd=='send') {
        
      $GKS_SMS_MASS_CHUNK_DELAY_SECS=10;
      $sql_sms="select myvalue from gks_settings where mykey='GKS_SMS_MASS_CHUNK_DELAY_SECS'";
      $result_sms = $db_link->query($sql_sms);
      if (!$result_sms) {debug_mail(false,'sql error',$sql_sms.' '.$db_link->errno . '-'.$db_link->error); die('sql error');}
      //echo $sql;die();
      if ($result_sms->num_rows==1) {
        $row_sms=$result_sms->fetch_assoc();
        $GKS_SMS_MASS_CHUNK_DELAY_SECS=intval($row_sms['myvalue']);
        if ($GKS_SMS_MASS_CHUNK_DELAY_SECS<1) $GKS_SMS_MASS_CHUNK_DELAY_SECS=10;
      }
      sleep($GKS_SMS_MASS_CHUNK_DELAY_SECS);
    }
    
    gks_curl_post_async(GKS_SITE_URL.'my/cron_async_queue.php',array('guid' =>$guid_async_queue));
    die();   
  }
}

