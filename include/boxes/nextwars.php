<?php
#   Copyright by Manuel
#   Support www.ilch.de


defined ('main') or die ( 'no direct access' );
echo '<div class="tdweight100 ilch_float_l">';
$akttime = date('Y-m-d');
$erg = @db_query("SELECT DATE_FORMAT(datime,'%d.%m.%y - %H:%i') as time,tag,gegner, id, game FROM prefix_wars WHERE status = 2 AND datime > '".$akttime."' ORDER BY datime");
if ( @db_num_rows($erg) == 0 ) {
	echo '<span class="text-center smalfont">kein War geplant</span>';
} else {
	while ($row = @db_fetch_object($erg) ) {
		$row->tag = ( empty($row->tag) ? $row->gegner : $row->tag );
		echo '<div class="tdweight10 text-left ilch_float_l"><strong>&raquo;</strong></div>';
		echo '<div class="tdweight90 text-left ilch_float_l">'.get_wargameimg($row->game).'<a class="box" href="index.php?wars-more-'.$row->id.'">'.$row->tag.'</a><br><span class="smalfont">'.$row->time.' Uhr</span></div>';
	}
}
echo '</div>';
?>