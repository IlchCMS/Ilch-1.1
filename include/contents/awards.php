<?php 
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );

$title = $allgAr['title'].' :: Awards';
$hmenu = 'Awards';
$design = new design ( $title , $hmenu );
$design->header();

$tpl = new tpl ( 'awards.htm' );
$tpl->out(0);
$class = 'Cnorm';
$erg = db_query("SELECT platz, text, wofur, team, bild, DATE_FORMAT(time, '%d.%m.%Y') as time FROM `prefix_awards` ORDER BY time DESC");
while($row = db_fetch_assoc($erg) ) {
  $class = ( $class == 'Cmite' ? 'Cnorm' : 'Cmite' );
  if ($row['bild'] != '' AND trim($row['bild']) != 'http://') {
    $row['bildutime'] = '<img src="'.$row['bild'].'" alt=""><span class="smalfont">'.$row['time'].'</span><br><br>';
  } else {
    $row['bildutime'] = '<span class="smalfont">'.$lang['date'].': '.$row['time'].'</span><br><br>';
  }
	$row['class'] = $class;
	$tpl->set_ar_out($row,1);

}
$tpl->out(2);

$design->footer();

?>