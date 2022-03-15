<?php  
/*
 *---------------------------------------------------------------
 * MVC Router
 *---------------------------------------------------------------
 *
 * Do Not Edit Or Remove Anything From This File 
 *
 * Parse Request URL   : www.site.com/blog/article/1
 *                     : www.site.com/controller/action/params
 *
 * parseUrl()
 * [-] Get Request URL : /blog/article/1/
 * [-] Remove '/'      :  blog/article/1
 * [-] Explode URL     : [0]controller [1]action [2‬]params
 *
 * IF Localhost 
 * [-] GEt Request URL : /appname/blog/article/1
 * [-] Remove appname  : /blog/article/1
 *
 * dispatch()
 * [-] Run parseUrl();
 * [-] Call The Target Controller -> Check If Exist And Run It 
 * [-] Call The Target Method -> Check If Exist And Run It
 *
 */


class Router
{

    private $_controller = 'index';
    private $_action = 'default';
    private $_params = [0];

    function __construct()
    {
        $this->dispatch();

    }

    private function parseUrl()
    {
        /* -----------------------IF localhost ------------------*/
        /*
         * @Lets say APP Name is (MyApp)
         * IF the app folder exists in sub folders (ex: /sub/projects/MyApp)
         * And the current link is (ex: localhost/sub/projects/myapp/user/login)
         * The we have to cut the link until myapp, result will be: (ex: user/login) 
         * So: user as controller{}  AND login as action()
         *
         * [1] Get AppName        : (ex: MyApp)
         * [2] Get current URI    : sub/projects/myapp/user/login
         * [3] Explod URI To parts: Loop on each part, and cut all parts until (myapp)
         */ 
        $url = null;
        if(isset($_SERVER['HTTP_HOST']) AND $_SERVER['HTTP_HOST'] == 'localhost'):
            $appname = strtolower(basename(BASE));
            $url = strtolower($_SERVER['REQUEST_URI']);
            $cut = null;
            foreach (explode('/', $url) as $item):
                if($item != $appname):
                    $cut .= $item.'/';
                else:
                    $cut .= $appname.'/';
                    break;
                endif;
            endforeach;
            $url = str_replace($cut, '', $url);
            /* -----------------------IF ! Localhost -----------------*/
        else:
            $url = strtolower($_SERVER['REQUEST_URI']);
        endif;


        $request    = parse_url($url,PHP_URL_PATH);
        $request    = trim($request, '/');


        $explode    = explode('/', $request);
        //------------------------------------------------
        if(isset($explode[0]) && $explode[0] != '')
        {
            $this->_controller = $explode[0]; 
        }
        //------------------------------------------------
        if(isset($explode[1]) && $explode[1] != '')
        {
            $this->_action = $explode[1]; 
        }
        //------------------------------------------------
        if(isset($explode[2]) && $explode[2] != '')
        {
            /*--------------------------------------------
            | Set Parameters                            
            ----------------------------------------------
            | IF We Have More Than One Parameter Convert
            | It To Array Or Keep It As A String
            | Ex: [Controller/Action/Parameters...]
            | [] controller/action/params   => param =str
            | [] controller/action/p1/p2‬/p3 => param = array
            |    params = array('p1', 'p2‬', 'p3')
            |----------------------------------------------
            */

            $this->_params = $explode[2];


        }


    }


    private function dispatch()
    {
        // Parse Url;
        $this->parseUrl();

        // Custom Route ----------------------------------------------
        global $route;
        $res = false;
        if(!empty($route)):
            foreach ($route as $key => $value):
                if($this->_controller == $key):
                    $res = true;
                    //echo 'key: '.$key .' | '. 'value: '.$value;
                    $targetController = $value;
                endif;
            endforeach;
        endif;

        // Default Route ---------------------------------------------
        if($res == false):
            $targetController = $this->_controller.'Controller';
        endif;

        // If Controller Not Exists
        if(!class_exists($targetController)):
            global $config;
            // Development Mode 
            if($config['devmode'] == true):
                echo show_msg("<br>Controller Not Found: <a style='color:#3f64b5'>$targetController</a><br>", DANGER);
                exit;
            // Production Mode 
            elseif($config['devmode'] == false):
                if(class_exists($config['notFound'])):
                    $targetController = $config['notFound'];
                else:
                    echo page404();
                    exit;
                endif;
            endif;
        endif;

        //Run The Target Controller
        $controller = new $targetController;

        //Call The Target Method -------------------------------------
        $actionName = $this->_action;

        // If Action Method Not Exists
        if(!method_exists($controller, $actionName)):  
            global $config; 
            // Development Mode
            if($config['devmode'] == true):
                echo show_msg('<br>The method <a style="color:#3f64b5">'.$actionName.'</a> is not set<br>', DANGER);
                exit;
            // Production Mode
            elseif($config['devmode'] == false):
                $controller = new $config['notFound'];
                $actionName = 'default';
            endif;
        endif;

        // Run The Target Action Method And Pass Params If Exists
        $controller->$actionName($this->_params);

    }
}
