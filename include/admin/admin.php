<?php
#   Copyright by: Manuel
#   Support: www.ilch.de


defined('main') or die('no direct access');
defined('admin') or die('only admin access');


$design = new design('Admins Area', 'Admins Area', 2);
$design->header();

# script version
$scriptVersion = 11;
$scriptUpdate = 'Q';

# statistik wird bereinigt.
$mon = date('n');
$lastmon = $mon - 1;
$jahr = date('Y');
$lastjahr = $jahr;
if ($lastmon <= 0) {
    $lastmon = 12;
    $lastjahr = $jahr - 1;
}

db_query("DELETE FROM prefix_stats WHERE NOT ((mon = $mon OR mon = $lastmon) AND (yar = $jahr OR yar = $lastjahr))");
db_query("OPTIMIZE TABLE prefix_stats");


$um = $menu->get(1);
switch ($um) {

    default : {
	    ?>
	    <div class="page-header">
	        <h3><img class="ilch_label_bsite" src="include/admin/templates/bootstrap/css/ilch_label_bsite.png">  Willkommen im Adminbereich vom IlchClan</h3>
	    </div>
	    <div class="row">
	        <div class="col-md-4">
	    	<div class="panel panel-default">
	    	    <div class="panel-body">
	    		<iframe style="width:100%;min-height:500px;" frameborder="0" src="http://www.maretz.eu/ilch_info.html"></iframe>
	    	    </div></div>
	        </div>
	        <div class="col-md-4">
	    	<div class="panel panel-default">
	    	    <div class="panel-body">
	    		<legend><i class="fa fa-info-circle"></i> Info´s zur Seite</legend>
	    		<ul class="list-group">
	    		    <li class="list-group-item list-group-item-info">
	    			<table width="100%"><tr>
	    				<td>Aktuelles Design</td>
	    				<td class="text-right"><strong><?php echo $allgAr['gfx']; ?></strong></td>
	    			    </tr></table>
	    		    </li>
	    		</ul>
	    		<ul class="list-group">
	    		    <li class="list-group-item list-group-item-info">
	    			<table width="100%"><tr>
	    				<td>Script Version</td>
	    				<td class="text-right"><strong><?php echo 'Ilch ' . $scriptVersion . ' ' . $scriptUpdate . ''; ?></strong></td>
	    			    </tr></table>
	    		    </li>
	    		</ul>
	    		<ul class="list-group">
	    		    <li class="list-group-item list-group-item-info">
	    			<table width="100%"><tr>
	    				<td>Gr&ouml;&szlig;e Datenbank</td>
	    				<td class="text-right"><strong><?php
						    $result = db_query("SHOW TABLE STATUS");
						    $dbsize = 0;
						    while ($row = mysql_fetch_assoc($result)) {
							$dbsize += $row['Data_length'];
						    }echo nicebytes($dbsize);
						    ?></strong></td>
	    			    </tr></table>
	    		    </li>
	    		</ul>
	    		<ul class="list-group">
	    		    <li class="list-group-item list-group-item-info">
	    			<table width="100%"><tr>
	    				<td>Download Ordner</td>
	    				<td class="text-right"><strong><?php echo nicebytes(dirsize('include/downs/')); ?> gro&szlig;</strong></td>
	    			    </tr>
	    			    <tr><td colspan="2" class="text-left"><a class="btn btn-default btn-xs" href="admin.php?archiv-downloads">Downloads Verwalten</a></td></tr>
	    			</table>
	    		    </li>
	    		</ul>
	    		<ul class="list-group">
	    		    <li class="list-group-item list-group-item-info">
	    			<table width="100%"><tr>
	    				<td>Status Seite</td>
	    				<td class="text-right"><strong><?php
						    if ($allgAr['wartung'] == 0) {
							echo '<span class="label label-success">Seite Online</span>';
						    } else {
							echo '<span class="label label-warning">Wartungs Modus</span>';
						    }
						    ?></strong></td>
	    			    </tr></table>
	    		    </li>
	    		</ul>
	    		<ul class="list-group">
	    		    <li class="list-group-item list-group-item-info">
	    			<table width="100%"><tr>
	    				<td>Serverzeit</td>
	    				<td class="text-right"><strong>
						    <?php
						    $timestamp = time();
						    $datum = date("d.m.Y", $timestamp);
						    $uhrzeit = date("H:i", $timestamp);
						    $woche = date("W", $timestamp);
						    $zone = date("e", $timestamp);
						    echo $datum, "<br>(", $zone, ") <br>(KW ", $woche, ") ", $uhrzeit, " Uhr";
						    ?></strong></td>
	    			    </tr></table>
	    		    </li>
	    		</ul>
	    	    </div>
	    	</div>
	        </div>
	        <div class="col-md-4">
	    	<div class="panel panel-default">
	    	    <div class="panel-body">
	    		<legend><i class="fa fa-users"></i> User</legend>
	    		<ul class="list-group">
	    		    <li class="list-group-item list-group-item-warning">
	    			<table width="100%"><tr>
	    				<td>Benutzer Online</td>
	    				<td class="text-right"><strong><?php echo ' ' . ges_online() . ' '; ?></strong></td>
	    			    </tr>
	    			    <tr><td colspan="2" class="text-left"><a class="btn btn-default btn-xs" href="admin.php?admin-userOnline">Online Liste anzeigen</a></td></tr>
	    			</table>
	    		    </li>
	    		</ul>
	    		<ul class="list-group">
	    		    <li class="list-group-item list-group-item-warning">
	    			<table width="100%"><tr>
	    				<td>G&auml;ste Online</td>
	    				<td class="text-right"><strong><?php echo ' ' . ges_gast_online() . ' '; ?></strong></td>
	    			    </tr></table>
	    		    </li>
	    		</ul>
	    		<ul class="list-group">
	    		    <li class="list-group-item list-group-item-warning">
	    			<table width="100%"><tr>
	    				<td>Offene Registry</td>
	    				<td class="text-right"><strong><?php
						    $gesuser = @db_result(db_query("SELECT count(name) FROM prefix_usercheck WHERE ak = 1"), 0);
						    echo ' ' . $gesuser . ' ';
						    ?></strong></td>
	    			    </tr>
	    			    <tr><td colspan="2" class="text-left"><a class="btn btn-default btn-xs" href="admin.php?puser">Liste anzeigen</a></td></tr>
	    			</table>
	    		    </li>
	    		</ul>
	    		<ul class="list-group">
	    		    <li class="list-group-item list-group-item-warning">
	    			<table width="100%"><tr>
	    				<td>Offene JoinUs</td>
	    				<td class="text-right"><strong><?php
						    $joinus = @db_result(db_query("SELECT count(name) FROM prefix_usercheck WHERE ak = 4"), 0);
						    echo ' ' . $joinus . ' ';
						    ?></strong></td>
	    			    </tr>
	    			    <tr><td colspan="2" class="text-left"><a class="btn btn-default btn-xs" href="admin.php?groups-joinus">Joinus anzeigen</a></td></tr>
	    			</table>
	    		    </li>
	    		</ul>
	    		<legend><h5><strong>neuste Mitglieder</strong></h5></legend>
			    <?php
			    $abf = 'SELECT * FROM prefix_user ORDER BY regist DESC LIMIT 3';
			    $erg = db_query($abf);
			    echo '<div class="list-group">';
			    while ($row = db_fetch_object($erg)) {
				$regsek = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - $row->regist;
				$regday = round($regsek / 86400);
				$user = $row->name;
				echo'<a href="?user-1-' . $row->id . '"  class="list-group-item"><strong>' . $user . '</strong><small rel="tooltip" title="angemeldet am ' . date('d.m.Y', $row->regist) . '" class="pull-right">( ' . date('d.m.Y', $row->regist) . ' )</small></a>';
			    }
			    echo '</div>';
			    ?>


	    	    </div></div>
	        </div></div>
	    <div class="row">
	        <div class="col-md-4">
	    	<div class="panel panel-default">
	    	    <div class="panel-body">
	    		<legend><i class="fa fa-signal"></i> Statistiken</legend>
			    <?php
			    if (empty($_GET['sum'])) {
				error_reporting(0);
				$heute = date('y-m-d');
				$time = time();
				$daysec = 86400;
				$weekdays = 7;
				$mth = 30;
				$day = $time - $daysec;
				$ges_visits = db_result(db_query("SELECT SUM(count) FROM prefix_counter"), 0);
				$ges_heute = @db_result(db_query("SELECT count FROM prefix_counter WHERE date = '" . $heute . "'"), 0);
				$ges_gestern = @db_result(db_query('SELECT count FROM prefix_counter WHERE date < "' . $heute . '" ORDER BY date DESC LIMIT 1'), 0);
				$news = @db_result(db_query("SELECT count(NEWS_ID) FROM prefix_news"), 0);
				$gbook = @db_result(db_query("SELECT count(ID) FROM prefix_gbook"), 0);
				$posts = @db_result(db_query("SELECT count(ID) FROM prefix_posts"), 0);
				$topic = @db_result(db_query("SELECT count(ID) FROM prefix_topics"), 0);
				$pollge = db_result(db_query("SELECT COUNT(poll_id) FROM prefix_poll WHERE recht = '2' "), 0);
				$komge = db_result(db_query("SELECT COUNT(ID) FROM `prefix_koms`"), 0);
				$shbox = db_result(db_query("SELECT COUNT(ID) FROM `prefix_shoutbox`"), 0);
				$downloads = @db_result(db_query("SELECT count(ID) FROM prefix_downloads"), 0);
				$bges = @db_count_query("SELECT COUNT(*) FROM prefix_gallery_imgs");
				$ubges = @db_count_query("SELECT COUNT(*) FROM prefix_usergallery");
				$links = @db_count_query("SELECT COUNT(ID) FROM prefix_links");
				$partner = @db_count_query("SELECT COUNT(ID) FROM prefix_partners");
				$gesuser = @db_result(db_query("SELECT count(ID) FROM prefix_user"), 0);
				$gesch1 = @db_result(db_query("SELECT count(ID) FROM prefix_user where geschlecht = 1"), 0);
				$gesch2 = @db_result(db_query("SELECT count(ID) FROM prefix_user where geschlecht = 2"), 0);
				$gesch3 = @db_result(db_query("SELECT count(ID) FROM prefix_user where geschlecht = 0"), 0);
				$maxErg = db_query('SELECT MAX(count) FROM `prefix_counter`');
				$max_in = db_result($maxErg, 0);
				$useroneregist = db_result(db_query('SELECT regist FROM prefix_user WHERE id = 1'), 0);
				$sincesec = $time - $useroneregist;
				$sinceday = floor($sincesec / $daysec);
				$dayvisits = floor($ges_visits / $sinceday) + 1;
				$mthvisits = floor($dayvisits * $mth);
				echo'<ul class="list-group">
  <li class="list-group-item list-group-item-info">
<table width="100%"><tr>
    <td>Seite Online seit</td>
<td class="text-right"><strong>' . $sinceday . ' Tagen</strong></td>
</tr>
<tr>
    <td>Mitglieder Gesamt</td>
<td class="text-right"><strong>' . $gesuser . '</strong></td>
</tr>
</table>
  </li>
</ul>
<legend><h5><strong>Besucher</strong></h5></legend>
<ul class="list-group">
  <li class="list-group-item list-group-item-warning">
<table width="100%">
<tr>
    <td>' . $lang['today'] . '</td>
<td class="text-right"><strong>' . $ges_heute . '</strong></td>
</tr>
<tr>
    <td>' . $lang['yesterday'] . '</td>
<td class="text-right"><strong>' . $ges_gestern . '</strong></td>
</tr>
<tr>
    <td>Rekord</td>
<td class="text-right"><strong>' . $max_in . ' am Tag</strong></td>
</tr>
<tr>
    <td>&#216; Tag</td>
<td class="text-right"><strong>' . $dayvisits . '</strong></td>
</tr>
<tr>
    <td>&#216; Monat</td>
<td class="text-right"><strong>' . $mthvisits . '</strong></td>
</tr>
<tr>
    <td>Gesamt</td>
<td class="text-right"><strong>' . $ges_visits . '</strong></td>
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
<td class="text-right"><strong>' . $news . '</strong></td>
</tr>
<tr>
    <td>Forum Topic</td>
<td class="text-right"><strong>' . $topic . '</strong></td>
</tr>
<tr>
    <td>Forum Posts</td>
<td class="text-right"><strong>' . $posts . '</strong></td>
</tr>
<tr>
    <td>G-Book Eintr&auml;ge</td>
<td class="text-right"><strong>' . $gbook . '</strong> <a href="admin.php?gbook" rel="tooltip" title="Eintr&auml;ge verwalten"><span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a></td>
</tr>
<tr>
    <td>Kommentare Gesamt</td>
<td class="text-right"><strong>' . $komge . '</strong></td>
</tr>
<tr>
    <td>Umfragen</td>
<td class="text-right"><strong>' . $pollge . '</strong></td>
</tr>
<tr>
    <td>Shoutbox Eintr&auml;ge</td>
<td class="text-right"><strong>' . $shbox . '</strong></td>
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
<td class="text-right"><strong>' . $bges . ' Image</strong> <a href="admin.php?gallery" rel="tooltip" title="Eintr&auml;ge verwalten"><span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a></td>
</tr>
<tr>
    <td>User Gallerie</td>
<td class="text-right"><strong>' . $ubges . ' Image</strong></td>
</tr>
<tr>
    <td>Downloads</td>
<td class="text-right"><strong>' . $downloads . ' Eintr&auml;ge</strong> <a href="admin.php?archiv-downloads" rel="tooltip" title="Eintr&auml;ge verwalten"><span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a></td>
</tr>
<tr>
    <td>Links</td>
<td class="text-right"><strong>' . $links . ' Eintr&auml;ge</strong> <a href="admin.php?archiv-links" rel="tooltip" title="Eintr&auml;ge verwalten"><span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a></td>
</tr>
<tr>
    <td>Partner</td>
<td class="text-right"><strong>' . $partner . ' Eintr&auml;ge</strong> <a href="admin.php?archiv-partners" rel="tooltip" title="Eintr&auml;ge verwalten"><span class="glyphicon glyphicon-new-window" aria-hidden="true"></span></a></td>
</tr>
</table>
  </li>
</ul>';
			    }
			    ?>
	    	    </div></div>
	        </div>
	        <div class="col-md-4">
	    	<div class="panel panel-default bg-warning">
	    	    <div class="panel-body bg-warning">
	    		<legend><i class="fa fa-file-text-o"></i> Letzte Eintr&auml;ge</legend>
	    		<legend class="text-info"><h5><strong>Letzte News</strong></h5></legend>
			    <?php
			    $abf = 'SELECT
          a.news_kat as kate,
          DATE_FORMAT(a.news_time,"%d.%m.%Y") as datum,
          a.news_title as title,
          a.news_kat as kate,
          a.news_id as id,
          b.name as username,
          b.id as userid
          FROM prefix_news as a
          LEFT JOIN prefix_user as b ON a.user_id = b.id
          WHERE news_recht >= ' . $_SESSION['authright'] . '
          ORDER BY a.news_time DESC
          LIMIT 0,3';
			    echo '<ul class="list-group list-group-boxen text-left">';
			    $erg = db_query($abf);
			    if (@db_num_rows($erg) == 0) {
				echo '<ul class="list-group list-group-boxen text-center"><div class="alert" role="alert">kein Newseintrag vorhanden<br><a class="text-info" href="admin.php?news"><strong>neue News schreiben</strong></a></div></ul>';
			    }
			    while ($row = db_fetch_object($erg)) {
				echo '<a href="admin.php?news-edit-' . $row->id . '" class="list-group-item"><small>Kategorie: ' . $row->kate . '</small><h5><strong><i class="fa fa-angle-double-right"></i> ' . $row->title . '</strong></h5><small>Autor : ' . $row->username . ' | ' . $row->datum . '</small></a>';
			    }
			    echo '</ul>';
			    ?>
	    		<legend class="text-info"><h5><strong>Letzte Forumeintr&auml;ge</strong></h5></legend>
			    <?php
			    $query = "SELECT a.id, a.name, a.rep,b.name as top, b.id as fid, c.erst as last, c.erstid, c.id as pid, c.time, a.rep, a.erst, a.hit, a.art, a.stat, d.name as kat
FROM prefix_topics a
  LEFT JOIN prefix_forums b ON b.id = a.fid
  LEFT JOIN prefix_posts c ON c.id = a.last_post_id
	LEFT JOIN prefix_forumcats d ON d.id = b.cid AND b.id = a.fid
  LEFT JOIN prefix_groupusers vg ON vg.uid = " . $_SESSION['authid'] . " AND vg.gid = b.view
  LEFT JOIN prefix_groupusers rg ON rg.uid = " . $_SESSION['authid'] . " AND rg.gid = b.reply
  LEFT JOIN prefix_groupusers sg ON sg.uid = " . $_SESSION['authid'] . " AND sg.gid = b.start
WHERE ((" . $_SESSION['authright'] . " <= b.view AND b.view < 1)
   OR (" . $_SESSION['authright'] . " <= b.reply AND b.reply < 1)
   OR (" . $_SESSION['authright'] . " <= b.start AND b.start < 1)
	 OR vg.fid IS NOT NULL
	 OR rg.fid IS NOT NULL
	 OR sg.fid IS NOT NULL
	 OR -9 >= " . $_SESSION['authright'] . ")
ORDER BY c.time DESC
LIMIT 0,3";
			    echo '<ul class="list-group list-group-boxen text-left">';
			    $resultID = db_query($query);
			    if (@db_num_rows($resultID) == 0) {
				echo '<ul class="list-group list-group-boxen text-center"><div class="alert" role="alert">kein Forumeintrag vorhanden<br><a class="text-info" href="admin.php?forum"><strong>jetzt neues Forum erstellen</strong></a></div></ul>';
			    }
			    while ($row = db_fetch_assoc($resultID)) {
				$row['date'] = date('d.m.y - H:i', $row['time']);
				$row['page'] = ceil(($row['rep'] + 1) / $allgAr['Fpanz']);
				$row['ORD'] = forum_get_ordner($row['time'], $row['id'], $row['fid']);

				echo '<a href="index.php?forum-showposts-' . $row['id'] . '-p' . $row['page'] . '#' . $row['pid'] . '" class="list-group-item"><small>Kategorie: ' . $row['kat'] . '</small><h5><strong><i class="fa fa-angle-double-right"></i> ' . $row['name'] . '</strong></h5><small>
Last Post:&nbsp;' . $row['last'] . ' | ' . $row['date'] . ' Uhr</small><br><small class="text-info">Autor: &nbsp;' . $row['erst'] . ' | Antworten: ' . $row['rep'] . '</small></a>';
			    }
			    echo '</ul>';
			    ?>
	    	    </div></div>
	        </div>
	        <div class="col-md-4">
	    	<div class="panel panel-default">
	    	    <div class="panel-body">
	    		<legend><i class="fa fa-calendar-o"></i> Termine</legend>
	    		<legend ><h5><strong>Kalender Eintr&auml;ge</strong></h5></legend>
			    <?php
			    $abf = "SELECT id, title, FROM_UNIXTIME(time,'%d.%m.%Y') as zeit FROM prefix_kalender WHERE time >= UNIX_TIMESTAMP() AND recht >= {$_SESSION['authright']} ORDER BY time LIMIT 3";
			    $erg = db_query($abf);
			    if (@db_num_rows($erg) == 0) {
				echo '<ul class="list-group list-group-boxen text-center"><div class="alert alert-warning" role="alert">Aktuell sind keine Termine vorhanden<br><a class="text-warning" href="admin.php?kalender"><strong>neuen Termin eintragen</strong></a></div></ul>';
			    }
			    echo '<ul class="list-group list-group-boxen text-left">';
			    while ($row = db_fetch_object($erg)) {
				echo '<a href="admin.php?kalender&edit=' . $row->id . '" class="list-group-item"><h5><strong><i class="fa fa-angle-double-right"></i> ' . $row->title . '</strong></h5><small>Termin am: ' . $row->zeit . '</small></a>';
			    }
			    echo '</ul>';
			    ?>
	    		<legend ><h5><strong>Next Wars</strong></h5></legend>
			    <?php
			    $akttime = date('Y-m-d');
			    $erg = @db_query("SELECT DATE_FORMAT(datime,'%d.%m.%y - %H:%i') as time,tag,gegner, id, game FROM prefix_wars WHERE status = 2 AND datime > '" . $akttime . "' ORDER BY datime");
			    if (@db_num_rows($erg) == 0) {
				echo '<ul class="list-group list-group-boxen text-center"><div class="alert alert-warning" role="alert">Aktuell kein War geplant<br><a class="text-warning" href="admin.php?wars-next"><strong>Next-War eintragen</strong></a></div></ul>';
			    } else {
				echo '<ul class="list-group list-group-boxen text-left">';
				while ($row = @db_fetch_object($erg)) {
				    $row->tag = ( empty($row->tag) ? $row->gegner : $row->tag );
				    echo '<a href="admin.php?wars-next&pkey=' . $row->id . '" class="list-group-item"><strong><i class="fa fa-angle-double-right"></i> ' . $row->tag . '</strong><span class="label label-success pull-right">' . $row->time . ' Uhr</span></a>';
				}
				echo '</ul>';
			    }
			    ?>
	    	    </div></div>
	        </div>
	    </div>
	    <?php
	    break;
	}

    case 'versionsKontrolle' : {
	    // ICON Anzeige...
	    echo '<table cellpadding="0" cellspacing="0" border="0"><tr><td><img src="include/images/icons/admin/version_check.png" /></td><td width="30"></td><td valign="bottom"><h1>Versionskontrolle</h1></td></tr></table>';

	    echo 'Scripte Version: ' . $scriptVersion . '<br />Update Version: ' . $scriptUpdate . '<br /><br />';
	    echo '<script language="JavaScript" type="text/javascript" src="http://www.ilch.de/down/ilchClan/update.php?version=' . $scriptVersion . '&update=' . $scriptUpdate . '"></script>';
	    #echo '<iframe width="100%" height="60" src="http://www.ilch.de/down/ilchClan/update.php?version='.$scriptVersion.'&update='.$scriptUpdate.'"></iframe>';
	    break;
	}

    #####################################

    case 'besucherStatistik' : {

	    function echo_admin_site_statistik($title, $col, $smon, $ges, $orderQuery) {
		$sql = db_query("SELECT COUNT(*) AS wert, $col as schl FROM  `prefix_stats` WHERE mon = " . $smon . " GROUP BY schl ORDER BY " . $orderQuery);
		$max = @db_result(db_query("SELECT COUNT(*) as wert, $col as schl FROM prefix_stats WHERE mon = " . $smon . " GROUP BY schl ORDER BY wert DESC LIMIT 1"), 0, 0);
		if (empty($max)) {
		    $max = 1;
		}
		if (empty($ges)) {
		    $ges = 1;
		}
		echo '<tr><th align="left" colspan="4">' . $title . '</th></tr>';
		while ($r = db_fetch_assoc($sql)) {
		    $wert = ( empty($r['wert']) ? 1 : $r['wert'] );
		    $weite = ($wert / $max) * 200;
		    $prozent = ($wert * 100) / $ges;
		    $prozent = number_format(round($prozent, 2), 2, ',', '');
		    $name = $r['schl'];
		    if (strlen($name) >= 50) {
			$name = substr($name, 0, 50) . '<b>...</b>';
		    }
		    echo '<tr class="norm"><td width="150" title="' . $r['schl'] . '">' . $name . '</td><td width="250">';
		    echo '<hr width="' . $weite . '" align="left" /></td>';
		    echo '<td width="50" align="right">' . $prozent . '%</td>';
		    echo '<td  width="50" align="right">' . $wert . '</td></tr>';
		}
	    }

	    // ICON Anzeige...
	    echo '<table cellpadding="0" cellspacing="0" border="0"><tr><td><img src="include/images/icons/admin/stats_visitor.png" /></td><td width="30"></td><td valign="bottom"><h1>Besucher Statistik</h1></td></tr></table>';

	    echo '<a href="admin.php?admin-besucherUebersicht">&Uuml;bersicht</a>&nbsp;<b>|</b>&nbsp;<a href="?admin-besucherStatistik-' . $lastmon . '" title="' . $lastmon . '. ' . $lastjahr . '">letzter Monat</a>&nbsp;<b>|</b>&nbsp;<a href="?admin-besucherStatistik-' . $mon . '" title="' . $mon . '. ' . $jahr . '">dieser Monat</a>';
	    $smon = $menu->get(2);
	    if (empty($smon)) {
		$smon = $mon;
	    }

	    $ges = db_result(db_query("SELECT COUNT(*) FROM prefix_stats WHERE mon = " . $smon), 0, 0);
	    echo '<br /><br /><b>Gesamt diesen Monat: ' . $ges . '</b>';
	    echo '<table cellpadding="2" border="0" cellspacing="0">';

	    echo_admin_site_statistik('Besucher nach Tagen', 'day', $smon, $ges, "schl DESC LIMIT 50");
	    echo_admin_site_statistik('Besucher nach Wochentagen', 'DAYNAME(FROM_UNIXTIME((wtag+3)*86400))', $smon, $ges, "wtag DESC LIMIT 50");
	    echo_admin_site_statistik('Besucher nach Uhrzeit', 'stunde', $smon, $ges, "schl ASC LIMIT 50");
	    echo_admin_site_statistik('Besucher nach Browsern', 'browser', $smon, $ges, "schl DESC LIMIT 50");
	    echo_admin_site_statistik('Besucher nach Betriebssytemen', 'os', $smon, $ges, "schl DESC LIMIT 50");
	    echo_admin_site_statistik('Besucher nach Herkunft', 'ref', $smon, $ges, "wert DESC LIMIT 50");

	    echo '</table>';
	    break;
	}


    case 'userOnline' : {
	    ?>
	    <table cellpadding="0" cellspacing="0" border="0"><tr><td><img src="include/images/icons/admin/stats_online.png" /></td><td width="30"></td><td valign="bottom"><h1>Online Statistik</h1></td></tr></table>
	    <table border="0" cellpadding="2" cellspacing="1" class="border">
	        <tr class="Chead">
	    	<th>Username</th>
	    	<th>Letzte aktivitaet</th>
	    	<th>IP-Adresse</th>
	    	<th>Anbieter</th>
	        </tr>
		<?php
		echo user_admin_online_liste();
		?>
	    </table>
	    <?php
	    break;
	}
    case 'besucherUebersicht' : {

	    function get_max_from_x($q) {
		$q = db_query($q);
		$m = 0;
		while ($r = db_fetch_row($q)) {
		    if ($r[0] > $m) {
			$m = $r[0];
		    }
		}
		return ($m);
	    }

	    function echo_admin_site_uebersicht($schl, $wert, $max, $ges) {
		$wert = ( empty($wert) ? 1 : $wert );
		$weite = ($wert / $max ) * 100;
		$prozent = ($wert * 100) / $ges;
		$prozent = number_format(round($prozent, 2), 2, ',', '');
		$name = $schl;
		if (strlen($name) >= 50) {
		    $name = substr($name, 0, 50) . '<b>...</b>';
		}
		echo '<tr class="norm"><td width="150" title="' . $schl . '">' . $name . '</td><td width="250">';
		echo '<hr width="' . $weite . '" align="left" /></td>';
		echo '<td width="50" align="right">' . $prozent . '%</td>';
		echo '<td  width="50" align="right">' . $wert . '</td></tr>';
	    }

	    // ICON Anzeige...
	    echo '<table cellpadding="0" cellspacing="0" border="0"><tr><td><img src="include/images/icons/admin/stats_visitor.png" /></td><td width="30"></td><td valign="bottom"><h1>Besucher Statistik</h1></td></tr></table>';

	    echo '<a href="admin.php?admin-besucherUebersicht">&Uuml;bersicht</a>&nbsp;<b>|</b>&nbsp;<a href="?admin-besucherStatistik-' . $lastmon . '" title="' . $lastmon . '. ' . $lastjahr . '">letzter Monat</a>&nbsp;<b>|</b>&nbsp;<a href="?admin-besucherStatistik-' . $mon . '" title="' . $mon . '. ' . $jahr . '">dieser Monat</a>';
	    echo '<br /><br /><table cellpadding="0" border="0" cellspacing="0" width="100%">';
	    echo '<tr><td valign="top" width="33%"><b>Nach Tagen (letzten 5 Monate):</b><br />';

	    echo '<table cellpadding="0" border="0" cellspacing="0" width="90%">';
	    $max = db_result(db_query("SELECT MAX(`count`) FROM prefix_counter"), 0);
	    $ges = db_result(db_query("SELECT SUM(`count`) FROM prefix_counter"), 0);
	    $erg = db_query("SELECT `count` as sum, DATE_FORMAT(`date`, '%d.%m.%Y') as datum FROM prefix_counter ORDER BY `date` DESC");
	    while ($r = db_fetch_assoc($erg)) {
		echo_admin_site_uebersicht($r['datum'], $r['sum'], $max, $ges);
	    }
	    echo '</table>';

	    echo '</td><td valign="top" width="33%"><b>Nach Monaten:</b><br />';

	    echo '<table cellpadding="0" border="0" cellspacing="0" width="90%">';
	    $max = get_max_from_x("SELECT SUM(`count`) FROM prefix_counter GROUP BY MONTH(`date`), YEAR(`date`)");
	    $erg = db_query("SELECT SUM(`count`) as sum, MONTH(`date`) as monat, YEAR(`date`) as jahr FROM prefix_counter GROUP BY monat, jahr ORDER BY jahr DESC, monat DESC");
	    while ($r = db_fetch_assoc($erg)) {
		echo_admin_site_uebersicht((strlen($r['monat']) == 1 ? '0' : '') . $r['monat'] . '.' . $r['jahr'], $r['sum'], $max, $ges);
	    }
	    echo '</table>';

	    echo '</td><td valign="top" width="33%"><b>Nach Jahren:</b><br />';

	    echo '<table cellpadding="0" border="0" cellspacing="0" width="90%">';
	    $max = get_max_from_x("SELECT SUM(`count`) FROM prefix_counter GROUP BY YEAR(`date`)");
	    $erg = db_query("SELECT SUM(`count`) as sum, YEAR(`date`) as jahr FROM prefix_counter GROUP BY jahr ORDER BY jahr DESC");
	    while ($r = db_fetch_assoc($erg)) {
		echo_admin_site_uebersicht($r['jahr'], $r['sum'], $max, $ges);
	    }
	    echo '</table>';

	    echo '</td></tr></table>';
	    break;
	}
    case 'siteStatistik' : {
##########################################

	    function forum_statistic_show($sql, $ges) {
		$erg = db_query($sql);
		echo '<table border="0" cellpadding="0" cellspacing="0">';
		while ($r = db_fetch_row($erg)) {
#    str_repeat('|',abs($row['regs'] / 2))
		    echo '<tr><td>' . $r[1] . '</td><td>' . str_repeat('|', $r[0]) . ' ' . $r[0] . '</td></tr>';
		}
		echo '</table>';
	    }

// ICON Anzeige...
	    echo '<table cellpadding="0" cellspacing="0" border="0"><tr><td><img src="include/images/icons/admin/stats_site.png" /></td><td width="30"></td><td valign="bottom"><h1>Seiten Statistik</h1></td></tr></table>';

	    echo '<table><tr><td valign="top">';
	    $heute = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
	    $anzheute = db_result(db_query("SELECT COUNT(*) FROM prefix_posts WHERE time >= " . $heute), 0, 0);
	    echo 'Gesamt Posts heute: ' . $anzheute . '<br /><hr>';

# aktivsten user
	    $sql = "SELECT COUNT(*) as kk , erst as vv FROM prefix_posts WHERE time >= " . $heute . " GROUP BY vv ORDER BY kk DESC LIMIT 10";
	    echo '<b>Aktivsten User heute</b><br />';
	    forum_statistic_show($sql, $anzheute);

# aktivsten themen
	    $sql = "SELECT COUNT(*) as kk , name as vv FROM prefix_topics LEFT JOIN prefix_posts ON prefix_posts.tid = prefix_topics.id WHERE time >= " . $heute . " GROUP BY vv ORDER BY kk DESC LIMIT 10";
	    echo '<hr><b>Aktivsten Themen heute</b><br />';
	    forum_statistic_show($sql, $anzheute);

# aktivsten foren
	    $sql = "SELECT COUNT(*) as kk , prefix_forums.name as vv FROM prefix_topics LEFT JOIN prefix_forums ON prefix_forums.id = prefix_topics.fid LEFT JOIN prefix_posts ON prefix_posts.tid = prefix_topics.id WHERE time >= " . $heute . " GROUP BY vv ORDER BY kk DESC LIMIT 10";
	    echo '<hr><b>Aktivsten Foren heute</b><br />';
	    forum_statistic_show($sql, $anzheute);

# neue user heute
	    $gsh = db_result(db_query("SELECT COUNT(*) FROM prefix_user WHERE regist >= " . $heute), 0, 0);
	    $sql = "SELECT COUNT(*) as kk , name as vv FROM prefix_user WHERE regist >= " . $heute . " GROUP BY vv ORDER BY kk DESC LIMIT 10";
	    echo '<hr><b>Neue User heute</b><br />';
	    forum_statistic_show($sql, $gsh);

	    echo '</td><td valign="top">';
	    $heute1 = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
	    $anzheute = db_result(db_query("SELECT COUNT(*) FROM prefix_posts WHERE time >= " . $heute1 . " AND time <= " . $heute), 0, 0);
	    echo 'Gesamt Posts gestern: ' . $anzheute . '<br /><hr>';

# aktivsten user
	    $sql = "SELECT COUNT(*) as kk , erst as vv FROM prefix_posts WHERE time >= " . $heute1 . " AND time <= " . $heute . " GROUP BY vv ORDER BY kk DESC LIMIT 10";
	    echo '<b>Aktivsten User gestern</b><br />';
	    forum_statistic_show($sql, $anzheute);

# aktivsten themen
	    $sql = "SELECT COUNT(*) as kk , name as vv FROM prefix_topics LEFT JOIN prefix_posts ON prefix_posts.tid = prefix_topics.id WHERE time >= " . $heute1 . " AND time <= " . $heute . " GROUP BY vv ORDER BY kk DESC LIMIT 10";
	    echo '<hr><b>Aktivsten Themen gestern</b><br />';
	    forum_statistic_show($sql, $anzheute);

# aktivsten foren
	    $sql = "SELECT COUNT(*) as kk , prefix_forums.name as vv FROM prefix_topics LEFT JOIN prefix_forums ON prefix_forums.id = prefix_topics.fid LEFT JOIN prefix_posts ON prefix_posts.tid = prefix_topics.id WHERE time >= " . $heute1 . " AND time <= " . $heute . " GROUP BY vv ORDER BY kk DESC LIMIT 10";
	    echo '<hr><b>Aktivsten Foren gestern</b><br />';
	    forum_statistic_show($sql, $anzheute);

# neue user heute
	    $gsh = db_result(db_query("SELECT COUNT(*) FROM prefix_user WHERE regist >= " . $heute1 . " AND regist <= " . $heute), 0, 0);
	    $sql = "SELECT COUNT(*) as kk , name as vv FROM prefix_user WHERE regist >= " . $heute1 . " AND regist <= " . $heute . " GROUP BY vv ORDER BY kk DESC LIMIT 10";
	    echo '<hr><b>Neue User gestern</b><br />';
	    forum_statistic_show($sql, $gsh);
	    echo '</td></tr></table>';

	    echo '<h1>Es ist ganz ehrlich noch mehr geplant :P</h1>';

##########################################
	    break;
	}
}

$design->footer();
?>