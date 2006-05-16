<script language="JavaScript" type="text/javascript" src="include/includes/js/rte/html2xhtml.js"></script>
<!-- To decrease bandwidth, use richtext_compressed.js instead of richtext.js //-->
<script language="JavaScript" type="text/javascript" src="include/includes/js/rte/richtext.js"></script>
<script language="javascript" type="text/javascript">
function changeAktion () {
  var akl = document.getElementById('akl').value;
  document.location.href="?selfbp=0&akl=" + akl;
}
function confirmDel(){
  var akl = document.getElementById('akl').value;
	var frage = confirm ( "Willst du " + akl.substr(1) + " wirklich löschen?" );
	if ( frage == true ) {
    document.location.href="?selfbp=0&del=" + akl;
  }
}
function submitForm() {
	//make sure hidden and iframe values are in sync before submitting form
	//to sync only 1 rte, use updateRTE(rte)
	//to sync all rtes, use updateRTEs
	updateRTE('text');
	//updateRTEs();
	
	//change the following line to true to submit form
	return true;
}

//Usage: initRTE(imagesPath, includesPath, cssFile, genXHTML)
initRTE("images/", "", "", true);

</script>
<form name="RTEDemo" action="?selfbp" method="POST" onsubmit="return submitForm();">

<table cellpadding="2" cellspacing="1" border="0" class="border">
  <tr>
    <td class="Cmite">Aktion w&auml;hlen</td>
    <td class="Cnorm"><select id="akl" name="akl" onChange="changeAktion()">{akl}</select></td>
  </tr><tr>
    <td class="Cmite">Name</td>
    <td class="Cnorm"><input name="name" size="50" value="{name}" /></td>
  </tr><tr>
    <td colspan="2" class="Cnorm">
<noscript><p><b>Javascript must be enabled to use this form.</b></p></noscript>

<script language="JavaScript" type="text/javascript"><!--
  writeRichText('text', '{text}', 750, 350, true, false);
//--></script>
    
    </td>
  </tr><tr class="Cdark">
    <td></td>
    <td><input type="submit" name="submit" value="Speichern">&nbsp;<input type="button" value="Löschen" onclick="confirmDel()"></td>
  </tr>
</table>
</form>

<br /><br />
<table width="100%" border="0" cellpadding="10" cellspacing="1" class="border">
  <tr>
	  <td class="Cnorm">
		  <b>Hilfe</b><br />
			<ol>
			  <li>Wie binde ich denn so eine eigene Page in das Men&uuml; ein?<br />
				Einfach auf Navigation Klicken und den Namen aus&auml;hlen (er f&auml;ngt mit 'selfp_' an).
				</li>
				<br /><br />
				<li>Und eigene Boxen?<br />
				Auch sehr einfach. Auf Navigation klicken und den Namen aus&auml;hlen (er f&auml;ngt mit 'selfb_' an).
		    </li>
				<br /><br />
				<li><b>Wichtig!</b><br />
				Folgendes bitte beachten. Die Namen m&uuml;ssen unterschiedlich sein!
        Wenn Sie einen gleichen Namen verwenden, wird die schon bestehende Datei
        einfach &uuml;berschrieben!
        <br />
        Ausserdem darf der Name einer neuen Seite nicht schon im include/contents
        Ordner liegen, sonst kann die neue eigene Seite nicht angezeigt werden!
        Bei eigenen Boxen ist dies egal, dort darf der Name im include/boxes
        Ordner nicht schon vorhanden sein.
        <br />
        Bitte im Namen keine Sonderzeichen verwenden. Beschr&auml;nken Sie sich auf
        die Buchstaben A-Z und a-z und auf die Zahlen 0-9 ...
        <br />
        Bitte unbedingt diese drei Punkte beachten, andernfalls kann es zu Datenverlust
        oder sonstigem unerw&uuml;nstem Verhalten kommen.
		    </li>
				<br /><br />
    </td>
    
	</tr> 
</table>	
