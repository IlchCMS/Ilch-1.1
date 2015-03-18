<?php
#   Copyright by Manuel
#   Support www.ilch.de


defined ('main') or die ( 'no direct access' );

$suchtpl = <<<HTML
<div class="text-center">
<form action="index.php?search" method="GET">
<input type="text" value="{search}" name="search" size="{size}" placeholder="Ich suche ....">
<input type="hidden" name="in" value="2">
<input type="submit" value="{_lang_search}">
</form>
<a href="index.php?search">{_lang_exsearch}</a>
</div>
HTML;

$tpl = new tpl ($suchtpl,3);
$tpl->set ('size', 16);
if(isset($_GET['search']))
	$tpl->set ('search', escape($_GET['search'],'string'));
else $tpl->set ('search', '');
$tpl->out(0);

?>