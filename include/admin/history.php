<?php

#   Copyright by: Manuel
#   Support: www.ilch.de

defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Admins Area', 'Admins Area', 2);
$design->header();
$tpl = new tpl('history', 1);

# delete
if ($menu->getA(1) == 'd' AND is_numeric($menu->getE(1))) {
    $IdToDelete = escape($menu->getE(1), 'integer');
    db_query("DELETE FROM prefix_history WHERE id = '" . $IdToDelete . "'");
    wd('?history', 'Erfolgreich gel&ouml;scht', 3);
}
if (isset($_POST['pkey'])) {
    $IdToEdit = escape($_POST['pkey'], 'integer');
}
if (!empty($_POST['sub'])) {
    list ( $d, $m, $y ) = explode('.', $_POST['date']);
    if (@checkdate($m, $d, $y)) {
	$date = $y . '-' . $m . '-' . $d;
	$date = escape($date, 'string');
	$txt = escape($_POST['txt'], 'textarea');
	$title = escape($_POST['title'], 'string');
	if (empty($_POST['pkey'])) {
	    db_query("INSERT INTO prefix_history (date,title,txt) VALUES ('" . $date . "','" . $title . "','" . $txt . "')");
	} else {
	    db_query("UPDATE prefix_history SET date = '" . $date . "',title = '" . $title . "',txt = '" . $txt . "' WHERE id = '" . $IdToEdit . "'");
	}
    } else {
	echo '<div class="alert alert-danger" role="alert"><span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span> Datum stimmt nicht, bitte im Format DD.MM.YYYY eingeben also z.B. 29.12.2005</div>';
    }
}
if (!isset($IdToDelete)) {
    if ($menu->getA(1) == 'e' AND is_numeric($menu->getE(1)) AND empty($IdToEdit)) {
	$IdToEdit = escape($menu->getE(1), 'integer');
	$erg = db_query("SELECT id,DATE_FORMAT(date,'%d.%m.%Y') as date,title,txt FROM prefix_history WHERE id = '" . $IdToEdit . "'");
	$_ilch = db_fetch_assoc($erg);
	$_ilch['pkey'] = $IdToEdit;
    } else {
	$_ilch = array('pkey' => '', 'date' => date('d.m.Y'), 'title' => '', 'txt' => '');
    }
    $tpl->set_ar_out($_ilch, 0);
    $limit = 20;
    $page = ($menu->getA(1) == 'p' ? escape($menu->getE(1), 'integer') : 1);
    $MPL = db_make_sites($page, 'ORDER BY `date` DESC', $limit, '?history', 'history');
    $anfang = ($page - 1) * $limit;
    $abf = "SELECT `id`,`date`,`title` FROM prefix_history ORDER BY `date` DESC LIMIT " . $anfang . "," . $limit;
    $erg = db_query($abf);
    while ($row = db_fetch_assoc($erg)) {
	$class = ($class == 'active' ? '' : 'active' );
	$row['class'] = $class;
	list ( $y, $m, $d ) = explode('-', $row['date']);
	$row['date'] = $d . '.' . $m . '.' . $y;
	$tpl->set_ar_out($row, 1);
    }
    $tpl->set_out('MPL', $MPL, 2);
}
$design->footer();
?>
