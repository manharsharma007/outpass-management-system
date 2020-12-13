<?php

class defaultController extends Controller {
	
    private $model;
    public $message;

    function __construct()
    {
        if(INSTALL == '%install%')
        {
            die('please install the app first.');
        }
    }

    function actionLogout() {
        session::clean_session(SITE_URL);
    }

    function actionMain() {
        if(isset($_POST['submit'])) {
            $model = $this->model('login');
            $result = $model->process_login();
            $this->error['error_code'] = $model->flag;
            $this->error['message'] = $model->message;
        }
        $this->view('login');
        $this->check_login();
    }

    function actionForgot() {
        if(isset($_POST['submit'])) {
            $model = $this->model('login');
            $result = $model->process_forgot();
            $this->error['error_code'] = $model->flag;
            $this->error['message'] = $model->message;
        }
        $this->view('forgot');
    }

    private function check_login()
    {
        if(isset($_SESSION['user_id']) && $_SESSION['user_id'] != '')
        {
            header("location:".SITE_URL.'home/');
        }
    }

}



?>