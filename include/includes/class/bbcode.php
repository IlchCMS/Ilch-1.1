<?php
/**
 * BBCode 2.0
 * erste Version von Thomas Bowe [Funjoy] - bbcode@phpline.de - www.phpline.de
 * extended and rewritten by Mairu
 */

/* Module - Information
* -------------------------------------------------------
* Hier könnt ihr eure Module includieren lassen.
* Wenn Ihr selber Module zum Highlight programmiert
* denkt daran das ihr auch noch das Parsen hier definieren müsst.
* und in der bbcode_config.php Datei müsstet ihr die Option auch noch einstellen.
* um ein Beispiel zu haben schaut euch die Funktion _htmlblock() am besten mal an.
* und in Zeile 308 und Zeile 490 habt ihr ein Beispiel wie ihr die Parsebefehle schreiben könnt.
*/

//> Bitte denkt daran das, dass Modul html.php immer unter dem Modul css.php sein muss.
//> Modul [css.php]
    if(file_exists("include/includes/class/highlight/css.php")) {
        require_once("include/includes/class/highlight/css.php");
    }

//> Modul [html.php]
    if(file_exists("include/includes/class/highlight/html.php")) {
        require_once("include/includes/class/highlight/html.php");
    }


class bbcode {
    //> Tags die geparsed werden dürfen.
    private $permitted = array();

    //> Verschlüsselte codeblocks.
    private $codecblocks = array();

    private $codecBlockFormatters = array();

    //> Badwords!
    private $badWordPatterns = array();

    //> Informationen für die Klasse!
    private $info = array();

    /** @var array Smilies die in Grafik umgewandelt werden sollen. */
    private $smileys = array();

    /** @var array Cache für Textblöcke (Zitate, Klapptext) */
    private $textBlockCache = array();

    /** @var bool */
    private $innerList = false;

    public function __construct(array $smileys, array $permitted, array $info, array $badWordPatterns)
    {
        $this->smileys = $smileys;
        $this->permitted = $permitted;
        $this->info = $info;
        $this->badWordPatterns = $badWordPatterns;

        $this->codecBlockFormatters = array(
            'php'  => array($this, 'formatPhpBlock'),
            'code' => array($this, 'formatCodeBlock'),
            'css'  => array($this, 'formatCssBlock'),
            'html' => array($this, 'formatHtmlBlock'),
        );

        $this->reset();
    }

    /**
     * @return void
     */
    private function reset()
    {
        $this->textBlockCache = array();
    }

    /**
     * Codeblock "verschlüsseln" und wieder ausgeben.
     * @param array $matches mit den Keys 'type', 'options', 'content'
     * @return string
     */
    private function encode_codec(array $matches) {
        $crypt = md5(count($this->codecblocks));
        $this->codecblocks[$crypt . ":" . $matches['type']] = str_replace('\"', '"', $matches['content']);
        return '[' . $matches['type'] . $matches['options'] . ']' . $crypt . '[/' . $matches['type'] . ']';
    }

    /**
     * Verschlüsselte Codeblocks formatiert ausgeben
     * @param array $matches
     * @return string
     */
    private function decode_codec(array $matches) {
        $string = $this->codecblocks[$matches['content'] . ':' . $matches['type']];

        if (isset($this->codecBlockFormatters[$matches['type']])) {
            $string = call_user_func($this->codecBlockFormatters[$matches['type']], $string, $matches['options']);
        }

        return $string;
    }

    /**
     * @param string $options
     * @return array
     */
    private function parseCodeOptions($options)
    {
        $file = null;
        $startLine = 1;

        if (!empty($options)) {
            $parsed = explode(';', $options);
            $file = $parsed[0];
            if (isset($parsed[1]) && ctype_digit($parsed[1])) {
                $startLine = $parsed[1];
            }
        }

        return array($file, $startLine);
    }

    /**
     * Parse options string like option="value" option2='value2' to array
     * @param string $options
     * @return array
     */
    private function parseOptions($options)
    {
        $parsedOptions = array();
        foreach (explode(' ', trim($options)) as $option) {
            list($key, $value) = explode('=', $option);
            $parsedOptions[trim($key)] = trim(html_entity_decode($value, ILCH_ENTITIES_FLAGS, ILCH_CHARSET), '"\'');
        }
        return $parsedOptions;
    }

    /**
     * Formatiert Code als Block
     * @param string $code
     * @param string $options
     * @return string
     */
    private function formatCodeBlock($code, $options) {
        list($file, $firstLine) = $this->parseCodeOptions($options);
		$code = htmlentities($code, ILCH_ENTITIES_FLAGS, ILCH_CHARSET);

        $code = str_replace("\t", '&nbsp; &nbsp;', $code);
        $code = str_replace('  ', '&nbsp; ', $code);
        $code = str_replace('  ', ' &nbsp;', $code);
        $code = nl2br($code);

        return $this->addCodeContainer($code, 'Code', $file, $firstLine);
    }

    /**
     * Formatiert HTML Code als Block
     * @param string $code
     * @param string $options
     * @return string
     */
    private function formatHtmlBlock($code, $options) {
        list($file, $firstLine) = $this->parseCodeOptions($options);
		$code = htmlentities($code, ILCH_ENTITIES_FLAGS, ILCH_CHARSET);

        //> Highlight Modul Funktion checken ob sie existerit.
        if(function_exists("highlight_html")) {
            $code = highlight_html($code,$this->info['BlockCodeFarbe']);
        }

        $code = str_replace("\t", '&nbsp; &nbsp;', $code);
        $code = str_replace('  ', '&nbsp; ', $code);
        $code = str_replace('  ', ' &nbsp;', $code);
        $code = nl2br($code);

        return $this->addCodeContainer($code, 'HTML', $file, $firstLine);
    }

