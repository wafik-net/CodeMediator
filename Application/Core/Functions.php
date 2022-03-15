<?php 

/*
 *---------------------------------------------------------------
 * MVC Functions
 *---------------------------------------------------------------
 *
 * Do Not Edit Or Remove Anything From This File 
 * This File Must Be Included In Core->Config File
 *
 */

function getHttp()
{

	return isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'].'://' : 'http://';
}

// Base URL
function base_url($sublink = null)
{
	global $config;
	$base = null;
	$http = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'].'://' : 'http://';
	/*---------------- If base_url not set ----------------------*/
	if(empty($config['base_url']) OR $config['base_url'] == ''):
		//IF Localhost
		if(isset($_SERVER['HTTP_HOST']) AND $_SERVER['HTTP_HOST'] == 'localhost'):
            /*
             * @Lets say APP Folder is (MyApp)
             * IF the app folder exists in sub folders (ex: /sub/projects/MyApp)
             * And the current Link is (ex: http://localhost/sub/projects/myapp/user/login)
             * Then we have to set the HomeLink to (http://localhost/sub/projects/myapp)
             *
             * [1] Get Appname          : (ex: MyApp)
             * [2] Get current URI      : sub/projects/myapp/user/login
             * [3] Cut the URI to parts : 
             * [4] Generate home link   : loop on each part and link them together until (myapp)
             * [5] Add http://localhost to homelink
             */ 
			$appname = strtolower(basename(BASE)); 
            $uri = trim($_SERVER['REQUEST_URI'],'/'); 
            $home = null;
            foreach (explode('/', $uri) as $item):
                if($item != $appname):
                    $home .= $item.'/';
                else:
                	$home .= $appname;
                    break;
                endif;
            endforeach;
            $base = $http.'localhost/'.$home;
		else:
			// IF Not Localhost
		    $base = $http.$_SERVER['HTTP_HOST'];
		endif;
	else:
		// remove any 'http://' or '/' 
		$base = str_replace('https://', '', $config['base_url']);
		$base = str_replace('http://', '', $base);
		$base = rtrim($base, '/');
		$base = $http.$base;
	endif;
	if($sublink != null):
		// remove any before slash /link
		$sublink = ltrim($sublink, '/');
		$base .= '/'.$sublink;
	endif; 
	return $base;
	
}

// Return Current Page Request
function page_url($request = null)
{
	if($request == null):
	    return getHttp().$_SERVER['HTTP_HOST'].'/'.ltrim($_SERVER['REQUEST_URI'],'/');
	else:
		return getHttp().$_SERVER['HTTP_HOST'].'/'.ltrim($_SERVER['REQUEST_URI'],'/').'/'.ltrim($request, '/');
	endif;
}
// Get assets folder
function assets($sub = null, $cache = false){
	if(is_dir(BASE.DS.'assets')):
		if($cache == true):
			return base_url('/assets/'.$sub).'?ver='.microtime();
		endif;
		return base_url('/assets/'.$sub);	
	else:
		return 'directory [assets] not set';
	endif;
}
// Get uploads folder
function uploads($sub = null){
	if(is_dir(BASE.DS.'uploads')):
		return base_url('uploads/'.$sub);
	else:
		return 'directory [uploads] not set';
	endif;
	
}

// Redirect 
function redirect($page = '')
{
	header('location:' .$page);
}

