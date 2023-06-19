<?php

session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in redirect to login page
{
    header("Location: login_form.php"); //Send to the shadow realm (login screen)
}

//If id is not send send back to session matching page
//if(!isset($_GET['id'])) { header("Location: session_matching.php");}

//Split the id into it constituent variables
$raw_id = $_GET['id'];
//Scan through and find each info block split up by -
$array_index = 0;
$info_array = array();
$info_array[$array_index] = ""; //Set first array val to be empty string (others are done via an if statement)
for ($string_index = 0; $string_index <= strlen($raw_id)-1; $string_index++)
{
    //If not partition char add value to string
    if($raw_id[$string_index] != "-") {$info_array[$array_index] .= $raw_id[$string_index];}
    //If partition char increment array index to start getting data for new val
    else
    {
        $array_index += 1;
        $info_array[$array_index] = "";
    }
}

//table_id - subject_id  - tutee_id - tutor_id
$id_in_table = $info_array[0];
$subject_id = $info_array[1];
$tutee_id = $info_array[2];
$tutor_id = $info_array[3];
$start_time = $info_array[4];
$end_time = $info_array[5];
$is_global = $info_array[6];
$date = $info_array[7] ."-". $info_array[8] ."-". substr($info_array[9],0,2);

$start_combined = $date . " " . $start_time;
$end_combined = $date . " " . $end_time;


include("sys_page/db_connect.php");

if($is_global)
{
    $sql = "INSERT INTO `6969_tutor_session`(`tutee_id`, `tutor_id`, `teacher_id`, `ext_tutor_id`, `session_start`, `session_end`, `global_subject_id`, `local_subject_id`, `is_active`) VALUES ('$tutee_id','$tutor_id','0','0','$start_combined','$end_combined','$subject_id','0','0')";
}
else
{
    $sql = "INSERT INTO `6969_tutor_session`(`tutee_id`, `tutor_id`, `teacher_id`, `ext_tutor_id`, `session_start`, `session_end`, `global_subject_id`, `local_subject_id`, `is_active`) VALUES ('$tutee_id','$tutor_id','0','0','$start_combined','$end_combined','0','$subject_id','0')";
}
if ($conn->query($sql) === TRUE)
{
    echo "New record created successfully";
    $alert = 1;
} 
else
{
    echo "Error: " . $sql . "<br>" . $conn->error;
    $alert = 2;
}

$conn->close();

header("Location: session_matching.php?alert=" . $alert);

?>