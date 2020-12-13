<?php
/**
 * File contains necessary information that defines the autoloader of your application
 * Source code pattern must not be modified
 * Files to be used with the comments.
 * @Author : Operce Technologies
 * @Year : 2016
 *
 *
 **/

defined('_DONUT') or die('Access Denied');
final class Loader {
	private function __constructor() {

	}
	private function __clone() {

	}
	public static function load($file, $dir) {
		if(file_exists($dir.DS.$file.'.php')) {
			require_once $dir.DS.$file.'.php';
		}
	}

	public static function addScript($script) {
		printf('<script type="text/javascript" src="'.SITE_URL.DS.'public'.DS.$script.'"></script>'."\n");
		return true;
	}
	public static function addStyle($script) {
		printf('<link rel="stylesheet" href="'.SITE_URL.DS.'public'.DS.$script.'" />'."\n");
		return true;
	}
	public static function addAsset($asset) {
		printf(SITE_URL.DS.'public'.DS.$asset."\n");
		return true;
	}
}


?>