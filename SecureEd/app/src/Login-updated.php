<?php
try {
    /*Get DB connection*/
    require_once "../src/DBController.php";

    /*Get information from the post request*/
    $myusername = $_POST['username'];
    $mypassword = $_POST['password'];
	
	// Define regular expressions to validate the username and password inputs
	//Valide username = username@domain.com
	$username_regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
	$password_regex = '/^(?=.*\d)(?=.*[A-Z])[a-zA-Z0-9]{8,16}$/';

   
	// Check if email is in correct format
	//if (!filter_var($myusername, FILTER_VALIDATE_EMAIL)) {
    //echo "Error: Invalid email format";
														 }

   if($myusername==null)
    {throw new Exception("input did not exist");}
    // Validate the username input
	
   else if (!preg_match($username_regex, $myusername)) {
   // If user is not in the correct email format, display an error message
   //echo "Invalid email address";

    $myusername = strtolower($myusername); //makes username noncase-sensitive
    global $acctype;


    //query for count
    $query = "SELECT COUNT(*) as count FROM User WHERE Email='$myusername' AND (Password='$mypassword' OR Password='$hashpassword')";
    $count = $db->querySingle($query);

    //query for the row(s)
    $query = "SELECT * FROM User WHERE Email='$myusername' AND (Password='$mypassword' OR Password='$hashpassword')";
    $results = $db->query($query);
	
    if ($results !== false) //query failed check
    {
        if (($userinfo = $results->fetchArray()) !== (null || false)) //checks if rows exist
        {
            // users or user found
            $error = false;

            $acctype = $userinfo[2];
        } else {
            // user was not found
            $error = true;

        }
    } else {
        //query failed
        $error = true;

    }

    //determine if an account that met the credentials was found
    if ($count >= 1 && !$error) {
        //login success

        if (isset($_SESSION)) {
            //a session already existed
            session_destroy();
            session_start();
            $_SESSION['email'] = $myusername;
            $_SESSION['acctype'] = $acctype;
        } else {
            //a session did not exist
            session_start();
            $_SESSION['email'] = $myusername;
            $_SESSION['acctype'] = $acctype;
        }
        //redirect
        header("Location: ../public/dashboard.php");
    } else {
        //login fail
        header("Location: ../public/index.php?login=fail");
    }
//note: since the database is not changed, it is not backed up
}
catch(Exception $e)
{
    //prepare page for content
    include_once "ErrorHeader.php";

    //Display error information
    echo 'Caught exception: ',  $e->getMessage(), "<br>";
    var_dump($e->getTraceAsString());
    echo 'in '.'http://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']."<br>";

    $allVars = get_defined_vars();
    debug_zval_dump($allVars);
}




