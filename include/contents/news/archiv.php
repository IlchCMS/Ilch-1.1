<?php
#   Script Copyright by: Manuel Staechele
#   Support: www.ilch.de
#   Modded by Mairu für News Extended
defined ('main') or die ( 'no direct access' );

$limit = 20; //News pro Seite
$tn_id = intval(@db_result($news_opts = db_query("SELECT v1 FROM prefix_allg WHERE k = 'news' LIMIT 1"),0));

//Veränderte db_make_sites, angepasst für ajax
function mpl ($cat,$year,$page) {
    global $limit,$tn_id;
    $hvmax = 4; // hinten und vorne links nach page
	$maxpage = '';
    $MPL = '';
	$groups = getGroupRights();
	$resultID = db_query ( "SELECT COUNT(a.news_id) FROM prefix_news a WHERE (((" . pow(2, abs($_SESSION['authright'])) . " | a.news_recht) = a.news_recht) OR
	      (a.news_groups != 0 AND ((a.news_groups ^ $groups) != (a.news_groups | $groups)))) $year $cat AND a.`show` > 0 AND a.`show` <= UNIX_TIMESTAMP() AND a.news_id != $tn_id AND (a.`archiv` = 1 OR (a.`archiv` = 2 AND a.endtime < UNIX_TIMESTAMP()))");
    $total    = db_result($resultID,0);

    if ($limit < $total) {
        $maxpage = $total / $limit;
        if (is_double($maxpage)) {
            $maxpage = ceil($maxpage);
        }
        $ibegin = $page - $hvmax;
        $iende  = $page + $hvmax ;

        $vgl1 = $iende + $ibegin;
        $vgl2 = ($hvmax * 2) + 1;
        if ( $vgl1 <= $vgl2 ) {
            $iende = $vgl2;
        }
        $vgl3 = $maxpage - ($vgl2 -1);
        if ($vgl3 < $ibegin ) {
          $ibegin = $vgl3;
        }

        if ($ibegin < 1) {
            $ibegin = 1;
        }
        if ($iende > $maxpage) {
            $iende = $maxpage;
        }
        $vMPL = '';
        if ($ibegin > 1) {
            $vMPL = "<a href=\"javascript:void(0);\" onclick=\"xajax_showcat(document.getElementById('cats').value,document.getElementById('year').value,1)\">&laquo;</a>";
        }
        $MPL = $vMPL.'[ ';
        for($i=$ibegin; $i <= $iende; $i++) {
            if($i == $page) {
            	$MPL .= $i;
            } else {
            	$MPL .= "<a href=\"javascript:void(0);\" onclick=\"xajax_showcat(document.getElementById('cats').value,document.getElementById('year').value,$i)\">$i</a>";
            }
            if ($i != $iende) {
            	$MPL .= ' | ';
            }
        }
        $MPL .= ' ]';
        if ($iende < $maxpage) {
            $MPL .= "<a href=\"javascript:void(0);\" onclick=\"xajax_showcat(document.getElementById('cats').value,document.getElementById('year').value,$maxpage)\">&raquo;</a>";
        }
    }
	return $MPL;
}

