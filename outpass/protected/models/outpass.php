<?php


/**
* 
*/
class outpass extends Model
{
	public $error_codes = array();
	private $crud;
    public $message;
    public $flag;

	function __construct()
	{
        global $api_error_codes;
		$this->error_codes = $api_error_codes;

		$this->crud = new crud('outpass');
	}

    function get_outpass_by_id()
    {
        $query = "select outpass.out_time, outpass.expected_time, outpass.id, outpass.date, students.* from outpass inner join students on students.stu_id = outpass.user_id where outpass.status = 'ISSUED' AND (outpass.user_id = '".$_POST['user_id'] ."' OR students.reg_no = '".$_POST['user_id']."' OR students.roll_no = '".$_POST['user_id']."')";

        $data = $this->crud->exec_query($query)->fetchAll(PDO::FETCH_CLASS);

        if(count($data) <= 0)
        {

            $this->flag = WARNING;
            $this->message = "No outpass issued to the user.";
        }
        
        return $data;
    }

    function get_outpass_by_details($filter = array())
    {
        $query = "select outpass.*, outpass.date as issued_date, students.name, students.reg_no from outpass inner join students on outpass.user_id = students.stu_id";
        $flag = false;

        if(isset($filter['reg_no']))
        {
            if(!$this->check_form($filter, ['reg_no'=>'Reg no'])) {
                $this->flag = ERROR;
                return false;
            }
            $query = ($flag == true) ? $query . " AND students.reg_no = '".$filter['reg_no']."'" : $query . " where students.reg_no = '".$filter['reg_no']."'" ;
            if($flag == false) $flag = true;
        }
        if(isset($filter['meal_type']))
        {
            if(!$this->check_form($filter, ['meal_type'=>'Meal Type'])) {
                $this->flag = ERROR;
                return false;
            }
            $query = ($flag == true) ? $query . " AND students.meal_type = '".$filter['meal_type']."'" : $query . " where students.meal_type = '".$filter['meal_type']."'";
            if($flag == false) $flag = true;
        }
        if(isset($filter['room_no']))
        {
            if(!$this->check_form($filter, ['room_no' => 'Room no'])) {
                $this->flag = ERROR;
                return false;
            }
            $query = ($flag == true) ? $query . " AND students.room_no = '".$filter['room_no']."'" : $query . " where students.room_no = '".$filter['room_no']."'";
            if($flag == false) $flag = true;
        }
        if(isset($filter['bed_no']))
        {
            if(!$this->check_form($filter, ['bed_no'=>'Bed no'])) {
                $this->flag = ERROR;
                return false;
            }
            $query = ($flag == true) ? $query . " AND students.bed_no = '".$filter['bed_no']."'" : $query . " where students.bed_no = '".$filter['bed_no']."'" ;
            if($flag == false) $flag = true;
        }
        if(isset($filter['from']) && isset($filter['to']))
        {
            $filter['from'] =  date("Y-m-d", strtotime($filter['from']));
            $filter['to'] =  date("Y-m-d", strtotime($filter['to']));

            $query =  $query . " AND outpass.date between '".$filter['from']."' AND '".$filter['to']."'" ;
            if($flag == false) $flag = true;
        }

        if($flag == true)
        {
            $data = $this->crud->exec_query($query)->fetchAll(PDO::FETCH_CLASS);
            return $data;
        }
        else
        {
            return array();
        }

    }

