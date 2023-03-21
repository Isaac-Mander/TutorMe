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
    $sql = "SELECT * FROM 6969_tutor_session WHERE time LIKE '%$time_search_string%'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
            echo $row['subject_id'];
    }
    } else {
    echo "0 results";
    }
    $conn->close();

    //Get the info about each session
    $sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutor_id=6969_students.id";
    //"SELECT * FROM 6969_tutor_session WHERE time LIKE '%$time_search_string%'"
    ?>

    <div class="index_datetime"><p id = "index_date_time"></p></div>


    <div class="upcoming_day_sessions">
        <h3>Upcoming</h3>
        <p><?php echo $row['subject_id']; ?></p>
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