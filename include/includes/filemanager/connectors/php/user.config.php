<?php
/**
 *	Filemanager PHP connector
 *  This file should at least declare auth() function 
 *  and instantiate the Filemanager as '$fm'
 *  
 *  IMPORTANT : by default Read and Write access is granted to everyone
 *  Copy/paste this file to 'user.config.php' file to implement your own auth() function
 *  to grant access to wanted users only
 *
 *	filemanager.php
 *	use for ckeditor filemanager
 *
 *	@license	MIT License
 *  @author		Simon Georget <simon (at) linea21 (dot) com>
 *	@copyright	Authors
 */

session_name('sid');
session_start();
date_default_timezone_set('Europe/Berlin');

/**
 *	Check if user is authorized
 *	
 *
 *	@return boolean true if access granted, false if no access
 */
function auth() {
  return (isset($_SESSION['allowCKUpload']) && $_SESSION['allowCKUpload']);
}


// we instantiate the Filemanager
$fm = new Filemanager();