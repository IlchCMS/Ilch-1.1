<?php

session_name  ('sid');
session_start ();

#header("Content-Type: image/jpeg");


$m = preg_replace("/[^a-z]+/","",$_GET['m']);
$w = intval(preg_replace("/[^0-2]/", "",$_GET['w']));

if (isset($_SESSION['antispam'][$m][$w])) {
  readfile ($_SESSION['antispam'][$m][$w].'.jpg');
  unset($_SESSION['antispam'][$m][$w]);
} else {
  readfile ('=.jpg');
	unset($_SESSION['antispam'][$m]);
}

if (count($_SESSION['antispam'][$m]) == 1) {
  $_SESSION['antispam'][$m] = $_SESSION['antispam'][$m][3];
}

?>