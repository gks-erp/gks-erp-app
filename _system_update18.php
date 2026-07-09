<?php
/* 
Κώστας Γουτούδης
gks ERP
www.gks.gr
*/

//if (file_exists(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/vendor/symfony/deprecation-contracts/function.php')==false) {
//  echo '<div style="background-color: red;color:white">update vendor</div>';die();}


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_cache_googlemaps_place` (
  `id_place` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `place_id` varchar(190) DEFAULT NULL,
  `response` longtext DEFAULT NULL,
  `lat` double NOT NULL DEFAULT 0,
  `lng` double NOT NULL DEFAULT 0,
  `url` text DEFAULT NULL,
  `address` varchar(1024) DEFAULT NULL,
  `language` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id_place`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `place_id` (`place_id`),
  KEY `language` (`language`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");	
	


$sql="select id_address from gks_cache_googlemaps_address limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
gks_run_sql("ALTER TABLE `gks_geocode` 
  RENAME TO `gks_cache_googlemaps_address`,
  CHANGE COLUMN `id_geocode` `id_address` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  DROP PRIMARY KEY,
  ADD PRIMARY KEY  USING BTREE(`id_address`);");
}




gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_cache_googlemaps_directions` (
  `id_directions` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `from_name` varchar(190) DEFAULT NULL,
  `from_place_id` varchar(190) DEFAULT NULL,
  `from_lat` double NOT NULL DEFAULT 0,
  `from_lng` double NOT NULL DEFAULT 0,
  `to_name` varchar(190) DEFAULT NULL,
  `to_place_id` varchar(190) DEFAULT NULL,
  `to_lat` double NOT NULL DEFAULT 0,
  `to_lng` double NOT NULL DEFAULT 0,
  `distance` int(11) NOT NULL DEFAULT 0,
  `duration` int(11) NOT NULL DEFAULT 0,
  `response` longtext default null,
  `ferries_count` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_directions`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `from_name` (`from_name`),
  KEY `from_place_id` (`from_place_id`),
  KEY `from_lat` (`from_lat`),
  KEY `from_lng` (`from_lng`),
  KEY `to_name` (`to_name`),
  KEY `to_place_id` (`to_place_id`),
  KEY `to_lat` (`to_lat`),
  KEY `to_lng` (`to_lng`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");




$sql="select poi_from_place_id from gks_transfer_reservation limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_transfer_reservation
	add column poi_from_place_id varchar(190) DEFAULT NULL after poi_id_from,
	add column poi_from_place_formatted_address varchar(190) DEFAULT NULL after poi_from_place_id,
	add column poi_from_place_lat double NOT NULL DEFAULT 0 after poi_from_place_formatted_address,
	add column poi_from_place_lng double NOT NULL DEFAULT 0 after poi_from_place_lat,
	
	add column poi_to_place_id varchar(190) DEFAULT NULL after poi_id_to,
	add column poi_to_place_formatted_address varchar(190) DEFAULT NULL after poi_to_place_id,
	add column poi_to_place_lat double NOT NULL DEFAULT 0 after poi_to_place_formatted_address,
	add column poi_to_place_lng double NOT NULL DEFAULT 0 after poi_to_place_lat");
}

$sql="select update_from_gks from gks_transfer_reservation limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("alter table gks_transfer_reservation
	add column update_from_gks tinyint(4) NOT NULL DEFAULT 0");
}
$sql="select update_from_gks from gks_hotel_reservation limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("alter table gks_hotel_reservation
	add column update_from_gks tinyint(4) NOT NULL DEFAULT 0");
}


$sql="select formula_per_km_ot from gks_transfer_oxima_type limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_transfer_oxima_type
	add column formula_per_km_ot text DEFAULT NULL");
}



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_oxima_type_per_km` (
  `id_transfer_oxima_type_per_km` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `transfer_oxima_type_id` int(11) NOT NULL DEFAULT 0,
  `from_to_poi_id` int(11) NOT NULL DEFAULT 0,
  `formula_per_km_ft` text DEFAULT NULL,
  `aa` int(11) NOT NULL DEFAULT 0,

  PRIMARY KEY (`id_transfer_oxima_type_per_km`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `transfer_oxima_type_id` (`transfer_oxima_type_id`),
  KEY `from_to_poi_id` (`from_to_poi_id`),
  KEY `aa` (`aa`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$read_file=file_get_contents(GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/_current/_config.php');
if (strpos($read_file, 'GKS_HOTEL_PREFIX') === false) {
  echo '_current/_config.php file not contains GKS_HOTEL_PREFIX<br>';die();}
if (strpos($read_file, 'GKS_ORDERS_PREFIX') === false) {
  echo '_current/_config.php file not contains GKS_ORDERS_PREFIX<br>';die();}
if (strpos($read_file, 'GKS_INV_ACC_PREFIX') === false) {
  echo '_current/_config.php file not contains GKS_INV_ACC_PREFIX<br>';die();}
if (strpos($read_file, 'GKS_TRANSFER_PREFIX') === false) {
  echo '_current/_config.php file not contains GKS_TRANSFER_PREFIX<br>';die();}



if (strpos($read_file, 'GKS_ILYDA_COM_MODE_TEST_API') === false) {
  echo '_current/_config.php file not contains GKS_ILYDA_COM_MODE_TEST_API<br>';die();}
if (strpos($read_file, 'GKS_ILYDA_COM_MODE_LIVE_API') === false) {
  echo '_current/_config.php file not contains GKS_ILYDA_COM_MODE_LIVE_API<br>';die();}
if (strpos($read_file, 'GKS_CARDLINK_uniqueIntegratorId') === false) {
  echo '_current/_config.php file not contains GKS_CARDLINK_uniqueIntegratorId<br>';die();}
if (strpos($read_file, 'GKS_MELLONGROUP_COM_API') === false) {
  echo '_current/_config.php file not contains GKS_MELLONGROUP_COM_API<br>';die();}
if (strpos($read_file, 'GKS_Meg_EFT_POS_Driver_licenseKey') === false) {
  echo '_current/_config.php file not contains GKS_Meg_EFT_POS_Driver_licenseKey<br>';die();}
if (strpos($read_file, 'GKS_Meg_EFT_POS_Driver_vatNumber') === false) {
  echo '_current/_config.php file not contains GKS_Meg_EFT_POS_Driver_vatNumber<br>';die();}
if (strpos($read_file, 'GKS_EPAY_COM_API') === false) {
  echo '_current/_config.php file not contains GKS_EPAY_COM_API<br>';die();}

if (strpos($read_file, 'GKS_WORLDLINE_COM_API') === false) {
  echo '_current/_config.php file not contains GKS_WORLDLINE_COM_API<br>';die();}
if (strpos($read_file, 'GKS_WORLDLINE_COM_API_TOKEN') === false) {
  echo '_current/_config.php file not contains GKS_WORLDLINE_COM_API_TOKEN<br>';die();}
if (strpos($read_file, 'GKS_WORLDLINE_COM_API_BANK_ID') === false) {
  echo '_current/_config.php file not contains GKS_WORLDLINE_COM_API_BANK_ID<br>';die();}
if (strpos($read_file, 'GKS_WORLDLINE_COM_API_PARTNER_ID') === false) {
  echo '_current/_config.php file not contains GKS_WORLDLINE_COM_API_PARTNER_ID<br>';die();}
if (strpos($read_file, 'GKS_WORLDLINE_COM_API_PARTNER_KEY') === false) {
  echo '_current/_config.php file not contains GKS_WORLDLINE_COM_API_PARTNER_KEY<br>';die();}

if (strpos($read_file, 'GKS_PROXY') === false) {
  echo '_current/_config.php file not contains GKS_PROXY<br>';die();}

if (strpos($read_file, 'GKS_IMAGE_EXTENSION') === false) {
  echo '_current/_config.php file not contains GKS_IMAGE_EXTENSION<br>';die();}


$sql="select peppol_code from gks_acc_eidi_parastatikon limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_acc_eidi_parastatikon
	add column peppol_code int(11) NOT NULL DEFAULT 0");
	
	gks_run_sql("update gks_acc_eidi_parastatikon 
	set peppol_code=380
	where eidos_parastatikou_aade_code in ('1.1','1.2','1.3','1.4','1.5','1.6','2.1','2.2','2.3','2.4','3.1','3.2','7.1','8.2','11.1','11.2','11.3','11.5')");
	
	gks_run_sql("update gks_acc_eidi_parastatikon 
	set peppol_code=381
	where eidos_parastatikou_aade_code in ('5.1','5.2','11.4')");
	
	gks_run_sql("update gks_acc_eidi_parastatikon 
	set peppol_code=389
	where eidos_parastatikou_aade_code in ('6.1','6.2')");
	
	gks_run_sql("update gks_acc_eidi_parastatikon 
	set peppol_code=394
	where eidos_parastatikou_aade_code in ('8.1')");
	
}



$sql="select poi_company_id from gks_poi limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_poi
	add column poi_company_id int(11) NOT NULL DEFAULT 0,
	add column poi_company_sub_id int(11) NOT NULL DEFAULT 0,
  add column poi_parastatiko_apodiji_journal_id int(11) NOT NULL DEFAULT 0,
  add column poi_parastatiko_apodiji_seira_id int(11) NOT NULL DEFAULT 0, 
  add column poi_parastatiko_timologio_journal_id int(11) NOT NULL DEFAULT 0,
  add column poi_parastatiko_timologio_seira_id int(11) NOT NULL DEFAULT 0");
}


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_sub_company_details` (
  `id_transfer_sub_company_details` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `myip` varchar(48) DEFAULT NULL,
  `transfer_id` int(11) NOT NULL DEFAULT 0,
  `company_sub_id` int(11) NOT NULL DEFAULT 0,

  `transfer_parastatiko_apodiji_journal_id` int(11) NOT NULL DEFAULT 0,
  `transfer_parastatiko_apodiji_seira_id` int(11) NOT NULL DEFAULT 0,
  `transfer_parastatiko_apodiji_aade_mydata_live` tinyint(4) NOT NULL DEFAULT 0,
  `transfer_parastatiko_apodiji_fiscal_position_id` int(11) NOT NULL DEFAULT 0,
  `transfer_parastatiko_apodiji_pricelist_id` int(11) NOT NULL DEFAULT 0,
  `transfer_parastatiko_apodiji_print_file_type` varchar(16) DEFAULT 'pdf',
  `transfer_parastatiko_apodiji_print_grayscale` tinyint(4) NOT NULL DEFAULT 0,
  `transfer_parastatiko_apodiji_print_landscape` tinyint(4) NOT NULL DEFAULT 0,
  `transfer_parastatiko_apodiji_print_zoom` double NOT NULL DEFAULT 1,
  `transfer_parastatiko_apodiji_print_form_id` int(11) NOT NULL DEFAULT 0,
  `transfer_parastatiko_apodiji_max_ammount` double NOT NULL DEFAULT 100,
  `transfer_parastatiko_apodiji_assigned_id` int(11) NOT NULL DEFAULT 0,
  `transfer_parastatiko_apodiji_affect_balance` tinyint(4) NOT NULL DEFAULT 0,
  `transfer_parastatiko_apodiji_affect_balance_all_poso` tinyint(4) NOT NULL DEFAULT 1,
  `transfer_parastatiko_apodiji_affect_balance_all_poso_type` varchar(32) DEFAULT 'pliroteo',
  `transfer_parastatiko_apodiji_affect_balance_pros` tinyint(4) NOT NULL DEFAULT 0,
  `transfer_parastatiko_apodiji_email_template` varchar(190) DEFAULT NULL,
  `transfer_parastatiko_apodiji_email_subject` varchar(190) DEFAULT NULL,
  `transfer_parastatiko_apodiji_email_from` varchar(190) DEFAULT NULL,
  `transfer_parastatiko_timologio_journal_id` int(11) NOT NULL DEFAULT 0,
  `transfer_parastatiko_timologio_seira_id` int(11) NOT NULL DEFAULT 0,
  `transfer_parastatiko_timologio_aade_mydata_live` tinyint(4) NOT NULL DEFAULT 0,
  `transfer_parastatiko_timologio_fiscal_position_id` int(11) NOT NULL DEFAULT 0,
  `transfer_parastatiko_timologio_pricelist_id` int(11) NOT NULL DEFAULT 0,
  `transfer_parastatiko_timologio_print_file_type` varchar(16) DEFAULT 'pdf',
  `transfer_parastatiko_timologio_print_grayscale` tinyint(4) NOT NULL DEFAULT 0,
  `transfer_parastatiko_timologio_print_landscape` tinyint(4) NOT NULL DEFAULT 0,
  `transfer_parastatiko_timologio_print_zoom` double NOT NULL DEFAULT 1,
  `transfer_parastatiko_timologio_print_form_id` int(11) NOT NULL DEFAULT 0,
  `transfer_parastatiko_timologio_max_ammount` double NOT NULL DEFAULT 100,
  `transfer_parastatiko_timologio_assigned_id` int(11) NOT NULL DEFAULT 0,
  `transfer_parastatiko_timologio_affect_balance` tinyint(4) NOT NULL DEFAULT 0,
  `transfer_parastatiko_timologio_affect_balance_all_poso` tinyint(4) NOT NULL DEFAULT 1,
  `transfer_parastatiko_timologio_affect_balance_all_poso_type` varchar(32) DEFAULT 'pliroteo',
  `transfer_parastatiko_timologio_affect_balance_pros` tinyint(4) NOT NULL DEFAULT 0,
  `transfer_parastatiko_timologio_email_template` varchar(190) DEFAULT NULL,
  `transfer_parastatiko_timologio_email_subject` varchar(190) DEFAULT NULL,
  `transfer_parastatiko_timologio_email_from` varchar(190) DEFAULT NULL,
  PRIMARY KEY (`id_transfer_sub_company_details`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `transfer_id` (`transfer_id`),
  KEY `company_sub_id` (`company_sub_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select monada_peppol_code from gks_monades_metrisis limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_monades_metrisis
	add column monada_peppol_code varchar(64) DEFAULT NULL");
	//1 - Τεμάχια H87 - piece
	//2 - Κιλά  KGM - kilogram
	//3 - Λίτρα LTR - litre
	//4 - Μέτρα MTR - metre
	//5 - Τετραγωνικά Μέτρα MTK - square metre
	//6 - Κυβικά Μέτρα - MTQ cubic metre
	//https://docs.peppol.eu/poacc/billing/3.0/codelist/UNECERec20/
	//https://docs.peppol.eu/poacc/billing/3.0/codelist/UNECERec21/
	
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='H87' where id_monada=1");  //Τεμάχια piece
	//gks_run_sql("update gks_monades_metrisis set monada_peppol_code='' where id_monada=6"); //6άδα
	//gks_run_sql("update gks_monades_metrisis set monada_peppol_code='' where id_monada=7"); //10άδα
	//gks_run_sql("update gks_monades_metrisis set monada_peppol_code='' where id_monada=8"); //12άδα
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='GRM' where id_monada=10"); //Γραμμάρια 
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='KGM' where id_monada=11"); //Κιλά kilogram
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='TNE' where id_monada=12"); //Τόνοι tonne (metric ton) Synonym: metric ton
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='MTR' where id_monada=20"); //Μέτρα metre
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='KMT' where id_monada=21"); //Χιλιόμετρα kilometre
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='MMT' where id_monada=22"); //Χιλιοστά millimetre
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='CMT' where id_monada=23"); //Εκατοστά centimetre
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='MTK' where id_monada=30"); //Τετραγωνικά Μέτρα
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='ACR' where id_monada=31"); //Στρέμματα acre
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='MMK' where id_monada=32"); //Τετραγωνικά Χιλιοστά square millimetre
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='CMK' where id_monada=33"); //Τετραγωνικά Εκατοστά square centimetre
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='KMK' where id_monada=34"); //Τετραγωνικά Χιλιόμετρα square kilometre
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='MTQ' where id_monada=40"); //Κυβικά Μέτρα
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='MMQ' where id_monada=41"); //Κυβικά Εκατοστά cubic centimetre
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='MTQ' where id_monada=42"); //Κυβικά Χιλιοστά cubic millimetre
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='H20' where id_monada=43"); //Κυβικά Χιλιόμετρα cubic kilometre
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='LTR' where id_monada=44"); //Λίτρα
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='MLT' where id_monada=45"); //Χιλιοστόλιτρα millilitre
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='SEC' where id_monada=50"); //Δευτερόλεπτα second [unit of time]
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='MIN' where id_monada=51"); //Λεπτά minute [unit of time]
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='HUR' where id_monada=52"); //Ώρες hour
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='DAY' where id_monada=53"); //Ημέρες day
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='WEE' where id_monada=54"); //Εβδομάδες week
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='MON' where id_monada=55"); //Μήνες month Unit of time equal to 1/12 of a year of 365,25 days.
	gks_run_sql("update gks_monades_metrisis set monada_peppol_code='ANN' where id_monada=56"); //Έτη year Unit of time equal to 365,25 days. Synonym: Julian year
	//gks_run_sql("update gks_monades_metrisis set monada_peppol_code='' where id_monada=100"); //Διανυκτέρευση
	//gks_run_sql("update gks_monades_metrisis set monada_peppol_code='' where id_monada=200"); //Διαδρομή
	
}

$sql="select loipon_foron_peppol_code from gks_aade_katigoria_loipon_foron limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_aade_katigoria_loipon_foron
	add column loipon_foron_peppol_code varchar(64) DEFAULT NULL");
	//https://docs.peppol.eu/poacc/billing/3.0/codelist/UNCL7161/
	gks_run_sql("update gks_aade_katigoria_loipon_foron set loipon_foron_peppol_code='CAP' where id_aade_katigoria_loipon_foron=1"); // Insurance brokerage service α1) ασφάλιστρα κλάδου πυρός 20%
	gks_run_sql("update gks_aade_katigoria_loipon_foron set loipon_foron_peppol_code='CAP' where id_aade_katigoria_loipon_foron=2"); // Insurance brokerage service α2) ασφάλιστρα κλάδου πυρός 20%
	gks_run_sql("update gks_aade_katigoria_loipon_foron set loipon_foron_peppol_code='CAP' where id_aade_katigoria_loipon_foron=3"); // Insurance brokerage service β) ασφάλιστρα κλάδου ζωής 4%
	gks_run_sql("update gks_aade_katigoria_loipon_foron set loipon_foron_peppol_code='CAP' where id_aade_katigoria_loipon_foron=4"); // Insurance brokerage service γ) ασφάλιστρα λοιπών κλάδων 15%
	gks_run_sql("update gks_aade_katigoria_loipon_foron set loipon_foron_peppol_code='CAP' where id_aade_katigoria_loipon_foron=5"); // Insurance brokerage service δ) απαλλασσόμενα φόρου ασφαλίστρων 0%
	gks_run_sql("update gks_aade_katigoria_loipon_foron set loipon_foron_peppol_code='AEF' where id_aade_katigoria_loipon_foron=6"); // Rents and leases Ξενοδοχεία 1-2 αστέρων 0,50 €
	gks_run_sql("update gks_aade_katigoria_loipon_foron set loipon_foron_peppol_code='AEF' where id_aade_katigoria_loipon_foron=7"); // Rents and leases Ξενοδοχεία 3 αστέρων 1,50 €
	gks_run_sql("update gks_aade_katigoria_loipon_foron set loipon_foron_peppol_code='AEF' where id_aade_katigoria_loipon_foron=8"); // Rents and leases Ξενοδοχεία 4 αστέρων 3,00 €
	gks_run_sql("update gks_aade_katigoria_loipon_foron set loipon_foron_peppol_code='AEF' where id_aade_katigoria_loipon_foron=9"); // Rents and leases Ξενοδοχεία 4 αστέρων 4,00 €
	gks_run_sql("update gks_aade_katigoria_loipon_foron set loipon_foron_peppol_code='AEF' where id_aade_katigoria_loipon_foron=10"); // Rents and leases Ενοικιαζόμενα - επιπλωμένα δωμάτια - διαμερίσματα 0,50 €
	gks_run_sql("update gks_aade_katigoria_loipon_foron set loipon_foron_peppol_code='AA'  where id_aade_katigoria_loipon_foron=11"); // Advertising Ειδικός Φόρος στις διαφημίσεις που προβάλλονται από την τηλεόραση (ΕΦΤΔ) 5%
	gks_run_sql("update gks_aade_katigoria_loipon_foron set loipon_foron_peppol_code='ABK' where id_aade_katigoria_loipon_foron=12"); // Miscellaneous 3.1 Φόρος πολυτελείας 10% επί της φορολογητέας αξίας για τα ενδοκοινοτικώς αποκτούμενα και εισαγόμενα από τρίτες χώρες 10%
	gks_run_sql("update gks_aade_katigoria_loipon_foron set loipon_foron_peppol_code='ABK' where id_aade_katigoria_loipon_foron=13"); // Miscellaneous 3.2 Φόρος πολυτελείας 10% επί της τιμής πώλησης προ Φ.Π.Α. για τα εγχωρίως παραγόμενα είδη 10%
	gks_run_sql("update gks_aade_katigoria_loipon_foron set loipon_foron_peppol_code='ABK' where id_aade_katigoria_loipon_foron=14"); // Miscellaneous Δικαίωμα του Δημοσίου στα εισιτήρια των καζίνο (80% επί του εισιτηρίου)
	gks_run_sql("update gks_aade_katigoria_loipon_foron set loipon_foron_peppol_code='CAP' where id_aade_katigoria_loipon_foron=15"); // Insurance brokerage service Ασφάλιστρα κλάδου πυρός 20%
	gks_run_sql("update gks_aade_katigoria_loipon_foron set loipon_foron_peppol_code='ABK' where id_aade_katigoria_loipon_foron=16"); // Miscellaneous Λοιποί Τελωνειακοί Δασμοί-Φόροι
	gks_run_sql("update gks_aade_katigoria_loipon_foron set loipon_foron_peppol_code='ABK' where id_aade_katigoria_loipon_foron=17"); // Miscellaneous Λοιποί Φόροι
	gks_run_sql("update gks_aade_katigoria_loipon_foron set loipon_foron_peppol_code='ABK' where id_aade_katigoria_loipon_foron=18"); // Miscellaneous Επιβαρύνσεις Λοιπών Φόρων
	gks_run_sql("update gks_aade_katigoria_loipon_foron set loipon_foron_peppol_code='ABK' where id_aade_katigoria_loipon_foron=19"); // Miscellaneous ΕΦΚ 
}

$sql="select telon_peppol_code from gks_aade_katigoria_telon limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_aade_katigoria_telon
	add column telon_peppol_code varchar(64) DEFAULT NULL");
  //https://docs.peppol.eu/poacc/billing/3.0/codelist/UNCL7161/
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='AAA' where id_aade_katigoria_telon=1"); // Telecommunication Για μηνιαίο λογαριασμό μέχρι και 50 ευρώ 12%
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='AAA' where id_aade_katigoria_telon=2"); // TelecommunicationΓια μηνιαίο λογαριασμό από 50,01 μέχρι και 100 ευρώ 15%
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='AAA' where id_aade_katigoria_telon=3"); // Telecommunication Για μηνιαίο λογαριασμό από 100,01 μέχρι και 150 ευρώ 18%
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='AAA' where id_aade_katigoria_telon=4"); // Telecommunication Για μηνιαίο λογαριασμό από 150,01 ευρώ και άνω 20%
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='AAA' where id_aade_katigoria_telon=5"); // Telecommunication Τέλος καρτοκινητής επί της αξίας του χρόνου ομιλίας (12%)
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='AAA' where id_aade_katigoria_telon=6"); // Telecommunication Τέλος στη συνδρομητική τηλεόραση 10%
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='AAA' where id_aade_katigoria_telon=7"); // Telecommunication Τέλος συνδρομητών σταθερής τηλεφωνίας 5%
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='AEV' where id_aade_katigoria_telon=8"); // Environmental protection service Περιβαλλοντικό Τέλος & πλαστικής σακούλας ν. 2339/2001 αρ. 6α 0,07 ευρώ ανά τεμάχιο
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='ACF' where id_aade_katigoria_telon=9"); // Miscellaneous treatment Εισφορά δακοκτονίας 2%
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='ABK' where id_aade_katigoria_telon=10"); // Miscellaneous Λοιπά τέλη
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='ABK' where id_aade_katigoria_telon=11"); // Miscellaneous Τέλη Λοιπών Φόρων
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='ACF' where id_aade_katigoria_telon=12"); // Miscellaneous treatment Εισφορά δακοκτονίας (ποσό)
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='AAA' where id_aade_katigoria_telon=13"); // Telecommunication Για μηνιαίο λογαριασμό κάθε σύνδεσης (10%)
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='AAA' where id_aade_katigoria_telon=14"); // Telecommunication Τέλος καρτοκινητής επί της αξίας του χρόνου ομιλίας (10%)
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='AAA' where id_aade_katigoria_telon=15"); // Telecommunication Τέλος κινητής και καρτοκινητής για φυσικά πρόσωπα ηλικίας 15 έως και 29 ετών (0%)
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='AEV' where id_aade_katigoria_telon=16"); // Environmental protection service Εισφορά προστασίας περιβάλλοντος πλαστικών προϊόντων 0,04 λεπτά ανά τεμάχιο [άρθρο 4 ν. 4736/2020]
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='AEV' where id_aade_katigoria_telon=17"); // Environmental protection service Τέλος ανακύκλωσης 0,08 λεπτά ανά τεμάχιο [άρθρο 80 ν. 4819/2021]
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='ABK' where id_aade_katigoria_telon=18"); // Miscellaneous Τέλος διαμονής παρεπιδημούντων
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='ABK' where id_aade_katigoria_telon=19"); // Miscellaneous Τέλος επί των ακαθάριστων εσόδων των εστιατορίων και συναφών καταστημάτων
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='ABK' where id_aade_katigoria_telon=20"); // Miscellaneous Τέλος επί των ακαθάριστων εσόδων των κέντρων διασκέδασης
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='ABK' where id_aade_katigoria_telon=21"); // Miscellaneous Τέλος επί των ακαθάριστων εσόδων των καζίνο
	gks_run_sql("update gks_aade_katigoria_telon set telon_peppol_code='ABK' where id_aade_katigoria_telon=22"); // Miscellaneous Λοιπά τέλη επί των ακαθάριστων εσόδων


}
$sql="select xartosimou_peppol_code from gks_aade_katigoria_xartosimou limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_aade_katigoria_xartosimou
	add column xartosimou_peppol_code varchar(64) DEFAULT NULL");
  //https://docs.peppol.eu/poacc/billing/3.0/codelist/UNCL7161/
	gks_run_sql("update gks_aade_katigoria_xartosimou set xartosimou_peppol_code='SAE'  where id_aade_katigoria_xartosimou=1"); // Stamping Συντελεστής 1,2 %
	gks_run_sql("update gks_aade_katigoria_xartosimou set xartosimou_peppol_code='SAE'  where id_aade_katigoria_xartosimou=2"); // Stamping Συντελεστής 2,4 %
	gks_run_sql("update gks_aade_katigoria_xartosimou set xartosimou_peppol_code='SAE'  where id_aade_katigoria_xartosimou=3"); // Stamping Συντελεστής 3,6 %
	gks_run_sql("update gks_aade_katigoria_xartosimou set xartosimou_peppol_code='SAE'  where id_aade_katigoria_xartosimou=4"); // Stamping Λοιπές περιπτώσεις Χαρτοσήμου
}



$sql="select fpa_ejeresi_peppol_code from gks_aade_katigoria_fpa_ejeresi limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_aade_katigoria_fpa_ejeresi
	add column fpa_ejeresi_peppol_code varchar(64) DEFAULT NULL");
  
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-132-1F'  where id_aade_katigoria_fpa_ejeresi=1"); // Χωρίς ΦΠΑ - άρθρο 2 και 3 του Κώδικα ΦΠΑ (Αντικείμενο του φόρου - Υποκείμενοι στο φόρο)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-79-C'    where id_aade_katigoria_fpa_ejeresi=2"); // Χωρίς ΦΠΑ - άρθρο 5 του Κώδικα ΦΠΑ (Παράδοση αγαθών)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-143'     where id_aade_katigoria_fpa_ejeresi=3"); // Χωρίς ΦΠΑ - άρθρο 13 του Κώδικα ΦΠΑ (Τόπος παράδοσης αγαθών)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-143'     where id_aade_katigoria_fpa_ejeresi=4"); // Χωρίς ΦΠΑ - άρθρο 14 του Κώδικα ΦΠΑ (Τόπος παροχής υπηρεσιών)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-143'     where id_aade_katigoria_fpa_ejeresi=5"); // Χωρίς ΦΠΑ - άρθρο 16 του Κώδικα ΦΠΑ (Χρόνος γένεσης της φορολογικής υποχρέωσης στην παράδοση αγαθών και στην παροχή υπηρεσιώ)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-143'     where id_aade_katigoria_fpa_ejeresi=6"); // Χωρίς ΦΠΑ - άρθρο 19 του Κώδικα ΦΠΑ (Φορολογητέα αξία στην παράδοση αγαθών, στην ενδοκοινοτική απόκτηση αγαθών και στην παροχή υπηρεσιών)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-132'     where id_aade_katigoria_fpa_ejeresi=7"); // Χωρίς ΦΠΑ - άρθρο 22 του Κώδικα ΦΠΑ (Απαλλαγές στο εσωτερικό της χώρας)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-143-1B'  where id_aade_katigoria_fpa_ejeresi=8"); // Χωρίς ΦΠΑ - άρθρο 24 του Κώδικα ΦΠΑ (Απαλλαγές των πράξεων κατά την εξαγωγή, εκτός Κοινότητας, των εξομοιούμενων προς αυτές πράξεων και των διεθνών μεταφορών)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-143-1C'  where id_aade_katigoria_fpa_ejeresi=9"); // Χωρίς ΦΠΑ - άρθρο 25 του Κώδικα ΦΠΑ (Απαλλαγές στη διεθνή διακίνηση αγαθών)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-148'     where id_aade_katigoria_fpa_ejeresi=10"); // Χωρίς ΦΠΑ - άρθρο 26 του Κώδικα ΦΠΑ (Απαλλαγές στο καθεστώς των φορολογικών αποθηκών, άλλων από αυτές του Ν. 2960/2001)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-148'     where id_aade_katigoria_fpa_ejeresi=11"); // Χωρίς ΦΠΑ - άρθρο 27 του Κώδικα ΦΠΑ (Ειδικές Απαλλαγές)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-148-A'   where id_aade_katigoria_fpa_ejeresi=12"); // Χωρίς ΦΠΑ - άρθρο 27 - Πλοία Ανοικτής Θαλάσσης του Κώδικα ΦΠΑ (Ειδικές Απαλλαγές)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-148-A'   where id_aade_katigoria_fpa_ejeresi=13"); // Χωρίς ΦΠΑ - άρθρο 27.1.γ - Πλοία Ανοικτής Θαλάσσης του Κώδικα ΦΠΑ
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-132'     where id_aade_katigoria_fpa_ejeresi=14"); // Χωρίς ΦΠΑ - άρθρο 28 του Κώδικα ΦΠΑ (Απαλλαγές στην παράδοση αγαθών σε άλλο κράτος-μέλος)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-143'     where id_aade_katigoria_fpa_ejeresi=15"); // Χωρίς ΦΠΑ - άρθρο 39 του Κώδικα ΦΠΑ (Ειδικό καθεστώς μικρών επιχειρήσεων)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-143'     where id_aade_katigoria_fpa_ejeresi=16"); // Χωρίς ΦΠΑ - άρθρο 39α του Κώδικα ΦΠΑ (Ειδικό καθεστώς καταβολής του φόρου από τον λήπτη αγαθών και υπηρεσιών)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-148'     where id_aade_katigoria_fpa_ejeresi=17"); // Χωρίς ΦΠΑ - άρθρο 40 του Κώδικα ΦΠΑ (Ειδικό καθεστώς κατ' αποκοπή καταβολής του φόρου)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-143'     where id_aade_katigoria_fpa_ejeresi=18"); // Χωρίς ΦΠΑ - άρθρο 41 του Κώδικα ΦΠΑ (Ειδικό καθεστώς αγροτών)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-143-1K'  where id_aade_katigoria_fpa_ejeresi=19"); // Χωρίς ΦΠΑ - άρθρο 47 του Κώδικα ΦΠΑ (Ειδικό καθεστώς επενδυτικού χρυσού)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-D'       where id_aade_katigoria_fpa_ejeresi=20"); // ΦΠΑ εμπεριεχόμενος - άρθρο 43 του Κώδικα ΦΠΑ (Ειδικό καθεστώς πρακτορείων ταξιδιών)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-143'     where id_aade_katigoria_fpa_ejeresi=21"); // ΦΠΑ εμπεριεχόμενος - άρθρο 44 του Κώδικα ΦΠΑ (Ειδικό καθεστώς φορολογίας βιομηχανοποιημένων καπνών)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-F'       where id_aade_katigoria_fpa_ejeresi=22"); // ΦΠΑ εμπεριεχόμενος - άρθρο 45 του Κώδικα ΦΠΑ (Ειδικό καθεστώς φορολογίας των υποκειμένων στο φόρο μεταπωλητών που παραδίδουν μεταχειρισμένα αγαθά και αντικείμενα καλλιτεχνικής, συλλεκτικής ή αρχαι...)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-J'       where id_aade_katigoria_fpa_ejeresi=23"); // ΦΠΑ εμπεριεχόμενος - άρθρο 46 του Κώδικα ΦΠΑ (Ειδικό καθεστώς φορολογίας για τις πωλήσεις σε δημοπρασία)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-143'     where id_aade_katigoria_fpa_ejeresi=24"); // Χωρίς ΦΠΑ - άρθρο 6 του Κώδικα ΦΠΑ (Παράδοση ακινήτων)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-143-1B'  where id_aade_katigoria_fpa_ejeresi=25"); // Χωρίς ΦΠΑ - ΠΟΛ.1029/1995
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-143'     where id_aade_katigoria_fpa_ejeresi=26"); // Χωρίς ΦΠΑ - ΠΟΛ.1167/2015
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-143'     where id_aade_katigoria_fpa_ejeresi=27"); // Λοιπές Εξαιρέσεις ΦΠΑ
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-143-1B'  where id_aade_katigoria_fpa_ejeresi=28"); // Χωρίς ΦΠΑ – άρθρο 24 περ. β' παρ.1 του Κώδικα ΦΠΑ, (Tax Free)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-143'     where id_aade_katigoria_fpa_ejeresi=29"); // Χωρίς ΦΠΑ – άρθρο 47β, του Κώδικα ΦΠΑ (OSS μη ενωσιακό καθεστώς)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-132'     where id_aade_katigoria_fpa_ejeresi=30"); // Χωρίς ΦΠΑ – άρθρο 47γ, του Κώδικα ΦΠΑ (OSS ενωσιακό καθεστώς)
	gks_run_sql("update gks_aade_katigoria_fpa_ejeresi set fpa_ejeresi_peppol_code='VATEX-EU-143-1C'  where id_aade_katigoria_fpa_ejeresi=31"); // Χωρίς ΦΠΑ – άρθρο 47δ του Κώδικα ΦΠΑ (IOSS)

}





$sql="select parakrat_peppol_code from gks_aade_katigoria_parakratoumemenon_foron limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_aade_katigoria_parakratoumemenon_foron
	add column parakrat_peppol_code varchar(64) DEFAULT NULL");
  
	gks_run_sql("update gks_aade_katigoria_parakratoumemenon_foron set parakrat_peppol_code='104'  where id_aade_katigoria_parakratoumemenon_foron=1"); // Standard Περιπτ. β’- Τόκοι - 15%
	gks_run_sql("update gks_aade_katigoria_parakratoumemenon_foron set parakrat_peppol_code='104'  where id_aade_katigoria_parakratoumemenon_foron=2"); // Standard Περιπτ. γ’ - Δικαιώματα - 20%
	gks_run_sql("update gks_aade_katigoria_parakratoumemenon_foron set parakrat_peppol_code='104'  where id_aade_katigoria_parakratoumemenon_foron=3"); // Standard Περιπτ. δ’ - Αμοιβές Συμβουλών Διοίκησης - 20%
	gks_run_sql("update gks_aade_katigoria_parakratoumemenon_foron set parakrat_peppol_code='104'  where id_aade_katigoria_parakratoumemenon_foron=4"); // Standard Περιπτ. δ’ - Τεχνικά Έργα - 3%
	gks_run_sql("update gks_aade_katigoria_parakratoumemenon_foron set parakrat_peppol_code='104'  where id_aade_katigoria_parakratoumemenon_foron=5"); // Standard Υγρά καύσιμα και προϊόντα καπνοβιομηχανίας 1%
	gks_run_sql("update gks_aade_katigoria_parakratoumemenon_foron set parakrat_peppol_code='104'  where id_aade_katigoria_parakratoumemenon_foron=6"); // Standard Λοιπά Αγαθά 4%
	gks_run_sql("update gks_aade_katigoria_parakratoumemenon_foron set parakrat_peppol_code='104'  where id_aade_katigoria_parakratoumemenon_foron=7"); // Standard Παροχή Υπηρεσιών 8%
	gks_run_sql("update gks_aade_katigoria_parakratoumemenon_foron set parakrat_peppol_code='104'  where id_aade_katigoria_parakratoumemenon_foron=8"); // Standard Προκαταβλητέος Φόρος Αρχιτεκτόνων και Μηχανικών επί Συμβατικών Αμοιβών, για Εκπόνηση Μελετών και Σχεδίων 4%
	gks_run_sql("update gks_aade_katigoria_parakratoumemenon_foron set parakrat_peppol_code='104'  where id_aade_katigoria_parakratoumemenon_foron=9"); // Standard Προκαταβλητέος Φόρος Αρχιτεκτόνων και Μηχανικών επί Συμβατικών Αμοιβών, που αφορούν οποιασδήποτε άλλης φύσης έργα 10%
	gks_run_sql("update gks_aade_katigoria_parakratoumemenon_foron set parakrat_peppol_code='104'  where id_aade_katigoria_parakratoumemenon_foron=10"); // Standard Προκαταβλητέος Φόρος στις Αμοιβές Δικηγόρων 15%
	gks_run_sql("update gks_aade_katigoria_parakratoumemenon_foron set parakrat_peppol_code='104'  where id_aade_katigoria_parakratoumemenon_foron=11"); // Standard Παρακράτηση Φόρου Μισθωτών Υπηρεσιών παρ. 1 αρ. 15 ν. 4172/2013
	gks_run_sql("update gks_aade_katigoria_parakratoumemenon_foron set parakrat_peppol_code='104'  where id_aade_katigoria_parakratoumemenon_foron=12"); // Standard Παρακράτηση Φόρου Μισθωτών Υπηρεσιών παρ. 2 αρ. 15 ν. 4172/2013 - Αξιωματικών Εμπορικού Ναυτικού
	gks_run_sql("update gks_aade_katigoria_parakratoumemenon_foron set parakrat_peppol_code='104'  where id_aade_katigoria_parakratoumemenon_foron=13"); // Standard Παρακράτηση Φόρου Μισθωτών Υπηρεσιών παρ. 2 αρ. 15 ν. 4172/2013 - Κατώτερο Πλήρωμα Εμπορικού Ναυτικού
	gks_run_sql("update gks_aade_katigoria_parakratoumemenon_foron set parakrat_peppol_code='104'  where id_aade_katigoria_parakratoumemenon_foron=14"); // Standard Παρακράτηση Ειδικής Εισφοράς Αλληλεγγύης
	gks_run_sql("update gks_aade_katigoria_parakratoumemenon_foron set parakrat_peppol_code='104'  where id_aade_katigoria_parakratoumemenon_foron=15"); // Standard Παρακράτηση Φόρου Αποζημίωσης λόγω Διακοπής Σχέσης Εργασίας παρ. 3 αρ. 15 ν. 4172/2013
	gks_run_sql("update gks_aade_katigoria_parakratoumemenon_foron set parakrat_peppol_code='104'  where id_aade_katigoria_parakratoumemenon_foron=16"); // Standard Παρακρατήσεις συναλλαγών αλλοδαπής βάσει συμβάσεων αποφυγής διπλής φορολογίας (Σ.Α.Δ.Φ.)
	gks_run_sql("update gks_aade_katigoria_parakratoumemenon_foron set parakrat_peppol_code='104'  where id_aade_katigoria_parakratoumemenon_foron=17"); // Standard Λοιπές Παρακρατήσεις Φόρου
	gks_run_sql("update gks_aade_katigoria_parakratoumemenon_foron set parakrat_peppol_code='104'  where id_aade_katigoria_parakratoumemenon_foron=18"); // Standard Παρακράτηση Φόρου Μερίσματα περ.α παρ. 1 αρ. 64 ν. 4172/2013 5%


}


$sql="select is_b2g from gks_users limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_users
	add column gemi_number varchar(190) DEFAULT NULL,
	add index gemi_number (gemi_number),
	add column is_b2g tinyint(4) NOT NULL DEFAULT 0,
	add index is_b2g (is_b2g),
	add column b2g_aaht_code varchar(190) DEFAULT NULL,
	add index b2g_aaht_code (b2g_aaht_code),
	add column b2g_aaht_name varchar(190) DEFAULT NULL,
	add column b2g_aaht_foreas varchar(190) DEFAULT NULL,
	add column b2g_aaht_typos_forea varchar(190) DEFAULT NULL,
	add column b2g_aaht_kodikos_ekatharisis varchar(190) DEFAULT NULL");

}



$sql="select contract_reference from gks_acc_inv limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_acc_inv
	add column contract_reference varchar(190) DEFAULT NULL,
	add column project_reference varchar(190) DEFAULT NULL,
	add index contract_reference (contract_reference),
	add index project_reference (project_reference)");
}

$sql="select pos_print_enable from gks_pos limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_pos
	add column pos_print_enable tinyint(4) NOT NULL DEFAULT 0 after def_products,
	add column pos_paroxos_send_pdf tinyint(4) NOT NULL DEFAULT 0 after pos_print_form_id");
	
	gks_run_sql("update gks_pos set pos_print_enable=1, pos_paroxos_send_pdf=1");
	
}

$sql="select paroxos_invoice_number from gks_acc_inv limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_acc_inv
	add column paroxos_invoice_number varchar(190) DEFAULT NULL");
}

$sql="select company_gemi_number from gks_company limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_company
	add column company_gemi_number varchar(190) DEFAULT NULL");
}


$sql="select * from gks_permission_object where id_permission_object=305";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (305,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση',0,'gks_aade','ΑΑΔΕ',3050)");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  )  AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",305,1,1,1,1,1)");
  }

}



$sql="select aade_disable from gks_aade_katigoria_telon limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_aade_katigoria_telon` 
  ADD COLUMN `aade_disable` TINYINT NOT NULL DEFAULT 0 AFTER `sortorder`,
  ADD INDEX `aade_disable`(`aade_disable`);");
}
$sql="select aade_disable from gks_aade_katigoria_parakratoumemenon_foron limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_aade_katigoria_parakratoumemenon_foron` 
  ADD COLUMN `aade_disable` TINYINT NOT NULL DEFAULT 0 AFTER `sortorder`,
  ADD INDEX `aade_disable`(`aade_disable`);");
}
$sql="select aade_disable from gks_aade_katigoria_xartosimou limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_aade_katigoria_xartosimou` 
  ADD COLUMN `aade_disable` TINYINT NOT NULL DEFAULT 0 AFTER `sortorder`,
  ADD INDEX `aade_disable`(`aade_disable`);");
}
$sql="select aade_disable from gks_aade_katigoria_fpa_ejeresi limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_aade_katigoria_fpa_ejeresi` 
  ADD COLUMN `aade_disable` TINYINT NOT NULL DEFAULT 0 AFTER `sortorder`,
  ADD INDEX `aade_disable`(`aade_disable`);");
}

