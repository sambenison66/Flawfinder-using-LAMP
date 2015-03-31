<!-- Samuel Benison Jeyaraj Victor & Sneha Kulkarni -->
<html>
<head>
  <title>Analysis Board - Login</title>
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

if(!isset($_SESSION['fullname']))
{
  $_SESSION['fullname'] = "";
}


if(!isset($_SESSION['status']))
{
  $_SESSION['status'] = "";
}

if(!isset($_SESSION['info']))
{
  $_SESSION['info'] = "";
}

// Variable declaration
$emptyuser = "";
$emptypass = "";
$infomsg = "";

// Actions to be performed when Login button is clicked
if (isset($_POST["login"])=='Login')
{
    // This is to validate whether the Mandatory fields are filled are not
   if (empty($_POST["username"]) || empty($_POST["password"]))
   {
      if (empty($_POST["username"]))
      {
        $emptyuser = "* Username is required"; // If username is not enter, throw error message
      }
      if (empty($_POST["password"]))
      {
        $emptypass = "* Password is required";   // If Password is not enter, throw error message
      }
   }
   else
   {
      // Retrieving the valid login credentials and stored in a variable
      $username = strtolower($_POST["username"]);
      $password = md5($_POST["password"]);  // md5 methodology is used for password encryption

      // This is part where the php program will connect to the database
      try {
        // Create a query for the database
	$query = "SELECT * FROM statictool.secureUsers where username='". $username . "' AND password='" . $password . "'";

	// Get a response from the database by sending the connection and the query
	$execquery = @mysqli_query($dbc, $query);
        
        //Returns 0 if the select query was not processed
        if(!$execquery)
        {
            $_SESSION['info'] = "Unable to process the query, Please try again";
        }
        else
        {
          // Validate the Result set by using the Fetch method
          $query = array();
          $count = 0;
          while ($query = mysqli_fetch_array($execquery)) {
            $count = 1;  // Counter incremented if valid login
            // If the result set matched with the login credentials
            if(strtolower($query['username']) == $username && $query['password'] == $password)
            {
              // Assign values to the session variables
              $_SESSION['username'] = $username;
              $_SESSION['fullname'] = $query['fullname'];
              $_SESSION['status'] = "Active";
              session_write_close();
              // Call the next page that is Message Board page
              header("location: analyze.php");
              exit();
            }
          }
          if($count == 0) {  // Counter would remain 0 if it is invalid login
            $_SESSION['info'] = "Invalid Username/Password";
          }
        }
      } catch (Exception $e) {   // Exception if the database connection is failed
          print "Error!: " . $e->getMessage() . "<br/>";
          die();
      }

      // Close connection to the database
      mysqli_close($dbc);
   }
}

// Various informations are passed from different pages, all the messages are verified here and displayed according on the html tag
if(isset($_SESSION['info']) && $_SESSION['info'] != "")
{
  $infomsg = $_SESSION['info'];
  $_SESSION['info'] = "";
}

?>
<form action="board.php" method="POST">
<table width="309" border="1" align="center">
  <tr>
    <td colspan="2"><b><span>Flawfiner Analysis Board</span></b></td>
  </tr>
  <tr>
    <td width="116"><div align="right">Username</div></td>
    <td width="177"><input name="username" type="text" /></td>
    <td nowrap><span style="color:red"><?php echo $emptyuser;?></span></td>
  </tr>
  <tr>
    <td><div align="right">Password</div></td>
    <td><input name="password" type="password" /></td>
    <td nowrap><span style="color:red"><?php echo $emptypass;?></span></td>
  </tr>
  <tr>
    <td><div align="right"></div></td>
    <td align="center"><input name="login" type="submit" value="Login" /></td>
    <td nowrap><span style="color:red"><?php echo $infomsg;?></span></td>
  </tr>
</table>
</form>

<form action="register.php" method="POST">
<table width="309" border="1" align="center">
<tr><td>
<label>New User Registration:<label>
<input type="submit" name="register" value="New users must register here"/>
</td></tr>
</table>
</form>

</body>
</html>