    /**
     * Formatiert CSS Code als Block
     * @param string $code
     * @param string $options
     * @return string
     */
    private function formatCssBlock($code, $options) {
        list($file, $firstLine) = $this->parseCodeOptions($options);
		$code = htmlentities($code, ILCH_ENTITIES_FLAGS, ILCH_CHARSET);

        //> Highlight Modul Funktion checken ob sie existerit.
        if(function_exists("highlight_css")) {
            $code = highlight_css($code);
        }

        $code = str_replace("\t", '&nbsp; &nbsp;', $code);
        $code = str_replace('  ', '&nbsp; ', $code);
        $code = str_replace('  ', ' &nbsp;', $code);
        $code = nl2br($code);

        return $this->addCodeContainer($code, 'CSS', $file, $firstLine);
    }

    /**
     * Formatiert PHP Code als Block
     * @param string $code
     * @param string $options
     * @return string
     */
    private function formatPhpBlock($code, $options) {
        list($file, $firstLine) = $this->parseCodeOptions($options);
        if (strpos($code, '<?php') === false) {
            $code = "<?php\n{$code}\n?>";
            $remove = true;
        } else {
            $remove = false;
        }
        $php = highlight_string($code, true);
        if ($remove) {
            $php = str_replace(
                array('&lt;?php<br />', '<br /></span><span style="color: #0000BB">?&gt;</span>', '<code>', '</code>'),
                array('', '</span>', '', ''),
                $php
            );
        }
        return $this->addCodeContainer($php, 'Php', $file, $firstLine);
    }

    /**
     * Erzeugt Block für Code
     * @param string $code
     * @param string $type
     * @param string null $file
     * @param int $firstLine
     * @return string
     */
    private function addCodeContainer($code, $type, $file=null, $firstLine = 1) {
        //> Datei pfad mit angegeben?
        $file = ($file == NULL) ? "":" von Datei <em>".$this->shortWords($file)."</em>";

        //> Zeilen zählen.
        $linescount = substr_count($code, '<br />') + $firstLine + 1;
        $line = '';
        for($no=$firstLine; $no < $linescount; $no++) {
            $line .= $no.":<br>";
        }

        //> Hier könnt ihr den Header und Footer für HTML editieren.
        $breite = trim($this->info['BlockTabelleBreite']);
        $breite = (strpos($breite, '%') !== false) ? '450px' : $breite.'px';
        $header = "<div style=\"overflow: auto; width: {$breite};\">"
                 ."<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" style=\"BORDER: 1px SOLID ".$this->info['BlockRandFarbe'].";\" width=\"100%\">"
                 ."<tr><td colspan=\"3\" style=\"font-size:12px; font-weight:bold; color:".$this->info['BlockSchriftfarbe'].";background-color:".$this->info['BlockHintergrundfarbe'].";\">&nbsp;".$type.$file."</td></tr>"
                 ."<tr style=\"background-color:".$this->info['BlockHintergrundfarbeIT']."\"><td style=\"width:20px; color:".$this->info['BlockSchriftfarbe'].";padding-left:2px;padding-right:2px;border-right:1px solid ".$this->info['BlockHintergrundfarbe'].";\" align=\"right\" valign=\"top\"><code style=\"width:20px;\">"
                 .$line
                 ."</code></td><td width=\"5\">&nbsp;</td><td valign=\"top\" style=\"background-color:".$this->info['BlockHintergrundfarbeIT']."; color:".$this->info['BlockSchriftfarbe'].";\" nowrap width=\"95%\"><code>";
        $footer = "</code></td></tr></table></div>";

        return $header.$code.$footer;
    }

    /**
     * Smileys im übergebenen String ersetzen
     * @param string $string
     * @return string
     */
    private function replaceSmileys($string) {
        if(is_array($this->smileys) && $this->permitted['smileys'] == true) {
            $smileystart = '#@'.uniqid('').'@#';
            $smileymid = '|#@|'.uniqid('').'|@#|';
            $smileyend = '#@'.uniqid('').'@#';
            foreach ($this->smileys as $icon => $info) {
                $string = str_replace($icon, $smileystart.$icon.$smileyend, $string);
            }
            $string = str_replace($smileyend.$smileystart, $smileymid, $string);
            $string = preg_replace('%(\S)' . $smileystart . '(.*)' . $smileyend . '%iU', '$1$2', $string);
            $string = preg_replace_callback(
                '%(^|\s)(' . $smileystart . ')(.*)(' . $smileyend . ')%iU',
                function (array $matches) use ($smileystart, $smileymid, $smileyend) {
                    return $matches[1] . $matches[2] . str_replace($smileymid, $smileyend.$smileystart, $matches[3])
                        . $matches[4];
                },
                $string);

            $string = str_replace($smileymid, '', $string);
            foreach ($this->smileys as $icon => $info) {
                list($emo, $url) = explode('#@#-_-_-#@#', $info);
                $string = str_replace(
                    $smileystart . $icon . $smileyend,
                    '<img src="include/images/smiles/' . $url . '" alt="' . $icon . '" title="' . $emo . '">',
                    $string
                );
            }
            $string = str_replace(array($smileyend, $smileystart), '', $string);
        }
        return $string;
    }

