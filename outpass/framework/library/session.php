<?php

defined('_DONUT') or die('Access Denied');

class session {

    private function __construct() {
    }



    static function set_post_data($post, $token, $checksum = array())
    {
        self::set($token, $post);
    }

    static function clean_data($token)
    {
        if(isset($_SESSION[$token]))
            unset($_SESSION[$token]);
    }

    static function check($token, $location)
    {
        if(session::get($token) == null) {
            $_SESSION = array();
            session_destroy();
            header("HTTP/1.1 301 Moved Permanently");
            header("location:".$location);
        }
    }

    static function compare($token, $data, $location)
    {
        if(session::get($token) == null) {
            $_SESSION = array();
            session_destroy();
            header("HTTP/1.1 301 Moved Permanently");
            header("location:".$location);
        }
        else if(session::get($token) != $data)
        {            
            $_SESSION = array();
            session_destroy();
            header("HTTP/1.1 301 Moved Permanently");
            header("location:".$location);
        }
    }

    static function clean_session($location)
    {
        $_SESSION = array();
        session_destroy();
        header("HTTP/1.1 301 Moved Permanently");
        header("location:".$location);
    }

    static protected function preventHijacking()
    {
        if(!isset($_SESSION['IPaddress']) || !isset($_SESSION['userAgent']))
            return false;

        if ($_SESSION['IPaddress'] != Helpers::getClientIP())
            return false;

        if( $_SESSION['userAgent'] != Helpers::getUserAgent())
            return false;

        return true;
    }


    static function regenerateSession()
    {
        // If this session is obsolete it means there already is a new id
        if(isset($_SESSION['OBSOLETE']))
            return;

        // Set current session to expire in 10 seconds
        $_SESSION['OBSOLETE'] = true;
        $_SESSION['EXPIRES'] = time() + 10;

        // Create new session without destroying the old one
        session_regenerate_id(false);

        // Grab current session ID and close both sessions to allow other scripts to use them
        $newSession = session_id();
        session_write_close();

        // Set session ID to the new one, and start it back up again
        session_id($newSession);
        session_start();

        // Now we unset the obsolete and expiration values for the session we want to keep
        unset($_SESSION['OBSOLETE']);
        unset($_SESSION['EXPIRES']);
    }


    static protected function validateSession()
    {
        if( isset($_SESSION['OBSOLETE']) && !isset($_SESSION['EXPIRES']) )
            return false;

        if(isset($_SESSION['EXPIRES']) && $_SESSION['EXPIRES'] < time())
            return false;

        return true;
    }

    static function sessionStart($name, $limit = 300, $path = '/', $domain = null, $secure = null, $httponly = true)
    {
        // Set the cookie name
        session_name($name . '_Session');
        // Set SSL level
        //$https = isset($secure) ? $secure : isset($_SERVER['HTTPS']);

        // Set session cookie options
        //session_set_cookie_params($limit, $path, $domain, $https, $httponly);
        session_start();

        // Make sure the session hasn't expired, and destroy it if it has
        if(self::validateSession())
        {
            // Check to see if the session is new or a hijacking attempt
            if(!self::preventHijacking())
            {
                // Reset session data and regenerate id
                $_SESSION = array();
                $_SESSION['IPaddress'] = Helpers::getClientIP();
                $_SESSION['userAgent'] = Helpers::getUserAgent();
                self::regenerateSession();

            // Give a 5% chance of the session id changing on any request
            }elseif(rand(1, 100) <= 5){
                self::regenerateSession();
            }
        }else{
            $_SESSION = array();
            session_destroy();
            session_start();
        }
    }


    static function set($key,$value){
        $_SESSION[$key]=$value;
    }
    
    // function to retrieve the value based on key from PHP session
    static function get($key){
        if(isset($_SESSION[$key]))
            return $_SESSION[$key];
        else
            return null;
    }


}

?>