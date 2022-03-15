<?php 

/*
 *-------------------------------------------------------------------
 * CodeMediator - Autoloader
 *-------------------------------------------------------------------
 *
 * Do Not Remove Anything From This File 
 *
 * This File Will Load The Required Classes Automatically 
 * This File Has To Be Included In Index Of MVC
 * 
 *
 */
spl_autoload_register(function($class)
{ 
	// Folder : Config 
	if(file_exists(CONFIG.DS.$class.'.php'))
	{
		require_once CONFIG.DS.$class.'.php';

	}
	// Folder : Core 
	if(file_exists(CORE.DS.$class.'.php'))
	{
		require_once CORE.DS.$class.'.php';

	}

	// Folder : Helper
	if(file_exists(HELPER.DS.$class.'.php'))
	{
		require_once HELPER.DS.$class.'.php';

	}

    // Folder : Controllers
	if(file_exists(CONT.DS.$class.'.php'))
	{
		require_once CONT.DS.$class.'.php';
	}

	// Folder : Models
	if(file_exists(MODELS.DS.$class.'.php'))
	{
		require_once MODELS.DS.$class.'.php';
	}
	
});
