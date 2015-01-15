<?php  
#   Copyright by Manuel Staechele 
#   Support www.ilch.de 


defined ('main') or die ( 'no direct access' ); 

 $abf = "SELECT id, title, FROM_UNIXTIME(time,'%d.%m.%Y') as zeit FROM prefix_kalender WHERE time >= UNIX_TIMESTAMP() AND recht >= {$_SESSION['authright']} ORDER BY time LIMIT 3";  
 $erg = db_query($abf); 
 if ( @db_num_rows($erg) == 0 ) { 
    echo '<ul class="list-group list-group-boxen text-center"><div class="alert alert-warning" role="alert">Aktuell sind keine Termine vorhanden<br><a class="text-warning" href="admin.php?kalender"><strong>neuen Termin eintragen</strong></a></div></ul>'; 
} 
echo '<ul class="list-group list-group-boxen text-left">'; 
  while ($row = db_fetch_object($erg)) { 
    echo '<a href="admin.php?kalender&edit='.$row->id.'" class="list-group-item"><h5><strong><i class="fa fa-angle-double-right"></i> '.$row->title.'</strong></h5><small>Termin am: '.$row->zeit.'</small></a>'; 
  } 
  echo '</ul>'; 
?>


