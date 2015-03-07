<?php

// Copyright by: Manuel Staechele
// Support: www.ilch.de
// Modded by Mairu f�r News Extended
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

if (!isset($_SESSION['allowFCKUpload'])) {
    $_SESSION['allowFCKUpload'] = true;
}

// -----------------------------------------------------------|
// #
// ##
// ###
// #### F u n k t i o n e n
function dz_timestamp($d, $t = '00:00') {
    $d = trim($d);
    $t = trim($t);
    if (preg_match('/^\d{1,2}.\d{1,2}.(\d{2}|\d{4})$/', $d) == false OR preg_match('/^\d{1,2}:\d\d$/', $t) == false) {
	return false;
    }
    $d = explode('.', $d);
    $t = explode(':', $t);
    if ($o = mktime($t[0], $t[1], 0, $d[1], $d[0], $d[2])) {
	return $o;
    } else {
	return false;
    }
}

function getKats($akt, $addkat = '', $self = false) {
    $katAr = array();
    if ($self) {
	$katAr['#0#'] = 'selbst w&auml;hlen';
    }
    $katAr['Allgemein'] = 'Allgemein';

    $kats = '';
    $erg = db_query("SELECT DISTINCT news_kat FROM `prefix_news`");
    while ($row = db_fetch_object($erg)) {
	$katAr[$row->news_kat] = $row->news_kat;
    }
    if (!empty($addkat) AND $addkat != '#0#') {
	$katAr[$addkat] = $addkat;
    }
    $katAr = array_unique($katAr);

    foreach ($katAr as $k => $a) {
	if (trim($k) == trim($akt)) {
	    $sel = 'selected="selected"';
	} else {
	    $sel = '';
	}
	$kats .= '<option value="' . $k . '" ' . $sel . '>' . $a . '</option>';
    }
    return ($kats);
}

function vorschau($form) {
    global $info;
    $resp = new xajaxResponse();
    $txt = bbcode($form['txt']);
    $resp->assign('vorschau_td', 'innerHTML', $txt);
    $resp->script("document.getElementById('vorschau').style.display = 'block';");
    if (isset($info['ImgMaxBreite'])) {
	$resp->script("ResizeBBCodeImages()");
    }
    return $resp;
}

function vorschau_id($id) {
    global $info;
    $resp = new xajaxResponse();
    $txt = db_result($q = db_query("SELECT news_text, html FROM prefix_news WHERE news_id = '$id'"), 0, 0);
    if (db_result($q, 0, 1) == 0) {
	$txt = bbcode($txt);
    }
    $resp->assign('vorschau_td', 'innerHTML', $txt);
    $resp->script("document.getElementById('vorschau').style.display = 'block';");
    if (isset($info['ImgMaxBreite'])) {
	$resp->script("ResizeBBCodeImages()");
    }
    return $resp;
}

function tn_koms() {
    $resp = new xajaxResponse();
    $now = db_result(db_query('SELECT v2 FROM prefix_allg WHERE k = "news"'), 0);
    db_query('UPDATE prefix_allg SET v2 = IF(v2=1,0,1) WHERE k = "news"');
    $linktxt = $now == '0' ? 'ja' : 'nein';
    $resp->assign('tn_koms', 'innerHTML', $linktxt);
    return $resp;
}

function saveopts($newsempf, $kat) {
    $resp = new xajaxResponse();
    if (!db_query("UPDATE prefix_allg SET v3 = '$newsempf', v4 = '$kat' WHERE k = 'News'")) {
	$resp->alert("Fehler aufgetreten:\n" . mysql_error());
    }
    return $resp;
}

function setArchiv($id, $old) {
    $resp = new xajaxResponse();
    $new = $old == 'A' ? 0 : 1;
    if (db_query("UPDATE prefix_news SET archiv = $new WHERE news_id = '$id'")) {
	$resp->assign('archiv_link_' . $id, 'innerHTML', $old == 'A' ? 'N' : 'A');
    } else {
	$resp->alert("Fehler:\n" . mysql_error());
    }
    return $resp;
}

// xajax f�r vorschau
$xajax = new xajax();
$xajax->configureMany(array('decodeUTF8Input' => true, 'characterEncoding' => 'ISO-8859-1', 'requestURI' => 'admin.php?news-ajax'));

