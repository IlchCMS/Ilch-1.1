<?php
#   Copyright by Manuel
#   Support www.ilch.de
defined ('main') or die ( 'no direct access' );

include __DIR__ . '/common.php';

$count_query_xyzXYZ = 0;

function db_connect () {
    global $mysqliConnection;
    if ($mysqliConnection instanceof mysqli) {
        return;
    }
    $mysqliConnection = @mysqli_connect(DBHOST, DBUSER, DBPASS);

    if (!$mysqliConnection) {
        die('Verbindung nicht m&ouml;glich, bitte pr&uuml;fen Sie ihre mySQL Daten wie Passwort, Username und Host<br />');
    }
    if (!@mysqli_select_db($mysqliConnection, DBDATE)) {
        die ('Kann Datenbank "' . DBDATE . '" nicht benutzen : ' . mysqli_error($mysqliConnection));
    }

    $mysqlServerVersion = mysqli_get_server_version($mysqliConnection);
    if (version_compare($mysqlServerVersion, '5.0.7') !== -1) {
        //Für ältere Installation die init.php nachladen
        if (!defined('ILCH_DB_CHARSET') && file_exists('include/includes/init.php')) {
            require_once 'include/includes/init.php';
        }
        mysqli_set_charset($mysqliConnection, ILCH_DB_CHARSET);
    }

    if (version_compare($mysqlServerVersion, '5.7.0')) {
        $sqlMode = db_result(mysqli_query($mysqliConnection, 'SELECT @@SESSION.sql_mode'), 0);
        if (strpos($sqlMode, 'NO_ZERO_IN_DATE') !== false || strpos($sqlMode, 'NO_ZERO_DATE') !== false) {
            $newSqlMode = preg_replace('~\b(NO_ZERO_IN_DATE|NO_ZERO_DATE)\b,?~', '', $sqlMode);
            mysqli_query($mysqliConnection, 'SET sql_mode="' . $newSqlMode . '"');
        }
    }

    $timeZoneSet = false;
    if (function_exists('date_default_timezone_get')) {
        $timeZoneSet = mysqli_query($mysqliConnection, 'SET time_zone = "' . date_default_timezone_get() . '"');
    }
    if (!$timeZoneSet && version_compare(PHP_VERSION, '5.1.3')) {
        mysqli_query($mysqliConnection, 'SET time_zone = "' . date('P') . '"');
    }
}

function db_close () {
    global $mysqliConnection;
    mysqli_close($mysqliConnection);
}

function db_check_error (&$r, $q) {
    global $mysqliConnection;
  if (!$r AND mysqli_errno($mysqliConnection) != 0 AND function_exists('is_coadmin') AND is_coadmin()) {
    echo('<span style="color:#FF0000">MySQL Error:</span><br>'.mysqli_errno($mysqliConnection).' : '
        .mysqli_error($mysqliConnection).'<br>in Query:<br>'.$q.'<pre>'.debug_bt().'</pre>');
  }
  return ($r);
}

function db_query ($q) {
  global $count_query_xyzXYZ, $mysqliConnection;;
  $count_query_xyzXYZ++;

  if (preg_match ("/^UPDATE `?prefix_\S+`?\s+SET/is", $q)) {
    $q = preg_replace("/^UPDATE `?prefix_(\S+?)`?([\s\.,]|$)/i","UPDATE `".DBPREF."\\1`\\2", $q);
  } elseif (preg_match ("/^INSERT INTO `?prefix_\S+`?\s+[a-z0-9\s,\)\(]*?VALUES/is", $q)) {
    $q = preg_replace("/^INSERT INTO `?prefix_(\S+?)`?([\s\.,]|$)/i", "INSERT INTO `".DBPREF."\\1`\\2", $q);
  } else {
    $q = preg_replace("/prefix_(\S+?)([\s\.,]|$)/", DBPREF."\\1\\2", $q);
  }

  return db_check_error(mysqli_query($mysqliConnection, $q), $q);
}

function db_result ($erg, $zeile=0, $spalte=0) {
    if ($erg instanceof mysqli_result && $erg->num_rows > $zeile) {
        $erg->data_seek($zeile);
        $row = $erg->fetch_array();
        if (isset($row[$spalte])) {
            return $row[$spalte];
        }
    }

    return false;
}

function db_fetch_assoc ($erg) {
    if ($erg instanceof mysqli_result) {
        return $erg->fetch_assoc();
    }
    return false;
}

function db_fetch_row ($erg) {
    if ($erg instanceof mysqli_result) {
        return $erg->fetch_row();
    }
    return false;
}

function db_fetch_object ($erg) {
    if ($erg instanceof mysqli_result) {
        return $erg->fetch_object();
    }
    return false;
}

function db_num_rows ($erg) {
    if ($erg instanceof mysqli_result) {
        return $erg->num_rows;
    }
    return false;
}

function db_num_fields($erg) {
    return mysqli_num_fields($erg);
}

function db_affected_rows() {
    global $mysqliConnection;
    return mysqli_affected_rows($mysqliConnection);
}

function db_last_id () {
    global $mysqliConnection;
    return mysqli_insert_id($mysqliConnection);
}

function db_error() {
    global $mysqliConnection;
    return mysqli_error($mysqliConnection);
}

function db_escape_string($unescaped) {
    global $mysqliConnection;
    return mysqli_real_escape_string($mysqliConnection, $unescaped);
}
