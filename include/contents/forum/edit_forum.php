<?php 
#   Copyright by: Manuel Staechele
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );

if ( $forum_rights['mods'] == FALSE ) {
  $forum_failure[] = 'Keine Berechtigung dieses Forum zu moderiren';
}

check_forum_failure($forum_failure);

$title = $allgAr['title'].' :: Forum :: '.$aktForumRow['kat'].' :: '.$aktForumRow['name'];
$hmenu  = $extented_forum_menu.'<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b><a class="smalfont" href="index.php?forum-showcat-'.$aktForumRow['cid'].'">'.$aktForumRow['kat'].'</a><b> &raquo; </b>'.$aktForumRow['name'].$extented_forum_menu_sufix;
$design = new design ( $title , $hmenu, 1);
$design->header();

if (isset($_POST['status'])) {
  foreach ($_POST['in'] as $k => $v) {
    $astat = db_result(db_query("SELECT stat FROM prefix_topics WHERE id = ".$k), 0, 0);
    $nstat = ($astat == 1 ? 0 : 1 );
    db_query("UPDATE `prefix_topics` SET stat = '".$nstat."' WHERE id = ".$k);
  }
  wd ( 'index.php?forum-showtopics-'.$fid , 'Status ge&auml;ndert' , 2 );
} elseif (empty($_POST['del']) AND empty($_POST['shift'])) {
  $limit = $allgAr['Ftanz'];  // Limit 
  $page = ( $menu->getA(3) == 'p' ? $menu->getE(3) : 1 );
  $MPL = db_make_sites ($page , "WHERE fid = '$fid'" , $limit , '?forum-editforum-'.$fid , 'topics' );
  $anfang = ($page - 1) * $limit;
	$q = "SELECT a.id, a.name, a.rep, a.erst, a.hit, a.art, a.stat, b.time, b.erst as last, b.id as pid
   FROM prefix_topics a
  	LEFT JOIN prefix_posts b ON a.last_post_id = b.id
  	WHERE a.fid = {$fid}
   	ORDER BY a.art DESC, b.time DESC
   	LIMIT ".$anfang.",".$limit;
  $tpl = new tpl ('forum/editforum.htm');
  $tpl->set('id', $fid);
  $tpl->set_out('MPL', $MPL, 0);
  $erg = db_query($q);
  while($row = db_fetch_assoc($erg) ) {
    $row['date'] = date('d.m.y - H:i',$row['time']);
    $tpl->set_ar_out($row, 1);  
  }
  $tpl->out(2);
} elseif (isset($_POST['del']) AND isset($_POST['dely']) AND $_POST['dely'] == 'yes') {
  $pmin = 0;
  $tmin = 0;
  foreach ($_POST['in'] as $k => $v) {
		$erg = db_query("SELECT erstid FROM prefix_posts WHERE tid = ".$k." AND erstid > 0");
		while ($row = db_fetch_object($erg) ) {
		  db_query("UPDATE prefix_user SET posts = posts - 1 WHERE id = ".$row->erstid);
      $pmin++;
		}
    $tmin++;
    db_query("DELETE FROM prefix_posts WHERE tid = ".$k);
    db_query("DELETE FROM prefix_topics WHERE id = ".$k);
  }
  $pid = db_result(db_query("SELECT MAX(id) FROM prefix_posts WHERE fid = ".$fid),0);
  if ( empty($pid) ) { $pid = 0; }
  db_query("UPDATE `prefix_forums` SET last_post_id = ".$pid.", `posts` = `posts` - ".$pmin.", `topics` = `topics` - ".$tmin." WHERE id = ".$fid);
	wd ('index.php?forum-editforum-'.$fid, 'Die Themen wurden gel&ouml;scht' , 2 );
} elseif (isset($_POST['shift']) AND isset($_POST['nfid'])) {

  $fal = db_result(db_query("SELECT name FROM prefix_forums WHERE id = ".$_POST['afid']),0);
  $fne = db_result(db_query("SELECT name FROM prefix_forums WHERE id = ".$_POST['nfid']),0);

  $tmin = 0;
  $pmin = 0;
  foreach ($_POST['in'] as $k => $v) {
    $tmin++;
    $pmin += db_result(db_query("SELECT rep FROM prefix_topics WHERE id = ".$k), 0, 0);
	  db_query("UPDATE `prefix_topics` SET `fid` = ".$_POST['nfid']." WHERE id = ".$k);
		db_query("UPDATE prefix_posts SET `fid` = ".$_POST['nfid']." WHERE tid = ".$k);
  
    # autor benachrichtigen
    if (isset($_POST['alertautor']) AND $_POST['alertautor'] == 'yes') {
	    $uid = db_result(db_query("SELECT erstid FROM prefix_posts WHERE tid = ".$k." ORDER BY id ASC LIMIT 1"),0);
      $top = db_result(db_query("SELECT name FROM prefix_topics WHERE id = ".$k),0);
      $page = $_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"];
      $txt  = 'Dein Thema "'.$top.'" wurde von dem Forum "'.$fal.'" in das neue Forum "'.$fne.'" verschoben... ';
      $txt .= "\n\n- [url=http://".$page."?forum-showposts-".$k."]Link zum Thema[/url]";
      $txt .= "\n- [url=http://".$page."?forum-showtopics-".$_POST['nfid']."]Link zum neuen Forum[/url]";
      $txt .= "\n- [url=http://".$page."?forum-showtopics-".$_POST['afid']."]Link zum alten Forum[/url]";
      sendpm($_SESSION['authid'], $uid, 'Thema verschoben',$txt);
    }
  }
  $pmin = $pmin + $tmin;
  $apid = db_result(db_query("SELECT MAX(id) FROM prefix_posts WHERE fid = ".$_POST['afid']),0);
	$npid = db_result(db_query("SELECT MAX(id) FROM prefix_posts WHERE fid = ".$_POST['nfid']),0);
  if ( empty($apid) ) { $apid = 0; }
  db_query("UPDATE `prefix_forums` SET last_post_id = ".$apid.", `posts` = `posts` - ".$pmin.", `topics` = `topics` - ".$tmin." WHERE id = ".$_POST['afid']);
	db_query("UPDATE `prefix_forums` SET last_post_id = ".$npid.", `posts` = `posts` + ".$pmin.", `topics` = `topics` + ".$tmin." WHERE id = ".$_POST['nfid']);      
      
	wd ( array (
	 'neue Themen Übersicht' => 'index.php?forum-showtopics-'.$_POST['nfid'],
	 'alte Themen Übersicht' => 'index.php?forum-showtopics-'.$_POST['afid'],
	) , 'Thema erfolgreich verschoben' , 3 );

} elseif (isset($_POST['del']) OR isset($_POST['shift'])) {
  echo '<form action="index.php?forum-editforum-'.$fid.'" method="POST">';
  foreach ($_POST['in'] as $k => $v) {
    echo '<input type="hidden" name="in['.$k.']" value="'.$v.'" />';
  }
  if (isset($_POST['del'])) {
    echo '<input type="hidden" name="dely" value="yes" />';
    echo 'Sicher die ausgewahlten Themen loeschen? <input type="submit" value="'.$lang['yes'].'" name="del" />';
  } elseif (isset($_POST['shift'])) {
    echo '<input type="hidden" name="afid" value="'.$fid.'">neues Forum ausw&auml;hlen<br />';
    echo '<select name="nfid">';	
    $erg1 = db_query("SELECT prefix_forums.id, prefix_forums.name, prefix_forumcats.name as cname FROM `prefix_forums` left join prefix_forumcats on prefix_forumcats.id = prefix_forums.cid WHERE prefix_forums.id != ".$fid." ORDER BY prefix_forums.cid, prefix_forums.pos");
    while ($row1 = db_fetch_assoc($erg1)) {
      if ( empty($acid) OR $acid != $row1['cname'] ) {
        if ( !empty($acid) AND $acid != $row1['cname'] ) {
          echo '</optgroup>';
        }
        echo '<optgroup label="'.$row1['cname'].'">';
        $acid = $row1['cname'];
      }
      echo '<option value="'.$row1['id'].'">'.$row1['name'].'</option>';
	  }
    echo '</optgroup>';
		echo '</select><br /><input type="checkbox" name="alertautor" value="yes" /> Die Autoren &uuml;ber das verschieben informieren?<br /><input type="submit" value="'.$lang['shift'].'" name="shift">';
  }
  
  echo '</form>';
}

$design->footer();

?>