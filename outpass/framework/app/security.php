<?php
defined('_DONUT') or die('Access Denied');
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
//-------------------------class to handle security
abstract class security {

	public $message;
	
	protected function sanitize($arg1) {
		$arg1 = rtrim(ltrim($arg1,' '),' ');
		return filter_var(filter_var(filter_var($arg1, FILTER_SANITIZE_STRING),FILTER_SANITIZE_MAGIC_QUOTES),FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	}

	protected function pass_generator() {
		$array = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
		$pass = mt_rand(1000,5000).$array[mt_rand(0,25)].$array[mt_rand(0,25)].$array[mt_rand(0,25)].$array[mt_rand(0,25)];
		return $pass;
	}

	protected function check_form(&$array, $keys, $require = false) {

		if($require === false) {
			foreach ($keys as $key=>$value) {
				if(!isset($array[$key]) || $array[$key] == '') {
					$this->message = $value.' is required';
					return false;
					break;
				}
				$array[$key] = $this->sanitize($array[$key]);
			}
		}	

		else {
			foreach ($keys as $key=>$value) {
				if(strpos($require, "{{$key}}") !== false) {

					if(isset($array[$key]) && $array[$key] != '') {
						$array[$key] = $this->sanitize($array[$key]);
					}
					else
						continue;
				}
				if(!isset($array[$key]) || $array[$key] == '') {
					$this->message = $value.' is required';
					return false;
					break;
				}
				$array[$key] = $this->sanitize($array[$key]);

				if($array[$key] == '') {
					$this->message = $value.' is required';
					return false;
					break;
				}
			}
		}

		return true;
		
	}

	protected function check_data($data, $type, $range = false) {
		if($type == 'email' && $data != '' && filter_var($data, FILTER_VALIDATE_EMAIL) === false)
			return false;
		elseif($type == 'int' && !is_numeric($data))
			return false;		
		elseif($type == 'range' && strlen($data) !== $range)
			return false;
		return true;

	}

	protected function set_title($value, &$page_title) {
		$page_title = $value;
		return true;
	}

	protected function send_mail($user_mail, $subject, $message, $is_html = false) // function to send mail.
    {
    	if($is_html) {
    		$from = ADMIN_EMAIL; // sender

    		$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	       	$headers .= "From: $from <$from>" . "\r\n";
	       	$message = '<html><body>'.$message.'</body></html>'; 
	        if(mail($user_mail,$subject,$message,$headers)) 
			{
	             return true;
	       	} 
	        else
			{
            	return false;
            }
    	}
    	else {
    		$from = ADMIN_EMAIL; // sender
	       	$headers = "From: $from <$from>" . "\r\n";
	        if(mail($user_mail,$subject,$message,$headers)) 
			{
	             return true;
	       	} 
	        else
			{
            	return false;
            }
    	}
       
    }

	public function load_view($value) {
		if(strpos($value, '.php') && file_exists(VIEWS.$value))
			include VIEWS.$value;
			
		elseif(file_exists(VIEWS.$value.'.php'))
			include VIEWS.$value.'.php';

		return true;
	}


	public function prepare_menu($array,$menu_class = false, $menu_id = false) {
		$menu = '';
		($menu_class === false) ? $menu .= '<ul' : $menu .= '<ul class="'.$menu.'" ';
		($menu_id === false) ? $menu .= '>' : $menu .= 'id="'.$menu.'" >';
		foreach($array as $arr) {
			$menu .= '<li';
			(isset($arr['parent_class'])) ? $menu .= ' class="'.$arr['parent_class'].'"': '' ;
			(isset($arr['parent_id'])) ? $menu .= ' id="'.$arr['parent_id'].'"': '' ;
			
			$menu .= '><a href="'.$arr['name'].'"';

			(isset($arr['class'])) ? $menu .= ' child_class="'.$arr['child_class'].'"': '' ;
			(isset($arr['child_id'])) ? $menu .= ' id="'.$arr['child_id'].'"': '' ;
			
			$menu .= '>';
			$menu .= $arr['value'].'</a></li>';
		}
		$menu .= '</ul>';
		return $menu;
	}
	public function name_generator() {
		$array = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
		$pass = mt_rand(1000,5000).$array[mt_rand(0,25)].$array[mt_rand(0,25)].$array[mt_rand(0,25)].$array[mt_rand(0,25)].$array[mt_rand(0,25)].mt_rand(1000,5000).mt_rand(1000,5000);
		return $pass;
	}

	public function number_generator() {
		$array = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
		$pass = mt_rand(1000,5000).mt_rand(0,9).mt_rand(0,5).$array[mt_rand(0,25)].$array[mt_rand(0,25)].mt_rand(1000,5000).mt_rand(1000,5000);
		return $pass;
	}

	public function otp_generator() {
		$pass = mt_rand(1000,5000).mt_rand(0,9).mt_rand(0,5).mt_rand(0,9);
		return $pass;
	}

	protected function create_string($array) {
		if(is_array($array))
			return implode(',',$array);
		return $array;
	}


	protected function redirect($url, $header_status = null, $header_message = null)
	{
		$header = '';
		if($header_status != null) {
			$header .= $header_status;
		}
		else{
			$header = '301';
		}
		if($header_message != null) {
			$header = $header.' '.$header_message;
		}
		else{
			$header = $header.' Moved Permanently';
		}

		header('HTTP/1.0 '.$header);
		header('location: '.$url);

	}

	protected function prg($token, $url)
	{
		if(session::get($token) != NULL)
		{
			$data = session::get('post');
			$_POST = $data[$token];
			session::clean_data($token);
		}
		else
		{
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$data = array();
				$data[$token] = $_POST;
				session::set_post_data($data, 'post');
				$this->redirect($url);
			}
		}
	}

	protected function generate_token()
	{
		$value = md5(uniqid(rand(), TRUE).time());
		session::set('security_token', $value);
	}

	protected function clear_token()
	{
		session::clean_data('security_token');
	}

}


function get_excerpt($string)
{
	return (strlen($string) > 155) ? substr($string,0,150).'...' : $string;
}

function validateDateTime($dateStr, $format)
{
    $date = DateTime::createFromFormat($format, $dateStr);
    return $date && ($date->format($format) === $dateStr);
}
