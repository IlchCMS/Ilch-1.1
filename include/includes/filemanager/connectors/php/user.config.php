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
  return (isset($_SESSION['ic_CKEditor']['allowUpload']) && $_SESSION['ic_CKEditor']['allowUpload']);
}

//set absolute or relative baseUrl if necessary
$config = array('options' => array());
if (isset($_SESSION['ic_CKEditor']['baseUrl'])) {
  $fileManagerConfig = json_decode(file_get_contents(__DIR__ . '/../../scripts/filemanager.config.js'), true);

  $config['options']['baseUrl'] = $_SESSION['ic_CKEditor']['baseUrl'] . $fileManagerConfig['options']['relPath'];
}

// we instantiate the Filemanager
$fm = new Filemanager($config);