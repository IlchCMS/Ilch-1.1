<?php 
#   Copyright by: Manuel Staechele
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );


$title = $allgAr['title'].' :: Forum';
$hmenu = $extented_forum_menu.'Forum'.$extented_forum_menu_sufix;
$design = new design ( $title , $hmenu, 1, 'forum/index.htm' );
$design->header();

if ($menu->get(1) == 'markallasread') {
  user_markallasread ();
}


$tpl = new tpl ( 'forum/showforum' );
$tpl->out (0);

$category_array = array();
$forum_array = array();

$q = "SELECT
  a.id, a.cid, a.name, a.besch,
  a.topics, a.posts, b.name as topic,
  c.id as pid, c.tid, b.rep, c.erst, c.time,
  a.cid, k.name as cname
FROM prefix_forums a
  LEFT JOIN prefix_forumcats k ON k.id = a.cid
  LEFT JOIN prefix_posts c ON a.last_post_id = c.id
  LEFT JOIN prefix_topics b ON c.tid = b.id
WHERE a.view  >= ".$_SESSION['authright']."
   OR a.reply >= ".$_SESSION['authright']."
   OR a.start >= ".$_SESSION['authright']."
ORDER BY k.pos, a.pos";
$erg1 = db_query($q);
$xcid = 0;
while ($r = db_fetch_assoc($erg1) ) {
  
  $r['topicl'] = $r['topic'];
  $r['topic']  = html_enc_substr($r['topic'],0,23);
  $r['ORD']    = forum_get_ordner($r['time'],$r['id']);
  $r['mods']   = getmods($r['id']);
  $r['datum']  = date('d.m.y - H:i', $r['time']);
  $r['page']   = ceil ( ($r['rep']+1)  / $allgAr['Fpanz'] );
  $tpl->set_ar ($r);
  
  if ($r['cid'] <> $xcid) {
    $tpl->out(1);
    $xcid = $r['cid'];
  }
  $tpl->out(2);
}

# statistic #
$ges_online_user = ges_online();
$stats_array = array (
  'privmsgpopup' => check_for_pm_popup (),
  'topics' => db_result(db_query("SELECT COUNT(ID) FROM `prefix_topics`"),0),
  'posts' => db_result(db_query("SELECT COUNT(ID) FROM `prefix_posts`"),0),
  'users' => db_result(db_query("SELECT COUNT(ID) FROM `prefix_user`"),0),
  'istsind' => ( $ges_online_user > 1 ? 'sind' : 'ist' ),
  'gesonline' => $ges_online_user,
  'gastonline' => ges_gast_online(),
  'useronline' => ges_user_online(),
  'userliste' => user_online_liste()
);

$tpl->set_ar_out($stats_array,3);

$design->footer();
?>