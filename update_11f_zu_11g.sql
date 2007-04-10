ALTER TABLE `prefix_usercheck` ADD `groupid` TINYINT NOT NULL ;
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('joinus_rules', 'r2', 'Team Optionen', 'Regeln bei Joinus vollst&auml;ndig anzeigen?', '0');
UPDATE `prefix_config` SET `frage` = 'Standard Absender bei eMails' WHERE `schl` = 'allg_default_subject' LIMIT 1;
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('groups_forall', 'r2', 'Team Optionen', 'Modulrecht <i>Gruppen</i> auf eigene Gruppe beschr&auml;nken?', '1');