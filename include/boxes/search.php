<?php 
#   Copyright by Manuel Staechele
#   Support www.ilch.de


defined ('main') or die ( 'no direct access' );

$tpl = new tpl ('search');
$tpl->set ('size', 16);
$tpl->set ('search', escape($_GET['search'],'string'));
$tpl->set ('autor', escape($_GET['autor'],'string'));
for($i=1;$i<=3;$i++){
if($_GET['in'] == $i) $tpl->set ('checked'.$i, 'checked="checked"');
} if(!isset($_GET['in'])) $tpl->set ('checked1', 'checked="checked"');
$tpl->out(0);

?>