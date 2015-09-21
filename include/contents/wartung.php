<?php

#   Copyright by: Saarlonz
#   Support: www.ilch.de

defined('main') or die('no direct access');

$title = ':: Wartungsmodus ::';

if (!is_admin()) {

    $tpl = new tpl('wartung');

    $ar = array(
        'title' => $allgAr['title'],
        'progress' => $allgAr['wartungs_progress'],
        'email' => $allgAr['adminMail'],
        'grund' => bbcode($allgAr['wartungs_information'])
    );

    $tpl->set_ar_out($ar, 0);
} else {

    if ($allgAr['wartungs_alert'] == '1') {
        echo '<div style="position:absolute; left: 0px; top: 0px; width:100%; display: block; background-color: #a94442;"><br><b>Diese Seite befindet sich im Wartungsmodus</b><br><a href="admin.php?allg">Wartungsmodus beenden</a></div>';
        exit();
    }
}
?>