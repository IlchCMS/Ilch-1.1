<?php
#   Copyright by: Manuel
#   Support: www.ilch.de
define ( 'main' , TRUE );
// Konfiguration zur Anzeige von Fehlern
// Auf http://www.php.net/manual/de/function.error-reporting.php sind die verfόgbaren Modi aufgelistet
// Seit php-5.3 ist eine Angabe der TimeZone Pflicht
// Seit php-5.4 ist der default-charset auf Utf-8 standardmδίig eingestellt - ilch1.1 lδuft jedoch noch auf ISO-8859-1
if (version_compare(substr(PHP_VERSION, 0,3), '5.3') != -1) {
	if (E_ALL > E_DEPRECATED) {
		@error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
	} else {
		@error_reporting(E_ALL ^ E_NOTICE);
	}
	date_default_timezone_set('Europe/Berlin');
} 
else {
	@error_reporting(E_ALL ^ E_NOTICE);	
}
if (@ini_get('default_charset') != 'ISO-8851-1'){
	@ini_set('default_charset', 'ISO-8859-1');
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
if (FALSE) { //debugging aktivieren
	debug('anzahl sql querys: '.$count_query_xyzXYZ);
	debug('',1,true);
}
?>