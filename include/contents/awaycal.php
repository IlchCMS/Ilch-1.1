<?php 
//   Copyright by: Manuel
//   Support: www.ilch.de

defined ('main') or die ( 'no direct access' );

//Text der gesendet wird, wenn ein neuer Eintrag ertellt wird
$message1 = $lang['awaycalnewenquiry'];
//Text der gesendet wird, wenn ein Eintrag bearbeitet wird
$message2 = $lang['awaycalchangedenquiry'];
//Text fuer den User wenn der Status geandert wurde
$message3 = $lang['awaycalstatuschanged'];

# function
function away_sendpmtoleaders ($m,$uid,$a) {
  $q = "SELECT
    DISTINCT prefix_user.id as uid
  FROM prefix_user
    LEFT JOIN prefix_groupusers ON prefix_groupusers.uid = ".$uid."
    LEFT JOIN prefix_groups ON prefix_groups.id = prefix_groupusers.gid
  WHERE recht <= -7
     OR (`mod1` = prefix_user.id AND uid = ".$uid.")
     OR (`mod2` = prefix_user.id AND uid = ".$uid.")
     OR (`mod3` = prefix_user.id AND uid = ".$uid.")
     OR (`mod4` = prefix_user.id AND uid = ".$uid.")";
  $erg = db_query($q);
  while ($r = db_fetch_assoc($erg)) {
    sendpm($_SESSION['authid'], $r['uid'], 'Away-Anfrage', $m, -1);
  }
}
$ILCH_HEADER_ADDITIONS .= "\n<link href=\"//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css\" rel=\"stylesheet\">";
$title = $allgAr['title'].' :: Awaycalender';
$hmenu = 'Awaycalender';
$design = new design ( $title , $hmenu );
$design->header();

$tpl = new tpl ( 'awaycal.htm' );

if($_SESSION['authright'] > -3) { //Pruefen ob der User ein TrialMember oder mehr ist
  echo $lang['nopermission'];
  $design->footer(1);
}

# status aendern
if ($menu->getA(1) == 'c' AND is_numeric($menu->getE(1)) AND is_numeric($menu->get(2)) AND is_siteadmin('awaycal')) {
  $uid = db_result(db_query("SELECT uid FROM prefix_awaycal WHERE id = ".$menu->getE(1)),0);
  db_query("UPDATE prefix_awaycal SET pruef = ".$menu->get(2)." WHERE id = ".$menu->getE(1));
  sendpm($_SESSION['authid'], $uid, 'Away-Anfrage', $message3);
}

if ($menu->getA(1) == 'd' AND is_numeric($menu->getE(1)) AND is_siteadmin('awaycal')) {
  db_query("DELETE FROM prefix_awaycal WHERE id = ".$menu->getE(1));
}

# eintragen
if (isset($_POST['ch'])) {
  $von = get_datum(escape($_POST['von'], 'string'));
  $bis = get_datum(escape($_POST['bis'], 'string'));
  $bet = escape($_POST['betreff'], 'string');
  $uid = $_SESSION['authid'];
  if (empty($_POST['ch'])) {
    away_sendpmtoleaders ($message1,$uid,0);
    db_query("INSERT INTO prefix_awaycal (uid,von,bis,betreff) VALUES (".$uid.",'".$von."','".$bis."','".$bet."')");
  } else {
    $id = escape($_POST['ch'],'integer');
    $uid = db_result(db_query("SELECT uid FROM prefix_awaycal WHERE id = ".$id),0);
    if (is_siteadmin('awaycal') OR $uid == $_SESSION['authid']) {
      away_sendpmtoleaders ($message2,$uid,1);
      db_query("UPDATE prefix_awaycal SET von = '".$von."', bis = '".$bis."', betreff = '".$bet."' WHERE id = ".$id);
    }
  }
}

$tpl->out(1); $class = '';
$statusar = array (2 => $lang['reported'], 1 => $lang['rejected'], 3 => $lang['allowed']);
$erg = db_query("SELECT pruef, DATE_FORMAT(von,'%d.%m.%Y') as von, DATE_FORMAT(bis,'%d.%m.%Y') as bis, betreff, prefix_user.name, uid, prefix_awaycal.id FROM prefix_awaycal LEFT JOIN prefix_user ON prefix_user.id = prefix_awaycal.uid ORDER BY id DESC");
while($r = db_fetch_assoc($erg)) {
  $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite' );
  $r['class']  = $class;
  $r['status'] = $statusar[$r['pruef']];
  if ($r['uid'] == $_SESSION['authid'] OR is_siteadmin('awaycal')) {
    $r['betreff'] .= '<br><span class="ilch_float_r"><a class="ilch_closed_icon" href="index.php?awaycal-d'.$r['id'].'"  title="'.$lang['delete'].'"><i class="fa fa-times"></i></a><a class="ilch_edit_icon" href="index.php?awaycal-e'.$r['id'].'" title="'.$lang['change'].'"><i class="fa fa-pencil-square-o"></i></a>';
    if (is_siteadmin('awaycal')) {
      $r['betreff'] .= '<a class="ilch_nob_icon" href="index.php?awaycal-c'.$r['id'].'-1" title="'.$lang['reject'].'"><i class="fa fa-thumbs-o-down"></i></a><a class="ilch_yep_icon" href="index.php?awaycal-c'.$r['id'].'-3" title="'.$lang['allow'].'"><i class="fa fa-thumbs-o-up"></i></a>';
    }
    $r['betreff'] .= '</span><br>';
  }
  $tpl->set_ar_out($r,2);
}
$tpl->out(3);

$e = FALSE;
if ($menu->getA(1) == 'e' AND is_numeric($menu->getE(1))) {
  $id = escape($menu->getE(1), 'intger');
  $ar = db_fetch_assoc(db_query("SELECT uid, id, von, bis, betreff FROM prefix_awaycal WHERE id = ".$id));
  $e |= (is_siteadmin('awaycal') OR $ar['uid'] == $_SESSION['authid']);
}

if ($e == FALSE) { $ar = array('id'=>'','von'=>date('d.m.Y'),'bis'=>date('d.m.Y'),'betreff'=>''); }
$tpl->set_ar_out($ar,0);
$design->footer();
?>