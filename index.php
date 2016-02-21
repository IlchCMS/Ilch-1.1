<?php

#   Copyright by: Manuel
#   Support: www.ilch.de
define('main', TRUE);

session_name('sid');
session_start();

require_once ('include/includes/config.php');
require_once ('include/includes/loader.php');

//TODO: move to design class!
$ILCH_HEADER_ADDITIONS .= '<link rel="stylesheet" type="text/css" href="include/includes/css/ilch_default.css">';

db_connect();
$allgAr = getAllgAr();
$menu = new menu();
user_identification();
site_statistic();

if (is_admin()) {
    require_once ('include/contents/' . $menu->get_url());
}

if ($menu->get(0) == 'user' AND $menu->get(1) == 'remind' OR $menu->get(0) == 'user' AND $menu->get(1) == '13' AND $menu->get(2) == 'admin') {
    require_once ('include/contents/user/password_reminder.php');
} elseif ($allgAr['wartung'] == 1) {
    require_once ('include/contents/wartung.php');
} else {
    require_once ('include/contents/' . $menu->get_url());
}

db_close();
if (FALSE) { //debugging aktivieren
    debug('anzahl sql querys: ' . $count_query_xyzXYZ);
    debug('', 1, true);
}
?>