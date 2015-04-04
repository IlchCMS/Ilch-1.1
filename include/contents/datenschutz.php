<?php

// Copyright by: Manuel
// Support: www.ilch.de
// Datenschutzerklaerung by Maretz.eu

defined('main') or die('no direct access');
$title = $allgAr['title'] . ' :: Datenschutzerkl&auml;rung';
$hmenu = 'Datenschutzerkl&auml;rung';
$design = new design($title, $hmenu);
$design->header();
$tpl = new tpl('datenschutz.htm');
$tpl->out(0);
$abf = 'SELECT * FROM prefix_datenschutzerklaerung ORDER BY pos ASC';
$erg = db_query($abf);
while ($row = db_fetch_assoc($erg)) {
    if ($row['einaus'] == '1') {
        $row['AUSGABEDATENSCHUTZ'] = '<h3>&raquo;&nbsp;' . $row['titel'] . '</h3><hr>' . $row['txt'] . '<br>';
    } else {
        $row['AUSGABEDATENSCHUTZ'] = '';
    }
    $tpl->set_ar_out($row, 1);
}
$row['ANZ'] = '<b>Quellen:</b>&nbsp;';
$tpl->set_ar_out($row, 2);
$ers = db_query($abf);
while ($ros = db_fetch_assoc($ers)) {
    if ($ros['urltitle'] == '') {
        $modtpl = '';
    } else {
        $modtpl = '<a href="' . $ros['url'] . '" target="_blank" title="' . $ros['urltitle'] . '">' . $ros['urltitle'] . '</a> , ';
    }
    if ($ros['einaus'] == '1') {
        $ros['QUELLE'] = $modtpl;
    } else {
        $ros['QUELLE'] = '';
    }
    $tpl->set_ar_out($ros, 3);
}
$tpl->out(4);
$design->footer();
?> 