<?php
//   Copyright by Manuel Staechele
//   Support www.ilch.de

defined('main') or die ('no direct access');

/**
 * Erstellt Farben für die getBBCodeButtons Funktion
 *
 * @param string[] $colors
 * @return string
 */
function getBBCodeColorList(array $colors)
{
    $l = '';
    foreach ($colors as $k => $v) {
        $l .= '<td class="tdweight10px" style="background-color: ' . $k . ';"><a href="javascript:bbcode_code_insert(\'color\',\'' . $k . '\'); hide_color();"><img src="include/images/icons/bbcode/transparent.gif" class="bbcodetransparentgif"  alt="' . $v . '" title="' . $v . '"></td>';
    }
    return ($l);
}

/**
 * Loads the BBCode Configuration from the database
 * @return array
 */
function getBBCodeConfig()
{
    static $permitted, $info;
    if (!empty($permitted)) {
        return array($permitted, $info);
    }

    $ButtonSql = db_query("SELECT * FROM prefix_bbcode_buttons WHERE fnButtonNr='1'");
    $boolButton = db_fetch_assoc($ButtonSql);

    //> Design Informationen.
    $DesignSql = db_query("SELECT * FROM prefix_bbcode_design WHERE fnDesignNr='1'");
    $strDesign = db_fetch_assoc($DesignSql);

    //> Config Informationen.
    $ConfigSql = db_query("SELECT * FROM prefix_bbcode_config WHERE fnConfigNr='1'");
    $objConfig = db_fetch_assoc($ConfigSql);
    #-------------------------------------------------------------------------------------------------------------
    //> Konfiguration für Zitat- Funktion (Quote)
    $info['QuoteRandFarbe'] = $strDesign['fcQuoteRandFarbe'];						#--> Rand (Border) Farbe.
    $info['QuoteTabelleBreite'] = $strDesign['fcQuoteTabelleBreite'];				#--> Tabellenbreite.
    $info['QuoteSchriftfarbe'] = $strDesign['fcQuoteSchriftfarbe'];					#--> Schriftfarbe für die Überschrift.
    $info['QuoteHintergrundfarbe'] = $strDesign['fcQuoteHintergrundfarbe'];			#--> Hintergrundfarbe für die Überschrift.
    $info['QuoteHintergrundfarbeIT'] = $strDesign['fcQuoteHintergrundfarbeIT'];		#--> Hintergrundfabre für den Inhalt.
    $info['QuoteSchriftformatIT'] = $strDesign['fcQuoteSchriftformatIT'];			#--> Font-Style für den Inhalt. (Italic = Kursiv)
    $info['QuoteSchriftfarbeIT'] = $strDesign['fcQuoteSchriftfarbeIT'];				#--> Schriftfarbe für den Inhalt.
    #-------------------------------------------------------------------------------------------------------------
    //> Konfiguration für die Code- Blocks (Code, Html CSS und PHP)
    $info['BlockRandFarbe'] = $strDesign['fcBlockRandFarbe'];						#--> Rand (Border) Farbe.
    $info['BlockTabelleBreite'] = $strDesign['fcBlockTabelleBreite'];				#--> Tabellenbreite.
    $info['BlockSchriftfarbe'] = $strDesign['fcBlockSchriftfarbe'];					#--> Schriftfarbe für die Überschrift.
    $info['BlockHintergrundfarbe'] = $strDesign['fcBlockHintergrundfarbe'];			#--> Hintergrundfarbe für die Überschrift.
    $info['BlockHintergrundfarbeIT'] = $strDesign['fcBlockHintergrundfarbeIT'];		#--> Hintergrundfabre für den Inhalt.
    $info['BlockCodeFarbe'] = $strDesign['fcBlockSchriftfarbeIT'];					#--> Code Farbe. (Nur für [Code] !!!)
    #-------------------------------------------------------------------------------------------------------------
    //> Konfiguration für die Klappfunktion
    $info['KtextRandFarbe'] = $strDesign['fcKtextRandFarbe'];						#--> Rand (Border) Farbe.
    $info['KtextTabelleBreite'] = $strDesign['fcKtextTabelleBreite'];				#--> Tabellenbreite.
    $info['KtextRandFormat'] = $strDesign['fcKtextRandFormat'];						#--> Rand Format (Dotted,Dashed,Solid)
    #-------------------------------------------------------------------------------------------------------------
    //> Konfiguration für die Hervorhebung eines Textes
    $info['EmphHintergrundfarbe'] = $strDesign['fcEmphHintergrundfarbe'];			#--> Hintergrundfarbe um den Text.
    $info['EmphSchriftfarbe'] = $strDesign['fcEmphSchriftfarbe'];					#--> Schriftfarbe für den Text
    #-------------------------------------------------------------------------------------------------------------
    //> Konfiguration für die Videos von Youtube
    $info['YoutubeBreite'] = $objConfig['fnYoutubeBreite'];							#--> Breite der Videos.
    $info['YoutubeHoehe'] = $objConfig['fnYoutubeHoehe'];							#--> Höhe der Videos.
    $info['YoutubeHintergrundfarbe'] = $objConfig['fcYoutubeHintergrundfarbe'];		#--> Hintergrundfarbe.
    #-------------------------------------------------------------------------------------------------------------
    //> Konfiguration für die Videos von Google
    $info['GoogleBreite'] = $objConfig['fnGoogleBreite'];							#--> Breite des Videos.
    $info['GoogleHoehe'] = $objConfig['fnGoogleHoehe'];								#--> Hoehe des Videos.
    $info['GoogleHintergrundfarbe'] = $objConfig['fcGoogleHintergrundfarbe'];		#--> Hintergurndfarbe.
    #-------------------------------------------------------------------------------------------------------------
    //> Konfiguration für die Videosvon MyVideo
    $info['MyvideoBreite'] = $objConfig['fnMyvideoBreite'];							#--> Breite des Videos.
    $info['MyvideoHoehe'] = $objConfig['fnMyvideoHoehe'];							#--> Hoehe des Videos.
    $info['MyvideoHintergrundfarbe'] = $objConfig['fcMyvideoHintergrundfarbe'];		#--> Hintergurndfarbe.
    #-------------------------------------------------------------------------------------------------------------
    //> Konfiguration für die Videosvon MyVideo
    $info['FlashBreite'] = $objConfig['fnFlashBreite'];							#--> Breite des Videos.
    $info['FlashHoehe'] = $objConfig['fnFlashHoehe'];							#--> Hoehe des Videos.
    $info['FlashHintergrundfarbe'] = $objConfig['fcFlashHintergrundfarbe'];		#--> Hintergurndfarbe.
    #-------------------------------------------------------------------------------------------------------------
    //> Konfiguration der Schriftgröße
    $info['SizeMax'] = $objConfig['fnSizeMax'];										#--> Maximale Schriftgröße in Pixel.
    #-------------------------------------------------------------------------------------------------------------
    //> Konfiguration für Bilder
    $info['ImgMaxBreite'] = $objConfig['fnImgMaxBreite'];							#--> Maximale breite des Bildes.
    $info['ImgMaxHoehe'] = $objConfig['fnImgMaxHoehe'];								#--> Maximale Höhe des Bildes.
    #-------------------------------------------------------------------------------------------------------------
    //> Konfiguration für Screenshots
    $info['ScreenMaxBreite'] = $objConfig['fnScreenMaxBreite'];						#--> Maximale breite des Bildes.
    $info['ScreenMaxHoehe'] = $objConfig['fnScreenMaxHoehe'];						#--> Maximale Höhe des Bildes.
    #-------------------------------------------------------------------------------------------------------------
    //> Konfiguration von Textlängen
    $info['UrlMaxLaenge'] = $objConfig['fnUrlMaxLaenge'];							#--> Maiximale Länge des Links.
    $info['WortMaxLaenge'] = $objConfig['fnWortMaxLaenge'];							#--> Maxiamle Länge eines Wortes (Muss länger sein wie Url/Links)
    #-------------------------------------------------------------------------------------------------------------
    //> Konfiguration von Countdown
    $info['CountdownRandFarbe'] = $strDesign['fcCountdownRandFarbe'];				#--> Rand (Border) Farbe.
    $info['CountdownTabelleBreite'] = $strDesign['fcCountdownTabelleBreite'];		#--> Tabellenbreite.
    $info['CountdownSchriftfarbe'] = $strDesign['fcCountdownSchriftfarbe'];			#--> Schriftfarbe.
    $info['CountdownSchriftformat'] = $strDesign['fcCountdownSchriftformat'];		#--> Schriftformat.
    $info['CountdownSchriftsize'] = $strDesign['fnCountdownSchriftsize'];			#--> Schriftgröße.


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

    //> Schriftgröße?
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

    return array($permitted, $info);
}