//Newsarchiv ausgeben über xajax
function showcat($cat,$year,$page=1){
    global $limit,$tn_id;
    $output = '';

    $resp = new xajaxResponse();
    $tpl = new tpl('news/archiv');

    $page = intval($page);
    $cat = $cat == '0' ? '' : 'AND a.news_kat = "'.escape($cat,'string').'"';
    $year = $year == '0' ? '' : "AND a.news_time >= '".escape($year,'integer')."-01-01' AND a.news_time <= '".escape($year,'integer')."-12-31'";

    $anfang = ($page - 1) * $limit;
	$groups = getGroupRights();
	$abf = "SELECT
      a.news_title as title,
      a.news_id as id,
      DATE_FORMAT(a.news_time,'%d.%m.%Y - %H:%i Uhr') as datum,
      a.news_kat as kat,
      a.user_id as uid,
      b.name as name,
      c.name as editorname,
      a.klicks,
      a.edit_time,
      COUNT(k.id) as koms
    FROM prefix_news as a
    LEFT JOIN prefix_user as b ON a.user_id = b.id
    LEFT JOIN prefix_user as c ON a.editor_id = c.id
	LEFT JOIN prefix_koms as k ON k.cat = 'NEWS' AND k.uid = a.news_id
    WHERE (((" . pow(2, abs($_SESSION['authright'])) . " | a.news_recht) = a.news_recht) OR
	      (a.news_groups != 0 AND ((a.news_groups ^ $groups) != (a.news_groups | $groups)))) $year $cat AND a.`show` > 0 AND a.`show` <= UNIX_TIMESTAMP() AND a.news_id != $tn_id AND (a.`archiv` = 1 OR (a.`archiv` = 2 AND a.endtime < UNIX_TIMESTAMP()))
	GROUP BY a.news_title,  a.news_id, a.news_time, a.news_kat, b.name, c.name, a.klicks, a.edit_time
	ORDER BY a.news_time DESC
    LIMIT ".$anfang.",".$limit;

    $q = db_query($abf);
    if (db_num_rows($q) > 0) {
        $output .= $tpl->get(1);
        while($r = db_fetch_assoc($q)){
            $class = $class == 'Cmite' ? 'Cnorm' : 'Cmite';
            $r['edit'] = is_null($r['edit_time']) ? '' : '&nbsp;<img src="include/images/icons/edit.gif" title="zuletzt ge&auml;ndert am '.date('d.m.Y - H:i',strtotime($r['edit_time'])).'&nbsp;Uhr';
            if (!empty($r['edit']) and $r['editorname'] != $r['name']) {
                $r['edit'] .= ' von '.$r['editorname'];
            }
            if (!empty($r['edit'])) {
                $r['edit'] .= '" />';
            }
            $r['print'] = '<a href="?news-print-'.$r['id'].'" alt="Druckoptimierte Version" title="Druckoptimierte Version"><img src="include/images/icons/print_k.gif" alt="Druckoptimierte Version" border="0"></a>';
            $r['class'] = $class;
            $tpl->set_ar($r);
            $output .= $tpl->get(2);
        }
        $tpl->set('MPL',mpl($cat,$year,$page));
        $output .= $tpl->get(3);
    } else {
        $output = 'Keine News in dieser Kategorie';
    }
    $resp->assign('narchiv','innerHTML' , utf8_encode($output) );
    return $resp;
}

//xajax
$xajax = new xajax('index.php?news-archiv-ajax');
$xajax->registerFunction('showcat');
$xajax->processRequest();


//Standardausgabe - Startseite
if ($menu->get(1) != 'ajax') {
    $title = $allgAr['title'].' :: Newsarchiv';
    $hmenu = 'Newsarchiv';
    $design = new design ( $title , $hmenu );
    $design->header();
    echo $xajax->printJavascript();
    $tpl = new tpl('news/archiv');
    $cats = $years = '';
    $q = db_query("SELECT DISTINCT news_kat FROM prefix_news WHERE (archiv = 1 OR (archiv = 2 AND endtime < UNIX_TIMESTAMP())) and `show` > 0");
    while($r = db_fetch_object($q)){
        $cats .= $tpl->list_get('cats', array( $r->news_kat));
    }
    $q = db_query("SELECT DISTINCT YEAR(news_time) AS `year` FROM prefix_news WHERE (archiv = 1 OR (archiv = 2 AND endtime < UNIX_TIMESTAMP())) AND `show` > 0");
    while($r = db_fetch_object($q)){
        $years .= $tpl->list_get('cats', array( $r->year));
    }
    $tpl->set('years',$years);
    $tpl->set_out('cats',$cats,0);
    $design->footer(1);
}

?>