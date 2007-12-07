<?php
#   Copyright by: Manuel Staechele
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );
defined ('admin') or die ( 'only admin access' );


if (!is_admin()) {
  $design = new design ( 'Admins Area', 'Admins Area', 2 );
  $design->header();
  echo 'Dieser Bereich ist nicht fuer dich...';
  $design->footer();
  exit();
}

function get_def($dbname, $table) {
    $def = "";
    if(isset($_POST['drop'])) {
        $def .= "DROP TABLE IF EXISTS $table;\n";
    }
    $def .= "CREATE TABLE $table (\n";
    $result = mysql_db_query($dbname, "SHOW FIELDS FROM $table",CONN);
    while($row = mysql_fetch_array($result)) {
        $def .= "    `".$row['Field']."` ".$row['Type'];
        if ($row["Default"] != "") $def .= " DEFAULT '".$row['Default']."'";
        if ($row["Null"] != "YES") $def .= " NOT NULL";
       	if ($row['Extra'] != "") $def .= " ".$row['Extra'];
        	$def .= ",\n";
     }
     $def = ereg_replace(",\n$","", $def);
     $result = mysql_db_query($dbname, "SHOW KEYS FROM $table",CONN);
     while($row = mysql_fetch_array($result)) {
          $kname = $row['Key_name'];
          if(($kname != "PRIMARY") && ($row['Non_unique'] == 0)) $kname="UNIQUE|$kname";
          if(!isset($index[$kname])) $index[$kname] = array();
          $index[$kname][] = "`".$row['Column_name']."`";
     }
     while(list($x, $columns) = @each($index)) {
          $def .= ",\n";
          if($x == "PRIMARY") $def .= "   PRIMARY KEY (" . implode($columns, ", ") . ")";
          else if (substr($x,0,6) == "UNIQUE") $def .= "   UNIQUE ".substr($x,7)." (" . implode($columns, ", ") . ")";
          else $def .= "   KEY $x (" . implode($columns, ", ") . ")";
     }
     $result = mysql_db_query($dbname, "SHOW TABLE STATUS FROM $dbname LIKE '$table'",CONN);
     $auto_inc = mysql_result($result,0,'Auto_increment');
     $def .= "\n)".($auto_inc != '' ? " AUTO_INCREMENT=$auto_inc":'').";";
     return (stripslashes($def));
}

function get_content($dbname, $table) {
     $content="";
     $result = mysql_db_query($dbname, "SHOW FIELDS FROM $table",CONN);
     $fields = '(';
     while($row = mysql_fetch_row($result)){
         $fields .= '`'.$row[0].'`,';
     }
     $fields = substr($fields,0,-1).')';
     $result = mysql_db_query($dbname, "SELECT * FROM $table",CONN);
     while($row = mysql_fetch_row($result)) {
         $insert = "INSERT INTO $table $fields VALUES (";
         for($j=0; $j<mysql_num_fields($result);$j++) {
            if(!isset($row[$j])) $insert .= "NULL,";
            else if($row[$j] != "") $insert .= "'".addslashes($row[$j])."',";
            else $insert .= "'',";
         }
         $insert = ereg_replace(",$","",$insert);
         $insert .= ");\n";
         $content .= $insert;
     }
     return $content;
}

if (!empty($_POST['sendBackup']) AND $_POST['sendBackup'] == 'yes' AND isset($_POST['gelesen']) AND $_POST['gelesen'] == 'yes') {
  #
  ##
  ###
  #### start backup
  /*

  phpMyBackup v.0.4 Beta - Documentation
  Homepage: http://www.nm-service.de/phpmybackup
  Copyright (c) 2000-2001 by Holger Mauermann, mauermann@nm-service.de

  phpMyBackup is distributed in the hope that it will be useful for you, but
  WITHOUT ANY WARRANTY. This programm may be used freely as long as all credit
  and copyright information are left intact.

  */

  $version = "0.4 beta";
  $cur_time=date("Y-m-d H:i");
	$newfile = "-- Dump created with 'phpMyBackup v.$version' on $cur_time\r\n";
	$tables = db_list_tables( DBDATE );
	$num_tables = @db_num_rows($tables);
	$i = 0;
	while($i < $num_tables) {
	   $table = db_tablename($tables, $i);
	   if (isset($_POST['prefix']) AND strpos($table,DBPREF) === false) {
	       $i++;
           continue;
	   }
	   $newfile .= "\n-- ----------------------------------------------------------\n--\n";
	   $newfile .= "-- structur for table '$table'\n--\n";
	   $newfile .= get_def( DBDATE ,$table);
	   $newfile .= "\n\n";
	   $newfile .= "--\n-- data for table '$table'\n--\n";
	   $newfile .= get_content( DBDATE ,$table);
	   $newfile .= "\n\n";
	   $i++;
	}
  $cod = $_POST['cod'] == 'ansi' ? 'ansi' : 'utf8';
  if ($cod == 'utf8') {
      $newfile = utf8_encode($newfile);
  }
  if (isset($_POST['prefix'])) {
      $prefix = '_'.str_replace('_','',DBPREF);
  } else {
      $prefix = '';
  }
  $name = 'ilch_11_'.date('Y-m-d').'_'.$cod.$prefix.'.sql';
  if ($_POST['backuptype'] == 'backupdir' OR $_POST['backuptype'] == 'both') {
      $fh = @fopen('include/backup/'.$name,'w+');
      if ($fh) {
          fwrite($fh,$newfile);
          fclose($fh);
          @chmod('include/backup/'.$name,0777);
          $wdtxt = 'Backup erfolgreich angelegt';
      } else {
          $wdtxt = 'Backup NICHT angelegt';
      }
  }
  if ($_POST['backuptype'] == 'download' OR $_POST['backuptype'] == 'both') {
      header("Content-type: application/octet-stream");
      header("Content-disposition: attachment; filename=".$name);
      header("Pragma: no-cache");
      header("Expires: 0");
      echo $newfile;
  } else {
      $design = new design ( 'Admins Area', 'Admins Area', 2 );
      $design->header();
      wd('admin.php?backup',$wdtxt,5);
      $design->footer();
  }
  #### ende backup
  ###
  ##
  #

} else {
  $design = new design ( 'Admins Area', 'Admins Area', 2 );
  $design->header();
  $tpl = new tpl ('backup', 1);
  $tpl->out(0);
  $design->footer();
}
?>