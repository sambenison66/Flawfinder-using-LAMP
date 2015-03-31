<!-- Samuel Benison Jeyaraj Victor & Sneha Kulkarni -->
<html>
<head>
  <title>Analysis Board - Register User</title>
</head>
<body bgcolor="#C0C0C0">
<?php

error_reporting(E_ALL);
ini_set('display_errors','On');

// Get a connection for the database
require_once('../mysql_connection.php');

//Start session
session_start();

// Initial declaration of all the session variables that are used
if(!isset($_SESSION['username']))
{
  $_SESSION['username'] = "";
}

if(!isset($_SESSION['info']))
{
  $_SESSION['info'] = "";
}

// If the program is called from previous page i.e., Login page
if (isset($_POST["register"])=='New users must register here')
{
  $_SESSION['username'] = "";
  $_SESSION['status'] == "";
  $_POST["username"] = "";
  $_POST["password"] = "";
  unset($_SESSION['username']);
  unset($_POST["username"]);
  unset($_POST["password"]);
}
// If the user is tried to access the third page directly, it will redirect to Page 1 (Login page)
else if (!isset($_POST["newuser"]) && !isset($_POST["back"])) {
  if(!isset($_SESSION['status']) || $_SESSION['status'] == "") {
    if($_SESSION['username'] == "")
    {
      // Redirecting to the Login page
      $_SESSION['info'] = "Redirected to the home page";
      session_write_close();
      header("location: board.php");
      exit();
    }
  }
}

// Declaring the variables
$emptyuser = "";
$emptypass = "";
$repass = "";
$emptyname = "";
$emptyemail = "";
$infomsg = "";

$dispusername = "";
$dispemail = "";
$dispfullname = "";

// Actions to be taken when the Register button is clicked
if (isset($_POST["newuser"]) == 'Register')
{
    // Using these variable to prefil the entered data in case the givin data has some error
    $dispusername = $_POST["username"];
    $dispfullname = $_POST["fullname"];
    $dispemail = $_POST["email"];

    // This is to validate whether the Mandatory fields are filled are not
    if (empty($_POST["username"]))
    {
      $emptyuser = "* Username is required";
    }
    else if (empty($_POST["password"]))
    {
      $emptypass = "* Password is required";
    }
    else if (empty($_POST["repassword"]))
    {
      $repass = "* Retype Password is required";
    }
    else if (empty($_POST["fullname"]))
    {
      $emptyname = "* Full Name is required";
    }
    else if (empty($_POST["email"]))
    {
      $emptyemail = "* Email is required";
    }
    else if ($_POST["password"] != $_POST["repassword"])
    {
      $repass = "* Password does not match";  // If the enter passsword does not match with Reentered Password
    }
    else
    {
      // Getting the values given as a input for Registration
      $username = strtolower($_POST["username"]);
      $password = md5($_POST["password"]);
      $fullname = $_POST["fullname"];
      $email = $_POST["email"];
      // This is part where the php program will connect to the database
      try {
	
	// Create a query for the database
	$query = "SELECT * FROM statictool.secureUsers where username='". $username . "'";

	// Get a response from the database by sending the connection and the query
	$execquery = @mysqli_query($dbc, $query);

        if(!$execquery)
        {
            $_SESSION['info'] = "Unable to process the query, Please try again";
        }
        else
        {
          $query = array();
          $count = 0;
          while ($query = mysqli_fetch_array($execquery)) {
            $count = 1;
          }
          if($count == 1) {  // If the username already exists, throw an error
            $_SESSION['info'] = "Username already exists";
          }
          else
          {
            // Else Proceed with the insertion of record
            try {

	      // Create a query for the database
	      $query1 = "INSERT into statictool.secureUsers (username, password, fullname, email) VALUES (?, ?, ?, ?)";

	      // Prepare the statement based on query
	      $stmt = mysqli_prepare($dbc, $query1);
         
		// Bind the input values to the statement
        	mysqli_stmt_bind_param($stmt, "ssss", $username, $password, $fullname, $email);

		// Execute the given query to MySQL
		mysqli_stmt_execute($stmt);
         
		// Get the Output result
        	$affected_rows = mysqli_stmt_affected_rows($stmt);
         
		// If affected row is 1, then it's good
		// Or else something went wrong
        	if($affected_rows == 1){

              	   $_SESSION['info'] = "New User created successfully";
              	   session_write_close();
		   mysqli_stmt_close($stmt);
            	   mysqli_close($dbc);

              	   header("location: board.php");  // Redirect to Login page once the Registration is complete
              	   exit();
		} else {
		   $_SESSION['info'] = "Something is Wrong, Try again";

		   mysqli_stmt_close($stmt);
            	   mysqli_close($dbc);
		}
            }
            catch (Exception $e) {
              print "Error!: " . $e->getMessage() . "<br/>";
              die();
            }
          }
        }
      } catch (Exception $e) {
          print "Error!: " . $e->getMessage() . "<br/>";
          die();
        }

   }
}

// Redirect to Login page upon clicking back button
if (isset($_POST["back"])=='Goback')
{
    session_write_close();
    header("location: board.php");
    exit();
}

// Displaying the information message on to the HTML tag
if(isset($_SESSION['info']) && $_SESSION['info'] != "")
{
  $infomsg = $_SESSION['info'];
  $_SESSION['info'] = "";
}

?>
<form action="register.php" method="POST">
<table width="309" border="1" align="center">
  <tr>
    <td colspan="2"><b><span>New User Registration</span>
  </b>
  </td>
  </tr>
  <tr>
    <td width="116"><div align="right">Username</div></td>
    <td width="177"><input name="username" value="<?php echo $dispusername; ?>" type="text" /></td>
    <td nowrap><span style="color:red"><?php echo $emptyuser;?></span></td>
  </tr>
  <tr>
    <td><div align="right">Password</div></td>
    <td><input name="password" type="password" /></td>
    <td nowrap><span style="color:red"><?php echo $emptypass;?></span></td>
  </tr>
  <tr>
    <td><div align="right">Retype Password</div></td>
    <td><input name="repassword" type="password" /></td>
    <td nowrap><span style="color:red"><?php echo $repass;?></span></td>
  </tr>
  <tr>
    <td width="116"><div align="right">Fullname</div></td>
    <td width="177"><input name="fullname" value="<?php echo $dispfullname; ?>" type="text" /></td>
    <td nowrap><span style="color:red"><?php echo $emptyname;?></span></td>
  </tr>
  <tr>
    <td width="116"><div align="right">Email</div></td>
    <td width="177"><input name="email" value="<?php echo $dispemail; ?>" type="text" /></td>
    <td nowrap><span style="color:red"><?php echo $emptyemail;?></span></td>
  </tr>
  <tr>
    <td align="center"><input name="back" type="submit" value="Goback" /></td>
    <td align="center"><input name="newuser" type="submit" value="Register" /></td>
    <td nowrap><span style="color:red"><?php echo $infomsg;?></span></td>
  </tr>
</table>
</form>
</body>
</html>
