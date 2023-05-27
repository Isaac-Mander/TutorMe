<?php
//Import functions\
include("sys_page/header.html");
include("sys_page/db_connect.php");
include("sys_page/functions.php");

?>
  
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <?php
    $tz = new DateTimeZone('NZ');
    $dt = new DateTime('now',$tz);
    $time_day = $dt->format('d'); // output: '1' - '31'
    $time_month = $dt->format('m'); // output: '1' - '12'cc
    $time_year = $dt->format('Y'); // output: '2023'
    $time_hour = $dt ->format('h');// output: '09'
    $time_minute = $dt ->format('i');// out: ':46'
    $time =  $time_year . "-" . $time_month . "-" . $time_day ." " . $time_hour . ":" . $time_minute;
    $date =  $time_year . "-" . $time_month . "-" . $time_day;

    $available_session_times_sql = "SELECT * FROM 6969_students INNER JOIN 6969_student_times ON 6969_student_times.student_id=6969_students.id WHERE 6969_students.id=3";
    $available_session_times_data = get_available_session_data($available_session_times_sql, $conn);
    //pulls all the potential times from the database, runs through function
    function grab_events($conn){
    //function so that when the database is updated the calender's events can be as well

    //Get the sessions this user is tutoring today
    $session_today_tutor_sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutor_id=6969_students.id WHERE 6969_students.id=3";  
    $session_today_tutor_data = get_session_data($session_today_tutor_sql,$conn);


    //Get the sessions this user is being tutored today
    $session_today_tutee_sql = "SELECT * FROM 6969_students INNER JOIN 6969_tutor_session ON 6969_tutor_session.tutee_id=6969_students.id WHERE 6969_students.id=3";  
    $session_today_tutee_data = get_session_data($session_today_tutee_sql,$conn);

    $available_session_times_sql = "SELECT * FROM 6969_students INNER JOIN 6969_student_times ON 6969_student_times.student_id=6969_students.id WHERE 6969_students.id=3";
    $available_session_times_data = get_available_session_data($available_session_times_sql, $conn);
    //pulls all the potential times from the database, runs through function

    if (is_array($available_session_times_data)) {
      for($i=0; $i<sizeof($available_session_times_data); $i++){
        //looping through all of the lines of the array
        $name = $available_session_times_data[$i][0]; //sets the name
        $potential_start_time = $available_session_times_data[$i][1]; //sets the start time
        $potential_end_time = $available_session_times_data[$i][2]; //sets the end time
        $week_day =  $available_session_times_data[$i][3]; //sets the day of the week 1-7

        $tz = new DateTimeZone('NZ');
        $dt = new DateTime('now',$tz);
        $time_day = $dt->format('d'); // output: '1' - '31'
        $time_month = $dt->format('m'); // output: '1' - '12'cc
        $time_year = $dt->format('Y'); // output: '2023'
        $date =  $time_year . "-" . $time_month . "-" . $time_day;

        $days_of_week_array = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
        $day_of_week = $days_of_week_array[$week_day-1]; //converts integer value to word, as $weekday is 1-7 and array is 0-6, 1 is minused

        $day_current_week = date('N');
        if ($day_current_week == 7){
          //checking if the day is Sunday, because the week on the calendar starts on Sunday, this if it is the events need to be for the next week
          $value_day_actual = Strtotime("Next week ".$day_of_week);
          $date_actual = date("Y-m-d",$value_day_actual);
          $potential_start_datetime = $date_actual."T".$potential_start_time;
          $potential_end_datetime = $date_actual."T".$potential_end_time;
          //converting values for the upcomming week
        } else {
          //if it is not Sunday
          $value_day_actual = Strtotime("This week ".$day_of_week);
          $date_actual = date("Y-m-d",$value_day_actual);
          $potential_start_datetime = $date_actual."T".$potential_start_time;
          $potential_end_datetime = $date_actual."T".$potential_end_time;
          //converting values for the current values
        }
        $potential_events[]=[
          "title" => "potential session",
          "start" => $potential_start_datetime,
          "end"   => $potential_end_datetime,
          "color" => "yellow"
        ];
        //putting all of the potential sessions into an array
        }
      }
    
    if (is_array($session_today_tutor_data) && is_array($session_today_tutee_data)) {
      $session_combined_data = array_merge($session_today_tutor_data, $session_today_tutee_data);
    } else {
      // Handle the case where one or both variables is not an array
      // For example:
      $session_combined_data = array();};
    if (is_array($session_combined_data)) {
      for($i=0; $i<sizeof($session_combined_data); $i++){
        //looping through all of the lines of the array
        $day = substr($session_combined_data[$i][1],0,10); //setting the day value
        $starttime = substr($session_combined_data[$i][1],11,8); //setting the start time
        $endtime = substr($session_combined_data[$i][2],11,8); //setting the end time
        $tutee =  $session_combined_data[$i][4]; //setting tutee name
        $tutor = $session_combined_data[$i][6]; //setting tutor name
        $subject = $session_combined_data[$i][8]; //setting subject name
        $events[] = [
          "title" => $tutor.' tutoring '.$tutee.' in '.$subject,
          //setting a title
          "start" => $day . "T" . $starttime,
          //setting the start time
          "end"   => $day."T".$endtime,
          //setting the end time
      ];
      //creating an array of all of the sessions that have been booked in
      }
      if (is_array($events) && is_array($potential_events)) {
        //making sure that both arrays are arrays
        $all_events = array_merge($events, $potential_events);
        return $all_events;
        //merging the arrays to be input into the calendar api
      }else {
        return '(」゜ロ゜)」';
      }
    } else {
      // Assign an array value to $session_today_tutor_data
      return '(」゜ロ゜)」';
    }
  };
    $events = grab_events($conn);  
    //calling the events function, and setting the events?>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src='fullcalendar-6.1.5\fullcalendar-6.1.5\dist\index.global.js'></script>