    //> Badwords Filtern.
    private function filterBadWords($string) {
        if(!empty($this->badWordPatterns)) {
            $string = preg_replace(array_keys($this->badWordPatterns), array_values($this->badWordPatterns), $string);
        }

        return $string;
    }

    /**
     * Callbackfunktion für [size]
     * @param array $matches
     * @return string
     */
    private function styleFontSize(array $matches) {
        $size = $matches['size'];
        $max = $this->info['SizeMax'];
        return '<span style="font-size:' . ($size > $max ? $max : $size) . 'px">' . stripcslashes($matches['text'])
        . '</span>';
    }

    /**
     * Gibt style Attribute für img Tag zurück, je nach float option
     * @param string $float
     * @return string
     */
    private function getImageFloat($float)
    {
        if ($float == 'none' || $float == 'left' || $float == 'right') {
            return 'style="float:' . $float . '; margin: 5px;" ';
        }
        return '';
    }

    /**
     * Callbackfunktion für [url]
     * @param array $matches
     * @return string
     */
    private function formatImage(array $matches) {
        $attributes = array();

        foreach (explode(';', $matches['options']) as $option) {
            if (in_array($option, array('none', 'left', 'right'))) {
                $attributes[] = 'style="float:' . $option . '; margin: 5px;"';
            } elseif (preg_match('%^(\d+)(w|h)?$%', $option, $dimMatches) === 1) {
                if (!isset($dimMatches[2]) || $dimMatches[2] === 'w') {
                    $attributes[] = 'width="' . $dimMatches[1] . '"';
                } elseif ($matches[2] ===  'h') {
                    $attributes[] = 'height="' . $dimMatches[1] . '"';
                }
            }
        }

        return '<img src="' . $matches['url'] . '" alt="" title="" class="bbcode_image ilchbordernone" '
            . implode(' ', $attributes) . '>';
    }

    /**
     * Callbackfunktion für [shot]
     * @param array $matches
     * @return string
     */
    private function formatScreenShot(array $matches) {
        return '<a href="' . $matches['url'] . '" target="_blank"><img src="' . $matches['url']
            . '" alt="" title="" class="ilchbordernone" width="' . $this->info['ScreenMaxBreite']
            . '" height="' . $this->info['ScreenMaxHoehe'] . '" ' . $this->getImageFloat($matches['options']) . '></a>';
    }

    /**
     * Linkbeschreibung kürzen, falls zu lang
     * @param string $string
     * @return string
     */
    private function shortCaption($string) {
        $words = explode(" ", $string);
        foreach ($words as $word) {
            if (strlen($word) > $this->info['WortMaxLaenge'] && !preg_match(
                    '%(\[(img|shot)\](.*)\[/(img|shot)\])%i',
                    $word
                )
            ) {
                $maxd2 = sprintf("%00d", ($this->info['WortMaxLaenge'] / 2));
                $string = str_replace($word, substr($word, 0, $maxd2) . "..." . substr($word, -$maxd2), $string);
            }
        }
        return $string;
    }

    /**
     * Check if one of the given patterns matches the word
     * @param array $patterns
     * @param string $word
     * @return bool true if at least one pattern matches
     */
    private function checkPatterns(array $patterns, $word) {
        if (!is_array($patterns)) {
            return false;
        }
        foreach ($patterns as $p) {
            if (preg_match($p, $word) === 1) {
                return true;
            }
        }
        return false;
    }

    /**
     * Kürzt zu lange Wörter
     * @param string $string
     * @param bool $checkPatterns
     * @return string
     */
    private function shortWords($string, $checkPatterns = true) {
        //> Zeichenkette in einzelne Array elemente zerlegen.
        $lines = explode("\n",$string);

        //> Patter Befehle die nicht gekürzt werden dürfen !!!
        $pattern = array('%^www\.[-a-z0-9@:;\%_\+\.~#?&/=]+%i',
            '%^(http|https|ftp)://[-a-z0-9@:;\%_\+.~#?&/=]+%i',
            '~\[(img|url|code|html|css|php|countdown|list)(=[^\]]+)?].*\[/\\1]~',
            "%\[(flash)(( \w+=('|\"|&quot;)\d+\g{-1})*)].*\[/\\1]%i"
        );

        foreach ($lines as &$line) {
            $words = explode(' ', $line);
            foreach ($words as &$word) {
                if (strlen($word) > $this->info['WortMaxLaenge']
                    && !($checkPatterns && $this->checkPatterns($pattern, $word))
                ) {
                    //Auskommentiert also Variante mit 'zulanges...Wort' zu gunsten von 'zulanges allesdazwischen Wort' (ohne ...)
                    //$maxd2 = sprintf("%00d",($this->info['WortMaxLaenge']/2));
                    $word = wordwrap($word, $this->info['WortMaxLaenge']);
                }
            }
            $line = implode(' ', $words);
        }
        return implode("\n", $lines);
    }
    //> Geöffnete Ktext- Tags Nummerieren.

    /**
     * Callbackfunktion für [url]
     * @param array $matches Array mit Keys 'url' [, 'caption'][, 'whitespace']
     * @return string
     */
    private function formatUrl(array $matches) {
        if (empty($matches['url'])) {
            return $matches[0];
        }

        if (empty($matches['caption'])) {
            $matches['caption'] = $matches['url'];
        }

        $url = trim($matches['url']);
        $caption = trim($this->replaceSmileys($matches['caption']));
        $server = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
        if (preg_match('%^((http|ftp|https)://)|^/%i', $url) == 0) {
            $url = 'http://' . $url;
        }
        if (substr($url, 0, 1) == '/' OR strpos($url, $server) !== false) {
            $target = '_self';
        } else {
            $target = '_blank';
        }

        $count = strlen($caption);
        if ($count >= $this->info['UrlMaxLaenge']) {
            $caption = $this->shortCaption($caption);
        }
        $ws = empty($matches['whitespace']) ? '' : $matches['whitespace'];
        return $ws . '<a href="' . $url . '" target="' . $target . '">' . $caption . '</a>';
    }

