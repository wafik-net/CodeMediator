<?php 
/*
 *---------------------------------------------------------------
 * MVC Controller
 *---------------------------------------------------------------
 *
 * Do Not Edit Or Remove Anything From This File 
 *
 * This Is The Core Controller Of The MVC
 * All The Other Controllers Will Extends From This Controller
 *
 * view()    method is using to call the view
 * extract() method is using to convert array keys into variable
 * extract() method is using to pass data to view as variables as vars
 *
 */
class Controller
{

    public $data = [];
    
    // Load View -----------------------------------------------
    public function view($viewName)
    {      
       
        $view = trim(VIEWS.DS.$viewName, '.php').'.php';

        if(file_exists($view)):

            extract($this->data); 
            require_once $view ; 
            return true;

        else:
            global $config;
            // Developement Mode
            if($config['devmode'] == true):
                echo show_msg("The view <a style='color:#2182f3'>$viewName</a> is not set<br>", DANGER);
            elseif($config['devmode'] == false):
                echo page404();
            endif;
            return false;

        endif;
    }


}