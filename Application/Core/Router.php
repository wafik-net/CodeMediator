<?php //Last Edit: 25-03-2022 
/*
 *---------------------------------------------------------------
 * CodeMediator - Router
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
 * ON Localhost 
 * [-] GEt Request URL : /appname/blog/article/1
 * [-] Remove appname  : /blog/article/1
 *
 * dispatch()
 * [-] Run parseUrl();
 * [-] Call The Target Controller -> Check If Exist And Run It 
 * [-] Call The Target Method -> Check If Exist And Run It
 *
 * -----------------------IF localhost ------------------
 *       
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



class Router
{
    // Default Properties
    private $uri_controller = 'index';
    private $uri_action     = 'default';
    private $uri_params     = null;

    function __construct()
    {
        $this->dispatch();

    }

    private function parseUrl()
    {
 
        $url = null;
        /* ----------------------- IF Localhost --------------------*/
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
        /* -----------------------IF ! Localhost ---------------------*/
        else:
            $url = strtolower($_SERVER['REQUEST_URI']);
        endif;
        # remove double slash // (otherwise, will be set as null)
        $url = str_replace('///', '/', $url);
        $url = str_replace('//', '/', $url);

        /* -----------------------Start Parse URL --------------------*/ 

        $request    = parse_url($url,PHP_URL_PATH); // filter queries (?q=..)
        $request    = trim($request, '/');

        $explode    = explode('/', $request);

        //------------------------------------------------
        if(isset($explode[0]) && $explode[0] != '')
        {
            $this->uri_controller = $explode[0];
        }
        //------------------------------------------------
        if(isset($explode[1]) && $explode[1] != '')
        {
            $this->uri_action = $explode[1]; 
        }
        //------------------------------------------------
        if(isset($explode[2]) && $explode[2] != '')
        {
            $this->uri_params = array_slice($explode, 2);
        }
        return $explode;

    }

    private function getRoutes()
    {
        global $route;

        $fetch = new stdClass();

        $key_controller = null;
        $key_action = null;
        $key_params = null;
        
        if(!empty($route)):
            /*
             * Loop on routes
             */
            foreach($route as $key => $value):
                $explode_key = explode('/', $key);
                /*
                 * Parse Route -> Key
                 */
                #key controller
                if(isset($explode_key[0]) && $explode_key[0] != ''):
                    $key_controller = $explode_key[0];
                else:
                    $key_controller = null;
                endif;
                #key acton
                if(isset($explode_key[1]) && $explode_key[1] != ''):
                    $key_action = $explode_key[1];
                else:
                    $key_action = null;
                endif;
                #key params
                if(isset($explode_key[2]) && $explode_key[2] != ''):
                    $key_params = implode('/', array_slice($explode_key, 2));
                else:
                    $key_params = null;
                endif;
                
                /*
                 * Parse Route -> Value
                 */
                # if uri controller found
                if($key_controller == $this->uri_controller):
                    $fetch->k_controller = $key_controller;
                    $fetch->k_action = $key_action;
                    $fetch->k_params = $key_params;
                   
                    $explode_val = explode('/', $value);

                    #value controller
                    if(isset($explode_val[0]) && $explode_val[0] != ''):
                        $fetch->v_controller = $explode_val[0];
                    else:
                        $fetch->v_controller = null;
                    endif;

                    #value action
                    /*
                     * case url has no action 
                     */
                    if(count($this->parseUrl()) == 1):
                        
                        if(isset($explode_val[1]) && $explode_val[1] != ''):
                            $fetch->v_action = $explode_val[1];
                            #value params
                            if(isset($explode_val[2]) && $explode_val[2] != ''):
                                $fetch->v_params = array_slice($explode_val, 2);
                            else:
                                $fetch->v_params = null;
                            endif;
                        else:
                            $fetch->v_action = null;
                        endif;
                        break;
                    endif;
                    /*
                     * remap all actions
                     */
                    if(count($this->parseUrl()) >= 2):
                        if($key_action == "*"):
                            if(isset($explode_val[1]) && $explode_val[1] != ''):
                                $fetch->v_action = $explode_val[1];
                                #value params
                                if(isset($explode_val[2]) && $explode_val[2] != ''):
                                    if($explode_val[2] == '$1'):
                                        $fetch->v_params = implode('/', array_slice($this->parseUrl(), 1));
                                    else:
                                        $fetch->v_params = array_slice($explode_val, 2);
                                    endif;
                                else:
                                    $fetch->v_params = null;
                                endif;
                                break;
                            else:
                                $fetch->v_action = null;
                            endif;
                            
                        endif;
                        /*
                         * case action found, and url has no args
                         */
                        if($key_action == $this->uri_action AND empty($this->uri_params)):

                            if(isset($explode_val[1]) && $explode_val[1] != ''):
                                $fetch->v_action = $explode_val[1];
                            else:
                               $fetch->v_action = null;
                            endif;
                            #value params
                            if(isset($explode_val[2]) && $explode_val[2] != ''):
                                $fetch->v_params = array_slice($explode_val, 2);
                            else:
                               $fetch->v_params = null;
                            endif;
                            break;

                        endif;
                        
                        /*
                         * case action found, but arg is not found
                         */
                        if($key_action == $this->uri_action AND $key_params != implode('/', $this->uri_params)):
                            /*
                             * case uri arg not found, but key arg = * : use uri arg & break
                             */
                            if(isset($key_params) AND $key_params == '*'):
                                if(isset($explode_val[1]) && $explode_val[1] != ''):
                                    $fetch->v_action = $explode_val[1];
                                    if(isset($explode_val[2]) && $explode_val[2] != ''):
                                        if($explode_val[2] == '$1'):
                                            $fetch->v_params = $this->uri_params;;
                                        else:
                                            $fetch->v_params = $explode_val[2];
                                        endif;
                                        break;
                                    else:
                                        $fetch->v_params = null;
                                    endif;  
                                else:
                                    $fetch->v_action = null;
                                endif;
                            endif;
                            /*
                             * case uri arg not found, do not break, until find right args
                             */
                            if(isset($explode_val[1]) && $explode_val[1] != ''):
                                $fetch->v_action = $explode_val[1];
                                if(isset($explode_val[2]) && $explode_val[2] != ''):
                                    $fetch->v_params = array_slice($explode_val, 2);
                                else:
                                    $fetch->v_params = null;
                                endif;
                            else:
                                $fetch->v_action = null;
                            endif;    
                        endif;
                        /*
                         * case both uri arg and action found
                         */
                        if($key_action == $this->uri_action AND $key_params == implode('/', $this->uri_params)):
                            if(isset($explode_val[1]) && $explode_val[1] != ''):
                                $fetch->v_action = $explode_val[1];
                                #value params
                                if(isset($explode_val[2]) && $explode_val[2] != ''):
                                    $fetch->v_params = array_slice($explode_val, 2);
                                else:
                                    $fetch->v_params = null;
                                endif;  
                                break;
                            else:
                                $fetch->v_action = null;
                            endif;
                        endif;
                    endif;     
                endif;
            endforeach;
        endif;
        return $fetch;

    }

    private function dispatch()
    {

        $parseUrl = $this->parseUrl();
        $route = $this->getRoutes();

        /* [1] Define controller/action/params ---------------------------------------*/

        # define controller
        if(@$route->v_controller == null):
            $run_controller = $this->uri_controller.'Controller';
        else:
            $run_controller = @$route->v_controller;
        endif;

        # define action
        switch(@$route->k_action):
            case null:
                $run_action = isset($route->v_action) ? $route->v_action : $this->uri_action;
                break;
            case "*":
                # run value action if exist
                if(@$route->v_action != null AND $this->uri_action != 'default'):
                    $run_action = @$route->v_action;
                else:
                    $run_action = $this->uri_action;
                endif;
                break;
            case $this->uri_action:
                # run value action if exist
                if(@$route->v_action != null):
                    $run_action = @$route->v_action;
                else:
                    $run_action = $this->uri_action;
                endif;
                break;
            default:
                $run_action = $this->uri_action;
            break;
        endswitch;

        # define args & set prefixes
        /*
         * Convert $1 to URI Arg
         */ 
        if(isset($route->k_params) AND $route->k_params == "*"):
            $route->k_params = array_slice($parseUrl, 2);
        endif;
        if(isset($route->v_params) AND $route->v_params[0] == '$1'):
            $route->v_params = array_slice($parseUrl, 2);
        endif;

        switch(@$route->k_params):
            case "*": #replace uri arg (only if exist) 
                if(!empty($this->uri_params) AND  !empty($route->v_params)):
                    $run_params = @$route->v_params;
                else:
                    $run_params = $this->uri_params;
                endif;
                break;
            case $this->uri_params[0]: #replace specific uri arg with custom arg
                $run_params = @$route->v_params;
                break;
            default: #use custom arg if exist, otherwise: use uri arg
                $run_params = isset($route->v_params) ? $route->v_params : $this->uri_params;
                break;
        endswitch;

        /* [2] Check availability ------------------------------------------------*/

        # 1: check controller 
        if(!class_exists($run_controller)):
            global $config;
            # Development Mode 
            if($config['devmode'] == true):
                echo show_msg("<br>Controller not found: <a style='color:#3f64b5'>$run_controller</a><br>", DANGER);
            # Production Mode 
            elseif($config['devmode'] == false):
                if(class_exists($config['notFound'])):
                    # run notFoundController
                    new $config['notFound'];
                else:
                    echo page404();
                endif;
            endif;
            exit;
        endif;
        

        # 2: check action
        if(!method_exists($run_controller, $run_action)):
            global $config; 
            # Development Mode
            if($config['devmode'] == true):
                echo show_msg('<br>Action <a style="color:#3f64b5">'.$run_action.'</a> is not set<br>', DANGER);
            # Production Mode
            elseif($config['devmode'] == false):
                if(class_exists($config['notFound'])):
                    # run notFoundController
                    new $config['notFound'];
                else:
                    echo page404();
                endif;
            endif;
            exit;
        endif;
        # 3: check params
        /*
         * case url has arg : 
         *    -check if url arg = route->key->arg
         *    -check if route->value->arg not empty
         */

        if(isset($route->k_action) AND $this->uri_action == $route->k_action): #apply only for custom route
            if(!empty($this->uri_params)):
                // check if key arg = url arg
                if(isset($route->k_params) AND $route->k_params != '*'):
                    # convert key->arg to str to make comparison
                    $k_params = is_array($route->k_params) 
                                ? implode('/', $route->k_params) : $route->k_params;
                    if(@implode('/', $this->uri_params) != $k_params):
                        /*-------------------- show error ------------------*/
                        global $config;
                        if(!isset($config['devmode'])):
                            echo page404(); exit();
                        endif;
                        # Development Mode
                        if($config['devmode'] == true):
                            echo show_msg('<br>Arg <a style="color:#3f64b5">'.implode('/', $this->uri_params).'</a> is not used, use <u>$1</u> to pass url args<br>', DANGER);
                        # Production mode
                        elseif(isset($config['devmode']) AND $config['devmode'] == false):
                            if(class_exists($config['notFound'])):
                                new $config['notFound']; // run notFoundController
                            else:
                                echo page404();
                            endif;
                        endif;
                        exit();
                    endif;
                endif;
                // check if value->arg not empty
                if(empty($route->v_params)):
                    /*-------------------- show error ------------------*/
                    global $config;
                    # Development Mode
                    if(!isset($config['devmode'])):
                        echo page404(); exit();
                    endif;
                    if($config['devmode'] == true):
                        echo show_msg('<br>Arg <a style="color:#3f64b5">'.implode('/', $this->uri_params).'</a> is not used, use <u>$1</u> to pass url args<br>', DANGER);
                    # Production mode
                    elseif(isset($config['devmode']) AND $config['devmode'] == false):
                        if(class_exists($config['notFound'])):
                            new $config['notFound']; // run notFoundController
                        else:
                            echo page404();
                        endif;
                    endif;
                    exit();
                endif;
            endif;
        endif;



        # check parameters : if one elements ? convert to str : keep it as array
        if(is_array($run_params) AND @count($run_params) == 1):
            $run_params = (string) $run_params[0];
        endif;

        /* [3] Run The Whole Things ------------------------------------------------*/

        # 1: run controller
        $controller = new $run_controller;

        # 2: run action an pass args
        $controller->$run_action($run_params);


    }
    

}

