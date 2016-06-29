<?php
#   Copyright by Manuel
#   Support www.ilch.de

defined ('main') or die ( 'no direct access' );

include __DIR__ . '/common.php';

$count_query_xyzXYZ = 0;

function db_connect () {
  if (defined('CONN')) {
    return;
  }
  define ( 'CONN', @mysql_pconnect(DBHOST, DBUSER, DBPASS));
  $db = @mysql_select_db(DBDATE, CONN);

  if (!CONN) {
    die('Verbindung nicht m&ouml;glich, bitte pr&uuml;fen Sie ihre mySQL Daten wie Passwort, Username und Host<br />');
  }
  if ( !$db ) {
    die ('Kann Datenbank "'.DBDATE.'" nicht benutzen : ' . mysql_error(CONN));
  }
  if (function_exists('mysql_set_charset') and version_compare(mysql_get_server_info(CONN), '5.0.7') !== -1) {
      //Für ältere Installation die init.php nachladen
      if (!defined('ILCH_DB_CHARSET') && file_exists('include/includes/init.php')) {
          require_once 'include/includes/init.php';
      }
      mysql_set_charset(ILCH_DB_CHARSET, CONN);
  }
  $timeZoneSetted = false;
  if (function_exists('date_default_timezone_get')) {
    $timeZoneSetted = mysql_query('SET time_zone = "' . date_default_timezone_get() . '"');
  }
  if (!$timeZoneSetted && version_compare(PHP_VERSION, '5.1.3')) {
    $timeZoneSetted = mysql_query('SET time_zone = "' . date('P') . '"');
  }
}

function db_close () {
  mysql_close ( CONN );
}

function db_check_error (&$r, $q) {
  if (!$r AND mysql_errno(CONN) <> 0 AND function_exists('is_coadmin') AND is_coadmin()) {
  	// var_export (debug_backtrace(), true)
    echo('<font color="#FF0000">MySQL Error:</font><br>'.mysql_errno(CONN).' : '.mysql_error(CONN).'<br>in Query:<br>'.$q.'<pre>'.debug_bt().'</pre>');
  }
  return ($r);
}

function db_query ($q) {

  global $count_query_xyzXYZ;
  $count_query_xyzXYZ++;

  if (preg_match ("/^UPDATE `?prefix_\S+`?\s+SET/is", $q)) {
    $q = preg_replace("/^UPDATE `?prefix_(\S+?)`?([\s\.,]|$)/i","UPDATE `".DBPREF."\\1`\\2", $q);
  } elseif (preg_match ("/^INSERT INTO `?prefix_\S+`?\s+[a-z0-9\s,\)\(]*?VALUES/is", $q)) {
    $q = preg_replace("/^INSERT INTO `?prefix_(\S+?)`?([\s\.,]|$)/i", "INSERT INTO `".DBPREF."\\1`\\2", $q);
  } else {
    $q = preg_replace("/prefix_(\S+?)([\s\.,]|$)/", DBPREF."\\1\\2", $q);
  }

  return (db_check_error(@mysql_query($q, CONN), $q));
}

function db_result ($erg, $zeile=0, $spalte=0) {
  return (mysql_result ($erg,$zeile,$spalte));
}

function db_fetch_assoc ($erg) {
  return (mysql_fetch_assoc($erg));
}

function db_fetch_row ($erg) {
  return (mysql_fetch_row($erg));
}

function db_fetch_object ($erg) {

  return (mysql_fetch_object($erg));
}

function db_num_rows ($erg) {
  return (mysql_num_rows ($erg));
}

function db_last_id () {
	return ( mysql_insert_id (CONN));
}

function db_count_query ( $query ) {
  return (db_result(db_query($query),0));
}

function db_check_erg ($erg) {
  if ($erg == false OR @db_num_rows($erg) == 0) {
    exit ('Es ist ein Fehler aufgetreten');
  }
}

function db_num_fields($erg) {
    return mysql_num_fields($erg);
}

function db_affected_rows() {
    return mysql_affected_rows(CONN);
}

function db_error() {
    return mysql_error(CONN);
}

function db_escape_string($unescaped) {
    return mysql_real_escape_string($unescaped, CONN);
}