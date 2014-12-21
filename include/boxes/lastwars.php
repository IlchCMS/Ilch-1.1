<?php
#   Copyright by Manuel
#   Support www.ilch.de


defined ('main') or die ( 'no direct access' );

$farbe = '';
$farb2 = '';

echo '<div class="tdweight100 ilch_float_l">';
$erg = db_query('SELECT * FROM prefix_wars WHERE status = "3" ORDER BY datime DESC LIMIT 3');
while ($row = db_fetch_object($erg) ) {
	$row->tag = ( empty($row->tag) ? $row->gegner : $row->tag );

  if ($row->wlp == 1) {
    $bild = 'include/images/icons/win.gif';

  } elseif ($row->wlp == 2) {
    $bild = 'include/images/icons/los.gif';

  } elseif ($row->wlp == 3) {
    $bild = 'include/images/icons/pad.gif';

  }

	
	echo '<div class="tdweight90 text-left ilch_float_l">'.get_wargameimg($row->game).'  <a href="index.php?wars-more-'.$row->id.'">'.$row->owp.' '.$lang['at2'].' '.$row->opp.' - '.$row->tag.'</a></div>';
	echo '<div class="tdweight10 text-right ilch_float_r"><img src="'.$bild.'"></div>';
}
echo '</div>';
?>