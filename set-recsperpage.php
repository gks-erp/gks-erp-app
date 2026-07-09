<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

define('SECURE', 1);
include_once('functions.php');
//gks_erp_cookie_start();

//echo $_gks_id_session.'|'.$_gks_session['gks']['rows_per_page'];die();
set_recsperpage();

function set_recsperpage() {
    global $_gks_session;
    
    $newnumpage = intval($_GET['num']);
    if ($newnumpage < 1)
        $newnumpage = 1;
    if ($newnumpage > 1000)
        $newnumpage = 1000;
    
    $_gks_session['gks']['rows_per_page'] = $newnumpage;
    //print_r($_GET);
    //die();
    gks_erp_cookie_save();

    $newurl = urldecode($_GET['url']);
    header('Location: ' . $newurl);
    die();
}