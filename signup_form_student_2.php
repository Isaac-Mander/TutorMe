<?php
//Connect to database
include("sys_page/db_connect.php");

//Start a session
session_start();

$error = false;
$error_msg = "";

//Check what page site should be showing
if(isset($_POST['page'])) {$page = $_POST['page'];}
else {$page = 0;}

echo "<br>";
//If first page add the user credentials to the holding database during setup
if($page == 1)
{
  if($_POST['email'] != "" && $_POST['password'] != "")
  {
    //Set session variables
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['password'] = $_POST['password'];
    $email = $_POST['email'];
    $password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $_SESSION['hashed_password'] = $password_hash;
    //Check if user already exists in holding database
    $sql = "SELECT email FROM holding_logins WHERE email='$email';";
    $result = $conn->query($sql); //Query database
    if ($result->num_rows > 0) { //If the number of rows are not zero means user found
        $error_msg = "That email already exists";
      }
      else 
      {
        $error_msg = "";
        $sql = "INSERT INTO `holding_logins` (`email`, `password` ,`expiry`) VALUES ('$email', '$password_hash', DATE_ADD(CURRENT_TIMESTAMP(),INTERVAL 7 DAY));";

        if ($conn->query($sql) === TRUE) {
          echo "New record created successfully in holding login table";
        } else {
          echo "Error: " . $sql . "<br>" . $conn->error;
        }
      }
  }
  else //If data not set
  {
    $error = true;
    $error_msg = "Please enter valid infomation for:";
    if($_POST['email'] == "") $error_msg = $error_msg . "<br>Email";
    if($_POST['password'] == "") $error_msg = $error_msg . "<br>Password ";
  }
}



if($page == 2)
{
  $school_code = null;

  //If the user typed in a school code prioritise that
  if($_POST['schoolcode'] != "")
  {
    $code = $_POST['schoolcode'];
    //Check if it is valid
    $sql = "SELECT * FROM `admin` WHERE school_code='$code';";
    $result = $conn->query($sql); //Query database
    if($result->num_rows > 0) { $school_code = $code; }//If code is valid
  }
  //If a school is selected and code not given find code of selected school
  if($_POST['schooldropdown'] != "" && $_POST['schoolcode'] == "")
  {
    $school_id = $_POST['schooldropdown'];
    $sql = "SELECT * FROM `admin` WHERE id='$school_id';";
    $result = $conn->query($sql); //Query database
    //If id is valid get the code
    if($result->num_rows > 0) { 
      $row = $result->fetch_assoc();
      $school_code = $row['school_code']; 
    }
  }
  echo $school_code; //DEBUG TO BE REMOVED

  //If school code isn't null and a display name was given add user in school's database
  if($school_code != null && $_POST['username'] != "")
  {
    //Write to session tokens
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['school_code'] = $school_code;
    


    //Get all the data from session tokens
    $email = $_SESSION['email'];
    $hashed_password = $_SESSION['hashed_password'];
    $username = $_SESSION['username'];

    //Set up sql query
    $table = $school_code . "_students";
    $sql = "INSERT INTO `$table` (`email`, `hashed_password` ,`username`) VALUES ('$email', '$hashed_password', '$username');";

    //If insertion was successful redirect to login page
    if ($conn->query($sql) === TRUE) {
      echo "New record created successfully in " . $table;
      //Clear session
      session_reset();
      header("Location: login_form.php"); //Send to the shadow realm (login screen)
      
    } else {
      echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }
}
if($error) $page += -1; //Stop progression if error with user input

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
</head>
<body>
  <h1>Signup form</h1>
  <p><?php echo $error_msg; ?></p>
  <form method="post" action="signup_form_student_2.php">
    <input type="hidden" name="page" value=<?php echo $page + 1; ?> />
    <?php
    //TO EDIT THESE JUST COPY AND PASTE THE HTML TO GET THE SYNTAX RIGHT
    //Page 1
    if($page == 0)
    {
      echo '    
      <label for="email">Email</label><br>
      <input type="text" id="email" name="email"><br>
      <label for="password">Password</label><br>
      <input type="text" id="password" name="password"><br>
      ';
    }

    //Page 2
    if($page == 1)
    {
      echo '
      <label for="schoolcode">School Code (If you know it)</label><br>
      <input type="text" name="schoolcode"><br><br>
      <label for="schooldropdown">Or select your school</label><br>
      <select name="schooldropdown" id="schooldropdown">
        <option value="1">DESOEAT</option>
        <option value="2">School 2</option>
      </select><br><br>
      <label for="username">What do you want other people to call you? (Display Name)</label><br>
      <input type="text" name="username"><br><br>
      ';
    }
    ?>
  <br><button type="submit">Send</button>
  </form>
</body>
</html>