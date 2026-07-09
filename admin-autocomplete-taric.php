<?php

define('SECURE', 1);
include_once('functions.php');
gks_permission_user_must_login_post();



if (!isset($_GET['term'])) die();
$term='';
if (isset($_GET['term'])) $term=trim_gks($_GET['term']);
$myrand=time().rand(1000,9999).rand(1000,9999);
$url='https://tools.gks.gr/taric/search.php?term='.$_GET['term'].'&rand='.$myrand;

$response=file_get_contents($url);

echo $response;
