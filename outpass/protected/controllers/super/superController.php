<?php

class superController extends Controller {
    
    private $model;
    private $pagination_limit = 10;
    private $pagination_buttons = array('Previous', 'Next');

    function __construct() {
        session::check('user_id', SITE_URL);
        if(session::get('user_role') != 'SA')
        {            
            session::clean_session(SITE_URL);
        }
        date_default_timezone_set('Asia/Kolkata');
    }    

    function actionMain() {

        header('HTTP/1.1 405 Requested Method Not Allowed');
        echo 'Invalid access! Go <a href="'.SITE_URL.'">Home</a>';
        die();
    }

    function actionBypassApproval()
    {
        $modal = $this->model('outpass');
        $this->view('header');

        if(isset($_POST['submit']))
        {
            if($_POST['security_token'] !== $_SESSION['token']) 
            {
                $this->error['message'] = 'Token Mismatch';
                $this->error['error_code'] = ERROR;
            }
            else
            {
                $result = $modal->process_bypass_approval();

                $this->error['error_code'] = $modal->flag;
                $this->error['message'] = $modal->message;
            }
           
        }
        /**
         *
         *
         *
         * Generate security token to handle double form submissions
         *
         *
         *
         **/
        $this->data['token'] = md5(uniqid(rand(), TRUE).time());
        $_SESSION['token'] = $this->data['token'];

        $this->view('bypass_approve_pass','super');
        $this->view('footer');
    }

    function actionIssued($page = 1)
    {
        $modal = $this->model('outpass');
        $filter = array();
        $limit = 'limit 0,'.$this->pagination_limit;
        $this->data['pagination_link'] = '';

        if($page > 1)
        {
            $limit = 'limit '.(($page * $this->pagination_limit) - $this->pagination_limit).','.($this->pagination_limit);
        }

        if(isset($_POST['filter']))
        {

            if(isset($_POST['from_filter']) && $_POST['from_filter'] != '')
            {
                $filter['from'] = $_POST['from_filter'];
            }
            if(isset($_POST['to_filter']) && $_POST['to_filter'] != '')
            {
                $filter['to'] = $_POST['to_filter'];
            }
            if(isset($_POST['type_filter']) && $_POST['type_filter'] != '')
            {
                $filter['type'] = $_POST['type_filter'];
            }
        }

        $this->data['students'] = $modal->get_issued_pass($filter,$limit);
        $count = count($modal->get_issued_pass($filter));

        if($page > 1)
        {
            $this->data['pagination_link'] .= '<a href="'.SITE_URL.'super/students/'.($page - 1).'" class="previous">Previous</a>';
        }
        if($count > count($this->data['students']) * $page && count($this->data['students']) > 0)
        {
            $this->data['pagination_link'] .= '<a href="'.SITE_URL.'super/students/'.($page + 1).'" class="next">Next</a>';
        }

        $this->data['page_title'] = 'View Students | Outpass Management System';

        $this->view('header');
        $this->view('view_pass','super');
        $this->view('footer');
    }

    function actionPending($page = 1)
    {
        $modal = $this->model('outpass');
        $filter = array();
        $limit = 'limit 0,'.$this->pagination_limit;
        $this->data['pagination_link'] = '';

        if($page > 1)
        {
            $limit = 'limit '.(($page * $this->pagination_limit) - $this->pagination_limit).','.($this->pagination_limit);
        }

        if(isset($_POST['filter']))
        {

            if(isset($_POST['from_filter']) && $_POST['from_filter'] != '')
            {
                $filter['from'] = $_POST['from_filter'];
            }
            if(isset($_POST['to_filter']) && $_POST['to_filter'] != '')
            {
                $filter['to'] = $_POST['to_filter'];
            }
            if(isset($_POST['type_filter']) && $_POST['type_filter'] != '')
            {
                $filter['type'] = $_POST['type_filter'];
            }
        }

        $this->data['students'] = $modal->get_pending_pass($filter,$limit);
        $count = count($modal->get_pending_pass($filter));

        if($page > 1)
        {
            $this->data['pagination_link'] .= '<a href="'.SITE_URL.'super/students/'.($page - 1).'" class="previous">Previous</a>';
        }
        if($count > count($this->data['students']) * $page && count($this->data['students']) > 0)
        {
            $this->data['pagination_link'] .= '<a href="'.SITE_URL.'super/students/'.($page + 1).'" class="next">Next</a>';
        }

        $this->data['page_title'] = 'View Students | Outpass Management System';

        $this->view('header');
        $this->view('pending_pass','super');
        $this->view('footer');
    }

    function actionApprove_Pass()
    {
        $modal = $this->model('outpass');
        $this->view('header');

        if(isset($_POST['submit']))
        {
            if($_POST['security_token'] !== $_SESSION['token']) 
            {
                $this->error['message'] = 'Token Mismatch';
                $this->error['error_code'] = ERROR;
            }
            else
            {
                $result = $modal->process_approve();

                $this->error['error_code'] = $modal->flag;
                $this->error['message'] = $modal->message;
            }
           
        }
/**
 *
 *
 *
 * Generate security token to handle double form submissions
 *
 *
 *
 **/
        $this->data['token'] = md5(uniqid(rand(), TRUE).time());
        $_SESSION['token'] = $this->data['token'];

        $this->view('approve_pass','super');
        $this->view('footer');
    }


}
?>