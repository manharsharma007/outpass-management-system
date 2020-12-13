
<?php
/**
 * File contains necessary information that defines the controllers of your application
 * Source code pattern must not be modified
 * Files to be used with the comments.
 * @Author : Operce Technologies
 * @Year : 2016
 *
 *
 **/

defined('_DONUT') or die('Access Denied');

Loader::load('security',APP);

abstract class controller extends security {
	public $data = [];
	public $error;
	public $formData;

	private function __constructor() {

	}
	private function __clone() {

	}
	
	protected function view($view, $controller = false) {
		$error = $this->error;
		extract($this->data);
		try {	
			if(file_exists(VIEWS.DS.$controller.DS.$view.'.php'))
				require_once VIEWS.DS.$controller.DS.$view.'.php';

			elseif(file_exists(VIEWS.DS.'common'.DS.$view.'.php'))
				require_once VIEWS.DS.'common'.DS.$view.'.php';

			else
				throw new Exception("Could not load the view", 1);
				
		}
		catch (Exception $e) {
			die($e->getMessage());
		}
		return true;
	}
	protected function model($model) {
		if(file_exists(MODELS.DS.$model.'.php')) {
			Loader::load($model, MODELS);
			return new $model;
		}
		else return true;
	}




}

?>
