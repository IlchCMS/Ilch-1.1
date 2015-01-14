<?php
#   Copyright by Manuel
#   Support www.ilch.de

defined ('main') or die ( 'no direct access' );
if (is_coadmin()) {
?>
<script language="JavaScript" type="text/javascript">
<!--
  function createNewUser() {
    var Fenster = window.open ('admin.php?user-createNewUser', 'createNewUser', 'status=yes,scrollbars=yes,height=200,width=350,left=300,top=50');
    Fenster.focus();
  }
//-->
</script>
<?php
}
echo '<li><a href="./">Seite</a></li>';
if ( is_coadmin() ) {
echo '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Admin <b class="caret"></b></a><ul class="dropdown-menu">';
if (is_admin()) { 
echo '<li><a href="admin.php?allg">Konfiguration</a></li>';
 }
if ($allgAr['mail_smtp']) { 
echo '<li><a href="admin.php?smtpconf">SMTP Konfiguration</a></li>';
} 
echo '<li><a href="admin.php?menu">Navigation</a></li>';
if (is_admin()) { 
echo '<li><a href="admin.php?backup">Backup</a></li>';
} 
echo '<li><a href="admin.php?compatibility">Kompatibilität</a></li>
      <li><a href="admin.php?smilies">Smilies</a></li>
      <li><a href="admin.php?newsletter">Newsletter</a></li>
      <li><a href="admin.php?admin-versionsKontrolle">Versions Kontrolle</a></li>
      <li><a href="admin.php?checkconf">Server Konfiguration</a></li>
      <li class="divider"></li>
      <li class="dropdown-header">Statistik</li>
      <li><a href="admin.php?admin-besucherStatistik">Besucher</a></li>
      <li><a href="admin.php?admin-siteStatistik">Seite</a></li>
      <li><a href="admin.php?admin-userOnline">Online</a></li>';
echo '</ul></li>';

echo '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">User <b class="caret"></b></a><ul class="dropdown-menu">';
echo '<li><a href="admin.php?user">Verwalten</a></li>';
if (is_admin()) { 
echo '<li><a href="admin.php?grundrechte">Grundrechte</a></li>';
} 
echo '<li><a href="admin.php?profilefields">Profilefelder</a></li>
      <li><a href="javascript: createNewUser();">neuen User</a></li>
      <li><a href="admin.php?range">Ranks</a></li>';
echo '</ul></li>';
echo '<li><a href="admin.php?selfbp">Eigene Box/Page</a></li>';
echo '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Clanbox <b class="caret"></b></a><ul class="dropdown-menu">';
echo '<li><a href="admin.php?wars-next">Nextwars</a></li>
      <li><a href="admin.php?wars-last">Lastwars</a></li>
      <li><a href="admin.php?groups">Teams</a></li>
      <li><a href="admin.php?awards">Awards</a></li>
      <li><a href="admin.php?kasse">Kasse</a></li>
      <li><a href="admin.php?rules">Rules</a></li>
      <li><a href="admin.php?history">History</a></li>
      <li><a href="admin.php?trains">Trainzeiten</a></li>';
echo '</ul></li>';
echo '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Content <b class="caret"></b></a><ul class="dropdown-menu">';
echo '<li><a href="admin.php?news">News</a></li>
      <li><a href="admin.php?forum">Forum</a></li>
      <li><a href="admin.php?archiv-downloads">Downloads</a></li>
      <li><a href="admin.php?archiv-links">Links</a></li>
      <li><a href="admin.php?gallery">Gallery</a></li>
      <li><a href="admin.php?gbook">G-Book</a></li>
      <li><a href="admin.php?vote">Umfrage</a></li>
      <li><a href="admin.php?kalender">Kalender</a></li>
      <li><a href="admin.php?contact">Kontakt</a></li>
      <li><a href="admin.php?impressum">Impressum</a></li>
      <li class="divider"></li>
      <li class="dropdown-header">Boxen</li>
      <li><a href="admin.php?archiv-partners">Partner</a></li>
      <li><a href="admin.php?picofx">Pic of X</a></li>';
echo '</ul></li>';
$erg = db_query("SELECT url, name FROM prefix_modules WHERE ashow = 1");
while($row = db_fetch_assoc($erg) ) {
echo '<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Module <b class="caret"></b></a><ul class="dropdown-menu">';
echo '<li><a href="admin.php?'.$row['url'].'">'.$row['name'].'</a></li>';
echo '</ul></li>';
		}
} elseif (count($_SESSION['authmod']) > 0) {
  echo "[null, 'Module', null, null, null,";
  $q = "SELECT DISTINCT url, name
	FROM prefix_modulerights a
	LEFT JOIN prefix_modules b ON b.id = a.mid
	WHERE b.gshow = 1 AND uid = ".$_SESSION['authid'];
  $erg = db_query($q);
	while($row = db_fetch_assoc($erg) ) {
echo '<li><a href="admin.php?'.$row['url'].'">'.$row['name'].'</a></li>';
  }
}
?>
