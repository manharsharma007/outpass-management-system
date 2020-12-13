<?php

class adminController extends Controller {
    
    private $model;
    private $pagination_limit = 10;
    private $pagination_buttons = array('Previous', 'Next');

    function __construct() {
        session::check('user_id', SITE_URL);
        if(!in_array(session::get('user_role'), array('SA','A')))
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
    function actionView_students($page = 1)
    {
        $modal = $this->model('user');

        if(isset($_GET['export']))
        {
            $modal->process_export();
        }
        else
        {
            $filter = array();
            $limit = 'limit 0,'.$this->pagination_limit;
            $this->data['pagination_link'] = '';

            if($page > 1)
            {
                $limit = 'limit '.(($page * $this->pagination_limit) - $this->pagination_limit).','.($this->pagination_limit);
            }

            if(isset($_POST['filter']))
            {

                if(isset($_POST['reg_no_filter']) && $_POST['reg_no_filter'] != '')
                {
                    $filter['reg_no'] = $_POST['reg_no_filter'];
                }
                if(isset($_POST['meal_type_filter']) && $_POST['meal_type_filter'] != '')
                {
                    $filter['meal_type'] = $_POST['meal_type_filter'];
                }
                if(isset($_POST['room_no_filter']) && $_POST['room_no_filter'] != '')
                {
                    $filter['room_no'] = $_POST['room_no_filter'];
                }
                if(isset($_POST['bed_no_filter']) && $_POST['bed_no_filter'] != '')
                {
                    $filter['bed_no'] = $_POST['bed_no_filter'];
                }
            }
            $this->data['students'] = $modal->get_students($filter,$limit);
            $count = count($modal->get_students($filter));

            if($page > 1)
            {
                $this->data['pagination_link'] .= '<a href="'.SITE_URL.'admin/view_students/'.($page - 1).'" class="previous">Previous</a>';
            }
            if($count > count($this->data['students']) * $page && count($this->data['students']) > 0)
            {
                $this->data['pagination_link'] .= '<a href="'.SITE_URL.'admin/view_students/'.($page + 1).'" class="next">Next</a>';
            }

            $this->data['page_title'] = 'View Students | Outpass Management System';

            $this->view('header');
            $this->view('view_students','admin');
            $this->view('footer');
        }
    }


    function actionView_users($page = 1)
    {
        $modal = $this->model('login');

        $filter = array();
        $limit = 'limit 0,'.$this->pagination_limit;
        $this->data['pagination_link'] = '';

        if($page > 1)
        {
            $limit = 'limit '.(($page * $this->pagination_limit) - $this->pagination_limit).','.($this->pagination_limit);
        }

        if(isset($_POST['filter']))
        {

            if(isset($_POST['name_filter']) && $_POST['name_filter'] != '')
            {
                $filter['name'] = $_POST['name_filter'];
            }
            if(isset($_POST['email_filter']) && $_POST['email_filter'] != '')
            {
                $filter['email'] = $_POST['email_filter'];
            }
            if(isset($_POST['username_filter']) && $_POST['username_filter'] != '')
            {
                $filter['username'] = $_POST['username_filter'];
            }
            if(isset($_POST['user_role_filter']) && $_POST['user_role_filter'] != '')
            {
                $filter['user_role'] = $_POST['user_role_filter'];
            }
        }
        $this->data['users'] = $modal->get_users($filter,$limit);
        $count = count($modal->get_users($filter));

        if($page > 1)
        {
            $this->data['pagination_link'] .= '<a href="'.SITE_URL.'admin/view_users/'.($page - 1).'" class="previous">Previous</a>';
        }
        if($count > count($this->data['users']) * $page && count($this->data['users']) > 0)
        {
            $this->data['pagination_link'] .= '<a href="'.SITE_URL.'admin/view_users/'.($page + 1).'" class="next">Next</a>';
        }

        $this->data['page_title'] = 'View Users | Outpass Management System';

        $this->view('header');
        $this->view('view_users','admin');
        $this->view('footer');
    }


    function actionAdd_student()
    {
        $model = $this->model('user');

        if(isset($_POST['submit']))
        {
            if($_POST['security_token'] !== $_SESSION['token']) 
            {
                $this->error['message'] = 'Token Mismatch';
                $this->error['error_code'] = ERROR;
            }
            else
            {
                $model->process_student();
                $this->error['message'] = $model->message;
                $this->error['error_code'] = $model->flag;
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

        $this->view('header');
        $this->view('addstudent','admin');
        $this->view('footer');

    }

    function actionAdd_user()
    {
        $model = $this->model('login');

        if(isset($_POST['submit']))
        {
            if($_POST['security_token'] !== $_SESSION['token']) 
            {
                $this->error['message'] = 'Token Mismatch';
                $this->error['error_code'] = ERROR;
            }
            else
            {
                $model->process_user();
                $this->error['message'] = $model->message;
                $this->error['error_code'] = $model->flag;
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

        $this->view('header');
        $this->view('adduser','admin');
        $this->view('footer');

    }

    function actionReturn()
    {
        $modal = $this->model('outpass');
        $this->view('header');
        $this->view('return','admin');

        if(isset($_POST['return']))
        {            
            $this->data['pass'] = $modal->get_outpass_by_id();
            $this->error['message'] = $modal->message;
            $this->error['error_code'] = $modal->flag;
            $this->view('user','admin');
        }

        $this->view('footer');
    }

    function ActionProcessReturn()
    {
        if(Helpers::isAjax())
        {
            $modal = $this->model('outpass');

            $modal->process_return_outpass();

            $response = ($modal->flag == SUCCESS) ? '<div class="msg success"><p>'.$modal->message.'</p></div>' : '<div class="msg fail"><p>'.$modal->message.'</p></div>' ;
            $code = $modal->flag;

            echo json_encode(array('text'=>$response, 'code'=>$code));
        }
    }

    function actionDelete_students()
    {
        $modal = $this->model('user');

        if(isset($_POST['submit']))
        {
            $modal->delete_students();

        }
        
        if(isset($_SERVER['HTTP_REFERER']))
            header('location:'.$_SERVER['HTTP_REFERER']);
        else
            header('location:'.SITE_URL);
    }

    function actionDelete_users()
    {
        $modal = $this->model('login');

        if(isset($_POST['submit']))
        {
            $modal->delete_users();

        }
        
        if(isset($_SERVER['HTTP_REFERER']))
            header('location:'.$_SERVER['HTTP_REFERER']);
        else
            header('location:'.SITE_URL);
    }

    function actionImport()
    {
        $modal = $this->model('user');
        if(isset($_POST['submit']))
        {
            $modal->process_import();
            $this->error['error_code'] = $modal->flag;
            $this->error['message'] = $modal->message;
        }

        $this->view('header');
        $this->view('import','admin');
        $this->view('footer');
        
    }

    function actionEditUser($regenerate = false)
    {
        $modal = $this->model('login');
        if($regenerate == true) {

            $user = $modal->check_user($_GET['id']);
            if (count($user) > 0 && $user != false) 
            {
                if($_GET['token'] !== $_SESSION['token']) 
                {
                    $this->error['message'] = 'Token Mismatch';
                    $this->error['error_code'] = ERROR;
                }

                else {
                    $modal->regenerate_password($_GET['id']);

                    $this->error['message'] = $modal->message;
                    $this->error['error_code'] = $modal->flag;
                }
            }
        }
        if(isset($_GET['id']) && is_numeric($_GET['id']))
        {
            $user = $modal->check_user($_GET['id']);
            if (count($user) > 0 && $user != false) 
            {
                if(isset($_POST['submit']))
                {
                    if($_POST['security_token'] !== $_SESSION['token']) 
                    {
                        $this->error['message'] = 'Token Mismatch';
                        $this->error['error_code'] = ERROR;
                    }
                    else
                    {
                        $modal->update_user();

                        $this->error['message'] = $modal->message;
                        $this->error['error_code'] = $modal->flag;
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

                // update form after submit
                $student = $modal->check_user($_GET['id']);

                $this->data['name'] = $student->full_name;
                $this->data['email'] = $student->user_email;
                $this->data['username'] = $student->user_name;
                $this->data['role'] = $student->user_type;
                $this->data['status'] = $student->active;

                $this->view('header');
                $this->view('edit_user','admin');
                $this->view('footer');
            }
            else
            {
                header('HTTP/1.1 405 Requested Method Not Allowed');
                echo 'Invalid access! Go <a href="'.SITE_URL.'admin/students">Home</a>';
                die();
            }
        }
        else
        {
            header('HTTP/1.1 405 Requested Method Not Allowed');
            echo 'Invalid access! Go <a href="'.SITE_URL.'admin/students">Home</a>';
            die();
        }
    }



    function actionEditStudent()
    {
        $modal = $this->model('user');
        if(isset($_GET['id']) && is_numeric($_GET['id']))
        {
            $user = $modal->check_student($_GET['id']);
            if (count($user) > 0 && $user != false) 
            {
                if(isset($_POST['submit']))
                {
                    if($_POST['security_token'] !== $_SESSION['token']) 
                    {
                        $this->error['message'] = 'Token Mismatch';
                        $this->error['error_code'] = ERROR;
                    }
                    else
                    {
                        $modal->update_student();

                        $this->error['message'] = $modal->message;
                        $this->error['error_code'] = $modal->flag;
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

                // update form after submit
                $student = $modal->check_student($_GET['id']);

                $this->data['fname'] = $student->name;
                $this->data['email'] = $student->email;
                $this->data['p_no'] = $student->primary_no;
                $this->data['s_no'] = $student->secondary_no;
                $this->data['reg_no'] = $student->reg_no;
                $this->data['roll_no'] = $student->roll_no;
                $this->data['meal_type'] = $student->meal_type;
                $this->data['room_no'] = $student->room_no;
                $this->data['bed_no'] = $student->bed_no;
                $this->data['rack_no'] = $student->rack_no;
                $this->data['status'] = $student->active;

                $this->view('header');
                $this->view('edit_student','admin');
                $this->view('footer');
            }
            else
            {
                header('HTTP/1.1 405 Requested Method Not Allowed');
                echo 'Invalid access! Go <a href="'.SITE_URL.'admin/students">Home</a>';
                die();
            }
        }
        else
        {
            header('HTTP/1.1 405 Requested Method Not Allowed');
            echo 'Invalid access! Go <a href="'.SITE_URL.'admin/students">Home</a>';
            die();
        }
    }

}
?>