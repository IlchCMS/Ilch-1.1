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
function closeThisWindow() {
  opener.focus();
  window.close();
}
function AddImage() {
	var oForm = document.imageForm;
	
	//validate form
	if (oForm.imageText.value == '') {
		alert('Please enter link text.');
		return false;
	}
	
	var html = '<img ';
  if (oForm.imageTarget.value == '_float') {
    html += 'style="float: left; padding: 5px;" '; 
  } else if (oForm.imageTarget.value == 'float_') {
    html += 'style="float: right; padding: 5px;" '; 
  }
  if (oForm.imageTitle.value != '') {
    html += 'title="' + oForm.imageTitle.value + '" ';
  }
  html += 'src="' + document.imageForm.imageText.value + '" />';
	
	window.opener.insertHTML(html);
	closeThisWindow();
	return true;
}
function openSmilies (f,v) {
  var Fenster = window.open ('insert_image_smilies.php', 'openSmilies', 'scrollbars=yes,height=300,width=150,left=500,top=100');
  Fenster.focus();
}
function openGallery (f,v) {
  var Fenster = window.open ('insert_image_gallery.php', 'openGallery', 'scrollbars=yes,height=300,width=500,left=300,top=100');
  Fenster.focus();
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
    <td colspan="2"><b>Bild einf&uuml;gen</b></td>
  </tr><tr class="Cdark">
    <td width="50%" align="center"><a href="javascript:openSmilies()">Smilies</a></td>
    <td width="50%" align="center"><a href="javascript:openGallery()">Gallery</a></td>
  </tr>
</table>
<table cellpadding="4" cellspacing="1" border="0" class="border" width="100%">
	<tr>
		<td class="Cmite">Bild</td>
		<td class="Cnorm"><input name="imageText" type="text" id="imageText" size="40"></td>
	</tr>
	<tr>
		<td class="Cmite">Titel</td>
		<td class="Cnorm"><input name="imageTitle" type="text" id="imageTitle" size="40"></td>
	</tr>
	<tr>
		<td class="Cmite">Option</td>
		<td class="Cnorm">
			<select name="imageTarget" id="imageTarget">
				<option value="_normal">normal</option>
				<option value="_float">Im Textfluss links</option>
        <option value="float_">Im Textfluss rechts</option>
			</select>
		</td>
	</tr>
	<tr class="Cdark">
		<td colspan="2" align="center">
			<input type="submit" value="Absenden" />
			<input type="button" value="Schliesen" onClick="closeThisWindow();" />
		</td>
	</tr>
</table>
    </td>
  </tr>
</table>
</form>

</body>
</html>