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

class user extends Model {
	private $crud;
    public $message;
    public $flag;

	function __construct() {
		$this->crud = new crud('students');
	}


	
    function process_student() {
        $this->values = $_POST;
        if(!$this->check_form($this->values, ['fname'=>'Name', 'email'=>'Email', 'p_no'=>'Primary number', 's_no'=>'Secondary number', 'reg_no' => 'Registration number', 'roll_no' => 'Roll number', 'meal_type' => 'Meal Type', 'room_no' => 'Room no', 'bed_no' => 'Bed no', 'rack_no' => 'Rack no',  'active' => 'Active Status'], '{s_no},{email}')) {
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

        $this->add_student();
    }

    private function add_student() {
        $dt = new DateTime();
        $date = $dt->format('Y-m-d H:i:s');

        $args = array('name'=>$this->values['fname'], 'email'=>$this->values['email'], 'primary_no' => $this->values['p_no'], 'secondary_no'=>$this->values['s_no'], 'reg_no' => $this->values['reg_no'], 'roll_no' => $this->values['roll_no'], 'meal_type' => $this->values['meal_type'], 'room_no' => $this->values['room_no'], 'bed_no' => $this->values['bed_no'], 'rack_no' => $this->values['rack_no'], 'active' => $this->values['active'], 'date' => $date);
        
        try
        {
            $data = $this->crud->insert( $args );
    
            if($data > 0)
            {
                $this->flag = SUCCESS;
                $this->message = 'Student Added';
                return true;
            }
            else
            {
                $this->flag = ERROR;
                $this->message = "Some error occured while adding student.";
                return true;
            }
        }
        catch(Exception $e)
        {
            $this->flag = ERROR;
            $this->message = 'Some error occured while adding student. '.$e->getMessage();
            return true;
        }
        
    }

    function get_students($filter = array(), $limit = false) {

        $query = 'select * from students';
        $flag = false;

        if(isset($filter['reg_no']))
        {
            if(!$this->check_form($filter, ['reg_no'=>'Reg no'])) {
                $this->flag = ERROR;
                return false;
            }
            $query = ($flag == true) ? $query . " AND reg_no = '".$filter['reg_no']."'" : $query . " where reg_no = '".$filter['reg_no']."'" ;
            if($flag == false) $flag = true;
        }
        if(isset($filter['meal_type']))
        {
            if(!$this->check_form($filter, ['meal_type'=>'Meal Type'])) {
                $this->flag = ERROR;
                return false;
            }
            $query = ($flag == true) ? $query . " AND meal_type = '".$filter['meal_type']."'" : $query . " where meal_type = '".$filter['meal_type']."'";
            if($flag == false) $flag = true;
        }
        if(isset($filter['room_no']))
        {
            if(!$this->check_form($filter, ['room_no' => 'Room no'])) {
                $this->flag = ERROR;
                return false;
            }
            $query = ($flag == true) ? $query . " AND room_no = '".$filter['room_no']."'" : $query . " where room_no = '".$filter['room_no']."'";
            if($flag == false) $flag = true;
        }
        if(isset($filter['bed_no']))
        {
            if(!$this->check_form($filter, ['bed_no'=>'Bed no'])) {
                $this->flag = ERROR;
                return false;
            }
            $query = ($flag == true) ? $query . " AND bed_no = '".$filter['bed_no']."'" : $query . " where bed_no = '".$filter['bed_no']."'" ;
            if($flag == false) $flag = true;
        }
        
        if($limit != false)
        {
            $query = $query.' '.$limit;
        }
        
        $data = $this->crud->exec_query($query)->fetchAll(PDO::FETCH_CLASS);
        return $data;
    }

    function check_user()
    {
        $this->values = $_POST;
        if(!$this->check_form($this->values, ['reg_no'=>'Registration number'])) {
            $this->flag = ERROR;
            return false;
        }

        $data = $this->crud->get_by( array('reg_no' => $this->values['reg_no'] ), '=', true);

        if(count($data) > 0)
        {
            $this->flag = SUCCESS;
            return $data;
        }
        else
        {
            $this->flag = ERROR;
            $this->message = "Item is not available.";
            return false;
        }
    }


    function delete_students()
    {
        foreach ($_POST['item'] as $key => $value) {

            $con = $this->crud->get_connection();

            $con->beginTransaction();

            try
            {
                $data = $this->crud->delete( array('stu_id' => $value) );
                
                $this->crud->changeTable('outpass');
                $data = $this->crud->delete( array('user_id' => $value) );
                
                $this->crud->changeTable('students');

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

    function process_export()
    {
          header('Content-Type: text/csv; charset=utf-8');  
          header('Content-Disposition: attachment; filename=data.csv');  
          $output = fopen("php://output", "w+");  
          fputcsv($output, array('name', 'email', 'primary_no', 'secondary_no', 'reg_no', 'roll_no', 'meal_type', 'room_no', 'bed_no', 'rack_no', 'active'));  
          $query = "SELECT name,email,primary_no,secondary_no,reg_no,roll_no,meal_type,room_no,bed_no,rack_no,active from students";
          $smt = $this->crud->get_connection()->prepare($query);
          $smt->execute();

          while($row = $smt->fetch(PDO::FETCH_ASSOC))  
          {  
               fputcsv($output, $row);  
          }  
          fclose($output);  
    }

    function process_import()
    {
        if(isset($_FILES['csv_file']))
        {
            $imageFileType = pathinfo(basename($_FILES["csv_file"]["name"]),PATHINFO_EXTENSION);

            if($imageFileType != "CSV" && $imageFileType != "csv") {
                $this->flag = ERROR;
                $this->message = "Valid CSV is required";
                return false;
            }
                $flag = true;
                $filename =  $_FILES['csv_file']['tmp_name'];
                $file = fopen($filename, "r");
                $count = 0;
                while (($getData = fgetcsv($file)) !== FALSE)
                 {
                    if($count <= 0)
                    {
                        $count++;
                        continue;
                    }
                    if(count($getData) < 7)
                    {

                        $this->flag = ERROR;
                        $flag = false;
                        if(isset($this->message))
                             $this->message .= "Invalid CSV data at line : ".($count+1).'<br>';
                        else
                            $this->message = "Invalid CSV data at line : ".($count+1).'<br>';

                        $count++;
                        continue;
                    }
                    $name = $getData[0];
                    $email = $getData[1];
                    $p_no = $getData[2];
                    $s_no = $getData[3];
                    $reg_no = $getData[4];
                    $roll_no = $getData[5];
                    $meal_type = $getData[6];
                    $room_no = $getData[7];
                    $bed_no = $getData[8];
                    $rack_no = $getData[9];
                    $active = $getData[10];

                    if($this->check_data($p_no, 'int') == false)
                    {
                        $this->flag = ERROR;
                        $flag = false;
                        if(isset($this->message))
                             $this->message .= "Invalid CSV data for primary no at line : ".($count+1).'<br>';
                        else
                            $this->message = "Invalid CSV data for primary no at line : ".($count+1).'<br>';


                        $count++;
                        continue;
                    }
                    if($email != '' &&  $this->check_data($email, 'email') == false)
                    {
                        $this->flag = ERROR;
                        $flag = false;
                        if(isset($this->message))
                             $this->message .= "Invalid CSV data for email at line : ".($count+1).'<br>';
                        else
                            $this->message = "Invalid CSV data for email at line : ".($count+1).'<br>';


                        $count++;
                        continue;
                    }

                    $data_prev2 = $this->crud->get_by( array('reg_no' => $reg_no), '=', true );
                    $data_prev3 = $this->crud->get_by( array('roll_no' => $roll_no), '=', true );

                    if(count($data_prev2) > 0)
                    {
                        $count++;
                        $this->flag = ERROR;
                        $flag = false;

                        if(isset($this->message))
                             $this->message .= "Student exists for reg_no ".$reg_no." at line : ".($count+1).'<br>';
                        else
                            $this->message = "Student exists for reg_no ".$reg_no." at line : ".($count+1).'<br>';
                        continue;
                    }
                    if(count($data_prev3) > 0)
                    {
                        $count++;
                        $this->flag = ERROR;
                        $flag = false;

                        if(isset($this->message))
                             $this->message .= "Student exists for roll_no ".$roll_no." at line : ".($count+1).'<br>';
                        else
                            $this->message = "Student exists for roll_no ".$roll_no." at line : ".($count+1).'<br>';
                        continue;
                    }

                    $query = "insert into students (name, email, primary_no, secondary_no, reg_no, roll_no, meal_type, room_no, bed_no, rack_no, active)
                            values (:name, :email, :primary_no, :secondary_no, :reg_no, :roll_no, :meal_type, :room_no, :bed_no, :rack_no, :active) ";
                    
                    $markers = array(':name' => $name, ':email' => $email, ':primary_no' =>  $p_no, ':secondary_no' => $s_no, ':reg_no' => $reg_no, ':roll_no' => $roll_no, ':meal_type' => $meal_type, ':room_no' => $room_no, ':bed_no' => $bed_no, ':rack_no' => $rack_no, ':active' => $active);


                    $this->crud->exec_query($query,$markers);

                    $data = $this->crud->get_connection()->lastInsertId();
                    
                    if($data > 0)
                    {
                        $this->flag = SUCCESS;
                        $flag = true;
                    }
                    else
                    {
                        $this->flag = ERROR;
                        $flag = false;
                        if(isset($this->message))
                             $this->message .= "Error Inserting student. Line : ". ($count+1).'<br>';
                        else
                            $this->message = "Error Inserting student. Line : ". ($count+1).'<br>';
                    }

                    $count++;
                }
                
                fclose($file); 
                if($flag == true)
                { 
                    $this->message = "Students imported successfully";
                    return true;
                }
                else return false;
        }

        return true;

    }





    function update_student($single = false)
    {
        $this->values = $_POST;
        if(!$this->check_form($this->values, ['fname'=>'Name', 'email'=>'Email', 'p_no'=>'Primary number', 's_no'=>'Secondary number', 'reg_no' => 'Registration number', 'roll_no' => 'Roll number', 'meal_type' => 'Meal Type', 'room_no' => 'Room no', 'bed_no' => 'Bed no', 'rack_no' => 'Rack no',  'status' => 'Active Status'], '{s_no},{email}')) {
            $this->flag = ERROR;
            return false;
        }           
    
        if(!empty($this->values['email']) && $this->check_data($this->values['email'], 'email') == false)
        {
            $this->message = 'Please enter valid email';
            $this->flag = ERROR;
            return false;
        }
        if($this->check_data($this->values['p_no'], 'int') == false)
        {
            $this->message = 'Please enter valid primary no';
            $this->flag = ERROR;
            return false;
        }
        if(!empty($this->values['s_no']) && $this->check_data($this->values['s_no'], 'int') == false)
        {
            $this->message = 'Please enter valid secondary no';
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
        
        $con = $this->crud->get_connection();

        $con->beginTransaction();

        try
        {

            $data = $this->crud->update( array('name'=>$this->values['fname'], 'email'=>$this->values['email'], 'primary_no' => $this->values['p_no'], 'secondary_no'=>$this->values['s_no'], 'reg_no' => $this->values['reg_no'], 'roll_no' => $this->values['roll_no'], 'meal_type' => $this->values['meal_type'], 'room_no' => $this->values['room_no'], 'bed_no' => $this->values['bed_no'], 'rack_no' => $this->values['rack_no'], 'active' => $this->values['status']), array('stu_id' => $this->values['id']) );
            
            $con->commit();

            $this->flag = SUCCESS;
            $this->message = 'Student updated';
            return true;
        }
        catch(Exception $e)
        {
            $con->rollBack();
            $this->flag = ERROR;
            $this->message = "Error updating student. Error : ". $e->getMessage();
            return true;
        }
        
    }



    function check_student($user_id)
    {
        $query = 'select * from students where stu_id = :user_id';
        $data = $this->crud->exec_query($query, array(':user_id' => $user_id))->fetchAll(PDO::FETCH_CLASS);
        
        if(count($data) <= 0)
        {
            $this->flag = ERROR;
            $this->message = "Invalid access";
            return false;
        }
        return $data[0];
    }



}


?>