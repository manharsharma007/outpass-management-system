<?php
/**
 * File contains necessary information that defines the bootstrap of your application
 * Source code pattern must not be modified
 * Files to be used with the comments.
 * @Author : Operce Technologies
 * @Developer : Manhar Sharma
 * @Year : 2016
 *
 *
 **/


/**
 * class for login
 * pattern must not be modified
 * Files to be used with the comments.
 *
 *
 */

class login extends Model {
	private $crud;
    public $message;
    public $flag;

	function __construct() {
		$this->crud = new crud('login');
	}

	function process_login() {
		$this->values = $_POST;
		if(!$this->check_form($this->values, ['username'=>'Username','password'=>'Password'])) {
			$this->flag = ERROR;
			return false;
		}

		$this->values['password'] = sha1(AUTH_KEY.$this->values['password']);
		$this->login();
	}

	private function login() {
		$data = $this->crud->get_by( array('user_name' => $this->values['username'], 'user_pass' => $this->values['password'], 'active' => 1), "=", true );
		
		if(count($data) > 0)
		{
            session::set('user_id', $data->user_id);
            session::set('user_role', $data->user_type);
		}
		else
		{
			$this->flag = ERROR;
			$this->message = "Incorrect username/password";
			return true;
		}
		
	}



    function process_user() {
        $this->values = $_POST;
        if(!$this->check_form($this->values, ['fname'=>'Name', 'email'=>'Email', 'user_name'=>'Username', 'pass'=>'Password', 'user_type' => 'User Type', 'active' => 'Active'])) {
            $this->flag = ERROR;
            return false;
        }

        if($this->check_data($this->values['email'], 'email') == false)
        {
            $this->flag = ERROR;
            $this->message = 'Valid email is required';
            return false;
        }

        if($this->values['active'] == 'yes')
        {
            $this->values['active'] = 1;
        }
        else if($this->values['active'] == 'no')
        {
            $this->values['active'] = 0;
        }
        else
        {
            $this->flag = ERROR;
            $this->message = 'Active status is required';
            return false;
        }

        if($this->values['user_type'] == 'super_user')
        {
            $this->values['user_type'] = 'SA';
        }
        else if($this->values['user_type'] == 'admin')
        {
            $this->values['user_type'] = 'A';
        }
        else if($this->values['user_type'] == 'manager')
        {
            $this->values['user_type'] = 'M';
        }
        else
        {
            $this->flag = ERROR;
            $this->message = 'Valid User Type is required';
            return false;
        }

        if($this->values['pass'] != $this->values['confirm_pass'])
        {
            $this->flag = ERROR;
            $this->message = 'Password donot match';
            return false;
        }

        $this->add_user();
    }

    function get_users($filter = array(), $limit = false) {

        $query = 'select * from login';
        $flag = false;

        if(isset($filter['name']))
        {
            if(!$this->check_form($filter, ['name'=>'Name'])) {
                $this->flag = ERROR;
                return false;
            }
            $query = ($flag == true) ? $query . " AND full_name like '%".$filter['name']."%'" : $query . " where full_name like '%".$filter['name']."%'" ;
            if($flag == false) $flag = true;
        }
        if(isset($filter['email']))
        {
            if(!$this->check_form($filter, ['email'=>'Email'])) {
                $this->flag = ERROR;
                return false;
            }
            $query = ($flag == true) ? $query . " AND user_email = '".$filter['email']."'" : $query . " where user_email = '".$filter['email']."'";
            if($flag == false) $flag = true;
        }
        if(isset($filter['username']))
        {
            if(!$this->check_form($filter, ['username' => 'Username'])) {
                $this->flag = ERROR;
                return false;
            }
            $query = ($flag == true) ? $query . " AND user_name = '".$filter['username']."'" : $query . " where user_name = '".$filter['username']."'";
            if($flag == false) $flag = true;
        }
        if(isset($filter['user_role']))
        {
            if(!$this->check_form($filter, ['user_role'=>'User Role'])) {
                $this->flag = ERROR;
                return false;
            }
            $query = ($flag == true) ? $query . " AND user_type = '".$filter['user_role']."'" : $query . " where user_type = '".$filter['user_role']."'";
            if($flag == false) $flag = true;
        }
        
        if($limit != false)
        {
            $query = $query.' '.$limit;
        }
        
        $data = $this->crud->exec_query($query)->fetchAll(PDO::FETCH_CLASS);
        return $data;
    }


