
<?php



//Connect to database
include("sys_page/db_connect.php");

//Import functions
include("sys_page/functions.php");

//Start a session
session_start();

$error = false;
$error_msg = "";

//Check what page site should be showing
if(isset($_POST['page'])) {$page = $_POST['page'];}
else {$page = 0;}


//Hide all errors on page 2 (don't ask why it breaks, the page still works but the error blocks part of the page)
if($page == 2) {ini_set('display_errors', 0 );}
else {ini_set('display_errors', 1);}


echo "<br>";
//If first page add the user credentials to the holding database during setup
if($page == 1)
{
  if($_POST['email'] != "" && $_POST['password'] != "")
  {
    //Set session variables
    $_SESSION['email'] = remove_emoji($_POST['email']);
    $_SESSION['password'] = remove_emoji($_POST['password']);
    $email = remove_emoji($_POST['email']);
    $password_hash = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $_SESSION['hashed_password'] = $password_hash;
    //Check if user already exists in holding database
    $sql = "SELECT email FROM holding_logins WHERE email='$email';";
    $result = $conn->query($sql); //Query database
    if ($result->num_rows > 0) { //If the number of rows are not zero means user found
      $error_msg = "";
      }
      else 
      {
        $error_msg = "";
        $sql = "INSERT INTO `holding_logins` (`email`, `password` ,`expiry`) VALUES ('$email', '$password_hash', DATE_ADD(CURRENT_TIMESTAMP(),INTERVAL 7 DAY));";

        if ($conn->query($sql) === TRUE) {
          //echo "New record created successfully in holding login table";
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
  //If school code isn't null and a display name was given add user in school's database
  if($school_code != null && $_POST['username'] != "")
  {
  //Check if the username is already taken by another user
  $username = $_POST['username'];
  $duplicate_check_sql = "SELECT * FROM `6969_students` WHERE `username`='$username';";
  $duplicate_check_result = $conn->query($duplicate_check_sql); //Query database
  if($duplicate_check_result->num_rows > 0) {  //If any rows are returned, a user exists with that username
    $error = True;
    $error_msg = "That username is already taken, pick another one";
  }
  else
  {
    //Write to session tokens
    $_SESSION['username'] = $_POST['username'];
    $_SESSION['school_code'] = $school_code;
    $_SESSION['name'] = $_POST['name'];
    


    //Get all the data from session tokens
    $email = $_SESSION['email'];
    $hashed_password = $_SESSION['hashed_password'];
    $username = $_SESSION['username'];
    $name = $_SESSION['name'];

    //Set up sql query
    $table = $school_code . "_students";
    $sql = "INSERT INTO `$table` (`email`, `hashed_password` ,`username`, `name`) VALUES ('$email', '$hashed_password', '$username', '$name');";

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
}
if($error) $page += -1; //Stop progression if error with user input

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="sys_page/styles.css">
    <title>Signup</title>
</head>
<body>
  
  <p><?php echo $error_msg; //can you make this a notification ?></p>
  <form method="post" action="signup_form_student_2.php">
    <input type="hidden" name="page" value=<?php echo $page + 1; ?> />
    <?php
    //TO EDIT THESE JUST COPY AND PASTE THE HTML TO GET THE SYNTAX RIGHT
    //Page 1
    if($page == 0)
    {
      echo ' 
      <section class="h-500 h-custom" style="background-color: #8fc4b7;">
      <div class="container py-5 h-300">
        <div class="row d-flex justify-content-center align-items-center h-100">
          <div class="col-lg-8 col-xl-6">
            <div class="card rounded-3">
              <div class="card-body p-4 p-md-5">
                <h3 class="mb-4 pb-2 pb-md-0 mb-md-5 px-md-2">Sign up info</h3>
    
                <form class="px-md-2">
    
                  <div class="form-outline mb-4">
                    <label class="form-label" for="email">Email</label>
                    <input type="text" id="email" name= "email" class="form-control" />
                  </div>
                  <div class="form-outline mb-4">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name= "password" class="form-control" />
                  </div>
    
                  <button type="submit" class="btn btn-success btn-lg mb-1">Submit</button>
    
                </form>
    
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </body>
  <style>
  @media (min-width: 1500px) {
    .h-custom {
    height: 100vh !important;
    }
  }
  body {
    overflow: hidden;
  }
  </style>  
      ';
    }

    //Page 2
    if($page == 1)
    {
      echo '
      <section class="h-500 h-custom" style="background-color: #8fc4b7;">
        <div class="container py-5 h-300">
          <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-lg-8 col-xl-6">
              <div class="card rounded-3">
                <div class="card-body p-4 p-md-5">
                  <h3 class="mb-4 pb-2 pb-md-0 mb-md-5 px-md-2">Sign up info</h3>
      
                  <form class="px-md-2">
      
                    <div class="form-outline mb-4">
                      <input type="text" id="name" name= "name" class="form-control" />
                      <label class="form-label" for="form3Example1q">Display Name</label>
                    </div>
      
                    <div class="row">
                      <div class="col-md-6 mb-4">
      
                        <div class="form-outline datepicker">
                          <input type="text" name= "username" class="form-control" id="exampleDatepicker1" />
                          <label for="exampleDatepicker1" class="form-label">User Name</label>
                        </div>
      
                      </div>
                    <div class="mb-4">
                      
                      <select name="schooldropdown" id"schooldropdown" class="select">
                        <option value="1">Stac</option>
                      </select>
      
                    </div>
      
                    <div class="row mb-4 pb-2 pb-md-0 mb-md-5">
                      <div class="col-md-6">
      
                        <div class="form-outline">
                          <input type="text" id="form3Example1w" name ="schoolcode" class="form-control" disabled/>
                          <label class="form-label" for="form3Example1w">School code (if you know it)</label>
                        </div>
      
                      </div>
                    </div>
      
                    <button type="submit" class="btn btn-success btn-lg mb-1">Submit</button>
      
                  </form>
      
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </body>
    <style>
    @media (min-width: 1500px) {
      .h-custom {
      height: 100vh !important;
      }
    }
    body {
      overflow: hidden;
    }
    </style>
      ';
    }
    ?>
  </form>
</body>

</html>