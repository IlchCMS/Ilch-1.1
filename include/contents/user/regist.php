<?php

#   Copyright by: Manuel
#   Support: www.ilch.de


defined('main') or die('no direct access');

if (!isset($_GET['step'])) {
    $_GET['step'] = 1;
}

if ($allgAr['forum_regist'] == 0) {
    # user duerfen sich nicht registrieren.
    $title = $allgAr['title'] . ' :: Users :: Keine registrierung m&ouml;glich';
    $hmenu = $extented_forum_menu . '<a class="smalfont" href="?user">User</a><b> &raquo; </b>Keine Registrierung m&ouml;glich' . $extented_forum_menu_sufix;
    $design = new design($title, $hmenu, 1);
    $design->header();
    $tpl = new tpl('user/login');
    echo '<div class="text-center"><span class="ilch_hinweis_rot">Der Administrator hat festgelegt das man sich nicht registrieren kann </span></div>';
    $tpl->set_out('WDLINK', '?' . $allgAr['smodul'], 0);
    $design->footer();
    exit();
}

switch ($menu->get(2)) {
    default :
        $title = $allgAr['title'] . ' :: Users :: Registrieren :: Step 1 von 3';
        $hmenu = $extented_forum_menu . '<a class="smalfont" href="?user">User</a><b> &raquo; </b><a class="smalfont" href="?user-regist">Registrieren</a><b> &raquo; </b>Step 1 von 3' . $extented_forum_menu_sufix;
        $design = new design($title, $hmenu, 1);
        $design->header();
        $tpl = new tpl('user/regist');
        $tpl->set_out('regeln', bbcode($allgAr['allg_regeln']), 0);
        $design->footer();
        break;

    case 2 :
        $name = '';
        $email = '';
        if (!empty($_POST['nutz'])) {
            $name = escape($_POST['nutz'], 'string');
        }
        if (!empty($_POST['email'])) {
            $email = escape($_POST['email'], 'string');
        }

        $ch_name = false;
        $xname = escape_nickname($name);
        if (!empty($name) AND $xname == $name AND 0 == db_result(db_query("SELECT COUNT(*) FROM prefix_user WHERE name = BINARY '" . $name . "'"), 0)) {
            $ch_name = true;
        }

        $chkAntispam = chk_antispam('regist');
        if (empty($name) OR empty($email) OR $name != $xname OR $ch_name == false OR !$chkAntispam) {
            $title = $allgAr['title'] . ' :: Users :: Registrieren :: Step 2 von 3';
            $hmenu = $extented_forum_menu . '<a class="smalfont" href="?user">User</a><b> &raquo; </b><a class="smalfont" href="?user-regist">Registrieren</a><b> &raquo; </b>Step 2 von 3' . $extented_forum_menu_sufix;
            $design = new design($title, $hmenu, 1);
            $design->header();
            if (isset($_POST['register'])) {
                if (empty($name) OR empty($email)) {
                    $fehler = '<div class="text-center"><span class="ilch_hinweis_rot">'.$lang['entereddataincomplete'].'</span></div>';
                } elseif ($name != $xname) {
                    $fehler = '<div class="text-center"><span class="ilch_hinweis_rot">'.$lang['wrongnickname'].'</span></div>';
                } elseif ($ch_name == false) {
                    $fehler = '<div class="text-center"><span class="ilch_hinweis_rot">'.$lang['namealreadyinuse'].'</span></div>';
                } elseif (!$chkAntispam) {
                    $fehler = '<div class="text-center"><span class="ilch_hinweis_rot">'.$lang['wrongcaptchaentered'].'</span></div>'; //antispamtxt
                }
            } else {
                $fehler = '';
            }
            $tpl = new tpl('user/regist');
            $tpl->set('name', $name);
            $tpl->set('email', $email);
            $tpl->set('antispam', get_antispam('regist', 0));
            $tpl->set_out('FEHLER', $fehler, 1);
            if ($allgAr['forum_regist_user_pass'] == 1) {
                $tpl->out(2);
            }
            $tpl->out(3);
        } else {
            $pass = genkey(8);
            if (!empty($_POST['pass'])) {
                $pass = escape($_POST['pass'], 'string');
            }
            user_regist($name, $email, $pass);

            $tpl = new tpl('user/regist');
            $title = $allgAr['title'] . ' :: Users :: Registrieren :: Step 3 von 3';
            $hmenu = $extented_forum_menu . '<a class="smalfont" href="?user">User</a><b> &raquo; </b><a class="smalfont" href="?user-regist">Registrieren</a><b> &raquo; </b>Step 3 von 3' . $extented_forum_menu_sufix;
            $design = new design($title, $hmenu, 1);
            $design->header();
            $tpl->set_out('NAME', $name, 4);
        }
        $design->footer();
        break;
}
?>