    function process_return_outpass()
    {
        $this->values = array('id' => $_GET['id']);
        if(!$this->check_form($this->values, ['id' => 'Book'])) {
            $this->flag = ERROR;
            $this->message = "Invalid request";
            return false;
        }

        $data = $this->crud->get_by(array('id' => $this->values['id'], 'status' => 'ISSUED'), '=', true);

        if(count($data) <= 0)
        {
            $this->message = 'Invalid user details';
            $this->flag = ERROR;
            return true;
        }

        $this->crud->changeTable('students');
        $check_user = $this->crud->get_by(array('stu_id' => $data->user_id), '=', true);
        $this->crud->changeTable('outpass');

        if(count($check_user) < 0)
        {
            $this->message = 'Invalid user details';
            $this->flag = ERROR;
            return true;
        }

        $number = $check_user->primary_no;
        $name = $check_user->name;

        $message = 'Dear Parent, Your son/daughter named '.$name.' has returned to the campus. The time of arrival is : '.(new \DateTime())->format(DATE_FORMAT).'. Expected time of arrival was : '.$data->expected_time;

        $url = API_URL.'?apikey='.API_KEY.'&sender='.SENDER_ID.'&number='.$number.'&message='.urlencode($message);

        $result = $this->curl_request($url);
        //die(var_dump($result));

        $result = $this->check_error($result);

        if(is_array($result))
        {
            $this->message = 'Error sending message';
            $this->flag = ERROR;
            return false;
        }
        else
        {

            $data = $this->crud->update( array('status' => 'COMPLETE', 'in_time' => (new \DateTime())->format(DATE_FORMAT)), array( 'id' => $this->values['id'] ));

            if($data > 0)
            {
                $this->flag = SUCCESS;
                $this->message = ' Updated Successfully';
                return true;
            }
            else
            {
                $this->flag = WARNING;
                $this->message = "Nothing was updated";
                return true;
            }
        }
    }


    function process_bypass_approval()
    {
        $this->values = $_POST;
        if(!$this->check_form($this->values, ['user_id' => 'User ID', 'in_time'=>'Expected In Time'])) {
            $this->flag = ERROR;
            return false;
        }

        $this->crud->changeTable('students');
        $query = 'select stu_id from students where roll_no = \''.$this->values['user_id'].'\' OR reg_no = \''.$this->values['user_id'].'\''; 
        $check_user = $this->crud->exec_query($query)->fetch(PDO::FETCH_OBJ);


        if(count($check_user) <= 0)
        {
            $this->message = 'Invalid user details';
            $this->flag = ERROR;
            return false;
        }
        $this->crud->changeTable('outpass');
        $check_prev = $this->crud->get_by(array('user_id' => $check_user->stu_id, 'status' => 'PENDING'), '=', true);

        if(count($check_prev) <= 0)
        {
            $this->message = 'Invalid user details';
            $this->flag = ERROR;
            return false;
        }

        $out_time = strtotime($check_prev->out_time);
        $in_time = strtotime($this->values['in_time']);

        if ($in_time < $out_time)
        {
            $this->flag = ERROR;
            $this->message = 'Valid expected IN time is required.';
            return false;
        }

        $this->crud->changeTable("outpass");
        $args = array('returned_by' => session::get('user_id'), 'expected_time'=>$this->values['in_time'], 'status' => 'ISSUED', 'date' => (new \DateTime())->format(DATE_FORMAT));
        
        try
        {
            $data = $this->crud->update( $args, array('user_id' => $check_user->stu_id) );
    
            if($data > 0)
            {
                $this->flag = SUCCESS;
                $this->message = 'Pass created';
                return true;
            }
            else
            {
                $this->flag = ERROR;
                $this->message = "Some error occured while creating Out Pass.";
                return true;
            }
        }
        catch(Exception $e)
        {
            $this->flag = ERROR;
            $this->message = 'Some error occured while creating Out Pass. '.$e->getMessage();
            return true;
        }
    }


    function process_approve()
    {
        $this->values = $_POST;
        if(!$this->check_form($this->values, ['user_id' => 'User ID', 'otp'=>'OTP', 'in_time'=>'Expected In Time'])) {
            $this->flag = ERROR;
            return false;
        }

        $this->crud->changeTable('students');
        $query = 'select stu_id from students where roll_no = \''.$this->values['user_id'].'\' OR reg_no = \''.$this->values['user_id'].'\''; 
        $check_user = $this->crud->exec_query($query)->fetch(PDO::FETCH_OBJ);


        if(count($check_user) <= 0)
        {
            $this->message = 'Invalid user details';
            $this->flag = ERROR;
            return false;
        }
        $this->crud->changeTable('outpass');
        $check_prev = $this->crud->get_by(array('user_id' => $check_user->stu_id, 'status' => 'PENDING', 'otp' => $this->values['otp']), '=', true);

        if(count($check_prev) <= 0)
        {
            $this->message = 'Invalid user details';
            $this->flag = ERROR;
            return false;
        }

        $out_time = strtotime($check_prev->out_time);
        $in_time = strtotime($this->values['in_time']);

        if ($in_time < $out_time)
        {
            $this->flag = ERROR;
            $this->message = 'Valid expected IN time is required.';
            return false;
        }

        $this->crud->changeTable("outpass");
        $args = array('returned_by' => session::get('user_id'), 'expected_time'=>$this->values['in_time'], 'status' => 'ISSUED', 'date' => (new \DateTime())->format(DATE_FORMAT));
        
        try
        {
            $data = $this->crud->update( $args, array('user_id' => $check_user->stu_id) );
    
            if($data > 0)
            {
                $this->flag = SUCCESS;
                $this->message = 'Pass created';
                return true;
            }
            else
            {
                $this->flag = ERROR;
                $this->message = "Some error occured while creating Out Pass.";
                return true;
            }
        }
        catch(Exception $e)
        {
            $this->flag = ERROR;
            $this->message = 'Some error occured while creating Out Pass. '.$e->getMessage();
            return true;
        }
    }

