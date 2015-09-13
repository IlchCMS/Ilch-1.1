<?php

// Dateschutzerklaerung
defined('main') or die('no direct access');
defined('admin') or die('only admin access');
$design = new design('Admins Area', 'Admins Area', 2);
$design->header();
$tpl = new tpl('datenschutz', 1);
// loeschen
if ($menu->getA(2) == 'd') {
    $pos = db_result(db_query("SELECT pos FROM prefix_datenschutzerklaerung WHERE id = " . $menu->getE(2)), 0);
    db_query("DELETE FROM prefix_datenschutzerklaerung WHERE id = " . $menu->getE(2));
    db_query("UPDATE prefix_datenschutzerklaerung SET pos = pos -1 WHERE pos > " . $pos);
}
// aendern / eintragen
if (isset($_POST['sub'])) {
    $_POST['titel'] = escape($_POST['titel'], 'string');
    $_POST['url'] = escape($_POST['url'], 'string');
    $_POST['urltitle'] = escape($_POST['urltitle'], 'string');
    $_POST['txt'] = escape($_POST['txt'], 'text');
    $_POST['einaus'] = escape($_POST['einaus'], 'string');
    $_POST['pos'] = escape($_POST['pos'], 'string');
    if (empty($_POST['id'])) {
        $_POST['pos'] = db_result(db_query("SELECT COUNT(*) FROM prefix_datenschutzerklaerung"), 0);
        db_query("INSERT INTO prefix_datenschutzerklaerung (titel,url,urltitle,txt,einaus,pos) VALUES ('" . $_POST['titel'] . "','" . $_POST['url'] . "','" . $_POST['urltitle'] . "','" . $_POST['txt'] . "','" . $_POST['einaus'] . "','" . $_POST['pos'] . "')");
        echo '<div id="meldung" style="visibility:hidden;width:300px;position:fixed;left:50%;top:8%;margin-left:-150px;z-index:1001;"><div class="alert alert-success text-center" role="alert"><strong>Der neue Abschnitt wurde eingestellt.</strong></div></div>';
    } else {
        db_query("UPDATE prefix_datenschutzerklaerung SET `titel` = '" . $_POST['titel'] . "',`url` = '" . $_POST['url'] . "',`urltitle` = '" . $_POST['urltitle'] . "',`txt` = '" . $_POST['txt'] . "',`einaus` = '" . $_POST['einaus'] . "'  WHERE `id` = '" . $_POST['id'] . "'");
        echo '<div id="meldung" style="visibility:hidden;width:300px;position:fixed;left:50%;top:8%;margin-left:-150px;z-index:1001;"><div class="alert alert-success text-center" role="alert"><strong>Der Abschnitt wurde ge&auml;ndert.</strong></div></div>';
    }
}
// verschieben
if ($menu->getA(2) == 'o' OR $menu->getA(2) == 'u') {
    $pos = $menu->get(3);
    $id = $menu->getE(2);
    $nps = ($menu->getA(2) == 'u' ? $pos + 1 : $pos - 1);
    $anz = db_result(db_query("SELECT COUNT(*) FROM prefix_datenschutzerklaerung"), 0);
    if ($nps < 0) {
        db_query("UPDATE prefix_datenschutzerklaerung SET pos = " . $anz . " WHERE id = " . $id);
        db_query("UPDATE prefix_datenschutzerklaerung SET pos = pos -1");
    }
    if ($nps >= $anz) {
        db_query("UPDATE prefix_datenschutzerklaerung SET pos = -1 WHERE id = " . $id);
        db_query("UPDATE prefix_datenschutzerklaerung SET pos = pos +1");
    }
    if ($nps < $anz AND $nps >= 0) {
        db_query("UPDATE prefix_datenschutzerklaerung SET pos = " . $pos . " WHERE pos = " . $nps);
        db_query("UPDATE prefix_datenschutzerklaerung SET pos = " . $nps . " WHERE id = " . $id);
    }
}
// aendern vorbereiten.
if ($menu->getA(2) == 'e') {
    $erg = db_query("SELECT id,titel,url,urltitle,txt,einaus FROM prefix_datenschutzerklaerung WHERE id = '" . $menu->getE(2) . "'");
    $rs = db_fetch_assoc($erg);
    $rs['id'] = $menu->getE(2);
    $rs['titeltext'] = 'Abschnitt bearbeiten';
    $rs['subbutton'] = '&Auml;nderungen speichern';
    if (strtolower($rs['einaus']) == "1") {
        $rs['ein'] = 'checked';
        $rs['aus'] = '';
    } else if (strtolower($rs['einaus']) == "0") {
        $rs['ein'] = '';
        $rs['aus'] = 'checked';
    }
} else {
    $rs = array('titeltext' => 'Neuen Abschnitt anlegen', 'subbutton' => 'Neuen Abschnitt eintragen', 'id' => '', 'titel' => '', 'url' => '', 'urltitle' => '', 'txt' => '', 'einaus' => '',);
}
$tpl->set_ar_out($rs, 0);
$page = ($menu->getA(2) == 'p' ? $menu->getE(2) : 1);
$limit = 20;
$class = 'Cnorm';
$MPL = db_make_sites($page, '', $limit, '?datenschutz-datenschutz', 'datenschutzerklaerung');
$anfang = ($page - 1) * $limit;
$abf = "SELECT id,titel,url,urltitle,pos,txt,einaus FROM prefix_datenschutzerklaerung ORDER BY pos ASC LIMIT " . $anfang . "," . $limit;
$erg = db_query($abf) or die('<span style="color:#ff0000">Das Modul Datenschutzerklaerung wurde nicht gefunden.<br>Bitte die Installation laut Anleitung umsetzen!</span>');
while ($row = db_fetch_assoc($erg)) {
    $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
    $row['class'] = $class;
    if ($row['status'] = ($row['einaus'] > '0')) {
        $row['status'] = '<td class="text-center"><span class="label label-success" rel="tooltip" title="Abschnitt wird angezeigt">on</span></td>';
    } else {
        $row['status'] = '<td class="text-center"><span class="label label-danger" rel="tooltip" title="Abschnitt wird nicht angezeigt">off</span></td>';
    }
    $tpl->set_ar($row);
    $tpl->out(1);
}
$tpl->set('MPL', $MPL);
$tpl->out(2);
$design->footer();
?> 