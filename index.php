<?php
#   Copyright by: Manuel
#   Support: www.ilch.de
define ( 'main' , TRUE );

session_name  ('sid');
session_start ();

require_once ('include/includes/config.php');
require_once ('include/includes/loader.php');

$ILCH_HEADER_ADDITIONS .= '<link rel="stylesheet" type="text/css" href="include/includes/css/ilch_default.css">';

db_connect();
$allgAr = getAllgAr ();
$menu = new menu();
user_identification();
site_statistic();

if (db_count_query('SELECT COUNT(*) FROM `prefix_config` WHERE `schl` = "wartung"') != 1) {echo 'ja is da';}

if (is_admin()) { 
  require_once ('include/contents/'.$menu->get_url());
} 

$lol = 1;

if ($allgAr['wartung'] == 1) {
  require_once ('include/contents/wartung.php');
} else {
  require_once ('include/contents/'.$menu->get_url());
}

db_close();
if (FALSE) { //debugging aktivieren
	debug('anzahl sql querys: '.$count_query_xyzXYZ);
	debug('',1,true);
}
?>