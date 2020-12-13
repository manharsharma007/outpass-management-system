<?php


defined('_DONUT') or die('Access Denied');

final class Router {

    private $nodes = array();
    private $terminal_position;
    protected $controllerSignature = 'Controller';
    protected $actionSignature = 'action';
    private $controller = 'default';
    private $action = 'main';
    private $defaultAction = 'main';
    private $routes = array();
    private $_404 = false;

    function __constructor() {

    }

    private function __clone() {
    }

    private function set_default()
    {
        global $routes;
        if(is_array($routes))
        {
            $this->routes = $routes;
        }

        if(array_key_exists('defaultController', $this->routes))
        {
            $this->controller = $this->routes['defaultController'];
        }
        if(array_key_exists('defaultAction', $this->routes))
        {
            $this->action = $this->routes['defaultAction'];
            $this->defaultAction = $this->routes['defaultAction'];
        }
        if(array_key_exists('errorHandler', $this->routes))
        {
            $this->_404 = $this->routes['errorHandler'];
        }

        $this->routes['routes'] = (isset($this->routes['routes'])) ? $this->routes['routes'] : array();
    }
    

    final public function route() {
        $this->set_default();
        $this->parse_route();
        $this->load_controller();   
    }

    private function load_controller() {

        $controller = $this->controller.$this->controllerSignature;

        Loader::load($controller, CONTROLLERS.DS.$this->controller);
        try {   
                if(class_exists($controller))
                    $controller = new $controller;
                else
                    throw new Exception("Error Processing Request", 1);
                    
        }
        catch (Exception $e) {
            die(strtoupper($controller).' is malformed. '.$e->getMessage());
        }

        if(!empty($this->action) && method_exists($controller, $this->actionSignature.$this->action)) {
            $method = $this->actionSignature.$this->action;
            call_user_func_array([$controller, $method], $this->nodes);
        }
        else {
            try {   
                if($this->_404 != false)
                {
                    header("HTTP/1.1 404 Item not found");
                    $this->_404 = explode('/', $this->_404['action']);
                    if(file_exists(CONTROLLERS.DS.$this->_404[0].DS.$this->_404[0].$this->controllerSignature.'.php'))
                    {
                        Loader::load($this->_404[0].$this->controllerSignature, CONTROLLERS.DS.$this->_404[0]);
                        if(class_exists($this->_404[0].$this->controllerSignature) && method_exists($this->_404[0].$this->controllerSignature, $this->actionSignature.$this->_404[1]))
                        {
                           $error = $this->_404[0].$this->controllerSignature;
                           $error = new $error;
                           $error->{$this->actionSignature.$this->_404[1]}();
                        }
                        else
                        {
                           throw new Exception("404. Requested item not found", 1);
                        }
                    }
                    else
                    {
                        throw new Exception("404. Requested item not found", 1);
                    }
                }
                else
                    throw new Exception("404. Requested item not found", 1);
                    
            }
            catch (Exception $e) {
                die($e->getMessage());
            }
        }
        return true;
    }


    private function parse_route()
    {
        if(isset($_GET['url']))
            $url = $_GET['url'];
        else
            $url = null;
        $args = $this->check_regex($url);
        if($args !== null)
        {
            if($args === false)
            {

                    header('HTTP/1.1 405 Requested Method Not Allowed');
                    echo 'Requested Method Not Allowed';
                    die();
            }
            else
            {
                $args['control'] = explode('/', $args['control']);
                $this->controller = $args['control'][0];
                $this->action = $args['control'][1];

                foreach ($args['args'] as $key => $value) {
                    $_GET[$key] = $value;
                }
            }
        }
        else
        {
            if(isset($url)) {
                 $url = str_replace(' ','',$url);                
                //trim the url to remove slashes on right
                $url = rtrim($url,'/');

                $url = filter_var($url, FILTER_SANITIZE_URL);

                //break the url to fragments
                $url = explode('/',$url);
                //load the url to the object variable 
                // as n1,n2,n3....

                if(isset($url[0]) && file_exists(CONTROLLERS.DS.$url[0].DS.$url[0].$this->controllerSignature.'.php')) {
                        $this->controller = $url[0];
                        unset($url[0]);
                        $url = array_values($url);
                }
                if(isset($url[0])) {
                                $this->action = $url[0];
                    unset($url[0]);
                }
                
                $this->nodes = $url;
                $this->nodes = $this->nodes ? array_values($this->nodes) : [];
            }
        }
    }

    private function filter_regex($regex)
    {
        $args = array();
        $arg_pos = array();
        $regex = rtrim($regex,'/');
        $regex_array = explode('/',$regex);
        
        foreach ($regex_array as $pos => $r) 
        {
            // if there is a dynamic parameter in url
            if (strpos($r,'{') !== false) 
            {
                $perm = substr($r,strpos($r,'{')+1,strpos($r,'}')-strpos($r,'{')-1);
                array_push($args, $perm);
                array_push($arg_pos, $pos);
                $regex_array[$pos] = str_replace( '{'.$perm.'}', '', $r );
            }
        }
        $regex = implode('/',$regex_array);
        return ['regex' =>$regex, 'args' => $args, 'arg_pos' => $arg_pos];
    }

    /**
     * match current url with registered url regexes
     */
    private function check_regex($url)
    {
        $url = rtrim($url,'/');
        foreach ($this->routes['routes'] as $route => $url_meta) {
            // filter the regex string
            $filtered_regex = $this->filter_regex($route);

            // matich regex
            preg_match('#^'.$filtered_regex['regex'].'$#',$url, $matches, PREG_OFFSET_CAPTURE);
            if(!empty($matches))
            {
                if(is_array($url_meta))
                {
                    $control = $url_meta['control'];

                    if(isset($url_meta['allow']) && array_search($_SERVER['REQUEST_METHOD'],$url_meta['allow']) === false)
                    {
                        return false;
                    } 
                    if(isset($url_meta['ajax']) && $url_meta['ajax'] === true && Helpers::isAjax() === false)
                    {
                        return false;
                    }    
                }        
                else
                    $control = $url_meta;

                $args = array();
                if(!empty($filtered_regex['args']))
                {
                    $url_split = split('/', $url);
                    $pos = 0;
                    foreach ($filtered_regex['args'] as $arg) {
                        $args[$arg] = $url_split[$filtered_regex['arg_pos'][$pos++]];
                    }
                }

                return ['control' => $control,'args' => $args];
            }
        }
        return null;
    }
    
}