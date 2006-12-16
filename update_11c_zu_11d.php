<?php


$sql_file = implode('',file('update_11c_zu_11d.sql'));
$sql_file = preg_replace ("/(\015\012|\015|\012)/", "\n", $sql_file);
$sql_statements = explode(";\n",$sql_file);
foreach ( $sql_statements as $sql_statement ) {
  if ( trim($sql_statement) != '' ) {
    #echo '<pre>'.$sql_statement.'</pre><hr>';
    db_query($sql_statement);
	}
}


echo 'Datenbank erfolgreich upgedatet!';
echo 'Das Updatefile "update_11c_zu_11d.sql" kann geloescht werden!';

?>