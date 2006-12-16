ALTER TABLE `prefix_gallery_cats` CHANGE `besch` `besch` TEXT NOT NULL;
ALTER TABLE `prefix_warmaps` CHANGE `opp` `opp` MEDIUMINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_warmaps` CHANGE `owp` `owp` MEDIUMINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_wars` CHANGE `opp` `opp` MEDIUMINT NOT NULL DEFAULT '0';
ALTER TABLE `prefix_wars` CHANGE `owp` `owp` MEDIUMINT NOT NULL DEFAULT '0';
INSERT INTO `prefix_config` ( `schl` , `typ` , `kat` , `frage` , `wert` ) VALUES ('allg_default_subject', 'input', 'Allgemeine Optionen', 'Standard Betreff bei eMails', 'automatische eMail');  