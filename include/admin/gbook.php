<?php 
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );
defined ('admin') or die ( 'only admin access' );

$design = new design ( 'Admins Area', 'Admins Area', 2 );
$design->header();

##
###
####
##### A k t i o n e n


if ( !empty ($_GET['del']) ) {
  $id = escape($_GET['del'], 'integer');
  db_query("DELETE FROM `prefix_gbook` WHERE id = ".$id." LIMIT 1");
  db_query("DELETE FROM prefix_koms WHERE uid = ".$id." AND cat = 'GBOOK'");

}

if (isset($_POST['sub'])) {
  $name = escape($_POST['name'], 'string');
  $mail = escape($_POST['mail'], 'string');
  $page = escape($_POST['page'], 'string');
  $text = escape($_POST['text'], 'string');
  if (empty($_POST['gid'])) {
    db_query("INSERT INTO prefix_gbook (name, mail, page, txt, time) VALUES ('".$name."','".$mail."','".$page."','".$text."', '".time()."')");
  } else {
    $gid  = escape($_POST['gid'], 'integer');
    db_query("UPDATE prefix_gbook SET name = '".$name."', mail = '".$mail."', page = '".$page."', txt = '".$text."' WHERE id = ".$gid);
  }
}

$r = array ('name'=>'','mail'=>'','page'=>'','text'=>'','id'=>'');
if (isset($_GET['edit'])) {
  $id = escape($_GET['edit'], 'integer');
  $r = db_fetch_assoc(db_query("SELECT id, name, mail, page, txt as text FROM prefix_gbook WHERE id = ".$id));
}

$tpl = new tpl ('gbook', 1);
$tpl->set_ar_out($r,0);

$class = '';
$erg = db_query('SELECT name, mail, txt, time, page,  id FROM `prefix_gbook` ORDER BY time DESC');
while ($r = db_fetch_assoc($erg) ) {
	$time = date('d.m.y',$r['time']);
  $class = ($class == 'list-group-item-success' ? 'list-group-item-warning' : 'list-group-item-success' );
  $text  = substr(preg_replace("/\015\012|\015|\012/", " ", htmlentities(strip_tags(stripslashes($r['txt'])), ILCH_ENTITIES_FLAGS, ILCH_CHARSET)),0,150);
  $text = bbcode($text);
  echo '<li class="list-group-item '.$class.'">';
  echo '<a href="admin.php?gbook=0&edit='.$r['id'].'" style="margin-right:4px" title="bearbeiten"><span style="color:#2D9600;" class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>';
  echo '<a href="javascript:delcheck('.$r['id'].')" style="margin-right:4px" title="l&ouml;schen"><span style="color:#AD0000;" class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>';
  echo '<strong><a href="mailto:'.$r['mail'].'" title="mailto:'.$r['mail'].'">von '.$r['name'].'</a></strong>';
  echo '<span class="pull-right" style="margin-right:5px"><small>'.$time.'</small></span>';
  echo '<div class="panel panel-default"><div class="panel-body" style="color:#2f2f2f;overflow:auto;">'.$text.'</div></div>';
  echo '</li>';
}

$tpl->out(1);

$design->footer();
?>