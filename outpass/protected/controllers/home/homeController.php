<?php

class homeController extends Controller {
    
    private $model;
    private $pagination_limit = 10;
    private $pagination_buttons = array('Previous', 'Next');

    function __construct() {
        session::check('user_id', SITE_URL);
        if(!in_array(session::get('user_role'), array('SA','A','M')))
        {            
            session::clean_session(SITE_URL);
        }
        date_default_timezone_set('Asia/Kolkata');
    }
    

    function actionMain() {

        $this->data['internet'] = $this->is_connected();

        $this->data['total_students'] = count($this->model('user')->get_students());
        $this->data['total_issued'] = count($this->model('outpass')->get_issued());
        $this->data['total_returned'] = count($this->model('outpass')->get_returned());

        $this->data['today_issued'] = count($this->model('outpass')->get_issued_count());
        $this->data['today_returned'] = count($this->model('outpass')->get_returned_count());

        $this->data['month_issued'] = count($this->model('outpass')->get_issued_count('month'));
        $this->data['month_returned'] = count($this->model('outpass')->get_returned_count('month'));

        $this->data['page_title'] = 'Home | OUTPASS Management System';

        $this->view('header');
        $this->view('dashboard','home');
        $this->view('footer');
    }


    function actionView_students($page = 1)
    {
        $modal = $this->model('user');

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
            $this->data['pagination_link'] .= '<a href="'.SITE_URL.'home/view_students/'.($page - 1).'" class="previous">Previous</a>';
        }
        if($count > count($this->data['students']) * $page && count($this->data['students']) > 0)
        {
            $this->data['pagination_link'] .= '<a href="'.SITE_URL.'home/view_students/'.($page + 1).'" class="next">Next</a>';
        }

        $this->data['page_title'] = 'View Students | Outpass Management System';

