<?php
define('main', TRUE);

chdir ('../../../../');

require_once ('include/includes/config.php');
require_once ('include/includes/loader.php');
db_connect();

$cat = 0;
if (isset($_REQUEST['cat'])) {
  $cat = $_REQUEST['cat'];
}
function gallery_admin_showcats ( $id , $stufe ) {
  $q = "SELECT * FROM prefix_gallery_cats WHERE cat = ".$id." ORDER BY pos";
	$erg = db_query($q);
	if ( db_num_rows($erg) > 0 ) {
 	  while ($row = db_fetch_object($erg) ) {
	    echo '<tr class="Cmite"><td>'.$stufe.'- <a href="?cat='.$row->id.'">'.$row->name.'</a></td></tr>';
		  gallery_admin_showcats($row->id, $stufe.' &nbsp;' );
	  }
	}
}

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
    <td colspan="2"><b>Gallery-Bild w&auml;hlen</b></td>
  </tr><tr>
		<td class="Cmite" valign="top">
    <table cellpadding="0" cellspacing="0" border="0">
    <tr class="Cmite"><td>- <a href="?cat=0">Keine</a></td></tr>
    <?php gallery_admin_showcats(0,''); ?>
    </table>
    </td>
    <td class="Cnorm" valign="top">
    <table cellpadding="2" cellspacing="1" border="0" class="border">
    <?php
    $abf = "SELECT id,besch,datei_name,endung FROM prefix_gallery_imgs WHERE cat = ".$cat;
    $erg = db_query($abf);
    $i = 0;
    while ($row = db_fetch_assoc($erg) ) {
      if ( $i <> 0 AND ($i % $allgAr['gallery_imgs_per_line'] ) == 0 ) { echo '</tr><tr>'; }
      $toput = 'include/images/gallery/img_thumb_'.$row['id'].'.'.$row['endung'];
      $pfad = '../../../../'.$toput;
      echo '<td class="Cnorm" valign="top"><a href="javascript:put(\''.$toput.'\')"><img src="'.$pfad.'" border="0" /></a></td>';
      $i++;
    }
    
    ?>
    </table>
    </td>
	</tr>
</table>
    </td>
  </tr>
</table>
</form>

</body>
</html>
