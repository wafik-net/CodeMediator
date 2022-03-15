<?php

/*
 *-------------------------------------------------------------------
 * CodeMediator -  Config
 *-------------------------------------------------------------------
 *
 * Do Not Edit Or Remove Anything From This File 
 * This File Has To Be Included In Index Of MVC Before Anything
 * 
 *
 */

defined('DS') OR define('DS', DIRECTORY_SEPARATOR); 
# storing errors
$system['error'] = null;



/*
 *-------------------------------------------------------------------
 * Import Constants
 *-------------------------------------------------------------------
 *
 */

require_once dirname(dirname( __FILE__ )).DS.'Core'.DS.'Constants.php';

/*
 *-------------------------------------------------------------------
 * Import Functions
 *-------------------------------------------------------------------
 *
 */

require_once CORE.DS.'Functions.php';

/*
 *-------------------------------------------------------------------
 * Import User Config Files
 *-------------------------------------------------------------------
 *
 */

# Include Config/app 
if(file_exists(CONFIG.DS.'app.php')):
	include_once CONFIG.DS.'app.php';
else:
	$system['error'] .= '<li>File config/app.php is not exists !</li>';
endif;

# Include Config/database 
if(file_exists(CONFIG.DS.'database.php')):
	include_once CONFIG.DS.'database.php';
else:
	$system['error'] .= '<li>File config/database.php is not exists !</li>';
endif;

# Include Config/routes 
if(file_exists(CONFIG.DS.'routes.php')):
	include_once CONFIG.DS.'routes.php';
else:
	$system['error'] .= '<li>File config/routes.php is not exists !</li>';
endif;

# Include Config/constants 
if(file_exists(CONFIG.DS.'constants.php')):
	include_once CONFIG.DS.'constants.php';
else:
	$system['error'] .= '<li>File config/constants.php is not exists !</li>';
endif;

/*
 *-------------------------------------------------------------------
 * Sessions Configuration
 *-------------------------------------------------------------------
 *
 */

# Custom Session Directory ----------------------------------------
if(isset($config['sess-dir']) AND $config['sess-dir'] != null):
	if(!is_dir(APP.DS.$config['sess-dir'])):
		mkdir(APP.DS.$config['sess-dir']);
	endif;
	if(is_dir(APP.DS.$config['sess-dir'])):
		if(session_save_path() != APP.DS.$config['sess-dir']):
		    @session_save_path(APP.DS.$config['sess-dir']);
		endif;
	endif;
endif;

# Rename Session Name --------------------------------------------
if(isset($config['sess-name']) AND $config['sess-name'] != null):
    @session_name($config['sess-name']);
else:
	@session_name('cm-sess');
endif;

# Prevent js to handle sess-cookie --------------------------------
if(isset($config['http-only']) AND $config['http-only'] == true):
  @ini_set('session.cookie_httponly', true);
endif;

# Prevent Session Hijacking --------------------------------------
if(isset($config['prev-sess-hi']) AND $config['prev-sess-hi'] == true):
    prevent_sess_hijacking();
endif;

# Clear Empty Session Files --------------------------------------
if(isset($config['sess-dir']) AND $config['sess-dir'] != null):
	$path = APP.DS.$config['sess-dir'];
	foreach (scandir($path) as $file):
		if(is_file($path.DS.$file)):
			if(filesize($path.DS.$file) == 0):
		        @unlink($path.DS.$file);
		    endif;
		endif;
	endforeach;
endif;

# Start Session By Default ------------------------------------------
if(isset($config['sess-start']) AND $config['sess-start'] == true):
	@session_start(); 
endif;

# Errors Reporting
$config['errors'] == false ? error_reporting(0) : null;


