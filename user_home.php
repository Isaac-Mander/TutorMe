<?php
//Import functions
include("sys_page/functions.php");

$_SESSION['school_code'] = 9696;
session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in redirect to login page
{
    header("Location: login_form.php"); //Send to the shadow realm (login screen)
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="sys_page/styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous"> 
</head>
<body>


    <?php
    //Add the header
    include("sys_page/header.html");

    //Connect to db
    include("sys_page/db_connect.php")
    ?>
    <h1 class="text-center">Welcome to TutorMe!</h1>    
    <p class="text-center">This site is designed to help connect people who want tuoring with those who want to volenteer their knoledge to help others.</p>
    
    <?php
    //Get user id from session var
    $user_id = $_SESSION['user_id'];
    //Setup array [times,tutee_subjects,tutor_subjects]
    $setup_errors = [false,false,false];
    //Check if anything needs to be set up in regards to this account
    $check_times_sql = "SELECT * FROM `6969_student_times` WHERE `student_id`=$user_id;";
    $result = $conn->query($check_times_sql); if ($result->num_rows == 0) { $setup_errors[0] = true;}

    //If both subject tables return empty, the user has not set any subjects
    $check_tutee_subjects_sql = "SELECT * FROM `6969_subjects_tutee` WHERE `tutee_id`=$user_id;";
    $check_tutor_subjects_sql = "SELECT * FROM `6969_subjects_tutor` WHERE `tutor_id`=$user_id;";
    $result = $conn->query($check_tutee_subjects_sql); if ($result->num_rows == 0) { $setup_errors[1] = true;}
    $result = $conn->query($check_tutor_subjects_sql); if ($result->num_rows == 0) { $setup_errors[2] = true;}

    //If any errors were detected, show the relevant error msg
    if(($setup_errors[0] == true) OR ($setup_errors[1] == true) OR ($setup_errors[1] == true))
    {
        //Echo welcome text
        echo "<h3>You are currently logged in as, ". $_SESSION['user'].", but you haven't finished setting up your account</h3>";
        echo "<ul>";
        if($setup_errors[0] == true) {echo "<li>Select the times when you are available for tutoring</li>";}
        if($setup_errors[1] == true OR $setup_errors[2] == true) {echo "<li>Select your subjects (in both recieving and giving tutoring)</li>";}
        echo "</ul>";
        echo "<button type='button' class='btn btn-success btn-md'>You can set these up here</button>";
    }
    else
    {
        echo "<h3>You are currently logged in as, ". $_SESSION['user']."</h3>";
    }
    ?>
    <h3>Unsure how to use TutorMe?</h3>
    <button type="button" class="btn btn-success btn-md">We have a guide</button>
    <script src="content.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script> 
</body>
</html>