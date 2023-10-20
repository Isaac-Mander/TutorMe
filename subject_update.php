<?php
//This page is for updating subjects that are performed via javascript
//THIS PAGE COULD BE A POTENTIAL SECURITY RISK AS THE NEW SUBJECTS ARE SENT VIA GET AND THE LINK IS VIEWABLE IN THE JS CONTENT PAGE
//To mitigate this, the server will use $_session data to only allow a http request to effect their account. 
//A malicious link could theoretically wipe a users subjects if they happen to be logged in, but there is not much I can do to mitigate this right now.

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
    //Saving the subjects ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //The subject data is sent via a binary string "01010010001001010010110" with the last bit acting as a pointer for if the string is meant to effect tutee or tutor subjects
    $raw_subject_string = $_GET['subjects']; // Get data
    echo $raw_subject_string;
    echo ".";

    //Check and remove the subject type bit
    $subject_type = substr($raw_subject_string, -1);
    $real_subject_string = substr($raw_subject_string, 0, -1);

    //Get the subject ids
    $subjects_to_check = get_available_subjects($school_code); //This array is formatted as [index][isglobalvariable?, subject name, subject_id]

    //Delete all the present subjects in the tables
    if($subject_type == "0")
    {
      $subject_delete_sql = "DELETE FROM `6969_subjects_tutee` WHERE `tutee_id`='$user_id';";
    }
    else
    {
      $subject_delete_sql = "DELETE FROM `6969_subjects_tutor` WHERE `tutor_id`='$user_id';";
    }
    //if it works echo 0, if it doesn't echo out the error
    if ($conn->query($subject_delete_sql) === TRUE) {
      echo "0";
    } else {
      echo "Error deleting record: " . $conn->error;
    }
    echo ".";
    for($x=0;$x<strlen($real_subject_string);$x++)
    {
      if($subject_type == "0")
      {
        if($real_subject_string[$x] == "1")
        {
          //if the cases are satisfied insert into the tutee subjects, if it works echo 0, if it doesn't echo out the error
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
      }
      if($subject_type == "1")
      {
        if($real_subject_string[$x] == "1")
        {
          //if the cases are satisfied insert into the tutor subjects, if it works echo 0, if it doesn't echo out the error
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
    }
    echo ".";
    //print_r($subjects_to_check);
    

    $conn->close();
}
?>