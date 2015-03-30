<?php
//  Copyright by Manuel
//  Support www.ilch.de


defined ('main') or die ( 'no direct access' );


if ( empty($_POST['NEWSLETTER'])  ) {

?>
  <form action="index.php" method="POST">
  <div>
		<input type="text" name="NEWSLETTER" size="15">
		<p></p>
		<input type="submit" value="<?php echo $lang['newsletterinout']; ?>">
		</div>
	</form>
<?php

} else {

	$email = escape ( $_POST['NEWSLETTER'] , 'string' );
	$erg = db_query ("SELECT COUNT(*) FROM prefix_newsletter WHERE email = '".$email."'");
	$anz = db_result($erg,0);
	if ( $anz == 1 ) {
	  db_query("DELETE FROM prefix_newsletter WHERE email = '".$email."'");
		echo '<div class="text-center"><span class="ilch_hinweis_gelb">'.$lang['deletesuccessful'].'<span></div>';
	} else {
	  db_query("INSERT INTO prefix_newsletter (`email`) VALUES ('".$email."')");
		echo '<div class="text-center"><span class="ilch_hinweis_gruen">'.$lang['insertsuccessful'].'<span></div>';
	}
}
?>