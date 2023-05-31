<?php

session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in redirect to login page
{
    header("Location: login_form.php"); //Send to the shadow realm (login screen)
}

//If id is not send send back to session matching page
if(!isset($_GET['id'])) { header("Location: session_matching.php");}


//Get the data about the time slot using the id that was passed through the $_GET
include("sys_page/db_connect.php");

$id_in_table = $_GET['id'];

$select_sql = "SELECT * FROM `6969_student_times` WHERE `id`=$id_in_table";
$select_result = $conn->query($select_sql); //Query database
if ($select_result->num_rows > 0) { //If the number of rows are not zero
    $row = $select_result->fetch_assoc();
}

//Create a tutor session that is not active (is yet to be accepted)
$tutor_id = $_SESSION['user_id'];
$tutee_id = $row['student_id'];
$sql = "INSERT INTO `6969_tutor_session`(`tutee_id`, `tutor_id`, `teacher_id`, `ext_tutor_id`, `session_start`, `session_end`, `global_subject_id`, `local_subject_id`, `is_active`) VALUES ('$tutee_id','$tutor_id','0','0','[value-6]','[value-7]','[value-8]','[value-9]','0')";
echo $sql;


$conn->close();

?>