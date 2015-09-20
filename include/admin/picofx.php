<?php
// by Manuel                        
// Modul Copyright   :  by David scarfhogg              

defined('main') or die('no direct access');
defined('admin') or die('only admin access');

function getGalleryCats($cats, $cat, &$op, $sel, $lvl) {
    foreach ($cats[$cat] as $k => $v) {
        $op .= '<option value="' . $k . '"' . ($sel == $k ? 'selected="selected"' : '') . '> ' . $lvl . ' ' . $v . '</option>';
        getGalleryCats($cats, $k, $op, $sel, $lvl . '-');
    }
}

$design = new design('Admins Area', 'Admins Area', 2);
$design->header();

$um = $menu->get(1);

if (empty($um)) {
    $svResult = db_query('SELECT * FROM prefix_allg WHERE k = \'picofx\'');
    while ($saRow = db_fetch_assoc($svResult)) {
        $picofxOpts[$saRow['v1']] = $saRow['v2'];
    }
    ?>
    <div class="cont_loose">
        <legend><h2>Pic of the X verwalten</h2></legend>
        <form action="admin.php?picofx-update" method="POST" class="form-horizontal" role="form">
            <div class="panel panel-default">
                <div class="panel-heading">Einstellungen</div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Kategorie</label>
                        <div class="col-xs-6">
                            <select class="form-control" name="directory">
                                <option value="">gallery</option>
    <?php
    $erg = db_query("SELECT id,cat,name FROM prefix_gallery_cats ORDER BY cat,pos");
    $cats = array();
    while ($r = db_fetch_object($erg)) {
        $cats[$r->cat][$r->id] = $r->name;
    }
    $outputcats = '';
    getGalleryCats($cats, 0, $outputcats, $picofxOpts['directory'], '-');
    echo $outputcats;

    $int_opts = array('0' => 'bei jedem Seitenaufruf', '1' => 'jeden Tag', '7' => 'jede Woche', '30' => 'jeden Monat', 'c' => 'selbstdefiniert');
    ?></select></div></div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Wechseln</label>
                        <div class="col-xs-6">
                            <select class="form-control" name="change">
    <?php
    foreach ($int_opts as $key => $val) {
        $sel = '';
        $cval = '';
        if ($picofxOpts['interval'] == $key) {
            $sel = ' selected';
            $seld = 1;
        }
        if ($seld != 1 && $key == 'c') {
            $sel = ' selected';
            $cval = $picofxOpts['interval'];
        }

        echo '<option value="' . $key . '"' . $sel . '>' . $val . '</option>' . "\n";
    }
    ?>
                            </select>
                        </div>
                    </div>
                    <form class="form-horizontal" role="form">
                        <div class="form-group">
                            <label for="eingabefeldEmail3" class="col-sm-2 control-label"></label>
                            <div class="col-sm-10 form-inline">
                                Wechseln alle <input class="form-control" type="text" value="<?php echo $cval ?>" name="cchange" > Tage
                                <br><span class="help-block">Nur ausf&uuml;llen wenn oben "selbstdefiniert" ausgew&auml;hlt ist! Andernfalls wird dieses Feld ignoriert.</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="eingabefeldEmail3" class="col-sm-2 control-label">Thumbnail Breite</label>
                            <div class="col-xs-4">
                                <input type="text" class="form-control" value="<?php echo $picofxOpts['picwidth'] ?>" name="picwidth">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="eingabefeldEmail3" class="col-sm-2 control-label"></label>
                            <div class="col-xs-6">
                                <input class="btn btn-primary" type="submit" value="Speichern">
                            </div>
                        </div>
                </div>
            </div>
        </form>
    </div>

    <?php
} elseif ($um == 'update') {
    $directory = '';
    $change = 'x';
    $picofxPicWidth = '100';
    if (!empty($_POST['picwidth'])) {
        $picofxPicWidth = intval($_POST['picwidth']);
    }
    $directory = intval($_POST['directory']);

    if (isset($_POST['change'])) {
        if ($_POST['change'] == 'c') {
            $change = intval($_POST['cchange']);
        } else {
            $change = intval($_POST['change']);
        }
        $picofxNextChange = date('Y-m-d', time() + 3600 * 24 * $change);
    }

    if (strval($change) == 'x') {
        echo 'c ' . $change;
        echo '<br>d ' . $directory;
        echo '<br>fehler';
    } else {
        if ($directory == 0) {
            $picofxOpts['pic'] = @db_result(db_query("SELECT id FROM prefix_gallery_imgs ORDER BY RAND() LIMIT 1"), 0);
        } else {
            $picofxOpts['pic'] = @db_result(db_query("SELECT id FROM prefix_gallery_imgs WHERE cat = " . $directory . " ORDER BY RAND() LIMIT 1"), 0);
        }
        if (!empty($picofxOpts['pic'])) {
            $picofxOpts['pic'] .= '.' . @db_result(db_query("SELECT endung FROM prefix_gallery_imgs WHERE id = " . $picofxOpts['pic']), 0);
        }

        // geaendertes pic in db speichern
        db_query('UPDATE prefix_allg SET v2 = \'' . $picofxOpts['pic'] . '\' WHERE k = \'picofx\' AND v1 =\'pic\' LIMIT 1');
        db_query('UPDATE prefix_allg SET v2 = \'' . $directory . '\' WHERE k = \'picofx\' AND v1 =\'directory\' LIMIT 1');
        db_query('UPDATE prefix_allg SET v2 = \'' . $change . '\' WHERE k = \'picofx\' AND v1 =\'interval\' LIMIT 1');
        db_query('UPDATE prefix_allg SET v2 = \'' . $picofxNextChange . '\' WHERE k = \'picofx\' AND v1 =\'nextchange\' LIMIT 1');
        db_query('UPDATE prefix_allg SET v2 = \'' . $picofxPicWidth . '\' WHERE k = \'picofx\' AND v1 =\'picwidth\' LIMIT 1');
        wd('admin.php?picofx', 'Pic of X Einstellungen gespeichert');
    }
}

$design->footer();
?>