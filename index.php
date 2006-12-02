<?php 
#   Copyright by: Manuel Staechele
#   Support: www.ilch.de

   function getmicrotime(){
      list($usec, $sec) = explode(" ",microtime());
      return ((float)$usec + (float)$sec);
   }
    
   $time_start = getmicrotime(); //Am anfang der

define ( 'main' , TRUE );

error_reporting(E_ALL);

session_name  ('sid');
session_start ();

require_once ('include/includes/config.php');
require_once ('include/includes/loader.php');

db_connect();
$allgAr = getAllgAr ();
$menu = new menu();
user_identification();
site_statistic();

require_once ('include/contents/'.$menu->get_url());

db_close();
debug('anzahl sql querys: '.$count_query_xyzXYZ);
debug('',1);

   $time_end = getmicrotime(); //Am ende der Seite
   $time = round($time_end - $time_start,4);
   echo '<div style="position: absolute; top: 0px; left: 0px;">Seite in '.$time." Sekunden generiert</div>";  

?>