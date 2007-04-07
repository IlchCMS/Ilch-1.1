<?php
#   Copyright by: Manuel Staechele
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );
defined ('admin') or die ( 'only admin access' );

function groups_update_modulerights_for ($ar) {
  $re = array (
    0 => array ('wars', 'groups', 'awards', 'awaycal'),
    1 => array ('wars', 'groups', 'awards', 'awaycal'),
    2 => array ('wars', 'awaycal'),
    3 => array ('groups', 'awaycal'),
  );
  
  foreach ($ar as $k => $uid) {
    if (0 == $uid) { continue; }
    foreach ($re[$k] as $r) {
      $s = "SELECT id FROM prefix_modules WHERE url = '".$r."'";
      $mid = db_result(db_query($s),0,0);
      $s = "SELECT COUNT(*) FROM prefix_modulerights WHERE uid = ".$uid." AND mid = ".$mid;
      if (0 == db_result(db_query($s),0,0)) {
        db_query("INSERT INTO prefix_modulerights (mid,uid) VALUES (".$mid.",".$uid.")");
      }
    }
  }
}

$show = true;
$msg  = '';
$um   = $menu->get(1);

if ( $um == 'ins' ) {
	$pos		= db_result(db_query("SELECT COUNT(*) FROM prefix_groups"),0);
	$name		= escape($_POST['group_name'], 'string');
	$img		= escape($_POST['img'], 'string');
	$mod1		= escape($_POST['mod1'], 'integer');
	$mod2		= escape($_POST['mod2'], 'integer');
	$mod3		= escape($_POST['mod3'], 'integer');
	$mod4		= escape($_POST['mod4'], 'integer');
  $zeigen		= escape($_POST['zeigen'], 'integer');
	$fightus	= escape($_POST['fightus'], 'integer');
	$joinus		= escape($_POST['joinus'], 'integer');
	$gid		= escape($_POST['gid'], 'integer');
	db_query("INSERT INTO prefix_groups (name,img,`mod1`,`mod2`,`mod3`,`mod4`,show_fightus,show_joinus,zeigen,pos) VALUES ('".$name."','".$img."',".$mod1.",".$mod2.",".$mod3.",".$mod4.",".$fightus.",".$joinus.",".$zeigen.",".$pos.")");

  
  if (is_coadmin()) {
    groups_update_modulerights_for (array($mod1,$mod2,$mod3,$mod4));
  }
}

if ( isset ( $_POST['ins_user'] ) ) {
	$gid = escape($menu->get(2), 'integer');
	if ( empty($_POST['fid']) ) {
		$_POST['fid'] = 0;
	}
	$fid = 0;
	if (!empty($_POST['fid'])) {
	$fid = escape($_POST['fid'], 'integer');
	}
	$name = escape($_POST['name'], 'string');
	$uid = @db_result(@db_query("SELECT id FROM prefix_user where name = BINARY '".$name."'"),0,0);
	if (!empty($uid) AND 0 == db_result(db_query("SELECT COUNT(*) FROM prefix_groupusers WHERE gid = ".$gid." AND uid = ".$uid),0)) {
		db_query("INSERT INTO prefix_groupusers (gid,uid,fid) VALUES (".$gid.",".$uid.",".$fid.")");
	}
	$um = 'addusers';
}

if ( $um == 'ch' ) {
  $name		= escape($_POST['group_name'], 'string');
	$img		= escape($_POST['img'], 'string');
	$mod1		= escape($_POST['mod1'], 'integer');
	$mod2		= escape($_POST['mod2'], 'integer');
	$mod3		= escape($_POST['mod3'], 'integer');
	$mod4		= escape($_POST['mod4'], 'integer');
	$zeigen		= escape($_POST['zeigen'], 'integer');
	$fightus	= escape($_POST['fightus'], 'integer');
	$joinus		= escape($_POST['joinus'], 'integer');
	$gid		= escape($_POST['gid'], 'integer');
	db_query("UPDATE prefix_groups SET name = '".$name."', show_fightus = ".$fightus.", show_joinus = ".$joinus.", img = '".$img."', `mod1` = ".$mod1.", `mod2` = ".$mod2.", `mod3` = ".$mod3.", `mod4` = ".$mod4.", zeigen = ".$zeigen." WHERE id = ".$gid);
  
  if (is_coadmin()) {  
    # mods wieder die richtigen modulrechte geben. dazu erst loeschen, dann eintragen.
    groups_update_modulerights_for (array($mod1,$mod2,$mod3,$mod4));
  }
  
  $msg = "Die Gruppe wude ver&auml;ndert, die Modulrechte wurden erneuert. Wenn allerdings Leader, Co-Leader, Warorga oder Memberorga ge&auml;ndert wurden haben diese User immer noch die Modulrechte ... das sollte daher &uuml;berpr&uuml;ft werden.";
  
}