$sql="select erp_app_dest_printer_method from gks_acc_seires limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_seires` 
  ADD COLUMN `erp_app_dest_printer_method` int(11) NOT NULL DEFAULT 0 AFTER `erp_app_dest_printer`,
  ADD COLUMN `erp_app_dest_printer_lpr_ip` varchar(190) DEFAULT NULL AFTER `erp_app_dest_printer_method`");
}


gks_run_sql("update gks_permission_object set object_name='gks ERP App Desktop' where id_permission_object=381 and object_name<>'gks ERP App Desktop'");


$sql="select tropos_pliromis_one_multi from gks_acc_inv limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_inv` 
  ADD COLUMN `tropos_pliromis_one_multi` int(11) NOT NULL DEFAULT 0 AFTER `kostos_pliromis_json`,
  add index tropos_pliromis_one_multi (tropos_pliromis_one_multi)");
}

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_acc_inv_payment` (
  `id_acc_inv_payment` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `myip` varchar(48) DEFAULT NULL,
  `acc_inv_id` int(11) NOT NULL DEFAULT 0,
  `pp` int(11) NOT NULL DEFAULT 0,
  `payment_acquirer_id` int(11) NOT NULL DEFAULT 0,
  `poso` double NOT NULL DEFAULT 0,
  `asset_id` int(11) NOT NULL DEFAULT 0,
  `transaction_pa_with_id` int(11) NOT NULL DEFAULT 0,
  `transaction_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_acc_inv_payment`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `acc_inv_id` (`acc_inv_id`),
  KEY `pp` (`pp`),
  KEY `payment_acquirer_id` (`payment_acquirer_id`),
  KEY `asset_id` (`asset_id`),
  KEY `transaction_pa_with_id` (`transaction_pa_with_id`),
  KEY `transaction_id` (`transaction_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");	
	  
$sql="select payment_acquirer_with_id from gks_payment_acquirers limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_payment_acquirers` 
  ADD COLUMN `payment_acquirer_with_id` int(11) NOT NULL DEFAULT 0 AFTER `aade_tropos_pliromis_id`,
  add index `payment_acquirer_with_id` (`payment_acquirer_with_id`)");
}

  
gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_payment_acquirer_with` (
  `id_payment_acquirer_with` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `myip` varchar(48) DEFAULT NULL,
  `payment_paroxos_name` varchar(190) DEFAULT NULL,
  `payment_paroxos_implemented` tinyint(4) NOT NULL DEFAULT 0,
  `payment_paroxos_sortorder` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_payment_acquirer_with`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `payment_paroxos_name` (`payment_paroxos_name`),
  KEY `payment_paroxos_implemented` (`payment_paroxos_implemented`),
  KEY `payment_paroxos_sortorder` (`payment_paroxos_sortorder`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");	

$sql="select * from gks_payment_acquirer_with where id_payment_acquirer_with<=3";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_payment_acquirer_with` (`id_payment_acquirer_with`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`payment_paroxos_name`,`payment_paroxos_implemented`,`payment_paroxos_sortorder`) VALUES 
   (1,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Viva',0,1),
   (2,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Meg EFT/POS Driver',0,2),
   (3,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Mellon',0,3)");
}
$sql="select * from gks_payment_acquirer_with where id_payment_acquirer_with=4";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_payment_acquirer_with` (`id_payment_acquirer_with`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`payment_paroxos_name`,`payment_paroxos_implemented`,`payment_paroxos_sortorder`) VALUES 
  (4,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Cardlink',0,4)");
}


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_eftpos_transaction` (
  `id_eftpos_transaction` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `myip` varchar(48) DEFAULT NULL,
  `transaction_type` varchar(64) DEFAULT NULL,
  `acc_inv_payment_id` int(11) NOT NULL DEFAULT 0,
  `payment_acquirer_with_id` int(11) NOT NULL DEFAULT 0,
  `payment_acquirer_id` int(11) NOT NULL DEFAULT 0,
  `aade_paroxos_id` int(11) NOT NULL DEFAULT 0,
  `asset_id` int(11) NOT NULL DEFAULT 0,
  `terminalId` varchar(190) DEFAULT NULL,
  `transaction_status` varchar(64) DEFAULT NULL,
  `amount` double NOT NULL DEFAULT 0,
  `sessionId` varchar(190) DEFAULT NULL,
  `cashRegisterId` varchar(190) DEFAULT NULL,
  `merchantReference` varchar(190) DEFAULT NULL,
  `customerTrns` varchar(190) DEFAULT NULL,
  `tipAmount` double NOT NULL DEFAULT 0, 
  `aadeProviderId` varchar(190) DEFAULT NULL,
  `aadeProviderSignatureData` text DEFAULT NULL,
  `aadeProviderSignature` text DEFAULT NULL,
  `transactionId` varchar(190) DEFAULT NULL,
  `response_array` longtext DEFAULT NULL,
  `remote_id` int(11) NOT NULL DEFAULT 0,
  
  PRIMARY KEY (`id_eftpos_transaction`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `transaction_type` (`transaction_type`),
  KEY `acc_inv_payment_id` (`acc_inv_payment_id`),
  KEY `payment_acquirer_with_id` (`payment_acquirer_with_id`),
  KEY `payment_acquirer_id` (`payment_acquirer_id`),
  KEY `aade_paroxos_id` (`aade_paroxos_id`),
  KEY `asset_id` (`asset_id`),
  KEY `terminalId` (`terminalId`),
  KEY `transaction_status` (`transaction_status`),
  KEY `sessionId` (`sessionId`),
  KEY `cashRegisterId` (`cashRegisterId`),
  KEY `merchantReference` (`merchantReference`),
  KEY `transactionId` (`transactionId`),
  KEY `customerTrns` (`customerTrns`),
  KEY `remote_id` (`remote_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");	



$sql="select viva_pos_client_id from gks_company limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_company` 
  ADD COLUMN `viva_pos_client_id` varchar(190) DEFAULT NULL,
  ADD COLUMN `viva_pos_client_secret` varchar(190) DEFAULT NULL");
}




gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_company_eftpos` (
  `id_company_eftpos` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `company_id` int(11) NOT NULL DEFAULT 0,
  `company_sub_id` int(11) NOT NULL DEFAULT 0,
  `payment_acquirer_with_id` int(11) NOT NULL DEFAULT 0,
  `pc_token_id` text DEFAULT NULL,
  `pc_token_expiration` datetime DEFAULT NULL,
  `pc_refresh_token_id` text DEFAULT NULL,
  `pc_refresh_token_expiration` datetime DEFAULT NULL,

  PRIMARY KEY (`id_company_eftpos`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `company_id` (`company_id`),
  KEY `company_sub_id` (`company_sub_id`),
  KEY `payment_acquirer_with_id` (`payment_acquirer_with_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_company_eftpos_log` (
  `id_company_eftpos_log` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `acc_inv_id` int(11) NOT NULL DEFAULT 0,
  `acc_pay_id` int(11) NOT NULL DEFAULT 0,
  `payment_acquirer_with_id` int(11) NOT NULL DEFAULT 0,
  `p_send` longtext DEFAULT NULL,
  `p_response` longtext DEFAULT NULL,
  PRIMARY KEY (`id_company_eftpos_log`),
  KEY `acc_inv_id` (`acc_inv_id`),
  KEY `acc_pay_id` (`acc_pay_id`),
  KEY `payment_acquirer_with_id` (`payment_acquirer_with_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select seira_need_signature from gks_acc_seires limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_seires` 
  ADD COLUMN `seira_need_signature` tinyint(4) NOT NULL DEFAULT 0,
  add index seira_need_signature (seira_need_signature)");
}


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_acc_seires_paymentacquirers` (
  `id_seira_payacq` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `acc_seira_id` int(11) NOT NULL DEFAULT 0,
  `payment_acquirer_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_seira_payacq`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `acc_seira_id` (`acc_seira_id`),
  KEY `payment_acquirer_id` (`payment_acquirer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_paroxos_signature` (
  `id_paroxos_signature` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `aade_paroxos_id` int(11) NOT NULL DEFAULT 0,
  `acc_inv_id` int(11) NOT NULL DEFAULT 0,
  `acc_pay_id` int(11) NOT NULL DEFAULT 0,
  `acc_inv_payment_id` int(11) NOT NULL DEFAULT 0,
  `payment_acquirer_with_id` int(11) NOT NULL DEFAULT 0,
  `payment_acquirer_id` int(11) NOT NULL DEFAULT 0,
  `signature_status` varchar(190) DEFAULT NULL,
  `asset_id` int(11) NOT NULL DEFAULT 0,
  `s_terminalId` varchar(190) DEFAULT NULL,
  `s_amount` double NOT NULL DEFAULT 0,
  `s_netAmount` double NOT NULL DEFAULT 0,
  `s_vatAmount` double NOT NULL DEFAULT 0,
  `s_grossAmount` double NOT NULL DEFAULT 0,

  `r_signingAuthor` varchar(64) DEFAULT NULL,
  `r_amount` double NOT NULL DEFAULT 0,
  `r_signatureExpirationDate` BIGINT NOT NULL DEFAULT 0,
  `r_netAmount` double NOT NULL DEFAULT 0,
  `r_signature` varchar(512) DEFAULT NULL,
  `r_vatRate` double NOT NULL DEFAULT 0,
  `r_grossAmount` double NOT NULL DEFAULT 0,
  `r_terminalId` varchar(190) DEFAULT NULL,
  `r_signedContent` varchar(190) DEFAULT NULL,
  `r_vatAmount` double NOT NULL DEFAULT 0,
  `r_sellerVat` varchar(190) DEFAULT NULL,
  `r_uid` varchar(190) DEFAULT NULL,
  `r_sellerBranch` int(11) NOT NULL DEFAULT 0,
  `r_serial` varchar(190) DEFAULT NULL,
  `r_series` varchar(190) DEFAULT NULL,
  `r_uidHash` varchar(190) DEFAULT NULL,
  `r_signaturePublicKey` varchar(512) DEFAULT NULL,
  `r_signedAt` BIGINT NOT NULL DEFAULT 0,
  `r_invoiceTypeCode` varchar(190) DEFAULT NULL,
  `r_nspProtocol` varchar(190) DEFAULT NULL,
  `response` longtext DEFAULT NULL,
  PRIMARY KEY (`id_paroxos_signature`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `aade_paroxos_id` (`aade_paroxos_id`),
  KEY `acc_inv_id` (`acc_inv_id`),
  KEY `acc_pay_id` (`acc_pay_id`),
  KEY `acc_inv_payment_id` (`acc_inv_payment_id`),
  KEY `payment_acquirer_with_id` (`payment_acquirer_with_id`),
  KEY `payment_acquirer_id` (`payment_acquirer_id`),
  KEY `signature_status` (`signature_status`),
  KEY `asset_id` (`asset_id`),
  KEY `s_terminalId` (`s_terminalId`),
  KEY `r_signingAuthor` (`r_signingAuthor`),
  KEY `r_signatureExpirationDate` (`r_signatureExpirationDate`)
  
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");	






$sql="select viva_company_id from gks_assets limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_assets` 
  ADD COLUMN `viva_company_id` int(11) NOT NULL DEFAULT 0 after asset_thesi");

}

$sql="select cardlink_terminal_id from gks_assets limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_assets` 
  ADD COLUMN `cardlink_terminal_id` varchar(190) DEFAULT NULL,
  ADD COLUMN `cardlink_static_ip` varchar(190) DEFAULT NULL,
  ADD COLUMN `cardlink_port` varchar(190) DEFAULT NULL,
  ADD COLUMN `cardlink_ecr2eftweb_erp_app_id` int(11) NOT NULL DEFAULT 0,
  ADD COLUMN `cardlink_ecr2eftweb_service_url` varchar(190) DEFAULT NULL,
  add index cardlink_terminal_id (cardlink_terminal_id)");
}



$sql="select my_uniqueTxnId from gks_eftpos_transaction limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_eftpos_transaction` 
  ADD COLUMN `my_uniqueTxnId` varchar(190) DEFAULT NULL after transaction_status ,
  add index my_uniqueTxnId (my_uniqueTxnId),
  ADD COLUMN `send_array` longtext DEFAULT NULL,
  ADD COLUMN mymessage text DEFAULT NULL,
  ADD COLUMN xxx_transaction_id int(11) NOT NULL DEFAULT 0,
  add index xxx_transaction_id (xxx_transaction_id),
  ADD COLUMN company_id int(11) NOT NULL DEFAULT 0,
  ADD COLUMN xeiristis_id int(11) NOT NULL DEFAULT 0,
  add index company_id (company_id),
  add index xeiristis_id (xeiristis_id)");
}

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_cardlink_transaction` (
  `id_cardlink_transaction` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `xeiristis_id` int(11) DEFAULT NULL,
  `add_from_system` varchar(64) DEFAULT NULL,
  `myfrom` varchar(64) DEFAULT NULL,

  `msgType` varchar(64) DEFAULT NULL,
  `paymentSpecs` varchar(64) DEFAULT NULL,
  `authorizationCode` varchar(64) DEFAULT NULL,
  `city` varchar(190) DEFAULT NULL,
  `msgOptions` varchar(190) DEFAULT NULL,
  `amountPayable` double DEFAULT NULL,
  `mid` varchar(64) DEFAULT NULL,
  `responseCodeMessage` varchar(190) DEFAULT NULL,
  `cardExpiryDate` varchar(64) DEFAULT NULL,
  `txnDateTime` varchar(190) DEFAULT NULL,
  `responseCode` varchar(64) DEFAULT NULL,
  `merchantName` varchar(190) DEFAULT NULL,
  `uniquePaymentIdECR` varchar(190) DEFAULT NULL,
  `referenceNumber` varchar(64) DEFAULT NULL,
  `sn` varchar(64) DEFAULT NULL,
  `msgCode` varchar(64) DEFAULT NULL,
  `acquirerName` varchar(190) DEFAULT NULL,
  `applicationName` varchar(190) DEFAULT NULL,
  `batchNumber` varchar(64) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `address` varchar(190) DEFAULT NULL,
  `eftTerminalId` varchar(64) DEFAULT NULL,
  `length` varchar(64) DEFAULT NULL,
  `cardType` varchar(190) DEFAULT NULL,
  `sessionId` varchar(190) DEFAULT NULL,
  `numberOfPostdatedInstallments` varchar(64) DEFAULT NULL,
  `accountNumber` varchar(190) DEFAULT NULL,
  `tc` varchar(190) DEFAULT NULL,
  `token` varchar(190) DEFAULT NULL,
  `transactionType` varchar(64) DEFAULT NULL,
  `bankId` varchar(64) DEFAULT NULL,
  `go4moreProducts` varchar(190) DEFAULT NULL,
  `uniquePaymentId` varchar(190) DEFAULT NULL,
  `tlvData` varchar(190) DEFAULT NULL,
  `numberOfInstallments` varchar(190) DEFAULT NULL,
  `phone` varchar(64) DEFAULT NULL,
  `posTerminalVersion` varchar(64) DEFAULT NULL,
  `aid` varchar(190) DEFAULT NULL,
  `signature` varchar(190) DEFAULT NULL,
  `cashierNumber` varchar(190) DEFAULT NULL,
  `invoiceNumber` varchar(190) DEFAULT NULL,
  `tillNumber` varchar(190) DEFAULT NULL,
  `agreementDate` varchar(190) DEFAULT NULL,
  `agreementNumber` varchar(190) DEFAULT NULL,
  `checkinDate` varchar(190) DEFAULT NULL,
  `checkoutDate` varchar(190) DEFAULT NULL,
  `roomNumber` varchar(190) DEFAULT NULL,
  `ecrToken` varchar(190) DEFAULT NULL,
  
  `myerror` varchar(190) DEFAULT NULL,

  PRIMARY KEY (`id_cardlink_transaction`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `xeiristis_id` (`xeiristis_id`),
  KEY `add_from_system` (`add_from_system`),
  KEY `myfrom` (`myfrom`),
  KEY `msgType` (`msgType`),
  KEY `mid` (`mid`),
  KEY `responseCodeMessage` (`responseCodeMessage`),
  KEY `responseCode` (`responseCode`),
  KEY `referenceNumber` (`referenceNumber`),
  KEY `eftTerminalId` (`eftTerminalId`),
  KEY `amountPayable` (`amountPayable`),
  KEY `amount` (`amount`),
  KEY `myerror` (`myerror`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");




$sql="select * from gks_permission_object where id_permission_object=851";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (851,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Λογιστική',0,'gks_cardlink_transaction','Συναλλαγές Cardlink',8540)");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  )  AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",851,1,1,1,1,1)");
  }
}


$sql="select mellon_username from gks_company limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_company` 
  ADD COLUMN `mellon_username` varchar(190) DEFAULT NULL,
  ADD COLUMN `mellon_password` varchar(190) DEFAULT NULL,
  ADD COLUMN `mellon_authorization_code` varchar(190) DEFAULT NULL,
  ADD COLUMN `mellon_x_api_key` varchar(190) DEFAULT NULL");
}



$sql="select mellon_id from gks_assets limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_assets` 
  ADD COLUMN `mellon_company_id` int(11) NOT NULL DEFAULT 0,
  ADD COLUMN `mellon_id` varchar(190) DEFAULT NULL,
  ADD COLUMN `mellon_terminal_id` varchar(190) DEFAULT NULL,
  add index mellon_id (mellon_id),
  add index mellon_terminal_id (mellon_terminal_id)");
}


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_mellon_transaction` (
  `id_mellon_transaction` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `xeiristis_id` int(11) DEFAULT NULL,
  `add_from_system` varchar(64) DEFAULT NULL,
  `myfrom` varchar(64) DEFAULT NULL,

  `TransactionId_org` varchar(190) DEFAULT NULL,
  `Id` varchar(190) DEFAULT NULL,
  `ExternalId` varchar(190) DEFAULT NULL,
  `TxnType` int(11) DEFAULT NULL,
  `Timestamp` datetime DEFAULT NULL,
  `VoidTimestamp` datetime DEFAULT NULL,
  `CardPAN` varchar(190) DEFAULT NULL,
  `CardHash` varchar(190) DEFAULT NULL,
  `Approved` tinyint(4) NOT NULL DEFAULT 0,
  `Voided` tinyint(4) NOT NULL DEFAULT 0,
  `STAN` int(11) DEFAULT NULL,
  `BatchNumber` int(11) DEFAULT NULL,
  `Batch` varchar(190) DEFAULT NULL,
  `Acquirer` varchar(190) DEFAULT NULL,
  `TID` varchar(190) DEFAULT NULL,
  `MID` varchar(190) DEFAULT NULL,
  `Amount` double DEFAULT NULL,
  `DccAmount` double DEFAULT NULL,
  `TipAmount` double DEFAULT NULL,
  `CashbackAmount` double DEFAULT NULL,
  `LoyaltyRedemptionAmount` double DEFAULT NULL,
  `Instalments` int(11) DEFAULT NULL,
  `PosEntryMode` int(11) DEFAULT NULL,
  `Cryptogram` varchar(190) DEFAULT NULL,
  `HostResponseCode` varchar(190) DEFAULT NULL,
  `RRN` varchar(190) DEFAULT NULL,
  `AuthCode` varchar(190) DEFAULT NULL,
  `OriginalRRN` varchar(190) DEFAULT NULL,
  `OriginalAuthCode` varchar(190) DEFAULT NULL,
  `CurrencyCode` int(11) DEFAULT NULL,
  `DccCurrencyCode` varchar(190) DEFAULT NULL,
  `CustomerReference` varchar(190) DEFAULT NULL,
  `CustomerEmail` varchar(190) DEFAULT NULL,
  `CustomerPhone` varchar(190) DEFAULT NULL,
  
  `myerror` varchar(190) DEFAULT NULL,
  `myjson` text DEFAULT NULL,
  PRIMARY KEY (`id_mellon_transaction`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `xeiristis_id` (`xeiristis_id`),
  KEY `add_from_system` (`add_from_system`),
  KEY `myfrom` (`myfrom`),
  KEY `Id` (`Id`),
  KEY `TID` (`TID`),
  KEY `MID` (`MID`),
  KEY `RRN` (`RRN`),
  KEY `OriginalRRN` (`OriginalRRN`),
  KEY `amount` (`amount`),
  KEY `myerror` (`myerror`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select * from gks_permission_object where id_permission_object=852";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (852,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Λογιστική',0,'gks_mellon_transaction','Συναλλαγές Mellon',8520)");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  )  AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",852,1,1,1,1,1)");
  }
}

gks_run_sql("update gks_permission_object set object_name='Εντατική Λιανική - Διαχείριση' where id_permission_object=683 and object_name='POS Διαχείριση';");
gks_run_sql("update gks_permission_object set object_name='Εντατική Λιανική - Λειτουργία' where id_permission_object=684 and object_name='POS Λειτουργία';");


$sql="select mellon_mid from gks_company limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_company
	add column mellon_mid varchar(190) DEFAULT NULL,
	add column cardlink_mid varchar(190) DEFAULT NULL");
}




$sql="select eftpos_transaction_id from gks_mellon_transaction limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_mellon_transaction` 
  ADD COLUMN `eftpos_transaction_id` int(11) DEFAULT NULL after odbc,
  ADD COLUMN intentId int(11) DEFAULT NULL after eftpos_transaction_id,
  ADD COLUMN myStatus tinyint(4) NOT NULL DEFAULT 0 after intentId,
  ADD COLUMN myResult tinyint(4) NOT NULL DEFAULT 0 after myStatus, 
  ADD index eftpos_transaction_id (eftpos_transaction_id),
  ADD index intentId (intentId),
  ADD index myStatus (myStatus),
  ADD index myResult (myResult)");

  gks_run_sql("ALTER TABLE `gks_cardlink_transaction` 
  ADD COLUMN `eftpos_transaction_id` int(11) DEFAULT NULL after odbc,
  ADD index eftpos_transaction_id (eftpos_transaction_id),
  ADD COLUMN `trans_status` varchar(64) DEFAULT NULL after eftpos_transaction_id,
  ADD index trans_status (trans_status),
  ADD COLUMN `trans_type` varchar(64) DEFAULT NULL after trans_status,
  ADD index trans_type (trans_type),
  ADD COLUMN mymessage text DEFAULT NULL");

  gks_run_sql("ALTER TABLE `gks_viva_transaction` 
  ADD COLUMN `eftpos_transaction_id` int(11) DEFAULT NULL after odbc,
  ADD index eftpos_transaction_id (eftpos_transaction_id)");
}


$sql="select * from gks_permission_object where id_permission_object=853";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (853,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Λογιστική',0,'gks_eftpos_transaction','Συναλλαγές EFT/POS',8490)");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  )  AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",853,1,1,1,1,1)");
  }
}


$sql="select mymessage from gks_viva_transaction limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_viva_transaction` 
  ADD COLUMN mymessage text DEFAULT NULL,
  MODIFY COLUMN `myfrom` VARCHAR(64) DEFAULT NULL,
  MODIFY COLUMN `StatusId` VARCHAR(64) DEFAULT NULL;");
}

gks_run_sql("update gks_aade_paroxos set paroxos_implemented=1,paroxos_need_username=1,paroxos_need_password=1 where id_aade_paroxos=8");



$sql="select erp_app_secret from gks_erp_app limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_erp_app` 
  ADD COLUMN erp_app_secret varchar(128) DEFAULT NULL,
  add index erp_app_secret (erp_app_secret)");
}



