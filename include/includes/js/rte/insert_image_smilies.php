<?php

define('main', TRUE);
chdir ('../../../../');

require_once ('include/includes/config.php');
require_once ('include/includes/loader.php');

db_connect();

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>Bild einf&uuml;gen</title>
  <link rel="stylesheet" type="text/css" href="../../../admin/templates/style.css">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
<script language="JavaScript" type="text/javascript">
<!--
function AddImage() {
	var oForm = document.imageForm;
	
	//validate form
	if (oForm.imageText.value == '') {
		alert('Please enter link text.');
		return false;
	}
	
	var html = '<img ';
  if (oForm.linkTarget.value == '_float') {
    html += 'style="float: left; padding: 5px;" '; 
  } else if (oForm.linkTarget.value == 'float_') {
    html += 'style="float: right; padding: 5px;" '; 
  }
  html += 'src="' + document.imageForm.imageText.value + '" />';
	
	window.opener.insertHTML(html);
	window.close();
	return true;
}
function closeThisWindow() {
  opener.focus();
  window.close();
}
function put(url) {
  window.opener.document.imageForm.imageText.value = url;
  window.opener.AddImage();
  closeThisWindow();
}
//-->
</script>
</head>

<body>

<form name="imageForm" onSubmit="return AddImage();">
<table border="0" cellpadding="5" cellspacing="1" class="border">
  <tr>
    <td class="Callg">
<table cellpadding="4" cellspacing="1" border="0" class="border" width="100%">
  <tr class="Chead">
    <td><b>Smilie w&auml;hlen</b></td>
  </tr><tr>
		<td class="Cnorm">
    <?php
  $zeilen = 3; $i = 0;
  $erg = db_query('SELECT * FROM `prefix_smilies`');
	while ($row = db_fetch_object($erg) ) {
    if($i%$zeilen == 0 AND $i <> 0) { echo '<br /><br />'; }
		$url = $row->url;
    $url1 = $row->url;
    if (file_exists($url)) {
      $url = '../../../../'.$url;
    }
    echo '<a href="#" onClick="javascript:put(\''.$url1.'\')">';
    echo '<img style="padding-left: 10px; float: left;" src="'.$url.'" border="0"></a>';
    $i++;
	}
    ?>
    </td>
	</tr>
</table>
    </td>
  </tr>
</table>
</form>

</body>
</html>