    private function add_user() {
        $dt = new DateTime();
        $date = $dt->format('Y-m-d H:i:s');

        $this->values['pass'] = sha1(AUTH_KEY.$this->values['pass']);

        $args = array('full_name'=>$this->values['fname'], 'user_email'=>$this->values['email'], 'user_pass' => $this->values['pass'], 'user_name'=>$this->values['user_name'], 'active' => $this->values['active'], 'user_type' => $this->values['user_type'], 'date' => $date);
        
        try
        {
            $data = $this->crud->insert( $args );
    
            if($data > 0)
            {
                $this->flag = SUCCESS;
                $this->message = 'User Added';
                return true;
            }
            else
            {
                $this->flag = ERROR;
                $this->message = "Some error occured while adding user.";
                return true;
            }
        }
        catch(Exception $e)
        {
            $this->flag = ERROR;
            $this->message = 'Some error occured while adding user. '.$e->getMessage();
            return true;
        }
        
    }


	function process_forgot() {
		$this->values = $_POST;
			if(!$this->check_form($this->values, ['email'=>'Email'])) {
				$this->flag = WARNING;
				return false;
			}

			if(!$this->check_data($this->values['email'], 'email')) {
				$this->flag = WARNING;
				$this->message = 'Valid email is required';
				return false;
			}
			$this->reset();
	}


	private function reset() {
		
		$this->values['pass_mail'] = $this->pass_generator();
		$this->values['pass'] = sha1(AUTH_KEY.$this->values['pass_mail']);



		$query = "select * from login where user_email = :email";
		$data = $this->crud->exec_query($query, array(':email' => $this->values['email']))->fetch(PDO::FETCH_ASSOC);
		
		if($data)
		{
			$query2 ="update login set user_pass = :pass where user_email = :email";
			$result = $this->crud->exec_query($query2, array(':email' => $this->values['email'], ':pass' => $this->values['pass']));

			if($result) {
				if ($this->send_mail($this->values['email'], 'Password reset', 'Your new Password is : '.$this->values["pass_mail"])) 
					{
                        // when mail has been send successfully
                        $this->flag = SUCCESS;
						$this->message = 'New Password has been sent through mail';
						return true;
                    }
                    else {
                    	 
		                  $this->flag = ERROR;
		                  $this->message = "Could not send verification email.";
						  return false;
                    }
			}
		}
		else {
			$con = NULL;
			unset($data);
			unset($query);
			$this->flag = ERROR;
			$this->message = 'Wrong Email Address';
		}
				

	}



    function get_settings() { 
        $data = $this->crud->get_by( array('user_id' => session::get('user_id')), '=', true );
        return $data;
    }

    function process_setting() {
        
        $this->values = $_POST;
        if(!$this->check_form($this->values, ['uname'=>'Username', 'name' => 'Full Name', 'email' => 'Email'] ,'{uname},{name},{email}')) {
            $this->flag = ERROR;
            return false;
        }

        if(!$this->check_data($this->values['email'], 'email')) {
            $this->flag = ERROR;
            $this->message = 'Valid Email is required';
            return false;
        }
        
        $this->change_setting();
    }
 	
 	private function change_setting() {        
        $args = array();

        if(isset($this->values['email']))
        {
            $args['user_email'] = $this->values['email'];
            $pre_email = $this->crud->get_by(array('user_id' => session::get('user_id')), '=', 'true');

            $my_file = ROOT.DS.'protected/config/config.php';
            $data = file_get_contents($my_file);
             
            $data = str_replace($pre_email->user_email,$args['user_email'],$data);

            $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
            fwrite($handle, $data);
            fclose($handle);
        }

        if(isset($this->values['name']))
            $args['full_name'] = $this->values['name'];

        if(isset($this->values['uname']))
            $args['user_name'] = $this->values['uname'];

        $data = $this->crud->update( $args, array( 'user_id' => session::get('user_id') ));
        
        if($data > 0)
        {
            $this->flag = SUCCESS;
            $this->message = ' settings updated';
            return true;
        }
        else
        {
            $this->flag = WARNING;
            $this->message = "Setting weren't updated";
            return true;
        }        
    }