//Meg EFT/POS Driver
$sql="select megeftpos_terminal_id from gks_assets limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_assets` 
  ADD COLUMN `cardlink_company_id` int(11) NOT NULL DEFAULT 0 after asset_rental_status,
  ADD COLUMN `megeftpos_company_id` int(11) NOT NULL DEFAULT 0,
  ADD COLUMN `megeftpos_terminal_id` varchar(190) DEFAULT NULL,
  ADD COLUMN `megeftpos_static_ip` varchar(190) DEFAULT NULL,
  ADD COLUMN `megeftpos_port` varchar(190) DEFAULT NULL,
  ADD COLUMN `megeftpos_protocol` int(11) NOT NULL DEFAULT 0,
  ADD COLUMN `megeftpos_erp_app_id` int(11) NOT NULL DEFAULT 0,
  ADD COLUMN `megeftpos_ecr2eftweb_service_url` varchar(190) DEFAULT NULL,
  add index megeftpos_terminal_id (megeftpos_terminal_id)");
}



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_megeftpos_transaction` (
  `id_megeftpos_transaction` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `eftpos_transaction_id` int(11) DEFAULT NULL,
  `trans_status` varchar(64) DEFAULT NULL,
  `trans_type` varchar(64) DEFAULT NULL,
  `xeiristis_id` int(11) DEFAULT NULL,
  `add_from_system` varchar(64) DEFAULT NULL,
  `myfrom` varchar(64) DEFAULT NULL,
  `myerror` varchar(190) DEFAULT NULL,
  `mymessage` text DEFAULT NULL,
  
  `responseCode` int(11) DEFAULT NULL,
  
  `transactionType` varchar(64) DEFAULT NULL,
  `eftTerminalId` varchar(190) DEFAULT NULL,
  `installments` int(11) DEFAULT NULL,
  `nspResponseCode` varchar(190) DEFAULT NULL,
  `nspResponseCodeDescription` varchar(190) DEFAULT NULL,
  `ecrReferenceNumber` varchar(190) DEFAULT NULL,
  `nspReferenceNumber` varchar(190) DEFAULT NULL,
  `receiptNumber` varchar(190) DEFAULT NULL,
  `transactionTimestamp` varchar(190) DEFAULT NULL,
  `invoiceAmount` double DEFAULT NULL,
  `originalAmount` double DEFAULT NULL,
  `paidAmount` double DEFAULT NULL,
  `loyaltyAmount` double DEFAULT NULL,
  `tipAmount` double DEFAULT NULL,
  `bankAuthorizationCode` varchar(190) DEFAULT NULL,
  `bankCode` varchar(190) DEFAULT NULL,
  `cardNumber` varchar(190) DEFAULT NULL,
  `cardType` varchar(190) DEFAULT NULL,
  `cardHolder` varchar(190) DEFAULT NULL,
  
  PRIMARY KEY (`id_megeftpos_transaction`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `eftpos_transaction_id` (`eftpos_transaction_id`),
  KEY `trans_status` (`trans_status`),
  KEY `trans_type` (`trans_type`),
  KEY `xeiristis_id` (`xeiristis_id`),
  KEY `add_from_system` (`add_from_system`),
  KEY `myfrom` (`myfrom`),
  KEY `myerror` (`myerror`),
  
  KEY `responseCode` (`responseCode`),
  KEY `eftTerminalId` (`eftTerminalId`),
  KEY `transactionType` (`transactionType`),
  KEY `installments` (`installments`),
  KEY `nspResponseCode` (`nspResponseCode`),
  KEY `nspResponseCodeDescription` (`nspResponseCodeDescription`),
  KEY `ecrReferenceNumber` (`ecrReferenceNumber`),
  KEY `nspReferenceNumber` (`nspReferenceNumber`),
  KEY `receiptNumber` (`receiptNumber`),
  KEY `transactionTimestamp` (`transactionTimestamp`),
  KEY `invoiceAmount` (`invoiceAmount`),
  KEY `originalAmount` (`originalAmount`),
  KEY `paidAmount` (`paidAmount`),
  KEY `loyaltyAmount` (`loyaltyAmount`),
  KEY `tipAmount` (`tipAmount`),
  KEY `bankAuthorizationCode` (`bankAuthorizationCode`),
  KEY `bankCode` (`bankCode`),
  KEY `cardNumber` (`cardNumber`),
  KEY `cardType` (`cardType`),
  KEY `cardHolder` (`cardHolder`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select * from gks_permission_object where id_permission_object=854";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (854,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Λογιστική',0,'gks_megeftpos_transaction','Συναλλαγές Meg EFT/POS Driver',8505)");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  )  AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",854,1,1,1,1,1)");
  }
}


$sql="select aadeTransactionId from gks_viva_transaction limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_viva_transaction` 
  ADD COLUMN `aadeTransactionId` varchar(190) DEFAULT NULL,
  add index aadeTransactionId (aadeTransactionId)");
  
  gks_run_sql("ALTER TABLE `gks_megeftpos_transaction` 
  ADD COLUMN `aadeTransactionId` varchar(190) DEFAULT NULL,
  add index aadeTransactionId (aadeTransactionId)");
  
  gks_run_sql("ALTER TABLE `gks_mellon_transaction` 
  ADD COLUMN `aadeTransactionId` varchar(190) DEFAULT NULL,
  add index aadeTransactionId (aadeTransactionId)");
  
  gks_run_sql("ALTER TABLE `gks_cardlink_transaction` 
  ADD COLUMN `aadeTransactionId` varchar(190) DEFAULT NULL,
  add index aadeTransactionId (aadeTransactionId)");
  
  gks_run_sql("ALTER TABLE `gks_eftpos_transaction` 
  ADD COLUMN `aadeTransactionId` varchar(190) DEFAULT NULL,
  add index aadeTransactionId (aadeTransactionId)");
  
  
  
}

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_eftpos_transaction_thisisfor` (
  `id_eftpos_transaction_thisisfor` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `myip` varchar(48) DEFAULT NULL,
  `my_this` int(11) NOT NULL DEFAULT 0,
  `my_is` varchar(64) DEFAULT NULL,
  `my_for` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_eftpos_transaction_thisisfor`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `my_this` (`my_this`),
  KEY `my_is` (`my_is`),
  KEY `my_for` (`my_for`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select transfer_online_search_type from gks_transfer limit 1";
$result = $db_link->query($sql);
if ($result) { //must NOT return error
  gks_run_sql("alter table gks_transfer drop column transfer_online_search_type");
}

gks_run_sql("update gks_aade_katigoria_fpa set aade_katigoria_fpa_descr='ΦΠΑ συντελεστής 13%' where id_aade_katigoria_fpa=2");
gks_run_sql("update gks_aade_katigoria_fpa set aade_katigoria_fpa_descr='Εγγραφές χωρίς ΦΠΑ' where id_aade_katigoria_fpa=8");


gks_run_sql("update gks_whi_mov set aade_skopos_diakinisis_id=16 where aade_skopos_diakinisis_id=10");
gks_run_sql("update gks_acc_inv set aade_skopos_diakinisis_id=16 where aade_skopos_diakinisis_id=10");




//Meg EFT/POS Driver
$sql="select megeftpos_pos_id from gks_assets limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_assets` 
  ADD COLUMN `megeftpos_pos_id` varchar(190) DEFAULT NULL,
  ADD COLUMN `megeftpos_api_key` varchar(190) DEFAULT NULL,
  add index megeftpos_pos_id (megeftpos_pos_id)");
}

$sql="select id_asset from gks_assets where megeftpos_pos_id='' or megeftpos_pos_id is null";
$result = gks_run_sql($sql);
$id_asset_ids=array();
while ($row = $result->fetch_assoc()) $id_asset_ids[]=$row['id_asset'];
foreach ($id_asset_ids as $value) {
  $myguid=guid_for_megeftpos_pos_id();
  gks_run_sql("update gks_assets set megeftpos_pos_id='".$myguid."' where id_asset=".$value);
}
//print '<pre>';print_r($id_asset_ids);die();


$sql="select odbc from gks_permission_user limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_permission_user` 
  ADD COLUMN odbc timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() after myip");
}
  

/*
271 Πρότυπα SMS-Viber
272 Τύποι Συνδέσμων Κοινωνικών Δικτύων
273 Σύνδεσμοι Κοινωνικών Δικτύων
385 gks ERP App Mobile
399 Χάρτης
497 Pivot Table - Εργασίες
498 Pivot Table - Ευκαιρίες
277 Γλώσσες
395 Πληροφορίες Εφαρμογής
399 Χάρτης
683 POS Διαχείριση
684 POS Λειτουργία

425 CalDav
115 CardDav
850 Συναλλαγές Viva
*/

$id_permission_object_list=[271,272,273,385,399,497,498,277,395,399,683,684];
$sql="SELECT ssss.user_id
FROM (
  SELECT user_id
  FROM gks_permission_user
  GROUP BY user_id
)  AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
$result = gks_run_sql($sql);
$user_ids=array();
while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];

foreach ($id_permission_object_list as $idpo) {
  foreach ($user_ids as $idur) {
    $sql="select id_permission_user from gks_permission_user where user_id=".$idur." and permission_object_id=".$idpo;
    $result = gks_run_sql($sql);
    if ($result->num_rows==0) {
      gks_run_sql("INSERT INTO `gks_permission_user` 
      (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
      ('2021-01-02 00:00:00','2021-01-02 00:00:00',2,2,'127.0.0.1',".$idur.",".$idpo.",1,1,1,1,1)");
    }
  }
}

$sql="select user_id,permission_object_id,count(*) as cc from gks_permission_user
group by user_id,permission_object_id
having count(*)>=2
order by cc desc";
$result = gks_run_sql($sql);
if ($result->num_rows>0) {
  echo '<div style="background-color: red;color:white">Σφάλμα δικαιωμάτων στον πίνακα gks_permission_user</div>'; die();
}


$sql="select installments from gks_eftpos_transaction limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_eftpos_transaction` 
  ADD COLUMN installments int(11) NOT NULL DEFAULT 0 after tipAmount,
  ADD COLUMN refund_val double NOT NULL DEFAULT 0 after installments,
  add index installments (installments)");
}

$sql="select installments from gks_viva_transaction limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_viva_transaction` 
  ADD COLUMN installments int(11) NOT NULL DEFAULT 0 after TipAmount,
  add index installments (installments)");
}


gks_run_sql("ALTER TABLE `gks_mellon_transaction` 
MODIFY COLUMN `myerror` TEXT DEFAULT NULL,
DROP INDEX `myerror`,
ADD INDEX `myerror` (`myerror`(190))");


$sql="select * from gks_permission_object where id_permission_object=840";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (840,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Λογιστική',0,'gks_paroxos_signature','Ψηφιακές υπογραφές από πάροχο',8400)");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  )  AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",840,1,1,1,1,1)");
  }
}


$sql="select epay_mid from gks_company limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_company
	add column epay_mid varchar(190) DEFAULT NULL");
}

$sql="select signing_author from gks_aade_paroxos limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_aade_paroxos
	add column signing_author varchar(20) DEFAULT NULL");
	
	gks_run_sql("update gks_aade_paroxos set signing_author='004' where id_aade_paroxos=1");//EPSILONDIGITAL
	gks_run_sql("update gks_aade_paroxos set signing_author='003',paroxos_name='IMPACT myDATA connect' where id_aade_paroxos=2");//IMPACT myDATA connect
	gks_run_sql("update gks_aade_paroxos set signing_author='001' where id_aade_paroxos=3");//SoftOne EINVOICΙNG
	gks_run_sql("update gks_aade_paroxos set signing_author='002' where id_aade_paroxos=4");//Entersoft e-Invoicing
	gks_run_sql("update gks_aade_paroxos set signing_author='006' where id_aade_paroxos=5");//Oxygen-Pelatologio
	gks_run_sql("update gks_aade_paroxos set signing_author='005' where id_aade_paroxos=6");//timologisi.online
	gks_run_sql("update gks_aade_paroxos set signing_author='007' where id_aade_paroxos=7");//Primer MyData
	gks_run_sql("update gks_aade_paroxos set signing_author='008' where id_aade_paroxos=8");//Meg myData 
	gks_run_sql("update gks_aade_paroxos set signing_author='009' where id_aade_paroxos=9");//PROSVASISGO eInvoicing
	gks_run_sql("update gks_aade_paroxos set signing_author='010',paroxos_name='MATRapidSign' where id_aade_paroxos=10");//MATRapidSign
	gks_run_sql("update gks_aade_paroxos set signing_author='011' where id_aade_paroxos=11");//SIMPLY
	gks_run_sql("update gks_aade_paroxos set signing_author='012',paroxos_name='eskap' where id_aade_paroxos=12");//eskap
	gks_run_sql("update gks_aade_paroxos set signing_author='013',paroxos_name='EMDI' where id_aade_paroxos=13");//EMDI
	gks_run_sql("update gks_aade_paroxos set signing_author='014',paroxos_name='ONESIGN' where id_aade_paroxos=14");//ONESIGN
	gks_run_sql("update gks_aade_paroxos set signing_author='015',paroxos_name='Orian MyInvoices' where id_aade_paroxos=15");//Orian MyInvoices
	gks_run_sql("update gks_aade_paroxos set signing_author='016',paroxos_name='Pegasus e-invoicing' where id_aade_paroxos=16");//Pegasus e-invoicing
	gks_run_sql("update gks_aade_paroxos set signing_author='017',paroxos_name='Cloud CRM' where id_aade_paroxos=17");//Cloud CRM
	gks_run_sql("update gks_aade_paroxos set signing_author='003',paroxos_name='Impact eInvoicing' where id_aade_paroxos=18");//Impact eInvoicing
	gks_run_sql("update gks_aade_paroxos set signing_author='018',paroxos_name='Cloud T' where id_aade_paroxos=19");//Cloud T
	gks_run_sql("update gks_aade_paroxos set signing_author='019',paroxos_name='Parochos Online' where id_aade_paroxos=20");//Parochos Online
}


$sql="select paroxos_signature_id from gks_eftpos_transaction limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_eftpos_transaction
	add column paroxos_signature_id int(11) NOT NULL DEFAULT 0,
	add index paroxos_signature_id (paroxos_signature_id)");
}

$sql="select count_send_to_pos from gks_paroxos_signature limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_paroxos_signature
	add column count_send_to_pos int(11) NOT NULL DEFAULT 0,
	add index count_send_to_pos (count_send_to_pos)");
}

$sql="select id_pos, def_tropos_pliromis_array from gks_pos where def_tropos_pliromis_array<>''";
$result = gks_run_sql($sql);
$myarray=array();
while ($row = $result->fetch_assoc()) $myarray[]=$row;
foreach ($myarray as $value) {
  $temp=json_decode($value['def_tropos_pliromis_array'],true);
  if (is_array($temp) and count($temp)>0 and is_integer($temp[0])) {
    //old_system  
    $temp2=[];
    foreach ($temp as $value2) {
      $temp2[]=array(
        'id' => $value2,
        'asset_id' => 0,
      );
    }
    $sql="update gks_pos set 
    def_tropos_pliromis_array='".$db_link->escape_string(json_encode($temp2))."'
    where id_pos=".$value['id_pos'];
    $result = gks_run_sql($sql);
    
  }
} 
//print '<pre>';print_r($myarray);die();

$sql="select pos_installments from gks_pos limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_pos
	add column pos_installments int(11) NOT NULL DEFAULT 0,
	add column pos_tip tinyint(4) NOT NULL DEFAULT 0");
}

$sql="select * from gks_payment_acquirer_with where id_payment_acquirer_with=5";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_payment_acquirer_with` (`id_payment_acquirer_with`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`payment_paroxos_name`,`payment_paroxos_implemented`,`payment_paroxos_sortorder`) VALUES 
   (5,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','ePay',0,5)");

}

$sql="select epay_username from gks_company limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_company
  add column `epay_username` varchar(190) DEFAULT NULL,
  add column `epay_password` varchar(190) DEFAULT NULL,
  add column `epay_authorization_code` varchar(190) DEFAULT NULL,
  add column `epay_x_api_key` varchar(190) DEFAULT NULL");
}

$sql="select epay_id from gks_assets limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_assets
  add column `epay_id` varchar(190) DEFAULT NULL,
  add column `epay_terminal_id` varchar(190) DEFAULT NULL,
  add column `epay_company_id` int(11) NOT NULL DEFAULT 0,
  add index `epay_id` (`epay_id`),
  add index `epay_terminal_id` (`epay_terminal_id`)");
}



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_epay_transaction` (
  `id_epay_transaction` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `eftpos_transaction_id` int(11) DEFAULT NULL,
  `intentId` int(11) DEFAULT NULL,
  `myStatus` tinyint(4) NOT NULL DEFAULT 0,
  `myResult` tinyint(4) NOT NULL DEFAULT 0,
  `xeiristis_id` int(11) DEFAULT NULL,
  `add_from_system` varchar(64) DEFAULT NULL,
  `myfrom` varchar(64) DEFAULT NULL,
  `TransactionId_org` varchar(190) DEFAULT NULL,
  `Id` varchar(190) DEFAULT NULL,
  `ExternalId` varchar(190) DEFAULT NULL,
  `TxnType` int(11) DEFAULT NULL,
  `Timestamp` datetime DEFAULT NULL,
  `VoidTimestamp` datetime DEFAULT NULL,
  `CardPAN` varchar(190) DEFAULT NULL,
  `CardHash` varchar(190) DEFAULT NULL,
  `Approved` tinyint(4) NOT NULL DEFAULT 0,
  `Voided` tinyint(4) NOT NULL DEFAULT 0,
  `STAN` int(11) DEFAULT NULL,
  `BatchNumber` int(11) DEFAULT NULL,
  `Batch` varchar(190) DEFAULT NULL,
  `Acquirer` varchar(190) DEFAULT NULL,
  `TID` varchar(190) DEFAULT NULL,
  `MID` varchar(190) DEFAULT NULL,
  `Amount` double DEFAULT NULL,
  `DccAmount` double DEFAULT NULL,
  `TipAmount` double DEFAULT NULL,
  `CashbackAmount` double DEFAULT NULL,
  `LoyaltyRedemptionAmount` double DEFAULT NULL,
  `Instalments` int(11) DEFAULT NULL,
  `PosEntryMode` int(11) DEFAULT NULL,
  `Cryptogram` varchar(190) DEFAULT NULL,
  `HostResponseCode` varchar(190) DEFAULT NULL,
  `RRN` varchar(190) DEFAULT NULL,
  `AuthCode` varchar(190) DEFAULT NULL,
  `OriginalRRN` varchar(190) DEFAULT NULL,
  `OriginalAuthCode` varchar(190) DEFAULT NULL,
  `CurrencyCode` int(11) DEFAULT NULL,
  `DccCurrencyCode` varchar(190) DEFAULT NULL,
  `CustomerReference` varchar(190) DEFAULT NULL,
  `myerror` text DEFAULT NULL,
  `myjson` text DEFAULT NULL,
  `CustomerEmail` varchar(190) DEFAULT NULL,
  `CustomerPhone` varchar(190) DEFAULT NULL,
  `aadeTransactionId` varchar(190) DEFAULT NULL,
  PRIMARY KEY (`id_epay_transaction`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `xeiristis_id` (`xeiristis_id`),
  KEY `add_from_system` (`add_from_system`),
  KEY `myfrom` (`myfrom`),
  KEY `Id` (`Id`),
  KEY `TID` (`TID`),
  KEY `MID` (`MID`),
  KEY `RRN` (`RRN`),
  KEY `OriginalRRN` (`OriginalRRN`),
  KEY `amount` (`Amount`),
  KEY `eftpos_transaction_id` (`eftpos_transaction_id`),
  KEY `myStatus` (`myStatus`),
  KEY `myResult` (`myResult`),
  KEY `intentId` (`intentId`),
  KEY `aadeTransactionId` (`aadeTransactionId`),
  KEY `myerror` (`myerror`(190))
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select * from gks_permission_object where id_permission_object=855";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (855,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Λογιστική',0,'gks_epay_transaction','Συναλλαγές ePay',8550)");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  )  AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",855,1,1,1,1,1)");
  }
}

$sql="select remote_id from gks_eftpos_transaction limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_eftpos_transaction
  add column `remote_id` BIGINT NOT NULL DEFAULT 0 after xxx_transaction_id,
  add index `remote_id` (`remote_id`)");
}



gks_run_sql("ALTER TABLE `gks_eftpos_transaction`
MODIFY COLUMN `remote_id` BIGINT NOT NULL DEFAULT 0;");
gks_run_sql("ALTER TABLE `gks_epay_transaction` 
MODIFY COLUMN `intentId` BIGINT DEFAULT NULL;");



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_acc_pay_payment` (
  `id_acc_pay_payment` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `myip` varchar(48) DEFAULT NULL,
  `acc_pay_id` int(11) NOT NULL DEFAULT 0,
  `acc_pay_method_id` int(11) NOT NULL DEFAULT 0,
  `pp` int(11) NOT NULL DEFAULT 0,
  `payment_acquirer_id` int(11) NOT NULL DEFAULT 0,
  `poso` double NOT NULL DEFAULT 0,
  `asset_id` int(11) NOT NULL DEFAULT 0,
  `transaction_pa_with_id` int(11) NOT NULL DEFAULT 0,
  `transaction_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_acc_pay_payment`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `acc_pay_id` (`acc_pay_id`),
  KEY `acc_pay_method_id` (`acc_pay_method_id`),
  KEY `pp` (`pp`),
  KEY `payment_acquirer_id` (`payment_acquirer_id`),
  KEY `asset_id` (`asset_id`),
  KEY `transaction_pa_with_id` (`transaction_pa_with_id`),
  KEY `transaction_id` (`transaction_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


//disable check index sto db compare gks_assets|megeftpos_pos_id

$sql="select update_from_gks from gks_acc_pay limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table `gks_acc_pay`
  add column `update_from_gks` tinyint(4) NOT NULL DEFAULT 0,
  add column `from_aade_import` varchar(16) DEFAULT '',
  add column `from_aade_import_json` text DEFAULT NULL,
  add column `import_pay_acc_seira_code` varchar(48) DEFAULT NULL,
  add column `import_pay_acc_number_str` varchar(48) DEFAULT NULL,
  add column `import_eidos_parastatikou_aade_code` varchar(8) DEFAULT NULL,
  
  
  add column `aade_invoiceuid` varchar(64) DEFAULT NULL,
  add column `aade_invoicemark` varchar(64) DEFAULT NULL,
  add column `aade_qrurl` varchar(256) DEFAULT NULL,
  add column `aade_statuscode` varchar(64) DEFAULT NULL,
  add column `aade_errors` text DEFAULT NULL,
  add column `aade_send_date` datetime DEFAULT NULL,
  add column `aade_user_id` int(11) NOT NULL DEFAULT 0,
  add column `aade_xml_send` varchar(64) DEFAULT NULL,
  add column `aade_xml_response` varchar(64) DEFAULT NULL,
  add column `aade_paroxos_id` int(11) NOT NULL DEFAULT 0,
  
  add column `paroxos_processId` varchar(64) DEFAULT NULL,
  add column `paroxos_last_response` text DEFAULT NULL,
  add column `paroxos_status` tinyint(4) NOT NULL DEFAULT -1,
  add column `paroxos_authenticationCode` varchar(64) DEFAULT NULL,
  add column `paroxos_user_send` int(11) NOT NULL DEFAULT 0,
  add column `paroxos_date_send` datetime DEFAULT NULL,
  add column `paroxos_get_files` datetime DEFAULT NULL,
  add column `paroxos_send_pdf` datetime DEFAULT NULL,
  add column `paroxos_send_pdf_name` varchar(255) DEFAULT NULL,
  add column `paroxos_send_pdf_url` varchar(1024) DEFAULT NULL,
  add column `paroxos_invoice_number` varchar(190) DEFAULT NULL,
  
  add index `aade_invoiceuid` (`aade_invoiceuid`),
  add index `aade_invoicemark` (`aade_invoicemark`),
  add index `aade_statuscode` (`aade_statuscode`),
  add index `aade_send_date` (`aade_send_date`);");
}


$sql="select acc_pay_payment_id from gks_eftpos_transaction limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table `gks_eftpos_transaction`
  add column `acc_pay_payment_id` int(11) NOT NULL DEFAULT 0 after acc_inv_payment_id");
}

$sql="select acc_pay_payment_id from gks_paroxos_signature limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table `gks_paroxos_signature`
  add column `acc_pay_payment_id` int(11) NOT NULL DEFAULT 0 after acc_inv_payment_id");
}

gks_run_sql("update gks_acc_eidi_parastatikon set sortorder=3050 where id_acc_eidos_parastatikou=802");
gks_run_sql("update gks_acc_eidi_parastatikon set sortorder=3501 where id_acc_eidos_parastatikou=812");
gks_run_sql("update gks_acc_eidi_parastatikon set sortorder=3201 where id_acc_eidos_parastatikou=803");
gks_run_sql("update gks_acc_eidi_parastatikon set sortorder=3551 where id_acc_eidos_parastatikou=813");

$sql="select * from gks_acc_eidi_parastatikon where id_acc_eidos_parastatikou=840";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_acc_eidi_parastatikon` (`id_acc_eidos_parastatikou`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`parent_id`,`eidos_parastatikou_type_id`,`eidos_parastatikou_need_prev`,`eidos_parastatikou_has_fpa`,`eidos_parastatikou_has_posotita`,`eidos_parastatikou_has_othertaxes`,`eidos_parastatikou_has_esoda`,`eidos_parastatikou_has_eksoda`,`eidos_parastatikou_need_afm`,`eidos_parastatikou_aade_code`,`eidos_parastatikou_descr`,`eidos_parastatikou_balance_pros`,`eidos_parastatikou_stock_pros`,`eidos_parastatikou_whi_type_id`,`def_prefix`,`def_suffix`,`sortorder`,`is_selectable`,`credit_acc_eidos_parastatikou_id`,`rbs_code_a`,`import_apo_allon`,`peppol_code`) VALUES 
 (840,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1',801,11,0,0,0,NULL,0,0,0,'8.4','Απόδειξη Είσπραξης POS',-1,0,0,NULL,NULL,3100,1,850,0,NULL,0),
 (850,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1',801,11,0,0,0,NULL,0,0,0,'8.5','Απόδειξη Επιστροφής POS',1,0,0,NULL,NULL,3251,1,0,0,NULL,0)");
}


$sql="select cancel_for_acc_pay_id from gks_acc_pay limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table `gks_acc_pay`
  add column `cancel_for_acc_pay_id` int(11) NOT NULL DEFAULT 0 after id_acc_pay,
  add index cancel_for_acc_pay_id (cancel_for_acc_pay_id)");
}




$sql="select * from gks_aade_tropoi_pliromis where id_aade_tropos_pliromis=8";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_aade_tropoi_pliromis` (`id_aade_tropos_pliromis`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`aade_tropos_pliromis_code`,`aade_tropos_pliromis_descr`,`sortorder`) VALUES 
   (8,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',8,'Άμεσες Πληρωμές IRIS',8)");
}


$sql="select acc_pay_id from gks_company_paroxos_log limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table `gks_company_paroxos_log`
  add column `acc_pay_id` int(11) NOT NULL DEFAULT 0 after acc_inv_id,
  add index acc_pay_id (acc_pay_id)");
}


$sql="select transfer_oxima_type_private_start_time_s from gks_transfer_oxima_type limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table `gks_transfer_oxima_type`
  add column `transfer_oxima_type_private_start_time_s` varchar(5) DEFAULT NULL,
  add column `transfer_oxima_type_private_start_time_e` varchar(5) DEFAULT NULL,
  add column `transfer_oxima_type_private_start_time_m` double DEFAULT 0");
}

$sql="select transfer_oxima_type_private_start_time_t from gks_transfer_oxima_type limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table `gks_transfer_oxima_type`
  add column `transfer_oxima_type_private_start_time_t` integer not null DEFAULT 0 after transfer_oxima_type_private_start_time_m");
}




$sql="select transfer_areas from gks_transfer limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer` 
  ADD COLUMN `transfer_areas` LONGTEXT DEFAULT NULL,
  ADD COLUMN `transfer_bound_north` double NOT NULL DEFAULT '0' AFTER `transfer_areas`,
  ADD COLUMN `transfer_bound_south` double NOT NULL DEFAULT '0' AFTER `transfer_bound_north`,
  ADD COLUMN `transfer_bound_east`  double NOT NULL DEFAULT '0' AFTER `transfer_bound_south`,
  ADD COLUMN `transfer_bound_west`  double NOT NULL DEFAULT '0' AFTER `transfer_bound_east`,
  ADD INDEX transfer_bound_north (transfer_bound_north),
  ADD INDEX transfer_bound_south (transfer_bound_south),
  ADD INDEX transfer_bound_east (transfer_bound_east),
  ADD INDEX transfer_bound_west (transfer_bound_west)");
  
}

$sql="select transfer_countries from gks_transfer limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer` 
  ADD COLUMN `transfer_countries` text DEFAULT NULL");
  
}

$sql="select transfer_outward_from_location_message from gks_transfer limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_transfer`
  ADD COLUMN `transfer_outward_from_location_message` text DEFAULT NULL after transfer_outward_from_cruise_message,
  ADD COLUMN `transfer_return_from_location_message` text DEFAULT NULL after transfer_return_from_cruise_message,

  CHANGE COLUMN `transfer_outward_from_pick_up_time_text` `transfer_outward_from_pick_up_time_text_airplane` text DEFAULT NULL,
  ADD COLUMN `transfer_outward_from_pick_up_time_text_train` text DEFAULT NULL after transfer_outward_from_pick_up_time_text_airplane,
  ADD COLUMN `transfer_outward_from_pick_up_time_text_cruise` text DEFAULT NULL after transfer_outward_from_pick_up_time_text_train,
  ADD COLUMN `transfer_outward_from_pick_up_time_text_location` text DEFAULT NULL after transfer_outward_from_pick_up_time_text_cruise,

  CHANGE COLUMN `transfer_return_from_pick_up_time_text` `transfer_return_from_pick_up_time_text_airplane` text DEFAULT NULL,
  ADD COLUMN `transfer_return_from_pick_up_time_text_train` text DEFAULT NULL after transfer_return_from_pick_up_time_text_airplane,
  ADD COLUMN `transfer_return_from_pick_up_time_text_cruise` text DEFAULT NULL after transfer_return_from_pick_up_time_text_train,
  ADD COLUMN `transfer_return_from_pick_up_time_text_location` text DEFAULT NULL after transfer_return_from_pick_up_time_text_cruise,

  ADD COLUMN transfer_price_round_type tinyint(4) NOT NULL DEFAULT 0 after transfer_default_price;");
  
}

gks_run_sql("update gks_acc_eidi_parastatikon set 
eidos_parastatikou_aade_code='9.3'
where id_acc_eidos_parastatikou in (902,903,952)");

$sql="select update_from_gks from gks_whi_mov limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_whi_mov`
  ADD COLUMN `update_from_gks`  tinyint(4) NOT NULL DEFAULT 0 after credit_memo_for_whi_mov_id,
  ADD COLUMN `from_aade_import` varchar(16) DEFAULT NULL after update_from_gks,
  ADD COLUMN `from_aade_import_json` text DEFAULT NULL after from_aade_import,

  ADD COLUMN `aade_invoiceuid` varchar(64) DEFAULT NULL after aade_skopos_diakinisis_id,
  ADD COLUMN `aade_invoicemark` varchar(64) DEFAULT NULL after aade_invoiceuid,
  ADD COLUMN `aade_qrurl` varchar(256) DEFAULT NULL after aade_invoicemark,
  ADD COLUMN `aade_statuscode` varchar(64) DEFAULT NULL after aade_qrurl,
  ADD COLUMN `aade_errors` text DEFAULT NULL after aade_statuscode,
  ADD COLUMN `aade_send_date` datetime DEFAULT NULL after aade_errors,
  ADD COLUMN `aade_user_id` int(11) NOT NULL DEFAULT 0 after aade_send_date,
  ADD COLUMN `aade_xml_send` varchar(64) DEFAULT NULL after aade_user_id,
  ADD COLUMN `aade_xml_response` varchar(64) DEFAULT NULL after aade_xml_send,
  ADD COLUMN `aade_paroxos_id` int(11) NOT NULL DEFAULT 0 after aade_xml_response,
  ADD COLUMN `paroxos_processId` varchar(64) DEFAULT NULL after aade_paroxos_id,
  ADD COLUMN `paroxos_last_response` text DEFAULT NULL after paroxos_processId,
  ADD COLUMN `paroxos_status` tinyint(4) NOT NULL DEFAULT -1 after paroxos_last_response,
  ADD COLUMN `paroxos_authenticationCode` varchar(64) DEFAULT NULL after paroxos_status,
  ADD COLUMN `paroxos_user_send` int(11) NOT NULL DEFAULT 0 after paroxos_authenticationCode,
  ADD COLUMN `paroxos_date_send` datetime DEFAULT NULL after paroxos_user_send,
  ADD COLUMN `paroxos_get_files` datetime DEFAULT NULL after paroxos_date_send,
  ADD COLUMN `paroxos_send_pdf` datetime DEFAULT NULL after paroxos_get_files,
  ADD COLUMN `paroxos_send_pdf_name` varchar(255) DEFAULT NULL after paroxos_send_pdf,
  ADD COLUMN `paroxos_send_pdf_url` varchar(1024) DEFAULT NULL after paroxos_send_pdf_name,
  ADD COLUMN `contract_reference` varchar(190) DEFAULT NULL after paroxos_send_pdf_url,
  ADD COLUMN `project_reference` varchar(190) DEFAULT NULL after contract_reference,
  ADD COLUMN `paroxos_invoice_number` varchar(190) DEFAULT NULL after project_reference,  
  
  add index `aade_invoiceuid` (`aade_invoiceuid`),
  add index `aade_invoicemark` (`aade_invoicemark`),
  add index `aade_statuscode` (`aade_statuscode`),
  add index `aade_send_date` (`aade_send_date`);");
}

gks_run_sql("update gks_aade_katigoria_fpa set aade_katigoria_fpa_descr='ΦΠΑ συντελεστής 0%' where id_aade_katigoria_fpa=7");
gks_run_sql("update gks_aade_katigoria_fpa set aade_katigoria_fpa_descr='ΦΠΑ συντελεστής 3% (αρ.31 ν.5057/2023)' where id_aade_katigoria_fpa=9");