// Show Custom Msg
function show_msg($msg, $type = null)
{
	$success = '<div style="background: #fdfdfd;color:#444;padding: 10px;margin: 10px ;
	                box-shadow: 0px 2px 2px 0px #79797945;border-left: 3px solid #29a62e;max-width: 700px">
	            <b style="color:#4caf50">Message :</b> '.$msg.'</div>';
	$warning = '<div style="background: #fdfdfd;color:#444;padding: 10px;margin: 10px;
	                box-shadow: 0px 2px 2px 0px #79797945;border-left: 3px solid #ffa107;max-width: 700px">
	            <b style="color:#ffa107">Message :</b> '.$msg.'</div>';
	$danger = '<div style="background: #fdfdfd;color:#444;padding: 10px;margin: 10px ;
	                box-shadow: 0px 2px 2px 0px #79797945;border-left: 3px solid #dc3545;max-width: 700px">
	            <b style="color: #dc3545">Message :</b> '.$msg.'</div>';
	$default = '<div style="background: #fdfdfd;color:#444;padding: 10px;margin: 10px ;
	                box-shadow: 0px 2px 2px 0px #79797945;border-left: 3px solid #219df3;max-width: 700px">
	            <b style="color: #219df3">Message :</b> '.$msg.'</div>';

	if($type == SUCCESS){return $success;}elseif($type == WARNING){return $warning;}
	elseif($type == DANGER || $type == ERROR){return $danger;}else{return $default;}

}
function page404($echo = false)
{
	$page = "
	<!DOCTYPE html><html><head><title>404</title><style type='text/css'>.p404{margin: 0px;padding: 0px;box-sizing: border-box;font-family: jozoor,janna, sans-serif;outline: none;scroll-behavior: smooth;position: relative;display: flex;width: 100%;height: 100%;justify-content: center;align-items: center;background: #fff!important;}.box404{background: #fff;position: absolute;width: 100%;height: 100%;z-index: 9999;}.box404 .content404{display: flex;justify-content: center;align-items: center;flex-direction: column;font-family: sans-serif;width: 100%;background: #fff;height: 50vh;}.box404 label{color: #444;letter-spacing: 1px;font-size: xx-large;font-weight: bold;margin-bottom: -5px;}</style></head><body class='p404'><div class='box404'><div class='content404'><label>Oops 404</label><p>The page you looking for is not exist </p></div></div></body></html> ";
		if($echo == true):echo $page;else:return $page;endif;
}
// Include Layout
function layout($layout)
{	
    $file =  trim(VIEWS.DS.'layouts'.DS.$layout, '.php').'.php';
    if(file_exists($file)):
        include_once $file;
    else:
        echo show_msg("layout $layout Not Found !", ERROR);
    endif;
}

// upercase and lowercase shurtcut
function upper($str = null)
{
	return strtoupper($str);
}
function lower($str = null)
{
	return strtolower($str);
}

// Capitilize Specific Letter
function capitalize($str, $letter = null)
{
	// Remove White Space
	$str = ltrim($str, ' ');
	// Convert To Array
	$arr = str_split($str);
	// Capitalize letter
	if($letter == null):
		$arr[0] = strtoupper($arr[0]);
	else:
		$arr[$letter-1] = strtoupper($arr[$letter-1]);
	endif;
	// Convert To String
	return implode($arr);
}
// Date Formate | String To Date
function parse_date($str_time)
{
	return date("M j, Y", strtotime($str_time));
}
// Set Cookie
function cookie($name, $value)
{
	setcookie($name, $value, time() + (86400 * 30), "/"); //30 days
}



/*-----------------------------------------------------------------
 * Data Validation | Sanitize Functions |  PHP 5.2.0+
 *------------------------------------------------------------------
 * Validate : Check If Data [?] Or Not => return True or False
 * Sanitize : Filter Data From [?] And Return It 
 *
 * Validate Func - Uses With If Condition
 *               - if(_is_int($int)): 'its int' ? 'its not';
 * Sanitize Func - Output The New Data After Sanitizing
 *               - echo _filter_str($string);
 *
 */

// Validate Email
function is_email($data)
{
	if(!filter_var($data, FILTER_VALIDATE_EMAIL) === false)
	{
		return true;
	}
}

// Validate IP Address
function is_IP($data)
{
	if(!filter_var($data, FILTER_VALIDATE_IP) === false)
	{
		return true;
	}
}
// Validate URL
function is_URL($data)
{
	if(!filter_var($data, FILTER_VALIDATE_URL) === false)
	{
		return true;
	}
}

// Sanitize String - remove tags
function filter_str($data)
{
	$newData = filter_var($data, FILTER_SANITIZE_STRING);
	
		return $newData;
	
}
// Sanitize URL : Remove Illegal Characters
function filter_url($data)
{
	$newData = filter_var($data, FILTER_SANITIZE_URL);
	
		return $newData;
	
}
// Sanitize Magic Quotes : ' and \ 
function filter_quotes($data)
{
	$newData = filter_var($data, FILTER_SANITIZE_MAGIC_QUOTES);
	    return $newData;
	
}

// Sanitize html charachters : <tag> prevent execute html tags
function filter_html($data)
{
	$newData = filter_var($data, FILTER_SANITIZE_SPECIAL_CHARS);
	
		return $newData;
	
}

// prevent execute html tags
function html_encode($data)
{ 
	return htmlspecialchars($data);
}
// decode html tags
function html_decode($data)
{
	return htmlspecialchars_decode($data);
}
/*------------------------------------------------
| Arrays Functions                                |
-------------------------------------------------*/

// Check Array If It's Values Or One Of Them Are Null
function arr_null($array)
{
	foreach ($array as $key => $value):
		if($value == ''):
			return true;
		endif;
	endforeach;
	return false;
}
// Check Array If One Of String Values Is More Than 
function arr_len($array, $len)
{
	foreach ($array as $key => $value):
		if(strlen($value) >= $len):
			return true;
			break;
		endif;
	endforeach;
	return false;
}
// Check Array If One Of String Values Is Less Than 
function arr_less($array, $len)
{
	foreach ($array as $key => $value):
		if(strlen($value) <= $len):
			return true;
			break;
		endif;
	endforeach;
	return false;
}
/*---------/--------------------------------------*/
// Check Request Method
function post_method()
{
	if($_SERVER['REQUEST_METHOD'] == 'POST'):
		return true;
	else:
		return false;
	endif;
}
function get_method()
{
	if($_SERVER['REQUEST_METHOD'] == 'GET'):
		return true;
	else:
		return false;
	endif;
}

/*-----------------Security------------------*/

// Set Agent Setup
function getUserAgent()
{   
	return $_SERVER['HTTP_USER_AGENT'];
}

function getUserIp()
{
	$ip = null;
	if(!empty($_SERVER['HTTP_CLIENT_IP'])):
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])):
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else:
		$ip = $_SERVER['REMOTE_ADDR'];
	endif;
	return $ip;
}

