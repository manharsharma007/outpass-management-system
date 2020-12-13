<?php

/**
 * File contains necessary information that defines the bootstrap of your application
 * Source code pattern must not be modified
 * Files to be used with the comments.
 * @Author : Operce Technologies
 * @Year : 2016
 *
 *
 **/

defined('SITE') or die('Config file not found');
define('_DONUT',TRUE);
// directory separator
define('DS',DIRECTORY_SEPARATOR);

//load the error donut class
require_once "framework/core".DS."donut.php";
//creating the instance of donut object
$donut = donut::init();


?>