$sql="select * from gks_aade_katigoria_fpa where id_aade_katigoria_fpa=10";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_aade_katigoria_fpa` (
  `id_aade_katigoria_fpa`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,
  `aade_katigoria_fpa_code`,`aade_katigoria_fpa_descr`,`aade_katigoria_fpa_pososto`,`sortorder`,`fpa_base_id`
  ) VALUES (
  10,'2024-01-01 00:00:00','2024-01-01 00:00:00',2,2,'127.0.0.1',
  '10', 'ΦΠΑ συντελεστής 4% (αρ.31 ν.5057/2023)',0.04,10,0)");
}






$sql="select ma_arithmos from gks_users limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_users`
  ADD COLUMN `ma_arithmos` varchar(64) DEFAULT NULL after ma_odos");
  
  gks_run_sql("ALTER TABLE `gks_users_extra_address`
  ADD COLUMN `ea_arithmos` varchar(64) DEFAULT NULL after ea_odos");
  
  gks_run_sql("ALTER TABLE `gks_company`
  ADD COLUMN `company_arithmos` varchar(64) DEFAULT NULL after company_odos");
  gks_run_sql("ALTER TABLE `gks_company_lang`
  ADD COLUMN `company_arithmos` varchar(64) DEFAULT NULL after company_odos");
  
  gks_run_sql("ALTER TABLE `gks_company_subs`
  ADD COLUMN `company_sub_arithmos` varchar(64) DEFAULT NULL after company_sub_odos");
  gks_run_sql("ALTER TABLE `gks_company_subs_lang`
  ADD COLUMN `company_sub_arithmos` varchar(64) DEFAULT NULL after company_sub_odos");
  
  gks_run_sql("ALTER TABLE `gks_warehouses`
  ADD COLUMN `warehouse_arithmos` varchar(64) DEFAULT NULL after warehouse_odos");
  gks_run_sql("ALTER TABLE `gks_warehouses_lang`
  ADD COLUMN `warehouse_arithmos` varchar(64) DEFAULT NULL after warehouse_odos");
  
  gks_run_sql("ALTER TABLE `gks_hotel`
  ADD COLUMN `hotel_arithmos` varchar(64) DEFAULT NULL after hotel_odos");
  
  gks_run_sql("ALTER TABLE `gks_transfer`
  ADD COLUMN `transfer_arithmos` varchar(64) DEFAULT NULL after transfer_odos");

  gks_run_sql("ALTER TABLE `gks_crm_leads`
  ADD COLUMN `arithmos` varchar(64) DEFAULT NULL after odos");

  gks_run_sql("ALTER TABLE `gks_calendar`
  ADD COLUMN `calendar_arithmos` varchar(64) DEFAULT NULL after calendar_odos");

  gks_run_sql("ALTER TABLE `gks_crm_tasks`
  ADD COLUMN `arithmos` varchar(64) DEFAULT NULL after odos");

  gks_run_sql("ALTER TABLE `gks_hotel_reservation`
  ADD COLUMN `ma_arithmos` varchar(64) DEFAULT NULL after ma_odos,
  ADD COLUMN `other_ma_arithmos` varchar(64) DEFAULT NULL after other_ma_odos");

  gks_run_sql("ALTER TABLE `gks_hotel_reservation_room`
  ADD COLUMN `ruser_ma_arithmos` varchar(64) DEFAULT NULL after ruser_ma_odos");

  gks_run_sql("ALTER TABLE `gks_transfer_reservation`
  ADD COLUMN `ma_arithmos` varchar(64) DEFAULT NULL after ma_odos,
  ADD COLUMN `other_ma_arithmos` varchar(64) DEFAULT NULL after other_ma_odos");

  gks_run_sql("ALTER TABLE `gks_transfer_reservation_oximata`
  ADD COLUMN `ruser_ma_arithmos` varchar(64) DEFAULT NULL after ruser_ma_odos");

  gks_run_sql("ALTER TABLE `gks_poi`
  ADD COLUMN `poi_arithmos` varchar(64) DEFAULT NULL after poi_odos");

  gks_run_sql("ALTER TABLE `gks_whi_mov`
  ADD COLUMN `ma_arithmos` varchar(64) DEFAULT NULL after ma_odos,
  ADD COLUMN `destination_data_arithmos` varchar(64) DEFAULT NULL after destination_data_odos,
  ADD COLUMN `other_ma_arithmos` varchar(64) DEFAULT NULL after other_ma_odos");

  gks_run_sql("ALTER TABLE `gks_orders`
  ADD COLUMN `ma_arithmos` varchar(64) DEFAULT NULL after ma_odos,
  ADD COLUMN `destination_data_arithmos` varchar(64) DEFAULT NULL after destination_data_odos,
  ADD COLUMN `other_ma_arithmos` varchar(64) DEFAULT NULL after other_ma_odos");

  gks_run_sql("ALTER TABLE `gks_acc_inv`
  ADD COLUMN `ma_arithmos` varchar(64) DEFAULT NULL after ma_odos,
  ADD COLUMN `destination_data_arithmos` varchar(64) DEFAULT NULL after destination_data_odos,
  ADD COLUMN `other_ma_arithmos` varchar(64) DEFAULT NULL after other_ma_odos");

}

$sql="select dispatch_time from gks_orders limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_orders`
  ADD COLUMN `dispatch_time` TIME DEFAULT NULL after dispatch_date");
  
  gks_run_sql("ALTER TABLE `gks_acc_inv`
  ADD COLUMN `dispatch_time` TIME DEFAULT NULL after dispatch_date");
  
  gks_run_sql("ALTER TABLE `gks_whi_mov`
  ADD COLUMN `dispatch_time` TIME DEFAULT NULL after dispatch_date");
}

$sql="select ma_branch from gks_users limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_users`
  ADD COLUMN `ma_branch` int(11) DEFAULT NULL after epaggelma");
}

$sql="select ea_branch from gks_users_extra_address limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_users_extra_address`
  ADD COLUMN `ea_branch` int(11) DEFAULT NULL after ea_phone");
}

$sql="select warehouse_branch from gks_warehouses limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_warehouses`
  ADD COLUMN `warehouse_branch` int(11) DEFAULT NULL after warehouse_website");
}

$sql="select load_odos from gks_whi_mov limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_whi_mov`
  
  ADD COLUMN `load_branch` int(11) DEFAULT NULL,
  ADD COLUMN `load_odos` varchar(255) DEFAULT NULL,
  ADD COLUMN `load_arithmos` varchar(64) DEFAULT NULL,
  ADD COLUMN `load_perioxi` varchar(255) DEFAULT NULL,
  ADD COLUMN `load_poli` varchar(255) DEFAULT NULL,
  ADD COLUMN `load_tk` varchar(255) DEFAULT NULL,
  ADD COLUMN `load_country_id` int(11) NOT NULL DEFAULT 0,
  ADD COLUMN `load_nomos_id` int(11) NOT NULL DEFAULT 0,

  ADD COLUMN `deli_branch` int(11) DEFAULT NULL,
  ADD COLUMN `deli_odos` varchar(255) DEFAULT NULL,
  ADD COLUMN `deli_arithmos` varchar(64) DEFAULT NULL,
  ADD COLUMN `deli_perioxi` varchar(255) DEFAULT NULL,
  ADD COLUMN `deli_poli` varchar(255) DEFAULT NULL,
  ADD COLUMN `deli_tk` varchar(255) DEFAULT NULL,
  ADD COLUMN `deli_country_id` int(11) NOT NULL DEFAULT 0,
  ADD COLUMN `deli_nomos_id` int(11) NOT NULL DEFAULT 0");
}

$sql="select load_odos from gks_acc_inv limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_inv`
  
  ADD COLUMN `load_branch` int(11) DEFAULT NULL,
  ADD COLUMN `load_odos` varchar(255) DEFAULT NULL,
  ADD COLUMN `load_arithmos` varchar(64) DEFAULT NULL,
  ADD COLUMN `load_perioxi` varchar(255) DEFAULT NULL,
  ADD COLUMN `load_poli` varchar(255) DEFAULT NULL,
  ADD COLUMN `load_tk` varchar(255) DEFAULT NULL,
  ADD COLUMN `load_country_id` int(11) NOT NULL DEFAULT 0,
  ADD COLUMN `load_nomos_id` int(11) NOT NULL DEFAULT 0,

  ADD COLUMN `deli_branch` int(11) DEFAULT NULL,
  ADD COLUMN `deli_odos` varchar(255) DEFAULT NULL,
  ADD COLUMN `deli_arithmos` varchar(64) DEFAULT NULL,
  ADD COLUMN `deli_perioxi` varchar(255) DEFAULT NULL,
  ADD COLUMN `deli_poli` varchar(255) DEFAULT NULL,
  ADD COLUMN `deli_tk` varchar(255) DEFAULT NULL,
  ADD COLUMN `deli_country_id` int(11) NOT NULL DEFAULT 0,
  ADD COLUMN `deli_nomos_id` int(11) NOT NULL DEFAULT 0");
}




gks_run_sql("update gks_acc_eidi_parastatikon set eidos_parastatikou_aade_code='8' where id_acc_eidos_parastatikou=801");
gks_run_sql("update gks_acc_eidi_parastatikon set eidos_parastatikou_aade_code='9' where id_acc_eidos_parastatikou=901");



$sql="select seira_isdeliverynote from gks_acc_seires limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_seires`
  add column seira_isdeliverynote tinyint(4) NOT NULL DEFAULT 0");
  
}

$sql="select aade_sending from gks_acc_inv limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_inv`
  add column aade_sending text DEFAULT NULL after aade_xml_response");

  $sql="alter table gks_acc_inv add index `aade_invoiceuid` (`aade_invoiceuid`)";
  $result = $db_link->query($sql);
}

$sql="select aade_sending from gks_acc_pay limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_pay`
  add column aade_sending text DEFAULT NULL after aade_xml_response");
  
  $sql="alter table gks_acc_pay add index `aade_invoiceuid` (`aade_invoiceuid`)";
  $result = $db_link->query($sql);
}

$sql="select aade_sending from gks_whi_mov limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_whi_mov`
  add column aade_sending text DEFAULT NULL after aade_xml_response");
  
  $sql="alter table gks_whi_mov add index `aade_invoiceuid` (`aade_invoiceuid`)";
  $result = $db_link->query($sql);
}


$sql="select product_taric from gks_eshop_products limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_eshop_products`
  add column product_taric varchar(48) DEFAULT NULL after product_sku,
  add index product_taric (product_taric)");
}

$sql="select * from gks_acc_eidi_parastatikon where id_acc_eidos_parastatikou=702 and rbs_code_a=215";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  
  gks_run_sql("update gks_acc_eidi_parastatikon set rbs_code_a=215 where id_acc_eidos_parastatikou=702");
  gks_run_sql("update gks_monades_metrisis set monada_descr='Έτη',           aade_eidos_posotitas_id=1, monada_peppol_code='ANN' where id_monada=56");
  gks_run_sql("update gks_monades_metrisis set monada_descr='Διανυκτέρευση', aade_eidos_posotitas_id=1, monada_peppol_code='DAY' where id_monada=100");

}

gks_run_sql("update gks_banks set bank_descr=trim(bank_descr) where id_bank=33");

$sql="select nomos_descr_en_US_old from gks_nomoi limit 1";
$result = $db_link->query($sql);
if ($result) { // return OK
  gks_run_sql("ALTER TABLE `gks_nomoi` drop column nomos_descr_en_US_old");
}

$sql="select country_name_en_US_old from gks_country limit 1";
$result = $db_link->query($sql);
if ($result) { // return OK
  gks_run_sql("ALTER TABLE `gks_country` drop column country_name_en_US_old");
}


gks_run_sql("update gks_nomoi set nomos_descr='U.S. Minor Outlying Islands (cf. separate entry UM)' where id_nomos=4527 and mydate_edit='2020-01-01'");






gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_aade_entitytype` (
  `id_aade_entitytype` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `aade_entitytype_code` int(11) NOT NULL DEFAULT 0,
  `aade_entitytype_descr` varchar(200) DEFAULT NULL,
  `sortorder` int(11) NOT NULL DEFAULT 1000,
  PRIMARY KEY (`id_aade_entitytype`),
  UNIQUE KEY `aade_entitytype_code` (`aade_entitytype_code`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `aade_entitytype_descr` (`aade_entitytype_descr`),
  KEY `sortorder` (`sortorder`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select * from gks_aade_entitytype where id_aade_entitytype=1";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("insert into gks_aade_entitytype (
  id_aade_entitytype,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  aade_entitytype_code,aade_entitytype_descr,sortorder
  ) values 

 (1,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',1,'Φορολογικός Εκπρόσωπος',1),
 (2,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',2,'Διαμεσολαβητής',2),
 (3,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',3,'Μεταφορέας',3),
 (4,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',4,'Λήπτης του Αποστολέα (Πωλητή)',4),
 (5,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',5,'Αποστολέας (Πωλητής)',5),
 (6,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',6,'Λοιπές Συσχετιζόμενες Οντότητες',6);");
}





$sql="select eidos_parastatikou_other_entity from gks_acc_eidi_parastatikon limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_eidi_parastatikon`
  add column eidos_parastatikou_other_entity tinyint(4) NOT NULL DEFAULT 0 after eidos_parastatikou_whi_type_id");
  
  gks_run_sql("update gks_acc_eidi_parastatikon set eidos_parastatikou_other_entity=1
  where id_acc_eidos_parastatikou in (11,12,13,14,15,16,21,22,23,24,31,32,51,52,61,62,71,81,82,141,142,143,144,502,503,504,505,902,903,912,913,952)");
}
gks_run_sql("update gks_acc_eidi_parastatikon set eidos_parastatikou_other_entity=1
where id_acc_eidos_parastatikou in (111,112,113,114,115)");



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_acc_inv_other_entity` (
  `id_acc_inv_other_entity` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `acc_inv_id` int(11) NOT NULL DEFAULT 0,
  `aade_entitytype_id` int(11) NOT NULL DEFAULT 0,
  `entity_user_id` int(11) NOT NULL DEFAULT 0,
  `address_extra` int(11) NOT NULL DEFAULT 0,
  `entity_afm` varchar(255) DEFAULT NULL,
  `entity_name` varchar(255) DEFAULT NULL,
  `entity_sub_name` varchar(255) DEFAULT NULL,
  `entity_branch` int(11) DEFAULT NULL,
  `entity_odos` varchar(255) DEFAULT NULL,
  `entity_arithmos` varchar(64) DEFAULT NULL,
  `entity_perioxi` varchar(255) DEFAULT NULL,
  `entity_poli` varchar(255) DEFAULT NULL,
  `entity_tk` varchar(24) DEFAULT NULL,
  `entity_country_id` int(11) NOT NULL DEFAULT 0,
  `entity_nomos_id` int(11) NOT NULL DEFAULT 0,
  `entity_latitude` double NOT NULL DEFAULT 0,
  `entity_longitude` double NOT NULL DEFAULT 0,
  `entity_aa` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_acc_inv_other_entity`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `acc_inv_id` (`acc_inv_id`),
  KEY `aade_entitytype_id` (`aade_entitytype_id`),
  KEY `entity_user_id` (`entity_user_id`),
  KEY `entity_afm` (entity_afm(190)),
  KEY `entity_aa` (entity_aa)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_whi_mov_other_entity` (
  `id_whi_mov_other_entity` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `whi_mov_id` int(11) NOT NULL DEFAULT 0,
  `aade_entitytype_id` int(11) NOT NULL DEFAULT 0,
  `entity_user_id` int(11) NOT NULL DEFAULT 0,
  `address_extra` int(11) NOT NULL DEFAULT 0,
  `entity_afm` varchar(255) DEFAULT NULL,
  `entity_name` varchar(255) DEFAULT NULL,
  `entity_sub_name` varchar(255) DEFAULT NULL,
  `entity_branch` int(11) DEFAULT NULL,
  `entity_odos` varchar(255) DEFAULT NULL,
  `entity_arithmos` varchar(64) DEFAULT NULL,
  `entity_perioxi` varchar(255) DEFAULT NULL,
  `entity_poli` varchar(255) DEFAULT NULL,
  `entity_tk` varchar(24) DEFAULT NULL,
  `entity_country_id` int(11) NOT NULL DEFAULT 0,
  `entity_nomos_id` int(11) NOT NULL DEFAULT 0,
  `entity_latitude` double NOT NULL DEFAULT 0,
  `entity_longitude` double NOT NULL DEFAULT 0,
  `entity_aa` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_whi_mov_other_entity`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `whi_mov_id` (`whi_mov_id`),
  KEY `aade_entitytype_id` (`aade_entitytype_id`),
  KEY `entity_user_id` (`entity_user_id`),
  KEY `entity_afm` (entity_afm(190)),
  KEY `entity_aa` (entity_aa)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



$sql="select acc_eidos_parastatikou_other_entity from gks_acc_journal limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_journal`
  add column acc_eidos_parastatikou_other_entity tinyint(4) NOT NULL DEFAULT 0 after acc_eidos_parastatikou_whi_id");
}

gks_run_sql("update gks_acc_eidi_parastatikon set eidos_parastatikou_aade_code='9.3' 
where id_acc_eidos_parastatikou in (912,913)");



$sql="SHOW INDEX FROM gks_acc_inv_other_entity where column_name='user_id_add';";
$result = gks_run_sql($sql);
if ($result->num_rows>0) {
  gks_run_sql("ALTER TABLE `gks_acc_inv_other_entity` 
  DROP INDEX `user_id_add`,
  ADD INDEX `mydate_edit`(`mydate_edit`);");
}

$sql="SHOW INDEX FROM gks_whi_mov_other_entity where column_name='user_id_add';";
$result = gks_run_sql($sql);
if ($result->num_rows>0) {
  gks_run_sql("ALTER TABLE `gks_whi_mov_other_entity` 
  DROP INDEX `user_id_add`,
  ADD INDEX `mydate_edit`(`mydate_edit`);");
}



//correlatedInvoices
gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_acc_inv_correlated_invoices` (
  `id_acc_inv_correlated_invoices` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `acc_inv_id` int(11) NOT NULL DEFAULT 0,

  `coi_mark` varchar(64) DEFAULT NULL,
  `coi_acc_inv_id` int(11) DEFAULT NULL,
  `coi_whi_mov_id` int(11) DEFAULT NULL,
  `coi_aa` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_acc_inv_correlated_invoices`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `acc_inv_id` (`acc_inv_id`),
  KEY `coi_mark` (`coi_mark`),
  KEY `coi_acc_inv_id` (`coi_acc_inv_id`),
  KEY `coi_whi_mov_id` (`coi_whi_mov_id`),
  KEY `coi_aa` (`coi_aa`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_acc_pay_correlated_invoices` (
  `id_acc_pay_correlated_invoices` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `acc_pay_id` int(11) NOT NULL DEFAULT 0,

  `coi_mark` varchar(64) DEFAULT NULL,
  `coi_acc_inv_id` int(11) DEFAULT NULL,
  `coi_acc_pay_id` int(11) DEFAULT NULL,
  `coi_whi_mov_id` int(11) DEFAULT NULL,
  `coi_aa` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_acc_pay_correlated_invoices`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `acc_pay_id` (`acc_pay_id`),
  KEY `coi_mark` (`coi_mark`),
  KEY `coi_acc_inv_id` (`coi_acc_inv_id`),
  KEY `coi_acc_pay_id` (`coi_acc_pay_id`),
  KEY `coi_whi_mov_id` (`coi_whi_mov_id`),
  KEY `coi_aa` (`coi_aa`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


//corinv -> coi
gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_whi_mov_correlated_invoices` (
  `id_whi_mov_correlated_invoices` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `whi_mov_id` int(11) NOT NULL DEFAULT 0,
  
  `coi_mark` varchar(64) DEFAULT NULL,
  `coi_acc_inv_id` int(11) DEFAULT NULL,
  `coi_whi_mov_id` int(11) DEFAULT NULL,
  `coi_aa` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_whi_mov_correlated_invoices`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `whi_mov_id` (`whi_mov_id`),
  KEY `coi_mark` (`coi_mark`),
  KEY `coi_acc_inv_id` (`coi_acc_inv_id`),
  KEY `coi_whi_mov_id` (`coi_whi_mov_id`),
  KEY `coi_aa` (`coi_aa`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select eidos_parastatikou_correlated_invoices from gks_acc_eidi_parastatikon limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_eidi_parastatikon`
  add column eidos_parastatikou_correlated_invoices tinyint(4) NOT NULL DEFAULT 0 
  after eidos_parastatikou_other_entity");
  
  gks_run_sql("update gks_acc_eidi_parastatikon set eidos_parastatikou_correlated_invoices=1
  where id_acc_eidos_parastatikou in (11,12,13,14,15,16,21,22,23,24,31,32,51,52,61,62,71,81,82,114,141,142,143,144,502,503,504,505,702,703,704,803,813,850)");
    
}


$sql="select journal_has_correlated_invoices from gks_acc_journal limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_journal`
  add column journal_has_correlated_invoices tinyint(4) NOT NULL DEFAULT 0 
  after acc_eidos_parastatikou_other_entity");
  
  gks_run_sql("UPDATE gks_acc_journal 
  SET journal_has_correlated_invoices = 1
  WHERE acc_eidos_parastatikou_id in (16,24,51,82,702,703,704,803,813,850)");


  
  
  //16 1.6 Τιμολόγιο Πώλησης / Συμπληρωματικό Παραστατικό
  //24 2.4 Τιμολόγιο Παροχής / Συμπληρωματικό Παραστατικό
  //51 5.1 Πιστωτικό Τιμολόγιο / Συσχετιζόμενο
  //82 8.2 Ειδικό Στοιχείο - Απόδειξης Είσπραξης Φόρου Διαμονής
  //702 Ακυρωτικό Παραστατικό
  //703 Ακυρωτική Πληρωμή
  //704 Ακυρωτικό Δελτίο
  //803 Επιστροφή είσπραξης σε πελάτες
  //813 Επιστροφή πληρωμής από προμηθευτές
  //850 8.5 Απόδειξη Επιστροφής POS

  
  
  
  gks_run_sql("UPDATE gks_acc_eidi_parastatikon set
  eidos_parastatikou_descr='Τέλος ανθεκτικότητας κλιματικής κρίσης'
  where id_acc_eidos_parastatikou=82");
  //από 8.2 Ειδικό Στοιχείο - Απόδειξης Είσπραξης Φόρου Διαμονής
}


gks_run_sql("update gks_acc_eidi_parastatikon set eidos_parastatikou_correlated_invoices=1
where id_acc_eidos_parastatikou in (114)");

gks_run_sql("update gks_acc_eidi_parastatikon set eidos_parastatikou_correlated_invoices=0
where id_acc_eidos_parastatikou in (902,903,912,913,952)");

gks_run_sql("update gks_acc_journal set journal_has_correlated_invoices=0 
where acc_eidos_parastatikou_id in (902,903,912,913,952)");



$sql="select aade_skopos_19_descr from gks_acc_inv limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_inv`
  add column aade_skopos_19_descr varchar(32) DEFAULT NULL after aade_skopos_diakinisis_id");
}

$sql="select aade_skopos_19_descr from gks_whi_mov limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_whi_mov`
  add column aade_skopos_19_descr varchar(32) DEFAULT NULL after aade_skopos_diakinisis_id");
}  


gks_run_sql("update gks_print_forms set
form_header=replace(form_header,
'{company_odos [Διεύθυνση: %%{company_tk [',
'{company_odos [Διεύθυνση: %% {company_arithmos}{company_tk [')
where form_header like '%{company_odos [Διεύθυνση: \%\%{company_tk [%'");

gks_run_sql("update gks_print_forms set
form_header=replace(form_header,
'{person_odos [Διεύθυνση: %%{person_tk [',
'{person_odos [Διεύθυνση: %% {person_arithmos}{person_tk [')
where form_header like '%{person_odos [Διεύθυνση: \%\%{person_tk [%'");

gks_run_sql("update gks_print_forms set
form_header=replace(form_header,
'{warehouse_from_odos [Διεύθυνση: %%{warehouse_from_tk [',
'{warehouse_from_odos [Διεύθυνση: %% {warehouse_from_arithmos}{warehouse_from_tk [')
where form_header like '%{warehouse_from_odos [Διεύθυνση: \%\%{warehouse_from_tk [%'");

gks_run_sql("update gks_print_forms set
form_header=replace(form_header,
'{warehouse_to_odos [Διεύθυνση: %%{warehouse_to_tk [',
'{warehouse_to_odos [Διεύθυνση: %% {warehouse_to_arithmos}{warehouse_to_tk [')
where form_header like '%{warehouse_to_odos [Διεύθυνση: \%\%{warehouse_to_tk [%'");

gks_run_sql("update gks_print_forms set
form_header=replace(form_header,
'{dest_odos [Διεύθυνση: %%{dest_tk [',
'{dest_odos [Διεύθυνση: %% {dest_arithmos}{dest_tk [')
where form_header like '%{dest_odos [Διεύθυνση: \%\%{dest_tk [%'");
  


$sql="select arc from gks_erp_app_ping limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_erp_app_ping`
  add column arc varchar(8) DEFAULT NULL");
} 


$sql="select app_mobile_userlogin_id from gks_pos limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_pos`
  add column app_mobile_userlogin_id int(11) NOT NULL DEFAULT 0");
} 

$sql="select erp_app_mobile_can_capture from gks_erp_app_mobile limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  
  $sql="select gks_erp_app_mobile_can_capture from gks_erp_app_mobile limit 1";
  $result = $db_link->query($sql);
  if ($result) { //no  error, exist, go rename
    gks_run_sql("ALTER TABLE `gks_erp_app_mobile` 
    CHANGE COLUMN `gks_erp_app_mobile_can_capture` `erp_app_mobile_can_capture` TINYINT(4) NOT NULL DEFAULT 0,
    CHANGE COLUMN `gks_erp_app_mobile_can_sms`     `erp_app_mobile_can_sms` TINYINT(4) NOT NULL DEFAULT 0,
    CHANGE COLUMN `gks_erp_app_mobile_can_pos`     `erp_app_mobile_can_pos` TINYINT(4) NOT NULL DEFAULT 0");
  } else {
    $sql="select erp_app_mobile_can_capture from gks_erp_app_mobile limit 1";
    $result = $db_link->query($sql);
    if (!$result) { //must return error
      gks_run_sql("ALTER TABLE `gks_erp_app_mobile`
      add column erp_app_mobile_can_capture tinyint(4) NOT NULL DEFAULT 0,
      add column erp_app_mobile_can_sms tinyint(4) NOT NULL DEFAULT 0,
      add column erp_app_mobile_can_pos tinyint(4) NOT NULL DEFAULT 0");
      gks_run_sql("update gks_erp_app_mobile set erp_app_mobile_can_capture=1,erp_app_mobile_can_sms=1,erp_app_mobile_can_pos=1");
    }  
  }
}


$sql="select erp_app_mobile_pos_list from gks_erp_app_mobile limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_erp_app_mobile`
  add column erp_app_mobile_pos_list text DEFAULT null");
}


//gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_public_file` (
//  `id_public_file` int(10) unsigned NOT NULL AUTO_INCREMENT,
//  `mydate_add` datetime DEFAULT NULL,
//  `mydate_edit` datetime DEFAULT NULL,
//  `user_id_add` int(11) NOT NULL DEFAULT 0,
//  `user_id_edit` int(11) NOT NULL DEFAULT 0,
//  `myip` varchar(48) DEFAULT NULL,
//  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
//  `myfs`   varchar(64) DEFAULT NULL,
//  `myfile` varchar(1024) DEFAULT NULL,
//  `expire_date` datetime DEFAULT NULL,
//  `shortcode` varchar(64) DEFAULT NULL,
//  `myopencount` int NOT NULL DEFAULT 0,
//  PRIMARY KEY (`id_public_file`),
//  KEY `mydate_edit` (`mydate_edit`),
//  KEY `user_id_edit` (`user_id_edit`),
//  KEY `myfs` (`myfs`),
//  KEY `myfile` (`myfile`(250)),
//  KEY `expire_date` (`expire_date`),
//  KEY `shortcode` (`shortcode`),
//  KEY `myopencount` (`myopencount`)
//) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");	




$sql="select pos_id from gks_acc_inv limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_inv`
  add column pos_id int NOT NULL DEFAULT 0,
  add column erp_app_mobile_id int NOT NULL DEFAULT 0,
  add index pos_id (pos_id),
  add index erp_app_mobile_id (erp_app_mobile_id)");
}


$sql="select display_name from gks_sms limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_sms`
  add column display_name varchar(190) DEFAULT NULL,
  add index display_name(display_name)");
}

$sql="select erp_app_mobile_country from gks_erp_app_mobile limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_erp_app_mobile`
  add column erp_app_mobile_country varchar(16) DEFAULT NULL after erp_app_mobile_name");
  
  gks_run_sql("update gks_erp_app_mobile set erp_app_mobile_country='+30'");
  gks_run_sql("update gks_erp_app_mobile set erp_app_mobile_phonenumber=SUBSTRING(erp_app_mobile_phonenumber,3,10) where erp_app_mobile_phonenumber like '3069%'");
  
   
  
	$sql="SELECT gks_users_communication.user_id as id
  FROM gks_users_communication
  WHERE comm_type='phone' AND comm_value<>''
  GROUP BY gks_users_communication.user_id;";
  
  $result = $db_link->query($sql);
  if (!$result) {echo 'error sql '.$sql;die();}
	$data=array();
  while ($row = $result->fetch_assoc()) {
    $data[]=$row['id'];
  }
  foreach ($data as $value) {
     gks_user_fix_phone_numbers($value);
  }
    
}



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_mass_messages` (
  `id_mass_message` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_send_start` datetime DEFAULT NULL,
  `date_send_end` datetime DEFAULT NULL,
  `sender_sms_provider` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `sender_sms_sender` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `mymessage` text COLLATE utf8mb4_unicode_520_ci,
  `send_with_viber` tinyint(4) NOT NULL DEFAULT '0',
  `send_with_sms` tinyint(4) NOT NULL DEFAULT '0',
  `send_with_email` tinyint(4) NOT NULL DEFAULT '0',
  `viber_from` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `email_from` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `email_subject` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `email_template` varchar(190) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `mylist` longtext COLLATE utf8mb4_unicode_520_ci,
  `myresult` longtext COLLATE utf8mb4_unicode_520_ci,
  `mybuttons` longtext COLLATE utf8mb4_unicode_520_ci,
  `cc_all` int(11) NOT NULL DEFAULT '0',
  `cc_viber` int(11) NOT NULL DEFAULT '0',
  `cc_sms` int(11) NOT NULL DEFAULT '0',
  `cc_email` int(11) NOT NULL DEFAULT '0',
  `cc_none` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_mass_message`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `date_send_start` (`date_send_start`),
  KEY `sender_sms_provider` (`sender_sms_provider`),
  KEY `sender_sms_sender` (`sender_sms_sender`),
  KEY `mymessage` (`mymessage`(190)),
  KEY `send_with_viber` (`send_with_viber`),
  KEY `send_with_sms` (`send_with_sms`),
  KEY `send_with_email` (`send_with_email`),
  KEY `cc_all` (`cc_all`),
  KEY `cc_viber` (`cc_viber`),
  KEY `cc_email` (`cc_email`),
  KEY `cc_none` (`cc_none`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select date_view from gks_email limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_email`
  add column date_view datetime DEFAULT NULL");
}


$sql="select erp_app_mobile_can_gps from gks_erp_app_mobile limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_erp_app_mobile`
  add column erp_app_mobile_can_gps tinyint(4) NOT NULL DEFAULT 0,
  add column erp_app_mobile_gps_dt int(11) NOT NULL DEFAULT 0,
  add column erp_app_mobile_gps_ds int(11) NOT NULL DEFAULT 0,
  add column erp_app_mobile_gps_chunk int(11) NOT NULL DEFAULT 0,
  add column erp_app_mobile_gps_timegap int(11) NOT NULL DEFAULT 0");
  gks_run_sql("update gks_erp_app_mobile set erp_app_mobile_can_gps=1,erp_app_mobile_gps_dt=30,erp_app_mobile_gps_ds=50,erp_app_mobile_gps_chunk=10,erp_app_mobile_gps_timegap=900");
}



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_gps` (
  `id_gps` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `erp_app_mobile_id` int(11) NOT NULL DEFAULT 0,
  `myaa` int(11) NOT NULL DEFAULT 0,
  `mylat` double NOT NULL DEFAULT 0,
  `mylng` double NOT NULL DEFAULT 0,
  `myprovider` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `mytime` datetime DEFAULT NULL,
  `mydiadromi` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  PRIMARY KEY (`id_gps`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `erp_app_mobile_id` (`erp_app_mobile_id`),
  KEY `myaa` (`myaa`),
  KEY `myprovider` (`myprovider`),
  KEY `mytime` (`mytime`),
  KEY `mydiadromi` (`mydiadromi`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("update gks_permission_object set
card_title='CRM',
sortorder=4350
where id_permission_object=399");


gks_run_sql("update gks_permission_object set
object_name='Περιοχές'
where id_permission_object=2180");

$sql="select company_id from gks_transfer_area limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  
  gks_run_sql("ALTER TABLE gks_transfer_area
  add column `company_id` int(11) NOT NULL DEFAULT 0,
  add column `company_sub_id` int(11) NOT NULL DEFAULT 0,
  add column `responsible_id` int(11) NOT NULL DEFAULT 0,
  add column `warehouse_id` int(11) NOT NULL DEFAULT 0 after responsible_id,
  add column `transfer_area_phone` varchar(64) DEFAULT NULL,
  add column `transfer_area_email` varchar(64) DEFAULT NULL,
  add column `transfer_area_website` varchar(255) DEFAULT NULL,
  add column `transfer_area_odos` varchar(255) DEFAULT NULL,
  add column `transfer_area_arithmos` varchar(64) DEFAULT NULL,
  add column `transfer_area_perioxi` varchar(255) DEFAULT NULL,
  add column `transfer_area_poli` varchar(255) DEFAULT NULL,
  add column `transfer_area_tk` varchar(24) DEFAULT NULL,
  add column `transfer_area_nomos_id` int(11) NOT NULL DEFAULT 0,
  add column `transfer_area_country_id` int(11) NOT NULL DEFAULT 0,
  
  add column `transfer_area_map_latitude` double NOT NULL DEFAULT 0,
  add column `transfer_area_map_longitude` double NOT NULL DEFAULT 0,
  add column `transfer_area_areas` longtext DEFAULT NULL,
  add column `transfer_area_bound_north` double NOT NULL DEFAULT 0,
  add column `transfer_area_bound_south` double NOT NULL DEFAULT 0,
  add column `transfer_area_bound_east`  double NOT NULL DEFAULT 0,
  add column `transfer_area_bound_west`  double NOT NULL DEFAULT 0,
  
  add index `company_id` (`company_id`),
  add index `company_sub_id` (`company_sub_id`),
  add index `responsible_id` (`responsible_id`),
  add index `warehouse_id` (`warehouse_id`),
  add index `transfer_area_odos` (`transfer_area_odos`(250)),
  add index `transfer_area_tk` (`transfer_area_tk`),
  add index `transfer_area_nomos_id` (`transfer_area_nomos_id`),
  add index `transfer_area_phone` (`transfer_area_phone`),
  add index `transfer_area_email` (`transfer_area_email`),
  add index `transfer_area_poli` (`transfer_area_poli`(250)),
  add index `transfer_area_country_id` (`transfer_area_country_id`),
  
  add index  `transfer_area_bound_north` (`transfer_area_bound_north`),
  add index  `transfer_area_bound_south` (`transfer_area_bound_south`),
  add index  `transfer_area_bound_east`  (`transfer_area_bound_east`),
  add index  `transfer_area_bound_west`  (`transfer_area_bound_west`)");
}



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_area2externalpartner` (
  `id_transfer_area2externalpartner` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `transfer_area_id` int(11) NOT NULL DEFAULT 0,
  `transfer_user_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_transfer_area2externalpartner`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `transfer_area_id` (`transfer_area_id`),
  KEY `transfer_user_id` (`transfer_user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("update gks_nomoi set nomos_descr='Πέλλας' where id_nomos=45");


$sql="select * from gks_permission_object where id_permission_object=2130";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (2130,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer',0,'gks_transfer_manage','Διαχείριση',21300)");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  )  AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",2130,1,1,1,1,1)");
  }

}


$sql="select transfer_area_id from gks_transfer_reservation limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  
  gks_run_sql("ALTER TABLE gks_transfer_reservation
  add column transfer_area_id int(11) NOT NULL DEFAULT 0 after transfer_id,
  add index `user_id_add` (`user_id_add`),
  add index `transfer_reservation_journal_id` (`transfer_reservation_journal_id`),
  add index `transfer_reservation_seira_id` (`transfer_reservation_seira_id`),
  add index `ma_nomos_id` (`ma_nomos_id`),
  add index `transfer_area_id` (`transfer_area_id`)");
  
}

$sql="select transfer_oxima_type_code from gks_transfer_oxima_type limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  
  gks_run_sql("ALTER TABLE gks_transfer_oxima_type
  add column transfer_oxima_type_code varchar(48) DEFAULT NULL after product_id,
  add index `transfer_oxima_type_code` (`transfer_oxima_type_code`)");
  
}


$sql="select * from gks_permission_object where id_permission_object=266";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("alter table `gks_permission_object`
   add column `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()");
   
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (266,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','CRM',0,'gks_sms_chat','Συζήτηση SMS',4971),
   (267,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','CRM',0,'gks_mass_messages','Μαζική Αποστολή SMS-Viber-email',4970)");
   
  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  )  AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",266,1,1,1,1,1),
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",267,1,1,1,1,1)");
  }
  
  

  gks_run_sql("update gks_permission_object set card_title='CRM', sortorder=4975 where id_permission_object=250");
  gks_run_sql("update gks_permission_object set card_title='CRM', sortorder=4976 where id_permission_object=260");
  gks_run_sql("update gks_permission_object set card_title='CRM', sortorder=4972 where id_permission_object=265");
  gks_run_sql("update gks_permission_object set card_title='CRM', sortorder=4974 where id_permission_object=270");
  gks_run_sql("update gks_permission_object set card_title='CRM', sortorder=4973 where id_permission_object=271");
  
  gks_run_sql("update gks_permission_object set sortorder=4990 where id_permission_object=498");
  gks_run_sql("update gks_permission_object set sortorder=4991 where id_permission_object=497");
  

}


$sql="select sms_id from gks_orders_messages limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  
  gks_run_sql("ALTER TABLE gks_orders_messages
  add column sms_id int(11) NOT NULL DEFAULT 0 after email_id,
  add index sms_id (sms_id);");
  
  gks_run_sql("ALTER TABLE gks_acc_inv_messages
  add column sms_id int(11) NOT NULL DEFAULT 0 after email_id,
  add index sms_id (sms_id);");

  gks_run_sql("ALTER TABLE gks_acc_pay_messages
  add column sms_id int(11) NOT NULL DEFAULT 0 after email_id,
  add index sms_id (sms_id);");

  gks_run_sql("ALTER TABLE gks_whi_mov_messages
  add column sms_id int(11) NOT NULL DEFAULT 0 after email_id,
  add index sms_id (sms_id);");

  gks_run_sql("ALTER TABLE gks_hotel_reservation_messages
  add column sms_id int(11) NOT NULL DEFAULT 0 after email_id,
  add index sms_id (sms_id);");

  gks_run_sql("ALTER TABLE gks_transfer_reservation_messages
  add column sms_id int(11) NOT NULL DEFAULT 0 after email_id,
  add index sms_id (sms_id);");

  gks_run_sql("ALTER TABLE gks_crm_tasks_messages
  add column sms_id int(11) NOT NULL DEFAULT 0 after email_id,
  add index sms_id (sms_id);");

  gks_run_sql("ALTER TABLE gks_crm_leads_messages
  add column sms_id int(11) NOT NULL DEFAULT 0 after email_id,
  add index sms_id (sms_id);");

  gks_run_sql("ALTER TABLE gks_crm_machine_messages
  add column sms_id int(11) NOT NULL DEFAULT 0 after email_id,
  add index sms_id (sms_id);");
  
}

$sql="select transfer_oxima_driver_id from gks_transfer_reservation_oximata limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error

  gks_run_sql("ALTER TABLE gks_transfer_reservation_oximata
  add column transfer_oxima_driver_id int(11) NOT NULL DEFAULT 0 after transfer_oxima_asset_id,
  add index transfer_oxima_driver_id (transfer_oxima_driver_id),
  add column dromologio_id int(11) NOT NULL DEFAULT 0 after transfer_oxima_driver_id,
  add index dromologio_id (dromologio_id),
  add column externalpartner_id int(11) NOT NULL DEFAULT 0 after dromologio_id,
  add index externalpartner_id (externalpartner_id)");

}



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_transfer_dromologio` (
  `id_transfer_dromologio` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `dromologio_descr` varchar(190) DEFAULT NULL,
  `dromologio_area_id`  int(11) NOT NULL DEFAULT 0,
  `dromologio_asset_id` int(11) NOT NULL DEFAULT 0,
  `dromologio_driver_id` int(11) NOT NULL DEFAULT 0,
  `dromologio_start` datetime DEFAULT NULL,
  `dromologio_end` datetime DEFAULT NULL,
  
  PRIMARY KEY (`id_transfer_dromologio`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `dromologio_descr` (`dromologio_descr`),
  KEY `dromologio_area_id` (`dromologio_area_id`),
  KEY `dromologio_asset_id` (`dromologio_asset_id`),
  KEY `dromologio_driver_id` (`dromologio_driver_id`),
  KEY `dromologio_start` (`dromologio_start`),
  KEY `dromologio_end` (`dromologio_end`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



$sql="select * from gks_permission_object where id_permission_object=2150";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (2150,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer',0,'gks_transfer_dromologio','Δρομολόγια',21500)");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  )  AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",2150,1,1,1,1,1)");
  }

}

$sql="select transfer_oxima_type_id from gks_assets limit 1";
$result = $db_link->query($sql);
if ($result) { //must NOT return error
  gks_run_sql("ALTER TABLE `gks_assets` drop COLUMN `transfer_oxima_type_id`");
}

gks_run_sql("update gks_permission_object set object_name='Κρατήσεις' where id_permission_object=2120");


gks_run_sql("ALTER TABLE `gks_settings_users` 
MODIFY COLUMN `mysubobject` VARCHAR(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL;");

$sql="select pricelist_item_product_id from gks_eshop_pricelist_items limit 1";
$result = $db_link->query($sql);
if ($result) { //must NOT return error
  gks_run_sql("ALTER TABLE `gks_eshop_pricelist_items` 
  drop COLUMN `pricelist_item_product_id`,
  drop COLUMN `pricelist_item_category_id`,
  add column `pricelist_item_min_price` double NOT NULL DEFAULT 0,
  add column `pricelist_item_max_price` double NOT NULL DEFAULT 0,
  add column `pricelist_item_individual_use` tinyint(4) NOT NULL DEFAULT 0,
  add column `pricelist_item_exclude_sale_items` tinyint(4) NOT NULL DEFAULT 0,
  add column `pricelist_item_users_emails` text DEFAULT NULL,
  add column `pricelist_item_usage_limit` int NOT NULL DEFAULT 0,
  add column `pricelist_item_limit_usage_to_x_items` int NOT NULL DEFAULT 0,
  add column `pricelist_item_usage_limit_per_user` int NOT NULL DEFAULT 0,
  add index   pricelist_item_individual_use (pricelist_item_individual_use)");
  
}

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_eshop_pricelist_items_products` (
  `id_pricelist_item_product` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pricelist_item_id` int(11) NOT NULL DEFAULT 0,
  `product_id` int(11) NOT NULL DEFAULT 0,
  `is_include` tinyint(4) NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `myip` varchar(48) DEFAULT NULL,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_pricelist_item_product`),
  KEY `pricelist_item_id` (`pricelist_item_id`),
  KEY `product_id` (`product_id`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_eshop_pricelist_items_categories` (
  `id_pricelist_item_category` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pricelist_item_id` int(11) NOT NULL DEFAULT 0,
  `product_category_id` int(11) NOT NULL DEFAULT 0,
  `is_include` tinyint(4) NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `myip` varchar(48) DEFAULT NULL,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_pricelist_item_category`),
  KEY `pricelist_item_id` (`pricelist_item_id`),
  KEY `product_category_id` (`product_category_id`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_eshop_pricelist_items_brands` (
  `id_pricelist_item_brand` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pricelist_item_id` int(11) NOT NULL DEFAULT 0,
  `product_brand_id` int(11) NOT NULL DEFAULT 0,
  `is_include` tinyint(4) NOT NULL DEFAULT 0,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `myip` varchar(48) DEFAULT NULL,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_pricelist_item_brand`),
  KEY `pricelist_item_id` (`pricelist_item_id`),
  KEY `product_brand_id` (`product_brand_id`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");




gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_eshop_pricelist_photo` (
  `id_pricelist_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pricelist_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(255) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_pricelist_photo`) USING BTREE,
  KEY `pricelist_id` (`pricelist_id`),
  KEY `photo_url` (`photo_url`(250)),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_eshop_pricelist_items_photo` (
  `id_pricelist_item_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pricelist_item_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(255) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_pricelist_item_photo`) USING BTREE,
  KEY `pricelist_item_id` (`pricelist_item_id`),
  KEY `photo_url` (`photo_url`(250)),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_woo_coupons` (
  `id_woo_coupon` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `pricelist_item_id` int(11) NOT NULL DEFAULT 0,
  `eshop_id` int(11) NOT NULL DEFAULT 0,
  `remote_coupon_id` int(11) NOT NULL DEFAULT 0,
  `last_update_user_id` int(11) NOT NULL DEFAULT 0,
  `last_update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id_woo_coupon`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `pricelist_item_id` (`pricelist_item_id`),
  KEY `eshop_id` (`eshop_id`),
  KEY `remote_coupon_id` (`remote_coupon_id`),
  KEY `last_update_user_id` (`last_update_user_id`),
  KEY `last_update_date` (`last_update_date`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



gks_run_sql("ALTER TABLE `gks_eshop_pricelist` AUTO_INCREMENT = 10001;");
gks_run_sql("ALTER TABLE `gks_eshop_pricelist_items` AUTO_INCREMENT = 10001;");


$sql="select acc_inv_ref_number from gks_acc_inv limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_inv` 
  ADD COLUMN `acc_inv_ref_number` varchar(64) DEFAULT NULL after inv_guid,
  ADD INDEX acc_inv_ref_number (acc_inv_ref_number)");
}

$sql="select order_ref_number from gks_orders limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_orders` 
  ADD COLUMN `order_ref_number` varchar(64) DEFAULT NULL after order_guid,
  ADD INDEX order_ref_number (order_ref_number)");
}



$sql="select * from gks_custom_table where id_custom_table=51";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_custom_table` (`id_custom_table`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`custom_table_descr`,`custom_table_name`,`field_name_id_parent`,`field_name_id_current`,`custom_table_disabled`,`custom_priv`,`custom_sortorder`,obj_url) VALUES 
   (51,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση - Τιμοκατάλογοι','gks_eshop_pricelist','id_pricelist','pricelist_custom_id',0,'base',60,'admin-pricelists.php'),
   (52,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Διαχείριση - Στοιχεία Τιμοκαταλόγου','gks_eshop_pricelist_items','id_pricelist_item','pricelist_item_id',0,'base',61,'admin-pricelists-items.php')");
}

$sql="select * from gks_crm_activity_objects where id_crm_activity_object=52";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("update gks_crm_activity_objects set crm_activity_object_descr='Τιμοκατάλογος Transfer' where id_crm_activity_object=41");
  gks_run_sql("INSERT INTO `gks_crm_activity_objects` (`id_crm_activity_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`crm_activity_object_code`,`crm_activity_object_descr`,`crm_activity_object_sortorder`,`crm_activity_object_disabled`,`crm_activity_object_page`) VALUES 
   (52,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_eshop_pricelist',      'Τιμοκατάλογος',                 52,0,'admin-pricelists-item.php?id=%s'),
   (53,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_eshop_pricelist_items','Στοιχείο Τιμοκαταλόγου-Κουπόνι',53,0,'admin-pricelists-items-item.php?id=%s')");
}


gks_run_sql("update gks_aade_katigoria_fpa set aade_katigoria_fpa_descr='Άνευ Φ.Π.Α.' where id_aade_katigoria_fpa=7");

$sql="select product_fpa_aade_id from gks_acc_inv_products limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_inv_products` 
  ADD COLUMN `product_fpa_aade_id` int(11) DEFAULT 0 after product_fpa_base_id,
  ADD INDEX product_fpa_aade_id (product_fpa_aade_id)");
}

$sql="select product_fpa_aade_id from gks_orders_products limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_orders_products` 
  ADD COLUMN `product_fpa_aade_id` int(11) DEFAULT 0 after product_fpa_base_id,
  ADD INDEX product_fpa_aade_id (product_fpa_aade_id)");
}

$sql="select direct_fpa_id from gks_aade_katigoria_fpa limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_aade_katigoria_fpa` 
  ADD COLUMN `direct_fpa_id` int(11) DEFAULT 0,
  ADD INDEX direct_fpa_id (direct_fpa_id)");
  
  gks_run_sql("update gks_aade_katigoria_fpa set direct_fpa_id=3001	where id_aade_katigoria_fpa=1");
  gks_run_sql("update gks_aade_katigoria_fpa set direct_fpa_id=3002	where id_aade_katigoria_fpa=2");
  gks_run_sql("update gks_aade_katigoria_fpa set direct_fpa_id=3003	where id_aade_katigoria_fpa=3");
  gks_run_sql("update gks_aade_katigoria_fpa set direct_fpa_id=3004	where id_aade_katigoria_fpa=4");
  gks_run_sql("update gks_aade_katigoria_fpa set direct_fpa_id=3005	where id_aade_katigoria_fpa=5");
  gks_run_sql("update gks_aade_katigoria_fpa set direct_fpa_id=3006	where id_aade_katigoria_fpa=6");
  gks_run_sql("update gks_aade_katigoria_fpa set direct_fpa_id=3007	where id_aade_katigoria_fpa=7");
  gks_run_sql("update gks_aade_katigoria_fpa set direct_fpa_id=3008	where id_aade_katigoria_fpa=8");
  gks_run_sql("update gks_aade_katigoria_fpa set direct_fpa_id=3009	where id_aade_katigoria_fpa=9");
  gks_run_sql("update gks_aade_katigoria_fpa set direct_fpa_id=3010	where id_aade_katigoria_fpa=10");
  
}



$sql="select * from gks_eshop_fpa where id_fpa=3001";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("insert into gks_eshop_fpa (
  id_fpa,fpa_descr,fpa_descr_short,fpa_descr_print,fpa_pososto,fpa_sortorder,aade_katigoria_fpa_id
  ) values 
  (3001,'ΦΠΑ συντελεστής 24%',                   'ΓΦ24%',        '24%',0.24,3001,1),
  (3002,'ΦΠΑ συντελεστής 13%',                   'ΓΦ13%',        '13%',0.13,3002,2),
  (3003,'ΦΠΑ συντελεστής 6%',                    'ΓΦ6%',         '6%', 0.06,3003,3),
  (3004,'ΦΠΑ συντελεστής 17%',                   'ΓΦ17%',        '17%',0.17,3004,4),
  (3005,'ΦΠΑ συντελεστής 9%',                    'ΓΦ9%',         '9%', 0.09,3005,5),
  (3006,'ΦΠΑ συντελεστής 4%',                    'ΓΦ4%',         '4%', 0.04,3006,6),
  (3007,'Άνευ Φ.Π.Α.',                           'ΓΦ0%',         '0%', 0,   3007,7),
  (3008,'Εγγραφές χωρίς ΦΠΑ',                    'ΓΦ0%',         '0%', 0,   3008,8),
  (3009,'ΦΠΑ συντελεστής 3% (αρ.31 ν.5057/2023)','ΓΦ3% α31/5057','3%', 0.03,3009,9),
  (3010,'ΦΠΑ συντελεστής 4% (αρ.31 ν.5057/2023)','ΓΦ4% α31/5057','4%', 0.04,3010,10);");
}

gks_run_sql("update gks_country set country_ee=null where id_country=79 and country_ee is not null"); //Ηνωμένο Βασίλειο






$sql="select shortcode_prefix from gks_custom_table limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_custom_table` 
  ADD COLUMN `shortcode_prefix` varchar(8) DEFAULT NULL,
  ADD INDEX shortcode_prefix (shortcode_prefix)");
}

$sql="select id_custom_table from gks_custom_table where shortcode_prefix is null or shortcode_prefix=''";
$result = gks_run_sql($sql);
$list=array();
while ($row = $result->fetch_assoc()) $list[]=$row['id_custom_table'];
foreach ($list as $row) {
  $shortcode_prefix=gks_shortcode_prefix_for_custom_table();
  gks_run_sql("update gks_custom_table 
  set shortcode_prefix='".$db_link->escape_string($shortcode_prefix)."'
  where id_custom_table=".$row);
}









$sql="select * from gks_public_file limit 1";	
$result = $db_link->query($sql);
if ($result) { //no  error, exist, go drop
  gks_run_sql("drop table gks_public_file");
}

$sql="select pos_sms_erp_app_mobile_id from gks_pos limit 1";
$result = $db_link->query($sql);
if ($result) { //must NOT return error
  gks_run_sql("ALTER TABLE `gks_pos` 
  drop COLUMN `pos_sms_erp_app_mobile_id`");
}

$sql="select pos_sms_erp_app_mobile_id_code from gks_pos limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_pos` 
  ADD COLUMN `pos_sms_erp_app_mobile_id_code` varchar(64) DEFAULT NULL");
}

$sql="select pos_sms_template_text from gks_pos limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_pos` 
  ADD COLUMN `pos_sms_template_text` varchar(190) DEFAULT NULL");
}

$sql="select pos_auto_click_start_at_paywith from gks_pos limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_pos` 
  ADD COLUMN `pos_auto_click_start_at_paywith` tinyint(4) NOT NULL DEFAULT 0,
  ADD COLUMN `pos_can_search_products` tinyint(4) NOT NULL DEFAULT 0");
}

$sql="select * from gks_customt_gks_eshop_pricelist limit 1";
$result = $db_link->query($sql);
if ($result) { //no  error, exist, go drop
  $sql="select pricelist_id from gks_customt_gks_eshop_pricelist limit 1";
  $result = $db_link->query($sql);
  if (!$result) { //must return error
    gks_run_sql("ALTER TABLE `gks_customt_gks_eshop_pricelist` 
    CHANGE COLUMN `pricelist_custom_id` `pricelist_id` INT(11) NOT NULL DEFAULT 0,
    DROP INDEX `pricelist_custom_id`,
    ADD INDEX `pricelist_custom_id` USING BTREE(`pricelist_id`);");
  
    gks_run_sql("update gks_custom_table set field_name_id_current='pricelist_id' where id_custom_table=51");
  }
}




$sql="select pos_thermal_form_id from gks_pos limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_pos` 
  ADD COLUMN `pos_thermal_form_id` int(11) DEFAULT 0 after pos_print_form_id");
}

$sql="select erp_app_mobile_local_printers from gks_erp_app_mobile limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_erp_app_mobile` 
  ADD COLUMN `erp_app_mobile_local_printers` longtext DEFAULT null");
}


$sql="select erp_app_mobile_user_id from gks_erp_app_mobile limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_erp_app_mobile` 
  ADD COLUMN `erp_app_mobile_user_id` int(11) DEFAULT 0 after erp_app_mobile_token,
  add index erp_app_mobile_user_id (erp_app_mobile_user_id)");
}

$sql="select erp_app_mobile_user_token from gks_erp_app_mobile limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_erp_app_mobile` 
  ADD COLUMN `erp_app_mobile_user_token` varchar(190) DEFAULT null after erp_app_mobile_user_id,
  add index erp_app_mobile_user_token (erp_app_mobile_user_token)");
}







$sql="select * from gks_permission_object where id_permission_object=499";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (499,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','CRM',0,'gks_crm_machine_pivot11','Pivot Table - Συσκευές',4992)");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  )  AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",499,1,1,1,1,1)");
  }
  
}


$sql="select * from gks_payment_acquirer_with where id_payment_acquirer_with=6";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_payment_acquirer_with` (`id_payment_acquirer_with`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`payment_paroxos_name`,`payment_paroxos_implemented`,`payment_paroxos_sortorder`) VALUES 
   (6,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Worldline',0,6)");

}


$sql="select worldline_mid from gks_company limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_company
	add column `worldline_mid` varchar(190) DEFAULT NULL,
  add column `worldline_username` varchar(190) DEFAULT NULL,
  add column `worldline_password` varchar(190) DEFAULT NULL,
  add column `worldline_authorization_code` varchar(190) DEFAULT NULL,
  add column `worldline_x_api_key` varchar(190) DEFAULT NULL");
  
}

$sql="select worldline_id from gks_assets limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_assets
  add column `worldline_id` varchar(190) DEFAULT NULL,
  add column `worldline_terminal_id` varchar(190) DEFAULT NULL,
  add column `worldline_company_id` int(11) NOT NULL DEFAULT 0,
  add index `worldline_id` (`worldline_id`),
  add index `worldline_terminal_id` (`worldline_terminal_id`)");
}


$sql="select * from gks_permission_object where id_permission_object=856";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (856,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Λογιστική',0,'gks_worldline_transaction','Συναλλαγές Worldline',8551)");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  )  AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",856,1,1,1,1,1)");
  }
}

$sql="select merchant_ref_trns from gks_acc_inv limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_acc_inv
  add column `merchant_ref_trns` varchar(190) DEFAULT NULL after assigned_id,
  add index merchant_ref_trns (merchant_ref_trns)");
}

$sql="select product_gtin from gks_eshop_products limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_eshop_products
  add column `product_gtin` varchar(128) DEFAULT NULL after product_sku,
  add index product_gtin (product_gtin),
  add column `product_upc` varchar(128) DEFAULT NULL after product_gtin,
  add index product_upc (product_upc),
  add column `product_ean` varchar(128) DEFAULT NULL after product_upc,
  add index product_ean (product_ean),
  add column `product_isbn` varchar(128) DEFAULT NULL after product_ean,
  add index product_isbn (product_isbn)");
}

$sql="select pos_indexeddb from gks_pos limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_pos
  add column `pos_indexeddb` tinyint(4) NOT NULL DEFAULT 0");
}

$sql="select PosEntryMode from gks_worldline_transaction limit 1";
$result = $db_link->query($sql);
if ($result) { //must NOT return error
	gks_run_sql("drop table gks_worldline_transaction");
}


gks_run_sql("CREATE TABLE IF NOT  EXISTS `gks_worldline_transaction` (
  `id_worldline_transaction` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `eftpos_transaction_id` int(11) DEFAULT NULL,
  `intentId` varchar(190) DEFAULT NULL,
  `myStatus` tinyint(4) NOT NULL DEFAULT 0,
  `myResult` tinyint(4) NOT NULL DEFAULT 0,
  `xeiristis_id` int(11) DEFAULT NULL,
  `add_from_system` varchar(64) DEFAULT NULL,
  `myfrom` varchar(64) DEFAULT NULL,
  `TransactionId_org` varchar(190) DEFAULT NULL,
  `Approved` tinyint(4) NOT NULL DEFAULT 0,
  `Voided` tinyint(4) NOT NULL DEFAULT 0,
  `myerror` text DEFAULT NULL,
  `myjson` text DEFAULT NULL,
  `aadeTransactionId` varchar(190) DEFAULT NULL,
  `CustomerEmail` varchar(190) DEFAULT NULL,
  `CustomerPhone` varchar(190) DEFAULT NULL,
  `Instalments` int(11) DEFAULT NULL,
  `TipAmount` double DEFAULT NULL,
  
  `message_type` varchar(190) DEFAULT NULL,
  `trn_id` varchar(190) DEFAULT NULL,
  `trn_debit_id` varchar(190) DEFAULT NULL,
  `trn_date_time` datetime DEFAULT NULL,
  `trn_type` varchar(190) DEFAULT NULL,
  `trn_type_name` varchar(190) DEFAULT NULL,
  `trn_status_code` int(11) DEFAULT NULL,
  `trn_reference_label` varchar(190) DEFAULT NULL,
  `trn_token` varchar(190) DEFAULT NULL,
  `trn_currency` varchar(190) DEFAULT NULL,
  `trn_amount` double DEFAULT NULL,
  
  `trn_host_rrn` varchar(190) DEFAULT NULL,
  `trn_host_auth_code` varchar(190) DEFAULT NULL,
  `trn_host_resp_code` varchar(190) DEFAULT NULL,
  `trn_host_error_desc` varchar(190) DEFAULT NULL,
  `trn_batch` varchar(190) DEFAULT NULL,
  `trn_stan` varchar(190) DEFAULT NULL,
  `trn_info_data` varchar(190) DEFAULT NULL,
  `merchant_bank_id` varchar(190) DEFAULT NULL,
  `merchant_id` varchar(190) DEFAULT NULL,
  `merchant_name` varchar(190) DEFAULT NULL,
  `merchant_taxpayer_id` varchar(190) DEFAULT NULL,
  `merchant_terminal_id` varchar(190) DEFAULT NULL,
  `merchant_terminal_description` varchar(190) DEFAULT NULL,
  `merchant_terminal_location` varchar(190) DEFAULT NULL,
  `merchant_terminal_city` varchar(190) DEFAULT NULL,
  `merchant_terminal_state` varchar(190) DEFAULT NULL,
  `merchant_terminal_country` varchar(190) DEFAULT NULL,
  `merchant_device_id` varchar(190) DEFAULT NULL,
  `trn_id_gr_aade` varchar(190) DEFAULT NULL,
  `card_pan` varchar(190) DEFAULT NULL,
  `card_aid` varchar(190) DEFAULT NULL,
  `card_label` varchar(190) DEFAULT NULL,
  `card_cvm_pin` varchar(190) DEFAULT NULL,
  `card_cvm_cdcvm` varchar(190) DEFAULT NULL,
  `card_tvr` varchar(190) DEFAULT NULL,
  `card_kvr` varchar(190) DEFAULT NULL,
  `card_cda_res` varchar(190) DEFAULT NULL,
  
  PRIMARY KEY (`id_worldline_transaction`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `eftpos_transaction_id` (`eftpos_transaction_id`),
  KEY `intentId` (`intentId`),
  KEY `myStatus` (`myStatus`),
  KEY `myResult` (`myResult`),
  KEY `xeiristis_id` (`xeiristis_id`),
  KEY `add_from_system` (`add_from_system`),
  KEY `myfrom` (`myfrom`),
  KEY `aadeTransactionId` (`aadeTransactionId`),
  KEY `myerror` (`myerror`(190)),
  KEY `CustomerEmail` (`CustomerEmail`),
  KEY `CustomerPhone` (`CustomerPhone`),
  KEY `Instalments` (`Instalments`),
  KEY `TipAmount` (`tipAmount`),
  KEY `message_type` (`message_type`),
  KEY `trn_id` (`trn_id`),
  KEY `trn_date_time` (`trn_date_time`),
  KEY `trn_type` (`trn_type`),
  KEY `trn_status_code` (`trn_status_code`),
  KEY `trn_token` (`trn_token`),
  KEY `trn_host_rrn` (`trn_host_rrn`),
  KEY `merchant_id` (`merchant_id`),
  KEY `merchant_terminal_id` (`merchant_terminal_id`),
  KEY `merchant_device_id` (`merchant_device_id`),
  KEY `trn_id_gr_aade` (`trn_id_gr_aade`)

) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



$sql="select erp_app_id from gks_pos limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_pos
  add column `erp_app_id` int(11) NOT NULL DEFAULT 0,
  add column `erp_app_dest` varchar(64) DEFAULT NULL,
  add column `erp_app_dest_printer` varchar(190) DEFAULT NULL,
  add column `erp_app_dest_printer_method` int(11) NOT NULL DEFAULT 0,
  add column `erp_app_dest_printer_lpr_ip` varchar(190) DEFAULT NULL,
  add column `erp_app_dest_printer_copies` int(11) NOT NULL DEFAULT 1,
  add column `erp_app_dest_folder` varchar(190) DEFAULT NULL,
  add column `erp_app_filter` varchar(190) DEFAULT NULL,
  add index erp_app_id (erp_app_id),
  add index erp_app_dest (erp_app_dest)");
}
$sql="select erp_app_filter from gks_acc_seires limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_acc_seires
  add column `erp_app_filter` varchar(190) DEFAULT NULL after erp_app_dest_folder");
  
  gks_run_sql("update gks_acc_seires set erp_app_filter='[\"webpage_computer\",\"webpage_tablet\",\"webpage_mobile\",\"app_with_thermal\",\"app_no_thermal\"]'");
}

$sql="select * from gks_eshop_fiscal_position where id_fiscal_position=4";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("insert into gks_eshop_fiscal_position (
  id_fiscal_position,fiscal_position_descr,fiscal_position_sortorder,fiscal_position_disable,katigoria_fpa_ejeresi_id
  ) values (
  4,'Λιανικής Εσωτερικού Μειωμένο',2,0,0
  )");
  
  gks_run_sql("update gks_eshop_fiscal_position set fiscal_position_sortorder=3 where id_fiscal_position=2");
  gks_run_sql("update gks_eshop_fiscal_position set fiscal_position_sortorder=4 where id_fiscal_position=3");
}

/*
$sql="select * from gks_eshop_fiscal_fpa where fiscal_position_id=4";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("insert into gks_eshop_fiscal_fpa (
  id_fiscal_fpa,company_fpa_type_id,fiscal_position_id,fpa_base_id_from,fpa_id_to,fiscal_fpa_disable
  ) values
  (157,1,4,1001,5,0),
  (158,1,4,1002,6,0),
  (159,1,4,1003,7,0),
  (160,1,4,1004,4,0),
  (161,2,4,1001,5,0),
  (162,2,4,1002,6,0),
  (163,2,4,1003,7,0),
  (164,2,4,1004,4,0),
  (165,3,4,1001,5,0),
  (166,3,4,1002,6,0),
  (167,3,4,1003,7,0),
  (168,3,4,1004,4,0)");
  
}
*/


$sql="select transfer_pricelist_price_per_person_child from gks_transfer_pricelist limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("ALTER TABLE `gks_transfer_pricelist`
  ADD COLUMN `transfer_pricelist_price_per_person_child` DOUBLE NOT NULL DEFAULT 0 AFTER `transfer_pricelist_price_per_person_offer`,
  ADD COLUMN `transfer_pricelist_price_per_person_child_offer` DOUBLE NOT NULL DEFAULT 0 AFTER `transfer_pricelist_price_per_person_child`,
  ADD COLUMN `transfer_pricelist_price_per_person_infant` DOUBLE NOT NULL DEFAULT 0 AFTER `transfer_pricelist_price_per_person_child_offer`,
  ADD COLUMN `transfer_pricelist_price_per_person_infant_offer` DOUBLE NOT NULL DEFAULT 0 AFTER `transfer_pricelist_price_per_person_infant`;");
}


$sql="select aade_lock_send_numbers from gks_acc_seires limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("ALTER TABLE `gks_acc_seires`
  ADD COLUMN `aade_lock_send_numbers` tinyint(4) NOT NULL DEFAULT 0 after next_number,
  add index aade_lock_send_numbers (aade_lock_send_numbers);");
  
  $sql="select * from gks_settings where mykey='GKS_AADE_LOCK_SEND_NUMBERS' and myvalue ='true'";
  $result = gks_run_sql($sql);
  if ($result->num_rows>0) {
    $sql="update gks_acc_seires set aade_lock_send_numbers=1
    where send_mydata<>0 or send_paroxos<>0";
    gks_run_sql($sql);
  }
  
  $sql="delete from gks_settings where mykey='GKS_AADE_LOCK_SEND_NUMBERS'";
  gks_run_sql($sql);
}



$sql="select * from gks_print_objects where id_print_object=7";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("insert into gks_print_objects (
  id_print_object,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  object_name,object_descr
  ) values (
  7,now(),now(),2,2,'127.0.0.1',
  'gks_crm_tasks','Εργασίες')");
  
}


$sql="select print_date from gks_crm_tasks limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("ALTER TABLE `gks_crm_tasks`
  add column `print_date` datetime DEFAULT NULL,
  add column `print_file_name` varchar(255) DEFAULT NULL,
  add column `print_file_url` varchar(255) DEFAULT NULL,
  add column `print_user_id` int(11) NOT NULL DEFAULT 0,
  add column `print_crm_task_status` varchar(190) DEFAULT NULL,
  add column `print_crm_task_status_id` int(11) NOT NULL DEFAULT 0");
}

$sql="select edit_mode from gks_print_forms limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("ALTER TABLE `gks_print_forms`
  add column `edit_mode` varchar(16) DEFAULT NULL");
  gks_run_sql("update gks_print_forms set edit_mode='html'");
}

$sql="select acc_journal_id_tim from gks_eshops limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("ALTER TABLE `gks_eshops`
  add column `acc_journal_id_tim` int(11) NOT NULL DEFAULT 0,
  add column `acc_seira_id_tim` int(11) NOT NULL DEFAULT 0,
  add column `warehouses_id_from_tim` int(11) NOT NULL DEFAULT 0");
  
  gks_run_sql("update gks_eshops set
  acc_journal_id_tim=acc_journal_id,
  acc_seira_id_tim=acc_seira_id,
  warehouses_id_from_tim=warehouses_id_from
  where import_as='acc_inv'");
}


$sql="select firebase_token from gks_erp_app_mobile limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_erp_app_mobile`
  add column firebase_token varchar(512) DEFAULT NULL");
}
$sql="select firebase_token from gks_erp_app_mobile_ping limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_erp_app_mobile_ping`
  add column firebase_token varchar(512) DEFAULT NULL");
}

$sql="select * from gks_settings where mykey='GKS_ERP_APP_MOBILE_VER'";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_settings` (`mykey`,`myvalue`) VALUES 
   ('GKS_ERP_APP_MOBILE_VER','1.9')");
}



$sql="select uid from gks_transfer_reservation limit 1";
$result = $db_link->query($sql);
if (!$result) {//must return error
  gks_run_sql("ALTER TABLE `gks_transfer_reservation`
  ADD COLUMN `calendardata` text COLLATE utf8mb4_unicode_520_ci,
  ADD COLUMN `uri` varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  ADD COLUMN `etag` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  ADD COLUMN `size` int(11) unsigned NOT NULL DEFAULT '0',
  ADD COLUMN `componenttype` varchar(32) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  ADD COLUMN `uid` varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  ADD INDEX `uid`(`uid`)");
}

$sql="select poi_timezone from gks_poi limit 1";
$result = $db_link->query($sql);
if (!$result) {//must return error
  gks_run_sql("ALTER TABLE `gks_poi`
  ADD COLUMN `poi_timezone` varchar(128) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL after poi_type_id");
}

$sql="select * from gks_settings where mykey='GKS_ERP_APP_DEF_TIMEZONE'";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_settings` (`mykey`,`myvalue`) VALUES 
   ('GKS_ERP_APP_DEF_TIMEZONE','Europe/Athens')");
}



$sql="select b2g_inv_aaht_code from gks_acc_inv limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_acc_inv
	add column b2g_inv_aaht_code varchar(190) DEFAULT NULL,
	add column b2g_inv_aaht_name varchar(190) DEFAULT NULL, 
	add column b2g_inv_buyer_name varchar(190) DEFAULT NULL");
	
	
	gks_run_sql("UPDATE gks_acc_inv 
	LEFT JOIN gks_users ON gks_acc_inv.user_id = gks_users.user_id SET 
	gks_acc_inv.b2g_inv_aaht_code = b2g_aaht_code, 
	gks_acc_inv.b2g_inv_aaht_name = b2g_aaht_name, 
	gks_acc_inv.b2g_inv_buyer_name = gks_users.eponimia
  WHERE gks_users.is_b2g=1;");


  /*
  b2g_inv_aaht_name  BT-10
  project_reference  BT-11
  contract_reference BT-12 
  b2g_inv_buyer_name BT-44
  b2g_inv_aaht_code  BT-46
  */
  
}


$sql="select oxima_status from gks_transfer_reservation_oximata limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_transfer_reservation_oximata
	add column oxima_status varchar(64) DEFAULT NULL,
	ADD INDEX `oxima_status`(`oxima_status`)");  
	
	gks_run_sql("update gks_transfer_reservation_oximata set oxima_status='210draft' where oxima_status is null");
}

$sql="select afora_driver from gks_transfer_reservation_log limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
	gks_run_sql("alter table gks_transfer_reservation_log
	add column afora_driver TINYINT NOT NULL DEFAULT 0,
	ADD INDEX `afora_driver`(`afora_driver`),
	add column afora_externalpartner TINYINT NOT NULL DEFAULT 0,
	ADD INDEX `afora_externalpartner`(`afora_externalpartner`)");  
}


$sql="select * from gks_permission_object where id_permission_object=2210";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_permission_object` (`id_permission_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`card_title`,`parent_id`,`table_name`,`object_name`,`sortorder`) VALUES 
   (2210,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','Transfer',0,'gks_transfer_driver','Σελίδα Οδηγού',21400)");

  $sql="SELECT ssss.user_id
  FROM (
    SELECT user_id
    FROM gks_permission_user
    GROUP BY user_id
  )  AS ssss LEFT JOIN ".GKS_WP_TABLE_PREFIX."users ON ssss.user_id = ".GKS_WP_TABLE_PREFIX."users.ID
  WHERE (gks_wp_capabilities Like '%adminmy%' or gks_wp_capabilities Like '%administrator%')";
  $result = gks_run_sql($sql);
  $user_ids=array();
  while ($row = $result->fetch_assoc()) $user_ids[]=$row['user_id'];
  
  foreach ($user_ids as $value) {
    gks_run_sql("INSERT INTO `gks_permission_user` 
    (mydate_add,mydate_edit,user_id_add,user_id_edit,myip,user_id,permission_object_id,perm_view,perm_edit,perm_add,perm_delete,perm_autocomplete) values 
    ('2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1',".$value.",2210,1,1,1,1,1)");
  }

}


gks_run_sql("delete from gks_aade_skopos_diakinisis where id_aade_skopos_diakinisis=10"); //Χρησιδάνειο
gks_run_sql("delete from gks_aade_skopos_diakinisis where id_aade_skopos_diakinisis=11"); //Αντικατάσταση

$sql="select * from gks_aade_skopos_diakinisis where id_aade_skopos_diakinisis=23";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("insert into gks_aade_skopos_diakinisis (
  id_aade_skopos_diakinisis,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  aade_skopos_diakinisis_code,aade_skopos_diakinisis_descr,sortorder
  ) values (
  23,'2020-01-01','2020-01-01',2,2,'127.0.0.1',
  20,'Μεταφορές - Ταχυμεταφορές',20)");
  
  gks_run_sql("update gks_aade_skopos_diakinisis set aade_skopos_diakinisis_descr='Επεξεργασία - Συναρμολόγηση - Αποσυναρμολόγηση' where id_aade_skopos_diakinisis=7");
  gks_run_sql("update gks_aade_skopos_diakinisis set aade_skopos_diakinisis_descr='Ενδοδιακίνηση' where id_aade_skopos_diakinisis=8");
  
  gks_run_sql("delete from gks_aade_skopos_diakinisis where id_aade_skopos_diakinisis=6"); //Φύλαξη
  
  gks_run_sql("delete from gks_aade_skopos_diakinisis where id_aade_skopos_diakinisis=18"); //Επιστροφή από Φύλαξη
  gks_run_sql("delete from gks_aade_skopos_diakinisis where id_aade_skopos_diakinisis=19"); //Ανακύκλωση
  gks_run_sql("delete from gks_aade_skopos_diakinisis where id_aade_skopos_diakinisis=20"); //Καταστροφή άχρηστου υλικού
  gks_run_sql("delete from gks_aade_skopos_diakinisis where id_aade_skopos_diakinisis=21"); //Διακίνηση Παγίων (Ενδοδιακίνηση)
  
  
}
  

//Thread stack overrun:  164896 bytes used of a 196608 byte stack, and 32000 bytes needed.  Use 'mysqld --thread_stack=#' to specify a bigger stack.


$sql="select * from gks_warehouse_balance_lots_serials limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  $sql="SHOW COLUMNS from gks_whi_mov_balance_lots_serials";
  $result = gks_run_sql($sql);
  if ($result) { //must return OK
    $temp=array();
    while ($row = $result->fetch_assoc()) {
      $temp[]=$row;
    }
    $fields_exist=array();
    foreach ($temp as $value) { //clean must have fields
      if ($value['Field']!='warehouse_id' and $value['Field']!='lot_product_id' and $value['Field']!='product_monada_id' and $value['Field']!='total_balance' and $value['Field']!='odbc' and
          strlen($value['Field'])>=4 and substr($value['Field'],0,3)=='wh_') {
        $fields_exist[]=array(
          'f' => $value['Field'],
          'id'=>intval(substr($value['Field'], 3)),
        );
      }
    }
    //echo '<pre>';print_r($fields_exist);die();
    
    //ean to warehouse_id==0 tote einai to total_balance
  	gks_run_sql("CREATE TABLE gks_warehouse_balance_lots_serials (
  	`warehouse_id` int(11) NOT NULL DEFAULT 0,
    `lot_product_id` int(11) NOT NULL DEFAULT 0,
    `balance` double NOT NULL DEFAULT 0,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`warehouse_id`,`lot_product_id`) USING BTREE,
  	KEY `warehouse_id` (`warehouse_id`),
  	KEY `lot_product_id` (`lot_product_id`),
  	KEY `balance` (`balance`)
  	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci");

	  $sql="select * from gks_whi_mov_balance_lots_serials";
    $result = gks_run_sql($sql);
    
    $edata=[];
    while ($row = $result->fetch_assoc()) {
      $edata[]=$row;
    }	    
    //echo '<pre>';print_r($edata);die();
    foreach ($edata as $v) {
      $sqls=[];
      foreach ($fields_exist as $ff) {
        if ($v[$ff['f']]!=0) {
          $sqls[]='('.$ff['id'].','.$v['lot_product_id'].','.$v[$ff['f']].')';
        }
      } 
      if ($v['total_balance']!=0) {
        $sqls[]='(0,'.$v['lot_product_id'].','.$v['total_balance'].')';    
      }
      //echo '<pre>';print_r($v);print_r($sqls);die();
      if (count($sqls)) {
        $sql="replace into gks_warehouse_balance_lots_serials (
        warehouse_id,lot_product_id,balance
        ) values ".implode(',',$sqls);
        //echo '<pre>'.$sql;die();  
        $res=gks_run_sql($sql);
      }
    }
	  gks_run_sql("ALTER TABLE `gks_whi_mov_balance_lots_serials` RENAME TO `gks_whi_mov_balance_lots_serials_old`;");

  }
}

$sql="select * from gks_warehouse_balance_eidi limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  $sql="SHOW COLUMNS from gks_whi_mov_balance";
  $result = gks_run_sql($sql);
  if ($result) { //must return OK
    $temp=array();
    while ($row = $result->fetch_assoc()) {
      $temp[]=$row;
    }
    $fields_exist=array();
    foreach ($temp as $value) { //clean must have fields
      if ($value['Field']!='warehouse_id' and $value['Field']!='product_id' and $value['Field']!='product_monada_id' and $value['Field']!='total_balance' and $value['Field']!='odbc' and
          strlen($value['Field'])>=4 and substr($value['Field'],0,3)=='wh_') {
        $fields_exist[]=array(
          'f' => $value['Field'],
          'id'=>intval(substr($value['Field'], 3)),
        );
      }
    }
    //echo '<pre>';print_r($fields_exist);die();
    
    //ean to warehouse_id==0 tote einai to total_balance
  	gks_run_sql("CREATE TABLE gks_warehouse_balance_eidi (
  	`warehouse_id` int(11) NOT NULL DEFAULT 0,
    `product_id` int(11) NOT NULL DEFAULT 0,
    `balance` double NOT NULL DEFAULT 0,
    `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`warehouse_id`,`product_id`) USING BTREE,
  	KEY `warehouse_id` (`warehouse_id`),
  	KEY `product_id` (`product_id`),
  	KEY `balance` (`balance`)
  	) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci");

	  $sql="select * from gks_whi_mov_balance";
    $result = gks_run_sql($sql);
    
    $edata=[];
    while ($row = $result->fetch_assoc()) {
      $edata[]=$row;
    }	    
    //echo '<pre>';print_r($edata);die();
    foreach ($edata as $v) {
      $sqls=[];
      foreach ($fields_exist as $ff) {
        if ($v[$ff['f']]!=0) {
          $sqls[]='('.$ff['id'].','.$v['product_id'].','.$v[$ff['f']].')';
        }
      } 
      if ($v['total_balance']!=0) {
        $sqls[]='(0,'.$v['product_id'].','.$v['total_balance'].')';    
      }
      //echo '<pre>';print_r($v);print_r($sqls);die();
      if (count($sqls)) {
        $sql="replace into gks_warehouse_balance_eidi (
        warehouse_id,product_id,balance
        ) values ".implode(',',$sqls);
        //echo '<pre>'.$sql;die();  
        $res=gks_run_sql($sql);
      }
    }
	  gks_run_sql("ALTER TABLE `gks_whi_mov_balance` RENAME TO `gks_whi_mov_balance_old`;");

  }
}


$sql="select erp_app_mobile_can_transfer from gks_erp_app_mobile limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_erp_app_mobile`
  add column erp_app_mobile_can_transfer tinyint(4) NOT NULL DEFAULT 0");
}

gks_run_sql("update gks_acc_eidi_parastatikon set import_apo_allon='[9.3]' where id_acc_eidos_parastatikou=912");


$sql="select from_aade_import_lock from gks_whi_mov_products limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_whi_mov_products`
  add column `from_aade_import_lock` tinyint(4) NOT NULL DEFAULT 0");
}


$sql="select aade_paroxos_qrurl from gks_acc_inv limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_inv`
  add column `aade_paroxos_qrurl` varchar(256) DEFAULT NULL after aade_qrurl");
}
$sql="select aade_paroxos_qrurl from gks_acc_pay limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_acc_pay`
  add column `aade_paroxos_qrurl` varchar(256) DEFAULT NULL after aade_qrurl");
}
$sql="select aade_paroxos_qrurl from gks_whi_mov limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_whi_mov`
  add column `aade_paroxos_qrurl` varchar(256) DEFAULT NULL after aade_qrurl");
}


$sql="select worldline_implementation from gks_worldline_transaction limit 1";
$result = $db_link->query($sql);
if (!$result) { //must return error
  gks_run_sql("ALTER TABLE `gks_worldline_transaction`
  add column `worldline_implementation` varchar(16) DEFAULT NULL after eftpos_transaction_id,
  add index worldline_implementation (worldline_implementation)");
}

gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_worldline_transaction_app2app_res` (
  `id_worldline_transaction_app2app_res` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT '0',
  `user_id_edit` int(11) NOT NULL DEFAULT '0',
  `myip` varchar(48) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `erp_app_mobile_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `mytoast` varchar(190) DEFAULT NULL,
  `myresultCode` varchar(64) DEFAULT NULL,
  `mydata` varchar(64) DEFAULT NULL,
  `res_intent_result` varchar(64) DEFAULT NULL,
  `res_trn_receipt` text DEFAULT NULL,
  `res_app_version` varchar(64) DEFAULT NULL,
  `res_token` varchar(190) DEFAULT NULL,
  `res_merchant_device_id` varchar(64) DEFAULT NULL,
  `res_server_message` varchar(190) DEFAULT NULL,
  `res_merchant_device_status_code` varchar(64) DEFAULT NULL,
  `res_protocol_version` varchar(64) DEFAULT NULL,
  `res_terminal_status` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id_worldline_transaction_app2app_res`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `erp_app_mobile_id` (`erp_app_mobile_id`),
  KEY `user_id` (`user_id`),
  KEY `myresultCode` (`myresultCode`),
  KEY `res_intent_result` (`res_intent_result`),
  KEY `res_token` (`res_token`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");


$sql="select * from gks_sociallinks_type where id_sociallinks_type=36";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_sociallinks_type` (`id_sociallinks_type`,`odbc`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`sociallinks_type_descr`,`sociallinks_type_icon`,`sociallinks_type_icon_email`,`sociallinks_type_sortorder`,`sociallinks_type_disable`,`sociallinks_type_comments`) VALUES 
 (36,'2025-07-05','2025-07-05','2025-07-05',2,2,'127.0.0.1','Viber','<img src=\"/my/img/sociallinks/viber.png\" style=\"width:24px;\" class=\"sociallinks_icon\">','<img src=\"/my/img/sociallinks/20/viber.png\" style=\"width:20px;\" width=\"20\" height=\"20\" class=\"sociallinks_icon_email\">',36,0,NULL)");
}



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_email_template` (
  `id_email_template` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email_template_descr` varchar(64) DEFAULT NULL,
  `email_body` longtext DEFAULT NULL,
  `email_subject` varchar(250) DEFAULT NULL,
  `email_message` text DEFAULT NULL,
  `is_disable` tinyint(4) NOT NULL DEFAULT 0,
  `sortorder` int(11) NOT NULL DEFAULT 1000,
  `gks_lang` varchar(8) NOT NULL DEFAULT 'el-GR',
  `need_attachments` tinyint(4) DEFAULT 0,
  `other_fields` longtext DEFAULT NULL,
  `localization_set_id` int(11) NOT NULL DEFAULT 0,
  `edit_mode` varchar(16) DEFAULT NULL,
  PRIMARY KEY (`id_email_template`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `is_disable` (`is_disable`),
  KEY `sortorder` (`sortorder`),
  KEY `gks_lang` (`gks_lang`),
  KEY `email_template_descr` (`email_template_descr`),
  KEY `email_subject` (`email_subject`),
  KEY `email_message` (`email_message`(190)),
  KEY `localization_set_id` (`localization_set_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_email_template_photo` (
  `id_email_template_photo` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email_template_id` int(11) NOT NULL DEFAULT 0,
  `photo_url` varchar(190) NOT NULL,
  `mydate` datetime NOT NULL,
  `mysize` int(11) DEFAULT 0,
  `ip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_add_id` int(11) NOT NULL DEFAULT 0,
  `show_print` tinyint(4) NOT NULL DEFAULT 0,
  `filesobjectlist` tinyint(4) NOT NULL DEFAULT 0,
  `public_expire_date` datetime DEFAULT NULL,
  `public_shortcode` varchar(64) DEFAULT NULL,
  `public_myopencount` int(11) NOT NULL DEFAULT 0,
  `descr` varchar(190) DEFAULT NULL,
  PRIMARY KEY (`id_email_template_photo`),
  KEY `email_template_id` (`email_template_id`),
  KEY `photo_url` (`photo_url`),
  KEY `mydate` (`mydate`),
  KEY `mysize` (`mysize`),
  KEY `ip` (`ip`),
  KEY `show_print` (`show_print`),
  KEY `filesobjectlist` (`filesobjectlist`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");




gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_email_template_object` (
  `id_email_template_object` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `object_name` varchar(64) DEFAULT NULL,
  `object_descr` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id_email_template_object`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `object_name` (`object_name`),
  KEY `object_descr` (`object_descr`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");



gks_run_sql("CREATE TABLE IF NOT EXISTS `gks_email_template_object_forms` (
  `id_email_template_object_form` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mydate_add` datetime DEFAULT NULL,
  `mydate_edit` datetime DEFAULT NULL,
  `user_id_add` int(11) NOT NULL DEFAULT 0,
  `user_id_edit` int(11) NOT NULL DEFAULT 0,
  `myip` varchar(48) DEFAULT NULL,
  `odbc` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `email_template_id` int(11) NOT NULL DEFAULT 0,
  `email_template_object_id` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_email_template_object_form`),
  KEY `mydate_edit` (`mydate_edit`),
  KEY `user_id_edit` (`user_id_edit`),
  KEY `email_template_id` (`email_template_id`),
  KEY `email_template_object_id` (`email_template_object_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;");

$sql="select * from gks_email_template_object where id_email_template_object<=7";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("insert into gks_email_template_object
  (id_email_template_object,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  object_name,object_descr) 
  values 
  (1,'2020-01-01','2020-01-01',2,2,'127.0.0.1','gks_orders','Παραγγελίες'),
  (2,'2020-01-01','2020-01-01',2,2,'127.0.0.1','gks_acc_inv','Παραστατικά'),
  (3,'2020-01-01','2020-01-01',2,2,'127.0.0.1','gks_acc_pay','Πληρωμές'),
  (4,'2020-01-01','2020-01-01',2,2,'127.0.0.1','gks_whi_mov','Δελτία'),
  (5,'2020-01-01','2020-01-01',2,2,'127.0.0.1','gks_hotel_reservation','Κρατήσεις'),
  (6,'2020-01-01','2020-01-01',2,2,'127.0.0.1','gks_transfer_reservation','Transfer'),
  (7,'2020-01-01','2020-01-01',2,2,'127.0.0.1','gks_crm_tasks','Εργασίες'),  
  (8,'2020-01-01','2020-01-01',2,2,'127.0.0.1','gks_crm_leads','Ευκαιρίες'),  
  (9,'2020-01-01','2020-01-01',2,2,'127.0.0.1','gks_crm_machine','Συσκευές')");  
}


gks_run_sql("update gks_permission_object set table_name='gks_email_template' where id_permission_object=260 and table_name='gks_files_email_templates'");

$sql="select * from gks_custom_table where id_custom_table=53";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  $shortcode_prefix=gks_shortcode_prefix_for_custom_table();
  gks_run_sql("insert into gks_custom_table (
  id_custom_table,mydate_add,mydate_edit,user_id_add,user_id_edit,myip,
  custom_table_descr,custom_table_name,field_name_id_parent,field_name_id_current,
  custom_table_disabled,custom_priv,custom_sortorder,obj_url,shortcode_prefix
  ) values (
  53,'2020-01-01','2020-01-01',2,2,'127.0.0.1',
  'CRM - Πρότυπα emails','gks_email_template','id_email_template','email_template_id',
  0,'crm',1060,'admin-email-templates.php','".$db_link->escape_string($shortcode_prefix)."'
  )");
  
}
gks_run_sql("update gks_custom_table set custom_table_descr='CRM - Μικρό URL' where id_custom_table=31 and custom_table_descr='CRM -Μικρό URL'");


$sql="select * from gks_crm_activity_objects where id_crm_activity_object=54";
$result = gks_run_sql($sql);
if ($result->num_rows==0) {
  gks_run_sql("INSERT INTO `gks_crm_activity_objects` (`id_crm_activity_object`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`crm_activity_object_code`,`crm_activity_object_descr`,`crm_activity_object_sortorder`,`crm_activity_object_disabled`,`crm_activity_object_page`) VALUES 
   (54,'2021-01-01 00:00:00','2021-01-01 00:00:00',2,2,'127.0.0.1','gks_email_template',      'Πρότυπο email',                 54,0,'admin-email-templates-item.php?id=%s')");
}




$email_temlpate_conv=[];
$email_temlpate_conv[]=array('default',1);
$email_temlpate_conv[]=array('default.en-US',2);
$email_temlpate_conv[]=array('empty',3);
$email_temlpate_conv[]=array('empty.en-US',4);
$email_temlpate_conv[]=array('calendar_notification',5);
$email_temlpate_conv[]=array('new_user',7);
$email_temlpate_conv[]=array('new_user.en-US',8);
$email_temlpate_conv[]=array('new_user.en_US',8);
$email_temlpate_conv[]=array('order_execute',9);
$email_temlpate_conv[]=array('order_execute.en-US',10);
$email_temlpate_conv[]=array('order_execute_partial',11);
$email_temlpate_conv[]=array('order_execute_partial.en-US',12);
$email_temlpate_conv[]=array('order_invoice',13);
$email_temlpate_conv[]=array('order_invoice.en-US',14);
$email_temlpate_conv[]=array('order_receive',15);
$email_temlpate_conv[]=array('order_receive.en-US',16);
$email_temlpate_conv[]=array('wait_bank_payment',17);
$email_temlpate_conv[]=array('wait_bank_payment.en-US',18);
$email_temlpate_conv[]=array('Έτοιμη η παραγγελία',19);
$email_temlpate_conv[]=array('Κράτηση αναμονή πληρωμής',21);
$email_temlpate_conv[]=array('Κράτηση_αναμονή_πληρωμής',21);
$email_temlpate_conv[]=array('Ραντεβού',23);
$email_temlpate_conv[]=array('Τιμολόγιο από transfer',25);
$email_temlpate_conv[]=array('Τιμολόγιο από transfer.en-US',26);
$email_temlpate_conv[]=array('Τιμολόγιο συνημμένο',27);
$email_temlpate_conv[]=array('Τιμολόγιο συνημμένο.en-US',28);
$email_temlpate_conv[]=array('Τιμολόγιο συνημμένο-en-US',28);
$email_temlpate_conv[]=array('ΑΛΠ',51);
$email_temlpate_conv[]=array('ΑΛΠ Novalis',52);
$email_temlpate_conv[]=array('ΑΛΠ Αποστολή',53);

foreach ($email_temlpate_conv as $value) {
  gks_run_sql("update gks_settings_users set myvalue='".$value[1]."' where myvalue='".$db_link->escape_string($value[0])."'");
}
    
$email_temlpate_tables=[];
$email_temlpate_tables[]=array('gks_transfer','transfer_parastatiko_apodiji_email_template','');
$email_temlpate_tables[]=array('gks_transfer','transfer_parastatiko_timologio_email_template','');
$email_temlpate_tables[]=array('gks_transfer_sub_company_details','transfer_parastatiko_apodiji_email_template','');
$email_temlpate_tables[]=array('gks_transfer_sub_company_details','transfer_parastatiko_timologio_email_template','');
$email_temlpate_tables[]=array('gks_email','template','.html');
$email_temlpate_tables[]=array('gks_mass_messages','email_template','');



foreach ($email_temlpate_tables as $tt) {
  $sql="select ".$tt[1]."_id from ".$tt[0]." limit 1";
  $result = $db_link->query($sql);
  if (!$result) { //must return error
    gks_run_sql("ALTER TABLE `".$tt[0]."`
    add column `".$tt[1]."_id` int(11) NOT NULL DEFAULT 0 after ".$tt[1]."");
  }
  $sql="select ".$tt[1]." from ".$tt[0]." limit 1";
  $result = $db_link->query($sql);
  if ($result) {//ean iparxei to pedio
    foreach ($email_temlpate_conv as $value) {
      gks_run_sql("update ".$tt[0]." set ".$tt[1]."_id=".$value[1]." where ".$tt[1]."='".$db_link->escape_string($value[0].$tt[2])."'");
    }
    $sql="select ".$tt[1].",count(*) as cc 
    from ".$tt[0]." 
    where ".$tt[1]."_id=0 and ".$tt[1]."<>''
    group by ".$tt[1];
    $result = $db_link->query($sql);
    if ($result->num_rows==0) {
      gks_run_sql("ALTER TABLE `".$tt[0]."` drop column `".$tt[1]."`");
    } else {
      while ($row = $result->fetch_assoc()) {
        echo '<pre>';print_r($row);echo '</pre>';
      }
    }
  }
}





$mytemplates=array();
$mydir=GKS_SITE_PATH.GKS_SITE_HTTPDOCS.'/my/templates/';
if (file_exists($mydir)) {
  $mytemp = array_diff( scandir( $mydir ), Array( ".", ".." ) );
  foreach ($mytemp as $value) {
    $is_for_add=true;
    if (is_dir($mydir.$value)) $is_for_add=false;
    else if (endwith($value,'.html')==false) $is_for_add=false;
    else if (endwith($value,'.params.html')) $is_for_add=false;
    if ($is_for_add) {
      $mytemplates[]=substr($value, 0, strlen($value)-5);
    }
  }
  sort($mytemplates);

  //print '<pre>';print_r($mytemplates);die();
  
  foreach ($mytemplates as &$myt) {
    
    $mytfs=filesize($mydir.$myt.'.html');
    $mytdt=filemtime($mydir.$myt.'.html');
    $mytfsp=filesize($mydir.$myt.'.params.html');
    $mytdtp=filemtime($mydir.$myt.'.params.html');
    
    switch ($myt) {   
      case 'default':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==7088 and ($mytdt==1735580292 or $mytdt==1637619769) and $mytfsp==1074 and $mytdtp==1608914664) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (1,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 19:22:02','default','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr> \n      \n     \n     \n      \n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      \n\n      \n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n\n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι \n        διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής \n        νομοθεσίας περί διαφημιστικών μηνυμάτων: \n        «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον \n        δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC»\n        του Ευρωπαϊκού Κοινοβουλίου,\n        Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.      \n      \n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        \n        Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a>\n        και στείλετε το email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n \n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail!\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n          Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: \n          Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται \n          και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος \n          ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση \n          ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη \n          για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση \n          δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, \n          ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. \n          Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά.            \n            \n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','[[GKS_SITE_HUMAN_NAME]]','Αγαπητέ πελάτη,<br/>\r\n<br/>\r\nΜε εκτίμηση, η ομάδα του<br/>\r\n[[GKS_SITE_HUMAN_NAME]]',0,1,'el-GR',0,NULL,1000001,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;
      case 'default.en-US':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==5438 and $mytdt==1637619759 and $mytfsp==1036 and $mytdtp==1608914671) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (2,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 19:22:02','default.en-US','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]--> \n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr> \n     \n               \n      \n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      \n\n      \n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n\n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        This e-mail address cannot be considered spam as long as the sender\'s details and deletion procedures are listed and \n        fulfills the requirements of European advertising law legislation: \n        «Each message must bear the sender\'s complete information clearly and should enable the receiver to delete. Directiva2002/58/EC»\n        of the European Parliament, Relative as A5-270/2001 of the European Parliament.\n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        You can unsubscribe from the list by clicking this \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">link</a> \n        and sending the email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr>   \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Please consider the environment before printing this e-mail !\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n            Confidentiality Warning - Disclaimer: This e-mail contains information intended only for the individual or entity to which it \n            is addressed and may contain confidential material. If the reader of this e-mail is not the intended recipient or the employee or agent responsible for delivering it to the intended recipient, \n            any dissemination, copying or other use of, or taking of any action in reliance upon this e-mail is strictly prohibited. The sender bears no responsibility for any loss, \n            disruption or damage to your data or computer system that may occur while using data contained in, or transmitted with, this e-mail. If you received this e-mail in error, \n            please immediately notify the sender by return e-mail and delete the material from any computer. Any views expressed are personal unless otherwise stated.<br>&nbsp;</SPAN>\n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','[[GKS_SITE_HUMAN_NAME]]','Dear customer,<br/>\r\n<br/>\r\nSincerely, the [[GKS_SITE_HUMAN_NAME]] team',0,2,'en-US',0,NULL,1000001,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;
      case 'empty':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==2022 and $mytdt==1637619150 and $mytfsp==1074 and $mytdtp==1608914652) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (3,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 19:22:01','empty','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      \n      <hr>          \n      \n      \n      <p>[[message]]</p>      \n      \n      <p>&nbsp;</p>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n  </tr>\n</table>\n<p>&nbsp;</p>\n</center>\n</body>\n</html>','[[GKS_SITE_HUMAN_NAME]]','Αγαπητέ πελάτη,<br/>\r\n<br/>\r\nΜε εκτίμηση, η ομάδα του<br/>\r\n[[GKS_SITE_HUMAN_NAME]]',0,3,'el-GR',0,NULL,1000003,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;
      case 'empty.en-US':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==2022 and $mytdt==1637619134 and $mytfsp==1036 and $mytdtp==1608914659) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (4,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 19:22:00','empty.en-US','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      \n      <hr>          \n      \n      \n      <p>[[message]]</p>      \n      \n      <p>&nbsp;</p>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n  </tr>\n</table>\n<p>&nbsp;</p>\n</center>\n</body>\n</html>','[[GKS_SITE_HUMAN_NAME]]','Dear customer,<br/>\r\n<br/>\r\nSincerely, the [[GKS_SITE_HUMAN_NAME]] team',0,4,'en-US',0,NULL,1000003,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;
      case 'calendar_notification':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==6850 and $mytdt==1637619754 and $mytfsp==2029 and $mytdtp==1670526776) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (5,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 21:26:26','calendar_notification','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color: #f3f3f3;\">\n<p><br /></p>\n<table style=\"width: 600px; margin-top: 20px; margin-bottom: 20px; color: #000000;\" border=\"0\" width=\"600px\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\n<tbody>\n<tr>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n<td style=\"width: 570px;\" width=\"570px\">\n<p style=\"margin: 13px;\" align=\"center\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo200.png\" width=\"200px\" border=\"0\" /></a></p>\n<hr />\n<p><br /></p>\n<p style=\"font-size: 22pt; padding: 0px 0px 20px; margin: 0px; font-weight: bold; text-align: center;\">Συμβάν Ημερολογίου</p>\n<p style=\"margin-top: 40px;\"><strong>[[is_oloimero]]</strong><br /><strong>Από:</strong> [[apo]]<br /><strong>Έως:</strong> [[eos]]<br /><strong>Περιγραφή:</strong> [[perigrafi]]<br /><strong>Τοποθεσία:</strong> [[topothesia]]</p>\n<p style=\"margin-top: 40px;\">[[message]]</p>\n<p style=\"margin-top: 40px;\"><br /></p>\n<hr />\n<table style=\"width: 100%; color: #000000;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 20%;\" align=\"left\" width=\"20%\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo100.png\" width=\"100px\" border=\"0\" /></a></td>\n<td style=\"width: 60%;\" align=\"center\" width=\"60%\"><a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a></td>\n<td style=\"width: 20%; vertical-align: middle;\" align=\"right\" width=\"20%\">[[link_facebook]] [[link_twitter]] [[link_instagram]]</td>\n</tr>\n</tbody>\n</table>\n<hr />\n<p style=\"margin-top: 40px; text-align: left; font-size: 8pt;\" align=\"left\">Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής νομοθεσίας περί διαφημιστικών μηνυμάτων: «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC» του Ευρωπαϊκού Κοινοβουλίου, Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a> και στείλετε το email.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Copyright © [[year]] <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\">[[GKS_SITE_NAME]]</a> All rights reserved.</p>\n<hr />\n<table style=\"width: 100%;\" width=\"100%\">\n<tbody>\n<tr>\n<td>\n<p align=\"justify\"><span style=\"font-size: 12pt; font-family: \'Webdings\'; color: #00f000;\">P</span><span style=\"font-size: 12pt; font-family: \'Arial\'; color: #008000;\"> </span> <span style=\"font-size: 8pt; font-family: \'Arial\'; color: #00f000;\"> Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail! </span></p>\n<p align=\"justify\"><span style=\"font-size: 8pt; color: #000000;\"> Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά. </span></p>\n</td>\n</tr>\n</tbody>\n</table>\n</td>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n</tr>\n</tbody>\n</table>\n<p><br /></p>\n</center>\n</body>\n</html>','Συνάντηση #[[id]]','Με εκτίμηση, η ομάδα του<br/> [[GKS_SITE_HUMAN_NAME]]',0,5,'el-GR',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"is_oloimero\",\r\n        \"id\": \"email_param_is_oloimero\",\r\n        \"px\": \"Ολοήμερο\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"apo\",\r\n        \"id\": \"email_param_apo\",\r\n        \"px\": \"31/12/2021\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"eos\",\r\n        \"id\": \"email_param_eos\",\r\n        \"px\": \"31/12/2021\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"perigrafi\",\r\n        \"id\": \"email_param_perigrafi\",\r\n        \"px\": \"Κείμενο\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"topothesia\",\r\n        \"id\": \"email_param_topothesia\",\r\n        \"px\": \"Θεσσαλονίκη\"\r\n    }\r\n]',1000005,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;
      case 'new_user':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==8492 and $mytdt==1637619776 and $mytfsp==1509 and $mytdtp==1670526944) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (7,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 20:45:07','new_user','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      \n      <hr>          \n      \n      <p  style=\"margin-top: 40px;\">\n      Γειά σας,\n      </p>\n      <p>[[message]]</p>\n      <p>Έχει δημιουργηθεί για εσάς ένας νέος λογαριασμός στο <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_OFFICIAL_SITE_URL]]</a></p>\n      <p>\n      Τα στοιχεία σύνδεσης είναι τα παρακάτω:\n      <br/>\n      Όνομα χρήστη: <b>[[username]]</b>\n      <br/>\n      Συνθηματικό: <b>[[password]]</b>\n      <br/>\n      Email: <b><a href=\"mailto:[[email]]\">[[email]]</a></b>\n      </p><p>\n      Για να συνδεθείτε μεταβείτε στην σελίδα:<br/>\n      <a href=\"[[GKS_OFFICIAL_SITE_URL]]/wp-login.php\" target=\"_blank\">[[GKS_OFFICIAL_SITE_URL]]/wp-login.php</a>\n      </p><p>\n      Για να αλλάξετε τα στοιχεία σας μεταβείτε στην σελίδα:<br/>\n      <a href=\"[[GKS_OFFICIAL_SITE_URL]]/my/profile.php\" target=\"_blank\">[[GKS_OFFICIAL_SITE_URL]]/my/profile.php</a>\n      </p><p>\n      Μην διστάσετε να επικοινωνήσετε μαζί μας.<br/>\n      Μέσω email: <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a><br/>\n      <br/>\n      Θα χαρούμε να σας εξυπηρετήσουμε.\n\n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Με εκτίμηση, η ομάδα του [[GKS_SITE_HUMAN_NAME]]\n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n             \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n            \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι \n        διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής \n        νομοθεσίας περί διαφημιστικών μηνυμάτων: \n        «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον \n        δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC»\n        του Ευρωπαϊκού Κοινοβουλίου,\n        Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.      \n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a>\n        και στείλετε το email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail!\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n          Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: \n          Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται \n          και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος \n          ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση \n          ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη \n          για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση \n          δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, \n          ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. \n          Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά.            \n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','[[GKS_SITE_HUMAN_NAME]]',NULL,0,7,'el-GR',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"username\",\r\n        \"id\": \"email_param_username\",\r\n        \"px\": \"username\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"password\",\r\n        \"id\": \"email_param_password\",\r\n        \"px\": \"12345678\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"email\",\r\n        \"id\": \"email_param_email\",\r\n        \"px\": \"username@example.com\"\r\n    }\r\n]',1000007,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;
      case 'new_user.en-US':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==6711 and $mytdt==1637619773 and $mytfsp==1509 and $mytdtp==1670526951) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (8,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 20:45:11','new_user.en-US','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      \n      <hr>          \n      \n      <p  style=\"margin-top: 40px;\">\n      Hello,\n      </p>\n      <p>[[message]]</p>\n      <p>A new account has been created for you <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_OFFICIAL_SITE_URL]]</a></p>\n      <p>\n      The login details are as follows:\n      <br/>\n      Username: <b>[[username]]</b>\n      <br/>\n      Password: <b>[[password]]</b>\n      <br/>\n      Email: <b><a href=\"mailto:[[email]]\">[[email]]</a></b>\n      </p><p>\n      To log in go to page:<br/>\n      <a href=\"[[GKS_OFFICIAL_SITE_URL]]/wp-login.php\" target=\"_blank\">[[GKS_OFFICIAL_SITE_URL]]/wp-login.php</a>\n      </p><p>\n      To change your details go to the page:<br/>\n      <a href=\"[[GKS_OFFICIAL_SITE_URL]]/my/profile.php\" target=\"_blank\">[[GKS_OFFICIAL_SITE_URL]]/my/profile.php</a>\n      </p><p>\n      Feel free to contact us.<br/>\n      By email: <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a><br/>\n     \n      <br/>\n      We will be happy to assist you.\n      </p>\n\n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Sincerely, the [[GKS_SITE_HUMAN_NAME]] team\n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]            \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        This e-mail address cannot be considered spam as long as the sender\'s details and deletion procedures are listed and \n        fulfills the requirements of European advertising law legislation: \n        «Each message must bear the sender\'s complete information clearly and should enable the receiver to delete. Directiva2002/58/EC»\n        of the European Parliament, Relative as A5-270/2001 of the European Parliament.\n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        You can unsubscribe from the list by clicking this <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">link</a> and sending the email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]]\n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\r              \n              Please consider the environment before printing this e-mail !\r            \n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\r             \n            Confidentiality Warning - Disclaimer: This e-mail contains information intended only for the individual or entity to which it \n            is addressed and may contain confidential material. If the reader of this e-mail is not the intended recipient or the employee or agent responsible for delivering it to the intended recipient, \n            any dissemination, copying or other use of, or taking of any action in reliance upon this e-mail is strictly prohibited. The sender bears no responsibility for any loss, \n            disruption or damage to your data or computer system that may occur while using data contained in, or transmitted with, this e-mail. If you received this e-mail in error, \n            please immediately notify the sender by return e-mail and delete the material from any computer. Any views expressed are personal unless otherwise stated.<br>&nbsp;</SPAN>\n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','[[GKS_SITE_HUMAN_NAME]]',NULL,0,8,'en-US',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"username\",\r\n        \"id\": \"email_param_username\",\r\n        \"px\": \"username\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"password\",\r\n        \"id\": \"email_param_password\",\r\n        \"px\": \"12345678\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"email\",\r\n        \"id\": \"email_param_email\",\r\n        \"px\": \"username@example.com\"\r\n    }\r\n]',1000007,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;
      case 'order_execute':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==7703 and $mytdt==1637619278 and $mytfsp==1300 and $mytdtp==1670526933) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (9,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 20:45:16','order_execute','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr>          \n\n      <p>&nbsp;</p>    \n      <p style=\"font-size:22pt;padding:0px 0px 20px 0px;margin:0px;font-weight: bold;\">Αριθμός παραγγελίας: [[id_order]]</p>\n\n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Για οποιαδήποτε πληροφορία, επικοινωνήστε μαζί μας στο email  \n        <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Σας  ευχαριστούμε</p>\n        \n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Με εκτίμηση, η ομάδα του [[GKS_SITE_HUMAN_NAME]]  \n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a  href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n              \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι \n        διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής \n        νομοθεσίας περί διαφημιστικών μηνυμάτων: \n        «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον \n        δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC»\n        του Ευρωπαϊκού Κοινοβουλίου,\n        Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.      \n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a>\n        και στείλετε το email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail!\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n          Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: \n          Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται \n          και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος \n          ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση \n          ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη \n          για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση \n          δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, \n          ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. \n          Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά.            \n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Εκτέλεση παραγγελίας [[id_order]]','Αγαπητέ πελάτη,<br/>\r\n<br/>\r\nΗ παραγγελία σας με αριθμό [[id_order]] έχει εκτελεσθεί επιτυχώς.',0,9,'el-GR',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    }\r\n]',1000009,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;        
      case 'order_execute.en-US':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==5946 and $mytdt==1637619267 and $mytfsp==1219 and $mytdtp==1670526936) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (10,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 20:45:17','order_execute.en-US','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr>          \n\n      <p>&nbsp;</p>\n      <p style=\"font-size:22pt;padding:0px 0px 20px 0px;margin:0px;font-weight: bold;\">Order number: [[id_order]]</p>\n  \n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">For any question please feel free to contact us at  \n        <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Thank you</p>\n        \n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Sincerely, the [[GKS_SITE_HUMAN_NAME]] team  \n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a  href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n              \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        This e-mail address cannot be considered spam as long as the sender\'s details and deletion procedures are listed and \n        fulfills the requirements of European advertising law legislation: \n        «Each message must bear the sender\'s complete information clearly and should enable the receiver to delete. Directiva2002/58/EC»\n        of the European Parliament, Relative as A5-270/2001 of the European Parliament.\n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        You can unsubscribe from the list by clicking this \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">link</a> \n        and sending the email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Please consider the environment before printing this e-mail !\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n            Confidentiality Warning - Disclaimer: This e-mail contains information intended only for the individual or entity to which it \n            is addressed and may contain confidential material. If the reader of this e-mail is not the intended recipient or the employee or agent responsible for delivering it to the intended recipient, \n            any dissemination, copying or other use of, or taking of any action in reliance upon this e-mail is strictly prohibited. The sender bears no responsibility for any loss, \n            disruption or damage to your data or computer system that may occur while using data contained in, or transmitted with, this e-mail. If you received this e-mail in error, \n            please immediately notify the sender by return e-mail and delete the material from any computer. Any views expressed are personal unless otherwise stated.<br>&nbsp;</SPAN>\n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Order execution [[id_order]]','Dear customer,<br/>\r\n<br/>\r\nYour order with number [[id_order]] has been successfully executed.',0,10,'en-US',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    }\r\n]',1000009,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;        
      case 'order_execute_partial':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==7703 and $mytdt==1637619303 and $mytfsp==1407 and $mytdtp==1670526928) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (11,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 20:45:20','order_execute_partial','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr>          \n\n      <p>&nbsp;</p>    \n      <p style=\"font-size:22pt;padding:0px 0px 20px 0px;margin:0px;font-weight: bold;\">Αριθμός παραγγελίας: [[id_order]]</p>\n\n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Για οποιαδήποτε πληροφορία, επικοινωνήστε μαζί μας στο email  \n        <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Σας  ευχαριστούμε</p>\n        \n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Με εκτίμηση, η ομάδα του [[GKS_SITE_HUMAN_NAME]]  \n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a  href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n              \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι \n        διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής \n        νομοθεσίας περί διαφημιστικών μηνυμάτων: \n        «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον \n        δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC»\n        του Ευρωπαϊκού Κοινοβουλίου,\n        Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.      \n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a>\n        και στείλετε το email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail!\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n          Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: \n          Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται \n          και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος \n          ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση \n          ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη \n          για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση \n          δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, \n          ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. \n          Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά.            \n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Μερική εκτέλεση παραγγελίας [[id_order]]','Αγαπητέ πελάτη,<br/>\r\n<br/>\r\nΗ παραγγελία σας με αριθμό [[id_order]] έχει εκτελεστεί μερικώς.\r\n<br/>\nΘα ενημερωθείτε με άλλο μήνυμα για την εξέλιξη της παραγγελίας.',0,11,'el-GR',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    }\r\n]',1000011,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;          
      case 'order_execute_partial.en-US':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==5946 and $mytdt==1637619291 and $mytfsp==1307 and $mytdtp==1670526930) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (12,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 20:45:21','order_execute_partial.en-US','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr>          \n\n      <p>&nbsp;</p>\n      <p style=\"font-size:22pt;padding:0px 0px 20px 0px;margin:0px;font-weight: bold;\">Order number: [[id_order]]</p>\n  \n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">For any question please feel free to contact us at  \n        <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Thank you</p>\n        \n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Sincerely, the [[GKS_SITE_HUMAN_NAME]] team  \n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a  href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n              \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        This e-mail address cannot be considered spam as long as the sender\'s details and deletion procedures are listed and \n        fulfills the requirements of European advertising law legislation: \n        «Each message must bear the sender\'s complete information clearly and should enable the receiver to delete. Directiva2002/58/EC»\n        of the European Parliament, Relative as A5-270/2001 of the European Parliament.\n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        You can unsubscribe from the list by clicking this \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">link</a> \n        and sending the email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Please consider the environment before printing this e-mail !\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n            Confidentiality Warning - Disclaimer: This e-mail contains information intended only for the individual or entity to which it \n            is addressed and may contain confidential material. If the reader of this e-mail is not the intended recipient or the employee or agent responsible for delivering it to the intended recipient, \n            any dissemination, copying or other use of, or taking of any action in reliance upon this e-mail is strictly prohibited. The sender bears no responsibility for any loss, \n            disruption or damage to your data or computer system that may occur while using data contained in, or transmitted with, this e-mail. If you received this e-mail in error, \n            please immediately notify the sender by return e-mail and delete the material from any computer. Any views expressed are personal unless otherwise stated.<br>&nbsp;</SPAN>\n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Partial order fulfillment [[id_order]]','Dear customer,<br/>\r\n<br/>\r\nYour order with [[id_order]] has been partially executed.<br/>\r\nYou will be notified with another message about how the order will progress.',0,12,'en-US',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    }\r\n]',1000011,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;        
      case 'order_invoice':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==7703 and $mytdt==1637619330 and $mytfsp==1343 and $mytdtp==1670526921) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (13,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 20:45:25','order_invoice','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr>          \n\n      <p>&nbsp;</p>    \n      <p style=\"font-size:22pt;padding:0px 0px 20px 0px;margin:0px;font-weight: bold;\">Αριθμός παραγγελίας: [[id_order]]</p>\n\n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Για οποιαδήποτε πληροφορία, επικοινωνήστε μαζί μας στο email  \n        <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Σας  ευχαριστούμε</p>\n        \n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Με εκτίμηση, η ομάδα του [[GKS_SITE_HUMAN_NAME]]  \n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a  href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n              \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι \n        διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής \n        νομοθεσίας περί διαφημιστικών μηνυμάτων: \n        «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον \n        δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC»\n        του Ευρωπαϊκού Κοινοβουλίου,\n        Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.      \n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a>\n        και στείλετε το email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail!\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n          Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: \n          Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται \n          και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος \n          ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση \n          ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη \n          για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση \n          δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, \n          ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. \n          Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά.            \n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Παραστατικό της παραγγελίας [[id_order]]','Αγαπητέ πελάτη,<br/>\r\n<br/>\r\nΣας αποστέλλουμε το παραστατικό για την παραγγελία με [[id_order]] ως συνημμένο.',0,13,'el-GR',1,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    }\r\n]',1000013,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;        
      case 'order_invoice.en-US':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==5946 and $mytdt==1637619314 and $mytfsp==1217 and $mytdtp==1670526925) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (14,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 20:45:27','order_invoice.en-US','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr>          \n\n      <p>&nbsp;</p>\n      <p style=\"font-size:22pt;padding:0px 0px 20px 0px;margin:0px;font-weight: bold;\">Order number: [[id_order]]</p>\n  \n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">For any question please feel free to contact us at  \n        <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Thank you</p>\n        \n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Sincerely, the [[GKS_SITE_HUMAN_NAME]] team  \n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a  href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n              \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        This e-mail address cannot be considered spam as long as the sender\'s details and deletion procedures are listed and \n        fulfills the requirements of European advertising law legislation: \n        «Each message must bear the sender\'s complete information clearly and should enable the receiver to delete. Directiva2002/58/EC»\n        of the European Parliament, Relative as A5-270/2001 of the European Parliament.\n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        You can unsubscribe from the list by clicking this \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">link</a> \n        and sending the email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Please consider the environment before printing this e-mail !\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n            Confidentiality Warning - Disclaimer: This e-mail contains information intended only for the individual or entity to which it \n            is addressed and may contain confidential material. If the reader of this e-mail is not the intended recipient or the employee or agent responsible for delivering it to the intended recipient, \n            any dissemination, copying or other use of, or taking of any action in reliance upon this e-mail is strictly prohibited. The sender bears no responsibility for any loss, \n            disruption or damage to your data or computer system that may occur while using data contained in, or transmitted with, this e-mail. If you received this e-mail in error, \n            please immediately notify the sender by return e-mail and delete the material from any computer. Any views expressed are personal unless otherwise stated.<br>&nbsp;</SPAN>\n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Order invoice [[id_order]]','Dear customer,<br/>\r\n<br/>\r\nWe send you the invoice for order [[id_order]] as an attachment.',0,14,'en-US',1,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    }\r\n]',1000013,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;        
      case 'order_receive':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==7703 and $mytdt==1637619350 and $mytfsp==1264 and $mytdtp==1670526911) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (15,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 20:47:14','order_receive','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr>          \n\n      <p>&nbsp;</p>    \n      <p style=\"font-size:22pt;padding:0px 0px 20px 0px;margin:0px;font-weight: bold;\">Αριθμός παραγγελίας: [[id_order]]</p>\n\n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Για οποιαδήποτε πληροφορία, επικοινωνήστε μαζί μας στο email  \n        <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Σας  ευχαριστούμε</p>\n        \n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Με εκτίμηση, η ομάδα του [[GKS_SITE_HUMAN_NAME]]  \n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a  href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n              \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι \n        διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής \n        νομοθεσίας περί διαφημιστικών μηνυμάτων: \n        «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον \n        δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC»\n        του Ευρωπαϊκού Κοινοβουλίου,\n        Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.      \n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a>\n        και στείλετε το email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail!\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n          Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: \n          Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται \n          και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος \n          ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση \n          ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη \n          για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση \n          δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, \n          ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. \n          Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά.            \n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Παραγγελία [[id_order]]','Αγαπητέ πελάτη,<br/>\r\n<br/>\r\nέχουμε λάβει την παραγγελία σας με αριθμό [[id_order]]',0,15,'el-GR',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    }\r\n]',1000015,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;        
      case 'order_receive.en-US':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==5946 and $mytdt==1637619341 and $mytfsp==1199 and $mytdtp==1670526917) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (16,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 20:47:14','order_receive.en-US','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr>          \n\n      <p>&nbsp;</p>\n      <p style=\"font-size:22pt;padding:0px 0px 20px 0px;margin:0px;font-weight: bold;\">Order number: [[id_order]]</p>\n  \n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">For any question please feel free to contact us at  \n        <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Thank you</p>\n        \n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Sincerely, the [[GKS_SITE_HUMAN_NAME]] team  \n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a  href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n              \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        This e-mail address cannot be considered spam as long as the sender\'s details and deletion procedures are listed and \n        fulfills the requirements of European advertising law legislation: \n        «Each message must bear the sender\'s complete information clearly and should enable the receiver to delete. Directiva2002/58/EC»\n        of the European Parliament, Relative as A5-270/2001 of the European Parliament.\n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        You can unsubscribe from the list by clicking this \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">link</a> \n        and sending the email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Please consider the environment before printing this e-mail !\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n            Confidentiality Warning - Disclaimer: This e-mail contains information intended only for the individual or entity to which it \n            is addressed and may contain confidential material. If the reader of this e-mail is not the intended recipient or the employee or agent responsible for delivering it to the intended recipient, \n            any dissemination, copying or other use of, or taking of any action in reliance upon this e-mail is strictly prohibited. The sender bears no responsibility for any loss, \n            disruption or damage to your data or computer system that may occur while using data contained in, or transmitted with, this e-mail. If you received this e-mail in error, \n            please immediately notify the sender by return e-mail and delete the material from any computer. Any views expressed are personal unless otherwise stated.<br>&nbsp;</SPAN>\n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Order [[id_order]]','Dear customer,<br/>\r\n<br/>\r\nwe have received your order with number [[id_order]]',0,16,'en-US',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    }\r\n]',1000015,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;        
      case 'wait_bank_payment':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==7703 and $mytdt==1648405634 and $mytfsp==3225 and $mytdtp==1670526907) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (17,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-25 18:11:29','wait_bank_payment','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr>          \n\n      <p>&nbsp;</p>    \n      <p style=\"font-size:22pt;padding:0px 0px 20px 0px;margin:0px;font-weight: bold;\">Αριθμός παραγγελίας: [[id_order]]</p>\n\n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Για οποιαδήποτε πληροφορία, επικοινωνήστε μαζί μας στο email  \n        <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Σας  ευχαριστούμε</p>\n        \n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Με εκτίμηση, η ομάδα του [[GKS_SITE_HUMAN_NAME]]  \n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a  href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n              \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι \n        διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής \n        νομοθεσίας περί διαφημιστικών μηνυμάτων: \n        «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον \n        δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC»\n        του Ευρωπαϊκού Κοινοβουλίου,\n        Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.      \n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a>\n        και στείλετε το email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail!\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n          Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: \n          Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται \n          και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος \n          ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση \n          ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη \n          για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση \n          δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, \n          ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. \n          Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά.            \n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Παραγγελία [[id_order]]','Αγαπητέ πελάτη,<br/>\r\n<br/>\r\nΓια να προχωρήσει η παραγγελία σας θα πρέπει να κάνετε κατάθεση του ποσού <b>[[poso]]</b> σε έναν από τους παρακάτω τραπεζικούς λογαριασμούς:<br/>\r\n[[get_list_bank_accounts]]<br/>\r\n<br/>\r\nΚατά την διαδικασία της κατάθεσης, ορίστε στην Αιτιολογία τον αριθμό <b>[[bank_deposit_9digit]]</b> έτσι ώστε να μπορέσουμε να ταυτοποιήσουμε την κατάθεση με την συγκεκριμένη παραγγελία.<br/>\r\n<br/>\r\nΕάν υπάρχουν τυχόν έξοδα για την μεταφορά των χρημάτων, θα πρέπει να τα επιβαρυνθείτε εσείς.<br/>\r\n<br/>\r\nΔεν θα εκτελεστεί η παραγγελία σας εάν δεν συμφωνεί το τελικό ποσό.<br/>\r\n<br/>\r\nΣτείλτε μας το αποδεικτικό κατάθεσης με email στο <a href=\\\"mailto:[[GKS_SITE_EMAIL]]\\\">[[GKS_SITE_EMAIL]]</a><br/>\r\n<br/>\r\nΘα ενημερωθείτε με email ή/και με SMS για την εξέλιξη της παραγγελίας.<br/>\r\n<br/>',0,17,'el-GR',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"bank_deposit_9digit\",\r\n        \"id\": \"email_param_bank_deposit_9digit\",\r\n        \"px\": \"123456789\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"poso\",\r\n        \"id\": \"email_param_poso\",\r\n        \"px\": \"10,00 &euro;\"\r\n    },\r\n    {\r\n        \"type\": \"textarea\",\r\n        \"label\": \"get_list_bank_accounts\",\r\n        \"id\": \"email_param_get_list_bank_accounts\",\r\n        \"px\": \"GR12 3456 ... Δικαιούχος: ... Τράπεζα: ...\",\r\n        \"icon\": \"<i class=\'fa fa-university set_def_bank_accounts tooltipster_params\' style=\'font-size: 200%;color: green;cursor: pointer;\' title=\'Ορισμός προεπιλογής\'></i>\"\r\n    }\r\n]',1000017,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;
      case 'wait_bank_payment.en-US':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==5946 and $mytdt==1637619385 and $mytfsp==2660 and $mytdtp==1670526905) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (18,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-25 18:13:11','wait_bank_payment.en-US','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr>          \n\n      <p>&nbsp;</p>\n      <p style=\"font-size:22pt;padding:0px 0px 20px 0px;margin:0px;font-weight: bold;\">Order number: [[id_order]]</p>\n  \n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">For any question please feel free to contact us at  \n        <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n      </p>\n      <p style=\"padding:0px 0px 20px 0px;margin:0px;\">Thank you</p>\n        \n      \n      <p align=\"left\" style=\"margin-top: 40px;\">\n        Sincerely, the [[GKS_SITE_HUMAN_NAME]] team  \n      </p>\n\n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n      \n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a  href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n              \n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        This e-mail address cannot be considered spam as long as the sender\'s details and deletion procedures are listed and \n        fulfills the requirements of European advertising law legislation: \n        «Each message must bear the sender\'s complete information clearly and should enable the receiver to delete. Directiva2002/58/EC»\n        of the European Parliament, Relative as A5-270/2001 of the European Parliament.\n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        You can unsubscribe from the list by clicking this \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">link</a> \n        and sending the email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n\n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Please consider the environment before printing this e-mail !\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n            Confidentiality Warning - Disclaimer: This e-mail contains information intended only for the individual or entity to which it \n            is addressed and may contain confidential material. If the reader of this e-mail is not the intended recipient or the employee or agent responsible for delivering it to the intended recipient, \n            any dissemination, copying or other use of, or taking of any action in reliance upon this e-mail is strictly prohibited. The sender bears no responsibility for any loss, \n            disruption or damage to your data or computer system that may occur while using data contained in, or transmitted with, this e-mail. If you received this e-mail in error, \n            please immediately notify the sender by return e-mail and delete the material from any computer. Any views expressed are personal unless otherwise stated.<br>&nbsp;</SPAN>\n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Order [[id_order]]','Dear customer,<br/>\r\n<br/>\r\nTo advance your order you will need to deposit the amount <b>[[poso]]</b> in one of the following bank accounts:<br/>\r\n[[get_list_bank_accounts]]<br/>\r\n<br/>\r\nWhen submitting a deposit, specify the number in the Reason <b>[[bank_deposit_9digit]]</b> so that we can identify the deposit with that particular order.<br/>\r\n<br/>\r\nIf there are any costs involved in transferring the money, you will have to bear it.<br/>\r\n<br/>\r\nYour order will not be executed if the final amount does not match.<br/>\r\n<br/>\r\nEmail us your proof of deposit at <a href=\\\"mailto:[[GKS_SITE_EMAIL]]\\\">[[GKS_SITE_EMAIL]]</a><br/>\r\n<br/>\r\nYou will be informed by email and/or SMS about the progress of the order.<br/>\r\n<br/>',0,18,'en-US',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"bank_deposit_9digit\",\r\n        \"id\": \"email_param_bank_deposit_9digit\",\r\n        \"px\": \"123456789\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"poso\",\r\n        \"id\": \"email_param_poso\",\r\n        \"px\": \"10,00 &euro;\"\r\n    },\r\n    {\r\n        \"type\": \"textarea\",\r\n        \"label\": \"get_list_bank_accounts\",\r\n        \"id\": \"email_param_get_list_bank_accounts\",\r\n        \"px\": \"GR12 3456 ... Δικαιούχος: ... Τράπεζα: ...\",\r\n        \"icon\": \"<i class=\'fa fa-university set_def_bank_accounts tooltipster_params\' style=\'font-size: 200%;color: green;cursor: pointer;\' title=\'Ορισμός προεπιλογής\'></i>\"\r\n    }\r\n]',1000017,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;        
      case 'Έτοιμη η παραγγελία':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==6438 and $mytdt==1637618459 and $mytfsp==1347 and $mytdtp==1670526847) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (19,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 21:26:16','Έτοιμη η παραγγελία','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color: #f3f3f3;\">\n<p><br /></p>\n<table style=\"width: 600px; margin-top: 20px; margin-bottom: 20px; color: #000000;\" border=\"0\" width=\"600px\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\n<tbody>\n<tr>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n<td style=\"width: 570px;\" width=\"570px\">\n<p style=\"margin: 13px;\" align=\"center\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo200.png\" width=\"200px\" border=\"0\" /></a></p>\n<hr />\n<p style=\"margin-top: 40px;\">[[message]]</p>\n<p style=\"margin-top: 40px;\"><br /></p>\n<hr />\n<table style=\"width: 100%; color: #000000;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 20%;\" align=\"left\" width=\"20%\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo100.png\" width=\"100px\" border=\"0\" /></a></td>\n<td style=\"width: 60%;\" align=\"center\" width=\"60%\"><a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a></td>\n<td style=\"width: 20%; vertical-align: middle;\" align=\"right\" width=\"20%\">[[link_facebook]] [[link_twitter]] [[link_instagram]]</td>\n</tr>\n</tbody>\n</table>\n<hr />\n<p style=\"margin-top: 40px; text-align: left; font-size: 8pt;\" align=\"left\">Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής νομοθεσίας περί διαφημιστικών μηνυμάτων: «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC» του Ευρωπαϊκού Κοινοβουλίου, Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a> και στείλετε το email.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Copyright © [[year]] <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\">[[GKS_SITE_NAME]]</a> All rights reserved.</p>\n<hr />\n<table style=\"width: 100%;\" width=\"100%\">\n<tbody>\n<tr>\n<td>\n<p align=\"justify\"><span style=\"font-size: 12pt; font-family: \'Webdings\'; color: #00f000;\">P</span><span style=\"font-size: 12pt; font-family: \'Arial\'; color: #008000;\"> </span> <span style=\"font-size: 8pt; font-family: \'Arial\'; color: #00f000;\"> Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail! </span></p>\n<p align=\"justify\"><span style=\"font-size: 8pt; color: #000000;\"> Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά. </span></p>\n</td>\n</tr>\n</tbody>\n</table>\n</td>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n</tr>\n</tbody>\n</table>\n<p><br /></p>\n</center>\n</body>\n</html>','Παραγγελία [[id_order]]','Αγαπητέ πελάτη,<br/>\r\n<br/>\r\nΗ παραγγελία είναι έτοιμη, μπορείτε να την παραλάβετε.<br/>\r\n<br/>\r\nΜε εκτίμηση, η ομάδα του [[GKS_SITE_HUMAN_NAME]]',0,19,'el-GR',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_order\",\r\n        \"id\": \"email_param_id_order\",\r\n        \"px\": \"10\"\r\n    }\r\n]',1000019,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;
      case 'Κράτηση_αναμονή_πληρωμής':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==7024 and $mytdt==1648493643 and $mytfsp==5532 and $mytdtp==1648493145) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (21,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-25 18:11:12','Κράτηση αναμονή πληρωμής','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color: #f3f3f3;\">\n<p><br /></p>\n<table style=\"width: 600px; margin-top: 20px; margin-bottom: 20px; color: #000000;\" border=\"0\" width=\"600px\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\n<tbody>\n<tr>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n<td style=\"width: 570px;\" width=\"570px\">\n<p style=\"margin: 13px;\" align=\"center\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo200.png\" width=\"200px\" border=\"0\" /></a></p>\n<hr />\n<p><br /></p>\n<p style=\"font-size: 22pt; padding: 0px 0px 20px 0px; margin: 0px; font-weight: bold;\">Αριθμός Κράτησης: [[id_hotel_reservation]]</p>\n<p style=\"margin-top: 40px;\">[[message]]</p>\n<p style=\"padding: 0px 0px 20px 0px; margin: 0px;\">Για οποιαδήποτε πληροφορία, επικοινωνήστε μαζί μας στο email <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a></p>\n<p style=\"padding: 0px 0px 20px 0px; margin: 0px;\">Σας ευχαριστούμε</p>\n<p style=\"margin-top: 40px;\" align=\"left\">Με εκτίμηση, η ομάδα του [[GKS_SITE_HUMAN_NAME]]</p>\n<p style=\"margin-top: 40px;\"><br /></p>\n<hr />\n<table style=\"width: 100%; color: #000000;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 20%;\" align=\"left\" width=\"20%\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo100.png\" width=\"100px\" border=\"0\" /></a></td>\n<td style=\"width: 60%;\" align=\"center\" width=\"60%\"><a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a></td>\n<td style=\"width: 20%; vertical-align: middle;\" align=\"right\" width=\"20%\">[[link_facebook]] [[link_twitter]] [[link_instagram]]</td>\n</tr>\n</tbody>\n</table>\n<hr />\n<p style=\"margin-top: 40px; text-align: left; font-size: 8pt;\" align=\"left\">Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής νομοθεσίας περί διαφημιστικών μηνυμάτων: «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC» του Ευρωπαϊκού Κοινοβουλίου, Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a> και στείλετε το email.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Copyright © [[year]] <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\">[[GKS_SITE_NAME]]</a> All rights reserved.</p>\n<hr />\n<table style=\"width: 100%;\" width=\"100%\">\n<tbody>\n<tr>\n<td>\n<p align=\"justify\"><span style=\"font-size: 12pt; font-family: \'Webdings\'; color: #00f000;\">P</span><span style=\"font-size: 12pt; font-family: \'Arial\'; color: #008000;\"> </span> <span style=\"font-size: 8pt; font-family: \'Arial\'; color: #00f000;\"> Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail! </span></p>\n<p align=\"justify\"><span style=\"font-size: 8pt; color: #000000;\"> Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά. </span></p>\n</td>\n</tr>\n</tbody>\n</table>\n</td>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n</tr>\n</tbody>\n</table>\n<p><br /></p>\n</center>\n</body>\n</html>','Κράτηση [[id_hotel_reservation]]','Αγαπητέ επισκέπτη,<br/>\r\n<br/>\r\nΑυτή είναι η επιβεβαίωση για την κράτηση δωματίου στο [[GKS_SITE_HUMAN_NAME]].<br/>\r\nΕκ μέρους του ξενοδοχείου, θα θέλαμε να σας ευχαριστήσουμε που επιλέξατε τις υπηρεσίες μας.<br/>\r\nΠαρακαλούμε βρείτε όλες τις λεπτομέρειες σχετικά με την επιβεβαίωση της κράτησής σας που παρατίθενται παρακάτω:<br/>\r\n<br/>\r\n<b>Λεπτομέρειες :</b><br/>\r\n[[get_list_reservation_rooms]]<br/>\r\n<br/>\r\nΣύνολο: <b>[[poso]]</b><br/>\r\n<br/>\r\nΘα το εκτιμούσαμε πολύ αν μπορείτε να μας ενημερώσετε για τυχόν αλλαγές στο χρονοδιάγραμμα ή στο πρόγραμμά σας.<br/>\r\nΣε περίπτωση που απαιτείται να γίνει check-in νωρίτερα λόγω αλλαγών στην πτήση σας ή για οποιοδήποτε άλλο λόγο, παρακαλούμε ενημερώστε μας.<br/>\r\nΣύμφωνα με την Πολιτική Κρατήσεων & Ακυρώσεων λόγω μεγάλης ζήτησης στα δωμάτια μας, παρακαλούμε να μας καταθέσετε μέσω εμβάσματος το 30% του συνολικού ποσού πληρωμής σας ως εγγύηση της κράτησή σας.<br/>\r\nΤο έμβασμα μπορεί να γίνει σε έναν από τους παρακάτω λογαριασμούς τουλάχιστον μέχρι και 3 εβδομάδες πριν την άφιξή σας στις εγκαταστάσεις μας:<br/>\r\n<br/>\r\n[[get_list_bank_accounts]]<br/>\r\n<br/>\r\nΑιτιολογία εμβάσματος: Αριθμός κράτησης: RSRV[[id_hotel_reservation]]/[[bank_deposit_9digit]]<br/>\r\nΠοσό: <b>[[poso_pososto_30]]</b><br/>\r\n<br/>\r\nΕάν υπάρχουν τυχόν έξοδα για την μεταφορά των χρημάτων, θα πρέπει να τα επιβαρυνθείτε εσείς.<br/>\r\nΔεν θα προχωρήσει η κράτησή σας εάν δεν συμφωνεί το τελικό ποσό.<br/>\r\nΣτείλτε μας το αποδεικτικό κατάθεσης με email στο <a href=\\\"mailto:[[GKS_SITE_EMAIL]]\\\">[[GKS_SITE_EMAIL]]</a><br/>\r\nΘα ενημερωθείτε με email ή/και με SMS για την εξέλιξη της κράτησης.<br/>\r\n<br/>\r\nΕίμαστε βέβαιοι ότι θα βρείτε όλες τις υπηρεσίες μας ικανοποιητικές.<br/>\r\nΣε περίπτωση που θα πρέπει να ακυρώσετε την κράτηση σας, παρακαλούμε ενημερώστε μας τουλάχιστον 24 ώρες πριν την αναμενόμενη άφιξή σας.<br/>\r\nΑνυπομονούμε να σας προσφέρουμε ποιοτικές υπηρεσίες στο ξενοδοχείο μας.<br/>',0,21,'el-GR',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"id_hotel_reservation\",\r\n        \"id\": \"email_param_id_hotel_reservation\",\r\n        \"px\": \"10\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"bank_deposit_9digit\",\r\n        \"id\": \"email_param_bank_deposit_9digit\",\r\n        \"px\": \"123456789\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"poso\",\r\n        \"id\": \"email_param_poso\",\r\n        \"px\": \"10,00 &euro;\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"poso_pososto_30\",\r\n        \"id\": \"email_param_poso_pososto_30\",\r\n        \"px\": \"10,00 &euro;\"\r\n    },\r\n    {\r\n        \"type\": \"textarea\",\r\n        \"label\": \"get_list_reservation_rooms\",\r\n        \"id\": \"email_param_get_list_reservation_rooms\",\r\n        \"px\": \"\",\r\n        \"icon\": \"<i class=\'fa fa-hotel set_def_list_reservation_rooms tooltipster_params\' style=\'font-size: 200%;color: green;cursor: pointer;\' title=\'Ορισμός προεπιλογής\'></i>\"\r\n    },\r\n    {\r\n        \"type\": \"textarea\",\r\n        \"label\": \"get_list_bank_accounts\",\r\n        \"id\": \"email_param_get_list_bank_accounts\",\r\n        \"px\": \"GR12 3456 ... Δικαιούχος: ... Τράπεζα: ...\",\r\n        \"icon\": \"<i class=\'fa fa-university set_def_bank_accounts tooltipster_params\' style=\'font-size: 200%;color: green;cursor: pointer;\' title=\'Ορισμός προεπιλογής\'></i>\"\r\n    }\r\n]',1000021,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;
      case 'Ραντεβού':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==6784 and $mytdt==1670528207 and $mytfsp==2248 and $mytdtp==1670528089) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (23,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 21:30:55','Ραντεβού','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color: #f3f3f3;\">\n<p><br /></p>\n<table style=\"width: 600px; margin-top: 20px; margin-bottom: 20px; color: #000000;\" border=\"0\" width=\"600px\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\n<tbody>\n<tr>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n<td style=\"width: 570px;\" width=\"570px\">\n<p style=\"margin: 13px;\" align=\"center\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo200.png\" width=\"200px\" border=\"0\" /></a></p>\n<hr />\n<p><br /></p>\n<p style=\"font-size: 22pt; padding: 0px; margin: 0px; font-weight: bold; text-align: center;\">Ραντεβού</p>\n<p style=\"margin-top: 20px;\"><strong>Από:</strong> [[apo]]<br /><strong>Έως:</strong> [[eos]]<br /><strong>Περιγραφή:</strong> [[perigrafi]]<br /><strong>Τοποθεσία:</strong> [[topothesia]]</p>\n<p style=\"margin-top: 20px;\">[[message]]</p>\n<p style=\"margin-top: 20px;\"><br /></p>\n<hr />\n<table style=\"width: 100%; color: #000000;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 20%;\" align=\"left\" width=\"20%\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo100.png\" width=\"100px\" border=\"0\" /></a></td>\n<td style=\"width: 60%;\" align=\"center\" width=\"60%\"><a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a></td>\n<td style=\"width: 20%; vertical-align: middle;\" align=\"right\" width=\"20%\">[[link_facebook]] [[link_twitter]] [[link_instagram]]</td>\n</tr>\n</tbody>\n</table>\n<hr />\n<p style=\"margin-top: 40px; text-align: left; font-size: 8pt;\" align=\"left\">Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής νομοθεσίας περί διαφημιστικών μηνυμάτων: «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC» του Ευρωπαϊκού Κοινοβουλίου, Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a> και στείλετε το email.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Copyright © [[year]] <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\">[[GKS_SITE_NAME]]</a> All rights reserved.</p>\n<hr />\n<table style=\"width: 100%;\" width=\"100%\">\n<tbody>\n<tr>\n<td>\n<p align=\"justify\"><span style=\"font-size: 12pt; font-family: \'Webdings\'; color: #00f000;\">P</span><span style=\"font-size: 12pt; font-family: \'Arial\'; color: #008000;\"> </span> <span style=\"font-size: 8pt; font-family: \'Arial\'; color: #00f000;\"> Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail! </span></p>\n<p align=\"justify\"><span style=\"font-size: 8pt; color: #000000;\"> Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά. </span></p>\n</td>\n</tr>\n</tbody>\n</table>\n</td>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n</tr>\n</tbody>\n</table>\n<p><br /></p>\n</center>\n</body>\n</html>','Ραντεβού #[[id]]','Αγαπητέ κύριε κυρία [[pelatis_name]],<br/>\r\n<br/>\r\nτο ραντεβού σας με την εταιρεία μας έχει επιβεβαιωθεί με τα παραπάνω στοιχεία.<br/>\r\n<br/>\r\nΕάν προκύψει κάποια αλλαγή στο πρόγραμμά σας παρακαλούμε να μας ενημερώσετε έγκαιρα είτε τηλεφωνικά είτε με email.<br/>\r\n<br/>\r\nΜε εκτίμηση, η ομάδα του <br/>[[GKS_SITE_HUMAN_NAME]]',0,23,'el-GR',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"apo\",\r\n        \"id\": \"email_param_apo\",\r\n        \"px\": \"31/12/2021 10:00\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"eos\",\r\n        \"id\": \"email_param_eos\",\r\n        \"px\": \"31/12/2021 11:00\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"perigrafi\",\r\n        \"id\": \"email_param_perigrafi\",\r\n        \"px\": \"Κείμενο\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"topothesia\",\r\n        \"id\": \"email_param_topothesia\",\r\n        \"px\": \"Θεσσαλονίκη\"\r\n    }\r\n]',1000023,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;
      case 'Τιμολόγιο από transfer':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==6790 and $mytdt==1707992455 and $mytfsp==1049 and $mytdtp==1707218730) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (25,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-26 00:13:04','Τιμολόγιο από transfer','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color: #f3f3f3;\">\n<p><br /></p>\n<table style=\"width: 600px; margin-top: 20px; margin-bottom: 20px; color: #000000;\" border=\"0\" width=\"600px\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\n<tbody>\n<tr>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n<td style=\"width: 570px;\" width=\"570px\">\n<p style=\"margin: 13px;\" align=\"center\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo200.png\" width=\"200px\" border=\"0\" /></a></p>\n<hr />\n<p style=\"margin-top: 40px;\">Αγαπητέ πελάτη,<br /><br />Η απόδειξη είναι συνημμένο.<br /><br />Για οποιαδήποτε πληροφορία, επικοινωνήστε μαζί μας στο email <a href=\"mailto:[[GKS_OFFICIAL_SITE_URL]]\">[[GKS_OFFICIAL_SITE_URL]]</a> <br /><br />[[message]]<br /><br />Με εκτίμηση,<br />η ομάδα του [[GKS_SITE_HUMAN_NAME]]<br /><br /></p>\n<hr />\n<table style=\"width: 100%; color: #000000;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 20%;\" align=\"left\" width=\"20%\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo100.png\" width=\"100px\" border=\"0\" /></a></td>\n<td style=\"width: 60%;\" align=\"center\" width=\"60%\"><a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a></td>\n<td style=\"width: 20%; vertical-align: middle;\" align=\"right\" width=\"20%\">[[link_facebook]] [[link_twitter]] [[link_instagram]] [[link_pinterest]]</td>\n</tr>\n</tbody>\n</table>\n<hr />\n<p style=\"margin-top: 40px; text-align: left; font-size: 8pt;\" align=\"left\">Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής νομοθεσίας περί διαφημιστικών μηνυμάτων: «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC» του Ευρωπαϊκού Κοινοβουλίου, Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a> και στείλετε το email.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Copyright © [[year]] <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\">[[GKS_SITE_NAME]]</a> All rights reserved.</p>\n<hr />\n<table style=\"width: 100%;\" width=\"100%\">\n<tbody>\n<tr>\n<td>\n<p align=\"justify\"><span style=\"font-size: 12pt; font-family: \'Webdings\'; color: #00f000;\">P</span><span style=\"font-size: 12pt; font-family: \'Arial\'; color: #008000;\"> </span> <span style=\"font-size: 8pt; font-family: \'Arial\'; color: #00f000;\"> Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail! </span></p>\n<p align=\"justify\"><span style=\"font-size: 8pt; color: #000000;\"> Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά. </span></p>\n</td>\n</tr>\n</tbody>\n</table>\n</td>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n</tr>\n</tbody>\n</table>\n<p><br /></p>\n</center>\n</body>\n</html>','[[GKS_SITE_HUMAN_NAME]]',NULL,0,25,'el-GR',1,NULL,1000025,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;
      case 'Τιμολόγιο από transfer.en-US':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==5069 and $mytdt==1707993174 and $mytfsp==1049 and $mytdtp==1707219786) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (26,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-26 00:24:38','Τιμολόγιο από transfer.en-US','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color: #f3f3f3;\">\n<p><br /></p>\n<table style=\"width: 600px; margin-top: 20px; margin-bottom: 20px; color: #000000;\" border=\"0\" width=\"600px\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\n<tbody>\n<tr>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n<td style=\"width: 570px;\" width=\"570px\">\n<p style=\"margin: 13px;\" align=\"center\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo200.png\" width=\"200px\" border=\"0\" /></a></p>\n<hr />\n<p style=\"margin-top: 40px;\">Dear customer,<br /><br />The receipt is attached.<br /><br />For any information, contact us at <a href=\"mailto:[[GKS_OFFICIAL_SITE_URL]]\">[[GKS_OFFICIAL_SITE_URL]]</a> <br /><br />[[message]]<br /><br />Yours sincerely,<br />the [[GKS_SITE_HUMAN_NAME]] team</p>\n<p style=\"margin-top: 40px;\"><br /></p>\n<hr />\n<table style=\"width: 100%; color: #000000;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 20%;\" align=\"left\" width=\"20%\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo100.png\" width=\"100px\" border=\"0\" /></a></td>\n<td style=\"width: 60%;\" align=\"center\" width=\"60%\"><a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a></td>\n<td style=\"width: 20%; vertical-align: middle;\" align=\"right\" width=\"20%\">[[link_facebook]] [[link_twitter]] [[link_instagram]] [[link_pinterest]]</td>\n</tr>\n</tbody>\n</table>\n<hr />\n<p style=\"margin-top: 40px; text-align: left; font-size: 8pt;\" align=\"left\"><br /></p>\n<p style=\"margin-top: 40px; text-align: left; font-size: 8pt;\" align=\"left\">This e-mail cannot be considered spam as long as the sender\'s details and unlisting procedures are indicated and it meets the requirements of the European legislation on advertising messages: \"Each message should clearly state the sender\'s full details and must give the recipient the option to delete. Directive2002/58/EC\' of the European Parliament, Relative as A5-270/2001 of the European Parliament.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">You can unsubscribe from the list by clicking on this <a href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">link</a> and send the email.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Copyright © [[year]] <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\">[[GKS_SITE_NAME]]</a> All rights reserved.</p>\n<table style=\"width: 100%;\" width=\"100%\">\n<tbody>\n<tr>\n<td>\n<p align=\"justify\"><span style=\"font-size: 12pt; font-family: \'Webdings\'; color: #00f000;\">P</span><span style=\"font-size: 12pt; font-family: \'Arial\'; color: #008000;\"> </span><span style=\"font-size: 8pt; font-family: \'Arial\'; color: #00f000;\">Consider the environment before printing this e-mail!</span></p>\n<p align=\"justify\"><span style=\"font-size: 8pt; color: #000000;\">Confidentiality Warning - Disclaimer: This email contains information intended only for the individual or entity to whom it is addressed and may contain confidential material. If the reader of this e-mail is not the intended recipient or the employee or agent responsible for its delivery to the intended recipient, any dissemination, copying or other use or taking of any action based on this e-mail is strictly prohibited . The sender is not responsible for any loss, interruption or damage to your data or computer system that may occur when using data contained in, or transmitted by, this e-mail. If you received this e-mail in error, please notify the sender immediately by returning e-mail and delete the material from any computer. Any opinions expressed are personal unless otherwise stated.</span></p>\n</td>\n</tr>\n</tbody>\n</table>\n</td>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n</tr>\n</tbody>\n</table>\n<p><br /></p>\n</center>\n</body>\n</html>','[[GKS_SITE_HUMAN_NAME]]',NULL,0,26,'en-US',1,NULL,1000025,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;
      case 'Τιμολόγιο συνημμένο':   
        //echo '<pre>'.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==6416 and $mytdt==1637619371 and $mytfsp==1407 and $mytdtp==1635849836) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (27,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-26 00:24:38','Τιμολόγιο συνημμένο','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color: #f3f3f3;\">\n<p><br /></p>\n<table style=\"width: 600px; margin-top: 20px; margin-bottom: 20px; color: #000000;\" border=\"0\" width=\"600px\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\n<tbody>\n<tr>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n<td style=\"width: 570px;\" width=\"570px\">\n<p style=\"margin: 13px;\" align=\"center\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo200.png\" width=\"200px\" border=\"0\" /></a></p>\n<hr />\n<p style=\"margin-top: 40px;\">[[message]]<br /><br /><br /></p>\n<hr />\n<table style=\"width: 100%; color: #000000;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 20%;\" align=\"left\" width=\"20%\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo100.png\" width=\"100px\" border=\"0\" /></a></td>\n<td style=\"width: 60%;\" align=\"center\" width=\"60%\"><a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a></td>\n<td style=\"width: 20%; vertical-align: middle;\" align=\"right\" width=\"20%\">[[link_facebook]] [[link_twitter]] [[link_instagram]]</td>\n</tr>\n</tbody>\n</table>\n<hr />\n<p style=\"margin-top: 40px; text-align: left; font-size: 8pt;\" align=\"left\">Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής νομοθεσίας περί διαφημιστικών μηνυμάτων: «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC» του Ευρωπαϊκού Κοινοβουλίου, Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a> και στείλετε το email.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Copyright © [[year]] <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\">[[GKS_SITE_NAME]]</a> All rights reserved.</p>\n<hr />\n<table style=\"width: 100%;\" width=\"100%\">\n<tbody>\n<tr>\n<td>\n<p align=\"justify\"><span style=\"font-size: 12pt; font-family: \'Webdings\'; color: #00f000;\">P</span><span style=\"font-size: 12pt; font-family: \'Arial\'; color: #008000;\"> </span> <span style=\"font-size: 8pt; font-family: \'Arial\'; color: #00f000;\"> Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail! </span></p>\n<p align=\"justify\"><span style=\"font-size: 8pt; color: #000000;\"> Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά. </span></p>\n</td>\n</tr>\n</tbody>\n</table>\n</td>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n</tr>\n</tbody>\n</table>\n<p><br /></p>\n</center>\n</body>\n</html>','[[GKS_SITE_HUMAN_NAME]] Παραστατικό','Αγαπητέ πελάτη,<br/>\r\n<br/>\r\nΤο παραστατικό είναι συνημμένο.\r\n<br/>\r\n<br/>\r\nΣτο υποσέλιδο που παραστατικού υπάρχει ο τραπεζικός λογαριασμός.\r\n<br/>\r\n<br/>\r\nΓια οποιαδήποτε πληροφορία, επικοινωνήστε μαζί μας στο email [[GKS_SITE_EMAIL]]\r\n<br/>\r\n<br/>\r\nΜε εκτίμηση,\r\n<br/>\r\nη ομάδα του [[GKS_SITE_HUMAN_NAME]]',0,27,'el-GR',1,NULL,1000027,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;
      case 'Τιμολόγιο συνημμένο-en-US':   
        //echo '<pre>'.$myt.' '.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==4849 and $mytdt==1678391297 and $mytfsp==1205 and $mytdtp==1678383833) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (28,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-23 22:01:28','Τιμολόγιο συνημμένο.en-US','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color: #f3f3f3;\">\n<p><br /></p>\n<table style=\"width: 600px; margin-top: 20px; margin-bottom: 20px; color: #000000;\" border=\"0\" width=\"600px\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"#ffffff\">\n<tbody>\n<tr>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n<td style=\"width: 570px;\" width=\"570px\">\n<p style=\"margin: 13px;\" align=\"center\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo200.png\" width=\"200px\" border=\"0\" /></a></p>\n<hr />\n<p style=\"margin-top: 40px;\">[[message]]<br /><br /><br /></p>\n<hr />\n<table style=\"width: 100%; color: #000000;\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\n<tbody>\n<tr>\n<td style=\"width: 20%;\" align=\"left\" width=\"20%\"><a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\"><img src=\"/my/_current/_img_site/logo100.png\" width=\"100px\" border=\"0\" /></a></td>\n<td style=\"width: 60%;\" align=\"center\" width=\"60%\"><a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a></td>\n<td style=\"width: 20%; vertical-align: middle;\" align=\"right\" width=\"20%\">[[link_facebook]] [[link_twitter]] [[link_instagram]]</td>\n</tr>\n</tbody>\n</table>\n<hr />\n<p style=\"margin-top: 40px; text-align: left; font-size: 8pt;\" align=\"left\">This e-mail cannot be considered spam as long as the sender\'s details and unlisting procedures are indicated and it meets the requirements of the European legislation on advertising messages: \"Each message should clearly state the sender\'s full details and must give the recipient the option to delete. Directive2002/58/EC\' of the European Parliament, Relative as A5-270/2001 of the European Parliament.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">You can unsubscribe from the list by clicking on this <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a> and send the email.</p>\n<p style=\"text-align: center; font-size: 8pt;\" align=\"center\">Copyright © [[year]] <a style=\"color: blue; text-decoration: none; outline: none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\" rel=\"noopener\">[[GKS_SITE_NAME]]</a> All rights reserved.</p>\n<hr />\n<table style=\"width: 100%;\" width=\"100%\">\n<tbody>\n<tr>\n<td>\n<p align=\"justify\"><span style=\"font-size: 12pt; font-family: \'Webdings\'; color: #00f000;\">P</span><span style=\"font-size: 12pt; font-family: \'Arial\'; color: #008000;\"> </span> <span style=\"font-size: 8pt; font-family: \'Arial\'; color: #00f000;\"> Consider the environment before printing this e-mail! </span></p>\n<p align=\"justify\"><span style=\"font-size: 8pt; color: #000000;\"> Confidentiality Warning - Disclaimer: This email contains information intended only for the individual or entity to whom it is addressed and may contain confidential material. If the reader of this e-mail is not the intended recipient or the employee or agent responsible for its delivery to the intended recipient, any dissemination, copying or other use or taking of any action based on this e-mail is strictly prohibited . The sender is not responsible for any loss, interruption or damage to your data or computer system that may occur when using data contained in, or transmitted by, this e-mail. If you received this e-mail in error, please notify the sender immediately by returning e-mail and delete the material from any computer. Any opinions expressed are personal unless otherwise stated. </span></p>\n</td>\n</tr>\n</tbody>\n</table>\n</td>\n<td style=\"width: 15px;\" width=\"15px\"><br /></td>\n</tr>\n</tbody>\n</table>\n<p><br /></p>\n</center>\n</body>\n</html>','[[GKS_SITE_HUMAN_NAME]] invoice','Dear customer,<br/>\r\n<br/>\r\nThe invoice is attached.\r\n<br/>\r\n<br/>\r\nIn the footer of that document there is the bank account.\r\n<br/>\r\n<br/>\r\nFor any information, contact us at [[GKS_SITE_EMAIL]]\r\n<br/>\r\n<br/>\r\nYours sincerely,\r\n<br/>\r\nthe [[GKS_SITE_HUMAN_NAME]] team',0,28,'en-US',1,NULL,1000027,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;
      case 'ΑΛΠ':   
        //echo '<pre>'.$myt.' '.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==7088 and $mytdt==1735580292 and $mytfsp==1723 and $mytdtp==1740216637) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (51,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-26 12:52:41','ΑΛΠ','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr> \n      \n     \n     \n      \n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      \n\n      \n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n\n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι \n        διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής \n        νομοθεσίας περί διαφημιστικών μηνυμάτων: \n        «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον \n        δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC»\n        του Ευρωπαϊκού Κοινοβουλίου,\n        Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.      \n      \n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        \n        Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a>\n        και στείλετε το email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n \n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail!\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n          Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: \n          Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται \n          και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος \n          ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση \n          ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη \n          για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση \n          δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, \n          ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. \n          Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά.            \n            \n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Παραστατικό από ΥΚ Μedica για την παραγγελία [[ref_number]]','Αγαπητέ/ή,<br/>\r\n<br/>Το παραστατικό είναι συνημμένο.<br/>\r\n<br/>Η αποστολή της παραγγελίας σας [[ref_number]] έχει δρομολογηθεί, με αναμενόμενη παράδοση σε 2-3 εργάσιμες ημέρες.<br/>\r\n<br/>Για οποιαδήποτε πληροφορία, επικοινωνήστε μαζί μας στο email info@ykmedica.gr<br/>\r\n<br/>\r\nΜε εκτίμηση, η ομάδα της [[GKS_SITE_HUMAN_NAME]]',0,51,'el-GR',1,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"ref_number\",\r\n        \"id\": \"email_param_ref_number\",\r\n        \"px\": \"#1234\"\r\n    }\r\n]',0,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;
      case 'ΑΛΠ Novalis':   
        //echo '<pre>'.$myt.' '.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==7088 and $mytdt==1735580292 and $mytfsp==1937 and $mytdtp==1746887480) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (52,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-26 13:01:00','ΑΛΠ Novalis','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr> \n      \n     \n     \n      \n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      \n\n      \n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n\n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι \n        διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής \n        νομοθεσίας περί διαφημιστικών μηνυμάτων: \n        «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον \n        δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC»\n        του Ευρωπαϊκού Κοινοβουλίου,\n        Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.      \n      \n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        \n        Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a>\n        και στείλετε το email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n \n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail!\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n          Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: \n          Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται \n          και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος \n          ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση \n          ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη \n          για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση \n          δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, \n          ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. \n          Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά.            \n            \n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Νέα Παραγγελία προς αποστολή από ΥΚ Μedica [[ref_number]] [[contact_name]]','Αγαπητές,<br/><br/>\r\nΝέα παραγγελία με αριθμό [[ref_number]].<br/>\r\nστο όνομα [[contact_name]].<br/>\r\nΤο παραστατικό είναι συνημμένο.<br/>\r\nΠαρακαλούμε για την αποστολή της.<br/>\r\n<br/>\r\nΜε εκτίμηση, η ομάδα της [[GKS_SITE_HUMAN_NAME]]',0,52,'el-GR',1,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"ref_number\",\r\n        \"id\": \"email_param_ref_number\",\r\n        \"px\": \"#1234\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"contact_name\",\r\n        \"id\": \"email_param_contact_name\",\r\n        \"px\": \"Κώστας Γουτ\"\r\n    },\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"to\",\r\n        \"id\": \"email_param_to\",\r\n        \"px\": \"info@gks.gr\",\r\n        \"value\": \"backoffice@novalisvita.gr\"\r\n    }\r\n]',0,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;
      case 'ΑΛΠ Αποστολή':   
        //echo '<pre>'.$myt.' '.$mytfs.' '.$mytdt.' '.$mytfsp.' '.$mytdtp;die();   
        if ($mytfs==7088 and $mytdt==1735580292 and $mytfsp==1596 and $mytdtp==1740219546) {
          gks_run_sql("INSERT INTO `gks_email_template` (`id_email_template`,`mydate_add`,`mydate_edit`,`user_id_add`,`user_id_edit`,`myip`,`odbc`,`email_template_descr`,`email_body`,`email_subject`,`email_message`,`is_disable`,`sortorder`,`gks_lang`,`need_attachments`,`other_fields`,`localization_set_id`,`edit_mode`) VALUES 
           (53,'2020-01-01 00:00:00','2020-01-01 00:00:00',2,2,'127.0.0.1','2025-07-26 12:54:13','ΑΛΠ Αποστολή','<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n<html xmlns=\"https://www.w3.org/1999/xhtml\" xmlns:v=\"urn:schemas-microsoft-com:vml\" xmlns:o=\"urn:schemas-microsoft-com:office:office\">\n<head>\n<!--[if gte mso 9]><xml>\n<o:OfficeDocumentSettings>\n<o:AllowPNG/>\n<o:PixelsPerInch>96</o:PixelsPerInch>\n</o:OfficeDocumentSettings>\n</xml><![endif]-->\n\n  <title>[[GKS_SITE_HUMAN_NAME]]</title>\n\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\n  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\" />\n  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\" />\n  <meta name=\"format-detection\" content=\"telephone=no\">\n  \n  <base href=\"[[GKS_SITE_URL]]\" target=\"_blank\">\n\n  <style>\n    html, body {margin: 0 auto !important;padding: 0 !important;color:#ffffff;font-size:12pt;font-family:arial, verdana, sans-serif;mso-line-height-rule: exactly;}    \n    * {-ms-text-size-adjust: 100%;}    \n		a{color:blue;text-decoration:none;outline:none;}\n		table, td {mso-table-lspace: 0pt !important;mso-table-rspace: 0pt !important;}	\n		img {-ms-interpolation-mode:bicubic;}\n		\n		\n  </style>\n</head>\n<body bgcolor=\"#f3f3f3\" color=\"#ffffff\">\n<center style=\"background-color:#f3f3f3;\">\n<p>&nbsp;</p>\n<table width=\"600px\" style=\"width:600px; margin-top:20px;margin-bottom:20px;color:#000000;\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" bgcolor=\"#ffffff\" color=\"#000000\">\n  <tr>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    <td width=\"570px\" style=\"width:570px\">\n      <p align=\"center\" style=\"margin:13px;\">\n        <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"200px\" src=\"/my/_current/_img_site/logo200.png\" border=\"0\"></a>\n      </p>  \n      <hr> \n      \n     \n     \n      \n      <p  style=\"margin-top: 40px;\">\n      [[message]]\n      </p>\n      \n\n      \n      <p style=\"margin-top: 40px;\">&nbsp;</p>\n\n      <hr>\n      <table role=\"presentation\" width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"width:100%;color:#000000;\">\n        <tr>\n          <td width=\"20%\" align=\"left\" style=\"width:20%\">\n            <a href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\"><img width=\"100px\" src=\"/my/_current/_img_site/logo100.png\" border=\"0\"></a>\n          </td>  \n          <td width=\"60%\" align=\"center\" style=\"width:60%\">\n          <a href=\"mailto:[[GKS_SITE_EMAIL]]\">[[GKS_SITE_EMAIL]]</a>\n          \n            \n          </td>  \n          <td width=\"20%\" align=\"right\" style=\"width:20%;vertical-align: middle;\">\n            [[link_facebook]]\n            [[link_twitter]]\n            [[link_instagram]]\n          </td>  \n        </tr>  \n      </table>\n      <hr>\n      \n\n      <p align=\"left\" style=\"margin-top: 40px;text-align:left;font-size:8pt;\">\n        Αυτό το e-mail δεν μπορεί να θεωρηθεί spam εφόσον αναγράφονται τα στοιχεία του αποστολέα και οι \n        διαδικασίες διαγραφής από τη λίστα παραληπτών και πληρεί τις προϋποθέσεις της Ευρωπαϊκής \n        νομοθεσίας περί διαφημιστικών μηνυμάτων: \n        «Κάθε μήνυμα θα πρέπει να φέρει τα πλήρη στοιχεία του αποστολέα ευκρινώς και θα πρέπει να δίνει στον \n        δέκτη τη δυνατότητα διαγραφής. Directiva2002/58/EC»\n        του Ευρωπαϊκού Κοινοβουλίου,\n        Relative as A5-270/2001 του Ευρωπαϊκού Κοινοβουλίου.      \n      \n      </p>\n\n      \n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        \n        Μπορείτε να διαγραφείτε από την λίστα εάν κάνετε κλικ στο αυτόν τον \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"mailto:[[GKS_SITE_EMAIL]]?subject=remove\">σύνδεσμο</a>\n        και στείλετε το email.    \n      </p>\n\n\n\n      <p align=\"center\" style=\"text-align:center;font-size:8pt;\">\n        Copyright © [[year]] \n        <a style=\"color:blue;text-decoration:none;outline:none;\" href=\"[[GKS_OFFICIAL_SITE_URL]]\" target=\"_blank\">[[GKS_SITE_NAME]]</a> \n        All rights reserved.\n      </p>\n \n      <hr> \n\n      <table width=\"100%\" style=\"width:100%;\">\n        <tr>\n          <td >\n          <P align=justify><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Webdings\'; COLOR: #00f000\">P</SPAN><SPAN style=\"FONT-SIZE: 12pt; FONT-FAMILY: \'Arial\'; COLOR: #008000\"> </SPAN>\n            <SPAN style=\"FONT-SIZE: 8pt; FONT-FAMILY: \'Arial\'; COLOR: #00f000\">\n              Σκεφτείτε το περιβάλλον πριν εκτυπώσετε αυτό το e-mail!\n            </SPAN>\n          </P>\n          <P align=justify><SPAN style=\"FONT-SIZE: 8pt;color:#000000;\">\n          Προειδοποίηση εμπιστευτικότητας - Αποποίηση ευθυνών: \n          Αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου περιέχει πληροφορίες που προορίζονται μόνο για το άτομο ή την οντότητα στην οποία απευθύνεται \n          και ενδέχεται να περιέχει εμπιστευτικό υλικό. Εάν ο αναγνώστης αυτού του e-mail δεν είναι ο προοριζόμενος παραλήπτης ή ο υπάλληλος \n          ή ο πράκτορας που είναι υπεύθυνος για την παράδοσή του στον προοριζόμενο παραλήπτη, οποιαδήποτε διάδοση, αντιγραφή ή άλλη χρήση \n          ή ανάληψη οποιασδήποτε ενέργειας βάσει αυτού του e-mail είναι αυστηρά απαγορευμένος. Ο αποστολέας δεν φέρει καμία ευθύνη \n          για οποιαδήποτε απώλεια, διακοπή ή ζημιά στα δεδομένα ή το σύστημα του υπολογιστή σας που μπορεί να προκύψει κατά τη χρήση \n          δεδομένων που περιέχονται σε αυτό, ή μεταδίδονται με αυτό το e-mail. Εάν λάβατε αυτό το μήνυμα ηλεκτρονικού ταχυδρομείου κατά λάθος, \n          ενημερώστε αμέσως τον αποστολέα επιστρέφοντας e-mail και διαγράψτε το υλικό από οποιονδήποτε υπολογιστή. \n          Τυχόν απόψεις που εκφράζονται είναι προσωπικές, εκτός αν αναφέρεται διαφορετικά.            \n            \n          </P>\n          </td>\n        </tr>\n      </table>\n      \n    </td>\n    <td width=\"15px\" style=\"width:15px\"></td>\n    </td>      \n  </tr>\n    \n\n    \n</table>\n<p>&nbsp;</p>\n</center>\n\n</body>\n</html>','Αποστολή παραγγελίας [[ref_number]] από ΥΚ Μedica','Αγαπητέ/ή,<br/><br/>\r\nΗ παραγγελία σας έχει αποσταλεί με ELTA Courier Γενική Ταχυδρομική, με αριθμό voucher xxxxxxxxxxxx<br/>\r\n<br/>\r\nΓια οποιαδήποτε πληροφορία, επικοινωνήστε μαζί μας στο email info@ykmedica.gr<br/>\r\n<br/>\r\nΜε εκτίμηση, η ομάδα της [[GKS_SITE_HUMAN_NAME]]',0,53,'el-GR',0,'[\r\n    {\r\n        \"type\": \"text\",\r\n        \"label\": \"ref_number\",\r\n        \"id\": \"email_param_ref_number\",\r\n        \"px\": \"#1234\"\r\n    }\r\n]',0,'html')");
          unlink($mydir.$myt.'.html');
          unlink($mydir.$myt.'.params.html');
          $myt='';
        }
        break;

        
    }
     
  } 
  unset($myt);
  

}



$email_errors=[];
foreach ($mytemplates as $myt) {
  if ($myt!='') $email_errors[]= '<div style="color: white;background-color: red;padding: 10px;border: 2px solid black;"><b>'.$myt.'</b> email template convert error</div>';
}

if (count($email_errors)>0) {
  echo implode('',$email_errors);
  die();
}