    /**
     * @param string $type
     */
    private function initTextBlockType($type)
    {
        $this->textBlockCache[$type] = array(
            'open' => 0,
            'close' => 0,
            'opened' => array()
        );
    }

    private function addTextBlock(array $matches)
    {
        $type = $matches['type'];
        $openClose = $matches['close'] === '/' ? 'close' : 'open';
        $this->textBlockCache[$type][$openClose]++;
        if ($openClose === 'open') {
            $this->textBlockCache[$type]['opened'][] = $this->textBlockCache[$type]['open'];
            $sprintfArgs = array(
                $type,
                $this->textBlockCache[$type]['open'],
                empty($matches['title']) ? '' : '=' . $matches['title']
            );
        } else {
            $sprintfArgs = array(
                '/' . $type,
                array_pop($this->textBlockCache[$type]['opened']),
                ''
            );
        }
        return vsprintf('[%s:%d%s]', $sprintfArgs);
    }

    private function addTextBlockOpen(array $matches)
    {
        $type = $matches['type'];
        $this->textBlockCache[$type]['open']++;
        $this->textBlockCache[$type]['opened'][] = $this->textBlockCache[$type]['open'];
        return sprintf('[%s:%d%s]', $type, $this->textBlockCache[$type]['open'], empty($matches['title']) ? '' : '=' . $matches['title']);
    }

    private function addTextBlockClose(array $matches)
    {
        $type = $matches['type'];
        $this->textBlockCache[$type]['close']++;
        return sprintf('[/%s:%d]', $type, array_pop($this->textBlockCache[$type]['opened']));
    }

    /**
     * Klapptext (ktext) formatieren
     * @param string $string
     * @return string
     */
    private function formatFoldingText($string) {
        $random = rand(1,10000000);

        //> Html- Muster für geöffnete Tags mit Titel.
        $HeaderTitel = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"".$this->info['KtextTabelleBreite']."\" align=\"center\">"
                      ."<tr><td><a href=\"javascript:Klapptext('__ID__')\">"
                      ."<img src=\"include/images/icons/plus.gif\" id=\"image___ID__\" class=\"ilchbordernone\" alt=\"Aus/Ein-klappen\" title=\"Aus/Ein-klappen\"> ";

        $FooterTitel = "</a></td></tr>"
                      ."<tr><td><div id=\"layer___ID__\" style=\"display:none;border:1px ".$this->info['KtextRandFormat']." ".$this->info['KtextRandFarbe'].";\">";

        //> Html- Muster für geschlossene Tags.
        $KtextClose = "</div></td></tr></table>\n";

        $completeBlocks = max($this->textBlockCache['ktext']['open'], $this->textBlockCache['ktext']['close']);
        $replaced = 0;
        do {
            $string = preg_replace(
                '%\[ktext:(\d+)=([^]]+)](.*)\[/ktext:\\1]%siU',
                str_replace("__ID__","\$1@".$random,$HeaderTitel)."\$2".str_replace("__ID__","\$1@".$random,$FooterTitel)."\$3".$KtextClose,
                $string,
                -1,
                $replacedInRun
            );
            $replaced += $replacedInRun;
        } while ($replacedInRun && $replaced < $completeBlocks);
        return $string;
    }

    /**
     * Zitate (quote) formatieren
     * @param string $string
     * @return string
     */
    private function formatQuotes($string) {
        //> überprüfen ob Bod gesetzt ist.
        if(strtolower($this->info['QuoteSchriftformatIT']) == "bold") {
            $Schriftformat = "font-weight:bold;";
        } else {
            $Schriftformat = "font-style:".$this->info['QuoteSchriftformatIT'].";";
        }

        //> Html- Muster für geöffnete Quote- Tags.
        $Header = "<table cellspacing=\"0\" cellpadding=\"2\" border=\"0\" style=\"BORDER: 1px SOLID ".$this->info['QuoteRandFarbe'].";\" width=\"".$this->info['QuoteTabelleBreite']."\" align=\"center\">"
                 ."<tr><td style=\"font-family:Arial, Helvetica, sans-serif;FONT-SIZE:13px;FONT-WEIGHT:BOLD;COLOR:".$this->info['QuoteSchriftfarbe'].";BACKGROUND-COLOR:".$this->info['QuoteHintergrundfarbe'].";\">&nbsp;Zitat</td></tr>"
                 ."<tr bgcolor=\"".$this->info['QuoteHintergrundfarbeIT']."\"><td><table align=\"center\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\"><tr><td style=\"".$Schriftformat."FONT-SIZE:10px;COLOR:".$this->info['QuoteSchriftfarbeIT'].";\">";

        //> Html- Muster für geöffnete Quote- Tags mit User.
        $HeaderUser = "<table cellspacing=\"0\" cellpadding=\"2\" border=\"0\" style=\"BORDER: 1px SOLID ".$this->info['QuoteRandFarbe'].";\" width=\"".$this->info['QuoteTabelleBreite']."\" align=\"center\">"
                           ."<tr><td style=\"font-family:Arial, Helvetica, sans-serif;FONT-SIZE:13px;FONT-WEIGHT:BOLD;COLOR:".$this->info['QuoteSchriftfarbe'].";BACKGROUND-COLOR:".$this->info['QuoteHintergrundfarbe'].";\">&nbsp;Zitat von ";

        $FooterUser = "</td></tr><tr bgcolor=\"".$this->info['QuoteHintergrundfarbeIT']."\"><td><table align=\"center\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"98%\"><tr><td style=\"".$Schriftformat."FONT-SIZE:10px;COLOR:".$this->info['QuoteSchriftfarbeIT'].";\">";

        //> Html- Muster für geschlossene Quote- Tags.
        $QuoteClose = "</td></tr></table></td></tr></table>";

        $completeQuotes = max($this->textBlockCache['quote']['open'], $this->textBlockCache['quote']['close']);
        $replaced = 0;
        do {
            $string = preg_replace_callback(
                "%\[quote:([0-9]*)(?:=(?P<name>[^[/]*))?](?P<content>.*)\[/quote:\\1]%siU",
                function (array $matches) use ($Header, $HeaderUser, $FooterUser, $QuoteClose) {
                    if (empty($matches['name'])) {
                        return $Header . $matches['content'] . $QuoteClose;
                    }
                    return $HeaderUser . $matches['name'] . $FooterUser . $matches['content'] . $QuoteClose;
                },
                $string,
                -1,
                $replacedInRun
            );
            $replaced += $replacedInRun;
        } while ($replacedInRun && $replaced < $completeQuotes);
        return $string;
    }