$xajax->register(XAJAX_FUNCTION, 'vorschau');
$xajax->register(XAJAX_FUNCTION, 'vorschau_id');
$xajax->register(XAJAX_FUNCTION, 'tn_koms');
$xajax->register(XAJAX_FUNCTION, 'saveopts');
$xajax->register(XAJAX_FUNCTION, 'setArchiv');
$xajax->processRequest();
// #### F u n k t i o n
// ###
// ###
// #### A k t i o n e n
$design = new design('Admins Area', 'Admins Area', 2);
$design->header();

if (!empty($_REQUEST['um'])) {
    $um = $_REQUEST['um'];
    $newscreatetime = time();
    $newschangesqladd = '';
    $archiv = 0;
    // Sperre
    // Escape
    $gesperrt = escape($_POST['gesperrt'], 'string');
    $datum = escape($_POST['datum'], 'string');
    $zeit = escape($_POST['zeit'], 'string');
    $set_time = escape($_POST['set_time'], 'string');
    $close = escape($_POST['close'], 'integer');
    $cdatum = escape($_POST['cdatum'], 'string');
    $czeit = escape($_POST['czeit'], 'string');
    $text = escape($_POST['txt'], 'string');
    $html = escape($_POST['html'], 'string');
    $katLis = escape($_POST['katLis'], 'string');
    $kat = escape($_POST['kat'], 'string');
    $titel = escape($_POST['titel'], 'string');
    $newsID = escape($_POST['newsID'], 'integer');

    if ($gesperrt != 'on') {
	$show = dz_timestamp($datum, $zeit);
	if (!$show) {
	    $show = 1;
	} elseif (isset($zeit)) {
	    $newscreatetime = $show;
	    $newschangesqladd .= ',news_time = FROM_UNIXTIME(' . $show . '), editor_id  = NULL, edit_time  = NULL';
	    debug('TEST: ' . $newscreatetimech);
	}
    } else {
	$show = 0;
    }
    // Enddatum
    if ($close == '0') {
	$endtime = 'NULL';
    } elseif ($close == '1') {
	$endtime = dz_timestamp($cdatum, $czeit);
    } else {
	$endtime = dz_timestamp($cdatum, $czeit);
	$archiv = 2;
    }

    //Grundrechte + Gruppen
    if ($um == 'insert' or $um == 'change') {
	$grecht = 0;
	for ($i = 0; $i < 10; $i++) {
	    if (escape($_POST['grecht_' . $i], 'string') != Null) {
		$grecht = $grecht | pow(2, $i);
	    }
	}

	$groups = 0;
	$sql = db_query("SELECT id FROM prefix_groups");
	while ($r = db_fetch_assoc($sql)) {
	    if (escape($_POST['groups_' . $r['id']], 'string') != Null) {
		$groups = $groups | pow(2, $r['id']);
	    }
	}
    }
    if ($um == 'insert') {
	if ($katLis == 'neu') {
	    $katLis = $kat;
	}

	db_query("INSERT INTO `prefix_news` (news_title,user_id,news_time,news_recht,news_groups,news_kat,news_text,html,`show`,archiv,endtime)
		VALUES ('" . $titel . "'," . $_SESSION['authid'] . ",FROM_UNIXTIME(" . $newscreatetime . ")," . $grecht . "," . $groups . ",'" . $katLis . "','" . $text . "','" . $html . "',$show,$archiv,$endtime)");
	// insert
    } elseif ($um == 'change') {

	if ($katLis == 'neu') {
	    $katLis = $kat;
	}
	db_query('UPDATE `prefix_news` SET
		news_title = "' . $titel . '",
		editor_id  = "' . $_SESSION['authid'] . '",
		edit_time  = NOW(),
		news_recht = "' . $grecht . '",
		news_groups = "' . $groups . '",
		news_kat   = "' . $katLis . '",
		html       = "' . $html . '",
		`show`     = ' . $show . ',
		archiv     = ' . $archiv . ',
		endtime     = ' . $endtime . ',
                news_text  = "' . $text . '"' . $newschangesqladd . ' WHERE news_id = "' . $newsID . '" LIMIT 1');
	$edit = $newsID;
    }
}
// edit
// del
if ($menu->get(1) == 'del') {
    db_query('DELETE FROM `prefix_news` WHERE news_id = "' . $menu->get(2) . '" LIMIT 1');
}
// del
// Sperren/Freischalten
if ($menu->getA(1) == 's') {
    db_query('UPDATE `prefix_news` SET `show` = IF(`show`>0,0,1) WHERE news_id = "' . $menu->getE(1) . '" LIMIT 1');
}
// Sperren/Freischalten
// Topnews
if ($menu->getA(1) == 't') {
    db_query('UPDATE `prefix_allg` SET `v1` = "' . $menu->getE(1) . '" WHERE k = "news" LIMIT 1');
}
// Topnews
// #### A k t i o n e n
// ###
// ##
// #
// #
// ##
// ###
// #### h t m l   E i n g a b e n
if (empty($doNoIn)) {
    $limit = 20; // Limit
    $page = ($menu->getA(1) == 'p' ? $menu->getE(1) : 1);
    $MPL = db_make_sites($page, '', $limit, "?news", 'news');
    $anfang = ($page - 1) * $limit;
    if ($menu->get(1) != 'edit') {
	$FnewsID = '';
	$Faktion = 'insert';
	$Fueber = '';
	$Fstext = '';
	$Ftxt = '';
	$Fgrecht = 1023;
	$Fgroups = 0;
	$FkatLis = '';
	$Fsub = 'Eintragen';
	$Fhtml = 'switch_html();';
	$sel0 = '';
	$sel1 = 'checked="checked"';
	$sel_show = 'checked="checked"';
	$datum = date('d.m.Y');
	$zeit = date('H:i');
	$csel0 = 'checked="checked"';
	$csel1 = '';
	$csel2 = '';
	$cdatum = date('d.m.Y', time() + 604800);
	$czeit = date('H:i');
    } else {
	$row = db_fetch_object(db_query("SELECT * FROM `prefix_news` WHERE news_id = " . $menu->get(2)));
	$FnewsID = $row->news_id;
	$Faktion = 'change';
	$Fueber = str_replace('"', '&quot;', $row->news_title);
	$Ftxt = stripslashes($row->news_text);
	$Fgrecht = $row->news_recht;
	$Fgroups = $row->news_groups;
	$FkatLis = $row->news_kat;
	$Fsub = '&Auml;ndern';
	$Fhtml = $row->html == 1 ? 'switch_html();' : '';
	if ($row->show == 0) {
	    $sel_gesperrt = 'checked="checked"';
	    $datum = '';
	    $zeit = '';
	} else {
	    $sel_gesperrt = '';
	    $row->show = $row->show < 10000 ? time() : $row->show;
	    $datum = date('d.m.Y', $row->show);
	    $zeit = date('H:i', $row->show);
	}
	$sel0 = $sel1 = '';
	if ($row->html) {
	    $sel1 = 'checked="checked"';
	} else {
	    $sel0 = 'checked="checked"';
	}
	$csel0 = $csel1 = $csel2 = '';
	if ($row->archiv == 1) {
	    $csel2 = 'checked="checked"';
	    $row->endtime = time() - 1000;
	} elseif (is_null($row->endtime)) {
	    $csel0 = 'checked="checked"';
	    $row->endtime = time() + 604800;
	} elseif ($row->archiv == 2) {
	    $csel2 = 'checked="checked"';
	} else {
	    $csel1 = 'checked="checked"';
	}
	$cdatum = date('d.m.Y', $row->endtime);
	$czeit = date('H:i', $row->endtime);
    }
    $tpl = new tpl('news', 1);

    $ar = array(
	'NEWSID' => $FnewsID,
	'AKTION' => $Faktion,
	'MPL' => $MPL,
	'UEBER' => $Fueber,
	'txt' => $Ftxt,
	'SMILIS' => getsmilies(),
	// 'grecht' => dbliste($Fgrecht,$tpl,'grecht',"SELECT id,name FROM prefix_grundrechte ORDER BY id DESC"),
	'KATS' => getKats($FkatLis),
	'FSUB' => $Fsub,
	'sel0' => $sel0,
	'sel1' => $sel1,
	'sel_gesperrt' => $sel_gesperrt,
	'datum' => $datum,
	'zeit' => $zeit,
	'csel0' => $csel0,
	'csel1' => $csel1,
	'csel2' => $csel2,
	'cdatum' => $cdatum,
	'czeit' => $czeit,
	'xajax' => $xajax->getJavascript()
    );
    // Grundrechte
    $ar['grecht'] = '';
    $qry = db_query('SELECT ABS(id) as id, name FROM prefix_grundrechte ORDER BY id');
    while ($r = db_fetch_assoc($qry)) {
	$ar['grecht'] .= '<span style="white-space: nowrap; margin-right: 5px;"><input type="checkbox" id="grecht_' . $r['id'] . '" name="grecht_' . $r['id'] . '" ' .
		(($Fgrecht == ($Fgrecht | pow(2, $r['id']))) ? 'checked="checked"' : '') . ' />' .
		'<label for="grecht_' . $r['id'] . '">' . $r['name'] . "</label></span>\n";
    }
    // Groups
    $ar['groups'] = '';
    $qry = db_query('SELECT id, name FROM prefix_groups ORDER BY id');
    while ($r = db_fetch_assoc($qry)) {
	$ar['groups'] .= '<span style="white-space: nowrap; margin-right: 5px;"><input type="checkbox" id="groups_' . $r['id'] . '" name="groups_' . $r['id'] . '" ' .
		(($Fgroups == ($Fgroups | pow(2, $r['id']))) ? 'checked="checked"' : '') . ' />' .
		'<label for="groups_' . $r['id'] . '">' . $r['name'] . "</label></span>\n";
    }

    $tpl->set_ar_out($ar, 0);
    if (isset($info['ImgMaxBreite'])) {
	$tpl->out(2); //BBCode 2.0 Modul
    } else {
	$tpl->out(1); //BBCode vom Ilchscript
    }
    $tpl->set_ar_out($ar, 3);
    // e d i t , d e l e t e
    $abf = 'SELECT * FROM `prefix_news` ORDER BY news_time DESC LIMIT ' . $anfang . ',' . $limit;

    $erg = db_query($abf);
    $class = '';
    $opts = db_fetch_object(db_query("SELECT v1 as topnews, v2 as koms,v3 as pmempf,v4 as kat FROM prefix_allg WHERE k = 'news'"));

    while ($row = db_fetch_object($erg)) {
	$class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');

	$tpl->set_ar_out(array('ID' => $row->news_id,
	    'class' => $class,
	    'TITEL' => $row->news_title,
	    'sperre' => $row->show >= 1 ? 'jep' : 'nop',
	    'sperren' => $row->show >= 1 ? 'Sperren' : 'Freischalten',
	    'title' => "Ersteller: " . get_n($row->user_id) . " ($row->news_time)" . (is_null($row->editor_id) ? '' : "\nGe&auml;ndert von: " . get_n($row->editor_id) . " ($row->edit_time)"),
	    'topnews' => $row->news_id == $opts->topnews ? 'ok' : 'leer',
	    'archiv' => (($row->archiv == 1) OR ( $row->archiv == 2 AND $row->endtime < time())) ? 'A' : 'N'
		), 4);
    }
    // e d i t , d e l e t e
    // Moegliche PM-Empf�nger
    $pmq = db_query("SELECT a.id, a.name FROM prefix_user a LEFT JOIN prefix_modulerights b ON b.mid = 2 AND b.uid = a.id WHERE a.recht <= -8 OR b.mid IS NOT NULL");
    $pmempf = '';
    $pmar = explode('#', $opts->pmempf);
    while ($r = db_fetch_object($pmq)) {
	$sel = in_array($r->id, $pmar) ? 'selected="selected"' : '';
	$pmempf .= "<option value=\"$r->id\" $sel>$r->name</option>";
    } // while
    $tpl->set_ar_out(array(
	'MPL' => $MPL,
	'html' => $Fhtml,
	'tn_koms' => $opts->koms == '1' ? 'ja' : 'nein',
	'nadd_kat' => getKats($opts->kat, $opts->kat, true),
	'pmempf' => $pmempf
	    ), 5);
}

$design->footer();
?>