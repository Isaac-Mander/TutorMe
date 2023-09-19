<?php
include("sys_page/db_connect.php");
include("sys_page/functions.php");

//Gets the data sent from the form from calendar_1.php
if (isset($_POST['sorting'])){
$sorting = $_POST['sorting'];
 header("Location: action.php?sorting=" . $sorting);
}
//setting the variables
$user_id = $_POST['user_id'];
$is_global = $_POST['is_global'];
$subject_id = $_POST['subject_id'];
$subject_status = $_POST['subject_status'];

//if it is a tutor
if ($subject_status == 0){
  //checks which if it's a global subject
    if ($is_global == TRUE){
        //adds it to the database
        $sql = "DELETE FROM 6969_subjects_tutor WHERE tutor_id = $user_id AND global_subject_id = $subject_id"; 
    }elseif ($is_global == FALSE){
        //adds it to the database
        $sql = "DELETE FROM 6969_subjects_tutor WHERE tutor_id = $user_id AND local_subject_id = $subject_id"; 
    }
    // does the query
    if ($conn->query($sql) === TRUE) {
        //if it works has message & returns to info_setting page
        echo "Record deleted successfully";
        header("Location: info_setting.php");
      } else {
        //if it doesn't work has message then returns to info-setting page
        echo "Error deleting record: " . $conn->error;
        header("Location: info_setting.php");
      }
//if it is a subject that needs help
}elseif ($subject_status == 1){
  //checks which if it's a global subject
    if ($is_global == TRUE){
      //adds it to the database
      $sql = "DELETE FROM 6969_subjects_tutee WHERE tutee_id = $user_id AND global_subject_id = $subject_id"; 
    }elseif ($is_global == FALSE){
      //adds it to the database
      $sql = "DELETE FROM 6969_subjects_tutee WHERE tutee_id = $user_id AND local_subject_id = $subject_id"; 
    } 
    // does the query
    if ($conn->query($sql) === TRUE) {
        //if it works has message & returns to info_setting page
        echo "Record deleted successfully";
        header("Location: info_setting.php");
      } else {
        //if it doesn't work has message then returns to info-setting page
        echo "Error deleting record: " . $conn->error;
        header ("Location: info_setting.php");
      }
      
}
?>