    /**
     * Callbackfunktion for list contents
     * @param array $matches
     * @return string
     */
    private function formatListContent(array $matches) {
        $type = (isset($matches['option']) && $matches['option'] === '1') ? 'ol' : 'ul';
        if (!$this->innerList) {
            preg_match('%((?:^|\[list:\d+(?:=[01])])(?:<br />|\s)*)\[\*]%is', $matches['content'], $om);
            $matches['content'] = preg_replace('%((?:^|\[list:\d+(?:=[01])])(?:<br />|\s)*)\[\*]%is', '\\1', $matches['content']);
            $matches['content'] = str_replace('[*]', '</li><li>', $matches['content']);
        }
        return sprintf('<%1$s><li>%2$s</li></%1$s>', $type, $matches['content']);
    }

    /**
     * Format lists
     * @param string $string
     * @return string
     */
    private function formatList($string) {
        $completeLists = max($this->textBlockCache['list']['open'], $this->textBlockCache['list']['close']);
        $parsedLists = 0;
         do {
            $string = preg_replace_callback(
                "%\[list:(\d+)(?:=(?P<option>[01]))?\](?P<content>.+)\[\/list:\\1\]%Uis",
                array($this, 'formatListContent'),
                $string,
                -1,
                $parsedListsInRun
            );
            $parsedLists += $parsedListsInRun;
             $this->innerList = true;
        } while ($parsedLists < $completeLists && $parsedListsInRun !== 0);
        $this->innerList = false;

        return $string;
    }

    /**
     * Callbackfunktion für [video]
     * @param array $matches
     * @return string
     */
    private function formatVideo(array $matches) {
        $id = $matches['id'];
        switch (strtolower($matches['type'])) {
            case 'google':
                $str = "<embed style=\"width:" . $this->info['GoogleBreite'] . "px; height:" . $this->info['GoogleHoehe'] . "px;\" id=\"VideoPlayback\" align=\"middle\" type=\"application/x-shockwave-flash\" src=\"http://video.google.com/googleplayer.swf?docId=" . $id . "\" allowScriptAccess=\"sameDomain\" quality=\"best\" bgcolor=\"" . $this->info['GoogleHintergrundfarbe'] . "\" scale=\"noScale\" salign=\"TL\" FlashVars=\"playerMode=embedded\"/>";
                break;
            case 'youtube':
                $str = "<object width=\"" . $this->info['YoutubeBreite'] . "\" height=\"" . $this->info['YoutubeHoehe'] . "\"><param name=\"movie\" value=\"http://www.youtube.com/v/" . $id . "\"></param><embed src=\"http://www.youtube.com/v/" . $id . "\" type=\"application/x-shockwave-flash\"  width=\"" . $this->info['YoutubeBreite'] . "\" height=\"" . $this->info['YoutubeHoehe'] . "\" bgcolor=\"" . $this->info['YoutubeHintergrundfarbe'] . "\"></embed></object>";
                break;
            case 'myvideo':
                $str = "<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" width=\"" . $this->info['MyvideoBreite'] . "\" height=\"" . $this->info['MyvideoHoehe'] . "\"><param name=\"movie\" value=\"http://www.myvideo.de/movie/" . $id . "\"></param><embed src=\"http://www.myvideo.de/movie/" . $id . "\" width=\"" . $this->info['MyvideoBreite'] . "\" height=\"" . $this->info['MyvideoHoehe'] . "\" type=\"application/x-shockwave-flash\"></embed></object>";
                break;
            case 'gametrailers':
                $str = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"  codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" id="gtembed" width="' . $this->info['YoutubeBreite'] . '" height="' . $this->info['YoutubeHoehe'] . '">    <param name="allowScriptAccess" value="sameDomain" />     <param name="allowFullScreen" value="true" /> <param name="movie" value="http://www.gametrailers.com/remote_wrap.php?mid=' . $id . '"/> <param name="quality" value="high" /> <embed src="http://www.gametrailers.com/remote_wrap.php?mid=' . $id . '" swLiveConnect="true" name="gtembed" align="middle" allowScriptAccess="sameDomain" allowFullScreen="true" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="' . $this->info['YoutubeBreite'] . '" height="' . $this->info['YoutubeHoehe'] . '"></embed> </object>';
                break;
            default:
                $str = '';
        }

        return $str;
    }

