<?php

#   Code Copyright by Manuel Staechele   
#   Support: www.ilch.de                          

defined ('main') or die ( 'no direct access' );

if (empty($_GET['sum'])) {

  error_reporting(0);
	$heute = date ('y-m-d');
  $time = time();
  $daysec = 86400;
  $weekdays = 7;
  $mth = 30;
  $day = $time - $daysec; 	
  $ges_visits = db_result(db_query("SELECT SUM(count) FROM prefix_counter"),0);
	$ges_heute  = @db_result(db_query("SELECT count FROM prefix_counter WHERE date = '".$heute."'"),0);
	$ges_gestern = @db_result(db_query('SELECT count FROM prefix_counter WHERE date < "'.$heute.'" ORDER BY date DESC LIMIT 1'),0);  
	$news  = @db_result(db_query("SELECT count(NEWS_ID) FROM prefix_news"),0);
  $gbook  = @db_result(db_query("SELECT count(ID) FROM prefix_gbook"),0);
	$posts  = @db_result(db_query("SELECT count(ID) FROM prefix_posts"),0);
	$topic  = @db_result(db_query("SELECT count(ID) FROM prefix_topics"),0);
  $pollge = db_result(db_query("SELECT COUNT(poll_id) FROM prefix_poll WHERE recht = '2' "),0);
  $komge = db_result(db_query("SELECT COUNT(ID) FROM `prefix_koms`"),0);
  $shbox = db_result(db_query("SELECT COUNT(ID) FROM `prefix_shoutbox`"),0);    
  $downloads  = @db_result(db_query("SELECT count(ID) FROM prefix_downloads"),0);
  $bges = @db_count_query("SELECT COUNT(*) FROM prefix_gallery_imgs");
  $ubges = @db_count_query("SELECT COUNT(*) FROM prefix_usergallery");
  $links = @db_count_query("SELECT COUNT(ID) FROM prefix_links");
  $partner = @db_count_query("SELECT COUNT(ID) FROM prefix_partners");
	$gesuser  = @db_result(db_query("SELECT count(ID) FROM prefix_user"),0);
	$gesch1  = @db_result(db_query("SELECT count(ID) FROM prefix_user where geschlecht = 1"),0);
	$gesch2  = @db_result(db_query("SELECT count(ID) FROM prefix_user where geschlecht = 2"),0);
  $gesch3  = @db_result(db_query("SELECT count(ID) FROM prefix_user where geschlecht = 0"),0);
	$maxErg = db_query('SELECT MAX(count) FROM `prefix_counter`');
  $max_in = db_result($maxErg,0); 
  $useroneregist = db_result(db_query('SELECT regist FROM prefix_user WHERE id = 1'),0);
  $sincesec = $time - $useroneregist;
  $sinceday = floor($sincesec / $daysec);
  $dayvisits = floor($ges_visits / $sinceday)+1;
  $mthvisits = floor($dayvisits * $mth);
     
echo'<ul class="list-group">
  <li class="list-group-item list-group-item-info">
<table width="100%"><tr>
    <td>Seite Online seit</td>
<td class="text-right"><strong>'.$sinceday.' Tagen</strong></td>
</tr>
<tr>
    <td>Mitglieder Gesamt</td>
<td class="text-right"><strong>'.$gesuser.'</strong></td>
</tr>
</table>
  </li>
</ul>
<legend><h5><strong>Besucher</strong></h5></legend>
<ul class="list-group">
  <li class="list-group-item list-group-item-warning">
<table width="100%">
<tr>
    <td>'.$lang['today'].'</td>
<td class="text-right"><strong>'.$ges_heute.'</strong></td>
</tr>
<tr>
    <td>'.$lang['yesterday'].'</td>
<td class="text-right"><strong>'.$ges_gestern.'</strong></td>
</tr>
<tr>
    <td>Rekord</td>
<td class="text-right"><strong>'.$max_in.' am Tag</strong></td>
</tr>
<tr>
    <td>&#216 Tag</td>
<td class="text-right"><strong>'.$dayvisits.'</strong></td>
</tr>
<tr>
    <td>&#216 Monat</td>
<td class="text-right"><strong>'.$mthvisits.'</strong></td>
</tr>
<tr>
    <td>Gesamt</td>
<td class="text-right"><strong>'.$ges_visits.'</strong></td>
</tr>
</table>
  </li>
</ul>
<legend><h5><strong>Eintr&auml;ge</strong></h5></legend>
<ul class="list-group">
  <li class="list-group-item list-group-item-success">
<table width="100%">
<tr>
    <td>News Eintr&auml;ge</td>
<td class="text-right"><strong>'.$news.'</strong></td>
</tr>
<tr>
    <td>Forum Topic</td>
<td class="text-right"><strong>'.$topic.'</strong></td>
</tr>
<tr>
    <td>Forum Posts</td>
<td class="text-right"><strong>'.$posts.'</strong></td>
</tr>
<tr>
    <td>G-Book Eintr&auml;ge</td>
<td class="text-right"><strong>'.$gbook.'</strong> <a href="admin.php?gbook" rel="tooltip" title="Eintr&auml;ge verwalten"><span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a></td>
</tr>
<tr>
    <td>Kommentare Gesamt</td>
<td class="text-right"><strong>'.$komge.'</strong></td>
</tr>
<tr>
    <td>Umfragen</td>
<td class="text-right"><strong>'.$pollge.'</strong></td>
</tr>
<tr>
    <td>Shoutbox Eintr&auml;ge</td>
<td class="text-right"><strong>'.$shbox.'</strong></td>
</tr>
</table>
  </li>
</ul>
<legend><h5><strong>Medien</strong></h5></legend>
<ul class="list-group">
  <li class="list-group-item list-group-item-danger">
<table width="100%">
<tr>
    <td>Gallerie</td>
<td class="text-right"><strong>'.$bges.' Image</strong> <a href="admin.php?gallery" rel="tooltip" title="Eintr&auml;ge verwalten"><span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a></td>
</tr>
<tr>
    <td>User Gallerie</td>
<td class="text-right"><strong>'.$ubges.' Image</strong></td>
</tr>
<tr>
    <td>Downloads</td>
<td class="text-right"><strong>'.$downloads.' Eintr&auml;ge</strong> <a href="admin.php?archiv-downloads" rel="tooltip" title="Eintr&auml;ge verwalten"><span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a></td>
</tr>
<tr>
    <td>Links</td>
<td class="text-right"><strong>'.$links.' Eintr&auml;ge</strong> <a href="admin.php?archiv-links" rel="tooltip" title="Eintr&auml;ge verwalten"><span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a></td>
</tr>
<tr>
    <td>Partner</td>
<td class="text-right"><strong>'.$partner.' Eintr&auml;ge</strong> <a href="admin.php?archiv-partners" rel="tooltip" title="Eintr&auml;ge verwalten"><span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a></td>
</tr>
</table>
  </li>
</ul>';
}
?>