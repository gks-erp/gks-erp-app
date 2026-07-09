<?php
//session_start();
define('SECURE', 1);
include_once('functions.php');

if (isset($_gks_session['gks']['stat']['admin_stats_bots_enable']) and $_gks_session['gks']['stat']['admin_stats_bots_enable']==false) {
  $_gks_session['gks']['stat']['admin_stats_bots_enable'] = true;
} else {
  $_gks_session['gks']['stat']['admin_stats_bots_enable'] = false;
}
gks_erp_cookie_save();
header('Location: '.$_GET['redirect']);
die();