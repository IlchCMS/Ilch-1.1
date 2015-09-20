<?php
#   Copyright by Thomas Bowe [Funjoy]
#   Support bbcode@phpline.de
#   link www.phpline.de
//> Hier werden die Einstellungen festgelegt.
##############################################################################################################
//> Verbindung mit der Datenbank herstellen.
	db_connect ();
	//> Buttons Informationen.
	$ButtonSql = db_query("SELECT * FROM prefix_bbcode_buttons WHERE fnButtonNr='1'");
	$boolButton = db_fetch_assoc($ButtonSql);
	
	//> Design Informationen.
	$DesignSql = db_query("SELECT * FROM prefix_bbcode_design WHERE fnDesignNr='1'");
	$strDesign = db_fetch_assoc($DesignSql);
	
	//> Config Informationen.
	$ConfigSql = db_query("SELECT * FROM prefix_bbcode_config WHERE fnConfigNr='1'");
	$objConfig = db_fetch_assoc($ConfigSql);
#-------------------------------------------------------------------------------------------------------------
//> Konfiguration f�r Zitat- Funktion (Quote)
	$info['QuoteRandFarbe'] = $strDesign['fcQuoteRandFarbe'];						#--> Rand (Border) Farbe.
	$info['QuoteTabelleBreite'] = $strDesign['fcQuoteTabelleBreite'];				#--> Tabellenbreite.
	$info['QuoteSchriftfarbe'] = $strDesign['fcQuoteSchriftfarbe'];					#--> Schriftfarbe f�r die �berschrift.
	$info['QuoteHintergrundfarbe'] = $strDesign['fcQuoteHintergrundfarbe'];			#--> Hintergrundfarbe f�r die �berschrift.
	$info['QuoteHintergrundfarbeIT'] = $strDesign['fcQuoteHintergrundfarbeIT'];		#--> Hintergrundfabre f�r den Inhalt.
	$info['QuoteSchriftformatIT'] = $strDesign['fcQuoteSchriftformatIT'];			#--> Font-Style f�r den Inhalt. (Italic = Kursiv)
	$info['QuoteSchriftfarbeIT'] = $strDesign['fcQuoteSchriftfarbeIT'];				#--> Schriftfarbe f�r den Inhalt.
#-------------------------------------------------------------------------------------------------------------
//> Konfiguration f�r die Code- Blocks (Code, Html CSS und PHP)
	$info['BlockRandFarbe'] = $strDesign['fcBlockRandFarbe'];						#--> Rand (Border) Farbe.
	$info['BlockTabelleBreite'] = $strDesign['fcBlockTabelleBreite'];				#--> Tabellenbreite.
	$info['BlockSchriftfarbe'] = $strDesign['fcBlockSchriftfarbe'];					#--> Schriftfarbe f�r die �berschrift.
	$info['BlockHintergrundfarbe'] = $strDesign['fcBlockHintergrundfarbe'];			#--> Hintergrundfarbe f�r die �berschrift.
	$info['BlockHintergrundfarbeIT'] = $strDesign['fcBlockHintergrundfarbeIT'];		#--> Hintergrundfabre f�r den Inhalt.
	$info['BlockCodeFarbe'] = $strDesign['fcBlockSchriftfarbeIT'];					#--> Code Farbe. (Nur f�r [Code] !!!)
#-------------------------------------------------------------------------------------------------------------	
//> Konfiguration f�r die Klappfunktion
	$info['KtextRandFarbe'] = $strDesign['fcKtextRandFarbe'];						#--> Rand (Border) Farbe.
	$info['KtextTabelleBreite'] = $strDesign['fcKtextTabelleBreite'];				#--> Tabellenbreite.
	$info['KtextRandFormat'] = $strDesign['fcKtextRandFormat'];						#--> Rand Format (Dotted,Dashed,Solid)
#-------------------------------------------------------------------------------------------------------------	
//> Konfiguration f�r die Hervorhebung eines Textes
	$info['EmphHintergrundfarbe'] = $strDesign['fcEmphHintergrundfarbe'];			#--> Hintergrundfarbe um den Text.
	$info['EmphSchriftfarbe'] = $strDesign['fcEmphSchriftfarbe'];					#--> Schriftfarbe f�r den Text
#-------------------------------------------------------------------------------------------------------------		
//> Konfiguration f�r die Videos von Youtube
	$info['YoutubeBreite'] = $objConfig['fnYoutubeBreite'];							#--> Breite der Videos.
	$info['YoutubeHoehe'] = $objConfig['fnYoutubeHoehe'];							#--> H�he der Videos.
	$info['YoutubeHintergrundfarbe'] = $objConfig['fcYoutubeHintergrundfarbe'];		#--> Hintergrundfarbe.
#-------------------------------------------------------------------------------------------------------------
//> Konfiguration f�r die Videos von Google
	$info['GoogleBreite'] = $objConfig['fnGoogleBreite'];							#--> Breite des Videos.
	$info['GoogleHoehe'] = $objConfig['fnGoogleHoehe'];								#--> Hoehe des Videos.
	$info['GoogleHintergrundfarbe'] = $objConfig['fcGoogleHintergrundfarbe'];		#--> Hintergurndfarbe.
#-------------------------------------------------------------------------------------------------------------
//> Konfiguration f�r die Videosvon MyVideo
	$info['MyvideoBreite'] = $objConfig['fnMyvideoBreite'];							#--> Breite des Videos.
	$info['MyvideoHoehe'] = $objConfig['fnMyvideoHoehe'];							#--> Hoehe des Videos.
	$info['MyvideoHintergrundfarbe'] = $objConfig['fcMyvideoHintergrundfarbe'];		#--> Hintergurndfarbe.