if (isset ($_GET['group_delete'])) {
	$pos = db_result(db_query("SELECT pos FROM prefix_groups WHERE id = ".$_GET['group_delete']),0);
  db_query("DELETE FROM prefix_groups WHERE id = ".$_GET['group_delete']);
	db_query("DELETE FROM prefix_groupusers WHERE gid = ".$_GET['group_delete']);
  db_query("UPDATE prefix_groups SET pos = pos -1 WHERE pos > ".$pos);
}

if ( $menu->get(3) == 'userdelete') {
	$gid = $menu->get(2);
	$uid = $menu->get(4);
	db_query("DELETE FROM prefix_groupusers WHERE gid = ".$gid." AND uid = ".$uid);
}

if ( $menu->get(3) == 'userchange') {
	$gid = escape($menu->get(2), 'integer');
	$uid = escape($menu->get(4), 'integer');
	$fid = escape($menu->get(5), 'integer');
	db_query("UPDATE `prefix_groupusers` SET fid = $fid WHERE gid = $gid AND uid = $uid");
}

if ( $um == 'addusers' ) {
	$design = new design ( 'Admins Area', 'Admins Area', 0 );
	$design->header();
	$gid = $menu->get(2);
	$tpl = new tpl ( 'groups/users', 1);

  $groupfuncs = array();
  $erg = db_query("SELECT id,name FROM prefix_groupfuncs ORDER BY pos");
  while ($row = db_fetch_object($erg)) {
    $groupfuncs[$row->id] = $row->name;  
  }
  
  function group_func ($gid, $uid, $fid, $gf) {
    $out = '<select id="user'.$uid.'" onchange="change_user('.$gid.', '.$uid.', this.value, '.$fid.', \'user'.$uid.'\');">';
    foreach ($gf as $key => $val) {
      $out .=  '<option value="'.$key.'" '.($fid == $key ? 'selected="selected"' : '').'>'.$val.'</option>';   
    }
    $out .= '</select>';
    return $out;
  }
  
	$row1 = db_fetch_object(db_query("SELECT name FROM prefix_groups WHERE id = ".$gid));
	$tpl->set('gruppe', $row1->name);
	$tpl->set('fehler', ( empty($fehler) ? '' : $fehler ) );
	$tpl->set('gid', $gid);
	$tpl->set('funcs', dbliste ( '', $tpl, 'funcs', "SELECT id,name FROM prefix_groupfuncs ORDER BY pos") );
	$tpl->out(0); $class = 'Cnorm';
	$q = "SELECT
	  a.fid,
		a.gid,
		a.uid,
		b.name as username,
		c.name as funcname
	FROM prefix_groupusers a
	LEFT JOIN prefix_user b ON a.uid = b.id
	LEFT JOIN prefix_groupfuncs c ON a.fid = c.id
	WHERE a.gid = ".$gid."
	ORDER BY c.pos";
	$erg = db_query($q);
	while($row = db_fetch_assoc($erg) ) {
		$class = ($class == 'Cnorm' ? 'Cmite' : 'Cnorm' );
		$row['funcname'] = group_func($gid, $row['uid'], $row['fid'], $groupfuncs);
		$row['class'] = $class;
		$tpl->set_ar_out($row,1);
	}
	$tpl->out(2);
	$show = false;
}

if ($menu->get(1) == 'move') {
	$id  = escape($menu->getE(2), 'integer');
	$pos = db_result(db_query("SELECT pos FROM prefix_groups WHERE id = ".$id),0);
	$anz = db_result(db_query("SELECT COUNT(*) FROM prefix_groups"),0);
	if ($menu->getA(2) == 'u') {
		$npos = $pos + 1;
	} elseif ($menu->getA(2) == 'o') {
		$npos = $pos - 1;
	}
  if ($npos < 0) {
    db_query("UPDATE prefix_groups SET pos = ".$anz." WHERE id = ".$id);
    db_query("UPDATE prefix_groups SET pos = pos -1");
  }
  if ($npos >= $anz) {
    db_query("UPDATE prefix_groups SET pos = -1 WHERE id = ".$id);
    db_query("UPDATE prefix_groups SET pos = pos +1");
  }
  
	if ($npos>=0 AND $npos < $anz) {
		db_query("UPDATE prefix_groups SET pos = ".$pos." WHERE pos = ".$npos);
		db_query("UPDATE prefix_groups SET pos = ".$npos." WHERE id = ".$id);
	}
}

