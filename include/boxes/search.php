<?php
#   Copyright by Manuel Staechele
#   Support www.ilch.de


defined ('main') or die ( 'no direct access' );

$suchtpl = <<<HTML
<form action="index.php?search" method="GET">
<input type="text" value="{search}" name="search" size="{size}" /><br />
<input type="hidden" name="in" value="2" />
<input type="submit" value="{_lang_search}" /><br />
</form>
<a href="index.php?search">{_lang_exsearch}</a>
HTML;

$tpl = new tpl ($suchtpl,3);
$tpl->set ('size', 16);
$tpl->set ('search', escape($_GET['search'],'string'));
$tpl->set ('autor', escape($_GET['autor'],'string'));
for($i=1;$i<=3;$i++){
if($_GET['in'] == $i) $tpl->set ('checked'.$i, 'checked="checked"');
} if(!isset($_GET['in'])) $tpl->set ('checked1', 'checked="checked"');
$tpl->out(0);

?>