<?php

#   Copyright by: Manuel
#   Support: www.ilch.de


defined('main') or die('no direct access');


$title = $allgAr['title'] . ' :: Users :: Password Reminder';
$hmenu = $extented_forum_menu . '<a class="smalfont" href="?user">Users</a><b> &raquo; </b> Password Reminder' . $extented_forum_menu_sufix;

if ($menu->get(2) != 'admin') {
    $design = new design($title, $hmenu, 1);
    $design->header();
}

$show = true;

if (isset($_POST['name'])) {
    $name = escape($_POST['name'], 'string');
    $erg = db_query("SELECT email FROM prefix_user WHERE name = BINARY '" . $name . "'");
    if (db_num_rows($erg) == 1) {
        $row = db_fetch_assoc($erg);

        $new_pass = genkey(8);
        $passwordHash = user_pw_crypt($new_pass);
        $id = md5(uniqid(rand()));

        db_query("INSERT INTO prefix_usercheck (`check`,name,email,pass,datime,ak)
		VALUES ('" . $id . "','" . $name . "','" . $row['email'] . "','" . $passwordHash . "',NOW(),2)");

        $page = $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"];

        $confirmlinktext = "\n" . $lang['registconfirm'] . "\n\n" . sprintf($lang['registconfirmlink'], $page, $id);
        $regmail = sprintf($lang['newpasswordmail'], $name, $confirmlinktext, $new_pass);

        icmail($row['email'], 'Password Reminder', $regmail); # email an user
        echo '<div class="text-center"><span class="ilch_hinweis_gruen">' . $lang['youhavereceivedaemail'] . '</span></div>';
    } else {
        echo '<div class="text-center"><span class="ilch_hinweis_rot">' . $lang['namenotfound'] . '</span></div>';
    }
}

if ($show) {
    if ($menu->get(2) == 'admin') {
        $tpl = new tpl('user/new_adminpass');
    } else {
        $tpl = new tpl('user/new_pass');
    }
    $tpl->out(0);
}

if ($menu->get(2) != 'admin') {
    $design->footer();
}
?>