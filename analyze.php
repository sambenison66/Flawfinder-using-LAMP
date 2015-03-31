<!-- Samuel Benison Jeyaraj Victor & Sneha Kulkarni  -->
<html>
<head>
  <title>Analysis Board - Flawfinder</title>
</head>
<body bgcolor="#C0C0C0">
<h1 align="center"><b>Flawfinder Static Analysis Tool</b></h1>
<?php

error_reporting(E_ALL);
ini_set('display_errors','On');

//Start session
session_start();

// If the user is tried to access the second page directly, it will redirect to Page 1 (Login page)
if(!isset($_SESSION['username']) || $_SESSION['username'] == "")
{
  $_SESSION['info'] = "Redirected to the home page";
  session_write_close();
  header("location: board.php");
  exit();
}

// Print a Welcome message with the user's full name
echo "Welcome " . $_SESSION['fullname'] . ",\n\n";

?>
<br>
<span><b><h3> This is a Web Application to analyze C/C++ Code using Flawfinder Tool </h3></b></span>

<?php

// Declaring the variables
$errormsg = "";
$infmsg = "";
$output = "";
$code = "";

// Actions to be taken when the Post button is clicked
if (isset($_POST["analyze"])=='Post' && $_SESSION['username'] != "")
{
  // Show an error message if the message field is blank
  if (empty($_POST["code"]))
  {
      $errormsg = "Please enter some code and then click Post";
  }
  else
  {
    $code = $_POST["code"];  // Get the message
    $type = $_POST["type"]; // Get the type of the program
    

    // Connect with the database
    try {
      if($type == "cpp") {
	// Create a file
    	$file = "testMyCppCode.cpp";
	// Write the code back to the file
      	file_put_contents($file, $code);
	// Execute the Flawfinder for the code
      	$output = shell_exec('bash findflawinCpp.sh');
      } else {
	$file = "testMyCCode.c";
	// Write the code back to the file
      	file_put_contents($file, $code);
	// Execute the Flawfinder for the code
      	$output = shell_exec('bash findflawinC.sh');
      }
      if($output != "") {
      	$infmsg = "Analyzed the code successfully";
      } else {
	$infmsg = "Something is wrong, Please try again";
      }
    } catch (Exception $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
      }
  }
}

// Clear the session and cache once the logout button is clicked
// and Navigate to Login page
if (isset($_POST["logout"])=='Logout')
{
  unset($_SESSION['username']);
  unset($_SESSION['fullname']);
  unset($_SESSION['status']);
  unset($_POST["username"]);
  unset($_POST["password"]);
  unset($_POST["code"]);
  unset($_POST["type"]);
  $_SESSION['info'] = "Logged Out Successfully";
  session_write_close();
  header("location: board.php");
  exit();
}

?>
<br>
<form action="analyze.php" method="POST">
<fieldset><legend>Enter Your Code here:</legend>
<textarea name="code" id="code" style="width:100%; height: 200px;">
<?php
echo $code . "\n";
?>
</textarea>
<br>
<br>
<input type="radio" name="type" value="c" checked>C
<tab align=right>
<input type="radio" name="type" value="cpp">C++
<br>
<input type="submit" name="analyze" value="Post"/>
<br>
<span style="color:red"><?php echo $errormsg;?></span>
<br>
<span style="color:green"><?php echo $infmsg;?></span>
</fieldset>
<br>
<b><h4> Analysis Result: </h4></b>
<textarea style="width:75%; height: 200px;">
<?php

echo $output;
?>
</textarea>
<br>
<br>
<input style="width:150px; height:100px; align:center;" type="submit" name="logout" value="Logout"/>
</form>
</body>
</html>
