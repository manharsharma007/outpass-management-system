<?php

defined('_DONUT') or die('Access Denied');

class cookie {

    private function __construct() {

    }



   static function set($cookie_name, $cookie_value, $time, $path = '/')
   {
        $domain = $_SERVER['HTTP_HOST'];
        setcookie($cookie_name, $cookie_value, $time, $path);
   }


   static function get($cookie_name)
   {
        if(!isset($_COOKIE[$cookie_name])) {
            return false;
        } else {
          return $_COOKIE[$cookie_name];
        }
   }


   static function delete($cookie_name)
   {
        unset($_COOKIE[$cookie_name]);
        // empty value and expiration one hour before
        setcookie($cookie_name, '', time() - 3600);
   }


}

?>