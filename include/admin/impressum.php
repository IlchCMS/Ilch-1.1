<?php
//   Copyright by: Manuel
//   Support: www.ilch.de


defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Admins Area', 'Admins Area', 2);
$design->header();

if (isset($_POST['sub'])) {
    $t1 = escape($_POST['t1'], 'textarea');
    $v1 = escape($_POST['v1'], 'string');
    $v2 = escape($_POST['v2'], 'string');
    $v3 = escape($_POST['v3'], 'string');
    $v4 = escape($_POST['v4'], 'string');
    db_query("UPDATE prefix_allg SET v1 = '" . $v1 . "', v2 = '" . $v2 . "', v3 = '" . $v3 . "', v4 = '" . $v4 . "', t1 = '" . $t1 . "' WHERE k = 'impressum'");
   wd('admin.php?impressum', 'Impressum wurde ge&auml;ndert', 1); 
}
$erg = db_query("SELECT * FROM prefix_allg WHERE k = 'impressum' LIMIT 1");
$row = db_fetch_assoc($erg);
if ($row['t1'] == '') {
    $f = @implode('', @file('http://disclaimer.de/disclaimer.htm'));
    $f = preg_replace("/.*?<a NAME=\"1\">(.*)<p><b><font size=2>5\..*?/Uis", "<h3><a name=\"1\">\\1<\/p>", $f);
    $f = preg_replace("/<\/?font[^>]*>/is", "", $f);
    $t = $f;
} else {
    $t = $row['t1'];
}
?>


<a class="btn btn-warning btn-sm" href="http://www.ilch.de/texts-s140-hinweise-zum-impressum.html" target="_blank">Info´s zum Impressum</a><br>
<legend><h2>Impressum</h2></legend>
<div class="cont_loose">
    <form action="?impressum" method="POST" class="form-horizontal" role="form">
        <div class="form-group">
            <label></label>
            <div class="col-xs-6">
                <input class="form-control" type="text" name="v1" value="<?php echo $row['v1']; ?>" >
            </div></div>
        <div class="form-group">
            <label></label>
            <div class="col-xs-6">
                <input class="form-control" type="text" name="v2" value="<?php echo $row['v2']; ?>" >
            </div></div>
        <div class="form-group">
            <label></label>
            <div class="col-xs-6">
                <input class="form-control" type="text" name="v3" value="<?php echo $row['v3']; ?>" >
            </div></div>
        <div class="form-group">
            <label></label>
            <div class="col-xs-6">
                <input class="form-control" type="text" name="v4" value="<?php echo $row['v4']; ?>" >
            </div></div>
        <div class="form-group">
            <label></label>
            <div class="col-sm-10">
                <textarea class="form-control" rows="10" name="t1"><?php echo unescape($t); ?></textarea>
                <span class="help-block text-right"><small>HTML-Code im Text m&ouml;glich.</small></span>
            </div></div>
        <div class="form-group">
            <label></label>
            <div class="col-sm-10">
                <input type="submit" class="btn btn-primary" name="sub" value="Absenden" >
            </div></div>
    </form>
</div>

<?php
$design->footer();
?>