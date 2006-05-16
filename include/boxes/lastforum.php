<?php 
#   Copyright by Manuel Staechele
#   Support www.ilch.de

defined ('main') or die ( 'no direct access' );

$query = "SELECT a.id, a.name, a.rep, c.erst as last, c.id as pid, c.time
FROM prefix_topics a
  LEFT JOIN prefix_forums b ON b.id = a.fid
  LEFT JOIN prefix_posts c ON c.id = a.last_post_id
WHERE (b.view >= ".$_SESSION['authright']." OR b.reply >= ".$_SESSION['authright']." OR b.start >= ".$_SESSION['authright'].")
ORDER BY c.time DESC
LIMIT 0,5";
echo '<table>';
$resultID = db_query($query);
while ($row = db_fetch_assoc($resultID)) {
	$row['date'] = date('d.m.y - H:i',$row['time']);
	$row['page'] = ceil ( ($row['rep']+1)  / $allgAr['Fpanz'] );
  echo '<tr><td valign="top"><b> &raquo; </b></td><td><a href="?forum-showposts-'.$row['id'].'-p'.$row['page'].'#'.$row['pid'].'" title="'.$row['date'].'">'.((strlen($row['name'])<18) ? $row['name'] : substr($row['name'],0,15).'...').'<br /><span class="smalfont"> von '.$row['last'].'</span></a></td></tr>';
}
echo '</table>';
?>
