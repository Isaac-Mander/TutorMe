<?php
//This page is for querys that are performed via javascript

//Firstly check if the user is logged in
session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in redirect to login page
{
    //If not return an error
    echo 1;
}
else
{
    //Connect to database
    include("sys_page/db_connect.php");
    //Get session data
    $user_id = $_SESSION['user_id'];
    $school_code = $_SESSION['school_code'];

    //As this is a file to be called in js, check what page is currently calling this
    $url = $_SERVER['REQUEST_URI'];

    //If profile page, update database with new info saved by the user
    if($url == "/dashboard/TutorMe/secure_query.php");
    {
        $new_description = $_GET['description'];
        $profile_sql = "UPDATE `6969_students` SET `description`='$new_description' WHERE id=$user_id";
        if ($conn->query($profile_sql) === TRUE) {
            echo "0";
          } else {
            echo "1";
          }
          $conn->close();
    }
}
?>