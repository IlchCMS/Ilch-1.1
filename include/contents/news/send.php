<?php
#  Script Copyright by:  Topolino
#  Support auf www.honklords.de


defined ('main') or die ( 'no direct access' );


$title = $allgAr['title'].' || News einen Freund senden';
$hmenu = 'Diese News an einen Freund senden';
$design = new design ( $title , $hmenu );
$design->header();


  $nid = escape($menu->get(2), 'integer');
  $abf = "SELECT
      a.news_title,
      a.news_id,
      DATE_FORMAT(a.news_time,'%d. %m. %Y') as datum,
      DATE_FORMAT(a.news_time,'%W') as dayofweek,
      a.news_kat as kate,
      a.news_text as text,
      a.news_recht as recht,
      b.name as username
    FROM prefix_news as a
    LEFT JOIN prefix_user as b ON a.user_id = b.id
    WHERE news_id = '".$nid."'
    ORDER BY news_time DESC ";

  $erg = db_query($abf);
  $row = db_fetch_assoc($erg);


    $newpoint   = '<img src="include/images/icons/stop.gif" width="16" height="16">&nbsp;';   // Fehlergrafik

    //--------------------------------------------------------------------------------------------------------------

    // Voreinstellung per Parameterübergabe

if(isset($_POST['submit'])) {
        if(empty($_POST['name']))  {
            $fehler .= $newpoint."Bitte geben Sie Ihren <b>Namen</b> ein.<br>\n";
        } elseif(strlen($_POST['name']) < 2) {
            $fehler .= $newpoint."Ihr <b>Name</b> hat bestimmt mehr als 1 Zeichen... :-)<br>\n";
        }
        if(empty($_POST['mail']))  {
            $fehler .= $newpoint."Bitte geben Sie ihre <b>Emailadresse</b> an.<br>";
        } elseif(!empty($_POST['mail']) && !empty($_POST['mail']) && !eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@([a-z0-9-]+\.){1,3}([a-z0-9-]{2,3})$",$_POST['mail'])) {
            $fehler .= $newpoint."Die <b>eMail-Adresse</b> entspricht nicht der korrekten Syntax.<br>\n";
        }
        if(empty($_POST['fname']))  {
            $fehler .= $newpoint."Bitte geben Sie den <b>Namen</b> ihres Freundes ein.<br>\n";
        } elseif(strlen($_POST['fname']) < 2) {
            $fehler .= $newpoint."Der <b>Name</b> ihres Freundes hat bestimmt mehr als 1 Zeichen... :-)<br>\n";
        }
        if(empty($_POST['fmail']))  {
            $fehler .= $newpoint."Bitte geben Sie die <b>Emailadresse</b> ihres Freundes an.<br>";
        } elseif(!empty($_POST['fmail']) && !empty($_POST['fmail']) && !eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@([a-z0-9-]+\.){1,3}([a-z0-9-]{2,3})$",$_POST['fmail'])) {
            $fehler .= $newpoint."Die <b>eMail-Adresse</b> entspricht nicht der korrekten Syntax.<br>\n";
        }
        if(empty($_POST['txt']))  {
            $fehler .= $newpoint."Sie haben noch keinen <b>Text</b> eingegeben.<br>\n";
        }
        if(chk_antispam ('send') != true){
            $fehler .= $newpoint."Bitte geben Sie den gültigen <b>Antispam-Code</b> ein.<br>\n";
        }

if(!$fehler) {
            // Mailinhalt definieren:
	$text  = "Hallo ".$_POST['fname'].",\n";
  $text .= str_repeat('.',70)."\n";
  $text .= "Ihr Freund ".$_POST['name']." fand den folgenden Artikel interessant und wollte ihn an Sie schicken.\n";
  $text .= "News Url:  http://".$_SERVER['HTTP_HOST'].$_SERVER["SCRIPT_NAME"]."?news-".$_POST['nid']."\n";
  $text .= "Hier können Sie weitere interessante Artikel lesen: ".$allgAr['title']."\n";
  $text .= "http://".$_SERVER['HTTP_HOST']."\n";
  $text .= str_repeat('.',70)."\n";
  $text .= $name." möchte Ihnen ergänzend folgendes mitteilen:\n";
  $text .= $txt;
  $Text .= str_repeat('.',70)."\n".strip_tags($_POST['txt'])."\n";


  $betreff = 'Ein interessanter Artikel bei '.$allgAr['title'].','.$_POST['ntitle'].'';
//-> Alles ok, Mail verschicken.
  $fmail = escape($_POST['fmail'],'string');
  icmail($fmail,$betreff,$text,$name);
// informieren
	        wd ('index.php?news', 'Die News <b>'.$_POST['ntitle'].'</b> wurde an <b>'.$_POST['fname'].'</b> gesendet', 3 );
        } echo '<font color="red">'.$fehler.'</font><br /><a href="javascript:history.back(1)"><b>&laquo;</b> '.$lang['back'].'</a>';
    } else {
//----------------------------------------------------------------------------------------------------
// Hier erfolgt die html-Ausgabe. Diese kann beliebig angepasst werden.
// Allerdings bitte darauf achten, dass die PHP-Befehle unverändert bleiben.


 $tpl = new tpl ( 'news/send.htm' );
  $ar = array (
    'nid'      => $row['news_id'],
    'ntitle'   => $row['news_title'],
    'name'     => $name,
		'mail'     => $mail,
		'fname'    => $fname,
    'fmail'    => $fmail,
    'txt'      => $txt,
    'ANTISPAM' => get_antispam ('send', 1),
  );


  $tpl->set_ar_out($ar,0);
}
$design->footer();
 ?>