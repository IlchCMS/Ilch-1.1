ALTER TABLE `prefix_forumcats` ADD `cid` TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER `id`;
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('sb_maxwordlength', 'input', 'Shoutbox Optionen', 'Maximale Wortl&auml;nge in der Shoutbox', '10');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('sb_recht', 'grecht2', 'Shoutbox Optionen', 'Schreiben in der Shoutbox ab?', '0');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('sb_limit', 'input', 'Shoutbox Optionen', 'Anzahl angezeigter Nachrichten', '5');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('antispam_shoutbox', 'grecht', 'Shoutbox Optionen', 'Antispam <small>(ab diesem Recht keine Eingabe mehr erforderlich)</small>', '-7');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('antispam_newskom', 'grecht', 'News Optionen', 'Antispam <small>(ab diesem Recht keine Eingabe mehr erforderlich)</small>', '-7');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('antispam_gbook', 'grecht', 'G&auml;stebuch Optionen', 'Antispam <small>(ab diesem Recht keine Eingabe mehr erforderlich)</small>', '-7');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('antispam_gbookkom', 'grecht', 'G&auml;stebuch Optionen', 'Kommentar-Antispam <small>(ab diesem Recht keine Eingabe mehr erforderlich)</small>', '-7');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('antispam_joinus', 'grecht', 'Team Optionen', 'Joinus-Antispam <small>(ab diesem Recht keine Eingabe mehr erforderlich)</small>', '-7');
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('antispam_fightus', 'grecht', 'Team Optionen', 'Fightus-Antispam <small>(ab diesem Recht keine Eingabe mehr erforderlich)</small>', '-7');