    function process_password()
    {
	 
        $this->values = $_POST;
        if(!$this->check_form($this->values, ['old_pass'=>'Old Password', 'new_pass' => 'New Password', 'confirm_pass' => 'Confirm Password'])) {
            $this->flag = ERROR;
            return false;
        }

        if($this->values['new_pass'] != $this->values['confirm_pass'])
        {
            $this->flag = ERROR;
            $this->message = 'Passwords donot match';
            return false;
        }

        $this->values['old_pass'] = sha1(AUTH_KEY.$this->values['old_pass']);
        $this->values['new_pass'] = sha1(AUTH_KEY.$this->values['new_pass']);


        $data = $this->crud->update( array('user_pass' => $this->values['new_pass']), array('user_pass' => $this->values['old_pass']) );
            
        if($data > 0)
        {
            $this->flag = SUCCESS;
            $this->message = 'Password updated';
            return true;
        }
        else
        {
            $this->flag = ERROR;
            $this->message = "Wrong password";
            return true;
        }
    }

    function update_user($single = false)
    {
        $this->values = $_POST;
        if(!$this->check_form($this->values, ['name'=>'Full name', 'email'=>'Email', 'username' => 'Username', 'role'=>'Role', 'status' => 'Status'])) {
            $this->flag = ERROR;
            return false;
        }            
    
        if($this->check_data($this->values['email'], 'email') == false)
        {
            $this->message = 'Please enter valid email';
            $this->flag = ERROR;
            return false;
        }

        if($this->values['status'] == 'yes')
        {
            $this->values['status'] = 1;
        }
        else if($this->values['status'] == 'no')
        {
            $this->values['status'] = 0;
        }
        else
        {
            $this->flag = ERROR;
            $this->message = 'Status is required';
            return false;
        }

        if($this->values['role'] == 'super_user')
        {
            $this->values['role'] = 'SA';
        }
        else if($this->values['role'] == 'admin')
        {
            $this->values['role'] = 'A';
        }
        else if($this->values['role'] == 'manager')
        {
            $this->values['role'] = 'M';
        }
        else
        {
            $this->flag = ERROR;
            $this->message = 'Valid User Role is required';
            return false;
        }
        
        $con = $this->crud->get_connection();

        $con->beginTransaction();

        try
        {

            $data = $this->crud->update( array('full_name'=>$this->values['name'], 'user_email'=>$this->values['email'], 'user_name' => $this->values['username'], 'user_type'=>$this->values['role'], 'active' => $this->values['status']), array('user_id' => $this->values['id']) );
            
            $con->commit();

            $this->flag = SUCCESS;
            $this->message = 'User updated';
            return true;
        }
        catch(Exception $e)
        {
            $con->rollBack();
            $this->flag = ERROR;
            $this->message = "Error updating user. Error : ". $e->getMessage();
            return true;
        }
        
    }



    function check_user($user_id)
    {
        $query = 'select * from login where user_id = :user_id';
        $data = $this->crud->exec_query($query, array(':user_id' => $user_id))->fetchAll(PDO::FETCH_CLASS);
        
        if(count($data) <= 0)
        {
            $this->flag = ERROR;
            $this->message = "Invalid access";
            return false;
        }
        return $data[0];
    }

    function regenerate_password($user_id)
    {
        $pass = $this->pass_generator();
        $pass_hash = sha1(AUTH_KEY.$pass);

        $data = $this->crud->update( array('user_pass'=>$pass_hash), array('user_id' => $user_id) );
                
        if($data > 0)
        {
            $this->flag = SUCCESS;
            $this->message = "Password has been updated. Password is ".$pass;
            return true;
        }
        return false;
    }

    function delete_users()
    {
        foreach ($_POST['item'] as $key => $value) {

            $con = $this->crud->get_connection();

            $con->beginTransaction();

            try
            {
                $data = $this->crud->delete( array('user_id' => $value) );

                $con->commit();

                $this->flag = SUCCESS;
                $this->message = 'Data Deleted';
            }
            catch(Exception $e)
            {
                $con->rollBack();
                $this->flag = ERROR;
                $this->message = "Error deleting details. Error : ". $e->getMessage();
            }
        }
    }

}


?>