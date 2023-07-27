<?php
session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in redirect to login page
{
    header("Location: login_form.php"); //Send to the shadow realm (login screen)
}
include("sys_page/header.html");
include("sys_page/db_connect.php");
include("sys_page/functions.php");

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
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  
  
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
        
        <section class= 'h-3000 h-custom' style='background-color:#8fc4b7;'>
        <div class='container py-5 h-500'>
          <div class='row d-flex justify-content-center align-items-center h-100'>
            <div class='col-lg-8 col-xl-6'>
              <div class='card rounded-3'>
                <div class='card-body p-4 p-md-5'>
                  <h3 class='border border-1 rounded border-success mb-4 pb-2 pb-md-0 mb-md-5 px-md-2'>Feedback</h3>
                  <div class='mb-4 border-top border-1 '>
                    <p>Tutor: " . $tutor_name . "</p>
                    <p>Tutee: " . $tutee_name . "</p>
                    <p>Subject: " . $subject_name . "</p>
                    <p>Start time: " . $starttime . "</p>
                    <p>End time " . $endtime . "</p>
                </div>
      
                  <form class='px-md-2' method='POST' action='log_feedback.php'>
                    <div class='mb-4 border-top border-1 '>
                    <p>How was the experience?</p>
                    <select name='experience' id='experience' class='select'>
                        <option value='5'>Incredible</option>
                        <option value='4'>Great</option>
                        <option value='3'>Average</option>
                        <option value='2'>Poor</option>
                        <option value='1'>Terrible</option>
                    </select>
      
                    </div>
                    <div class='mb-4 border-top border-1 '>
                    <p>How productive was it?</p>
                    <select name='productivity' id='productivity' class='select'>
                        <option value='5'>Incredible</option>
                        <option value='4'>Great</option>
                        <option value='3'>Average</option>
                        <option value='2'>Poor</option>
                        <option value='1'>Terrible</option>
                    </select>
            
                    </div>
                    <div class='mb-4 border-top border-1 '>
                    <p>Do you want this to be a recurring session? <strong> (This feature is coming soon)<strong> </p>
                    <select name='button' id='button' class='select'>
                        <option value='0'>No</option>"
                        // <option value='1'>Weekly</option>
                        // <option value='2'>Fortnightly</option>
                        // <option value='3'>Monthly</option>
                        ."
                    </select>
      
                    </div>
      
      
                    <button type='submit' value='Submit Feedback'class='btn btn-success btn-lg mb-1'>Submit Feedback</button>
      
                  </form>
      
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </body>
    <style>
    @media (min-width: 1500px) {
      .h-custom {
      height: 300vh !important;
      }
    }
    </style>
        ";
    }?>


<div id="feedback_page_marker"></div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</html>