    /**
     * Callbackfunktion für [countdown]
     * @param array $matches key 'date'[, 'time']
     * @return string
     */
    private function formatCountDown(array $matches) {
        $time = $matches['time'];
        $date = explode(".", $matches['date']);

        if (!empty($time)) {
            $timechk = explode(':', $time);
            if ($timechk[0] <= 23 && $timechk[1] <= 59 && $timechk[2] <= 59) {
                $timechk = true;
            } else {
                $timechk = false;
            }
        } else {
            $timechk = true;
        }

        //> Html Design.,
        $Font = ($this->info['CountdownSchriftformat'] == "bold") ? "font-wight:bold;" : "font-style:" . $this->info['CountdownSchriftformat'] . ";";
        $Header = "<div style=\"width:" . $this->info['CountdownTabelleBreite'] . ";padding:5px;font-family:Verdana;font-size:" . $this->info['CountdownSchriftsize'] . "px;" . $Font . "color:" . $this->info['CountdownSchriftfarbe'] . ";border:2px dotted " . $this->info['CountdownRandFarbe'] . ";text-align:center\">";
        $Footer = "</div>";

        //> Überprüfen ob die angaben stimmen.
        if ($date[0] <= 31 && $date[1] <= 12 && $date[2] /*>= date("Y")*/ &&
            checkdate($date[1], $date[0], $date[2]) && $timechk
        ) {
            if (!empty($time)) {
                $time = explode(":", $time);
                $intStd = $time[0];
                $intMin = $time[1];
                $intSek = $time[2];
            } else {
                $intStd = 0;
                $intMin = 0;
                $intSek = 0;
            }

            $Timestamp = @mktime($intStd, $intMin, $intSek, $date[1], $date[0], $date[2]);
            $Diff = $Timestamp - time();

            if ($Diff > 1) {
                $Tage = sprintf("%00d", ($Diff / 86400));
                $Stunden = sprintf("%00d", (($Diff - ($Tage * 86400)) / 3600));
                $Minuten = sprintf("%00d", (($Diff - (($Tage * 86400) + ($Stunden * 3600))) / 60));
                $Sekunden = ($Diff - (($Tage * 86400) + ($Stunden * 3600) + ($Minuten * 60)));

                //> Bei höheren Wert wie 1 als Mehrzahl ausgeben.
                $mzTg = ($Tage == 1) ? "" : "e";
                $mzStd = ($Stunden == 1) ? "" : "n";
                $mzMin = ($Minuten == 1) ? "" : "n";
                $mzSek = ($Sekunden == 1) ? "" : "n";

                //> Datum zusamstellen.
                $str = $Header . $Tage . " Tag" . $mzTg . ", " . $Stunden . " Stunde" . $mzStd . ", "
                    . $Minuten . " Minute" . $mzMin . " und " . $Sekunden . " Sekunde" . $mzSek . $Footer;
            } else {
                //> Datum zusamstellen wenn Datum unmittelbar bevor steht.
                $str = $Header . (is_array($time) ? implode(':', $time) : $time) . ' '
                    . implode('.', $date) . " !!!" . $Footer;
            }
        } else {
            $str = $Header . "Der Countdown ist falsch definiert" . $Footer;
        }

        return $str;
    }

    /**
     * Callbackfunktion für [flash]
     * @param array $matches
     * @return string
     */
    private function formatFlash(array $matches)
    {
        $url = $matches['url'];
        $width = $this->info['FlashBreite'];
        $height = $this->info['FlashHoehe'];
        if (!empty($matches['options'])) {
            $options = $this->parseOptions($matches['options']);
            if (!empty($options['height']) && $options['height'] < $height) {
                $height = $options['height'];
            }
            if (!empty($options['width']) && $options['width'] < $width) {
                $width = $options['width'];
            }
        }

        return '<object classid="CLSID:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'.$width.'" height="'.$height.'"'.
            'codebase="http://active.macromedia.com/flash2/cabs/swflash.cab#version=7,0,0,0" class="bbcode_flash">'.
            '<param name="movie" value="' . $url . '">'.
            '<param name="quality" value="high">'.
            '<param name="scale" value="exactfit">'.
            '<param name="menu" value="true">'.
            '<param name="bgcolor" value="'.$this->info['FlashHintergrundfarbe'].'"> '.
            '<embed src="' . $url . '" quality="high" scale="exactfit" menu="false" '.
            'bgcolor="'.$this->info['FlashHintergrundfarbe'].'" width="'.$width.'" height="'.$height.'" swLiveConnect="false" '.
            'type="application/x-shockwave-flash" '.
            'pluginspage="http://www.macromedia.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash">'.
            '</embed>'.
            '</object>';
    }

