<?php
// Copyright by: Topolino-Arts
defined ('main') or die ('no direct access');

function news_find_kat ($kat) {
    $katpfad = 'images/news/';
    $katjpg = $katpfad . $kat . '.jpg';
    $katgif = $katpfad . $kat . '.gif';
    $katpng = $katpfad . $kat . '.jpg';

    if (file_exists($katjpg)) {
        $pfadzumBild = $katjpg;
    } elseif (file_exists ($katgif)) {
        $pfadzumBild = $katgif;
    } elseif (file_exists ($katpng)) {
        $pfadzumBild = $katpng;
    }

    if (!empty($pfadzumBild)) {
        $katimages = '<img style="width: 300px; hight: 38px" src="' . $pfadzumBild . '" alt="' . $kat . '">';
    } else {
        $katimages = '<b>' . $kat . '</b><br /><br />';
    }

    return ($katimages);
}
$groups = getGroupRights();
$nid = escape($menu->get(2), 'integer');
$abf = "SELECT
      a.news_title as title,
      a.news_id as id,
      DATE_FORMAT(a.news_time,'%d. %m. %Y') as datum,
      DATE_FORMAT(a.news_time,'%W') as dayofweek,
      a.news_kat as kate,
      a.news_text as text,
      b.name as username,
      a.html
    FROM prefix_news as a
    LEFT JOIN prefix_user as b ON a.user_id = b.id
    WHERE (((" . pow(2, abs($_SESSION['authright'])) . " | a.news_recht) = a.news_recht) OR
	      (a.news_groups != 0 AND ((a.news_groups ^ $groups) != (a.news_groups | $groups)))) AND news_id = '" . $nid . "'
    ORDER BY news_time DESC ";

$erg = db_query($abf);

if (db_num_rows($erg) == 1) {
	$row = db_fetch_assoc($erg);

	$katimages = news_find_kat($row['kate']);
	$textToShow = $row['html'] ? $row['text'] : bbcode($row['text']);
	$textToShow = str_replace('[PREVIEWENDE]', '', $textToShow);

	$tpl = new tpl ('news/print.htm');
	$ar = array (
    'HEADER_ADDITIONS' => $ILCH_HEADER_ADDITIONS,
    'TITLE' => $row['title'],
    'TEXT' => $textToShow,
    'KATIMG' => $katimages,
    'KAT' => $row['kate'],
    'AUTOR' => $row['username'],
    'DATUM' => $row['datum']
    );
	$tpl->set_ar_out($ar, 0);

} else {
	echo 'News existiert nicht oder Sie haben keine Rechte sie zu sehen. <a href="javascript:history.back();">zur&uuml;ck</a>';
}

?>