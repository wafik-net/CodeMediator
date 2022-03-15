<?php  
/*
 *---------------------------------------------------------------
 * MVC App
 *---------------------------------------------------------------
 * 
 * Do Not Edit Or Remove Anything From This File 
 * This Is The Class Which Will Run The Whole Project
 *
 */

class App
{
	function __construct()
	{
		global $system;
		// If
		if($system['error'] != null):
			die( "<div style='background: #f6f6f6;padding: 10px;border-left: 3px solid #2196f3;list-style: none'>".$system['error']."</div>");
			
		else:
			new Router();
		endif;
		
	}

}
