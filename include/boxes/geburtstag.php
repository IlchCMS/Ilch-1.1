<?php 
//Copyright by Hannes Wunderli
//www.fast-death.com
// v1.2

defined ('main') or die ( 'no direct access' );

//----------------------------------- Einstellungen-----------------------------------

$limit = 3;        //wieviele Geburtstage Angezeigt werden sollen.
$recht = -1;       //Anzeige Modus 0 = Alle / -1 Alle die mehr als Memberrechte haben usw.
$showavatars = 1;  //Wenn 1 werden die Avatare wenn vorhanden angezeigt.

//------------------------------------------------------------------------------------

$count = 0;

$timestamp = time();
$akttime = date('Y-m-d',$timestamp);

function get_gebtage ($datum) {
  list($y, $m, $d) = explode('-', $datum);
  return ($d.'.'.$m.'.'.$y);
}

# DIE krasse Abfrage :-)...   von Manue
$q = "SELECT name, id, avatar,
CASE WHEN ( MONTH(gebdatum) < MONTH(NOW()) ) OR ( MONTH(gebdatum) <= MONTH(NOW()) AND DAYOFMONTH(gebdatum) < DAYOFMONTH(NOW()) ) THEN
gebdatum + INTERVAL (YEAR(NOW()) - YEAR(gebdatum) + 1) YEAR
ELSE
gebdatum + INTERVAL (YEAR(NOW()) - YEAR(gebdatum)) YEAR
END
AS gebtage
FROM prefix_user WHERE gebdatum > 0000-00-00 AND recht <= ".$recht." ORDER BY gebtage LIMIT ".$limit;

$erg = db_query($q);

echo '<div class="tdweight100 text-center">';

$i = 1;

while($row = db_fetch_object($erg)) {
 
  if($akttime == $row->gebtage)  {
   echo ''.$lang['today'].'&nbsp;'.$lang['had'].'&nbsp;<br><a class="box" href="index.php?user-details-'.$row->id.'">'.$row->name.'</a><br>'.$lang['birthday'].'<br><img border="0" src="include/images/icons/birthday.gif">';
  } else {
    $gebtage = get_gebtage ($row->gebtage);
    echo '<a class="box" href="index.php?user-details-'.$row->id.'">'.$row->name.'</a><br>'.$lang['had'].' '.$lang['on'].' '.$gebtage.' '.$lang['birthday'];
    if ($showavatars && $row->avatar) {
      echo '<br><img class="ilch_geb_avatar" border="0" src="'.$row->avatar.'">';
    }
    echo "";
  }
  
  if ($i<$limit) {
    echo '<br>';
  }
  $i++;
}

echo '</div>';
?>