if ($um == 'funcs') {
	$design = new design ( 'Admins Area', 'Admins Area', 0 );
	$design->header();

	if (isset($_POST['s']) AND $_POST['s'] == 'Add') {
		$pos = escape($_POST['apos'], 'integer');
		$name = escape($_POST['aname'], 'string');
		db_query("INSERT INTO prefix_groupfuncs (pos,name) VALUES (".$pos.", '".$name."')");
	} elseif (isset($_POST['s']) AND $_POST['s'] == 'Send') {
		$erg = db_query('SELECT * FROM `prefix_groupfuncs` ORDER BY pos');
		while ($row = db_fetch_assoc($erg) ) {
			if ((!empty($_POST['pos'][$row['id']]) AND !empty($_POST['name'][$row['id']])) AND $_POST['pos'][$row['id']] != $row['pos'] OR $_POST['name'][$row['id']] != $row['name']) {
				$pos = escape($_POST['pos'][$row['id']], 'integer');
				$name = escape($_POST['name'][$row['id']], 'string');
				db_query("UPDATE prefix_groupfuncs SET pos = ".$pos.", name = '".$name."' WHERE id = ".$row['id']);
			}
		}
	}
	if ($menu->getA(2) == 'd' AND is_numeric($menu->getE(2))) {
		$id = escape($menu->getE(2), 'integer');
		db_query("DELETE FROM prefix_groupfuncs WHERE id = ".$id);
	}

	$tpl = new tpl ( 'groups/funcs', 1);
	$tpl->out(0);
	$class = '';
	$erg = db_query('SELECT * FROM `prefix_groupfuncs` ORDER BY pos');
	while ($row = db_fetch_assoc($erg) ) {
		$class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite' );
		$row['class'] = $class;
		$tpl->set_ar_out($row,1);
	}
	$tpl->out(2);
	$show = false;
}
if ($um == 'joinus') {
	$design = new design ( 'Admins Area', 'Admins Area', 2 );
	$design->header();
  
  # als trial aufnehmen
  if ($menu->getA(2) == 'a' AND is_numeric($menu->getE(2)) AND $menu->getE(2) <> 0) {
    $check = escape($menu->get(3), 'string');
    $id    = escape($menu->getE(2), 'integer');
    if ($menu->get(4) == 'addtoteam') {
      $gid = db_count_query("SELECT groupid FROM `prefix_usercheck` WHERE `check` = '$check'");
      db_query("INSERT INTO `prefix_groupusers` (gid,uid,fid) VALUES ($gid,$id,4)");
      $msg = 'Er wurde als Trial in das Team eingetragen.';
    } else {
      $msg = 'Jetzt muss er noch in ein Team aufgenommen werden.';
    }
    db_query("DELETE FROM prefix_usercheck WHERE ak = 4 AND `check` = '".$check."'");
    db_query("UPDATE prefix_user SET recht = -3 WHERE id = ".$id." AND recht > -3");
    sendpm ($_SESSION['authid'], $id, 'Deine Joinus Anfrage', 'Du wurdest als Trial-Member aufgenommen.');
    $msg = 'erfolgreich als Trial markiert, der User wurde darueber informiert. '.$msg;
  }
  
  # aus check tabelle loeschen (nicht aufnehmen)
  if ($menu->getA(2) == 'd' AND is_numeric($menu->getE(2))) {
    $check = escape($menu->get(3), 'string');
    $id    = escape($menu->getE(2), 'integer');
    db_query("DELETE FROM prefix_usercheck WHERE ak = 4 AND `check` = '".$check."'");
    if ($id <> 0) {
      sendpm ($_SESSION['authid'], $id, 'Deine Joinus Anfrage', 'Deine Joinus Anfrage wurde leider abgelehnt');
    }
    $msg = 'erfolgreich gel&ouml;scht ..., wenn er schon registriert war wurde ihm eine Nachricht geschickt.';
  }
  
  $tpl = new tpl ( 'groups/joinus', 1);
  $tpl->set('msg',(empty($msg)?'':'<table width="50%" cellpadding="2" cellspacing="1" border="0" class="border"><tr><td class="Cnorm"><b>Nachricht:</b>&nbsp;'.$msg.'</td></tr></table>'));
  $tpl->out(0);
  
	$class = 'Cnorm';
  $erg = db_query("SELECT `check`, prefix_usercheck.name, prefix_user.id, prefix_user.email, prefix_groups.name as groupname FROM prefix_usercheck LEFT JOIN prefix_user ON prefix_user.name = BINARY prefix_usercheck.name LEFT JOIN prefix_groups ON prefix_groups.id = prefix_usercheck.groupid WHERE ak = 4");
  while ($r = db_fetch_assoc($erg)) {
    if ($r['id'] < 1) {
      $r['email'] = db_count_query("SELECT email FROM `prefix_usercheck` WHERE name = '{$r['name']}' AND ak");
    }
		$class = ($class == 'Cnorm' ? 'Cmite' : 'Cnorm' );
		$r['class'] = $class;
    $r['status'] = (empty($r['id'])?'Registrierung noch nicht abgeschlossen' : 'bereits Registriert');
    if (empty($r['id'])) { $r['id'] = 0; }
    $tpl->set_ar_out($r,1);
  }
  $tpl->out(2);

  $show = false;
}

