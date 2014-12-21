<?php 
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );

$title = $allgAr['title'].' :: Regeln';
$hmenu = 'Regeln';
$design = new design ( $title , $hmenu );
$design->header();





//-----------------------------------------------------------|


  $erg = db_query('SELECT zahl,titel,text FROM `prefix_rules` ORDER BY zahl');
	while ($row = db_fetch_row($erg)) {
			echo '<div class="border"><div class="ilch_case">';
		  echo '<div class="Cmite ilch_casesmall_in"><strong>&sect;'.$row[0].'. &nbsp; '.$row[1].'</strong></div>';
			echo '<div class="ilch_casesmall_in Cnorm">'.bbcode($row[2]).'</div>'; 
			echo '</div></div>';
  } 


$design->footer();

?>

