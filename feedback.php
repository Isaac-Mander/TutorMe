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
    if ( filter_var($session_id, FILTER_VALIDATE_INT) === false ) {$error = true;} //If the session id is not an int show an error
    else //If the session id is an int, check if a valid session exists in the db
    {
        include("sys_page/db_connect.php");
        // Perform query
        $sql = "SELECT * FROM `6969_tutor_session` WHERE `id`= $session_id AND (`tutee_id` = $user_id OR `tutor_id` = $user_id)";
        if ($result = $conn -> query($sql)) {
            
            if($result -> num_rows <= 0) {$error = true;}//If no results are found, this session is not valid for this user, so an error should appear

            //If some data was found, set it into various variables
            else
            {
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
                else $error = true;
                //Tutor name
                if ($result = $conn -> query("SELECT * FROM `6969_students` WHERE `id` = $tutor_id_resolved[0]")) {$data = $result->fetch_assoc(); $tutor_name = $data['name'];}
                else $error = true;
                //Subject name
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
    else //If session is value show feedback form
    echo "
    <div>
        <p>OTHER PERSON NAME</p>
        <p>SUBJECT</p>
        <p>SESSION SLOT</p>
        <p>DATE</p>
    </div>

    <div>
        <p>How was the experience?</p>
    </div>

    <div>
        <p>How productive was it?</p>
    </div>

    <div>
        <p>Do you want this to be a recurring session?</p>
        <button>Same time</button>
        <button>Different time</button>
        <button>No</button>
    </div>
    ";?>



</body>
</html>