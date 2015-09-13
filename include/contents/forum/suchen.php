<?php 
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );

$title = $allgAr['title'].' :: Save Post';
$hmenu  = $extented_forum_menu.'<a class="smalfont" href="?search">Suchen</a>'.$extented_forum_menu_sufix;
$design = new design ( $title , $hmenu, 1);
$design->header();

?>

<form action="?forum-6" method="POST">
<div class="text-center">
<input type="text" value="<?php echo $such; ?>" name="such" size="30">
<input type="submit" value="Suchen" name="submit">
</div>
</form>
<hr>
<br>

<?php
$i = 0;
if ( ! empty ($such) ) {
  if ( !empty ($jforum) ) {
		$abf = 'SELECT DISTINCT a.tid, b.name
					  FROM `prefix_posts` a
		        INNER JOIN `prefix_topic` b ON a.tid = b.id
						WHERE txt LIKE "%'.$such.'%"';
	  
		$such_string = '<b>Forum</b><br><br>';
		
		$erg = db_query($abf);
	  while ($row = db_fetch_object($erg)) {
			
				$i++;
				$such_string .= '<a href="?forum-2&fid=&tid='.$row->tid.'&such=';
				$such_string .= $such.'">'.$row->name.'</a><br>';
				
		}
	} // ende forum durchsuchen
	if ( !empty($jnews) AND !empty($jforum) ) {
	  $such_string .= '</td><td class="Cnorm ilch_vertikal_top">';
	}
	
	if ( !empty ($jnews) ) {		
		$such_string .= '<b>News</b><br><br>';
		$abf = 'SELECT 
	            titel,id
						FROM 
						  prefix_news
		        WHERE
							text LIKE "%'.$such.'%" 
						ORDER
						  BY time DESC';	
		$erg = db_query($abf);
	  while ($row = db_fetch_object($erg)) {
        $i++;			
				$such_string .= '<a href="?news-1&nid=';
				$such_string .= $row->id.'&such=';
				$such_string .= $such.'">'.$row->titel.'</a><br>';

		}
  }
  if ($i > 0) {
	  echo $i.' Treffer f�r die suche nach: '.$such;
		echo '<table ><tr><td>';
		echo $such_string;
	  echo '</td></tr></table>';
	} else {
	  echo 'Leider wurde keine �bereinstimmung mit dem Suchbegriff '.$such.' gefunden';
	}
}    
				
$design->footer();

?>