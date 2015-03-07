<?php 
#   Copyright 	by: Felix Hohlwegler
#   Support: 	www.felix-hohlwegler.de
# 	Version 	1.2
#	Datum:		24.03.2013


defined ('main') or die ( 'no direct access' );
defined ('admin') or die ( 'only admin access' );

$design = new design ( 'Admins Area', 'Admins Area', 2 );
 
# ausloggen
user_logout();
$design->header();
wd('index.php' , $lang['logoutsuccessful'] , 3);
$design->footer();
?>