<?php 
#   Copyright by: Manuel Staechele
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );
defined ('admin') or die ( 'only admin access' );

function forum_admin_showcats ( $id , $stufe ) {
  $q = "SELECT * FROM prefix_forumcats WHERE cid = ".$id." ORDER BY pos";
	$erg = db_query($q);
	if ( db_num_rows($erg) > 0 ) {
 	  while ($row = db_fetch_object($erg) ) {
	    echo '<tr class="Cmite"><td>'.$stufe.'- <a href="?forum-S'.$row->id.'">'.$row->name.'</a></td>';
      echo '<td><a href="admin.php?forum-changeCategorie-'.$row->id.'"><img src="include/images/icons/edit.gif" border="0" alt="&auml;ndern" title="&auml;ndern"></a></td>';
      echo '<td><a href="javascript:delCcheck('.$row->id.')"><img src="include/images/icons/del.gif" border="0" alt="l&ouml;schen" title="l&ouml;schen"></a></td>';
      echo '<td><a href="admin.php?forum-moveCategorie-0-'.$row->id.'-'.$row->pos.'"><img src="include/images/icons/pfeilo.gif" border="0" title="hoch" alt="hoch"></a></td>';
      echo '<td><a href="admin.php?forum-moveCategorie-1-'.$row->id.'-'.$row->pos.'"><img src="include/images/icons/pfeilu.gif" border="0" title="runter" alt="runter"></a></td></tr>';
		  forum_admin_showcats($row->id, $stufe.' &nbsp; &nbsp;' );
	  }
	}
}

$show = true;
$um = $menu->get(1);

if ($um == 'choosemods') {
  $design = new design ( 'Admins Area', 'Admins Area', 0 );
  $design->header();
} else {
  $design = new design ( 'Admins Area', 'Admins Area', 2 );
  $design->header();
}

