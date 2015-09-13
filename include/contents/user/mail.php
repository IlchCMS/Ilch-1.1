<?php 
#   Copyright by: Manuel
#   Support: www.ilch.de


defined ('main') or die ( 'no direct access' );

$abf = "SELECT * FROM prefix_user WHERE id = ".$menu->get(2);
$erg = db_query($abf);
$DA_IS_WAS_FAUL = FALSE;
if ( @db_num_rows($erg) <> 1 ) {
  $DA_IS_WAS_FAUL = TRUE;
}
$row = db_fetch_assoc($erg);
if ( $row['opt_mail'] == 0 ) {
  $DA_IS_WAS_FAUL = TRUE;
}
if ( $DA_IS_WAS_FAUL === TRUE ) {
  header ( 'location: index.php?'.$allAr['smodul'] );
  exit();
}

$title = $allgAr['title'].' :: Users :: eMail '.$lang['touser'].' '.$row['name'];
$hmenu  = $extented_forum_menu.'<a class="smalfont" href="?user">Users</a><b> &raquo; </b> eMail '.$lang['touser'].' '.$row['name'].$extented_forum_menu_sufix;
$design = new design ( $title , $hmenu, 1);
$design->header();


if ( ! array_key_exists('klicktime',$_SESSION) ) { 
  $_SESSION['klicktime'] = ''; 
}

# vars definieren
$_POST['email'] = ( isset($_POST['email']) ? trim($_POST['email']) : '' );
$_POST['bet'] = ( isset($_POST['bet']) ? trim($_POST['bet']) : '' );
$_POST['txt'] = ( isset($_POST['txt']) ? trim($_POST['txt']) : '' );

if ( empty($_POST['bet']) OR empty($_POST['email']) OR empty($_POST['txt']) OR $_SESSION['klicktime'] > (time() - 60) ) {
  
	if ( !empty($_POST['send']) ) {
	  $fehler = '';
		if ( $_SESSION['klicktime'] > (time() - 60) ) {
		  $fehler .= '<div class="text-center"><span class="ilch_hinweis_rot">'.$lang['Pleasenotwemails'].'</span></div>';
		}
		if ( trim($_POST['bet']) == '' ) {
		  $fehler .= '<div class="text-center"><span class="ilch_hinweis_rot">'.$lang['pleasespecify'].'</span></div>';
		}
    if ( trim($_POST['email']) == '' ) {
		  $fehler .= '<div class="text-center"><span class="ilch_hinweis_rot">'.$lang['nomailadress'].'</span></div>';
		}
		if ( trim($_POST['txt']) == '' ) {
		  $fehler .= '<div class="text-center"><span class="ilch_hinweis_rot">'.$lang['nomailmessage'].'</span></div>';
		}
	} else {
	  $fehler = '';
	}
	echo $fehler;
  
  ?>
	<form action="index.php?user-mail-<?php echo $menu->get(2) ?>" method="POST">
      <fieldset>
        <legend>eMail <?php echo $lang['touser']; ?> <strong><?php echo $row['name']; ?></strong></legend>
        <label class="ilch_float_l label_80"><?php echo $lang['reference']; ?></label><input type="text" name="bet" value="<?php echo $_POST['bet']; ?>"><br>
        <label class="ilch_float_l label_80"><?php echo $lang['youemail']; ?></label><input type="text" name="email" value="<?php echo $_POST['email']; ?>"><br>
        <label class="ilch_float_l label_80"><?php echo $lang['message']; ?></label><textarea cols="40" rows="10" name="txt"><?php echo $_POST['txt']; ?></textarea><br>
        <label class="ilch_float_l label_80"></label><input type="submit" name="send" value="<?php echo $lang['formsub']; ?>">
    </fieldset>
  </form>
  <?php
} else {
  $_SESSION['klicktime'] = time();
	if ( 1 == $row['opt_mail'] ) {
    icmail ($row['email'],strip_tags($_POST['bet']),strip_tags($_POST['txt']),'SeitenKontakt <'.escape_for_email($_POST['email']).'>');
	  wd ('index.php?forum',$lang['emailsuccessfullsend']);
	} else {
    header ( 'location: index.php?'.$allAr['smodul'] );
    exit();
  }
}


$design->footer();

?>