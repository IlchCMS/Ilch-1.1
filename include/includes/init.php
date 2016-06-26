<?php
#   Copyright by Manuel
#   Support www.ilch.de
defined('main') or die('no direct access');

if (!version_compare(phpversion(), '5.3.0', '>=')) {
    die('Ilch 1.1Q benötigt mindestens PHP in Version 5.3.0, Deine Version: ' . phpversion());
}

/*
 * Hier werden globale Einstellungen (Konstanten, php Einstellungen etc.) gesetzt, die ggf. versionsabhängige sind
 */
// define some script wide constants
define('ILCH_TIMEZONE', 'Europe/Berlin'); // http://php.net/manual/en/timezones.php
define('ILCH_CHARSET', 'ISO-8859-1');
define('ILCH_DB_CHARSET', 'latin1');
define('ILCH_ENTITIES_FLAGS', defined('ENT_HTML401') ? ENT_COMPAT | ENT_HTML401 : ENT_COMPAT);

// Konfiguration zur Anzeige von Fehlern
// Auf http://www.php.net/manual/de/function.error-reporting.php sind die verfügbaren Modi aufgelistet
@error_reporting(E_ERROR | E_WARNING | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING);

@ini_set('display_errors', 'On');

// Seit php-5.4 ist der default-charset auf Utf-8 standardmäßig eingestellt - ilch1.1 läuft jedoch noch auf ISO-8859-1
// Weiterhin sollte alle Aufrufe von htmlspecialchars mit der Konstante erfolgen
if (@ini_get('default_charset') != ILCH_CHARSET) {
    @ini_set('default_charset', ILCH_CHARSET);
}

// Seit php-5.3 ist eine Angabe der TimeZone Pflicht
// Setzen der Zeitzone, wenn möglich
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set(ILCH_TIMEZONE);
}
