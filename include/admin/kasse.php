<?php

defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Admins Area', 'Admins Area', 2);
$design->header();

if (isset($_POST['ksub']) AND ! empty($_POST['kontodaten'])) {
    $kontodaten = escape($_POST['kontodaten'], 'textarea');
    db_query("UPDATE prefix_allg SET t1 = '" . $kontodaten . "' WHERE k = 'kasse_kontodaten'");
} elseif (isset($_POST['sub'])) {
    $name = escape($_POST['name'], 'string');
    $verwendung = escape($_POST['verwendung'], 'string');
    $betrag = str_replace(',', '.', $_POST['betrag']);
    $datum = get_datum($_POST['datum']);
    if (!is_numeric($betrag)) {
	echo '<div class="alert alert-danger" role="alert">der Betrag is keine Nummer?.. !!</div>';
    } elseif (is_numeric($menu->get(1))) {
	if (db_query("UPDATE `prefix_kasse` SET name = '$name', datum = '$datum', betrag = '$betrag', verwendung = '$verwendung' WHERE id = " . $menu->get(1)))
	    echo '<div class="alert alert-warning" role="alert"><span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> Buchung wurde ge&auml;ndert</div>';
	else
	    echo '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span> Es ist ein Fehler aufgetreten, Buchung nicht ge&auml;ndert</div>';
	$menu->set_url(1, '');
    } else {
	db_query("INSERT INTO prefix_kasse (datum,name,verwendung,betrag) VALUES ('" . $datum . "','" . $name . "','" . $verwendung . "'," . $betrag . ")");
	echo '<div class="alert alert-success" role="alert"><span class="glyphicon glyphicon-check" aria-hidden="true"></span>  Buchung wurde gespeichert</div>';
    }
}

$kontodaten = db_result(db_query("SELECT t1 FROM prefix_allg WHERE k = 'kasse_kontodaten'"), 0);
$kontodaten = unescape($kontodaten);

if (is_numeric($menu->get(1))) {
    $r = db_fetch_assoc(db_query("SELECT name,betrag,verwendung,DATE_FORMAT(datum,'%d.%m.%Y') as datum FROM `prefix_kasse` WHERE id = " . $menu->get(1)));
    $r['id'] = '-' . $menu->get(1);
} else {
    $r = array('id' => '', 'name' => '', 'betrag' => '', 'datum' => date('d.m.Y'), 'verwendung' => '');
}
$tpl = new tpl('kasse', 1);
$r['kontodaten'] = $kontodaten;
$tpl->set_ar_out($r, 0);

$design->footer();
?>