if ( $show ) {
	$design = new design ( 'Admins Area', 'Admins Area', 2 );
	$design->header();
	$tpl = new tpl ( 'groups/groups', 1);

	if ( $um == 'edit' ) {
		$ar = db_fetch_assoc(db_query("SELECT id as gid, name, img, `mod1`, `mod2`, `mod3`, `mod4`, zeigen, show_joinus, show_fightus FROM prefix_groups WHERE id = ".$menu->get(2) ));
		$ar['ak'] = 'ch';
		$ar['zeigenja'] = ( $ar['zeigen'] == 1 ? 'checked' : '' );
		$ar['zeigenno'] = ( $ar['zeigen'] == 1 ? '' : 'checked' );
		$ar['joinusja'] = ( $ar['show_joinus'] == 1 ? 'checked' : '' );
		$ar['joinusno'] = ( $ar['show_joinus'] == 1 ? '' : 'checked' );
		$ar['fightusja'] = ( $ar['show_fightus'] == 1 ? 'checked' : '' );
		$ar['fightusno'] = ( $ar['show_fightus'] == 1 ? '' : 'checked' );
	} else {
		$ar = array (
		'name'=>'','img'=>'','mod1'=>'','mod2'=>'','mod3'=>'', 'mod4'=>'',
		'zeigenja'=>'','zeigenno'=>'checked','ak'=>'ins','gid'=>'',
		'fightusja'=>'','fightusno'=>'checked','joinusja'=>'','joinusno'=>'checked',
		);
	}
  
	$ar['mods1'] = dbliste ( $ar['mod1'] , $tpl, 'mods1', "SELECT id,name FROM prefix_user WHERE recht <= -4 ORDER BY name");
	$ar['mods2'] = dbliste ( $ar['mod2'] , $tpl, 'mods2', "SELECT id,name FROM prefix_user WHERE recht <= -4 ORDER BY name");
  $ar['mods3'] = dbliste ( $ar['mod3'] , $tpl, 'mods3', "SELECT id,name FROM prefix_user WHERE recht <= -4 ORDER BY name");
  $ar['mods4'] = dbliste ( $ar['mod4'] , $tpl, 'mods4', "SELECT id,name FROM prefix_user WHERE recht <= -4 ORDER BY name");
  $ar['mods2'] = '<option value="0">keiner</option>'.$ar['mods2'];
  $ar['mods3'] = '<option value="0">keiner</option>'.$ar['mods3'];
  $ar['mods4'] = '<option value="0">keiner</option>'.$ar['mods4'];
  $ar['pic']   = arlistee(  $ar['img'], get_teampic_ar() );
  $ar['pic']   = '<option value="0">kein Bild<option>'.$ar['pic'];
  $ar['msg']   = (empty($msg)?'':'<table width="50%" cellpadding="2" cellspacing="1" border="0" class="border"><tr><td class="Cnorm"><b>Nachricht:</b>&nbsp;'.$msg.'</td></tr></table>');
	$ar['joinu'] = '';
  if (0 < db_result(db_query("SELECT COUNT(*) FROM prefix_usercheck WHERE ak = 4"),0)) {
    $ar['joinu'] = '<a href="admin.php?groups-joinus"><b>Joinus Anfragen bearbeiten</b></a><br /><br />';
  }
  
  $tpl->set_ar_out($ar,0);

	$class = 'Cnorm';
	$erg = db_query("SELECT name,id FROM prefix_groups ORDER BY pos ASC");
	while($row = db_fetch_assoc($erg) ) {
		$row['useranz'] = db_count_query("SELECT COUNT(uid) FROM prefix_groupusers WHERE gid = ".$row['id']);
		$class = ($class == 'Cnorm' ? 'Cmite' : 'Cnorm' );
		$row['class'] = $class;
		$tpl->set_ar_out($row,1);
	}
	$tpl->out(2);
}

$design->footer();
?>
