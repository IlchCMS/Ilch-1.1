<?php 
#   Copyright by: Manuel Staechele
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );


if ( $forum_rights['mods'] == FALSE ) {
  $forum_failure[] = 'Keine Berechtigung dieses Forum zu moderiren';
}

check_forum_failure($forum_failure);


$title = $allgAr['title'].' :: Forum :: '.$aktForumRow['kat'].' :: '.$aktForumRow['name'].' :: '.$aktTopicRow['name'].' :: Thema &auml;ndern';
$hmenu  = $extented_forum_menu.'<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b><a class="smalfont" href="index.php?forum-showcat-'.$aktForumRow['cid'].'">'.$aktForumRow['kat'].'</a><b> &raquo; </b><a class="smalfont" href="index.php?forum-showtopics-'.$fid.'">'.$aktForumRow['name'].'</a><b> &raquo; </b>';
$hmenu .= '<a class="smalfont" href="index.php?forum-showposts-'.$tid.'">'.$aktTopicRow['name'].'</a> <b> &raquo; </b>Thema &auml;ndern'.$extented_forum_menu_sufix;
$design = new design ( $title , $hmenu, 1);
$design->header();

$uum = $menu->get(3);
$tid = $menu->get(2);
switch($uum) {
  case 1 : # change topic title
    db_query("UPDATE `prefix_topics` SET name = '".$_REQUEST['newTopic']."' WHERE id = '".$tid."'");
		wd ( array ( 
			'zur&uuml;ck zum Thema' => 'index.php?forum-showposts-'.$tid,
		  'zur Themen &Uuml;bersicht' => 'index.php?forum-showtopics-'.$fid
		) , 'Das Themas wurde umbennant' , 3 );
    break;
	case 2 : # delete topic
	  if (empty($_POST['sub'])) {
      echo '<form action="index.php?forum-edittopic-'.$tid.'-2" method="POST">';
      echo 'Begr&uuml;ndung an den Ersteller (freiwillig)<br /><textarea cols="50" rows="2" name="reason"></textarea>';
      echo '<br /><br ><input type="submit" value="'.$lang['delete'].'" name="sub">';
      echo '</form>';
    } else {
      # autor benachrichtigen
      if (!empty($_POST['reason'])) {
	      $uid = db_result(db_query("SELECT erstid FROM prefix_posts WHERE tid = ".$tid." ORDER BY id ASC LIMIT 1"),0);
        $top = db_result(db_query("SELECT name FROM prefix_topics WHERE id = ".$tid),0);
        $page = $_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"];
        $txt  = "Dein Thema \"".$top."\" wurde gelöscht Begründung:\n\n".escape($_POST['reason'], 'string');
        sendpm($_SESSION['authid'], $uid, 'Theme gelöscht',$txt);
      }
    $postsMinus = $aktTopicRow['rep'] + 1;
		db_query("DELETE FROM `prefix_topics` WHERE id = '".$tid."' LIMIT 1");
		$erg = db_query("SELECT erstid FROM prefix_posts WHERE tid = ".$tid." AND erstid > 0");
		while ($row = db_fetch_object($erg) ) {
		  db_query("UPDATE prefix_user SET posts = posts - 1 WHERE id = ".$row->erstid);
		}
		db_query("DELETE FROM `prefix_posts` WHERE tid = '".$tid."'");
		$pid = db_result(db_query("SELECT MAX(id) FROM prefix_posts WHERE fid = ".$fid),0);
		if ( empty($pid) ) { $pid = 0; }
    db_query("UPDATE `prefix_forums` SET last_post_id = ".$pid.", `posts` = `posts` - ".$postsMinus.", `topics` = `topics` - 1 WHERE id = ".$fid);
		wd ('index.php?forum-showtopics-'.$fid, 'Das Thema wurde gel&ouml;scht' , 2 );
    }
	  break;
  case 3 : # move topic in another forum
		if ( empty ( $_POST['sub'] ) ) {
			echo '<form action="index.php?forum-edittopic-'.$tid.'-3" method="POST">';
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
		  echo '</select><br /><input type="checkbox" name="alertautor" value="yes" /> Den Autor &uuml;ber das verschieben informieren?<br /><input type="submit" value="Verschieben" name="sub"></form>';	
    } else {
      $postsMinus = $aktTopicRow['rep'] + 1;
			db_query("UPDATE `prefix_topics` SET `fid` = ".$_POST['nfid']." WHERE id = ".$tid);
			db_query("UPDATE prefix_posts SET `fid` = ".$_POST['nfid']." WHERE tid = ".$tid);
      $apid = db_result(db_query("SELECT MAX(id) FROM prefix_posts WHERE fid = ".$_POST['afid']),0);
			$npid = db_result(db_query("SELECT MAX(id) FROM prefix_posts WHERE fid = ".$_POST['nfid']),0);
		  if ( empty($apid) ) { $apid = 0; }
      db_query("UPDATE `prefix_forums` SET last_post_id = ".$apid.", `posts` = `posts` - ".$postsMinus.", `topics` = `topics` - 1 WHERE id = ".$_POST['afid']);
			db_query("UPDATE `prefix_forums` SET last_post_id = ".$npid.", `posts` = `posts` + ".$postsMinus.", `topics` = `topics` + 1 WHERE id = ".$_POST['nfid']);
      
      
      # autor benachrichtigen
      if (isset($_POST['alertautor']) AND $_POST['alertautor'] == 'yes') {
	      $uid = db_result(db_query("SELECT erstid FROM prefix_posts WHERE tid = ".$tid." ORDER BY id ASC LIMIT 1"),0);
        $fal = db_result(db_query("SELECT name FROM prefix_forums WHERE id = ".$_POST['afid']),0);
        $fne = db_result(db_query("SELECT name FROM prefix_forums WHERE id = ".$_POST['nfid']),0);
        $top = db_result(db_query("SELECT name FROM prefix_topics WHERE id = ".$tid),0);
        $page = $_SERVER["HTTP_HOST"].$_SERVER["SCRIPT_NAME"];
        $txt  = 'Dein Thema "'.$top.'" wurde von dem Forum "'.$fal.'" in das neue Forum "'.$fne.'" verschoben... ';
        $txt .= "\n\n- [url=http://".$page."?forum-showposts-".$tid."]Link zum Thema[/url]";
        $txt .= "\n- [url=http://".$page."?forum-showtopics-".$_POST['nfid']."]Link zum neuen Forum[/url]";
        $txt .= "\n- [url=http://".$page."?forum-showtopics-".$_POST['afid']."]Link zum alten Forum[/url]";
        sendpm($_SESSION['authid'], $uid, 'Thema verschoben',$txt);
      }
      
      
			wd ( array (
			 'neue Themen Übersicht' => 'index.php?forum-showtopics-'.$_POST['nfid'],
			 'alte Themen Übersicht' => 'index.php?forum-showtopics-'.$_POST['afid'],
			 'Zum Thema' => 'index.php?forum-showposts-'.$tid
			) , 'Thema erfolgreich verschoben' , 3 );
		}
		break;
  case 4 : # change topic status
    $aktion = ( $aktTopicRow['stat'] == 1 ? 0 : 1 );
	  db_query("UPDATE `prefix_topics` SET stat = '".$aktion."' WHERE id = '".$tid."'");
	  wd ( 'index.php?forum-showposts-'.$tid , 'ge&auml;ndert' , 0 );
	  break;
  case 5 : # change topic art
    $nart = ( $aktTopicRow['art'] == 0 ? 1 : 0 );
		db_query("UPDATE `prefix_topics` SET art = '".$nart."' WHERE id = ".$tid );
		wd ( array ( 
		  'zur&uuml;ck zum Thema' => 'index.php?forum-showposts-'.$tid,
			'zur Themen &Uuml;bersicht' => 'index.php?forum-showtopics-'.$fid
		) , 'Die Art des Themas wurde ge&auml;ndert' , 3 );
		break;
}
$design->footer();
?>