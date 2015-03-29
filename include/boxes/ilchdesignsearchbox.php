<?php
//  Copyright by Manuel
//  Support www.ilch.de
//  modifiziert fuer ilchclan Design


defined ('main') or die ( 'no direct access' );

$suchtpl = <<<HTML
<form action="index.php?search" method="GET">
<input type="text" class="ilchclandearchbox" value="{search}" name="search" size="15" placeholder="Suchbegriff eingeben" title="Hier den Suchbegriff eingeben">
<input type="hidden" name="in" value="2">
<button type="submit" class="ilchclandearchboxsubmit">search</button>
</form>
HTML;

$tpl = new tpl ($suchtpl,3);
$tpl->set ('size', 16);
if(isset($_GET['search']))
	$tpl->set ('search', escape($_GET['search'],'string'));
else $tpl->set ('search', '');
$tpl->out(0);

?>