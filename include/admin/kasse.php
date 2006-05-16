<?php
defined ('main') or die ( 'no direct access' );
defined ('admin') or die ( 'only admin access' );

$design = new design ( 'Admins Area', 'Admins Area', 2 );
$design->header();

if (isset($_POST['ksub']) AND !empty($_POST['kontodaten'])) {
  $kontodaten = escape($_POST['kontodaten'], 'textarea');
  db_query("UPDATE prefix_allg SET t1 = '".$kontodaten."' WHERE k = 'kasse_kontodaten'");
} elseif (isset($_POST['sub'])) {
  $name = escape($_POST['name'], 'string');
  $verwendung = escape($_POST['verwendung'], 'string');
  $betrag = str_replace(',','.',$_POST['betrag']);
  $datum = get_datum ($_POST['datum']);
  if (!is_numeric($betrag)) {
    echo 'der Betrag is keine Nummer?.. !!';
  } else {
    db_query("INSERT INTO prefix_kasse (datum,name,verwendung,betrag) VALUES ('".$datum."','".$name."','".$verwendung."',".$betrag.")");
    echo 'Buchung wurde gespeichert ... ';
  }
}

$kontodaten = db_result(db_query("SELECT t1 FROM prefix_allg WHERE k = 'kasse_kontodaten'"),0);
$kontodaten = unescape($kontodaten);

$tpl = new tpl ('kasse', 1);
$tpl->set('kontodaten', $kontodaten);
$tpl->set('datum', date('d.m.Y'));
$tpl->out(0);
  
$design->footer();
?>