<?php
#   Copyright by: Manuel
#   Support: www.ilch.de

define ( 'main' , TRUE );
define ( 'admin', TRUE );

//Konfiguration zur Anzeige von Fehlern
//Auf http://www.php.net/manual/de/function.error-reporting.php sind die verfügbaren Modi aufgelistet
@error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
@ini_set('display_errors','On');

//Seid php-5.3 ist eine Angabe der TimeZone Pflicht
date_default_timezone_set('Europe/Berlin');

session_name  ('sid');
session_start ();

require_once ('include/includes/config.php');
require_once ('include/includes/loader.php');

db_connect();
$allgAr = getAllgAr ();
user_identification();
$menu = new menu();

if ( user_has_admin_right($menu) ) {
  require_once ('include/admin/'.$menu->get_url('admin'));
}

db_close();
?>