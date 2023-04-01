<?php
//CHECK IF USER IS LOGGED IN
session_start();
if(!isset($_SESSION['user']) && !isset($_SESSION['school_code']) && !isset($_SESSION['user_id'])) //If not logged in redirect to login page
{
    header("Location: login_form.php"); //Send to the shadow realm (login screen)
}
else //If user is logged in show rest of page content
{
    function get_session_data($sql,$conn)
    {
        //Query the database to get all the sessions THE USER IS TUTORING TODAY =============================================================================================================================
        $result = $conn->query($sql); //Query database
        if ($result->num_rows > 0) { //If the number of rows are not zero
            $no_today_sessions = false; //Tell other elements to expect session data
            $tutor_session_data = []; //Output data of each row into an array of session ids
            $session_index = 0;
            while($row = $result->fetch_assoc()) {
                //tutor_session_data is formatted like:
                //[index of session in array][(0)Session id,  (1)session time,   (2)session tutee id,   (3)session tutee name,  (4)session tutor id,   (5)session tutor name,   (6)subject_id,   (7)subject_name]
                $tutor_session_data[$session_index][0] = $row['id']; //Tutor Session Id
                $tutor_session_data[$session_index][1] = $row['time']; //Session time
                $tutor_session_data[$session_index][2] = $row['tutee_id']; //Session tutee id
                $tutee_id = $tutor_session_data[$session_index][2]; //Set variable to session tutee id for sql query
                $tutor_session_data[$session_index][4] = $row['tutor_id']; //Session tutor id
                $tutor_id = $tutor_session_data[$session_index][4]; //Set variable to session tutor id for sql query

                //Query the student list to get the tutee's name
                $sql_tutee_name = "SELECT name FROM 6969_students WHERE id=$tutee_id";
                $result_tutee = $conn->query($sql_tutee_name);
                $data = $result_tutee->fetch_assoc();
                $tutor_session_data[$session_index][3] = $data['name'];//Tutee name

                //Query the student list to get the tutor's name
                $sql_tutor_name = "SELECT name FROM 6969_students WHERE id=$tutor_id";
                $result_tutor = $conn->query($sql_tutor_name);
                $data = $result_tutor->fetch_assoc();
                $tutor_session_data[$session_index][5] = $data['name'];//Tutor name


                //Each tutor session can only have a single subject. This program will filter out any id that is zero
                if($row['global_subject_id'] == 0) $tutor_session_data[$session_index][5] = $row['local_subject_id']; //Session subject
                else $tutor_session_data[$session_index][6] = $row['global_subject_id']; //Session subject id

                //Query the subject list to get the subjects's name
                $subject_id = $tutor_session_data[$session_index][6];
                $sql_subject_name = "SELECT name FROM 6969_subjects WHERE id=$subject_id";
                $result_subject = $conn->query($sql_subject_name);
                $data2 = $result_subject->fetch_assoc();
                $tutor_session_data[$session_index][7] = $data2['name'];//Tutee name
                
                //Increment the session index the data is stored under
                $session_index += 1;
            }
        } else {
            echo "0 results";
            $no_today_sessions = true; //This variable tells the page to show the no sessions today msg
        }

        //Return the result of the query 
        if($no_today_sessions) return 1;
        else return $tutor_session_data;
    }
}?>
    




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEV INDEX PAGE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous"> 
</head>
<body>
    <?php
    //Add the header
    include("sys_page/header.html");
    //Connect to database
    include("sys_page/db_connect.php");

    //Query to check the sessions that the user has set up for today
    //Get current day
    $tz = new DateTimeZone('NZ');
    $dt = new DateTime('now',$tz);
    $time_day = $dt->format('D'); // output: 'Mon' - 'Sun'
    $time_month = $dt->format('M'); // output: 'Jan' - 'Dec'
    $time_day_month = $dt->format('d'); // output '1' - '31'
    $time_year = $dt->format('Y'); // output: '2023'

    //Combine the datetime info into the same format as the database
    $time_search_string = $time_day . " " . $time_month . " " . $time_day_month . " " . $time_year;
    

    //Get the user info from the session cookie
    $user_id = $_SESSION['user_id'];
    echo $time_search_string;


    //Get the sessions this user is tutoring today
    $session_today_tutor_sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutor_id=6969_students.id WHERE 6969_tutor_session.time LIKE '%$time_search_string%' AND 6969_students.id=$user_id";  
    $session_today_tutor_data = get_session_data($session_today_tutor_sql,$conn);

    //Get the sessions this user is tutoring today
    $session_today_tutee_sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutee_id=6969_students.id WHERE 6969_tutor_session.time LIKE '%$time_search_string%' AND 6969_students.id=$user_id";  
    $session_today_tutee_data = get_session_data($session_today_tutee_sql,$conn);
    
    //Combine the today sessions data into a single array
    $session_combined_data = array_merge($session_today_tutor_data,$session_today_tutee_data);
    usort($session_combined_data, function($a, $b) {
        return $a[1] <=> $b[1];
    });
    ?>

    
    <div class="index_date_time"><p id = "index_date_time2"></p></div>


    <div class="upcoming_day_sessions container text-center border border-3 border-dark rounded w-100">
        <h3 class="py-3 m-0">Upcoming</h3>
        <?php
        
        //Show the sessions today this user is tutoring
        for($i=0; $i<sizeof($session_combined_data); $i++)
        {
        ?>
        <div class="session_card d-flex row border-top">
            <p class="py-2 m-0">
                <?php
                if($session_combined_data != 1)
                {
                    //Convert 24h time to 12h time
                    $time_24h = substr($session_combined_data[$i][1],15,6);
                    $time_24h_hours = (int)substr($time_24h,0,3);
                    //If hours > 12 remove the extra time and add pm to end of number
                    $time_ending = "am";
                    if($time_24h_hours > 12)
                    {
                        $time_24h_hours += -12;
                        $time_ending = "pm";
                    }
                    //Combine everything back into one string
                    $time_12h = (string)$time_24h_hours . substr($time_24h,3,5) . $time_ending;


                    //Check if the user is tutoring or being tutored
                    $is_tutor = false;
                    if($session_combined_data[$i][4] == $user_id) $is_tutor = true;

                    //Show the session time, name of person, subject, mark of tutor/tutee
                    if($is_tutor) echo $time_12h . " " . $session_combined_data[$i][3] . " " . $session_combined_data[$i][7] . " -Tutor";
                    else echo $time_12h . " " . $session_combined_data[$i][5] . " " . $session_combined_data[$i][7] . " -Tutee";
                    
                }
                ?>
            </p>
        </div>
        <?php } ?> 
    </div>

    <div class="upcoming_week_sessions container text-center border border-3 border-dark rounded w-75">
        <h3>Weekly</h3>
        <!-- THE NEXT FEW LINES ARE JUST TEMP TEXT -->
        <p class="col">Tuesday - 3 events</p>
        <p class="col">Wednesday - 1 event</p>
        <p class="col">Thursday - 2 events</p>
        <p class="col">Friday - 2 events</p>
    </div>


    <script src="content.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script> 
</body>
</html>