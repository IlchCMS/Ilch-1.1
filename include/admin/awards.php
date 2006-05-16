<?php 
#   Copyright by: Manuel Staechele
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );
defined ('admin') or die ( 'only admin access' );

$design = new design ( 'Admins Area', 'Admins Area', 2 );
$design->header();

##
###
####
##### F u n k t i o n e n

function getTeams ( $db ) {
  
	$squads = '';
  $erg1 = db_query("SELECT name, id FROM prefix_groups ORDER BY pos");
  while ($row = db_fetch_assoc($erg1) ) {
    $squads .= '<option>'.$row['name'].'</option>';
  }
	return ($squads);
	
}


##
###
####
##### A k t i o n e n


if ( !empty ($_GET['del']) ) {

  db_query('DELETE FROM `prefix_awards` WHERE id = "'.$_GET['del'].'" LIMIT 1');

}

if ( !empty($_POST['ins']) ) {
  
  $datum = get_datum ($_POST['datum']);
  $wofur = escape($_POST['wofur'], 'string');
  $text  = escape($_POST['text'], 'string');
  $team  = escape($_POST['team'], 'string');
  $platz = escape($_POST['platz'], 'string');
  $bild  = get_homepage(escape($_POST['bild'], 'string'));
  db_query("INSERT INTO `prefix_awards` (time, platz, team, wofur, bild, text) VALUES
  ('".$datum."', '".$platz."', '".$team."', '".$wofur."', '".$bild."', '".$text."')");

}


##
###
####
##### H t m l

?>
<script language="JavaScript" type="text/javascript">
    <!--
      
			function delcheck ( DELID ) {
			  var frage = confirm ( "Willst du diesen Eintrag wirklich löschen?" );
				if ( frage == true ) {
				  document.location.href="?awards&del="+DELID;
				}
			}
		//-->
</script>

<table cellpadding="0" cellspacing="0" border="0"><tr><td><img src="include/images/icons/admin/awards.png" /></td><td width="30"></td><td valign="bottom"><h1>Awards</h1></td></tr></table>

<table width="100%" border="0" cellpadding="2" cellspacing="1" class="border">
   <tr>
	  <td width="60%" valign="top" class="Cnorm">
		  <form action="admin.php?awards" method="POST">
			
			<table width="100%" border="0" cellpadding="2" cellspacing="1" class="border">
			  <tr>
				  <td class="Cmite" width="100">Datum</td>
					<td class="Cnorm"><input type="text" name="datum" value="<?php echo date('d.m.Y'); ?>"></td>
				</tr><tr>
				  <td class="Cmite">Platz</td>
					<td class="Cnorm"><input type="text" name="platz"></td>
				</tr><tr>
				  <td class="Cmite">Team</td>
					<td class="Cnorm"><select name="team"><option>ges. Clan</option><?php echo getTeams() ?></select></td>
				</tr><tr>
				  <td class="Cmite">Wof&uuml;r</td>
					<td class="Cnorm"><input name="wofur" size="40"></td>
				</tr><tr>
				  <td class="Cmite">Bild zum Award</td>
					<td class="Cnorm"><input name="bild" size="40"></td>
				</tr><tr>
				  <td class="Cmite">Beschreibung</td>
					<td class="Cnorm"><textarea cols="30" rows="3" name="text"></textarea></td>
				</tr><tr>
				  <td class="Cmite"></td>
					<td class="Cnorm"><input type="submit" value="Eintragen" name="ins"></td>
				</tr>
			</table>
			
			</form>
    </td>
		<td width="40%" valign="top" class="Cmite">
      <h2>L&ouml;schen</h2>
      <table width="100%" border="0" cellpadding="5" cellspacing="0">
        <tr>
	        <td></td>
		      <td>Datum</td>
		      <td>Platz</td>
		      <td>Squad</td>
	      </tr>

<?php
$erg = db_query('SELECT * FROM `prefix_awards` ORDER BY time DESC');
while ($row = db_fetch_object($erg) ) {
  echo '<tr><td><a href="#" onclick="delcheck('.$row->id.')">L&ouml;schen</a></td>';
	echo '<td>'.$row->time.'</td>';
	echo '<td>'.$row->platz.'</td><td>'.$row->team.'</td></tr>';
}
?>

      </table>
		</td>
	</tr>
</table>

<?php
$design->footer();
?>