<script>
//loading in the calender
  document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    <?php $JsonEvents = json_encode($events); //putting the array into a format the calendar can read ?>
    var calendar = new FullCalendar.Calendar(calendarEl, {
      height: 'auto',
      headerToolbar: {
        left:'',
        center: 'title',
        right:''
      },
      //setting the tool bar
      // customize the button names,
      // otherwise they'd all just say "list"
      views: {
        timeGridWeek: { buttonText: 'grid week' }
      },
      //only having the initial view
      initialView: 'timeGridWeek',
      initialDate:  '<?php echo $date?>',
      navLinks: true, // can click day/week names to navigate views
      businessHours: true,
      dayMaxEvents: true, // allow "more" link when too many events
      events: <?php echo $JsonEvents ?> //uploading all of the events
    });
    calendar.render();
    //rendering calendar
  });
</script>
<style>

  body {
    margin: 40px 10px;
    padding: 0;
    font-family: Arial, Helvetica Neue, Helvetica, sans-serif;
    font-size: 14px;
  }

  #calendar {
    max-width: 1100px;
    margin: 0 auto;
  }

</style>
  </head>
  <body>
  <form action='calendar_2.php' method='post'>
    <div class="container">
        <label for="start_time"><b>Start time</b></label>
        <input type="time" id="start_time" placeholder="Start time" name="start_time" required><br>


        <label for="end_time"><b>End time</b></label>
        <input type="time" id="end_time" placeholder="End time" name="end_time" required><br>
        
        <label for="day_of_week">Day of the week (between 1 and 7) 1 = Monday, 7 = Sunday:</label>
        <input type="number" id="day_of_week" name="day_of_week" min="1" max="7">
        <input type="submit">
        <!-- form for uploading new potential sessions to the database-->
    </div>
    </form> 
    <?php    
    if (is_array($available_session_times_data)) {
      //if is array
      for($i=0; $i<sizeof($available_session_times_data); $i++){
        //running through all of the array
        $tz = new DateTimeZone('NZ');
        $dt = new DateTime('now',$tz);
        $time_day = $dt->format('d'); // output: '1' - '31'
        $time_month = $dt->format('m'); // output: '1' - '12'cc
        $time_year = $dt->format('Y'); // output: '2023'
        $day =  $time_year . "-" . $time_month . "-" . $time_day;
        $name = $available_session_times_data[$i][0]; //setting name
        $day_of_week = $available_session_times_data[$i][3]; //setting day of the week

        $potential_start_time_session = $available_session_times_data[$i][1]; //setting potential start time
        $potential_end_time_session = $available_session_times_data[$i][2]; //setting potential end time
        $potential_starttime_rough = strtotime($day.$potential_start_time_session); //getting a time value
        $potential_endtime_rough = strtotime($day.$potential_end_time_session); //getting a time value
        if (date('N') == $day_of_week){ //checking if the date is the same
          $potential_starttime = $potential_starttime_rough; //if so the times need no change
          $potential_endtime = $potential_endtime_rough;

        } elseif (date('N') > $day_of_week){ //if it is different 
          $time_diff = date('N') - $day_of_week; //find the time difference
          $potential_starttime = $potential_starttime_rough - ($time_diff * 86400);//accounts for the diffence
          $potential_endtime = $potential_endtime_rough - ($time_diff * 86400);

        } elseif(date('N') < $day_of_week){//if it is different
          $time_diff = $day_of_week - date('N'); //finds the time difference 
          $potential_starttime = $potential_starttime_rough + ($time_diff * 86400); //acounts for the difference
          $potential_endtime = $potential_endtime_rough + ($time_diff * 86400);
        }
        ?>    <div class='card' style="width: 24 rem;">
       <p class="card-text" <?php echo ($name."<br>".date("l jS \of F Y h:i:s A", $potential_starttime) . "<br>"); ?> </p>
       <p class="card-text" <?php  echo date("l jS \of F Y h:i:s A", $potential_endtime); ?> </p> <?php //prints out the cards of the time sessions.
        ?>     </div><?php
      }
    }?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <div id='calendar'></div>
  </body>
</html>
