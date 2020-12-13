<?php

class Database 
{
    private static $dbName = DB_NAME ; 
    private static $dbHost = DB_HOST ;
    private static $dbUsername = DB_USER;
    private static $dbUserPassword = DB_PASS;
    
    private static $cont  = null;
    
    public function __construct() {
        die('Init function is not allowed');
    }
    
    public static function connect()
    {
       // One connection through whole application
       if ( null == self::$cont )
       {      
        try 
        {
          self::$cont =  new PDO( "mysql:host=".self::$dbHost.";"."dbname=".self::$dbName, self::$dbUsername, self::$dbUserPassword);
          self::$cont->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
        }
        catch(PDOException $e) 
        {
          die($e->getMessage());  
        }
       } 
       return self::$cont;
    }
    
    public static function disconnect()
    {
        self::$cont = null;
    }
}
?>