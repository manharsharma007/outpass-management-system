<?php
		$protocol = 'http';
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')
		{
			$protocol = 'https';
		}
		define('URL', $protocol.'://'.$_SERVER['HTTP_HOST'].'/');
		define('SITE','outpass/');
		define('DB_NAME','oms');
		define('DB_PASS','');
		define('DB_HOST','localhost');
		define('DB_USER','root');
		define('ADMIN_EMAIL','manharshasrma007@gmai.com');

		define('INSTALL', 'false');

		define('TIME_ZONE', 'Asia/Kolkata');

		$api_error_codes = array(
					'201!' => 'Username or password may be wrong',
					'205!' => 'Insufficient Credits',
					'207!' => 'Un-authorized user',
					'105!' => 'Empty Message',
					'106!' => 'Invalid Mobile Number',
					'107!' => 'Not Valid API KEY'
					);

		define('API_KEY', 'd2cee786-eb8e-468d-bd8e-3691690a066e');
		define('SENDERID', 'BEERAM');
		define('API_URL', 'http://www.bulksmsapps.com/api/apismsv2.aspx');
		define('DATE_FORMAT', 'Y-m-d H:i:s');date_default_timezone_set(TIME_ZONE);