switch ( $um ) {
  case 'choosemods' :
    $fid = escape($_REQUEST['fid'], 'integer');
    if (isset($_POST['s']) AND $_POST['s'] == 'Add') {
      # find user id
      $name = escape($_POST['name'], 'string');
      $uid = @db_result(@db_query("SELECT id FROM prefix_user where name = BINARY '".$name."'"),0,0);
      
      if (!empty($uid) AND 0 == db_result(db_query("SELECT COUNT(*) FROM prefix_forummods WHERE uid = ".$uid." AND fid = ".$fid),0)) {
        db_query("INSERT INTO prefix_forummods (uid,fid) VALUES (".$uid.", ".$fid.")");
      }
    }
    # delete
    if ($menu->getA(2) == 'd' AND is_numeric($menu->getE(2))) {
      $uid = escape($menu->getE(2), 'integer');
      db_query("DELETE FROM prefix_forummods WHERE uid = ".$uid." AND fid = ".$fid);
    }
    
    $tpl = new tpl ('forum/mods', 1);
    $tpl->set('fid', $fid);
    $tpl->out(0);
    $class = '';
    $erg = db_query("SELECT name, uid FROM prefix_forummods LEFT JOIN prefix_user ON prefix_user.id = prefix_forummods.uid WHERE prefix_forummods.fid = ".$fid);
    while($r = db_fetch_assoc($erg)) {
      $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite');
      $r['class'] = $class;
      $tpl->set_ar_out($r, 1);
    }
    $tpl->out(2);
    $show = false;
    break;
  case 'newForum' :
		if ( empty ( $_POST['sub'] ) ) {
		  # false if no cat exists
			if ( db_result(db_query("SELECT COUNT(id) FROM prefix_forumcats"),0) == 0 ) {
			  wd ( 'admin.php?forum-newCategorie', 'Erst eine neue Kategorie anlegen dann ein Forum' );
				die ();
			}
			
      $ar = array(
			  'ak' => 'new',
				'sub' => 'Eintragen',
				'name' => '',
				'fid' => '',
				'text' => ''
			);
			$tpl = new tpl ('forum/eforum',1);
			$ar['kats']  = dbliste('',$tpl,'kats',"SELECT id, name FROM prefix_forumcats ORDER BY name");
			$ar['view']   = '<optgroup label="Grundrechte">';
			$ar['view']  .= dbliste('', $tpl,'view',"SELECT id, name FROM prefix_grundrechte ORDER BY id DESC");
			$ar['view']  .= '</optgroup>';
      $ar['view']  .= '<optgroup label="Gruppen">';
			$ar['view']  .= dbliste('', $tpl,'view',"SELECT id, name FROM prefix_groups ORDER BY id DESC");
			$ar['view']  .= '</optgroup>';
			$ar['reply']  = '<optgroup label="Grundrechte">';
			$ar['reply'] .= dbliste('', $tpl,'reply',"SELECT id, name FROM prefix_grundrechte ORDER BY id DESC");
			$ar['reply'] .= '</optgroup>';
      $ar['reply'] .= '<optgroup label="Gruppen">';
			$ar['reply'] .= dbliste('', $tpl,'reply',"SELECT id, name FROM prefix_groups ORDER BY id DESC");
			$ar['reply'] .= '</optgroup>';
			$ar['start']  = '<optgroup label="Grundrechte">';
			$ar['start'] .= dbliste('', $tpl,'start',"SELECT id, name FROM prefix_grundrechte ORDER BY id DESC");
			$ar['start'] .= '</optgroup>';
      $ar['start'] .= '<optgroup label="Gruppen">';
			$ar['start'] .= dbliste('', $tpl,'start',"SELECT id, name FROM prefix_groups ORDER BY id DESC");
			$ar['start'] .= '</optgroup>';
			$tpl->set_ar_out($ar,0);
			unset($tpl);
			$show = false;
		} else {
		  $cid = escape($_POST['cid'],'integer');
			$name = escape($_POST['name'],'string');
			$text = escape($_POST['text'],'string');
			$view = escape($_POST['view'],'integer');
			$start = escape($_POST['start'],'integer');
			$reply = escape($_POST['reply'],'integer');
		  $a = db_count_query("SELECT COUNT(id) as anz FROM prefix_forums WHERE cid = ".$cid);
			db_query("INSERT INTO prefix_forums (cid,view,start,reply,pos,name,besch) VALUES (".$cid.",".$view.",".$start.",".$reply.",".$a.",'".$name."','".$text."')");
		}
	  break;
	case 'changeForum' :
	  if ( empty ($_POST['sub']) ) {
      $fid = escape($menu->get(2),'integer');
			$row = db_fetch_object(db_query("SELECT * FROM prefix_forums WHERE id = ".$fid));
			$ar = array(
			  'ak' => 'change',
				'sub' => '&Auml;ndern',
				'fid' => $fid,
				'name' => $row->name,
				'text' => $row->besch
			);
			$tpl = new tpl ('forum/eforum',1);
			$ar['kats'] = dbliste($row->cid,$tpl,'kats',"SELECT id, name FROM prefix_forumcats ORDER BY name");
			
			
			$ar['view']   = '<optgroup label="Grundrechte">';
			$ar['view']  .= dbliste($row->view, $tpl,'view',"SELECT id, name FROM prefix_grundrechte ORDER BY id DESC");
			$ar['view']  .= '</optgroup>';
      $ar['view']  .= '<optgroup label="Gruppen">';
			$ar['view']  .= dbliste($row->view, $tpl,'view',"SELECT id, name FROM prefix_groups ORDER BY id DESC");
			$ar['view']  .= '</optgroup>';
			$ar['reply']  = '<optgroup label="Grundrechte">';
			$ar['reply'] .= dbliste($row->reply, $tpl,'reply',"SELECT id, name FROM prefix_grundrechte ORDER BY id DESC");
			$ar['reply'] .= '</optgroup>';
      $ar['reply'] .= '<optgroup label="Gruppen">';
			$ar['reply'] .= dbliste($row->reply, $tpl,'reply',"SELECT id, name FROM prefix_groups ORDER BY id DESC");
			$ar['reply'] .= '</optgroup>';
			$ar['start']  = '<optgroup label="Grundrechte">';
			$ar['start'] .= dbliste($row->start, $tpl,'start',"SELECT id, name FROM prefix_grundrechte ORDER BY id DESC");
			$ar['start'] .= '</optgroup>';
      $ar['start'] .= '<optgroup label="Gruppen">';
			$ar['start'] .= dbliste($row->start, $tpl,'start',"SELECT id, name FROM prefix_groups ORDER BY id DESC");
			$ar['start'] .= '</optgroup>';
			$tpl->set_ar_out($ar,0);
			unset($tpl);
			$show = false;
		} else {
		  $cid = escape($_POST['cid'],'integer');
			$name = escape($_POST['name'],'string');
			$text = escape($_POST['text'],'string');
			$view = escape($_POST['view'],'integer');
			$start = escape($_POST['start'],'integer');
			$reply = escape($_POST['reply'],'integer');
			$fid = escape($_POST['fid'],'integer');
			$r = db_fetch_object(db_query("SELECT * FROM prefix_forums WHERE id = ".$fid));
			if ( $cid != $r->cid ) {
			  db_query("UPDATE prefix_forums SET pos = pos -1 WHERE pos > ".$r->pos);
				$a = db_count_query("SELECT COUNT(*) as anz FROM prefix_forums WHERE cid = ".$cid);
			} else {
			  $a = $r->pos;
			}
			db_query("UPDATE prefix_forums SET name = '".$name."', besch = '".$text."', cid = ".$cid.", pos = ".$a.", start = ".$start.", reply = ".$reply.", view = ".$view." WHERE id = ".$fid);
		}
	  break;
	case 'deleteForum' :
	  $fid = escape($menu->get(2),'integer');
		db_query("DELETE FROM prefix_posts WHERE fid = ".$fid);
		db_query("DELETE FROM prefix_topics WHERE fid = ".$fid);
		$pos = db_result(db_query("SELECT pos FROM prefix_forums WHERE id = ".$fid),0);
		db_query("DELETE FROM prefix_forums WHERE id = ".$fid);
		db_query("UPDATE prefix_forums SET pos = pos -1 WHERE pos > ".$pos);
	  break;
  case 'moveForum' :
      $move = $menu->get(2);
      $fid = $menu->get(3);
      $pos = $menu->get(4);
      $cid = $menu->get(5);
	    $a = db_count_query("SELECT COUNT(*) as anz FROM prefix_forums WHERE cid = ".$cid);
			$np = ( $move == 0 ? $pos -1 : $pos+1 );
			$np = ( $np >= ( $a -1 ) ? ( $a - 1) : $np );
      $np = ( $np < 0 ? 0 : $np );
			db_query("UPDATE prefix_forums SET pos = ".$pos." WHERE pos = ".$np." AND cid = ".$cid);
			db_query("UPDATE prefix_forums SET pos = ".$np." WHERE id = ".$fid);
	  break;
	case 'newCategorie' :
	  if ( empty ($_POST['cat_sub']) ) {
			$show = true;
		} else {
		  $name = escape($_POST['name'],'string');
			$cid = escape($_POST['Ccat'],'integer');
			$a = db_count_query("SELECT COUNT(*) as anz FROM prefix_forumcats WHERE cid = $cid");
      db_query("INSERT INTO prefix_forumcats (name,pos,cid) VALUES ('".$name."',".$a.",$cid)");
		}
	  break;
	case 'changeCategorie' :
	  if ( empty ($_POST['cat_sub']) ) {
			$cid = escape($menu->get(2),'integer');
			$r = db_fetch_object(db_query("SELECT name as name FROM prefix_forumcats WHERE id = ".$cid));
			$show = true;
		} else {
		  $name = escape($_POST['name'],'string');
			$cid = escape($_POST['cid'],'integer');
			db_query("UPDATE prefix_forumcats SET name = '".$name."' WHERE id = ".$cid);
		}
	  break;
	case 'deleteCategorie' :
	    
			$cid = escape($menu->get(2),'integer');
			$e = db_query("SELECT id FROM prefix_forums WHERE cid = ".$cid);
			while ($r = db_fetch_row($e) ) {
			  db_query("DELETE FROM prefix_posts WHERE fid = ".$r[0]);
				db_query("DELETE FROM prefix_topics WHERE fid = ".$r[0]);
			}
			db_query("DELETE FROM prefix_forums WHERE cid = ".$cid);
			$pos = db_result(db_query("SELECT pos FROM prefix_forumcats WHERE id = ".$cid),0);
		  db_query("UPDATE prefix_forumcats SET pos = pos -1 WHERE pos > ".$pos);
			db_query("DELETE FROM prefix_forumcats WHERE id = ".$cid);
	  break;
  case 'moveCategorie' :
      $move = $menu->get(2);
      $cid = $menu->get(3);
      $topcid = db_result(db_query("SELECT cid FROM `prefix_forumcats` WHERE id = $cid"),0);
      $pos = $menu->get(4);
	    $a = db_count_query("SELECT COUNT(*) as anz FROM prefix_forumcats WHERE cid = $topcid");
			$np = ( $move == 0 ? $pos -1 : $pos+1 );
			$np = ( $np >= ( $a -1 ) ? ( $a - 1) : $np );
      $np = ( $np < 0 ? 0 : $np );
			db_query("UPDATE prefix_forumcats SET pos = ".$pos." WHERE cid = ".$topcid." AND pos = ".$np);
			db_query("UPDATE prefix_forumcats SET pos = ".$np." WHERE id = ".$cid);
	  break;
}


