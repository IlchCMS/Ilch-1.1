<?php 
#   Copyright by: Manuel Staechele
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );

$title = $allgAr['title'].' :: User :: '.$lang['listofmembers'];
$hmenu = $extented_forum_menu.'User <b> &raquo; </b> '.$lang['listofmembers'].$extented_forum_menu_sufix;
$design = new design ( $title , $hmenu, 1);
$design->header();

$limit = 20;  // Limit 
$page = ($menu->getA(1) == 'p' ? $menu->getE(1) : 1 );
$MPL = db_make_sites ($page , "" , $limit , '?user' , 'user' );
$anfang = ($page - 1) * $limit;

$tpl = new tpl ( 'user/memb_list.htm' );
$tpl->set_out ( 'SITELINK', $MPL, 0);

$class = '';
$erg = db_query("SELECT
  posts,
  prefix_user.id,
  prefix_grundrechte.name as recht_name,
  regist,
  prefix_user.name
FROM prefix_user
 LEFT JOIN prefix_grundrechte ON prefix_user.recht = prefix_grundrechte.id
ORDER by recht,prefix_user.posts DESC LIMIT ".$anfang.",".$limit);
while ($row = db_fetch_object($erg)) {

	if ($class == 'Cmite') { $class = 'Cnorm'; } else { $class = 'Cmite'; }
	$ar = array ( 'NAME' => $row->name,
	                'RANG' => userrang($row->posts,$row->id),
									'CLASS' => $class,
									'POSTS' => $row->posts,
									'UID'   => $row->id,
									'DATE' => date('d.m.Y',$row->regist),
									'GRUPE' => $row->recht_name
	);
	$tpl->set_ar_out($ar,1);
}
$tpl->out(2);

$design->footer();
?>
