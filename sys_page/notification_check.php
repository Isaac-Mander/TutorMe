<?php
//Check if the user to logged in
session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in redirect to login page
{
    header("Location: login_form.php"); //Send to the shadow realm (login screen)
}
$tz = new DateTimeZone('NZ');
$dt = new DateTime('now',$tz);
$time_day = $dt->format('d'); // output: '1' - '31'
$time_month = $dt->format('m'); // output: '1' - '12'cc
$time_year = $dt->format('Y'); // output: '2023'
$time_hours = $dt->format('H'); // output: '2023'
$time_minutes = $dt->format('i'); // output: '2023'
//gets the time from right now to january 1970
$time = mktime($time_hours,$time_minutes,0,$time_month,$time_day,$time_year);
//Get userid from session token
$user_id = $_SESSION['user_id'];

include("../sys_page/db_connect.php");
include("../sys_page/functions.php");

//Check if the user has a session to accept
$sql = "SELECT * FROM `6969_tutor_session` WHERE `tutee_id` = $user_id AND `is_active`= 0";
$result = $conn->query($sql); //Query database
$potential_session_array = [];

//If no sessions are found nothing happens
//If the number of rows are not zero get data as an array
$check = 0;
if ($result->num_rows > 0) { 
    $index = 0;
    while($row = $result->fetch_assoc()) {
        //getting the length of time the session start is away from january 1970
        $day = substr($row['session_start'],8,2);
        $month = substr($row['session_start'],5,2);
        $year = substr($row['session_start'],0,4);
        $hour = substr($row['session_start'],11,2);
        $minutes = substr($row['session_start'],14,2);
        $session_time = mktime($hour,$minutes,0,$month,$day,$year);
        //If the current time is closer to jan 1970 then the session is then the code will run
        if ($time < $session_time) 
        {   

            $check = 1;
            //setting the data
            $potential_session_array[$index]['id'] = $row['id'];
            $potential_session_array[$index]['tutee_id'] = $user_id;
            $potential_session_array[$index]['tutor_id'] = $row['tutor_id'];


            //querying the database to get the associated tutor name
            $id_to_check = $row['tutor_id'];
            $sql_student_name = "SELECT * FROM `6969_students` WHERE `id` = $id_to_check";
            $result_student_name = $conn->query($sql_student_name); //Query database
            $data_student_name = $result_student_name->fetch_assoc();
            $potential_session_array[$index]['tutor_name'] = $data_student_name['name'];


            //setting the data for the session times
            $potential_session_array[$index]['session_start'] = $row['session_start'];
            $potential_session_array[$index]['session_end'] = $row['session_end'];
            $potential_session_array[$index]['is_active'] = $row['is_active'];

            //Find the average ratings of the tutor
            $tutor_ratings = average_ratings($conn, $row['tutor_id']);
            $potential_session_array[$index]['av_prod'] = $tutor_ratings[0];
            $potential_session_array[$index]['av_expe'] = $tutor_ratings[1];

            //Resolve subject name
            //Check which database to use
            if($row['global_subject_id'] == 0) 
            {   
                //querying the database to get the local subject name
                $subject_id = $row['local_subject_id'];
                $name_sql = "SELECT `name` FROM `6969_subjects` WHERE `id`= '$subject_id'";
            }
            else 
            {
                //querying the database to get the global subject name
                $subject_id = $row['global_subject_id'];
                $name_sql = "SELECT `name` FROM `subjects` WHERE `id`= '$subject_id'";
            }
            //Get the subject name
            $name_results = $conn->query($name_sql);
            $name_data = $name_results->fetch_assoc();

            $potential_session_array[$index]['subject_name'] = $name_data['name'];
            $index += 1;
        }
    }


    //Get the names of users/subjects
    echo json_encode($potential_session_array);
    
}
else echo "nodata";
if ($check ==1){
    //echo "no data";
}
?>