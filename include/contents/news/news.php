<?php
// Copyright by: Manuel Staechele
// Support: www.ilch.de
// Modded by Mairu für News Extended
defined ('main') or die ('no direct access');

$title = $allgAr['title'] . ' :: News';
$hmenu = 'News';
$design = new design ($title , $hmenu);
$design->addheader('<link rel="alternate" type="application/atom+xml" title="News (Atom)" href="index.php?news-atom" />
<link rel="alternate" type="application/rss+xml" title="News (RSS)" href="index.php?news-rss" />');

function news_find_kat ($kat) {
    $katpfad = 'include/images/news/';
    $katjpg = $katpfad . $kat . '.jpg';
    $katgif = $katpfad . $kat . '.gif';
    $katpng = $katpfad . $kat . '.png';

    if (file_exists($katjpg)) {
        $pfadzumBild = $katjpg;
    } elseif (file_exists ($katgif)) {
        $pfadzumBild = $katgif;
    } elseif (file_exists ($katpng)) {
        $pfadzumBild = $katpng;
    }

    if (!empty($pfadzumBild)) {
        $kategorie = '<img style="" src="' . $pfadzumBild . '" alt="' . $kat . '">';
    } else {
        $kategorie = '<b>' . $kat . '</b><br /><br />';
    }

    return ($kategorie);
}
// Schaut ob ein Name so oder ähnlich in der Datenbank vorhanden ist
// gibt true zurück falls der Name noch nicht verwendet ist
function checkName($name) {
    if (db_count_query("SELECT COUNT(name) FROM prefix_user WHERE name LIKE '$name'")) {
        return false;
    } else {
        return true;
    }
}

function checkKomName($name) {
    $resp = new xajaxResponse();
    if (checkName($name) OR loggedin()) {
        $resp->script('document.forms["komform"].submit();');
    } else {
        $resp->assign('komname', 'value' , '');
        $resp->alert('Dieser Name ist bereits an einen User vergeben, benutze bitte einen anderen.');
        $resp->script("document.getElementById('komname').focus();");
    }
    return $resp;
}
// xajax für namencheck
$xajax = new xajax();
$xajax->configureMany(array('decodeUTF8Input' => true ,'characterEncoding' => 'ISO-8859-1', 'requestURI' => 'admin.php?news-ajax'));
$xajax->register(XAJAX_FUNCTION, 'checkKomName');
$xajax->processRequest();

if ($menu->get(1) == 'ajax') {
    exit();
}

if (!is_numeric($menu->get(1))) {
    if ($menu->get(1) == 'rss' || $menu->get(1) == 'atom') {
        // ob_clean();
        $feed_type = $menu->get(1);

        $abf = "SELECT MAX(news_time) AS last_update FROM prefix_news";
        $erg = db_query($abf);
        $row = db_fetch_assoc($erg);
        $last_update = str_replace(' ', 'T', $row['last_update']) . 'Z';

        $tn_id = intval(@db_result($news_opts = db_query("SELECT v1 FROM prefix_allg WHERE k = 'news' LIMIT 1"), 0));

        $abf = "SELECT
      a.news_title as title,
      a.news_id as id,";
        $abf .= ($feed_type == 'atom') ? 'a.news_time as datum,' : "DATE_FORMAT(a.news_time,'%a, %e %b %y %H:%i:%s') as datum,";
        $abf .=
        "a.news_kat as kate,
      a.news_text as text,
      b.name as username,
      a.html
    FROM prefix_news as a
    LEFT JOIN prefix_user as b ON a.user_id = b.id
    WHERE (a.news_recht | 1) = a.news_recht AND a.`show` > 0 AND a.`show` <= UNIX_TIMESTAMP() AND a.news_id != $tn_id AND a.`archiv` != 1 AND (a.endtime IS NULL OR a.endtime > UNIX_TIMESTAMP())
    ORDER BY news_time DESC LIMIT 15";
        $erg = db_query($abf);
        $tpl = new tpl('news_' . $menu->get(1) . '.htm');

        header('Content-type: application/' . $menu->get(1) . '+xml');

        $tpl->set_ar_out(array('FEEDTITLE' => $allgAr['title'],
                'UPDATED' => $last_update,
                'SITEURL' => 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF'])), 0);
        while ($row = db_fetch_assoc($erg)) {
            if ($feed_type == 'atom') {
                $Z = (date('Z') > 0 ? '+' : '') . date('H:i:s', date('Z') + 23 * 3600);
                $row['datum'] = str_replace(' ', 'T', $row['datum']) . $Z;
            }

            $a = explode('[PREVIEWENDE]', $row['text']);
            $tpl->set_ar_out(array('TITLE' => $row['title'],
                    'TXT' => $row['html'] ? $a[0] : bbcode($a[0]),
                    'LINK' => 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php?news-' . $row['id'],
                    'AUTHOR' => $row['username'],
                    'DATE' => $row['datum']
                    ), 1);
        }
        $tpl->out(2);
        exit;
    } else {
        $design->header();
        $limit = $allgAr['Nlimit'];
        $page = ($menu->getA(1) == 'p' ? $menu->getE(1) : 1);

        //Gruppenrechte
        $groups = getGroupRights();

        // Topnews ausgeben
        $tn_id = intval(@db_result($news_opts = db_query("SELECT v1, v2 FROM prefix_allg WHERE k = 'news' LIMIT 1"), 0, 0));
        $tn_koms = @db_result($news_opts, 0, 1);
        $tn_sql = db_query("SELECT a.news_title as title, a.news_text, a.news_kat, a.news_recht, a.news_groups, a.html,
                DATE_FORMAT(a.news_time,'%d. %m. %Y - %H:%i Uhr') as datum,
                DATE_FORMAT(a.news_time,'%W') as dayofweek,
                b.name as username,
                c.name as editorname,
                a.html,
                a.edit_time,
                a.klicks,
                b.id as uid
            FROM prefix_news a
            LEFT JOIN prefix_user as b ON a.user_id = b.id
            LEFT JOIN prefix_user as c ON a.editor_id = c.id
            WHERE a.news_id = $tn_id AND a.`show` > 0 AND a.`show` <= UNIX_TIMESTAMP() AND (a.endtime IS NULL OR a.endtime > UNIX_TIMESTAMP()) AND
            (((" . pow(2, abs($_SESSION['authright'])) . " | a.news_recht) = a.news_recht) OR
	        (a.news_groups != 0 AND ((a.news_groups ^ $groups) != (a.news_groups | $groups))))");
        if (db_num_rows($tn_sql) > 0) {
            $tn_r = db_fetch_assoc($tn_sql);

			$dontShow = false;
			if ((pow(2, abs($_SESSION['authright'])) | $tn_r['news_recht']) != $tn_r['news_recht']) {
				$dontShow = true;
			}
			if ($dontShow) {
				foreach($_SESSION['authgrp'] as $id => $bool) {
					if ($bool and (pow(2, abs($id)) | $tn_r['news_groups'] == $tn_r['news_groups'])) {
						$dontShow = false;
						break;
					}
				}
			}
        	if (!$dontShow) {
				$tn_tpl = new tpl ('news/top');
                if ($tn_koms) {
                    $tn_r['kom'] = db_count_query("SELECT COUNT(id) FROM prefix_koms WHERE uid = $tn_id AND cat = 'NEWS'");
                }
        	    $tn_r['showkom'] = $tn_koms;
                if (strpos ($tn_r['news_text'], '[PREVIEWENDE]') !== false) {
                    $a = explode('[PREVIEWENDE]' , $tn_r['news_text']);
                    $tn_r['news_text']= $a[0];
                    $tn_r['readwholenews'] = '<a href="index.php?news-' . $tn_id . '" alt="mehr lesen" title="mehr lesen"><img src="include/images/icons/news/more.gif" alt="mehr lesen" border="0"></a>';
                } else {
                     $tn_r['readwholenews'] = '';
                }
                $tn_r['id'] = $tn_id;
        	    $tn_r['klicks'] = '<img src="include/images/icons/news/counter.gif" alt="' . $tn_r['klicks']. ' mal gelesen" title="' . $tn_r['klicks']. ' mal gelesen" border="0">';
                $tn_r['datum']  = $lang[$tn_r['dayofweek']] . ' ' . $tn_r['datum'];
        	    $tn_r['edit']   = is_null($tn_r['edit_time']) ? '' : '<br /><i>zuletzt ge&auml;ndert am ' . date('d.m.Y - H:i', strtotime($tn_r['edit_time'])) . '&nbsp;Uhr';
        	    if (!empty($tn_r['edit']) and $tn_r['editorname'] != $tn_r['username']) {
        	        $tn_r['edit'].= ' von ' . $tn_r['editorname']. '</i>';
        	    } elseif (!empty($tn_r['edit'])) {
        	        $tn_r['edit'].= '</i>';
        	    }

                $tn_r['kate'] = news_find_kat($tn_r['news_kat']);
        	    $tn_r['text'] = $tn_r['html']? $tn_r['news_text']: bbcode($tn_r['news_text']);
                $tn_tpl->set_ar_out($tn_r, 0);
				unset($tn_tpl);
        	}
        }

		//Kategorie einschränken
		if ($menu->get(1) != '' and ($menu->getA(1) != 'p' or $menu->getE(1) == 0)) {
			$kats = $katssql = array();  #collect given kats
			$i = 1;
			while($kat = escape($menu->get($i), 'string')){
				$kats[] = $kat;
				$katssql[] = '"' . $kat . '"';
				$i++;
			}
			$news_kat = 'news_kat IN ('.implode(',', $katssql).') AND';
			$katmpl = '-'.implode('-', $kats);
			$page = $menu->getE('p');
			if ($page < 1) {
				$page = 1;
			}
			$katmpl = str_replace('-p'.$page, '', $katmpl);
		} else {
			$news_kat = $katmpl = '';
			$page = ($menu->getA(1) == 'p' ? $menu->getE(1) : 1);
		}

        $anfang = ($page - 1) * $limit;

		$MPL = db_make_sites ($page , "WHERE (((" . pow(2, abs($_SESSION['authright'])) . " | news_recht) = news_recht) OR
			(news_groups != 0 AND ((news_groups ^ $groups) != (news_groups | $groups)))) AND $news_kat `show` > 0 AND `show` <= UNIX_TIMESTAMP() AND news_id != $tn_id AND archiv != 1 AND (endtime IS NULL OR endtime > UNIX_TIMESTAMP())" , $limit , '?news'.$katmpl , 'news');
        // Normale News
        $tpl = new tpl ('news/news.htm');

        $abf = "SELECT
      a.news_title as title,
      a.news_id as id,
      DATE_FORMAT(a.news_time,'%d. %m. %Y - %H:%i Uhr') as datum,
      DATE_FORMAT(a.news_time,'%W') as dayofweek,
      a.news_kat as kate,
      a.news_text as text,
      b.name as username,
      c.name as editorname,
      a.html,
      a.edit_time,
      a.klicks,
      b.id as uid
    FROM prefix_news as a
    LEFT JOIN prefix_user as b ON a.user_id = b.id
    LEFT JOIN prefix_user as c ON a.editor_id = c.id
    WHERE (((" . pow(2, abs($_SESSION['authright'])) . " | a.news_recht) = a.news_recht) OR
	      (a.news_groups != 0 AND ((a.news_groups ^ $groups) != (a.news_groups | $groups)))) AND $news_kat
		a.`show` > 0 AND a.`show` <= UNIX_TIMESTAMP() AND news_id != $tn_id AND a.`archiv` != 1 AND
		(a.endtime IS NULL OR a.endtime > UNIX_TIMESTAMP())
    ORDER BY a.news_time DESC
    LIMIT " . $anfang . "," . $limit;

        // echo '<pre>'.$abf.'</pre>';
        $erg = db_query($abf);
    	if (db_num_rows($erg) == 0 and !empty($news_kat)) {
    		echo 'Keine News in dieser Kategorie gefunden.<br />
    			<a href="index.php?news">News&uuml;bersichtsseite aufrufen</a>';
    		$design->footer(1);
    	}
        while ($row = db_fetch_assoc($erg)) {
            $k0m = db_query("SELECT COUNT(ID) FROM `prefix_koms` WHERE uid = " . $row['id'] . " AND cat = 'NEWS'");
            $row['kom'] = db_result($k0m, 0);

            $row['kate'] = news_find_kat($row['kate']);
            $row['datum'] = $lang[$row['dayofweek']] . ' ' . $row['datum'];
            if (strpos ($row['text'] , '[PREVIEWENDE]') !== false) {
                $a = explode('[PREVIEWENDE]' , $row['text']);
                $row['text'] = $a[0];
                $row['readwholenews'] = '<a href="index.php?news-' . $row['id'] . '" alt="mehr lesen" title="mehr lesen"><img src="include/images/icons/news/more.gif" alt="mehr lesen" border="0"></a>';
            } else {
                $row['readwholenews'] = '';
            }
            $row['klicks'] = '<img src="include/images/icons/news/counter.gif" alt="' . $row['klicks'] . ' mal gelesen" title="' . $row['klicks'] . ' mal gelesen" border="0">';
            if (!$row['html']) {
                $row['text'] = bbcode($row['text']);
            }
            $row['edit'] = is_null($row['edit_time']) ? '' : '<br /><i>zuletzt ge&auml;ndert am ' . date('d.m.Y - H:i', strtotime($row['edit_time'])) . '&nbsp;Uhr';
            if (!empty($row['edit']) and $row['editorname'] != $row['username']) {
                $row['edit'] .= ' von ' . $row['editorname'] . '</i>';
            } elseif (!empty($row['edit'])) {
                $row['edit'] .= '</i>';
            }
            $tpl->set_ar_out($row, 0);
        }
        $tpl->set_out('SITELINK', $MPL, 1);
        unset($tpl);
    }
} else {
    $design->header();
    $xajax->printJavascript();

    $nid = escape($menu->get(1), 'integer');
    $erg = db_query("SELECT * FROM `prefix_news` WHERE `show` > 0 AND `show` <= UNIX_TIMESTAMP() AND news_id = '" . $nid . "'");
    if (db_num_rows($erg) == 0) {
        $dontShow = true;
	} else {
		$dontShow = false;
		$row = db_fetch_object($erg);
		if ((pow(2, abs($_SESSION['authright'])) | $row->news_recht) != $row->news_recht) {
			$dontShow = true;
		}
		if ($dontShow) {
			foreach($_SESSION['authgrp'] as $id => $bool) {
				if ($bool and (pow(2, abs($id)) | $row->news_groups) == $row->news_groups) {
					$dontShow = false;
					break;
				}
			}
		}
	}

	if ($dontShow) {
		echo 'News existiert nicht oder Sie haben keine Rechte sie zu sehen.  <a href="javascript:history.back();">zur&uuml;ck</a>';
		$design->footer(1);
	}

    $komsOK = true;
    if ($allgAr['Ngkoms'] == 0) {
        if (loggedin()) {
            $komsOK = true;
        } else {
            $komsOK = false;
        }
    }
    if ($allgAr['Nukoms'] == 0) {
        $komsOK = false;
    }

    $kom_info = '';
    // kommentar add
    if ((loggedin() OR chk_antispam ('newskom')) AND $komsOK AND !empty($_POST['name']) AND !empty($_POST['txt'])) {
        $_POST['txt'] = escape($_POST['txt'], 'string');
        $_POST['name'] = escape($_POST['name'], 'string');
        if (checkName($_POST['name']) or loggedin()) {
            if (loggedin()) {
                $_POST['name'] = $_SESSION['authname'];
            }
            db_query("INSERT INTO `prefix_koms` (`uid`,`cat`,`name`,`text`) VALUES (" . $nid . ",'NEWS','" . $_POST['name'] . "','" . $_POST['txt'] . "')");
        } else {
            $kom_info = '<span style="color:red;">Dieser Name ist bereits an einen User vergeben, benutze bitte einen anderen.</span><br />';
        }
    }
    // kommentar add
    // kommentar loeschen
    if ($menu->getA(2) == 'd' AND is_numeric($menu->getE(2)) AND has_right(- 7, 'news')) {
        $kommentar_id = escape($menu->getE(2), 'integer');
        db_query("DELETE FROM prefix_koms WHERE uid = " . $nid . " AND cat = 'NEWS' AND id = " . $kommentar_id);
    }
    // kommentar loeschen
    $kategorie = news_find_kat($row->news_kat);

    $textToShow = $row->html ? $row->news_text : bbcode($row->news_text);
    $textToShow = str_replace('[PREVIEWENDE]', '', $textToShow);
    if (!empty($such)) {
        $textToShow = markword($textToShow, $such);
    }
    // klicks zaehlen
    db_query("UPDATE prefix_news SET klicks = klicks + 1 WHERE news_id = " . $nid);

    $tpl = new tpl ('news/news.htm');
    $ar = array (
        'TEXT' => $textToShow,
        'KATE' => $kategorie,
        'NID' => $nid,
        'uname' => $_SESSION['authname'],
        'ANTISPAM' => (loggedin()?'':get_antispam ('newskom', 0)),
        'NAME' => $row->news_title,
        'info' => $kom_info
        );
    $tpl->set_ar_out($ar, 2);

    if ($komsOK) {
        $tpl->set_ar_out (array ('NAME' => $row->news_title , 'NID' => $nid, 'style' => loggedin() ? 'style="display:none;"' : ''), 3);
    }
    $erg1 = db_query("SELECT text, name, id FROM `prefix_koms` WHERE uid = " . $nid . " AND cat = 'NEWS' ORDER BY id DESC");
    $ergAnz1 = db_num_rows($erg1);
    if ($ergAnz1 == 0) {
        echo '<b>' . $lang['nocomments'] . '</b>';
    } else {
        $zahl = $ergAnz1;
        while ($row1 = db_fetch_assoc($erg1)) {
            $row1['text'] = bbcode(trim($row1['text']));
            if (has_right(- 7, 'news')) {
                $row1['text'] .= '<a href="javascript:delkom(' . $nid . ',' . $row1['id'] . ')"><img src="include/images/icons/del.gif" alt="l&ouml;schen" border="0" title="l&ouml;schen" /></a>';
            }
            $tpl->set_ar_out(array('NAME' => $row1['name'], 'TEXT' => $row1['text'], 'ZAHL' => $zahl) , 4);
            $zahl--;
        }
    }
    $tpl->out(5);
}

$design->footer();

?>