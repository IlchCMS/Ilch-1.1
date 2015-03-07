<?php

// Copyright by: Manuel
// Support: www.ilch.de
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

//maximale Anzahl von Queries in extended inserts
define('BACKUP_MAX_INSERTS', 100);

if (!is_admin()) {
    $design = new design('Admins Area', 'Admins Area', 2);
    $design->header();
    echo 'Dieser Bereich ist nicht fuer dich...';
    $design->footer();
    exit();
}

class AbstractBackupWriter {

    function isValid() {
	return true;
    }

    function write($msg) {

    }

    function close() {

    }

}

class FileBackupWriter extends AbstractBackupWriter {

    var $handle;
    var $filename;
    var $valid;

    function FileBackupWriter($filename) {
	if (!is_writable('include/backup/')) {
	    if (!headers_sent()) {
		echo 'Backupverzeichnis ist schreibgesch&uuml;tzt, es wird keine Datei geschrieben.<br />';
		echo '<a href="admin.php?backup">zur&uuml;ck</a><br />';
	    }
	    $this->valid = false;
	} else {
	    $this->filename = 'include/backup/' . $filename;
	    $this->handle = fopen($this->filename, 'w');
	    $this->valid = true;
	}
    }

    function isValid() {
	return $this->valid;
    }

    function write($msg) {
	fwrite($this->handle, $msg, strlen($msg));
    }

    function close() {
	fclose($this->handle);
	@chmod('include/backup/' . $this->filename, 0777);
	if (!headers_sent()) {
	    echo 'Backupdatei ' . $this->filename . ' erfolgreich angelegt.<br />';
	    echo '<a href="admin.php?backup">zur&uuml;ck</a><br />';
	}
    }

}

class BrowserBackupWriter extends AbstractBackupWriter {

    function BrowserBackupWriter($filename) {
	header("Content-type: application/octet-stream");
	header("Content-disposition: attachment; filename=" . $filename);
	header("Pragma: no-cache");
	header("Expires: 0");
    }

    function write($msg) {
	print $msg;
    }

}

class BackupWriter {

    var $writers;
    var $useUtf8;

    function BackupWriter($utf8) {
	$this->writers = array();
	$this->useUtf8 = (bool) $utf8;
    }

    function addWriter($writer) {
	if ($writer->isValid()) {
	    $this->writers[] = $writer;
	}
    }

    function close() {
	foreach ($this->writers as $writer) {
	    $writer->close();
	}
	unset($this);
    }

    function write($msg) {
	if ($this->useUtf8) {
	    $msg = utf8_encode($msg);
	}
	foreach ($this->writers as $writer) {
	    $writer->write($msg);
	}
    }

    function countWriters() {
	return count($this->writers);
    }

}

function get_def($dbname, $table, $writer) {
    $def = "\r\n-- ----------------------------------------------------------\r\n--\r\n";
    $def .= "-- structur for table '$table'\r\n--\r\n";
    if (isset($_POST['drop'])) {
	$def .= "DROP TABLE IF EXISTS `$table`;\n";
    }
    mysql_select_db($dbname);
    $def .= "CREATE TABLE `$table` (\n";
    $result = mysql_query("SHOW FIELDS FROM `$table`", CONN);
    while ($row = mysql_fetch_array($result)) {
	$def .= "    `" . $row['Field'] . "` " . $row['Type'];
	if ($row["Default"] != "")
	    $def .= " DEFAULT '" . $row['Default'] . "'";
	if ($row["Null"] != "YES")
	    $def .= " NOT NULL";
	if ($row['Extra'] != "")
	    $def .= " " . $row['Extra'];
	$def .= ",\r\n";
    }
    $def = preg_replace('%,\r\n$%', '', $def);
    $result = mysql_query("SHOW KEYS FROM `$table`", CONN);
    while ($row = mysql_fetch_array($result)) {
	$kname = $row['Key_name'];
	if (($kname != "PRIMARY") && ($row['Non_unique'] == 0))
	    $kname = "UNIQUE|$kname";
	if (!isset($index[$kname]))
	    $index[$kname] = array();
	$index[$kname][] = "`" . $row['Column_name'] . "`";
    } while (list($x, $columns) = @each($index)) {
	$def .= ",\r\n";
	if ($x == "PRIMARY")
	    $def .= "   PRIMARY KEY (" . implode($columns, ", ") . ")";
	else if (substr($x, 0, 6) == "UNIQUE")
	    $def .= "   UNIQUE " . substr($x, 7) . " (" . implode($columns, ", ") . ")";
	else
	    $def .= "   KEY $x (" . implode($columns, ", ") . ")";
    }
    $result = mysql_query("SHOW TABLE STATUS FROM `$dbname` LIKE '$table'", CONN);
    $auto_inc = mysql_result($result, 0, 'Auto_increment');
    $def .= "\r\n)" . ($auto_inc != '' ? " AUTO_INCREMENT=$auto_inc" : '') . ";";
    $def .= "\r\n\r\n";
    stripslashes($def);
    $writer->write($def);
}

