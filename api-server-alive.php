<?php

define('SECURE', 1);
include_once('functions.php');
db_open();
$sql="select * from gks_stat_online limit 1";
$result = $db_link->query($sql);

echo time();
