<?php
#   Script Copyright by: Manuel Staechele
#   Support: www.ilch.de
#   Modded by Mairu für News Extended
defined ('main') or die ( 'no direct access' );

function getKats () {
  $katAR = array();
	$kats = '';
	$erg = db_query("SELECT DISTINCT news_kat FROM `prefix_news`");
	while ($row = db_fetch_object($erg)) {
		$katAr[] = $row->news_kat;
	}
    $katAr[] = 'Allgemein';
	$katAr = array_unique($katAr);
	foreach($katAr as $a) {
	  $kats .= '<option'.$sel.'>'.$a.'</option>';
	}
  return ($kats);
}

if ($menu->get(2) != 'ajax') {
    $title = $allgAr['title'].' :: News einsenden';
    $hmenu = 'News einsenden';
    $design = new design ( $title , $hmenu );
    $design->header();
}

function vorschau($form) {
    $resp = new xajaxResponse();
    $txt = utf8_decode($form['txt']);
    $txt = bbcode($txt);
    $resp->assign('vorschau_td','innerHTML' , $txt );
    $resp->script("document.getElementById('vorschau_table').style.display = 'block';");
    if (isset($info['ImgMaxBreite'])) {
        $resp->script("ResizeBBCodeImages()");
    }
    return $resp;
}

//xajax für vorschau
$xajax = new xajax('index.php?news-add-ajax');
$xajax->registerFunction('vorschau');
$xajax->processRequest();

if (loggedin()) {
    if (isset($_POST['submit'])) {
        $txt = escape($_POST['txt'],'textarea');
        $titel = escape($_POST['titel'],'string');
        $pmempf = explode('#',@db_result($q = db_query("SELECT v3,v4 FROM prefix_allg WHERE k = 'news'"),0,0));
        $kat = @db_result($q,0,1);
        if ($kat = '#0#') {
            $kat = escape($_POST['kat'],'string');
        }
        if (db_query("INSERT INTO `prefix_news` (user_id,news_time,news_recht,news_kat,news_title,news_text,`show`,html) VALUES ({$_SESSION['authid']},NOW(),0,'$kat','$titel','$txt',0,0)")) {
            foreach($pmempf as $uid){
                $uid = intval($uid);
                if ($uid > 0) {
                    sendpm($_SESSION['authid'],$uid,'News eingesendet',$_SESSION['authname'].' hat eine News mit dem Title [b]'.$titel.'[/b] eingesendet, die nun im Adminmenü freigeschaltet werden muss.',-1);
                }
            }
            $wdtxt = 'Deine News wurde erfolgreich eingetragen und ein Admin dar&uuml;ber informiert.';
        } else {
            $wdtxt = 'Es ist ein Fehler beim Eintragen aufgetreten, falls das immer passiert solltest du einen Admin kontaktieren.';
        }
        wd(array('Startseite'=>'index.php','weitere News einsenden'=>'index.php?news-add'),$wdtxt,10);
    } else {
        echo $xajax->printJavascript();
        $tpl = new tpl('news/add');
        $tpl->out(0);
        $kat = @db_result($q = db_query("SELECT v4 FROM prefix_allg WHERE k = 'news'"),0,0);
        if ($kat == '#0#') {
            $tpl->set_out('KATS',getKats(),1);
        }
        $tpl->out(2);
        if (isset($info['ImgMaxBreite'])) {
		    $tpl->out(4); //BBCode 2.0 Modul
		} else {
            $tpl->out(3); //BBCode vom Ilchscript
        }
		$tpl->set_out('SMILIES',getsmilies(),5);
    }
} else {
    echo 'Nur f&uuml;r angemeldete Benutzer';
}
$design->footer();
?>