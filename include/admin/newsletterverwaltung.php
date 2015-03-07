<?php

#   Copyright 	by: Felix Hohlwegler
#   Support: 	www.felix-hohlwegler.de
# 	Version 	0.1
#	Datum:		12.07.2014

$tpl = new tpl('newsletterverwaltung', 1);

defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Admins Area', 'Admins Area', 2);
$design->header();

#-----------------------------------------------------------------------------------------------------------------------------------------------
#Eintrag Löschen Tag -> OK
#-----------------------------------------------------------------------------------------------------------------------------------------------
if (!empty($_GET['delete'])) {
    $delete = escape($_GET['delete'], 'string');
    db_query('DELETE FROM `prefix_newsletter` WHERE email = "' . $delete . '" LIMIT 1');
}

$tpl->out(0);

#Abfrage der Eingetragenen Email-Adressen
$countABF = db_query('SELECT COUNT(*) as zahl FROM prefix_newsletter');
$erg = db_query('SELECT * FROM `prefix_newsletter` ORDER BY email');
$count = db_fetch_assoc($countABF);


if ($count['zahl'] > 0) {
    #ausgabe der Mail-Adressen
    while ($row = db_fetch_assoc($erg)) {
        $clas = ($clas == 'Cmite' ? 'Cnorm' : 'Cmite' );
        $row['class'] = $clas;
        $tpl->set_ar_out($row, 1);
    }
} else {
    echo "<p> Keine Eintragungen vorhanden </p>";
}
$tpl->out(2);
echo mysql_error();
$design->footer();
?>