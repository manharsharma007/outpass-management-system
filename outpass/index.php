<?php
require_once 'protected/config/config.php';
$routes = array('errorHandler'=>array(
			// use 'site/error' action to display errors
			'action'=>'error/main',
		));

require_once "framework/index.php";