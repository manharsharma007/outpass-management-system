<?php
if(isset($_POST['submit']))
{
   define('AUTH_KEY', 'O4D#$HthjJ#B[n#skG++5t:O8>D1?O#vA->s.ZpF;z;;BLR#`MmLtRgCGs{iG@</M$S,{');

   ini_set('memory_limit', '5120M');
   set_time_limit ( 0 );
   
   function remove_comments(&$output)
   {
      $lines = explode("\n", $output);
      $output = "";

      // try to keep mem. use down
      $linecount = count($lines);

      $in_comment = false;
      for($i = 0; $i < $linecount; $i++)
      {
         if( preg_match("/^\/\*/", preg_quote($lines[$i])) )
         {
            $in_comment = true;
         }

         if( !$in_comment )
         {
            $output .= $lines[$i] . "\n";
         }

         if( preg_match("/\*\/$/", preg_quote($lines[$i])) )
         {
            $in_comment = false;
         }
      }

      unset($lines);
      return $output;
   }

   //
   // remove_remarks will strip the sql comment lines out of an uploaded sql file
   //
   function remove_remarks($sql)
   {
      $lines = explode("\n", $sql);

      // try to keep mem. use down
      $sql = "";

      $linecount = count($lines);
      $output = "";

      for ($i = 0; $i < $linecount; $i++)
      {
         if (($i != ($linecount - 1)) || (strlen($lines[$i]) > 0))
         {
            if (isset($lines[$i][0]) && $lines[$i][0] != "#")
            {
               $output .= $lines[$i] . "\n";
            }
            else
            {
               $output .= "\n";
            }
            // Trading a bit of speed for lower mem. use here.
            $lines[$i] = "";
         }
      }

      return $output;

   }

   //
   // split_sql_file will split an uploaded sql file into single sql statements.
   // Note: expects trim() to have already been run on $sql.
   //
   function split_sql_file($sql, $delimiter)
   {
      // Split up our string into "possible" SQL statements.
      $tokens = explode($delimiter, $sql);

      // try to save mem.
      $sql = "";
      $output = array();

      // we don't actually care about the matches preg gives us.
      $matches = array();

      // this is faster than calling count($oktens) every time thru the loop.
      $token_count = count($tokens);
      for ($i = 0; $i < $token_count; $i++)
      {
         // Don't wanna add an empty string as the last thing in the array.
         if (($i != ($token_count - 1)) || (strlen($tokens[$i] > 0)))
         {
            // This is the total number of single quotes in the token.
            $total_quotes = preg_match_all("/'/", $tokens[$i], $matches);
            // Counts single quotes that are preceded by an odd number of backslashes,
            // which means they're escaped quotes.
            $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$i], $matches);

            $unescaped_quotes = $total_quotes - $escaped_quotes;

            // If the number of unescaped quotes is even, then the delimiter did NOT occur inside a string literal.
            if (($unescaped_quotes % 2) == 0)
            {
               // It's a complete sql statement.
               $output[] = $tokens[$i];
               // save memory.
               $tokens[$i] = "";
            }
            else
            {
               // incomplete sql statement. keep adding tokens until we have a complete one.
               // $temp will hold what we have so far.
               $temp = $tokens[$i] . $delimiter;
               // save memory..
               $tokens[$i] = "";

               // Do we have a complete statement yet?
               $complete_stmt = false;

               for ($j = $i + 1; (!$complete_stmt && ($j < $token_count)); $j++)
               {
                  // This is the total number of single quotes in the token.
                  $total_quotes = preg_match_all("/'/", $tokens[$j], $matches);
                  // Counts single quotes that are preceded by an odd number of backslashes,
                  // which means they're escaped quotes.
                  $escaped_quotes = preg_match_all("/(?<!\\\\)(\\\\\\\\)*\\\\'/", $tokens[$j], $matches);

                  $unescaped_quotes = $total_quotes - $escaped_quotes;

                  if (($unescaped_quotes % 2) == 1)
                  {
                     // odd number of unescaped quotes. In combination with the previous incomplete
                     // statement(s), we now have a complete statement. (2 odds always make an even)
                     $output[] = $temp . $tokens[$j];

                     // save memory.
                     $tokens[$j] = "";
                     $temp = "";

                     // exit the loop.
                     $complete_stmt = true;
                     // make sure the outer loop continues at the right point.
                     $i = $j;
                  }
                  else
                  {
                     // even number of unescaped quotes. We still don't have a complete statement.
                     // (1 odd and 1 even always make an odd)
                     $temp .= $tokens[$j] . $delimiter;
                     // save memory.
                     $tokens[$j] = "";
                  }

               } // for..
            } // else
         }
      }

      return $output;
   }

   $dbms_schema = 'db.sql';

   $sql_query = @fread(@fopen($dbms_schema, 'r'), @filesize($dbms_schema)) or die('problem ');
   $sql_query = remove_remarks($sql_query);
   $sql_query = split_sql_file($sql_query, ';');

   $host = $_POST['host'];
   $user = $_POST['username'];
   $pass = $_POST['password'];
   $db = $_POST['db_name'];
   $email = $_POST['email'];
   $uname = $_POST['app_uname'];
   $upass = $_POST['app_password'];
   $time_zone = $_POST['time_zone'];

   $uname = filter_var(filter_var(filter_var($uname, FILTER_SANITIZE_STRING),FILTER_SANITIZE_MAGIC_QUOTES),FILTER_SANITIZE_FULL_SPECIAL_CHARS);
   $upass = filter_var(filter_var(filter_var($upass, FILTER_SANITIZE_STRING),FILTER_SANITIZE_MAGIC_QUOTES),FILTER_SANITIZE_FULL_SPECIAL_CHARS);

   if($host == '')
   {
      $message = 'Valid host is required';
      $flag = 0; 
   }
   elseif($db == '')
   {
      $message = 'Valid database is required';
      $flag = 0; 
   }
   elseif($email == '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false)
   {
      $message = 'Valid email is required';
      $flag = 0; 
   }
   elseif($uname == '')
   {
      $message = 'Valid app username is required';
      $flag = 0; 
   }
   elseif($upass == '')
   {
      $message = 'Valid app password is required';
      $flag = 0; 
   }
   elseif($time_zone == '')
   {
      $message = 'Valid time zone is required';
      $flag = 0; 
   }
   else
   {

      
      $folder = basename(dirname(dirname(__FILE__))).'/';

      $upass = SHA1(AUTH_KEY.$upass);


      //In case mysql is deprecated use mysqli functions.
      
      try 
      {
        $pdo =  new PDO( "mysql:host=".$host.";"."dbname=".$db, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 



         $i=1;
         foreach($sql_query as $sql){
            $pdo->exec($sql);
         }

         $my_file = '../protected/config/config.php';
         $data = file_get_contents($my_file);

         
         $data = str_replace('%site%',$folder,$data);
         
         $data = str_replace('%email%',$email,$data);
         
         $data = str_replace('%db%',$db,$data);
         
         $data = str_replace('%host%',$host,$data);
         
         $data = str_replace('%user%',$user,$data);
         
         $data = str_replace('%pass%',$pass,$data);
         
         $data = str_replace('%install%','false' ,$data);
         
         $data = str_replace('%timezone%', $time_zone ,$data);

         $data = str_replace('?>', '' ,$data);

         $data = $data.'date_default_timezone_set(TIME_ZONE);';


         $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
         fwrite($handle, $data);
         fclose($handle);


         $message .= 'Database imported successfully. please delete install folder before you continue.<br>';

         $query = "insert into login (user_name, user_pass, user_email, user_type, active) VALUES ('$uname', '$upass', '$email', 'SA', 1)";

         $dbResult = $pdo->prepare($query);
         $res = $dbResult->execute();

         if($res)
         {
            $message .= 'Data inserted successfully.<br>';
         }

         $flag = 1; 
      }
      catch(PDOException $e) 
      {
        $message = ($e->getMessage());
        $flag = 0;  
      }
   }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Install LMS</title>
   <link rel="stylesheet" href="../public/style.css" />
   <link rel="shortcut icon" href="../public/images/favicon.ico" />
   <link rel="stylesheet" href="../public/css/font-awesome.min.css" />
</head>
<body>
   <div class="login-container container">

   <div class="login form" style="width:550px;">
   <form action="#" method="post">
      
      <div class="row heading">
         <h4 style="font-size:20px;"><i class="fa fa-cogs" aria-hidden="true"></i> Install</h4>
      </div>

<div class="row">
   <?php
      if(isset($flag) && isset($message)) {

         if($flag == 1)
            echo '<div class="msg success"><p>'.$message.'</p>';
         elseif($flag == 0)
            echo '<div class="msg fail"><p>'.$message.'</p>';

         echo'</div>';
      }

   ?>
   </div>

      <div class="form-element clearfix">
         <label>Host
            <sup>*</sup>
         </label>
         <input type="text" name="host"  required placeholder="Enter username"  value="">
      </div>

      <div class="form-element clearfix">
         <label>Database
            <sup>*</sup>
         </label>
         <input type="text" name="db_name"  required placeholder="Enter username"  value="">
      </div>

      <div class="form-element clearfix">
         <label>DB username
            <sup>*</sup>
         </label>
         <input type="password" name="username" placeholder="Enter password"   value="">
      </div>

      <div class="form-element clearfix">
         <label>DB Password
            <sup>*</sup>
         </label>
         <input type="password" name="password" placeholder="Enter password"   value="">
      </div>


      <div class="form-element clearfix">
         <label>Email
            <sup>*</sup>
         </label>
         <input type="email" name="email" required placeholder="Enter email"   value="">
      </div>

      <div class="form-element clearfix">
         <label>App username
            <sup>*</sup>
         </label>
         <input type="text" name="app_uname" required placeholder="Enter username"   value="">
      </div>

      <div class="form-element clearfix">
         <label>App password
            <sup>*</sup>
         </label>
         <input type="text" name="app_password" required placeholder="Enter password"   value="">
      </div>
      <div class="input-element">
         <label>Time Zone
         </label>
         <select name="time_zone">
            <option value="Kwajalein">GMT -12.00 Kwajalein</option>
            <option value="Pacific/Midway">GMT -11.00 Pacific/Midway</option>
            <option value="Pacific/Honolulu">GMT -10.00 Pacific/Honolulu</option>
            <option value="America/Anchorage">GMT -9.00  America/Anchorage</option>
            <option value="America/Los_Angeles">GMT -8.00  America/Los_Angeles</option>
            <option value="America/Denver">GMT -7.00  America/Denver</option>
            <option value="America/Tegucigalpa">GMT -6.00  America/Tegucigalpa</option>
            <option value="America/New_York">GMT -5.00  America/New_York</option>
            <option value="America/Caracas">GMT -4.30  America/Caracas</option>
            <option value="America/Halifax">GMT -4.00  America/Halifax</option>
            <option value="America/St_Johns">GMT -3.30  America/St_Johns</option>
            <option value="America/Argentina/Buenos_Aires">GMT +-3.00 America/Argentina/Buenos_Aires</option>
            <option value="America/Sao_Paulo"> GMT -3.00  America/Sao_Paulo</option>
            <option value="Atlantic/South_Georgia">GMT +-2.00 Atlantic/South_Georgia</option>
            <option value="Atlantic/Azores">GMT -1.00  Atlantic/Azores</option>
            <option value="Europe/Dublin">GMT        Europe/Dublin</option>
            <option value="Europe/Belgrade">GMT +1.00  Europe/Belgrade</option>
            <option value="Europe/Minsk">GMT +2.00  Europe/Minsk</option>
            <option value="Asia/Kuwait">GMT +3.00  Asia/Kuwait</option>
            <option value="Asia/Tehran">GMT +3.30  Asia/Tehran</option>
            <option value="Asia/Muscat">GMT +4.00  Asia/Muscat</option>
            <option value="Asia/Yekaterinburg">GMT +5.00  Asia/Yekaterinburg</option>
            <option value="Asia/Kolkata" selected="selected">GMT +5.30  Asia/Kolkata</option>
            <option value="Asia/Katmandu">GMT +5.45  Asia/Katmandu</option>
            <option value="Asia/Dhaka">GMT +6.00  Asia/Dhaka</option>
            <option value="Asia/Rangoon">GMT +6.30  Asia/Rangoon</option>
            <option value="Asia/Krasnoyarsk">GMT +7.00  Asia/Krasnoyarsk</option>
            <option value="Asia/Brunei">GMT +8.00  Asia/Brunei</option>
            <option value="Asia/Seoul">GMT +9.00  Asia/Seoul</option>
            <option value="Australia/Darwin">GMT +9.30  Australia/Darwin</option>
            <option value="Australia/Canberra">GMT +10.00 Australia/Canberra</option>
            <option value="Asia/Magadan">GMT +11.00 Asia/Magadan</option>
            <option value="Pacific/Fiji">GMT +12.00 Pacific/Fiji</option>
            <option value="Pacific/Tongatapu">GMT +13.00 Pacific/Tongatapu</option>
            <option value="">Time Zone</option>
         </select>
      </div>

         <input type="submit" name="submit" value="Continue" class="login_btn btn-green" />

      
   </form>
</div>
   </div>