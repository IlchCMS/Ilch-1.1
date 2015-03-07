<?php

#   Copyright by: Manuel Staechele
#   Support: www.ilch.de

defined('main') or die('no direct access');

$title = $allgAr['title'] . ' :: Kontakt';
$hmenu = 'Kontakt';
$design = new design($title, $hmenu);
$design->header();

$erg = db_query("SELECT v2,t1,v1 FROM prefix_allg WHERE k = 'kontakt'");
$row = db_fetch_assoc($erg);
$k = explode('#', $row['t1']);

$name = '';
$mail = '';
$subject = '';
$wer = '';
$text = '';
$fehler = '';

## config
$mailadr = true; // check  check valide email --> true = Yes, false = No
## check valide email

function check_valide_mail($email) {
    $regex = '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+' .
	    '(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|' .
	    '(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|' .
	    '([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))' .
	    '\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|' .
	    '(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|' .
	    '([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))' .
	    '\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|' .
	    '((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/';
    if (preg_match($regex, $email)) {
	if (function_exists('checkdnsrr')) {
	    list (, $domain) = explode('@', $email);
	    if (checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A')) {
		return true;
	    }
	}
    }
    return false;
}

## absenden
if (isset($_POST['submit'])) {
    if (empty($_POST['wer'])) {
	$fehler .= 'Es wurde kein Empfänger ausgewählt!<br />';
    }
    if (empty($_POST['name'])) {
	$fehler .= 'Es wurde kein Name angegeben!<br />';
    }
    if ($_POST['mail'] != '') {
	if ($mailadr) {
	    $fehler .= (check_valide_mail($_POST['mail']) == true ? '' : 'Es wurde keine gültige E-Mail Adresse angeben!<br />');
	}
    } else {
	$fehler .= 'Es wurde keine E-Mail-Adresse angegeben!<br />';
    }
    if (empty($_POST['subject'])) {
	$fehler .= 'Es wurde kein Betreff eingegeben!<br />';
    }
    if (empty($_POST['txt'])) {
	$fehler .= 'Es wurde kein Nachrichtentext eingegeben!<br />';
    }
    if (chk_antispam('contact') != true) {
	$fehler .= 'Der AntiSpam Code wurde war nicht korrekt!<br />';
    }
    if ($fehler == '' AND ! empty($_POST['wer'])) {
	$name = escape($_POST['name'], 'string');
	$mail = escape_for_email($_POST['mail']);
	$betreff = escape_for_email($_POST['subject'], true);
	$wer = escape($_POST['wer'], 'string');
	$text = strip_tags($_POST['txt']);
	$wero = FALSE;
	foreach ($k as $a) {
	    $e = explode('|', $a);
	    if (md5($e[0]) == $wer) {
		$wero = TRUE;
		$wer = $e[0];
		break;
	    }
	}
	if (strpos($text, 'Content-Type:') === FALSE
		AND strpos($text, 'MIME-Version:') === FALSE
		AND strpos($mail, '@') !== FALSE AND $wero === TRUE
		AND strlen($name) <= 50 AND strlen($mail) <= 50
		AND strlen($text) <= 5000
		AND $mail != $name AND $name != $text AND $text != $mail) {
	    $subject = "Kontakt über " . $_SERVER['HTTP_HOST'];
	    $inhalt = $name . " hat Ihnen eine Nachricht über " . $_SERVER['HTTP_HOST'] . " gesendet. \n\n";
	    $inhalt .= "Betreff: " . $betreff . " \n\n";
	    $text .= " \n\nViele Grüße von \n";
	    $text .= $name . " (" . $mail . ") ";
	    $text = $inhalt . $text;
	    icmail($wer, $subject, $text, $name . " <" . $mail . ">");
	    echo '<div style="border:1px green dotted;text-align:center;"><strong>Ihre Anfrage per Email wurde erfolgreich versendet!</strong></div>';
	    $name = '';
	    $mail = '';
	    $subject = '';
	    $wer = '';
	    $text = '';
	} else {
	    $name = escape($_POST['name'], 'string');
	    $mail = escape($_POST['mail'], 'string');
	    $subject = escape($_POST['subject'], 'string');
	    $wer = escape($_POST['wer'], 'string');
	    $text = escape($_POST['txt'], 'string');
	    echo '<div style="border:1px red dotted;text-align:center;"><strong>' . $lang['emailcouldnotsend'] . '</strong></div>';
	}
    } else {
	$name = escape($_POST['name'], 'string');
	$mail = escape($_POST['mail'], 'string');
	$subject = escape($_POST['subject'], 'string');
	$wer = escape($_POST['wer'], 'string');
	$text = escape($_POST['txt'], 'string');
	echo '<div style="border:1px red dotted;text-align:center;"><strong>Aufgrund folgender Fehler, wurde die Email nicht versendet:</strong><br />' . $fehler . '</div>';
    }
}

$tpl = new tpl('contact.htm');
$tpl->out(0);

foreach ($k as $a) {
    $e = explode('|', $a);
    if ($e[0] == '' OR $e[1] == '') {
	continue;
    }
    if (md5($e[0]) == $wer) {
	$c = 'checked';
    } else {
	$c = '';
    }
    $tpl->set_ar_out(array('KEY' => md5($e[0]), 'VAL' => $e[1], 'c' => $c), 1);
}

$tpl->set('name', $name);
$tpl->set('mail', $mail);
$tpl->set('subject', $subject);
$tpl->set('text', $text);
$tpl->set('ANTISPAM', get_antispam('contact', 1));
$tpl->out(2);

$design->footer();
?>