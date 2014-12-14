<?php

defined ('main') or die ( 'no direct access' );

function getGroupRights(){
	$ret = 0;
	foreach ($_SESSION['authgrp'] as $id => $bool){
		$ret = $ret | pow(2, $id);
	}
	return $ret;
}

switch($menu->get(1)) {
  	default :            $userDatei = 'news';           break;
	case 'archiv'      : $userDatei = 'archiv';         break;
	case 'print'       : $userDatei = 'print';          break;
	case 'send'        : $userDatei = 'send';	        break;
	case 'add'         : $userDatei = 'add';	        break;
}

 require_once('include/contents/news/'.$userDatei.'.php');

?>