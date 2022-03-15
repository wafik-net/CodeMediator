<?php

/*
 *-------------------------------------------------------------------
 * CodeMediator -  Constants
 *-------------------------------------------------------------------
 *
 * Do Not Edit Or Remove Anything From This File 
 * This File Has To Be Included In Core->Config File Before Anything 
 *
 */

// Main Constants
defined('DS')        OR define('DS', DIRECTORY_SEPARATOR);
defined('BASE')      OR define('BASE', dirname(dirname(dirname( __FILE__ )), true));
defined('APP')       OR define('APP', dirname(dirname( __FILE__ )), true);
defined('CONFIG')    OR define('CONFIG', APP.DS.'config', true);
defined('CORE')      OR define('CORE', APP.DS.'core', true);
defined('SESS')      OR define('SESS', APP.DS.'session', true);
defined('HELPER')    OR define('HELPER', APP.DS.'helper', true);
defined('VIEWS')     OR define('VIEWS', APP.DS.'views', true);
defined('CONT')      OR define('CONT', APP.DS.'controllers', true);
defined('MODELS')    OR define('MODELS', APP.DS.'models', true);
defined('ASSETS')    OR define('ASSETS', BASE.DS.'assets'.DS, true);
defined('UPLOADS')   OR define('UPLOADS', BASE.DS.'uploads'.DS, true);
/**/
defined('SUCCESS')   OR define('SUCCESS', 'SUCCESS', true);
defined('DANGER')    OR define('DANGER', 'DANGER', true);
defined('WARNING')   OR define('WARNING', 'WARNING', true);
defined('ERROR')     OR define('ERROR', 'ERROR', true);

