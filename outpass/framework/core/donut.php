<?php
/**
 * File contains necessary information that defines the main skeleton of your application
 * Source code pattern must not be modified
 * Files to be used with the comments.
 * @Author : Operce Technologies
 * @Year : 2016
 *
 *
 **/


defined('_DONUT') or die('Access Denied');
require_once "framework/core".DS."_autoload.php";

final class donut {

    public static $instance = array();
    
    private function __constructor() {

    }
    private function __clone() {

    }
    public static function init() {
        Loader::load('setting','framework/core');        
        Loader::load('database', LIB);  
        Loader::load('crud', LIB);          
        Loader::load('UserAgent', LIB);
        Loader::load('Helpers', LIB);    
        Loader::load('session', LIB);
        Loader::load('cookie', LIB);
        Loader::load('model', APP);
        Loader::load('controller',APP);
        Loader::load('router', CORE);

      session::sessionStart('DONUT');

        // remove the magic quotes
        Helpers::removeMagicQuotes();

        // unregister globals
        Helpers::unregisterGlobals();

        $router = new Router();
        $router->route();
    }

}

?>