<?php
#   Copyright by Manuel
#   Support www.ilch.de


defined ('main') or die ( 'no direct access' );
$akttime = date('Y-m-d');
$erg = @db_query("SELECT DATE_FORMAT(datime,'%d.%m.%y - %H:%i') as time,tag,gegner, id, game FROM prefix_wars WHERE status = 2 AND datime > '".$akttime."' ORDER BY datime");
if ( @db_num_rows($erg) == 0 ) {
	echo '<ul class="list-group list-group-boxen text-center"><div class="alert alert-warning" role="alert">Aktuell kein War geplant<br><a class="text-warning" href="admin.php?wars-next"><strong>Next-War eintragen</strong></a></div></ul>';
} else {
echo '<ul class="list-group list-group-boxen text-left">';
	while ($row = @db_fetch_object($erg) ) {
		$row->tag = ( empty($row->tag) ? $row->gegner : $row->tag );

echo '<a href="admin.php?wars-next&pkey='.$row->id.'" class="list-group-item"><strong><i class="fa fa-angle-double-right"></i> '.$row->tag.'</strong><span class="label label-success pull-right">'.$row->time.' Uhr</span></a>';
	}
echo '</ul>';
}

?>