    function bulk_issue() {
        $count = 0;

        if(isset($_POST['item']) && is_array($_POST['item']))
        foreach ($_POST['item'] as $key => $value) {

            if(!$this->check_data($value, 'int')) {
                $this->flag = ERROR;
                $this->message = "Invalid request";
                return false;
            }

            $con = $this->crud->get_connection();

            $con->beginTransaction();

            $date = (new DateTime())->format('Y-m-d H:i:s');

            try
            {

                $this->crud->changeTable('students');
                $check_user = $this->crud->get_by(array('stu_id' => $value, 'active' => true), '=', true);

                if(count($check_user) <= 0)
                {
                    throw new Exception('Invalid user details. ID - '.$value);
                }

                $this->crud->changeTable('outpass');
                $query = "select user_id from outpass where user_id = ".$check_user->stu_id." AND (status = 'PENDING' OR status = 'ISSUED')";
                $check_prev = $this->crud->exec_query($query)->fetchAll(PDO::FETCH_CLASS);


                if(count($check_prev) > 0)
                {
                    throw new Exception('Pass already issued to the user : '. $check_user->name);
                }

                $number = $check_user->primary_no;
                $name = $check_user->name;

                $otp = $this->otp_generator();
                $message = 'Dear Parent, Your son/daughter named '.$name.' has requested a pass to go out of the campus. Kindly provide this OPT < '.$otp.' > to issue the pass.';

                $url = API_URL.'?apikey='.API_KEY.'&sender='.SENDER_ID.'&number='.$number.'&message='.urlencode($message);

                $result = $this->curl_request($url);
                //die(var_dump($result));

                $result = $this->check_error($result);

                if(is_array($result))
                {
                    throw new Exception('Error sending OTP. Error message returned: '.$result[1]);
                }
                else
                {

                    $this->crud->changeTable('outpass');
                    $data = $this->crud->insert( array('user_id' => $value, 'issued_by' => session::get('user_id'), 'returned_by' => 0,  'status' => 'PENDING', 'out_time' => $date, 'date' => $date, 'otp' => $otp) );

                    $con->commit();
                    if($data > 0)
                    {
                        $count++;
                        $this->message = 'Passes issued to '.$count.' users';
                        $this->flag = SUCCESS;
                    }
                    else
                    {
                        $this->message = 'Passes issued to only '.$count.' users';
                        $this->flag = ERROR;
                    }
                }
            }
            catch(Exception $e)
            {
                $con->rollBack();
                $this->flag = ERROR;
                $this->message = "Error : ". $e->getMessage();
            }
        }
    }

