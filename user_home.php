<?php
//Import functions
include("sys_page/functions.php");


//CHECK IF USER IS LOGGED IN
session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in redirect to login page
{
    header("Location: login_form.html"); //Send to the shadow realm (login screen)
}
?>
    




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous"> 
</head>
<body>
    <?php
    //Add the header
    include("sys_page/header.html");
    //Connect to database
    include("sys_page/db_connect.php");    

    //Get the user info from the session cookie
    $user_id = $_SESSION['user_id'];

    //Session error code array
    $session_error_codes = [0,0];

    //Get the sessions this user is tutoring today
    $session_today_tutor_sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutor_id=6969_students.id WHERE date(6969_tutor_session.session_start) = CURRENT_DATE() AND 6969_students.id=$user_id";  
    $session_today_tutor_data = get_session_data($session_today_tutor_sql,$conn);
    if($session_today_tutor_data == 1) {$session_error_codes[0] = 1;} //If error code = 1 set error code array to true

    //Get the sessions this user is tutoring today
    $session_today_tutee_sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutee_id=6969_students.id WHERE date(6969_tutor_session.session_start) = CURRENT_DATE() AND 6969_students.id=$user_id";  
    $session_today_tutee_data = get_session_data($session_today_tutee_sql,$conn);
    if($session_today_tutee_data == 1) {$session_error_codes[1] = 1;} //If error code = 1 set error code array to true

    //Combine the today sessions data into a single array
    //Only use the arrays with correct data
    $no_session_today = false;
    if($session_error_codes[0] == 1 && $session_error_codes[1] == 1) {$no_session_today = true;}
    if($session_error_codes[0] == 1 && $session_error_codes[1] == 0) {$session_combined_data = $session_today_tutee_data;}
    if($session_error_codes[0] == 0 && $session_error_codes[1] == 1) {$session_combined_data = $session_today_tutor_data;}
    if($session_error_codes[0] == 0 && $session_error_codes[1] == 0) {$session_combined_data = array_merge($session_today_tutor_data,$session_today_tutee_data);}
    
    //If there is any stored data sort it
    if($no_session_today == false)
    {
        usort($session_combined_data, function($a, $b) {
            return $a[1] <=> $b[1];
        });
    }
    ?>

    
    <div class="text-center fs-1"><p id = "index_date_time"></p></div>
    <?php
        //Check if there are any sessions today
        if($no_session_today == false)
        {
            echo '<div class="upcoming_day_sessions container text-center border border-3 border-dark rounded">';
            echo '<h3 class="py-3 m-0">Upcoming</h3>';
            //Show the sessions today this user is tutoring
            for($i=0; $i<sizeof($session_combined_data); $i++)
            {
            ?>
            <div class="session_card d-flex row border-top">
                <p class="py-2 m-0">
                    <?php
                    if($session_combined_data != 1)
                    {
                        //SESSION START TIME
                        //Convert timestamp to 12h time
                        $time_raw_start = $session_combined_data[$i][1];
                        $time_24h_hours_start = (int)substr($time_raw_start,10,3);
                        //If hours > 12 remove the extra time and add pm to end of number
                        $time_ending_start = "am";
                        if($time_24h_hours_start > 12)
                        {
                            $time_24h_hours_start += -12;
                            $time_ending_start = "pm";
                        }
                        //Combine everything back into one string
                        $time_12h_start = (string)$time_24h_hours_start . ":" .  substr($time_raw_start,14,2) . $time_ending_start;

                        //SESSION END TIME
                        //Convert timestamp to 12h time
                        $time_raw_end = $session_combined_data[$i][2];
                        $time_24h_hours_end = (int)substr($time_raw_end,10,3);
                        //If hours > 12 remove the extra time and add pm to end of number
                        $time_ending_end = "am";
                        if($time_24h_hours_end > 12)
                        {
                            $time_24h_hours_end += -12;
                            $time_ending_end = "pm";
                        }
                        //Combine everything back into one string
                        $time_12h_end = (string)$time_24h_hours_end . ":" .  substr($time_raw_end,14,2) . $time_ending_end;




                        //Check if the user is tutoring or being tutored
                        $is_tutor = false;
                        if($session_combined_data[$i][5] == $user_id) $is_tutor = true;
                        //Show the session time, name of person, subject, mark of tutor/tutee
                        if($is_tutor) 
                        {
                            echo $time_12h_start . " - " . $time_12h_end . " " . $session_combined_data[$i][4];
                            echo "<br>";
                            echo $session_combined_data[$i][8] . " -Tutor";
                        }
                        else 
                        {
                            echo $time_12h_start . " - " . $time_12h_end . " " . $session_combined_data[$i][6];
                            echo "<br>";
                            echo $session_combined_data[$i][8] . " -Tutee";
                        }
                    }
                    ?>
                </p>
            </div>
            <?php } 
        }
        else //IF THERE ARE NO SESSIONS TODAY
        {
            echo '<div class="no_sessions_today text-center">';
            echo '<p>no sessions today</p>';
        }
        ?> 
        
    </div>

    <div class="upcoming_week_sessions container text-center border border-3 border-dark rounded mt-4">
        <h3>Weekly</h3>
        
        <?php
        $data_increment = 1;
        for($i=0; $i<7; $i++)
        {
            //Get the day to query
            $day_sql = "SELECT DAYNAME(DATE_ADD(CURRENT_DATE(), INTERVAL $data_increment DAY));";
            $day_result = $conn->query($day_sql);
            $day_data = $day_result->fetch_assoc();
            
            //Get the sessions over the next few days
            $tutor_sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutor_id=6969_students.id WHERE date(6969_tutor_session.session_start) = DATE_ADD(CURRENT_DATE(), INTERVAL $data_increment DAY) AND 6969_students.id=$user_id;";
            $tutor_data = get_session_data($tutor_sql,$conn);
            $tutee_sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutee_id=6969_students.id WHERE date(6969_tutor_session.session_start) = DATE_ADD(CURRENT_DATE(), INTERVAL $data_increment DAY) AND 6969_students.id=$user_id;";
            $tutee_data = get_session_data($tutee_sql,$conn);


            echo "<p>";
            echo $day_data['DAYNAME(DATE_ADD(CURRENT_DATE(), INTERVAL ' . $data_increment . ' DAY))'] . ' - ';
            
            if($tutor_data != 1 && $tutee_data != 1) {echo sizeof($tutor_data) + sizeof($tutee_data);}
            else
            {
                if($tutor_data != 1) {echo sizeof($tutor_data);}
                else if($tutee_data != 1) {echo sizeof($tutee_data);}
                else {echo "no";}
            }
            echo " events</p>";
            $data_increment += 1;
        }
        ?>
    </div>


    <script src="content.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script> 
</body>
</html>