function get_content($dbname, $table, $writer) {
    mysql_select_db($dbname);
    $writer->write("--\r\n-- data for table '$table'\r\n--\r\n");
    $result = mysql_query("SHOW FIELDS FROM `$table`", CONN);
    $fields = '(';
    while ($row = mysql_fetch_row($result)) {
	$fields .= '`' . $row[0] . '`,';
    }
    $fields = substr($fields, 0, - 1) . ')';
    $result = mysql_query("SELECT * FROM `$table`", CONN);
    $insert_begin = "INSERT INTO `$table` $fields VALUES ";
    $i = 0;
    while ($row = mysql_fetch_row($result)) {
	$insert = '(';
	for ($j = 0; $j < mysql_num_fields($result); $j++) {
	    if (!isset($row[$j]))
		$insert .= "NULL,";
	    else if ($row[$j] != "")
		$insert .= "'" . addcslashes(addslashes($row[$j]), "\n\r") . "',";
	    else
		$insert .= "'',";
	}
	$insert = preg_replace('%,$%', '', $insert);
	$insert .= ");\r\n";
	$writer->write($insert_begin . $insert);
    }
    $writer->write("\r\n\r\n");
}

if (!empty($_POST['sendBackup']) AND $_POST['sendBackup'] == 'yes' AND isset($_POST['gelesen']) AND $_POST['gelesen'] == 'yes') {

    $prefix = isset($_POST['prefix']) ? '_' . str_replace('_', '', DBPREF) : '';
    $utf8 = $_POST['cod'] == 'ansi' ? false : true;
    $cod = $utf8 ? 'utf-8' : 'ansi';
    $name = 'ilch_11_' . date('Y-m-d_H-i') . '_' . $cod . $prefix . '.sql';
    $writer = new BackupWriter($utf8);
    if ($_POST['backuptype'] == 'download' OR $_POST['backuptype'] == 'both') {
	$writer->addWriter(new BrowserBackupWriter($name));
    }
    if ($_POST['backuptype'] == 'backupdir' OR $_POST['backuptype'] == 'both') {
	$writer->addWriter(new FileBackupWriter($name));
    }
    // #
    // ##
    // ### start backup
    /*

      phpMyBackup v.0.4 Beta - Documentation
      Homepage: http://www.nm-service.de/phpmybackup
      Copyright (c) 2000-2001 by Holger Mauermann, mauermann@nm-service.de

      phpMyBackup is distributed in the hope that it will be useful for you, but
      WITHOUT ANY WARRANTY. This programm may be used freely as long as all credit
      and copyright information are left intact.

     */
    if ($writer->countWriters()) {
	$version = "0.4 beta";
	$cur_time = date("Y-m-d H:i");
	$writer->write("-- Dump created with 'phpMyBackup v.$version' on $cur_time\r\n");
	mysql_select_db(DBDATE);
	$tables = array();
	$qry = mysql_query('SHOW TABLES');
	while ($row = mysql_fetch_array($qry)) {
	    $tables[] = $row[0];
	}
	foreach ($tables as $table) {
	    if (isset($_POST['prefix']) AND strpos($table, DBPREF) === false) {
		continue;
	    }
	    get_def(DBDATE, $table, $writer);
	    get_content(DBDATE, $table, $writer);
	}
	$writer->close();
    }
} else {
    $design = new design('Admins Area', 'Admins Area', 2);
    $design->header();
    $tpl = new tpl('backup', 1);
    $tpl->out(0);
    $design->footer();
}
?>