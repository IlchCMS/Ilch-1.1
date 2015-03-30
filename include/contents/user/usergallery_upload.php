<?php
#   Usergallery Upload
#   Support: www.ilch.de

defined ('main') or die ( 'no direct access' );

$uid = $_SESSION['authid'];
$errormsg = '';

# bild hochladen
if (!empty($_FILES['file']['name']) 
    AND is_writeable('include/images/usergallery') 
	AND loggedin() 
	AND $uid == $_SESSION['authid'] 
	AND substr(ic_mime_type($_FILES['file']['tmp_name']), 0, 6 ) == 'image/') {
	require_once('include/includes/func/gallery.php');
	$size = @getimagesize($_FILES['file']['tmp_name']);
	$fileinfo = pathinfo($_FILES['file']['name']);
	$fende = strtolower($fileinfo['extension']);
	if (!empty($_FILES['file']['name']) 
	    AND $size[0] > 10
		AND $size[1] > 10 
		AND ($size[2] == 2 OR $size[2] == 3 OR $size[2] == 1) 
		AND ($fende == 'gif' OR $fende == 'jpg' OR $fende == 'jpeg' OR $fende == 'png')) {
		$name = $_FILES['file']['name'];
		$tmp = explode('.', $name);
		$tm1 = count($tmp) -1;
		$endung = escape($tmp[$tm1], 'string');
		unset($tmp[$tm1]);
		$name = escape(implode('', $tmp), 'string');
		$besch = escape($_POST['text'], 'string');
		$id = db_result(db_query("SHOW TABLE STATUS FROM `" . DBDATE . "` LIKE 'prefix_usergallery'"), 0, 'Auto_increment');
		$bild_url = 'include/images/usergallery/img_' . $id . '.' . $endung;
		if (@move_uploaded_file ($_FILES['file']['tmp_name'], $bild_url)) {
			@chmod($bild_url, 0777);
			db_query("INSERT INTO `prefix_usergallery` (`uid`, `name`, `endung`, `besch`) VALUES (" . $uid . ", '" . $name . "','" . $endung . "','" . $besch . "')");
			$bild_thumb = 'include/images/usergallery/img_thumb_' . $id . '.' . $endung;
			create_thumb($bild_url, $bild_thumb, $allgAr['gallery_preview_width']);
			@chmod($bild_thumb, 0777);
			$page = 'include/images/usergallery';
			$server = 'http://' . $_SERVER['HTTP_HOST'] . str_replace('index.php', '', $_SERVER['PHP_SELF']);
			echo '<!DOCTYPE html>
			<html>
			<head>
			    <meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
			    <title>Upload in die Usergallery</title>
				<link rel="stylesheet" type="text/css" href="include/includes/css/ilch_default.css">
			    <script language="javascript" type="text/javascript">
			    <!--
			    var bbcode = new Array(\'[img]' . $server.$bild_thumb . '[/img]\', 
				                       \'[img]' . $server.$bild_url . '[/img]\',
									   \'[url=' . $server.$bild_url . '][img]' . $server.$bild_thumb . '[/img][/url]\');
			    function insert_bbcode (codeid) { opener.put(bbcode[codeid]); opener.focus(); window.close(); }
			    //-->
			    </script>
			</head>
			<body>
					<fieldset>
					<legend><strong>Das Bild wurde erfolgreich hochgeladen</strong></legend>
						<div class="text-center ilchusergalleryupload">
							<img src="' . $server . $bild_thumb . '" alt="thumb"><br>
							Einf&uuml;gen des Bildes in Form von:<br>
							<a href="javascript:insert_bbcode(0);"><b>verkleinertes Vorschaubild</b></a> | <a href="javascript:insert_bbcode(1);"><b>originale Bildgr&ouml;&szlig;e</b></a><br>
							<a href="javascript:insert_bbcode(2);"><b>Vorschaubild mit Link zum original Bild</b></a>
						</div>
					</fieldset>
			</body>
			</html>';
		} else {
			$errormsg = 'Es sind Fehler beim Upload aufgetreten!';
		}
	} else {
		$errormsg = 'Das Bild entspricht nicht den Vorgaben der Usergallery';
	}
}

# bild hochladen
$writable = is_writeable('include/images/usergallery');
if ($writable AND (empty($_FILES['file']['name']) OR !empty($errormsg))) {
	$errormsg = !empty($errormsg) ? '<div class="text-center"><span class="ilch_hinweis_rot"><b>Es ist ein Fehler aufgetreten:<b><br> ' . $errormsg . '</span></div>' : '';
	echo '<!DOCTYPE html>
	<html>
	<head>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<title>Upload in die Usergallery</title>
		<link rel="stylesheet" type="text/css" href="include/includes/css/ilch_default.css">
		<script language="JavaScript" type="text/javascript">
		<!--
		function upload_check() {
			document.form.submit.disabled = true;
			document.form.submit.value = \'Bild wird geladen ...\';
			document.form.submit.style.backgroundColor = \'#FF0000\';
			return true;
		}
		//-->
		</script>
	</head>
	<body>
		<form name="form" onSubmit="return upload_check()" class="form" action="index.php?user-usergallery_upload" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="gesendet" value="yes">
			<fieldset>
			<legend><strong>Ein Bild in die Usergallery hochladen und einf&uuml;gen</strong></legend>
			<p>' . $errormsg . '</p>
			<label class="ilch_float_l label_100">Bildauswahl</label><input type="file" name="file"><br>
			<label class="ilch_float_l label_100">Beschreibung</label><input class="tdweight50" name="text" maxlength="255"><br><br>
			<label class="ilch_float_l label_100"></label><input type="submit" value="Absenden" name="submit">
				<br><br>
				<div class="text-center"><span class="ilch_hinweis_gelb">Bitte nur einmal auf Absenden klicken, der Upload dauert kurz!<br>
				Information &uuml;ber den Status folgt automatisch!</span></div>
				<br>
				<div class="text-center"><a href="javascript:window.close(\'usergalleryupl\');">Fenster schlie&szlig;en</a></div>
			</fieldset>
		</form>
    </body>
    </html>';
} elseif (!$writable) {
	echo '<div class="text-center"><span class="ilch_hinweis_gelb">Die Usergalerie ist nicht funktionst&uuml;chtig!</span></div>';
}
?>