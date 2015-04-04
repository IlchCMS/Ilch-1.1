<?php
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );

$title = $allgAr['title'].' :: G&auml;stebuch';
$hmenu = 'G&auml;stebuch';
$design = new design ( $title , $hmenu );
$design->header();
# time sperre in sekunden
$timeSperre = $allgAr['Gsperre'];

/*

  gbook

	id , name , mail , page , ip , time , txt

*/

switch($menu->get(1)) {
case 1 :

	$tpl = new tpl ( 'gbook.htm' );
	$ar = array (
    'uname' => $_SESSION['authname'],
    'SMILIES' => getsmilies(),
		'ANTISPAM' => get_antispam ('gbook', 1),
    'TXTL' => $allgAr['Gtxtl']
  );
	$tpl->set_ar_out($ar,3);
  if (!isset($_SESSION['klicktime_gbook'])) { $_SESSION['klicktime_gbook'] = 0; }

break;
case 2 :

  $dppk_time = time();

  if (($_SESSION['klicktime_gbook'] + $timeSperre) < $dppk_time
  AND isset($_POST['name'])
  AND isset($_POST['txt'])
  AND trim($_POST['name']) != ""
  AND trim($_POST['txt']) != ""
  AND chk_antispam ('gbook' )
  AND strlen ($_POST['txt']) <= $allgAr['Gtxtl'] ) {

    $txt = escape($_POST['txt'], 'textarea');
	  $name = escape($_POST['name'], 'string');
	  $mail = escape($_POST['mail'], 'string');
	  $page = escape($_POST['page'], 'string');

  	db_query("INSERT INTO prefix_gbook (`name`,`mail`,`page`,`time`,`ip`,`txt`) VALUES ('".$name."', '".$mail."', '".$page."', '".time()."', '".getip()."', '".$txt."')");

    $_SESSION['klicktime_gbook'] = $dppk_time;
    wd('index.php?gbook',$lang['insertsuccessful']);
	} else {
	  echo '<div class="text-center"><span class="ilch_hinweis_rot">'.$lang['donotpostsofast'].'</span></div>';
	  echo '<div class="text-center"><span class="ilch_hinweis_rot">'.sprintf($lang['gbooktexttolong'], $allgAr['Gtxtl']).'</span></div>';
	  echo '<div class="text-center"><span class="ilch_hinweis_rot">'.$lang['plsfilloutallfields'].'</span></div>';
	}
  break;
case 'show' :
  if ($allgAr['gbook_koms_for_inserts'] == 1) {
    $id = escape($menu->get(2), 'integer');
    if (chk_antispam('gbookkom') AND isset($_POST['name']) AND isset($_POST['text'])) {
      $name = escape($_POST['name'], 'string');
      $text = escape($_POST['text'], 'string');
      db_query("INSERT INTO prefix_koms (name,text,uid,cat) VALUES ('".$name."', '".$text."', ".$id.", 'GBOOK')");
    }
    if ($menu->getA(3) == 'd' AND is_numeric($menu->getE(3)) AND has_right(-7, 'gbook')) {
      $did = escape($menu->getE(3), 'integer');
      db_query("DELETE FROM prefix_koms WHERE uid = ".$id." AND cat = 'GBOOK' AND id = ".$did);
    }

    $r  = db_fetch_assoc(db_query("SELECT time, name, mail, page, txt as text, id FROM prefix_gbook WHERE id = ".$id));
    $r['datum'] = date('d.m.Y', $r['time']);
    if ($r['page'] != '') {
      $r['page'] = get_homepage($r['page']);
      $r['page'] = '<a class="ilch_gbook_icons" href="'.$r['page'].'" title="Homepage '.$lang['from'].' '.$r['name'].'"><i class="fa fa-globe"></i></a>';
		}
		if (loggedin($r['mail'] != '')) {
	    $r['mail'] = '<a class="ilch_gbook_icons" href="mailto:'.escape_email_to_show($r['mail']).'" title="E-Mail '.$lang['from'].' '.$r['name'].'"><i class="fa fa-envelope"></i></a>';
		} else {
		$r['mail'] = '';
		}
    $tpl = new tpl ( 'gbook.htm' );
		$r['ANTISPAM'] = get_antispam('gbookkom', 0);
    $r['uname'] = $_SESSION['authname'];
    $r['text'] = bbcode($r['text']);
    $tpl->set_ar_out($r, 4);
    $i = 1;
    $erg = db_query("SELECT id, name, text FROM prefix_koms WHERE uid = ".$id." AND cat = 'GBOOK' ORDER BY id DESC");
    $anz = db_num_rows($erg)+1;
    while ($r1 = db_fetch_assoc($erg)) {
      $r1['zahl'] = $anz - $i;
      $r1['text'] = bbcode($r1['text']);
      if (has_right(-7, 'gbook')) { $r1['text'] .= '<a class="ilch_closed_icon" href="index.php?gbook-show-'.$id.'-d'.$r1['id'].'" title="'.$lang['delete'].'"><i class="fa fa-times"></i></a>'; }
      $tpl->set_ar_out($r1, 5);
      $i++;
    }
    $tpl->out(6);
  }
  break;
default :

  $limit = $allgAr['gbook_posts_per_site'];  // Limit
  $page = ( $menu->getA(1) == 'p' ? escape($menu->getE(1), 'integer') : 1 );
  $MPL = db_make_sites ($page , "" , $limit , "?gbook" , 'gbook' );
  $anfang = ($page - 1) * $limit;

	$tpl = new tpl ( 'gbook.htm' );

  $ei1 = @db_query("SELECT COUNT(ID) FROM prefix_gbook");
  $ein    = @db_result($ei1,0);
	$ar = array ('EINTRAGE' => $ein );
	$tpl->set_ar_out($ar,0);
	$erg = db_query("SELECT * FROM prefix_gbook ORDER BY time DESC LIMIT ".$anfang.",".$limit) or die (db_error());
	while ($row = db_fetch_object($erg)) {

    $page = '';
    $mail = '';
		if ($row->page) {
      $row->page = get_homepage($row->page);
      $page = '<a class="ilch_gbook_icons" href="'.$row->page.'" target="_blank" title="Homepage '.$lang['from'].' '.$row->name.'"><i class="fa fa-globe"></i></a>';
		}
		if (loggedin($row->mail)) {
	    $mail = '<a class="ilch_gbook_icons" href="mailto:'.escape_email_to_show($row->mail).'" title="E-Mail '.$lang['from'].' '.$row->name.'"><i class="fa fa-envelope"></i></a>';
		}
    $koms = '';
    if ($allgAr['gbook_koms_for_inserts'] == 1) {
      $koms = db_result(db_query("SELECT COUNT(*) FROM prefix_koms WHERE uid = ".$row->id." AND cat = 'GBOOK'"),0,0);
      $koms = '<a class="ilch_a_link_s" href="index.php?gbook-show-'.$row->id.'">'.$koms.' '.$lang['comments'].'</a>';
    }

		$ar = array ( 'NAME' => $row->name,
		                'DATE' => date("d.m.Y",$row->time),
                    'koms' => $koms,
										'MAIL' => $mail,
										'ID'   => $row->id,
										'PAGE' => $page,
										'TEXT' => BBCode($row->txt)
		  );

			$tpl->set_ar_out($ar,1);
	}
	$tpl->set_out('SITELINK', $MPL, 2 );
break;
}

//-----------------------------------------------------------|

$design->footer();

?>