<?php
//   Copyright by: Manuel
//   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );


if ( $forum_rights['mods'] == FALSE ) {
  $forum_failure[] = $lang['nopermission'];
	check_forum_failure($forum_failure);
}


$title = $allgAr['title'].' :: Forum :: '.$aktForumRow['kat'].' :: '.$aktForumRow['name'].' :: '.$aktTopicRow['name'].' :: '.$lang['delete'];
$hmenu  = $extented_forum_menu.'<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b><a class="smalfont" href="index.php?forum-showcat-'.$aktForumRow['cid'].'">'.$aktForumRow['kat'].'</a><b> &raquo; </b><a class="smalfont" href="index.php?forum-showtopics-'.$fid.'">'.$aktForumRow['name'].'</a><b> &raquo; </b>';
$hmenu .= '<a class="smalfont" href="index.php?forum-showposts-'.$tid.'">'.$aktTopicRow['name'].'</a> <b> &raquo; </b>'.$lang['delete'] .$extented_forum_menu_sufix;
$design = new design ( $title , $hmenu, 1);
$design->header();

  $postid = escape($menu->get(3), 'integer');
  $csrfCheck = chk_antispam('forum_del_post', true);
  if ( empty($_POST['delete']) || !$csrfCheck ) {
    $tpl = new tpl ( 'forum/del_post' );
    $tpl->set_ar(array(
        'tid' => $tid,
        'get3' => $postid,
        'antispam' => get_antispam('forum_del_post', 0, true)
    ));
    $tpl->out(0);
} elseif ($csrfCheck) {
    $erstid = @db_result(db_query("SELECT erstid FROM `prefix_posts` WHERE id = ".$postid." LIMIT 1"),0);
    if ($erstid > 0) db_query("UPDATE `prefix_user` SET posts = posts - 1 WHERE id = $erstid");

    db_query("DELETE FROM `prefix_posts` WHERE id = ".$postid." LIMIT 1");
		$erg = db_query("SELECT MAX(id) FROM prefix_posts WHERE tid = ".$tid );
		$max = db_result($erg,0);
		db_query("UPDATE `prefix_topics` SET last_post_id = ".$max.", `rep` = `rep` - 1 WHERE id = ".$tid );
		db_query("UPDATE `prefix_forums` SET last_post_id = ".$max.", posts = posts - 1 WHERE id = ".$fid );

    $tpl = new tpl ( 'forum/del_post' );
    $tpl->set_out('tid',$tid,1);
	}

$design->footer();

?>