    private function create_pass() {
        $this->values = session::get('otp_data');
        if(!isset($this->values['data']))
        {  
            $this->message = 'Server Error. Please try again';
            $this->flag = ERROR;
            return true;
        }

        $this->values = $this->values['data'];
        if(!$this->check_form($this->values, ['reg_no' => $this->values['reg_no'],'out_time'=>'Out Time', 'in_time'=>'Expected In Time'], '{in_time}')) {
            $this->flag = ERROR;
            return false;
        }


        $dt = new DateTime();
        $date = $dt->format('Y-m-d H:i:s');

        $this->crud->changeTable('students');
        $check_user = $this->crud->get_by(array('reg_no' => $this->values['reg_no']), '=', true);

        if(count($check_user) < 0)
        {
            $this->message = 'Invalid user details'.
            $this->flag = ERROR;
            return true;
        }

        $this->crud->changeTable("outpass");
        $args = array('issued_by' => session::get('user_id'), 'returned_by' => 0, 'out_time'=>$this->values['out_time'], 'expected_time'=>$this->values['in_time'], 'status' => 'PENDING', 'user_id' => $check_user->stu_id, 'date' => $date);
        
        try
        {
            $data = $this->crud->insert( $args );
    
            if($data > 0)
            {
                $this->flag = SUCCESS;
                $this->message = 'Pass created';
                return true;
            }
            else
            {
                $this->flag = ERROR;
                $this->message = "Some error occured while creating Out Pass.";
                return true;
            }
        }
        catch(Exception $e)
        {
            $this->flag = ERROR;
            $this->message = 'Some error occured while creating Out Pass. '.$e->getMessage();
            return true;
        }
        
    }

    function check_otp()
    {
        if(session::get('otp_data') != null)
        {
            $this->values = $_POST;
            if(!$this->check_form($this->values, ['otp'=>'OTP'])) {
                $this->flag = ERROR;
                return false;
            }

            $data = session::get('otp_data');
            if($data['count'] > 5)
            {
                $this->message = 'OTP Expired. Please try again.';
                $this->flag = ERROR;
                return true;
            }
            if($this->values['otp'] == session::get('otp_data')['otp'])
            {
                $this->create_pass();
            }
            else
            {
                $this->message = 'Invalid OTP. Please try again';
                $this->flag = ERROR;
                return false;
            }
            $data['count']++;
        }
        else
        {
            $this->message = 'No OTP has been registered.';
            $this->flag = ERROR;
            return true;
        }
    }

    function process_send_otp()
    {
        $this->values = $_POST;
        if(!$this->check_form($this->values, ['reg_no' => 'Registration no', 'out_time'=>'Out Time', 'in_time'=>'Expected In Time'], '{in_time}')) {
            $this->flag = ERROR;
            return false;
        }

        $this->crud->changeTable('students');
        $check_user = $this->crud->get_by(array('reg_no' => $this->values['reg_no']), '=', true);

        $this->crud->changeTable('outpass');
        $check_prev = $this->crud->get_by(array('user_id' => $check_user->stu_id, 'status' => 'PENDING'), '=', true);


        if(count($check_user) < 0)
        {
            $this->message = 'Invalid user details';
            $this->flag = ERROR;
            return false;
        }

        if(count($check_prev) > 0)
        {
            $this->message = 'Pass already issued to the user';
            $this->flag = ERROR;
            return false;
        }

        $out_time = strtotime($this->values['out_time']);
        $in_time = strtotime($this->values['in_time']);

        if ($in_time < $out_time)
        {
            $this->flag = ERROR;
            $this->message = 'Valid expected IN time is required.';
            return false;
        }

        $this->values['primary_no'] = $check_user->primary_no;
        $this->values['name'] = $check_user->name;

        $this->send_otp();
    }

    function send_otp()
    {

        $number = $this->values['primary_no'];
        $name = $this->values['name'];

        $otp = $this->otp_generator();
        $message = 'Dear Parent, Your son/daughter named '.$name.' has requested a pass to go out of the campus. Kindly provide this OPT < '.$otp.' > to issue the pass.';

        $url = API_URL.'?apikey='.API_KEY.'&sender='.SENDER_ID.'&number='.$number.'&message='.urlencode($message);

        $result = $this->curl_request($url);
        //die(var_dump($result));

        $result = $this->check_error($result);

        if(is_array($result))
        {
            $this->message = 'Error sending OTP. Error message returned: '.$result[1];
            $this->flag = ERROR;
            return false;
        }
        else
        {
            $this->message ='OTP sent to the registered mobile number.';
            $this->flag = SUCCESS;
            session::clean_data('otp_data');

            $args = array(
                'data' => $this->values,
                'otp' => $otp,
                'count' => 0,
                'user_id' => $this->values['reg_no'] 
                );
            session::set('otp_data', $args);
            return true;
        }        

    }

    function check_error($string)
    {
    	foreach ($this->error_codes as $key => $value) {

    		if (strpos($string, $key) !== false) {
    			return array(
    					$key, $value
    				);
    		}
    	}
    	return false;
    }

