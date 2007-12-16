ALTER TABLE `prefix_config` ADD `pos` SMALLINT(6) NOT NULL default '0';
INSERT INTO `prefix_config` (`schl`, `typ`, `kat`, `frage`, `wert`, `pos`) VALUES('mail_smtp', 'r2', 'Mail Optionen', 'Soll anstatt der PHP Funktion mail() versucht werden, den angegeben SMTP Server zum Versenden von Mails zu benutzen?', '0', 0);
INSERT INTO `prefix_config` (`schl`, `typ`, `kat`, `frage`, `wert`, `pos`) VALUES('mail_smtp_login', 'input', 'Mail Optionen', 'SMTP Benutzername', '', 3);
INSERT INTO `prefix_config` (`schl`, `typ`, `kat`, `frage`, `wert`, `pos`) VALUES('mail_smtp_password', 'password', 'Mail Optionen', 'SMTP Passwort', '', 4);
INSERT INTO `prefix_config` (`schl`, `typ`, `kat`, `frage`, `wert`, `pos`) VALUES('mail_smtp_host', 'input', 'Mail Optionen', 'Hostadresse des SMTP', '', 1);
INSERT INTO `prefix_config` (`schl`, `typ`, `kat`, `frage`, `wert`, `pos`) VALUES('mail_smtp_email', 'input', 'Mail Optionen', 'E-Mail-Adresse des Accounts', '', 2);
