<?php 
#   Copyright by: Manuel Staechele
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );

  $title = $allgAr['title'].' :: Shoutbox '.$lang['archiv'];
  $hmenu = 'Shoutbox '.$lang['archiv'];
  $design = new design ( $title , $hmenu );
  $design->header();

if (is_siteadmin()) {
  # delete
  if ($menu->getA(1) == 'd' AND is_numeric($menu->getE(1))) {
    db_query("DELETE FROM prefix_shoutbox WHERE id = ".$menu->getE(1));
  }
  # delete all
  if ($menu->get(1) == 'delall') {
    db_query("DELETE FROM `prefix_shoutbox`");
  }
}

$class = 'Cnorm';
echo '<table width="100%" align="center" class="border" cellpadding="2" cellspacing="1" border="0"><tr class="Chead"><td><b>Shoutbox '.$lang['archiv'].'</b></td></tr>';
$erg = db_query('SELECT * FROM `prefix_shoutbox` ORDER BY id DESC');
while ($row = db_fetch_assoc($erg) ) {
 $class = ( $class == 'Cmite' ? 'Cnorm' : 'Cmite' );
  echo '<tr class="'.$class.'"><td>';
  if ( is_siteadmin() ) {
	  echo '<a href="index.php?shoutbox-d'.$row['id'].'"><img src="include/images/icons/del.gif" alt="'.$lang['delete'].'" title="'.$lang['delete'].'"></a>&nbsp;';
	}
	echo '<b>'.$row['nickname'].':</b> '.preg_replace( '/([^\s]{'.$allgAr['sb_maxwordlength'].'})(?=[^\s])/', "$1\n", $row['textarea']).'</td></tr>';
}
echo '</table>';
if (is_siteadmin()) {
  echo '<a href="index.php?shoutbox-delall">'.$lang['clearshoutbox'].'</a>';
}
$design->footer();
?>