#-------------------------------------------------------------------------------------------------------------	
//> Konfiguration f�r die Videosvon MyVideo
	$info['FlashBreite'] = $objConfig['fnFlashBreite'];							#--> Breite des Videos.
	$info['FlashHoehe'] = $objConfig['fnFlashHoehe'];							#--> Hoehe des Videos.
	$info['FlashHintergrundfarbe'] = $objConfig['fcFlashHintergrundfarbe'];		#--> Hintergurndfarbe.
#-------------------------------------------------------------------------------------------------------------
//> Konfiguration der Schriftgr��e
	$info['SizeMax'] = $objConfig['fnSizeMax'];										#--> Maximale Schriftgr��e in Pixel.
#-------------------------------------------------------------------------------------------------------------
//> Konfiguration f�r Bilder
	$info['ImgMaxBreite'] = $objConfig['fnImgMaxBreite'];							#--> Maximale breite des Bildes.
	$info['ImgMaxHoehe'] = $objConfig['fnImgMaxHoehe'];								#--> Maximale H�he des Bildes.
#-------------------------------------------------------------------------------------------------------------
//> Konfiguration f�r Screenshots
	$info['ScreenMaxBreite'] = $objConfig['fnScreenMaxBreite'];						#--> Maximale breite des Bildes.
	$info['ScreenMaxHoehe'] = $objConfig['fnScreenMaxHoehe'];						#--> Maximale H�he des Bildes.
#-------------------------------------------------------------------------------------------------------------
//> Konfiguration von Textl�ngen
	$info['UrlMaxLaenge'] = $objConfig['fnUrlMaxLaenge'];							#--> Maiximale L�nge des Links.
	$info['WortMaxLaenge'] = $objConfig['fnWortMaxLaenge'];							#--> Maxiamle L�nge eines Wortes (Muss l�nger sein wie Url/Links)
#-------------------------------------------------------------------------------------------------------------
//> Konfiguration von Countdown
	$info['CountdownRandFarbe'] = $strDesign['fcCountdownRandFarbe'];				#--> Rand (Border) Farbe.
	$info['CountdownTabelleBreite'] = $strDesign['fcCountdownTabelleBreite'];		#--> Tabellenbreite.
	$info['CountdownSchriftfarbe'] = $strDesign['fcCountdownSchriftfarbe'];			#--> Schriftfarbe.
	$info['CountdownSchriftformat'] = $strDesign['fcCountdownSchriftformat'];		#--> Schriftformat.
	$info['CountdownSchriftsize'] = $strDesign['fnCountdownSchriftsize'];			#--> Schriftgr��e.
	

#-------------------------------------------------------------------------------------------------------------
//> Smileys umwandeln ja oder nein?
	$permitted['smileys'] = $boolButton['fnFormatSmilies'];

	//> Schrift formatierung erlauben?
	//> Fett?
	$permitted['b'] = $boolButton['fnFormatB'];
	
	//> Kursiv?
	$permitted['i'] = $boolButton['fnFormatI']; 
	
	//> Unterstrichen?
	$permitted['u'] = $boolButton['fnFormatU']; 
	
	//> Durchgestrichen?
	$permitted['s'] = $boolButton['fnFormatS']; 
	
	#>--------------------------------<#
	
	//> Ausrichtung des Textes erlauben?
	//> Links?
	$permitted['left'] = $boolButton['fnFormatLeft']; 
	
	//> Mitte?
	$permitted['center'] = $boolButton['fnFormatCenter']; 
	
	//> Rechts?
	$permitted['right'] = $boolButton['fnFormatRight']; 

	//> Block?
	$permitted['block'] = $boolButton['fnFormatBlock']; 
	
	#>--------------------------------<#
	
	//> Sonstige Text formatierungen.
	//> Liste?
	$permitted['list'] = $boolButton['fnFormatList'];
	
	//> Text hervorheben?
	$permitted['emph'] = $boolButton['fnFormatEmph'];
	
	//> Textfarbe?
	$permitted['color'] = $boolButton['fnFormatColor'];
	
	//> Schriftgr��e?
	$permitted['size'] = $boolButton['fnFormatSize'];
	
	//> Klapptext?
	$permitted['ktext'] = $boolButton['fnFormatKtext'];
	
	#>--------------------------------<#
	
	//> Url verlinken?
	$permitted['url'] = $boolButton['fnFormatUrl'];
	
	//> Url automatisch verlinken?
	$permitted['autourl'] = $boolButton['fnFormatUrlAuto'];
	
	//> Email Erlauben?
	$permitted['email'] = $boolButton['fnFormatEmail'];
	
	//> Bild darstellen?
	$permitted['img'] = $boolButton['fnFormatImg'];
	
	//> Screenshots darstellen?
	$permitted['screenshot'] = $boolButton['fnFormatScreen'];
	
	//> Videos darstellen?
    $permitted['video'] = $boolButton['fnFormatVideo'];
	
	//> Videos darstellen?
    $permitted['flash'] = $boolButton['fnFormatFlash'];
	
	//> Countdowns erzeugen?
	$permitted['countdown'] = $boolButton['fnFormatCountdown'];
    
	
	#>--------------------------------<#
	
	//> Sonstige Tags erlauben?
	//> Tag: [php]
	$permitted['php'] = $boolButton['fnFormatPhp'];
	
	//> Tag: [css]
	$permitted['css'] = $boolButton['fnFormatCss'];
	
	//> Tag: [html]
	$permitted['html'] = $boolButton['fnFormatHtml'];
	
	//> Tag: [code]
	$permitted['code'] = $boolButton['fnFormatCode'];
	
	//> Tag: [quote]
	$permitted['quote'] = $boolButton['fnFormatQuote'];
	
	#>--------------------------------<#
?>
