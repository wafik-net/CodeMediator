<?php

/* 
 *---------------------------------------------------------------
 * CodeMediator - A PHP Web Framework Based On MVC
 *---------------------------------------------------------------
 *
 * This Is The Index Of The Project
 * Do Not Edit Or Remove Any Code In This page
 *
 * Make Sure To Setup The Server Connection First
 * The Setup Go To : application/config/database.php
 *
 * Version 1.0
 *
 *---------------------------------------------------------------
 * Include Config & Autoload
 *
 */

defined('DS') OR define('DS', DIRECTORY_SEPARATOR);

/* 
 *---------------------------------------------------------------
 * Check .htaccess
 *---------------------------------------------------------------
 *
 */

if(!file_exists(".htaccess")):
	$msg = "File [.htaccess] does not exist on root directory, you have to upload this file to run your application <br>";
	$msg .= "If you cannot see this file, you can create new one named \".htaccess\" in the root directory of your web application, and write this lines one by one <br>";
	$msg .= "<pre style='color:#444'>";
	$msg .= "RewriteEngine On <br>";
	$msg .= "RewriteCond %{REQUEST_FILENAME} !-f <br>";
	$msg .= "RewriteCond %{REQUEST_FILENAME} !-d <br>";
	$msg .= "RewriteRule ^(.*)$ index.php/$1 [L] <br>";
	$msg .= "Options -indexes <br>";
	$msg .= "</pre>";
	die($msg);
endif;






require_once 'application'.DS.'core'.DS.'config.php';
require_once CORE.DS.'autoload.php';

new App();


