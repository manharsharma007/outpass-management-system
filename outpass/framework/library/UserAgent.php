<?php
class UserAgent
{   
    public static function check($pattern){
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $match = preg_match($pattern, strtolower($agent));
        return !empty($match);
    }
    
    public static function isOpera(){
        return self::check("/opera/");
    }
    public static function isOpera10_5(){
        return self::isOpera() && self::check("/version\/10\.5/");
    }       
    public static function isChrome(){ 
        return self::check("/\bchrome\b/");
    }
    public static function isWebKit(){
        return self::check("/webkit/");
    }

    public static function isAndroid(){
        return self::check("/android/");
    }

    public static function isSafari(){
        return !self::isChrome() && self::check("/safari/");
    }

    public static function isSafari2(){
        return self::isSafari() && self::check("/applewebkit\/4/");
    }
 // unique to Safari 2
    public static function isSafari3(){
        return self::isSafari() && self::check("/version\/3/");
    }

    public static function isSafari4(){
        return self::isSafari() && self::check("/version\/4/");
    }

    public static function isSafari5(){
        return self::isSafari() && self::check("/version\/5/");
    }

    public static function isiPhone(){
        return self::isSafari() && self::check("/iphone/");
    }

    public static function isiPod(){
        return self::isSafari() && self::check("/ipod/");
    }

    public static function isiPad(){
        return self::isSafari() && self::check("/ipad/");
    }

    public static function isIE(){
        return !self::isOpera() && self::check("/msie/");
    }

    public static function isGecko(){
        return !self::isWebKit() && self::check("/gecko/");
    }

    public static function isGecko3(){
        return self::isGecko() && self::check("/rv:1\.9/");
    }

    public static function isGecko4(){
        return self::isGecko() && self::check("/rv:2\.0/");
    }

    public static function isGecko5(){
        return self::isGecko() && self::check("/rv:5\./");
    }

    public static function isFF(){
        return self::isGecko() && self::check("/firefox/");
    }

    public static function isFF3_0(){
        return self::isGecko3() && self::check("/rv:1\.9\.0/");
    }

    public static function isFF3_5(){
        return self::isGecko3() && self::check("/rv:1\.9\.1/");
    }

    public static function isFF3_6(){
        return self::isGecko3() && self::check("/rv:1\.9\.2/");
    }

    public static function isWindows(){
        return self::check("/windows|win32/");
    }

    public static function isWindowsCE(){
        return self::check("/windows ce/");
    }

    public static function isMac(){
        return self::check("/macintosh|mac os x/");
    }

    public static function isLinux(){
        return self::check("/linux/");
    }

    public static function isiOS(){
        return self::isiPhone() || self::isiPod() || self::isiPad();
    }
    
    public static function isMobile(){
        return self::isiOS() || self::isAndroid();
    }
}
?>
