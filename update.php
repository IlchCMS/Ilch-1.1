<?php
#   Copyright by: Manuel
#   Support: www.ilch.de

$readme = <<<README
Changelog:
==========
+ neue Funktionen       * Änderungen/Bugfixes

Version 1.1 P
-------------
* Kompatibilität zu PHP 5.3 und 5.4 verbessert
* Passwordhashmethod verbessert (sicherere Passwörter in der Datenbank)
* verbesserte Antispam-Methode eingebunden
* einige Absicherungen gegen CSRF Attacken
README;

$rows = substr_count($readme, "\n");
if ($rows > 45) $rows = 45;
?>
<html>
<head><title>... ::: [ U p d a t e f &uuml; r &nbsp; i l c h C l a n  &nbsp; 1 . 1 P] ::: ...</title>
<link rel="stylesheet" href="include/designs/ilchClan/style.css" type="text/css">
</head>
<body>

<form method="post">
		<table width="70%" class="border" border="0" cellspacing="0" cellpadding="25" align="center">
      <tr><th class="Chead" align="center">... ::: [ U p d a t e f &uuml; r &nbsp; i l c h C l a n  &nbsp; 1 . 1 P</u>] ::: ...</th></tr>
      <tr>
        <td class="Cmite">
<?php
if ( empty ($_POST['step']) ) {
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

    define ( 'main' , TRUE );
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
	while ($r = db_fetch_assoc($qry)){
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
	while ($r = db_fetch_assoc($qry)){
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
	while ($r = db_fetch_assoc($qry)){
		$old[] = $r['Field'];
	}
	if (!in_array('pos', $old)) {
		$sql_statements[] = '-- UPDATE 1.1I';
		$sql_statements[] = "ALTER TABLE `prefix_config` ADD `pos` SMALLINT(6) NOT NULL default '0'";
		$sql_statements[] = "INSERT INTO `prefix_config` (`schl`, `typ`, `kat`, `frage`, `wert`, `pos`) VALUES('mail_smtp', 'r2', 'Allgemeine Optionen', 'SMTP für den Mailversand verwenden? <a href=\"admin.php?smtpconf\" class=\"smalfont\">weitere Einstellungen</a>', '0', 0)";
	}

	//Update 1.1n
    if (db_count_query("SELECT COUNT(*) FROM `prefix_allg` WHERE k = 'smtpconf'") == 0) {
    	$smtp = array('smtp_host' => '', 'smtp_port' => '', 'smtp_auth' => 'auth', 'smtp_pop3beforesmtp' => '', 'smtp_pop3host' => '',
    	'smtp_pop3port' => '', 'smtp_login' => '', 'smtp_email' => '', 'smtp_login' => '', 'smtp_pass' => '', 'smtp_changesubject' => '1');

		$qry = db_query('SELECT * FROM `prefix_config` WHERE `schl` LIKE "mail_%"');
		while ($r = db_fetch_assoc($qry)){
    		switch($r['schl']){
    			case 'mail_smtp_login':		$smtp['smtp_login'] = $r['wert']; break;
    			case 'mail_smtp_password': 	$smtp['smtp_pass']  = $r['wert']; break;
    			case 'mail_smtp_host': 		$smtp['smtp_host']  = $r['wert']; break;
    			case 'mail_smtp_email': 	$smtp['smtp_email'] = $r['wert']; break;
    		}
    	}
		$smtpser = mysql_real_escape_string(serialize($smtp));
    	$sql_statements[] = '-- UPDATE 1.1N';
		$sql_statements[] = 'INSERT INTO `prefix_allg` ( `k` , `v1`, `v2`, `v3`, `v4`, `t1`) VALUES ( "smtpconf", "", "", "", "", "' . $smtpser . '" )';
    	$sql_statements[] = 'DELETE FROM `prefix_config` WHERE `schl` IN ("mail_smtp_login", "mail_smtp_password", "mail_smtp_host", "mail_smtp_email")';
    	$sql_statements[] = 'UPDATE `prefix_config` SET `kat` = "Allgemeine Optionen", `frage` = "SMTP für den Mailversand verwenden? <a href=\"admin.php?smtpconf\" class=\"smalfont\">weitere Einstellungen</a>" WHERE `schl` = "mail_smtp"';
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

    foreach ( $sql_statements as $sql_statement ) {
        if ( trim($sql_statement) != '' ) {
            echo '<pre>'.htmlentities($sql_statement, ENT_COMPAT, 'ISO-8859-1').'</pre>';
            $e = db_query($sql_statement);
            if (!$e) {
                echo '<font color="#FF0000"><b>Es ist ein Fehler aufgetreten</b></font>, bitte alles auf dieser Seite kopieren und auf ilch.de im Forum fragen...:<div style="border: 1px dashed grey; padding: 5px; background-color: #EEEEEE">'. mysql_error().'<hr>'.$sql_statement.'</div><br /><b>Es sei denn,</b> es ist ein Fehler mit <i>duplicate entry</i> aufgetreten, das liegt einfach nur daran, dass du die Updatedatei mehrmals ausgeführt hast.<br />';
            }
            echo '<hr>';
        }
    }
    echo '<br /><br />Wenn keine Fehler aufgetreten sind, sollte die Installation ohne Probleme verlaufen sein und du solltest die update.php nun vom Webspace l&ouml;schen.';

}
?>
</td></tr></table>
</form>
</body>
</html>