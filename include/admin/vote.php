<?php

// Copyright by: Manuel
// Support: www.ilch.de
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Admins Area', 'Admins Area', 2);
$design->header();

$_GET['del'] = escape($_GET['del'], 'integer');
$_GET['ak'] = escape($_GET['ak'], 'integer');
$_GET['id'] = escape($_GET['id'], 'integer');

function showVote($id) {
    $maxRow = db_fetch_object(db_query('SELECT MAX(res) as res FROM `prefix_poll_res` WHERE poll_id = "' . $id . '"'));
    $gesRow = db_fetch_object(db_query('SELECT SUM(res) as res FROM `prefix_poll_res` WHERE poll_id = "' . $id . '"'));
    $max = $maxRow->res;
    $ges = $gesRow->res;
    $erg = db_query('SELECT antw, res FROM `prefix_poll_res` WHERE poll_id = "' . $id . '" ORDER BY sort');
    while ($row = db_fetch_object($erg)) {
        if (!empty($row->res)) {
            $weite = ($row->res / $max) * 200;
            $prozent = $row->res * 100 / $ges;
            $prozent = round($prozent, 0);
        } else {
            $weite = 0;
            $prozent = 0;
        }
        echo '<tr><td width="50%">' . $row->antw . '</td>';
        echo '<td width="40%"><div class="progress">
  <div class="progress-bar" role="progressbar" aria-valuenow="' . $prozent . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $prozent . '%;min-width: 20px;">
    ' . $prozent . '%
  </div>
</div></td>';
        echo '<td width="10%" align="right">' . $row->res . '</td></tr>';
    }
    echo '<tr><td colspan="3" align="right">Gesamt: &nbsp; ' . $ges . '</td></tr>';
}

function getPollRecht($akt) {
    $liste = '';
    $ar = array(1 => 'alle', 2 => 'registrierte');
    foreach ($ar as $k => $v) {
        if ($akt == $k) {
            $sel = ' selected';
        } else {
            $sel = '';
        }
        $liste .= '<option' . $sel . ' value="' . $k . '">' . $v . '</option>';
    }
    return ($liste);
}

$um = $menu->get(1);
if ($menu->get(1) == 'del') {
    db_query('DELETE FROM `prefix_poll` WHERE poll_id = "' . $_GET['del'] . '"');
    db_query('DELETE FROM `prefix_poll_res` WHERE poll_id = "' . $_GET['del'] . '"');
}
if ($menu->get(1) == 5) {
    db_query('UPDATE `prefix_poll` SET stat = "' . $_GET['ak'] . '" WHERE poll_id = "' . $_GET['id'] . '"');
}
// A L L E   V O T E S   W E R D E N   A N G E Z E I G T
if (isset($_POST['sub'])) {
    $_POST['frage'] = escape($_POST['frage'], 'string');
    $_POST['poll_recht'] = escape($_POST['poll_recht'], 'integer');
    $_POST['vid'] = escape($_POST['vid'], 'integer');
    if (empty($_POST['vid'])) {
        db_query('INSERT INTO `prefix_poll` (`frage`,`recht`,`stat`,`text`) VALUES ( "' . $_POST['frage'] . '" , "' . $_POST['poll_recht'] . '" , "1" ,"") ');
        $poll_id = db_last_id();
        $i = 1;
        foreach ($_POST['antw'] as $v) {
            if (!empty($v)) {
                $v = escape($v, 'string');
                db_query('INSERT INTO `prefix_poll_res` (`sort`,`poll_id`,`antw`,`res`) VALUES ( "' . $i . '" , "' . $poll_id . '" , "' . $v . '" , "" ) ');
                $i++;
            }
        }
    } else {
        db_query('UPDATE `prefix_poll` SET frage = "' . $_POST['frage'] . '", recht = "' . $_POST['poll_recht'] . '" WHERE poll_id = "' . $_POST['vid'] . '"');
        $i = 1;
        foreach ($_POST['antw'] as $k => $v) {
            $a = db_count_query("SELECT COUNT(*) FROM prefix_poll_res WHERE poll_id = " . $_POST['vid'] . " AND sort = " . $k);
            $v = escape($v, 'string');
            if ($a == 0 AND $v != '') {
                db_query("INSERT INTO `prefix_poll_res` (`sort`,`poll_id`,`antw`,`res`) VALUES ( '" . $i . "' , '" . $_POST['vid'] . "' , '" . $v . "' , '' )");
                $i++;
            } elseif ($a == 1 AND $v == '') {
                db_query("DELETE FROM `prefix_poll_res` WHERE poll_id = " . $_POST['vid'] . " AND sort = " . $k);
            } elseif ($a == 1 AND $v != '') {
                db_query("UPDATE `prefix_poll_res` SET antw = '" . $v . "', sort = " . $i . " WHERE poll_id = " . $_POST['vid'] . " AND sort = " . $k);
                $i++;
            }
        }
    }
}
if (empty($_POST['add'])) {
    if (isset($_GET['vid'])) {
        $row1 = db_fetch_object(db_query('SELECT frage, recht FROM `prefix_poll` WHERE poll_id = "' . $_GET['vid'] . '"'));
        $_POST['frage'] = $row1->frage;
        $_POST['poll_recht'] = $row1->recht;
        $_POST['antw'] = array();
        $erg2 = db_query('SELECT sort,antw FROM `prefix_poll_res` WHERE poll_id = "' . $_GET['vid'] . '" ORDER BY sort');
        while ($row2 = db_fetch_object($erg2)) {
            $_POST['antw'][$row2->sort] = $row2->antw;
        }
        $_POST['vid'] = $_GET['vid'];
    } else {
        $_POST['frage'] = '';
        $_POST['antw'] = array(1 => '');
        $_POST['poll_recht'] = '';
        $_POST['vid'] = '';
    }
}
$anzFeld = count($_POST['antw']);
if (isset($_POST['add'])) {
    $anzFeld++;
    $_POST['antw'][] = '';
}

