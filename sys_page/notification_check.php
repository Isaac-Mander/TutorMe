<?php
//Check if the user to logged in
session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in redirect to login page
{
    header("Location: login_form.php"); //Send to the shadow realm (login screen)
}

//Get userid from session token
$user_id = $_SESSION['user_id'];

include("../sys_page/db_connect.php");

//Check if the user has a session to accept
$sql = "SELECT * FROM `6969_tutor_session` WHERE `tutee_id` = $user_id AND `is_active`= 0";
$result = $conn->query($sql); //Query database
$potential_session_array = [];

//If no sessions are found nothing happens
//If the number of rows are not zero get data as an array
if ($result->num_rows > 0) { 
    $index = 0;
    while($row = $result->fetch_assoc()) {
        $potential_session_array[$index]['id'] = $row['id'];
        $potential_session_array[$index]['tutee_id'] = $user_id;

        $potential_session_array[$index]['tutor_id'] = $row['tutor_id'];


        $id_to_check = $row['tutor_id'];
        $sql_student_name = "SELECT * FROM `6969_students` WHERE `id` = $id_to_check";
        $result_student_name = $conn->query($sql_student_name); //Query database
        $data_student_name = $result_student_name->fetch_assoc();
        $potential_session_array[$index]['tutor_name'] = $data_student_name['name'];



        $potential_session_array[$index]['session_start'] = $row['session_start'];
        $potential_session_array[$index]['session_end'] = $row['session_end'];
        $potential_session_array[$index]['is_active'] = $row['is_active'];

         //Resolve subject name
         //Check which database to use
        if($row['global_subject_id'] == 0) {
            $subject_id = $row['local_subject_id'];
            $name_sql = "SELECT `name` FROM `6969_subjects` WHERE `id`= '$subject_id'";
        }
        else {
            $subject_id = $row['global_subject_id'];
            $name_sql = "SELECT `name` FROM `subjects` WHERE `id`= '$subject_id'";
        }
        //Get the subject name
        $name_results = $conn->query($name_sql);
        $name_data = $name_results->fetch_assoc();

        $potential_session_array[$index]['subject_name'] = $name_data['name'];
        $index += 1;
    }


    //Get the names of users/subjects
    echo json_encode($potential_session_array);
    
}
else echo "nodata";


?>