function prevent_sess_hijacking(){
	/*----------- Prevent Session Hijacking ------------*/
	# get user ip
	$ip = null;
	if(!empty($_SERVER['HTTP_CLIENT_IP'])):
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])):
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else:
		$ip = $_SERVER['REMOTE_ADDR'];
	endif;
	# get user agent
    $agent =  $_SERVER['HTTP_USER_AGENT'];
    /**/
	@session_start();
	// if first visit, set a code verification [ip+agent]
	if (empty($_SESSION['userInfo'])):
		@session_unset($_SESSION['userInfo']);
		$_SESSION['userInfo'] = md5($agent.$ip);
	else:
	// if not, check if code is the same as we set before
		if($_SESSION['userInfo'] != md5($agent.$ip)):
			// if not the same, destroy session
            session_unset();
			session_destroy();
	    endif;
	endif;
}
/*-----------------Csrf--------------------------------*/
function csrf()
{
	@session_start();
	$_SESSION['csrf-token'] = md5(uniqid(mt_rand(),true));
	return "<input type='hidden' name='csrf-token' value='".$_SESSION['csrf-token']."' >";
	 
}
function csrf_validation()
{
	@session_start();
	return isset($_POST['csrf-token']) && $_POST['csrf-token'] == @$_SESSION['csrf-token'] ? true : false;

}
/**/

function clear_sess_dir($path = null)
{
	if($path == null):
		global $config;
		if(isset($config['sess-dir']) AND !empty($config['sess-dir'])):
			$path = $config['sess-dir'];
	    endif;
	endif;
	foreach (scandir($path) as $file):
		if(is_file($path.DS.$file)):
			if(filesize($path.DS.$file) == 0):
		        @unlink($path.DS.$file);// Delete file if empty
		    endif;
		endif;
	endforeach;

}