/**
 * Get HTML for BBCode Buttons
 * @return string
 */
function getBBCodeButtons()
{
    global $ILCH_BODYEND_ADDITIONS;
    static $jsLoaded;

    $BBCodeButtons = '';
    if (!isset($jsLoaded)) {
        $ILCH_BODYEND_ADDITIONS .= '<script type="text/javascript" src="include/includes/js/interface.js"></script>';
        $jsLoaded = true;
    }

    list($permitted, $info) = getBBCodeConfig();

    //> Fett Button!
    if ($permitted['b']) {
        $BBCodeButtons .= "<a href=\"javascript:bbcode_insert('b','Gib hier den Text an der fett formatiert werden soll.')\"><img src=\"include/images/icons/bbcode/bbcode_bold.png\" alt=\"Fett formatieren\" title=\"Fett formatieren\" class=\"ilchbbcodebuttons\"></a> ";
    }

    //> Kursiv Button!
    if ($permitted['i']) {
        $BBCodeButtons .= "<a href=\"javascript:bbcode_insert('i','Gib hier den Text an der kursiv formatiert werden soll.')\"><img src=\"include/images/icons/bbcode/bbcode_italic.png\" alt=\"Kursiv formatieren\" title=\"Kursiv formatieren\" class=\"ilchbbcodebuttons\"></a> ";
    }

    //> Unterschrieben Button!
    if ($permitted['u']) {
        $BBCodeButtons .= "<a href=\"javascript:bbcode_insert('u','Gib hier den Text an der unterstrichen formatiert werden soll.')\"><img src=\"include/images/icons/bbcode/bbcode_underline.png\" alt=\"Unterstrichen formatieren\" title=\"Unterstrichen formatieren\" class=\"ilchbbcodebuttons\"></a> ";
    }

    //> Durchgestrichener Button!
    if ($permitted['s']) {
        $BBCodeButtons .= "<a href=\"javascript:bbcode_insert('s','Gib hier den Text an der durchgestrichen formatiert werden soll..')\"><img src=\"include/images/icons/bbcode/bbcode_strike.png\" alt=\"Durchgestrichen formatieren\" title=\"Durchgestrichen formatieren\" class=\"ilchbbcodebuttons\"></a> ";
    }

    //> Leerzeichen?
    if ($permitted['b'] || $permitted['i'] || $permitted['u'] || $permitted['s']) {
        $BBCodeButtons .= "&nbsp;";
    }

    //> Links Button!
    if ($permitted['left']) {
        $BBCodeButtons .= "<a href=\"javascript:bbcode_code_insert('left','0')\"><img src=\"include/images/icons/bbcode/bbcode_left.png\" alt=\"Links ausrichten\" title=\"Links ausrichten\" class=\"ilchbbcodebuttons\"></a> ";
    }

    //> Zentriert Button!
    if ($permitted['center']) {
        $BBCodeButtons .= "<a href=\"javascript:bbcode_code_insert('center','0')\"><img src=\"include/images/icons/bbcode/bbcode_center.png\" alt=\"Mittig ausrichten\" title=\"Mittig ausrichten\" class=\"ilchbbcodebuttons\"></a> ";
    }

    //> Rechts Button!
    if ($permitted['right']) {
        $BBCodeButtons .= "<a href=\"javascript:bbcode_code_insert('right','0')\"><img src=\"include/images/icons/bbcode/bbcode_right.png\" alt=\"Rechts ausrichten\" title=\"Rechts ausrichten\" class=\"ilchbbcodebuttons\"></a> ";
    }

    //> Block Button!
    if ($permitted['block']) {
        $BBCodeButtons .= "<a href=\"javascript:bbcode_code_insert('block','0')\"><img src=\"include/images/icons/bbcode/bbcode_block.png\" alt=\"Blocksatz ausrichten\" title=\"Blocksatz ausrichten\" class=\"ilchbbcodebuttons\"></a> ";
    }

    //> Leerzeichen?
    if ($permitted['left'] || $permitted['center'] || $permitted['right'] || $permitted['block']) {
        $BBCodeButtons .= "&nbsp;";
    }

    //> Listen Button!
    if ($permitted['list']) {
        $BBCodeButtons .= "<a href=\"javascript:bbcode_insert('list','Gib hier den Text ein der aufgelistet werden soll.\\nUm die liste zu beenden einfach auf Abbrechen klicken.')\"><img src=\"include/images/icons/bbcode/bbcode_list.png\" alt=\"Liste erzeugen\" title=\"Liste erzeugen\" class=\"ilchbbcodebuttons\"></a> ";
    }

    //> Hervorheben Button!
    if ($permitted['emph']) {
        $BBCodeButtons .= "<a href=\"javascript:bbcode_code_insert('emph','0')\"><img src=\"include/images/icons/bbcode/bbcode_emph.png\" alt=\"Text hervorheben\" title=\"Text hervorheben\" class=\"ilchbbcodebuttons\"></a> ";
    }

    //> Schriftfarbe Button!
    if ($permitted['color']) {
    }

    //> Schriftfarbeauswahlcontainer
    if ($permitted['color']) {
        $BBCodeButtons .= "<a href=\"javascript:hide_color();\"><img id=\"bbcode_color_button\" src=\"include/images/icons/bbcode/bbcode_color.png\" alt=\"Text f&auml;rben\" title=\"Text f&auml;rben\" class=\"ilchbbcodebuttons\"></a> ";
        $colorar = array(
            '#FF0000' => 'red',
            '#FFFF00' => 'yellow',
            '#008000' => 'green',
            '#00FF00' => 'lime',
            '#008080' => 'teal',
            '#808000' => 'olive',
            '#0000FF' => 'blue',
            '#00FFFF' => 'aqua',
            '#000080' => 'navy',
            '#800080' => 'purple',
            '#FF00FF' => 'fuchsia',
            '#800000' => 'maroon',
            '#C0C0C0' => 'grey',
            '#808080' => 'silver',
            '#000000' => 'black',
            '#FFFFFF' => 'white',
        );
        $BBCodeButtons .= '<div class="ilchpositionabsolute"><div class="ilchbbcodefarbwahl" id="colorinput">
			<table>
				<tr class="Chead" onclick="javascript:hide_color();"><td colspan="16" class="text-center ilchcursorpoint" title="Fenster schlie&szlig;en"><b>Farbe w&auml;hlen</b></td></tr>
				<tr class="Cmite" height="15">' . getBBCodeColorList($colorar) . '</tr></table>
			</div></div>';
    }

    //> Schriftgröße Button!
    if ($permitted['size']) {
        $BBCodeButtons .= "<a href=\"javascript:bbcode_insert_with_value('size','Gib hier den Text an, der in einer anderen Schriftgr&ouml;ße formatiert werden soll.','Gib hier die Gr&ouml;&szlig;e des textes in Pixel an. \\n Pixellimit liegt bei " . $info['SizeMax'] . "px !!!')\"><img src=\"include/images/icons/bbcode/bbcode_size.png\" alt=\"Textgr&ouml;&szlig;e ver&auml;ndern\" title=\"Textgr&ouml;&szlig;e ver&auml;ndern\" class=\"ilchbbcodebuttons\"></a> ";
    }

    //> Leerzeichen?
    if ($permitted['list'] || $permitted['emph'] || $permitted['color'] || $permitted['size']) {
        $BBCodeButtons .= "&nbsp;";
    }

    //> Url Button!
    if ($permitted['url']) {
        $BBCodeButtons .= "<a href=\"javascript:bbcode_insert_with_value('url','Gib hier die Beschreibung für den Link an.','Gib hier die Adresse zu welcher verlinkt werden soll an.')\"><img src=\"include/images/icons/bbcode/bbcode_url.png\" alt=\"Hyperlink einf&uuml;gen\" title=\"Hyperlink einf&uuml;gen\" class=\"ilchbbcodebuttons\"></a> ";
    }

    //> E-Mail Button!
    if ($permitted['email']) {
        $BBCodeButtons .= "<a href=\"javascript:bbcode_insert_with_value('mail','Gib hier den namen des links an.','Gib hier die eMail - Adresse an.')\"><img src=\"include/images/icons/bbcode/bbcode_email.png\" alt=\"eMail hinzuf&uuml;gen\" title=\"eMail hinzuf&uuml;gen\" class=\"ilchbbcodebuttons\"></a> ";
    }

    //> Leerzeichen?
    if ($permitted['url'] || $permitted['email']) {
        $BBCodeButtons .= "&nbsp;";
    }

    //> Bild Button!
    if ($permitted['img']) {
        $infoText = 'Hinweise: Die Breite und H&ouml;he des Bildes ist auf '
            . $info['ImgMaxBreite'] . 'x' . $info['ImgMaxHoehe']
            . ' eingeschr&auml;nkt und w&uuml;rde verkleinert dargstellt werden.\\n\\n'
            . 'Die Darstellung des Bildes kann über Parameter angepasst werden, die über [img=params]url[/img] angegebene werden können,\\n'
            . 'wobei die einzelnen Parameter mit Semikolon ( ; ) voneinander getrennt werden.\\n'
            . 'Mögliche Parameter:\\n    1. left oder right: Bild innerhalb des Textflusses positionieren, left|right ist dabei die Position des Bildes\\n'
            . '    2. Breite und Höhe des Bilder, als Zahl nachgestellt mit w für Breite und h für Höhe, w ist optional, wenn es die einzige Angabe ist\\n'
            . 'Beispiel: [img=right;300w;200h]http://path/to/image.png[/img]\\n\\n (Jeder Parameter kann auch einzeln genutzt werden)'
            . 'Gib hier (nur) die Adresse des Bildes an:\\n';
        $BBCodeButtons .= "<a href=\"javascript:bbcode_insert('img','{$infoText}')\"><img src=\"include/images/icons/bbcode/bbcode_image.png\" alt=\"Bild einf&uuml;gen\" title=\"Bild einf&uuml;gen\" class=\"ilchbbcodebuttons\"></a> ";
    }

    //> Bild hochladen!
    global $allgAr;
    if ($allgAr['forum_usergallery'] == 1 && loggedin() && $permitted['imgUpl']) {
        $BBCodeButtons .= "<a href=\"javascript:usergalleryupl();\" title=\"Bild in Usergallery hochladen und einf&uuml;gen\"><img src=\"include/images/icons/bbcode/bbcode_imageupl.png\" alt=\"Bild hochladen\" class=\"ilchbbcodebuttons\"></a> ";
    }

    //> Screenshot Button!
    if ($permitted['screenshot']) {
        $BBCodeButtons .= "<a href=\"javascript:bbcode_insert('shot','Gib hier die Adresse des Screens an.\\nDie Breite und H&ouml;he des Bildes ist auf " . $info['ScreenMaxBreite'] . "x" . $info['ScreenMaxHoehe'] . " eingeschränkt und wird verkleinert dargstellt.\\nEs ist möglich ein Screenshot rechts oder links von anderen Elementen darzustellen, indem man [shot=left] oder [shot=right] benutzt.')\"><img src=\"include/images/icons/bbcode/bbcode_screenshot.png\" alt=\"Bild einf&uuml;gen\" title=\"Screen einf&uuml;gen\" class=\"ilchbbcodebuttons\"></a> ";
    }

    //> Leerzeichen?
    if ($permitted['img'] || $permitted['screenshot']) {
        $BBCodeButtons .= "&nbsp;";
    }

    //> Quote Button!
    if ($permitted['quote']) {
        $BBCodeButtons .= "<a href=\"javascript:bbcode_code_insert('quote','0')\"><img src=\"include/images/icons/bbcode/bbcode_quote.png\" alt=\"Zitat einf&uuml;gen\" title=\"Zitat einf&uuml;gen\" class=\"ilchbbcodebuttons\"></a> ";
    }

    //> Klapptext Button!
    if ($permitted['ktext']) {
        $BBCodeButtons .= "<a href=\"javascript:bbcode_insert_with_value('ktext','Gib hier den zu verbergenden Text ein.','Gib hier einen Titel f&uuml;r den Klapptext an.')\"><img src=\"include/images/icons/bbcode/bbcode_ktext.png\" alt=\"Klappfunktion hinzuf&uuml;gen\" title=\"Klappfunktion hinzuf&uuml;gen\" class=\"ilchbbcodebuttons\"></a> ";
    }

    //> Video Button!
    if ($permitted['video']) {
        $BBCodeButtons .= "<a href=\"javascript:bbcode_insert_with_value_2('video','Gib hier die Video ID vom Anbieter an.','Bitte Anbieter ausw&auml;hlen.\\nAkzeptiert werden: Google, YouTube, MyVideo und GameTrailers')\"><img src=\"include/images/icons/bbcode/bbcode_video.png\" alt=\"Video einf&uuml;gen\" title=\"Video einf&uuml;gen\" class=\"ilchbbcodebuttons\"></a> ";
    }

    //> Flash Button!
    if ($permitted['flash']) {
        $BBCodeButtons .= "<a href=\"javascript:bbcode_insert_with_multiple_values('flash',{tag:['Gib hier den Link zur Flashdatei an',''],width:['Gib hier die Breite für die Flashdatei an','400'],height:['Gib hier die Höhe für die Flashdatei an','300']})\"><img src=\"include/images/icons/bbcode/bbcode_flash.png\" alt=\"Flash einf&uuml;gen\" title=\"Flash einf&uuml;gen\" class=\"ilchbbcodebuttons\"></a> ";
    }

    //> Countdown Button!
    if ($permitted['countdown']) {
        $BBCodeButtons .= "<a href=\"javascript:bbcode_insert_with_value('countdown','Gib hier das Datum an wann das Ereignis beginnt.\\n Format: TT.MM.JJJJ Bsp: 24.12." . date(
                "Y"
            ) . "','Gib hier eine Zeit an, wann das Ergeinis am Ereignis- Tag beginnt.\\nFormat: Std:Min:Sek Bsp: 20:15:00')\"><img src=\"include/images/icons/bbcode/bbcode_countdown.png\" alt=\"Countdown festlegen\" title=\"Countdown festlegen\" class=\"ilchbbcodebuttons\"></a> ";
    }

    //> Leerzeichen?
    if ($permitted['quote'] || $permitted['ktext'] || $permitted['video'] || $permitted['flash'] || $permitted['countdown']) {
        $BBCodeButtons .= "&nbsp;";
    }

    //> Code Dropdown!
    if ($permitted['code'] || $permitted['php'] || $permitted['html'] || $permitted['css']) {
        $BBCodeButtons .= "<select onChange=\"javascript:bbcode_code_insert_codes(this.value); javascript:this.value='0';\" class=\"ilchbbcodedropdown\" name=\"code\"><option value=\"0\">Code einf&uuml;gen</option>";
    }


    if ($permitted['php']) {
        $BBCodeButtons .= "<option value=\"php\">PHP</option>";
    }

    if ($permitted['html']) {
        $BBCodeButtons .= "<option value=\"html\">HTML</option>";
    }

    if ($permitted['css']) {
        $BBCodeButtons .= "<option value=\"css\">CSS</option>";
    }

    if ($permitted['code']) {
        $BBCodeButtons .= "<option value=\"code\">Sonstiger Code</option>";
    }

    if ($permitted['code'] || $permitted['php'] || $permitted['html'] || $permitted['css']) {
        $BBCodeButtons .= "</select>";
    }

    return $BBCodeButtons;
}