if ( $show ) {
  $tpl = new tpl ( 'forum/forum', 1);
  $tpl->out(0); $class = '';
  $firstcat = @db_result(db_query("SELECT id FROM `prefix_forumcats` LIMIT 1"),0);
  $id = ($menu->getA(1) == 'S' ? $menu->getE(1) : (is_numeric($firstcat)?$firstcat:0));
  $erg = db_query("SELECT id, cid, name as cname, pos as cpos FROM prefix_forumcats WHERE id = $id ORDER BY pos");
  while ($row = db_fetch_assoc($erg) ) {
    $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite' );
	  $row['class'] = $class;
	  $tpl->set_ar_out($row,1);
	  $erg1 = db_query("SELECT
      prefix_forums.id as fid,
      prefix_forums.name as fname,
      prefix_forums.pos as fpos,
      case when view  <= 0 then vg.name else vt.name end as view,
      case when reply <= 0 then rg.name else rt.name end as reply,
      case when start <= 0 then sg.name else st.name end as start
    FROM prefix_forums
      LEFT JOIN prefix_grundrechte as vg ON prefix_forums.view = vg.id 
      LEFT JOIN prefix_grundrechte as rg ON rg.id = prefix_forums.reply
      LEFT JOIN prefix_grundrechte as sg ON sg.id = prefix_forums.start
      
			LEFT JOIN prefix_groups as vt ON prefix_forums.view = vt.id
      LEFT JOIN prefix_groups as rt ON rt.id = prefix_forums.reply
      LEFT JOIN prefix_groups as st ON st.id = prefix_forums.start
    WHERE prefix_forums.cid = ".$row['id']." ORDER BY prefix_forums.pos" );
	  while ($row1 = db_fetch_assoc($erg1) ) {
	    $row1['class'] = $row['class'];
      $tpl->set_ar_out($row1,2);
    }
  }
  $tpl->out(3);
  
  forum_admin_showcats(0,'');
  $cid = (is_numeric($r->topcid)?$r->topcid:0);
  $Cout = array();
  $Cout['ak'] = ($um == 'changeCategorie' ? 'change' : 'new');
  $Cout['sub'] = ($um == 'changeCategorie' ? '&auml;ndern' : 'erstellen');
  $Cout['name'] = ($um == 'changeCategorie' ? $r->name : '');
  $Cout['cat'] = '<option value="0">Keine</option>'.dblistee($cid, "SELECT id,name FROM prefix_forumcats ORDER BY name");
  $tpl->set_ar_out($Cout,4);
  
}


//-----------------------------------------------------------|

$design->footer();
?>