echo '<form action="admin.php?vote" method="POST" class="form-horizontal" role="form">';
echo '<input type="hidden" name="vid" value="' . $_POST['vid'] . '" >';
echo '<legend><h2><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span> Umfrage</h2></legend>';
echo '<div class="form-group">
    <label class="col-sm-2 control-label">Frage</label>
    <div class="col-xs-6">
      <input type="text" class="form-control" value="' . $_POST['frage'] . '" name="frage">
    </div>
  </div>';
echo '<div class="form-group">
    <label class="col-sm-2 control-label">F&uuml;r</label>
    <div class="col-xs-4">
      <select class="form-control" name="poll_recht">' . getPollRecht($_POST['poll_recht']) . '</select>
    </div>
  </div>';
for ($i = 1; $i <= $anzFeld; $i++) {
    echo '<div class="form-group">
    <label class="col-sm-2 control-label">Antwort ' . $i . '</label>
    <div class="col-xs-6">
      <input type="text" class="form-control" value="' . $_POST['antw'][$i] . '" name="antw[' . $i . ']">
    </div>
  </div>';
    if ($i == $anzFeld) {
        echo '<div class="form-group">
    <label class="col-sm-2 control-label"></label>
    <div class="col-xs-6">
      <input class="btn btn-success btn-sm" type="submit" name="add" value="Antwort hinzuf&uuml;gen">
    </div>
  </div>';
    }
    echo '' . "\n";
}
echo '<div class="form-group">
    <label class="col-sm-2 control-label"></label>
    <div class="col-xs-6">
      <input class="btn btn-primary" name="sub" type="submit" value="' . $lang['formsub'] . '">
    </div>
  </div>';
echo '</form>';
echo '<table class="table table-bordered">';
echo '<tr class="active"><td><strong>Vote verwalten</strong></td></tr>';
?>
<script language="JavaScript" type="text/javascript">
<!--

    function delcheck(DELID) {
        var frage = confirm("Willst du diesen Eintrag wirklich löschen?");
        if (frage == true) {
            document.location.href = "?vote-del&del=" + DELID;
        }
    }
    //-->
</script>
<?php

$abf = 'SELECT * FROM `prefix_poll` ORDER BY poll_id DESC';
$erg = db_query($abf);
$class = '';
while ($row = db_fetch_object($erg)) {
    if ($row->stat == 1) {
        $coo = 'schlie&szlig;en';
        $up = 0;
    } else {
        $coo = '&ouml;ffnen';
        $up = 1;
    }
    if ($class == '') {
        $class = '';
    } else {
        $class = '';
    }
    echo '<tr>';
    echo '<td><strong>' . $row->frage . '</strong><br><div class="btn-group btn-group-sm"><a  class="btn btn-danger" href="javascript:delcheck(' . $row->poll_id . ')">l&ouml;schen</a>
<a class="btn btn-primary" href="?vote=0&vid=' . $row->poll_id . '">&auml;ndern</a>
<a class="btn btn-warning" href="?vote-5=0&ak=' . $up . '&id=' . $row->poll_id . '">' . $coo . '</a>
<a class="btn btn-info" href="?vote=0&showVote=' . $row->poll_id . '">zeigen</a></div></td>';
    echo '</tr>';
    if (isset($_GET['showVote']) AND $_GET['showVote'] == $row->poll_id) {
        echo '<tr class="' . $class . '"><td>';
        echo '<table class="table table-striped" width="100%"  align="right">';
        showVote($row->poll_id);
        echo '</table></td></tr>';
    }
}
echo '</table>';

$design->footer();
?>