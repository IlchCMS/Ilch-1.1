<?php 
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );

  $title = $allgAr['title'].' :: Statistik';
  $hmenu = 'Statistik';
  $design = new design ( $title , $hmenu );
  $design->header();

	$anzahlShownTage = 7;
	
	echo '<table class="border"><tr><td>';
  echo '<table>';
  echo '<tr class="Chead"><td colspan="3" class="text-center"><strong>Site Statistic</strong></td></tr>';
	
	$max_in = 0;
	$ges = 0;
	$dat = array();
	$max_width = 200;
	
	$maxErg = db_query('SELECT MAX(count) FROM `prefix_counter`');
	$max_in = db_result($maxErg,0);
	
	$erg = db_query ("SELECT count, DATE_FORMAT(date,'%a der %d. %b') as datum FROM `prefix_counter` ORDER BY date DESC LIMIT ".$anzahlShownTage);
	while ($row = db_fetch_row($erg) ) {
	
	  $value = $row[0];

		if ( empty($value) ) {
		  $bwidth = 0;
	  } else {
		  $bwidth = $value/$max_in * $max_width;
		  $bwidth = round($bwidth,0);
		}  
		
		echo '<tr class="Cnorm">';
	  echo '<td>'.$row[1].'</td>';
		echo '<td><table width="'.$bwidth.'">';
		echo '<tr><td height="2" class="border"></td></tr></table>';		
		echo '</td><td class="text-right">'.$value.'</td></tr>';
	  
		$ges += $value;
	}
	
	$gesBesucher = db_query('SELECT SUM(count) FROM prefix_counter');
	$gesBesucher = @db_result($gesBesucher,0);
	
  echo '<tr class="Cmite"><td colspan="3" class="text-center">';
  echo $lang['weeksum'].': <strong>'.$ges.'&nbsp;&nbsp;</strong>';
  echo $lang['wholevisitor'].': <strong>'.$gesBesucher.'</strong> &nbsp;&nbsp; '.$lang['max'].': <strong>'.$max_in.'</strong><br><br>';
	echo '</td></tr></table></td></tr></table>';
  
$design->footer();
?>