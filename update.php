<?php
#   Copyright by: Manuel
#   Support: www.ilch.de

$readme = <<<README
Changelog:
==========
+ neue Funktionen       * Aenderungen/Bugfixes

Version 1.1 Q
-------------
* Sicherheitsupdates im Bezug auf mögliche SQL-Injections
* Integration SSL / TLS beim Mail-Versand (Transportverschlüsselung)
* PHP Versionscheck während der Installation
* Bugfix Forum verschieben
* Korrektur ungültiger Links
* Kontakt Formular improved --> Absenden des Formulars nur dann, wenn alle felder gefüllt sind.
+ Integration des BBCode
+ Integration von News Extended
+ Komplette Überarbeitung des Backends -- Integration Bootstrap
+ Frontend Templates alle tabellen durch divs ersetzt
README;

$rows = substr_count($readme, "\n");
if ($rows > 45)
    $rows = 45;
?>
<html>
    <head><title>... ::: [ U p d a t e f &uuml; r &nbsp; i l c h C l a n  &nbsp; 1 . 1 Q ] ::: ...</title>
        <link rel="stylesheet" href="include/designs/ilchClan/style.css" type="text/css">
    </head>
    <body>

        <form method="post">
            <table width="70%" class="border" border="0" cellspacing="0" cellpadding="25" align="center">
                <tr><th class="Chead" align="center">... ::: [ U p d a t e f &uuml; r &nbsp; i l c h C l a n  &nbsp; 1 . 1 Q] ::: ...</th></tr>
                <tr>
                    <td class="Cmite">
			<?php
			if (empty($_POST['step'])) {
			    ?>
    			<div align="center">
    			    <h2>Readme</h2>
    			    <textarea cols="120" rows="<?php echo $rows; ?>"><?php echo htmlentities($readme, ENT_COMPAT, 'ISO-8859-1'); ?></textarea><br /><br />

    			    <br /><br />
    			    Dieses Script soll die n&ouml;tigen Datanbank&auml;ndernungen f&uuml;r das Update machen<br />
    			    <br />
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
			    echo mysql_error();
			    while ($r = db_fetch_assoc($qry)) {
				echo mysql_error();
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
				$smtpser = mysql_real_escape_string(serialize($smtp));
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

			    // Update für 1.1Q.2 - > News Extended Integration
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
			    // Update für 1.1Q.2 - > News Extended Integration - Ende
			    //
			    // Update für 1.1Q.3 - > Impressum Update
			    $sql_statements[] = 'UPDATE `prefix_allg` SET `v5` = "meine@mail.de" WHERE `id` = 2 ';
			    // Update für 1.1Q.3 - > Impressum Update ENDE

			    foreach ($sql_statements as $sql_statement) {
				if (trim($sql_statement) != '') {
				    echo '<pre>' . htmlentities($sql_statement, ENT_COMPAT, 'ISO-8859-1') . '</pre>';
				    $e = db_query($sql_statement);
				    echo mysql_error();
				    if (!$e) {
					echo '<span style="color:#ff0000; font-weight: bold" color="#FF0000">Es ist ein Fehler aufgetreten</span>, bitte alles auf dieser Seite kopieren und auf ilch.de im Forum fragen...:<div style="border: 1px dashed grey; padding: 5px; background-color: #EEEEEE">' . mysql_error() . '<hr>' . $sql_statement . '</div><br /><b>Es sei denn,</b> es ist ein Fehler mit <i>duplicate entry</i> aufgetreten, das liegt einfach nur daran, dass du die Updatedatei mehrmals ausgef?hrt hast.<br />';
				    }
				    echo '<hr>';
				}
			    }
			    echo '<br /><br />Wenn keine Fehler aufgetreten sind, sollte die Installation ohne Probleme verlaufen sein und du solltest die update.php nun vom Webspace l&ouml;schen.';
			}
			?>
                    </td>
		</tr>
	    </table>
        </form>
    </body>
</html>