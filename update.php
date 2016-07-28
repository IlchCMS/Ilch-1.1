<?php
#   Copyright by: Manuel
#   Support: www.ilch.de

$readme = <<<README
Changelog:
==========
+ neue Funktionen       * Aenderungen/Bugfixes

Version 1.1 Q
-------------
* Sicherheitsupdates im Bezug auf moegliche SQL-Injections
* Integration SSL / TLS beim Mail-Versand (Transportverschluesselung)
* PHP Versionscheck waehrend der Installation
* Bugfix Forum verschieben
* Korrektur ungueltiger Links
* Kontakt Formular improved --> Absenden des Formulars nur dann, wenn alle Felder gefuellt sind.
+ Integration des BBCode
+ Integration von News Extended
+ Komplette Ueberarbeitung des Backends -- Integration Bootstrap
+ Frontend Templates alle unnoetigen Tabellen durch divs ersetzt
README;

$rows = substr_count($readme, "\n");
if ($rows > 45)
    $rows = 45;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html lang="de">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <title>... ::: [ U p d a t e  f &uuml; r &nbsp; i l c h C l a n  &nbsp; 1 . 1 Q ] ::: ...</title>
        <link rel="shortcut icon" href="include/designs/ilchClan/img/favicon.png" type="image/png">
        <link rel="stylesheet" href="include/designs/ilchClan/style.css" type="text/css"> 
        <link rel="stylesheet" href="install.css" type="text/css">
    </head>
    <body>
        <form method="post">
                        <div class="installcontent">
                    <legend>... ::: [ U p d a t e  f &uuml; r &nbsp; i l c h C l a n  &nbsp; 1 . 1 Q] ::: ...</legend>
                    <div class="install_lizenz">
			<?php
			if (empty($_POST['step'])) {
			    ?>
    			<div class="text-center">
    			    <h2>Readme</h2>
    			    <textarea style="width:98%" rows="<?php echo $rows; ?>"><?php echo htmlentities($readme, ENT_COMPAT, 'ISO-8859-1'); ?></textarea><br><br>

    			    <br><br>
    			    Dieses Script soll die n&ouml;tigen Datanbank&auml;ndernungen f&uuml;r das Update machen<br>
    			    <br>
    			    <input type="hidden" name="step" value="2" />
    			    <input type="submit" value="Installieren" />
    			</div>
			    <?php
			} elseif ($_POST['step'] == 2) {

			    define('main', TRUE);
			    require_once('include/includes/config.php');
			    require_once('include/includes/func/db/mysql.php');
			    db_connect();

			    $sql_statements = array();

			    //Update 1.1d
			    if (db_count_query('SELECT COUNT(*) FROM `prefix_config` WHERE `schl` = "allg_default_subject"') == 0) {
				$sql_statements[] = '-- UPDATE 1.1D';
				$sql_statements[] = "ALTER TABLE `prefix_gallery_cats` CHANGE `besch` `besch` TEXT NOT NULL";
				$sql_statements[] = "ALTER TABLE `prefix_warmaps` CHANGE `opp` `opp` MEDIUMINT NOT NULL DEFAULT '0', CHANGE `owp` `owp` MEDIUMINT NOT NULL DEFAULT '0'";
				$sql_statements[] = "ALTER TABLE `prefix_wars` CHANGE `opp` `opp` MEDIUMINT NOT NULL DEFAULT '0', CHANGE `owp` `owp` MEDIUMINT NOT NULL DEFAULT '0'";
				$sql_statements[] = "INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('allg_default_subject', 'input', 'Allgemeine Optionen', 'Standard Betreff bei eMails', 'automatische eMail')";
			    }

			    //Update 1.1f
			    $old = array();
			    $qry = db_query('SHOW FULL COLUMNS FROM `prefix_forumcats`');
			    echo db_error();
			    while ($r = db_fetch_assoc($qry)) {
				echo db_error();
				$old[] = $r['Field'];
			    }
			    if (!in_array('cid', $old)) {
				$sql_statements[] = '-- UPDATE 1.1F';
				$sql_statements[] = "ALTER TABLE `prefix_forumcats` ADD `cid` TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER `id`";
				$sql_statements[] = "INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('sb_maxwordlength', 'input', 'Shoutbox Optionen', 'Maximale Wortl&auml;nge in der Shoutbox', '10')";
				$sql_statements[] = "INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('sb_recht', 'grecht', 'Shoutbox Optionen', 'Schreiben in der Shoutbox ab?', '0')";
				$sql_statements[] = "INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('sb_limit', 'input', 'Shoutbox Optionen', 'Anzahl angezeigter Nachrichten', '5')";
				$sql_statements[] = "INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('antispam', 'grecht2', 'Allgemeine Optionen', 'Antispam <small>(ab diesem Recht keine Eingabe mehr erforderlich)</small>', '-2')";
			    }

			    //Update 1.1g
			    $old = array();
			    $qry = db_query('SHOW FULL COLUMNS FROM `prefix_usercheck`');
			    while ($r = db_fetch_assoc($qry)) {
				$old[] = $r['Field'];
			    }
			    if (!in_array('groupid', $old)) {
				$sql_statements[] = '-- UPDATE 1.1G';
				$sql_statements[] = 'ALTER TABLE `prefix_usercheck` ADD `groupid` TINYINT NOT NULL';
				$sql_statements[] = "INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('joinus_rules', 'r2', 'Team Optionen', 'Regeln bei Joinus vollst&auml;ndig anzeigen?', '0')";
				$sql_statements[] = "UPDATE `prefix_config` SET `frage` = 'Standard Absender bei eMails' WHERE `schl` = 'allg_default_subject' LIMIT 1";
				$sql_statements[] = "INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('groups_forall', 'r2', 'Team Optionen', 'Modulrecht <i>Gruppen</i> auf eigene Gruppe beschr&auml;nken?', '1')";
			    }

			    //Update 1.1i
			    $old = array();
			    $qry = db_query('SHOW FULL COLUMNS FROM `prefix_config`');
			    while ($r = db_fetch_assoc($qry)) {
				$old[] = $r['Field'];
			    }
			    if (!in_array('pos', $old)) {
				$sql_statements[] = '-- UPDATE 1.1I';
				$sql_statements[] = "ALTER TABLE `prefix_config` ADD `pos` SMALLINT(6) NOT NULL default '0'";
				$sql_statements[] = "INSERT INTO `prefix_config` (`schl`, `typ`, `kat`, `frage`, `wert`, `pos`) VALUES('mail_smtp', 'r2', 'Allgemeine Optionen', 'SMTP f?r den Mailversand verwenden? <a href=\"admin.php?smtpconf\" class=\"smalfont\">weitere Einstellungen</a>', '0', 0)";
			    }

			    //Update 1.1n
			    if (db_count_query("SELECT COUNT(*) FROM `prefix_allg` WHERE k = 'smtpconf'") == 0) {
				$smtp = array('smtp_host' => '', 'smtp_port' => '', 'smtp_auth' => 'auth', 'smtp_pop3beforesmtp' => '', 'smtp_pop3host' => '',
				    'smtp_pop3port' => '', 'smtp_login' => '', 'smtp_email' => '', 'smtp_pass' => '', 'smtp_changesubject' => '1');

				$qry = db_query('SELECT * FROM `prefix_config` WHERE `schl` LIKE "mail_%"');
				while ($r = db_fetch_assoc($qry)) {
				    switch ($r['schl']) {
					case 'mail_smtp_login': $smtp['smtp_login'] = $r['wert'];
					    break;
					case 'mail_smtp_password': $smtp['smtp_pass'] = $r['wert'];
					    break;
					case 'mail_smtp_host': $smtp['smtp_host'] = $r['wert'];
					    break;
					case 'mail_smtp_email': $smtp['smtp_email'] = $r['wert'];
					    break;
				    }
				}
				$smtpser = db_escape_string(serialize($smtp));
				$sql_statements[] = '-- UPDATE 1.1N';
				$sql_statements[] = 'INSERT INTO `prefix_allg` ( `k` , `v1`, `v2`, `v3`, `v4`, `t1`) VALUES ( "smtpconf", "", "", "", "", "' . $smtpser . '" )';
				$sql_statements[] = 'DELETE FROM `prefix_config` WHERE `schl` IN ("mail_smtp_login", "mail_smtp_password", "mail_smtp_host", "mail_smtp_email")';
				$sql_statements[] = 'UPDATE `prefix_config` SET `kat` = "Allgemeine Optionen", `frage` = "SMTP f?r den Mailversand verwenden? <a href=\"admin.php?smtpconf\" class=\"smalfont\">weitere Einstellungen</a>" WHERE `schl` = "mail_smtp"';
			    }

			    //Update 1.1p
			    $passType = '';
			    $qry = db_query('SHOW COLUMNS FROM `prefix_user` LIKE "pass"');
			    if ($row = db_fetch_assoc($qry)) {
				$passType = trim(strtolower($row['Type']));
			    }
			    if ($passType === 'varchar(32)') {
				$sql_statements[] = '-- UPDATE 1.1P';
				$sql_statements[] = 'ALTER TABLE `prefix_user` MODIFY COLUMN `pass` varchar(123) NOT NULL DEFAULT ""';
				$sql_statements[] = 'ALTER TABLE `prefix_usercheck` MODIFY COLUMN `pass` varchar(123) NOT NULL DEFAULT ""';
				$sql_statements[] = "UPDATE `prefix_config` SET `frage`='Antispam <small>(ab diesem Recht keine Eingabe mehr erforderlich)</small><br><a href=\"http://www.ilch.de/texts-s132.html\" target=\"_blank\">Hilfe: Antispam anpassen</a>' WHERE `schl`='antispam'";
			    }

			    //Update 1.1p.2
			    $sidType = '';
			    $qry = db_query('SHOW COLUMNS FROM `prefix_online` LIKE "sid"');
			    if ($row = db_fetch_assoc($qry)) {
				$sidType = trim(strtolower($row['Type']));
			    }
			    if ($sidType === 'varchar(32)') {
				$sql_statements[] = 'ALTER TABLE `prefix_online` MODIFY COLUMN `sid` varchar(123) NOT NULL DEFAULT ""';
			    }

			    //Update 1.1q
			    $qry = db_query('SHOW TABLES LIKE `prefix_bbcode_badword`');
			    if (!$qry) {
				$sql_statements[] = '-- UPDATE 1.1Q';
				$sql_statements[] = " CREATE TABLE `prefix_bbcode_badword` (
                                `fnBadwordNr` int(10) unsigned NOT NULL auto_increment,
                                `fcBadPatter` varchar(70) NOT NULL default '',
                                `fcBadReplace` varchar(70) NOT NULL default '',
                                PRIMARY KEY  (`fnBadwordNr`)) ENGINE=MyISAM COMMENT='powered by ilch.de'";

				$sql_statements[] = "INSERT INTO `prefix_bbcode_badword` (`fcBadPatter`, `fcBadReplace`) VALUES('Idiot', '*peep*')";
				$sql_statements[] = "INSERT INTO `prefix_bbcode_badword` (`fcBadPatter`, `fcBadReplace`) VALUES('Arschloch', '*peep*')";

				$sql_statements[] = "CREATE TABLE `prefix_bbcode_buttons` (
                                `fnButtonNr` int(10) unsigned NOT NULL auto_increment,
                                `fnFormatB` tinyint(1) unsigned NOT NULL default '1',
                                `fnFormatI` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatU` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatS` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatEmph` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatColor` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatSize` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatUrl` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatUrlAuto` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatEmail` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatLeft` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatCenter` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatRight` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatBlock` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatSmilies` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatList` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatKtext` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatImg` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatImgUpl` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatScreen` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatVideo` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatPhp` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatCss` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatHtml` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatCode` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatQuote` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatCountdown` tinyint(1) unsigned NOT NULL default '0',
                                `fnFormatFlash` tinyint(1) unsigned NOT NULL default '0',
                                PRIMARY KEY (`fnButtonNr`)
                                ) ENGINE = MyISAM COMMENT = 'powered by ilch.de'";

				$sql_statements[] = "INSERT INTO `prefix_bbcode_buttons` (`fnButtonNr`, `fnFormatB`, `fnFormatI`, `fnFormatU`, `fnFormatS`, `fnFormatEmph`, `fnFormatColor`, `fnFormatSize`, `fnFormatUrl`, `fnFormatUrlAuto`, `fnFormatEmail`, `fnFormatLeft`, `fnFormatCenter`, `fnFormatRight`, `fnFormatBlock`, `fnFormatSmilies`, `fnFormatList`, `fnFormatKtext`, `fnFormatImg`, `fnFormatImgUpl`, `fnFormatScreen`, `fnFormatVideo`, `fnFormatPhp`, `fnFormatCss`, `fnFormatHtml`, `fnFormatCode`, `fnFormatQuote`, `fnFormatCountdown`) VALUES(1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1)";

				$sql_statements[] = "CREATE TABLE `prefix_bbcode_config` (
                                `fnConfigNr` int(10) unsigned NOT NULL auto_increment,
                                `fnYoutubeBreite` smallint(3) unsigned NOT NULL default '0',
                                `fnYoutubeHoehe` smallint(3) unsigned NOT NULL default '0',
                                `fcYoutubeHintergrundfarbe` varchar(7) NOT NULL default '',
                                `fnGoogleBreite` smallint(3) unsigned NOT NULL default '0',
                                `fnGoogleHoehe` smallint(3) unsigned NOT NULL default '0',
                                `fcGoogleHintergrundfarbe` varchar(7) NOT NULL default '',
                                `fnMyvideoBreite` smallint(3) unsigned NOT NULL default '0',
                                `fnMyvideoHoehe` smallint(3) unsigned NOT NULL default '0',
                                `fcMyvideoHintergrundfarbe` varchar(7) NOT NULL default '',
                                `fnSizeMax` tinyint(2) unsigned NOT NULL default '0',
                                `fnImgMaxBreite` smallint(3) unsigned NOT NULL default '0',
                                `fnImgMaxHoehe` smallint(3) unsigned NOT NULL default '0',
                                `fnScreenMaxBreite` smallint(3) unsigned NOT NULL default '0',
                                `fnScreenMaxHoehe` smallint(3) unsigned NOT NULL default '0',
                                `fnUrlMaxLaenge` smallint(3) unsigned NOT NULL default '0',
                                `fnWortMaxLaenge` smallint(3) unsigned NOT NULL default '0',
                                `fnFlashBreite` smallint(3) unsigned NOT NULL default '0',
                                `fnFlashHoehe` smallint(3) unsigned NOT NULL default '0',
                                `fcFlashHintergrundfarbe` varchar(7) NOT NULL default '',
                                PRIMARY KEY (`fnConfigNr`)
                                ) ENGINE = MyISAM COMMENT = 'powered by ilch.de'";

				$sql_statements[] = "INSERT INTO `prefix_bbcode_config` (`fnConfigNr`, `fnYoutubeBreite`, `fnYoutubeHoehe`, `fcYoutubeHintergrundfarbe`, `fnGoogleBreite`, `fnGoogleHoehe`, `fcGoogleHintergrundfarbe`, `fnMyvideoBreite`, `fnMyvideoHoehe`, `fcMyvideoHintergrundfarbe`, `fnSizeMax`, `fnImgMaxBreite`, `fnImgMaxHoehe`, `fnScreenMaxBreite`, `fnScreenMaxHoehe`, `fnUrlMaxLaenge`, `fnWortMaxLaenge`, `fnFlashBreite`, `fnFlashHoehe`, `fcFlashHintergrundfarbe`) VALUES(1, 425, 350, '#000000', 400, 326, '#ffffff', 470, 406, '#ffffff', 20, 500, 500, 150, 150, 60, 70, 400, 300, '#ffffff')";

				$sql_statements[] = "CREATE TABLE `prefix_bbcode_design` (
                                `fnDesignNr` int(10) unsigned NOT NULL auto_increment,
                                `fcQuoteRandFarbe` varchar(7) NOT NULL default '',
                                `fcQuoteTabelleBreite` varchar(7) NOT NULL default '',
                                `fcQuoteSchriftfarbe` varchar(7) NOT NULL default '',
                                `fcQuoteHintergrundfarbe` varchar(7) NOT NULL default '',
                                `fcQuoteHintergrundfarbeIT` varchar(7) NOT NULL default '',
                                `fcQuoteSchriftformatIT` varchar(6) NOT NULL default '',
                                `fcQuoteSchriftfarbeIT` varchar(7) NOT NULL default '',
                                `fcBlockRandFarbe` varchar(7) NOT NULL default '',
                                `fcBlockTabelleBreite` varchar(7) NOT NULL default '',
                                `fcBlockSchriftfarbe` varchar(7) NOT NULL default '',
                                `fcBlockHintergrundfarbe` varchar(7) NOT NULL default '',
                                `fcBlockHintergrundfarbeIT` varchar(7) NOT NULL default '',
                                `fcBlockSchriftfarbeIT` varchar(7) NOT NULL default '',
                                `fcKtextRandFarbe` varchar(7) NOT NULL default '',
                                `fcKtextTabelleBreite` varchar(7) NOT NULL default '',
                                `fcKtextRandFormat` varchar(6) NOT NULL default '',
                                `fcEmphHintergrundfarbe` varchar(7) NOT NULL default '',
                                `fcEmphSchriftfarbe` varchar(7) NOT NULL default '',
                                `fcCountdownRandFarbe` varchar(7) NOT NULL default '',
                                `fcCountdownTabelleBreite` varchar(7) NOT NULL default '',
                                `fcCountdownSchriftfarbe` varchar(7) NOT NULL default '',
                                `fcCountdownSchriftformat` varchar(7) NOT NULL default '',
                                `fnCountdownSchriftsize` smallint(2) unsigned NOT NULL default '0',
                                PRIMARY KEY (`fnDesignNr`)
                                ) ENGINE = MyISAM COMMENT = 'powered by ilch.de'";

				$sql_statements[] = "INSERT INTO `prefix_bbcode_design` (`fnDesignNr`, `fcQuoteRandFarbe`, `fcQuoteTabelleBreite`, `fcQuoteSchriftfarbe`, `fcQuoteHintergrundfarbe`, `fcQuoteHintergrundfarbeIT`, `fcQuoteSchriftformatIT`, `fcQuoteSchriftfarbeIT`, `fcBlockRandFarbe`, `fcBlockTabelleBreite`, `fcBlockSchriftfarbe`, `fcBlockHintergrundfarbe`, `fcBlockHintergrundfarbeIT`, `fcBlockSchriftfarbeIT`, `fcKtextRandFarbe`, `fcKtextTabelleBreite`, `fcKtextRandFormat`, `fcEmphHintergrundfarbe`, `fcEmphSchriftfarbe`, `fcCountdownRandFarbe`, `fcCountdownTabelleBreite`, `fcCountdownSchriftfarbe`, `fcCountdownSchriftformat`, `fnCountdownSchriftsize`) VALUES(1, '#f6e79d', '320', '#666666', '#f6e79d', '#faf7e8', 'italic', '#666666', '#f6e79d', '350', '#666666', '#f6e79d', '#faf7e8', '#FF0000', '#000000', '90%', 'dotted', '#ffd500', '#000000', '#FF0000', '90%', '#FF0000', 'bold', 10)";
			    }

			    // Update f�r 1.1Q.2 - > News Extended Integration
			    $old = array();


			    $q = db_query("SHOW FULL COLUMNS FROM `prefix_news`");
			    while ($r = db_fetch_object($q)) {
				$old[] = $r->Field;
			    }

			    $update_news = array();
			    if (!in_array('editor_id', $old)) {
				$update_news[] = 'ADD `editor_id` INT NULL AFTER `news_time`';
			    }
			    if (!in_array('edit_time', $old)) {
				$update_news[] = 'ADD `edit_time` DATETIME NULL AFTER `editor_id`';
			    }
			    if (!in_array('html', $old)) {
				$update_news[] = 'ADD `html` TINYINT ( 1 )NOT NULL';
			    }
			    if (!in_array('show', $old)) {
				$update_news[] = 'ADD `show` INT ( 12 ) NOT NULL';
			    }
			    if (!in_array('archiv', $old)) {
				$update_news[] = 'ADD `archiv` TINYINT ( 1 ) NOT NULL DEFAULT \'0\'';
			    }
			    if (!in_array('endtime', $old)) {
				$update_news[] = 'ADD `endtime` INT ( 12 ) NULL';
			    }
			    if (!in_array('klicks', $old)) {
				$update_news[] = 'ADD `klicks`  MEDIUMINT ( 9 )  NOT NULL DEFAULT \'0\'';
			    }

			    if (!in_array('news_groups', $old)) {
				$update_news [] = "ADD `news_groups` INT NOT NULL DEFAULT '0' AFTER `news_recht`";
				$sql_statements[] = 'UPDATE `prefix_news` SET `news_recht` = 1023 WHERE `news_recht` = 0';
				$sql_statements[] = 'UPDATE `prefix_news` SET `news_recht` = 1022 WHERE `news_recht` = -1';
				$sql_statements[] = 'UPDATE `prefix_news` SET `news_recht` = 1020 WHERE `news_recht` = -2';
				$sql_statements[] = 'UPDATE `prefix_news` SET `news_recht` = 1016 WHERE `news_recht` = -3';
				$sql_statements[] = 'UPDATE `prefix_news` SET `news_recht` = 1008 WHERE `news_recht` = -4';
				$sql_statements[] = 'UPDATE `prefix_news` SET `news_recht` = 992 WHERE `news_recht` = -5';
				$sql_statements[] = 'UPDATE `prefix_news` SET `news_recht` = 960 WHERE `news_recht` = -6';
				$sql_statements[] = 'UPDATE `prefix_news` SET `news_recht` = 896 WHERE `news_recht` = -7';
				$sql_statements[] = 'UPDATE `prefix_news` SET `news_recht` = 768 WHERE `news_recht` = -8';
				$sql_statements[] = 'UPDATE `prefix_news` SET `news_recht` = 512 WHERE `news_recht` = -9';
			    }

			    if (!empty($update_news)) {
				$sql_statements[] = 'ALTER TABLE `prefix_news` ' . implode(', ', $update_news) . ';';
				$sql_statements[] = 'UPDATE `prefix_news` SET `show` = 1;';
			    }

			    if (db_count_query("SELECT COUNT(*) FROM `prefix_allg` WHERE k = 'news'") == 0) {
				$sql_statements[] = 'INSERT INTO `prefix_allg` ( `k` , `v1`, `v2`, `v3`, `v4`, `v5`, `v6`, `t1` ) VALUES ( "news", "0", "1", "1", "Allgemein", "", "", "" )';
			    }

			    if (in_array('news_html', $old)) {
				$sql_statements[] = 'UPDATE `prefix_news` SET `html` = IF(news_html=\'true\',1,0);';
				$sql_statements[] = 'ALTER TABLE `prefix_news` DROP `news_html`';
			    }
			    // Update f�r 1.1Q.2 - > News Extended Integration - Ende
			    //
			    // Update f�r 1.1Q.3 - > Impressum Update
			    $sql_statements[] = 'UPDATE `prefix_allg` SET `v5` = "meine@mail.de" WHERE `id` = 2 ';
			    // Update f�r 1.1Q.3 - > Impressum Update ENDE
			    // Update f�r 1.1Q.4 -> Datenschutzerkl�rung
			    $qry = db_query('SHOW TABLES LIKE `prefix_datenschutzerklaerung`');
			    if (!$qry) {
				$sql_statements[] = '-- UPDATE 1.1Q Datenschutzerkl�rung';
				$sql_statements[] = 'CREATE TABLE IF NOT EXISTS `prefix_datenschutzerklaerung` (
				    `id` int(5) unsigned NOT NULL auto_increment,
				    `pos`  varchar(2) NOT NULL,
				    `titel`  varchar(300) NOT NULL,
				    `url` varchar(300) NOT NULL,
				    `urltitle` varchar(200) NOT NULL,
				    `txt`  text NOT NULL,
				    `einaus` varchar(1) NOT NULL,
				    PRIMARY KEY  (`id`)
				  )ENGINE = MYISAM;';
				$sql_statements[] = "INSERT INTO `prefix_datenschutzerklaerung` (`id`, `pos`, `titel`, `url`, `urltitle`, `txt`, `einaus`) VALUES (1, '0', 'Datenschutz', 'http://www.e-recht24.de/muster-datenschutzerklaerung.html', 'e-Recht24', '<p>Die Nutzung unserer Webseite ist in der Regel ohne Angabe personenbezogener Daten m�glich. Soweit auf unseren Seiten personenbezogene Daten (beispielsweise Name, Anschrift oder E-Mail-Adressen) erhoben werden, erfolgt dies, soweit m�glich, stets auf freiwilliger Basis. Diese Daten werden ohne Ihre ausdr�ckliche Zustimmung nicht an Dritte weitergegeben.</p><p>Wir weisen darauf hin, dass die Daten�bertragung im Internet (z.B. bei der Kommunikation per E-Mail) Sicherheitsl�cken aufweisen kann. Ein l�ckenloser Schutz der Daten vor dem Zugriff durch Dritte ist nicht m�glich.</p><p>Der Nutzung von im Rahmen der Impressumspflicht ver�ffentlichten Kontaktdaten durch Dritte zur �bersendung von nicht ausdr�cklich angeforderter Werbung und Informationsmaterialien wird hiermit ausdr�cklich widersprochen. Die Betreiber der Seiten behalten sich ausdr�cklich rechtliche Schritte im Falle der unverlangten Zusendung von Werbeinformationen, etwa durch Spam-Mails, vor.</p>', '1')";
				$sql_statements[] = "INSERT INTO `prefix_datenschutzerklaerung` (`id`, `pos`, `titel`, `url`, `urltitle`, `txt`, `einaus`) VALUES (2, '1', 'Datenschutzerkl�rung f�r die Nutzung von Facebook-Plugins (Like-Button)', 'http://www.e-recht24.de/artikel/datenschutz/6590-facebook-like-button-datenschutz-disclaimer.html', 'eRecht24 Facebook Datenschutzerkl�rung', '<p>Auf unseren Seiten sind Plugins des sozialen Netzwerks Facebook, 1601 South California Avenue, Palo Alto, CA 94304, USA integriert. Die Facebook-Plugins erkennen Sie an dem Facebook-Logo oder dem 'Like-Button' ('Gef�llt mir') auf unserer Seite. Eine �bersicht �ber die Facebook-Plugins finden Sie hier: <a href='http://developers.facebook.com/docs/plugins/' target='_blank'>http://developers.facebook.com/docs/plugins/</a>.</p><p>Wenn Sie unsere Seiten besuchen, wird �ber das Plugin eine direkte Verbindung zwischen Ihrem Browser und dem Facebook-Server hergestellt. Facebook erh�lt dadurch die Information, dass Sie mit Ihrer IP-Adresse unsere Seite besucht haben. Wenn Sie den Facebook 'Like-Button' anklicken w�hrend Sie in Ihrem Facebook-Account eingeloggt sind, k�nnen Sie die Inhalte unserer Seiten auf Ihrem Facebook-Profil verlinken. Dadurch kann Facebook den Besuch unserer Seiten Ihrem Benutzerkonto zuordnen.</p><p>Wir weisen darauf hin, dass wir als Anbieter der Seiten keine Kenntnis vom Inhalt der �bermittelten Daten sowie deren Nutzung durch Facebook erhalten.<br />Weitere Informationen hierzu finden Sie in der Datenschutzerkl�rung von facebook unter <a href='http://de-de.facebook.com/policy.php' target='_blank'>http://de-de.facebook.com/policy.php</a>.</p><p>Wenn Sie nicht w�nschen, dass Facebook den Besuch unserer Seiten Ihrem Facebook-Nutzerkonto zuordnen kann, loggen Sie sich bitte aus Ihrem Facebook-Benutzerkonto aus.</p>', '1')";
				$sql_statements[] = "INSERT INTO `prefix_datenschutzerklaerung` (`id`, `pos`, `titel`, `url`, `urltitle`, `txt`, `einaus`) VALUES (3, '2', 'Datenschutzerkl�rung f�r die Nutzung von Google +1', 'https://developers.google.com/+/web/buttons-policy', 'Datenschutzerkl�rung Google +1', '<h4>Erfassung und Weitergabe von Informationen:</h4><p>Mithilfe der Google +1-Schaltfl�che k�nnen Sie Informationen weltweit ver�ffentlichen. �ber die Google +1-Schaltfl�che erhalten Sie und andere Nutzer personalisierte Inhalte von Google und unseren Partnern. Google speichert sowohl die Information, dass Sie f�r einen Inhalt +1 gegeben haben, als auch Informationen �ber die Seite, die Sie beim Klicken auf +1 angesehen haben. Ihre +1 k�nnen als Hinweise zusammen mit Ihrem Profilnamen und Ihrem Foto in Google-Diensten, wie etwa in Suchergebnissen oder in Ihrem Google-Profil, oder an anderen Stellen auf Websites und Anzeigen im Internet eingeblendet werden.</p><p>Google zeichnet Informationen �ber Ihre +1-Aktivit�ten auf, um die Google-Dienste f�r Sie und andere zu verbessern. Um die Google +1-Schaltfl�che verwenden zu k�nnen, ben�tigen Sie ein weltweit sichtbares, �ffentliches Google-Profil, das zumindest den f�r das Profil gew�hlten Namen enthalten muss. Dieser Name wird in allen Google-Diensten verwendet. In manchen F�llen kann dieser Name auch einen anderen Namen ersetzen, den Sie beim Teilen von Inhalten �ber Ihr Google-Konto verwendet haben. Die Identit�t Ihres Google-Profils kann Nutzern angezeigt werden, die Ihre E-Mail-Adresse kennen oder �ber andere identifizierende Informationen von Ihnen verf�gen.</p><h4>Verwendung der erfassten Informationen:</h4><p>Neben den oben erl�uterten Verwendungszwecken werden die von Ihnen bereitgestellten Informationen gem�� den geltenden Google-Datenschutzbestimmungen genutzt. Google ver�ffentlicht m�glicherweise zusammengefasste Statistiken �ber die +1-Aktivit�ten der Nutzer bzw. gibt diese an Nutzer und Partner weiter, wie etwa Publisher, Inserenten oder verbundene Websites.</p>', '1')";
				$sql_statements[] = "INSERT INTO `prefix_datenschutzerklaerung` (`id`, `pos`, `titel`, `url`, `urltitle`, `txt`, `einaus`) VALUES (4, '3', 'Datenschutzerkl�rung f�r die Nutzung von Google Analytics', 'https://support.google.com/analytics/answer/6004245?hl=de', 'Datenschutzerkl�rung f�r Google Analytics', '<p>Diese Website benutzt Google Analytics, einen Webanalysedienst der Google Inc. ('Google'). Google Analytics verwendet sog. 'Cookies', Textdateien, die auf Ihrem Computer gespeichert werden und die eine Analyse der Benutzung der Website durch Sie erm�glichen. Die durch den Cookie erzeugten Informationen �ber Ihre Benutzung dieser Website werden in der Regel an einen Server von Google in den USA �bertragen und dort gespeichert. Im Falle der Aktivierung der IP-Anonymisierung auf dieser Webseite wird Ihre IP-Adresse von Google jedoch innerhalb von Mitgliedstaaten der Europ�ischen Union oder in anderen Vertragsstaaten des Abkommens �ber den Europ�ischen Wirtschaftsraum zuvor gek�rzt.</p><p>Nur in Ausnahmef�llen wird die volle IP-Adresse an einen Server von Google in den USA �bertragen und dort gek�rzt. Im Auftrag des Betreibers dieser Website wird Google diese Informationen benutzen, um Ihre Nutzung der Website auszuwerten, um Reports �ber die Websiteaktivit�ten zusammenzustellen und um weitere mit der Websitenutzung und der Internetnutzung verbundene Dienstleistungen gegen�ber dem Websitebetreiber zu erbringen. Die im Rahmen von Google Analytics von Ihrem Browser �bermittelte IP-Adresse wird nicht mit anderen Daten von Google zusammengef�hrt.<p><p>Sie k�nnen die Speicherung der Cookies durch eine entsprechende Einstellung Ihrer Browser-Software verhindern; wir weisen Sie jedoch darauf hin, dass Sie in diesem Fall gegebenenfalls nicht s�mtliche Funktionen dieser Website vollumf�nglich werden nutzen k�nnen. Sie k�nnen dar�ber hinaus die Erfassung der durch das Cookie erzeugten und auf Ihre Nutzung der Website bezogenen Daten (inkl. Ihrer IP-Adresse) an Google sowie die Verarbeitung dieser Daten durch Google verhindern, indem sie das unter dem folgenden Link verf�gbare Browser-Plugin herunterladen und installieren: <a href='http://tools.google.com/dlpage/gaoptout?hl=de' target='_blank'>http://tools.google.com/dlpage/gaoptout?hl=de</a>.</p>', '0')";
				$sql_statements[] = "INSERT INTO `prefix_datenschutzerklaerung` (`id`, `pos`, `titel`, `url`, `urltitle`, `txt`, `einaus`) VALUES (5, '4', 'Datenschutzerkl�rung f�r die Nutzung von Google Adsense', 'http://www.e-recht24.de/artikel/datenschutz/6635-datenschutz-rechtliche-risiken-bei-der-nutzung-von-google-analytics-und-googleadsense.html', 'Datenschutzerkl�rung f�r Google Adsense', '<p>Diese Website benutzt Google AdSense, einen Dienst zum Einbinden von Werbeanzeigen der Google Inc. ('Google'). Google AdSense verwendet sog. 'Cookies', Textdateien, die auf Ihrem Computer gespeichert werden und die eine Analyse der Benutzung der Website erm�glicht. Google AdSense verwendet auch so genannte Web Beacons (unsichtbare Grafiken). Durch diese Web Beacons k�nnen Informationen wie der Besucherverkehr auf diesen Seiten ausgewertet werden.</p><p>Die durch Cookies und Web Beacons erzeugten Informationen �ber die Benutzung dieser Website (einschlie�lich Ihrer IP-Adresse) und Auslieferung von Werbeformaten werden an einen Server von Google in den USA �bertragen und dort gespeichert. Diese Informationen k�nnen von Google an Vertragspartner von Google weiter gegeben werden. Google wird Ihre IP-Adresse jedoch nicht mit anderen von Ihnen gespeicherten Daten zusammenf�hren.</p><p>Sie k�nnen die Installation der Cookies durch eine entsprechende Einstellung Ihrer Browser Software verhindern; wir weisen Sie jedoch darauf hin, dass Sie in diesem Fall gegebenenfalls nicht s�mtliche Funktionen dieser Website voll umf�nglich nutzen k�nnen. Durch die Nutzung dieser Website erkl�ren Sie sich mit der Bearbeitung der �ber Sie erhobenen Daten durch Google in der zuvor beschriebenen Art und Weise und zu dem zuvor benannten Zweck einverstanden.</p>', '1')";
				$sql_statements[] = "INSERT INTO `prefix_datenschutzerklaerung` (`id`, `pos`, `titel`, `url`, `urltitle`, `txt`, `einaus`) VALUES (6, '5', 'Datenschutzerkl�rung f�r die Nutzung von Twitter', 'https://twitter.com/privacy?lang=de', 'Datenschutzerkl�rung f�r Twitter', '<p>Auf unseren Seiten sind Funktionen des Dienstes Twitter eingebunden. Diese Funktionen werden angeboten durch die Twitter Inc., Twitter, Inc. 1355 Market St, Suite 900, San Francisco, CA 94103, USA. Durch das Benutzen von Twitter und der Funktion 'Re-Tweet' werden die von Ihnen besuchten Webseiten mit Ihrem Twitter-Account verkn�pft und anderen Nutzern bekannt gegeben. Dabei werden auch Daten an Twitter �bertragen.</p><p>Wir weisen darauf hin, dass wir als Anbieter der Seiten keine Kenntnis vom Inhalt der �bermittelten Daten sowie deren Nutzung durch Twitter erhalten. Weitere Informationen hierzu finden Sie in der Datenschutzerkl�rung von Twitter unter <a href='http://twitter.com/privacy' target='_blank'>http://twitter.com/privacy</a>.</p><p>Ihre Datenschutzeinstellungen bei Twitter k�nnen Sie in den Konto-Einstellungen unter <a href='http://twitter.com/account/settings' target='_blank'>http://twitter.com/account/settings</a> �ndern.</p>', '1')";
				$sql_statements[] = "INSERT INTO `prefix_datenschutzerklaerung` (`id`, `pos`, `titel`, `url`, `urltitle`, `txt`, `einaus`) VALUES (7, '6', 'Datenschutzerkl�rung f�r die Nutzung von Piwik', '', '', '<p>Diese Webseite nutzt den Open-Source-Webanalysedienst Piwik. Piwik verwendet sog. 'Cookies', Textdateien, die auf Ihrem Computer gespeichert werden und die eine Analyse der Benutzung der Website durch Sie erm�glicht.</p><p>Auf dieser Webseite  werden die IP-Adressen anonymisiert, so dass kein R�ckschluss auf eine Person m�glich ist. Die von Piwik erfassten Daten werden nicht und niemals auf andere Server �bertragen oder an Dritte weitergegeben, sondern in anonymisierter Form dazu verwendet, unser Angebot zu verbessern. Sie k�nnen die Installation der Cookies durch eine entsprechende Einstellung Ihrer Browser Software unterbinden; Sofern Ihr Browser die 'Do-Not-Track'-Technik unterst�tzt und Sie diese aktiviert haben, wird ihr Besuch automatisch ignoriert.</p><p>Durch die Nutzung dieser Website erkl�ren Sie sich mit der Verarbeitung der �ber Sie erhobenen Daten durch Piwik in der zuvor beschriebenen Art und Weise und zu dem zuvor benannten Zweck einverstanden.</p><p>Weitere Informationen zu Piwik finden Sie unter <a href='http://piwik.org' target='_blank'>http://piwik.org</a></p>', '0')";
				$sql_statements[] = "INSERT INTO `prefix_datenschutzerklaerung` (`id`, `pos`, `titel`, `url`, `urltitle`, `txt`, `einaus`) VALUES (8, '7', 'Auskunft, L�schung, Sperrung', '', '', '<p>Sie haben jederzeit das Recht auf unentgeltliche Auskunft �ber Ihre gespeicherten personenbezogenen Daten, deren Herkunft und Empf�nger und den Zweck der Datenverarbeitung sowie ein Recht auf Berichtigung, Sperrung oder L�schung dieser Daten. Hierzu sowie zu weiteren Fragen zum Thema personenbezogene Daten k�nnen Sie sich jederzeit �ber die im Impressum angegeben Adresse des Webseitenbetreibers an uns wenden.</p>', '0')";

				$sql_statements[] = "INSERT INTO `prefix_modules` (`url` ,`name` ,`gshow` ,`ashow` ,`fright`) VALUES ('datenschutz', 'Datenschutzerkl�rung', '1', '1', '0')";
			    }


			    foreach ($sql_statements as $sql_statement) {
				if (trim($sql_statement) != '') {
				    echo '<pre>' . htmlentities($sql_statement, ENT_COMPAT, 'ISO-8859-1') . '</pre>';
				    $e = db_query($sql_statement);
				    echo db_error();
				    if (!$e) {
					echo '<br><br><span style="color:#ff0000; font-weight: bold" color="#FF0000">Es ist ein Fehler aufgetreten</span>,<br> bitte alles auf dieser Seite kopieren und auf ilch.de im Forum fragen...:<br><br><div style="border: 1px dashed grey; padding: 5px; background-color: #EEEEEE">' . db_error() . '<hr>' . $sql_statement . '</div><br><b>Es sei denn,</b> es ist ein Fehler mit <i>duplicate entry</i> aufgetreten, das liegt einfach nur daran, dass du die Updatedatei mehrmals ausgef?hrt hast.<br>';
				    }
				    echo '<hr>';
				}
			    }
			    echo '<br><br>Wenn keine Fehler aufgetreten sind, sollte die Installation ohne Probleme verlaufen sein und du solltest die update.php nun vom Webspace l&ouml;schen.<br>';
			}
			?>
                    </div>
                    <div class="installbut">
                    </div>
                </div>
                <div class="installfoot">
                    <a href="http://www.ilch.de" title="&copy; Ilch.de - Content Management System">&copy;&nbsp;Ilch.de - Content Management System</a>
                </div>
        </form>
    </body>
</html>