<?php
#   Copyright by Manuel
#   Support www.ilch.de
defined('main') or die('no direct access');
/*
 * Hier werden globale Einstellungen (Konstanten, php Einstellungen etc.) gesetzt, die ggf. versionsabhδngige sind
 */

// define some script wide constants
define('ILCH_TIMEZONE', 'Europe/Berlin'); // http://php.net/manual/en/timezones.php
define('ILCH_CHARSET', 'ISO-8859-1');
define('ILCH_DB_CHARSET', 'latin1');
define('ILCH_ENTITIES_FLAGS', defined('ENT_HTML401') ? ENT_COMPAT | ENT_HTML401 : ENT_COMPAT);

// Konfiguration zur Anzeige von Fehlern
// Auf http://www.php.net/manual/de/function.error-reporting.php sind die verfόgbaren Modi aufgelistet
@error_reporting(E_ERROR | E_WARNING | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING);

@ini_set('display_errors', 'On');

// Seit php-5.4 ist der default-charset auf Utf-8 standardmδίig eingestellt - ilch1.1 lδuft jedoch noch auf ISO-8859-1
// Weiterhin sollte alle Aufrufe von htmlspecialchars mit der Konstante erfolgen
if (@ini_get('default_charset') != ILCH_CHARSET) {
    @ini_set('default_charset', ILCH_CHARSET);
}

// Seit php-5.3 ist eine Angabe der TimeZone Pflicht
// Setzen der Zeitzone, wenn mφglich
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set(ILCH_TIMEZONE);
}