function bbcode($s, $maxLength = 0, $maxImgWidth = 0, $maxImgHeight = 0)
{
    global $ILCH_BODYEND_ADDITIONS;
    static $badWords, $smilesArray, $jsInitialized;

    list($permitted, $info) = getBBCodeConfig();

    if (!isset($jsInitialized)) {
        $ILCH_BODYEND_ADDITIONS .= '<script type="text/javascript" src="include/includes/js/BBCodeGlobal.js"></script>'
            . '<script type="text/javascript">var bbcodemaximagewidth = ' . $info['ImgMaxBreite']
            . ', bbcodemaximageheight = ' . $info['ImgMaxHoehe'] . ';</script>';
        $jsInitialized = true;

        //Klasse laden
        require_once __DIR__ . '/../class/bbcode.php';
    }

    if (!isset($badWords)) {
        //> Badwords aus der Datenbank laden!
        $cfgBBCodeSql = db_query("SELECT fcBadPatter, fcBadReplace FROM prefix_bbcode_badword");
        while ($row = db_fetch_object($cfgBBCodeSql) ) {
            $badWords['%' . preg_quote($row->fcBadPatter,'%') . '%iU'] = $row->fcBadReplace; 
        }
    }

    //> Smilies in array abspeichern.
    if (!isset($smilesArray)) {
        $erg = db_query("SELECT ent, url, emo FROM `prefix_smilies`");
        while ($row = db_fetch_object($erg)) {
            $smilesArray[$row->ent] = $row->emo . '#@#-_-_-#@#' . $row->url;
        }
    }

    if ($maxLength != 0) {
        $info['fnWortMaxLaenge'] = $maxLength;
    }
    if ($maxImgWidth != 0) {
        $info['fnImgMaxBreite'] = $maxImgWidth;
    }
    if ($maxImgHeight != 0) {
        $info['fnImgMaxBreite'] = $maxImgHeight;
    }

    $bbcode = new bbcode($smilesArray, $permitted, $info, $badWords);
    return $bbcode->parse($s);
}
