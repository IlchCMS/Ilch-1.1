<?php
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );
defined ('admin') or die ( 'only admin access' );

$design = new design ( 'Admins Area', 'Admins Area', 2 );
$design->header();

$_POST['show'] = escape($_POST['show'], 'string');
$_POST['func'] = escape($_POST['func'], 'integer');
$_POST['sid']  = escape($_POST['sid'], 'integer');

$show = TRUE;
if ( isset ($_POST['sub']) ) {
  if ( empty ( $_POST['sid']) ) {
	  $pos = db_count_query("SELECT COUNT(*) as anz FROM prefix_profilefields");
		db_query("INSERT INTO `prefix_profilefields` (pos,`show`,func) VALUES (".$pos.",'".$_POST['show']."','".$_POST['func']."')");
	} else {
	  db_query("UPDATE `prefix_profilefields` SET `show` = '".$_POST['show']."', func = ".$_POST['func']."  WHERE id = ".$_POST['sid']);
	}
}

if ( $menu->get(1) == 'delete' ) {
  $id = $menu->get(2);
  $anz = db_count_query("SELECT COUNT(id) FROM prefix_profilefields WHERE id = ".$id." AND func < 3");
	if ( $anz == 1 ) {
	  $pos = db_result(db_query("SELECT pos FROM prefix_profilefields WHERE id = ".$id ),0);
    db_query("DELETE FROM `prefix_profilefields` WHERE id = ".$id);
		db_query("UPDATE prefix_profilefields SET pos = pos - 1 WHERE pos > ".$pos);
		db_query("DELETE FROM prefix_userfields WHERE fid = ".$id);
	}
}

if ( $menu->get(1) == 'u' OR $menu->get(1) == 'o' ) {
	$a = db_count_query("SELECT COUNT(*) as anz FROM prefix_profilefields");
  $np = ( $menu->get(1) == 'o' ? $menu->get(3) -1 : $menu->get(3) +1 );
  $np = ( $np >= ( $a -1 ) ? ( $a - 1) : $np );
  $np = ( $np < 0 ? 0 : $np );
  db_query("UPDATE prefix_profilefields SET pos = ".$menu->get(3)." WHERE pos = ".$np);
  db_query("UPDATE prefix_profilefields SET pos = ".$np." WHERE id = ".$menu->get(2));
}

if ( $menu->get(1) == 'c' ) {
  $n = ( $menu->get(3) == 3 ? 4 : 3 );
  db_query("UPDATE prefix_profilefields SET func = ".$n." WHERE id = ".$menu->get(2));
}

if ( $show ) {

	$tpl = new tpl ( 'profilefields', 1);
	if ( $menu->get(1) != 'edit' ) {
	  $row = array(
		  'sub' => 'Eintragen',
		  'pos' => '',
			'show' => '',
			'func' => arliste('',profilefields_functions2(),$tpl,'func'),
			'sid' => ''
		);
	} else {
    $sid = $menu->get(2);
		$abf = 'SELECT `show`,func,id as sid FROM `prefix_profilefields` WHERE id = "'.$sid.'"';
		$erg = db_query($abf);
		$row = db_fetch_assoc($erg);
		$row['func'] = arliste($row['func'],profilefields_functions2(),$tpl,'func');
		$row['sub'] = '&Auml;ndern';

	}


  $tpl->set_ar_out($row,0);
  $class = 'Cnorm';
	$ar = profilefields_functions();
	$erg = db_query('SELECT * FROM `prefix_profilefields` ORDER BY pos');
	while ($r = db_fetch_assoc($erg) ) {
    $class = ( $class == 'Cnorm' ? 'Cmite' : 'Cnorm' );
		$class = ( $r['func'] == 2 ? 'Cdark' : $class );
    echo '<tr><td>'.$r['show'].'</td>';
    echo '<td class="text-center"><span class="label label-default">'.$ar[$r['func']].'</span></td>';
    if ( $r['func'] < 3 ) {
		  echo '<td class="text-center" nowrap><a style="margin-right:5px;" href="?profilefields-edit-'.$r['id'].'" rel="tooltip" title="&auml;ndern"><span style="color:#2D9600;" class="glyphicon glyphicon-edit" aria-hidden="true"></span></a><a href="javascript:delcheck('.$r['id'].')" rel="tooltip" title="l&ouml;schen"><span style="color:#AD0000;" class="glyphicon glyphicon-trash" aria-hidden="true"></span></a></td>';
		} else {
      echo '<td class="text-center"><a href="?profilefields-c-'.$r['id'].'-'.$r['func'].'">'.($r['func']==3?'<span class="label label-danger">verstecken</span>':'<span class="label label-success">anzeigen</span>').'</a></td>';
    }
    echo '<td nowrap><a style="margin-right:5px;" href="?profilefields-o-'.$r['id'].'-'.$r['pos'].'" rel="tooltip" title="nach oben verschieben"><span style="color:#C64F00;" class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span></a><a href="?profilefields-u-'.$r['id'].'-'.$r['pos'].'" rel="tooltip" title="nach unten verschieben"><span style="color:#C64F00;" class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span></a></td>';
	  echo '</tr>';
	}
	$tpl->out(2);

}

$design->footer();
?>