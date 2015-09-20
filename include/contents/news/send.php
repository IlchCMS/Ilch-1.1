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


    //--------------------------------------------------------------------------------------------------------------

    // Voreinstellung per Parameterübergabe

if(isset($_POST['submit'])) {
        if(empty($_POST['name']))  {
            $fehler .= "Bitte geben Sie Ihren <strong>Namen</strong> ein.<br>\n";
        } elseif(strlen($_POST['name']) < 2) {
            $fehler .= "Ihr <strong>Name</strong> hat bestimmt mehr als 1 Zeichen... :-)<br>\n";
        }
        if(empty($_POST['mail']))  {
            $fehler .= "Bitte geben Sie ihre <strong>Emailadresse</strong> an.<br>";
        } elseif(!empty($_POST['mail']) && !empty($_POST['mail']) && !eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@([a-z0-9-]+\.){1,3}([a-z0-9-]{2,3})$",$_POST['mail'])) {
            $fehler .= "Die <strong>eMail-Adresse</strong> entspricht nicht der korrekten Syntax.<br>\n";
        }
        if(empty($_POST['fname']))  {
            $fehler .= "Bitte geben Sie den <strong>Namen</strong> ihres Freundes ein.<br>\n";
        } elseif(strlen($_POST['fname']) < 2) {
            $fehler .= "Der <strong>Name</strong> ihres Freundes hat bestimmt mehr als 1 Zeichen... :-)<br>\n";
        }
        if(empty($_POST['fmail']))  {
            $fehler .= "Bitte geben Sie die <strong>Emailadresse</strong> ihres Freundes an.<br>";
        } elseif(!empty($_POST['fmail']) && !empty($_POST['fmail']) && !eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@([a-z0-9-]+\.){1,3}([a-z0-9-]{2,3})$",$_POST['fmail'])) {
            $fehler .= "Die <strong>eMail-Adresse</strong> entspricht nicht der korrekten Syntax.<br>\n";
        }
        if(empty($_POST['txt']))  {
            $fehler .= "Sie haben noch keinen <strong>Text</strong> eingegeben.<br>\n";
        }
        if(chk_antispam ('send') != true){
            $fehler .= "Bitte geben Sie den gültigen <strong>Antispam-Code</strong> ein.<br>\n";
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
	        wd ('index.php?news', '<div class="text-center"><span class="ilch_hinweis_gruen">Die News <strong>'.$_POST['ntitle'].'</strong> wurde an <strong>'.$_POST['fname'].'</strong> gesendet</span></div>', 3 );
        } echo wd ('index.php?news-send-'.$_POST['nid'].' ','<div class="text-center"><span class="ilch_hinweis_rot">'.$fehler.'</span></div>', 4 );
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