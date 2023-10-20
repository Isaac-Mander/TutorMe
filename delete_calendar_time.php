<?php
//Check if user is logged in and needed variables are set
session_start();
if((!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) && isset($_GET['id']))//If not logged in redirect to login page
{
    header("Location: login_form.php"); //Send to the shadow realm (login screen)
}

//Connect to the database
include("sys_page/db_connect.php");

//Get id to delete from session tag
$id_to_del = $_GET['id'];
//Delete id from free times table
$sql = "DELETE FROM 6969_student_times WHERE `6969_student_times`.`id` = $id_to_del";

//the code for running the query and echoing out an error if it doesn't work
if ($conn->query($sql) === TRUE) {
  echo "Worked";
} else {
  echo "Error deleting record: " . $conn->error;
}

$conn->close();


//Redirect user back to the calendar page
header("Location: info_setting.php");
?>