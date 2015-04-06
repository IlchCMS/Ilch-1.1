<?php

//  Copyright by Manuel
//  Support www.ilch.de

defined ('main') or die ( 'no direct access' );

$farbe = '';
$farb2 = '';

echo '<div class="tdweight100">';
$erg = db_query('SELECT * FROM prefix_wars WHERE status = "3" ORDER BY datime DESC LIMIT 3');
if ( @db_num_rows($erg) == 0 ) {
	echo '<div class="text-center smalfont">'.$lang['noentry'].'</div>';
} else {
while ($row = db_fetch_object($erg) ) {
	$row->tag = ( empty($row->tag) ? $row->gegner : $row->tag );

  if ($row->wlp == 1) {
    $bild = 'include/images/icons/win.gif';

  } elseif ($row->wlp == 2) {
    $bild = 'include/images/icons/los.gif';

  } elseif ($row->wlp == 3) {
    $bild = 'include/images/icons/pad.gif';

  }
	echo '<div class="text-left">'.get_wargameimg($row->game).'  <a href="index.php?wars-more-'.$row->id.'">'.$row->owp.' '.$lang['at2'].' '.$row->opp.' - '.$row->tag.'</a><span class="ilch_float_r"><img src="'.$bild.'" alt=""></span></div>';
}
}
echo '</div>';
?>