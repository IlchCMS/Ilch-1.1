<?php
#   Copyright by: Manuel
#   Support: www.ilch.de

define ( 'main' , TRUE );

//Konfiguration zur Anzeige von Fehlern
//Auf http://www.php.net/manual/de/function.error-reporting.php sind die verfügbaren Modi aufgelistet

//Seit php-5.3 ist eine Angabe der TimeZone Pflicht
if (version_compare(phpversion(), '5.3') != -1) {
	if (E_ALL > E_DEPRECATED) {
		@error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
	} else {
		@error_reporting(E_ALL ^ E_NOTICE);
	}
	date_default_timezone_set('Europe/Berlin');
} else {
	@error_reporting(E_ALL ^ E_NOTICE);
}
@ini_set('display_errors','On');

session_name  ('sid');
session_start ();

require_once ('include/includes/config.php');
require_once ('include/includes/loader.php');

db_connect();
$allgAr = getAllgAr ();
$menu = new menu();
user_identification();
site_statistic();

require_once ('include/contents/'.$menu->get_url());

db_close();
if (false) { //debugging aktivieren
	debug('anzahl sql querys: '.$count_query_xyzXYZ);
	debug('',1,true);
}
?>