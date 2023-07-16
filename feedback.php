<?php
session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in redirect to login page
{
    header("Location: login_form.php"); //Send to the shadow realm (login screen)
}

//Check if the session variable is set
$error = false;
if(!isset($_GET['session_id']))
{
    $error = true;//Show error if not set
}
else //Get the info about the session
{
    //Set variable from get array
    $session_id = $_GET['session_id'];
    $user_id = $_SESSION['user_id'];

    //Check if session id is an int
    if (filter_var($session_id, FILTER_VALIDATE_INT) === false ) {$error = true;} //If the session id is not an int show an error
    else //If the session id is an int, check if a valid session exists in the db
    {
        include("sys_page/db_connect.php");
        // Perform query
        $sql = "SELECT * FROM `6969_tutor_session` WHERE `id`= $session_id AND (`tutee_id` = $user_id OR `tutor_id` = $user_id)";
        if ($result = $conn -> query($sql)) {
            
            if($result -> num_rows <= 0) {$error = true;}//If no results are found, this session is not valid for this user, so an error should appear

            //If some data was found, check if this user has already placed feedback on this session
            $sql_feedback_check = "SELECT * FROM `6969_feedback` WHERE `session_id`='$session_id' AND `user_id`='$user_id'";
            if ($result_feedback_check = $conn -> query($sql_feedback_check)) {

                if($result_feedback_check -> num_rows <= 0) //If no results are found, no feedback has been placed so the user can proceed
                {
                    //If some data in first query was found, set it into various variables
                    $data = $result->fetch_assoc();
                    $tutee_id = $data['tutee_id'];
                    $tutor_id = array($data['tutor_id'],$data['teacher_id'],$data['ext_tutor_id']);
                    $subject_id = array($data['global_subject_id'],$data['local_subject_id']);
                    $starttime = $data['session_start'];
                    $endtime = $data['session_end'];

                    //Check which tutor id to use (the ones with 0/null are discarded)
                    for ($x = 0; $x <= 2 ; $x++) 
                    {
                        if($tutor_id[$x] != 0 && $tutor_id[$x] != NULL) 
                        {
                            $tutor_id_resolved = array($tutor_id[$x],$x); //Write the correct id to an array [resolved id, type]
                        }
                    }

                    //Check which subject id to use (the ones with 0/null are discarded)
                    for ($x = 0; $x <= 1 ; $x++) 
                    {
                        if($subject_id[$x] != 0 && $tutor_id[$x] != NULL) 
                        {
                            $subject_id_resolved = array($subject_id[$x],$x); //Write the correct id to an array [resolved id, type]
                        }
                    }

                    //Resolve ids into their respective names
                    //Tutee name
                    if ($result = $conn -> query("SELECT * FROM `6969_students` WHERE `id` = $tutee_id")) {$data = $result->fetch_assoc(); $tutee_name = $data['name'];}
                    else $error = true; //If the query failed show general purpose error

                    //Tutor name
                    if ($result = $conn -> query("SELECT * FROM `6969_students` WHERE `id` = $tutor_id_resolved[0]")) {$data = $result->fetch_assoc(); $tutor_name = $data['name'];}
                    else $error = true; //If the query failed show general purpose error

                    //Subject name
                    if($subject_id_resolved[1] == 0) {$table = "`subjects`";} //If the type is zero, the global subject table is used
                    else {$table = "`6969_subjects`";}
                    //Resolve name
                    if ($result = $conn -> query("SELECT * FROM " . $table . " WHERE `id` = $subject_id_resolved[0]")) {$data = $result->fetch_assoc(); $subject_name = $data['name'];}
                    else $error = true; //If the query failed show general purpose error
                }
                else //If the user has alredy placed feedback, show error msg
                {
                    echo "
                    <p>You have alredy placed feedback on this session</p>
                    <p>Changing feedback is not currently supported, if you want us to add it let us know in a bug report</p>
                    ";
                    $error = true;
                }
            }
        }
        else {$error = true;} //If the query failed enable error msg
        
        $conn -> close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Give some feedback</title>
</head>
<body>
    <?php if($error) //The error msg if a session is invalid
    {
        echo "
        <p>Sorry, it looks like you can't give feedback on this session, or that session doesn't exist</p>
        <p>If you think this is a bug, please fill out a bug report at <a href='#'>this link</a></p>
        ";
    }
    else //If session is value show feedback form, this form is then sent to log_feedback.php
    {
        //Set the session id as a session variable as it is used in the log feedback page
        $_SESSION['session_id'] = $session_id;
        echo "
        <div>
            <p>Tutor: " . $tutor_name . "</p>
            <p>Tutee: " . $tutee_name . "</p>
            <p>Subject: " . $subject_name . "</p>
            <p>Start time: " . $starttime . "</p>
            <p>End time " . $endtime . "</p>
        </div>
        <form method='POST' action='log_feedback.php'>
        <div>
            <p>How was the experience?</p>
            <select name='experience' id='experience'>
                <option value='5'>Incredible</option>
                <option value='4'>Great</option>
                <option value='3'>Average</option>
                <option value='2'>Poor</option>
                <option value='1'>Terrible</option>
            </select>
        </div>

        <div>
            <p>How productive was it?</p>
            <select name='productivity' id='productivity'>
                <option value='5'>Incredible</option>
                <option value='4'>Great</option>
                <option value='3'>Average</option>
                <option value='2'>Poor</option>
                <option value='1'>Terrible</option>
            </select>
        </div>

        <div>
            <p>Do you want this to be a recurring session? <strong> (This feature is coming soon)<strong> </p>
            <select name='productivity' id='productivity'>
                <option value='0'>No</option>"
                // <option value='1'>Weekly</option>
                // <option value='2'>Fortnightly</option>
                // <option value='3'>Monthly</option>
                ."
            </select>
        </div>
        <input type='submit' value='Submit Feedback'>
        </form>
        ";
    }?>


<div id="feedback_page_marker"></div>
</body>
</html>