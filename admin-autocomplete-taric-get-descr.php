<?php

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();


$code=''; if (isset($_POST['code'])) $code=trim_gks(base64_decode($_POST['code']));

if ($code=='') {
  debug_mail(false,'code empty','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν έχει ορισθεί o κωδικός')));
  echo json_encode($return); die();}  

$myrand=time().rand(1000,9999).rand(1000,9999);
$url='https://tools.gks.gr/taric/get_code.php?code='.rawurlencode($code).'&rand='.$myrand;

$return=@file_get_contents($url);
if ($return===false) {
  debug_mail(false,'can not connect to tools.gks.gr','');
  $return = array('success' => false, 'message' => base64_encode(gks_lang('Δεν είναι δυνατή η σύνδεση με τον διακομιστή')));
  echo json_encode($return); die();} 
  
  
echo $return; die();  


