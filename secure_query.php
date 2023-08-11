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
    //Include the function document
    include("sys_page/functions.php");
    
    //Get session data
    $user_id = $_SESSION['user_id'];
    $school_code = $_SESSION['school_code'];

    //As this is a file to be called in js, check what page is currently calling this
    $url = $_SERVER['REQUEST_URI'];

    //If profile page, update database with new info saved by the user =============================================================================================================================
    if($url == "/dashboard/TutorMe/secure_query.php");
    {

        
        echo "."; //Add a dividing dot for each status
        //Saving the subjects ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        $subject_string = $_GET['subjects'];
        $subject_number = strlen($subject_string) / 2; //Take half the subjects as they are repeated twice, once for tutor and tutee each
        $tutee_subjects = substr($subject_string,0,$subject_number);
        $tutor_subjects = substr($subject_string,$subject_number);

        //Get the subject ids
        $subjects_to_check = get_available_subjects($school_code); //This array is formatted as [index][isglobalvariable?, subject name, subject_id]

        //Delete all the present subjects in the tables
        $subject_tutor_delete_sql = "DELETE FROM `6969_subjects_tutor` WHERE `tutor_id`='$user_id';";
        $subject_tutee_delete_sql = "DELETE FROM `6969_subjects_tutee` WHERE `tutee_id`='$user_id';";
        
        if ($conn->query($subject_tutor_delete_sql) === TRUE) {
          echo "0";
        } else {
          echo "Error deleting record: " . $conn->error;
        }
        echo ".";
        if ($conn->query($subject_tutee_delete_sql) === TRUE) {
          echo "0";
        } else {
          echo "Error deleting record: " . $conn->error;
        }
        echo ".";
        for($x=0;$x<$subject_number;$x++)
        {
          if($tutee_subjects[$x] == 1)
          {
            $subject_id = $subjects_to_check[$x][2];  
            $global_state = $subjects_to_check[$x][0];
            if($global_state == true) {$subject_update_sql = "INSERT INTO `6969_subjects_tutee`(`tutee_id`, `global_subject_id`, `local_subject_id`) VALUES ('$user_id','$subject_id','0');";}
            else {$subject_update_sql = "INSERT INTO `6969_subjects_tutee`(`tutee_id`, `global_subject_id`, `local_subject_id`) VALUES ('$user_id','0','$subject_id');";}
            if ($conn->query($subject_update_sql) === TRUE) {
              echo "0";
            } else {
              echo "Error adding record: " . $conn->error;
            }
          }
          if($tutor_subjects[$x] == 1)
          {
            $subject_id = $subjects_to_check[$x][2];  
            $global_state = $subjects_to_check[$x][0];
            if($global_state == true) {$subject_update_sql = "INSERT INTO `6969_subjects_tutor`(`tutor_id`, `global_subject_id`, `local_subject_id`) VALUES ('$user_id','$subject_id','0');";}
            else {$subject_update_sql = "INSERT INTO `6969_subjects_tutor`(`tutor_id`, `global_subject_id`, `local_subject_id`) VALUES ('$user_id','0','$subject_id');";}
            if ($conn->query($subject_update_sql) === TRUE) {
              echo "0";
            } else {
              echo "Error adding record: " . $conn->error;
            }
          }
        }
        echo ".";
        //print_r($subjects_to_check);
        

        $conn->close();
    }
}
?>