        $this->view('header');
        $this->view('view_students','home');
        $this->view('footer');
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
            $this->data['pagination_link'] .= '<a href="'.SITE_URL.'home/view_users/'.($page - 1).'" class="previous">Previous</a>';
        }
        if($count > count($this->data['users']) * $page && count($this->data['users']) > 0)
        {
            $this->data['pagination_link'] .= '<a href="'.SITE_URL.'home/view_users/'.($page + 1).'" class="next">Next</a>';
        }

        $this->data['page_title'] = 'View Users | Outpass Management System';

        $this->view('header');
        $this->view('view_users','home');
        $this->view('footer');
    }

    function actionCreate_pass()
    {
        $model = $this->model('user');
        $outpass = $this->model('outpass');

        $dt = new DateTime();
        $this->data['curr_time'] = $dt->format('Y-m-d H:i:s');

        if(isset($_POST['search']))
        {
            session::clean_data('otp_data');
            if($_POST['security_token'] !== $_SESSION['token']) 
            {
                $this->error['message'] = 'Token Mismatch';
                $this->error['error_code'] = ERROR;
            }
            else
            {
                $data = $model->check_user();
                $this->error['message'] = $model->message;
                $this->error['error_code'] = $model->flag;

                if($data !== false)
                {
                    $this->data['stage'] = 'OTP';
                    $args = array(
                        'data' => '',
                        'otp' => '',
                        'count' => 0,
                        'user_id' => $data->reg_no
                        );
                    session::set('otp_data',$args);
                    $this->data['reg_no'] = session::get('otp_data')['user_id'];        
                }
            }
           
        }

        if(isset($_POST['otp']))
        {
            if($_POST['security_token'] !== $_SESSION['token']) 
            {
                $this->error['message'] = 'Token Mismatch';
                $this->error['error_code'] = ERROR;
            }
            else
            {
                $data = $outpass->process_send_otp();
                $this->data['stage'] = 'OTP';
                $this->data['reg_no'] = session::get('otp_data')['user_id'];  
                $this->error['message'] = $outpass->message;
                $this->error['error_code'] = $outpass->flag;

                if($outpass->flag == SUCCESS)
                {
                    $this->data['stage'] = 'CREATE';  
                }
            }
        }

        if(isset($_POST['create']))
        {
            if($_POST['security_token'] !== $_SESSION['token']) 
            {
                $this->error['message'] = 'Token Mismatch';
                $this->error['error_code'] = ERROR;
            }
            else
            {
                $otp = $outpass->check_otp();
                $this->data['stage'] = 'CREATE';
                $this->error['message'] = $outpass->message;
                $this->error['error_code'] = $outpass->flag;

                if($otp == true)
                {
                    session::clean_data('otp_data');
                    unset($this->data['stage']);
                }
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
        $this->view('create_pass','home');
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

        $this->data['issued'] = $modal->get_issued_pass($filter,$limit);
        $count = count($modal->get_issued_pass($filter));

        if($page > 1)
        {
            $this->data['pagination_link'] .= '<a href="'.SITE_URL.'home/issued/'.($page - 1).'" class="previous">Previous</a>';
        }
        if($count > count($this->data['issued']) * $page && count($this->data['issued']) > 0)
        {
            $this->data['pagination_link'] .= '<a href="'.SITE_URL.'home/issued/'.($page + 1).'" class="next">Next</a>';
        }

        $this->data['page_title'] = 'View Students | Outpass Management System';

        $this->view('header');
        $this->view('view_pass','home');
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
        }

        $this->data['pending'] = $modal->get_pending_pass($filter,$limit);
        $count = count($modal->get_pending_pass($filter));

        if($page > 1)
        {
            $this->data['pagination_link'] .= '<a href="'.SITE_URL.'home/pending/'.($page - 1).'" class="previous">Previous</a>';
        }
        if($count > count($this->data['pending']) * $page && count($this->data['pending']) > 0)
        {
            $this->data['pagination_link'] .= '<a href="'.SITE_URL.'home/pending/'.($page + 1).'" class="next">Next</a>';
        }

        $this->data['page_title'] = 'View Students | Outpass Management System';

        $this->view('header');
        $this->view('pending_pass','home');
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

        $this->view('approve_pass','home');
        $this->view('footer');
    }

    function actionReturn()
    {
        $modal = $this->model('outpass');
        $this->view('header');
        $this->view('return','home');

        if(isset($_POST['return']))
        {            
            $this->data['pass'] = $modal->get_outpass_by_id();
            $this->error['message'] = $modal->message;
            $this->error['error_code'] = $modal->flag;
            $this->view('user','home');
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

    function actionBulk_Pass($page = 1)
    {
        $modal = $this->model('outpass');

        if(isset($_POST['submit']))
        {
            $modal->bulk_issue();
            $this->error['error_code'] = $modal->flag;
            $this->error['message'] = $modal->message;

        }

        $modal = $this->model('user');
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
            $this->data['pagination_link'] .= '<a href="'.SITE_URL.'home/bulk_pass/'.($page - 1).'" class="previous">Previous</a>';
        }
        if($count > count($this->data['students']) * $page && count($this->data['students']) > 0)
        {
            $this->data['pagination_link'] .= '<a href="'.SITE_URL.'home/bulk_pass/'.($page + 1).'" class="next">Next</a>';
        }

        $this->data['page_title'] = 'View Students | Outpass Management System';

        $this->view('header');
        $this->view('issue_bulk','home');
        $this->view('footer');
    }


    function actionSettings() {

        $model = $this->model('login');
        if(isset($_POST['change_personal']))
        {
            if($_POST['security_token'] !== $_SESSION['token']) 
            {
                $this->error['message'] = 'Token Mismatch';
                $this->error['error_code'] = ERROR;
            }
            else
            {
                $result = $model->process_setting();

                $this->error['error_code'] = $model->flag;
                $this->error['message'] = $model->message;
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

        $this->data['settings'] = $model->get_settings();

        $this->view('header');
        $this->view('settings');
        $this->view('footer');
    }


    function actionChange_password() {

        $model = $this->model('login');
        if(isset($_POST['change_pass']))
        {
            if($_POST['security_token'] !== $_SESSION['token']) 
            {
                $this->error['message'] = 'Token Mismatch';
                $this->error['error_code'] = ERROR;
            }
            else
            {
                
                $result = $model->process_password();

                $this->error['error_code'] = $model->flag;
                $this->error['message'] = $model->message;
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

        $this->data['settings'] = $model->get_settings();

        $this->view('header');
        $this->view('change_password');
        $this->view('footer');
    }

    private function is_connected()
    {
        $connected = @fsockopen("www.operce.com", 80); 
                                            //website, port  (try 80 or 443)
        if ($connected){
            $is_conn = true; //action when connected
            fclose($connected);
        }else{
            $is_conn = false; //action in connection failure
        }
        return $is_conn;

    }

    function actionLatecomer($page = 1)
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

        $this->data['issued'] = $modal->get_late_comer($filter,$limit);
        $count = count($modal->get_late_comer($filter));

        if($page > 1)
        {
            $this->data['pagination_link'] .= '<a href="'.SITE_URL.'home/latecomer/'.($page - 1).'" class="previous">Previous</a>';
        }
        if($count > count($this->data['issued']) * $page && count($this->data['issued']) > 0)
        {
            $this->data['pagination_link'] .= '<a href="'.SITE_URL.'home/latecomer/'.($page + 1).'" class="next">Next</a>';
        }

        $this->data['page_title'] = 'View Students | Outpass Management System';

        $this->view('header');
        $this->view('late_comers','home');
        $this->view('footer');
    }

    function actionNoncomer($page = 1)
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

        $this->data['issued'] = $modal->get_non_comer($filter,$limit);
        $count = count($modal->get_non_comer($filter));

        if($page > 1)
        {
            $this->data['pagination_link'] .= '<a href="'.SITE_URL.'home/noncomer/'.($page - 1).'" class="previous">Previous</a>';
        }
        if($count > count($this->data['issued']) * $page && count($this->data['issued']) > 0)
        {
            $this->data['pagination_link'] .= '<a href="'.SITE_URL.'home/noncomer/'.($page + 1).'" class="next">Next</a>';
        }

        $this->data['page_title'] = 'View Students | Outpass Management System';

        $this->view('header');
        $this->view('non_comers','home');
        $this->view('footer');
    }

    function actionReports()
    {

        $this->data['internet'] = $this->is_connected();

        $this->data['total_students'] = count($this->model('user')->get_students());
        $this->data['total_issued'] = count($this->model('outpass')->get_issued());
        $this->data['total_returned'] = count($this->model('outpass')->get_returned());

        $this->data['today_issued'] = count($this->model('outpass')->get_issued_count());
        $this->data['today_returned'] = count($this->model('outpass')->get_returned_count());
        $this->data['today_pending'] = count($this->model('outpass')->get_pending_count());
        $this->data['today_late_not_returned'] = count($this->model('outpass')->get_late_count('today', 'ISSUED'));
        $this->data['today_late_returned'] = count($this->model('outpass')->get_late_count('today', 'COMPLETE'));

        $this->data['month_issued'] = count($this->model('outpass')->get_issued_count('month'));
        $this->data['month_returned'] = count($this->model('outpass')->get_returned_count('month'));
        $this->data['month_pending'] = count($this->model('outpass')->get_pending_count('month'));
        $this->data['month_late_not_returned'] = count($this->model('outpass')->get_late_count('month', 'ISSUED'));
        $this->data['month_late_returned'] = count($this->model('outpass')->get_late_count('month', 'COMPLETE'));

        $this->data['page_title'] = 'Reports | OUTPASS Management System';

        $this->view('header');
        $this->view('reports','home');
        $this->view('footer');

    }

    function actionUserhistory()
    {
        $modal = $this->model('outpass');
        $this->view('header');
        $this->view('reports_userdetails','home');

        if(isset($_POST['submit']))
        {
            $filter = array();

            if(isset($_POST['reg_no_filter']) && $_POST['reg_no_filter'] != '')
            {
                $filter['reg_no'] = $_POST['reg_no_filter'];
            }

            if(isset($_POST['roll_no_filter']) && $_POST['roll_no_filter'] != '')
            {
                $filter['roll_no'] = $_POST['roll_no_filter'];
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

            if(isset($_POST['rack_no_filter']) && $_POST['rack_no_filter'] != '')
            {
                $filter['rack_no'] = $_POST['rack_no_filter'];
            }

            if(isset($_POST['type_filter']) && $_POST['type_filter'] != '')
            {
                $filter['type'] = $_POST['type_filter'];
            }
            if(isset($_POST['from_filter']) && $_POST['from_filter'] != '')
            {
                $filter['from'] = $_POST['from_filter'];
            }
            if(isset($_POST['to_filter']) && $_POST['to_filter'] != '')
            {
                $filter['to'] = $_POST['to_filter'];
            }

            $this->data['history'] = $modal->get_outpass_by_details($filter);
            $this->error['message'] = $modal->message;
            $this->error['error_code'] = $modal->flag;
            $this->view('reports_userhistory','home');
        }

        $this->view('footer');
    }

}
?>