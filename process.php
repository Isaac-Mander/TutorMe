<?php
include("sys_page/db_connect.php");
include("sys_page/functions.php");

//Gets the data sent from the form from calendar_1.php
if (isset($_POST['sorting'])){
$sorting = $_POST['sorting'];
 header("Location: action.php?sorting=" . $sorting);
}

$user_id = $_POST['user_id'];
$is_global = $_POST['is_global'];
$subject_id = $_POST['subject_id'];
$subject_status = $_POST['subject_status'];

if ($subject_status == 0){
    if ($is_global == TRUE){
        $sql = "DELETE FROM 6969_subjects_tutor WHERE tutor_id = $user_id AND global_subject_id = $subject_id"; 
        echo "working 1";
        echo $sql;
    }elseif ($is_global == FALSE){
        $sql = "DELETE FROM 6969_subjects_tutor WHERE tutor_id = $user_id AND local_subject_id = $subject_id"; 
        echo "working 2";
        echo $sql;
    }
    // DELETE FROM 6969_subjects_tutor WHERE tutor_id = 23 AND local_subject_id = 7
    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
        header("Location: info_setting.php");
      } else {
        echo "Error deleting record: " . $conn->error;
        header("Location: info_setting.php");
      }
      
}elseif ($subject_status == 1){
    if ($is_global == TRUE){
        $sql = "DELETE FROM 6969_subjects_tutee WHERE tutee_id = $user_id AND global_subject_id = $subject_id"; 
    }elseif ($is_global == FALSE){
        $sql = "DELETE FROM 6969_subjects_tutee WHERE tutee_id = $user_id AND local_subject_id = $subject_id"; 
    } 
    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
        header("Location: info_setting.php");
      } else {
        echo "Error deleting record: " . $conn->error;
        header ("Location: info_setting.php");
      }
      
}
?>
