<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/


define('SECURE', 1);
include_once('functions.php');

$my_page_title=gks_lang('Αυτόματη αποσύνδεση');
$redirect_to=''; if (isset($_GET['redirect_to']))  $redirect_to=rawurldecode($_GET['redirect_to']);


db_open();
stat_record();

wp_logout();
if ($redirect_to!='') {
  header ('Location: '.$redirect_to);
} else {
  header ('Location: /');
}
die();