    function get_issued_pass($filter = array(), $limit = false)
    {
        
        $query = 'select students.name, students.*, outpass.out_time, outpass.in_time, outpass.expected_time, outpass.date as issued_date, outpass.status, outpass.issued_by, outpass.returned_by from outpass inner join students on students.stu_id = outpass.user_id where (status = \'ISSUED\' || status = \'COMPLETE\')';
        
        $flag = true;

        if(isset($filter['from']) && isset($filter['to']))
        {
            $filter['from'] =  date("Y-m-d", strtotime($filter['from']));
            $filter['to'] =  date("Y-m-d", strtotime($filter['to']));

            $query =  $query . " AND outpass.date between '".$filter['from']."' AND '".$filter['to']."'" ;
            if($flag == false) $flag = true;
        }
        if(isset($filter['type']))
        {
            $query = ($flag == true) ? $query . " AND outpass.status = '".$filter['type']."'" : $query . " where outpass.status = '".$filter['type']."'" ;
            if($flag == false) $flag = true;
        }
        
        if($limit != false)
        {
            $query = $query.' '.$limit;
        }

        $data = $this->crud->exec_query($query)->fetchAll(PDO::FETCH_CLASS);
        return $data;
        
    }

    function get_pending_pass($filter = array(), $limit = false)
    {
        
        $query = 'select students.name, students.*, outpass.out_time, outpass.in_time, outpass.expected_time, outpass.date as issued_date, outpass.status,  outpass.issued_by, outpass.returned_by from outpass inner join students on students.stu_id = outpass.user_id where status = \'PENDING\'';
        
        $flag = true;

        if(isset($filter['from']) && isset($filter['to']))
        {
            $filter['from'] =  date("Y-m-d", strtotime($filter['from']));
            $filter['to'] =  date("Y-m-d", strtotime($filter['to']));

            $query =  $query . " AND outpass.date between '".$filter['from']."' AND '".$filter['to']."'" ;
            if($flag == false) $flag = true;
        }
        
        if($limit != false)
        {
            $query = $query.' '.$limit;
        }

        $data = $this->crud->exec_query($query)->fetchAll(PDO::FETCH_CLASS);
        return $data;
        
    }

    function get_issued()
    {        
        $query = 'select students.name, students.*, outpass.out_time, outpass.in_time, outpass.expected_time, outpass.date as issued_date, outpass.issued_by, outpass.returned_by from outpass inner join students on students.stu_id = outpass.user_id';        
        $query = $query." where outpass.status = 'PENDING'";    

        $data = $this->crud->exec_query($query)->fetchAll(PDO::FETCH_CLASS);
        return $data;
    }

    function get_returned()
    {        
        $query = 'select students.name, students.*, outpass.out_time, outpass.in_time, outpass.expected_time, outpass.date as issued_date,  outpass.issued_by, outpass.returned_by from outpass inner join students on students.stu_id = outpass.user_id';        
        $query = $query." where outpass.status = 'COMPLETE'";    

        $data = $this->crud->exec_query($query)->fetchAll(PDO::FETCH_CLASS);
        return $data;
    }

    function get_issued_count($args = 'today') {

        if($args == 'today')
        {
            $query = 'SELECT * FROM outpass WHERE status = \'ISSUED\' and date >= CURDATE()';
            $flag = false;
        }
        elseif($args == 'month')
        {
            $query = 'SELECT * FROM outpass WHERE status = \'ISSUED\' and MONTH(date) = MONTH(CURDATE())';
            $flag = false;
        }
        elseif($args == 'year')
        {
            $query = 'SELECT * FROM outpass WHERE status = \'ISSUED\' and YEAR(date) = YEAR(CURDATE())';
            $flag = false;
        }
        
        $data = $this->crud->exec_query($query)->fetchAll(PDO::FETCH_CLASS);
        return $data;
    }

