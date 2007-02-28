<?php

define ( 'main' , TRUE );
require_once('include/includes/config.php');
require_once('include/includes/func/db/mysql.php');

db_connect();

$sql_file = implode('',file('update_11e_zu_11f.sql'));
$sql_file = preg_replace ("/(\015\012|\015|\012)/", "\n", $sql_file);
$sql_statements = explode(";\n",$sql_file);
foreach ( $sql_statements as $sql_statement ) {
  if ( trim($sql_statement) != '' ) {
    echo '<pre>'.$sql_statement.'</pre>';
    $e = db_query($sql_statement);
    if (!$e) { echo '<font color="#FF0000"><b>Es ist ein Fehler aufgetreten</b></font>, bitte alles auf dieser Seite kopieren und auf ilch.de im Forum fragen...:<div style="border: 1px dashed grey; padding: 5px; background-color: #EEEEEE">'. mysql_error().'<hr>'.$sql_statement.'</div><br /><br />'; }
    echo '<hr>';
	}
}


echo 'Datenbank erfolgreich upgedatet!';
echo 'Das Updatefile "update_11e_zu_11f.sql" kann nun gel&ouml;scht werden und sollte kein 2.mal aufgerufen werden!';

?>
