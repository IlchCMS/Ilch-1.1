<?php
//  Copyright by: Manuel
//   Support: www.ilch.de


defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Admins Area', 'Admins Area', 2);
$design->header();

if (!is_admin()) {
    echo '<div class="alert alert-danger" role="alert">Dieser Bereich ist nicht fuer dich...</div>';
    $design->footer(1);
}

if (isset($_GET['m']) AND $_GET['m'] == 'm') {


    if (isset($_POST['sub'])) {
        // immer alle loeschen und dann alle eintragen fals gewuenscht hort sich doch
        // logisch an und ist es auch.
        $mid = escape($_POST['md'], 'integer');
        $gr = escape($_POST['gr'], 'integer');
        db_query("DELETE FROM prefix_modulerights USING prefix_modulerights, prefix_user WHERE prefix_user.id = prefix_modulerights.uid AND prefix_modulerights.mid = " . $mid . " AND prefix_user.recht = " . $gr);

        if ($_POST['ak'] == 1) {
            db_query("INSERT INTO prefix_modulerights (mid,uid) SELECT " . $mid . " as mid, id as uid FROM prefix_user WHERE recht = " . $gr);
        }

        wd(
                array(
            'Grundrechten' => 'admin.php?grundrechte',
            'Userverwalten' => 'admin.php?user',
            'zur&uuml;ck zu Modulrechte' => 'admin.php?grundrechte=0&amp;m=m',
                ), 'Die ge&uuml;nschte Operation wurde ausgef&uuml;hrt... Bitte &uuml;berpr&uuml;fen!!', 66
        );
        $design->footer(1);
    }

    $grl = dblistee('', "SELECT id, name FROM prefix_grundrechte ORDER BY id ASC");
    $mdl = dblistee('', "SELECT id, name FROM prefix_modules ORDER BY name");
    ?>

    <form action="admin.php?grundrechte=0&amp;m=m" method="POST" class="form-inline" role="form">
        <a class="btn btn-primary btn-sm" href="javascript: history.go(-1)">zur&uuml;ck</a><br><br>
        <legend><h2>Modulrechte f&uuml;r Grundrechte</h2></legend>
        <div class="panel panel-default" style="overflow-x:auto;overflow-y:hidden;">
            <div class="panel-body">
                <table class="table">
                    <tr>
                        <td class="danger">Allen</td>
                        <td class="warning"><select style="min-width:100px;" class="form-control" name="gr"><?php echo $grl; ?></select></td>
                        <td class="danger text-center">das Modulrecht</td>
                        <td class="text-right warning"><select style="min-width:100px;" class="form-control" name="md"><?php echo $mdl; ?></select></td>
                        <td class="text-left warning"><select style="min-width:70px;" class="form-control" name="ak"><option value="1">geben</option><option value="2">nehmen</option></select></td>
                        <td class="danger text-center"><input  class="btn btn-primary btn-sm" type="submit" value="Absenden" name="sub" /></td>
                    </tr>
                </table>
            </div></div>
    </form>

    <?php
    $design->footer(1);
}
$arb = array(
    -9 => 'Dieser User hat alle Rechte :-)',
    -8 => 'Dieser User darf alles mit einer paar Ausnahmen:
         er darf User &uuml;ber ihm nicht l&ouml;schen,
         diesen Bereich nicht &auml;ndern, kein Backup machen, die Konfiguration nicht ver&auml;ndern.',
    -7 => 'Der User darf alles auf der Seite administrieren. Also z.B. alle Foren Moderieren in die er rein kommt, Kommentare l&ouml;schen, Userbilder verwalten, War zu oder Absagen l&ouml;schen... Im Adminbereich hat er allerdings nur &uuml;ber Modulrechte etwas zu sagen.',
    -6 => 'Der User hat keine speziellen Rechte ausser die Ihm zugeteilten.',
    -5 => 'Der User hat keine speziellen Rechte ausser die Ihm zugeteilten.',
    -4 => 'Der User hat keine speziellen Rechte ausser die Ihm zugeteilten.',
    -3 => 'Der User hat keine speziellen Rechte ausser die Ihm zugeteilten.',
    -2 => 'Der User hat keine speziellen Rechte ausser die Ihm zugeteilten.',
    -1 => 'Der User hat keine speziellen Rechte ausser die Ihm zugeteilten.',
    0 => 'Dieses Recht bekommen alle G&auml;ste, also Besucher die nicht registriert sind',
);
if (isset($_POST['o'])) {
    $erg = db_query("SELECT * FROM prefix_grundrechte ORDER BY id ASC");
    while ($r = db_fetch_assoc($erg)) {
        if ($r['name'] != $_POST['gr'][$r['id']]) {
            db_query("UPDATE prefix_grundrechte SET name = '" . escape($_POST['gr'][$r['id']], 'string') . "' WHERE id = " . $r['id']);
        }
    }
    echo '<div class="alert alert-success" role="alert">Die &Auml;nderungen wurden gespeichert</div>';
}
?>
<form action="admin.php?grundrechte" method="POST" class="form-inline" role="form">

    <legend><h2><i class="fa fa-fire"></i> Grundrechte</h2></legend>
    <div class="panel panel-default" style="overflow-x:auto;overflow-y:hidden;">
        <div class="panel-body">
            <table class="table table-striped">
                <tr>
                    <td colspan="2"><input class="btn btn-primary" type="submit" value="&Auml;nderungen speichern" name="o"> <span class="pull-right"><a class="btn btn-warning" href="admin.php?grundrechte=0&m=m">Modulrechte f&uuml;r Grundrechte</a></span></td>
                </tr>
<?php
$class = '';
$erg = db_query("SELECT * FROM prefix_grundrechte ORDER BY id ASC");
while ($r = db_fetch_assoc($erg)) {
    $class = ($class == 'Cmite' ? 'Cnorm' : 'Cmite' );
    ?>
                    <tr>
                        <td><input style="min-width:100px;" class="form-control" name="gr[<?php echo $r['id']; ?>]" value="<?php echo $r['name']; ?>" >
                        </td><td><?php echo $arb[$r['id']]; ?></td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td colspan="2"><input class="btn btn-primary" type="submit" value="&Auml;nderungen speichern" name="o"></td>
                </tr>
            </table>
        </div></div>
</form>
<?php
$design->footer();
?>