    /**
     * Wandle den Text mit BBCode zu HTML um
     * @param string $string
     * @return string
     */
    public function parse($string) {
        $searchPattern =  $searchReplace = $callbacks = $callbackPatterns = array();

        $codeTypes = array();
        $possibleCodeTypes = array('php', 'html', 'css', 'code');
        foreach ($possibleCodeTypes as $type) {
            if ($this->permitted[$type]) {
                $codeTypes[] = $type;
            }
        }

        if (!empty($codeTypes)) {
            $string = preg_replace_callback(
                '%\[(?P<type>' . implode('|', $codeTypes) . ')(?P<options>=.+)?\](?P<content>.+)\[\/(?P=type)\]%siU',
                array($this, 'encode_codec'),
                $string
            );
        }

        //> Badwords Filtern.
        $string = $this->filterBadWords($string);

        //> BB Code der den Codeblock nicht betrifft.
        //> Überprüfen ob die wörter nicht die maximal länge überschrieten.
        $string = $this->shortWords($string);
		$string = htmlentities($string, ILCH_ENTITIES_FLAGS, ILCH_CHARSET);
        $string = nl2br($string);


        if ($this->permitted['url']) {
            if($this->permitted['autourl']) {
                //> Format: www.xxx.de
                $callbackPatterns[] = "%(?P<whitespace> |\n|^)(?P<url>www.[a-zA-Z\-0-9@:\%_\+.~#?&//=,;]+?)%Ui";
                $callbacks[] = array($this, 'formatUrl');

                //> Format: http://www.xxx.de
                $callbackPatterns[] = "%(?P<whitespace> |\n|^)(?P<url>(?:http|https|ftp)://{1}[a-zA-Z\-0-9@:\%_\+.~#?&//=,;]+?)%Ui";
                $callbacks[] = array($this, 'formatUrl');

                //> Format xxx@xxx.de
                $searchPattern[] = "%(\s|^)([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})%i";
                $searchReplace[] = "$1<a href=\"mailto:$2\">$2</a>";
            }

            //> Format: [url=xxx]xxx[/url]
            $callbackPatterns[] = "%\[url=(?P<url>[^\]]*)\](?P<caption>.+)\[\/url\]%Uis";
            $callbacks[] = array($this, 'formatUrl');

            //> Format: [url]xxx[/url]
            $callbackPatterns[] = "%\[url\](?P<url>.+)\[\/url\]%siU";
            $callbacks[] = array($this, 'formatUrl');
        }

        //> Darf BB Code [MAIL] dekodiert werden?
        if ($this->permitted['email']) {
            //> Format: [mail]xxx@xxx.de[/mail]
            $searchPattern[] = "%\[mail\]([_\.0-9a-z-]+\@([0-9a-z\-]+)\.[a-z]{2,3})\[\/mail\]%Uis";
            $searchReplace[] = "<a href=\"mailto:$1\">$1</a>";

            //> Format: [mail=xxx@xxx.de]xxx[/mail]
            $searchPattern[] = "%\[mail=([_\.0-9a-z-]+\@([0-9a-z\-]+)\.[a-z]{2,3})\](.+)\[\/mail\]%Uis";
            $searchReplace[] = "<a href=\"mailto:$1\">$3</a>";
        }

        //> Darf BB Code [B] dekodiert werden?
        if ($this->permitted['b']) {
            //> Format: [b]xxx[/b]
            $searchPattern[] = "%\[b\](.+)\[\/b\]%Uis";
            $searchReplace[] = "<b>\$1</b>";
        }

        //> Darf BB Code [I] dekodiert werden?
        if ($this->permitted['i']) {
            //> Format: [i]xxx[/i]
            $searchPattern[] = "%\[i\](.+)\[\/i\]%Uis";
            $searchReplace[] = "<i>\$1</i>";
        }

        //> Darf BB Code [U] dekodiert werden?
        if ($this->permitted['u']) {
            //> Format: [u]xxx[/u]
            $searchPattern[] = "%\[u\](.+)\[\/u\]%Uis";
            $searchReplace[] = "<u>\$1</u>";
        }

        //> Darf BB Code [S] dekodiert werden?
        if ($this->permitted['s']) {
            //> Format: [s]xxx[/s]
            $searchPattern[] = "%\[s\](.+)\[\/s\]%Uis";
            $searchReplace[] = "<strike>\$1</strike>";
        }


        ###############################################


        //> Darf BB Code [LEFT] dekodiert werden?
        if ($this->permitted['left']) {
            //> Format: [left]xxx[/left]
            $searchPattern[] = "%\[left\](.+)\[\/left\]%Uis";
            $searchReplace[] = "<div align=\"left\">\$1</div>";
        }

        //> Darf BB Code [CENTER] dekodiert werden?
        if ($this->permitted['center']) {
            //> Format: [center]xxx[/center]
            $searchPattern[] = "%\[center\](.+)\[\/center\]%Uis";
            $searchReplace[] = "<div align=\"center\">\$1</div>";
        }

        //> Darf BB Code [RIGHT] dekodiert werden?
        if ($this->permitted['right']) {
            //> Format: [right]xxx[/right]
            $searchPattern[] = "%\[right\](.+)\[\/right\]%Uis";
            $searchReplace[] = "<div align=\"right\">\$1</div>";
        }
		
        //> Darf BB Code [BLOCK] dekodiert werden?
        if ($this->permitted['block']) {
            //> Format: [right]xxx[/right]
            $searchPattern[] = "%\[block\](.+)\[\/block\]%Uis";
            $searchReplace[] = "<div align=\"justify\">\$1</div>";
        }
        ###############################################

        //> Darf BB Code [EMPH] dekodiert werden?
        if ($this->permitted['emph']) {
            //> Format: [emph]xxx[/emph]
            $searchPattern[] = "%\[emph\](.+)\[\/emph\]%Uis";
            $searchReplace[] = "<span style=\"background-color:".$this->info['EmphHintergrundfarbe'].";color:".$this->info['EmphSchriftfarbe'].";\">$1</span>";
        }

        //> Darf BB Code [COLOR] dekodiert werden?
        if ($this->permitted['color']) {
            //> Format: [color=#xxxxxx]xxx[/color]
            $searchPattern[] = "%\[color=(#{1}[0-9a-zA-Z]+?)\](.+)\[\/color\]%Uis";
            $searchReplace[] = '<span style="color:$1">$2</span>';
        }

        //> Darf BB Code [SIZE] dekodiert werden?
        if ($this->permitted['size']) {
            //> Format: [size=xx]xxx[/size]
            $callbackPatterns[] = "%\[size=(?P<size>[0-9]+?)\](?P<text>.+)\[\/size\]%Uis";
            $callbacks[] = array($this, 'styleFontSize');
        }

        $textBlockTypes = array();
        $possibleTextBlockTypes = array('ktext', 'quote', 'list');

        foreach ($possibleTextBlockTypes as $possibleTextBlockType) {
            if ($this->permitted[$possibleTextBlockType]) {
                $this->initTextBlockType($possibleTextBlockType);
                $textBlockTypes[] = $possibleTextBlockType;
            }
        }

        //> Darf BB Code Text Blöcke (ktext, quote, usw.) decodiert werden?
        if (!empty($textBlockTypes)) {
            //> Format: [ktext=xxx]
            $callbackPatterns[] = '%\[(?P<close>/)?(?P<type>' . implode('|', $textBlockTypes) . ')(?:=(?P<title>[^\]]*))?\]%siU';
            $callbacks[] = array($this, 'addTextBlock');
        }

        //> Darf BB Code [IMG] dekodiert werden?
        if ($this->permitted['img']) {
            $callbackPatterns[] = "%\[img(?:=(?P<options>[a-z0-9;]+))?\](?P<url>[-a-zA-Z0-9@:\%_\+,.~#?&//=]+?)\[\/img\]%Ui";
            $callbacks[] = array($this, 'formatImage');
        }

        //> Darf BB Code [SCREENSHOT] dekodiert werden?
        if ($this->permitted['screenshot']) {
            $callbackPatterns[] = "%\[shot(?:=(?P<options>left|right))\](?P<url>[-a-zA-Z0-9@:\%_\+.~#?&//=]+?)\[\/shot\]%Ui";
            $callbacks[] = array($this, 'formatScreenShot');

        }

        //> Farf BB Code [VIDEO] dekodiert werden?
        if ($this->permitted['video']) {
            //> Format: [video=xxx]xxx[/video]
            $callbackPatterns[] = "%\[video=(?P<type>google|youtube|myvideo|gametrailers)\](?P<id>.+)\[\/video\]%Uis";
            $callbacks[] = array($this, 'formatVideo');
        }

        //> Darf BB Code [COUNTDOWN] dekodiert werden?
        if ($this->permitted['countdown']) {
            // Format: [countdown=Std:Min:Sek]TT.MM.JJJJ[/countdown] oder [countdown]TT.MM.JJJJ[/countdown]
            $callbackPatterns[] = "%\[countdown(?:=(?P<time>\d\d:\d\d:\d\d))\](?P<date>\d\d\.\d\d\.\d{4})\[\/countdown\]%Uis";
            $callbacks[] = array($this, 'formatCountDown');
        }

        ###############################################

        //> Darf BB Code [FLASH] dekodiert werden?
        if ($this->permitted['flash']) {
            //> Format: [flash]*[/flash] oder [flash width='123' height="34"]*[/flash]
            $callbackPatterns[] = "%\[flash(?P<options>( \w+=('|\"|&quot;)\d+\g{-1})*)](?P<url>(?:http|https|ftp)://[a-z-0-9@:\%_\+.~#\?&/=,;]+)\[/flash]%i";
            $callbacks[] = array($this, 'formatFlash');
        }

        //Konfigurierte Patterns ausführen
        $string = preg_replace($searchPattern, $searchReplace, $string);
        foreach ($callbackPatterns as $key => $callbackPattern) {
            $string = preg_replace_callback($callbackPattern, $callbacks[$key], $string);
        }

        //> Darf BB Code [QUOTE] dekodiert werden?
        if ($this->permitted['quote']) {
            $string = $this->formatQuotes($string);
        }

        //> Darf BB Code [KTEXT] decodiert werden?
        if ($this->permitted['ktext']) {
            $string = $this->formatFoldingText($string);
        }

        if ($this->permitted['list']) {
            $string = $this->formatList($string);
        }

        if (!empty($textBlockTypes)) {
            //> Nicht gefundene/verarbeitet TextBlöcke Paare wieder darstellen.
            //> Format: [list:1=xxx], [list:1] oder [/list:1]
            $string = preg_replace(
                '%\[(/?' . implode('|', $textBlockTypes) . '):(?:[0-9])(=[^[/]*)?\]%siU',
                '[$1$2]',
                $string
            );
        }

        //> Smilies Filtern.
        $string = $this->replaceSmileys($string);

        //> Zum schluss die blöcke die verschlüsselt wurden wieder entschlüsseln und Parsen.
        if (!empty($codeTypes)) {
            $string = preg_replace_callback(
                '%\[(?P<type>' . implode('|', $codeTypes) . ')(?:=(?P<options>.+))?\](?P<content>.+)\[\/(?P=type)\]%siU',
                array($this, 'decode_codec'),
                $string
            );
        }

        $this->reset();

        return $string;
    }
}
