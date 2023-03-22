<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEV INDEX PAGE</title>
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

    //Query the database to get all the sessions the user is tutoring today
    $sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutor_id=6969_students.id WHERE 6969_tutor_session.time LIKE '%$time_search_string%'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) { //if the number of rows are not zero
    // output data of each row into an array of session ids
    $tutor_session_data = [];
    $session_index = 0;
    while($row = $result->fetch_assoc()) {
        //tutor_session_data is formated like [index of session in array][Session id,session time,session tutee id,session tutee name (string),subject]
        $tutor_session_data[$session_index][0] = $row['id']; //Tutor Session Id
        $tutor_session_data[$session_index][1] = $row['time']; //Session time
        $tutor_session_data[$session_index][2] = $row['tutee_id']; //Session tutee id
        $tutee_id = $tutor_session_data[$session_index][2];

        //Query the student list to get the tutee's name
        $sql_tutee_name = "SELECT name FROM 6969_students WHERE id=$tutee_id";
        $result_tutee = $conn->query($sql_tutee_name);
        $data = $result_tutee->fetch_assoc();
        $tutor_session_data[$session_index][3] = $data['name'];//Tutee name


        //Each tutor session can only have a single subject. This program will filter out any id that is zero
        if($row['global_subject_id'] == 0) $tutor_session_data[$session_index][4] = $row['local_subject_id']; //Session subject
        else $tutor_session_data[$session_index][4] = $row['global_subject_id']; //Session subject
        
        //Increment the session index the data is stored under
        $session_index += 1;
    }
    } else {
    echo "0 results";
    }



    
    ?>

    <div class="index_datetime"><p id = "index_date_time"></p></div>


    <div class="upcoming_day_sessions">
        <h3>Upcoming</h3>
        <div class="session_card">
            <p><?php echo substr($tutor_session_data[0][1],15,6) ?></p>
            <p><?php echo $tutor_session_data[0][3] ?></p>
            <p><?php echo $tutor_session_data[0][4] ?></p>
        </div>
        <!-- THE NEXT FEW LINES ARE JUST TEMP TEXT -->
        <p>10:00am - Sarah (Chemistry)</p>
        <p>12:00pm - Paul (French)</p>
        <p>1:00pm - Amelia (Math)</p>
        <p>3 other events today...</p>
    </div>

    <div class="upcoming_week_sessions">
        <h3>Weekly</h3>
        <!-- THE NEXT FEW LINES ARE JUST TEMP TEXT -->
        <p>Tuesday - 3 events</p>
        <p>Wednesday - 1 event</p>
        <p>Thursday - 2 events</p>
        <p>Friday - 2 events</p>
    </div>


    <script src="content.js"></script> 
</body>
</html>