    function get_late_count($args = 'today', $type = 'ISSUED') {

        if($args == 'today')
        {
            $query = 'SELECT id FROM outpass WHERE status = \''.$type.'\' and date >= CURDATE() and in_time > expected_time';
            $flag = false;
        }
        elseif($args == 'month')
        {
            $query = 'SELECT id FROM outpass WHERE status = \''.$type.'\' and MONTH(date) = MONTH(CURDATE()) and in_time > expected_time';
            $flag = false;
        }
        elseif($args == 'year')
        {
            $query = 'SELECT id FROM outpass WHERE status = \''.$type.'\' and YEAR(date) = YEAR(CURDATE()) and in_time > expected_time';
            $flag = false;
        }
        
        $data = $this->crud->exec_query($query)->fetchAll(PDO::FETCH_CLASS);
        return $data;
    }

    function get_returned_count($args = 'today') {

        if($args == 'today')
        {
            $query = 'SELECT * FROM outpass WHERE status = \'COMPLETE\' and date >= CURDATE()';
            $flag = false;
        }
        elseif($args == 'month')
        {
            $query = 'SELECT * FROM outpass WHERE status = \'COMPLETE\' and MONTH(date) = MONTH(CURDATE())';
            $flag = false;
        }
        elseif($args == 'year')
        {
            $query = 'SELECT * FROM outpass WHERE status = \'COMPLETE\' and YEAR(date) = YEAR(CURDATE())';
            $flag = false;
        }
        
        $data = $this->crud->exec_query($query)->fetchAll(PDO::FETCH_CLASS);
        return $data;
    }

    function get_pending_count($args = 'today') {

        if($args == 'today')
        {
            $query = 'SELECT * FROM outpass WHERE status = \'PENDING\' and date >= CURDATE()';
            $flag = false;
        }
        elseif($args == 'month')
        {
            $query = 'SELECT * FROM outpass WHERE status = \'PENDING\' and MONTH(date) = MONTH(CURDATE())';
            $flag = false;
        }
        elseif($args == 'year')
        {
            $query = 'SELECT * FROM outpass WHERE status = \'PENDING\' and YEAR(date) = YEAR(CURDATE())';
            $flag = false;
        }
        
        $data = $this->crud->exec_query($query)->fetchAll(PDO::FETCH_CLASS);
        return $data;
    }

    function curl_request($Url){
      // is cURL installed yet?
      if (!function_exists('curl_init')){
        die('Sorry cURL is not installed!');
      }
     
       $curl = curl_init();
       //Set the URL that you want to GET by using the CURLOPT_URL option.
        curl_setopt($curl, CURLOPT_URL, $Url);
         
        //Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
         
        //Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp;
    }

    function get_late_comer($filter = array(), $limit = false)
    {
        
        $query = 'select students.name, students.*, outpass.out_time, outpass.in_time, outpass.expected_time, outpass.date as issued_date, outpass.status, outpass.issued_by, outpass.returned_by from outpass inner join students on students.stu_id = outpass.user_id where (status = \'ISSUED\' || status = \'COMPLETE\') AND outpass.in_time > outpass.expected_time';
        
        $flag = true;

        if(isset($filter['from']) && isset($filter['to']))
        {
            $filter['from'] =  date("Y-m-d", strtotime($filter['from']));
            $filter['to'] =  date("Y-m-d", strtotime($filter['to']));

            $query =  $query . " AND outpass.date between '".$filter['from']."' AND '".$filter['to']."'" ;
            if($flag == false) $flag = true;
        }
        
        if($limit != false)
        {
            $query = $query.' '.$limit;
        }

        $data = $this->crud->exec_query($query)->fetchAll(PDO::FETCH_CLASS);
        return $data;
        
    }

    function get_non_comer($filter = array(), $limit = false)
    {
        
        $query = 'select students.name, students.*, outpass.out_time, outpass.in_time, outpass.expected_time, outpass.date as issued_date, outpass.status, outpass.issued_by, outpass.returned_by from outpass inner join students on students.stu_id = outpass.user_id where (status = \'ISSUED\')';
        
        $flag = true;

        if(isset($filter['from']) && isset($filter['to']))
        {
            $filter['from'] =  date("Y-m-d", strtotime($filter['from']));
            $filter['to'] =  date("Y-m-d", strtotime($filter['to']));

            $query =  $query . " AND outpass.date between '".$filter['from']."' AND '".$filter['to']."'" ;
            if($flag == false) $flag = true;
        }
        
        if($limit != false)
        {
            $query = $query.' '.$limit;
        }

        $data = $this->crud->exec_query($query)->fetchAll(PDO::FETCH_CLASS);
        return $data;
        
    }
}